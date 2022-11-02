<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Batches_model
 *
 * @author german polovnikov
 */

class Balances_model extends My_Model
{

    private $EMPTY_PROFIT='------';
    private $empty_html_content='&nbsp;';
    private $start_netprofitdatashow=2013;

    function __construct()
    {
        parent::__construct();
    }

    public function get_calcdata($sort, $direction, $brand) {
        if ($brand=='ALL') {
            $this->db->select('description, max(calc_id) as calc_id, sum(monthsum) as monthly, sum(weeksum) as weekly');
            $this->db->select('sum(coalesce(monthsum,0)/4+coalesce(weeksum,0)) as quarta, sum(coalesce(monthsum,0)*12+coalesce(weeksum,0)*52) as yearly');
            $this->db->from('calcdata');
            $this->db->group_by('description');
        } else {
            $this->db->select('calc_id,description,monthsum as monthly, weeksum as weekly');
            $this->db->select('(coalesce(monthsum,0)/4+coalesce(weeksum,0)) as quarta, coalesce(monthsum,0)*12+coalesce(weeksum,0)*52 as yearly');
            $this->db->from('calcdata');
            if ($brand=='SB') {
                $this->db->where_in('brand', ['BT','SB']);
            } else {
                $this->db->where('brand', $brand);
            }
        }
        $this->db->order_by($sort, $direction);
        $res=$this->db->get()->result_array();
        // ,
        $total=0;
        foreach ($res as $row) {
            $total+=$row['yearly'];
        }

        $out=array();
        $sums=array('month'=>0,'week'=>0,'quart'=>0, 'year'=>0);
        foreach ($res as $row) {
            $row['out_month']=($row['monthly']=='' ? $this->empty_html_content : MoneyOutput($row['monthly']));
            $row['out_week']=($row['weekly']=='' ? $this->empty_html_content : MoneyOutput($row['weekly']));
            // $quarta=floatval($row['monthsum'])/4+floatval($row['weeksum']);
            $row['out_quarta']=(floatval($row['quarta'])==0 ? '&nbsp;' : MoneyOutput($row['quarta']));
            $row['out_year']=(floatval($row['yearly'])==0 ? '&nbsp;' : MoneyOutput($row['yearly']));
            $row['expense_perc']=0;
            if ($total>0) {
                $row['expense_perc']=round($row['yearly']/$total*100,1);
            }
            $out[]=$row;
            $sums['month']+=floatval($row['monthly']);
            $sums['week']+=floatval($row['weekly']);
            $sums['quart']+=floatval($row['quarta']);
            $sums['year']+=floatval($row['yearly']);
        }

        $sums['month']=($sums['month']==0 ? $this->empty_html_content : MoneyOutput($sums['month'],2));
        $sums['week']=($sums['week']==0 ? $this->empty_html_content : MoneyOutput($sums['week'],2));
        $sums['quart']=($sums['quart']==0 ? $this->empty_html_content : MoneyOutput($sums['quart'],2));
        $sums['year']=($sums['year']==0 ? $this->empty_html_content : MoneyOutput($sums['year'],2));

        return array('body'=>$out,'sums'=>$sums);
    }

    public function get_calcrow_data($calc_id) {
        /* Totals */
        if ($calc_id==0) {
            $res=array(
                'calc_id'=>0,
                'description'=>'',
                'monthsum'=>0,
                'weeksum'=>'',
                'quartasum'=>'',
                'yearsum'=>'',
                'expense_perc'=>0,
            );
        } else {
            $this->db->select('coalesce(monthsum,0)*12+coalesce(weeksum,0)*52 as yearly',FALSE);
            $this->db->from('calcdata');
            $totres=$this->db->get()->result_array();
            $totals=0;
            foreach ($totres as $row) {
                $totals+=$row['yearly'];
            }
            $this->db->select('*');
            $this->db->from('calcdata');
            $this->db->where('calc_id',$calc_id);
            $res=$this->db->get()->row_array();
            if (isset($res['calc_id'])) {
                $monthdat=floatval($res['monthsum']);
                $weeksum=floatval($res['weeksum']);
                $quarta=$monthdat/4+$weeksum;
                $yearsum=$monthdat*12+$weeksum*52;
                $res['quartasum']=($quarta==0 ? $this->empty_html_content : MoneyOutput($quarta,2));
                $res['yearsum']=($yearsum==0 ? $this->empty_html_content : MoneyOutput($yearsum,2));
                $res['expense_perc']=($totals==0 ? 0 : round($yearsum/$totals*100,1));
            }
        }
        return $res;
    }

    public function save_calcdata($options) {
        $res = ['result'=>$this->error_result, 'msg' => 'Data not saved'];
        if (empty($options['description'])) {
            $res['msg']='Empty Description';
        } else {
            $this->db->set('description',$options['description']);
            $this->db->set('monthsum',(floatval($options['monthsum'])==0 ? NULL : floatval($options['monthsum'])));
            $this->db->set('weeksum',(floatval($options['weeksum'])==0 ? NULL : floatval($options['weeksum'])));
            if ($options['calc_id']==0) {
                $this->db->set('brand', $options['brand']);
                $this->db->insert('calcdata');
                $newcalc=$this->db->insert_id();
                if ($newcalc>0) {
                    $res['result']=$this->success_result;
                }
            } else {
                $this->db->where('calc_id',$options['calc_id']);
                $this->db->update('calcdata');
                $res['result']=$this->success_result;
            }
        }
        return $res;
    }

    public function delete_calcdata($calc_id) {
        $this->db->where('calc_id',$calc_id);
        $this->db->delete('calcdata');
        $result=$this->db->affected_rows();
        return $result;
    }

