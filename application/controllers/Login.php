<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Base_Controller
{
    public $success_result =1;
    public $error_result = 0;


    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
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
            $res = $this->user_model->login($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['chkcode'] = 0;
                $usrdat = $res['usrdat'];
                if (!empty($usrdat['user_secret'])) {
                    $curtime = time();
                    if ($curtime - intval($usrdat['last_verified']) > (24*60*60)) {
                        // Prepare verify
                        $mdata['content'] = $this->load->view('page/unlock_content_view', [], TRUE);
                        $mdata['chkcode'] = 1;
                    } else {
                        $mdata['url']='welcome';
                        if (ifset($usrdat, 'user_page',0) > 0) {
                            $mdata['url'] = $this->user_model->default_page($usrdat['user_page']);
                        }
                        $this->load->model('useractivity_model');
                        $this->useractivity_model->userlog($usrdat['user_id'],'Sign in', 1);
                    }
                } else {
                    $mdata['url']='welcome';
                    if (ifset($usrdat, 'user_page',0) > 0) {
                        $mdata['url'] = $this->user_model->default_page($usrdat['user_page']);
                    }
                    $this->load->model('useractivity_model');
                    $this->useractivity_model->userlog($usrdat['user_id'],'Sign in', 1);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Verify code
    public function codeverify()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Very Code';
            $postdata = $this->input->post();
            $code = ifset($postdata,'code','');
            if (!empty($code)) {
                $res = $this->user_model->verify_user_code($code);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['url']='welcome';
                    if (ifset($res, 'user_page',0) > 0) {
                        $mdata['url'] = $this->user_model->default_page($res['user_page']);
                    }
                    $this->load->model('useractivity_model');
                    $this->useractivity_model->userlog($res['user_id'],'Verify Account', 1);

                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function logout() {
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