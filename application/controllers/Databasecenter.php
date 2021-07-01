<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Databasecenter extends MY_Controller
{
    private $pagelink = '/databasecenter';
    private $Inventory_Source='Stock';
    private $STRESSBALL_TEMPLATE='Stressball';
    private $OTHER_TEMPLATE='Other Item';
    private $MAX_PROMOPRICES = 10;

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

    public function index() {
        $head = [];
        $head['title'] = 'Database';
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
        $master=[];
        $channelsb=[];
        $channelnsb=[];
        $channelbt=[];
        $channellbt=[];
        $channelamz=[];
        foreach ($menu as $mitems) {
            if ($mitems['item_link']=='#dbcentermaster') {
                foreach ($mitems['submenu'] as $mitem) {
                    $master[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            } elseif ($mitems['item_link']=='#dbcentersbchannel') {

            }
        }

        $content_options = [
            'master' => $master,
        ];
        $search = usersession('liftsearch');
        usersession('liftsearch', NULL);
        // Add main page management
        $head['styles'][] = array('style' => '/css/database_center/main_page.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        // Utils
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
        // Item details
        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $content_view = $this->load->view('database_center/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function masteritems($start) {
        
    }
}
