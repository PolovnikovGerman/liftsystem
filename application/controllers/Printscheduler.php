<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Printscheduler extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
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
                $this->load->model('printscheduler_model');
                if ($calendar=='past') {
                    $res = $this->printscheduler_model->get_pastorders($brand);
                    $options = [
                        'orders' => $res,
                        'numorders' => count($res),
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
}