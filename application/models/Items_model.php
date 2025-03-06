<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Items_model extends My_Model
{
    private $Inventory_Source='Stock';
    protected $max_colors = 56;
    protected $max_images = 40;


    function __construct()
    {
        parent::__construct();
        ini_set('memory_limit', '-1');
    }

    public function count_searchres($search, $brand, $vendor_id='', $itemstatus = 0) {
        $this->db->select('count(i.item_id) as cnt',FALSE);
        $this->db->from('sb_items i');
        if ($vendor_id) {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
            $this->db->where('v.vendor_item_vendor',$vendor_id);
        }
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('i.brand', $brand);
            } else {
                $this->db->where_in('i.brand', ['SB','BT']);
            }
        }
        if (!empty($search)) {
            $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        if ($itemstatus!=0) {
            if ($itemstatus==1) {
                $this->db->where('i.item_active',1);
            } else {
                $this->db->where('i.item_active',0);
            }
        }
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

    function get_items_count($options=array()) {
        $this->db->select('count(item_id) as cnt',FALSE);
        $this->db->from('sb_items');
        foreach ($options as $key=>$value) {
            $this->db->where($key,$value);
        }
        $result = $this->db->get()->row_array();
        return $result['cnt'];
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
                $result[]=array('term_id'=>(-1)*($i+1), 'item_id'=>$item_id,'common_term'=>'');
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
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('i.brand', $options['brand']);
            } else {
                $this->db->where_in('i.brand', ['BT','SB']);
            }
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
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('i.brand', $options['brand']);
            } else {
                $this->db->where_in('i.brand', ['BT','SB']);
            }

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
            $item_image = '';
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

