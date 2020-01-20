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

    public function get_itemseq_vendors() {
        $vend=[
            'Stressballs.com','Ariel','Alpi','Mailine','Pinnacle','Jetline','Hit',
        ];
        $out=[];
        foreach ($vend as $vrow) {
            $this->db->select('*');
            $this->db->from('vendors');
            $this->db->where('vendor_name',$vrow);
            $vres=$this->db->get()->row_array();
            $out[]=$vres;
        }
        $out[]=[
            'vendor_id' => -1,
            'vendor_name' => '----------------------',
        ];
        $this->db->select('*');
        $this->db->from('vendors');
        $this->db->where_not_in('vendor_name', $vend);
        $this->db->order_by('vendor_name');
        $otherres = $this->db->get()->result_array();
        foreach ($otherres as $row) {
            $out[]=$row;
        }
        return $out;
    }

    public function get_inventory_list() {
        $this->db->select('*');
        $this->db->from('ts_printshop_items');
        $this->db->order_by('item_num');
        $res=$this->db->get()->result_array();
        // Get data
        $out=array();
        foreach ($res as $row) {
            $out[]=array(
                'printshop_item_id'=>$row['printshop_item_id'],
                'item_name'=>$row['item_num'].' '.trim(str_replace('Stress Balls', '', $row['item_name'])),
            );
        }
        return $out;
    }

}
