<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Vendors_model extends My_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_vendors($options=array()) {
        $this->db->select('*',FALSE);
        $this->db->from('vendors');
        foreach ($options as $key=>$value) {
            $this->db->where($key,$value);
        }
        $this->db->order_by('vendor_name');
        $result=$this->db->get()->result_array();
        return $result;
    }

}
