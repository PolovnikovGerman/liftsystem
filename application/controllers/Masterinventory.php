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
            $mdata=[];
            $this->load->model('inventory_model');
            $this->ajaxResponse($mdata);
        }
        show_404();
    }

}