    public function get_netprofit_runs($options, $radio='amount') {
        // $datebgn=$this->config->item('netprofit_start');
        $type=$options['type'];
        $out=array(
            'out_revenue'=>'&nbsp;',
            'out_profit'=>'&nbsp;',
            'out_sales'=>'&nbsp;',
            'out_operating'=>'&nbsp;',
            'out_payroll'=>'&nbsp;',
            'out_advertising'=>'&nbsp;',
            'out_projects'=>'&nbsp;',
            'out_w9work'=>'&nbsp;',
            'out_purchases'=>'&nbsp;',
            'out_totalcost'=>'&nbsp;',
            'out_netprofit'=>'&nbsp;',
            'out_saved'=>'&nbsp;',
            'out_debt'=>'&nbsp;',
            'out_owners'=>'&nbsp;',
            'out_od2'=>'&nbsp;',
            'out_debtval'=>0,
            'out_debtclass'=>'',
        );
        $brand = ifset($options, 'brand','ALL');
        $this->db->select('sum(netprofit_revenue(nd.datebgn, nd.dateend,\''.$brand.'\')) as revenue');
        $this->db->select('sum(netprofit_profit(nd.datebgn, nd.dateend,\''.$brand.'\')) as gross_profit');
        $this->db->select('sum(netprofit_cntsale(nd.datebgn, nd.dateend,\''.$brand.'\')) as sales');
        $this->db->select('sum(np.profit_operating) as profit_operating');
        $this->db->select('sum(np.profit_payroll) as profit_payroll, sum(np.profit_advertising) as profit_advertising');
        $this->db->select('sum(np.profit_projects) as profit_projects, sum(np.profit_purchases) as profit_purchases');
        $this->db->select('sum(np.profit_w9) as profit_w9');
        $this->db->select('sum(netprofit_profit(nd.datebgn, nd.dateend,\''.$brand.'\')*np.debtinclude) as rungross',FALSE);
        $this->db->select('sum(coalesce(np.profit_operating,0)*np.debtinclude+coalesce(np.profit_payroll,0)*np.debtinclude+coalesce(np.profit_advertising,0)*np.debtinclude+coalesce(np.profit_projects,0)*np.debtinclude+coalesce(np.profit_w9,0)*np.debtinclude+coalesce(np.profit_purchases,0)*np.debtinclude) as runtotalcost',FALSE);
        $this->db->select('sum(np.profit_owners*np.debtinclude) as profit_owners, sum(np.profit_saved*np.debtinclude) as profit_saved, sum(np.od2*np.debtinclude) as od2');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('np.runinclude', 1);
        if ($type=='week') {
            $this->db->where('nd.profit_month is NULL');
        } else {
            $this->db->where('nd.profit_week is NULL');
        }
        if (isset($options['start']) && $options['start']) {
            $this->db->where('nd.datebgn >= ',$options['start']);
        }
        if (isset($options['end']) && $options['end']) {
            $this->db->where('nd.dateend <= ',$options['end']);
        }
        if (isset($options['startweek']) && $options['startweek']) {
            $this->db->where('nd.profit_week >= ',$options['startweek']);
        }
        if (isset($options['endweek']) && $options['endweek']) {
            $this->db->where('nd.profit_week < ',$options['endweek']);
        }
        if (isset($options['datayear'])) {
            $this->db->where('nd.profit_year',$options['datayear']);
        }
        $results=$this->db->get()->row_array();
        if (isset($options['dataonly']) && $options['dataonly']==1) {
            return $results;
        }
        if (count($results)!=0) {
            /* Init values */
            $prof_operating=floatval($results['profit_operating']);
            $prof_payroll=floatval($results['profit_payroll']);
            $prof_advertising=floatval($results['profit_advertising']);
            $prof_projects=floatval($results['profit_projects']);
            $prof_w9=floatval($results['profit_w9']);
            $prof_purchases=floatval($results['profit_purchases']);
            $prof_sales=intval($results['sales']);
            $prof_gross_profit=floatval($results['gross_profit']);
            $prof_revenue=floatval($results['revenue']);
            $prof_owners=floatval($results['profit_owners']);
            $prof_saved=floatval($results['profit_saved']);
            $prof_od2=floatval($results['od2']);
            // Calculated fields
            $prof_total_cost=$prof_operating+$prof_payroll+$prof_advertising+$prof_projects+$prof_purchases+$prof_w9;
            $prof_net_profit=$prof_gross_profit-$prof_total_cost;
            $prof_debt=floatval($results['rungross'])-floatval($results['runtotalcost'])-$prof_owners-$prof_saved-$prof_od2;
            $prof_od = $prof_owners + $prof_od2;
            // $prof_net_profit-$prof_owners-$prof_saved-$prof_od2;
            // Prepare for view
            $prof_debt+=$this->config->item('netprofit_debt_start');
            $out['out_debtval']=$prof_debt;
            if($radio == "amount") {
                $out['out_sales']=($prof_sales==0 ? $this->EMPTY_PROFIT : number_format($prof_sales,0,'.',','));
                $out['out_revenue']=($prof_revenue==0 ? $this->EMPTY_PROFIT : '$'.number_format(floatval($prof_revenue),0,'.',','));
                $out['out_profit']=($prof_gross_profit==0 ? $this->EMPTY_PROFIT : '$'.number_format($prof_gross_profit,0,'.',','));
                $out['out_profitperc']=($prof_revenue==0 ? '&nbsp;' : round($prof_gross_profit/$prof_revenue*100,0).'%');
                $out['out_operating']=($prof_operating==0 ? $this->EMPTY_PROFIT : '$'.number_format($prof_operating,0,'.',','));
                $out['out_payroll']=($prof_payroll==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_payroll,0,'.',','));
                $out['out_advertising']=($prof_advertising==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_advertising,0,'.',','));
                $out['out_projects']=($prof_projects==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_projects,0,'.',','));
                $out['out_purchases']=($prof_purchases==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_purchases,0,'.',','));
                $out['out_w9work']=($prof_w9==0 ? $this->EMPTY_PROFIT : MoneyOutput($prof_w9));
                $out['out_totalcost']=($prof_total_cost==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_total_cost,0,'.',','));
                $out['out_netprofit']=($prof_net_profit==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_net_profit,0,'.',','));
                $out['out_totalcostperc']=($prof_revenue==0 ? '&nbsp;' : round($prof_total_cost/$prof_revenue*100,0).'%');
                $out['out_netprofitperc']=($prof_revenue==0 ? '&nbsp;' : round($prof_net_profit/$prof_revenue*100,0).'%');
                $out['out_netgrossprofitperc']=($prof_gross_profit==0 ? '&nbsp;' : round($prof_net_profit/$prof_gross_profit*100,0).'%');
                $value=$prof_debt;
                if ($value<0) {
                    $out['out_debt']='($'.  number_format(abs($prof_debt),0,'.',',').')';
                    $out['out_debtclass']='color_red';
                } else {
                    $out['out_debt']=($prof_debt==0 ? $this->EMPTY_PROFIT : '+$'.  number_format($prof_debt,0,'.',','));
                    $out['out_debtclass']=($prof_debt==0 ? '' : 'color_blue2');
                }
                $value=$prof_saved;
                if ($value<0) {
                    $out['out_saved']='-$'.  number_format(abs($prof_saved),0,'.',',');
                } else {
                    $out['out_saved']=($prof_saved==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_saved,0,'.',','));
                }
                $value=$prof_od;
                if ($value<0) {
                    $out['out_od'] = '-$'.number_format(abs($prof_od),0,'.',',');
                } else {
                    $out['out_od'] = ($prof_od == 0 ? $this->EMPTY_PROFIT : '$'.number_format($prof_od,0,'.',','));
                }
            } else {
                $out['out_sales']=($prof_sales==0 ? $this->EMPTY_PROFIT : number_format($prof_sales,0,'.',','));
                $out['out_revenue']=($prof_revenue==0 ? $this->EMPTY_PROFIT : '$'.number_format(floatval($prof_revenue),0,'.',','));
                $out['out_profit']=($prof_gross_profit==0 ? $this->EMPTY_PROFIT : '$'.number_format($prof_gross_profit,0,'.',','));
                $out['out_profitperc']=($prof_revenue==0 ? '&nbsp;' : round($prof_gross_profit/$prof_revenue*100,0).'%');
                $out['out_operating']=(floatval($prof_operating)==0 ? $this->EMPTY_PROFIT : round(abs($prof_operating/$prof_revenue)*100,0).'%');
                $out['out_payroll']=(floatval($prof_payroll)==0 ? $this->EMPTY_PROFIT : round(abs($prof_payroll/$prof_revenue)*100,0).'%');
                $out['out_advertising']=(floatval($prof_advertising)==0 ? $this->EMPTY_PROFIT : round(abs($prof_advertising/$prof_revenue)*100,0).'%');
                $out['out_projects']=(floatval($prof_projects)==0 ? $this->EMPTY_PROFIT : round(abs($prof_projects/$prof_revenue)*100,0).'%');
                $out['out_purchases']=(floatval($prof_purchases)==0 ? $this->EMPTY_PROFIT : round(abs($prof_purchases/$prof_revenue)*100,0).'%');
                $out['out_w9work']=($prof_w9==0 ? $this->EMPTY_PROFIT : round(abs($prof_w9/$prof_revenue)*100,0).'%');
                $out['out_totalcost']='&nbsp;';
                $out['out_netprofit']='&nbsp;';
                $out['out_totalcostperc']=($prof_revenue==0 ? '&nbsp;' : round($prof_total_cost/$prof_revenue*100,0).'%');
                $out['out_netprofitperc']=($prof_revenue==0 ? '&nbsp;' : round($prof_net_profit/$prof_revenue*100,0).'%');
                $out['out_netgrossprofitperc']=($prof_gross_profit==0 ? '&nbsp;' : round($prof_net_profit/$prof_gross_profit*100,0).'%');
                $value=$prof_debt;
                if ($value<0) {
                    $out['out_debt']='-$'.  number_format(abs($prof_debt),0,'.',',');
                } else {
                    $out['out_debt']=($prof_debt==0 ? $this->EMPTY_PROFIT : '$'.  number_format($prof_debt,0,'.',','));
                }
                $out['out_saved']=(floatval($prof_saved)==0 ? $this->EMPTY_PROFIT : round(abs($prof_saved/$prof_revenue)*100,0).'%');
                $out['out_od'] = (floatval($prof_od)==0 ? $this->EMPTY_PROFIT : round(abs($prof_od/$prof_revenue)*100,0).'%');
            }
        }
        return $out;
    }

    public function get_netprofit_data($datebgn, $dateend, $order, $direc, $user_id, $radio, $brand, $limitshow=0) {
        /* Get data about Weeks results and profit, revenue, num of sales */
        $this->db->select('nd.datebgn, nd.dateend, nd.profit_id, nd.profit_week, nd.profit_year, sum(np.profit_operating) as profit_operating, sum(np.interest) as interest');
        $this->db->select('sum(np.profit_payroll) as profit_payroll, sum(np.profit_advertising) as profit_advertising');
        $this->db->select('sum(np.profit_projects) as profit_projects, sum(np.profit_w9) as profit_w9, sum(np.profit_purchases) as profit_purchases');
        $this->db->select('sum(np.profit_saved) as profit_saved, sum(np.profit_debt) as profit_debt, sum(np.profit_owners) as profit_owners');
        $this->db->select('sum(np.od2) as od2');
        $this->db->select('netprofit_revenue(nd.datebgn, nd.dateend, \''.$brand.'\') as revenue, netprofit_profit(nd.datebgn, nd.dateend,\''.$brand.'\') as gross_profit,');
        $this->db->select('netprofit_cntsale(nd.datebgn, nd.dateend, \''.$brand.'\') as sales, netprofit_cntproj(nd.datebgn, nd.dateend,\''.$brand.'\') as cntproj');
        $this->db->select('netprofit_totalcost(nd.datebgn, nd.dateend,1,\''.$brand.'\') as totalcost');
        $this->db->select('netprofit_netprofit(nd.datebgn, nd.dateend,1,\''.$brand.'\') as netprofit');
        $this->db->select('netprofit_qtysold(nd.datebgn, nd.dateend,\''.$brand.'\') as pcssold');
        $this->db->select('min(np.debtinclude) as debtinclude, min(runinclude) as runinclude, min(weekcheck) as weekcheck');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('nd.datebgn >= ',$datebgn);
        $this->db->where('nd.dateend <= ',$dateend);
        $this->db->where('nd.profit_month is NULL');
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('np.brand', ['BT','SB']);
            } else {
                $this->db->where('np.brand', $brand);
            }
        }
        $this->db->group_by('nd.datebgn, nd.dateend, nd.profit_id');
        $this->db->order_by($order, $direc);
        if ($limitshow>0) {
            $this->db->limit($limitshow);
        }
        $results=$this->db->get()->result_array();

        $out_array=array();
        $outkey=array();
        foreach ($results as $row) {
            $row['datarowclass']='';
            if ($row['profit_week']==1) {
                $row['datarowclass']='yearbegin';
            }
            $dstart=$row['datebgn'];
            $dend=$row['dateend'];
            $row['year']=date('Y',$dstart);
            $weekname='';
            if (date('M',$dstart)!=date('M',$dend)) {
                $weekname.=date('M',$dstart).'/'.date('M',$dend);
            } else {
                $weekname.=date('M',$dstart);
            }
            $weekname.=' '.date('j',$dstart).'-'.date('j',$dend);
            $weekname.=','.date('Y', $dend);
            $row['week']=$weekname;
            $row['out_revenue']=(floatval($row['revenue'])==0 ? $this->EMPTY_PROFIT : MoneyOutput($row['revenue'],0));
            $row['out_profit']=(floatval($row['gross_profit'])==0 ? $this->EMPTY_PROFIT : MoneyOutput($row['gross_profit'],0));
            $row['out_profitperc']=(floatval($row['revenue'])==0 ? '&nbsp;' : round($row['gross_profit']/$row['revenue']*100,0).'%');
            $row['out_operating']=$this->EMPTY_PROFIT;
            $row['operating_class']='';
            $row['out_payroll']=$this->EMPTY_PROFIT;
            $row['payroll_class']='';
            $row['out_advertising']=$this->EMPTY_PROFIT;
            $row['advertising_class']='';
            $row['out_projects']=$this->EMPTY_PROFIT;
            $row['projects_class']='';
            $row['out_purchases']=$this->EMPTY_PROFIT;
            $row['purchases_class']='';
            $row['out_w9']=$this->EMPTY_PROFIT;
            $row['w9work_class']='';
            $row['out_totalcost']='Not Calc Yet';
            $row['totalcost_class']='';
            $row['out_netprofit']='Not Calc Yet';
            $row['netprofit_class']='';
            $row['out_debt']=$this->EMPTY_PROFIT;
            $row['debt_class']='';
            $row['out_owners']=$this->EMPTY_PROFIT;
            $row['owners_class']='';
            $row['saved_class']='';
            $row['revenue']=$row['revenue'];
            $row['gross_profit']=$row['gross_profit'];
            $row['sales']=number_format($row['sales'],0,'.','');
            $row['cntord']=$row['cntproj'];
            $row['profit_class']=(floatval($row['cntproj'])==0 ? '' : 'projprof');
            $row['debtinclude']=$row['debtinclude'];
            $row['datord']=$row['profit_year'].'-'.str_pad($row['profit_week'], 2,"0",STR_PAD_LEFT);
            /* ANALYSE */
            $y = $row['profit_year'];
            $tq = str_pad($row['profit_week'], 2,"0",STR_PAD_LEFT);
            $row['tax_quarter_class'] = '';
            if($row['profit_year'] < 2016) {
                $row['tax_quarter'] = "&nbsp;";
            } else {
                if($tq == 1) {
                    $row['tax_quarter_class'] = 'tax_border';
                    $row['tax_quarter'] = "TQ1";
                } elseif ($tq == 14) {
                    $row['tax_quarter_class'] = 'tax_border';
                    $row['tax_quarter'] = "TQ2";
                } elseif ($tq == 22) {
                    $row['tax_quarter_class'] = 'tax_border';
                    $row['tax_quarter'] = "TQ3";
                } elseif ($tq == 36) {
                    $row['tax_quarter_class'] = 'tax_border';
                    $row['tax_quarter'] = "TQ4";
                } else {
                    $row['tax_quarter'] = $tq;
                }
            }
            $operating=floatval($row['profit_operating']);
            $payroll=floatval($row['profit_payroll']);
            $advertising=floatval($row['profit_advertising']);
            $projects=floatval($row['profit_projects']);
            $w9=floatval($row['profit_w9']);
            $purchases=floatval($row['profit_purchases']);
            // $debt=floatval($results[$key]['profit_debt']);
            $owners=floatval($row['profit_owners']);
            $saved=  floatval($row['profit_saved']);
            $od2=floatval($row['od2']);
            $debtinclude=intval($row['debtinclude']);
            $runinclude = intval($row['runinclude']);
            $row['run_include']='<i class="fa fa-square-o" aria-hidden="true"></i>';
            if ($runinclude==1) {
                $row['run_include']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            }
            // weekcheck
            $row['week_check'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
            $row['weekcheck_class'] = '';
            if ($row['weekcheck']==1) {
                $row['week_check']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                $row['weekcheck_class']='included';
            }
            if($radio == "amount") {
                if ($operating<0) {
                    $row['out_operating']=MoneyOutput(abs($operating),0);
                    $row['operating_class']='color_green';
                } elseif ($operating>0) {
                    $row['out_operating']='('.MoneyOutput($operating,0).')';
                    $row['operating_class']='color_red';
                }
                if ($payroll<0) {
                    $row['out_payroll']=MoneyOutput(abs($payroll),0);
                    $row['payroll_class']='color_green';
                } elseif ($payroll>0) {
                    $row['out_payroll']='('.MoneyOutput($payroll,0).')';
                    $row['payroll_class']='color_red';
                }

                if ($advertising<0) {
                    $row['out_advertising']= MoneyOutput(abs($advertising),0);
                    $row['advertising_class']='color_green';
                } elseif($advertising>0) {
                    $row['out_advertising']='('.MoneyOutput($advertising,0).')';
                    $row['advertising_class']='color_red';
                }

                if ($projects<0) {
                    $row['out_projects']=MoneyOutput(abs($projects),0);
                    $row['projects_class']='color_green';
                } elseif($projects>0) {
                    $row['out_projects']='('.MoneyOutput($projects,0).')';
                    $row['projects_class']='color_red';
                }
                if ($w9<0) {
                    $row['out_w9']=MoneyOutput(abs($w9),0);
                    $row['w9work_class']='color_green';
                } elseif ($w9>0) {
                    $row['out_w9']='('.MoneyOutput($w9,0).')';
                    $row['w9work_class']='color_red';
                }
                if ($purchases<0) {
                    $row['out_purchases']=MoneyOutput(abs($purchases),0);
                    $row['purchases_class']='color_green';
                } elseif ($purchases>0) {
                    $row['out_purchases']='('.MoneyOutput($purchases,0).')';
                    $row['purchases_class']='color_red';
                }
                $totalcost=$row['totalcost'];
                if ($totalcost<0) {
                    $row['out_totalcost']=MoneyOutput(abs($totalcost),0);
                    $row['totalcost_class']='color_green2';
                } elseif($totalcost>0) {
                    $row['out_totalcost']='('.MoneyOutput($totalcost,0).')';
                    $row['totalcost_class']='color_red2';
                }
                $row['out_totalcostperc']='&nbsp;';
                if (floatval($row['revenue'])!=0) {
                    $row['out_totalcostperc']=(floatval($totalcost)==0 ? '&nbsp;' : round(abs($totalcost/floatval($row['revenue']))*100,0).'%');
                }

                $netprofit=$row['netprofit'];
                if ($netprofit>0) {
                    $row['out_netprofit']=MoneyOutput($netprofit,0);
                    $row['netprofit_class']='color_green';
                } elseif($netprofit<0) {
                    $row['out_netprofit']='('.MoneyOutput(abs($netprofit),0).')';
                    $row['netprofit_class']='color_red';
                }
                $row['out_netprofitperc']='&nbsp;';
                if (floatval($row['revenue'])!=0) {
                    $row['out_netprofitperc']=(floatval($netprofit)==0 ? '&nbsp;' : round(abs($netprofit/$row['revenue']*100),0).'%');
                }
                $row['out_netgrossprofitperc']=(floatval($row['gross_profit'])==0 ? '&nbsp;' : round($netprofit/$row['gross_profit']*100,0).'%');
                if ($debtinclude==0) {
                    $row['debt_include']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                } else {
                    $row['debt_include']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                }
                $debt=floatval($netprofit)-floatval($owners)-floatval($saved)-floatval($od2);

                if (round($debt,0)>0) {
                    $row['out_debt']=MoneyOutput($debt,0);
                    $row['debt_class']='color_blue2';
                } elseif (round($debt,0)<0) {
                    $row['out_debt']='('.MoneyOutput(abs($debt),0).')';
                    $row['debt_class']='color_red';
                } else {
                    $row['out_debt']=$this->EMPTY_PROFIT;
                }
                if (round($saved,0)>0) {
                    $row['out_saved']=MoneyOutput($saved,0);
                    $row['saved_class']='color_blue2';
                } elseif (round($saved,0)<0) {
                    $row['out_saved']='('.MoneyOutput(abs($saved),0).')';
                    $row['saved_class']='color_red';
                } else {
                    $row['out_saved']=$this->EMPTY_PROFIT;
                }

                $out_od = $od2 + $owners;
                if(round($out_od,0)>0) {
                    $row['out_od'] = MoneyOutput($out_od,0);
                    $row['out_od_class'] = 'color_blue2';
                } elseif (round($out_od,0)<0) {
                    $row['out_od'] = '('.MoneyOutput(abs($out_od),0).')';
                    $row['out_od_class'] = 'color_red';
                } else {
                    $row['out_od'] = $this->EMPTY_PROFIT;
                    $row['out_od_class'] = '';
                }
            } else {
                if ($operating<0) {
                    $row['out_operating']=(floatval($operating)==0 ? $this->EMPTY_PROFIT : round(abs($operating/floatval($row['revenue']))*100,0).'%');
                    $row['operating_class']='color_green';
                } elseif ($operating>0) {
                    $row['out_operating']='('.(floatval($operating)==0 ? $this->EMPTY_PROFIT : round(abs($operating/floatval($row['revenue']))*100,0).'%').')';
                    $row['operating_class']='color_red';
                }

                if ($payroll<0) {
                    $row['out_payroll']=(floatval($payroll)==0 ? $this->EMPTY_PROFIT : round(abs($payroll/floatval($row['revenue']))*100,0).'%');
                    $row['payroll_class']='color_green';
                } elseif ($payroll>0) {
                    $row['out_payroll']='('.(floatval($payroll)==0 ? $this->EMPTY_PROFIT : round(abs($payroll/floatval($row['revenue']))*100,0).'%').')';
                    $row['payroll_class']='color_red';
                }

                if ($advertising<0) {
                    $row['out_advertising']=(floatval($advertising)==0 ? $this->EMPTY_PROFIT : round(abs($advertising/floatval($row['revenue']))*100,0).'%');
                    $row['advertising_class']='color_green';
                } elseif($advertising>0) {
                    $row['out_advertising']='('.(floatval($advertising)==0 ? $this->EMPTY_PROFIT : round(abs($advertising/floatval($row['revenue']))*100,0).'%').')';
                    $row['advertising_class']='color_red';
                }

                if ($projects<0) {
                    $row['out_projects']=(floatval($projects)==0 ? $this->EMPTY_PROFIT : round(abs($projects/floatval($row['revenue']))*100,0).'%');
                    $row['projects_class']='color_green';
                } elseif($projects>0) {
                    $row['out_projects']='('.(floatval($projects)==0 ? $this->EMPTY_PROFIT : round(abs($projects/floatval($row['revenue']))*100,0).'%').')';
                    $row['projects_class']='color_red';
                }
                if ($w9<0) {
                    $row['out_w9']=(floatval($w9)==0 ? $this->EMPTY_PROFIT : round(abs($w9/floatval($row['revenue']))*100,0).'%');
                    $row['w9work_class']='color_green';
                } elseif($w9>0) {
                    $row['out_w9']='('.(floatval($w9)==0 ? $this->EMPTY_PROFIT : round(abs($w9/floatval($row['revenue']))*100,0).'%').')';
                    $row['w9work_class']='color_red';
                }

                if ($purchases<0) {
                    $row['out_purchases']=(floatval($purchases)==0 ? $this->EMPTY_PROFIT : round(abs($purchases/floatval($row['revenue']))*100,0).'%');
                    $row['purchases_class']='color_green';
                } elseif ($purchases>0) {
                    $row['out_purchases']='('.(floatval($purchases)==0 ? $this->EMPTY_PROFIT : round(abs($purchases/floatval($row['revenue']))*100,0).'%').')';
                    $row['purchases_class']='color_red';
                }
                // $totalcost=$operating+$payroll+$advertising+$projects+$purchases;
                $totalcost=$row['totalcost'];
                if ($totalcost<0) {
                    $row['out_totalcost']='&nbsp;';
                    $row['totalcost_class']='color_green2';
                } elseif($totalcost>0) {
                    $row['out_totalcost']='&nbsp;';
                    $row['totalcost_class']='color_red2';
                }
                $row['out_totalcostperc']=(floatval($totalcost)==0 ? '&nbsp;' : round(abs($totalcost/floatval($row['revenue']))*100,0).'%');
                $netprofit=$row['netprofit'];
                if ($netprofit>0) {
                    $row['out_netprofit']='&nbsp;';
                    $row['netprofit_class']='color_green';
                } elseif($netprofit<0) {
                    $row['out_netprofit']='&nbsp;';
                    $row['netprofit_class']='color_red';
                }
                $row['out_netprofitperc']=(floatval($netprofit)==0 ? '&nbsp;' : round(abs($netprofit/$row['revenue']*100),0).'%');
                $row['out_netgrossprofitperc']=(floatval($row['gross_profit'])==0 ? '&nbsp;' : round($netprofit/$row['gross_profit']*100,0).'%');

                if ($debtinclude==0) {
                    $row['debt_include']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                } else {
                    $row['debt_include']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                }
                $debt=floatval($netprofit)-floatval($owners)-floatval($saved)-floatval($od2);

                if (round($debt,0)>0) {
                    $row['out_debt']='$'.number_format($debt,0,'.',',');
                    $row['debt_class']='color_blue2';
                } elseif (round($debt,0)<0) {
                    $row['out_debt']='($'.number_format(abs($debt),0,'.',',').')';
                    $row['debt_class']='color_red';
                } else {
                    $row['out_debt']=$this->EMPTY_PROFIT;
                }
                if (round($saved,0)>0) {
                    $row['out_saved']=(floatval($saved)==0 ? $this->EMPTY_PROFIT : round(abs($saved/floatval($row['revenue']))*100,0).'%');
                    $row['saved_class']='color_blue2';
                } elseif (round($saved,0)<0) {
                    $row['out_saved']='('.(floatval($saved)==0 ? $this->EMPTY_PROFIT : round(abs($saved/floatval($row['revenue']))*100,0).'%').')';
                    $row['saved_class']='color_red';
                } else {
                    $row['out_saved']=$this->EMPTY_PROFIT;
                }
                $out_od = $od2 + $owners;
                if(round($out_od,0)>0) {
                    $row['out_od'] = (floatval($out_od)==0 ? $this->EMPTY_PROFIT : round(abs($out_od/floatval($row['revenue']))*100,0).'%');
                    $row['out_od_class'] = 'color_blue2';
                } elseif (round($out_od,0)<0) {
                    $row['out_od'] = '('.(floatval($out_od)==0 ? $this->EMPTY_PROFIT : round(abs($out_od/floatval($row['revenue']))*100,0).'%').')';
                    $row['out_od_class'] = 'color_red';
                } else {
                    $row['out_od'] = $this->EMPTY_PROFIT;
                    $row['out_od_class'] = '';
                }
            }
            // Notes class
            $row['notesclass']='';
            $row['shownote']=0;
            if (!empty($row['weeknote'])) {
                $row['shownote']=1;
                $this->db->select('count(d.netprofit_detail_id) as cnt');
                $this->db->from('ts_netprofit_details d');
                $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id');
                $this->db->where('d.profit_id', $row['profit_id']);
                $this->db->where('c.category_type','Purchase');
                $detres=$this->db->get()->row_array();
                if ($detres['cnt']==0) {
                    $row['notesclass']='emptynetproofnote';
                }
            } else {
                $this->db->select('count(d.netprofit_detail_id) as cnt');
                $this->db->from('ts_netprofit_details d');
                $this->db->where('d.profit_id', $row['profit_id']);
                $detres=$this->db->get()->row_array();
                if ($detres['cnt']>0) {
                    $row['shownote']=1;
                }
            }
            $out_array[]=$row;
            array_push($outkey, $row['profit_year'].'-'.str_pad($row['profit_week'], 2,"0",STR_PAD_LEFT));
        }

        return $out_array;
    }

    public function get_netprofit_monthdata($datebgn, $dateend, $order, $direc, $user_id, $radio, $brand) {
        $this->db->select('nd.datebgn, nd.dateend, nd.profit_id, nd.profit_month, nd.profit_year, sum(np.profit_operating) as profit_operating, sum(np.interest) as interest');
        $this->db->select('sum(np.profit_payroll) as profit_payroll, sum(np.profit_advertising) as profit_advertising');
        $this->db->select('sum(np.profit_projects) as profit_projects, sum(np.profit_w9) as profit_w9, sum(np.profit_purchases) as profit_purchases');
        $this->db->select('sum(np.profit_saved) as profit_saved, sum(np.profit_debt) as profit_debt, sum(np.profit_owners) as profit_owners');
        $this->db->select('sum(np.od2) as od2');
        $this->db->select('netprofit_revenue(nd.datebgn, nd.dateend, \''.$brand.'\') as revenue, netprofit_profit(nd.datebgn, nd.dateend,\''.$brand.'\') as gross_profit,');
        $this->db->select('netprofit_cntsale(nd.datebgn, nd.dateend, \''.$brand.'\') as sales, netprofit_cntproj(nd.datebgn, nd.dateend,\''.$brand.'\') as cntproj');
        $this->db->select('netprofit_totalcost(nd.datebgn, nd.dateend,0,\''.$brand.'\') as totalcost');
        $this->db->select('netprofit_netprofit(nd.datebgn, nd.dateend,0,\''.$brand.'\') as netprofit');
        $this->db->select('netprofit_qtysold(nd.datebgn, nd.dateend,\''.$brand.'\') as pcssold');
        $this->db->select('min(np.debtinclude) as debtinclude');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('nd.datebgn >= ',$datebgn);
        $this->db->where('nd.dateend <= ',$dateend);
        $this->db->where('nd.profit_week is NULL');
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('np.brand', ['BT','SB']);
            } else {
                $this->db->where('np.brand', $brand);
            }
        }
        $this->db->group_by('nd.datebgn, nd.dateend, nd.profit_id');
        $this->db->order_by($order, $direc);
        $results=$this->db->get()->result_array();

        $monthdat=array();
        foreach ($results as $row) {
            array_push($monthdat, str_pad($row['profit_month'], 2, '0', STR_PAD_LEFT).'-'.$row['profit_year']);
        }
        /* Get Grant Profit */
        $out_array=array();
        $outkey=array();
        foreach ($results as $row) {
            $row['datarowclass']='';
            if ($row['profit_month']==1) {
                $row['datarowclass']='yearbegin';
            }
            $this->db->select('sum(order_qty) as pcssold');
            $this->db->from('ts_orders');
            $this->db->where('order_date >= ',$datebgn);
            $this->db->where('order_date <= ',$dateend);
            $this->db->where('is_canceled',0);
            $cntsld=$this->db->get()->row_array();
            $row['pcssold']=intval($cntsld['pcssold']);

            $row['out_revenue']=(floatval($row['revenue'])==0 ? $this->EMPTY_PROFIT : MoneyOutput($row['revenue'],0));
            $row['out_profit']=(floatval($row['gross_profit'])==0 ? $this->EMPTY_PROFIT : MoneyOutput($row['gross_profit'],0));
            $row['out_profitperc']=(floatval($row['revenue'])==0 ? '&nbsp;' : round($row['gross_profit']/$row['revenue']*100,0));
            $row['profit_class']=(floatval($row['cntproj'])==0 ? '' : 'projprof');
            $row['sales']=($row['sales']==0 ? $this->EMPTY_PROFIT : number_format($row['sales'],0,'.',''));
            $row['datord']=$row['profit_year'].'-'.str_pad($row['profit_month'], 2,"0",STR_PAD_LEFT);
            $row['year']=$row['profit_year'];
            $row['week']=date('M, Y',$row['datebgn']);
            $operating=floatval($row['profit_operating']);
            $payroll=floatval($row['profit_payroll']);
            $advertising=floatval($row['profit_advertising']);
            $projects=floatval($row['profit_projects']);
            $purchases=floatval($row['profit_purchases']);
            $w9work=floatval($row['profit_w9']);
            /* $debt=floatval($results[$key]['profit_debt']); */
            $owners=floatval($row['profit_owners']);
            $od2=floatval($row['od2']);
            $od = $owners + $od2;
            $saved=floatval($row['profit_saved']);
            $row['tax_quarter_class'] = '';
            $row['tax_quarter'] = "&nbsp;";
            if($radio == "amount") {
                $row['operating_class']='';
                $row['out_operating']=$this->EMPTY_PROFIT;
                if ($operating<0) {
                    $row['out_operating']=($operating==0 ? $this->EMPTY_PROFIT : MoneyOutput(abs($operating),0));
                    $row['operating_class']='color_green';
                } elseif ($operating>0) {
                    $row['out_operating']='('.MoneyOutput($operating,0).')';
                    $row['operating_class']='color_red';
                }
                $row['payroll_class']='';
                $row['out_payroll']=$this->EMPTY_PROFIT;
                if ($payroll<0) {
                    $row['out_payroll']=MoneyOutput(abs($payroll),0,'.',',');
                    $row['payroll_class']='color_green';
                } elseif ($payroll>0) {
                    $row['out_payroll']='('.MoneyOutput($payroll,0).')';
                    $row['payroll_class']='color_red';
                }
                $row['advertising_class']='';
                $row['out_advertising']=$this->EMPTY_PROFIT;
                if ($advertising<0) {
                    $row['out_advertising']=MoneyOutput(abs($advertising),0);
                    $row['advertising_class']='color_green';
                } elseif($advertising>0) {
                    $row['out_advertising']='('.MoneyOutput($advertising,0).')';
                    $row['advertising_class']='color_red';
                }
                $row['projects_class']='';
                $row['out_projects']=$this->EMPTY_PROFIT;
                if ($projects<0) {
                    $row['out_projects']=MoneyOutput(abs($projects),0);
                    $row['projects_class']='color_green';
                } elseif($projects>0) {
                    $row['out_projects']='('.MoneyOutput($projects,0).')';
                    $row['projects_class']='color_red';
                }
                $row['w9work_class']='';
                $row['out_w9']=$this->EMPTY_PROFIT;
                if ($w9work<0) {
                    $row['out_w9']=MoneyOutput(abs($w9work),0);
                    $row['w9work_class']='color_green';
                } else {
                    $row['out_w9']='('.MoneyOutput($w9work,0).')';
                    $row['w9work_class']='color_red';
                }
                $row['purchases_class']='';
                $row['out_purchases']=$this->EMPTY_PROFIT;
                if ($purchases<0) {
                    $row['out_purchases']=MoneyOutput(abs($purchases),0);
                    $row['purchases_class']='color_green';
                } elseif ($purchases>0) {
                    $row['out_purchases']='('.MoneyOutput($purchases,0).')';
                    $row['purchases_class']='color_red';
                }
                $totalcost=$row['totalcost'];
                $row['out_totalcost']=$this->EMPTY_PROFIT;
                $row['totalcost_class']='';
                if ($totalcost<0) {
                    $row['out_totalcost']=MoneyOutput(abs($totalcost),0);
                    $row['totalcost_class']='color_green2';
                } elseif($totalcost>0) {
                    $row['out_totalcost']='('.MoneyOutput($totalcost,0).')';
                    $row['totalcost_class']='color_red2';
                }
                $row['out_totalcostperc']=(floatval($totalcost)==0 ? '&nbsp;' : round(abs($totalcost/floatval($row['revenue']))*100,0).'%');
                $row['out_netprofit']=  $this->EMPTY_PROFIT;
                $row['netprofit_class']='';
                $netprofit=$row['netprofit'];
                if ($netprofit>0) {
                    $row['out_netprofit']=MoneyOutput($netprofit,0);
                    $row['netprofit_class']='color_green';
                } elseif($netprofit<0) {
                    $row['out_netprofit']='('.MoneyOutput(abs($netprofit),0).')';
                    $row['netprofit_class']='color_red';
                }
                $row['out_netprofitperc']=(floatval($netprofit)==0 ? '&nbsp;' : round(abs($netprofit/$row['revenue'])*100,0).'%');
                $row['out_netgrossprofitperc']=(floatval($row['gross_profit'])==0 ? '&nbsp;' : round($netprofit/$row['gross_profit']*100,0).'%');
                if ($row['debtinclude']==0) {
                    $row['debt_include']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                } else {
                    $row['debt_include']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                }
//                if ($row['debtinclude']==0) {
//                    $row['debt_include']='<input type="checkbox" class="netdebincl" id="netdebincl'.$row['datord'].'" />';
//                } else {
//                    $row['debt_include']='<input type="checkbox" class="netdebincl" id="netdebincl'.$row['datord'].'" checked="checked"/>';
//                }
                $row['debt_class']='';
                $row['out_debt']=$this->EMPTY_PROFIT;
                $debt=$netprofit-$owners-$saved-$od2;
                if ($debt>0) {
                    $row['out_debt']=MoneyOutput($debt,0);
                    $row['debt_class']='color_blue2';
                } elseif ($debt<0) {
                    $row['out_debt']='('.MoneyOutput(abs($debt),0).')';
                    $row['debt_class']='color_red';
                }
                $row['owners_class']='';
                $row['out_owners']=$this->EMPTY_PROFIT;
                if ($owners>0) {
                    $row['out_owners']=MoneyOutput($owners,0);
                    $row['owners_class']='color_blue2';
                } elseif ($owners<0) {
                    $row['out_owners']='('.MoneyOutput(abs($owners),0).')';
                    $row['owners_class']='color_red';
                }
                $row['od2_class']='';
                $row['out_od2']=$this->EMPTY_PROFIT;
                if ($od2>0) {
                    $row['out_od2']=MoneyOutput($od2,0,'.',',');
                    $row['od2_class']='color_blue2';
                } elseif ($od2<0) {
                    $row['out_d2']='('.MoneyOutput(abs($od2),0).')';
                    $row['od2_class']='color_red';
                }
                $row['out_od_class']='';
                $row['out_od']=$this->EMPTY_PROFIT;
                if ($od2>0) {
                    $row['out_od']=MoneyOutput($od,0);
                    $row['out_od_class']='color_blue2';
                } elseif ($od2<0) {
                    $row['out_od']='('.MoneyOutput(abs($od),0).')';
                    $row['out_od_class']='color_red';
                }

                $row['saved_class']='';
                $row['out_saved']=$this->EMPTY_PROFIT;
                if ($saved>0) {
                    $row['out_saved']=MoneyOutput($saved,0);
                    $row['saved_class']='color_blue2';
                } elseif ($saved<0) {
                    $row['out_saved']='('.MoneyOutput(abs($saved),0).')';
                    $row['saved_class']='color_red';
                }
            } else {
                $row['operating_class']='';
                if ($operating<0) {
                    $row['out_operating']=(floatval($operating)==0 ? $this->EMPTY_PROFIT : round(abs($operating/floatval($row['revenue']))*100,0).'%');
                    $row['operating_class']='color_green';
                } elseif ($operating>0) {
                    $row['out_operating']='('.(floatval($operating)==0 ? $this->EMPTY_PROFIT : round(abs($operating/floatval($row['revenue']))*100,0).'%').')';
                    $row['operating_class']='color_red';
                } else {
                    $row['out_operating']=$this->EMPTY_PROFIT;
                }
                $row['payroll_class']='';
                if ($payroll<0) {
                    $row['out_payroll']=(floatval($payroll)==0 ? $this->EMPTY_PROFIT : round(abs($payroll/floatval($row['revenue']))*100,0).'%');
                    $row['payroll_class']='color_green';
                } elseif ($payroll>0) {
                    $row['out_payroll']='('.(floatval($payroll)==0 ? $this->EMPTY_PROFIT : round(abs($payroll/floatval($row['revenue']))*100,0).'%').')';
                    $row['payroll_class']='color_red';
                } else {
                    $row['out_payroll']=$this->EMPTY_PROFIT;
                }
                $row['advertising_class']='';
                if ($advertising<0) {
                    $row['out_advertising']=(floatval($advertising)==0 ? $this->EMPTY_PROFIT : round(abs($advertising/floatval($row['revenue']))*100,0).'%');
                    $row['advertising_class']='color_green';
                } elseif($advertising>0) {
                    $row['out_advertising']='('.(floatval($advertising)==0 ? $this->EMPTY_PROFIT : round(abs($advertising/floatval($row['revenue']))*100,0).'%').')';
                    $row['advertising_class']='color_red';
                } else {
                    $row['out_advertising']=$this->EMPTY_PROFIT;
                }
                $row['projects_class']='';
                if ($projects<0) {
                    $row['out_projects']=(floatval($projects)==0 ? $this->EMPTY_PROFIT : round(abs($projects/floatval($row['revenue']))*100,0).'%');
                    $row['projects_class']='color_green';
                } elseif($projects>0) {
                    $row['out_projects']='('.(floatval($projects)==0 ? $this->EMPTY_PROFIT : round(abs($projects/floatval($row['revenue']))*100,0).'%').')';
                    $row['projects_class']='color_red';
                } else {
                    $row['out_projects']=$this->EMPTY_PROFIT;
                }
                $row['w9work_class']='';
                $row['out_w9']=  $this->EMPTY_PROFIT;
                if ($w9work<0) {
                    $row['out_w9']=(floatval($w9work)==0 ? $this->EMPTY_PROFIT : round(abs($w9work/floatval($row['revenue']))*100,0).'%');
                    $row['w9work_class']='color_green';
                } elseif ($w9work>0) {
                    $row['out_w9']='('.(floatval($w9work)==0 ? $this->EMPTY_PROFIT : round(abs($w9work/floatval($row['revenue']))*100,0).'%').')';
                    $row['w9work_class']='color_red';
                }
                $row['purchases_class']='';
                if ($purchases<0) {
                    $row['out_purchases']=(floatval($purchases)==0 ? $this->EMPTY_PROFIT : round(abs($purchases/floatval($row['revenue']))*100,0).'%');
                    $row['purchases_class']='color_green';
                } elseif ($purchases>0) {
                    $row['out_purchases']='('.(floatval($purchases)==0 ? $this->EMPTY_PROFIT : round(abs($purchases/floatval($row['revenue']))*100,0).'%').')';
                    $row['purchases_class']='color_red';
                } else {
                    $row['out_purchases']=$this->EMPTY_PROFIT;
                }
                // $totalcost=$operating+$payroll+$advertising+$projects+$purchases;
                $totalcost=$row['totalcost'];
                $row['out_totalcost']=$this->EMPTY_PROFIT;
                $row['totalcost_class']='';
                if ($totalcost<0) {
                    $row['out_totalcost']='&nbsp;';
                    $row['totalcost_class']='color_green2';
                } elseif($totalcost>0) {
                    $row['out_totalcost']='&nbsp;';
                    $row['totalcost_class']='color_red2';
                }
                $row['out_totalcostperc']=(floatval($totalcost)==0 ? '&nbsp;' : round(abs($totalcost/floatval($row['revenue']))*100,0).'%');
                $row['out_netprofit']=  $this->EMPTY_PROFIT;
                $row['netprofit_class']='';
                // $netprofit=floatval($row['gross_profit'])-$totalcost;
                $netprofit=$row['netprofit'];
                if ($netprofit>0) {
                    $row['out_netprofit']='&nbsp;';
                    $row['netprofit_class']='color_green';
                } elseif($netprofit<0) {
                    $row['out_netprofit']='&nbsp;';
                    $row['netprofit_class']='color_red';
                }
                $row['out_netprofitperc']=(floatval($netprofit)==0 ? '&nbsp;' : round(abs($netprofit/$row['revenue'])*100,0).'%');
//                if ($row['debtinclude']==0) {
//                    $row['debt_include']='<input type="checkbox" class="netdebincl" id="netdebincl'.$row['datord'].'" />';
//                } else {
//                    $row['debt_include']='<input type="checkbox" class="netdebincl" id="netdebincl'.$row['datord'].'" checked="checked"/>';
//                }
                if ($row['debtinclude']==0) {
                    $row['debt_include']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                } else {
                    $row['debt_include']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                }
                $row['debt_class']='';
                $debt=$netprofit-$owners-$saved-$od2;
                if ($debt>0) {
                    $row['out_debt']='$'.number_format($debt,0,'.',',');
                    $row['debt_class']='color_blue2';
                } elseif ($debt<0) {
                    $row['out_debt']='($'.number_format(abs($debt),0,'.',',').')';
                    $row['debt_class']='color_red';
                } else {
                    $row['out_debt']=$this->EMPTY_PROFIT;
                }
                $row['out_od_class']='';
                if ($od2>0) {
                    $row['out_od']=(floatval($od)==0 ? $this->EMPTY_PROFIT : round(abs($od/floatval($row['revenue']))*100,0).'%');
                    $row['out_od_class']='color_blue2';
                } elseif ($od2<0) {
                    $row['out_od']='('.(floatval($od)==0 ? $this->EMPTY_PROFIT : round(abs($od/floatval($row['revenue']))*100,0).'%').')';
                    $row['out_od_class']='color_red';
                } else {
                    $row['out_od']=$this->EMPTY_PROFIT;
                }

                $row['saved_class']='';
                if ($saved>0) {
                    $row['out_saved']=(floatval($saved)==0 ? $this->EMPTY_PROFIT : round(abs($saved/floatval($row['revenue']))*100,0).'%');
                    $row['saved_class']='color_blue2';
                } elseif ($saved<0) {
                    $row['out_saved']='('.(floatval($saved)==0 ? $this->EMPTY_PROFIT : round(abs($saved/floatval($row['revenue']))*100,0).'%').')';
                    $row['saved_class']='color_red';
                } else {
                    $row['out_saved']=$this->EMPTY_PROFIT;
                }
            }
            // Notes class
            $row['notesclass']='';
            $row['shownote']=0;
            if (!empty($row['weeknote'])) {
                $row['shownote']=1;
                $this->db->select('count(d.netprofit_detail_id) as cnt');
                $this->db->from('ts_netprofit_details d');
                $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id');
                $this->db->where('d.profit_id', $row['profit_id']);
                $this->db->where('c.category_type','Purchase');
                $detres=$this->db->get()->row_array();
                if ($detres['cnt']==0) {
                    $row['notesclass']='emptynetproofnote';
                }
            } else {
                $this->db->select('count(d.netprofit_detail_id) as cnt');
                $this->db->from('ts_netprofit_details d');
                $this->db->where('d.profit_id', $row['profit_id']);
                $detres=$this->db->get()->row_array();
                if ($detres['cnt']>0) {
                    $row['shownote']=1;
                }
            }
            $out_array[]=$row;
            array_push($outkey, str_pad($row['profit_month'],2,'0',STR_PAD_LEFT).'-'.$row['profit_year']);
        }
        return $out_array;
    }

