<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Dashboard_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_totals($dayview) {
        if ($dayview =='day') {
            $options = [
                'conversions' => 45,
                'sales' => 16,
                'revenue' => 5448,
            ];
            $label = date('l, F j, Y');
        } else {
            $weeknum = date('W');
            $year = date('Y');
            $dates = getDatesByWeek($weeknum, $year);
            $label = 'M '.date('M j', $dates['start_week']).' - S '.date('M j', $dates['end_week']).' '.$year;
            $this->db->select('count(order_id) as cnt, sum(revenue) as revenue');
            $this->db->from('ts_orders');
            $this->db->where('order_date >= ', $dates['start_week']);
            $this->db->where('order_date < ', $dates['end_week']);
            $this->db->where('is_canceled',0);
            $res = $this->db->get()->row_array();
            if ($res['cnt']==0) {
                $options = [
                    'conversions' => 204,
                    'sales' => 0,
                    'revenue' => 0,
                ];
            } else {
                $options = [
                    'conversions' => 204,
                    'sales' => $res['cnt'],
                    'revenue' => $res['revenue'],
                ];
            }
        }
        return ['data'=>$options, 'label'=>$label];
    }
}
?>