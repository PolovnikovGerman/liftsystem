<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Items_model extends My_Model
{
    private $Inventory_Source='Stock';


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

    public function update_imprint_update($data) {
        $out=['result'=>$this->error_result, 'msg'=>'Not All parameters sended'];
        if (isset($data['item_id']) && isset($data['imprint_update'])) {
            $this->db->where('item_id', $data['item_id']);
            $this->db->set('imprint_update', $data['imprint_update']);
            $this->db->update('sb_items');
            $out['result']= $this->success_result;
        }
        return $out;
    }

    function get_item($item_id) {
        $out=['result'=>$this->error_result,'msg'=>'Item Nit Found'];
        $this->db->select('i.* ');
        $this->db->from('sb_items i');
        $this->db->where('i.item_id',$item_id);
        $result = $this->db->get()->row_array();
        if (ifset($result,'item_id',0)>0) {
            if ($result['cartoon_qty']!='' && $result['cartoon_width']!='' && $result['cartoon_heigh']!='' && $result['cartoon_depth']!='' && $result['item_weigth']!='') {
                $result['shipping_info']='';
            } else {
                $result['shipping_info']='Information Missing';
            }
            foreach ($this->config->item('item_specialchars') as $row) {
                $result[$row]=  htmlspecialchars($result[$row]);
            }
            $out['result']=$this->success_result;
            $out['data']=$result;
        }
        return $out;
    }

    public function get_commonterms_item($item_id,$max_val=0) {
        $this->db->select('*');
        $this->db->from('sb_item_commonterms');
        $this->db->where('item_id',$item_id);
        $result=$this->db->get()->result_array();
        if ($max_val && count($result)<$max_val) {
            $cnt=count($result);
            for ($i=$cnt;$i<$max_val;$i++) {
                $result[]=array('term_id'=>0, 'item_id'=>$item_id,'common_term'=>'');
            }
        }
        return $result;
    }

    public function get_sequence_count($options=[]) {
        $this->db->select('count(i.item_id) as cnt');
        $this->db->from('sb_items i');
        $this->db->where('item_active',1);
        if (isset($options['vendor_id'])) {
            $this->db->join('sb_vendor_items iv','iv.vendor_item_id=i.vendor_item_id');
            $this->db->join("vendors v", 'v.vendor_id=iv.vendor_item_vendor');
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
            $this->db->join("vendors v", 'v.vendor_id=iv.vendor_item_vendor');
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

    function get_missinginfo($options=array(),$order='item_name',$direc='asc',$limit=0,$offset=0,$search='', $vendor_id='') {
        $this->db->select('v.*,(v.size+v.weigth+v.material+v.lead_a+v.lead_b+v.lead_c+v.colors+v.categories+v.images+v.prices) as missings',FALSE);
        $this->db->select('unix_timestamp(i.update_time) as updtime');
        $this->db->from('v_item_missinginfo v');
        $this->db->join('sb_items i','i.item_id=v.item_id');
        foreach ($options as $key=>$value) {
            $this->db->where($key,$value);
        }

        if ($vendor_id) {
            $this->db->join('sb_vendor_items vi','vi.vendor_item_id=i.vendor_item_id');
            $this->db->where('vi.vendor_item_vendor',$vendor_id);
        }

        if ($search!='') {
            $where="lower(concat(item_number,item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        $this->db->order_by($order,$direc);
        if ($limit) {
            $this->db->limit($limit,$offset);
        }
        $result=$this->db->get()->result_array();

        $out_array=array();
        $curtime=time();
        $diff=86400;
        foreach ($result as $row) {
            $row['itemnameclass']='';
            if ($curtime-$row['updtime']<$diff) {
                $row['itemnameclass']='nearlyupdate';
            }
            $missings=array();
            if ($row['colors']==1) {
                $missings[]=array('type'=>'colors');
            }
            if ($row['size']==1) {
                $missings[]=array('type'=>'size');
            }
            if ($row['images']==1) {
                $missings[]=array('type'=>'images');
            }
            if ($row['weigth']==1) {
                $missings[]=array('type'=>'weight');
            }
            if ($row['material']==1) {
                $missings[]=array('type'=>'material');
            }
            if ($row['lead_a']==1) {
                $missings[]=array('type'=>'lead_a');
            }
            if ($row['lead_b']==1) {
                $missings[]=array('type'=>'lead_b');
            }
            if ($row['lead_c']==1) {
                $missings[]=array('type'=>'lead_c');
            }
            if ($row['categories']==1) {
                $missings[]=array('type'=>'category');
            }
            if ($row['prices']==1) {
                $missings[]=array('type'=>'prices');
            }
            if ($row['item_keywords']==1) {
                $missings[]=array('type'=>'item kw');
            }
            if ($row['url']==1) {
                $missings[]=array('type'=>'url');
            }
            if ($row['meta_title']==1) {
                $missings[]=array('type'=>'meta title');
            }
            if ($row['meta_description']==1) {
                $missings[]=array('type'=>'descript');
            }
            if ($row['meta_keywords']==1) {
                $missings[]=array('type'=>'meta KW');
            }
            if ($row['attributes']==1) {
                $missings[]=array('type'=>'attributes');
            }

            $out_array[]=array('item_id'=>$row['item_id'],'item_number'=>$row['item_number'],'item_name'=>$row['item_name'],'missings'=>$missings, 'itemnameclass'=>$row['itemnameclass']);
        }
        return $out_array;
    }

    /* Get array of items */
    public function get_items($options=array(),$sort_by='item_id',$direct='asc',$limit=0,$offset=0,$search='',$vendor_id='') {
        $this->db->select('i.*, unix_timestamp(i.update_time) as updtime',FALSE);
        $this->db->from('sb_items i');
        foreach ($options as $key=>$val) {
            $this->db->where($key,$val);
        }
        if ($search!='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        if ($vendor_id) {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
            $this->db->where('v.vendor_item_vendor',$vendor_id);
        }
        $this->db->order_by($sort_by,$direct);
        if ($limit) {
            $this->db->limit($limit,$offset);
        }
        $result = $this->db->get()->result_array();
        $out_array=array();
        $curtime=time();
        $diff=86400;
        foreach ($result as $row) {
            $row['itemnameclass']='';
            if ($curtime-$row['updtime']<$diff) {
                $row['itemnameclass']='nearlyupdate';
            }
            $row['update_template_class']=$row['update_template']==1 ? 'updated' : '';
            $row['update_imprint_class']=$row['imprint_update']>0 ? ($row['imprint_update']==1 ? 'partialupdate' : 'updated') : 'empty';
            $out_array[]=$row;

        }
        return $out_array;
    }

}