//    /* Garbage */
//    function monthdat_grant_profit() {
//        /* Get Grant Profit */
//        $this->db->select("date_format(from_unixtime(ord.order_date),'%m-%Y') as datord, sum(ord.revenue) as revenue, sum(ord.profit) as gross_profit, count(ord.order_id) as sales, proj.cntord, sum(order_qty) as pcssold ",FALSE);
//        $this->db->from('ts_orders ord');
//        $this->db->join("(select date_format(from_unixtime(order_date),'%m-%Y') datproj, count(order_id) as cntord from ts_orders where order_cog is null and is_canceled=0 group by datproj) proj","proj.datproj=date_format(from_unixtime(ord.order_date),'%m-%Y') ","left");
//        $this->db->group_by("datord");
//        $this->db->where("ord.order_date >= ",$datebgn);
//        $this->db->where("ord.order_date <= ",$dateend);
//        $this->db->where("ord.is_canceled",0);
//        $this->db->order_by("ord.order_date",'desc');
//        $resord=$this->db->get()->result_array();
//
//        foreach ($resord as $row) {
//            /* Search in out array */
//            $key=array_search($row['datord'], $outkey);
//            $out_array[$key]['out_revenue']=(floatval($row['revenue'])==0 ? '' : '$'.number_format($row['revenue'],2,'.',','));
//            $out_array[$key]['out_profit']=(floatval($row['gross_profit'])==0 ? '' : '$'.number_format($row['gross_profit'],2,'.',','));
//            $out_array[$key]['profit_class']=(floatval($row['cntord'])==0 ? '' : 'projprof');
//            $out_array[$key]['sales']=$row['sales'];
//            $out_array[$key]['pcssold']=$row['pcssold'];
//            $out_array[$key]['datord']=$row['datord'];
//            $operating=floatval($results[$key]['profit_operating']);
//            $payroll=floatval($results[$key]['profit_payroll']);
//            $advertising=floatval($results[$key]['profit_advertising']);
//            $projects=floatval($results[$key]['profit_projects']);
//
//            $purchases=floatval($results[$key]['profit_purchases']);
//            /* $debt=floatval($results[$key]['profit_debt']); */
//            $owners=floatval($results[$key]['profit_owners']);
//            if ($operating<0) {
//                $out_array[$key]['out_operating']=($operating==0 ? $this->EMPTY_PROFIT : '$'.number_format(abs($operating),2,'.',','));
//                $out_array[$key]['operating_class']='color_green';
//            } elseif ($operating>0) {
//                $out_array[$key]['out_operating']='($'.number_format($operating,2,'.',',').')';
//                $out_array[$key]['operating_class']='color_red';
//            } else {
//                $out_array[$key]['out_operating']=$this->EMPTY_PROFIT;
//            }
//
//            if ($payroll<0) {
//                $out_array[$key]['out_payroll']='$'.number_format(abs($payroll),2,'.',',');
//                $out_array[$key]['payroll_class']='color_green';
//            } elseif ($payroll>0) {
//                $out_array[$key]['out_payroll']=($payroll==0 ? $this->EMPTY_PROFIT : '($'.number_format($payroll,2,'.',',').')');
//                $out_array[$key]['payroll_class']='color_red';
//            } else {
//                $out_array[$key]['out_payroll']=$this->EMPTY_PROFIT;
//            }
//
//            if ($advertising<0) {
//                $out_array[$key]['out_advertising']='$'.number_format(abs($advertising),2,'.',',');
//                $out_array[$key]['advertising_class']='color_green';
//
//            } elseif($advertising>0) {
//                $out_array[$key]['out_advertising']='($'.number_format($advertising,2,'.',',').')';
//                $out_array[$key]['advertising_class']='color_red';
//            } else {
//                $out_array[$key]['out_advertising']=$this->EMPTY_PROFIT;
//            }
//
//            if ($projects<0) {
//                $out_array[$key]['out_projects']='$'.number_format(abs($projects),2,'.',',');
//                $out_array[$key]['projects_class']='color_green';
//            } elseif($projects>0) {
//                $out_array[$key]['out_projects']='($'.number_format($projects,2,'.',',').')';
//                $out_array[$key]['projects_class']='color_red';
//            } else {
//                $out_array[$key]['out_projects']=$this->EMPTY_PROFIT;
//            }
//            if ($purchases<0) {
//                $out_array[$key]['out_purchases']='$'.number_format(abs($purchases),2,'.',',');
//                $out_array[$key]['purchases_class']='color_green';
//            } elseif ($purchases>0) {
//                $out_array[$key]['out_purchases']='($'.number_format($purchases,2,'.',',').')';
//                $out_array[$key]['purchases_class']='color_red';
//            } else {
//                $out_array[$key]['out_purchases']=$this->EMPTY_PROFIT;
//            }
//            $totalcost=$operating+$payroll+$advertising+$projects+$purchases;
//            if ($totalcost<0) {
//                $out_array[$key]['out_totalcost']='$'.number_format(abs($totalcost),2,'.',',');
//                $out_array[$key]['totalcost_class']='color_green2';
//            } elseif($totalcost>0) {
//                $out_array[$key]['out_totalcost']='($'.number_format($totalcost,2,'.',',').')';
//                $out_array[$key]['totalcost_class']='color_red2';
//            }
//            if ($totalcost>0) {
//                $netprofit=floatval($row['gross_profit'])-$totalcost;
//                if ($netprofit>0) {
//                    $out_array[$key]['out_netprofit']='$'.number_format($netprofit,2,'.',',');
//                    $out_array[$key]['netprofit_class']='color_green';
//                } elseif($netprofit<0) {
//                    $out_array[$key]['out_netprofit']='($'.number_format(abs($netprofit),2,'.',',').')';
//                    $out_array[$key]['netprofit_class']='color_red';
//                }
//            } else {
//                $netprofit=0;
//            }
//            $debt=$netprofit-$owners;
//            if ($debt>0) {
//                $out_array[$key]['out_debt']='$'.number_format($debt,2,'.',',');
//                $out_array[$key]['debt_class']='color_blue2';
//            } elseif ($debt<0) {
//                $out_array[$key]['out_debt']='($'.number_format(abs($debt),2,'.',',').')';
//                $out_array[$key]['debt_class']='color_red';
//            } else {
//                $out_array[$key]['out_debt']=$this->EMPTY_PROFIT;
//            }
//            if ($owners>0) {
//                $out_array[$key]['out_owners']='$'.number_format($owners,2,'.',',');
//                $out_array[$key]['owners_class']='color_blue2';
//            } elseif ($owners<0) {
//                $out_array[$key]['out_owners']='($'.number_format(abs($owners),2,'.',',').')';
//                $out_array[$key]['owners_class']='color_red';
//            } else {
//                $out_array[$key]['out_owners']=$this->EMPTY_PROFIT;
//            }
//        }
//    }

    public function include_netprofit_debt($profit_id, $brand, $type) {
        $out=array('result'=>$this->error_result,'msg'=>'Period Not Found');
        $this->db->select('profit_id,debtinclude');
        $this->db->from('netprofit_dat');
        $this->db->where('profit_id', $profit_id);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('brand', ['BT','SB']);
            } else {
                $this->db->where('brand', $brand);
            }
        }
        $res=$this->db->get()->row_array();
        if (isset($res['profit_id'])) {
            // Success - Profit data found
            $out['result']=$this->success_result;
            $newdata=0;
            $outinclude='<i class="fa fa-square-o" aria-hidden="true"></i>';
            if ($res['debtinclude']==0) {
                $newdata=1;
                $outinclude='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            }
            $this->db->where('profit_id', $profit_id);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('brand', ['BT','SB']);
                } else {
                    $this->db->where('brand', $brand);
                }
            }
            $this->db->set('debtinclude', $newdata);
            $this->db->update('netprofit_dat');
            $out['debincl']=$outinclude;
            /* Get data about Netprofit  */
            $total_options=array(
                'type'=>$type,
                'start'=>$this->config->item('netprofit_start'),
                'brand' => $brand,
            );
            $out['totals']=$this->get_netprofit_runs($total_options);
        }
        return $out;
    }

    public function include_netprofit_week($profit_id, $brand, $type) {
        $out=array('result'=>$this->error_result,'msg'=>'Period Not Found');
        $this->db->select('profit_id, runinclude');
        $this->db->from('netprofit_dat');
        $this->db->where('profit_id', $profit_id);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('brand', ['BT','SB']);
            } else {
                $this->db->where('brand', $brand);
            }
        }
        $res=$this->db->get()->row_array();
        if (isset($res['profit_id'])) {
            // Success - Profit data found
            $out['result']=$this->success_result;
            $newdata=0;
            $outinclude='<i class="fa fa-square-o" aria-hidden="true"></i>';
            if ($res['runinclude']==0) {
                $newdata=1;
                $outinclude='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            }
            $this->db->where('profit_id', $profit_id);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('brand', ['BT','SB']);
                } else {
                    $this->db->where('brand', $brand);
                }
            }
            $this->db->set('runinclude', $newdata);
            $this->db->update('netprofit_dat');
            $out['runincl']=$outinclude;
            /* Get data about Netprofit  */
            $total_options=array(
                'type'=>$type,
                'start'=>$this->config->item('netprofit_start'),
                'brand' => $brand,
            );
            $out['totals']=$this->get_netprofit_runs($total_options);
        }
        return $out;
    }

    public function netprofit_check_week($profit_id, $brand, $type) {
        $out=array('result'=>$this->error_result,'msg'=>'Period Not Found');
        $this->db->select('profit_id, weekcheck');
        $this->db->from('netprofit_dat');
        $this->db->where('profit_id', $profit_id);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('brand', ['BT','SB']);
            } else {
                $this->db->where('brand', $brand);
            }
        }
        $res=$this->db->get()->row_array();
        if (isset($res['profit_id'])) {
            // Success - Profit data found
            $out['result']=$this->success_result;
            $newdata=0;
            $outinclude='<i class="fa fa-square-o" aria-hidden="true"></i>';
            $outclass='';
            if ($res['weekcheck']==0) {
                $newdata=1;
                $outinclude='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                $outclass='included';
            }
            $this->db->where('profit_id', $profit_id);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('brand', ['BT','SB']);
                } else {
                    $this->db->where('brand', $brand);
                }
            }
            $this->db->set('weekcheck', $newdata);
            $this->db->update('netprofit_dat');
            $out['weekcheck'] = $outinclude;
            $out['weekclass'] = $outclass;
            /* Get data about Netprofit  */
        }
        return $out;
    }

