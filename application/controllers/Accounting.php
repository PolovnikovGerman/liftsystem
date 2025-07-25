<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends MY_Controller
{

    private $pagelink = '/accounting';
    private $order_profit_perpage=100;
    private $order_totals_perpage=100;
    private $perpage_options =array(100, 250, 500, 1000);
    private $restore_data_error = 'Edit Connection Lost. Please, recall form';
    private $weekshow_limit=0;
    public $current_brand;

    public function __construct()
    {
        parent::__construct();
        $this->current_brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink,0, $this->current_brand);
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
        $this->load->model('orders_model');
        $this->load->model('balances_model');
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Accounting';
        $brand = $this->current_brand;
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);

        $start = $this->input->get('start', TRUE);
        $content_options = [];
        $gmaps = 0;
        if (!empty($this->config->item('google_map_key'))) {
            $gmaps = 1;
        }
        foreach ($menu as $row) {
            if ($row['item_link']=='#profitordesview') {
                $head['styles'][]=array('style'=>'/css/accounting/profitordesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/profitordesview.js');
                $content_options['profitordesview'] = $this->_prepare_order_profit($brand);
            } elseif ($row['item_link']=='#profitdatesview') {
                $head['styles'][] = array('style' => '/css/accounting/profitdatesview.css');
                $head['scripts'][] = array('src' => '/js/accounting/profitdatesview.js');
                $content_options['profitdatesview'] = $this->_prepare_profitcalend_content($brand);
            } elseif ($row['item_link']=='#purchaseordersview') {
                $head['styles'][]=array('style'=>'/css/accounting/pototals.css');
                $head['scripts'][]=array('src'=>'/js/accounting/pototals.js');
                $content_options['purchaseordersview'] = $this->_prepare_purchaseorders_view($brand);
            } elseif ($row['item_link']=='#openinvoicesview') {
                $head['styles'][]=array('style'=>'/css/accounting/openinvoicesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/openinvoicesview.js');
                $content_options['openinvoicesview'] = $this->_prepare_openinvoice_content($brand);
            } elseif ($row['item_link']=='#financebatchesview') {
                $head['styles'][]=array('style'=>'/css/accounting/financebatchesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/financebatchesview.js');
                $content_options['financebatchesview'] = $this->_prepare_batches_view($brand);
            } elseif ($row['item_link']=='#netprofitview') {
                $head['styles'][] = array('style' => '/css/accounting/netprofit.css');
                $head['scripts'][] = array('src' => '/js/accounting/netprofit.js');
                $content_options['netprofitview'] = $this->_prepare_netprofit_content($brand);
            } elseif ($row['item_link']=='#ownertaxesview') {
                $head['styles'][] = array('style' => '/css/accounting/ownertaxesview.css');
                $head['scripts'][] = array('src' => '/js/accounting/ownertaxesview.js');
                $content_options['ownertaxesview'] = $this->_prepare_ownerstaxes_view($brand);
            } elseif ($row['item_link']=='#expensesview') {
                $head['styles'][]=array('style'=>'/css/accounting/expensesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/expensesview.js');
                $content_options['expensesview'] = $this->_prepare_expensives_view($brand);
            } elseif ($row['item_link']=='#accreceiv') {
                $head['styles'][]=array('style'=>'/css/accounting/accreceiv.css');
                $head['scripts'][]=array('src'=>'/js/accounting/accreceiv.js');
                $content_options['accreceivview'] = $this->_prepare_accreceiv_view($brand);
            }
        }
        // Add main page management
        $head['scripts'][] = array('src' => '/js/accounting/page.js');
        $head['styles'][] = array('style' => '/css/accounting/accountpage.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // DatePicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        /*Google Chart */
        $head['scripts'][]=array('src'=>"https://www.gstatic.com/charts/loader.js");
        // Order popup
        $head['styles'][]=array('style'=>'/css/leadorder/popup.css');
        $head['scripts'][]=array('src'=>'/js/leads/leadorderpopup.js');
        if ($gmaps==1) {
            $head['scripts'][]=array('src'=>'/js/leads/order_address.js');
        }
        // Uploader
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        // File Download
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Select 2
        $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
        $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];
        // Scroll panel
        // $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'brand' => $brand,
        ];
        if ($gmaps==1) {
            $options['gmaps'] = $gmaps;
        }
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['brand'] = $brand;
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options['menu_view'] = $this->load->view('page_modern/submenu_view',['menu' => $menu, 'start' => $start, 'brandclass' => $brandclass ], TRUE);
        $content_view = $this->load->view('accounting/page_new_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $dat['modal_view'] = $this->load->view('accounting/modal_view',[], TRUE);
        $this->load->view('page_modern/page_template_view', $dat);
    }

    /* Calculate qty of Orders */
    public function search_orders() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $options=array();
            if (isset($postdata['search'])) {
                $options['search']=$postdata['search'];
            }
            if (isset($postdata['filter'])) {
                $options['filter']=$postdata['filter'];
            }
            if (isset($postdata['add_filter'])) {
                $options['add_filtr']=$postdata['add_filter'];
            }
            if ($postdata['show_year']==1) {
                if ($postdata['year']>0) {
                    $nxtyear = $postdata['year']+1;
                    if (intval($postdata['month'])==0) {
                        $options['date_bgn']=strtotime($postdata['year'].'-01-01');
                        $options['date_end']=strtotime($nxtyear.'-01-01');
                    } else {
                        $start = $postdata['year'].'-'.str_pad($postdata['month'],2,'0',STR_PAD_LEFT).'-01';
                        $options['date_bgn']=strtotime($start);
                        $finish = date('Y-m-d', strtotime($start. ' + 1 month'));
                        $options['date_end']=strtotime($finish);
                    }
                }
            } else {
                if ($postdata['date_bgn']) {
                    $options['date_bgn']=strtotime($postdata['date_bgn']);
                }
                if ($postdata['date_end']) {
                    $d_finish = date('Y-m-d',strtotime($postdata['date_end']));
                    $options['date_end'] = date(strtotime("+1 day", strtotime($d_finish)));
                }
            }
            if (isset($postdata['shipping_country']) && intval($postdata['shipping_country'])!==0) {
                $options['shipping_country']=$postdata['shipping_country'];
                if (isset($postdata['shipping_state']) && intval($postdata['shipping_state'])>0) {
                    $options['shipping_state'] = $postdata['shipping_state'];
                }
            }
            if (isset($postdata['order_type']) && !empty($postdata['order_type'])) {
                $options['order_type']=$postdata['order_type'];
            }
            if (isset($postdata['item_type']) && !empty($postdata['item_type'])) {
                $options['item_type'] = $postdata['item_type'];
            }
            $options['exclude_quickbook'] = ifset($postdata,'exclude_quickbook',0);
            /* count number of orders */
            $options['admin_mode']=0;
            // if ($this->USR_ROLE=='masteradmin') {
            if ($this->USER_ORDER_EXPORT==1) {
                $options['admin_mode']=1;
            }
            if (isset($postdata['brand']) && !empty($postdata['brand'])) {
                $options['brand'] = $postdata['brand'];
            }

            $mdata['totals']=$this->orders_model->get_count_orders($options);
            /* Total of sums */
            $totalord=$this->orders_model->orders_profit_tolals($options);
            if (!isset($options['order_type'])) {
                // Prepare tooltip
                $order_tool_options = [
                    'title' => 'Orders',
                    'type' => 'qty',
                    'new_val' => $totalord['numorders_detail_new'],
                    'repeat_val' => $totalord['numorders_detail_repeat'],
                    'blank_val' => $totalord['numorders_detail_blank'],
                    'new_perc' => $totalord['numorders_detail_newperc'],
                    'repeat_perc' => $totalord['numorders_detail_repeatperc'],
                    'blank_perc'=> $totalord['numorders_detail_blankperc'],
                    'total' => $totalord['numorders'],
                ];
                $order_tooltip = $this->load->view('orderprofit/total_tooltip_view', $order_tool_options, TRUE);
                $qty_tool_options = [
                    'title' => 'QTY',
                    'type' => 'qty',
                    'new_val' => $totalord['qty_detail_new'],
                    'repeat_val' => $totalord['qty_detail_repeat'],
                    'blank_val' => $totalord['qty_detail_blank'],
                    'new_perc' => $totalord['qty_detail_newperc'],
                    'repeat_perc' => $totalord['qty_detail_repeatperc'],
                    'blank_perc'=> $totalord['qty_detail_blankperc'],
                    'total' => $totalord['qty'],
                ];
                $qty_tooltip = $this->load->view('orderprofit/total_tooltip_view', $qty_tool_options, TRUE);
                $revenue_tool_options = [
                    'title' => 'Revenue',
                    'type' => 'money',
                    'new_val' => $totalord['revenue_detail_new'],
                    'repeat_val' => $totalord['revenue_detail_repeat'],
                    'blank_val' => $totalord['revenue_detail_blank'],
                    'new_perc' => $totalord['revenue_detail_newproc'],
                    'repeat_perc' => $totalord['revenue_detail_repeatproc'],
                    'blank_perc'=> $totalord['revenue_detail_blankproc'],
                    'total' => $totalord['revenue'],
                ];
                $revenue_tooltip = $this->load->view('orderprofit/total_tooltip_view', $revenue_tool_options, TRUE);
                // Balance
                $balance_tool_options = [
                    'title' => 'Balance',
                    'type' => 'money',
                    'new_val' => $totalord['balance_detail_new'],
                    'repeat_val' => $totalord['balance_detail_repeat'],
                    'blank_val' => $totalord['balance_detail_blank'],
                    'new_perc' => $totalord['balance_detail_newproc'],
                    'repeat_perc' => $totalord['balance_detail_repeatproc'],
                    'blank_perc'=> $totalord['balance_detail_blankproc'],
                    'total' => $totalord['balance'],
                ];
                $balance_tooltip = $this->load->view('orderprofit/total_tooltip_view', $balance_tool_options, TRUE);

                $shipping_tool_options = [
                    'title' => 'Shipping',
                    'type' => 'money',
                    'new_val' => $totalord['shipping_detail_new'],
                    'repeat_val' => $totalord['shipping_detail_repeat'],
                    'blank_val' => $totalord['shipping_detail_blank'],
                    'new_perc' => $totalord['shipping_detail_newperc'],
                    'repeat_perc' => $totalord['shipping_detail_repeatperc'],
                    'blank_perc'=> $totalord['shipping_detail_blankperc'],
                    'total' => $totalord['shipping'],
                ];
                $shipping_tooltip = $this->load->view('orderprofit/total_tooltip_view', $shipping_tool_options, TRUE);
                $tax_tool_options = [
                    'title' => 'Tax',
                    'type' => 'money',
                    'new_val' => $totalord['tax_detail_new'],
                    'repeat_val' => $totalord['tax_detail_repeat'],
                    'blank_val' => $totalord['tax_detail_blank'],
                    'new_perc' => $totalord['tax_detail_newperc'],
                    'repeat_perc' => $totalord['tax_detail_repeatperc'],
                    'blank_perc'=> $totalord['tax_detail_blankperc'],
                    'total' => $totalord['tax'],
                ];
                $tax_tooltip = $this->load->view('orderprofit/total_tooltip_view', $tax_tool_options, TRUE);
                $cog_tool_options = [
                    'title' => 'COG',
                    'type' => 'money',
                    'new_val' => $totalord['cog_detail_new'],
                    'repeat_val' => $totalord['cog_detail_repeat'],
                    'blank_val' => $totalord['cog_detail_blank'],
                    'new_perc' => $totalord['cog_detail_newperc'],
                    'repeat_perc' => $totalord['cog_detail_repeatperc'],
                    'blank_perc'=> $totalord['cog_detail_blankperc'],
                    'total' => $totalord['cog'],
                ];
                $cog_tooltip = $this->load->view('orderprofit/total_tooltip_view', $cog_tool_options, TRUE);
                $profit_tool_options = [
                    'title' => 'Profit',
                    'type' => 'money',
                    'new_val' => $totalord['profit_detail_new'],
                    'repeat_val' => $totalord['profit_detail_repeat'],
                    'blank_val' => $totalord['profit_detail_blank'],
                    'new_perc' => $totalord['profit_detail_newperc'],
                    'repeat_perc' => $totalord['profit_detail_repeatperc'],
                    'blank_perc'=> $totalord['profit_detail_blankperc'],
                    'total' => $totalord['profit'],
                ];
                $profi_tooltip = $this->load->view('orderprofit/total_tooltip_view', $profit_tool_options, TRUE);
                $total_options = [
                    'data' => $totalord,
                    'order_tooltip' => $order_tooltip,
                    'qty_tooltip' => $qty_tooltip,
                    'revenue_tooltip' => $revenue_tooltip,
                    'shipping_tooltip' => $shipping_tooltip,
                    'tax_tooltip' => $tax_tooltip,
                    'cog_tooltip' => $cog_tooltip,
                    'profit_tooltip' => $profi_tooltip,
                    'balance_tooltip' => $balance_tooltip,
                    'brand' => ifset($postdata,'brand','SB'),
                ];
                $mdata['total_row']=$this->load->view('orderprofit/total_profitall_view',$total_options,TRUE);
                $mdata['totals_head']=$this->load->view('orderprofit/total_allprofittitle_view',['brand' => ifset($postdata,'brand','SB'),],TRUE);
            } else {
                $mdata['totals_head']=$this->load->view('orderprofit/total_profittitle_view',['brand' => ifset($postdata,'brand','SB'),],TRUE);
                $mdata['total_row']=$this->load->view('orderprofit/total_profit_view',$totalord,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function totaldetailsshow() {
        $postdata=$this->input->get();
        $options = [
            'title' => $postdata['title'],
            'type' => $postdata['type'],
            'new_val' => $postdata['nv'],
            'new_perc' => $postdata['np'],
            'repeat_val' => $postdata['rv'],
            'repeat_perc' => $postdata['rp'],
            'blank_val' => $postdata['bv'],
            'blank_perc' => $postdata['bp'],
            'total' => $postdata['total'],
        ];
        $content = $this->load->view('orderprofit/total_tooltipdetails_view', $options, TRUE);
        echo $content;
    }

    public function get_ordercolordetails() {
        $order_id=$this->input->get('id');
        $res=$this->orders_model->get_order_colordata($order_id);
        $msg=$res['msg'];
        if ($res['result'] == $this->success_result) {
            $msg=$this->load->view('orderprofit/color_details_view',['data'=>$res['data']], TRUE);
        }
        echo $msg;
    }


    public function adminprofitorderdat() {
        if ($this->isAjax()) {
            $mdata=array('content'=>'','totals'=>'');
            $error='';
            $postdata = $this->input->post();
            $offset=0; $limit=10; $order_by='order_num';
            $direct = 'asc';
            if (isset($postdata['limit'])) {
                $limit=$postdata['limit'];
            }
            if (isset($postdata['offset'])) {
                $offset=$postdata['offset']*$limit;
            }
            if (isset($postdata['order_by'])) {
                $order_by=$postdata['order_by'];
            }
            if (isset($postdata['direction'])) {
                $direct=$postdata['direction'];
            }
            $search=array();
            if (isset($postdata['search'])) {
                $search['search']=$postdata['search'];
            }
            if (isset($postdata['filter'])) {
                $search['filter']=$postdata['filter'];
            }
            if (isset($postdata['add_filter'])) {
                $search['add_filtr']=$postdata['add_filter'];
            }
            if ($postdata['show_year']==1) {
                if ($postdata['year']>0) {
                    $nxtyear = $postdata['year']+1;
                    if (intval($postdata['month'])==0) {
                        $search['start_date']=strtotime($postdata['year'].'-01-01');
                        $search['end_date']=strtotime($nxtyear.'-01-01');
                    } else {
                        $start = $postdata['year'].'-'.str_pad($postdata['month'],2,'0',STR_PAD_LEFT).'-01';
                        $search['start_date']=strtotime($start);
                        $finish = date('Y-m-d', strtotime($start. ' + 1 month'));
                        $search['end_date']=strtotime($finish);
                    }
                }
            } else {
                if ($postdata['date_bgn']) {
                    $search['start_date']=strtotime($postdata['date_bgn']);
                }
                if ($postdata['date_end']) {
                    // $search['end_date']=strtotime($postdata['date_end']);
                    $d_finish = date('Y-m-d',strtotime($postdata['date_end']));
                    $search['end_date'] = date(strtotime("+1 day", strtotime($d_finish)));

                }
            }
            if (isset($postdata['shipping_country']) && intval($postdata['shipping_country'])!==0) {
                $search['shipping_country']=$postdata['shipping_country'];
                if (isset($postdata['shipping_state']) && intval($postdata['shipping_state'])>0) {
                    $search['shipping_state'] = $postdata['shipping_state'];
                }
            }
            if (isset($postdata['order_type']) && !empty($postdata['order_type'])) {
                $search['order_type']=$postdata['order_type'];
            }
            if (isset($postdata['item_type']) && !empty($postdata['item_type'])) {
                $search['item_type'] = $postdata['item_type'];
            }

            /* Fetch data about prices */
            // if ($this->USR_ROLE=='masteradmin') {
            if ($this->USER_ORDER_EXPORT==1) {
                $admin_mode=1;
            } else {
                $admin_mode=0;
            }
            if (isset($postdata['brand']) && !empty($postdata['brand'])) {
                $search['brand'] = $postdata['brand'];
            }
            $search['exclude_quickbook'] = ifset($postdata,'exclude_quickbook',0);

            $ordersdat=$this->orders_model->get_profit_orders($search,$order_by,$direct,$limit,$offset, $admin_mode, $this->USR_ID);

            if (count($ordersdat)==0) {
                $content=$this->load->view('orderprofit/empty_view',array(),TRUE);
            } else {
                $data=array(
                    'orders'=>$ordersdat,
                    'brand' => ifset($postdata,'brand','SB'),
                );
                $content = $this->load->view('orderprofit/table_data_view',$data, TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function prepare_orderprofit_export() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $fields = $this->orders_model->get_profitexport_fields();
            $mdata['content']=$this->load->view('orderprofit/prepare_export', ['fields'=>$fields], TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function orderprofit_export() {
        if ($this->isAjax()) {
            $mdata=[];
            $error='';
            $postdata=$this->input->post();
            $res = $this->orders_model->profit_export($postdata);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $mdata['url']=$res['url'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function orderprofit_states() {
        if ($this->isAjax()) {
            $mdata = [
                'content' => '',
            ];
            $error = '';
            $postdata = $this->input->post();

            $this->load->model('shipping_model');
            $data = $this->shipping_model->get_country_states($postdata['country_id']);

            if (count($data)>0) {
                $mdata['content']=$this->load->view('orderprofit/select_shipstates', ['states'=>$data], TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Change year at bottom - show Count of orders per year */
    public function ordercnt_total() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $out=$this->_prepare_orderprofit_bottom($brand);
                $mdata['content']=$out['content'];
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    private function _prepare_orderprofit_bottom($brand) {
        $yearview='';
        $dats=$this->orders_model->get_profit_limitdates($brand);

        if (isset($dats['max_year'])) {
            if (date('Y-m',time())!=$dats['max_year'].'-'.$dats['max_month']) {
                $dats['cur_month']=$dats['max_month'];
                $dats['cur_year']=$dats['max_year'];
            } else {
                $dats['cur_month']=date('m');
                $dats['cur_year']=date('Y');
            }
        } else {
            $dats['max_month']=date('m');
            $dats['max_year']=date('Y');
            $dats['max_date']=time();
            $dats['cur_month']=date('m');
            $dats['cur_year']=date('Y');
        }
        $year=$dats['cur_year'];
        $i=0;
        while ($i<5) {
            $out = $this->orders_model->calendar_orders($year, $brand);
            $voptions = array('title' => $year);
            foreach ($out as $k => $v) {
                $voptions[$k] = $out[$k];
            }
            if ($i==4) {
                $voptions['lastrow']='lastrow';
            } else {
                $voptions['lastrow']='';
            }
            $yearview .= $this->load->view('accounting/admin_ordercnt_view', $voptions, TRUE);
            $year--;
            $i++;
        }

        $voption=array(
            'content'=>$yearview,
        );
        return $voption;

    }

    public function totaldetails() {
        /* totaldetails/?type=proj&year=2013 */
        $year=$this->input->get('year');
        $type=$this->input->get('type');
        /* Orders by year and profit_type */
        $res=$this->orders_model->get_orders_byprofittype($year, $type);
        $options=array(
            'orders'=>$res['orders'],
            'cnt'=>$res['numord'],
            'totals'=>$res['totals'],
        );
        $content=$this->load->view('accounting/orders_profittype_view',$options,TRUE);
        echo $content;
    }

    /* cancel order */
    function cancel_order() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            $flag=$this->input->post('flag');
            $res=$this->orders_model->cancel_order($order_id,$flag, $this->USR_ID);
            if (!$res) {
                $error='Order wasn\'t canceled';
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Include/exclude shipment cost from Profit */
    function order_changeship() {
        if ($this->isAjax()) {
            $mdata=array();
            $order_id=$this->input->post('order_id');
            // $is_shipping=$this->input->post('shipincl');
            $res=$this->orders_model->ship_orderprofit($order_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $order_detail=$this->orders_model->get_order_detail($order_id);
                $mdata['profit']=($order_detail['profit']=='' ? '' : '$'.number_format($order_detail['profit'],2,'.',','));
                $mdata['profit_perc']=($order_detail['profit_perc']=='' ? 'PROJ' : $order_detail['profit_perc'].'%');
                $mdata['profit_class']=$order_detail['profit_class'];
                $mdata['shipinput']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                if ($order_detail['is_shipping']==1) {
                    $mdata['shipinput']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }
    // New order
    public function order_brand() {
        if ($this->isAjax()) {
            $brands =[
                ['brand' => 'SB', 'label' => 'stressball.com only'],
                ['brand' => 'BT', 'label' => 'bluetrack only'],
            ];
            $mdata = [
                'content' => $this->load->view('leadorder/order_brands_view',['brands' => $brands], TRUE),
            ];
            $this->ajaxResponse($mdata, '');
        }
        show_404();
    }
    // Profit (Date)
    public function profit_calendar() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $month=$this->input->post('month');
            $year=$this->input->post('year');
            $brand = $this->input->post('brand');
            if ($brand=='SG') {
                $brand = 'ALL';
            }
            $orders=$this->orders_model->orders_by_date($month,$year, $brand);
            $orders['cnt']=count($orders['data_results']);
            $orders['brand']=$brand;
            $mdata['monthtotal']=$this->load->view('profit_calend/ajax_totalbymonth_view',$orders['month_results'],TRUE);
            $mdata['content']=$this->load->view('profit_calend/ajax_monthcalend_view',$orders,TRUE);
            $mdata['monthname']=$orders['current_month'];
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function profitdate_months() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $year=$this->input->post('year');
            $brand = $this->input->post('brand');
            /* Get Full limits */
            $dats=$this->orders_model->get_profit_limitdates($brand);

            /* Cur Month */
            $error='No Data';
            if (isset($dats['max_year'])) {
                $error = '';
                if ($year==$dats['max_year']) {
                    /* We start new month but there are no orders in this month */
                    $dats['cur_month']=$dats['max_month'];
                    $dats['cur_year']=$dats['max_year'];
                    $dats['min_month']=1;
                } elseif($year==$dats['min_year']) {
                    $dats['cur_year']=$dats['min_year'];
                    $dats['cur_month']=12;
                    $dats['min_month']=$dats['min_month'];
                } else {
                    $dats['cur_year']=$year;
                    $dats['min_month']=1;
                    $dats['cur_month']=12;
                }
                $curyear_months=$this->orders_model->get_months($dats['cur_year'],$dats['cur_month'], $brand, $dats['min_month']);
                $year_links=$this->load->view('accounting/protidate_yearlinks_view',array('lnks'=>$curyear_months,'numrec'=>count($curyear_months)),TRUE);
                $mdata['content']=$year_links;
                $mdata['min_month']=$dats['min_month'];
            }
            /* Build curent year scale with motnt */
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    function dayresults() {
        $date=$this->input->get('day');
        $brand = $this->input->get('brand');
        $orders=$this->orders_model->get_order_bydate($date, $brand);
        $options=array(
            'orders'=>$orders,
            'cnt'=>  count($orders),
            'date'=>$date,
        );
        $content=$this->load->view('profit_calend/orders_bydate_view',$options, TRUE);
        echo $content;
    }

    // Edit Goal value
    public function edit_profitdata_goals() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Edit parameters';
            $year=$this->input->post('year');
            $brand = $this->input->post('brand');
            if (!empty($year) && !empty($brand)) {
                // Get Goal Content
                $error = '';
                $goaldata=$this->orders_model->get_profit_goaldata($year, $brand);
                // Save to Session
                usersession('goaldata', $goaldata);
                $mdata['content']=$this->load->view('profit_calend/goal_edit_view',$goaldata,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Change GOAL parameter
    public function change_profitdata_goals() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // Restore session
            $goaldata=usersession('goaldata');
            if (empty($goaldata)) {
                $error='Connection Lost. Please, recall function';
            } else {
                $field=$this->input->post('field');
                $newval=$this->input->post('newval');
                $res=$this->orders_model->change_goal_value($goaldata, $field, $newval);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['goalavgrevenue']=$res['goalavgrevenue'];
                    $mdata['goalavgprofit']=$res['goalavgprofit'];
                    $mdata['goalavgprofitperc']=$res['goalavgprofitperc'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save GOAL value
    public function save_profitdata_goals() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // Restore session
            $goaldata=usersession('goaldata');
            if (empty($goaldata)) {
                $error='Connection Lost. Please, recall function';
            } else {
                $res=$this->orders_model->save_profitdate_goal($goaldata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $growth=$this->input->post('showgrowth');
                    $brand = $this->input->post('brand');
                    if ($growth==1) {
                        $showgrowth=0;
                    } else {
                        $showgrowth=1;
                    }
                    $out=$this->_prepare_profit_dateslider($brand, $showgrowth);
                    $mdata['content']=$out['content'];
                    $mdata['slider_width']=$out['slider_width'];
                    $mdata['margin']=$out['margin'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    function adminbatchesdata() {
        if ($this->isAjax()) {
            $postdata=$this->input->post();
            $mdata=array();
            $error='Empty Brand';
            $filtr = ifset($postdata, 'filter', 0);
            $year_view = ifset($postdata, 'year', date('Y'));
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $brandcalc = 'ALL';
                $error = '';
                $this->load->model('batches_model');
                /* Get Max & min date  */
                $batch_dates=$this->batches_model->get_batches_limits($brandcalc);
                $max_date=strtotime(date("Y-m-d", time()) . " +5 week");
                $min_date=strtotime(date("Y-m-d",time())." -5 week");
                if (isset($batch_dates['min_date']) && $min_date>$batch_dates['min_date']) {
                    $min_date=$batch_dates['min_date'];
                }
                $mdata['max_date']=$max_date;
                $mdata['min_date']=$min_date;
                $dats=getDatesByWeek(date('W',$min_date),date('Y',$min_date));

                $options=array(
                    'curdate'=>strtotime(date('Y-m-d')),
                    'monday'=>$dats['start_week'],
                    'max_date'=>$max_date,
                    'min_date'=>$min_date,
                    'brand' => $brandcalc,
                );
                if ($filtr!='') {
                    $options['received']=$filtr;
                }
                $options['viewyear']=$year_view;
                /* Batch calendar */
                /*get data about batches from current data */

                $batchdat=$this->batches_model->get_calendar_view($options);

                /* Get totals */
                $calend_total=$this->batches_model->get_calend_totals($options);
                $cnt=count($batchdat);
                $view_options=array(
                    'data'=>$batchdat,
                    'cnt'=>$cnt,
                    'totals'=>$calend_total,
                    'curdate'=>strtotime(date('Y-m-d')),
                );
                $mdata['calendar_view']=$this->load->view('batch/batches_calendar_view',$view_options,TRUE);

                $batchdet=$this->batches_model->get_batchdetails($options);
                $detdat=array();
                /* Add content */
                foreach ($batchdet as $row) {
                    $options=array(
                        'totals'=>$row['totals'],
                        'details'=>$row['lines'],
                        'brand' => $brand,
                    );
                    $content=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                    $detdat[]=array(
                        'batch_date'=>$row['batch_date'],
                        'content'=>$content,
                    );
                }
                $mdata['details']=$this->load->view('batch/batch_detailpart_view',array('details'=>$detdat),TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    // View popup batch due
    public function batchdue()
    {
        $date=$this->input->get('day');
        $this->load->model('batches_model');
        $batches=$this->batches_model->get_batches_duedate($date);
        $options=array(
            'batches'=>$batches,
            'cnt'=>count($batches),
            'date'=>$date,
        );
        $content=$this->load->view('batch/batches_due_view',$options, TRUE);
        echo $content;
    }

    /* Change Batch option EMAILED */
    function batchmailed() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $batch_id=$this->input->post('batch_id');
            if (!$batch_id) {
                $error='Unknown batch';
            } else {
                $mail=$this->input->post('mail');
                $this->load->model('batches_model');
                $res=$this->batches_model->batchmailed($batch_id,$mail);
                $error='Batch wasn\'t updated';
                if ($res==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Change BATCH OPTION received */
    public function batchreceived() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $filtr=$this->input->post('filter');
            $batch_id=$this->input->post('batch_id');
            $brand = $this->input->post('brand');
            if (!$batch_id) {
                $error='Unknown batch';
            } else {
                $this->load->model('batches_model');
                $receiv=$this->input->post('receiv');
                $res=$this->batches_model->batchreceived($batch_id,$receiv);
                $error='Batch wasn\'t updated';
                if ($res==$this->success_result) {
                    $error = '';
                    /* Get data about current day - according to filter statement */
                    $batch_data=$this->batches_model->get_batch_detail($batch_id);
                    $batch_date=strtotime(date('Y-m-d',$batch_data['batch_date']));
                    $batch_enddate = strtotime(date("Y-m-d", $batch_date) . " +1days");
                    $mdata['batch_date']=$batch_date;
                    $mdata['batch_due']=$batch_data['batch_due'];

                    /* prepare new day content */
                    $filter=array(
                        'batch_date'=>$batch_date,
                        'batch_enddate' => $batch_enddate,
                        'brand' => $brand,
                    );
                    if ($filtr!='') {
                        $filter['received']=$filtr;
                    }
                    $batchdetails=$this->batches_model->get_batchdetails_date($filter);
                    if (count($batchdetails['details'])==0) {
                        $mdata['content']='';
                    } else {
                        $options=array(
                            'totals'=>$batchdetails['totals'],
                            'details'=>$batchdetails['details'],
                        );
                        $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                    }
                    /* Get data about calendar by day */
                    /* Calendar */
                    $options=array(
                        'batch_due'=>$batch_data['batch_due'],
                        'brand' => $brand,
                    );
                    if ($filtr!='') {
                        $options['received']=$filtr;
                    }
                    $batch_cal=$this->batches_model->get_batchcalen_date($options);
                    $mdata['calendar_view']=$this->load->view('batch/batch_daycalend_view',$batch_cal,TRUE);
                    /* Totals */
                    $calend_total=$this->batches_model->get_calend_totals($options);
                    $mdata['pendcc']=$calend_total['out_pendcc'];
                    $mdata['terms']=$calend_total['out_term'];
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function delbatchrow() {
        if ($this->isAjax()) {
            $this->load->model('batches_model');
            $mdata=array();
            $error='';
            $batch_id=$this->input->post('batch_id');
            $filtr=$this->input->post('filter');
            $brand = $this->input->post('brand');
            /* get data about batch */
            $batchdata=$this->batches_model->get_batch_detail($batch_id);
            if (!isset($batchdata['batch_id'])) {
                $error='Incorrect batch ID';
            } else {
                /* delete data */
                $res=$this->batches_model->del_batch($batch_id);
                if (!$res) {
                    $error='Delete of batch not execute';
                } else {
                    /* get data by calend */
                    $mdata['batch_date']=$batchdata['batch_date'];
                    $mdata['batch_due']=$batchdata['batch_due'];
                    $options=array(
                        'batch_date'=>$batchdata['batch_date'],
                        'brand' => $brand,
                    );
                    if ($filtr!='') {
                        $options['received']=$filtr;
                    }
                    $batchs=$this->batches_model->get_batchdetails_date($options);
                    $mdata['content']='';
                    if (count($batchs['details'])>0) {
                        $options=array(
                            'totals'=>$batchs['totals'],
                            'details'=>$batchs['details'],
                        );
                        $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                    }
                    /* Calendar */
                    $options=array(
                        'batch_due'=>$batchdata['batch_due'],
                        'brand' => $brand,
                    );
                    if ($filtr!='') {
                        $options['received']=$filtr;
                    }
                    $batch_cal=$this->batches_model->get_batchcalen_date($options);
                    $mdata['calendar_view']=$this->load->view('batch/batch_daycalend_view',$batch_cal,TRUE);
                    /* Totals */
                    $calend_total=$this->batches_model->get_calend_totals($options);
                    $mdata['pendcc']=$calend_total['out_pendcc'];
                    $mdata['terms']=$calend_total['out_term'];
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Edit batch row */
    public function edit_batch() {
        if ($this->isAjax()) {
            $this->load->model('batches_model');
            $mdata=array();
            $error='';
            $batch_id=$this->input->post('batch_id');
            if (!$batch_id) {
                $error='Empty batch';
            } else {
                $batch_data=$this->batches_model->get_batch_detail($batch_id);
                if (!isset($batch_data['batch_id'])) {
                    $error='Incorrect batch ID';
                } else {
                    if ($batch_data['batch_other']!=0 || $batch_data['batch_term']!=0) {
                        $batch_data['due_vie']='<input id="dueedit" class="batchdueedit" readonly="readonly" value="'.date('m/d/Y',$batch_data['batch_due']).'"/>';
                    } else {
                        $batch_data['due_vie']=date('m/d',$batch_data['batch_due']);
                    }
                    $mdata['content']=$this->load->view('batch/batch_editrow_view',$batch_data,TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* cancel_edit */
    public function batch_canceledit() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('batches_model');
            $error='';
            $batch_id=$this->input->post('batch_id');
            $filtr=$this->input->post('filter');
            $brand = $this->input->post('brand');
            /* get data about batch */
            $batchdata=$this->batches_model->get_batch_detail($batch_id);
            if (!isset($batchdata['batch_id'])) {
                $error='Incorrect batch ID';
            } else {
                $mdata['batch_date']=strtotime(date('Y-m-d', $batchdata['batch_date']));
                // $batch_date=$batchdata['batch_date'];
                $batch_date = $mdata['batch_date'];
                $batch_enddate = strtotime(date("Y-m-d", $batch_date) . " +1days");
                $options=array(
                    'batch_date'=>$batch_date,
                    'batch_enddate' => $batch_enddate,
                    'brand' => $brand,
                );
                if ($filtr!='') {
                    $options['received']=$filtr;
                }
                $batchs=$this->batches_model->get_batchdetails_date($options);
                if (count($batchs['details'])>0) {
                    $options=array(
                        'totals'=>$batchs['totals'],
                        'details'=>$batchs['details'],
                    );
                    $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                } else {
                    $mdata['content']='';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* save bathc row */
    public function save_batch() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('batches_model');
            $options=$this->input->post();
            $user_id=$this->USR_ID;
            $filtr=$options['filter'];
            $brand = ifset($options,'brand','SR');
            $batch_data=$this->batches_model->get_batch_detail($options['batch_id']);
            if ($batch_data['order_id']) {
                $options['batch_sum']=($this->batches_model->get_batchsum_order($batch_data['order_id'])-$batch_data['batch_amount']);
                $order_data=$this->orders_model->get_order_detail($batch_data['order_id']);
                $options['order_revenue']=$order_data['revenue'];
                $old_batch_due=$batch_data['batch_due'];
                $batch_date=$batch_data['batch_date'];
                $mdata['batch_date']=strtotime(date('Y-m-d', $batch_date));
                $options['batch_date']=$batch_data['batch_date'];
                $options['batch_writeoff'] = ifset($options,'batch_writeoff',0);
                $res=$this->batches_model->save_batchrow($options,$user_id);
            } else {
                $options['batch_date']=$batch_data['batch_date'];
                $options['batch_writeoff'] = ifset($options,'batch_writeoff',0);
                $old_batch_due=$batch_data['batch_due'];
                $batch_date=$batch_data['batch_date'];
                $mdata['batch_date']=$batch_date;
                $res=$this->batches_model->save_manualrow($options, $user_id);
            }
            /* Previously entered  */
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $new_batch_due=$res['batch_due'];

                $batch_datestart = $mdata['batch_date'];
                $batch_enddate = strtotime(date("Y-m-d", $batch_datestart) . " +1days");

                $options=array(
                    'batch_date'=>$batch_datestart,
                    'batch_enddate' => $batch_enddate,
                    'brand' => $brand,
                );
                if ($filtr!='') {
                    $options['received']=$filtr;
                }
                $batchs=$this->batches_model->get_batchdetails_date($options);
                $mdata['content']='';
                if (count($batchs['details'])>0) {
                    $options=array(
                        'totals'=>$batchs['totals'],
                        'details'=>$batchs['details'],
                        'brand' => $brand,
                    );
                    $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                }
                $mdata['calend_change']=1;
                $mdata['batch_due']=$old_batch_due;
                /* Calendar */
                $options=array(
                    'batch_due'=>$old_batch_due,
                    'brand' => $brand,
                );
                if ($filtr!='') {
                    $options['received']=$filtr;
                }
                $batch_cal=$this->batches_model->get_batchcalen_date($options);
                $mdata['calendar_view']=$this->load->view('batch/batch_daycalend_view',$batch_cal,TRUE);
                /* Totals */
                $calend_total=$this->batches_model->get_calend_totals($options);
                $mdata['pendcc']=$calend_total['out_pendcc'];
                $mdata['terms']=$calend_total['out_term'];
                if ($old_batch_due!=$new_batch_due) {
                    $mdata['calend_change']=2;
                    $mdata['batch_due_second']=$new_batch_due;
                    $options=array(
                        'batch_due'=>$new_batch_due,
                        'brand' => $brand,
                    );
                    if ($filtr!='') {
                        $options['received']=$filtr;
                    }
                    $batch_cal=$this->batches_model->get_batchcalen_date($options);
                    $mdata['calendar_view_second']=$this->load->view('batch/batch_daycalend_view',$batch_cal,TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function batch_addmanual() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Payment date';
            $postdata = $this->input->post();
            $batchdat = ifset($postdata,'batchdate', '');
            if (!empty($batchdat)) {
                $batchdate=strtotime($batchdat);
                $filtr = ifset($postdata,'filter','');
                $brand = ifset($postdata,'brand','SR');

                $newrec=array(
                    'create_usr'=>  $this->USR_ID,
                    'create_date'=>date('Y-m-d H:i:s'),
                    'update_usr'=>  $this->USR_ID,
                    'batch_date'=>$batchdate,
                    'brand' => $brand,
                );
                $this->load->model('batches_model');
                $duedate=$this->batches_model->getAmexDueDate($batchdate,'paypal');
                $newrec['batch_due']=$duedate;
                $res=$this->batches_model->batch_freebatchadd($newrec);
                $error=$res['msg'];
                if ($res['result']=$this->success_result) {
                    $error = '';
                    $options=array(
                        'batch_date'=>$batchdate,
                        'batch_enddate' => strtotime('+1 day', $batchdate),
                        'brand' => $brand,
                    );
                    if ($filtr!='') {
                        $options['received']=$filtr;
                    }
                    $batchs=$this->batches_model->get_batchdetails_date($options);
                    if (count($batchs['details'])>0) {
                        $options=array(
                            'totals'=>$batchs['totals'],
                            'details'=>$batchs['details'],
                            'brand' => $brand,
                        );
                        $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                    } else {
                        $mdata['content']='';
                    }
                }

            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function batchnote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('batches_model');
            $batch_id=$this->input->post('batch_id');
            $batch=$this->batches_model->get_batch_detail($batch_id);
            if (!isset($batch['batch_id'])) {
                $error='Unknown batch';
            } else {
                $mdata['content']=$this->load->view('batch/batchnote_edit_view',$batch,TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function save_batchnote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $batch_id=$this->input->post('batch_id');
            $batch_note=$this->input->post('batch_note');
            $filtr=$this->input->post('filter');
            $brand = $this->input->post('brand');
            $this->load->model('batches_model');
            $this->batches_model->save_batchnote($batch_id,$batch_note);

            $batchdata=$this->batches_model->get_batch_detail($batch_id);
            if (!isset($batchdata['batch_id'])) {
                $error='Incorrect batch ID';
            } else {
                $mdata['batch_date']=$batchdata['batch_date'];
                $batch_date=$batchdata['batch_date'];
                $options=array(
                    'batch_date'=>$batch_date,
                    'brand' => $brand,
                );
                if ($filtr!='') {
                    $options['received']=$filtr;
                }
                $batchs=$this->batches_model->get_batchdetails_date($options);
                $mdata['content']='';
                if (count($batchs['details'])>0) {
                    $options=array(
                        'totals'=>$batchs['totals'],
                        'details'=>$batchs['details'],
                        'brand' => $brand,
                    );
                    $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Batch details per day */
    public function batchdetails() {
        if($this->isAjax()) {
            $this->load->model('batches_model');
            $mdata=array();
            $error='';
            $batch_date=$this->input->post('batch_date');
            $received=$this->input->post('filter');
            $brand = $this->input->post('brand');
            $filter=array(
                'batch_date'=>$batch_date,
                'brand' => $brand,
            );
            if ($received!='') {
                $filter['received']=$received;
            }
            $batchdetails=$this->batches_model->get_batchdetails($filter);
            $options=array(
                'totals'=>$batchdetails['totals'],
                'details'=>$batchdetails['details'],
                'brand' => $brand,
            );
            $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }


    // Private functions - Orders Profit
    private function _prepare_order_profit ($brand) {
        // $search_form=''
        $legend=$this->load->view('accounting/profit_legend_view',array(),TRUE);
        /* Calc total orders */
        $options=[];
        $options['brand'] = $brand;
        $options['exclude_quickbook']=1;
        $total_rec=$this->orders_model->get_count_orders($options);
        $options=array();
        /* Prepare per page view */
        $perpage_data=array(
            'fieldname'=>'perpage_profitorders',
            'default_value'=>$this->order_profit_perpage,
            'numrecs'=>$this->perpage_options,
        );
        $perpage_view=$this->load->view('page/number_records', $perpage_data, TRUE);
        $view_options=array(
            'total'=>$total_rec,
            'order'=>'o.order_num',
            'direc'=>'desc',
            'perpage_view'=>$perpage_view,
            'curpage'=>0,
            // 'searchform'=>$search_form,
            'legend'=>$legend,
            'total_row'=>'', // $totalrow,
            // 'adminview' => $this->USR_ROLE=='masteradmin' ? 1 : 0,
            'adminview' => $this->USER_ORDER_EXPORT==1 ? 1 : 0,
            'brand' => $brand,
        );
        $years=$this->orders_model->get_orders_dates($options);
        $orders_cnttotal='';
        $years_options=[];
        $years_options[]=['key'=>0, 'label'=>'All time'];
        $select_year=$years['max_year'];
        for ($i=0; $i<100; $i++) {
            $years_options[]=['key'=>$select_year,'label'=>$select_year];
            $select_year=intval($select_year)-1;
            if ($select_year<$years['min_year']) {
                break;
            }
        }
        $view_options['years']=$years_options;
        $view_options['bottom_view']=$this->load->view('accounting/admin_bottom_view',array('year'=>$years,'orders_cnttotal'=>$orders_cnttotal),TRUE);

        $content=$this->load->view('orderprofit/admin_head_view',$view_options,TRUE);

        return $content;
    }

    private function _prepare_profitcalend_content($brand) {
        if ($brand=='SG') {
            $brand = 'ALL';
        }
        $dats=$this->orders_model->get_profit_limitdates($brand);
        $legend=$this->load->view('accounting/profit_legend_view',[],TRUE);
        /* Cur Month */
        if (isset($dats['max_year'])) {
            if (date('Y-m',time())!=$dats['max_year'].'-'.$dats['max_month']) {
                /* We start new month but there are no orders in this month */
                $dats['cur_month']=$dats['max_month'];
                $dats['cur_year']=$dats['max_year'];
            } else {
                $dats['cur_month']=date('m');
                $dats['cur_year']=date('Y');
            }
        } else {
            $dats['max_month']=date('m');
            $dats['max_year']=date('Y');
            $dats['max_date']=time();
            $dats['cur_month']=date('m');
            $dats['cur_year']=date('Y');
        }
        /* Build curent year scale with month */

        $curyear_months=$this->orders_model->get_months($dats['cur_year'],$dats['cur_month'], $brand);
        $year_links=$this->load->view('accounting/protidate_yearlinks_view',array('lnks'=>$curyear_months,'numrec'=>count($curyear_months)),TRUE);
//        $slider=$this->_prepare_profit_dateslider($brand, 1);
//        $yearview=$slider['content'];
//        $slider_width=$slider['slider_width'];
//        $margin=$slider['margin'];
        $yearview = ''; $slider_width = 0; $margin = 0;
        // Get Previous Month
        $datefiltr=new DateTime('NOW');
        $datefiltr->modify('-1 Month');

        $options=array(
            'brand' => $brand,
            'legend'=>$legend,
            'max_year'=>$dats['max_year'],
            'month_name'=>date('F',$dats['max_date']),
            'max_month'=>$dats['max_month'],
            'min_year'=>$dats['min_year'],
            'min_month'=>$dats['min_month'],
            'year_links'=>$year_links,
            'cur_month'=>$dats['cur_month'],
            'cur_year'=>$dats['cur_year'],
            'yearsview'=>$yearview,
            'slider_width'=>$slider_width,
            'start_margin'=>($margin>0 ? 0 : ceil($margin)),
            'showgrowth'=>1,
            'showhidegrowth'=>'[hide growth]',
            'montfilterend'=>$datefiltr->format('m'),
        );
        $content=$this->load->view('accounting/profit_date_view',$options,TRUE);
        return $content;
    }

    public function profit_calendar_totals() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $slider=$this->_prepare_profit_dateslider($brand, 1);
                $mdata['yearview']=$slider['content'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    //
    public function profitdate_showgrowth() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $showgrowth=$this->input->post('showgrowth');
            $brand = $this->input->post('brand');
            $out=$this->_prepare_profit_dateslider($brand, $showgrowth);
            $mdata['content']=$out['content'];
            $mdata['slider_width']=$out['slider_width'];
            $mdata['margin']=$out['margin'];
            if ($showgrowth==1) {
                $mdata['label']='[hide growth]';
            } else {
                $mdata['label']='[show growth]';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function filter_profitdate_showgrowth() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata=$this->input->post();
            $showgrowth=(isset($postdata['showgrowth']) ? $postdata['showgrowth'] : 1);
            $startdate=(isset($postdata['startdate']) ? $postdata['startdate'] : '01');
            $enddate=(isset($postdata['enddate']) ? $postdata['enddate'] : '01');
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $out=$this->_prepare_filter($startdate, $enddate, $brand, $showgrowth);
                $mdata['content']=$out['content'];
                $mdata['slider_width']=$out['slider_width'];
                $mdata['margin']=$out['margin'];

                if ($showgrowth==1) {
                    $mdata['label']='[hide growth]';
                } else {
                    $mdata['label']='[show growth]';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Payments Monitor
    public function adminpaymonitordat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            $paid=$this->input->post('paid',0);
            $search=$this->input->post('search');
            $offset=$offset*$limit;
            $brand = $this->input->post('brand');
            /* Get data about orders */
            $filtr=array(
                'paid'=>$paid,
                'search'=>$search,
                'brand' => $brand,
            );
            $totals=$this->orders_model->get_totals_monitor($brand);
            $mdata['not_invoiced']=$totals['sum_invoice'];
            $mdata['not_paid']=$totals['sum_paid'];
            $mdata['qty_inv']=$totals['qty_inv'];
            $mdata['qty_paid']=$totals['qty_paid'];
            $orders=$this->orders_model->get_paymonitor_data($filtr,$order_by,$direct,$limit,$offset, $this->USR_ID);
            if (count($orders)==0) {
                $mdata['content']=$this->load->view('finopenivoice/empty_monitordat_view',array(),TRUE);
            } else {
                $mdata['content']=$this->load->view('finopenivoice/monitordat_view',array('orders'=>$orders),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function calc_monitor() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $paid = ifset($postdata, 'paid', '');
            $search = ifset($postdata, 'search', '');
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $filtr=array(
                    'paid'=>$paid,
                    'search'=>$search,
                    'brand' => $brand,
                );
                $res=$this->orders_model->get_count_monitor($filtr);
                $mdata['totals']=$res['total_rec'];
                if ($paid==1) {
                    $mdata['paidlink']='[hide paid]';
                } elseif ($paid==0) {
                    $mdata['paidlink']='[show paid]';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    /* Event CLICK on INV */
    public function inviteorder() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            $is_invited=$this->input->post('is_invited');
            $brand = $this->input->post('brand');
            $res=$this->orders_model->ordinvite($order_id,$is_invited);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $order_data=$this->orders_model->get_monitor_data($order_id);
                $res=$this->orders_model->get_totals_monitor($brand);
                $mdata['not_invoiced']=$res['sum_invoice'];
                $mdata['not_paid']=$res['sum_paid'];
                $mdata['qty_inv']=$res['qty_inv'];
                $mdata['qty_paid']=$res['qty_paid'];
                $mdata['content']=$this->load->view('finopenivoice/paymonitor_line_view',array('order'=>$order_data),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

//    /* Event CLICK on PAID */
//    function payorder() {
//        if ($this->isAjax()) {
//            $mdata=array();
//            $order_id=$this->input->post('order_id');
//            $is_paid=$this->input->post('is_paid');
//            $brand = $this->input->post('brand');
//            $res=$this->orders_model->orderpay($order_id,$is_paid, $brand);
//            $error=$res['msg'];
//            if ($res['result']==$this->success_result) {
//                $error = '';
//                $mdata['content']=$this->load->view('finopenivoice/paymonitor_line_view',array('order'=>$res['order']),TRUE);
//                $mdata['not_invoiced']=$res['invoice'];
//                $mdata['not_paid']=$res['paid'];
//                $mdata['qty_inv']=$res['qty_inv'];
//                $mdata['qty_paid']=$res['qty_paid'];
//            }
//            $this->ajaxResponse($mdata, $error);
//        }
//    }

    /* Event CLICK on PAY - call batch */
    public function paybatch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            /* Get list of Batches */
            $order_details=$this->orders_model->get_order_detail($order_id);
            if ($order_details['is_canceled']==1) {
                $order_details['revenue']=0;
            }
            $this->load->model('batches_model');
            $batchsum=$this->batches_model->get_batchsum_order($order_id);
            $amount=$order_details['revenue']-$batchsum;
            if ($amount==0) {
                $error='Order paid';
            } else {
                $options=array(
                    'order_id'=>$order_id,
                    'amount'=>$amount,
                    'batch_note'=>'',
                    'is_cancel'=>$order_details['is_canceled'],
                );
                $mdata['content']=$this->load->view('finopenivoice/batchselect_view',$options,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Show table with orders */
    public function batchdetailview() {
        if($this->isAjax()) {
            $mdata=array();
            $error='';
            $date=$this->input->post('date');
            $paymethod=$this->input->post('paymethod');
            $date=strtotime($date);
            /*  get data about batch orders */
            $filter=array(
                'batch_date'=>$date,
            );
            $this->load->model('batches_model');
            $batchdetails=$this->batches_model->get_batchdetails_date($filter);
            $mdata['dayresults']=$batchdetails['totals']['day_results'];
            $options=array(
                'totals'=>$batchdetails['totals'],
                'details'=>$batchdetails['details'],
            );
            $mdata['content']=$this->load->view('finopenivoice/batchselect_table_view',$options,TRUE);
            /* Recalculate DUE Date */
            $datdue=$this->batches_model->get_batchdue($date,$paymethod);
            $mdata['datedue']=$datdue;
            $mdata['edit_option']=0;
            $mdata['dateinpt']='Due '.date('m/d/Y',$datdue);
            if ($paymethod=='o' || $paymethod=='t') {
                $mdata['edit_option']=1;
                $due='<div style="color: #000000;float: left;font-size: 13px; padding-top: 3px; width: 29px;">Due</div>';
                $mdata['dateinpt']=$due.'<input type="text" id="datdue" class="selectbatchunit" style="margin-top: -3px;" readonly="readonly" value="'.date('m/d/Y',$datdue).'"/>';
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Change Paymethod  */
    public function batch_paymethod() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $date=$this->input->post('date');
            $paymethod=$this->input->post('paymethod');
            $date=strtotime($date);
            /* Recalculate DUE Date */
            $this->load->model('batches_model');
            $datdue=$this->batches_model->get_batchdue($date,$paymethod);
            $mdata['datedue']=$datdue;
            $mdata['edit_option']=0;
            $mdata['dateinpt']='Due '.date('m/d/Y',$datdue);
            if ($paymethod=='o' || $paymethod=='t') {
                $mdata['edit_option']=1;
                $due='<div style="color: #000000;float: left;font-size: 13px; padding-top: 3px; width: 29px;">Due</div>';
                $mdata['dateinpt']=$due.'<input type="text" id="datdue" class="selectbatchunit" style="margin-top: -3px;" readonly="readonly" value="'.date('m/d/Y',$datdue).'"/>';
                $mdata['dateeditinpt']='<input id="dueedit" class="batchdueedit" readonly="readonly" value="'.date('m/d/Y',$datdue).'"/>';
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function change_datedue() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $duedate=$this->input->post('date');
            $duedate=strtotime($duedate);
            $this->load->model('calendars_model');
            $duedate=$this->calendars_model->businessdate($duedate);
            $mdata['datedue']=$duedate;
            $mdata['datedueformat']=date('m/d/Y',$duedate);
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    /* Save batch */
    public function savebatch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $date=$this->input->post('date');
            $batch_date=strtotime($date);
            $paymethod=$this->input->post('paymethod');
            $amount=$this->input->post('amount');
            $amount=floatval($amount);
            $batch_note=$this->input->post('batch_note');
            $datedue=$this->input->post('datedue');
            $order_id=$this->input->post('order_id');
            $brand = $this->input->post('brand');
            $error='Empty order data';
            if ($order_id) {
                $options=array(
                    'batch_id'=>0,
                    'batch_date'=>$batch_date,
                    'paymethod'=>$paymethod,
                    'amount'=>$amount,
                    'order_id'=>$order_id,
                    'batch_note'=>$batch_note,
                    'datedue'=>$datedue,
                );
                /* Order Details */
                $order_data=$this->orders_model->get_order_detail($order_id);
                /* Save results */
                $user_id=$this->USR_ID;
                $this->load->model('batches_model');
                $res=$this->batches_model->save_batch($options, $order_data,$user_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $filter=array(
                        'batch_date'=>$batch_date,
                    );
                    $batchdetails=$this->batches_model->get_batchdetails_date($filter);
                    $options=array(
                        'totals'=>$batchdetails['totals'],
                        'details'=>$batchdetails['details'],
                    );
                    $mdata['dayresults']=$batchdetails['totals']['day_results'];
                    $mdata['content']=$this->load->view('finopenivoice/batchselect_table_view',$options,TRUE);
                }
            }
        }
        $this->ajaxResponse($mdata,$error);
    }

    /* Prepare form for halp payment */
    public function customer_payment() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            /* Get order Data */
            $order_data=$this->orders_model->get_order_detail($order_id);
            $sum_notpaid=floatval($order_data['revenue']);
            $paid_sum=floatval($order_data['paid_sum']);
            $data=array(
                'order_id'=>$order_id,
                'revenue'=>$sum_notpaid,
                'paid_sum'=>number_format($paid_sum,2,'.',''),
            );
            $mdata['content']=$this->load->view('accounting/editpaidsum_view',$data,TRUE);

            $this->ajaxResponse($mdata, $error);
        }
    }

    public function save_custompay() {
        if ($this->isAjax()) {
            $mdata=array();
            $order_id=$this->input->post('order_id');
            $paid_sum=$this->input->post('paid_sum');
            $brand = $this->input->post('brand');
            $res=$this->orders_model->save_custompayment($order_id,$paid_sum, $brand);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['not_invoiced']=$res['invoice'];
                $mdata['not_paid']=$res['paid'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function order_note() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            $order_dat=$this->orders_model->get_order_detail($order_id);
            $mdata['content']=$this->load->view('accounting/ordernote_edit_view',$order_dat,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function save_ordernote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            $order_note=$this->input->post('order_note');
            $res=$this->orders_model->update_ordernote($order_id,$order_note);
            $order=$this->orders_model->get_order_detail($order_id);
            $order=$this->orders_model->order_data_profit($order);
            $mdata['content']=$this->load->view('finopenivoice/paymonitor_line_view',array('order'=>$order),TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    /* Show docs attached to Order */
    public function order_viewattach() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $order_id=$this->input->post('order_id');
            $order_attach=$this->orders_model->get_order_artattachs($order_id);
            if (count($order_attach)==0) {
                $error='No attachments to this order';
            } else {
                $mdata['attachments']=$order_attach;

            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function opercalcdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $sort = ifset($postdata, 'sort','percent');
            $direction = ifset($postdata, 'direction', 'desc');
            $brand = ifset($postdata,'brand','SB');
            $calc=$this->balances_model->get_calcdata($sort,$direction, $brand);
            $calc_data=$calc['body'];
            if (count($calc_data)==0) {
                $mdata['content']=$this->load->view('expensives/tabledata_empty_view',array('brand' => $brand),TRUE);
            } else {
                $expand = 0;
                if (count($calc_data)<23) {
                    $expand = 1;
                }
                $mdata['content']=$this->load->view('expensives/tabledata_view',array('datas'=>$calc['body'],  'brand' => $brand, 'expand' => $expand),TRUE);
            }
            $mdata['total_month']=$calc['sums']['month'];
            $mdata['total_week']=$calc['sums']['week'];
            // $mdata['total_quart']=$calc['sums']['quart'];
            $mdata['total_year']=$calc['sums']['year'];
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function calcrow() {
        if ($this->isAjax()) {
            $mdata=array();
            $calc_id=$this->input->post('calc_id');
            $calcres=$this->balances_model->get_calcrow_data($calc_id);
            $error = $calcres['msg'];
            if ($calcres['result']==$this->success_result) {
                $error='';
                $calcdata = $calcres['data'];
                $calcdata['date_edit'] = $this->load->view('expensives/dateday_edit_view', $calcdata, TRUE);
                $mdata['content']=$this->load->view('expensives/edit_form_view',$calcdata,TRUE);
                $mdata['datetype'] = $calcdata['date_type'];
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function calc_edit_type() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $error = 'Empty Expense Type';
            if (ifset($postdata, 'date_type','')!=='') {
                $error = '';
                $date_type = $postdata['date_type'];
                $calcdata = [
                    'date_type' => $postdata['date_type'],
                    'date_day' => '',
                ];
                $mdata['daydatecontent'] = $this->load->view('expensives/dateday_edit_view', $calcdata, TRUE);
                $yearinpt_options = [
                    'checked' => $date_type=='year' ? 1 : 0,
                    'datetype' => 'year',
                ];
                $mdata['yearcontent'] = $this->load->view('expensives/weekedit_input_view', $yearinpt_options, TRUE);
                $monthinpt_options = [
                    'checked' => $date_type=='month' ? 1 : 0,
                    'datetype' => 'month',
                ];
                $mdata['monthcontent'] = $this->load->view('expensives/weekedit_input_view', $monthinpt_options, TRUE);
                $weekinpt_options = [
                    'checked' => $date_type=='week' ? 1 : 0,
                    'datetype' => 'week',
                ];
                $mdata['weekcontent'] = $this->load->view('expensives/weekedit_input_view', $weekinpt_options, TRUE);
                $mdata['weektotal'] = '';
                $mdata['yeartotal'] = '';
                $mdata['percentval'] = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function calc_edit_amount() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $error = 'Empty Expense Type';
            if (ifset($postdata, 'date_type','')!=='') {
                $calc_id=$this->input->post('calc_id');
                $options = [];
                $options['date_type'] = ifset($postdata, 'date_type', 'year');
                $options['brand'] = ifset($postdata, 'brand', 'SB');
                $options['amount'] = ifset($postdata, 'amount', 0);
                $calcres=$this->balances_model->calcrow_amount_update($calc_id, $options);
                $error = $calcres['msg'];
                if ($calcres['result']==$this->success_result) {
                    $error = '';
                    $mdata['weektotal'] = $calcres['weektotal'];
                    $mdata['yeartotal'] = $calcres['yeartotal'];
                    $mdata['percentval'] = $calcres['percentval'];
                }

            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function calcsave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $options=array();
            $options['calc_id']=ifset($postdata, 'calc_id','0');
            $options['date_type'] = ifset($postdata,'date_type');
            $options['yearsum'] = ifset($postdata,'yearsum',0);
            $options['monthsum']=ifset($postdata, 'monthsum','');
            $options['weeksum']=ifset($postdata, 'weeksum','');
            $options['method'] = ifset($postdata,'method','');
            $options['date_day'] = ifset($postdata, 'date_day','');
            $options['description']=ifset($postdata, 'descr','');
            $brand = ifset($postdata, 'brand','');
            if (!empty($brand)) {
                $options['brand']=$brand;
                $res=$this->balances_model->save_calcdata($options);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function calcdelete() {
        if ($this->isAjax()) {
            $mdata=array();
            $calc_id=$this->input->post('calc_id');
            if (!$calc_id) {
                $error='Unknown calculator row';
            } else {
                $res=$this->balances_model->delete_calcdata($calc_id);
                $error='Data wasn\'t deleted';
                if ($res) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function ownnertaxes_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $this->load->model('rates_model');
                // Get current week, year
                $res=$this->balances_model->get_ownertax_dates();
                $now=$res['date'];
                $curyear=$res['year'];
                $curyearshort=substr($curyear,2);

                // Prepare Single view
                $singfedrates=$this->rates_model->get_ratestable('single','federal');

                $singfedoptions=array(
                    'title'=>'`'.$curyearshort.' Federal Tax Brackets',
                    'rates'=>$singfedrates,
                );
                $singlefed_rate_view=$this->load->view('ownertaxes/rates_view', $singfedoptions,TRUE);

                $singlestaterates=$this->rates_model->get_ratestable('single','state');

                $singlestateoptions=array(
                    'title'=>'`'.$curyearshort.' NJ Tax Brackets',
                    'rates'=>$singlestaterates,
                );

                $singlestate_rate_view=$this->load->view('ownertaxes/rates_view', $singlestateoptions,TRUE);
                $siglerate_options=array(
                    'fedtax'=>$singlefed_rate_view,
                    'state'=>$singlestate_rate_view,
                );
                $siglerateview=$this->load->view('ownertaxes/calclrates_view', $siglerate_options, TRUE);

                // Get NetProfit
                $paceincome=$paceexpense=1;
                $netprofit=$this->balances_model->get_projected_netprofit($now, $curyear, $paceincome, $paceexpense, $brand);
                // $netprofit=400000;
                if (strtoupper($this->USER_REPLICA)=='SEAN') {
                    $profitkf=0.75;
                } else {
                    $profitkf=0.25;
                }

                // Get data for single calc

                $singledata=$this->rates_model->get_ownertaxdata('single', $this->USER_REPLICA, $brand);
                $singledata['netprofit']=$netprofit;
                $singledata['profitkf']=$profitkf;
                $singledata['od_incl']=1;

                $siglecalcdata=$this->rates_model->get_owner_taxes('single', $singledata);

                $singcalcoption=array(
                    'rateview'=>$siglerateview,
                    'profitkf'=>$profitkf,
                    'brand' => $brand,
                );
                foreach ($siglecalcdata as $key=>$val) {
                    $singcalcoption[$key]=$val;
                }
                $singlecalcview=$this->load->view('ownertaxes/singlecalc_view', $singcalcoption, TRUE);

                // Prepare Join Calc
                $jointfedrates=$this->rates_model->get_ratestable('joint','federal');

                $jointfedoptions=array(
                    'title'=>'`'.$curyearshort.' Federal Tax Brackets',
                    'rates'=>$jointfedrates,
                );

                $jointfed_rate_view=$this->load->view('ownertaxes/rates_view', $jointfedoptions,TRUE);

                $jointstaterates=$this->rates_model->get_ratestable('joint','state');

                $jointstateoptions=array(
                    'title'=>'`'.$curyearshort.' NJ Tax Brackets',
                    'rates'=>$jointstaterates,
                );

                $jointstate_rate_view=$this->load->view('ownertaxes/rates_view', $jointstateoptions,TRUE);
                $jointrate_options=array('fedtax'=>$jointfed_rate_view,'state'=>$jointstate_rate_view);
                $jointrateview=$this->load->view('ownertaxes/calclrates_view', $jointrate_options, TRUE);
                // Get data for joint calc
                $jointdata=$this->rates_model->get_ownertaxdata('joint', $this->USER_REPLICA, $brand);
                $jointdata['netprofit']=$netprofit;
                $jointdata['profitkf']=$profitkf;
                $jointdata['od_incl']=1;

                $jointcalcdata=$this->rates_model->get_owner_taxes('joint', $jointdata);

                $jointcalcoption=array(
                    'rateview'=>$jointrateview,
                    'brand' => $brand,
                );
                foreach ($jointcalcdata as $key=>$val) {
                    $jointcalcoption[$key]=$val;
                }
                $jointcalcview=$this->load->view('ownertaxes/jointcalc_view', $jointcalcoption, TRUE);
                $session_id=uniq_link('15');
                $content_options=array(
                    'singlecalc'=>$singlecalcview,
                    'jointcalc'=>$jointcalcview,
                    'od_incl'=>0,
                    'session_id'=>$session_id,
                    'netprofit'=>$netprofit,
                    'profitkf'=>$profitkf*100,
                    'ownership'=>round($netprofit*$profitkf,0),
                    'year'=>$curyear,
                );
                $calcdata=array(
                    'joint'=>$jointcalcdata,
                    'single'=>$siglecalcdata,
                    'now'=>$now,
                    'curyear'=>$curyear,
                );
                usersession($session_id, $calcdata);
                $mdata['content']=$this->load->view('ownertaxes/page_view', $content_options, TRUE);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function ownnertaxes_change() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = $this->restore_data_error;
            $postdata = $this->input->post();
            $session_id = (isset($postdata['calcsession']) ? $postdata['calcsession'] : 'test');
            $calcsession = usersession($session_id);
            if (!empty($calcsession)) {
                // Params
                $fldname = $postdata['fldname'];
                $calc_type = $postdata['calc_type'];
                $newval = $postdata['newval'];
                // Update
                $this->load->model('rates_model');
                $res = $this->rates_model->taxowner_change($calcsession, $calc_type, $fldname, $newval, $session_id);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    // Out
                    $mdata['newval'] = ($res['newval'] == 0 ? '' : MoneyOutput($res['newval'], 2));
                    $calc = $res['calc'];
                    $mdata['total_income'] = ($calc['total_income'] == 0 ? $this->empty_html_content : MoneyOutput($calc['total_income'], 2));
                    $mdata['taxable_income'] = ($calc['taxable_income'] == 0 ? $this->empty_html_content : MoneyOutput($calc['taxable_income'], 2));
                    $mdata['fed_taxes'] = ($calc['fed_taxes'] == 0 ? $this->empty_html_content : MoneyOutput($calc['fed_taxes'], 2));
                    $mdata['fed_taxes_due'] = ($calc['fed_taxes_due'] == 0 ? $this->empty_html_content : MoneyOutput($calc['fed_taxes_due'], 2));
                    $mdata['fed_pay'] = ($calc['fed_pay'] == 0 ? $this->empty_html_content : MoneyOutput($calc['fed_pay'], 2));
                    $mdata['state_taxes'] = ($calc['state_taxes'] == 0 ? $this->empty_html_content : MoneyOutput($calc['state_taxes'], 2));
                    $mdata['state_taxes_due'] = ($calc['state_taxes_due'] == 0 ? $this->empty_html_content : MoneyOutput($calc['state_taxes_due'], 2));
                    $mdata['state_pay'] = ($calc['state_pay'] == 0 ? $this->empty_html_content : MoneyOutput($calc['state_pay'], 2));
                    $mdata['take_home'] = ($calc['take_home'] == 0 ? $this->empty_html_content : MoneyOutput($calc['take_home'], 2));
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function ownnertaxes_odincl() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=  $this->restore_data_error;
            $postdata=$this->input->post();
            $session_id=(isset($postdata['calcsession']) ? $postdata['calcsession'] : 'test');
            $calcsession=usersession($session_id);
            if (!empty($calcsession)) {
                $od_incl=$postdata['od_incl'];
                $this->load->model('rates_model');
                $res=$this->rates_model->taxowner_change_odincl($calcsession, $od_incl, $session_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $calcsession=usersession($session_id);
                    // Prepare output
                    $single=$calcsession['single'];
                    $mdata['single_total_income']=($single['total_income']==0 ? $this->empty_html_content : MoneyOutput($single['total_income'],2));
                    $mdata['single_taxable_income']=($single['taxable_income']==0 ? $this->empty_html_content : MoneyOutput($single['taxable_income'],2));
                    $mdata['single_fed_taxes']=($single['fed_taxes']==0 ? $this->empty_html_content : MoneyOutput($single['fed_taxes'],2));
                    $mdata['single_fed_taxes_due']=($single['fed_taxes_due']==0 ? $this->empty_html_content : MoneyOutput($single['fed_taxes_due'],2));
                    $mdata['single_fed_pay']=($single['fed_pay']==0 ? $this->empty_html_content : MoneyOutput($single['fed_pay'],2));
                    $mdata['single_state_taxes']=($single['state_taxes']==0 ? $this->empty_html_content : MoneyOutput($single['state_taxes'],2));
                    $mdata['single_state_taxes_due']=($single['state_taxes_due']==0 ? $this->empty_html_content : MoneyOutput($single['state_taxes_due'],2));
                    $mdata['single_state_pay']=($single['state_pay']==0 ? $this->empty_html_content : MoneyOutput($single['state_pay'],2));
                    $mdata['single_take_home']=($single['take_home']==0 ? $this->empty_html_content : MoneyOutput($single['take_home'],2));
                    $joint=$calcsession['joint'];
                    $mdata['joint_total_income']=($joint['total_income']==0 ? $this->empty_html_content : MoneyOutput($joint['total_income'],2));
                    $mdata['joint_taxable_income']=($joint['taxable_income']==0 ? $this->empty_html_content : MoneyOutput($joint['taxable_income'],2));
                    $mdata['joint_fed_taxes']=($joint['fed_taxes']==0 ? $this->empty_html_content : MoneyOutput($joint['fed_taxes'],2));
                    $mdata['joint_fed_taxes_due']=($joint['fed_taxes_due']==0 ? $this->empty_html_content : MoneyOutput($joint['fed_taxes_due'],2));
                    $mdata['joint_fed_pay']=($joint['fed_pay']==0 ? $this->empty_html_content : MoneyOutput($joint['fed_pay'],2));
                    $mdata['joint_state_taxes']=($joint['state_taxes']==0 ? $this->empty_html_content : MoneyOutput($joint['state_taxes'],2));
                    $mdata['joint_state_taxes_due']=($joint['state_taxes_due']==0 ? $this->empty_html_content : MoneyOutput($joint['state_taxes_due'],2));
                    $mdata['joint_state_pay']=($joint['state_pay']==0 ? $this->empty_html_content : MoneyOutput($joint['state_pay'],2));
                    $mdata['joint_take_home']=($joint['take_home']==0 ? $this->empty_html_content : MoneyOutput($joint['take_home'],2));
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function ownnertaxes_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            $session_id=(isset($postdata['calcsession']) ? $postdata['calcsession'] : 'test');
            $calcsession=usersession($session_id);
            $brand = ifset($postdata,'brand');
            if (!empty($calcsession)) {
                $this->load->model('rates_model');
                $res=$this->rates_model->taxowner_save($calcsession, $this->USER_REPLICA, $session_id, $brand);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function old_netprofitdat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $type=$this->input->post('type');
            $radio=$this->input->post('radio');
            $datbgn=$this->config->item('netprofit_start');
            $order_by=$this->input->post('order_by');
            $limitshow=$this->input->post('limitshow');
            $brand = $this->input->post('brand');
            $order='nd.datebgn';
            $direc='desc';
            switch ($order_by) {
                case 'profitdate_desc':
                    break;
                case 'profitdate_asc':
                    $direc='asc';
                    break;
                case 'sales_desc':
                    $order='sales';
                    break;
                case 'sales_asc':
                    $order='sales';
                    $direc='asc';
                    break;
                case 'revenue_desc':
                    $order='revenue';
                    break;
                case 'revenue_desc':
                    $order='revenue';
                    $direc='asc';
                    break;
                case 'grosprofit_desc':
                    $order='gross_profit';
                    break;
                case 'grosprofit_asc':
                    $order='gross_profit';
                    $direc='asc';
                    break;
                case 'operating_desc':
                    $order='np.profit_operating';
                    break;
                case 'operating_asc':
                    $order='np.profit_operating';
                    $direc='asc';
                    break;
                case 'payroll_desc':
                    $order='profit_payroll';
                    break;
                case 'payroll_asc':
                    $order='profit_payroll';
                    $direc='asc';
                    break;
                case 'advertising_desc':
                    $order='profit_advertising';
                    break;
                case 'advertising_asc':
                    $order='profit_advertising';
                    $direc='asc';
                    break;
                case 'projects_desc':
                    $order='profit_projects';
                    break;
                case 'projects_asc':
                    $order='profit_projects';
                    $direc='asc';
                    break;
                case 'w9work_desc':
                    $order='profit_projects';
                    break;
                case 'w9work_asc':
                    $order='profit_projects';
                    $direc='asc';
                    break;
                case 'purchases_desc':
                    $order='profit_purchases';
                    break;
                case 'purchases_asc':
                    $order='profit_purchases';
                    $direc='asc';
                    break;
                case 'totalcost_desc':
                    $order='totalcost';
                    break;
                case 'totalcost_asc':
                    $order='totalcost';
                    $direc='asc';
                    break;
                case 'netprofit_desc':
                    $order='netprofit';
                    break;
                case 'netprofit_asc':
                    $order='netprofit';
                    $direc='asc';
                    break;
                case 'netsaved_desc':
                    $order='np.profit_saved';
                    break;
                case 'netsaved_asc':
                    $order='np.profit_saved';
                    $direc='asc';
                    break;
                case 'owners_desc':
                    $order='np.profit_owners';
                    break;
                case 'owners_asc':
                    $order='np.profit_owners';
                    $direc='asc';
                    break;
                case 'od2_desc':
                    $order='np.od2';
                    break;
                case 'od2_asc':
                    $order='np.od2';
                    $direc='asc';
                    break;
            }
            if ($type=='week') {
                $this->balances_model->_check_current_week($this->USR_ID);
                $dat_end=date('Y-m-d', strtotime("Sunday this week", time())).' 23:59:59';
                $datend=strtotime($dat_end);
                $data=$this->balances_model->get_netprofit_data($datbgn,$datend, $order, $direc, $this->USR_ID, $radio, $brand, $limitshow);
            } else {
                $curmonth=date('m');
                $curyear=date('Y');
                $datend=strtotime($curmonth.'/01/'.$curyear.' 00:00:00');
                $datend=strtotime(date("Y-m-d", ($datend)) . " +1 month");
                $datend=$datend-1;
                $data=$this->balances_model->get_netprofit_monthdata($datbgn,$datend, $order, $direc, $this->USR_ID, $radio, $brand);
            }
            if ($type=='week') {
                $fromweek=$this->input->post('fromweek');
                $untilweek=$this->input->post('untilweek');
                $options=array(
                    'type'=>'week',
                    'start'=>0,
                    'end'=>0,
                    'brand' => $brand,
                );
                // Get start && end date
                if ($fromweek) {
                    $res=$this->balances_model->getweekdetail($fromweek,'start');
                    if ($res['result']==$this->error_result) {
                        $this->ajaxResponse($mdata, $res['msg']);
                    }
                    $options['start']=$res['date'];
                }
                if ($untilweek) {
                    $res=$this->balances_model->getweekdetail($untilweek,'end');
                    if ($res['result']==$this->error_result) {
                        $this->ajaxResponse($mdata, $res['msg']);
                    }
                    $options['end']=$res['date'];
                }
                $runtotal=$this->balances_model->get_netprofit_runs($options, $radio);
            } else {
                $total_options=array(
                    'type'=>$type,
                    'start'=>$this->config->item('netprofit_start'),
                    'brand' => $brand,
                );
                $runtotal=$this->balances_model->get_netprofit_runs($total_options, $radio);
            }
            $total_view=$this->load->view('netprofit/netprofit_totals_view', $runtotal, TRUE);
            $content_options=array(
                'data'=>$data,
                'totals'=>$total_view,
                'limitshow'=>$limitshow,
                'brand' => $brand,
            );
            $mdata['content']=$this->load->view('netprofit/netprofit_tabledata_view',$content_options,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }


    public function get_weektotals() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $fromweek=$this->input->post('fromweek');
            $untilweek=$this->input->post('untilweek');
            $brand = $this->input->post('brand');
            $options=array(
                'type'=>'week',
                'start'=>0,
                'end'=>0,
                'brand' => $brand,
            );
            // Get start && end date
            if ($fromweek) {
                $res=$this->balances_model->getweekdetail($fromweek,'start');
                if ($res['result']==$this->error_result) {
                    $this->ajaxResponse($mdata, $res['msg']);
                }
                $options['start']=$res['date'];
            }
            if ($untilweek) {
                $res=$this->balances_model->getweekdetail($untilweek,'end');
                if ($res['result']==$this->error_result) {
                    $this->ajaxResponse($mdata, $res['msg']);
                }
                $options['end']=$res['date'];
            }
            $totals=$this->balances_model->get_netprofit_runs($options);
            $mdata['content']=$this->load->view('netprofit/weektotal_data_view',$totals,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function purchase_newdetails() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $res=$this->balances_model->purchase_details_add($netprofitdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $categories=$this->balances_model->get_profit_categories('Purchase');
                        $netprofitdata=usersession($session_id);
                        $options=array(
                            'data'=>$netprofitdata['purchase_details'],
                            'category'=>$categories,
                        );
                        $mdata['content']=$this->load->view('netprofit/purchase_tabledata_view', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function w9work_newdetails() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_data_error;
            $postdata=$this->input->post();
            if (isset($postdata['session'])) {
                $session_id=$postdata['session'];
                // Restore session data
                $netprofitdata=usersession($session_id);
                if (!empty($netprofitdata)) {
                    $res=$this->balances_model->w9work_details_add($netprofitdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $netprofitdata=usersession($session_id);
                        $details=$netprofitdata['w9work_details'];
                        $categories=$this->balances_model->get_profit_categories('W9');
                        $tableoptions=array(
                            'category'=>$categories,
                            'data'=>$details,
                        );
                        // Table view
                        $mdata['content']=$this->load->view('netprofit/w9work_tabledata_view', $tableoptions, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function netprofit_weeknote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $week_id=$this->input->post('week_id');
            $type=$this->input->post('type');
            $brand = $this->input->post('brand');
            $weekar=explode('-',$week_id);
            $week=$weekar[1];
            $year=$weekar[0];
            $notearr=$this->balances_model->get_week_note($week,$year, $brand, $type);
            if ($type=='week') {
                $mdata['content']=$this->load->view('netprofit/netprofit_weeknote_view',$notearr,TRUE);
            } else {
                $mdata['content']=$this->load->view('netprofit/netprofit_monthnote_view',$notearr,TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function save_weeknote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $profit_id=$this->input->post('profit_id');
            $weeknote=$this->input->post('weeknote');
            $brand = $this->input->post('brand');
            $res=$this->balances_model->save_week_note($profit_id, $brand, $weeknote);
            if (!$res['result']) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function netprofit_viewtype() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $type=$this->input->post('type');
            if ($type=='week') {
                $mdata['content']=$this->load->view('netprofit/admin_netprofitweek_view',array(),TRUE);
                $weeklist=$this->balances_model->get_weeklist();
                $weekopt=array(
                    'weekallcheck'=>'checked="checked"',
                    'weekfrom'=>'',
                    'weekuntil'=>'',
                    'weeklist'=>$weeklist,
                );
                $mdata['weekchoice']=$this->load->view('netprofit/week_select_view',$weekopt,TRUE);
            } else {
                $mdata['content']=$this->load->view('netprofit/admin_netprofitmonth_view',array(),TRUE);
                $mdata['weekchoice']=$this->load->view('netprofit/week_selectempty_view',array(),TRUE);;
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function netprofit_debincl() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $type=$this->input->post('type');
            $profit_id=$this->input->post('profit_id');
            $brand = $this->input->post('brand');
            if (!$profit_id) {
                $error='Unknown period';
            } else {
                /* Get data about */
                $res=$this->balances_model->include_netprofit_debt($profit_id, $brand, $type);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['debincl']=$res['debincl'];
                    $data=$res['totals'];
                    $mdata['content']=$this->load->view('netprofit/netprofit_totals_view',$data,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function netprofit_weekincl() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Unknown period';
            $postdata = $this->input->post();
            $type=ifset($postdata, 'type','week');
            $profit_id=ifset($postdata, 'profit_id',0);
            $brand = ifset($postdata, 'brand','ALL');
            //
            if (!empty($profit_id)) {
                $res = $this->balances_model->include_netprofit_week($profit_id, $brand, $type);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['runincl']=$res['runincl'];
                    $data=$res['totals'];
                    $mdata['content']=$this->load->view('netprofit/netprofit_totals_view',$data,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    public function netprofit_w9purchasetable() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Year';
            $postdata=$this->input->post();
            if (isset($postdata['year']) && !empty($postdata['year'])) {
                $brand =ifset($postdata,'brand','ALL');
                $error='';
                $w9sortfld=(isset($postdata['w9worksort']) ? $postdata['w9worksort'] : 'category_name');
                $w9sortdir=(isset($postdata['w9workdir']) ? $postdata['w9workdir'] : 'asc');
                $purchasesortfld=(isset($postdata['purchasesort']) ? $postdata['purchasesort'] : 'category_name');
                $purchasesortdir=(isset($postdata['purchasedir']) ? $postdata['purchasedir'] : 'asc');
                $w9data=$this->balances_model->get_w9yeardetails($postdata['year'], $brand, $w9sortfld, $w9sortdir);
                $purchdata=$this->balances_model->get_purchaseyeardetails($postdata['year'], $brand, $purchasesortfld, $purchasesortdir);
                $totals=$w9data['totals']+$purchdata['totals'];
                $mdata['title']='W9 Work & Purchase Breakdown: '.MoneyOutput($totals,2);
                // Build table
                $w9table_options=array(
                    'w9totals'=>$w9data['totals'],
                    'w9details'=>$w9data['details'],
                    'purchasetotals'=>$purchdata['totals'],
                    'purchasedetails'=>$purchdata['details'],
                    'w9sortfld'=>$w9sortfld,
                    'w9sortdir'=>$w9sortdir,
                    'purchasesortfld'=>$purchasesortfld,
                    'purchasesortdir'=>$purchasesortdir,
                    'sortascicon'=>'<i class="fa fa-caret-square-o-up" aria-hidden="true"></i>',
                    'sortdescicon'=>'<i class="fa fa-caret-square-o-down" aria-hidden="true"></i>',
                    'year'=>$postdata['year'],
                    'brand' => $brand,
                );
                $mdata['content']=$this->load->view('netprofit/w9purchase_table_view', $w9table_options, TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function netprofit_comparetabledata() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $pacedata=$this->balances_model->get_onpacecompare($postdata);
            $mdata['content']=$this->load->view('netprofit/onpace_grown_view', $pacedata,TRUE);
            $error='';
        }
        $this->ajaxResponse($mdata,$error);
    }

    public function netprofit_chartdata() {
        if ($this->isAjax()) {
            // $mdata=array();
            $postdata=$this->input->post();
            $mdata=$this->balances_model->get_netprofit_chartbyweekdata($postdata);
            $error='';
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function netprofit_weeknoteview() {
        $profit_id=$this->input->get('d');
        $brand = $this->input->get('brand');
        $netprofit=$this->balances_model->get_netprofit_details($profit_id);
        if (!isset($netprofit['profit_id'])) {
            $out_msg='Orders Data Not Found';
        } else {
            // Get a W9 work and purchase
            $expensdat=$this->balances_model->get_netprofit_purchasedetails($profit_id, $brand);
            if ($expensdat['result']==$this->error_result) {
                $out_msg=$this->load->view('netprofit/weeknote_view', $netprofit, TRUE);
            } else {
                $w9work=$purchase='&nbsp;';
                if (count($expensdat['w9work'])) {
                    $totals=0;
                    foreach ($expensdat['w9work'] as $wrow) {
                        $totals+=$wrow['amount'];
                    }
                    $options=array(
                        'data'=>$expensdat['w9work'],
                        'total'=>$totals,
                        'label'=>'W9 Work',
                    );
                    $w9work=$this->load->view('netprofit/expense_details_view', $options, TRUE);
                }
                if (count($expensdat['purchase'])>0) {
                    $totals=0;
                    foreach ($expensdat['purchase'] as $wrow) {
                        $totals+=$wrow['amount'];
                    }
                    $options=array(
                        'data'=>$expensdat['purchase'],
                        'total'=>$totals,
                        'label'=>'Purchase',
                    );
                    $purchase=$this->load->view('netprofit/expense_details_view', $options, TRUE);
                }
                $totaloptions=array(
                    'w9work'=>$w9work,
                    'purchase'=>$purchase,
                );
                $out_msg=$this->load->view('netprofit/expense_week_view', $totaloptions, TRUE);
            }
        }
        /* Get Orders List */
        echo $out_msg;
    }

    public function netprofit_showdetais() {
        $getdata=$this->input->get();
        $year=ifset($getdata, 'year', date('Y'));
        $category=ifset($getdata, 'cat', '');
        $brand = ifset($getdata, 'brand', 'ALL');
        // Get category Data
        $res=$this->balances_model->get_expenses_details($category, $year, $brand);

        if ($res['result']==$this->error_result) {
            echo $res['msg'];
        } else {
            $options=array(
                'category'=>$res['category'],
                'type'=>($res['type']=='W9' ? 'W9 Work' : $res['type']),
                'data'=>$res['data'],
                'totals'=>$res['totals'],
            );
            $content=$this->load->view('netprofit/w9purchase_expenses_view', $options, TRUE);
            echo $content;
        }
    }

    public function exclude_quickbook() {
        if ($this->isAjax()) {
            $mdata = [];
            $error='';
            $postdata = $this->input->post();
            $newval = 1;
            if (ifset($postdata,'exclude_quickbook',0)==1) {
                $newval = 0;
            }
            if ($newval==1) {
                $mdata['content'] = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            } else {
                $mdata['content'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
            }
            $mdata['newval'] = $newval;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function accountreceiv_totals() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $period = ifset($postdata,'period', -1);
            $brand = ifset($postdata,'brand', 'ALL');
            $ownsort1 = ifset($postdata, 'ownsort1', 'batch_due');
            $ownsort2 = ifset($postdata, 'ownsort2', 'ownapprove');
            if ($brand=='SG') {
                $brand = 'ALL';
            }
            $res = $this->orders_model->accountreceiv_totals($period, $brand);
            $res['brand'] = $brand;
            $res['ownsort1'] = $ownsort1;
            $res['ownsort2'] = $ownsort2;
            $mdata['content'] = $this->load->view('accreceiv/totals_view', $res, TRUE);
            $mdata['totals'] = $this->load->view('accreceiv/balances_view', $res, TRUE);
            $error = '';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function accountreceiv_details() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $period = ifset($postdata,'period', -1);
            $brand = ifset($postdata,'brand', 'ALL');
            if ($brand=='SG') {
                $brand = 'ALL';
            }
            $ownsort1 = ifset($postdata,'ownsort1', 'batch_due');
            $ownsort1 = ($ownsort1=='owntype' ? 'type' : ($ownsort1=='ownapprove' ? 'approved' : $ownsort1));
            // $owndirec = ifset($postdata,'owndirec', 'desc');
            $ownsort2 = ifset($postdata,'ownsort2', 'batch_due');
            $ownsort2 = ($ownsort2=='owntype' ? 'type' : ($ownsort2=='ownapprove' ? 'approved' : $ownsort2));
            // $refundsort = ifset($postdata,'refundsort','order_date');
            $refundsort = 'order_date';
            // $refunddirec = ifset($postdata, 'refunddirec', 'desc');
            $refunddirec = 'desc';

            $res = $this->orders_model->accountreceiv_details($period, $brand, $ownsort1, $ownsort2, $refundsort, $refunddirec);
            if ($brand=='ALL') {
                $mdata['content'] = $this->load->view('accreceiv/details_sigma_view', $res, TRUE);
            } elseif ($brand=='SR') {
                $mdata['content'] = $this->load->view('accreceiv/details_sr_view', $res, TRUE);
            } else {
                $mdata['content'] = $this->load->view('accreceiv/details_view', $res, TRUE);
            }

            $error = '';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function accown_showstatus()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order #';
            $postdata = $this->input->post();
            $order_id = ifset($postdata, 'order', 0);
            if (!empty($order_id)) {
                $res = $this->orders_model->get_order_detail($order_id);
                if (ifset($res, 'order_id',0)==$order_id) {
                    $error = '';
                    $mdata['content'] = $this->load->view('accreceiv/status_edit_view', $res, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    public function debtstatus()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Parameters';
            $postdata = $this->input->post();
            $order_id = ifset($postdata, 'order_id',0);
            $debt_status = ifset($postdata, 'debt_status','');
            if (!empty($order_id)) {
                $res = $this->orders_model->update_debtstatus($order_id, $debt_status);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // Build content
                    $data = $res['data'];
                    if (empty($data['debt_status'])) {
                        $mdata['content'] = $this->load->view('accreceiv/empty_debtstatus_view', $data, TRUE);
                    } else {
                        $mdata['content'] = $this->load->view('accreceiv/debet_status_view', $data, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function accowed_export()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $period = ifset($postdata,'period', -1);
            $brand = ifset($postdata,'brand', 'ALL');
            if ($brand=='SG') {
                $brand = 'ALL';
            }
            $exporttype = ifset($postdata, 'exporttype', 'O');
            $ownsort1 = ifset($postdata,'ownsort1', 'batch_due');
            $ownsort1 = ($ownsort1=='owntype' ? 'type' : ($ownsort1=='ownapprove' ? 'approved' : $ownsort1));
            $ownsort2 = ifset($postdata,'ownsort2', 'batch_due');
            $ownsort2 = ($ownsort2=='owntype' ? 'type' : ($ownsort2=='ownapprove' ? 'approved' : $ownsort2));
            $refundsort = ifset($postdata,'refundsort','order_date');
            $refunddirec = ifset($postdata, 'refunddirec', 'desc');
            $res = $this->orders_model->accountreceiv_details($period, $brand, $ownsort1, $ownsort2, $refundsort, $refunddirec);
            $this->load->model('exportexcell_model');
            if ($exporttype=='O') {
                $mdata['url'] = $this->exportexcell_model->export_owed($res['owns'], $brand);
            } else {
                $mdata['url'] = $this->exportexcell_model->export_refund($res['refunds']);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_profit_dateslider($brand, $showgrowth=1) {
        $yearview='';
        $numyears=0;
        $dats=$this->orders_model->get_profit_limitdates($brand);
        if (isset($dats['max_year'])) {
            if (date('Y-m',time())!=$dats['max_year'].'-'.$dats['max_month']) {
                /* We start new month but there are no orders in this month */
                $dats['cur_month']=$dats['max_month'];
                $dats['cur_year']=$dats['max_year'];
            } else {
                $dats['cur_month']=date('m');
                $dats['cur_year']=date('Y');
            }
        } else {
            $dats['max_month']=date('m');
            $dats['max_year']=date('Y');
            $dats['max_date']=time();
            $dats['cur_month']=date('m');
            $dats['cur_year']=date('Y');
        }
        $prvdata = [];
        for($i=$dats['min_year']; $i<=$dats['cur_year'] ; $i++) {
            // Get Data about year
            if ($numyears==0) {
                $ydata=$this->orders_model->calendar_totals($i, $brand);
            } else {
                $ydata=$this->orders_model->calendar_totals($i, $brand, $prvdata,1);
            }
            if ($i!=$dats['cur_year']) {
                $prvdata=$ydata;
            } else {
                $tt = 1;
            }
            if ($numyears==0 || $i==$dats['cur_year']) {
                $voptions=array(
                    'year'=>$i,
                    'title'=>($i==$dats['cur_year'] ? $i.' - Year to Date' : $i),
                    'total_orders'=>$ydata['total_orders'],
                    'revenue'=>$ydata['revenue'],
                    'avg_revenue'=>$ydata['avg_revenue'],
                    'profit'=>$ydata['profit'],
                    'avg_profit'=>$ydata['avg_profit'],
                    'profit_class'=>$ydata['profit_class'],
                    'avg_profit_perc'=>$ydata['avg_profit_perc'],
                    'devider'=>($i==$dats['cur_year'] ? 0 : 1),
                    'growthview'=>0,
                );
                $yearview.=$this->load->view('accounting/year_data_view', $voptions, TRUE);
            } else {
                $voptions=array(
                    'year'=>$i,
                    'title'=>($i==$dats['cur_year'] ? $i.' - Year to Date' : $i),
                    'total_orders'=>$ydata['total_orders'],
                    'revenue'=>$ydata['revenue'],
                    'avg_revenue'=>$ydata['avg_revenue'],
                    'profit'=>$ydata['profit'],
                    'avg_profit'=>$ydata['avg_profit'],
                    'profit_class'=>$ydata['profit_class'],
                    'avg_profit_perc'=>$ydata['avg_profit_perc'],
                    'devider'=>1,
                    'growthview'=>0,
                );
                if ($showgrowth==1) {
                    $voptions['growthview']=1;
                    // Growth title
                    $prvyear=$i-1;
                    $grtitle="'".substr($prvyear,2,2)."'-'".substr($i,2,2)." Growth";
                    $voptions['growthtitle']=$grtitle;
                    $growth=$ydata['growth'];
                    $voptions['growth_orderclass']=$voptions['growth_revenueclass']=$voptions['growth_avgprofitclass']='';
                    if ($growth['order_num']==0) {
                        $voptions['growth_ordernum']='&mdash;';
                        $voptions['growth_orderprc']='&mdash;';
                    } else {
                        if ($growth['order_num']>0) {
                            $voptions['growth_ordernum']=QTYOutput($growth['order_num']);
                            $voptions['growth_orderprc']=$growth['order_proc'].'%';
                        } else {
                            $voptions['growth_ordernum']='('.abs(QTYOutput($growth['order_num'])).')';
                            $voptions['growth_orderprc']='('.abs($growth['order_proc']).'%)';
                            $voptions['growth_orderclass']='color_red';
                        }
                    }
                    if ($growth['revenue_num']==0) {
                        $voptions['growth_revenuenum']='&mdash;';
                        $voptions['growth_revenueprc']='&mdash;';
                    } else {
                        if ($growth['revenue_num']>0) {
                            $voptions['growth_revenuenum']=MoneyOutput($growth['revenue_num'],0);
                            $voptions['growth_revenueprc']=$growth['revenue_perc'].'%';
                        } else {
                            $voptions['growth_revenuenum']='('.MoneyOutput(abs($growth['revenue_num']),0).')';
                            $voptions['growth_revenueprc']='('.abs($growth['revenue_perc']).'%)';
                            $voptions['growth_revenueclass']='color_red';
                        }
                    }
                    $voptions['growth_avgrevenueclass']=$voptions['growth_profitclass']='';
                    if ($growth['avgrevenue_num']==0) {
                        $voptions['growth_avgrevenuenum']='&mdash;';
                        $voptions['growth_avgrevenueprc']='&mdash;';
                    } else {
                        if ($growth['avgrevenue_num']>0) {
                            $voptions['growth_avgrevenuenum']=MoneyOutput($growth['avgrevenue_num'],0);
                            $voptions['growth_avgrevenueprc']=$growth['avgrevenue_perc'].'%';
                        } else {
                            $voptions['growth_avgrevenuenum']='('.MoneyOutput(abs($growth['avgrevenue_num']),0).')';
                            $voptions['growth_avgrevenueprc']='('.abs($growth['avgrevenue_perc']).'%)';
                            $voptions['growth_avgrevenueclass']='color_red';
                        }
                    }
                    if ($growth['avgrevenue_num']==0) {
                        $voptions['growth_avgrevenuenum']='&mdash;';
                        $voptions['growth_avgrevenueprc']='&mdash;';
                    } else {
                        if ($growth['avgrevenue_num']>0) {
                            $voptions['growth_avgrevenuenum']=MoneyOutput($growth['avgrevenue_num'],0);
                            $voptions['growth_avgrevenueprc']=$growth['avgrevenue_perc'].'%';
                        } else {
                            $voptions['growth_avgrevenuenum']='('.MoneyOutput(abs($growth['avgrevenue_num']),0).')';
                            $voptions['growth_avgrevenueprc']='('.abs($growth['avgrevenue_perc']).'%)';
                            $voptions['growth_avgrevenueclass']='color_red';
                        }
                    }
                    if ($growth['profit_num']==0) {
                        $voptions['growth_profitnum']='&mdash;';
                        $voptions['growth_profitprc']='&mdash;';
                    } else {
                        if ($growth['profit_num']>0) {
                            $voptions['growth_profitnum']=MoneyOutput($growth['profit_num'],0);
                            $voptions['growth_profitprc']=$growth['profit_perc'].'%';
                        } else {
                            $voptions['growth_profitnum']='('.MoneyOutput(abs($growth['profit_num']),0).')';
                            $voptions['growth_profitprc']='('.abs($growth['profit_perc']).'%)';
                            $voptions['growth_profitclass']='color_red';
                        }
                    }

                    if ($growth['avgprofit_num']==0) {
                        $voptions['growth_avgprofitnum']='&mdash;';
                        $voptions['growth_avgprofitprc']='&mdash;';
                    } else {
                        if ($growth['avgprofit_num']>0) {
                            $voptions['growth_avgprofitnum']=MoneyOutput($growth['avgprofit_num'],0);
                            $voptions['growth_avgprofitprc']=$growth['avgprofit_perc'].'%';
                        } else {
                            $voptions['growth_avgprofitnum']='('.MoneyOutput(abs($growth['avgprofit_num']),0).')';
                            $voptions['growth_avgprofitprc']='('.abs($growth['avgprofit_perc']).'%)';
                            $voptions['growth_avgprofitclass']='color_red';
                        }
                    }
                    $voptions['growth_aveprcclass']='';
                    if ($growth['ave_num']==0) {
                        $voptions['growth_avenum']='&mdash;';
                        $voptions['growth_aveprc']='&mdash;';
                    } else {
                        if ($growth['ave_num']>0) {
                            $voptions['growth_avenum']=$growth['ave_num'].'%';
                            $voptions['growth_aveprc']=$growth['ave_proc'].'%';
                        } else {
                            $voptions['growth_avenum']='('.abs($growth['ave_num']).'%)';
                            $voptions['growth_aveprc']='('.abs($growth['ave_proc']).'%)';
                            $voptions['growth_aveprcclass']='color_red';
                        }
                    }
                }
                $yearview.=$this->load->view('accounting/year_data_view', $voptions, TRUE);
            }
            $numyears++;
        }
        if ($numyears>1) {
            // Calc Pace to Hit
            $pacetotal=$this->orders_model->orders_pacetohit($dats['cur_year'], $brand, $prvdata, 1);
        } else {
            // Calc Pace to Hit
            $pacetotal=$this->orders_model->orders_pacetohit($dats['cur_year'], $brand);
        }

        $voptions=array(
            'year'=>$dats['cur_year'],
            'title'=>$dats['cur_year'].' On Pace to Hit',
            'total_orders'=>$pacetotal['total_orders'],
            'revenue'=>$pacetotal['revenue'],
            'avg_revenue'=>$pacetotal['avg_revenue'],
            'profit'=>$pacetotal['profit'],
            'avg_profit'=>$pacetotal['avg_profit'],
            'profit_class'=>$pacetotal['profit_class'],
            'avg_profit_perc'=>$pacetotal['avg_profit_perc'],
            'devider'=>0,
            'growthview'=>0,
        );
        // On Pace Growth
        $growth=$pacetotal['growth'];
        if (!empty($growth) && $showgrowth==1) {
            $voptions['growthview']=1;
            $prvyear=$dats['cur_year']-1;
            $grtitle="'".substr($prvyear,2,2)."'-'".substr($dats['cur_year'],2,2)." Growth";
            $voptions['growthtitle']=$grtitle;
            $voptions['growth_orderclass']=$voptions['growth_revenueclass']=$voptions['growth_avgprofitclass']='';
            if ($growth['order_num']==0) {
                $voptions['growth_ordernum']='&mdash;';
                $voptions['growth_orderprc']='&mdash;';
            } else {
                if ($growth['order_num']>0) {
                    $voptions['growth_ordernum']=QTYOutput($growth['order_num']);
                    $voptions['growth_orderprc']=$growth['order_proc'].'%';
                } else {
                    $voptions['growth_ordernum']='('.QTYOutput(abs($growth['order_num'])).')';
                    $voptions['growth_orderprc']='('.abs($growth['order_proc']).'%)';
                    $voptions['growth_orderclass']='color_red';
                }
            }
            if ($growth['revenue_num']==0) {
                $voptions['growth_revenuenum']='&mdash;';
                $voptions['growth_revenueprc']='&mdash;';
            } else {
                if ($growth['revenue_num']>0) {
                    $voptions['growth_revenuenum']=MoneyOutput($growth['revenue_num'],0);
                    $voptions['growth_revenueprc']=$growth['revenue_perc'].'%';
                } else {
                    $voptions['growth_revenuenum']='('.MoneyOutput(abs($growth['revenue_num']),0).')';
                    $voptions['growth_revenueprc']='('.abs($growth['revenue_perc']).'%)';
                    $voptions['growth_revenueclass']='color_red';
                }
            }
            $voptions['growth_avgrevenueclass']=$voptions['growth_profitclass']='';
            if ($growth['avgrevenue_num']==0) {
                $voptions['growth_avgrevenuenum']='&mdash;';
                $voptions['growth_avgrevenueprc']='&mdash;';
            } else {
                if ($growth['avgrevenue_num']>0) {
                    $voptions['growth_avgrevenuenum']=MoneyOutput($growth['avgrevenue_num'],0);
                    $voptions['growth_avgrevenueprc']=$growth['avgrevenue_perc'].'%';
                } else {
                    $voptions['growth_avgrevenuenum']='('.MoneyOutput(abs($growth['avgrevenue_num']),0).')';
                    $voptions['growth_avgrevenueprc']='('.abs($growth['avgrevenue_perc']).'%)';
                    $voptions['growth_avgrevenueclass']='color_red';
                }
            }
            if ($growth['avgrevenue_num']==0) {
                $voptions['growth_avgrevenuenum']='&mdash;';
                $voptions['growth_avgrevenueprc']='&mdash;';
            } else {
                if ($growth['avgrevenue_num']>0) {
                    $voptions['growth_avgrevenuenum']=MoneyOutput($growth['avgrevenue_num'],0);
                    $voptions['growth_avgrevenueprc']=$growth['avgrevenue_perc'].'%';
                } else {
                    $voptions['growth_avgrevenuenum']='('.MoneyOutput(abs($growth['avgrevenue_num']),0).')';
                    $voptions['growth_avgrevenueprc']='('.abs($growth['avgrevenue_perc']).'%)';
                    $voptions['growth_avgrevenueclass']='color_red';
                }
            }
            if ($growth['profit_num']==0) {
                $voptions['growth_profitnum']='&mdash;';
                $voptions['growth_profitprc']='&mdash;';
            } else {
                if ($growth['profit_num']>0) {
                    $voptions['growth_profitnum']=MoneyOutput($growth['profit_num'],0);
                    $voptions['growth_profitprc']=$growth['profit_perc'].'%';
                } else {
                    $voptions['growth_profitnum']='('.MoneyOutput(abs($growth['profit_num']),0).')';
                    $voptions['growth_profitprc']='('.abs($growth['profit_perc']).'%)';
                    $voptions['growth_profitclass']='color_red';
                }
            }

            if ($growth['avgprofit_num']==0) {
                $voptions['growth_avgprofitnum']='&mdash;';
                $voptions['growth_avgprofitprc']='&mdash;';
            } else {
                if ($growth['avgprofit_num']>0) {
                    $voptions['growth_avgprofitnum']=MoneyOutput($growth['avgprofit_num'],0);
                    $voptions['growth_avgprofitprc']=$growth['avgprofit_perc'].'%';
                } else {
                    $voptions['growth_avgprofitnum']='('.MoneyOutput(abs($growth['avgprofit_num']),0).')';
                    $voptions['growth_avgprofitprc']='('.abs($growth['avgprofit_perc']).'%)';
                    $voptions['growth_avgprofitclass']='color_red';
                }
            }
            $voptions['growth_aveprcclass']='';
            if ($growth['ave_num']==0) {
                $voptions['growth_avenum']='&mdash;';
                $voptions['growth_aveprc']='&mdash;';
            } else {
                if ($growth['ave_num']>0) {
                    $voptions['growth_avenum']=$growth['ave_num'].'%';
                    $voptions['growth_aveprc']=$growth['ave_proc'].'%';
                } else {
                    $voptions['growth_avenum']='('.abs($growth['ave_num']).'%)';
                    $voptions['growth_aveprc']='('.abs($growth['ave_proc']).'%)';
                    $voptions['growth_aveprcclass']='color_red';
                }
            }
        }
        $yearview.=$this->load->view('accounting/year_data_view', $voptions, TRUE);
        $numyears++;
        // Goals
        $goptions=array(
            'year'=>$dats['cur_year'],
            'title'=>$dats['cur_year'].' - Goals',
            'total_orders'=>$pacetotal['goal_orders'],
            'revenue'=>$pacetotal['goal_revenue'],
            'avg_revenue'=>$pacetotal['goal_avgrevenue'],
            'profit'=>$pacetotal['goal_profit'],
            'avg_profit'=>$pacetotal['goal_avgprofit'],
            'profit_class'=>$pacetotal['goal_profit_class'],
            'avg_profit_perc'=>$pacetotal['goal_avgprofit_perc'],
            'growthview'=>0,
            'brand' => $brand,
        );
        $growth=$pacetotal['growth_goals'];
        if (!empty($growth) && $showgrowth==1) {
            $goptions['growthview']=1;
            $prvyear=$dats['cur_year']-1;
            $grtitle="'".substr($prvyear,2,2)."'-'".substr($dats['cur_year'],2,2)." Growth";
            $goptions['growthtitle']=$grtitle;
            $goptions['growth_orderclass']=$goptions['growth_revenueclass']=$goptions['growth_avgprofitclass']='';
            if ($growth['order_num']==0) {
                $goptions['growth_ordernum']='&mdash;';
                $goptions['growth_orderprc']='&mdash;';
            } else {
                if ($growth['order_num']>0) {
                    $goptions['growth_ordernum']=QTYOutput($growth['order_num']);
                    $goptions['growth_orderprc']=$growth['order_proc'].'%';
                } else {
                    $goptions['growth_ordernum']='('.QTYOutput(abs($growth['order_num'])).')';
                    $goptions['growth_orderprc']='('.abs($growth['order_proc']).'%)';
                    $goptions['growth_orderclass']='color_red';
                }
            }
            if ($growth['revenue_num']==0) {
                $goptions['growth_revenuenum']='&mdash;';
                $goptions['growth_revenueprc']='&mdash;';
            } else {
                if ($growth['revenue_num']>0) {
                    $goptions['growth_revenuenum']=MoneyOutput($growth['revenue_num'],0);
                    $goptions['growth_revenueprc']=$growth['revenue_perc'].'%';
                } else {
                    $goptions['growth_revenuenum']='('.MoneyOutput(abs($growth['revenue_num']),0).')';
                    $goptions['growth_revenueprc']='('.abs($growth['revenue_perc']).'%)';
                    $goptions['growth_revenueclass']='color_red';
                }
            }
            $goptions['growth_avgrevenueclass']=$goptions['growth_profitclass']='';
            if ($growth['avgrevenue_num']==0) {
                $goptions['growth_avgrevenuenum']='&mdash;';
                $goptions['growth_avgrevenueprc']='&mdash;';
            } else {
                if ($growth['avgrevenue_num']>0) {
                    $goptions['growth_avgrevenuenum']=MoneyOutput($growth['avgrevenue_num'],0);
                    $goptions['growth_avgrevenueprc']=$growth['avgrevenue_perc'].'%';
                } else {
                    $goptions['growth_avgrevenuenum']='('.MoneyOutput(abs($growth['avgrevenue_num']),0).')';
                    $goptions['growth_avgrevenueprc']='('.abs($growth['avgrevenue_perc']).'%)';
                    $goptions['growth_avgrevenueclass']='color_red';
                }
            }
            if ($growth['profit_num']==0) {
                $goptions['growth_profitnum']='&mdash;';
                $goptions['growth_profitprc']='&mdash;';
            } else {
                if ($growth['profit_num']>0) {
                    $goptions['growth_profitnum']=MoneyOutput($growth['profit_num'],0);
                    $goptions['growth_profitprc']=$growth['profit_perc'].'%';
                } else {
                    $goptions['growth_profitnum']='('.MoneyOutput(abs($growth['profit_num']),0).')';
                    $goptions['growth_profitprc']='('.abs($growth['profit_perc']).'%)';
                    $goptions['growth_profitclass']='color_red';
                }
            }

            if ($growth['avgprofit_num']==0) {
                $goptions['growth_avgprofitnum']='&mdash;';
                $goptions['growth_avgprofitprc']='&mdash;';
            } else {
                if ($growth['avgprofit_num']>0) {
                    $goptions['growth_avgprofitnum']=MoneyOutput($growth['avgprofit_num'],0);
                    $goptions['growth_avgprofitprc']=$growth['avgprofit_perc'].'%';
                } else {
                    $goptions['growth_avgprofitnum']='('.MoneyOutput(abs($growth['avgprofit_num']),0).')';
                    $goptions['growth_avgprofitprc']='('.abs($growth['avgprofit_perc']).'%)';
                    $goptions['growth_avgprofitclass']='color_red';
                }
            }
            $goptions['growth_aveprcclass']='';
            if ($growth['ave_num']==0) {
                $goptions['growth_avenum']='&mdash;';
                $goptions['growth_aveprc']='&mdash;';
            } else {
                if ($growth['ave_num']>0) {
                    $goptions['growth_avenum']=$growth['ave_num'].'%';
                    $goptions['growth_aveprc']=$growth['ave_proc'].'%';
                } else {
                    $goptions['growth_avenum']='('.abs($growth['ave_num']).'%)';
                    $goptions['growth_aveprc']='('.abs($growth['ave_proc']).'%)';
                    $goptions['growth_aveprcclass']='color_red';
                }
            }
        }
        $yearview.=$this->load->view('accounting/goal_data_view', $goptions, TRUE);
        $numyears++;
        // Reminder
        $roptions=array(
            'days'=>$pacetotal['reminder_days'],
            'orders'=>$pacetotal['reminder_orders'],
            'profit'=>$pacetotal['reminder_profit'],
            'revenue'=>$pacetotal['reminder_revenue'],
            'totaldays'=>$pacetotal['bankdays'],
            'reminder_prc'=>$pacetotal['reminder_prc'],
        );
        $yearview.=$this->load->view('accounting/reminder_data_view', $roptions, TRUE);
        // Calc total length
        $slideryears = $dats['cur_year']-$dats['min_year'];
        if ($showgrowth==1) {
            $slider_width=($dats['cur_year']-$dats['min_year'])*246+125+547; // 547;
            // $slider_width=($dats['cur_year']-$dats['min_year'])*246+572; // 547;
            if ($slideryears < 2) {
                $margin=(1018-$slider_width);
            } else {
                $margin=(1100-$slider_width); // 918
            }
        } else {
            $slider_width=($dats['cur_year']-$dats['min_year']+1)*132+364+24; // +363;
            if ($slideryears < 2) {
                $margin=(964-$slider_width);
            } else {
                $margin=(927-$slider_width);
            }

        }
        // $slider_width=$numyears*172+83;
        //
        $out=array(
            'content'=>$yearview,
            'slider_width'=>$slider_width,
            'margin'=>$margin,
        );
        return $out;
    }

    private function _prepare_filter($startmonth, $endmonth, $brand, $showgrowth=1)
    {
        $yearview = '';
        $numyears = 0;
        $dats=$this->orders_model->get_profit_limitdates($brand);
        if (isset($dats['max_year'])) {
            if (date('Y-m',time())!=$dats['max_year'].'-'.$dats['max_month']) {

                $dats['cur_month']=$dats['max_month'];
                $dats['cur_year']=$dats['max_year'];
            } else {
                $dats['cur_month']=date('m');
                $dats['cur_year']=date('Y');
            }
        } else {
            $dats['max_month']=date('m');
            $dats['max_year']=date('Y');
            $dats['max_date']=time();
            $dats['cur_month']=date('m');
            $dats['cur_year']=date('Y');
        }
        $prvdata = [];
        for ($i = $dats['min_year']; $i <= $dats['cur_year']; $i++) {
            if ($numyears == 0) {
                $ydate = $this->orders_model->get_filter_data($i, $startmonth, $endmonth, $brand);
            } else {
                $ydate = $this->orders_model->get_filter_data($i, $startmonth, $endmonth, $brand , $prvdata, 1);
            }
            if ($i != $dats['cur_year']) {
                $prvdata = $ydate;
            }
            if ($numyears == 0 || $i == $dats['cur_year']) {
                $voptions = array(
                    'year' => $i,
                    'title' => $i,
                    'total_orders' => $ydate['total_orders'],
                    'revenue' => $ydate['revenue'],
                    'avg_revenue' => $ydate['avg_revenue'],
                    'profit' => $ydate['profit'],
                    'avg_profit' => $ydate['avg_profit'],
                    'profit_class' => $ydate['profit_class'],
                    'avg_profit_perc' => $ydate['avg_profit_perc'],
                    'devider' => ($i == $dats['cur_year'] ? 0 : 1),
                    'growthview' => 0,
                );
                if ($i==$dats['cur_year'] && $showgrowth==1) {
                    $voptions['growthview'] = 1;
                    $prvyear = $i - 1;
                    $grtitle = "'" . substr($prvyear, 2, 2) . "'-'" . substr($i, 2, 2) . " Growth";
                    $voptions['growthtitle'] = $grtitle;
                    $growth = $ydate['growth'];
                    $voptions['growth_orderclass'] = $voptions['growth_revenueclass'] = $voptions['growth_avgprofitclass'] = '';
                    if ($growth['order_num'] == 0) {
                        $voptions['growth_ordernum'] = '&mdash;';
                        $voptions['growth_orderprc'] = '&mdash;';
                    } else {
                        if ($growth['order_num'] > 0) {
                            $voptions['growth_ordernum'] = QTYOutput($growth['order_num']);
                            $voptions['growth_orderprc'] = $growth['order_proc'] . '%';
                        } else {
                            $voptions['growth_ordernum'] = '('.QTYOutput(abs($growth['order_num'])) . ')';
                            $voptions['growth_orderprc'] = '('.abs($growth['order_proc']).'%)';
                            $voptions['growth_orderclass'] = 'color_red';
                        }
                    }
                    if ($growth['revenue_num'] == 0) {
                        $voptions['growth_revenuenum'] = '&mdash;';
                        $voptions['growth_revenueprc'] = '&mdash;';
                    } else {
                        if ($growth['revenue_num'] > 0) {
                            $voptions['growth_revenuenum'] = MoneyOutput($growth['revenue_num'], 0);
                            $voptions['growth_revenueprc'] = $growth['revenue_perc'] . '%';
                        } else {
                            $voptions['growth_revenuenum'] = '(' . MoneyOutput(abs($growth['revenue_num']), 0) . ')';
                            $voptions['growth_revenueprc'] = '(' . abs($growth['revenue_perc']). '%)';
                            $voptions['growth_revenueclass'] = 'color_red';
                        }
                    }
                    $voptions['growth_avgrevenueclass'] = $voptions['growth_profitclass'] = '';
                    if ($growth['avgrevenue_num'] == 0) {
                        $voptions['growth_avgrevenuenum'] = '&mdash;';
                        $voptions['growth_avgrevenueprc'] = '&mdash;';
                    } else {
                        if ($growth['avgrevenue_num'] > 0) {
                            $voptions['growth_avgrevenuenum'] = MoneyOutput($growth['avgrevenue_num'], 0);
                            $voptions['growth_avgrevenueprc'] = $growth['avgrevenue_perc'] . '%';
                        } else {
                            $voptions['growth_avgrevenuenum'] = '(' . MoneyOutput(abs($growth['avgrevenue_num']), 0) . ')';
                            $voptions['growth_avgrevenueprc'] = '(' . abs($growth['avgrevenue_perc']) . '%)';
                            $voptions['growth_avgrevenueclass'] = 'color_red';
                        }
                    }
                    if ($growth['profit_num'] == 0) {
                        $voptions['growth_profitnum'] = '&mdash;';
                        $voptions['growth_profitprc'] = '&mdash;';
                    } else {
                        if ($growth['profit_num'] > 0) {
                            $voptions['growth_profitnum'] = MoneyOutput($growth['profit_num'], 0);
                            $voptions['growth_profitprc'] = $growth['profit_perc'] . '%';
                        } else {
                            $voptions['growth_profitnum'] = '(' . MoneyOutput(abs($growth['profit_num']), 0) . ')';
                            $voptions['growth_profitprc'] = '(' . abs($growth['profit_perc']) . '%)';
                            $voptions['growth_profitclass'] = 'color_red';
                        }
                    }
                    if ($growth['avgprofit_num'] == 0) {
                        $voptions['growth_avgprofitnum'] = '&mdash;';
                        $voptions['growth_avgprofitprc'] = '&mdash;';
                    } else {
                        if ($growth['avgprofit_num'] > 0) {
                            $voptions['growth_avgprofitnum'] = MoneyOutput($growth['avgprofit_num'], 0);
                            $voptions['growth_avgprofitprc'] = $growth['avgprofit_perc'] . '%';
                        } else {
                            $voptions['growth_avgprofitnum'] = '(' . MoneyOutput(abs($growth['avgprofit_num']), 0) . ')';
                            $voptions['growth_avgprofitprc'] = '(' . abs($growth['avgprofit_perc']) . '%)';
                            $voptions['growth_avgprofitclass'] = 'color_red';
                        }
                    }
                    $voptions['growth_aveprcclass'] = '';
                    if ($growth['ave_num'] == 0) {
                        $voptions['growth_avenum'] = '&mdash;';
                        $voptions['growth_aveprc'] = '&mdash;';
                    } else {
                        if ($growth['ave_num'] > 0) {
                            $voptions['growth_avenum'] = $growth['ave_num'] . '%';
                            $voptions['growth_aveprc'] = $growth['ave_proc'] . '%';
                        } else {
                            $voptions['growth_avenum'] = '(' . abs($growth['ave_num']) . '%)';
                            $voptions['growth_aveprc'] = '(' . abs($growth['ave_proc']) . '%)';
                            $voptions['growth_aveprcclass'] = 'color_red';
                        }
                    }
                }
                $yearview .= $this->load->view('accounting/year_data_view', $voptions, TRUE);
            } else {
                $voptions = array(
                    'year' => $i,
                    'title' => ($i == $dats['cur_year'] ? $i . ' - Year to Date' : $i),
                    'total_orders' => $ydate['total_orders'],
                    'revenue' => $ydate['revenue'],
                    'avg_revenue' => $ydate['avg_revenue'],
                    'profit' => $ydate['profit'],
                    'avg_profit' => $ydate['avg_profit'],
                    'profit_class' => $ydate['profit_class'],
                    'avg_profit_perc' => $ydate['avg_profit_perc'],
                    'devider' => 1,
                    'growthview' => 0,
                );
                if ($showgrowth == 1) {
                    $voptions['growthview'] = 1;
                    $prvyear = $i - 1;
                    $grtitle = "'" . substr($prvyear, 2, 2) . "'-'" . substr($i, 2, 2) . " Growth";
                    $voptions['growthtitle'] = $grtitle;
                    $growth = $ydate['growth'];
                    $voptions['growth_orderclass'] = $voptions['growth_revenueclass'] = $voptions['growth_avgprofitclass'] = '';
                    if ($growth['order_num'] == 0) {
                        $voptions['growth_ordernum'] = '&mdash;';
                        $voptions['growth_orderprc'] = '&mdash;';
                    } else {
                        if ($growth['order_num'] > 0) {
                            $voptions['growth_ordernum'] = QTYOutput($growth['order_num']);
                            $voptions['growth_orderprc'] = $growth['order_proc'] . '%';
                        } else {
                            $voptions['growth_ordernum'] = '(' . QTYOutput(abs($growth['order_num'])) . ')';
                            $voptions['growth_orderprc'] = '(' . abs($growth['order_proc']) . '%)';
                            $voptions['growth_orderclass'] = 'color_red';
                        }
                    }
                    if ($growth['revenue_num'] == 0) {
                        $voptions['growth_revenuenum'] = '&mdash;';
                        $voptions['growth_revenueprc'] = '&mdash;';
                    } else {
                        if ($growth['revenue_num'] > 0) {
                            $voptions['growth_revenuenum'] = MoneyOutput($growth['revenue_num'], 0);
                            $voptions['growth_revenueprc'] = $growth['revenue_perc'] . '%';
                        } else {
                            $voptions['growth_revenuenum'] = '(' . MoneyOutput(abs($growth['revenue_num']), 0) . ')';
                            $voptions['growth_revenueprc'] = '(' . abs($growth['revenue_perc']) . '%)';
                            $voptions['growth_revenueclass'] = 'color_red';
                        }
                    }
                    $voptions['growth_avgrevenueclass'] = $voptions['growth_profitclass'] = '';
                    if ($growth['avgrevenue_num'] == 0) {
                        $voptions['growth_avgrevenuenum'] = '&mdash;';
                        $voptions['growth_avgrevenueprc'] = '&mdash;';
                    } else {
                        if ($growth['avgrevenue_num'] > 0) {
                            $voptions['growth_avgrevenuenum'] = MoneyOutput($growth['avgrevenue_num'], 0);
                            $voptions['growth_avgrevenueprc'] = $growth['avgrevenue_perc'] . '%';
                        } else {
                            $voptions['growth_avgrevenuenum'] = '(' . MoneyOutput(abs($growth['avgrevenue_num']), 0) . ')';
                            $voptions['growth_avgrevenueprc'] = '(' . abs($growth['avgrevenue_perc']) . '%)';
                            $voptions['growth_avgrevenueclass'] = 'color_red';
                        }
                    }
                    if ($growth['profit_num'] == 0) {
                        $voptions['growth_profitnum'] = '&mdash;';
                        $voptions['growth_profitprc'] = '&mdash;';
                    } else {
                        if ($growth['profit_num'] > 0) {
                            $voptions['growth_profitnum'] = MoneyOutput($growth['profit_num'], 0);
                            $voptions['growth_profitprc'] = $growth['profit_perc'] . '%';
                        } else {
                            $voptions['growth_profitnum'] = '(' . MoneyOutput(abs($growth['profit_num']), 0) . ')';
                            $voptions['growth_profitprc'] = '(' . abs($growth['profit_perc']). '%)';
                            $voptions['growth_profitclass'] = 'color_red';
                        }
                    }
                    if ($growth['avgprofit_num'] == 0) {
                        $voptions['growth_avgprofitnum'] = '&mdash;';
                        $voptions['growth_avgprofitprc'] = '&mdash;';
                    } else {
                        if ($growth['avgprofit_num'] > 0) {
                            $voptions['growth_avgprofitnum'] = MoneyOutput($growth['avgprofit_num'], 0);
                            $voptions['growth_avgprofitprc'] = $growth['avgprofit_perc'] . '%';
                        } else {
                            $voptions['growth_avgprofitnum'] = '(' . MoneyOutput(abs($growth['avgprofit_num']), 0) . ')';
                            $voptions['growth_avgprofitprc'] = '(' . abs($growth['avgprofit_perc']) . '%)';
                            $voptions['growth_avgprofitclass'] = 'color_red';
                        }
                    }
                    $voptions['growth_aveprcclass'] = '';
                    if ($growth['ave_num'] == 0) {
                        $voptions['growth_avenum'] = '&mdash;';
                        $voptions['growth_aveprc'] = '&mdash;';
                    } else {
                        if ($growth['ave_num'] > 0) {
                            $voptions['growth_avenum'] = $growth['ave_num'] . '%';
                            $voptions['growth_aveprc'] = $growth['ave_proc'] . '%';
                        } else {
                            $voptions['growth_avenum'] = '(' . abs($growth['ave_num']) . '%)';
                            $voptions['growth_aveprc'] = '(' . abs($growth['ave_proc']) . '%)';
                            $voptions['growth_aveprcclass'] = 'color_red';
                        }
                    }
                }
                $yearview .= $this->load->view('accounting/year_data_view', $voptions, TRUE);
            }
            $numyears++;
        }
        if ($showgrowth==1) {
            $slider_width=($dats['cur_year']-$dats['min_year'])*246+100; // +547;
        } else {
            $slider_width=($dats['cur_year']-$dats['min_year']+1)*136;// +364; // +363;
        }
        $margin=(918-$slider_width);

        $out=array(
            'content'=>$yearview,
            'slider_width'=>$slider_width,
            'margin'=>$margin,
        );
        return $out;
    }

    // Open Invoices
    private function _prepare_openinvoice_content($brand) {
        $filtr=array(
            'paid'=>1,
            'brand' => $brand,
        );
        /* count, sums */
        $dats=$this->orders_model->get_count_monitor($filtr);
        $searchform=$this->load->view('finopenivoice/monitor_search_view',array(),TRUE);

        $perpage_data=array(
            // 'fieldname'=>'perpagetab4',
            'fieldname'=>'perpageopeninvoice',
            'default_value'=>$this->order_profit_perpage,
            'numrecs'=>$this->perpage_options,
        );

        $perpage_view=$this->load->view('page/number_records', $perpage_data, TRUE);

        $options=array(
            'perpage_view'=>$perpage_view,
            'order'=>'order_num',
            'direc'=>'desc',
            'total'=>$dats['total_rec'],
            'curpage'=>0,
            'total_inv'=>'',
            'qty_inv'=>'',
            'total_paid'=>'',
            'qty_paid'=>'',
            'searchform'=>$searchform,
            'paid'=>1,
            'brand' => $brand,
        );

        $content=$this->load->view('finopenivoice/paymonitor_view',$options,TRUE);
        return $content;
    }

    private function _prepare_batches_view($brand) {
        /* Batch calendar */
        /*get data about batches from current data */
        $details='';
        $calendar_view='';
        // Get a list of batch years
        $this->load->model('batches_model');
        $brandcalc = 'ALL';
        $years_list=$this->batches_model->get_batches_years($brandcalc);
        $options=array(
            'details'=>$details,
            'calendar'=>$calendar_view,
            'years'=>$years_list,
            'brand' => $brand,
        );
        $content=$this->load->view('batch/batches_view',$options,TRUE);
        return $content;
    }

    private function _prepare_netprofit_content($brand) {
        $weeklist=$this->balances_model->get_weeklist();
        $weekyearlist=$this->balances_model->get_currentyearweeks();
        $page_options = [
            'weeklists'=>$weeklist,
            'weekyearlist' => $weekyearlist,
            'brand' => $brand,
            'limitrow' => $this->weekshow_limit,
            'view' => 'detail',
        ];
        $content=$this->load->view('netprofitnew/page_view', $page_options ,TRUE);
        return $content;

    }

    private function _oldprepare_netprofit_content($brand) {
        $title=$this->load->view('netprofit/admin_netprofitweek_view',array(),TRUE);
        // Get List of weeks
        $weeklist=$this->balances_model->get_weeklist();
        $weekopt=array(
            'weekallcheck'=>'checked="checked"',
            'weekfrom'=>'',
            'weekuntil'=>'',
            'weeklist'=>$weeklist,
        );
        $weekselect=$this->load->view('netprofit/week_select_view',$weekopt,TRUE);
        // Prepare Years totals
        $options=array(
            'compareweek'=>0,
            'paceincome'=>1,
            'paceexpense'=>1,
            'brand' => $brand,
        );
        $yearstotals=$this->balances_model->get_netprofit_totalsbyweekdata($options);

        $yearstotals['compareweek']=0;
        $yearstotals['paceincome']=1;
        $yearstotals['paceexpense']=1;

        $table_view=$this->load->view('netprofit/chartdata_table_view',$yearstotals, TRUE);
        $weekyearlist=$this->balances_model->get_currentyearweeks();
        // Build chart data
        $chartoptions=array(
            'table_view'=>$table_view,
            'weeklist'=>$weekyearlist,
            'paceincome'=>1,
            'paceexpense'=>1,
        );
        $chartdata=$this->load->view('netprofit/chartdata_view', $chartoptions, TRUE);
        // Build On Race compare with prev year
        $endyear=$yearstotals['end_year'];
        if ($endyear==date('Y')) {
            $endyear=$endyear-1;
        }
        $years=array();
        for ($i=$endyear; $i>=$yearstotals['start_year']; $i--) {
            array_push($years, $i);
        }
        $compare_options=array(
            'compareyear'=>$endyear,
            'paceincome'=>1,
            'paceexpense'=>1,
            'brand' => $brand,
        );
        $pacedata=$this->balances_model->get_onpacecompare($compare_options);
        $pacegrow_view=$this->load->view('netprofit/onpace_grown_view', $pacedata,TRUE);
        // W9 Work
        $w9data=$this->balances_model->get_w9purchase_tabledata($brand);
        $w9table_options=array(
            'w9totals'=>$w9data['w9totals'],
            'w9details'=>$w9data['w9details'],
            'purchasetotals'=>$w9data['purchasetotals'],
            'purchasedetails'=>$w9data['purchasedetails'],
            'w9sortfld'=>'amount_perc',
            'w9sortdir'=>'desc',
            'purchasesortfld'=>'amount_perc',
            'purchasesortdir'=>'desc',
            'sortascicon'=>'<i class="fa fa-caret-square-o-up" aria-hidden="true"></i>',
            'sortdescicon'=>'<i class="fa fa-caret-square-o-down" aria-hidden="true"></i>',
            'year'=>$w9data['years'][0]['year'],
            'brand' => $brand,
        );
        $w9tableview=$this->load->view('netprofit/w9purchase_table_view', $w9table_options, TRUE);
        $w9options=array(
            'years'=>$w9data['years'],
            'totals'=>$w9data['totals'],
            'table_view'=>$w9tableview,
        );
        $w9purchase_view=$this->load->view('netprofit/w9purchase_view', $w9options, TRUE);
        // Get year, start / end time
        $expancedates=$this->balances_model->get_expansedates();

        $pageoptions=array(
            'title'=>$title,
            'weekselect'=>$weekselect,
            'sorting'=>'profitdate_desc',
            'chartdata_view'=>$chartdata,
            'limitrow'=>$this->weekshow_limit,
            'years'=>$years,
            'pacegrow_view'=>$pacegrow_view,
            'cur_year'=>$expancedates['year'],
            'cur_start'=>$expancedates['datebgn'],
            'cur_end'=>$expancedates['dateend'],
            'w9purchase'=>$w9purchase_view,
            'brand' => $brand,
        );
        $content=$this->load->view('netprofit/admin_netprofit_view',$pageoptions ,TRUE);
        return $content;
    }

    private function _prepare_ownerstaxes_view($brand) {
        $options = [
            'brand' => $brand,
        ];
        return $this->load->view('accounting/ownertaxes_view', $options, TRUE);
    }

    private function _prepare_expensives_view($brand) {
        $options = [
            'calcdirec'=>'desc',
            'calcsort'=>'yearly',
            'brand' => $brand,
        ];
        return $this->load->view('expensives/page_view', $options, TRUE);
        // return $this->load->view('accounting/opercalc_form_view',$options,TRUE);
    }

    private function _prepare_purchaseorders_view($brand) {
        $inner = 0;
        $this->load->model('orders_model');
        $this->load->model('payments_model');

        // $totaltab = $this->orders_model->purchaseorder_totals($inner, $brand);
        $totaltab = [];
        $totaltab['toplace'] = [
            'qty' => 0,
            'total' => 0,
        ];
        $totaltab['toapprove'] = [
            'qty' => 0,
            'total' => 0,
        ];
        $totaltab['toproof'] = [
            'qty' => 0,
            'total' => 0,
        ];

        // $totals = $this->orders_model->purchase_fulltotals($brand);
        $totals = [
            'total' => 0,
            'totalfree' => 0,
        ];

        // Years
        $years = $this->payments_model->get_pototals_years($brand);
        $year1 = $year2 = $year3 = $years[0];
        if (count($years) > 1) {
            $year2 = $years[1];
        }
        if (count($years) > 2) {
            $year3 = $years[2];
        }
        // Temporary
        $poreptotals = $this->payments_model->get_poreport_totals($year1, $year2, $year3, $brand);
        $options=[
            'totaltab' => $totaltab,
            'totals' => $totals,
            'inner' => $inner,
            'brand' => $brand,
            'years' => $years,
            'year1' => $year1,
            'year2' => $year2,
            'year3' => $year3,
            'poreporttotals' => $poreptotals,
            'poreportperpage' => 8,
        ];
        return $this->load->view('pototals/page_view',$options,TRUE);
    }

    private function _prepare_accreceiv_view($brand) {
        $options=[
            'brand' => $brand,
        ];
        return $this->load->view('accreceiv/page_view',$options, TRUE);
    }

}