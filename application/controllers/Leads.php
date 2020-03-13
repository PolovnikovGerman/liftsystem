<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leads extends My_Controller {

    protected $PERPAGE=1000;
    protected $PERPAGE_LEADS=250;
    protected $ITEMLNK='leadsbtn';
    protected $NONPARSED=0;
    protected $PARSED_CLASS='empty';
    protected $PERPAGE_ORDERS=array(
        '100','150','200','250'
    );

    protected $vendor_prices = array(
        array('base'=>25,'label'=>'25'),
        array('base'=>75,'label'=>'75'),
        array('base'=>150,'label'=>'150'),
        array('base'=>250,'label'=>'250'),
        array('base'=>500,'label'=>'500'),
        array('base'=>1000,'label'=>'1000'),
        array('base'=>3000,'label'=>'3000'),
        array('base'=>5000,'label'=>'5000'),
        array('base'=>10000,'label'=>'10K'),
        array('base'=>20000,'label'=>'20K'),
    );

    /* Timeout to show DEAD option - 14 DAYS */
    protected $timeout_dead=1209600;
    /* Statuses - DEAD & CLOSED */
    protected $LEAD_DEAD=3;
    protected $LEAD_CLOSED=4;
    private $restore_orderdata_error='Connection Lost. Please, recall form';

    private $pagelink = '/leads';

    function __construct() {
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

    function index() {
        $head = [];
        $head['title'] = 'Leads';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];

        $content_options['menu'] = $menu;
        $content_view = $this->load->view('leads/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/leads/page.js');
        $head['styles'][] = array('style' => '/css/leads/leadspage.css');
        // Utils
        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

//    /* Get data about LEAD & Account Reminder */
//    function leadpage_data() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            /* Posts */
//            /* 'search':search, 'offset':page_index,'limit':perpage,'maxval':maxval,'usrrepl':usrreplic,'sorttime':sorttime,'leadtype':leadtype */
//            $search=$this->input->post('search');
//            $offset=$this->input->post('offset');
//            $limit=$this->input->post('limit');
//            /* $maxval=$this->input->post('maxval'); */
//            $usrrepl=$this->input->post('usrrepl');
//            $sorttime=$this->input->post('sorttime');
//            $leadtype=$this->input->post('leadtype');
//            $offset=$offset*$limit;
//            /*  */
//            $options=array();
//            if ($search!='') {
//                $options['search']=$search;
//            }
//            if ($leadtype!='') {
//                $options['lead_type']=$leadtype;
//            }
//            if ($usrrepl!='') {
//                $options['usrrepl']=$usrrepl;
//            }
//            $sort=$sorttime;
//
//            $leaddat=$this->mleads->get_leads($options,$sort,$limit,$offset);
//
//            if (count($leaddat)==0) {
//                $mdata['leadcontent']=$this->load->view('leadpage/empty_data_view',array(),TRUE);
//            } else {
//                $options=array(
//                    'data'=>$leaddat,
//                );
//                $mdata['leadcontent']=$this->load->view('leadpage/table_data_view',$options, TRUE);
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    // Show closed data
//    public function leadsclosed_totals() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $date=$this->input->post('date');
//            $direction=$this->input->post('direction');
//            $user_id=$this->input->post('user_id');
//            // New start
//            if ($direction=='prev') {
//                $newdate=strtotime(date("Y-m-d", $date) . " +5 month");
//            } else {
//                // $newdate=strtotime(date("Y-m-d", $date) . " -1 month");
//                $newdate=$date;
//            }
//            $total_options=array(
//                'enddate'=>$newdate,
//            );
//            if ($user_id) {
//                $total_options['user_id']=$user_id;
//            }
//            $rdattop=$this->mleads->count_closed_totals($total_options);
//            $rdat['prev']=$rdattop['prev'];
//            $rdat['next']=$rdattop['next'];
//            $rdat['data']=$rdattop['data'];
//            $rdat['dateend']=$newdate;
//            $rdat['datestart']=$rdattop['datestart'];
//            $mdata['content']=$this->load->view('leadpage/close_total_view', $rdat, TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    // Data per weeks
//    public function leadsclosed_data() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $out_options=array(
//
//            );
//            $this->load->model('order_model');
//            $minlead=$this->mleads->get_lead_mindate();
//            $minorder=$this->order_model->get_order_mindate();
//            $user_id=$this->input->post('user_id');
//            $show_feature=$this->input->post('showfeature');
//            $options=array(
//                'startdate'=>($minlead>$minorder ? $minorder : $minlead),
//                'show_feature'=>$show_feature,
//            );
//            if ($user_id) {
//                $options['user_id']=$user_id;
//                $usrdata=$this->muser->get_user_data($user_id);
//                if ($usrdata['user_leadname']) {
//                    $out_options['owner_name']=$usrdata['user_leadname'].'&apos;s';
//                } else {
//                    $out_options['owner_name']=$usrdata['user_name'];
//                }
//            } else {
//                $out_options['owner_name']='Company ';
//            }
//            $data=$this->mleads->get_closedleads_data($options);
//            $out_options['weeks']=$data['weeks'];
//            $out_options['curweek']=$data['curweek'];
//            $out_options['totals']=$data['totals'];
//            $mdata['content']=$this->load->view('leadpage/close_data_view', $out_options, TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//
//        }
//        show_404();
//    }
//
//    // Rebuild totals and current week results
//    public function check_neworders() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $postdata=$this->input->post();
//            $chktotals=(isset($postdata['totals']) ? $postdata['totals'] : 0);
//            // Get Totals
//            $this->load->model('order_model');
//            $totalorders=$this->order_model->orders_total_year(date('Y'));
//            if ($totalorders!=$chktotals) {
//                // Aha, new order data
//                $minlead=$this->mleads->get_lead_mindate();
//                $minorder=$this->order_model->get_order_mindate();
//                $options=array(
//                    'startdate'=>($minlead>$minorder ? $minorder : $minlead),
//                    'show_feature'=>0,
//                );
//                // Get weeks array
//                if (isset($postdata['user_id']) && !empty($postdata['user_id'])) {
//                    $options['user_id']=$postdata['user_id'];
//                }
//
//                $data=$this->mleads->get_closedleads_data($options);
//
//                $totals=$data['totals'];
//                $mdata['totalcontent']=$this->load->view('leadpage/newtotal_view', $totals, TRUE);
//                $weeks=$data['weeks'];
//                $found=0;
//                foreach ($weeks as $row) {
//                    if ($row['curweek']==1) {
//                        $found=1;
//                        $weekdata=$row;
//                        $mdata['curweek']=$row['week'];
//                        break;
//                    }
//                }
//                $mdata['weektotal']='';
//                if ($found==1) {
//                    $mdata['weektotal']=$this->load->view('leadpage/weektotals_view', $weekdata, TRUE);
//                }
//                $curweek=$data['curweek'];
//                $mdata['weekdetails']=$this->load->view('leadpage/weekdetails_view', array('data'=>$curweek), TRUE);
//                $mdata['update']=1;
//                $mdata['newtotal']=$totalorders;
//            } else {
//                $mdata['update']=0;
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    // Week details
//    public function leadsclosed_details() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $postdata=$this->input->post();
//            $options=array(
//                'week'=>$postdata['week'],
//                'start'=>$postdata['start'],
//                'end'=>$postdata['end'],
//            );
//            if (isset($postdata['user_id']) && $postdata['user_id']) {
//                $options['user_id']=$postdata['user_id'];
//            }
//            $data=$this->mleads->get_leadclosed_details($options);
//            if (count($data)==0) {
//                $error='Empty Week Details';
//            } else {
//                $det_options=array(
//                    'data'=>$data,
//                );
//                $mdata['content']=$this->load->view('leadpage/weekdetails_view', $det_options, TRUE);
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    // Orders from Company Points cell
//    public function leadsclosed_cmporders() {
//        $bgn=$this->input->get('bgn');
//        $this->load->model('order_model');
//        $data=$this->order_model->get_cmporders($bgn);
//        $content=$this->load->view('leadpage/company_ordertotals_view', $data, TRUE);
//        echo $content;
//    }
//
//    // Orders, related with Orders # from All users Lead report
//    public function leadsclosed_companyorders() {
//        $bgn=$this->input->get('bgn');
//        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
//        $this->load->model('order_model');
//        $options=array(
//            'begin'=>$bgn,
//            'end'=>$end,
//        );
//
//        $orders=$this->order_model->get_companyleadorders($options);
//
//        $label=date('M d',$bgn).'-';
//        if (date('m', $bgn)!=date('m',$end)) {
//            $label.=date('M d',$end);
//        } else {
//            $label.=date('d', $end);
//        }
//        $label.=','.date('Y', $end);
//
//        $data=array(
//            'totals'=>$orders['totals'],
//            'orders'=>$orders['userdata'],
//            'label'=>$label,
//        );
//        $content=$this->load->view('leadpage/company_orderstotals_view', $data, TRUE);
//        echo $content;
//
//    }
//
//
//    // Orders from User Orders cell (User Replica)
//    public function leadsclosed_usrorders() {
//        $bgn=$this->input->get('bgn');
//        $user_id=$this->input->get('user');
//        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
//        $this->load->model('order_model');
//        $options=array(
//            'begin'=>$bgn,
//            'order_usr_repic'=>$user_id,
//            'end'=>$end,
//            'order_by'=>'o.order_num',
//            'direct'=>'desc',
//        );
//
//        $orders=$this->order_model->get_leadorders($options);
//        $label=date('M d',$bgn).'-';
//        if (date('m', $bgn)!=date('m',$end)) {
//            $label.=date('M d',$end);
//        } else {
//            $label.=date('d', $end);
//        }
//        $label.=','.date('Y', $end);
//
//        $data=array(
//            'total'=>count($orders),
//            'orders'=>$orders,
//            'label'=>$label,
//        );
//        $content=$this->load->view('leadpage/user_ordertotals_view', $data, TRUE);
//        echo $content;
//    }
//
//    // Leads for company
//    public function leadsclosed_companyleads() {
//        $bgn=$this->input->get('bgn');
//        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
//        $options=array(
//            'begin'=>$bgn,
//            'end'=>$end,
//        );
//        $leads=$this->mleads->get_company_leads($options);
//        // Label
//        $label=date('M d',$bgn).'-';
//        if (date('m', $bgn)!=date('m',$end)) {
//            $label.=date('M d',$end);
//        } else {
//            $label.=date('d', $end);
//        }
//        $label.=','.date('Y', $end);
//        $data=array(
//            'label'=>$label,
//            'totals'=>$leads['totals'],
//            'leads'=>$leads['usrdata'],
//        );
//        $content=$this->load->view('leadpage/company_leadstotal_view', $data, TRUE);
//        echo $content;
//
//    }
//
//    // New Leads, related with user
//    public function leadsclosed_usrleads() {
//        $bgn=$this->input->get('bgn');
//        $user_id=$this->input->get('user');
//        $leadtype=$this->input->get('leadtype');
//        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
//        $options=array(
//            'begin'=>$bgn,
//            'end'=>$end,
//            'user_id'=>$user_id,
//            'leadtype'=>$leadtype,
//        );
//        $leads=$this->mleads->get_newleads($options);
//        $label=date('M d',$bgn).'-';
//        if (date('m', $bgn)!=date('m',$end)) {
//            $label.=date('M d',$end);
//        } else {
//            $label.=date('d', $end);
//        }
//        $label.=','.date('Y', $end);
//
//        $data=array(
//            'total'=>count($leads),
//            'leads'=>$leads,
//            'label'=>$label,
//        );
//        $content=$this->load->view('leadpage/user_leadstotal_view', $data, TRUE);
//        echo $content;
//    }
//    // Lead Closed Year total
//    public function leadsclosed_yeartotals() {
//        $leads=$this->mleads->get_yearleads();
//        $content=$this->load->view('leadpage/year_totals_view', $leads, TRUE);
//        echo $content;
//    }
//
//    /* Calculate # of Leads records according to filters */
//    function search_leads() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $errors='';
//            /* Post parameters */
//            $search=$this->input->post('search');
//            $usrrepl=$this->input->post('usrrepl');
//            $leadtype=$this->input->post('leadtype');
//            $options=array();
//            if ($search!='') {
//                $options['search']=$search;
//            }
//            if ($leadtype!='') {
//                $options['lead_type']=$leadtype;
//            }
//            if ($usrrepl!='') {
//                $options['usrrepl']=$usrrepl;
//            }
//            $mdata['totalrec']=$this->mleads->get_total_leads($options);
//            $this->func->ajaxResponse($mdata,$errors);
//        }
//    }
//    /* Change footer for Leads TAB */
//    function lead_totalclosed() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $year=$this->input->post('year');
//            $data=$this->mleads->get_closed_byyear($year);
//            $mdata['content']=$this->load->view('leads/leadtab_footertotals_view',array('total'=>$data),TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
    /* Prepare content for edit */
    function edit_lead() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $lead_id=$this->input->post('lead_id');
            $dead_av=1;
            $this->load->model('leads_model');
            $this->load->model('questions_model');
            $this->load->model('quotes_model');
            $this->load->model('artproof_model');
            // $this->load->model('documents_model','mdocs');
            // $this->load->model('contact_model');

            if ($lead_id==0) {
                // $replicas=$this->muser->get_user_leadreplicas(1);
                $lead_data=$this->leads_model->get_empty_lead();
                $dead_av=0;
                $lead_data['lead_id']=0;
                $lead_data['lead_type']=2;
                $lead_data['lead_number']=$this->leads_model->get_leadnum();
                $lead_history=array();
                $lead_usr=array();
            } else {
                // $replicas=$this->muser->get_user_leadreplicas(0);
                $lead_data=$this->leads_model->get_lead($lead_id);
                if (isset($lead_data['create_date'])) {
                    $crtime=strtotime($lead_data['create_date']);
                    // Temporary COMMENTED
                    /*if (time()-$crtime<$this->timeout_dead) {
                        $dead_av=0;
                    }*/
                }
                $lead_history=$this->leads_model->get_lead_history($lead_id);
                $lead_usr=$this->leads_model->get_lead_users($lead_id);
            }
            $lead_tasks=$this->leads_model->get_lead_tasks($lead_id);

            $save_av=1;

            /* */
            if (count($lead_usr)==0) {
                array_push($lead_usr, $this->USR_ID);
            }
            $lead_replic=array();
            foreach ($lead_usr as $row) {
                $usr=$this->user_model->get_user_data($row);
                $lead=array(
                    'user_id'=>$row,
                    'user_leadname'=>$usr['user_leadname'],
                    'value'=>1,
                );
                $lead_replic[]=$lead;
            }
            // $leadrepl=1;
            if ($lead_data['lead_type']==$this->LEAD_CLOSED || $lead_data['lead_type']==$this->LEAD_DEAD) {
                // $leadrepl=0;
                $replic=$this->load->view('leads/lead_replicalock_view',array('repl'=>$lead_replic),TRUE);
            } else {
                if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin' || $this->USR_ID==$lead_data['create_user']) {
                    $replic=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic),TRUE);
                } else {
                    $replic=$this->load->view('leads/lead_replicareadonly_view',array('repl'=>$lead_replic),TRUE);
                }
            }
            // Save User Lead into session
            $session_id='leadusers'.uniq_link(10);
            usersession($session_id, $lead_replic);
            $lead_data['other_item_label']='';
            if ($lead_data['lead_item']=='Other') {
                $lead_data['other_item_label']='Type Other Item Here:';
            } elseif ($lead_data['lead_item']=='Multiple') {
                $lead_data['other_item_label']='Type Multiple Items Here:';
            } elseif ($lead_data['lead_item']=='Custom Shaped Stress Balls') {
                $lead_data['other_item_label']='Type Custom Items Here:';
            }

            $history=$this->load->view('leads/lead_history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
            $lead_tasks['edit']=$save_av;

            // $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
            $tasks='';
            $qdat=$this->questions_model->get_lead_questions($lead_id);
            if (count($qdat)==0) {
                $questions='';
            } else {
                $questions=$this->load->view('leads/lead_questions_view',array('quests'=>$qdat),TRUE);
            }

            $qdat=$this->quotes_model->get_lead_quotes($lead_id);
            if (count($qdat)==0) {
                $quotes='';
            } else {
                $quotes=$this->load->view('leads/lead_quotes_view',array('quotes'=>$qdat),TRUE);
            }

            $qdat=$this->artproof_model->get_lead_proofs($lead_id);
            if (count($qdat)==0) {
                $onlineproofs='';
            } else {
                $onlineproofs=$this->load->view('leads/lead_proofs_view',array('proofs'=>$qdat),TRUE);
            }
            $dead_option='';
            if ($dead_av==1) {
                $dead_selected=($lead_data['lead_type'] == 3 ? 'selected="selected"' : '');
                $dead_option="<option value=\"3\" ".$dead_selected.">Dead</option>";
            } else {
                $dead_option='';
            }
            /* Get Available Items */
            $items_list=$this->leads_model->items_list();
            // $itemslist=$this->m
            $options=array(
                'data'=>$lead_data,
                'history'=>$history,
                'replica'=>$replic,
                'tasks'=>$tasks,
                'quotes'=>$quotes,
                'questions'=>$questions,
                'onlineproofs'=>$onlineproofs,
                'save_available'=>$save_av,
                'dead_option'=>$dead_option,
                'items' => $items_list,
                'session_id'=>$session_id,
            );
            $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
            $this->func->ajaxResponse($mdata,$error);
        }
    }

