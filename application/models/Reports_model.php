<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Reports_model extends My_Model
{

    private $init_errmsg='Unknown error. Try later';
    private $salestype_start=2014;
    // private $empty_show='&ndash;&ndash;&ndash;';
    private $empty_show='&nbsp;';

    function __construct()
    {
        parent::__construct();
    }

    public function get_old_salestypes($reppermis, $profitview, $usr_profitview, $brand) {
        $start_date=strtotime($this->salestype_start.'-01-01');
        $end_report=strtotime(date('Y').'-01-01');
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $cust_years=$stock_years=$ariel_years=$alpi_years=$mailine_years=$hit_years=$other_years=$esp_years=array();
        // Select Custom Shaped SB
        if (in_array('itemsalescustoms', $reppermis)) {
            $cust_years=$this->_get_itemreport_customs_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        // Stock shape
        if (in_array('itemsalesstock', $reppermis)) {
            $stock_years=$this->_get_itemreport_stock_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        // Ariel Items
        if (in_array('itemsalesariel', $reppermis)) {
            $ariel_years=$this->_get_itemreport_ariel_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        // Alpi Items
        if (in_array('itemsalesalpi', $reppermis)) {
            $alpi_years=$this->_get_itemreport_alpi_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        // Mailine Items
        if (in_array('itemsalesmailine', $reppermis)) {
            $mailine_years=$this->_get_itemreport_mailine_old($usr_profitview, $profitview, $start_date, $end_report,$brand);
        }
        // Hits Items
        if (in_array('itemsaleshit', $reppermis)) {
            $hit_years=$this->_get_itemreport_hits_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        // Other Items
        if (in_array('itemsalesother', $reppermis)) {
            $other_years=$this->_get_itemreport_other_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        // ESP/Other Items
        if (in_array('itemsalesesp', $reppermis)) {
            $esp_years=$this->_get_itemreport_esp_old($usr_profitview, $profitview, $start_date, $end_report, $brand);
        }
        $out['customs']=$cust_years;
        $out['stocks']=$stock_years;
        $out['ariel']=$ariel_years;
        $out['alpi']=$alpi_years;
        $out['mailine']=$mailine_years;
        $out['hits']=$hit_years;
        $out['others']=$other_years;
        $out['esp']=$esp_years;
        return $out;
    }

    private function _prepare_olddata($scryears, $custom=0) {
        $out=array();
        $yearidx=0;
        foreach ($scryears as $row) {
            $numord=$profit=$revenue=$profitpnts=0;
            $rob=$sage=$sean=0;
            $totals=$scryears[$yearidx]['totals'];
            $monthidx=0;
            foreach ($row['months'] as $mrow) {
                if (intval($mrow['numorders'])!=0) {
                    $numord+=$mrow['numorders'];
                    $revenue+=$mrow['revenue'];
                    $profit+=$mrow['profit'];
                    $profitpnts+=$mrow['profitpnts'];
                    if ($custom==1) {
                        $rob+=$mrow['rob'];
                        $sage+=$mrow['sage'];
                        $sean+=$mrow['sean'];
                    }
                    $totals['numorders_val']+=$mrow['numorders'];
                    $totals['profit_val']+=$mrow['profit'];
                    $totals['profitpnts_val']+=$mrow['profitpnts'];
                    $totals['revenue_val']+=$mrow['revenue'];
                    if ($custom==1) {
                        $totals['rob_val']+=$mrow['rob'];
                        $totals['sage_val']+=$mrow['sage'];
                        $totals['sean_val']+=$mrow['sean'];
                    }
                    // Recalc month data
                    if (floatval($mrow['revenue'])!=0) {
                        $profit_perc=round($mrow['profit']/$mrow['revenue']*100,0);
                        $row['months'][$monthidx]['profit_percent']=$profit_perc.'%';
                        $row['months'][$monthidx]['profit_class']=  orderProfitClass($profit_perc);
                        $row['months'][$monthidx]['revenue']=($mrow['revenue']>=10000 ? MoneyOutput($mrow['revenue'],0,',') : MoneyOutput($mrow['revenue'],2,''));
                        $row['months'][$monthidx]['profit']=($mrow['profit']>=10000 ? MoneyOutput($mrow['profit'],0,',') : MoneyOutput($mrow['profit'],2,''));
                        $row['months'][$monthidx]['profitpnts']=($mrow['profitpnts']>0 ? number_format($mrow['profitpnts'],0,'.',',').'pts' : '&nbsp;');
                    }
                }
                $monthidx++;
            }
            // Update totals
            $row['totals']=$totals;
            $scryears[$yearidx]['totals']=$totals;

            // Change Totals
            $row['totals']['avg_profit']=$row['totals']['avg_revenue']=$this->empty_show;

            if ($numord!=0) {
                $row['totals']['numorders']=$numord;
                $row['totals']['profit']=($profit >=10000 ? MoneyOutput($profit,0,',') : MoneyOutput($profit,2,''));
                $row['totals']['profitpnts']=($profitpnts>0 ? number_format($profitpnts,0,'.',',').'pts' : '&nbsp;');
                $row['totals']['revenue']=($revenue>=10000 ? MoneyOutput($revenue,0,',') : MoneyOutput($revenue,2,''));
                if ($custom==1){
                    $row['totals']['rob']=$rob;
                    $row['totals']['sage']=$sage;
                    $row['totals']['sean']=$sean;
                }
                $avg_revenue=($revenue/$numord);
                $row['totals']['avg_revenue']=MoneyOutput($avg_revenue,2,',');
                if ($row['profit_type']=='Points') {
                    $avg_profit=($profitpnts/$numord);
                    $row['totals']['avg_profit']=number_format($avg_profit,2,'.',',').'pts';
                } else {
                    $avg_profit=($profit/$numord);
                    $row['totals']['avg_profit']=  MoneyOutput($avg_profit,2,',');
                }
                if (floatval($revenue)!=0) {
                    $profit_perc=round($profit/$revenue*100,0);
                    $row['totals']['profit_percent']=$profit_perc.'%';
                    $row['totals']['profit_class']=orderProfitClass($profit_perc);
                }
            }
            if ($yearidx>0) {
                $prvidx=$yearidx-1;
                $prvtotals=$scryears[$prvidx]['totals'];
                if ($prvtotals['numorders_val']!=0) {
                    $row['totals']['numorders_diff']=round(($row['totals']['numorders_val']-$prvtotals['numorders_val'])/$prvtotals['numorders_val']*100,0).'%';
                } else {
                    if ($row['totals']['numorders_val']!=0) {
                        $row['totals']['numorders_diff']='100%';
                    }
                }
                if ($prvtotals['profit_val']!=0) {
                    $row['totals']['profit_diff']=round(($row['totals']['profit_val']-$prvtotals['profit_val'])/$prvtotals['profit_val']*100,0).'%';
                } else {
                    if ($row['totals']['profit_val']>0) {
                        $row['totals']['profit_diff']='100%';
                    } elseif ($row['totals']['profit_val']<0) {
                        $row['totals']['profit_diff']='-100%';
                    }
                }
                if ($prvtotals['profitpnts_val']!=0) {
                    $row['totals']['profitpnts_diff']=round(($row['totals']['profitpnts_val']-$prvtotals['profitpnts_val'])/$prvtotals['profitpnts_val']*100,0).'%';
                } else {
                    if ($row['totals']['profitpnts_val']>0) {
                        $row['totals']['profitpnts_diff']='100%';
                    } elseif ($row['totals']['profitpnts_val']<0) {
                        $row['totals']['profitpnts_diff']='-100%';
                    }
                }
                if ($prvtotals['revenue_val']!=0) {
                    $row['totals']['revenue_diff']=round(($row['totals']['revenue_val']-$prvtotals['revenue_val'])/$prvtotals['revenue_val']*100,0).'%';
                } else {
                    if ($row['totals']['revenue_val']>0) {
                        $row['totals']['revenue_diff']='100%';
                    } elseif ($row['totals']['revenue_val']<0) {
                        $row['totals']['revenue_diff']='-100%';
                    }
                }
                $prv_profit_perc=$cur_profit_perc=0;
                if ($row['totals']['revenue_val']!=0) {
                    $cur_profit_perc=round($row['totals']['profit_val']/$row['totals']['revenue_val']*100,0);
                }
                if ($prvtotals['revenue_val']!=0) {
                    $prv_profit_perc=round($prvtotals['profit_val']/$prvtotals['revenue_val']*100,0);
                }
                if ($prv_profit_perc!=0) {
                    $row['totals']['profit_percent_diff']=round(($cur_profit_perc-$prv_profit_perc)/$prv_profit_perc*100,0).'%';
                } else {
                    if ($cur_profit_perc>0) {
                        $row['totals']['profit_percent_diff']='100%';
                    } elseif ($cur_profit_perc<0) {
                        $row['totals']['profit_percent_diff']='-100%';
                    }
                }

                if ($custom==1) {
                    if ($prvtotals['rob_val']!=0) {
                        $row['totals']['rob_diff']=round(($row['totals']['rob_val']-$prvtotals['rob_val'])/$prvtotals['rob_val']*100,0).'%';
                    } else {
                        if ($row['totals']['rob_val']>0) {
                            $row['totals']['rob_diff']='100%';
                        }
                    }
                    if ($prvtotals['sage_val']!=0) {
                        $row['totals']['sage_diff']=round(($row['totals']['sage_val']-$prvtotals['sage_val'])/$prvtotals['sage_val']*100,0).'%';
                    } else {
                        if ($row['totals']['sage_val']>0) {
                            $row['totals']['sage_diff']='100%';
                        }
                    }
                    if ($prvtotals['sean_val']!=0) {
                        $row['totals']['sean_diff']=round(($row['totals']['sean_val']-$prvtotals['sean_val'])/$prvtotals['sean_val']*100,0).'%';
                    } else {
                        if ($row['totals']['sean_val']>0) {
                            $row['totals']['sean_diff']='100%';
                        }
                    }
                }
            }
            $out[]=$row;
            $yearidx++;
        }
        return $out;
    }

    public function get_bisness_dates() {
        $out=array();
        $now = strtotime(date('Y-m-d',time())); // or your date as well
        $yearbgn = strtotime(date('Y')."-01-01");
        $datediff=$now - $yearbgn;
        $daysdiff=floor($datediff/(60*60*24));
        $nxtyear=strtotime(date("Y-m-d", $yearbgn) . " +1year")-1;
        $yearsdays=floor(($nxtyear-$yearbgn)/(60*60*24));
        // $kf=($yearsdays/$daysdiff);

        $total_bankdays=BankDays($yearbgn, $nxtyear);
        $elaps_bankdays=BankDays($yearbgn, $now);

        $bankdays=($total_bankdays-$elaps_bankdays);

        $kf=($total_bankdays/$elaps_bankdays);
        $out['days']=$bankdays;
        $out['elaps']=$elaps_bankdays;
        $out['pacekf']=$kf;
        return $out;
    }

    public function get_newcustoms_salestypes($dates, $oldcustoms, $brand) {
        // Get Month Data
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldcustoms as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }
        // Select Custom Shaped SB
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.item_id', $this->config->item('custom_id'));
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $custom_monthdat=$this->db->get()->result_array();

        $customs=$custom_keys=array();
        foreach ($custom_monthdat as $row) {
            // 3 Special Users - Robert - 19, Sage -3, Sean - 1
            $startmonth=strtotime($row['repyear'].'-'.$row['repmonth'].'-01');
            $endmonth=strtotime(date("Y-m-d", $startmonth) . " +1 month");
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ',$endmonth);
            $this->db->where('o.order_usr_repic',19);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $robres=$this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ',$endmonth);
            $this->db->where('o.order_usr_repic',3);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $sageres=$this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ',$endmonth);
            $this->db->where('o.order_usr_repic',1);
            $seanres=$this->db->get()->row_array();
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }

            $customs[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>  floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
                'rob'=>$robres['cnt_orders'],
                'sage'=>$sageres['cnt_orders'],
                'sean'=>$seanres['cnt_orders'],
                'profit_type'=>$dates['profit_type'],
            );
            array_push($custom_keys, $row['repyear'].'-'.$row['repmonth']);
        }

        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi","oi.order_id=o.order_id");
        $this->db->where('o.item_id != ', $this->config->item('custom_id'));
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $custom_monthotherdat=$this->db->get()->result_array();

        foreach ($custom_monthotherdat as $row) {
            // 3 Special Users - Robert - 19, Sage -3, Sean - 1
            $startmonth=strtotime($row['repyear'].'-'.$row['repmonth'].'-01');
            $endmonth=strtotime(date("Y-m-d", $startmonth) . " +1 month");
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ',$endmonth);
            $this->db->where('o.order_usr_repic',19);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $robres=$this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ',$endmonth);
            $this->db->where('o.order_usr_repic',3);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $sageres=$this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ',$endmonth);
            $this->db->where('o.order_usr_repic',1);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $seanres=$this->db->get()->row_array();

            $key=$row['repyear'].'-'.$row['repmonth'];
            if (!in_array($key, $custom_keys)) {
                array_push($custom_keys, $key);
                $customs[]=array(
                    'year'=>$row['repyear'],
                    'month'=>$row['repmonth'],
                    'numorders'=>intval($row['cnt_orders']),
                    'revenue'=>floatval($row['revenue']),
                    'profit'=>  floatval($row['profit']),
                    'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
                    'rob'=>$robres['cnt_orders'],
                    'sage'=>$sageres['cnt_orders'],
                    'sean'=>$seanres['cnt_orders'],
                    'profit_type'=>$dates['profit_type'],
                );
            } else {
                $cidx=0;
                foreach ($customs as $crow) {
                    if ($crow['month']==$row['repmonth'] && $crow['year']==$row['repyear']) {
                        $customs[$cidx]['numorders']+=$row['cnt_orders'];
                        $customs[$cidx]['revenue']+=$row['revenue'];
                        $customs[$cidx]['profit']+=$row['profit'];
                        $customs[$cidx]['profitpnts']+=round(floatval($row['profit'])*$this->config->item('profitpts'),0);
                        $customs[$cidx]['rob']+=$robres['cnt_orders'];
                        $customs[$cidx]['sage']+=$sageres['cnt_orders'];
                        $customs[$cidx]['sean']+=$seanres['cnt_orders'];
                    } else {
                        $cidx++;
                    }
                }
            }
        }
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'CUSTOMS');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'CUSTOMS');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($customs, $custom_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'CUSTOMS');
        return $out;
    }

    public function get_newstock_salestypes($dates, $oldstocks, $brand) {
        // Get Month Data
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldstocks as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }

        // Stock shape
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $stock_monthdat=$this->db->get()->result_array();

        $stocks=$stock_keys=array();
        foreach ($stock_monthdat as $row) {
            $stocks[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>  floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($stock_keys,$row['repyear'].'-'.$row['repmonth']);
        }
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'STOCK');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'STOCK');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }

        $out=$this->_prepare_newdata($stocks, $stock_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'STOCK');
        return $out;
    }

    public function get_newariel_salestypes($dates, $oldariel, $brand) {
        // Get Month Data
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldariel as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }
        // Ariel Items
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
        $this->db->join("{$item_table} i",'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        $this->db->where('v.vendor_name','Ariel');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $ariel_monthdat=$this->db->get()->result_array();

        $ariels=$ariel_keys=array();
        foreach ($ariel_monthdat as $row) {
            $ariels[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>  floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($ariel_keys,$row['repyear'].'-'.$row['repmonth']);
        }
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'ARIEL');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'ARIEL');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($ariels, $ariel_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'ARIEL');
        return $out;
    }

    public function get_newalpi_salestypes($dates, $oldalpi, $brand) {
        // Get Month Data
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldalpi as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }
        // Get Alpi Data
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
        $this->db->join("{$item_table} i",'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        $this->db->where('v.vendor_name','Alpi');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $alpi_monthdat=$this->db->get()->result_array();

        $alpis=$alpi_keys=array();
        foreach ($alpi_monthdat as $row) {
            $alpis[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($alpi_keys,$row['repyear'].'-'.$row['repmonth']);
        }
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'ALPI');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'ALPI');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($alpis, $alpi_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'ALPI');
        return $out;
    }

    public function get_newmailine_salestypes($dates, $oldmailine, $brand) {
        // Get Month Data
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldmailine as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }
        // Mailine Items
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
        $this->db->join("{$item_table} i",'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        $this->db->where('v.vendor_name','Mailine');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $mailine_monthdat=$this->db->get()->result_array();

        $mailines=$mailine_keys=array();
        foreach ($mailine_monthdat as $row) {
            $mailines[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($mailine_keys,$row['repyear'].'-'.$row['repmonth']);
        }
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'MAILINE');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'MAILINE');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($mailines, $mailine_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'MAILINE');
        return $out;
    }

    public function get_newesp_salestypes($dates, $oldesp, $brand) {
        // Get Month Data
        $item_table='.sb_items';
        $vendoritem_table='sb_vendor_items';
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldesp as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }
        // ESP/Other Items
        $other_vendor=array(
            'Ariel','Alpi','Mailine','Hit',
        );
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
        $this->db->join("{$item_table} i",'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        $this->db->where_not_in('v.vendor_name',$other_vendor);
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $esp_monthdata=$this->db->get()->result_array();

        $esps=$esp_keys=array();
        foreach ($esp_monthdata as $row) {
            $esps[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($esp_keys,$row['repyear'].'-'.$row['repmonth']);
        }
        // Multy Items
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where_in('o.item_id', array($this->config->item('multy_id'),-4,-5));
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $multi_monthdata=$this->db->get()->result_array();

        foreach ($multi_monthdata as $row) {
            $key=array_search($row['repyear'].'-'.$row['repmonth'], $esp_keys);
            if ($key===FALSE) {
                // New Element
                array_push($esp_keys,$row['repyear'].'-'.$row['repmonth']);
                $esps[]=array(
                    'year'=>$row['repyear'],
                    'month'=>$row['repmonth'],
                    'numorders'=>intval($row['cnt_orders']),
                    'revenue'=>floatval($row['revenue']),
                    'profit'=>floatval($row['profit']),
                    'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
                );
            } else {
                $esps[$key]['numorders']+=intval($row['cnt_orders']);
                $esps[$key]['revenue']+=floatval($row['revenue']);
                $esps[$key]['profit']+=floatval($row['profit']);
                $esps[$key]['profitpnts']+=round(floatval($row['profit'])*$this->config->item('profitpts'),0);
            }
        }

        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'ESP');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'ESP');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($esps, $esp_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'ESP');
        return $out;
    }

    public function get_newhit_salestypes($dates, $oldhits, $brand) {
        // Get Month Data
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldhits as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }
        // Hits Items
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
        $this->db->join("{$item_table} i",'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        $this->db->where('v.vendor_name','Hit');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $hits_monthdata=$this->db->get()->result_array();

        $hits=$hit_keys=array();
        foreach ($hits_monthdata as $row) {
            $hits[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($hit_keys,$row['repyear'].'-'.$row['repmonth']);
        }
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'HIT');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'HIT');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($hits, $hit_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand, 'HITS');
        return $out;
    }

    public function get_newother_salestypes($dates, $oldothers, $brand) {
        // Get Month Data
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $year=date('Y');
        $month=date('m');
        $days=$dates['days'];
        $pacekf=$dates['pacekf'];
        $start=strtotime($year.'-01-01');
        // Previous year
        $prvyear=intval($year-1);
        $prvtotals=array(
            'numorders' => $this->empty_show,
            'profit_class' => 'empty',
            'profit_percent' => $this->empty_show,
            'profit' => $this->empty_show,
            'profitpnts' => $this->empty_show,
            'revenue' => $this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        foreach ($oldothers as $crow) {
            if ($crow['year']==$prvyear) {
                $prvtotals=$crow['totals'];
            }
        }

        // Other Items
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth",FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.item_id', $this->config->item('other_id'));
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $other_monthdata=$this->db->get()->result_array();

        $others=$other_keys=array();
        foreach ($other_monthdata as $row) {
            $others[]=array(
                'year'=>$row['repyear'],
                'month'=>$row['repmonth'],
                'numorders'=>intval($row['cnt_orders']),
                'revenue'=>floatval($row['revenue']),
                'profit'=>floatval($row['profit']),
                'profitpnts'=>round(floatval($row['profit'])*$this->config->item('profitpts'),0),
            );
            array_push($other_keys,$row['repyear'].'-'.$row['repmonth']);
        }

        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('sum(goal_orders) as goal_orders, sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->select('count(goal_order_id) as cnt_goals');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'OTHER');
            $goalres=$this->db->get()->row_array();
            $goalres['goal_order_id']=-1;
            if ($goalres['cnt_goals']==0) {
                $goalres['goal_orders']=0;
                $goalres['goal_revenue']=0;
                $goalres['goal_profit']=0;
            }
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', date('Y'));
            $this->db->where('goal_type', 'OTHER');
            $this->db->where('brand', $brand);
            $goalres=$this->db->get()->row_array();
        }
        $out=$this->_prepare_newdata($others, $other_keys, $goalres, $year, $month, $days, $pacekf, $prvtotals, $brand,  'OTHER');
        return $out;
    }

    private function _prepare_newdata($monthdata, $monthkeys, $goals, $year, $month, $days, $pacekf, $prvtotals, $brand, $goaltype) {
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';

        $totals=array(
            'numorders'=>$this->empty_show,
            'profit_class'=>'empty',
            'profit_percent'=>$this->empty_show,
            'profit'=>$this->empty_show,
            'profitpts'=>$this->empty_show,
            'revenue'=>$this->empty_show,
            'avg_profit'=>$this->empty_show,
            'avg_profitpts'=>$this->empty_show,
            'avg_revenue'=>$this->empty_show,
            'numorders_val' => 0,
            'profit_val' => 0,
            'profitpnts_val' => 0,
            'revenue_val' => 0,
            /* Diff of values */
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );

        if ($goaltype=='CUSTOMS') {
            $totals['rob']=$totals['sage']=$totals['sean']=$this->empty_show;
            $totals['rob_val']=$totals['sage_val']=$totals['sean_val']=0;
        }
        $numorders=$profit=$revenue=$profitpts=0;
        $rob=$sage=$sean=0;
        $months=array();
        for ($j=1; $j<13; $j++) {
            if ($j>$month) {
                if ($goaltype=='CUSTOMS') {
                    $months[]=array(
                        'year' => $year,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'future',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpts'=>$this->empty_show,
                        'revenue' => $this->empty_show,
                        'rob'=>$this->empty_show,
                        'sage'=>$this->empty_show,
                        'sean'=>$this->empty_show,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    $months[]=array(
                        'year' => $year,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'future',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpts'=>$this->empty_show,
                        'revenue' => $this->empty_show,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                }
            } else {
                $key=array_search($year.'-'.str_pad($j,2,'0',STR_PAD_LEFT), $monthkeys);
                if ($key===FALSE) {
                    if ($goaltype=='CUSTOMS') {
                        $months[] = array(
                            'year' => $year,
                            'month' => $j,
                            'numorders' => $this->empty_show,
                            'profit_class' => 'empty',
                            'profit_percent' => $this->empty_show,
                            'profit' => $this->empty_show,
                            'profitpts'=>$this->empty_show,
                            'revenue' => $this->empty_show,
                            'rob'=>$this->empty_show,
                            'sage'=>$this->empty_show,
                            'sean'=>$this->empty_show,
                            'prvrevenuediff'=>$this->empty_show,
                            'prvrevenueclass'=>'',
                            'prvrevenueprc'=>$this->empty_show,
                            'prvrevenueshow'=>0,
                        );
                    } else {
                        $months[] = array(
                            'year' => $year,
                            'month' => $j,
                            'numorders' => $this->empty_show,
                            'profit_class' => 'empty',
                            'profit_percent' => $this->empty_show,
                            'profit' => $this->empty_show,
                            'profitpts'=>$this->empty_show,
                            'revenue' => $this->empty_show,
                            'prvrevenuediff'=>$this->empty_show,
                            'prvrevenueclass'=>'',
                            'prvrevenueprc'=>$this->empty_show,
                            'prvrevenueshow'=>0,
                        );
                    }
                } else {
                    $morders=intval($monthdata[$key]['numorders']);
                    if ($morders==0) {
                        if ($goaltype=='CUSTOMS') {
                            $months[] = array(
                                'year' => $year,
                                'month' => $j,
                                'numorders' => $this->empty_show,
                                'profit_class' => 'empty',
                                'profit_percent' => $this->empty_show,
                                'profit' => $this->empty_show,
                                'profitpts'=>$this->empty_show,
                                'revenue' => $this->empty_show,
                                'rob'=>$this->empty_show,
                                'sage'=>$this->empty_show,
                                'sean'=>$this->empty_show,
                                'prvrevenuediff'=>$this->empty_show,
                                'prvrevenueclass'=>'',
                                'prvrevenueprc'=>$this->empty_show,
                                'prvrevenueshow'=>0,
                            );
                        } else {
                            $months[] = array(
                                'year' => $year,
                                'month' => $j,
                                'numorders' => $this->empty_show,
                                'profit_class' => 'empty',
                                'profit_percent' => $this->empty_show,
                                'profit' => $this->empty_show,
                                'profitpts'=>$this->empty_show,
                                'revenue' => $this->empty_show,
                                'prvrevenuediff'=>$this->empty_show,
                                'prvrevenueclass'=>'',
                                'prvrevenueprc'=>$this->empty_show,
                                'prvrevenueshow'=>0,
                            );
                        }
                    } else {
                        $mprofit=floatval($monthdata[$key]['profit']);
                        $mprofitpts=floatval($monthdata[$key]['profitpnts']);
                        $mrevenue=floatval($monthdata[$key]['revenue']);
                        // Inclrease Year totals
                        $numorders+=$morders;
                        $profit+=$mprofit;
                        $profitpts+=$mprofitpts;
                        $revenue+=$mrevenue;
                        $prof_proc=round($mprofit/$mrevenue*100,0);
                        // Get previous year
                        $profit_type=(isset($monthdata[$key]['profit_type']) ? $monthdata[$key]['profit_type'] : 'Profit');
                        $i=intval($monthdata[$key]['year']);
                        $j=intval($monthdata[$key]['month']);
                        $nxtmnth=($j==12 ? 1 : $j+1);
                        $nxtyear=($j==12 ? $i : $i-1);
                        $prvyear=$i-1;
                        $prvrevenue=0;
                        $prvclass='';
                        $outrevenue='';
                        $outrevenueprc='';

                        if ($goaltype=='CUSTOMS') {
                            $rob+=$monthdata[$key]['rob'];
                            $sage+=$monthdata[$key]['sage'];
                            $sean+=$monthdata[$key]['sean'];
                            $months[]=array(
                                'year' => $year,
                                'month' => $j,
                                'numorders' => $morders,
                                'profit_class' => orderProfitClass($prof_proc),
                                'profit_percent' => $prof_proc.'%',
                                'profit' => ($mprofit>=10000 ? MoneyOutput($mprofit,0,',') : MoneyOutput($mprofit,2,'')),
                                'revenue' =>($mrevenue>=10000 ? MoneyOutput($mrevenue,0,',') : MoneyOutput($mrevenue,2,'')),
                                'profitpts'=>  number_format($mprofitpts,0,'.',',').'pts',
                                'rob'=>$monthdata[$key]['rob'],
                                'sage'=>$monthdata[$key]['sage'],
                                'sean'=>$monthdata[$key]['sean'],
                            );
                        } else {
                            $months[]=array(
                                'year' => $year,
                                'month' => $j,
                                'numorders' => $morders,
                                'profit_class' => orderProfitClass($prof_proc),
                                'profit_percent' => $prof_proc.'%',
                                'profit' => ($mprofit>=10000 ? MoneyOutput($mprofit,0,',') : MoneyOutput($mprofit,2,'')),
                                'revenue' =>($mrevenue>=10000 ? MoneyOutput($mrevenue,0,',') : MoneyOutput($mrevenue,2,'')),
                                'profitpts'=>  number_format($mprofitpts,0,'.',',').'pts',
                            );
                        }
                    }
                }
            }
        }

        if ($numorders!=0) {
            $totals['numorders']=$numorders;
            $totals['numorders_val']=$numorders;
            if ($revenue!=0) {
                $prof_proc=round($profit/$revenue*100,0);
                $avg_revenue=$revenue/$numorders;
                $avg_profit=$profit/$numorders;
                $avg_profitpts=$profitpts/$numorders;
                $totals['avg_profit']=  MoneyOutput($avg_profit,2,',');
                $totals['avg_profitpts']=number_format($avg_profitpts,0,'.',',').'pts';
                $totals['avg_revenue']=  MoneyOutput($avg_revenue,2,',');
                $totals['profit_percent']=$prof_proc.'%';
                $totals['profit_class']=  orderProfitClass($prof_proc);
                $totals['profit']=($profit>=10000 ? MoneyOutput($profit,0,',') : MoneyOutput($profit,2,''));
                $totals['profitpts']=  number_format($profitpts,0, '.',',').'pts';
                $totals['revenue']=($revenue>=10000 ? MoneyOutput($revenue,0,',') : MoneyOutput($revenue,2,''));
                $totals['profit_val']=$profit;
                $totals['profitpnts_val']=$profitpts;
                $totals['revenue_val']=$revenue;
                if ($goaltype=='CUSTOMS') {
                    $totals['rob']=$rob;
                    $totals['sage']=$sage;
                    $totals['sean']=$sean;
                    $totals['rob_val']=$rob;
                    $totals['sage_val']=$sage;
                    $totals['sean_val']=$sean;
                }
            }
        }
        if ($prvtotals['numorders_val']!=0) {
            $totals['numorders_diff']=round(($totals['numorders_val']-$prvtotals['numorders_val'])/$prvtotals['numorders_val']*100,0).'%';
        } else {
            if ($totals['numorders_val']!=0) {
                $totals['numorders_diff']='100%';
            }
        }
        if ($prvtotals['profit_val']!=0) {
            $totals['profit_diff']=round(($totals['profit_val']-$prvtotals['profit_val'])/$prvtotals['profit_val']*100,0).'%';
        } else {
            if ($totals['profit_val']>0) {
                $totals['profit_diif']='100%';
            } elseif ($totals['profit_val']<0) {
                $totals['profit_diif']='-100%';
            }
        }
        if ($prvtotals['profitpnts_val']!=0) {
            $totals['profitpnts_diff']=round(($totals['profitpnts_val']-$prvtotals['profitpnts_val'])/$prvtotals['profitpnts_val']*100,0).'%';
        } else {
            if ($totals['profitpnts_val']>0) {
                $totals['profitpnts_diff']='100%';
            } elseif ($totals['profitpnts_val']<0) {
                $totals['profitpnts_diff']='-100%';
            }
        }
        if ($prvtotals['revenue_val']!=0) {
            $totals['revenue_diff']=round(($totals['revenue_val']-$prvtotals['revenue_val'])/$prvtotals['revenue_val']*100,0).'%';
        } else {
            if ($totals['revenue_val']>0) {
                $totals['revenue_diff']='100%';
            } elseif ($totals['revenue_val']<0) {
                $totals['revenue_diff']='-100%';
            }
        }
        $cur_profitperc=$prv_profitperc=0;
        if ($prvtotals['revenue_val']>0) {
            $prv_profitperc=round($prvtotals['profit_val']/$prvtotals['revenue_val']*100,0);
        }
        if ($totals['revenue_val']>0) {
            $cur_profitperc=round($totals['profit_val']/$totals['revenue_val']*100,0);
        }
        if ($prv_profitperc!=0) {
            $totals['profit_percent_diff']=round(($cur_profitperc-$prv_profitperc)/$prv_profitperc*100,0).'%';
        } else {
            if ($cur_profitperc>0) {
                $totals['profit_percent_diff']='100%';
            } elseif ($cur_profitperc<0) {
                $totals['profit_percent_diff']='-100%';
            }
        }
        if ($goaltype=='CUSTOMS') {
            if ($prvtotals['rob_val']!=0) {
                $totals['rob_diff']=round(($totals['rob_val']-$prvtotals['rob_val'])/$prvtotals['rob_val']*100,0).'%';
            } else {
                if ($totals['rob_val']>0) {
                    $totals['rob_diff']='100%';
                }
            }
            if ($prvtotals['sage_val']!=0) {
                $totals['sage_diff']=round(($totals['sage_val']-$prvtotals['sage_val'])/$prvtotals['sage_val']*100,0).'%';
            } else {
                if ($totals['sage_val']>0) {
                    $totals['sage_diff']='100%';
                }
            }
            if ($prvtotals['sean_val']!=0) {
                $totals['sean_diff']=round(($totals['sean_val']-$prvtotals['sean_val'])/$prvtotals['sean_val']*100,0).'%';
            } else {
                if ($totals['sean_val']>0) {
                    $totals['sean_diff']='100%';
                }
            }
        }

        if (empty($goals)) {
            if ($brand!='ALL') {
                // Add New goals records
                $this->db->set('goal_year', $year);
                $this->db->set('goal_type', $goaltype);
                $this->db->set('goal_orders', round($numorders*$pacekf,0));
                $this->db->set('goal_revenue', round($revenue*$pacekf,2));
                $this->db->set('goal_profit', round($profit*$pacekf,2));
                $this->db->set('brand', $brand);
                $this->db->insert('ts_goal_orders');
                $goal_id=$this->db->insert_id();
                $goals=array(
                    'goal_order_id'=>$goal_id,
                    'goal_orders'=>round($numorders*$pacekf,0),
                    'goal_revenue'=>round($revenue*$pacekf,2),
                    'goal_profit'=>round($profit*$pacekf,2),
                    'goal_profitpts'=>round($profit*$pacekf*$this->config->item('profitpts'),0),
                );
            }
        } else {
            $goals['profitpts']=round($goals['goal_profit']*$this->config->item('profitpts'),0);
        }
        // Pace Hits
        $pacehits=array(
            'numorders'=>round($numorders*$pacekf,0),
            'profit'=>round($profit*$pacekf,2),
            'profitpts'=>round($profit*$pacekf*$this->config->item('profitpts'),0),
            'revenue'=>round($revenue*$pacekf,2),
            'numorders_val' => round($numorders*$pacekf,0),
            'profit_val' => round($profit*$pacekf,2),
            'profitpnts_val' => round($profit*$pacekf*$this->config->item('profitpts'),0),
            'revenue_val' => round($revenue*$pacekf,2),
            'numorders_diff'=>$this->empty_show,
            'profit_diff' =>$this->empty_show,
            'profitpnts_diff' => $this->empty_show,
            'profit_percent_diff'=>$this->empty_show,
            'revenue_diff' => $this->empty_show,
        );
        if ($goaltype=='CUSTOMS') {
            $pacehits['rob']=round($rob*$pacekf,0);
            $pacehits['sage']=round($sage*$pacekf,0);
            $pacehits['sean']=round($sean*$pacekf,0);
            $pacehits['rob_diff']=$this->empty_show;
            $pacehits['sage_diff']=$this->empty_show;
            $pacehits['sean_diff']=$this->empty_show;
        }
        if ($prvtotals['numorders_val']!=0) {
            $pacehits['numorders_diff']=round(($pacehits['numorders_val']-$prvtotals['numorders_val'])/$prvtotals['numorders_val']*100,0).'%';
        } else {
            if ($pacehits['numorders_val']!=0) {
                $pacehits['numorders_diff']='100%';
            }
        }
        if ($prvtotals['profit_val']!=0) {
            $pacehits['profit_diff']=round(($pacehits['profit_val']-$prvtotals['profit_val'])/$prvtotals['profit_val']*100,0).'%';
        } else {
            if ($pacehits['profit_val']>0) {
                $pacehits['profit_diif']='100%';
            } elseif ($pacehits['profit_val']<0) {
                $pacehits['profit_diif']='-100%';
            }
        }
        if ($prvtotals['profitpnts_val']!=0) {
            $pacehits['profitpnts_diff']=round(($pacehits['profitpnts_val']-$prvtotals['profitpnts_val'])/$prvtotals['profitpnts_val']*100,0).'%';
        } else {
            if ($pacehits['profitpnts_val']>0) {
                $pacehits['profitpnts_diff']='100%';
            } elseif ($pacehits['profitpnts_val']<0) {
                $pacehits['profitpnts_diff']='-100%';
            }
        }
        if ($prvtotals['revenue_val']!=0) {
            $pacehits['revenue_diff']=round(($pacehits['revenue_val']-$prvtotals['revenue_val'])/$prvtotals['revenue_val']*100,0).'%';
        } else {
            if ($pacehits['revenue_val']>0) {
                $pacehits['revenue_diff']='100%';
            } elseif ($pacehits['revenue_val']<0) {
                $pacehits['revenue_diff']='-100%';
            }
        }
        $cur_hitprofitperc=0;
        if ($pacehits['revenue_val']>0) {
            $cur_hitprofitperc=round($pacehits['profit_val']/$pacehits['revenue_val']*100,0);
        }
        if ($prv_profitperc!=0) {
            $pacehits['profit_percent_diff']=round(($cur_hitprofitperc-$prv_profitperc)/$prv_profitperc*100,0).'%';
        } else {
            if ($cur_hitprofitperc>0) {
                $pacehits['profit_percent_diff']='100%';
            } elseif ($cur_hitprofitperc<0) {
                $pacehits['profit_percent_diff']='-100%';
            }
        }
        if ($goaltype=='CUSTOMS') {
            if ($prvtotals['rob_val']!=0) {
                $pacehits['rob_diff']=round(($pacehits['rob']-$prvtotals['rob_val'])/$prvtotals['rob_val']*100,0).'%';
            } else {
                if ($pacehits['rob']>0) {
                    $pacehits['rob_diff']='100%';
                }
            }
            if ($prvtotals['sage_val']!=0) {
                $pacehits['sage_diff']=round(($pacehits['sage']-$prvtotals['sage_val'])/$prvtotals['sage_val']*100,0).'%';
            } else {
                if ($pacehits['sage']>0) {
                    $pacehits['sage_diff']='100%';
                }
            }
            if ($prvtotals['sean_val']!=0) {
                $pacehits['sean_diff']=round(($pacehits['sean']-$prvtotals['sean_val'])/$prvtotals['sean_val']*100,0).'%';
            } else {
                if ($pacehits['sean']>0) {
                    $pacehits['sean_diff']='100%';
                }
            }
        }


        // Calc avg
        $avg_data=array(
            'numorders'=>0,
            'profit'=>0,
            'profitpts'=>0,
            'revenue'=>0,
        );
        if ($days!=0) {
            $avg_data['numorders']=round(($goals['goal_orders']-$numorders)/$days,0);
            $avg_data['profit']=round(($goals['goal_profit']-$profit)/$days,2);
            $avg_data['profitpts']=round((ifset($goals,'profitpts',0) - (isset($profitpts) ? $profitpts : 0))/$days,2);
            $avg_data['revenue']=round(($goals['goal_revenue']-$revenue)/$days,2);
        }

        $year_data=array(
            'year'=>$year,
            'totals'=>$totals,
            'months'=>$months,
            'days'=>$days,
        );
        $out['data']=$year_data;
        // Out Pace to Hit
        if ($pacehits['revenue']==0) {
            $pace_profproc=$this->empty_show;
            $pace_class='empty';
        } else {
            $pace_profproc=round($pacehits['profit']/$pacehits['revenue']*100,0);
            $pace_class=orderProfitClass($pace_profproc);
        }
        $out['pacehits']=array(
            'numorders'=>($pacehits['numorders']==0 ? $this->empty_show : $pacehits['numorders']),
            'profit_class'=>$pace_class,
            'profit_percent'=>$pace_profproc.'%',
            'profit'=>($pacehits['profit']==0 ? $this->empty_show : ($pacehits['profit']>=10000 ? MoneyOutput($pacehits['profit'],0,',') : MoneyOutput($pacehits['profit'],2,''))),
            'profitpts'=>($pacehits['profitpts']==0 ? $this->empty_show : number_format($pacehits['profitpts'],0,',',',').'pts'),
            'revenue'=>($pacehits['revenue']==0 ? $this->empty_show : ($pacehits['revenue']>=10000 ? MoneyOutput($pacehits['revenue'],0,',') : MoneyOutput($pacehits['revenue'],2,''))),
            'numorders_diff'=>$pacehits['numorders_diff'],
            'profit_diff' =>$pacehits['profit_diff'],
            'profitpnts_diff' => $pacehits['profitpnts_diff'],
            'profit_percent_diff'=>$pacehits['profit_percent_diff'],
            'revenue_diff' => $pacehits['revenue_diff'],
        );
        if ($goaltype=='CUSTOMS') {
            $out['pacehits']['rob']=($pacehits['rob']==0 ? $this->empty_show : $pacehits['rob']);
            $out['pacehits']['sage']=($pacehits['sage']==0 ? $this->empty_show : $pacehits['sage']);
            $out['pacehits']['sean']=($pacehits['sean']==0 ? $this->empty_show : $pacehits['sean']);
            $out['pacehits']['rob_diff']=$pacehits['rob_diff'];
            $out['pacehits']['sage_diff']=$pacehits['sage_diff'];
            $out['pacehits']['sean_diff']=$pacehits['sean_diff'];
        }
        if ($goals['goal_revenue']==0) {
            $goal_profit_proc=$this->empty_show;
            $goal_class='empty';
        } else {
            $goal_profit_proc=round($goals['goal_profit']/$goals['goal_revenue']*100,0);
            $goal_class=orderProfitClass($goal_profit_proc);
        }
        // Out Goals
        $out['goals']=array(
            'goal_order_id'=>$goals['goal_order_id'],
            'goal_type'=>$goaltype,
            'numorders'=>($goals['goal_orders']==0 ? $this->empty_show : $goals['goal_orders']),
            'profit_class'=>$goal_class,
            'profit_percent'=>$goal_profit_proc.'%',
            'profit'=>($goals['goal_profit']==0 ? $this->empty_show : ($goals['goal_profit']>=10000 ? MoneyOutput($goals['goal_profit'],0,',') : MoneyOutput($goals['goal_profit'],2,''))),
            'profitpts'=>(ifset($goals,'profitpts',0)==0 ? $this->empty_show : number_format($goals['profitpts'],0,'.',',').'pts'),
            'revenue'=>($goals['goal_revenue']==0 ? $this->empty_show : ($goals['goal_revenue']>=10000 ? MoneyOutput($goals['goal_revenue'],0,',') : MoneyOutput($goals['goal_revenue'],2,''))),
        );
        // Out AVG data
        $out['avg']=array(
            'numorders'=>$avg_data['numorders'],
            'profit'=>  ($avg_data['profit'] >=10000 ? MoneyOutput($avg_data['profit'],0,',') : MoneyOutput($avg_data['profit'],2,'')),
            'revenue'=> ($avg_data['revenue']>=10000 ? MoneyOutput($avg_data['revenue'],0,'') : MoneyOutput($avg_data['revenue'],2,'')),
            'profitpts'=> number_format($avg_data['profitpts'],0,'.',',').'pts',
        );
        return $out;
    }

    public function get_sales_goaldata($goal_order_id) {
        $this->db->select('*');
        $this->db->from('ts_goal_orders');
        $this->db->where('goal_order_id', $goal_order_id);
        $goalres=$this->db->get()->row_array();
        if (!isset($goalres['goal_order_id'])) {

        }
        $out=array(
            'goal_order_id'=>$goalres['goal_order_id'],
            'goal_year'=>$goalres['goal_year'],
            'goal_orders'=>$goalres['goal_orders'],
            'goal_revenue'=>$goalres['goal_revenue'],
            'goal_profit'=>$goalres['goal_profit'],
            'goal_type'=>$goalres['goal_type'],
        );
        // Calc other params
        $goal_avgrevenue=$goal_avgprofit=0;
        if ($goalres['goal_orders']>0) {
            $goal_avgrevenue=($goalres['goal_revenue']/$goalres['goal_orders']);
            $goal_avgprofit=($goalres['goal_profit']/$goalres['goal_orders']);
        }
        $out['goal_avgrevenue']=($goal_avgrevenue==0 ? '&nbsp;' : '$'.number_format($goal_avgrevenue,2,'.',','));
        $out['goal_avgprofit']=($goal_avgprofit==0 ? '&nbsp;' : '$'.number_format($goal_avgprofit,2,'.',','));
        // Profit %
        $goal_avgprofit_perc=0;
        if ($goalres['goal_revenue']>0) {
            $goal_avgprofit_perc=($goalres['goal_profit']/$goalres['goal_revenue']*100);
        }
        $out['goal_avgprofit_perc']=($goal_avgprofit_perc==0 ? '&nbsp;' : number_format($goal_avgprofit_perc,1,'.',',').'%');
        $out['goal_profit_class']=orderProfitClass(round($goal_avgprofit_perc),0);
        return $out;
    }

    public function get_monthsales_details($month, $year, $salestype, $brand, $user_id) {
        $this->load->model('permissions_model');

        $out=array('result'=>$this->error_result, 'msg'=>$this->init_errmsg);
        $datbgn=strtotime($year.'-'.str_pad($month,2,'0', STR_PAD_LEFT).'-01');
        $datend=strtotime($year.'-'.str_pad($month,2,'0', STR_PAD_LEFT).'-01 +1 month');
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $this->db->select("date_format(from_unixtime(o.order_date),'%m/%d/%y') as order_date",FALSE);
        $this->db->select('o.order_num, o.customer_name, o.item_id, o.order_items, o.revenue, o.profit, o.order_cog, o.order_usr_repic');
        $this->db->from('ts_orders o');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $profit_type='';
        switch ($salestype) {
            case 'customs':
                $this->db->where('o.item_id', $this->config->item('custom_id'));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                // Get Other
                $this->db->select("date_format(from_unixtime(o.order_date),'%m/%d/%y') as order_date",FALSE);
                $this->db->select('o.order_num, o.customer_name, o.item_id, o.order_items, o.revenue, o.profit, o.order_cog, o.order_usr_repic');
                $this->db->from('ts_orders o');
                $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi","oi.order_id=o.order_id");
                $this->db->where('o.item_id != ', $this->config->item('custom_id'));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->order_by('o.order_date');
                $dataoth=$this->db->get()->result_array();
                $data=array_merge($data, $dataoth);

                $title='Custom Shaped Stress Balls orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalescustoms');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'stock':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                $title='Stock Shape Stress Balls orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalesstock');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'ariel':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('v.vendor_name','Ariel');
                $this->db->where('st.item_id is null');
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                $title='Ariel Stress Balls orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalesariel');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'alpi':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('v.vendor_name','Alpi');
                $this->db->where('st.item_id is null');
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                $title='Alpi Stress Balls orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalesalpi');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'mailine':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('v.vendor_name','Mailine');
                $this->db->where('st.item_id is null');
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                $title='Mailine Stress Balls orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalesmailine');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'esp':
                $other_vendor=array(
                    'Ariel','Alpi','Mailine','Hit',
                );
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where_not_in('v.vendor_name',$other_vendor);
                $this->db->where('st.item_id is null');
                $this->db->order_by('o.order_date');
                $data1=$this->db->get()->result_array();
                $this->db->select("date_format(from_unixtime(o.order_date),'%m/%d/%y') as order_date",FALSE);
                $this->db->select('o.order_num, o.customer_name, o.item_id, o.order_items, o.revenue, o.profit, o.order_cog');
                $this->db->from('ts_orders o');
                $this->db->where_in('o.item_id', array($this->config->item('multy_id'),-4,-5));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->order_by('o.order_date');
                $data2=$this->db->get()->result_array();
                $data=  array_merge($data1, $data2);
                $title='Other Stress Balls orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalesother');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'hits':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('o.is_canceled',0);
                $this->db->where('v.vendor_name','Hit');
                $this->db->where('st.item_id is null');
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                $title='Hit Items orders';
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsaleshit');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            case 'others':
                $title='ESP / Other Items orders';
                $this->db->where('o.item_id', $this->config->item('other_id'));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->order_by('o.order_date');
                $data=$this->db->get()->result_array();
                $profitdat=$this->permissions_model->get_pageprofit_view($user_id,'itemsalesesp');
                if (count($profitdat)==1) {
                    $profit_type=$profitdat[0]['profit_view'];
                }
                break;
            default :
                $this->db->get()->row_array();
                $out['msg']='Unknown type of Sales';
                return $out;
                break;
        }
        if (count($data)==0) {
            $out['msg']='Empty Data';
            return $out;
        }
        $out['result']=$this->success_result;
        $out['title']=$title.' ('.date('F, Y',$datbgn).')';
        $totals=array(
            'numorders'=>0,
            'profit'=>0,
            'profitpts'=>0,
            'revenue'=>0,
        );

        $orders=array();
        foreach ($data as $row) {
            $row['rowclass']='';
            if ($row['item_id']==$this->config->item('custom_id')) {
                $row['rowclass']='customitemorder';
            }
            $row['profitpts']=round($row['profit']*$this->config->item('profitpts'));
            if ($row['order_cog']=='') {
                $row['profit_class']='projprof';
                $row['profit_perc']=$this->config->item('default_profit').'%';
            } else {
                if ($row['revenue']==0) {
                    $row['profit_class']='';
                    $row['profit_perc']=$this->empty_show;
                } else {
                    $profit_perc=round($row['profit']/$row['revenue']*100,0);
                    $row['profit_class']=orderProfitClass($profit_perc);
                    $row['profit_perc']=$profit_perc.'%';
                }
            }
            $row['out_profit']=MoneyOutput($row['profit'],2,',');
            $row['out_revenue']=MoneyOutput($row['revenue'],2,',');
            $row['out_profitpts']=number_format($row['profitpts'],0,'.',',').'pts';
            $orders[]=$row;
            $totals['numorders']+=1;
            $totals['profit']+=$row['profit'];
            $totals['profitpts']+=$row['profitpts'];
            $totals['revenue']+=$row['revenue'];
        }
        $out['data']=$orders;
        if ($totals['revenue']==0) {
            $totals['profit_perc']=$this->empty_show;
            $totals['profit_class']='empty';
        } else {
            $profit_perc=round($totals['profit']/$totals['revenue']*100,0);
            $totals['profit_perc']=$profit_perc.'%';
            $totals['profit_class']=orderProfitClass($profit_perc);
        }
        $totals['out_profit']=MoneyOutput($totals['profit'],2,',');
        $totals['out_profitpts']=number_format($totals['profit'],0,'.',',').'pts';
        $totals['out_revenue']=MoneyOutput($totals['revenue'],2,',');
        $out['totals']=$totals;
        $out['profit_type']=$profit_type;
        // Grow Data
        return $out;
    }

    // Month details per Item
    public function get_monthsales_itemdetails($month, $year, $item_id, $brand) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->init_errmsg);
        if ($month==0) {
            $datbgn=strtotime($year.'-01-01');
            $datend=strtotime($year.'-01-01 +1 year');
        } else {
            $datbgn=strtotime($year.'-'.str_pad($month,2,'0', STR_PAD_LEFT).'-01');
            $datend=strtotime($year.'-'.str_pad($month,2,'0', STR_PAD_LEFT).'-01 +1 month');
        }
        $item_table='sb_items';
        $this->db->select('item_number, item_name');
        $this->db->from("{$item_table}");
        $this->db->where('item_id', $item_id);
        $itmres=$this->db->get()->row_array();
        if (!isset($itmres['item_number'])) {
            $out['msg']='Item Not Found';
            return $out;
        }
        $title=$itmres['item_number'].' '.$itmres['item_name'].' orders';
        if ($month==0) {
            $out['title']=$title.' ('.date('Y',$datbgn).')';
        } else {
            $out['title']=$title.' ('.date('F, Y',$datbgn).')';
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%m/%d/%y') as order_date",FALSE);
        $this->db->select('o.order_num, o.customer_name, o.item_id, o.order_qty, o.order_items, o.revenue, o.profit, o.order_cog');
        $this->db->from('ts_orders o');
        $this->db->where('o.item_id', $item_id);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $datbgn);
        $this->db->where('o.order_date < ', $datend);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->order_by('o.order_date');
        $data=$this->db->get()->result_array();
        if (count($data)==0) {
            $out['msg']='Empty Data';
            return $out;
        }
        $out['result']=$this->success_result;
        $totals=array(
            'numorders'=>0,
            'qty'=>0,
            'profit'=>0,
            'revenue'=>0,
        );
        $orders=array();
        foreach ($data as $row) {
            $row['rowclass']='';
            if ($row['item_id']==$this->config->item('custom_id')) {
                $row['rowclass']='customitemorder';
            }
            if ($row['order_cog']=='') {
                $row['profit_class']='proj';
                $row['profit_perc']=$this->config->item('default_profit').'%';
            } else {
                if ($row['revenue']==0) {
                    $row['profit_class']='';
                    $row['profit_perc']=$this->empty_show;
                } else {
                    $profit_perc=round($row['profit']/$row['revenue']*100,0);
                    $row['profit_class']=orderProfitClass($profit_perc);
                    $row['profit_perc']=$profit_perc.'%';
                }
            }
            $row['out_profit']=MoneyOutput($row['profit'],2,',');
            $row['out_revenue']=MoneyOutput($row['revenue'],2,',');
            $orders[]=$row;
            $totals['numorders']+=1;
            $totals['qty']+=$row['order_qty'];
            $totals['profit']+=$row['profit'];
            $totals['revenue']+=$row['revenue'];
        }
        $out['data']=$orders;
        if ($totals['revenue']==0) {
            $totals['profit_perc']=$this->empty_show;
            $totals['profit_class']='empty';
        } else {
            $profit_perc=round($totals['profit']/$totals['revenue']*100,0);
            $totals['profit_perc']=$profit_perc.'%';
            $totals['profit_class']=orderProfitClass($profit_perc);
        }
        $totals['out_profit']=MoneyOutput($totals['profit'],2,',');
        $totals['out_revenue']=MoneyOutput($totals['revenue'],2,',');
        $out['totals']=$totals;
        return $out;
    }

    // Additional cost per year
    public function get_addcost() {
        $this->db->select('*');
        $this->db->from('ts_itemsold_addcost');
        $res=$this->db->get()->row_array();
        if (!isset($res['itemsold_addcost_id'])) {
            $this->db->set('addcost',0);
            $this->db->insert('ts_itemsold_addcost');
            $retres=0;
        } else {
            $retres=floatval($res['addcost']);
        }
        return $retres;
    }

    // Update add cost
    public function update_itemsaleaddcost($data) {
        $out=array('result'=>$this->error_result,'msg'=>$this->init_errmsg);
        $this->db->select('*');
        $this->db->from('ts_itemsold_addcost');
        $res=$this->db->get()->row_array();
        if (!isset($res['itemsold_addcost_id'])) {
            $this->db->set('addcost',$data['addcost']);
            $this->db->insert('ts_itemsold_addcost');
        } else {
            $this->db->where('itemsold_addcost_id', $res['itemsold_addcost_id']);
            $this->db->set('addcost',$data['addcost']);
            $this->db->update('ts_itemsold_addcost');
        }
        $out['result']=$this->success_result;
        $itemchk=usersession('itemsaleschk');
        if (empty($itemchk)) {
            $out['totals']=0;
        } else {
            $data['checked']=$itemchk;
            $totals=$this->_get_totalcheck($data);
            usersession('itemsaleschk',$itemchk);
            $out['totals']=$totals;
        }

        // $this->_recount_imptsavings($year);
        return $out;
    }


    // Count
    public function itemsales_totals($options) {
        $brand = ifset($options,'brand','ALL');
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        if (isset($options['vendor_cost']) && $options['vendor_cost']=='avg') {
            $this->db->select('count(distinct(isale.item_id)) as cnt');
            $this->db->from('v_itemcogsales isale');
        } else {
            $this->db->select('count(distinct(isale.item_id)) as cnt');
            $this->db->from('v_itemsales isale');
        }
        $this->db->join("{$item_table} i",'i.item_id=isale.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        if (isset($options['vendor']) && !empty($options['vendor'])) {
            if ($options['vendor']=='other') {
                $vendexclude=$this->config->item('report_vendors');
                $this->db->where_not_in('v.vendor_name', $vendexclude);
            } else {
                $this->db->where('v.vendor_name',$options['vendor']);
            }
        }
        if (isset($options['search']) && !empty($options['search'])) {
            $this->db->like('concat(upper(i.item_number),upper(i.item_name))',$options['search']);
        }
        $this->db->where_in('isale.yearsale',array($options['curentyear'], $options['prevyear']));
        $this->db->where('isale.qtysale > ',0);
        if ($brand!='ALL') {
            $this->db->where('isale.brand', $brand);
        }
        $data=$this->db->get()->row_array();
        return $data['cnt'];
    }

    public function itemsales_checkitem($options, $chkflag=0) {
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $this->db->select('distinct(isale.item_id) as item_id');
        $this->db->from('v_itemsales isale');
        $this->db->join("{$item_table} i",'i.item_id=isale.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where_in('isale.yearsale',array($options['current_year'], $options['prev_year']));
        $this->db->where('isale.qtysale > ',0);
        if ($options['brand']!='ALL') {
            $this->db->where('isale.brand', $options['brand']);
        }
        $data=$this->db->get()->result_array();
        $out_array=array();
        foreach ($data as $row) {
            array_push($out_array, $row['item_id']);
        }
        usersession('itemsaleschk', $out_array);
        return TRUE;
    }

    // Get data
    public function itemsale_data($options) {
        $out=array('result'=>$this->error_result,'msg'=>$this->init_errmsg);
        $data=$this->_get_solditemdata($options);
        $items=array();
        foreach ($data as $row) {
            $row['out_curqty']=number_format($row['curqty'],0,'.',',');
            $row['out_prevqty']=number_format($row['prevqty'],0,'.',',');
            $row['diffclass']='';
            $diffqty=$row['curqty']-$row['prevqty'];
            if ($diffqty==0) {
                $row['out_difqty']='0';
            } else {
                $row['out_difqty']=($diffqty>0 ? '+': '-').number_format(abs($diffqty),0,'.',',');
                $row['diffclass']=($diffqty<0 ? 'negative' : '');
            }
            $difford=$row['curordsale']-$row['prevordsale'];
            $row['diffordclass']=($difford<0 ? 'negative' : '');
            if ($difford==0) {
                $row['difford']='0';
            } else {
                $row['difford']=($difford>0 ? '+'.$difford : $difford);
            }

            $row['out_currevenue']=MoneyOutput($row['currevenue'],0);
            $row['out_prevrevenue']=  MoneyOutput($row['prevrevenue'],0);
            $row['out_cost']=MoneyOutput($row['cost'],2);


            $row['out_cog']=MoneyOutput($row['cog'],0);
            $row['profitvalclass']=($row['profit']<0 ? 'negative' : '');
            $row['out_profit']=MoneyOutput($row['profit'],0);

            $row['out_imptcost']=$row['out_imptcog']=$row['out_imprpofit']=$row['out_savings']=$this->empty_show;
            // $row['out_imprpofitclass']=
            $row['savings_class']='';
            // $row['imptprofit_class']=$row['imptprofit_perc']=
            // Get imptcost
            if ($options['brand']=='ALL') {
                $row['ipcog']=0; // Temporary
            } else {
                $this->db->select('cost');
                $this->db->from('ts_itemsold_impts');
                $this->db->where('item_id', $row['item_id']);
                $imprcostdata=$this->db->get()->row_array();
                if (isset($imprcostdata['cost']) && floatval($imprcostdata['cost'])>0) {
                    $row['imptcost']=floatval($imprcostdata['cost']);
                    $row['out_imptcost']=MoneyOutput($imprcostdata['cost'],3);
                }
            }
            if ($row['ipcog']>0) {
                $row['out_imptcog']=MoneyOutput($row['ipcog'],0);
                $row['out_imprpofit']=MoneyOutput($row['iprofit'],0);
                $row['out_savings']=MoneyOutput($row['savings'],0);
                $row['savings_class']=($row['savings']<0 ? 'negative' : '');
            }
            $items[]=$row;
        }
        $out['data']=$items;
        $out['result']=$this->success_result;
        return $out;
    }

    // Get Item Import Cost
    public function get_itemimport_cost($item_id, $year=0, $brand='ALL') {
        $this->db->select('*');
        $this->db->from('ts_itemsold_impts');
        $this->db->where('item_id', $item_id);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        // $this->db->where('year', $year);
        $res=$this->db->get()->row_array();
        if (isset($res['itemsold_impt_id'])) {
            return $res['cost'];
        } else {
            return 0;
        }
    }

    // Save Import Cost
    public function save_itemimport_cost($options) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->init_errmsg);
        $this->db->select('*');
        $this->db->from('ts_itemsold_impts');
        $this->db->where('item_id', $options['item_id']);
        $this->db->where('brand',$options['brand']);
        $res=$this->db->get()->row_array();
        $imprcost_id=NULL;
        if (isset($res['itemsold_impt_id'])) {
            $this->db->where('itemsold_impt_id', $res['itemsold_impt_id']);
            if (floatval($options['cost'])==0) {
                $this->db->delete('ts_itemsold_impts');
            } else {
                $this->db->set('cost', floatval($options['cost']));
                $this->db->update('ts_itemsold_impts');
                $imprcost_id=$res['itemsold_impt_id'];
            }
        } else {
            if (floatval($options['cost'])>0) {
                $this->db->set('item_id', $options['item_id']);
                $this->db->set('cost', floatval($options['cost']));
                $this->db->set('brand', $options['brand']);
                $this->db->insert('ts_itemsold_impts');
                if (!$this->db->insert_id()) {
                    $out['msg']='Error During Insert Imprint Cost';
                    return $out;
                }
                $imprcost_id=$this->db->insert_id();
            }
        }
        $itmdata=$this->itemsale_data($options);
        $itmres=$itmdata['data'][0];
        $out['data']=$itmres;
        $out['result']=$this->success_result;
        // Get Data
        return $out;
    }

    // Check item
    public function itemsale_change_itemcheck($item_id, $chkres) {
        $chkitem=usersession('itemsaleschk');
        if (empty($chkitem)) {
            $chkitem=array();
        }
        if  ($chkres==0) {
            // Remove element
            $newitems=array();
            foreach ($chkitem as $row) {
                if ($row!=$item_id) {
                    array_push($newitems, $row);
                }
            }
            $chkitem=$newitems;
        } else {
            if (!in_array($item_id, $chkitem)) {
                array_push($chkitem, $item_id);
            }
        }
        // Save to session
        usersession('itemsaleschk', $chkitem);
        return count($chkitem);
    }

    public function _get_totalcheck($options) {
        $data=$this->_get_solditemdata($options);
        if (count($data)==0) {
            return array();
        }

        $totals=array(
            'curqty'=>0,
            'prevqty'=>0,
            'curordsale'=>0,
            'prevordsale'=>0,
            'currevenue'=>0,
            'prevrevenue'=>0,
            'cog'=>0,
            'profit'=>0,
            'ipcog'=>0,
            'iprofit'=>0,
            'savings'=>0,
        );
        foreach ($data as $row) {
            $totals['curqty']+=intval($row['curqty']);
            $totals['prevqty']+=intval($row['prevqty']);
            $totals['curordsale']+=intval($row['curordsale']);
            $totals['prevordsale']+=intval($row['prevordsale']);
            $totals['currevenue']+=floatval($row['currevenue']);
            $totals['prevrevenue']+=floatval($row['prevrevenue']);
            $totals['cog']+=floatval($row['cog']);
            $totals['profit']+=floatval($row['profit']);
            $totals['ipcog']+=floatval($row['ipcog']);
            $totals['iprofit']+=floatval($row['iprofit']);
            $totals['savings']+=floatval($row['savings']);
        }
        $totals['title_curqty']=$row['title_prevqty']='';
        $totals['out_curqty']=$totals['out_prevqty']=$this->empty_show;
        if ($totals['curqty']>=0) {
            $totaldat=$this->ItemSalesTotals($totals['curqty']);
            $totals['title_curqty']=$totaldat['title'];
            $totals['out_curqty']=$totaldat['data'];
        }
        if ($totals['prevqty']>0) {
            $totaldat=$this->ItemSalesTotals($totals['prevqty']);
            $totals['title_prevqty']=$totaldat['title'];
            $totals['out_prevqty']=$totaldat['data'];
        }
        $totals['title_curordsale']=$row['title_prevordsale']='';
        $totals['out_curordsale']=$totals['out_prevordsale']=$this->empty_show;
        if ($totals['curordsale']>0) {
            $totaldat=$this->ItemSalesTotals($totals['curordsale']);
            $totals['title_curordsale']=$totaldat['title'];
            $totals['out_curordsale']=$totaldat['data'];
        }
        if ($totals['prevordsale']>0) {
            $totaldat=$this->ItemSalesTotals($totals['prevordsale']);
            $totals['title_prevordsale']=$totaldat['title'];
            $totals['out_prevordsale']=$totaldat['data'];
        }
        $diffqty=$totals['curqty']-$totals['prevqty'];
        $totals['diffclass']='';
        if ($diffqty==0) {
            $totals['out_difqty']='0';
        } else {
            $totals['out_difqty']=($diffqty>0 ? '+': '-').number_format(abs($diffqty),0,'.',',');
            $totals['diffclass']=($diffqty<0 ? 'negative' : '');
        }
        $difford=$totals['curordsale']-$totals['prevordsale'];
        $totals['diffordclass']=($difford<0 ? 'negative' : '');
        if ($difford==0) {
            $totals['difford']='0';
        } else {
            $totals['difford']=($difford>0 ? '+'.$difford : $difford);
        }
        // Revenue
        $totals['title_currevenue']=$row['title_prevrevenue']='';
        $totals['out_currevenue']=$totals['out_prevrevenue']=$this->empty_show;
        if ($totals['currevenue']>0) {
            $totaldat=$this->ItemSalesTotals($totals['currevenue'],1);
            $totals['title_currevenue']=$totaldat['title'];
            $totals['out_currevenue']=$totaldat['data'];
        }
        if ($totals['prevrevenue']>0) {
            $totaldat=$this->ItemSalesTotals($totals['prevrevenue'],1);
            $totals['title_prevrevenue']=$totaldat['title'];
            $totals['out_prevrevenue']=$totaldat['data'];
        }
        $totals['title_cog']=$row['title_profit']='';
        $totals['out_cog']=$totals['out_profit']=$this->empty_show;
        if ($totals['cog']>0) {
            $totaldat=$this->ItemSalesTotals($totals['cog'],1);
            $totals['title_cog']=$totaldat['title'];
            $totals['out_cog']=$totaldat['data'];
        }
        if ($totals['profit']>0) {
            $totaldat=$this->ItemSalesTotals($totals['profit'],1);
            $totals['title_profit']=$totaldat['title'];
            $totals['out_profit']=$totaldat['data'];
        }
        $profit_perc=$profit_class='';
        if ($options['calc_year']==$options['current_year']) {
            if ($totals['currevenue']!=0) {
                $profit_perc=round($totals['profit']/$totals['currevenue']*100,0);
                $profit_class=orderProfitClass($profit_perc);
            }
        } else {
            if ($totals['prevrevenue']!=0) {
                $profit_perc=round($totals['profit']/$totals['prevrevenue']*100,0);
                $profit_class=orderProfitClass($profit_perc);
            }
        }
        $totals['profitvalclass']=($totals['profit']<0 ? 'negative' : '');
        $totals['profit_class']=$profit_class;
        $totals['profit_perc']=$profit_perc;
        $totals['title_imptcog']=$totals['title_imprpofit']=$totals['title_savings']=$totals['out_imprpofitclass']='';
        $totals['imptprofit_perc']=$totals['savings_class']=$totals['imptprofit_class']='';
        $totals['out_imptcog']=$totals['out_imprpofit']=$totals['out_savings']=$this->empty_show;
        if ($totals['ipcog']!=0) {
            $totaldat=$this->ItemSalesTotals($totals['ipcog'],1);
            $totals['title_imptcog']=$totaldat['title'];
            $totals['out_imptcog']=$totaldat['data'];
            if ($totals['iprofit']!=0) {
                $totaldat=$this->ItemSalesTotals($totals['iprofit'],1);
                $totals['title_imprpofit']=$totaldat['title'];
                $totals['out_imprpofit']=$totaldat['data'];
                $totals['out_imprpofitclass']=($totals['iprofit']<0 ? 'negative' : '');
            }

            if ($options['calc_year']==$options['current_year']) {
                if ($totals['currevenue']!=0) {
                    $iprofit_perc=round($totals['iprofit']/$totals['currevenue']*100,0);
                    $iprofit_class=orderProfitClass($iprofit_perc);
                }
            } else {
                if ($totals['prevrevenue']!=0) {
                    $iprofit_perc=round($totals['iprofit']/$totals['prevrevenue']*100,0);
                    $iprofit_class=orderProfitClass($iprofit_perc);
                }
            }
            $totals['imptprofit_class']=$iprofit_class;
            $totals['imptprofit_perc']=$iprofit_perc;
            if ($totals['savings']!=0) {
                $totaldat=$this->ItemSalesTotals($totals['savings'],1);
                $totals['title_savings']=$totaldat['title'];
                $totals['out_savings']=$totaldat['data'];
                $totals['savings_class']=($totals['savings']<0 ? 'negative' : '');
            }
        }
        return $totals;
    }

    public function _recount_imptsavings($year) {
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';

        $this->db->select('vi.vendor_item_cost as cost, impt.item_id, impt.itemsold_impt_id, impt.cost as imptcost');
        $this->db->select('vi.vendor_item_id, coalesce(salecur.qtysale, 0) as curqty, coalesce(salecur.revenue, 0) as currevenue',FALSE);
        $this->db->select('coalesce(salecur.shipsale, 0) as curshipsale, coalesce(salecur.ordsale, 0) as curordsale',FALSE);
        $this->db->from('ts_itemsold_impts impt');
        $this->db->join("{$item_table} i",'i.item_id=impt.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("(select item_id, qtysale, revenue, shipsale, ordsale from v_itemsales where yearsale={$year}) salecur",'salecur.item_id=i.item_id','left');
        $this->db->where('impt.year',$year);
        $res=$this->db->get()->result_array();
        $addcost=$this->get_addcost();
        foreach ($res as $row) {
            if ($row['imptcost']==0) {
                $savings=0;
            } else {
                $cost=$row['cost'];
                // Get max Cost for Item
                $this->db->select('max(vendorprice_color) as maxcost');
                $this->db->from('sb_vendor_prices');
                $this->db->where('vendor_item_id', $row['vendor_item_id']);
                $costres=$this->db->get()->row_array();
                if (floatval($costres['maxcost'])>0 && floatval($costres['maxcost'])>$cost) {
                    $cost=$costres['maxcost'];
                }
                $cog=$row['curqty']*$cost;
                $profit=$row['currevenue']-$cog-$row['curshipsale'];
                $imptcost=$row['imptcost'];
                $imptcog=$row['curqty']*($imptcost+$addcost);
                $iprofit=$row['currevenue']-$imptcog-$row['curshipsale'];
                $savings=$iprofit-$profit;
            }
            $this->db->set('savings', $savings);
            $this->db->where('itemsold_impt_id', $row['itemsold_impt_id']);
            $this->db->update('ts_itemsold_impts');
        }
        return TRUE;
    }

    public function get_item_mainimage($item_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->init_errmsg);

        $this->db->select('item_img_name, item_img_id');
        $this->db->from('sb_item_images');
        $this->db->where('item_img_item_id', $item_id);
        $this->db->order_by('item_img_order');
        $res=$this->db->get()->row_array();
        if (!isset($res['item_img_id'])) {
            $out['msg']='Image Not Found';
        } else {
            $out['result']=$this->success_result;
            $out['data']=$res;
        }
        return $out;
    }

    // Get a Total number of records
    public function get_itemmonth_total($options) {
        $item_table='sb_items';
        $this->db->select('count(distinct(isale.item_id)) as cnt');
        $this->db->from('v_itemsales isale');
        $this->db->join("{$item_table} i",'i.item_id=isale.item_id');
        if (isset($options['search']) && !empty($options['search'])) {
            $this->db->like('concat(upper(i.item_number),upper(i.item_name))',$options['search']);
        }
        $this->db->where('isale.yearsale >=',$options['startyear']);
        $this->db->where('isale.yearsale <=',$options['curentyear']);
        if ($options['brand']!='ALL') {
            $this->db->where('isale.brand', $options['brand']);
        }
        $this->db->where('isale.qtysale > ',0);
        $data=$this->db->get()->row_array();

        return $data['cnt'];
    }

    // Get Data about month sold
    public function get_itemmonthsolddata($options) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->init_errmsg);
        $current_year=$options['current_year'];
        $start_year=$options['start_year'];
        $years=array();
        for ($i=0; $i<10; $i++) {
            $chkyear=$current_year-$i;
            if ($chkyear<$start_year) {
                break;
            }
            array_push($years, $chkyear);
        }
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $this->db->select('i.item_id, i.item_number, i.item_name, v.vendor_name');
        $this->db->from('v_itemsales isale');
        $this->db->join("{$item_table} i",'i.item_id=isale.item_id');
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        if (isset($options['search']) && !empty($options['search'])) {
            $this->db->like('concat(upper(i.item_number),upper(i.item_name))',$options['search']);
        }
        $this->db->where('isale.yearsale >=', $start_year);
        $this->db->where('isale.yearsale <=', $current_year);
        $this->db->where('isale.qtysale > ',0);
        if ($options['brand']!=='ALL') {
            $this->db->where('isale.brand', $options['brand']);
        }
        if (isset($options['limit'])) {
            if (isset($options['offset']) && !empty($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        // Sort
        //order_fld	total
        // order_year	2015

        if (isset($options['sort_fld']) && !empty($options['sort_fld'])) {
            $this->db->select('coalesce(srt.sort_field,0) as sort_field',FALSE);
            $yearsort=(isset($options['sort_year']) ? $options['sort_year'] : $current_year);
            if ($options['sort_fld']=='total') {
                if ($options['brand']=='ALL') {
                    $joinsql="select item_id, sum(qtysale) as sort_field from v_itemsales where yearsale=".$yearsort.' group by item_id';
                } else {
                    $joinsql="select item_id, qtysale as sort_field from v_itemsales where yearsale=".$yearsort.' and brand=\''.$options['brand'].'\'';
                }
                $this->db->join("({$joinsql}) as srt",'srt.item_id=i.item_id','left');
            } else {
                $montshsort=$options['sort_fld'];
                $dbgn=strtotime($yearsort.'-'.$montshsort.'-01');
                if ($montshsort==12) {
                    $yearnxt=$yearsort+1;
                    $dend=strtotime($yearnxt.'-01-01');
                } else {
                    $monnxt=$montshsort+1;
                    $dend=strtotime($yearsort.'-'.$monnxt.'-01');
                }
                if ($options['brand']=='ALL') {
                    $joinsql="select item_id, sum(order_qty) as sort_field from ts_orders where order_date>=".$dbgn.' and order_date< '.$dend.' and is_canceled=0 group by item_id';
                } else {
                    $joinsql="select item_id, sum(order_qty) as sort_field from ts_orders where order_date>=".$dbgn.' and order_date< '.$dend.' and is_canceled=0 and brand=\''.$options['brand'].'\' group by item_id';
                }

                $this->db->join("({$joinsql}) as srt",'srt.item_id=i.item_id','left');
            }
            $this->db->order_by('sort_field desc');
        }
        $this->db->select('sum(isale.qtysale) as sumqty');
        $this->db->group_by('i.item_id');
        $data=$this->db->get()->result_array();

        $nbgn=(isset($options['offset']) ? $options['offset']+1 : 1);
        $items=array();
        foreach ($data as $row) {
            $row['numrec']=$nbgn;
            $nbgn++;
            $sales=array();
            foreach ($years as $yrow) {
                $yearrow=array(
                    'year'=>$yrow,
                    'orders'=>0,
                    'qty'=>0,
                    'Jan'=>0,
                    'Feb'=>0,
                    'Mar'=>0,
                    'Apr'=>0,
                    'May'=>0,
                    'Jun'=>0,
                    'Jul'=>0,
                    'Aug'=>0,
                    'Sep'=>0,
                    'Oct'=>0,
                    'Nov'=>0,
                    'Dec'=>0,
                );
                $dbgn=strtotime($yrow.'-01-01');
                $dend=strtotime(($yrow+1).'-01-01');
                $this->db->select("date_format(from_unixtime(o.order_date),'%b') as ordmonth, count(o.order_id) as cntord, sum(o.order_qty) as sumqty", FALSE);
                $this->db->from('ts_orders o');
                $this->db->where('o.order_date >= ', $dbgn);
                $this->db->where('o.order_date < ', $dend);
                $this->db->where('o.item_id', $row['item_id']);
                $this->db->where('o.is_canceled',0);
                $this->db->group_by('ordmonth');
                if ($options['brand']!=='ALL') {
                    $this->db->where('o.brand', $options['brand']);
                }
                $yres=$this->db->get()->result_array();
                foreach ($yres as $resrow) {
                    $yearrow['orders']+=$resrow['cntord'];
                    $yearrow['qty']+=$resrow['sumqty'];
                    $yearrow[$resrow['ordmonth']]+=$resrow['sumqty'];
                }
                $sales[]=$yearrow;
            }
            $row['sales']=$sales;
            $items[]=$row;
        }

        $out['result']=$this->success_result;
        $out['data']=$items;
        $out['years']=$years;
        return $out;
    }

    public function get_vendoritem_prices($item_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->init_errmsg);
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $this->db->select('i.item_id, i.item_number, i.item_name, v.vendor_name, i.vendor_item_id, vi.vendor_item_blankcost');
        $this->db->select('vi.vendor_item_cost, vi.vendor_item_exprint, vi.vendor_item_setup, vi.vendor_item_number');
        $this->db->from("{$item_table} i");
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('i.item_id', $item_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_id'])) {
            $out['res']='Item Not Found';
            return $out;
        }

        $this->db->select('*');
        $this->db->from('sb_vendor_prices');
        $this->db->where('vendor_item_id', $res['vendor_item_id']);
        $this->db->order_by('vendorprice_qty');
        $priceres=$this->db->get()->result_array();
        $out['result']=$this->success_result;
        $out['data']=$res;
        $out['prices']=$priceres;
        return $out;
    }

    private function _get_solditemdata($options) {
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $addcost=$this->get_addcost();
        // Prepare Left select with Impt Data
        if ($options['brand']!='ALL') {
            $imptsql="select i.item_id, i.cost, (i.cost+{$addcost})*v.qtysale as ipcog,";
            $imptsql.="v.revenue-(i.cost+{$addcost})*v.qtysale-v.shipsale as iprofit,";
            $imptsql.="(v.revenue-(i.cost+{$addcost})*v.qtysale-v.shipsale)-(v.revenue-v.qtysale*vi.vendor_item_cost-v.shipsale) as savings ";
            $imptsql.='from ts_itemsold_impts i ';
            if (isset($options['vendor_cost']) && $options['vendor_cost']=='avg') {
                $imptsql.='join v_itemcogsales v on v.item_id=i.item_id ';
            } else {
                $imptsql.='join v_itemsales v on v.item_id=i.item_id ';
            }
            $imptsql.="join {$item_table} itsp on itsp.item_id=i.item_id ";
            $imptsql.="join {$vendoritem_table} vi on vi.vendor_item_id=itsp.vendor_item_id ";
            $imptsql.="where v.yearsale={$options['calc_year']} and coalesce(i.cost,0)!=0 and v.brand='{$options['brand']}' and i.brand='{$options['brand']}'";
        }
        // Try to select
        $this->db->select('i.item_id, i.item_number, i.item_name, v.vendor_name, vi.vendor_item_cost as cost, vi.vendor_item_id');
        // Current year yeas qty
        $this->db->select('coalesce(salecur.qtysale, 0) as curqty, coalesce(salecur.revenue,0) as currevenue',FALSE);
        $this->db->select('coalesce(salecur.shipsale, 0) as curshipsale, coalesce(salecur.ordsale,0) as curordsale',FALSE);
        $this->db->select('coalesce(saleprev.qtysale, 0) as prevqty, coalesce(saleprev.revenue,0) as prevrevenue',FALSE);
        $this->db->select('coalesce(saleprev.shipsale, 0) as prevshipsale, coalesce(saleprev.ordsale,0) as prevordsale',FALSE);
        if ($options['brand']!='ALL') {
            $this->db->select('coalesce(impt.cost,0) as imptcost, coalesce(impt.savings,-9999999999) as savings',FALSE);
            $this->db->select('coalesce(impt.iprofit,0) as iprofit, coalesce(impt.ipcog,0) as ipcog',FALSE);
        }
        if (isset($options['vendor_cost']) && $options['vendor_cost']=='avg') {
            $this->db->select('coalesce(salecur.cogsale,0) as curcog, coalesce(saleprev.cogsale,0) as prevcog',FALSE);
        }
        $this->db->from("{$item_table} i");
        $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        if (isset($options['vendor_cost']) && $options['vendor_cost']=='avg') {
            if ($options['brand']=='ALL') {
                $this->db->join("(select item_id, sum(qtysale) as qtysale, sum(revenue) as revenue, sum(shipsale) as shipsale, sum(ordsale) as ordsale, sum(cogsale) as cogsale from v_itemsales where yearsale={$options['current_year']} group by item_id) salecur", 'salecur.item_id=i.item_id','left');
                $this->db->join("(select item_id, sum(qtysale) as qtysale, sum(revenue) as revenue, sum(shipsale) as shipsale, sum(ordsale) as ordsale, sum(cogsale) as cogsale from v_itemsales where yearsale={$options['prev_year']} group by item_id) saleprev", 'saleprev.item_id=i.item_id','left');
            } else {
                $this->db->join("(select item_id, qtysale, revenue, shipsale, ordsale, cogsale from v_itemsales where yearsale={$options['current_year']} and brand='{$options['brand']}') salecur", 'salecur.item_id=i.item_id','left');
                $this->db->join("(select item_id, qtysale, revenue, shipsale, ordsale, cogsale from v_itemsales where yearsale={$options['prev_year']} and brand='{$options['brand']}') saleprev", 'saleprev.item_id=i.item_id','left');
            }
        } else {
            if ($options['brand']=='ALL') {
                $this->db->join("(select item_id, sum(qtysale) as qtysale, sum(revenue) as revenue, sum(shipsale) as shipsale, sum(ordsale) as ordsale from v_itemsales where yearsale={$options['current_year']} group by item_id) salecur", 'salecur.item_id=i.item_id','left');
                $this->db->join("(select item_id, sum(qtysale) as qtysale, sum(revenue) as revenue, sum(shipsale) as shipsale, sum(ordsale) as ordsale from v_itemsales where yearsale={$options['prev_year']} group by item_id) saleprev", 'saleprev.item_id=i.item_id','left');
            } else {
                $this->db->join("(select item_id, qtysale, revenue, shipsale, ordsale from v_itemsales where yearsale={$options['current_year']} and brand='{$options['brand']}') salecur", 'salecur.item_id=i.item_id','left');
                $this->db->join("(select item_id, qtysale, revenue, shipsale, ordsale from v_itemsales where yearsale={$options['prev_year']} and brand='{$options['brand']}') saleprev", 'saleprev.item_id=i.item_id','left');
            }
        }
        if ($options['brand']!='ALL') {
            $this->db->join("({$imptsql}) impt",'impt.item_id=i.item_id','left');
        }
        if (isset($options['vendor']) && !empty($options['vendor'])) {
            if ($options['vendor']=='other') {
                $vendexclude=$this->config->item('report_vendors');
                $this->db->where_not_in('v.vendor_name', $vendexclude);
            } else {
                $this->db->where('v.vendor_name',$options['vendor']);
            }
        }
        if (isset($options['search']) && !empty($options['search'])) {
            $this->db->like('concat(upper(i.item_number),upper(i.item_name))',$options['search']);
        }
        if (isset($options['item_id'])) {
            $this->db->where('i.item_id', $options['item_id']);
        }
        $this->db->having('(prevqty > 0 or curqty > 0) ');
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['orderby'])) {
            switch ($options['orderby']) {
                case 'curyearqty':
                    $this->db->order_by('curqty','desc');
                    break;
                case 'prvyearqty':
                    $this->db->order_by('prevqty','desc');
                    break;
                case 'cursaved':
                    if ($options['brand']!=='ALL') {
                        $this->db->order_by('savings','desc');
                    }
                    break;
            }
        }
        if (isset($options['checked'])) {
            $this->db->where_in('i.item_id', $options['checked']);
        }
        $res=$this->db->get()->result_array();

        $items=array();

        foreach ($res as $row) {
            $profit_perc=$profit_class='';
            if ($options['vendor_cost']=='avg') {
                if ($options['calc_year']==$options['current_year']) {
                    $cog=$row['curcog'];
                    $row['cost']=($row['curqty']==0 ? 0 : $row['curcog']/$row['curqty']);
                    $profit=$row['currevenue']-$row['curcog']-$row['curshipsale'];
                    if ($row['currevenue']!=0) {
                        $profit_perc=round($profit/$row['currevenue']*100,0);
                        $profit_class=orderProfitClass($profit_perc);
                    }
                } else {
                    $cog=$row['prevcog'];
                    $row['cost']=($row['prevqty']==0 ? 0 : $row['prevcog']/$row['prevqty']);
                    $profit=$row['prevrevenue']-$cog-$row['prevshipsale'];
                    if ($row['prevrevenue']!=0) {
                        $profit_perc=round($profit/$row['prevrevenue']*100,0);
                        $profit_class=orderProfitClass($profit_perc);
                    }
                }
            } else {
                if ($options['vendor_cost']=='high') {
                    // Get max Cost for Item
                    $this->db->select('max(vendorprice_color) as maxcost');
                    $this->db->from('sb_vendor_prices');
                    $this->db->where('vendor_item_id', $row['vendor_item_id']);
                    $costres=$this->db->get()->row_array();
                    if (floatval($costres['maxcost'])>0 && floatval($costres['maxcost'])>$row['cost']) {
                        $row['cost']=$costres['maxcost'];
                    }
                } elseif ($options['vendor_cost']=='low') {
                    // Get min Cost for Item
                    $this->db->select('min(vendorprice_color) as maxcost');
                    $this->db->from('sb_vendor_prices');
                    $this->db->where('vendor_item_id', $row['vendor_item_id']);
                    $costres=$this->db->get()->row_array();
                    if (floatval($costres['maxcost'])>0 && floatval($costres['maxcost'])<$row['cost']) {
                        $row['cost']=$costres['maxcost'];
                    }
                }
                if ($options['calc_year']==$options['current_year']) {
                    $cog=$row['curqty']*$row['cost'];
                    $profit=$row['currevenue']-$cog-$row['curshipsale'];
                    if ($row['currevenue']!=0) {
                        $profit_perc=round($profit/$row['currevenue']*100,0);
                        $profit_class=orderProfitClass($profit_perc);
                    }
                } else {
                    $cog=$row['prevqty']*$row['cost'];
                    $profit=$row['prevrevenue']-$cog-$row['prevshipsale'];
                    if ($row['prevrevenue']!=0) {
                        $profit_perc=round($profit/$row['prevrevenue']*100,0);
                        $profit_class=orderProfitClass($profit_perc);
                    }
                }
            }
            $row['cog']=$cog;
            $row['profit_perc']=$profit_perc;
            $row['profit_class']=$profit_class;
            $row['profit']=$profit;
            $row['savings']=$this->empty_show;
            $row['imptprofit_perc']=$row['out_imprpofitclass']=$row['imptprofit_class']='';
            if ($options['brand']=='ALL') {
                $row['imptcost']=0;
            }
            if ($row['imptcost']>0) {
                $iprofit_perc=$iprofit_class='';
                $iprofit=$row['iprofit'];
                if ($options['calc_year']==$options['current_year']) {
                    if ($row['currevenue']!=0) {
                        $iprofit_perc=round($iprofit/$row['currevenue']*100,0);
                        $iprofit_class=orderProfitClass($iprofit_perc);
                    }
                } else {
                    if ($row['prevrevenue']!=0) {
                        $iprofit_perc=round($iprofit/$row['prevrevenue']*100,0);
                        $iprofit_class=  orderProfitClass($iprofit_perc);
                    }
                }
                // Add Impt Part
                $row['savings']=($iprofit - $profit);
                $row['imptprofit_perc']=$iprofit_perc;
                $row['imptprofit_class']=$iprofit_class;
                $row['out_imprpofitclass']=($iprofit<0 ? 'negative' : '');
            }
            $items[]=$row;
        }
        return $items;
    }

    public function get_rolerpt_limit() {
        $this->db->select('max(rolerpt_week_id) as max_week, min(rolerpt_week_id) as min_week, count(rolerpt_week_id) as cnt');
        $this->db->from('ts_rolerpt_weeks');
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function get_rolerpt_viewweeks($options=array()) {
        // Teams
        $this->db->select('*');
        $this->db->from('ts_rolerpt_teams');
        $this->db->order_by('owner desc, rolerep_team_id asc');
        $members=$this->db->get()->result_array();
        $ownerkeys=$teamkeas=array();
        foreach ($members as $row) {
            if ($row['owner']==1) {
                array_push($ownerkeys,$row['rolerep_team_id']);
            } else {
                array_push($teamkeas,$row['rolerep_team_id']);
            }
        }

        $limit=$this->config->item('show_rolerptweek');
        $this->db->select('rolerpt_week_id as week_id, week_num, week_year, datebgn, dateend');
        $this->db->from('ts_rolerpt_weeks');
        if (isset($options['maxweek'])) {
            $this->db->where('rolerpt_week_id <= ', $options['maxweek']);
        }
        $this->db->order_by('datebgn','desc');
        $this->db->limit($limit);
        $res=$this->db->get()->result_array();
        $weeks=$totals=$owners=$team=$unsign=$autoassig=array();
        foreach ($res as $vrow) {
            $monthbgn=date('m', $vrow['datebgn']);
            $monthend=date('m', $vrow['dateend']);
            if ($monthbgn!=$monthend) {
                $vrow['monthlabel']=date('M', $vrow['datebgn']).'/'.date('M', $vrow['dateend']);
            } else {
                $vrow['monthlabel']=date('M', $vrow['datebgn']);
            }
            $weeks[]=$vrow;
            // Get Totals
            $totalweek=$this->get_rolerpt_totals($vrow['week_id']);
            $totals[]=array(
                'week_id'=>$vrow['week_id'],
                'totals'=>$totalweek,
            );
            if ($totalweek==0) {
                $members=array();
                foreach ($ownerkeys as $mrow) {
                    $members[]=array(
                        'team_id'=>$mrow,
                        'totals'=>0,
                        'percent'=>$this->empty_show,
                    );
                }
                $owners[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>0,
                    'percent'=>$this->empty_show,
                    'members'=>$members,
                );
                $unsign[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>0,
                    'percent'=>$this->empty_show,
                );
                $teams=array();
                foreach ($teamkeas as $trow) {
                    $teams[]=array(
                        'team_id'=>$mrow,
                        'totals'=>0,
                        'percent'=>$this->empty_show,
                    );
                }
                $team[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>0,
                    'percent'=>$this->empty_show,
                    'members'=>$teams,
                );
                $autoassig[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>0,
                    'percent'=>$this->empty_show,
                );
            } else {
                $members=array();
                $ownertotal=0;
                foreach ($ownerkeys as $mrow) {
                    $totalmember=$this->get_rolerpt_totals($vrow['week_id'], $mrow);
                    $members[]=array(
                        'team_id'=>$mrow,
                        'totals'=>$totalmember,
                        'percent'=>($totalmember==0 ? $this->empty_show : round($totalmember/$totalweek*100,0).'%'),
                    );
                    $ownertotal+=$totalmember;
                }
                $owners[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>$ownertotal,
                    'percent'=>($ownertotal==0 ? $this->empty_show : round($ownertotal/$totalweek*100,0).'%'),
                    'members'=>$members,
                );
                $totalunusign=$this->get_rolerpt_totals($vrow['week_id'], 'unsig');
                $unsign[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>$totalunusign,
                    'percent'=>($totalunusign==0 ? $this->empty_show : round($totalunusign/$totalweek*100,0).'%'),
                );
                $totalauto=$this->get_rolerpt_totals($vrow['week_id'], 'auto');
                $autoassig[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>$totalauto,
                    'percent'=>($totalauto==0 ? $this->empty_show : round($totalauto/$totalweek*100,0).'%'),
                );
                $members=array();
                $teamtotal=0;
                foreach ($teamkeas as $mrow) {
                    $totalteam=$this->get_rolerpt_totals($vrow['week_id'], $mrow);
                    $members[]=array(
                        'team_id'=>$mrow,
                        'totals'=>$totalteam,
                        'percent'=>($totalteam==0 ? $this->empty_show : round($totalteam/$totalweek*100,0).'%'),
                    );
                    $teamtotal+=$totalteam;
                }
                $team[]=array(
                    'week_id'=>$vrow['week_id'],
                    'totals'=>$teamtotal,
                    'percent'=>($teamtotal==0 ? $this->empty_show : round($teamtotal/$totalweek*100,0).'%'),
                    'members'=>$members,
                );
            }
        }

        $out=array(
            'weeks'=>$weeks,
            'totals'=>$totals,
            'owners'=>$owners,
            'unsign'=>$unsign,
            'teams'=>$team,
            'auto'=>$autoassig,
        );
        return $out;
    }

    public function get_rolerpt_totals($rolerpt_week_id, $type='') {
        $total=0;
        $this->db->select('w.rolerpt_response_id, count(wt.rolerpt_weekdatateam_id) as assigncnt');
        $this->db->from('ts_rolerpt_weekdata w');
        $this->db->where('w.rolerpt_week_id', $rolerpt_week_id);
        $this->db->group_by('w.rolerpt_response_id');
        if (empty($type)) {
            $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id','left');
            $res=$this->db->get()->result_array();
            foreach ($res as $row) {
                if ($row['assigncnt']==0) {
                    $total+=1;
                } else {
                    $total+=$row['assigncnt'];
                }
            }
        } else {
            if ($type=='auto') {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->where('wt.autoassign', 1);
                $res=$this->db->get()->result_array();
                foreach ($res as $row) {
                    $total+=$row['assigncnt'];
                }
            } elseif ($type=='unsig') {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id','left');
                $this->db->having('assigncnt = 0');
                $res=$this->db->get()->result_array();
                foreach ($res as $row) {
                    $total+=1;
                }
            } elseif ($type=='owners') {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->join('ts_rolerpt_teams t','t.rolerep_team_id=wt.rolerep_team_id');
                $this->db->where('t.owner',1);
                $res=$this->db->get()->result_array();
                foreach ($res as $row) {
                    $total+=$row['assigncnt'];
                }
            } elseif ($type=='teams') {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->join('ts_rolerpt_teams t','t.rolerep_team_id=wt.rolerep_team_id');
                $this->db->where('t.owner',0);
                $res=$this->db->get()->result_array();
                foreach ($res as $row) {
                    $total+=$row['assigncnt'];
                }
            } else {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->where('rolerep_team_id', $type);
                $res=$this->db->get()->result_array();
                foreach ($res as $row) {
                    $total+=$row['assigncnt'];
                }
            }
        }
        return $total;
    }

    public function get_report_team($owner=0, $active=0) {
        $this->db->select('*');
        $this->db->from('ts_rolerpt_teams');
        $this->db->where('owner', $owner);
        if ($active!=0) {
            $this->db->where('active',1);
        }
        $this->db->order_by('rolerep_team_id asc');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_rolerpt_weekdata($week_id, $filtr='') {
        $out=array('result'=>  $this->error_result, 'msg'=>'Week Not Found');
        $unsigsql="select ww.rolerpt_weekdata_id, count(wt.rolerpt_weekdatateam_id) as cnt from ts_rolerpt_weekdata ww ";
        $unsigsql.="left join ts_rolerpt_weekdatateam wt ON wt.rolerpt_weekdata_id=ww.rolerpt_weekdata_id ";
        $unsigsql.="group by ww.rolerpt_weekdata_id having cnt=0";
        $this->db->select('*');
        $this->db->from('ts_rolerpt_weeks');
        $this->db->where('rolerpt_week_id', $week_id);
        $weekdata=$this->db->get()->row_array();
        if (isset($weekdata['rolerpt_week_id'])) {
            $out['result']=$this->success_result;
            $out['week']=$weekdata;
            // Get roles data
            $this->db->select('w.rolerpt_weekdata_id, w.rolerpt_response_id, r.respons_name, c.category_name');
            $this->db->from('ts_rolerpt_weekdata w');
            $this->db->join('ts_rolerpt_respons r','r.rolerpt_response_id=w.rolerpt_response_id');
            $this->db->join('ts_rolerpt_categories c','c.rolerpt_category_id=r.rolerpt_category_id');
            $this->db->where('w.rolerpt_week_id', $week_id);
            if ($filtr=='unsig') {
                $this->db->join("({$unsigsql}) ff","ff.rolerpt_weekdata_id=w.rolerpt_weekdata_id");
            } elseif ($filtr=='auto') {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->where('wt.autoassign',1);
            } elseif (!empty($filtr)) {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->where('wt.rolerep_team_id',$filtr);
            }
            $this->db->order_by('c.category_name, r.respons_name');
            $categ=$this->db->get()->result_array();

            // Get uncategor
            $this->db->select('w.rolerpt_weekdata_id, w.rolerpt_response_id, r.respons_name, \'No Category\' as category_name', FALSE);
            $this->db->from('ts_rolerpt_weekdata w');
            $this->db->join('ts_rolerpt_respons r','r.rolerpt_response_id=w.rolerpt_response_id');
            $this->db->where('w.rolerpt_week_id', $week_id);
            if ($filtr=='unsig') {
                $this->db->join("({$unsigsql}) ff","ff.rolerpt_weekdata_id=w.rolerpt_weekdata_id");
            } elseif ($filtr=='auto') {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->where('wt.autoassign',1);
            } elseif (!empty($filtr)) {
                $this->db->join('ts_rolerpt_weekdatateam wt','wt.rolerpt_weekdata_id=w.rolerpt_weekdata_id');
                $this->db->where('wt.rolerep_team_id',$filtr);
            }
            $this->db->where('r.rolerpt_category_id is NULL');
            $this->db->order_by('r.respons_name');
            $unsig=$this->db->get()->result_array();

            $details=array_merge($categ, $unsig);

            $outdetails=array();
            if (count($details)>0) {
                $numpp=1;
                $category=$details[0]['category_name'];
                foreach ($details as $row) {
                    $row['numpp']=$numpp;
                    if ($row['category_name']==$category && $numpp>1) {
                        $row['category_name']='&mdash;';
                    } else {
                        $category=$row['category_name'];
                    }
                    $row['numassign']=0;
                    $row['assignclass']='unassign';
                    $assign=array();
                    $this->db->select('w.rolerpt_weekdatateam_id, w.rolerep_team_id, w.autoassign, t.user_repl, t.owner');
                    $this->db->from('ts_rolerpt_weekdatateam w');
                    $this->db->join('ts_rolerpt_teams t','t.rolerep_team_id=w.rolerep_team_id','left');
                    $this->db->where('w.rolerpt_weekdata_id', $row['rolerpt_weekdata_id']);
                    $res=$this->db->get()->result_array();
                    if (count($res)>0) {
                        $row['numassign']=count($res);
                        $row['assignclass']='';
                        foreach ($res as $drow) {
                            if ($drow['autoassign']==1) {
                                $drow['user_repl']='Automated';
                                $drow['assigntype']='auto';
                                $drow['rolerep_team_id']='auto';
                            } else {
                                $drow['assigntype']=($drow['owner']==1 ? 'owner' : 'team');
                            }
                            $assign[]=$drow;
                        }
                    }
                    $row['assign']=$assign;
                    $outdetails[]=$row;
                    $numpp++;
                }

            }
            $out['details']=$outdetails;
        }
        return $out;
    }

    public function get_teamforassign() {
        $this->db->select('*');
        $this->db->from('ts_rolerpt_teams');
        $this->db->where('active',1);
        $this->db->order_by('owner desc, user_name asc');
        $res=$this->db->get()->result_array();
        $out=array();
        $out[]=array('team_id'=>'', 'user_name'=>'UNASSIGNED');
        $out[]=array('team_id'=>'auto','user_name'=>'Automated');
        foreach ($res as $row) {
            $out[]=array(
                'team_id'=>$row['rolerep_team_id'],
                'user_name'=>$row['user_name'],
            );
        }
        return $out;
    }

    // Get assigned
    public function get_assigned($weekdata_id) {
        $this->db->select('w.rolerpt_weekdatateam_id, w.rolerep_team_id, w.autoassign, t.user_repl, t.owner');
        $this->db->from('ts_rolerpt_weekdatateam w');
        $this->db->join('ts_rolerpt_teams t','t.rolerep_team_id=w.rolerep_team_id','left');
        $this->db->where('w.rolerpt_weekdata_id', $weekdata_id);
        $result=$this->db->get()->result_array();
        $out=array(
            'numassign'=>0,
            'assignclass'=>'unassign',
            'rolerpt_weekdata_id'=>$weekdata_id,
        );
        $assign=array();
        if (count($result)>0) {
            $out['numassign']=count($result);
            $out['assignclass']='';
            foreach ($result as $drow) {
                if ($drow['autoassign']==1) {
                    $drow['user_repl']='Automated';
                    $drow['assigntype']='auto';
                    $drow['rolerep_team_id']='auto';
                } else {
                    $drow['assigntype']=($drow['owner']==1 ? 'owner' : 'team');
                }
                $assign[]=$drow;
            }
        }
        $out['assign']=$assign;
        return $out;
    }


    // Save assign
    public function save_rolerpt_assign($week_id, $team_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Week Data Not Found');
        $this->db->select('wd.*');
        $this->db->from('ts_rolerpt_weekdata wd');
        $this->db->where('wd.rolerpt_weekdata_id', $week_id);
        $weekdat=$this->db->get()->row_array();
        if (isset($weekdat['rolerpt_week_id'])) {
            $out['result']=$this->success_result;
            $out['week']=$weekdat['rolerpt_week_id'];
            // Unique
            if ($team_id!='') {
                $this->db->select('count(*) as cnt');
                $this->db->from('ts_rolerpt_weekdatateam');
                $this->db->where('rolerpt_weekdata_id', $week_id);
                if ($team_id=='auto') {
                    $this->db->where('autoassign',1);
                } else {
                    $this->db->where('rolerep_team_id', $team_id);
                }
                $chkres=$this->db->get()->row_array();
                if ($chkres['cnt']==0) {
                    // New assign
                    if ($team_id=='auto') {
                        $this->db->set('autoassign',1);
                    } else {
                        $this->db->set('rolerep_team_id', $team_id);
                    }
                    $this->db->set('rolerpt_weekdata_id', $week_id);
                    $this->db->insert('ts_rolerpt_weekdatateam');
                }
            }
            // Rebuild totals, etc
            $totals=$this->_recalc_rolerpt_weekdata($out['week']);
            $out['totals']=$totals;
        }
        return $out;
    }

    public function remove_rolerpt_assign($week_id, $assignteam_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Week Data Not Found');
        $this->db->select('wd.*');
        $this->db->from('ts_rolerpt_weekdata wd');
        $this->db->where('wd.rolerpt_weekdata_id', $week_id);
        $weekdat=$this->db->get()->row_array();
        if (isset($weekdat['rolerpt_week_id'])) {
            $out['result']=$this->success_result;
            $out['week']=$weekdat['rolerpt_week_id'];
            $this->db->where('rolerpt_weekdatateam_id', $assignteam_id);
            $this->db->delete('ts_rolerpt_weekdatateam');
            // Rebuild totals, etc
            $totals=$this->_recalc_rolerpt_weekdata($out['week']);
            $out['totals']=$totals;
        }
        return $out;
    }

    public function remove_rolerpt_responsibility($weekdata_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Week Data Not Found');
        $this->db->select('wd.*');
        $this->db->from('ts_rolerpt_weekdata wd');
        $this->db->where('wd.rolerpt_weekdata_id', $weekdata_id);
        $weekdat=$this->db->get()->row_array();
        if (isset($weekdat['rolerpt_week_id'])) {
            $out['result']=$this->success_result;
            $out['week']=$weekdat['rolerpt_week_id'];
            $this->db->where('rolerpt_weekdata_id', $weekdata_id);
            $this->db->delete('ts_rolerpt_weekdata');
            // Rebuild totals, etc
            $totals=$this->_recalc_rolerpt_weekdata($out['week']);
            $out['totals']=$totals;
        }
        return $out;
    }

    private function _recalc_rolerpt_weekdata($week_id) {
        $ownkeys=$this->get_report_team(1);
        $memberkeys=$this->get_report_team(0);
        $totals=array();
        $totalweek=$this->get_rolerpt_totals($week_id);
        $totals['totalweek']=$totalweek;
        // Owners
        $ownertotals=0;
        $ownerdata=array();
        foreach ($ownkeys as $mrow) {
            $membtotal=$this->get_rolerpt_totals($week_id, $mrow['rolerep_team_id']);
            $memberprc=($membtotal==0 ? $this->empty_show : round($membtotal/$totalweek*100,0).'%');
            $ownerdata[]=array(
                'team_id'=>$mrow['rolerep_team_id'],
                'totals'=>$membtotal,
                'percent'=>$memberprc,
            );
            $ownertotals+=$membtotal;
        }
        $totals['ownersdata']=$ownerdata;
        $totals['ownertotal']=$ownertotals;
        $totals['ownerpercent']=($ownertotals==0 ? $this->empty_show : round($ownertotals/$totalweek*100,0).'%');
        // Unassigned
        $unsigtotal=$this->get_rolerpt_totals($week_id, 'unsig');
        $totals['unsigtotal']=$unsigtotal;
        $totals['unsigperc']=($unsigtotal==0 ? $this->empty_show : round($unsigtotal/$totalweek*100,0).'%');
        // Team
        $teamtotals=0;
        $teamdata=array();
        foreach ($memberkeys as $mrow) {
            $membtotal=$this->get_rolerpt_totals($week_id, $mrow['rolerep_team_id']);
            $memberprc=($membtotal==0 ? $this->empty_show : round($membtotal/$totalweek*100,0).'%');
            $teamdata[]=array(
                'team_id'=>$mrow['rolerep_team_id'],
                'totals'=>$membtotal,
                'percent'=>$memberprc,
            );
            $teamtotals+=$membtotal;
        }
        $totals['teamsdata']=$teamdata;
        $totals['teamtotal']=$teamtotals;
        $totals['teampercent']=($teamtotals==0 ? $this->empty_show : round($teamtotals/$totalweek*100,0).'%');
        // Auto assign
        $autototals=$this->get_rolerpt_totals($week_id, 'auto');
        $totals['autototal']=$autototals;
        $totals['autopercent']=($autototals==0 ? $this->empty_show : round($autototals/$totalweek*100,0).'%');
        return $totals;
    }

    // search category
    public function search_roles_category($search) {
        $this->db->select('rolerpt_category_id as id, category_name as label');
        $this->db->from('ts_rolerpt_categories');
        $this->db->like('upper(category_name)', strtoupper($search));
        $this->db->order_by('category_name');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function search_roles_response($search, $category=0) {
        $this->db->select('rolerpt_response_id as id, respons_name as label');
        $this->db->from('ts_rolerpt_respons');
        $this->db->like('upper(respons_name)', strtoupper($search));
        if ($category>0) {
            $this->db->where('rolerpt_category_id', $category);
        }
        $this->db->order_by('respons_name');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_rolerep_responsedata($response_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Responsibility / Role Not Found');
        $this->db->select('r.rolerpt_response_id, r.respons_name, r.rolerpt_category_id, c.category_name');
        $this->db->from('ts_rolerpt_respons r');
        $this->db->join('ts_rolerpt_categories c','c.rolerpt_category_id=r.rolerpt_category_id','left');
        $this->db->where('r.rolerpt_response_id',$response_id);
        $res=$this->db->get()->row_array();
        if (isset($res['rolerpt_response_id'])) {
            $out['result']=$this->success_result;
            $out['data']=$res;
        }
        return $out;
    }

    public function get_weekresponsedata($weekdata) {
        $out=array('result'=>$this->error_result, 'msg'=>'Week Role Not Found');
        $this->db->select('wd.*, r.respons_name, c.category_name, c.rolerpt_category_id');
        $this->db->from('ts_rolerpt_weekdata wd');
        $this->db->join('ts_rolerpt_respons r','r.rolerpt_response_id=wd.rolerpt_response_id');
        $this->db->join('ts_rolerpt_categories c', 'c.rolerpt_category_id=r.rolerpt_category_id','left');
        $this->db->where('wd.rolerpt_weekdata_id',$weekdata);
        $res=$this->db->get()->row_array();
        if (isset($res['rolerpt_weekdata_id'])) {
            $out['result']=$this->success_result;
            $assigned=$teamlist=$this->empty_show;
            $data=array(
                'week'=>$res['rolerpt_week_id'],
                'weekdata'=>$weekdata,
                'category_id'=>(empty($res['rolerpt_category_id']) ? 0 : $res['rolerpt_category_id']),
                'response_id'=>$res['rolerpt_response_id'],
                'category'=>$res['category_name'],
                'response'=>$res['respons_name'],
                'assign'=>$assigned,
                'teamlist'=>$teamlist,

            );
            $out['data']=$data;
        }
        return $out;

    }

    // Save week data
    public function save_rolerpt_weekdata($data, $usr_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Enter Responsibility / Role');
        $week=$data['week'];
        $weekdata=$data['weekdata'];
        $category_id=$data['category_id'];
        $category_name=$data['category_name'];
        $response_id=$data['response_id'];
        $response_name=$data['response_name'];

        // Check
        if (!empty($response_name)) {
            if (empty($response_id)) {
                $response_id=$this->_save_rolerpt_response($response_name, $category_id, $category_name);
            } else {
                // Get Response Details
                $responsedat=$this->get_rolerep_responsedata($response_id);
                if ($responsedat['result']==$this->error_result) {
                    $response_id=0;
                }
            }
            $out['msg']='Responsibility / Role Not Found';
            if (!empty($response_id)) {
                $out['result']=$this->success_result;
                $out['week']=$week;
                // Check, that week data unique
                $this->db->select('count(rolerpt_weekdata_id) as cnt');
                $this->db->from('ts_rolerpt_weekdata');
                $this->db->where('rolerpt_week_id',$week);
                $this->db->where('rolerpt_response_id', $response_id);
                $chkres=$this->db->get()->row_array();
                if ($chkres['cnt']==0) {
                    if ($weekdata<=0) {
                        $this->db->set('user_add',$usr_id);
                        $this->db->set('user_edit',$usr_id);
                        $this->db->set('date_add', date('Y-m-d H:i:s'));
                        $this->db->set('rolerpt_week_id', $week);
                        $this->db->set('rolerpt_response_id', $response_id);
                        $this->db->insert('ts_rolerpt_weekdata');
                        $weekdata=$this->db->insert_id();
                    } else {
                        $this->db->where('rolerpt_weekdata_id', $weekdata);
                        $this->db->set('user_edit',$usr_id);
                        $this->db->set('rolerpt_response_id', $response_id);
                        $this->db->update('ts_rolerpt_weekdata');
                    }
                }
            }
        }
        return $out;
    }

    // Save Response
    private function _save_rolerpt_response($response_name, $category_id, $category_name) {
        // Check Response
        if (empty($category_id) && !empty($category_name)) {
            $this->db->select('max(rolerpt_category_id) as catid');
            $this->db->from('ts_rolerpt_categories');
            $this->db->where('upper(category_name)', strtoupper($category_name));
            $chkcat=$this->db->get()->row_array();
            if (!isset($chkcat['catid'])) {
                $this->db->set('category_name', $category_name);
                $this->db->insert('ts_rolerpt_categories');
                $category_id=$this->db->insert_id();
            } else {
                $category_id=$chkcat['catid'];
            }
        }
        $this->db->select('max(rolerpt_response_id) as respid');
        $this->db->from('ts_rolerpt_respons');
        if (!empty($category_id)) {
            $this->db->where('rolerpt_category_id', $category_id);
        } else {
            $this->db->where('rolerpt_category_id is NULL');
        }
        $this->db->where('upper(respons_name)', strtoupper($response_name));
        $chkresp=$this->db->get()->row_array();
        if (!isset($chkresp['respid'])) {
            // Save Response
            $this->db->set('respons_name', $response_name);
            if (!empty($category_id)) {
                $this->db->set('rolerpt_category_id', $category_id);
            }
            $this->db->insert('ts_rolerpt_respons');
            $retval=$this->db->insert_id();
        } else {
            $retval=$chkresp['respid'];
        }
        return $retval;
    }

    // Add New Week
    public function _check_current_week($user_id) {
        $curweek=date('W');
        $curyear=date('Y');
        $dats=getDatesByWeek($curweek,$curyear);
        $this->db->select('count(rolerpt_week_id) as cnt');
        $this->db->from('ts_rolerpt_weeks');
        $this->db->where('week_num', $curweek);
        $this->db->where('week_year', $curyear);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            $this->db->set('week_num',$curweek);
            $this->db->set('week_year',$curyear);
            $this->db->set('datebgn',$dats['start_week']);
            $this->db->set('dateend',$dats['end_week']);
            $this->db->insert('ts_rolerpt_weeks');
            // Get Week ID
            $week_id=$this->db->insert_id();
            // Get prewious week data
            // Get prewious week data
            $this->db->select('max(datebgn) as maxbgn');
            $this->db->from('ts_rolerpt_weeks');
            $this->db->where('datebgn < ',$dats['start_week']);
            $this->db->order_by('datebgn','desc');
            $minw=$this->db->get()->row_array();
            $this->db->select('*');
            $this->db->from('ts_rolerpt_weeks');
            $this->db->where('datebgn',$minw['maxbgn']);
            $chkres=$this->db->get()->row_array();
            $weekprv_id=$chkres['rolerpt_week_id'];
            $this->db->select('*');
            $this->db->from('ts_rolerpt_weekdata');
            $this->db->where('rolerpt_week_id', $weekprv_id);
            $weekdata=$this->db->get()->result_array();
            foreach ($weekdata as $wrow) {
                $this->db->set('user_add', $user_id);
                $this->db->set('date_add', date('Y-m-d H:i:s'));
                $this->db->set('user_edit', $user_id);
                $this->db->set('rolerpt_week_id', $week_id);
                $this->db->set('rolerpt_response_id', $wrow['rolerpt_response_id']);
                $this->db->insert('ts_rolerpt_weekdata');
                $newresponse=$this->db->insert_id();
                // Add assign
                $this->db->select('*');
                $this->db->from('ts_rolerpt_weekdatateam');
                $this->db->where('rolerpt_weekdata_id', $wrow['rolerpt_weekdata_id']);
                $teamres=$this->db->get()->result_array();
                foreach ($teamres as $trow) {
                    $this->db->set('rolerpt_weekdata_id', $newresponse);
                    $this->db->set('rolerep_team_id', (empty($trow['rolerep_team_id']) ? NULL : $trow['rolerep_team_id']));
                    $this->db->set('autoassign', $trow['autoassign']);
                    $this->db->insert('ts_rolerpt_weekdatateam');
                }
            }
        }
    }

    public function rolerpt_team_status($data) {
        $out=array('result'=>$this->error_result, 'msg'=>'Team Member Not Found');
        $this->db->select('*');
        $this->db->from('ts_rolerpt_teams');
        $this->db->where('rolerep_team_id', $data['team_id']);
        $res=$this->db->get()->row_array();
        if (isset($res['rolerep_team_id'])) {
            $out['result']=$this->success_result;
            $this->db->set('active', ($data['newstatus']=='active' ? 1 : 0));
            $this->db->where('rolerep_team_id', $res['rolerep_team_id']);
            $this->db->update('ts_rolerpt_teams');
        }
        return $out;
    }

    public function rolerpt_team_newmember($teamtype) {
        $out=array('result'=>$this->error_result, 'msg'=>'Team Member Not Found');
        $newdata=array(
            'rolerep_team_id'=>-1,
            'user_name'=>'',
            'user_repl'=>'',
            'owner'=>$teamtype,
            'active'=>1,
        );
        $out['result']=$this->success_result;
        $out['data']=$newdata;
        return $out;
    }

    public function rolerep_member_update($data) {
        $out=array('result'=>$this->error_result, 'msg'=>'Fill Team Member Data');
        if (!empty($data['user_repl']) && !empty($data['user_name'])) {
            $out['msg']='Enter Unique Team member';
            if ($this->_rolerpt_teammember_check($data)==TRUE) {
                // Insert update
                $this->db->set('user_repl', $data['user_repl']);
                $this->db->set('user_name', $data['user_name']);
                if ($data['team_id']<=0) {
                    $this->db->set('owner',$data['membertype']);
                    $this->db->insert('ts_rolerpt_teams');
                } else {
                    $this->db->where('rolerep_team_id', $data['team_id']);
                    $this->db->update('ts_rolerpt_teams');
                }
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    private function _rolerpt_teammember_check($data) {
        $result=FALSE;
        $this->db->select('count(rolerep_team_id) as cnt');
        $this->db->from('ts_rolerpt_teams');
        $this->db->where('upper(user_repl)', strtoupper($data['user_repl']));
        $this->db->where('rolerep_team_id != ', $data['team_id']);
        $chkrepl=$this->db->get()->row_array();
        if ($chkrepl['cnt']==0) {
            $this->db->select('count(rolerep_team_id) as cnt');
            $this->db->from('ts_rolerpt_teams');
            $this->db->where('upper(user_name)', strtoupper($data['user_name']));
            $this->db->where('rolerep_team_id != ', $data['team_id']);
            $chkname=$this->db->get()->row_array();
            if ($chkname['cnt']==0) {
                $result=TRUE;
            }
        }
        return $result;
    }

    public function get_rolerpt_chartdata() {
        $out=array('result'=>$this->error_result, 'msg'=>'Data Not Found');
        $datarows=array();
        // Get week data
        $this->db->select('w.week_num, w.week_year, w.datebgn, w.dateend, w.rolerpt_week_id, count(d.rolerpt_weekdata_id) as cnt');
        $this->db->from('ts_rolerpt_weeks w');
        $this->db->join('ts_rolerpt_weekdata d','d.rolerpt_week_id=w.rolerpt_week_id');
        $this->db->group_by('w.week_num, w.week_year, w.rolerpt_week_id');
        $this->db->order_by('w.week_year,w.week_num');
        $weekres=$this->db->get()->result_array();
        if (count($weekres)>0) {
            foreach ($weekres as $wrow) {
                $wlabel=date('M',$wrow['datebgn']).' '.date('j', $wrow['datebgn']).'-'.date('j', $wrow['dateend']).','.date('y', $wrow['datebgn']);
                $totals=$this->get_rolerpt_totals($wrow['rolerpt_week_id']);
                $owners=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'owners');
                $ownerperc=round($owners/$totals*100,2);
                $teams=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'teams');
                $teamsperc=round($teams/$totals*100,2);
                $auto=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'auto');
                $autoperc=round($auto/$totals*100,2);
                $unassig=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'unsig');
                $unassigperc=round($unassig/$totals*100,2);
                $datarows[]=array(
                    // $wrow['week_num'].'.'.substr($wrow['week_year'],-2),
                    $wlabel,
                    $ownerperc,
                    $wlabel.PHP_EOL.$ownerperc.' %',
                    $teamsperc,
                    $wlabel.PHP_EOL.$teamsperc.' %',
                    $autoperc,
                    $wlabel.PHP_EOL.$autoperc.' %',
                    $unassigperc,
                    $wlabel.PHP_EOL.$unassig.' %',
                );
            }
            $out['result']=$this->success_result;
            $out['data']=$datarows;
        }
        return $out;
    }

    public function get_rolerpt_chartdata_old() {
        $out=array('result'=>$this->error_result, 'msg'=>'Data Not Found');
        $datarows=array();
        // owners with a red line, Team Members with a blue line, Automated with a green line, and Unassigned with a yellow (or dark yellow) line.
        $datarows[]=array('Week', 'Owners', 'Team Members','Automated','Unassigned');
        // Get week data
        $this->db->select('w.week_num, w.week_year, w.datebgn, w.dateend, w.rolerpt_week_id, count(d.rolerpt_weekdata_id) as cnt');
        $this->db->from('ts_rolerpt_weeks w');
        $this->db->join('ts_rolerpt_weekdata d','d.rolerpt_week_id=w.rolerpt_week_id');
        $this->db->group_by('w.week_num, w.week_year, w.rolerpt_week_id');
        $this->db->order_by('w.week_year,w.week_num');
        $weekres=$this->db->get()->result_array();
        if (count($weekres)>0) {
            foreach ($weekres as $wrow) {
                $wlabel=date('M',$wrow['datebgn']).' '.date('j', $wrow['datebgn']).'-'.date('j', $wrow['dateend']).','.date('y', $wrow['datebgn']);
                $totals=$this->get_rolerpt_totals($wrow['rolerpt_week_id']);
                $owners=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'owners');
                $ownerperc=round($owners/$totals*100,2);
                $teams=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'teams');
                $teamsperc=round($teams/$totals*100,2);
                $auto=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'auto');
                $autoperc=round($auto/$totals*100,2);
                $unassig=$this->get_rolerpt_totals($wrow['rolerpt_week_id'], 'unsig');
                $unassigperc=round($unassig/$totals*100,2);
                $datarows[]=array(
                    // $wrow['week_num'].'.'.substr($wrow['week_year'],-2),
                    $wlabel,
                    $ownerperc,
                    $teamsperc,
                    $autoperc,
                    $unassigperc,
                );
            }
            $out['result']=$this->success_result;
            $out['data']=$datarows;

        }
        return $out;
    }

    // Items Report - Section Customs - get old data
    private function _get_itemreport_customs_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {

        $profit_type = $usr_profitview;
        $cust_scryears=$customs=$custom_keys=array();

        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalescustoms') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.item_id', $this->config->item('custom_id'));
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $custom_monthdat = $this->db->get()->result_array();
        foreach ($custom_monthdat as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            // 3 Special Users - Robert - 19, Sage -3, Sean - 1
            $startmonth = strtotime($row['repyear'] . '-' . $row['repmonth'] . '-01');
            $endmonth = strtotime(date("Y-m-d", $startmonth) . " +1 month");
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ', $endmonth);
            $this->db->where('o.order_usr_repic', 19);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $robres = $this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ', $endmonth);
            $this->db->where('o.order_usr_repic', 3);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $sageres = $this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ', $endmonth);
            $this->db->where('o.order_usr_repic', 1);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $seanres = $this->db->get()->row_array();
            $customs[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
                'rob' => $robres['cnt_orders'],
                'sage' => $sageres['cnt_orders'],
                'sean' => $seanres['cnt_orders'],
            );
            array_push($custom_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi", "oi.order_id=o.order_id");
        $this->db->where('o.item_id != ', $this->config->item('custom_id'));
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $custom_monthotherdat = $this->db->get()->result_array();

        foreach ($custom_monthotherdat as $row) {
            // 3 Special Users - Robert - 19, Sage -3, Sean - 1
            $startmonth = strtotime($row['repyear'] . '-' . $row['repmonth'] . '-01');
            $endmonth = strtotime(date("Y-m-d", $startmonth) . " +1 month");
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ', $endmonth);
            $this->db->where('o.order_usr_repic', 19);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $robres = $this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ', $endmonth);
            $this->db->where('o.order_usr_repic', 3);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $sageres = $this->db->get()->row_array();
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->from('ts_orders o');
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $startmonth);
            $this->db->where('o.order_date < ', $endmonth);
            $this->db->where('o.order_usr_repic', 1);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $seanres = $this->db->get()->row_array();
            $key = $row['repyear'] . '-' . $row['repmonth'];
            if (!in_array($key, $custom_keys)) {
                array_push($custom_keys, $key);
                $customs[] = array(
                    'year' => $row['repyear'],
                    'month' => $row['repmonth'],
                    'numorders' => intval($row['cnt_orders']),
                    'revenue' => floatval($row['revenue']),
                    'profit' => floatval($row['profit']),
                    'profitpnts' => round(floatval($row['profit']) * $this->config->item('profitpts'), 0),
                    'rob' => $robres['cnt_orders'],
                    'sage' => $sageres['cnt_orders'],
                    'sean' => $seanres['cnt_orders'],
                );
            } else {
                $cidx = 0;
                foreach ($customs as $crow) {
                    if ($crow['month'] == $row['repmonth'] && $crow['year'] == $row['repyear']) {
                        $customs[$cidx]['numorders'] += $row['cnt_orders'];
                        $customs[$cidx]['revenue'] += $row['revenue'];
                        $customs[$cidx]['profit'] += $row['profit'];
                        $customs[$cidx]['profitpnts'] += round(floatval($row['profit']) * $this->config->item('profitpts'), 0);
                        $customs[$cidx]['rob'] += $robres['cnt_orders'];
                        $customs[$cidx]['sage'] += $sageres['cnt_orders'];
                        $customs[$cidx]['sean'] += $seanres['cnt_orders'];
                    } else {
                        $cidx++;
                    }
                }
            }
        }

        // Manage Customs
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'rob' => $this->empty_show,
                'sage' => $this->empty_show,
                'sean' => $this->empty_show,
                /* Values */
                'numorders_val'=>0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                'rob_val' => 0,
                'sage_val' => 0,
                'sean_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
                'rob_diff' => $this->empty_show,
                'sage_diff' => $this->empty_show,
                'sean_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $custom_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'rob' => $this->empty_show,
                        'sage' => $this->empty_show,
                        'sean' => $this->empty_show,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get a previous revenue
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';


                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $customs[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $customs[$key]['profit'],
                        'profitpnts' => $customs[$key]['profitpnts'],
                        'revenue' => $customs[$key]['revenue'],
                        'profit_view' => $profit_type,
                        'rob' => $customs[$key]['rob'],
                        'sage' => $customs[$key]['sage'],
                        'sean' => $customs[$key]['sean'],
                    );
                }
            }
            $cust_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild Customs
        $cust_years = $this->_prepare_olddata($cust_scryears, 1);
        return $cust_years;
    }

    private function _get_itemreport_stock_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $stocks = $stock_keys = $stock_scryears = array();
        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalesstock') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $stock_monthdat = $this->db->get()->result_array();
        foreach ($stock_monthdat as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $stocks[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
            );
            array_push($stock_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Manage Stocks
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $stock_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $stocks[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $stocks[$key]['profit'],
                        'profitpnts' => $stocks[$key]['profitpnts'],
                        'revenue' => $stocks[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $stock_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild Stocks
        $stock_years = $this->_prepare_olddata($stock_scryears);
        return $stock_years;
    }

    private function _get_itemreport_ariel_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $ariels = $ariel_keys = $ariel_scryears = array();
        $item_table='.sb_items';
        $vendoritem_table='sb_vendor_items';

        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalesariel') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
        $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        $this->db->where('v.vendor_name', 'Ariel');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->where('st.item_id is null');
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $ariel_monthdat = $this->db->get()->result_array();

        foreach ($ariel_monthdat as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $ariels[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
            );
            array_push($ariel_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Manage Ariels
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $ariel_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';

                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $ariels[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $ariels[$key]['profit'],
                        'profitpnts' => $ariels[$key]['profitpnts'],
                        'revenue' => $ariels[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $ariel_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild ARIEL
        $ariel_years = $this->_prepare_olddata($ariel_scryears);
        return $ariel_years;
    }

    private function _get_itemreport_alpi_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $item_table = 'sb_items';
        $vendoritem_table = 'sb_vendor_items';

        $alpis = $alpi_keys = $alpi_scryears = array();
        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalesalpi') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
        $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        $this->db->where('v.vendor_name', 'Alpi');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $alpi_monthdat = $this->db->get()->result_array();

        foreach ($alpi_monthdat as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $alpis[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
            );
            array_push($alpi_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Manage Alpis
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $alpi_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $alpis[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $alpis[$key]['profit'],
                        'profitpnts' => $alpis[$key]['profitpnts'],
                        'revenue' => $alpis[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $alpi_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild Alpi
        $alpi_years = $this->_prepare_olddata($alpi_scryears);
        return $alpi_years;
    }

    private function _get_itemreport_mailine_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';

        $mailines = $mailine_keys = $mailine_scryears = array();
        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalesmailine') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
        $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        $this->db->where('v.vendor_name', 'Mailine');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $mailine_monthdat = $this->db->get()->result_array();

        foreach ($mailine_monthdat as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $mailines[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
            );
            array_push($mailine_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Manage Mailines
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $mailine_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $mailines[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $mailines[$key]['profit'],
                        'profitpnts' => $mailines[$key]['profitpnts'],
                        'revenue' => $mailines[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $mailine_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild Mailine
        $mailine_years = $this->_prepare_olddata($mailine_scryears);
        return $mailine_years;
    }

    private function _get_itemreport_hits_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $item_table = 'sb_items';
        $vendoritem_table = 'sb_vendor_items';

        $hits = $hit_keys = $hit_scryears = array();
        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsaleshit') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
        $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        $this->db->where('v.vendor_name', 'Hit');
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $hits_monthdata = $this->db->get()->result_array();

        foreach ($hits_monthdata as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $hits[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
            );
            array_push($hit_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Manage Hits
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff' => $this->empty_show,
                'profit_diff' => $this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff' => $this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $hit_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $hits[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $hits[$key]['profit'],
                        'profitpnts' => $hits[$key]['profitpnts'],
                        'revenue' => $hits[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $hit_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild Hit
        $hit_years = $this->_prepare_olddata($hit_scryears);
        return $hit_years;
    }

    private function _get_itemreport_other_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $others = $other_keys = $other_scryears = array();
        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalesother') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.item_id', $this->config->item('other_id'));
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $other_monthdata = $this->db->get()->result_array();

        foreach ($other_monthdata as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $others[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => $profitval,
                'profitpnts' => $profitpnts,
            );
            array_push($other_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Manage Others
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $other_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $others[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $others[$key]['profit'],
                        'profitpnts' => $others[$key]['profitpnts'],
                        'revenue' => $others[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $other_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild Others
        $other_years = $this->_prepare_olddata($other_scryears);
        return $other_years;
    }

    private function _get_itemreport_esp_old($usr_profitview, $profitview, $start_date, $end_report, $brand) {
        $item_table = 'sb_items';
        $vendoritem_table = 'sb_vendor_items';

        $esps = $esp_keys = $esp_scryears = array();
        $other_vendor = array(
            'Ariel', 'Alpi', 'Mailine', 'Hit',
        );
        $profit_type = $usr_profitview;
        foreach ($profitview as $prow) {
            if ($prow['websys_page_link'] == 'itemsalesesp') {
                $profit_type = $prow['profit_view'];
            }
        }
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
        $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
        $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        $this->db->where_not_in('v.vendor_name', $other_vendor);
        $this->db->where('st.item_id is null');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $esp_monthdata = $this->db->get()->result_array();

        foreach ($esp_monthdata as $row) {
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            $esps[] = array(
                'year' => $row['repyear'],
                'month' => $row['repmonth'],
                'numorders' => intval($row['cnt_orders']),
                'revenue' => floatval($row['revenue']),
                'profit' => floatval($row['profit']),
                'profitpnts' => $profitpnts,
            );
            array_push($esp_keys, $row['repyear'] . '-' . $row['repmonth']);
        }
        // Multy Items
        $this->db->select("date_format(from_unixtime(o.order_date),'%Y') as repyear, date_format(from_unixtime(o.order_date),'%m') as repmonth", FALSE);
        $this->db->select('count(o.order_id) as cnt_orders, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where_in('o.item_id', array($this->config->item('multy_id'), -4, -5));
        $this->db->where('o.is_canceled', 0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $end_report);
        $this->db->group_by('repyear, repmonth');
        $this->db->order_by('repyear, repmonth');
        $multi_monthdata = $this->db->get()->result_array();

        foreach ($multi_monthdata as $row) {
            $key = array_search($row['repyear'] . '-' . $row['repmonth'], $esp_keys);
            $profitval = floatval($row['profit']);
            $profitpnts = round($profitval * $this->config->item('profitpts'), 0);
            if ($key === FALSE) {
                // New Element
                array_push($esp_keys, $row['repyear'] . '-' . $row['repmonth']);
                $esps[] = array(
                    'year' => $row['repyear'],
                    'month' => $row['repmonth'],
                    'numorders' => intval($row['cnt_orders']),
                    'revenue' => floatval($row['revenue']),
                    'profit' => $profitval,
                    'profitpnts' => $profitpnts,
                );
            } else {
                $esps[$key]['numorders'] += intval($row['cnt_orders']);
                $esps[$key]['revenue'] += floatval($row['revenue']);
                $esps[$key]['profit'] += $profitval;
                $esps[$key]['profitpnts'] += $profitpnts;
            }
        }
        // Manage ESP
        for ($i = $this->salestype_start; $i < intval(date('Y')); $i++) {
            $total = array(
                'numorders' => $this->empty_show,
                'profit_class' => 'empty',
                'profit_percent' => $this->empty_show,
                'profit' => $this->empty_show,
                'profitpnts' => $this->empty_show,
                'revenue' => $this->empty_show,
                'profit_view' => $profit_type,
                'numorders_val' => 0,
                'profit_val' => 0,
                'profitpnts_val' => 0,
                'revenue_val' => 0,
                /* Diff of values */
                'numorders_diff'=>$this->empty_show,
                'profit_diff' =>$this->empty_show,
                'profitpnts_diff' => $this->empty_show,
                'profit_percent_diff'=>$this->empty_show,
                'revenue_diff' => $this->empty_show,
            );
            $months = array();
            for ($j = 1; $j < 13; $j++) {
                $key = array_search($i . '-' . str_pad($j, 2, '0', STR_PAD_LEFT), $esp_keys);
                if ($key === FALSE) {
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $this->empty_show,
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $this->empty_show,
                        'profitpnts' => $this->empty_show,
                        'revenue' => $this->empty_show,
                        'profit_view' => $profit_type,
                        'prvrevenuediff'=>$this->empty_show,
                        'prvrevenueclass'=>'',
                        'prvrevenueprc'=>$this->empty_show,
                        'prvrevenueshow'=>0,
                    );
                } else {
                    // Get previous year
                    $nxtmnth=($j==12 ? 1 : $j+1);
                    $nxtyear=($j==12 ? $i : $i-1);
                    $prvyear=$i-1;
                    $prvrevenue=0;
                    $prvclass='';
                    $outrevenue='';
                    $outrevenueprc='';
                    $months[] = array(
                        'year' => $i,
                        'month' => $j,
                        'numorders' => $esps[$key]['numorders'],
                        'profit_class' => 'empty',
                        'profit_percent' => $this->empty_show,
                        'profit' => $esps[$key]['profit'],
                        'profitpnts' => $esps[$key]['profitpnts'],
                        'revenue' => $esps[$key]['revenue'],
                        'profit_view' => $profit_type,
                    );
                }
            }
            $esp_scryears[] = array(
                'year' => $i,
                'totals' => $total,
                'months' => $months,
                'profit_type' => $profit_type,
            );
        }
        // Rebuild ESP
        $esp_years = $this->_prepare_olddata($esp_scryears);
        return $esp_years;
    }

    private function _get_monthdata_grow($salestype, $totals, $datbgn, $datend) {
        $out=array('result'=>$this->error_result,'msg'=>'Data Not Found');
        $item_table='sb_items';
        $vendoritem_table='sb_vendor_items';
        $this->db->select('count(o.order_id) as numorders');
        $this->db->select('sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $datbgn);
        $this->db->where('o.order_date < ', $datend);
        switch ($salestype) {
            case 'customs':
                $this->db->where('o.item_id', $this->config->item('custom_id'));
                break;
            case 'stock':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id');
                break;
            case 'ariel':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('v.vendor_name','Ariel');
                $this->db->where('st.item_id is null');
                break;
            case 'alpi':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('v.vendor_name','Alpi');
                $this->db->where('st.item_id is null');
                break;
            case 'mailine':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('v.vendor_name','Mailine');
                $this->db->where('st.item_id is null');
                break;
            case 'esp':
                $other_vendor=array(
                    'Ariel','Alpi','Mailine','Hit',
                );
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where_not_in('v.vendor_name',$other_vendor);
                $this->db->where('st.item_id is null');
                break;
            case 'hits':
                $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
                $this->db->join("{$item_table} i",'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
                $this->db->where('v.vendor_name','Hit');
                $this->db->where('st.item_id is null');
                break;
            case 'others':
                $this->db->where('o.item_id', $this->config->item('other_id'));
                break;
        }
        $data=$this->db->get()->row_array();
        if ($salestype=='customs') {
            $this->db->select('count(o.order_id) as numorders');
            $this->db->select('sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi","oi.order_id=o.order_id");
            $this->db->where('o.item_id != ', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $datbgn);
            $this->db->where('o.order_date < ', $datend);
            $data2=$this->db->get()->row_array();
            $data['numorders']+=intval($data2['numorders']);
            $data['revenue']=floatval($data['revenue'])+floatval($data2['revenue']);
            $data['profit']= floatval($data['profit'])+floatval($data2['profit']);
        } elseif ($salestype=='esp') {
            $this->db->select('count(o.order_id) as cnt_orders');
            $this->db->select('sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            $this->db->where_in('o.item_id', array($this->config->item('multy_id'),-4,-5));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $datbgn);
            $this->db->where('o.order_date < ', $datend);
            $data2=$this->db->get()->row_array();
            $data['numorders']+=intval($data2['numorders']);
            $data['revenue']=floatval($data['revenue'])+floatval($data2['revenue']);
            $data['profit']= floatval($data['profit'])+floatval($data2['profit']);
        }
        if ($data['numorders']>0) {
            // Calculate diff
            $data['profitpts']=round(floatval($data['profit'])*$this->config->item('profitpts'),0);
            $grow=array(
                'numorders'=>$this->empty_show,
                'profit'=>$this->empty_show,
                'profitpts'=>$this->empty_show,
                'revenue'=>$this->empty_show,
                'profit_perc'=>$this->empty_show,
            );
            if ($salestype=='customs') {
                $grow['sean']=$grow['rob']=$grow['sage']=$this->empty_show;
                // Get data about users orders
                $this->db->select('count(o.order_id) as cnt_orders');
                $this->db->from('ts_orders o');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('o.item_id', $this->config->item('custom_id'));
                $this->db->where('o.order_usr_repic', 19);
                $robrea1=$this->db->get()->row_array();
                $this->db->select('count(o.order_id) as cnt_orders');
                $this->db->from('ts_orders o');
                $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi","oi.order_id=o.order_id");
                $this->db->where('o.item_id != ', $this->config->item('custom_id'));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('o.order_usr_repic', 19);
                $robrea2=$this->db->get()->row_array();
                $data['rob']=$robrea1['cnt_orders']+$robrea2['cnt_orders'];
                if ($data['rob']!=0) {
                    $grow['rob']=round(($totals['rob']-$data['rob'])/$data['rob']*100,0).'%';
                } else {
                    if ($totals['rob']>0) {
                        $grow['rob']='100%';
                    }
                }
                $this->db->select('count(o.order_id) as cnt_orders');
                $this->db->from('ts_orders o');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('o.item_id', $this->config->item('custom_id'));
                $this->db->where('o.order_usr_repic', 3);
                $sageres1=$this->db->get()->row_array();
                $this->db->select('count(o.order_id) as cnt_orders');
                $this->db->from('ts_orders o');
                $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi","oi.order_id=o.order_id");
                $this->db->where('o.item_id != ', $this->config->item('custom_id'));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('o.order_usr_repic', 3);
                $sageres2=$this->db->get()->row_array();
                $data['sage']=$sageres1['cnt_orders']+$sageres2['cnt_orders'];
                if ($data['sage']!=0) {
                    $grow['sage']=round(($totals['sage']-$data['sage'])/$data['sage']*100,0).'%';
                } else {
                    if ($totals['sage']>0) {
                        $grow['sage']='100%';
                    }
                }
                $this->db->select('count(o.order_id) as numorders');
                $this->db->from('ts_orders o');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('o.item_id', $this->config->item('custom_id'));
                $this->db->where('o.order_usr_repic', 1);
                $seanrea1=$this->db->get()->row_array();
                $this->db->select('count(o.order_id) as numorders');
                $this->db->from('ts_orders o');
                $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi","oi.order_id=o.order_id");
                $this->db->where('o.item_id != ', $this->config->item('custom_id'));
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $datbgn);
                $this->db->where('o.order_date < ', $datend);
                $this->db->where('o.order_usr_repic', 1);
                $seanrea2=$this->db->get()->row_array();
                $data['sean']=$seanrea1['numorders']+$seanrea2['numorders'];
                if ($data['sean']!=0) {
                    $grow['sean']=round(($totals['sean']-$data['sean'])/$data['sean']*100,0).'%';
                } else {
                    if ($totals['sean']>0) {
                        $grow['sean']='100%';
                    }
                }
            }
            if ($data['numorders']>0) {
                $grow['numorders']=round(($totals['numorders']-$data['numorders'])/$data['numorders']*100,0).'%';
            }
            if ($data['profit']!=0) {
                $grow['profit']=round(($totals['profit']-$data['profit'])/$data['profit']*100,0).'%';
            } else {
                if ($totals['profit']>0) {
                    $grow['profit']='100%';
                } elseif ($totals['profit']<0) {
                    $grow['profit']='-100%';
                }
            }
            if ($data['profitpts']!=0) {
                $grow['profitpts']=round(($totals['profitpts']-$data['profitpts'])/$data['profitpts']*100,0).'%';
            } else {
                if ($totals['profitpts']>0) {
                    $grow['profitpts']='100%';
                } elseif ($totals['profitpts']<0) {
                    $grow['profitpts']='-100%';
                }
            }
            if ($data['revenue']!=0) {
                $grow['revenue']=round(($totals['revenue']-$data['revenue'])/$data['revenue']*100,0).'%';
            } else {
                if ($totals['revenue']>0) {
                    $grow['revenue']='100%';
                } elseif ($totals['revenue']<0) {
                    $grow['revenue']='-100%';
                }
            }
            $out['result']=$this->success_result;
            $out['grow']=$grow;
        }
        return $out;
    }

    public function salesmonthdiff($month, $year, $salestype, $brand, $profit_view) {
        // Get previous year
        $item_table = 'sb_items';
        $vendoritem_table = 'sb_vendor_items';
        $other_vendor = array(
            'Ariel', 'Alpi', 'Mailine', 'Hit',
        );

        $nxtmnth=($month==12 ? 1 : $month+1);
        $nxtyear=($month==12 ? $year+1 : $year);
        $quoter=1;
        if ($month>=4 && $month<=7) {
            $quoter=2;
        } elseif ($month>=8 && $month<=9) {
            $quoter=3;
        } elseif ($month>=10 && $month<=12) {
            $quoter=4;
        }
        $curmnthstart=strtotime($year.'-'.$month.'-01');
        $curmnthfinish=strtotime($nxtyear.'-'.$nxtmnth.'-01');
        switch ($quoter) {
            case 1:
                $curquoterstart=strtotime($year.'-01-01');
                $curquoterfinish=strtotime($year.'-04-01');
                break;
            case 2:
                $curquoterstart=strtotime($year.'-04-01');
                $curquoterfinish=strtotime($year.'-07-01');
                break;
            case 3:
                $curquoterstart=strtotime($year.'-07-01');
                $curquoterfinish=strtotime($year.'-10-01');
                break;
            case 4:
                $curquoterstart=strtotime($year.'-10-01');
                $curquoterfinish=strtotime(($year+1).'-01-01');
                break;
        }
        $prvyear=$year-1;
        $nxtyear=($month==12 ? $year : $year-1);
        $prvmnthstart=strtotime($prvyear.'-'.$month.'-01');
        $prvmnthfinish=strtotime($nxtyear.'-'.$nxtmnth.'-01');
        switch ($quoter) {
            case 1:
                $prvquoterstart=strtotime($prvyear.'-01-01');
                $prvquoterfinish=strtotime($prvyear.'-04-01');
                break;
            case 2:
                $prvquoterstart=strtotime($prvyear.'-04-01');
                $prvquoterfinish=strtotime($prvyear.'-07-01');
                break;
            case 3:
                $prvquoterstart=strtotime($prvyear.'-07-01');
                $prvquoterfinish=strtotime($prvyear.'-10-01');
                break;
            case 4:
                $prvquoterstart=strtotime($prvyear.'-10-01');
                $prvquoterfinish=strtotime(($year).'-01-01');
                break;
        }
        if ($salestype=='customs') {
            // Month dat
            $this->db->select('count(o.order_id) as cnt, sum(o.profit) as profit, sum(o.revenue) as revenue');
            $this->db->from('ts_orders o');
            $this->db->where('o.order_date >= ', $curmnthstart);
            $this->db->where('o.order_date < ', $curmnthfinish);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $curmnthdat=$this->db->get()->row_array();
            // Other Parts
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi", "oi.order_id=o.order_id");
            $this->db->where('o.item_id != ', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $curmnthstart);
            $this->db->where('o.order_date < ', $curmnthfinish);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $othercurmnth=$this->db->get()->row_array();
            $curmnthdat['cnt']=$curmnthdat['cnt']+$othercurmnth['cnt'];
            $curmnthdat['profit']=floatval($curmnthdat['profit'])+floatval($othercurmnth['profit']);
            $curmnthdat['revenue']=floatval($curmnthdat['revenue'])+floatval($othercurmnth['revenue']);

            $this->db->select('count(o.order_id) as cnt, sum(o.profit) as profit, sum(o.revenue) as revenue');
            $this->db->from('ts_orders o');
            $this->db->where('o.order_date >= ', $prvmnthstart);
            $this->db->where('o.order_date < ', $prvmnthfinish);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $prvmnthdat=$this->db->get()->row_array();
            // Other Part
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi", "oi.order_id=o.order_id");
            $this->db->where('o.item_id != ', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $prvmnthstart);
            $this->db->where('o.order_date < ', $prvmnthfinish);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $otherprvmnth=$this->db->get()->row_array();
            $prvmnthdat['cnt']=$prvmnthdat['cnt']+$otherprvmnth['cnt'];
            $prvmnthdat['profit']=floatval($prvmnthdat['profit'])+floatval($otherprvmnth['profit']);
            $prvmnthdat['revenue']=floatval($prvmnthdat['revenue'])+floatval($otherprvmnth['revenue']);

            // Quater Data
            $this->db->select('count(o.order_id) as cnt, sum(o.profit) as profit, sum(o.revenue) as revenue');
            $this->db->from('ts_orders o');
            $this->db->where('o.order_date >= ', $curquoterstart);
            $this->db->where('o.order_date < ', $curquoterfinish);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $curqtrdat=$this->db->get()->row_array();
            // Other Parts
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi", "oi.order_id=o.order_id");
            $this->db->where('o.item_id != ', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $curquoterstart);
            $this->db->where('o.order_date < ', $curquoterfinish);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $otherqtrdat=$this->db->get()->row_array();
            $curqtrdat['cnt']=$curqtrdat['cnt']+$otherqtrdat['cnt'];
            $curqtrdat['profit']=floatval($curqtrdat['profit'])+floatval($otherqtrdat['profit']);
            $curqtrdat['revenue']=floatval($curqtrdat['revenue'])+floatval($otherqtrdat['revenue']);

            $this->db->select('count(o.order_id) as cnt, sum(o.profit) as profit, sum(o.revenue) as revenue');
            $this->db->from('ts_orders o');
            $this->db->where('o.order_date >= ', $prvquoterstart);
            $this->db->where('o.order_date < ', $prvquoterfinish);
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.item_id', $this->config->item('custom_id'));
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $prvqtrdat=$this->db->get()->row_array();
            // Other Parts
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            $this->db->join("(select distinct(order_id) from ts_order_items where item_id={$this->config->item('custom_id')}) oi", "oi.order_id=o.order_id");
            $this->db->where('o.item_id != ', $this->config->item('custom_id'));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $prvquoterstart);
            $this->db->where('o.order_date < ', $prvquoterfinish);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $otherqtrdat=$this->db->get()->row_array();
            $prvqtrdat['cnt']=$prvqtrdat['cnt']+$otherqtrdat['cnt'];
            $prvqtrdat['profit']=floatval($prvqtrdat['profit'])+floatval($otherqtrdat['profit']);
            $prvqtrdat['revenue']=floatval($prvqtrdat['revenue'])+floatval($otherqtrdat['revenue']);
        } else {
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
                $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
            }
            if ($salestype=='stock') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
            }
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $curmnthstart);
            $this->db->where('o.order_date < ', $curmnthfinish);
            if ($salestype=='alpi') {
                $this->db->where('v.vendor_name', 'Alpi');
            } elseif ($salestype=='ariel') {
                $this->db->where('v.vendor_name', 'Ariel');
            } elseif ($salestype=='esp') {
                $this->db->where_not_in('v.vendor_name', $other_vendor);
            } elseif ($salestype=='hit') {
                $this->db->where('v.vendor_name', 'Hit');
            } elseif ($salestype=='mailine') {
                $this->db->where('v.vendor_name', 'Mailine');
            } elseif ($salestype=='other') {
                $this->db->where('o.item_id', $this->config->item('other_id'));
            }
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->where('st.item_id is null');
            }
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $curmnthdat=$this->db->get()->row_array();

            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
                $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
            }
            if ($salestype=='stock') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
            }
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $prvmnthstart);
            $this->db->where('o.order_date < ', $prvmnthfinish);
            if ($salestype=='alpi') {
                $this->db->where('v.vendor_name', 'Alpi');
            } elseif ($salestype=='ariel') {
                $this->db->where('v.vendor_name', 'Ariel');
            } elseif ($salestype=='esp') {
                $this->db->where_not_in('v.vendor_name', $other_vendor);
            } elseif ($salestype=='hit') {
                $this->db->where('v.vendor_name', 'Hit');
            } elseif ($salestype=='mailine') {
                $this->db->where('v.vendor_name', 'Mailine');
            } elseif ($salestype=='other') {
                $this->db->where('o.item_id', $this->config->item('other_id'));
            }
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->where('st.item_id is null');
            }
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $prvmnthdat=$this->db->get()->row_array();

            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
                $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
            }
            if ($salestype=='stock') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
            }
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $curquoterstart);
            $this->db->where('o.order_date < ', $curquoterfinish);
            if ($salestype=='alpi') {
                $this->db->where('v.vendor_name', 'Alpi');
            } elseif ($salestype=='ariel') {
                $this->db->where('v.vendor_name', 'Ariel');
            } elseif ($salestype=='esp') {
                $this->db->where_not_in('v.vendor_name', $other_vendor);
            } elseif ($salestype=='hit') {
                $this->db->where('v.vendor_name', 'Hit');
            } elseif ($salestype=='mailine') {
                $this->db->where('v.vendor_name', 'Mailine');
            } elseif ($salestype=='other') {
                $this->db->where('o.item_id', $this->config->item('other_id'));
            }
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->where('st.item_id is null');
            }
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $curqtrdat=$this->db->get()->row_array();

            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
            $this->db->from('ts_orders o');
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
                $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
                $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
                $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
            }
            if ($salestype=='stock') {
                $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
            }
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $prvquoterstart);
            $this->db->where('o.order_date < ', $prvquoterfinish);
            if ($salestype=='alpi') {
                $this->db->where('v.vendor_name', 'Alpi');
            } elseif ($salestype=='ariel') {
                $this->db->where('v.vendor_name', 'Ariel');
            } elseif ($salestype=='esp') {
                $this->db->where_not_in('v.vendor_name', $other_vendor);
            } elseif ($salestype=='hit') {
                $this->db->where('v.vendor_name', 'Hit');
            } elseif ($salestype=='mailine') {
                $this->db->where('v.vendor_name', 'Mailine');
            } elseif ($salestype=='other') {
                $this->db->where('o.item_id', $this->config->item('other_id'));
            }
            if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
                $this->db->where('st.item_id is null');
            }
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $prvqtrdat=$this->db->get()->row_array();
        }
        $monthdata=array();
        // Profit %
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=($curmnthdat['revenue']==0 ? 0 : round($curmnthdat['profit']/$curmnthdat['revenue']*100,0));
        if ($prvmnthdat['cnt']>0) {
            $prvdat=round($prvmnthdat['profit']/$prvmnthdat['revenue']*100,0);
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            if ($grow<0) {
                $growclass='negative';
                $grow='('.abs($grow).'%)';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow=$grow.'%';
                $growprc=$growprc.'%';
            }
        }
        $monthdata[]=array(
            'title'=>'Profit %',
            'prvdata'=>$prvdat.'%',
            'curdata'=>$curdat.'%',
            'diff'=>$grow,
            'diffproc'=>$growprc,
            'diffclass'=>$growclass,
        );
        // Profit $ or PNT
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=floatval($curmnthdat['profit']);
        if ($prvmnthdat['cnt']>0) {
            $prvdat=floatval($prvmnthdat['profit']);
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            $prvdat_pts=round($prvdat*$this->config->item('profitpts'),0);
            $prvdat=MoneyOutput($prvdat,0);
            //  round($profit*$pacekf*$this->config->item('profitpts'),0)
            $curdat_pts=round($curdat*$this->config->item('profitpts'),0);
            $curdat=MoneyOutput($curdat,0);
            if ($grow<0) {
                $growclass='negative';
                $grow_pts='('.round(abs($grow)*$this->config->item('profitpts'),0).')';
                $grow='('.MoneyOutput(abs($grow),0).')';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow_pts=round($grow*$this->config->item('profitpts'),0);
                $grow=MoneyOutput($grow,0);
                $growprc=$growprc.'%';
            }
        }
        if ($profit_view=='Points') {
            $monthdata[]=array(
                'title'=>'Profit, pts',
                'prvdata'=>$prvdat_pts,
                'curdata'=>$curdat_pts,
                'diff'=>$grow_pts,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        } else {
            $monthdata[]=array(
                'title'=>'Profit $',
                'prvdata'=>$prvdat,
                'curdata'=>$curdat,
                'diff'=>$grow,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        }
        // #orders
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=$curmnthdat['cnt'];
        if ($prvmnthdat['cnt']>0) {
            $prvdat=$prvmnthdat['cnt'];
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            if ($grow<0) {
                $growclass='negative';
                $grow='('.abs($grow).')';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow=$grow;
                $growprc=$growprc.'%';
            }
        }
        $monthdata[]=array(
            'title'=>'#orders',
            'prvdata'=>$prvdat,
            'curdata'=>$curdat,
            'diff'=>$grow,
            'diffproc'=>$growprc,
            'diffclass'=>$growclass,
        );
        // Revenue $
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=floatval($curmnthdat['revenue']);
        if ($prvmnthdat['cnt']>0) {
            $prvdat=floatval($prvmnthdat['revenue']);
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            $prvdat_pts=round($prvdat*$this->config->item('profitpts'),0);
            $prvdat=MoneyOutput($prvdat,0);
            $curdat_pts=round($curdat*$this->config->item('profitpts'),0);
            $curdat=MoneyOutput($curdat,0);
            if ($grow<0) {
                $growclass='negative';
                $grow_pts='('.round(abs($grow)*$this->config->item('profitpts'),0).')';
                $grow='('.MoneyOutput(abs($grow),0).')';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow_pts=round($grow*$this->config->item('profitpts'),0);
                $grow=MoneyOutput($grow,0);
                $growprc=$growprc.'%';
            }
        }
        if ($profit_view=='Points') {
            $monthdata[]=array(
                'title'=>'Rev $',
                'prvdata'=>$prvdat_pts,
                'curdata'=>$curdat_pts,
                'diff'=>$grow_pts,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        } else {
            $monthdata[]=array(
                'title'=>'Rev $',
                'prvdata'=>$prvdat,
                'curdata'=>$curdat,
                'diff'=>$grow,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        }

        $quotedata=array();
        // Profit %
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=($curqtrdat['revenue']==0 ? 0 : round($curqtrdat['profit']/$curqtrdat['revenue']*100,0));
        if ($prvqtrdat['cnt']>0) {
            $prvdat=round($prvqtrdat['profit']/$prvqtrdat['revenue']*100,0);
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            $prvdat=$prvdat.'%';
            if ($grow<0) {
                $growclass='negative';
                $grow='('.abs($grow).'%)';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow=$grow.'%';
                $growprc=$growprc.'%';
            }
        }
        $quotedata[]=array(
            'title'=>'Profit %',
            'prvdata'=>$prvdat,
            'curdata'=>$curdat.'%',
            'diff'=>$grow,
            'diffproc'=>$growprc,
            'diffclass'=>$growclass,
        );
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=floatval($curqtrdat['profit']);
        if ($prvqtrdat['cnt']>0) {
            $prvdat=floatval($prvqtrdat['profit']);
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            $prvdat_pts=round($prvdat*$this->config->item('profitpts'),0);
            $prvdat=MoneyOutput($prvdat,0);
            $curdat_pts=round($curdat*$this->config->item('profitpts'),0);
            $curdat=MoneyOutput($curdat,0);
            if ($grow<0) {
                $growclass='negative';
                $grow_pts='('.round(abs($grow)*$this->config->item('profitpts'),0).')';
                $grow='('.MoneyOutput(abs($grow),0).')';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow_pts=round($grow*$this->config->item('profitpts'),0);
                $grow=MoneyOutput($grow,0);
                $growprc=$growprc.'%';
            }
        }
        if ($profit_view=='Points') {
            $quotedata[]=array(
                'title'=>'Profit pts',
                'prvdata'=>$prvdat_pts,
                'curdata'=>$curdat_pts,
                'diff'=>$grow_pts,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        } else {
            $quotedata[]=array(
                'title'=>'Profit $',
                'prvdata'=>$prvdat,
                'curdata'=>$curdat,
                'diff'=>$grow,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        }

        // #orders
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=$curqtrdat['cnt'];
        if ($prvqtrdat['cnt']>0) {
            $prvdat=$prvqtrdat['cnt'];
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            if ($grow<0) {
                $growclass='negative';
                $grow='('.abs($grow).')';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow=$grow;
                $growprc=$growprc.'%';
            }
        }
        $quotedata[]=array(
            'title'=>'#orders',
            'prvdata'=>$prvdat,
            'curdata'=>$curdat,
            'diff'=>$grow,
            'diffproc'=>$growprc,
            'diffclass'=>$growclass,
        );
        // Revenue $
        $grow=$growprc=$prvdat='N/A';
        $growclass='';
        $curdat=floatval($curqtrdat['revenue']);
        if ($prvqtrdat['cnt']>0) {
            $prvdat=floatval($prvqtrdat['revenue']);
            $grow=round($curdat-$prvdat,0);
            $growprc=-100;
            if ($curdat!=0) {
                $growprc=round($grow/$curdat*100,0);
            }
            $prvdat_pts=round($prvdat*$this->config->item('profitpts'),0);
            $prvdat=MoneyOutput($prvdat,0);
            $curdat_pts=round($curdat*$this->config->item('profitpts'),0);
            $curdat=MoneyOutput($curdat,0);
            if ($grow<0) {
                $growclass='negative';
                $grow_pts='('.round(abs($grow)*$this->config->item('profitpts'),0).')';
                $grow='('.MoneyOutput(abs($grow),0).')';
                $growprc='('.abs($growprc).'%)';
            } elseif ($grow>0) {
                $growclass='positive';
                $grow_pts=round($grow*$this->config->item('profitpts'),0);
                $grow=MoneyOutput($grow,0);
                $growprc=$growprc.'%';
            }
        }
        if ($profit_view=='Points') {
            $quotedata[]=array(
                'title'=>'Rev pts',
                'prvdata'=>$prvdat_pts,
                'curdata'=>$curdat_pts,
                'diff'=>$grow_pts,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        } else {
            $quotedata[]=array(
                'title'=>'Rev $',
                'prvdata'=>$prvdat,
                'curdata'=>$curdat,
                'diff'=>$grow,
                'diffproc'=>$growprc,
                'diffclass'=>$growclass,
            );
        }
        // Prepare a output
        $curmnth=date('F',$curmnthstart);
        $curyear=date('Y',$curmnthstart);
        $prvyear=date('Y', $prvmnthstart);
        $out=array(
            'month'=>$curmnth,
            'curyear'=>$curyear,
            'prvyear'=>$prvyear,
            'quoter'=>$quoter,
            'monthdata'=>$monthdata,
            'quotedata'=>$quotedata,
        );
        return $out;
    }

    public function get_report_years() {
        $this->db->select('max(order_date) as dat');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $res = $this->db->get()->row_array();
        $year = date('Y', $res['dat']);
        return array(
            'start' =>$this->salestype_start,
            'finish' => $year,
        );
    }

    public function get_difference($diffYearBgn, $diffYearEnd, $profit_type, $salestype, $brand) {
        // Get prvyear date
        $prvMonth = $this->_get_diff_month($diffYearBgn, $salestype, $brand);
        $curMonth = $this->_get_diff_month($diffYearEnd, $salestype, $brand);
        $prvQuater = $this->_get_diff_quater($diffYearBgn, $salestype, $brand);
        $curQuater = $this->_get_diff_quater($diffYearEnd, $salestype, $brand);
        // Calc last day of current month, current quater
        $month = date('F');
        $last_day = strtotime('last day of '.$month);
        $last_month_day = strtotime(date('Y-m-d', $last_day) .' + 1 DAY');

        $current_quarter = ceil(date('n') / 3);
        $last_quater_date = strtotime(date('Y') . '-' . (($current_quarter * 3)) . '-1');
        // Calc diffs
        $month = [];
        for ($i=1; $i<13; $i++) {
            // Build a date
            $date_check = strtotime($diffYearEnd.'-'.$i.'-10');
            if ($date_check>=$last_month_day) {
                $month[]=[
                    'view_class' => 'future',
                    'chg_perc' => $this->empty_show,
                    'chg_perc_class' => 'positive',
                    'chg_revenue' => $this->empty_show,
                    'chg_revenue_class' => 'positive',
                ];
            } else {
                $prvval = $curval = 0;
                $orderproj = 0;
                foreach ($prvMonth as $row) {
                    if ($row['month']==$i) {
                        $prvval=$row['revenue'];
                        $orderproj += $row['project'];
                        break;
                    }
                }
                foreach ($curMonth as $row) {
                    if ($row['month']==$i) {
                        $curval=$row['revenue'];
                        $orderproj += $row['project'];
                        break;
                    }
                }
                $viewclass = ($orderproj == 0 ? 'salesview' : 'projectview');
                if ($prvval == 0) {
                    if ($curval == 0) {
                        $month[] = [
                            'view_class' => $viewclass,
                            'chg_perc' => $this->empty_show,
                            'chg_perc_class' => 'positive',
                            'chg_revenue' => $this->empty_show,
                            'chg_revenue_class' => 'positive',
                        ];
                    } else {
                        if ($profit_type=='Profit') {
                            $month[] = [
                                'view_class' => $viewclass,
                                'chg_perc' => ($curval>0 ? '+100%' : '(-100%)'),
                                'chg_perc_class' => ($curval>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($curval>0 ? '+'.MoneyOutput($curval,0) : '('.MoneyOutput($curval,0).')'),
                                'chg_revenue_class' => ($curval>0 ? 'positive' : 'negative'),
                            ];
                        } else {
                            $monthpts = round(floatval($curval)*$this->config->item('profitpts'),0);
                            $month[] = [
                                'view_class' => $viewclass,
                                'chg_perc' => ($curval>0 ? '+100%' : '(-100%)'),
                                'chg_perc_class' => ($curval>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($curval>0 ? '+'.$monthpts.'pt' : '('.$monthpts.'pt)'),
                                'chg_revenue_class' => ($curval>0 ? 'positive' : 'negative'),
                            ];
                        }
                    }
                } else {
                    $diff = $curval - $prvval;
                    if ($diff == 0) {
                        $month[] = [
                            'view_class' => $viewclass,
                            'chg_perc' => '0%',
                            'chg_perc_class' => 'positive',
                            'chg_revenue' => '0',
                            'chg_revenue_class' => 'positive',
                        ];
                    } else {
                        $diffPerc = round(($diff / $prvval) * 100,0);
                        if ($profit_type=='Profit') {
                            $month[] = [
                                'view_class' => $viewclass,
                                'chg_perc' => ($diff>0 ? '+'.$diffPerc.'%' : '('.$diffPerc.'%)'),
                                'chg_perc_class' => ($diff>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($diff>0 ? '+'.MoneyOutput($diff,0) : '('.MoneyOutput($diff,0).')'),
                                'chg_revenue_class' => ($diff>0 ? 'positive' : 'negative'),
                            ];
                        } else {
                            $monthpts = round(floatval($diff)*$this->config->item('profitpts'),0);
                            $month[] = [
                                'view_class' => $viewclass,
                                'chg_perc' => ($diff>0 ? '+'.$diffPerc.'%' : '('.$diffPerc.'%)'),
                                'chg_perc_class' => ($diff>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($diff>0 ? '+'.$monthpts.'pt' : '('.$monthpts.'pt)'),
                                'chg_revenue_class' => ($diff>0 ? 'positive' : 'negative'),
                            ];
                        }
                    }
                }
            }
        }
        $quarters = [];
        for ($i=1; $i<5; $i++) {
            // Build a date
            // $date_check = strtotime(date('Y') . '-' . ($i * 3) . '-1');
            $date_check = strtotime($diffYearEnd . '-' . ($i * 3) . '-1');
            if ($date_check>=$last_quater_date) {
                $quarters[]=[
                    'view_class' => 'future',
                    'label' => 'Q'.$i.':',
                    'chg_perc' => $this->empty_show,
                    'chg_perc_class' => 'positive',
                    'chg_revenue' => $this->empty_show,
                    'chg_revenue_class' => 'positive',
                    'calcurl' =>'',
                ];
            } else {
                $calcUrl ='/analytics/showdifference_calc/?q='.$i.'&start='.$diffYearBgn.'&finish='.$diffYearEnd.'&type='.$salestype.'&view='.$profit_type.'&brand='.$brand;
                $prvval = $curval = 0;
                $orderproj = 0;
                foreach ($prvQuater as $row) {
                    if ($row['quater']==$i) {
                        $prvval=$row['revenue'];
                        $orderproj += $row['project'];
                        break;
                    }
                }
                foreach ($curQuater as $row) {
                    if ($row['quater']==$i) {
                        $curval=$row['revenue'];
                        $orderproj += $row['project'];
                        break;
                    }
                }
                $viewclass = ($orderproj == 0 ? 'salesview' : 'projectview');
                if ($prvval == 0) {
                    if ($curval == 0) {
                        $quarters[] = [
                            'view_class' => $viewclass,
                            'label' => 'Q'.$i.':',
                            'chg_perc' => $this->empty_show,
                            'chg_perc_class' => 'positive',
                            'chg_revenue' => $this->empty_show,
                            'chg_revenue_class' => 'positive',
                            'calcurl' =>$calcUrl,
                        ];
                    } else {
                        if ($profit_type=='Profit') {
                            $quarters[] = [
                                'view_class' => $viewclass,
                                'label' => 'Q'.$i.':',
                                'chg_perc' => ($curval>0 ? '+100%' : '(-100%)'),
                                'chg_perc_class' => ($curval>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($curval>0 ? '+'.MoneyOutput($curval,0) : '('.MoneyOutput($curval,0).')'),
                                'chg_revenue_class' => ($curval>0 ? 'positive' : 'negative'),
                                'calcurl' =>$calcUrl,
                            ];
                        } else {
                            $qtpts = round(floatval($curval)*$this->config->item('profitpts'),0);
                            $quarters[] = [
                                'view_class' => $viewclass,
                                'label' => 'Q'.$i.':',
                                'chg_perc' => ($curval>0 ? '+100%' : '(-100%)'),
                                'chg_perc_class' => ($curval>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($curval>0 ? '+'.$qtpts.'pt' : '('.$qtpts.'pt)'),
                                'chg_revenue_class' => ($curval>0 ? 'positive' : 'negative'),
                                'calcurl' =>$calcUrl,
                            ];
                        }
                    }
                } else {
                    $diff = $curval - $prvval;
                    if ($diff == 0) {
                        $quarters[] = [
                            'view_class' => $viewclass,
                            'label' => 'Q'.$i.':',
                            'chg_perc' => '0%',
                            'chg_perc_class' => 'positive',
                            'chg_revenue' => '0',
                            'chg_revenue_class' => 'positive',
                            'calcurl' =>$calcUrl,
                        ];
                    } else {
                        $diffPerc = round(($diff / $prvval) * 100,0);
                        if ($profit_type=='Profit') {
                            $quarters[] = [
                                'view_class' => $viewclass,
                                'label' => 'Q'.$i.':',
                                'chg_perc' => ($diff>0 ? '+'.$diffPerc.'%' : '('.$diffPerc.'%)'),
                                'chg_perc_class' => ($diff>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($diff>0 ? '+'.MoneyOutput($diff,0) : '('.MoneyOutput($diff,0).')'),
                                'chg_revenue_class' => ($diff>0 ? 'positive' : 'negative'),
                                'calcurl' =>$calcUrl,
                            ];
                        } else {
                            $qtpts = round(floatval($diff)*$this->config->item('profitpts'),0);
                            $quarters[] = [
                                'view_class' => $viewclass,
                                'label' => 'Q'.$i.':',
                                'chg_perc' => ($diff>0 ? '+'.$diffPerc.'%' : '('.$diffPerc.'%)'),
                                'chg_perc_class' => ($diff>0 ? 'positive' : 'negative'),
                                'chg_revenue' => ($diff>0 ? '+'.$qtpts.'pt' : '('.$diff.'pt)'),
                                'chg_revenue_class' => ($diff>0 ? 'positive' : 'negative'),
                                'calcurl' =>$calcUrl,
                            ];
                        }
                    }
                }
            }
        }
        return
            [
                'months' => $month,
                'quarters' => $quarters,
            ];
    }

    public function get_differences_details($quarter, $yearBgn, $yearEnd, $salestype, $profit_type, $brand) {
        $quarterPrev = $this->_get_diff_quater($yearBgn, $salestype, $brand);
        $quarterCur = $this->_get_diff_quater($yearEnd, $salestype, $brand);
        $prevVal = 0;
        foreach ($quarterPrev as $row) {
            if ($row['quater']==$quarter) {
                $prevVal = ($profit_type=='Profit' ? $row['revenue'] : round(floatval($row['revenue'])*$this->config->item('profitpts'),0));
                break;
            }
        }
        $curVal = 0;
        foreach ($quarterCur as $row) {
            if ($row['quater']==$quarter) {
                $curVal = ($profit_type=='Profit' ? $row['revenue'] : round(floatval($row['revenue'])*$this->config->item('profitpts'),0));
            }
        }
        return [
            'prvval' => $prevVal,
            'curval' => $curVal,
        ];
    }

    private function _get_diff_month($diffYear, $salestype, $brand) {
        $item_table = 'sb_items';
        $vendoritem_table = 'sb_vendor_items';
        $other_vendor = array(
            'Ariel', 'Alpi', 'Mailine', 'Hit',
        );
        $dateBgn = strtotime($diffYear.'-01-01');
        $dateEnd = strtotime(($diffYear+1).'-01-01');
        $this->db->select('date_format(from_unixtime(o.order_date),\'%c\') as month, sum(o.revenue) revenue', FALSE);
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $dateBgn);
        $this->db->where('o.order_date < ', $dateEnd);
        $this->db->where('o.is_canceled',0);
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
            $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
            $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
            $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        }
        if ($salestype=='stock') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
        }
        if ($salestype == 'customs') {
            $this->db->where('o.item_id', $this->config->item('custom_id'));
        } elseif ($salestype=='alpi') {
            $this->db->where('v.vendor_name', 'Alpi');
        } elseif ($salestype=='ariel') {
            $this->db->where('v.vendor_name', 'Ariel');
        } elseif ($salestype=='esp') {
            $this->db->where_not_in('v.vendor_name', $other_vendor);
        } elseif ($salestype=='hit') {
            $this->db->where('v.vendor_name', 'Hit');
        } elseif ($salestype=='mailine') {
            $this->db->where('v.vendor_name', 'Mailine');
        } elseif ($salestype=='other') {
            $this->db->where('o.item_id', $this->config->item('other_id'));
        }
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->where('st.item_id is null');
        }
        if ($brand!='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('month');
        $monthRes = $this->db->get()->result_array();
        // Count orders in stage - project
        $this->db->select('date_format(from_unixtime(o.order_date),\'%c\') as month, count(o.order_id) as cnt', FALSE);
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $dateBgn);
        $this->db->where('o.order_date < ', $dateEnd);
        $this->db->where('o.is_canceled',0);
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
            $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
            $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
            $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        }
        if ($salestype=='stock') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
        }
        if ($salestype == 'customs') {
            $this->db->where('o.item_id', $this->config->item('custom_id'));
        } elseif ($salestype=='alpi') {
            $this->db->where('v.vendor_name', 'Alpi');
        } elseif ($salestype=='ariel') {
            $this->db->where('v.vendor_name', 'Ariel');
        } elseif ($salestype=='esp') {
            $this->db->where_not_in('v.vendor_name', $other_vendor);
        } elseif ($salestype=='hit') {
            $this->db->where('v.vendor_name', 'Hit');
        } elseif ($salestype=='mailine') {
            $this->db->where('v.vendor_name', 'Mailine');
        } elseif ($salestype=='other') {
            $this->db->where('o.item_id', $this->config->item('other_id'));
        }
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->where('st.item_id is null');
        }
        $this->db->where('o.profit_perc is null');
        if ($brand!='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('month');
        $projorder=$this->db->get()->result_array();
        if ($salestype=='esp') {
            $this->db->select('date_format(from_unixtime(o.order_date),\'%c\') as month, sum(o.revenue) revenue', FALSE);
            $this->db->from('ts_orders o');
            $this->db->where_in('o.item_id', array($this->config->item('multy_id'),-4,-5));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $dateBgn);
            $this->db->where('o.order_date < ', $dateEnd);
            if ($brand!='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $this->db->group_by('month');
            $multi_monthdata=$this->db->get()->result_array();
            // Add multiple
            foreach ($multi_monthdata as $row) {
                $i=0;
                $find=0;
                foreach ($monthRes as $mrow) {
                    if ($mrow['month']==$row['month']) {
                        $find=1;
                        break;
                    }
                    $i++;
                }
                if ($find==1) {
                    $monthRes[$i]['revenue']+=$row['revenue'];
                } else {
                    $monthRes[]=[
                        'month'=>$row['month'],
                        'revenue'=>$row['revenue'],
                    ];
                }
            }
            // Proj Orders
            $this->db->select('date_format(from_unixtime(o.order_date),\'%c\') as month, count(o.order_id) as cnt', FALSE);
            $this->db->from('ts_orders o');
            $this->db->where_in('o.item_id', array($this->config->item('multy_id'),-4,-5));
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $dateBgn);
            $this->db->where('o.order_date < ', $dateEnd);
            $this->db->where('o.profit_perc is null');
            if ($brand!='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $this->db->group_by('month');
            $proj_multi=$this->db->get()->result_array();
            // Add multiple
            foreach ($proj_multi as $row) {
                $i=0;
                $find=0;
                foreach ($projorder as $mrow) {
                    if ($mrow['month']==$row['month']) {
                        $find=1;
                        break;
                    }
                    $i++;
                }
                if ($find==1) {
                    $projorder[$i]['cnt']+=$row['cnt'];
                } else {
                    $projorder[]=[
                        'month'=>$row['month'],
                        'cnt'=>$row['cnt'],
                    ];
                }
            }
        }

        $month = [];

        for ($i=1; $i<13; $i++) {
            $find=0;
            $key=0;
            foreach ($monthRes as $row) {
                if ($row['month']==$i) {
                    $find=1;
                    break;
                }
                $key++;
            }
            $ordproj=0;
            foreach ($projorder as $orow) {
                if ($orow['month']==$i) {
                    $ordproj=$orow['cnt'];
                }
            }
            if ($find==1) {
                $month[]=[
                    'month'=>$i,
                    'revenue'=>$monthRes[$key]['revenue'],
                    'type'=>'view',
                    'project' => $ordproj,
                ];
            } else {
                $month[]=[
                    'month'=>$i,
                    'revenue'=>0,
                    'type'=>'empty',
                    'project' => $ordproj,
                ];
            }
        }
        return $month;
    }

    private function _get_diff_quater($diffYear, $salestype, $brand) {
        $item_table = 'sb_items';
        $vendoritem_table =  'sb_vendor_items';
        $other_vendor = array(
            'Ariel', 'Alpi', 'Mailine', 'Hit',
        );

        $dateBgn = strtotime($diffYear.'-01-01');
        $dateEnd = strtotime(($diffYear+1).'-01-01');
        $this->db->select('QUARTER(from_unixtime(o.order_date)) as qt, sum(o.revenue) revenue');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $dateBgn);
        $this->db->where('o.order_date < ', $dateEnd);
        $this->db->where('o.is_canceled',0);
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
            $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
            $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
            $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        }
        if ($salestype=='stock') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
        }
        if ($salestype == 'customs') {
            $this->db->where('o.item_id', $this->config->item('custom_id'));
        } elseif ($salestype=='alpi') {
            $this->db->where('v.vendor_name', 'Alpi');
        } elseif ($salestype=='ariel') {
            $this->db->where('v.vendor_name', 'Ariel');
        } elseif ($salestype=='esp') {
            $this->db->where_not_in('v.vendor_name', $other_vendor);
        } elseif ($salestype=='hit') {
            $this->db->where('v.vendor_name', 'Hit');
        } elseif ($salestype=='mailine') {
            $this->db->where('v.vendor_name', 'Mailine');
        } elseif ($salestype=='other') {
            $this->db->where('o.item_id', $this->config->item('other_id'));
        }
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->where('st.item_id is null');
        }
        if ($brand!='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('qt');
        $quaterRes = $this->db->get()->result_array();

        // Count proj Orders
        $this->db->select('QUARTER(from_unixtime(o.order_date)) as qt, count(o.order_id) as cnt');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $dateBgn);
        $this->db->where('o.order_date < ', $dateEnd);
        $this->db->where('o.is_canceled',0);
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id', 'left');
            $this->db->join("{$item_table} i", 'i.item_id=o.item_id');
            $this->db->join("{$vendoritem_table} vi", 'vi.vendor_item_id=i.vendor_item_id');
            $this->db->join('vendors v', 'v.vendor_id=vi.vendor_item_vendor');
        }
        if ($salestype=='stock') {
            $this->db->join('ts_stock_items st', 'st.item_id=o.item_id');
        }
        if ($salestype == 'customs') {
            $this->db->where('o.item_id', $this->config->item('custom_id'));
        } elseif ($salestype=='alpi') {
            $this->db->where('v.vendor_name', 'Alpi');
        } elseif ($salestype=='ariel') {
            $this->db->where('v.vendor_name', 'Ariel');
        } elseif ($salestype=='esp') {
            $this->db->where_not_in('v.vendor_name', $other_vendor);
        } elseif ($salestype=='hit') {
            $this->db->where('v.vendor_name', 'Hit');
        } elseif ($salestype=='mailine') {
            $this->db->where('v.vendor_name', 'Mailine');
        } elseif ($salestype=='other') {
            $this->db->where('o.item_id', $this->config->item('other_id'));
        }
        if ($salestype=='alpi' || $salestype=='ariel' || $salestype=='esp' || $salestype=='hit' || $salestype=='mailine') {
            $this->db->where('st.item_id is null');
        }
        $this->db->where('o.profit_perc is null');
        if ($brand!='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('qt');
        $project_ord = $this->db->get()->result_array();

        if ($salestype=='esp') {
            $this->db->select('QUARTER(from_unixtime(o.order_date)) as qt, sum(o.revenue) revenue');
            $this->db->from('ts_orders o');
            $this->db->where_in('o.item_id', array($this->config->item('multy_id'), -4, -5));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $dateBgn);
            $this->db->where('o.order_date < ', $dateEnd);
            if ($brand!='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $this->db->group_by('qt');
            $multi_quartdata = $this->db->get()->result_array();

            foreach ($multi_quartdata as $row) {
                $i=0;
                $find=0;
                foreach ($quaterRes as $mrow) {
                    if ($mrow['qt']==$row['qt']) {
                        $find=1;
                        break;
                    }
                    $i++;
                }
                if ($find==1) {
                    $quaterRes[$i]['revenue']+=$row['revenue'];
                } else {
                    $quaterRes[]=[
                        'qt'=>$row['qt'],
                        'revenue'=>$row['revenue'],
                    ];
                }
            }
            // Proj Orders
            $this->db->select('QUARTER(from_unixtime(o.order_date)) as qt, count(o.order_id) as cnt');
            $this->db->from('ts_orders o');
            $this->db->where_in('o.item_id', array($this->config->item('multy_id'), -4, -5));
            $this->db->where('o.is_canceled', 0);
            $this->db->where('o.order_date >= ', $dateBgn);
            $this->db->where('o.order_date < ', $dateEnd);
            $this->db->where('o.profit_perc is null');
            $this->db->group_by('qt');
            if ($brand!='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $proj_multi = $this->db->get()->result_array();

            foreach ($proj_multi as $row) {
                $i=0;
                $find=0;
                foreach ($project_ord as $mrow) {
                    if ($mrow['qt']==$row['qt']) {
                        $find=1;
                        break;
                    }
                    $i++;
                }
                if ($find==1) {
                    $project_ord[$i]['cnt']+=$row['cnt'];
                } else {
                    $project_ord[]=[
                        'qt'=>$row['qt'],
                        'cnt'=>$row['cnt'],
                    ];
                }
            }
        }
        $quater = [];
        for ($i=1; $i<5; $i++) {
            $find=0;
            $key=0;
            foreach ($quaterRes as $row) {
                if ($row['qt']==$i) {
                    $find=1;
                    break;
                }
                $key++;
            }
            $ordproj = 0;
            foreach ($project_ord as $item) {
                if ($item['qt']==$i) {
                    $ordproj = $item['cnt'];
                }
            }
            if ($find==1) {
                $quater[]=[
                    'quater'=>$i,
                    'revenue'=>$quaterRes[$key]['revenue'],
                    'type'=>'view',
                    'project' => $ordproj,
                ];
            } else {
                $quater[]=[
                    'quater'=>$i,
                    'revenue'=>0,
                    'type'=>'empty',
                    'project' => $ordproj,
                ];
            }
        }
        return $quater;
    }

    private function ItemSalesTotals($val,$money=0) {
        $title=$data='';
        if (abs($val)>1000) {
            if ($money==0) {
                $title=number_format($val,0,'.',',');
            } else {
                $title=  MoneyOutput($val,0);
            }
        }
        if (abs($val)>1000000) {
            if ($money==0) {
                $data=number_format(round($val/1000000,1),1,'.',',').'M';
            } else {
                $data=MoneyOutput(round($val/1000000,1),1).'M';
            }
        } elseif (abs($val)>1000) {
            if ($money==0) {
                $data=number_format(round($val/1000,1),1,'.',',').'K';
            } else {
                $data=MoneyOutput(round($val/1000,1),1).'K';
            }
        } else {
            if ($money==0) {
                $data=number_format($val,0,'.',',');
            } else {
                $data=MoneyOutput($val,0);
            }
        }
        return array('title'=>$title, 'data'=>$data);

    }

    public function artproof_daily_report($datestart, $dateend, $brand) {
        $this->db->select('a.order_id, a.mail_id, l.artwork_id, u.user_name, o.brand as order_brand, e.brand as email_brand, count(l.proofdoclog_id) as cnt');
        $this->db->from('ts_proofdoc_log l');
        $this->db->join('ts_artworks a','a.artwork_id=l.artwork_id');
        $this->db->join('users u','u.user_id=l.user_id');
        $this->db->join('ts_orders o','o.order_id=a.order_id','left');
        $this->db->join('ts_emails e','e.email_id=a.mail_id','left');
        $this->db->where('l.work','Send Proof');
        $this->db->where('unix_timestamp(l.create_time) >= ', $datestart);
        $this->db->where('unix_timestamp(l.create_time) < ', $dateend);
        $this->db->where('(o.brand=\''.$brand.'\' or e.brand=\''.$brand.'\')');
        $this->db->group_by('a.order_id, a.mail_id, l.artwork_id, u.user_name, o.brand, e.brand');
        $this->db->order_by('u.user_name');
        $res=$this->db->get()->result_array();
        $out=[];
        $outtype=[];
        $total=[
            'orders_first'=>0,
            'orders_first_attach'=>0,
            'request_first'=>0,
            'request_first_attach'=>0,
            'all_first'=>0,
            'all_first_attach'=>0,
            'orders_resend'=>0,
            'orders_resend_attach'=>0,
            'request_resend'=>0,
            'request_resend_attach'=>0,
            'all_resend'=>0,
            'all_resend_attach'=>0,
            'all'=>0,
            'all_attach'=>0,
        ];
        $totaltype=[
            'orders_reg_first'=>0,
            'orders_reg_first_attach'=>0,
            'request_reg_first'=>0,
            'request_reg_first_attach'=>0,
            'all_reg_first'=>0,
            'all_reg_first_attach'=>0,
            'orders_reg_resend'=>0,
            'orders_reg_resend_attach'=>0,
            'request_reg_resend'=>0,
            'request_reg_resend_attach'=>0,
            'all_reg_resend'=>0,
            'all_reg_resend_attach'=>0,
            'orders_cust_first'=>0,
            'orders_cust_first_attach'=>0,
            'request_cust_first'=>0,
            'request_cust_first_attach'=>0,
            'all_cust_first'=>0,
            'all_cust_first_attach'=>0,
            'orders_cust_resend'=>0,
            'orders_cust_resend_attach'=>0,
            'request_cust_resend'=>0,
            'request_cust_resend_attach'=>0,
            'all_cust_resend'=>0,
            'all_cust_resend_attach'=>0,
        ];
        $user_key=[];
        foreach ($res as $row) {
            // Search User
            $key=array_search($row['user_name'], $user_key);
            if ($key===FALSE) {
                // Add key
                array_push($user_key, $row['user_name']);
                $usrdata= explode(' ', $row['user_name']);
                $user_name=(isset($usrdata[0]) ? $usrdata[0] : $row['user_name']);
                $out[]=[
                    'user'=>$user_name,
                    'orders_first'=>0,
                    'orders_first_attach'=>0,
                    'request_first'=>0,
                    'request_first_attach'=>0,
                    'all_first'=>0,
                    'all_first_attach'=>0,
                    'orders_resend'=>0,
                    'orders_resend_attach'=>0,
                    'request_resend'=>0,
                    'request_resend_attach'=>0,
                    'all_resend'=>0,
                    'all_resend_attach'=>0,
                    'all'=>0,
                    'all_attach'=>0,
                ];
                $outtype[]=[
                    'user'=>$user_name,
                    'orders_reg_first'=>0,
                    'orders_reg_first_attach'=>0,
                    'request_reg_first'=>0,
                    'request_reg_first_attach'=>0,
                    'all_reg_first'=>0,
                    'all_reg_first_attach'=>0,
                    'orders_reg_resend'=>0,
                    'orders_reg_resend_attach'=>0,
                    'request_reg_resend'=>0,
                    'request_reg_resend_attach'=>0,
                    'all_reg_resend'=>0,
                    'all_reg_resend_attach'=>0,
                    'orders_cust_first'=>0,
                    'orders_cust_first_attach'=>0,
                    'request_cust_first'=>0,
                    'request_cust_first_attach'=>0,
                    'all_cust_first'=>0,
                    'all_cust_first_attach'=>0,
                    'orders_cust_resend'=>0,
                    'orders_cust_resend_attach'=>0,
                    'request_cust_resend'=>0,
                    'request_cust_resend_attach'=>0,
                    'all_cust_resend'=>0,
                    'all_cust_resend_attach'=>0,
                ];
                $key=count($user_key)-1;
            }
            // Lets go - check other
            $custom=0;
            if (!empty($row['order_id'])) {
                $this->db->select('item_id');
                $this->db->from('ts_orders');
                $this->db->where('order_id', $row['order_id']);
                $ordres=$this->db->get()->row_array();
                if ($ordres['item_id']<0) {
                    $custom=1;
                }
            } else {
                $this->db->select('email_item_id');
                $this->db->from('ts_emails');
                $this->db->where('email_id', $row['mail_id']);
                $ordres=$this->db->get()->row_array();
                if ($ordres['email_item_id']<0) {
                    $custom=1;
                }
            }
            $out[$key]['all']+=1;
            $out[$key]['all_attach']+=$row['cnt'];
            $total['all']+=1;
            $total['all_attach']+=$row['cnt'];
            // Check - if artwork was sended in previous time
            $this->db->select('count(proofdoclog_id) as cnt');
            $this->db->from('ts_proofdoc_log');
            $this->db->where('artwork_id', $row['artwork_id']);
            $this->db->where('unix_timestamp(create_time) < ', $datestart);
            $chkres=$this->db->get()->row_array();
            if ($chkres['cnt']==0) {
                // This document sends first time
                if (!empty($row['order_id'])) {
                    // Artwork related with order
                    $out[$key]['orders_first']+=1;
                    $out[$key]['orders_first_attach']+=$row['cnt'];
                    $total['orders_first']+=1;
                    $total['orders_first_attach']+=$row['cnt'];
                    if ($custom==0) {
                        $outtype[$key]['orders_reg_first']+=1;
                        $outtype[$key]['orders_reg_first_attach']+=$row['cnt'];
                        $totaltype['orders_reg_first']+=1;
                        $totaltype['orders_reg_first_attach']+=$row['cnt'];
                    } else {
                        $outtype[$key]['orders_cust_first']+=1;
                        $outtype[$key]['orders_cust_first_attach']+=$row['cnt'];
                        $totaltype['orders_cust_first']+=1;
                        $totaltype['orders_cust_first_attach']+=$row['cnt'];
                    }
                } else {
                    // Artwork related with request
                    $out[$key]['request_first']+=1;
                    $out[$key]['request_first_attach']+=$row['cnt'];
                    $total['request_first']+=1;
                    $total['request_first_attach']+=$row['cnt'];
                    if ($custom==0) {
                        $outtype[$key]['request_reg_first']+=1;
                        $outtype[$key]['request_reg_first_attach']+=$row['cnt'];
                        $totaltype['request_reg_first']+=1;
                        $totaltype['request_reg_first_attach']+=$row['cnt'];
                    } else {
                        $outtype[$key]['request_cust_first']+=1;
                        $outtype[$key]['request_cust_first_attach']+=$row['cnt'];
                        $totaltype['request_cust_first']+=1;
                        $totaltype['request_cust_first_attach']+=$row['cnt'];
                    }
                }
                $out[$key]['all_first']+=1;
                $out[$key]['all_first_attach']+=$row['cnt'];
                $total['all_first']+=1;
                $total['all_first_attach']+=$row['cnt'];
                if ($custom==0) {
                    $outtype[$key]['all_reg_first']+=1;
                    $outtype[$key]['all_reg_first_attach']+=$row['cnt'];
                    $totaltype['all_reg_first']+=1;
                    $totaltype['all_reg_first_attach']+=$row['cnt'];
                } else {
                    $outtype[$key]['all_cust_first']+=1;
                    $outtype[$key]['all_cust_first_attach']+=$row['cnt'];
                    $totaltype['all_cust_first']+=1;
                    $totaltype['all_cust_first_attach']+=$row['cnt'];
                }
            } else {
                // Re send
                if (!empty($row['order_id'])) {
                    // Artwork related with order
                    $out[$key]['orders_resend']+=1;
                    $out[$key]['orders_resend_attach']+=$row['cnt'];
                    $total['orders_resend']+=1;
                    $total['orders_resend_attach']+=$row['cnt'];
                    if ($custom==0) {
                        $outtype[$key]['orders_reg_resend']+=1;
                        $outtype[$key]['orders_reg_resend_attach']+=$row['cnt'];
                        $totaltype['orders_reg_resend']+=1;
                        $totaltype['orders_reg_resend_attach']+=$row['cnt'];
                    } else {
                        $outtype[$key]['orders_cust_resend']+=1;
                        $outtype[$key]['orders_cust_resend_attach']+=$row['cnt'];
                        $totaltype['orders_cust_resend']+=1;
                        $totaltype['orders_cust_resend_attach']+=$row['cnt'];
                    }
                } else {
                    // Artwork related with request
                    $out[$key]['request_resend']+=1;
                    $out[$key]['request_resend_attach']+=$row['cnt'];
                    $total['request_resend']+=1;
                    $total['request_resend_attach']+=$row['cnt'];
                    if ($custom==0) {
                        $outtype[$key]['request_reg_resend']+=1;
                        $outtype[$key]['request_reg_resend_attach']+=$row['cnt'];
                        $totaltype['request_reg_resend']+=1;
                        $totaltype['request_reg_resend_attach']+=$row['cnt'];
                    } else {
                        $outtype[$key]['request_cust_resend']+=1;
                        $outtype[$key]['request_cust_resend_attach']+=$row['cnt'];
                        $totaltype['request_cust_resend']+=1;
                        $totaltype['request_cust_resend_attach']+=$row['cnt'];
                    }
                }
                $out[$key]['all_resend']+=1;
                $out[$key]['all_resend_attach']+=$row['cnt'];
                $total['all_resend']+=1;
                $total['all_resend_attach']+=$row['cnt'];
                if ($custom==0) {
                    $outtype[$key]['all_reg_resend']+=1;
                    $outtype[$key]['all_reg_resend_attach']+=$row['cnt'];
                    $totaltype['all_reg_resend']+=1;
                    $totaltype['all_reg_resend_attach']+=$row['cnt'];
                } else {
                    $outtype[$key]['all_cust_resend']+=1;
                    $outtype[$key]['all_cust_resend_attach']+=$row['cnt'];
                    $totaltype['all_cust_resend']+=1;
                    $totaltype['all_cust_resend_attach']+=$row['cnt'];
                }
            }
        }
        return [
            'out' => $out,
            'total' => $total,
            'outtype' => $outtype,
            'totaltype' => $totaltype,
        ];
    }


}