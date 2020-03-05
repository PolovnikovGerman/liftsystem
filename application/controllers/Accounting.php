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
            $this->load->model('orders_model');
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
        $this->load->model('orders_model');
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
            $this->load->model('orders_model');
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
            $this->load->model('orders_model');
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
            $this->load->model('orders_model');
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
        $this->load->model('orders_model');
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
        $this->load->model('orders_model');
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
            $this->load->model('orders_model');
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
            $this->load->model('orders_model');
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

    // Private functions - Orders Profit
    private function _prepare_order_profit ($brand, $top_menu) {
        // $search_form=''
        $legend=$this->load->view('accounting/profit_legend_view',array(),TRUE);
        /* Calc total orders */
        $this->load->model('orders_model');
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

}