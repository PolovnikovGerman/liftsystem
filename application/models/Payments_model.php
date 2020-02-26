<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Payments_model extends MY_Model {
    private $START_ORDNUM=22000;

    function __construct() {
        parent::__construct();
    }

//    function get_payment_results($options) {
//        $res=array('unbilled'=>0, 'billbymethods'=>array());
//        $this->db->select('m.method_name, (coalesce(pay.sum_bill,0) - coalesce(sum_pay,0)) as sum_pay',FALSE);
//        $this->db->from('purchase_methods m');
//        $this->db->join('(select oa.method_id, sum(oa.amount_sum) as sum_bill , sum(op.charge_sum) as sum_pay from ts_order_amounts oa
//                          left join ts_order_charges op on op.amount_id=oa.amount_id
//                          join ts_orders o on o.order_id=oa.order_id where o.is_canceled=0 and o.is_closed=0
//                          group by oa.method_id) pay','pay.method_id=m.method_id','left');
//        $this->db->order_by('m.method_name');
//        $result=$this->db->get()->result_array();
//        /* Select sum unnamed */
//        $this->db->select('sum(revenue-profit) as sum_pay');
//        $this->db->from('ts_orders');
//        $this->db->where("is_canceled",0);
//        $this->db->where('order_cog is null');
//        $resunb=$this->db->get()->row_array();
//        $result[]=array('method_name'=>'', 'sum_pay'=>$resunb['sum_pay']);
//        /* Calculate Total unbilled */
//
//        $sumall=0;
//        $out_arr=array();
//        foreach ($result as $row) {
//            $sumall=$sumall+floatval($row['sum_pay']);
//            $row['sum_pay']=($row['sum_pay']=='' ? '-' : number_format($row['sum_pay'],2,'.',','));
//            $row['method_title']=($row['method_name']=='' ? 'Unplaced (est 66%)' : $row['method_name']);
//            $row['method_class']=($row['method_name']=='' ? 'unplacecharge' : 'methodcharge');
//            $out_arr[]=$row;
//        }
//        $res['billbymethods']=$out_arr;
//        $res['unbilled']=($sumall==0 ? '-' : '$'.number_format($sumall,2,'.',','));
//        return $res;
//    }

    public function get_count_purchorders($options) {
        $this->db->select('count(oa.amount_id) as cnt_rec');
        $this->db->from('ts_order_amounts oa');
        $this->db->join('ts_orders o','o.order_id=oa.order_id');
        $this->db->where("o.is_canceled",0);
        if (isset($options['status'])) {
            if ($options['status']=='showclosed') {
                $this->db->where('o.is_closed',0);
            }
        }
        if (isset($options['vendor_id'])) {
            $this->db->where('oa.vendor_id',$options['vendor_id']);
        }
        if (isset($options['searchpo'])) {
            $this->db->where('o.order_num', $options['searchpo']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('o.brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();
        return $res['cnt_rec'];
    }

//    public function get_amountrow($amount_id) {
//        $this->db->select('oa.amount_id, oa.amount_date, oa.amount_sum, oa.order_id, o.order_num, o.profit_perc, o.profit, o.order_cog');
//        $this->db->select('v.vendor_name, m.method_name, oa.date_charge, coalesce(pay.numchr,0) as numchr, pay.sumchr',FALSE);
//        $this->db->select('coalesce(oa.amount_sum,0)-coalesce(pay.sumchr,0) as rest, oa.is_closed ,  oa.order_replica, ad.cnt_att ',FALSE);
//        $this->db->from('ts_order_amounts oa');
//        $this->db->join('ts_orders o','o.order_id=oa.order_id');
//        $this->db->join('(select amount_id, count(charge_id) as numchr, sum(charge_sum) sumchr from ts_order_charges group by amount_id) pay','pay.amount_id=oa.amount_id','left');
//        $this->db->join('purchase_methods m','m.method_id=oa.method_id');
//        $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
//        $this->db->join('(select amount_id, count(amountdoc_id) as cnt_att from ts_amount_docs group by amount_id) ad','ad.amount_id=oa.amount_id','left');
//        $this->db->where("oa.amount_id",$amount_id);
//        $res=$this->db->get()->row_array();
//
//        $res['amount_date']=date('m/d/y',$res['amount_date']);
//        $res['amount_sum']=($res['amount_sum']=='' ? '-' : '$'.number_format($res['amount_sum'],2,'.',','));
//        if (floatval($res['profit'])==0) {
//            $res['profit']='-';
//        } else {
//            $res['profit']='$'.number_format($res['profit'],2,'.',',');
//        }
//        if ($res['order_cog']=='') {
//            $res['order_cog']='-';
//            $res['profit_class']='projprof';
//            $res['profit_perc']='PROJ';
//        } else {
//            // $res['order_cog']='$'.number_format($res['order_cog'],2,'.',',');
//            $res['profit_class']=$this->profit_class($res['profit_perc']);
//            $res['profit_perc']=($res['profit_perc']=='' ? '' :  number_format($res['profit_perc'],1,'.',',').'%');
//        }
//        $res['vendor_name']=($res['vendor_name']=='' ? '&nbsp;' : $res['vendor_name']);
//        $res['method_name']=($res['method_name']=='' ? '&nbsp;' : $res['method_name']);
//        $res['datcharclass']='';
//        if ($res['numchr']==0) {
//            $res['date_charge']='&nbsp';
//        } elseif ($res['numchr']==1) {
//            $res['date_charge']=date('m/d/Y',$res['date_charge']);
//        } else {
//            $res['date_charge']='Multiple';
//            $res['datcharclass']='multy';
//        }
//        $res['rowclass']=($res['is_closed']==1 ? 'closedorder' : '');
//        $res['sumchr']=($res['sumchr']=='' ? '-' : '$'.number_format($res['sumchr'],2,'.',','));
//        $res['rest']=($res['is_closed']==1 ? 0 : $res['rest']);
//        $res['rest']=($res['rest']==0  ? '---' : '$'.number_format($res['rest'],2,'.',','));
//        $res['closed_view']='/img/'.($res['is_closed']==1 ? 'closed' : 'opened').'.png';
//        $res['order_replica']=($res['order_replica']=='' ? '&nbsp' : $res['order_replica']);
//        $res['out_attach']='<img src="/img/empty_square.png" alt="Empty"/>';
//        $res['attclass']='';
//        $res['atttitle']='';
//        if (intval($res['cnt_att'])!=0) {
//            $res['out_attach']='<img src="/img/red_square.png" alt="Exist"/>';
//            $res['attclass']='allowattach';
//            $res['atttitle']=$res['cnt_att'].' Attachments';
//        }
//        return $res;
//    }

    public function get_purchorders($options,$order_by,$direct,$limit,$offset, $user_id) {
        $usrdata=$this->user_model->get_user_data($user_id);
        $this->db->select('oa.amount_id, oa.amount_date, oa.amount_sum, oa.order_id, o.order_num, o.profit_perc, o.profit, o.order_cog, o.order_items, o.order_itemnumber');
        $this->db->select('v.vendor_name, m.method_name, oa.date_charge',FALSE);
        $this->db->select("oa.is_closed, oa.order_replica, purchase_attachs(oa.amount_id) as cnt_att",FALSE);
        $this->db->select('o.reason as lowprofit, oa.reason, oa.printshop');
        $this->db->from('ts_order_amounts oa');
        $this->db->join('ts_orders o','o.order_id=oa.order_id');
        $this->db->join('purchase_methods m','m.method_id=oa.method_id');
        $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
        $this->db->where("o.is_canceled",0);
        if (isset($options['status'])) {
            if ($options['status']=='showclosed') {
                $this->db->where('oa.is_closed',0);
            }
        }
        if (isset($options['vendor_id'])) {
            $this->db->where('oa.vendor_id',$options['vendor_id']);
        }
        if (isset($options['searchpo'])) {
            $this->db->where('o.order_num', $options['searchpo']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('o.brand', $options['brand']);
        }
        if ($order_by=='oa.amount_date') {
            $order_by='oa.amount_date, oa.amount_id';
        } else {
            $this->db->order_by($order_by,$direct);
        }
        $this->db->limit($limit,$offset);
        $data=$this->db->get()->result_array();
        $out_array=array();
        foreach ($data as $row) {
            $row['amount_date']=date('m/d/y',$row['amount_date']);
            $row['potitle']=$row['order_itemnumber'].' - '.htmlspecialchars($row['order_items']).($row['printshop']==1 ? '<br/>PRINT SHOP ORDER - Not Editable Here' : '');

            $row['amount_sum']=($row['amount_sum']=='' ? '-' : '$'.number_format($row['amount_sum'],2,'.',','));
            if (floatval($row['profit'])==0) {
                $row['profit']='-';
            } else {
                if ($usrdata['profit_view']=='Points') {
                    $row['profit']=round($row['profit']*$this->config->item('profitpts'),0).' pts';
                } else {
                    $row['profit']=MoneyOutput($row['profit'],2);
                }
                // $row['profit']='$'.number_format($row['profit'],2,'.',',');
            }
            if ($row['order_cog']=='') {
                $row['order_cog']='-';
                $row['profit_class']='projprof';
                $row['profit_perc']='PROJ';
            } else {
                // $row['order_cog']='$'.number_format($row['order_cog'],2,'.',',');
                $row['profit_class']=profit_bgclass($row['profit_perc']);
                $row['profit_perc']=($row['profit_perc']=='' ? '' :  number_format($row['profit_perc'],1,'.',',').'%');
            }
            $row['vendor_name']=($row['vendor_name']=='' ? '&nbsp;' : $row['vendor_name']);
            $row['method_name']=($row['method_name']=='' ? '&nbsp;' : $row['method_name']);
            $row['out_lowprofit']=($row['lowprofit']=='' ? '&nbsp;' : $row['lowprofit']);
            $row['out_reason']=($row['reason']=='' ? '&nbsp;' : $row['reason']);
            // $row['rowclass']=($row['is_closed']==1 ? 'closedorder' : '');
            $row['rowclass']=($row['printshop']==1 ? 'printshoporder' : '');
            $row['out_attach']='<img src="/img/fulfillment/empty_square.png" alt="Empty"/>';
            $row['attclass']='';
            $row['atttitle']='';
            if (intval($row['cnt_att'])!=0) {
                $row['out_attach']='<img src="/img/fulfillment/red_square.png" alt="Exist"/>';
                $row['attclass']='allowattach';
                $row['atttitle']='title="'.$row['cnt_att'].'" Attachments"';
            }
            $out_array[]=$row;
        }
        return $out_array;
    }

//    function get_purchase_order($amount_id) {
//        $this->db->select('oa.*,o.order_num, oa.is_closed, o.profit, o.profit_perc, o.order_cog, o.is_shipping, pay.sumchr, coalesce(oa.amount_sum,0)-coalesce(pay.sumchr,0) as rest',FALSE);
//        $this->db->from('ts_order_amounts oa');
//        $this->db->join('ts_orders o','o.order_id=oa.order_id');
//        $this->db->join('(select amount_id, count(charge_id) as numchr, sum(charge_sum) sumchr from ts_order_charges group by amount_id) pay','pay.amount_id=oa.amount_id','left');
//        $this->db->where("o.is_canceled",0);
//        $this->db->where('oa.amount_id',$amount_id);
//        $res=$this->db->get()->row_array();
//        if (isset($res['order_id'])) {
//            /* Import cog */
//            $this->db->select('sum(amount_sum) as sum_amt',FALSE);
//            $this->db->from('ts_order_amounts');
//            $this->db->where('order_id',$res['order_id']);
//            $amt=$this->db->get()->row_array();
//            $amounts=floatval($amt['sum_amt']);
//            $import_cog=floatval($res['order_cog'])-$amounts;
//            $res['import_cog']=($import_cog==0 ? '' : '$'.number_format($import_cog,2,'.',','));
//            $res['import_label']=($import_cog==0 ? '' : 'Imported COG');
//            $res['closed_view']='/img/'.($res['is_closed']==1 ? 'closed' : 'opened').'.png';
//            if (floatval($res['profit'])==0) {
//                $res['profit']='-';
//            } else {
//                $res['profit']='$'.number_format($res['profit'],2,'.',',');
//            }
//            if ($res['order_cog']=='') {
//                $res['order_cog']='-';
//                $res['profit_class']='projprof';
//                $res['profit_perc']='PROJ';
//            } else {
//                $res['profit_class']=$this->profit_class($res['profit_perc']);
//                $res['profit_perc']=($res['profit_perc']=='' ? '' :  number_format($res['profit_perc'],1,'.',',').'%');
//            }
//
//        }
//
//        return $res;
//    }
//
//    function get_order_bynum($order_num) {
//        $this->db->select('o.order_num, o.order_id, o.is_closed, o.order_cog, o.profit, o.profit_perc, o.is_shipping');
//        $this->db->from('ts_orders o');
//        $this->db->where("o.is_canceled",0);
//        $this->db->where('o.order_num',$order_num);
//        $res=$this->db->get()->row_array();
//        if (isset($res['order_id'])) {
//            $this->db->select('sum(amount_sum) as sum_amt',FALSE);
//            $this->db->from('ts_order_amounts');
//            $this->db->where('order_id',$res['order_id']);
//            $amt=$this->db->get()->row_array();
//            $amounts=floatval($amt['sum_amt']);
//            $import_cog=floatval($res['order_cog'])-$amounts;
//            $res['import_cog']=($import_cog==0 ? '' : '$'.number_format($import_cog,2,'.',','));
//            $res['import_label']=($import_cog==0 ? '' : 'Imported COG');
//            $res['closed_view']='<img src="/img/'.($res['is_closed']==1 ? 'closed' : 'opened').'.png"/>';
//            if (floatval($res['profit'])==0) {
//                $res['profit']='-';
//            } else {
//                $res['profit']='$'.number_format($res['profit'],2,'.',',');
//            }
//            if ($res['order_cog']=='') {
//                $res['profit_class']='projprof';
//                $res['profit_perc']='PROJ';
//            } else {
//                $res['profit_class']=$this->profit_class($res['profit_perc']);
//                $res['profit_perc']=($res['profit_perc']=='' ? '' :  number_format($res['profit_perc'],1,'.',',').'%');
//            }
//        }
//
//        return $res;
//    }
//
//    public function save_poamount($amtdata) {
//        /* $amount_id,$order_id,$amount_date,$amount_sum,$vendor_id, $method_id,$user_id,$is_shipping,$attach */
//        $res=array('result'=>0,'msg'=>'Unknown error');
//        $data=$amtdata['amount'];
//        $attach=$amtdata['attach'];
//        $amount_id=$data['amount_id'];
//        $order_id=$data['order_id'];
//        $amount_date=$data['amount_date'];
//        $amount_sum=floatval($data['amount_sum']);
//        $vendor_id=$data['vendor_id'];
//        $method_id=$data['method_id'];
//        $user_id=$amtdata['user_id'];
//        $is_shipping=(isset($data['is_shipping']) ? $data['is_shipping'] : 0);
//        $old_amount_sum=$data['oldamount_sum'];
//        $profperc=floatval($amtdata['order']['profit_perc']);
//
//        if (!$order_id) {
//            $res['msg']='Non-exist PO#';
//            return $res;
//        }
//        if (floatval($amount_sum)==0) {
//            $res['msg']='Amount sum not entered';
//            return $res;
//        }
//        if(intval($vendor_id)==0) {
//            $res['msg']='Select Vendor';
//            return $res;
//        }
//        if (intval($method_id)==0) {
//            $res['msg']='Select Purchase Method';
//            return $res;
//        }
//        // Special data
//        if ($amount_id && ($old_amount_sum!=$amount_sum) && empty($data['reason'])) {
//            $res['msg']='Enter Reason for Change PO Amount';
//            return $res;
//        }
//        if ($profperc<$this->config->item('minimal_profitperc') && empty($data['lowprofit'])) {
//            $res['msg']='Enter Reason for Low Profit';
//            return $res;
//        }
//        $this->db->select('order_cog, revenue, profit, shipping, tax, cc_fee, order_num, order_date');
//        $this->db->from('ts_orders');
//        $this->db->where('order_id',$order_id);
//        $cog=$this->db->get()->row_array();
//        $order_cog=floatval($cog['order_cog']);
//        $revenue=floatval($cog['revenue']);
//        $shipping=floatval($cog['shipping']);
//        $tax=floatval($cog['tax']);
//        $cc_fee=floatval($cog['cc_fee']);
//        $order_num='BT'.$cog['order_num'];
//        $order_date=$cog['order_date'];
//        $profit=floatval($cog['profit']);
//        /* Insert update Amount */
//        // $this->db->set('amount_date', strtotime($amount_date));
//        $this->db->set('amount_date', $amount_date);
//        $this->db->set('vendor_id',$vendor_id);
//        $this->db->set('method_id',$method_id);
//        $this->db->set('amount_sum', $amount_sum);
//        if (isset($data['reason'])) {
//            $this->db->set('reason', $data['reason']);
//        }
//        if ($amount_id==0) {
//            $this->db->set('order_id',$order_id);
//            $this->db->set('create_date', time());
//            $this->db->set('create_user',$user_id);
//            $this->db->set('update_date',time());
//            $this->db->set('update_user',$user_id);
//            $this->db->insert('ts_order_amounts');
//            $resins=$this->db->insert_id();
//            $amount_id=$resins;
//        } else {
//            $this->db->set('update_date',time());
//            $this->db->set('update_user',$user_id);
//            $this->db->where('amount_id',$amount_id);
//            $this->db->update('ts_order_amounts');
//            $resins=1;
//        }
//        if ($resins) {
//            $res['msg']='';
//            $res['result']=1;
//            /* Update order */
//            $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
//            $this->db->from('ts_orders');
//            $this->db->where('order_id',$order_id);
//            $statres=$this->db->get()->row_array();
//            $new_order_cog=$order_cog-$old_amount_sum+$amount_sum;
//            $new_profit=$revenue-($shipping*$is_shipping)-$tax-$cc_fee-$new_order_cog;
//            $new_profit_pc=($revenue==0 ? null : round(($new_profit/$revenue)*100,1));
//            $this->db->set('order_cog',$new_order_cog);
//            $this->db->set('is_shipping',$is_shipping);
//            $this->db->set('profit',$new_profit);
//            $this->db->set('profit_perc',$new_profit_pc);
//            $this->db->set('order_artview', $statres['aprrovview']);
//            $this->db->set('order_placed', $statres['placeord']);
//            if ($new_profit_pc<$this->config->item('minimal_profitperc') && isset($data['lowprofit'])) {
//                $this->db->set('reason', $data['lowprofit']);
//            }
//            $this->db->where('order_id',$order_id);
//            $this->db->update('ts_orders');
//            /* Insert attachments */
//            $this->db->where('amount_id',$amount_id);
//            $this->db->delete('ts_amount_docs');
//            /* Insert new/old data */
//            foreach ($attach as $row) {
//                $fl_avail=0;
//                if (stripos($this->config->item('pathpreload'),$row['doc_link'])) {
//                    /* new file */
//                    $filename=str_replace($this->config->item('pathpreload'), '', $row['doc_link']);
//                    $dest=$this->config->item('amountattach');
//                    $src=$this->config->item('upload_path_preload');
//                    $res=$this->func->move_docfile($filename, $src, $dest);
//                    if ($res) {
//                        $fl_avail=1;
//                        $row['doc_link']=$this->config->item('amountattach_path').$filename;
//                    }
//                } else {
//                    $fl_avail=1;
//                }
//                if ($fl_avail) {
//                    $dat=date('Y-m-d H:i:s',strtotime($row['upd_time']));
//                    $this->db->set('upd_time',$dat);
//                    $this->db->set('amount_id',$amount_id);
//                    $this->db->set('doc_link',$row['doc_link']);
//                    $this->db->set('doc_name',$row['doc_name']);
//                    $this->db->insert('ts_amount_docs');
//                }
//            }
//            if ($old_amount_sum!=0 && $old_amount_sum!=$amount_sum) {
//                $notifoptions=array(
//                    'order_num'=>$order_num,
//                    'old_amount_sum'=>$old_amount_sum,
//                    'amount_sum'=>$amount_sum,
//                    'comment'=>(isset($data['comment']) ? $data['comment'] : ''),
//                    'user_id'=>$user_id,
//                );
//                $this->notification_pochange_email($notifoptions);
//            }
//            if ($old_amount_sum!=$amount_sum) {
//                $this->load->model('order_model');
//
//                /* get netprofit */
//                $this->db->select('np.*, netprofit_profit(datebgn, dateend) as gross_profit',FALSE);
//                $this->db->from('netprofit np');
//                $this->db->where('np.profit_month',NULL);
//                $this->db->where('np.datebgn <= ',$order_date);
//                $this->db->where('np.dateend > ',$order_date);
//                $netdat=$this->db->get()->row_array();
//                if (isset($netdat['profit_id']) && $netdat['debtinclude']==1) {
//                    $this->load->model('balances_model');
//                    $total_options=array(
//                        'type'=>'week',
//                        'start'=>$this->config->item('netprofit_start'),
//                    );
//                    $rundat=$this->balances_model->get_netprofit_runs($total_options);
//                    $newtotalrun=$rundat['out_debtval'];
//                    $oldtotalrun=$newtotalrun-($new_profit-$profit);
//                    if ($oldtotalrun<0) {
//                        $outoldrundebt='($'.number_format(abs($oldtotalrun),0,'.',',').')';
//                    } else {
//                        $outoldrundebt='$'.number_format($oldtotalrun,0,'.',',');
//                    }
//                    if ($newtotalrun<0) {
//                        $outnewrundebt='($'.number_format(abs($newtotalrun),0,'.',',').')';
//                    } else {
//                        $outnewrundebt='$'.number_format($newtotalrun,0,'.',',');
//                    }
//
//                    $totalcost=floatval($netdat['profit_operating'])+floatval($netdat['profit_payroll'])+floatval($netdat['profit_advertising'])+floatval($netdat['profit_projects'])+floatval($netdat['profit_purchases']);
//                    $netprofit=floatval($netdat['gross_profit'])-$totalcost;
//                    $newdebt=floatval($netprofit)-floatval($netdat['profit_owners'])-floatval($netdat['profit_saved'])-floatval($netdat['od2']);
//                    if ($newdebt<0) {
//                        $outnewdebt='($'.number_format(abs($newdebt),0,'.',',').')';
//                    } else {
//                        $outnewdebt='$'.number_format($newdebt,0,'.',',');
//                    }
//                    $olddebt=$newdebt-($new_profit-$profit);
//                    if ($olddebt<0) {
//                        $outolddebt='($'.number_format(abs($olddebt),0,'.',',').')';
//                    } else {
//                        $outolddebt='$'.number_format(abs($olddebt),0,'.',',');
//                    }
//                    $start_month=date('M',$netdat['datebgn']);
//                    $start_year=date('Y',$netdat['datebgn']);
//                    $end_month=date('M',$netdat['dateend']);
//                    $end_year=date('Y',$netdat['dateend']);
//                    if ($start_month!=$end_month) {
//                        $weekname=$start_month.'/'.$end_month;
//                    } else {
//                        $weekname=$start_month;
//                    }
//                    $weekname.=' '.date('j',$netdat['datebgn']).'-'.date('j',$netdat['dateend']);
//                    if ($start_year!=$end_year) {
//                        $weekname.=' '.$start_year.'/'.date('y',$netdat['dateend']);
//                    } else {
//                        $weekname.=', '.$start_year;
//                    }
//                    $notifoptions=array(
//                        'pochange'=>1,
//                        'order_num'=>$order_num,
//                        'old_amount_sum'=>$old_amount_sum,
//                        'amount_sum'=>$amount_sum,
//                        'olddebt'=>$outolddebt,
//                        'newdebt'=>$outnewdebt,
//                        'weeknum'=>$weekname,
//                        'user_id'=>$user_id,
//                        'comment'=>(isset($data['comment']) ? $data['comment'] : ''),
//                        'oldtotalrun'=>$outoldrundebt,
//                        'newtotalrun'=>$outnewrundebt,
//                    );
//                    $this->order_model->notify_netdebtchanged($notifoptions);
//                }
//            }
//        } else {
//            $res['msg']='New amount not inserted';
//        }
//        return $res;
//    }
//
//    function notification_pochange_email($options) {
//        $this->load->model('user_model');
//        if (isset($options['amount_delete'])) {
//            $msg_subj='PO '.$options['order_num'].' removed';
//        } else {
//            $msg_subj='PO '.$options['order_num'].' changed';
//        }
//
//        $email_body='At '.date('h:i a').' on '.date('m/d/y');
//        $usrdat=$this->user_model->get_user_data($options['user_id']);
//        $email_body.=' '.$usrdat['user_name'].' changed PO '.$options['order_num'].' from $'.number_format($options['old_amount_sum'],2,'.','');
//        $email_body.=' to $'.number_format($options['amount_sum'],2,'.','');
//        if (isset($options['amount_delete'])) {
//            $email_body.=' and delete amount';
//        }
//        if (isset($options['comment']) && $options['comment']) {
//            $email_body.=PHP_EOL.'Reason '.$options['comment'];
//        }
//        $this->load->library('email');
//        $config = $this->config->item('email_setup');
//        $config['mailtype'] = 'text';
//        $this->email->initialize($config);
//        $this->email->set_newline("\r\n");
//        $this->email->to($this->config->item('sean_email'));
//        $from = $this->config->item('email_notification_sender');
//        $this->email->from($from);
//        $this->email->subject($msg_subj);
//        $this->email->message($email_body);
//        if (isset($options['amount_delete'])) {
//            $this->email->send();
//        }
//        $this->email->clear(TRUE);
//        return TRUE;
//    }
//
//    function save_amount($amount_id,$order_id,$amount_date,$amount_sum,$vendor_id, $method_id,$user_id,$is_shipping,$attach) {
//        $res=array('result'=>0,'msg'=>'Unknown error');
//        /* Check Incoming Data */
//        if (!$order_id) {
//            $res['msg']='Non-exist PO#';
//        } elseif (floatval($amount_sum)==0) {
//            $res['msg']='Amount sum not entered';
//        } elseif(intval($vendor_id)==0) {
//            $res['msg']='Select Vendor';
//        } elseif (intval($method_id)==0) {
//            $res['msg']='Select Purchase Method';
//        } else {
//            $this->db->select('order_cog, revenue, shipping, tax, cc_fee');
//            $this->db->from('ts_orders');
//            $this->db->where('order_id',$order_id);
//            $cog=$this->db->get()->row_array();
//            $order_cog=floatval($cog['order_cog']);
//            $revenue=floatval($cog['revenue']);
//            $shipping=floatval($cog['shipping']);
//            $tax=floatval($cog['tax']);
//            $cc_fee=floatval($cog['cc_fee']);
//            if ($amount_id==0) {
//                $old_amount_sum=0;
//            } else {
//                $this->db->select('amount_sum');
//                $this->db->from('ts_order_amounts');
//                $this->db->where('amount_id',$amount_id);
//                $amn=$this->db->get()->row_array();
//                $old_amount_sum=floatval($amn['amount_sum']);
//            }
//            /* Insert update Amount */
//            $this->db->set('amount_date', strtotime($amount_date));
//            $this->db->set('vendor_id',$vendor_id);
//            $this->db->set('method_id',$method_id);
//            $this->db->set('amount_sum', floatval($amount_sum));
//            if ($amount_id==0) {
//                $this->db->set('order_id',$order_id);
//                $this->db->set('create_date', time());
//                $this->db->set('create_user',$user_id);
//                $this->db->set('update_date',time());
//                $this->db->set('update_user',$user_id);
//                $this->db->insert('ts_order_amounts');
//                $resins=$this->db->insert_id();
//                $amount_id=$resins;
//            } else {
//                $this->db->set('update_date',time());
//                $this->db->set('update_user',$user_id);
//                $this->db->where('amount_id',$amount_id);
//                $this->db->update('ts_order_amounts');
//                $resins=1;
//            }
//            if ($resins) {
//                $res['msg']='';
//                $res['result']=1;
//                /* Update order */
//                $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
//                $this->db->from('ts_orders');
//                $this->db->where('order_id',$order_id);
//                $statres=$this->db->get()->row_array();
//                $new_order_cog=$order_cog-$old_amount_sum+floatval($amount_sum);
//                $new_profit=$revenue-($shipping*$is_shipping)-$tax-$cc_fee-$new_order_cog;
//                $new_profit_pc=($revenue==0 ? null : round(($new_profit/$revenue)*100,1));
//                $this->db->set('order_cog',$new_order_cog);
//                $this->db->set('is_shipping',$is_shipping);
//                $this->db->set('profit',$new_profit);
//                $this->db->set('profit_perc',$new_profit_pc);
//                $this->db->set('order_artview', $statres['aprrovview']);
//                $this->db->set('order_placed', $statres['placeord']);
//                $this->db->where('order_id',$order_id);
//                $this->db->update('ts_orders');
//                /* Insert attachments */
//                $this->db->where('amount_id',$amount_id);
//                $this->db->delete('ts_amount_docs');
//                /* Insert new/old data */
//                foreach ($attach as $row) {
//                    $fl_avail=0;
//                    if (stripos($this->config->item('pathpreload'),$row['doc_link'])) {
//                        /* new file */
//                        $filename=str_replace($this->config->item('pathpreload'), '', $row['doc_link']);
//                        $dest=$this->config->item('amountattach');
//                        $src=$this->config->item('upload_path_preload');
//                        $res=$this->func->move_docfile($filename, $src, $dest);
//                        if ($res) {
//                            $fl_avail=1;
//                            $row['doc_link']=$this->config->item('amountattach_path').$filename;
//                        }
//                    } else {
//                        $fl_avail=1;
//                    }
//                    if ($fl_avail) {
//                        $dat=date('Y-m-d H:i:s',strtotime($row['upd_time']));
//                        $this->db->set('upd_time',$dat);
//                        $this->db->set('amount_id',$amount_id);
//                        $this->db->set('doc_link',$row['doc_link']);
//                        $this->db->set('doc_name',$row['doc_name']);
//                        $this->db->insert('ts_amount_docs');
//                    }
//                }
//            } else {
//                $res['msg']='New amount not inserted';
//            }
//
//        }
//        return $res;
//    }
//
//    function get_order_payments($order_id) {
//        $this->db->select('*');
//        $this->db->from('ts_order_charges');
//        $this->db->where('order_id',$order_id);
//        $this->db->order_by('charge_date');
//        $res=$this->db->get()->result_array();
//        $out_array=array();
//        foreach ($res as $row) {
//            $row['charge_date']=($row['charge_date']=='' ? '-' : date('m/d/Y',$row['charge_date']));
//            $row['charge_sum']=(floatval($row['charge_sum'])==0 ? '-' : number_format($row['charge_sum'],2,'.',','));
//            $out_array[]=$row;
//        }
//        return $out_array;
//    }
//
//    function get_order_details($order_id) {
//        $this->db->select('o.*, pay.numchr, pay.sumchr');
//        $this->db->from('ts_orders o');
//        $this->db->join('(select order_id, count(charge_id) as numchr, sum(charge_sum) sumchr from ts_order_charges group by order_id) pay','pay.order_id=o.order_id','left');
//        $this->db->where('o.order_id',$order_id);
//        $order_dat=$this->db->get()->row_array();
//        if (isset($order_dat['order_id'])) {
//            /* Rest */
//            $order_dat['rest']=floatval($order_dat['order_cog'])-floatval($order_dat['sumchr']);
//            $order_dat['rest']=number_format($order_dat['rest'],2,'.',',');
//        }
//        return $order_dat;
//    }
//
//    function get_charge_data($charge_id) {
//        $this->db->select('*');
//        $this->db->from('ts_order_charges');
//        $this->db->where('charge_id',$charge_id);
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    function delete_amount($amount_id, $user_id) {
//        $outres=0;
//        $def_profit=$this->config->item('default_profit');
//        $this->db->select('o.*, oa.amount_sum');
//        $this->db->from('ts_order_amounts oa');
//        $this->db->join('ts_orders o','o.order_id=oa.order_id');
//        $this->db->where('amount_id',$amount_id);
//        $res=$this->db->get()->row_array();
//        if (isset($res['order_id'])) {
//            $this->db->select('np.*, netprofit_profit(datebgn, dateend) as gross_profit',FALSE);
//            $this->db->from('netprofit np');
//            $this->db->where('np.profit_month',NULL);
//            $this->db->where('np.datebgn <= ',$res['order_date']);
//            $this->db->where('np.dateend > ',$res['order_date']);
//            $netdat=$this->db->get()->row_array();
//            $flag_note=0;
//            if (isset($netdat['profit_id']) && $netdat['debtinclude']==1) {
//                $flag_note=1;
//                // Get Old Running
//                $this->load->model('balances_model');
//                $total_options=array(
//                    'type'=>'week',
//                    'start'=>$this->config->item('netprofit_start'),
//                );
//                $rundat=$this->balances_model->get_netprofit_runs($total_options);
//                $oldtotalrun=$rundat['out_debtval'];
//                $totalcost=floatval($netdat['profit_operating'])+floatval($netdat['profit_payroll'])+floatval($netdat['profit_advertising'])+floatval($netdat['profit_projects'])+floatval($netdat['profit_purchases']);
//                $netprofit=floatval($netdat['gross_profit'])-$totalcost;
//                $olddebt=floatval($netprofit)-floatval($netdat['profit_owners'])-floatval($netdat['profit_saved'])-floatval($netdat['od2']);
//                $netdat_id=$netdat['profit_id'];
//            }
//            /* Get Old data about Running Totals */
//            $order_id=$res['order_id'];
//            $revenue=$res['revenue'];
//            $shipping=$res['shipping'];
//            $is_shipping=$res['is_shipping'];
//            $tax=$res['tax'];
//            $cc_fee=$res['cc_fee'];
//            $order_cog=$res['order_cog'];
//            $amount_sum=$res['amount_sum'];
//            $order_num='BT'.$res['order_num'];
//            if ($order_cog!='') {
//                $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
//                $this->db->from('ts_orders');
//                $this->db->where('order_id',$order_id);
//                $statres=$this->db->get()->row_array();
//                if ($order_cog==$amount_sum) {
//                    $new_cog=null;
//                    /* Calc profit as project */
//                    $profit=round($revenue*$def_profit/100,2);
//                    $profit_perc=null;
//                    $this->db->set('order_cog',$new_cog);
//                    $this->db->set('profit',$profit);
//                    $this->db->set('profit_perc',$profit_perc);
//                    $this->db->set('order_artview', $statres['aprrovview']);
//                    $this->db->set('order_placed', $statres['placeord']);
//                    $this->db->where('order_id',$order_id);
//                    $this->db->update('ts_orders');
//                    $this->db->where('amount_id',$amount_id);
//                    $this->db->delete('ts_order_amounts');
//                    $outres=$this->db->affected_rows();
//                } elseif($order_cog>$amount_sum) {
//                    $new_cog=$order_cog-$amount_sum;
//                    $cost=floatval($shipping)*$is_shipping+floatval($tax)+floatval($cc_fee)+floatval($new_cog);
//                    $profit=round($revenue-$cost,2);
//                    $profit_perc=round($profit/$revenue*100,1);
//                    $this->db->set('order_cog',$new_cog);
//                    $this->db->set('profit',$profit);
//                    $this->db->set('profit_perc',$profit_perc);
//                    $this->db->set('order_artview', $statres['aprrovview']);
//                    $this->db->set('order_placed', $statres['placeord']);
//                    $this->db->where('order_id',$order_id);
//                    $this->db->update('ts_orders');
//                    $this->db->where('amount_id',$amount_id);
//                    $this->db->delete('ts_order_amounts');
//                    $outres=$this->db->affected_rows();
//                }
//                if ($outres) {
//                    $notifoptions=array(
//                        'order_num'=>$order_num,
//                        'old_amount_sum'=>$amount_sum,
//                        'amount_sum'=>0,
//                        'user_id'=>$user_id,
//                        'amount_delete'=>1,
//                    );
//                    $this->notification_pochange_email($notifoptions);
//                    if ($flag_note==1) {
//                        // Get new Run value, etc
//                        $total_options=array(
//                            'type'=>'week',
//                            'start'=>$this->config->item('netprofit_start'),
//                        );
//                        $rundat=$this->balances_model->get_netprofit_runs($total_options);
//                        $newtotalrun=$rundat['out_debtval'];
//                        /* Prepare to notification Email */
//                        $start_month=date('M',$netdat['datebgn']);
//                        $start_year=date('Y',$netdat['datebgn']);
//                        $end_month=date('M',$netdat['dateend']);
//                        $end_year=date('Y',$netdat['dateend']);
//                        if ($start_month!=$end_month) {
//                            $weekname=$start_month.'/'.$end_month;
//                        } else {
//                            $weekname=$start_month;
//                        }
//                        $weekname.=' '.date('j',$netdat['datebgn']).'-'.date('j',$netdat['dateend']);
//                        if ($start_year!=$end_year) {
//                            $weekname.=' '.$start_year.'/'.date('y',$netdat['dateend']);
//                        } else {
//                            $weekname.=', '.$start_year;
//                        }
//                        if ($oldtotalrun<0) {
//                            $outoldrundebt='($'.number_format(abs($oldtotalrun),0,'.',',').')';
//                        } else {
//                            $outoldrundebt='$'.number_format($oldtotalrun,0,'.',',');
//                        }
//                        if ($newtotalrun<0) {
//                            $outnewrundebt='($'.number_format(abs($newtotalrun),0,'.',',').')';
//                        } else {
//                            $outnewrundebt='$'.number_format($newtotalrun,0,'.',',');
//                        }
//                        $this->db->select('np.*, netprofit_profit(datebgn, dateend) as gross_profit',FALSE);
//                        $this->db->from('netprofit np');
//                        $this->db->where('np.profit_id',$netdat_id);
//                        $netdata=$this->db->get()->row_array();
//                        $totalcost=floatval($netdata['profit_operating'])+floatval($netdata['profit_payroll'])+floatval($netdata['profit_advertising'])+floatval($netdata['profit_projects'])+floatval($netdata['profit_purchases']);
//                        $netprofit=floatval($netdata['gross_profit'])-$totalcost;
//                        $newdebt=floatval($netprofit)-floatval($netdata['profit_owners'])-floatval($netdata['profit_saved'])-floatval($netdata['od2']);
//                        if ($newdebt<0) {
//                            $outnewdebt='($'.number_format(abs($newdebt),0,'.',',').')';
//                        } else {
//                            $outnewdebt='$'.number_format($newdebt,0,'.',',');
//                        }
//                        if ($olddebt<0) {
//                            $outolddebt='($'.number_format(abs($olddebt),0,'.',',').')';
//                        } else {
//                            $outolddebt='$'.number_format(abs($olddebt),0,'.',',');
//                        }
//
//                        $notifoptions=array(
//                            'podelete'=>1,
//                            'order_num'=>$order_num,
//                            'old_amount_sum'=>$amount_sum,
//                            'weeknum'=>$weekname,
//                            'user_id'=>$user_id,
//                            'oldtotalrun'=>$outoldrundebt,
//                            'newtotalrun'=>$outnewrundebt,
//                            'olddebt'=>$outolddebt,
//                            'newdebt'=>$outnewdebt,
//                        );
//                        $this->load->model('order_model');
//                        $this->order_model->notify_netdebtchanged($notifoptions);
//
//                    }
//                }
//            }
//        }
//        return $outres;
//    }
//
//    function delete_payment($charge_id,$amount_id) {
//        $this->db->where('charge_id',$charge_id);
//        $this->db->delete('ts_order_charges');
//        $res=$this->db->affected_rows();
//        if ($res!=0) {
//            $this->db->select('max(charge_date) as max_charge');
//            $this->db->from('ts_order_charges');
//            $this->db->where('amount_id',$amount_id);
//            $dat=$this->db->get()->row_array();
//
//            $data=$this->get_amount_details($amount_id);
//            if ($data['rest_num']<=0) {
//                $this->db->set('is_closed',1);
//            } else {
//                $this->db->set('is_closed',0);
//            }
//            $this->db->set('date_charge',$dat['max_charge']);
//            $this->db->where('amount_id',$amount_id);
//            $this->db->update('ts_order_amounts');
//        }
//
//        return $res;
//    }
//
//
//    function save_charge($charge_id, $amount_id, $charge_date, $charge_sum, $user_id) {
//        $res=array('result'=>0, 'msg'=>'Unknown Error');
//
//        /* check parameters */
//        if (!$amount_id) {
//            $res['msg']='Unknown PO';
//        } elseif (!$charge_date) {
//            $res['msg']='Fill charge date';
//        } elseif (floatval($charge_sum)==0) {
//            $res['msg']='Fill charge sum';
//        } elseif (!$user_id) {
//            $res['msg']='You have no permissions to make this operation';
//        } else {
//            $this->db->set('charge_date', strtotime($charge_date));
//            $this->db->set('charge_sum', floatval($charge_sum));
//            if ($charge_id==0) {
//                $this->db->set('amount_id',$amount_id);
//                $this->db->set('create_time', time());
//                $this->db->set('create_user',$user_id);
//                $this->db->set('update_time',time());
//                $this->db->set('update_user',$user_id);
//                $this->db->insert('ts_order_charges');
//                $resins=$this->db->insert_id();
//            } else {
//                $this->db->set('update_time',time());
//                $this->db->set('update_user',$user_id);
//                $this->db->where('charge_id',$charge_id);
//                $this->db->update('ts_order_charges');
//                $resins=1;
//            }
//            if ($resins) {
//                $res['result']=1;$res['msg']='';
//
//                $this->db->select('max(charge_date) as max_charge');
//                $this->db->from('ts_order_charges');
//                $this->db->where('amount_id',$amount_id);
//                $dat=$this->db->get()->row_array();
//                /* Auto cloase */
//                $data=$this->get_amount_details($amount_id);
//                if ($data['rest_num']<=0) {
//                    $isclose=1;
//                } else {
//                    $isclose=0;
//                }
//                if ($isclose==1) {
//                    $this->db->set('is_closed',1);
//                }
//                $this->db->set('date_charge',$dat['max_charge']);
//                $this->db->where('amount_id',$amount_id);
//                $this->db->update('ts_order_amounts');
//            } else {
//                $res['msg']='Error during insert data';
//            }
//        }
//        return $res;
//    }
//
//    function get_amount_details($amount_id) {
//        $this->db->select('oa.*,o.order_num, pay.numchr, pay.sumchr');
//        $this->db->from('ts_order_amounts oa');
//        $this->db->join('ts_orders o','o.order_id=oa.order_id');
//        $this->db->join('(select amount_id, count(charge_id) as numchr, sum(charge_sum) sumchr from ts_order_charges group by amount_id) pay','pay.amount_id=oa.amount_id','left');
//        $this->db->where('oa.amount_id',$amount_id);
//        $amount_dat=$this->db->get()->row_array();
//        if (isset($amount_dat['amount_id'])) {
//            /* Rest */
//            $amount_dat['rest_num']=floatval($amount_dat['amount_sum'])-floatval($amount_dat['sumchr']);
//            $amount_dat['rest']='$'.number_format($amount_dat['rest_num'],2,'.',',');
//        }
//        return $amount_dat;
//    }
//
//    function get_amount_payments($amount_id) {
//        $this->db->select('*');
//        $this->db->from('ts_order_charges');
//        $this->db->where('amount_id',$amount_id);
//        $this->db->order_by('charge_date');
//        $res=$this->db->get()->result_array();
//        $out_array=array();
//        foreach ($res as $row) {
//            $row['charge_date']=($row['charge_date']=='' ? '-' : date('m/d/Y',$row['charge_date']));
//            $row['charge_sum']=(floatval($row['charge_sum'])==0 ? '-' : number_format($row['charge_sum'],2,'.',','));
//            $out_array[]=$row;
//        }
//        return $out_array;
//    }
//
//    function save_status($amount_id, $order_status, $order_replica,$user_id) {
//        $res=array('result'=>  Order_model::ERR_FLAG,'msg'=>'Unknown Error');
//        $this->db->set('is_closed',$order_status);
//        $this->db->set('order_replica',  strtoupper($order_replica));
//        $this->db->set('update_user',$user_id);
//        $this->db->where('amount_id',$amount_id);
//        $this->db->update('ts_order_amounts');
//
//        $res['result']=0;
//        $res['msg']='';
//        return $res;
//
//    }
//
//    public function profit_class($profit_perc) {
//        $profit_class='';
//        if (round($profit_perc,0)<=0) {
//            $profit_class='black';
//        } elseif ($profit_perc>0 && $profit_perc<10) {
//            $profit_class='moroon';
//        } elseif ($profit_perc>=10 && $profit_perc<20) {
//            $profit_class='red';
//        } elseif ($profit_perc>=20 && $profit_perc<30) {
//            $profit_class='orange';
//        } elseif ($profit_perc>=30 && $profit_perc<40) {
//            $profit_class='white';
//        } elseif ($profit_perc>=40) {
//            $profit_class='green';
//        }
//
//        return $profit_class;
//    }
//
//    function get_batches() {
//        return array();
//    }
//
//    function get_amount_attachments($amount_id) {
//        $this->db->select('*');
//        $this->db->from('ts_amount_docs');
//        $this->db->where('amount_id',$amount_id);
//        $res=$this->db->get()->result_array();
//        return $res;
//    }

    public function get_vendorpayment($brand, $year=2014) {
        $start=strtotime($year.'-01-01');
        $finish=strtotime($year.'-12-31 23:59:59');
        // Get PO by checked Vendors
        $this->load->model('vendors_model');
        $vendlist=$this->vendors_model->vendors_included();
        $paym=array();
        if (count($vendlist)!=0) {
            $this->db->select('oa.vendor_id, sum(oa.amount_sum) as paysum ');
            $this->db->from('ts_order_amounts oa');
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
            $this->db->where('oa.amount_date >=', $start);
            $this->db->where('oa.amount_date < ', $finish);
            $this->db->where('o.is_canceled',0);
            $this->db->where('v.payinclude',1);
            if ($brand!=='ALL') {
                $this->db->where('o.brand', $brand);
            }
            $this->db->group_by('oa.vendor_id');
            $vpays=$this->db->get()->result_array();
            foreach ($vendlist as $row) {
                $sum=0;
                foreach ($vpays as $prow) {
                    if ($prow['vendor_id']==$row['vendor_id']) {
                        $sum=$prow['paysum'];
                        break;
                    }
                }
                $paym[]=array(
                    'vendor'=>($row['vendor_name']=='BLUETRACK Warehouse' ? 'BT Warehouse' : $row['vendor_name']),
                    'pay'=>($sum==0 ? '---' : MoneyOutput($sum,2)),
                );
            }
        }
        // Other vendors payment
        $this->db->select('sum(revenue) as paysum');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >=', $start);
        $this->db->where('o.order_date < ', $finish);
        $this->db->where('order_placedflag(o.order_id)',0);
        $resplace=$this->db->get()->row_array();
        $sum=floatval($resplace['paysum']);
        $paym[]=array(
            'vendor'=>'TO PLACE',
            'pay'=>($sum==0 ? '---' : MoneyOutput($sum,2)),
        );
        return $paym;
    }

    public function get_years($brand) {
        $this->db->select("date_format(from_unixtime(oa.amount_date),'%Y') as pay_year, count(oa.amount_id) as cnt",FALSE);
        $this->db->from('ts_order_amounts oa');
        if ($brand!=='ALL') {
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            $this->db->where('o.brand', $brand);
        }
        $this->db->group_by('pay_year');
        $this->db->order_by('pay_year','desc');
        $list=$this->db->get()->result_array();
        $years=array();
        foreach ($list as $row) {
            array_push($years, $row['pay_year']);
        }
        if (count($years)==0) {
            array_push(date('Y'));
        }
        return $years;
    }

//    // Change session saved data of amount
//    public function change_amount($data, $fld, $value) {
//        $out=array('result'=>  Payments_model::ERR_FLAG, 'msg'=> 'Unknown Error');
//        $this->load->model('order_model');
//        $amount_data=$data['amount'];
//
//        $order_id=$amount_data['order_id'];
//        $order_data=$this->order_model->get_order_detail($order_id);
//        // Make changes
//        $edit=0;
//        $reason_view='';
//        $rescalc=$this->recalc_orderprofit($order_data, $amount_data);
//        $profval=$rescalc['profval'];
//        $profperc=$rescalc['profperc'];
//        $profclass=$rescalc['profclass'];
//        switch ($fld) {
//            case 'amount_date':
//                $amount_data['amount_date']=  strtotime($value);
//                $edit=1;
//                break;
//            case 'amount_sum':
//                $edit=1;
//                $newamount=floatval($value);
//                $amount_data['amount_sum']=$newamount;
//                // Recalc Order Profit,
//                $rescalc=$this->recalc_orderprofit($order_data, $amount_data);
//                $profval=$rescalc['profval'];
//                $profperc=$rescalc['profperc'];
//                $profclass=$rescalc['profclass'];
//                break;
//            case 'reason':
//                $edit=1;
//                $amount_data['lowprofit']=$value;
//                break;
//            case 'is_shipping':
//                $edit=1;
//                $amount_data['is_shipping']=$value;
//                // Recalc Order Profit,
//                $rescalc=$this->recalc_orderprofit($order_data, $amount_data);
//                $profval=$rescalc['profval'];
//                $profperc=$rescalc['profperc'];
//                $profclass=$rescalc['profclass'];
//                break;
//            case 'vendor_id':
//                $edit=1;
//                $amount_data['vendor_id']=$value;
//                break;
//            case 'method_id':
//                $edit=1;
//                $amount_data['method_id']=$value;
//                break;
//            case 'comment':
//                $edit=1;
//                $amount_data['reason']=$value;
//                break;
//            case 'low_profit':
//                $edit=1;
//                $amount_data['lowprofit']=$value;
//                break;
//            default :
//                break;
//        }
//        if ($edit==0) {
//            $out['msg']='Field Not Found';
//        } else {
//            if (is_numeric($profperc) && $profperc<$this->config->item('minimal_profitperc')) {
//                $options=array(
//                    'reason'=>$amount_data['lowprofit'],
//                );
//                $reason_view=$this->load->view('purchase_orders/lowprofit_reason_view', $options,TRUE);
//            }
//            // Save in temporary array
//            $order_data['profit_class']=$profclass;
//            $order_data['profit_perc']=$profperc;
//            $order_data['profit']=$profval;
//            $newdata=array(
//                'amount'=>$amount_data,
//                'order'=>$order_data,
//                'attach'=>$data['attach'],
//            );
//            $this->func->session('editpurchase', $newdata);
//            $out['result']=  Payments_model::SUCCESS_RESULT;
//            $out['msg']='';
//            $out['profit_class']=$profclass;
//            $out['profit_perc']=($profperc=='' ? 'Proj' : $profperc.' %');
//            $out['profit']=$profval;
//            $out['reason']=$reason_view;
//        }
//        return $out;
//    }
//
//    private function recalc_orderprofit($order_data, $amount_data) {
//        $out=array();
//        $out['profval']=$order_data['profit'];
//        $out['profperc']='';
//        $out['profclass']='projprof';
//        if ($order_data['order_cog']==0 && $amount_data['amount_sum']==0) {
//        } else {
//            $costs=floatval($amount_data['is_shipping'])*$order_data['shipping']+floatval($order_data['tax'])+floatval($order_data['cc_fee'])+
//                floatval($amount_data['amount_sum']-$amount_data['oldamount_sum'])+floatval($order_data['order_cog']);
//            $out['profval']=round(floatval($order_data['revenue'])-$costs,2);
//            $out['profperc']=round(($out['profval']/$order_data['revenue'])*100,1);
//            $out['profclass']=$this->profit_class($out['profperc']);
//        }
//        return $out;
//    }

}