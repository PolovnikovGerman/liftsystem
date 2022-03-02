<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for proof requests queries

class Purchaseorders extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    // Purchase Orders
    // Purchase orders data
    public function purchaseorderdat() {
        if ($this->isAjax()) {
            $error = '';
            $mdata = array();
            $this->load->model('orders_model');
            $this->load->model('payments_model');
            $postdata = $this->input->post();
            // Build Options for form
            $page = ifset($postdata, 'offset', 0);
            $limit = ifset($postdata, 'limit', 30);
            $order_by = ifset($postdata, 'order_by', 'order_id');
            $direct = ifset($postdata, 'direction', 'asc');
            $search = array();
            if (isset($postdata['status']) && !empty($postdata['status'])) {
                $search['status'] = $postdata['status'];
            }
            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                $search['vendor_id'] = $postdata['vendor_id'];
            }
            if (isset($postdata['searchpo']) && !empty($postdata['searchpo'])) {
                $search['searchpo'] = $postdata['searchpo'];
            }
            $brand = ifset($postdata, 'brand');
            $search['brand'] = $brand;
            // $year_pay=((isset($postdata['year_pay']) && !empty($postdata['year_pay'])) ? $postdata['year_pay'] : date('Y'));

            $placedshow = ifset($postdata, 'placedpo', 'show');

            // $totalnotplaced=(isset($postdata['totalnotplaced']) ? $postdata['totalnotplaced'] : 0);
            $viewnonplace = 0;

            if ($page == 0 && $placedshow == 'show') {
                // Get content of NOT Placed orders
                $data = $this->orders_model->get_notplaced_orders($this->USR_ID, $search);
                if (count($data) > 0) {
                    $viewnonplace = 1;
                    $maxdata = count($data['stock']);
                    $maxview = 'stock';
                    $stok_options = array('label' => 'stock', 'data' => $data['stock'],);
                    $nonplace_content = $this->load->view('fulfillment/ordernotplaced_section_view', $stok_options, TRUE);
                    if (count($data['domestic']) > $maxdata) {
                        $maxdata = count($data['domestic']);
                        $maxview = 'domestic';
                    }
                    $domestic_options = array('label' => 'domestic', 'data' => $data['domestic'],);
                    $nonplace_content .= $this->load->view('fulfillment/ordernotplaced_section_view', $domestic_options, TRUE);
                    if (count($data['chinese']) > $maxdata) {
                        $maxview = 'chinese';
                    }
                    $chinese_options = array('data' => $data['chinese'],);
                    $nonplace_content .= $this->load->view('fulfillment/ordernotplaced_section_view', $chinese_options, TRUE);
                    $mdata['nonplace_content'] = $nonplace_content;
                    $mdata['maxview'] = $maxview;
                }
            }
            $mdata['viewnonplace'] = $viewnonplace;
            $offset = $page * $limit;
            /* Fetch data about prices */
            $ordersdat = $this->payments_model->get_purchorders($search, $order_by, $direct, $limit, $offset, $this->USR_ID);

            if (count($ordersdat) == 0) {
                $content = $this->load->view('fulfillment/purchaseorders_empty_view', array(), TRUE);
            } else {
                $data = array('orders' => $ordersdat,);
                $content = $this->load->view('fulfillment/purchaseorders_data_view', $data, TRUE);
            }
            $mdata['content'] = $content;
            // $mdata['paym']=$paymview;
            // $mdata['unbil']=$unbilled;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchasenotplaced_add() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('orders_model');
            $this->load->model('vendors_model');
            $v_options = [
                'order_by' => 'v.vendor_name',
            ];
            $vendors=$this->vendors_model->get_vendors_list($v_options);
            $methods=$this->orders_model->get_methods_edit();
            $order_id=$this->input->post('order_id');
            // Get data about order
            $order_data=$this->orders_model->get_order_detail($order_id);
            $amount_data=array(
                'amount_id'=>0,
                'amount_date'=>time(),
                'order_id'=>$order_id,
                'amount_sum'=>0,
                'oldamount_sum'=>0,
                'vendor_id'=>'',
                'method_id'=>'',
                'is_shipping'=>$order_data['is_shipping'],
                'lowprofit'=>'',
                'reason'=>'',
            );

            $data=array(
                'amount'=>$amount_data,
                'order'=>$order_data,
                'attach'=>array(),
            );
            // Save Data to Session
            usersession('editpurchase', $data);

            $order_view=$this->load->view('fulfillment/purchase_orderdata_view', $order_data,TRUE);
            $options=array(
                'order'=>$order_data,
                'amount'=>$amount_data,
                'attach'=>'',
                'vendors'=>$vendors,
                'methods'=>$methods,
                'order_view'=>$order_view,
                'lowprofit_view'=>'',
                'editpo_view'=>'',
            );
            // $content=$this->load->view('finance/edit_purchasenotplaced_view',$options,TRUE);
            $content=$this->load->view('fulfillment/purchase_orderedit_view',$options,TRUE);
            $mdata['content']=$content;
            $mdata['title'] = 'Purchase for NOT Placed Order';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchaseorder_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $amount_id=$this->input->post('amount_id');
            $this->load->model('vendors_model');
            $this->load->model('orders_model');
            $this->load->model('payments_model');
            $v_options = [
                'order_by' => 'v.vendor_name',
            ];
            $vendors=$this->vendors_model->get_vendors_list($v_options);
            $methods=$this->orders_model->get_methods_edit();
            $lowprofit_view='';
            $editpo_view='';
            $attach=array();
            if ($amount_id==0) {
                /* New Order */
                $amount=array(
                    'amount_id'=>0,
                    'order_id'=>'',
                    'amount_date'=>time(),
                    'order_num'=>'',
                    'amount_sum'=>'',
                    'vendor_id'=>'',
                    'method_id'=>'',
                    'is_shipping'=>1,
                    'oldamount_sum'=>0,
                    'lowprofit'=>'',
                    'reason'=>'',
                );
                $order_data=array();
                $order_view=$this->load->view('fulfillment/purchase_orderinput_view', array(), TRUE);
            } else {
                $res=$this->payments_model->get_purchase_order($amount_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $amount = $res['data'];
                    $amount['oldamount_sum']=$amount['amount_sum'];
                    $amount['reason']='';
                    $order_data=$this->orders_model->get_order_detail($amount['order_id']);
                    $amount['lowprofit']=$order_data['reason'];
                    if (floatval($order_data['profit_perc'])<$this->config->item('minimal_profitperc')) {
                        $lowprofit_view=$this->load->view('fulfillment/lowprofit_reason_view',array('reason'=>$order_data['reason']),TRUE);
                    }
                    $order_view=$this->load->view('fulfillment/purchase_orderdata_view', $order_data, TRUE);
                    $editpo_view=$this->load->view('fulfillment/pochange_reason_view', array('reason'=>''),TRUE);
                    $attach=$this->payments_model->get_amount_attachments($amount_id);
                }
                // Save data to Session
            }
            if (empty($error)) {
                $data=array(
                    'amount'=>$amount,
                    'order'=>$order_data,
                    'attach'=>$attach,
                );
                // Save Data to Session
                usersession('editpurchase', $data);
                // Content
                $options=array(
                    'order'=>$order_data,
                    'amount'=>$amount,
                    'attach'=>'',
                    'vendors'=>$vendors,
                    'methods'=>$methods,
                    'order_view'=>$order_view,
                    'lowprofit_view'=>$lowprofit_view,
                    'editpo_view'=>$editpo_view,
                );
                // $content=$this->load->view('finance/edit_purchasenotplaced_view',$options,TRUE);
                $content=$this->load->view('fulfillment/purchase_orderedit_view',$options,TRUE);
                $mdata['content']=$content;
                $mdata['title'] = 'Purchase for NOT Placed Order';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchaseorder_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty PO#';
            /* Order Num */
            $order_num=$this->input->post('order_num');
            if ($order_num) {
                $this->load->model('payments_model');
                $data=$this->payments_model->get_order_bynum($order_num);
                $error = $data['msg'];
                if ($data['result']==$this->success_result) {
                    // Save Data to Session
                    $error = '';
                    $res = $data['data'];
                    $this->load->model('orders_model');
                    $amtdata=usersession('editpurchase');
                    $amount=$amtdata['amount'];
                    $order_data=$this->orders_model->get_order_detail($res['order_id']);
                    $amount['order_id']=$res['order_id'];
                    $amount['order_num']=$order_data['order_num'];
                    $mdata['content']=$this->load->view('fulfillment/purchase_orderdata_view', $order_data, TRUE);
                    // Save new Data To session
                    $newdata=array(
                        'amount'=>$amount,
                        'order'=>$order_data,
                        'attach'=>$amtdata['attach'],
                    );
                    usersession('editpurchase', $newdata);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchaseorder_amountchange() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Time for change expired';
            $fld=$this->input->post('fld');
            $value=$this->input->post('value');
            $amntdata=usersession('editpurchase');
            if (!empty($amntdata)) {
                $this->load->model('payments_model');
                $res=$this->payments_model->change_amount($amntdata, $fld, $value);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['profit_class']=$res['profit_class'];
                    $mdata['profit_perc']=$res['profit_perc'];
                    $mdata['profit']=$res['profit'];
                    $mdata['reason']=$res['reason'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchaseorder_amountsave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Time for change expired';
            $amntdata=usersession('editpurchase');
            $brand = $this->input->post('brand');
            if (!empty($amntdata)) {
                $amntdata['user_id']=$this->USR_ID;
                $amntdata['brand'] = $brand;
                $this->load->model('payments_model');
                $res=$this->payments_model->save_poamount($amntdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $options=array(
                        'status'=>'showclosed',
                        'brand' => $brand,
                    );
                    $total_rec=$this->payments_model->get_count_purchorders($options);
                    $mdata['totals']=$total_rec;
                    // Clean Session
                    usersession('editpurchase', NULL);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchaseorder_delete() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='You have no permissions for this function';
            if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin' || $this->USR_ROLE=='PO Placer') {
                $amount_id=$this->input->post('amount_id');
                $brand = $this->input->post('brand');
                $this->load->model('payments_model');
                $res=$this->payments_model->delete_amount($amount_id, $this->USR_ID, $brand);
                if ($res==0) {
                    $error='Amount was not deleted';
                } else {
                    $error='';
                    $options =['brand' => $brand];
                    $mdata['totals']=$this->payments_model->get_count_purchorders($options);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Count purchase orders
    public function purchaseorder_search() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $status=$this->input->post('showtype');
            $vendor_id=$this->input->post('vendor_id');
            $placedshow=$this->input->post('placedpo');
            $searchpo=$this->input->post('searchpo');
            $brand = $this->input->post('brand');
            $options=array();
            if ($status) {
                $options['status']=$status;
            }
            if ($vendor_id) {
                $options['vendor_id']=$vendor_id;
            }
            $options['placedpo']=$placedshow;
            if ($searchpo) {
                $options['searchpo']=$searchpo;
            }
            if (!empty($brand)) {
                $options['brand'] = $brand;
            }
            $this->load->model('payments_model');
            $mdata['total']=$this->payments_model->get_count_purchorders($options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Vendors totasl
    public function purchase_vendortotals() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $year = ifset($postdata,'year',0);
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error='';
                $this->load->model('payments_model');
                $years=$this->payments_model->get_years($brand);
                if ($year==0) {
                    $year = $years[0];
                }
                $payvend=$this->payments_model->get_vendorpayment($brand, $year);
                $payoptions=array(
                    'payments'=>$payvend,
                    'year'=>$year,
                    'years'=>$years,
                );
                $mdata['content']=$this->load->view('fulfillment/purchase_vendortotals_view',$payoptions,TRUE);

            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchasemethods_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $mdata['title'] = 'PO Payment Methods';
            $this->load->model('payments_model');
            $active = $this->payments_model->get_purchase_methods(1);
            $options = [];
            $options['active']='';
            if (count($active)>0) {
                $options['active']=$this->load->view('fulfillment/purchase_methods_view',['methods' => $active,'active' => 1],TRUE);
            }
            $inactive = $this->payments_model->get_purchase_methods(0);
            $options['inactive']='';
            if (count($inactive)>0) {
                $options['inactive']=$this->load->view('fulfillment/purchase_methods_view',['methods' => $inactive, 'active' => 0],TRUE);
            }
            $mdata['content'] = $this->load->view('fulfillment/manage_purchase_view', $options, true);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchasemethod_status() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $method_id = ifset($postdata,'method',0);
            $status = ifset($postdata, 'action',0);

            $this->load->model('payments_model');
            $res = $this->payments_model->update_method_status($method_id, $status);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $active = $this->payments_model->get_purchase_methods(1);

                $mdata['active']='';
                if (count($active)>0) {
                    $mdata['active']=$this->load->view('fulfillment/purchase_methods_view',['methods' => $active,'active' => 1],TRUE);
                }
                $inactive = $this->payments_model->get_purchase_methods(0);
                $mdata['inactive']='';
                if (count($inactive)>0) {
                    $mdata['inactive']=$this->load->view('fulfillment/purchase_methods_view',['methods' => $inactive, 'active' => 0],TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function purchasemethod_new() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $method_name = ifset($postdata,'method','');
            $this->load->model('payments_model');
            $res = $this->payments_model->purchase_method_add($method_name);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $active = $this->payments_model->get_purchase_methods(1);

                $mdata['active']='';
                if (count($active)>0) {
                    $mdata['active']=$this->load->view('fulfillment/purchase_methods_view',['methods' => $active,'active' => 1],TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function pototals_details() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = '';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand','ALL');
            $inner = ifset($postdata,'inner', 0);
            $this->load->model('orders_model');
            $unsign = $this->orders_model->purchaseorder_details('unsign', $inner, $brand);
            $approv = $this->orders_model->purchaseorder_details('approved', $inner, $brand);
            $proof = $this->orders_model->purchaseorder_details('proof', $inner, $brand);
            $unsignview = $approvview = $needproofview = '';
            if (count($unsign) > 0) {
                $unsignview = $this->load->view('pototals/pototals_details_view',['datas' => $unsign], TRUE);
//            } else {
//                $unsignview = $this->load->view('pototals/test_table_view.php',[],TRUE);
            }
            if (count($approv) > 0) {
                $approvview = $this->load->view('pototals/pototals_details_view',['datas' => $approv], TRUE);
            }
            if (count($proof) > 0) {
                $needproofview = $this->load->view('pototals/pototals_details_view',['datas' => $proof], TRUE);
            }
            $mdata['unsignview'] = $unsignview;
            $mdata['approvview'] = $approvview;
            $mdata['needproofview'] = $needproofview;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


}