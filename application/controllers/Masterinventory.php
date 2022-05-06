<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Masterinventory extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('inventory_model');
    }

    public function get_inventory_list() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = ifset($postdata,'inventory_filter',0);
            $showmax = ifset($postdata,'showmax', 0);
            $mdata=[];

            $data = $this->inventory_model->get_masterinvent_list($inventory_type, $inventory_filter);
            if (count($data['list'])==0) {
                $mdata['content']=$this->load->view('masterinvent/inventorylist_emptydata_view',[],TRUE);
            } else {
                $mdata['content']=$this->load->view('masterinvent/inventorylist_data_view',['lists' => $data['list'],'showmax' => $showmax],TRUE);
            }
            $mdata['instock'] = empty($data['type_instock']) ? '' : QTYOutput($data['type_instock']);
            $mdata['available'] = empty($data['type_available']) ? '' : QTYOutput($data['type_available']);
            $mdata['maximum'] = empty($data['type_maximum']) ? '' : QTYOutput($data['type_maximum']);
            $this->ajaxResponse($mdata,'');
        }
        show_404();
    }

    public function get_color_inventory() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $res = $this->inventory_model->get_masterinventory_color($coloritem);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['wintitle'] = $this->load->view('masterinvent/prices_head_view', $res['itemdata'],TRUE);
                    $options = [
                        'lists' => $res['lists'],
                        'totals' => $res['totals'],
                    ];
                    $mdata['winbody'] = $this->load->view('masterinvent/prices_body_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function get_color_history() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $coloritem = ifset($postdata,'itemcolor', 0);
            $mdata = [];
            $error = 'Non exist Item / Color';
            if (!empty($coloritem)) {
                $res = $this->inventory_model->get_masterinventory_colorhistory($coloritem);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['wintitle'] = $this->load->view('masterinvent/history_head_view', $res['itemdata'],TRUE);
                    $options = [
                        'lists' => $res['lists'],
                        'item' => $res['itemdata'],
                    ];
                    $mdata['winbody'] = $this->load->view('masterinvent/history_body_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

}