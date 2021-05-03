<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbitemdetails extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dbitemdetails_model');
    }

    // Edit parameter
    public function change_parameter() {
        if ($this->isAjax()) {
            $postdata=$this->input->post();
            $error=$this->session_error;
            $mdata=[];
            $session_id=ifset($postdata, 'session_id','defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->dbitemdetails_model->change_parameter($session_data, $postdata, $session_id);
                $error=$res['msg'];
                $mdata['oldvalue'] = $res['oldvalue'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($res['entity']=='item') {
                        if ($res['fld']=='item_sale' || $res['fld']=='item_new') {
                            if ($res['newval']==0) {
                                $mdata['newcheck'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                            } else {
                                $mdata['newcheck'] = '<i class="fa fa-square" aria-hidden="true"></i>';
                            }
                        }  elseif ($res['fld']=='sellblank' || $res['fld']=='sellcolor' || $res['fld']=='sellcolors') {
                            if ($res['newval']==0) {
                                $mdata['newcheck'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                            } else {
                                $mdata['newcheck'] = '<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                            }
                        } elseif ($res['fld']=='printlocat_example_img') {
                            $session_data = usersession($session_id);
                            $mdata['content'] = $this->load->view('dbitemdetails/advimage_edit', ['item' => $session_data['item']], TRUE);
                        }
                    }
//                    if ($res['entity']=='item_images') {
//                        // Build new slider
//                        $img_options=array(
//                            'images'=>$res['images'],
//                            'pos'=>0,
//                            'edit'=>0,
//                            'limit'=>$this->config->item('slider_images'),
//                            'video'=> '', // $video,
//                            'audio'=> '', // $audio,
//                            'faces'=> '', // $faces,
//                        );
//                        $mdata['content']=$this->load->view('itemdetails/pictures_slider_view',$img_options,TRUE);
//                    } elseif ($res['entity']=='item_prices') {
//                        $profit = $res['profit'];
//                        $mdata['profitdat'] = $this->load->view('itemdetails/stressball_profit_view', array('prices' => $profit, 'price_types' => $this->config->item('price_types')), TRUE);
//                        $mdata['research']=$res['research'];
//                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function searchvendor() {
        $vend_name=$this->input->get('q');
        $this->load->model('vendors_model');
        $get_dat=$this->vendors_model->search_vendors($vend_name);
        echo json_encode($get_dat);
    }

    public function vendor_check() {
        if ($this->isAjax()) {
            $postdata=$this->input->post();
            $error=$this->session_error;
            $mdata=[];
            $session_id=ifset($postdata, 'session_id','defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('itemdetails_model');
                $res = $this->dbitemdetails_model->check_vendor($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['showvendor']=$res['newvendor'];
                    $session_data = usersession($session_id);
                    $item = $session_data['item'];
                    $vendor = $session_data['vendor_item'];
                    $vendor_price = $session_data['vendor_price'];
                    $mdata['vendor_id']=$vendor['vendor_item_vendor'];
                    if ($res['newvendor']==1) {
                        // Vendor data
                        $vendor_options = [
                            'vendor_item' => $vendor,
                            'vendor_price' => $vendor_price,
                            'item' => $item,
                            'editmode' => 1,
                        ];
                        $mdata['vendor_view'] = $this->load->view('dbitemdetails/vendor_view', $vendor_options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function search_vendor_item() {
        $vend_it_num=$this->input->get('q');
        $vendor_id=$this->input->get('vendor_id');
        $this->load->model('vendors_model');
        $get_dat=$this->vendors_model->search_vendor_items($vend_it_num, $vendor_id);
        echo json_encode($get_dat);
    }

    public function vendoritem_check() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->dbitemdetails_model->check_vendor_item($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $session_data = usersession($session_id);
                    $item = $session_data['item'];
                    $vendor = $session_data['vendor_item'];
                    $vendor_price = $session_data['vendor_price'];
                    $mdata['vendor_id']=$vendor['vendor_item_vendor'];
                    // Vendor data
                    $vendor_options = [
                        'vendor_item' => $vendor,
                        'vendor_price' => $vendor_price,
                        'item' => $item,
                        'editmode' => 1,
                    ];
                    $mdata['vendor_view'] = $this->load->view('dbitemdetails/vendor_view', $vendor_options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function change_price() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->dbitemdetails_model->change_price($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $session_data = usersession($session_id);
                    $prices = $session_data['prices'];
                    $item = $session_data['item'];
                    $mdata['profit_view'] = $this->load->view('dbitemdetails/price_profit_view', ['prices' => $prices, 'item' => $item], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inprint_prepare() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->dbitemdetails_model->get_inprint_area($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $imprsession_id = 'imprint'.uniq_link(10);
                    usersession($imprsession_id, ['imprint'=>$res['inprint']]);
                    $options = [
                        'session' => $imprsession_id,
                        'imprint' => $res['inprint'],
                    ];
                    $mdata['content'] = $this->load->view('dbitemdetails/inprintlocation_add_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_imprintlocation() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $imprsession = ifset($postdata,'imprsession', 'defsess');
            $imprsession_data = usersession($imprsession);
            if (!empty($imprsession_data)) {
                $res = $this->dbitemdetails_model->change_imprintlocation($postdata, $imprsession_data, $imprsession);
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

    public function save_imprintlocation() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $imprsession = ifset($postdata, 'imprsession','defsess');
            $imprsession_data = usersession($imprsession);
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($imprsession_data) && !empty($session_data)) {
                $res = $this->dbitemdetails_model->save_imprint($imprsession_data, $imprsession, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content']=$this->load->view('dbitemdetails/inprintdata_view',array('inprints'=>$res['imprints'],'editmode' => 1),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    public function remove_inprint() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = $this->session_error;
            $session_id = ifset($postdata,'session_id', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->dbitemdetails_model->remove_inprint($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content'] = $this->load->view('dbitemdetails/inprintdata_view',['inprints' => $res['inprints'],'editmode' => 1,], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
}