<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Printshop_model extends MY_Model
{

    const ROW_UNCHANGED = 0;
    const ROW_INSERT = 1;
    const ROW_DELETE = 2;

    private $error_message='Unknown error. Try later';
    private $empty_html_content='&nbsp;';
    private $outstockclass='outstock';
    private $outstoklabel='Out of Stock';
    private $lowstockclass='lowstock';

    function __construct()
    {
        parent::__construct();
    }

    public function count_prinshop_items($options=array()) {
        $this->db->select('count(printshop_item_id) as cnt');
        $this->db->from('ts_printshop_colors');
        // Options - filters
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

//    public function count_inventory_pics($printshop_color_id) {
//        $this->db->select('count(printshop_pics_id) as cnt');
//        $this->db->from('ts_printshop_pics');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $res=$this->db->get()->row_array();
//        return $res['cnt'];
//    }

    public function invaddcost() {
        $this->db->select('inv_addcost');
        $this->db->from('ts_configs');
        $costres=$this->db->get()->row_array();
        return floatval($costres['inv_addcost']);
    }

//    // Update Addcost
//    public function inventory_addcost_upd($addcost) {
//        $cost=floatval(str_replace('$','',$addcost));
//        $this->db->set('inv_addcost', $cost);
//        $this->db->update('ts_configs');
//        return TRUE;
//    }
//    // OldPrintshop Colors
//    private function _oldgetprintshop_items() {
//        // Get Stock Data
//        $crow['have']=$this->printcolor_have($crow['printshop_color_id'])+1;
//        $crow['max'] = $crow['suggeststock'];
//        $crow['toget']=($crow['max']-$crow['have'])+$crow['reserved'];
//        $crow['instock']=$crow['have']+$crow['backup'];
//        $crow['specs'] = 0;
//        $crow['platetemp'] = 0;
//        $crow['prooftemp'] = 0;
//        $crow['pics'] = 0;
//        $crow['availabled'] = $crow['instock']-$crow['reserved'];
//        $stockperc=$stockavail=$this->empty_html_content;
//        $stockclass='';
//        if ($crow['instock']-$crow['reserved']!=0) {
//            $stockavail=$crow['instock']-$crow['reserved'];
//        }
//        $outavail=$crow['availabled'];
//
//        if ($crow['reserved']==0) {
//            $outreserved='-';
//            $reservclass='';
//        } else {
//            $outreserved='('.QTYOutput($crow['reserved']).')';
//            $reservclass='redtext';
//        }
//        if ($crow['backup']==0) {
//            $outbackup='-';
//            $backupclass='';
//        } else {
//            $outbackup=QTYOutput($crow['backup']);
//            $backupclass='redtext';
//        }
//        if ($crow['have']==0) {
//            $outhave='-';
//            $haveclass='';
//        } else {
//            $outhave=QTYOutput($crow['have']);
//            $haveclass='redtext';
//        }
//        if ($crow['max']==0) {
//            $outmax='-';
//            $maxclass='';
//        } else {
//            $outmax=QTYOutput($crow['max']);
//            $maxclass='redtext';
//        }
//        if ($crow['toget']==0) {
//            $outtoget='-';
//            $togetclass='';
//        } else {
//            $outtoget=QTYOutput($crow['toget']);
//            $togetclass='redtext';
//        }
//        if ($crow['specs']==0) {
//            $outspecs='-';
//            $specsclass='';
//        } else {
//            $outspecs=QTYOutput($crow['specs']);
//            $specsclass='redtext';
//        }
//        if ($crow['platetemp']==0) {
//            $outplatetemp='-';
//            $platetempclass='';
//        } else {
//            $outplatetemp=QTYOutput($crow['platetemp']);
//            $platetempclass='redtext';
//        }
//        if ($crow['prooftemp']==0) {
//            $outprooftemp='-';
//            $prooftempclass='';
//        } else {
//            $outprooftemp=QTYOutput($crow['prooftemp']);
//            $prooftempclass='redtext';
//        }
//        if ($crow['pics']==0) {
//            $outpics='-';
//            $picsclass='';
//        } else {
//            $outpics=QTYOutput($crow['pics']);
//            $picsclass='redtext';
//        }
//        if ($crow['instock']<=0 || $stockavail<=0) {
//            $stockclass=$this->outstockclass;
//        }
//        if ($crow['instock']<=0) {
//            $outinstock=$this->outstoklabel;
//        } else {
//            $outinstock=QTYOutput($crow['instock']);
//        }
//        if ($crow['suggeststock']!=0) {
//            $stockperc=round($crow['instock']/$crow['suggeststock']*100,0).'%';
//        }
//        if ($stockperc>0 && $stockperc<33) {
//            $stockclass='lowstock';
//        }
//        $totalea=($crow['price']!=0 ? $crow['price']+$addlcost : '-');
//        $ndecim=2;
//        $whole = floor($crow['price']*100);      // 1
//        $fraction = $crow['price']*100 - $whole; // .25
//        if (intval($fraction*10)>0) {
//            $ndecim=3;
//        }
//
//    }


    public function get_printshopitems($options=array()) {
        $addlcost=$this->invaddcost();
        $this->db->select('*');
        $this->db->from('ts_printshop_items');
        // Options where
        if (isset($options['orderby'])) {
            if (isset($options['direct'])) {
                $this->db->order_by($options['orderby'], $options['direct']);
            } else {
                $this->db->order_by($options['orderby']);
            }
        } else {
            $this->db->order_by('printshop_item_id', 'desc');
        }
        $res=$this->db->get()->result_array();
        $numpp=1;
        $total_invent=0;
        $inventory = array();
        $colorsdata=array();
        foreach ($res as $row) {
            // Add to colorsdat
            $colorsdata[]=array(
                'printshop_item_id'=>$row['printshop_item_id'],
                'printshop_color_id'=>0,
                'type'=>'item',
                'numpp'=>$this->empty_html_content,
                'item_num'=>$row['item_num'],
                'item_name'=>$row['item_name'],
            );
            $itemkey=count($colorsdata)-1;
            $inventory[]=array(
                'printshop_item_id'=>$row['printshop_item_id'],
                'printshop_color_id'=>0,
                'type'=>'item',
                'item_num'=>$row['item_num'],
                'item_name'=>$row['item_name'],
                'percenturl' => 'href="/fulfillment/max_item_percent/?id='.$row['printshop_item_id'].'"',
                'plate_temp'=>$row['plate_temp'],
                'proof_temp'=>$row['proof_temp'],
                'item_label'=>$row['item_label'],
                'platetemp'=>(empty($row['plate_temp']) ? 'empty' : 'full'),
                'prooftemp'=>(empty($row['proof_temp']) ? 'empty' : 'full'),
                'itemlabel'=>(empty($row['item_label']) ? 'empty' : 'full'),
            );
            // Get data by colors
            $colors=$this->get_item_colors($row['printshop_item_id']);
            $sum_available = 0;
            $sum_instock = 0;
            $sum_reserved = 0;
            $sum_max=0;
            foreach ($colors as $crow) {
                // Get data
                $income=$this->printcolor_income($crow['printshop_color_id'], $options['brand']);
                $outcome=$this->printcolor_outcome($crow['printshop_color_id'], $options['brand']);
                $reserved=$this->printcolor_reserved($crow['printshop_item_id'], $crow['color'], $options['brand']);
                $crow['specfile'] = $crow['color_descript'];
                // -------------------------------------------------------------------
                $instock=$income-$outcome;
                $sum_instock = $sum_instock + $instock;
                $available=$instock-$reserved;
                $total_invent+=($available*$crow['price']);
                $sum_available = $sum_available + $available;
                $sum_reserved = $sum_reserved + $reserved;
                $max=$crow['suggeststock'];
                $sum_max = $sum_max + $crow['suggeststock'];
                $stockperc=$this->empty_html_content;
                $outpics = $this->empty_html_content;
                $picsclass = '';
                $stockperc=$this->empty_html_content;
                $stockclass='';
                if ($max!=0) {
                    $stockperc=round($instock/$max*100,0);
                    if ($stockperc <= $this->config->item('invoutstock')) {
                        $stockclass = $this->outstockclass;
                    } elseif ($stockperc <= $this->config->item('invlowstock')) {
                        $stockclass = $this->lowstockclass;
                    }
                }
                $totalea=($crow['price']!=0 ? $crow['price']+$addlcost : '-');
                $ndecim=2;
                $whole = floor($crow['price']*100);
                $fraction = $crow['price']*100 - $whole;
                if (intval($fraction*10)>0) {
                    $ndecim=3;
                }

                $pics = $this->get_picsattachments($crow['printshop_color_id']);
                $colorsdata[]=array(
                    'printshop_item_id'=>$row['printshop_item_id'],
                    'printshop_color_id'=>$crow['printshop_color_id'],
                    'type'=>'color',
                    'item_num'=>$this->empty_html_content,
                    'item_name'=>$crow['color'],
                    'notreorder'=>$crow['notreorder'],
                );
                $inventory[]=array(
                    'printshop_item_id'=>$row['printshop_item_id'],
                    'printshop_color_id'=>$crow['printshop_color_id'],
                    'type'=>'color',
                    'numpp'=>$numpp,
                    'item_num'=>$this->empty_html_content,
                    'item_name'=>$crow['color'],
                    'percent'=>$stockperc.'%',
                    'instock'=>($instock<=0 ? $this->outstoklabel : QTYOutput($instock)),
                    'reserved'=>($reserved==0 ? $this->empty_html_content : $reserved),
                    'availabled'=>($available==0 ? $this->empty_html_content : $available),
                    'max'=>($max==0 ? $this->empty_html_content : QTYOutput($max)),
                    'price'=>MoneyOutput($crow['price'],$ndecim),
                    'total'=>MoneyOutput($totalea,3),
                    'platetemp'=>(empty($crow['plate_temp']) ? 'empty' : ''),
                    'prooftemp'=>(empty($crow['proof_temp']) ? 'empty' : ''),
                    'pics'=>$outpics,
                    'color_descript'=>$crow['color_descript'],
                    'color_order'=>$crow['color_order'],
                    'stockclass'=>$stockclass,
                    'specs_desc' => $crow['specfile'],
                    'specsclass'=>(empty($crow['specfile']) ? 'empty' : 'full'),
                    'picsclass'=>(count($pics) >0 ? '' : 'empty'),
                    'specsurl' => (empty($crow['specfile']) ? '' : 'href="/fulfillment/inventory_specs_bt/?id='.$crow['printshop_color_id'].'"'),
                    'percenturl' => 'href="/fulfillment/max_color_percent/?id='.$crow['printshop_color_id'].'"',
                    'notreorder'=>$crow['notreorder'],
                    'price_int'=>$crow['price'],
                    'total_int'=>round($totalea,3),
                    'instock_int'=>$instock,
                    'reserved_int'=>$reserved,
                    'availabled_int'=>$available,
                );
                $numpp++;
            }
            // Change parameters of item
            $picsclass = '';
            $stockclass='';
            $stockperc=$this->empty_html_content;
            $outpics = $this->empty_html_content;
            $stockperc=$this->empty_html_content;
            if ($sum_max!=0) {
                $stockperc=round($sum_instock/$sum_max*100,0);
                if ($stockperc<=$this->config->item('invoutstock')) {
                    $stockclass=$this->outstockclass;
                } elseif ($stockperc<=$this->config->item('invlowstock')) {
                    $stockclass=$this->lowstockclass;
                }
            }
            $inventory[$itemkey]['percent']=$stockperc.'%';
            $inventory[$itemkey]['instock']=($sum_instock==0 ? $this->empty_html_content : QTYOutput($sum_instock));
            $inventory[$itemkey]['reserved']=($sum_reserved==0 ? $this->empty_html_content : QTYOutput($sum_reserved));
            $inventory[$itemkey]['availabled']=($sum_available==0 ? $this->empty_html_content : QTYOutput($sum_available));
            $inventory[$itemkey]['max']=($sum_max==0 ? $this->empty_html_content : QTYOutput($sum_max));
            $inventory[$itemkey]['price']=$this->empty_html_content;
            $inventory[$itemkey]['total']=$this->empty_html_content;
            $inventory[$itemkey]['platetemp']=(empty($row['plate_temp']) ? 'empty' : '');
            $inventory[$itemkey]['prooftemp']=(empty($row['proof_temp']) ? 'empty' : '');;
            $inventory[$itemkey]['pics']=$this->empty_html_content;
            $inventory[$itemkey]['color_descript']=$this->empty_html_content;
            $inventory[$itemkey]['color_order']='';
            $inventory[$itemkey]['stockclass']=$stockclass;
            $inventory[$itemkey]['specs_desc']=$this->empty_html_content;
            $inventory[$itemkey]['specsclass']='';
            $inventory[$itemkey]['picsclass']='';
            $inventory[$itemkey]['specsurl']='';
            $inventory[$itemkey]['price_int']='';
            $inventory[$itemkey]['total_int']='';
            $inventory[$itemkey]['instock_int']=$sum_instock;
            $inventory[$itemkey]['reserved_int']=$sum_reserved;
            $inventory[$itemkey]['availabled_int']=$sum_available;
        }
        $out=array(
            'inventory'=>$inventory,
            'colors'=>$colorsdata,
            'inventtotal'=>round(floatval($total_invent),2),
        );
        return $out;
    }

//    public function get_printshop_items($options=array()) {
//        $addlcost=$this->invaddcost();
//        $this->db->select('*');
//        $this->db->from('ts_printshop_items');
//        // Options where
//        if (isset($options['orderby'])) {
//            if (isset($options['direct'])) {
//                $this->db->order_by($options['orderby'], $options['direct']);
//            } else {
//                $this->db->order_by($options['orderby']);
//            }
//        } else {
//            $this->db->order_by('printshop_item_id', 'desc');
//        }
//        $res=$this->db->get()->result_array();
//
//        $numpp=1;
//        $inventory = array(
//            "inventory" => array(),
//            "boats" => array(),
//        );
//
//        foreach ($res as $row) {
//            $colors=$this->get_item_colors($row['printshop_item_id']);
//            $sum_available = 0;
//            $sum_instock = 0;
//            $sum_onroute = 0;
//            $avg_percent = 0;
//
//            $boats = array("boats" => array(), "colors" => array(), "item" => array());
//
//            foreach ($colors as $sum) {
//                $income=$this->printcolor_income($sum['printshop_color_id']);
//                $outcome=$this->printcolor_outcome($sum['printshop_color_id']);
//                $reserved=$this->printcolor_reserved($sum['printshop_item_id'], $sum['color']);
//                // $innermove=$this->printcolor_innermove($sum['printshop_color_id']);
//                $boatItems = $this->get_onboats_by_color_id($sum['printshop_color_id']);
//                $boat_date = $this->get_data_onboat();
//                $sum['specfile'] = $sum['color_descript'];
//
//                // -------------------------------------------------------------------
//                $instock=$income-$outcome;
//                $sum_instock = $sum_instock + $instock;
//                $available=$instock-$reserved;
//                $sum_available = $sum_available + $available;
//
//                //$sum_onroute = $sum_onroute + $onroute;
//                $max=$sum['suggeststock'];
//                $stockperc=$this->empty_html_content;
//                if ($max!=0) {
//                    $stockperc=round($instock/$max*100,0);
//                }
//                $avg_percent = $avg_percent + floatval($stockperc);
//
//                //--------------------------------------------------------------------
//
//                $color = array("boats" => array(), "color" => array());
//
//                foreach ($boat_date as $boat_d) {
//                    $boatD = $boat_d['onboat_container'];
//                    $color["boats"][$boatD] = 0;
//                    /*$boats["boats"][$boatD] = 0;
//                    $inventory["boats"][$boatD]['value'] = 0;*/
//                }
//
//                foreach($boatItems as $boat) {
//                    $boatDate = $boat["onboat_container"];
//                    // Calc total boats for color
//
//                    if (isset($color["boats"][$boatDate])) {
//                        $color["boats"][$boatDate] += $boat["onroutestock"];
//                    } else {
//                        $color["boats"][$boatDate] = $boat["onroutestock"];
//                    }
//                    // Calc total boats for item
//                    if (isset($boats["boats"][$boatDate])) {
//                        $boats["boats"][$boatDate] += $boat["onroutestock"];
//                    } else {
//                        $boats["boats"][$boatDate] = $boat["onroutestock"];
//                    }
//                    // Calc total boats for inventory
//                    if (isset($inventory["boats"][$boatDate])) {
//                        $inventory["boats"][$boatDate]['value'] += $boat["onroutestock"];
//                        $inventory["boats"][$boatDate]['status'] = $boat["onboat_status"];
//                    } else {
//                        $inventory["boats"][$boatDate]['value'] = $boat["onroutestock"];
//                        $inventory["boats"][$boatDate]['status'] = $boat["onboat_status"];
//                    }
//                }
//
//
//                $outpics = $this->empty_html_content;
//                $picsclass = '';
//
//                $stockperc=$this->empty_html_content;
//                $stockclass='';
//                if ($max!=0) {
//                    $stockperc=round($instock/$max*100,0)."%";
//                    if ($stockperc <= $this->config->item('invoutstock')) {
//                        $stockclass = $this->outstockclass;
//                    } elseif ($stockperc <= $this->config->item('invlowstock')) {
//                        $stockclass = $this->lowstockclass;
//                    }
//                }
////                if ($instock<=0 || $available<=0) {
////                    $stockclass=$this->outstockclass;
////                }
////                if ($stockperc>0 && $stockperc<$this->config->item('min_stockperc')) {
////                    $stockclass='lowstock';
////                }
//                $totalea=($sum['price']!=0 ? $sum['price']+$addlcost : '-');
//                $ndecim=2;
//                $whole = floor($sum['price']*100);
//                $fraction = $sum['price']*100 - $whole;
//                if (intval($fraction*10)>0) {
//                    $ndecim=3;
//                }
//
//                $pics = $this->get_picsattachments($sum['printshop_color_id']);
//
//                $color["color"]=array(
//                    'printshop_item_id'=>$row['printshop_item_id'],
//                    'printshop_color_id'=>$sum['printshop_color_id'],
//                    'type'=>'color',
//                    'numpp'=>$numpp,
//                    'item_num'=>$this->empty_html_content,
//                    'item_name'=>$sum['color'],
//                    'percent'=>$stockperc,
//                    'instock'=>($instock<=0 ? $this->outstoklabel : QTYOutput($instock)),
//                    'reserved'=>($reserved==0 ? $this->empty_html_content : $reserved),
//                    'availabled'=>($available==0 ? $this->empty_html_content : $available),
//                    'max'=>($max==0 ? $this->empty_html_content : QTYOutput($max)),
//                    //'toget'=>($toget==0 ? $this->empty_html_content : QTYOutput($toget)),
//                    //'onroutestock'=>($onroute==0 ? $this->empty_html_content : $onroute),
//                    'price'=>MoneyOutput($sum['price'],$ndecim),
//                    'total'=>MoneyOutput($totalea,3),
//                    'platetemp'=>(empty($sum['plate_temp']) ? 'empty' : ''),
//                    'prooftemp'=>(empty($sum['proof_temp']) ? 'empty' : ''),
//                    'pics'=>$outpics,
//                    'color_descript'=>$sum['color_descript'],
//                    'color_order'=>$sum['color_order'],
//                    'stockclass'=>$stockclass,
//                    'specs_desc' => $sum['specfile'],
//                    /*'onboat_class' =>,*/
//                    'specsclass'=>(empty($sum['specfile']) ? 'empty' : 'full'),
//                    'picsclass'=>(count($pics) >0 ? '' : 'empty'),
//                    'specsurl' => (empty($sum['specfile']) ? '' : 'href="/fulfillment/inventory_specs_bt/?id='.$sum['printshop_color_id'].'"'),
//                    'percenturl' => 'href="/fulfillment/max_color_percent/?id='.$sum['printshop_color_id'].'"',
//                );
//                $numpp++;
//                $boats["colors"][$sum['printshop_color_id']] = $color;
//            }
//
//
//
//
//            $avg_percent = round($avg_percent/count($colors), 0);
//            $boats["item"] = array(
//                'printshop_item_id'=>$row['printshop_item_id'],
//                'printshop_color_id'=>0,
//                'type'=>'item',
//                'numpp'=>$this->empty_html_content,
//                'item_num'=>$row['item_num'],
//                'item_name'=>$row['item_name'],
//                'percent'=>$avg_percent.'%',
//                'instock'=>$sum_instock,
//                'reserved'=>$this->empty_html_content,
//                'availabled'=>$sum_available,
//                'have'=>$this->empty_html_content,
//                'max'=>$this->empty_html_content,
//                'toget'=>$this->empty_html_content,
//                'backup'=>$this->empty_html_content,
//                'onroutestock'=>$sum_onroute,
//                'price'=>$this->empty_html_content,
//                'total'=>$this->empty_html_content,
//                'platetemp'=>$this->empty_html_content,
//                'prooftemp'=>$this->empty_html_content,
//                'pics'=>$this->empty_html_content,
//                'color_descript'=>$this->empty_html_content,
//                'color_order'=>$this->empty_html_content,
//                'stockclass'=>'',
//                'reservclass'=>'',
//                'backupclass'=>'',
//                'haveclass'=>'',
//                'maxclass'=>'',
//                'togetclass'=>'',
//                'specsclass'=>'',
//                'platetempclass'=>'',
//                'prooftempclass'=>'',
//                'picsclass'=>'',
//                'specsurl' => '',
//                'percenturl' => 'href="/fulfillment/max_item_percent/?id='.$row['printshop_item_id'].'"'
//            );
//
//            $inventory["inventory"][$row['printshop_item_id']] = $boats;
//
//        }
//        return $inventory;
//    }
//
//    public function get_percent_color($printshop_color_id) {
//        $this->db->select('suggeststock, color');
//        $this->db->from('ts_printshop_colors');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    public function get_percent_item($printshop_item_id) {
//        $this->db->select('sum(suggeststock) as sug, tspi.item_name');
//        $this->db->from('ts_printshop_colors tspc');
//        $this->db->join('ts_printshop_items tspi', 'tspi.printshop_item_id=tspc.printshop_item_id');
//        $this->db->where('tspc.printshop_item_id', $printshop_item_id);
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//    public function get_percent_total() {
//        $this->db->select('sum(suggeststock) as sug');
//        $this->db->from('ts_printshop_colors');
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    public function get_onboats_by_color_id($printshop_color_id) {
//        $this->db->select('*');
//        $this->db->from('ts_printshop_onboats');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $this->db->group_by('onboat_container');
//        $this->db->order_by('onboat_container', 'asc');
//        return $this->db->get()->result_array();
//    }

    public function get_data_onboat($brand) {
        $this->db->select('onboat_container, onboat_status, onboat_date, sum(onroutestock) as onboat_total');
        $this->db->from('ts_printshop_onboats');
        $this->db->group_by('onboat_container, onboat_status, onboat_date');
        /*$this->db->order_by('onboat_date', 'asc');*/
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res = $this->db->get()->result_array();

        return $res;
    }

//    public function save_onboat_amount($data, $amount) {
//
//        $this->db->select('max(onboat_container) as container');
//        $this->db->from('ts_printshop_onboats');
//
//        $res = $this->db->get()->row_array();
//
//        foreach ($amount as $key => $row) {
//            if($key!='onboatdate' && is_int($key)) {
//                $this->db->set('printshop_color_id', $key);
//                $this->db->set('onroutestock', $row);
//                $this->db->set('onboat_date', strtotime($data));
//                $this->db->set('onboat_container', floatval($res['container'])+1);
//                $this->db->insert('ts_printshop_onboats');
//            } else {
//                continue;
//            }
//        }
//
//        return TRUE;
//    }
//
//    public function find_printshop_onboats($data, $container_number, $color_id) {
//        $this->db->select('*');
//        $this->db->from('ts_printshop_onboats');
//        $this->db->where(array(
//            "printshop_color_id" => $color_id,
//            "onboat_date" => strtotime($data),
//            "onboat_container" => $container_number
//        ));
//        return $this->db->get()->result_array();
//    }
//
//    public function save_onboatedit_amount($data, $container_number, $color_list) {
//        foreach ($color_list as $item) {
//            $color_id = $item["color_id"];
//            $value  = $item["value"];
//            if ($container_number > 0) {
//                $onboat = $this->find_printshop_onboats($data, $container_number, $color_id);
//                if (count($onboat) > 0) {
//                    // if value was changed to new value
//                    if (($value) != '') {
//                        $this->db->set('onroutestock', $value);
//
//                        $this->db->where('onboat_date', strtotime($data));
//                        $this->db->where('printshop_color_id', $color_id);
//                        $this->db->where('onboat_container', $container_number);
//
//                        $this->db->update('ts_printshop_onboats');
//                    } else {
//                        // if value was erased from field
//                        $this->db->where('printshop_color_id', $color_id);
//                        $this->db->where('onboat_container', $container_number);
//                        $this->db->where('onboat_date', strtotime($data));
//
//                        $this->db->delete('ts_printshop_onboats');
//                    }
//                } else {
//                    $this->db->set('onroutestock', $value);
//                    $this->db->set('onboat_date', strtotime($data));
//                    $this->db->set('printshop_color_id', $color_id);
//                    $this->db->set('onboat_container', $container_number);
//                    $this->db->insert('ts_printshop_onboats');
//                }
//            } else {
//                $this->db->select('max(onboat_container) as container');
//                $this->db->from('ts_printshop_onboats');
//                $res = $this->db->get()->row_array();
//                $container_number = floatval($res['container'])+1;
//
//                $this->db->set('onroutestock', $value);
//                $this->db->set('onboat_date', strtotime($data));
//                $this->db->set('printshop_color_id', $color_id);
//                $this->db->set('onboat_container', $container_number);
//                $this->db->insert('ts_printshop_onboats');
//            }
//        }
//    }
//
//    public function onboat_arrived($onboat_container) {
//        $out=array('result'=>$this->error_result,'msg'=>'Container Not Found');
//        $this->db->select('count(*) as cnt');
//        $this->db->from('ts_printshop_onboats');
//        $this->db->where('onboat_container', $onboat_container);
//        $this->db->where('onboat_status',0);
//        $chkres=$this->db->get()->row_array();
//        if ($chkres['cnt']>0) {
//            $this->db->set('onboat_status', 1);
//            $this->db->where('onboat_container', $onboat_container);
//            $this->db->update('ts_printshop_onboats');
//
//            $this->db->select('*');
//            $this->db->from('ts_printshop_onboats');
//            $this->db->where('onboat_container', $onboat_container);
//
//            $res = $this->db->get()->result_array();
//            $descript='Container '.$res[0]['onboat_container'];
//            foreach ($res as $row) {
//                $this->db->set('printshop_color_id', $row['printshop_color_id']);
//                $this->db->set('instock_date', $row['onboat_date']);
//                $this->db->set('instock_amnt', $row['onroutestock']);
//                $this->db->set('instock_descrip', $descript);
//                $this->db->insert('ts_printshop_instock');
//            }
//            $out['result']=$this->success_result;
//        }
//        return $out;
//    }
//
//    public function add_to_instock($onboat_container) {
//        $this->db->select('*');
//        $this->db->from('ts_printshop_onboats');
//        $this->db->where('onboat_container', $onboat_container);
//
//        $res = $this->db->get()->result_array();
//
//        foreach ($res as $row) {
//            $this->db->set('printshop_color_id', $row['printshop_color_id']);
//            $this->db->set('instock_date', $row['onboat_date']);
//            $this->db->set('instock_amnt', $row['onroutestock']);
//            $this->db->set('instock_descrip', 'On Boat');
//            $this->db->insert('ts_printshop_instock');
//        }
//    }

    public function printcolor_reserved($printshop_item_id, $color, $brand) {
        $this->db->select('sum(i.item_qty) as reserved, count(i.order_itemcolor_id) as cnt');
        $this->db->from('ts_order_itemcolors i');
        $this->db->join('ts_order_items im','im.order_item_id=i.order_item_id');
        $this->db->join('ts_orders o','o.order_id=im.order_id');
        $this->db->join('ts_printshop_colors c','c.printshop_item_id=i.printshop_item_id');
        $this->db->where('o.order_cog', null);
        if ($printshop_item_id>0) {
            $this->db->where('i.printshop_item_id', $printshop_item_id);
            $this->db->where('i.item_color', $color);
        }
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $reserv=$this->db->get()->row_array();
        $reserved = 0;
        if ($reserv['cnt']>0) {
            $reserved = intval($reserv['reserved']);
        }
        return $reserved;
    }

    public function printcolor_income($printshop_color_id, $brand) {
        $this->db->select('sum(instock_amnt) as backup, count(printshop_instock_id) as cnt');
        $this->db->from('ts_printshop_instock');
        if ($printshop_color_id>0) {
            $this->db->where('printshop_color_id', $printshop_color_id);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $back=$this->db->get()->row_array();
        $backup = 0;

        if($back['cnt']>0) {
            $backup = intval($back['backup']);
        }
        return $backup;
    }

    public function printcolor_outcome($printshop_color_id, $brand) {
        $this->db->select('sum(oa.shipped) as shipped, sum(oa.kepted) as kepted, sum(oa.misprint) as misprint');
        $this->db->from('ts_order_amounts oa');
        $this->db->where('printshop',1);
        if ($printshop_color_id>0) {
            $this->db->where('printshop_color_id', $printshop_color_id);
        }
        if ($brand!=='ALL') {
            $this->db->join('ts_orders o','oa.order_id=o.order_id');
            $this->db->where('o.brand', $brand);
        }
        $data=$this->db->get()->row_array();
        $outcome=intval($data['shipped'])+intval($data['kepted'])+intval($data['misprint']);
        return $outcome;
    }

    public function printcolor_innermove($printshop_color_id) {
        $this->db->select('count(printshop_move_id) as cnt, sum(move_amnt) as amnt');
        $this->db->from('ts_printshop_moves');
        $this->db->where('printshop_color_id', $printshop_color_id);
        $res=$this->db->get()->row_array();

        if ($res['cnt']==0) {
            return 0;
        } else {
            return intval($res['amnt']);
        }

    }

    public function get_item_colors($printshop_item_id) {
        $this->db->select('tspc.*');
        $this->db->from('ts_printshop_colors tspc');
        /*$this->db->join('ts_printshop_instock tspi', 'tspi.printshop_color_id = tspc.printshop_color_id');*/
        $this->db->where('tspc.printshop_item_id', $printshop_item_id);
        /*$this->db->group_by('tspi.instock_date');*/
        // $this->db->order_by('printshop_color_id','desc');
        $this->db->order_by('tspc.color');
        $res=$this->db->get()->result_array();
        $str = $this->db->last_query();
        return $res;
    }

    public function get_picsattachments($printshop_color_id) {
        $data = $this->db->select('*')
            ->from('ts_printshop_pics')
            ->where("printshop_color_id", $printshop_color_id)
            ->get();
        $data = $data->result_array();

        foreach($data as &$row) {
            $row["status"] = self::ROW_UNCHANGED;
        }

        return $data;
    }

//    function save_uploadpicsattach($filename, $printshop_color_id) {
//        $this->db->set('pics',$filename);
//        $this->db->set('printshop_color_id',$printshop_color_id);
//        $this->db->insert('ts_printshop_pics');
//
//        return $this->db->insert_id();
//    }
//
//    // New Item
//    public function new_invent_item() {
//        $fields = $this->db->list_fields('ts_printshop_items');
//        $item=array();
//
//        foreach ($fields as $field) {
//            if ($field=='printshop_item_id') {
//                $item[$field]=-1;
//            } else {
//                $item[$field]='';
//            }
//        }
//
//        return $item;
//    }
//
//    // Edit Item
//    public function get_invent_item($printshop_item_id) {
//        $out=array('result'=>$this->error_result);
//        $out['msg']='Item Not Found';
//        $this->db->select('*');
//        $this->db->from('ts_printshop_items');
//        $this->db->where('printshop_item_id', $printshop_item_id);
//        $res=$this->db->get()->row_array();
//
//        if (isset($res['printshop_item_id'])) {
//            $out['result']=$this->success_result;
//            $out['item']=$res;
//        }
//
//        return $out;
//    }
//
//    // Change Item details
//    public function invitem_item_change($item, $postdata) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//
//        if (!array_key_exists('fldname', $postdata) || !array_key_exists('newval', $postdata)) {
//            $out['msg']='Empty Changed Parameter';
//            return $out;
//        }
//
//        $fldname=$postdata['fldname'];
//        $newval=$postdata['newval'];
//
//        if (!array_key_exists($fldname, $item)) {
//            $out['msg']='Unknown Parameter '.$fldname;
//            return $out;
//        }
//
//        $item[$fldname]=$newval;
//        $out['result']=$this->success_result;
//        $this->func->session('invitemdata', $item);
//
//        return $out;
//    }
//
//    // Save changes
//    public function invitem_item_save($item) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (empty($item['item_num'])) {
//            $out['msg']='Enter Shape #';
//            return $out;
//        }
//        if (empty($item['item_name'])) {
//            $out['msg']='Enter Shape Name';
//            return $out;
//        }
//        if (!$this->_check_invitem($item['printshop_item_id'], $item['item_num'])) {
//            $out['msg']='Enter Unique Shape #';
//            return $out;
//        }
//        $this->db->set('item_num', $item['item_num']);
//        $this->db->set('item_name', $item['item_name']);
//        $this->db->set('plate_temp', $item['plate_temp']);
//        $this->db->set('plate_temp_source', $item['plate_temp_source']);
//        $this->db->set('proof_temp', $item['proof_temp']);
//        $this->db->set('proof_temp_source', $item['proof_temp_source']);
//        $this->db->set('item_label', $item['item_label']);
//        $this->db->set('item_label_source', $item['item_label_source']);
//        if ($item['printshop_item_id']<=0) {
//            $this->db->insert('ts_printshop_items');
//        } else {
//            $this->db->where('printshop_item_id', $item['printshop_item_id']);
//            $this->db->update('ts_printshop_items');
//        }
//
//        $path_preload = $this->config->item('upload_path_preload');
//        $route=$this->func->extract_filename($item['plate_temp']);
//        $text = substr(strrchr($route['name'], '/'), 1 );
//        $filename = $text.'.'.$route['ext'];
//
//        $path_full_plate=$this->config->item('invplatetemp');
//        $result = @copy($path_preload.$filename, $path_full_plate.$filename);
//        if($item['proof_temp'] != NULL) {
//            $route=$this->func->extract_filename($item['proof_temp']);
//            $text = substr(strrchr($route['name'], '/'), 1 );
//            $filename = $text.'.'.$route['ext'];
//            $path_full_proof=$this->config->item('invprooftemp');
//            $result = @copy($path_preload.$filename, $path_full_proof.$filename);
//        }
//
//        if($item['item_label'] != NULL) {
//            $route=$this->func->extract_filename($item['item_label']);
//            $text = substr(strrchr($route['name'], '/'), 1 );
//            $filename = $text.'.'.$route['ext'];
//            $path_full=$this->config->item('invitemlabel');
//            $result = @copy($path_preload.$filename, $path_full.$filename);
//        }
//
//        $out['result']=$this->success_result;
//        $this->func->session('invitemdata', NULL);
//        return $out;
//    }
//
//    //Add Pics
//    public function get_invent_pics($printshop_color_id) {
//        $out=array('result'=>$this->error_result);
//        $out['msg']='Item Not Found';
//        $this->db->select('*');
//        $this->db->from('ts_printshop_pics');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $res=$this->db->get()->row_array();
//        if (isset($res['printshop_color_id'])) {
//            $out['result']=$this->success_result;
//            $out['item']=$res;
//        }
//        return $out;
//    }
//
//    public function add_picsfile($data) {
//        $out = array('result'=>$this->error_result);
//
//        if (isset($data['printshop_color_id'])) {
//            $numpp=0;
//            $idxproof=0;
//            foreach ($data['docs'] as $row) {
//                $idxproof++;
//            }
//        }
//
//    }
//
//    // New Inventory Item Color
//    public function invitem_newcolor($printshop_item_id) {
//        $fields = $this->db->list_fields('ts_printshop_colors');
//        $colors=array();
//        foreach ($fields as $field) {
//            if ($field=='printshop_item_id') {
//                $colors[$field]=$printshop_item_id;
//            } elseif ($field=='printshop_color_id') {
//                $colors[$field]=-1;
//            } else {
//                $colors[$field]='';
//            }
//            $colors['availabled']='';
//            $colors['instock']='';
//        }
//        $colors=array(
//            'printshop_item_id'=>$printshop_item_id,
//            'printshop_color_id'=>-1,
//            'type'=>'color',
//            'item_num'=>$this->empty_html_content,
//            'color'=>'',
//            'percent'=>$this->empty_html_content,
//            'instock'=>$this->empty_html_content,
//            'reserved'=>$this->empty_html_content,
//            'availabled'=>$this->empty_html_content,
//            'backup'=>$this->empty_html_content,
//            'have'=>$this->empty_html_content,
//            'suggeststock'=>0,
//            'toget'=>$this->empty_html_content,
//            'onroutestock'=>0,
//            'price'=>0,
//            'total'=>$this->empty_html_content,
//            'color_descript'=>'',
//            'color_order'=>1,
//            'specfile' => '',
//            'specsclass'=>(empty($crow['specfile']) ? 'empty' : 'full'),
//            'picsclass'=>'empty',
//            'notreorder'=>0,
//        );
//        $this->db->select('count(printshop_color_id) as cnt');
//        $this->db->from('ts_printshop_colors');
//        $this->db->where('printshop_item_id', $printshop_item_id);
//        $res=$this->db->get()->row_array();
//        $colors['color_order']=$res['cnt']+1;
//
//        return $colors;
//    }
//
//    // Inv Item Color data
//    public function invitem_colordata($printshop_color_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        $this->db->select('c.*, i.item_name, i.item_num, c.color_descript');
//        $this->db->from('ts_printshop_colors c');
//        $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
//        $this->db->where('c.printshop_color_id', $printshop_color_id);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['printshop_color_id'])) {
//            $out['msg']='Color Not Found';
//        } else {
//            $out['result']=$this->success_result;
//            $addlcost=$this->invaddcost();
//            // Get Stock Data
////            $res['instock']=$this->printcolor_instock($printshop_color_id);
////            $avail=intval($res['instock'])-intval($res['reserved']);
////            $res['availabled']=QTYOutput($avail);
////            $out['result']=$this->success_result;
////            $out['color']=$res;
//            $income=$this->printcolor_income($printshop_color_id);
//            $outcome=$this->printcolor_outcome($printshop_color_id);
//            $innermove=$this->printcolor_innermove($printshop_color_id);
//            $reserved=$this->printcolor_reserved($res['printshop_item_id'], $res['color']);
//            $backup=$income-$innermove;
//            $have=$innermove-$outcome;
//            $instock=$income-$outcome;
//            $available=$instock-$reserved;
//            $max=$res['suggeststock'];
//            $toget=($max - $have) + $reserved;
//            $onroute=$res['onroutestock'];
//            // Special cols
//            $stockperc=$this->empty_html_content;
//            $stockclass='';
//            if ($max!=0) {
//                $stockperc=round($instock/$max*100,0).'%';
//                if ($stockperc <= $this->config->item('invoutstock')) {
//                    $stockclass = $this->outstockclass;
//                } elseif ($stockperc <= $this->config->item('invlowstock')) {
//                    $stockclass = $this->lowstockclass;
//                }
//            }
////                if ($instock<=0 || $available<=0) {
////                    $stockclass=$this->outstockclass;
////                }
////                if ($stockperc>0 && $stockperc<$this->config->item('min_stockperc')) {
////                    $stockclass='lowstock';
////                }
//            $haveclass='';
//            if ($have<0) {
//                $haveclass='red_text';
//                $outhave='('.QTYOutput(abs($have)).')';
//            } else {
//                $outhave=$have==0 ? $this->empty_html_content : QTYOutput($have);
//            }
//            $totalea=($res['price']!=0 ? $res['price']+$addlcost : '-');
//            $ndecim=2;
//            $whole = floor($res['price']*100);
//            $fraction = $res['price']*100 - $whole;
//            if (intval($fraction*10)>0) {
//                $ndecim=3;
//            }
//
//            $pics = $this->count_inventory_pics($printshop_color_id);
//
//            $data=array(
//                'printshop_item_id'=>$res['printshop_item_id'],
//                'printshop_color_id'=>$printshop_color_id,
//                'type'=>'color',
//                'item_num'=>$this->empty_html_content,
//                'color'=>$res['color'],
//                'percent'=>$stockperc,
//                'instock'=>($instock<=0 ? $this->outstoklabel : QTYOutput($instock)),
//                'reserved'=>($reserved==0 ? $this->empty_html_content : QTYOutput($reserved)),
//                'availabled'=>($available==0 ? $this->empty_html_content : QTYOutput($available)),
//                'backup'=>($backup==0 ? $this->empty_html_content : QTYOutput($backup)),
//                'have'=>$outhave,
//                'suggeststock'=>$max,
//                'toget'=>($toget==0 ? $this->empty_html_content : QTYOutput($toget)),
//                'onroutestock'=>$onroute,
//                'price'=>number_format($res['price'],$ndecim),
//                'total'=>MoneyOutput($totalea,3),
//                'color_descript'=>$res['color_descript'],
//                'color_order'=>$res['color_order'],
//                'stockclass'=>$stockclass,
//                'haveclass'=>$haveclass,
//                'specfile' => $res['color_descript'],
//                'specsclass'=>(empty($res['specfile']) ? 'empty' : 'full'),
//                'picsclass'=>(empty($pics['cnt']) ? 'empty' : ''),
//                'notreorder'=>$res['notreorder'],
//            );
//            $out['data']=$data;
//        }
//        return $out;
//    }
//
//
//    // Change Inv Color Value
//    public function invitem_color_change($color, $postdata) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (!array_key_exists('fldname', $postdata) || !array_key_exists('newval', $postdata)) {
//            $out['msg']='Empty Changed Parameter';
//            return $out;
//        }
//        $fldname=$postdata['fldname'];
//        $newval=$postdata['newval'];
//        if (!array_key_exists($fldname, $color)) {
//            $out['msg']='Unknown Parameter '.$fldname;
//            return $out;
//        }
//        if ($fldname=='notreorder') {
//            $newval=0;
//            if ($color['notreorder']==0) {
//                $newval=1;
//            }
//        }
//        $color[$fldname]=$newval;
//        $out['result']=$this->success_result;
//        $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
//        $this->func->session($uploadsession, $color);
//        // Calc available
//        $out['availabled']=intval($color['instock'])-intval($color['reserved']);
//        $out['notreorder']=intval($newval);
//        return $out;
//    }
//
//    // Save Changes in Item Color
//    public function invitem_color_save($color) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (empty($color['color'])) {
//            $out['msg']='Enter Color Name';
//            return $out;
//        }
//        if (!$this->_check_invitemcolor($color['printshop_item_id'], $color['printshop_color_id'], $color['color'])) {
//            $out['msg']='Enter Unique Color';
//            return $out;
//        }
//        $this->db->set('color', $color['color']);
//        $this->db->set('suggeststock', intval($color['suggeststock']));
//        // $this->db->set('instock', intval($color['instock']));
//        $this->db->set('reserved', intval($color['reserved']));
//        $this->db->set('onroutestock',  intval($color['onroutestock']));
//        $this->db->set('price', floatval($color['price']));
//        $this->db->set('color_descript', $color['specfile']);
//        $this->db->set('color_order', $color['color_order']);
//        $this->db->set('notreorder', $color['notreorder']);
//        // $this->db->set('specfile', $color['specfile']);
//        if ($color['printshop_color_id']<=0) {
//            $this->db->set('printshop_item_id', $color['printshop_item_id']);
//            $this->db->insert('ts_printshop_colors');
//            $color['printshop_color_id']=$this->db->insert_id();
//        } else {
//            $this->db->where('printshop_color_id', $color['printshop_color_id']);
//            $this->db->update('ts_printshop_colors');
//        }
//        // Rebuild color order
//        $this->db->select('printshop_color_id, color');
//        $this->db->from('ts_printshop_colors');
//        $this->db->where('printshop_item_id', $color['printshop_item_id']);
//        $this->db->where('printshop_color_id != ', $color['printshop_color_id']);
//        $this->db->order_by('color_order');
//        $colordat=$this->db->get()->result_array();
//        if (count($colordat)>1) {
//            $i=1;
//            foreach ($colordat as $crow) {
//                if ($i==$color['color_order']) {
//                    $i++;
//                }
//                $this->db->set('color_order', $i);
//                $this->db->where('printshop_color_id', $crow['printshop_color_id']);
//                $this->db->update('ts_printshop_colors');
//                $i++;
//            }
//        }
//        $out['printshop_color_id'] = $color['printshop_color_id'];
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//    public function get_invitem_colordata($printshop_color_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        $this->db->select('c.*, i.item_name, i.item_num');
//        $this->db->from('ts_printshop_colors c');
//        $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
//        $this->db->where('c.printshop_color_id', $printshop_color_id);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['printshop_color_id'])) {
//            $out['msg']='Color Not Found';
//        } else {
//            $out['result']=$this->success_result;
//            $out['color']=$res;
//        }
//        return $out;
//
//    }
//
//    public function invitem_color_stock_bydate() {
//        $this->db->select('tspi.instock_date, tspc.onroutestock');
//        $this->db->from('ts_printshop_colors tspc');
//        $this->db->join('ts_printshop_instock tspi', 'tspi.printshop_color_id=tspc.printshop_color_id');
//        $this->db->group_by('tspc.printshop_color_id, tspi.instock_date');
//        $res=$this->db->get()->result_array();
//
//        return $res;
//    }
//
//    public function invitem_color_stocklog($printshop_color_id) {
//        $this->db->select('*');
//        $this->db->from('v_printshop_instock');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $this->db->order_by('instock_date','desc');
//        $res=$this->db->get()->result_array();
//
//        $balance=$this->printcolor_instock($printshop_color_id);
//        $data=array();
//        foreach ($res as $row) {
//            $order_id=$order_class='';
//            if ($row['instok_type']=='O') {
//                $order_class='leadorderlink';
//                $this->db->select('order_id');
//                $this->db->from('ts_orders');
//                $this->db->where('order_num', $row['instock_descrip']);
//                $ordres=$this->db->get()->row_array();
//                $order_id=$ordres['order_id'];
//            }
//            $balanceclass=($balance<0 ? 'red' : '');
//            $outbalance=QTYOutput($balance);
//            if ($balance<0) {
//                $outbalance='('.QTYOutput(abs($balance)).')';
//            }
//            $outamnt=QTYOutput($row['instock_amnt']);
//            $amntclass='';
//            if ($row['instock_amnt']<0)  {
//                $amntclass='red';
//                $outamnt='('.QTYOutput(abs($row['instock_amnt'])).')';
//            }
//            $data[]=array(
//                'printshop_instock_id'=>($row['instok_type']=='S' ? $row['printshop_instock_id'] : ''),
//                'instok_type'=>$row['instok_type'],
//                'outstockdate'=>date('m/d/y',$row['instock_date']),
//                'instock_descrip'=>$row['instock_descrip'],
//                'amntclass'=>$amntclass,
//                'outamnt'=>$outamnt,
//                'balanceclass'=>$balanceclass,
//                'balance'=>$outbalance,
//                'order_class'=>$order_class,
//                'order_id'=>$order_id,
//            );
//            $balance-=$row['instock_amnt'];
//        }
//        return $data;
//    }
//
//    public function new_colorinstock($printshop_color_id) {
//        $stock=array();
//        $fields = $this->db->list_fields('ts_printshop_instock');
//        foreach ($fields as $row) {
//            if ($row=='printshop_color_id') {
//                $stock[$row]=$printshop_color_id;
//            } elseif ($row=='printshop_instock_id') {
//                $stock[$row]=-1;
//            } elseif ($row=='instock_date') {
//                $stock[$row]=time();
//            } else {
//                $stock[$row]='';
//            }
//        }
//        return $stock;
//    }
//
//    // Edit exist data
//    public function invitem_color_stockdata($printshop_instock_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        $this->db->select('*');
//        $this->db->from('ts_printshop_instock');
//        $this->db->where('printshop_instock_id', $printshop_instock_id);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['printshop_instock_id'])) {
//            $out['msg']='Stock Log Row not Found';
//        } else {
//            $out['result']=$this->success_result;
//            $out['data']=$res;
//        }
//        return $out;
//    }
//
//
//    // Change Stock Data
//    public function invcolor_stock_change($stockdata, $postdata) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (!array_key_exists('fldname',$postdata) || !array_key_exists('newval', $postdata)) {
//            $out['msg']='No All Data sended';
//            return $out;
//        }
//        $fldname=$postdata['fldname'];
//        $newval=$postdata['newval'];
//        if (!array_key_exists($fldname,$stockdata)) {
//            $out['msg']='Unknown Parameter '.$fldname;
//            return $out;
//        }
//        if ($fldname=='instock_date') {
//            $newval=  strtotime($newval);
//        }
//        $stockdata[$fldname]=$newval;
//        $out['result']=$this->success_result;
//        $this->func->session('stockdata',$stockdata);
//        return $out;
//    }
//
//    // Save New Stock
//    public function invitem_color_stocksave($stockdata) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (empty($stockdata['instock_descrip'])) {
//            $out['msg']='Enter Stock Description';
//            return $out;
//        }
//        // Add / Update
//        $this->db->set('instock_date', $stockdata['instock_date']);
//        $this->db->set('instock_descrip', $stockdata['instock_descrip']);
//        $this->db->set('instock_amnt', intval($stockdata['instock_amnt']));
//        if ($stockdata['printshop_instock_id']<=0) {
//            $this->db->set('printshop_color_id', $stockdata['printshop_color_id']);
//            $this->db->insert('ts_printshop_instock');
//        } else {
//            $this->db->where('printshop_instock_id',$stockdata['printshop_instock_id']);
//            $this->db->update('ts_printshop_instock');
//        }
//        $this->func->session('stockdata', NULL);
//        $out['result']=$this->success_result;
//        $out['printshop_color_id']=$stockdata['printshop_color_id'];
//        return $out;
//    }
//
//    // Check Unique Item #
//    private function _check_invitem($printshop_item_id, $item_num) {
//        $outres=FALSE;
//        $this->db->select('count(printshop_item_id) as cnt');
//        $this->db->from('ts_printshop_items');
//        $this->db->where('printshop_item_id != ', $printshop_item_id);
//        $this->db->where('upper(item_num)', strtoupper($item_num));
//        $res=$this->db->get()->row_array();
//        if ($res['cnt']==0) {
//            $outres=TRUE;
//        }
//        return $outres;
//    }
//
//
//    // Check unique item color
//    private function _check_invitemcolor($printshop_item_id, $printshop_color_id, $color) {
//        $outres=FALSE;
//        $this->db->select('count(printshop_color_id) as cnt');
//        $this->db->from('ts_printshop_colors');
//        $this->db->where('printshop_item_id', $printshop_item_id);
//        $this->db->where('printshop_color_id != ', $printshop_color_id);
//        $this->db->where('upper(color)', strtoupper($color));
//        $res=$this->db->get()->row_array();
//        if ($res['cnt']==0) {
//            $outres=TRUE;
//        }
//        return $outres;
//    }
//
//    // Calc a number of Order Reports
//    public function get_orderreport_counts($options=array()) {
//        $this->db->select('count(oa.amount_id) as cnt');
//        $this->db->from('ts_order_amounts oa');
//        $this->db->where('oa.printshop',1);
//        // Additional Options
//        if (isset($options['search'])) {
//            $this->db->join('ts_orders o','o.order_id=oa.order_id');
//            $this->db->join('ts_printshop_colors c','c.printshop_color_id=oa.printshop_color_id');
//            $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
//            $this->db->like('upper(concat(o.order_num, o.customer_name, i.item_num, i.item_name))', $options['search']);
//        }
//        if (isset($options['report_year'])) {
//            $start=strtotime($options['report_year'].'-01-01');
//            $year_finish=intval($options['report_year']+1);
//            $finish=strtotime($year_finish.'-01-01');
//            $this->db->where('oa.printshop_date >= ', $start);
//            $this->db->where('oa.printshop_date < ', $finish);
//        }
//        $res=$this->db->get()->row_array();
//        return $res['cnt'];
//    }
//
//    // Totals
//    public function get_orderreport_totals($options=array()) {
//        $this->db->select('sum(oa.shipped) as shipped, sum(oa.kepted) as kepted, sum(oa.misprint) as misprint');
//        $this->db->select('sum(oa.shipped+oa.kepted+oa.misprint)as totalqty');
//        $this->db->select('sum(oa.orangeplate+oa.blueplate) as totalplate');
//        $this->db->select('sum((oa.shipped+oa.kepted+oa.misprint)*oa.extracost) as total_extra');
//        $this->db->select('sum((oa.shipped+oa.kepted+oa.misprint)*(oa.price+oa.extracost)) as item_cost, sum(oa.orangeplate) as oranplate');
//        $this->db->select('sum(oa.blueplate) as blueplate, sum(oa.misprint*(oa.price+oa.extracost)) as misprint_cost');
//        $this->db->select('sum(oa.orangeplate*oa.orangeplate_price) as orangeplatecost');
//        $this->db->select('sum(oa.blueplate*oa.blueplate_price) as blueplatecost');
//        $this->db->select('sum(oa.printshop_total) as total_cost');
//        $this->db->from('ts_order_amounts oa');
//        $this->db->where('printshop',1);
//        // Options
//        if (isset($options['search'])) {
//            $this->db->join('ts_orders o','o.order_id=oa.order_id');
//            $this->db->join('ts_printshop_colors c','c.printshop_color_id=oa.printshop_color_id');
//            $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
//            $this->db->like('upper(concat(o.order_num, o.customer_name, i.item_num, i.item_name))', $options['search']);
//        }
//        if (isset($options['report_year'])) {
//            $start=strtotime($options['report_year'].'-01-01');
//            $year_finish=intval($options['report_year']+1);
//            $finish=strtotime($year_finish.'-01-01');
//            $this->db->where('oa.printshop_date >= ', $start);
//            $this->db->where('oa.printshop_date < ', $finish);
//        }
//        $res=$this->db->get()->row_array();
//        $res['misprintperc']='0%';
//        if ($res['shipped']>0) {
//            $res['misprintperc']=round($res['misprint']/$res['shipped']*100,0).'%';
//        }
//        $res['platecost']=$res['orangeplatecost']+$res['blueplatecost'];
//        return $res;
//    }
//
//    // Get Data with Order Reports
//    public function get_orderreport_data($options) {
//        // Get Cost - Blue and Orange plates
//        // $platesprice=$this->_get_plates_costs();
//        // $blueplate_price=$platesprice['blueplate_price'];
//        // $orangeplate_price=$platesprice['orangeplate_price'];
//        $this->db->select('oa.*, c.color, i.item_name, i.item_num, o.customer_name, o.order_num');
//        $this->db->select('(oa.price+oa.extracost) as priceea');
//        $this->db->select('(oa.extracost)*(oa.shipped+oa.kepted+oa.misprint) as extraitem');
//        $this->db->select('(oa.price+oa.extracost)*(oa.shipped+oa.kepted+oa.misprint) as costitem');
//        $this->db->select('(oa.shipped+oa.kepted+oa.misprint) as totalitem');
//        $this->db->select('(oa.orangeplate+oa.blueplate) as totalplates');
//        $this->db->select('(oa.orangeplate*oa.orangeplate_price+oa.blueplate*oa.blueplate_price) as platescost');
//        $this->db->select('oa.printshop_total as totalitemcost');
//        $this->db->select('(oa.price+oa.extracost)*oa.misprint as misprintcost');
//        $this->db->select('date_format(from_unixtime(oa.printshop_date),\'%Y%m%d\') as sortdatefld',FALSE);
//        $this->db->from('ts_order_amounts oa');
//        $this->db->join('ts_printshop_colors c', 'c.printshop_color_id=oa.printshop_color_id');
//        $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
//        $this->db->join('ts_orders o','o.order_id=oa.order_id');
//        $this->db->where('oa.printshop',1);
//        if (isset($options['search'])) {
//            $this->db->like('upper(concat(o.order_num, o.customer_name, i.item_num, i.item_name))', $options['search']);
//        }
//        if (isset($options['report_year'])) {
//            $start=strtotime($options['report_year'].'-01-01');
//            $year_finish=intval($options['report_year']+1);
//            $finish=strtotime($year_finish.'-01-01');
//            $this->db->where('oa.printshop_date >= ', $start);
//            $this->db->where('oa.printshop_date < ', $finish);
//        }
//        if (isset($options['limit'])) {
//            if (isset($options['offset'])) {
//                $this->db->limit($options['limit'], $options['offset']);
//            } else {
//                $this->db->limit($options['limit']);
//            }
//        }
//        // $this->db->order_by('o.order_num desc,oa.printshop_date desc');
//        // $this->db->order_by('oa.printshop_date desc, o.order_num desc');
//        // $this->db->order_by('oa.printshop_date desc, oa.amount_id desc');
//        $this->db->order_by("sortdatefld desc, oa.update_date desc");
//
//        $res=$this->db->get()->result_array();
//
//        // Calc start index
//        $startidx=$options['totals']-$options['offset'];
//        $data=array();
//        foreach ($res as $row) {
//            $misprint_proc=($row['shipped']==0 ? 0 : $row['misprint']/$row['shipped']*100);
//            $data[]=array(
//                'printshop_income_id'=>$row['amount_id'],
//                'numpp'=>$startidx,
//                'order_date'=>date('j-M', $row['printshop_date']),
//                'order_num'=>$row['order_num'],
//                'customer'=>$row['customer_name'],
//                'item_name'=>$row['item_num'].' '.str_replace('Stress Balls', '', $row['item_name']),
//                'color'=>$row['color'],
//                'shipped'=>$row['shipped'],
//                'kepted'=>$row['kepted'],
//                'misprint'=>$row['misprint'],
//                'misprint_proc'=>round($misprint_proc,0).'%',
//                'total_qty'=>$row['totalitem'],
//                'price'=>$row['price'],
//                'extracost'=>$row['extracost'],
//                'totalea'=>round($row['priceea'],3),
//                'extraitem'=>round($row['extraitem'],2),
//                'costitem'=>round($row['costitem'],2),
//                'oranplate'=>$row['orangeplate'],
//                'blueplate'=>$row['blueplate'],
//                'totalplates'=>$row['totalplates'],
//                'platescost'=>$row['platescost'],
//                'itemstotalcost'=>$row['totalitemcost'],
//                'misprintcost'=>$row['misprintcost'],
//                'orderclass'=>($row['printshop_type']=='M' ? 'manualinput' : 'systeminput'),
//                'order_id'=>$row['order_id'],
//            );
//            $startidx--;
//        }
//        return $data;
//    }
//
//    // Get Data about separate Order
//    public function get_printshop_order($printshop_income_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if ($printshop_income_id==0) {
//            $res=$this->_newprintshop_order();
//        } else {
//            $this->db->select('oa.*, oa.amount_id as printshop_income_id, c.printshop_item_id, o.customer_name as customer, o.order_num');
//            $this->db->from('ts_order_amounts oa');
//            $this->db->join('ts_printshop_colors c', 'c.printshop_color_id=oa.printshop_color_id');
//            $this->db->join('ts_orders o','o.order_id=oa.order_id');
//            $this->db->where('oa.amount_id', $printshop_income_id);
//            $res=$this->db->get()->row_array();
//            if (!isset($res['amount_id'])) {
//                $out['msg']='Printshop Order Not Found';
//                return $out;
//            }
//        }
//        $data=$this->_prinshoporder_params($res);
//        $out['result']=$this->success_result;
//        $out['data']=$data;
//        return $out;
//    }
//
//    private function _newprintshop_order() {
////        $ordfld = $this->db->list_fields('ts_printshop_orders');
////        foreach ($ordfld as $row) {
////            if ($row=='printshop_income_id') {
////                $data[$row]=-1;
////            } elseif ($row=='order_date') {
////                $data[$row]=time();
////            } elseif($row=='order_num' || $row=='customer' || $row=='printshop_color_id') {
////                $data[$row]='';
////            } elseif ($row=='order_type') {
////                $data[$row]='M';
////            } else {
////                $data[$row]=0;
////            }
////        }
////        $data['printshop_item_id']='';
//        $platesprice=$this->_get_plates_costs();
//        $blueplate_price=$platesprice['blueplate_price'];
//        $orangeplate_price=$platesprice['orangeplate_price'];
//        $data=array(
//            'printshop_income_id'=>0,
//            'printshop_date'=>time(),
//            'printshop_color_id'=>'',
//            'printshop_item_id'=>'',
//            'shipped'=>0,
//            'kepted'=>0,
//            'misprint'=>0,
//            'price'=>0,
//            'extracost'=>0,
//            'orangeplate'=>0,
//            'blueplate'=>0,
//            'extraitem'=>0,
//            'orangeplate_price'=>$orangeplate_price,
//            'blueplate_price'=>$blueplate_price,
//            'printshop_type'=>'M',
//            'order_id'=>0,
//            'order_num'=>'',
//            'customer'=>'',
//            'printshop_history'=>0,
//        );
//        return $data;
//    }
//
//    public function _prinshoporder_params($order) {
//        /* $platesprice=$this->_get_plates_costs();
//        $blueplate_price=$platesprice['blueplate_price'];
//        $orangeplate_price=$platesprice['orangeplate_price'];
//        */
//        $totalea=round($order['price']+$order['extracost'],3);
//        $costitem=$totalea*($order['shipped']+$order['kepted']+$order['misprint']);
//        $misprint_proc=($order['shipped']==0 ? 0 : $order['misprint']/$order['shipped']*100);
//        $misprintcost=$order['misprint']*$totalea;
//        $totalplates=$order['orangeplate']+$order['blueplate'];
//        $platescost=$order['orangeplate']*$order['orangeplate_price']+$order['blueplate']*$order['blueplate_price'];
//        $totalitemcost=$platescost+$costitem;
//        $data=array(
//            'printshop_income_id'=>$order['printshop_income_id'],
//            'printshop_date'=>$order['printshop_date'],
//            'order_num'=>$order['order_num'],
//            'customer'=>$order['customer'],
//            'printshop_item_id'=>$order['printshop_item_id'],
//            'printshop_color_id'=>$order['printshop_color_id'],
//            'shipped'=>$order['shipped'],
//            'kepted'=>$order['kepted'],
//            'misprint'=>$order['misprint'],
//            'misprint_proc'=>round($misprint_proc,0).'%',
//            'total_qty'=>($order['shipped']+$order['kepted']+$order['misprint']),
//            'price'=>$order['price'],
//            'extracost'=>$order['extracost'],
//            'extraitem'=>($order['shipped']+$order['kepted']+$order['misprint'])*$order['extracost'],
//            'totalea'=>$totalea,
//            'costitem'=>round($costitem,2),
//            'orangeplate'=>$order['orangeplate'],
//            'orangeplate_price'=>$order['orangeplate_price'],
//            'blueplate'=>$order['blueplate'],
//            'blueplate_price'=>$order['blueplate_price'],
//            'totalplates'=>$totalplates,
//            'platescost'=>$platescost,
//            'itemstotalcost'=>$totalitemcost,
//            'misprintcost'=>$misprintcost,
//            'printshop_type'=>$order['printshop_type'],
//            'order_id'=>$order['order_id'],
//            'printshop_history'=>$order['printshop_history'],
//        );
//        return $data;
//    }
//
//    // Get Data about config values (plates prices, etc)
//    public function _get_plates_costs() {
//        $this->db->select('orangeplate_price, blueplate_price, repaid_cost, inv_addcost');
//        $this->db->from('ts_configs');
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    public function get_printshopitem_list() {
//        $this->db->select("printshop_item_id, concat(replace(item_name, 'Stress Balls',''),' ',item_num ) as item_name", FALSE);
//        $this->db->from('ts_printshop_items');
//        $this->db->order_by('item_name');
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
//    public function change_printshop_order($orderdata, $fldname, $newval,$sessionid) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (!array_key_exists($fldname, $orderdata)) {
//            $out['msg']='Field '.$fldname.' Not Found';
//            return $out;
//        }
//        if ($fldname=='order_num') {
//            $this->db->select('order_id, customer_name');
//            $this->db->from('ts_orders');
//            $this->db->where('order_num', $newval);
//            $res=$this->db->get()->row_array();
//            if (!isset($res['order_id'])) {
//                $out['msg']='Order Not Exist';
//                $out['oldval']=$orderdata['order_num'];
//                return $out;
//            }
//            $orderdata['order_id']=$res['order_id'];
//            $orderdata['customer']=$res['customer_name'];
//        }
//        if ($fldname=='printshop_date') {
//            $newval=strtotime($newval);
//        }
//        $orderdata[$fldname]=$newval;
//        if ($fldname=='printshop_item_id') {
//            // New Item
//            $colors=$this->get_item_colors($newval);
//            $colordef=$colors[0];
//            // $orderdata['price']=$colordef['price'];
//            // $orderdata['printshop_color_id']=$colordef['printshop_color_id'];
//            $orderdata['price']=0;
//            $orderdata['printshop_color_id']='';
//            $costs=$this->_get_plates_costs();
//            $orderdata['extracost']=0; //$costs['inv_addcost'];
//            $orderdata['colors']=$colors;
//        } elseif ($fldname=='printshop_color_id') {
//            $outcolor=$this->get_invitem_colordata($newval);
//            if ($outcolor['result']==$this->error_result) {
//                $out['msg']=$outcolor['msg'];
//                return $out;
//            }
//            $colordat=$outcolor['color'];
//            $orderdata['price']=$colordat['price'];
//            $costs=$this->_get_plates_costs();
//            $orderdata['extracost']=$costs['inv_addcost'];
//        }
//        $data=$this->_prinshoporder_params($orderdata);
//        $data['items']=$orderdata['items'];
//        $data['colors']=$orderdata['colors'];
//        $data['session']=$orderdata['session'];
//        $this->func->session($sessionid, $data);
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//    public function save_printshop_order($orderdata, $sessionid, $user_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (empty($orderdata['printshop_color_id'])) {
//            $out['msg']='Choose Item Color';
//            return $out;
//        }
//        if (empty($orderdata['printshop_item_id'])) {
//            $out['msg']='Choose Item';
//            return $out;
//        }
//        if (empty($orderdata['order_num'])) {
//            $out['msg']='Enter Order #';
//            return $out;
//        }
//        if (empty($orderdata['customer'])) {
//            $out['msg']='Enter Customer';
//            return $out;
//        }
//        $this->db->set('printshop_date', $orderdata['printshop_date']);
//        // $this->db->set('order_num', $orderdata['order_num']);
//        // $this->db->set('customer', $orderdata['customer']);
//        $this->db->set('printshop_color_id', $orderdata['printshop_color_id']);
//        $this->db->set('shipped', intval($orderdata['shipped']));
//        $this->db->set('kepted', intval($orderdata['kepted']));
//        $this->db->set('misprint', intval($orderdata['misprint']));
//        $this->db->set('orangeplate', floatval($orderdata['orangeplate']));
//        $this->db->set('blueplate', floatval($orderdata['blueplate']));
//        $this->db->set('price', floatval($orderdata['price']));
//        $this->db->set('extracost', floatval($orderdata['extracost']));
//        if ($orderdata['printshop_history']==0) {
//            $this->db->set('amount_sum', floatval($orderdata['itemstotalcost']));
//        }
//        $this->db->set('printshop_total', floatval($orderdata['itemstotalcost']));
//        if ($orderdata['printshop_income_id']<=0) {
//            $this->db->set('printshop_type', $orderdata['printshop_type']);
//            $this->db->set('printshop', 1);
//            $this->db->set('order_id', $orderdata['order_id']);
//            $this->db->set('orangeplate_price', $orderdata['orangeplate_price']);
//            $this->db->set('blueplate_price', $orderdata['blueplate_price']);
//            $this->db->set('vendor_id', $this->config->item('inventory_vendor'));
//            $this->db->set('method_id', $this->config->item('inventory_paymethod'));
//            $this->db->set('amount_date', time());
//            $this->db->set('create_date', time());
//            $this->db->set('create_user', $user_id);
//            $this->db->set('update_date', time());
//            $this->db->set('update_user', $user_id);
//            $this->db->insert('ts_order_amounts');
//            $orderdata['printshop_income_id']=$this->db->insert_id();
//        } else {
//            $this->db->set('update_date', time());
//            $this->db->set('update_user', $user_id);
//            $this->db->where('amount_id', $orderdata['printshop_income_id']);
//            $this->db->update('ts_order_amounts');
//        }
//        // Update Orders by new COG
////        $this->db->select('order_id');
////        $this->db->from('ts_order_amounts');
////        $this->db->where('amount_id', $orderdata['printshop_income_id']);
////        $orddat=$this->db->get()->row_array();
////        $order_id=$orddat['order_id'];
//        if ($orderdata['printshop_history']==0) {
//            $this->_update_ordercog($orderdata['order_id']);
//        }
//        $out['result']=$this->success_result;
//        $out['order_id']=$orderdata['order_id'];
//        $out['printshop_income_id']=$orderdata['printshop_income_id'];
//        $this->func->session($sessionid, NULL);
//        return $out;
//    }
//
//    // Remove amounts
//    public function orderreport_remove($amount_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        $chk=$this->get_printshop_order($amount_id);
//        if ($chk['result']==$this->error_result) {
//            $out['msg']=$chk['msg'];
//            return $out;
//        }
//        $order_id=$chk['data']['order_id'];
//        $this->db->where('amount_id', $amount_id);
//        $this->db->delete('ts_order_amounts');
//        // Recalc COG
//        $this->_update_ordercog($order_id);
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//
//    public function change_additional_cost($fldname, $newval) {
//        $this->db->set($fldname, floatval($newval));
//        $this->db->update('ts_configs');
//        return TRUE;
//    }
//
//    public function printcolor_instock($printshop_color_id) {
//        $this->db->select('sum(instock_amnt) as stock');
//        $this->db->from('ts_printshop_instock');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $stockres=$this->db->get()->row_array();
//        if (isset($stockres['stock'])) {
//            $income=intval($stockres['stock']);
//        } else {
//            $income=0;
//        }
//        $this->db->select('sum(shipped) as shipped, sum(kepted) as kepted, sum(misprint) as misprint');
//        $this->db->from('ts_order_amounts');
//        $this->db->where('printshop',1);
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $data=$this->db->get()->row_array();
//        $outcome=intval($data['shipped'])+intval($data['kepted'])+intval($data['misprint']);
//        return $income-$outcome;
//    }
//
//    public function _update_ordercog($order_id) {
//        $this->db->select('revenue, shipping, is_shipping, tax, cc_fee');
//        $this->db->from('ts_orders');
//        $this->db->where('order_id', $order_id);
//        $orddat=$this->db->get()->row_array();
//        $revenue=  floatval($orddat['revenue']);
//        $shipping=floatval($orddat['shipping']);
//        $is_shipping=intval($orddat['is_shipping']);
//        $tax=floatval($orddat['tax']);
//        $cc_fee=floatval($orddat['cc_fee']);
//        // Get COG Value
//        $this->db->select('count(amount_id) cnt, sum(amount_sum) as cog');
//        $this->db->from('ts_order_amounts');
//        $this->db->where('order_id', $order_id);
//        $cogres=$this->db->get()->row_array();
//        if ($cogres['cnt']==0) {
//            // Default
//            $new_order_cog=NULL;
//            $new_profit_pc=NULL;
//            $new_profit=round((floatval($revenue))*$this->config->item('default_profit')/100,2);
//        } else {
//            $new_order_cog=floatval($cogres['cog']);
//            $new_profit=$revenue-($shipping*$is_shipping)-$tax-$cc_fee-$new_order_cog;
//            $new_profit_pc=($revenue==0 ? null : round(($new_profit/$revenue)*100,1));
//        }
//        $this->db->set('order_cog',$new_order_cog);
//        $this->db->set('profit',$new_profit);
//        $this->db->set('profit_perc',$new_profit_pc);
//        $this->db->where('order_id',$order_id);
//        $this->db->update('ts_orders');
//        return TRUE;
//    }
//
//    public function save_printshopcolor_spec($printshop_color_id, $specfile) {
//        if ($specfile=='') {
//            $this->db->set('specfile', NULL);
//        } else {
//            $this->db->set('specfile', $specfile);
//        }
//        $this->db->where('printshop_color_id',$printshop_color_id);
//        $this->db->update('ts_printshop_colors');
//        return TRUE;
//    }
//
//    public function save_printshop_platetemp($printshop_item_id, $doclink, $platetempfile) {
//        if ($platetempfile=='' && $doclink == '') {
//            $this->db->set('plate_temp', NULL);
//            $this->db->set('plate_temp_source', NULL);
//        } else {
//            $this->db->set('plate_temp_source', $platetempfile);
//            $this->db->set('plate_temp', $doclink);
//        }
//        $this->db->where('printshop_item_id',$printshop_item_id);
//        $this->db->update('ts_printshop_items');
//        return TRUE;
//    }
//
//    public function load_dataplatetemp($printshop_item_id) {
//        $out=array('result'=>$this->error_result, 'msg' => "Color not found");
//        $this->db->select('*');
//        $this->db->from('ts_printshop_items');
//        $this->db->where('printshop_item_id', $printshop_item_id);
//        $dataplate=$this->db->get()->row_array();
//
//        if (isset($dataplate['printshop_item_id'])) {
//            $out['result'] = $this->success_result;
//            $out['data'] = $dataplate;
//        }
//
//        return $out;
//    }
//    public function load_dataprooftemp($printshop_color_id) {
//        $out=array('result'=>$this->error_result, 'msg' => "Color not found");
//        $this->db->select('*');
//        $this->db->from('ts_printshop_colors');
//        $this->db->where('printshop_color_id', $printshop_color_id);
//        $dataplate=$this->db->get()->row_array();
//
//        if (isset($dataplate['printshop_color_id'])) {
//            $out['result'] = $this->success_result;
//            $out['data'] = $dataplate;
//        }
//
//        return $out;
//    }
//
//    function cut_link($data) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        if (isset($data['printshop_item_id'])) {
//            $out['msg'] = "File not exist";
//
//
//            $path_sh_plate=$this->config->item('invplatetemp_relative');
//            $path_full_proof=$this->config->item('invprooftemp');
//            $path_sh_proof=$this->config->item('invprooftemp_relative');
//            $filename=$this->func->extract_filename($data['filesource']);
//            if (file_exists($data["filesource"])) {
//                /*$purefilesrc=$this->func->uniq_link(12).'.'.$filename['ext'];*/
//                $text = substr(strrchr($filename['name'], '/'), 1 );
//                $purefilesrc = $text.'.'.$filename['ext'];
//                if ($data['uploadtype']=='proof_temp') {
//                    $data['filesource']=$path_sh_proof.$purefilesrc;
//                } elseif ($data['uploadtype']=='plate_temp') {
//                    $data['filesource']=$path_sh_plate.$purefilesrc;
//                } else {
//                    $path_sh=$this->config->item('invitemlabel_relative');
//                    $data['filesource']=$path_sh.$purefilesrc;
//                }
//                //}
//            } else {
//                /*$del = $data['filesource'];
//                @unlink($del);*/
//                $data['filesource']=NULL;
//                $data['filename']=NULL;
//            }
//            //$this->save_printshop_platetemp($data['printshop_item_id'], $data['filesource'], $data['filename']);
//            $out['result']=$this->success_result;
//            $out['msg'] = "";
//        } else {
//            $out['msg']='Printshop not found';
//        }
//        return $data;
//    }
//
//    // Transfer Data
//    public function get_inventory_transfer($options) {
//        $onlyget = 0;
//        if (isset($options['onlyget'])) {
//            $onlyget = 1;
//        }
//        $this->db->select('*');
//        $this->db->from('ts_printshop_items');
//        // Options where
//        if (isset($options['orderby'])) {
//            if (isset($options['direct'])) {
//                $this->db->order_by($options['orderby'], $options['direct']);
//            } else {
//                $this->db->order_by($options['orderby']);
//            }
//        } else {
//            $this->db->order_by('printshop_item_id', 'desc');
//        }
//        $res = $this->db->get()->result_array();
//
//        // Prepare
//        $out = array();
//        $numpp = 1;
//        foreach ($res as $row) {
//            $out[] = array(
//                'printshop_item_id' => $row['printshop_item_id'],
//                'printshop_color_id' => 0,
//                'type' => 'item',
//                'numpp' => $this->empty_html_content,
//                'item_num' => $row['item_num'],
//                'item_name' => $row['item_name'],
//                'percent' => $this->empty_html_content,
//                'instock' => $this->empty_html_content,
//                'reserved' => $this->empty_html_content,
//                'availabled' => $this->empty_html_content,
//                'have' => $this->empty_html_content,
//                'max' => $this->empty_html_content,
//                'toget' => $this->empty_html_content,
//                'backup' => $this->empty_html_content,
//                'onroutestock' => $this->empty_html_content,
//                'price' => $this->empty_html_content,
//                'total' => $this->empty_html_content,
//                'platetemp' => $this->empty_html_content,
//                'prooftemp' => $this->empty_html_content,
//                'pics' => $this->empty_html_content,
//                'color_descript' => $this->empty_html_content,
//                'color_order' => $this->empty_html_content,
//                'stockclass' => '',
//                'reservclass' => '',
//                'backupclass' => '',
//                'haveclass' => '',
//                'maxclass' => '',
//                'togetclass' => '',
//                'specsclass' => '',
//                'platetempclass' => '',
//                'prooftempclass' => '',
//                'picsclass' => '',
//            );
//            // Get Colors
//            $colors = $this->get_item_colors($row['printshop_item_id']);
//            // $colors = $this->get_item_colors($row['printshop_item_id']);
//            foreach ($colors as $crow) {
//                $income = $this->printcolor_income($crow['printshop_color_id']);
//                $outcome = $this->printcolor_outcome($crow['printshop_color_id']);
//                $innermove = $this->printcolor_innermove($crow['printshop_color_id']);
//                $reserved = $this->printcolor_reserved($crow['printshop_item_id'], $crow['color']);
//                $backup = $income - $innermove;
//                $have = $innermove - $outcome;
//                $instock = $income - $outcome;
//                $available = $instock - $reserved;
//                $max = $crow['suggeststock'];
//                $toget = ($max - $have) + $reserved;
//                if ($onlyget == 1 && $toget == 0) {
//                    // Next record
//                } else {
//                    $onroute = $crow['onroutestock'];
//                    // Special cols
//                    $stockperc = $this->empty_html_content;
//                    $stockclass = '';
//                    if ($max != 0) {
//                        $stockperc = round($instock / $max * 100, 0) . '%';
//                        if ($stockperc <= $this->config->item('invoutstock')) {
//                            $stockclass = $this->outstockclass;
//                        } elseif ($stockperc <= $this->config->item('invlowstock')) {
//                            $stockclass = $this->lowstockclass;
//                        }
//                    }
////                    if ($instock <= 0 || $available <= 0) {
////                        $stockclass = $this->outstockclass;
////                    }
////                    if ($stockperc > 0 && $stockperc < $this->config->item('min_stockperc')) {
////                        $stockclass = 'lowstock';
////                    }
//                    $haveclass = '';
//                    if ($have < 0) {
//                        $haveclass = 'red_text';
//                        $outhave = '(' . QTYOutput(abs($have)) . ')';
//                    } else {
//                        $outhave = $have == 0 ? $this->empty_html_content : QTYOutput($have);
//                    }
//
//                    $out[] = array(
//                        'printshop_item_id' => $row['printshop_item_id'],
//                        'printshop_color_id' => $crow['printshop_color_id'],
//                        'type' => 'color',
//                        'numpp' => $numpp,
//                        'item_num' => $this->empty_html_content,
//                        'item_name' => $crow['color'],
//                        'percent' => $stockperc,
//                        'instock' => ($instock <= 0 ? $this->outstoklabel : QTYOutput($instock)),
//                        'stockclass' => $stockclass,
//                        'reserved' => ($reserved == 0 ? $this->empty_html_content : QTYOutput($reserved)),
//                        'availabled' => ($available == 0 ? $this->empty_html_content : QTYOutput($available)),
//                        'backup' => ($backup == 0 ? $this->empty_html_content : QTYOutput($backup)),
//                        'have' => $outhave,
//                        'haveclass' => $haveclass,
//                        'max' => ($max == 0 ? $this->empty_html_content : QTYOutput($max)),
//                        'toget' => ($toget == 0 ? $this->empty_html_content : QTYOutput($toget)),
//                        'onroutestock' => ($onroute == 0 ? $this->empty_html_content : QTYOutput($onroute)),
//                    );
//                    $numpp++;
//                }
//            }
//        }
//        return $out;
//    }
//
//    public function change_inventory_transfer($transfer, $prinshop_color_id, $fldname, $newval) {
//        $out=array('result'=>$this->error_result, 'msg'=>  'Item Color Not Found');
//        $found=0;
//        $idx=0;
//        foreach ($transfer as $trow) {
//            if ($trow['printshop_color_id']==$prinshop_color_id) {
//                $found=1;
//                break;
//            } else {
//                $idx++;
//            }
//        }
//        if ($found==1) {
//            if ($fldname=='move_amnt') {
//                $transfer[$idx]['move_amnt']=intval($newval);
//            } else {
//                $transfer[$idx]['direct']=$newval;
//            }
//            $this->func->session('transfer', $transfer);
//            $newhave=$transfer[$idx]['have']+($transfer[$idx]['move_amnt']*($transfer[$idx]['direct']=='direct' ? 1 : -1));
//            $newbackup=$transfer[$idx]['backup']-($transfer[$idx]['move_amnt']*($transfer[$idx]['direct']=='direct' ? 1 : -1));
//            $out['newhave']=  QTYOutput($newhave);
//            $out['newbackup']=  QTYOutput($newbackup);
//            $out['result']=$this->success_result;
//        }
//        return $out;
//    }
//
//    public function save_inventory_transfer($transfer, $user_id) {
//        foreach ($transfer as $trow) {
//            if ($trow['move_amnt']!=0) {
//                $this->db->set('move_insuser', $user_id);
//                $this->db->set('printshop_color_id', $trow['printshop_color_id']);
//                if ($trow['direct']=='direct') {
//                    $this->db->set('move_amnt', $trow['move_amnt']);
//                } else {
//                    $this->db->set('move_amnt', $trow['move_amnt']*(-1));
//                }
//                $this->db->insert('ts_printshop_moves');
//            }
//        }
//        $this->func->session('transfer', NULL);
//        return TRUE;
//    }
//
//    public function get_inventory_colorswares() {
//        $this->db->select('printshop_color_id');
//        $this->db->from('ts_printshop_colors');
//        $colors = $this->db->get()->result_array();
//        foreach ($colors as $crow) {
//            $income = $this->printcolor_income($crow['printshop_color_id']);
//            $outcome = $this->printcolor_outcome($crow['printshop_color_id']);
//            $innermove = $this->printcolor_innermove($crow['printshop_color_id']);
//            $backup = $income - $innermove;
//            $have = $innermove - $outcome;
//            $out[] = array(
//                'printshop_color_id' => $crow['printshop_color_id'],
//                'backup' => $backup ,
//                'have' => $have,
//                'move_amnt'=>0,
//                'direct'=>'direct'
//            );
//        }
//        return $out;
//    }
//
//    public function save_printshop_pics($rows, $printshop_color_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>  "Can't save Pics");
//        $path = $this->config->item('invpics_relative');
//        foreach ($rows as $item) {
//            switch ($item['status']) {
//                case self::ROW_INSERT:
//                    $this->db->set('pics', $path.$item['pics']);
//                    $this->db->set('pics_source', $item['pics_source']);
//                    $this->db->set('printshop_color_id', $printshop_color_id);
//                    $this->db->insert('ts_printshop_pics');
//
//                    $path_preload = $this->config->item('upload_path_preload');
//                    $path_pics = $this->config->item('invpics');
//                    if (!file_exists($path_pics)) {
//                        mkdir($path_pics, 0777);
//                    }
//
//                    @copy($path_preload.$item['pics'], $path_pics.$item['pics']);
//                    @unlink($path_preload.$item['pics']);
//
//                    break;
//                case self::ROW_DELETE:
//                    $this->db->where('printshop_pics_id', $item['printshop_pics_id']);
//                    $this->db->delete('ts_printshop_pics');
//                    $path_pics = $this->config->item('invpics');
//                    @unlink($path_pics.$item['pics']);
//                    break;
//            }
//        }
//        return TRUE;
//    }
//
//    // Download XLS file
//    public function inventory_download($onboat_container) {
//        $out=array('result'=>$this->error_result,'msg'=>'No Data Exist');
//        $this->db->select('i.item_num, i.item_name, c.color, c.price, sum(b.onroutestock) as qty, c.color_descript, b.onboat_date');
//        $this->db->from('ts_printshop_onboats b');
//        $this->db->join('ts_printshop_colors c','c.printshop_color_id=b.printshop_color_id');
//        $this->db->join('ts_printshop_items i','i.printshop_item_id=c.printshop_item_id');
//        $this->db->where('b.onboat_container', $onboat_container);
//        $this->db->group_by('i.item_num, i.item_name, c.color, c.price');
//        $this->db->order_by('i.item_num, c.color');
//        $res=$this->db->get()->result_array();
//        if (count($res)>0) {
//            $onboat_date=$res[0]['onboat_date'];
//            $this->load->library('PHPExcel');
//            $this->load->library('PHPExcel/IOFactory');
//
//            $objPHPExcel = new PHPExcel();
//
//            $objPHPExcel->getProperties()->setCreator("PHP");
//            $i=0;
//            $objPHPExcel->createSheet($i);
//            $objPHPExcel->setActiveSheetIndex($i);
//            $sheet = $objPHPExcel->getActiveSheet();
//            $title='Container '.$onboat_container.' - Arriving in USA '.date('m/d/y', $onboat_date);
//            // $objPHPExcel->setActiveSheetIndex($i)->setTitle($title);
//            // Title
//            $font= new PHPExcel_Style_Font;
//            $font->setName('Arial');
//            $font->setSize('10');
//
//
//            $sheet->getStyle()->setFont($font);
//
//            $sheet->getColumnDimension('A')->setWidth(7);
//            $sheet->getColumnDimension('B')->setWidth(40);
//            $sheet->getColumnDimension('C')->setWidth(60);
//            $sheet->getColumnDimension('D')->setWidth(60);
//            $sheet->getColumnDimension('E')->setWidth(10);
//            $sheet->getColumnDimension('F')->setWidth(10);
//            $sheet->getColumnDimension('G')->setWidth(10);
//            $objPHPExcel->setActiveSheetIndex($i)->setCellValue('A1', $title);
//
//            $sheet->getStyle('A2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('A2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle('B2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('B2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle('C2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('C2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle('D2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('D2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle('E2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('E2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle('F2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('F2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//            $sheet->getStyle('G2')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('6C6C6C');
//            $sheet->getStyle('G2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//
//
//            $objPHPExcel->setActiveSheetIndex($i)
//                ->setCellValue('A2', 'Item #')
//                ->setCellValue('B2', 'Shape')
//                ->setCellValue('C2', 'Color')
//                ->setCellValue('D2', 'Pantone')
//                ->setCellValue('E2', 'Quantity')
//                ->setCellValue('F2', 'Cost Ea')
//                ->setCellValue('G2', 'Total Cost');
//            $j=3;
//            $styleArray = array(
//                'font'  => array(
//                    'color' => array('rgb' => 'FFFFFF'),
//                ));
//            $sheet->getStyle('A2')->applyFromArray($styleArray);
//            $sheet->getStyle('B2')->applyFromArray($styleArray);
//            $sheet->getStyle('C2')->applyFromArray($styleArray);
//            $sheet->getStyle('D2')->applyFromArray($styleArray);
//            $sheet->getStyle('E2')->applyFromArray($styleArray);
//            $sheet->getStyle('F2')->applyFromArray($styleArray);
//            $sheet->getStyle('G2')->applyFromArray($styleArray);
//            $addcost=$this->invaddcost();
//
//            foreach ($res as $row) {
//                // $price=round($row['price']+$addcost,3);
//                $price=round($row['price'],3);
//                $total=round($row['qty']*$price,2);
//                // Write Row
//                // $sheet->getStyle('E'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
//                $sheet->getStyle('F'.$j)->getNumberFormat()->setFormatCode("$0#.###");
//                $sheet->getStyle('G'.$j)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
//                $sheet->getStyle('A'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $sheet->getStyle('B'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $sheet->getStyle('C'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $sheet->getStyle('D'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $sheet->getStyle('E'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $sheet->getStyle('F'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//                $sheet->getStyle('G'.$j)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
//
//                $objPHPExcel->setActiveSheetIndex($i)
//                    ->setCellValue('A'.$j, $row['item_num'])
//                    ->setCellValue('B'.$j, $row['item_name'])
//                    ->setCellValue('C'.$j, $row['color'])
//                    ->setCellValue('D'.$j, $row['color_descript'])
//                    ->setCellValue('E'.$j, $row['qty'])
//                    ->setCellValue('F'.$j, $price)
//                    ->setCellValue('G'.$j, $total);
//                $j++;
//            }
//            // Write file
//            $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
//            $filename=$this->func->uniq_link(10).'.xls';
//
//            $filepath=$this->config->item('upload_path_preload').$filename;
//            $objWriter->save($filepath);
//            $url=$this->config->item('pathpreload').$filename;
//            $out['url']=$url;
//            $out['result']=$this->success_result;
//        }
//        return $out;
//    }

    function get_inventory_totals($brand) {

        $this->db->select('sum(suggeststock) as suggeststock');
        $this->db->from('ts_printshop_colors');
        $res=$this->db->get()->row_array();
        $max=intval($res['suggeststock']);

        $income=$this->printcolor_income(0, $brand);
        $outcome=$this->printcolor_outcome(0, $brand);
        $reserved=$this->printcolor_reserved(0, '', $brand);

        $instock=$income-$outcome;

        $available=$instock-$reserved;

        $stockperc=$this->empty_html_content;
        if ($max!=0) {
            $stockperc=round($instock/$max*100,0).'%';
        }
        $this->db->select('sum(suggeststock*price) as stocksum');
        $this->db->from('ts_printshop_colors');
        $sres=$this->db->get()->row_array();
        $maxsum=round(floatval($sres['stocksum']),2);
        // $availsum
        $out=array(
            'itempercent'=>$stockperc,
            'instock'=>($instock==0 ? $this->empty_html_content : QTYOutput($instock)),
            'reserved'=>($reserved==0 ? $this->empty_html_content : QTYOutput($reserved)),
            'available'=>($available==0 ? $this->empty_html_content : QTYOutput($available)),
            'total_max'=>'href="/fulfillment/max_total_percent/"',
            'maxsum'=>$maxsum,
            'max'=>($max==0 ? $this->empty_html_content : QTYOutput($max)),

        );
        return $out;
    }

    public function get_container_view($onboat_container, $colors) {
        $this->db->select('b.*');
        $this->db->from('ts_printshop_onboats b');
        $this->db->where('b.onboat_container', $onboat_container);
        $contdata=$this->db->get()->result_array();
        $out=array();
        foreach ($colors as $crow) {
            $cellval=$this->empty_html_content;
            if ($crow['type']=='item') {
                $this->db->select('count(b.printshop_onboat_id) as cnt, sum(b.onroutestock) as total');
                $this->db->from('ts_printshop_onboats b');
                $this->db->join('ts_printshop_colors c','c.printshop_color_id=b.printshop_color_id');
                $this->db->where('b.onboat_container', $onboat_container);
                $this->db->where('c.printshop_item_id', $crow['printshop_item_id']);
                $totres=$this->db->get()->row_array();
                if ($totres['cnt']>0) {
                    $cellval=  QTYOutput($totres['total']);
                }
            } else {
                foreach ($contdata as $brow) {
                    if ($brow['printshop_color_id']==$crow['printshop_color_id']) {
                        $cellval=QTYOutput($brow['onroutestock']);
                        break;
                    }
                }
            }
            $out[]=array(
                'printshop_item_id'=>$crow['printshop_item_id'],
                'printshop_color_id'=>$crow['printshop_color_id'],
                'type'=>$crow['type'],
                'onroutestock'=>$cellval,
            );
        }
        return $out;
    }

    public function get_container_edit($onboat_container) {
        $out=array('res'=>$this->error_result, 'msg'=>'Container Not Found');
        // Collect data from Inventory Items and Colors
        if ($onboat_container==0) {
            return $this->_new_container();
        } else {
            $colordata=$this->get_printshop_itemcolors();
            // Get data from container
            $this->db->select('b.*');
            $this->db->from('ts_printshop_onboats b');
            $this->db->where('b.onboat_container', $onboat_container);
            $contdata=$this->db->get()->result_array();
            if (count($contdata)>0) {
                $data=array();
                $details=array(
                    'onboat_container'=>$onboat_container,
                    'onboat_date'=>$contdata[0]['onboat_date'],
                    'onboat_status'=>$contdata[0]['onboat_status'],
                );
                $total=0;
                foreach ($colordata as $drow) {
                    $cellval=$this->empty_html_content;
                    $cellnval=0;
                    if ($drow['type']=='item') {
                        $onboatid=0;
                        $this->db->select('count(b.printshop_onboat_id) as cnt, sum(b.onroutestock) as total');
                        $this->db->from('ts_printshop_onboats b');
                        $this->db->join('ts_printshop_colors c','c.printshop_color_id=b.printshop_color_id');
                        $this->db->where('b.onboat_container', $onboat_container);
                        $this->db->where('c.printshop_item_id', $drow['printshop_item_id']);
                        $totres=$this->db->get()->row_array();
                        if ($totres['cnt']>0) {
                            $cellnval=$totres['total'];
                            $cellval=QTYOutput($totres['total']);
                        }
                    } else {
                        $onboatid=-1;
                        foreach ($contdata as $crow) {
                            if ($crow['printshop_color_id']==$drow['printshop_color_id']) {
                                $cellnval=$cellnval=$crow['onroutestock'];
                                $onboatid=$crow['printshop_onboat_id'];
                                $total+=$crow['onroutestock'];
                                break;
                            }
                        }
                    }
                    $data[] = array(
                        'printshop_item_id' => $drow['printshop_item_id'],
                        'printshop_color_id' => $drow['printshop_color_id'],
                        'printshop_onboat_id' => $onboatid,
                        'type' => $drow['type'],
                        'numval' => $cellnval,
                        'onroutestock' => $cellval,
                    );
                }
            }
            $out['data']=$data;
            $out['total']=$total;
            $out['details']=$details;
            $out['result']=$this->success_result;
        }
        return $out;
    }

    private function _new_container() {
        $colordata=$this->get_printshop_itemcolors();
        foreach ($colordata as $drow) {
            $cellval=$this->empty_html_content;
            $cellnval=0;
            if ($drow['type']=='item') {
                $onboatid=0;
            } else {
                $onboatid=-1;
            }
            $data[] = array(
                'printshop_item_id' => $drow['printshop_item_id'],
                'printshop_color_id' => $drow['printshop_color_id'],
                'printshop_onboat_id' => $onboatid,
                'type' => $drow['type'],
                'numval' => $cellnval,
                'onroutestock' => $cellval,
            );
        }
        $details=array(
            'onboat_container'=>-1,
            'onboat_date'=>time(),
            'onboat_status'=>0,
        );
        $out['data']=$data;
        $out['total']=0;
        $out['details']=$details;
        $out['result']=$this->success_result;
        $out['msg']='';
        return $out;
    }

    public function inventory_editcontainer($sessdata, $postdata) {
        $out=array('result'=>$this->error_result,'msg'=>'Unknown Parameter');
        if ($postdata['entity']=='color') {
            $out['msg']='Color Not Found';
            $data=$sessdata['data'];
            $total=$sessdata['total'];
            $found=0;
            $idx=0;
            foreach ($data as $row) {
                if ($row['printshop_color_id']==$postdata['color']) {
                    $found=1;
                    break;
                } else {
                    $idx++;
                }
            }
            // Color found
            if ($found==1) {
                $out['result']=$this->success_result;
                $oldval=$data[$idx]['numval'];
                $newval=intval($postdata['newval']);
                $data[$idx]['numval']=$newval;
                $newtotal=$total-$oldval+$newval;
                $sessdata['total']=$newtotal;
                $out['total']=$newtotal;
                $out['item']=$data[$idx]['printshop_item_id'];
                $itemtotal=0;
                for ($i=0; $i<=$idx; $i++) {
                    if ($data[$i]['printshop_item_id']==$out['item'] && $data[$i]['printshop_color_id']==0) {
                        $itemtotal=intval($data[$i]['numval']);
                        $newtotal=$itemtotal-$oldval+$newval;
                        $out['totalitem']=$newtotal;
                        $data[$i]['numval']=$newtotal;
                        $data[$i]['onroutestock']=QTYOutput($newtotal);
                        break;
                    }
                }
                $sessdata['data']=$data;
                $sessdata=$this->func->session($postdata['session'], $sessdata);
                // Item
            }
        } elseif ($postdata['entity']=='boatcontainerdate') {
            $details=$sessdata['details'];
            $details['onboat_date']=  strtotime($postdata['newval']);
            $sessdata['details']=$details;
            $this->func->session($postdata['session'], $sessdata);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    function inventory_savecontainer($sessdata, $session_id) {
        $out=array('result'=>$this->error_result,'msg'=>'Color Not Found');
        $data=$sessdata['data'];
        $total=$sessdata['total'];
        $details=$sessdata['details'];
        $onboat_container=$details['onboat_container'];
        if ($onboat_container<0) {
            // New Container
            $this->db->select('max(onboat_container) as maxval');
            $this->db->from('ts_printshop_onboats');
            $res=$this->db->get()->row_array();
            if (!isset($res['maxval'])) {
                $onboat_container=1;
            } else {
                $onboat_container=$res['maxval']+1;
            }
        }
        foreach ($data as $drow) {
            if ($drow['printshop_color_id']>0) {
                $newval=intval($drow['numval']);
                if ($newval==0)  {
                    if ($drow['printshop_onboat_id']>0) {
                        $this->db->where('printshop_onboat_id', $drow['printshop_onboat_id']);
                        $this->db->delete('ts_printshop_onboats');
                    }
                } else {
                    $this->db->set('onroutestock', $newval);
                    $this->db->set('onboat_date', $details['onboat_date']);
                    if ($drow['printshop_onboat_id']>0) {
                        $this->db->where('printshop_onboat_id', $drow['printshop_onboat_id']);
                        $this->db->update('ts_printshop_onboats');
                    } else {
                        $this->db->set('printshop_color_id', $drow['printshop_color_id']);
                        $this->db->set('onboat_container', $onboat_container);
                        $this->db->insert('ts_printshop_onboats');
                    }
                }
            }
        }
        // All saved
        $this->func->session($session_id, NULL);
        // Prepare colors
        $colordata=$this->get_printshop_itemcolors();
        $out['result']=$this->success_result;
        $out['onboat_container']=$onboat_container;
        $out['data']=$this->get_container_view($onboat_container, $colordata);
        return $out;
    }

    public function get_container_details($onboat_container) {
        $this->db->select('onboat_container, onboat_date, onboat_status, count(printshop_onboat_id) as cnt, sum(onroutestock) as onboat_total');
        $this->db->from('ts_printshop_onboats');
        $this->db->where('onboat_container', $onboat_container);
        $this->db->group_by('onboat_container, onboat_date, onboat_status');
        $res=$this->db->get()->row_array();
        return $res;
    }

//    public function get_printshop_itemcolors() {
//        $colordata=array();
//        $this->db->select('printshop_item_id');
//        $this->db->from('ts_printshop_items');
//        $this->db->order_by('item_num');
//        $items=$this->db->get()->result_array();
//        foreach ($items as $irow) {
//            $colordata[]=array(
//                'printshop_item_id'=>$irow['printshop_item_id'],
//                'printshop_color_id'=>0,
//                'type'=>'item',
//            );
//            $colors=$this->get_item_colors($irow['printshop_item_id']);
//            foreach ($colors as $crow) {
//                $colordata[]=array(
//                    'printshop_item_id'=>$irow['printshop_item_id'],
//                    'printshop_color_id'=>$crow['printshop_color_id'],
//                    'type'=>'color',
//                );
//            }
//        }
//        return $colordata;
//    }
//
//    public function get_invenory_level($printshop_income_id) {
//        $this->db->select('oa.printshop_color_id, oa.printshop_date, oa.shipped, oa.kepted, oa.misprint');
//        $this->db->from('ts_order_amounts oa');
//        $this->db->where('oa.amount_id', $printshop_income_id);
//        $amtdata=$this->db->get()->row_array();
//        // Get Prv
//        $this->db->select('sum(v.instock_amnt) as total');
//        $this->db->from('v_printshop_instock v');
//        $this->db->where('v.printshop_color_id', $amtdata['printshop_color_id']);
//        $this->db->where('v.instock_date < ', $amtdata['printshop_date']);
//        $strockres=$this->db->get()->row_array();
//        $instock=intval($strockres['total']);
//        $outcome=intval($amtdata['shipped'])+intval($amtdata['kepted'])+intval($amtdata['misprint']);
//        $balance=$instock-$outcome;
//        return array(
//            'instock'=>$instock,
//            'outcome'=>$outcome,
//            'balance'=>$balance,
//        );
//    }
//
//    public function get_needinvlistdata($options) {
//        $this->db->select('i.printshop_item_id, i.item_num, i.item_name, c.printshop_color_id');
//        $this->db->select('c.color, c.price, c.suggeststock, inventory_instock(c.printshop_color_id) as instock');
//        $this->db->select("if(c.suggeststock=0,1,(inventory_instock(c.printshop_color_id)+onboat_notarrived(c.printshop_color_id))/c.suggeststock) as aftercontproc",FALSE);
//        $this->db->select('onboat_notarrived(c.printshop_color_id) as  notarrived');
//        $this->db->select('i.plate_temp, i.proof_temp, c.color_descript as specfile');
//        $this->db->select('i.item_label');
//        $this->db->from('ts_printshop_items i');
//        $this->db->join('ts_printshop_colors c','c.printshop_item_id=i.printshop_item_id');
//        // Options where
//        $this->db->where('c.notreorder',0);
//        if (isset($options['orderby'])) {
//            if (isset($options['direct'])) {
//                $this->db->order_by($options['orderby'], $options['direct']);
//            } else {
//                $this->db->order_by($options['orderby']);
//            }
//        } else {
//            $this->db->order_by('printshop_item_id', 'desc');
//        }
//        $res=$this->db->get()->result_array();
//        $numpp=1;
//        $invent=array();
//        foreach ($res as $row) {
//            $row['needtomake']=0;
//            $row['specsclass']='empty';
//            $row['specsurl']='';
//            if (!empty($row['specfile'])) {
//                // specsdata full
//                $row['specsclass']='full';
//                if ($options['place']=='inventory') {
//                    $row['specsurl']='href="/inventory/specs_bt/?id='.$row['printshop_color_id'].'"';
//                } else {
//                    $row['specsurl']='href="/fulfillment/specs_bt/?id='.$row['printshop_color_id'].'"';
//                }
//            }
//            $row['itemlabel']=(empty($row['item_label']) ? 'empty' : 'full');
//            $row['platetemp']=(empty($row['plate_temp']) ? 'empty' : 'full');
//            $row['prooftemp']=(empty($row['proof_temp']) ? 'empty' : 'full');
//            $pics = $this->get_picsattachments($row['printshop_color_id']);
//
//            $row['picsclass']=(count($pics) >0 ? 'full' : 'empty');
//
//            $need=$row['suggeststock']-($row['instock']+$row['notarrived']);
//            if ($need>0) {
//                $row['needtomake']=$need;
//            }
//            $row['aftercontproc']=round($row['aftercontproc']*100,0);
//            $row['needclass']='';
//            if ($row['aftercontproc']<=$this->config->item('invoutstock') && $need>0) {
//                $row['needclass']='red';
//            } elseif ($row['aftercontproc']<=$this->config->item('invlowstock') && $need>0) {
//                $row['needclass']='orange';
//            }
//            $row['numpp']=$numpp;
//            $numpp++;
//            $invent[]=$row;
//        }
//        $out=array(
//            'inventory'=>$invent,
//        );
//        return $out;
//    }
//
//    public function get_needinvlistboat_details($onboat_container, $colors) {
//        $this->db->select('b.*');
//        $this->db->from('ts_printshop_onboats b');
//        $this->db->where('b.onboat_container', $onboat_container);
//        $contdata=$this->db->get()->result_array();
//        $out=array();
//        foreach ($colors as $crow) {
//            $cellval=$this->empty_html_content;
//            foreach ($contdata as $brow) {
//                if ($brow['printshop_color_id']==$crow['printshop_color_id']) {
//                    $cellval=QTYOutput($brow['onroutestock']);
//                    break;
//                }
//            }
//            $out[]=array(
//                'printshop_item_id'=>$crow['printshop_item_id'],
//                'printshop_color_id'=>$crow['printshop_color_id'],
//                'onroutestock'=>$cellval,
//            );
//        }
//        return $out;
//    }
//
//    public function get_report_years() {
//        $this->db->select("date_format(from_unixtime(oa.amount_date),'%Y') as year_amount, count(oa.amount_id) as cnt",FALSE);
//        $this->db->from('ts_order_amounts oa');
//        $this->db->where('oa.printshop',1);
//        $this->db->group_by('year_amount');
//        $this->db->order_by('year_amount','desc');
//        $res=$this->db->get()->result_array();
//        // Additional Options
//        return $res;
//    }
//
//    public function export_inventory() {
//        $out=array('msg'=>$this->error_message, 'result'=>$this->error_result);
//
//        $options=array(
//            'orderby'=>'item_num',
//        );
//        $invres=$this->get_printshopitems($options);
//        $res=$invres['inventory'];
//        $this->firephp->log($res[0]);
//        $filename='export_inventory_'.time().'.xls';
//        // die();
//        $filesrc=$this->config->item('upload_path_preload').$filename;
//        @unlink($filesrc);
//        // Prepare Export file
//        $this->load->library('PHPExcel');
//        $this->load->library('PHPExcel/IOFactory');
//        $objPHPExcel = new PHPExcel();
//        //
//        $objPHPExcel->getProperties()->setCreator("PHP");
//        $namesheet = 'inventory_export';
//        $objPHPExcel->getActiveSheet()->setTitle($namesheet);
//        $styleWhite = array(
//            'font' => array(
//                'bold' => false,
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//            ),
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN,
//                )
//            ),
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_NONE
//            ),
//        );
//
//
//        $styleGray = array(
//            'font' => array(
//                'bold' => true,
//                'color' => array(
//                    'argb' => 'FFFFFFFF')
//            ),
//            'alignment' => array(
//                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//            ),
//            'borders' => array(
//                'allborders' => array(
//                    'style' => PHPExcel_Style_Border::BORDER_THIN,
//                )
//            ),
//            'fill' => array(
//                'type' => PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY
//            ),
//        );
//        /* sheet */
//        $sheet = $objPHPExcel->getActiveSheet();
//        $sheet->setTitle('Price Report');
//        $sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT);
//        $sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);
//        $sheet->getColumnDimension('A')->setAutoSize(); // Item #
//        $sheet->getColumnDimension('B')->setAutoSize(); // Shape/ Color
//        $sheet->getColumnDimension('C')->setAutoSize(); // Color Descript
//        $sheet->getColumnDimension('D')->setAutoSize(); // In Stock
//        $sheet->getColumnDimension('E')->setAutoSize(); // Reserved
//        $sheet->getColumnDimension('F')->setAutoSize(); // Available
//        $sheet->getColumnDimension('G')->setAutoSize(); // Cost Ea
//        $sheet->getColumnDimension('H')->setAutoSize(); // Total Ea
//        $sheet->setCellValue('A1','Item #');
//        $sheet->setCellValue('B1','Shape/ Color');
//        $sheet->setCellValue('C1','Color Descript');
//        $sheet->setCellValue('D1','%');
//        $sheet->setCellValue('E1','In Stock');
//        $sheet->setCellValue('F1','Reserved');
//        $sheet->setCellValue('G1','Available');
//        $sheet->setCellValue('H1','Cost Ea');
//        $sheet->setCellValue('I1','Total Ea');
//        $numrow=2;
//        foreach ($res as $row) {
//            if ($row['type']=='item') {
//                $sheet->setCellValue('A'.$numrow,$row['item_num']);
//                $sheet->setCellValue('B'.$numrow, $row['item_name']);
//            } else {
//                $sheet->setCellValue('B'.$numrow,$row['item_name']);
//                $sheet->setCellValue('C'.$numrow,$row['color_descript']);
//            }
//            /*
//            'price_int'=>$crow['price'],
//            'total_int'=>round($totalea,3),
//            'instock_int'=>$instock,
//            'reserved_int'=>$reserved,
//            'availabled_int'=>$available,
//
//             */
//            $sheet->setCellValue('D'.$numrow, str_replace('&nbsp;','0',$row['percent']));
//            $sheet->setCellValue('E'.$numrow, $row['instock_int']);
//            $sheet->setCellValue('F'.$numrow, $row['reserved_int']);
//            $sheet->setCellValue('G'.$numrow, $row['availabled_int']);
//            $sheet->setCellValue('H'.$numrow, $row['price_int']);
//            $sheet->setCellValue('I'.$numrow, $row['total_int']);
//            $numrow++;
//        }
//
//        $objWriter = IOFactory::createWriter($objPHPExcel, 'Excel5');
//        $objWriter->save($filesrc);
//        $out['result']=$this->success_result;
//        $out['url']=$this->config->item('pathpreload').$filename;
//        return $out;
//    }

}