//    function get_missinginfo($options=array(),$order='item_name',$direc='asc',$limit=0,$offset=0,$search='', $vendor_id='') {
//        $this->db->select('v.*,(v.size+v.weigth+v.material+v.lead_a+v.lead_b+v.lead_c+v.colors+v.categories+v.images+v.prices) as missings',FALSE);
//        $this->db->select('unix_timestamp(i.update_time) as updtime');
//        $this->db->from('v_item_missinginfo v');
//        $this->db->join('sb_items i','i.item_id=v.item_id');
//        foreach ($options as $key=>$value) {
//            if ($key=='brand') {
//                if ($value!=='ALL') {
//                    if ($value=='SR') {
//                        $this->db->where('i.brand', $value);
//                    } else {
//                        $this->db->where_in('i.brand', ['SB','BT']);
//                    }
//                }
//            } else {
//                $this->db->where($key,$value);
//            }
//        }
//        if ($vendor_id) {
//            $this->db->join('sb_vendor_items vi','vi.vendor_item_id=i.vendor_item_id');
//            $this->db->where('vi.vendor_item_vendor',$vendor_id);
//        }
//
//        if ($search!='') {
//            $where="lower(concat(item_number,item_name)) like '%".strtolower($search)."%'";
//            $this->db->where($where);
//        }
//        $this->db->order_by($order,$direc);
//        if ($limit) {
//            $this->db->limit($limit,$offset);
//        }
//        $result=$this->db->get()->result_array();
//
//        $out_array=array();
//        $curtime=time();
//        $diff=86400;
//        foreach ($result as $row) {
//            $row['itemnameclass']='';
//            if ($curtime-$row['updtime']<$diff) {
//                $row['itemnameclass']='nearlyupdate';
//            }
//            $missings=array();
//            if ($row['colors']==1) {
//                $missings[]=array('type'=>'colors');
//            }
//            if ($row['size']==1) {
//                $missings[]=array('type'=>'size');
//            }
//            if ($row['images']==1) {
//                $missings[]=array('type'=>'images');
//            }
//            if ($row['weigth']==1) {
//                $missings[]=array('type'=>'weight');
//            }
//            if ($row['material']==1) {
//                $missings[]=array('type'=>'material');
//            }
//            if ($row['lead_a']==1) {
//                $missings[]=array('type'=>'lead_a');
//            }
//            if ($row['lead_b']==1) {
//                $missings[]=array('type'=>'lead_b');
//            }
//            if ($row['lead_c']==1) {
//                $missings[]=array('type'=>'lead_c');
//            }
//            if ($row['categories']==1) {
//                $missings[]=array('type'=>'category');
//            }
//            if ($row['prices']==1) {
//                $missings[]=array('type'=>'prices');
//            }
//            if ($row['item_keywords']==1) {
//                $missings[]=array('type'=>'item kw');
//            }
//            if ($row['url']==1) {
//                $missings[]=array('type'=>'url');
//            }
//            if ($row['meta_title']==1) {
//                $missings[]=array('type'=>'meta title');
//            }
//            if ($row['meta_description']==1) {
//                $missings[]=array('type'=>'descript');
//            }
//            if ($row['meta_keywords']==1) {
//                $missings[]=array('type'=>'meta KW');
//            }
//            if ($row['attributes']==1) {
//                $missings[]=array('type'=>'attributes');
//            }
//
//            $out_array[]=array('item_id'=>$row['item_id'],'item_number'=>$row['item_number'],'item_name'=>$row['item_name'],'missings'=>$missings, 'itemnameclass'=>$row['itemnameclass']);
//        }
//        return $out_array;
//    }

    /* Get array of items */
    public function get_items($options=array(),$sort_by='item_id',$direct='asc',$limit=0,$offset=0,$search='',$vendor_id='') {
        $this->db->select('i.*, unix_timestamp(i.update_time) as updtime',FALSE);
        $this->db->from('sb_items i');
        foreach ($options as $key=>$val) {
            if ($key=='brand') {
                if ($val!=='ALL') {
                    if ($val=='SR') {
                        $this->db->where('i.brand', $val);
                    } else {
                        $this->db->where_in('i.brand', ['SB','BT']);
                    }
                }
            } else {
                $this->db->where($key,$val);
            }
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

    public function get_special_prices($item_id, $edit=0) {
        $this->db->select('*, price_qty*price as amount',FALSE);
        $this->db->from('sb_item_specprices');
        $this->db->where('item_id',$item_id);
        $this->db->order_by('price_qty');
        $res=$this->db->get()->result_array();
        $out=array();
        $num_pp=1;
        foreach ($res as $row) {
            $profit=floatval($row['profit']);
            $row['profit_percent']='';
            $row['profit_class']='';
            if ($profit!=0 && floatval($row['price']!=0)) {
                $prof_perc=$profit/($row['price']*$row['price_qty'])*100;
                $row['profit_class']=profit_bgclass($prof_perc);
                $row['profit_percent']=round($prof_perc,0).'%';
            }
            if ($edit==0) {
                $row['price']=(floatval($row['price'])==0 ? '&nbsp;' : '$'.number_format($row['price'],2,'.',''));
                $row['amount']=(floatval($row['amount'])==0 ? '&nbsp;' : '$'.number_format($row['amount'],2,'.',''));
            }
            $out[]=$row;
            $num_pp++;
        }
        if ($edit==1) {
            for ($j=$num_pp; $j<=$this->config->item('specialcheckout_prices'); $j++) {
                $out[]=array(
                    'item_specprice_id'=>$j*(-1),
                    'price_qty'=>'',
                    'price'=>'',
                    'amount'=>'',
                    'profit'=>'',
                    'profit_percent'=>'',
                    'profit_class'=>'',
                );
            }
        }
        return $out;
    }

    public function get_item_list($item_id) {
        $this->db->select("item_id,concat(item_number,'-',item_name) as item_name",FALSE);
        $this->db->from('sb_items');
        $this->db->where('item_id !=',$item_id);
        $this->db->order_by('item_number');
        $out=$this->db->get()->result_array();
        return $out;
    }

    public function get_leaditems_data($options) {
        $pricelist=$options['prices'];
        $this->db->select('i.item_number, i.item_name, i.item_template, i.vendor_name, i.vendor_item_cost, i.vendor_id, v.vendor_zipcode');
        foreach ($pricelist as $prow) {
            $this->db->select("i.price_{$prow},i.profit_{$prow}_sum, i.profit_{$prow}, i.profit_{$prow}_class");
        }
        $this->db->select('i.price_setup, i.profit_setup, i.profit_setup_sum, i.profit_setup_class');
        $this->db->select("if(profit_25_class='empty',1,0)+if(profit_75_class='empty',1,0)+if(profit_150_class='empty',1,0)+if(profit_250_class='empty',1,0)+if(profit_500_class='empty',1,0)+if(profit_1000_class='empty',1,0)+if(profit_3000_class='empty',1,0)+if(profit_5000_class='empty',1,0)+if(profit_10000_class='empty',1,0)+if(profit_20000_class='empty',1,0)+if(profit_print_class='empty',1,0)+if(profit_setup_class='empty',1,0) as empty_sum",FALSE);
        $this->db->select("if(profit_25_class='black',1,0)+if(profit_75_class='black',1,0)+if(profit_150_class='black',1,0)+if(profit_250_class='black',1,0)+if(profit_500_class='black',1,0)+if(profit_1000_class='black',1,0)+if(profit_3000_class='black',1,0)+if(profit_5000_class='black',1,0)+if(profit_10000_class='black',1,0)+if(profit_20000_class='black',1,0)+if(profit_print_class='black',1,0)+if(profit_setup_class='black',1,0) as black_sum",FALSE);
        $this->db->select("if(profit_25_class='maroon',1,0)+if(profit_75_class='maroon',1,0)+if(profit_150_class='maroon',1,0)+if(profit_250_class='maroon',1,0)+if(profit_500_class='maroon',1,0)+if(profit_1000_class='maroon',1,0)+if(profit_3000_class='maroon',1,0)+if(profit_5000_class='maroon',1,0)+if(profit_10000_class='maroon',1,0)+if(profit_20000_class='maroon',1,0)+if(profit_print_class='maroon',1,0)+if(profit_setup_class='maroon',1,0) as maroon_sum,",FALSE);
        $this->db->select("if(profit_25_class='red',1,0)+if(profit_75_class='red',1,0)+if(profit_150_class='red',1,0)+if(profit_250_class='red',1,0)+if(profit_500_class='red',1,0)+if(profit_1000_class='red',1,0)+if(profit_3000_class='red',1,0)+if(profit_5000_class='red',1,0)+if(profit_10000_class='red',1,0)+if(profit_20000_class='red',1,0)+if(profit_print_class='red',1,0)+if(profit_setup_class='red',1,0) as red_sum",FALSE);
        $this->db->select("if(profit_25_class='orange',1,0)+if(profit_75_class='orange',1,0)+if(profit_150_class='orange',1,0)+if(profit_250_class='orange',1,0)+if(profit_500_class='orange',1,0)+if(profit_1000_class='orange',1,0)+if(profit_3000_class='orange',1,0)+if(profit_5000_class='orange',1,0)+if(profit_10000_class='orange',1,0)+if(profit_20000_class='orange',1,0)+if(profit_print_class='orange',1,0)+if(profit_setup_class='orange',1,0) as orange_sum",FALSE);
        $this->db->select("if(profit_25_class='white',1,0)+if(profit_75_class='white',1,0)+if(profit_150_class='white',1,0)+if(profit_250_class='white',1,0)+if(profit_500_class='white',1,0)+if(profit_1000_class='white',1,0)+if(profit_3000_class='white',1,0)+if(profit_5000_class='white',1,0)+if(profit_10000_class='white',1,0)+if(profit_20000_class='white',1,0)+if(profit_print_class='white',1,0)+if(profit_setup_class='white',1,0) as white_sum",FALSE);
        $this->db->select("if(profit_25_class='green',1,0)+if(profit_75_class='green',1,0)+if(profit_150_class='green',1,0)+if(profit_250_class='green',1,0)+if(profit_500_class='green',1,0)+if(profit_1000_class='green',1,0)+if(profit_3000_class='green',1,0)+if(profit_5000_class='green',1,0)+if(profit_10000_class='green',1,0)+if(profit_20000_class='green',1,0)+if(profit_print_class='green',1,0)+if(profit_setup_class='green',1,0) as green_sum",FALSE);
        $this->db->from("v_stressprofits i");
        $this->db->join('vendors v','v.vendor_id=i.vendor_id');
        if (isset($options['vendor_id'])) {
            $this->db->where('i.vendor_id', $options['vendor_id']);
        }
        if (isset($options['search'])) {
            $this->db->like('upper(concat(i.item_number, i.item_name))', $options['search'],'both');
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('sb_items itm','itm.item_id=i.item_id');
            if ($options['brand']=='SR') {
                $this->db->where('itm.brand', $options['brand']);
            } else {
                $this->db->where_in('itm.brand', ['BT','SB']);
            }
        }
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['priority'])) {
            switch($options['priority']) {
                case 'black' :
                    $this->db->order_by('black_sum','desc');
                    break;
                case 'maroon' :
                    $this->db->order_by('maroon_sum','desc');
                    break;
                case 'red' :
                    $this->db->order_by('red_sum','desc');
                    break;
                case 'orange' :
                    $this->db->order_by('orange_sum','desc');
                    break;
                case 'white' :
                    $this->db->order_by('white_sum','desc');
                    break;
                case 'green' :
                    $this->db->order_by('green_sum','desc');
                    break;
            }
        }
        if (isset($options['order_by'])) {
            $this->db->order_by($options['order_by']);
        }
        $this->db->order_by($options['order_by'],'asc');
        $res=$this->db->get()->result_array();
        $out=array();
        $numpp=(isset($options['offset']) ? $options['offset'] : 0);
        foreach ($res as $row) {
            $numpp++;
            $row['numpp']=$numpp;
            foreach ($pricelist as $prow) {
                $row['price_'.$prow]=($row['price_'.$prow]=='' ? 'n/a' : MoneyOutput($row['price_'.$prow]));
                $row['profit_'.$prow.'_sum']=($row['profit_'.$prow.'_class']=='empty' ? 'n/a' : round($row['profit_'.$prow.'_sum']*$this->config->item('profitpts'),0).'pts');
            }
            $row['price_setup']=($row['price_setup']=='' ? 'n/a' : MoneyOutput($row['price_setup']));
            $row['profit_setup_sum']=($row['profit_setup_class']=='empty' ? 'n/a' : round($row['profit_setup_sum']*$this->config->item('profitpts'),0).'pts');
            $out[]=$row;
        }
        return $out;
    }

    public function count_lead_items($options=array()) {
        $this->db->select('count(*) as cnt');
        $this->db->from("v_stressprofits");
        if (isset($options['vendor_id'])) {
            $this->db->where('vendor_id', $options['vendor_id']);
        }
        if (isset($options['search'])) {
            $this->db->like('upper(concat(item_number, item_name))', $options['search']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('sb_items i','i.item_id=v_stressprofits.item_id');
            if ($options['brand']=='SR') {
                $this->db->where('i.brand',$options['brand']);
            } else {
                $this->db->where_in('i.brand',['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_export_data($search) {
        $this->db->select('i.*,vi.vendor_item_number, vi.vendor_item_name, vi.vendor_item_cost, vi.vendor_item_exprint, vi.vendor_item_setup, v.vendor_name');
        $i=1;
        foreach ($this->config->item('price_types') as $row) {
            $this->db->select("{$row['type']} as qty_price{$i}",FALSE);
            $this->db->select("ip.item_price_{$row['type']} as price{$i}",FALSE);
            $this->db->select("ip.item_sale_{$row['type']} as sale_price{$i}",FALSE);
            $i++;
        }
        $this->db->select('ip.item_price_print as price_print , ip.item_price_setup as price_setup , ip.item_sale_print as sale_print , ip.item_sale_setup as sale_setup');
        $this->db->from('sb_items i');
        $this->db->join("sb_vendor_items vi","vi.vendor_item_id=i.vendor_item_id","left");
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor","left");
        $this->db->join('sb_item_prices ip','ip.item_price_itemid=i.item_id','left');
        /* Analyse search */
        if ($search['item_number']!='') {
            $this->db->like('i.item_number',$search['item_number']);
        }
        if ($search['item_name']!='') {
            $this->db->like('upper(i.item_name) ',$search['item_name']);
        }
        if ($search['item_template']!='') {
            $this->db->where('i.item_template ',$search['item_template']);
        }
        if ($search['item_new']!=''){
            $this->db->where('i.item_new',$search['item_new']);
        }
        if ($search['item_active']!=='') {
            $this->db->where('i.item_active',$search['item_active']);
        }
        if ($search['vendor_id']!='') {
            $this->db->where('v.vendor_id',$search['vendor_id']);
        }
        $this->db->order_by('i.item_number');
        $res=$this->db->get()->result_array();
        if (isset($search['print_ver']) && $search['print_ver']) {
            $out=array();
            foreach ($res as $row) {
                $row['item_new']=($row['item_new']==1 ? 'Yes' : 'No');
                $row['item_active']=($row['item_active']==1 ? 'Yes' : 'No');
                $row['item_lead_a']=(intval($row['item_lead_a'])==0 ? '' : intval($row['item_lead_a']));
                $row['item_lead_b']=(intval($row['item_lead_b'])==0 ? '' : intval($row['item_lead_b']));
                $row['item_lead_c']=(intval($row['item_lead_c'])==0 ? '' : intval($row['item_lead_c']));
                $row['charge_pereach']=(floatval($row['charge_pereach'])==0 ? '' : $row['charge_pereach']);
                $row['charge_perorder']=(floatval($row['charge_perorder'])==0 ? '' : $row['charge_perorder']);
                $row['vendor_item_cost']=(floatval($row['vendor_item_cost'])==0 ? '' : $row['vendor_item_cost']);
                $row['vendor_item_exprint']=(floatval($row['vendor_item_exprint'])==0 ? '' : $row['vendor_item_exprint']);
                $row['vendor_item_setup']=(floatval($row['vendor_item_setup'])==0 ? '' : $row['vendor_item_setup']);
                $row['price_print']=(floatval($row['price_print'])==0 ? '' : $row['price_print']);
                $row['price_setup']=(floatval($row['price_setup'])==0 ? '' : $row['price_setup']);
                $row['sale_print']=(floatval($row['sale_print'])==0 ? '' : $row['sale_print']);
                $row['sale_setup']=(floatval($row['sale_setup'])==0 ? '' : $row['sale_setup']);
                if ($row['item_template']!='Stressball') {
                    for ($i=1; $i<=10; $i++) {
                        $row['qty_price'.$i]='';
                        $row['price'.$i]='';
                        $row['sale_price'.$i]='';
                    }
                    /* Get Promo Prices */
                    $this->db->select('*');
                    $this->db->from('sb_promo_price');
                    $this->db->where('item_id',$row['item_id']);
                    $this->db->order_by('item_qty');
                    $price_dat=$this->db->get()->result_array();
                    $i=1;
                    foreach ($price_dat as $prow) {
                        $row['qty_price'.$i]=(intval($prow['item_qty'])==0 ? '' : $prow['item_qty']);
                        $row['price'.$i]=(floatval($prow['price'])==0 ? '' : floatval($prow['price']));
                        $row['sale_price'.$i]=(floatval($prow['sale_price'])==0 ? '' : floatval($prow['sale_price']));
                    }
                } else {
                    for ($i=1; $i<=10; $i++) {
                        $row['qty_price'.$i]=(intval($row['qty_price'.$i])==0 ? '' : $row['qty_price'.$i]);
                        $row['price'.$i]=(floatval($row['price'.$i])==0 ? '' : floatval($row['price'.$i]));
                        $row['sale_price'.$i]=(floatval($row['sale_price'.$i])==0 ? '' : floatval($row['sale_price'.$i]));
                    }
                }
                $out[]=$row;
            }
            $res=$out;
        }
        return $res;
    }

    public function get_export_description($options=array()) {
        $this->db->select('*');
        $this->db->from('sb_export_fields');
        if (count($options)>0) {
            $this->db->where_in('expfield_id',$options);
        }
        $this->db->order_by('expfield_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_itemlists($options) {
        $this->db->select('ic.item_categories_itemid as item_id, min(c.category_leftnavig) as category_name');
        $this->db->from('sb_item_categories ic');
        $this->db->join('sb_categories c','c.category_id=ic.item_categories_categoryid');
        $this->db->group_by('ic.item_categories_itemid');
        $category_qty = $this->db->get_compiled_select();
        $this->db->reset_query();

        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->select('v.vendor_name as vendor, v.vendor_code, v.vendor_phone, v.vendor_email, v.vendor_website, svi.vendor_item_number');
        $this->db->select('(vm.keyinfo+vm.prices+vm.printing+vm.meta+vm.shiping+vm.imagescolors+vm.supplier+vm.similar) as missings');
        $this->db->select('categ.category_name as category');
        $this->db->select('svi.vendor_item_vendor as vendor_id');
        $this->db->from('sb_items i');
        $this->db->join('sb_vendor_items svi','i.vendor_item_id = svi.vendor_item_id','left');
        $this->db->join('vendors v','v.vendor_id=svi.vendor_item_vendor','left');
        $this->db->join('v_sbitem_missinginfo vm','i.item_id=vm.item_id');
        $this->db->join("({$category_qty}) categ",'categ.item_id=i.item_id', 'left');
        if (ifset($options,'brand', 'ALL')!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('i.brand', $options['brand']);
            } else {
                $this->db->where_in('i.brand', ['BT','SB']);
            }
        }
        if (ifset($options, 'search', '')!=='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".$options['search']."%'";
            $this->db->where($where);
        }
        if (ifset($options,'vendor', '')!=='') {
            $this->db->where('v.vendor_id', $options['vendor']);
        }
        if (ifset($options, 'itemstatus', 0) > 0) {
            if ($options['itemstatus']==1) {
                $this->db->where('i.item_active', 1);
            } else {
                $this->db->where('i.item_active', 0);
            }
        }
        if (ifset($options,'category_id',0)>0) {
            $this->db->where('i.category_id', $options['category_id']);
        }
        if (ifset($options,'missinfo',0)>0) {
            if ($options['missinfo']==1) {
                $this->db->having('missings=0');
            } else {
                $this->db->having('missings > 0');
            }
        }
        $order_by = ifset($options, 'order_by','item_id');
        $direc = ifset($options, 'direct','asc');
        $this->db->order_by($order_by, $direc);
        $limit = ifset($options, 'limit', 0);
        $offset = ifset($options, 'offset', 0);
        if ($limit > 0) {
            if ($offset>0) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        $res = $this->db->get()->result_array();
        $out=[];
        $numpp = $offset + 1;
        foreach ($res as $item) {
            $item['misclas'] = ($item['missings']==0 ? '' : 'missing');
            $item['misinfo'] = ($item['missings']==0 ? 'Complete' : $item['missings'].' Missing');
            $item['misinfo_content'] = 'Complete';
            if ($item['missings']>0) {
                $this->db->select('*');
                $this->db->from('v_sbitem_missinginfo');
                $this->db->where('item_id', $item['item_id']);
                $misdata = $this->db->get()->row_array();
                $item['misinfo_content'] = $this->load->view('dbitems/missinfo_details_view', $misdata, TRUE);
            }
            $item['status'] = $item['item_active']==1 ? 'Active' : 'Inactve';
            $item['rowclass'] = $item['item_active']==1 ? '' : 'inactive';
            $item['numpp'] = $numpp;
            // Bluetrack
            $item['vendorclass'] = '';
            if ($item['vendor_id']==$this->config->item('inventory_vendor')) {
                $item['vendor']='INTERNAL';
                $item['vendorclass']='internal';
            }
            // if ($item['vendor_code']=='50000') {
            //}
            $numpp++;
            $out[] = $item;
        }
        return $out;
    }
    public function new_itemlist($brand) {
        $out=['result' => $this->success_result, 'msg' => ''];
        // Start
        $item = [
            'item_id' => -1,
            'item_number' => '',
            'item_numberone' => '',
            'item_numbersec' => '',
            'item_name' => '',
            'item_active' => 1,
            'item_new' => 0,
            'item_sale' => 0,
            'item_topsale' => 0,
            'item_template' => '',
            // 'item_template' => 'Stressballs',
            'item_lead_a' => 0,
            'item_lead_b' => 0,
            'item_lead_c' => 0,
            'item_material' => '',
            'subcategory_id' => '',
            'item_weigth' => 0,
            'item_size' => '',
            'item_keywords' => '',
            'item_url' => '',
            'item_meta_title' => '',
            'item_metadescription' => '',
            'item_metakeywords' => '',
            'item_description1' => '',
            'item_description2' => '',
            'item_vector_img' => '',
            'options' => '',
            'note_material' => '',
            'brand' => $brand,
            'printlocat_example_img' => '',
            'sellblank' => 0,
            'sellcolor' => 0,
            'sellcolors' => 0,
            'item_price_id' => -1,
            'item_price_print' => 0.00,
            'item_sale_print' => 0.00,
            'profit_print' => '',
            'item_price_setup' => 0.00,
            'item_sale_setup' => 0.00,
            'profit_setup' => '',
            'profit_print_class' => '',
            'profit_print_perc' => '',
            'profit_setup_class' => '',
            'profit_setup_perc' => '',
            'bullet1' => '',
            'bullet2' => '',
            'bullet3' => '',
            'bullet4' => '',
            'item_minqty' => '',
            'main_image' => '',
            // 'main_imgage_id' => '',
            'category_image' => '',
            // 'category_image_id' => '',
            'top_banner' => '',
            'option_images' => 0,
            'imprint_method' => '',
            'imprint_color' => '',
            'charge_pereach' => '',
            'charge_perorder' => '',
            // Price
            'item_price_id' => -1,
            'item_price_print' => '',
            'item_sale_print' => '',
            'profit_print' => '',
            'item_price_setup' => '',
            'item_sale_setup' => '',
            'profit_setup' => '',
            'profit_print_class' => '',
            'profit_print_perc' => '',
            'profit_setup_class' => '',
            'profit_setup_perc' => '',
            'profit_repeat_class' => '',
            'profit_repeat_perc' => '',
            'item_price_rush1' => '',
            'item_sale_rush1' => '',
            'profit_rush1' => 0,
            'profit_rush1_class' => '',
            'profit_rush1_perc' => '',
            'item_price_rush2' => '',
            'item_sale_rush2' => '',
            'profit_rush2' => '',
            'profit_rush2_class' => '',
            'profit_rush2_perc' => '',
            'item_price_pantone' => '',
            'item_sale_pantone' => '',
            'profit_pantone' => '',
            'profit_pantone_class' => '',
            'profit_pantone_perc' => '',
            'price_discount' => '',
            'price_discount_val' => '',
            'print_discount' => '',
            'print_discount_val' => '',
            'setup_discount' => '',
            'setup_discount_val' => '',
            'repeat_discount' => '',
            'repeat_discount_val' => '',
            'rush1_discount' => '',
            'rush1_discount_val' => '',
            'rush2_discount' => '',
            'rush2_discount_val' => '',
            'pantone_discount' => '',
            'pantone_discount_val' => '',
            'item_price_repeat' => '',
            'item_sale_repeat' => '',
            'profit_repeat' => '',
            'pantone_profit' => '',
        ];
        $vendor = [
            'vendor_id' => '',
            'vendor_name' => '',
            'vendor_zipcode' => '',
            'shipaddr_state' => '',
            'shipaddr_country' => '',
            'po_note' => '',
        ];
        $vitem=[
            'vendor_item_id' => -1,
            'vendor_item_vendor' => '',
            'vendor_item_number' => '',
            'vendor_item_name' => '',
            'vendor_item_blankcost' => '',
            'vendor_item_cost' => '',
            'vendor_item_exprint' => '',
            'vendor_item_setup' => '',
            'vendor_item_repeat' => '',
            'vendor_item_notes' => '',
            'vendor_item_zipcode' => '',
            'printshop_item_id' => '',
            'vendor_name' => '',
            'vendor_zipcode' => '',
            'stand_days' => '',
            'rush1_days' => '',
            'rush1_price' => '',
            'rush2_days' => '',
            'rush2_price' => '',
            'pantone_match' => '',
        ];
        $vprices = [];
        if ($brand=='SR') {
            $pricesmax = $this->config->item('relievers_prices_val');
        } else {
            $pricesmax = $this->config->item('prices_val');
        }
        for ($i=1; $i<=$pricesmax-1; $i++) {
            $vprices[] = [
                'vendorprice_id' => $i*-1,
                'vendor_item_id' => -1,
                'vendorprice_qty' => '',
                'vendorprice_val' => '',
                'vendorprice_color' => '',
            ];
        }
        $images = [];
//        for ($i=1; $i<=$this->config->item('slider_images'); $i++) {
//            if ($i==1) {
//                $title = 'Main Pic';
//            } else {
//                $title = 'Pic '.$i;
//            }
//
//            $images[] = [
//                'item_img_id' => $i * (-1),
//                'item_img_item_id' => -1,
//                'item_img_name' => '',
//                'item_img_thumb' => '',
//                'item_img_order' => '',
//                'item_img_big' => '',
//                'item_img_medium' => '',
//                'item_img_small' => '',
//                'item_img_label' => '',
//                'title' => $title,
//            ];
//        }
        $imprints = [];
        $prices = [];
        for ($i=1; $i<=$pricesmax; $i++) {
            $prices[] = [
                'promo_price_id' => $i * (-1),
                'item_id' => -1,
                'item_qty' => '',
                'price' => '',
                'sale_price' => '',
                'profit' => '',
                'show_first' => '0',
                'shipbox' => 0,
                'shipweight' => 0.000,
                'profit_class' => '',
                'profit_perc' => '',
            ];
        }
        $similar = [];
        if ($brand=='SR') {
            $maxnum = $this->config->item('relievers_similar_items');
        } else {
            $maxnum = $this->config->item('similar_items');
        }

        for ($i=1; $i<=$maxnum; $i++) {
            $similar[] = [
                'item_similar_id' => $i*(-1),
                'item_similar_similar' => '',
                'item_number' => '',
                'item_name' => '',
                'item_template' => '',
            ];
        }
        $colors = [];
//        for ($i=0; $i<$this->config->item('item_colors'); $i++) {
//            $idx = ($i + 1) * (-1);
//            $colors[] = [
//                'item_color_id' => $idx,
//                'item_color' => '',
//            ];
//        }
        $categor = [];
        for ($i=0; $i<3; $i++) {
            $categor[] = [
                'item_categories_id' => $i*(-1),
                'category_id' => '',
                'category_name' => '',
            ];
        }

        $shipboxes = [];
        for ($i=0; $i<3; $i++) {
            $shipboxes[] = [
                'item_shipping_id' => (-1)*($i+1),
                'box_qty' => '',
                'box_width' => '',
                'box_length' => '',
                'box_height' => '',
            ];
        }
        $data=[
            'item' => $item,
            'colors' => $colors,
            'vendor' => $vendor,
            'vendor_item' => $vitem,
            'vendor_price' => $vprices,
            'images' => $images,
            'option_images' => [],
            'inprints' => $imprints,
            'prices' => $prices,
            'similar' => $similar,
            'shipboxes' => $shipboxes,
            'categories' => $categor,
            'deleted' => [],
        ];
        // $out['data'] = $data;
        // return $out;
        return $data;
    }

    public function get_itemlist_details($item_id, $editmode = 0) {
        $out=['result' => $this->error_result, 'msg' => 'Item Not Found'];
        $this->db->select('i.*, c.category_name');
        $this->db->from('sb_items i');
        $this->db->join('sr_categories c','c.category_id=i.category_id');
        $this->db->where('item_id', $item_id);
        $item = $this->db->get()->row_array();
        if (ifset($item, 'item_id',0)==$item_id) {
            $out['result'] = $this->success_result;
            $item['printshop_item_num'] = $item['printshop_item_name'] = '';
            if (!empty($item['printshop_inventory_id'])) {
                $this->db->select('inventory_item_id, item_num, item_name');
                $this->db->from('ts_inventory_items');
                $this->db->where('inventory_item_id', $item['printshop_inventory_id']);
                $invres = $this->db->get()->row_array();
                if (ifset($invres,'inventory_item_id',0)==$item['printshop_inventory_id']) {
                    $item['printshop_item_num'] = $invres['item_num'];
                    $item['printshop_item_name'] = $invres['item_name'];
                }
            }
            // Missing Info
            $this->db->select('*');
            $this->db->from('v_sbitem_missinginfo');
            $this->db->where('item_id', $item_id);
            $misinfo = $this->db->get()->row_array();
            if (ifset($misinfo,'item_id',0)==0) {
                $misinfo = [
                    'item_id' => $item_id,
                    'keyinfo' => 0,
                    'similar' => 0,
                    'prices' => 0,
                    'printing' => 0,
                    'meta' => 0,
                    'shiping' => 0,
                    'imagescolors' => 0,
                    'supplier' => 0,
                ];
            }
            $this->load->model('itemimages_model');
            $this->load->model('vendors_model');
            $this->load->model('imprints_model');
            $this->load->model('prices_model');
            $this->load->model('similars_model');
            $this->load->model('itemcolors_model');
            $this->load->model('shipping_model');
            $this->load->model('categories_model');
            // Discounts
            $def_discount = 0;
            $this->db->select('ic.item_categories_id, ic.item_categories_categoryid as category_id, c.category_leftnavig as category_name');
            $this->db->from('sb_item_categories ic');
            $this->db->join('sb_categories c','ic.item_categories_categoryid = c.category_id');
            $this->db->where('ic.item_categories_itemid', $item_id);
            $this->db->limit(3);
            $categor = $this->db->get()->result_array();
            if (count($categor)<3) {
                $newid = count($categor) + 1;
                for ($i=count($categor); $i<3; $i++) {
                    $categor[] = [
                        'item_categories_id' => $newid*(-1),
                        'category_id' => '',
                        'category_name' => '',
                    ];
                    $newid++;
                }
            }
            $categories = $this->categories_model->get_categories_list();
            // Colors
            $colors = [];
            $numpp=1;
            if (empty($item['printshop_inventory_id'])) {
                $colorsrc = $this->itemcolors_model->get_colors_item($item_id, $editmode);
                foreach ($colorsrc as $itmcolor) {
                    $colors[] = [
                        'item_color_id' => $itmcolor['item_color_id'],
                        'item_color' => $itmcolor['item_color'],
                        'item_color_image' => $itmcolor['item_color_image'],
                        'item_color_order' => $numpp,
                    ];
                    $numpp++;
                }
            } else {
                if ($editmode==0) {
                    $colorsrc = $this->itemcolors_model->get_invent_itemcolors($item_id, $editmode);
                } else {
                    $colorsrc = $this->itemcolors_model->get_invent_itemcolors($item_id, $editmode);
                }
                foreach ($colorsrc as $itmcolor) {
                    $colors[] = [
                        'item_color_id' => $itmcolor['item_color_id'],
                        'item_color' => $itmcolor['item_color'],
                        'item_color_image' => $itmcolor['color_image'],
                        'item_color_order' => $itmcolor['color_order'],
                        'printshop_color' => $itmcolor['printshop_color_id'],
                        'item_color_source' => $itmcolor['color'],
                    ];
                }
            }
            // Vendor Info
            $pricesmax = $this->config->item('prices_val');
            $vitem = $this->vendors_model->get_item_vendor($item['vendor_item_id'], $item['printshop_inventory_id']);
            $vprices = [];
            if (ifset($vitem,'vendor_item_id', 0 )==0) {
                $vitem=[
                    'vendor_item_id' => -1,
                    'vendor_item_vendor' => '',
                    'vendor_item_number' => '',
                    'vendor_item_name' => '',
                    'vendor_item_blankcost' => 0,
                    'vendor_item_cost' => 0,
                    'vendor_item_exprint' => 0,
                    'vendor_item_setup' => 0,
                    'vendor_item_repeat' => 0,
                    'vendor_item_notes' => '',
                    'vendor_item_zipcode' => '',
                    'item_shipstate' => '',
                    'item_shipcountry' => '',
                    'item_shipcountry_name' => '',
                    'item_shipcity' => '',
                    'printshop_item_id' => '',
                    'stand_days' => '',
                    'rush1_days' => '',
                    'rush2_days' => '',
                    'rush1_price' => '',
                    'rush2_price' => '',
                    'pantone_match' => '',
                    'po_note' => '',
                    'vendor_name' => '',
                ];
                for ($i=1; $i<=$pricesmax-1; $i++) {
                    $vprices[] = [
                        'vendorprice_id' => $i*-1,
                        'vendor_item_id' => -1,
                        'vendorprice_qty' => '',
                        'vendorprice_val' => '',
                        'vendorprice_color' => '',
                    ];
                }
            } else {
                $results = $this->vendors_model->get_item_vendorprice($item['vendor_item_id']);
                $numpp = 1;
                foreach ($results as $result) {
                    $vprices[] = [
                        'vendorprice_id' => $result['vendorprice_id'],
                        'vendor_item_id' => $result['vendor_item_id'],
                        'vendorprice_qty' => $result['vendorprice_qty'],
                        'vendorprice_val' => $result['vendorprice_val'],
                        'vendorprice_color' => $result['vendorprice_color'],
                    ];
                    $numpp++;
                }
                for ($i=$numpp; $i<=$pricesmax-1; $i++) {
                    $vprices[] = [
                        'vendorprice_id' => $i*-1,
                        'vendor_item_id' => $vitem['vendor_item_id'],
                        'vendorprice_qty' => '',
                        'vendorprice_val' => '',
                        'vendorprice_color' => '',
                    ];
                }
            }
            $imagsrc = $this->itemimages_model->get_itemlist_images($item_id);
            $numpp = 1;
            $images = [];
            foreach ($imagsrc as $image) {
                if ($numpp==1) {
                    $title = 'Main Pic';
                } else {
                    $title = 'Pic '.$numpp;
                }
                $images[]=[
                    'item_img_id' => $image['item_img_id'],
                    'item_img_item_id' => $image['item_img_item_id'],
                    'item_img_name' => $image['item_img_name'],
                    'item_img_thumb' => $image['item_img_thumb'],
                    'item_img_order' => $numpp, // $image['item_img_order'],
                    'item_img_big' => $image['item_img_big'],
                    'item_img_medium' => $image['item_img_medium'],
                    'item_img_small' => $image['item_img_small'],
                    'item_img_label' => $image['item_img_label'],
                    'title' => $title,
                ];
                $numpp++;
                if ($numpp > $this->config->item('slider_images')) {
                    break;
                }
            }
            // Options images
            $imprints = $this->imprints_model->get_imprint_item($item_id);
            $priceres = $this->prices_model->get_itemlist_price($item_id);
            if (!empty($item['printshop_inventory_id'])) {
                $priceres = $this->_recalc_inventory_profit($priceres, $vitem['vendor_item_cost']);
            }
            $prices = [];
            $numpp = 1;
            foreach ($priceres as $price) {
                $profitperc = $profitclass = '';
                if (floatval($price['sale_price']) > 0 && $price['profit']!==NULL) {
                    $profitperc = round(($price['profit'] / ($price['sale_price']*$price['item_qty'])) * 100,1);
                    $profitclass = profit_bgclass($profitperc);
                }
                $prices[] = [
                    'promo_price_id' => $price['promo_price_id'],
                    'item_id' => $price['item_id'],
                    'item_qty' => $price['item_qty'],
                    'price' => $price['price'],
                    'sale_price' => $price['sale_price'],
                    'profit' => $price['profit'],
                    'show_first' => $price['show_first'],
                    'shipbox' => $price['shipbox'],
                    'shipweight' => $price['shipweight'],
                    'profit_class' => $profitclass,
                    'profit_perc' => $profitperc,
                ];
                $numpp++;
                if ($numpp > $pricesmax) {
                    break;
                }
            }
            if ($editmode==1) {
                if ($numpp <= $pricesmax) {
                    $idx = 1;
                    for ($i=$numpp; $i<=$pricesmax; $i++) {
                        $prices[] = [
                            'promo_price_id' => $idx * (-1),
                            'item_id' => $item_id,
                            'item_qty' => '',
                            'price' => '',
                            'sale_price' => '',
                            'profit' => '',
                            'show_first' => '0',
                            'shipbox' =>  '',
                            'shipweight' =>  '',
                            'profit_class' =>  '',
                            'profit_perc' =>  '',
                        ];
                        $idx++;
                    }
                }
            }
            // Special price - setup, print
            $specprice = $this->prices_model->get_itemlist_specprice($item_id);
            foreach ($specprice as $key => $val) {
                $item[$key] = $val;
            }
            $shipboxes = $this->shipping_model->get_itemshipbox($item_id, $editmode);
            // Simular
            $similar = $this->similars_model->get_similar_items($item_id, $item['brand']);
            // config
            $data=[
                'item' => $item,
                'categories' => $categor,
                'colors' => $colors,
                'vendor_item' => $vitem,
                'vendor_price' => $vprices,
                'images' => $images,
                'inprints' => $imprints,
                'prices' => $prices,
                'similar' => $similar,
                'shipboxes' => $shipboxes,
                'deleted' => [],
            ];
            $out['data'] = $data;
            $out['missinfo'] = $misinfo;
        }
        return $out;
    }

    public function get_item_mainimage($item_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>'Item Not Found');
        $this->db->select('item_id, main_image');
        $this->db->from('sb_items');
        $this->db->where('item_id', $item_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_id'])) {
            $out['msg']='Image Not Found';
            return $out;
        }
        $path_sh=$this->config->item('itemimages_relative');
        $path_fl=$this->config->item('itemimages');
        $source=$res['main_image'];
        $filesource=  str_replace($path_sh, $path_fl, $source);
        if (!file_exists($filesource)) {
            $out['msg']='Source File '.$filesource.' Not Found ';
            return $out;
        }
        $viewopt=array(
            'source'=>$source,
        );
        list($width, $height, $type, $attr) = getimagesize($filesource);
        // Rate
        if ($width >= $height) {
            if ($width<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$width;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        } else {
            if ($height<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$height;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        }
        $out['result']=$this->success_result;
        $out['viewoptions']=$viewopt;
        return $out;

    }

    public function count_item_searchres($options) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->from('sb_items i');
        if (ifset($options, 'vendor_id','')!=='') {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
            $this->db->where('v.vendor_item_vendor',$options['vendor_id']);
        }
        if ($options['brand']!=='ALL') {
            if ($options['brand'] == 'SR') {
                $this->db->where('i.brand', $options['brand']);
            } else {
                $this->db->where_in('i.brand', ['SB', 'BT']);
            }
        }
        if (ifset($options, 'search','')!=='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($options['search'])."%'";
            $this->db->where($where);
        }
        if (!empty($search)) {
            $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        if (ifset($options,'itemstatus',0)!=0) {
            if ($options['itemstatus']==1) {
                $this->db->where('i.item_active',1);
            } else {
                $this->db->where('i.item_active',0);
            }
        }
        if (ifset($options,'category',0) > 0) {
            $this->db->where('i.category_id',$options['category']);
        }
        if (ifset($options,'missinfo','0')!=0) {
            $this->db->select('(vm.keyinfo+vm.prices+vm.printing+vm.meta+vm.shiping+vm.imagescolors+vm.supplier+vm.similar) as missings');
            $this->db->join('v_sbitem_missinginfo vm','i.item_id=vm.item_id','left');
            if ($options['missinfo']==1) {
                $this->db->having('missings=0');
            } else {
                $this->db->having('missings>0');
            }
        }
        $res = $this->db->get()->result_array();
        return count($res);
    }

    public function new_btitem($data, $user_id) {
        $out=['result'=>$this->error_result, 'msg' => 'Item add fail'];
        $errflag = 0;
        $errmsg = '';
        if (empty($data['category'])) {
            $errmsg.='Empty Item Category'.PHP_EOL;
            $errflag = 1;
        }
        if (empty($data['subcategory'])) {
            $errmsg.='Empty Item SubCategory'.PHP_EOL;
            $errflag = 1;
        }
        if (empty($data['itemname'])) {
            $errmsg.='Empty Item Name'.PHP_EOL;
            $errflag = 1;
        }
        if ($errflag==1) {
            $out['msg'] = $errmsg;
        } else {
            // Construct item Number;
            $this->db->select('category_code');
            $this->db->from('sr_categories');
            $this->db->where('category_id', $data['category']);
            $catres = $this->db->get()->row_array();

            $numtempl = $catres['category_code'].'-';
            $this->db->select('code as category_code');
            $this->db->from('sb_subcategories');
            $this->db->where('subcategory_id', $data['subcategory']);
            $subres = $this->db->get()->row_array();
            $numtempl.=$subres['category_code'];
            $this->db->select('max(substr(item_number, 6)) as maxnum, count(item_id) as cnt');
            $this->db->from('sb_items');
            $this->db->like('item_number', $numtempl, 'after');
            $this->db->where_in('brand',['SB','BT']);
            $itemres = $this->db->get()->row_array();
            if ($itemres['cnt']==0) {
                $newnumb = 1;
            } else {
                $newnumb = intval($itemres['maxnum']) + 1;
            }
            $item_number = $numtempl.str_pad($newnumb,3,'0',STR_PAD_LEFT);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('create_user', $user_id);
            $this->db->set('item_number', $item_number);
            $this->db->set('item_name', $data['itemname']);
            $this->db->set('item_active', 1);
            $this->db->set('category_id', $data['category']);
            $this->db->set('item_template','Stressball');
            $this->db->set('brand', 'BT');
            $this->db->insert('sb_items');
            $newid = $this->db->insert_id();
            if ($newid > 0) {
                // Add subcategory
                // $this->db->set('item_categories_itemid', $newid);
                // $this->db->set('item_categories_categoryid', $data['subcategory']);
                // $this->db->set('item_categories_order', 1);
                // $this->db->insert('sb_item_categories');
                $out['result'] = $this->success_result;
                $out['item_id'] = $newid;
                // Update Category
                $this->load->model('categories_model');
                $this->categories_model->activate_reliver_categories($data['category']);
            }
        }
        return $out;
    }

    public function new_sritem($data, $user_id) {
        $out=['result'=>$this->error_result, 'msg' => 'Item add fail'];
        $errflag = 0;
        $errmsg = '';
        if (empty($data['category'])) {
            $errmsg.='Empty Item Category'.PHP_EOL;
            $errflag = 1;
        }
        if (empty($data['itemname'])) {
            $errmsg.='Empty Item Name'.PHP_EOL;
            $errflag = 1;
        }
        if ($errflag==1) {
            $out['msg'] = $errmsg;
        } else {
            // Construct item Number;
            $this->db->select('category_code');
            $this->db->from('sr_categories');
            $this->db->where('category_id', $data['category']);
            $catres = $this->db->get()->row_array();

            $numtempl = $catres['category_code'];

            $this->db->select('max(substr(item_number, 2)) as maxnum, count(item_id) as cnt');
            $this->db->from('sb_items');
            $this->db->like('item_number', $numtempl, 'after');
            $this->db->where('brand','SR');
            $itemres = $this->db->get()->row_array();
            if ($itemres['cnt']==0) {
                $newnumb = 1;
            } else {
                $newnumb = intval($itemres['maxnum']) + 1;
            }
            $item_number = $numtempl.str_pad($newnumb,3,'0',STR_PAD_LEFT);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('create_user', $user_id);
            $this->db->set('item_number', $item_number);
            $this->db->set('item_name', $data['itemname']);
            $this->db->set('item_active', 1);
            $this->db->set('category_id', $data['category']);
            $this->db->set('item_template','Stock Stress Reliever');
            $this->db->set('brand', 'SR');
            $this->db->insert('sb_items');
            $newid = $this->db->insert_id();
            if ($newid > 0) {
                // Add subcategory
                // $this->db->set('item_categories_itemid', $newid);
                // $this->db->set('item_categories_categoryid', $data['subcategory']);
                // $this->db->set('item_categories_order', 1);
                // $this->db->insert('sb_item_categories');
                $out['result'] = $this->success_result;
                $out['item_id'] = $newid;
            }
        }
        return $out;
    }


    public function save_history($history, $item_id, $user_id) {
        $needsave = 0;
        foreach ($history as $key => $val) {
            if (!empty($val)) {
                $needsave = 1;
            }
        }
        if ($needsave==1) {
            // Generate Key
            $history_key = uniq_link(15);
            $dateadd = date('Y-m-d H:i:s');
            foreach ($history as $key=>$val) {
                if (count($val) > 0) {
                    $item_key = '';
                    if ($key=='keyinfo') {
                        $item_key = 'KEY INFO';
                    } elseif ($key=='meta') {
                        $item_key = 'META & SEARCH';
                    } elseif ($key=='similar') {
                        $item_key = 'SIMILAR ITEMS';
                    } elseif ($key=='options') {
                        $item_key = 'IMAGES & OPTIONS';
                    } else {
                        $item_key = strtoupper($key);
                    }
                    foreach ($val as $row) {
                        $this->db->set('item_id', $item_id);
                        $this->db->set('user_id', $user_id);
                        $this->db->set('added_at', $dateadd);
                        $this->db->set('item_key', $item_key);
                        $this->db->set('article', $history_key);
                        $this->db->set('change_txt', $row);
                        $this->db->insert('sb_item_history');
                    }
                }
            }
        }
    }

    public function get_item_history($item_id) {
        $this->db->select('h.*, u.user_name');
        $this->db->from('sb_item_history h');
        $this->db->join('users u','h.user_id=u.user_id');
        $this->db->where('h.item_id', $item_id);
        $this->db->order_by('added_at, article','desc');
        $results =  $this->db->get()->result_array();
        $out = [];
        $curcode = '';
        foreach ($results as $result) {
            if ($curcode!==$result['article']) {
                $result['newaricle'] = 1;
                $result['date'] = date('M j, Y - H:i', strtotime($result['added_at'])).' <span>'.date('T', strtotime($result['added_at'])).'</span>';
                $curcode = $result['article'];
            } else {
                $result['newaricle'] = 0;
                $result['user_name'] = '';
                $result['date'] = '';
            }
            $out[] = $result;
        }
        return $out;
    }

    public function prepare_options_edit($sessiondata) {
        $colors = $sessiondata['colors'];
        $item = $sessiondata['item'];
        $images = $sessiondata['images'];
        $numidx = 1;
        $outcolors = [];
        foreach ($colors as $color) {
            $outcolors[] = $color;
            $numidx++;
        }
        if (empty($item['printshop_inventory_id'])) {
            if ($numidx < $this->max_colors) {
                for ($i=$numidx; $i < $this->max_colors; $i++) {
                    $outcolors[] = [
                        'item_color_id' => ($i)*(-1),
                        'item_color' => '',
                        'item_color_image' => '',
                        'item_color_order' => $i,
                    ];
                }
            }
        }
        $outimages = [];
        $numidx=1;
        foreach ($images as $image) {
            $outimages[] = $image;
            $numidx++;
        }
        if ($numidx < $this->max_images) {
            for ($i=$numidx; $i <= $this->max_images; $i++) {
                $outimages[] = [
                    'item_img_id' => $i*(-1),
                    'item_img_name' => '',
                    'item_img_order' => $i,
                    'item_img_label' => '',
                    'title' => '',
                ];
            }
        }
        return [
            'colors' => $outcolors,
            'images' => $outimages,
        ];
    }


    public function get_item_shipboxes($item_id) {
        $this->db->select('box_qty, box_width, box_height, box_length');
        $this->db->from('sb_item_shipping');
        $this->db->where('item_id', $item_id);
        // $this->db->order_by('box_qty','desc');
        return $this->db->get()->result_array();
    }

    private function _recalc_inventory_profit($prices, $vendor_item_cost) {
        $idx = 0;
        foreach ($prices as $price) {
            if (!empty($vendor_item_cost) && !empty($price['item_qty'])) {
                $base_price = $price['price'];
                if (!empty($price['sale_price'])) {
                    $base_price = $price['sale_price'];
                }
                $profit = round(($base_price - $vendor_item_cost) * $price['item_qty'],2);
                $prices[$idx]['profit'] = $profit;
            }
            $idx++;
        }
        return $prices;
    }

    public function merchantcenter_items($brand)
    {
        $url = 'most-popular-stress-balls.html';
        $itemspop = [];
        $populkeys = [];
        $catdat = $this->db->select('category_id')->from('sb_categories')->where(['category_url' => $url, 'parent_id' => NULL])->get()->row_array();
        if (ifset($catdat,'category_id',0)>0) {
            $category_id = $catdat['category_id'];
            // Build list of items
            $this->db->select('i.*')->from('sb_items i')->join('sb_item_categories ic','ic.item_categories_itemid=i.item_id')
            ->where(['ic.item_categories_categoryid' => $category_id, 'i.brand' => $brand, 'i.item_active' =>1])->order_by('i.item_number', 'asc');
            $items = $this->db->get()->result_array();
            $itemspop = $this->_prepare_xmlitems($items);
            foreach ($items as $item) {
                array_push($populkeys, $item['item_id']);
            }
            echo 'Popular '.count($items).PHP_EOL;
        }
        // Select all other items
        $this->db->select('i.*')->from('sb_items i')->where(['i.brand' => $brand, 'i.item_active' =>1])->order_by('i.item_number', 'asc');
        $itemraw = $this->db->get()->result_array();
        $items = [];
        foreach ($itemraw as $item) {
            if (!in_array($item['item_id'], $populkeys)) {
                $items[] = $item;
            }
        }
        $itemsdat = $this->_prepare_xmlitems($items);
        $allitems = array_merge($itemspop, $itemsdat);
        if (count($allitems) > 0 ) {
            $this->_prepare_xmldoc($allitems);
        }

    }

    private function _prepare_xmlitems($items)
    {
        $out = [];
        $urlprefix = 'https://www.stressballs.com';
        foreach ($items as $item) {
            if (empty($item['main_image'])) {
                $this->db->select('item_img_item_id ,item_img_name')->from('sb_item_images')->where(['item_img_item_id' => $item['item_id']])->order_by('item_img_order', 'asc');
                $imgdata = $this->db->get()->row_array();
                $image = '';
                if (ifset($imgdata, 'item_img_item_id',0) > 0) {
                    $image =  $urlprefix.$imgdata['item_img_name'];
                }
            } else {
                $image = $urlprefix.$item['main_image'];
            }
            // Min Price
            $this->db->select('count(promo_price_id) as cnt, min(price) as price, min(sale_price) as saleprice');
            $this->db->from('sb_promo_price');
            $this->db->where('item_id',$item['item_id']);
            $pricedat = $this->db->get()->row_array();
            $price = 0;
            if ($pricedat['cnt']>0) {
                $price = $pricedat['saleprice'];
            }
            $out[] = [
                'id' => $item['item_number'],
                'title' => $item['item_name'],
                'description' => $item['item_metadescription'],
                'link' => $urlprefix.'/shop/'.$item['item_url'],
                'image_link' => $image,
                'condition' => 'new',
                'availability' => 'in stock',
                'price' => number_format($price,2,'.','').' '.($price>0 ? 'USD' : ''),
            ];
        }
        return $out;
    }

    private function _prepare_xmldoc($items)
    {
        $filename = 'merchantcenter_sbitems_'.date('Ymd').'.xml';
        $fullpath = $this->config->item('upload_path_preload').$filename;
        @unlink($fullpath);
        $fh = fopen($fullpath, 'w+');
        if ($fh) {
            $rows = [];
            $rows[] = '<?xml version="1.0"?>'.PHP_EOL;
            $rows[] = '<rss xmlns:g="http://base.google.com/ns/1.0" version="2.0">'.PHP_EOL;
            $rows[] = '<channel>'.PHP_EOL;
            foreach ($items as $item) {
                $rows[] = '<item>'.PHP_EOL;
                foreach ($item as $key => $value) {
                    $rows[] = '<g:'.$key.'>'.$value.'</g:'.$key.'>'.PHP_EOL;
                }
                $rows[] = '</item>'.PHP_EOL;
            }
            $rows[] = '</channel>'.PHP_EOL;
            $rows[] = '</rss>';
            foreach ($rows as $row) {
                fwrite($fh, $row);
            }
            fclose($fh);
            echo 'File '.$this->config->item('pathpreload').$filename.' ready'.PHP_EOL;
        } else {
            echo 'Error '.PHP_EOL;
        }
    }
}
