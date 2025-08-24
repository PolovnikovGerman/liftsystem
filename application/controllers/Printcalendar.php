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

}