//    public function lead_remove_rep() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $postdata=$this->input->post();
//            $session_id=$postdata['session_id'];
//            // Restore data from session
//            $lead_usr=$this->func->session($session_id);
//            $error='Connect lost. Reload Form';
//            if (!empty($lead_usr)) {
//                $error='Lead Must have 1 Rep';
//                if (count($lead_usr)>1) {
//                    $error='';
//                    $usrid=$postdata['user'];
//                    $new_lead=array();
//                    foreach ($lead_usr as $row) {
//                        if ($row['user_id']!=$usrid) {
//                            $new_lead[]=$row;
//                        }
//                    }
//                    // Save to sassion
//                    $lead_replic=$new_lead;
//                    $this->func->session($session_id, $lead_replic);
//                    // Build New content
//                    $mdata['content']=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic),TRUE);
//                }
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    public function lead_addrep_view() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='Connect lost. Reload Form';
//            $postdata=$this->input->post();
//            $session_id=$postdata['session_id'];
//            $lead_usr=$this->func->session($session_id);
//            if (!empty($lead_usr)) {
//                $error='';
//                $used=array();
//                foreach ($lead_usr as $row) {
//                    array_push($used, $row['user_id']);
//                }
//                $active=1;
//                $usrrepl=$this->muser->get_user_leadreplicas($active);
//                $newrepl=array();
//                foreach ($usrrepl as $row) {
//                    if (!in_array($row['user_id'], $used)) {
//                        $newrepl[]=array(
//                            'user_id'=>$row['user_id'],
//                            'user_leadname'=>$row['user_leadname'],
//                            'value'=>1,
//                        );
//                    }
//                }
//                if (count($newrepl)==0) {
//                    $error='No Active users to add as Lead Rep';
//                } else {
//                    $mdata['content']=$this->load->view('leads/lead_replicadd_view',array('repl'=>$newrepl),TRUE);
//                }
//                $this->func->ajaxResponse($mdata, $error);
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    public function lead_addrep_save() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='Connect lost. Reload Form';
//            $postdata=$this->input->post();
//            $session_id=$postdata['session_id'];
//            unset($postdata['session_id']);
//            $lead_usr=$this->func->session($session_id);
//            if (!empty($lead_usr)) {
//                $error='';
//                foreach ($postdata as $key=>$val) {
//                    $usr=$this->muser->get_user_data($val);
//                    $lead=array(
//                        'user_id'=>$val,
//                        'user_leadname'=>$usr['user_leadname'],
//                        'value'=>1,
//                    );
//                    $lead_usr[]=$lead;
//                }
//                $this->func->session($session_id, $lead_usr);
//                // Build New content
//                $mdata['content']=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_usr),TRUE);
//
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    /* Create PR requst on base of Lead */
//    public function lead_addproofrequst() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $leadpost=$this->input->post();
//
//            /* Get Tasks & user array */
//            $lead_tasks=array();
//            $session_id=$leadpost['session_id'];
//            $lead_replic=$this->func->session($session_id);
//            $lead_usr=array();
//            foreach ($lead_replic as $row) {
//                array_push($lead_usr, $row['user_id']);
//            }
//            $usr_id=$this->USR_ID;
//            $res=$this->mleads->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $lead_id=$res['result'];
//                $mdata['lead_id']=$res['result'];
//                // Get new value of Lead
//                $lead_data=$this->mleads->get_lead($lead_id);
//                $this->session->set_userdata('leaddata',$lead_data);
//                $resrequest=$this->mleads->add_proof_request($lead_data, $usr_id, $this->USR_NAME);
//                if ($resrequest['result']==Leads::ERR_FLAG) {
//                    $error=$resrequest['msg'];
//                } else {
//                    $mdata['email_id']=$resrequest['email_id'];
//                }
//            }
//
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    public function lead_deleteproof() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $email_id=$this->input->post('email_id');
//            $res=$this->mleads->remove_proof_request($email_id, $this->USR_NAME);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Save data about Lead Form - to call edit function */
//    public function lead_proofrequest() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $leadpost=$this->input->post();
//            $this->session->set_userdata('leaddata',$leadpost);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//    /* Get Approved Proofs, related with Email */
//    public function lead_approvedshow() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $email_id=$this->input->post('email_id');
//            $approved=$this->mproofs->get_approved($email_id);
//            if (count($approved)==0) {
//                $error='Empty List of Approved Proofs';
//            } else {
//                $mdata['approved']=$approved;
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//    /* Save LEAD */
//    function save_lead() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $leadpost=$this->input->post();
//
//            /* Get Tasks & user array */
//            $lead_tasks=array();
//            $session_id=$leadpost['session_id'];
//            $lead_replic=$this->func->session($session_id);
//
//            $lead_usr=array();
//            foreach ($lead_replic as $row) {
//                array_push($lead_usr, $row['user_id']);
//            }
//            $usr_id=$this->USR_ID;
//            $res=$this->mleads->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            }
//            /* Get # of new messages */
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Data about special account reminder */
//    function specialacc() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $special=array();
//            $special['data']=$this->mleads->get_specialacc();
//            $special['cnt']=count($special['data']);
//            $mdata['speccontent']=$this->load->view('leads/lead_specdata_view',$special,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Manage Special account reminder */
//    function specialacc_edit() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $acc_id=$this->input->post('acc_id');
//            if ($acc_id==0) {
//                /* New Acc */
//                $acc=array(
//                    'special_id'=>0,
//                    'company_name'=>'',
//                );
//            } else {
//                /* Edit */
//                $acc=$this->mleads->get_special_acc($acc_id);
//            }
//            if (!isset($acc['special_id'])) {
//                $error='Unknown account';
//            } else {
//                $mdata['content']=$this->load->view('leads/specialacc_edit_view',$acc,TRUE);
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Save account to Special ACC Reminder */
//    function save_specialacc() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $options=$this->input->post();
//            $res=$this->mleads->save_specialacc($options);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Delete special account reminder */
//    function special_delete() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $special_id=$this->input->post('acc_id');
//            $res=$this->mleads->delete_specialacc($special_id);
//            if ($res==Leads::ERR_FLAG) {
//                $error='Delete of account finished unsuccessfully';
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* NON QB Fucnctions */
//    /* Not QB table data */
//    function notqbdat() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $offset=$this->input->post('offset',0);
//            $limit=$this->input->post('limit',10);
//            $order_by=$this->input->post('order_by');
//            $direct = $this->input->post('direction','asc');
//            $searchval=$this->input->post('search','');
//            $add_filtr=$this->input->post('add_filtr');
//
//            $search=array();
//            if ($searchval) {
//                $search['search']=$searchval;
//            }
//            if ($add_filtr!='') {
//                $search['add_filtr']=$add_filtr;
//            }
//            $offset=$offset*$limit;
//
//            /* Fetch data about prices */
//            $ordersdat=$this->mordernqb->get_orders($search,$order_by,$direct,$limit,$offset);
//
//            if (count($ordersdat)==0) {
//                $mdata['content']=$this->load->view('fingeneral/empty_generalnqbdat_view',array(),TRUE);
//            } else {
//                $data=array(
//                    'orders'=>$ordersdat,
//                );
//                $mdata['content']=$this->load->view('fingeneral/generalnqb_orderdat_view',$data, TRUE);
//
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Get Data about Not in QB Order for edit */
//    function ordernqbdata() {
//        if ($this->func->isAjax()) {
//            $order_id=$this->input->post('order_id',0);
//            $brand_list=$this->morder->get_brands();
//            if ($order_id==0) {
//                /* New Order */
//                $default_brand=$this->config->item('default_brand');
//                $order_data=array(
//                    'order_id'=>0,
//                    'order_date'=>time(),
//                    'brand_id'=>$default_brand,
//                    'order_num'=>'',
//                    'order_items'=>'',
//                    'customer_name'=>'',
//                    'order_rush'=>0,
//                    'order_art'=>0,
//                    'order_redrawn'=>0,
//                    'order_proofed'=>0,
//                    'order_approved'=>0,
//                    'order_vectorized'=>0,
//                    'order_code'=>'',
//                );
//            } else {
//                /* Get Order */
//                $order_data=$this->mordernqb->get_order_detail($order_id);
//            }
//            if (isset($order_data['order_id'])) {
//                if ($order_id==0) {
//                    $order_num=$this->mordernqb->get_max_ordernum();
//                    $order_data['order_num']=$order_num+1;
//                }
//                $order_data['brands']=$brand_list;
//                $order_data['order_date']=date('m/d/Y',$order_data['order_date']);
//                $content=$this->load->view('fingeneral/generalnqb_formedit_view',$order_data,TRUE);
//                $mdata=array('content'=>'');
//                $mdata['content']=$content;
//                $error='';
//            } else {
//                $mdata=array('content'=>'');
//                $error='Unknown order';
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//    /* Save changes in NOT in QB sections */
//    function save_notqb() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $mdata['neworder']='';
//            /* POSTS */
//            $order_id=$this->input->post('order_id');
//            $order_date=$this->input->post('order_date');
//            $order_date=strtotime($order_date);
//
//            $brand_id=$this->config->item('default_brand');
//            $customer_name=$this->input->post('customer_name');
//            $order_items=$this->input->post('order_items');
//            $order_rush=$this->input->post('order_rush',0);
//            $order_art=$this->input->post('order_art',0);
//            $order_redrawn=$this->input->post('order_redrawn',0);
//            $order_vectorized=$this->input->post('order_vectorized',0);
//            $order_proofed=$this->input->post('order_proofed',0);
//            $order_approved=$this->input->post('order_approved',0);
//            $order_code=$this->input->post('order_code');
//            $order_data=array(
//                'order_id'=>$order_id,
//                'order_date'=>$order_date,
//                'brand_id'=>$brand_id,
//                'customer_name'=>$customer_name,
//                'order_items'=>$order_items,
//                'order_rush'=>$order_rush,
//                'order_art'=>$order_art,
//                'order_redrawn'=>$order_redrawn,
//                'order_vectorized'=>$order_vectorized,
//                'order_proofed'=>$order_proofed,
//                'order_approved'=>$order_approved,
//                'order_code'=>$order_code,
//            );
//            $user_id=$this->USR_ID;
//            $res=$this->mordernqb->save_order($order_data,$user_id);
//            if ($res['result']==0) {
//                /* Error */
//                $error=$res['msg'];
//            } else {
//                if ($order_id==0) {
//                    $mdata['neworder']=$res['neworder'];
//                    $mdata['total']=$this->mordernqb->get_count_orders();
//                }
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//    /* Search NOT in QB */
//    function search_notqb() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $search=$this->input->post('search');
//            $filter=$this->input->post('filter');
//            $add_filtr=$this->input->post('add_filtr');
//
//            $options=array();
//
//            $options['search']=$search;
//            $options['filter']=$filter;
//            $options['add_filtr']=$add_filtr;
//            /* count number of orders */
//            $mdata['totals']=$this->mordernqb->get_count_orders($options);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Not in QB change options (RUSH, ART, PROFF, etc) */
//    function notqb_changeoptions() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $option_type=$this->input->post('type','');
//            $option_value=$this->input->post('val');
//            if (!$order_id) {
//                $error='Unknown order';
//            } elseif (!$option_type) {
//                $error='Unknown option';
//            } else {
//                $option_fl=0;
//                switch ($option_type) {
//                    case 'order_rush':
//                        $option_fl=1;
//                        $options=array(
//                            'order_rush'=>$option_value,
//                        );
//                        break;
//                    case 'order_art':
//                        $option_fl=1;
//                        $options=array(
//                            'order_art'=>$option_value,
//                        );
//                        break;
//                    case 'order_redr':
//                        $option_fl=1;
//                        $options=array(
//                            'order_redrawn'=>$option_value,
//                        );
//                        break;
//                    case 'order_vect':
//                        $option_fl=1;
//                        $options=array(
//                            'order_vectorized'=>$option_value,
//                        );
//                        break;
//                    case 'order_proof':
//                        $option_fl=1;
//                        $options=array(
//                            'order_proofed'=>$option_value,
//                        );
//                        break;
//                    case 'order_approv':
//                        $option_fl=1;
//                        $options=array(
//                            'order_approved'=>$option_value,
//                        );
//                        break;
//                    default:
//                        break;
//                }
//                if ($option_fl==0) {
//                    $error='Invalid option';
//                } else {
//                    $res=$this->mordernqb->order_changeoptions($order_id,$options);
//                    if (!$res) {
//                        $error='Update ended unsucessfully';
//                    }
//                }
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Edit order Not in QB Attachments */
//    function notqb_attachment() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $order_attach=$this->mordernqb->get_order_attachments($order_id);
//            $order_data=$this->mordernqb->get_order_detail($order_id);
//            $cnt=count($order_attach);
//            $ordesview=$this->load->view('finance/orderattach_list_view',array('attach'=>$order_attach,'cnt'=>$cnt),TRUE);
//            $mdata['content']=$this->load->view('finance/ajax_orderattachedit_view',array('order_id'=>$order_id,'attach_list'=>$ordesview,'order_data'=>$order_data),TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Save attach to orders Not in QB */
//    function notqb_saveattach() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $filename=$this->input->post('filename');
//            $doc_name=$this->input->post('doc_name');
//            $order_id=$this->input->post('order_id');
//            /* Save file */
//            $res=$this->mordernqb->save_orederattach($order_id,$filename,$doc_name);
//            // if ($res)
//            $order_attach=$this->mordernqb->get_order_attachments($order_id);
//            $cnt=count($order_attach);
//            $mdata['content']=$this->load->view('finance/orderattach_list_view',array('attach'=>$order_attach,'cnt'=>$cnt),TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Delete Order Not in QB Attachments */
//    function notqb_attach_delete() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $attach_id=$this->input->post('attach_id');
//            $order_id=$this->input->post('order_id');
//            /* Save file */
//            $res=$this->mordernqb->delete_orederattach($attach_id);
//            if (!$res) {
//                $error='Error during delete of attach';
//            } else {
//                $order_attach=$this->mordernqb->get_order_attachments($order_id);
//                $cnt=count($order_attach);
//                $mdata['content']=$this->load->view('finance/orderattach_list_view',array('attach'=>$order_attach,'cnt'=>$cnt),TRUE);
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Edit Not in QB ART Note */
//    function notqb_artnote() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $order_dat=$this->mordernqb->get_order_detail($order_id);
//            $mdata['content']=$this->load->view('finance/orderartnote_edit_view',$order_dat,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Save Order Not in QB Art Note */
//    function notqb_artnote_save() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $art_note=$this->input->post('art_note');
//            $this->mordernqb->save_artnote($order_id,$art_note);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Delete order not in QB */
//    function delete_nqborder() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $res=$this->mordernqb->delete_order($order_id);
//            if ($res==0) {
//                $error='Order was not deleted';
//            } else {
//                $mdata['totals']=$this->morder->get_count_orders();
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Questions Section */
//    function questionsdat() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            /* 'search':search, 'offset':page_index,'limit':perpage,'maxval':maxval */
//            $offset=$this->input->post('offset',0);
//            $limit=$this->input->post('limit',10);
//            $order_by=$this->input->post('order_by');
//            $direct = $this->input->post('direction','asc');
//            $searchval=$this->input->post('search','');
//            $maxval=$this->input->post('maxval');
//            $assign=$this->input->post('assign');
//            $brand=$this->input->post('brand');
//            $hideincl=$this->input->post('hideincl');
//            $search=array();
//            if ($searchval) {
//                $search['search']=$searchval;
//            }
//            if ($assign) {
//                $search['assign']=$assign;
//            }
//            if ($brand) {
//                $search['brand']=$brand;
//            }
//            if ($hideincl) {
//                $search['hideincl']=$hideincl;
//            }
//
//            $offset=$offset*$limit;
//
//            /* Fetch data about prices */
//            $questdat=$this->mquests->get_questions($search,$order_by,$direct,$limit,$offset,$maxval);
//
//            if (count($questdat)==0) {
//                $mdata['content']=$this->load->view('questions/empty_tabledat_view',array(),TRUE);
//            } else {
//                $data=array(
//                    'quests'=>$questdat,
//                );
//                $mdata['content']=$this->load->view('questions/tabledat_view',$data, TRUE);
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Count # of questions */
//    function questcount() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $assign=$this->input->post('assign');
//            $search_val=$this->input->post('search');
//            $brand=$this->input->post('brand');
//            $hideincl=$this->input->post('hideincl');
//
//            $search=array();
//            if ($assign) {
//                $search['assign']=$assign;
//            }
//            if ($hideincl) {
//                $search['hideincl']=$hideincl;
//            }
//            if ($search_val) {
//                $search['search']=$search_val;
//            }
//            if ($brand) {
//                $search['brand']=$brand;
//            }
//            $mdata['total_rec']=$this->mquests->get_count_questions($search);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//
//    function question_detail() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quest_id=$this->input->post('quest_id');
//            /* Get data about question */
//            $quest=$this->mquests->get_quest_data($quest_id);
//            $mdata['content']=$this->load->view('questions/details_view',$quest,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//    /* Include / exclude question */
//    public function question_include() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quest_id=$this->input->post('question_id');
//            $quest=$this->mquests->get_quest_data($quest_id);
//            if (!isset($quest['email_id'])) {
//                $error='Unknown Question';
//            } else {
//                $newval=($quest['email_include_lead']==1 ? 0 : 1 );
//                $res=$this->mquests->question_include($quest_id,$newval);
//                if ($res['result']==Leads::ERR_FLAG) {
//                    $error=$res['msg'];
//                } else {
//                    $mdata['newicon']=$res['newicon'];
//                    $mdata['newclass']=$res['newclass'];
//                    $mdata['newmsg']=$res['newmsg'];
//                }
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Show Quote Details from Lead Form */
//    function show_quote_detail() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quote_id=$this->input->post('quote_id');
//            $quote=$this->mquotes->get_quote_dat($quote_id);
//            if ($quote['email_quota_link']) {
//                $mdata['url']=$quote['email_quota_link'];
//            } else {
//                $error='Quote in Process Stage';
//            }
//            // $mdata['content']=$this->load->view('onlinequotes/details_view',$quote,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//
//    /* Show Quote Details from Lead Form */
//    function show_quote_detail_old() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $data=$this->input->post();
//            $leaddat=array();
//            foreach ($data as $key=>$val) {
//                if ($key=='quote_id') {
//                    $quote_id=$val;
//                } else {
//                    $leaddat[$key]=$val;
//                }
//            }
//            /* Save data to session */
//            $this->session->set_userdata('leaddata',$leaddat);
//            $quote=$this->mquotes->get_quote_dat($quote_id);
//            $mdata['content']=$this->load->view('onlinequotes/details_view',$quote,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    public function quote_include() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quote_id=$this->input->post('quote_id');
//            $quote=$this->mquotes->get_quote_dat($quote_id);
//            if (!isset($quote['email_id'])) {
//                $error='Unknown Online Quote';
//            } else {
//                $newval=($quote['email_include_lead']==1 ? 0 : 1 );
//                $res=$this->mquotes->quote_include($quote_id,$newval);
//                if ($res['result']==Leads::ERR_FLAG) {
//                    $error=$res['msg'];
//                } else {
//                    $mdata['newicon']=$res['newicon'];
//                    $mdata['newclass']=$res['newclass'];
//                    $mdata['newmsg']=$res['newmsg'];
//                }
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//
//
//
//    /* Show proof Details from Lead Form */
//    function show_proof_details() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $data=$this->input->post();
//            $leaddat=array();
//            foreach ($data as $key=>$val) {
//                if ($key=='proof_id') {
//                    $proof_id=$val;
//                } else {
//                    $leaddat[$key]=$val;
//                }
//            }
//            /* Save data to session */
//            $this->session->set_userdata('leaddata',$leaddat);
//            $proof=$this->mproofs->get_proof_data($proof_id);
//            $mdata['content']=$this->load->view('artproof/details_view',$proof,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Save Lead form data into session, show details */
//    function show_question_detail() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $data=$this->input->post();
//            $leaddat=array();
//            foreach ($data as $key=>$val) {
//                if ($key=='quest_id') {
//                    $quest_id=$val;
//                } else {
//                    $leaddat[$key]=$val;
//                }
//            }
//            /* Save data to session */
//            $this->session->set_userdata('leaddata',$leaddat);
//            $quest=$this->mquests->get_quest_data($quest_id);
//            $mdata['content']=$this->load->view('questions/details_view',$quest,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    function quotesdat() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $offset=$this->input->post('offset',0);
//            $limit=$this->input->post('limit',10);
//            $order_by=$this->input->post('order_by');
//            $direct = $this->input->post('direction','desc');
//            $maxval=$this->input->post('maxval');
//            $brand=$this->input->post('brand');
//            $search_val=$this->input->post('search');
//            $assign=$this->input->post('assign');
//            $hideincl=$this->input->post('hideincl');
//            $search=array();
//            if ($assign) {
//                $search['assign']=$assign;
//            }
//            if ($search_val) {
//                $search['search']=$search_val;
//            }
//            if ($brand) {
//                $search['brand']=$brand;
//            }
//            if ($hideincl) {
//                $search['hideincl']=$hideincl;
//            }
//
//            $ordoffset=$offset*$limit;
//            $offset=$offset*$limit;
//
//            /* Get data about Competitor prices */
//            if ($ordoffset>$maxval) {
//                $ordnum = $maxval;
//            } else {
//                $ordnum = $maxval - $ordoffset;
//            }
//            $email_dat=$this->mquotes->get_quotes($search,$order_by,$direct,$limit,$offset,$maxval);
//
//            if (count($email_dat)==0) {
//                $content = $this->load->view('onlinequotes/emptytabdata_view',array(), TRUE);
//            } else {
//                $data=array('email_dat'=>$email_dat,'offset'=>$offset,'ordnum'=>$ordnum);
//                $content = $this->load->view('onlinequotes/tabledat_view',$data, TRUE);
//
//            }
//            $mdata['content']=$content;
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Data about Proofs  */
//    function proofsdat() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $offset=$this->input->post('offset',0);
//            $limit=$this->input->post('limit',10);
//            $order_by=$this->input->post('order_by');
//            $direct = $this->input->post('direction','desc');
//            $maxval=$this->input->post('maxval');
//            $brand=$this->input->post('brand');
//            $search_val=$this->input->post('search');
//            $assign=$this->input->post('assign');
//            $search=array();
//            if ($assign) {
//                $search['assign']=$assign;
//            }
//            if ($search_val) {
//                $search['search']=$search_val;
//            }
//            if ($brand) {
//                $search['brand']=$brand;
//            }
//
//            $ordoffset=$offset*$limit;
//            $offset=$offset*$limit;
//
//            if ($ordoffset>$maxval) {
//                $ordnum = $maxval;
//            } else {
//                $ordnum = $maxval - $ordoffset;
//            }
//
//            $email_dat=$this->mproofs->get_proofs($search,$order_by,$direct,$limit,$offset,$maxval);
//
//            if (count($email_dat)==0) {
//                $content = $this->load->view('artproof/emptytabledat_view',array(), TRUE);
//            } else {
//                $data=array('email_dat'=>$email_dat);
//                $content = $this->load->view('artproof/tabledat_view',$data, TRUE);
//
//            }
//            $mdata['content']=$content;
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Number of Records - search */
//    function countproofs() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $brand=$this->input->post('brand');
//            $search_val=$this->input->post('search');
//            $assign=$this->input->post('assign');
//            $search=array();
//            if ($assign) {
//                $search['assign']=$assign;
//            }
//            if ($search_val) {
//                $search['search']=$search_val;
//            }
//            if ($brand) {
//                $search['brand']=$brand;
//            }
//            $mdata['total_rec']=$this->mproofs->get_count_proofs($search);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Art proof details */
//    function proofdetails() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $proof_id=$this->input->post('proof_id');
//            $data=$this->mproofs->get_proof_data($proof_id);
//            if (!isset($data['email_id'])) {
//                $error='Incorrect Proof';
//            } else {
//                $mdata['content']=$this->load->view('artproof/details_view',$data,TRUE);
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//
//    /* Count # of quotes */
//    function quotecount() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $assign=$this->input->post('assign');
//            $search_val=$this->input->post('search');
//            $brand=$this->input->post('brand');
//            $hideincl=$this->input->post('hideincl');
//            $search=array();
//            if ($assign) {
//                $search['assign']=$assign;
//            }
//            if ($search_val) {
//                $search['search']=$search_val;
//            }
//            if ($brand) {
//                $search['brand']=$brand;
//            }
//            if ($hideincl) {
//                $search['hideincl']=$hideincl;
//            }
//            $mdata['total_rec']=$this->mquotes->get_count_quotes($search);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Quote Details */
//    function quote_details() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quote_id=$this->input->post('quote_id');
//            /* Get Data */
//            $data=$this->mquotes->get_quote_dat($quote_id);
//            $mdata['content']=  $this->load->view('onlinequotes/details_view',$data,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Restore Lead Edit form */
//    function restore_ledform() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            /* Restore session data */
//            $leaddat=$this->session->userdata('leaddata');
//
//            /* Label */
//            /* Prepare arrays for view */
//            $lead_id=$leaddat['lead_id'];
//            $dead_av=1;
//            // $replicas=$this->muser->get_user_leadreplicas();
//            if ($lead_id==0) {
//                $lead_data=$this->mleads->get_empty_lead();
//                $dead_av=0;
//                $lead_data['lead_id']=0;
//                $lead_data['lead_type']=2;
//                $lead_data['lead_number']=$this->mleads->get_leadnum();
//                $lead_history=array();
//                $lead_usr=array();
//            } else {
//                $lead_data=$this->mleads->get_lead($lead_id);
//                if (isset($lead_data['create_date'])) {
//                    $crtime=strtotime($lead_data['create_date']);
//                    if (time()-$crtime<$this->timeout_dead) {
//                        $dead_av=0;
//                    }
//                }
//                $lead_history=$this->mleads->get_lead_history($lead_id);
//                if (isset($leaddat['session_id'])) {
//                    $session_id=$leaddat['session_id'];
//                    $lead_users=$this->func->session($session_id);
//                    $lead_usr=[];
//                    foreach ($lead_users as $row) {
//                        array_push($lead_usr, $row['user_id']);
//                    }
//                } else {
//                    $lead_usr=$this->mleads->get_lead_users($lead_id);
//                }
//            }
//            $lead_tasks=$this->mleads->get_lead_tasks($lead_id);
//
//            $save_av=1;
//
//            /* */
//            if (count($lead_usr)==0) {
//                array_push($lead_usr, $this->USR_ID);
//            }
//            $lead_replic=array();
//            foreach ($lead_usr as $row) {
//                $usr=$this->muser->get_user_data($row);
//                $lead=array(
//                    'user_id'=>$row,
//                    'user_leadname'=>$usr['user_leadname'],
//                    'value'=>1,
//                );
//                $lead_replic[]=$lead;
//            }
//            if ($lead_data['lead_type']==Leads::LEAD_CLOSED || $lead_data['lead_type']==Leads::LEAD_DEAD) {
//                $replic=$this->load->view('leads/lead_replicalock_view',array('repl'=>$lead_replic),TRUE);
//            } else {
//                if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin') {
//                    $replic=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic),TRUE);
//                } else {
//                    $replic=$this->load->view('leads/lead_replicareadonly_view',array('repl'=>$lead_replic),TRUE);
//                }
//            }
//            // Save User Lead into session
//            $session_id='leadusers'.$this->func->uniq_link(10);
//            $this->func->session($session_id, $lead_replic);
//            $lead_data['other_item_label']='';
//            if ($lead_data['lead_item']=='Other') {
//                $lead_data['other_item_label']='Type Other Item Here:';
//            } elseif ($lead_data['lead_item']=='Multiple') {
//                $lead_data['other_item_label']='Type Multiple Items Here:';
//            }
//
//
//
//            $history=$this->load->view('leads/lead_history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
//            $lead_tasks['edit']=$save_av;
//
//            // $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
//            $tasks='';
//            $qdat=$this->mquests->get_lead_questions($lead_id);
//            if (count($qdat)==0) {
//                $questions='';
//            } else {
//                $questions=$this->load->view('leads/lead_questions_view',array('quests'=>$qdat),TRUE);
//            }
//
//            $qdat=$this->mquotes->get_lead_quotes($lead_id);
//            if (count($qdat)==0) {
//                $quotes='';
//            } else {
//                $quotes=$this->load->view('leads/lead_quotes_view',array('quotes'=>$qdat),TRUE);
//            }
//
//            $qdat=$this->mproofs->get_lead_proofs($lead_id);
//            if (count($qdat)==0) {
//                $onlineproofs='';
//            } else {
//                $onlineproofs=$this->load->view('leads/lead_proofs_view',array('proofs'=>$qdat),TRUE);
//            }
//            $dead_option='';
//            if ($dead_av==1) {
//                $dead_selected=($lead_data['lead_type'] == 3 ? 'selected="selected"' : '');
//                $dead_option="<option value=\"3\" ".$dead_selected.">Dead</option>";
//            } else {
//                $dead_option='';
//            }
//            /* Get Available Items */
//            $items_list=$this->mleads->items_list();
//            // $itemslist=$this->m
//            $options=array(
//                'data'=>$lead_data,
//                'history'=>$history,
//                'replica'=>$replic,
//                'tasks'=>$tasks,
//                'quotes'=>$quotes,
//                'questions'=>$questions,
//                'onlineproofs'=>$onlineproofs,
//                'save_available'=>$save_av,
//                'dead_option'=>$dead_option,
//                'items' => $items_list,
//                'session_id'=>$session_id,
//            );
//            $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    function change_status() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quest_id=$this->input->post('quest_id');
//            $type=$this->input->post('type');
//            $chkrel=$this->mleads->check_leadrelation($quest_id);
//            if ($chkrel) {
//                $error='This Request Related with Lead. Please, reload page';
//                $this->func->ajaxResponse($mdata, $error);
//            }
//            /* Get data about question */
//            $quest=$this->mquests->get_quest_data($quest_id);
//
//            /* Get open leads  */
//            $options=array(
//                'orderby'=>'lead_number',
//                'direction'=>'desc',
//            );
//            $leaddat=$this->mleads->get_lead_list($options);
//            $options=array('leads'=>$leaddat,'current'=>$quest['lead_id']);
//            switch ($type) {
//                case 'quote':
//                    $options['title']='Quote Details';
//                    break;
//                case 'question':
//                    $options['title']='Question Details';
//                    break;
//                case 'proof':
//                    $options['title']='Proof Details';
//                    break;
//                default:
//                    $options['title']='Message Details';
//                    break;
//            }
//            $quest['leadselect']=$this->load->view('leads/lead_openlist_view',$options,TRUE);
//            $mdata['content']=$this->load->view('questions/update_status_view',$quest,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    function savequeststatus() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $quest=$this->input->post();
//            /* Get data about question */
//            $res=$this->mleads->save_leadrelation($quest);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $data=$this->mquests->get_quest_data($quest['mail_id']);
//                /* Recalculate Totals New  */
//                $mdata['type']=$data['email_type'];
//                $mdata['total_proof']=$this->mproofs->get_count_proofs(array('assign'=>1));
//                $mdata['total_quote']=$this->mquotes->get_count_quotes(array('assign'=>1));
//                $mdata['total_quest']=$this->mquests->get_count_questions(array('assign'=>1));
//                $mdata['sumquote']=$this->mquotes->get_todays();
//                $mdata['sumproofs']=$this->mproofs->get_todays();
//                $mdata['sumquest']=$this->mquests->get_todays();
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Get data about 1 email */
//    function maildetails() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $email_id=$this->input->post('mail_id');
//            $type=$this->input->post('type');
//            $rownum=$this->input->post('rownum');
//            if (!$email_id) {
//                $error='Unknown Message';
//            } else {
//                switch ($type) {
//                    case 'Question':
//                        $maildat=$this->mquests->get_quest_data($email_id);
//                        $maildat['ordnum']=$rownum;
//                        $mdata['content']=$this->load->view('questions/questrow_view',$maildat,TRUE);
//                        $mdata['rowclass']=($maildat['lead_id']=='' ? '' : 'leadentered');
//                        break;
//                    default:
//                        break;
//                }
//
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Data about new lead */
//    function change_leadrelation() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $lead_id=$this->input->post('lead_id');
//            if (!$lead_id) {
//                $error='Unknown Lead';
//            } else {
//                $leaddata=$this->mleads->get_lead($lead_id);
//                if (!isset($leaddata['lead_id'])) {
//                    $error='Lead not found';
//                } else {
//                    $mdata['lead_date']=($leaddata['lead_date']==0 ? '' : 'Date: '.date('m/d/y',$leaddata['lead_date']));
//                    $mdata['lead_customer']='Name: '.$leaddata['lead_customer'];
//                    $mdata['lead_mail']='Email: '.$leaddata['lead_mail'];
//                }
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Create Lead from Message */
//    function create_leadmessage() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $email_id=$this->input->post('mail_id');
//            $leademail_id=$this->input->post('leademail_id');
//            $type=$this->input->post('type');
//            $chkrel=$this->mleads->check_leadrelation($email_id);
//            if ($chkrel) {
//                $error='This Request Related with Lead. Please, reload page';
//                $this->func->ajaxResponse($mdata, $error);
//            }
//            switch ($type) {
//                case 'Question':
//                    $maildat=$this->mquests->get_quest_data($email_id);
//                    $res=$this->mleads->create_leadquest($maildat,$leademail_id,$this->USR_ID);
//                    break;
//                case 'Quote':
//                    $maildat=$this->mquotes->get_quote_dat($email_id);
//                    $res=$this->mleads->create_leadquote($maildat,$leademail_id, $this->USR_ID);
//                    break;
//                case 'Proof';
//                    $maildat=$this->mproofs->get_proof_data($email_id);
//                    $res=$this->mleads->create_leadproof($maildat,$leademail_id, $this->USR_ID);
//                    break;
//                default:
//                    break;
//            }
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $mdata['total_proof']=$this->mproofs->get_count_proofs(array('assign'=>1));
//                $mdata['total_quote']=$this->mquotes->get_count_quotes(array('assign'=>1));
//                $mdata['total_quest']=$this->mquests->get_count_questions(array('assign'=>1));
//                $mdata['sumquote']=$this->mquotes->get_todays();
//                $mdata['sumproofs']=$this->mproofs->get_todays();
//                $mdata['sumquest']=$this->mquests->get_todays();
//                $mdata['leadid']=$res['result'];
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Duplicate Leads */
//    function dublicatelead() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            /* Save current lead */
//            $leadpost=$this->input->post();
//            /* Get Tasks & user array */
//            $lead_tasks=array();
//            $session_id=$leadpost['session_id'];
//            $lead_replic=$this->func->session($session_id);
//
//            $lead_usr=array();
//            foreach ($lead_replic as $row) {
//                array_push($lead_usr, $row['user_id']);
//            }
//            $lead_tasks['leadtask_id']=(isset($leadpost['leadtask_id']) ? $leadpost['leadtask_id'] : NULL);
//            $lead_tasks['send_quote']=(isset($leadpost['send_quote']) ? 1 :0);
//            $lead_tasks['send_artproof']=(isset($leadpost['send_artproof'])?1:0);
//            $lead_tasks['send_sample']=(isset($leadpost['send_sample'])?1:0);
//            $lead_tasks['answer_question']=(isset($leadpost['answer_question'])?1:0);
//            $lead_tasks['other']=(isset($leadpost['other_task']) ? $leadpost['other'] : '');
//            $usr_id=$this->USR_ID;
//            $res=$this->mleads->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $lead_id=$leadpost['lead_id'];
//                /* Duplicate Lead */
//                $lead=$this->mleads->duplicate_lead($lead_id,$this->USR_ID);
//                if ($lead['lead_id']==0) {
//                    $error='Can\'t duplicate Lead';
//                } else {
//                    $lead_id=$lead['lead_id'];
//                    $mdata['lead_number']=$lead['lead_number'];
//                    $lead_usr=$this->mleads->get_lead_users($lead_id);
//                    $lead_replic=[];
//                    foreach ($lead_usr as $row) {
//                        $usr=$this->muser->get_user_data($row);
//                        $lead_replic[]=[
//                            'user_id'=>$row,
//                            'user_leadname'=>$usr['user_leadname'],
//                            'value'=>1,
//                        ];
//                    }
//                    // Save User Lead into session
//                    $session_id='leadusers'.$this->func->uniq_link(10);
//                    $this->func->session($session_id, $lead_replic);
//                    $save_av=1;
//                    /* Get Replicas */
//                    $replic=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic,'edit'=>$save_av),TRUE);
//                    /* History */
//                    $lead_history=array();
//                    $history=$this->load->view('leads/lead_history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
//                    /* Tasks */
//                    $lead_tasks=[]; // $this->mleads->get_lead_tasks($lead_id);
//                    $lead_tasks['edit']=$save_av;
//                    $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
//                    /* Questions */
//                    $qdat=$this->mquests->get_lead_questions($lead_id);
//                    if (count($qdat)==0) {
//                        $questions='';
//                    } else {
//                        $questions=$this->load->view('leads/lead_questions_view',array('quests'=>$qdat),TRUE);
//                    }
//                    /* Quotes  */
//                    $qdat=$this->mquotes->get_lead_quotes($lead_id);
//                    if (count($qdat)==0) {
//                        $quotes='';
//                    } else {
//                        $quotes=$this->load->view('leads/lead_quotes_view',array('quotes'=>$qdat),TRUE);
//                    }
//
//                    /* Online Proofs */
//                    $qdat=$this->mproofs->get_lead_proofs($lead_id);
//                    if (count($qdat)==0) {
//                        $onlineproofs='';
//                    } else {
//                        $onlineproofs=$this->load->view('leads/lead_proofs_view',array('proofs'=>$qdat),TRUE);
//                    }
//
//                    /* Get Available Items */
//                    $items_list=$this->mleads->items_list();
//
//                    $options=array(
//                        'data'=>$lead,
//                        'history'=>$history,
//                        'replica'=>$replic,
//                        'tasks'=>$tasks,
//                        'quotes'=>$quotes,
//                        'questions'=>$questions,
//                        'onlineproofs'=>$onlineproofs,
//                        'save_available'=>$save_av,
//                        'items' => $items_list,
//                        'session_id'=>$session_id,
//                    );
//                    $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
//                }
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Calsulate # of today messages */
//    function countmsg() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $mdata['sumquote']=$this->mquotes->get_todays();
//            $mdata['sumproofs']=$this->mproofs->get_todays();
//            $mdata['sumquest']=$this->mquests->get_todays();
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    /* Docums functions */
//    function depositdata() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $assign=$this->input->post('assign');
//            $options=array(
//                'assign'=>$assign,
//            );
//            $depos=$this->mdocs->get_deposits($options);
//            $mdata['content']=$this->load->view('docums/deposit_table_view',array('deposits'=>$depos),TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Add Deposit */
//    function adddeposit() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $res=$this->mdocs->adddeposit($this->USR_ID);
//            if ($res==Leads::ERR_FLAG) {
//                $error='Deposit wasn\'t added ';
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Edit deposit Amount */
//    function depositamnt() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            $depos=$this->mdocs->get_deposit($deposit_id,'edit');
//            $mdata['content']=$this->load->view('docums/edit_deposamnt_view',$depos,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Save deposit amnt */
//    function savedepositamnt() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            $amount=floatval($this->input->post('amount'));
//            $mdata['amnt']=$this->mdocs->savedeposamnt($deposit_id, $amount, $this->USR_ID);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Edit Replica */
//    function deposreplic() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            $depos=$this->mdocs->get_deposit($deposit_id,'edit');
//            $mdata['content']=$this->load->view('docums/edit_deposreplic_view',$depos,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Save Replic */
//    function savedepositrepl() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            $replic=$this->input->post('replic');
//            $mdata['replic']=$this->mdocs->savedeposreplic($deposit_id, $replic, $this->USR_ID);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Change Deposit options */
//    function deposit_option() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            $type=$this->input->post('type');
//            $newval=$this->input->post('newval');
//            $mdata['reinit']=$this->mdocs->save_depositoptions($deposit_id, $type, $newval, $this->USR_ID);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Attachments for deposit */
//    function deposit_attachment() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            /* Get List of attachments */
//            $attach=$this->mdocs->get_deposit_docs($deposit_id);
//            $cnt=count($attach);
//            $attachlist=$this->load->view('docums/attachlist_view',array('attach'=>$attach,'cnt'=>$cnt),TRUE);
//            $depos=$this->mdocs->get_deposit($deposit_id,'edit');
//            $deposit_date=date('m/d/Y',$depos['deposit_date']);
//            $mdata['content']=$this->load->view('docums/attach_edit_view',array('deposit_id'=>$deposit_id,'attach_list'=>$attachlist,'deposit_date'=>$deposit_date),TRUE);
//
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Save Attachment */
//    function deposit_saveattach() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            /* 'filename':responseJSON.filename,'doc_name':fileName,'deposit_id':deposit_id */
//            $deposit_id=$this->input->post('deposit_id');
//            $filename=$this->input->post('filename');
//            $doc_name=$this->input->post('doc_name');
//            $res=$this->mdocs->save_deposattach($deposit_id,$doc_name,$filename);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $attach=$this->mdocs->get_deposit_docs($deposit_id);
//                $cnt=count($attach);
//                $attachlist=$this->load->view('docums/attachlist_view',array('attach'=>$attach,'cnt'=>$cnt),TRUE);
//                $mdata['content']=$attachlist;
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Delete Deposit Attachment */
//    function deposit_attachdelete() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_doc_id=$this->input->post('attach_id');
//            $res=$this->mdocs->delete_depositattach($deposit_doc_id);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $deposit_id=$res['deposit_id'];
//                $attach=$this->mdocs->get_deposit_docs($deposit_id);
//                $cnt=count($attach);
//                $attachlist=$this->load->view('docums/attachlist_view',array('attach'=>$attach,'cnt'=>$cnt),TRUE);
//                $mdata['content']=$attachlist;
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Close popup deposit attachments - change Attachment Icon */
//    function deposit_attachclose() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $deposit_id=$this->input->post('deposit_id');
//            $cnt=$this->mdocs->get_depositdocs_count($deposit_id);
//            if ($cnt==0) {
//                $mdata['content']='<img src="/img/empty_square.png" alt="Empty attachment"/>';
//            } else {
//                $mdata['content']='<img src="/img/yellow_square.png" alt="Attachments" title="'.$cnt.' attchment(s)"/>';
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Data about invoices ready to print */
//    function invprintdata() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $assign=$this->input->post('assign');
//            $options=array(
//                'assign'=>$assign,
//            );
//            $docs=$this->mdocs->get_invdata($options);
//            $mdata['content']=$this->load->view('docums/invprn_table_view',array('docs'=>$docs),TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Mark order as mailed */
//    function mailedord() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $order_id=$this->input->post('order_id');
//            $newval=$this->input->post('newval');
//            $res=$this->mdocs->order_mailed($order_id,$newval,$this->USR_ID);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Get data about documents for sort */
//    function docsortsdata() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $assign=$this->assign=$this->input->post('assign');
//            $options=array(
//                'assign'=>$assign,
//            );
//            $docs=$this->mdocs->get_docsort($options);
//            $mdata['content']=$this->load->view('docums/docsort_table_view',array('docs'=>$docs),TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Edit docsort ROW */
//    function docsort_edit() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $docsort_id=$this->input->post('docedit_id');
//            $docs=$this->mdocs->get_docsort_data($docsort_id);
//            $mdata['content']=$this->load->view('docums/docsort_edit_view',$docs,TRUE);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    function docsort_save() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $details=$this->input->post();
//            $res=$this->mdocs->docsort_update($details,$this->USR_ID);
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    /* Manualy add documsort */
//    function add_docsort() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $usrdat=$this->muser->get_user_data($this->USR_ID);
//
//            $options=array(
//                'docsort_replic'=>$usrdat['user_initials'],
//            );
//            $mdata['content']=$this->load->view('docums/docsort_add_view',$options,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//    function newuploaddoc() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $options=array();
//            $options['filename']=$this->input->post('filename');
//            $options['doc_name']=$this->input->post('doc_name');
//            $mdata['content']=$this->load->view('docums/newdoc_view',$options,TRUE);
//            $this->func->ajaxResponse($mdata,$error);
//        }
//    }
//
//    function addsortdoc() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $filename=$this->input->post('filename');
//            $path_from=$this->config->item('upload_path_preload');
//            $path_to=$this->config->item('documattach');
//            $file_path=$this->config->item('documattach_path');
//            /* Save file to new place */
//            $res=$this->func->move_docfile($filename,$path_from,$path_to);
//            if (!$res) {
//                $error='Error during save Document source';
//            } else {
//                $doclink=$file_path.$filename;
//                $user_repl=$this->input->post('replic');
//                $res=$this->mdocs->create_docsort($doclink, $user_repl, $this->USR_ID);
//                if (!$res) {
//                    $error='Error';
//                }
//            }
//            $this->func->ajaxResponse($mdata, $error);
//        }
//    }
//
//    /* Search items */
//    public function search_item() {
//        $item=$this->input->get('term');
//        $get_dat=$this->mleads->search_items($item);
//        echo json_encode($get_dat);
//
//    }
//
//    public function lead_itemchange() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $item_id=$this->input->post('item_id');
//            /* Search data */
//            $res=$this->mleads->search_itemid($item_id);
//
//            if ($res['result']==Leads::ERR_FLAG) {
//                $error=$res['msg'];
//            } else {
//                $mdata['item_number']=$res['item_number'];
//                $mdata['item_name']=$res['item_name'];
//                $mdata['other_label']='';
//                if ($item_id<0) {
//                    // if ($res['item_name']=='Other' || $res['item_name']=='Multiple' || $res['item_name']=='Custom Shaped Stress Balls') {
//                    $mdata['other']=1;
//                    switch ($res['item_name']) {
//                        case 'Other':
//                            $mdata['other_label']='Type Other Item Here:';
//                            break;
//                        case 'Multiple':
//                            $mdata['other_label']='Type Multiple Items Here:';
//                            break;
//                        default :
//                            $mdata['other_label']='Type Custom Items Here:';
//                            break;
//                    }
//                } else {
//                    $mdata['other']=0;
//                }
//            }
//
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    // Show Items Content
//    public function leaditems_data() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $this->load->model('vendors_model');
//            $postdata=$this->input->post();
//            $limit=(isset($postdata['limit']) ? $postdata['limit'] : 150);
//            $offset=(isset($postdata['offset']) ? $postdata['offset']*$limit : 0);
//            $options=array(
//                'offset'=>$offset,
//                'limit'=>$limit,
//                'order_by'=>'item_number',
//                'prices'=>$this->vendor_prices,
//            );
//            if (isset($postdata['search']) && !empty($postdata['search'])) {
//                $options['search']=strtoupper($postdata['search']);
//            }
//            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
//                $options['vendor_id']=$postdata['vendor_id'];
//            }
//            if (isset($postdata['priority']) && !empty($postdata['priority'])) {
//                $options['priority']=$postdata['priority'];
//            }
//            // Manage other parameters
//            $items=$this->vendors_model->get_items_data($options);
//            if (count($items)==0) {
//                // Empty Content
//                $mdata['content']=$this->load->view('leaditems/empty_data_view', array(), TRUE);
//            } else {
//                $outoptions=array(
//                    'data'=>$items,
//                    'prices'=>$this->vendor_prices,
//                );
//                $mdata['content']=$this->load->view('leaditems/table_data_view', $outoptions, TRUE);
//            }
//            $this->func->ajaxResponse($mdata,$error);
//        }
//        show_404();
//    }
//
//    // Count Items
//    public function leaditems_count() {
//        if ($this->func->isAjax()) {
//            $mdata=array();
//            $error='';
//            $this->load->model('vendors_model');
//            $postdata=$this->input->post();
//            $options=array();
//            if (isset($postdata['search']) && !empty($postdata['search'])) {
//                $options['search']=strtoupper($postdata['search']);
//            }
//            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
//                $options['vendor_id']=$postdata['vendor_id'];
//            }
//            $mdata['total']=$this->vendors_model->count_items($options);
//            $this->func->ajaxResponse($mdata, $error);
//        }
//        show_404();
//    }
//
//    /* Prepare Content */
//    private function prepare_leads() {
//        $ldat=array();
//        /* Prepare Right part */
//        $rdat=array();
//        $date=date('Y-m').'-01';
//        $dateend = strtotime(date("Y-m-d", strtotime($date)) . " +1 month");
//        $total_options=array(
//            'enddate'=>$dateend,
//            'user_id'=>$this->USR_ID,
//        );
//        $rdattop=$this->mleads->count_closed_totals($total_options);
//        $rdat['prev']=$rdattop['prev'];
//        $rdat['next']=$rdattop['next'];
//        $rdat['data']=$rdattop['data'];
//        $rdat['dateend']=$dateend;
//        $rdat['datestart']=$rdattop['datestart'];
//        $ldat['right_content']=$this->load->view('leadpage/close_total_view', $rdat, TRUE);
//
//        /* Get user with reps */
//        $ldat['replicas']=$this->muser->get_user_leadreplicas();
//        $ldat['user_id']=$this->USR_ID;
//        $user_dat=$this->muser->get_user_data($this->USR_ID);
//        $ldat['user_name']=($user_dat['user_leadname']=='' ? $this->USR_NAME : $user_dat['user_leadname']);
//
//        $options=array(
//            'lead_type'=>1,
//            'usrrepl'=>  $this->USR_ID,
//        );
//
//        $ldat['totalrec']=$this->mleads->get_total_leads($options);
//        $ldat['perpage']=$this->PERPAGE_LEADS;
//        $ldat['curpage']=0;
//        $this->load->model('order_model');
//        $ldat['totalorders']=$this->order_model->orders_total_year(date('Y'));
//        $content=$this->load->view('leads/leadtab_view',$ldat,TRUE);
//        return $content;
//    }
//
//    private function prepare_questions() {
//        $datqs=array();
//        $datqs['search_form']=$this->load->view('questions/search_form_view',array(),TRUE);
//        $datqs['perpage']=$this->PERPAGE;
//        $search=array('assign'=>1);
//        $datqs['total_rec']=$this->mquests->get_count_questions($search);
//        $datqs['order_by']='email_date';
//        $datqs['direction']='desc';
//        $datqs['cur_page']=0;
//        $content=$this->load->view('questions/questions_view',$datqs,TRUE);
//        return $content;
//    }
//    private function prepare_nonqb() {
//        $datq=array();
//        $datq['searchform']=$this->load->view('fingeneral/generalnqb_search_view',array(),TRUE);
//        $datq['perpage']=  $this->PERPAGE;
//        $datq['total_rec']=$this->mordernqb->get_count_orders();
//        $datq['order_by']='o.order_num';
//        $datq['direction']='desc';
//        $datq['cur_page']=0;
//        $content=$this->load->view('fingeneral/general_ordersnqb_view',$datq,TRUE);
//        return $content;
//    }
//
//    private function prepare_quotes() {
//        $datqs=array();
//        $datqs['searchform']=$this->load->view('onlinequotes/search_form_view',array(),TRUE);
//        $datqs['perpage']=$this->PERPAGE;
//        $search=array('assign'=>1);
//        $datqs['total_rec']=$this->mquotes->get_count_quotes($search);
//        $datqs['order_by']='email_date';
//        $datqs['direction']='desc';
//        $datqs['cur_page']=0;
//        $content=$this->load->view('onlinequotes/quotes_view',$datqs,TRUE);
//        return $content;
//
//    }
//    private function prepare_proofs() {
//        $datqs=array();
//        $datqs['perpage']=$this->PERPAGE;
//        $search=array('assign'=>1);
//        $datqs['total_rec']=$this->mproofs->get_count_proofs($search);
//        $datqs['order_by']='email_date';
//        $datqs['direction']='desc';
//        $datqs['cur_page']=0;
//        $datqs['assign']='1';
//        $content=$this->load->view('artpage/prooflist_head_view',$datqs,TRUE);
//        return $content;
//    }
//
//    private function prepare_docums() {
//        $datqs=array();
//        $content=$this->load->view('docums/docums_view',$datqs,TRUE);
//        return $content;
//    }
//
//    private function prepare_customers() {
//        $datqs=array();
//        $datqs['perpage']=$this->PERPAGE_LEADS;
//        $search=array();
//        $datqs['total_rec']=$this->contact_model->get_count_contacts($search);
//        $datqs['order_by']='contact_name';
//        $datqs['direction']='asc';
//        $datqs['cur_page']=0;
//        $srchopt=array();
//        $srchopt[]='1';
//        for ($i=65; $i<=90; $i++) {
//            $srchopt[]=chr($i);
//        }
//        $datqs['searchopt']=$srchopt;
//        $datqs['searchview']=$this->load->view('contacts/chars_search_view',$datqs,TRUE);
//        $content=$this->load->view('contacts/list_head_view',$datqs,TRUE);
//        return $content;
//    }
//
//    // Orders
//    private function prepare_orders() {
//        $datqs=array();
//        $this->load->model('order_model');
//        $this->load->model('user_model');
//        $users=$this->user_model->get_user_leadreplicas();
//        $datqs['perpage']=$this->PERPAGE_ORDERS;
//        $options=array(
//            // 'order_usr_repic'=>  $this->USR_ID,
//        );
//        $ordertemplate=$this->func->session('searchordertemplate');
//        if (!empty($ordertemplate)) {
//            $options['search']=strtoupper($ordertemplate);
//            $datqs['search']=$ordertemplate;
//            $this->func->session('searchordertemplate',NULL);
//        } else {
//            $datqs['search']='';
//        }
//        $datqs['total']=$this->order_model->get_count_orders($options);
//        $datqs['users']=$users;
//        // $datqs['current_user']=$this->USR_ID;
//        $datqs['current_user']=-2;
//        $datqs['order_by']='order_id';
//        $datqs['direction']='desc';
//        $datqs['cur_page']=0;
//        $content=$this->load->view('leadorder/head_view',$datqs,TRUE);
//        return $content;
//    }
//
//    private function prepare_items() {
//        $datqs=array();
//        $this->load->model('vendors_model');
//        // Get list of vendors
//        $datqs['vendors']=$this->vendors_model->get_vendors_list('v.vendor_name');
//        $datqs['perpage']=$this->PERPAGE_ORDERS;
//        $datqs['total']=$this->vendors_model->count_items();
//        $datqs['cur_page']=0;
//        $datqs['prices']=$this->vendor_prices;
//        $content=$this->load->view('leaditems/head_view',$datqs,TRUE);
//        return $content;
//    }

}
/* End of file leads.php */
/* Location: ./application/controllers/leads.php */