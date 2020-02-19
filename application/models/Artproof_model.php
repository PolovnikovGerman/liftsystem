<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Artproof_model extends MY_Model
{
    private $EMAIL_TYPE='Art_Submit';
    private $INIT_ERRMSG='Unknown error. Try later';
    private $order_status=3;
    private $void_status=4;
    private $active_status=1;

    private $noart_common_overdue=3600000; /* 24 h */
    private $noart_rush_overdue=3600000; /* 2 h */
    private $redraw_common_overdue=3600000; /* 8 h */
    private $redraw_rush_overdue=3600000; /* 1 h */
    private $toproof_common_overdue=3600000; /* 24 h */
    private $toproof_rush_overdue=3600000; /* 2 h */
    private $needapproval_common_overdue=3600000; /* 24 h */
    private $needapproval_rush_overdue=3600000; /* 2 h */
    private $needapproval_limit = 30;
    // private $EMAIL_EMPTY='----';
    private $NO_ART = '06_noart';
    private $REDRAWN = '05_notredr';
    private $TO_PROOF = '03_notprof';
    private $NEED_APPROVAL = '02_notapprov';
    private $JUST_APPROVED = '01_notplaced';
    private $NO_VECTOR = '04_notvector';


    function __construct() {
        parent::__construct();
    }

    public function get_count_proofs($options) {
        $this->db->select('count(e.email_id) as cnt');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        if (isset($options['show_deleted']) && $options['show_deleted']==1) {
            $this->db->where('e.email_status != ',$this->order_status);
        } else {
            $this->db->where('e.email_status < ',$this->order_status);
        }

        if (isset($options['search'])) {
            $this->db->like('upper(concat(coalesce(e.email_sender,""), coalesce(e.email_sendermail,""), coalesce(e.email_sendercompany,""),e.proof_num))',  strtoupper($options['search']));
        }
        if (isset($options['assign']) && $options['assign']==1) {
            $this->db->where('lem.email_id is null');
            $this->db->where('e.email_include_lead',1);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            // $this->db->where('e.email_websys',$options['brand']);
            $this->db->where('e.brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_tasks_stage($stage, $taskview, $inclreq, $order_by, $direction, $viewall=0) {
        /* Get with data More then then 24 hours */
        $olddat=$this->get_stagedat($stage, $taskview, $inclreq, $order_by, $direction, 0 , $viewall);
        /* Current day */
        $curdat=$this->get_stagedat($stage, $taskview, $inclreq, $order_by, $direction, 1, $viewall);
        if ($stage=='just_approved' && $order_by=='time' && $direction=='asc') {
            $res=array_merge($curdat,$olddat);
        } else {
            $res=array_merge($olddat, $curdat);
        }
        $out=array();
        $cfgdat=$this->get_taskalert_config();
        if ($cfgdat['noart_alert']==1) {
            $this->noart_common_overdue=($cfgdat['noart_common_days']*24+$cfgdat['noart_common_hours'])*60*60;
            $this->noart_rush_overdue=($cfgdat['noart_rush_days']*24+$cfgdat['noart_rush_hours'])*60*60;
        }
        if ($cfgdat['redraw_alert']==1) {
            $this->redraw_common_overdue=($cfgdat['redraw_common_days']*24+$cfgdat['redraw_common_hours'])*60*60;
            $this->redraw_rush_overdue=($cfgdat['redraw_rush_days']*24+$cfgdat['redraw_rush_hours'])*60*60;
        }
        if ($cfgdat['toproof_alert']==1) {
            $this->toproof_common_overdue=($cfgdat['toproof_common_days']*24+$cfgdat['toproof_common_hours'])*60*60;
            $this->toproof_rush_overdue=($cfgdat['toproof_rush_days']*24+$cfgdat['toproof_rush_hours'])*60*60;
        }
        if ($cfgdat['needapproval_alert']==1) {
            $this->needapproval_common_overdue=($cfgdat['needapproval_common_days']*24+$cfgdat['needapproval_common_hours'])*60*60;
            $this->needapproval_rush_overdue=($cfgdat['needapproval_rush_days']*24+$cfgdat['needapproval_rush_hours'])*60*60;
        }
        $rushimg="<img src='/img/art/task_rushicon.png' alt='Rush'/>";
        foreach ($res as $row) {
            $taskclass='';
            switch ($stage) {
                case 'noart':
                    if ($row['update_date']==0) {
                        $diff='';
                    } else {
                        $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                        if ($row['order_rush_val']==1 && $row['commondiff']>$this->noart_rush_overdue) {
                            $taskclass='taskoverdue';
                        } elseif ($row['order_rush_val']==0 && $row['commondiff']>$this->noart_common_overdue) {
                            $taskclass='taskoverdue';
                        }
                    }
                    break;
                case 'redrawn':
                    if ($row['order_art_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                            if ($row['order_rush_val']==1 && $row['commondiff']>$this->redraw_rush_overdue) {
                                $taskclass='taskoverdue';
                            } elseif($row['order_rush_val']==0 && $row['commondiff']>$this->redraw_common_overdue) {
                                $taskclass='taskoverdue';
                            }
                        }
                    } else {
                        $diff=($row['art_day_diff']==0 ? $row['art_hour_diff'].' h' : $row['art_day_diff'].' d '.($row['art_hour_diff']-($row['art_day_diff']*24)).'h');
                        if ($row['order_rush_val']==1 && $row['specialdiff']>$this->redraw_rush_overdue) {
                            $taskclass='taskoverdue';
                        } elseif($row['order_rush_val']==0 && $row['specialdiff']>$this->redraw_common_overdue) {
                            $taskclass='taskoverdue';
                        }
                    }
                    break;
                case 'need_approve':
                    if ($row['order_proofed_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                            if ($row['order_rush_val']==1 && $row['commondiff']>$this->needapproval_rush_overdue) {
                                $taskclass='taskoverdue';
                            } elseif($row['order_rush_val']==0 && $row['commondiff']>$this->needapproval_common_overdue) {
                                $taskclass='taskoverdue';
                            }
                        }
                    } else {
                        $diff=($row['proofed_day_diff']==0 ? $row['proofed_hour_diff'].' h' : $row['proofed_day_diff'].' d '.($row['proofed_hour_diff']-($row['proofed_day_diff']*24)).'h');
                        if ($row['order_rush_val']==1 && $row['specialdiff']>$this->needapproval_rush_overdue) {
                            $taskclass='taskoverdue';
                        } elseif($row['order_rush_val']==0 && $row['specialdiff']>$this->needapproval_common_overdue) {
                            $taskclass='taskoverdue';
                        }
                    }
                    break;
                case 'need_proof':
                    if ($row['order_vectorized_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                            if ($row['order_rush_val']==1 && $row['commondiff']>$this->toproof_rush_overdue) {
                                $taskclass='taskoverdue';
                            } elseif($row['order_rush_val']==0 && $row['commondiff']>$this->toproof_common_overdue) {
                                $taskclass='taskoverdue';
                            }
                        }
                    } else {
                        $diff=($row['vectorized_day_diff']==0 ? $row['vectorized_hour_diff'].' h' : $row['vectorized_day_diff'].' d '.($row['vectorized_hour_diff']-($row['vectorized_day_diff']*24)).'h');
                        if ($row['order_rush_val']==1 && $row['specialdiff']>$this->toproof_rush_overdue) {
                            $taskclass='taskoverdue';
                        } elseif($row['order_rush_val']==0 && $row['specialdiff']>$this->toproof_common_overdue) {
                            $taskclass='taskoverdue';
                        }
                    }
                    break;
                case 'just_approved':
                    if ($row['order_approved_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                        }
                    } else {
                        $diff=($row['approved_day_diff']==0 ? $row['approved_hour_diff'].' h' : $row['approved_day_diff'].' d '.($row['approved_hour_diff']-($row['approved_day_diff']*24)).'h');
                    }
                    break;
            }
            $row['diff']=$diff;
            $row['bypass_class']='';
            if ($row['redraw_bypass']>0 && ($row['order_proj_status']!=$this->REDRAWN && $row['order_proj_status']!=$this->NO_VECTOR) ) {
                $row['bypass_class']='bypass';
            }
            $row['order_rush']=($row['order_rush_val']==1 ? $rushimg : '&nbsp;');
            $row['order_overclass']=$taskclass;
            $row['order_num']=str_replace('pr','', $row['order_num']);
            if ($row['item_id']==$this->config->item('custom_id')) {
                $row['order_num'].=' <i class="fa fa-diamond" aria-hidden="true"></i>';
            }
            $row['task_title']=($row['customer_name'] ? 'Customer - <b>'.htmlspecialchars($row['customer_name']).'</b>' : '');
            $row['task_title'].=($row['item_name'] ? '<br/>Item - <b>'.  htmlspecialchars($row['item_name']).'</b>' : '');
            $row['task_title'].=($row['vendor_name'] ? '<br/>Vendor - <b>'.htmlspecialchars($row['vendor_name']).($row['vendor_asinumber'] ? '('.$row['vendor_asinumber'].')' : '').'</b>' : '');
            if ($row['lastupdate']) {
                $row['task_title'].='<br/>---------------------------------------------<br/>';
                $row['task_title'].='Last Update: '.$row['lastupdate'];
            }
            $out[]=$row;
        }
        return $out;
    }

    /* Config for Task Alert */
    public function get_taskalert_config() {
        $this->db->select('noart_alert, noart_common_days, noart_common_hours, noart_rush_days, noart_rush_hours,
            redraw_alert, redraw_common_days, redraw_common_hours, redraw_rush_days, redraw_rush_hours,
            toproof_alert, toproof_common_days, toproof_common_hours, toproof_rush_days, toproof_rush_hours,
            needapproval_alert, needapproval_common_days, needapproval_common_hours, needapproval_rush_days, needapproval_rush_hours');
        $this->db->from('ts_configs');
        $cfgdat=$this->db->get()->row_array();
        return $cfgdat;
    }


    private function get_stagedat($stage, $taskview, $inclreq, $order_by, $direction, $less=0, $viewall) {
        $daylimit=24*60*60;
        /* Get with data More then then 24 hours */
        $this->db->select('v.order_disp_id, v.order_num, v.order_rush as order_rush_val, v.specialdiff, v.commondiff, v.update_date, v.order_proj_status ');
        $this->db->select('v.day_diff, v.hour_diff, v.customer_name, v.item_name, v.order_date, v.revenue, artwok_bypassredraw( v.order_id,v.status_type ) as redraw_bypass',FALSE);
        $this->db->select('i.vendor_name, i.vendor_zipcode, i.vendor_asinumber, art_last_history(v.artwork_id) as lastupdate, v.status_type');
        $this->db->select('i.item_id');
        $this->db->from('v_order_statuses v');
        $this->db->join('v_itemsearch i','i.item_number=v.item_number','left');
        switch ($stage) {
            case 'noart':
                $this->db->where('v.order_proj_status', $this->NO_ART);
                break;
            case 'redrawn':
                $this->db->select('if(v.order_proj_status="'.$this->REDRAWN.'",v.order_art_update, v.order_redrawn_update) as order_art_update',FALSE);
                $this->db->select('if(v.order_proj_status="'.$this->REDRAWN.'",v.art_day_diff, v.redrawn_day_diff) as art_day_diff,
                    if(v.order_proj_status="'.$this->REDRAWN.'",v.art_hour_diff, v.redrawn_hour_diff) as art_hour_diff',FALSE);
                /*if(v.order_proj_status="'.Artproof_model::REDRAWN.'" v.art_day_diff, ) v.art_day_diff, v.art_hour_diff');*/
                $this->db->where('(v.order_proj_status in ("'.$this->REDRAWN.'","'.$this->NO_VECTOR.'") or artwok_bypassredraw(v.order_id, v.status_type) > 0 ) ');
                break;
            case 'need_proof':
                $this->db->select('v.order_vectorized_update, v.vectorized_day_diff, v.vectorized_hour_diff');
                $this->db->where('v.order_proj_status', $this->TO_PROOF);
                break;
            case 'need_approve':
                $this->db->select('v.order_proofed_update, v.proofed_hour_diff, v.proofed_day_diff');
                $this->db->where('v.order_proj_status', $this->NEED_APPROVAL);
                if ($inclreq==0) {
                    $this->db->where('v.status_type','O');
                } else {
                    switch ($taskview) {
                        case 'orders':
                            $this->db->where('v.status_type','O');
                            break;
                        case 'proofs':
                            $this->db->where('v.status_type','R');
                            break;
                    }
                }
                break;
            case 'just_approved':
                $this->db->select('v.order_approved_update, v.approved_hour_diff, v.approved_day_diff');
                $this->db->where('v.order_proj_status',  $this->JUST_APPROVED);
                $this->db->where('v.order_approved_view',0);
                break;
            default:
                break;
        }
        // More then 1 day
        if ($less==0) {
            $this->db->where('v.specialdiff > ',$daylimit);
            if ($stage=='just_approved' && $viewall==0) {
                $this->db->limit($this->needapproval_limit);
            }
        } else {
            $this->db->where('v.specialdiff <= ',$daylimit);
        }

        if ($stage!='need_approve') {
            switch ($taskview) {
                case 'orders':
                    $this->db->where('v.status_type','O');
                    break;
                case 'proofs':
                    $this->db->where('v.status_type','R');
                    break;
            }
        }

        if ($order_by=='time') {
            if ($direction=='desc') {
                $this->db->order_by('v.status_type asc, v.order_rush desc,  v.specialdiff desc, v.commondiff desc');
            } else {
                $this->db->order_by('v.status_type asc, v.order_rush desc, v.specialdiff asc, v.commondiff asc');
            }
        } elseif ($order_by=='order') {
            if ($direction=='desc') {
                $this->db->order_by('v.status_type asc, v.order_rush desc,  v.order_num desc');
            } else {
                $this->db->order_by('v.status_type asc, v.order_rush desc, v.order_num asc');
            }
        }
        $res=$this->db->get()->result_array();

        return $res;
    }


    public function get_artproofs($search,$order_by,$direct,$limit,$offset,$maxval) {
        $this->db->select('e.*,l.lead_number, l.lead_id,vo.order_proj_status,artwork_alert(e.email_id, "email") as vect_alert,
            artwok_bypassredraw(e.email_id,"R") as redraw_bypass',FALSE);
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->join('ts_leads l','l.lead_id=lem.lead_id','left');
        $this->db->join('v_order_statuses vo','vo.order_id=e.email_id and vo.status_type="R"','left');
        $this->db->where('e.email_type', $this->EMAIL_TYPE);
        if (isset($search['search'])) {
            $this->db->like('upper(concat(coalesce(e.email_sender,""), coalesce(e.email_sendermail,""), coalesce(e.email_sendercompany,""),e.proof_num)) ',  strtoupper($search['search']));
        }
        if (isset($search['assign'])) {
            $this->db->where('lem.email_id is null');
            $this->db->where('e.email_include_lead',1);
        }
        if (isset($search['brand']) && $search['brand']!=='ALL') {
            // $this->db->where('e.email_websys',$search['brand']);
            $this->db->where('e.brand', $search['brand']);
        }
        if (isset($search['show_deleted'])) {
            $this->db->where('e.email_status != ',3);
        } else {
            $this->db->where('e.email_status < ',3);
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
        $incl_icon='<img src="/img/art/noninclide_lead_icon.png" alt="Include"/>';
        $nonincl_icon='<img src="/img/art/inclide_lead_icon.png" alt="Non Include"/>';
        $curimg=$prvimg='<img src="/img/art/artarrow.png" alt="Previous" class="prvartstageicon"/>';
        $parsericon='<img src="/img/art/parsed.png" alt="Parser"/>';
        $emptynote='<img src="/img/art/empty_square.png" alt="Notes"/>';
        $fullnote='<img src="/img/art/lightblue_square.png" alt="Notes" />';
        $revert_icon="<img src='/img/art/refund.png' alt='Revert' title='Revert' data-type='revert'/>";
        $delete_icon="<img src='/img/art/cancel_order.png' alt='Cancel' title='Cancel' data-type='delete'/>";

        foreach ($res as $row) {
            // $lastmsg=$this->get_lastupdate($row['email_id'],'artproofs');
            // $artlastupdat=($lastmsg=='' ? '' : 'title="'.$lastmsg.'"');
            $artlastupdat="prooflastmessageview";
            // $row['lastmsg']='/artproofs/proof_lastmessage?d='.$row['email_id'];
            $row['ordnum']=$ordnum;
            $row['email']=($row['email_sendermail']=='' ? '' : '<img src="/img/icons/email.png" alt="Email" title="'.$row['email_sendermail'].'" style="margin-right:3px;"/>');
            $row['emailparsed']=($row['email_webpage']=='EMAILPARSER' ? $parsericon : '&nbsp;');
            $row['emailparsed_title']=($row['email_webpage']=='EMAILPARSER' ? 'data-content="'.$row['email_sendermail'].' - '.date('m/d/y H:i:s',  strtotime($row['email_date'])).'"' : '');
            $row['email_date']=date('m/d/y',strtotime($row['email_date']));
            $row['art_class']=$row['redrawn_class']=$row['vectorized_class']=$row['proofed_class']=$row['approved_class']='';
            $row['approved_cell']=$row['proofed_cell']=$row['vectorized_cell']=$row['redrawn_cell']=$row['art_cell']='&nbsp';
            $row['art_title']=$row['redrawn_title']=$row['vectorized_title']=$row['proofed_title']=$row['approved_title']='';
            switch ($row['order_proj_status']) {
                case $this->NO_ART:
                    // $curstage='art_stage';
                    break;
                case $this->REDRAWN:
                    $row['art_class']='chk-ordoption';
                    $row['art_cell']=$curimg;
                    $row['art_title']=$artlastupdat;
                    break;
                case $this->NO_VECTOR:
                    $row['art_class']='chk-ordoption';
                    if ($row['vect_alert']==0) {
                        $row['redrawn_class']='chk-ordoption';
                    } else {
                        $row['redrawn_class']='chk-ordoption-alert';
                    }
                    $row['art_cell']=$prvimg;
                    $row['redrawn_cell']=$curimg;
                    $row['redrawn_title']=$artlastupdat;
                    break;
                case $this->TO_PROOF:
                    $row['art_class']='chk-ordoption';
                    if ($row['redraw_bypass']==0) {
                        $row['redrawn_class']='chk-ordoption';
                        $row['redrawn_cell']=$prvimg;
                    }
                    $row['vectorized_class']='chk-ordoption';
                    $row['art_cell']=$prvimg;
                    $row['vectorized_cell']=$curimg;
                    $row['vectorized_title']=$artlastupdat;
                    break;
                case $this->NEED_APPROVAL:
                    $row['art_class']='chk-ordoption';
                    $row['vectorized_class']='chk-ordoption';
                    $row['proofed_class']='chk-ordoption';
                    $row['art_cell']=$prvimg;
                    $row['vectorized_cell']=$prvimg;
                    $row['proofed_cell']=$curimg;
                    $row['proofed_title']=$artlastupdat;
                    if ($row['redraw_bypass']==0) {
                        $row['redrawn_class']='chk-ordoption';
                        $row['redrawn_cell']=$prvimg;
                    }
                    break;
                case $this->JUST_APPROVED:
                    $curstage='approved_stage';
                    $row['art_class']='chk-ordoption';
                    $row['vectorized_class']='chk-ordoption';
                    $row['proofed_class']='chk-ordoption';
                    $row['approved_class']='chk-ordoption';
                    $row['art_cell']=$prvimg;
                    $row['vectorized_cell']=$prvimg;
                    $row['proofed_cell']=$prvimg;
                    $row['approved_cell']=$curimg;
                    $row['approved_title']=$artlastupdat;
                    if ($row['redraw_bypass']==0) {
                        $row['redrawn_class']='chk-ordoption';
                        $row['redrawn_cell']=$prvimg;
                    }
                    break;
                default :
                    $curstage='approved_stage';
                    $row['art_class']='chk-ordoption';
                    $row['redrawn_class']='chk-ordoption';
                    $row['vectorized_class']='chk-ordoption';
                    $row['proofed_class']='chk-ordoption';
                    $row['approved_class']='chk-ordoption';
                    $row['art_cell']=$prvimg;
                    $row['redrawn_cell']=$prvimg;
                    $row['vectorized_cell']=$prvimg;
                    $row['proofed_cell']=$prvimg;
                    $row['approved_cell']=$curimg;
                    $row['approved_title']=$artlastupdat;
                    break;
            }
            $row['assigned']=($row['lead_id']=='' ? 'leadassign' : '');
            $row['email_sender']=($row['email_sender']=='' ? '&nbsp;' : $row['email_sender']);
            $row['rowclass']=($row['lead_id']=='' ? '' : 'leadentered');
            $row['lead_number']=($row['lead_number']=='' ? '' : 'L'.$row['lead_number']);
            $row['leadid']=($row['lead_id']=='' ? 0 : $row['lead_id']);

            if ($row['email_questions']=='') {
                $row['proof_note']=$emptynote;
                $row['note_title']='';
            } else {
                $row['proof_note']=$fullnote;
                $row['note_title']=$row['email_questions'];
            }
            if ($row['email_status']==$this->void_status) {
                $row['action_icon']=$revert_icon;
            } else {
                $row['action_icon']=$delete_icon;
            }
            if ($row['email_include_lead']==1) {
                $row['inclicon']=$incl_icon;
            } else {
                $row['inclicon']=$nonincl_icon;
            }

            $out[]=$row;
            $ordnum--;
        }
        return $out;
    }

    public function get_proof_data($email_id) {
        $ci=&get_instance();
        $this->db->select('e.*,lem.leademail_id, l.lead_id, l.lead_number, l.lead_date, l.lead_customer, l.lead_mail');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->join('ts_leads l','l.lead_id=lem.lead_id','left');
        $this->db->where('e.email_id',$email_id);
        $res=$this->db->get()->row_array();
        $res['email_color']='';
        $res['email_logo']='';
        if (isset($res['email_id'])) {
            $res['usercomment']=$res['email_text'];
            $res['email_date']=date('m/d/Y',strtotime($res['email_date']));
            $res['lead_date']=(intval($res['lead_date'])==0 ? '' : date('m/d/y',$res['lead_date']));
            /* Proof Details */
            $res['email_text']=get_json_param($res['email_other_info'],'usertext','');
            $usr_color1=get_json_param($res['email_other_info'],'user_color1','');
            $usr_color2=get_json_param($res['email_other_info'],'user_color2','');
            $res['email_color']=$usr_color1.($usr_color2=='' ? '' : ','.$usr_color2);
            $res['item_colors']=get_json_param($res['email_other_info'],'itemcolors','');
            $logo_lnk=get_json_param($res['email_other_info'],'userlogo','');
            if ($logo_lnk!='') {
                if ($res['email_websys']=='BT') {
                    $logo_lnk=$this->bt_main.$logo_lnk;
                } elseif ($res['email_websys']=='SB') {
                    $logo_lnk=$this->sb_main.$logo_lnk;
                }
                $res['email_logo']="<a href='".$logo_lnk."' target='_blank'>User Logo</a>";
            }
            $res['email_font']=get_json_param($res['email_other_info'],'user_font','');
        }
        return $res;
    }

    function delete_proof($proof_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        $this->db->set('email_status',  $this->void_status);
        $this->db->where('email_id',$proof_id);
        $this->db->update('ts_emails');
        $out['msg']='Unknown proof request';
        if ($this->db->affected_rows()==1) {
            $out['result']= $this->success_result;
            $out['msg']='';
        }
        return $out;
    }

    function revert_proof($proof_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        $this->db->set('email_status',  $this->active_status);
        $this->db->where('email_id',$proof_id);
        $this->db->update('ts_emails');
        $out['msg']='Unknown proof request';
        if ($this->db->affected_rows()==1) {
            $out['result']=  $this->success_result;
            $out['msg']='';
        }
        return $out;
    }

    public function get_lastupdate($order_id,$mode='artproofs') {
        $this->db->select('ah.message');
        $this->db->from('ts_artwork_history ah');
        $this->db->join('ts_artworks a','a.artwork_id=ah.artwork_id');
        if ($mode=='order') {
            $this->db->where('a.order_id',$order_id);
        } else {
            $this->db->where('a.mail_id',$order_id);
        }
        $this->db->order_by('ah.artwork_history_id','desc');
        $this->db->limit(1);
        $res=$this->db->get()->row_array();
        if (isset($res['message'])) {
            return $res['message'];
        } else {
            return 'No User Messages';
        }
    }

    function update_proof_include($email_id, $newval) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);

        $incl_icon='<img src="/img/art/noninclide_lead_icon.png" alt="Include"/>';
        $nonincl_icon='<img src="/img/art/inclide_lead_icon.png" alt="Non Include"/>';

        $this->db->set('email_include_lead',  $newval);
        $this->db->where('email_id',$email_id);
        $this->db->update('ts_emails');
        if ($this->db->affected_rows()==1) {
            $out['result']=  $this->success_result;
            $out['newicon']=($newval==1 ? $incl_icon : $nonincl_icon );
            /* Get Today total */
            $today=$this->get_todays();
            if (floatval($today)==0) {
                $out['newclass']='empval';
            } else {
                $out['newclass']='curmail';
            }
            /* Get Totals */
            $msgopt=array(
                'assign'=>1,
            );
            $non_assign=$this->get_count_proofs($msgopt);
        } else {
            $out['msg']='Error during change data';
        }
        return $out;
    }

    function get_todays() {
        // $todaybgn=strtotime($this->startdate);
        $this->db->select('count(e.email_id) as cnt');
        $this->db->from('ts_emails e');
        $this->db->join('ts_lead_emails lem','lem.email_id=e.email_id','left');
        $this->db->where('email_type', $this->EMAIL_TYPE);
        $this->db->where('lem.email_id is null');
        $this->db->where('e.email_include_lead',1);
        $this->db->where('e.email_status != ',$this->void_status);
        // $this->db->where('unix_timestamp(email_date) >=',$todaybgn);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            $retval='';
        } else {
            $retval=$res['cnt'];
        }
        return $retval;
    }


}