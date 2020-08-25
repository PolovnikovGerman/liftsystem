<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $head=[];
        $head['title']='Welcome';
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = '';
        $this->load->view('page/page_template_view', $dat);
    }

    /* Open File content */
    public function art_openimg() {
        $url = $this->input->post('url');
        $filename = $this->input->post('file');
        /* Get extension */
        openfile($url, $filename);
    }

    // Lift search
    public function liftsite_search() {
        if ($this->isAjax()) {
            $mdata =[
                'url' => '',
            ];
            $error = '';
            $postdata = $this->input->post();
            $search_type = ifset($postdata,'search_type','unk');
            $search_template = ifset($postdata,'search_template','');
            if ($search_type=='Orders') {
                $mdata['url'] = '/orders';
                usersession('liftsearch', $search_template);
            } elseif ($search_type=='Items') {
                $mdata['url'] = '/database';
                usersession('liftsearch', $search_template);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function testtabs() {
        $this->load->view('test/test',[]);
    }

    public function restore_main_menu() {
        if ($this->isAjax()) {
            $error = '';
            $mdata = [];
            $postdata = $this->input->post();
            $activelnk = ifset($postdata,'activelnk', '');
            $this->load->model('menuitems_model');
            $menu_options = [
                'activelnk'=>$activelnk,
                'permissions' => $this->menuitems_model->get_user_permissions($this->USR_ID),
            ];
            $mdata['content'] = $this->load->view('page/menu_view', $menu_options, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function ordertotalsparse() {
        if ($this->isAjax()) {
            $error = '';
            $mdata = [];
            $this->load->model('dashboard_model');
            $res = $this->dashboard_model->get_totals('week');
            $totals = $res['data'];
            $mdata['sales'] = QTYOutput($totals['sales']);
            $mdata['revenue'] = MoneyOutput($totals['revenue']);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}
