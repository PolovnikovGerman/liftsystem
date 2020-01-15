<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Items_model extends My_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function count_searchres($search, $vendor_id='') {
        $this->db->select('count(i.item_id) as cnt',FALSE);
        $this->db->from('sb_items i');
        $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($search)."%'";
        if ($vendor_id) {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.item_id');
            $this->db->where('v.vendor_item_vendor',$vendor_id);
        }
        $this->db->where($where);
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

}
