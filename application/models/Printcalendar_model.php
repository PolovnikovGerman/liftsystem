<?php

class Printcalendar_model extends MY_Model
{
    public $amntsql;
    public $printsql;
    public $proofsql;
    public $shipsql;
    public $showweeks_perpage = 13;
    public $empty_content = '&nbsp;';

    function __construct() {
        parent::__construct();
        // $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, max(amount_date) as amount_date, sum(amount_sum) as amount_sum, sum(misprint) as misprint , sum(kepted) as kepted, sum(orangeplate+blueplate+beigeplate) as plates, sum(printshop_total) as printshop_total')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, max(amount_date) as amount_date, sum(shipped+misprint+kepted) as amount_sum, sum(misprint) as misprint , sum(kepted) as kepted, sum(orangeplate+blueplate+beigeplate) as plates, sum(shipped+misprint+kepted) as printshop_total')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $this->amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $this->printsql = $this->db->get_compiled_select();
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt')->from('ts_artworks a')->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id')->where('p.approved > ',0)->group_by('a.order_id');
        $this->proofsql = $this->db->get_compiled_select();
        $this->db->select('order_itemcolor_id, sum(qty) as shipped')->from('ts_order_trackings')->group_by('order_itemcolor_id');
        $this->shipsql = $this->db->get_compiled_select();

    }

    public function get_printshops_years($limit=10) {
        $this->db->select('DATE_FORMAT(FROM_UNIXTIME(o.print_date),\'%Y\') as yearprint, count(o.order_id)');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
        $this->db->join('ts_order_itemcolors oic','oic.order_item_id=oi.order_item_id');
        $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('coalesce(o.print_date,0) > 0');
        $this->db->group_by('yearprint');
        $this->db->order_by('yearprint', 'desc');
        $this->db->limit($limit);
        return $this->db->get()->result_array();
    }

    public function build_calendar($start_year, $year)
    {
        if ($year>=date('Y')) {
            $yearlimit = strtotime($year.'-12-31');
            $this->db->select('max(o.print_date) as printdate')->from('ts_orders o');
            $this->db->join('ts_order_items oi','o.order_id=oi.order_id');
            $this->db->join('ts_order_itemcolors oic','oic.order_item_id=oi.order_item_id');
            $this->db->join('ts_inventory_colors ic','ic.inventory_color_id=oic.inventory_color_id');
            $this->db->where('o.is_canceled', 0)->where('o.print_date is not null')->where('o.print_date <=', $yearlimit);
            $res = $this->db->get()->row_array();
            $finish_year = $res['printdate'];
        } else {
            $finish_year = strtotime($year.'-12-31');
        }
        // Get Neares Sunday
        $date = new DateTime(date('Y-m-d', $finish_year));
        // Set the date to the last day of the current month
        $date->modify('sunday this week');
        // Get the day of the week for the last day of the month (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
        $finish_year = strtotime($date->format('Y-m-d'));

        $calend = [];
        $weektotals = [];
        $widx = 1;
        $current_date = strtotime(date('Y-m-d'));
        while (1==1) {
            $week = [];
            $total_orders = $total_prints = $total_printed = $total_items = 0;
            $date = new DateTime(date('Y-m-d',$start_year));
            $newdate = $date;
            $weekstart = $newdate->getTimestamp();
            for ($i=0; $i<=6; $i++) {
                $active = 0;
                if ($newdate->getTimestamp() >= strtotime(date('Y-m-d'))) {
                    $active = 1;
                }
                $week[] = [
                    'date' => $newdate->getTimestamp(),
                    'month' => $newdate->format('M'),
                    'day' => $newdate->format('j'),
                    'week' => $newdate->format('W-Y'),
                    'orders' => 0,
                    'items' => 0,
                    'prints' => 0,
                    'printed' => 0,
                    'weekend' => ($newdate->format('w') > 0 && $newdate->format('w') < 6) ? 0 : 1,
                    'active' => $active,
                ];
                $newdate = $date->modify('+1 day');
            }
            $weekfinish = $newdate->getTimestamp();
            $weeknumber = $newdate->format('W-Y');
            // Week content current date
            $readyweek = 0;
            if ($current_date >= $weekstart && $current_date <= $weekfinish) {
                $results = $this->_current_week_sum($weekstart, $weekfinish);
            } else {
                if ($weekstart >= $current_date) {
                    // new orders
                    $results = $this->_feature_week_sum($weekstart, $weekfinish);
                } else {
                    $readyweek = 1;
                    // old orders
                    $results = $this->_late_week_sum($weekstart, $weekfinish);
                }
            }
            // Get data
            $total_orders = $total_items = $total_prints = $total_printed = 0;
            foreach ($results as $result) {
                $idx = 0;
                foreach ($week as $w) {
                    if ($w['date'] == $result['print_date']) {
                        $week[$idx]['orders'] = $result['ordercnt'];
                        $week[$idx]['items'] = $result['itemscnt'];
                        $week[$idx]['prints'] = $result['printqty'];
                        $week[$idx]['printed'] = $result['fullfill'];
                        $total_orders+=$result['ordercnt'];
                        $total_items+=$result['itemscnt'];
                        $total_prints+=$result['printqty'];
                        $total_printed+=$result['fullfill'];
                        break;
                    } else {
                        $idx++;
                    }
                }
            }
            $start_year = $newdate->getTimestamp();
            $calend[] = [
                'week' => $week,
                'weeknum' => $widx,
                'showdata' => 1,
                'weeknumber' => $weeknumber,
            ];
            $weektotals[] = [
                'start' => $weekstart,
                'finish' => $weekfinish,
                'total_orders' => $total_orders,
                'total_items' => $total_items,
                'total_prints' => $total_prints,
                'total_printed' => $total_printed,
                'total_toprint' => ($total_prints - $total_printed),
                'readyweek' => $readyweek,
                'weeknum' => $widx,
                'showdata' => 1,
                'weeknumber' => $weeknumber,
            ];
            if ($start_year >= $finish_year) {
                break;
            }
            $widx++;
        }
        $minidx = 1;
        $maxidx = count($calend);
        if (count($calend)>$this->showweeks_perpage) {
            $idx = 0;
            $minidx = count($calend)-$this->showweeks_perpage-1;
            foreach ($calend as $c) {
                if ($idx<=$minidx) {
                    $calend[$idx]['showdata'] = 0;
                    $weektotals[$idx]['showdata'] = 0;
                }
                $idx++;
            }
        }
        return ['calend' => $calend, 'totals' => $weektotals, 'minweek' => $minidx, 'maxweek' => count($weektotals)];
    }

    private function _current_week_sum($weekstart, $weekfinish) {
        $curdate = strtotime(date('Y-m-d'));
        $daybgn = $weekstart;
        $week = [];
        for ($i=0; $i<=6; $i++) {
            $dayend = strtotime('+1 day', $daybgn);
            if ($daybgn >= $curdate) {
                $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty, sum(amnt.fullfill) as fullfill');
                $this->db->from('ts_order_itemcolors oic');
                $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
                $this->db->join('ts_orders o', 'o.order_id=oi.order_id');
                $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
                $this->db->join('(' . $this->amntsql . ') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id', 'left');
                $this->db->join('(' . $this->printsql . ') impr', 'impr.order_item_id = oi.order_item_id', 'left');
                $this->db->join('(' . $this->shipsql . ') ship', 'ship.order_itemcolor_id = oic.order_itemcolor_id');
                $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0,]);
                $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
            } else {
                $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, sum(shipped+misprint+kepted) as amount_sum');
                $this->db->from('ts_order_amounts');
                $this->db->where('amount_date >= ', $daybgn)->where('amount_date < ', $dayend);
                $this->db->group_by('order_itemcolor_id');
                $amntsql = $this->db->get_compiled_select();

                $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(amnt.fullfill) as itemscnt');
                $this->db->select('sum(coalesce(impr.cntprint,0)*coalesce(amnt.fullfill,0)) as printqty, sum(amnt.fullfill) as fullfill');
                $this->db->from('ts_order_itemcolors oic');
                $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
                $this->db->join('ts_orders o', 'oi.order_id = o.order_id');
                $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
                $this->db->join('ts_inventory_items ii','ii.inventory_item_id=oi.inventory_item_id');
                $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id'); // ,'left'
                $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
                $this->db->where('o.is_canceled', 0);
            }
            $res = $this->db->get()->row_array();
            $week[] = [
                'print_date' => $daybgn,
                'ordercnt' => $res['ordercnt'],
                'itemscnt' => $res['itemscnt'],
                'printqty' => $res['printqty'],
                'fullfill' => $res['fullfill'],
            ];
            $daybgn = $dayend;
        }
        return $week;
    }

    private function _feature_week_sum($weekstart, $weekfinish)
    {
        $this->db->select('oi.print_date, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty');
        $this->db->select('sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=oi.inventory_item_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('oi.print_date >= ', $weekstart);
        $this->db->where('oi.print_date < ', $weekfinish);
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->group_by('oi.print_date');
        $results = $this->db->get()->result_array();
        return $results;
    }

    private function _late_week_sum($weekstart, $weekfinish)
    {
        $this->db->select('order_itemcolor_id, amount_date as amntdate, sum(shipped) as fullfill, sum(shipped+misprint+kepted) as amount_sum');
        $this->db->from('ts_order_amounts');
        $this->db->where('amount_date >= ', $weekstart)->where('amount_date < ', $weekfinish);
        $this->db->group_by('order_itemcolor_id, amntdate');
        $amntsql = $this->db->get_compiled_select();

        $this->db->select('amnt.amntdate, count(distinct(o.order_id)) as ordercnt, sum(amnt.fullfill) as itemscnt');
        $this->db->select('sum(coalesce(impr.cntprint,0)*coalesce(amnt.fullfill,0)) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_orders o', 'oi.order_id = o.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=oi.inventory_item_id');
        $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->where('o.is_canceled', 0);
        $this->db->group_by('amnt.amntdate');
        $results = $this->db->get()->result_array();
        $out = [];
        $outkeys = [];
        foreach ($results as $result) {
            if (!empty($result['amntdate'])) {
                $print_date = strtotime(date('Y-m-d', $result['amntdate']));
                if (in_array($print_date, $outkeys)) {
                    $idx = array_search($print_date, $outkeys);
                } else {
                    array_push($outkeys, $print_date);
                    $out[] = [
                        'print_date' => $print_date,
                        'ordercnt' => 0,
                        'itemscnt' => 0,
                        'printqty' => 0,
                        'fullfill' => 0,
                    ];
                    $idx = count($outkeys) - 1;
                }
                $out[$idx]['ordercnt']+=$result['ordercnt'];
                $out[$idx]['itemscnt']+=$result['itemscnt'];
                $out[$idx]['printqty']+=$result['printqty'];
                $out[$idx]['fullfill']+=$result['fullfill'];
            }
        }
        return $out;
    }

    public function year_statistic($year)
    {
        $start_year = strtotime($year.'-01-01');
        $end_year = strtotime(($year+1).'-01-01');
        $curdate = new DateTime(date('Y-m-d'));
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty');
        $this->db->select('sum(coalesce(oic.item_qty,0)) as itemqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left'); //
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('ts_orders o', 'o.order_id=oi.order_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('oi.print_date > ', 0);
        $this->db->where('oi.print_date < ', $curdate->getTimestamp());
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $lateres = $this->db->get()->row_array();
        // Year schedule
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as printqty, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as imprintqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('ts_orders o', 'o.order_id=oi.order_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('oi.print_date >= ', $start_year);
        $this->db->where('oi.print_date < ', $end_year);
        $statres = $this->db->get()->row_array();
        // Year printed
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as printqty, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as imprintqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('ts_orders o','o.order_id=oi.order_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('oi.print_date >= ', $start_year);
        $this->db->where('oi.print_date < ', $end_year);
        $this->db->where('coalesce(amnt.fullfill,0) >= oic.item_qty');
        $readyres = $this->db->get()->row_array();

        return [
            'late' => $lateres,
            'total_orders' => $statres['ordercnt'],
            'total_items' => $statres['printqty'],
            'total_prints' => $statres['imprintqty'],
            'leave_orders' => $statres['ordercnt'] - $readyres['ordercnt'],
            'leave_prints' => $statres['imprintqty'] - $readyres['imprintqty'],
            'leave_items' => $statres['printqty'] - $readyres['printqty'],
            'year' => $year,
        ];
    }

    public function week_calendar($weeknumber, $year)
    {
        // Date Bgn / end
        $curdate = strtotime(date('Y-m-d'));
        $dates = getDatesByWeek($weeknumber, $year);
        $date = new DateTime(date('Y-m-d',$dates['start_week']));
        $newdate = $date;
        $weekstart = $newdate->getTimestamp();
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
                'late' => ($newdate->getTimestamp() >= $curdate ? 0 : 1),
            ];
            $newdate = $date->modify('+1 day');
        }
        $weekfinish = $newdate->getTimestamp();
        // Week content current date
        if ($curdate >= $weekstart && $curdate <= $weekfinish) {
            $results = $this->_current_week_sum($weekstart, $weekfinish);
        } else {
            if ($weekstart >= $curdate) {
                // new orders
                $results = $this->_feature_week_sum($weekstart, $weekfinish);
            } else {
                // old orders
                $results = $this->_late_week_sum($weekstart, $weekfinish);
            }
        }
//
//        $this->db->select('oi.print_date, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty');
//        $this->db->select('sum(amnt.fullfill) as fullfill');
//        $this->db->from('ts_orders o');
//        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
//        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
//        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
//        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
//        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
//        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
//        $this->db->where('o.is_canceled', 0);
//        $this->db->where('oi.print_date >= ', $weestart);
//        $this->db->where('oi.print_date < ', $weekfinish);
//        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
//        $this->db->group_by('oi.print_date');
//        $results = $this->db->get()->result_array();
        foreach ($results as $result) {
            $idx = 0;
            foreach ($week as $w) {
                if ($w['date'] == $result['print_date']) {
                    $week[$idx]['orders'] = $result['ordercnt'];
                    $week[$idx]['prints'] = $result['printqty'];
                    $week[$idx]['printed'] = $result['fullfill'];
                    break;
                } else {
                    $idx++;
                }
            }
        }
        return [
            'weeks' => $week,
            'week_num' => $weeknumber,
            'year' => $year,
        ];
    }

    public function daydetails($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        $daytitle = date('D - M, j, Y', $printdate);
        // Precompiled SQL
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.is_canceled', 0)->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend);
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $results = $this->db->get()->row_array();
        // Warnings
        $warnings = $this->get_printdate_warnings($printdate);
        // Get Unassign
        $unsign = $this->get_printdate_unsigned($printdate);
        // Get users
        $assign = $this->get_printdate_assigned($printdate);
        // Get History
        $history = $this->get_printdate_history($printdate);
        return [
            'orders' => $results['ordercnt'],
            'items' => $results['itemscnt'],
            'prints' => $results['printqty'],
            'title' => $daytitle,
            'warnings' => $warnings,
            'unsigntotal' => $unsign['total'],
            'unsign' => $unsign['data'],
            'assign' => $assign,
            'history' => $history['data'],
            'history_total' => $history['total'],
            'late' => 0,
            'printdate' => $printdate,
        ];
    }

    public function dayshortdetails($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        $daytitle = date('D - M, j, Y', $printdate);
        // Precompiled SQL
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.is_canceled', 0)->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend);
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $results = $this->db->get()->row_array();
        // Warnings
        $warnings = $this->get_printdate_warnings($printdate);
        // Regular
        $regulars = $this->get_printdate_regulars($printdate);
        // Get History
        $history = $this->get_printdate_history($printdate);
        return [
            'orders' => $results['ordercnt'],
            'items' => $results['itemscnt'],
            'prints' => $results['printqty'],
            'title' => $daytitle,
            'warnings' => $warnings,
            'regulartotal' => $regulars['total'],
            'regular' => $regulars['data'],
            'history' => $history['data'],
            'history_total' => $history['total'],
            'late' => 0,
            'printdate' => $printdate,
        ];
    }

    public function get_printdate_warnings($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('oic.order_itemcolor_id, COALESCE(approv.cnt,0) as approv, o.order_rush, o.order_num , oic.item_qty, impr.cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
        $this->db->select('ic.color,concat(ii.item_num, \' - \', ii.item_name) as item, coalesce(amnt.fullfill,0) as fulfill');
        $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, amnt.amount_date, amnt.amount_sum');
        $this->db->select('o.shipdate as order_shipdate, o.order_blank');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->where('ship.shipped > COALESCE(amnt.fullfill,0)');
        $this->db->order_by('o.order_rush desc');
        $this->db->order_by('o.order_id asc');
        $this->db->order_by('item asc');
        $this->db->order_by('ic.color asc');
        $warnings = $this->db->get()->result_array();
        if (count($warnings) > 0) {
            $idx = 0;
            foreach ($warnings as $warning) {
                $warnings[$idx]['fulfillprc'] = round($warning['fulfill']/$warning['item_qty']*100, 0);
                $warnings[$idx]['shippedprc'] = round($warning['shipped']/$warning['item_qty']*100, 0);
                $warnings[$idx]['notfulfill'] = $warning['item_qty'] - $warning['fulfill'];
                $warnings[$idx]['notshipp'] = $warning['item_qty'] - $warning['shipped'];
                if (empty($warnings[$idx]['approv']) && $warnings[$idx]['order_blank'] == 1) {
                    $warnings[$idx]['approv'] = 1;
                }
                $ordertype = 'ontime';
                if ($warnings[$idx]['order_rush']) {
                    $ordertype = 'rush';
                } elseif ($warnings[$idx]['order_shipdate'] < strtotime(date('Y-m-d', $printdate))) {
                    $ordertype = 'late';
                }
                $warnings[$idx]['shipclass'] = $ordertype;
                $idx++;
            }
        }
        return $warnings;
    }

    public function get_printdate_unsigned($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_orders o','o.order_id=oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('('.$this->amntsql.') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr', 'impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0, 'o.print_user'=> NULL]); // 'o.shipped_date' => 0
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $unsigntotal = $this->db->get()->row_array();

        $unsign = [];
        if ($unsigntotal['ordercnt'] > 0) {
            $this->db->select('oic.order_itemcolor_id, oi.order_item_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
            $this->db->select('o.order_num , oic.item_qty, impr.cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints,');
            $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
            $this->db->select('ship.shipped, o.brand, o.order_id, o.order_blank, oi.order_item_id, oic.print_ready, oi.plates_ready, oic.ink_ready, amnt.amount_date, amnt.amount_sum');
            $this->db->select('o.shipdate as order_shipdate');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
            $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
            $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
            $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
            $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0, 'o.print_user'=> NULL]); // 'o.shipped_date' => 0
            $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
            $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
            // $this->db->order_by('o.order_rush desc', 'order_id asc');
            $this->db->order_by('o.order_rush desc');
            $this->db->order_by('o.order_id asc');
            $this->db->order_by('item asc');
            $this->db->order_by('ic.color asc');
            $unsign = $this->db->get()->result_array();
            $idx = 0;
            foreach ($unsign as $uns) {
                $unsign[$idx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
                $unsign[$idx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
                $unsign[$idx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
                $unsign[$idx]['notshipp'] = $uns['fulfill'] - $uns['shipped']; // $uns['item_qty'] - $uns['shipped'];
                $unsign[$idx]['class'] = ($unsign[$idx]['fulfillprc']>$unsign[$idx]['shippedprc'] ? 'critical' : 'normal');
                $unsign[$idx]['platedocs'] = 0;
                if (empty($unsign[$idx]['approv']) && $unsign[$idx]['order_blank'] == 1) {
                    $unsign[$idx]['approv'] = 1;
                }
                $ordertype = 'ontime';
                if ($unsign[$idx]['order_rush']) {
                    $ordertype = 'rush';
                } elseif ($unsign[$idx]['order_shipdate'] < strtotime(date('Y-m-d', $printdate))) {
                    $ordertype = 'late';
                }
                $unsign[$idx]['shipclass'] = $ordertype;
                $idx++;
            }
        }
        return [
            'total' => $unsigntotal,
            'data' => $unsign,
        ];
    }

    public function get_printdate_regulars($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_orders o','o.order_id=oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('('.$this->amntsql.') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr', 'impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0,]);
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $total = $this->db->get()->row_array();

        $data = [];
        if ($total['ordercnt'] > 0) {
            $this->db->select('oic.order_itemcolor_id, oi.order_item_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
            $this->db->select('o.order_num , oic.item_qty, impr.cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints,');
            $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
            $this->db->select('ship.shipped, o.brand, o.order_id, o.order_blank, oi.order_item_id, oic.print_ready, oi.plates_ready, oic.ink_ready, amnt.amount_date, amnt.amount_sum');
            $this->db->select('o.shipdate as order_shipdate');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
            $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
            $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
            $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
            $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0,]);
            $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
            $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
            $this->db->order_by('o.order_rush desc');
            $this->db->order_by('o.order_id asc');
            $this->db->order_by('item asc');
            $this->db->order_by('ic.color asc');
            $data = $this->db->get()->result_array();
            $idx = 0;
            foreach ($data as $uns) {
                $data[$idx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
                $data[$idx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
                $data[$idx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
                $data[$idx]['notshipp'] = $uns['fulfill'] - $uns['shipped']; // $uns['item_qty'] - $uns['shipped'];
                $data[$idx]['class'] = ($data[$idx]['fulfillprc']>$data[$idx]['shippedprc'] ? 'critical' : 'normal');
                $data[$idx]['platedocs'] = 0;
                if (empty($data[$idx]['approv']) && $data[$idx]['order_blank'] == 1) {
                    $data[$idx]['approv'] = 1;
                }
                $ordertype = 'ontime';
                if ($data[$idx]['order_rush']) {
                    $ordertype = 'rush';
                } elseif ($data[$idx]['order_shipdate'] < strtotime(date('Y-m-d', $printdate))) {
                    $ordertype = 'late';
                }
                $data[$idx]['shipclass'] = $ordertype;
                $idx++;
            }
        }
        return [
            'total' => $total,
            'data' => $data,
        ];
    }

    public function get_printdate_assigned($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('o.print_user as user_id, u.first_name as user_name, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$this->amntsql.') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr', 'impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('users u', 'u.user_id = o.print_user');
        $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0
        $this->db->where('o.print_user IS NOT NULL');
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->group_by('o.print_user, u.first_name');
        $asssign = $this->db->get()->result_array();
        return $asssign;
    }

    public function get_printdate_usrassigned($printdate, $user)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
        $this->db->select('o.order_num , oic.item_qty, impr.cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
        $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
        $this->db->select('ship.shipped, o.brand, o.order_id, o.order_blank, oi.order_item_id, oic.print_ready');
        $this->db->select('oi.plates_ready, oic.ink_ready, amnt.amount_date, amnt.amount_sum');
        $this->db->select('o.shipdate as order_shipdate');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend)->where(['o.is_canceled' => 0,  'o.print_user'=> $user]); // 'o.shipped_date' => 0,
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        // $this->db->order_by('o.order_rush desc', 'order_id asc');
        $this->db->order_by('o.order_rush desc');
        $this->db->order_by('o.order_id asc');
        $this->db->order_by('item asc');
        $this->db->order_by('ic.color asc');
        $assigns = $this->db->get()->result_array();
        $idx = 0;
        foreach ($assigns as $uns) {
            $assigns[$idx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
            $assigns[$idx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
            $assigns[$idx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
            $assigns[$idx]['notshipp'] = $uns['fulfill'] - $uns['shipped']; // $uns['item_qty'] - $uns['shipped'];
            $assigns[$idx]['class'] = ($assigns[$idx]['fulfillprc']>$assigns[$idx]['shippedprc'] ? 'critical' : 'normal');
            $assigns[$idx]['platedocs'] = 0;
            if (empty($assigns[$idx]['approv']) && $assigns[$idx]['order_blank'] == 1) {
                $assigns[$idx]['approv'] = 1;
            }
            $ordertype = 'ontime';
            if ($assigns[$idx]['order_rush']) {
                $ordertype = 'rush';
            } elseif ($assigns[$idx]['order_shipdate'] < strtotime(date('Y-m-d', $printdate))) {
                $ordertype = 'late';
            }
            $assigns[$idx]['shipclass'] = $ordertype;
            $idx++;
        }
        return $assigns;
    }

    public function _get_printdate_history($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // List data
        $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
        $this->db->select('o.order_num , oic.item_qty, impr.cntprint, impr.imprintqty as prints, amnt.printshop_total');
        $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
        $this->db->select('o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, amnt.amount_date, amnt.amount_sum, amnt.misprint, amnt.kepted, amnt.plates');
        $this->db->select('o.print_user as user_id, u.first_name as user_name');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('(' . $this->shipsql . ') ship', 'ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('(' . $this->amntsql . ') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id', 'left');
        $this->db->join('(' . $this->printsql . ') impr', 'impr.order_item_id = oi.order_item_id', 'left');
        $this->db->join('(' . $this->proofsql . ') approv', 'approv.order_id=o.order_id', 'left');
        $this->db->join('users u', 'u.user_id = o.print_user', 'left');
        $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0,]); //'o.shipped_date' => 0
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('ship.shipped >= oic.item_qty');
        $this->db->where('coalesce(amnt.fullfill,0) >= oic.item_qty');
        $this->db->order_by('o.order_rush desc', 'order_id asc');
        $history = $this->db->get()->result_array();
        $idx = 0;
        foreach ($history as $uns) {
            $history[$idx]['shipped'] = 0;
            $history[$idx]['trackcode'] = $history[$idx]['trackservice'] = $this->empty_content;
            $history[$idx]['fulfillprc'] = round($uns['fulfill'] / $uns['item_qty'] * 100, 0);
            // $unsign[$idx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
            // $history[$idx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
            // $history[$idx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
            $history[$idx]['misprintprc'] = $uns['fulfill'] == 0 ? 0 : $uns['misprint'] / $uns['fulfill'] * 100;
            // Get method track #
            $this->db->select('count(tracking_id) as cnt, sum(qty) as qty, max(trackservice) as trackservice, max(trackcode) as trackcode')->from('ts_order_trackings');
            $this->db->where('order_itemcolor_id', $uns['order_itemcolor_id']);
            $this->db->where('trackdate >=', $daybgn);
            $this->db->where('trackdate <', $dayend);
            $trackings = $this->db->get()->row_array();
            if ($trackings['cnt'] > 0) {
                $history[$idx]['shipped'] = $trackings['qty'];
                $history[$idx]['trackcode'] = $trackings['trackcode'];
                $history[$idx]['trackservice'] = $trackings['trackservice'];
            }
            $idx++;
        }
        // Get History totals
        $this->db->select('o.print_user as user_id, u.first_name as user_name, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(impr.imprintqty) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('(' . $this->amntsql . ') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('(' . $this->printsql . ') impr', 'impr.order_item_id = oi.order_item_id', 'left');
        $this->db->join('(' . $this->shipsql . ') ship', 'ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('users u', 'u.user_id = o.print_user', 'left');
        $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend)->where(['o.is_canceled' => 0,]); // 'o.shipped_date' => 0
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('ship.shipped >= oic.item_qty');
        $this->db->where('coalesce(amnt.fullfill,0) >= oic.item_qty');
        $this->db->group_by('o.print_user, u.first_name');
        $history_total = $this->db->get()->result_array();
        $idx = 0;
        $orders = $prints = $items = 0;
        foreach ($history_total as $uns) {
            $orders += $uns['ordercnt'];
            $items += $uns['itemscnt'];
            $prints += $uns['printqty'];
            $history_total[$idx]['class'] = 'normal';
            $idx++;
        }
        $history_total[] = [
            'class' => 'total',
            'user_name' => 'Total',
            'ordercnt' => $orders,
            'itemscnt' => $items,
            'printqty' => $prints,
        ];
        return [
            'total' => $history_total,
            'data' => $history,
        ];
    }

    public function get_printdate_history($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompile SQL
        // $this->db->select('amount_id, order_itemcolor_id, amount_date, shipped, amount_sum, misprint, kepted, (orangeplate+blueplate+beigeplate) as plates');
        $this->db->select('amount_id, order_itemcolor_id, amount_date, shipped, (shipped+misprint+kepted) as amount_sum, misprint, kepted, (orangeplate+blueplate+beigeplate) as plates');
        $this->db->from('ts_order_amounts');
        $this->db->where('amount_date >= ', $daybgn);
        $this->db->where('amount_date < ', $dayend);
        $ordamnt = $this->db->get_compiled_select();
        $this->db->select('tracking_id, order_itemcolor_id, qty, trackservice, trackcode, trackdate');
        $this->db->from('ts_order_trackings');
        $this->db->where('trackdate >= ', $daybgn);
        $this->db->where('trackdate < ', $dayend);
        $ordtrack = $this->db->get_compiled_select();
        // List data
        $this->db->select('oic.order_itemcolor_id, oa.amount_id, tr.tracking_id, COALESCE(approv.cnt, 0) as approv, o.brand');
        $this->db->select('o.print_user as user_id, u.first_name as user_name, o.order_rush, o.order_blank, o.order_id, o.order_num, oic.item_qty');
        $this->db->select('impr.cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints, ic.color, concat(ii.item_num,\' - \', ii.item_name) as item, oa.shipped as printed');
        $this->db->select('oa.kepted, oa.misprint, oa.amount_sum, oa.plates, tr.qty as shipped, tr.trackservice , tr.trackcode');
        $this->db->select('o.shipdate as order_shipdate');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii','ii.inventory_item_id=ic.inventory_item_id');
        $this->db->join('('.$ordamnt.') oa', 'oa.order_itemcolor_id = oic.order_itemcolor_id', 'left');
        $this->db->join('('.$ordtrack.') tr', 'tr.order_itemcolor_id = oic.order_itemcolor_id', 'left');
        $this->db->join('('.$this->proofsql.') approv', 'approv.order_id=o.order_id','left');
        $this->db->join('('.$this->printsql.') impr', 'impr.order_item_id = oi.order_item_id','left');
        $this->db->join('users u', 'u.user_id = o.print_user', 'left');
        $this->db->where('(COALESCE(oa.amount_id,0) > 0 or COALESCE(tr.qty,0) > 0)');
        $this->db->order_by('o.order_rush desc');
        $this->db->order_by('o.order_id asc');
        $this->db->order_by('item asc');
        $this->db->order_by('ic.color asc');
        $history = $this->db->get()->result_array();
        $idx = 0;
        foreach ($history as $uns) {
            $history[$idx]['fulfillprc'] = empty($uns['printed']) ? '0' : round($uns['printed'] / $uns['item_qty'] * 100, 0);
            $history[$idx]['misprintprc'] = empty($uns['printed']) ? 0 : $uns['misprint'] / $uns['printed'] * 100;
            if (empty($history[$idx]['approv']) && $history[$idx]['order_blank'] == 1) {
                $history[$idx]['approv'] = 1;
            }
            $ordertype = 'ontime';
            if ($history[$idx]['order_rush']) {
                $ordertype = 'rush';
            } elseif ($history[$idx]['order_shipdate'] < strtotime(date('Y-m-d', $printdate))) {
                $ordertype = 'late';
            }
            $history[$idx]['shipclass'] = $ordertype;
            $idx++;
        }
        // Totals ???
        $this->db->select('o.print_user as user_id, u.first_name as user_name, count(distinct(o.order_id)) as ordercnt');
        // , sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty,
        // sum(oa.shipped) as fullfill');
        $this->db->select('sum(oa.shipped) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oa.shipped,0)) as printqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$ordamnt.') oa','oa.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('users u', 'u.user_id = o.print_user', 'left');
        // $this->db->where('oic.shipping_ready > ', 0);
        $this->db->group_by('o.print_user, u.first_name');
        $history_total = $this->db->get()->result_array();
        $idx = 0;
        $orders = $prints = $items = 0;
        foreach ($history_total as $uns) {
            $orders += $uns['ordercnt'];
            $items += $uns['itemscnt'];
            $prints += $uns['printqty'];
            $history_total[$idx]['class'] = 'normal';
            $idx++;
        }
        $history_total[] = [
            'class' => 'total',
            'user_name' => 'Total',
            'ordercnt' => $orders,
            'itemscnt' => $items,
            'printqty' => $prints,
        ];
        return [
            'total' => $history_total,
            'data' => $history,
        ];

    }

    public function get_reschedule_printdate()
    {
        $curdate = strtotime(date('Y-m-d'));
        // Get LATE orders
        $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
        $this->db->select('o.order_num , oic.item_qty, coalesce(impr.cntprint,0) as cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
        $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
        $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, amnt.amount_date, amnt.amount_sum');
        $this->db->select('timestampdiff(DAY,  from_unixtime(o.print_date),  now()) as diffdays');
        $this->db->select('o.shipdate as order_shipdate, o.order_blank');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('o.print_date < ', $curdate)->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0
        // $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->order_by('o.order_rush desc');
        $this->db->order_by('oi.print_date asc');
        $this->db->order_by('order_id asc');
        $this->db->order_by('item asc');
        $this->db->order_by('ic.color asc');
        $lateorders = $this->db->get()->result_array();
        $didx = 0;
        foreach ($lateorders as $uns) {
            $lateorders[$didx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
            $lateorders[$didx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
            $lateorders[$didx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
            $lateorders[$didx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
            $lateorders[$didx]['class'] = ($lateorders[$didx]['fulfillprc']>$lateorders[$didx]['shippedprc'] ? 'critical' : 'normal');
            if (empty($lateorders[$didx]['approv']) && $lateorders[$didx]['order_blank'] == 1) {
                $lateorders[$didx]['approv'] = 1;
            }
            $ordertype = 'ontime';
            if ($lateorders[$didx]['order_rush']) {
                $ordertype = 'rush';
            } elseif ($lateorders[$didx]['order_shipdate'] < $curdate) {
                $ordertype = 'late';
            }
            $lateorders[$didx]['shipclass'] = $ordertype;
            $didx++;
        }
        $lates = count($lateorders);
        // ON Time
        $this->db->select('oi.print_date, count(oic.order_itemcolor_id) as cnt');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('oi.print_date >= ', $curdate);
        $this->db->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0,
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->group_by('oi.print_date');
        $this->db->order_by('oi.print_date');
        $sheduls = $this->db->get()->result_array();
        $idx = 0;
        $ontime = 0;
        foreach ($sheduls as $shedul) {
            $daybgn = $shedul['print_date'];
            $dayend = strtotime('+1 day', $daybgn);
            $ontime++;
            $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
            $this->db->select('o.order_num , oic.item_qty, coalesce(impr.cntprint,0) as cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
            $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
            $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, amnt.amount_date, amnt.amount_sum');
            $this->db->select('o.shipdate as order_shipdate, o.order_blank');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
            $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
            $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
            $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
            $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend)->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0
            $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
            $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
            $this->db->order_by('o.order_rush desc');
            $this->db->order_by('order_id asc');
            $this->db->order_by('item asc');
            $this->db->order_by('ic.color asc');
            $dats = $this->db->get()->result_array();
            $didx = 0;
            foreach ($dats as $uns) {
                $dats[$didx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
                $dats[$didx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
                $dats[$didx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
                $dats[$didx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
                $dats[$didx]['class'] = ($dats[$didx]['fulfillprc']>$dats[$didx]['shippedprc'] ? 'critical' : 'normal');
                if (empty($dats[$didx]['approv']) && $dats[$didx]['order_blank'] == 1) {
                    $dats[$didx]['approv'] = 1;
                }
                $ordertype = 'ontime';
                if ($dats[$didx]['order_rush']) {
                    $ordertype = 'rush';
                } elseif ($dats[$didx]['order_shipdate'] < $daybgn) {
                    $ordertype = 'late';
                }
                $dats[$didx]['shipclass'] = $ordertype;
                $didx++;
            }
            $sheduls[$idx]['data'] = $dats;
            if ($daybgn < $curdate) {
                $sheduls[$idx]['class'] = 'late';
            } else {
                $sheduls[$idx]['class'] = 'ontime';
            }
            $idx++;
        }
        return [
            'calendar' => $sheduls,
            'lates' => $lates,
            'ontime' => $ontime,
            'lateorders' => $lateorders,
        ];
    }

    public function _get_reschedule_printdate()
    {
        $this->db->select('oi.print_date, count(oic.order_itemcolor_id) as cnt');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('oi.print_date > ', 0);
        $this->db->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0,
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->group_by('oi.print_date');
        $this->db->order_by('oi.print_date');
        $sheduls = $this->db->get()->result_array();
        $idx = 0;
        $lates = $ontime = 0;
        $curdate = strtotime(date('Y-m-d'));
        foreach ($sheduls as $shedul) {
            $daybgn = $shedul['print_date'];
            $dayend = strtotime('+1 day', $daybgn);
            if ($daybgn < $curdate) {
                $lates++;
            } else {
                $ontime++;
            }
            $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
            $this->db->select('o.order_num , oic.item_qty, coalesce(impr.cntprint,0) as cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
            $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
            $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, amnt.amount_date, amnt.amount_sum');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
            $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
            $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
            $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
            $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend)->where(['o.is_canceled' => 0,]);  // 'o.shipped_date' => 0
            $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
            $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
            $this->db->order_by('o.order_rush desc', 'order_id asc');
            $dats = $this->db->get()->result_array();
            $didx = 0;
            foreach ($dats as $uns) {
                $dats[$didx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
                $dats[$didx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
                $dats[$didx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
                $dats[$didx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
                $dats[$didx]['class'] = ($dats[$didx]['fulfillprc']>$dats[$didx]['shippedprc'] ? 'critical' : 'normal');
                $didx++;
            }
            $sheduls[$idx]['data'] = $dats;
            if ($daybgn < $curdate) {
                $sheduls[$idx]['class'] = 'late';
            } else {
                $sheduls[$idx]['class'] = 'ontime';
            }
            $idx++;
        }
        return [
            'calendar' => $sheduls,
            'lates' => $lates,
            'ontime' => $ontime,
        ];
    }

    public function get_reschedule_items()
    {
        $this->db->select('ii.inventory_item_id, concat(ii.item_num , \' - \', ii.item_name) as item, count(oic.order_itemcolor_id) as cnt, count(distinct(o.order_id)) as orders');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('oi.print_date > ', 0);
        $this->db->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0,
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->group_by('ii.inventory_item_id, item');
        $this->db->order_by('ii.item_num asc');
        $sheduls = $this->db->get()->result_array();
        $idx = 0;
        $curdate = strtotime(date('Y-m-d'));
        foreach ($sheduls as $shedul) {
            $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush, o.order_blank');
            $this->db->select('o.order_num , oic.item_qty, coalesce(impr.cntprint,0) as cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
            $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
            $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, amnt.amount_date, amnt.amount_sum, o.print_date');
            $this->db->select('o.shipdate as order_shipdate');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
            $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
            $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
            $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
            $this->db->where(['ii.inventory_item_id' => $shedul['inventory_item_id'],'o.is_canceled' => 0, ]); // 'o.shipped_date' => 0
            $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
            $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
            $this->db->order_by('o.order_rush desc');
            $this->db->order_by('o.order_id asc');
            $dats = $this->db->get()->result_array();
            $didx = 0;
            $items = $prints = 0;
            foreach ($dats as $uns) {
                $dats[$didx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
                $dats[$didx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
                $dats[$didx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
                $dats[$didx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
                $dats[$didx]['class'] = ($dats[$didx]['fulfillprc']>$dats[$didx]['shippedprc'] ? 'critical' : 'normal');
                $dats[$didx]['dateclass'] = ($uns['print_date']<$curdate ? 'latedate' : 'ontimedate');
                if (empty($dats[$didx]['approv']) && $dats[$didx]['order_blank'] == 1) {
                    $dats[$didx]['approv'] = 1;
                }
                $items+=$uns['item_qty'];
                $prints+=$uns['prints'];
                $ordertype = 'ontime';
                if ($dats[$didx]['order_rush']) {
                    $ordertype = 'rush';
                } elseif ($dats[$didx]['order_shipdate'] < $curdate) {
                    $ordertype = 'late';
                }
                $dats[$didx]['shipclass'] = $ordertype;
                $didx++;
            }
            $sheduls[$idx]['items'] = $items;
            $sheduls[$idx]['prints'] = $prints;
            $sheduls[$idx]['data'] = $dats;
//            $sheduls[$idx]['item'] = str_replace('Stress Balls','', $sheduls[$idx]['item']);
            $idx++;
        }
        return $sheduls;
    }

    public function updateorder_printdate($order_id, $printdate)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];
        $order = $this->db->select('order_item_id, order_id, print_date')->from('ts_order_items')->where('order_item_id', $order_id)->get()->row_array();
        if (ifset($order, 'order_item_id', 0)==$order_id) {
            $out['result'] = $this->success_result;
            $out['olddate'] = $order['print_date'];
            // Update Order
            $this->db->where('order_id', $order['order_id']);
            $this->db->set('print_date', $printdate);
            $this->db->set('print_user', NULL);
            $this->db->update('ts_orders');
            // Update order items
            $this->db->where('order_item_id', $order_id);
            $this->db->set('print_date', $printdate);
            $this->db->update('ts_order_items');
            $orddat = $this->db->select('order_num')->from('ts_orders')->where('order_id', $order['order_id'])->get()->row_array();
            $out['order_num'] = $orddat['order_num'];
        }
        return $out;
    }

    public function weekdates($week, $direct)
    {
        $weekdats = explode('-', $week);
        $weeknum = $weekdats[0];
        $year = $weekdats[1];
        $weekdats = getDatesByWeek($weeknum, $year);
        if ($direct=='prev') {
            $mondaydate = strtotime('-1 week', $weekdats['start_week']);
        } else {
            $mondaydate = strtotime('+1 week', $weekdats['start_week']);
        }
        $newweek = explode('-', date('W-Y', $mondaydate));
        return [
            'week' => $newweek[0],
            'year' => $newweek[1],
        ];
    }

    public function assignorder($order_id, $user_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];
        // $this->db->select('o.order_id, oi.print_date')->from('ts_orders o')->join('ts_order_items oi','oi.order_id=o.order_id')->join('ts_order_itemcolors oic', 'oic.order_item_id=oi.order_item_id')->where('oic.order_itemcolor_id', $order_itemcolor_id);
        $this->db->select('o.order_id, oi.print_date')->from('ts_orders o')->join('ts_order_items oi','oi.order_id=o.order_id')->where('o.order_id', $order_id);
        $orderres = $this->db->get()->row_array();
        if (ifset($orderres,'order_id',0)>0) {
            $out['result'] = $this->success_result;
            $out['printdate'] = $orderres['print_date'];
            $this->db->where('order_id', $orderres['order_id']);
            if (intval($user_id)==0) {
                $this->db->set('print_user', NULL);
            } else {
                $this->db->set('print_user', $user_id);
            }
            $this->db->update('ts_orders');
        }
        return $out;
    }

    public function stockupdate($order_itemcolor_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Color Not Found'];
        $this->db->select('order_itemcolor_id, print_ready')->from('ts_order_itemcolors')->where('order_itemcolor_id', $order_itemcolor_id);
        $res = $this->db->get()->row_array();
        if (ifset($res,'order_itemcolor_id',0)==$order_itemcolor_id) {
            $out['result'] = $this->success_result;
            if ($res['print_ready']==0) {
                $newval = 1;
            } else {
                $newval = 0;
            }
            $this->db->where('order_itemcolor_id', $order_itemcolor_id);
            $this->db->set('print_ready', $newval);
            $this->db->update('ts_order_itemcolors');
            $out['newval'] = $newval;
        }
        return $out;
    }

    public function plateupdate($order_item_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Item Not Found'];
        $this->db->select('order_item_id, plates_ready')->from('ts_order_items')->where('order_item_id', $order_item_id);
        $res = $this->db->get()->row_array();
        if (ifset($res,'order_item_id',0)==$order_item_id) {
            $out['result'] = $this->success_result;
            if ($res['plates_ready']==0) {
                $newval = 1;
            } else {
                $newval = 0;
            }
            $this->db->where('order_item_id', $order_item_id);
            $this->db->set('plates_ready', $newval);
            $this->db->update('ts_order_items');
            $out['newval'] = $newval;
        }
        return $out;
    }

    public function inkupdate($order_itemcolor_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Color Not Found'];
        $this->db->select('order_itemcolor_id, ink_ready')->from('ts_order_itemcolors')->where('order_itemcolor_id', $order_itemcolor_id);
        $res = $this->db->get()->row_array();
        if (ifset($res,'order_itemcolor_id',0)==$order_itemcolor_id) {
            $out['result'] = $this->success_result;
            if ($res['ink_ready']==0) {
                $newval = 1;
            } else {
                $newval = 0;
            }
            $this->db->where('order_itemcolor_id', $order_itemcolor_id);
            $this->db->set('ink_ready', $newval);
            $this->db->update('ts_order_itemcolors');
            $out['newval'] = $newval;
        }
        return $out;
    }

    public function outcomesave($order_itemcolor_id,$shipped,$kepted,$misprint,$plates, $podate)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order not found'];
        // Get order
        $this->db->select('oic.order_itemcolor_id, oic.print_date colorprint, oic.print_completed, oic.item_qty, oic.inventory_color_id, o.order_id, o.order_num, o.order_date, o.customer_name, o.print_date');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('ts_orders o', 'oi.order_id = o.order_id')->where('oic.order_itemcolor_id', $order_itemcolor_id);
        $orderdata = $this->db->get()->row_array();
        if (ifset($orderdata, 'order_id', 0) > 0) {
            $out['msg'] = 'Empty Outcome values';
            if ($shipped + $kepted + $misprint + $plates > 0) {
                $inventory_color_id = $orderdata['inventory_color_id'];
                $out['printdate'] = $orderdata['print_date'];
                $this->load->model('inventory_model');
                $diff = $shipped + $kepted + $misprint;
                $balance = $this->inventory_model->inventory_color_income($inventory_color_id) - $this->inventory_model->inventory_color_outcome($inventory_color_id);
                $newbalance = $balance - $diff;
                if ($newbalance < 0) {
                    $out['msg'] = 'Enter Other QTY or Increase Income, or Choose other Inventory item';
                    return $out;
                }
                $orderdata = $this->_prepare_amount_save($orderdata, $inventory_color_id, $shipped, $kepted, $misprint, $plates, $podate);
                $amountres = $this->_save_amount($orderdata, $this->USR_ID);
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
                        // Update print_date & print_completed
                        $passed = $this->_completed_itemcolor($order_itemcolor_id);
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
            return $out;
        }
    }

    private function _prepare_amount_save($orderdata, $inventory_color_id, $shipped, $kepted, $misprint, $plates, $podate)
    {
        // Prices
        $this->db->select('c.price, c.avg_price, t.type_addcost')->from('ts_inventory_colors c')->join('ts_inventory_items i','c.inventory_item_id = i.inventory_item_id');
        $this->db->join('ts_inventory_types t','i.inventory_type_id = t.inventory_type_id')->where('c.inventory_color_id', $inventory_color_id);
        $pricedat = $this->db->get()->row_array();
        // Prepare saving
        $this->load->model('inventory_model');
        $platesprice = $this->inventory_model->_get_plates_costs();
        $orderdata['printshop_date'] = time();
        $orderdata['inventory_color_id'] = $inventory_color_id;
        $orderdata['shipped'] = $shipped;
        $orderdata['kepted'] = $kepted;
        $orderdata['misprint'] = $misprint;
        $orderdata['blueplate'] = $plates;
        $orderdata['blueplate_price'] = $platesprice['blueplate_price'];
        $orderdata['extracost'] = floatval($pricedat['type_addcost']);
        $orderdata['printshop_type'] = 'S';
        $totalea = round($pricedat['avg_price']+$pricedat['type_addcost'],3);
        $costitem = $totalea * ($orderdata['shipped']+$orderdata['kepted']+$orderdata['misprint']);
        $platescost = $plates * $orderdata['blueplate_price'];
        $totalitemcost = $platescost+$costitem;
        $orderdata['price'] = $pricedat['avg_price'];
        $orderdata['itemstotalcost'] = $totalitemcost;
        $orderdata['amount_date'] = $podate;
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
        $this->db->set('amount_date', $orderdata['amount_date']);// time());
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

    private function _completed_itemcolor($order_itemcolor_id)
    {
        $outcome = 0;
        $this->db->select('count(amount_id) as amntcnt, sum(shipped) as amnttotal')->from('ts_order_amounts')->where(['order_itemcolor_id' => $order_itemcolor_id]);
        $amntres = $this->db->get()->row_array();
        if ($amntres['amntcnt'] > 0) {
            $outcome = $amntres['amnttotal'];
        }
        return $outcome;
    }

    public function get_itemcolor_details($order_itemcolor_id)
    {
        // Precompiled SQL
        $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
        $this->db->select('o.order_num , oic.item_qty, impr.cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
        $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
        $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, oic.ink_ready, amnt.amount_date, amnt.amount_sum');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('oic.order_itemcolor_id', $order_itemcolor_id);
        $data = $this->db->get()->row_array();
        $data['fulfillprc'] = round($data['fulfill']/$data['item_qty']*100,0);
        $data['shippedprc'] = round($data['shipped']/$data['item_qty']*100,0);
        $data['notfulfill'] = $data['item_qty'] - $data['fulfill'];
        $data['notshipp'] = $data['item_qty'] - $data['shipped'];
        $data['class'] = ($data['fulfillprc']>$data['shippedprc'] ? 'critical' : 'normal');
        return $data;
    }

    public function shiporder($order_itemcolor_id, $shippingqty, $shipmethod, $trackcode, $shipdate)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order not found'];
        $this->db->select('oic.order_itemcolor_id, oic.item_qty, oic.shipping_ready, o.order_id, o.order_num, oi.print_date')->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi','oic.order_item_id = oi.order_item_id')->join('ts_orders o','oi.order_id = o.order_id')->where('oic.order_itemcolor_id', $order_itemcolor_id);
        $orderdata = $this->db->get()->row_array();
        if (ifset($orderdata, 'order_id',0) > 0) {
            if (empty($orderdata['shipping_ready'])) {
                $this->db->select('*')->from('ts_order_trackings')->where(['order_itemcolor_id' => $order_itemcolor_id,'qty'=>0]);
                $trackraw = $this->db->get()->row_array();
                if (ifset($trackraw, 'tracking_id',0) > 0) {
                    $this->db->where('tracking_id', $trackraw['tracking_id']);
                    $this->db->set('updated_by', $this->USR_ID);
                    $this->db->set('qty', $shippingqty);
                    $this->db->set('trackdate', strtotime($shipdate));
                    $this->db->set('trackservice', $shipmethod);
                    $this->db->set('trackcode', $trackcode);
                    $this->db->update('ts_order_trackings');
                    $track_id = $trackraw['tracking_id'];
                } else {
                    // Add new tracking
                    $this->db->set('created_at', date('Y-m-d H:i:s'));
                    $this->db->set('created_by', $this->USR_ID);
                    $this->db->set('updated_by', $this->USR_ID);
                    $this->db->set('order_itemcolor_id', $order_itemcolor_id);
                    $this->db->set('qty', $shippingqty);
                    $this->db->set('trackdate', strtotime($shipdate));
                    $this->db->set('trackservice', $shipmethod);
                    $this->db->set('trackcode', $trackcode);
                    $this->db->insert('ts_order_trackings');
                    $track_id = $this->db->insert_id();
                }
                if ($track_id > 0) {
                    $out['result'] = $this->success_result;
                    $out['printdate'] = $orderdata['print_date'];
                    $tracked = $this->_shipped_itemcolor($order_itemcolor_id);
                    if ($tracked >= $orderdata['item_qty']) {
                        $this->db->where('order_itemcolor_id', $order_itemcolor_id);
                        $this->db->set('shipping_ready', time());
                        $this->db->update('ts_order_itemcolors');
                    }
                    // Count shipping parts
                    $this->db->select('count(oa.amount_id) as amntcnt, sum(oa.shipped) as amnttotal, sum(oic.item_qty) totalqty');
                    $this->db->from('ts_order_amounts oa');
                    $this->db->join('ts_order_itemcolors oic', 'oic.order_itemcolor_id = oa.order_itemcolor_id');
                    $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
                    $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
                    $this->db->where('o.order_id', $orderdata['order_id']);
                    $printed = $this->db->get()->row_array();
                    if ($printed['amntcnt'] > 0) {
                        if ($printed['amnttotal'] >= $printed['totalqty']) {
                            $this->db->select('count(oic.order_itemcolor_id) as cnt')->from('ts_order_itemcolors oic')->join('ts_order_items oi', 'oic.order_item_id=oi.order_item_id')->join('ts_orders o', 'o.order_id=oi.order_id');
                            $this->db->where(['o.order_id' => $orderdata['order_id'],'oic.shipping_ready' => 0]);
                            $chkres = $this->db->get()->row_array();
                            if ($chkres['cnt']==0) {
                                $this->db->where('order_id', $orderdata['order_id']);
                                $this->db->set('shipped_date', time());
                                $this->db->update('ts_orders');
                            }
                        }
                    }
                }
            } else {
                $out['result'] = $this->success_result;
                $out['printdate'] = $orderdata['print_date'];
            }
        }
        return $out;
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

    public function daylatedetails($printdate)
    {
        $daytitle = date('D - M, j, Y', $printdate);
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0)) as printqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.is_canceled', 0)->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend);
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $totalday = $this->db->get()->row_array();

        return [
            'title' => $daytitle,
            'orders' => $totalday['ordercnt'],
            'items' => $totalday['itemscnt'],
            'prints' => $totalday['printqty'],
            'late' => 1,
            'printdate' => $printdate,
        ];
    }

    public function get_reschedule_data($print_date)
    {
        $curdate = strtotime(date('Y-m-d'));
        if ($print_date < $curdate) {
            $late = 1;
            $daybgn = strtotime('2001-01-01');
            $dayend = $curdate;
        } else {
            $late = 0;
            $daybgn = $print_date;
            $dayend = strtotime('+1 day', $daybgn);
        }
        $this->db->select('oic.order_itemcolor_id, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
        $this->db->select('o.order_num , oic.item_qty, coalesce(impr.cntprint,0) as cntprint, coalesce(impr.cntprint,0)*coalesce(oic.item_qty,0) as prints');
        $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
        $this->db->select('ship.shipped, o.brand, o.order_id, oi.order_item_id, oic.print_ready, oi.plates_ready, amnt.amount_date, amnt.amount_sum');
        if ($late==1) {
            $this->db->select('timestampdiff(DAY,  from_unixtime(o.print_date),  now()) as diffdays');
        }
        $this->db->select('o.order_blank, o.shipdate as order_shipdate');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$this->shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$this->amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$this->proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('oi.print_date >= ', $daybgn)->where('oi.print_date < ', $dayend)->where(['o.is_canceled' => 0, ]); // 'o.shipped_date' => 0
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $this->db->where('(ship.shipped < oic.item_qty or coalesce(amnt.fullfill,0) < oic.item_qty)');
        $this->db->order_by('o.order_rush desc');
        $this->db->order_by('order_id asc');
        $this->db->order_by('item asc');
        $this->db->order_by('ic.color asc');
        $dats = $this->db->get()->result_array();
        $didx = 0;
        foreach ($dats as $uns) {
            $dats[$didx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
            $dats[$didx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
            $dats[$didx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
            $dats[$didx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
            $dats[$didx]['class'] = ($dats[$didx]['fulfillprc']>$dats[$didx]['shippedprc'] ? 'critical' : 'normal');
            if (empty($dats[$didx]['approv']) && $dats[$didx]['order_blank'] == 1) {
                $dats[$didx]['approv'] = 1;
            }
            $ordertype = 'ontime';
            if ($dats[$didx]['order_rush']) {
                $ordertype = 'rush';
            } elseif ($dats[$didx]['order_shipdate'] < $curdate) {
                $ordertype = 'late';
            }
            $dats[$didx]['shipclass'] = $ordertype;
            $didx++;
        }
        return ['data' => $dats, 'late' => $late];
    }

    public function total_statistic()
    {
        $years = $this->get_printshops_years();
        $data = [];
        foreach ($years as $year) {
            $datbgn = strtotime($year['yearprint'].'-01-01');
            if (intval(date('Y'))==intval($year['yearprint'])) {
                $datend = strtotime(date('Y-m-d').'+1 day');
                $weeks = intval(date('W'));
            } else {
                $datend = strtotime(date('Y-m-d', $datbgn).'+1 year');
                $weeks = 53;
            }
            // Days diff
            $earlier = new DateTime(date('Y-m-d', $datbgn));
            $later = new DateTime(date('Y-m-d', $datend));
            $daydiff = $later->diff($earlier)->format("%a");

            $this->db->select('order_itemcolor_id, amount_date as amntdate, sum(shipped) as fullfill, sum(shipped+misprint+kepted) as amount_sum');
            $this->db->from('ts_order_amounts');
            $this->db->group_by('order_itemcolor_id, amntdate');
            $amntsql = $this->db->get_compiled_select();

            $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(amnt.fullfill) as itemscnt');
            $this->db->select('sum(coalesce(impr.cntprint,0)*coalesce(amnt.fullfill,0)) as printqty, sum(amnt.fullfill) as fullfill');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
            $this->db->join('ts_orders o', 'oi.order_id = o.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii','ii.inventory_item_id=oi.inventory_item_id');
            $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->where('o.is_canceled', 0);
            $this->db->where('amnt.amntdate >= ', $datbgn)->where('amnt.amntdate < ', $datend);
            $totals = $this->db->get()->row_array();

            // Avg, max per day
            $this->db->select('date_format(from_unixtime(amnt.amntdate),"%Y-%m-%d") as printdate, sum(coalesce(impr.cntprint,0)*coalesce(amnt.fullfill,0)) as maxday');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
            $this->db->join('ts_orders o', 'oi.order_id = o.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii','ii.inventory_item_id=oi.inventory_item_id');
            $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->where('o.is_canceled', 0);
            $this->db->where('amnt.amntdate >= ', $datbgn)->where('amnt.amntdate < ', $datend);
            $this->db->group_by('printdate');
            $this->db->order_by('maxday', 'desc');
            $this->db->limit(1);
            $dayres = $this->db->get()->result_array();
            $dayres[0]['avgday'] = $daydiff==0 ? 0 : round($totals['printqty']/$daydiff,2);
            // Avg, max per week
            $this->db->select('date_format(from_unixtime(amnt.amntdate), "%x-%v") as printweek');
            $this->db->select('sum(coalesce(impr.cntprint,0)*coalesce(amnt.fullfill,0)) as maxweek');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
            $this->db->join('ts_orders o', 'oi.order_id = o.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii','ii.inventory_item_id=oi.inventory_item_id');
            $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$this->printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->where('o.is_canceled', 0);
            $this->db->where('amnt.amntdate >= ', $datbgn)->where('amnt.amntdate < ', $datend);
            $this->db->group_by('printweek');
            $this->db->order_by('maxweek','desc');
            $this->db->limit(1);
            $weekres = $this->db->get()->result_array();
            $weekres[0]['avgweek'] = $weeks==0 ? 0 : round($totals['printqty']/$weeks,2);

            $weeknum = explode('-', $weekres[0]['printweek']);
            $weekdates = getDatesByWeek($weeknum[1], $weeknum[0]);

            $data[] = [
                'year' => $year['yearprint'],
                'orders' => $totals['ordercnt'],
                'items' => $totals['itemscnt'],
                'prints' => $totals['printqty'],
                'avgday' => $dayres[0]['avgday'],
                'avgweek' => $weekres[0]['avgweek'],
                'maxday_label' => strtotime($dayres[0]['printdate']),
                'maxday' => $dayres[0]['maxday'],
                'maxweek_bgn' => $weekdates['start_week'],
                'maxweek_end' => $weekdates['end_week'],
                'maxweek' => $weekres[0]['maxweek'],
            ];

        }
        return $data;
    }

    public function get_calendar_item($printdate)
    {
        $total_orders = $total_prints = $total_printed = $total_items = 0;
        $dayorders = $dayitems = $dayprints = $dayprinted = 0;
        $newdate = new DateTime(date('Y-m-d',$printdate));
        $weeknum = $newdate->format('W-Y');
        $week = substr($weeknum,1,2);
        $year = substr($weeknum,-4);
        $datesweek = getDatesByWeek($week, $year);
        $weekstart = $datesweek['start_week'];
        $weekfinish = $datesweek['end_week'];

        if ($printdate >= $weekstart && $printdate <= $weekfinish) {
            $results = $this->_current_week_sum($weekstart, $weekfinish);
        } else {
            if ($weekstart >= $printdate) {
                // new orders
                $results = $this->_feature_week_sum($weekstart, $weekfinish);
            } else {
                $readyweek = 1;
                // old orders
                $results = $this->_late_week_sum($weekstart, $weekfinish);
            }
        }
        foreach ($results as $result) {
            if ($printdate == $result['print_date']) {
                $dayorders = $result['ordercnt'];
                $dayitems = $result['itemscnt'];
                $dayprints = $result['printqty'];
                $dayprinted = $result['fullfill'];
            }
            $total_orders+=$result['ordercnt'];
            $total_items+=$result['itemscnt'];
            $total_prints+=$result['printqty'];
            $total_printed+=$result['fullfill'];
        }
        return [
            'dayorders' => $dayorders,
            'dayitems' => $dayitems,
            'dayprints' => $dayprints,
            'dayprinted' => $dayprinted,
            'total_orders' => $total_orders,
            'total_items' => $total_items,
            'total_prints' => $total_prints,
            'total_printed' => $total_printed,
            'week' => $weeknum,
        ];
    }

    public function order_approvedocs($order_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Exist'];
        $orderdat = $this->db->select('order_id, order_num, order_blank')->from('ts_orders')->where('order_id', $order_id)->get()->row_array();
        if (ifset($orderdat,'order_id',0)==$order_id) {
            $out['ordernum'] = $orderdat['order_num'];
            // Get approved docs
            $this->db->select('p.source_name, p.proof_name')->from('ts_artworks a')->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id');
            $this->db->where('a.order_id', $order_id)->where('p.approved > ',0);
            $docs = $this->db->get()->result_array();
            if (count($docs)==0 && $orderdat['order_blank']==0) {
                $out['msg'] = 'Order Not Approved';
            } else {
                $out['result'] = $this->success_result;
                $out['docs'] = $docs;
            }
        }
        return $out;
    }
}