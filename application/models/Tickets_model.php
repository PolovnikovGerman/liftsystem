<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Tickets_model extends My_Model
{

    private $INIT_ERRMSG='Unknown error. Try later';
    protected $START_NUMBER='T00-K01';
    

    function __construct()
    {
        parent::__construct();
    }

//    function get_tickets_nums($options=array()) {
//        $this->db->select('count(t.ticket_id) as cnt');
//        $this->db->from('ts_tickets t');
//        $this->db->join('vendors v','v.vendor_id=t.vendor_id','left');
//        $this->db->join('ts_ticket_issues ci','ci.ticket_issue_id=t.custom_issue_id','left');
//        $this->db->join('ts_ticket_issues vi','vi.ticket_issue_id=t.vendor_issue_id','left');
//        if (isset($options['is_open']) && $options['is_open']) {
//            $this->db->where('ticket_closed',0);
//        }
//        if (isset($options['is_closed']) && $options['is_closed']) {
//            $this->db->where('ticket_closed',1);
//        }
//        if (isset($options['ticket_adjast']) && $options['ticket_adjast']==1) {
//            $this->db->where('ticket_adjast',1);
//        }
//        if (isset($options['search']) && !empty($options['search'])) {
//            $this->db->like("concat(t.order_num,upper(t.customer),upper(v.vendor_name),upper(ci.issue_name),upper(vi.issue_name))",$options['search']);
//        }
//        $res=$this->db->get()->row_array();
//
//        return $res['cnt'];
//    }
//
//    function get_tickets($options,$order_by,$direct,$limit,$offset) {
//        $this->db->select('t.*, v.vendor_name as vendor,ci.issue_name as custom_issue, vi.issue_name as vendor_issue, cntatt.cnt as cnt_attach');
//        $this->db->from('ts_tickets t');
//        $this->db->join('vendors v','v.vendor_id=t.vendor_id','left');
//        $this->db->join('ts_ticket_issues ci','ci.ticket_issue_id=t.custom_issue_id','left');
//        $this->db->join('ts_ticket_issues vi','vi.ticket_issue_id=t.vendor_issue_id','left');
//        $this->db->join('(select ticket_id, count(ticket_doc_id) as cnt from ts_ticket_docs group by ticket_id) as cntatt','cntatt.ticket_id=t.ticket_id','left');
//        if (isset($options['is_open']) && $options['is_open']) {
//            $this->db->where('t.ticket_closed',0);
//        }
//        if (isset($options['is_closed']) && $options['is_closed']) {
//            $this->db->where('t.ticket_closed',1);
//        }
//        if (isset($options['ticket_adjast']) && $options['ticket_adjast']==1) {
//            $this->db->where('t.ticket_adjast',1);
//        }
//        if (isset($options['search']) && !empty($options['search'])) {
//            // $this->db->like("concat(coalesce(t.order_num,''),upper(coalesce(t.customer,'')),upper(v.vendor_name),upper(ci.issue_name),upper(vi.issue_name))",$options['search']);
//            $this->db->like("concat(coalesce(t.order_num,''),upper(coalesce(t.customer,'')),upper(coalesce(v.vendor_name,'')),upper(coalesce(ci.issue_name,'')),upper(coalesce(vi.issue_name,'')))",$options['search']);
//        }
//        $this->db->order_by($order_by,$direct);
//        $this->db->limit($limit,$offset);
//        $result=$this->db->get()->result_array();
//        $out=array();
//        foreach ($result as $row) {
//            $row['closedclass']="";
//            $row['vendor_class']="vendor_title_dat";
//            $row['customer_class']="customer_title_dat";
//            $row['vendor']=$row['vendor'].$row['other_vendor'];
//            if ($row['ticket_adjast']==0) {
//                $row['ticket_adjast']='';
//            } else {
//                $row['ticket_adjast']='<img src="/img/brownstar.png" alt="Adjast"/>';
//            }
//            if (!empty($row['vendor_issue'])) {
//                if ($row['vendor_close']==0) {
//                    $row['vendor_class']='vendor_colored_dat';
//                }
//            }
//            if (!empty($row['custom_issue'])) {
//                if ($row['custom_close']==0) {
//                    $row['customer_class']="customer_colored_dat";
//                }
//            }
//            if ($row['ticket_closed']) {
//                $row['closedclass']="closedticket";
//            }
//            if (floatval($row['cost'])==0) {
//                $row['cost']='--';
//            } else {
//                $row['cost']='$'.number_format(floatval($row['cost']),2,'.',',');
//            }
//            if ($row['ticket_date']=='') {
//                $row['ticket_date']='&nbsp;';
//            } else {
//                $row['ticket_date']=date('m/d/y',$row['ticket_date']);
//            }
//            $row['cnt_attach']=intval($row['cnt_attach']);
//            $row['attach_title']=($row['cnt_attach']==0 ? '' : $row['cnt_attach'].' attachments');
//            $row['attach_class']=($row['cnt_attach']==0 ? '' : 'tickattachexist');
//            $out[]=$row;
//        }
//        return $out;
//    }

    function get_ticket_data($ticket_id) {
        $this->db->select('*');
        $this->db->from('ts_tickets');
        $this->db->where('ticket_id',$ticket_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    function save_ticket($data,$user_id) {
        $out=array('result'=>0,'msg'=>$this->INIT_ERRMSG);

        $ticket_id=$data['ticket_id'];
        $type=($data['type']=='' ? NULL : $data['type']);
        $order_num=($data['order_num']=='' ? NULL : $data['order_num']);
        $customer=($data['customer']=='' ? NULL : $data['customer']);
        $custom_issue_id=($data['custom_issue_id']=='' ? NULL : $data['custom_issue_id']);
        $custom_description=($data['custom_description']=='' ? NULL : $data['custom_description']);
        $custom_close=(isset($data['custom_close']) ? $data['custom_close'] : 0);
        $custom_history=($data['custom_history']=='' ? NULL : $data['custom_history']);
        $cost=($data['cost']=='' ? NULL : $data['cost']);
        $vendor_id=($data['vendor_id']=='' ? NULL : $data['vendor_id']);
        $vendor_issue_id=($data['vendor_issue_id']=='' ? NULL : $data['vendor_issue_id']);
        $vendor_description=($data['vendor_description']=='' ? NULL : $data['vendor_description']);
        $vendor_close=(isset($data['vendor_close']) ? $data['vendor_close'] : 0);
        $vendor_history=($data['vendor_history']=='' ? NULL : $data['vendor_history']);
        $other_vendor=($data['other_vendor']=='' ? NULL : $data['other_vendor']);
        $ticket_adjast=(isset($data['ticket_adjast']) ? $data['ticket_adjast'] : 0);
        $ticket_date=$data['ticket_date'];
        /* Block of limitation */
        /* */
        if (!$vendor_id) {
            $out['msg']='Select Vendor';
        } elseif ($vendor_id==-1 && !$other_vendor) {
            $out['msg']='Enter other vendor';
        } elseif (!$vendor_issue_id) {
            $out['msg']='Enter Vendor Side Issue';
        } else {
            $ticket_closed=0;
            if ($vendor_close==1) {
                if (!$custom_issue_id) {
                    $ticket_closed=1;
                } else {
                    if ($custom_close==1) {
                        $ticket_closed=1;
                    }
                }
            }
            $this->db->set('ticket_closed',$ticket_closed);
            $this->db->set('type',$type);
            $this->db->set('order_num',$order_num);
            $this->db->set('customer',$customer);
            $this->db->set('custom_issue_id',$custom_issue_id);
            $this->db->set('custom_description',$custom_description);
            $this->db->set('custom_close',$custom_close);
            $this->db->set('ticket_adjast',$ticket_adjast);
            $this->db->set('custom_history',$custom_history);
            $this->db->set('cost',$cost);
            $this->db->set('ticket_date',$ticket_date);
            if ($vendor_id==-1) {
                $vendor_id=NULL;
            } else {
                $other_vendor=NULL;
            }
            $this->db->set('vendor_id',$vendor_id);
            $this->db->set('other_vendor',$other_vendor);
            $this->db->set('vendor_issue_id',$vendor_issue_id);
            $this->db->set('vendor_description',$vendor_description);
            $this->db->set('vendor_close',$vendor_close);
            $this->db->set('vendor_history',$vendor_history);
            $this->db->set('user_updated',$user_id);
            if ($ticket_id==0) {
                /* New Record */
                $this->db->set('created',date('Y-m-d H:i:s'));
                $this->db->set('user_created',$user_id);
                $tikn=$this->get_last_ticknum();
                if ($tikn['max_id']=='') {
                    /* First ticket */
                    $ticket_num1=0;
                    $ticket_num2=1;
                } else {
                    if ($tikn['ticket_num2']==99) {
                        $ticket_num2=0;
                        $ticket_num1=$tikn['ticket_num1']+1;
                    } else {
                        $ticket_num2=$tikn['ticket_num2']+1;
                        $ticket_num1=$tikn['ticket_num1'];
                    }
                }
                $ticket_num='T'.str_pad($ticket_num1,2,'0',STR_PAD_LEFT).'-K'.  str_pad($ticket_num2, 2, '0', STR_PAD_LEFT);
                $this->db->set('ticket_num',$ticket_num);
                $this->db->set('ticket_num1',$ticket_num1);
                $this->db->set('ticket_num2',$ticket_num2);
                $this->db->insert('ts_tickets');
                $result=$this->db->insert_id();
                if ($result!=0) {
                    $out['result']=$result;
                    $out['msg']='';
                } else {
                    $out['msg']='Add of ticket ended with error';
                }
            } else {
                $this->db->where('ticket_id',$ticket_id);
                $this->db->update('ts_tickets');
                $out['result']=1;
                $out['msg']='';
            }
        }
        return $out;
    }

    public function get_last_ticknum() {
        $this->db->select('max(ticket_id) as max_id');
        $this->db->from('ts_tickets');
        $res=$this->db->get()->row_array();
        if (!$res['max_id']) {
            return array('max_id'=>'','ticket_num1'=>0, 'ticket_num2'=>0);
        } else {
            $this->db->select('ticket_num1, ticket_num2');
            $this->db->from('ts_tickets');
            $this->db->where('ticket_id',$res['max_id']);
            $row=$this->db->get()->row_array();
            $res['ticket_num1']=$row['ticket_num1'];
            $res['ticket_num2']=$row['ticket_num2'];
            return $res;
        }
    }

    /* List of issues  */
    public function get_issues($issues_type) {
        $this->db->select('*');
        $this->db->from('ts_ticket_issues');
        $this->db->where('ticket_issue_type',$issues_type);
        $result=$this->db->get()->result_array();
        return $result;
    }

    public function attach_init($ticket_id) {
        $this->db->set('is_deleted',0);
        $this->db->where('ticket_id',$ticket_id);
        $this->db->update('ts_ticket_docs');
        return TRUE;
    }

    public function get_attachments($ticket_id,$sess_id) {
        $this->db->select('*');
        $this->db->from('ts_ticket_docs');
        $this->db->where("(ticket_id={$ticket_id} or session_id='{$sess_id}') and is_deleted = 0 ");
        $res=$this->db->get()->result_array();
        return $res;
    }

//    function save_uploadattach($filename,$doc_name,$sess_id) {
//        $this->db->set('doc_name',$doc_name);
//        $this->db->set('doc_link',$filename);
//        $this->db->set('session_id',$sess_id);
//        $this->db->insert('ts_ticket_docs');
//        return $this->db->insert_id();
//    }

    function save_attach($ticket_id,$sess_id) {
        /* Delete files which was marked as deleted */
        $this->db->select('*');
        $this->db->from('ts_ticket_docs');
        $this->db->where("(ticket_id={$ticket_id} or session_id='{$sess_id}') and is_deleted=1 ");
        $result=$this->db->get()->result_array();
        foreach ($result as $row) {
            if (strpos($row['doc_link'], $this->config->item('ticketattach_path'))) {
                $path=$this->config->item('ticketattach');
                $path_sh=$this->config->item('ticketattach_path');
                $filename=  str_replace($path_sh, $path, $row['doc_link']);
                @unlink($filename);
            }
            $this->db->where('ticket_doc_id',$row['ticket_doc_id']);
            $this->db->delete('ts_ticket_docs');
        }
        /* Update uploaded docs */
        $this->db->select('*');
        $this->db->from('ts_ticket_docs');
        $this->db->where('session_id',$sess_id);
        $this->db->where("ticket_id is null");
        $result=$this->db->get()->result_array();
        foreach ($result as $row) {
            /* Move file to ticket attachment folder */
            $pathdest=$this->config->item('upload_path_preload');
            $pathtarg=$this->config->item('ticketattach');
            $fileTarget=str_replace($pathdest, $pathtarg, $row['doc_link']);
            /* Move file to new destinition */
            $res1 = copy($row['doc_link'],  $fileTarget);
            unlink($row['doc_link']);
            $lnkpath=$this->config->item('ticketattach_path');
            $filename=str_replace($pathtarg, $lnkpath ,$fileTarget);
            /* Save changes */
            $this->db->set('ticket_id',$ticket_id);
            $this->db->set('session_id',NULL);
            $this->db->set('doc_link',$filename);
            $this->db->where('ticket_doc_id',$row['ticket_doc_id']);
            $this->db->update('ts_ticket_docs');
        }
    }

//    function get_ticket_attachm($ticket_id) {
//        $this->db->select('*');
//        $this->db->from('ts_ticket_docs');
//        $this->db->where('ticket_id',$ticket_id);
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
//    function save_parsed($ticketdata, $user_id, $user_name) {
//        $out=array('result'=>0,'msg'=>$this->INIT_ERRMSG);
//        if (!$ticketdata['vendor_id']) {
//            $out['msg']='Select Vendor';
//            return $out;
//        }
//        if ($ticketdata['vendor_id']==-1 && !$ticketdata['other_vendor']) {
//            $out['msg']='Enter other vendor';
//            return $out;
//        }
//        if (!$ticketdata['vendor_issue_id']) {
//            $out['msg']='Enter Vendor Side Issue';
//            return $out;
//        }
//        $ticket_closed=0;
//        if ($ticketdata['vendor_close']==1) {
//            if (!$ticketdata['custom_issue_id']) {
//                $ticket_closed=1;
//            } else {
//                if ($ticketdata['custom_close']==1) {
//                    $ticket_closed=1;
//                }
//            }
//        }
//        $this->db->set('ticket_closed',$ticket_closed);
//        $this->db->set('type',$ticketdata['type']);
//        $this->db->set('order_num',$ticketdata['order_num']);
//        $this->db->set('customer',$ticketdata['customer']);
//        $this->db->set('custom_issue_id',$ticketdata['custom_issue_id']);
//        $this->db->set('custom_description',$ticketdata['custom_description']);
//        $this->db->set('custom_close',$ticketdata['custom_close']);
//        $this->db->set('ticket_adjast',$ticketdata['ticket_adjast']);
//        $this->db->set('custom_history',$ticketdata['custom_history']);
//        $this->db->set('cost',$ticketdata['cost']);
//        $this->db->set('ticket_date',$ticketdata['ticket_date']);
//        $vendor_id=$ticketdata['vendor_id'];
//        $other_vendor=$ticketdata['other_vendor'];
//        if ($ticketdata['vendor_id']==-1) {
//            $vendor_id=NULL;
//        } else {
//            $other_vendor=NULL;
//        }
//        $this->db->set('vendor_id',$vendor_id);
//        $this->db->set('other_vendor',$other_vendor);
//        $this->db->set('vendor_issue_id',$ticketdata['vendor_issue_id']);
//        $this->db->set('vendor_description',$ticketdata['vendor_description']);
//        $this->db->set('vendor_close',$ticketdata['vendor_close']);
//        $this->db->set('vendor_history',$ticketdata['vendor_history']);
//        $this->db->set('user_updated',$user_id);
//        $this->db->where('ticket_id',$ticketdata['ticket_id']);
//        $this->db->update('ts_tickets');
//        $out['result']=1;
//        $out['msg']='';
//        // Save attachments
//        $attachs=$ticketdata['attachments'];
//        foreach ($attachs as $row) {
//            if ($row['is_deleted']==0) {
//                $this->db->set('ticket_id',$ticketdata['ticket_id']);
//                $this->db->set('doc_name',$row['doc_name']);
//                $this->db->set('doc_link',$row['doc_link']);
//                $this->db->set('is_deleted',0);
//                if ($row['ticket_doc_id']<=0) {
//                    $this->db->set('upd_time',date('Y-m-d H:i:s'));
//                    $this->db->insert('ts_ticket_docs');
//                } else {
//                    $this->db->where('ticket_doc_id',$row['ticket_doc_id']);
//                    $this->db->update('ts_ticket_docs');
//                }
//            }
//        }
//        return $out;
//    }
//
    public function get_ticketreport_overview($brand) {
        $this->db->select('quarter(from_unixtime(ticket_date)) as tickquat, date_format(from_unixtime(ticket_date),\'%Y\') as tickyear, count(ticket_id) as cnt', FALSE);
        $this->db->from('ts_tickets');
        $this->db->where('ticket_date is not null');
        $this->db->where('ticket_closed',0);
        $this->db->where('brand', $brand);
        $this->db->order_by('ticket_date');
        $this->db->group_by('tickquat, tickyear');
        $res=$this->db->get()->result_array();
        $out=array();
        $curyear=0;
        foreach ($res as $row) {
            if ($curyear==$row['tickyear']) {
                $row['out_tickyear']='';
            } else {
                $curyear=$row['tickyear'];
                $row['out_tickyear']=$curyear;
            }
            $out[]=$row;
        }
        return $out;
    }

    public function get_ticketreport_details($quater, $year, $brand) {
        $this->db->select('t.ticket_date, t.ticket_num, t.order_num, t.customer, v.vendor_name, unix_timestamp(t.updated) as lastupdate');
        $this->db->from('ts_tickets t');
        $this->db->join('vendors v','v.vendor_id=t.vendor_id','left');
        $this->db->where('t.ticket_closed',0);
        $this->db->where('quarter(from_unixtime(ticket_date))', $quater);
        $this->db->where("date_format(from_unixtime(ticket_date), '%Y')={$year}");
        $this->db->where('t.brand', $brand);
        $this->db->order_by('ticket_date');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function add_newticket($options=array()) {
        $tik_n=$this->get_last_ticknum();
        if ($tik_n['max_id']=='') {
            $ticket_number=$this->START_NUMBER;
        } else {
            if ($tik_n['ticket_num2']==99) {
                $tik_n['ticket_num1']=$tik_n['ticket_num1']+1;
                $tik_n['ticket_num2']=0;
            } else {
                $tik_n['ticket_num2']=$tik_n['ticket_num2']+1;
            }
            $ticket_number='T'.str_pad($tik_n['ticket_num1'],2,'0',STR_PAD_LEFT).'-K'.str_pad($tik_n['ticket_num2'],2,'0',STR_PAD_LEFT);
        }

        $ticket=array(
            'ticket_id'=>0,
            'ticket_num'=>$ticket_number,
            'type'=>'',
            'order_num'=>(isset($options['order_num']) ? $options['order_num'] : ''),
            'customer'=>'',
            'custom_issue_id'=>'',
            'custom_description'=>'',
            'custom_close'=>0,
            'custom_history'=>'',
            'cost'=>'',
            'vendor_id'=>'',
            'vendor_issue_id'=>'',
            'vendor_description'=>'',
            'vendor_close'=>0,
            'vendor_history'=>'',
            'other_vendor'=>'',
            'ticket_adjast'=>'',
            'ticket_date'=>date('m/d/Y'),
        );
        return $ticket;
    }
    
}