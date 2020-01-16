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

    public function get_sequence_count($options=[]) {
        $this->db->select('count(i.item_id) as cnt');
        $this->db->from('sb_items i');
        $this->db->where('item_active',1);
        if (isset($options['vendor_id'])) {
            $this->db->join('sb_vendor_items iv','iv.vendor_item_id=i.vendor_item_id');
            $this->db->join("{$this->vendor_db} v", 'v.vendor_id=iv.vendor_item_vendor');
            $this->db->where('v.vendor_id', $options['vendor_id']);
        }
        if (isset($options['search'])) {
            $this->db->like('upper(concat(i.item_number, i.item_name))', strtoupper($options['search']));
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_sequence_items($options) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_new, i.item_sequence, i.item_sale');
        $this->db->from('sb_items i');
        $this->db->where('i.item_active',1);
        if (isset($options['vendor_id'])) {
            $this->db->join('sb_vendor_items iv','iv.vendor_item_id=i.vendor_item_id');
            $this->db->join("{$this->vendor_db} v", 'v.vendor_id=iv.vendor_item_vendor');
            $this->db->where('v.vendor_id', $options['vendor_id']);
        }
        if (isset($options['search'])) {
            $this->db->like('upper(concat(i.item_number, i.item_name))', strtoupper($options['search']));
        }
        $this->db->order_by('i.item_sequence');
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        $res = $this->db->get()->result_array();
        $out = [];
        $this->load->model('itemimages_model');

        foreach ($res as $row) {
            $item_image_src = $this->itemimages_model->get_item_images($row['item_id'], 1);
            if (count($item_image_src)>0) {
                $item_image = $item_image_src[0]['item_img_name'];
            }
            $out[]=[
                'item_id' => $row['item_id'],
                'item_number' => $row['item_number'],
                'item_name' => $row['item_name'],
                'item_image' => $item_image,
                'item_new' => $row['item_new'],
                'item_sequence' => $row['item_sequence'],
                'item_sale' => $row['item_sale'],
            ];
        }
        return $out;
    }

    public function update_item_property($data) {
        $out=['result'=>$this->error_result, 'msg'=>'Not all parameters send'];
        if (array_key_exists('item_id',$data) && array_key_exists('newval',$data) && array_key_exists('property', $data)) {
            $out['result']=$this->success_result;
            $this->db->where('item_id', $data['item_id']);
            $this->db->set($data['property'], $data['newval']);
            $this->db->update('sb_items');
        }
        return $out;
    }

    public function update_item_sequence($data) {
        $out=['result'=>$this->error_result, 'msg'=>'Not all parameters send'];
        if (array_key_exists('item_id',$data) && array_key_exists('newval',$data)) {
            $out['result']=$this->success_result;
            $newseq = intval($data['newval']);
            $this->db->select('item_id, item_sequence, item_name, item_number');
            $this->db->from('sb_items');
            $this->db->where('item_id != ', $data['item_id']);
            $this->db->where('item_active',1);
            $this->db->order_by('item_sequence');
            $res=$this->db->get()->result_array();
            $maxseq = count($res);
            $newseq=($newseq==0 ? 1 : ($newseq>$maxseq ? $maxseq : $newseq));
            $numpp=1;
            foreach ($res as $row) {
                if ($numpp == $newseq) {
                    $this->db->where('item_id', $data['item_id']);
                    $this->db->set('item_sequence', $newseq);
                    $this->db->update('sb_items');
                    $numpp++;
                }
                if ($numpp != $row['item_sequence']) {
                    $this->db->where('item_id', $row['item_id']);
                    $this->db->set('item_sequence', $numpp);
                    $this->db->update('sb_items');
                }
                $numpp++;
            }
        }
        return $out;
    }

    public function update_itemsequence_sort($data) {
        $offset = 0;
        foreach ($data as $key=>$val) {
            if ($key=='offset') {
                $offset=$val;
            }
        }
        $start = ($offset * 80) +1;
        foreach ($data as $key=> $val) {
            if ($key !== 'offset') {
                $this->db->where('item_id', $val);
                $this->db->set('item_sequence', $start);
                $this->db->update('sb_items');
                $start++;
            }
        }
    }

}
