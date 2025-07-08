<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Printscheduler_model extends MY_Model
{
    private $mustship = 'mustshipbox';
    private $emptybalance = 'emptybalance';
    private $partial_completed = 'green-note';
    function __construct() {
        parent::__construct();
    }

    public function get_printsheduler_totals($brand)
    {
        $curdate = strtotime(date('Y-m-d'));
        $this->db->select('count(o.order_id) as cntorder');
        $this->db->from('ts_orders o');
        $this->db->where('o.print_date < ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date',0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $oldorderres = $this->db->get()->row_array();
        $this->db->select('sum(oi.item_qty) as totalitems')->from('ts_order_items oi')->join('ts_orders o','oi.order_id=o.order_id');
        $this->db->where('o.print_date < ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date',0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $olditemres = $this->db->get()->row_array();

        $this->db->select('sum(imp.imprint_qty) as totalimpr')->from('ts_order_imprints imp')->join('ts_order_items oi','imp.order_item_id=oi.order_item_id')->join('ts_orders o','o.order_id=oi.order_id');
        $this->db->where('imp.imprint_item',1);
        $this->db->where('o.print_date < ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date',0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $oldimprres = $this->db->get()->row_array();

        $this->db->select('count(o.order_id) as cntorder');
        $this->db->from('ts_orders o');
        // $this->db->where('o.print_date >= ', $curdate);
        $this->db->where('o.print_date > ', 0);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date',0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $neworderres = $this->db->get()->row_array();

        $this->db->select('sum(oi.item_qty) as totalitems')->from('ts_order_items oi')->join('ts_orders o','oi.order_id=o.order_id');
        // $this->db->where('o.print_date >= ', $curdate);
        $this->db->where('o.print_date > ', 0);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date',0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $newitemres = $this->db->get()->row_array();

        $this->db->select('sum(imp.imprint_qty) as totalimpr')->from('ts_order_imprints imp')->join('ts_order_items oi','imp.order_item_id=oi.order_item_id')->join('ts_orders o','o.order_id=oi.order_id');
        $this->db->where('imp.imprint_item',1);
        // $this->db->where('o.print_date >= ', $curdate);
        $this->db->where('o.print_date > ', 0);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date',0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $newimprres = $this->db->get()->row_array();

        return [
            'old_orders' => $oldorderres['cntorder'],
            'old_items' => intval($olditemres['totalitems']),
            'old_prints' => intval($oldimprres['totalimpr']),
            'new_orders' => $neworderres['cntorder'],
            'new_items' => intval($newitemres['totalitems']),
            'new_prints' => intval($newimprres['totalimpr']),
            // 'brand' => $brand,
        ];
    }

    public function get_pastorders($brand)
    {
        // Prepare Approved view
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        $curdate = strtotime(date('Y-m-d'));
        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, oi.order_item_id, toi.order_itemcolor_id');
        $this->db->select('o.order_approved, ii.item_num, ii.item_name, ic.color, toi.item_qty, o.print_date, toi.inventory_color_id, o.brand');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('v_itemsearch v', 'v.item_id=oi.item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date < ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date', 0);
        // Add vendor
        $this->db->where('v.vendor_id', $this->config->item('inventory_vendor'));
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $pastorders = $this->db->get()->result_array();
        $orders = [];

        foreach ($pastorders as $pastorder) {
            // Imprints
            $this->db->select('sum(if(i.imprint_item=1, 1, 0)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->where('i.order_item_id', $pastorder['order_item_id']);
            $imprdet = $this->db->get()->row_array();
            $pastorder['imprints'] = $imprdet['imprints'];
            $pastorder['prints'] = $imprdet['imprqty']*$pastorder['item_qty'];
            $pastorder['item_name'] = $pastorder['item_num'].' - '.$pastorder['item_name'];
            $pastorder['stock_class'] = '';
            $balance = $this->_scheduler_balance($pastorder['inventory_color_id']);
            if ($balance <=0 ) {
                $pastorder['stock_class'] = $this->emptybalance;
            }
            $orders[] = $pastorder;
        }
        return $orders;
    }

    public function get_ontimeorders_dates($brand)
    {
        $curdate = strtotime(date('Y-m-d'));
        $this->db->select('date_format(from_unixtime(o.print_date), \'%Y-%m-%d\') as printdate, count(o.order_id) as cntorder');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors toi','toi.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        // $this->db->where('o.print_date >= ', $curdate);
        $this->db->where('o.print_date > ', 0);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->group_by('printdate');
        $this->db->order_by('printdate');
        $newres = $this->db->get()->result_array();
        $newresidx = 0;
        foreach ($newres as $newitem) {
            $daybgn = strtotime($newitem['printdate']);
            $dayend = strtotime('+1 day', $daybgn);
            $this->db->select('sum(oi.item_qty) as totalitem');
            $this->db->from('ts_order_items oi');
            $this->db->join('ts_orders o','oi.order_id=o.order_id');
            $this->db->where('o.print_date >= ', $daybgn);
            $this->db->where('o.print_date < ', $dayend);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.shipped_date', 0);
            if ($brand!=='ALL') {
                if ($brand=='SR') {
                    $this->db->where('o.brand', $brand);
                } else {
                    $this->db->where_in('o.brand', ['SB','BT']);
                }
            }
            $itemdat = $this->db->get()->row_array();
            $this->db->select('sum(imp.imprint_qty) as totalimpr');
            $this->db->from('ts_order_imprints imp');
            $this->db->join('ts_order_items oi','imp.order_item_id=oi.order_item_id');
            $this->db->join('ts_orders o','oi.order_id=o.order_id');
            $this->db->where('o.print_date >= ', $daybgn);
            $this->db->where('o.print_date < ', $dayend);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.print_finish',0);
            if ($brand !== 'ALL') {
                if ($brand=='SR') {
                    $this->db->where('o.brand', $brand);
                } else {
                    $this->db->where_in('o.brand', ['SB','BT']);
                }
            }
            $this->db->where('imp.imprint_item',1);
            $imprdat = $this->db->get()->row_array();

            $newres[$newresidx]['totalitems'] = intval($itemdat['totalitem']);
            $newres[$newresidx]['totalimpr'] = intval($imprdat['totalimpr']);
            $newresidx++;
        }
        return $newres;
    }

    public function get_ontimedates($brand, $pastorder=0)
    {
        $curdate = strtotime(date('Y-m-d'));
        $this->db->select('min(o.print_date) as mindate, max(o.print_date) as maxdate, count(o.order_id) as cntorder');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors toi','toi.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        if ($pastorder==1) {
            $this->db->where('o.print_date < ', $curdate);
        } else {
            // $this->db->where('o.print_date >= ', $curdate);
            $this->db->where('o.print_date > ', 0);
        }
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $res = $this->db->get()->row_array();
        if ($res['cntorder']>0) {
            return ['min' => $res['mindate'], 'max' => $res['maxdate']];
        } else {
            return ['min' => $curdate, 'max' => $curdate];
        }
    }

    public function get_ontimeorders_day($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, oi.order_item_id, sh.event_date, toi.order_itemcolor_id, o.order_approved');
        $this->db->select('ii.item_num, ii.item_name, ic.color, toi.item_qty, o.print_date, toi.inventory_color_id, o.brand');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings sh','o.order_id=sh.order_id');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $currorders = $this->db->get()->result_array();
        $orders = [];

        foreach ($currorders as $currorder) {
            // Imprints
            // $currorder['inventory_color'] = $this->_inventory_color($currorder['item_number'], $currorder['item_color']);
            $currorder['inventory_color'] = $currorder['inventory_color_id'];
            // $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->select('sum(if(i.imprint_item=1, 1, 0)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->where('i.order_item_id', $currorder['order_item_id']);
            $imprdet = $this->db->get()->row_array();
            $currorder['shipclass'] = '';
            if (!empty($currorder['event_date'])) {
                $currorder['shipclass'] = $this->mustship;
            }
            $currorder['imprints'] = $imprdet['imprints'];
            $currorder['prints'] = $imprdet['imprqty']*$currorder['item_qty'];
            $currorder['item_name'] = $currorder['item_num'].' - '.$currorder['item_name'];
            $currorder['stock_class'] = '';
            $balance = $this->_scheduler_balance($currorder['inventory_color']);
            if ($balance <=0 ) {
                $currorder['stock_class'] = $this->emptybalance;
            }
            $orders[] = $currorder;
        }
        return $orders;
    }

    public function get_dayorders($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        $stocks = $plates = [];
        $order_idx = [];
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        // get order details
        $this->db->select('o.order_id, o.order_num, ii.item_num, ii.item_name, ic.color, o.shipdate, o.order_qty, o.order_rush, toi.print_ready');
        $this->db->select('oi.plates_ready, oi.order_item_id, toi.order_itemcolor_id, toi.item_qty, o.order_rush, o.brand, toi.inventory_color_id');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >=', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where(['o.is_canceled'=>0, 'o.shipped_date' => 0]);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, ii.item_num, ic.suggeststock desc, ic.color');
        $orders = $this->db->get()->result_array();
        foreach ($orders as $order) {
            $order['item_name'] = $order['item_num'].' - '.$order['item_name'];
            $order['stock_class'] = ($order['print_ready'] == 0) ? '' : 'inwork';
            if ($order['order_blank']==1) {
                $order['plate_class'] = ($order['print_ready'] == 0 ? '': 'inwork');
            } else {
                $order['plate_class'] = ($order['plates_ready'] == 0) ? '' : 'inwork';
            }
            // Balance
            $order['balance_class'] = '';
            $balance = $this->_scheduler_balance($order['inventory_color_id']);
            if ($balance <=0 ) {
                $order['balance_class'] = $this->emptybalance;
            }
            $order['imprints'] = $order['plates'] = '';
            if ($order['order_blank'] == 1) {
                $order['imprints'] = 'blank order';
            } else {
                if (!empty($order['order_item_id'])) {
                    $this->db->select('substr(imprint_description,1,6) as locnum, count(*) as cnt')->from('ts_order_imprints')->where(['order_item_id' => $order['order_item_id'],'imprint_item' => 1]);
                    $this->db->group_by('locnum')->order_by('locnum');
                    $imprres = $this->db->get()->result_array();
                    $loctxt = ''; $plates = 0;
                    foreach ($imprres as $impr) {
                        $loctxt .= $impr['cnt'].' + ';
                        $plates += $impr['cnt'];
                    }
                    $order['imprints'] = substr($loctxt, 0, -3);
                    $order['plates'] = $plates;
                }
            }
            $stocks[] = $order;
        }
        return $stocks;
    }

    public function get_dayprintorders($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready');
        $this->db->select('v.item_number, toi.item_description, toi.item_color, toi.item_qty, o.print_user, u.first_name');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('users u', 'u.user_id=o.print_user','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',0);
        $this->db->where('o.print_ready > ', 0);
        $this->db->where('o.shipping_ready',0);

        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.print_user, o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $unassign = $assign = [];
        $totals = [];
        $totals[] = [
            'print_user' => null,
            'prints' => 0,
            'items' => 0,
            'orders' => 0,
        ];
        $totalidx = 0;
        $ordernum = '';
        $prinusr = '';
        foreach ($orders as $order) {
            if (!empty($order['print_user']) && $order['print_user']!==$prinusr)  {
                $totals[] = [
                    'print_user' => $order['print_user'],
                    'prints' => 0,
                    'items' => 0,
                    'orders' => 0,
                ];
                $totalidx = count($totals)-1;
                $ordernum = '';
                $prinusr = $order['print_user'];
            }
            if ($order['order_num'] != $ordernum) {
                $totals[$totalidx]['orders']+=1;
            }
            $totals[$totalidx]['items']+=$order['item_qty'];
            if (empty($order['print_user'])) {
                $unassign[] = $order;
            } else {
                $assign[] = $order;
            }
        }
        return [
            'unasigned' => $unassign,
            'assign' => $assign,
            'totals' => $totals,
        ];
    }

    public function get_dayunassignorders($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        // count orders
        $this->db->select('count(distinct(o.order_id)) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('oic.print_ready > ', 0);
        $this->db->where('oi.plates_ready > ',0);
        $this->db->where('oic.print_completed', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.print_user', null);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $ordercnt = $this->db->get()->row_array();
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, toi.print_ready, oi.plates_ready, oi.order_item_id, sh.event_date, toi.order_itemcolor_id');
        $this->db->select('ii.item_num, ii.item_name, ic.color, toi.item_qty, toi.inventory_color_id, o.brand');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings sh','o.order_id=sh.order_id');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('toi.print_ready > ', 0);
        $this->db->where('oi.plates_ready > ',0);
        $this->db->where('toi.print_completed', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.print_user', null);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $unassign = [];
        $totals = [
            'prints' => 0,
            'items' => 0,
            'orders' => $ordercnt['cnt'],
        ];
        foreach ($orders as $order) {
            // $order['inventory_color'] = $this->_inventory_color($order['item_number'], $order['item_color']);
            $order['inventory_color'] = $order['inventory_color_id'];
            $passed = $this->_completed_itemcolor($order['order_id'], $order['inventory_color']);
            $order['qtyclass'] = '';
            if ($passed > 0) {
                $order['qtyclass'] = $this->partial_completed;
                $order['item_qty'] = $order['item_qty'] - $passed;
            }
            // Imprints
            // $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->select('sum(if(i.imprint_item=1, 1, 0)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->where('i.order_item_id', $order['order_item_id']);
            $imprdet = $this->db->get()->row_array();
            $order['imprints'] = $imprdet['imprints'];
            $totals['items']+=$order['item_qty'];
            $order['prints'] = $imprdet['imprqty']*$order['item_qty'];
            $totals['prints']+=$imprdet['imprqty']*$order['item_qty'];
            $order['item_name'] = $order['item_num'].' - '.$order['item_name'];
            $order['shipclass'] = '';
            if (!empty($order['event_date'])) {
                $order['shipclass'] = $this->mustship;
            }
            $order['stock_class'] = '';
            $balance = $this->_scheduler_balance($order['inventory_color']);
            if ($balance <=0 ) {
                $order['stock_class'] = $this->emptybalance;
            }
            $unassign[] = $order;
        }
        return [
            'orders' => $unassign,
            'totals' => $totals,
        ];
    }

    public function get_dayassignorders($printdate, $user_id, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        // count orders
        $this->db->select('count(distinct(o.order_id)) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('oic.print_ready > ', 0);
        $this->db->where('oi.plates_ready > ', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('oic.print_completed', 0);
        $this->db->where('o.print_user', $user_id);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $ordercnt = $this->db->get()->row_array();
        // get order details
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, o.plates_ready, oi.order_item_id');
        $this->db->select('sh.event_date, toi.order_itemcolor_id, ii.item_num, ii.item_name, ic.color, toi.item_qty, toi.inventory_color_id, o.brand');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings sh','o.order_id=sh.order_id');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('toi.print_ready > ', 0);
        $this->db->where('oi.plates_ready > ', 0);
        $this->db->where('toi.print_completed', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.print_user', $user_id);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $assign = [];
        $totals = [
            'prints' => 0,
            'items' => 0,
            'orders' => $ordercnt['cnt'],
        ];
        foreach ($orders as $order) {
            // $order['inventory_color'] = $this->_inventory_color($order['item_number'], $order['item_color']);
            $order['inventory_color'] = $order['inventory_color_id'];
            $passed = $this->_completed_itemcolor($order['order_id'], $order['inventory_color']);
            $order['qtyclass'] = '';
            if ($passed > 0) {
                $order['qtyclass'] = $this->partial_completed;
                $order['item_qty'] = $order['item_qty'] - $passed;
            }
            $totals['items']+=$order['item_qty'];
            // Imprints
            $this->db->select('sum(if(i.imprint_item=1, 1, 0)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->where('i.order_item_id', $order['order_item_id']);
            $imprdet = $this->db->get()->row_array();
            $order['imprints'] = $imprdet['imprints'];
            $order['prints'] = $imprdet['imprqty']*$order['item_qty'];
            $totals['prints']+=$imprdet['imprqty']*$order['item_qty'];
            $order['item_name'] = $order['item_num'].' - '.$order['item_name'];
            $order['shipclass'] = '';
            if (!empty($order['event_date'])) {
                $order['shipclass'] = $this->mustship;
            }
            $order['stock_class'] = '';
            $balance = $this->_scheduler_balance($order['inventory_color']);
            if ($balance <=0 ) {
                $order['stock_class'] = $this->emptybalance;
            }
            $assign[] = $order;
        }
        return [
            'orders' => $assign,
            'totals' => $totals,
        ];
    }

    public function get_day_assignusers($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        $this->db->select('o.print_user as user_id, u.first_name as user_name, count(o.order_id) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('users u','u.user_id=o.print_user');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors oic','oi.order_item_id=oic.order_item_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',0);
        $this->db->where('oic.print_ready > ', 0);
        $this->db->where('oi.plates_ready > ', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.print_user != ', null);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->group_by('o.print_user, u.first_name');
        $users = $this->db->get()->result_array();
        return $users;
    }

    // Get ready to ship
    public function getreadyshiporders($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        // Total orders
        $this->db->select('count(distinct(o.order_id)) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('oic.print_date > ',0);
        $this->db->where('oic.print_completed', 1);
        $this->db->where('oic.shipping_ready',0);
        $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $ordercnt = $this->db->get()->row_array();
        $totals = [
            'items' => 0,
            'orders' => $ordercnt['cnt'],
        ];
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, oi.order_item_id, sh.event_date, toi.order_itemcolor_id');
        $this->db->select('ii.item_num, ii.item_name, ic.color, toi.item_qty, toi.inventory_color_id, o.brand');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings sh','o.order_id=sh.order_id');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('toi.print_date > ',0);
        $this->db->where('toi.print_completed', 1);
        $this->db->where('toi.shipping_ready',0);
        $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $ships = [];
        foreach ($orders as $order) {
            // Check a prev send
            $sendqty =  $this->_shipped_itemcolor($order['order_itemcolor_id']);
            $order['item_qty'] = $order['item_qty'] - $sendqty;
            $totals['items']+=$order['item_qty'];
            $order['item_name'] = $order['item_num'].' - '.$order['item_name'];
            $order['shipclass'] = '';
            if (!empty($order['event_date'])) {
                $order['shipclass'] = $this->mustship;
            }
            $ships[] = $order;
        }
        return [
            'orders' => $ships,
            'totals' => $totals,
        ];
    }

    public function get_day_completedusers($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        $this->db->select('o.print_user as user_id, u.first_name as user_name, count(o.order_id) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('users u','u.user_id=o.print_user');
        $this->db->join('ts_order_items oi', 'o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        // $this->db->where('o.print_finish > ',0);
        $this->db->where('oic.print_date > ',0);
        $this->db->where('oic.print_completed', 1); // 0
        $this->db->where('o.shipped_date', 0);
        $this->db->where('o.print_user != ', null);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->group_by('o.print_user, u.first_name');
        $users = $this->db->get()->result_array();
        return $users;
    }

    public function getcompleteprintorders($printdate, $user_id, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        // Total orders
        $this->db->select('count(distinct(o.order_id)) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        // $this->db->where('o.print_finish > ',0);
        $this->db->where('oic.print_date > ',0);
        $this->db->where('oic.print_completed', 1); // 0
        $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $ordercnt = $this->db->get()->row_array();
        $totals = [
            'prints' => 0,
            'items' => 0,
            'orders' => $ordercnt['cnt'],
        ];
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, oi.order_item_id, sh.event_date, toi.order_itemcolor_id');
        $this->db->select('ii.item_num, ii.item_name, ic.color, toi.item_qty, toi.inventory_color_id, o.brand');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings sh','o.order_id=sh.order_id');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        // $this->db->where('o.print_finish > ',0);
        $this->db->where('toi.print_date > ',0);
        $this->db->where('toi.print_completed', 1); // 0
        $this->db->where('o.print_user', $user_id);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $ships = [];
        foreach ($orders as $order) {
            // $order['inventory_color'] = $this->_inventory_color($order['item_number'], $order['item_color']);
            $order['inventory_color'] = $order['inventory_color_id'];
            $passed = $this->_completed_itemcolor($order['order_id'], $order['inventory_color']);
            $order['qtyclass'] = '';
            if ($passed > 0) {
                $order['qtyclass'] = $this->partial_completed;
                $order['item_qty'] = $order['item_qty'] - $passed;
            }
            $totals['items']+=$order['item_qty'];
            // Imprints
            // $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->select('sum(if(i.imprint_item=1, 1, 0)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->where('i.order_item_id', $order['order_item_id']);
            $imprdet = $this->db->get()->row_array();
            $order['imprints'] = $imprdet['imprints'];
            $order['prints'] = $imprdet['imprqty']*$order['item_qty'];
            $totals['prints']+=$imprdet['imprqty']*$order['item_qty'];
            $order['item_name'] = $order['item_num'].' - '.$order['item_name'];
            $order['shipclass'] = '';
            if (!empty($order['event_date'])) {
                $order['shipclass'] = $this->mustship;
            }
            $order['stock_class'] = '';
            $balance = $this->_scheduler_balance($order['inventory_color']);
            if ($balance <=0 ) {
                $order['stock_class'] = $this->emptybalance;
            }
            $ships[] = $order;
        }
        return [
            'orders' => $ships,
            'totals' => $totals,
        ];
    }

    public function getshippedorders($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        // Total orders
        $this->db->select('count(distinct(o.order_id)) as cnt');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('oic.print_date > ',0);
        $this->db->where('oic.print_completed', 1);
        $this->db->where('oic.shipping_ready > ',0);
        // $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $ordercnt = $this->db->get()->row_array();
        $totals = [
            'items' => 0,
            'orders' => $ordercnt['cnt'],
        ];
        /* Prepare Approved view */
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt');
        $this->db->from('ts_artworks a');
        $this->db->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
        $this->db->where('p.approved > ',0);
        $this->db->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();

        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, oi.order_item_id, sh.event_date, toi.order_itemcolor_id');
        $this->db->select('ii.item_num, ii.item_name, ic.color, toi.item_qty, o.brand, toi.inventory_color_id');
        $this->db->select('o.order_blank, coalesce(p.cnt,0) as approved');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings sh','o.order_id=sh.order_id');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$proofsql.') p','p.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('toi.print_date > ',0);
        $this->db->where('toi.print_completed', 1);
        $this->db->where('toi.shipping_ready > ',0);
        // $this->db->where('o.shipped_date', 0);
        if ($brand !== 'ALL') {
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
        }
        $this->db->order_by('o.order_rush desc, o.order_id'); // $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $ships = [];
        foreach ($orders as $order) {
            $totals['items']+=$order['item_qty'];
            $order['item_name'] = $order['item_num'].' - '.$order['item_name'];
            $order['shipclass'] = '';
            if (!empty($order['event_date'])) {
                $order['shipclass'] = $this->mustship;
            }
            // Get Track codes
            $this->db->select('*')->from('ts_order_trackings')->where('order_itemcolor_id', $order['order_itemcolor_id']);
            $tracks = $this->db->get()->result_array();
            foreach ($tracks as $track) {
                $ships[] = [
                    'order_id' => $order['order_id'],
                    'order_num' => $order['order_num'],
                    'order_blank' => $order['order_blank'],
                    'approved' => $order['approved'],
                    'item_color' => $order['color'],
                    'item_name' => $order['item_name'],
                    'item_qty' => $order['item_qty'],
                    'shipclass' => $order['shipclass'],
                    'shipdate' => $track['trackdate'],
                    'shipqty' => $track['qty'],
                    'shipmethod' => $track['trackservice'],
                    'trackcode' => $track['trackcode'],
                    'brand' => $order['brand'],
                ];
            }

        }
        return [
            'orders' => $ships,
            'totals' => $totals,
        ];
    }

    public function stockdonecheck($order_id, $updtype, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];
        if ($updtype=='stock') {
            $this->db->select('oic.order_itemcolor_id as order_id, oic.print_ready, o.print_date, o.order_blank, o.order_id as id')->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi','oi.order_item_id=oic.order_item_id')->join('ts_orders o','o.order_id=oi.order_id');
            $this->db->where('oic.order_itemcolor_id', $order_id);
        } else {
            $this->db->select('oi.order_item_id as order_id, oi.plates_ready, o.print_date, o.order_id as id')->from('ts_order_items oi');
            $this->db->join('ts_orders o','o.order_id=oi.order_id')->where('oi.order_item_id', $order_id);
        }
        $orderres = $this->db->get()->row_array();
        if (ifset($orderres,'order_id',0)==$order_id) {
            // Check - empty movement of order
            $out['msg'] = 'Order in work';
            if ($updtype=='stock') {
                $this->db->select('count(amount_id) as cnt, sum(shipped) as shipped')->from('ts_order_amounts')->where('order_itemcolor_id', $order_id);
            } else {
                $this->db->select('count(toa.amount_id) as cnt, sum(toa.shipped) as shipped')->from('ts_order_amounts toa')->join('ts_order_itemcolors toi','toi.order_itemcolor_id = toa.order_itemcolor_id');
                $this->db->where('toi.order_item_id', $order_id);
            }
            $chkres = $this->db->get()->row_array();
            if ($chkres['cnt']==0) {
                $out['result'] = $this->success_result;
                $out['printdate'] = date('Y-m-d',$orderres['print_date']);
                $outupdate = 0;
                if ($updtype=='stock') {
                    $this->db->where('order_itemcolor_id', $order_id);
                    if ($orderres['print_ready']==0) {
                        $this->db->set('print_ready',time());
                        if ($orderres['order_blank']==1) {
                            $this->db->set('print_completed', 1);
                            $this->db->set('print_date', time());
                            $outupdate = 1;
                        }
                        $out['checked'] = 1;
                    } else {
                        $this->db->set('print_ready',0);
                        if ($orderres['order_blank']==1) {
                            $this->db->set('print_completed', 0);
                            $this->db->set('print_date', 0);
                        }
                        $out['checked'] = 0;
                    }
                    $this->db->update('ts_order_itemcolors');
                } else {
                    $this->db->where('order_item_id', $order_id);
                    if ($orderres['plates_ready']==0) {
                        $this->db->set('plates_ready',time());
                        $out['checked'] = 1;
                    } else {
                        $this->db->set('plates_ready',0);
                        $out['checked'] = 1;
                    }
                    $this->db->update('ts_order_items');
                }
                // Update outcome - for blank order
                if ($outupdate==1) {
                    $updres = $this->_inventory_blank_outcome($order_id, $user_id);
                }
            }
        }
        return $out;
    }

    public function assignorder($order_id, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];
        $this->db->select('order_id, print_date')->from('ts_orders')->where('order_id', $order_id);
        $orderres = $this->db->get()->row_array();
        if (ifset($orderres,'order_id',0)==$order_id) {
            $out['result'] = $this->success_result;
            $out['printdate'] = date('Y-m-d',$orderres['print_date']);
            $this->db->where('order_id', $order_id);
            $this->db->set('print_user', $user_id);
            $this->db->update('ts_orders');
        }
        return $out;
    }

    public function outcomedata($order_itemcolor_id, $inventory_color_id, $shipped, $kepted, $misprint, $plates, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order not found'];
        // Get order
        $this->db->select('oic.order_itemcolor_id, oic.print_date colorprint, oic.print_completed, oic.item_qty, o.order_id, o.order_num, o.order_date, o.customer_name, o.print_date')->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi','oic.order_item_id = oi.order_item_id')->join('ts_orders o','oi.order_id = o.order_id')->where('oic.order_itemcolor_id', $order_itemcolor_id);
        $orderdata = $this->db->get()->row_array();
        if (ifset($orderdata,'order_id',0) > 0) {
            $out['msg'] = 'Empty Outcome values';
            if (floatval($shipped) + floatval($kepted) + floatval($misprint) + floatval($plates) > 0) {
                $this->load->model('inventory_model');
                $diff = intval($shipped) + intval($kepted) + intval($misprint);
                $balance = $this->inventory_model->inventory_color_income($inventory_color_id)-$this->inventory_model->inventory_color_outcome($inventory_color_id);
                $newbalance = $balance-$diff;
                if ($newbalance < 0) {
                    $out['msg'] = 'Enter Other QTY or Increase Income, or Choose other Inventory item';
                    return $out;
                }
                $orderdata = $this->_prepare_amount_save($orderdata, $inventory_color_id, $shipped, $kepted, $misprint, $plates);
                $amountres = $this->_save_amount($orderdata, $user_id);
                $out['msg'] = $amountres['msg'];
                if ($amountres['result']==$this->success_result) {
                    $cogflag = $this->error_result;
                    $cogres = $this->inventory_model->_update_ordercog($orderdata['order_id']);
                    if ($cogres['result']==$this->success_result) {
                        $cogflag = $this->success_result;
                    } else {
                        $out['msg'] = $cogres['msg'];
                    }
                    if ($cogflag==$this->success_result) {
                        $out['result']=$this->success_result;
                        $out['order_id']=$orderdata['order_id'];
                        $out['printshop_income_id']=$amountres['amount_id'];
                        $out['printdate'] = date('Y-m-d', $orderdata['print_date']);
                        // Update print_date & print_completed
                        $passed = $this->_completed_itemcolor($orderdata['order_id'], $inventory_color_id);
                        $print_compl = 0;
                        if ($passed >= $orderdata['item_qty']) {
                            $print_compl = 1;
                        }
                        $this->db->where('order_itemcolor_id', $orderdata['order_itemcolor_id']);
                        $this->db->set('print_completed', $print_compl);
                        if ($orderdata['colorprint']==0) {
                            $this->db->set('print_date', time());
                        }
                        $this->db->update('ts_order_itemcolors');
                    }
                }
            }
        }
        return $out;
    }

    public function save_inventory_outcome($amount_id, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Amount not found'];
        $this->db->select('oa.amount_id as printshop_income_id, oa.order_id, oa.inventory_color_id, oa.printshop_date, (oa.shipped+oa.misprint+oa.kepted) as total_qty, o.brand')->from('ts_order_amounts oa')->join('ts_orders o','oa.order_id=oa.order_id')->where('oa.amount_id', $amount_id);
        $orderdata = $this->db->get()->row_array();
        $this->load->model('inventory_model');
        $invres = $this->inventory_model->_add_inventory_outcome($orderdata, $user_id);
        $out['msg'] = $invres['msg'];
        if ($invres['result']==$this->success_result) {
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function shiporder($order_itemcolor_id, $shipqty, $shipmethod, $trackcode, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order not found'];
        $this->db->select('oic.order_itemcolor_id, oic.item_qty, oic.shipping_ready, o.order_id, o.order_num, o.print_date')->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi','oic.order_item_id = oi.order_item_id')->join('ts_orders o','oi.order_id = o.order_id')->where('oic.order_itemcolor_id', $order_itemcolor_id);
        $orderdata = $this->db->get()->row_array();
        if (ifset($orderdata, 'order_id',0) > 0) {
            // Order found
            if (empty($orderdata['shipping_ready'])) {
                $shippingqty = intval($shipqty);
                // Check empty tracking
                $this->db->select('*')->from('ts_order_trackings')->where(['order_itemcolor_id' => $order_itemcolor_id,'qty'=>0]);
                $trackraw = $this->db->get()->row_array();
                if (ifset($trackraw, 'tracking_id',0) > 0) {
                    $this->db->where('tracking_id', $trackraw['tracking_id']);
                    $this->db->set('updated_by', $user_id);
                    $this->db->set('qty', $shippingqty);
                    $this->db->set('trackdate', time());
                    $this->db->set('trackservice', $shipmethod);
                    $this->db->set('trackcode', $trackcode);
                    $this->db->update('ts_order_trackings');
                    $track_id = $trackraw['tracking_id'];
                } else {
                    // Add new tracking
                    $this->db->set('created_at', date('Y-m-d H:i:s'));
                    $this->db->set('created_by', $user_id);
                    $this->db->set('updated_by', $user_id);
                    $this->db->set('order_itemcolor_id', $order_itemcolor_id);
                    $this->db->set('qty', $shippingqty);
                    $this->db->set('trackdate', time());
                    $this->db->set('trackservice', $shipmethod);
                    $this->db->set('trackcode', $trackcode);
                    $this->db->insert('ts_order_trackings');
                    $track_id = $this->db->insert_id();
                }
                if ($track_id > 0) {
                    $out['result'] = $this->success_result;
                    $out['printdate'] = date('Y-m-d', $orderdata['print_date']);
                    $tracked = $this->_shipped_itemcolor($order_itemcolor_id);
                    if ($tracked >= $orderdata['item_qty']) {
                        $this->db->where('order_itemcolor_id', $order_itemcolor_id);
                        $this->db->set('shipping_ready', time());
                        $this->db->update('ts_order_itemcolors');
                    }
                    // Count shipping parts
                    $this->db->select('count(oic.order_itemcolor_id) as cnt')->from('ts_order_itemcolors oic')->join('ts_order_items oi', 'oic.order_item_id=oi.order_item_id')->join('ts_orders o', 'o.order_id=oi.order_id');
                    $this->db->where(['o.order_id' => $orderdata['order_id'],'oic.shipping_ready' => 0]);
                    $chkres = $this->db->get()->row_array();
                    if ($chkres['cnt']==0) {
                        $this->db->where('order_id', $orderdata['order_id']);
                        $this->db->set('shipped_date', time());
                        $this->db->update('ts_orders');
                    }
                }
            } else {
                $out['result'] = $this->success_result;
                $out['printdate'] = date('Y-m-d', $orderdata['print_date']);
            }
        }
        return $out;
    }

    private function _scheduler_balance($inventory_color_id)
    {
        // $out = ['result' => $this->error_result, 'msg' => 'Inventory not found', 'balance' => 0];
        $balance = 0;
        if ($inventory_color_id) {
            $this->load->model('inventory_model');
            $balance = $this->inventory_model->inventory_balance($inventory_color_id);
        }
        return $balance;
    }

    public function _inventory_color($item_number, $item_color)
    {
        $color_id = NULL;
        $this->db->select('inventory_item_id')->from('ts_inventory_items')->where('item_num', $item_number);
        $itemres = $this->db->get()->row_array();
        if (ifset($itemres,'inventory_item_id',0) > 0) {
            // Color
            $this->db->select('inventory_color_id')->from('ts_inventory_colors')->where(['inventory_item_id' => $itemres['inventory_item_id'], 'color' => $item_color]);
            $colorres = $this->db->get()->row_array();
            if (ifset($colorres, 'inventory_color_id', 0) > 0) {
                $color_id = $colorres['inventory_color_id'];
            }
        }
        return $color_id;
    }

    private function _completed_itemcolor($order_id, $inventory_color)
    {
        $outcome = 0;
        $this->db->select('count(amount_id) as amntcnt, sum(shipped) as amnttotal')->from('ts_order_amounts')->where(['order_id' => $order_id, 'inventory_color_id' => $inventory_color]);
        $amntres = $this->db->get()->row_array();
        if ($amntres['amntcnt'] > 0) {
            $outcome = $amntres['amnttotal'];
        }
        return $outcome;
    }

    private function _shipped_itemcolor($order_itemcolor_id)
    {
        $outcome = 0;
        $this->db->select('count(tracking_id) as cnt, sum(qty) as tracked')->from('ts_order_trackings')->where('order_itemcolor_id', $order_itemcolor_id);
        $trackres = $this->db->get()->row_array();
        if ($trackres['cnt']>0) {
            $outcome = $trackres['tracked'];
        }
        return $outcome;
    }

    private function _prepare_amount_save($orderdata, $inventory_color_id, $shipped, $kepted, $misprint, $plates)
    {
        // Prices
        $this->db->select('c.price, c.avg_price, t.type_addcost')->from('ts_inventory_colors c')->join('ts_inventory_items i','c.inventory_item_id = i.inventory_item_id');
        $this->db->join('ts_inventory_types t','i.inventory_type_id = t.inventory_type_id')->where('c.inventory_color_id', $inventory_color_id);
        $pricedat = $this->db->get()->row_array();
        // Prepare saving
        $platesprice = $this->inventory_model->_get_plates_costs();
        $orderdata['printshop_date'] = time();
        $orderdata['inventory_color_id'] = $inventory_color_id;
        $orderdata['shipped'] = intval($shipped);
        $orderdata['kepted'] = intval($kepted);
        $orderdata['misprint'] = intval($misprint);
        $orderdata['blueplate'] = floatval($plates);
        $orderdata['blueplate_price'] = $platesprice['blueplate_price'];
        $orderdata['extracost'] = floatval($pricedat['type_addcost']);
        $orderdata['printshop_type'] = 'S';
        $totalea = round($pricedat['avg_price']+$pricedat['type_addcost'],3);
        $costitem = $totalea * ($orderdata['shipped']+$orderdata['kepted']+$orderdata['misprint']);
        $platescost = $plates * $orderdata['blueplate_price'];
        $totalitemcost=$platescost+$costitem;
        $orderdata['price'] = $pricedat['avg_price'];
        $orderdata['itemstotalcost'] = $totalitemcost;

        return $orderdata;
    }

    private function _save_amount($orderdata, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Error during Save Amount'];
        $this->db->set('printshop_date', $orderdata['printshop_date']);
        $this->db->set('inventory_color_id', $orderdata['inventory_color_id']);
        $this->db->set('shipped', $orderdata['shipped']);
        $this->db->set('kepted', $orderdata['kepted']);
        $this->db->set('misprint', $orderdata['misprint']);
        // $this->db->set('orangeplate', floatval($orderdata['orangeplate']));
        $this->db->set('blueplate', floatval($orderdata['blueplate']));
        // $this->db->set('beigeplate', floatval($orderdata['beigeplate']));
        $this->db->set('price', floatval($orderdata['price']));
        $this->db->set('extracost', floatval($orderdata['extracost']));
        // if ($orderdata['printshop_history']==0) {
        $this->db->set('amount_sum', floatval($orderdata['itemstotalcost']));
        // }
        $this->db->set('printshop_total', floatval($orderdata['itemstotalcost']));
        $this->db->set('printshop_type', $orderdata['printshop_type']);
        $this->db->set('printshop', 1);
        $this->db->set('order_id', $orderdata['order_id']);
        $this->db->set('order_itemcolor_id', $orderdata['order_itemcolor_id']);
        // $this->db->set('orangeplate_price', $orderdata['orangeplate_price']);
        $this->db->set('blueplate_price', $orderdata['blueplate_price']);
        // $this->db->set('beigeplate_price', floatval($orderdata['beigeplate_price']));
        $this->db->set('vendor_id', $this->config->item('inventory_vendor'));
        $this->db->set('method_id', $this->config->item('inventory_paymethod'));
        $this->db->set('amount_date', time());
        $this->db->set('create_date', time());
        $this->db->set('create_user', $user_id);
        $this->db->set('update_date', time());
        $this->db->set('update_user', $user_id);
        $this->db->insert('ts_order_amounts');
        $amntid = $this->db->insert_id();
        if ($amntid > 0) {
            // successfully
            $out['result'] = $this->success_result;
            $out['amount_id'] = $amntid;
        }
        return $out;
    }

    public function update_printdate($printdate, $order)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Error during Update Printdate'];
        $print_date = strtotime($printdate);
        $this->db->select('order_id, print_date')->from('ts_orders')->where('order_id' , $order);
        $orddat = $this->db->get()->row_array();
        if (ifset($orddat, 'order_id', 0) == $order) {
            $out['result'] = $this->success_result;
            $this->db->where('order_id', $order);
            $this->db->set('print_date', $print_date);
            $this->db->update('ts_orders');
        }
        return $out;
    }

//    public function get_daystocks($printdate, $brand)
//    {
//        $daybgn = strtotime($printdate);
//        $dayend = strtotime('+1 day', $daybgn);
//        $stocks = [];
//        // get order details
//        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, o.plates_ready, oi.order_item_id, toi.order_itemcolor_id');
//        $this->db->select('v.item_number, toi.item_description, toi.item_color, toi.item_qty, o.brand, toi.inventory_color_id');
//        $this->db->from('ts_orders o');
//        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
//        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
//        $this->db->join('v_itemsearch v', 'v.item_id=oi.item_id');
//        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
//        $this->db->where('o.print_date >= ', $daybgn);
//        $this->db->where('o.print_date < ', $dayend);
//        $this->db->where('o.is_canceled',0);
//        $this->db->where('o.shipped_date',0);
//        if ($brand !== 'ALL') {
//            if ($brand=='SR') {
//                $this->db->where('o.brand', $brand);
//            } else {
//                $this->db->where_in('o.brand', ['SB','BT']);
//            }
//        }
//        $this->db->order_by('o.print_ready asc, o.order_rush desc, o.order_id asc'); // $this->db->order_by('o.order_rush desc, o.order_num');
//        $orders = $this->db->get()->result_array();
//        foreach ($orders as $order) {
//            $order['item_name'] = $order['item_number'].' - '.$order['item_description'];
//            $order['order_class'] = ($order['print_ready'] == 0) ? '' : 'inwork';
//            $stocks[] = $order;
//        }
//        return $stocks;
//    }

//    public function get_dayplates($printdate, $brand)
//    {
//        $daybgn = strtotime($printdate);
//        $dayend = strtotime('+1 day', $daybgn);
//        $plates = [];
//        // Get Plates
//        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, o.plates_ready, oi.order_item_id, toi.order_itemcolor_id');
//        $this->db->select('v.item_number, toi.item_description, toi.item_color, toi.item_qty, o.brand, toi.inventory_color_id');
//        $this->db->from('ts_orders o');
//        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
//        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
//        $this->db->join('v_itemsearch v', 'v.item_id=oi.item_id');
//        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=toi.inventory_color_id');
//        $this->db->where('o.print_date >= ', $daybgn);
//        $this->db->where('o.print_date < ', $dayend);
//        $this->db->where('o.is_canceled',0);
//        $this->db->where('o.shipped_date',0);
//        if ($brand !== 'ALL') {
//            if ($brand=='SR') {
//                $this->db->where('o.brand', $brand);
//            } else {
//                $this->db->where_in('o.brand', ['SB','BT']);
//            }
//        }
//        $this->db->order_by('o.plates_ready asc, o.order_rush desc, o.order_id asc'); // $this->db->order_by('o.order_rush desc, o.order_num');
//        $amnts = $this->db->get()->result_array();
//        foreach ($amnts as $amnt) {
//            $this->db->select('sum(if(i.imprint_item=1, 1, 0)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
//            $this->db->from('ts_order_imprints i');
//            $this->db->where('i.order_item_id', $amnt['order_item_id']);
//            $imprdet = $this->db->get()->row_array();
//            $amnt['item_name'] = $amnt['item_number'].' - '.$amnt['item_description'];
//            $amnt['order_class'] = ($amnt['plates_ready'] == 0) ? '' : 'inwork';
//            $plates[] = [
//                'order_id' => $amnt['order_id'],
//                'plates_ready' => $amnt['plates_ready'],
//                'order_num' => $amnt['order_num'],
//                'plates_qty' => intval($imprdet['imprints']),
//                'item_name' => $amnt['item_name'],
//                'item_color' => $amnt['item_color'],
//                'order_class' => $amnt['order_class'],
//                'brand' => $amnt['brand'],
//            ];
//        }
//        return $plates;
//    }

    public function get_approved_proofs($order_id, $type)
    {
        if ($type=='order') {
            $this->db->select('ap.*')->from('ts_artwork_proofs ap')->join('ts_artworks ta','ta.artwork_id = ap.artwork_id');
            $this->db->where('ta.order_id', $order_id)->where('ap.approved_time > 0');
        } else {
            $this->db->select('ap.*')->from('ts_artwork_proofs ap')->join('ts_artworks ta','ta.artwork_id = ap.artwork_id');
            $this->db->join('ts_order_items oi','oi.order_id = ta.order_id')->join('ts_order_itemcolors oic','oic.order_item_id = oi.order_item_id');
            $this->db->where('oic.order_itemcolor_id', $order_id)->where('ap.approved_time > 0');
        }
        $proofs = $this->db->get()->result_array();
        $out = [];
        $approvenum=1;
        foreach ($proofs as $proof) {
            $out[] = [
                'src' => $proof['proof_name'],
                'out_apprname' => 'approved_'.str_pad($approvenum, 2, '0', STR_PAD_LEFT),
                'approve_class' => 'proofapproved',
                'out_approved' => '<img src="/img/art/artpopup_greenstar.png" alt="proof"/>',
            ];
            $approvenum++;
        }
        return $out;
    }

    private function _inventory_blank_outcome($order_itemcolor_id, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Error during Save Amount'];
        $this->db->select('*')->from('ts_order_itemcolors')->where('order_itemcolor_id', $order_itemcolor_id);
        $itemcolor = $this->db->get()->row_array();
        if (!empty($itemcolor['inventory_color_id'])) {
            $this->db->select('oic.order_itemcolor_id, oic.print_date colorprint, oic.print_completed, oic.item_qty, o.order_id, o.order_num, o.order_date, o.customer_name, o.print_date')->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi','oic.order_item_id = oi.order_item_id')->join('ts_orders o','oi.order_id = o.order_id')->where('oic.order_itemcolor_id', $order_itemcolor_id);
            $orderdata = $this->db->get()->row_array();
            $inventory_color_id = $itemcolor['inventory_color_id'];
            $shipped = $itemcolor['item_qty'];
            $kepted = $misprint = $plates = 0;
            $this->db->select('count(amount_id) as cnt, sum(shipped) as shipped')->from('ts_order_amounts')->where('order_itemcolor_id', $order_itemcolor_id);
            $amnt = $this->db->get()->row_array();
            if ($amnt['cnt']>0) {
                $shipped = $shipped - $amnt['shipped'];
            }
            if ($shipped > 0) {
                $this->load->model('inventory_model');
                $diff = intval($shipped) + intval($kepted) + intval($misprint);
                $balance = $this->inventory_model->inventory_color_income($inventory_color_id)-$this->inventory_model->inventory_color_outcome($inventory_color_id);
                $newbalance = $balance-$diff;
                if ($newbalance < 0) {
                    $out['msg'] = 'Enter Other QTY or Increase Income, or Choose other Inventory item';
                    return $out;
                }
                $orderdata = $this->_prepare_amount_save($orderdata, $inventory_color_id, $shipped, $kepted, $misprint, $plates);
                $amountres = $this->_save_amount($orderdata, $user_id);
                $out['msg'] = $amountres['msg'];
                if ($amountres['result']==$this->success_result) {
                    $cogflag = $this->error_result;
                    $cogres = $this->inventory_model->_update_ordercog($orderdata['order_id']);
                    if ($cogres['result']==$this->success_result) {
                        $cogflag = $this->success_result;
                    } else {
                        $out['msg'] = $cogres['msg'];
                    }
                    if ($cogflag==$this->success_result) {
                        $out['result']=$this->success_result;
                        $out['order_id']=$orderdata['order_id'];
                        $out['printshop_income_id']=$amountres['amount_id'];
                        $out['printdate'] = date('Y-m-d', $orderdata['print_date']);
                        // Update print_date & print_completed
                        $passed = $this->_completed_itemcolor($orderdata['order_id'], $inventory_color_id);
                        $print_compl = 0;
                        if (intval($passed) >= intval($orderdata['item_qty'])) {
                            $print_compl = 1;
                        }
                        $this->db->where('order_itemcolor_id', $orderdata['order_itemcolor_id']);
                        $this->db->set('print_completed', $print_compl);
                        if ($orderdata['colorprint']==0) {
                            $this->db->set('print_date', time());
                        }
                        $this->db->update('ts_order_itemcolors');
                    }
                }

            }

        }
        return $out;
    }

}