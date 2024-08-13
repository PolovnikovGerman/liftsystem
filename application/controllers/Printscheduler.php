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
                $dates = $this->printscheduler_model->get_ontimeorders_dates($brand);
                $leftactive = $rightactive = 1;
                if ($dates[0]['printdate']==$printdate) {
                    $leftactive = 0;
                }
                $datesidx = count($dates)-1;
                if ($dates[$datesidx]['printdate']==$printdate) {
                    $rightactive = 0;
                }
                $datoptions = [
                    'dates' => $dates,
                    'printdate' => $printdate,
                    'brand' => $brand,
                    'prevactive' => $leftactive,
                    'nxtactive' => $rightactive,
                ];
                $dateview = $this->load->view('printscheduler/daydetails_date_view', $datoptions, TRUE);
                $orders = $this->printscheduler_model->get_dayorders($printdate, $brand);
                $stockview = $this->load->view('printscheduler/daydetails_stocks_view', ['stocks' => $orders['stocks'], 'brand' => $brand], TRUE);
                $plateview = $this->load->view('printscheduler/daydetails_plates_view', ['plates' => $orders['plates'], 'brand' => $brand], TRUE);
                $this->load->model('user_model');
                $userlist = $this->user_model->get_printschedul_users();
                $unassignorders = $this->printscheduler_model->get_dayunassignorders($printdate, $brand);
                $printoptions = [];
                $printoptions['unsignview'] = $this->load->view('printscheduler/daydetails_unsigns_view', ['orders' => $unassignorders['orders'], 'total' => $unassignorders['totals'], 'users' => $userlist, 'brand' => $brand], TRUE);
                $printoptions['assignview'] = '';
                $printview = $this->load->view('printscheduler/daydetails_printorders_view', $printoptions, TRUE);
                $options = [
                    'dateview' => $dateview,
                    'stockview' => $stockview,
                    'plateview' => $plateview,
                    'printview' => $printview,
                ];
                $mdata['content'] = $this->load->view('printscheduler/daydetails_view', $options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function stockdonecheck()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $error = 'Empty Order';
            $order_id = ifset($postdata, 'order','');
            $brand = ifset($postdata,'brand', 'SR');
            if (!empty($order_id)) {
                // Update print_ready
                $res = $this->printscheduler_model->stockdonecheck($order_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $this->load->model('user_model');
                    $userlist = $this->user_model->get_printschedul_users();
                    $unassignorders = $this->printscheduler_model->get_dayunassignorders($res['printdate'], $brand);
                    $printoptions = [];
                    $printoptions['unsignview'] = $this->load->view('printscheduler/daydetails_unsigns_view', ['orders' => $unassignorders['orders'], 'total' => $unassignorders['totals'], 'users' => $userlist, 'brand' => $brand], TRUE);
                    $printoptions['assignview'] = '';
                    $mdata['content'] = $this->load->view('printscheduler/daydetails_printorders_view', $printoptions, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}