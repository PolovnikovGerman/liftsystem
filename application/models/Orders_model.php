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
            $res['profit_class']=orderProfitClass($res['profit_perc']);
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
            if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
                $this->db->where('o.brand', $filtr['brand']);
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
                $row['colordata']='data-viewsrc="/accounting/get_ordercolordetails?id='.$row['order_id'].'"';
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
                        $row['proftitle']='data-content="'.$row['reason'].'"';
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

    public function profit_export($postdata) {
        $out=['result'=>$this->error_result,'msg'=>'Select Fields for Export'];
        $fields=$this->_export_fields($postdata);
        $labels=$this->_export_labels($fields);
        if (count($fields)>0) {
            $search=array();
            if (isset($postdata['search'])) {
                $search['search']=$postdata['search'];
            }
            if (isset($postdata['filter'])) {
                $search['filter']=$postdata['filter'];
            }
            if (isset($postdata['add_filter'])) {
                $search['add_filtr']=$postdata['add_filter'];
            }
            if ($postdata['show_year']==1) {
                if ($postdata['year']>0) {
                    $nxtyear = $postdata['year']+1;
                    if ($postdata['month']==0) {
                        $search['start_date']=strtotime($postdata['year'].'-01-01');
                        $search['end_date']=strtotime($nxtyear.'-01-01');
                    } else {
                        $start = $postdata['year'].'-'.str_pad($postdata['month'],2,'0',STR_PAD_LEFT).'-01';
                        $search['start_date']=strtotime($start);
                        $finish = date('Y-m-d', strtotime($start. ' + 1 month'));
                        $search['end_date']=strtotime($finish);
                    }
                }
            } else {
                if ($postdata['date_bgn']) {
                    $search['start_date']=strtotime($postdata['date_bgn']);
                }
                if ($postdata['date_end']) {
                    // $search['end_date']=strtotime($postdata['date_end']);
                    $d_finish = date('Y-m-d',strtotime($postdata['date_end']));
                    $search['end_date'] = date(strtotime("+1 day", strtotime($d_finish)));
                }
            }
            if (isset($postdata['shipping_country']) && intval($postdata['shipping_country'])!==0) {
                $search['shipping_country']=$postdata['shipping_country'];
                if (isset($postdata['shipping_state']) && intval($postdata['shipping_state'])>0) {
                    $search['shipping_state'] = $postdata['shipping_state'];
                }
            }
            if (isset($postdata['order_type']) && !empty($postdata['order_type'])) {
                $search['order_type']=$postdata['order_type'];
            }


            $select_flds=[];
            foreach ($fields as $row) {
                if (!in_array($row,['colors','vendor_dates', 'vendor_name', 'vendor_cog','rush_days','shipping_state','order_new'])) {
                    array_push($select_flds, $row);
                }
            }
            $this->db->select('o.order_id, o.is_canceled, o.order_blank, o.arttype');
            foreach ($select_flds as $select_fld) {
                $this->db->select("o.{$select_fld}");
            }
            $this->db->from('ts_orders o');

            if (count($search)>0) {
                if (isset($search['search']) && $search['search']) {
                    $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(o.order_items), ucase(o.order_itemnumber), o.revenue ) ",strtoupper($search['search']));
                }
                if (isset($search['filter']) && $search['filter']==1) {
                    $this->db->where('order_cog is null');
                }
                if (isset($search['filter']) && $search['filter']==2) {
                    $this->db->where('order_cog is not null and round(profit_perc,1)>=40');
                }
                if (isset($search['filter']) && $search['filter']==3) {
                    $this->db->where('order_cog is not null and round(profit_perc,1)>=30 and round(profit_perc,1)<40');
                }
                if (isset($search['filter']) && $search['filter']==4) {
                    $this->db->where('order_cog is not null and round(profit_perc,1)>=20 and round(profit_perc,1)<30');
                }
                if (isset($search['filter']) && $search['filter']==5) {
                    $this->db->where('order_cog is not null and round(profit_perc,1)>=10 and round(profit_perc,1)<20');
                }
                if (isset($search['filter']) && $search['filter']==6) {
                    $this->db->where('order_cog is not null and round(profit_perc,1)<=0');
                }
                if (isset($search['filter']) && $search['filter']==8) {
                    $this->db->where('order_cog is not null and round(profit_perc,1)>0 and round(profit_perc,1)<10');
                }
                if (isset($search['filter']) && $search['filter']==7) {
                    $this->db->where('is_canceled',1);
                    // } else {
                    //     $this->db->where('is_canceled',0);
                }
                if (isset($search['start_date'])) {
                    $this->db->where('o.order_date >= ', $search['start_date']);
                }
                if (isset($search['end_date'])) {
                    $this->db->where('o.order_date < ', $search['end_date']);
                }
                if (isset($search['shipping_country'])) {
                    $shipsql = "select distinct(order_id) as order_id from ts_order_shipaddres ";
                    if (isset($search['shipping_state'])) {
                        $shipsql.=" where state_id=".$search['shipping_state'];
                    } else {
                        if (intval($search['shipping_country'])>0) {
                            $shipsql.=" where country_id=".$search['shipping_country'];
                        } else {
                            $shipsql.=" where country_id not in (223, 39)";
                        }
                    }
                    $this->db->join("({$shipsql}) as s",'s.order_id=o.order_id');
                }
                if (isset($search['order_type'])) {
                    $this->db->where('o.order_blank',0);
                    $this->db->where('o.arttype', $search['order_type']);
                }
            }
            if (isset($postdata['brand']) && $postdata['brand']!=='ALL') {
                $this->db->where('o.brand', $postdata['brand']);
            }
            $this->db->order_by('o.order_id');
            $res=$this->db->get()->result_array();

            $data=[];
            foreach ($res as $row) {
                if (in_array('order_date', $fields)) {
                    $row['order_date']=date('m/d/Y', $row['order_date']);
                }
                if (in_array('is_canceled', $fields)) {
                    $row['is_canceled']=($row['is_canceled']==1 ? 'YES' : '');
                }
                if (in_array('colors', $fields)) {
                    // Add Colors
                    $this->db->select("group_concat(oi.item_color,' - ', oi.item_qty) as color",FALSE);
                    $this->db->from('ts_order_itemcolors oi');
                    $this->db->join('ts_order_items i','i.order_item_id=oi.order_item_id');
                    $this->db->where('i.order_id', $row['order_id']);
                    $colorres=$this->db->get()->row_array();
                    $row['colors']=$colorres['color'];
                }
                if (in_array('shipping_state', $fields)) {
                    // Add shipping states
                    $this->db->select('group_concat(st.state_code) ship_states');
                    $this->db->from('ts_order_shipaddres s');
                    $this->db->join('ts_states st','s.state_id=st.state_id');
                    $this->db->where('s.order_id', $row['order_id']);
                    $statesres=$this->db->get()->row_array();
                    $row['shipping_state']=$statesres['ship_states'];
                }
                if (in_array('vendor_cog', $fields) || in_array('vendor_dates', $fields) || in_array('vendor_name', $fields)) {
                    $this->db->select('v.vendor_name, oa.amount_sum, oa.amount_date');
                    $this->db->from('ts_order_amounts oa');
                    $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
                    $this->db->where('oa.order_id',$row['order_id']);
                    $cogres=$this->db->get()->result_array();
                    $cogcont='';
                    $vendname='';
                    $venddate='';
                    $numpp = 1; $numcogs = count($cogres);
                    foreach ($cogres as $crow) {
                        $cogcont.=$crow['amount_sum'];
                        $vendname.=$crow['vendor_name'];
                        $venddate.=date('m/d/y', $crow['amount_date']);
                        $numpp++;
                        if ($numpp<=$numcogs) {
                            $cogcont.=PHP_EOL;
                            $vendname.=PHP_EOL;
                            $venddate.=PHP_EOL;
                        }
                    }
                    if (in_array('vendor_cog', $fields)) {
                        $row['vendor_cog'] = $cogcont;
                    }
                    if (in_array('vendor_dates', $fields)) {
                        $row['vendor_dates'] = $venddate;
                    }
                    if (in_array('vendor_name', $fields)) {
                        $row['vendor_name'] = $vendname;
                    }
                }
                if (in_array('profit', $fields) && $row['is_canceled']==1) {
                    $row['profit']='Canceled';
                }
                if (in_array('order_cog', $fields) && $row['is_canceled']==1) {
                    $row['order_cog']='Canceled';
                }
                if (in_array('profit_perc', $fields) && $row['is_canceled']==1) {
                    $row['profit_perc']='Canceled';
                }
                if (in_array('order_cog', $fields) && is_null($row['order_cog'])) {
                    $row['order_cog']='Project';
                }
                if (in_array('profit_perc', $fields) && is_null($row['profit_perc'])) {
                    $row['profit_perc']='Project';
                }
                if (in_array('order_usr_repic', $fields)) {
                    if (intval($row['order_usr_repic'])>0) {
                        $this->db->select('user_name');
                        $this->db->from('users');
                        $this->db->where('user_id', $row['order_usr_repic']);
                        $usrres=$this->db->get()->row_array();
                        $row['order_usr_repic']=$usrres['user_name'];
                    } else {
                        $row['order_usr_repic']='Weborder';
                    }
                }
                if (in_array('rush_days', $fields)) {
                    $this->db->select('*');
                    $this->db->from('ts_order_shippings');
                    $this->db->where('order_id', $row['order_id']);
                    $shipres=$this->db->get()->row_array();
                    $row['rush_days']='n/d';
                    if (isset($shipres['order_shipping_id'])) {
                        $shipdate=$shipres['shipdate'];
                        $rush_list=$shipres['rush_list'];
                        $outrush=unserialize($rush_list);
                        $list=$outrush['rush'];
                        foreach ($list as $lrow) {
                            if ($lrow['date']==$shipdate) {
                                $row['rush_days']=$lrow['rushterm'];
                                break;
                            }
                        }
                    }
                }
                if (in_array('order_new', $fields)) {
                    if ($row['order_blank']==1) {
                        $row['order_new']='Blank';
                    } else {
                        $row['order_new']=ucfirst($row['arttype']);
                    }
                }
                $datarow=[];
                foreach ($fields as $frow) {
                    $datarow[$frow]=$row[$frow];
                }
                $data[]=$datarow;
            }
            // save to file
            $this->load->model('exportexcell_model');
            $replink=$this->exportexcell_model->export_profitorders($data, $labels);
            $out['result']=$this->success_result;
            $out['url']=$replink;
        }
        return $out;
    }

    private function _export_fields($data) {
        $out=[];
        foreach ($data as $key=>$val) {
            $var=substr($key,0,6);
            if (substr($key,0,6)=='field_') {
                array_push($out, substr($key,6));
            }
        }
        return $out;
    }

    private function _export_labels($fields) {
        $labels=[];
        foreach ($fields as $frow) {
            if ($frow=='order_date') {
                array_push($labels,'Date');
            } elseif ($frow=='order_num') {
                array_push($labels, 'Order#');
            } elseif ($frow=='is_canceled') {
                array_push($labels, 'Canceled');
            } elseif ($frow=='customer_name') {
                array_push($labels, 'Customer');
            } elseif ($frow=='order_qty') {
                array_push($labels,'QTY');
            } elseif ($frow=='colors') {
                array_push($labels,'Colors');
            } elseif ($frow=='order_itemnumber') {
                array_push($labels,'Item #');
            } elseif ($frow=='order_items') {
                array_push($labels,'Item Name');
            } elseif ($frow=='revenue') {
                array_push($labels,'Revenue');
            } elseif ($frow=='shipping') {
                array_push($labels,'Shipping');
            } elseif ($frow=='tax') {
                array_push($labels, 'Tax');
            } elseif ($frow=='shipping_state') {
                array_push($labels, 'Shipping States');
            } elseif ($frow=='order_cog') {
                array_push($labels,'COG');
            } elseif ($frow=='profit') {
                array_push($labels,'Profit');
            } elseif ($frow=='profit_perc') {
                array_push($labels,'Profit %');
            } elseif ($frow=='vendor_cog') {
                array_push($labels,'COG/PO Vendors');
            } elseif ($frow=='rush_days') {
                array_push($labels,'Rush Days');
            } elseif ($frow=='order_usr_repic') {
                array_push($labels,'Sales Replica');
            } elseif ($frow=='vendor_dates') {
                array_push($labels,'PO Dates');
            } elseif ($frow=='vendor_name') {
                array_push($labels, 'PO Vendors');
            } elseif ($frow=='order_new') {
                array_push($labels, 'Order Type');
            }
        }
        return $labels;
    }

    public function get_profit_limitdates($brand) {
        $this->db->select('max(order_date) as max_date, min(order_date) as min_date');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        if (isset($res['max_date'])) {
            $res['max_month']=date('m',$res['max_date']);
            $res['max_year']=date('Y',$res['max_date']);
        }
        if (isset($res['min_date'])) {
            $res['min_month']=date('m',$res['min_date']);
            $res['min_year']=date('Y',$res['min_date']);
        }
        return $res;
    }

    /* Calculate average  */
    public function calendar_orders($year, $brand) {
        /* Empty array */
        $empty_val='&mdash;';
        $kilolimit=10000;
        // Prepare sub-query
        $field_list = 'select date_format(from_unixtime(order_date),\'%Y\') as ordyear, count(order_id) as cntord, sum(revenue) as sumrevenue, sum(profit) as sumprofit from ts_orders where is_canceled=0';
        if ($brand!=='ALL') {
            $field_list.=' and brand = \''.$brand.'\'';
        }
        $projSql = $field_list.' and profit_perc is null group by ordyear';
        $greenSql = $field_list.' and profit_perc>=40 group by ordyear';
        $whiteSql = $field_list.' and profit_perc>=30 and profit_perc<40 group by ordyear';
        $orangeSql = $field_list.' and profit_perc>=20 and profit_perc<30 group by ordyear';
        $redSql = $field_list.' and profit_perc>=10 and profit_perc<20 group by ordyear';
        $maroonSql = $field_list.' and profit_perc>0 and profit_perc<10 group by ordyear';
        $blackSql = $field_list.' and profit_perc<=0 group by ordyear';
        // Build SQL
        $this->db->select("date_format(from_unixtime(ord.order_date),'%Y') as order_year, count(order_id) cntord, sum(revenue) as sumrevenue, sum(profit) as sumprofit, proj.cntord as proj_ord,
                                green.cntord as green_order,white.cntord as white_order,orange.cntord as orange_order,red.cntord as red_order,
                                maroon.cntord as maroon_order, black.cntord as black_order,
                                proj.sumrevenue as proj_revenue, proj.sumprofit as proj_profit,
                                green.sumrevenue as green_revenue, green.sumprofit as green_profit,
                                white.sumrevenue as white_revenue, white.sumprofit as white_profit,
                                orange.sumrevenue as orange_revenue, orange.sumprofit as orange_profit,
                                red.sumrevenue as red_revenue, red.sumprofit as red_profit,
                                maroon.sumrevenue as maroon_revenue, maroon.sumprofit as maroon_profit,
                                black.sumrevenue as black_revenue, black.sumprofit as black_profit
                                ",FALSE);
        $this->db->from("ts_orders ord");
        $this->db->join("({$projSql}) proj","proj.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->join("({$greenSql}) green","green.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->join("({$whiteSql}) white","white.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->join("({$orangeSql}) orange","orange.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->join("({$maroonSql}) maroon","maroon.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->join("({$redSql}) red","red.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->join("({$blackSql}) black","black.ordyear=date_format(from_unixtime(ord.order_date),'%Y')","left");
        $this->db->where('ord.is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('ord.brand', $brand);
        }
        $this->db->where("date_format(from_unixtime(order_date),'%Y')",$year);
        $this->db->group_by("order_year");
        $res=$this->db->get()->row_array();
        $out = array(
            'title' => $year,
            'order_year' => '',
            'total_order' => '',
            'proj_ord' => $empty_val,
            'proj_perc' => $empty_val,
            'proj_link' => '',
            'green_ord' => $empty_val,
            'green_perc' => $empty_val,
            'green_link' => '',
            'white_ord' => $empty_val,
            'white_perc' => $empty_val,
            'white_link' => '',
            'orange_ord' => $empty_val,
            'orange_perc' => $empty_val,
            'orange_link' => '',
            'red_ord' => $empty_val,
            'red_perc' => $empty_val,
            'red_link' => '',
            'maroon_ord' => $empty_val,
            'maroon_perc' => $empty_val,
            'maroon_link' => '',
            'black_ord' => $empty_val,
            'black_perc' => $empty_val,
            'black_link' => '',
            'proj_revenue' => $empty_val,
            'proj_profit' => $empty_val,
            'green_revenue' => $empty_val,
            'green_profit' => $empty_val,
            'white_revenue' => $empty_val,
            'white_profit' => $empty_val,
            'orange_revenue' => $empty_val,
            'orange_profit' => $empty_val,
            'red_revenue' => $empty_val,
            'red_profit' => $empty_val,
            'maroon_revenue' => $empty_val,
            'maroon_profit' => $empty_val,
            'black_revenue' => $empty_val,
            'black_profit' => $empty_val,
            'proj_revenue_class' => 'small',
            'proj_profit_class' => 'small',
            'green_revenue_class' => 'small',
            'green_profit_class' => 'small',
            'white_revenue_class' => 'small',
            'white_profit_class' => 'small',
            'orange_revenue_class' => 'small',
            'orange_profit_class' => 'small',
            'red_revenue_class' => 'small',
            'red_profit_class' => 'small',
            'maroon_revenue_class' => 'small',
            'maroon_profit_class' => 'small',
            'black_revenue_class' => 'small',
            'black_profit_class' => 'small',

        );
        if (isset($res['order_year'])) {
            $detail_url = '/accounting/totaldetails/';
            $out['order_year'] = $res['order_year'];
            $total_ord = intval($res['cntord']);
            $total_rev = intval($res['sumrevenue']);
            $total_prof = intval($res['sumprofit']);
            if ($total_ord != 0) {
                $out['total_order'] = $total_ord;
                $out['total_rev'] = $total_rev;
                $out['total_prof'] = $total_prof;
                /* Project */
                $proj_ord = intval($res['proj_ord']);
                if ($proj_ord != 0) {
                    $out['proj_ord'] = $proj_ord;
                    $out['proj_perc'] = round($proj_ord / $total_ord * 100, 0) . '%';
                    $out['proj_link'] = $detail_url."?type=projprof&year=" . $year."&brand=".$brand;
                }
                $proj_revenue = round(floatval($res['proj_revenue']), 0);
                if ($proj_revenue > 0) {
                    if ($proj_revenue>=$kilolimit) {
                        $out['proj_revenue_class']='';
                        $out['proj_revenue']=  MoneyOutput($proj_revenue/1000,0).'K';
                    } else {
                        $out['proj_revenue'] = MoneyOutput($proj_revenue,0);
                    }
                }
                $proj_profit = round(floatval($res['proj_profit']), 0);
                if ($proj_profit > 0) {
                    if ($proj_profit>$kilolimit) {
                        $out['proj_profit_class']='';
                        $out['proj_profit']=MoneyOutput($proj_profit/1000,0).'K';
                    } else {
                        $out['proj_profit']=MoneyOutput($proj_profit,0);
                    }
                }
                $green_ord = intval($res['green_order']);
                if ($green_ord != 0) {
                    $out['green_ord'] = $green_ord;
                    $out['green_perc'] = round($green_ord / $total_ord * 100, 0) . '%';
                    $out['green_link'] = $detail_url."?type=green&year=" . $year."&brand=".$brand;
                }
                $green_revenue = round(floatval($res['green_revenue']), 0);
                if ($green_revenue != 0) {
                    if ($green_revenue>=$kilolimit) {
                        $out['green_revenue_class']='';
                        $out['green_revenue'] = MoneyOutput($green_revenue/1000, 0).'K';
                    } else {
                        $out['green_revenue'] = MoneyOutput($green_revenue, 0);
                    }
                }
                $green_profit = round(floatval($res['green_profit']), 0);
                if ($green_profit != 0) {
                    if ($green_profit>=$kilolimit) {
                        $out['green_profit_class']='';
                        $out['green_profit'] = MoneyOutput($green_profit/1000, 0).'K';
                    } else {
                        $out['green_profit'] = MoneyOutput($green_profit, 0);
                    }
                }
                $white_ord = intval($res['white_order']);
                if ($white_ord != 0) {
                    $out['white_ord'] = $white_ord;
                    $out['white_perc'] = round($white_ord / $total_ord * 100, 0) . '%';
                    $out['white_link'] = $detail_url."?type=white&year=" . $year."&brand=".$brand;
                }
                $white_revenue = round(floatval($res['white_revenue']), 0);
                if ($white_revenue != 0) {
                    if ($white_revenue>=$kilolimit) {
                        $out['white_revenue_class']='';
                        $out['white_revenue'] = MoneyOutput($white_revenue/1000, 0).'K';
                    } else {
                        $out['white_revenue'] = MoneyOutput($white_revenue, 0);
                    }
                }
                $white_profit = round(floatval($res['white_profit']), 0);
                if ($white_profit != 0) {
                    if ($white_revenue>=$kilolimit) {
                        $out['white_profit_class']='';
                        $out['white_profit'] = MoneyOutput($white_profit/1000, 0).'K';
                    } else {
                        $out['white_profit'] = MoneyOutput($white_profit, 0);
                    }
                }
                $orange_ord = intval($res['orange_order']);
                if ($orange_ord != 0) {
                    $out['orange_ord'] = $orange_ord;
                    $out['orange_perc'] = round($orange_ord / $total_ord * 100, 0) . '%';
                    $out['orange_link'] = $detail_url."?type=orange&year=" . $year."&brand=".$brand;
                }
                $orange_revenue = round(floatval($res['orange_revenue']), 0);
                if ($orange_revenue != 0) {
                    if ($orange_revenue>=$kilolimit) {
                        $out['orange_revenue_class']='';
                        $out['orange_revenue'] = MoneyOutput($orange_revenue/1000, 0).'K';
                    } else {
                        $out['orange_revenue'] = MoneyOutput($orange_revenue, 0);
                    }
                }
                $orange_profit = round(floatval($res['orange_profit']), 0);
                if ($orange_profit != 0) {
                    if ($orange_revenue>=$kilolimit) {
                        $out['orange_profit_class']='';
                        $out['orange_profit'] = MoneyOutput($orange_profit/1000,0).'K';
                    } else {
                        $out['orange_profit'] = MoneyOutput($orange_profit, 0);
                    }
                }
                $red_ord = intval($res['red_order']);
                if ($red_ord != 0) {
                    $out['red_ord'] = $red_ord;
                    $out['red_perc'] = round($red_ord / $total_ord * 100, 0) . '%';
                    $out['red_link'] = $detail_url."?type=red&year=" . $year."&brand=".$brand;
                }
                $red_revenue = round(floatval($res['red_revenue']), 0);
                if ($red_revenue != 0) {
                    if ($red_revenue>=$kilolimit) {
                        $out['orange_profit_class']='';
                        $out['red_revenue'] = MoneyOutput($red_revenue/1000, 0).'K';
                    } else {
                        $out['red_revenue'] = MoneyOutput($red_revenue, 0);
                    }
                }
                $red_profit = round(floatval($res['red_profit']), 0);
                if ($red_profit != 0) {
                    if ($red_profit>=$kilolimit) {
                        $out['red_profit_class']='';
                        $out['red_profit'] = MoneyOutput($red_profit/1000, 0).'K';
                    } else {
                        $out['red_profit'] = MoneyOutput($red_profit, 0);
                    }
                }
                $maroon_ord = intval($res['maroon_order']);
                if ($maroon_ord != 0) {
                    $out['maroon_ord'] = $maroon_ord;
                    $out['maroon_perc'] = round($maroon_ord / $total_ord * 100, 0) . '%';
                    $out['maroon_link'] = $detail_url."?type=moroon&year=" . $year."&brand=".$brand;
                }
                $maroon_revenue = round(floatval($res['maroon_revenue']), 0);
                if ($maroon_revenue != 0) {
                    if ($maroon_revenue>=$kilolimit) {
                        $out['maroon_revenue_class']='';
                        $out['maroon_revenue'] = MoneyOutput($maroon_revenue/1000, 0).'K';
                    } else {
                        $out['maroon_revenue'] = MoneyOutput($maroon_revenue, 0);
                    }
                }
                $maroon_profit = round(floatval($res['maroon_profit']), 0);
                if ($maroon_profit != 0) {
                    if ($maroon_profit>=$kilolimit) {
                        $out['maroon_profit_class']='';
                        $out['maroon_profit'] = MoneyOutput($maroon_profit/1000, 0).'K';
                    } else {
                        $out['maroon_profit'] =  MoneyOutput($maroon_profit, 0);
                    }
                }
                $black_ord = intval($res['black_order']);
                if ($black_ord != 0) {
                    $out['black_ord'] = $black_ord;
                    $out['black_perc'] = round($black_ord / $total_ord * 100, 0) . '%';
                    $out['black_link'] = $detail_url."?type=black&year=" . $year."&brand=".$brand;
                }
                $black_revenue = round(floatval($res['black_revenue']), 0);
                if ($black_revenue != 0) {
                    if (abs($black_revenue)>=$kilolimit) {
                        $out['black_revenue_class']='';
                        $out['black_revenue'] = MoneyOutput($black_revenue/1000, 0).'K';
                    } else {
                        $out['black_revenue'] = MoneyOutput($black_revenue, 0);
                    }
                }
                $black_profit = round(floatval($res['black_profit']), 0);
                if ($black_profit != 0) {
                    if (abs($black_revenue)>=$kilolimit) {
                        $out['black_profit_class']='';
                        $out['black_profit'] = MoneyOutput($black_profit/1000, 0).'K';
                    } else {
                        $out['black_profit'] = MoneyOutput($black_profit, 0);
                    }
                }
            }
        }
        return $out;
    }

    /* Get data about orders per year */
    function get_orders_byprofittype($year, $type) {
        $datbgn=strtotime($year."-01-01 00:00:00");
        $datend=strtotime($year."-12-31 23:59:59");
        $this->db->select('*');
        $this->db->from('ts_orders');
        $this->db->where('order_date >= ',$datbgn);
        $this->db->where('order_date <= ',$datend);
        switch ($type) {
            case 'projprof':
                $this->db->where('profit_perc is null');
                break;
            case 'black':
                $this->db->where('profit_perc <= ',0);
                break;
            case 'moroon':
                $this->db->where('profit_perc > ',0);
                $this->db->where('profit_perc < ',10);
                break;
            case 'red':
                $this->db->where('profit_perc >= ',10);
                $this->db->where('profit_perc < ',20);
                break;
            case 'orange':
                $this->db->where('profit_perc >= ',20);
                $this->db->where('profit_perc < ',30);
                break;
            case 'white':
                $this->db->where('profit_perc >= ',30);
                $this->db->where('profit_perc < ',40);
                break;
            case 'green':
                $this->db->where('profit_perc >= ',40);
                break;
        }
        $this->db->where('is_canceled',0);
        $this->db->order_by('revenue','desc');
        $result=$this->db->get()->result_array();
        $orders=array();
        $def_profperc='';
        if ($type=='proj') {
            $def_profperc='PROJ';
        }
        $total_profit=0;
        $total_revenue=0;
        $total_orders=0;
        foreach ($result as $row) {
            $total_orders++;
            $total_profit+=floatval($row['profit']);
            $total_revenue+=floatval($row['revenue']);
            $orders[]=array(
                'order_num'=>$row['order_num'],
                'order_date'=>date('m/d/y',$row['order_date']),
                'revenue'=>(floatval($row['revenue'])==0 ? '' : '$'.  number_format($row['revenue'],2,'.',',')),
                'profit'=>(floatval($row['profit'])==0 ? '' : '$'.number_format($row['profit'],2,'.',',')),
                'profit_perc'=>($row['profit_perc']=='' ? $def_profperc : round($row['profit_perc'],0).'%'),
                'customer'=>($row['customer_name']=='' ? '&nbsp;' : $row['customer_name']),
                'item_name'=>($row['order_items']=='' ? '&nbsp;' : $row['order_items']),
            );
        }
        $avg_revenue=0;
        $avg_profit=0;
        if ($total_orders!=0) {
            $avg_profit=$total_profit/$total_orders;
            $avg_revenue=$total_revenue/$total_orders;
        }
        $totals=array(
            'order_date' => $year,
            'type' => $type,
            'total_revenue'=>($total_revenue==0 ? '' : '$'.number_format($total_revenue,2,'.',',')),
            'total_profit'=>($total_profit==0 ? '' : '$'.number_format($total_profit,2,'.',',')),
            'avg_revenue'=>($avg_revenue==0 ? '' : '$'.number_format($avg_revenue,2,'.',',')),
            'avg_profit'=>($avg_profit==0 ? '' : '$'.number_format($avg_profit,2,'.',',')),
            'total_orders'=>($total_orders==0 ? '' : $total_orders),
        );
        return array('orders'=>$orders,'numord'=>$total_orders, 'totals'=>$totals);
    }

    public function get_order_colordata($order_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Order Not Found'];
        $this->db->select('oi.item_color, oi.item_qty');
        $this->db->from('ts_order_itemcolors oi');
        $this->db->join('ts_order_items i','i.order_item_id=oi.order_item_id');
        $this->db->where('i.order_id', $order_id);
        $result=$this->db->get()->result_array();
        if (!empty($result)) {
            $out['result']=$this->success_result;
            $out['data']=$result;
        }
        return $out;
    }

    /* Cancel_order */
    function cancel_order($order_id,$flag, $user_id) {
        $this->db->set('is_canceled',$flag);
        $this->db->set('update_date',time());
        $this->db->set('update_usr',$user_id);
        $this->db->where('order_id',$order_id);
        $this->db->update('ts_orders');
        if ($this->db->affected_rows()==0) {
            $retval=FALSE;
        } else {
            if ($flag) {
                $orderdat=$this->get_order_detail($order_id);
                $this->db->select('np.*, netprofit_profit(datebgn, dateend) as gross_profit',FALSE);
                $this->db->from('netprofit np');
                $this->db->where('np.profit_month',NULL);
                $this->db->where('np.datebgn <= ',$orderdat['order_date']);
                $this->db->where('np.dateend > ',$orderdat['order_date']);
                $netdat=$this->db->get()->row_array();
                if (isset($netdat['profit_id']) && $netdat['debtinclude']==1) {
                    $this->load->model('balances_model');
                    $total_options=array(
                        'type'=>'week',
                        'start'=>$this->config->item('netprofit_start'),
                    );
                    $rundat=$this->balances_model->get_netprofit_runs($total_options);
                    $newtotalrun=$rundat['out_debtval'];
                    $oldtotalrun=$newtotalrun+$orderdat['profit'];
                    /* Get total data */
                    $totalcost=floatval($netdat['profit_operating'])+floatval($netdat['profit_payroll'])+floatval($netdat['profit_advertising'])+floatval($netdat['profit_projects'])+floatval($netdat['profit_purchases']);
                    $netprofit=floatval($netdat['gross_profit'])-$totalcost;
                    $newdebt=floatval($netprofit)-floatval($netdat['profit_owners'])-floatval($netdat['profit_saved'])-floatval($netdat['od2']);
                    if ($newdebt<0) {
                        $outnewdebt='($'.number_format(abs($newdebt),0,'.',',').')';
                    } else {
                        $outnewdebt='$'.number_format($newdebt,0,'.',',');
                    }
                    // $newdebt=$netdat['gross_profit'];
                    $olddebt=$newdebt+$orderdat['profit'];
                    if ($olddebt<0) {
                        $outolddebt='($'.number_format(abs($olddebt),0,'.',',').')';
                    } else {
                        $outolddebt='$'.number_format(abs($olddebt),0,'.',',');
                    }
                    $start_month=date('M',$netdat['datebgn']);
                    $start_year=date('Y',$netdat['datebgn']);
                    $end_month=date('M',$netdat['dateend']);
                    $end_year=date('Y',$netdat['dateend']);
                    if ($start_month!=$end_month) {
                        $weekname=$start_month.'/'.$end_month;
                    } else {
                        $weekname=$start_month;
                    }
                    $weekname.=' '.date('j',$netdat['datebgn']).'-'.date('j',$netdat['dateend']);
                    if ($start_year!=$end_year) {
                        $weekname.=' '.$start_year.'/'.date('y',$netdat['dateend']);
                    } else {
                        $weekname.=', '.$start_year;
                    }
                    if ($oldtotalrun<0) {
                        $outoldrundebt='($'.number_format(abs($oldtotalrun),0,'.',',').')';
                    } else {
                        $outoldrundebt='$'.number_format($oldtotalrun,0,'.',',');
                    }
                    if ($newtotalrun<0) {
                        $outnewrundebt='($'.number_format(abs($newtotalrun),0,'.',',').')';
                    } else {
                        $outnewrundebt='$'.number_format($newtotalrun,0,'.',',');
                    }
                    $notifoptions=array(
                        'ordercancel'=>1,
                        'orderdata'=>$orderdat,
                        'olddebt'=>$outolddebt,
                        'newdebt'=>$outnewdebt,
                        'weeknum'=>$weekname,
                        'user_id'=>$user_id,
                        'oldtotalrun'=>$outoldrundebt,
                        'newtotalrun'=>$outnewrundebt,
                    );
                    $this->notify_netdebtchanged($notifoptions);
                }
            }
            $retval=TRUE;
        }
        return $retval;
    }

    public function ship_orderprofit($order_id) {
        $out=array('result'=> $this->error_result, 'msg'=>'Order Not Found');

        $order_det=$this->get_order_detail($order_id);
        if (isset($order_det['order_id'])) {
            $is_shipping=($order_det['is_shipping']==1 ? 0 : 1);
            $this->db->set('is_shipping',$is_shipping);
            $this->db->set('update_date',time());
            if (floatval($order_det['order_cog'])!=0) {
                /* Update Profit */
                $profit=floatval($order_det['revenue'])-(floatval($order_det['shipping'])*$is_shipping)-floatval($order_det['tax'])-floatval($order_det['cc_fee'])-floatval($order_det['order_cog']);
                if (floatval($order_det['revenue'])!=0) {
                    $profit_perc=round($profit/$order_det['revenue']*100,1);
                } else {
                    $profit_perc=NULL;
                }
                $this->db->set('profit',$profit);
                $this->db->set('profit_perc',$profit_perc);
            }
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_orders');
            // Update ART View parameters
            $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
            $this->db->from('ts_orders');
            $this->db->where('order_id',$order_id);
            $statres=$this->db->get()->row_array();
            $this->db->where('order_id',$order_id);
            $this->db->set('order_artview', $statres['aprrovview']);
            $this->db->set('order_placed', $statres['placeord']);
            $this->db->update('ts_orders');
            $out['result']=  $this->success_result;
        }
        return $out;
    }

    function get_months($year, $max_month, $brand, $min_month=1) {
        $dbgn='01/01/'.$year;
        $dbgn=strtotime($dbgn);
        $dend=strtotime(date('m/d/Y',$dbgn).' +1 year');
        $months=array();
        $max_month=intval($max_month);
        $min_month=intval($min_month);
        for ($i=$min_month; $i<=$max_month; $i++) {
            $dat=strtotime(str_pad($i, 2, '0', STR_PAD_LEFT).'/01/'.$year);
            $nmonth=date('M',$dat);
            $months[$i]=array(
                'month'=>$i,
                'month_name'=>$nmonth,
                'link_class'=>'normal',
            );
        }
        /* select by month */
        $this->db->select('date_format(from_unixtime(order_date), \'%m\') month, count(order_id) cnt');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where('order_cog is null');
        $this->db->where('order_date >= ', $dbgn);
        $this->db->where('order_date <= ', $dend);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('month');
        $res=$this->db->get()->result_array();
        /*$sql="SELECT  FROM (`ts_orders`) WHERE `order_cog` is null AND is_canceled=0 AND
        `order_date` >= ".$dbgn." AND `order_date` <= ".$dend." GROUP BY date_format(from_unixtime(order_date), '%m')";
        $res=$this->db->query($sql)->result_array();*/
        foreach ($res as $row) {
            $months[intval($row['month'])]['link_class']='estimate';
        }
        return $months;
    }

    public function calendar_totals($year, $brand,  $prvdata=array(), $compare=0) {
        $out=array(
            'total_orders'=>0,
            'avg_revenue'=>0,
            'avg_profit'=>0,
            'avg_profit_perc'=>0,
            'revenue'=>0,
            'profit'=>0,
        );
        $this->db->select('count(order_id) as total_orders, avg(revenue) as avg_revenue, sum(revenue) as revenue, sum(profit) as profit');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where('date_format(from_unixtime(order_date),\'%Y\')', $year);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();

        $total_orders=floatval($res['total_orders']);
        $profit=round(floatval($res['profit']),0);
        $revenue=round(floatval($res['revenue']),0);

        $avg_revenue=$avg_profit=$avg_profit_perc=0;
        if ($total_orders>0) {
            $avg_profit=round($profit/$total_orders,2);
            $avg_revenue=round($revenue/$total_orders,2);
            if ($revenue>0) {
                $avg_profit_perc=round($profit/$revenue*100,1);
            }
        }
        $out['total_orders']=($total_orders==0 ? '&nbsp;' : QTYOutput($total_orders));
        $out['avg_revenue']=($avg_revenue==0 ? '&nbsp;' : MoneyOutput($avg_revenue));
        $out['avg_profit']=($avg_profit==0 ? '&nbsp;' : MoneyOutput($avg_profit));
        $out['avg_profit_perc']=($avg_profit_perc==0 ? '&nbsp;' : number_format($avg_profit_perc,1,'.',',').'%');
        $out['profit_class']=orderProfitClass(round($avg_profit_perc),0);
        $out['profit']=($profit==0 ? '&nbsp;' : MoneyOutput($profit));
        $out['revenue']=($revenue==0 ? '&nbsp;' : MoneyOutput($revenue));
        // Out number
        $out['num_orders']=$total_orders;
        $out['num_avgrevenue']=$avg_revenue;
        $out['num_avgprofit']=$avg_profit;
        $out['num_avgprofitperc']=$avg_profit_perc;
        $out['num_profit']=$profit;
        $out['num_revenue']=$revenue;
        // Growth
        $growth=array(
            'order_num'=>'',
            'order_perc'=>'',
            'revenue_num'=>'',
            'revenue_perc'=>'',
            'avgrevenue_num'=>'',
            'avgrevenue_perc'=>'',
            'profit_num'=>'',
            'profit_perc'=>'',
            'avgprofit_num'=>'',
            'avgprofit_perc'=>'',
            'ave_num'=>'',
            'ave_proc'=>'',
        );
        if ($compare==0) {
            $out['growth']=$growth;
        } else {
            if ($total_orders==0) {
                if ($prvdata['num_orders']>0) {
                    $growth['order_num']=(-1)*($prvdata['num_orders']);
                    $growth['order_proc']=-100;
                }
            } else {
                // 1500 - 1000 = 500.   500/1000 = 50% growth
                if ($prvdata['num_orders']==0) {
                    $growth['order_num']=$total_orders;
                    $growth['order_proc']=100;
                } else {
                    $diff=($total_orders-$prvdata['num_orders']);
                    $growth['order_num']=$diff;
                    $growth['order_proc']=round($diff/$prvdata['num_orders']*100,0);
                }
            }
            if ($revenue==0) {
                if ($prvdata['num_revenue']>0) {
                    $growth['revenue_num']=(-1)*($prvdata['num_revenue']);
                    $growth['revenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_revenue']==0) {
                    $growth['revenue_num']=$revenue;
                    $growth['revenue_perc']=100;
                } else {
                    $diff=($revenue-$prvdata['num_revenue']);
                    $growth['revenue_num']=$diff;
                    $growth['revenue_perc']=round($diff/$prvdata['num_revenue']*100,0);
                }
            }
            if ($avg_revenue==0) {
                if ($prvdata['num_avgrevenue']>0) {
                    $growth['avgrevenue_num']=(-1)*($prvdata['num_avgrevenue']);
                    $growth['avgrevenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgrevenue']==0) {
                    $growth['avgrevenue_num']=$avg_revenue;
                    $growth['avgrevenue_perc']=100;
                } else {
                    $diff=($avg_revenue-$prvdata['num_avgrevenue']);
                    $growth['avgrevenue_num']=$diff;
                    $growth['avgrevenue_perc']=round($diff/$prvdata['num_avgrevenue']*100,0);
                }
            }
            if ($profit==0) {
                if ($prvdata['num_profit']>0) {
                    $growth['profit_num']=(-1)*($prvdata['num_profit']);
                    $growth['profit_perc']=-100;
                }
            } else {
                if ($prvdata['num_profit']==0) {
                    $growth['profit_num']=$profit;
                    $growth['profit_perc']=100;
                } else {
                    $diff=($profit-$prvdata['num_profit']);
                    $growth['profit_num']=$diff;
                    $growth['profit_perc']=round($diff/$prvdata['num_profit']*100,0);
                }
            }
            if ($avg_profit==0) {
                if ($prvdata['num_avgprofit']>0) {
                    $growth['avgprofit_num']=(-1)*$prvdata['num_avgprofit'];
                    $growth['avgprofit_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofit']==0) {
                    $growth['avgprofit_num']=$avg_profit;
                    $growth['avgprofit_perc']=100;
                } else {
                    $diff=($avg_profit-$prvdata['num_avgprofit']);
                    $growth['avgprofit_num']=$diff;
                    $growth['avgprofit_perc']=round($diff/$prvdata['num_avgprofit']*100,0);
                }
            }
            if ($avg_profit_perc==0) {
                if ($prvdata['num_avgprofitperc']) {
                    $growth['ave_num']=(-1)*$prvdata['num_avgprofitperc'];
                    $growth['ave_proc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofitperc']==0) {
                    $growth['ave_num']=$avg_profit_perc;
                    $growth['ave_proc']=100;
                } else {
                    $diff=($avg_profit_perc-$prvdata['num_avgprofitperc']);
                    $growth['ave_num']=round($diff,1);
                    $growth['ave_proc']=round($diff/$prvdata['num_avgprofitperc']*100,0);
                }
            }
            $out['growth']=$growth;
        }
        return $out;
    }

    public function orders_pacetohit($year, $brand, $prvdata=array(), $compare=0) {
        $out=array();

        $this->db->select('count(order_id) as total_orders, sum(revenue) as revenue, sum(profit) as profit');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where('date_format(from_unixtime(order_date),\'%Y\')', $year);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        $totals=array();
        $totals['total_orders']=intval($res['total_orders']);
        $totals['profit']=floatval($res['profit']);
        $totals['revenue']=floatval($res['revenue']);

        $now = strtotime(date('Y-m-d',time())); // or your date as well
        $yearbgn = strtotime($year."-01-01");
        $datediff=$now - $yearbgn;
        $daysdiff=floor($datediff/(60*60*24));
        $nxtyear=strtotime(date("Y-m-d", $yearbgn) . " +1year")-1;
        $yearsdays=floor(($nxtyear-$yearbgn)/(60*60*24));
        // $kf=($yearsdays/$daysdiff);
        $total_bankdays=BankDays($yearbgn, $nxtyear);
        $elaps_bankdays=BankDays($yearbgn, $now);
        $out['bankdays']=$total_bankdays;
        $bankdays=($total_bankdays-$elaps_bankdays);
        $out['reminder_days']=$bankdays;
        $out['reminder_prc']=round($bankdays/$total_bankdays*100,0);
        if ($elaps_bankdays>0) {
            $kf=$total_bankdays/$elaps_bankdays;
        } else {
            $kf=1;
        }
        $total_orders=round($totals['total_orders']*$kf,0);
        $revenue=round($totals['revenue']*$kf,2);
        $profit=round($totals['profit']*$kf,2);

        $out['total_orders']=($total_orders==0 ? '&nbsp;' : number_format($total_orders,0,'.',','));
        $out['profit']=($profit==0 ? '&nbsp;' : '$'.number_format($profit,0,'.',','));
        $out['revenue']=($revenue==0 ? '&nbsp;' : '$'.number_format($revenue,0,'.',','));
        $avg_revenue=$avg_profit=0;
        if ($total_orders>0) {
            $avg_revenue=($revenue/$total_orders);
            $avg_profit=($profit/$total_orders);
        }
        $out['avg_revenue']=($avg_revenue==0 ? '&nbsp;' : '$'.number_format($avg_revenue,2,'.',','));
        $out['avg_profit']=($avg_profit==0 ? '&nbsp;' : '$'.number_format($avg_profit,2,'.',','));
        // Profit %
        $avg_profit_perc=0;
        if ($revenue>0) {
            $avg_profit_perc=($profit/$revenue*100);
        }
        $out['avg_profit_perc']=($avg_profit_perc==0 ? '&nbsp;' : number_format($avg_profit_perc,1,'.',',').'%');
        $out['profit_class']=orderProfitClass(round($avg_profit_perc),0);
        // Get Goals
        if ($brand=='ALL') {
            $this->db->select('max(goal_order_id) as goal_order_id, sum(goal_orders) as goal_orders');
            $this->db->select('sum(goal_revenue) as goal_revenue, sum(goal_profit) as goal_profit');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', $year);
            $this->db->where('goal_type', 'TOTAL');
        } else {
            $this->db->select('*');
            $this->db->from('ts_goal_orders');
            $this->db->where('goal_year', $year);
            $this->db->where('brand', $brand);
            $this->db->where('goal_type', 'TOTAL');
        }
        $goalres=$this->db->get()->row_array();

        if (!isset($goalres['goal_order_id'])) {
            $id = -1;
            if ($brand!=='ALL') {
                // Insert New record
                $this->db->set('goal_year', $year);
                $this->db->set('goal_type', 'TOTAL');
                $this->db->set('goal_orders', $total_orders);
                $this->db->set('goal_revenue', $revenue);
                $this->db->set('goal_profit', $profit);
                $this->db->insert('ts_goal_orders');
                $id=$this->db->insert_id();
            }
            $goalres=array(
                'goal_order_id'=>$id,
                'goal_year'=>$year,
                'goal_orders'=>$total_orders,
                'goal_revenue'=>$revenue,
                'goal_profit'=>$profit,
            );
        }
        $out['goal_orders']=($goalres['goal_orders']==0 ? '&nbsp;' : number_format($goalres['goal_orders'],0,'.',','));
        $out['goal_revenue']=($goalres['goal_revenue']==0 ? '&nbsp;' : '$'.number_format($goalres['goal_revenue'],0,'.',','));
        $out['goal_profit']=($goalres['goal_profit']==0 ? '&nbsp;' : '$'.number_format($goalres['goal_profit'],0,'.',','));
        // Calc other params
        $goal_avgrevenue=$goal_avgprofit=0;
        if ($goalres['goal_orders']>0) {
            $goal_avgrevenue=($goalres['goal_revenue']/$goalres['goal_orders']);
            $goal_avgprofit=($goalres['goal_profit']/$goalres['goal_orders']);
        }
        $out['goal_avgrevenue']=($goal_avgrevenue==0 ? '&nbsp;' : '$'.number_format($goal_avgrevenue,2,'.',','));
        $out['goal_avgprofit']=($goal_avgprofit==0 ? '&nbsp;' : '$'.number_format($goal_avgprofit,2,'.',','));
        // Profit %
        $goal_avgprofit_perc=0;
        if ($goalres['goal_revenue']>0) {
            $goal_avgprofit_perc=($goalres['goal_profit']/$goalres['goal_revenue']*100);
        }
        $out['goal_avgprofit_perc']=($goal_avgprofit_perc==0 ? '&nbsp;' : number_format($goal_avgprofit_perc,1,'.',',').'%');
        $out['goal_profit_class']=orderProfitClass(round($goal_avgprofit_perc),0);
        // Reminder
        $rem_orders=$rem_profit=$rem_revenue='&nbsp;';
        if ($bankdays>0) {
            $rem_orders=round(($goalres['goal_orders']-$totals['total_orders'])/$bankdays,0);
            $remprofit=round(($goalres['goal_profit']-$totals['profit'])/$bankdays,0);

            if ($remprofit<0) {
                $rem_profit='&ndash;$'.number_format(abs($remprofit),0,'.',',');
            } else {
                $rem_profit='$'.number_format($remprofit,0,'.',',');
            }
            $remrevenue=round(($goalres['goal_revenue']-$totals['revenue'])/$bankdays,0);
            if ($remrevenue<0) {
                $rem_revenue='&ndash;$'.number_format(abs($remrevenue),0,'.',',');
            } else {
                $rem_revenue='$'.number_format($remrevenue,0,'.',',');
            }
        }
        $out['reminder_orders']=$rem_orders;
        $out['reminder_profit']=$rem_profit;
        $out['reminder_revenue']=$rem_revenue;
        if ($compare==0) {
            $out['growth']=array();
            $out['growth_goals']=array();
        } else {
            $growth_goals=$growth=array(
                'order_num'=>'',
                'order_perc'=>'',
                'revenue_num'=>'',
                'revenue_perc'=>'',
                'avgrevenue_num'=>'',
                'avgrevenue_perc'=>'',
                'profit_num'=>'',
                'profit_perc'=>'',
                'avgprofit_num'=>'',
                'avgprofit_perc'=>'',
                'ave_num'=>'',
                'ave_proc'=>'',
            );
            // Grows for Pace to Hit
            if ($total_orders==0) {
                if ($prvdata['num_orders']>0) {
                    $growth['order_num']=(-1)*($prvdata['num_orders']);
                    $growth['order_proc']=-100;
                }
            } else {
                // 1500 - 1000 = 500.   500/1000 = 50% growth
                if ($prvdata['num_orders']==0) {
                    $growth['order_num']=$total_orders;
                    $growth['order_proc']=100;
                } else {
                    $diff=($total_orders-$prvdata['num_orders']);
                    $growth['order_num']=$diff;
                    $growth['order_proc']=round($diff/$prvdata['num_orders']*100,0);
                }
            }
            if ($revenue==0) {
                if ($prvdata['num_revenue']>0) {
                    $growth['revenue_num']=(-1)*($prvdata['num_revenue']);
                    $growth['revenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_revenue']==0) {
                    $growth['revenue_num']=$revenue;
                    $growth['revenue_perc']=100;
                } else {
                    $diff=($revenue-$prvdata['num_revenue']);
                    $growth['revenue_num']=$diff;
                    $growth['revenue_perc']=round($diff/$prvdata['num_revenue']*100,0);
                }
            }
            if ($avg_revenue==0) {
                if ($prvdata['num_avgrevenue']>0) {
                    $growth['avgrevenue_num']=(-1)*($prvdata['num_avgrevenue']);
                    $growth['avgrevenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgrevenue']==0) {
                    $growth['avgrevenue_num']=$avg_revenue;
                    $growth['avgrevenue_perc']=100;
                } else {
                    $diff=($avg_revenue-$prvdata['num_avgrevenue']);
                    $growth['avgrevenue_num']=$diff;
                    $growth['avgrevenue_perc']=round($diff/$prvdata['num_avgrevenue']*100,0);
                }
            }
            if ($profit==0) {
                if ($prvdata['num_profit']>0) {
                    $growth['profit_num']=(-1)*($prvdata['num_profit']);
                    $growth['profit_perc']=-100;
                }
            } else {
                if ($prvdata['num_profit']==0) {
                    $growth['profit_num']=$profit;
                    $growth['profit_perc']=100;
                } else {
                    $diff=($profit-$prvdata['num_profit']);
                    $growth['profit_num']=$diff;
                    $growth['profit_perc']=round($diff/$prvdata['num_profit']*100,0);
                }
            }
            if ($avg_profit==0) {
                if ($prvdata['num_avgprofit']>0) {
                    $growth['avgprofit_num']=(-1)*$prvdata['num_avgprofit'];
                    $growth['avgprofit_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofit']==0) {
                    $growth['avgprofit_num']=$avg_profit;
                    $growth['avgprofit_perc']=100;
                } else {
                    $diff=($avg_profit-$prvdata['num_avgprofit']);
                    $growth['avgprofit_num']=$diff;
                    $growth['avgprofit_perc']=round($diff/$prvdata['num_avgprofit']*100,0);
                }
            }
            if ($avg_profit_perc==0) {
                if ($prvdata['num_avgprofitperc']) {
                    $growth['ave_num']=(-1)*$prvdata['num_avgprofitperc'];
                    $growth['ave_proc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofitperc']==0) {
                    $growth['ave_num']=$avg_profit_perc;
                    $growth['ave_proc']=100;
                } else {
                    $diff=($avg_profit_perc-$prvdata['num_avgprofitperc']);
                    $growth['ave_num']=round($diff,1);
                    $growth['ave_proc']=round($diff/$prvdata['num_avgprofitperc']*100,0);
                }
            }
            $out['growth']=$growth;

            if ($goalres['goal_orders']==0) {
                if ($prvdata['num_orders']>0) {
                    $growth_goals['order_num']=(-1)*($prvdata['num_orders']);
                    $growth_goals['order_proc']=-100;
                }
            } else {
                if ($prvdata['num_orders']==0) {
                    $growth_goals['order_num']=$goalres['goal_orders'];
                    $growth_goals['order_proc']=100;
                } else {
                    $diff=($goalres['goal_orders']-$prvdata['num_orders']);
                    $growth_goals['order_num']=$diff;
                    $growth_goals['order_proc']=round($diff/$prvdata['num_orders']*100,0);
                }
            }
            if ($goalres['goal_revenue']==0) {
                if ($prvdata['num_revenue']>0) {
                    $growth_goals['revenue_num']=(-1)*($prvdata['num_revenue']);
                    $growth_goals['revenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_revenue']==0) {
                    $growth_goals['revenue_num']=$goalres['goal_revenue'];
                    $growth_goals['revenue_perc']=100;
                } else {
                    $diff=($goalres['goal_revenue']-$prvdata['num_revenue']);
                    $growth_goals['revenue_num']=$diff;
                    $growth_goals['revenue_perc']=round($diff/$prvdata['num_revenue']*100,0);
                }
            }
            if ($goal_avgrevenue==0) {
                if ($prvdata['num_avgrevenue']>0) {
                    $growth_goals['avgrevenue_num']=(-1)*($prvdata['num_avgrevenue']);
                    $growth_goals['avgrevenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgrevenue']==0) {
                    $growth_goals['avgrevenue_num']=$goal_avgrevenue;
                    $growth_goals['avgrevenue_perc']=100;
                } else {
                    $diff=($goal_avgrevenue-$prvdata['num_avgrevenue']);
                    $growth_goals['avgrevenue_num']=$diff;
                    $growth_goals['avgrevenue_perc']=round($diff/$prvdata['num_avgrevenue']*100,0);
                }
            }
            if ($goalres['goal_profit']==0) {
                if ($prvdata['num_profit']>0) {
                    $growth_goals['profit_num']=(-1)*($prvdata['num_profit']);
                    $growth_goals['profit_perc']=-100;
                }
            } else {
                if ($prvdata['num_profit']==0) {
                    $growth_goals['profit_num']=$goalres['goal_profit'];
                    $growth_goals['profit_perc']=100;
                } else {
                    $diff=($goalres['goal_profit']-$prvdata['num_profit']);
                    $growth_goals['profit_num']=$diff;
                    $growth_goals['profit_perc']=round($diff/$prvdata['num_profit']*100,0);
                }
            }
            if ($goal_avgprofit==0) {
                if ($prvdata['num_avgprofit']>0) {
                    $growth_goals['avgprofit_num']=(-1)*$prvdata['num_avgprofit'];
                    $growth_goals['avgprofit_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofit']==0) {
                    $growth_goals['avgprofit_num']=$goal_avgprofit;
                    $growth_goals['avgprofit_perc']=100;
                } else {
                    $diff=($avg_profit-$prvdata['num_avgprofit']);
                    $growth_goals['avgprofit_num']=$diff;
                    $growth_goals['avgprofit_perc']=round($diff/$prvdata['num_avgprofit']*100,0);
                }
            }
            if ($goal_avgprofit_perc==0) {
                if ($prvdata['num_avgprofitperc']) {
                    $growth_goals['ave_num']=(-1)*$prvdata['num_avgprofitperc'];
                    $growth_goals['ave_proc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofitperc']==0) {
                    $growth_goals['ave_num']=$goal_avgprofit_perc;
                    $growth_goals['ave_proc']=100;
                } else {
                    $diff=($goal_avgprofit_perc-$prvdata['num_avgprofitperc']);
                    $growth_goals['ave_num']=round($diff,1);
                    $growth_goals['ave_proc']=round($diff/$prvdata['num_avgprofitperc']*100,0);
                }
            }
            $out['growth_goals']=$growth_goals;
        }
        return $out;
    }

    function orders_by_date($month, $year, $brand) {
        $dat_month_bgn=  strtotime(str_pad($month, 2, '0' ,STR_PAD_LEFT).'/01/'.$year);
        $current_month=date('F',$dat_month_bgn).' '.$year;
        $dat_month_end=strtotime(date('m/d/Y',$dat_month_bgn).' + 1 month');
        $dat_month_end=$dat_month_end-1;
        /* Day of week - bgn */
        $week=date('N',$dat_month_bgn);
        switch ($week) {
            case 1:
                break;
            case 2:
                $dat_month_bgn=strtotime(date('m/d/Y',$dat_month_bgn).' -1 day');
                break;
            case 3:
                $dat_month_bgn=strtotime(date('m/d/Y',$dat_month_bgn).' -2 days');
                break;
            case 4:
                $dat_month_bgn=strtotime(date('m/d/Y',$dat_month_bgn).' -3 days');
                break;
            case 5:
                $dat_month_bgn=strtotime(date('m/d/Y',$dat_month_bgn).' -4 days');
                break;
            case 6:
                $dat_month_bgn=strtotime(date('m/d/Y',$dat_month_bgn).' -5 days');
                break;
            case 7:
                $dat_month_bgn=strtotime(date('m/d/Y',$dat_month_bgn).' -6 days');
                break;
        }

        /* End time */
        $week=date('N',$dat_month_end);
        switch ($week) {
            case 7:
                break;
            case 6:
                $dat_month_end=strtotime(date('m/d/Y',$dat_month_end).' +1 day');
                break;
            case 5:
                $dat_month_end=strtotime(date('m/d/Y',$dat_month_end).' +2 days');
                break;
            case 4:
                $dat_month_end=strtotime(date('m/d/Y',$dat_month_end).' +3 days');
                break;
            case 3:
                $dat_month_end=strtotime(date('m/d/Y',$dat_month_end).' +4 days');
                break;
            case 2:
                $dat_month_end=strtotime(date('m/d/Y',$dat_month_end).' +5 days');
                break;
            case 1:
                $dat_month_end=strtotime(date('m/d/Y',$dat_month_end).' +6 days');
                break;
        }

        $i=$dat_month_bgn;
        $datsrch=array();
        $data_results=array();
        $curweek=date('W',$dat_month_bgn);
        $week_results[$curweek]=array('week'=>$curweek, 'profit'=>0,'orders'=>0,'profit_percent'=>0,'revenue'=>0);
        $month_results=array('profit'=>0,'orders'=>0,'profit_percent'=>0,'revenue'=>0);
        while ($i<=$dat_month_end) {
            array_push($datsrch, date('m/d/Y',$i));
            $data_results[]=array(
                'day'=>date('j',$i),
                'weekday'=>  strtolower(date('D',$i)),
                'week'=>date('W',$i),
                'profit'=>0,
                'orders'=>0,
                'profit_percent'=>0,
                'revenue'=>0,
                'day_class'=>'empty',
                'curmonth'=>(date('m',$i)==$month ? 1 : 0),
                'curdate'=>$i,
            );
            if (date('W',$i)!=$curweek) {
                $curweek=date('W',$i);
                $week_results[$curweek]=array('week'=>$curweek, 'profit'=>0,'orders'=>0,'profit_percent'=>0,'revenue'=>0);
            }
            $i=strtotime(date('m/d/Y',$i).' + 1 day');
        }

        /* Select date where Profit is estimated */
        $this->db->select('date_format(from_unixtime(order_date),\'%m/%d/%Y\') AS order_date, count(order_id) AS numorders');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where('order_cog is null');
        // $this->db->where_in('order_date',$datsrch);
        $this->db->where('order_date >= ',$dat_month_bgn);
        $this->db->where('order_date <= ',$dat_month_end);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('date_format(from_unixtime(order_date),\'%m/%d/%Y\')');
        $res=$this->db->get()->result_array();
        foreach ($res as $row) {
            /* Key in search array */
            if (in_array($row['order_date'], $datsrch)) {
                $key=array_search($row['order_date'], $datsrch);
                if ($data_results[$key]['curmonth']==1) {
                    $data_results[$key]['day_class']='projprof';
                }
            }
        }

        /* Common data */
        $this->db->select('date_format(from_unixtime(order_date),\'%m/%d/%Y\') AS order_date,sum(profit) AS profit');
        $this->db->select('count(order_id) AS numorders, sum(coalesce(order_cog,(revenue * 0.34))) AS order_cog, sum(coalesce(revenue,0)) AS revenue');
        $this->db->from('ts_orders');
        // $this->db->where_in('order_date',$datsrch);
        $this->db->where('order_date >= ',$dat_month_bgn);
        $this->db->where('order_date <= ',$dat_month_end);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('date_format(from_unixtime(order_date),\'%m/%d/%Y\')');
        $this->db->order_by('order_date');
        $datord=$this->db->get()->result_array();
        $res=array();
        foreach ($datord as $row) {
            if (in_array($row['order_date'], $datsrch)) {
                $key=array_search($row['order_date'],$datsrch);
                $data_results[$key]['orders']=$row['numorders'];
                $data_results[$key]['profit']=$row['profit'];
                $data_results[$key]['revenue']=$row['revenue'];
                $weekkey=$data_results[$key]['week'];
                $week_results[$weekkey]['orders']+=$row['numorders'];
                $week_results[$weekkey]['revenue']+=$row['revenue'];
                $week_results[$weekkey]['profit']+=$row['profit'];
                $weekday=$data_results[$key]['weekday'];
                if ($data_results[$key]['curmonth']==1) {
                    /* Add to Week resuls and day Results */
                    $month_results['orders']+=$row['numorders'];
                    $month_results['profit']+=$row['profit'];
                    $month_results['revenue']+=$row['revenue'];
                }

            }
        }
        /* Recallculate Week results */
        $weeks=array();
        foreach ($week_results as $row) {
            $row['profit_perc']=($row['revenue']==0 ? 0 : round($row['profit']/$row['revenue']*100,0));
            $row['profit_class']='emptyweek';
            if ($row['revenue']) {
                $row['profit_class']=orderProfitClass($row['profit_perc']);
            }
            if ($row['profit']==0) {
                $row['profitdata_class']='bluetxt';
            } else {
                $row['profitdata_class']='';
            }
            $row['profit']=($row['profit']==0 ? '-----' : '$'.number_format($row['profit'],2,'.',','));

            $row['revenue']=($row['revenue']==0 ? '------' : '$'.number_format($row['revenue'],2,'.',','));
            $row['orders']=($row['orders']==0 ? '0 ordres' : $row['orders'].' orders');
            $row['profit_perc']=($row['profit_perc']==0 ? '&nbsp;' : $row['profit_perc'].'%');
            $row['week']=intval($row['week']);
            $weeks[$row['week']]=$row;
        }
        $days=array();
        foreach ($data_results as $row) {
            $row['profit_perc']=($row['revenue']==0 ? 0 : round($row['profit']/$row['revenue']*100,0));
            if ($row['curmonth']==0) {
                /* Day from other month */
                $row['day_class']='othermonth';
                $row['profit_class']='blue2txt';
                if ($row['orders']==0) {
                    $row['profitval_class']='blue2txt';
                } else {
                    $row['profitval_class']='';
                }
            } elseif ($row['day_class']=='projprof') {
                $row['profit_class']='whitetxt';
                $row['profitval_class']='';
            } else {
                if ($row['orders']>0) {
                    $row['day_class']=orderProfitClass($row['profit_perc']);
                    $row['profitval_class']='';
                } else {
                    $row['day_class']='emptyday';
                    $row['profitval_class']='bluetxt';
                }
                $row['profit_class']='bluetxt';
            }
            $row['profit_perc']=($row['profit_perc']==0 ? '---' : $row['profit_perc'].'%');
            $row['revenue']=($row['revenue']==0 ? '------' : '$'.number_format($row['revenue'],2,'.',','));
            $row['profit']=($row['profit']==0 ? '------' : '$'.number_format($row['profit'],2,'.',','));
            $row['orders']=($row['orders']==0 ? '0 orders' : $row['orders'].' orders');
            $days[]=$row;
        }
        /*  */
        $profit_perc=($month_results['revenue']==0 ? 0 : $month_results['profit']/$month_results['revenue']*100);
        $month_results['profit_perc']=round($profit_perc,0);
        /* Prepare for out */
        $month_results['profit_class']=orderProfitClass($profit_perc);
        $month_results['profit']=($month_results['profit']==0 ? '&nbsp;' : '$'.number_format($month_results['profit'],2,'.',','));
        $month_results['revenue']=($month_results['revenue']==0 ? '&nbsp;' : '$'.number_format($month_results['revenue'],2,'.',','));
        $month_results['orders']=($month_results['orders']==0 ? '&nbsp;' : $month_results['orders'].' orders');
        $month_results['profit_perc']=($month_results['profit_perc']==0 ? '&nbsp;' : $month_results['profit_perc'].'%');
        /* Rebuild weekdays summary for show */

        $out_array=array(
            'data_results'=>$days,
            'weeks_results'=>$weeks,
            'month_results'=>$month_results,
            'current_month'=>$current_month,
        );

        return $out_array;
    }

    // Get data filter about orders */
    public function get_filter_data($year, $startmonth, $endmonth, $brand, $prvdata=array(), $compare=0) {
        $out=array(
            'total_orders'=>0,
            'avg_revenue'=>0,
            'avg_profit'=>0,
            'avg_profit_perc'=>0,
            'revenue'=>0,
            'profit'=>0,
        );

        $startDate = new DateTime();
        $startDate->setDate($year, $startmonth, 1);
        $endDate= new DateTime();
        $endDate->setDate($year, $endmonth, 1);
        $endDate->modify(' +1 Month ');

        $sDate=$startDate->format('U');
        $eDate=$endDate->format('U');
        $this->db->select('count(order_id) as total_orders, avg(revenue) as avg_revenue, sum(revenue) as revenue, sum(profit) as profit',FALSE);
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where("order_date >= '".$sDate."'");
        $this->db->where("order_date < '".$eDate."'");
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();

        $total_orders=floatval($res['total_orders']);
        $profit=round(floatval($res['profit']),0);
        $revenue=round(floatval($res['revenue']),0);

        $avg_revenue=$avg_profit=$avg_profit_perc=0;
        if ($total_orders>0) {
            $avg_profit=round($profit/$total_orders,2);
            $avg_revenue=round($revenue/$total_orders,2);
            if ($revenue>0) {
                $avg_profit_perc=round($profit/$revenue*100,1);
            }
        }
        $out['total_orders']=($total_orders==0 ? '&nbsp;' : number_format($total_orders,0,'.',','));
        $out['avg_revenue']=($avg_revenue==0 ? '&nbsp;' : '$'.number_format($avg_revenue,2,'.',','));
        $out['avg_profit']=($avg_profit==0 ? '&nbsp;' : '$'.number_format($avg_profit,2,'.',','));
        $out['avg_profit_perc']=($avg_profit_perc==0 ? '&nbsp;' : number_format($avg_profit_perc,1,'.',',').'%');
        $out['profit_class']=orderProfitClass(round($avg_profit_perc),0);
        $out['profit']=($profit==0 ? '&nbsp;' : '$'.number_format($profit,0,'.',','));
        $out['revenue']=($revenue==0 ? '&nbsp;' : '$'.number_format($revenue,0,'.',','));
        // Out number
        $out['num_orders']=$total_orders;
        $out['num_avgrevenue']=$avg_revenue;
        $out['num_avgprofit']=$avg_profit;
        $out['num_avgprofitperc']=$avg_profit_perc;
        $out['num_profit']=$profit;
        $out['num_revenue']=$revenue;
        // Growth
        $growth=array(
            'order_num'=>'',
            'order_perc'=>'',
            'revenue_num'=>'',
            'revenue_perc'=>'',
            'avgrevenue_num'=>'',
            'avgrevenue_perc'=>'',
            'profit_num'=>'',
            'profit_perc'=>'',
            'avgprofit_num'=>'',
            'avgprofit_perc'=>'',
            'ave_num'=>'',
            'ave_proc'=>'',
        );
        if ($compare==0) {
            $out['growth']=$growth;
        } else {
            if ($total_orders==0) {
                if ($prvdata['num_orders']>0) {
                    $growth['order_num']=(-1)*($prvdata['num_orders']);
                    $growth['order_proc']=-100;
                }
            } else {
                // 1500 - 1000 = 500.   500/1000 = 50% growth
                if ($prvdata['num_orders']==0) {
                    $growth['order_num']=$total_orders;
                    $growth['order_proc']=100;
                } else {
                    $diff=($total_orders-$prvdata['num_orders']);
                    $growth['order_num']=$diff;
                    $growth['order_proc']=round($diff/$prvdata['num_orders']*100,0);
                }
            }
            if ($revenue==0) {
                if ($prvdata['num_revenue']>0) {
                    $growth['revenue_num']=(-1)*($prvdata['num_revenue']);
                    $growth['revenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_revenue']==0) {
                    $growth['revenue_num']=$revenue;
                    $growth['revenue_perc']=100;
                } else {
                    $diff=($revenue-$prvdata['num_revenue']);
                    $growth['revenue_num']=$diff;
                    $growth['revenue_perc']=round($diff/$prvdata['num_revenue']*100,0);
                }
            }
            if ($avg_revenue==0) {
                if ($prvdata['num_avgrevenue']>0) {
                    $growth['avgrevenue_num']=(-1)*($prvdata['num_avgrevenue']);
                    $growth['avgrevenue_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgrevenue']==0) {
                    $growth['avgrevenue_num']=$avg_revenue;
                    $growth['avgrevenue_perc']=100;
                } else {
                    $diff=($avg_revenue-$prvdata['num_avgrevenue']);
                    $growth['avgrevenue_num']=$diff;
                    $growth['avgrevenue_perc']=round($diff/$prvdata['num_avgrevenue']*100,0);
                }
            }
            if ($profit==0) {
                if ($prvdata['num_profit']>0) {
                    $growth['profit_num']=(-1)*($prvdata['num_profit']);
                    $growth['profit_perc']=-100;
                }
            } else {
                if ($prvdata['num_profit']==0) {
                    $growth['profit_num']=$profit;
                    $growth['profit_perc']=100;
                } else {
                    $diff=($profit-$prvdata['num_profit']);
                    $growth['profit_num']=$diff;
                    $growth['profit_perc']=round($diff/$prvdata['num_profit']*100,0);
                }
            }
            if ($avg_profit==0) {
                if ($prvdata['num_avgprofit']>0) {
                    $growth['avgprofit_num']=(-1)*$prvdata['num_avgprofit'];
                    $growth['avgprofit_perc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofit']==0) {
                    $growth['avgprofit_num']=$avg_profit;
                    $growth['avgprofit_perc']=100;
                } else {
                    $diff=($avg_profit-$prvdata['num_avgprofit']);
                    $growth['avgprofit_num']=$diff;
                    $growth['avgprofit_perc']=round($diff/$prvdata['num_avgprofit']*100,0);
                }
            }
            if ($avg_profit_perc==0) {
                if ($prvdata['num_avgprofitperc']) {
                    $growth['ave_num']=(-1)*$prvdata['num_avgprofitperc'];
                    $growth['ave_proc']=-100;
                }
            } else {
                if ($prvdata['num_avgprofitperc']==0) {
                    $growth['ave_num']=$avg_profit_perc;
                    $growth['ave_proc']=100;
                } else {
                    $diff=($avg_profit_perc-$prvdata['num_avgprofitperc']);
                    $growth['ave_num']=round($diff,1);
                    $growth['ave_proc']=round($diff/$prvdata['num_avgprofitperc']*100,0);
                }
            }
            $out['growth']=$growth;
        }
        return $out;
    }

    public function get_order_bydate($date, $brand) {
        $bgn=strtotime(date('Y-m-d',$date));
        $end=strtotime(date('m/d/Y',$bgn).' +1 day');
        $this->db->select('*');
        $this->db->from('ts_orders');
        $this->db->where('order_date >= ',$bgn);
        $this->db->where('order_date < ',$end);
        $this->db->where('is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->order_by('order_num');
        $result=$this->db->get()->result_array();
        $out=array();
        foreach ($result as $row) {
            $row['revenue']=($row['revenue']==0 ? '----' : '$'.number_format($row['revenue'],2,'.',','));
            $row['status']='-------';
            if (floatval($row['order_cog'])==0) {
                $row['profit_class']='projprof';
                $row['profit_perc']='PROJ';
                /* get stage of order */
                $status=$this->get_order_status($row['order_id']);
                if ($status!='') {
                    $status=substr($status,2).'_short';
                    $row['status']=$this->$status;
                }
            } else {
                $row['profit_class']=orderProfitClass($row['profit_perc']);
                $row['profit_perc']=($row['profit_perc']==0 ? '-----' : number_format($row['profit_perc'],1,'.','').'%');
            }
            $row['profit']=($row['profit']==0 ? '-----' : '$'.number_format($row['profit'],2,'.',','));
            $row['item_class']='';
            if ($row['item_id']==-3) {
                $row['item_class']='customitem';
            }
            $out[]=$row;
        }
        return $out;
    }

    private function get_order_status($order_id) {
        $this->db->select('order_proj_status');
        $this->db->from('v_order_statuses');
        $this->db->where('order_id',$order_id);
        $res=$this->db->get()->row_array();
        if (isset($res['order_proj_status'])) {
            return $res['order_proj_status'];
        } else {
            return '';
        }
    }

    public function get_profit_goaldata($year, $brand, $goal_type='TOTAL') {
        $this->db->select('*');
        $this->db->from('ts_goal_orders');
        $this->db->where('goal_year', $year);
        $this->db->where('goal_type', $goal_type);
        $this->db->where('brand', $brand);
        $goalres=$this->db->get()->row_array();
        if (!isset($goalres['goal_order_id'])) {
            // Insert New record
            $this->db->set('goal_year', $year);
            $this->db->set('goal_type', $goal_type);
            $this->db->insert('ts_goal_orders');
            $id=$this->db->insert_id();
            $goalres=array(
                'goal_order_id'=>$id,
                'goal_year'=>$year,
                'goal_orders'=>0,
                'goal_revenue'=>0,
                'goal_profit'=>0,
                'goal_type'=>$goal_type,
                'brand' => $brand,
            );
        }
        $out=array(
            'goal_order_id'=>$goalres['goal_order_id'],
            'goal_year'=>$goalres['goal_year'],
            'goal_orders'=>$goalres['goal_orders'],
            'goal_revenue'=>$goalres['goal_revenue'],
            'goal_profit'=>$goalres['goal_profit'],
            'goal_type'=>$goalres['goal_type'],
            'brand' => $goalres['brand'],
        );
        // Calc other params
        $goal_avgrevenue=$goal_avgprofit=0;
        if ($goalres['goal_orders']>0) {
            $goal_avgrevenue=($goalres['goal_revenue']/$goalres['goal_orders']);
            $goal_avgprofit=($goalres['goal_profit']/$goalres['goal_orders']);
        }
        $out['goal_avgrevenue']=($goal_avgrevenue==0 ? '&nbsp;' : '$'.number_format($goal_avgrevenue,2,'.',','));
        $out['goal_avgprofit']=($goal_avgprofit==0 ? '&nbsp;' : '$'.number_format($goal_avgprofit,2,'.',','));
        // Profit %
        $goal_avgprofit_perc=0;
        if ($goalres['goal_revenue']>0) {
            $goal_avgprofit_perc=($goalres['goal_profit']/$goalres['goal_revenue']*100);
        }
        $out['goal_avgprofit_perc']=($goal_avgprofit_perc==0 ? '&nbsp;' : number_format($goal_avgprofit_perc,1,'.',',').'%');
        $out['goal_profit_class']=orderProfitClass(round($goal_avgprofit_perc),0);
        return $out;
    }

    public function change_goal_value($data, $field, $newval) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->init_error_msg);
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
            $goal_avgrevenue='$'.number_format($goal_avgrevenue,2,'.',',');
            $goal_avgprofit=($data['goal_profit']/$data['goal_orders']);
            $goal_avgprofit='$'.number_format($goal_avgprofit,2,'.',',');
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
        $out = array('result' => $this->error_result, 'msg' => $this->init_error_msg);
        $this->db->where('goal_order_id', $data['goal_order_id']);
        $this->db->set('goal_orders', $data['goal_orders']);
        $this->db->set('goal_revenue', $data['goal_revenue']);
        $this->db->set('goal_profit', $data['goal_profit']);
        $this->db->set('brand', $data['brand']);
        $this->db->update('ts_goal_orders');
        $out['result']=$this->success_result;
        usersession('goaldata', NULL);
        return $out;
    }
    // Number of records in monitor
    public function get_count_monitor($filtr) {
        $this->db->select('count(order_id) as total_rec ',FALSE);
        $this->db->from('v_paymonitor');
        if (isset($filtr['paid'])) {
            if ($filtr['paid']==1) {
                $this->db->where('is_invoiced',0);
                $this->db->or_where('is_canceled',1);
            } elseif ($filtr['paid']==2) {
                $this->db->where('is_paid', 0);
                $this->db->where('(revenue-sum_amounts) > ',0);
                $this->db->where('is_invoiced',1);
            } elseif ($filtr['paid']==4) {
                $this->db->where('order_approved',1);
                $this->db->where('is_invoiced',0);
            }
        }
        if (isset($filtr['search']) && $filtr['search']!='') {
            $this->db->like('concat(ucase(customer_name),order_num) ',strtoupper($filtr['search']));
        }
        if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
            $this->db->where('brand', $filtr['brand']);
        }
        $res=$this->db->get()->row_array();
        return $res;
    }

    /* Totals for Open Invoice page */
    public function get_totals_monitor($brand) {
        /* Totals for grey shape */
        $empty_money='------';
        $empty_qty='---';
        $res=array(
            'sum_invoice'=>$empty_money,
            'sum_paid'=>$empty_money,
            'qty_inv'=>$empty_qty,
            'qty_paid'=>$empty_qty,
        );

        $this->db->select('count(order_id) as qty, sum(revenue) as sum_invoice',FALSE);
        $this->db->from('v_paymonitor');
        $this->db->where('is_invoiced',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $ordsum=$this->db->get()->row_array();
        $res['sum_invoice']=(floatval($ordsum['sum_invoice'])==0 ? $empty_money : '$'.number_format($ordsum['sum_invoice'],2,'.',','));
        $res['qty_inv']=$ordsum['qty'];

        $this->db->select("count(order_id) as cnt_not_paid, sum(revenue-sum_amounts) as sum_debt");
        $this->db->from("v_paymonitor");
        $this->db->where('is_paid',0);
        $this->db->where('(revenue-sum_amounts) > ',0);
        $this->db->where('sum_amounts > ',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $ordsum=$this->db->get()->row_array();
        $res['sum_paid']=(floatval($ordsum['sum_debt'])==0 ? $empty_money : '$'.number_format($ordsum['sum_debt'],2,'.',','));
        $res['qty_paid']=$ordsum['cnt_not_paid'];
        return $res;
    }

    /* Get data for payment monitor */
    public function get_paymonitor_data($filtr, $order_by, $direct, $limit, $offset, $user_id) {
        $this->load->model('user_model');
        $userdata=$this->user_model->get_user_data($user_id);

        $empty_money='------';
        $this->db->select('*');
        $this->db->from('v_paymonitor');
        if (isset($filtr['paid'])) {
            if ($filtr['paid']==1) {
                $this->db->where('(is_invoiced=0 or is_canceled=1)');
                //$this->db->or_where('is_canceled',1);
            } elseif ($filtr['paid']==2) {
                $this->db->where('is_paid', 0);
                $this->db->where('(revenue-sum_amounts) > ',0);
                $this->db->where('is_invoiced',1);
            } elseif ($filtr['paid']==4) {
                $this->db->where('order_approved',1);
                $this->db->where('is_invoiced',0);
            }
        }

        if (isset($filtr['search']) && $filtr['search']!='') {
            $this->db->like('concat(ucase(customer_name),order_num) ',strtoupper($filtr['search']));
        }
        if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
            $this->db->where('brand', $filtr['brand']);
        }
        $this->db->limit($limit, $offset);
        $this->db->order_by($order_by,$direct);
        $res=$this->db->get()->result_array();

        $out_arr=array();

        foreach ($res as $row) {
            $row['add_payment']='';
            $row['invoiced']=$row['not_invoiced']=$row['not_paid']=$empty_money;
            $row['paid_class']='';
            $row['cccheck']='&nbsp;';
            $row['invpay_class']='';
            if (floatval($row['cc_fee'])==0) {
                $row['cccheck']='<img src="/img/icons/check_symbol.png" alt="No CC FEE" title="No CC FEE"/>';
            }
            $row['chkinv']=$row['chkpaym']='';
            $row['refund']='';
            if ($row['is_invoiced']==0) {
                $row['not_invoiced']=($row['revenue']==0 ? $empty_money : '$'.number_format($row['revenue'],2,'.',','));
            } else {
                $row['chkinv']="checked='checked'";
                $row['invoiced']=($row['revenue']==0 ? $empty_money : number_format($row['revenue'],2,'.',','));
            }
            if ($row['is_paid']==1) {
                $row['chkpaym']="checked='checked'";
                $row['paid_class']='paid';
                $row['not_paid']='PAID';
            } else {
                if (floatval($row['sum_amounts'])>0) {
                    $diff=floatval($row['revenue'])-floatval($row['sum_amounts']);
                    if ($diff==0) {
                        $row['chkpaym']="checked='checked'";
                        $row['paid_class']='paid';
                        $row['not_paid']='PAID';
                    } else {
                        if ($diff<0) {
                            $row['refund']=$this->load->view('finopenivoice/refund_view',array('id'=>$row['order_id']),TRUE);
                            $row['not_paid']='-$'.number_format(abs($diff),2,'.',',');
                        } else {
                            $row['not_paid']='$'.number_format($diff,2,'.',',');
                        }
                    }
                }
            }
            if ($row['chkpaym']!='' && $row['is_invoiced']==0) {
                $row['invpay_class']='paynotinvoice';
            }
            $sign=(floatval($row['profit'])<0 ? '-' : '');
            if ($row['order_cog']=='') {
                $row['profitclass']='deepblue';
                $row['profit_percent']=$this->project_name;
            } else {
                $row['profit_percent']='';
                $row['profitclass']='';
                if ($row['revenue']!=0) {
                    $profit_perc=$row['profit']/$row['revenue']*100;
                    $row['profit_percent']=round($profit_perc,1);
                    $row['profitclass']=orderProfitClass($profit_perc);
                }
            }
            if (floatval($row['profit'])==0) {
                $row['profit']=$empty_money;
            } else {
                if ($userdata['profit_view']=='Points') {
                    $row['profit']=round($row['profit']*$this->config->item('profitpts'),0).' pts';
                } else {
                    $row['profit']=MoneyOutput($row['profit'],2);
                }
            }

            $row['revenue']=($row['revenue']==0 ? $empty_money : number_format($row['revenue'],2,'.',','));
            $row['order_date']=date('m/d/y',$row['order_date']);
            if ($row['order_approved']==1) {
                $row['approved']='<img src="/img/icons/bluestar.png" alt="Approved" class="monitorapproved" data-content="Attachments '.intval($row['cntdocs']).'"/>';
            } else {
                $row['approved']='<img src="/img/icons/whitestar.png" alt="Not approved"/>';
            }
            /* Refund button */
            //$row['refund']=()
            $out_arr[]=$row;
        }
        return $out_arr;
    }

    public function ordinvite($order_id,$is_invited) {
        $res=array('result'=>$this->error_result, 'msg'=> $this->init_error_msg);
        $this->db->set('is_invoiced',$is_invited);
        $this->db->set('update_date',time());
        $this->db->where('order_id',$order_id);
        $this->db->update('ts_orders');
        if ($this->db->affected_rows()==0) {
            $res['msg']='Order not Invited. Try late';
        } else {
            $res['result']=$this->success_result;
            $res['msg']='';
        }
        return $res;
    }

    public function orderpay($order_id,$is_paid, $brand) {
        $res=array('result'=>$this->error_result, 'msg'=> $this->init_error_msg);
        $this->db->set('is_paid',$is_paid);
        $this->db->set('update_date',time());
        if ($is_paid==0) {
            $this->db->set('paid_sum',0);
        }
        $this->db->where('order_id',$order_id);
        $this->db->update('ts_orders');
        if ($this->db->affected_rows()==0) {
            $res['msg']='Order not Paid. Try late';
        } else {
            /* Get ORDER */
            $this->db->select('*');
            $this->db->from('ts_orders');
            $this->db->where('order_id',$order_id);
            $order=$this->db->get()->row_array();
            if (!isset($order['order_id'])) {
                $res['msg']='Unknown Order';
            } else {
                $res['result']=$this->success_result;
                $empty_money='------';
                $order['invoiced']=$order['not_invoiced']=$order['not_paid']=$empty_money;
                $order['paid_class']='';
                $order['chkinv']=$order['chkpaym']='';
                $order['add_payment']='';
                if ($order['is_invoiced']==0) {
                    $order['not_invoiced']=($order['revenue']==0 ? $empty_money : '$'.number_format($order['revenue'],2,'.',','));
                } else {
                    $order['chkinv']="checked='checked'";
                    // $order['add_payment']='<div class="add_payment" id="addpayment'.$order['order_id'].'">*</div>';
                    $order['invoiced']=($order['revenue']==0 ? $empty_money : '$'.number_format($order['revenue'],2,'.',','));
                    if ($order['is_paid']==0) {
                        $sum_notpaid=floatval($order['revenue'])-floatval($order['paid_sum']);
                        $order['not_paid']=($sum_notpaid==0 ? $empty_money : number_format($sum_notpaid,2,'.',','));
                    } else {
                        $order['add_payment']='';
                        $order['chkpaym']="checked='checked'";
                        $order['paid_class']='paid';
                        $order['not_paid']='PAID';
                    }
                }
                $sign=(floatval($order['profit'])<0 ? '-' : '');
                if ($order['order_cog']=='') {
                    $order['profitclass']='deepblue';
                    $order['profit_percent']=$this->project_name;
                } else {
                    $order['profit_percent']='';
                    $order['profitclass']='';
                    if (floatval($order['revenue'])!=0) {
                        $profit_percent=$order['profit']/$order['revenue']*100;
                        $order['profit_percent']=round($profit_percent,1);
                        $order['profitclass']=$this->profit_class($profit_percent);
                    }
                }
                $order['profit']=(floatval($order['profit'])==0 ? $empty_money : $sign.'$'.number_format(abs($order['profit']),2,'.',','));
                $order['revenue']=($order['revenue']==0 ? $empty_money : number_format($order['revenue'],2,'.',','));
                $order['order_date']=($order['order_date']==0 ? '&nbsp' : date('m/d/y',$order['order_date']));
                $order['revenue']=($order['revenue']==0 ? $empty_money : number_format($order['revenue'],2,'.',','));
                if ($order['order_approved']==1) {
                    $order['approved']='<img src="/img/bluestar.png" alt="Approved"/>';
                } else {
                    $order['approved']='<img src="/img/whitestar.png" alt="Not approved"/>';
                }
                $res['order']=$order;
                $sums=$this->get_count_monitor(array('brand' => $brand));
                $res['invoice']=$sums['sum_invoice'];
                $res['paid']=$sums['sum_paid'];
                $res['qty_inv']=$sums['qty_inv'];
                $res['qty_paid']=$sums['qty_paid'];
            }
        }
        return $res;
    }

    public function get_monitor_data($order_id) {
        $empty_money='------';
        $this->db->select('o.*, batch.sum_amounts');
        $this->db->from('ts_orders o');
        $this->db->join("(select order_id, sum(batch_amount) as sum_amounts from ts_order_batches group by order_id) as batch","batch.order_id=o.order_id","left");
        $this->db->where('o.order_id',$order_id);
        $order=$this->db->get()->row_array();
        $order['add_payment']='';
        $order['cccheck']='&nbsp;';
        if (floatval($order['cc_fee'])==0) {
            $order['cccheck']='<img src="/img/icons/check_symbol.png" alt="No CC FEE" title="No CC FEE"/>';
        }
        $order['not_invoiced']=$order['invoiced']=$order['not_paid']=$empty_money;
        $order['paid_class']=$order['chkinv']=$order['chkpaym']='';
        $order['invpay_class']='';
        if ($order['is_invoiced']==1) {
            $order['invoiced']=($order['revenue']==0 ? '' : '$'.number_format($order['revenue'],2,'.',','));
            $order['chkinv']="checked='checked'";
        } else {
            $order['not_invoiced']=($order['revenue']==0 ? '' : '$'.number_format($order['revenue'],2,'.',','));
        }
        if ($order['is_paid']==1) {
            $order['chkpaym']="checked='checked'";
            $order['paid_class']='paid';
            $order['not_paid']='PAID';
        } else {
            $diff=floatval($order['revenue'])-floatval($order['sum_amounts']);
            if ($diff==0) {
                $order['chkpaym']="checked='checked'";
                $order['paid_class']='paid';
                $order['not_paid']='PAID';
            } else {
                $order['not_paid']='$'.number_format($diff,2,'.',',');
            }
        }
        if ($order['chkpaym']!='' && $order['is_invoiced']==0) {
            $order['invpay_class']='paynotinvoice';
        }
        $sign=(floatval($order['profit'])<0 ? '-' : '');
        if ($order['order_cog']=='') {
            $order['profitclass']='deepblue';
            $order['profit_percent']=$this->project_name;
        } else {
            $order['profit_percent']='';
            $order['profitclass']='';
            if (floatval($order['revenue'])!=0) {
                $profit_percent=$order['profit']/$order['revenue']*100;
                $order['profit_percent']=round($profit_percent,1);
                $order['profitclass']=orderProfitClass($profit_percent);
            }
        }
        $order['profit']=(floatval($order['profit'])==0 ? $empty_money : $sign.'$'.number_format(abs($order['profit']),2,'.',','));
        $order['revenue']=($order['revenue']==0 ? $empty_money : number_format($order['revenue'],2,'.',','));
        $order['order_date']=($order['order_date']==0 ? '&nbsp' : date('m/d/y',$order['order_date']));
        if ($order['order_approved']==1) {
            $order['approved']='<img src="/img/icons/bluestar.png" alt="Approved"/>';
        } else {
            $order['approved']='<img src="/img/icons/whitestar.png" alt="Not approved"/>';
        }
        return $order;
    }

    function save_custompayment($order_id,$paid_sum, $brand) {
        $res=array('result'=>$this->error_result , 'msg'=>  $this->init_error_msg);
        if (!$order_id) {
            $res['msg']='Unknown Order';
        } else {
            $this->db->select('*');
            $this->db->from('ts_orders');
            $this->db->where('order_id',$order_id);
            $order=$this->db->get()->row_array();
            if (floatval($order['revenue'])<floatval($paid_sum)) {
                $res['msg']='You try to pay more then Order revenue';
            } else {
                $this->db->set('paid_sum',floatval($paid_sum));
                $this->db->set('update_date',time());
                if (floatval($order['revenue'])==floatval($paid_sum)) {
                    $this->db->set('is_paid',1);
                }
                $this->db->where('order_id',$order_id);
                $this->db->update('ts_orders');
                $res['msg']='';
                $res['result']=$this->success_result;
                $sums=$this->get_count_monitor(array('brand' => $brand));
                $res['invoice']=$sums['sum_invoice'];
                $res['paid']=$sums['sum_paid'];
            }
        }
        return $res;
    }

    public function update_ordernote($order_id,$order_note) {
        $this->db->set('order_note',$order_note);
        $this->db->set('update_date',time());
        $this->db->where('order_id',$order_id);
        $this->db->update('ts_orders');
        return TRUE;
    }

    function order_data_profit($order) {
        $empty_money='------';
        $order['invoiced']=$order['not_invoiced']=$order['not_paid']=$empty_money;
        $order['paid_class']='';
        $order['chkinv']=$order['chkpaym']='';
        $order['add_payment']='';
        if ($order['is_invoiced']==0) {
            $order['not_invoiced']=($order['revenue']==0 ? $empty_money : '$'.number_format($order['revenue'],2,'.',','));
        } else {
            $order['chkinv']="checked='checked'";
            $order['add_payment']='<div class="add_payment" id="addpayment'.$order['order_id'].'">*</div>';

            $order['invoiced']=($order['revenue']==0 ? $empty_money : number_format($order['revenue'],2,'.',','));
            if ($order['is_paid']==0) {
                $sum_notpaid=floatval($order['revenue'])-floatval($order['paid_sum']);
                $order['not_paid']=($sum_notpaid==0 ? $empty_money : number_format($sum_notpaid,2,'.',','));
            } else {
                $order['add_payment']='';
                $order['chkpaym']="checked='checked'";
                $order['paid_class']='paid';
                $order['not_paid']='PAID';
            }
        }
        $sign=(floatval($order['profit'])<0 ? '-' : '');
        if ($order['order_cog']=='') {
            $order['profitclass']='deepblue';
            $order['profit_percent']=$this->project_name;
        } else {
            $order['profit_percent']=($order['revenue']==0 ? '' : round($order['profit']/$order['revenue']*100,1));
            $order['profitclass']=$this->morder->profit_class($order['profit_percent']);
        }
        $order['profit']=(floatval($order['profit'])==0 ? $empty_money : $sign.'$'.number_format(abs($order['profit']),2,'.',','));
        $order['revenue']=($order['revenue']==0 ? $empty_money : number_format($order['revenue'],2,'.',','));
        $order['order_date']=($order['order_date']==0 ? '&nbsp' : date('m/d/y',$order['order_date']));
        if ($order['order_approved']==1) {
            $order['approved']='<img src="/img/icons/bluestar.png" alt="Approved"/>';
        } else {
            $order['approved']='<img src="/img/icons/whitestar.png" alt="Not approved"/>';
        }
        $order['cccheck']='&nbsp;';
        $order['invpay_class']='';
        if (floatval($order['cc_fee'])==0) {
            $order['cccheck']='<img src="/img/icons/check_symbol.png" alt="No CC FEE" title="No CC FEE"/>';
        }
        if ($order['chkpaym']!='' && $order['is_invoiced']==0) {
            $order['invpay_class']='paynotinvoice';
        }
        return $order;
    }

    public function get_order_artattachs($order_id) {
        $this->db->select('ad.artwork_proof_id as orderdoc_id, a.order_id, ad.approved_time as upd_time, proof_name as doc_link');
        $this->db->from('ts_artwork_proofs ad');
        $this->db->join('ts_artworks a','a.artwork_id=ad.artwork_id');
        $this->db->where('a.order_id',$order_id);
        $this->db->where('ad.approved',1);
        $res=$this->db->get()->result_array();
        $out=array();
        $path_file=$this->config->item('artwork_proofs_relative');
        foreach ($res as $row) {
            $row['upd_time']=date('m/d/Y H:i:s',  strtotime($row['upd_time']));
            $row['doc_name']=str_replace($path_file,'',$row['doc_link']);
            $out[]=$row;
        }
        $this->db->select('*');
        $this->db->from('ts_order_docs');
        $this->db->where('order_id',$order_id);
        $docres=$this->db->get()->result_array();
        foreach ($docres as $row) {
            $row['upd_time']=date('m/d/Y H:i:s',  strtotime($row['upd_time']));
            $out[]=$row;
        }
        return $out;
    }

}
