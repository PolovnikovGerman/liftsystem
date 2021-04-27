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
                    if ($res['entity']=='item' && ($res['fld']=='item_sale' || $res['fld']=='item_new')) {
                        if ($res['newval']==0) {
                            $mdata['newcheck'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                        } else {
                            $mdata['newcheck'] = '<i class="fa fa-square" aria-hidden="true"></i>';
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
                        $mdata['vendor_item_number']=$vendor['vendor_item_number'];
                        $mdata['vendor_item_name']=$vendor['vendor_item_name'];
                        $mdata['vendor_item_cost']=$vendor['vendor_item_cost'];
                        $mdata['vendor_item_exprint']=$vendor['vendor_item_exprint'];
                        $mdata['vendor_item_setup']=$vendor['vendor_item_setup'];
                        $mdata['vendor_item_notes']=$vendor['vendor_item_notes'];
                        $mdata['vendor_name']=$vendor['vendor_name'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}