//    function _include_netprofit_debt($weekid, $type, $newdat) {
//        $datbgn=$this->config->item('netprofit_start');
//        $year=intval(substr($weekid,0,4));
//        if ($type=='week') {
//            $curweek=date('W');
//            $curyear=date('Y');
//            $dats=$this->func->getDatesByWeek($curweek,$curyear);
//            $datend=$dats['end_week'];
//
//            $week=intval(substr($weekid,5));
//            $this->db->where('profit_week',$week);
//            $this->db->where('profit_month',NULL);
//        } else {
//            $curmonth=date('m');
//            $curyear=date('Y');
//            $datend=strtotime($curmonth.'/01/'.$curyear.' 00:00:00');
//            $datend=strtotime(date("Y-m-d", ($datend)) . " +1 month");
//            $datend=$datend-1;
//            $month=intval(substr($weekid,5));
//            $this->db->where('profit_month',$month);
//            $this->db->where('profit_week',NULL);
//        }
//        $this->db->where('profit_year',$year);
//        $this->db->set('debtinclude',$newdat);
//        $this->db->update('netprofit');
//        /* Get data about Netprofit  */
//        $total_options=array(
//            'type'=>$type,
//            'start'=>$this->config->item('netprofit_start'),
//        );
//        $out=$this->get_netprofit_runs($total_options);
//        return $out;
//    }

    function get_netprofit($weekid, $type, $brand) {
        $datarr=explode("-", $weekid);
        if ($type=='week') {
            $week=$datarr[1];
            $year=$datarr[0];
        } else {
            $month=$datarr[1];
            $year=$datarr[0];
        }
        /* get data about from orders */
        $this->db->select('np.*, nd.datebgn, nd.dateend, nd.profit_year, nd.profit_week, nd.profit_month, netprofit_revenue(nd.datebgn, nd.dateend, \''.$brand.'\') as revenue,
            netprofit_profit(nd.datebgn, nd.dateend,\''.$brand.'\') as gross_profit,
            netprofit_cntsale(nd.datebgn, nd.dateend,\''.$brand.'\') as sales, netprofit_cntproj(nd.datebgn, nd.dateend,\''.$brand.'\') as cntproj',FALSE);
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('nd.profit_year',$year);
        if ($type=='week') {
            $this->db->where('nd.profit_week', intval($week));
            $this->db->where('nd.profit_month is NULL');
        } else {
            $this->db->where('nd.profit_month', intval($month));
            $this->db->where('nd.profit_week is NULL');
        }
        $this->db->where('np.brand', $brand);
        $res=$this->db->get()->row_array();

        $out=array();
        if (isset($res['profit_id'])) {
            /* Not Empty Array */
            if ($res['profit_operating']=='' && $res['profit_payroll']=='' && $res['profit_advertising']=='' && $res['profit_projects']=='' && $res['profit_purchases']=='') {
                $out_totalcost='Not Calc Yet';
                $out_netprofit='Not Calc Yet';
                $netprofit=0;
            } else {
                $totalcost=floatval($res['profit_operating'])+floatval($res['profit_payroll'])+floatval($res['profit_advertising'])+floatval($res['profit_projects'])+floatval($res['profit_purchases']);
                $out_totalcost=($totalcost==0 ? $this->EMPTY_PROFIT : '$'.number_format($totalcost,0,'.',','));
                $netprofit=floatval($res['gross_profit'])-$totalcost;
                $out_netprofit=($netprofit==0 ? $this->EMPTY_PROFIT : '$'.number_format($netprofit,0,'.',','));
            }

            if ($type=='week') {
                $debt=floatval($netprofit)-floatval($res['profit_owners'])-floatval($res['profit_saved'])-floatval($res['od2']);
                if ($debt<0) {
                    $out_debt='-$'.number_format(abs($debt),2,'.','');
                } else {
                    $out_debt=($debt==0 ? '' : '$'.number_format($debt,2,'.',''));
                }
                $weekname='';
                if (date('M',$res['datebgn'])!=date('M',$res['dateend'])) {
                    $weekname.=date('M',$res['datebgn']).'/'.date('M',$res['dateend']);
                } else {
                    $weekname.=date('M',$res['datebgn']);
                }
                $weekname.=' '.date('j',$res['datebgn']).'-'.date('j',$res['dateend']);
                $res['od'] = floatval($res['profit_owners'])+floatval($res['od2']);
                $out=array(
                    'profit_id'=>$res['profit_id'],
                    'profit_week'=>$week,
                    'profit_year'=>$year,
                    'profit_operating'=>$res['profit_operating'],
                    'profit_payroll'=>$res['profit_payroll'],
                    'profit_advertising'=>$res['profit_advertising'],
                    'profit_projects'=>$res['profit_projects'],
                    'profit_w9'=>$res['profit_w9'],
                    'profit_purchases'=>$res['profit_purchases'],
                    'out_debt'=>$out_debt,
                    'profit_saved'=>$res['profit_saved'],
                    'profit_owners'=>$res['profit_owners'],
                    'od2'=>$res['od2'],
                    'od' => $res['od'],
                    'out_revenue'=>($res['revenue']==0 ? '' : '$'.number_format($res['revenue'],0,'.',',')),
                    'out_profit'=>($res['gross_profit']==0 ? '' : '$'.number_format($res['gross_profit'],0,'.',',')),
                    'out_revenueprc'=>($res['revenue']==0 ? '&nbsp;' : round($res['gross_profit']/$res['revenue']*100,0).'%'),
                    'week'=>$weekname,
                    'profit_class'=>($res['cntproj']==0 ? '' : 'projprof'),
                    'sales'=>($res['sales']==0 ? '' : number_format($res['sales'],0,'.',',')),
                    'out_totalcost'=>$out_totalcost,
                    'out_netprofit'=>$out_netprofit,
                );
            } else {
                $res['od'] = floatval($res['profit_owners'])+floatval($res['od2']);
                $debt=floatval($netprofit)-floatval($res['profit_owners'])-floatval($res['profit_saved'])-floatval($res['od2']);
                if ($debt<0) {
                    $out_debt='-$'.number_format(abs($debt),2,'.','');
                } else {
                    $out_debt=($debt==0 ? '' : '$'.number_format($debt,2,'.',''));
                }
                $weekname=date('M, Y',$res['datebgn']);
                $out=array(
                    'profit_id'=>$res['profit_id'],
                    'profit_month'=>$month,
                    'profit_year'=>$year,
                    'profit_operating'=>$res['profit_operating'],
                    'profit_payroll'=>$res['profit_payroll'],
                    'profit_advertising'=>$res['profit_advertising'],
                    'profit_projects'=>$res['profit_projects'],
                    'profit_w9'=>$res['profit_w9'],
                    'profit_purchases'=>$res['profit_purchases'],
                    'profit_saved'=>$res['profit_saved'],
                    'od2'=>$res['od2'],
                    'out_debt'=>($debt==0 ? '' : '$'.number_format($debt,2,'.','')),
                    'profit_owners'=>$res['profit_owners'],
                    'out_revenue'=>($res['revenue']==0 ? '' : '$'.number_format($res['revenue'],0,'.',',')),
                    'out_profit'=>($res['gross_profit']==0 ? '' : '$'.number_format($res['gross_profit'],0,'.',',')),
                    'out_revenueprc'=>($res['revenue']==0 ? '&nbsp;' : round($res['gross_profit']/$res['revenue']*100,0).'%'),
                    'week'=>$weekname,
                    'profit_class'=>($res['cntproj']==0 ? '' : 'projprof'),
                    'sales'=>($res['sales']==0 ? '' : number_format($res['sales'],0,'.',',')),
                    'out_totalcost'=>$out_totalcost,
                    'out_netprofit'=>$out_netprofit,
                    'od' => $res['od'],
                );
            }
            if ($res['debtinclude']==0) {
                $out['debt_include']='<input type="checkbox" value="1" class="net_debincl" name="debtinclude" />';
            } else {
                $out['debt_include']='<input type="checkbox" value="1" class="net_debincl" name="debtinclude" checked="checked"/>';
            }
            $out['debtinclude']=$res['debtinclude'];
            $out['debtval']=$debt;
            $out['datebgn']=$res['datebgn'];
            $out['dateend']=$res['dateend'];
        }
        return $out;
    }

    public function get_netprofit_dataedit($profit_id, $brand) {
        $out=array('result'=>$this->error_result, 'msg'=>'Data Not Found');
        $this->db->select('np.*, nd.datebgn, nd.dateend, nd.profit_month, nd.profit_week, nd.profit_year,  netprofit_revenue(nd.datebgn, nd.dateend,\''.$brand.'\') as revenue,
            netprofit_profit(nd.datebgn, nd.dateend,\''.$brand.'\') as gross_profit,
            netprofit_cntsale(nd.datebgn, nd.dateend,\''.$brand.'\') as sales, netprofit_cntproj(nd.datebgn, nd.dateend,\''.$brand.'\') as cntproj',FALSE);
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('np.profit_id',$profit_id);
        $this->db->where('np.brand', $brand);
        $res=$this->db->get()->row_array();
        if (isset($res['profit_id'])) {
            $out['result']=$this->success_result;
            $type='week';
            if (empty($res['profit_week'])) {
                $type='month';
            }
            if ($res['profit_operating']=='' && $res['profit_payroll']=='' && $res['profit_advertising']=='' && $res['profit_projects']=='' && $res['profit_purchases']=='') {
                $out_totalcost='Not Calc Yet';
                $out_netprofit='Not Calc Yet';
                $netprofit=0;
            } else {
                $totalcost=floatval($res['profit_operating'])+floatval($res['profit_payroll'])+floatval($res['profit_advertising'])+floatval($res['profit_projects'])+floatval($res['profit_purchases']);
                $out_totalcost=($totalcost==0 ? $this->EMPTY_PROFIT : '$'.number_format($totalcost,0,'.',','));
                $netprofit=floatval($res['gross_profit'])-$totalcost;
                $out_netprofit=($netprofit==0 ? $this->EMPTY_PROFIT : '$'.number_format($netprofit,0,'.',','));
            }
            $out_w9=$this->EMPTY_PROFIT;
            if (abs($res['profit_w9'])>0) {
                if ($res['profit_w9']>0) {
                    $out_w9='('.MoneyOutput($res['profit_w9'],2).')';
                } else {
                    $out_w9=MoneyOutput(abs($res['profit_w9']),2);
                }
            }
            $out_purchase=$this->EMPTY_PROFIT;
            if (abs($res['profit_purchases'])>0) {
                if ($res['profit_purchases']>0) {
                    $out_purchase='('.MoneyOutput($res['profit_purchases'],2).')';
                } else {
                    $out_purchase=MoneyOutput(abs($res['profit_purchases']),2);
                }
            }
            if ($type=='week') {
                $debt=floatval($netprofit)-floatval($res['profit_owners'])-floatval($res['profit_saved'])-floatval($res['od2']);
                if ($debt<0) {
                    $out_debt=MoneyOutput(abs($debt),2);
                } else {
                    $out_debt=($debt==0 ? '' : MoneyOutput($debt,2));
                }
                $weekname='';
                if (date('M',$res['datebgn'])!=date('M',$res['dateend'])) {
                    $weekname.=date('M',$res['datebgn']).'/'.date('M',$res['dateend']);
                } else {
                    $weekname.=date('M',$res['datebgn']);
                }
                $weekname.=' '.date('j',$res['datebgn']).'-'.date('j',$res['dateend']);
                $res['od']=floatval($res['profit_owners'])+floatval($res['od2']);
                $data=array(
                    'profit_id'=>$res['profit_id'],
                    'profit_week'=>$res['profit_week'],
                    'profit_year'=>$res['profit_year'],
                    'profit_operating'=>$res['profit_operating'],
                    'profit_payroll'=>$res['profit_payroll'],
                    'profit_advertising'=>$res['profit_advertising'],
                    'profit_projects'=>$res['profit_projects'],
                    'profit_w9'=>$res['profit_w9'],
                    'profit_purchases'=>$res['profit_purchases'],
                    'profit_owners'=>$res['profit_owners'],
                    'od2'=>$res['od2'],
                    'out_debt'=>$out_debt,
                    'profit_saved'=>$res['profit_saved'],
                    'od' => $res['od'],
                    'out_revenue'=>($res['revenue']==0 ? '' : MoneyOutput($res['revenue'],0)),
                    'out_profit'=>($res['gross_profit']==0 ? '' : MoneyOutput($res['gross_profit'],0)),
                    'out_revenueprc'=>($res['revenue']==0 ? '&nbsp;' : round($res['gross_profit']/$res['revenue']*100,0).'%'),
                    'week'=>$weekname,
                    'profit_class'=>($res['cntproj']==0 ? '' : 'projprof'),
                    'sales'=>($res['sales']==0 ? '' : number_format($res['sales'],0,'.',',')),
                    'out_totalcost'=>$out_totalcost,
                    'out_netprofit'=>$out_netprofit,
                );
            } else {
                $res['od'] = floatval($res['profit_owners'])+floatval($res['od2']);
                $debt=floatval($netprofit)-floatval($res['profit_owners'])-floatval($res['profit_saved'])-floatval($res['od2']);
                if ($debt<0) {
                    $out_debt='-$'.number_format(abs($debt),2,'.','');
                } else {
                    $out_debt=($debt==0 ? '' : '$'.number_format($debt,2,'.',''));
                }
                $weekname=date('M, Y',$res['datebgn']);
                $data=array(
                    'profit_id'=>$res['profit_id'],
                    'profit_month'=>$res['profit_month'],
                    'profit_year'=>$res['profit_year'],
                    'profit_operating'=>$res['profit_operating'],
                    'profit_payroll'=>$res['profit_payroll'],
                    'profit_advertising'=>$res['profit_advertising'],
                    'profit_projects'=>$res['profit_projects'],
                    'profit_w9'=>$res['profit_w9'],
                    'profit_purchases'=>$res['profit_purchases'],
                    'profit_saved'=>$res['profit_saved'],
                    'od2'=>$res['od2'],
                    'out_debt'=>($debt==0 ? '' : '$'.number_format($debt,2,'.','')),
                    'profit_owners'=>$res['profit_owners'],
                    'out_revenue'=>($res['revenue']==0 ? '' : '$'.number_format($res['revenue'],0,'.',',')),
                    'out_profit'=>($res['gross_profit']==0 ? '' : '$'.number_format($res['gross_profit'],0,'.',',')),
                    'out_revenueprc'=>($res['revenue']==0 ? '&nbsp;' : round($res['gross_profit']/$res['revenue']*100,0).'%'),
                    'week'=>$weekname,
                    'profit_class'=>($res['cntproj']==0 ? '' : 'projprof'),
                    'sales'=>($res['sales']==0 ? '' : number_format($res['sales'],0,'.',',')),
                    'out_totalcost'=>$out_totalcost,
                    'out_netprofit'=>$out_netprofit,
                    'od' => $res['od'],
                );
            }
            if ($res['debtinclude']==0) {
                $data['debt_include']='<i class="fa fa-square-o" aria-hidden="true"></i>';
            } else {
                $data['debt_include']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
            }
            $data['debtinclude']=$res['debtinclude'];
            $data['debtval']=$debt;
            $data['datebgn']=$res['datebgn'];
            $data['dateend']=$res['dateend'];
            $data['out_w9']=$out_w9;
            $data['out_purchases']=$out_purchase;
            $data['weeknote']=$res['weeknote'];
            $out['data']=$data;
            $out['weekid']=$res['profit_year'].'-';
            if ($type=='week') {
                $out['weekid'].=str_pad($res['profit_week'], 2,'0',STR_PAD_LEFT);
            } else {
                $out['weekid'].=str_pad($res['profit_month'], 2,'0',STR_PAD_LEFT);
            }
            $out['type']=$type;
            // Get Purchase Details
            $this->db->select('c.category_name, d.*');
            $this->db->from('ts_netprofit_details d');
            $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id','left');
            $this->db->where('profit_id', $profit_id);
            $this->db->where('details_type','Purchase');
            $purch=$this->db->get()->result_array();
            $out['purchase_details']=$purch;
            // Get W9WORK
            // Get Purchase Details
            $this->db->select('c.category_name, d.*');
            $this->db->from('ts_netprofit_details d');
            $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id','left');
            $this->db->where('d.profit_id', $profit_id);
            $this->db->where('d.details_type','W9');
            $details=$this->db->get()->result_array();
            $out['w9work_details']=$details;
        }
        return $out;
    }

    public function netprofit_details_edit($netprofitdata, $data, $session_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
        $netprofit=$netprofitdata['netprofit'];
        $fld=$data['fldname'];
        $newval=$data['newval'];
        if (array_key_exists($fld, $netprofit)) {
            $netprofit[$fld]=$newval;
            $netprofitdata['netprofit']=$netprofit;
            usersession($session_id, $netprofitdata);
            $out['result']=$this->success_result;
        }
        return $out;
    }

//    public function netprofit_details_debtincl($netprofitdata, $session_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
//        $netprofit=$netprofitdata['netprofit'];
//        $debincl=0;
//        if ($netprofit['debtinclude']==0) {
//            $debincl=1;
//        }
//        $netprofit['debtinclude']=$debincl;
//        if ($debincl==0) {
//            $out['content']='<i class="fa fa-square-o" aria-hidden="true"></i>';
//        } else {
//            $out['content']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
//        }
//        $netprofitdata['netprofit']=$netprofit;
//        $this->func->session($session_id, $netprofitdata);
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//    function save_netprof($options) {
//        $profit_id=$options['profit_id'];
//        if ($profit_id==0) {
//            $this->db->select('profit_id');
//            $this->db->from('netprofit');
//            if ($options['type']=='week') {
//                $this->db->where('profit_week',$options['profit_week']);
//                $this->db->where('profit_month is NULL');
//            } else {
//                $this->db->where('profit_month',$options['profit_month']);
//                $this->db->where('profit_week is NULL');
//            }
//            $this->db->where('profit_year',$options['profit_year']);
//            $res=$this->db->get()->row_array();
//            if (isset($res['profit_id']) && $res['profit_id']) {
//                $profit_id=$res['profit_id'];
//            }
//        }
//        if ($options['type']=='week' && $options['debtinclude']==1) {
//            $week_id=$options['profit_year'].'-'.$options['profit_week'];
//            $olddat=$this->get_netprofit($week_id,'week');
//            $total_options=array(
//                'type'=>'week',
//                'start'=>$this->config->item('netprofit_start'),
//            );
//            $rundat=$this->get_netprofit_runs($total_options);
//            $oldrundebt=$rundat['out_debtval'];
//        }
//        $this->db->set('profit_operating',($options['profit_operating']==0 ? NULL : $options['profit_operating']));
//        $this->db->set('profit_payroll',($options['profit_payroll']==0 ? NULL : $options['profit_payroll']));
//        $this->db->set('profit_advertising',($options['profit_advertising']==0 ? NULL : $options['profit_advertising']));
//        $this->db->set('profit_projects',($options['profit_projects']==0 ? NULL : $options['profit_projects']));
//        $this->db->set('profit_purchases',($options['profit_purchases']==0 ? NULL : $options['profit_purchases']));
//        $this->db->set('profit_debt',($options['profit_debt']==0 ? NULL : $options['profit_debt']));
//        $this->db->set('profit_owners',($options['profit_owners']==0 ? NULL : $options['profit_owners']));
//        $this->db->set('od2',($options['od2']==0 ? NULL : $options['od2']));
//        $this->db->set('profit_saved',($options['profit_saved']==0 ? NULL : $options['profit_saved']));
//        $this->db->set('debtinclude',$options['debtinclude']);
//        if ($profit_id==0) {
//            if ($options['type']=='week') {
//                $this->db->set('profit_week',$options['profit_week']);
//            } else {
//                $this->db->set('profit_month',$options['profit_month']);
//            }
//            $this->db->set('profit_year',$options['profit_year']);
//            $this->db->set('datebgn',$options['datebgn']);
//            $this->db->set('dateend',$options['dateend']);
//            $this->db->set('create_user',$options['user_id']);
//            $this->db->set('create_date',date('Y-m-d H:i:s'));
//            $this->db->set('update_user',$options['user_id']);
//            $this->db->insert('netprofit');
//            $result=$this->db->insert_id();
//        } else {
//            $this->db->set('update_user',$options['user_id']);
//            $this->db->where('profit_id',$profit_id);
//            $this->db->update('netprofit');
//            $result=1;
//        }
//        if ($options['type']=='week' && $options['debtinclude']==1) {
//            $newdat=$this->get_netprofit($week_id,'week');
//            if ($newdat['profit_saved']!=$olddat['profit_saved'] || $newdat['profit_owners']!=$olddat['profit_owners'] || $newdat['od2']!=$olddat['od2']) {
//                $total_options=array(
//                    'type'=>'week',
//                    'start'=>$this->config->item('netprofit_start'),
//                );
//                $rundat=$this->get_netprofit_runs($total_options);
//                $newrundebt=$rundat['out_debtval'];
//                if ($newrundebt<0) {
//                    $outnewrundebt='($'.number_format(abs(newrundebt),0,'.',',');
//                } else {
//                    $outnewrundebt='$'.number_format($newrundebt,0,'.',',');
//                }
//                if ($oldrundebt<0) {
//                    $outoldrundebt='$('.number_format(abs($oldrundebt),'0','.',',').')';
//                } else {
//                    $outoldrundebt='$'.number_format($oldrundebt,'0','.',',');
//                }
//
//                $this->load->model('order_model');
//                $weeknum=$olddat['week'];
//                if (date('Y',$newdat['dateend'])==date('Y',$newdat['datebgn'])) {
//                    $weeknum.=', '.date('Y',$newdat['datebgn']);
//                } else {
//                    $weeknum.=', '.date('Y',$newdat['datebgn']).'/'.date('y',$newdat['dateend']);
//                }
//                $noteoptions=array(
//                    'netproofdebt'=>1,
//                    'user_id'=>$options['user_id'],
//                    'olddebt'=>$olddat['out_debt'],
//                    'newdebt'=>$newdat['out_debt'],
//                    'weeknum'=>$weeknum,
//                    'newtotalrun'=>$outnewrundebt,
//                    'oldtotalrun'=>$outoldrundebt,
//                );
//                //
//                if ($newdat['profit_saved']!=$olddat['profit_saved']) {
//                    $noteoptions['profit_saved']=array(
//                        'old'=>$olddat['profit_saved'],
//                        'new'=>$newdat['profit_saved'],
//                    );
//                }
//                if ($newdat['profit_owners']!=$olddat['profit_owners']) {
//                    $noteoptions['profit_owners']=array(
//                        'old'=>$olddat['profit_owners'],
//                        'new'=>$newdat['profit_owners'],
//                    );
//                }
//                if ($newdat['od2']!=$olddat['od2']) {
//                    $noteoptions['od2']=array(
//                        'old'=>$olddat['od2'],
//                        'new'=>$newdat['od2'],
//                    );
//                }
//                $this->order_model->notify_netdebtchanged($noteoptions);
//            }
//        }
//        return $result;
//    }
//    /* Balances */
//    function get_balance_cols($coltype) {
//        $this->db->select('*');
//        $this->db->from('balance_columns');
//        $this->db->where('column_type',$coltype);
//        $this->db->order_by('is_calc desc, balance_column_id');
//        $cols=$this->db->get()->result_array();
//        return $cols;
//    }
//
//    function get_balances() {
//        /* Select positive cols */
//        $this->db->select('*');
//        $this->db->from('balance_columns');
//        $this->db->where('column_type','POS');
//        $this->db->order_by('balance_column_id');
//        $posit_cols=$this->db->get()->result_array();
//        $possrearch=array();
//        $npos=4;
//        foreach ($posit_cols as $row) {
//            array_push($possrearch, $row['balance_column_id']);
//            $npos++;
//        }
//
//        /* Select Negat cols */
//        $this->db->select('*');
//        $this->db->from('balance_columns');
//        $this->db->where('column_type','NEG');
//        $this->db->order_by('balance_column_id');
//        $negat_cols=$this->db->get()->result_array();
//        $negatsearch=array();
//        foreach ($negat_cols as $row) {
//            array_push($negatsearch, $row['balance_column_id']);
//        }
//
//        $this->db->select('count(balance_id) as cnt');
//        $this->db->from('balances');
//        $res=$this->db->get()->row_array();
//        $cnt=$res['cnt'];
//        if ($cnt==0) {
//            $out[]=array(
//                'out_date'=>'',
//                'out_balance'=>'',
//                'out_positives'=>'',
//                'out_negatives'=>'',
//            );
//            foreach ($posit_cols as $pcol) {
//                $out[0]['pos'.$pcol['balance_column_id']]='';
//            }
//            foreach ($negat_cols as $pcol) {
//                $out[0]['neg'.$pcol['balance_column_id']]='';
//            }
//        } else {
//            $this->db->select('b.balance_id, b.balance_date, b.balance_positive, b.balance_negative, b.balance_sum',FALSE);
//            foreach ($posit_cols as $pcol) {
//                $this->db->select("q{$pcol['balance_column_id']}.pos{$pcol['balance_column_id']}",FALSE);
//                $this->db->join("(select balance_id, line_sum as pos{$pcol['balance_column_id']} from balances_sums where balance_column_id={$pcol['balance_column_id']}) q{$pcol['balance_column_id']}","q{$pcol['balance_column_id']}.balance_id=b.balance_id",'left');
//            }
//            foreach ($negat_cols as $pcol) {
//                $this->db->select("q{$pcol['balance_column_id']}.neg{$pcol['balance_column_id']}",FALSE);
//                $this->db->join("(select balance_id, line_sum as neg{$pcol['balance_column_id']} from balances_sums where balance_column_id={$pcol['balance_column_id']}) q{$pcol['balance_column_id']}","q{$pcol['balance_column_id']}.balance_id=b.balance_id",'left');
//            }
//            $this->db->from('balances b');
//            $this->db->order_by('b.balance_date','desc');
//            $results=$this->db->get()->result_array();
//
//            $out=array();
//            foreach ($results as $row) {
//                $row['out_date']=date('D m/d/y',$row['balance_date']);
//                $positive=floatval($row['balance_positive']);
//                $out_positive=($positive==0 ? '' : '$'.number_format(abs($positive),0,'.',','));
//                if ($positive<0) {
//                    $out_positive='<span style="color:#db1d1d;">('.$out_positive.')</span>';
//                }
//                $row['out_positives']=$out_positive;
//                $negative=floatval($row['balance_negative']);
//                $out_negatives=($negative==0 ? '' : '$'.number_format(abs($negative),0,'.',','));
//                if ($negative>0) {
//                    $out_negatives='<span style="color:#db1d1d;">('.$out_negatives.')<span>';
//                }
//                $row['out_negatives']=$out_negatives;
//                $balance=floatval($row['balance_sum']);
//                if ($balance<0) {
//                    $row['balance_class']='color_red2';
//                } else {
//                    $row['balance_class']='color_green';
//                }
//                $out_balance=($balance==0 ? '' : '$'.number_format(abs($balance),0,'.',','));
//                if ($balance<0) {
//                    $row['out_balance']='<span style="color:#db1d1d;font-weight:bold;">('.$out_balance.')</span>';
//                } elseif($balance>0) {
//                    $row['out_balance']='<span style="color:#1ea527;font-weight:bold;">'.$out_balance.'</span>';
//                } else {
//                    $row['out_balance']='';
//                }
//                $out[]=array(
//                    'balance_id'=>$row['balance_id'],
//                    'out_date'=>$row['out_date'],
//                    'out_balance'=>$row['out_balance'],
//                    'out_positives'=>$row['out_positives'],
//                    'out_negatives'=>$row['out_negatives'],
//                );
//                $keyout=count($out);
//                $keyout=$keyout-1;
//                foreach ($posit_cols as $pcol) {
//                    $linsum=floatval($row['pos'.$pcol['balance_column_id']]);
//                    $out_linsum=($linsum==0 ? '&nbsp;' : '$'.number_format(abs($linsum),0,'.',','));
//                    if ($linsum<0) {
//                        $out_linsum='<span style="color:#db1d1d;font-weight:bold;">('.$out_linsum.')<span style="color:#db1d1d;font-weight:bold;">';
//                    }
//                    $out[$keyout]['pos'.$pcol['balance_column_id']]=$out_linsum;
//                }
//                foreach ($negat_cols as $pcol) {
//                    $linsum=floatval($row['neg'.$pcol['balance_column_id']]);
//                    $out_linsum=($linsum==0 ? '' : '$'.number_format(abs($linsum),0,'.',','));
//                    if ($linsum>0) {
//                        $out_linsum='<span style="color:#db1d1d;font-weight:bold;">('.$out_linsum.')<span style="color:#db1d1d;font-weight:bold;">';
//                    }
//                    $out[$keyout]['neg'.$pcol['balance_column_id']]=$out_linsum;
//                }
//            }
//
//        }
//        return $out;
//    }
//
//    function get_new_balance() {
//        $datbgn=$this->config->item('balance_start');
//
//        $this->db->select('sum(revenue*(1-is_invoiced)) as sum_invoice, sum((revenue-coalesce(paid_sum,0))*is_invoiced*(1-is_paid)) as sum_paid',FALSE);
//        $this->db->from('ts_orders');
//        $this->db->where('order_date >= ',$datbgn);
//        $this->db->where("is_canceled",0);
//        $ordsum=$this->db->get()->row_array();
//        $balance=array(
//            'not_invoiced'=>floatval($ordsum['sum_invoice']),
//            'terms'=>floatval($ordsum['sum_paid']),
//            'balance_date'=>time(),
//        );
//        /* Unbilled & unplaced */
//        $this->db->select('sum(oa.amount_sum) as sum_bill , sum(op.charge_sum) as sum_pay',FALSE);
//        $this->db->from('ts_order_amounts oa');
//        $this->db->join('ts_order_charges op','op.amount_id=oa.amount_id','left');
//        $this->db->join('ts_orders o','o.order_id=oa.order_id');
//        $this->db->where('o.is_canceled',0);
//        $this->db->where('o.is_closed',0);
//        $result=$this->db->get()->row_array();
//
//        $sum_bill=floatval($result['sum_bill']);
//        $sum_pay=floatval($result['sum_pay']);
//
//        /* Select sum unnamed */
//        $this->db->select('sum(revenue-profit) as sum_pay');
//        $this->db->from('ts_orders');
//        $this->db->where('order_cog is null');
//        $this->db->where('order_date >= '.$datbgn);
//        $this->db->where("is_canceled",0);
//        $resunb=$this->db->get()->row_array();
//        $balance['unplaced']=floatval($resunb['sum_pay']);
//        $balance['unbilled']=($sum_bill-$sum_pay);
//        /* Calculate Total unbilled */
//
//        /* Prepare fields for output */
//        $balance['positives']=$balance['not_invoiced']+$balance['terms'];
//        $balance['out_positives']=($balance['positives']==0 ? '' : '$'.number_format($balance['positives'],2,'.',','));
//        $balance['negatives']=$balance['unplaced']+$balance['unbilled'];
//        $balance['out_negatives']=($balance['negatives']==0 ? '' : '<span style="color:#db1d1d;font-weight:bold;">($'.number_format($balance['negatives'],2,'.',',').')<span style="color:#db1d1d;font-weight:bold;">');
//        $balance['balance']=$balance['positives']-$balance['negatives'];
//        $balance['out_balance']=($balance['balance']==0 ? '' : '$'.number_format(abs($balance['balance']),2,'.',','));
//
//        if ($balance['balance']<0) {
//            $balance['balance_class']='balance_negative';
//            $balance['out_balance']='('.$balance['out_balance'].')';
//        } else {
//            $balance['balance_class']='balance_positive';
//        }
//
//        return $balance;
//    }
//
//    function save_balance($details) {
//        $res=array('result'=>0,'msg'=>'Unknown error');
//        $negatives=$this->get_balance_cols('NEG');
//        /* search */
//        $aneg=array();
//        foreach ($negatives as $prow) {
//            array_push($aneg, 'neg'.$prow['balance_column_id']);
//        }
//        $positives=$this->get_balance_cols('POS');
//        $apos=array();
//        foreach ($positives as $prow) {
//            array_push($apos, 'pos'.$prow['balance_column_id']);
//        }
//        $possum=0;$negsum=0;
//        $balanssum=array();
//        foreach ($details as $key=>$value) {
//            if (substr($key,0,3)=='pos') {
//                $colkey=array_search($key, $apos);
//                if ($positives[$colkey]['is_calc']==1) {
//                    $possum+=floatval($value);
//                }
//                $balanssum[]=array(
//                    'balance_column_id'=>substr($key,3),
//                    'line_sum'=>floatval($value),
//                );
//            } elseif (substr($key, 0, 3)=='neg') {
//                $colkey=array_search($key, $aneg);
//                if ($negatives[$colkey]['is_calc']==1) {
//                    $negsum+=floatval($value);
//                }
//                $balanssum[]=array(
//                    'balance_column_id'=>substr($key,3),
//                    'line_sum'=>floatval($value),
//                );
//            }
//        }
//        $balance=$possum-$negsum;
//        $this->db->set('balance_sum',$balance);
//        $this->db->set('balance_positive',$possum);
//        $this->db->set('balance_negative',$negsum);
//        if ($details['balance_id']==0) {
//            $this->db->set('balance_date',$details['balance_date']);
//            $this->db->insert('balances');
//            $balance_id=$this->db->insert_id();
//        } else {
//            $balance_id=$details['balance_id'];
//            $this->db->where('balance_id',$balance_id);
//            $this->db->update('balances');
//        }
//        if ($balance_id==0) {
//            $res['msg']='Balance data not insert';
//        } else {
//            $this->db->where('balance_id',$balance_id);
//            $this->db->delete('balances_sums');
//            foreach ($balanssum as $prow) {
//                $this->db->set('balance_id',$balance_id);
//                $this->db->set('balance_column_id',$prow['balance_column_id']);
//                $this->db->set('line_sum',$prow['line_sum']);
//                $this->db->insert('balances_sums');
//            }
//            $res['result']=1;
//            $res['msg']='';
//        }
//        return $res;
//    }
//
//    function get_balance_data($balance_id) {
//        $out=array();
//        $this->db->select('balance_id, balance_date, balance_positive, balance_negative, balance_sum as balance');
//        $this->db->from('balances');
//        $this->db->where('balance_id',$balance_id);
//
//
//        $balance=$this->db->get()->row_array();
//
//        $balance['out_balance']=(floatval($balance['balance'])==0 ? '' : '$'.number_format(abs($balance['balance']),2,'.',','));
//        if ($balance['balance']<0) {
//            $balance['balance_class']='balance_negative';
//            $balance['out_balance']='('.$balance['out_balance'].')';
//        } else {
//            $balance['balance_class']='balance_positive';
//        }
//        $positive=floatval($balance['balance_positive']);
//        $out_positive=($positive==0 ? '' : '$'.number_format(abs($positive),2,'.',','));
//        if ($positive<0) {
//            $out_positive='<span style="color:#db1d1d;font-weight:bold;">('.$out_positive.')<span style="color:#db1d1d;font-weight:bold;">';
//        }
//        $balance['out_positives']=$out_positive;
//        $negative=floatval($balance['balance_negative']);
//        $out_negative=($negative==0 ? '' : '$'.number_format(abs($negative),2,'.',','));
//        if ($negative>0) {
//            $out_negative='<span style="color:#db1d1d;font-weight:bold;">('.$out_negative.')<span style="color:#db1d1d;font-weight:bold;">';
//        }
//        $balance['out_negatives']=$out_negative;
//        $out['balance']=$balance;
//        /* Get data about columns */
//        $this->db->select("bc.balance_column_id, bc.column_type, bc.column_title, bc.is_calc, bc.is_edit,bs.line_sum");
//        $this->db->from("balance_columns bc");
//        $this->db->join("(select balance_column_id, line_sum from balances_sums where balance_id=".$balance_id.") bs","bs.balance_column_id=bc.balance_column_id","left");
//        $this->db->where("bc.column_type","POS");
//        $positives=$this->db->get()->result_array();
//
//        $poscols=array();
//        foreach ($positives as $prow) {
//            $fldval=floatval($prow['line_sum']);
//            $outval=($fldval==0 ? '' : '$'.number_format(abs($fldval),2,'.','.'));
//            if ($fldval<0) {
//                $outval='<span style="color:#db1d1d;font-weight:bold;">('.$outval.')<span style="color:#db1d1d;font-weight:bold;">';
//            }
//            $poscols[]=array(
//                'key' =>'pos'.$prow['balance_column_id'],
//                'title'=>$prow['column_title'],
//                'is_edit' =>$prow['is_edit'],
//                'class' =>($prow['is_calc']==1 ? 'balance_editsum' : 'balance_viewsum'),
//                'input_val'=>floatval($prow['line_sum']),
//                'output_val' =>$outval,
//            );
//        }
//        $out['poscols']=$poscols;
//        $this->db->select("bc.balance_column_id, bc.column_type, bc.column_title, bc.is_calc, bc.is_edit,bs.line_sum");
//        $this->db->from("balance_columns bc");
//        $this->db->join("(select balance_column_id, line_sum from balances_sums where balance_id=".$balance_id.") bs","bs.balance_column_id=bc.balance_column_id","left");
//        $this->db->where("bc.column_type","NEG");
//        $negatives=$this->db->get()->result_array();
//
//        $negcols=array();
//        foreach ($negatives as $prow) {
//            $fldval=floatval($prow['line_sum']);
//            $outval=($fldval==0 ? '' : '$'.number_format(abs($fldval),2,'.','.'));
//            if ($fldval>0) {
//                $outval='<span style="color:#db1d1d;font-weight:bold;">('.$outval.')<span style="color:#db1d1d;font-weight:bold;">';
//            }
//            $negcols[]=array(
//                'key' =>'neg'.$prow['balance_column_id'],
//                'title'=>$prow['column_title'],
//                'is_edit' =>$prow['is_edit'],
//                'class' =>($prow['is_calc']==1 ? 'balance_editsum' : 'balance_viewsum'),
//                'input_val'=>floatval($prow['line_sum']),
//                'output_val' =>$outval,
//            );
//        }
//        $out['negcols']=$negcols;
//        return $out;
//    }
//
//    function add_column($column_title, $column_type) {
//        $res=array('result'=>0,'msg'=>'Unknown error');
//        if ($column_title=='') {
//            $res['msg']='Enter column title';
//        } elseif($column_type=='') {
//            $res['msg']='Select column type';
//        } else {
//            $this->db->select('count(balance_column_id) as cnt');
//            $this->db->from('balance_columns');
//            $this->db->where('upper(column_title) ',  strtoupper($column_title));
//            $result=$this->db->get()->row_array();
//            if ($result['cnt']!=0) {
//                $res['msg']='Non unique column name';
//            } else {
//                $this->db->set('column_title',$column_title);
//                $this->db->set('column_type',$column_type);
//                $this->db->insert('balance_columns');
//                $result=$this->db->insert_id();
//                if ($result!=0) {
//                    $res['result']=1;
//                    $res['msg']='';
//                } else {
//                    $res['msg']='Data not added';
//                }
//            }
//        }
//        return $res;
//    }

    function get_week_note($week,$year, $brand, $type) {
        $this->db->select('np.*');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('nd.profit_year',$year);
        if ($type=='week') {
            $this->db->where('nd.profit_week',$week);
            $this->db->where('nd.profit_month is NULL');
        } else {
            $this->db->where('nd.profit_month',$week);
            $this->db->where('nd.profit_week is NULL');
        }
        $this->db->where('np.brand', $brand);
        $res=$this->db->get()->row_array();
        return $res;
    }

    function save_week_note($profit_id, $brand, $weeknote) {
        $out=array('result'=>0, 'msg'=>'Unknown error');
        /* Check that profit exist */
        $this->db->select('*');
        $this->db->from('netprofit_dat');
        $this->db->where('profit_id',$profit_id);
        $this->db->where('brand', $brand);
        $res=$this->db->get()->row_array();
        if (!isset($res['profit_id'])) {
            $out['msg']='Profit data not exist';
        } else {
            $this->db->set('weeknote',$weeknote);
            $this->db->where('profit_id',$profit_id);
            $this->db->where('brand', $brand);
            $this->db->update('netprofit_dat');
            $out['result']=1;
            $out['msg']='';
        }
        return $out;
    }

    public function get_netprofit_details($profit_id) {
        $this->db->select('*');
        $this->db->from('netprofit');
        $this->db->where('profit_id',$profit_id);
        $res=$this->db->get()->row_array();
        if (isset($res['profi_id'])) {
            if ($res['profit_week']=='') {
                $res['profit_type']='Month';
            } else {
                $res['profit_type']='Week';
            }
        }
        return $res;
    }

    public function get_weeklist($start=0) {
        if ($start==0) {
            $start=$this->config->item('netprofit_start');
        }
        $this->db->select('nb.profit_id, nb.datebgn, nb.dateend');
        $this->db->from('netprofit nb');
        $this->db->where('nb.profit_week is not null');
        $this->db->where('nb.datebgn >= ', $start);
        $this->db->order_by('nb.datebgn desc');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            $weekname = '';
            if (date('M', $row['datebgn']) != date('M', $row['dateend'])) {
                $weekname.=date('M', $row['datebgn']) . '/' . date('M', $row['dateend']);
            } else {
                $weekname.=date('M', $row['datebgn']);
            }
            $weekname.=' ' . date('j', $row['datebgn']) . '-' . date('j', $row['dateend']);
            if (date('Y', $row['datebgn']) != date('Y', $row['dateend'])) {
                $weekname.=', '.date('y', $row['datebgn']).'/'.date('y', $row['dateend']);
            } else {
                $weekname.=', '.date('Y', $row['datebgn']);
            }
            $out[]=array(
                'id'=>$row['profit_id'],
                'label'=>$weekname,
            );
        }
        return $out;
    }

    public function getweekdetail($week_id, $param='start') {
        $out=array('result'=>0,'msg'=>'Week Not Exist');
        $this->db->select('nb.profit_id, nb.datebgn, nb.dateend');
        $this->db->from('netprofit nb');
        $this->db->where('nb.profit_id', $week_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['profit_id'])) {
            return $out;
        }
        $out['result']=1;
        if ($param=='start') {
            $out['date']=$res['datebgn'];
        } else {
            $out['date']=$res['dateend'];
        }
        return $out;
    }

//    public function getlastweekdetail() {
//        $this->db->select('nb.profit_id, nb.datebgn, nb.dateend');
//        $this->db->from('netprofit nb');
//        $this->db->where('nb.profit_month is null');
//        $this->db->where('nb.dateend < ', time());
//        $this->db->order_by('nb.datebgn desc');
//        $res=$this->db->get()->row_array();
//        return $res['dateend'];
//    }

    public function get_netprofit_totalsbyweekdata($options) {
        // Select max and min Year
        $compareweek=$options['compareweek'];
        $paceincome=$options['paceincome'];
        $paceexpense=$options['paceexpense'];
        if ($compareweek==1) {
            $weekbgn=intval($options['weekbgn']);
            $weekend=intval($options['weekend']);
        }
        // $now = strtotime(date('Y-m-d')); // or your date as well
        // Get an end of full week
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        // Get current week number
        $this->db->select('profit_week');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not NULL');
        $this->db->where('dateend < ',$now);
        $this->db->order_by('datebgn','desc');
        $weekres=$this->db->get()->row_array();
        if ($weekres['profit_week']>52) {
            $weekres['profit_week']=52;
        }
        $paceweekkf=52/$weekres['profit_week'];
        $current_weeknum=$weekres['profit_week'];
        $this->db->select('max(profit_year) end_year, min(profit_year) as start_year, min(datebgn) as start_date');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('profit_year >=', $this->start_netprofitdatashow);
        $this->db->where('dateend < ',$now);
        $yres=$this->db->get()->row_array();
        $start_year=$yres['start_year'];
        $end_year=$yres['end_year'];
        $start_date=$yres['start_date'];
        // Period begin in case of paceincome=2 - prev year as pace
        // if ($paceincome==2) {
        $prev_year=$end_year-1;
        if ($current_weeknum==52) {
            $prev_weeknum=1;
            $prev_year=$prev_year+1;
        } else {
            $prev_weeknum=$current_weeknum+1;
        }
        // Select date
        $this->db->select('profit_id,datebgn');
        $this->db->from('netprofit');
        $this->db->where('profit_week', $prev_weeknum);
        $this->db->where('profit_year', $prev_year);
        $paceres=$this->db->get()->row_array();
        if (isset($paceres['profit_id'])) {
            $pacedatstart=$paceres['datebgn'];
        } else {
            // Exclude
            $pacedatstart=strtotime(date("Y-m-d", $now) . " -1year -7days");
        }
        // }


        // Get Elapsed Days
        // $this->load->model('reports_model');
        // $days=$this->reports_model->get_bisness_dates();

        // Build empty array
        $this->db->select('date_format(from_unixtime(o.order_date),\'%x\') as orddat, count(o.order_id) as cnt');
        $this->db->select('sum(o.revenue) as revenue, sum(o.profit) as gross_profit, sum(order_qty) as pcssold');
        $this->db->from('ts_orders o');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $now);
        if ($compareweek==1) {
            $this->db->where("date_format(from_unixtime(o.order_date),'%v') >= ", $weekbgn);
            $this->db->where("date_format(from_unixtime(o.order_date),'%v') <= ", $weekend);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SB') {
                $this->db->where_in('o.brand', ['BT','SB']);
            } else {
                $this->db->where('o.brand', $options['brand']);
            }
        }
        $this->db->group_by('orddat');
        $this->db->order_by('orddat');
        $ordersres=$this->db->get()->result_array();
        // Get Other params
        $this->db->select('nd.profit_year, sum(np.profit_operating) as operating');
        $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
        $this->db->select('sum(np.profit_projects) as projects, sum(np.profit_purchases) as purchases');
        $this->db->select('sum(np.profit_w9) as profw9');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('nd.profit_week is not NULL');
        $this->db->where('nd.dateend < ',$now);
        if ($compareweek==1) {
            $this->db->where("nd.profit_week >= ", $weekbgn);
            $this->db->where("nd.profit_week <= ", $weekend);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SB') {
                $this->db->where_in('np.brand', ['BT','SB']);
            } else {
                $this->db->where('np.brand', $options['brand']);
            }
        }
        $this->db->group_by('nd.profit_year');
        $debtres=$this->db->get()->result_array();

        $sales=array();
        $revenue=array();
        $grossprofit=array();
        $pcssold=array();
        $expensive=array();
        $operating=array();
        $advertising=array();
        $payroll=array();
        $odesk=array();
        $profitw9=array();
        $purchases=array();
        $netprofit=array();
        $revenue_perc=array();
        $grossprofit_perc=array();
        $grossrevenue_perc = [];
        // Fill Data
        for($i=$start_year; $i<=$end_year; $i++) {
            $sales[]=$this->empty_html_content;
            $revenue[]= $this->empty_html_content;
            $grossprofit[]=$this->empty_html_content;
            $expensiveval=$netprofitval=$revenuepercval=$grossprofitpercval=0;
            $operating[]=$this->empty_html_content;
            $advertising[]=$this->empty_html_content;
            $payroll[]=$this->empty_html_content;
            $odesk[]=$this->empty_html_content;
            $profitw9[]=$this->empty_html_content;
            $purchases[]=$this->empty_html_content;
            $operating_help[]=$this->empty_html_content;
            $advertising_help[]=$this->empty_html_content;
            $payroll_help[]=$this->empty_html_content;
            $odesk_help[]=$this->empty_html_content;
            $profitw9_help[]=$this->empty_html_content;
            $purchases_help[]=$this->empty_html_content;
            $pcssold[]=$this->empty_html_content;
            $tablekey=count($sales)-1;
            foreach ($ordersres as $row) {
                if ($row['orddat']==$i) {
                    $sales[$tablekey]=QTYOutput($row['cnt']);
                    $revenue[$tablekey]=  MoneyOutput($row['revenue'],0);
                    $grossprofit[$tablekey]=  MoneyOutput($row['gross_profit'],0);
                    $pcssold[$tablekey]=  QTYOutput($row['pcssold']);
                    // Select Data about Other Expensives
                    foreach ($debtres as $drow) {
                        if ($drow['profit_year']==$i) {
                            $operating[$tablekey]=MoneyOutput($drow['operating'],0);
                            $advertising[$tablekey]=MoneyOutput($drow['advertising'],0);
                            $payroll[$tablekey]=  MoneyOutput($drow['payroll'],0);
                            $odesk[$tablekey]=  MoneyOutput($drow['projects'],0);
                            $profitw9[$tablekey]=  MoneyOutput($drow['profw9'],0);
                            $purchases[$tablekey]=  MoneyOutput($drow['purchases']);
                            $expensiveval=floatval($drow['operating'])+floatval($drow['advertising'])+floatval($drow['payroll']);
                            $expensiveval+=floatval($drow['projects'])+floatval($drow['purchases'])+floatval($drow['profw9']);
                            $expensive[$tablekey]=MoneyOutput($expensiveval,0);
                            $operating_helpstr = $advertising_helpstr = $payroll_helpstr = $odesk_helpstr = $profitw9_helpstr= $purchases_helpstr = '';
                            if (abs($row['revenue'])>0) {
                                $operating_helpstr.=round($drow['operating']/$row['revenue']*100,1).'% Rev<br>';
                                $advertising_helpstr.=round($drow['advertising']/$row['revenue']*100,1).'% Rev<br>';
                                $payroll_helpstr.=round($drow['payroll']/$row['revenue']*100,1).'% Rev <br>';
                                $odesk_helpstr.=round($drow['projects']/$row['revenue']*100,1).'% Rev<br>';
                                $profitw9_helpstr.=round($drow['profw9']/$row['revenue']*100,1).'% Rev<br>';
                                $purchases_helpstr.=round($drow['purchases']/$row['revenue']*100,1).'% Rev<br>';
                            }
                            if (abs($row['gross_profit'])>0) {
                                $operating_helpstr.=round($drow['operating']/$row['gross_profit']*100,1).'% GP<br>';
                                $advertising_helpstr.=round($drow['advertising']/$row['gross_profit']*100,1).'% GP<br>';
                                $payroll_helpstr.=round($drow['payroll']/$row['gross_profit']*100,1).'% GP<br>';
                                $odesk_helpstr.=round($drow['projects']/$row['gross_profit']*100,1).'% GP<br>';
                                $profitw9_helpstr.=round($drow['profw9']/$row['gross_profit']*100,1).'% GP<br>';
                                $purchases_helpstr.=round($drow['purchases']/$row['gross_profit']*100,1).'% GP<br>';
                            }
                            $operating_help[$tablekey] = $operating_helpstr;
                            $advertising_help[$tablekey] = $advertising_helpstr;
                            $payroll_help[$tablekey] = $payroll_helpstr;
                            $odesk_help[$tablekey] = $odesk_helpstr;
                            $profitw9_help[$tablekey] = $profitw9_helpstr;
                            $purchases_help[$tablekey] = $purchases_helpstr;
                        }
                    }
                    $netprofitval=floatval($row['gross_profit'])-$expensiveval;
                    $netprofit[$tablekey]=MoneyOutput($netprofitval,0);
                    $revenue_perc[$tablekey]=$this->empty_html_content;
                    $grossprofit_perc[$tablekey]=$this->empty_html_content;
                    $grossrevenue_perc[$tablekey] = $this->empty_html_content;
                    if (floatval($row['revenue'])>0) {
                        $revenuepercval=round($netprofitval/$row['revenue']*100,1).'%';
                        $revenue_perc[$tablekey]=$revenuepercval;
                    }
                    if (abs(floatval($row['revenue']))>0) {
                        $grossrevenue_perc[$tablekey] = round($row['gross_profit']/$row['revenue']*100,1).'%';
                    }
                    if (floatval($row['gross_profit'])>0) {
                        $grossprofitpercval=round($netprofitval/$row['gross_profit']*100,1).'%';
                        $grossprofit_perc[$tablekey]=$grossprofitpercval;
                    }
                }
            }
        }
        if ($compareweek==0) {
            $sales[]=$this->empty_html_content;
            $revenue[]= $this->empty_html_content;
            $grossprofit[]=$this->empty_html_content;
            $revenuepercval=$grossprofitpercval=$grossrevenuepercval=0;
            $operating[]=$this->empty_html_content;
            $advertising[]=$this->empty_html_content;
            $payroll[]=$this->empty_html_content;
            $odesk[]=$this->empty_html_content;
            $profitw9[]=$this->empty_html_content;
            $purchases[]=$this->empty_html_content;
            $pcssold[]=$this->empty_html_content;
            $operating_help[]=$this->empty_html_content;
            $advertising_help[]=$this->empty_html_content;
            $payroll_help[]=$this->empty_html_content;
            $odesk_help[]=$this->empty_html_content;
            $profitw9_help[]=$this->empty_html_content;
            $purchases_help[]=$this->empty_html_content;

            $tablekey=count($sales)-1;
            $operatingpace = 0;
            $advertisingpace = 0;
            $payrollpace = 0;
            $odeskpace = 0;
            $profw9pace=0;
            $purchasespace = 0;
            $grosprofitpace=0;
            $salespace=0;
            $revenuepace=0;
            $pcssoldpace=0;
            $operating_helpstr = $advertising_helpstr = $payroll_helpstr = $odesk_helpstr = $profitw9_helpstr= $purchases_helpstr = '';
            // Build Pace
            if ($paceincome==1) {
                // Income by current year
                foreach ($ordersres as $row) {
                    if ($row['orddat'] == $end_year) {
                        $salespace = round($row['cnt'] * $paceweekkf, 0);
                        $revenuepace = round($row['revenue'] * $paceweekkf, 2);
                        $grosprofitpace = round($row['gross_profit'] * $paceweekkf, 2);
                        $pcssoldpace=round($row['pcssold'] * $paceweekkf,0);
                    }
                }
            } else {
                // Income by prev year
                $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as gross_profit, sum(o.order_qty) as pcssold');
                $this->db->from('ts_orders o');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $pacedatstart);
                $this->db->where('o.order_date < ', $now);
                if (isset($options['brand']) && $options['brand']!=='ALL') {
                    if ($options['brand']=='SB') {
                        $this->db->where_in('o.brand', ['BT','SB']);
                    } else {
                        $this->db->where('o.brand', $options['brand']);
                    }
                }
                $curordat=$this->db->get()->row_array();
                $salespace=intval($curordat['cnt']);
                $revenuepace=floatval($curordat['revenue']);
                $grosprofitpace=floatval($curordat['gross_profit']);
                $pcssoldpace=intval($curordat['pcssold']);
            }
            if ($paceexpense==1) {
                foreach ($debtres as $drow) {
                    if ($drow['profit_year'] == date('Y')) {
                        $operatingpace = round(floatval($drow['operating'] * $paceweekkf), 2);
                        $advertisingpace = round(floatval($drow['advertising']) * $paceweekkf, 2);
                        $payrollpace = round(floatval($drow['payroll']) * $paceweekkf, 2);
                        $odeskpace = round(floatval($drow['projects']) * $paceweekkf, 2);
                        $profw9pace = round(floatval($drow['profw9'])*$paceweekkf,2);
                        $purchasespace = round(floatval($drow['purchases']) * $paceweekkf, 2);
                    }
                }
            } else {
                // Curren Year Espenses
                $this->db->select('sum(np.profit_operating) as operating');
                $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
                $this->db->select('sum(np.profit_projects) as projects, sum(np.profit_purchases) as purchases');
                $this->db->select('sum(np.profit_w9) as profw9');
                $this->db->from('netprofit_dat np');
                $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
                $this->db->where('nd.datebgn >= ', $pacedatstart);
                $this->db->where('nd.dateend < ', $now);
                if (isset($options['brand']) && $options['brand']!=='ALL') {
                    if ($options['brand']=='SB') {
                        $this->db->where_in('np.brand', ['BT','SB']);
                    } else {
                        $this->db->where('np.brand', $options['brand']);
                    }
                }
                $curdebtres=$this->db->get()->row_array();
                // Prev Year
                $operatingpace=floatval($curdebtres['operating']);
                $advertisingpace=floatval($curdebtres['advertising']);
                $payrollpace=floatval($curdebtres['payroll']);
                $odeskpace=floatval($curdebtres['projects']);
                $profw9pace=  floatval($curdebtres['profw9']);
                $purchasespace=floatval($curdebtres['purchases']);
            }
            if (abs($revenuepace)>0) {
                $operating_helpstr.=round($operatingpace/$revenuepace*100,1).'% Rev<br>';
                $advertising_helpstr.=round($advertisingpace/$revenuepace*100,1).'% Rev<br>';
                $payroll_helpstr.=round($payrollpace/$revenuepace*100,1).'% Rev <br>';
                $odesk_helpstr.=round($odeskpace/$revenuepace*100,1).'% Rev<br>';
                $profitw9_helpstr.=round($profw9pace/$revenuepace*100,1).'% Rev<br>';
                $purchases_helpstr.=round($purchasespace/$revenuepace*100,1).'% Rev<br>';
            }
            if (abs($grosprofitpace)>0) {
                $operating_helpstr.=round($operatingpace/$grosprofitpace*100,1).'% GP<br>';
                $advertising_helpstr.=round($advertisingpace/$grosprofitpace*100,1).'% GP<br>';
                $payroll_helpstr.=round($payrollpace/$grosprofitpace*100,1).'% GP <br>';
                $odesk_helpstr.=round($odeskpace/$grosprofitpace*100,1).'% GP<br>';
                $profitw9_helpstr.=round($profw9pace/$grosprofitpace*100,1).'% GP<br>';
                $purchases_helpstr.=round($purchasespace/$grosprofitpace*100,1).'% GP<br>';
            }
            $operating_help[$tablekey]=$operating_helpstr;
            $advertising_help[$tablekey]=$advertising_helpstr;
            $payroll_help[$tablekey]=$payroll_helpstr;
            $odesk_help[$tablekey]=$odesk_helpstr;
            $profitw9_help[$tablekey]=$profitw9_helpstr;
            $purchases_help[$tablekey]=$purchases_helpstr;

            $expensiveval = $operatingpace + $advertisingpace + $payrollpace + $odeskpace + $purchasespace+$profw9pace;
            $netprofitval = $grosprofitpace - $expensiveval;

            $sales[$tablekey] = QTYOutput($salespace);
            $revenue[$tablekey] = MoneyOutput($revenuepace, 0);
            $grossprofit[$tablekey] = MoneyOutput($grosprofitpace, 0);
            $pcssold[$tablekey]=  QTYOutput($pcssoldpace);
            $netprofit[$tablekey] = MoneyOutput($netprofitval, 0);
            $operating[$tablekey] = MoneyOutput($operatingpace, 0);
            $advertising[$tablekey] = MoneyOutput($advertisingpace, 0);
            $payroll[$tablekey] = MoneyOutput($payrollpace, 0);
            $odesk[$tablekey] = MoneyOutput($odeskpace, 0);
            $profitw9[$tablekey]=  MoneyOutput($profw9pace,0);
            $purchases[$tablekey] = MoneyOutput($purchasespace);
            $expensive[$tablekey] = MoneyOutput($expensiveval, 0);

            $revenue_perc[$tablekey] = $this->empty_html_content;
            $grossprofit_perc[$tablekey] = $this->empty_html_content;
            if (floatval($revenuepace) > 0) {
                $revenuepercval = round($netprofitval / $revenuepace * 100, 1) . '%';
                $revenue_perc[$tablekey] = $revenuepercval;
            }
            if (abs(floatval($revenuepace))>0) {
                $grossrevenue_perc[$tablekey] = round($grosprofitpace/$revenuepace*100,1).'%';
            }
            if (floatval($grosprofitpace) > 0) {
                $grossprofitpercval = round($netprofitval / $grosprofitpace * 100, 1) . '%';
                $grossprofit_perc[$tablekey] = $grossprofitpercval;
            }
        }

        $out=array(
            'start_year'=>$start_year,
            'end_year'=>$end_year,
            'sales'=>$sales,
            'revenue'=>$revenue,
            'grossprofit'=>$grossprofit,
            'pcssold'=>$pcssold,
            'expenses'=>$expensive,
            'operating'=>$operating,
            'advertising'=>$advertising,
            'payroll'=>$payroll,
            'odesk'=>$odesk,
            'profitw9'=>$profitw9,
            'purchases'=>$purchases,
            'netprofit'=>$netprofit,
            'revenue_perc'=>$revenue_perc,
            'grossprofit_perc'=>$grossprofit_perc,
            'grossrevenue_perc' => $grossrevenue_perc,
            'operating_help' => $operating_help,
            'advertising_help' => $advertising_help,
            'payroll_help' => $payroll_help,
            'odesk_help' => $odesk_help,
            'profitw9_help' => $profitw9_help,
            'purchases_help' => $purchases_help,
        );

        return $out;
    }

    public function get_netprofit_chartbyweekdata($options) {
        $datarows=array();
        $percrows = [];
        $compareweek=$options['compareweek'];
        $paceincome=$options['paceincome'];
        $paceexpense=$options['paceexpense'];
        if ($compareweek==1) {
            $weekbgn=intval($options['weekbgn']);
            $weekend=intval($options['weekend']);
        }
        $brand = ifset($options, 'brand', 'ALL');
        // Get an end of full week
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        // Get current week number
        $this->db->select('profit_week');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not NULL');
        $this->db->where('dateend < ',$now);
        $this->db->order_by('datebgn','desc');
        $weekres=$this->db->get()->row_array();
        if ($weekres['profit_week']>52) {
            $weekres['profit_week']=52;
        }
        $paceweekkf=52/$weekres['profit_week'];
        $current_weeknum=$weekres['profit_week'];

        $datarows[]=array('Year', 'Gross Profit', 'Expenses','Net Profit');
        $percrows[]=['Year','Net Profit %','Gross Profit %','Net/Gross %'];
        $this->db->select('max(profit_year) end_year, min(profit_year) as start_year, min(datebgn) as start_date');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('profit_year >=', $this->start_netprofitdatashow);
        $this->db->where('dateend < ',$now);
        $yres=$this->db->get()->row_array();
        // Get Elapsed Days
        $start_year=$yres['start_year'];
        $end_year=$yres['end_year'];
        $start_date=$yres['start_date'];

        // Period begin in case of paceincome=2 - prev year as pace
        $prev_year=$end_year-1;
        if ($current_weeknum==52) {
            $prev_weeknum=1;
            $prev_year=$prev_year+1;
        } else {
            $prev_weeknum=$current_weeknum+1;
        }
        // Select date
        $this->db->select('profit_id,datebgn');
        $this->db->from('netprofit');
        $this->db->where('profit_week', $prev_weeknum);
        $this->db->where('profit_year', $prev_year);
        $paceres=$this->db->get()->row_array();
        if (isset($paceres['profit_id'])) {
            $pacedatstart=$paceres['datebgn'];
        } else {
            // Exclude
            $pacedatstart=strtotime(date("Y-m-d", $now) . " -1year -7days");
        }

        // Build empty array
        $this->db->select('date_format(from_unixtime(o.order_date),\'%x\') as orddat, sum(o.profit) as gross_profit, sum(order_qty) as pcssold, sum(o.revenue) as revenue', FALSE);
        $this->db->from('ts_orders o');
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.order_date < ', $now);
        if ($compareweek==1) {
            $this->db->where("date_format(from_unixtime(o.order_date),'%v') >= ", $weekbgn);
            $this->db->where("date_format(from_unixtime(o.order_date),'%v') <= ", $weekend);
        }
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('o.brand', ['BT','SB']);
            } else {
                $this->db->where('o.brand', $brand);
            }
        }
        $this->db->group_by('orddat');
        $this->db->order_by('orddat');
        $ordersres=$this->db->get()->result_array();

        // Get Other params
        $this->db->select('nd.profit_year, sum(np.profit_operating) as operating');
        $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
        $this->db->select('sum(np.profit_projects) as projects, sum(np.profit_purchases) as purchases');
        $this->db->select('sum(np.profit_w9) as profitw9');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd', 'nd.profit_id=np.profit_id');
        $this->db->where('nd.profit_week is not NULL');
        $this->db->where('nd.dateend < ',$now);
        if ($compareweek==1) {
            $this->db->where("np.profit_week >= ", $weekbgn);
            $this->db->where("np.profit_week <= ", $weekend);
        }
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('np.brand', ['BT','SB']);
            } else {
                $this->db->where('np.brand', $brand);
            }
        }
        $this->db->group_by('nd.profit_year');
        $debtres=$this->db->get()->result_array();

        for ($i=$start_year; $i<$end_year; $i++) {
            foreach ($ordersres as $row) {
                $grossprofit=$expensive=$netprofit=$revenue=0;
                if ($row['orddat']==$i) {
                    $grossprofit=floatval($row['gross_profit']);
                    $revenue = floatval($row['revenue']);
                    foreach ($debtres as $drow) {
                        if ($drow['profit_year']==$i) {
                            $expensive=floatval($drow['operating'])+floatval($drow['advertising'])+floatval($drow['payroll']);
                            $expensive+=floatval($drow['projects'])+floatval($drow['purchases']);
                            $expensive+=floatval($drow['profitw9']);
                            break;
                        }
                    }
                    $netprofit=$grossprofit-$expensive;
                    $datarows[]=array($i, round($grossprofit,0), round($expensive,0), round($netprofit,0));
                    $revenuepercval = ($revenue!=0 ? round($netprofit / $revenue * 100, 1) : 0);
                    $grossrevenuepercval = ($revenue!=0 ? round($grossprofit / $revenue * 100,1) : 0);
                    $grossprofitpercval = ($netprofit!=0 ? round($netprofit / $grossprofit * 100, 1) : 0);
                    $percrows[]=[$i, $revenuepercval, $grossrevenuepercval, $grossprofitpercval];
                    break;
                }
            }
        }
        if ($compareweek==0) {
            $grossprofit=$expensive=$netprofit=0;
            if ($paceincome==1) {
                // Current Year Income
                foreach ($ordersres as $row) {
                    // if ($row['orddat']==date('Y')) {
                    if ($row['orddat']==$end_year) {
                        $grossprofit=floatval($row['gross_profit'])*$paceweekkf;
                        $revenue = floatval($row['revenue'])*$paceweekkf;
                        $pcssold=intval($row['pcssold'])*$paceweekkf;
                    }
                }
            } else {
                // Prev Year income
                $this->db->select('sum(o.profit) as gross_profit, sum(o.order_qty) as pcssold, sum(o.revenue) as revenue');
                $this->db->from('ts_orders o');
                $this->db->where('o.is_canceled',0);
                $this->db->where('o.order_date >= ', $pacedatstart);
                $this->db->where('o.order_date < ', $now);
                $curordat=$this->db->get()->row_array();
                $grossprofit=floatval($curordat['gross_profit']);
                $pcssold=intval($curordat['pcssold']);
                $revenue = floatval($curordat['revenue']);
            }
            if ($paceexpense==1) {
                // Curren Year Expensives
                foreach ($debtres as $drow) {
                    // if ($drow['profit_year']==date('Y')) {
                    if ($drow['profit_year']==$end_year) {
                        $expensive=floatval($drow['operating'])+floatval($drow['advertising'])+floatval($drow['payroll']);
                        $expensive+=floatval($drow['projects'])+floatval($drow['purchases']);
                        $expensive+=floatval($drow['profitw9']);
                        $expensive=$expensive*$paceweekkf;
                        break;
                    }
                }
            } else {
                // Curren Year Espenses
                $this->db->select('sum(np.profit_operating) as operating');
                $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
                $this->db->select('sum(np.profit_projects) as projects, sum(np.profit_purchases) as purchases');
                $this->db->select('sum(np.profit_w9) as profitw9');
                $this->db->from('netprofit np');
                $this->db->where('np.datebgn >= ', $pacedatstart);
                $this->db->where('np.dateend < ', $now);
                $curdebtres=$this->db->get()->row_array();
                $expensive=0;
                foreach ($curdebtres as $key=>$val) {
                    $expensive+=floatval($val);
                }
            }
            $netprofit=$grossprofit-$expensive;
            $datarows[]=array('On Pace', round($grossprofit,0), round($expensive,0), round($netprofit,0));

            $revenuepercval = ($revenue!=0 ? round($netprofit / $revenue * 100, 1) : 0);
            $grossrevenuepercval = ($revenue!=0 ? round($grossprofit / $revenue * 100,1) : 0);
            $grossprofitpercval = ($netprofit!=0 ? round($netprofit / $grossprofit * 100, 1) : 0);
            $percrows[]=['On Pace', $revenuepercval, $grossrevenuepercval, $grossprofitpercval];
        }
        return [
            'datarows' => $datarows,
            'percrows' => $percrows,
        ];
    }

    public function get_currentyearweeks() {
        $weeks=array();
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        $year=date('Y', $now);

        $this->db->select('max(profit_week) curweek');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('dateend < ', $now);
        $this->db->where('profit_year', $year);
        $weekres=$this->db->get()->row_array();

        $curweek=52;
        if (isset($weekres['curweek'])) {
            $curweek=$weekres['curweek'];
        }
        for ($i=1; $i<53; $i++) {
            $dates=getDatesByWeek($i, $year);
            $label=date('M, d',$dates['start_week']).'-'.date('M, d',$dates['end_week']);
            $weeks[]=array('weeknum'=>$i,'label'=>$label,'current'=>($i==$curweek ? 1 : 0));
        }
        return $weeks;
    }

    public function get_expansedates() {

        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        $year=date('Y', $now);

        $this->db->select('max(dateend) as dateend, min(datebgn) as datebgn');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('dateend < ', $now);
        $this->db->where('profit_year', $year);
        $weekres=$this->db->get()->row_array();

        $out=array('year'=>$year, 'datebgn'=>$weekres['datebgn'], 'dateend'=>$weekres['dateend']);
        return $out;
    }

    public function _check_current_week($user_id) {
        $curweek=date('W');
        $curyear=date('Y');
        $this->db->select('count(profit_id) as cnt');
        $this->db->from('netprofit');
        $this->db->where('profit_week', $curweek);
        $this->db->where('profit_year', $curyear);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            $dats=getDatesByWeek($curweek, $curyear);
            $this->db->set('profit_week',$curweek);
            $this->db->set('profit_year',$curyear);
            $this->db->set('datebgn',$dats['start_week']);
            $this->db->set('dateend',$dats['end_week']);
            $this->db->set('create_user',$user_id);
            $this->db->set('create_date',date('Y-m-d H:i:s'));
            $this->db->set('update_user',$user_id);
            $this->db->insert('netprofit');
            $newprofit = $this->db->insert_id();
            // Insrt into Netprofit Dat
            $this->db->set('profit_id', $newprofit);
            $this->db->set('brand','SB');
            $this->db->insert('netprofit_dat');
            $this->db->set('profit_id', $newprofit);
            $this->db->set('brand','BT');
            $this->db->insert('netprofit_dat');
        }
    }

    public function _check_current_month($user_id) {
        $curmonth=date('m');
        $curyear=date('Y');
        $this->db->select('count(profit_id) as cnt');
        $this->db->from('netprofit');
        $this->db->where('profit_month', $curmonth);
        $this->db->where('profit_year', $curyear);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            $dats=getDatesByMonth($curmonth, $curyear);
            $this->db->set('profit_month',$curmonth);
            $this->db->set('profit_year',$curyear);
            $this->db->set('datebgn',$dats['start_month']);
            $this->db->set('dateend',$dats['end_month']);
            $this->db->set('create_user',$user_id);
            $this->db->set('create_date',date('Y-m-d H:i:s'));
            $this->db->set('update_user',$user_id);
            $this->db->insert('netprofit');
            $newprofit = $this->db->insert_id();
            // Insrt into Netprofit Dat
            $this->db->set('profit_id', $newprofit);
            $this->db->set('brand','SB');
            $this->db->insert('netprofit_dat');
            $this->db->set('profit_id', $newprofit);
            $this->db->set('brand','BT');
            $this->db->insert('netprofit_dat');
        }
    }

    public function get_onpacecompare($options) {
        // Get Elapsed Days
        // $this->load->model('reports_model');
        // Get an end of full week
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        // Get current week number
        $this->db->select('profit_week');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not NULL');
        $this->db->where('dateend < ',$now);
        $this->db->order_by('datebgn','desc');
        $weekres=$this->db->get()->row_array();
        if ($weekres['profit_week']>52) {
            $weekres['profit_week']=52;
        }
        $paceweekkf=52/$weekres['profit_week'];
        $current_weeknum=$weekres['profit_week'];
        // Prepare return array
        $this->db->select('max(profit_year) end_year');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not null');
        $this->db->where('profit_year >=', $this->start_netprofitdatashow);
        $this->db->where('dateend < ',$now);
        $yres=$this->db->get()->row_array();
        $curyear=$yres['end_year'];
        $prevyear=(isset($options['compareyear']) ? $options['compareyear'] : $curyear-1);
        $compare=array();
        $paceincome=$options['paceincome'];
        $paceexpense=$options['paceexpense'];
        // Period begin in case of paceincome=2 - prev year as pace
        $prev_year=$curyear-1;
        if ($current_weeknum==52) {
            $prev_weeknum=1;
            $prev_year=$prev_year+1;
        } else {
            $prev_weeknum=$current_weeknum+1;
        }
        // Select date
        $this->db->select('profit_id,datebgn');
        $this->db->from('netprofit');
        $this->db->where('profit_week', $prev_weeknum);
        $this->db->where('profit_year', $prev_year);
        $paceres=$this->db->get()->row_array();
        if (isset($paceres['profit_id'])) {
            $pacedatstart=$paceres['datebgn'];
        } else {
            // Exclude
            $pacedatstart=strtotime(date("Y-m-d", $now) . " -1year -7days");
        }

        // Get current year data
        $this->db->select('count(o.order_id) as cnt');
        $this->db->select('sum(o.revenue) as revenue, sum(o.profit) as gross_profit, sum(o.order_qty) as pcssold');
        $this->db->from('ts_orders o');
        $this->db->where('o.is_canceled',0);
        $this->db->where("date_format(from_unixtime(o.order_date),'%x') = {$curyear}");
        $this->db->where('o.order_date < ', $now);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SB') {
                $this->db->where_in('o.brand', ['BT','SB']);
            } else {
                $this->db->where('o.brand', $options['brand']);
            }
        }
        $curyear_orders=$this->db->get()->row_array();
        // Get Prev year
        $this->db->select('count(o.order_id) as cnt', FALSE);
        $this->db->select('sum(o.revenue) as revenue, sum(o.profit) as gross_profit, sum(o.order_qty) as pcssold');
        $this->db->from('ts_orders o');
        $this->db->where('o.is_canceled',0);
        $this->db->where("date_format(from_unixtime(o.order_date),'%x') ={$prevyear}");
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SB') {
                $this->db->where_in('o.brand', ['BT','SB']);
            } else {
                $this->db->where('o.brand', $options['brand']);
            }
        }
        $prvyear_orders=$this->db->get()->row_array();
        if ($paceincome==2) {
            // Prev Year income
            // $now = strtotime(date('Y-m-d',time())); // or your date as well
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as gross_profit, sum(o.order_qty) as pcssold');
            $this->db->from('ts_orders o');
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $pacedatstart);
            $this->db->where('o.order_date < ', $now);
            if (isset($options['brand']) && $options['brand']!=='ALL') {
                if ($options['brand']=='SB') {
                    $this->db->where_in('o.brand', ['BT','SB']);
                } else {
                    $this->db->where('o.brand', $options['brand']);
                }
            }
            $paceordat=$this->db->get()->row_array();
        }
        // # Orders
        $compare['sales']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>  $this->empty_html_content,
            'class'=>'',
        );
        if ($paceincome==1) {
            $placesales=round($curyear_orders['cnt']*$paceweekkf,0);
        } else {
            // $placesales=intval($curyear_orders['cnt'])+intval($paceordat['cnt']);
            $placesales=intval($paceordat['cnt']);
        }
        $diffsales=$placesales-$prvyear_orders['cnt'];
        if ($diffsales!=0) {
            $diffsales_prc=round($diffsales/$prvyear_orders['cnt']*100,0);
            $diffsales_class=($diffsales<0 ? 'negative' : '');
            $compare['sales']=array(
                'grown'=>($diffsales<0 ? '(' : '').QTYOutput(abs($diffsales)).($diffsales<0 ? ')' : ''),
                'grownprc'=>($diffsales<0 ? '(' : '').abs($diffsales_prc).'%'.($diffsales<0 ? ')' : ''),
                'class'=>$diffsales_class,
            );
        }
        // # Orders QTY
        $compare['pcssold']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>  $this->empty_html_content,
            'class'=>'',
        );
        if ($paceincome==1) {
            $placepcssold=round($curyear_orders['pcssold']*$paceweekkf,0);
        } else {
            // $placepcssold=intval($curyear_orders['pcssold'])+intval($paceordat['pcssold']);
            $placepcssold=intval($paceordat['pcssold']);
        }
        $diffpcssold=$placepcssold-$prvyear_orders['pcssold'];
        if ($diffpcssold!=0) {
            $diffpcssold_prc=round($diffpcssold/$prvyear_orders['pcssold']*100,0);
            $diffpcssold_class=($diffpcssold<0 ? 'negative' : '');
            $compare['pcssold']=array(
                'grown'=>($diffpcssold<0 ? '(' : '').QTYOutput(abs($diffpcssold)).($diffpcssold<0 ? ')' : ''),
                'grownprc'=>($diffpcssold<0 ? '(' : '').abs($diffpcssold_prc).'%'.($diffpcssold<0 ? ')' : ''),
                'class'=>$diffpcssold_class,
            );
        }
        // Revenue
        $compare['revenue']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceincome==1) {
            $placerevenue=round($curyear_orders['revenue']*$paceweekkf,0);
        } else {
            // $placerevenue=round(floatval($curyear_orders['revenue'])+floatval($paceordat['revenue']),0);
            $placerevenue=round(floatval($paceordat['revenue']),0);
        }
        $diffrevenue=round($placerevenue-$prvyear_orders['revenue'],0);
        if ($diffrevenue!=0) {
            $diffrevenue_prc=round($diffrevenue/$prvyear_orders['revenue']*100,0);
            $diffrevenue_class=($diffrevenue<0 ? 'negative' : '');

            $compare['revenue']=array(
                'grown'=>($diffrevenue<0 ? '(' : '').  MoneyOutput(abs($diffrevenue),0).($diffrevenue<0 ? ')' : ''),
                'grownprc'=>($diffrevenue<0 ? '(' : '').abs($diffrevenue_prc).'%'.($diffrevenue<0 ? ')' : ''),
                'class'=>$diffrevenue_class,
            );
        }
        // Gross Profit
        $compare['gross_profit']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceincome==1) {
            $placeprofit=round($curyear_orders['gross_profit']*$paceweekkf,0);
        } else {
            // $placeprofit=round(floatval($curyear_orders['gross_profit'])+floatval($paceordat['gross_profit']),0);
            $placeprofit=round(floatval($paceordat['gross_profit']),0);
        }
        $diffprofit=round($placeprofit-$prvyear_orders['gross_profit'],0);
        if ($diffprofit!=0) {
            $diffprofit_prc=round($diffprofit/$prvyear_orders['gross_profit']*100,0);
            $diffprofit_class=($diffprofit<0 ? 'negative' : '');

            $compare['gross_profit']=array(
                'grown'=>($diffprofit<0 ? '(' : '').  MoneyOutput(abs($diffprofit),0).($diffprofit<0 ? ')' : ''),
                'grownprc'=>($diffprofit<0 ? '(' : '').abs($diffprofit_prc).'%'.($diffprofit<0 ? ')' : ''),
                'class'=>$diffprofit_class,
            );
        }
        // Gross / Revenue %
        $compare['grossrevenue']=array(
            'grown'=>$this->empty_html_content,
            'class'=>'',
        );
        if (abs(floatval($curyear_orders['revenue']))>0) {
            if ($paceincome==1) {
                $placegrossrevenue=round(round($curyear_orders['gross_profit']*$paceweekkf,0)/round($curyear_orders['revenue']*$paceweekkf,0)*100,0);
            } else {
                $placegrossrevenue=round(floatval($paceordat['gross_profit'])/$paceordat['revenue']*100,0);
            }

        }
        $prev_grossrevenue=0;
        if (abs(floatval($prvyear_orders['revenue']))>0) {
            $prev_grossrevenue = $prvyear_orders['gross_profit'] / $prvyear_orders['revenue'] * 100;
        }
        $diffgrossrevenue=round($placegrossrevenue-$prev_grossrevenue,0);
        if ($diffgrossrevenue!=0) {
            $diffgrossrevenue_class=($diffgrossrevenue<0 ? 'negative' : '');
            $compare['grossrevenue']=array(
                'grown'=>($diffgrossrevenue<0 ? '(' : '').  abs($diffgrossrevenue).($diffgrossrevenue<0 ? ')' : ''),
                'class'=>$diffgrossrevenue_class,
            );
        }

        $cur_expenses=$prv_expenses=0;


        $this->db->select('sum(np.profit_operating) as operating');
        $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
        $this->db->select('sum(np.profit_projects) as odesk, sum(np.profit_purchases) as purchases');
        $this->db->select('sum(np.profit_w9) as profitw9');
        $this->db->from('netprofit_dat np');
        $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
        $this->db->where('nd.profit_week is not NULL');
        $this->db->where('nd.profit_year', $prevyear);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SB') {
                $this->db->where_in('np.brand', ['BT','SB']);
            } else {
                $this->db->where('np.brand', $options['brand']);
            }
        }
        $prvyear_netdata=$this->db->get()->row_array();
        if ($paceexpense==1) {
            $this->db->select('sum(np.profit_operating) as operating');
            $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
            $this->db->select('sum(np.profit_projects) as odesk, sum(np.profit_purchases) as purchases');
            $this->db->select('sum(np.profit_w9) as profitw9');
            $this->db->from('netprofit_dat np');
            $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
            $this->db->where('nd.profit_week is not NULL');
            $this->db->where('nd.profit_year', $curyear);
            $this->db->where('nd.dateend < ', $now);
            if (isset($options['brand']) && $options['brand']!=='ALL') {
                if ($options['brand']=='SB') {
                    $this->db->where_in('np.brand', ['BT','SB']);
                } else {
                    $this->db->where('np.brand', $options['brand']);
                }
            }
            $curyear_netdata=$this->db->get()->row_array();
            foreach ($curyear_netdata as $row) {
                $cur_expenses+=floatval($row);
            }
        } else {
            $this->db->select('sum(np.profit_operating) as operating');
            $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
            $this->db->select('sum(np.profit_projects) as odesk, sum(np.profit_purchases) as purchases');
            $this->db->select('sum(np.profit_w9) as profitw9');
            $this->db->from('netprofit_dat np');
            $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
            $this->db->where('nd.datebgn >= ', $pacedatstart);
            $this->db->where('nd.dateend < ', $now);
            if (isset($options['brand']) && $options['brand']!=='ALL') {
                if ($options['brand']=='SB') {
                    $this->db->where_in('np.brand', ['BT','SB']);
                } else {
                    $this->db->where('np.brand', $options['brand']);
                }
            }
            $pace_netdata=$this->db->get()->row_array();
        }
        foreach ($prvyear_netdata as $row) {
            $prv_expenses+=floatval($row);
        }
        // All Expenses
        $compare['expenses']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placeexpens=round($cur_expenses*$paceweekkf,0);
        } else {
            // $placeexpens=floatval($cur_expenses);
            $placeexpens=0;
            foreach ($pace_netdata as $key=>$val) {
                $placeexpens+=floatval($val);
            }
        }
        $diffexpens=round($placeexpens-$prv_expenses,0);
        if ($diffexpens!=0) {
            if ($prv_expenses==0) {
                $diffexpens_prc = $diffexpens > 0 ? 100 : -100;
            } else {
                $diffexpens_prc=round($diffexpens/$prv_expenses*100,0);
            }
            $diffexpens_class=($diffexpens<0 ? 'negative' : '');

            $compare['expenses']=array(
                'grown'=>($diffexpens<0 ? '(' : '').  MoneyOutput(abs($diffexpens),0).($diffexpens<0 ? ')' : ''),
                'grownprc'=>($diffexpens<0 ? '(' : '').abs($diffexpens_prc).'%'.($diffexpens<0 ? ')' : ''),
                'class'=>$diffexpens_class,
            );
        }
        // Operating
        $compare['operating']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placeoperating=round($curyear_netdata['operating']*$paceweekkf,2);
        } else {
            // $placeoperating=round(floatval($curyear_netdata['operating'])+floatval($pace_netdata['operating']) ,2);
            $placeoperating=round(floatval($pace_netdata['operating']) ,2);
        }
        $diffoperating=round($placeoperating-$prvyear_netdata['operating'],0);
        if ($diffoperating!=0) {
            if ($prvyear_netdata['operating']==0) {
                $diffoperating_prc = $diffoperating > 0 ? 100 : -100;
            } else {
                $diffoperating_prc=round($diffoperating/$prvyear_netdata['operating']*100,0);
            }
            $diffoperating_class=($diffoperating<0 ? 'negative' : '');

            $compare['operating']=array(
                'grown'=>($diffoperating<0 ? '(' : '').  MoneyOutput(abs($diffoperating),0).($diffoperating<0 ? ')' : ''),
                'grownprc'=>($diffoperating<0 ? '(' : '').abs($diffoperating_prc).'%'.($diffoperating<0 ? ')' : ''),
                'class'=>$diffoperating_class,
            );
        }
        // Advertesing - advertising
        $compare['advertising']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placeadvertising=round($curyear_netdata['advertising']*$paceweekkf,2);
        } else {
            // $placeadvertising=round(floatval($curyear_netdata['advertising'])+floatval($pace_netdata['advertising']) ,2);
            $placeadvertising=round(floatval($pace_netdata['advertising']) ,2);
        }
        $diffadvertising=round($placeadvertising-$prvyear_netdata['advertising'],0);
        if ($diffadvertising!=0) {
            if ($prvyear_netdata['advertising']==0) {
                $diffadvertising_prc = $diffadvertising > 0 ? 100 : -100;
            } else {
                $diffadvertising_prc = round($diffadvertising/$prvyear_netdata['advertising']*100,0);
            }
            $diffadvertising_class=($diffadvertising<0 ? 'negative' : '');

            $compare['advertising']=array(
                'grown'=>($diffadvertising<0 ? '(' : '').  MoneyOutput(abs($diffadvertising),0).($diffadvertising<0 ? ')' : ''),
                'grownprc'=>($diffadvertising<0 ? '(' : '').abs($diffadvertising_prc).'%'.($diffadvertising<0 ? ')' : ''),
                'class'=>$diffadvertising_class,
            );
        }
        // Payroll
        $compare['payroll']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placepayroll=round($curyear_netdata['payroll']*$paceweekkf,2);
        } else {
            // $placepayroll=round(floatval($curyear_netdata['payroll'])+floatval($pace_netdata['payroll']) ,2);
            $placepayroll=round(floatval($pace_netdata['payroll']),2);
        }
        $diffpayroll=round($placepayroll-$prvyear_netdata['payroll'],0);
        if ($diffpayroll!=0) {
            if ($prvyear_netdata['payroll']==0) {
                $diffpayroll_prc = $diffpayroll > 0 ? 100 : -100;
            } else {
                $diffpayroll_prc=round($diffpayroll/$prvyear_netdata['payroll']*100,0);
            }
            $diffpayroll_class=($diffpayroll<0 ? 'negative' : '');

            $compare['payroll']=array(
                'grown'=>($diffpayroll<0 ? '(' : '').  MoneyOutput(abs($diffpayroll),0).($diffpayroll<0 ? ')' : ''),
                'grownprc'=>($diffpayroll<0 ? '(' : '').abs($diffpayroll_prc).'%'.($diffpayroll<0 ? ')' : ''),
                'class'=>$diffpayroll_class,
            );
        }
        // Odesk
        $compare['odesk']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placeodesk=round($curyear_netdata['odesk']*$paceweekkf,2);
        } else {
            // $placeodesk=round(floatval($curyear_netdata['odesk'])+floatval($pace_netdata['odesk']) ,2);
            $placeodesk=round(floatval($pace_netdata['odesk']) ,2);
        }
        $diffodesk=round($placeodesk-$prvyear_netdata['odesk'],0);
        if ($diffodesk!=0) {
            if ($prvyear_netdata['odesk']==0) {
                $diffodesk_prc = $diffodesk > 0 ? 100 : -100;
            } else {
                $diffodesk_prc=round($diffodesk/$prvyear_netdata['odesk']*100,0);
            }
            $diffodesk_class=($diffodesk<0 ? 'negative' : '');
            $compare['odesk']=array(
                'grown'=>($diffodesk<0 ? '(' : '').  MoneyOutput(abs($diffodesk),0).($diffodesk<0 ? ')' : ''),
                'grownprc'=>($diffodesk<0 ? '(' : '').abs($diffodesk_prc).'%'.($diffodesk<0 ? ')' : ''),
                'class'=>$diffodesk_class,
            );
        }
        // Odesk
        $compare['profitw9']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placew9=round($curyear_netdata['profitw9']*$paceweekkf,2);
        } else {
            // $placeodesk=round(floatval($curyear_netdata['odesk'])+floatval($pace_netdata['odesk']) ,2);
            $placew9=round(floatval($pace_netdata['profitw9']) ,2);
        }
        $diffw9=round($placew9-$prvyear_netdata['profitw9'],0);
        if ($diffw9!=0) {
            if ($prvyear_netdata['profitw9']==0) {
                $diffw9_prc= $diffw9 > 0 ? 100 : -100;
            } else {
                $diffw9_prc=round($diffw9/$prvyear_netdata['profitw9']*100,0);
            }
            $diffw9_class=($diffw9<0 ? 'negative' : '');
            $compare['profitw9']=array(
                'grown'=>($diffw9<0 ? '(' : '').  MoneyOutput(abs($diffw9),0).($diffw9<0 ? ')' : ''),
                'grownprc'=>($diffw9<0 ? '(' : '').abs($diffw9_prc).'%'.($diffw9<0 ? ')' : ''),
                'class'=>$diffw9_class,
            );
        }
        // Purchases
        $compare['purchases']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );
        if ($paceexpense==1) {
            $placepurchases=round($curyear_netdata['purchases']*$paceweekkf,2);
        } else {
            // $placepurchases=round(floatval($curyear_netdata['purchases'])+floatval($pace_netdata['purchases']) ,2);
            $placepurchases=round(floatval($pace_netdata['purchases']) ,2);
        }
        $diffpurchases=round($placepurchases-$prvyear_netdata['purchases'],0);
        if ($diffpurchases!=0) {
            if ($prvyear_netdata['purchases']==0) {
                $diffpurchases_prc=$diffpurchases > 0 ? 100 : -100;
            } else {
                $diffpurchases_prc=round($diffpurchases/$prvyear_netdata['purchases']*100,0);
            }
            $diffpurchases_class=($diffpurchases<0 ? 'negative' : '');

            $compare['purchases']=array(
                'grown'=>($diffpurchases<0 ? '(' : '').  MoneyOutput(abs($diffpurchases),0).($diffpurchases<0 ? ')' : ''),
                'grownprc'=>($diffpurchases<0 ? '(' : '').abs($diffpurchases_prc).'%'.($diffpurchases<0 ? ')' : ''),
                'class'=>$diffpurchases_class,
            );
        }

        $compare['netprofit']=array(
            'grown'=>$this->empty_html_content,
            'grownprc'=>$this->empty_html_content,
            'class'=>'',
        );

        $placenetprofit=$placeprofit-$placeexpens;
        $diffnetprofit=round($placenetprofit-($prvyear_orders['gross_profit']-$prv_expenses),0);
        if ($diffnetprofit!=0) {
            if (($prvyear_orders['gross_profit']-$prv_expenses) ==0) {
                $diffnetprofit_prc= $diffnetprofit > 0 ? 100 : -100;
            }  else {
                $diffnetprofit_prc=round($diffnetprofit/($prvyear_orders['gross_profit']-$prv_expenses)*100,0);
            }
            $diffnetprofit_class=($diffnetprofit<0 ? 'negative' : '');

            $compare['netprofit']=array(
                'grown'=>($diffnetprofit<0 ? '(' : '').  MoneyOutput(abs($diffnetprofit),0).($diffnetprofit<0 ? ')' : ''),
                'grownprc'=>($diffnetprofit<0 ? '(' : '').abs($diffnetprofit_prc).'%'.($diffnetprofit<0 ? ')' : ''),
                'class'=>$diffnetprofit_class,
            );
        }

        $compare['revenuegrow']=array(
            'grown'=>$this->empty_html_content,
            'class'=>'',
        );
        $currevenprc=0;
        if ($placerevenue!=0) {
            $currevenprc=round($placenetprofit/$placerevenue*100,0);
        }
        if ($prvyear_orders['revenue']==0) {
            $prvrevenprc=($prvyear_orders['gross_profit']-$prv_expenses) > 0 ? 100 : -100;
        } else {
            $prvrevenprc=round(($prvyear_orders['gross_profit']-$prv_expenses)/$prvyear_orders['revenue']*100);
        }

        $diffrevgrow=round($currevenprc-$prvrevenprc,0);
        if ($diffrevgrow!=0) {
            $compare['revenuegrow']=array(
                'grown'=>($diffrevgrow<0 ? '(' : '').abs($diffrevgrow).'%'.($diffrevgrow<0 ? ')' : ''),
                'class'=>($diffrevgrow<0 ? 'negative' : ''),
            );
        }

        $compare['grosprofitgrow']=array(
            'grown'=>$this->empty_html_content,
            'class'=>'',
        );
        $curgpprc=0;
        if ($placeprofit!=0) {
            $curgpprc=round($placenetprofit/$placeprofit*100,0);
        }
        $prvgpprc=round(($prvyear_orders['gross_profit']-$prv_expenses)/$prvyear_orders['gross_profit']*100);
        $diffgpprc=round($curgpprc-$prvgpprc,0);
        if ($diffgpprc!=0) {
            $compare['grosprofitgrow']=array(
                'grown'=>($diffgpprc<0 ? '(' : '').abs($diffgpprc).'%'.($diffgpprc<0 ? ')' : ''),
                'class'=>($diffgpprc<0 ? 'negative' : ''),
            );
        }
        return $compare;
    }

    public function get_ownertax_dates() {
        $out=array('year'=>date('Y'), 'week'=>'1', 'date'=>time());
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        // Get current week number
        $this->db->select('profit_week, profit_year');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not NULL');
        $this->db->where('dateend < ',$now);
        $this->db->order_by('datebgn','desc');
        $weekres=$this->db->get()->row_array();
        $out['date']=$now;
        $out['year']=$weekres['profit_year'];
        $out['week']=$weekres['profit_week'];
        return $out;
    }


    public function get_projected_netprofit($now, $year, $paceincome, $paceexpense, $brand) {
        // $this->load->model('reports_model');
        // Get current week number
        $this->db->select('profit_week');
        $this->db->from('netprofit');
        $this->db->where('profit_week is not NULL');
        $this->db->where('dateend < ',$now);
        $this->db->order_by('datebgn','desc');
        $weekres=$this->db->get()->row_array();
        if ($weekres['profit_week']>52) {
            $weekres['profit_week']=52;
        }
        $paceweekkf=52/$weekres['profit_week'];
        $current_weeknum=$weekres['profit_week'];
        $prev_year=$year-1;
        if ($current_weeknum==52) {
            $prev_weeknum=1;
            $prev_year=$prev_year+1;
        } else {
            $prev_weeknum=$current_weeknum+1;
        }
        // Select date
        $this->db->select('profit_id,datebgn');
        $this->db->from('netprofit');
        $this->db->where('profit_week', $prev_weeknum);
        $this->db->where('profit_year', $prev_year);
        $paceres=$this->db->get()->row_array();
        if (isset($paceres['profit_id'])) {
            $pacedatstart=$paceres['datebgn'];
        } else {
            // Exclude
            $pacedatstart=strtotime(date("Y-m-d", $now) . " -1year -7days");
        }

        $this->db->select('sum(o.profit) as gross_profit', FALSE);
        $this->db->from('ts_orders o');
        $this->db->where('o.is_canceled',0);
        $this->db->where('date_format(from_unixtime(o.order_date),\'%x\')', $year);
        $this->db->where('o.order_date < ', $now);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('brand', ['BT','SB']);
            } else {
                $this->db->where('brand', $brand);
            }
        }
        $ordersres=$this->db->get()->row_array();
        if ($paceincome==2) {
            // Prev Year income
            $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as gross_profit');
            $this->db->from('ts_orders o');
            $this->db->where('o.is_canceled',0);
            $this->db->where('o.order_date >= ', $pacedatstart);
            $this->db->where('o.order_date < ', $now);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('brand', ['BT','SB']);
                } else {
                    $this->db->where('brand', $brand);
                }
            }
            $paceordat=$this->db->get()->row_array();
            $grossprofit=round(floatval($paceordat['gross_profit']),0);
        } else {
            $grossprofit=floatval($ordersres['gross_profit'])*$paceweekkf;
        }
        if ($paceexpense==1) {
            $cur_expenses=0;
            $this->db->select('sum(np.profit_operating) as operating');
            $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
            $this->db->select('sum(np.profit_projects) as odesk, sum(np.profit_purchases) as purchases');
            $this->db->from('netprofit_dat np');
            $this->db->join('netprofit nd', 'nd.profit_id=np.profit_id');
            $this->db->where('nd.profit_week is not NULL');
            $this->db->where('nd.profit_year', $year);
            $this->db->where('nd.dateend < ', $now);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('np.brand', ['BT','SB']);
                } else {
                    $this->db->where('np.brand', $brand);
                }
            }
            $curyear_netdata=$this->db->get()->row_array();
            foreach ($curyear_netdata as $row) {
                $cur_expenses+=floatval($row);
            }
            $expensive=$cur_expenses*$paceweekkf;
        } else {
            // Curren Year Espenses
            $this->db->select('sum(np.profit_operating) as operating');
            $this->db->select('sum(np.profit_payroll) as payroll, sum(np.profit_advertising) as advertising');
            $this->db->select('sum(np.profit_projects) as projects, sum(np.profit_purchases) as purchases');
            $this->db->from('netprofit_dat np');
            $this->db->join('netprofit nd','nd.profit_id=np.profit_id');
            $this->db->where('nd.datebgn >= ', $pacedatstart);
            $this->db->where('nd.dateend < ', $now);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('np.brand', ['BT','SB']);
                } else {
                    $this->db->where('np.brand', $brand);
                }
            }
            $curdebtres=$this->db->get()->row_array();
            $expensive=0;
            foreach ($curdebtres as $row) {
                $expensive+=floatval($row);
            }
        }
        $netprofit=$grossprofit-$expensive;
        return $netprofit;
    }

