<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fulfillment extends MY_Controller
{

    private $pagelink = '/fulfillment';

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

}