<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// insert into menu_items (item_name, item_link, menu_order, menu_section, newver, brand)
// values('Postbox','/mailbox',26,'marketsection', 1,'SB');

class Mailbox extends MY_Controller
{

    private $pagelink = '/mailbox';
    public $current_brand;

    public function __construct()
    {
        parent::__construct();
        $this->current_brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink, 0, $this->current_brand);
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
        $head['title'] = 'Postbox';
        $brand = $this->current_brand;
        // $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        // $head['scripts'][] = array('src' => '/js/accounting/page.js');
        $head['styles'][] = array('style' => '/css/postbox/page.css');

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
//            'scripts' => $head['scripts'],
            'brand' => $brand,
        ];
//        if ($gmaps==1) {
//            $options['gmaps'] = $gmaps;
//        }
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['brand'] = $brand;
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options['menu_view'] = $this->load->view('page_modern/submenu_view',['menu' => [], 'start' => '', 'brandclass' => $brandclass ], TRUE);
        $content_view = $this->load->view('postbox/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $dat['modal_view'] = ''; // $this->load->view('accounting/modal_view',[], TRUE);
        $this->load->view('page_modern/page_template_view', $dat);

    }
}
