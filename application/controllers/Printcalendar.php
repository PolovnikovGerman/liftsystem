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

    public function daylidetails()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Date';
            $postdata = $this->input->post();
            $printdate = ifset($postdata, 'printdate',0);
            if (!empty($printdate)) {
                $error = '';
                $res = $this->printcalendar_model->daydetails($printdate);
                $header_view = $this->load->view('printcalendar/daydetails_header_view', $res, true);
                $warnings = $res['warnings'];
                $warnings_view = '';
                if (count($warnings) > 0) {
                    $warnings_view = $this->load->view('printcalendar/daydetails_warnings_view', ['lists' => $warnings], true);
                }
                $regular_view = '';
                if (count($res['unsign'])+count($res['assign']) > 0) {
                    $unassign_view = '';
                    if (count($res['unsign']) > 0) {
                        $unassign_view = $this->load->view('printcalendar/daydetails_unsign_view', ['total'=> $res['unsigntotal'], 'lists' => $res['unsign']], true);
                    }
                    $assign_view = '';
                    if (count($res['assign']) > 0) {
                    }
                    $regoptions = [
                        'unsign_view' => $unassign_view,
                        'assign_view' => $assign_view,
                    ];
                    $regular_view = $this->load->view('printcalendar/daydetails_regular_view', $regoptions, true);
                }
                $history_view = '';
                if (count($res['history']) > 0) {
                    $history_view = $this->load->view('printcalendar/daydetails_history_view', ['totals' => $res['history_total'], 'lists' => $res['history']], true);
                }
                $options = [
                    'header_view' => $header_view,
                    'warnings_view' => $warnings_view,
                    'regular_view' => $regular_view,
                ];
                $mdata['content'] = $this->load->view('printcalendar/daydetails_view', $options, true);
                $mdata['historyview'] = $history_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}