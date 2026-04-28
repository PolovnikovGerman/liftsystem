<?php

class Easylogin extends Base_Controller
{
    public $success_result =1;
    public $error_result = 0;

    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->helper('cookie_helper');
    }

    public function index()
    {
        if ($this->config->item('test_server')==0) {
            redirect('/');
        } else {
            $token = $this->input->get('token', TRUE);
            $url = $this->input->get('url', TRUE);
            $chkuser = $this->user_model->get_user_accesstoken($token);
            if ($chkuser['result']==$this->success_result) {
                $user=$chkuser['user'];
                usersession('usr_data', $user);
                // If empty cookie
                $server=$this->input->server('SERVER_NAME');
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
                redirect('/login');
            }
        }
    }
}