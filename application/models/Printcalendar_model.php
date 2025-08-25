<?php

class Printcalendar_model extends MY_Model
{
    function __construct() {
        parent::__construct();
    }

    public function get_printshops_years($limit=10) {
        $this->db->select('DATE_FORMAT(FROM_UNIXTIME(o.print_date),\'%Y\') as yearprint, count(o.order_id)');
        $this->db->from('ts_orders o');
//        $this->db->join('ts_order_items oi', 'oi.order_id = o.order_id');
//        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date is not null');
        $this->db->group_by('yearprint');
        $this->db->order_by('yearprint', 'desc');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function build_calendar($start_year, $year)
    {
        if ($year==date('Y')) {
            $this->db->select('max(o.print_date) as printdate')->from('ts_orders o')->where('o.is_canceled', 0)->where('o.print_date is not null');
            $res = $this->db->get()->row_array();
            $finish_year = $res['printdate'];
        } else {
            $finish_year = strtotime($year.'-12-31');
        }
        // Get Neares Sunday
        $date = new DateTime(date('Y-m-d', $finish_year));
        // Set the date to the last day of the current month
        $date->modify('last day of this month');
        // Get the day of the week for the last day of the month (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        $dayOfWeek = $date->format('w');
        // If the last day of the month is not already a Sunday, modify the date to be the last Sunday
        if ($dayOfWeek != 0) {
            $date->modify('last sunday');
        }
        $finish_year = strtotime($date->format('Y-m-d'));

        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $calend = [];
        while (1==1) {
            $week = [];
            $total_orders = $total_prints = $total_printed = 0;
            $date = new DateTime(date('Y-m-d',$start_year));
            $newdate = $date;
            $weestart = $newdate->getTimestamp();
            for ($i=0; $i<=6; $i++) {
                $active = 0;
                if ($newdate->getTimestamp() >= strtotime(date('Y-m-d'))) {
                    $active = 1;
                }
                $week[] = [
                    'date' => $newdate->getTimestamp(),
                    'month' => $newdate->format('M'),
                    'day' => $newdate->format('j'),
                    'orders' => 0,
                    'prints' => 0,
                    'printed' => 0,
                    'weekend' => ($newdate->format('w') > 0 && $newdate->format('w') < 6) ? 0 : 1,
                    'active' => $active,
                ];
                $newdate = $date->modify('+1 day');
            }
            $weekfinish = $newdate->getTimestamp();
            // Get data
            $this->db->select('o.print_date, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(COALESCE(impr.cntprint,0)*oic.item_qty) as printqty');
            $this->db->select('sum(amnt.fullfill) as fullfill');
            $this->db->from('ts_orders o');
            $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
            $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
            $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.print_date >= ', $weestart);
            $this->db->where('o.print_date < ', $weekfinish);
            $this->db->group_by('o.print_date');
            $results = $this->db->get()->result_array();
            foreach ($results as $result) {
                $idx = 0;
                foreach ($week as $w) {
                    if ($w['date'] == $result['print_date']) {
                        $week[$idx]['ordercnt'] = $result['ordercnt'];
                        $week[$idx]['prints'] = $result['printqty'];
                        $week[$idx]['printed'] = $result['fullfill'];
                        break;
                    } else {
                        $idx++;
                    }
                }
                $total_orders+=$result['ordercnt'];
                $total_prints+=$result['printqty'];
                $total_printed+=$result['fullfill'];
            }
            $start_year = $newdate->getTimestamp();
            $calend[] = [
                'week' => $week,
                'total_orders' => $total_orders,
                'total_prints' => $total_prints,
                'total_printed' => $total_printed,
                'total_toprint' => ($total_prints - $total_printed),
            ];
            if ($start_year >= $finish_year) {
                break;
            }
        }
        return $calend;
    }

    public function year_statistic($year)
    {
        $start_year = strtotime($year.'-01-01');
        $end_year = strtotime(($year+1).'-01-01');
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $curdate = new DateTime(date('Y-m-d'));
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(COALESCE(impr.cntprint,0)*oi.item_qty) as printqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date < ', $curdate->getTimestamp());
        $this->db->where('o.shipped_date',0);
        $lateres = $this->db->get()->row_array();
        // Year schedule
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(COALESCE(impr.cntprint,0)*oi.item_qty) as printqty, sum(impr.imprintqty) as imprintqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date >= ', $start_year);
        $this->db->where('o.print_date < ', $end_year);
        $statres = $this->db->get()->row_array();
        // Year printed
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(COALESCE(impr.cntprint,0)*oic.item_qty) as printqty, sum(impr.imprintqty) as imprintqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date >= ', $start_year);
        $this->db->where('o.print_date < ', $end_year);
        $this->db->where('coalesce(amnt.fullfill,0) >= oic.item_qty');
        $readyres = $this->db->get()->row_array();

        return [
            'late' => $lateres,
            'total_orders' => $statres['ordercnt'],
            'total_items' => $statres['imprintqty'],
            'total_prints' => $statres['printqty'],
            'leave_orders' => $statres['ordercnt'] - $readyres['ordercnt'],
            'leave_items' => $statres['imprintqty'] - $readyres['imprintqty'],
            'leave_prints' => $statres['printqty'] - $readyres['printqty'],
            'year' => $year,
        ];

    }
}