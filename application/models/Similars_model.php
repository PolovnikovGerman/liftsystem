<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Similars_model extends My_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_similar_items($item_id, $brand='BT') {
        $this->db->select('si.item_similar_id as item_similar_id,si.item_similar_similar as item_similar_similar');
        $this->db->select('i.item_number as item_number, i.item_name as item_name, i.item_template as item_template');
        $this->db->from('sb_item_similars si');
        $this->db->join('sb_items i','i.item_id=si.item_similar_similar');
        $this->db->where('si.item_similar_item',$item_id);
        if ($brand=='SR') {
            $this->db->limit($this->config->item('relievers_similar_items'));
        } else {
            $this->db->limit($this->config->item('similar_items'));
        }
        $results=$this->db->get()->result_array();
        $out_array=array();
        $maxitem = ($brand=='SR' ? $this->config->item('relievers_similar_items') : $this->config->item('similar_items'));
        for ($i=0;$i < $maxitem; $i++) {
            if (isset($results[$i]['item_similar_id'])) {
                $out_array[]=array(
                    'item_similar_id'=>$results[$i]['item_similar_id'],
                    'item_similar_similar'=>$results[$i]['item_similar_similar'],
                    'item_number'=>$results[$i]['item_number'],
                    'item_name'=>$results[$i]['item_name'],
                    'item_template'=>$results[$i]['item_template'],
                );
            } else {
                $out_array[]=array(
                    'item_similar_id'=>'',
                    'item_similar_similar'=>'',
                    'item_number'=>'',
                    'item_name'=>'',
                    'item_template'=>'',
                );
            }
        }
        return $out_array;
    }

//    function search_items($item_name) {
//        $this->db->select("concat(item_number,'/',item_name) as label,item_id as id",FALSE);
//        $this->db->from('sb_items');
//        $this->db->like('upper(concat(item_number,item_name))',  strtoupper($item_name));
//        $this->db->order_by('item_number');
//        $result=$this->db->get()->result_array();
//        return $result;
//
//    }
//
//    function update_similar($item_similar,$item_id) {
//        foreach ($item_similar as $row) {
//            if ($row['item_similar_similar']!='') {
//                $this->db->set('item_similar_similar',$row['item_similar_similar']);
//                if ($row['id']=='') {
//                    /* Insert data */
//                    $this->db->set('item_similar_item',$item_id);
//                    $this->db->insert('sb_item_similars');
//                } else {
//                    $this->db->where('item_similar_id',$row['id']);
//                    $this->db->update('sb_item_similars');
//                }
//            }
//        }
//    }
//
//    function get_item_similar($item_id) {
//        $this->db->select('si.item_similar_id as item_similar_id,si.item_similar_similar as item_similar_similar,
//            i.item_number as item_number, i.item_name as item_name, i.item_url as item_url,i.item_template as item_template');
//        $this->db->from('sb_item_similars si');
//        $this->db->join('sb_items i','i.item_id=si.item_similar_similar');
//        $this->db->where('si.item_similar_item',$item_id);
//        // add check active
//        $this->db->where('i.item_active',1);
//        $this->db->limit(3);
//        $results=$this->db->get()->result_array();
//        $simular_array=array();
//
//        foreach ($results as $row) {
//            $this->db->select('ip.item_img_name as item_image',FALSE);
//            $this->db->from('sb_item_images ip');
//            $this->db->where('ip.item_img_item_id',$row['item_similar_similar']);
//            $this->db->where('ip.item_img_order',1);
//            $res=$this->db->get()->row_array();
//            if (!isset($res['item_image'])) {
//                $row['item_image']='/img/no-camera.png';
//            } else {
//                $row['item_image']=$res['item_image'];
//            }
//            $simular_array[]=$row;
//        }
//
//        return $simular_array;
//    }

}