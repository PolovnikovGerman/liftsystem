<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing extends MY_Controller
{

    private $pagelink = '/marketing';

    public function __construct()
    {
        parent::__construct();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink);
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

    public function index()
    {
        $head = [];
        $head['title'] = 'Marketing';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);

        $brands = $this->menuitems_model->get_brand_permisions($this->USR_ID, $this->pagelink);
        if (count($brands)==0) {
            redirect('/');
        }
        $brand = $brands[0]['brand'];
        $top_options = [
            'brands' => $brands,
            'active' => $brand,
        ];

        $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);

        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#searchestimeview') {
                // Search results by time
                $head['styles'][]=array('style'=>'/css/marketing/searchestimeview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searchestimeview.js');
                $content_options['searchestimeview'] = $this->_prepare_searchbytime($brand, $top_menu);
            } elseif ($row['item_link']=='#searcheswordview') {
                // Search Results by Keywords
                // $head['styles'][]=array('style'=>'/css/marketing/searcheswordview.css');
                // $head['scripts'][]=array('src'=>'/js/marketing/searcheswordview.js');
                $content_options['searcheswordview'] = ''; // $this->_prepare_orderlist_view();
            } elseif ($row['item_link']=='#searchesipadrview') {
                // Search results by IP
                // $head['styles'][]=array('style'=>'/css/marketing/searchesipadrview.css');
                // $head['scripts'][]=array('src'=>'/js/marketing/searchesipadrview.js');
                $content_options['searchesipadrview'] = ''; // $this->_prepare_requestlist_view();
            } elseif ($row['item_link']=='#signupview') {
                // Search results by IP
                $head['styles'][]=array('style'=>'/css/marketing/signupview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/signupview.js');
                $content_options['signupview'] = ''; // $this->_prepare_requestlist_view();
            } elseif ($row['item_link']=='#couponsview') {
                // Search results by IP
                $head['styles'][]=array('style'=>'/css/marketing/couponsview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/couponsview.js');
                $content_options['couponsview'] = ''; // $this->_prepare_requestlist_view();
            }
        }

        $content_options['menu'] = $menu;
        $content_view = $this->load->view('marketing/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/marketing/page.js');
        $head['styles'][] = array('style' => '/css/marketing/marketpage.css');
        // Utils
        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    private function _prepare_searchbytime($brand, $top_menu) {
        $options = [
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        return $this->load->view('marketing/search_time_view', $options,TRUE);
    }
}