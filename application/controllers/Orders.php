<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Controller
{

    private $pagelink = '/orders';

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
        $head['title'] = 'Orders';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];

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

        foreach ($menu as $row) {
            if ($row['item_link']=='#ordersview') {
                // Orders
                $head['styles'][]=array('style'=>'/css/orders/ordersview.css');
                $head['scripts'][]=array('src'=>'/js/orders/ordersview.js');
                $content_options['ordersview'] = $this->_prepare_orders_view($brand, $top_menu);
            } elseif ($row['item_link']=='#orderlistsview') {
                $head['styles'][]=array('style'=>'/css/orders/orderslistview.css');
                $head['scripts'][]=array('src'=>'/js/orders/orderslistview.js');
                $content_options['orderlistsview'] = $this->_prepare_orderlist_view($brand, $top_menu);
            } elseif ($row['item_link']=='#requestlist') {
                $head['styles'][]=array('style'=>'/css/art/requestlist.css');
                $head['scripts'][]=array('src'=>'/js/art/requestlist.js');
                $content_options['requestlist'] = $this->_prepare_requestlist_view();
            }
        }

        $content_options['menu'] = $menu;
        $content_view = $this->load->view('orders/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/orders/page.js');
        $head['styles'][] = array('style' => '/css/orders/orderspage.css');
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
            $search='';
            $order_by = 'order_num';
            $direct = 'desc';

            $options=array(
                'offset' => $offset,
                'limit' => $limit,
                'order_by' => 'order_num',
                'direct' => 'desc',
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
                if (isset($postdata['listdata']) && $postdata['listdata']==1) {
                    $options=array(
                        'data'=>$ordersdat,
                    );
                    $content = $this->load->view('orders/orderslist_datalist_view', $options, TRUE);
                } else {
                    $data=array(
                        'data'=>$ordersdat,
                        'role'=>'user',
                    );
                    $content = $this->load->view('orders/orders_datalist_view', $data, TRUE);
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

    private function _prepare_orders_view($brand, $top_menu) {
        $datqs=[
            'brand' => $brand,
            'top_menu' => $top_menu,
            'perpage' => $this->config->item('perpage_orders'),
            'activesearch' => ''
        ];
        $this->load->model('orders_model');
        $users=$this->user_model->get_user_leadreplicas();

        $options=[];

        $ordertemplate=usersession('searchordertemplate');
        if (!empty($ordertemplate)) {
            // Check, that such order exits and # of orders==1
            $res=$this->orders_model->get_orderbynum($ordertemplate);
            if ($res['detail']!=0) {
                $datqs['activesearch']=$res['detail'];
            }
            $options['search']=strtoupper($ordertemplate);
            $datqs['search']=$ordertemplate;
            usersession('searchordertemplate',NULL);
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
        return  $this->load->view('orders/orders_head_view',$datqs,TRUE);
    }

    private function _prepare_orderlist_view($brand, $top_menu) {
        $datqs=[
            'brand' => $brand,
            'top_menu' => $top_menu,
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
}