<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Class Otherprices_model extends My_Model
{

    private $price_types;

    function __construct()
    {
        parent::__construct();
        $this->price_types = $this->config->item('price_types');
    }

    public function get_prices_item($item_id) {
        $this->db->select('op.*,o.other_vendor_name as vendor_name,o.other_vendor_url as other_vendor_url,o.other_vendor_id',FALSE);
        $this->db->from('sb_other_vendor_price op');
        $this->db->join('sb_other_vendor o','o.other_vendor_id=op.other_vendorprice_vendor');
        $this->db->where('op.other_vendorprice_item',$item_id);
        $this->db->order_by('o.other_vendor_id');
        $result=$this->db->get()->result_array();
        if (count($result)==0) {
            /* Empty research data */
            $this->db->select('other_vendor_name as vendor_name,other_vendor_id as other_vendorprice_vendor,other_vendor_url');
            $this->db->from('sb_other_vendor');
            $this->db->order_by('other_vendor_id');
            $vendors=$this->db->get()->result_array();
            $result=array();
            $i=1;
            foreach ($vendors as $row) {
                $row['other_vendorprice_id']=(-1)*$i;
                $row['other_vendor_id']=$row['other_vendorprice_vendor'];
                $row['other_vendorprice_item']=$item_id;
                foreach ($this->price_types as $types) {
                    $row['other_vendorprice_price_'.$types['type']]='';
                }
                $row['other_vendor_price_url']=$row['other_vendor_url'];
                $row['other_vendorprice_created']=time();
                $row['other_vendorprice_updated']=time();
                $row['other_vendorprice_updateby']='';
                $out[]=$row;
                $i++;
            }
        } else {
            /* Check URL */
            $out = array();
            foreach ($result as $row) {
                if ($row['other_vendor_price_url']=='') {
                    $row['other_vendor_price_url']=$row['other_vendor_url'];
                }
                $row['other_vendorprice_updated']=strtotime($row['other_vendorprice_updated']);
                $out[]=$row;
            }
        }

        return $out;
    }

    /* Compare Prices , apply different colors */
    public function compare_prices_item($prices,$research_price) {
        $mindiff=($this->config->item('price_diff')/100);
        $price_types = $this->config->item('competitors_prices');
        $return_prices=array();
        foreach ($research_price as $row) {
            foreach ($price_types as $type) {
                $row['price_'.$type['type'].'_class']='empty_price';
                if ($row['other_vendorprice_price_'.$type['type']]!='' && $row['other_vendorprice_price_'.$type['type']]!=0) {
                    $row['price_'.$type['type'].'_class']='white';
                    $compare_price='';
                    if (isset($prices['item_sale_'.$type['type']]) && ($prices['item_sale_'.$type['type']]!='' && $prices['item_sale_'.$type['type']]!=0)) {
                        $compare_price=$prices['item_sale_'.$type['type']];
                    } elseif (isset($prices['item_price_'.$type['type']]) && ($prices['item_price_'.$type['type']]!='' && $prices['item_price_'.$type['type']]!=0)) {
                        $compare_price=$prices['item_price_'.$type['type']];
                    }
                    if ($compare_price!='') {
                        if ($compare_price==$row['other_vendorprice_price_'.$type['type']]) {
                            $row['price_'.$type['type'].'_class']='orange';
                        } elseif ($compare_price>$row['other_vendorprice_price_'.$type['type']]) {
                            $row['price_'.$type['type'].'_class']='red';
                        } elseif (($row['other_vendorprice_price_'.$type['type']]-$compare_price)>=$mindiff) {
                            $row['price_'.$type['type'].'_class']='blue';
                        }
                    }
                }
            }
            /* Add row to result array */
            $return_prices[]=$row;
        }
        return $return_prices;
    }

    public function get_compared_prices($order_by, $direct, $limit, $offset, $search, $compareprefs, $vendor_id) {
        $this->db->select('item.item_id,item.item_number,item.item_name,item.item_template');
        $this->db->select('price_25 as item_price_25, profit_25 as item_profitperc_25, profit_25_class as item_profitclass_25, profit_25_sum as item_profit_25, price_25_class');
        $this->db->select('price_50 as item_price_50, profit_50 as item_profitperc_50, profit_50_class as item_profitclass_50, profit_50_sum as item_profit_50, price_50_class');
        $this->db->select('price_75 as item_price_75, profit_75 as item_profitperc_75, profit_75_class as item_profitclass_75, profit_75_sum as item_profit_75, price_75_class');
        $this->db->select('price_150 as item_price_150, profit_150 as item_profitperc_150, profit_150_class as item_profitclass_150, profit_150_sum as item_profit_150, price_150_class');
        $this->db->select('price_250 as item_price_250, profit_250 as item_profitperc_250, profit_250_class as item_profitclass_250, profit_250_sum as item_profit_250, price_250_class');
        $this->db->select('price_500 as item_price_500, profit_500 as item_profitperc_500, profit_500_class as item_profitclass_500, profit_500_sum as item_profit_500, price_500_class');
        $this->db->select('price_1000 as item_price_1000, profit_1000 as item_profitperc_1000, profit_1000_class as item_profitclass_1000, profit_1000_sum as item_profit_1000, price_1000_class');
        $this->db->select('price_2500 as item_price_2500, profit_2500 as item_profitperc_2500, profit_2500_class as item_profitclass_2500, profit_2500_sum as item_profit_2500, price_2500_class');
        $this->db->select('price_3000 as item_price_3000, profit_3000 as item_profitperc_3000, profit_3000_class as item_profitclass_3000, profit_3000_sum as item_profit_3000, price_3000_class');
        $this->db->select('price_5000 as item_price_5000, profit_5000 as item_profitperc_5000, profit_5000_class as item_profitclass_5000, profit_5000_sum as item_profit_5000, price_5000_class');
        $this->db->select('price_10000 as item_price_10000, profit_10000 as item_profitperc_10000, profit_10000_class as item_profitclass_10000, profit_10000_sum as item_profit_10000, price_10000_class');
        $this->db->select('price_20000 as item_price_20000, profit_20000 as item_profitperc_20000, profit_20000_class as item_profitclass_20000, profit_20000_sum as item_profit_20000, price_20000_class');
        $this->db->select('price_setup as item_price_setup, profit_setup as item_profitperc_setup, profit_setup_class as item_profitclass_setup, profit_setup_sum as item_profit_setup, price_setup_class, last_upd');
        /* Other Prices */
        $this->db->select('otherprice_25, otherprice_50, otherprice_75, otherprice_150, otherprice_250, otherprice_500, otherprice_1000');
        $this->db->select('otherprice_2500, otherprice_3000, otherprice_5000, otherprice_10000, otherprice_20000, otherprice_setup');
        /* Summ by type of price classes */
        $this->db->select("if(price_25_class='empty',1,0)+if(price_50_class='empty',1,0)+if(price_75_class='empty',1,0)+if(price_150_class='empty',1,0)+if(price_250_class='empty',1,0)+if(price_500_class='empty',1,0)+if(profit_1000_class='empty',1,0)+if(price_2500_class='empty',1,0)+if(price_3000_class='empty',1,0)+if(price_5000_class='empty',1,0)+if(price_10000_class='empty',1,0)+if(price_20000_class='empty',1,0)+if(price_setup_class='empty',1,0) as empty_sum",FALSE);
        $this->db->select("if(price_25_class='white',1,0)+if(price_50_class='white',1,0)+if(price_75_class='white',1,0)+if(price_150_class='white',1,0)+if(price_250_class='white',1,0)+if(price_500_class='white',1,0)+if(profit_1000_class='white',1,0)+if(price_2500_class='white',1,0)+if(price_3000_class='white',1,0)+if(price_5000_class='white',1,0)+if(price_10000_class='white',1,0)+if(price_20000_class='white',1,0)+if(price_setup_class='white',1,0) as white_sum",FALSE);
        $this->db->select("if(price_25_class='pink',1,0)+if(price_50_class='pink',1,0)+if(price_75_class='pink',1,0)+if(price_150_class='pink',1,0)+if(price_250_class='pink',1,0)+if(price_500_class='pink',1,0)+if(profit_1000_class='pink',1,0)+if(price_2500_class='pink',1,0)+if(price_3000_class='pink',1,0)+if(price_5000_class='pink',1,0)+if(price_10000_class='pink',1,0)+if(price_20000_class='pink',1,0)+if(price_setup_class='pink',1,0) as pink_sum",FALSE);
        $this->db->select("if(price_25_class='orange',1,0)+if(price_50_class='orange',1,0)+if(price_75_class='orange',1,0)+if(price_150_class='orange',1,0)+if(price_250_class='orange',1,0)+if(price_500_class='orange',1,0)+if(profit_1000_class='orange',1,0)+if(price_2500_class='orange',1,0)+if(price_3000_class='orange',1,0)+if(price_5000_class='orange',1,0)+if(price_10000_class='orange',1,0)+if(price_20000_class='orange',1,0)+if(price_setup_class='orange',1,0) as orange_sum",FALSE);
        $this->db->select("if(price_25_class='red',1,0)+if(price_50_class='red',1,0)+if(price_75_class='red',1,0)+if(price_150_class='red',1,0)+if(price_250_class='red',1,0)+if(price_500_class='red',1,0)+if(profit_1000_class='red',1,0)+if(price_2500_class='red',1,0)+if(price_3000_class='red',1,0)+if(price_5000_class='red',1,0)+if(price_10000_class='red',1,0)+if(price_20000_class='red',1,0)+if(price_setup_class='red',1,0) as red_sum",FALSE);
        $this->db->select('unix_timestamp(item.update_time) as updtime');
        $this->db->from('v_otherpriceprofits');
        $this->db->join('sb_items item', 'item.item_id=v_otherpriceprofits.item_id');
        /* Apply Limit */
        if ($search!='') {
            $where="lower(concat(item.item_number,item.item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        if ($vendor_id) {
            // $this->db->join('sb_vendor_items v','v.vendor_item_id=v_otherpriceprofits.item_id');
            $this->db->where('vendor_id',$vendor_id);
        }
        if ($compareprefs!='') {
            switch ($compareprefs) {
                case 'white':
                    $this->db->order_by('white_sum','desc');
                    break;
                case 'pink':
                    $this->db->order_by('pink_sum','desc');
                    break;
                case 'red':
                    $this->db->order_by('red_sum','desc');
                    break;
                case 'orange':
                    $this->db->order_by('orange_sum','desc');
                    break;
            }
        }
        $this->db->order_by($order_by,$direct);
        if ($limit) {
            $this->db->limit($limit,$offset);
        }

        $results=$this->db->get()->result_array();

        $out_array=array();
        $curtime=time();
        $diff=86400;
        foreach ($results as $row) {
            $row['itemnameclass']='';
            if ($curtime-$row['updtime']<$diff) {
                $row['itemnameclass']='nearlyupdate';
            }
            if ($row['item_template']=='Other Item') {
                $row['update_class'] ='';
                $row['update'] ='';
                $row['price_25_class'] ='white';
                $row['price_50_class'] ='white';
                $row['price_75_class'] ='white';
                $row['price_150_class'] = 'white';
                $row['price_250_class'] = 'white';
                $row['price_500_class'] = 'white';
                $row['price_1000_class'] = 'white';
                $row['price_2500_class'] = 'white';
                $row['price_3000_class'] = 'white';
                $row['price_5000_class'] = 'white';
                $row['price_10000_class'] = 'white';
                $row['price_20000_class'] = 'white';
                $row['price_print_class'] = 'white';
                $row['price_setup_class'] ='white';
                $row['vendor_id'] =0;
                $row['vendor_name'] ='none';
                $this->db->select('pm.item_qty, pm.price, pm.sale_price,v.vendor_item_cost');
                $this->db->from('sb_promo_price pm');
                $this->db->join('sb_items i','i.item_id=pm.item_id');
                $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
                $this->db->where('pm.item_id',$row['item_id']);
                $this->db->order_by('item_qty');
                $res=$this->db->get()->result_array();

                $i=0;
                foreach ($this->price_types as $type) {
                    if ($type['type']=='print') {

                    } elseif ($type['type']=='setup') {

                    } else {
                        $row['item_profit_'.$type['type']]='';
                        $row['item_profitperc_'.$type['type']]='';
                        $row['item_profitclass_'.$type['type']]='';
                        $row['item_price_'.$type['type']]='';
                        if (isset($res[$i]['item_qty'])) {
                            if (floatval($res[$i]['sale_price'])>0) {
                                $row['item_price_'.$type['type']]=$res[$i]['sale_price'];
                            } elseif (floatval($res[$i]['price'])>0) {
                                $row['item_price_'.$type['type']]=$res[$i]['price'];
                            } else {
                                $row['item_price_'.$type['type']]='';
                            }
                            if ($row['item_price_'.$type['type']]!='' && floatval($res[$i]['vendor_item_cost'])!=0) {
                                /* Calculate profit */
                                $basecost=$row['item_price_'.$type['type']];
                                $profit=($basecost-$res[$i]['vendor_item_cost'])*intval($res[$i]['item_qty']);
                                $profit_perc=round(($profit/($basecost*intval($res[$i]['item_qty']))*100),0);
                                $row['item_profit_'.$type['type']]=$profit;
                                $row['item_profitperc_'.$type['type']]=$profit_perc;
                                $row['item_profitclass_'.$type['type']]=profit_bgclass($profit_perc);
                            }
                        }

                    }
                    $i++;
                }
            } else {
                $row['vendor_id']=0;
                $row['vendor_name']='none';
                foreach ($this->price_types as $type) {
                    if ($type['type']=='print') {

                    } else {
                        if ($row['item_price_'.$type['type']]!='') {
                            $this->db->select('vp.other_vendorprice_vendor as vendor_id, v.other_vendor_name as vendor_name');
                            $this->db->from('sb_other_vendor_price vp');
                            $this->db->join('sb_other_vendor v','v.other_vendor_id=vp.other_vendorprice_vendor');
                            $this->db->where('vp.other_vendorprice_item',$row['item_id']);
                            $this->db->where("coalesce(vp.other_vendorprice_price_".$type['type'].",0) > ",0);
                            $this->db->where('vp.other_vendorprice_price_'.$type['type'].' <= ',$row['item_price_'.$type['type']]);
                            $vres=$this->db->get()->result_array();
                            if (count($vres)==1) {
                                if ($row['vendor_id']==0) {
                                    $row['vendor_id']=$vres[0]['vendor_id'];
                                    $row['vendor_name']=$vres[0]['vendor_name'];
                                } elseif ($row['vendor_id']!=$vres[0]['vendor_id']) {
                                    $row['vendor_id']=-1;
                                    $row['vendor_name']='Multiple';
                                    break;
                                }
                            } elseif(count($vres)>1) {
                                $row['vendor_id']=-1;
                                $row['vendor_name']='Multiple';
                                break;

                            }
                        }

                    }
                }
            }
            if ($row['last_upd']) {
                if (time()-strtotime($row['last_upd'])>=(7*24*60*60)) {
                    $row['update_class']='updatelastprice';
                } else {
                    $row['update_class']='';
                }
                $row['update']=date('y-md',strtotime($row['last_upd']));
            } else {
                $row['update_class']='';
                $row['update']='';
            }
            $out_array[]=$row;
        }
        return $out_array;
    }

    public function get_compared_pricelimit($order_by, $direct, $limit, $offset, $search, $compareprefs, $vendor_id, $othervend) {
        $this->db->select('item.item_id,item.item_number,item.item_name,item.item_template');
        $this->db->select('price_25 as item_price_25, profit_25 as item_profitperc_25, profit_25_class as item_profitclass_25, profit_25_sum as item_profit_25');
        $this->db->select('price_50 as item_price_50, profit_50 as item_profitperc_50, profit_50_class as item_profitclass_50, profit_50_sum as item_profit_50');
        $this->db->select('price_75 as item_price_75, profit_75 as item_profitperc_75, profit_75_class as item_profitclass_75, profit_75_sum as item_profit_75');
        $this->db->select('price_150 as item_price_150, profit_150 as item_profitperc_150, profit_150_class as item_profitclass_150, profit_150_sum as item_profit_150');
        $this->db->select('price_250 as item_price_250, profit_250 as item_profitperc_250, profit_250_class as item_profitclass_250, profit_250_sum as item_profit_250');
        $this->db->select('price_500 as item_price_500, profit_500 as item_profitperc_500, profit_500_class as item_profitclass_500, profit_500_sum as item_profit_500');
        $this->db->select('price_1000 as item_price_1000, profit_1000 as item_profitperc_1000, profit_1000_class as item_profitclass_1000, profit_1000_sum as item_profit_1000');
        $this->db->select('price_2500 as item_price_2500, profit_2500 as item_profitperc_2500, profit_2500_class as item_profitclass_2500, profit_2500_sum as item_profit_2500');
        $this->db->select('price_3000 as item_price_3000, profit_3000 as item_profitperc_3000, profit_3000_class as item_profitclass_3000, profit_3000_sum as item_profit_3000, price_3000_class');
        $this->db->select('price_5000 as item_price_5000, profit_5000 as item_profitperc_5000, profit_5000_class as item_profitclass_5000, profit_5000_sum as item_profit_5000, price_5000_class');
        $this->db->select('price_10000 as item_price_10000, profit_10000 as item_profitperc_10000, profit_10000_class as item_profitclass_10000, profit_10000_sum as item_profit_10000');
        $this->db->select('price_20000 as item_price_20000, profit_20000 as item_profitperc_20000, profit_20000_class as item_profitclass_20000, profit_20000_sum as item_profit_20000');
        $this->db->select('price_setup as item_price_setup, profit_setup as item_profitperc_setup, profit_setup_class as item_profitclass_setup, profit_setup_sum as item_profit_setup, last_upd');
        /* Other Prices */
        $this->db->select('otherprice_25, otherprice_50, otherprice_75, otherprice_150, otherprice_250, otherprice_500, otherprice_1000, otherprice_2500, otherprice_3000, otherprice_5000, otherprice_10000, otherprice_20000, otherprice_setup');
        /* Summ by type of price classes */
        $this->db->select("if(price_25_class='empty',1,0)+if(price_50_class='empty',1,0)+if(price_75_class='empty',1,0)+if(price_150_class='empty',1,0)+if(price_250_class='empty',1,0)+if(price_500_class='empty',1,0)+if(profit_1000_class='empty',1,0)+if(price_2500_class='empty',1,0)+if(price_3000_class='empty',1,0)+if(price_5000_class='empty',1,0)+if(price_10000_class='empty',1,0)+if(price_20000_class='empty',1,0)+if(price_setup_class='empty',1,0) as empty_sum",FALSE);
        $this->db->select("if(price_25_class='white',1,0)+if(price_50_class='white',1,0)+if(price_75_class='white',1,0)+if(price_150_class='white',1,0)+if(price_250_class='white',1,0)+if(price_500_class='white',1,0)+if(profit_1000_class='white',1,0)+if(price_2500_class='white',1,0)+if(price_3000_class='white',1,0)+if(price_5000_class='white',1,0)+if(price_10000_class='white',1,0)+if(price_20000_class='white',1,0)+if(price_setup_class='white',1,0) as white_sum",FALSE);
        $this->db->select("if(price_25_class='pink',1,0)+if(price_50_class='pink',1,0)+if(price_75_class='pink',1,0)+if(price_150_class='pink',1,0)+if(price_250_class='pink',1,0)+if(price_500_class='pink',1,0)+if(profit_1000_class='pink',1,0)+if(price_2500_class='pink',1,0)+if(price_3000_class='pink',1,0)+if(price_5000_class='pink',1,0)+if(price_10000_class='pink',1,0)+if(price_20000_class='pink',1,0)+if(price_setup_class='pink',1,0) as pink_sum",FALSE);
        $this->db->select("if(price_25_class='orange',1,0)+if(price_50_class='orange',1,0)+if(price_75_class='orange',1,0)+if(price_150_class='orange',1,0)+if(price_250_class='orange',1,0)+if(price_500_class='orange',1,0)+if(profit_1000_class='orange',1,0)+if(price_2500_class='orange',1,0)+if(price_3000_class='orange',1,0)+if(price_5000_class='orange',1,0)+if(price_10000_class='orange',1,0)+if(price_20000_class='orange',1,0)+if(price_setup_class='orange',1,0) as orange_sum",FALSE);
        $this->db->select("if(price_25_class='red',1,0)+if(price_50_class='red',1,0)+if(price_75_class='red',1,0)+if(price_150_class='red',1,0)+if(price_250_class='red',1,0)+if(price_500_class='red',1,0)+if(profit_1000_class='red',1,0)+if(price_2500_class='red',1,0)+if(price_3000_class='red',1,0)+if(price_5000_class='red',1,0)+if(price_10000_class='red',1,0)+if(price_20000_class='red',1,0)+if(price_setup_class='red',1,0) as red_sum",FALSE);
        $this->db->select('unix_timestamp(item.update_time) as updtime');
        $this->db->from('v_otherpriceprofits');
        $this->db->join('sb_items item', 'item.item_id=v_otherpriceprofits.item_id');
        /* Apply Limit */
        if ($search!='') {
            $where="lower(concat(item.item_number,item.item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        if ($vendor_id) {
            // $this->db->join('sb_vendor_items v','v.vendor_item_id=v_otherpriceprofits.item_id');
            // $this->db->where('v.vendor_item_vendor',$vendor_id);
            $this->db->where('vendor_id',$vendor_id);
        }
        if ($compareprefs!='') {
            switch ($compareprefs) {
                case 'white':
                    $this->db->order_by('white_sum','desc');
                    break;
                case 'pink':
                    $this->db->order_by('pink_sum','desc');
                    break;
                case 'red':
                    $this->db->order_by('red_sum','desc');
                    break;
                case 'orange':
                    $this->db->order_by('orange_sum','desc');
                    break;
            }
        }
        $this->db->order_by($order_by,$direct);
        if ($limit) {
            $this->db->limit($limit,$offset);
        }

        $results=$this->db->get()->result_array();
        $out=array();
        $curtime=time();
        $diff=86400;
        foreach ($results as $row) {
            $row['itemnameclass']='';
            if ($curtime-$row['updtime']<$diff) {
                $row['itemnameclass']='nearlyupdate';
            }
            if ($row['item_template']=='Other Item') {
                $row['update_class'] ='';
                $row['update'] ='';
                $row['price_25_class'] ='white';
                $row['price_50_class'] ='white';
                $row['price_150_class'] = 'white';
                $row['price_250_class'] = 'white';
                $row['price_500_class'] = 'white';
                $row['price_1000_class'] = 'white';
                $row['price_2500_class'] = 'white';
                $row['price_5000_class'] = 'white';
                $row['price_10000_class'] = 'white';
                $row['price_20000_class'] = 'white';
                $row['price_print_class'] = 'white';
                $row['price_setup_class'] ='white';
                $row['vendor_id'] =0;
                $row['vendor_name'] ='none';
                $this->db->select('pm.item_qty, pm.price, pm.sale_price,v.vendor_item_cost');
                $this->db->from('sb_promo_price pm');
                $this->db->join('sb_items i','i.item_id=pm.item_id');
                $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
                $this->db->where('pm.item_id',$row['item_id']);
                $this->db->order_by('item_qty');
                $res=$this->db->get()->result_array();

                $i=0;
                foreach ($this->price_types as $type) {
                    if ($type['type']=='print') {

                    } elseif ($type['type']=='setup') {

                    } else {
                        $row['item_profit_'.$type['type']]='';
                        $row['item_profitperc_'.$type['type']]='';
                        $row['item_profitclass_'.$type['type']]='';
                        $row['item_price_'.$type['type']]='';
                        if (isset($res[$i]['item_qty'])) {
                            if (floatval($res[$i]['sale_price'])>0) {
                                $row['item_price_'.$type['type']]=$res[$i]['sale_price'];
                            } elseif (floatval($res[$i]['price'])>0) {
                                $row['item_price_'.$type['type']]=$res[$i]['price'];
                            } else {
                                $row['item_price_'.$type['type']]='';
                            }
                            if ($row['item_price_'.$type['type']]!='' && floatval($res[$i]['vendor_item_cost'])!=0) {
                                /* Calculate profit */
                                $basecost=$row['item_price_'.$type['type']];
                                $profit=($basecost-$res[$i]['vendor_item_cost'])*intval($res[$i]['item_qty']);
                                $profit_perc=round(($profit/($basecost*intval($res[$i]['item_qty']))*100),0);
                                $row['item_profit_'.$type['type']]=$profit;
                                $row['item_profitperc_'.$type['type']]=$profit_perc;
                                $row['item_profitclass_'.$type['type']]=profit_bgclass($profit_perc);
                            }
                        }

                    }
                    $i++;
                }
            } else {
                // Stressball
                $row['vendor_id']=0;
                $row['vendor_name']='none';

                foreach ($this->price_types as $type) {
                    if ($type['type']=='print') {

                    } else {
                        $row['price_'.$type['type'].'_class']='empty';
                        if ($row['item_price_'.$type['type']]!='') {
                            $itemprice=$row['item_price_'.$type['type']];
                            $reschk=$this->compare_price($row['item_id'], $type['type'], $itemprice, $othervend);

                            $row['price_'.$type['type'].'_class']=$reschk['class'];
                            if ($row['vendor_id']!=-1) {
                                $this->db->select('vp.other_vendorprice_vendor as vendor_id, v.other_vendor_name as vendor_name');
                                $this->db->from('sb_other_vendor_price vp');
                                $this->db->join('sb_other_vendor v','v.other_vendor_id=vp.other_vendorprice_vendor');
                                $this->db->where('vp.other_vendorprice_item',$row['item_id']);
                                $this->db->where("coalesce(vp.other_vendorprice_price_".$type['type'].",0) > ",0);
                                $this->db->where('vp.other_vendorprice_price_'.$type['type'].' <= ',$row['item_price_'.$type['type']]);
                                $this->db->where_in('vp.other_vendorprice_vendor',$othervend);
                                $vres=$this->db->get()->result_array();
                                if (count($vres)==1) {
                                    if ($row['vendor_id']==0) {
                                        $row['vendor_id']=$vres[0]['vendor_id'];
                                        $row['vendor_name']=$vres[0]['vendor_name'];
                                    } elseif ($row['vendor_id']!=$vres[0]['vendor_id']) {
                                        $row['vendor_id']=-1;
                                        $row['vendor_name']='Multiple';
                                    }
                                } elseif(count($vres)>1) {
                                    $row['vendor_id']=-1;
                                    $row['vendor_name']='Multiple';
                                }
                            }
                        }
                    }
                }
            }
            if ($row['last_upd']) {
                if (time()-strtotime($row['last_upd'])>=(7*24*60*60)) {
                    $row['update_class']='updatelastprice';
                } else {
                    $row['update_class']='';
                }
                $row['update']=date('y-md',strtotime($row['last_upd']));
            } else {
                $row['update_class']='';
                $row['update']='';
            }
            $out[]=$row;

        }
        return $out;
    }

    /* Function which compare price with Competiotors and return - Competiotor, Price_class, */
    private function compare_price($item_id, $price_type, $item_price, $competitors) {
        $out=array('class'=>'empty','price'=>'');
        $mindiff=($this->config->item('price_diff')/100);
        $field='other_vendorprice_price_'.$price_type;
        $this->db->select('min(coalesce('.$field.',999999)) as minprice',FALSE);
        $this->db->from('sb_other_vendor_price');
        $this->db->where('other_vendorprice_item',$item_id);
        $this->db->where_in('other_vendorprice_vendor',$competitors);
        $result=$this->db->get()->row_array();
        $othprice=$result['minprice'];
        if ($othprice!='999999') {
            $out['price']=$othprice;
            // Empty prices
            if ($item_price==$othprice) {
                $out['class']='orange';
            } elseif ($item_price>$othprice) {
                $out['class']='red';
            } elseif (($othprice-$item_price)<($mindiff/100)) {
                $out['class']='white';
            } else {
                $out['class']='pink';
            }
        }
        return $out;
    }


    public function get_othervendors() {
        $this->db->select('other_vendor_id, other_vendor_name');
        $this->db->from('sb_other_vendor');
        $this->db->order_by('other_vendor_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

}