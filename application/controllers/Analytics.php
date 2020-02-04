<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics extends MY_Controller
{

    private $pagelink = '/analytics';
    private $PERPAGE = 100;
    private $perpage_options = [50, 100, 250, 500];

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
        $this->load->model('reports_model');
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Analytics';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#reportsalestypeview') {
                // Taks View
                $head['styles'][] = ['style' => '/css/analytics/salestypes.css'];
                $head['scripts'][] = ['src' => '/js/analytics/salestypes.js'];
                $content_options['reportsalestypeview'] = $this->_prepare_salestype_view();
            } elseif ($row['item_link']=='#reportitemsoldyearview') {
                $head['styles'][]=['style'=>'/css/analytics/itemsales.css'];
                $head['scripts'][]=['src'=>'/js/analytics/itemsales.js'];
                $content_options['reportitemsoldyearview'] = $this->_prepare_itemsales();
            } elseif ($row['item_link']=='#reportitemsoldmonthview') {
                $head['styles'][]=['style'=>'/css/analytics/itemmonth.css'];
                $head['scripts'][]=['src'=>'/js/analytics/itemmonth.js'];
                $content_options['reportitemsoldmonthview'] = '';
            } elseif ($row['item_link']=='#checkoutreportview') {
                $head['styles'][]=['style'=>'/css/analytics/orderreports.css'];
                $head['scripts'][]=['src'=>'/js/analytics/ordersreports.js'];
                $content_options['checkoutreportview']='';
            }
        }
        $content_options['menu']=$menu;

        $content_options['menu'] = $menu;
        $content_view = $this->load->view('analytics/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/analytics/page.js');
        $head['styles'][] = array('style' => '/css/analytics/analyticpage.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');

        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function salesmonthdiff() {
        $postdata=$this->input->get();
        if (isset($postdata['month']) || isset($postdata['year']) || isset($postdata['type'])) {
            $month=$postdata['month'];
            $year=$postdata['year'];
            $salestype=$postdata['type'];
            $usrdat=$this->user_model->get_user_data($this->USR_ID);
            $data=$this->reports_model->salesmonthdiff($month, $year, $salestype,$usrdat['profit_view']);
            $content=$this->load->view('reports/salestype_monthdiff_view', $data, TRUE);
            echo $content;
            return TRUE;
        }
        show_404();
    }

    public function salesgoal_editform() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $goal_order_id=$this->input->post('goal');
            // Get Goal Content
            $goaldata=$this->reports_model->get_sales_goaldata($goal_order_id);
            // Save to Session
            usersession('goaldata', $goaldata);
            $mdata['content']=$this->load->view('finance/goal_edit_view',$goaldata,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function salesgoal_changeparam() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // Restore session
            $goaldata=usersession('goaldata');
            if (empty($goaldata)) {
                $error='Connection Lost. Please, recall function';
            } else {
                $field=$this->input->post('field');
                $newval=$this->input->post('newval');
                $this->load->model('orders_model');
                $res=$this->orders_model->change_goal_value($goaldata, $field, $newval);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $mdata['goalavgrevenue']=$res['goalavgrevenue'];
                    $mdata['goalavgprofit']=$res['goalavgprofit'];
                    $mdata['goalavgprofitperc']=$res['goalavgprofitperc'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function salesgoal_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // Restore session
            $goaldata=usersession('goaldata');
            if (empty($goaldata)) {
                $error='Connection Lost. Please, recall function';
            } else {
                $this->load->model('order_model');
                $res=$this->order_model->save_profitdate_goal($goaldata);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $dates=$this->reports_model->get_bisness_dates();
                    switch ($goaldata['goal_type']) {
                        case 'CUSTOMS':
                            $data=$this->reports_model->get_newcustoms_salestypes($dates);
                            $mdata['area']='salestype_customs';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalescustoms');
                            break;
                        case 'STOCK':
                            $data=$this->reports_model->get_newstock_salestypes($dates);
                            $mdata['area']='salestype_stock';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalesstock');
                            break;
                        case 'ARIEL':
                            $data=$this->reports_model->get_newariel_salestypes($dates);
                            $mdata['area']='salestype_ariel';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalesariel');
                            break;
                        case 'ALPI':
                            $data=$this->reports_model->get_newalpi_salestypes($dates);
                            $mdata['area']='salestype_alpi';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalesalpi');
                            break;
                        case 'MAILINE':
                            $data=$this->reports_model->get_newmailine_salestypes($dates);
                            $mdata['area']='salestype_mailine';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalesmailine');
                            break;
                        case 'ESP':
                            $data=$this->reports_model->get_newesp_salestypes($dates);
                            $mdata['area']='salestype_others';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalesesp');
                            break;
                        case 'HIT':
                            $data=$this->reports_model->get_newhit_salestypes($dates);
                            $mdata['area']='salestype_hits';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsaleshit');
                            break;
                        default :
                            // Other
                            $data=$this->reports_model->get_newother_salestypes($dates);
                            $mdata['area']='salestype_others';
                            $profitdat=$this->permissions_model->get_pageprofit_view($this->USR_ID,'itemsalesother');
                            break;
                    }
                    $data['profit_type']='';
                    if (count($profitdat)==1) {
                        $data['profit_type']=$profitdat[0]['profit_view'];
                    }
                    $data['elapsed']=$dates['elaps'];
                    $mdata['content']=$this->load->view('reports/current_data_view', $data, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Show month details
    public function sales_month_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $month=$postdata['month'];
            $year=$postdata['year'];
            if (isset($postdata['saletype'])) {
                $salestype=$postdata['saletype'];
                $res=$this->reports_model->get_monthsales_details($month, $year, $salestype, $this->USR_ID);
                $qtyview=0;
            } else {
                $item_id=$postdata['item'];
                $res=$this->reports_model->get_monthsales_itemdetails($month, $year, $item_id);
                $qtyview=1;
            }
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $options=array(
                    'data'=>$res['data'],
                    'totals'=>$res['totals'],
                    'title'=>$res['title'],
                );
                if ($qtyview==0) {
                    $options['profit_type']=$res['profit_type'];
                    $mdata['content']=$this->load->view('reports/salesmonth_details_view', $options, TRUE);
                } else {
                    $mdata['content']=$this->load->view('reports/itemdata_details_view', $options, TRUE);
                }
                $mdata['countdata']=count($res['data']);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Show difference section in type
    public function salestype_showdifference() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty parameters for compare';
            $postdata = $this->input->post();
            if (isset($postdata['type']) && isset($postdata['profit']) && isset($postdata['compare']) && isset($postdata['to'])) {
                $error = 'Error in select years for compare';
                $type = $postdata['type'];
                $profit_type = $postdata['profit'];
                $diffYearBgn = intval($postdata['compare']);
                $diffYearEnd = intval($postdata['to']);
                if ($diffYearEnd > $diffYearBgn) {
                    $error = '';
                    $custom_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, $type);
                    $sales_years = $this->reports_model->get_report_years();
                    $yearDifffrom = $this->_prepare_comparefrom_select($sales_years['start'], $sales_years['finish']);
                    $yearDiffTo = $this->_prepare_compareto_select($sales_years['start'], $sales_years['finish']);
                    $diffoptions = [
                        'months' => $custom_diffs['months'],
                        'quarters' => $custom_diffs['quarters'],
                        'years_from' => $yearDifffrom,
                        'years_to' => $yearDiffTo,
                        'type' => $type,
                        'year_from' => $diffYearBgn,
                        'year_to' => $diffYearEnd,
                        'profit_type' => $profit_type,
                    ];
                    $mdata['content'] = $this->load->view('reports/differences_view', $diffoptions, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showdifference_calc() {
        $postdata = $this->input->get();
        // echo var_dump($postdata); die();
        $quarter=(isset($postdata['q']) ? $postdata['q'] : 1);
        $yearBgn = (isset($postdata['start']) ? $postdata['start'] : date('Y')-1);
        $yearEnd = (isset($postdata['finish']) ? $postdata['finish'] : date('Y'));
        $salestype = (isset($postdata['type']) ? $postdata['type'] : 'test');
        $profit_type = (isset($postdata['view']) ? $postdata['view'] : 'Points');
        // Get data
        $res = $this->reports_model->get_differences_details($quarter, $yearBgn, $yearEnd, $salestype, $profit_type);
        $options = [
            'qt' => $quarter,
            'start' => $yearBgn,
            'finish' => $yearEnd,
            'startval' => $res['prvval'],
            'finishval' => $res['curval'],
            'profit' => $profit_type,
        ];
        echo $this->load->view('reports/diffdetails_view', $options, TRUE);
    }

    // Item Sales - Year
    // Item Sales page
    public function itemsalesdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $limit=(isset($postdata['limit']) ? $postdata['limit'] : $this->PERPAGE);
            $start=(isset($postdata['offset']) ? $postdata['offset'] : 0);
            $offset=$start*$limit;
            $orderby=(isset($postdata['order_by']) ? $postdata['order_by'] : 'curyearqty');
            $vendor=(isset($postdata['vendor']) ? $postdata['vendor'] : '');

            $options=array(
                'limit'=>$limit,
                'offset'=>$offset,
                'orderby'=>$orderby,
                'vendor'=>$vendor,
                'current_year'=>$postdata['current_year'],
                'prev_year'=>$postdata['prev_year'],
                'calc_year'=>(isset($postdata['calc_year']) ? $postdata['calc_year'] : $postdata['current_year']),
                'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
            );
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }
            $res=$this->reports_model->itemsale_data($options);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $itemchk=usersession('itemsaleschk');
                $voptions=array(
                    'data'=>$res['data'],
                    'curyear'=>$postdata['current_year'],
                    'prevyear'=>$postdata['prev_year'],
                    'itemchk'=>$itemchk,
                );
                $mdata['content']=$this->load->view('reports/itemsales_data_view', $voptions, TRUE);
                $mdata['addcost']=$this->reports_model->get_addcost();
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Count # of records for show in Item Sale Report
    public function itemsalessearch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $vendor=(isset($postdata['vendor']) ? $postdata['vendor'] : '');
            $options=array(
                'curentyear'=>$postdata['current_year'],
                'prevyear'=>$postdata['prev_year'],
                'vendor'=>$vendor,
                'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
            );
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }
            $mdata['totals']=$this->reports_model->itemsales_totals($options);
            // Build Totals
            $itemchk=usersession('itemsaleschk');
            $mdata['chktotals']=0;
            if (empty($itemchk)) {
            } else {
                $toptions=array(
                    'checked'=>$itemchk,
                    'current_year'=>$postdata['current_year'],
                    'prev_year'=>$postdata['prev_year'],
                    'calc_year'=>$postdata['calc_year'],
                    'vendor'=>$vendor,
                    'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
                );
                if (isset($postdata['search']) && !empty($postdata['search'])) {
                    $toptions['search']=strtoupper($postdata['search']);
                }
                $totals=$this->reports_model->_get_totalcheck($toptions);
                if (!empty($totals)) {
                    $mdata['chktotals']=count($itemchk);
                    $mdata['totalview']=$this->load->view('reports/itemsales_totals_view', $totals, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Show image
    public function itemimage() {
        $item_id=$this->input->get('item');
        $res=$this->reports_model->get_item_mainimage($item_id);
        if ($res['result']==$this->success_result) {
            $content=$this->load->view('reports/itemimage_view', $res, TRUE);
            echo $content;
        }
    }


    // Change Base Year
    public function itemsales_baseyear() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $contoptions=array(
                'baseyear'=>(isset($postdata['baseyear']) ? $postdata['baseyear'] : intval(date('Y'))),
                'limit'=>(isset($postdata['limit']) ? $postdata['limit'] : $this->PERPAGE),
                'vendor'=>(isset($postdata['vendor']) ? $postdata['vendor'] : ''),
                'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
                'order_by'=>(isset($postdata['order_by']) ? $postdata['order_by'] : 'curyearqty'),
            );
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $contoptions['search']=$postdata['search'];
            }
            // Try to build content
            $mdata['content']=$this->_prepare_itemsales($contoptions);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function sales_year_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();

            $year=$postdata['year'];
            $item_id=$postdata['item'];
            $month=0;
            $res=$this->reports_model->get_monthsales_itemdetails($month, $year, $item_id);

            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $options=array(
                    'data'=>$res['data'],
                    'totals'=>$res['totals'],
                    'title'=>$res['title'],
                );
                $mdata['content']=$this->load->view('reports/itemdata_details_view', $options, TRUE);

                $mdata['countdata']=count($res['data']);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsales_checkitem() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $item_id=$postdata['item'];
            $chkres=$postdata['check'];
            // Change Check
            $totalchk=$this->reports_model->itemsale_change_itemcheck($item_id, $chkres);
            $mdata['totals']=$totalchk;
            if ($totalchk>0) {
                $itemchk=$this->func->session('itemsaleschk');
                $options=array(
                    'current_year'=>$postdata['current_year'],
                    'prev_year'=>$postdata['prev_year'],
                    'calc_year'=>$postdata['calc_year'],
                    'checked'=>$itemchk,
                    'vendor'=>$postdata['vendor'],
                    'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
                );
                if (isset($postdata['search']) && !empty($postdata['search'])) {
                    $options['search']=$postdata['search'];
                }
                $res=$this->reports_model->_get_totalcheck($options);
                if (empty($res)) {
                    $mdata['totals']=0;
                } else {
                    $mdata['totalview']=$this->load->view('reports/itemsales_totals_view', $res, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Edit value
    public function itemsalesedit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // Get Item values
            $postdata=$this->input->post();
            if (!array_key_exists('item', $postdata) || !array_key_exists('year', $postdata)) {
                $error='Fill all Parameters';
            } else {
                $item_id=$postdata['item'];
                $year=$postdata['year'];
                $item_cost=$this->reports_model->get_itemimport_cost($item_id, $year);
                $mdata['editcontent']=$this->load->view('reports/itemsale_imptedit_view', array('cost'=>$item_cost), TRUE);
                $mdata['savecontent']=$this->load->view('reports/itemsale_imptsave_view', array(), TRUE);
                $mdata['cancelcontent']=$this->load->view('reports/itemsale_imptcancel_view', array(), TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsalesgetrow() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $item_id=$postdata['item'];
            $year=$postdata['year'];
            $curentyear=$postdata['current_year'];
            $prevyear=$postdata['prev_year'];
            $itemchk=usersession('itemsaleschk');
            $options=array(
                'item_id'=>$item_id,
                'current_year'=>$curentyear,
                'prev_year'=>$prevyear,
                'calc_year'=>$year,
                'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
            );
            $res=$this->reports_model->itemsale_data($options);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $data=$res['data'][0];
                $data['itemchk']=$itemchk;
                $mdata['content']=$this->load->view('reports/itemsales_row_view', $data, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Save Import Cost
    public function itemsalessave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $vendor=(isset($postdata['vendor']) ? $postdata['vendor'] : '');
            $options=array(
                'item_id'=>$postdata['item'],
                'current_year'=>$postdata['current_year'],
                'prev_year'=>$postdata['prev_year'],
                'calc_year'=>$postdata['calc_year'],
                'vendor'=>$vendor,
                'cost'=>$postdata['cost'],
                'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
            );
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }

            $res=$this->reports_model->save_itemimport_cost($options);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $data=$res['data'];
                $itemchk=usersession('itemsaleschk');
                $data['itemchk']=$itemchk;
                $mdata['content']=$this->load->view('reports/itemsales_row_view', $data, TRUE);
                $mdata['totals']=count($itemchk);
                if (count($itemchk)>0) {
                    $options=array(
                        'current_year'=>$postdata['current_year'],
                        'prev_year'=>$postdata['prev_year'],
                        'calc_year'=>$postdata['calc_year'],
                        'checked'=>$itemchk,
                        'vendor'=>$postdata['vendor'],
                        'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
                    );
                    if (isset($postdata['search']) && !empty($postdata['search'])) {
                        $options['search']=$postdata['search'];
                    }
                    $res=$this->reports_model->_get_totalcheck($options);
                    if (empty($res)) {
                        $mdata['totals']=0;
                    } else {
                        $mdata['totalview']=$this->load->view('reports/itemsales_totals_view', $res, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }




    // Mass check Items
    public function itemsales_masscheck() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            if ($postdata['check']==0) {
                usersession('itemsaleschk', array());
            } else {
                // Add All items
                $chkoptions=array(
                    'current_year'=>$postdata['current_year'],
                    'prev_year'=>$postdata['prev_year'],
                );
                $this->reports_model->itemsales_checkitem($chkoptions);
                $itemchk=usersession('itemsaleschk');
                $mdata['totals']=count($itemchk);
                $options=array(
                    'current_year'=>$postdata['current_year'],
                    'prev_year'=>$postdata['prev_year'],
                    'calc_year'=>$postdata['calc_year'],
                    'checked'=>$itemchk,
                    'vendor'=>$postdata['vendor'],
                    'vendor_cost'=>(isset($postdata['vendor_cost']) ? $postdata['vendor_cost'] : 'high'),
                );
                if (isset($postdata['search']) && !empty($postdata['search'])) {
                    $options['search']=$postdata['search'];
                }
                $res=$this->reports_model->_get_totalcheck($options);
                if (empty($res)) {
                    $mdata['totals']=0;
                } else {
                    $mdata['totalview']=$this->load->view('reports/itemsales_totals_view', $res, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    private function _prepare_salestype_view() {
        $this->load->model('permissions_model');
        $usrdat=$this->user_model->get_user_data($this->USR_ID);
        $reppermis=$this->permissions_model->get_subitems($this->USR_ID, 'salestypebtn');
        $profitview=$this->permissions_model->get_pageprofit_view($this->USR_ID, 'salestypebtn');
        $usr_profitview = $usrdat['profit_view'];
        $olddata=$this->reports_model->get_old_salestypes($reppermis, $profitview, $usr_profitview);
        $dates=$this->reports_model->get_bisness_dates();
        $sales_years = $this->reports_model->get_report_years();
        $yearDifffrom = $this->_prepare_comparefrom_select($sales_years['start'], $sales_years['finish']);
        $yearDiffTo = $this->_prepare_compareto_select($sales_years['start'], $sales_years['finish']);
        $customs_view=$stocks_view=$ariel_view=$alpi_view=$mailine_view=$esp_view=$hit_view=$other_view='';
        $diffYearEnd=intval(date('Y'));
        $diffYearBgn=$diffYearEnd-1;
        // Custom View
        if (in_array('itemsalescustoms', $reppermis)) {
            $profit_type=$usr_profitview;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalescustoms') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $dates['profit_type']=$profit_type;
            $newcustoms=$this->reports_model->get_newcustoms_salestypes($dates, $olddata['customs']);
            $custom_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'customs');
            $diffoptions = [
                'months' => $custom_diffs['months'],
                'quarters' => $custom_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'customs',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $custom_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newcustoms['elapsed']=$dates['elaps'];
            $newcustoms['profit_type']=$profit_type;
            $newcustoms['differences'] = $custom_diffview;
            $newcustoms['type'] = 'customs';
            $newcustom_view=$this->load->view('reports/current_customdata_view', $newcustoms, TRUE);
            $custom_options=array(
                'data'=>$olddata['customs'],
                'curview'=>$newcustom_view,
                'newlabel'=>'customs',
                'profit_type'=>$profit_type,
            );
            $customs_view=$this->load->view('reports/customitems_data_view', $custom_options, TRUE);
        }
        // Stock View
        if (in_array('itemsalesstock', $reppermis)) {
            $profit_type=$usr_profitview;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalesstock') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $dates['profit_type']=$profit_type;
            $newstock=$this->reports_model->get_newstock_salestypes($dates, $olddata['stocks']);
            $stock_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'stock');
            $diffoptions = [
                'months' => $stock_diffs['months'],
                'quarters' => $stock_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'stock',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $stock_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newstock['elapsed']=$dates['elaps'];
            $newstock['profit_type']=$profit_type;
            $newstock['differences'] = $stock_diffview;
            $newstock['type'] = 'stock';
            $newstock_view=$this->load->view('reports/current_data_view', $newstock, TRUE);
            $stock_options=array(
                'data'=>$olddata['stocks'],
                'curview'=>$newstock_view,
                'newlabel'=>'stock',
                'profit_type'=>$profit_type,
            );
            $stocks_view=$this->load->view('reports/customs_data_view', $stock_options, TRUE);
        }
        // Ariel View
        if (in_array('itemsalesariel', $reppermis)) {
            $dates['profit_type']=$profit_type;
            $profit_type=$usr_profitview;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalesariel') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $newariel=$this->reports_model->get_newariel_salestypes($dates, $olddata['ariel']);
            $ariel_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'ariel');
            $diffoptions = [
                'months' => $ariel_diffs['months'],
                'quarters' => $ariel_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'ariel',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $ariel_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newariel['elapsed']=$dates['elaps'];
            $newariel['profit_type']=$profit_type;
            $newariel['differences'] = $ariel_diffview;
            $newariel['type'] = 'ariel';
            $newariel_view=$this->load->view('reports/current_data_view', $newariel, TRUE);
            $ariel_options=array(
                'data'=>$olddata['ariel'],
                'curview'=>$newariel_view,
                'newlabel'=>'ariel',
            );
            $ariel_view=$this->load->view('reports/customs_data_view', $ariel_options, TRUE);
        }
        // Alpi View
        if (in_array('itemsalesalpi', $reppermis)) {
            $dates['profit_type']=$profit_type;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalesalpi') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $newalpi=$this->reports_model->get_newalpi_salestypes($dates, $olddata['alpi']);
            $alpi_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'alpi');
            $diffoptions = [
                'months' => $alpi_diffs['months'],
                'quarters' => $alpi_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'alpi',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $alpi_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newalpi['elapsed']=$dates['elaps'];
            $newalpi['profit_type']=$profit_type;
            $newalpi['differences'] = $alpi_diffview;
            $newalpi['type'] = 'alpi';
            $newalpi_view=$this->load->view('reports/current_data_view', $newalpi, TRUE);
            $alpi_options=array(
                'data'=>$olddata['alpi'],
                'curview'=>$newalpi_view,
                'newlabel'=>'alpi',
            );
            $alpi_view=$this->load->view('reports/customs_data_view', $alpi_options, TRUE);
        }
        // Mailine Views
        if (in_array('itemsalesmailine', $reppermis)) {
            $dates['profit_type']=$profit_type;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalesmailine') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $newmailine=$this->reports_model->get_newmailine_salestypes($dates, $olddata['mailine']);
            $mailine_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'mailine');
            $diffoptions = [
                'months' => $mailine_diffs['months'],
                'quarters' => $mailine_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'mailine',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $mailine_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newmailine['elapsed']=$dates['elaps'];
            $newmailine['profit_type']=$profit_type;
            $newmailine['differences'] = $mailine_diffview;
            $newmailine['type'] = 'mailine';
            $newmailine_view=$this->load->view('reports/current_data_view', $newmailine, TRUE);
            $mailine_options=array(
                'data'=>$olddata['mailine'],
                'curview'=>$newmailine_view,
                'newlabel'=>'mailine',
            );
            $mailine_view=$this->load->view('reports/customs_data_view', $mailine_options, TRUE);
        }
        // ESP / Other View
        if (in_array('itemsalesesp', $reppermis)) {
            $dates['profit_type']=$profit_type;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalesesp') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $newesp=$this->reports_model->get_newesp_salestypes($dates, $olddata['esp']);
            $esp_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'esp');
            $diffoptions = [
                'months' => $esp_diffs['months'],
                'quarters' => $esp_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'esp',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $esp_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newesp['elapsed']=$dates['elaps'];
            $newesp['profit_type']=$profit_type;
            $newesp['differences'] = $esp_diffview;
            $newesp['type'] = 'esp';
            $newesp_view=$this->load->view('reports/current_data_view', $newesp, TRUE);
            $esp_options=array(
                'data'=>$olddata['esp'],
                'curview'=>$newesp_view,
                'newlabel'=>'esp',
            );
            $esp_view=$this->load->view('reports/customs_data_view', $esp_options, TRUE);
        }
        // Hit View
        if (in_array('itemsaleshit', $reppermis)) {
            $dates['profit_type']=$profit_type;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsaleshit') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $newhit=$this->reports_model->get_newhit_salestypes($dates, $olddata['hits']);
            $hit_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'hit');
            $diffoptions = [
                'months' => $hit_diffs['months'],
                'quarters' => $hit_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'hit',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $hit_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newhit['elapsed']=$dates['elaps'];
            $newhit['profit_type']=$profit_type;
            $newhit['differences'] = $hit_diffview;
            $newhit['type'] = 'hit';
            $newhit_view=$this->load->view('reports/current_data_view', $newhit, TRUE);
            $hit_options=array(
                'data'=>$olddata['hits'],
                'curview'=>$newhit_view,
                'newlabel'=>'hits',
            );
            $hit_view=$this->load->view('reports/customs_data_view', $hit_options, TRUE);
        }

        // Other Item View
        if (in_array('itemsalesother', $reppermis)) {
            $dates['profit_type']=$profit_type;
            foreach ($profitview as $prow) {
                if ($prow['websys_page_link']=='itemsalesother') {
                    $profit_type=$prow['profit_view'];
                }
            }
            $newother=$this->reports_model->get_newother_salestypes($dates, $olddata['others']);
            $other_diffs = $this->reports_model->get_difference($diffYearBgn, $diffYearEnd, $profit_type, 'other');
            $diffoptions = [
                'months' => $other_diffs['months'],
                'quarters' => $other_diffs['quarters'],
                'years_from' => $yearDifffrom,
                'years_to' => $yearDiffTo,
                'type' => 'other',
                'year_from' => $diffYearBgn,
                'year_to' => $diffYearEnd,
                'profit_type' =>$profit_type,
            ];
            $other_diffview = $this->load->view('reports/differences_view', $diffoptions, TRUE);
            unset($dates['profit_type']);
            $newother['elapsed']=$dates['elaps'];
            $newother['profit_type']=$profit_type;
            $newother['differences'] = $other_diffview;
            $newother['type'] = 'other';
            $newother_view=$this->load->view('reports/current_data_view', $newother, TRUE);

            $other_options=array(
                'data'=>$olddata['others'],
                'curview'=>$newother_view,
                'newlabel'=>'others',
            );
            $other_view=$this->load->view('reports/customs_data_view', $other_options, TRUE);
        }
        // Complete together
        $options=array(
            'customs_view'=>$customs_view,
            'stocks_view'=>$stocks_view,
            'ariel_view'=>$ariel_view,
            'alpi_view'=>$alpi_view,
            'mailine_view'=>$mailine_view,
            'other_view'=>$other_view,
            'hit_view'=>$hit_view,
            'esp_view'=>$esp_view,
        );
        $content=$this->load->view('reports/salestype_view', $options, TRUE);
        return $content;

    }

    private function _prepare_comparefrom_select($start, $finish) {
        $yearDiff =[];
        for ($i=0; $i<100; $i++) {
            if (($start+$i) >= $finish) {
                break;
            } else {
                array_push($yearDiff, ($start+$i));
            }
        }
        return $yearDiff;
    }

    private function _prepare_compareto_select($start, $finish) {
        $yearDiff =[];
        for ($i=0; $i<100; $i++) {
            if (($start+$i) > $finish) {
                break;
            } else {
                array_push($yearDiff, ($start+$i));
            }
        }
        return $yearDiff;
    }

    // Item Sales (Year)
    private function _prepare_itemsales($options=array()) {
        // Get current year
        // $current_year=0
        if (!isset($options['baseyear'])) {
            $current_year=0;
        } else {
            $current_year=$options['baseyear'];
        }
        if ($current_year==0) {
            $curyear=intval(date('Y'));
        } else {
            $curyear=intval($current_year);
        }
        $chkyear=intval(date('Y'))-2;
        $prvyear=$curyear-1;
        $selectyearcalcview='';
        if ($chkyear>=2014) {
            // Build show for select
            $yoptions=array(
                'start_year'=>intval(date('Y')),
                'current_year'=>$curyear,
            );
            $selectyearcalcview=$this->load->view('reports/selectcalcyear_view', $yoptions, TRUE);
        }
        // Default
        $defvenfor='';
        if (isset($options['vendor']) && !empty($options['vendor'])) {
            $defvenfor=$options['vendor'];
        }
        $cntoptions=array(
            'curentyear'=>$curyear,
            'prevyear'=>$prvyear,
            'vendor'=>$defvenfor,
        );
        if (isset($options['search'])) {
            $cntoptions['search']=strtoupper($options['search']);
        }

        $totals=$this->reports_model->itemsales_totals($cntoptions);

        $addcost=$this->reports_model->get_addcost();

        $voptions=array(
            'sort'=>(isset($options['order_by']) ? $options['order_by'] : 'curyearqty'),
            'curentyear'=>$curyear,
            'prevyear'=>$prvyear,
            'vendor'=>$defvenfor,
            'itmtotals'=>$totals,
            'vendors'=>$this->config->item('report_vendors'),
            'perpage'=>  $this->perpage_options,
            'currenrows'=>(isset($options['limit']) ? $options['limit'] : $this->PERPAGE),
            'addcost'=>$addcost,
            'selectyearshow'=>$selectyearcalcview,
            'search'=>(isset($options['search']) ? $options['search'] : ''),
            'vendor_cost'=>(isset($options['vendor_cost']) ? $options['vendor_cost'] : 'high'),
        );

        $voptions['totals']=0;

        $itemchk=usersession('itemsaleschk');

        if (!is_array($itemchk) || empty($itemchk)) {
            $itemchk=array();
        } else {
            // Get Totals for view
            $totaloptions=array(
                'current_year'=>$curyear,
                'prev_year'=>$prvyear,
                'calc_year'=>$curyear,
                'checked'=>$itemchk,
                'vendor'=>$defvenfor,
                'vendor_cost'=>(isset($options['vendor_cost']) ? $options['vendor_cost'] : 'high'),
            );
            if (isset($options['search']) && !empty($options['search'])) {
                $totaloptions['search']=$options['search'];
            }

            $res=$this->reports_model->_get_totalcheck($totaloptions);

            if (!empty($res)) {
                $voptions['totals']=1;
                $voptions['totalview']=$this->load->view('reports/itemsales_totals_view', $res, TRUE);
            }
        }

        $content=$this->load->view('reports/itemsales_head_view', $voptions, TRUE);
        // Prepare session for check
        usersession('itemsaleschk', $itemchk);
        return $content;
    }


}