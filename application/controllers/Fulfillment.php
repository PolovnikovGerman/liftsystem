<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fulfillment extends MY_Controller
{

    private $pagelink = '/fulfillment';

    private $container_with=60;
    private $maxlength=240;
    private $needlistlength=180;
    private $salesreplength=300;

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
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Fulfillment';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);

        $brands = $this->menuitems_model->get_brand_permisions($this->USR_ID, $this->pagelink);
        if (count($brands)==0) {
            redirect('/');
        }
        $brand = $brands[0]['brand'];
        $top_options = [
            'brands' => $brands,
            'active' => $brand,
        ];
        $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#vendorsview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/vendorsview.css');
                $head['scripts'][]=array('src'=>'/js/fulfillment/vendorsview.js');
                $content_options['vendorsview'] = $this->_prepare_vendors_view();
            } elseif ($row['item_link']=='#fullfilstatusview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/postatus.css');
                $head['scripts'][]=array('src'=>'/js/fulfillment/postatus.js');
                $content_options['fullfilstatusview'] = $this->_prepare_status_view($brand, $top_menu);
            } elseif ($row['item_link']=='#pototalsview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/pototals.css');
                $head['scripts'][]=array('src'=>'/js/fulfillment/pototals.js');
                $content_options['pototalsview'] = $this->_prepare_pototals_view($brand, $top_menu);
            } elseif ($row['item_link']=='#printshopinventview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/inventory.css');
                $head['scripts'][]=array('src'=>'/js/fulfillment/inventory.js');
                $content_options['printshopinventview'] = $this->_prepare_printshop_inventory($brand, $top_menu);
            }
        }
        $content_options['menu'] = $menu;
        $content_view = $this->load->view('fulfillment/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/fulfillment/page.js');
        $head['styles'][] = array('style' => '/css/fulfillment/fulfilmpage.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // DatePicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function vendordata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $page_num = ifset($postdata, 'offset', 0);
            $limit = ifset($postdata, 'limit', 100);
            $offset = $page_num * $limit;
            $order_by = ifset($postdata,'order_by','vendor_name');
            $direction = ifset($postdata, 'direction','asc');
            $options = [
                'offset' => $offset,
                'limit' => $limit,
                'order_by' => $order_by,
                'direct' => $direction,
            ];
            $this->load->model('vendors_model');
            $vendors=$this->vendors_model->get_vendors_list($options);
            if (count($vendors)==0) {
                $content=$this->load->view('fulfillment/vendors_emptydata_view', array(), TRUE);
            } else {
                $content=$this->load->view('fulfillment/vendor_tabledat_view',array('vendors'=>$vendors),TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function vendor_remove() {
        if ($this->isAjax()) {
            $error = 'No permissions';
            $mdata = [];
            if ($this->USR_ROLE!=='general') {
                $vendor_id=$this->input->post('vendor_id');
                $this->load->model('vendors_model');
                $res=$this->vendors_model->delete_vendor($vendor_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['totals'] = $this->vendors_model->get_count_vendors();
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendor_edit() {
        if ($this->isAjax()) {
            $error = 'Vendor not found';
            $mdata = [];
            $postdata = $this->input->post();
            $vendor_id = ifset($postdata, 'vendor_id');
            if (!empty($vendor_id)) {
                $this->load->model('vendors_model');
                $this->load->model('calendars_model');
                $calendars=$this->calendars_model->get_calendars();
                if ($vendor_id==0) {
                    $error = '';
                    $data = $this->vendors_model->add_vendor();
                    $mdata['title'] = 'New Vendor';
                } else {
                    $res = $this->vendors_model->get_vendor($vendor_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = $res['data'];
                        $mdata['title'] = 'Change Vendor '.$data['vendor_name'];
                    }
                }
                if ($error =='') {
                    $mdata['content']=$this->load->view('fulfillment/vendor_formdata_view',array('vendor'=>$data,'calendars'=>$calendars),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendordata_save() {
        if ($this->isAjax()) {
            $mdata = [];
            $vendor_id=$this->input->post('vendor_id');
            $vendor_name=$this->input->post('vendor_name');
            $vendor_zipcode=$this->input->post('vendor_zipcode');
            $calendar_id=$this->input->post('calendar_id');
            $this->load->model('vendors_model');
            $res=$this->vendors_model->save_vendor($vendor_id,$vendor_name, $vendor_zipcode, $calendar_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['totals'] = $this->vendors_model->get_count_vendors();
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendor_includereport() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $vendor_id=$this->input->post('vendor_id');
            $payinclude=$this->input->post('payinclude');
            $this->load->model('vendors_model');
            $res=$this->vendors_model->vendor_includerep($vendor_id, $payinclude);
            $this->ajaxResponse($mdata, $error);
        }
    }

    // STATUS page
    // Status data
    public function statusdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $pagenum=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            $search=$this->input->post('search');
            $date_filter=$this->input->post('date_filter');
            $options_filter=$this->input->post('options_filter');
            $addsort=$this->input->post('addsort');
            $brand = $this->input->post('brand');
            $offset=$pagenum*$limit;

            /* Get data about orders */
            $options=array(
                'profit_perc'=>NULL,
                'is_canceled'=>0,
                'status_type'=>'O',
                'brand' => $brand,
            );
            if ($date_filter==1) {
                $dat=strtotime(date('m/d/Y',time())." -6 months");
                $options['min_time']=$dat;
            }
            if ($options_filter!='') {
                $options['order_status']=$options_filter;
            }
            if ($search!='') {
                $options['search']=$search;
            }
            $this->load->model('orders_model');
            $orders=$this->orders_model->get_orderslimits($options,$order_by,$addsort,$direct,$limit,$offset);
            if (count($orders)>0) {
                $mdata['content']=$this->load->view('fulfillment/status_data_view',array('orders'=>$orders),TRUE);
            } else {
                $mdata['content']=$this->load->view('fulfillment/status_emptydata_view',array('orders'=>$orders),TRUE);
            }

            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Count # of records for new status of filter */
    function statussearchdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            /* Post params */
            $search=$this->input->post('search');
            $date_filter=$this->input->post('date_filter');
            $options_filter=$this->input->post('options_filter');
            $options=array(
                'profit_perc'=>NULL,
                'is_canceled'=>0,
            );
            if ($date_filter==1) {
                $dat=strtotime(date('m/d/Y',time())." -6 months");
                $options['min_time']=$dat;
            }
            if ($options_filter!='') {
                $options['order_status']=$options_filter;
            }
            if ($search!='') {
                $options['search']=$search;
            }
            /* Get New Total recors */
            $this->load->model('orders_model');
            $mdata['totalrec']=$this->orders_model->get_count_orderslimits($options);

            $this->ajaxResponse($mdata, $error);
        }
    }

    // Purchase Orders
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

            $vendors=$this->vendors_model->get_vendors_list('v.vendor_name');
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
            $vendors=$this->vendors_model->get_vendors_list('v.vendor_name');
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
                $res=$this->payments_model->delete_amount($amount_id, $this->USR_ID);
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
    // Inventory
    public function inventory_brand() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $this->load->model('printshop_model');
                $addcost=$this->printshop_model->invaddcost();
                $totalinv=$this->printshop_model->get_inventory_totals($brand);
                $mdata['maxsum'] = MoneyOutput($totalinv['maxsum']);
                $mdata['invetorytotal'] = $this->load->view('printshopinventory/total_inventory_view',$totalinv,TRUE);
                $data = $this->printshop_model->get_data_onboat($brand);
                $boathead_view='';
                foreach ($data as $drow) {
                    $boathead_view.=$this->load->view('printshopinventory/onboat_containerhead_view', $drow, TRUE);
                }
                // Build head content
                $slider_width=60*count($data);
                $margin = $this->maxlength-$slider_width;
                $margin=($margin>0 ? 0 : $margin);
                $boatoptions=array(
                    'data'=>$data,
                    'container_view'=>$boathead_view,
                    'width' => $slider_width,
                    'margin' => $margin,
                );
                $mdata['onboathead'] = $this->load->view('printshopinventory/onboathead_view', $boatoptions, TRUE);
                $mdata['width']=$slider_width.'px';
                $mdata['margin'] = $margin.'px';
                $mdata['download_view'] =$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Inventory Data
    public function inventory_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $postdata=$this->input->post();
            $brand = ifset($postdata,'brand');
            $options=array(
                'orderby'=>'item_num',
                'direct'=>'asc',
                'brand' => $brand,
            );

            $data=$this->printshop_model->get_printshopitems($options);

            $permission=$this->user_model->get_user_data($this->USR_ID);
            // Make Total Inv content
            $totaloptions=array(
                'data'=>$data['inventory'],
            );
            if (isset($postdata['salesreport']) && $postdata['salesreport']==1) {
                $mdata['totalinvcontent']=$this->load->view('invsalesrep/totalinventory_data_view', $totaloptions, TRUE);
                $specopt=array(
                    'data'=>$data['inventory'],
                    'permission'=>$permission['profit_view'],
                );

                $mdata['speccontent']=$this->load->view('invsalesrep/specinventory_data_view', $specopt, TRUE);
            } else {
                $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
                $specopt=array(
                    'data'=>$data['inventory'],
                    'permission'=>$permission['profit_view'],
                );

                $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
            }
            $mdata['total_inventory']=MoneyOutput($data['inventtotal']);
            // Get OnBoat Data
            $colors=$data['colors'];
            $boatdata = $this->printshop_model->get_data_onboat($brand);
            $containers_view='';
            foreach ($boatdata as $drow) {
                $boatcontndata=$this->printshop_model->get_container_view($drow['onboat_container'], $colors);
                $boptions=array(
                    'data'=>$boatcontndata,
                    'onboat_container'=>$drow['onboat_container'],
                    'onboat_status'=>$drow['onboat_status'],
                );
                $containers_view.=$this->load->view('printshopinventory/container_data_view', $boptions, TRUE);
            }
            // foreach ($data)
            $slider_width=60*(count($boatdata));
            if (isset($postdata['salesreport']) && $postdata['salesreport']==1) {
                $margin=$this->salesreplength-$slider_width;
            } else {
                $margin=$this->maxlength-$slider_width;
            }
            $boatoptions=array(
                'width'=>$slider_width,
                'margin'=>($margin>0 ? 0 : $margin),
                'boatcontent'=>$containers_view,
            );
            $mdata['onboatcontent']=$this->load->view('printshopinventory/onboatdata_view', $boatoptions, TRUE);
            // $mdata['width'] = $slider_width-240;
            $mdata['margin']=$margin;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    private function _prepare_vendors_view() {
        $this->load->model('vendors_model');
        $totals=$this->vendors_model->get_count_vendors();
        $options=array(
            'perpage'=> 250,
            'order'=>'vendor_name',
            'direc'=>'asc',
            'total'=>$totals,
            'curpage'=>0,
        );
        $content=$this->load->view('fulfillment/vendors_view', $options, TRUE);
        return $content;
    }

    private function _prepare_status_view($brand, $top_menu) {
        $dat=strtotime(date('m/d/Y',time())." -6 months");
        $def_options=array(
            'profit_perc'=>NULL,
            'is_canceled'=>0,
            'min_time'=>$dat,
            'brand' => $brand,
        );
        $this->load->model('orders_model');
        $totals=$this->orders_model->get_count_orderslimits($def_options);
        $options=array(
            'perpage'=> 250,
            'order'=>'order_proj_status',
            'direc'=>'asc',
            'total'=>$totals,
            'curpage'=>0,
            'brand' => $brand,
            'top_menu' => $top_menu,
        );
        $content=$this->load->view('fulfillment/status_view',$options,TRUE);
        return $content;
    }

    private function _prepare_pototals_view($brand, $top_menu) {
        $this->load->model('orders_model');
        $this->load->model('payments_model');
        $this->load->model('vendors_model');
        // $search_form
        $optionstotal=array(
            'status'=>'showclosed',
            'brand' => $brand,
        );
        $total_rec=$this->payments_model->get_count_purchorders($optionstotal);
        $total_notplaced=$this->orders_model->count_notplaced_orders(['brand'=>$brand]);

        $sort_array=array(
            'oa.amount_date-desc'=>'Date &#9660;',
            'oa.amount_date-asc'=>'Date &#9650;',
            'o.order_num-desc'=>'PO# &#9660;',
            'o.order_num-asc'=>'PO# &#9650;',
            'v.vendor_name-desc'=>'Vendor &#9660;',
            'v.vendor_name-asc'=>'Vendor &#9650;',
            'oa.amount_sum-desc'=>'Amount &#9660;',
            'oa.amount_sum-asc'=>'Amount &#9650;',
        );

        $vsort='v.vendor_name';
        $vendors=$this->vendors_model->get_vendors_list($vsort);

        $nonplaceview='';
        if ($total_notplaced!=0) {
            // Non placed
            $nonplaceview=$this->load->view('fulfillment/pototals_nonplacehead_view', array(), TRUE);
        }
        $perpages = $this->config->item('orders_perpage');
        $options=array(
            'total'=>$total_rec,
            'total_nonplaced'=>$total_notplaced,
            'nonplacedview'=>$nonplaceview,
            'order'=>'oa.amount_date desc',
            'direc'=>'',
            'curpage'=>0,
            'curstatus'=>'showclosed',
            'showplace'=>'show',
            'sort'=>$sort_array,
            'current_sort'=>'oa.amount_date-desc',
            'brand' => $brand,
            'top_menu' => $top_menu,
            'perpages' => $perpages,
            'perpage' => $perpages[0],
            'vendors' => $vendors,
        );
        return $this->load->view('fulfillment/pototals_head_view',$options,TRUE);
    }

    private function _prepare_printshop_inventory($brand, $top_menu) {
        $this->load->model('printshop_model');
        $addcost=$this->printshop_model->invaddcost();
        $totals=$this->printshop_model->count_prinshop_items();
        $totalinv=$this->printshop_model->get_inventory_totals($brand);
        $totalinvview=$this->load->view('printshopinventory/total_inventory_view',$totalinv,TRUE);
        $data = $this->printshop_model->get_data_onboat($brand);
        $boathead_view='';
        foreach ($data as $drow) {
            $boathead_view.=$this->load->view('printshopinventory/onboat_containerhead_view', $drow, TRUE);
        }
        // Build head content
        // $slider_width=60*count($data);
        $slider_width=60*count($data);
        $margin = $this->maxlength-$slider_width;
        $margin=($margin>0 ? 0 : $margin);
        $width_edit = 58;
        $boatoptions=array(
            'data'=>$data,
            'container_view'=>$boathead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $onboat_content=$this->load->view('printshopinventory/onboathead_view', $boatoptions, TRUE);

        $permission=$this->user_model->get_user_data($this->USR_ID);
        $download_view=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
        $headoptions=array(
            'permission' => $permission['profit_view'],
            'addcost'=>$addcost,
            'data' => $data,
            'width' => $slider_width,
            'margin' => $margin,
            'onboathead'=>$onboat_content,
            'invetorytotal'=>$totalinvview,
            'download_view'=>$download_view,
        );
        $headview=$this->load->view('printshopinventory/fullview_head_view', $headoptions,TRUE);

        /*$specs_disc = $this->printshop_model->get_color_disc();*/

        $invoption=array(
            'totals'=>$totals,
            'fullview'=>$headview,
            'maxsum'=>$totalinv['maxsum'],
            'brand' => $brand,
            'top_menu' => $top_menu,
        );
        $content=$this->load->view('printshopinventory/page_view', $invoption, TRUE);
        return $content;
    }

}