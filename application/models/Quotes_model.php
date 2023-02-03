<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Quotes_model extends My_Model {
    private $INIT_ERRMSG='Unknown error. Try later';
    private $EMAIL_TYPE='Leads';
    private $EMAIL_SUBTYPE='Quote';
    private $EMAIL_EMPTY='----';
    private $bt_main='http://bluetrack.com';
    private $sb_main='http://stressball.com';
    private $startdate='2013-01-17';

    function __construct() {
        parent::__construct();
    }

    public function get_count_quotes($options) {
        $this->db->select('count(e.email_id) as cnt');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        $this->db->where('e.email_subtype', $this->EMAIL_SUBTYPE);
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

    public function get_quotes($search,$order_by,$direct,$limit,$offset,$maxval) {
        $this->db->select('e.*,l.lead_number, l.lead_id');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->join('ts_leads l','l.lead_id=lem.lead_id','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        $this->db->where('e.email_subtype', $this->EMAIL_SUBTYPE);
        if (isset($search['search'])) {
            $this->db->like('upper(concat(coalesce(e.email_sender,""), coalesce(e.email_sendermail,""), coalesce(e.email_sendercompany,"") )) ',  strtoupper($search['search']));
        }
        if (isset($search['assign'])) {
            $this->db->where('lem.email_id is null');
        }
        if (isset($search['hideincl'])) {
            $this->db->where('e.email_include_lead',1);
        }
        if (isset($search['brand'])) {
            if ($search['brand']=='SR') {
                $this->db->where('e.brand',$search['brand']);
            } else {
                $this->db->where_in('e.brand',['BT','SB']);
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
            $row['email_sender']=($row['email_sender']=='' ? '&nbsp;' : $row['email_sender']);
            $row['rowclass']=($row['lead_id']=='' ? '' : 'leadentered');
//            switch ($row['brand']) {
//                case 'BT':
//                    $row['email_quota_link']=$this->bt_main.$row['email_quota_link'];
//                    break;
//                case 'SB':
//                    $row['email_quota_link']=$this->sb_main.$row['email_quota_link'];
//                    break;
//            }
            $row['assign_class']=($row['lead_number']=='' ? 'quoteassign' : '');
            $row['lead_number']=($row['lead_number']=='' ? '&nbsp;' : 'L'.$row['lead_number']);
            $out[]=$row;
            $ordnum--;
        }
        return $out;
    }

    public function get_quote_dat($quote_id) {
        $out = ['result'=> $this->error_result, 'msg' => $this->INIT_ERRMSG];
        $this->db->select('e.*,lem.leademail_id, l.lead_id, l.lead_number, l.lead_date, l.lead_customer, l.lead_mail');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->join('ts_leads l','l.lead_id=lem.lead_id','left');
        $this->db->where('e.email_id',$quote_id);
        $res=$this->db->get()->row_array();
        if (isset($res['email_id'])) {
            $res['email_date']=date('m/d/Y',strtotime($res['email_date']));
            $res['lead_date']=(intval($res['lead_date'])==0 ? '' : date('m/d/y',$res['lead_date']));
            $res['email_sendermaillnk']=($res['email_status']==0 ? '<a href="javascript:void(0);" onclick="replyquestmail(\''.$res['email_sendermail'].'\');return false;">'.$res['email_sendermail'].'</a>' : $res['email_sendermail']);
            $colorprint=get_json_param($res['email_other_info'], 'colorprint', 0);
            if ($colorprint==0) {
                $res['colorimprint']='Blank (no Imprint)';
            } elseif ($colorprint==1) {
                $res['colorimprint']='Imprint  in one color';
            } else {
                $res['colorimprint']='Imprint  in two colors';
            }
            $res['colors']=get_json_param($res['email_other_info'], 'colors', '');
            $itemcolor=get_json_param($res['email_other_info'],'itemcolors',array());
            $res['item_colors']=(count($itemcolor)==0 ? '' : implode(",", $itemcolor));
            $res['rush_days']=get_json_param($res['email_other_info'], 'rush_days', '');
            $res['itemcost']=get_json_param($res['email_other_info'], 'itemcost', 0);
            $res['itemcost']=($res['itemcost']==0 ? '' : '$'.number_format($res['itemcost'],2,'.',','));
            $res['total']=get_json_param($res['email_other_info'], 'total', 0);
            $res['total']=($res['total']==0 ? '' : '$'.number_format($res['total'],2,'.',','));
            $out['result']=$this->success_result;
            $out['data'] = $res;
        }
        return $out;
    }

    public function get_lead_quotes($lead_id) {
        $this->db->select('e.*');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails el','el.email_id=e.email_id');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        $this->db->where('e.email_subtype',  $this->EMAIL_SUBTYPE);
        $this->db->where('el.lead_id',$lead_id);
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            $row['email_date']=date('m/d/y',strtotime($row['email_date']));
            $out[]=$row;
        }
        return $out;
    }

    public function get_todays() {
        $todaybgn=strtotime($this->startdate);
        $this->db->select('count(e.email_id) as cnt');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        $this->db->where('e.email_subtype', $this->EMAIL_SUBTYPE);
        $this->db->where('e.email_include_lead',1);
        $this->db->where('lem.email_id is null');
        $this->db->where('unix_timestamp(email_date) >=',$todaybgn);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            $retval='';
        } else {
            $retval=$res['cnt'];
        }
        return $retval;
    }

    public function quote_include($email_id, $newval) {
        $out=array('result'=> $this->error_result, 'msg'=> $this->INIT_ERRMSG);
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
            );
            $out['newmsg']=$this->get_count_quotes($options);
            $today=$this->get_todays();
            $out['newclass']=(floatval($today)==0 ? 'empval' : 'curmail');
        } else {
            $out['msg']='Quote Not Found';
        }
        return $out;
    }


}
/* End of file quotes_model.php */
/* Location: ./application/models/quotes_model.php */
