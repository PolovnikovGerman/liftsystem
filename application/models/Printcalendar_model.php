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
        $date->modify('sunday this week');
        // Get the day of the week for the last day of the month (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
//        $dayOfWeek = $date->format('w');
//        // If the last day of the month is not already a Sunday, modify the date to be the last Sunday
//        if ($dayOfWeek != 0) {
//            $date->modify('last sunday');
//        }
        $finish_year = strtotime($date->format('Y-m-d'));

        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $calend = [];
        while (1==1) {
            $week = [];
            $total_orders = $total_prints = $total_printed = $total_items = 0;
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
            // Get data
            $this->db->select('o.print_date, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(impr.imprintqty) as printqty');
            // sum(COALESCE(impr.cntprint,0)*oic.item_qty) as printqty');
            $this->db->select('sum(amnt.fullfill) as fullfill');
            $this->db->from('ts_orders o');
            $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
            $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
            $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id'); // ,'left'
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.print_date >= ', $weestart);
            $this->db->where('o.print_date < ', $weekfinish);
            $this->db->group_by('o.print_date');
            $results = $this->db->get()->result_array();
            foreach ($results as $result) {
                $idx = 0;
                foreach ($week as $w) {
                    if ($w['date'] == $result['print_date']) {
                        $week[$idx]['orders'] = $result['ordercnt'];
                        $week[$idx]['items'] = $result['itemscnt'];
                        $week[$idx]['prints'] = $result['printqty'];
                        $week[$idx]['printed'] = $result['fullfill'];
                        break;
                    } else {
                        $idx++;
                    }
                }
                $total_orders+=$result['ordercnt'];
                $total_items+=$result['itemscnt'];
                $total_prints+=$result['printqty'];
                $total_printed+=$result['fullfill'];
            }
            $start_year = $newdate->getTimestamp();
            $calend[] = [
                'week' => $week,
                'total_orders' => $total_orders,
                'total_items' => $total_items,
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
        $this->db->select('count(distinct(o.order_id)) as ordercnt, COALESCE(sum(impr.imprintqty)) as printqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id'); // ,'left'
        $this->db->join('ts_orders o', 'o.order_id=oi.order_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date < ', $curdate->getTimestamp());
        $this->db->where('o.shipped_date',0);
        $lateres = $this->db->get()->row_array();
        // Year schedule
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as printqty, sum(impr.imprintqty) as imprintqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('ts_orders o', 'o.order_id=oi.order_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date >= ', $start_year);
        $this->db->where('o.print_date < ', $end_year);
        $statres = $this->db->get()->row_array();
        // Year printed
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as printqty, sum(impr.imprintqty) as imprintqty');
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('ts_order_items oi', 'oi.order_item_id=oic.order_item_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('ts_orders o','o.order_id=oi.order_id');
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

    public function week_calendar($weeknumber, $year)
    {
        // Date Bgn / end
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $dates = getDatesByWeek($weeknumber, $year);
        $date = new DateTime(date('Y-m-d',$dates['start_week']));
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
        $this->db->select('o.print_date, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(impr.imprintqty) as printqty');
        $this->db->select('sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.print_date >= ', $weestart);
        $this->db->where('o.print_date < ', $weekfinish);
        $this->db->group_by('o.print_date');
        $results = $this->db->get()->result_array();
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
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, max(amount_date) as amount_date, sum(amount_sum) as amount_sum')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt')->from('ts_artworks a')->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id')->where('p.approved > ',0)->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();
        $this->db->select('order_itemcolor_id, sum(qty) as shipped')->from('ts_order_trackings')->group_by('order_itemcolor_id');
        $shipsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(impr.imprintqty) as printqty');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.is_canceled', 0)->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend);
        $results = $this->db->get()->row_array();
        // Warnings
        $warnings = $this->get_printdate_warnings($printdate);
        // Get Unassign
        $unsign = $this->get_printdate_unsigned($printdate);
        // Get users
        $assign = $this->get_printdate_assigned($printdate);

        return [
            'orders' => $results['ordercnt'],
            'items' => $results['itemscnt'],
            'prints' => $results['printqty'],
            'title' => $daytitle,
            'warnings' => $warnings,
            'unsigntotal' => $unsign['total'],
            'unsign' => $unsign['data'],
            'assign' => $assign,
        ];
    }

    public function get_printdate_warnings($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, max(amount_date) as amount_date, sum(amount_sum) as amount_sum')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt')->from('ts_artworks a')->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id')->where('p.approved > ',0)->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();
        $this->db->select('order_itemcolor_id, sum(qty) as shipped')->from('ts_order_trackings')->group_by('order_itemcolor_id');
        $shipsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('oic.order_itemcolor_id, ship.shipped, COALESCE(approv.cnt,0) as approv, o.order_rush, o.order_num , oic.item_qty, impr.cntprint, impr.imprintqty as prints'); // cntprint*oic.item_qty
        $this->db->select('ic.color,concat(ii.item_num, \' - \', ii.item_name) as item, coalesce(amnt.fullfill,0) as fulfill'); //  oic.item_qty - COALESCE(amnt.fulfill,0) as notfulfill
        $this->db->select('ship.shipped, o.brand, o.order_id, amnt.amount_date, amnt.amount_sum'); // oic.item_qty - ship.shipped as notshipp,
        $this->db->from('ts_order_itemcolors oic');
        $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
        $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
        $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
        $this->db->join('('.$shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$proofsql.') approv','approv.order_id=o.order_id','left');
        $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date <= ', $dayend)->where(['o.is_canceled' => 0, 'o.shipped_date' => 0]);
        $this->db->where('ship.shipped > COALESCE(amnt.fullfill,0)');
        $warnings = $this->db->get()->result_array();
        if (count($warnings) > 0) {
            $idx = 0;
            foreach ($warnings as $warning) {
                $warnings[$idx]['fulfillprc'] = round($warning['fulfill']/$warning['item_qty']*100, 0);
                $warnings[$idx]['shippedprc'] = round($warning['shipped']/$warning['item_qty']*100, 0);
                $warnings[$idx]['notfulfill'] = $warning['item_qty'] - $warning['fulfill'];
                $warnings[$idx]['notshipp'] = $warning['item_qty'] - $warning['shipped'];
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
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, max(amount_date) as amount_date, sum(amount_sum) as amount_sum')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt')->from('ts_artworks a')->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id')->where('p.approved > ',0)->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();
        $this->db->select('order_itemcolor_id, sum(qty) as shipped')->from('ts_order_trackings')->group_by('order_itemcolor_id');
        $shipsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(impr.imprintqty) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$amntsql.') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
        $this->db->join('('.$printsql.') impr', 'impr.order_item_id = oi.order_item_id','left');
        $this->db->join('('.$shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend)->where(['o.is_canceled' => 0, 'o.shipped_date' => 0, 'o.print_user'=> NULL]);
        $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
        $unsigntotal = $this->db->get()->row_array();

        $unsign = [];
        if ($unsigntotal['ordercnt'] > 0) {
            $this->db->select('oic.order_itemcolor_id, ship.shipped, COALESCE(amnt.fullfill,0) as fulfill, COALESCE(approv.cnt,0) as approv, o.order_rush');
            $this->db->select('o.order_num , oic.item_qty, impr.cntprint, impr.imprintqty as prints');
            $this->db->select('ic.color , concat(ii.item_num , \' - \', ii.item_name) as item');
            $this->db->select('ship.shipped, o.brand, o.order_id, oic.print_ready, oi.plates_ready');
            $this->db->from('ts_order_itemcolors oic');
            $this->db->join('ts_order_items oi', 'oi.order_item_id = oic.order_item_id');
            $this->db->join('ts_orders o', 'o.order_id = oi.order_id');
            $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id = oic.inventory_color_id');
            $this->db->join('ts_inventory_items ii', 'ii.inventory_item_id = ic.inventory_item_id');
            $this->db->join('('.$shipsql.') ship','ship.order_itemcolor_id = oic.order_itemcolor_id');
            $this->db->join('('.$amntsql.') amnt','amnt.order_itemcolor_id = oic.order_itemcolor_id','left');
            $this->db->join('('.$printsql.') impr','impr.order_item_id = oi.order_item_id','left');
            $this->db->join('('.$proofsql.') approv','approv.order_id=o.order_id','left');
            $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date < ', $dayend)->where(['o.is_canceled' => 0, 'o.shipped_date' => 0, 'o.print_user'=> NULL]);
            $this->db->where('ship.shipped <= COALESCE(amnt.fullfill,0)');
            $this->db->order_by('o.order_rush desc', 'order_id asc');
            $unsign = $this->db->get()->result_array();
            $idx = 0;
            foreach ($unsign as $uns) {
                $unsign[$idx]['fulfillprc'] = round($uns['fulfill']/$uns['item_qty']*100,0);
                $unsign[$idx]['shippedprc'] = round($uns['shipped']/$uns['item_qty']*100,0);
                $unsign[$idx]['notfulfill'] = $uns['item_qty'] - $uns['fulfill'];
                $unsign[$idx]['notshipp'] = $uns['item_qty'] - $uns['shipped'];
                $idx++;
            }
        }
        return [
            'total' => $unsigntotal,
            'data' => $unsign,
        ];
    }

    public function get_printdate_assigned($printdate)
    {
        $daybgn = $printdate;
        $dayend = strtotime('+1 day', $daybgn);
        // Precompiled SQL
        $this->db->select('order_itemcolor_id, sum(shipped) as fullfill, max(amount_date) as amount_date, sum(amount_sum) as amount_sum')->from('ts_order_amounts')->group_by('order_itemcolor_id');
        $amntsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('a.order_id, count(p.artwork_proof_id) as cnt')->from('ts_artworks a')->join('ts_artwork_proofs p','p.artwork_id=a.artwork_id')->where('p.approved > ',0)->group_by('a.order_id');
        $proofsql = $this->db->get_compiled_select();
        $this->db->select('order_itemcolor_id, sum(qty) as shipped')->from('ts_order_trackings')->group_by('order_itemcolor_id');
        $shipsql = $this->db->get_compiled_select();
        $this->db->select('order_item_id, count(order_imprint_id) as cntprint, sum(imprint_qty) as imprintqty')->from('ts_order_imprints')->where('imprint_item', 1)->group_by('order_item_id');
        $printsql = $this->db->get_compiled_select();
        $this->db->select('o.print_user as user_id, u.first_name as user_name, count(distinct(o.order_id)) as ordercnt, sum(oic.item_qty) as itemscnt, sum(impr.imprintqty) as printqty, sum(amnt.fullfill) as fullfill');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_items oi', 'oi.order_id=o.order_id');
        $this->db->join('ts_order_itemcolors oic', 'oic.order_item_id = oi.order_item_id');
        $this->db->join('('.$amntsql.') amnt', 'amnt.order_itemcolor_id = oic.order_itemcolor_id');
        $this->db->join('('.$printsql.') impr', 'impr.order_item_id = oi.order_item_id','left');
        $this->db->join('ts_inventory_colors ic', 'ic.inventory_color_id=oic.inventory_color_id');
        $this->db->join('users u', 'u.user_id = o.print_user');
        $this->db->where('o.print_date >= ', $daybgn)->where('o.print_date <= ', $dayend)->where(['o.is_canceled' => 0, 'o.shipped_date' => 0]);
        $this->db->where('o.print_user IS NOT NULL');
        $this->db->group_by('o.print_user, u.first_name');
        $asssign = $this->db->get()->result_array();
        return $asssign;
    }
}