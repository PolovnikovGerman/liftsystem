<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Art extends MY_Controller {

    private $pagelink = '/art';


    public function __construct()
    {
        parent::__construct();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink);
        if ($pagedat['result']==$this->error_result) {
            show_404();
        }
        $page = $pagedat['menuitem'];
        $permdat = $this->menuitems_model->get_menuitem_userpermisiion($this->USR_ID, $page['menu_item_id']);
        if ($permdat['result']==$this->success_result && $permdat['permission']>0) {
        } else {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url' => '/'), 'Your have no permission to this page');
            } else {
                redirect('/');
            }
        }
    }

    public function index() {


        $this->load->view('design/main_art_view');
    }
}