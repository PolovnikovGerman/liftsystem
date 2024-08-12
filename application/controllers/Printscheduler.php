<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Printscheduler extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('printscheduler_model');
    }

    public function index()
    {

    }

    public function get_calendar()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand', '');
            $calendar = ifset($postdata,'calendar', 'past');
            if (!empty($brand)) {
                if ($calendar=='past') {
                    $res = $this->printscheduler_model->get_pastorders($brand);
                    $options = [
                        'orders' => $res,
                        'numorders' => count($res),
                        'brand' => $brand,
                    ];
                    $mdata['content'] = $this->load->view('printscheduler/pastorders_view', $options, TRUE);
                    $error = '';
                } else {
                    $dates = $this->printscheduler_model->get_ontimeorders_dates($brand);
                    $content = '';
                    foreach ($dates as $datrow) {
                        $orders = $this->printscheduler_model->get_ontimeorders_day($datrow['printdate'],$brand);
                        $options = [
                            'orders' => $orders,
                            'dayhead' => $datrow,
                            'brand' => $brand,
                        ];
                        $content.=$this->load->view('printscheduler/ontimeorder_day_view', $options, TRUE);
                    }
                    $mdata['content'] = $content;
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function dayscheduler()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Print Date';
            $postdata = $this->input->post();
            $printdate = ifset($postdata,'printdate', '');
            $brand = ifset($postdata,'brand', 'SR');
            if (!empty($printdate)) {
                $error = '';
                $orders = $this->printscheduler_model->get_dayorders($printdate, $brand);
                $dateview = $this->load->view('printscheduler/daydetails_date_view', ['date' => strtotime($printdate)], TRUE);
                $stockview = $this->load->view('printscheduler/daydetails_stocks_view', ['stocks' => $orders['stocks'], 'brand' => $brand], TRUE);
                $plateview = $this->load->view('printscheduler/daydetails_plates_view', ['plates' => $orders['plates'], 'brand' => $brand], TRUE);
                $options = [
                    'dateview' => $dateview,
                    'stockview' => $stockview,
                    'plateview' => $plateview,
                ];
                $mdata['content'] = $this->load->view('printscheduler/daydetails_view', $options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}