//    public function get_profit_purchase($profit_id,$type) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Profit Data Not Found');
//        $this->db->select('profit_id, weeknote, datebgn, dateend, profit_purchases');
//        $this->db->from('netprofit');
//        $this->db->where('profit_id', $profit_id);
//        $res=$this->db->get()->row_array();
//        if (isset($res['profit_id'])) {
//            $out['result']=$this->success_result;
//            $out['weeknote']=$res['weeknote'];
//            $out['datebgn']=$res['datebgn'];
//            $out['dateend']=$res['dateend'];
//            $out['profit_purchases']=floatval($res['profit_purchases']);
//            // Get Purchase Details
//            $this->db->select('c.category_name, d.*');
//            $this->db->from('ts_netprofit_details d');
//            $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id');
//            $this->db->where('profit_id', $profit_id);
//            $this->db->where('details_type','Purchase');
//            $purch=$this->db->get()->result_array();
//            $out['details']=$purch;
//            if (count($purch)==0) {
//                $out['profit_purchases']=0;
//            }
//        }
//        return $out;
//    }
//    // Edit session data of purchases
//    public function change_purchase_data($purchdata,$data, $session_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
//        if (array_key_exists('field',$data) && array_key_exists('newval', $data)) {
//            $purchdata[$data['field']]=$data['newval'];
//            // Save session
//            $this->func->session($session_id, $purchdata);
//            $out['result']=$this->success_result;
//        }
//        return $out;
//    }
    // Add new details
    public function purchase_details_add($netprofitdata, $session_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
        $details=$netprofitdata['purchase_details'];
        $profit_id=$netprofitdata['profit_id'];
        $minid=0;
        foreach ($details as $row) {
            if ($row['netprofit_detail_id']<$minid) {
                $minid=$row['netprofit_detail_id'];
            }
        }
        $minid=$minid-1;
        // Add row
        $details[]=array(
            'netprofit_detail_id'=>$minid,
            'profit_id'=>$profit_id,
            'netprofit_category_id'=>'',
            'category_name'=>'',
            'amount'=>'',
            'vendor'=>'',
            'description'=>'',
        );
        // Save to new session
        $netprofitdata['purchase_details']=$details;
        usersession($session_id, $netprofitdata);
        $out['result']=$this->success_result;
        return $out;
    }

    // edit Prurchase details
    public function purchase_details_edit($netprofitdata, $data, $session_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
        if ($data['category_type']=='Purchase') {
            $details=$netprofitdata['purchase_details'];
        } else {
            $details=$netprofitdata['w9work_details'];
        }
        $idx=0; $found=0;
        foreach ($details as $drow) {
            if ($drow['netprofit_detail_id']==$data['detail_id']) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==1) {
            $fld=$data['fldname'];
            $newval=$data['newval'];
            if ($fld=='amount') {
                $newval=floatval(str_replace(array('$',','), '', $newval));
            }
            $details[$idx][$fld]=$newval;
            if ($data['category_type']=='Purchase') {
                $netprofitdata['purchase_details']=$details;
            } else {
                $netprofitdata['w9work_details']=$details;
            }
            usersession($session_id, $netprofitdata);
            $out['result']=$this->success_result;
        }
        return $out;
    }

