<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Leads_model extends MY_Model
{

    private $INIT_ERRMSG = 'Unknown error. Try later';
    private $init_number = 10000;
    private $init_lead_type = 2;

    function __construct()
    {
        parent::__construct();
    }

    // Total records
    public function get_total_leads($options) {
        $this->db->select('count(l.lead_id) as cnt');
        $this->db->from('ts_leads l');
        if (isset($options['usrrepl'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['usrrepl']);
        }
        if (isset($options['lead_type'])) {
            switch ($options['lead_type']) {
                case '1':
                    /* Open & Priority & Soon */
                    $this->db->where_in('lead_type',array(1,2,6));
                    break;
                case '2':
                    /* Priority */
                    $this->db->where('lead_type',1);
                    break;
                case '3':
                    /* Dead */
                    $this->db->where('lead_type',4);
                    break;
                case '4':
                    /* Closed */
                    $this->db->where('lead_type',3);
                    break;
                case '5':
                    /* Open Only */
                    $this->db->where('lead_type',2);
                    break;
                case '6':
                    $this->db->where('lead_type',6);
                    break;
            }
        }
        if (isset($options['search'])) {
            $search='%'.strtoupper($options['search']).'%';
            // $this->db->like('upper(concat(coalesce(l.lead_item,\'\'),coalesce(l.other_item_name,\'\'),coalesce(l.lead_customer,\'\'),coalesce(l.lead_company,\'\'),coalesce(l.lead_mail,\'\'),coalesce(l.lead_phone,\'\'),concat(\'L\',l.lead_number))) ',$search);
            $searchdata="(CONCAT_WS('',l.lead_item,l.other_item_name,l.lead_customer,l.lead_company,l.lead_mail,l.lead_phone)  LIKE '{$search}' or concat('L',l.lead_number) like '{$search}')";
            $this->db->where("{$searchdata}");
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();

        return $res['cnt'];
    }

    /* Get data about Leads */
    public function get_leads($options,$sort,$limit,$offset) {
        $this->db->select('l.*');
        $this->db->from('ts_leads l');
        if (isset($options['usrrepl'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['usrrepl']);
        }
        if (isset($options['lead_type'])) {
            switch ($options['lead_type']) {
                case '1':
                    /* Open & Priority & Soon */
                    $this->db->where_in('lead_type',array(1,2,6));
                    break;
                case '2':
                    /* Priority */
                    $this->db->where('lead_type',1);
                    break;
                case '3':
                    /* Dead */
                    $this->db->where('lead_type',4);
                    break;
                case '4':
                    /* Closed */
                    $this->db->where('lead_type',3);
                    break;
                case '5':
                    /* Open Only */
                    $this->db->where('lead_type',2);
                    break;
                case '6':
                    $this->db->where('lead_type',6);
                    break;
            }
        }
        if (isset($options['search'])) {
            $search='%'.strtoupper($options['search']).'%';
            $searchdata="(CONCAT_WS('',l.lead_item,l.other_item_name,l.lead_customer,l.lead_company,l.lead_mail,l.lead_phone)  LIKE '{$search}' or concat('L',l.lead_number) like '{$search}')";
            $this->db->where("{$searchdata}");
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        if ($sort) {
            if ($sort==2) {
                $this->db->order_by('lead_date','desc');
            } elseif($sort==1) {
                $this->db->order_by('update_date','desc');
            }
        }
        $this->db->limit($limit,$offset);
        $result=$this->db->get()->result_array();
        $out=array();
        $cur_date='';
        $fl_show=0;
        $numpp=0;

        foreach ($result as $row) {
            if ($sort==1) {
                $weekday=date('w',strtotime($row['update_date']));
                $compdat=date('m/d',strtotime($row['update_date']));
            } else {
                $weekday=date('w',$row['lead_date']);
                $compdat=date('m/d',$row['lead_date']);
            }
            switch ($weekday) {
                case '0':
                    $compdate='Su '.$compdat;
                    break;
                case '1':
                    $compdate='Md '.$compdat;
                    break;
                case '2':
                    $compdate='Tu '.$compdat;
                    break;
                case '3':
                    $compdate='Wd '.$compdat;
                    break;
                case '4':
                    $compdate='Th '.$compdat;
                    break;
                case '5':
                    $compdate='Fr '.$compdat;
                    break;
                case '6':
                    $compdate='St '.$compdat;
                    break;
                default :
                    $compdate=$compdat;
                    break;
            }
            $row['out_date']='---';
            $row['dateclass']='';
            $row['separate']='';
            if ($cur_date!=$compdate) {
                $cur_date=$compdate;
                $row['out_date']=$compdate;
                $row['dateclass']='leadnewdat';
                if ($numpp>0) {
                    $row['separate']='separate';
                }
            }
            $row['itemshow_class']='normal';
            if ($row['lead_item_id']==-3) {
                $row['itemshow_class']='custom';
            }
            $row['lead_priority_icon']='&nbsp;';
            if ($row['lead_type']==1) {
                $row['lead_priority_icon']='<img src="/img/leads/goldstar.png" alt="Priority"/>';
            } elseif($row['lead_type']==6) {
                $row['lead_priority_icon']='<img src="/img/leads/ordersoon.gif" alt="Soon"/>';
            }
            $row['out_value']=(floatval($row['lead_value'])==0 ? '?' : round($row['lead_value']*$this->config->item('leadpts'),0).'pts');
            $row['contact']=($row['lead_company']=='' ? ($row['lead_customer']=='' ? $row['lead_mail'] : $row['lead_customer']) : $row['lead_company']);
            if (empty($row['contact'])) {
                $row['contact']='&nbsp;';
            }
            $row['contact']=($row['contact']=='' ? '&nbsp;' : $row['contact']);
            $row['lead_needby']=($row['lead_needby']=='' ? '&nbsp;' : $row['lead_needby']);
            $row['lead_customer']=($row['lead_customer']=='' ? '&nbsp;' : $row['lead_customer']);
            $row['lead_itemqty']=($row['lead_itemqty']=='' ? '&nbsp;' : $row['lead_itemqty']);
            switch ($row['lead_item']) {
                case '':
                    $row['out_lead_item']='&nbsp;';
                    break;
                case 'Other':
                case 'Multiple':
                case 'Custom Shaped Stress Balls':
                    if ($row['other_item_name']=='') {
                        $row['out_lead_item']=$row['lead_item'];
                    } else {
                        $row['out_lead_item']=$row['other_item_name'];
                    }
                    break;
                default :
                    $row['out_lead_item']=$row['lead_item'];
                    break;

            }
            $row['lead_item']=($row['lead_item']=='' ? '&nbsp;' : $row['lead_item']);
            $row['lead_needby']=($row['lead_needby']=='' ? '&nbsp;' : $row['lead_needby']);
            $row['leadrow_class']='';
            switch ($row['lead_type']) {
                case '3':
                    $row['leadrow_class']='dead';
                    break;
                case '4':
                    $row['leadrow_class']='closed';
                    break;
            }
            $this->db->select('u.user_initials');
            $this->db->from('users u');
            $this->db->join('ts_lead_users lu','lu.user_id=u.user_id');
            $this->db->where('lu.lead_id',$row['lead_id']);
            $usr=$this->db->get()->result_array();
            $lusr='';
            $nusr=0;
            foreach ($usr as $urow) {
                if ($nusr==2) {
                    $lusr=substr($lusr,0,-1).'+';
                    break;
                }
                $lusr.=$urow['user_initials'].' ';
                $nusr++;
            }
            $row['usr_data']=($lusr=='' ? '&nbsp;' : $lusr);
            $out[]=$row;
            $numpp++;
        }
        return $out;
    }

    // years for footer
//    function get_footer_years() {
//        $this->db->select("distinct(date_format(from_unixtime(lead_date),'%Y')) as year",FALSE);
//        $this->db->from('ts_leads');
//        $this->db->order_by('lead_date','desc');
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
//    function get_closed_byyear($year) {
//        $dat=$year.'-01-01 00:00:00';
//        $yearbgn=strtotime($dat);
//        $dat=$year.'-12-31 23:59:59';
//        $yearend=strtotime($dat);
//        /* Calculate */
//        $out=array(
//            'total'=>0,
//            '01'=>0,
//            '02'=>0,
//            '03'=>0,
//            '04'=>0,
//            '05'=>0,
//            '06'=>0,
//            '07'=>0,
//            '08'=>0,
//            '09'=>0,
//            '10'=>0,
//            '11'=>0,
//            '12'=>0,
//        );
//        $calc=array();
//        $calc['total']=array('base'=>0,'closed'=>0);
//        $calc['01']=array('base'=>0,'closed'=>0);
//        $calc['02']=array('base'=>0,'closed'=>0);
//        $calc['03']=array('base'=>0,'closed'=>0);
//        $calc['04']=array('base'=>0,'closed'=>0);
//        $calc['05']=array('base'=>0,'closed'=>0);
//        $calc['06']=array('base'=>0,'closed'=>0);
//        $calc['07']=array('base'=>0,'closed'=>0);
//        $calc['08']=array('base'=>0,'closed'=>0);
//        $calc['09']=array('base'=>0,'closed'=>0);
//        $calc['10']=array('base'=>0,'closed'=>0);
//        $calc['11']=array('base'=>0,'closed'=>0);
//        $calc['12']=array('base'=>0,'closed'=>0);
//
//        $this->db->select("date_format(update_date,'%m') as month, count(lead_id) as cnt",FALSE);
//        $this->db->from('ts_leads');
//        $this->db->where('unix_timestamp(update_date) >= ',$yearbgn);
//        $this->db->where('unix_timestamp(update_date) <= ',$yearend);
//        $this->db->group_by('month');
//        $totals=$this->db->get()->result_array();
//        foreach ($totals as $row) {
//            $key=$row['month'];
//            $val=intval($row['cnt']);
//            $calc[$key]['base']+=$val;
//            $calc['total']['base']+=$val;
//        }
//        $this->db->select("date_format(update_date,'%m') as month, count(lead_id) as cnt",FALSE);
//        $this->db->from('ts_leads');
//        $this->db->where('unix_timestamp(update_date) >= ',$yearbgn);
//        $this->db->where('unix_timestamp(update_date) <= ',$yearend);
//        $this->db->where('lead_type',3);
//        $this->db->group_by('month');
//        $totals=$this->db->get()->result_array();
//        foreach ($totals as $row) {
//            $key=$row['month'];
//            $val=intval($row['cnt']);
//            $calc[$key]['closed']+=$val;
//            $calc['total']['closed']+=$val;
//        }
//        /* update out */
//        if ($calc['total']['base']!=0) {
//            $out['total']=round($calc['total']['closed']/$calc['total']['base']*100,0).'%';
//            $out['01']=($calc['01']['base']==0 ? '&mdash;' : round($calc['01']['closed']/$calc['01']['base']*100,0).'%');
//            $out['02']=($calc['02']['base']==0 ? '&mdash;' : round($calc['02']['closed']/$calc['02']['base']*100,0).'%');
//            $out['03']=($calc['03']['base']==0 ? '&mdash;' : round($calc['03']['closed']/$calc['03']['base']*100,0).'%');
//            $out['04']=($calc['04']['base']==0 ? '&mdash;' : round($calc['04']['closed']/$calc['04']['base']*100,0).'%');
//            $out['05']=($calc['05']['base']==0 ? '&mdash;' : round($calc['05']['closed']/$calc['05']['base']*100,0).'%');
//            $out['06']=($calc['06']['base']==0 ? '&mdash;' : round($calc['06']['closed']/$calc['06']['base']*100,0).'%');
//            $out['07']=($calc['07']['base']==0 ? '&mdash;' : round($calc['07']['closed']/$calc['07']['base']*100,0).'%');
//            $out['08']=($calc['08']['base']==0 ? '&mdash;' : round($calc['08']['closed']/$calc['08']['base']*100,0).'%');
//            $out['09']=($calc['09']['base']==0 ? '&mdash;' : round($calc['09']['closed']/$calc['09']['base']*100,0).'%');
//            $out['10']=($calc['10']['base']==0 ? '&mdash;' : round($calc['10']['closed']/$calc['10']['base']*100,0).'%');
//            $out['11']=($calc['11']['base']==0 ? '&mdash;' : round($calc['11']['closed']/$calc['11']['base']*100,0).'%');
//            $out['12']=($calc['12']['base']==0 ? '&mdash;' : round($calc['12']['closed']/$calc['12']['base']*100,0).'%');
//        } else {
//            $out=array(
//                'total' => '&mdash;',
//                '01' => '&mdash;',
//                '02' => '&mdash;',
//                '03' => '&mdash;',
//                '04' => '&mdash;',
//                '05' => '&mdash;',
//                '06' => '&mdash;',
//                '07' => '&mdash;',
//                '08' => '&mdash;',
//                '09' => '&mdash;',
//                '10' => '&mdash;',
//                '11' => '&mdash;',
//                '12' => '&mdash;',
//            );
//        }
//        return $out;
//    }
//
    /* Lead Data by ID */
    public function get_lead($lead_id) {
        $this->db->select('*');
        $this->db->from('ts_leads');
        $this->db->where('lead_id',$lead_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function get_empty_lead() {
        $fields=$this->db->list_fields('ts_leads');
        $res=array();
        foreach ($fields as $fld) {
            $res[$fld]='';
        }
        return $res;

    }
    /* History */
    public function get_lead_history($lead_id) {
        $this->db->select('h.*, u.user_leadname as user_name');
        $this->db->from('ts_lead_logs h');
        $this->db->join('users u','u.user_id=h.user_id','left');
        $this->db->where('h.lead_id',$lead_id);
        $this->db->order_by('h.created_date','desc');
        $logs=$this->db->get()->result_array();
        return $logs;
    }
    /* Users replicas */
    public function get_lead_users($lead_id) {
        $this->db->select('user_id');
        $this->db->from('ts_lead_users');
        $this->db->where('lead_id',$lead_id);
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            array_push($out, $row['user_id']);
        }
        return $out;
    }
    /* Get Lead Tasks, related with lead */
    public function get_lead_tasks($lead_id) {
        $this->db->select('*');
        $this->db->from('ts_lead_tasks');
        $this->db->where('lead_id',$lead_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['leadtask_id'])) {
            $res=array();
            /* Get Struct */
            $fields = $this->db->list_fields('ts_lead_tasks');
            foreach ($fields as $field)
            {
                $res[$field]='';
            }
            $res['lead_id']=$lead_id;
            $res['leadtask_id']=0;
        }
        return $res;
    }

    public function get_leadnum() {
        $this->db->select('max(lead_number) as last_num');
        $this->db->from('ts_leads');
        $res=$this->db->get()->row_array();
        if (!$res['last_num']) {
            $outval=$this->init_number;
        } else {
            $outval=$res['last_num']+1;
        }
        return $outval;
    }


    public function save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id) {
        $out=array('result'=> $this->error_result,'msg'=>  $this->INIT_ERRMSG);
        $oldlead=array();
        if ($leadpost['lead_id']!=0) {
            $oldlead=$this->get_lead($leadpost['lead_id']);
        }
        /* Check incoming */
        if (count($lead_usr)==0) {
            $out['msg']='Assign Lead executor';
        } else {
            /* Save Lead main data */
            $newhistory='';
            $newval=(floatval($leadpost['lead_value']));
            if ($newval==0) {
                $newval=NULL;
            }
            $this->db->set('lead_company',$leadpost['lead_company']);
            $this->db->set('lead_phone',$leadpost['lead_phone']);
            // $this->db->set('lead_value',$leadpost['lead_value']);
            $this->db->set('lead_value',$newval);
            $this->db->set('lead_needby',$leadpost['lead_needby']);
            $this->db->set('lead_customer',$leadpost['lead_customer']);
            $this->db->set('lead_mail',$leadpost['lead_mail']);
            $this->db->set('lead_itemqty',$leadpost['lead_itemqty']);

            $this->db->set('lead_note',$leadpost['lead_note']);

            if (isset($leadpost['lead_item_id'])) {
                if ($leadpost['lead_item_id']=='') {
                    $leadpost['lead_item_id']=NULL;
                    $leadpost['lead_item']='';
                } else {
                    /* Get DATA about Item */
                    $itemdat=$this->search_itemid($leadpost['lead_item_id']);
                    if ($itemdat['result']==$this->error_result) {
                        $leadpost['lead_item']='';
                        $leadpost['lead_item_id']=NULL;
                    } else {
                        $leadpost['lead_item']=$itemdat['item_name'];
                    }
                }
                $this->db->set('lead_item',$leadpost['lead_item']);
                $this->db->set('lead_item_id',$leadpost['lead_item_id']);
            }
            if (isset($leadpost['other_item_name'])) {
                $this->db->set('other_item_name',$leadpost['other_item_name']);
            }
            if ($leadpost['lead_status']!='') {
                $this->db->set('lead_status',$leadpost['lead_status']);
                $this->db->set('update_date', date('Y-m-d H:i:s'));
                $newhistory=$leadpost['lead_status'];
            }
            $this->db->set('lead_type',$leadpost['lead_type']);
            if ($leadpost['lead_id']==0) {
                /* New Record */
                $this->db->set('brand', $leadpost['brand']);
                $this->db->set('lead_date',time());
                $this->db->set('lead_assign_time',time());
                $this->db->set('create_user',$usr_id);
                $this->db->set('update_usr',$usr_id);
                $this->db->set('create_date',date('Y-m-d H:i:s'));
                $leadnum=$this->get_leadnum();
                $this->db->set('lead_number',$leadnum);
                $this->db->insert('ts_leads');
                $lead_id=$this->db->insert_id();
                if ($lead_id==0) {
                    $out['msg']='Unable to add record. Try later';
                } else {
                    $leadpost['lead_id']=$lead_id;
                    $leadpost['lead_number']=$leadnum;
                }
            } else {
                $this->db->set('update_usr',$usr_id);
                $this->db->where('lead_id',$leadpost['lead_id']);
                $this->db->update('ts_leads');
                $leadpost['lead_number']=$oldlead['lead_number'];
            }
            // Change Type
            if ($leadpost['lead_type']==4) {
                if (empty($oldlead) || $oldlead['lead_type']!=4) {
                    // Close
                    $this->db->select('ul.userdeed_log_id, ul.deed_type, ul.user_id, u.user_name');
                    $this->db->from('ts_userdeed_logs ul');
                    $this->db->join('users u','u.user_id=ul.user_id');
                    $this->db->where('ul.deed_type','LEAD_CLOSE');
                    $this->db->where('ul.user_id',$usr_id);
                    $usrlog=$this->db->get()->row_array();
                    if (isset($usrlog['user_id'])) {
                        $this->db->select('user_email');
                        $this->db->from('users');
                        $this->db->where('mail_notification',1);
                        $mails=$this->db->get()->result_array();
                        if (count($mails)>0) {
                            $this->_leadclose_log($leadpost, $oldlead, $usrlog, $mails);
                        }
                    }
                }
            }
            /* Update - create related data */
            if ($leadpost['lead_id']!=0) {
                /* History */
                if ($newhistory) {
                    $this->db->set('lead_id',$leadpost['lead_id']);
                    $this->db->set('user_id',$usr_id);
                    $this->db->set('created_date',time());
                    $this->db->set('history_message',$newhistory);
                    $this->db->insert('ts_lead_logs');
                }
                /* clean users */
                $this->db->where('lead_id',$leadpost['lead_id']);
                $this->db->delete('ts_lead_users');
                foreach ($lead_usr as $row) {
                    $this->db->set('lead_id',$leadpost['lead_id']);
                    $this->db->set('user_id',$row);
                    $this->db->insert('ts_lead_users');
                }
                $out['msg']='';
                $out['result']=$leadpost['lead_id'];
            }
        }
        return $out;
    }

    /* Add New PR request */
    public function add_proof_request($leadpost, $usr_id, $usr_name) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_ERRMSG);
        $this->load->model('artwork_model');
        /* Create record in TS_EMAILS */
        $item_name=NULL;
        $item_num=NULL;
        $itemdata=$this->search_itemid($leadpost['lead_item_id']);
        if ($itemdata['result']==$this->success_result) {
            $item_name=$itemdata['item_name'];
            $item_num=$itemdata['item_number'];
        }
        // Get Proof Num
        $proof_num=$this->get_new_proofnum();
        $this->db->set('email_type','Art_Submit');
        $this->db->set('proof_num',$proof_num);
        $this->db->set('proof_updated',  time());
        $this->db->set('email_sender',$leadpost['lead_company']);
        $this->db->set('email_sendermail',$leadpost['lead_mail']);
        $this->db->set('email_senderphone',$leadpost['lead_phone']);
        $this->db->set('email_sendercompany',$leadpost['lead_customer']);
        $this->db->set('email_webpage', 'Sales');
        $this->db->set('email_item_name',$item_name);
        $this->db->set('email_item_number',$item_num);
        $this->db->set('brand', $leadpost['brand']);
        $this->db->insert('ts_emails');
        $newrec=$this->db->insert_id();
        if (!$newrec) {
            // Oops - record wasn't added
            $out['msg']='Error during Add Proof Request. Try later';
            return $out;
        }
        $out['result']=  $this->success_result;
        $out['email_id']=$newrec;
        // Add relation with lead
        $this->db->set('lead_id',$leadpost['lead_id']);
        $this->db->set('email_id',$newrec);
        $this->db->insert('ts_lead_emails');
        // Add artwork, Artwork history
        $this->db->select('*');
        $this->db->from('ts_emails');
        $this->db->where('email_id',$newrec);
        $maildat=$this->db->get()->row_array();
        $artw=array(
            'artwork_id'=>0,
            'order_id'=>NULL,
            'mail_id'=>$newrec,
            'user_id'=>$usr_id,
            'customer'=>$leadpost['lead_company'],
            'customer_phone'=>$maildat['email_senderphone'],
            'customer_email'=>$maildat['email_sendermail'],
            'customer_contact'=>$leadpost['lead_customer'],
            'item_name'=>$maildat['email_item_name'],
            'other_item'=>$leadpost['other_item_name'],
            'item_number'=>$maildat['email_item_number'],
            'item_color'=>$maildat['email_special_requests'],
            'item_qty'=>$maildat['email_qty'],
            'item_id'=>$leadpost['lead_item_id'],
        );
        $art_id=$this->artwork_model->artwork_update($artw);
        if ($art_id) {
            // All OK - add history
            $history_msg='Proof Request was created '.date('m/d/Y H:i:s',  strtotime($maildat['email_date'])).' manually by '.$usr_name;
            $this->db->set('artwork_id',$art_id);
            $this->db->set('message',$history_msg);
            $this->db->insert('ts_artwork_history');
        }
        return $out;
    }
    /* New Proof Number */
    private function get_new_proofnum() {
        $this->db->select('max(proof_num) as proof');
        $this->db->from('ts_emails');
        $this->db->where('email_type', 'Art_Submit');
        $res=$this->db->get()->row_array();
        if (!isset($res['proof']) || $res['proof']=='') {
            $part1=0;
            $part2=0;
        } else {
            $mailarr=explode('-', $res['proof']);
            $part1=intval($mailarr[1]);
            $part2=intval($mailarr[0]);
        }
        $part1++;
        if ($part1==999) {
            $part2++;
            $part1=0;
        }
        $new_proof=str_pad($part2, 3, '0', STR_PAD_LEFT).'-'.str_pad($part1, 3, '0', STR_PAD_LEFT);
        return $new_proof;

    }

    public function remove_proof_request($email_id, $user_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        $this->db->where('email_id',$email_id);
        // $this->db->set('email_status',2);
        $this->db->delete('ts_emails');
        if ($this->db->affected_rows()==0) {
            $out['msg']='Request wasn\'t deleted';
        } else {
            $out['result']= $this->success_result;
            $out['msg']='';
        }
        return $out;
    }

//    /* Get data for special acc reminder */
//    function get_specialacc($options=array()) {
//        $this->db->select('*');
//        $this->db->from('ts_special_reminder');
//        /* $this->db->where('') */
//        $this->db->order_by('company_name');
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
//    function get_special_acc($acc_id) {
//        $this->db->select('*');
//        $this->db->from('ts_special_reminder');
//        $this->db->where('special_id',$acc_id);
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    function save_specialacc($options) {
//        $out=array('result'=>  Leads_model::ERR_FLAG, 'msg'=>  Leads_model::INIT_ERRMSG);
//        $special_id=$options['special_id'];
//        $company_name=$options['company_name'];
//        if (empty($company_name)) {
//            $out['msg']='Enter company name';
//        } elseif ($this->specialacc_exist($company_name, $special_id)) {
//            $out['error']='This company in list';
//        } else {
//            $this->db->set('company_name',$company_name);
//            if ($special_id==0) {
//                $this->db->insert('ts_special_reminder');
//                $res=$this->db->insert_id();
//                if ($res!=0) {
//                    $out['result']=Leads_model::SUCCESS_RESULT;
//                    $out['msg']='';
//                } else {
//                    $out['msg']='Error during add account';
//                }
//            } else {
//                $this->db->where('special_id',$special_id);
//                $this->db->update('ts_special_reminder');
//                $out['result']=Leads_model::SUCCESS_RESULT;
//                $out['msg']='';
//            }
//        }
//        return $out;
//    }
//
//    function delete_specialacc($special_id) {
//        $this->db->where('special_id',$special_id);
//        $this->db->delete('ts_special_reminder');
//        if ($this->db->affected_rows()==0) {
//            return Leads_model::ERR_FLAG;
//        } else {
//            return Leads_model::SUCCESS_RESULT;
//        }
//    }
//
//    function specialacc_exist($company_name, $special_id) {
//        $this->db->select('count(special_id) as cnt');
//        $this->db->from('ts_special_reminder');
//        $this->db->where('special_id != ',$special_id);
//        $this->db->where('upper(company_name) ',$company_name);
//        $res=$this->db->get()->row_array();
//        if ($res['cnt']!=0) {
//            return TRUE;
//        } else {
//            return FALSE;
//        }
//    }
//
    public function get_lead_list($options=array()) {
        $this->db->select('lead_id, concat("L" , lead_number ) as lead_number, lead_customer, lead_item');
        $this->db->from('ts_leads');
        $this->db->where_in('lead_type',array(1,2));
        if (isset($options['orderby'])) {
            if (isset($options['direction'])) {
                $this->db->order_by($options['orderby'],$options['direction']);
            } else {
                $this->db->order_by($options['orderby']);
            }
        } else {
            $this->db->order_by('lead_number');
        }
        $res=$this->db->get()->result_array();
        return $res;
    }

    // Relation between message & Lead
    public function save_leadrelation($quest) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_ERRMSG);
        if (!isset($quest['mail_id'])) {
            $out['msg']='Unknown question';
        } elseif(!$quest['mail_id']) {
            $out['msg']='Wrong Question';
        } elseif (empty($quest['lead_id'])) {
            $out['msg']='Enter Lead for Question or Create New';
        } elseif ($this->check_leadrelation($quest['mail_id'])) {
            $out['msg']='This Request Related with Lead. Please, select other Request';
        } else {
            /* Lead Relations */
            $this->db->set('lead_id',$quest['lead_id']);
            if (intval($quest['leademail_id'])==0) {
                $this->db->set('email_id',$quest['mail_id']);
                $this->db->insert('ts_lead_emails');
                if ($this->db->insert_id()==0) {
                    $out['msg']='Error during building of Lead-Message relation';
                } else {
                    $this->db->set('lead_assign_time',  time());
                    $this->db->where('lead_id',$quest['lead_id']);
                    $this->db->update('ts_leads');
                    $out['msg']='';
                    $out['result']=$this->success_result;
                }
            } else {
                $this->db->where('leademail_id',$quest['leademail_id']);
                $this->db->update('ts_lead_emails');
                $this->db->set('lead_assign_time',  time());
                $this->db->where('lead_id',$quest['lead_id']);
                $this->db->update('ts_leads');
                $out['result']=$this->success_result;
                $out['msg']='';
            }
        }
        return $out;
    }

    // Relation between custom quote and lead
    public function save_quotelead_relation($options) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_ERRMSG);
        if ($this->check_leadquoterelation($options['customform'])) {
            $out['msg'] = 'This Request Related with Lead. Please, select other Request';
        } else {
            $this->db->set('lead_id',$options['lead_id']);
            if (intval($options['leademail_id'])==0) {
                $this->db->set('custom_quote_id',$options['customform']);
                $this->db->insert('ts_lead_emails');
                if ($this->db->insert_id()==0) {
                    $out['msg']='Error during building of Lead-Message relation';
                } else {
                    $this->db->set('lead_assign_time',  time());
                    $this->db->where('lead_id',$options['lead_id']);
                    $this->db->update('ts_leads');
                    $out['msg']='';
                    $out['result']=$this->success_result;
                }
            } else {
                $this->db->where('leademail_id',$options['leademail_id']);
                $this->db->update('ts_lead_emails');
                $this->db->set('lead_assign_time',  time());
                $this->db->where('lead_id',$options['lead_id']);
                $this->db->update('ts_leads');
                $out['result']=$this->success_result;
                $out['msg']='';
            }
        }
        return $out;
    }

    /* Create Lead from quote */
    function create_leadquote($maildat,$leademail_id, $user_id) {
        $out=array('result'=> $this->error_result, 'msg'=> $this->INIT_ERRMSG);
        /* Create array with Lead data */
        $lead_usr=array($user_id);
        /* Try to get Item ID */
        $item_id='';

        $this->load->model('artwork_model');
        if (!empty($maildat['email_other_info'])) {
            $item_id=get_json_param($maildat['email_other_info'],'item_id','');
        }
        if ($item_id=='') {
            if (!empty($maildat['email_item_id'])) {
                $item_id=$maildat['email_item_id'];
            } elseif (!empty($maildat['email_item_number'])) {
                $itemoption=array(
                    'item_num'=>$maildat['email_item_number'],
                );
                $itemdat=$this->artwork_model->get_item_details($itemoption);
                if (count($itemdat)==1) {
                    $item_id=$itemdat[0]['item_id'];
                }
            } elseif (!empty($maildat['email_item_name'])) {
                $itemoption=array(
                    'item_name'=>$maildat['email_item_name'],
                );
                $itemdat=$this->artwork_model->get_item_details($itemoption);
                if (count($itemdat)==1) {
                    $item_id=$itemdat[0]['item_id'];
                }
            }
        }

        $leadpost=array(
            'lead_id'=>0,
            'lead_company'=>ifset($maildat, 'email_sendercompany', NULL),
            'lead_phone'=>ifset($maildat, 'email_senderphone', NULL),
            'lead_value'=>ifset($maildat, 'email_total' , NULL ),
            'lead_needby'=>($maildat['email_due']=='' ? NULL : $maildat['email_due']),
            'lead_customer'=>($maildat['email_sender']=='' ? NULL : $maildat['email_sender']),
            'lead_mail'=>($maildat['email_sendermail']=='' ? NULL : $maildat['email_sendermail']),
            'lead_itemqty'=>(intval($maildat['email_qty'])==0 ? NULL : $maildat['email_qty']),
            'lead_item'=>($maildat['email_item_name']=='' ? NULL : $maildat['email_item_name']),
            'lead_item_id'=>($item_id=='' ? NULL : $item_id),
            'lead_note'=>($maildat['email_text']=='' ? NULL : $maildat['email_text']),
            'lead_status'=>'',
            'lead_type'=>$this->init_lead_type,
            'brand' => $maildat['brand'],
        );
        $lead_tasks=array(
            'send_quote'=>1,
            'send_artproof'=>0,
            'send_sample'=>0,
            'answer_question'=>0,
            'other'=>NULL,
            'leadtask_id'=>0,
        );
        $res=$this->save_leads($lead_usr, $lead_tasks, $leadpost, $user_id);
        $out['msg']=$res['msg'];
        if ($res['result']!=$this->error_result) {
            $out['result']=$res['result'];
            /* Create relations between Mail and Leads */
            $this->db->set('lead_id',$res['result']);
            if (intval($leademail_id)==0) {
                $this->db->set('email_id',$maildat['email_id']);
                $this->db->insert('ts_lead_emails');
                if ($this->db->insert_id()==0) {
                    $out['msg']='Error during build relations';
                    /* Delete Lead */
                } else {
                    //$out['result']=Leads_model::SUCCESS_RESULT;
                    $out['msg']='';
                }
            }
        }
        return $out;
    }

    /* Create Lead from Questions */
    public function create_leadquest($maildat,$leademail_id,$user_id) {
        $out=array('result'=>  $this->error_result,'msg'=>  $this->INIT_ERRMSG);
        /* Create array with Lead data */
        $lead_usr=array($user_id);
        $leadpost=array(
            'lead_id'=>0,
            'lead_company'=>($maildat['email_sendercompany']=='' ? NULL : $maildat['email_sendercompany']),
            'lead_phone'=>($maildat['email_senderphone']=='' ? NULL : $maildat['email_senderphone']),
            'lead_value'=>NULL,
            'lead_needby'=>NULL,
            'lead_customer'=>($maildat['email_sender']=='' ? NULL : $maildat['email_sender']),
            'lead_mail'=>($maildat['email_sendermail']=='' ? NULL : $maildat['email_sendermail']),
            'lead_itemqty'=>NULL,
            'lead_item'=>NULL,
            'lead_note'=>($maildat['email_text']=='' ? NULL : $maildat['email_text']),
            'lead_status'=>'',
            'lead_type'=>$this->init_lead_type,
            'brand' => $maildat['brand'],
        );
        $lead_tasks=array(
            'send_quote'=>0,
            'send_artproof'=>0,
            'send_sample'=>0,
            'answer_question'=>1,
            'other'=>NULL,
            'leadtask_id'=>0,
        );
        $res=$this->save_leads($lead_usr, $lead_tasks, $leadpost, $user_id);
        if ($res['result']==$this->error_result) {
            $out['msg']=$res['msg'];
        } else {
            $out['result']=$res['result'];
            /* Create relations between Mail and Leads */
            $this->db->set('lead_id',$res['result']);
            if (intval($leademail_id)==0) {
                $this->db->set('email_id',$maildat['email_id']);
                $this->db->insert('ts_lead_emails');
                if ($this->db->insert_id()==0) {
                    $out['msg']='Error during build relations';
                    /* Delete Lead */
                } else {
                    // $out['result']=Leads_model::SUCCESS_RESULT;
                    $out['msg']='';
                }
            }
        }
        return $out;
    }

    /* Create Lead from Art Proof */
    public function create_leadproof($maildat,$leademail_id, $user_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        /* Create array with Lead data */
        $lead_usr=array($user_id);
        /* Try to get Item ID */
        $item_id='';
        $this->load->model('artwork_model');
        if (!empty($maildat['email_other_info'])) {
            $item_id=get_json_param($maildat['email_other_info'],'item_id','');
        }
        if ($item_id=='') {
            if (!empty($maildat['email_item_id'])) {
                $item_id=$maildat['email_item_id'];
            } elseif (!empty($maildat['email_item_number'])) {
                $itemoption=array(
                    'item_num'=>$maildat['email_item_number'],
                );
                $itemdat=$this->artwork_model->get_item_details($itemoption);
                if (count($itemdat)==1) {
                    $item_id=$itemdat[0]['item_id'];
                }
            } elseif (!empty($maildat['email_item_name'])) {
                $itemoption=array(
                    'item_name'=>$maildat['email_item_name'],
                );
                $itemdat=$this->artwork_model->get_item_details($itemoption);
                if (count($itemdat)==1) {
                    $item_id=$itemdat[0]['item_id'];
                }
            }
        }

        $leadpost=array(
            'lead_id'=>0,
            'lead_company'=>($maildat['email_sendercompany']=='' ? NULL : $maildat['email_sendercompany']),
            'lead_phone'=>($maildat['email_senderphone']=='' ? NULL : $maildat['email_senderphone']),
            'lead_value'=>(floatval($maildat['email_total'])==0 ? NULL : $maildat['email_total']),
            'lead_needby'=>($maildat['email_due']=='' ? NULL : $maildat['email_due']),
            'lead_customer'=>($maildat['email_sender']=='' ? NULL : $maildat['email_sender']),
            'lead_mail'=>($maildat['email_sendermail']=='' ? NULL : $maildat['email_sendermail']),
            'lead_itemqty'=>(intval($maildat['email_qty'])==0 ? NULL : $maildat['email_qty']),
            'lead_item'=>($maildat['email_item_name']=='' ? NULL : $maildat['email_item_name']),
            'lead_item_id'=>($item_id=='' ? NULL : $item_id),
            'lead_note'=>($maildat['email_text']=='' ? NULL : $maildat['email_text']),
            'lead_status'=>'',
            'lead_type'=>$this->init_lead_type,
            'brand' => $maildat['brand'],
        );
        $lead_tasks=array(
            'send_quote'=>0,
            'send_artproof'=>1,
            'send_sample'=>0,
            'answer_question'=>0,
            'other'=>NULL,
            'leadtask_id'=>0,
        );
        $res=$this->save_leads($lead_usr, $lead_tasks, $leadpost, $user_id);
        $out['msg']=$res['msg'];
        if ($res['result']==$this->error_result) {
            $out['msg'] = $res['msg'];
        } else {
            $out['result']=$res['result'];
            /* Create relations between Mail and Leads */
            $this->db->set('lead_id',$res['result']);
            if (intval($leademail_id)==0) {
                $this->db->set('email_id',$maildat['email_id']);
                $this->db->insert('ts_lead_emails');
                if ($this->db->insert_id()==0) {
                    $out['msg']='Error during build relations';
                    /* Delete Lead */
                } else {
                    //$out['result']=Leads_model::SUCCESS_RESULT;
                    $out['msg']='';
                }
            }
        }
        return $out;
    }

    /* Duplicate Lead */
    function duplicate_lead($lead_id,$user_id) {
        /* Select Lead */
        $this->db->select('*');
        $this->db->from('ts_leads');
        $this->db->where('lead_id',$lead_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['lead_id'])) {
            $retval=array('lead_id'=>0);
        } else {
            /* Change Lead data */
            // Made new Lead status - Open
            $res['lead_type']=2;
            // $lead_usr=$this->get_lead_users($lead_id);
            $lead_usr=[];
            array_push($lead_usr, $user_id);
            $lead_tasks=$this->get_lead_tasks($lead_id);
            if (isset($lead_tasks['leadtask_id'])) {
                $lead_tasks['leadtask_id']=0;
                $lead_tasks['lead_id']=0;
            }

            $res['create_date']=date('Y-m-d H:i:s');
            $res['lead_date']=time();
            $res['lead_status']='';
            $res['lead_id']=0;
            $leaddat=$this->save_leads($lead_usr, $lead_tasks, $res, $user_id);
            // $leaddat['result']=0;
            if ($leaddat['result']==$this->error_result) {
                $retval=array('lead_id'=>0);
            } else {
                $retval=$this->get_lead($leaddat['result']);
            }
        }
        return $retval;
    }

