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
        $brand = $this->menuitems_model->get_current_brand();
        if (empty($brand)) {
            $brands = $this->menuitems_model->get_userbrands($this->USR_ID);
            if (!empty($brands)) {
                if (isset($brands['SB'])) {
                    $brand = 'SB';
                } else {
                    $brand = 'SR';
                }
            } else {
                $brand = 'SB';
            }
            usersession('currentbrand', $brand);
        }
        $url = $this->user_model->default_brandpage($this->USR_ID, $brand);
        if ($url=='/welcome' || $url=='/' || empty($url)) {
            $options = [
                'title' => $head['title'],
                'user_id' => $this->USR_ID,
                'user_name' => $this->USER_NAME,
                'activelnk' => '',
                'brand' => $brand,
            ];
            $dat = $this->template->prepare_pagecontent($options);
            $options=[
                'left_menu' => $dat['left_menu'],
                'brand' => $brand,
            ];
            $dat['content_view'] = $this->load->view('welcome/page_view', $options, TRUE);
            $this->load->view('page/page_template_view', $dat);
        } else {
            redirect($url,'refresh');
        }
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
            // $search_type = ifset($postdata,'search_type','unk');
            $search_type = 'Orders';
            $search_template = ifset($postdata,'search_template','');
            if ($search_type=='Orders') {
                $mdata['url'] = '/orders';
                usersession('liftsearch', $search_template);
            // } elseif ($search_type=='Items') {
            //    $mdata['url'] = '/database';
            //    usersession('liftsearch', $search_template);
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
            $mdata['revenue'] = MoneyOutput($totals['revenue'],0);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function brandnavigate() {
        if ($this->isAjax()) {
            $error = 'Url Not Found';
            $mdata = [];
            $postdata = $this->input->post();
            if (ifset($postdata,'url', '')!=='') {
                $error = '';
                $currentbrand=ifset($postdata,'brand','SB');
                usersession('currentbrand', $currentbrand);
                $mdata['url'] = $postdata['url'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function brandshow() {
        if ($this->isAjax()) {
            $error = 'Url Not Found';
            $mdata = [];
            $postdata = $this->input->post();
            if (ifset($postdata,'brand', '')!=='') {
                $error = '';
                $currentbrand=$postdata['brand'];
                usersession('currentbrand', $currentbrand);

                $this->load->model('user_model');
                // $usrdat = $this->user_model->get_user_data($this->USR_ID);
                // $url = '/';
                // if (!empty($usrdat['user_page'])) {
                $url = $this->user_model->default_brandpage($this->USR_ID, $currentbrand);
                // }
                $mdata['url'] = $url;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function weektotals($curweek) {
        $this->load->model('dashboard_model');
        $totals = $this->dashboard_model->get_totals_brand( 'totals', $curweek);
        $msg = $this->load->view('page/dashboard_totalrevenuebrand_view', $totals, TRUE);
        echo $msg;
    }

    public function weektotalorders($curweek) {
        $this->load->model('dashboard_model');
        $totals = $this->dashboard_model->get_totals_brand('orders', $curweek);
        $msg = $this->load->view('page/dashboard_totalbrand_view',['totals'=> $totals], TRUE);
        echo $msg;
    }

    public function submenus() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Menu Item Not Found';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand','DS');
            $menu = ifset($postdata,'menu', 0);
            $user_id = $this->USR_ID;
            $this->load->model('menuitems_model');
            $maindat = $this->menuitems_model->get_menuitem('',$menu);
            if ($maindat['result']==$this->success_result) {
                $error = '';
                $mainurl = $maindat['menuitem']['item_link'];
                $data = $this->menuitems_model->get_user_submenu($menu, $user_id);
                $mdata['content'] = $this->load->view('page/submenu_view',['items' => $data, 'url' => $mainurl, 'brand' => $brand], TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function unlockcontent()
    {
        if ($this->isAjax()) {
            $error = '';
            $mdata=[];
            $mdata['content'] = $this->load->view('page/unlock_content_view',[], TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function viewbalance()
    {
        $this->load->model('dashboard_model');
        $totals = $this->dashboard_model->get_total_balance();
        if (count($totals) > 0) {
            $msg = $this->load->view('page/dashboard_totalbalance_view',['totals'=> $totals], TRUE);
        } else {
            $msg = $this->load->view('page/dashboard_totalempty_view',[], TRUE);
        }
        echo $msg;
    }

    public function salestotals()
    {
        if ($this->isAjax()) {
            $error = 'Date wasn\'t define';
            $mdata = [];
            $postdata = $this->input->post();
            $weekdate = ifset($postdata,'weekdate','');
            if (!empty($weekdate)) {
                $error = '';
                $this->load->model('dashboard_model');
                $weeknum = date('W', $weekdate);
                $year = date('Y', $weekdate);
                $total_options = $this->dashboard_model->get_totals('week', $weeknum, $year);
                $mdata['content'] = $this->load->view('page/dashboard_total_view', $total_options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function weektotalvisitors($curweek)
    {
        $this->load->model('dashboard_model');
        $totals = $this->dashboard_model->get_leadvisits_week($curweek);
        $msg = $this->load->view('page/dashboard_leadvisitors_view', $totals, TRUE);
        echo $msg;
    }
}
