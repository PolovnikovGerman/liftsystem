<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mailbox extends MY_Controller
{

    private $pagelink = '/mailbox';

    public function __construct()
    {
        parent::__construct();
        $brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink,0, $brand);
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
        $this->load->model('mailbox_model');
    }

    public function index() {
        $head = [];
        $head['title'] = 'Mailbox';
        $brand = $this->menuitems_model->get_current_brand();
        // $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        // Get User Mailboxes
        $postboxes = $this->mailbox_model->get_user_mailboxes($this->USR_ID);
        $menu_view = $this->load->view('mailbox/postboxes_view', ['postboxes' => $postboxes], TRUE);
        $content_options = [];
        $gmaps = 0;
//        if (!empty($this->config->item('google_map_key'))) {
//            $gmaps = 1;
//        }

        $content_options['menu'] = $menu_view;
        // Add main page management
        $head['scripts'][] = array('src' => '/js/mailbox/page.js');
        $head['styles'][] = array('style' => '/css/mailbox/page.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // DatePicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
//        /*Google Chart */
//        $head['scripts'][]=array('src'=>"https://www.gstatic.com/charts/loader.js");
        // Order popup
//        $head['styles'][]=array('style'=>'/css/leadorder/popup.css');
//        $head['scripts'][]=array('src'=>'/js/leads/leadorderpopup.js');
//        if ($gmaps==1) {
//            $head['scripts'][]=array('src'=>'/js/leads/order_address.js');
//        }
        // Uploader
//        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
//        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
//        // File Download
//        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
//        // Select 2
//        $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
//        $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];
        // Scroll panel
        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
        ];
        if ($gmaps==1) {
            $options['gmaps'] = $gmaps;
        }
        $options['googlefont'] = 1;
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $content_view = $this->load->view('mailbox/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function postbox_details()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox',0);
            $error = 'Empty Postbox Parameter';
            if (!empty($postbox)) {
                $res = $this->mailbox_model->get_postbox_details($postbox);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $folders_view = $this->load->view('mailbox/folders_view',['folders'=>$res['folders']], TRUE);
                    $messages_view = '';
                    $options = [
                        'folders' => $folders_view,
                        'messages' => $messages_view,
                    ];
                    $mdata['content'] = $this->load->view('mailbox/postbox_details_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}
