<?php

class Leaddesign extends MY_Controller
{
    public $current_brand;
    private $pagelink = '/projects';

    public function __construct()
    {
        parent::__construct();
        $this->current_brand = $this->menuitems_model->get_current_brand();
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Accounting';
        $brand = $this->current_brand;
        $menu = [];
        $start = '';
        $head['styles'][]=array('style'=>'/css/leaddesign/page.css');
        $head['scripts'][]=array('src'=>'/js/leaddesign/page.js');
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
//        if ($gmaps==1) {
//            $options['gmaps'] = $gmaps;
//        }
        $dat = $this->template->prepare_pagecontent($options);
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        $content_options['brand'] = $brand;
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options['brandclass'] = $brandclass;
        $content_options['menu_view'] = $this->load->view('page_modern/submenu_view',['menu' => $menu, 'start' => 'projectsview', 'brandclass' => $brandclass ], TRUE);
        $content_view = $this->load->view('leaddesign/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        // $dat['modal_view'] = $this->load->view('accounting/modal_view',[], TRUE);
        $this->load->view('page_modern/page_template_view', $dat);


    }
}