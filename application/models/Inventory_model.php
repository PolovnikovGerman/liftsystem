<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_model extends MY_Model
{

    private $outstockclass = 'severevalstock';
    private $outstoklabel = 'No Stock'; // 'Out of Stock';
    private $emptystockclass = 'emptyvalstock';
    private $lowstockclass = 'lowinstock';
    private $donotreorder = 'Do Not Reorder';
    private $bt_label = 'Bluetrack Legacy';
    private $sb_label = 'StressBalls.com';
    private $sr_label = 'StressRelievers';
    private $empty_html_content='&nbsp;';
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
        $colorsdata = [];
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
                    'status' => '', // ($item['item_status']==1 ? 'Active' : 'Inactive'),
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
                    'avg_price' => '',
                    'total' => 0,
                    'noreorder' => 0,
                    'totalclass' => '',
                ];
                $itemidx = count($out) - 1;
                $colorsdata[] = [
                    'item_id' => $item['inventory_item_id'],
                    'color_id' => 0,
                ];
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
                    $total_invent+=($available*$color['avg_price']);
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
                        $stockclass = $this->emptystockclass;
                    }
                    $outavail = QTYOutput($available);
                    if ($stockclass==$this->outstockclass && empty($stockperc)) {
                        $outavail=$this->outstoklabel;
                        $stockclass = $this->emptystockclass;
                    }
                    $totalclass='';
                    if (empty($available)) {
                        $totalclass = 'emptytotal';
                    }
                    // $avgprice = $available==0 ? 0 :
                    $out[]=[
                        'id' => $color['inventory_color_id'],
                        'item_id' => $item['inventory_item_id'],
                        'item_flag' =>0,
                        'status' => ($color['notreorder']==1 ? $this->donotreorder : ''), // ($color['color_status']==1 ? 'Active' : 'Inactive')),
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
                        'avg_price' => $color['avg_price'],
                        'total' => $available*$color['avg_price'],
                        'noreorder' => $color['notreorder'],
                        'totalclass' => $totalclass,
                        'color_image' => $color['color_image'],
                    ];
                    $color_seq++;
                    $colorsdata[] = [
                        'item_id' => $item['inventory_item_id'],
                        'color_id' => $color['inventory_color_id'],
                    ];
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
                'colors' => $colorsdata,
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
                if ($stock['instock_type']=='S') {
                    $rectype = 'income';
                } elseif ($stock['instock_type']=='O') {
                    if (empty($stock['order_id'])) {
                        $rectype='outadjust';
                    } else {
                        $rectype='order';
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
                    'rectype' => $rectype,
                    'order' => $stock['order_id'],
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
                $this->db->select('*');
                $this->db->from('ts_inventory_types');
                $this->db->where('inventory_type_id', $itemdata['inventory_type_id']);
                $invdata = $this->db->get()->row_array();
                if (!isset($invdata['inventory_type_id'])) {
                    return $out;
                }
                // Get new Item # and order
                $this->db->select('max(item_num) as maxnum, max(item_order) as maxord');
                $this->db->from('ts_inventory_items');
                $this->db->where('inventory_type_id', $itemdata['inventory_type_id']);
                $dat = $this->db->get()->row_array();
                if (isset($dat['maxnum'])) {
                    $newnum = intval(substr($dat['maxnum'],4))+1;
                    $neword = intval($dat['maxord'])+1;
                } else {
                    $newnum = 1;
                    $neword = 1;
                }
                $this->db->set('inventory_type_id', $itemdata['inventory_type_id']);
                $this->db->set('item_num', $invdata['type_short'].'-'.str_pad($newnum,3,'0',STR_PAD_LEFT));
                $this->db->set('item_order', $neword);
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
                        $this->db->set('proof_template', $proof_sh.$filetempl);
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
        if (empty($itemdata['item_name'])) {
            $out['msg']='Empty Item Name';
            $out['result'] = $this->error_result;
        }
        if (empty($itemdata['item_unit'])) {
            $out['msg']='Empty Item Unit';
            $out['result'] = $this->error_result;
        }
        if ($itemdata['proofflag']==1 && empty($itemdata['proofsrc'])) {
            $out['msg']='Empty Proof Template';
            $out['result'] = $this->error_result;
        }
        if ($itemdata['plateflag']==1 && empty($itemdata['platesrc'])) {
            $out['msg']='Empty Plate Template';
            $out['result'] = $this->error_result;
        }
        if ($itemdata['boxflag']==1 && empty($itemdata['boxsrc'])) {
            $out['msg']='Empty Box Template';
            $out['result'] = $this->error_result;
        }
        return $out;
    }

    public function get_inventory_mastercolor($color, $item) {
        $out = ['result' => $this->error_result, 'msg' => 'Unknown Master Item'];
        if (empty($color)) {
            return $this->new_masterinventory_color($item);
        }
        $out['msg'] = 'Master Color Not Found';
        $this->db->select('i.item_num, i.item_name, i.inventory_item_id, c.*');
        $this->db->from('ts_inventory_colors c');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('c.inventory_color_id', $color);
        $res = $this->db->get()->row_array();
        if (isset($res['inventory_color_id'])) {
            $out['result'] = $this->success_result;
            $out['colordata'] = $res;
            $this->db->select('vc.*, v.vendor_name');
            $this->db->from('ts_invcolor_vendors vc');
            $this->db->join('vendors v','v.vendor_id=vc.vendor_id','left');
            $this->db->where('vc.inventory_color_id', $color);
            $venddat = $this->db->get()->result_array();
            $out['vendordat'] = $venddat;
        }
        return $out;
    }

    public function new_masterinventory_color($item) {
        $out = ['result' => $this->error_result, 'msg' => 'Unknown Master Item'];
        if (!empty($item)) {
            $this->db->select('inventory_item_id, item_num, item_name, item_unit');
            $this->db->from('ts_inventory_items');
            $this->db->where('inventory_item_id', $item);
            $itemdat = $this->db->get()->row_array();
            if (isset($itemdat['inventory_item_id'])) {
                $colordat = [
                    'inventory_color_id' => -1,
                    'inventory_item_id' => $item,
                    'item_num' => $itemdat['item_num'],
                    'item_name' => $itemdat['item_name'],
                    'color' => '',
                    'price' => 0,
                    'color_status' => 1,
                    'color_unit' => $itemdat['item_unit'],
                    'suggeststock' => 0,
                    'notreorder' => 0,
                    'pantones' => '',
                    'color_image' => '',
                    'color_image_source' => '',
                ];
                $vidx = 1;
                $vendors = [];
                for ($i=0; $i<5; $i++) {
                    $vendors[] = [
                        'invcolor_vendor_id' => -1*$vidx,
                        'vendor_id' => '',
                        'price' => 0,
                    ];
                    $vidx++;
                }
                $out['result'] = $this->success_result;
                $out['colordata'] = $colordat;
                $out['vendordat'] = $vendors;
            }
        }
        return $out;
    }

    public function mastercolor_change($sessiondat, $fld, $newval, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Unknown Color Parameter'];
        $colordat = $sessiondat['color'];
        if (array_key_exists($fld, $colordat)) {
            $colordat[$fld] = $newval;
            $out['result'] = $this->success_result;
            $sessiondat['color'] = $colordat;
            usersession($session_id, $sessiondat);
        }
        return $out;
    }

    public function mastercolor_vendorchange($sessiondat, $vendlist, $fld, $newval, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Vendor Not Found'];
        $vendorlists = $sessiondat['vendors'];
        $find = 0;
        $idx = 0;
        foreach ($vendorlists as $vendorlist) {
            if ($vendorlist['invcolor_vendor_id']==$vendlist) {
                $find = 1;
                break;
            }
            $idx++;
        }
        if ($find==1) {
            if ($fld=='price') {
                $newval = floatval($newval);
            }
            $vendorlists[$idx][$fld] = $newval;
            $sessiondat['vendors'] = $vendorlists;
            usersession($session_id, $sessiondat);
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function mastercolor_updateimg($sessiondat, $doc_url, $doc_src, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Empty Image'];
        if (!empty($doc_url)) {
            $colordat = $sessiondat['color'];
            $colordat['newitemimage'] = 1;
            $colordat['color_image'] = $doc_url;
            $colordat['color_image_source'] = $doc_src;
            $sessiondat['color'] = $colordat;
            usersession($session_id, $sessiondat);
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function masterinventory_color_save($sessiondat, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Unknown Master Item'];
        $colordat = $sessiondat['color'];
        $vendordat = $sessiondat['vendors'];
        // Check data
        $chkres = $this->_check_mastercolor($colordat, $vendordat);
        $out['msg'] = $chkres['msg'];
        if ($chkres['result']==$this->success_result) {
            // Save
            if ($colordat['inventory_color_id']<0) {
                $this->db->select('max(color_order) maxord');
                $this->db->from('ts_inventory_colors');
                $this->db->where('inventory_item_id', $colordat['inventory_item_id']);
                $dat = $this->db->get()->row_array();
                if (isset($dat['maxord'])) {
                    $neword = $dat['maxord'] + 1;
                } else {
                    $neword = 1;
                }
                // New Color
                $this->db->set('inventory_item_id', $colordat['inventory_item_id']);
                $this->db->set('color_order', $neword);
            } else {
                $this->db->where('inventory_color_id', $colordat['inventory_color_id']);
            }
            $this->db->set('color', $colordat['color']);
            if ($colordat['color_status']==0) {
                $this->db->set('notreorder', 1);
            } else {
                $this->db->set('color_status', 1);
                $this->db->set('notreorder', 0);
            }
            $this->db->set('color_status', $colordat['color_status']);
            $this->db->set('suggeststock', $colordat['suggeststock']);
            $this->db->set('pantones', $colordat['pantones']);
            if ($colordat['inventory_color_id'] < 0 ) {
                $this->db->insert('ts_inventory_colors');
                $newid = $this->db->insert_id();
                if ($newid) {
                    $out['result'] = $this->success_result;
                    $colordat['inventory_color_id'] = $newid;
                }
            } else {
                $this->db->update('ts_inventory_colors');
                $out['result'] = $this->success_result;
            }

            // Save Vendors
            foreach ($vendordat as $vendorrow) {
                if ($vendorrow['invcolor_vendor_id'] < 0 ) {
                    $this->db->set('inventory_color_id', $colordat['inventory_color_id']);
                } else {
                    $this->db->where('invcolor_vendor_id', $vendorrow['invcolor_vendor_id']);
                }
                if (empty($vendorrow['vendor_id']) || (floatval($vendorrow['price'])==0)) {
                    $this->db->set('vendor_id', null);
                    $this->db->set('price', 0);
                } else {
                    $this->db->set('vendor_id', $vendorrow['vendor_id']);
                    $this->db->set('price', $vendorrow['price']);
                }
                if ($vendorrow['invcolor_vendor_id'] < 0) {
                    $this->db->insert('ts_invcolor_vendors');
                } else {
                    $this->db->update('ts_invcolor_vendors');
                }
            }
            if (ifset($colordat,'newitemimage',0)==1) {
                // New image
                $path_sh = $this->config->item('pathpreload');
                $path_fl = $this->config->item('upload_path_preload');
                $imgpath_sh = $this->config->item('invpics_relative');
                $imgpah_fl = $this->config->item('invpics');
                $filename = str_replace($path_sh,'', $colordat['color_image']);
                createPath($imgpath_sh);
                $cpres = @copy($path_fl.$filename, $imgpah_fl.$filename);
                if ($cpres) {
                    $this->db->where('inventory_color_id', $colordat['inventory_color_id']);
                    $this->db->set('color_image', $imgpath_sh.$filename);
                    $this->db->set('color_image_source', $colordat['color_image_source']);
                    $this->db->update('ts_inventory_colors');
                }
            }
            usersession($session_id, null);
        }
        return $out;
    }

    private function _check_mastercolor($colordat, $vendordat) {
        $out = ['result' => $this->success_result, 'msg' => 'Unknown Master Item'];
        if (empty($colordat['color'])) {
            $out['result'] = $this->error_result;
            $out['msg'] = 'Empty Color Name';
        }
        return $out;
    }

    public function save_color_manualincome($inventory_color_id, $options) {
        $out = ['result' => $this->success_result, 'msg' => 'Unknown Master Item'];
        $chkflag=1;
        if (empty($options['income_date'])) {
            $chkflag=0;
            $out['msg']='Empty Income Date';
//        } elseif (empty($options['income_recnum'])) {
//            $chkflag=0;
//            $out['msg']='Empty Record #';
        } elseif (empty($options['income_desript'])) {
            $chkflag=0;
            $out['msg']='Empty Income Description';
        } elseif (empty($options['income_price']) || floatval($options['income_price'])==0) {
            $chkflag=0;
            $out['msg']='Empty Income Price';
        } elseif (empty($options['income_qty']) || intval($options['income_qty'])==0) {
            $chkflag=0;
            $out['msg']='Empty Income QTY';
        }
        if ($chkflag==1) {
            // Get new recnum
            $this->db->select('max(inventory_adjust_id) as ordnum, count(inventory_adjust_id) as cnt');
            $this->db->from('ts_inventory_adjusts');
            $numdat=$this->db->get()->row_array();
            if ($numdat['cnt']==0) {
                $newrec = $numdat['cnt'];
            } else {
                $newrec = $numdat['ordnum'];
            }
            $recnum = 'D-'.str_pad($newrec,5,'0',STR_PAD_LEFT);
            $this->db->set('inventory_color_id', $inventory_color_id);
            $this->db->set('income_date', strtotime($options['income_date']));
            $this->db->set('income_record', $recnum); //$options['income_recnum']
            $this->db->set('income_description', $options['income_desript']);
            $this->db->set('income_qty', intval($options['income_qty']));
            $this->db->set('income_price', floatval($options['income_price']));
            $this->db->set('inserted_by', $options['user_id']);
            $this->db->set('inserted_at', date('Y-m-d H:i:s'));
            $this->db->insert('ts_inventory_incomes');
            $newrec = $this->db->insert_id();
            $out['msg'] = 'Error during add Manual Income';
            if ($newrec > 0) {
                $this->db->set('adjust_type', 'S');
                $this->db->insert('ts_inventory_adjusts');
                // Get new data for content
                $data = $this->get_masterinventory_color($inventory_color_id);
                $out['msg'] = $data['msg'];
                if ($data['result']==$this->success_result) {
                    $out['result'] = $this->success_result;
                    $out['itemdata'] = $data['itemdata'];
                    $out['lists'] = $data['lists'];
                    $out['totals'] = $data['totals'];
                    $this->db->where('inventory_color_id', $inventory_color_id);
                    $this->db->set('price', $data['totals']['avg_price']);
                    $this->db->update('ts_inventory_colors');
                }
            }
        }
        return $out;
    }

    public function save_color_manualoutcome($coloritem, $options) {
        $out = ['result' => $this->success_result, 'msg' => 'Unknown Master Item'];
        $chkflag=1;
        if (empty($options['outcome_date'])) {
            $chkflag=0;
            $out['msg']='Empty Outcome Date';
//        } elseif (empty($options['outcome_recnum'])) {
//            $chkflag=0;
//            $out['msg']='Empty Record #';
        } elseif (empty($options['outcome_descript'])) {
            $chkflag=0;
            $out['msg']='Empty Outcome Description';
        } elseif (empty($options['outcome_qty']) || intval($options['outcome_qty'])<=0) {
            $chkflag=0;
            $out['msg']='Empty Income QTY';
        }
        if ($chkflag==1) {
            // Add expense
            $qtyout = intval($options['outcome_qty']);
            $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
            $this->db->from('ts_inventory_incomes');
            $this->db->where('inventory_color_id', $coloritem);
            $this->db->having('leftqty > 0');
            $this->db->order_by('income_date');
            $candidats = $this->db->get()->result_array();
            foreach ($candidats as $candidat) {
                if ($qtyout > $candidat['leftqty']) {
                    $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                } else {
                    $newexp = $candidat['income_expense'] + $qtyout;
                }
                $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                $this->db->set('income_expense', $newexp);
                $this->db->update('ts_inventory_incomes');
                $qtyout= $qtyout - $candidat['leftqty'];
                if ($qtyout <= 0 ) {
                    break;
                }
            }
            // Calc new Rec NUM
            $outcome_type = 'X';
//            $this->db->select('count(inventory_outcome_id) as cnt, max(outcome_number) as outnumb');
//            $this->db->from('ts_inventory_outcomes');
//            $this->db->where('outcome_type', $outcome_type);
//            $outdat = $this->db->get()->row_array();
//            if ($outdat['cnt']==1) {
//                $recnum = -1;
//            } else {
//                $recnum = $outdat['outnumb'];
//            }
//            $newrecnum = $recnum + 1;
//            $recnummask = str_pad($newrecnum, 5,'0', STR_PAD_LEFT);
//            $recnum = $outcome_type.substr($recnummask,0,1).'-'.substr($recnummask,1);

            $this->db->select('max(inventory_adjust_id) as ordnum, count(inventory_adjust_id) as cnt');
            $this->db->from('ts_inventory_adjusts');
            $numdat=$this->db->get()->row_array();
            if ($numdat['cnt']==0) {
                $newrec = $numdat['cnt'];
            } else {
                $newrec = $numdat['ordnum'];
            }
            $recnum = 'D-'.str_pad($newrec,5,'0',STR_PAD_LEFT);

            $this->db->set('inventory_color_id', $coloritem);
            $this->db->set('outcome_date', strtotime($options['outcome_date']));
            $this->db->set('outcome_qty', intval($options['outcome_qty']));
            $this->db->set('outcome_description', $options['outcome_descript']);
            $this->db->set('outcome_record', $recnum);
            $this->db->set('outcome_type', $outcome_type);
            // $this->db->set('outcome_number', $newrecnum);
            $this->db->set('inserted_by', $options['user_id']);
            $this->db->set('inserted_at', date('Y-m-d H:i:s'));
            $this->db->insert('ts_inventory_outcomes');
            // Add Adjusted
            $this->db->set('adjust_type', 'O');
            $this->db->insert('ts_inventory_adjusts');
            // get new itemprice
            $invdata = $this->get_masterinventory_color($coloritem);
            if ($invdata['result']==$this->success_result) {
                $totals = $invdata['totals'];
                $this->db->where('inventory_color_id', $coloritem);
                $this->db->set('price', $totals['avg_price']);
                $this->db->update('ts_inventory_colors');
                // Get new history
                $res = $this->get_masterinventory_colorhistory($coloritem);
                $out['msg'] = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $out['result'] = $this->success_result;
                    $out['lists'] = $res['lists'];
                    $out['itemdata'] = $res['itemdata'];
                }
            }
        }
        return $out;
    }

    public function get_inventory_totals($inventory_type_id) {
        // Lets go
        $this->db->select('sum(suggeststock) as suggeststock, sum(suggeststock*avg_price) as maxtotal');
        $this->db->from('ts_inventory_colors');
        $res=$this->db->get()->row_array();
        $maxval=intval($res['suggeststock']);
        $maxtotal = floatval($res['maxtotal']);

        $income=$this->inventory_income($inventory_type_id);
        $outcome=$this->inventory_outcome($inventory_type_id);
        $reserved=$this->inventory_reserved($inventory_type_id);

        $instock=$income-$outcome;

        $available=$instock-$reserved;

        $stockperc=$this->empty_html_content;
        if ($maxval!=0) {
            $stockperc=round($instock/$maxval*100,0).'%';
        }
        // $availsum
        $out=array(
            'itempercent'=>$stockperc,
            'instock'=> $instock,
            'reserved'=> $reserved,
            'available'=> $available,
            //'total_max'=>'href="/fulfillment/max_total_percent/"',
            'maxsum'=>$maxtotal,
            'max'=> $maxval,

        );
        return $out;
    }

    public function inventory_income($inventory_type_id) {
        $this->db->select('sum(i.income_qty) as qty_in');
        $this->db->from('ts_inventory_incomes i');
        $this->db->join('ts_inventory_colors c','i.inventory_color_id=c.inventory_color_id');
        $this->db->join('ts_inventory_items itm','itm.inventory_item_id=c.inventory_item_id');
        $this->db->where('itm.inventory_type_id', $inventory_type_id);
        $res = $this->db->get()->row_array();
        return intval($res['qty_in']);
    }

    public function inventory_outcome($inventory_type_id) {
        $this->db->select('sum(o.outcome_qty) as qty_out');
        $this->db->from('ts_inventory_outcomes o');
        $this->db->join('ts_inventory_colors c','c.inventory_color_id=o.inventory_color_id');
        $this->db->join('ts_inventory_items itm','itm.inventory_item_id=c.inventory_item_id');
        $this->db->where('itm.inventory_type_id', $inventory_type_id);
        $res = $this->db->get()->row_array();
        return intval($res['qty_out']);
    }

    public function inventory_reserved($inventory_type_id) {
        return 0;
//        $this->db->select('sum(i.item_qty) as reserved, count(i.order_itemcolor_id) as cnt');
//        $this->db->from('ts_order_itemcolors i');
//        $this->db->join('ts_order_items im','im.order_item_id=i.order_item_id');
//        $this->db->join('ts_orders o','o.order_id=im.order_id');
//        $this->db->join('ts_printshop_colors c','c.printshop_item_id=i.printshop_item_id');
//        $this->db->where('o.order_cog', null);
//
//        $reserv=$this->db->get()->row_array();
//        $reserved = 0;
//        if ($reserv['cnt']>0) {
//            $reserved = intval($reserv['reserved']);
//        }
//        return $reserved;
    }

    public function get_data_onboat($inventory_type_id, $onboat_type = 'C') {
        $this->db->select('b.onboat_container, b.onboat_status, b.onboat_type, max(b.onboat_date) as onboat_date, sum(b.onroutestock) as onboat_total');
        $this->db->select('max(b.freight_price) as freight_price');
        $this->db->from('ts_inventory_onboats b');
        $this->db->join('ts_inventory_colors c','b.inventory_color_id=c.inventory_color_id');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('i.inventory_type_id', $inventory_type_id);
        $this->db->where('b.onboat_type', $onboat_type);
        $this->db->group_by('b.onboat_container, b.onboat_status, b.onboat_type');
        $res = $this->db->get()->result_array();
        $out = [];
        foreach ($res as $item) {
            $title = '';
            if (floatval($item['freight_price'])!=0 && intval($item['onboat_total']) > 0 ) {
                $price = round(floatval($item['freight_price']) / intval($item['onboat_total']),3);
                $title = ($price > 0 ? '+' : '-').MoneyOutput(abs($price),3).' ea';
            }
            $item['title'] = $title;
            $out[]=$item;
        }
        return $out;
    }

    public function get_onboatdetails($onboat_container, $colors, $onboat_type='C', $edit=0) {
        $this->db->select('o.inventory_onboat_id, o.onroutestock, o.vendor_price, c.inventory_color_id, i.inventory_item_id');
        $this->db->from('ts_inventory_onboats o');
        $this->db->join('ts_inventory_colors c','o.inventory_color_id = c.inventory_color_id');
        $this->db->join('ts_inventory_items i','c.inventory_item_id = i.inventory_item_id');
        $this->db->where('o.onboat_container', $onboat_container);
        $this->db->where('o.onboat_type', $onboat_type);
        $this->db->order_by('i.inventory_item_id');
        $details = $this->db->get()->result_array();
        $items = [];
        if (count($details) >0) {
            $curitem = 0;
            $itemid = 0;
            foreach ($details as $detail) {
                if ($detail['inventory_item_id']!==$itemid) {
                    if ($itemid!==0) {
                        $items[] = [
                            'item_id' => $itemid,
                            'qty' => $curitem,
                        ];
                    }
                    $itemid = $detail['inventory_item_id'];
                    $curitem = 0;
                }
                $curitem+=$detail['onroutestock'];
            }
            if ($curitem > 0) {
                $items[] = [
                    'item_id' => $itemid,
                    'qty' => $curitem,
                ];
            }
        }
        $container=[];
        $onboardidx = -1;
        foreach ($colors as $color) {
            $rowqty = 0;
            $inventory_onboat_id = 0;
            $vprice = 0;
            if ($color['color_id']==0) {
                foreach ($items as $item) {
                    if ($item['item_id']==$color['item_id']) {
                        $rowqty = $item['qty'];
                        break;
                    }
                }
            } else {
                foreach ($details as $detail) {
                    if ($detail['inventory_color_id']==$color['color_id']) {
                        $rowqty = $detail['onroutestock'];
                        $inventory_onboat_id = $detail['inventory_onboat_id'];
                        $vprice = $detail['vendor_price'];
                        break;
                    }
                }
            }
            if ($edit==1) {
                if ($color['color_id']!=0 && $inventory_onboat_id==0) {
                    $inventory_onboat_id = $onboardidx;
                    $onboardidx = $onboardidx - 1;
                }
                $container[] = [
                    'inventory_onboat_id' => $inventory_onboat_id,
                    'inventory_item_id' => $color['item_id'],
                    'inventory_color_id' => $color['color_id'],
                    'onroutestock' => $rowqty,
                    'vendor_price' => $vprice==0 ? ifset($color, 'vendor_price',0) : $vprice,
                ];
            } else {
                $container[] = [
                    'inventory_item_id' => $color['item_id'],
                    'inventory_color_id' => $color['color_id'],
                    'onroutestock' => $rowqty,
                    // 'vendor_price' => ifset($color, 'vendor_price',0),
                    'vendor_price' => $vprice==0 ? ifset($color, 'vendor_price',0) : $vprice,
                ];
            }
        }
        return $container;
    }

    public function new_onboatcontainer($colors, $onboat_type) {
        $container=[];
        $onboardidx = -1;
        foreach ($colors as $color) {
            $rowqty = 0;
            $inventory_onboat_id = 0;
            $vprice = 0;
                if ($color['color_id']!=0 && $inventory_onboat_id==0) {
                    $inventory_onboat_id = $onboardidx;
                    $onboardidx = $onboardidx - 1;
                }
                $container[] = [
                    'inventory_onboat_id' => $inventory_onboat_id,
                    'inventory_item_id' => $color['item_id'],
                    'inventory_color_id' => $color['color_id'],
                    'onroutestock' => $rowqty,
                    'vendor_price' => $vprice==0 ? ifset($color, 'vendor_price',0) : $vprice,
                ];
        }
        return $container;
    }
    public function get_inventory_colors($inventory_type, $inventory_filter) {
        $this->db->select('*');
        $this->db->from('ts_inventory_items');
        $this->db->where('inventory_type_id', $inventory_type);
        $this->db->order_by('item_order');
        $items=$this->db->get()->result_array();
        $colorsdata = [];
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
            if ($additem) {
                $colorsdata[] = [
                    'item_id' => $item['inventory_item_id'],
                    'color_id' => 0,
                    'vendor_price' => '',
                ];
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
                foreach ($colors as $color) {
                    $colorsdata[] = [
                        'item_id' => $item['inventory_item_id'],
                        'color_id' => $color['inventory_color_id'],
                        'vendor_price' => $color['price'],
                    ];
                }
            }
        }
        return $colorsdata;
    }

    public function get_onboattotals($onboat_container, $onboat_type='C') {
        $out=['result' => $this->error_result,'msg' => 'Container Not Found'];

        $this->db->select('count(inventory_onboat_id) as cnt, sum(onroutestock) as total, max(onboat_date) as onboat_date, max(freight_price) as freight_price');
        $this->db->from('ts_inventory_onboats');
        $this->db->where('onboat_container', $onboat_container);
        $this->db->where('onboat_type', $onboat_type);
        $res = $this->db->get()->row_array();
        if ($res['cnt'] > 0) {
            $out['result'] = $this->success_result;
            $out['data'] = [
                'total' => intval($res['total']),
                'onboat_date' => $res['onboat_date'],
                'freight_price' => floatval($res['freight_price']),
            ];
        }
        return $out;
    }

    public function changecontainer_param($sessiondata, $color_id, $item_id, $entity, $newval, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Container content not found'];
        $container = $sessiondata['container'];
        $idx = 0;
        $find = 0;
        foreach ($container as $row) {
            if ($row['inventory_item_id']==2) {
                $tt = 1;
            }
            if ($row['inventory_item_id']==$item_id && $row['inventory_color_id']==$color_id) {
                $find = 1;
                break;
            }
            $idx++;
        }
        if ($find==1) {
            $out['result'] = $this->success_result;
            $oldval = ($entity=='qty' ? $container[$idx]['onroutestock'] : $container[$idx]['vendor_price']);
            if ($entity=='qty') {
                $diff = intval($newval) - $oldval;
                $container[$idx]['onroutestock'] = intval($newval);
                $itmidx = 0;
                foreach ($container as $item) {
                    if ($item['inventory_item_id']==$item_id && $item['inventory_color_id']==0) {
                        $newval = intval($item['onroutestock']) + $diff;
                        $out['itemtval'] = $newval;
                        $container[$itmidx]['onroutestock'] = $newval;
                        $total = intval($sessiondata['total']) + $diff;
                        $sessiondata['total'] = $total;
                        $out['total'] = $total;
                    }
                    $itmidx++;
                }
            } else {
                $container[$idx]['vendor_price'] = floatval($newval);
            }
            $sessiondata['container'] = $container;
            usersession($session_id, $sessiondata);
        }
        return $out;
    }

    public function changecontainer_header($sessiondata, $entity, $newval, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Container parameter not found'];
        if (array_key_exists($entity, $sessiondata)) {
            if ($entity=='onboat_date') {
                if (empty($newval)) {
                    $sessiondata[$entity] = 0;
                } else {
                    $sessiondata[$entity] = strtotime($newval);
                }
            } elseif ($entity=='freight_price') {
                $sessiondata[$entity] = floatval($newval);
            }
            usersession($session_id, $sessiondata);
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function inventory_container_save($onboat_container, $onboat_type, $container, $onboat_date, $freight_price) {
        $out = ['result' => $this->error_result, 'msg' => ''];
        if (!empty($onboat_date)) {
            if ($onboat_container<=0) {
                $this->db->select('max(onboat_container) as ncontainer, count(*) cnt');
                $this->db->from('ts_inventory_onboats');
                $chkres = $this->db->get()->row_array();
                if ($chkres['cnt']==0) {
                    $onboat_container = 1;
                } else {
                    $onboat_container = intval($chkres['ncontainer']) + 1;
                }
            }
            foreach ($container as $row) {
                if ($row['inventory_color_id']>0) {
                    // Color row
                    if ($row['inventory_onboat_id'] > 0) {
                        if (intval($row['onroutestock'])==0) {
                            $this->db->where('inventory_onboat_id', $row['inventory_onboat_id']);
                            $this->db->delete('ts_inventory_onboats');
                        } else {
                            $this->db->where('inventory_onboat_id', $row['inventory_onboat_id']);
                            $this->db->set('onroutestock', intval($row['onroutestock']));
                            $this->db->set('vendor_price', floatval($row['vendor_price']));
                            $this->db->set('onboat_date', $onboat_date);
                            $this->db->set('freight_price', floatval($freight_price));
                            $this->db->update('ts_inventory_onboats');
                        }
                    } else {
                        if (intval($row['onroutestock']) > 0) {
                            $this->db->set('inventory_color_id', $row['inventory_color_id']);
                            $this->db->set('onboat_container', $onboat_container);
                            $this->db->set('onroutestock', intval($row['onroutestock']));
                            $this->db->set('vendor_price', floatval($row['vendor_price']));
                            $this->db->set('onboat_date', $onboat_date);
                            $this->db->set('freight_price', floatval($freight_price));
                            $this->db->set('onboat_type', $onboat_type);
                            $this->db->insert('ts_inventory_onboats');
                        }
                    }
                }
            }
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function onboat_arrived($onboat_container, $onboat_type, $totals, $user_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Container Arrived. Reload page'];
        // Update status
        $this->db->select('count(*) as cnt');
        $this->db->from('ts_inventory_onboats');
        $this->db->where('onboat_container', $onboat_container);
        $this->db->where('onboat_type', $onboat_type);
        $this->db->where('onboat_status',0);
        $chkres = $this->db->get()->row_array();
        if ($chkres['cnt'] > 0) {
            $out['result'] = $this->success_result;
            $this->db->where('onboat_container', $onboat_container);
            $this->db->where('onboat_type', $onboat_type);
            $this->db->set('onboat_status',1);
            $this->db->update('ts_inventory_onboats');
            // Code, description
            $income_code = ($onboat_type=='C' ? 'CON-' : 'EXP-').str_pad($onboat_container, 3,'0', STR_PAD_LEFT);
            $income_descr = 'Purchased - '.($onboat_type=='C' ? 'Container ' : 'Express ').str_pad($onboat_container, 3,'0', STR_PAD_LEFT);
            $this->db->select('*');
            $this->db->from('ts_inventory_onboats');
            $this->db->where('onboat_container', $onboat_container);
            $this->db->where('onboat_type', $onboat_type);
            $details = $this->db->get()->result_array();
            $addprice = round($totals['freight_price']/$totals['total'],3);
            foreach ($details as $detail) {
                // New AVG Price
                $income = $this->inventory_color_income($detail['inventory_color_id']);
                $outcome = $this->inventory_color_outcome($detail['inventory_color_id']);
                $balance = $income - $outcome;
                $this->db->select('*');
                $this->db->from('ts_inventory_colors');
                $this->db->where('inventory_color_id', $detail['inventory_color_id']);
                $colordata = $this->db->get()->row_array();
                if ($balance+$detail['onroutestock']==0) {
                    $avg_price = $detail['vendor_price'];
                } else {
                    $avg_price = ($balance*$colordata['avg_price']+($detail['onroutestock']*($detail['vendor_price']+$addprice)))/($balance+$detail['onroutestock']);
                }
                $this->db->where('inventory_color_id', $detail['inventory_color_id']);
                $this->db->set('avg_price', round($avg_price,3));
                $this->db->update('ts_inventory_colors');
                // Add income
                $this->db->set('inventory_color_id', $detail['inventory_color_id']);
                $this->db->set('income_date', $detail['onboat_date']);
                $this->db->set('income_qty', $detail['onroutestock']);
                $this->db->set('income_price', $detail['vendor_price']+$addprice);
                $this->db->set('income_description', $income_descr);
                $this->db->set('income_record', $income_code);
                $this->db->set('inserted_by', $user_id);
                $this->db->set('inserted_at', date('Y-m-d H:i:s'));
                $this->db->set('updated_by', $user_id);
                $this->db->insert('ts_inventory_incomes');
            }
        }
        return $out;
    }

    public function onboat_download($onboat_container, $onboat_type) {
        $out=array('result'=>$this->error_result,'msg'=>'No Data Exist');
        $this->db->select('i.item_num, i.item_name, c.color, b.vendor_price as price, b.onroutestock as qty, b.onboat_date, b.freight_price');
        $this->db->from('ts_inventory_onboats b');
        $this->db->join('ts_inventory_colors c','c.inventory_color_id=b.inventory_color_id');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('b.onboat_container', $onboat_container);
        $this->db->where('b.onboat_type', $onboat_type);
        // $this->db->group_by('i.item_num, i.item_name, c.color, c.price');
        $this->db->order_by('i.item_num, c.color');
        $res=$this->db->get()->result_array();
        if (count($res)>0) {
            $onboat_date=$res[0]['onboat_date'];
            if ($onboat_type=='C') {
                $title='Container '.$onboat_container.' - Arriving in USA '.date('m/d/y', $onboat_date);
            } else {
                $title='Express '.$onboat_container.' - Arriving in USA '.date('m/d/y', $onboat_date);
            }
            $addprice = $res[0]['freight_price'];
            if ($addprice!=0) {
                $title.=' Freight price '.MoneyOutput($addprice);
            }

            $options = [
                'res' => $res,
                'title' => $title,
            ];
            $this->load->model('exportexcell_model');
            $filename = $this->exportexcell_model->export_onboatcontent($options);
            $url=$this->config->item('pathpreload').$filename;
            $out['url']=$url;
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function inventorytype_addcost($inventory_type, $addcost) {
        $out=['result' => $this->error_result, 'msg' => 'Empty Inventory Type'];
        if ($inventory_type > 0) {
            $this->db->select('count(*) as cnt');
            $this->db->from('ts_inventory_types');
            $this->db->where('inventory_type_id', $inventory_type);
            $chkres = $this->db->get()->row_array();
            if ($chkres['cnt']==1) {
                $this->db->where('inventory_type_id', $inventory_type);
                $this->db->set('type_addcost', floatval($addcost));
                $this->db->update('ts_inventory_types');
                $out['result'] = $this->success_result;
                $out['addcost'] = floatval($addcost);
            }
        }
        return $out;
    }
}