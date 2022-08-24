<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Prices_model extends My_Model
{

    private $MAX_PROMOPRICES = 10;

    function __construct() {
        parent::__construct();
    }

    public function get_item_profitprefs($order,$direc,$limit=0,$offset=0,$search='',$profitpref='',$vendor_id='', $brand='ALL') {
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
        if ($brand!=='ALL') {
            $this->db->where('item.brand', $brand);
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
        $price_types = $this->config->item('price_types');
        if (!isset($result['item_price_id'])) {
            $result=array('item_price_id'=>'',
                'item_price_itemid'=>$item_id,
            );
            foreach ($price_types as $row) {
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

    public function recalc_special_profit($vendor_price, $spec_qty, $spec_price ) {
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

    // Recalc Stress Profit
    public function recalc_stress_profit($prices, $vendprices, $price_types) {
        /* IDX of Vendor Prices */
        /* Init Profits array */
        $profits=array();
        foreach ($price_types as $row) {
            $base_cost=0;
            if (floatval($prices['item_sale_'.$row['type']])!=0) {
                $base_cost=floatval($prices['item_sale_'.$row['type']]);
            } elseif (floatval($prices['item_price_'.$row['type']])!=0) {
                $base_cost=floatval($prices['item_price_'.$row['type']]);
            }
            $profits[]=array(
                'type'=>'qty',
                'base'=>$row['type'],
                'vendprice'=>$prices['base_cost'],
                'base_cost'=>$base_cost,
                'profit'=>'',
                'profit_perc'=>'',
                'profit_class'=>'empty',
            );
        }
        /* Add 2 special prices */
        $base_cost=0;
        if (floatval($prices['item_sale_print'])!=0) {
            $base_cost=floatval($prices['item_sale_print']);
        } elseif(floatval($prices['item_price_print'])) {
            $base_cost=floatval($prices['item_price_print']);
        }
        $profits[]=array(
            'type'=>'print',
            'base'=>1,
            'base_cost'=>$base_cost,
            'vendprice'=>(floatval($prices['vendor_item_exprint'])==0 ? 0 : floatval($prices['vendor_item_exprint'])),
            'profit'=>'',
            'profit_perc'=>'',
            'profit_class'=>'empty',
        );
        $base_cost=0;
        if (floatval($prices['item_sale_setup'])!=0) {
            $base_cost=floatval($prices['item_sale_setup']);
        } elseif(floatval($prices['item_price_setup'])) {
            $base_cost=floatval($prices['item_price_setup']);
        }
        $profits[]=array(
            'type'=>'setup',
            'base'=>1,
            'base_cost'=>$base_cost,
            'vendprice'=>(floatval($prices['vendor_item_setup'])==0 ? 0 : floatval($prices['vendor_item_setup'])),
            'profit'=>'',
            'profit_perc'=>'',
            'profit_class'=>'empty',
        );
        $new_profit=$this->recalc_profit($vendprices, $profits);
        $profit=array();
        foreach ($new_profit as $profrow) {
            if ($profrow['type']=='qty') {
                $profit['profit_'.$profrow['base']]=$profrow['profit'];
                $profit['profit_'.$profrow['base'].'_perc']=$profrow['profit_perc'];
                $profit['profit_'.$profrow['base'].'_class']=$profrow['profit_class'];
            } elseif ($profrow['type']=='print') {
                $profit['profit_print']=$profrow['profit'];
                $profit['profit_print_perc']=$profrow['profit_perc'];
                $profit['profit_print_class']=$profrow['profit_class'];
            } elseif ($profrow['type']=='setup') {
                $profit['profit_setup']=$profrow['profit'];
                $profit['profit_setup_perc']=$profrow['profit_perc'];
                $profit['profit_setup_class']=$profrow['profit_class'];
            }
        }
        return $profit;
    }

    // Recalc Promo Profit
    public function recalc_promo_profit($prices, $vendprices, $commonprices)
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

    public function get_item_pricebytype($item_id,$price_type) {
        switch ($price_type) {
            case 'setup':
                $select_fld='item_price_setup as price,item_sale_setup as sale';
                break;
            case 'imprint':
                $select_fld='item_price_print as price,item_sale_print as sale';
                break;
            default :
                $select_fld='item_price_'.$price_type.' as price,item_sale_'.$price_type.' as sale';
                break;

        }
        $select_fld.=', item_price_itemid as item_id';
        $this->db->select($select_fld,FALSE);
        $this->db->from('sb_item_prices');
        $this->db->where('item_price_itemid',$item_id);
        $res=$this->db->get()->row_array();
        $retprice = 0;
        if (isset($res['item_id'])) {
            if (floatval($res['sale'])>0) {
                $retprice = $res['sale'];
            } elseif ($res['sale']===0.00) {
                $retprice = 0;
            } elseif (floatval($res['price'])>0) {
                $retprice=$res['price'];
            }
        }
        return $retprice;
    }

    function get_item_pricebyval($item_id,$itemval) {
        $retprice=0;
        $this->db->select('*');
        $this->db->from('sb_item_prices');
        $this->db->where('item_price_itemid',$item_id);
        $res = $this->db->get()->row_array();
        $retprice = 0;
        $minval=25;
        $itemprice=array();$findlimit=0;
        foreach ($this->price_types as $row) {
            if (floatval($res['item_price_'.$row['type']])!=0 || floatval($res['item_sale_'.$row['type']])!=0) {
                if (floatval($res['item_sale_'.$row['type']])>0) {
                    $retprice=$res['item_sale_'.$row['type']];
                } elseif (floatval($res['item_price_'.$row['type']])>0) {
                    $retprice=$res['item_price_'.$row['type']];
                }
                if ($row['type']>$itemval) {
                    $findlimit=1;
                    break;
                }
                $itemprice[]=array(
                    'min'=>$minval,
                    'max'=>$row['base'],
                    'price'=>$retprice,
                );
                $minval=$row['base'];
            }
        }
        if ($findlimit==0) {
            $itemprice[]=array(
                'min'=>$minval,
                'max'=>9999999999999999,
                'price'=>$retprice,
            );
        }

        $maxidx=count($itemprice);
        if ($maxidx>0) {
            $retprice=$itemprice[$maxidx-1]['price'];
        }
        return $retprice;
    }

    public function get_itemlist_price($item_id) {
        $this->db->select('*');
        $this->db->from('sb_promo_price');
        $this->db->where('item_id', $item_id);
        $this->db->order_by('item_qty');
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function get_itemlist_specprice($item_id) {
        $this->db->select('item_price_id, item_price_print, item_sale_print, profit_print, item_price_setup, item_sale_setup, profit_setup');
        $this->db->select('item_price_repeat, item_sale_repeat, profit_repeat, item_price_pantone, item_sale_pantone, pantone_profit');
        $this->db->select('item_price_rush1, item_sale_rush1, rush1_profit, item_price_rush2, item_sale_rush2, rush2_profit');
        $this->db->from('sb_item_prices');
        $this->db->where('item_price_itemid', $item_id);
        $res = $this->db->get()->row_array();
        if (ifset($res, 'item_price_id', 0)==0) {
            // Not found
            $res = [
                'item_price_id' => -1,
                'item_price_print' => 0,
                'item_sale_print' => 0,
                'profit_print' => 0,
                'item_price_setup' => 0,
                'item_sale_setup' => 0,
                'profit_setup' => 0,
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
                'profit_rush2' => 0,
                'profit_rush2_class' => '',
                'profit_rush2_perc' => '',
                'item_price_pantone' => '',
                'item_sale_pantone' => '',
                'profit_pantone' => 0,
                'profit_pantone_class' => '',
                'profit_pantone_perc' => '',
            ];
        } else {
            $res['profit_print_class']=$res['profit_print_perc']=$res['profit_setup_class']=$res['profit_setup_perc']=$res['profit_repeat_class']=$res['profit_repeat_perc']='';
            $res['profit_rush1_class']=$res['profit_rush1_perc']=$res['profit_rush2_class']=$res['profit_rush2_perc']=$res['profit_pantone_class']=$res['profit_pantone_perc']='';
            if (floatval($res['item_sale_print'])!=0 && $res['profit_print']!==NULL) {
                $profit_perc = round(($res['profit_print']/$res['item_sale_print'])*100,1);
                $res['profit_print_perc'] =$profit_perc.'%';
                $res['profit_print_class'] = profit_bgclass($profit_perc);
            }
            if (floatval($res['item_sale_setup'])!=0 && $res['profit_setup']!==NULL) {
                $profit_perc = round($res['profit_setup']/$res['item_sale_setup']*100,1);
                $res['profit_setup_perc'] = $profit_perc.'%';
                $res['profit_setup_class'] = profit_bgclass($profit_perc);
            }
            if (floatval($res['item_sale_repeat'])!=0 && $res['profit_repeat']!==NULL) {
                $profit_perc = round($res['profit_repeat']/$res['item_sale_repeat']*100,1);
                $res['profit_repeat_perc'] = $profit_perc.'%';
                $res['profit_repeat_class'] = profit_bgclass($profit_perc);
            }
            if (floatval($res['item_sale_rush1'])!=0 && $res['profit_rush1']!==NULL) {
                $profit_perc = round($res['profit_rush1']/$res['item_sale_rush1']*100,1);
                $res['profit_rush1_perc'] = $profit_perc.'%';
                $res['profit_rush1_class'] = profit_bgclass($profit_perc);
            }
            if (floatval($res['item_sale_rush2'])!=0 && $res['profit_rush2']!==NULL) {
                $profit_perc = round($res['profit_rush2']/$res['item_sale_rush2']*100,1);
                $res['profit_rush2_perc'] = $profit_perc.'%';
                $res['profit_rush2_class'] = profit_bgclass($profit_perc);
            }
            if (floatval($res['item_sale_pantone'])!=0 && $res['profit_pantone']!==NULL) {
                $profit_perc = round($res['profit_pantone']/$res['item_sale_pantone']*100,1);
                $res['profit_pantone_perc'] = $profit_perc.'%';
                $res['profit_pantone_class'] = profit_bgclass($profit_perc);
            }

        }
        return $res;
    }
//    // Recalc profit for Promo Prices
//    public function recalc_promo_profit($vendor_prices, $item_prices) {
//        $priceidx = 0;
//        foreach ($item_prices  as $item_price) {
//            $item_prices[$priceidx]['profit']=$item_prices[$priceidx]['profit_class']=$item_prices[$priceidx]['profit_perc']='';
//            if (intval($item_price['item_qty'])>0 && (floatval($item_price['price']) > 0 || floatval($item_price['sale_price']) > 0)) {
//                // Calc
//                $basecost = (floatval($item_price['sale_price'])>0 ? $item_price['sale_price'] : $item_price['price']);
//                $baseqty = intval($item_price['item_qty']);
//                $vendqty = intval($vendor_prices[0]['vendorprice_qty']);
//                $vendprice = floatval($vendor_prices[0]['vendorprice_color']);
//                foreach ($vendor_prices as $vendor_price) {
//                    if (intval($vendor_price['vendorprice_qty'])>0 && intval($vendor_price['vendorprice_qty'])<=$baseqty && floatval($vendor_price['vendorprice_color'])>0) {
//                        $vendqty=intval($vendor_price['vendorprice_qty']);
//                        $vendprice = floatval($vendor_price['vendorprice_color']);
//                        if ($vendqty > 75) {
//                            $tt=1;
//                        }
//                    }
//                }
//
//                if ($vendprice>0) {
//                    $profit = ($basecost - $vendprice)*$baseqty;
//                    $profperc = round($profit / ($basecost * $baseqty) * 100, 2);
//                    $item_prices[$priceidx]['profit']=round($profit, 0);
//                    $item_prices[$priceidx]['profit_perc'] = round($profperc,0).'%';
//                    $item_prices[$priceidx]['profit_class']=profit_bgclass($profperc);
//                }
//            }
//            $priceidx++;
//        }
//        return $item_prices;
//    }
    // Recalc inprint and setup Profit
    public function recalc_setup_profit($item, $vendor_item) {
        // Imprint
        $item['profit_print']=$item['profit_print_class']=$item['profit_print_perc']='';
        $item['profit_setup']=$item['profit_setup_class']=$item['profit_setup_perc']='';
        $printbasecost = (floatval($item['item_sale_print'])>0 ? floatval($item['item_sale_print']) : floatval($item['item_price_print']));
        if ($printbasecost > 0) {
            $profit = round($printbasecost - floatval($vendor_item['vendor_item_exprint']),2);
            $profitperc = $profit / $printbasecost * 100;
            $item['profit_print']=$profit;
            $item['profit_print_perc']=round($profitperc,0).'%';
            $item['profit_print_class']=profit_bgclass($profitperc);
        }
        $setupbasecost = floatval($item['item_sale_setup'])>0 ? floatval($item['item_sale_setup']) : floatval($item['item_price_setup']);
        if ($setupbasecost > 0) {
            $profit = round($setupbasecost - floatval($vendor_item['vendor_item_setup']),2);
            $profitperc = $profit / $setupbasecost * 100;
            $item['profit_setup']=$profit;
            $item['profit_setup_perc']=round($profitperc,0).'%';
            $item['profit_setup_class']=profit_bgclass($profitperc);
        }
        return $item;
    }

    public function get_price_discounts() {
        $this->db->select('*');
        $this->db->from('sb_price_discounts');
        $res = $this->db->get()->result_array();
        return $res;
    }


}