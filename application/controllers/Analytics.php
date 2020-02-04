<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Analytics extends MY_Controller
{

    private $pagelink = '/analytics';

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
                $content_options['reportitemsoldyearview'] = '';
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

}