<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function get_inventory_types() {
        $this->db->select('*');
        $this->db->from('ts_inventory_types');
        $this->db->order_by('type_order');
        return $this->db->get()->result_array();
    }
}