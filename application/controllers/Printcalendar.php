<?php

class Printcalendar extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('printcalendar_model');
    }

    public function index(){}

    public function yearcalendar()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $year = ifset($postdata, 'year',0);
            $error = 'Empty Year';
            if (!empty($year)) {
                $error = '';
                $yearbgn = getDatesByWeek(1,$year);
                $calend = $this->printcalendar_model->build_calendar($yearbgn['start_week'], $year);
                $mdata['calendarview'] = $this->load->view('printcalendar/calendar_view', ['calendars' => $calend], true);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function yearstatic()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $year = ifset($postdata, 'year',0);
            $mdata = [];
            $error = 'Empty Year';
            if (!empty($year)) {
                $error = '';
                $res = $this->printcalendar_model->year_statistic($year);
                $lates = $res['late'];
                $lateoptions = [
                    'orders' => $lates['ordercnt'],
                    'prints' => $lates['printqty'],
                ];
                $mdata['latecontent'] = $this->load->view('printcalendar/lateresult_view', $lateoptions, true);
                $mdata['statistic'] = $this->load->view('printcalendar/statist_year_view', $res, true);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function weekcalendar()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Date';
            $postdata = $this->input->post();
            $printweek = ifset($postdata, 'printweek','');
            if (!empty($printweek)) {
                $error = '';
                $weekdat = explode("-", $printweek);
                $res = $this->printcalendar_model->week_calendar($weekdat[0], $weekdat[1]);
                $mdata['content'] = $this->load->view('printcalendar/week_calendar_view', $res, true);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function weekcalendarmove()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Date';
            $postdata = $this->input->post();
            $week = ifset($postdata, 'week','');
            $direct = ifset($postdata, 'direct','');
            if (!empty($week) && !empty($direct)) {
                $error = '';
                $weekdat = $this->printcalendar_model->weekdates($week, $direct);
                $res = $this->printcalendar_model->week_calendar($weekdat['week'], $weekdat['year']);
                $mdata['content'] = $this->load->view('printcalendar/week_calendar_view', $res, true);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function daylidetails()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Date';
            $postdata = $this->input->post();
            $printdate = ifset($postdata, 'printdate',0);
            $smallview = ifset($postdata, 'smallview',0);
            if (!empty($printdate)) {
                $error = '';
                $curdate = strtotime(date("Y-m-d"));
                if ($printdate >= $curdate) {
                    $mdata['late'] = 0;
                    if ($smallview == 1) {
                        $res = $this->printcalendar_model->dayshortdetails($printdate);
                    } else {
                        $res = $this->printcalendar_model->daydetails($printdate);
                    }
                    $header_view = $this->load->view('printcalendar/daydetails_header_view', $res, true);
                    $warnings = $res['warnings'];
                    $warnings_view = '';
                    $mdata['warningcnt'] = 0;
                    if (count($warnings) > 0) {
                        $mdata['warningcnt'] = 1;
                        if ($smallview == 1) {
                            $warnings_view = $this->load->view('printcalendar/dayshort_warnings_view', ['lists' => $warnings], true);
                        } else {
                            $warnings_view = $this->load->view('printcalendar/daydetails_warnings_view', ['lists' => $warnings], true);
                        }
                    }
                    if ($smallview == 1) {
                        $regular_view = '';
                        if (count($res['regular']) > 0) {
                            $regular_view = $this->load->view('printcalendar/dayshort_regular_view', ['total'=> $res['regulartotal'], 'lists' => $res['regular'], ], true);
                        }
                    } else {
                        $this->load->model('user_model');
                        $userlist = $this->user_model->get_printschedul_users();
                        $regular_view = '';
                        if (count($res['unsign'])+count($res['assign']) > 0) {
                            $unassign_view = '';
                            if (count($res['unsign']) > 0) {
                                $unassign_view = $this->load->view('printcalendar/daydetails_unsign_view', ['total'=> $res['unsigntotal'], 'lists' => $res['unsign'], 'users' => $userlist], true);
                            }
                            $assign_view = '';
                            if (count($res['assign']) > 0) {
                                $assigns = $res['assign'];
                                foreach ($assigns as $assign) {
                                    $usrassgn = $this->printcalendar_model->get_printdate_usrassigned($printdate, $assign['user_id']);
                                    $assign_options = [
                                        'user_id' => $assign['user_id'],
                                        'user' => $assign['user_name'],
                                        'users' => $userlist,
                                        'orders' => $assign['ordercnt'],
                                        'items' => $assign['itemscnt'],
                                        'prints' => $assign['printqty'],
                                        'lists' => $usrassgn,
                                    ];
                                    $assign_view.= $this->load->view('printcalendar/daydetails_assign_view', $assign_options, true);
                                }
                            }
                            $regoptions = [
                                'unsign_view' => $unassign_view,
                                'assign_view' => $assign_view,
                            ];
                            $regular_view = $this->load->view('printcalendar/daydetails_regular_view', $regoptions, true);
                        }
                    }
                    $history_view = '';
                    if (count($res['history']) > 0) {
                        if ($smallview == 1) {
                            $history_view = $this->load->view('printcalendar/dayshort_history_view', ['totals' => $res['history_total'], 'lists' => $res['history'], 'printdate' => $printdate], true);
                        } else {
                            $history_view = $this->load->view('printcalendar/daydetails_history_view', ['totals' => $res['history_total'], 'lists' => $res['history'], 'printdate' => $printdate], true);
                        }
                    }
                    $options = [
                        'header_view' => $header_view,
                        'warnings_view' => $warnings_view,
                        'regular_view' => $regular_view,
                    ];
                    $mdata['content'] = $this->load->view('printcalendar/daydetails_view', $options, true);
                    $mdata['historyview'] = $history_view;
                } else {
                    $res = $this->printcalendar_model->daylatedetails($printdate);
                    $mdata['late'] = 1;
                    $mdata['warningcnt'] = 0;
                    $header_view = $this->load->view('printcalendar/daydetails_header_view', $res, true);
                    $options = [
                        'header_view' => $header_view,
                    ];
                    $mdata['content'] = $this->load->view('printcalendar/latedetails_view', $options, true);
                    $history_view = '';
                    $historyres = $this->printcalendar_model->get_printdate_history($printdate);
                    if (count($historyres['data']) > 0) {
                        if ($smallview == 1) {
                            $history_view = $this->load->view('printcalendar/dayshort_history_view', ['totals' => $historyres['total'], 'lists' => $historyres['data'], 'printdate' => $printdate], true);
                        } else {
                            $history_view = $this->load->view('printcalendar/daydetails_history_view', ['totals' => $historyres['total'], 'lists' => $historyres['data'], 'printdate' => $printdate], true);
                        }
                    }
                    $mdata['historyview'] = $history_view;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function assignprintorder()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty User/Order';
            $postdata = $this->input->post();
            $order_itemcolor_id = ifset($postdata, 'order', '');
            $user_id = ifset($postdata, 'user',0);
            $smallview = ifset($postdata, 'smallview', 0);
            if (!empty($order_itemcolor_id)) {
                $res = $this->printcalendar_model->assignorder($order_itemcolor_id, $user_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $printdate = $res['printdate'];
                    $this->load->model('user_model');
                    $userlist = $this->user_model->get_printschedul_users();
                    $unassign_view = $assign_view = '';
                    $unsigdata = $this->printcalendar_model->get_printdate_unsigned($printdate);
                    if (count($unsigdata['data'])>0) {
                        if ($smallview == 1) {
                            $unassign_view = $this->load->view('printcalendar/dayshort_unsign_view', ['total'=> $unsigdata['total'], 'lists' => $unsigdata['data'], 'users' => $userlist], true);
                        } else {
                            $unassign_view = $this->load->view('printcalendar/daydetails_unsign_view', ['total'=> $unsigdata['total'], 'lists' => $unsigdata['data'], 'users' => $userlist], true);
                        }
                    }
                    // Get list of printers
                    $usrs = $this->printcalendar_model->get_printdate_assigned($printdate);
                    if (count($usrs)>0) {
                        foreach ($usrs as $usr) {
                            $assigndat = $this->printcalendar_model->get_printdate_usrassigned($printdate, $usr['user_id']);
                            $assign_options = [
                                'user_id' => $usr['user_id'],
                                'user' => $usr['user_name'],
                                'users' => $userlist,
                                'orders' => $usr['ordercnt'],
                                'items' => $usr['itemscnt'],
                                'prints' => $usr['printqty'],
                                'lists' => $assigndat,
                            ];
                            if ($smallview == 1) {
                                $assign_view.= $this->load->view('printcalendar/dayshort_assign_view', $assign_options, true);
                            } else {
                                $assign_view.= $this->load->view('printcalendar/daydetails_assign_view', $assign_options, true);
                            }
                        }
                    }
                    $regoptions = [
                        'unsign_view' => $unassign_view,
                        'assign_view' => $assign_view,
                    ];
                    if ($smallview == 1) {
                        $regular_view = $this->load->view('printcalendar/dayshort_regular_view', $regoptions, true);
                    } else {
                        $regular_view = $this->load->view('printcalendar/daydetails_regular_view', $regoptions, true);
                    }
                    $mdata['content'] = $regular_view;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function rescheduleview()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Date';
            $postdata = $this->input->post();
            $printdate = ifset($postdata, 'printdate',0);
            if (!empty($printdate)) {
                $error = '';
                $mdata['printdate'] = $printdate;
                $res = $this->printcalendar_model->daydetails($printdate);
                $curdate = strtotime(date("Y-m-d"));
                $mdata['warningcnt'] = 0;
                if ($printdate >= $curdate) {
                    $mdata['late'] = 0;
                    $res = $this->printcalendar_model->dayshortdetails($printdate);
                    $header_view = $this->load->view('printcalendar/daydetails_header_view', $res, true);
                    $warnings = $res['warnings'];
                    $warnings_view = '';
                    if (count($warnings) > 0) {
                        $mdata['warningcnt'] = 1;
                        $warnings_view = $this->load->view('printcalendar/dayshort_warnings_view', ['lists' => $warnings], true);
                    }
//                    $this->load->model('user_model');
//                    $userlist = $this->user_model->get_printschedul_users();
                    $regular_view = '';
                    if (count($res['regular']) > 0) {
                        $regular_view = $this->load->view('printcalendar/dayshort_regular_view', ['totals' => $res['regulartotal'], 'lists' => $res['regular']], true);
                    }
                    $history_view = '';
                    if (count($res['history']) > 0) {
                        $history_view = $this->load->view('printcalendar/dayshort_history_view', ['totals' => $res['history_total'], 'lists' => $res['history'], 'printdate' => $printdate], true);
                    }
                    $options = [
                        'header_view' => $header_view,
                        'warnings_view' => $warnings_view,
                        'regular_view' => $regular_view,
                    ];
                    $mdata['content'] = $this->load->view('printcalendar/daydetails_view', $options, true);
                    $mdata['historyview'] = $history_view;
                } else {
                    $res = $this->printcalendar_model->daylatedetails($printdate);
                    $mdata['late'] = 1;
                    $header_view = $this->load->view('printcalendar/daydetails_header_view', $res, true);
                    $options = [
                        'header_view' => $header_view,
                    ];
                    $mdata['content'] = $this->load->view('printcalendar/latedetails_view', $options, true);
                    $history_view = '';
                    $historyres = $this->printcalendar_model->get_printdate_history($printdate);
                    if (count($historyres['data']) > 0) {
                        $history_view = $this->load->view('printcalendar/dayshort_history_view', ['totals' => $historyres['total'], 'lists' => $historyres['data'], 'printdate' => $printdate], true);
                    }
                    $mdata['historyview'] = $history_view;
                }
                // Get calendar
                $sortfld = ifset($postdata, 'sortfld', 'print_date');
                $calendview = '';
                if ($sortfld == 'print_date') {
                    $calend = $this->printcalendar_model->get_reschedule_printdate();
                    if ($calend['lates']+$calend['ontime'] > 0) {
                        $calendoptions = [
                            'lates' => $calend['lates'],
                            'ontime' => $calend['ontime'],
                            'calendars' => $calend['calendar'],
                            'lateorders' => $calend['lateorders'],
                        ];
                        $calendview = $this->load->view('printcalendar/rescheduler_dates_view', $calendoptions, true);
                    }
                } else {
                    $calend = $this->printcalendar_model->get_reschedule_items();
                    $calendview = $this->load->view('printcalendar/rescheduler_items_view', ['calendars' => $calend], true);
                }
                $mdata['calendarview'] = $calendview;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Re Schedule
    public function ordernewdate()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Date';
            $postdata = $this->input->post();
            $order_id = ifset($postdata, 'order_id',0);
            $printdate = ifset($postdata, 'print_date',0);
            $incomeblock = ifset($postdata, 'incomeblock','right');
            $outcomeblock = ifset($postdata, 'outcomeblock','right');
            if (!empty($printdate) && !empty($order_id)) {
                $res = $this->printcalendar_model->updateorder_printdate($order_id, $printdate);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['message'] = 'Order '.$res['order_num'].' was rescheduled from '.date('D - M, j, Y', $res['olddate']).' to '.date('D - M, j, Y', $printdate);
                    if ($incomeblock!=$outcomeblock) {
                        $this->load->model('user_model');
                        $userlist = $this->user_model->get_printschedul_users();
                        $olddate = $res['olddate'];
                        if ($incomeblock=='left') {
                            $totals = $this->printcalendar_model->daylatedetails($printdate);
                            // Warnings
                            $warnings = $this->printcalendar_model->get_printdate_warnings($printdate);
                            $regul = $this->printcalendar_model->get_printdate_regulars($printdate);
                            $schedul = $this->printcalendar_model->get_reschedule_data($olddate);
                            $mdata['warningscnt'] = count($warnings) > 0 ? 1 : 0;
                            $warnings_view = '';
                            if ($mdata['warningscnt']>0) {
                                $warnings_view = $this->load->view('printcalendar/dayshort_warnings_view', ['lists' => $warnings], true);
                            }
                            $mdata['warnings'] = $warnings_view;
                            $mdata['income'] = $this->load->view('printcalendar/dayshort_regular_view', ['total'=> $regul['total'], 'lists' => $regul['data'], ], true);
                            $mdata['late'] = $schedul['late'];
                            $mdata['outcome'] = $this->load->view('printcalendar/day_schedule_view', ['lists' => $schedul['data'], 'late' => $schedul['late']], true);
                        } else {
                            $totals = $this->printcalendar_model->daylatedetails($olddate);
                            $unsign = $this->printcalendar_model->get_printdate_unsigned($olddate);
                            $unassign_view = '';
                            if (count($unsign['data']) > 0) {
                                $unassign_view = $this->load->view('printcalendar/dayshort_unsign_view', ['total'=> $unsign['total'], 'lists' => $unsign['data'], 'users' => $userlist], true);
                            }
                            $assignusrs = $this->printcalendar_model->get_printdate_assigned($olddate);
                            $assign_view = '';
                            if (count($assignusrs)>0) {
                                foreach ($assignusrs as $assign) {
                                    $usrassgn = $this->printcalendar_model->get_printdate_usrassigned($olddate, $assign['user_id']);
                                    $assign_options = [
                                        'user_id' => $assign['user_id'],
                                        'user' => $assign['user_name'],
                                        'users' => $userlist,
                                        'orders' => $assign['ordercnt'],
                                        'items' => $assign['itemscnt'],
                                        'prints' => $assign['printqty'],
                                        'lists' => $usrassgn,
                                    ];
                                    $assign_view.= $this->load->view('printcalendar/dayshort_assign_view', $assign_options, true);
                                }
                            }
                            $schedul = $this->printcalendar_model->get_reschedule_data($printdate);
                            $mdata['late'] = $schedul['late'];
                            $mdata['income'] = $this->load->view('printcalendar/day_schedule_view', ['lists' => $schedul['data'], 'late' => $schedul['late']], true);
                            $mdata['unassign'] = $unassign_view;
                            $mdata['assign'] = $assign_view;
                        }
                        $mdata['outdate'] = $olddate;
                        $mdata['incomedate'] = $printdate;
                        $mdata['orders'] = QTYOutput($totals['orders']);
                        $mdata['items'] = QTYOutput($totals['items']);
                        $mdata['prints'] = QTYOutput($totals['prints']);
                        $mdata['todaytemplate'] = $this->load->view('printcalendar/today_template_view', [], true);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function reschedulechangeview()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Sorting Parameter';
            $postdata = $this->input->post();
            $sortfld = ifset($postdata,'sortfld','');
            if (!empty($sortfld)) {
                $error = '';
                $calendview = '';
                if ($sortfld == 'print_date') {
                    $calend = $this->printcalendar_model->get_reschedule_printdate();
                    if ($calend['lates']+$calend['ontime'] > 0) {
                        $calendoptions = [
                            'lates' => $calend['lates'],
                            'ontime' => $calend['ontime'],
                            'calendars' => $calend['calendar'],
                            'lateorders' => $calend['lateorders'],
                        ];
                        $calendview = $this->load->view('printcalendar/rescheduler_dates_view', $calendoptions, true);
                    }
                } else {
                    $calend = $this->printcalendar_model->get_reschedule_items();
                    $calendview = $this->load->view('printcalendar/rescheduler_items_view', ['calendars' => $calend], true);
                }
                $mdata['calendarview'] = $calendview;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function stockupdate()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order Color';
            $postdata = $this->input->post();
            $order_itemcolor_id = ifset($postdata, 'order_color',0);
            if (!empty($order_itemcolor_id)) {
                $res = $this->printcalendar_model->stockupdate($order_itemcolor_id);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['newval'] = $res['newval'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function platesupdate()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order Item';
            $postdata = $this->input->post();
            $order_item_id = ifset($postdata, 'order_item',0);
            if (!empty($order_item_id)) {
                $res = $this->printcalendar_model->plateupdate($order_item_id);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['newval'] = $res['newval'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inkupdate()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order Color';
            $postdata = $this->input->post();
            $order_itemcolor_id = ifset($postdata, 'order_color',0);
            if (!empty($order_itemcolor_id)) {
                $res = $this->printcalendar_model->inkupdate($order_itemcolor_id);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['newval'] = $res['newval'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function outcomesave()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order Color';
            $postdata = $this->input->post();
            $order_itemcolor_id = ifset($postdata,'itemcolor','');
            $shipped = intval(ifset($postdata,'shipped',0));
            $kepted = floatval(ifset($postdata,'kepted',0));
            $misprint = floatval(ifset($postdata,'misprint',0));
            $plates = floatval(ifset($postdata,'plates',0));
            $podateval = ifset($postdata,'podate',date('m/d/Y'));
            $podate = strtotime($podateval);
            if (!empty($order_itemcolor_id)) {
                $res = $this->printcalendar_model->outcomesave($order_itemcolor_id,$shipped,$kepted,$misprint,$plates, $podate);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['refreshinfo'] = 1;
                    $printdate = $res['printdate'];
                    $views = $this->_prepare_daydetails_parts($printdate);
                    $mdata['warningview'] = $views['warningview'];
                    $mdata['regularview'] = $views['regularview'];
                    $mdata['historyview'] = $views['historyview'];
                    $mdata['warningcnt'] = $views['warningcnt'];
//                    $this->load->model('user_model');
//                    $userlist = $this->user_model->get_printschedul_users();
//                    $itemcolor = $this->printcalendar_model->get_itemcolor_details($order_itemcolor_id);
//                    $mdata['refreshinfo'] = 0;
//                    if (($itemcolor['fulfillprc']>=100 && $itemcolor['shippedprc']>=100) || ($itemcolor['fulfill']<$itemcolor['shipped']))  {
//                        $mdata['refreshinfo'] = 1;
//                        // Build new day
//                        $printdate = $res['printdate'];
//                        $views = $this->_prepare_daydetails_parts($printdate);
//                        $mdata['warningview'] = $views['warningview'];
//                        $mdata['regularview'] = $views['regularview'];
//                        $mdata['historyview'] = $views['historyview'];
//                    } else {
//                        $mdata['content'] = $this->load->view('printcalendar/printcolor_data_view', ['list' => $itemcolor, 'users' => $userlist], true);
//                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function shiporder()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order Color';
            $postdata = $this->input->post();
            $order_itemcolor_id = ifset($postdata, 'itemcolor',0);
            $shipqty = ifset($postdata, 'shipqty',0);
            $shipmethod = ifset($postdata, 'shipmethod','');
            $trackcode = ifset($postdata, 'trackcode','');
            $shipdate = ifset($postdata, 'shipdate','');
            if (!empty($order_itemcolor_id)) {
                $error = 'Empty Shipping Date';
                if (!empty($shipdate)) {
                    $error = 'Empty Shipping Method';
                    if (!empty($shipmethod)) {
                        $error = 'Empty Tracking Value';
                        if (intval($shipqty) > 0) {
                            $res = $this->printcalendar_model->shiporder($order_itemcolor_id, $shipqty, $shipmethod, $trackcode, $shipdate);
                            $error = $res['msg'];
                            if ($res['result'] == $this->success_result) {
                                $error = '';
                                $mdata['refreshinfo'] = 1;
                                // Build new day
                                $printdate = $res['printdate'];
                                $views = $this->_prepare_daydetails_parts($printdate);
                                $mdata['warningview'] = $views['warningview'];
                                $mdata['regularview'] = $views['regularview'];
                                $mdata['historyview'] = $views['historyview'];
                                $mdata['warningcnt'] = $views['warningcnt'];
//                                $this->load->model('user_model');
//                                $userlist = $this->user_model->get_printschedul_users();
//                                $itemcolor = $this->printcalendar_model->get_itemcolor_details($order_itemcolor_id);
//                                $mdata['refreshinfo'] = 0;
//                                if ($itemcolor['fulfillprc']>=100 && $itemcolor['shippedprc']>=100) {
//                                    $mdata['refreshinfo'] = 1;
//                                    // Build new day
//                                    $printdate = $res['printdate'];
//                                    $views = $this->_prepare_daydetails_parts($printdate);
//                                    $mdata['warningview'] = $views['warningview'];
//                                    $mdata['regularview'] = $views['regularview'];
//                                    $mdata['historyview'] = $views['historyview'];
//                                } else {
//                                    $mdata['content'] = $this->load->view('printcalendar/printcolor_data_view', ['list' => $itemcolor, 'users' => $userlist], true);
//                                }
                            }
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_daydetails_parts($printdate)
    {
        $res = $this->printcalendar_model->daydetails($printdate);
        $warnings = $res['warnings'];
        $warnings_view = '';
        if (count($warnings) > 0) {
            $warnings_view = $this->load->view('printcalendar/daydetails_warnings_view', ['lists' => $warnings], true);
        }
        $regular_view = '';
        if (count($res['unsign'])+count($res['assign']) > 0) {
            $unassign_view = '';
            $this->load->model('user_model');
            $userlist = $this->user_model->get_printschedul_users();
            if (count($res['unsign']) > 0) {
                $unassign_view = $this->load->view('printcalendar/daydetails_unsign_view', ['total'=> $res['unsigntotal'], 'lists' => $res['unsign'], 'users' => $userlist], true);
            }
            $assign_view = '';
            if (count($res['assign']) > 0) {
                $assigns = $res['assign'];
                foreach ($assigns as $assign) {
                    $usrassgn = $this->printcalendar_model->get_printdate_usrassigned($printdate, $assign['user_id']);
                    $assign_options = [
                        'user_id' => $assign['user_id'],
                        'user' => $assign['user_name'],
                        'users' => $userlist,
                        'orders' => $assign['ordercnt'],
                        'items' => $assign['itemscnt'],
                        'prints' => $assign['printqty'],
                        'lists' => $usrassgn,
                    ];
                    $assign_view.= $this->load->view('printcalendar/daydetails_assign_view', $assign_options, true);
                }
            }
            $regoptions = [
                'unsign_view' => $unassign_view,
                'assign_view' => $assign_view,
            ];
            $regular_view = $this->load->view('printcalendar/daydetails_regular_view', $regoptions, true);
        }
        $history_view = '';
        if (count($res['history']) > 0) {
            $history_view = $this->load->view('printcalendar/daydetails_history_view', ['totals' => $res['history_total'], 'lists' => $res['history'], 'printdate' => $printdate], true);
        }
        return [
            'warningview' => $warnings_view,
            'regularview' => $regular_view,
            'historyview' => $history_view,
            'warningcnt' => count($warnings),
        ];
    }

    public function rescheduletoday()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $printdate = strtotime(date('Y-m-d'));
            $printweek = date('W-Y', $printdate);
            $weekdat = explode("-", $printweek);
            $res = $this->printcalendar_model->week_calendar($weekdat[0], $weekdat[1]);
            $mdata['weekcalend'] = $this->load->view('printcalendar/week_calendar_view', $res, true);
            $mdata['printdate'] = $printdate;
            // Day Details
            $res = $this->printcalendar_model->dayshortdetails($printdate);
            $header_view = $this->load->view('printcalendar/daydetails_header_view', $res, true);
            $warnings = $res['warnings'];
            $warnings_view = '';
            $mdata['warningcnt'] = 0;
            if (count($warnings) > 0) {
                $mdata['warningcnt'] = 1;
                $warnings_view = $this->load->view('printcalendar/dayshort_warnings_view', ['lists' => $warnings], true);
            }
            $regular_view = '';
            if (count($res['regular']) > 0) {
                $regular_view = $this->load->view('printcalendar/dayshort_regular_view', ['totals' => $res['regulartotal'], 'lists' => $res['regular']], true);
            }
            $history_view = '';
            if (count($res['history']) > 0) {
                $history_view = $this->load->view('printcalendar/dayshort_history_view', ['totals' => $res['history_total'], 'lists' => $res['history'], 'printdate' => $printdate], true);
            }
            $options = [
                'header_view' => $header_view,
                'warnings_view' => $warnings_view,
                'regular_view' => $regular_view,
            ];
            $mdata['content'] = $this->load->view('printcalendar/daydetails_view', $options, true);
            $mdata['historyview'] = $history_view;
            // Get calendar
            $sortfld = ifset($postdata, 'sortfld', 'print_date');
            $calendview = '';
            if ($sortfld == 'print_date') {
                $calend = $this->printcalendar_model->get_reschedule_printdate();
                if ($calend['lates']+$calend['ontime'] > 0) {
                    $calendoptions = [
                        'lates' => $calend['lates'],
                        'ontime' => $calend['ontime'],
                        'calendars' => $calend['calendar'],
                        'lateorders' => $calend['lateorders'],
                    ];
                    $calendview = $this->load->view('printcalendar/rescheduler_dates_view', $calendoptions, true);
                }
            } else {
                $calend = $this->printcalendar_model->get_reschedule_items();
                $calendview = $this->load->view('printcalendar/rescheduler_items_view', ['calendars' => $calend], true);
            }
            $mdata['calendarview'] = $calendview;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}