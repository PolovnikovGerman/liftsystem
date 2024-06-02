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

    public function get_totals_brand($resulttype = 'totals') {
        $weeknum = date('W');
        $year = date('Y');
        $dates = getDatesByWeek($weeknum, $year);
        $label = 'M '.date('M j', $dates['start_week']).' - S '.date('M j', $dates['end_week']).' '.$year;
        $this->db->select('brand, count(order_id) as cnt, sum(revenue) as revenue');
        $this->db->from('ts_orders');
        $this->db->where('order_date >= ', $dates['start_week']);
        $this->db->where('order_date < ', $dates['end_week']);
        $this->db->where('is_canceled',0);
        $this->db->group_by('brand');
        $res = $this->db->get()->result_array();
        $sbtotal = $srtotal = 0;
        $sborders = $srorders = 0;
        foreach ($res as $row) {
            if ($row['brand']=='SR') {
                $srtotal+=$row['revenue'];
                $srorders+=$row['cnt'];
            } else {
                $sbtotal+=$row['revenue'];
                $sborders+=$row['cnt'];
            }
        }
        // Temporary -
        if ($srtotal==0 && $sbtotal==0 && $this->config->item('test_server')==1) {
            $srtotal = 5204;
            $sbtotal = 17405;
        }
        if ($sborders==0 && $srorders==0 && $this->config->item('test_server')==1) {
            $sborders = 32;
            $srorders = 20;
        }
        $out = [];
        if ($resulttype=='totals') {
            $out[] = ['label' => 'Stress Balls', 'value' => MoneyOutput($sbtotal,0)];
            $out[] = ['label' => 'Stress Relievers', 'value' => MoneyOutput($srtotal,0)];
        } else {
            $out[] = ['label' => 'Stress Balls', 'value' => QTYOutput($sborders,0)];
            $out[] = ['label' => 'Stress Relievers', 'value' => QTYOutput($srorders,0)];
        }
        return $out;
    }

    public function get_total_balance()
    {
        // $this->db->select('customer_name, sum(balance) as debettotal')->from('v_order_balances')->group_by('customer_name')->having('debettotal > 0')->order_by('debettotal','desc');
        $this->db->select('brand, sum(balance) as debettotal')->from('v_order_balances')->where('balance > ',0)->group_by('brand')->order_by('brand');
        // where yearorder >= 2021
        $results = $this->db->get()->result_array();
        $srtotal = $sbtotal = 0;
        foreach ($results as $result) {
            if ($result['brand']=='SR') {
                $srtotal+=$result['debettotal'];
            } else {
                $sbtotal+=$result['debettotal'];
            }
        }
        $out = [];
        $out[] = ['label' => 'Stress Balls', 'value' => MoneyOutput($sbtotal,0)];
        $out[] = ['label' => 'Stress Relievers', 'value' => MoneyOutput($srtotal,0)];
        return $out;
    }

    public function get_debt_totals()
    {
        $this->db->select('count(order_id) cntdebt, sum(balance) as debettotal')->from('v_order_balances')->where('balance > ',0);
        // where yearorder >= 2021
        $res = $this->db->get()->row_array();
        if ($res['cntdebt']==0) {
            return 0;
        } else {
            return $res['debettotal'];
        }
    }

}
?>