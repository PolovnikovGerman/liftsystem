<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends MY_Controller
{

    private $pagelink = '/database';


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
        $head['title'] = 'Database';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link'] == '#customshappedview') {
                // Custom shaped
                $head['styles'][] = array('style' => '/css/content/customshape_page.css');
                $head['scripts'][] = array('src' => '/js/content/custom_shaped.js');
            }
        }
        $content_options['menu'] = $menu;
        $content_view = $this->load->view('database/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/database/page.js');
        $head['styles'][] = array('style' => '/css/database/databasepage.css');
        // Utils

        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');

        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

}