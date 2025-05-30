<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Controller
{

    private $pagelink = '/orders';
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
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Orders';
        $brand = $this->current_brand;
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        $content_options = [];
        $start = $this->input->get('start', TRUE);
        $search = usersession('liftsearch');
        usersession('liftsearch', NULL);
        $gmaps = 0;
        if (!empty($this->config->item('google_map_key'))) {
            $gmaps = 1;
        }

        foreach ($menu as $row) {
            if ($row['item_link']=='#ordersview') {
                // Orders
                $head['styles'][]=array('style'=>'/css/orders/ordersview.css');
                $head['scripts'][]=array('src'=>'/js/orders/ordersview.js');
                $content_options['ordersview'] = $this->_prepare_orders_view($brand, $search);
            } elseif ($row['item_link']=='#orderlistsview') {
                $head['styles'][]=array('style'=>'/css/orders/orderslistview.css');
                $head['scripts'][]=array('src'=>'/js/orders/orderslistview.js');
                $content_options['orderlistsview'] = $this->_prepare_orderlist_view($brand);
            } elseif ($row['item_link']=='#onlineordersview') {
                $head['styles'][]=array('style'=>'/css/orders/onlineorders.css');
                $head['scripts'][]=array('src'=>'/js/orders/onlineorders.js');
                $content_options['onlineordersview'] = $this->_prepare_onlineorders($brand);
            }
        }

        $content_options['menu'] = $menu;
        // Add main page management
        $head['scripts'][] = array('src' => '/js/orders/page.js');
        $head['styles'][] = array('style' => '/css/orders/orderspage.css');
        // Order popup
        $head['styles'][]=array('style'=>'/css/leadorder/popup.css');
        $head['scripts'][]=array('src'=>'/js/leads/leadorderpopup.js');
        // Customer autocomplete
        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
        // Uploader
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        if ($gmaps==1) {
            $head['scripts'][]=array('src'=>'/js/leads/order_address.js');
        }
        // File Download
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Select 2
        $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
        $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];

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
            'brand' => $brand,
        ];
        if ($gmaps==1) {
            $options['gmaps'] = $gmaps;
        }
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['brand'] = $brand;
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options['menu_view'] = $this->load->view('page_modern/submenu_view',['menu' => $menu, 'start' => $start, 'brandclass' => $brandclass ], TRUE);
        $content_view = $this->load->view('orders/page_new_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $dat['modal_view'] = $this->load->view('orders/modal_view', [], TRUE);
        $this->load->view('page_modern/page_template_view', $dat);
    }

    // Orders view
    // Search
    public function leadorder_count() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('orders_model');
            $postdata=$this->input->post();
            $options=array();
            $show_totals = 0;
            if (isset($postdata['user_replic'])) {
                if ($postdata['user_replic']<0) {
                    if ($postdata['user_replic']==-2) {

                    } else {
                        $options['weborder']=1;
                    }
                } elseif ($postdata['user_replic']>0) {
                    $options['order_usr_repic']=$postdata['user_replic'];
                } elseif ($postdata['user_replic']==0) {
                    $options['unassigned']=1;
                }
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }
            if (isset($postdata['order_qty']) && $postdata['order_qty']==0) {
                $options['order_qty']=0;
                $show_totals = 1;
            }
            if (isset($postdata['brand'])) {
                $options['brand']=$postdata['brand'];
            }
            $mdata['total']=$this->orders_model->get_count_orders($options);
            if ($show_totals==1 && isset($options['brand'])) {
                $mdata['show_totals']=1;
                $totals=$this->orders_model->get_missed_orders($options['brand']);
                $mdata['total_view']=$this->load->view('orders/orderlist_totals_view', array('totals'=>$totals),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Orders content
    public function leadorder_data() {
        if ($this->isAjax()) {
            $this->load->model('leadorder_model');
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $pagenum=ifset($postdata, 'offset', 0);
            $limit=ifset($postdata, 'limit',  100);

            $offset=$pagenum*$limit;
            // $search='';
            $order_by = 'order_num';
            $direct = 'desc';

            $options=array(
                'offset' => $offset,
                'limit' => $limit,
                'order_by' => $order_by,
                'direct' => $direct,
            );

            if (isset($postdata['user_replic'])) {
                if ($postdata['user_replic']<0) {
                    if ($postdata['user_replic']==-2) {

                    } else {
                        $options['weborder']=1;
                    }
                } elseif ($postdata['user_replic']>0) {
                    $options['order_usr_repic']=$postdata['user_replic'];
                } elseif ($postdata['user_replic']==0) {
                    $options['unassigned']=1;
                }
            }

            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }

            if (isset($postdata['order_qty'])) {
                if ($postdata['order_qty']==0) {
                    $options['order_qty']=0;
                }
            }

            if (isset($postdata['brand'])) {
                $options['brand']=$postdata['brand'];
            }

            $ordersdat=$this->leadorder_model->get_leadorders($options);

            if (count($ordersdat)==0) {
                $content=$this->load->view('orders/orders_emptylist_view',array(),TRUE);
            } else {
                $brand = ifset($postdata,'brand','SB');
                if (isset($postdata['listdata']) && $postdata['listdata']==1) {
                    $options=array(
                        'data'=>$ordersdat,
                        'brand' => ifset($postdata,'brand','SB'),
                    );
                    $content = $this->load->view('orders/orderslist_datalist_view', $options, TRUE);
                } else {
                    $data=array(
                        'data'=>$ordersdat,
                        'role'=>'user',
                        'brand' => ifset($postdata,'brand','SB'),
                    );
                    if ($brand=='SR') {
                        $content = $this->load->view('orders/srorders_datalist_view', $data, TRUE);
                    } else {
                        $content = $this->load->view('orders/orders_datalist_view', $data, TRUE);
                    }

                }
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function leadorder_qtysave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Unknown Order #';
            $postdata=$this->input->post();
            $order_id = ifset($postdata,'order_id',0);
            if ($order_id>0) {
                $options=array(
                    'user_id'=>$this->USR_ID,
                    'order_id'=>$postdata['order_id'],
                    'order_qty'=>intval($postdata['order_qty']),
                );
                $this->load->model('orders_model');
                $res=$this->orders_model->update_order($options);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $totals=$this->orders_model->get_missed_orders($postdata['brand']);
                    $mdata['totals']=$this->load->view('orders/orderlist_totals_view', array('totals'=>$totals),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function leadorder_qtysaveall() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Parameter';
            $postdata=$this->input->post();
            $brand = ifset($postdata,'brand');
            $saverow = ifset($postdata,'saverow');
            if (!empty($brand) && !empty($saverow)) {
                $error = '';
                $ordarray=  explode('|', $saverow);
                $this->load->model('orders_model');
                foreach ($ordarray as $row) {
                    if (!empty($row)) {
                        // Devide
                        $dat=  explode('-', $row);
                        $order_id=$dat[0];
                        $order_qty=intval($dat[1]);
                        $options=array(
                            'user_id'=>$this->USR_ID,
                            'order_id'=>$order_id,
                            'order_qty'=>$order_qty,
                        );
                        $res=$this->orders_model->update_order($options);
                    }
                }
                $totals=$this->orders_model->get_missed_orders($brand);
                $mdata['totals']=$this->load->view('orders/orderlist_totals_view', array('totals'=>$totals),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function onlinesearch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $replica=$this->input->post('replica');
            $confirm=$this->input->post('confirm');
            $customer=$this->input->post('customer');
            $brand = $this->input->post('brand');
            $options = [];
            if (!empty($replica)) {
                $options['replica']=$replica;
            }
            if (!empty($confirm)) {
                $options['confirm'] = $confirm;
            }
            if (!empty($customer)) {
                $options['customer'] = $customer;
            }
            $this->load->model('orders_model');
            $mdata['total_rec']=$this->orders_model->count_onlineorders($brand, $options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function onlineorderdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';

            $offset = $this->input->post('offset', 0);
            $limit = $this->input->post('limit', 10);
            $order_by = $this->input->post('order_by', 'order_id');
            $direct = $this->input->post('direction', 'desc');
            $replica = $this->input->post('replica');
            $confirm = $this->input->post('confirm');
            $customer = $this->input->post('customer');
            $brand = $this->input->post('brand');
            $offset = $offset * $limit;

            $search = array();
            if ($replica != '') {
                $search['replica'] = $replica;
            }
            if ($confirm != '') {
                $search['confirm'] = $confirm;
            }
            if ($customer != '') {
                $search['customer'] = $customer;
            }
            $search['brand'] = $brand;
            /* Get data  */
            $this->load->model('orders_model');
            $orders_dat = $this->orders_model->get_online_orders(array(), $order_by, $direct, $limit, $offset, $search);

            $data = array('orders_dat' => $orders_dat);

            $mdata['content']=$this->load->view('orders/onlineorders_data_view', $data, TRUE);


            $this->ajaxResponse($mdata, $error);
        }
    }

    public function online_details() {
        if ($this->isAjax()) {
            $order_id=$this->input->post('order_id');
            $mdata=array();
            /* Get Data about order */
            /*$options=array(
                'order_id'=>$order_id,
            );*/
            $this->load->model('orders_model');
            $data=$this->orders_model->orderonline_details($order_id, $this->USER_PAYMENT);
            $error = $data['msg'];
            if ($data['result']==$this->success_result) {
                $error = '';
                $order_data = $data['data'];
                if ($order_data['order_status']=='NEW') {
                    // Change Order Num input
                    $ordnums=$this->orders_model->finorder($order_data['order_date']);
                    $order_data['order_num_view']=$this->load->view('orders/ordernum_select_view',array('orders'=>$ordnums),TRUE);
                } else {
                    $order_data['order_num_view']=$this->load->view('orders/ordernum_input_view',$order_data,TRUE);
                }
                $datart = array();
                $datart['art'] = $this->orders_model->get_online_artwork($order_id);
                $art_disp = $this->load->view('orders/onlineorders_artwork_view', $datart, TRUE);
                $options = array(
                    'order' => $order_data,
                    'artwork' => $art_disp,
                    'imprint' => $this->online_imprintval($order_data['imprinting'])
                );
                $mdata['content'] = $this->load->view('orders/onlineorders_detail_view', $options, TRUE);
                $mdata['footer'] = $this->load->view('orders/onlineorders_footer_view',[], TRUE);
                $mdata['title'] = 'Order '.$order_data['order_confirmation'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function upload_attach() {
        $url=$this->input->post('url');
        $this->load->helper('file');
        $pathdest=$this->config->item('artwork_logo');
        $filename=str_replace('/uploads/artlogos/',$pathdest,$url);
        if (file_exists($filename)) {
            $short_name=  str_replace('/uploads/artlogos/', '', $url);
            $url=base_url().$url;
            header("Pragma: public");
            header('Content-disposition: attachment; filename='.$short_name);
            header("Content-type: ".get_mime_by_extension($filename));
            header('Content-Transfer-Encoding: binary');
            readfile($url);
        } else {
            echo 'Sorry - file not exist';
        }
    }

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

    public function unlockonlinepayments()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $error = 'Empty Verify Code';
            $code = ifset($postdata,'code','');
            $order_id = $postdata['order_id'];
            if (!empty($code) && !empty($order_id)) {
                $this->load->model('orders_model');
                $res = $this->orders_model->online_payment_view($code, $order_id, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['cardnum'] = $res['cardnum'];
                    $mdata['cardcode'] = $res['cardcode'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function online_imprintval($typeimprint) {
        $out='';
        if ($typeimprint==0) {
            $out='Blank, no imprinting';
        } elseif($typeimprint==1) {
            $out='Imprinting in one color';
        } elseif ($typeimprint==2) {
            $out='Imprinting in two colors';
        }
        return $out;
    }

    private function _prepare_orders_view($brand, $liftsearch) {
        $datqs=[
            'brand' => $brand,
            'perpage' => $this->config->item('perpage_orders'),
            'activesearch' => '',
            'default_perpage' => 500,
        ];
        $this->load->model('orders_model');
        $users=$this->user_model->get_user_leadreplicas();

        $options=[];
        $options['brand'] = $brand;
        $ordertemplate=$liftsearch;
        if (!empty($ordertemplate)) {
            // Check, that such order exits and # of orders==1
            $res=$this->orders_model->get_orderbynum($ordertemplate, $brand);
            if ($res['detail']!=0) {
                $datqs['activesearch']=$res['detail'];
            }
            $options['search']=strtoupper($ordertemplate);
            $datqs['search']=$ordertemplate;
        } else {
            $datqs['search']='';
        }
        $this->load->model('orders_model');
        $datqs['total']=$this->orders_model->get_count_orders($options);
        $datqs['users']=$users;

        $datqs['current_user']=-2;
        $datqs['order_by']='order_id';
        $datqs['direction']='desc';
        $datqs['cur_page']=0;
        if ($brand=='SR') {
            return  $this->load->view('orders/srorders_head_view',$datqs,TRUE);
        } else {
            return  $this->load->view('orders/orders_head_view',$datqs,TRUE);
        }

    }

    private function _prepare_orderlist_view($brand) {
        $datqs=[
            'brand' => $brand,
            'perpage' => [30, 60, 90, 120],
            'order_by' => 'order_id',
            'direction' => 'desc',
            'cur_page' => 0,
        ];
        $options=array(
            'order_qty'=>0,
            'brand' => $brand,
        );
        $this->load->model('orders_model');
        $datqs['total']=$this->orders_model->get_count_orders($options);
        // Get totals by years
        $totals=$this->orders_model->get_missed_orders($brand);
        $datqs['total_view']=$this->load->view('orders/orderlist_totals_view', array('totals'=>$totals),TRUE);
        return $this->load->view('orders/orderlist_head_view',$datqs,TRUE);
    }

    private function _prepare_onlineorders($brand) {
        $datl=[
            'order_by' => 'order_id',
            'direction' => 'desc',
            'cur_page' => 0,
            'perpage' => 250,
            'brand' => $brand,
        ];
        $this->load->model('orders_model');
        $datl['total_rec']=$this->orders_model->count_onlineorders($brand);
        $datl['last_order']=$this->orders_model->last_order($brand);
        $datl['last_cart']=$this->orders_model->last_attempt($brand);
        // $content=$this->load->view('orders/orders_list_view',$datl,TRUE);
        return $this->load->view('orders/onlineorders_head_view',$datl,TRUE);

    }
}