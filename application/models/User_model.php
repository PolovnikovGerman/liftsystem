<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class User_model extends MY_Model
{

    private $delete_status=3;
    private $user_active = 1;
    private $user_paused = 2;

    public function __construct() {
        parent::__construct();
    }

    public function current_user() {
        $this->load->helper('cookie');
        $out = ['result' =>$this->error_result,];
        $user=usersession('usr_data');
        $cookie=get_cookie('acctoken');
        if (empty($user)) {
            if ($cookie) {
                $res=$this->get_user_accesstoken($cookie);
                if ($res['result']==$this->success_result) {
                    $user=$res['user'];
                }
            }
        }
        if (is_array($user)) {
            /* Try to check Cooikie */
            $this->db->select('user_status');
            $this->db->from('users');
            $this->db->where('user_id', $user['id']);
            $chkres=$this->db->get()->row_array();
            if ($chkres['user_status']==1) {
                $out['result'] = $this->success_result;
                $out['data'] = $user;
                usersession('usr_data', $user);
            }
        }
        return $out;
    }

    public function get_user_accesstoken($access_token) {
        $out=['result'=>$this->error_result, 'msg'=>'User not exist'];
        $this->db->select('user_id');
        $this->db->from('ts_acces_tokens');
        $this->db->where('access_token',$access_token);
        $res=$this->db->get()->result_array();
        if (count($res)==1) {
            $user_id=$res[0]['user_id'];
            $chkres=$this->get_user_details($user_id);
            if ($chkres['result']==$this->success_result) {
                $user=$chkres['data'];
                $out['user']=[
                    'id' => $user['user_id'],
                    'user_logged_in'=> $user['role_short'],
                    'user_email' => $user['user_email'],
                    'user_name'=>$user['user_name'],
                    'user_replica'=>(!empty($user['user_leadname']) ? $user['user_leadname'] : $user['first_name']),
                    'user_logo' => (empty($user['user_logo']) ? $this->config->item('empty_profile') : $user['user_logo']),
                ];
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function signout() {
        $res=$this->current_user();
        if ($res['result']==$this->success_result) {
            $user = $res['data'];
            $this->userlog($user['id'],'Sign out', 1);
        }
        usersession('usr_data', NULL);
        $this->load->helper('cookie');
        $cookie=get_cookie('acctoken');
        if (!empty($cookie)) {
            $this->db->where('access_token',$cookie);
            $this->db->delete('ts_acces_tokens');
            delete_cookie($cookie);
        }
        return true;
    }

    function login($data) {
        $out=array('result'=>  $this->error_result, 'msg'=>'', $url='error_404');
        $ipaddr = $this->input->ip_address();
        $this->load->helper('cookie');
        if (!$this->locked_ip()) {
            $out['url']='';
            $out['msg']='Enter user name and password';
            if (isset($data['username']) && isset($data['passwd'])) {
                $out['msg'] = 'Enter correct User Name / Password';
                $login=$data['username'];
                $passwd=md5($data['passwd']);
                $this->db->select('u.*, r.role_short');
                $this->db->from('users u');
                $this->db->join('roles r','r.role_id=u.role_id','left');
                $this->db->where('u.user_email', $login);
                $this->db->where('u.user_passwd', $passwd);
                $res = $this->db->get()->row_array();
                if (isset($res['user_id'])) {
                    $out['result'] = $this->success_result;
                } else {
                    $this->db->select('u.*, r.role_short');
                    $this->db->from('users u');
                    $this->db->join('roles r','r.role_id=u.role_id','left');
                    $this->db->where('u.userlogin', $login);
                    $this->db->where('u.user_passwd', $passwd);
                    $res = $this->db->get()->row_array();
                    if (isset($res['user_id'])) {
                        $out['result'] = $this->success_result;
                    }
                }

                if ($out['result'] == $this->success_result) {
                    /* Check Additional restricts - BY IP & Time */
                    $user_iprestr = $this->get_user_restrictions($res['user_id']);
                    $ipfound = 0;
                    if (count($user_iprestr) == 0) {
                        $ipfound = 1;
                    } else {
                        foreach ($user_iprestr as $row) {
                            if ($row['ip_address'] == $ipaddr) {
                                $ipfound = 1;
                            }
                        }
                    }
                    if ($ipfound == 0) {
                        $out['msg'] = 'Security check failed';
                        $out['result'] = $this->error_result;
                    } else {
                        $chktime = 0;
                        if ($res['time_restrict'] == 0) {
                            /* Chk User time restrict */
                            $chktime = 1;
                        } else {
                            $times = get_timerestrict($res['user_timerestrict']);
                            $curtime = time();
                            if ($curtime >= $times['begin'] && $curtime <= $times['end']) {
                                $chktime = 1;
                            }
                        }
                        if ($chktime == 0) {
                            $out['msg'] = 'Security check failed';
                            $out['result'] = $this->error_result;
                        } else {
                            $out['result'] = $this->success_result;
                            $out['msg'] = '';
                            $out['usrdat'] = $res;
                            $usr_data = array(
                                'id' => $res['user_id'],
                                'user_logged_in'=> $res['role_short'],
                                'user_email' => $res['user_email'],
                                'user_name'=>$res['user_name'],
                                'user_replica'=>(!empty($res['user_leadname']) ? $res['user_leadname'] : $res['first_name']),
                                'user_logo' => (empty($res['user_logo']) ? $this->config->item('empty_profile') : $res['user_logo']),
                            );
                            usersession('usr_data', $usr_data);
                            // Create access token
                            $res['token']=uniq_link(30);
                            /* Save token to DB */
                            $this->db->set('token_created',date('Y-m-d H:i:s'));
                            $this->db->set('access_token',$res['token']);
                            $this->db->set('user_id',$res['user_id']);
                            $this->db->insert('ts_acces_tokens');
                            // Add Access token into cookies
                            $server=$this->input->server('SERVER_NAME');
                            $cookie = array(
                                'name'   => 'acctoken',
                                'value'  => $res['token'],
                                'expire' => '86500',
                                'domain' => '.'.$server,
                                'path'   => '/',
                                'secure' => FALSE,
                            );
                            set_cookie($cookie);
                        }
                    }
                }
            }
        }
        return $out;
    }

    public function locked_ip() {
        $usr_ip = $this->input->ip_address();
        $this->db->select('count(lockedip_id) as cnt');
        $this->db->from('sb_locked_ips');
        $this->db->where('ip_address', $usr_ip);
        $this->db->where('locked_status',1);
        $res = $this->db->get()->row_array();
        return ($res['cnt']==0 ? FALSE : TRUE);
    }

    /* GET LIST OF IP RESTRICTS */
    public function get_user_restrictions($user_id='',$ipaddr='') {
        $this->db->select('*');
        $this->db->from('user_restrictions');
        if ($user_id) {
            $this->db->where('user_id',$user_id);
        }
        if ($ipaddr) {
            $this->db->where('ip_address',$ipaddr);
        }
        $result=$this->db->get()->result_array();
        return $result;
    }

    /* Number of users */
    public function get_count_user($options=[]) {
        $this->db->select('count(user_id) as cnt');
        $this->db->from('users');
        if (isset($options['user_status'])) {
            if (is_array($options['user_status'])) {
                $this->db->where_in('user_status', $options['user_status']);
            } else {
                $this->db->where('user_status', $options['user_status']);
            }
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    // User list for contol
    public function get_userscontrollist($options=[]) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown error',];
        // Prepare last activity select
        $this->db->select('user_id, max(action_time) as lastactivity');
        $this->db->from('ts_user_activities');
        $this->db->where('activity',1);
        $this->db->group_by('user_id');
        $lastactiv_qty = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('u.*, r.role_name, act.lastactivity');
        $this->db->from('users u');
        $this->db->join('roles r','r.role_id=u.role_id', 'left');
        $this->db->join("({$lastactiv_qty}) act",'act.user_id=u.user_id', 'left');
        $this->db->where('u.user_status != ', $this->delete_status);
        if (isset($options['user_status'])) {
            $this->db->where('u.user_status', $options['user_status']);
        }
        if (isset($options['user_type'])) {
            if ($options['user_type']=='nonemployee') {
                $this->db->where('coalesce(u.user_type,\'empty\') != ', 'employee');
            } else {
                $this->db->where('u.user_type', $options['user_type']);
            }
        }
        if (isset($options['sort'])) {
            if (isset($options['sort_direct'])) {
                $this->db->order_by($options['sort'], $options['sort_direct']);
            } else {
                $this->db->order_by($options['sort']);
            }
        }
        $res=$this->db->get()->result_array();

        $data = [];
        $numpp=1;
        foreach ($res as $row) {
            $row['numpp']=$numpp;
            $row['last_activity']= 'n/a';
            if (!empty($row['lastactivity'])) {
                $row['last_activity']=substr(date('D', $row['lastactivity']),0,1).' - '.date('M j, Y H:i', $row['lastactivity']);
            }
            // $row['last_activity']=(empty($row['lastactivity']) ? 'n/a' : substr(date('D', $row['lastactivity']),0,1).' - '.date('M j, Y H:i', $row['lastactivity']));
            if ($row['user_status']==$this->user_active) {
                $row['status_txt'] = 'Active';
            } else {
                $row['status_txt'] = 'Non-Active';
            }


            $data[]=$row;
            $numpp++;
        }
        $out['result']=$this->success_result;
        $out['data']=$data;
        return $out;
    }

    public function get_user_details($user_id) {
        $out=['result'=>$this->error_result,'msg'=>'Data not found'];
        $this->db->select('u.*, r.role_name, r.role_short');
        $this->db->from('users u');
        $this->db->join('roles r','r.role_id=u.role_id', 'left');
        $this->db->where('u.user_id', $user_id);
        $res=$this->db->get()->result_array();
        if (count($res)==1) {
            $out['result']=$this->success_result;
            $res[0]['user_passwd_txt2'] = '';
            $out['data']=$res[0];
        }
        return $out;
    }

    public function new_user() {
        $data = [
            'user_id' => 0,
            'user_email' =>'',
            'user_name'=>'',
            'user_status'=>'0',
            'user_id'=>0,
            'user_leadrep'=>0,
            'user_leadname'=>'',
            'user_initials'=>'',
            'time_restrict'=>0,
            'user_page'=>'',
            'redmine_executor'=>'NO',
            'personal_email'=>'',
            'email_signature'=>'',
            'finuser'=>0,
            'user_passwd_txt' => '',
            'user_passwd_txt2' => '',
            'profit_view'=>'Points',
        ];
        return $data;
    }

    public function get_user_data($user_id) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_id',$user_id);
        $res=$this->db->get()->row_array();
        return $res;
    }


    public function update_user_status($data) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown user code'];


        if (isset($data['code1']) && !empty($data['code1']) && isset($data['code2']) && !empty($data['code2']) && isset($data['code3']) && !empty($data['code3']) && isset($data['code4']) && !empty($data['code4'])) {
            $checkcode = $data['code1'].$data['code2'].$data['code3'].$data['code4'];
            // Check PIN Code

            $out['msg'] = 'Empty user / new status ';
            if (isset($data['user']) && !empty($data['user']) && isset($data['status']) && !empty($data['status'])) {
                $user_id=$data['user']; $newstatus = $data['status'];
                $out['msg'] = 'Unknown new status ';
                if ($newstatus==$this->user_active || $newstatus==$this->user_paused) {
                    $out['msg']='Unknown user';
                    $usrres=$this->get_user_details($user_id);
                    if ($usrres['result']==$this->success_result) {
                        $this->db->where('user_id', $user_id);
                        $this->db->set('user_status', $newstatus);
                        $this->db->update('users');
                        $out['result']=$this->success_result;
                        $msg=$usrres['data']['user_name'].' Status updated on '.($newstatus==1 ? 'ACTIVE' : 'PAUSED');
                        $this->userlog($data['executor'],$msg);
                    }
                }
            }
        }
        return $out;
    }

    public function update_signinuser($user_id) {
        $this->db->select('u.*, r.role_short');
        $this->db->from('users u');
        $this->db->join('roles r','r.role_id=u.role_id','left');
        $this->db->where('u.user_id', $user_id);
        $res = $this->db->get()->row_array();

        $usr_data = array(
            'id' => $res['user_id'],
            'user_logged_in'=> $res['role_short'],
            'user_email' => $res['user_email'],
            'user_name'=>$res['user_name'],
            'user_replica'=>(!empty($res['user_leadname']) ? $res['user_leadname'] : $res['first_name']),
            'user_logo' => (empty($res['user_logo']) ? $this->config->item('empty_profile') : $res['user_logo']),
        );
        usersession('usr_data', $usr_data);
        return TRUE;

    }

    public function get_user_leadreplicas($active=1) {
        $this->db->select('*');
        $this->db->from('users');
        $this->db->where('user_leadrep',1);
        if ($active==1) {
            // $this->db->where('user_status <',3);
            $this->db->where('user_status ',1);
        }
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function delete_usr($user_id, $executor_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'User not found'];
        $chkres = $this->get_user_details($user_id);
        if ($chkres['result']==$this->success_result) {
            $this->db->set('user_status', $this->delete_status);
            $this->db->where('user_id', $user_id);
            $this->db->update('users');
            $out['result'] = $this->success_result;
            $this->userlog($executor_id,'Delete User '.$user_id, 1);
        }
        return $out;
    }

    public function update_userstatus($user_id, $curstatus, $executor_id) {
        $out = ['result' => $this->error_result, 'msg' => 'User not found'];
        $chkres = $this->get_user_details($user_id);
        if ($chkres['result']==$this->success_result) {
            if ($curstatus==$this->user_active) {
                $this->db->set('user_status', $this->user_paused);
            } else {
                $this->db->set('user_status', $this->user_active);
            }
            $this->db->where('user_id', $user_id);
            $this->db->update('users');
            $out['result'] = $this->success_result;
            if ($curstatus==$this->user_active) {
                $this->userlog($executor_id,'Pause User '.$user_id, 1);
                $out['user_status'] = $this->user_paused;
                $out['status_txt'] = 'Non-Active';
            } else {
                $this->userlog($executor_id,'Activate User '.$user_id, 1);
                $out['user_status'] = $this->user_active;
                $out['status_txt'] = 'Active';
            }
        }
        return $out;
    }

    function get_user_iprestrict($user_id) {
        $this->db->select('*');
        $this->db->from('user_restrictions');
        $this->db->where('user_id',$user_id);
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function update_userdata($session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Error during update user'];
        $user = $session_data['user'];
        $userip = $session_data['userip'];
        $webpages = $session_data['webpages'];
        $deleted = $session_data['deleted'];
        // checks incoming data
        $chkusrdat = $this->_checkuserdata($user);
        $out['msg'] = $chkusrdat['msg'];
        if ($chkusrdat['result']==$this->success_result) {
            // Update
            $this->db->set('user_email', $user['user_email']);
            $this->db->set('user_name', $user['user_name']);
            $this->db->set('user_status', $user['user_status']);
            $this->db->set('user_leadrep', $user['user_leadrep']);
            $this->db->set('finuser', $user['finuser']);
            $this->db->set('user_leadname', $user['user_leadname']);
            $this->db->set('user_initials', $user['user_initials']);
            $this->db->set('time_restrict', $user['time_restrict']);
            $this->db->set('personal_email', $user['personal_email']);
            $this->db->set('email_signature', $user['email_signature']);
            $this->db->set('profit_view', $user['profit_view']);
            $this->db->set('user_page', ifset($user,'user_page',NULL));
            if ($user['user_id']==0) {
                $this->db->insert('users');
                $user_id = $this->db->insert_id();
            } else {
                $this->db->where('user_id', $user['user_id']);
                $this->db->update('users');
                $user_id = $user['user_id'];
            }
            // Insert finished successfully
            $out['msg'] = 'Error during update user';
            if ($user_id>0) {
                if (!empty($user['user_passwd_txt'])) {
                    $this->db->set('user_passwd', md5($user['user_passwd_txt']));
                    $this->db->set('user_passwd_txt', $user['user_passwd_txt']);
                    $this->db->where('user_id', $user_id);
                    $this->db->update('users');
                }
                // Update restrict
                $this->update_iprestrict($userip, $user_id);
                // Update page permissions
                $this->load->model('menuitems_model');
                $this->menuitems_model->save_userpermissions($webpages, $user_id);
                foreach ($deleted as $row) {
                    $this->db->where('user_restriction_id', $row);
                    $this->db->delete('user_restrictions');
                }
                $out['result']=$this->success_result;
                usersession($session_id, NULL);
            }
        }
        return $out;
    }

    public function update_iprestrict($userip, $user_id) {
        foreach ($userip as $row) {
            if ($row['user_restriction_id']<0) {
                $this->db->set('user_id', $user_id);
                $this->db->set('ip_address', $row['ip_address']);
                $this->db->insert('user_restrictions');
            }
        }
        return true;
    }

    public function userip_restrict_add($session_data, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Error During Add Restrict'];
        $ip_restrict = $session_data['userip'];
        $newkey = count($ip_restrict)+1;
        $ip_restrict[] = [
            'user_restriction_id' => $newkey*(-1),
            'ip_address' => '',
        ];
        $session_data['userip']=$ip_restrict;
        usersession($session_id, $session_data);
        $out['result']=$this->success_result;
        $out['userip']=$ip_restrict;
        return $out;
    }

    public function userip_restrict_edit($user_restriction_id, $newval, $session_data, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Error During Add Restrict', 'oldval'=>''];
        $found = 0;
        $idx=0;
        $ip_restrict = $session_data['userip'];
        foreach ($ip_restrict as $row) {
            if ($row['user_restriction_id']==$user_restriction_id) {
                $ip_restrict[$idx]['ip_address'] = $newval;
                $found = 1;
                break;
            }
            $idx++;
        }
        if ($found==1) {
            $session_data['userip']=$ip_restrict;
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function userip_restrict_delete($user_restriction_id, $session_data, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Error During Add Restrict', 'oldval'=>''];
        $found = 0;
        $ip_restrict = $session_data['userip'];
        $deleted = $session_data['deleted'];
        $newip = [];
        foreach ($ip_restrict as $row) {
            if ($row['user_restriction_id']==$user_restriction_id) {
                $found = 1;
                if ($user_restriction_id>0) {
                    $deleted[]=$user_restriction_id;
                }
            } else {
                $newip[] = $row;
            }
        }
        if ($found==1) {
            $session_data['userip']=$newip;
            $session_data['deleted']=$deleted;
            usersession($session_id, $session_data);
            $out['userip'] = $newip;
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function userdata_edit($item, $newval, $session_data, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Unknown Item',];
        $user = $session_data['user'];
        if (array_key_exists($item, $user)) {
            $user[$item] = $newval;
            $session_data['user']=$user;
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    private function _checkuserdata($userdat) {
        $out=['result'=>$this->error_result, 'msg' => 'User Login non unique'];
        // select count # of user with login
        $this->db->select('count(user_id) as cnt');
        $this->db->from('users');
        $this->db->where('user_email',$userdat['user_email']);
        $this->db->where('user_id !=',$userdat['user_id']);
        $res=$this->db->get()->row_array();
        $numrec=$res['cnt'];
        if ($numrec==0) {
            $out['msg'] = 'For new user password required parameter';
            if ($userdat['user_id']<=0 && empty($userdat['user_passwd_txt'])) {
                return $out;
            }
            $out['msg'] = 'Enter Leads repl name';
            if ($userdat['user_leadrep']==1 && empty($userdat['user_leadname'])) {
                return $out;
            }
            /* Check password */
            $out['msg']='Please re-type password';
            if (!empty($userdat['user_passwd_txt']) && ($userdat['user_passwd_txt']!=$userdat['user_passwd_txt2'])) {
                return $out;
            }
            $out['msg']='User email (login) is required parameter';
            if (empty($userdat['user_email'])) {
                return $out;
            }
            $out['result'] = $this->success_result;
            // Check default page
//            if ($userdat['user_page']!='') {
//                $found=0;
//                foreach ($permissions as $row) {
//                    if ($userdat['user_page']==$row) {
//                        $found=1;
//                    }
//                }
//                if ($found==0) {
//                    $outres['id']=  User_model::ERR_FLAG;
//                    $outres['msg']='User do not have permission to the Default Page';
//                }
//            }

        }
        return $out;
    }

    public function get_users($options=[]) {
        $this->db->select('*');
        $this->db->from('users');
        if (isset($options['user_status'])) {
            $this->db->where('user_status', $options['user_status']);
        }
        if (isset($options['order_by'])) {
            if (isset($options['direction'])) {
                $this->db->order_by($options['order_by'], $options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function default_page($user_page) {
        $this->db->select('menu_item_id, parent_id, item_link');
        $this->db->from('menu_items');
        $this->db->where('menu_item_id', $user_page);
        $res = $this->db->get()->row_array();
        if (ifset($res, 'menu_item_id', 0)==0) {
            return 'welcome';
        }
        if (empty($res['parent_id'])) {
            return $res['item_link'];
        }
        $this->db->select('menu_item_id, item_link');
        $this->db->from('menu_items');
        $this->db->where('menu_item_id', $res['parent_id']);
        $main = $this->db->get()->row_array();
        return $main['item_link'].'/?start='.str_replace('#', '', $res['item_link']);
    }

}
/* End of file user_model.php */
/* Location: ./application/models/user_model.php */