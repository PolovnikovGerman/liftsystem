<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fulfillment extends MY_Controller
{

    private $pagelink = '/fulfillment';

    private $container_with=60;
    private $maxlength=240;
    private $needlistlength=180;
    private $salesreplength=300;
    protected $restore_invdata_error='Connection Lost. Please, recall function';

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
            } elseif ($row['item_link']=='#invneedlistview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/invneedlistview.css');
                $head['scripts'][] = array('src'=>'/js/fulfillment/invneedlistview.js');
                $content_options['invneedlistview'] = $this->_prepare_needlist_view($brand, $top_menu);
            } elseif ($row['item_link']=='#salesrepinventview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/salesrepinventview.css');
                $head['scripts'][] = array('src'=>'/js/fulfillment/salesrepinventview.js');
                $content_options['salesrepinventview'] = $this->_prepare_inventsalesrep_view($brand, $top_menu);
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
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
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
    // Change Brand
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
    // Change cost
    public function inventory_addcost() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $this->load->model('printshop_model');
            $postdata = $this->input->post();
            $addcost=ifset($postdata,'cost',0);
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $this->load->model('printshop_model');
                $res=$this->printshop_model->inventory_addcost_upd($addcost);
                $options=array(
                    'orderby'=>'item_num',
                    'direct'=>'asc',
                    'brand' => $brand,
                );
                $data=$this->printshop_model->get_printshopitems($options);
                $permission=$this->user_model->get_user_data($this->USR_ID);
                // Make Total Inv content
                $mdata['total_inventory']=MoneyOutput($data['inventtotal']);
                $specopt=array(
                    'data'=>$data['inventory'],
                    'permission'=>$permission['profit_view'],
                );
                $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Item Data change
    public function inventory_item() {
        if ($this->isAjax()) {
            $this->load->model('printshop_model');
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $printshop_item_id=$postdata['printshop_item_id'];
            if ($printshop_item_id==0) {
                $item=$this->printshop_model->new_invent_item();
            } else {
                $res=$this->printshop_model->get_invent_item($printshop_item_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
                $item=$res['item'];
            }
            if (empty($error)) {
                $permission=$this->user_model->get_user_data($this->USR_ID);
                // Make Total Inv content
                $item['permission']=$permission['profit_view'];
                usersession('invitemdata', $item);
                $mdata['content']=$this->load->view('printshopinventory/invitem_itemdata_view', $item, TRUE);
                $mdata['addcontent']=$this->load->view('printshopinventory/invitem_itemaddons_view', $item, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Item Detail Value
    public function inventory_item_change() {
        if ($this->isAjax()) {
            $mdata = array();
            $error='Edit time expired. Recconect';
            $item=usersession('invitemdata');
            if (!empty($item)) {
                $this->load->model('printshop_model');
                $postdata = $this->input->post();
                $res=$this->printshop_model->invitem_item_change($item, $postdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Load data
    public function inventory_loaddata() {
        if ($this->isAjax()) {
            $error=$this->restore_invdata_error;
            $mdata = [];
            $newss = usersession('invitemdata');
            if (!empty($newss)) {
                $plate_temp=$this->input->post('plate_temp');
                $proof_temp=$this->input->post('proof_temp');
                $item_label=$this->input->post('item_label');
                $error='Unknown Upload Parameter';
                if (!empty($proof_temp)) {
                    // $out=$this->printshop_model->get_invent_item($proof_temp);
                    $error='';
                    $id = $proof_temp;
                    $filesource = $newss['proof_temp'];
                    $filename=$newss['proof_temp_source'];
                    $uploadsess='prooftempupload';
                    $title='Upload Proof temp';
                    $uploadtype='proof_temp';
                } elseif (!empty($plate_temp)) {
                    $error='';
                    $id=$plate_temp;
                    $filesource = $newss['plate_temp'];
                    $filename=$newss['plate_temp_source'];
                    $uploadsess='platetempupload';
                    $title='Upload Plate temp';
                    $uploadtype='plate_temp';
                } elseif (!empty($item_label)) {
                    $error='';
                    $id=$item_label;
                    $filesource = $newss['item_label'];
                    $filename=$newss['item_label_source'];
                    $uploadsess='itemlabelupload';
                    $title='Upload Item Label';
                    $uploadtype='item_label';
                }
            }
            if ($error=='') {
                $viewParams=array(
                    'filename'=> $filename,
                    'filesource'=>$filesource,
                    'uplsess' => $uploadsess,
                    'uploadtype'=>$uploadtype,
                );
                usersession($uploadsess, $newss);
                $mdata['title'] = $title;
                $mdata['content'] = $this->load->view('printshopinventory/plate_temp_view',$viewParams,TRUE);
                $mdata['filename'] = $filename;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function inventory_platetempattach() {
        $this->load->helper('upload');
        $postdata=$this->input->get();
        $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');

        $response = array('success' => false, 'msg'=>'Empty Upload Data');
        $data=usersession($uploadsession);

        if (!empty($data)) {
            $response['error']= 'server-error file not passed';
            $path = $this->config->item('upload_path_preload');
            // Allowed Extensions
            $uploadtype=$postdata['uploadtype'];
            if ($uploadtype=='item_label') {
                $arrayext=array('jpg','JPG','jpeg','JPEG');
            } else {
                $arrayext=array('ai','AI', 'pdf', 'PDF');
            }

            if (isset($_GET['qqfile'])) {
                $file = new qqUploadedFileXhr();
            } elseif (isset($_FILES['qqfile'])) {
                $file = new qqUploadedFileForm();
            } elseif (isset($_POST['qqfile'])) {
                $file = new qqUploadedFileXhr();
            }

            if ($file) {
                $response['error']= 'server-error file size is zero';
                $filename = $file->getName();
                $filesize = $file->getSize();

                if ($filesize > 0) {
                    $these = implode(', ', $arrayext);
                    $response['error']= 'File has an invalid extension, it should be one of '. $these . '.';
                    $pathinfo = pathinfo($filename);
                    $newfilename=uniq_link(12);
                    $ext = strtolower($pathinfo['extension']);

                    if (in_array($ext, $arrayext )) {
                        $filesource = $path . $newfilename . '.' . $ext;
                        $file->save($filesource);

                        $data['filesource'] = $filesource;
                        $data['filename'] = $filename;
                        usersession($uploadsession, $data);
                        $response['success'] = true;
                        $response['error'] = '';
                    }
                }
            }
        }
        echo (json_encode($response));
        exit();
    }

    public function inventory_deluplplatetempdocs() {
        if ($this->isAjax()) {
            $error='Session Expired. Please, recall form';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);

            if (!empty($data)) {
                $data['filesource'] = NULL;
                $data['filename'] = NULL;
                usersession($uploadsession, $data);
                $error ="";
            }
            $this->ajaxResponse(array('filename'=>$data['filesource']), $error);
        }
    }

    public function save_platetempload() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_invdata_error;

            // Restore from session
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);
            $ss = usersession('invitemdata');

            if (!empty($data) || !empty($ss)) {
                $this->load->model('printshop_model');
                $data['uploadtype']=$postdata['uploadtype'];
                $cut = $this->printshop_model->cut_link($data);
                if ($postdata['uploadtype']=='item_label') {
                    $ss['item_label'] = $cut['filesource'];
                    $ss['item_label_source'] = $cut['filename'];
                } elseif ($postdata['uploadtype']=='proof_temp') {
                    $ss['proof_temp'] = $cut['filesource'];
                    $ss['proof_temp_source'] = $cut['filename'];
                } else {
                    $ss['plate_temp'] = $cut['filesource'];
                    $ss['plate_temp_source'] = $cut['filename'];
                }
                usersession($uploadsession,NULL);
                usersession('invitemdata', $ss);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Save Inventory item
    public function inventory_item_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $item=usersession('invitemdata');
                $error='Edit time expired. Reload';
                if (!empty($item)) {
                    $item_id=$item['printshop_item_id'];
                    $this->load->model('printshop_model');
                    $res=$this->printshop_model->invitem_item_save($item);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        if ($item_id>0) {
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
                            $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
                            $specopt=array(
                                'data'=>$data['inventory'],
                                'permission'=>$permission['profit_view'],
                            );
                            $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
                            $mdata['newitem']=0;
                        } else {
                            $mdata['newitem']=1;
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Add Color for Inventory Item
    public function inventory_color() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $postdata=$this->input->post();
            $printshop_item_id=$postdata['printshop_item_id'];
            $printshop_color_id=$postdata['printshop_color_id'];
            $showmax=$postdata['showmax'];
            $brand = ifset($postdata, 'brand', 'ALL');
            if ($printshop_color_id==0) {
                $color=$this->printshop_model->invitem_newcolor($printshop_item_id);
                $colors=$this->printshop_model->get_item_colors($printshop_item_id);
                $color['numcolors']=count($colors)+1;
                $color["printshop_pics"] = array();
            } else {
                $res=$this->printshop_model->invitem_colordata($printshop_color_id, $brand);
                // Get a number of colors
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                    $this->ajaxResponse($mdata, $error);
                } else {
                    $color=$res['data'];
                    $color["printshop_pics"] = $this->printshop_model->get_picsattachments($printshop_color_id);
                }
            }
            $uploadsess='picsupload'.uniq_link(15);

            $color["uplsess"] = $uploadsess;
            $color['showmax']=$showmax;
            usersession($uploadsess, $color);
            // Devide form on 2 parts
            $mdata['commoncontent']=$this->load->view('printshopinventory/invitem_colordata_view', $color, TRUE);
            // Permissions
            $permission=$this->user_model->get_user_data($this->USR_ID);
            $color['permission']=$permission['profit_view'];
            $mdata['addcontent']=$this->load->view('printshopinventory/invitem_coloradddata_view', $color, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Item Color Value
    public function inventory_color_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Edut time expired. Reconnect';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $color=usersession($uploadsession);
            if (!empty($color)) {
                $this->load->model('printshop_model');
                $postdata=$this->input->post();
                $res=$this->printshop_model->invitem_color_change($color, $postdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $avail=$res['availabled'];
                    $mdata['availabled']='';
                    if ($avail!=0) {
                        $mdata['availabled']=  number_format($avail,0,'.',',');
                    }
                    $mdata['notreorder']=$res['notreorder'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Pics download - prepare
    public function inventory_pics() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $uploadsess=$this->input->post('uplsess');
            $data = usersession($uploadsess);
            $data["state_".$uploadsess] = $data;
            usersession($uploadsess, $data);
            $data["html"] = "";
            $mdata["numrec"] = 0;
            if (isset($data['printshop_pics']) && count($data['printshop_pics']) > 0) {
                $this->load->model('printshop_model');
                foreach ($data['printshop_pics'] as $row) {
                    if ($row["status"] != Printshop_model::ROW_DELETE) {
                        $data["html"] .= $this->load->view('printshopinventory/picsfile_view', $row, TRUE);
                        $mdata["numrec"]++;
                    }
                }
            }
            // Popup Options
            $mdata['content']=$this->load->view('printshopinventory/pics_upload_view', $data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Save upload pictures
    public function inventory_picsattach() {
        $this->load->helper('upload');
        $postdata=$this->input->get();
        $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');

        $response = array('success' => false, 'msg'=>'Empty Upload Data');
        $data=usersession($uploadsession);

        if (!empty($data)) {
            $response['error']= 'server-error file not passed';

            $path = $this->config->item('upload_path_preload');
            // Allowed Extensions
            $arrayext=array('jpg', 'jpeg', 'JPG', 'JPEG');
            if (isset($_GET['qqfile'])) {
                $file = new qqUploadedFileXhr();
            } elseif (isset($_FILES['qqfile'])) {
                $file = new qqUploadedFileForm();
            } elseif (isset($_POST['qqfile'])) {
                $file = new qqUploadedFileXhr();
            }
            if ($file) {
                $response['error']= 'server-error file size is zero';
                $pics_source = $file->getName();
                $filesize = $file->getSize();

                if ($filesize > 0) {
                    $these = implode(', ', $arrayext);
                    $response['error']= 'File has an invalid extension, it should be one of '. $these . '.';
                    $pathinfo = pathinfo($pics_source);
                    $newfilename=uniq_link(12);
                    $ext = strtolower($pathinfo['extension']);

                    if (in_array($ext, $arrayext )) {
                        $pics = $path . $newfilename . '.' . $ext;
                        $file->save($pics);

                        $this->load->model('printshop_model');
                        $data['printshop_pics'][] = array(
                            'printshop_pics_id' => -1 * (count($data['printshop_pics']) + 1),
                            'pics_source' => $pics_source,
                            'pics' => $newfilename . '.' . $ext,
                            'status' => Printshop_model::ROW_INSERT,
                            'printshop_color_id' => $data["printshop_color_id"]
                        );
                        $content = "";
                        $response['numrec'] = 0;
                        foreach ($data['printshop_pics'] as &$row) {
                            if ($row["status"] != Printshop_model::ROW_DELETE) {
                                $response['numrec']++;
                                $content .= $this->load->view('printshopinventory/picsfile_view',$row,TRUE);
                            }
                        }
                        $response['content']=$content;
                        $response['success'] = true;
                        $response['error'] = '';
                        usersession($uploadsession, $data);
                    }
                }
            }
        }
        echo (json_encode($response));
        exit();
    }

    public function inventory_deluplpics() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);
            if (empty($data)) {
                $error='Session Expired. Please, recall form';
            } else {
                $id = $this->input->post('id');
                $content='';
                $mdata['numrec'] = 0;
                $this->load->model('printshop_model');
                foreach ($data['printshop_pics'] as &$row) {
                    if ($row["status"] != Printshop_model::ROW_DELETE && $row['printshop_pics_id'] != $id) {
                        $mdata['numrec']++;
                        $content .= $this->load->view('printshopinventory/picsfile_view',$row,TRUE);
                    } else {
                        $row["status"] = Printshop_model::ROW_DELETE;
                    }
                }
                $mdata['content']=$content;
                usersession($uploadsession, $data);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Change Spec for Inventory
    public function inventory_specedit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $specfile = $this->input->get('specfile');
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);
            $mdata['content']=$this->load->view('printshopinventory/specedit_view', $data,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Save changes
    public function inventory_color_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Edit time expired. Reconnect';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $color=usersession($uploadsession);
            $brand = ifset($postdata,'brand');
            $printshop_color_id=$this->input->post('printshop_color_id');
            $specfile=$this->input->post('specfile');
            if (!empty($color)) {
                $color_id=$color['printshop_color_id'];
                $this->load->model('printshop_model');
                $res=$this->printshop_model->invitem_color_save($color);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $printshop_pics = $color['printshop_pics'];
                    $this->printshop_model->save_printshop_pics($printshop_pics, $res['printshop_color_id']);
                    usersession($uploadsession, NULL);
                    $mdata['new']=0;
                    if ($color_id<=0) {
                        $mdata['new']=1;
                    }
                    if ($mdata['new']==0) {
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
                        $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
                        $specopt=array(
                            'data'=>$data['inventory'],
                            'permission'=>$permission['profit_view'],
                        );
                        $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Download OnBoat
    public function inventory_boat_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Date';
            $postdate=$this->input->post();
            if (isset($postdate['onboat_container'])) {
                $onboat_container=$postdate['onboat_container'];
                $this->load->model('printshop_model');
                $res=$this->printshop_model->inventory_download($onboat_container);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['url']=$res['url'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Add / change onboat container
    public function inventory_changecontainer() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata = $this->input->post();
            $onboat_container=ifset($postdata, 'container',0);
            $showmax=ifset($postdata, 'showmax',0);
            $brand = ifset($postdata,'brand','ALL');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_container_edit($onboat_container, $brand);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $session_id=uniq_link(14);
                $mdata['managecontent']=$this->load->view('printshopinventory/onboat_editmanage',array(), TRUE);
                if ($onboat_container==0) {
                    // Calc exist number of
                    $containers=$this->printshop_model->get_data_onboat($brand);
                    $viewwidth=(count($containers)+1)*$this->container_with;
                    $mdata['width']=$viewwidth;
                    $marginleft=($viewwidth>$this->maxlength ? ($this->maxlength-$viewwidth) : 0);
                    $mdata['marginleft']=($showmax==0 ? $marginleft : $marginleft-$this->container_with);
                    $details=$res['details'];
                    $headview='<div data-container="'.$details['onboat_container'].'" class="onboacontainer ">'.$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE)."</div>";
                    $mdata['containerhead']=$headview;
                    $boptions=array(
                        'data'=>$res['data'],
                        'onboat_container'=>$onboat_container,
                        'session'=>$session_id,
                        'brand' => $brand,
                    );
                    $content='<div data-container="'.$details['onboat_container'].'" class="onboacontainerarea">'.$this->load->view('printshopinventory/container_data_edit', $boptions, TRUE).'</div>';
                    $mdata['containercontent']=$content;
                    $mdata['onboat_container']=$details['onboat_container'];
                } else {
                    $boptions=array(
                        'data'=>$res['data'],
                        'onboat_container'=>$onboat_container,
                        'session'=>$session_id,
                        'brand' => $res['details']['brand'],
                    );
                    $mdata['containercontent']=$this->load->view('printshopinventory/container_data_edit', $boptions, TRUE);
                }

                $sessdata=array(
                    'total'=>$res['total'],
                    'data'=>$res['data'],
                    'details'=>$res['details'],
                );
                usersession($session_id, $sessdata);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change container data
    public function inventory_editcontainer() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_invdata_error;
            $postdata=$this->input->post();
            $session_id=ifset($postdata, 'session', 'emptysession');
            $sessdata=usersession($session_id);
            if (!empty($sessdata)) {
                // Lets go
                $this->load->model('printshop_model');
                $res=$this->printshop_model->inventory_editcontainer($sessdata, $postdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($postdata['entity']=='color') {
                        $mdata['total']=  QTYOutput($res['total']);
                        $mdata['item']=$res['item'];
                        $mdata['totalitem']=QTYOutput($res['totalitem']);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_savecontainer() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $this->load->model('printshop_model');
            $session_id=ifset($postdata, 'session', 'emptysession');
            if ($postdata['action']=='cancel') {
                $error='';
                usersession($session_id, NULL);
                $onboat_container=$postdata['container'];
                // $data=$res['data'];
                $details=$this->printshop_model->get_container_details($onboat_container);
                $mdata['containerhead']=$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE);
                $colors=$this->printshop_model->get_printshop_itemcolors();
                $data=$this->printshop_model->get_container_view($onboat_container, $colors);
                $boptions=array(
                    'data'=>$data,
                    'onboat_container'=>$details['onboat_container'],
                    'onboat_status'=>$details['onboat_status'],
                );
                $mdata['containercontent']=$this->load->view('printshopinventory/purecontainer_data_view', $boptions, TRUE);
            } else {
                $error=$this->restore_invdata_error;
                $sessdata=usersession($session_id);
                if (!empty($sessdata)) {
                    $edit_container=$sessdata['details']['onboat_container'];
                    $res=$this->printshop_model->inventory_savecontainer($sessdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $brand = ifset($postdata,'brand','ALL');
                        $onboat_container=$res['onboat_container'];
                        $mdata['onboat_container']=$res['onboat_container'];
                        $data=$res['data'];
                        $details=$this->printshop_model->get_container_details($onboat_container);
                        $mdata['delete']=1;
                        if (empty($details)) {
                            $options=array(
                                'orderby'=>'item_num',
                                'direct'=>'asc',
                                'brand' => $brand,
                            );
                            $data=$this->printshop_model->get_printshopitems($options);
                            // Get OnBoat Data
                            $colors=$data['colors'];

                            $boatdata = $this->printshop_model->get_data_onboat();
                            $slider_width=60*(count($boatdata));
                            $margin=$this->maxlength-$slider_width;
                            $margin=($margin>0 ? 0 : $margin);
                            // Head View
                            $boathead_view='';
                            foreach ($boatdata as $drow) {
                                $boathead_view.=$this->load->view('printshopinventory/onboat_containerhead_view', $drow, TRUE);
                            }
                            $mdata['headview']=$boathead_view;
                            // Download view
                            $mdata['download_view']=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$boatdata), TRUE);

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
                            $boatoptions=array(
                                'width'=>$slider_width,
                                'margin'=>$margin,
                                'boatcontent'=>$containers_view,
                            );
                            $mdata['onboatcontent']=$this->load->view('printshopinventory/onboatdata_view', $boatoptions, TRUE);
                            $mdata['margin']=$margin;
                            $mdata['width']=$slider_width;
                        } else {
                            $mdata['delete']=0;
                            $boptions=array(
                                'data'=>$data,
                                'onboat_container'=>$details['onboat_container'],
                                'onboat_status'=>$details['onboat_status'],
                            );
                            if ($edit_container<0) {
                                $headview='<div data-container="'.$details['onboat_container'].'" class="onboacontainer ">'.$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE)."</div>";
                                $mdata['containerhead']=$headview;
                                $content='<div data-container="'.$details['onboat_container'].'" class="onboacontainerarea">'.$this->load->view('printshopinventory/purecontainer_data_view', $boptions, TRUE).'</div>';
                                $mdata['containercontent']=$content;
                                $containers=$this->printshop_model->get_data_onboat($brand);
                                $viewwidth=(count($containers))*$this->container_with;
                                $mdata['width']=$viewwidth;
                                $marginleft=($viewwidth>$this->maxlength ? ($this->maxlength-$viewwidth) : 0);
                                $mdata['marginleft']=($postdata['showmax']==0 ? $marginleft : $marginleft-$this->container_with);
                                $mdata['downloadview']=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$containers,), TRUE);
                            } else {
                                $mdata['containercontent']=$this->load->view('printshopinventory/purecontainer_data_view', $boptions, TRUE);
                                $mdata['containerhead']=$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE);
                            }

                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Export inventore to excell
    public function inventory_export() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $error = 'Empty Brand';
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $this->load->model('printshop_model');
                $res=$this->printshop_model->export_inventory($brand);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['url']=$res['url'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Stock Data
    public function inventory_colorstock() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $printshop_color_id=$this->input->post('printshop_color_id');
            $brand = $this->input->post('brand');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id, $brand);
            $content=$this->load->view('printshop/instock_data_view', array('data'=>$res,'brand'=>$brand),TRUE);
            $mdata['content']=$this->load->view('printshop/instock_popup_view', array('content'=>$content, 'brand'=>$brand, 'color' => $printshop_color_id,), TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Edit Stock value
    public function invcolor_stock_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $printshop_instock_id=$this->input->post('printshop_instock_id');
            $printshop_color_id=$this->input->post('printshop_color_id');

            $this->load->model('printshop_model');
            if ($printshop_instock_id==0) {
                $stock=$this->printshop_model->new_colorinstock($printshop_color_id);
            } else {
                $res=$this->printshop_model->invitem_color_stockdata($printshop_instock_id);
                if ($res['result']==Fulfillment::ERR_FLAG) {
                    $error=$res['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $stock=$res['data'];
            }
            $mdata['content']=$this->load->view('printshop/instock_data_edit', $stock,TRUE);
            $this->session('stockdata', $stock);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change value
    public function invcolor_stock_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $stockdata=$this->session('stockdata');
            $postdata=$this->input->post();
            $this->load->model('printshop_model');
            $res=$this->printshop_model->invcolor_stock_change($stockdata, $postdata);
            if ($res['result']==Fulfillment::ERR_FLAG) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Cancel data
    public function inventory_colorstock_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';

            $this->load->model('printshop_model');
            $stockdata=$this->session('stockdata');
            $res=$this->printshop_model->invitem_color_stocksave($stockdata);
            if ($res['result']==Fulfillment::ERR_FLAG) {
                $error=$res['msg'];
            } else {
                $printshop_color_id=$res['printshop_color_id'];
                $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id);
                $mdata['content']=$this->load->view('printshop/instock_data_view', array('data'=>$res),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_colorstock_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $printshop_color_id=$this->input->post('printshop_color_id');
            $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id);
            $mdata['content']=$this->load->view('printshop/instock_data_view', array('data'=>$res),TRUE);
            $this->session('stockdata', NULL);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Arive container
    public function inventory_arrivecontainer() {
        if($this->isAjax()) {
            $mdata = array();
            $this->load->model('printshop_model');
            $onboat_container=$this->input->post('onboat_container');
            $brand = $this->input->post('brand');
            $res = $this->printshop_model->onboat_arrived($onboat_container);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $options=array(
                    'orderby'=>'item_num',
                    'direct'=>'asc',
                    'brand' => 'brand',
                );
                $data=$this->printshop_model->get_printshopitems($options);
                // New Inv totals
                $mdata['total_inventory']=MoneyOutput($data['inventtotal']);
                // Make Total Inv content
                $totaloptions=array(
                    'data'=>$data['inventory'],
                );
                $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);

                $details=$this->printshop_model->get_container_details($onboat_container);
                $mdata['containerhead']=$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE);
                $totalinv=$this->printshop_model->get_inventory_totals($brand);
                $mdata['totalinvview']=$this->load->view('printshopinventory/total_inventory_view',$totalinv,TRUE);

            }
            // $edit = $this->printshop_model->add_to_instock($onboat_date);

            $this->ajaxResponse($mdata, $error);
        }
    }

    function inventory_plate_download() {
        if ($this->isAjax()) {
            $mdata=array();

            $item_id=$this->input->post('printshop_item_id');
            $type = $this->input->post('type');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_invent_item($item_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $item=$res['item'];
                $mdata['fileurl']=$item['plate_temp'];
                $mdata['filename']=$item['plate_temp_source'];
                if($type=='proof') {
                    $mdata['fileurl']=$item['proof_temp'];
                    $mdata['filename']=$item['proof_temp_source'];
                } elseif ($type=='itemlabel') {
                    $mdata['fileurl']=$item['item_label'];
                    $mdata['filename']=$item['item_label_source'];
                }
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    function inventory_pics_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $color_id=$this->input->post('printshop_color_id');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_picsattachments($color_id);

            foreach ($res as $fileurl => $row) {
                $fileu[$fileurl]=$row['pics'];
            }

            foreach ($res as $filename => $row) {
                $filen[$filename]=$row['pics_source'];
            }

            $mdata['fileurl'] = $fileu;
            $mdata['filename'] =$filen;
            $error='';

            $this->ajaxResponse($mdata, $error);
        }
    }

    // INVENTORY NEED LIST
    public function invneedlist_brand() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $this->load->model('printshop_model');
                $data = $this->printshop_model->get_data_onboat($brand);
                $boathead_view='';
                foreach ($data as $drow) {
                    $boathead_view.=$this->load->view('inventoryview/onboat_containerhead_view', $drow, TRUE);
                }
                // Build head content
                $slider_width=60*count($data);
                $margincount = $this->needlistlength-$slider_width;
                $margin=($margincount>0 ? 0 : $margincount);

                $boatoptions=array(
                    'data'=>$data,
                    'container_view'=>$boathead_view,
                    'width' => $slider_width,
                    'margin' => $margin,
                );
                $mdata['onboat_content']=$this->load->view('inventoryview/onboathead_view', $boatoptions, TRUE);
                $mdata['download_view']=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
                $mdata['width']=$slider_width;
                $mdata['margin'] = $margin;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Inventory List data
    public function datalist() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $this->load->model('printshop_model');
            $brand = ifset($postdata,'brand');
            $options=array(
                'orderby'=>'aftercontproc',
                'direct'=>'asc',
                'place'=>'fulfillment',
                'brand' => $brand,
            );
            $data=$this->printshop_model->get_needinvlistdata($options);


            // Make Total Inv content
            $totaloptions=array(
                'data'=>$data['inventory'],
            );
            $mdata['totalinvcontent']=$this->load->view('inventoryview/inventory_data_view', $totaloptions, TRUE);
            $specopt=array(
                'data'=>$data['inventory'],
            );
            $mdata['speccontent']=$this->load->view('inventoryview/specinventory_data_view', $specopt, TRUE);
            // Get OnBoat Data
            $colors=$data['inventory'];
            $boatdata = $this->printshop_model->get_data_onboat($brand);
            $containers_view='';
            foreach ($boatdata as $drow) {
                $boatcontndata=$this->printshop_model->get_needinvlistboat_details($drow['onboat_container'], $colors);
                $boptions=array(
                    'data'=>$boatcontndata,
                    'onboat_container'=>$drow['onboat_container'],
                    'onboat_status'=>$drow['onboat_status'],
                );
                $containers_view.=$this->load->view('inventoryview/container_data_view', $boptions, TRUE);
            }
            $slider_width=60*(count($boatdata));
            $margin=$this->needlistlength-$slider_width;

            $boatoptions=array(
                'width'=>$slider_width,
                'margin'=>($margin>0 ? 0 : $margin),
                'boatcontent'=>$containers_view,
            );
            $mdata['onboatcontent']=$this->load->view('inventoryview/onboatdata_view', $boatoptions, TRUE);
            $mdata['margin']=$margin;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    function plate_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $item_id=$this->input->post('printshop_item_id');
            $type = $this->input->post('type');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_invent_item($item_id);
            $error=$res['msg'];
            if ($res['result']== $this->success_result) {
                $item=$res['item'];
                $mdata['fileurl']=$item['plate_temp'];
                $mdata['filename']=$item['plate_temp_source'];

                if($type=='proof') {
                    $mdata['fileurl']=$item['proof_temp'];
                    $mdata['filename']=$item['proof_temp_source'];
                } elseif ($type=='itemlabel') {
                    $mdata['fileurl']=$item['item_label'];
                    $mdata['filename']=$item['item_label_source'];
                }
                $error='';
            }
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

    private function _prepare_needlist_view($brand, $top_menu) {
        $this->load->model('printshop_model');
        $addcost=$this->printshop_model->invaddcost();
        $totals=$this->printshop_model->count_prinshop_items();
        $data = $this->printshop_model->get_data_onboat($brand);
        $boathead_view='';
        foreach ($data as $drow) {
            $boathead_view.=$this->load->view('inventoryview/onboat_containerhead_view', $drow, TRUE);
        }
        // Build head content
        $slider_width=60*count($data);
        $margincount = $this->needlistlength-$slider_width;
        $margin=($margincount>0 ? 0 : $margincount);

        $boatoptions=array(
            'data'=>$data,
            'container_view'=>$boathead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $onboat_content=$this->load->view('inventoryview/onboathead_view', $boatoptions, TRUE);

        $download_view=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
        $headoptions=array(
            'addcost'=>$addcost,
            'data' => $data,
            'width' => $slider_width,
            'margin' => $margin,
            'onboathead'=>$onboat_content,
            'download_view'=>$download_view,
        );
        $headview=$this->load->view('inventoryview/needlist_head_view', $headoptions,TRUE);

        /*$specs_disc = $this->printshop_model->get_color_disc();*/

        $invoption=array(
            'totals'=>$totals,
            'headview'=>$headview,
            'brand' => $brand,
            'top_menu' => $top_menu,
        );

        $content=$this->load->view('inventoryview/page_data_view', $invoption, TRUE);
        return $content;
    }

    private function _prepare_inventsalesrep_view($brand, $top_menu) {
        $this->load->model('printshop_model');
        $addcost=$this->printshop_model->invaddcost();
        $totals=$this->printshop_model->count_prinshop_items();
        $totalinv=$this->printshop_model->get_inventory_totals($brand);
        $totalinvview=$this->load->view('invsalesrep/total_inventory_view',$totalinv,TRUE);
        $data = $this->printshop_model->get_data_onboat($brand);
        $boathead_view='';
        foreach ($data as $drow) {
            $boathead_view.=$this->load->view('invsalesrep/onboat_containerhead_view', $drow, TRUE);
        }
        // Build head content
        $slider_width=60*count($data);
        $margin = $this->salesreplength-$slider_width;
        $margin=($margin>0 ? 0 : $margin);

        $width_edit = 58;
        $boatoptions=array(
            'data'=>$data,
            'container_view'=>$boathead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $onboat_content=$this->load->view('invsalesrep/onboathead_view', $boatoptions, TRUE);

        $permission=$this->user_model->get_user_data($this->USR_ID);
        // $download_view=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
        $headoptions=array(
            'permission' => $permission['profit_view'],
            'addcost'=>$addcost,
            'data' => $data,
            'width' => $slider_width,
            'margin' => $margin,
            'onboathead'=>$onboat_content,
            'invetorytotal'=>$totalinvview,
        );
        $headview=$this->load->view('invsalesrep/salesrep_head_view', $headoptions,TRUE);

        $invoption=array(
            'totals'=>$totals,
            'fullview'=>$headview,
            'maxsum'=>$totalinv['maxsum'],
            'brand' => $brand,
            'top_menu'=>$top_menu,
        );

        $content=$this->load->view('invsalesrep/page_view', $invoption, TRUE);
        return $content;

    }

}