//    // Search purchase category
//    public function search_network_category($search, $type) {
//        $this->db->select('netprofit_category_id as data, category_name as value');
//        $this->db->from('ts_netprofit_categories');
//        $this->db->where('category_type', $type);
//        $this->db->like('upper(category_name)', strtoupper($search));
//        $this->db->order_by('category_name');
//        $res=$this->db->get()->result_array();
//        return $res;
//    }

    public function purchase_details_remove($netprofitdata, $category_type, $detail_id, $session_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
        if ($category_type=='Purchase') {
            $details=$netprofitdata['purchase_details'];
        } else {
            $details=$netprofitdata['w9work_details'];
        }
        $delrecords=$netprofitdata['delrecords'];
        $newdetail=array();
        $found=0;
        foreach ($details as $drow) {
            if ($drow['netprofit_detail_id']==$detail_id) {
                if ($drow['netprofit_detail_id']>0) {
                    $delrecords[]=$drow['netprofit_detail_id'];
                }
                $found=1;
            } else {
                $newdetail[]=$drow;
            }
        }
        if ($found==1) {
            if ($category_type=='Purchase') {
                $netprofitdata['purchase_details']=$newdetail;
            } else {
                $netprofitdata['w9work_details']=$newdetail;
            }
            $netprofitdata['delrecords']=$delrecords;
            usersession($session_id, $netprofitdata);
            $out['result']=$this->success_result;
        }
        return $out;
    }

