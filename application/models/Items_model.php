<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Items_model extends My_Model
{
    private $Inventory_Source='Stock';


    function __construct()
    {
        parent::__construct();
    }

    public function count_searchres($search, $brand, $vendor_id='') {
        $this->db->select('count(i.item_id) as cnt',FALSE);
        $this->db->from('sb_items i');
        $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($search)."%'";
        if ($vendor_id) {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.item_id');
            $this->db->where('v.vendor_item_vendor',$vendor_id);
        }
        if ($brand!=='ALL') {
            $this->db->where('i.brand', $brand);
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


}
