<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leads extends MY_Controller
{

    private $pagelink = '/leads';

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
        $head['title'] = 'Leads';
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
        $content_options['menu'] = $menu;
        foreach ($menu as $row) {
            if ($row['item_link'] == '#leadsview') {
                $head['styles'][]=array('style'=>'/css/leads/leadsview.css');
                $head['scripts'][]=array('src'=>'/js/leads/leadsview.js');
                $content_options['leadsview'] = $this->_prepare_leadsview($brand, $top_menu);
            }
        }
        $content_view = $this->load->view('leads/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/leads/page.js');
        $head['styles'][] = array('style' => '/css/leads/leadspage.css');
        // Utils
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

    public function leadpage_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            /* Posts */
            /* 'search':search, 'offset':page_index,'limit':perpage,'maxval':maxval,'usrrepl':usrreplic,'sorttime':sorttime,'leadtype':leadtype */
            $search=$this->input->post('search');
            $offset=$this->input->post('offset');
            $limit=$this->input->post('limit');
            /* $maxval=$this->input->post('maxval'); */
            $usrrepl=$this->input->post('usrrepl');
            $sorttime=$this->input->post('sorttime');
            $leadtype=$this->input->post('leadtype');
            $offset=$offset*$limit;
            /*  */
            $options=array();
            if ($search!='') {
                $options['search']=$search;
            }
            if ($leadtype!='') {
                $options['lead_type']=$leadtype;
            }
            if ($usrrepl!='') {
                $options['usrrepl']=$usrrepl;
            }
            $sort=$sorttime;
            $this->load->model('leads_model');
            $leaddat=$this->leads_model->get_leads($options,$sort,$limit,$offset);

            if (count($leaddat)==0) {
                $mdata['leadcontent']=$this->load->view('leads/leads_emptydata_view',array(),TRUE);
            } else {
                $options=array(
                    'data'=>$leaddat,
                );
                $mdata['leadcontent']=$this->load->view('leads/leads_tabledata_view',$options, TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function leadsclosed_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $out_options=[];
            $this->load->model('orders_model');
            $this->load->model('leads_model');
            $minlead=$this->leads_model->get_lead_mindate();
            $minorder=$this->orders_model->get_order_mindate();
            $user_id=$this->input->post('user_id');
            $show_feature=$this->input->post('showfeature');
            $options=array(
                'startdate'=>($minlead>$minorder ? $minorder : $minlead),
                'show_feature'=>$show_feature,
            );
            if ($user_id) {
                $options['user_id']=$user_id;
                $usrdata=$this->user_model->get_user_data($user_id);
                if ($usrdata['user_leadname']) {
                    $out_options['owner_name']=$usrdata['user_leadname'].'&apos;s';
                } else {
                    $out_options['owner_name']=$usrdata['user_name'];
                }
            } else {
                $out_options['owner_name']='Company ';
            }
            $data=$this->leads_model->get_closedleads_data($options);
            $out_options['weeks']=$data['weeks'];
            $out_options['curweek']=$data['curweek'];
            $out_options['totals']=$data['totals'];
            $mdata['content']=$this->load->view('leads/leads_closedata_view', $out_options, TRUE);
            $this->ajaxResponse($mdata,$error);

        }
        show_404();
    }

    public function leadsclosed_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $options=array(
                'week'=>$postdata['week'],
                'start'=>$postdata['start'],
                'end'=>$postdata['end'],
            );
            if (isset($postdata['user_id']) && $postdata['user_id']) {
                $options['user_id']=$postdata['user_id'];
            }
            $this->load->model('leads_model');
            $data=$this->leads_model->get_leadclosed_details($options);
            if (count($data)==0) {
                $error='Empty Week Details';
            } else {
                $det_options=array(
                    'data'=>$data,
                );
                $mdata['content']=$this->load->view('leads/lead_weekdetails_view', $det_options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function leadsclosed_cmporders() {
        $bgn=$this->input->get('bgn');
        $this->load->model('orders_model');
        $data=$this->orders_model->get_cmporders($bgn);
        $content=$this->load->view('leads/company_ordertotals_view', $data, TRUE);
        echo $content;
    }

    public function leadsclosed_yeartotals() {
        $this->load->model('leads_model');
        $leads=$this->leads_model->get_yearleads();
        $content=$this->load->view('leads/lead_yeartotals_view', $leads, TRUE);
        echo $content;
    }

    public function leadsclosed_usrorders() {
        $bgn=$this->input->get('bgn');
        $user_id=$this->input->get('user');
        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
        $this->load->model('orders_model');
        $options=array(
            'begin'=>$bgn,
            'order_usr_repic'=>$user_id,
            'end'=>$end,
            'order_by'=>'o.order_num',
            'direct'=>'desc',
        );

        $orders=$this->orders_model->get_leadorders($options);
        $label=date('M d',$bgn).'-';
        if (date('m', $bgn)!=date('m',$end)) {
            $label.=date('M d',$end);
        } else {
            $label.=date('d', $end);
        }
        $label.=','.date('Y', $end);

        $data=array(
            'total'=>count($orders),
            'orders'=>$orders,
            'label'=>$label,
        );
        $content=$this->load->view('leads/lead_userordertotals_view', $data, TRUE);
        echo $content;
    }

    private function _prepare_leadsview($brand, $top_menu) {
        $ldat=array();
        $this->load->model('leads_model');
        /* Prepare Right part */
        $rdat=array();
        $date=date('Y-m').'-01';
        $dateend = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
        $total_options=array(
            'enddate'=>$dateend,
            'user_id'=>$this->USR_ID,
        );
        $rdattop=$this->leads_model->count_closed_totals($total_options);
        $rdat['prev']=$rdattop['prev'];
        $rdat['next']=$rdattop['next'];
        $rdat['data']=$rdattop['data'];
        $rdat['dateend']=$dateend;
        $rdat['datestart']=$rdattop['datestart'];
        $ldat['right_content']=$this->load->view('leads/leads_closetotal_view', $rdat, TRUE);

        /* Get user with reps */
        $ldat['replicas']=$this->user_model->get_user_leadreplicas();
        $ldat['user_id']=$this->USR_ID;
        $user_dat=$this->user_model->get_user_data($this->USR_ID);
        $ldat['user_name']=($user_dat['user_leadname']=='' ? $this->USR_NAME : $user_dat['user_leadname']);

        $options=array(
            'lead_type'=>1,
            'usrrepl'=>  $this->USR_ID,
        );

        $ldat['totalrec']=$this->leads_model->get_total_leads($options);
        $ldat['perpage']=$this->config->item('leads_perpage');
        $ldat['curpage']=0;
        $this->load->model('orders_model');
        $ldat['totalorders']=$this->orders_model->orders_total_year(date('Y'));
        $ldat['brand'] = $brand;
        $ldat['top_menu'] = $top_menu;
        $content=$this->load->view('leads/leadtab_view',$ldat,TRUE);
        return $content;
    }
}