//    public function purchase_details_save($purchdata,$netdetails, $session_id, $mainsession) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
//        // Check a data???
//        $profit_id=$purchdata['profit_id'];
//        $totals=0;
//        $delrecords=$purchdata['delrecords'];
//        $detalsdelrecs=$netdetails['delrecords'];
//        foreach ($delrecords as $row) {
//            $detalsdelrecs[]=$row;
//        }
//        $details=$purchdata['details'];
//        $netdetails['purchase_details']=$details;
//        foreach ($details as $drow) {
//            $totals+=floatval($drow['amount']);
//        }
//        // Remove details
//        $netprofit=$netdetails['netprofit'];
//        $netprofit['weeknote']=$purchdata['weeknote'];
//        $netprofit['profit_purchases']=$totals;
//        $out['result']=$this->success_result;
//        $this->func->session($session_id, NULL);
//        $netdetails['netprofit']=$netprofit;
//        $netdetails['delrecords']=$detalsdelrecs;
//        $this->func->session($mainsession, $netdetails);
//        return $out;
//    }

    private function _purchase_details_save($details, $profit_id, $brand) {
        foreach ($details as $drow) {
            if (empty($drow['netprofit_category_id'])) {
                $drow['netprofit_category_id']=NULL;
            } else {
                if ($drow['netprofit_category_id']<0) {
                    // Check that this category unique
                    $this->db->select('netprofit_category_id, count(netprofit_category_id) as cnt');
                    $this->db->from('ts_netprofit_categories');
                    $this->db->where('category_type','Purchase');
                    $this->db->where('upper(category_name)', strtoupper($drow['category_name']));
                    $chkres=$this->db->get()->row_array();
                    if ($chkres['cnt']>0) {
                        $drow['netprofit_category_id']=$chkres['netprofit_category_id'];
                    } else {
                        $this->db->set('category_type','Purchase');
                        $this->db->set('category_name',$drow['category_name']);
                        $this->db->insert('ts_netprofit_categories');
                        $drow['netprofit_category_id']=$this->db->insert_id();
                    }
                }
            }
            // Insert / update Purchase details
            $this->db->set('netprofit_category_id', $drow['netprofit_category_id']);
            $this->db->set('amount', floatval($drow['amount']));
            $this->db->set('vendor', $drow['vendor']);
            $this->db->set('description', $drow['description']);
            if ($drow['netprofit_detail_id']<0) {
                $this->db->set('profit_id', $profit_id);
                $this->db->set('details_type', 'Purchase');
                $this->db->set('brand', $brand);
                $this->db->insert('ts_netprofit_details');
            } else {
                $this->db->where('netprofit_detail_id', $drow['netprofit_detail_id']);
                $this->db->update('ts_netprofit_details');
            }
        }
        return TRUE;
    }

//    // Get W9 Work
//    public function get_profit_w9work($profit_id,$type) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Profit Data Not Found');
//        $this->db->select('profit_id, weeknote, datebgn, dateend, profit_w9');
//        $this->db->from('netprofit');
//        $this->db->where('profit_id', $profit_id);
//        $res=$this->db->get()->row_array();
//        if (isset($res['profit_id'])) {
//            $out['result']=$this->success_result;
//            $out['datebgn']=$res['datebgn'];
//            $out['dateend']=$res['dateend'];
//            $out['profit_w9']=floatval($res['profit_w9']);
//            // Get Purchase Details
//            $this->db->select('c.category_name, d.*');
//            $this->db->from('ts_netprofit_details d');
//            $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id');
//            $this->db->where('d.profit_id', $profit_id);
//            $this->db->where('d.details_type','W9');
//            $details=$this->db->get()->result_array();
//            $out['details']=$details;
//            if (count($details)==0) {
//                $out['profit_w9']=0;
//            }
//        }
//        return $out;
//    }

    public function w9work_details_add($netprofitdata, $session_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
        $profit_id=$netprofitdata['profit_id'];
        $details=$netprofitdata['w9work_details'];
        $minid=0;
        foreach ($details as $row) {
            if ($row['netprofit_detail_id']<$minid) {
                $minid=$row['netprofit_detail_id'];
            }
        }
        $minid=$minid-1;
        $w9work_categories=$this->get_profit_categories('W9');
        // Add row
        $details[]=array(
            'netprofit_detail_id'=>$minid,
            'profit_id'=>$profit_id,
            'netprofit_category_id'=>$w9work_categories[0]['netprofit_category_id'],
            'category_name'=>$w9work_categories[0]['category_name'],
            'amount'=>'',
            'vendor'=>'',
            'description'=>'',
        );
        // Save to new session
        $netprofitdata['w9work_details']=$details;
        usersession($session_id, $netprofitdata);
        $out['result']=$this->success_result;
        return $out;
    }

