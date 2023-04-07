<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of Batches_model
 *
 * @author german polovnikov
 */

class Batches_model extends My_Model
{
    private $INIT_ERRMSG='Unknown error. Try later';
    private $VISA='v';
    private $AMEX='a';
    private $OTHERPAY='o';
    private $TERMS='t';
    private $WRITE_OFF = 'w';
    private $paypal_apply='2013-01-28';
    private $manual_batch='<img src="/img/manual_batch.png" alt="Manual Batch"/>';


    function __construct()
    {
        parent::__construct();
    }

    public function get_batches_limits($brand) {
        $this->db->select('min(b.batch_due) as min_date, max(b.batch_due) as max_date');
        $this->db->from('ts_order_batches b');
        if ($brand!=='ALL') {
            $this->db->join('ts_orders o', 'o.order_id=b.order_id');
            $this->db->where('o.brand', $brand);
        }
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function get_calendar_view($options) {
        $this->db->select('batch_due, count(b.order_id) AS total_orders, sum(b.batch_vmd) AS inv_vmd, sum(b.batch_amex) AS inv_amex');
        $this->db->select('sum(b.batch_other) AS inv_other, sum(b.batch_term) AS inv_term, sum(b.batch_writeoff) as inv_writeoff,');
        $this->db->select('(-(1) * sum((b.batch_vmd * (b.batch_received - 1)))) AS deb_vmd, (-(1) * sum((b.batch_amex * (b.batch_received - 1)))) AS deb_amex');
        $this->db->select('(-(1) * sum((b.batch_other * (b.batch_received - 1)))) AS deb_other, (-(1) * sum((b.batch_term * (b.batch_received - 1)))) AS deb_term');
        $this->db->select('(-(1) * sum((b.batch_writeoff * (b.batch_received - 1)))) AS deb_writeoff');
        $this->db->select('sum((b.batch_vmd * b.batch_received)) AS rec_vmd, sum((b.batch_amex * b.batch_received)) AS rec_amex, sum((b.batch_other * b.batch_received)) AS rec_other');
        $this->db->select('sum((b.batch_term * b.batch_received)) AS rec_term, sum((b.batch_writeoff * b.batch_received)) AS rec_writeoff');
        $this->db->from('ts_order_batches b');
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o', 'o.order_id=b.order_id');
            $this->db->where('o.brand', $options['brand']);
        }
        $this->db->group_by('b.batch_due');
        $this->db->order_by('b.batch_due');
        $res=$this->db->get()->result_array();

        $dates=array();
        $dateres=array();
        foreach ($res as $row) {
            $newdat=strtotime(date('Y-m-d',$row['batch_due']));
            array_push($dates,$newdat);
            $row['out_vmdclass']=(floatval($row['deb_vmd'])<0 ? 'negative' : '');
            $sum=floatval($row['deb_vmd']);
            $row['out_vmd']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->VISA);
            // $row['out_vmd']=(floatval($row['deb_vmd'])==0 ? '&nbsp;' : '$'.  number_format($row['deb_vmd'],2,'.','').Batches_model::VISA);
            $row['out_amexclass']=(floatval($row['deb_amex'])<0 ? 'negative' : '');
            $sum=floatval($row['deb_amex']);
            $row['out_amex']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->AMEX);
            // $row['out_amex']=(floatval($row['deb_amex'])==0 ? '&nbsp;' : '$'.number_format($row['deb_amex'],2,'.','').Batches_model::AMEX);
            $row['out_otherclass']=(floatval($row['deb_other'])<0 ? 'negative' : '');
            $sum=floatval($row['deb_other']);
            $row['out_other']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->OTHERPAY);
            // $row['out_other']=(floatval($row['deb_other'])==0 ? '&nbsp;' : '$'.number_format($row['deb_other'],2,'.','').Batches_model::OTHERPAY);
            $row['out_termclass']=(floatval($row['deb_term'])<0 ? 'negative' : '');
            $sum=floatval($row['deb_term']);
            $row['out_term']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->TERMS);
            // $row['out_term']=(floatval($row['deb_term'])==0 ? '&nbsp;' : '$'.number_format($row['deb_term'],2,'.','').$this->TERMS);
            $row['out_writeoffclass']=(floatval($row['deb_writeoff'])<0 ? 'negative' : '');
            $sum=floatval($row['deb_writeoff']);
            $row['out_writeoff']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->WRITE_OFF);
            $dateres[]=$row;
        }
        $out=array();
        $start=$options['monday'];
        /* Number of weeks */
        $date_max=$options['max_date'];
        // $start=$date_max;
        $date_min=$options['min_date'];
        $weeks=dates_diff($date_min, $date_max, 'W');
        if ($weeks<5) {
            $weeks=5;
        } else {
            $weeks=$weeks+2;
        }
        /* Calendar dates */
        $calendar_id=$this->config->item('bank_calendar');
        /* get data about holidays */
        $this->db->select('line_date');
        $this->db->from('calendar_lines');
        $this->db->where('calendar_id',$calendar_id);
        $this->db->where('line_date < ', $date_max);
        $this->db->where('line_date >= ', $date_min);
        $hol=$this->db->get()->result_array();
        $holidays=array();
        foreach ($hol as $row) {
            $date=strtotime(date("Y-m-d", $row['line_date']));
            array_push($holidays,$date);
        }
        for ($i=0; $i<$weeks; $i++) {
            for($j=0;$j<7;$j++) {
                $date=strtotime(date("Y-m-d", $start) . " +{$j} day");
                $daytype='';
                if (in_array($date, $holidays)) {
                    $daytype='batchholiday';
                } else {
                    $weekday=date('N',$date);
                    $daytype=($weekday < 6 ? '' : 'batchholiday');
                }
                /* check date */
                if (in_array($date, $dates)) {
                    $key=  array_search($date, $dates);
                    $out[]=array(
                        'batch_date'=>$date,
                        'out_date'=>date('M j',$date),
                        'out_daytype'=>$daytype,
                        'out_vmd'=>$dateres[$key]['out_vmd'],
                        'out_vmdclass'=>$dateres[$key]['out_vmdclass'],
                        'out_amex'=>$dateres[$key]['out_amex'],
                        'out_amexclass'=>$dateres[$key]['out_amexclass'],
                        'out_other'=>$dateres[$key]['out_other'],
                        'out_otherclass'=>$dateres[$key]['out_otherclass'],
                        'out_term'=>$dateres[$key]['out_term'],
                        'out_termclass'=>$dateres[$key]['out_termclass'],
                        'out_writeoff'=>$dateres[$key]['out_writeoff'],
                        'out_writeofflass'=>$dateres[$key]['out_writeoffclass'],
                    );
                } else {
                    $out[]=array(
                        'batch_date'=>$date,
                        'out_date'=>date('M j',$date),
                        'out_daytype'=>$daytype,
                        'out_vmd'=>'&nbsp;',
                        'out_amex'=>'&nbsp;',
                        'out_other'=>'&nbsp;',
                        'out_term'=>'&nbsp;',
                        'out_vmdclass'=>'',
                        'out_amexclass'=>'',
                        'out_otherclass'=>'',
                        'out_termclass'=>'',
                        'out_writeoff'=>'',
                        'out_writeofflass'=>'',
                    );
                }
            }
            $start=strtotime(date("Y-m-d", $start) . " +1 week");
        }
        return $out;
    }

    public function get_calend_totals($options) {
        $empty='---';
        $this->db->select('count(b.order_id) as sumord, sum(b.batch_vmd) AS sum_inv_vmd, sum(b.batch_amex) AS sum_inv_amex, sum(b.batch_other) AS sum_inv_other');
        $this->db->select('sum(b.batch_term) AS sum_inv_term, sum(b.batch_writeoff) as sum_inv_writeoff');
        $this->db->select('(-(1) * sum((b.batch_vmd * (b.batch_received - 1)))) AS sum_deb_vmd, (-(1) * sum((b.batch_amex * (b.batch_received - 1)))) AS sum_deb_amex');
        $this->db->select('(-(1) * sum((b.batch_other * (b.batch_received - 1)))) AS sum_deb_other, (-(1) * sum((b.batch_term * (b.batch_received - 1)))) AS sum_deb_term');
        $this->db->select('(-(1) * sum((b.batch_writeoff * (b.batch_received - 1)))) AS sum_deb_writeoff');
        $this->db->from('ts_order_batches b');
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o', 'o.order_id=b.order_id');
            $this->db->where('o.brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();
        /* Total past */
        $date=date("Y-m-d");// current date
        $pastdate = strtotime(date("Y-m-d", strtotime($date)) . " +1 day");
        $this->db->select('sum(b.batch_amount) as sum');
        $this->db->from('ts_order_batches b');
        $this->db->where('b.batch_received',0);
        $this->db->where('b.batch_due <= ', $pastdate);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o', 'o.order_id=b.order_id');
            $this->db->where('o.brand', $options['brand']);
        }
        $past=$this->db->get()->row_array();
        $out=array();
        $sum=floatval($res['sum_deb_vmd']);
        $out['vmd_class']='';
        if ($sum<0) {
            $out['vmd_class']='batchnegative';
            $out['out_vmd']='($'.number_format(abs($sum),2,'.',',').')';
        } else {
            $out['out_vmd']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',','));
        }
        $sum=floatval($res['sum_deb_amex']);
        $out['amex_class']='';
        if ($sum<0) {
            $out['amex_class']='batchnegative';
            $out['out_vmd']='($'.number_format(abs($sum),2,'.',',').')';
        } else {
            $out['out_amex']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',','));
        }
        $sum=floatval($res['sum_deb_other']);
        $out['other_class']='';
        if ($sum<0 ) {
            $out['other_class']='batchnegative';
            $out['out_other']='($'.number_format(abs($sum),2,'.',',');
        } else {
            $out['out_other']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',','));
        }
        $sum=floatval($res['sum_deb_term']);
        $out['term_class']='';
        if ($sum<0) {
            $out['term_class']='batchnegative';
            $out['out_term']='($'.number_format(abs($sum),2,'.',',');
        } else {
            $out['out_term']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',','));
        }
        $sum=floatval($res['sum_deb_writeoff']);
        $out['writeoff_class']='';
        if ($sum<0) {
            $out['writeoff_class']='batchnegative';
            $out['out_writeoff']='($'.number_format(abs($sum),2,'.',',');
        } else {
            $out['out_writeoff']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',','));
        }
        $pendcc=floatval($res['sum_deb_vmd'])+floatval($res['sum_deb_amex']);
        $out['pendcc_class']='';
        if ($pendcc<0) {
            $out['pendcc_class']='batchnegative';
            $out['out_pendcc']='($'.number_format(abs($pendcc),2,'.',',');
        } else {
            $out['out_pendcc']=($pendcc==0 ? $empty : '$'.number_format($pendcc,2,'.',','));
        }
        $out['pastdue_class']='';
        $pastdue=floatval($past['sum']);
        if ($pastdue<0) {
            $out['pastdue_class']='batchnegative';
            $out['out_pastdue']='($'.number_format(abs($pastdue),2,'.',',');
        } else {
            $out['out_pastdue']=($pastdue==0 ? $empty : '$'.number_format($pastdue,2,'.',','));
        }
        return $out;
    }

    public function get_batchdetails($options) {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $empty='---';
        $this->db->select('date_format(from_unixtime(b.batch_date),\'%Y-%m-%d\') as batch_date, count(b.batch_id) as total_orders, sum(if(b.batch_vmd!=0,b.batch_amount,0)) as inv_vmd');
        $this->db->select('sum(if(b.batch_amex!=0,b.batch_amount,0)) as inv_amex, sum(if(b.batch_other!=0,b.batch_amount,0)) as inv_other');
        $this->db->select('sum(if(b.batch_term!=0,b.batch_amount,0)) as inv_term, sum(if(b.batch_writeoff!=0,b.batch_amount,0)) as inv_writeoff');
        $this->db->select('(-1)*sum(if(b.batch_vmd!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_vmd, (-1)*sum(if(b.batch_amex!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_amex');
        $this->db->select('(-1)*sum(if(b.batch_other!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_other, (-1)*sum(if(b.batch_term!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_term');
        $this->db->select('(-1)*sum(if(b.batch_writeoff!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_writeoff');
        $this->db->select('(-1)*sum(b.batch_vmd*(b.batch_received-1)+b.batch_amex*(b.batch_received-1)+b.batch_other*(b.batch_received-1)+b.batch_term*(b.batch_received-1)+b.batch_writeoff*(b.batch_received-1)) as deb_total');
        $this->db->select('sum(if(b.batch_vmd!=0,b.batch_amount,0)*(b.batch_received)) as receiv_vmd, sum(if(b.batch_amex!=0,b.batch_amount,0)*b.batch_received) as receiv_amex');
        $this->db->select('sum(if(b.batch_other!=0,b.batch_amount,0)*(b.batch_received)) as receiv_other, sum(if(b.batch_term!=0,b.batch_amount,0)*(b.batch_received)) as receiv_term');
        $this->db->select('sum(if(b.batch_writeoff!=0,b.batch_amount,0)*(b.batch_received)) as receiv_writeoff');
        $this->db->select('sum(b.batch_vmd*b.batch_received+b.batch_amex*b.batch_received+b.batch_other*b.batch_received+b.batch_term*b.batch_received+b.batch_writeoff*b.batch_received) as receiv_total');
        $this->db->from('ts_order_batches b');
        if (isset($options['received'])) {
            if ($options['received']==0) {
                $this->db->having('deb_total != ',0);
            } elseif($options['received']==1) {
                $this->db->having('receiv_total != ',0);
            }
        } else {
            $start_date=strtotime($options['viewyear'].'-01-01');
            $end_date=strtotime(($options['viewyear']+1).'-01-01');
            $this->db->where('batch_date >= ', $start_date);
            $this->db->where('batch_date < ', $end_date);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o','o.order_id=b.order_id');
            $this->db->where('o.brand', $options['brand']);
        }
        $this->db->group_by('date_format(from_unixtime(b.batch_date),\'%Y-%m-%d\')');
        $this->db->order_by('batch_date','desc');
        $res=$this->db->get()->result_array();
        $out=array();
        $numpp=1;

        foreach ($res as $row) {
            $numord=intval($row['total_orders']);
            if ($numord>0) {
                $row['day_results']=$numord;
                $totalsum=floatval($row['inv_vmd'])+floatval($row['inv_amex'])+floatval($row['inv_other'])+floatval($row['inv_term'])+floatval($row['inv_writeoff']);
                if ($totalsum<0) {
                    $row['day_results'].=' - <span style="color:red">($'.number_format(abs($totalsum),2,'.',',').')</span>';
                } else {
                    $row['day_results'].=($totalsum==0 ? '' : ' - $'.number_format($totalsum,2,'.',','));
                }
            } else {
                $row['day_results']='&nbsp;';
            }
            // $row['out_date']=date('D M j, Y',$row['batch_date']);
            $row['out_date']=date('D M j, Y',strtotime($row['batch_date']));
            $sum=floatval($row['inv_vmd']);
            $row['inv_vmdclass']='';
            if ($sum<0) {
                $row['inv_vmdclass']='batchnegative';
                $row['inv_vmd']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['inv_vmd']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['inv_amex']);
            $row['inv_amexclass']='';
            if ($sum<0) {
                $row['inv_amexclass']='batchnegative';
                $row['inv_amex']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['inv_amex']=($sum==0 ? $empty : '$'.number_format($row['inv_amex'],2,'.',''));
            }
            $sum=floatval($row['inv_other']);
            $row['inv_otherclass']='';
            if ($sum<0) {
                $row['inv_otherclass']='batchnegative';
                $row['inv_other']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['inv_other']=($sum==0 ? $empty : '$'.number_format($row['inv_other'],2,'.',''));
            }
            $sum=floatval($row['inv_term']);
            $row['inv_termclass']='';
            if ($sum<0) {
                $row['inv_termclass']='batchnegative';
                $row['inv_term']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['inv_term']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['inv_writeoff']);
            $row['inv_writeoffclass']='';
            if ($sum<0) {
                $row['inv_writeoffclass']='batchnegative';
                $row['inv_writeoff']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['inv_writeoff']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['deb_vmd']);
            $row['deb_vmdclass']='';
            if ($sum<0) {
                $row['deb_vmdclass']='batchnegative';
                $row['deb_vmd']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['deb_vmd']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['deb_amex']);
            $row['deb_amexclass']='';
            if ($sum<0) {
                $row['deb_amexclass']='batchnegative';
                $row['deb_amex']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['deb_amex']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['deb_other']);
            $row['deb_otherclass']='';
            if ($sum<0) {
                $row['deb_otherclass']='batchnegative';
                $row['deb_other']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['deb_other']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['deb_term']);
            $row['deb_termclass']='';
            if ($sum<0) {
                $row['deb_termclass']='batchnegative';
                $row['deb_term']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['deb_term']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($row['deb_writeoff']);
            $row['deb_writeoffclass']='';
            if ($sum<0) {
                $row['deb_writeoffclass']='batchnegative';
                $row['deb_writeoff']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $row['deb_writeoff']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $totals=$row;
            $batch_date=$row['batch_date'];
            // $datebgn=strtotime(date('Y-m-d',$batch_date));
            $datebgn=strtotime($batch_date);
            $dateend=strtotime(date("Y-m-d", $datebgn) . " +1days");
            /* Select Lines */
            $this->db->select('b.*,o.order_num, o.customer_name');
            $this->db->from('ts_order_batches b');
            $this->db->join('ts_orders o','o.order_id=b.order_id','left');
            // start-end
            // $this->db->where('b.batch_date',$batch_date);
            $this->db->where('b.batch_date >=', $datebgn);
            $this->db->where('b.batch_date < ', $dateend);
            if (isset($options['received'])) {
                if ($options['received']==0) {
                    $this->db->where('batch_received',0);
                } elseif($options['received']==1) {
                    $this->db->where('batch_received',1);
                }
            }
            $this->db->order_by('batch_id');
            $lines=$this->db->get()->result_array();
            // log_message('ERROR','detlines SQL 1 '.$this->db->last_query());
            $outlines=array();
            foreach($lines as $lrow) {
                $lrow['emailed_class']=($lrow['batch_email']==1 ? 'emailed' : '');
                $sum=floatval($lrow['batch_vmd']);
                $sumrow=floatval($lrow['batch_amount']);
                $lrow['vmd_class']='';
                if ($sum<0) {
                    $lrow['vmd_class']='batchnegative';
                    $lrow['batch_vmd']='($'.number_format(abs($sumrow),2,'.','').')';
                } else {
                    $lrow['batch_vmd']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
                }
                $sum=floatval($lrow['batch_amex']);
                $lrow['amex_class']='';
                if ($sum<0) {
                    $lrow['amex_class']='batchnegative';
                    $lrow['batch_amex']='($'.number_format(abs($sumrow),2,'.','').')';
                } else {
                    $lrow['batch_amex']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
                }
                $sum=floatval($lrow['batch_other']);
                $lrow['other_class']='';
                if ($sum<0) {
                    $lrow['other_class']='batchnegative';
                    $lrow['batch_other']='($'.number_format(abs($sumrow),2,'.','').')';
                } else {
                    $lrow['batch_other']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
                }
                $sum=floatval($lrow['batch_term']);
                $lrow['term_class']='';
                if ($sum<0) {
                    $lrow['term_class']='batchnegative';
                    $lrow['batch_term']='($'.number_format(abs($sumrow),2,'.','').')';
                } else {
                    $lrow['batch_term']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
                }
                $sum=floatval($lrow['batch_writeoff']);
                $lrow['writeoff_class']='';
                if ($sum<0) {
                    $lrow['writeoff_class']='batchnegative';
                    $lrow['batch_writeoff']='($'.number_format(abs($sumrow),2,'.','').')';
                } else {
                    $lrow['batch_writeoff']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
                }
                $lrow['batch_due']=($lrow['batch_due']==0 ? $empty : date('m/j',$lrow['batch_due']));
                $lrow['received_class']=($lrow['batch_received']==1 ? 'received' : '');
                $lrow['batchnote']='<img src="/img/accounting/empty_square.png" alt="empty note"/>';
                $lrow['batchnote_class']='';
                $lrow['batchnote_title']='';
                if ($lrow['batch_note']!='') {
                    $lrow['batchnote']='<img src="/img/accounting/lightblue_square.png" alt="Batch note"/>';
                    $lrow['batchnote_title']=$lrow['batch_note'];
                    $lrow['batchnote_class']='batchnoteview';
                }
                $lrow['order_num']=($lrow['order_num']=='' ? $this->manual_batch : $lrow['order_num']);
                $outlines[]=$lrow;
            }
            $out[]=array(
                'totals'=>$totals,
                'lines'=>$outlines,
                'batch_date'=>$batch_date
            );
        }
        return $out;
    }

    public function get_batchdetails_date($options) {
        $empty='---';
        $this->db->select('count(b.batch_id) as total_orders, sum(if(b.batch_vmd!=0,b.batch_amount,0)) as inv_vmd');
        $this->db->select('sum(if(b.batch_amex!=0,b.batch_amount,0)) as inv_amex, sum(if(b.batch_other!=0,b.batch_amount,0)) as inv_other');
        $this->db->select('sum(if(b.batch_term!=0,b.batch_amount,0)) as inv_term, sum(if(b.batch_writeoff!=0,b.batch_amount,0)) as inv_writeoff');
        $this->db->select('(-1)*sum(if(b.batch_vmd!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_vmd, (-1)*sum(if(b.batch_amex!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_amex');
        $this->db->select('(-1)*sum(if(b.batch_other!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_other, (-1)*sum(if(b.batch_term!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_term');
        $this->db->select('(-1)*sum(if(b.batch_writeoff!=0,b.batch_amount,0)*(b.batch_received-1)) as deb_writeoff');
        $this->db->select('(-1)*sum(b.batch_vmd*(b.batch_received-1)+b.batch_amex*(b.batch_received-1)+b.batch_other*(b.batch_received-1)+b.batch_term*(b.batch_received-1)+b.batch_writeoff*(b.batch_received-1)) as deb_total');
        $this->db->select('sum(if(b.batch_vmd!=0,b.batch_amount,0)*(b.batch_received)) as receiv_vmd, sum(if(b.batch_amex!=0,b.batch_amount,0)*b.batch_received) as receiv_amex');
        $this->db->select('sum(if(b.batch_other!=0,b.batch_amount,0)*(b.batch_received)) as receiv_other, sum(if(b.batch_term!=0,b.batch_amount,0)*(b.batch_received)) as receiv_term');
        $this->db->select('sum(if(b.batch_writeoff!=0,b.batch_amount,0)*(b.batch_received)) as receiv_writeoff');
        $this->db->select('sum(b.batch_vmd*b.batch_received+b.batch_amex*b.batch_received+b.batch_other*b.batch_received+b.batch_term*b.batch_received+b.batch_writeoff*b.batch_received) as receiv_total');
        $this->db->from('ts_order_batches b');
        if (isset($options['batch_enddate'])) {
            $this->db->where('b.batch_date >= ',$options['batch_date']);
            $this->db->where('b.batch_date < ',$options['batch_enddate']);
        } else {
            $this->db->where('b.batch_date',$options['batch_date']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o','o.order_id=b.order_id');
            $this->db->where('o.brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();

        $total=array();
        // if (isset($res['batch_date'])) {
        if (isset($res['total_orders'])) {
            $total['batch_date']=$options['batch_date'];
            $total['out_date']=date('D M j, Y',$options['batch_date']);
            $sum=floatval($res['inv_vmd']);
            $total['inv_vmdclass']='';
            if ($sum<0) {
                $total['inv_vmdclass']='batchnegative';
                $total['inv_vmd']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['inv_vmd']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['inv_amex']);
            $total['inv_amexclass']='';
            if ($sum<0) {
                $total['inv_amexclass']='batchnegative';
                $total['inv_amex']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['inv_amex']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['inv_other']);
            $total['inv_otherclass']='';
            if ($sum<0) {
                $total['inv_otherclass']='batchnegative';
                $total['inv_other']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['inv_other']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['inv_term']);
            $total['inv_termclass']='';
            if ($sum<0) {
                $total['inv_termclass']='batchnegative';
                $total['inv_term']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['inv_term']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['inv_writeoff']);
            $total['inv_writeoffclass']='';
            if ($sum<0) {
                $total['inv_writeoffclass']='batchnegative';
                $total['inv_writeoff']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['inv_writeoff']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['deb_vmd']);
            $total['deb_vmdclass']='';
            if ($sum<0) {
                $total['deb_vmdclass']='batchnegative';
                $total['deb_vmd']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['deb_vmd']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['deb_amex']);
            $total['deb_amexclass']='';
            if ($sum<0) {
                $total['deb_amexclass']='batchnegative';
                $total['deb_amex']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['deb_amex']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['deb_other']);
            $total['deb_otherclass']='';
            if ($sum<0) {
                $total['deb_otherclass']='batchnegative';
                $total['deb_other']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['deb_other']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['deb_term']);
            $total['deb_termclass']='';
            if ($sum<0) {
                $total['deb_termclass']='batchnegative';
                $total['deb_term']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['deb_term']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }
            $sum=floatval($res['deb_writeoff']);
            $total['deb_writeoffclass']='';
            if ($sum<0) {
                $total['deb_writeoffclass']='batchnegative';
                $total['deb_writeoff']='($'.number_format(abs($sum),2,'.','').')';
            } else {
                $total['deb_writeoff']=($sum==0 ? $empty : '$'.number_format($sum,2,'.',''));
            }

            $numord=intval($res['total_orders']);

            if ($numord>0) {
                $totalsum=floatval($res['inv_vmd'])+floatval($res['inv_amex'])+floatval($res['inv_other'])+floatval($res['inv_term'])+floatval($res['inv_writeoff']);
                $total['day_results']=$numord;
                if ($totalsum<0) {
                    $total['day_results'].=' - <span style="color:red">($'.number_format(abs($totalsum),2,'.',',').')</span>';
                } else {
                    $total['day_results'].=($totalsum==0 ? '' : ' - $'.number_format($totalsum,2,'.',','));
                }

            } else {
                $total['day_results']='&nbsp;';
            }
        } else {
            $total['out_date']=date('D M j, Y',$options['batch_date']);
            $total['inv_vmdclass']=$total['inv_amexclass']=$total['inv_otherclass']=$total['inv_termclass']=$total['inv_writeoffclass']='';
            $total['inv_vmd']=$empty;
            $total['inv_amex']=$empty;
            $total['inv_other']=$empty;
            $total['inv_term']=$empty;
            $total['inv_writeoff']=$empty;
            $total['deb_vmdclass']=$total['deb_amexclass']=$total['deb_otherclass']=$total['deb_termclass']=$total['deb_writeoffclass']='';
            $total['deb_vmd']=$empty;
            $total['deb_amex']=$empty;
            $total['deb_other']=$empty;
            $total['deb_term']=$empty;
            $total['deb_writeoff']=$empty;
            $total['day_results']='&nbsp;';
        }

        /* select orders included in batch */
        $this->db->select('b.*,o.order_num, o.customer_name');
        $this->db->from('ts_order_batches b');
        $this->db->join('ts_orders o','o.order_id=b.order_id','left');
        // $this->db->where('b.batch_date',$options['batch_date']);
        if (isset($options['batch_enddate'])) {
            $this->db->where('b.batch_date >= ',$options['batch_date']);
            $this->db->where('b.batch_date < ',$options['batch_enddate']);
        } else {
            $this->db->where('b.batch_date',$options['batch_date']);
        }
        if (isset($options['received'])) {
            if ($options['received']==0) {
                $this->db->where('batch_received',0);
            } elseif($options['received']==1) {
                $this->db->where('batch_received',1);
            }
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('o.brand', $options['brand']);
        }
        $this->db->order_by('batch_id');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach($res as $row) {
            $sumrow=floatval($row['batch_amount']);
            $row['emailed_class']=($row['batch_email']==1 ? 'emailed' : '');
            $sum=floatval($row['batch_vmd']);
            $row['order_num']=($row['order_num']=='' ? $this->manual_batch : $row['order_num']);
            $row['vmd_class']='';
            if ($sum<0) {
                $row['vmd_class']='batchnegative';
                $row['batch_vmd']='($'.number_format(abs($sumrow),2,'.','').')';
            } else {
                $row['batch_vmd']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
            }
            $sum=floatval($row['batch_amex']);
            $row['amex_class']='';
            if ($sum<0) {
                $row['amex_class']='batchnegative';
                $row['batch_amex']='($'.number_format(abs($sumrow),2,'.','').')';
            } else {
                $row['batch_amex']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
            }
            $sum=floatval($row['batch_other']);
            $row['other_class']='';
            if ($sum<0) {
                $row['other_class']='batchnegative';
                $row['batch_other']='($'.number_format(abs($sumrow),2,'.','').')';
            } else {
                $row['batch_other']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
            }
            $sum=floatval($row['batch_term']);
            $row['term_class']='';
            if ($sum<0) {
                $row['term_class']='batchnegative';
                $row['batch_term']='($'.number_format(abs($sumrow),2,'.','').')';
            } else {
                $row['batch_term']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
            }
            $sum=floatval($row['batch_writeoff']);
            $row['writeoff_class']='';
            if ($sum<0) {
                $row['writeoff_class']='batchnegative';
                $row['batch_writeoff']='($'.number_format(abs($sumrow),2,'.','').')';
            } else {
                $row['batch_writeoff']=($sum==0 ? $empty : '$'.number_format($sumrow,2,'.',''));
            }
            $row['batch_due']=($row['batch_due']==0 ? $empty : date('m/j',$row['batch_due']));
            $row['received_class']=($row['batch_received']==1 ? 'received' : '');
            $row['batchnote']='<img src="/img/accounting/empty_square.png" alt="empty note"/>';
            $row['batchnote_class']='';
            $row['batchnote_title']='';
            if ($row['batch_note']!='') {
                $row['batchnote']='<img src="/img/accounting/lightblue_square.png" alt="Batch note"/>';
                $row['batchnote_title']=$row['batch_note'];
                $row['batchnote_class']='batchnoteview';
            }
            $out[]=$row;
        }
        return array('totals'=>$total,'details'=>$out);
    }

    public function save_batch($batch_data, $order_data, $user_id) {
        $ci=&get_instance();
        $amnt = round(floatval($batch_data['amount']),2);
        $orderrev = round(floatval($order_data['revenue']),2);
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        if ($batch_data['batch_date']==0) {
            $out['msg']='Empty batch date';
        } elseif ($batch_data['paymethod']=='') {
            $out['msg']='Empty payment method';
        } elseif ($batch_data['amount']==0) {
            $out['msg']='Empty amount';
        } elseif (!isset($order_data['order_id'])) {
            $out['msg'] = 'Order not found';
            // } elseif ($batch_data['amount']>floatval($order_data['revenue'])) {
        } elseif ($amnt > $orderrev) {
            $out['msg']='Amount great then order revenue';
        } else {
            $paypal_apply=strtotime($this->paypal_apply);
            if ($batch_data['batch_date']<$paypal_apply) {
                /* Apply Fee of Auth.net */
                $paymethod='AUTH';
                $amex_fee=$this->config->item('auth_amexfee');
                $vmd_fee=$this->config->item('auth_vmdfee');
            } else {
                /* Apply Fee of Paypal */
                $paymethod='PAYPAL';
                $amex_fee=$this->config->item('paypal_amexfee');
                $vmd_fee=$this->config->item('paypal_vmdfee');
                if ($batch_data['batch_date'] >= $this->config->item('datenewfee')) {
                    $amex_fee=$this->config->item('paypal_amexfeenew');
                    $vmd_fee=$this->config->item('paypal_vmdfeenew');
                }
            }
            $inv_vmd=0;
            $inv_amex=0;
            $inv_other=0;
            $inv_term=0;
            $inv_writeoff=0;
            switch ($batch_data['paymethod']) {
                case 'v':
                case 'm':
                case 'd':
                    $inv_vmd=round($batch_data['amount']*((100-$vmd_fee)/100),2);
                    $duedate=$this->getVMDDueDate($batch_data['batch_date'],$paymethod);
                    break;
                case 'a':
                    $inv_amex=round($batch_data['amount']*((100-$amex_fee)/100),2);
                    $duedate=$this->getAmexDueDate($batch_data['batch_date'],$paymethod);
                    break;
                case 'o':
                    // $duedate=strtotime(date("Y-m-d", $batch_data['batch_date']) . " +1 day");
                    $duedate=$batch_data['datedue'];
                    $inv_other=$batch_data['amount'];
                    break;
                case 't':
                    $duedate=$batch_data['datedue'];
                    $inv_term=$batch_data['amount'];
                    break;
                case 'w':
                    $duedate=$batch_data['datedue'];
                    $inv_writeoff=$batch_data['amount'];
                    $batch_data['batch_received']=1;
                    break;
            }
            /* Correct data according to business calendar */
            $this->load->model('calendars_model');
            $duedate=$this->calendars_model->businessdate($duedate);
            $this->db->set('update_usr',$user_id);
            $this->db->set('batch_date',$batch_data['batch_date']);
            $this->db->set('order_id',$order_data['order_id']);
            $this->db->set('batch_amount',$batch_data['amount']);
            $this->db->set('batch_vmd',$inv_vmd);
            $this->db->set('batch_amex',$inv_amex);
            $this->db->set('batch_other',$inv_other);
            $this->db->set('batch_term',$inv_term);
            $this->db->set('batch_writeoff', $inv_writeoff);
            $this->db->set('batch_note',$batch_data['batch_note']);
            $this->db->set('batch_due',$duedate);
            if (isset($batch_data['batch_received'])) {
                $this->db->set('batch_received', $batch_data['batch_received']);
            }
            if (isset($batch_data['batch_type'])) {
                $this->db->set('batch_type', $batch_data['batch_type']);
            }
            if (isset($batch_data['batch_num'])) {
                $this->db->set('batch_num', $batch_data['batch_num']);
            }
            if (isset($batch_data['batch_transaction'])) {
                $this->db->set('batch_transaction', $batch_data['batch_transaction']);
            }
            if ($batch_data['amount']<0) {
                /* Refund */
                // $this->db->set('batch_received',1);
            }
            if ($batch_data['batch_id']==0) {
                $this->db->set('create_date',date('Y-m-d H:i:s'));
                $this->db->set('create_usr',$user_id);
                $this->db->insert('ts_order_batches');
                $newid = $this->db->insert_id();
                if ($newid > 0) {
                    $out['result'] = $this->success_result;
                }
            } else {
                $this->db->where('batch_id',$batch_data['batch_id']);
                $this->db->update('ts_order_batches');
                $out['result']=$this->success_result;
            }
        }
        // Update Order CC FEE
        $this->db->select('count(b.batch_id) as cnt, sum(b.batch_amount) as batch_amount, sum(b.batch_amex) as batch_amex, sum(b.batch_vmd) as batch_vmd');
        $this->db->from('ts_order_batches b');
        $this->db->where('b.order_id', $order_data['order_id']);
        $this->db->where('(b.batch_amex!=0 or b.batch_vmd!=0)');
        $batchres=$this->db->get()->row_array();
        if ($batchres['cnt']>0) {
            // Update Order CC FEE
            $cc_fee=$batchres['batch_amount']-$batchres['batch_amex']-$batchres['batch_vmd'];
            // Update profit
        } else {
            $cc_fee=0;
        }
        $this->db->set('cc_fee', $cc_fee);
        $this->db->where('order_id', $order_data['order_id']);
        $this->db->update('ts_orders');
        $this->db->select('revenue, cc_fee, order_cog, tax, shipping, is_shipping');
        $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
        $this->db->from('ts_orders');
        $this->db->where('order_id', $order_data['order_id']);
        $ordres=$this->db->get()->row_array();
        if (!empty($ordres['order_cog'])) {
            $newprofit=$ordres['revenue']-($ordres['order_cog']+$ordres['cc_fee']+$ordres['tax']+$ordres['shipping']*$ordres['is_shipping']);
            $newprofitprc=0;
            if ($ordres['revenue']>0) {
                $newprofitprc=round($newprofit/$ordres['revenue']*100,2);
            }
            $this->db->set('profit', $newprofit);
            $this->db->set('profit_perc', $newprofitprc);
            $this->db->set('order_artview', $ordres['aprrovview']);
            $this->db->set('order_placed', $ordres['placeord']);
            $this->db->where('order_id', $order_data['order_id']);
            $this->db->update('ts_orders');
        }
        return $out;
    }

    public function batchmailed($batch_id,$mail) {
        $this->db->set('batch_email',$mail);
        $this->db->where('batch_id',$batch_id);
        $this->db->update('ts_order_batches');
        return $this->db->affected_rows();
    }

    public function batchreceived($batch_id,$receiv) {
        $this->db->set('batch_received',$receiv);
        $this->db->where('batch_id',$batch_id);
        $this->db->update('ts_order_batches');
        return $this->db->affected_rows();
    }

    public function get_batchsum_order($order_id) {
        $this->db->select('sum(batch_amount) as batchsum, count(batch_id) as batchcnt');
        $this->db->from('ts_order_batches');
        $this->db->where('order_id',$order_id);
        $res=$this->db->get()->row_array();
        if ($res['batchcnt']==0) {
            $retsum=0;
        } else {
            $retsum=floatval($res['batchsum']);
        }
        return $retsum;
    }

    function get_batch_detail($batch_id) {
        $this->db->select('b.*, o.order_num, o.customer_name');
        $this->db->from('ts_order_batches b');
        $this->db->join('ts_orders o','o.order_id=b.order_id','left');
        $this->db->where('b.batch_id',$batch_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    /* batches by due date */
    public function get_batchcalen_date($options) {
        $this->db->select('count(b.order_id) AS total_orders, sum(b.batch_vmd) AS inv_vmd, sum(b.batch_amex) AS inv_amex, sum(b.batch_other) AS inv_other');
        $this->db->select('sum(b.batch_term) AS inv_term, sum(b.batch_writeoff) as inv_writeoff, (-(1) * sum((b.batch_vmd * (b.batch_received - 1)))) AS deb_vmd');
        $this->db->select('(-(1) * sum((b.batch_amex * (b.batch_received - 1)))) AS deb_amex, (-(1) * sum((b.batch_other * (b.batch_received - 1)))) AS deb_other');
        $this->db->select('(-(1) * sum((b.batch_term * (b.batch_received - 1)))) AS deb_term, (-(1) * sum((b.batch_writeoff * (b.batch_received - 1)))) AS deb_writeoff');
        $this->db->select('sum((b.batch_vmd * b.batch_received)) AS rec_vmd, sum((b.batch_amex * b.batch_received)) AS rec_amex');
        $this->db->select('sum((b.batch_other * b.batch_received)) AS rec_other, sum((b.batch_term * b.batch_received)) AS rec_term, sum((b.batch_writeoff * b.batch_received)) AS rec_writeoff');
        $this->db->from('ts_order_batches b');
        $this->db->where('b.batch_due',$options['batch_due']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->join('ts_orders o','o.order_id=b.order_id');
            $this->db->where('o.brand', $options['brand']);
        }
        $row=$this->db->get()->row_array();
        // if (!isset($row['batch_due'])) {
        if ($row['total_orders']==0) {
            $row['out_date']=date('M j',$options['batch_due']);
            $row['out_vmdclass']='';
            $row['out_vmd']='';
            $row['out_amexclass']='';
            $row['out_amex']='';
            $row['out_otherclass']='';
            $row['out_other']='';
            $row['out_termclass']='';
            $row['out_term']='';
            $row['out_writeoffclass']='';
            $row['out_writeoff']='';
        } else {
            $row['out_date']=date('M j',$options['batch_due']);
            $sum=floatval($row['deb_vmd']);
            $row['out_vmdclass']=($sum<0 ? 'batchnegative' : '');
            $row['out_vmd']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->VISA);
            // $row['out_vmd']=(floatval($row['deb_vmd'])==0 ? '&nbsp;' :  '$'.  number_format($row['deb_vmd'],2,'.','').$this->VISA);
            $sum=floatval($row['deb_amex']);
            $row['out_amexclass']=($sum<0 ? 'batchnegative' : '');
            $row['out_amex']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->AMEX);
            // $row['out_amex']=(floatval($row['deb_amex'])==0 ? '&nbsp;' : '$'.number_format($row['deb_amex'],2,'.','').$this->AMEX);
            $sum=floatval($row['deb_other']);
            $row['out_otherclass']=($sum<0 ? 'batchnegative' : '');
            $row['out_other']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->OTHERPAY);
            // $row['out_other']=(floatval($row['deb_other'])==0 ? '&nbsp;' : '$'.number_format($row['deb_other'],2,'.','').$this->OTHERPAY);
            $sum=floatval($row['deb_term']);
            $row['out_termclass']=($sum<0 ? 'batchnegative' : '');
            $row['out_term']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->TERMS);
            //$row['out_term']=(floatval($row['deb_term'])==0 ? '&nbsp;' : '$'.number_format($row['deb_term'],2,'.','').$this->TERMS);
            $sum=floatval($row['deb_writeoff']);
            $row['out_writeoffclass']=($sum<0 ? 'batchnegative' : '');
            $row['out_writeoff']=($sum==0 ? '&nbsp;' : ($sum>0 ? '$'.number_format($sum,2,'.','') : '($'.number_format(abs($sum),2,'.','').')').$this->WRITE_OFF);
        }
        return $row;
    }

    /* delete batch */
    public function del_batch($batch_id) {
        $this->db->where('batch_id',$batch_id);
        $this->db->delete('ts_order_batches');
        return $this->db->affected_rows();
    }

    function save_manualrow($options, $user_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        $amex_fee=$this->config->item('amex_fee');
        $total_sum=0;
        $num_sum=1;
        $batch_vmd=floatval($options['batch_vmd']);
        $total_sum+=$batch_vmd;
        $num_sum-=($batch_vmd==0 ? 0 : 1);
        $batch_amex=floatval($options['batch_amex']);
        $total_sum+=$batch_amex;
        $num_sum-=($batch_amex==0 ? 0 : 1);
        $batch_other=floatval($options['batch_other']);
        $total_sum+=$batch_other;
        $num_sum-=($batch_other==0 ? 0 : 1);
        $batch_term=floatval($options['batch_term']);
        $total_sum+=$batch_term;
        $num_sum-=($batch_term==0 ? 0 : 1);
        $batch_writeoff=floatval($options['batch_writeoff']);
        $total_sum+=$batch_writeoff;
        $num_sum-=($batch_writeoff==0 ? 0 : 1);
        $datedue=$options['datedue'];
        $batch_date=strtotime($options['batch_date']);
        if ($total_sum==0) {
            $out['msg']='Empty batch amount';
        } elseif ($num_sum<0) {
            $out['msg']='More then 1 amount in batch';
        } elseif ($datedue<=$batch_date) {
            $out['msg']='Date due less then batch date';
        } else {
            $batch_amount=0;
            $paypal_apply=strtotime($this->paypal_apply);
            if ($options['batch_date']<$paypal_apply) {
                /* Apply Fee of Auth.net */
                $paymethod='AUTH';
                $amex_fee=$this->config->item('auth_amexfee');
                $vmd_fee=$this->config->item('auth_vmdfee');
            } else {
                /* Apply Fee of Paypal */
                $paymethod='PAYPAL';
                $amex_fee=$this->config->item('paypal_amexfee');
                $vmd_fee=$this->config->item('paypal_vmdfee');
                if ($options['batch_date'] >= $this->config->item('datenewfee')) {
                    $amex_fee=$this->config->item('paypal_amexfeenew');
                    $vmd_fee=$this->config->item('paypal_vmdfeenew');
                }
            }
            if ($batch_vmd!=0) {
                $batch_amount=$batch_vmd;
                $batch_vmd=round($batch_amount*((100-$vmd_fee)/100),2);
                $duedate=$this->getVMDDueDate($options['batch_date'],$paymethod);
            } elseif($batch_amex!=0) {
                $batch_amount=$batch_amex;
                $duedate=$this->getAmexDueDate($options['batch_date'],$paymethod);
                $batch_amex=round($batch_amount*((100-$amex_fee)/100),2);
            } elseif ($batch_other!=0) {
                $batch_amount=$batch_other;
                $duedate=$datedue;
            } elseif($batch_term!=0) {
                $batch_amount=$batch_term;
                $duedate=$datedue;
            } elseif ($batch_writeoff!=0) {
                $batch_amount=$batch_writeoff;
                $duedate=$datedue;
            }
            $this->load->model('calendars_model');
            $duedate=$this->calendars_model->businessdate($duedate);
            if (isset($options['batch_email'])) {
                $this->db->set('batch_email',1);
            }
            if (isset($options['batch_received'])) {
                $this->db->set('batch_received',1);
            }
            $this->db->set('batch_due',$duedate);
            $this->db->set('batch_amount',$batch_amount);
            $this->db->set('batch_vmd',$batch_vmd);
            $this->db->set('batch_amex',$batch_amex);
            $this->db->set('batch_other',$batch_other);
            $this->db->set('batch_term',$batch_term);
            $this->db->set('batch_writeoff',$batch_writeoff);
            $this->db->set('update_usr',$user_id);
            $this->db->where('batch_id',$options['batch_id']);
            $this->db->update('ts_order_batches');
            $out['result']=$this->success_result;
            $out['msg']='';
            $out['batch_due']=$duedate;
        }
        return $out;
    }


    /* Save changes in batch row */
    function save_batchrow($options,$user_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);

        $amex_fee=$this->config->item('amex_fee');
        $total_sum=0;
        $num_sum=1;
        $batch_vmd=floatval($options['batch_vmd']);
        $total_sum+=$batch_vmd;
        $num_sum-=($batch_vmd==0 ? 0 : 1);
        $batch_amex=floatval($options['batch_amex']);
        $total_sum+=$batch_amex;
        $num_sum-=($batch_amex==0 ? 0 : 1);
        $batch_other=floatval($options['batch_other']);
        $total_sum+=$batch_other;
        $num_sum-=($batch_other==0 ? 0 : 1);
        $batch_term=floatval($options['batch_term']);
        $total_sum+=$batch_term;
        $num_sum-=($batch_term==0 ? 0 : 1);
        $batch_writeoff=floatval($options['batch_writeoff']);
        $total_sum+=$batch_writeoff;
        $num_sum-=($batch_writeoff==0 ? 0 : 1);
        $new_paidsum=$options['batch_sum']+$total_sum;
        $datedue=$options['datedue'];
        // $batch_date=strtotime($options['batch_date']);
        $batch_date=$options['batch_date'];
        if ($total_sum==0) {
            $out['msg']='Empty batch amount';
        } elseif ($num_sum<0) {
            $out['msg']='More then 1 amount in batch';
        } elseif ($new_paidsum>$options['order_revenue']) {
            $out['msg']='Batch amount value great then Order amount';
        } elseif ($datedue<=$batch_date) {
            $out['msg']='Date due less then batch date';
        } else {
            $batch_amount=0;
            $paypal_apply=strtotime($this->paypal_apply);
            if ($options['batch_date']<$paypal_apply) {
                /* Apply Fee of Auth.net */
                $paymethod='AUTH';
                $amex_fee=$this->config->item('auth_amexfee');
                $vmd_fee=$this->config->item('auth_vmdfee');
            } else {
                /* Apply Fee of Paypal */
                $paymethod='PAYPAL';
                $amex_fee=$this->config->item('paypal_amexfee');
                $vmd_fee=$this->config->item('paypal_vmdfee');
                if ($options['batch_date'] >= $this->config->item('datenewfee')) {
                    $amex_fee=$this->config->item('paypal_amexfeenew');
                    $vmd_fee=$this->config->item('paypal_vmdfeenew');
                }
            }
            if ($batch_vmd!=0) {
                $batch_amount=$batch_vmd;
                $batch_vmd=round($batch_amount*((100-$vmd_fee)/100),2);
                $duedate=$this->getVMDDueDate($options['batch_date'],$paymethod);
            } elseif($batch_amex!=0) {
                $batch_amount=$batch_amex;
                $duedate=$this->getAmexDueDate($options['batch_date'],$paymethod);
                $batch_amex=round($batch_amount*((100-$amex_fee)/100),2);
            } elseif ($batch_other!=0) {
                $batch_amount=$batch_other;
                $duedate=$datedue;
            } elseif($batch_term!=0) {
                $batch_amount=$batch_term;
                $duedate=$datedue;
            } elseif($batch_writeoff!=0) {
                $batch_amount=$batch_writeoff;
                $duedate=$datedue;
            }
            $this->load->model('calendars_model');
            $duedate=$this->calendars_model->businessdate($duedate);
            if (isset($options['batch_email'])) {
                $this->db->set('batch_email',1);
            }
            if (isset($options['batch_received'])) {
                $this->db->set('batch_received',1);
            }
            $this->db->set('batch_due',$duedate);
            $this->db->set('batch_amount',$batch_amount);
            $this->db->set('batch_vmd',$batch_vmd);
            $this->db->set('batch_amex',$batch_amex);
            $this->db->set('batch_other',$batch_other);
            $this->db->set('batch_term',$batch_term);
            $this->db->set('update_usr',$user_id);
            $this->db->where('batch_id',$options['batch_id']);
            $this->db->update('ts_order_batches');
            $out['result']=$this->success_result;
            $out['msg']='';
            $out['batch_due']=$duedate;
        }
        return $out;
    }

    function save_batchnote($batch_id,$batch_note) {
        $this->db->set('batch_note',$batch_note);
        $this->db->where('batch_id',$batch_id);
        $this->db->update('ts_order_batches');
        return TRUE;
    }

    /* Calculate STANDARD Due date */
    function get_batchdue($date,$paymethod) {
        $paypal_apply=strtotime($this->paypal_apply);
        if ($date<$paypal_apply) {
            $paymeth='AUTH';
        } else {
            $paymeth='PAYPAL';
        }
        switch ($paymethod) {
            case 'v':
            case 'm':
            case 'd':
                $duedate=$this->getVMDDueDate($date,$paymeth);
                break;
            case 'a':
                $duedate=$this->getAmexDueDate($date,$paymeth);
                break;
            case 't':
                $duedate=strtotime(date("Y-m-d", $date) . " +1 month");
                break;
            case 'o':
            case 'w':
                $duedate=strtotime(date("Y-m-d", $date) . " +1 day");
                break;
        }
        $this->load->model('calendars_model');
        $duedate=$this->calendars_model->businessdate($duedate);
        return $duedate;
    }

//    /* get list of batches which have date due */
//    function get_batches_duedate($due) {
//        /* Delete hours */
//        $due=date('Y-m-d',$due);
//        /* Get list of date */
//        $this->db->select('b.batch_date, b.batch_vmd, b.batch_amex, b.batch_other, b.batch_term, b.batch_writeoff, b.batch_received, b.batch_amount');
//        $this->db->select('o.order_num, o.customer_name, o.revenue');
//        $this->db->from('ts_order_batches b');
//        $this->db->join('ts_orders o','o.order_id=b.order_id','left');
//        $this->db->where("date_format(from_unixtime(batch_due),'%Y-%m-%d') ",$due);
//        $res=$this->db->get()->result_array();
//        $out=array();
//        foreach ($res as $row) {
//            $paymeth='';
//            $paysum=floatval($row['batch_amount']);
//            if (floatval($row['batch_vmd'])!=0) {
//                $paymeth='VMD';
//            } elseif(floatval($row['batch_amex']!=0)) {
//                $paymeth='Amex';
//            } elseif (floatval($row['batch_other'])!=0) {
//                $paymeth='Other';
//            } elseif (floatval($row['batch_term'])!=0) {
//                $paymeth='Term';
//            } elseif (floatval($row['batch_writeoff'])!=0) {
//                $paymeth='WriteOFF';
//            }
//            if ($paymeth!='') {
//                $row['paymeth']=$paymeth;
//                $row['paysum_class']='';
//                if ($paysum<0) {
//                    $row['paysum_class']='batchnegative';
//                    $row['paysum']='($'.number_format(abs($paysum),2,'.',',').')';
//                } else {
//                    $row['paysum']='$'.number_format($paysum,2,'.',',');
//                }
//                $row['batch_date']=date('m/d/y',$row['batch_date']);
//                $row['order_num']=($row['order_num']=='' ? $this->manual_batch : $row['order_num']);
//                $row['rowclass']=($row['batch_received']==1 ? 'duereceived' : '');
//                $out[]=$row;
//            }
//        }
//        return $out;
//    }

    public function batch_freebatchadd($newrec) {
        $out = ['result' => $this->error_result, 'msg' => 'Batch wasn\'t added' ];
        $this->db->set('create_usr',$newrec['create_usr']);
        $this->db->set('create_date',$newrec['create_date']);
        $this->db->set('update_usr',$newrec['update_usr']);
        $this->db->set('batch_date',$newrec['batch_date']);
        $this->db->set('batch_due',$newrec['batch_due']);
        $this->db->insert('ts_order_batches');
        $newbatch=$this->db->insert_id();
        if ($newbatch>0) {
            $out['result'] = $this->success_result;
            $out['newbatch'] = $newbatch;
        }
        return $out;
    }

    public function get_batches_years($brand) {
        $this->db->select('distinct(date_format(from_unixtime(b.batch_date),\'%Y\')) as year', FALSE);
        $this->db->from('ts_order_batches b');
        if ($brand!=='ALL') {
            $this->db->join('ts_orders o','o.order_id=b.order_id');
            $this->db->where('o.brand', $brand);
        }
        $this->db->order_by('year', 'desc');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function getAmexDueDate($batch_date,$paymeth) {
        if ($paymeth=='PAYPAL') {
            $duedate=strtotime(date('Y-m-d',$batch_date). " +2 day");
        } else {
            $weekday=date('N',$batch_date);
            $weeknum=date('W',$batch_date);
            $year=date('Y',$batch_date);
            if ($weekday==1) {
                /* Monday */
                $duedate=strtotime($year . 'W' . $weeknum . '5 00:00:00');
            } elseif ($weekday>1 && $weekday<5) {
                /* Tuesday, Wednesday, Thuesday  */
                $batch_date=strtotime(date('Y-m-d',$batch_date). " +7 day");
                $weeknum=date('W',$batch_date);
                $year=date('Y',$batch_date);
                $duedate=strtotime($year . 'W' . $weeknum . '1 00:00:00');
            } else {
                /* Friday, Saturday, Sunday  */
                $batch_date=strtotime(date('Y-m-d',$batch_date). " +7 day");
                $weeknum=date('W',$batch_date);
                $year=date('Y',$batch_date);
                $duedate=strtotime($year . 'W' . $weeknum . '2 00:00:00');
            }
        }
        return $duedate;
    }

    public function getVMDDueDate($batch_date, $paymethod) {
        if ($paymethod=='PAYPAL') {
            $duedate=strtotime(date('Y-m-d',$batch_date). " +2 day");
        } else {
            $duedate=strtotime(date('Y-m-d',$batch_date). " +1 day");
        }
        return $duedate;
    }

    public function error_batch($batch_data, $order, $usr_id) {
        $this->db->set('user', $usr_id);
        $this->db->set('batch_date', $batch_data['batch_date']);
        $this->db->set('paymethod', $batch_data['paymethod']);
        $this->db->set('amount', $batch_data['amount']);
        $this->db->set('order_revenue', $order['revenue']);
        $this->db->set('order_id', $order['order_id']);
        $this->db->set('batch_type', $batch_data['batch_type']);
        $this->db->set('batch_num', $batch_data['batch_num']);
        $this->db->set('batch_transaction', $batch_data['batch_transaction']);
        $this->db->set('error_message', $batch_data['msg']);
        $this->db->insert('ts_batch_errorlog');
        // Send notification
        if ($this->config->item('test_server')!=1) {
            $email_body = '';
            $email_body.='Batch Date '.date('d.m.Y H:i:s', $batch_data['batch_date']).PHP_EOL;
            $email_body.='Order '.$order['order_id'].PHP_EOL;
            $email_body.='Pay method '.$batch_data['paymethod'].PHP_EOL;
            $email_body.='Amount '.MoneyOutput($batch_data['amount']).PHP_EOL;
            $email_body.='Order Revenue '.MoneyOutput($order['revenue']).PHP_EOL;
            $email_body.='Batch Type '.$batch_data['batch_type'].PHP_EOL;
            $email_body.='Batch Number '.$batch_data['batch_num'].PHP_EOL;
            $email_body.='Transaction # '.$batch_data['batch_transaction'].PHP_EOL;
            $email_body.='Error Message '.$batch_data['msg'].PHP_EOL;
            $this->load->library('email');
            $config = $this->config->item('email_setup');
            $config['mailtype'] = 'text';
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->to($this->config->item('developer_email'));
            $this->email->bcc('to_german@yahoo.com'); //
            $this->email->from($this->config->item('customer_notification_sender'));

            $this->email->subject('Error during add batch '.date('d.m.Y H:i:s'));
            $this->email->message($email_body);
            $this->email->send();
            $this->email->clear(TRUE);
        }

    }
}