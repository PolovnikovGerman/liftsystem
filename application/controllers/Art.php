<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Art extends MY_Controller {

    private $pagelink = '/art';
    protected $artorderperpage=250;

    public function __construct()
    {
        parent::__construct();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink);
        if ($pagedat['result']==$this->error_result) {
            show_404();
        }
        $page = $pagedat['menuitem'];
        $permdat = $this->menuitems_model->get_menuitem_userpermisiion($this->USR_ID, $page['menu_item_id']);
        if ($permdat['result']==$this->success_result && $permdat['permission']>0) {
        } else {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url' => '/'), 'Your have no permission to this page');
            } else {
                redirect('/');
            }
        }
    }

    public function index() {
        $head=[];
        $head['title']='ART';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#taskview') {
                // Taks View
                $head['styles'][]=array('style'=>'/css/art/taskview.css');
                $head['scripts'][]=array('src'=>'/js/art/taskview.js');
                $content_options['taskview'] = $this->_prepare_task_view();
            } elseif ($row['item_link']=='#orderlist') {
                $head['styles'][]=array('style'=>'/css/art/orderslist.css');
                $head['scripts'][]=array('src'=>'/js/art/orderslist.js');
                $content_options['orderlist'] = $this->_prepare_orderlist_view();
            } elseif ($row['item_link']=='#requestlist') {
                $head['styles'][]=array('style'=>'/css/art/requestlist.css');
                $head['scripts'][]=array('src'=>'/js/art/requestlist.js');
                $content_options['requestlist'] = $this->_prepare_requestlist_view();
            }
        }
        $content_options['menu']=$menu;
        $content_view = $this->load->view('artpage/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][]=array('src'=>'/js/art/page.js');
        $head['styles'][] = array('style'=> '/css/art/artpage.css');
        // Utils
        // $head['scripts'][]=array('src'=>'/js/jquery.bt.js');
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
        // $this->load->view('design/main_art_view');
    }

    public function tasks_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';

            $taskview=$this->input->post('taskview','orders');
            $inclreq=$this->input->post('showreq',0);
            $nonart_sort=$this->input->post('nonart_sort','time');
            $nonart_direc=$this->input->post('nonart_direc','desc');
            $redraw_sort=$this->input->post('redraw_sort','time');
            $redraw_direc=$this->input->post('redraw_direc','desc');
            $proof_sort=$this->input->post("proof_sort",'time');
            $proof_direc=$this->input->post('proof_direc','desc');
            $needapr_sort=$this->input->post('needapr_sort','time');
            $needapr_direc=$this->input->post('needapr_direc','desc');
            $aproved_sort=$this->input->post('approved_sort','time');
            $aproved_direc=$this->input->post('aproved_direc','desc');
            $aproved_viewall=$this->input->post('aproved_viewall');
            /* Get data */
            $this->load->model('artproof_model');

            $data_not_art=$this->artproof_model->get_tasks_stage('noart', $taskview, $inclreq, $nonart_sort, $nonart_direc);
            if (count($data_not_art)==0) {
                $mdata['nonart']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['nonart']=$this->load->view('tasklist/task_data_view',array('data'=>$data_not_art,'note'=>1),TRUE);
            }
            $data_redraw=$this->artproof_model->get_tasks_stage('redrawn', $taskview, $inclreq, $redraw_sort, $redraw_direc);
            if (count($data_redraw)==0) {
                $mdata['redrawn']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['redrawn']=$this->load->view('tasklist/task_data_view',array('data'=>$data_redraw,'note'=>0),TRUE);
            }
            $data_proof=$this->artproof_model->get_tasks_stage('need_proof', $taskview, $inclreq, $proof_sort, $proof_direc);
            if (count($data_proof)==0) {
                $mdata['toproof']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['toproof']=$this->load->view('tasklist/task_data_view',array('data'=>$data_proof,'note'=>0),TRUE);
            }
            $data_needapr=$this->artproof_model->get_tasks_stage('need_approve', $taskview, $inclreq, $needapr_sort, $needapr_direc);
            if (count($data_needapr)==0) {
                $mdata['needapr']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['needapr']=$this->load->view('tasklist/task_data_view',array('data'=>$data_needapr,'note'=>1),TRUE);
            }
            $data_aproved=$this->artproof_model->get_tasks_stage('just_approved', $taskview, $inclreq, $aproved_sort, $aproved_direc, $aproved_viewall);
            if (count($data_aproved)==0) {
                $mdata['aproved']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['aproved']=$this->load->view('tasklist/task_data_view',array('data'=>$data_aproved,'note'=>0),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* LIST view of Orders List */
    public function order_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            $searchval=$this->input->post('search','');
            $add_filtr=$this->input->post('add_filtr');
            $filter=$this->input->post('filter');
            $search=array();
            if ($searchval) {
                $search['search']=$searchval;
            }
            if ($filter!='') {
                $search['artfiltr']=$filter;
            }
            if ($add_filtr!='') {
                $search['artadd_filtr']=$add_filtr;
            }
            $offset=$offset*$limit;
            /* Fetch data about prices */
            $this->load->model('orders_model');
            $ordersdat=$this->orders_model->get_general_orders($search,$order_by,$direct,$limit,$offset, $this->USR_ID);
            if (count($ordersdat)==0) {
                $content=$this->load->view('artorder/order_emptydat_view',array(),TRUE);
            } else {
                $data=array(
                    'orders'=>$ordersdat,
                );
                $content = $this->load->view('artorder/order_tabledat_view',$data, TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function proof_listdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $show_deleted=$this->input->post('show_deleted');
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','desc');
            $maxval=$this->input->post('maxval');
            $brand=$this->input->post('brand');
            $search_val=$this->input->post('search');
            $assign=$this->input->post('assign');
            $search=array();
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($search_val) {
                $search['search']=$search_val;
            }
            if ($brand) {
                $search['brand']=$brand;
            }
            if ($show_deleted==1) {
                $search['show_deleted']=1;
            }

            $ordoffset=$offset*$limit;
            $offset=$offset*$limit;

            if ($ordoffset>$maxval) {
                $ordnum = $maxval;
            } else {
                $ordnum = $maxval - $ordoffset;
            }
            $this->load->model('artproof_model');
            $email_dat=$this->artproof_model->get_artproofs($search,$order_by,$direct,$limit,$offset,$maxval);

            if (count($email_dat)==0) {
                $content = $this->load->view('artrequest/proofs_emptytabledat_view',array(), TRUE);
            } else {
                $data=array('email_dat'=>$email_dat);
                $content = $this->load->view('artrequest/proofs_tabledat_view',$data, TRUE);

            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata,$error);
        }
    }



    private function _prepare_task_view() {
        $datf=array();
        $datf['sort_need_art']='time';
        $datf['direc_needart']='desc';
        $datf['sort_redraw']='time';
        $datf['direc_redraw']='desc';
        $datf['sort_proof']='time';
        $datf['direc_proof']='desc';
        $datf['sort_needapr']='time';
        $datf['direc_needapr']='desc';
        $datf['sort_aproved']='time';
        $datf['direc_aproved']='asc';
        $content=$this->load->view('tasklist/page_view',$datf,TRUE);
        return $content;
    }

    private function _prepare_orderlist_view() {
        $datqs=array();
        $datqs['perpage']=$this->artorderperpage;
        $search=array('hideart'=>0);
        $this->load->model('orders_model');
        $datqs['total_rec']=$this->orders_model->get_count_orders($search);
        $datqs['order_by']='order_num';
        $datqs['direction']='desc';
        $datqs['cur_page']=0;
        $datqs['assign']='';
        $datqs['hideart']=0;

/*        $options=array(
            'hideart'=>1,
        );
        $this->load->model('orders_model');
        $totals=$this->orders_model->get_count_orders($options);

        $options_view=array(
            'perpage'=> $this->artorderperpage,
            'order'=>'order_num',
            'direc'=>'desc',
            'total'=>$totals,
            'curpage'=>0,
        );*/
        $content=$this->load->view('artorder/page_view',$datqs,TRUE);
        return $content;
    }

    private function _prepare_requestlist_view() {
        $datqs=array();
        $datqs['perpage']=$this->artorderperpage;
        $search=array('assign'=>'','hideart'=>0);
        $this->load->model('artproof_model');
        $datqs['total_rec']=$this->artproof_model->get_count_proofs($search);
        $datqs['order_by']='email_date';
        $datqs['direction']='desc';
        $datqs['cur_page']=0;
        $datqs['assign']='';
        $datqs['hideart']=0;
        $content=$this->load->view('artrequest/page_view',$datqs,TRUE);
        return $content;
    }
}