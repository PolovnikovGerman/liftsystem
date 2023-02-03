<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Class Questions_model extends My_Model {

    private $START_ORDNUM=22000;
    private $INIT_ERRMSG='Unknown error. Try later';
    private $EMAIL_TYPE='Questions';
    private $EMAIL_EMPTY='----';
    private $startdate='2013-01-17';

    function __construct() {
        parent::__construct();
    }

    public function get_count_questions($options=array()) {
        $this->db->select('count(e.email_id) as cnt');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        if (isset($options['search'])) {
            $this->db->like('upper(concat(coalesce(e.email_sender,""), coalesce(e.email_sendermail,""), coalesce(e.email_sendercompany,"") )) ',  strtoupper($options['search']));
        }
        if (isset($options['assign'])) {
            $this->db->where('lem.email_id is null');
        }
        if (isset($options['hideincl'])) {
            $this->db->where('e.email_include_lead',1);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('e.brand', $options['brand']);
            } else {
                $this->db->where_in('e.brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_questions($search,$order_by,$direct,$limit,$offset,$maxval) {
        $this->db->select('e.*,l.lead_number, l.lead_id');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->join('ts_leads l','l.lead_id=lem.lead_id','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        if (isset($search['search'])) {
            $this->db->like('upper(concat(coalesce(e.email_sender,""), coalesce(e.email_sendermail,""), coalesce(e.email_sendercompany,"") )) ',  strtoupper($search['search']));
        }
        if (isset($search['hideincl'])) {
            $this->db->where('e.email_include_lead',1);
        }
        if (isset($search['assign'])) {
            $this->db->where('lem.email_id is null');
        }
        if (isset($search['brand']) && $search['brand']!=='ALL') {
            if ($search['brand']=='SR') {
                $this->db->where('e.brand', $search['brand']);
            } else {
                $this->db->where_in('e.brand', ['BT','SB']);
            }
        }
        $this->db->limit($limit,$offset);
        $this->db->order_by($order_by,$direct);
        $res=$this->db->get()->result_array();
        $out=array();
        if ($offset>$maxval) {
            $ordnum = $maxval;
        } else {
            $ordnum = $maxval - $offset;
        }

        $incl_icon='<img src="/img/leads/noninclide_lead_icon.png" alt="Include"/>';
        $nonincl_icon='<img src="/img/leads/inclide_lead_icon.png" alt="Non Include"/>';
        foreach ($res as $row) {
            $row['ordnum']=$ordnum;
            $row['inclicon']='&nbsp';
            if ($row['lead_id']=='') {
                $row['inclicon']=($row['email_include_lead']==0 ? $nonincl_icon : $incl_icon);
            }
            $row['email_date']=date('m/d/y',strtotime($row['email_date']));
            if ($row['email_sendermail']) {
                $row['email_sendermail']=($row['email_status']==0 ? '<a href="javascript:void(0);" onclick="replyquestmail(\''.$row['email_sendermail'].'\');return false;">'.$row['email_sendermail'].'</a>' : $row['email_sendermail']);
            } else {
                $row['email_sendermail']=$this->EMAIL_EMPTY;
            }
            $row['rowclass']=($row['lead_id']=='' ? '' : 'leadentered');
            $row['assign_class']=($row['lead_number']=='' ? 'questassign' : '');
            $row['lead_number']=($row['lead_number']=='' ? '&nbsp;' : 'L'.$row['lead_number']);
            $out[]=$row;
            $ordnum--;
        }
        return $out;
    }

    public function get_quest_data($quest_id) {
        $out=['result' => $this->error_result, 'msg' => $this->INIT_ERRMSG];
        $this->db->select('e.*,lem.leademail_id, l.lead_id, l.lead_number, l.lead_date, l.lead_customer, l.lead_mail');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->join('ts_leads l','l.lead_id=lem.lead_id','left');
        $this->db->where('e.email_id',$quest_id);
        $res=$this->db->get()->row_array();
        if (isset($res['email_id'])) {
            $out['result']=$this->success_result;
            $res['email_date']=date('m/d/Y',strtotime($res['email_date']));
            $res['lead_date']=(intval($res['lead_date'])==0 ? '' : date('m/d/y',$res['lead_date']));
            $res['chk']=($res['email_status']==0 ? 'chreplic' : '');
            $res['email_sendermaillnk']=($res['email_status']==0 ? '<a href="javascript:void(0);" onclick="replyquestmail(\''.$res['email_sendermail'].'\');return false;">'.$res['email_sendermail'].'</a>' : $res['email_sendermail']);
            $out['data'] = $res;
        }
        return $out;
    }

//    function save_queststatus($quest) {
//        $out=array('result'=>  Questions_model::ERR_FLAG,'msg'=>  Questions_model::INIT_ERRMSG);
//        if (!isset($quest['mail_id'])) {
//            $out['msg']='Unknown question';
//        } elseif(!$quest['mail_id']) {
//            $out['msg']='Wrong Question';
//        } elseif (empty($quest['mail_rev'])) {
//            $out['msg']='Enter Initial of rep';
//        } else {
//            $this->db->set('email_status',1);
//            $this->db->set('email_rep',  strtoupper($quest['mail_rev']));
//            $this->db->where('email_id',$quest['mail_id']);
//            $this->db->update('ts_emails');
//            $out['result']=Questions_model::SUCCESS_RESULT;
//            $out['msg']='';
//        }
//        return $out;
//    }

    public function get_lead_questions($lead_id) {
        $this->db->select('e.*');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails el','el.email_id=e.email_id');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        $this->db->where('el.lead_id',$lead_id);
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            $row['email_date']=date('m/d/y',strtotime($row['email_date']));
            $out[]=$row;
        }
        return $out;
    }

    function get_todays() {
        // $todaybgn=strtotime($this->startdate);
        $this->db->select('count(e.email_id) as cnt');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->where('email_type',  $this->EMAIL_TYPE);
        $this->db->where('lem.email_id is null');
        $this->db->where('e.email_include_lead',1);
        // $this->db->where('unix_timestamp(email_date) >=',$todaybgn);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            $retval='';
        } else {
            $retval=$res['cnt'];
        }
        return $retval;
    }

    public function question_include($email_id, $newval) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        $incl_icon='<img src="/img/noninclide_lead_icon.png" alt="Include"/>';
        $nonincl_icon='<img src="/img/inclide_lead_icon.png" alt="Non Include"/>';
        $this->db->set('email_include_lead',$newval);
        $this->db->where('email_id',$email_id);
        $this->db->update('ts_emails');
        if ($this->db->affected_rows()==1) {
            $out['result']=  $this->success_result;
            $out['msg']='';
            $out['newicon']=($newval==0 ? $nonincl_icon : $incl_icon);
            $options=array(
                'assign'=>1,
                'hideincl'=>1,
            );
            $totals=$this->get_count_questions($options);
            $out['newmsg']=($totals==0 ? '' : intval($totals));
            $today=$this->get_todays();
            $out['newclass']=(floatval($today)==0 ? 'empval' : 'curmail');
        } else {
            $out['msg']='Question Not Found';
        }
        return $out;
    }

}
/* End of file questions_model.php */
/* Location: ./application/models/questions_model.php */