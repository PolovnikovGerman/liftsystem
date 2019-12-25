<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Artproof_model extends MY_Model
{
    private $EMAIL_TYPE='Art_Submit';
    private $order_status=3;
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
        if (isset($options['brand'])) {
            $this->db->where('e.email_websys',$options['brand']);
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

}