<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends MY_Controller
{

    private $pagelink = '/projects';
    public $current_brand;
    private $doubleorderwidth = '1276px';
    private $leadorderwidth = '755px';
    public function __construct()
    {
        parent::__construct();
        $this->current_brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink,0, $this->current_brand);
        if ($pagedat['result'] == $this->error_result) {
            show_404();
        }
        $page = $pagedat['menuitem'];
        $permdat = $this->menuitems_model->get_menuitem_userpermisiion($this->USR_ID, $page['menu_item_id']);
        if ($permdat['result'] == $this->success_result && $permdat['permission'] > 0) {
        } else {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url' => '/'), 'Your have no permission to this page');
            } else {
                redirect('/');
            }
        }
    }

    public function index() {
        $head = [];
        $head['styles'] = $head['scripts'] = [];
        $head['title'] = 'Projects';
        $brand = $this->current_brand;
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        $start = $this->input->get('start', TRUE);

        $content_options = [];
        $start = $this->input->get('start', TRUE);
        foreach ($menu as $row) {
            if ($row['item_link']=='#projectsview') {
                // $head['styles'][] = array('style' => '/css/projects/projects.css');
                // $head['scripts'][] = array('src' => '/js/projects/projects.js');
                $projoptions = [
                    'brand' => $brand,
                    'start' => $start,
                ];
                $content_options['projectsview'] = $this->_prepare_projects_view($projoptions);
            }
        }
        $content_options['menu'] = $menu;
        // Add main page management
        $head['scripts'][] = array('src' => '/js/projects/page.js');
        $head['styles'][] = array('style' => '/css/projects/page.css');
        // Add Order Dual Orders
        $head['styles'][] = array('style' => 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css');
        $head['styles'][] = array('style' => '/css/projects/leadorders_view.css');
        $head['scripts'][] = array('src' => 'https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js');
        $head['scripts'][] = array('src' => '/js/projects/leadorders_view.js');
        $head['styles'][] = array('style' => '/css/projects/oldorders_view.css');
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'brand' => $brand,
            'showhidemenu' => 1,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        // $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options['menu_view'] = $this->load->view('page_modern/submenu_view',['menu' => $menu, 'start' => $start, 'brandclass' => $brandclass ], TRUE);
        $content_options['brandclass'] = $brandclass;
        $content_options['showhidemenu'] = 1;
        $content_view = $this->load->view('projects/page_new_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $dat['modal_view'] = $this->load->view('projects/modal_view', [], TRUE);
        $this->load->view('page_modern/page_template_view', $dat);
    }

    public function vieworders()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $blocked = ifset($postdata, 'blocked', 0);
            $brand = $this->current_brand;
            $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
            $orderoptions = [
                'brandclass' => $brandclass,
                'brand' => $brand,
                'blocked' => $blocked,
            ];
            $error = '';
            $mdata = [];
            $mdata['content'] = $this->load->view('dualorders/page_view', $orderoptions, true);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function viewcontent()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $blocked = ifset($postdata, 'blocked', 0);
            $content = ifset($postdata, 'content', 'dualorders');
            $brand = $this->current_brand;
            $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
            $orderoptions = [
                'brandclass' => $brandclass,
                'brand' => $brand,
                'blocked' => $blocked,
            ];
            $error = '';
            $mdata = [];
            $customer_view = $this->load->view('dualorders/customer_data_view', $orderoptions, true);
            $viewoptions = [
                'brandclass' => $brandclass,
                'brand' => $brand,
                'blocked' => $blocked,
                'customer_view' => $customer_view,
            ];
            if ($content=='dualorders') {
                $viewoptions['order_view'] = $this->load->view('dualorders/oldorder_view', [], true);
                $mdata['content'] = $this->load->view('dualorders/page_dualorders_view', $viewoptions, true);
                $mdata['modalwidth'] = $this->doubleorderwidth;
//                $mdata['content'] = $this->load->view('dualorders/page_orders_view', $viewoptions, true);
//            } elseif ($content=='leadsview') {
//                $mdata['content'] = $this->load->view('dualorders/leads_view', $viewoptions, true);
//                $mdata['modalwidth'] = $this->leadorderwidth;
            } elseif ($content=='orderleaddataview') {
                $viewoptions['order_view'] = $this->load->view('dualorders/order_view', [], true);
                $viewoptions['lead_view'] = $this->load->view('dualorders/lead_view', [], true);
                $mdata['content'] = $this->load->view('dualorders/orderlead_view', $viewoptions, true);
                $mdata['modalwidth'] = $this->doubleorderwidth;
            } else {
                $mdata['content'] = $this->load->view('dualorders/order_lead_view', $viewoptions, true);
                $mdata['modalwidth'] = $this->leadorderwidth;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function getaccess()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $url = ifset($postdata, 'url', 'home');
            $user_id = $this->USR_ID;
            $this->load->model('user_model');
            $res = $this->user_model->projectaccess($user_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $easylink = getenv('EASYLINK');
                $mdata['viewurl'] = $easylink.'?token='.$res['token'].'&url='.$url;
                // $mdata['token'] = $res['token'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_projects_view($projoptions) {
        $options = [
            'liftlink' => getenv('LIFTTEST'),
            'bluelink' => getenv('BLUETRACKTEST'),
            'relivlink' => getenv('RELIVERSTEST'),
            'dualorders' => getenv('DUALORDERS'),
            'orderleads' => getenv('ORDERLEADS'),
            'orderleaddata' => getenv('ORDERLEADDATA'),
            'complexlead' => getenv('COMPLEXLEAD'),
            'brand' => $projoptions['brand'],
            'start' => $projoptions['start'],
        ];
        // $options['doubleorder'] = $this->load->view('dualorders/page_view',[], true);
        if ($this->config->item('test_server')==0) {
            $content = $this->load->view('projects/projects_live_view', $options, TRUE);
        } else {
            $content = $this->load->view('projects/projects_view', $options, TRUE);
        }
        return $content;
    }
}
