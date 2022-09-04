<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Items_model extends My_Model
{
    private $Inventory_Source='Stock';


    function __construct()
    {
        parent::__construct();
    }

    public function count_searchres($search, $brand, $vendor_id='', $itemstatus = 0) {
        $this->db->select('count(i.item_id) as cnt',FALSE);
        $this->db->from('sb_items i');
        if ($vendor_id) {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
            $this->db->where('v.vendor_item_vendor',$vendor_id);
        }
        if ($brand!=='ALL') {
            $this->db->where('i.brand', $brand);
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
            $this->db->where('i.brand', $options['brand']);
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
            $this->db->where('i.brand', $options['brand']);
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
            if ($key=='brand') {
                if ($value!=='ALL') {
                    $this->db->where('i.brand', $value);
                }
            } else {
                $this->db->where($key,$value);
            }
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
            if ($key=='brand') {
                if ($val!=='ALL') {
                    $this->db->where('i.brand', $val);
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
            $this->db->where('itm.brand', $options['brand']);
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
            $this->db->where('i.brand',$options['brand']);
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
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->select('v.vendor_name as vendor, v.vendor_phone, v.vendor_email, v.vendor_website, svi.vendor_item_number');
        $this->db->select('(vm.size+vm.weigth+vm.material+vm.lead_a+vm.lead_b+vm.lead_c+vm.colors+vm.categories+vm.images+vm.prices) as missings');
        $this->db->from('sb_items i');
        $this->db->join('sb_vendor_items svi','i.vendor_item_id = svi.vendor_item_id','left');
        $this->db->join('vendors v','v.vendor_id=svi.vendor_item_vendor');
        $this->db->join('v_item_missinginfo vm','i.item_id=vm.item_id','left');
        if (ifset($options,'brand', 'ALL')!=='ALL') {
            $this->db->where('i.brand', $options['brand']);
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
            $item['vendor_details'] = $this->load->view('dbitems/vendor_details_view', $item, TRUE);
            $item['category1']=$item['category2']=$item['category3']='';
            $this->db->select('ic.item_categories_id, ic.item_categories_categoryid');
            $this->db->from('sb_item_categories ic');
            $this->db->where('ic.item_categories_itemid',$item['item_id']);
            $categ=$this->db->get()->result_array();
            $i=1;
            foreach ($categ as $cat) {
                $item['category'.$i]=$cat['item_categories_categoryid'];
                $i++;
                if ($i>3) {
                    break;
                }
            }
            $item['misinfo_class'] = ($item['missings']==0 ? '' : 'missing');
            $item['misinfo_name'] = ($item['missings']==0 ? 'Complete' : $item['missings'].' Missing');
            $item['misinfo_content'] = '';
            if ($item['missings']>0) {
                $this->db->select('*');
                $this->db->from('v_item_missinginfo');
                $this->db->where('item_id', $item['item_id']);
                $misdata = $this->db->get()->row_array();
                $item['misinfo_content'] = $this->load->view('dbitems/missinfo_details_view', $misdata, TRUE);
            }
            $item['numpp'] = $numpp;
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
        for ($i=0; $i<$this->config->item('item_colors'); $i++) {
            $idx = ($i + 1) * (-1);
            $colors[] = [
                'item_color_id' => $idx,
                'item_color' => '',
            ];
        }
        $shipboxes = [];
        for ($i=0; $i<3; $i++) {
            $shipbox[] = [
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
            'deleted' => [],
        ];
        // $out['data'] = $data;
        // return $out;
        return $data;
    }

    public function get_itemlist_details($item_id, $editmode = 0) {
        $out=['result' => $this->error_result, 'msg' => 'Item Not Found'];
        $this->db->select('*');
        $this->db->from('sb_items');
        $this->db->where('item_id', $item_id);
        $item = $this->db->get()->row_array();
        if (ifset($item, 'item_id',0)==$item_id) {
            $out['result'] = $this->success_result;
            $this->load->model('itemimages_model');
            $this->load->model('vendors_model');
            $this->load->model('imprints_model');
            $this->load->model('prices_model');
            $this->load->model('similars_model');
            $this->load->model('itemcolors_model');
            // Colors
            $colorsrc = $this->itemcolors_model->get_colors_item($item_id, $editmode);
            $colors = [];
            $numpp=0;
            foreach ($colorsrc as $itmcolor) {
                $colors[] = [
                    'item_color_id' => $itmcolor['item_color_id'],
                    'item_color' => $itmcolor['item_color'],
                ];
                $numpp++;
            }
            if ($numpp < 9 ) {
                for ($i=$numpp; $i < 9 ; $i++) {
                    $colors[] = [
                        'item_color_id' => $i*(-1),
                        'item_color_itemid' => $item_id,
                        'item_color' => '',
                    ];
                }
            }
            // Vendor Info
            if ($item['brand']=='SR') {
                $pricesmax = $this->config->item('relievers_prices_val');
            } else {
                $pricesmax = $this->config->item('prices_val');
            }
            $vitem = $this->vendors_model->get_item_vendor($item['vendor_item_id']);
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
                    'printshop_item_id' => '',
                    'stand_days' => '',
                    'rush1_days' => '',
                    'rush2_days' => '',
                    'rush1_price' => '',
                    'rush2_price' => '',
                    'pantone_match' => '',

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
                $vendor = [
                    'vendor_id' => '',
                    'vendor_name' => '',
                    'vendor_zipcode' => '',
                    'shipaddr_state' => '',
                    'shipaddr_country' => '',
                    'po_note' => '',
                ];
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
                    'item_img_order' => $image['item_img_order'],
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
            if ($editmode==1) {
//                if ($numpp < $this->config->item('slider_images')) {
//                    $idx = 1;
//                    for ($i=$numpp; $i<=$this->config->item('slider_images'); $i++) {
//                        $title = 'Pic '.$i;
//                        $images[] = [
//                            'item_img_id' => $idx * (-1),
//                            'item_img_item_id' => $item_id,
//                            'item_img_name' => '',
//                            'item_img_thumb' => '',
//                            'item_img_order' => '',
//                            'item_img_big' => '',
//                            'item_img_medium' => '',
//                            'item_img_small' => '',
//                            'item_img_label' => '',
//                            'title' => $title,
//                        ];
//                        $idx++;
//                    }
//                }
            }
            // Options images
            if ($item['option_images']==0) {
                $option_images = [];
            } else {
                $option_images = $this->itemimages_model->get_itemoption_images($item_id);
            }
            $imprints = $this->imprints_model->get_imprint_item($item_id);
            $priceres = $this->prices_model->get_itemlist_price($item_id);
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
                    'profit_perc' => (empty($profitperc) ? $profitperc : $profitperc.'%'),
                ];
                $numpp++;
                if ($numpp > $this->config->item('prices_val')) {
                    break;
                }
            }
            if ($numpp < $pricesmax) {
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
            // Special price - setup, print
            $specprice = $this->prices_model->get_itemlist_specprice($item_id);
            foreach ($specprice as $key => $val) {
                $item[$key] = $val;
            }
            $shipboxes = $this->get_itemshipbox($item_id, $editmode);
            // Simular
            $similar = $this->similars_model->get_similar_items($item_id, $item['brand']);
            // config
            $data=[
                'item' => $item,
                'colors' => $colors,
                'vendor' => $vendor,
                'vendor_item' => $vitem,
                'vendor_price' => $vprices,
                'images' => $images,
                'option_images' => $option_images,
                'inprints' => $imprints,
                'prices' => $prices,
                'similar' => $similar,
                'shipboxes' => $shipboxes,
                'deleted' => [],
            ];
            $out['data'] = $data;
        }
        return $out;

    }

    public function get_relievers_itemscount($options=[]) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->select('(vm.size+vm.weigth+vm.material+vm.lead_a+vm.lead_b+vm.lead_c+vm.colors+vm.categories+vm.images+vm.prices) as missings');
        $this->db->from('sb_items i');
        $this->db->join('v_item_missinginfo vm','i.item_id=vm.item_id','left');
        $this->db->where('i.brand', 'SR');
        if (ifset($options, 'category',0)!=0) {
            $this->db->where('i.category_id', $options['category']);
        }
        if (ifset($options, 'search', '')!=='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".$options['search']."%'";
            $this->db->where($where);
        }
        if (ifset($options, 'status', 0) > 0) {
            if ($options['status']==1) {
                $this->db->where('i.item_active', 1);
            } else {
                $this->db->where('i.item_active', 0);
            }
        }
        if (ifset($options,'vendor', '')!=='') {
            $this->db->join('sb_vendor_items svi','i.vendor_item_id = svi.vendor_item_id','left');
            $this->db->join('vendors v','v.vendor_id=svi.vendor_item_vendor');
            $this->db->where('v.vendor_id', $options['vendor']);
        }
        if (ifset($options,'misinfo',0) > 0) {
            if ($options['misinfo']==1) {
                $this->db->having('missings=0');
            } else {
                $this->db->having('missings>0');
            }
        }
        $res = $this->db->get()->result_array();
        return count($res);
    }

    public function get_relievers_itemslist($options) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        // $this->db->select('v.vendor_name as vendor, v.vendor_phone, v.vendor_email, v.vendor_website, svi.vendor_item_number');
        $this->db->select('(vm.size+vm.weigth+vm.material+vm.lead_a+vm.lead_b+vm.lead_c+vm.colors+vm.categories+vm.images+vm.prices) as missings');
        $this->db->from('sb_items i');
        $this->db->join('v_item_missinginfo vm','i.item_id=vm.item_id','left');
        $this->db->where('i.brand', 'SR');
        if (ifset($options, 'category',0)!=0) {
            $this->db->where('i.category_id', $options['category']);
        }
        if (ifset($options, 'search', '')!=='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".$options['search']."%'";
            $this->db->where($where);
        }
        if (ifset($options, 'status', 0) > 0) {
            if ($options['status']==1) {
                $this->db->where('i.item_active', 1);
            } else {
                $this->db->where('i.item_active', 0);
            }
        }
        if (ifset($options,'vendor', '')!=='') {
            $this->db->join('sb_vendor_items svi','i.vendor_item_id = svi.vendor_item_id','left');
            $this->db->join('vendors v','v.vendor_id=svi.vendor_item_vendor');
            $this->db->where('v.vendor_id', $options['vendor']);
        }
        if (ifset($options,'misinfo',0) > 0) {
            if ($options['misinfo']==1) {
                $this->db->having('missings=0');
            } else {
                $this->db->having('missings>0');
            }
        }
        $order_by = ifset($options, 'order_by','i.item_id');
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
        foreach ($res as $row) {
            $rowclass='';
            if ($row['item_active']==0) {
                $rowclass='inactive';
            }
            $row['rowclass']=$rowclass;
            $row['status'] = $row['item_active']==1 ? 'Active' : 'Inactive';
            if ($row['missings']==0) {
                $row['misclas'] = '';
                $row['misinfo'] = '-';
            } else {
                $row['misclas'] = 'missing';
                if ($row['missings']==1) {
                    $row['misinfo'] = '1 field';
                } else {
                    $row['misinfo'] = $row['missings'].' fields';
                }
            }
            $row['numpp'] = $numpp;
            $out[] = $row;
            $numpp++;
        }
        return $out;
    }

    public function get_itemshipbox($item_id, $editmode) {
        $this->db->select('*');
        $this->db->from('sb_item_shipping');
        $this->db->where('item_id', $item_id);
        $this->db->order_by('box_qty');
        $res = $this->db->get()->result_array();
        if ($editmode==1) {
            if (count($res) < 3) {
                $numbox = count($res)+1;
                for ($i=$numbox; $i<4; $i++) {
                    $res[] = [
                        'item_shipping_id' => (-1)*$i,
                        'box_qty' => '',
                        'box_width' => '',
                        'box_length' => '',
                        'box_height' => '',
                    ];
                }
            }
        }
        return $res;
    }

    public function itemdetails_change_iteminfo($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($options, 'fld','unknownfd');
        $newval=ifset($options, 'newval','');
        if (array_key_exists($fldname, $item)) {
            $out['result'] = $this->success_result;
            $item[$fldname] = $newval;
            $sessiondata['item'] = $item;
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_itemcheck($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($options, 'fld','unknownfd');
        if (array_key_exists($fldname, $item)) {
            $out['result'] = $this->success_result;
            $val = $item[$fldname];
            if ($val==0) {
                $newval = 1;
            } else {
                $newval = 0;
            }
            $item[$fldname] = $newval;
            $out['newval'] = $newval;
            $sessiondata['item'] = $item;
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_similar($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $similars = $sessiondata['similar'];
        $fld = ifset($options,'fld','0');
        $newval = ifset($options, 'newval', '');
        $idx=0;
        $found = 0;
        foreach ($similars as $similar) {
            if ($similar['item_similar_id']==$fld) {
                $found = 1;
                break;
            }
            $idx++;
        }
        if ($found == 1) {
            $similars[$idx]['item_similar_similar'] = (empty($newval) ? NULL : $newval);
            $out['result'] = $this->success_result;
            $sessiondata['similar'] = $similar;
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_vendor($sessiondata, $postdata, $sessionsid) {
        $out=['result' => $this->error_result,'msg' => 'Item Not Found'];
        $vendor = $sessiondata['vendor'];
        $vendor_id = ifset($postdata, 'newval', '-1');
        if (empty($vendor_id)) {
            $out['result'] = $this->success_result;
            $vendor=[
                'vendor_id' => '',
                'vendor_name' => '',
                'vendor_zipcode' => '',
                'shipaddr_state' => '',
                'shipaddr_country' => '',
                'po_note' => '',
            ];
        } else {
            $this->load->model('vendors_model');
            $venddat = $this->vendors_model->get_vendor($vendor_id);
            $out['msg'] = $venddat['msg'];
            if ($venddat['result']==$this->success_result) {
                $out['result'] = $this->success_result;
                $data = $venddat['data'];
                $vendor = [
                    'vendor_id' => $data['vendor_id'],
                    'vendor_name' => $data['vendor_name'],
                    'vendor_zipcode' => $data['vendor_zipcode'],
                    'shipaddr_state' => $data['shipaddr_state'],
                    'shipaddr_country' => $data['shipaddr_country'],
                    'po_note' => $data['po_note'],
                ];
            }
        }
        if ($out['result']==$this->success_result) {
            $vendor_item = [
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
                'printshop_item_id' => '',
                'stand_days' => '',
                'rush1_days' => '',
                'rush2_days' => '',
                'rush1_price' => '',
                'rush2_price' => '',
                'pantone_match' => '',
            ];
            $vprices = [];
            // if ($brand=='SR') {
            $pricesmax = $this->config->item('relievers_prices_val');
            // } else {
            //    $pricesmax = $this->config->item('prices_val');
            // }
            for ($i=1; $i<=$pricesmax-1; $i++) {
                $vprices[] = [
                    'vendorprice_id' => $i*-1,
                    'vendor_item_id' => -1,
                    'vendorprice_qty' => '',
                    'vendorprice_val' => '',
                    'vendorprice_color' => '',
                ];
            }
            $sessiondata['vendor'] = $vendor;
            $sessiondata['vendor_item'] = $vendor_item;
            $sessiondata['vendor_price'] = $vprices;
            usersession($sessionsid, $sessiondata);
            $data=[
                'vendor' => $vendor,
                'vendor_item' => $vendor_item,
                'vendor_price' => $vprices,
            ];
            $out['data'] = $data;
        }
        return $out;
    }

    public function itemdetails_save_addimages($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['images'];
        $neword = 0;
        foreach ($images as $image) {
            if ($neword < $image['item_img_order']) {
                $neword = $image['item_img_order'];
            }
        }
        $neword+=1;
        $newid = (count($images)+1) * (-1);
        $images[] = [
            'item_img_id' => $newid,
            'item_img_name' => $postdata['newval'],
            'item_img_label' => '',
            'item_img_order' => $neword,
        ];
        $sessiondata['images'] = $images;
        usersession($session, $sessiondata);
        $out['result'] = $this->success_result;
        return $out;
    }

    public function itemdetails_save_updimages($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = str_replace('replimg', '',$imgidx);
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_name'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_imagessort($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = intval($imgidx);
            $numpp = ifset($postdata, 'newval', '');
            if (!empty($numpp)) {
                $numpp = intval($numpp);
                $numord = 1;
                $newimg = [];
                $arrimg = [];
                for ($i=0; $i<count($images); $i++) {
                    foreach ($images as $image) {
                        if (!in_array($image['item_img_id'], $arrimg)) {
                            if ($numord==$numpp) {
                                array_push($arrimg, $imgidx);
                                $numord++;
                                break;
                            } else {
                                if ($image['item_img_id']!=$imgidx) {
                                    array_push($arrimg, $image['item_img_id']);
                                    $numord++;
                                    break;
                                }
                            }
                        }
                    }
                }
                $numord=1;
                foreach ($arrimg as $keyval) {
                    foreach ($images as $image) {
                        if ($image['item_img_id']==$keyval) {
                            $image['item_img_order'] = $numord;
                            $newimg[] = $image;
                            $numord++;
                        }
                    }
                }
                $sessiondata['images'] = $newimg;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function itemdetails_addimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['images'];
        $deleted = $sessiondata['deleted'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            $numrow = 1;
            $newimg = [];
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    if ($imgidx > 0) {
                        $deleted[] = [
                            'entity' => 'images',
                            'id' => $imgidx,
                        ];
                    }
                } else {
                    $image['item_img_order'] = $numrow;
                    $newimg[] = $image;
                    $numrow++;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['images'] = $newimg;
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_addimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_label'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_optionimages_add($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['option_images'];
        $neword = 0;
        foreach ($images as $image) {
            if ($neword < $image['item_img_order']) {
                $neword = $image['item_img_order'];
            }
        }
        $neword+=1;
        $newid = (count($images)+1) * (-1);
        $images[] = [
            'item_img_id' => $newid,
            'item_img_name' => $postdata['newval'],
            'item_img_label' => '',
            'item_img_order' => $neword,
        ];
        $sessiondata['option_images'] = $images;
        usersession($session, $sessiondata);
        $out['result'] = $this->success_result;
        return $out;
    }

    public function itemdetails_optionimages_update($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['option_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = str_replace('reploptimg', '',$imgidx);
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_name'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['option_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_optimagessort($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['option_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = intval($imgidx);
            $numpp = ifset($postdata, 'newval', '');
            if (!empty($numpp)) {
                $numpp = intval($numpp);
                $numord = 1;
                $newimg = [];
                $arrimg = [];
                for ($i=0; $i<count($images); $i++) {
                    foreach ($images as $image) {
                        if (!in_array($image['item_img_id'], $arrimg)) {
                            if ($numord==$numpp) {
                                array_push($arrimg, $imgidx);
                                $numord++;
                                break;
                            } else {
                                if ($image['item_img_id']!=$imgidx) {
                                    array_push($arrimg, $image['item_img_id']);
                                    $numord++;
                                    break;
                                }
                            }
                        }
                    }
                }
                $numord=1;
                foreach ($arrimg as $keyval) {
                    foreach ($images as $image) {
                        if ($image['item_img_id']==$keyval) {
                            $image['item_img_order'] = $numord;
                            $newimg[] = $image;
                            $numord++;
                        }
                    }
                }
                $sessiondata['option_images'] = $newimg;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;

    }

    public function itemdetails_optimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['option_images'];
        $deleted = $sessiondata['deleted'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            $numrow = 1;
            $newimg = [];
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    if ($imgidx > 0) {
                        $deleted[] = [
                            'entity' => 'images',
                            'id' => $imgidx,
                        ];
                    }
                } else {
                    $image['item_img_order'] = $numrow;
                    $newimg[] = $image;
                    $numrow++;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['option_images'] = $newimg;
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_optimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['option_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_label'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['option_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_vendor_price($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $vendor_prices = $sessiondata['vendor_price'];
        $priceidx = ifset($postdata,'priceidx','');
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($priceidx) && !empty($fldname)) {
            $find = 0;
            $idx=0;
            foreach ($vendor_prices as $vendor_price) {
                if ($vendor_price['vendorprice_id']==$priceidx) {
                    $vendor_prices[$idx][$fldname] = ifset($postdata,'newval','');
                    $find=1;
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['vendor_price'] = $vendor_prices;
                usersession($session, $sessiondata);
                // Add base price
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $prices = $sessiondata['prices'];
                $item = $sessiondata['item'];
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
        }
        return $out;
    }

    public function itemdetails_vendoritem_price($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $vendoritem = $sessiondata['vendor_item'];
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($fldname) && array_key_exists($fldname,$vendoritem)) {
            $vendoritem[$fldname] = ifset($postdata,'newval','');
            $out['result'] = $this->success_result;
            $sessiondata['vendor_item'] = $vendoritem;
            usersession($session, $sessiondata);
            // Add base price
            $commonprice = $this->_prepare_common_prices($sessiondata);
            $prices = $sessiondata['prices'];
            $item = $sessiondata['item'];
            $vendor_prices = $sessiondata['vendor_price'];
            $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
            $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
        }
        return $out;
    }

    public function itemdetails_price_discount($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($fldname) && array_key_exists($fldname, $item)) {
            $fldval = $fldname.'_val';
            if (empty($postdata['newval'])) {
                $item[$fldname] = '';
                $item[$fldval] = '';
                $out['result'] = $this->success_result;
            } else {
                $this->load->model('prices_model');
                $res = $this->prices_model->get_discount($postdata['newval']);
                $out['msg'] = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $out['result'] = $this->success_result;
                    $discount = $res['discount'];
                    $item[$fldval] = $discount['discount_val'];
                    $item[$fldname] = $discount['price_discount_id'];
                }
            }
            if ($out['result']==$this->success_result) {
                // Recount prices
                $prices = $sessiondata['prices'];
                $recount = $this->_recalc_prices($item, $prices);
                $item = $recount['item'];
                $prices = $recount['prices'];
                $sessiondata['item'] = $item;
                $sessiondata['prices'] = $prices;
                usersession($session, $sessiondata);
                $vendor_prices = $sessiondata['vendor_price'];
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
        }
        return $out;
    }

    public function itemdetails_item_price($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $prices = $sessiondata['prices'];
        $fldname = ifset($postdata,'fld','');
        $priceidx = ifset($postdata, 'priceidx', '');
        if (!empty($fldname) && !empty($priceidx)) {
            $idx = 0;
            $find = 0;
            foreach ($prices as $price) {
                if ($price['promo_price_id']==$priceidx) {
                    $find = 1;
                    $prices[$idx][$fldname] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $item = $sessiondata['item'];
                $recount = $this->_recalc_prices($item, $prices);
                $item = $recount['item'];
                $prices = $recount['prices'];
                $sessiondata['item'] = $item;
                $sessiondata['prices'] = $prices;
                usersession($session, $sessiondata);
                $vendor_prices = $sessiondata['vendor_price'];
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
        }
        return $out;
    }

    public function itemdetails_item_priceval($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($postdata,'fld','');
        if (!empty($fldname) && in_array($fldname, $item)) {
            $out['result'] = $this->success_result;
            $item[$fldname] = $postdata['newval'];
            $prices = $sessiondata['prices'];
            $recount = $this->_recalc_prices($item, $prices);
            $item = $recount['item'];
            $prices = $recount['prices'];
            $sessiondata['item'] = $item;
            $sessiondata['prices'] = $prices;
            usersession($session, $sessiondata);
            $vendor_prices = $sessiondata['vendor_price'];
            $commonprice = $this->_prepare_common_prices($sessiondata);
            $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
            $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
        }
        return $out;
    }

    private function _recalc_prices($item, $prices) {
        // Item Prices
        if (empty($item['price_discount'])) {
            $idx = 0;
            foreach ($prices as $price) {
                $prices[$idx]['sale_price'] = '';
                $idx++;
            }
        } else {
            $idx = 0;
            foreach ($prices as $price) {
                if ($price['price']!='') {
                    $prices[$idx]['sale_price'] = round($price['price']*(100-$item['price_discount_val'])/100,3);
                }
                $idx++;
            }
        }
        // Print
        if (empty($item['print_discount'])) {
            $item['item_sale_print'] = '';
        } else {
            if ($item['item_price_print']=='') {
                $item['item_sale_print'] = '';
            } else {
                $item['item_sale_print'] = round($item['item_price_print']*(100-$item['print_discount_val'])/100, 3);
            }
        }
        // Setup
        if (empty($item['setup_discount'])) {
            $item['item_sale_setup'] = '';
        } else {
            if ($item['item_price_setup']=='') {
                $item['item_sale_setup'] = '';
            } else {
                $item['item_sale_setup'] = round(floatval($item['item_price_setup'])*(100-floatval($item['setup_discount_val']))/100, 3);
            }
        }
        // Repeat
        if (empty($item['repeat_discount'])) {
            $item['item_sale_repeat'] = '';
        } else {
            if ($item['item_price_repeat']=='') {
                $item['item_sale_repeat'] = '';
            } else {
                $item['item_sale_repeat'] = round($item['item_price_repeat']*(100-$item['repeat_discount_val'])/100, 3);
            }
        }
        // Rush
        if (empty($item['rush1_discount'])) {
            $item['item_sale_rush1'] = '';
        } else {
            if ($item['item_price_rush1']=='') {
                $item['item_sale_rush1'] = '';
            } else {
                $item['item_sale_rush1'] = round($item['item_price_rush1']*(100-$item['rush1_discount_val'])/100,3);
            }
        }
        if (empty($item['rush2_discount'])) {
            $item['item_sale_rush2'] = '';
        } else {
            if ($item['item_price_rush2']=='') {
                $item['item_sale_rush2'] = '';
            } else {
                $item['item_sale_rush2'] = round($item['item_price_rush2']*(100-$item['rush2_discount_val'])/100,3);
            }
        }
        // Pantone
        if (empty($item['pantone_discount'])) {
            $item['item_sale_pantone'] = '';
        } else {
            if ($item['item_price_pantone']=='') {
                $item['item_sale_pantone'] = '';
            } else {
                $item['item_sale_pantone'] = round($item['item_price_pantone']*(100-$item['pantone_discount_val'])/100,3);
            }
        }
        return [
            'item' => $item,
            'prices' => $prices,
        ];
    }

    // Recalc Promo Profit
    private function _recalc_promo_profit($prices, $vendprices, $commonprices)
    {
        /* IDX of Vendor Prices */
        /* Init Profits array */
        $profits = array();
        foreach ($prices as $row) {
            $base_cost = 0;
            if (floatval($row['sale_price']) != 0) {
                $base_cost = floatval($row['sale_price']);
            } elseif (floatval($row['price']) != 0) {
                $base_cost = floatval($row['price']);
            }
            $profits[] = array(
                'price_id' => $row['promo_price_id'],
                'type' => 'qty',
                'base' => $row['item_qty'],
                'vendprice' => $commonprices['base_cost'],
                'base_cost' => $base_cost,
                'profit' => '',
                'profit_perc' => '',
                'profit_class' => 'empty',
            );
        }
        /* Add 2 special prices */
        $base_cost = 0;
        if (floatval($commonprices['item_sale_print']) != 0) {
            $base_cost = floatval($commonprices['item_sale_print']);
        } elseif (floatval($commonprices['item_price_print'])) {
            $base_cost = floatval($commonprices['item_price_print']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'print',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_exprint']) == 0 ? 0 : floatval($commonprices['vendor_item_exprint'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        $base_cost = 0;
        if (floatval($commonprices['item_sale_setup']) != 0) {
            $base_cost = floatval($commonprices['item_sale_setup']);
        } elseif (floatval($commonprices['item_price_setup'])) {
            $base_cost = floatval($commonprices['item_price_setup']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'setup',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_setup']) == 0 ? 0 : floatval($commonprices['vendor_item_setup'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Repeat
        $base_cost = 0;
        if (floatval($commonprices['item_sale_repeat']) != 0) {
            $base_cost = floatval($commonprices['item_sale_repeat']);
        } elseif (floatval($commonprices['item_price_repeat'])) {
            $base_cost = floatval($commonprices['item_price_repeat']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'repeat',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_repeat']) == 0 ? 0 : floatval($commonprices['vendor_item_repeat'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Rush 1
        $base_cost = 0;
        if (floatval($commonprices['item_sale_rush1']) != 0) {
            $base_cost = floatval($commonprices['item_sale_rush1']);
        } elseif (floatval($commonprices['item_price_rush1'])) {
            $base_cost = floatval($commonprices['item_price_rush1']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'rush1',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_rush1']) == 0 ? 0 : floatval($commonprices['vendor_item_rush1'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Rush 2
        $base_cost = 0;
        if (floatval($commonprices['item_sale_rush2']) != 0) {
            $base_cost = floatval($commonprices['item_sale_rush2']);
        } elseif (floatval($commonprices['item_price_rush2'])) {
            $base_cost = floatval($commonprices['item_price_rush2']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'rush2',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_rush2']) == 0 ? 0 : floatval($commonprices['vendor_item_rush2'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Pantone
        $base_cost = 0;
        if (floatval($commonprices['item_sale_pantone']) != 0) {
            $base_cost = floatval($commonprices['item_sale_pantone']);
        } elseif (floatval($commonprices['item_price_pantone'])) {
            $base_cost = floatval($commonprices['item_price_pantone']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'pantone',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_pantone']) == 0 ? 0 : floatval($commonprices['vendor_item_pantone'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );

        $new_profit = $this->recalc_profit($vendprices, $profits);
        return $new_profit;
    }

    private function recalc_profit($vendprice, $profits) {
        $out = array();
        foreach ($profits as $row) {
            if ($row['base_cost'] != 0) {
                if ($row['type'] == 'qty') {
                    /* Our Base less then 1-st entered value */
                    if ($row['base'] == 5000) {
                        $proof = 0;
                    }
                    foreach ($vendprice as $qrow) {
                        if ($qrow['vendorprice_qty'] <= $row['base'] && !empty($qrow['vendorprice_color'])) {
                            $row['vendprice'] = $qrow['vendorprice_color'];
                        }
                    }
                    if (floatval($row['vendprice']) != 0) {
                        $profit = ($row['base_cost'] - $row['vendprice']) * $row['base'];
                        $row['profit'] = round($profit, 0);
                        $row['profit_perc'] = round($profit / ($row['base_cost'] * $row['base']) * 100, 0);
                        $row['profit_class'] = profit_bgclass($row['profit_perc']);
                    }
                } else {
                    if (floatval($row['vendprice']) != 0) {
                        $row['profit'] = round($row['base_cost'] - $row['vendprice'], 2);
                        $row['profit_perc'] = round($row['profit'] / ($row['base_cost']) * 100, 0);
                        $row['profit_class'] = profit_bgclass($row['profit_perc']);
                    }
                }
            }
            $out[] = $row;
        }
        return $out;
    }


    private function _prepare_common_prices($sessiondata) {
        $vendor = $sessiondata['vendor_item'];
        $item = $sessiondata['item'];
        $commonprice['base_cost'] = $commonprice['vendor_item_exprint'] = $commonprice['vendor_item_setup'] = 0;
        $commonprice['vendor_item_repeat'] = $commonprice['vendor_item_rush1'] = $commonprice['vendor_item_rush2'] = 0;

        $commonprice['item_sale_print'] = $item['item_sale_print'];
        $commonprice['item_price_print'] = $item['item_price_print'];
        $commonprice['item_sale_setup'] = $item['item_sale_setup'];
        $commonprice['item_price_setup'] = $item['item_price_setup'];
        $commonprice['item_sale_repeat'] = $item['item_sale_repeat'];
        $commonprice['item_price_repeat'] = $item['item_price_repeat'];
        $commonprice['item_sale_rush1'] = $item['item_sale_rush1'];
        $commonprice['item_price_rush1'] = $item['item_price_rush1'];
        $commonprice['item_sale_rush2'] = $item['item_sale_rush2'];
        $commonprice['item_price_rush2'] = $item['item_price_rush2'];
        $commonprice['item_sale_pantone'] = $item['item_sale_pantone'];
        $commonprice['item_price_pantone'] = $item['item_price_pantone'];

        $commonprice['item_price_id'] = $item['item_price_id'];
        if (!empty($vendor['vendor_item_cost'])) {
            $commonprice['base_cost'] = $vendor['vendor_item_cost'];
        }
        if (!empty($vendor['vendor_item_exprint'])) {
            $commonprice['vendor_item_exprint'] = $vendor['vendor_item_exprint'];
        }
        if (!empty($vendor['vendor_item_setup'])) {
            $commonprice['vendor_item_setup'] = $vendor['vendor_item_setup'];
        }
        if (!empty($vendor['vendor_item_repeat'])) {
            $commonprice['vendor_item_repeat'] = $vendor['vendor_item_repeat'];
        }
        if (!empty($vendor['rush1_price'])) {
            $commonprice['vendor_item_rush1'] = $vendor['rush1_price'];
        }
        if (!empty($vendor['rush2_price'])) {
            $commonprice['vendor_item_rush2'] = $vendor['rush2_price'];
        }
        if (!empty($vendor['pantone_match'])) {
            $commonprice['vendor_item_pantone'] = $vendor['pantone_match'];
        }

        return $commonprice;
    }

    private function _update_profit($profits, $item, $prices, $sessiondata, $session) {
        foreach ($profits as $profit) {
            if ($profit['type']=='qty') {
                $idx = 0;
                foreach ($prices as $price) {
                    if ($price['item_qty']==$profit['base']) {
                        $prices[$idx]['profit'] = $profit['profit'];
                        $prices[$idx]['profit_class'] = $profit['profit_class'];
                        $prices[$idx]['profit_perc'] = $profit['profit_perc'];
                        break;
                    }
                    $idx++;
                }
            } elseif ($profit['type']=='setup') {
                $item['profit_setup'] = $profit['profit'];
                $item['profit_setup_class'] = $profit['profit_class'];
                $item['profit_setup_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='print') {
                $item['profit_print'] = $profit['profit'];
                $item['profit_print_class'] = $profit['profit_class'];
                $item['profit_print_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='repeat') {
                $item['profit_repeat'] = $profit['profit'];
                $item['profit_repeat_class'] = $profit['profit_class'];
                $item['profit_repeat_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='rush1') {
                $item['profit_rush1'] = $profit['profit'];
                $item['profit_rush1_class'] = $profit['profit_class'];
                $item['profit_rush1_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='rush2') {
                $item['profit_rush2'] = $profit['profit'];
                $item['profit_rush2_class'] = $profit['profit_class'];
                $item['profit_rush2_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='pantone') {
                $item['profit_pantone'] = $profit['profit'];
                $item['profit_pantone_class'] = $profit['profit_class'];
                $item['profit_pantone_perc'] = $profit['profit_perc'];
            }
        }
        $sessiondata['item'] = $item;
        $sessiondata['prices'] = $prices;
        usersession($session, $sessiondata);
        return true;
    }

}
