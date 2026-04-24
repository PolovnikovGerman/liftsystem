<?php

class Easylogin extends Base_Controller
{
    public $success_result =1;
    public $error_result = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function index()
    {
        $token = $this->input->get('token', TRUE);
        $url = $this->input->get('url', TRUE);
        log_message('error', 'Easylogin start Token '.$token.' url '.$url);
        $chkuser = $this->user_model->get_user_accesstoken($token);
        if ($chkuser['result']==$this->success_result) {
            log_message('error', 'Token '.$token.'  active ');
            $user=$chkuser['user'];
            foreach ($user as $key=>$value) {
                log_message('error', 'User param '.$key.' '.$value);
            }
            usersession('usr_data', $user);
            // If empty cookie
            $server=$this->input->server('SERVER_NAME');
            log_message('error', 'Easylogin server '.$server);
            $cookienew = array(
                'name'   => 'acctoken',
                'value'  => $token,
                'expire' => '86500',
                'domain' => $server,
            );
            set_cookie($cookienew);
            if ($url=='home') {
                redirect('/');
            } else {
                redirect('/'.$url);
            }
        } else {
            log_message('error', 'Token '.$token.'  not found ');
            redirect('/login');
        }
    }
}