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

    public function edit_specialcheck() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->session_error;
            $mdata = [];
            $session_id = ifset($postdata, 'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $error = '';
                $item=$session_data['item'];
                $special_prices = $session_data['special_prices'];
                $priceview=$this->load->view('itemdetails/specialcheck_priceedit_view',['prices' => $special_prices], TRUE);
                $special_session_id = 'spec'.uniq_link();
                $options=array(
                    'session_id' => $special_session_id,
                    'item_name'=>$item['item_name'],
                    'special_checkout'=>$item['special_checkout'],
                    'special_shipping'=>$item['special_shipping'],
                    'special_setup'=>$item['special_setup'],
                    'prices'=>$priceview,
                );
                $mdata['content']=$this->load->view('itemdetails/specialcheckedit_view',$options,TRUE);
                $vendor_prices=$session_data['vendor_prices'];
                usersession($session_id, $session_data);
                $vend_prices = $session_data['vendor_prices'];
                $vendor = $session_data['vendor'];
                $vendor_prices = [];
                $vendor_prices[]=[
                    'vendorprice_qty' => 0,
                    'vendorprice_val' => $vendor['vendor_item_blankcost'],
                    'vendorprice_color' => $vendor['vendor_item_cost'],
                ];
                foreach ($vend_prices as $vrow) {
                    $vendor_prices[]=$vrow;
                }
                $special_session = [
                    'item' => $item,
                    'prices' => $special_prices,
                    'vendor_prices' => $vendor_prices,
                ];
                usersession($special_session_id, $special_session);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_specialcheck_parameter() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->session_error;
            $mdata = [];
            $session_id = ifset($postdata, 'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->change_specialcheck_parameter($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($res['type']=='price') {
                        $mdata['amount']=$res['amount'];
                        $mdata['profit']=$res['profit'];
                        $mdata['profit_percent']=$res['profit_percent'];
                        $mdata['profit_class']=$res['profit_class'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_specialcheckout() {
         if ($this->isAjax()) {
             $postdata=$this->input->post();
             $error=$this->session_error;
             $mdata=[];
             $session_id = ifset($postdata, 'session_id', 'defsess');
             $session_data = usersession($session_id);
             $specsession_id = ifset($postdata, 'specsession_id', 'specsess');
             $specsession_data = usersession($specsession_id);
             if (!empty($session_data) && !empty($specsession_data)) {
                 $this->load->model('itemdetails_model');
                 $res = $this->itemdetails_model->save_specialcheckout($session_data, $specsession_data, $session_id, $specsession_id);
                 $error = $res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error='';
                 }
             }
             $this->ajaxResponse($mdata,$error);
         }
         show_404();
    }

    public function edit_footer() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session_id', 'defsess');
            $session_data = usersession($session_id);
            $error = $this->session_error;
            $mdata=[];
            if (!empty($session_data)) {
                $error = '';
                $item = $session_data['item'];
                $mdata['content'] = $this->load->view('itemdetails/botttextedit_view', array('text' => $item['bottom_text']), TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function edit_shipping() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $mdata=[];
             $error = $this->session_error;
             $session_id = ifset($postdata, 'session_id','defsess');
             $session_data = usersession($session_id);
             if (!empty($session_data)) {
                 $error = '';
                 $item = $session_data['item'];
                 $mdata['content']=$this->load->view('itemdetails/shipping_edit_info', $item, TRUE);
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }

    public function save_shipping() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $session_id = ifset($postdata, 'session_id','defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->save_shipping($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function edit_commons() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $mdata=[];
             $error = $this->session_error;
             $session_id = ifset($postdata, 'session_id','defsess');
             $session_data = usersession($session_id);
             if (!empty($session_data)) {
                 $error='';
                 $commons = $session_data['commons'];
                 $common_session = 'common'.uniq_link(10);
                 usersession($common_session, ['commons'=>$commons]);
                 $options = [
                     'terms' => $commons,
                     'session' => $common_session,
                 ];
                 $mdata['content']=$this->load->view('itemdetails/commontermsedit_view', $options, TRUE);
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }

    public function change_commonterm() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $commonsession = ifset($postdata, 'commonsession','defsess');
            $commonsession_data = usersession($commonsession);
            if (!empty($commonsession_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->change_commonterm($postdata, $commonsession_data, $commonsession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_commonterms() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $commonsession = ifset($postdata, 'commonsession','defsess');
            $commonsession_data = usersession($commonsession);
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($commonsession_data) && !empty($session_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->save_commonterm($commonsession_data, $commonsession, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function del_imprintlocation() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $mdata=[];
             $error = $this->session_error;
             $session_id = ifset($postdata,'session_id', 'defsess');
             $session_data = usersession($session_id);
             if (!empty($session_data)) {
                 $this->load->model('itemdetails_model');
                 $res = $this->itemdetails_model->del_imprintlocation($postdata, $session_data, $session_id);
                 $error = $res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error='';
                     $mdata['content']=$this->load->view('itemdetails/imprintsedit_view',array('imprint'=>$res['imprints']),TRUE);
                 }
             }
             $this->ajaxResponse($mdata, $error);
         }
         show_404();
    }

    public function edit_imprintlocation() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->edit_imprintlocation($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $imprsession_id = 'imprint'.uniq_link(10);
                    usersession($imprsession_id, ['imprint'=>$res['imprint']]);
                    $options = [
                        'session' => $imprsession_id,
                        'imprint' => $res['imprint'],
                    ];
                    $mdata['content']=$this->load->view('itemdetails/imprintlocationedit_view', $options,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    function change_imprintlocation() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $imprsession = ifset($postdata,'imprsession', 'defsess');
            $imprsession_data = usersession($imprsession);
            if (!empty($imprsession_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->change_imprintlocation($postdata, $imprsession_data, $imprsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($res['newfld']=='item_inprint_view') {
                        $options = [
                            'item_inprint_view' => $res['imprintview_src'],
                        ];
                        $mdata['content']=$this->load->view('itemdetails/iteminprint_preview', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    function save_imprintlocation() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $imprsession = ifset($postdata, 'imprsession','defsess');
            $imprsession_data = usersession($imprsession);
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($imprsession_data) && !empty($session_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->itemdetails_model->save_imprint($imprsession_data, $imprsession, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content']=$this->load->view('itemdetails/imprintsedit_view',array('imprint'=>$res['imprints']),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save item data
    public function save_itemdetails() {
         if ($this->isAjax()) {
             $postdata = $this->input->post();
             $mdata = [];
             $error=$this->session_error;
             $session_id = ifset($postdata, 'session_id', 'defsess');
             $session_data = usersession($session_id);
             if (!empty($session_data)) {
                 $this->load->model('itemdetails_model');
                 $res = $this->itemdetails_model->save_itemdata($session_data, $session_id, $this->USR_ID, $this->USR_ROLE);
                 $error = $res['msg'];
                 if ($res['result']==$this->success_result) {
                     $error='';
                 }
             }
             $this->ajaxResponse($mdata,$error);
         }
         show_404();
    }

}