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



}
/* End of file user_model.php */
/* Location: ./application/models/user_model.php */