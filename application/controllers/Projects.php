<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends MY_Controller
{

    private $pagelink = '/projects';
    public $current_brand;

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

        $content_options = [];
        $start = $this->input->get('start', TRUE);
        foreach ($menu as $row) {
            if ($row['item_link']=='#projectsview') {
                // $head['styles'][] = array('style' => '/css/projects/projects.css');
                // $head['scripts'][] = array('src' => '/js/projects/projects.js');
                $content_options['projectsview'] = $this->_prepare_projects_view();
            }
        }
        $content_options['menu'] = $menu;
        // Add main page management
        $head['scripts'][] = array('src' => '/js/projects/page.js');
        $head['styles'][] = array('style' => '/css/projects/page.css');
        // Add Order Dual Orders
        $head['styles'][] = array('style' => '/css/projects/dualorders_view.css');
        $head['scripts'][] = array('src' => '/js/projects/dualorders_view.js');
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'brand' => $brand,
        ];

        $dat = $this->template->prepare_pagecontent($options);
        // $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options['menu_view'] = $this->load->view('page_modern/submenu_view',['menu' => $menu, 'start' => $start, 'brandclass' => $brandclass ], TRUE);
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

    private function _prepare_projects_view() {
        $options = [
            'liftlink' => getenv('LIFTTEST'),
            'bluelink' => getenv('BLUETRACKTEST'),
            'relivlink' => getenv('RELIVERSTEST'),
            'designlink' => getenv('DESIGSTEST'),
            'testorderlink' => getenv('TESTORDERLINK'),
        ];
        // $options['doubleorder'] = $this->load->view('dualorders/page_view',[], true);
        $content = $this->load->view('projects/projects_view', $options, TRUE);
        return $content;
    }
}
