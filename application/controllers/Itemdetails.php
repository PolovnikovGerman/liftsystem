<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Itemdetails extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

     public function __construct()
    {
        parent::__construct();
    }

    public function view_footer() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $item_id = ifset($postdata, 'item_id',0);
             $error = 'Empty Item';
             $mdata=[];
             if ($item_id > 0) {
                 $this->load->model('items_model');
                 $res = $this->items_model->get_item($item_id);
                 $error = $res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error='';
                     $item = $res['data'];
                     if ($postdata['param']=='bottom_text') {
                         $mdata['content']=$this->load->view('itemdetails/botttextview_view',array('text'=>$item['bottom_text']),TRUE);
                     } else {
                         $data = $this->items_model->get_commonterms_item($item_id);
                         $mdata['content']=$this->load->view('itemdetails/commontermsview_view',array('terms'=>$data),TRUE);
                     }

                 }
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }

    public function view_specialcheck() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $mdata = [];
             $error = 'Empty Item';
             $item_id = ifset($postdata, 'item_id',0);
             if ($item_id > 0) {
                 $this->load->model('items_model');
                 $res = $this->items_model->get_item($item_id);
                 $error = $res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error = '';
                     $item=$res['data'];
                     $special_prices=$this->items_model->get_special_prices($item_id,0);
                     $priceview=$this->load->view('itemdetails/specialcheck_priceview_view',['prices' => $special_prices], TRUE);
                     $options=array(
                         'item_name'=>$item['item_name'],
                         'special_checkout'=>$item['special_checkout'],
                         'special_shipping'=>$item['special_shipping'],
                         'special_setup'=>$item['special_setup'],
                         'prices'=>$priceview,
                     );
                     $mdata['content']=$this->load->view('itemdetails/specialcheckview_view',$options,TRUE);
                 }
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }

    public function view_shipping() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $mdata=[];
             $error = 'Empty Item';
             $item_id = ifset($postdata, 'item_id',0);
             if ($item_id > 0) {
                 $this->load->model('items_model');
                 $res = $this->items_model->get_item($item_id);
                 $error = $res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error = '';
                     $item=$res['data'];
                     $mdata['content']=$this->load->view('itemdetails/shipping_view_info', $item, TRUE);
                 }
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }
    // Edit function
    public function change_parameter() {
         if ($this->isAjax()) {
             $postdata=$this->input->post();
             $error=$this->session_error;
             $mdata=[];
             $session_id=ifset($postdata, 'session_id','defsess');
             $session_data = usersession($session_id);
             if (!empty($session_data)) {
                 $this->load->model('itemdetails_model');
                 $res = $this->itemdetails_model->change_parameter($session_data, $postdata, $session_id);
                 $error=$res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error='';
                 }
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }

}