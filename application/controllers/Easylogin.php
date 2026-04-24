<?php

class Easylogin extends Base_Controller
{
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
        redirect('/');
    }
}