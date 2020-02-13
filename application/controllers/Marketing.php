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

    public function searchtimedata() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $period = ifset($postdata,'period');
            $brand = ifset($postdata,'brand', 'ALL');
            $mdata=[];
            $error = 'Empty Search Parameter';

            if (!empty($period) && !empty($brand)) {
                if ($period=='week') {
                    $dates = getDatesByWeek(date('W'),date('Y'));
                    $d_bgn = $dates['start_week'];
                    $d_end = $dates['end_week'];
                }

                $this->load->model('searchresults_model');
                $data=$this->searchresults_model->get_search_bytime($d_bgn,$d_end);
                $content=$this->load->view('search/searchtime_dat_view',$datz,TRUE);


            }
            $this->ajaxResponse($mdata, $error);

//            switch ($period) {
//                case 'alltime':
//                    $d_bgn='';
//                    $d_end='';
//                    break;
//                case 'week':
//                    $curtime=date('Y-m-d').' 00:00:00';
//                    $curtime=strtotime($curtime);
//                    $curweekday=date('w');
//                    if ($curweekday==0) {
//                        $d_bgn=$curtime-(6*24*60*60);
//                        $d_end=$curtime+(23*60*60+59*60+59);
//                    } elseif ($curweekday==1) {
//                        $d_bgn=$curtime;
//                        $d_end=$curtime+(6*24*60*60)+(23*60*60+59*60+59);
//                    } elseif ($curweekday==2) {
//                        $d_bgn=$curtime-(1*24*60*60);
//                        $d_end=$curtime+(5*24*60*60)+(23*60*60+59*60+59);
//                    } elseif ($curweekday==3) {
//                        $d_bgn=$curtime-(2*24*60*60);
//                        $d_end=$curtime+(4*24*60*60)+(23*60*60+59*60+59);
//                    } elseif ($curweekday==4) {
//                        $d_bgn=$curtime-(3*24*60*60);
//                        $d_end=$curtime+(3*24*60*60)+(23*60*60+59*60+59);
//                    } elseif ($curweekday==5) {
//                        $d_bgn=$curtime-(4*24*60*60);
//                        $d_end=$curtime+(2*24*60*60)+(23*60*60+59*60+59);
//                    } elseif ($curweekday==6) {
//                        $d_bgn=$curtime-(5*24*60*60);
//                        $d_end=$curtime+(1*24*60*60)+(23*60*60+59*60+59);
//                    }
//                    break;
//                case 'month':
//                    $curtime=date('Y-m-').'01 00:00:00';
//                    $d_bgn=strtotime($curtime);
//                    $curtime=date('Y-m-t').' 23:59:59';
//                    $d_end=strtotime($curtime);
//                    break;
//                case 'custom':
//                    $d_bgn=$this->input->post('d_bgn');
//                    if ($d_bgn!='') {
//                        $d_bgn=$d_bgn.' 00:00:00';
//                        $d_bgn=strtotime($d_bgn);
//                    }
//                    $d_end=$this->input->post('d_end');
//                    if ($d_end!='') {
//                        $d_end=$d_end.' 23:59:59';
//                        $d_end=strtotime($d_end);
//                    }
//                    break;
//            }
//
//            $datz=array();
//            $datz['dat']=$this->msearch->get_search_bytime($d_bgn,$d_end);
//            $content=$this->load->view('search/searchtime_dat_view',$datz,TRUE);
//            echo json_encode(array('content'=>$content));
        }
        show_404();
    }

    private function _prepare_searchbytime($brand, $top_menu) {
        $options = [
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        return $this->load->view('marketing/search_time_view', $options,TRUE);
    }
}