<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Itemcolors_model extends My_Model
{

    private $error_message = 'Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

    public function get_colors_item($item_id, $edit=0) {
        $this->db->select('ci.*');
        $this->db->from('sb_item_colors ci');
        $this->db->where('ci.item_color_itemid',$item_id);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_invent_itemcolors($item_id, $edit=0) {
        if ($edit==0) {
            $this->db->select('ci.*, c.color, c.color_image, c.color_order');
            $this->db->from('sb_item_colors ci');
            $this->db->join('ts_inventory_colors c','c.inventory_color_id=ci.printshop_color_id');
            $this->db->where('ci.item_color_itemid', $item_id);
            $this->db->where('ci.item_color != ','');
            $result = $this->db->get()->result_array();
        } else {
            $this->db->select('ci.*, c.color, c.color_image, c.color_order, c.inventory_color_id');
            $this->db->from('ts_inventory_colors c');
            $this->db->join('sb_item_colors ci','c.inventory_color_id=ci.printshop_color_id','left');
            $this->db->where('c.inventory_item_id',$item_id);
            $colors = $this->db->get()->result_array();
            $result = [];
            $numpp=1;
            foreach ($colors as $color) {
                $result[] = [
                    'item_color_id' => (empty($color['item_color_id']) ? $numpp*(-1) : $color['item_color_id']),
                    'item_color' => $color['item_color'],
                    'color_image' => $color['item_color_image'],
                    'color_order' => $color['color_order'],
                    'printshop_color_id' => $color['inventory_color_id'],
                    'color' => $color['color'],
                ];
                $numpp++;
            }
        }
        return $result;
    }

    function get_colorval_item($itemcolor_id) {
        $this->db->select('item_color_id, item_color');
        $this->db->from('sb_item_colors');
        $this->db->where('item_color_id',$itemcolor_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_color_id'])) {
            $result='';
        } else {
            $result=$res['item_color'];
        }
        return $result;
    }

    /* Get Colors from Inventory */
    public function get_inventcolors_item($printshop_inventory_id) {
        $this->db->select('*');
        $this->db->from('ts_printshop_colors');
        $this->db->where('printshop_item_id', $printshop_inventory_id);
        // $this->db->order_by('color');
        $this->db->order_by('color_order');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            // Calc inStock
            $this->db->select('sum(instock_amnt) as amnt');
            $this->db->from('v_printshop_instock');
            $this->db->where('printshop_color_id', $row['printshop_color_id']);
            $stokdat=$this->db->get()->row_array();
            $out[]=array(
                'printshop_color_id'=>$row['printshop_color_id'],
                'color'=>$row['color'],
                'instock'=>QTYOutput(intval($stokdat['amnt'])),
                'onroutestock'=>QTYOutput($row['onroutestock']),
            );
        }
        return $out;
    }

}