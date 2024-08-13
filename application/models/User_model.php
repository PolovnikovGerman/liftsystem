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
        // $user = [];
        $cookie=get_cookie('acctoken');
        if (empty($user)) {
            if ($cookie) {
                $res=$this->get_user_accesstoken($cookie);
                if ($res['result']==$this->success_result) {
                    $user=$res['user'];
                }
            }
        }
        if (ifset($user,'id',0)>0) {
            /* Try to check Cooikie */
            $this->db->select('user_status, user_secret, last_verified');
            $this->db->from('users');
            $this->db->where('user_id', $user['id']);
            $chkres=$this->db->get()->row_array();
            if (!empty($chkres['user_secret']) && empty($chkres['last_verified'])) {
                usersession('usr_data', null);
            } else {
                if ($chkres['user_status']==1) {
                    $out['result'] = $this->success_result;
                    $out['data'] = $user;
                    usersession('usr_data', $user);
                    // If empty cookie
                    if ($cookie) {
                        $server=$this->input->server('SERVER_NAME');
                        $cookienew = array(
                            'name'   => 'acctoken',
                            'value'  => $cookie,
                            'expire' => '86500',
                            'domain' => $server,
                            'path'   => '/; SameSite=Strict',
                            'secure' => TRUE,
                            'httponly' => TRUE,
                        );
                        set_cookie($cookienew);
                    }
                }
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
                    'user_order_export' => $user['user_order_export'],
                    'user_secret' => $user['user_secret'],
                    'user_payuser' => $user['user_payuser'],
                    'finuser' => $user['finuser'],
                    'first_name' => $user['first_name'],
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
                            $times = get_timerestrict($res['time_restrict']);
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
                                'user_order_export' => $res['user_order_export'],
                                'finuser' => $res['finuser'],
                                'user_secret' => $res['user_secret'],
                                'user_payuser' => $res['user_payuser'],
                                'first_name' => $res['first_name'],
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
                                'domain' => $server,
                                'path'   => '/; SameSite=Strict',
                                'prefix' => '',
                                'secure' => FALSE,
                                'httponly' => FALSE,
                            );
                            set_cookie($cookie);
                            usersession('currentbrand',null);
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
        if (isset($options['sort2'])) {
            if (isset($options['sort_direct2'])) {
                $this->db->order_by($options['sort2'], $options['sort_direct2']);
            } else {
                $this->db->order_by($options['sort2']);
            }
        }
        $res=$this->db->get()->result_array();

        $data = [];
        $numpp=1;
        foreach ($res as $row) {
            $row['numpp']=$numpp;
            $row['last_activity']= 'n/a';
            if (!empty($row['lastactivity'])) {
                $row['last_activity']=date('D', $row['lastactivity']).' - '.date('M j, Y H:i', $row['lastactivity']);
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
            $res[0]['user_passwd_txt1'] = '';
            $out['data']=$res[0];
        }
        return $out;
    }

    public function new_user() {
        $data = [
            'user_id' => 0,
            'user_email' =>'',
            'user_name'=>'',
            'first_name' => '',
            'last_name' => '',
            'user_status'=>'1',
            'user_id'=>0,
            'user_leadrep'=>0,
            'user_leadname'=>'',
            'user_initials'=>'',
            'time_restrict'=>0,
            'user_page'=>'',
            'redmine_executor'=>'NO',
            'personal_email'=>'',
            'email_signature'=>'',
            'contactnote_bluetrack' => '',
            'contactnote_relievers' => '',
            'finuser'=>0,
            'user_passwd_txt1' => '',
            'user_passwd_txt2' => '',
            'profit_view'=>'Points',
            'user_payuser' => 0,
            'default_brand' => 'SB',
            'user_order_export' => 0,
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
            'user_order_export' => $res['user_order_export'],
            'finuser' => $res['finuser'],
            'user_secret' => $res['user_secret'],
            'user_payuser' => $res['user_payuser'],
            'first_name' => $res['first_name'],
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
            // Delete access tokens
            $this->db->where('user_id', $user_id);
            $this->db->delete('ts_acces_tokens');
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
            // Delete access tokens
            $this->db->where('user_id', $user_id);
            $this->db->delete('ts_acces_tokens');
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

    public function update_userdata($session_data, $session_id, $updusr) {
        $out = ['result' => $this->error_result, 'msg' => 'Error during update user'];
        $user = $session_data['user'];
        $userip = $session_data['userip'];
        $webpages = $session_data['webpages'];
        $deleted = $session_data['deleted'];
        // checks incoming data
        $chkusrdat = $this->_checkuserdata($user);
        $out['msg'] = $chkusrdat['msg'];
        if ($chkusrdat['result']==$this->success_result) {
            $usrname = $user['first_name'];
            if (!empty($user['last_name'])) {
                $usrname.=' '.$user['last_name'];
            }
            // Update
            $this->db->set('user_email', $user['user_email']);
            $this->db->set('user_name', $usrname);
            $this->db->set('first_name', $user['first_name']);
            $this->db->set('last_name', $user['last_name']);
            $this->db->set('user_status', $user['user_status']);
            $this->db->set('user_leadrep', $user['user_leadrep']);
            $this->db->set('finuser', $user['finuser']);
            $this->db->set('user_leadname', $user['user_leadname']);
            $this->db->set('user_initials', $user['user_initials']);
            $this->db->set('time_restrict', $user['time_restrict']);
            $this->db->set('personal_email', $user['personal_email']);
            $this->db->set('email_signature', $user['email_signature']);
            $this->db->set('contactnote_bluetrack', $user['contactnote_bluetrack']);
            $this->db->set('contactnote_relievers', $user['contactnote_relievers']);
            $this->db->set('profit_view', $user['profit_view']);
            $this->db->set('user_page', ifset($user,'user_page',NULL));
            $this->db->set('user_order_export', ifset($user,'user_order_export',0));
            $this->db->set('user_payuser', $user['user_payuser']);
            $this->db->set('default_brand', $user['default_brand']);
            if ($user['user_id']==0) {
                $this->db->set('created_at', date('Y-m-d H:i:s'));
                $this->db->set('created_by', $updusr);
                $this->db->set('updated_by', $updusr);
                $this->db->insert('users');
                $user_id = $this->db->insert_id();
                $user['user_name'] = $usrname;
                // Generate secret
                $this->_secret_code($user, $user_id);
            } else {
                $this->db->set('updated_by', $updusr);
                $this->db->where('user_id', $user['user_id']);
                $this->db->update('users');
                $user_id = $user['user_id'];
            }
            // Insert finished successfully
            $out['msg'] = 'Error during update user';
            if ($user_id>0) {
                if (!empty($user['user_passwd_txt1'])) {
                    $this->db->set('user_passwd', md5($user['user_passwd_txt1']));
                    $this->db->set('user_passwd_txt', $user['user_passwd_txt1']);
                    $this->db->set('last_verified', 0);
                    $this->db->where('user_id', $user_id);
                    $this->db->update('users');
                    // Delete access tokens
                    $this->db->where('user_id', $user_id);
                    $this->db->delete('ts_acces_tokens');
                }
                // Update restrict
                $this->update_iprestrict($userip, $user_id);
                // Update page permissions
                $this->load->model('menuitems_model');
                $this->menuitems_model->save_userpermissions($webpages, $user_id);
                // Update default pages
                $this->db->select('*')->from('user_default_page')->where(['user_id'=> $user_id, 'brand'=> 'SB']);
                $chksb = $this->db->get()->row_array();
                if (ifset($chksb, 'user_page_id', 0)==0) {
                    if (!empty($session_data['defsbpage'])) {
                        $this->db->set('user_id', $user_id);
                        $this->db->set('brand', 'SB');
                        $this->db->set('page_id', $session_data['defsbpage']);
                        $this->db->insert('user_default_page');
                    }
                } else {
                    if (!empty($session_data['defsbpage'])) {
                        $this->db->where('user_page_id', $chksb['user_page_id']);
                        $this->db->set('page_id', $session_data['defsbpage']);
                        $this->db->update('user_default_page');
                    } else {
                        $this->db->where('user_page_id', $chksb['user_page_id']);
                        $this->db->delete('user_default_page');
                    }
                }
                $this->db->select('*')->from('user_default_page')->where(['user_id'=> $user_id, 'brand'=> 'SR']);
                $chksr = $this->db->get()->row_array();
                if (ifset($chksr, 'user_page_id', 0)==0) {
                    if (!empty($session_data['defsrpage'])) {
                        $this->db->set('user_id', $user_id);
                        $this->db->set('brand', 'SR');
                        $this->db->set('page_id', $session_data['defsrpage']);
                        $this->db->insert('user_default_page');
                    }
                } else {
                    if (!empty($session_data['defsrpage'])) {
                        $this->db->where('user_page_id', $chksr['user_page_id']);
                        $this->db->set('page_id', $session_data['defsrpage']);
                        $this->db->update('user_default_page');
                    } else {
                        $this->db->where('user_page_id', $chksr['user_page_id']);
                        $this->db->delete('user_default_page');
                    }
                }
                $this->db->select('*')->from('user_default_page')->where(['user_id'=> $user_id, 'brand'=> 'SG']);
                $chksg = $this->db->get()->row_array();
                if (ifset($chksg, 'user_page_id', 0)==0) {
                    if (!empty($session_data['defsgpage'])) {
                        $this->db->set('user_id', $user_id);
                        $this->db->set('brand', 'SG');
                        $this->db->set('page_id', $session_data['defsgpage']);
                        $this->db->insert('user_default_page');
                    }
                } else {
                    if (!empty($session_data['defsgpage'])) {
                        $this->db->where('user_page_id', $chksg['user_page_id']);
                        $this->db->set('page_id', $session_data['defsgpage']);
                        $this->db->update('user_default_page');
                    } else {
                        $this->db->where('user_page_id', $chksg['user_page_id']);
                        $this->db->delete('user_default_page');
                    }
                }
                // Delete not used parameters
                foreach ($deleted as $row) {
                    $this->db->where('user_restriction_id', $row);
                    $this->db->delete('user_restrictions');
                }
                $out['result']=$this->success_result;
                $out['user_id'] = $user_id;
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
            if ($userdat['user_id']<=0 && empty($userdat['user_passwd_txt1'])) {
                return $out;
            }
            $out['msg'] = 'Enter Leads repl name';
            if ($userdat['user_leadrep']==1 && empty($userdat['user_leadname'])) {
                return $out;
            }
            /* Check password */
            $out['msg']='Please re-type password';
            if (!empty($userdat['user_passwd_txt1']) && ($userdat['user_passwd_txt1']!=$userdat['user_passwd_txt2'])) {
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

    public function default_page($user_id) {
        $this->db->select('default_brand as brand')->from('users')->where('user_id', $user_id);
        $brdat = $this->db->get()->row_array();
        $brand = ifset($brdat,'brand', 'SB');
        usersession('currentbrand', $brand);
        // Get default url for brand
        $this->db->select('menu_item_id, parent_id, item_link');
        $this->db->from('menu_items mi');
        $this->db->join('user_default_page usrp', 'usrp.page_id=mi.menu_item_id');
        $this->db->where('usrp.user_id', $user_id);
        $this->db->where('usrp.brand', $brand);
        $res = $this->db->get()->row_array();
        if (ifset($res, 'menu_item_id', 0)==0) {
            return '/welcome';
        } else {
            if (empty($res['parent_id'])) {
                return $res['item_link'];
            } else {
                $this->db->select('menu_item_id, item_link');
                $this->db->from('menu_items');
                $this->db->where('menu_item_id', $res['parent_id']);
                $main = $this->db->get()->row_array();
                return $main['item_link'].'/?start='.str_replace('#', '', $res['item_link']);
            }
        }
    }

    public function rebuild_currentuser($user_id)
    {
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
            'user_order_export' => $res['user_order_export'],
            'finuser' => $res['finuser'],
            'user_secret' => $res['user_secret'],
            'user_payuser' => $res['user_payuser'],
            'first_name' => $res['first_name'],
        );
        usersession('usr_data', $usr_data);
        return TRUE;
    }

    public function verify_user_code($code)
    {
        $out = ['result' => $this->error_result,'msg' => 'Sign in failed'];
        $user = usersession('usr_data');
        $this->load->library('GoogleAuthenticator');
        if (ifset($user,'id',0) > 0) {
            $out['msg'] = 'Empty User Secret Key';
            $secret = ifset($user,'user_secret','');
            if (!empty($secret)) {
                $out['msg'] = 'Invalid Verification code';
                $ga=new GoogleAuthenticator();
                $chkcode=$ga->getCode($secret);
                if ($chkcode == $code) {
                    $this->db->where('user_id',$user['id']);
                    $this->db->set('last_verified', time());
                    $this->db->update('users');
                    $out['result'] = $this->success_result;
                    // Get default Page
                    $this->db->select('user_page')->from('users')->where('user_id',$user['id']);
                    $pagedat = $this->db->get()->row_array();
                    $out['user_page'] = $pagedat['user_page'];
                    $out['user_id'] = $user['id'];
                }
            }
        }
        return $out;
    }

    private function _secret_code($user, $user_id)
    {
        // Init Mail
        $this->load->library('email');
        $email_conf = array(
            'protocol' => 'sendmail',
            'charset' => 'utf-8',
            'wordwrap' => TRUE,
            'mailtype' => 'html',
        );
        $this->email->initialize($email_conf);
        $email_from = 'admin@bluetrack.com';
        // Init GA
        $this->load->library('GoogleAuthenticator');
        $ga = new GoogleAuthenticator();
        $secret = $ga->generateSecret();
        $this->db->where('user_id', $user_id);
        $this->db->set('user_secret', $secret);
        $this->db->update('users');
        $usrlogin = ifset($user, 'userlogin','');
        if (empty($usrlogin)) {
            $usrlogin = $user['user_email'];
        }
        $url = $ga->getUrl($usrlogin, 'lift.bluetrack.com', $secret);
        $options = [
            'user_name' => $user['user_name'],
            'secret' => $secret,
            'url' => $url,
            'manual_url' => 'https://support.google.com/accounts/answer/1066447?hl=en',
        ];
        $message_body = $this->load->view('messages/secret_update_view', $options, TRUE);
        $this->email->to($user['user_email']);
        $this->email->from($email_from);
        $mail_subj = 'Update account security';
        $this->email->subject($mail_subj);
        $this->email->message($message_body);
        $this->email->send();
        $this->email->clear(TRUE);
        return TRUE;
    }

    public function clean_verification()
    {
        $this->db->where('user_status',1);
        $this->db->set('last_verified',0);
        $this->db->update('users');
        return true;
    }

    public function get_user_defaultpages($user_id)
    {
        $this->db->select('*')->from('user_default_page')->where('user_id',$user_id);
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function userpage_edit($item, $newval, $brand, $session_data, $session_id)
    {
        $out=['result' => $this->error_result, 'msg' => 'Select Brand'];
        if (!empty($brand)) {
            if ($brand=='SB') {
                $session_data['defsbpage'] = $newval;
            } elseif ($brand=='SR') {
                $session_data['defsrpage'] = $newval;
            } else {
                $session_data['defsgpage'] = $newval;
            }
            $out['result'] = $this->success_result;
            usersession($session_id, $session_data);
        }
        return $out;
    }

    public function default_brandpage($user_id, $brand)
    {
        $this->db->select('menu_item_id, parent_id, item_link');
        $this->db->from('menu_items mi');
        $this->db->join('user_default_page usrp', 'usrp.page_id=mi.menu_item_id');
        $this->db->where('usrp.user_id', $user_id);
        $this->db->where('usrp.brand', $brand);
        $res = $this->db->get()->row_array();
        if (ifset($res, 'menu_item_id', 0)==0) {
            return '/welcome';
        } else {
            if (empty($res['parent_id'])) {
                return $res['item_link'];
            } else {
                $this->db->select('menu_item_id, item_link');
                $this->db->from('menu_items');
                $this->db->where('menu_item_id', $res['parent_id']);
                $main = $this->db->get()->row_array();
                return $main['item_link'].'/?start='.str_replace('#', '', $res['item_link']);
            }
        }
    }

    public function get_printschedul_users()
    {
        $this->db->select('user_id, first_name')->from('users')->where(['print_scheduler' =>1,'user_status' => $this->user_active])->order_by('first_name');
        $users = $this->db->get()->result_array();
        return $users;
    }

}
/* End of file user_model.php */
/* Location: ./application/models/user_model.php */