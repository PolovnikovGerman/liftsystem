<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Test_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function custom_orders_table($year_bgn, $year_end=0) {
        $datestart = strtotime($year_bgn.'-01-01');
        if ($year_end != 0) {
            $yearstart = strtotime($year_end.'-01-01');
            $datefinish = strtotime(date('Y-m-d', $yearstart).' +1 year');
        }
        $this->db->select('o.order_id, o.order_num, o.order_date, o.customer_name as customer, o.revenue, o.shipping, o.profit, o.order_qty, GROUP_CONCAT(oic.item_description) as itemname');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id = o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->where('o.order_date >= ', $datestart);
        if ($year_end > 0) {
            $this->db->where('o.order_date < ', $datefinish);
        }
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.item_id', $this->config->item('custom_id'));
        $this->db->group_by('o.order_id');
        $orders = $this->db->get()->result_array();
        $idx = 0;
        foreach ($orders as $order) {
            for ($i=1; $i<4; $i++) {
                $orders[$idx]['po'.$i.'_amnt']='';
                $orders[$idx]['po'.$i.'_vendor']='';
            }
            $this->db->select('oa.order_id, v.vendor_name as vendor, count(oa.amount_id) as cnt, sum(oa.amount_sum) as total');
            $this->db->from('ts_order_amounts oa');
            $this->db->join('vendors v', 'v.vendor_id = oa.vendor_id');
            $this->db->where('oa.order_id', $order['order_id']);
            $this->db->group_by('oa.order_id, v.vendor_name');
            $amnts = $this->db->get()->result_array();
            $i = 1;
            foreach ($amnts as $amnt) {
                $orders[$idx]['po'.$i.'_amnt'] = $amnt['total'];
                $orders[$idx]['po'.$i.'_vendor'] = $amnt['vendor'];
                $i++;
            }
            $idx++;
        }
        return $orders;
    }
}