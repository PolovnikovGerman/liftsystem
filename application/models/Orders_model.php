<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Orders_model extends MY_Model
{
    protected $START_ORDNUM=22000;
    protected $INIT_ERRMSG='Unknown error. Try later';
    protected $project_name='PROJ';

    protected $NO_ART = '06_noart';
    protected $REDRAWN = '05_notredr';
    protected $TO_PROOF = '03_notprof';
    protected $NEED_APPROVAL = '02_notapprov';
    protected $JUST_APPROVED = '01_notplaced';
    protected $NO_VECTOR = '04_notvector';

    protected $_notplaced='To Place PO';
    protected $_notapprov = 'Need Approval (Waiting on Customer)';
    protected $_noart = 'Need Art (Waiting on Customer)';
    protected $_notprof = 'To Proof (Need to Make Proof)';
    protected $_notredr = 'To Check if Vector (Check if need to send Ravi)';
    protected $_notvector = 'Need Redraw (Waiting on Ravi)';

    protected $_notplaced_short='To Place PO';
    protected $_notapprov_short = 'Need Approval';
    protected $_noart_short = 'Need Art';
    protected $_notprof_short = 'To Proof';
    protected $_notredr_short = 'To Check if Vector';
    protected $_notvector_short = 'Need Redraw';

    /* Start date for check email 03/28/2013 */
    protected $req_email_date = 1364421600;

    protected $init_error_msg='Unknown error. Try later';
    private $multicolor='Multiple';

    function __construct() {
        parent::__construct();
    }

    public function get_count_orders($filtr=array()) {
        $this->db->select('count(o.order_id) as cnt',FALSE);
        if (isset($filtr['artfilter'])) {
            $this->db->join('v_order_statuses vo','vo.order_id=o.order_id and vo.status_type="O"','left');
        }
        if (isset($filtr['order_usr_repic'])) {
            $this->db->where('o.order_usr_repic', $filtr['order_usr_repic']);
        }
        if (isset($filtr['unassigned'])) {
            $this->db->where('o.order_usr_repic is null');
        }
        if (isset($filtr['weborder'])) {
            $this->db->where('o.weborder', $filtr['weborder']);
        }
        $this->db->from('ts_orders o');
        if (isset($filtr['filter']) && $filtr['filter']==7) {
            $this->db->where('o.is_canceled',1);
        } else {
            if (isset($filtr['admin_mode']) && $filtr['admin_mode']==0) {
                $this->db->where('o.is_canceled',0);
            }
        }

        /*  */
        if (count($filtr)>0) {
            if (isset($filtr['hideart']) && $filtr['hideart']==1) {
                $this->db->where('o.order_arthide',0);
            }
            if (isset($filtr['artproj']) && $filtr['artproj']==1) {
                $this->db->where('o.profit_perc is NULL');
            }
            if (isset($filtr['search']) && $filtr['search']) {
                // $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ',o.revenue) ",strtoupper($filtr['search']));
                $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(o.order_items),ucase(o.order_itemnumber), o.revenue ) ",strtoupper($filtr['search']));
            }
            if (isset($filtr['filter']) && $filtr['filter']==1) {
                $this->db->where('o.order_cog is null');
            }
            if (isset($filtr['filter']) && $filtr['filter']==2) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=40');
            }
            if (isset($filtr['filter']) && $filtr['filter']==3) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=30 and round(o.profit_perc,0)<40');
            }
            if (isset($filtr['filter']) && $filtr['filter']==4) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=20 and round(o.profit_perc,0)<30');
            }
            if (isset($filtr['filter']) && $filtr['filter']==5) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=10 and round(o.profit_perc,0)<20');
            }
            if (isset($filtr['filter']) && $filtr['filter']==6) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)<=0');
            }
            if (isset($filtr['filter']) && $filtr['filter']==8) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>0 and round(o.profit_perc,0)<10');
            }
            if (isset($filtr['date_bgn'])) {
                $this->db->where('o.order_date >= ', $filtr['date_bgn']);
            }
            if (isset($filtr['date_end'])) {
                $this->db->where('o.order_date < ', $filtr['date_end']);
            }
        }
        if (isset($filtr['artfilter'])) {
            switch ($filtr['artfilter']) {
                case 1:
                    $this->db->where('vo.order_proj_status',$this->NEED_APPROVAL);
                    break;
                case 2:
                    $this->db->where('vo.order_proj_status',$this->TO_PROOF);
                    break;
                case 3:
                    $this->db->where('vo.order_proj_status',$this->NO_VECTOR);
                    break;
                case 4:
                    $this->db->where('vo.order_proj_status',$this->REDRAWN);
                    break;
                case 5:
                    $this->db->where('vo.order_proj_status',$this->NO_ART);
                    break;
                default:
                    break;
            }
        }
        if (isset($filtr['artadd_filtr']) && $filtr['artadd_filtr']==1) {
            $this->db->where('o.order_rush',1);
        }
        if (isset($filtr['order_qty'])) {
            $this->db->where('o.order_qty',$filtr['order_qty']);
        }
        if (isset($filtr['shipping_country'])) {
            $shipsql = "select distinct(order_id) as order_id from ts_order_shipaddres ";
            if (isset($filtr['shipping_state'])) {
                $shipsql.= "where state_id=".$filtr['shipping_state'];
            } else {
                if (intval($filtr['shipping_country'])>0) {
                    $shipsql.=" where country_id=".$filtr['shipping_country'];
                } else {
                    $shipsql.=" where country_id not in (223, 39)";
                }
            }
            $this->db->join("({$shipsql}) as s",'s.order_id=o.order_id');
        }
        if (isset($filtr['order_type'])) {
            $artsql ="select DISTINCT art.order_id from ts_artwork_arts a join ts_artworks art on art.artwork_id=a.artwork_id where order_id is not null ";
            if ($filtr['order_type']=='new') {
                $artsql.=" and a.art_type!='Repeat'";
            } else {
                $artsql.=" and a.art_type='Repeat'";
            }
            $this->db->join("({$artsql}) as art",'art.order_id=o.order_id');
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_orders($filtr,$order_by,$direct,$limit,$offset) {
        $this->db->select('o.order_id, o.order_rush, o.order_blank, o.order_date, o.brand_id, o.order_num, o.customer_name, o.customer_email, o.order_items, o.revenue, o.shipping, o.tax,
            o.cc_fee, o.order_art, o.order_redrawn, o.order_vectorized, o.order_proofed, o.order_approved, artwork_alert(o.order_id, "order") as vect_alert, o.order_code, o.art_note, b.brand_name,
            orders_cntattachment(o.order_id) as doccnt, vo.order_proj_status, artwok_bypassredraw(o.order_id, "O") as redraw_bypass',FALSE);
        $this->db->from('ts_orders o');
        $this->db->join('brands b','b.brand_id=o.brand_id','left');
        $this->db->join('v_order_artstage vo','vo.order_id=o.order_id','left');
        $this->db->where('o.is_canceled',0);
        if (count($filtr)>0) {
            if (isset($filtr['search']) && $filtr['search']) {
                $this->db->like("concat(ucase(o.customer_name),' ',o.order_num,' ',o.revenue,' ',ucase(o.customer_email)) ",strtoupper($filtr['search']));
            }
            if (isset($filtr['hideart']) && $filtr['hideart']==1) {
                $this->db->where('o.order_arthide',0);
            }
            if (isset($filtr['artfiltr'])) {
                switch ($filtr['artfiltr']) {
                    case 1:
                        $this->db->where('vo.order_proj_status',$this->NEED_APPROVAL);
                        break;
                    case 2:
                        $this->db->where('vo.order_proj_status',$this->TO_PROOF);
                        break;
                    case 3:
                        $this->db->where('vo.order_proj_status',$this->NO_VECTOR);
                        break;
                    case 4:
                        $where="vo.order_proj_status = '".$this->REDRAWN."' or artwok_bypassredraw(o.order_id, 'O') > 0 ";
                        $this->db->where($where);
                        break;
                    case 5:
                        $this->db->where('vo.order_proj_status',$this->NO_ART);
                        break;
                    default:
                        break;
                }
            }
            if (isset($filtr['artadd_filtr']) && $filtr['artadd_filtr']==1) {
                $this->db->where('o.order_rush',1);
            }
        }
        $this->db->limit($limit,$offset);
        $this->db->order_by($order_by,$direct);
        $res=$this->db->get()->result_array();

        $out=array();
        // $curimg='<img src="/img/artstatus_icon.png" alt="Current" class="curartstageicon"/>';
        $curimg=$prvimg='<img src="/img/art/artarrow.png" alt="Previous" class="prvartstageicon"/>';
        foreach ($res as $row) {
            $artclass=($row['order_blank']==1 ? 'chk-ordoptionblank' : 'chk-ordoption');
            $row['order_date']=($row['order_date']==0 ? '' : date('m/d/y',$row['order_date']));
            $row['revenue']=(floatval($row['revenue'])==0 ? '-' : '$'.number_format($row['revenue'],2,'.',','));
            $row['shipping']=(floatval($row['shipping'])==0 ? '-' : '$'.number_format($row['shipping'], 2, '.', ','));
            $row['tax']=(floatval($row['tax'])==0 ? '-' : '$'.number_format($row['tax'], 2, '.', ',') );
            $row['title_ccfee']='$'.number_format($row['cc_fee'], 2, '.', ',');
            // $lastmsg=$this->get_lastupdate($row['order_id']);
            // $artlastupdat=($lastmsg=='' ? '' : 'title="'.$lastmsg.'"');
            $artlastupdat="artlastmessageview";
            $row['lastmsg']='/artorders/order_lastmessage?d='.$row['order_id'];
            $row['email']=($row['customer_email']=='' ? '&nbsp;' : '<img src="/img/icons/email.png" alt="Email" title="'.$row['customer_email'].'" />');
            $row['out_code']=($row['order_code']=='' ? '&nbsp;' : $row['order_code']);
            $row['rush_class']=($row['order_rush']==0 ? '' : 'rushorder');
            if ($row['order_blank']==1) {
                $row['art_class']=$row['redrawn_class']=$row['vectorized_class']=$row['proofed_class']=$row['approved_class']=$artclass;
                $row['art_cell']=$row['redrawn_cell']=$row['vectorized_cell']=$row['proofed_cell']=$row['approved_cell']=$curimg;
                $row['art_title']=$row['redrawn_title']=$row['vectorized_title']=$row['proofed_title']='';
                $row['approved_title']=$artlastupdat;
            } else {
                // $curstage='art_stage';
                $row['art_class']=$row['redrawn_class']=$row['vectorized_class']=$row['proofed_class']=$row['approved_class']='';
                $row['art_cell']=$row['redrawn_cell']=$row['vectorized_cell']=$row['proofed_cell']=$row['approved_cell']='&nbsp;';
                $row['art_title']=$row['redrawn_title']=$row['vectorized_title']=$row['proofed_title']=$row['approved_title']='';
                /* */
                switch ($row['order_proj_status']) {
                    case $this->NO_ART:
                        break;
                    case $this->REDRAWN:
                        $row['art_class']=$artclass;
                        $row['art_cell']=$curimg;
                        $row['art_title']=$artlastupdat;
                        break;
                    case $this->NO_VECTOR:
                        $row['art_class']=$artclass;
                        if ($row['vect_alert']==0) {
                            $row['redrawn_class']=$artclass;
                        } else {
                            $row['redrawn_class']='chk-ordoption-alert';
                        }
                        $row['art_cell']=$prvimg;
                        $row['redrawn_cell']=$curimg;
                        $row['redrawn_title']=$artlastupdat;
                        break;
                    case $this->TO_PROOF:
                        $row['art_class']=$artclass;
                        if ($row['redraw_bypass']==0) {
                            $row['redrawn_class']=$artclass;
                            $row['redrawn_cell']=$prvimg;
                        }
                        $row['vectorized_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['vectorized_cell']=$curimg;
                        $row['vectorized_title']=$artlastupdat;
                        break;
                    case $this->JUST_APPROVED:
                        $row['art_class']=$artclass;
                        if ($row['redraw_bypass']==0) {
                            $row['redrawn_class']=$artclass;
                            $row['redrawn_cell']=$prvimg;
                        }
                        $row['vectorized_class']=$artclass;
                        $row['proofed_class']=$artclass;
                        $row['approved_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['vectorized_cell']=$prvimg;
                        $row['proofed_cell']=$prvimg;
                        $row['approved_cell']=$curimg;
                        $row['approved_title']=$artlastupdat;
                        break;
                    case $this->NEED_APPROVAL:
                        $row['art_class']=$artclass;
                        if ($row['redraw_bypass']==0) {
                            $row['redrawn_class']=$artclass;
                            $row['redrawn_cell']=$prvimg;
                        }

                        $row['vectorized_class']=$artclass;
                        $row['proofed_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['vectorized_cell']=$prvimg;
                        $row['proofed_cell']=$curimg;
                        $row['proofed_title']=$artlastupdat;
                        break;
                    default :
                        $row['art_class']=$artclass;
                        $row['redrawn_class']=$artclass;
                        $row['vectorized_class']=$artclass;
                        $row['proofed_class']=$artclass;
                        $row['approved_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['redrawn_cell']=$prvimg;
                        $row['vectorized_cell']=$prvimg;
                        $row['proofed_cell']=$prvimg;
                        $row['approved_cell']=$curimg;
                        $row['approved_title']=$artlastupdat;
                        break;
                }

            }
            $out[]=$row;
        }
        return $out;
    }

    public function get_general_orders($filtr,$order_by,$direct,$limit,$offset, $usr_id) {
        $this->load->model('user_model');
        // $usrdata=$this->user_model->get_user_data($usr_id);
        $this->db->select('o.order_id, o.order_rush, o.order_blank, o.order_date, o.brand_id, o.order_num, o.customer_name, o.customer_email, o.order_items, o.revenue, o.shipping, o.tax,
            o.cc_fee, o.order_art, o.order_redrawn, o.order_vectorized, o.order_proofed, o.order_approved, artwork_alert(o.order_id, "order") as vect_alert, o.order_code, o.art_note,
            orders_cntattachment(o.order_id) as doccnt, vo.order_proj_status, artwok_bypassredraw(o.order_id, "O") as redraw_bypass',FALSE);
        $this->db->select('o.order_confirmation, o.order_usr_repic, o.weborder, u.user_leadname, u.user_name');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_artstage vo','vo.order_id=o.order_id','left');
        $this->db->join('users u','u.user_id=o.order_usr_repic','left');
        $this->db->where('o.is_canceled',0);
        if (count($filtr)>0) {
            if (isset($filtr['search']) && $filtr['search']) {
                $this->db->like("concat(ucase(o.customer_name),' ',o.order_num,' ',o.revenue,' ',ucase(o.customer_email)) ",strtoupper($filtr['search']));
            }
            if (isset($filtr['hideart']) && $filtr['hideart']==1) {
                $this->db->where('o.order_arthide',0);
            }
            if (isset($filtr['artfiltr'])) {
                switch ($filtr['artfiltr']) {
                    case 1:
                        $this->db->where('vo.order_proj_status',$this->NEED_APPROVAL);
                        break;
                    case 2:
                        $this->db->where('vo.order_proj_status',$this->TO_PROOF);
                        break;
                    case 3:
                        $this->db->where('vo.order_proj_status',$this->NO_VECTOR);
                        break;
                    case 4:
                        $where="vo.order_proj_status = '".$this->REDRAWN."' or artwok_bypassredraw(o.order_id, 'O') > 0 ";
                        $this->db->where($where);
                        break;
                    case 5:
                        $this->db->where('vo.order_proj_status',$this->NO_ART);
                        break;
                    default:
                        break;
                }
            }
            if (isset($filtr['artadd_filtr']) && $filtr['artadd_filtr']==1) {
                $this->db->where('o.order_rush',1);
            }
        }
        $this->db->limit($limit,$offset);
        $this->db->order_by($order_by,$direct);
        $res=$this->db->get()->result_array();

        $out=array();
        $curimg=$prvimg='<img src="/img/art/artarrow.png" alt="Previous" class="prvartstageicon"/>';
        foreach ($res as $row) {
            $artclass=($row['order_blank']==1 ? 'chk-ordoptionblank' : 'chk-ordoption');
            $row['order_date']=($row['order_date']==0 ? '' : date('m/d/y',$row['order_date']));
            $row['revenue']=(floatval($row['revenue'])==0 ? '-' : '$'.number_format($row['revenue'],2,'.',','));
            $row['shipping']=(floatval($row['shipping'])==0 ? '-' : '$'.number_format($row['shipping'], 2, '.', ','));
            $row['tax']=(floatval($row['tax'])==0 ? '-' : '$'.number_format($row['tax'], 2, '.', ',') );
            $row['title_ccfee']='$'.number_format($row['cc_fee'], 2, '.', ',');
            // $lastmsg=$this->get_lastupdate($row['order_id']);
            // $artlastupdat=($lastmsg=='' ? '' : 'title="'.$lastmsg.'"');
            $artlastupdat="artlastmessageview";
            $row['lastmsg']='/artorders/order_lastmessage?d='.$row['order_id'];
            $row['email']=($row['customer_email']=='' ? '&nbsp;' : '<img src="/img/icons/email.png" alt="Email" title="'.$row['customer_email'].'" />');
            $row['out_code']=($row['order_code']=='' ? '&nbsp;' : $row['order_code']);
            $row['rush_class']=($row['order_rush']==0 ? '' : 'rushorder');
            if ($row['order_blank']==1) {
                $row['art_class']=$row['redrawn_class']=$row['vectorized_class']=$row['proofed_class']=$row['approved_class']=$artclass;
                $row['art_cell']=$row['redrawn_cell']=$row['vectorized_cell']=$row['proofed_cell']=$row['approved_cell']=$curimg;
                $row['art_title']=$row['redrawn_title']=$row['vectorized_title']=$row['proofed_title']='';
                $row['approved_title']=$artlastupdat;
            } else {
                // $curstage='art_stage';
                $row['art_class']=$row['redrawn_class']=$row['vectorized_class']=$row['proofed_class']=$row['approved_class']='';
                $row['art_cell']=$row['redrawn_cell']=$row['vectorized_cell']=$row['proofed_cell']=$row['approved_cell']='&nbsp;';
                $row['art_title']=$row['redrawn_title']=$row['vectorized_title']=$row['proofed_title']=$row['approved_title']='';
                /* */
                switch ($row['order_proj_status']) {
                    case $this->NO_ART:
                        break;
                    case $this->REDRAWN:
                        $row['art_class']=$artclass;
                        $row['art_cell']=$curimg;
                        $row['art_title']=$artlastupdat;
                        break;
                    case $this->NO_VECTOR:
                        $row['art_class']=$artclass;
                        if ($row['vect_alert']==0) {
                            $row['redrawn_class']=$artclass;
                        } else {
                            $row['redrawn_class']='chk-ordoption-alert';
                        }
                        $row['art_cell']=$prvimg;
                        $row['redrawn_cell']=$curimg;
                        $row['redrawn_title']=$artlastupdat;
                        break;
                    case $this->TO_PROOF:
                        $row['art_class']=$artclass;
                        if ($row['redraw_bypass']==0) {
                            $row['redrawn_class']=$artclass;
                            $row['redrawn_cell']=$prvimg;
                        }
                        $row['vectorized_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['vectorized_cell']=$curimg;
                        $row['vectorized_title']=$artlastupdat;
                        break;
                    case $this->JUST_APPROVED:
                        $row['art_class']=$artclass;
                        if ($row['redraw_bypass']==0) {
                            $row['redrawn_class']=$artclass;
                            $row['redrawn_cell']=$prvimg;
                        }
                        $row['vectorized_class']=$artclass;
                        $row['proofed_class']=$artclass;
                        $row['approved_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['vectorized_cell']=$prvimg;
                        $row['proofed_cell']=$prvimg;
                        $row['approved_cell']=$curimg;
                        $row['approved_title']=$artlastupdat;
                        break;
                    case $this->NEED_APPROVAL:
                        $row['art_class']=$artclass;
                        if ($row['redraw_bypass']==0) {
                            $row['redrawn_class']=$artclass;
                            $row['redrawn_cell']=$prvimg;
                        }

                        $row['vectorized_class']=$artclass;
                        $row['proofed_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['vectorized_cell']=$prvimg;
                        $row['proofed_cell']=$curimg;
                        $row['proofed_title']=$artlastupdat;
                        break;
                    default :
                        $row['art_class']=$artclass;
                        $row['redrawn_class']=$artclass;
                        $row['vectorized_class']=$artclass;
                        $row['proofed_class']=$artclass;
                        $row['approved_class']=$artclass;
                        $row['art_cell']=$prvimg;
                        $row['redrawn_cell']=$prvimg;
                        $row['vectorized_cell']=$prvimg;
                        $row['proofed_cell']=$prvimg;
                        $row['approved_cell']=$curimg;
                        $row['approved_title']=$artlastupdat;
                        break;
                }
            }
            $row['usrreplclass']='user';
            if ($row['order_usr_repic']>0) {
                $row['user_replic']=($row['user_leadname']=='' ? $row['user_name'] : $row['user_leadname']);
            } else {
                if ($row['weborder']==1) {
                    $row['user_replic']='Website';
                    $row['usrreplclass']='website';
                }
            }
            $out[]=$row;
        }

        return $out;
    }

    public function get_order_detail($order_id) {
        $this->db->select('o.*, br.brand_name, itm.item_name as item_name');
        $this->db->from('ts_orders o');
        $this->db->join('brands br','br.brand_id=o.brand_id','left');
        $this->db->join('v_itemsearch itm','itm.item_id=o.item_id','left');
        $this->db->where('o.order_id',$order_id);
        $res=$this->db->get()->row_array();

        if ($res['order_cog']=='') {
            $res['profit_class']='projprof';
        } else {
            $res['profit_class']=profitClass($res['profit_perc']);
        }
        return $res;
    }

    public function get_lastupdate($order_id,$mode='order') {
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

    public function change_goal_value($data, $field, $newval) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_ERRMSG);
        if (!isset($data[$field])) {
            $out['msg']='Unknown field '.$field;
            return $out;
        }
        $data[$field]=$newval;
        usersession('goaldata', $data);
        $out['result']=  $this->success_result;
        // Count new params
        $goal_avgprofit=$goal_avgrevenue=$goal_avgprofit_perc='&nbsp;';
        if ($data['goal_orders']!=0) {
            $goal_avgrevenue=($data['goal_revenue']/$data['goal_orders']);
            $goal_avgrevenue=MoneyOutput($goal_avgrevenue);
            $goal_avgprofit=($data['goal_profit']/$data['goal_orders']);
            $goal_avgprofit=MoneyOutput($goal_avgprofit);
        }
        if ($data['goal_revenue']) {
            $goal_avgprofit_perc=($data['goal_profit']/$data['goal_revenue']*100);
            $goal_avgprofit_perc=number_format($goal_avgprofit_perc,1).'%';
        }
        $out['goalavgrevenue']=$goal_avgrevenue;
        $out['goalavgprofit']=$goal_avgprofit;
        $out['goalavgprofitperc']=$goal_avgprofit_perc;
        return $out;
    }

    public function save_profitdate_goal($data) {
        $out = array('result' => $this->error_result, 'msg' => $this->INIT_ERRMSG);
        $this->db->where('goal_order_id', $data['goal_order_id']);
        $this->db->set('goal_orders', $data['goal_orders']);
        $this->db->set('goal_revenue', $data['goal_revenue']);
        $this->db->set('goal_profit', $data['goal_profit']);
        $this->db->update('ts_goal_orders');
        $out['result']=$this->success_result;
        usersession('goaldata', NULL);
        return $out;
    }




    public function get_checkouts_by_weekday($brand) {
        $out_array = [];
        /* Add Empty values */
        $out_array['mon'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['tue'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['wed'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['thu'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['fri'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['sat'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['sun'] = ['cnt' => 0, 'sum' => 0.00];
        $out_array['total'] = ['cnt' => 0, 'sum' => 0.00];
        $this->db->select('date_format(order_date,\'%w\') as dayweek,count(order_id) as cnt_ord, sum(order_total) as sum_order',FALSE);
        $this->db->from('sb_orders');
        $this->db->where('is_void',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('dayweek');
        $res = $this->db->get()->result_array();

        foreach ($res as $row) {
            switch ($row['dayweek']) {
                case '0':
                    $out_array['sun']['cnt'] = $row['cnt_ord'];
                    $out_array['sun']['sum'] = MoneyOutput($row['sum_order']);
                    break;
                case '1':
                    $out_array['mon']['cnt'] = $row['cnt_ord'];
                    $out_array['mon']['sum'] = MoneyOutput($row['sum_order']);
                    break;
                case '2':
                    $out_array['tue']['cnt'] = $row['cnt_ord'];
                    $out_array['tue']['sum'] = MoneyOutput($row['sum_order']);
                    break;
                case '3':
                    $out_array['wed']['cnt'] = $row['cnt_ord'];
                    $out_array['wed']['sum'] = MoneyOutput($row['sum_order']);
                    break;
                case '4':
                    $out_array['thu']['cnt'] = $row['cnt_ord'];
                    $out_array['thu']['sum'] = MoneyOutput($row['sum_order']);
                    break;
                case '5':
                    $out_array['fri']['cnt'] = $row['cnt_ord'];
                    $out_array['fri']['sum'] = MoneyOutput($row['sum_order']);
                    break;
                case '6':
                    $out_array['sat']['cnt'] = $row['cnt_ord'];
                    $out_array['sat']['sum'] = MoneyOutput($row['sum_order']);
                    break;
            }
            $out_array['total']['cnt']+=$row['cnt_ord'];
            $out_array['total']['sum']+=$row['sum_order'];
        }
        if ($out_array['total']['sum'] > 0) {
            $out_array['total']['sum'] = MoneyOutput($out_array['total']['sum']);
        }
        return $out_array;
    }

    public function checkout_reportdata($brand, $d_bgn = '', $d_end = '') {
        if ($d_end == '') {
            $d_end = strtotime(date('Y-m-d') . ' 23:59:59');
        }
        /* Get Begin & End of Week  */
        $weekend = date('W', $d_end);
        $yearend = date('Y', $d_end);
        $dates = getDatesByWeek($weekend, $yearend);
        $d_end = $dates['end_week'];

        /* Calculate END day */
        if ($d_bgn == '') {
            $this->db->select('min(unix_timestamp(order_date)) as min_date', FALSE);
            $this->db->from('sb_orders');
            $this->db->where('is_void', 0);
            if ($brand!=='ALL') {
                $this->db->where('brand', $brand);
            }
            $res_ar = $this->db->get()->row_array();
            $last_date = $res_ar['min_date'];
        } else {
            $last_date = $d_bgn;
        }
        if ($last_date == '') {
            $last_date = $dates['start_week'];
        }
        $d_bgn = $last_date;
        /* Select # of weekes between end date and begin date */
        $this->db->select('TIMESTAMPDIFF(WEEK, from_unixtime(' . $d_bgn . '),from_unixtime(' . ($d_end) . ')) as numweeks');
        $wres = $this->db->get()->row_array();


        $out_array = array();
        // $out_index=-1;
        for ($j = 0; $j <= $wres['numweeks']; $j++) {

            $weekend = date('W', $d_end);
            $yearend = date('Y', $d_end);
            $dates = getDatesByWeek($weekend, $yearend);

            $week_bgn = $dates['start_week'];
            $week_end = $dates['end_week'];
            if (date('m', $week_bgn) != date('m', $week_end)) {
                if (date('Y', $week_bgn) != date('Y', $week_end)) {
                    $week_name = date('M, Y', $week_bgn) . '/' . date('M, Y', $week_end);
                } else {
                    $week_name = date('M', $week_bgn) . '/' . date('M, Y', $week_end);
                }
            } else {
                $week_name = date('F, Y', $week_bgn);
            }
            $out_array[] = array(
                'date' => $week_name,
                'mon_day' => '',
                'mon_cnt' => 0,
                'mon_sum' => 0,
                'tue_day' => 0,
                'tue_cnt' => 0,
                'tue_sum' => 0,
                'wed_day' => 0,
                'wed_cnt' => 0,
                'wed_sum' => 0,
                'thu_day' => 0,
                'thu_cnt' => 0,
                'thu_sum' => 0,
                'fri_day' => 0,
                'fri_cnt' => 0,
                'fri_sum' => 0,
                'sat_day' => 0,
                'sat_cnt' => 0,
                'sat_sum' => 0,
                'sun_day' => 0,
                'sun_cnt' => 0,
                'sun_sum' => 0,
                'total_cnt' => 0,
                'total_sum' => 0,
                'week_num' => $weekend,
                'year' => $yearend,
            );
            $outidx = count($out_array) - 1;
            $this->db->select("date_format(order_date,'%Y-%m-%d') as weekday, count(order_id) as cnt_ord, sum(order_total) as sum_ord ", FALSE);
            $this->db->from('sb_orders');
            $this->db->where('unix_timestamp(order_date) >= ', $week_bgn);
            $this->db->where('unix_timestamp(order_date) <= ', $week_end);
            if ($brand!=='ALL') {
                $this->db->where('brand', $brand);
            }
            $this->db->group_by('weekday');
            $res = $this->db->get()->result_array();
            foreach ($res as $row) {
                $datrep = strtotime($row['weekday']);
                $weekday = date('w', $datrep);
                switch ($weekday) {
                    case '0':
                        /* Sun */
                        $out_array[$outidx]['sun_day'] = date('j', $datrep);
                        $out_array[$outidx]['sun_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['sun_sum'] = floatval($row['sum_ord']);
                        break;
                    case '1':
                        /* Mon */
                        $out_array[$outidx]['mon_day'] = date('j', $datrep);
                        $out_array[$outidx]['mon_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['mon_sum'] = floatval($row['sum_ord']);
                        break;
                    case '2' :
                        /* Tue */
                        $out_array[$outidx]['tue_day'] = date('j', $datrep);
                        $out_array[$outidx]['tue_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['tue_sum'] = floatval($row['sum_ord']);
                        break;
                    case '3':
                        /* Wed */
                        $out_array[$outidx]['wed_day'] = date('j', $datrep);
                        $out_array[$outidx]['wed_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['wed_sum'] = floatval($row['sum_ord']);
                        break;
                    case '4':
                        /* Thu */
                        $out_array[$outidx]['thu_day'] = date('j', $datrep);
                        $out_array[$outidx]['thu_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['thu_sum'] = floatval($row['sum_ord']);
                        break;
                    case '5':
                        /* Thu */
                        $out_array[$outidx]['fri_day'] = date('j', $datrep);
                        $out_array[$outidx]['fri_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['fri_sum'] = floatval($row['sum_ord']);
                        break;
                    case '6':
                        /* Sat */
                        $out_array[$outidx]['sat_day'] = date('j', $datrep);
                        $out_array[$outidx]['sat_cnt'] = $row['cnt_ord'];
                        $out_array[$outidx]['sat_sum'] = floatval($row['sum_ord']);
                        break;
                }
                $out_array[$outidx]['total_cnt']+=$row['cnt_ord'];
                $out_array[$outidx]['total_sum']+=floatval($row['sum_ord']);
            }
            /* Rebuild Date End */
            $d_end = strtotime(date("Y-m-d", $d_end) . " - 7 days");
        }
        $out = array();
        foreach ($out_array as $row) {
            if ($row['sun_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 7);
                $row['sun_day'] = date('j', $dayw);
            }
            $row['sun_sum'] = MoneyOutput($row['sun_sum']);
            if ($row['mon_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 1);
                $row['mon_day'] = date('j', $dayw);
            }
            $row['mon_sum'] = MoneyOutput($row['mon_sum']);
            if ($row['tue_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 2);
                $row['tue_day'] = date('j', $dayw);
            }
            $row['tue_sum'] = MoneyOutput($row['tue_sum']);
            if ($row['wed_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 3);
                $row['wed_day'] = date('j', $dayw);
            }
            $row['wed_sum'] = MoneyOutput($row['wed_sum']);
            if ($row['thu_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 4);
                $row['thu_day'] = date('j', $dayw);
            }
            $row['thu_sum'] = MoneyOutput($row['thu_sum']);
            if ($row['fri_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 5);
                $row['fri_day'] = date('j', $dayw);
            }
            $row['fri_sum'] = MoneyOutput($row['fri_sum']);
            if ($row['sat_day'] == 0) {
                $dayw = getDayOfWeek($row['week_num'], $row['year'], 6);
                $row['sat_day'] = date('j', $dayw);
            }
            $row['sat_sum'] = MoneyOutput($row['sat_sum']);
            $row['total_sum'] = MoneyOutput($row['total_sum']);
            $out[] = $row;
        }
        return $out;
    }

    function get_graphp_data($type, $d_bgn = '', $d_end = '') {

        if ($d_end == '') {
            $d_end = strtotime(date('Y-m-d') . ' 23:59:59');
        }
        if ($d_bgn=='') {
            // 53 week ago
            $d_bgn = strtotime(date('Y-m-d', strtotime(date('Y-m-d',$d_end). ' - 53 weeks')));
        }
        /* Calculate nearlest monday */
        if ($type == 'chart-bydate') {
            $this->db->select('date_format(order_date,\'%Y-%m-%d\') as odate, sum(order_total) as sum_order', FALSE);
            $this->db->from('sb_orders');
            $this->db->where('unix_timestamp(order_date) >= ', $d_bgn);
            $this->db->where('unix_timestamp(order_date) <= ', $d_end);
            $this->db->group_by('odate');
            $this->db->order_by('odate');
        } else {
            $this->db->select('date_format(order_date,\'%Y %u\') as odate, sum(order_total) as sum_order',FALSE);
            $this->db->from('sb_orders');
            $this->db->where('unix_timestamp(order_date) >= ', $d_bgn);
            $this->db->where('unix_timestamp(order_date) <= ', $d_end);
            $this->db->group_by('odate');
            $this->db->order_by('odate');
            // $sql = "select date_format(order_date,'%Y %u') as date, coalesce(sum(order_total),0) as sum_order from sb_orders  where unix_timestamp(order_date) between " . $d_bgn . " and " . $d_end . " group by 1 order by 1";
        }
        $res = $this->db->get()->result_array();
        $datarows[]=['Date', 'Totals, $'];
        foreach ($res as $row) {
            $datarows[]=[$row['odate'], floatval($row['sum_order'])];
        }
        return $datarows;
    }

}
