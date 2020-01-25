<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Prices_model extends My_Model
{

    private $MAX_PROMOPRICES = 10;

    function __construct() {
        parent::__construct();
    }

    public function get_item_profitprefs($order,$direc,$limit=0,$offset=0,$search='',$profitpref='',$vendor_id='') {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_template, i.vendor_name, i.vendor_item_cost');
        $this->db->select('i.price_25, i.price_50, i.price_75, i.price_150');
        $this->db->select('i.price_250, i.price_500, i.price_1000, i.price_2500, i.price_3000, i.price_5000, i.price_10000');
        $this->db->select('i.price_20000, i.price_print, i.price_setup, i.profit_25, i.profit_25_class, i.profit_50');
        $this->db->select('i.profit_50_class, i.profit_75, i.profit_75_class, i.profit_150, i.profit_150_class');
        $this->db->select('i.profit_250, i.profit_250_class, i.profit_500, i.profit_500_class, i.profit_1000, i.profit_1000_class');
        $this->db->select('i.profit_2500, i.profit_2500_class, i.profit_5000, i.profit_5000_class, i.profit_3000, i.profit_3000_class');
        $this->db->select('i.profit_10000, i.profit_10000_class, i.profit_20000, i.profit_20000_class, i.profit_print, i.profit_print_class');
        $this->db->select('i.profit_setup, i.profit_setup_class');
        $this->db->select("if(profit_25_class='empty',1,0)+if(profit_75_class='empty',1,0)+if(profit_150_class='empty',1,0)+if(profit_250_class='empty',1,0)+if(profit_500_class='empty',1,0)+if(profit_1000_class='empty',1,0)+if(profit_3000_class='empty',1,0)+if(profit_5000_class='empty',1,0)+if(profit_10000_class='empty',1,0)+if(profit_20000_class='empty',1,0)+if(profit_print_class='empty',1,0)+if(profit_setup_class='empty',1,0) as empty_sum",FALSE);
        $this->db->select("if(profit_25_class='black',1,0)+if(profit_75_class='black',1,0)+if(profit_150_class='black',1,0)+if(profit_250_class='black',1,0)+if(profit_500_class='black',1,0)+if(profit_1000_class='black',1,0)+if(profit_3000_class='black',1,0)+if(profit_5000_class='black',1,0)+if(profit_10000_class='black',1,0)+if(profit_20000_class='black',1,0)+if(profit_print_class='black',1,0)+if(profit_setup_class='black',1,0) as black_sum",FALSE);
        $this->db->select("if(profit_25_class='maroon',1,0)+if(profit_75_class='maroon',1,0)+if(profit_150_class='maroon',1,0)+if(profit_250_class='maroon',1,0)+if(profit_500_class='maroon',1,0)+if(profit_1000_class='maroon',1,0)+if(profit_3000_class='maroon',1,0)+if(profit_5000_class='maroon',1,0)+if(profit_10000_class='maroon',1,0)+if(profit_20000_class='maroon',1,0)+if(profit_print_class='maroon',1,0)+if(profit_setup_class='maroon',1,0) as maroon_sum,",FALSE);
        $this->db->select("if(profit_25_class='red',1,0)+if(profit_75_class='red',1,0)+if(profit_150_class='red',1,0)+if(profit_250_class='red',1,0)+if(profit_500_class='red',1,0)+if(profit_1000_class='red',1,0)+if(profit_3000_class='red',1,0)+if(profit_5000_class='red',1,0)+if(profit_10000_class='red',1,0)+if(profit_20000_class='red',1,0)+if(profit_print_class='red',1,0)+if(profit_setup_class='red',1,0) as red_sum",FALSE);
        $this->db->select("if(profit_25_class='orange',1,0)+if(profit_75_class='orange',1,0)+if(profit_150_class='orange',1,0)+if(profit_250_class='orange',1,0)+if(profit_500_class='orange',1,0)+if(profit_1000_class='orange',1,0)+if(profit_3000_class='orange',1,0)+if(profit_5000_class='orange',1,0)+if(profit_10000_class='orange',1,0)+if(profit_20000_class='orange',1,0)+if(profit_print_class='orange',1,0)+if(profit_setup_class='orange',1,0) as orange_sum",FALSE);
        $this->db->select("if(profit_25_class='white',1,0)+if(profit_75_class='white',1,0)+if(profit_150_class='white',1,0)+if(profit_250_class='white',1,0)+if(profit_500_class='white',1,0)+if(profit_1000_class='white',1,0)+if(profit_3000_class='white',1,0)+if(profit_5000_class='white',1,0)+if(profit_10000_class='white',1,0)+if(profit_20000_class='white',1,0)+if(profit_print_class='white',1,0)+if(profit_setup_class='white',1,0) as white_sum",FALSE);
        $this->db->select("if(profit_25_class='green',1,0)+if(profit_75_class='green',1,0)+if(profit_150_class='green',1,0)+if(profit_250_class='green',1,0)+if(profit_500_class='green',1,0)+if(profit_1000_class='green',1,0)+if(profit_3000_class='green',1,0)+if(profit_5000_class='green',1,0)+if(profit_10000_class='green',1,0)+if(profit_20000_class='green',1,0)+if(profit_print_class='green',1,0)+if(profit_setup_class='green',1,0) as green_sum",FALSE);
        $this->db->select('unix_timestamp(item.update_time) as updtime');
        $this->db->from('v_stressprofits i');
        $this->db->join('sb_items item','item.item_id=i.item_id');
        if ($search!='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".strtolower($search)."%'";
            $this->db->where($where);
        }
        if ($vendor_id) {
            // $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
            // $this->db->where('v.vendor_item_vendor',$vendor_id);
            $this->db->where('i.vendor_id',$vendor_id);
        }
        if ($profitpref!='') {
            switch ($profitpref) {
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
            if ($row['item_template']=='Other Item') {
                $this->db->select('item_qty, price,sale_price, profit');
                $this->db->from('sb_promo_price');
                $this->db->where('item_id',$row['item_id']);
                $this->db->order_by('item_qty');
                $promos=$this->db->get()->result_array();
                $i=0;
                $price_types = $this->config->item('price_types');
                foreach ($price_types as $type) {
                    $row['profit_'.$type['type']]='';
                    $row['profit_'.$type['type'].'_class']='empty';
                    if (isset($promos[$i]['item_qty'])) {
                        $basecost='';
                        if (floatval($promos[$i]['sale_price'])>0) {
                            $basecost=$promos[$i]['sale_price'];
                        } elseif (floatval($promos[$i]['price'])>0) {
                            $basecost=$promos[$i]['price'];
                        }
                        if ($basecost!='' && $promos[$i]['profit']!='') {
                            $profit=$promos[$i]['profit'];
                            $profit_perc=($profit/($basecost*$promos[$i]['item_qty']))*100;
                            $row['profit_'.$type['type']]=round($profit_perc,0);
                            $row['profit_'.$type['type'].'_class']=profit_bgclass($profit_perc);
                        }
                    }
                    $i++;
                }
            }
            $out_array[]=$row;
        }
        return $out_array;
    }

    public function get_promoprices_edit($item_id) {
        $this->db->select('*');
        $this->db->from('sb_item_prices');
        $this->db->where('item_price_itemid',$item_id);
        $result=$this->db->get()->row_array();
        if (!isset($result['item_price_id'])) {
            $result['item_price_id']=0;
            $result['item_price_setup']=0;
            $result['item_sale_setup']=0;
            $result['item_price_print']=0;
            $result['item_sale_print']=0;
            $result['profit_print']='';
            $result['profit_setup']='';
            $result['profit_print_perc']='';
            $result['profit_setup_perc']='';
            $result['profit_print_class']='empty';
            $result['profit_setup_class']='empty';
        } else {
            $base=0;
            $result['profit_setup_perc']='';
            $result['profit_setup_class']='empty';
            $result['profit_print_perc']='';
            $result['profit_print_class']='empty';
            if (floatval($result['item_sale_setup'])!=0) {
                $base=floatval($result['item_sale_setup']);
            } elseif (floatval($result['item_price_setup'])!=0) {
                $base=floatval($result['item_price_setup']);
            }
            if ($base!=0 && floatval($result['profit_setup'])!=0) {
                $result['profit_setup_perc']=round($result['profit_setup']/$base*100,0);
                $result['profit_setup_class']=profit_bgclass($result['profit_setup_perc']);
            }
            $base=0;
            if (floatval($result['item_sale_print'])!=0) {
                $base=floatval($result['item_sale_print']);
            } elseif (floatval($result['item_price_print'])!=0) {
                $base=floatval($result['item_price_print']);
            }
            if ($base!=0 && floatval($result['profit_print'])!=0) {
                $result['profit_print_perc']=round($result['profit_print']/$base*100,0);
                $result['profit_print_class']=profit_bgclass($result['profit_print_perc']);
            }
        }
        $this->db->select('*');
        $this->db->from('sb_promo_price');
        $this->db->where('item_id',$item_id);
        $this->db->order_by('item_qty');
        $res=$this->db->get()->result_array();
        $i=0;
        $prices=array();
        foreach ($res as $row) {
            $base=0;
            $profit_perc='';
            $profit_class='empty';
            if (floatval($row['sale_price'])!=0) {
                $base=$row['sale_price'];
            } elseif (floatval($row['price'])!=0) {
                $base=$row['price'];
            }
            if ($base!=0 && floatval($row['profit'])!=0) {
                $profit_perc=round($row['profit']/($base*$row['item_qty'])*100,0);
                $profit_class=profit_bgclass($profit_perc);
            }
            $prices[]=array(
                'promo_price_id'=>$row['promo_price_id'],
                'item_qty'=>$row['item_qty'],
                'price'=>$row['price'],
                'sale_price'=>$row['sale_price'],
                'profit'=>$row['profit'],
                'profit_perc'=>$profit_perc,
                'profit_class'=>$profit_class,
            );
            $i++;
        }
        for ($j=$i; $j < $this->MAX_PROMOPRICES; $j++) {
            $prices[]=array(
                'promo_price_id'=>$j*(-1),
                'item_qty'=>'',
                'price'=>'',
                'sale_price'=>'',
                'profit'=>'',
                'profit_perc'=>'',
                'profit_class'=>'empty',
            );
        }
        return array('qty_prices'=>$prices,'common_prices'=>$result);
    }

    public function get_price_itemedit($item_id) {
        $this->db->select('*');
        $this->db->from('sb_item_prices');
        $this->db->where('item_price_itemid',$item_id);
        $result = $this->db->get()->row_array();
        if (!isset($result['item_price_id'])) {
            $result=array('item_price_id'=>'',
                'item_price_itemid'=>$item_id,
            );
            foreach ($this->price_types as $row) {
                $result['item_price_'.$row['type']]='';
                $result['item_sale_'.$row['type']]='';
                $result['profit_'.$row['type']]='';
                $result['profit_'.$row['type'].'_perc']='';
                $result['profit_'.$row['type'].'_class']='empty';
            }
            $result['item_price_print']='';
            $result['item_sale_print']='';
            $result['profit_print']='';
            $result['profit_print_perc']='';
            $result['profit_print_class']='empty';
            $result['item_price_setup']='';
            $result['item_sale_setup']='';
            $result['profit_setup']='';
            $result['profit_setup_perc']='';
            $result['profit_setup_class']='empty';
        } else {
            $price_types = $this->config->item('price_types');
            foreach ($price_types as $row) {
                $result['profit_'.$row['type'].'_perc']='';
                $result['profit_'.$row['type'].'_class']='empty';
                if ($result['profit_'.$row['type']]) {
                    $base=0;
                    if (floatval($result['item_sale_'.$row['type']])!=0) {
                        $base=floatval($result['item_sale_'.$row['type']]);
                    } elseif (floatval($result['item_price_'.$row['type']])) {
                        $base=  floatval($result['item_price_'.$row['type']]);
                    }
                    if ($base!=0) {
                        $profit_perc=$result['profit_'.$row['type']]/($base*$row['base'])*100;
                        $profit_class=profit_bgclass($profit_perc);
                        $result['profit_'.$row['type'].'_perc']=round($profit_perc,0);
                        $result['profit_'.$row['type'].'_class']=$profit_class;
                    }
                }
            }
            $result['profit_print_perc']='';
            $result['profit_print_class']='empty';
            if (floatval($result['profit_print'])!=0) {
                $base=0;
                if (floatval($result['item_sale_print'])!=0) {
                    $base=floatval($result['item_sale_print']);
                } elseif (floatval($result['item_price_print'])) {
                    $base=floatval($result['item_price_print']);
                }
                if ($base!=0) {
                    $profit_perc = $result['profit_print']/$base* 100;
                    $profit_class = profit_bgclass($profit_perc);
                    $result['profit_print_perc'] = round($profit_perc,0);
                    $result['profit_print_class'] = $profit_class;
                }

            }
            $result['profit_setup_perc']='';
            $result['profit_setup_class']='empty';
            if (floatval($result['profit_print'])!=0) {
                $base=0;
                if (floatval($result['item_sale_setup'])!=0) {
                    $base=floatval($result['item_sale_setup']);
                } elseif (floatval($result['item_price_setup'])!=0) {
                    $base=floatval($result['item_price_setup']);
                }
                if ($base!=0) {
                    $profit_perc=$result['profit_setup']/$base*100;
                    $profit_class=profit_bgclass($profit_perc);
                    $result['profit_setup_perc']=round($profit_perc,0);
                    $result['profit_setup_class']=$profit_class;

                }
            }
        }
        return $result;
    }

    function recalc_special_profit($vendor_price, $spec_qty, $spec_price ) {
        $base_prof=$vendor_price[0]['vendorprice_color'];
        /* Init Profits array */
        foreach ($vendor_price as $row) {
            if (intval($row['vendorprice_qty'])>0 && $row['vendorprice_qty']<$spec_qty) {
                $base_prof=floatval($row['vendorprice_color'])==0 ? floatval($row['vendorprice_val']) : $row['vendorprice_color'];
            }
        }
        $profitval=($spec_price-$base_prof)*$spec_qty;
        return $profitval;
    }



}