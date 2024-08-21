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
                $alltotals = [
                    'prints' => $unassignorders['totals']['prints'],
                    'items' => $unassignorders['totals']['items'],
                    'orders' => $unassignorders['totals']['orders'],
                ];
                $printoptions = [];
                $printoptions['unsignview'] = $this->load->view('printscheduler/daydetails_unsigns_view', ['orders' => $unassignorders['orders'], 'total' => $unassignorders['totals'], 'users' => $userlist, 'brand' => $brand], TRUE);
                $printusers = $this->printscheduler_model->get_day_assignusers($printdate, $brand);
                $assignview = '';
                foreach ($printusers as $printuser) {
                    $assignorders = $this->printscheduler_model->get_dayassignorders($printdate, $printuser['user_id'], $brand);
                    $usroptions = [
                        'user_name' => $printuser['user_name'],
                        'orders' => $assignorders['orders'],
                        'totals' => $assignorders['totals'],
                        'brand' => $brand,
                    ];
                    $assignview.=$this->load->view('printscheduler/daydetails_assigns_view', $usroptions, TRUE);
                    $alltotals['prints']+=$assignorders['totals']['prints'];
                    $alltotals['items']+=$assignorders['totals']['items'];
                    $alltotals['orders']+=$assignorders['totals']['orders'];
                }
                $printoptions['assignview'] = $assignview;
                $printoptions['totals'] = $alltotals;
                $printview = $this->load->view('printscheduler/daydetails_printorders_view', $printoptions, TRUE);
                // Build ready to ship
                $shipready = $this->printscheduler_model->getreadyshiporders($printdate, $brand);
                $readyshipview = $this->load->view('printscheduler/daydetails_readyshiporders_view',['orders' => $shipready['orders'], 'totals'=> $shipready['totals'], 'brand' => $brand], TRUE);
                // Completed Printjob
                $completed_users = $this->printscheduler_model->get_day_completedusers($printdate, $brand);
                $totalcomlet = [
                    'orders' => 0,
                    'prints' => 0,
                    'items' => 0,
                ];
                $completedview = '';
                foreach ($completed_users as $completeduser) {
                    $compljob = $this->printscheduler_model->getcompleteprintorders($printdate, $completeduser['user_id'], $brand);
                    $comploptions = [
                        'user_name' => $completeduser['user_name'],
                        'totals' => $compljob['totals'],
                        'orders' => $compljob['orders'],
                    ];
                    $completedview.=$this->load->view('printscheduler/daydetails_completed_users_view', $comploptions, TRUE);
                    $totalcomlet['orders']+=$compljob['totals']['orders'];
                    $totalcomlet['items']+=$compljob['totals']['items'];
                    $totalcomlet['prints']+=$compljob['totals']['prints'];
                }
                $complljobview = $this->load->view('printscheduler/daydetails_completedorders_view',['totals' => $totalcomlet, 'content' => $completedview], TRUE);
                // Shipped
                $shipres = $this->printscheduler_model->getshippedorders($printdate, $brand);
                $shippedview = $this->load->view('printscheduler/daydetails_shippeddorders_view',['totals' => $shipres['totals'], 'orders' => $shipres['orders']], TRUE);
                $options = [
                    'dateview' => $dateview,
                    'stockview' => $stockview,
                    'plateview' => $plateview,
                    'printview' => $printview,
                    'readyship' => $readyshipview,
                    'completed' => $complljobview,
                    'shippedview' => $shippedview,
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
                    $printdate = $res['printdate'];
                    $this->load->model('user_model');
                    $userlist = $this->user_model->get_printschedul_users();
                    $unassignorders = $this->printscheduler_model->get_dayunassignorders($printdate, $brand);
                    $printoptions = [];
                    $printoptions['unsignview'] = $this->load->view('printscheduler/daydetails_unsigns_view', ['orders' => $unassignorders['orders'], 'total' => $unassignorders['totals'], 'users' => $userlist, 'brand' => $brand], TRUE);
                    $alltotals = [
                        'prints' => $unassignorders['totals']['prints'],
                        'items' => $unassignorders['totals']['items'],
                        'orders' => $unassignorders['totals']['orders'],
                    ];
                    // Get list of print users
                    $printusers = $this->printscheduler_model->get_day_assignusers($printdate, $brand);
                    $assignview = '';
                    foreach ($printusers as $printuser) {
                        $assignorders = $this->printscheduler_model->get_dayassignorders($printdate, $printuser['user_id'], $brand);
                        $usroptions = [
                            'user_name' => $printuser['user_name'],
                            'orders' => $assignorders['orders'],
                            'totals' => $assignorders['totals'],
                            'brand' => $brand,
                        ];
                        $assignview.=$this->load->view('printscheduler/daydetails_assigns_view', $usroptions, TRUE);
                        $alltotals['prints']+=$assignorders['totals']['prints'];
                        $alltotals['items']+=$assignorders['totals']['items'];
                        $alltotals['orders']+=$assignorders['totals']['orders'];
                    }
                    $printoptions['assignview'] = $assignview;
                    $printoptions['totals'] = $alltotals;
                    $mdata['content'] = $this->load->view('printscheduler/daydetails_printorders_view', $printoptions, TRUE);
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
            $postdata = $this->input->post();
            $error = 'Empty Order';
            $order_id = ifset($postdata, 'order', '');
            $user_id = ifset($postdata, 'user','');
            $brand = ifset($postdata, 'brand', 'SR');
            if (!empty($order_id) && !empty($user_id)) {
                // Assign order
                $res = $this->printscheduler_model->assignorder($order_id, $user_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $printdate = $res['printdate'];
                    $this->load->model('user_model');
                    $userlist = $this->user_model->get_printschedul_users();
                    $unassignorders = $this->printscheduler_model->get_dayunassignorders($printdate, $brand);
                    $printoptions = [];
                    $printoptions['unsignview'] = $this->load->view('printscheduler/daydetails_unsigns_view', ['orders' => $unassignorders['orders'], 'total' => $unassignorders['totals'], 'users' => $userlist, 'brand' => $brand], TRUE);
                    $alltotals = [
                        'prints' => $unassignorders['totals']['prints'],
                        'items' => $unassignorders['totals']['items'],
                        'orders' => $unassignorders['totals']['orders'],
                    ];
                    // Get list of print users
                    $printusers = $this->printscheduler_model->get_day_assignusers($printdate, $brand);
                    $assignview = '';
                    foreach ($printusers as $printuser) {
                        $assignorders = $this->printscheduler_model->get_dayassignorders($printdate, $printuser['user_id'], $brand);
                        $usroptions = [
                            'user_name' => $printuser['user_name'],
                            'orders' => $assignorders['orders'],
                            'totals' => $assignorders['totals'],
                            'brand' => $brand,
                        ];
                        $assignview.=$this->load->view('printscheduler/daydetails_assigns_view', $usroptions, TRUE);
                        $alltotals['prints']+=$assignorders['totals']['prints'];
                        $alltotals['items']+=$assignorders['totals']['items'];
                        $alltotals['orders']+=$assignorders['totals']['orders'];
                    }
                    $printoptions['assignview'] = $assignview;
                    $printoptions['totals'] = $alltotals;
                    $mdata['content'] = $this->load->view('printscheduler/daydetails_printorders_view', $printoptions, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function outcome()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order';
            $postdata = $this->input->post();
            // Params
            $order_itemcolor_id = ifset($postdata,'itemcolor','');
            $inventory_color_id = ifset($postdata,'inventcolor','');
            $shipped = intval(ifset($postdata,'shipped',0));
            $kepted = floatval(ifset($postdata,'kepted',0));
            $misprint = floatval(ifset($postdata,'misprint',0));
            $plates = floatval(ifset($postdata,'plates',0));
            $brand = ifset($postdata,'brand','SR');
            if (!empty($order_itemcolor_id) && !empty($inventory_color_id)) {
                $res = $this->printscheduler_model->outcome($order_itemcolor_id, $inventory_color_id, $shipped, $kepted, $misprint, $plates, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $amount_id = $res['printshop_income_id'];
                    // Add MInventory movement
                    $invres = $this->printscheduler_model->save_inventory_outcome($amount_id, $this->USR_ID);
                    $error = $invres['msg'];
                    if ($invres['result']==$this->success_result) {
                        $error = '';
                        $printdate = $res['printdate'];
                        // Create content
                        $orders = $this->printscheduler_model->get_dayorders($printdate, $brand);
                        $mdata['plateview'] = $this->load->view('printscheduler/daydetails_plates_view', ['plates' => $orders['plates'], 'brand' => $brand], TRUE);
                        $this->load->model('user_model');
                        $userlist = $this->user_model->get_printschedul_users();
                        $unassignorders = $this->printscheduler_model->get_dayunassignorders($printdate, $brand);
                        $alltotals = [
                            'prints' => $unassignorders['totals']['prints'],
                            'items' => $unassignorders['totals']['items'],
                            'orders' => $unassignorders['totals']['orders'],
                        ];
                        $printoptions = [];
                        $printoptions['unsignview'] = $this->load->view('printscheduler/daydetails_unsigns_view', ['orders' => $unassignorders['orders'], 'total' => $unassignorders['totals'], 'users' => $userlist, 'brand' => $brand], TRUE);
                        $printusers = $this->printscheduler_model->get_day_assignusers($printdate, $brand);
                        $assignview = '';
                        foreach ($printusers as $printuser) {
                            $assignorders = $this->printscheduler_model->get_dayassignorders($printdate, $printuser['user_id'], $brand);
                            $usroptions = [
                                'user_name' => $printuser['user_name'],
                                'orders' => $assignorders['orders'],
                                'totals' => $assignorders['totals'],
                                'brand' => $brand,
                            ];
                            $assignview.=$this->load->view('printscheduler/daydetails_assigns_view', $usroptions, TRUE);
                            $alltotals['prints']+=$assignorders['totals']['prints'];
                            $alltotals['items']+=$assignorders['totals']['items'];
                            $alltotals['orders']+=$assignorders['totals']['orders'];
                        }
                        $printoptions['assignview'] = $assignview;
                        $printoptions['totals'] = $alltotals;
                        $mdata['printview'] = $this->load->view('printscheduler/daydetails_printorders_view', $printoptions, TRUE);
                        // Build ready to ship
                        $shipready = $this->printscheduler_model->getreadyshiporders($printdate, $brand);
                        $mdata['readyshipview'] = $this->load->view('printscheduler/daydetails_readyshiporders_view',['orders' => $shipready['orders'], 'totals'=> $shipready['totals'], 'brand' => $brand], TRUE);
                        // Completed Printjob
                        $completed_users = $this->printscheduler_model->get_day_completedusers($printdate, $brand);
                        $totalcomlet = [
                            'orders' => 0,
                            'prints' => 0,
                            'items' => 0,
                        ];
                        $completedview = '';
                        foreach ($completed_users as $completeduser) {
                            $compljob = $this->printscheduler_model->getcompleteprintorders($printdate, $completeduser['user_id'], $brand);
                            $comploptions = [
                                'user_name' => $completeduser['user_name'],
                                'totals' => $compljob['totals'],
                                'orders' => $compljob['orders'],
                            ];
                            $completedview.=$this->load->view('printscheduler/daydetails_completed_users_view', $comploptions, TRUE);
                            $totalcomlet['orders']+=$compljob['totals']['orders'];
                            $totalcomlet['items']+=$compljob['totals']['items'];
                            $totalcomlet['prints']+=$compljob['totals']['prints'];
                        }
                        $mdata['complljobview'] = $this->load->view('printscheduler/daydetails_completedorders_view',['totals' => $totalcomlet, 'content' => $completedview], TRUE);
                        // Shipped
                        $shipres = $this->printscheduler_model->getshippedorders($printdate, $brand);
                        $mdata['shippedview'] = $this->load->view('printscheduler/daydetails_shippeddorders_view',['totals' => $shipres['totals'], 'orders' => $shipres['orders']], TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}