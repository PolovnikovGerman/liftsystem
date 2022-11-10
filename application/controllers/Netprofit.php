<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Netprofit extends MY_Controller
{
    private $restore_data_error = 'Edit Connection Lost. Please, recall form';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('balances_model');
    }

    public function netprofitdat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $radio=$this->input->post('radio');
            $datbgn=$this->config->item('netprofit_start');
            $order_by=$this->input->post('order_by');
            $limitshow=$this->input->post('limitshow');
            // $brand = $this->input->post('brand');
            $brand = 'ALL';
            $order='nd.datebgn';
            $direc='desc';
            switch ($order_by) {
                case 'profitdate_desc':
                    break;
                case 'profitdate_asc':
                    $direc='asc';
                    break;
                case 'sales_desc':
                    $order='sales';
                    break;
                case 'sales_asc':
                    $order='sales';
                    $direc='asc';
                    break;
                case 'revenue_desc':
                    $order='revenue';
                    break;
                case 'revenue_desc':
                    $order='revenue';
                    $direc='asc';
                    break;
                case 'grosprofit_desc':
                    $order='gross_profit';
                    break;
                case 'grosprofit_asc':
                    $order='gross_profit';
                    $direc='asc';
                    break;
                case 'operating_desc':
                    $order='np.profit_operating';
                    break;
                case 'operating_asc':
                    $order='np.profit_operating';
                    $direc='asc';
                    break;
                case 'payroll_desc':
                    $order='profit_payroll';
                    break;
                case 'payroll_asc':
                    $order='profit_payroll';
                    $direc='asc';
                    break;
                case 'advertising_desc':
                    $order='profit_advertising';
                    break;
                case 'advertising_asc':
                    $order='profit_advertising';
                    $direc='asc';
                    break;
                case 'projects_desc':
                    $order='profit_projects';
                    break;
                case 'projects_asc':
                    $order='profit_projects';
                    $direc='asc';
                    break;
                case 'w9work_desc':
                    $order='profit_projects';
                    break;
                case 'w9work_asc':
                    $order='profit_projects';
                    $direc='asc';
                    break;
                case 'purchases_desc':
                    $order='profit_purchases';
                    break;
                case 'purchases_asc':
                    $order='profit_purchases';
                    $direc='asc';
                    break;
                case 'totalcost_desc':
                    $order='totalcost';
                    break;
                case 'totalcost_asc':
                    $order='totalcost';
                    $direc='asc';
                    break;
                case 'netprofit_desc':
                    $order='netprofit';
                    break;
                case 'netprofit_asc':
                    $order='netprofit';
                    $direc='asc';
                    break;
                case 'netsaved_desc':
                    $order='np.profit_saved';
                    break;
                case 'netsaved_asc':
                    $order='np.profit_saved';
                    $direc='asc';
                    break;
                case 'owners_desc':
                    $order='np.profit_owners';
                    break;
                case 'owners_asc':
                    $order='np.profit_owners';
                    $direc='asc';
                    break;
                case 'od2_desc':
                    $order='np.od2';
                    break;
                case 'od2_asc':
                    $order='np.od2';
                    $direc='asc';
                    break;
            }
            // $this->balances_model->_check_current_week($this->USR_ID);
            $dat_end=date('Y-m-d', strtotime("Sunday this week", time())).' 23:59:59';
            $datend=strtotime($dat_end);
            // Get start && end date
            $fromweek=$this->input->post('fromweek');
            $untilweek=$this->input->post('untilweek');
            if ($fromweek) {
                $res=$this->balances_model->getweekdetail($fromweek,'start');
                if ($res['result']==$this->error_result) {
                    $this->ajaxResponse($mdata, $res['msg']);
                }
                $datbgn=$res['date'];
            }
            if ($untilweek) {
                $res=$this->balances_model->getweekdetail($untilweek,'end');
                if ($res['result']==$this->error_result) {
                    $this->ajaxResponse($mdata, $res['msg']);
                }
                $datend=$res['date'];
            }
            $data=$this->balances_model->get_netprofit_data($datbgn,$datend, $order, $direc, $this->USR_ID, $radio, $brand, $limitshow);
            // Run Totals
            $options=array(
                'type'=>'week',
                'start'=>0,
                'end'=>0,
                'brand' => $brand,
            );
            // Get start && end date
            if ($fromweek) {
                $res=$this->balances_model->getweekdetail($fromweek,'start');
                if ($res['result']==$this->error_result) {
                    $this->ajaxResponse($mdata, $res['msg']);
                }
                $options['start']=$res['date'];
            }
            if ($untilweek) {
                $res=$this->balances_model->getweekdetail($untilweek,'end');
                if ($res['result']==$this->error_result) {
                    $this->ajaxResponse($mdata, $res['msg']);
                }
                $options['end']=$res['date'];
            }
            $runtotal=$this->balances_model->get_netprofit_runs($options, $radio);
            $mdata['total_view']=$this->load->view('netprofitnew/running_totals_view', $runtotal, TRUE);
            $content_options=array(
                'data'=>$data,
                'limitshow'=>$limitshow,
            );
            $mdata['content']=$this->load->view('netprofitnew/table_data_view',$content_options,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function netprofit_charttabledata() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $yearstotals=$this->balances_model->get_netprofit_totalsbyweekdata($postdata);
            $yearstotals['compareweek']=$postdata['compareweek'];
            $mdata['content']=$this->load->view('netprofitnew/years_totals_view',$yearstotals, TRUE);
            $error='';
        }
        $this->ajaxResponse($mdata,$error);
    }

    public function netprofit_expensetable() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Expense Type';
            $postdata=$this->input->post();
            $expenstype = ifset($postdata,'expenstype','');
            if (!empty($expenstype)) {
                $error = '';
                $brand = ifset($postdata,'brand','ALL');
                $year = ifset($postdata,'year', date('Y'));
                // $year = 2017;
                $sortfld = ifset($postdata,'sortfld','category_name');
                $sortdir = ifset($postdata,'sortdir','asc');
                if ($expenstype=='ads') {
                    $data=$this->balances_model->get_expresyeardetails('ADS', $year, $brand, $sortfld, $sortdir);
                } elseif ($expenstype=='w9work') {
                    $data=$this->balances_model->get_expresyeardetails('W9', $year, $brand, $sortfld, $sortdir);
                } elseif ($expenstype=='discretionary') {
                    $data=$this->balances_model->get_expresyeardetails('Purchase', $year, $brand, $sortfld, $sortdir);
                } elseif ($expenstype=='upwork') {
                    $data=$this->balances_model->get_expresyeardetails('Upwork', $year, $brand, $sortfld, $sortdir);
                }
                $mdata['totals'] = empty($data['totals']) ? '' : MoneyOutput($data['totals']);
                if (count($data['details'])==0) {
                    $mdata['tableview'] = $this->load->view('netprofitnew/expensives_empty_view',[],TRUE);
                } else {
                    $mdata['tableview'] = $this->load->view('netprofitnew/expensives_view', ['datas' => $data['details']], TRUE);
                }

            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function netprofit_checkweek() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty Profit';
            $postdata = $this->input->post();
            $profit_id = ifset($postdata, 'profit_id', 0);
            if ($profit_id) {
                $brand = ifset($postdata,'brand','ALL');
                $res = $this->balances_model->include_netprofit_week($profit_id, $brand);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['weekcheck']=$res['runincl'];
                    // Get start && end date
                    $options = [
                        'type' => 'week',
                        'brand' => 'ALL',
                    ];
                    $fromweek = ifset($postdata, 'fromweek',0);
                    $untilweek = ifset($postdata,'untilweek', 0);
                    if ($fromweek) {
                        $res=$this->balances_model->getweekdetail($fromweek,'start');
                        if ($res['result']==$this->error_result) {
                            $this->ajaxResponse($mdata, $res['msg']);
                        }
                        $options['start']=$res['date'];
                    }
                    if ($untilweek) {
                        $res=$this->balances_model->getweekdetail($untilweek,'end');
                        if ($res['result']==$this->error_result) {
                            $this->ajaxResponse($mdata, $res['msg']);
                        }
                        $options['end']=$res['date'];
                    }
                    $viewtype = ifset($postdata,'viewtype', 'amount');
                    $runtotal=$this->balances_model->get_netprofit_runs($options, $viewtype);
                    $mdata['total_view']=$this->load->view('netprofitnew/running_totals_view', $runtotal, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function netprofitedit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Unknown Period For Edit';
            $postdata=$this->input->post();
            $profit_id = ifset($postdata,'profit_id',0);
            $brand = ifset($postdata,'brand');
            if ($profit_id>0 && !empty($brand)) {
                /* Get data about */
                $res=$this->balances_model->get_netprofit_dataedit($profit_id, $brand);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    // Session ID
                    $session='purchedit'.uniq_link(15);
                    // Save to session
                    $sessiondata=array(
                        'session'=>$session,
                        'profit_id'=>$profit_id,
                        'netprofit'=>$res['data'],
                        'purchase_details'=>$res['purchase_details'],
                        'w9work_details'=>$res['w9work_details'],
                        'upwork_details' => $res['upwork_details'],
                        'ads_details' => $res['ads_details'],
                        'delrecords'=>array(),
                    );
                    usersession($session, $sessiondata);
                    $data=$res['data'];
                    $data['session']=$session;
                    $mdata['content'] = $this->load->view('netprofitnew/weekdata_edit_view', $data, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function netprofit_details_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $res=$this->balances_model->netprofit_details_edit($netprofitdata, $postdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function netprofit_weekruncheck() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $res=$this->balances_model->netprofit_weekrun_edit($netprofitdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $mdata['content'] = $res['run_include'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function netprofit_purchase() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $netprofit=$netprofitdata['netprofit'];
                    $purchsession='purchedit'.uniq_link(15);
                    // Table view
                    $purchase_details=$netprofitdata['purchase_details'];
                    $purch_categories=$this->balances_model->get_profit_categories('Purchase');
                    $tableoptions=array(
                        'data'=>$purchase_details,
                        'categories'=>$purch_categories,
                        'category' => 'Purchase',
                    );
                    $purch_tableview=$this->load->view('netprofitnew/expensive_tabledata_view', $tableoptions, TRUE);
                    $purchase_totals=0;
                    foreach ($purchase_details as $drow) {
                        $purchase_totals+=$drow['amount'];
                    }
                    // Get W9 Works
                    $w9work_details=$netprofitdata['w9work_details'];
                    $w9work_categories=$this->balances_model->get_profit_categories('W9');
                    $w9options=array(
                        'data'=>$w9work_details,
                        'categories'=>$w9work_categories,
                        'category' => 'W9',
                    );
                    $w9work_tableview=$this->load->view('netprofitnew/expensive_tabledata_view', $w9options, TRUE);
                    $w9work_total=0;
                    foreach ($w9work_details as $wrow) {
                        $w9work_total+=$wrow['amount'];
                    }
                    $ads_details = $netprofitdata['ads_details'];
                    $ads_categories=$this->balances_model->get_profit_categories('Ads');
                    $adsoptions=array(
                        'data'=>$ads_details,
                        'categories'=>$ads_categories,
                        'category' => 'Ads',
                    );
                    $ads_tableview=$this->load->view('netprofitnew/expensive_tabledata_view', $adsoptions, TRUE);
                    $ads_total=0;
                    foreach ($ads_details as $wrow) {
                        $ads_total+=$wrow['amount'];
                    }

                    $upwork_details = $netprofitdata['upwork_details'];
                    $upwork_categories=$this->balances_model->get_profit_categories('Upwork');
                    $upworkoptions=array(
                        'data'=>$upwork_details,
                        'categories'=>$upwork_categories,
                        'category' => 'Upwork',
                    );
                    $upwork_tableview=$this->load->view('netprofitnew/expensive_tabledata_view', $upworkoptions, TRUE);
                    $upwork_total=0;
                    foreach ($upwork_details as $wrow) {
                        $upwork_total+=$wrow['amount'];
                    }

                    $options=array(
                        'session'=>$session_id,
                        'weeknote'=>'', //$netprofit['weeknote'],
                        'profit_purchases'=>$purchase_totals,
                        'putchase_tableview'=>$purch_tableview,
                        'w9work_tableview'=>$w9work_tableview,
                        'profit_w9'=>$w9work_total,
                        'ads_tableview'=>$ads_tableview,
                        'profit_ads' => $ads_total,
                        'upwork_tableview' => $upwork_tableview,
                        'profit_upwork' => $upwork_total,
                    );
                    // $mdata['title'] = '<b>W9 Work &amp; Purchases</b> for Week of '.date('m/d/Y', $netprofit['datebgn']).' - '.date('m/d/Y', $netprofit['dateend']);
                    $mdata['title'] = '<b>W9 Work &amp; Purchases</b>';
                    $mdata['content']=$this->load->view('netprofitnew/expensives_details_view', $options, TRUE);
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function netprofit_details_save() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = $this->restore_data_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'testsession');
            $brand = ifset($postdata,'brand');
            // Restore session data
            $netprofitdata = usersession($session_id);
            if (!empty($netprofitdata) && !empty($brand)) {
                $res = $this->balances_model->netprofit_details_save($netprofitdata, $this->USR_ID, $session_id, $brand);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['refresh'] = $res['refresh'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function profit_newcategory() {
        $mdata = array();
        $error = 'Test';
        $postdata = $this->input->post();
        $options = array('category' => $postdata['category'],);
        $content = $this->load->view('netprofitnew/new_category_view', $options, TRUE);
        echo $content;
    }

    public function profit_categorysave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $res=$this->balances_model->netprofit_newcategory($netprofitdata,$postdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $netprofitdata=usersession($session_id);
                        $categories=$this->balances_model->get_profit_categories($postdata['category_type']);
                        if ($postdata['category_type']=='Purchase') {
                            $details=$netprofitdata['purchase_details'];
                        } elseif ($postdata['category_type']=='W9') {
                            $details=$netprofitdata['w9work_details'];
                        } elseif ($postdata['category_type']=='Upwork') {
                            $details=$netprofitdata['upwork_details'];
                        } elseif ($postdata['category_type']=='Ads') {
                            $details=$netprofitdata['ads_details'];
                        }
                        $tableoptions=array(
                            'data'=>$details,
                            'categories'=>$categories,
                            'category' => $postdata['category_type'],
                        );
                        $mdata['content']=$this->load->view('netprofitnew/expensive_tabledata_view', $tableoptions, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchase_deletedetails() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $detail_id=$postdata['detail_id'];
                    $category_type=$postdata['category_type'];
                    $res=$this->balances_model->purchase_details_remove($netprofitdata, $category_type, $detail_id, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $netprofitdata=usersession($session_id);
                        if ($category_type=='Purchase') {
                            $details=$netprofitdata['purchase_details'];
                            $categories=$this->balances_model->get_profit_categories('Purchase');
                            $options=array(
                                'data'=>$details,
                                'categories'=>$categories,
                                'category' => 'Purchase',
                            );
                        } elseif ($category_type=='W9') {
                            $details=$netprofitdata['w9work_details'];
                            $categories=$this->balances_model->get_profit_categories('W9');
                            $options=array(
                                'data'=>$details,
                                'categories'=>$categories,
                                'category' => 'W9',
                            );
                        } elseif ($category_type=='Ads') {
                            $details=$netprofitdata['ads_details'];
                            $categories=$this->balances_model->get_profit_categories('Ads');
                            $options=array(
                                'data'=>$details,
                                'categories'=>$categories,
                                'category' => 'Ads',
                            );
                        } elseif ($category_type=='Upwork') {
                            $details=$netprofitdata['upwork_details'];
                            $categories=$this->balances_model->get_profit_categories('Upwork');
                            $options=array(
                                'data'=>$details,
                                'categories'=>$categories,
                                'category' => 'Upwork',
                            );
                        }
                        $mdata['content']=$this->load->view('netprofitnew/expensive_tabledata_view', $options, TRUE);
                        $total=0;
                        foreach ($details as $row) {
                            $total+=floatval($row['amount']);
                        }
                        $mdata['total']=MoneyOutput($total,2);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function netprofit_newdetails() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = $this->restore_data_error;
            $postdata = $this->input->post();
            if (isset($postdata['session'])) {
                $session_id = $postdata['session'];
                $netprofitdata = usersession($session_id);
                if (!empty($netprofitdata)) {
                    $error = 'Empty Expense type';
                    $expence_type = ifset($postdata,'expense','');
                    if (!empty($expence_type)) {
                        $netprofitdata=usersession($session_id);
                        $res=$this->balances_model->netprofit_details_add($netprofitdata, $expence_type, $session_id);
                        $error=$res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error='';
                            if ($expence_type=='Purchase') {
                                $details=$netprofitdata['purchase_details'];
                                $categories=$this->balances_model->get_profit_categories('Purchase');
                                $options=array(
                                    'data'=>$details,
                                    'categories'=>$categories,
                                    'category' => 'Purchase',
                                );
                            } elseif ($expence_type=='W9') {
                                $details=$netprofitdata['w9work_details'];
                                $categories=$this->balances_model->get_profit_categories('W9');
                                $options=array(
                                    'data'=>$details,
                                    'categories'=>$categories,
                                    'category' => 'W9',
                                );
                            } elseif ($expence_type=='Ads') {
                                $details=$netprofitdata['ads_details'];
                                $categories=$this->balances_model->get_profit_categories('Ads');
                                $options=array(
                                    'data'=>$details,
                                    'categories'=>$categories,
                                    'category' => 'Ads',
                                );
                            } elseif ($expence_type=='Upwork') {
                                $details=$netprofitdata['upwork_details'];
                                $categories=$this->balances_model->get_profit_categories('Upwork');
                                $options=array(
                                    'data'=>$details,
                                    'categories'=>$categories,
                                    'category' => 'Upwork',
                                );
                            }
                            $mdata['content']=$this->load->view('netprofitnew/expensive_tabledata_view', $options, TRUE);
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchase_editdetails() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $res=$this->balances_model->purchase_details_edit($netprofitdata, $postdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $category = $postdata['category_type'];
                        $netprofitdata=usersession($session_id);
                        if ($category=='Purchase') {
                            $details=$netprofitdata['purchase_details'];
                        } elseif ($category=='W9') {
                            $details=$netprofitdata['w9work_details'];
                        } elseif ($category=='Ads') {
                            $details=$netprofitdata['ads_details'];
                        } else {
                            $details=$netprofitdata['upwork_details'];
                        }
                        $total=0;
                        foreach ($details as $row) {
                            $total+=floatval($row['amount']);
                        }
                        $mdata['total']=MoneyOutput($total,2);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function manage_profcategory() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $category_type=$this->input->post('category_type');
            $categories=$this->balances_model->get_profit_categories($category_type, 0);
            $tableoptions=array(
                'data'=>$categories,
            );
            $tableview=$this->load->view('netprofitnew/profitcategory_table_view', $tableoptions, TRUE);
            $options=array(
                'category_type'=>$category_type,
                'tableview'=>$tableview,
            );
            $mdata['content']=$this->load->view('netprofitnew/netprofit_categories_view', $options, TRUE);
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function profcategory_edit() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $category_id=$this->input->post('category_id');
            if ($category_id==0) {
                $data=array(
                    'category_id'=>-1,
                    'category_name'=>'',
                );
            } else {
                $res=$this->balances_model->get_profit_category($category_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $data=array(
                        'category_id'=>$res['data']['netprofit_category_id'],
                        'category_name'=>$res['data']['category_name'],
                    );
                }
            }
            if (empty($error)) {
                $mdata['content']=$this->load->view('netprofitnew/netprofit_category_edit', $data, TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function profcategory_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();

            $res=$this->balances_model->save_profit_category($postdata);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $category_type=$postdata['category_type'];
                $categories=$this->balances_model->get_profit_categories($category_type, 0);
                $tableoptions=array(
                    'data'=>$categories,
                );
                $mdata['content']=$this->load->view('netprofitnew/profitcategory_table_view', $tableoptions, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function profcategory_cancel() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $category_type=$this->input->post('category_type');
            $categories=$this->balances_model->get_profit_categories($category_type, 0);
            $tableoptions=array(
                'data'=>$categories,
            );
            $mdata['content']=$this->load->view('netprofitnew/profitcategory_table_view', $tableoptions, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}