<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Masterinventory extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_inventory_list() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $inventory_type = ifset($postdata,'inventory_type',0);
            $inventory_filter = ifset($postdata,'inventory_filter',0);
            $mdata=[];
            $this->load->model('inventory_model');
            $data = $this->inventory_model->get_masterinvent_list($inventory_type, $inventory_filter);
            $mdata['content']=$this->load->view('masterinvent/inventorylist_data_view',['lists' => $data['list']],TRUE);
            $mdata['instock'] = empty($data['type_instock']) ? '' : QTYOutput($data['type_instock']);
            $mdata['available'] = empty($data['type_available']) ? '' : QTYOutput($data['type_available']);
            $this->ajaxResponse($mdata,'');
        }
        show_404();
    }

}