//    public function w9work_details_edit($workdata, $data, $session_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
//        $details=$workdata['details'];
//        $idx=0; $found=0;
//        foreach ($details as $drow) {
//            if ($drow['netprofit_detail_id']==$data['detail_id']) {
//                $found=1;
//                break;
//            } else {
//                $idx++;
//            }
//        }
//        if ($found==1) {
//            $fld=$data['fldname'];
//            $newval=$data['newval'];
//            if ($fld=='category_name') {
//                if (empty($newval)) {
//                    $details[$idx]['netprofit_category_id']='';
//                    $details[$idx]['category_name']='';
//                } else {
//                    // Search a new value
//                    $this->db->select('netprofit_category_id, count(netprofit_category_id) as cnt');
//                    $this->db->from('ts_netprofit_categories');
//                    $this->db->where('category_type','W9');
//                    $this->db->where('upper(category_name)', strtoupper($newval));
//                    $res=$this->db->get()->row_array();
//
//                    if ($res['cnt']>0) {
//                        $details[$idx]['netprofit_category_id']=$res['netprofit_category_id'];
//                    } else {
//                        $details[$idx]['netprofit_category_id']='-1';
//                    }
//                    $details[$idx]['category_name']=$newval;
//                }
//            } else {
//                $details[$idx][$fld]=$newval;
//            }
//            $workdata['details']=$details;
//            $this->func->session($session_id, $workdata);
//            $out['result']=$this->success_result;
//        }
//        return $out;
//    }
//
//    public function w9work_details_remove($workdata, $detail_id, $session_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
//        $details=$workdata['details'];
//        $delrecords=$workdata['delrecords'];
//        $newdetail=array();
//        $found=0;
//        foreach ($details as $drow) {
//            if ($drow['netprofit_detail_id']==$detail_id) {
//                if ($drow['netprofit_detail_id']>0) {
//                    $delrecords[]=$drow['netprofit_detail_id'];
//                }
//                $found=1;
//            } else {
//                $newdetail[]=$drow;
//            }
//        }
//        if ($found==1) {
//            $workdata['details']=$newdetail;
//            $workdata['delrecords']=$delrecords;
//            $this->func->session($session_id, $workdata);
//            $out['result']=$this->success_result;
//        }
//        return $out;
//    }
//
//    public function w9work_details_save($workdata,$netdetails, $session_id, $mainsession) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
//        // Check a data???
//        $profit_id=$workdata['profit_id'];
//        $totals=0;
//        $delrecords=$workdata['delrecords'];
//        $detalsdelrecs=$netdetails['delrecords'];
//        foreach ($delrecords as $row) {
//            $detalsdelrecs[]=$row;
//        }
//        $details=$workdata['details'];
//        $netdetails['w9work_details']=$details;
//        foreach ($details as $drow) {
//            $totals+=floatval($drow['amount']);
//        }
//        // Remove details
//        $netprofit=$netdetails['netprofit'];
//        $netprofit['profit_w9']=$totals;
//        $out['result']=$this->success_result;
//        $this->func->session($session_id, NULL);
//        $netdetails['netprofit']=$netprofit;
//        $netdetails['delrecords']=$detalsdelrecs;
//        $this->func->session($mainsession, $netdetails);
//        return $out;
//    }

    private function _w9work_details_save($details, $profit_id, $brand) {
        foreach ($details as $drow) {
            if (empty($drow['netprofit_category_id'])) {
                $drow['netprofit_category_id']=NULL;
            } else {
                if ($drow['netprofit_category_id']<0) {
                    // Check that this category unique
                    $this->db->select('netprofit_category_id, count(netprofit_category_id) as cnt');
                    $this->db->from('ts_netprofit_categories');
                    $this->db->where('category_type','W9');
                    $this->db->where('upper(category_name)', strtoupper($drow['category_name']));
                    $chkres=$this->db->get()->row_array();
                    if ($chkres['cnt']>0) {
                        $drow['netprofit_category_id']=$chkres['netprofit_category_id'];
                    } else {
                        $this->db->set('category_type','W9');
                        $this->db->set('category_name',$drow['category_name']);
                        $this->db->insert('ts_netprofit_categories');
                        $drow['netprofit_category_id']=$this->db->insert_id();
                    }
                }
            }
            // Insert / update Purchase details
            $this->db->set('netprofit_category_id', $drow['netprofit_category_id']);
            $this->db->set('amount', floatval($drow['amount']));
            $this->db->set('vendor', $drow['vendor']);
            $this->db->set('description', $drow['description']);
            if ($drow['netprofit_detail_id']<0) {
                $this->db->set('profit_id', $profit_id);
                $this->db->set('details_type', 'W9');
                $this->db->set('brand', $brand);
                $this->db->insert('ts_netprofit_details');
            } else {
                $this->db->where('netprofit_detail_id', $drow['netprofit_detail_id']);
                $this->db->update('ts_netprofit_details');
            }
        }
        return TRUE;
    }

    public function netprofit_details_save($netprofitdata, $usrid, $session_id, $brand) {
        $out=array('result'=>$this->error_result, 'msg'=>'Record Not Found');
        $profit_id=$netprofitdata['profit_id'];
        // Get Old Data
        $netprofit=$netprofitdata['netprofit'];
        $type=$netprofitdata['type'];
        if ($type=='week' && $netprofit['debtinclude']==1) {
            $week_id=$netprofit['profit_year'].'-'.$netprofit['profit_week'];
            $olddat=$this->get_netprofit($week_id,'week', $brand);
            $total_options=array(
                'type'=>'week',
                'start'=>$this->config->item('netprofit_start'),
                'brand' => $brand,
            );
            $rundat=$this->get_netprofit_runs($total_options);
            $oldrundebt=$rundat['out_debtval'];
        }
        // Save Purchase Details
        $purchase_details=$netprofitdata['purchase_details'];
        $this->_purchase_details_save($purchase_details, $profit_id, $brand);
        $purchase_total=0;
        foreach ($purchase_details as $trow) {
            $purchase_total+=floatval($trow['amount']);
        }
        $netprofit['profit_purchases']=$purchase_total;
        // Save W9 Work
        $w9work_details=$netprofitdata['w9work_details'];
        $this->_w9work_details_save($w9work_details, $profit_id, $brand);
        $w9work_total=0;
        foreach ($w9work_details as $wrow) {
            $w9work_total+=floatval($wrow['amount']);
        }
        $netprofit['profit_w9']=$w9work_total;
        // Save
        $this->db->set('profit_operating',(floatval($netprofit['profit_operating'])==0 ? NULL : floatval($netprofit['profit_operating'])));
        $this->db->set('profit_payroll',(floatval($netprofit['profit_payroll'])==0 ? NULL : floatval($netprofit['profit_payroll'])));
        $this->db->set('profit_advertising',(floatval($netprofit['profit_advertising'])==0 ? NULL : floatval($netprofit['profit_advertising'])));
        $this->db->set('profit_projects',(floatval($netprofit['profit_projects'])==0 ? NULL : floatval($netprofit['profit_projects'])));
        $this->db->set('profit_purchases',(floatval($netprofit['profit_purchases'])==0 ? NULL : floatval($netprofit['profit_purchases'])));
        $this->db->set('profit_w9', (floatval($netprofit['profit_w9'])==0 ? NULL : floatval($netprofit['profit_w9'])));
        // $this->db->set('profit_debt',(floatval($netprofit['profit_debt'])==0 ? NULL : floatval($netprofit['profit_debt'])));
        $this->db->set('profit_owners',(floatval($netprofit['profit_owners'])==0 ? NULL : floatval($netprofit['profit_owners'])));
        $this->db->set('od2',(floatval($netprofit['od2'])==0 ? NULL : floatval($netprofit['od2'])));
        $this->db->set('profit_saved',(floatval($netprofit['profit_saved'])==0 ? NULL : floatval($netprofit['profit_saved'])));
        $this->db->set('debtinclude',intval($netprofit['debtinclude']));
        $this->db->set('weeknote', $netprofit['weeknote']);
        $this->db->where('profit_id', $profit_id);
        $this->db->where('brand', $brand);
        $this->db->update('netprofit_dat');
        $this->db->set('update_user',$usrid);
        $this->db->where('profit_id', $profit_id);
        $this->db->update('netprofit');
        $out['result']=$this->success_result;
        $delrecords=$netprofitdata['delrecords'];
        foreach ($delrecords as $drow) {
            $this->db->where('netprofit_detail_id', $drow);
            $this->db->delete('ts_netprofit_details');
        }
        usersession($session_id, NULL);
        if ($type=='week' && $netprofit['debtinclude']==1) {
            $newdat=$this->get_netprofit($week_id,'week', $brand);
            if ($newdat['profit_saved']!=$olddat['profit_saved'] || $newdat['profit_owners']!=$olddat['profit_owners'] || $newdat['od2']!=$olddat['od2']) {
                $total_options=array(
                    'type'=>'week',
                    'start'=>$this->config->item('netprofit_start'),
                    'brand' => $brand,
                );
                $rundat=$this->get_netprofit_runs($total_options);
                $newrundebt=$rundat['out_debtval'];
                if ($newrundebt<0) {
                    $outnewrundebt='($'.number_format(abs(newrundebt),0,'.',',');
                } else {
                    $outnewrundebt='$'.number_format($newrundebt,0,'.',',');
                }
                if ($oldrundebt<0) {
                    $outoldrundebt='$('.number_format(abs($oldrundebt),'0','.',',').')';
                } else {
                    $outoldrundebt='$'.number_format($oldrundebt,'0','.',',');
                }

                $this->load->model('orders_model');
                $weeknum=$olddat['week'];
                if (date('Y',$newdat['dateend'])==date('Y',$newdat['datebgn'])) {
                    $weeknum.=', '.date('Y',$newdat['datebgn']);
                } else {
                    $weeknum.=', '.date('Y',$newdat['datebgn']).'/'.date('y',$newdat['dateend']);
                }
                $noteoptions=array(
                    'netproofdebt'=>1,
                    'user_id'=>$usrid,
                    'olddebt'=>$olddat['out_debt'],
                    'newdebt'=>$newdat['out_debt'],
                    'weeknum'=>$weeknum,
                    'newtotalrun'=>$outnewrundebt,
                    'oldtotalrun'=>$outoldrundebt,
                );
                //
                if ($newdat['profit_saved']!=$olddat['profit_saved']) {
                    $noteoptions['profit_saved']=array(
                        'old'=>$olddat['profit_saved'],
                        'new'=>$newdat['profit_saved'],
                    );
                }
                if ($newdat['profit_owners']!=$olddat['profit_owners']) {
                    $noteoptions['profit_owners']=array(
                        'old'=>$olddat['profit_owners'],
                        'new'=>$newdat['profit_owners'],
                    );
                }
                if ($newdat['od2']!=$olddat['od2']) {
                    $noteoptions['od2']=array(
                        'old'=>$olddat['od2'],
                        'new'=>$newdat['od2'],
                    );
                }
                $this->orders_model->notify_netdebtchanged($noteoptions);
            }
        }
        $out['refresh']=0;
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);

        if ($type=='week' && $netprofit['dateend']<$now) {
            $out['refresh']=1;
        }
        return $out;
    }

    public function get_w9purchase_tabledata($brand) {
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);

        $year=date('Y', $now);
        $years=array();
        $years[]=array(
            'year'=>$year,
            'current'=>1,
        );
        // W9 Work
        $w9array=$this->get_w9yeardetails($year, $brand);
        $purcharray=$this->get_purchaseyeardetails($year, $brand);
        $totals=$w9array['totals']+$purcharray['totals'];
        // Get a list of years
        $this->db->select('distinct(n.profit_year) as year');
        $this->db->from('netprofit n');
        $this->db->join('ts_netprofit_details d','d.profit_id=n.profit_id');
        $this->db->where('profit_year != ', $year);
        $this->db->order_by('profit_year','desc');
        $yearres=$this->db->get()->result_array();
        foreach ($yearres as $yrow) {
            $years[]=array(
                'year'=>$yrow['year'],
                'current'=>0,
            );
        }
        $out=array(
            'years'=>$years,
            'totals'=>$totals,
            'w9totals'=>$w9array['totals'],
            'w9details'=>$w9array['details'],
            'purchasetotals'=>$purcharray['totals'],
            'purchasedetails'=>$purcharray['details'],
        );
        return $out;
    }

    public function get_w9yeardetails($year, $brand, $w9sortfld='amount_perc', $w9sortdir='desc') {
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);
        // Totals
        $this->db->select('sum(d.amount) amount');
        $this->db->from('ts_netprofit_details d');
        $this->db->join('netprofit n','n.profit_id=d.profit_id');
        $this->db->where('n.profit_week is not null');
        $this->db->where('d.details_type','W9');
        $this->db->where('n.dateend < ', $now);
        $this->db->where('n.profit_year', $year);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('d.brand', ['BT','SB']);
            } else {
                $this->db->where('d.brand', $brand);
            }
        }
        $totaldata=$this->db->get()->row_array();
        $totals=  floatval($totaldata['amount']);

        $this->db->select('coalesce(c.category_name,\'Unclassified\') as category_name, d.netprofit_category_id, sum(d.amount) amount',FALSE);
        // percent
        if ($totals==0) {
            $this->db->select("0 as amount_perc", FALSE);
        } else {
            $this->db->select("round(sum(d.amount)/{$totals}*100,1) as amount_perc",FALSE);
        }
        $this->db->from('ts_netprofit_details d');
        $this->db->join('ts_netprofit_categories c', 'c.netprofit_category_id=d.netprofit_category_id','left');
        $this->db->join('netprofit n','n.profit_id=d.profit_id');
        $this->db->where('n.profit_week is not null');
        $this->db->where('d.details_type','W9');
        $this->db->where('n.dateend < ', $now);
        $this->db->where('n.profit_year', $year);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('d.brand', ['BT','SB']);
            } else {
                $this->db->where('d.brand', $brand);
            }
        }
        $this->db->group_by('d.netprofit_category_id');
        $this->db->order_by($w9sortfld, $w9sortdir);
        $data=$this->db->get()->result_array();

        $out=array();
        foreach ($data as $row) {
            $row['amount_class']='';
            $row['amout_out']=$this->EMPTY_PROFIT;
            if ($row['amount']>0) {
                $row['amount_out']=MoneyOutput($row['amount'],2);
            } elseif ($row['amount']<0) {
                $row['amount_class']='color_red2';
                $row['amount_out']='('.MoneyOutput(abs($row['amount']),2).')';
            }
            $out[]=$row;
        }
        return array('totals'=>$totals, 'details'=>$out);
    }

    public function get_purchaseyeardetails($year, $brand, $purchasesortfld='amount_perc', $purchasesortdir='desc') {
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);

        $this->db->select('sum(d.amount) amount');
        $this->db->from('ts_netprofit_details d');
        $this->db->join('netprofit n','n.profit_id=d.profit_id');
        $this->db->where('n.profit_week is not null');
        $this->db->where('d.details_type','Purchase');
        $this->db->where('n.dateend < ', $now);
        $this->db->where('n.profit_year', $year);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('d.brand', ['BT','SB']);
            } else {
                $this->db->where('d.brand', $brand);
            }
        }
        $totaldata=$this->db->get()->row_array();
        $totals=  floatval($totaldata['amount']);

        $this->db->select('coalesce(c.category_name,\'Unclassified\') as category_name, d.netprofit_category_id, sum(d.amount) amount',FALSE);
        // percent
        if ($totals==0) {
            $this->db->select("0 as amount_perc", FALSE);
        } else {
            $this->db->select("round(sum(d.amount)/{$totals}*100,1) as amount_perc",FALSE);
        }
        $this->db->from('ts_netprofit_details d');
        $this->db->join('ts_netprofit_categories c', 'c.netprofit_category_id=d.netprofit_category_id','left');
        $this->db->join('netprofit n','n.profit_id=d.profit_id');
        $this->db->where('d.details_type','Purchase');
        $this->db->where('n.profit_week is not null');
        $this->db->where('n.dateend < ', $now);
        $this->db->where('n.profit_year', $year);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('d.brand', ['BT','SB']);
            } else {
                $this->db->where('d.brand', $brand);
            }
        }
        $this->db->group_by('d.netprofit_category_id');
        $this->db->order_by($purchasesortfld, $purchasesortdir);
        $data=$this->db->get()->result_array();

        $out=array();
        foreach ($data as $row) {
            $row['amount_class']='';
            $row['amout_out']=$this->EMPTY_PROFIT;
            if ($row['amount']>0) {
                $row['amount_out']=MoneyOutput($row['amount'],2);
            } elseif ($row['amount']<0) {
                $row['amount_class']='color_red2';
                $row['amount_out']='('.MoneyOutput(abs($row['amount']),2).')';
            }
            $out[]=$row;
        }
        return array('totals'=>$totals, 'details'=>$out);
    }

    public function get_profit_categories($category, $unsign=1) {
        $this->db->select('netprofit_category_id, category_name');
        $this->db->from('ts_netprofit_categories');
        $this->db->where('category_type',$category);
        $this->db->order_by('category_name');
        $res=$this->db->get()->result_array();
        $out=array();
        if ($category=='Purchase' && $unsign==1) {
            $out[]=array(
                'netprofit_category_id'=>'',
                'category_name'=>'Unclassified',
            );
        }
        foreach ($res as $row) {
            $out[]=array(
                'netprofit_category_id'=>$row['netprofit_category_id'],
                'category_name'=>$row['category_name'],
            );
        }
        return $out;
    }

    public function netprofit_newcategory($sessiondata,$data, $session_id='') {
        $out=array('result'=>$this->error_result, 'msg'=>'Empty Category');
        // Check a new category
        $category_name=$data['category'];
        if (!empty($category_name)) {
            $out['msg']=
            // Select a new category
            $this->db->select('count(*) as cnt');
            $this->db->from('ts_netprofit_categories');
            $this->db->where('upper(category_name)', strtoupper($category_name));
            $this->db->where('category_type', $data['category_type']);
            $res=$this->db->get()->row_array();
            if ($res['cnt']==0) {
                $out['result']=$this->success_result;
                $this->db->set('category_name', $category_name);
                $this->db->set('category_type', $data['category_type']);
                $this->db->insert('ts_netprofit_categories');
                $newid=$this->db->insert_id();
                if (!empty($session_id)) {
                    // Search details
                    if ($data['category_type']=='Purchase') {
                        $details=$sessiondata['purchase_details'];
                    } else {
                        $details=$sessiondata['w9work_details'];
                    }
                    $idx=0;
                    foreach ($details as $drow) {
                        if ($drow['netprofit_detail_id']==$data['detail']) {
                            $details[$idx]['netprofit_category_id']=$newid;
                            break;
                        } else {
                            $idx++;
                        }
                    }
                    if ($data['category_type']=='Purchase') {
                        $sessiondata['purchase_details']=$details;
                    } else {
                        $sessiondata['w9work_details']=$details;
                    }
                    usersession($session_id, $sessiondata);
                }
            }
        }
        return $out;
    }

//    public function get_profit_category($category_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Category Not Found');
//        $this->db->select('*');
//        $this->db->from('ts_netprofit_categories');
//        $this->db->where('netprofit_category_id', $category_id);
//        $res=$this->db->get()->row_array();
//        if (isset($res['netprofit_category_id'])) {
//            $out['result']=$this->success_result;
//            $out['data']=$res;
//        }
//        return $out;
//    }
//
//    public function save_profit_category($data) {
//        $out=array('result'=>$this->error_result, 'msg'=>'Category Empty');
//        $category_id=$data['category_id'];
//        $category_type=$data['category_type'];
//        $category_name=$data['category_name'];
//        if (!empty($category_name)) {
//            $out['msg']='Category Not Unique';
//            $this->db->select('count(*) as cnt');
//            $this->db->from('ts_netprofit_categories');
//            $this->db->where('upper(category_name)', strtoupper($category_name));
//            $this->db->where('category_type', $category_type);
//            $this->db->where('netprofit_category_id != ', $category_id);
//            $chkres=$this->db->get()->row_array();
//            if ($chkres['cnt']==0) {
//                $out['result']=$this->success_result;
//                $this->db->set('category_name', $category_name);
//                if ($category_id<0) {
//                    $this->db->set('category_type', $category_type);
//                    $this->db->insert('ts_netprofit_categories');
//                } else {
//                    $this->db->where('netprofit_category_id', $category_id);
//                    $this->db->update('ts_netprofit_categories');
//                }
//            }
//        }
//        return $out;
//    }

    public function get_netprofit_purchasedetails($profit_id, $brand) {
        $out=array('result'=>$this->error_result, $msg='Empty Purchase Details');
        // Get count
        $this->db->select('count(d.netprofit_detail_id) as cnt');
        $this->db->from('ts_netprofit_details d');
        $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id');
        $this->db->where('d.profit_id', $profit_id);
        if ($brand!=='ALL') {
            if ($brand=='SB') {
                $this->db->where_in('d.brand', ['BT','SB']);
            } else {
                $this->db->where('d.brand', $brand);
            }
        }
        $res=$this->db->get()->row_array();
        if ($res['cnt']>0) {
            $this->db->select('c.category_name, d.vendor, d.description, sum(d.amount) as amount');
            $this->db->from('ts_netprofit_details d');
            $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id','left');
            $this->db->where('d.profit_id', $profit_id);
            $this->db->where('d.details_type','W9');
            if ($brand!='ALL') {
                $this->db->where('d.brand', $brand);
            }
            $this->db->group_by('c.category_name, d.vendor, d.description');
            $resw9=$this->db->get()->result_array();
            $w9out=array();
            foreach ($resw9 as $row) {
                $amount_class='';
                $amount=$this->EMPTY_PROFIT;
                if ($row['amount']<0) {
                    $amount_class='color_red2';
                    $amount='('.MoneyOutput(abs($row['amount']),2).')';
                } elseif ($row['amount']>0) {
                    $amount=MoneyOutput($row['amount'],2);
                }
                $row['amount_class']=$amount_class;
                $row['amount_out']=$amount;
                $w9out[]=$row;
            }
            $out['w9work']=$w9out;
            $this->db->select('c.category_name, d.vendor, d.description, sum(d.amount) as amount');
            $this->db->from('ts_netprofit_details d');
            $this->db->join('ts_netprofit_categories c','c.netprofit_category_id=d.netprofit_category_id','left');
            $this->db->where('d.profit_id', $profit_id);
            $this->db->where('d.details_type','Purchase');
            if ($brand!='ALL') {
                $this->db->where('d.brand', $brand);
            }
            $this->db->group_by('c.category_name, d.vendor, d.description');
            $purchres=$this->db->get()->result_array();
            $purchout=array();
            foreach ($purchres as $row) {
                $amount_class='';
                $amount=$this->EMPTY_PROFIT;
                if ($row['amount']<0) {
                    $amount_class='color_red2';
                    $amount='('.MoneyOutput(abs($row['amount']),2).')';
                } elseif ($row['amount']>0) {
                    $amount=MoneyOutput($row['amount'],2);
                }
                $row['amount_class']=$amount_class;
                $row['amount_out']=$amount;
                $purchout[]=$row;
            }
            $out['purchase']=$purchout;
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function  get_expenses_details($category, $year, $brand) {
        $out=array('result'=>$this->error_result,'msg'=>'Category Not Exist');
        // $now=strtotime('monday this week');
        $now=getDayOfWeek(date('W'), date('Y'),1);

        $category_name='';
        if (empty($category)) {
            $category_name='Unclassified';
            $type='Purchase';
        } else {
            $this->db->select('category_type, category_name');
            $this->db->from('ts_netprofit_categories');
            $this->db->where('netprofit_category_id', $category);
            $catres=$this->db->get()->row_array();
            if (isset($catres['category_name'])) {
                $category_name=$catres['category_name'];
                $type=$catres['category_type'];
            }
        }
        if (!empty($category_name)) {
            $out['result']=$this->success_result;
            $out['category']=$category_name;
            $out['type']=$type;
            // Get details
            $this->db->select('n.datebgn, n.dateend, d.vendor, d.description, sum(d.amount) amount');
            $this->db->from('ts_netprofit_details d');
            $this->db->join('netprofit n', 'n.profit_id=d.profit_id');
            $this->db->where('n.profit_week is not null');
            $this->db->where('dateend < ', $now);
            $this->db->where('profit_year', $year);
            if (empty($category)) {
                $this->db->where('d.netprofit_category_id is null');
            } else {
                $this->db->where('d.netprofit_category_id', $category);
            }
            $this->db->where('d.details_type', $type);
            if ($brand!=='ALL') {
                if ($brand=='SB') {
                    $this->db->where_in('d.brand', ['BT','SB']);
                } else {
                    $this->db->where('d.brand', $brand);
                }
            }
            $this->db->group_by('n.datebgn, n.dateend, d.vendor, d.description');
            $this->db->order_by('n.datebgn','desc');
            $res=$this->db->get()->result_array();
            $totals=0;
            $data=array();
            foreach ($res as $row) {
                $totals+=floatval($row['amount']);
                if (date('M',$row['datebgn'])!=date('M',$row['dateend'])) {
                    $weekname=date('M',$row['datebgn']).'/'.date('M',$row['dateend']);
                } else {
                    $weekname=date('M',$row['datebgn']);
                }
                $weekname.=' '.date('j',$row['datebgn']).'-'.date('j',$row['dateend']);
                $weekname.=','.date('Y', $row['datebgn']);
                $amount_class='';
                $amount_out=$this->EMPTY_PROFIT;
                if ($row['amount']>0) {
                    $amount_out=MoneyOutput($row['amount'],2);
                } elseif ($row['amount']<0) {
                    $amount_out='('.MoneyOutput(abs($row['amount']),2).')';
                    $amount_class='color_red2';
                }
                $data[]=array(
                    'week'=>$weekname,
                    'amount'=>$amount_out,
                    'amount_class'=>$amount_class,
                    'vendor'=>$row['vendor'],
                    'description'=>$row['description'],
                );
            }
            $out['data']=$data;
            $out['totals']=$totals;
        }
        return $out;
    }

}