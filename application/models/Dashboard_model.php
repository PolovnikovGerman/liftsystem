<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Dashboard_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_totals($dayview, $weeknum=0, $year=0) {
        if ($dayview =='day') {
            $options = [
                'conversions' => 45,
                'sales' => 16,
                'revenue' => 5448,
                'reviews' => 450,
            ];
            $label = date('l, F j, Y');
        } else {
            $curweek=date('W');
            $curyear=date('Y');
            if ($curweek==$weeknum && $curyear==$year) {
                $weeknum = 0;
                $year = 0;
            }
            if ($weeknum==0) {
                // $label = 'ALL BRANDS THIS WEEK';
                $label = 'THIS WEEK';
                $weeknum = date('W');
                $year = date('Y');
                if (intval(date('m'))==12 && intval($weeknum)==1) {
                    $year = $year + 1;
                }
                $dates = getDatesByWeek($weeknum, $year);
                $nxtweek = 0;
                $nxtnavig = 0;
                $prvweek = $date = strtotime(date("Y-m-d", $dates['start_week']) . " -1 week");
                $prvnavig = 1;
            } else {
                $dates = getDatesByWeek($weeknum, $year);
                $dstart = $dates['start_week'];
                $dend = $dates['end_week'];
                $year = date('Y', $dstart);
                $weekname = '';
                if (date('M', $dstart) != date('M', $dend)) {
                    $weekname .= date('M', $dstart) . '/' . date('M', $dend);
                } else {
                    $weekname .= date('M', $dstart);
                }
                $weekname .= ' ' . date('j', $dstart) . '-' . date('j', $dend);
                $weekname .= ', ' . date('Y', $dend);
                $label = $weekname; // 'M '.date('M j', $dates['start_week']).' - S '.date('M j', $dates['end_week']).' '.$year;
                $nxtweek = strtotime(date("Y-m-d", $dates['start_week']) . " +1 week");;
                $nxtnavig = 1;
                $prvweek = strtotime(date("Y-m-d", $dates['start_week']) . " -1 week");
                $prvnavig = 1;
            }
            $this->db->select('count(order_id) as cnt, sum(revenue) as revenue');
            $this->db->from('ts_orders');
            $this->db->where('order_date >= ', $dates['start_week']);
            $this->db->where('order_date < ', $dates['end_week']);
            $this->db->where('is_canceled',0);
            $res = $this->db->get()->row_array();
            $visitors = 0;
            $reviews = 0;
            if ($this->config->item('test_server')==1) {
                $dates['end_week'] = strtotime('05/18/2024');
                $dates['start_week'] = strtotime('05/11/2024');
                $visitors = ceil(rand(15000, 35000));
                $reviews = ceil(rand(120, 350));
            }
            $leads = $this->db->select('count(lead_id) as leadcnt')->from('ts_leads')->where('unix_timestamp(update_date) >= ', $dates['start_week'])->where('unix_timestamp(update_date) < ', $dates['end_week'])->get()->row_array();
                $options = [
                    'conversions' => 204,
                    'sales' => $res['cnt'],
                    'revenue' => empty($res['revenue']) ? 0 : $res['revenue'],
                    'start_week' => $dates['start_week'],
                    'end_week' => $dates['end_week'],
                    'label' => $label,
                    'currweek' => $dates['start_week'],
                    'prev_week' => $prvweek,
                    'prev_navig' => $prvnavig,
                    'next_week' => $nxtweek,
                    'next_navig' => $nxtnavig,
                    'leads' => $leads['leadcnt'],
                    'visitors' => $visitors,
                    'reviews' => $reviews,
                ];
//            }
        }
        return ['data'=>$options, 'label'=>$label];
    }

    public function get_totals_brand($resulttype = 'totals', $curweek = 0) {
        if ($curweek==0) {
            $weeknum = date('W');
            $year = date('Y');
            if (intval(date('m'))==12 && intval($weeknum)==1) {
                $year = $year + 1;
            }
        } else {
            $weeknum = date('W', $curweek);
            $year = date('Y', $curweek);
            if (intval(date('m',$curweek))==12 && intval($weeknum)==1) {
                $year = $year + 1;
            }
        }
        $dates = getDatesByWeek($weeknum, $year);
        // $label = 'M '.date('M j', $dates['start_week']).' - S '.date('M j', $dates['end_week']).' '.$year;
        // Batches sql
        $this->db->select('order_id, count(batch_id) cnt, sum(batch_amount) paysum');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_term',0);
        $this->db->group_by('order_id');
        $batchsql = $this->db->get_compiled_select();

        $this->db->select('o.brand, count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(b.paysum) as paysum');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $dates['start_week']);
        $this->db->where('o.order_date < ', $dates['end_week']);
        $this->db->where('o.is_canceled',0);
        $this->db->join("({$batchsql}) b",'b.order_id=o.order_id','left');
        $this->db->group_by('brand');
        $res = $this->db->get()->result_array();
        $sbtotal = $srtotal = 0;
        $sbpaym = $srpaym = 0;
        $sborders = $srorders = 0;
        foreach ($res as $row) {
            if ($row['brand']=='SR') {
                $srtotal+=$row['revenue'];
                $srorders+=$row['cnt'];
                $srpaym+=floatval($row['paysum']);
            } else {
                $sbtotal+=$row['revenue'];
                $sborders+=$row['cnt'];
                $sbpaym+=floatval($row['paysum']);
            }
        }
        // Temporary -
        if ($srtotal==0 && $sbtotal==0 && $this->config->item('test_server')==1) {
            $srtotal = 5204;
            $sbtotal = 17405;
            $srpaym = 1522.96;
            $sbpaym = 9872.13;
        }
        if ($sborders==0 && $srorders==0 && $this->config->item('test_server')==1) {
            $sborders = 32;
            $srorders = 20;
        }
        $out = [];
        if ($resulttype=='totals') {
            $totalrevenue = $srtotal + $sbtotal;
            $totalpaym  = $srpaym + $sbpaym;
            $totalunpaid = $totalrevenue - $totalpaym;
            $out = [
                'sbtotal' => $sbtotal,
                'srtotal' => $srtotal,
                'alltotal' => $totalrevenue,
                'sbrevenueperc' => 0,
                'srrevenueperc' => 0,
                'sbpayment' => $sbpaym,
                'srpayment' => $srpaym,
                'allpayment' => $totalpaym,
                'sbpaymentperc' => 0,
                'srpaymentperc' => 0,
                'allpaymentperc' => 0,
                'sbunpaid' => $sbtotal - $sbpaym,
                'srunpaid' => $srtotal - $srpaym,
                'allunpaid' => $totalunpaid,
                'sbunpaidperc' => 0,
                'srunpaidperc' => 0,
                'allunpaidperc' => 0,
            ];
            if ($totalrevenue != 0) {
                $out['sbrevenueperc'] = round($sbtotal/$totalrevenue*100,1);
                $out['srrevenueperc'] = round($srtotal/$totalrevenue*100,1);
                $out['allpaymentperc'] = round($totalpaym / $totalrevenue * 100,1);
                $out['allunpaidperc'] = round($totalunpaid / $totalrevenue * 100, 1);
            }
            if ($sbtotal != 0 ) {
                $out['sbpaymentperc'] = round($sbpaym / $sbtotal * 100, 1);
                $out['sbunpaidperc'] = round($out['sbunpaid'] / $sbtotal * 100,1);
            }
            if ($srtotal != 0 ) {
                $out['srpaymentperc'] = round($srpaym / $srtotal * 100, 1);
                $out['srunpaidperc'] = round($out['srunpaid'] / $srtotal * 100, 1);
            }
//                ''
//            ];
//            $out[] = ['label' => 'Stress Balls', 'value' => MoneyOutput($sbtotal,0)];
//            $out[] = ['label' => 'Stress Relievers', 'value' => MoneyOutput($srtotal,0)];
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

    public function get_leadvisits_week($curweek)
    {
        $weeknum = date('W', $curweek);
        $year = date('Y', $curweek);
        $dates = getDatesByWeek($weeknum, $year);
        // Temporary
        if ($this->config->item('test_server')==1) {
            $dates['start_week'] = strtotime('2024-05-11');
            $dates['end_week'] = strtotime('2024-05-18');
        }
        $srleads = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ];
        $srleadtotal = 0;
        $btleads = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ];
        $btleadtotal = 0;
        $leaddats = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ];
        $this->db->select('brand, date_format(update_date,\'%w\') as dayweek, count(lead_id) as cnt')->from('ts_leads')->where('unix_timestamp(update_date) >= ', $dates['start_week'])->where('unix_timestamp(update_date) <= ', $dates['end_week'])->group_by('brand, dayweek');
        $leads = $this->db->get()->result_array();
        foreach ($leads as $lead) {
            if ($lead['brand']=='SR') {
                $srleadtotal+=$lead['cnt'];
                $srleads[$lead['dayweek']]+=$lead['cnt'];
            } else {
                $btleadtotal+=$lead['cnt'];
                $btleads[$lead['dayweek']]+=$lead['cnt'];
            }
            $leaddats[$lead['dayweek']]+=$lead['cnt'];
        }
        $srvisittotal = 0;
        $srvisits = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ];
        $btvisittotal = 0;
        $btvisits = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ];
        $visitors = [
            '0' => 0,
            '1' => 0,
            '2' => 0,
            '3' => 0,
            '4' => 0,
            '5' => 0,
            '6' => 0,
        ];
        if ($this->config->item('test_server')==1) {
            for ($i=0; $i<7; $i++) {
                $btvisits[$i] = ceil(rand(500,2000));
                $btvisittotal+=$btvisits[$i];
                $srvisits[$i] = ceil(rand(500,2000));
                $srvisittotal+=$srvisits[$i];
                $visitors[$i]+=$srvisits[$i]+$btvisits[$i];
            }
        }
        $out = [
            'btleads' => $btleads,
            'btleadtotal' => $btleadtotal,
            'srleads' => $srleads,
            'srleadtotal' => $srleadtotal,
            'leadtotal' => $btleadtotal+$srleadtotal,
            'leads' => $leaddats,
            'btvisits' => $btvisits,
            'btvisittotal' => $btvisittotal,
            'srvisits' => $srvisits,
            'srvisittotal' => $srvisittotal,
            'visittotal' => $btvisittotal+$srvisittotal,
            'visitors' => $visitors,

        ];
        return $out;
    }
}
?>