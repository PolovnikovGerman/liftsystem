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
        $content_options['start'] = $this->input->get('start', TRUE);
        $content_options['menu'] = $menu;
        foreach ($menu as $row) {
            if ($row['item_link'] == '#leadsview') {
                $head['styles'][]=array('style'=>'/css/leads/leadsview.css');
                $head['scripts'][]=array('src'=>'/js/leads/leadsview.js');
                $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['leadsview'] = $this->_prepare_leadsview($brand, $top_menu);
            } elseif ($row['item_link']=='#itemslistview') {
                $head['styles'][]=array('style'=>'/css/leads/itemslistview.css');
                $head['scripts'][]=array('src'=>'/js/leads/itemslistview.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['itemslistview'] = $this->_prepare_itemslistview($brand, $top_menu);
            } elseif ($row['item_link']=='#onlinequotesview') {
                $head['styles'][]=array('style'=>'/css/leads/onlinequotes.css');
                $head['scripts'][]=array('src'=>'/js/leads/onlinequotes.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['onlinequotesview'] = $this->_prepare_onlinequotesview($brand, $top_menu);
            } elseif ($row['item_link']=='#proofrequestsview') {
                $head['styles'][] = array('style' => '/css/art/requestlist.css');
                $head['scripts'][] = array('src' => '/js/art/requestlist.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['proofrequestsview'] = $this->_prepare_requestlist_view($brand, $top_menu);
            } elseif ($row['item_link']=='#questionsview') {
                $head['styles'][] = array('style' => '/css/leads/questionsview.css');
                $head['scripts'][] = array('src' => '/js/leads/questionsview.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands) == 0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['questionsview'] = $this->_prepare_questionslist_view($brand, $top_menu);
                // Custom shaped
            } elseif ($row['item_link']=='#customsbform') {
                $head['styles'][] = array('style' => '/css/leads/customsbform.css');
                $head['scripts'][] = array('src' => '/js/leads/customsbform.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands) == 0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['customsbformview'] = $this->_prepare_customsbform_view($brand, $top_menu);
            } elseif ($row['item_link']=='#checkoutattemptsview') {
                $head['styles'][]=array('style'=>'/css/leads/orderattempts.css');
                $head['scripts'][]=array('src'=>'/js/leads/orderattempts.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['checkoutattemptsview'] = $this->_prepare_attempts_view($brand, $top_menu);
            }
        }
        $content_view = $this->load->view('leads/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/leads/page.js');
        $head['styles'][] = array('style' => '/css/leads/leadspage.css');
        // Lead popup
        $head['styles'][] = array('style' => '/css/leads/lead_popup.css');
        $head['scripts'][] = array('src' => '/js/leads/lead_popup.js');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // Artwork popup
        $head['scripts'][]=array('src'=>'/js/artwork/artpopup.js');
        $head['styles'][]=array('style'=>'/css/artwork/artpopup.css');
        // Uploader
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        // File Download
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Searchable
        $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
        $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];

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
            $error='Empty Brand';
            // Posts
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $pagenum=ifset($postdata, 'offset',0);
                $limit=ifset($postdata, 'limit', 250);
                $sorttime=$this->input->post('sorttime');
                $sort=$sorttime;
                $offset=$pagenum*$limit;
                $options=[
                    'brand' => $brand,
                ];
                $search=ifset($postdata,'search');
                if (!empty($search)) {
                    $options['search']=$search;
                }
                $usrrepl=ifset($postdata, 'usrrepl');
                if (!empty($usrrepl)) {
                    $options['usrrepl']=$usrrepl;
                }
                $leadtype=ifset($postdata, 'leadtype');
                if (!empty($leadtype)) {
                    $options['lead_type']=$leadtype;
                }

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
        }
        show_404();
    }

    public function search_leads() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $options=[
                    'brand' => $brand,
                ];
                /* Post parameters */
                $search=ifset($postdata, 'search');
                if (!empty($search)) {
                    $options['search']=$search;
                }
                $usrrepl = ifset($postdata,'usrrepl');
                if (!empty($usrrepl)) {
                    $options['usrrepl']=$usrrepl;
                }
                $leadtype = ifset($postdata,'leadtype');
                if (!empty($leadtype)) {
                    $options['lead_type']=$leadtype;
                }
                $this->load->model('leads_model');
                $mdata['totalrec']=$this->leads_model->get_total_leads($options);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function leadsclosed_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $this->load->model('orders_model');
                $this->load->model('leads_model');
                $minlead=$this->leads_model->get_lead_mindate($brand);
                $minorder=$this->orders_model->get_order_mindate($brand);
                $out_options=[];
                $user_id = ifset($postdata,'user_id');
                $show_feature=ifset($postdata, 'showfeature',0);
                $options=array(
                    'startdate'=>($minlead>$minorder ? $minorder : $minlead),
                    'show_feature'=>$show_feature,
                    'brand' => $brand,
                );
                if (!empty($user_id)) {
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
                $out_options['brand'] = $brand;
                $mdata['content']=$this->load->view('leads/leads_closedata_view', $out_options, TRUE);
            }
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
                'brand' => $postdata['brand'],
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

    public function leadsclosed_totals() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $date = $this->input->post('date');
                $direction = $this->input->post('direction');
                $user_id = $this->input->post('user_id');
                // New start
                if ($direction == 'prev') {
                    $newdate = strtotime(date("Y-m-d", $date) . " +5 month");
                } else {
                    // $newdate=strtotime(date("Y-m-d", $date) . " -1 month");
                    $newdate = $date;
                }
                $total_options = [
                    'enddate' => $newdate,
                    'brand' => $brand,
                ];
                if ($user_id) {
                    $total_options['user_id'] = $user_id;
                }
                $this->load->model('leads_model');
                $rdattop = $this->leads_model->count_closed_totals($total_options);
                $rdat['prev'] = $rdattop['prev'];
                $rdat['next'] = $rdattop['next'];
                $rdat['data'] = $rdattop['data'];
                $rdat['dateend'] = $newdate;
                $rdat['datestart'] = $rdattop['datestart'];
                $mdata['content'] = $this->load->view('leads/leads_closetotal_view', $rdat, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function leadsclosed_cmporders() {
        $bgn=$this->input->get('bgn');
        $brand = $this->input->get('brand');
        $this->load->model('orders_model');
        $data=$this->orders_model->get_cmporders($bgn, $brand);
        $content=$this->load->view('leads/company_ordertotals_view', $data, TRUE);
        echo $content;
    }

    public function leadsclosed_yeartotals() {
        $brand = $this->input->get('brand');
        $this->load->model('leads_model');
        $leads=$this->leads_model->get_yearleads($brand);
        $content=$this->load->view('leads/lead_yeartotals_view', $leads, TRUE);
        echo $content;
    }

    public function leadsclosed_usrorders() {
        $bgn=$this->input->get('bgn');
        $user_id=$this->input->get('user');
        $brand = $this->input->get('brand');
        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
        $this->load->model('orders_model');
        $options=array(
            'begin'=>$bgn,
            'order_usr_repic'=>$user_id,
            'end'=>$end,
            'order_by'=>'o.order_num',
            'direct'=>'desc',
            'brand' => $brand,
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

    public function leadsclosed_usrleads() {
        $bgn=$this->input->get('bgn');
        $user_id=$this->input->get('user');
        $leadtype=$this->input->get('leadtype');
        $brand = $this->input->get('brand');
        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
        $options=array(
            'begin'=>$bgn,
            'end'=>$end,
            'user_id'=>$user_id,
            'leadtype'=>$leadtype,
            'brand' => $brand,
        );
        $this->load->model('leads_model');
        $leads=$this->leads_model->get_newleads($options);
        $label=date('M d',$bgn).'-';
        if (date('m', $bgn)!=date('m',$end)) {
            $label.=date('M d',$end);
        } else {
            $label.=date('d', $end);
        }
        $label.=','.date('Y', $end);

        $data=array(
            'total'=>count($leads),
            'leads'=>$leads,
            'label'=>$label,
        );
        $content=$this->load->view('leads/lead_userleadstotal_view', $data, TRUE);
        echo $content;
    }

    public function leadsclosed_companyleads() {
        $bgn=$this->input->get('bgn');
        $brand = $this->input->get('brand');
        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
        $options=array(
            'begin'=>$bgn,
            'end'=>$end,
            'brand' => $brand,
        );
        $this->load->model('leads_model');
        $leads=$this->leads_model->get_company_leads($options);
        // Label
        $label=date('M d',$bgn).'-';
        if (date('m', $bgn)!=date('m',$end)) {
            $label.=date('M d',$end);
        } else {
            $label.=date('d', $end);
        }
        $label.=','.date('Y', $end);
        $data=array(
            'label'=>$label,
            'totals'=>$leads['totals'],
            'leads'=>$leads['usrdata'],
        );
        $content=$this->load->view('leads/lead_companyleadstotal_view', $data, TRUE);
        echo $content;
    }

    // Items lists
    public function itemslist_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $this->load->model('items_model');
            $postdata=$this->input->post();
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $limit=(isset($postdata['limit']) ? $postdata['limit'] : 150);
                $offset=(isset($postdata['offset']) ? $postdata['offset']*$limit : 0);
                $options=array(
                    'offset'=>$offset,
                    'limit'=>$limit,
                    'order_by'=>'item_number',
                    'prices'=>$this->config->item('normal_price_base'),
                    'brand' => $brand,
                );
                if (isset($postdata['search']) && !empty($postdata['search'])) {
                    $options['search']=strtoupper($postdata['search']);
                }
                if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                    $options['vendor_id']=$postdata['vendor_id'];
                }
                if (isset($postdata['priority']) && !empty($postdata['priority'])) {
                    $options['priority']=$postdata['priority'];
                }
                // Manage other parameters
                $items=$this->items_model->get_leaditems_data($options);
                if (count($items)==0) {
                    // Empty Content
                    $mdata['content']=$this->load->view('leads/itemslist_emptydata_view', array(), TRUE);
                } else {
                    $outoptions=array(
                        'data'=>$items,
                        'prices'=>$this->config->item('normal_price_base'),
                    );
                    $mdata['content']=$this->load->view('leads/itemslist_tabledata_view', $outoptions, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    // Count Items
    public function leaditems_count() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('items_model');
            $error='Empty Brand';
            $postdata=$this->input->post();
            $brand= ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $options=[];
                if ($brand!=='ALL') {
                    $options['brand']=$brand;
                }
                if (isset($postdata['search']) && !empty($postdata['search'])) {
                    $options['search']=strtoupper($postdata['search']);
                }
                if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                    $options['vendor_id']=$postdata['vendor_id'];
                }
                $mdata['total']=$this->items_model->count_lead_items($options);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Online Quotes
    // Table data
    public function quotesdat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();

            $pagenum=ifset($postdata, 'offset',0);
            $limit=ifset($postdata, 'limit',150);
            $offset=$pagenum*$limit;
            $order_by=ifset($postdata, 'order_by');
            $direct = ifset($postdata, 'direction','desc');
            $maxval=ifset($postdata, 'maxval');
            $brand=ifset($postdata, 'brand');
            $search_val=$this->input->post('search');
            $assign=$this->input->post('assign');
            $hideincl=$this->input->post('hideincl');
            $search=array();
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($search_val) {
                $search['search']=$search_val;
            }
            if (!empty($brand) && $brand!='ALL') {
                $search['brand']=$brand;
            }
            if ($hideincl) {
                $search['hideincl']=$hideincl;
            }

            /* Get data about Competitor prices */
            /*if ($ordoffset>$maxval) {
                $ordnum = $maxval;
            } else {
                $ordnum = $maxval - $ordoffset;
            }*/
            $this->load->model('quotes_model');
            $email_dat=$this->quotes_model->get_quotes($search,$order_by,$direct,$limit,$offset,$maxval);

            if (count($email_dat)==0) {
                $content = $this->load->view('leads/quotes_emptytabdata_view',array(), TRUE);
            } else {
                $data=array('email_dat'=>$email_dat,);
                $content = $this->load->view('leads/quotes_tabledat_view',$data, TRUE);

            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function quotecount() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $assign=$this->input->post('assign');
            $search_val=$this->input->post('search');
            $brand=$this->input->post('brand');
            $hideincl=$this->input->post('hideincl');
            $search=array();
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($search_val) {
                $search['search']=$search_val;
            }
            if ($brand) {
                $search['brand']=$brand;
            }
            if ($hideincl) {
                $search['hideincl']=$hideincl;
            }
            $this->load->model('quotes_model');
            $mdata['total_rec']=$this->quotes_model->get_count_quotes($search);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function quote_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $quote_id=$this->input->post('quote_id');
            /* Get Data */
            $this->load->model('quotes_model');
            $res = $this->quotes_model->get_quote_dat($quote_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $data = $res['data'];
                $mdata['content']=  $this->load->view('leads/quote_details_view',$data,TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Show Quote Details from Lead Form */
    function show_quote_detail() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('quotes_model');
            $quote_id=$this->input->post('quote_id');
            $res=$this->quotes_model->get_quote_dat($quote_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $quote = $res['data'];
                if ($quote['email_quota_link']) {
                    $mdata['url']=$quote['email_quota_link'];
                    $error='';
                } else {
                    $error='Quote in Process Stage';
                }
            }
            // $mdata['content']=$this->load->view('onlinequotes/details_view',$quote,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }


    public function change_status() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='This Request Related with Lead. Please, reload page';
            $postdata = $this->input->post();
            $type = ifset($postdata, 'type','');
            if (!empty($type)) {
                $this->load->model('leads_model');
                if ($type=='CustomQuote') {
                    $customform = ifset($postdata, 'customform', 0);
                    if ($customform > 0) {
                        $this->load->model('customform_model');
                        $data = $this->customform_model->get_customform_details($customform);
                        $error = $data['msg'];
                        if ($data['result']==$this->success_result) {
                            $error = '';
                            $quotadata = $data['data'];
                            $leadoptions=array(
                                'orderby'=>'lead_number',
                                'direction'=>'desc',
                            );
                            $leaddat=$this->leads_model->get_lead_list($leadoptions);
                            $options=array('leads'=>$leaddat,'current'=>$quotadata['lead_id'],'title' => 'Custom SB Form');
                            $quotadata['leadselect']=$this->load->view('artrequest/lead_openlist_view',$options,TRUE);
                            $mdata['content']=$this->load->view('customsbforms/update_status_view',$quotadata,TRUE);
                        }
                    }
                } else {
                    $this->load->model('questions_model');
                    $quest_id=$this->input->post('quest_id');

                    $chkrel=$this->leads_model->check_leadrelation($quest_id);
                    if ($chkrel==0) {
                        /* Get data about question */
                        $res = $this->questions_model->get_quest_data($quest_id);
                        $error = $res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            $quest= $res['data'];
                            /* Get open leads  */
                            $options=array(
                                'orderby'=>'lead_number',
                                'direction'=>'desc',
                            );
                            $leaddat=$this->leads_model->get_lead_list($options);
                            $options=array('leads'=>$leaddat,'current'=>$quest['lead_id']);
                            switch ($type) {
                                case 'quote':
                                    $options['title']='Quote Details';
                                    break;
                                case 'question':
                                    $options['title']='Question Details';
                                    break;
                                case 'proof':
                                    $options['title']='Proof Details';
                                    break;
                                default:
                                    $options['title']='Message Details';
                                    break;
                            }
                            $quest['leadselect']=$this->load->view('artrequest/lead_openlist_view',$options,TRUE);
                            $mdata['content']=$this->load->view('artrequest/update_status_view',$quest,TRUE);

                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function create_leadmessage() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata = $this->input->post();
            $this->load->model('leads_model');
            if (ifset($postdata,'type', '')!=='') {
                if ($postdata['type']=='CustomQuote') {
                    $customquote = ifset($postdata,'customquote', 0);
                    $leademail_id = ifset($postdata,'leademail_id',0);
                    $error = 'Empty Custom SB Form';
                    if ($customquote > 0) {
                        $error='This Request Related with Lead. Please, reload page';
                        $chkrel=$this->leads_model->check_leadquoterelation($customquote);
                        if ($chkrel==0) {
                            $this->load->model('customform_model');
                            $res = $this->customform_model->get_customform_details($customquote);
                            $error = $res['msg'];
                            if ($res['result']==$this->success_result) {
                                $formdata = $res['data'];
                                if (isset($res['attach']) && count($res['attach']) > 0) {
                                    $formdata['attach'] = $res['attach'];
                                }
                                $dat = $this->leads_model->create_leadcustomform($formdata, $leademail_id, $this->USR_ID);
                                $error = $dat['msg'];
                                if ($dat['result']==$this->success_result) {
                                    $error = '';
                                    $mdata['leadid'] = $dat['lead_id'];
                                }
                            }
                        }
                    }
                } else {
                    $error='This Request Related with Lead. Please, reload page';
                    $email_id=$this->input->post('mail_id');
                    $leademail_id=$this->input->post('leademail_id');
                    $type=$this->input->post('type');
                    $chkrel=$this->leads_model->check_leadrelation($email_id);
                    if ($chkrel==0) {
                        switch ($type) {
                            case 'Question':
                                $this->load->model('questions_model');
                                $maildat = $this->questions_model->get_quest_data($email_id);
                                $res = $this->leads_model->create_leadquest($maildat['data'], $leademail_id, $this->USR_ID);
                                break;
                            case 'Quote':
                                $this->load->model('quotes_model');
                                $maildat = $this->quotes_model->get_quote_dat($email_id);
                                $res['msg'] = $maildat['msg'];
                                if ($maildat['result']==$this->success_result) {
                                    $res = $this->leads_model->create_leadquote($maildat['data'], $leademail_id, $this->USR_ID);
                                }
                                break;
                            case 'Proof';
                                $this->load->model('artproof_model');
                                $maildat = $this->artproof_model->get_proof_data($email_id);
                                $res = $this->leads_model->create_leadproof($maildat, $leademail_id, $this->USR_ID);
                                break;
                            default:
                                break;
                        }
                        $error = $res['msg'];
                        if ($res['result'] != $this->error_result) {
                            $error = '';
                            $mdata['leadid'] = $res['result'];
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function quote_include() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Unknown Quote';
            $postdata = $this->input->post();
            $quote_id = ifset($postdata, 'quote_id',0);
            if ($quote_id > 0) {
                $this->load->model('quotes_model');
                $res=$this->quotes_model->get_quote_dat($quote_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $quote = $res['data'];
                    $newval=($quote['email_include_lead']==1 ? 0 : 1 );
                    $updres=$this->quotes_model->quote_include($quote_id,$newval);
                    $error=$updres['msg'];
                    if ($updres['result']==$this->success_result) {
                        $error = '';
                        $mdata['newicon']=$updres['newicon'];
                        $mdata['newclass']=$updres['newclass'];
                        $mdata['newmsg']=$updres['newmsg'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function questcount() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $assign=$this->input->post('assign');
            $search_val=$this->input->post('search');
            $brand=$this->input->post('brand');
            $hideincl=$this->input->post('hideincl');

            $search=array();
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($hideincl) {
                $search['hideincl']=$hideincl;
            }
            if ($search_val) {
                $search['search']=$search_val;
            }
            if ($brand) {
                $search['brand']=$brand;
            }
            $this->load->model('questions_model');
            $mdata['total_rec']=$this->questions_model->get_count_questions($search);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function questionsdat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            /* 'search':search, 'offset':page_index,'limit':perpage,'maxval':maxval */
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            $searchval=$this->input->post('search','');
            $maxval=$this->input->post('maxval');
            $assign=$this->input->post('assign');
            $brand=$this->input->post('brand');
            $hideincl=$this->input->post('hideincl');
            $search=array();
            if ($searchval) {
                $search['search']=$searchval;
            }
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($brand) {
                $search['brand']=$brand;
            }
            if ($hideincl) {
                $search['hideincl']=$hideincl;
            }

            $offset=$offset*$limit;

            /* Fetch data about prices */
            $this->load->model('questions_model');
            $questdat=$this->questions_model->get_questions($search,$order_by,$direct,$limit,$offset,$maxval);

            if (count($questdat)==0) {
                $mdata['content']=$this->load->view('leads/questions_emptytabledat_view',array(),TRUE);
            } else {
                $data=array(
                    'quests'=>$questdat,
                );
                $mdata['content']=$this->load->view('leads/questions_tabledat_view',$data, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function question_detail() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $quest_id=$this->input->post('quest_id');
            /* Get data about question */
            $this->load->model('questions_model');
            $res=$this->questions_model->get_quest_data($quest_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $quest=$res['data'];
                $mdata['content']=$this->load->view('leads/questions_details_view',$quest,TRUE);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Attempts
    public function attempts_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error='';
                $this->load->model('orders_model');
                // Temp
                $d_bgn = strtotime('2019-06-01');
                $datz=$this->orders_model->attempts_table_dat($brand, $d_bgn);
                $mdata['content']=$this->load->view('leads/order_attemptsdata_view',array('tabledat'=>$datz),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function attempts_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $date=$this->input->post('day');
            $brand = $this->input->post('brand');
            /* Get Attempts Results */
            $this->load->model('orders_model');
            $data=$this->orders_model->get_attemts_duedate($date, $brand);

            $options=array(
                'attempts'=>$data,
                'cnt'=>count($data),
                'date'=>$date,
            );
            $mdata['content']=$this->load->view('orders/order_attemtsday_view',$options, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /*Return log of art submit*/
    public function artsubmitlog()
    {
        $session_id = $this->input->get('d', 0);
        if ($session_id == 0) {
            echo '&nbsp;';
        }
        $this->load->model('orders_model');
        $log = $this->orders_model->get_artsubmitlog($session_id);
        if (count($log) > 0) {
            $content = $this->load->view('orders/artsubmitlog_view', array('data' => $log), TRUE);
        } else {
            $content = 'No ART log for this Attempt';
        }
        echo $content;
    }

    /* Data about new lead */
    public function change_leadrelation() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $lead_id=$this->input->post('lead_id');
            if (!$lead_id) {
                $error='Unknown Lead';
            } else {
                $this->load->model('leads_model');
                $leaddata=$this->leads_model->get_lead($lead_id);
                if (!isset($leaddata['lead_id'])) {
                    $error='Lead not found';
                } else {
                    $mdata['lead_date']=($leaddata['lead_date']==0 ? '' : 'Date: '.date('m/d/y',$leaddata['lead_date']));
                    $mdata['lead_customer']='Name: '.$leaddata['lead_customer'];
                    $mdata['lead_mail']='Email: '.$leaddata['lead_mail'];
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function savequeststatus() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $quest=$this->input->post();
            /* Get data about question */
            $this->load->model('leads_model');
            $res=$this->leads_model->save_leadrelation($quest);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $this->load->model('questions_model');
                $resquest=$this->questions_model->get_quest_data($quest['mail_id']);
                $error = $resquest['msg'];
                if ($resquest['result']==$this->success_result) {
                    $error = '';
                    $data = $resquest['data'];
                    $mdata['type']=$data['email_type'];
                }
                // Recalculate Totals New
//                $mdata['total_proof']=$this->mproofs->get_count_proofs(array('assign'=>1));
//                $mdata['total_quote']=$this->mquotes->get_count_quotes(array('assign'=>1));
//                $mdata['total_quest']=$this->mquests->get_count_questions(array('assign'=>1));
//                $mdata['sumquote']=$this->mquotes->get_todays();
//                $mdata['sumproofs']=$this->mproofs->get_todays();
//                $mdata['sumquest']=$this->mquests->get_todays();

            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function savecustomformstatus() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Custom SB Form ID';
            $postdata = $this->input->post();
            if (ifset($postdata,'customform',0) > 0 && ifset($postdata,'lead_id',0) > 0) {
                $this->load->model('customform_model');
                $dat = $this->customform_model->get_customform_details($postdata['customform']);
                $error = $dat['msg'];
                if ($dat['result']==$this->success_result) {
                    $this->load->model('leads_model');
                    if (isset($dat['attach']) && count($dat['attach']) > 0) {
                        $postdata['leadattach'] = $dat['attach'];
                    }
                    $res=$this->leads_model->save_quotelead_relation($postdata);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function customformsearch() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = '';
            $options = [];
            if (ifset($postdata, 'brand', '')!=='') {
                $options['brand'] = $postdata['brand'];
            }
            $this->load->model('customform_model');
            $mdata['totals'] = $this->customform_model->get_count_forms($options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function customformsdat() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = '';
            $postdata = $this->input->post();
            $this->load->model('customform_model');
            $data = $this->customform_model->get_customform_data($postdata);
            $event = 'hover'; // click
            if (count($data)==0) {
                $mdata['content'] = $this->load->view('customsbforms/content_empty_view',[],TRUE);
            } else {
                $mdata['content'] = $this->load->view('customsbforms/content_data_view',['data' => $data, 'event' => $event], TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function customformdmanage() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty Custom Form';
            $postdata = $this->input->post();
            $this->load->model('customform_model');
            if (ifset($postdata,'form_id',0) > 0) {
                $this->customform_model->update_customforn($postdata);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function customformdetail() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty Custom Form';
            $postdata = $this->input->post();
            $this->load->model('customform_model');
            if (ifset($postdata,'form_id',0) > 0) {
                $res = $this->customform_model->get_customform_details($postdata['form_id']);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $attachm_view = '';
                    if ($res['attach'] > 0) {
                        $attachm_view = $this->load->view('customsbforms/details_attached_view',['attachs' => $res['attach']], TRUE);
                    }
                    $options = [
                        'data' => $res['data'],
                        'attach' => $attachm_view,
                    ];
                    $mdata['content'] = $this->load->view('customsbforms/details_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
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
        $active = 0;
        $ldat['replicas']=$this->user_model->get_user_leadreplicas($active);
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

    private function _prepare_itemslistview($brand, $top_menu) {
        $datqs=array(
            'brand' => $brand,
            'top_menu' => $top_menu,
        );
        $this->load->model('vendors_model');
        $this->load->model('items_model');
        // Get list of vendors
        $v_options = [
            'order_by' => 'v.vendor_name',
        ];
        $datqs['vendors']=$this->vendors_model->get_vendors_list($v_options);
        $datqs['perpage']=$this->PERPAGE_LEADS;

        $item_options = [];
        if ($brand!=='ALL') {
            $item_options[]=['brand'=>$brand];
        }
        $datqs['total']=$this->items_model->count_lead_items($item_options);
        $datqs['cur_page']=0;
        $datqs['prices']=$this->config->item('normal_price_base');
        $content=$this->load->view('leads/itemslist_head_view',$datqs,TRUE);
        return $content;
    }

    private function _prepare_onlinequotesview($brand, $top_menu) {
        $datqs=array();
        $datqs=[
            'perpage' => $this->config->item('quotes_perpage'),
            'order_by' => 'email_date',
            'direction' => 'desc',
            'cur_page' => 0,
            'brand' => $brand,
        ];
        $search=array('assign'=>1,'brand'=>$brand);
        $this->load->model('quotes_model');
        $datqs['total_rec']=$this->quotes_model->get_count_quotes($search);
        $content=$this->load->view('leads/quotes_head_view',$datqs,TRUE);
        return $content;
    }

    private function _prepare_requestlist_view($brand, $top_menu) {
        $datqs = [
            'perpage' => $this->config->item('quotes_perpage'),
            'order_by' => 'email_date',
            'direction' => 'desc',
            'cur_page' => 0,
            'assign' => '',
            'hideart' => 0,
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];

        $search=array('assign'=>'','hideart'=>0, 'brand' => $brand);
        $this->load->model('artproof_model');
        $datqs['total_rec']=$this->artproof_model->get_count_proofs($search);
        $content=$this->load->view('artrequest/page_view',$datqs,TRUE);
        return $content;

    }

    private function _prepare_questionslist_view($brand, $top_menu) {
        $datqs=[
            'perpage' => $this->config->item('quotes_perpage'),
            'order_by' => 'email_date',
            'direction' => 'desc',
            'cur_page' => 0,
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        // $datqs['search_form']=$this->load->view('questions/search_form_view',array(),TRUE);

        $search=array('assign'=>1,'brand'=>$brand);
        $this->load->model('questions_model');
        $datqs['total_rec']=$this->questions_model->get_count_questions($search);

        $content=$this->load->view('leads/questions_view',$datqs,TRUE);
        return $content;

    }

    private function _prepare_attempts_view($brand, $top_menu) {
        $options = [
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        $content=$this->load->view('leads/order_attempts_view', $options,TRUE);
        return $content;
    }

    private function _prepare_customsbform_view($brand, $top_menu) {
        $datqs=[
            'perpage' => $this->config->item('quotes_perpage'),
            'order_by' => 'date_add',
            'direction' => 'desc',
            'cur_page' => 0,
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];

        $search=array('assign'=>1,'brand'=>$brand);
        $this->load->model('customform_model');
        $datqs['total_rec']=$this->customform_model->get_count_forms($search);

        $content=$this->load->view('customsbforms/customform_view.php',$datqs,TRUE);
        return $content;

    }

}