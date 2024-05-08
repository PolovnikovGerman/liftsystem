<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Marketing extends MY_Controller
{

    private $pagelink = '/marketing';
    private $keywodslist = 100;
    private $ipaddrlist = 50;
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
        $brand = $this->menuitems_model->get_current_brand();
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);

        $content_options = [];
        $content_options['start'] = $this->input->get('start', TRUE);

        foreach ($menu as $row) {
            if ($row['item_link']=='#searchestimeview') {
                // Search results by time
                $head['styles'][]=array('style'=>'/css/marketing/searchestimeview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searchestimeview.js');
                $content_options['searchestimeview'] = $this->_prepare_searchbytime($brand);
            } elseif ($row['item_link']=='#searcheswordview') {
                // Search Results by Keywords
                $head['styles'][]=array('style'=>'/css/marketing/searcheswordview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searcheswordview.js');
                $content_options['searcheswordview'] = $this->_prepare_searchbywords($brand);
            } elseif ($row['item_link']=='#searchesipadrview') {
                // Search results by IP
                $head['styles'][]=array('style'=>'/css/marketing/searchesipadrview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searchesipadrview.js');
                $content_options['searchesipadrview'] = $this->_prepare_searckipaddress($brand);
            } elseif ($row['item_link']=='#signupview') {
                // Search results by IP
                $head['styles'][]=array('style'=>'/css/marketing/signupview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/signupview.js');
                $content_options['signupview'] = $this->_prepare_signup($brand);
            } elseif ($row['item_link']=='#couponsview') {
                // Search results by IP
                $head['styles'][] = array('style' => '/css/marketing/couponsview.css');
                $head['scripts'][] = array('src' => '/js/marketing/couponsview.js');
                $content_options['couponsview'] = $this->_prepare_couponsview($brand);
            } elseif ($row['item_link']=='#searchesview') {
                // Search results
                $head['styles'][]=array('style'=>'/css/marketing/searchesview.css');
                $head['scripts'][]=array('src'=>'/js/marketing/searchesview.js');
                $content_options['searchesview'] = $this->_prepare_search($brand);
            }
        }

        // Add main page management
        $head['scripts'][] = array('src' => '/js/marketing/page.js');
        $head['styles'][] = array('style' => '/css/marketing/marketpage.css');
        // Utils
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Switcher
        $head['scripts'][]=array('src'=>"https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js");
        $head['styles'][]=array('style'=>"https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css");
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
            'brand' => $brand,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['menu'] = $menu;
        $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $content_view = $this->load->view('marketing/page_view', $content_options, TRUE);
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
                if (in_array($period,['week','month','year','custom'])) {
                    $error = '';
                    if ($period=='week') {
                        $dates = getDatesByWeek(date('W'),date('Y'));
                        $d_bgn = $dates['start_week'];
                        $d_end = $dates['end_week'];
                    } elseif ($period=='month') {
                        $curtime = date('Y-m-') . '01 00:00:00';
                        $d_bgn = strtotime($curtime);
                        $curtime = date('Y-m-t') . ' 23:59:59';
                        $d_end = strtotime($curtime);
                    } elseif ($period=='year') {
                        $curtime = date('Y-') . '01-01 00:00:00';
                        $d_bgn = strtotime($curtime);
                        $curtime = date('Y-m-t') . ' 23:59:59';
                        $d_end = strtotime($curtime);
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
            $period = ifset($postdata, 'period','today');
            $show_result = ifset($postdata, 'result','0');
            $brand = ifset($postdata,'brand','ALL');
            $error = 'Empty Parameters';
            if (!empty($period) && !empty($brand)) { // !empty($show_result) &&
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
                    $numres = count($data);
                    if ($numres <= 60) {
                        $num_cols=ceil(count($data)/20);
                        $numrows = 20;
                    } else {
                        $num_cols = 3;
                        $numrows = ceil($numres/3);
                    }
                    $options = [
                        'dat'=> $data,
                        'numrows' => $numrows,
                        'numcols' => $num_cols,
                        'numrecs' => $numres,
                    ];
                    $mdata['content']=$this->load->view('marketing/searchkeyword_dat_view', $options,TRUE);
                    $mdata['num_cols'] = $num_cols;
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
                    $numres = count($data);
                    if ($numres <= 60) {
                        $num_cols=ceil(count($data)/20);
                        $numrows = 20;
                    } else {
                        $num_cols = 3;
                        $numrows = ceil($numres/3);
                    }
                    $options = [
                        'dat'=> $data,
                        'numrows' => $numrows,
                        'numcols' => $num_cols,
                        'numrecs' => $numres,
                    ];
                    $mdata['content']=$this->load->view('marketing/searchipaddress_dat_view', $options,TRUE);
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

    public function couponsdat() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $page_num = ifset($postdata,'offset',0);
                $limit = ifset($postdata, 'limit', 100);
                $this->load->model('coupons_model');
                $options = [
                    'coupon_deleted' => 0,
                    'brand' => $brand,
                    'order_by' => ifset($postdata,'order_by','coupon_id'),
                    'direction' => ifset($postdata,'direction','desc'),
                    'offset' => $page_num * $limit,
                    'limit' => $limit,
                ];

                $data=$this->coupons_model->get_coupons($options);
                $mdata['content'] = $this->load->view('marketing/coupons_data_view', ['data'=>$data], TRUE);

            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function coupon_details() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $coupon_id = ifset($postdata,'coupon_id',0);
            $brands = $this->menuitems_model->get_brand_permisions($this->USR_ID, $this->pagelink);
            $this->load->model('coupons_model');
            if ($coupon_id==0) {
                $res = $this->coupons_model->new_coupon($brands);
            } else {
                $res = $this->coupons_model->get_coupon_details($coupon_id, $brands);
            }
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['content']=$this->load->view('marketing/coupon_detail_view',['data'=>$res['data'],'brands'=>$res['brands']], TRUE);
                if ($coupon_id==0) {
                    $mdata['label']='New Coupon';
                } else {
                    $mdata['label']='Edit Coupon '.$res['data']['coupon_code1'].'-'.$res['data']['coupon_code2'];
                }
                $mdata['percent_lock'] = $res['percent_lock'];
                $mdata['money_lock'] = $res['money_lock'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function coupon_activate() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $coupon_id = ifset($postdata,'coupon_id',0);
            $this->load->model('coupons_model');
            $res = $this->coupons_model->update_coupon_status($coupon_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                if ($res['active']==1) {
                    $mdata['content'] = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                } else {
                    $mdata['content'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function coupon_delete() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $coupon_id = ifset($postdata, 'coupon_id', 0);
            $this->load->model('coupons_model');
            $res = $this->coupons_model->del_coupon($coupon_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['total'] = $res['total'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function coupon_details_save() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $this->load->model('coupons_model');
            $res = $this->coupons_model->update_coupon($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['total']=$res['total'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Searches fuctions
    public function searches_count()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand','ALL');
            $display_option = ifset($postdata,'display_option',0);
            $display_period = ifset($postdata, 'display_period','today');
            if ($display_period=='today') {
                $d_bgn = strtotime(date('Y-m-d').' 00:00:00');
                $d_end = strtotime(date('Y-m-d').' 23:59:59');
            } elseif ($display_period=='week') {
                $dates = getDatesByWeek(date('W'), date('Y'));
                $d_bgn = $dates['start_week'];
                $d_end = $dates['end_week'];
            } elseif ($display_period=='month') {
                $month = $postdata['month'];
                $d_bgn = strtotime($month.'-01');
                $d_end = strtotime("+1 month", $d_bgn)-1;
            } elseif ($display_period=='year') {
                $year = $postdata['year'];
                $d_bgn = strtotime($year.'-01-01');
                $d_end = strtotime("+1 year", $d_bgn)-1;
            } elseif ($display_period=='custom') {
                $datbgn = $postdata['d_bgn'];
                $datend = $postdata['d_end'];
                $d_bgn = $d_end = '';
                if (!empty($datbgn)) {
                    $d_bgn = strtotime($datbgn);
                }
                if (!empty($datend)) {
                    $d_end = strtotime($datend);
                }
            }

            $this->load->model('searchresults_model');
            $res = $this->searchresults_model->get_count_searches($display_option, $d_bgn, $d_end, $brand);
            $mdata['keyword'] = $res['keyword'];
            $mdata['ipaddr'] = $res['ipaddr'];
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function searches_keywords()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand','ALL');
            $display_option = ifset($postdata,'display_option',0);
            $display_period = ifset($postdata, 'display_period','today');
            if ($display_period=='today') {
                $d_bgn = strtotime(date('Y-m-d').' 00:00:00');
                $d_end = strtotime(date('Y-m-d').' 23:59:59');
            } elseif ($display_period=='week') {
                $dates = getDatesByWeek(date('W'), date('Y'));
                $d_bgn = $dates['start_week'];
                $d_end = $dates['end_week'];
            } elseif ($display_period=='month') {
                $month = $postdata['month'];
                $d_bgn = strtotime($month.'-01');
                $d_end = strtotime("+1 month", $d_bgn)-1;
            } elseif ($display_period=='year') {
                $year = $postdata['year'];
                $d_bgn = strtotime($year.'-01-01');
                $d_end = strtotime("+1 year", $d_bgn)-1;
            } elseif ($display_period=='custom') {
                $datbgn = $postdata['d_bgn'];
                $datend = $postdata['d_end'];
                $d_bgn = $d_end = '';
                if (!empty($datbgn)) {
                    $d_bgn = strtotime($datbgn);
                }
                if (!empty($datend)) {
                    $d_end = strtotime($datend);
                }
            }
            $page = ifset($postdata,'page',0);
            $total = ifset($postdata,'total', 0);
            $offset = intval($page * $this->keywodslist);
            $limit = $this->keywodslist;
            $this->load->model('searchresults_model');
            $res = $this->searchresults_model->get_keywords_data($display_option, $d_bgn, $d_end, $brand, $limit, $offset);
            $options = [
                'total' => count($res),
                'items' => $res,
                'numcols' => ceil(count($res)/4),
                'limit' => 25,
            ];
            $mdata['content']=$this->load->view('marketing/keywords_content_view', $options, TRUE);
            $mdata['prev'] = 0;
            if ($page > 0) {
                $mdata['prev'] = 1;
            }
            $mdata['next'] = ($total <= $limit ? 0 : 1);
            if (($offset+$limit) >= $total) {
                $mdata['next'] = 0;
            }
            $label = '';
            if (count($res)==0) {
                $label = '0 from '.$total;
            } else {
                $start = ($offset+1);
                $finish = $offset+count($res);
                $label = $start.' - '.$finish.' from '.$total;
            }
            $mdata['label'] = $label;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_searchbytime($brand) {
        $options = [
            'brand' => $brand,
        ];
        return $this->load->view('marketing/search_time_view', $options,TRUE);
    }

    private function _prepare_searchbywords($brand) {
        $options = [
            'brand' => $brand,
        ];
        // searchkeyword_view
        return $this->load->view('marketing/search_keyword_view', $options,TRUE);
    }

    private function _prepare_searckipaddress($brand) {
        $options = [
            'brand' => $brand,
        ];
        // searchkeyword_view
        return $this->load->view('marketing/search_ipaddress_view', $options,TRUE);
    }

    private function _prepare_signup($brand) {
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
        );

        return $this->load->view('marketing/signups_head_view',$content_dat,TRUE);
    }

    private function _prepare_couponsview($brand) {
        $options=[
            'coupon_deleted'=>0,
            'brand' => $brand,
        ];
        $this->load->model('coupons_model');
        $total_rec=$this->coupons_model->get_coupons_number($options);
        /* Get Data about coupons */

        $cur_page=0;
        $order_by='coupon_id';
        $direction='desc';
        /* View Window Legend */
        $view_options=[
            'order_by'=>$order_by,
            'direction'=>$direction,
            'total_rec'=>$total_rec,
            'cur_page'=>$cur_page,
            'perpage' => 100,
            'brand' => $brand,
        ];
        return $this->load->view('marketing/coupons_view',$view_options,TRUE);
    }

    private function _prepare_search($brand)
    {
        $this->load->model('searchresults_model');
        $dates = $this->searchresults_model->get_searchdates($brand);
        $options = [
            'brand' => $brand,
            'minyear' => $dates['minyear'],
            'maxyear' => $dates['maxyear'],
            'months' => $dates['months'],
        ];
        return $this->load->view('marketing/searches_view', $options,TRUE);
    }
}