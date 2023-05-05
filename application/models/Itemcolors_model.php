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
//        Temporary comment LEGACY
//         $this->db->order_by('item_color_order','asc');
        $result = $this->db->get()->result_array();
        return $result;
//        if ($edit==0) {
//            return $result;
//        } else {
//            $out_colors=array();
//            for ($i=0;$i<$this->config->item('item_colors');$i++) {
//                if (isset ($result[$i]['item_color_id'])) {
//                    $out_colors[$i]=array('item_color_id'=>$result[$i]['item_color_id'],'item_color'=>$result[$i]['item_color']);
//                } else {
//                    $out_colors[$i]=array('item_color_id'=>($i)*(-1),'item_color'=>'');
//                }
//            }
//            return $out_colors;
//        }
    }

//    function get_editcolors_item($item_id,$limit) {
//        $this->db->select('ci.*');
//        $this->db->from('sb_item_colors ci');
//        $this->db->where('ci.item_color_itemid',$item_id);
//        $result = $this->db->get()->result_array();
//        $out_colors=array();
//        for ($i=0;$i<$limit;$i++) {
//            if (isset ($result[$i]['item_color_id'])) {
//                $out_colors[$i]=array('item_color_id'=>$result[$i]['item_color_id'],'item_color'=>$result[$i]['item_color']);
//            } else {
//                $out_colors[$i]=array('item_color_id'=>($i)*(-1),'item_color'=>'');
//            }
//        }
//        return $out_colors;
//    }
//
//    function update_itemoptions($item_colors, $item_id) {
//        foreach ($item_colors as $row) {
//            if ($row['id']>0 && $row['color']=='') {
//                $this->db->where('item_color_id',$row['id']);
//                $this->db->delete('sb_item_colors');
//            } else{
//                $this->db->set('item_color',$row['color']);
//                if ($row['id']<=0 && $row['color']!='') {
//                    $this->db->set('item_color_itemid',$item_id);
//                    $this->db->insert('sb_item_colors');
//                } else {
//                    $this->db->where('item_color_id',$row['id']);
//                    $this->db->update('sb_item_colors');
//                }
//            }
//        }
//        return TRUE;
//    }
//
//    function update_inventoryoptions($item_id) {
//        $this->db->where('item_color_itemid',$item_id);
//        $this->db->delete('sb_item_colors');
//        return TRUE;
//    }
//
//    function update_colors($item_colors,$item_id) {
//        foreach ($item_colors as $row) {
//            if ($row['deed']=='delete') {
//                $this->db->where('item_color_id',$row['id']);
//                $this->db->delete('sb_item_colors');
//            } elseif($row['deed']=='insert') {
//                $this->db->set('item_color_itemid',$item_id);
//                $this->db->set('item_color',$row['newval']);
//                $this->db->insert('sb_item_colors');
//            } else {
//                $this->db->set('item_color',$row['newval']);
//                $this->db->where('item_color_id',$row['id']);
//                $this->db->update('sb_item_colors');
//            }
//        }
//    }

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

//    public function get_colorval_inventory($printshop_color_id) {
//        $this->_DB_BROWN = $this->load->database('brown', TRUE);
//        $this->_DB_BROWN->db_select();
//        $this->_DB_BROWN->select('*');
//        $this->_DB_BROWN->from('ts_printshop_colors');
//        $this->_DB_BROWN->where('printshop_color_id', $printshop_color_id);
//        $res=$this->_DB_BROWN->get()->row_array();
//        $color='';
//        if (isset($res['printshop_color_id'])) {
//            $color=$res['color'];
//        }
//        return $color;
//    }
//
//    function get_color_pantone($color_code) {
//        $pantone_name='';
//        foreach ($this->imprint_colors as $row) {
//            if (substr($row['code'],1)==$color_code) {
//                $pantone_name=$row['name'];
//                break;
//            }
//        }
//        if ($pantone_name=='') {
//            $pantone_name=$color_code;
//        }
//        return $pantone_name;
//    }
//
//    /* Save import values for options */
//    function import_option($options_arr,$options,$item_id) {
//        $this->db->set('options',$options);
//        $this->db->where('item_id',$item_id);
//        $this->update('sb_items');
//        $this->db->where('item_color_itemid',$item_id);
//        $this->db->delete('sb_item_colors');
//        foreach ($options_arr as $row) {
//            $this->db->set('item_color_itemid',$item_id);
//            $this->db->set('item_color',$row);
//            $this->db->insert('sb_item_colors');
//        }
//        return TRUE;
//    }
//
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