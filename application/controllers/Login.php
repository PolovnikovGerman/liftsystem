<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public $success_result =1;
    public $error_result = 0;


    public function __construct()
    {
        parent::__construct();
    }

    public function index() {
        $head=[];
        $head['title']='Sign In';
        $dat = $this->template->prepare_public_page($head);
        $dat['content'] = $this->load->view('signin/page_view',[], TRUE);
        $this->load->view('public_pages/public_template_view', $dat);
    }

    public function show_signinform() {
        if ($this->isAjax()) {
            $mdata=[];
            $mdata['content']=$this->load->view('signin/form_view', [], TRUE);
            $this->ajaxResponse($mdata);
        }
    }

    public function signin() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $this->load->model('user_model');
            $res = $this->user_model->login($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['url']='welcome';
                $usrdat = $res['usrdat'];
                $this->load->model('useractivity_model');
                $this->useractivity_model->userlog($usrdat['user_id'],'Sign in', 1);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function logout() {
        $this->load->model('user_model');
        $this->user_model->signout();
        redirect('/login');
    }

    private function ajaxResponse($mdata, $merrors = array())
    {
        $aResponse = array(
            'data' => $mdata,
            'errors' => $merrors
        );
        echo(json_encode($aResponse));
        exit;
    }

    private function isAjax()
    {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
        {
            return TRUE;
        }

        return FALSE;
    }


}