//    function get_user_scores($user_id=0) {
//        $out=array(
//            'close_30'=>'',
//            'open_30'=>'',
//            'close_100'=>'',
//            'open_100'=>'',
//            'last_month'=>'',
//            'leads_month'=>'',
//        );
//        $curday=date('d');
//        $d_bgn=date('Y-m-d');
//        $time_bgn=strtotime($d_bgn);
//        // if ($curday=='01') {
//        /* Today first day - we get data for prev month */
//        // $day=time()-24*60*60;
//        // $out['last_month']=date('F',$day);
//        // $dats=$this->func->getDatesByMonth(date('m',$day),date('Y',$day));
//        // } else {
//        $out['last_month']=date('F');
//        $dats=$this->func->getDatesByMonth(date('m'),date('Y'));
//        // }
//        $bgn=$dats['start_month'];
//        $end=$dats['end_month'];
//        /* Lets go */
//        /* 30 */
//        $this->db->select('l.lead_type,count(l.lead_id) as cnt');
//        if ($user_id==0) {
//            $this->db->from('(select lead_type, lead_id from ts_leads  where lead_assign_time <= '.$time_bgn.'  order by lead_assign_time desc limit 30) l');
//        } else {
//            $this->db->from('(select ld.lead_type, ld.lead_id from ts_leads ld join ts_lead_users lu on lu.lead_id=ld.lead_id where ld.lead_assign_time <= '.$time_bgn.'  and lu.user_id='.$user_id.' order by ld.lead_assign_time  desc limit 30) l');
//        }
//        $this->db->group_by('l.lead_type');
//        $res30=$this->db->get()->result_array();
//
//        $close30=0;
//        $open30=0;
//        $total=0;
//        foreach ($res30 as $row) {
//            if ($row['lead_type']==1 || $row['lead_type']==2) {
//                $open30+=$row['cnt'];
//            } elseif($row['lead_type']==4) {
//                $close30+=$row['cnt'];
//            }
//            $total+=$row['cnt'];
//        }
//        if ($open30==0) {
//            $out['open_30']='';
//        } else {
//            $dat=round(($open30/$total)*100,0);
//            $out['open_30']=$dat.'%';
//        }
//        if ($close30==0) {
//            $out['close_30']='';
//        } else {
//            $dat=round(($close30/$total)*100,0);
//            $out['close_30']=$dat.'%';
//        }
//        /* 100 */
//        $this->db->select('l.lead_type,count(l.lead_id) as cnt');
//        if ($user_id==0) {
//            $this->db->from('(select lead_type, lead_id from ts_leads  where lead_assign_time <= '.$time_bgn.'  order by lead_assign_time  desc limit 100) l');
//        } else {
//            $this->db->from('(select ld.lead_type, ld.lead_id from ts_leads ld join ts_lead_users lu on lu.lead_id=ld.lead_id where ld.lead_assign_time <= '.$time_bgn.'  and lu.user_id='.$user_id.' order by ld.lead_assign_time desc limit 100) l');
//        }
//        $this->db->group_by('l.lead_type');
//        $res100=$this->db->get()->result_array();
//        $close100=0;
//        $open100=0;
//        $total100=0;
//        foreach ($res100 as $row) {
//            if ($row['lead_type']==1 || $row['lead_type']==2) {
//                $open100+=$row['cnt'];
//            } elseif($row['lead_type']==4) {
//                $close100+=$row['cnt'];
//            }
//            $total100+=$row['cnt'];
//        }
//        if ($open100==0) {
//            $out['open_100']='';
//        } else {
//            $dat=round(($open100/$total100)*100,0);
//            $out['open_100']=$dat.'%';
//        }
//        if ($close100==0) {
//            $out['close_100']='';
//        } else {
//            $dat=round(($close100/$total100)*100,0);
//            $out['close_100']=$dat.'%';
//        }
//        /* Total per month */
//        if ($user_id==0) {
//            $this->db->select('count(lead_id) as cnt');
//            $this->db->from('ts_leads');
//            /* $this->db->where('lead_date < ', $time_bgn); */
//            $this->db->where('lead_date between '.$bgn.' and '.$end.' ');
//        } else {
//            $this->db->select('count(l.lead_id) as cnt');
//            $this->db->from('ts_leads l');
//            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
//            $this->db->where('lu.user_id',$user_id);
//            /* $this->db->where('l.lead_date < ', $time_bgn); */
//            $this->db->where('l.lead_date between '.$bgn.' and '.$end.' ');
//        }
//        $res=$this->db->get()->row_array();
//        $out['leads_month']=($res['cnt']==0 ? '' : $res['cnt']);
//        return $out;
//    }
//
//    public function search_items($item) {
//        $this->db->select("item_id as value, concat(item_number,' / ',item_name) as label",FALSE);
//        $this->db->from('v_itemsearch');
//        $this->db->like('upper(concat(item_name,item_number)) ',  strtoupper($item));
//        $this->db->order_by('item_number');
//        $result=$this->db->get()->result_array();
//        return $result;
//    }

    public function items_list($brand) {
        $this->db->select('item_id, item_name, item_number');
        $this->db->from('v_itemsearch');
        $this->db->where('(brand=\''.$brand.'\' or brand=\'\')');
        $this->db->order_by('item_name');
        $result=$this->db->get()->result_array();
        return $result;
    }

    public function search_itemid($item_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->INIT_ERRMSG);
        // Search
        $this->db->select('item_number, item_name');
        $this->db->from('v_itemsearch');
        $this->db->where('item_id',$item_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_number'])) {
            $out['msg']='Item Not Found';
        } else {
            $out['result']=$this->success_result;
            $out['msg']='';
            $out['item_name']=$res['item_name'];
            $out['item_number']=$res['item_number'];
        }
        return $out;
    }

    // Check relation with lead
    public function check_leadrelation($quest_id) {
        $this->db->select('count(*) as cnt');
        $this->db->from('ts_lead_emails');
        $this->db->where('email_id',$quest_id);
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    // Check relation with lead
    public function check_leadquoterelation($customquote) {
        $this->db->select('count(*) as cnt');
        $this->db->from('ts_lead_emails');
        $this->db->where('custom_quote_id',$customquote);
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    // Count % of closed leads
    public function count_closed_totals($options) {
        $dateend=$options['enddate'];
        $response=array(
            'prev'=>0,
            'next'=>0,
        );
        $out=array();
        for ($i=1; $i<6; $i++) {
            $dat=strtotime(date("Y-m-d", $dateend) . " -{$i} month");
            $out[]=array(
                'label'=>date('M\'y', $dat),
                'month'=>date('m-Y', $dat),
                'base'=>0,
                'closed'=>0,
                'percent'=>'&mdash;',
            );
        }
        $datestart=$dat;
        $response['datestart']=$datestart;
        if (isset($options['user_id'])) {
            $this->db->select("date_format(l.create_date,'%m-%Y') as month, count(l.lead_id) as cnt", FALSE);
            $this->db->from('ts_leads l');
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        } else {
            $this->db->select("date_format(l.create_date,'%m-%Y') as month, count(l.lead_id) as cnt", FALSE);
            $this->db->from('ts_leads l');
        }
        $this->db->where('unix_timestamp(l.create_date) >= ', $datestart);
        $this->db->where('unix_timestamp(l.create_date) < ', $dateend);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('month');
        $res=$this->db->get()->result_array();
        foreach ($res as $row) {
            $idx=0;
            foreach ($out as $orow) {
                if ($orow['month']==$row['month']) {
                    $out[$idx]['base']=$row['cnt'];
                }
                $idx++;
            }
        }
        //  Closed
        if (isset($options['user_id'])) {
            $this->db->select("date_format(l.create_date,'%m-%Y') as month, count(l.lead_id) as cnt", FALSE);
            $this->db->from('ts_leads l');
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        } else {
            $this->db->select("date_format(l.create_date,'%m-%Y') as month, count(l.lead_id) as cnt", FALSE);
            $this->db->from('ts_leads l');
        }
        $this->db->where('l.lead_type',3);
        $this->db->where('unix_timestamp(create_date) >= ', $datestart);
        $this->db->where('unix_timestamp(create_date) < ', $dateend);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('month');
        $clres=$this->db->get()->result_array();
        foreach ($clres as $row) {
            $idx=0;
            foreach ($out as $orow) {
                if ($orow['month']==$row['month']) {
                    $out[$idx]['closed']=$row['cnt'];
                }
                $idx++;
            }
        }
        $idx=0;
        foreach ($out as $row) {
            if ($row['base']!=0) {
                $out[$idx]['percent']=round($row['closed']/$row['base']*100,0).'%';
            }
            $idx++;
        }
        $response['data']=$out;
        // Count previous period
        $this->db->select('count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->where('unix_timestamp(create_date) < ', $datestart);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $datprv=$this->db->get()->row_array();
        if ($datprv['cnt']>0) {
            $response['next']=1;
        }
        $this->db->select('count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->where('unix_timestamp(create_date) >= ', $dateend);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $datnxt=$this->db->get()->row_array();
        if ($datnxt['cnt']>0) {
            $response['prev']=1;
        }
        return $response;
    }

    // Get Minimal Lead Create Date
    public function get_lead_mindate($brand) {
        $this->db->select('min(unix_timestamp(create_date)) as mindate');
        $this->db->from('ts_leads');
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        return $res['mindate'];
    }

    // Get Closed data, Orders summary per weeks
    public function get_closedleads_data($options) {
        $emptyval='&mdash;';
        $emptydat='&nbsp;';
        $startdate=$options['startdate'];
        // Get weeks array
        if ($options['show_feature']==1) {
            $date = strtotime(date("Y-m-d") . " +6 week");
            $enddate=strtotime(date('Y-m-d', strtotime('Sunday this week', $date)).' 23:59:59');
        } else {
            $enddate=strtotime(date('Y-m-d', strtotime('Sunday this week')).' 23:59:59');
        }
        $date=strtotime(date("Y-m-d", $enddate) . " -6 days");
        $curyear=date('Y');
        $totals=array(
            'newleads'=>0,
            'newleads'=>0,
            'wrkleads'=>0,
            'outcalls'=>0,
            'orders'=>0,
            'revenue'=>0,
            'profit'=>0,
            'points'=>0,
            'cmpprofit'=>0,
            'goals'=>0,
        );
        $weekkey=array();
        $weeks=array();
        $weeks[]=array(
            'bgn'=>$date,
            'end'=>$enddate,
            'week'=>date('W-o',$date),
            'weeknum'=>date('W',$date),
            'year'=>date('Y',$date),
            'yearweek'=>date('Y',$enddate),
            'newleads'=>0,
            'wrkleads'=>0,
            'outcalls'=>0,
            'orders'=>0,
            'revenue'=>0,
            'profit'=>0,
            'points'=>0,
            'cmpprofit'=>0,
            'goals'=>0,
            'curweek'=>($options['show_feature']==0 ? 1 : 0),
            'project'=>0,
            'goals'=>$emptyval,
            'goalperc'=>$emptyval,
            'goalperc_class'=>'empty',

        );
        array_push($weekkey,date('o-W',$date));
        $weekoptions=array(
            'start'=>$date,
            'end'=>$enddate,
            'brand' => $options['brand'],
        );
        if (isset($options['user_id'])) {
            $weekoptions['user_id']=$options['user_id'];
        }
        $curweek=$this->get_leadclosed_details($weekoptions);
        while (true) {
            $date=strtotime(date("Y-m-d", $date) . " -1 week");
            $sunday=strtotime(date('Y-m-d', strtotime('Sunday this week',$date)).' 23:59:59');
            $weeks[]=array(
                'bgn'=>$date,
                'end'=>$sunday,
                'week'=>date('W-o',$date),
                'weeknum'=>date('W',$date),
                'year'=>date('Y',$date),
                'yearweek'=>date('Y',$sunday),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'points'=>0,
                'cmpprofit'=>0,
                'goals'=>$emptyval,
                'goalperc'=>$emptyval,
                'goalperc_class'=>'empty',
                'curweek'=>0,
                'project'=>0,
            );
            array_push($weekkey, date('o-W',$date));
            if ($date<$startdate) {
                break;
            }
        }
        // Select New leads
        $this->db->select("date_format(l.create_date,'%x-%v') as week, count(l.lead_id) as cnt",FALSE);
        $this->db->from('ts_leads l');
        if (isset($options['user_id'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        }
        if ($options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('week');
        $res=$this->db->get()->result_array();
        foreach ($res as $row) {
            $key=array_search($row['week'],$weekkey);
            if ($key===FALSE) {
            } else {
                $weeks[$key]['newleads']+=$row['cnt'];
            }
        }

        // Select Updated leads
        $this->db->select("date_format(l.update_date,'%x-%v') as week, count(l.lead_id) as cnt",FALSE);
        $this->db->from('ts_leads l');
        if (isset($options['user_id'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        }
        if ($options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('week');
        $updres=$this->db->get()->result_array();
        foreach ($updres as $row) {
            $key=array_search($row['week'],$weekkey);
            if ($key===FALSE) {
            } else {
                $weeks[$key]['wrkleads']+=$row['cnt'];
            }
        }
        $this->load->model('orders_model');
        $orderres=$this->orders_model->get_orders_leadreport($options);

        foreach ($orderres as $row) {
            $key=array_search($row['week'],$weekkey);
            if ($key===FALSE) {
            } else {
                $weeks[$key]['orders']+=$row['cnt'];
                $weeks[$key]['revenue']+=$row['revenue'];
                $weeks[$key]['profit']+=$row['profit'];
            }
        }
        $total_options = [
            'brand' => $options['brand'],
        ];
        $cmporders=$this->orders_model->get_orders_leadreport($total_options);
        foreach ($cmporders as $row) {
            $key=array_search($row['week'],$weekkey);
            if ($key===FALSE) {
            } else {
                // $weeks[$key]['points']+=round($row['profit']*$this->config->item('profitpts'),0);
                $weeks[$key]['points']+=round($row['profit']*$this->config->item('profitpts'),0);
                $weeks[$key]['cmpprofit']+=$row['profit'];
            }
        }
        $proj=array(
            'project'=>1,
            'brand' => $options['brand'],
        );
        // Get count orders in PROJ stage
        $orderproj=$this->orders_model->get_orders_leadreport($proj);

        foreach ($orderproj as $row) {
            $key=array_search($row['week'],$weekkey);
            if ($key===FALSE) {
            } else {
                $weeks[$key]['project']=($row['cnt']>0 ? 1 : 0);
            }
        }

        $idx=0;
        foreach ($weeks as $row) {
            $weeks[$idx]['label']=date('M d', $row['bgn']).'-'.date('d', $row['end']).', '.date('Y',$row['bgn']);
            $leadurl='';
            if ($row['newleads']>0) {
                if (isset($options['user_id'])) {
                    $leadurl='/leads/leadsclosed_usrleads?bgn='.$row['bgn'].'&user='.$options['user_id'].'&leadtype=new&brand='.$options['brand'];
                } else {
                    $leadurl='/leads/leadsclosed_companyleads?bgn='.$row['bgn'].'&leadtype=new&brand='.$options['brand'];
                }
            }
            $weeks[$idx]['newleadsurl']=$leadurl;
            $leadurl='';
            if ($row['wrkleads']>0) {
                if (isset($options['user_id'])) {
                    $leadurl='/leads/leadsclosed_usrleads?bgn='.$row['bgn'].'&user='.$options['user_id'].'&leadtype=wrk&brand='.$options['brand'];
                } else {
                    $leadurl='/leads/leadsclosed_companyleads?bgn='.$row['bgn'].'&leadtype=wrk&brand='.$options['brand'];
                }
            }
            $weeks[$idx]['wrkleadsurl']=$leadurl;
            if ($weeks[$idx]['yearweek']==$curyear) {
                $totals['newleads']+=$row['newleads'];
                $totals['wrkleads']+=$row['wrkleads'];
                $totals['outcalls']+=$row['outcalls'];
                $totals['orders']+=$row['orders'];
                $totals['revenue']+=$row['revenue'];
                $totals['profit']+=$row['profit'];
                $totals['points']+=$row['points'];
                $totals['cmpprofit']+=$row['cmpprofit'];
            }
            $weeks[$idx]['newleads']=($row['newleads']==0 ? $emptyval : $row['newleads']);
            $weeks[$idx]['wrkleads']=($row['wrkleads']==0 ? $emptyval : $row['wrkleads']);
            $weeks[$idx]['outcalls']=($row['outcalls']==0 ? $emptyval : $row['outcalls']);
            $ordersurl='';
            if ($row['orders']>0) {
                if (isset($options['user_id'])) {
                    $ordersurl='/leads/leadsclosed_usrorders?bgn='.$row['bgn'].'&user='.$options['user_id'].'&brand='.$options['brand'];
                } else {
                    $ordersurl='/leads/leadsclosed_companyorders?bgn='.$row['bgn'].'&brand='.$options['brand'];
                }
            }
            $weeks[$idx]['ordersurl']=$ordersurl;
            $weeks[$idx]['orders']=($row['orders']==0 ? $emptyval : $row['orders']);
            $weeks[$idx]['revenue']=($row['revenue']==0 ? $emptyval : '$'.number_format(round($row['revenue'],0),0,'.',','));
            $weeks[$idx]['profit']=($row['profit']==0 ? $emptyval : round($row['profit']*$this->config->item('profitpts'),0));
            $cmpordurl='';
            if ($row['points']>0) {
                $cmpordurl='/leads/leadsclosed_cmporders?bgn='.$row['bgn'].'&brand='.$options['brand'];
            }
            $weeks[$idx]['cmporderurl']=$cmpordurl;
            $weeks[$idx]['points']=($row['points']==0 ? $emptyval : $row['points']);
            $weeks[$idx]['weekclass']=($row['project']==0 ? '' : 'proj');
            // Calc Goal and goal Proc, class
            if (intval(date('Y',$row['bgn']))<2015) {
                $weeks[$idx]['goals']=$emptydat;
                $weeks[$idx]['goalperc']=$emptydat;
            } else {
                $weekbgn=strtotime(date("Y-m-d", $row['bgn']) . " - 52 weeks");
                $newkey=date('o-W',$weekbgn);
                // $newkey=(intval($row['yearweek'])-1).'-'.$row['weeknum'];
                $srch=array_search($newkey, $weekkey);
                if ($srch===FALSE) {
                    // No data
                    $weeks[$idx]['goals']='n/a';
                    $weeks[$idx]['goalperc']='n/a';
                } else {
                    $goals=round($weeks[$srch]['cmpprofit']*$this->config->item('cmpprofitpts'),0);
                    if ($weeks[$idx]['yearweek']==$curyear) {
                        $totals['goals']+=$goals;
                    }
                    $perc=0;
                    if ($goals>0) {
                        $perc=round(floatval($weeks[$idx]['points'])/$goals*100,0);
                    }
                    $weeks[$idx]['goals']=$goals;
                    $weeks[$idx]['goalperc']=$perc.'%';
                    $weeks[$idx]['goalperc_class']=($perc<100 ? 'red' : 'blue');
                }
            }
            $idx++;
        }
        // Out Totals
        if ($totals['goals']>0) {
            $perc=round($totals['points']/$totals['goals']*100,0).'%';
            $totals['goalperc_class']=($perc<100 ? 'red' : 'blue');
        } else {
            $perc='n/a';
            $totals['goalperc_class']='empty';
        }
        $totals['goalperc']=$perc;
        $totals['goals']=($totals['goals']==0 ? $emptyval : $totals['goals']);
        $totals['newleads']=($totals['newleads']==0 ? $emptyval : $totals['newleads']);
        $totals['wrkleads']=($totals['wrkleads']==0 ? $emptyval : $totals['wrkleads']);
        $totals['outcalls']=($totals['outcalls']==0 ? $emptyval : $totals['outcalls']);
        $totals['orders']=($totals['orders']==0 ? $emptyval : $totals['orders']);
        $totals['revenue']=($totals['revenue']==0 ? $emptyval : MoneyOutput($totals['revenue'],0));
        $totals['points']=($totals['points']==0 ? $emptyval : round($totals['points'],0));
        $totals['profit']=($totals['profit']==0 ? $emptyval : round($totals['profit']*$this->config->item('profitpts'),0));
        $totals['cmpprofit']=($totals['cmpprofit']==0 ? $emptyval : $totals['cmpprofit']);
        // Orders
        $data=array(
            'weeks'=>$weeks,
            'curweek'=>$curweek,
            'totals'=>$totals,
        );
        return $data;
    }

    public function get_leadclosed_details($options) {
        // Sunday begin
        $emptyval='&mdash;';
        $date=$options['start'];
        $week=array();
        $limit=strtotime(date('Y-m-d').' 23:59:59');
        $day=strtotime(date('Y-m-d', strtotime('Sunday this week',$date)).' 23:59:59');
        if ($day<=$limit) {
            $week[]=array(
                'day'=>date('m/d/Y',strtotime('Sunday this week',$date)),
                'label'=>date('d-D',strtotime('Sunday this week',$date)),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'weekend'=>1,
            );
        }
        $day=strtotime(date('Y-m-d', strtotime('Saturday this week',$date)).' 23:59:59');
        if ($day<=$limit) {
            $week[]=array(
                'day'=>date('m/d/Y',strtotime('Saturday this week',$date)),
                'label'=>date('d-D',strtotime('Saturday this week',$date)),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'weekend'=>1,
            );
        }
        $day=strtotime(date('Y-m-d', strtotime('Friday this week',$date)).' 23:59:59');
        if ($day<=$limit) {
            $week[]=array(
                'day'=>date('m/d/Y',strtotime('Friday this week',$date)),
                'label'=>date('d-D',strtotime('Friday this week',$date)),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'weekend'=>0,
            );
        }
        $day=strtotime(date('Y-m-d', strtotime('Thursday this week',$date)).' 23:59:59');
        if ($day<=$limit) {
            $week[]=array(
                'day'=>date('m/d/Y',strtotime('Thursday this week',$date)),
                'label'=>date('d-D',strtotime('Thursday this week',$date)),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'weekend'=>0,
            );
        }
        $day=strtotime(date('Y-m-d', strtotime('Wednesday this week',$date)).' 23:59:59');
        if ($day<=$limit) {
            $week[]=array(
                'day'=>date('m/d/Y',strtotime('Wednesday this week',$date)),
                'label'=>date('d-D',strtotime('Wednesday this week',$date)),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'weekend'=>0,
            );
        }
        $day=strtotime(date('Y-m-d', strtotime('Tuesday this week',$date)).' 23:59:59');
        if ($day<=$limit) {
            $week[]=array(
                'day'=>date('m/d/Y',strtotime('Tuesday this week',$date)),
                'label'=>date('d-D',strtotime('Tuesday this week',$date)),
                'newleads'=>0,
                'wrkleads'=>0,
                'outcalls'=>0,
                'orders'=>0,
                'revenue'=>0,
                'profit'=>0,
                'weekend'=>0,
            );
        }
        $week[]=array(
            'day'=>date('m/d/Y',strtotime('Monday this week',$date)),
            'label'=>date('d-D',strtotime('Monday this week',$date)),
            'newleads'=>0,
            'wrkleads'=>0,
            'outcalls'=>0,
            'orders'=>0,
            'revenue'=>0,
            'profit'=>0,
            'weekend'=>0,
        );
        // Select New leads
        $this->db->select("date_format(l.create_date,'%m/%d/%Y') as day, count(l.lead_id) as cnt",FALSE);
        $this->db->from('ts_leads l');
        if (isset($options['user_id'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        }
        $this->db->where('unix_timestamp(l.create_date) >= ', $options['start']);
        $this->db->where('unix_timestamp(l.create_date) <= ', $options['end']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('day');
        $res=$this->db->get()->result_array();
        foreach ($res as $row) {
            $key=0;
            foreach ($week as $wrow) {
                if ($wrow['day']==$row['day']) {
                    $week[$key]['newleads']+=$row['cnt'];
                    break;
                }
                $key++;
            }
        }
        // Select Updated leads
        $this->db->select("date_format(l.update_date,'%m/%d/%Y') as day, count(l.lead_id) as cnt",FALSE);
        $this->db->from('ts_leads l');
        if (isset($options['user_id'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        }
        $this->db->where('unix_timestamp(l.update_date) >= ', $options['start']);
        $this->db->where('unix_timestamp(l.update_date) <= ', $options['end']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('day');
        $updres=$this->db->get()->result_array();
        foreach ($updres as $row) {
            $key=0;
            foreach ($week as $wrow) {
                if ($wrow['day']==$row['day']) {
                    $week[$key]['wrkleads']+=$row['cnt'];
                    break;
                }
                $key++;
            }
        }
        $this->load->model('orders_model');
        $orderres=$this->orders_model->get_orders_weekleadreport($options);
        foreach ($orderres as $row) {
            $key=0;
            foreach ($week as $wrow) {
                if ($wrow['day']==$row['day']) {
                    $week[$key]['orders']+=$row['cnt'];
                    $week[$key]['revenue']+=$row['revenue'];
                    $week[$key]['profit']+=$row['profit'];
                    break;
                }
                $key++;
            }
        }

        $weeks=array();
        foreach ($week as $wrow) {
            if ($wrow['weekend']==1) {
                $total=$wrow['newleads']+$wrow['wrkleads']+$wrow['outcalls']+$wrow['orders']+$wrow['profit']; // Add Orders
            } else {
                $total=1;
            }
            if ($total!=0) {
                $weeks[]=array(
                    'label'=>$wrow['label'],
                    'newleads'=>($wrow['newleads']==0 ? $emptyval : $wrow['newleads']),
                    'wrkleads'=>($wrow['wrkleads']==0 ? $emptyval : $wrow['wrkleads']),
                    'outcalls'=>($wrow['outcalls']==0 ? $emptyval : $wrow['outcalls']),
                    'orders'=>($wrow['orders']==0 ? $emptyval : $wrow['orders']),
                    'revenue'=>($wrow['revenue']==0 ? $emptyval : MoneyOutput($wrow['revenue'],0)),
                    'profit'=>($wrow['profit']==0 ? $emptyval : number_format(round($wrow['profit']*$this->config->item('profitpts'),0),0,'.',',')),
                );
            }
        }
        // Get Created
        return $weeks;
    }

    public function get_newleads($options) {
        $emptyval='&mdash;';
        $this->db->select('l.*');
        $this->db->from('ts_leads l');
        if (isset($options['user_id'])) {
            $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
            $this->db->where('lu.user_id',$options['user_id']);
        }
        if (isset($options['begin'])) {
            if ($options['leadtype']=='new') {
                $this->db->where('unix_timestamp(l.create_date) >= ', $options['begin']);
            } else {
                $this->db->where('unix_timestamp(l.update_date) >= ', $options['begin']);
            }
        }
        if (isset($options['end'])) {
            if ($options['leadtype']=='new') {
                $this->db->where('unix_timestamp(l.create_date) <= ', $options['end']);
            } else {
                $this->db->where('unix_timestamp(l.update_date) <= ', $options['end']);
            }
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->order_by('l.lead_number','desc');
        $result=$this->db->get()->result_array();
        $out=array();

        foreach ($result as $row) {
            // $row['out_value']=(floatval($row['lead_value'])==0 ? '?' : '$'.number_format($row['lead_value'],0,'.',''));
            $row['out_value']=(floatval($row['lead_value'])==0 ? $emptyval : round($row['lead_value']*$this->config->item('leadpts'),0));
            $row['contact']=($row['lead_company']=='' ? ($row['lead_customer']=='' ? $row['lead_mail'] : $row['lead_customer']) : $row['lead_company']);
            $row['lead_needby']=($row['lead_needby']=='' ? '&nbsp;' : $row['lead_needby']);
            $row['lead_customer']=($row['lead_customer']=='' ? '&nbsp;' : $row['lead_customer']);
            $row['lead_itemqty']=($row['lead_itemqty']=='' ? '&nbsp;' : $row['lead_itemqty']);
            $row['lead_itemclass']='';
            switch ($row['lead_item']) {
                case '':
                    $row['out_lead_item']='&nbsp;';
                    break;
                case 'Other':
                case 'Multiple':
                case 'Custom Shaped Stress Balls':
                    $row['lead_itemclass']='custom';
                    if ($row['other_item_name']=='') {
                        $row['out_lead_item']=$row['lead_item'];
                    } else {
                        $row['out_lead_item']=$row['other_item_name'];
                    }
                    break;
                default :
                    $row['out_lead_item']=$row['lead_item'];
                    break;
            }
            $out[]=$row;
        }
        return $out;
    }

    public function get_company_leads($options) {
        $emptyval='&mdash;';
        $totals=array(
            'newleads'=>0,
            'wrkleads'=>0,
            'outcalls'=>0,
        );
        $usrs=array();
        $usrleads=array();
        // New Leads
        $this->db->select('count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->where('unix_timestamp(create_date) >= ', $options['begin']);
        $this->db->where('unix_timestamp(create_date) <= ', $options['end']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $resnew=$this->db->get()->row_array();
        $totals['newleads']=($resnew['cnt']==0 ? $emptyval : $resnew['cnt']);
        $this->db->select('u.user_leadname, lu.user_id, count(l.lead_id) as cnt');
        $this->db->from('ts_leads l');
        $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
        $this->db->join('users u','u.user_id=lu.user_id');
        $this->db->where('unix_timestamp(l.update_date) >= ', $options['begin']);
        $this->db->where('unix_timestamp(l.update_date) <= ', $options['end']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('u.user_leadname, lu.user_id');
        $this->db->order_by('cnt','desc');
        $usrwrk=$this->db->get()->result_array();
        foreach ($usrwrk as $row) {
            array_push($usrs, $row['user_id']);
            $usrleads[]=array(
                'user_name'=>$row['user_leadname'],
                'newleads'=>0,
                'wrkleads'=>$row['cnt'],
                'outcalls'=>0,
            );
        }
        // Update Leads
        $this->db->select('count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->where('unix_timestamp(update_date) >= ', $options['begin']);
        $this->db->where('unix_timestamp(update_date) <= ', $options['end']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $reswrk=$this->db->get()->row_array();
        $totals['wrkleads']=($reswrk['cnt']==0 ? $emptyval : $reswrk['cnt']);
        $this->db->select('u.user_leadname, lu.user_id, count(l.lead_id) as cnt');
        $this->db->from('ts_leads l');
        $this->db->join('ts_lead_users lu','lu.lead_id=l.lead_id');
        $this->db->join('users u','u.user_id=lu.user_id');
        $this->db->where('unix_timestamp(create_date) >= ', $options['begin']);
        $this->db->where('unix_timestamp(create_date) <= ', $options['end']);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('l.brand', $options['brand']);
        }
        $this->db->group_by('u.user_leadname, lu.user_id');
        $this->db->order_by('cnt','desc');
        $usrnew=$this->db->get()->result_array();
        foreach ($usrnew as $row) {
            if (!in_array($row['user_id'], $usrs)) {
                array_push($usrs, $row['user_id']);
                $usrleads[]=array(
                    'user_name'=>$row['user_leadname'],
                    'newleads'=>$row['cnt'],
                    'wrkleads'=>0,
                    'outcalls'=>0,
                );
            } else {
                $key=  array_search($row['user_id'], $usrs);
                $usrleads[$key]['newleads']+=$row['cnt'];
            }
        }
        // Out Calls
        $totals['outcalls']=($totals['outcalls']==0 ? $emptyval : $totals['outcalls']);
        // Users
        $idx=0;
        foreach ($usrleads as $row) {
            $usrleads[$idx]['newleads']=($row['newleads']==0 ? $emptyval : $row['newleads']);
            $usrleads[$idx]['wrkleads']=($row['wrkleads']==0 ? $emptyval : $row['wrkleads']);
            $usrleads[$idx]['outcalls']=($row['outcalls']==0 ? $emptyval : $row['outcalls']);
            $idx++;
        }
        // Return data
        $data=array(
            'totals'=>$totals,
            'usrdata'=>$usrleads,
        );
        return $data;
    }

    // Totals from Year begin
    public function get_yearleads($brand) {
        $emptyval='&mdash;';
        $out=array(
            'weeks'=>$emptyval,
            'newleads'=>$emptyval,
            'wrkleads'=>$emptyval,
            'outcalls'=>$emptyval,
            'orders_reg'=>$emptyval,
            'orders_cust'=>$emptyval,
            'orders'=>$emptyval,
            'revenue_reg'=>$emptyval,
            'revenue_cust'=>$emptyval,
            'revenue'=>$emptyval,
            'points_reg'=>$emptyval,
            'points_cust'=>$emptyval,
            'points'=>$emptyval,
        );
        // Get first monday;
        $date=strtotime(date('Y').'-01-01');
        $sundate=strtotime(date('Y-m-d', strtotime('Sunday this week', $date)));
        $startdate = strtotime(date("Y-m-d", $sundate) . " -6 days");
        $enddate=strtotime(date('Y-m-d', strtotime('Sunday this week')).' 23:59:59');
        // New Leads
        $out['weeks']=intval(date('W'));
        $this->db->select('count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->where('unix_timestamp(create_date) >=', $startdate);
        $this->db->where('unix_timestamp(create_date) < ', $enddate);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        if ($res['cnt']>0) {
            $out['newleads']=$res['cnt'];
        }
        // New Leads
        $out['weeks']=intval(date('W'));
        $this->db->select('count(lead_id) as cnt');
        $this->db->from('ts_leads');
        $this->db->where('unix_timestamp(update_date) >=', $startdate);
        $this->db->where('unix_timestamp(update_date) < ', $enddate);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        if ($res['cnt']>0) {
            $out['wrkleads']=$res['cnt'];
        }
        // All Orders
        $this->db->select('count(order_id) as cnt, sum(revenue) as revenue, sum(profit) as profit');
        $this->db->from('ts_orders');
        $this->db->where('order_date >=', $startdate);
        $this->db->where('order_date < ', $enddate);
        $this->db->where('is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            return $out;
        }
        $orders=$revenue=$points=0;
        if ($res['cnt']>0) {
            $orders=$res['cnt'];
            $out['orders']=$res['cnt'];
        }
        if (floatval($res['revenue'])!=0) {
            $revenue=$res['revenue'];
            $out['revenue']=number_format($revenue,0,'.',',');
        }
        if (floatval($res['profit'])!=0) {
            $points=round($res['profit']*$this->config->item('profitpts'),0);
            $out['points']=number_format($points,0,'.',',');
        }
        // Count Custom Orders
        $this->db->select('count(order_id) as cnt, sum(revenue) as revenue, sum(profit) as profit');
        $this->db->from('ts_orders');
        $this->db->where('order_date >=', $startdate);
        $this->db->where('order_date < ', $enddate);
        $this->db->where('item_id',$this->config->item('custom_id'));
        $this->db->where('is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        $orders_cust=$revenue_cust=$points_cust=0;
        if ($res['cnt']>0) {
            $orders_cust=$res['cnt'];
            $out['orders_cust']=$orders_cust;
        }
        $orders_reg=($orders-$orders_cust);
        if ($orders_reg>0) {
            $out['orders_reg']=$orders_reg;
        }
        if (floatval($res['revenue'])!=0) {
            $revenue_cust=$res['revenue'];
            $out['revenue_cust']=number_format($revenue_cust,0,'.',',');
        }
        $revenue_reg=($revenue-$revenue_cust);
        if ($revenue_reg!=0) {
            $out['revenue_reg']=number_format($revenue_reg,0,'.',',');
        }
        if (floatval($res['profit'])!=0) {
            $points_cust=round($res['profit']*$this->config->item('profitpts'),0);
            $out['points_cust']=number_format($points_cust,0,'.',',');
        }
        $points_reg=($points-$points_cust);
        if ($points_reg!=0) {
            $out['points_reg']=number_format($points_reg,0,'.',',');
        }
        return $out;
    }

    private function _leadclose_log($leadpost, $oldlead, $usrlog, $mails)
    {
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype']='html';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);

        $email_from=$this->config->item('email_notification_sender');

        $email_to=array();
        foreach ($mails as $row) {
            array_push($email_to,$row['user_email']);
        }

        $email_body='At '.date('hA:i').' on '.date('m/d/y').' '.$usrlog['user_name'].' changed lead # '.$leadpost['lead_number'].' ';
        if (isset($leadpost['lead_itemqty']) && $leadpost['lead_itemqty']) {
            $email_body.='for '.$leadpost['lead_itemqty'].' ';
        }
        if (isset($leadpost['lead_item']) && $leadpost['lead_item']) {
            $email_body.=$leadpost['lead_item'].' ';
        }
        if (isset($leadpost['lead_customer']) && $leadpost['lead_customer']) {
            $email_body.='by '.$leadpost['lead_customer'].' ';
        }
        $email_body.'into a Closed Order.';
        if (empty($oldlead)) {
            $email_body.='<br/>It is a new Lead';
        } else {
            $email_body.='Below is the status update saved:<br/>';
            switch ($oldlead['lead_type']) {
                case '1':
                    $email_body.='Priority';
                    break;
                case '2':
                    $email_body.='Open';
                    break;
                case '3':
                    $email_body.='Dead';
                    break;
                case '4':
                    $email_body.='Closed';
                    break;
            }
        }

        $this->email->from($email_from);
        $this->email->to($email_to);
        $subj=$usrlog['user_name']." closed Lead #".$leadpost['lead_id'];
        $this->email->subject($subj);
        $this->email->message($email_body);
        $this->email->send();
        $this->email->clear(TRUE);
        return TRUE;
    }

    public function create_leadcustomform($formdata, $leademail_id, $user_id) {
        $out=array('result'=>  $this->error_result,'msg'=>  $this->INIT_ERRMSG);
        /* Create array with Lead data */
        $lead_usr=[$user_id];
        $leadpost=[
            'lead_id'=>0,
            'lead_company'=> $formdata['customer_company'],
            'lead_phone'=> $formdata['customer_phone'],
            'lead_customer'=> $formdata['customer_name'],
            'lead_mail'=> $formdata['customer_email'],
            'lead_itemqty'=> $formdata['quota_qty'],
            'lead_item'=> 'Custom Item',
            'other_item_name'=> $formdata['shape_desription'],
            'lead_item_id' => $this->config->item('custom_id'),
            'lead_needby'=> (empty($formdata['ship_date']) ? NULL : date('Y-m-d', $formdata['ship_date'])),
            'lead_status'=>'',
            'lead_value' => '',
            'lead_note' => '',
            'lead_type'=>$this->init_lead_type,
            'brand' => $formdata['brand'],
        ];
        $lead_tasks = [
            'send_quote'=>0,
            'send_artproof'=>0,
            'send_sample'=>0,
            'answer_question'=>0,
            'other'=>NULL,
            'leadtask_id'=>0,
        ];
        $res=$this->save_leads($lead_usr, $lead_tasks, $leadpost, $user_id);
        if ($res['result']==$this->error_result) {
            $out['msg'] = $res['msg'];
        } else {
            $out['result'] = $this->success_result;
            $out['lead_id'] = $res['result'];
            // Create relations between Mail and Leads
            if (intval($leademail_id)==0) {
                $this->db->set('lead_id',$res['result']);
                $this->db->set('custom_quote_id', $formdata['custom_quote_id']);
                $this->db->insert('ts_lead_emails');
                $out['relation_id'] = $this->db->insert_id();
            }
        }
        return $out;
    }


}
/* End of file leads_model.php */
/* Location: ./application/models/leads_model.php */