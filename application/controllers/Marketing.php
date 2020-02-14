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
                $head['styles'][]=array('style'=>'/css/marketing/searcheswordview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searcheswordview.js');
                $content_options['searcheswordview'] = $this->_prepare_searchbywords($brand, $top_menu);
            } elseif ($row['item_link']=='#searchesipadrview') {
                // Search results by IP
                $head['styles'][]=array('style'=>'/css/marketing/searchesipadrview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searchesipadrview.js');
                $content_options['searchesipadrview'] = $this->_prepare_searckipaddress($brand, $top_menu);
            } elseif ($row['item_link']=='#signupview') {
                // Search results by IP
                $head['styles'][]=array('style'=>'/css/marketing/signupview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/signupview.js');
                $content_options['signupview'] = $this->_prepare_signup($brand, $top_menu);
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
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Pagination
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
        ];
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
                $error = 'Unknown period';
                if (in_array($period,['week','month','custom'])) {
                    $error = '';
                    if ($period=='week') {
                        $dates = getDatesByWeek(date('W'),date('Y'));
                        $d_bgn = $dates['start_week'];
                        $d_end = $dates['end_week'];
                    } elseif ($period=='month') {
                        $curtime=date('Y-m-').'01 00:00:00';
                        $d_bgn=strtotime($curtime);
                        $curtime=date('Y-m-t').' 23:59:59';
                        $d_end=strtotime($curtime);
                    } else {
                        $d_bgn=ifset($postdata,'d_bgn','');
                        if (!empty($d_bgn)) {
                            $d_bgn=strtotime($d_bgn);
                        }
                        $d_end=ifset($postdata,'d_end','');
                        if (!empty($d_end)) {
                            $d_end=strtotime($d_end. ' 23:59:59');
                        }
                    }
                    $this->load->model('searchresults_model');
                    $data=$this->searchresults_model->get_search_bytime($brand, $d_bgn,$d_end);
                    $mdata['content']=$this->load->view('marketing/searchtime_dat_view',['dat' => $data],TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function searchkeyworddata() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $period = ifset($postdata, 'period');
            $show_result = ifset($postdata, 'result');
            $brand = ifset($postdata,'brand');
            $error = 'Empty Parameters';
            if (!empty($period) && !empty($show_result) && !empty($brand)) {
                $error='Unknown Period';
                if (in_array($period,['today','week','month','custom'])) {
                    $error = '';
                    switch ($period) {
                        case 'custom':
                            $d_bgn=ifset($postdata, 'd_bgn');
                            if (!empty($d_bgn)) {
                                $d_bgn=strtotime($d_bgn.' 00:00:00');
                            }
                            $d_end=ifset($postdata, 'd_end');
                            if (!empty($d_end)) {
                                $d_end=strtotime($d_end.' 23:59:59');
                            }
                            break;
                        case 'today' :
                            $d_bgn=time();
                            $d_end=time();
                            break;
                        case 'week' :
                            $dates = getDatesByWeek(date('W'),date('Y'));
                            $d_bgn = $dates['start_week'];
                            $d_end = $dates['end_week'];
                            break;
                        case 'month':
                            $curtime=date('Y-m-').'01 00:00:00';
                            $d_bgn=strtotime($curtime);
                            $curtime=date('Y-m-t').' 23:59:59';
                            $d_end=strtotime($curtime);
                            break;
                    }
                    $this->load->model('searchresults_model');
                    $data=$this->searchresults_model->get_search_bykeywords($brand, $d_bgn,$d_end,$show_result);
                    $mdata['num_cols']=ceil(count($data)/20);
                    $mdata['content']=$this->load->view('marketing/searchkeyword_dat_view',['dat'=> $data],TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function searchipaddresdata() {
        if ($this->isAjax()) {
            $mdata=[];
            $error='Empty Parameter';
            $postdata = $this->input->post();
            $period = ifset($postdata,'period');
            $brand = ifset($postdata,'brand');
            if (!empty($period) && !empty($brand)) {
                $error = 'Unknown period';
                if (in_array($period,['today','week','month','custom'])) {
                    $error = '';
                    switch ($period) {
                        case 'custom':
                            $d_bgn = ifset($postdata, 'd_bgn');
                            if (!empty($d_bgn)) {
                                $d_bgn = strtotime($d_bgn . ' 00:00:00');
                            }
                            $d_end = ifset($postdata, 'd_end');
                            if (!empty($d_end)) {
                                $d_end = strtotime($d_end . ' 23:59:59');
                            }
                            break;
                        case 'today' :
                            $d_bgn = time();
                            $d_end = time();
                            break;
                        case 'week' :
                            $dates = getDatesByWeek(date('W'), date('Y'));
                            $d_bgn = $dates['start_week'];
                            $d_end = $dates['end_week'];
                            break;
                        case 'month':
                            $curtime = date('Y-m-') . '01 00:00:00';
                            $d_bgn = strtotime($curtime);
                            $curtime = date('Y-m-t') . ' 23:59:59';
                            $d_end = strtotime($curtime);
                            break;
                    }
                    $this->load->model('searchresults_model');
                    $data=$this->searchresults_model->get_search_byipaddress($brand, $d_bgn,$d_end);
                    $mdata['num_cols']=ceil(count($data)/20);
                    $mdata['content']=$this->load->view('marketing/searchipaddress_dat_view',['dat'=>$data],TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function signupsdat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $pagenum = ifset($postdata,'offset',0);
                $limit = ifset($postdata,'limit', 500);
                $order_by = ifset($postdata,'order_by','email_id');
                $direct = ifset($postdata,'direction','desc');
                $maxval = ifset($postdata,'maxval',0);
                $etype = ifset($postdata,'type', 0);

                $ordoffset=$pagenum*$limit;
                $offset=$pagenum*$limit;

                /* Fetch data about prices */
                $options=['email_type' => 'Signups','brand' => $brand];
                if ($etype==1) {
                    $options['email_status']=0;
                }
                $startdate = ifset($postdata,'startdate');
                if (!empty($startdate)) {
                    $options['startdate'] = strtotime($postdata['startdate']);
                }
                $enddate = ifset($postdata,'enddate');
                if (!empty($enddate)) {
                    $options['enddate'] = strtotime($postdata['enddate'].' 23:59:59');
                }
                $this->load->model('email_model');
                $email_dat=$this->email_model->get_emails($options,$order_by,$direct,$limit,$offset);
                if ($ordoffset>$maxval) {
                    $ordnum = $maxval;
                } else {
                    $ordnum = $maxval - $ordoffset;
                }
                $data = $this->email_model->prepare_signupcontent($email_dat, $ordnum);
                $email_dat_left=$data['left'];$email_dat_right=$data['right'];

                /* Get data about Competitor prices */

                $mdata['content_left'] = $this->load->view('marketing/signups_data_view',['data' => $email_dat_left], TRUE);
                $mdata['content_right'] = '';
                if (count($email_dat_right)>0) {
                    $mdata['content_right'] = $this->load->view('marketing/signups_data_view',['data' => $email_dat_right], TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function count_signup() {
        if ($this->isAjax()) {
            $options=array();
            $postdata = $this->input->post();
            $mdata=[];
            $error = 'Empty Brand';
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $startdate = ifset($postdata,'startdate');
                if (!empty($startdate)) {
                    $options['startdate'] = strtotime($postdata['startdate']);
                }
                $enddate = ifset($postdata, 'enddate');
                if (!empty($enddate)) {
                    $options['enddate'] = strtotime($postdata['enddate'].' 23:59:59');
                }
                $options['type']='Signups';
                $options['brand'] = $brand;
                $this->load->model('email_model');
                $mdata['totals'] = $this->email_model->count_messages($options);
                $error = '';
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function export_signups() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $error = 'Empty Brand';
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $options=[
                    'brand' => $brand,
                ];
                if (isset($postdata['startdate']) && !empty($postdata['startdate'])) {
                    $options['startdate'] = strtotime($postdata['startdate']);
                }
                if (isset($postdata['enddate']) && !empty($postdata['enddate'])) {
                    $options['enddate'] = strtotime($postdata['enddate'].' 23:59:59');
                }
                if (isset($postdata['type'])) {
                    $options['type'] = $postdata['type'];
                }
                $this->load->model('email_model');
                $res = $this->email_model->export_signupdata($options);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['url'] = $res['url'];
                }
            }
            $this->ajaxResponse($mdata,$error);
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

    private function _prepare_searchbywords($brand, $top_menu) {
        $options = [
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        // searchkeyword_view
        return $this->load->view('marketing/search_keyword_view', $options,TRUE);
    }

    private function _prepare_searckipaddress($brand, $top_menu) {
        $options = [
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        // searchkeyword_view
        return $this->load->view('marketing/search_ipaddress_view', $options,TRUE);
    }

    private function _prepare_signup($brand, $top_menu) {
        $this->load->model('email_model');
        $total_rec=$this->email_model->get_emails_count($brand, 'Signups');
        /* 2 special Counter */
        $mailstat=$this->email_model->get_emails_count_by_type($brand);

        /* Check session */
        $cur_page=0;
        $order_by='email_id';
        $direction='desc';

        /* Prepare contetn for display */
        $content=array();
        /* View Window Legend */

        $content_dat=array(
            'order_by'=>$order_by,
            'direction'=>$direction,
            'total_rec'=>$total_rec,
            'cur_page'=>$cur_page,
            'mailstat'=>$mailstat,
            'perpage'=>500,
            'brand' => $brand,
            'top_menu' => $top_menu,
        );

        return $this->load->view('marketing/signups_head_view',$content_dat,TRUE);
    }
}