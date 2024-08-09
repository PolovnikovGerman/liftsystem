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

}