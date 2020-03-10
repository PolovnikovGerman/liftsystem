<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Accounting extends MY_Controller
{

    private $pagelink = '/accounting';
    private $order_profit_perpage=100;
    private $order_totals_perpage=100;
    private $perpage_options =array(100, 250, 500, 1000);

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
        $this->load->model('orders_model');
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Accounting';
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
            if ($row['item_link']=='#profitordesview') {
                $head['styles'][]=array('style'=>'/css/accounting/profitordesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/profitordesview.js');
                $content_options['profitordesview'] = $this->_prepare_order_profit($brand, $top_menu);
            } elseif ($row['item_link']=='#profitdatesview') {
                $head['styles'][]=array('style'=>'/css/accounting/profitdatesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/profitdatesview.js');
                $content_options['profitdatesview'] = $this->_prepare_profitcalend_content($brand, $top_menu);
            } elseif ($row['item_link']=='#openinvoicesview') {
                $head['styles'][]=array('style'=>'/css/accounting/openinvoicesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/openinvoicesview.js');
                $content_options['openinvoicesview'] = $this->_prepare_openinvoice_content($brand, $top_menu);
            } elseif ($row['item_link']=='#financebatchesview') {
                $head['styles'][]=array('style'=>'/css/accounting/financebatchesview.css');
                $head['scripts'][]=array('src'=>'/js/accounting/financebatchesview.js');
                $content_options['financebatchesview'] = $this->_prepare_batches_view($brand, $top_menu);
            }
        }
        $content_options['menu'] = $menu;
        $content_view = $this->load->view('accounting/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/accounting/page.js');
        $head['styles'][] = array('style' => '/css/accounting/accountpage.css');
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
                    if ($postdata['month']==0) {
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
            /* count number of orders */
            $options['admin_mode']=0;
            if ($this->USR_ROLE=='masteradmin') {
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
                ];
                $revenue_tooltip = $this->load->view('orderprofit/total_tooltip_view', $revenue_tool_options, TRUE);
                $shipping_tool_options = [
                    'title' => 'Shipping',
                    'type' => 'money',
                    'new_val' => $totalord['shipping_detail_new'],
                    'repeat_val' => $totalord['shipping_detail_repeat'],
                    'blank_val' => $totalord['shipping_detail_blank'],
                    'new_perc' => $totalord['shipping_detail_newperc'],
                    'repeat_perc' => $totalord['shipping_detail_repeatperc'],
                    'blank_perc'=> $totalord['shipping_detail_blankperc'],
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
                ];
                $mdata['total_row']=$this->load->view('orderprofit/total_profitall_view',$total_options,TRUE);
                $mdata['totals_head']=$this->load->view('orderprofit/total_allprofittitle_view',[],TRUE);
                // $mdata['total_row']=$this->load->view('orderprofit/total_profit_view',$totalord,TRUE);
            } else {
                $mdata['totals_head']=$this->load->view('orderprofit/total_profittitle_view',[],TRUE);
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
                    if ($postdata['month']==0) {
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

            /* Fetch data about prices */
            if ($this->USR_ROLE=='masteradmin') {
                $admin_mode=1;
            } else {
                $admin_mode=0;
            }
            if (isset($postdata['brand']) && !empty($postdata['brand'])) {
                $search['brand'] = $postdata['brand'];
            }
            $ordersdat=$this->orders_model->get_profit_orders($search,$order_by,$direct,$limit,$offset, $admin_mode, $this->USR_ID);

            if (count($ordersdat)==0) {
                $content=$this->load->view('orderprofit/empty_view',array(),TRUE);
            } else {
                $data=array(
                    'orders'=>$ordersdat,
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
    // Profit (Date)
    public function profit_calendar() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $month=$this->input->post('month');
            $year=$this->input->post('year');
            $brand = $this->input->post('brand');
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
                $error = '';
                $this->load->model('batches_model');
                /* Get Max & min date  */
                $batch_dates=$this->batches_model->get_batches_limits($brand);
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
                    'brand' => $brand,
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
                $this->load->model('');
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
                    $batch_date=$batch_data['batch_date'];
                    $mdata['batch_date']=$batch_date;
                    $mdata['batch_due']=$batch_data['batch_due'];

                    /* prepare new day content */
                    $filter=array(
                        'batch_date'=>$batch_date,
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
                        'brand' => $options['brand'],
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
            $brand = ifset($options,'brand');
            $batch_data=$this->batches_model->get_batch_detail($options['batch_id']);
            if ($batch_data['order_id']) {
                $options['batch_sum']=($this->batches_model->get_batchsum_order($batch_data['order_id'])-$batch_data['batch_amount']);
                $order_data=$this->orders_model->get_order_detail($batch_data['order_id']);
                $options['order_revenue']=$order_data['revenue'];
                $old_batch_due=$batch_data['batch_due'];
                $batch_date=$batch_data['batch_date'];
                $mdata['batch_date']=$batch_date;
                $options['batch_date']=$batch_data['batch_date'];
                $res=$this->batches_model->save_batchrow($options,$user_id);
            } else {
                $options['batch_date']=$batch_data['batch_date'];
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
            $error='';
            $batchdate=$this->input->post('batchdate');
            $filtr=$this->input->post('filter');
            $brand = $this->input->post('brand');
            $newrec=array(
                'create_usr'=>  $this->USR_ID,
                'create_date'=>date('Y-m-d H:i:s'),
                'update_usr'=>  $this->USR_ID,
                'batch_date'=>$batchdate,
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
            );
            $mdata['content']=$this->load->view('batch/batch_daydetails_view',$options,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }


    // Private functions - Orders Profit
    private function _prepare_order_profit ($brand, $top_menu) {
        // $search_form=''
        $legend=$this->load->view('accounting/profit_legend_view',array(),TRUE);
        /* Calc total orders */
        $options=[];
        $options['brand'] = $brand;
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
            'adminview' => $this->USR_ROLE=='masteradmin' ? 1 : 0,
            'brand' => $brand,
            'top_menu' => $top_menu,
        );
        $years=$this->orders_model->get_orders_dates($options);
        $orders_cnttotal='';
        $years_options=[];
        $years_options[]=['key'=>0, 'label'=>'All time'];
        $select_year=$years['max_year'];
        for ($i=0; $i<20; $i++) {
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

    private function _prepare_profitcalend_content($brand, $top_menu) {
        $dats=$this->orders_model->get_profit_limitdates($brand);
        $legend=$this->load->view('accounting/profit_legend_view',array(),TRUE);
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
        $slider=$this->_prepare_profit_dateslider($brand, 1);
        $yearview=$slider['content'];
        $slider_width=$slider['slider_width'];
        $margin=$slider['margin'];
        // Get Previous Month
        $datefiltr=new DateTime('NOW');
        $datefiltr->modify('-1 Month');

        $options=array(
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
        if ($showgrowth==1) {
            $slider_width=($dats['cur_year']-$dats['min_year'])*246+547;
        } else {
            $slider_width=($dats['cur_year']-$dats['min_year']+1)*132+364; // +363;
        }
        // $slider_width=$numyears*172+83;
        $margin=(918-$slider_width);
        /*  margin-left: -1164px;
            width: 2064px; */
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
    private function _prepare_openinvoice_content($brand, $top_menu) {
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
            'top_menu' => $top_menu,
        );

        $content=$this->load->view('finopenivoice/paymonitor_view',$options,TRUE);
        return $content;
    }

    private function _prepare_batches_view($brand, $top_menu) {
        /* Batch calendar */
        /*get data about batches from current data */
        $details='';
        $calendar_view='';
        // Get a list of batch years
        $this->load->model('batches_model');
        $years_list=$this->batches_model->get_batches_years($brand);
        $options=array(
            'details'=>$details,
            'calendar'=>$calendar_view,
            'years'=>$years_list,
            'brand' => $brand,
            'top_menu' => $top_menu,
        );
        $content=$this->load->view('batch/batches_view',$options,TRUE);
        return $content;
    }

}