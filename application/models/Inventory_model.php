<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_model extends MY_Model
{

    private $outstockclass='severevalstock';
    private $outstoklabel='Out of Stock';
    private $lowstockclass='lowinstock';
    private $donotreorder = 'Do Not Reorder';
    private $bt_label = 'Bluetrack Legacy';
    private $sb_label = 'StressBalls.com';
    private $sr_label = 'StressRelievers';

    function __construct()
    {
        parent::__construct();
    }

    public function get_inventory_types() {
        $this->db->select('*');
        $this->db->from('ts_inventory_types');
        $this->db->order_by('type_order');
        return $this->db->get()->result_array();
    }

    public function get_masterinvent_list($inventory_type, $inventory_filter) {
        $type_instock = $type_available = $type_maximum = 0;
        $this->db->select('*');
        $this->db->from('ts_inventory_items');
        $this->db->where('inventory_type_id', $inventory_type);
        $this->db->order_by('item_order');
        $items=$this->db->get()->result_array();
        $out = [];
        foreach ($items as $item) {
            $additem = 1;
            if ($inventory_filter) {
                $this->db->select('count(*) as cnt');
                $this->db->from('ts_inventory_colors');
                $this->db->where('inventory_item_id', $item['inventory_item_id']);
                if ($inventory_filter==1) {
                    $this->db->where('color_status', 1);
                } elseif ($inventory_filter==2) {
                    $this->db->where('color_status',0);
                }
                $cntdat = $this->db->get()->row_array();
                $additem = $cntdat['cnt'] ==0 ? 0 : 1;
            }
            // Add item row
            if ($additem) {
                $out[]=[
                    'id' => $item['inventory_item_id'],
                    'item_flag' =>1,
                    'status' => ($item['item_status']==1 ? 'Active' : 'Inactive'),
                    'item_seq' => '',
                    'item_code' => $item['item_num'],
                    'description' => $item['item_name'],
                    'max' => 0,
                    'percent' => 0,
                    'stockclass' => '',
                    'instock' => 0,
                    'reserved' => 0,
                    'available' => 0,
                    'unit' => $item['item_unit'],
                    'onorder' => 0,
                    'price' => 0,
                    'total' => 0,
                    'noreorder' => 0,
                    'totalclass' => '',
                ];
                $itemidx = count($out) - 1;
                $sum_available = 0;
                $sum_instock = 0;
                $sum_reserved = 0;
                $sum_max = 0;
                $total_invent = 0;
                // Get colors
                $this->db->select('*');
                $this->db->from('ts_inventory_colors');
                $this->db->where('inventory_item_id', $item['inventory_item_id']);
                if ($inventory_filter==1) {
                    $this->db->where('color_status', 1);
                } elseif ($inventory_filter==2) {
                    $this->db->where('color_status',0);
                }
                $this->db->order_by('color_order');
                $colors = $this->db->get()->result_array();
                $color_seq = 1;
                foreach ($colors as $color) {
                    // Get income, outcome etc
                    $income = $this->inventory_color_income($color['inventory_color_id']);
                    $outcome = $this->inventory_color_outcome($color['inventory_color_id']);
                    $reserved = $this->inventory_color_reserved($color['inventory_color_id']);
                    $instock=$income-$outcome;
                    $sum_instock = $sum_instock + $instock;
                    $available=$instock-$reserved;
                    $total_invent+=($available*$color['price']);
                    $sum_available = $sum_available + $available;
                    $sum_reserved = $sum_reserved + $reserved;
                    $max=$color['suggeststock'];
                    $sum_max = $sum_max + $max;
                    $stockperc='';
                    $stockclass='';
                    if ($max!=0) {
                        $stockperc=round($instock/$max*100,0);
                        if ($stockperc <= $this->config->item('invoutstock')) {
                            $stockclass = $this->outstockclass;
                        } elseif ($stockperc <= $this->config->item('invlowstock')) {
                            $stockclass = $this->lowstockclass;
                        }
                    }
                    $outstock = QTYOutput($instock);
                    if ($stockclass==$this->outstockclass && empty($stockperc)) {
                        $outstock=$this->outstoklabel;
                    }
                    $outavail = QTYOutput($available);
                    if ($stockclass==$this->outstockclass && empty($stockperc)) {
                        $outavail=$this->outstoklabel;
                    }
                    $totalclass='';
                    if (empty($available)) {
                        $totalclass = 'emptytotal';
                    }
                    $out[]=[
                        'id' => $color['inventory_color_id'],
                        'item_id' => $item['inventory_item_id'],
                        'item_flag' =>0,
                        'status' => ($color['notreorder']==1 ? $this->donotreorder : ($color['color_status']==1 ? 'Active' : 'Inactive')),
                        'item_seq' => $color['color_order'], // $color_seq,
                        'item_code' => '',
                        'description' => $color['color'],
                        'max' => $max,
                        'percent' => $stockperc,
                        'stockclass' => $stockclass,
                        'instock' => $outstock,
                        'reserved' => $reserved,
                        'available' => $outavail,
                        'unit' => $color['color_unit'],
                        'onorder' => 0, // ????
                        'price' => $color['price'],
                        'total' => $available*$color['price'],
                        'noreorder' => $color['notreorder'],
                        'totalclass' => $totalclass,
                    ];
                    $color_seq++;
                    // Calc totals
                }
                $out[$itemidx]['max']=$sum_max;
                $stockperc = 0;
                $stockclass = '';
                if ($sum_max !=0 ) {
                    $stockperc=round($sum_instock/$sum_max*100,0);
                    if ($stockperc <= $this->config->item('invoutstock')) {
                        $stockclass = $this->outstockclass;
                    } elseif ($stockperc <= $this->config->item('invlowstock')) {
                        $stockclass = $this->lowstockclass;
                    }
                }

                $out[$itemidx]['percent'] = $stockperc;
                $out[$itemidx]['stockclass'] = $stockclass;
                $out[$itemidx]['instock'] = QTYOutput($sum_instock);
                $out[$itemidx]['reserved'] = $sum_reserved;
                $out[$itemidx]['available'] = QTYOutput($sum_available);
                $type_instock += $sum_instock;
                $type_available += $sum_available;
                $type_maximum += $sum_max;
                if ($sum_available!=0) {
                    $out[$itemidx]['price'] = round($total_invent / $sum_available,3);
                }
                $out[$itemidx]['total'] = $total_invent;
            }
        }
        return
            [
                'type_instock' => $type_instock,
                'type_available' => $type_available,
                'type_maximum' => $type_maximum,
                'list' => $out,
            ];
    }

    public function inventory_color_income($inventory_color_id) {
        $this->db->select('sum(income_qty) as income, count(inventory_income_id) as cnt');
        $this->db->from('ts_inventory_incomes');
        $this->db->where('inventory_color_id', $inventory_color_id);
        $income=$this->db->get()->row_array();
        $backup = 0;
        if($income['cnt']>0) {
            $backup = intval($income['income']);
        }
        return $backup;
    }

    public function inventory_color_outcome($inventory_color_id) {
        $this->db->select('sum(outcome_qty) as outcome, count(inventory_outcome_id) as cnt');
        $this->db->from('ts_inventory_outcomes');
        $this->db->where('inventory_color_id', $inventory_color_id);
        $outcome=$this->db->get()->row_array();
        $outqty = 0;
        if($outcome['cnt']>0) {
            $outqty = intval($outcome['outcome']);
        }
        return $outqty;
    }

    public function inventory_color_reserved($inventory_color_id) {
        // Params $printshop_item_id, $color, $brand
        return 0;
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

    public function get_inventtype_stock($inventory_type_id) {
        $this->db->select('inventory_color_id, sum(income_qty) as qty_in');
        $this->db->from('ts_inventory_incomes');
        $this->db->group_by('inventory_color_id');
        $incomesql = $this->db->get_compiled_select();
        $this->db->select('inventory_color_id, sum(outcome_qty) as qty_out');
        $this->db->from('ts_inventory_outcomes');
        $this->db->group_by('inventory_color_id');
        $outcomesql = $this->db->get_compiled_select();
        $this->db->select('sum((coalesce(invincom.qty_in,0)-coalesce(invoutcom.qty_out,0))*colors.price) as total');
        $this->db->from('ts_inventory_colors colors');
        $this->db->join('ts_inventory_items items','items.inventory_item_id=colors.inventory_item_id');
        $this->db->join('('.$incomesql.') invincom','invincom.inventory_color_id=colors.inventory_color_id','left');
        $this->db->join('('.$outcomesql.') invoutcom','invoutcom.inventory_color_id=colors.inventory_color_id','left');
        $this->db->where('items.inventory_type_id', $inventory_type_id);
        $res = $this->db->get()->row_array();
        return floatval($res['total']);
    }

    public function get_masterinventory_color($inventory_color_id, $showhiden=0) {
        $out=['result' => $this->error_result, 'msg' => 'Item / color not exist'];
        $this->db->select('c.color, i.item_num, item_name, c.inventory_color_id');
        $this->db->from('ts_inventory_colors c');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('c.inventory_color_id', $inventory_color_id);
        $itemdat = $this->db->get()->row_array();
        if (isset($itemdat['inventory_color_id'])) {
            $out['result'] = $this->success_result;
            $out['itemdata'] = $itemdat;
            // Get List
            $this->db->select('*, (income_qty-income_expense) as income_left, (income_qty-income_expense)*income_price as income_left_total');
            $this->db->from('ts_inventory_incomes');
            $this->db->where('inventory_color_id', $inventory_color_id);
            $this->db->order_by('income_date desc');
            if ($showhiden==0) {
                $this->db->having('income_left > 0');
            }
            $lists = $this->db->get()->result_array();
            $balance_qty = $balance_total = 0;
            $idx=0;
            foreach ($lists as $list) {
                $balance_qty += ($list['income_qty']-$list['income_expense']); // Add outcome
                $balance_total+= ($list['income_qty']-$list['income_expense'])*$list['income_price'];
                $lists[$idx]['rowclass']='';
                if ($list['income_left'] > 0 &&  $list['income_qty']!==$list['income_left']) {
                    $lists[$idx]['rowclass']='lastrow';
                } elseif ($list['income_left']==0) {
                    $lists[$idx]['rowclass']='used';
                }
                $idx++;
            }
            $out['lists'] = $lists;
            $totals = [
                'balance_qty' => $balance_qty,
                'balance_total' => $balance_total,
                'avg_price' => ($balance_qty==0 ? 0 : round($balance_total/$balance_qty,3)),
            ];
            $out['totals'] = $totals;
        }
        return $out;
    }

    public function get_masterinventory_colorhistory($inventory_color_id) {
        $out=['result' => $this->error_result, 'msg' => 'Item / color not exist'];
        $this->db->select('c.color, i.item_num, item_name, c.inventory_color_id');
        $this->db->from('ts_inventory_colors c');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('c.inventory_color_id', $inventory_color_id);
        $itemdat = $this->db->get()->row_array();
        if (isset($itemdat['inventory_color_id'])) {
            $out['result'] = $this->success_result;
            $out['itemdata'] = $itemdat;
            $this->db->select('*');
            $this->db->from('v_inventory_instock');
            $this->db->where('color_id', $inventory_color_id);
            $this->db->order_by('instock_date','desc');
            $stocks = $this->db->get()->result_array();
            $lists = [];
            foreach ($stocks as $stock) {
                $descrip = $stock['instock_description'];
                if ($stock['instock_type']=='O' && $stock['brand']!='M') {
                    $descrip='<span>Order - </span>';
                    if ($stock['brand']=='BT') {
                        $descrip.=$this->bt_label;
                    } elseif ($stock['brand']=='SB') {
                        $descrip.=$this->sb_label;
                    } else {
                        $descrip.=$this->sr_label;
                    }
                }
                $lists[]=[
                    'id' => $stock['instock_id'],
                    'type' => $stock['instock_type'],
                    'date' => $stock['instock_date'],
                    'record' => $stock['instock_record'],
                    'description' => $descrip,
                    'amount' => $stock['instock_qty'],
                    'balance' => 0,
                ];
            }
            // Calc balance
            $listcnt = count($lists) - 1;
            $balance = 0;
            for ($i=$listcnt; $i>=0; $i--) {
                if ($lists[$i]['type']=='O') {
                    $balance-=$lists[$i]['amount'];
                } else {
                    $balance+=$lists[$i]['amount'];
                }
                $lists[$i]['balance']=$balance;
            }
            $out['lists'] = $lists;
        }
        return $out;
    }

    public function get_masterinventory_item($item, $invtype) {
        if ($item==0) {
            return $this->new_masterinventory_item($invtype);
        } else {
            $out=['result' => $this->error_result, 'msg' => 'Item not exist'];
            $this->db->select('*');
            $this->db->from('ts_inventory_items');
            $this->db->where('inventory_item_id', $item);
            $itemdata = $this->db->get()->row_array();
            if (isset($itemdata['inventory_item_id'])) {
                $out['result'] = $this->success_result;
                $out['itemdata'] = $itemdata;
            }
            return $out;
        }
    }

    public function new_masterinventory_item($invtype) {
        $out=['result' => $this->error_result, 'msg' => 'Inventory type not exist'];
        // Get data about Inventory type
        $this->db->select('*');
        $this->db->from('ts_inventory_types');
        $this->db->where('inventory_type_id', $invtype);
        $invdata = $this->db->get()->row_array();
        if (isset($invdata['inventory_type_id'])) {
            $out['result']=$this->success_result;
            // Get max Item Number
            $this->db->select('max(item_num) as maxnum');
            $this->db->from('ts_inventory_items');
            $this->db->where('inventory_type_id', $invtype);
            $dat = $this->db->get()->row_array();
            if (isset($dat['maxnum'])) {
                $newnum = intval(substr($dat['maxnum'],4))+1;
            } else {
                $newnum = 1;
            }
            $item_data = [
                'inventory_item_id' => -1,
                'inventory_type_id' => $invtype,
                'item_num' => $invdata['type_short'].'-'.str_pad($newnum,3,'0',STR_PAD_LEFT),
                'item_name' => '',
                'item_unit' => 'pc',
                'proof_templte' => '',
                'plate_template' => '',
                'box_template' => '',
            ];
            $out['itemdata'] = $item_data;
        }
        return $out;
    }

    public function masterinventory_item_save($itemdata) {
        $out=['result' => $this->error_result, 'msg' => 'Inventory type not exist'];
        // Check data
        $chkres = $this->_check_masteritem($itemdata);
        $out['msg'] = $chkres['msg'];
        if ($chkres['result']==$this->success_result) {
            if ($itemdata['inventory_item_id']<0) {

            }
            $this->db->set('item_name', $itemdata['item_name']);
            $this->db->set('item_unit', $itemdata['item_unit']);
            if ($itemdata['inventory_item_id']<0) {
                $out['msg'] = 'Error during add new item';
                $this->db->insert('ts_inventory_items');
                $newid = $this->db->insert_id();
                if ($newid) {
                    $out['result'] = $this->success_result;
                    $itemdata['inventory_item_id'] = $newid;
                }
            } else {
                $this->db->where('inventory_item_id', $itemdata['inventory_item_id']);
                $this->db->update('ts_inventory_items');
                $out['result'] = $this->success_result;
            }
            if ($out['result']==$this->success_result) {
                // Analyse Templates
                if ($itemdata['proofflag']==1) {
                    $preload_sh = $this->config->item('pathpreload');
                    $preload_fl = $this->config->item('upload_path_preload');
                    $proof_sh = $this->config->item('invprooftemp_relative');
                    $proof_fl = $this->config->item('invprooftemp');
                    createPath($proof_sh);
                    $filetempl = str_replace($preload_sh,'', $itemdata['proofsrc']);
                    $srcfile = $preload_fl.$filetempl;
                    $distfile = $proof_fl.$filetempl;
                    $rescp = @copy($srcfile, $distfile);
                    if ($rescp) {
                        $this->db->where('inventory_item_id', $itemdata['inventory_item_id']);
                        $this->db->set('proof_templte', $proof_sh.$filetempl);
                        $this->db->set('proof_template_source', $itemdata['proofname']);
                        $this->db->update('ts_inventory_items');
                    }
                }
                if ($itemdata['plateflag']==1) {
                    $preload_sh = $this->config->item('pathpreload');
                    $preload_fl = $this->config->item('upload_path_preload');
                    $plate_fl = $this->config->item('invplatetemp');
                    $plate_sh = $this->config->item('invplatetemp_relative');
                    createPath($plate_sh);
                    $filetempl = str_replace($preload_sh,'', $itemdata['platesrc']);
                    $srcfile = $preload_fl.$filetempl;
                    $distfile = $plate_fl.$filetempl;
                    $rescp = @copy($srcfile, $distfile);
                    if ($rescp) {
                        $this->db->where('inventory_item_id', $itemdata['inventory_item_id']);
                        $this->db->set('plate_template', $plate_sh.$filetempl);
                        $this->db->set('plate_template_source', $itemdata['platename']);
                        $this->db->update('ts_inventory_items');
                    }
                }
                if ($itemdata['boxflag']==1) {
                    $preload_sh = $this->config->item('pathpreload');
                    $preload_fl = $this->config->item('upload_path_preload');
                    $box_fl = $this->config->item('invboxtemp');
                    $box_sh = $this->config->item('invboxtemp_relative');
                    createPath($box_sh);
                    $filetempl = str_replace($preload_sh,'', $itemdata['boxsrc']);
                    $srcfile = $preload_fl.$filetempl;
                    $distfile = $box_fl.$filetempl;
                    $rescp = @copy($srcfile, $distfile);
                    if ($rescp) {
                        $this->db->where('inventory_item_id', $itemdata['inventory_item_id']);
                        $this->db->set('box_template', $box_sh.$filetempl);
                        $this->db->set('box_template_source', $itemdata['boxname']);
                        $this->db->update('ts_inventory_items');
                    }
                }
            }
        }
        return $out;
    }

    private function _check_masteritem($itemdata) {
        $out = ['result' => $this->success_result, 'msg' => 'Test Result'];
        return $out;
    }
}