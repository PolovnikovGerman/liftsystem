<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventory_model extends MY_Model
{

    private $outstockclass = 'severevalstock';
    private $outstoklabel = 'No Stock'; // 'Out of Stock';
    private $emptystockclass = 'emptyvalstock';
    private $lowstockclass = 'lowinstock';
    private $donotreorder = 'Do Not Reorder';
    // private $bt_label = 'Bluetrack Legacy';
    private $bt_label = 'Bluetrack/Stressballs';
    // private $sb_label = 'StressBalls.com';
    private $sr_label = 'StressRelievers';
    private $empty_html_content='&nbsp;';
    private $error_message='Unknown error. Try later';
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
                    if ($stock['brand']=='SR') {
                        $descrip.=$this->sr_label;
                    // } elseif ($stock['brand']=='SB') {
                    //     $descrip.=$this->sb_label;
                    } else {
                        $descrip.=$this->bt_label;
                    }
                    $stock['instock_record'] = ($stock['brand']=='SR' ? 'SR' : 'BT').$stock['instock_record'];
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
            $this->db->select('max(item_order) as maxnum, count(inventory_item_id) as cnt');
            $this->db->from('ts_inventory_items');
            $this->db->where('inventory_type_id', $invtype);
            $dat = $this->db->get()->row_array();
            if ($dat['cnt']==0) {
                $newnum = 1;
            } else {
                $newnum = intval($dat['maxnum'])+1;
            }
            $item_data = [
                'inventory_item_id' => -1,
                'inventory_type_id' => $invtype,
                'item_num' => $invdata['type_short'].str_pad($newnum,3,'0',STR_PAD_LEFT),
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
                $this->db->select('count(inventory_item_id) as cnt, max(item_order) as maxord');
                $this->db->from('ts_inventory_items');
                $this->db->where('inventory_type_id', $itemdata['inventory_type_id']);
                $dat = $this->db->get()->row_array();
                if ($dat['cnt']==0) {
                    $neword = 1;
                } else {
                    $neword = intval($dat['maxord'])+1;
                }
                $this->db->set('inventory_type_id', $itemdata['inventory_type_id']);
                $this->db->set('item_num', $invdata['type_short'].str_pad($neword,3,'0',STR_PAD_LEFT));
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
            // $this->db->select('max(inventory_adjust_id) as ordnum, count(inventory_adjust_id) as cnt');
            // $this->db->from('ts_inventory_adjusts');
            $this->db->set('adjust_type', 'S');
            $this->db->insert('ts_inventory_adjusts');
            $newrec = $this->db->insert_id();
//            $numdat=$this->db->get()->row_array();
//            if ($numdat['cnt']==0) {
//                $newrec = $numdat['cnt'];
//            } else {
//                $newrec = $numdat['ordnum'];
//            }
            $recnum = 'AJ'.str_pad($newrec,5,'0',STR_PAD_LEFT);
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
                // Get new data for content
                $data = $this->get_masterinventory_color($inventory_color_id);
                $out['msg'] = $data['msg'];
                if ($data['result']==$this->success_result) {
                    $out['result'] = $this->success_result;
                    $out['itemdata'] = $data['itemdata'];
                    $out['lists'] = $data['lists'];
                    $out['totals'] = $data['totals'];
                    $this->db->where('inventory_color_id', $inventory_color_id);
                    $this->db->set('avg_price', $data['totals']['avg_price']);
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

//            $this->db->select('max(inventory_adjust_id) as ordnum, count(inventory_adjust_id) as cnt');
//            $this->db->from('ts_inventory_adjusts');
//            $numdat=$this->db->get()->row_array();
//            if ($numdat['cnt']==0) {
//                $newrec = $numdat['cnt'];
//            } else {
//                $newrec = $numdat['ordnum'];
//            }
            // Add Adjusted
            $this->db->set('adjust_type', 'O');
            $this->db->insert('ts_inventory_adjusts');
            $newrec = $this->db->insert_id();
            $recnum = 'AJ'.str_pad($newrec,5,'0',STR_PAD_LEFT);

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
            // get new itemprice
            $invdata = $this->get_masterinventory_color($coloritem);
            if ($invdata['result']==$this->success_result) {
                $totals = $invdata['totals'];
                $this->db->where('inventory_color_id', $coloritem);
                $this->db->set('avg_price', $totals['avg_price']);
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

    public function get_inventory_totals($inventory_type_id, $itemstatus = 0) {
        // Lets go
        $this->db->select('sum(suggeststock) as suggeststock, sum(suggeststock*avg_price) as maxtotal');
        $this->db->from('ts_inventory_colors c');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('i.inventory_type_id', $inventory_type_id);
        if ($itemstatus!==0) {
            if ($itemstatus==1) {
                $this->db->where('i.item_status', 1);
            } else {
                $this->db->where('i.item_status', 0);
            }
        }

        $res=$this->db->get()->row_array();
        $maxval=intval($res['suggeststock']);
        $maxtotal = floatval($res['maxtotal']);

        $income=$this->inventory_income($inventory_type_id, $itemstatus);
        $outcome=$this->inventory_outcome($inventory_type_id, $itemstatus);
        $reserved=$this->inventory_reserved($inventory_type_id, $itemstatus);

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

    public function inventory_income($inventory_type_id, $itemstatus=0) {
        $this->db->select('sum(i.income_qty) as qty_in');
        $this->db->from('ts_inventory_incomes i');
        $this->db->join('ts_inventory_colors c','i.inventory_color_id=c.inventory_color_id');
        $this->db->join('ts_inventory_items itm','itm.inventory_item_id=c.inventory_item_id');
        $this->db->where('itm.inventory_type_id', $inventory_type_id);
        if ($itemstatus!==0) {
            if ($itemstatus==1) {
                $this->db->where('itm.item_status', 1);
            } else {
                $this->db->where('itm.item_status', 0);
            }
        }
        $res = $this->db->get()->row_array();
        return intval($res['qty_in']);
    }

    public function inventory_outcome($inventory_type_id, $itemstatus=0) {
        $this->db->select('sum(o.outcome_qty) as qty_out');
        $this->db->from('ts_inventory_outcomes o');
        $this->db->join('ts_inventory_colors c','c.inventory_color_id=o.inventory_color_id');
        $this->db->join('ts_inventory_items itm','itm.inventory_item_id=c.inventory_item_id');
        $this->db->where('itm.inventory_type_id', $inventory_type_id);
        if ($itemstatus!==0) {
            if ($itemstatus==1) {
                $this->db->where('itm.item_status', 1);
            } else {
                $this->db->where('itm.item_status', 0);
            }
        }
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

    public function get_data_onboat($inventory_type_id, $onboat_type = 'C', $itemstatus = 0 ) {
        $this->db->select('b.onboat_container, b.onboat_status, b.onboat_type, max(b.onboat_date) as onboat_date, sum(b.onroutestock) as onboat_total');
        $this->db->select('max(b.freight_price) as freight_price, max(t.type_short) as type_short');
        $this->db->from('ts_inventory_onboats b');
        $this->db->join('ts_inventory_colors c','b.inventory_color_id=c.inventory_color_id');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->join('ts_inventory_types t', 't.inventory_type_id=i.inventory_type_id');
        $this->db->where('i.inventory_type_id', $inventory_type_id);
        $this->db->where('b.onboat_type', $onboat_type);
        if ($itemstatus!=0) {
            if ($itemstatus==1) {
                $this->db->where('i.item_status', 1);
            } else {
                $this->db->where('i.item_status', 0);
            }
        }
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

    public function get_onboatdetails($onboat_container, $colors, $onboat_type='C', $itemstatus=0, $edit=0) {
        $this->db->select('o.inventory_onboat_id, o.onroutestock, o.vendor_price, c.inventory_color_id, i.inventory_item_id');
        $this->db->from('ts_inventory_onboats o');
        $this->db->join('ts_inventory_colors c','o.inventory_color_id = c.inventory_color_id');
        $this->db->join('ts_inventory_items i','c.inventory_item_id = i.inventory_item_id');
        $this->db->where('o.onboat_container', $onboat_container);
        $this->db->where('o.onboat_type', $onboat_type);
        if ($itemstatus!=0) {
            if ($itemstatus==1) {
                $this->db->where('i.item_status', 1);
            } else {
                $this->db->where('i.item_status', 0);
            }
        }
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
                $this->db->where('onboat_type', $onboat_type);
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

    public function get_orderreport_counts($options=array()) {
        $this->db->select('count(oa.amount_id) as cnt');
        $this->db->from('ts_order_amounts oa');
        $this->db->join('ts_orders o','o.order_id=oa.order_id');
        $this->db->where('oa.printshop',1);
        // Additional Options
        if (isset($options['search'])) {
            $this->db->join('ts_inventory_colors c','c.inventory_color_id=oa.inventory_color_id');
            $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
            $this->db->like('upper(concat(o.order_num, o.customer_name, i.item_num, i.item_name))', $options['search']);
        }
        if (isset($options['report_year'])) {
            $start=strtotime($options['report_year'].'-01-01');
            $year_finish=intval($options['report_year']+1);
            $finish=strtotime($year_finish.'-01-01');
            $this->db->where('oa.printshop_date >= ', $start);
            $this->db->where('oa.printshop_date < ', $finish);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('o.brand', $options['brand']);
            } else {
                $this->db->where_in('o.brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_orderreport_totals($options=array()) {
        $this->db->select('sum(oa.shipped) as shipped, sum(oa.kepted) as kepted, sum(oa.misprint) as misprint');
        $this->db->select('sum(oa.shipped+oa.kepted+oa.misprint)as totalqty');
        $this->db->select('sum(oa.orangeplate+oa.blueplate+oa.beigeplate) as totalplate');
        $this->db->select('sum((oa.shipped+oa.kepted+oa.misprint)*oa.extracost) as total_extra');
        $this->db->select('sum((oa.shipped+oa.kepted+oa.misprint)*(oa.price+oa.extracost)) as item_cost, sum(oa.orangeplate) as oranplate');
        $this->db->select('sum(oa.blueplate) as blueplate, sum(oa.beigeplate) as beigeplate, sum(oa.misprint*(oa.price+oa.extracost)) as misprint_cost');
        $this->db->select('sum(oa.orangeplate*oa.orangeplate_price) as orangeplatecost');
        $this->db->select('sum(oa.blueplate*oa.blueplate_price) as blueplatecost');
        $this->db->select('sum(oa.beigeplate*oa.beigeplate_price) as beigeplatecost');
        $this->db->select('sum(oa.printshop_total) as total_cost');
        $this->db->from('ts_order_amounts oa');
        $this->db->join('ts_orders o','o.order_id=oa.order_id');
        $this->db->where('printshop',1);
        // Options
        if (isset($options['search'])) {
            $this->db->join('ts_inventory_colors c','c.inventory_color_id=oa.inventory_color_id');
            $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
            $this->db->like('upper(concat(o.order_num, o.customer_name, i.item_num, i.item_name))', $options['search']);
        }
        if (isset($options['report_year'])) {
            $start=strtotime($options['report_year'].'-01-01');
            $year_finish=intval($options['report_year']+1);
            $finish=strtotime($year_finish.'-01-01');
            $this->db->where('oa.printshop_date >= ', $start);
            $this->db->where('oa.printshop_date < ', $finish);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            // $this->db->where('o.brand', $options['brand']);
            if ($options['brand']=='SR') {
                $this->db->where('o.brand', $options['brand']);
            } else {
                $this->db->where_in('o.brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        $res['misprintperc']='0%';
        if ($res['shipped']>0) {
            $res['misprintperc']=round($res['misprint']/$res['shipped']*100,0).'%';
        }
        $res['platecost']=$res['orangeplatecost']+$res['blueplatecost'];
        return $res;
    }

    public function _get_plates_costs() {
        $this->db->select('orangeplate_price, blueplate_price, repaid_cost, inv_addcost, beigeplate_price');
        $this->db->from('ts_configs');
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function get_report_years($options=[]) {
        $this->db->select("date_format(from_unixtime(oa.amount_date),'%Y') as year_amount, count(oa.amount_id) as cnt",FALSE);
        $this->db->from('ts_order_amounts oa');
        $this->db->where('oa.printshop',1);
        $this->db->group_by('year_amount');
        $this->db->order_by('year_amount','desc');
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            if ($options['brand']=='SR') {
                $this->db->where('o.brand', $options['brand']);
            } else {
                $this->db->where_in('o.brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->result_array();
        // Additional Options
        return $res;
    }

    public function get_orderreport_data($options) {
        // Get Cost - Blue and Orange plates
        $this->db->select('amount_id, count(inventory_income_id) as cnt');
        $this->db->from('ts_order_inventory');
        $this->db->group_by('amount_id');
        $incomesql = $this->db->get_compiled_select();

        // $this->db->select('oa.*');
        $this->db->select('oa.amount_id, oa.shipped, oa.kepted,oa.misprint, oa.price, oa.extracost, oa.printshop_date');
        $this->db->select('oa.orangeplate, oa.beigeplate, oa.blueplate, , oa.printshop_type, oa.order_id');
        $this->db->select('c.color, i.item_name, i.item_num, o.customer_name, o.order_num, o.profit, o.profit_perc');
        $this->db->select('(oa.price+oa.extracost) as priceea');
        $this->db->select('(oa.extracost)*(oa.shipped+oa.kepted+oa.misprint) as extraitem');
        $this->db->select('(oa.price+oa.extracost)*(oa.shipped+oa.kepted+oa.misprint) as costitem');
        $this->db->select('(oa.shipped+oa.kepted+oa.misprint) as totalitem');
        $this->db->select('(oa.orangeplate+oa.blueplate+oa.beigeplate) as totalplates');
        $this->db->select('(oa.orangeplate*oa.orangeplate_price+oa.blueplate*oa.blueplate_price+oa.beigeplate*oa.beigeplate_price) as platescost');
        $this->db->select('oa.printshop_total as totalitemcost');
        $this->db->select('(oa.price+oa.extracost)*oa.misprint as misprintcost');
        $this->db->select('date_format(from_unixtime(oa.printshop_date),\'%Y%m%d\') as sortdatefld',FALSE);
        $this->db->select('invincom.cnt as countincome');
        $this->db->from('ts_order_amounts oa');
        $this->db->join('ts_inventory_colors c', 'c.inventory_color_id=oa.inventory_color_id');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->join('ts_orders o','o.order_id=oa.order_id');
        $this->db->join('('.$incomesql.') invincom','invincom.amount_id=oa.amount_id','left');
        $this->db->where('oa.printshop',1);
        if (isset($options['search'])) {
            $this->db->like('upper(concat(o.order_num, o.customer_name, i.item_num, i.item_name))', $options['search']);
        }
        if (isset($options['report_year'])) {
            $start=strtotime($options['report_year'].'-01-01');
            $year_finish=intval($options['report_year']+1);
            $finish=strtotime($year_finish.'-01-01');
            $this->db->where('oa.printshop_date >= ', $start);
            $this->db->where('oa.printshop_date < ', $finish);
        }
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('o.brand', $options['brand']);
            } else {
                $this->db->where_in('o.brand', ['BT','SB']);
            }
        }
        $this->db->order_by("sortdatefld desc, oa.update_date desc");

        $res=$this->db->get()->result_array();
        if (isset($options['export']) && $options['export']==1) {
            return $res;
        }

        // Calc start index
        $startidx=$options['totals']-$options['offset'];
        $data=array();
        foreach ($res as $row) {
            $misprint_proc=($row['shipped']==0 ? 0 : $row['misprint']/$row['shipped']*100);
            $details = '';
            if ($row['countincome'] > 1) {
                $details = 'data-event="hover" data-css="inventincome" data-bgcolor="#FFFFFF" data-bordercolor="#adadad" data-textcolor="#000000"
     data-position="right" data-balloon="{ajax} /fulfillment/inventoryoutdetails/'.$row['amount_id'].'"';
            }
            $data[]=array(
                'printshop_income_id'=>$row['amount_id'],
                'numpp'=>$startidx,
                'order_date'=>date('j-M', $row['printshop_date']),
                'order_num'=>$row['order_num'],
                'customer'=>$row['customer_name'],
                'item_name'=>$row['item_num'].' '.str_replace('Stress Balls', '', $row['item_name']),
                'color'=>$row['color'],
                'shipped'=>$row['shipped'],
                'kepted'=>$row['kepted'],
                'misprint'=>$row['misprint'],
                'misprint_proc'=>round($misprint_proc,0).'%',
                'total_qty'=>$row['totalitem'],
                'price'=>$row['price'],
                'extracost'=>$row['extracost'],
                'totalea'=>round($row['priceea'],3),
                'extraitem'=>round($row['extraitem'],2),
                'costitem'=>round($row['costitem'],2),
                'oranplate'=>$row['orangeplate'],
                'blueplate'=>$row['blueplate'],
                'beigeplate' => $row['beigeplate'],
                'totalplates'=>$row['totalplates'],
                'platescost'=>$row['platescost'],
                'itemstotalcost'=>$row['totalitemcost'],
                'misprintcost'=>$row['misprintcost'],
                'orderclass'=>($row['printshop_type']=='M' ? 'manualinput' : 'systeminput'),
                'order_id'=>$row['order_id'],
                'countincome' => $row['countincome'],
                'details' => $details,
            );
            $startidx--;
        }
        return $data;
    }

    public function get_printshop_order($printshop_income_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'PO Order not Found');
        $title = '';
        if ($printshop_income_id==0) {
            $res=$this->_newprintshop_order();
        } else {
            $this->db->select('oa.*, oa.amount_id as printshop_income_id, c.inventory_item_id, o.customer_name as customer, o.order_num, i.inventory_type_id');
            $this->db->from('ts_order_amounts oa');
            $this->db->join('ts_inventory_colors c', 'c.inventory_color_id=oa.inventory_color_id');
            $this->db->join('ts_inventory_item i','i.inventory_item_id=c.inventory_item_id');
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            $this->db->where('oa.amount_id', $printshop_income_id);
            $res=$this->db->get()->row_array();
            if (!isset($res['amount_id'])) {
                $out['msg']='Printshop Order Not Found';
                return $out;
            }
            $res['printshop_oldqty'] = intval($res['shipped'])+intval($res['kepted'])+intval($res['misprint']);
            $res['newprintshop'] = 0;
            $res['color_old'] = $res['inventory_color_id'];
            $res['type_old'] = $res['inventory_type_id'];
            // Get balance
            $income = $this->inventory_color_income($res['inventory_color_id']);
            $outcome = $this->inventory_color_outcome($res['inventory_color_id']);
            // $reserved = $this->inventory_color_reserved($color['inventory_color_id']);
            $instock=$income-$outcome;
            $title = 'Available '.QTYOutput($instock);
        }
        $data=$this->_prinshoporder_params($res);
        $out['result']=$this->success_result;
        $out['data']=$data;
        $out['title'] = $title;
        return $out;
    }

    public function inventory_balance($inventory_color_id) {
        $income = $this->inventory_color_income($inventory_color_id);
        $outcome = $this->inventory_color_outcome($inventory_color_id);
        // $reserved = $this->inventory_color_reserved($color['inventory_color_id']);
        return $income-$outcome;
    }

    private function _newprintshop_order() {
        $platesprice=$this->_get_plates_costs();
        $blueplate_price=$platesprice['blueplate_price'];
        $orangeplate_price=$platesprice['orangeplate_price'];
        $beigeplate_price = $platesprice['beigeplate_price'];
        $data=array(
            'printshop_income_id'=>0,
            'printshop_date'=>time(),
            'inventory_color_id'=>'',
            'inventory_item_id'=>'',
            'shipped'=>0,
            'kepted'=>0,
            'misprint'=>0,
            'price'=>0,
            'extracost'=>0,
            'orangeplate'=>0,
            'blueplate'=>0,
            'beigeplate' => 0,
            'extraitem'=>0,
            'orangeplate_price'=>$orangeplate_price,
            'blueplate_price'=>$blueplate_price,
            'beigeplate_price' => $beigeplate_price,
            'printshop_type'=>'M',
            'order_id'=>0,
            'order_num'=>'',
            'customer'=>'',
            'printshop_history'=>0,
            'printshop_oldqty' => 0,
            'newprintshop' => 1,
            'color_old' => 0,
            'type_old' => 0,
        );
        return $data;
    }

    public function _prinshoporder_params($order) {
        $totalea=round($order['price']+$order['extracost'],3);
        $costitem=$totalea*($order['shipped']+$order['kepted']+$order['misprint']);
        $misprint_proc=($order['shipped']==0 ? 0 : $order['misprint']/$order['shipped']*100);
        $misprintcost=$order['misprint']*$totalea;
        $totalplates=$order['orangeplate']+$order['blueplate']+$order['beigeplate'];;
        $platescost=$order['orangeplate']*$order['orangeplate_price']+$order['blueplate']*$order['blueplate_price']+$order['beigeplate']*$order['beigeplate_price'];
        $totalitemcost=$platescost+$costitem;
        $data=array(
            'printshop_income_id'=>$order['printshop_income_id'],
            'printshop_date'=>$order['printshop_date'],
            'order_num'=>$order['order_num'],
            'customer'=>$order['customer'],
            'inventory_item_id'=>$order['inventory_item_id'],
            'inventory_color_id'=>$order['inventory_color_id'],
            'shipped'=>$order['shipped'],
            'kepted'=>$order['kepted'],
            'misprint'=>$order['misprint'],
            'misprint_proc'=>round($misprint_proc,0).'%',
            'total_qty'=>(intval($order['shipped'])+intval($order['kepted'])+intval($order['misprint'])),
            'price'=>$order['price'],
            'extracost'=>$order['extracost'],
            'extraitem'=>(intval($order['shipped'])+intval($order['kepted'])+intval($order['misprint']))*$order['extracost'],
            'totalea'=>$totalea,
            'costitem'=>round($costitem,2),
            'orangeplate'=>$order['orangeplate'],
            'orangeplate_price'=>$order['orangeplate_price'],
            'blueplate'=>$order['blueplate'],
            'blueplate_price'=>$order['blueplate_price'],
            'beigeplate' => $order['beigeplate'],
            'beigeplate_price' => $order['beigeplate_price'],
            'totalplates'=>$totalplates,
            'platescost'=>$platescost,
            'itemstotalcost'=>$totalitemcost,
            'misprintcost'=>$misprintcost,
            'printshop_type'=>$order['printshop_type'],
            'order_id'=>$order['order_id'],
            'printshop_history'=>$order['printshop_history'],
            'printshop_oldqty' => $order['printshop_oldqty'],
            'newprintshop' => $order['newprintshop'],
            'color_old' => $order['color_old'],
            'type_old' => $order['type_old'],
        );
        return $data;
    }

    public function get_printshopitem_list() {
        $this->db->select("inventory_item_id, concat(replace(item_name, 'Stress Balls',''),' ',item_num ) as item_name", FALSE);
        $this->db->from('ts_inventory_items');
        $this->db->order_by('item_name');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_item_colors($inventory_item_id) {
        $this->db->select('tspc.*');
        $this->db->from('ts_inventory_colors tspc');
        $this->db->where('tspc.inventory_item_id', $inventory_item_id);
        $this->db->order_by('tspc.color');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function change_printshop_order($orderdata, $fldname, $newval,$sessionid) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if (!array_key_exists($fldname, $orderdata)) {
            $out['msg']='Field '.$fldname.' Not Found';
            return $out;
        }
        if ($fldname=='order_num') {
            $this->db->select('order_id, customer_name');
            $this->db->from('ts_orders');
            $this->db->where('order_num', $newval);
            $res=$this->db->get()->row_array();
            if (!isset($res['order_id'])) {
                $out['msg']='Order Not Exist';
                $out['oldval']=$orderdata['order_num'];
                return $out;
            }
            $orderdata['order_id']=$res['order_id'];
            $orderdata['customer']=$res['customer_name'];
        }
        if ($fldname=='printshop_date') {
            $newval=strtotime($newval);
        }
        $orderdata[$fldname]=$newval;
        if ($fldname=='inventory_item_id') {
            // New Item
            $colors=$this->get_item_colors($newval);
            $colordef=$colors[0];
            $orderdata['price']=0;
            $orderdata['printshop_color_id']='';
            $orderdata['extracost'] = $this->_get_extracost($newval, $orderdata);
            $orderdata['colors']=$colors;
        } elseif ($fldname=='inventory_color_id') {
            $outcolor=$this->get_invitem_colordata($newval);
            if ($outcolor['result']==$this->error_result) {
                $out['msg']=$outcolor['msg'];
                return $out;
            }
            $colordat=$outcolor['color'];
            $orderdata['price']=$colordat['avg_price'];
            $costs=$this->_get_plates_costs();
            // $orderdata['extracost']=$costs['inv_addcost'];
        }
        $data=$this->_prinshoporder_params($orderdata);
        $data['items']=$orderdata['items'];
        $data['colors']=$orderdata['colors'];
        $data['session']=$orderdata['session'];
        usersession($sessionid, $data);
        $out['result']=$this->success_result;
        return $out;
    }

    public function save_printshop_order($orderdata, $sessionid, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if (empty($orderdata['inventory_color_id'])) {
            $out['msg']='Choose Item Color';
            return $out;
        }
        if (empty($orderdata['inventory_item_id'])) {
            $out['msg']='Choose Item';
            return $out;
        }
        if (empty($orderdata['order_num'])) {
            $out['msg']='Enter Order #';
            return $out;
        }
        if (empty($orderdata['customer'])) {
            $out['msg']='Enter Customer';
            return $out;
        }
        if ($orderdata['color_old']==$orderdata['inventory_color_id']) {
            // Calc diff, compare with balance
            $diff = (intval($orderdata['shipped']) + intval($orderdata['kepted']) + intval($orderdata['misprint'])) - $orderdata['printshop_oldqty'];
        } else {
            $diff = intval($orderdata['shipped']) + intval($orderdata['kepted']) + intval($orderdata['misprint']);
        }
        $balance = $this->inventory_color_income($orderdata['inventory_color_id'])-$this->inventory_color_outcome($orderdata['inventory_color_id']);
        $newbalance = $balance-$diff;
        if ($newbalance<0) {
            $out['msg']='Enter Other QTY or Increase Income, or Choose other Inventory item';
            return $out;
        }

        $this->db->set('printshop_date', $orderdata['printshop_date']);
        $this->db->set('inventory_color_id', $orderdata['inventory_color_id']);
        $this->db->set('shipped', intval($orderdata['shipped']));
        $this->db->set('kepted', intval($orderdata['kepted']));
        $this->db->set('misprint', intval($orderdata['misprint']));
        $this->db->set('orangeplate', floatval($orderdata['orangeplate']));
        $this->db->set('blueplate', floatval($orderdata['blueplate']));
        $this->db->set('beigeplate', floatval($orderdata['beigeplate']));
        $this->db->set('price', floatval($orderdata['price']));
        $this->db->set('extracost', floatval($orderdata['extracost']));
        if ($orderdata['printshop_history']==0) {
            $this->db->set('amount_sum', floatval($orderdata['itemstotalcost']));
        }
        $this->db->set('printshop_total', floatval($orderdata['itemstotalcost']));
        if ($orderdata['printshop_income_id']<=0) {
            $this->db->set('printshop_type', $orderdata['printshop_type']);
            $this->db->set('printshop', 1);
            $this->db->set('order_id', $orderdata['order_id']);
            $this->db->set('orangeplate_price', $orderdata['orangeplate_price']);
            $this->db->set('blueplate_price', $orderdata['blueplate_price']);
            $this->db->set('beigeplate_price', floatval($orderdata['beigeplate_price']));
            $this->db->set('vendor_id', $this->config->item('inventory_vendor'));
            $this->db->set('method_id', $this->config->item('inventory_paymethod'));
            $this->db->set('amount_date', time());
            $this->db->set('create_date', time());
            $this->db->set('create_user', $user_id);
            $this->db->set('update_date', time());
            $this->db->set('update_user', $user_id);
            $this->db->insert('ts_order_amounts');
            $orderdata['printshop_income_id']=$this->db->insert_id();
        } else {
            $this->db->set('update_date', time());
            $this->db->set('update_user', $user_id);
            $this->db->where('amount_id', $orderdata['printshop_income_id']);
            $this->db->update('ts_order_amounts');
        }
        // Update Orders by new COG
        $cogflag = $this->error_result;
        if (intval($orderdata['printshop_history'])==0) {
            $cogres = $this->_update_ordercog($orderdata['order_id']);
            if ($cogres['result']==$this->success_result) {
                $cogflag = $this->success_result;
            } else {
                $out['msg'] = $cogres['msg'];
            }
        } else {
            $cogflag = $this->success_result;
            log_message('ERROR', 'Order '.$orderdata['order_id'].' exclude from update COG');
        }
        if ($cogflag==$this->success_result) {
            $out['result']=$this->success_result;
            $out['order_id']=$orderdata['order_id'];
            $out['printshop_income_id']=$orderdata['printshop_income_id'];
            usersession($sessionid, $orderdata);
        }
        return $out;
    }

    public function save_inventory_outcome($orderdata, $sessionid, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if ($orderdata['newprintshop']==1) {
            // Add new outcome
            $res = $this->_add_inventory_outcome($orderdata, $user_id);
            $out['msg'] = $res['msg'];
            if ($res['result']==$this->success_result) {
                $out['result'] = $this->success_result;
            }
        } else {
            if ($orderdata['color_old']!==$orderdata['inventory_color_id']) {
                // Delete old data
                $this->delete_inventory_amount($orderdata['printshop_income_id']);
                // change inventory outcome
                $totalqty = $orderdata['total_qty'];
                $this->db->select('inventory_outcome_id, outcome_qty');
                $this->db->from('ts_inventory_outcomes');
                $this->db->where('order_id', $orderdata['order_id']);
                $this->db->where('inventory_color_id', $orderdata['color_old']);
                $outcomes = $this->db->get()->result_array();
                foreach ($outcomes as $outcome) {
                    if ( $totalqty >= $outcome['outcome_qty']) {
                        $restqty = 0;
                        $restval = $totalqty - $outcome['outcome_qty'];
                    } else {
                        $restqty = $outcome['outcome_qty'] - $totalqty;
                        $restval = 0;
                    }
                    $this->db->where('inventory_outcome_id', $outcome['inventory_outcome_id']);
                    if ($restqty==0) {
                        $this->db->delete('ts_inventory_outcomes');
                    } else {
                        $this->db->set('outcome_qty', $restqty);
                        $this->db->update('ts_inventory_outcomes');
                    }
                    if ($restval <= 0) {
                        break;
                    }
                }
                $res = $this->_add_inventory_outcome($orderdata, $user_id);
                $out['msg'] = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $out['result'] = $this->success_result;
                }
            } else {
                $diff = $orderdata['total_qty'] - $orderdata['printshop_oldqty'];
                if ($diff!==0) {
                    // Change outcome inventory
                    $this->update_orderinventory($orderdata['printshop_income_id'], $orderdata['inventory_color_id'], $diff);
                    // Change outcome
                    $this->db->select('inventory_outcome_id, outcome_qty');
                    $this->db->from('ts_inventory_outcomes');
                    $this->db->where('order_id', $orderdata['order_id']);
                    $this->db->where('inventory_color_id', $orderdata['inventory_color_id']);
                    $outcomes = $this->db->get()->result_array();
                    foreach ($outcomes as $outcome) {
                        $newval = $outcome['outcome_qty'] + $diff;
                        if ($newval <= 0) {
                            $this->db->where('inventory_outcome_id', $outcome['inventory_outcome_id']);
                            $this->db->delete('ts_inventory_outcomes');
                            $diff = $newval;
                        } else {
                            $this->db->where('inventory_outcome_id', $outcome['inventory_outcome_id']);
                            $this->db->set('outcome_qty', $newval);
                            $this->db->update('ts_inventory_outcomes');
                            $diff=0;
                        }
                        if ($diff==0) {
                            break;
                        }
                    }
                }
                $out['result'] = $this->success_result;
            }
        }
        if ($out['result']==$this->success_result) {
            // Count avg price, update amount
            $this->_count_amount_avgprice($orderdata['printshop_income_id']);
        }
        return $out;
    }
    // Remove amounts
    public function orderreport_remove($amount_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $chk=$this->get_printshop_order($amount_id);
        if ($chk['result']==$this->error_result) {
            $out['msg']=$chk['msg'];
            return $out;
        }
        $order_id=$chk['data']['order_id'];
        $color_id = $chk['data']['inventory_color_id'];
        // Get amount
        $this->db->select('shipped, kepted, misprint');
        $this->db->from('ts_order_amounts');
        $this->db->where('amount_id', $amount_id);
        $amnt = $this->db->get()->row_array();
        $totalqty = intval($amnt['shipped'])+intval($amnt['kepted'])+intval($amnt['misprint']);
        // change inventory outcome
        $this->db->select('inventory_outcome_id, outcome_qty');
        $this->db->from('ts_inventory_outcomes');
        $this->db->where('order_id', $order_id);
        $this->db->where('inventory_color_id', $color_id);
        $outcomes = $this->db->get()->result_array();
        foreach ($outcomes as $outcome) {
            if ( $totalqty >= $outcome['outcome_qty']) {
                $restqty = 0;
                $restval = $totalqty - $outcome['outcome_qty'];
            } else {
                $restqty = $outcome['outcome_qty'] - $totalqty;
                $restval = 0;
            }
            $this->db->where('inventory_outcome_id', $outcome['inventory_outcome_id']);
            if ($restqty==0) {
                $this->db->delete('ts_inventory_outcomes');
            } else {
                $this->db->set('outcome_qty', $restqty);
                $this->db->update('ts_inventory_outcomes');
            }
            if ($restval <= 0) {
                break;
            }
        }
        // Remove order inventory
        $this->delete_inventory_amount($amount_id);
        $this->db->where('amount_id', $amount_id);
        $this->db->delete('ts_order_amounts');
        // Recalc COG
        $this->_update_ordercog($order_id);
        $out['result']=$this->success_result;
        return $out;
    }

    public function delete_inventory_amount($amount_id) {
        $this->db->select('i.order_inventory_id, i.qty, i.inventory_income_id, t.income_expense, t.income_qty');
        $this->db->from('ts_order_inventory i');
        $this->db->join('ts_inventory_incomes t','i.inventory_income_id = t.inventory_income_id');
        $this->db->where('i.amount_id', $amount_id);
        $invents = $this->db->get()->result_array();
        foreach ($invents as $invent) {
            $resval = intval($invent['income_expense'])-intval($invent['qty']);
            $this->db->where('inventory_income_id', $invent['inventory_income_id']);
            $this->db->set('income_expense', $resval);
            $this->db->update('ts_inventory_incomes');
            $this->db->where('order_inventory_id', $invent['order_inventory_id']);
            $this->db->delete('ts_order_inventory');
        }
        return true;
    }

    public function update_orderinventory($amount_id, $inventory_color_id, $difqty) {
        if ($difqty < 0) {
            $difval = abs($difqty);
            $this->db->select('i.order_inventory_id, i.qty, i.inventory_income_id, t.income_expense, t.income_qty');
            $this->db->from('ts_order_inventory i');
            $this->db->join('ts_inventory_incomes t','i.inventory_income_id = t.inventory_income_id');
            $this->db->where('i.amount_id', $amount_id);
            $this->db->order_by('i.order_inventory_id','desc');
            $invents = $this->db->get()->result_array();
            foreach ($invents as $invent) {
                if ($difval <= $invent['qty']) {
                    $restval = $invent['income_expense'] - $difval;
                    $restqty = $invent['qty'] - $difval;
                    // update income
                    $this->db->where('inventory_income_id', $invent['inventory_income_id']);
                    $this->db->set('income_expense', $restval);
                    $this->db->update('ts_inventory_incomes');
                    $this->db->where('order_inventory_id', $invent['order_inventory_id']);
                    if ($restqty==0) {
                        $this->db->delete('ts_order_inventory');
                    } else {
                        $this->db->set('qty', $restqty);
                        $this->db->update('ts_order_inventory');
                    }
                    $difval=0;
                } else {
                    $restval = $invent['income_expense'] - $invent['qty'];
                    $this->db->where('inventory_income_id', $invent['inventory_income_id']);
                    $this->db->set('income_expense', $restval);
                    $this->db->update('ts_inventory_incomes');
                    $this->db->where('order_inventory_id', $invent['order_inventory_id']);
                    $this->db->delete('ts_order_inventory');
                    $difval = $difval - $invent['qty'];
                }
                if ($difval==0) {
                    break;
                }
            }
        } elseif ($difqty > 0) {
            $this->db->select('*');
            $this->db->from('ts_order_amounts');
            $this->db->where('amount_id', $amount_id);
            $amntdat = $this->db->get()->row_array();
            $this->db->select('inventory_income_id, (income_qty - income_expense) as leftqty, income_qty, income_expense');
            $this->db->from('ts_inventory_incomes');
            $this->db->where('inventory_color_id', $inventory_color_id);
            $this->db->having('leftqty > 0');
            $this->db->order_by('income_date');
            $candidats = $this->db->get()->result_array();
            foreach ($candidats as $candidat) {
                if ($difqty > $candidat['leftqty']) {
                    $newexp = $candidat['income_expense'] + $candidat['leftqty'];
                    $ordinv = $candidat['leftqty'];
                } else {
                    $newexp = $candidat['income_expense'] + $difqty;
                    $ordinv = $difqty;
                }
                $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                $this->db->set('income_expense', $newexp);
                $this->db->update('ts_inventory_incomes');
                // Insert to order inventory
                $this->db->set('order_id', $amntdat['order_id']);
                $this->db->set('inventory_income_id', $candidat['inventory_income_id']);
                $this->db->set('amount_id', $amount_id);
                $this->db->set('qty',$ordinv);
                $this->db->insert('ts_order_inventory');
                $difqty= $difqty - $candidat['leftqty'];
                if ($difqty <= 0 ) {
                    break;
                }
            }
        }
    }

    public function _update_ordercog($order_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];
        $this->db->select('order_id, revenue, shipping, is_shipping, tax, cc_fee');
        $this->db->from('ts_orders');
        $this->db->where('order_id', $order_id);
        $orddat=$this->db->get()->row_array();
        if (ifset($orddat, 'order_id',0)==$order_id) {
            $revenue=  floatval($orddat['revenue']);
            $shipping=floatval($orddat['shipping']);
            $is_shipping=intval($orddat['is_shipping']);
            $tax=floatval($orddat['tax']);
            $cc_fee=floatval($orddat['cc_fee']);
            // Get COG Value
            $this->db->select('count(amount_id) cnt, sum(amount_sum) as cog');
            $this->db->from('ts_order_amounts');
            $this->db->where('order_id', $order_id);
            $cogres=$this->db->get()->row_array();
            if ($cogres['cnt']==0) {
                // Default
                $new_order_cog=NULL;
                $new_profit_pc=NULL;
                $new_profit=round((floatval($revenue))*$this->config->item('default_profit')/100,2);
                log_message('ERROR','Empty order COG, Order ID '.$order_id.'!');
            } else {
                $new_order_cog=floatval($cogres['cog']);
                $new_profit=$revenue-($shipping*$is_shipping)-$tax-$cc_fee-$new_order_cog;
                $new_profit_pc=($revenue==0 ? null : round(($new_profit/$revenue)*100,1));
            }
            $this->db->set('order_cog',$new_order_cog);
            $this->db->set('profit',$new_profit);
            $this->db->set('profit_perc',$new_profit_pc);
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_orders');
            if (!empty($new_order_cog)) {
                $this->db->select('order_id, order_cog');
                $this->db->from('ts_orders');
                $this->db->where('order_id',$order_id);
                $orderchk = $this->db->get()->row_array();
                if (floatval($orderchk['order_cog'])==$new_order_cog) {
                    $out['result'] = $this->success_result;
                } else {
                    log_message('ERROR','Order COG update unsuccess, Order ID '.$order_id.'!');
                    $out['msg'] = 'Order COG update unsuccess';
                }
            } else {
                $out['result'] = $this->success_result;
            }
        } else {
            log_message('ERROR','Attempt update order COG, Order ID '.$order_id.'!');
        }
        return $out;
    }

    public function get_amount_details($amount_id) {
        $this->db->select('oi.qty, t.income_price as price');
        $this->db->from('ts_order_inventory oi');
        $this->db->join('ts_inventory_incomes t','oi.inventory_income_id = t.inventory_income_id');
        $this->db->where('oi.amount_id', $amount_id);
        $res = $this->db->get()->result_array();
        $out = [];
        $total_qty = $total_sum = 0;
        foreach ($res as $row) {
            $total_qty+=$row['qty'];
            $total_sum+=$row['qty']*$row['price'];
            $out[] = [
                'qty' => $row['qty'],
                'price' => $row['price'],
                'total' => $row['qty']*$row['price'],
                'totalrow' => 0,
            ];
        }
        $out[] = [
            'qty' => $total_qty,
            'price' => round($total_sum/$total_qty,3),
            'total' => $total_sum,
            'totalrow' => 1,
        ];
        return $out;
    }

    public function get_invitem_colordata($inventory_color_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->db->select('c.*, i.item_name, i.item_num');
        $this->db->from('ts_inventory_colors c');
        $this->db->join('ts_inventory_items i','i.inventory_item_id=c.inventory_item_id');
        $this->db->where('c.inventory_color_id', $inventory_color_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['inventory_color_id'])) {
            $out['msg']='Color Not Found';
        } else {
            $out['result']=$this->success_result;
            $out['color']=$res;
        }
        return $out;

    }

    private function _add_inventory_outcome($orderdata, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $outcome_type = 'P';
        // $this->db->select('count(inventory_outcome_id) as cnt, max(outcome_number) as outnumb');
        // $this->db->from('ts_inventory_outcomes');
        // $this->db->where('outcome_type', $outcome_type);
        // $outdat = $this->db->get()->row_array();
        $this->db->select('order_num, brand');
        $this->db->from('ts_orders');
        $this->db->where('order_id', $orderdata['order_id']);
        $outcome = $this->db->get()->row_array();
        // if ($outdat['cnt']==1) {
        //    $recnum = -1;
        //} else {
        //    $recnum = $outdat['outnumb'];
        // }
        // $newrecnum = $recnum + 1;
        // $recnummask = str_pad($newrecnum, 5,'0', STR_PAD_LEFT);
        // $recnum = $outcome_type.substr($recnummask,0,1).'-'.substr($recnummask,1);
        // $recnum = 'A0-'.$outcome['order_num'];
        $recnum = ($outcome['brand']=='SR' ? 'SR' : 'BT').str_pad($outcome['order_num'],'0', STR_PAD_LEFT);
        $this->db->set('inventory_color_id', $orderdata['inventory_color_id']);
        $this->db->set('outcome_date', $orderdata['printshop_date']);
        $this->db->set('outcome_qty', $orderdata['total_qty']);
        // $this->db->set('outcome_description','Order - '.$outcome['order_num']);
        $this->db->set('outcome_description', $outcome['brand']=='SR' ? 'StressRelievers' : 'Bluetrack/Stressballs');
        $this->db->set('outcome_record', $recnum);
        $this->db->set('order_id', $orderdata['order_id']);
        // $this->db->set('outcome_number', $newrecnum);
        $this->db->set('outcome_type', $outcome_type);
        $this->db->set('inserted_at', date('Y-m-d H:i:s'));
        $this->db->set('inserted_by', $user_id);
        $this->db->insert('ts_inventory_outcomes');
        // Add Order Inventory
        $this->update_orderinventory($orderdata['printshop_income_id'], $orderdata['inventory_color_id'], $orderdata['total_qty']);
        $out['result'] = $this->success_result;
        return $out;
    }

    private function _count_amount_avgprice($amount_id) {
        $this->db->select('oi.qty, t.income_price as price');
        $this->db->from('ts_order_inventory oi');
        $this->db->join('ts_inventory_incomes t','oi.inventory_income_id = t.inventory_income_id');
        $this->db->where('oi.amount_id', $amount_id);
        $res = $this->db->get()->result_array();
        $total_qty = 0; $total_sum = 0;
        foreach ($res as $item) {
            $total_qty+=$item['qty'];
            $total_sum+=$item['qty']*$item['price'];
        }
        $this->db->select('*');
        $this->db->from('ts_order_amounts');
        $this->db->where('amount_id', $amount_id);
        $amount_data = $this->db->get()->row_array();
        if ($total_qty>0) {
            $newprice = round($total_sum/$total_qty,3);
            $newcost = ($newprice + $amount_data['extracost'])*($amount_data['shipped']+$amount_data['kepted']+$amount_data['misprint']);
            $newcost+=$amount_data['orangeplate']*$amount_data['orangeplate_price']+$amount_data['blueplate']*$amount_data['blueplate_price'];
            $newcost+=$amount_data['beigeplate']*$amount_data['beigeplate_price'];
            $this->db->where('amount_id', $amount_id);
            $this->db->set('price', $newprice);
            $this->db->set('printshop_total', round($newcost,2));
            $this->db->update('ts_order_amounts');
            // Update order COG
            $this->_update_ordercog($amount_data['order_id']);
        }
        return true;
    }

    private function _get_extracost($newval, $orderdata) {
        $extracost = $orderdata['extracost'];
        $this->db->select('i.inventory_item_id, i.inventory_type_id, t.type_addcost');
        $this->db->from('ts_inventory_items i');
        $this->db->join('ts_inventory_types t', 't.inventory_type_id=i.inventory_type_id');
        $this->db->where('i.inventory_item_id', $newval);
        $itmdat = $this->db->get()->row_array();
        if (intval($itmdat['inventory_type_id'])!==intval($orderdata['type_old'])) {
            $extracost = floatval($itmdat['type_addcost']);
        }
        return $extracost;
    }

}