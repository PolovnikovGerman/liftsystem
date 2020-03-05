<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Orders_model extends MY_Model
{
    const START_ORDNUM=22000;
    const INIT_ERRMSG='Unknown error. Try later';
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
            $this->db->where('o.order_blank',0);
            $this->db->where('o.arttype', $filtr['order_type']);
        }
        if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
            $this->db->where('o.brand', $filtr['brand']);
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

    function get_orders_dates($options=[]) {
        $this->db->select("max(date_format(from_unixtime(order_date),'%Y')) as max_year, min(date_format(from_unixtime(order_date),'%Y')) as min_year ",FALSE);
        $this->db->from('ts_orders');
        $this->db->where("is_canceled",0);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function orders_profit_tolals($filtr) {
        $this->db->select("count(o.order_num) as numorders, sum(o.order_qty) as qty, sum(o.revenue) as revenue,
            sum(o.shipping*o.is_shipping) as shipping, sum(o.tax) as tax, sum(o.cc_fee) as cc_fee,
            sum(coalesce(o.order_cog,0)) as order_cog, sum(o.profit) as profit",FALSE);
        $this->db->from("ts_orders o");
        $this->db->where('o.is_canceled',0);
        if (count($filtr)>0) {
            if (isset($filtr['shipping_country'])) {
                $shipsql = "select distinct(order_id) as order_id from ts_order_shipaddres ";
                if (isset($filtr['shipping_state'])) {
                    $shipsql.=" where state_id=".$filtr['shipping_state'];
                } else {
                    if (intval($filtr['shipping_country'])>0) {
                        $shipsql.=" where country_id = ".$filtr['shipping_country'];
                    } else {
                        $shipsql.=" where country_id not in (223,39)";
                    }
                }
            }

            if (isset($filtr['search']) && $filtr['search']) {
                // $this->db->like("concat(ucase(customer_name),' ',order_num,' ',revenue) ",strtoupper($filtr['search']));
                $this->db->like("concat(ucase(customer_name),' ',ucase(customer_email),' ',order_num,' ', coalesce(order_confirmation,''), ' ', ucase(order_items), ucase(order_itemnumber), revenue ) ",strtoupper($filtr['search']));
            }
            if (isset($filtr['filter'])) {
                if ($filtr['filter']==1) {
                    $this->db->where('o.order_cog is null');
                } elseif ($filtr['filter']==2) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=40');
                } elseif ($filtr['filter']==3) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=30 and round(o.profit_perc,0)<40');
                } elseif ($filtr['filter']==4) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=20 and round(o.profit_perc,0)<30');
                } elseif ($filtr['filter']==5) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=10 and round(o.profit_perc,0)<20');
                } elseif ($filtr['filter']==6) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)<=0');
                } elseif ($filtr['filter']==7) {
                    $this->db->where('o.is_canceled',1);
                } elseif ($filtr['filter']==8) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>0 and round(o.profit_perc,0)<10');
                }
            }
            // Dates
            if (isset($filtr['date_bgn'])) {
                $this->db->where('o.order_date >= ', $filtr['date_bgn']);
            }
            if (isset($filtr['date_end'])) {
                $this->db->where('o.order_date < ', $filtr['date_end']);
            }
            if (isset($filtr['shipping_country'])) {
                $this->db->join("({$shipsql}) as s" ,'s.order_id=o.order_id');
            }
            if (isset($filtr['order_type'])) {
                $this->db->where('o.order_blank',0);
                $this->db->where('o.arttype', $filtr['order_type']);
            }
        }
        $totalres=$this->db->get()->row_array();
        $sum_array=array(
            'numorders'=>intval($totalres['numorders']),
            'qty'=>intval($totalres['qty']),
            'revenue'=>floatval($totalres['revenue']),
            'shipping'=>floatval($totalres['shipping']),
            'tax'=>floatval($totalres['tax']),
            'cog'=>floatval($totalres['order_cog']),
            'cc_fee'=>floatval($totalres['cc_fee']),
            'profit'=>floatval($totalres['profit']),
            'profit_perc'=>'',
            'profit_class'=>'',
        );
        if (!isset($filtr['order_type'])) {
            // Get totals for blank
            $totals_blank = $this->_profit_totals($filtr, 'blank');
            // Get totals for new
            $totals_new = $this->_profit_totals($filtr, 'new');
            // Get Totals for repeat
            $totals_repeat = $this->_profit_totals($filtr, 'repeat');
            $sum_array['numorders_new']=$sum_array['numorders_detail_new']=intval($totals_new['numorders']);
            $sum_array['numorders_repeat']=$sum_array['numorders_detail_repeat']=intval($totals_repeat['numorders']);
            $sum_array['numorders_blank']=$sum_array['numorders_detail_blank']=intval($totals_blank['numorders']);
            // Percents ORDERS
            $sum_array['numorders_detail_newperc']=$sum_array['numorders_detail_repeatperc']=$sum_array['numorders_detail_blankperc']='';
            if ($sum_array['numorders']!=0) {
                $sum_array['numorders_detail_newperc']=round($sum_array['numorders_new']/$sum_array['numorders']*100,0);
                $sum_array['numorders_detail_repeatperc']=round($sum_array['numorders_repeat']/$sum_array['numorders']*100,0);
                $sum_array['numorders_detail_blankperc']=round($sum_array['numorders_blank']/$sum_array['numorders']*100,0);
            }
            $sum_array['qty_new']=$sum_array['qty_detail_new']=intval($totals_new['qty']);
            $sum_array['qty_repeat']=$sum_array['qty_detail_repeat']=intval($totals_repeat['qty']);
            $sum_array['qty_blank']=$sum_array['qty_detail_blank']=intval($totals_blank['qty']);
            // Percent QTY
            $sum_array['qty_detail_newperc']=$sum_array['qty_detail_repeatperc']=$sum_array['qty_detail_blankperc']='';
            if ($sum_array['qty']!=0) {
                $sum_array['qty_detail_newperc']=round($sum_array['qty_new']/$sum_array['qty']*100,0);
                $sum_array['qty_detail_repeatperc']=round($sum_array['qty_repeat']/$sum_array['qty']*100,0);
                $sum_array['qty_detail_blankperc']=round($sum_array['qty_blank']/$sum_array['qty']*100,0);
            }
            $sum_array['revenue_new']=$sum_array['revenue_detail_new']=floatval($totals_new['revenue']);
            $sum_array['revenue_repeat']=$sum_array['revenue_detail_repeat']=floatval($totals_repeat['revenue']);
            $sum_array['revenue_blank']=$sum_array['revenue_detail_blank']=floatval($totals_blank['revenue']);
            // Percent Revenue
            $sum_array['revenue_detail_newproc']=$sum_array['revenue_detail_repeatproc']=$sum_array['revenue_detail_blankproc']='';
            if ($sum_array['revenue']!=0) {
                $sum_array['revenue_detail_newproc']=round($sum_array['revenue_new']/$sum_array['revenue']*100,0);
                $sum_array['revenue_detail_repeatproc']=round($sum_array['revenue_repeat']/$sum_array['revenue']*100,0);
                $sum_array['revenue_detail_blankproc']=round($sum_array['revenue_blank']/$sum_array['revenue']*100,0);
            }
            $sum_array['shipping_new']=$sum_array['shipping_detail_new']=floatval($totals_new['shipping']);
            $sum_array['shipping_repeat']=$sum_array['shipping_detail_repeat']=floatval($totals_repeat['shipping']);
            $sum_array['shipping_blank']=$sum_array['shipping_detail_blank']=floatval($totals_blank['shipping']);
            // Percent Shipping
            $sum_array['shipping_detail_newperc']=$sum_array['shipping_detail_repeatperc']=$sum_array['shipping_detail_blankperc']='';
            if ($sum_array['shipping']!=0) {
                $sum_array['shipping_detail_newperc']=round($sum_array['shipping_new']/$sum_array['shipping']*100,0);
                $sum_array['shipping_detail_repeatperc']=round($sum_array['shipping_repeat']/$sum_array['shipping']*100,0);
                $sum_array['shipping_detail_blankperc']=round($sum_array['shipping_blank']/$sum_array['shipping']*100,0);
            }
            $sum_array['tax_new']=$sum_array['tax_detail_new']=floatval($totals_new['tax']);
            $sum_array['tax_repeat']=$sum_array['tax_detail_repeat']=floatval($totals_repeat['tax']);
            $sum_array['tax_blank']=$sum_array['tax_detail_blank']=floatval($totals_blank['tax']);
            // Tax Percent
            $sum_array['tax_detail_newperc']=$sum_array['tax_detail_repeatperc']=$sum_array['tax_detail_blankperc']='';
            if ($sum_array['tax']!=0) {
                $sum_array['tax_detail_newperc']=round($sum_array['tax_new']/$sum_array['tax']*100,0);
                $sum_array['tax_detail_repeatperc']=round($sum_array['tax_repeat']/$sum_array['tax']*100,0);
                $sum_array['tax_detail_blankperc']=round($sum_array['tax_blank']/$sum_array['tax']*100,0);
            }
            $sum_array['cog_new']=$sum_array['cog_detail_new']=floatval($totals_new['order_cog']);
            $sum_array['cog_repeat']=$sum_array['cog_detail_repeat']=floatval($totals_repeat['order_cog']);
            $sum_array['cog_blank']=$sum_array['cog_detail_blank']=floatval($totals_blank['order_cog']);
            // Procent COG
            $sum_array['cog_detail_newperc']=$sum_array['cog_detail_repeatperc']=$sum_array['cog_detail_blankperc']='';
            if ($sum_array['cog']!=0) {
                $sum_array['cog_detail_newperc']=round($sum_array['cog_new']/$sum_array['cog']*100,0);
                $sum_array['cog_detail_repeatperc']=round($sum_array['cog_repeat']/$sum_array['cog']*100,0);
                $sum_array['cog_detail_blankperc']=round($sum_array['cog_blank']/$sum_array['cog']*100,0);
            }
            $sum_array['profit_new']=$sum_array['profit_detail_new']=floatval($totals_new['profit']);
            $sum_array['profit_repeat']=$sum_array['profit_detail_repeat']=floatval($totals_repeat['profit']);
            $sum_array['profit_blank']=$sum_array['profit_detail_blank']=floatval($totals_blank['profit']);
            // Procent Profit
            $sum_array['profit_detail_newperc']=$sum_array['profit_detail_repeatperc']=$sum_array['profit_detail_blankperc']='';
            if ($sum_array['profit']!=0) {
                $sum_array['profit_detail_newperc']=round($sum_array['profit_new']/$sum_array['profit']*100,0);
                $sum_array['profit_detail_repeatperc']=round($sum_array['profit_repeat']/$sum_array['profit']*100,0);
                $sum_array['profit_detail_blankperc']=round($sum_array['profit_blank']/$sum_array['profit']*100,0);
            }
            $sum_array['numorders_new']=($sum_array['numorders_new']==0 ? '' : short_number($sum_array['numorders_new'],0));
            $sum_array['numorders_repeat']=($sum_array['numorders_repeat']==0 ? '' : short_number($sum_array['numorders_repeat'],0));
            $sum_array['numorders_blank']=($sum_array['numorders_blank']==0 ? '' : short_number($sum_array['numorders_blank'],0));
        }
        /* Prepare sum_array */
        $sum_array['show_numorders']=short_number($sum_array['numorders']);
        $sum_array['numorders']=($totalres['numorders']==0 ? '' : QTYOutput($totalres['numorders'],0));
        $sum_array['show_qty']=short_number($sum_array['qty']);
        $sum_array['qty']=($totalres['qty']==0 ? '' : QTYOutput($totalres['qty'],0));
        $sum_array['profit_perc']='';
        if ($sum_array['revenue']!=0) {
            $profit_perc=$sum_array['profit']/$sum_array['revenue']*100;
            $sum_array['profit_perc']=round($profit_perc,0).'%';
            $sum_array['profit_class']=profitClass($profit_perc);
        }
        $sum_array['show_revenue']=($sum_array['revenue']==0 ? '-' : '$'.short_number($sum_array['revenue'],2));
        $sum_array['revenue']=($sum_array['revenue']==0 ? '-' : MoneyOutput($sum_array['revenue'],0));
        $sum_array['show_shipping']=($sum_array['shipping']==0 ? '-' : '$'.short_number($sum_array['shipping'],2));
        $sum_array['shipping']=($sum_array['shipping']==0 ? '-' : MoneyOutput($sum_array['shipping'],0));
        $sum_array['show_tax']=($sum_array['tax']==0 ? '-' : '$'.short_number($sum_array['tax']));
        $sum_array['tax']=($sum_array['tax']==0 ? '-' : MoneyOutput($sum_array['tax'],0));
        $sum_array['show_cc_fee']=($sum_array['cc_fee']==0 ? '-' : '$'.short_number($sum_array['cc_fee']));
        $sum_array['cc_fee']=($sum_array['cc_fee']==0 ? '-' : MoneyOutput($sum_array['cc_fee'],0));
        $sum_array['show_cog']=($sum_array['cog']==0 ? '-' : '$'.short_number($sum_array['cog']));
        $sum_array['cog']=($sum_array['cog']==0 ? '-' : MoneyOutput($sum_array['cog'],0));
        $sum_array['show_profit']=($sum_array['profit']==0 ? '-' : '$'.short_number($sum_array['profit']));
        $sum_array['profit']=($sum_array['profit']==0 ? '-' : MoneyOutput($sum_array['profit'],0));
        return $sum_array;
    }

    private function _profit_totals($filtr, $addtype) {
        $this->db->select("count(o.order_num) as numorders, sum(o.order_qty) as qty, sum(o.revenue) as revenue,
            sum(o.shipping*o.is_shipping) as shipping, sum(o.tax) as tax, sum(o.cc_fee) as cc_fee,
            sum(coalesce(o.order_cog,0)) as order_cog, sum(o.profit) as profit",FALSE);
        $this->db->from("ts_orders o");
        $this->db->where('o.is_canceled',0);
        if (count($filtr)>0) {
            if (isset($filtr['shipping_country'])) {
                $shipsql = "select distinct(order_id) as order_id from ts_order_shipaddres ";
                if (isset($filtr['shipping_state'])) {
                    $shipsql.=" where state_id=".$filtr['shipping_state'];
                } else {
                    if (intval($filtr['shipping_country'])>0) {
                        $shipsql.=" where country_id = ".$filtr['shipping_country'];
                    } else {
                        $shipsql.=" where country_id not in (223,39)";
                    }
                }
            }

            if (isset($filtr['search']) && $filtr['search']) {
                // $this->db->like("concat(ucase(customer_name),' ',order_num,' ',revenue) ",strtoupper($filtr['search']));
                $this->db->like("concat(ucase(customer_name),' ',ucase(customer_email),' ',order_num,' ', coalesce(order_confirmation,''), ' ', ucase(order_items), ucase(order_itemnumber), revenue ) ",strtoupper($filtr['search']));
            }
            if (isset($filtr['filter'])) {
                if ($filtr['filter']==1) {
                    $this->db->where('o.order_cog is null');
                } elseif ($filtr['filter']==2) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=40');
                } elseif ($filtr['filter']==3) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=30 and round(o.profit_perc,0)<40');
                } elseif ($filtr['filter']==4) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=20 and round(o.profit_perc,0)<30');
                } elseif ($filtr['filter']==5) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=10 and round(o.profit_perc,0)<20');
                } elseif ($filtr['filter']==6) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)<=0');
                } elseif ($filtr['filter']==7) {
                    $this->db->where('o.is_canceled',1);
                } elseif ($filtr['filter']==8) {
                    $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>0 and round(o.profit_perc,0)<10');
                }
            }
            // Dates
            if (isset($filtr['date_bgn'])) {
                $this->db->where('o.order_date >= ', $filtr['date_bgn']);
            }
            if (isset($filtr['date_end'])) {
                $this->db->where('o.order_date < ', $filtr['date_end']);
            }
            if (isset($filtr['shipping_country'])) {
                $this->db->join("({$shipsql}) as s" ,'s.order_id=o.order_id');
            }
            if ($addtype=='blank') {
                $this->db->where('o.order_blank',1);
            } else {
                $this->db->where('o.order_blank',0);
                $this->db->where('o.arttype', $addtype);
            }
            if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
                $this->db->where('o.brand', $filtr['brand']);
            }
        }
        $totalres=$this->db->get()->row_array();
        return $totalres;
    }

    public function get_profit_orders($filtr,$order_by,$direct,$limit,$offset, $admin_mode, $user_id) {
        $this->load->model('user_model');
        $usrdat=$this->user_model->get_user_data($user_id);
        $item_dbtable='sb_items';
        $this->db->select('o.order_id, o.create_usr, o.order_date, o.brand, o.order_num, o.customer_name, o.customer_email, o.revenue,
            o.shipping,o.is_shipping, o.tax, o.cc_fee, o.order_cog, o.profit, o.profit_perc, o.is_canceled,
            o.reason, itm.item_name, o.item_id, o.order_items, finance_order_amountsum(o.order_id) as cnt_amnt',FALSE);
        $this->db->select('o.order_blank, o.arttype');
        $this->db->select('o.order_qty, o.shipdate, o.order_confirmation');
        $this->db->from('ts_orders o');
        // $this->db->join('brands b','b.brand_id=o.brand_id','left');
        $this->db->join("{$item_dbtable} as  itm",'itm.item_id=o.item_id','left');
        if ($admin_mode==0) {
            $this->db->where('o.is_canceled',0);
        }

        if (count($filtr)>0) {
            if (isset($filtr['search']) && $filtr['search']) {
                // $this->db->like("concat(ucase(customer_name),' ',order_num,' ',revenue,' ',ucase(o.order_items)) ",strtoupper($filtr['search']));
                $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(o.order_items), ucase(o.order_itemnumber), o.revenue ) ",strtoupper($filtr['search']));
            }
            if (isset($filtr['filter']) && $filtr['filter']==1) {
                $this->db->where('order_cog is null');
            }
            if (isset($filtr['filter']) && $filtr['filter']==2) {
                $this->db->where('order_cog is not null and round(profit_perc,1)>=40');
            }
            if (isset($filtr['filter']) && $filtr['filter']==3) {
                $this->db->where('order_cog is not null and round(profit_perc,1)>=30 and round(profit_perc,1)<40');
            }
            if (isset($filtr['filter']) && $filtr['filter']==4) {
                $this->db->where('order_cog is not null and round(profit_perc,1)>=20 and round(profit_perc,1)<30');
            }
            if (isset($filtr['filter']) && $filtr['filter']==5) {
                $this->db->where('order_cog is not null and round(profit_perc,1)>=10 and round(profit_perc,1)<20');
            }
            if (isset($filtr['filter']) && $filtr['filter']==6) {
                $this->db->where('order_cog is not null and round(profit_perc,1)<=0');
            }
            if (isset($filtr['filter']) && $filtr['filter']==8) {
                $this->db->where('order_cog is not null and round(profit_perc,1)>0 and round(profit_perc,1)<10');
            }
            if (isset($filtr['filter']) && $filtr['filter']==7) {
                $this->db->where('is_canceled',1);
            }
            if (isset($filtr['start_date'])) {
                $this->db->where('o.order_date >= ', $filtr['start_date']);
            }
            if (isset($filtr['end_date'])) {
                $this->db->where('o.order_date < ', $filtr['end_date']);
            }
            if (isset($filtr['shipping_country'])) {
                $shipsql = "select distinct(order_id) as order_id from ts_order_shipaddres ";
                if (isset($filtr['shipping_state'])) {
                    $shipsql.=" where state_id=".$filtr['shipping_state'];
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
                $this->db->where('o.order_blank',0);
                $this->db->where('o.arttype', $filtr['order_type']);
            }
            if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
                $this->db->where('o.brand', $filtr['brand']);
            }
        }
        $this->db->limit($limit,$offset);
        $this->db->order_by($order_by,$direct);
        $res=$this->db->get()->result_array();
        /* Summary */
        $out_array=array();
        foreach ($res as $row) {
            $this->db->select('count(DISTINCT oi.item_color) as cnt_colors, min(oi.item_color) as item_color');
            $this->db->from('ts_order_itemcolors oi');
            $this->db->join('ts_order_items i','i.order_item_id=oi.order_item_id');
            $this->db->where('i.order_id', $row['order_id']);
            $colorres=$this->db->get()->row_array();
            $row['coloropt']='';
            $row['color']=$colorres['item_color'];
            $row['colordata']='title="'.$row['color'].'"';
            if ($colorres['cnt_colors']>1) {
                $row['color'] = $this->multicolor;
                $row['coloropt'] = 'multicolor';
                $row['colordata']='href="/finance/get_ordercolordetails?id='.$row['order_id'].'"';
            }
            $row['lineclass']='';

            if ($admin_mode==0) {
                $row['cancellnk']='';
            } else {
                $row['cancellnk']='<a class="cancord" data-order="'.$row['order_id'].'" href="javascript:void(0);"><img src="/img/accounting/cancel_order.png"/></a>';
            }

            $row['order_date']=($row['order_date']=='' ? '' : date('m/d/y',$row['order_date']));
            $row['revenue']=(intval($row['revenue'])==0 ? '-' : MoneyOutput($row['revenue'],2));
            $row['shipping']=(intval($row['shipping'])==0 ? '-' : MoneyOutput($row['shipping'],2));
            $row['tax']=(intval($row['tax'])==0 ? '-' : MoneyOutput($row['tax'],2));
            $row['out_email']=($row['customer_email']=='' ? '&nbsp;' : '<img src="/img/email.png" alt="Email" title="'.$row['customer_email'].'"/>');
            $row['cc_fee_show']=($row['cc_fee']==0 ? 0 : 1);
            $row['cc_fee']=(floatval($row['cc_fee'])==0 ? '' : MoneyOutput($row['cc_fee'],2));
            $row['profit_class']='';
            $row['proftitleclass']='';
            $row['proftitle']='';
            $row['input_ship']='&nbsp;';
            $order_type = '';
            $order_type_class='';
            if ($row['order_blank']==1) {
                $order_type='B';
                $order_type_class='ordertypeblank';
            } else {
                if ($row['arttype']=='new') {
                    $order_type='N';
                    $order_type_class='ordertypenew';
                } else {
                    $order_type='R';
                    $order_type_class='ordertyperepeat';
                }
            }
            $row['ordertype']=$order_type;
            $row['ordertype_class']=$order_type_class;

            $row['item_class']='';
            if ($row['is_canceled']) {
                $row['cancellnk']='<a class="revertord" data-order="'.$row['order_id'].'" href="javascript:void(0);"><img src="/img/accounting/revert.png"/></a>';
                $row['lineclass']='cancelord';
                $row['order_cog']='canceled';
                $row['cog_class']='canceledcog';
                $row['profit_class']='cancelprof';
                $row['profit_perc']='CANC';
                $row['add']='&nbsp;';
                $row['editlnk']='';
                $row['dellnk']='';
            } else {
                $row['input_ship']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                if ($row['is_shipping']==0) {
                    $row['input_ship']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                }
                if ($usrdat['profit_view']=='Points') {
                    $row['profit']=round($row['profit']*$this->config->item('profitpts'),0).' pts';
                } else {
                    $row['profit']=MoneyOutput($row['profit'],2);
                }
                $row['points']=round($row['profit']*$this->config->item('profitpts'),0).' pts';

                if ($row['item_id']==$this->config->item('custom_id')) {
                    $row['item_class']='customitem';
                }
                if ($row['order_cog']=='') {
                    $row['order_cog']='project';
                    $row['cog_class']='projectcog';
                    $row['profit_class']='projprof';
                    $row['profit_perc']=$this->project_name;
                    $row['add']='';
                } else {
                    $row['cog_class']='';
                    $row['profit_class']=orderProfitClass($row['profit_perc']);
                    if ($row['profit_perc']<$this->config->item('minimal_profitperc') && !empty($row['reason'])) {
                        $row['proftitleclass']='lowprofittitle';
                        $row['proftitle']='title="'.$row['reason'].'"';
                    }
                    $row['profit_perc']=number_format($row['profit_perc'],1,'.',',').'%';
                    if ($admin_mode==0) {
                        $row['add']=(floatval($row['cnt_amnt'])==floatval($row['order_cog']) ? '' : '<a href="javascript:void(0);" class="editcoglnk" id="add'.$row['order_id'].'">*</a>' );
                    } else {
                        $row['add']='<a href="javascript:void(0);" class="editcoglnk" id="add'.$row['order_id'].'">*</a>';
                    }
                    $row['order_cog']=MoneyOutput($row['order_cog'],2);
                }
            }
            $row['out_shipdate']=($row['shipdate']==0 ? '&nbsp;' : date('m/d', $row['shipdate']));
            $out_array[]=$row;
        }
        return $out_array;
    }

    public function get_profitexport_fields() {
        $fields=['field_order_date', 'field_order_num', 'field_is_canceled', 'field_customer_name', 'field_order_qty', 'field_colors', 'field_order_itemnumber',
            'field_order_items', 'field_revenue','field_shipping', 'field_tax', 'field_shipping_state', 'field_order_cog','field_profit', 'field_profit_perc',
            'field_vendor_dates', 'field_vendor_name', 'field_vendor_cog', 'field_rush_days', 'field_order_usr_repic','field_order_new'];
        $labels=['Date', 'Order#', 'Canceled', 'Customer', 'QTY', 'Colors', 'Item #',
            'Item Name', 'Revenue','Shipping','Sales Tax','Shipping States', 'COG','Profit','Profit %',
            'PO Dates', 'PO Vendor', 'COG/PO Vendors','Rush Days','Sales Replica','Order New/Repeat'];
        $data=[];
        $idx=0;
        foreach ($fields as $row) {
            $data[]=[
                'field'=>$fields[$idx],
                'label'=>$labels[$idx],
            ];
            $idx++;
        }
        return $data;
    }

}
