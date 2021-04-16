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
            'item_template' => 'Stressballs',
            'item_lead_a' => 0,
            'item_lead_b' => 0,
            'item_lead_c' => 0,
            'item_material' => '',
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
            ''
        ];
        $vitem=[
            'vendor_item_id' => -1,
            'vendor_item_vendor' => '',
            'vendor_item_number' => '',
            'vendor_item_name' => '',
            'vendor_item_blankcost' => 0,
            'vendor_item_cost' => 0,
            'vendor_item_exprint' => 0,
            'vendor_item_setup' => 0,
            'vendor_item_notes' => '',
            'vendor_item_zipcode' => '',
            'printshop_item_id' => '',
        ];
        $vprices = [];
        for ($i=1; $i<=$this->config->item('prices_val'); $i++) {
            $vprices[] = [
                'vendorprice_id' => $i*-1,
                'vendor_item_id' => -1,
                'vendorprice_qty' => '',
                'vendorprice_val' => '',
                'vendorprice_color' => '',
            ];
        }
        $images = [];
        for ($i=1; $i<=$this->config->item('slider_images'); $i++) {
            if ($i==1) {
                $title = 'Main Pic';
            } else {
                $title = 'Pic '.$i;
            }

            $images[] = [
                'item_img_id' => $i * (-1),
                'item_img_item_id' => -1,
                'item_img_name' => '',
                'item_img_thumb' => '',
                'item_img_order' => '',
                'item_img_big' => '',
                'item_img_medium' => '',
                'item_img_small' => '',
                'item_img_label' => '',
                'title' => $title,
            ];
        }
        $imprints = [];
        $prices = [];
        for ($i=1; $i<=$this->config->item('prices_val'); $i++) {
            $prices[] = [
                'promo_price_id' => $i * (-1),
                'item_id' => -1,
                'price' => '',
                'sale_price' => '',
                'profit' => '',
                'show_first' => '0',
            ];
        }
        $data=[
            'item' => $item,
            'vendor_item' => $vitem,
            'vendor_price' => $vprices,
            'images' => $images,
            'inprints' => $imprints,
            'prices' => $prices,
        ];
        $out['data'] = $data;
        return $out;
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
                    'item_color_itemid' => $itmcolor['item_color_itemid'],
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
                    'vendor_item_notes' => '',
                    'vendor_item_zipcode' => '',
                    'printshop_item_id' => '',
                ];
                for ($i=1; $i<=$this->config->item('prices_val'); $i++) {
                    $vprices[] = [
                        'vendorprice_id' => $i*-1,
                        'vendor_item_id' => -1,
                        'vendorprice_qty' => '',
                        'vendorprice_val' => '',
                        'vendorprice_color' => '',
                    ];
                }
                $vendor = [

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
                for ($i=$numpp; $i<=$this->config->item('prices_val'); $i++) {
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
                if ($numpp < $this->config->item('slider_images')) {
                    for ($i=$numpp; $i<=$this->config->item('slider_images'); $i++) {
                        $title = 'Pic '.$i;
                        $images[] = [
                            'item_img_id' => $i * (-1),
                            'item_img_item_id' => $item_id,
                            'item_img_name' => '',
                            'item_img_thumb' => '',
                            'item_img_order' => '',
                            'item_img_big' => '',
                            'item_img_medium' => '',
                            'item_img_small' => '',
                            'item_img_label' => '',
                            'title' => $title,
                        ];
                    }
                }
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
            if ($numpp<$this->config->item('prices_val')) {
                for ($i=$numpp; $i<=$this->config->item('prices_val'); $i++) {
                    $prices[] = [
                        'promo_price_id' => $i * (-1),
                        'item_id' => $item_id,
                        'price' => '',
                        'sale_price' => '',
                        'profit' => '',
                        'show_first' => '0',
                        'shipbox' =>  '',
                        'shipweight' =>  '',
                        'profit_class' =>  '',
                        'profit_perc' =>  '',
                    ];
                }
            }
            // Special price - setup, print
            $specprice = $this->prices_model->get_itemlist_specprice($item_id);
            $item['item_price_id'] = $specprice['item_price_id'];
            $item['item_price_print'] = $specprice['item_price_print'];
            $item['item_sale_print'] = $specprice['item_sale_print'];
            $item['profit_print'] = $specprice['profit_print'];
            $item['item_price_setup'] = $specprice['item_price_setup'];
            $item['item_sale_setup'] = $specprice['item_sale_setup'];
            $item['profit_setup'] = $specprice['profit_setup'];
            $item['profit_print_class']=$specprice['profit_print_class'];
            $item['profit_print_perc']=$specprice['profit_print_perc'];
            $item['profit_setup_class']=$specprice['profit_setup_class'];
            $item['profit_setup_perc']=$specprice['profit_setup_perc'];
            // Simular
            $similar = $this->similars_model->get_similar_items($item_id);
            $data=[
                'item' => $item,
                'colors' => $colors,
                'vendor_item' => $vitem,
                'vendor_price' => $vprices,
                'images' => $images,
                'inprints' => $imprints,
                'prices' => $prices,
                'similar' => $similar,
            ];
            $out['data'] = $data;
        }
        return $out;

    }

}
