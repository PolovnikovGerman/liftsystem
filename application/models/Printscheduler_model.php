<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Printscheduler_model extends MY_Model
{
    function __construct() {
        parent::__construct();
    }

    public function get_printsheduler_totals($brand)
    {
        $curdate = strtotime(date('Y-m-d'));
        $this->db->select('count(distinct(o.order_id)) as cntorder, sum(oi.item_qty) as totalitems, sum(imp.imprint_qty) as totalimpr');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','oi.order_id=o.order_id');
        $this->db->join('ts_order_imprints imp','imp.order_item_id=oi.order_item_id');
        $this->db->where('o.print_date < ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->where('imp.imprint_item',1);
        $oldres = $this->db->get()->row_array();
        $this->db->select('count(distinct(o.order_id)) as cntorder, sum(oi.item_qty) as totalitems, sum(imp.imprint_qty) as totalimpr');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','oi.order_id=o.order_id');
        $this->db->join('ts_order_imprints imp','imp.order_item_id=oi.order_item_id');
        $this->db->where('o.print_date >= ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->where('imp.imprint_item',1);
        $newres = $this->db->get()->row_array();
        return [
            'old_orders' => $oldres['cntorder'],
            'old_items' => intval($oldres['totalitems']),
            'old_prints' => intval($oldres['totalimpr']),
            'new_orders' => $newres['cntorder'],
            'new_items' => intval($newres['totalitems']),
            'new_prints' => intval($newres['totalimpr']),
            'brand' => $brand,
        ];
    }

    public function get_pastorders($brand)
    {
        $curdate = strtotime(date('Y-m-d'));
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush');
        $this->db->from('ts_orders o');
        $this->db->where('o.print_date < ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->order_by('o.order_rush desc, o.print_date');
        $orders = $this->db->get()->result_array();
        $res = [];
        foreach ($orders as $order) {
            // Item Name, Item Color
            $this->db->select('group_concat(v.item_number,\'-\', toi.item_description) as itemdescr, group_concat(toi.item_color) as itemcolor');
            $this->db->from('ts_order_items i');
            $this->db->join('ts_order_itemcolors toi','i.order_item_id = toi.order_item_id');
            $this->db->join('v_itemsearch v', 'v.item_id=i.item_id');
            $this->db->where('i.order_id', $order['order_id']);
            $itemdet = $this->db->get()->row_array();
            // Imprints
            $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprint, sum(if(i.imprint_item=1, i.imprint_qty, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->join('ts_order_items toi','i.order_item_id = toi.order_item_id');
            $this->db->where('toi.order_id', $order['order_id']);
            $imprdet = $this->db->get()->row_array();
            $order['item_name'] = $itemdet['itemdescr'];
            $order['item_color'] = $itemdet['itemcolor'];
            $order['imprint'] = intval($imprdet['imprint']);
            $order['imprint_qty'] = intval($imprdet['imprqty']);
            $res[] = $order;
        }
        return $res;
    }

    public function get_ontimeorders_dates($brand)
    {
        $curdate = strtotime(date('Y-m-d'));
        $this->db->select('date_format(from_unixtime(o.print_date), \'%Y-%m-%d\') as printdate, count(distinct(o.order_id)) as cntorder, sum(imp.imprint_qty) as totalimpr');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','oi.order_id=o.order_id');
        $this->db->join('ts_order_imprints imp','imp.order_item_id=oi.order_item_id');
        $this->db->where('o.print_date >= ', $curdate);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->where('imp.imprint_item',1);
        $this->db->group_by('printdate');
        $this->db->order_by('printdate');
        $newres = $this->db->get()->result_array();
        $newresidx = 0;
        foreach ($newres as $newitem) {
            $daybgn = strtotime($newitem['printdate']);
            $dayend = strtotime('+1 day', $daybgn);
            $this->db->select('sum(oi.item_qty) as totalitem');
            $this->db->from('ts_orders o');
            $this->db->join('ts_order_items oi','oi.order_id=o.order_id');
            $this->db->where('o.print_date >= ', $daybgn);
            $this->db->where('o.print_date < ', $dayend);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.print_finish',null);
            if ($brand=='SR') {
                $this->db->where('o.brand', $brand);
            } else {
                $this->db->where_in('o.brand', ['SB','BT']);
            }
            $itemdat = $this->db->get()->row_array();
            $newres[$newresidx]['totalitems'] = intval($itemdat['totalitem']);
            $newresidx++;
        }
        return $newres;
    }

    public function get_ontimeorders_day($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);

        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush');
        $this->db->from('ts_orders o');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $res = [];
        foreach ($orders as $order) {
            // Item Name, Item Color
            $this->db->select('group_concat(v.item_number,\'-\', toi.item_description) as itemdescr, group_concat(toi.item_color) as itemcolor');
            $this->db->from('ts_order_items i');
            $this->db->join('ts_order_itemcolors toi','i.order_item_id = toi.order_item_id');
            $this->db->join('v_itemsearch v', 'v.item_id=i.item_id');
            $this->db->where('i.order_id', $order['order_id']);
            $itemdet = $this->db->get()->row_array();
            // Imprints
            $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprint, sum(if(i.imprint_item=1, i.imprint_qty, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->join('ts_order_items toi','i.order_item_id = toi.order_item_id');
            $this->db->where('toi.order_id', $order['order_id']);
            $imprdet = $this->db->get()->row_array();
            $order['item_name'] = $itemdet['itemdescr'];
            $order['item_color'] = $itemdet['itemcolor'];
            $order['imprint'] = intval($imprdet['imprint']);
            $order['imprint_qty'] = intval($imprdet['imprqty']);
            $res[] = $order;
        }
        return $res;
    }

    public function get_dayorders($printdate, $brand)
    {
        $daybgn = strtotime($printdate);
        $dayend = strtotime('+1 day', $daybgn);
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready');
        $this->db->from('ts_orders o');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $stocks = $plates = [];
        foreach ($orders as $order) {
            $this->db->select('group_concat(v.item_number,\'-\', toi.item_description) as itemdescr, group_concat(toi.item_color) as itemcolor');
            $this->db->from('ts_order_items i');
            $this->db->join('ts_order_itemcolors toi','i.order_item_id = toi.order_item_id');
            $this->db->join('v_itemsearch v', 'v.item_id=i.item_id');
            $this->db->where('i.order_id', $order['order_id']);
            $itemdet = $this->db->get()->row_array();
            // Imprints
            $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprint, sum(if(i.imprint_item=1, i.imprint_qty, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->join('ts_order_items toi','i.order_item_id = toi.order_item_id');
            $this->db->where('toi.order_id', $order['order_id']);
            $imprdet = $this->db->get()->row_array();
            $order['item_name'] = $itemdet['itemdescr'];
            $order['item_color'] = $itemdet['itemcolor'];
            $order['imprint'] = intval($imprdet['imprint']);
            $order['imprint_qty'] = intval($imprdet['imprqty']);
            $stocks[] = $order;
            // Get plates
            $this->db->select('count(amount_id) as platescnt,  sum(blueplate+orangeplate+beigeplate) as platessum')->from('ts_order_amounts')->where('order_id', $order['order_id']);
            $platedet = $this->db->get()->row_array();
            if ($platedet['platescnt'] > 0) {
                $plates[] = [
                    'order_id' => $order['order_id'],
                    'print_ready' => $order['print_ready'],
                    'order_num' => $order['order_num'],
                    'plates_qty' => $platedet['platessum'],
                    'item_name' => $itemdet['itemdescr'],
                    'item_color' => $itemdet['itemcolor'],
                ];
            }
        }
        return [
            'stocks' => $stocks,
            'plates' => $plates,
        ];
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
        $this->db->where('o.print_finish',null);
        $this->db->where('o.print_ready > ', 0);
        $this->db->where('o.shipping_ready',0);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->order_by('o.print_user, o.order_rush desc, o.order_num');
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
        $this->db->select('count(o.order_id) as cnt');
        $this->db->from('ts_orders o');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        $this->db->where('o.print_ready > ', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.print_user', null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $ordercnt = $this->db->get()->row_array();
        // get order details
        $this->db->select('o.order_id, o.order_num, o.shipdate, o.order_qty, o.order_rush, o.print_ready, oi.order_item_id');
        $this->db->select('v.item_number, toi.item_description, toi.item_color, toi.item_qty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors toi','oi.order_item_id=toi.order_item_id');
        $this->db->join('v_itemsearch v', 'v.item_id=oi.item_id');
        $this->db->where('o.print_date >= ', $daybgn);
        $this->db->where('o.print_date < ', $dayend);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.print_finish',null);
        $this->db->where('o.print_ready > ', 0);
        $this->db->where('o.shipping_ready',0);
        $this->db->where('o.print_user', null);
        if ($brand=='SR') {
            $this->db->where('o.brand', $brand);
        } else {
            $this->db->where_in('o.brand', ['SB','BT']);
        }
        $this->db->order_by('o.order_rush desc, o.order_num');
        $orders = $this->db->get()->result_array();
        $unassign = [];
        $totals = [
            'prints' => 0,
            'items' => 0,
            'orders' => $ordercnt['cnt'],
        ];
        $ordernum = '';
        foreach ($orders as $order) {
            $totals['items']+=$order['item_qty'];
            // Imprints
            $this->db->select('sum(if(i.imprint_item=1, 1, i.imprint_qty)) as imprints, sum(if(i.imprint_item=1, 1, 0)) as imprqty');
            $this->db->from('ts_order_imprints i');
            $this->db->where('i.order_item_id', $order['order_item_id']);
            $imprdet = $this->db->get()->row_array();
            $order['imprints'] = $imprdet['imprints'];
            $order['prints'] = $imprdet['imprqty']*$order['item_qty'];
            $totals['prints']+=$imprdet['imprqty']*$order['item_qty'];
            $order['item_name'] = $order['item_number'].' - '.$order['item_description'];
            $unassign[] = $order;
        }
        return [
            'orders' => $unassign,
            'totals' => $totals,
        ];
    }

    public function stockdonecheck($order_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];
        $this->db->select('order_id, print_ready, print_date')->from('ts_orders')->where('order_id', $order_id);
        $orderres = $this->db->get()->row_array();
        if (ifset($orderres,'order_id',0)==$order_id) {
            $out['result'] = $this->success_result;
            $out['printdate'] = date('Y-m-d',$orderres['print_date']);
            $this->db->where('order_id', $order_id);
            if ($orderres['print_ready']==0) {
                $this->db->set('print_ready',time());
            } else {
                $this->db->set('print_ready',0);
            }
            $this->db->update('ts_orders');
        }
        return $out;
    }

}