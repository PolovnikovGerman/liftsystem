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

    // Get Total number of Orders of Year
    public function orders_total_year($year) {
        $start=strtotime($year.'-01-01');
        $this->db->select("count(order_id) as total",FALSE);
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where('order_date >= ',$start);
        $res=$this->db->get()->row_array();
        return $res['total'];
    }

    // Min order create date
    public function get_order_mindate($brand) {
        $this->db->select('min(create_date) as mindate');
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res=$this->db->get()->row_array();
        return $res['mindate'];
    }

    public function get_orders_weekleadreport($options) {
        $this->db->select("date_format(from_unixtime(o.order_date),'%m/%d/%Y') as day, count(o.order_id) as cnt, sum(o.revenue) as revenue",FALSE);
        $this->db->select('sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        if (isset($options['user_id'])) {
            $this->db->where('o.order_usr_repic', $options['user_id']);
        }
        $this->db->where('o.order_date >= ', $options['start']);
        $this->db->where('o.order_date <= ', $options['end']);
        $this->db->where('o.is_canceled',0);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('o.brand', $options['brand']);
        }
        $this->db->group_by('day');
        $res=$this->db->get()->result_array();
        return $res;
    }

    // Get Orders, created by weeks
    public function get_orders_leadreport($options=array()) {
        $this->db->select("date_format(from_unixtime(o.order_date),'%x-%v') as week, count(o.order_id) as cnt, sum(o.revenue) as revenue",FALSE);
        $this->db->select('sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        if (isset($options['user_id'])) {
            $this->db->where('o.order_usr_repic', $options['user_id']);
        }
        if (isset($options['project'])) {
            $this->db->where('o.order_cog is null');
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('o.brand', $options['brand']);
        }
        $this->db->where('o.is_canceled',0);
        $this->db->group_by('week');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_cmporders($bgn, $brand) {
        $end=strtotime(date('Y-m-d', strtotime('Sunday this week',$bgn)).' 23:59:59');
        $data=array();
        $emptydata='&mdash;';
        // Get data about orders per week
        $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $bgn);
        $this->db->where('o.order_date <= ', $end);
        $this->db->where('o.is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $totals=$this->db->get()->row_array();
        $points=round($totals['profit']*$this->config->item('profitpts'),0);
        $totals['points']=($points==0 ? $emptydata : $points);
        $totals['revenue']=($totals['revenue']==0 ? $emptydata : '$'.number_format($totals['revenue'],2,'.',','));
        $totals['cnt']=($totals['cnt']==0 ? $emptydata : $totals['cnt']);
        $data['totals']=$totals;
        $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $bgn);
        $this->db->where('o.order_date <= ', $end);
        $this->db->where('o.item_id = ', $this->config->item('custom_id'));
        $this->db->where('o.is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $customs=$this->db->get()->row_array();
        $points=round($customs['profit']*$this->config->item('profitpts'),0);
        $customs['points']=($points==0 ? $emptydata : $points);
        $customs['revenue']=($customs['revenue']==0 ? $emptydata : '$'.number_format($customs['revenue'],2,'.',','));
        $customs['cnt']=($customs['cnt']==0 ? $emptydata : $customs['cnt']);
        $data['customs']=$customs;

        $this->db->select('count(o.order_id) as cnt, sum(o.revenue) as revenue, sum(o.profit) as profit');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ', $bgn);
        $this->db->where('o.order_date <= ', $end);
        $this->db->where('o.item_id != ', $this->config->item('custom_id'));
        $this->db->where('o.is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $regular=$this->db->get()->row_array();
        $points=round($regular['profit']*$this->config->item('profitpts'),0);
        $regular['points']=($points==0 ? $emptydata : $points);
        $regular['revenue']=($regular['revenue']==0 ? $emptydata : '$'.number_format($regular['revenue'],2,'.',','));
        $regular['cnt']=($regular['cnt']==0 ? $emptydata : $regular['cnt']);
        $data['regular']=$regular;
        return $data;
    }

    public function get_leadorders($options) {
        $item_dbtable='sb_items';
        $this->db->select('o.order_id, o.create_usr, o.order_date, o.brand_id, o.order_num, o.customer_name, o.customer_email, o.revenue');
        $this->db->select('o.shipping,o.is_shipping, o.tax, o.cc_fee, o.order_cog, o.profit, o.profit_perc, b.brand_name, o.is_canceled');
        $this->db->select('o.reason, itm.item_name, o.item_id, o.order_items, o.order_usr_repic, o.weborder, o.order_qty, o.shipdate');
        $this->db->select('finance_order_amountsum(o.order_id) as cnt_amnt, u.user_leadname, u.user_name');
        $this->db->select('o.invoice_doc, o.invoice_send, o.order_confirmation');
        // ',FALSE);
        $this->db->from('ts_orders o');
        $this->db->join('brands b','b.brand_id=o.brand_id','left');
        $this->db->join("{$item_dbtable} as  itm",'itm.item_id=o.item_id','left');
        $this->db->join('users u','u.user_id=o.order_usr_repic','left');
        $this->db->where('o.is_canceled',0);
        if (isset($options['unassigned'])) {
            $this->db->where('o.order_usr_repic is null');
        }
        if (isset($options['weborder'])) {
            $this->db->where('o.weborder', $options['weborder']);
        }
        if (isset($options['order_usr_repic'])) {
            $this->db->where('o.order_usr_repic',$options['order_usr_repic']);
        }
        if (isset($options['search'])) {
            $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ',o.revenue) ",strtoupper($options['search']));
        }
        if (isset($options['begin'])) {
            $this->db->where('o.order_date >= ',$options['begin']);
        }
        if (isset($options['end'])) {
            $this->db->where('o.order_date <= ',$options['end']);
        }
        if (isset($options['order_qty'])) {
            $this->db->where('o.order_qty',$options['order_qty']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('o.brand', $options['brand']);
        }
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'],$options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['order_by'])) {
            if (isset($options['direct'])) {
                $this->db->order_by($options['order_by'],$options['direct']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }

        $res=$this->db->get()->result_array();
        /* Summary */
        $out_array=array();
        if (isset($options['offset'])) {
            $numpp=$options['offset']+1;
        } else {
            $numpp=1;
        }
        if (count($res)>0) {
            $ordnxt=$res[0]['order_num'];
        }

        foreach ($res as $row) {
            $row['rowclass']='';
            if ($row['order_num']!=$ordnxt) {
                $row['rowclass']='underline';
            }
            $ordnxt=intval($row['order_num'])-1;
            $row['numpp']=$numpp;
            $row['editlnk']='<a class="editclayord" data-order="'.$row['order_id'].'" href="javascript:void(0);"><img src="/img/edit.png"/></a>';
            $row['order_date']=($row['order_date']=='' ? '' : date('m/d/y',$row['order_date']));
            $row['revenue']=(intval($row['revenue'])==0 ? '-' : '$'.number_format($row['revenue'],2,'.',','));
            $row['shipping']=(intval($row['shipping'])==0 ? '-' : '$'.number_format($row['shipping'],2,'.',','));
            $row['tax']=(intval($row['tax'])==0 ? '-' : '$'.number_format($row['tax'],2,'.',','));
            $row['cc_fee_show']=($row['cc_fee']==0 ? 0 : 1);
            $row['cc_fee']=(floatval($row['cc_fee'])==0 ? '' : '$'.number_format($row['cc_fee'],2,'.',','));

            $row['profit_class']='';
            $row['proftitleclass']='';
            $row['proftitle']='';
            $row['input_ship']='&nbsp;';
            $row['input_other']='&nbsp;';
            $row['out_item']='&nbsp;';
            if ($row['order_items']) {
                $row['out_item']=$row['order_items'];
            } elseif ($row['item_name']) {
                $row['out_item']=$row['item_name'];
            }
            $row['custom_order']=($row['item_id']==$this->config->item('custom_id') ? 1 : 0);
            $row['input_ship']='<input type="checkbox" id="cship'.$row['order_id'].'" class="calcship" '.($row['is_shipping'] ? 'checked="checked"' : '').' />';
            $row['input_other']='<input type="checkbox" data-order="'.$row['order_id'].'" class="calcccfee" '.($row['cc_fee'] ?  'checked="checked"' : '' ).' title="'.($row['cc_fee']==0 ? '-' : '$'.number_format($row['cs_fee'],2,'.',',')).'" />';
            $row['points']=round($row['profit']*$this->config->item('profitpts'),0).' pts';
            $row['points_val']=round($row['profit']*$this->config->item('profitpts'),0);
            $row['profit']='$'.number_format($row['profit'],2,'.',',');
            if ($row['order_cog']=='') {
                $row['order_cog']='project';
                $row['cog_class']='projectcog';
                $row['profit_class']='projprof';
                $row['profit_perc']=$this->project_name;
                $row['add']='';
            } else {
                $row['cog_class']='';
                // $row['profit_class']=$this->profit_class($row['profit_perc']);
                $row['profit_class']=orderProfitClass($row['profit_perc']);
                if ($row['profit_perc']<$this->config->item('minimal_profitperc') && !empty($row['reason'])) {
                    $row['proftitleclass']='lowprofittitle';
                    $row['proftitle']='title="'.$row['reason'].'"';
                }
                $row['profit_perc']=number_format($row['profit_perc'],1,'.',',').'%';
                $row['order_cog']='$'.number_format($row['order_cog'],2,'.',',');
            }
            $row['user_replic']='&nbsp;';
            $row['usrreplclass']='user';
            if ($row['order_usr_repic']>0) {
                $row['user_replic']=($row['user_leadname']=='' ? $row['user_name'] : $row['user_leadname']);
            } else {
                if ($row['weborder']==1) {
                    $row['user_replic']='Website';
                    $row['usrreplclass']='website';
                }
            }
            $row['order_status']=$row['order_class']='&nbsp;';
            $out_array[]=$row;
            $numpp++;
        }
        return $out_array;
    }

    public function attempts_table_dat($brand, $d_bgn = '', $d_end = '')
    {
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

            $out_array[] = [
                'date' => $week_name,
                'mon_day' => '',
                'mon_date' => getDayOfWeek($weekend, $yearend, 1),
                'mon_ordercnt' => 0,
                'mon_attemcnt' => 0,
                'tue_day' => 0,
                'tue_date' => getDayOfWeek($weekend, $yearend, 2),
                'tue_ordercnt' => 0,
                'tue_attemcnt' => 0,
                'wed_day' => 0,
                'wed_date' => getDayOfWeek($weekend, $yearend, 3),
                'wed_ordercnt' => 0,
                'wed_attemcnt' => 0,
                'thu_day' => 0,
                'thu_date' => getDayOfWeek($weekend, $yearend, 4),
                'thu_ordercnt' => 0,
                'thu_attemcnt' => 0,
                'fri_day' => 0,
                'fri_date' => getDayOfWeek($weekend, $yearend, 5),
                'fri_ordercnt' => 0,
                'fri_attemcnt' => 0,
                'sat_day' => 0,
                'sat_date' => getDayOfWeek($weekend, $yearend, 6),
                'sat_ordercnt' => 0,
                'sat_attemcnt' => 0,
                'sun_day' => 0,
                'sun_date' => getDayOfWeek($weekend, $yearend, 7),
                'sun_ordercnt' => 0,
                'sun_attemcnt' => 0,
                'total_ordercnt' => 0,
                'total_attemcnt' => 0,
                'week_num' => $weekend,
                'year' => $yearend,
            ];

            $outidx = count($out_array) - 1;
            $this->db->select("date, sum(day_orders) as cnt_orders, sum(day_attempt) as cnt_attempt ", FALSE);
            $this->db->from('v_orderattempts');
            $this->db->where('unix_timestamp(date) >= ', $week_bgn);
            $this->db->where('unix_timestamp(date) <= ', $week_end);
            if ($brand!=='ALL') {
                $this->db->where('brand', $brand);
            }
            $this->db->group_by('date');
            $res = $this->db->get()->result_array();

            foreach ($res as $row) {
                $datrep = strtotime($row['date']);
                $weekday = date('w', $datrep);
                $day_pref = '';
                switch ($weekday) {
                    case '0':
                        /* Sun */
                        $day_pref = 'sun_';
                        break;
                    case '1':
                        /* Mon */
                        $day_pref = 'mon_';
                        break;
                    case '2' :
                        /* Tue */
                        $day_pref = 'tue_';
                        break;
                    case '3':
                        /* Wed */
                        $day_pref = 'wed_';
                        break;
                    case '4':
                        /* Thu */
                        $day_pref = 'thu_';
                        break;
                    case '5':
                        /* Thu */
                        $day_pref = 'fri_';
                        break;
                    case '6':
                        /* Sat */
                        $day_pref = 'sat_';
                        break;
                }
                if ($day_pref != '') {
                    // $out_array[$outidx][$day_pref.'day']=date('j',$datrep);
                    $out_array[$outidx][$day_pref . 'ordercnt'] = intval($row['cnt_orders']);
                    $out_array[$outidx][$day_pref . 'attemcnt'] = intval($row['cnt_attempt']) + intval($row['cnt_orders']);
                    $out_array[$outidx]['total_ordercnt'] += intval($row['cnt_orders']);
                    $out_array[$outidx]['total_attemcnt'] += intval($row['cnt_attempt']) + intval($row['cnt_orders']);
                }
            }
            /* Rebuild Date End */
            $d_end = strtotime(date("Y-m-d", $d_end) . " - 7 days");
        }

        $out = array();
        foreach ($out_array as $row) {
            $row['sun_day'] = date('j', $row['sun_date']);
            $row['sun_total'] = $row['sun_attemcnt'];
            $row['sun_success'] = ($row['sun_total'] == 0 ? '&nbsp;' : round($row['sun_ordercnt'] / $row['sun_total'] * 100, 0) . '% success');
            $row['mon_day'] = date('j', $row['mon_date']);
            $row['mon_total'] = $row['mon_attemcnt'];
            $row['mon_success'] = ($row['mon_total'] == 0 ? '&nbsp;' : round($row['mon_ordercnt'] / $row['mon_total'] * 100, 0) . '% success');
            $row['tue_day'] = date('j', $row['tue_date']);
            $row['tue_total'] = $row['tue_attemcnt'];
            $row['tue_success'] = ($row['tue_total'] == 0 ? '&nbsp;' : round($row['tue_ordercnt'] / $row['tue_total'] * 100, 0) . '% success');
            $row['wed_day'] = date('j', $row['wed_date']);
            $row['wed_total'] = $row['wed_attemcnt'];
            $row['wed_success'] = ($row['wed_total'] == 0 ? '&nbsp;' : round($row['wed_ordercnt'] / $row['wed_total'] * 100, 0) . '% success');
            $row['thu_day'] = date('j', $row['thu_date']);
            $row['thu_total'] = $row['thu_attemcnt'];
            $row['thu_success'] = ($row['thu_total'] == 0 ? '&nbsp;' : round($row['thu_ordercnt'] / $row['thu_total'] * 100, 0) . '% success');
            $row['fri_day'] = date('j', $row['fri_date']);
            $row['fri_total'] = $row['fri_attemcnt'];
            $row['fri_success'] = ($row['fri_total'] == 0 ? '&nbsp;' : round($row['fri_ordercnt'] / $row['fri_total'] * 100, 0) . '% success');
            $row['sat_day'] = date('j', $row['sat_date']);
            $row['sat_total'] = $row['sat_attemcnt'];
            $row['sat_success'] = ($row['sat_total'] == 0 ? '&nbsp;' : round($row['sat_ordercnt'] / $row['sat_total'] * 100, 0) . '% success');
            $row['total_totals'] = $row['total_attemcnt'];
            $row['total_success'] = ($row['total_totals'] == 0 ? '&nbsp;' : round($row['total_ordercnt'] / $row['total_totals'] * 100, 0) . '% success');
            $out[] = $row;
        }
        return $out;
    }

    public function get_attemts_duedate($date, $brand) {
        $start = strtotime(date('Y-m-d', $date));
        $end = strtotime(date('Y-m-d', $date) . ' 23:59:59');
        $this->db->select('order_id, unix_timestamp(order_date) as attdate, 1 as orderdat, 0 as attempt');
        $this->db->from('sb_orders');
        $this->db->where('unix_timestamp(order_date) >= ', $start);
        $this->db->where('unix_timestamp(order_date) <= ', $end);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $ordres = $this->db->get()->result_array();
        $this->db->select('cart_id as order_id, created_date as attdate, 0 as orderdat, 1 as attempt');
        $this->db->from('sb_cartdatas');
        $this->db->where('created_date >= ', $start);
        $this->db->where('created_date <= ', $end);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $attres = $this->db->get()->result_array();
        $res = array_merge($ordres, $attres);
        usort($res, function ($a, $b){
            return $a['attdate'] - $b['attdate'];
        });

        /* Union select */
        /* $sql = "select order_id, unix_timestamp(order_date) as attdate, 1 as orderdat, 0 as attempt
              from sb_orders
              where unix_timestamp(order_date) between " . $start . " and " . $end . " and is_void=0
              union
              select cart_id as order_id, created_date as attdate, 0 as orderdat, 1 as attempt
              from sb_cartdatas
              where created_date between " . $start . " and " . $end . "
              order by attdate";
        $query = $this->db->query($sql);
        $res = $query->result_array();
        */
        $outarr = array();
        foreach ($res as $row) {
            if ($row['orderdat'] == 1) {
                $orddat = $this->order_data($row['order_id']);

                $cc_card = $orddat['payment_card_type'] . ' ' . $orddat['payment_card_number'] . ' ' . $orddat['payment_card_month'] . '/' . $orddat['payment_card_year'];
                $datrow = array(
                    'attempt_id' => '',
                    'confirm' => $orddat['order_confirmation'],
                    'customer' => $orddat['contact_first_name'] . ' ' . $orddat['contact_last_name'],
                    'item' => $orddat['item'],
                    'qty' => $orddat['item_qty'],
                    'amount' => '$' . number_format($orddat['order_total'], 2, '.', ''),
                    'email' => $orddat['contact_email'],
                    'phone' => $orddat['contact_phone'],
                    'item_color' => '&nbsp;',
                    'customer_location' => '&nbsp;',
                    'cc_details' => $cc_card,
                    'last_field' => '&nbsp;',
                    'row_class' => 'orderdat',
                    'artsubm' => '&nbsp;',
                );
            } else {
                $attdat = $this->attempt_data($row['order_id']);
                $datrow = array(
                    'attempt_id' => $row['order_id'],
                    'confirm' => '&nbsp;',
                    'customer' => $attdat['customer'],
                    'item' => $attdat['item'],
                    'qty' => $attdat['item_qty'],
                    'amount' => $attdat['order_total'],
                    'email' => $attdat['email'],
                    'phone' => $attdat['phone'],
                    'item_color' => $attdat['item_color'],
                    'customer_location' => $attdat['customer_location'],
                    'cc_details' => $attdat['cc_details'],
                    'last_field' => $attdat['last_field'],
                    'row_class' => '',
                    'artsubm' => $attdat['artsubm'],
                );
            }
            $outarr[] = $datrow;
        }
        return $outarr;
    }

    private function order_data($order_id) {
        $this->db->select('ord.*, i.item_name as item, i.item_number');
        $this->db->from('sb_orders ord');
        $this->db->join('sb_items i', 'i.item_id=ord.order_item_id', 'left');
        $this->db->where('ord.order_id', $order_id);
        $res = $this->db->get()->row_array();
        return $res;
    }

    private function attempt_data($cart_id) {
        $ci = &get_instance();
        $ci->load->model('items_model', 'mitem');
        $ci->load->model('itemcolors_model', 'mcolors');
        $this->db->select('*');
        $this->db->from('sb_cartdatas');
        $this->db->where('cart_id', $cart_id);
        $dat = $this->db->get()->row_array();
        $cartdat = (isset($dat['cart']) ? $dat['cart'] : array());
        $out = array(
            'customer' => '&nbsp;',
            'item' => '&nbsp;',
            'item_qty' => '&nbsp;',
            'order_total' => '&nbsp;',
            'email' => '&nbsp;',
            'phone' => '&nbsp;',
            'item_color' => '&nbsp;',
            'customer_location' => '&nbsp;',
            'cc_details' => '&nbsp;',
            'last_field' => '&nbsp;',
            'artsubm' => '&nbsp;',
        );
        if (!empty($cartdat)) {
            $data = unserialize($cartdat);
            if (isset($data['ship_firstname']) && $data['ship_firstname'] != '') {
                $out['customer'].=$data['ship_firstname'] . ' ';
            }
            if (isset($data['ship_lastname']) && $data['ship_lastname'] != '') {
                $out['customer'].=$data['ship_lastname'] . ' ';
            }
            if (isset($data['item_id'])) {
                $itemdat = $ci->mitem->get_item($data['item_id']);
                $out['item'] = $itemdat['item_name'];
            }
            if (isset($data['sumval']) && $data['sumval']) {
                $out['item_qty'] = $data['sumval'];
            }
            if (isset($data['total']) && floatval($data['total']) != 0) {
                $out['order_total'] = '$' . number_format($data['total'], 2, '.', '');
            }
            if (isset($data['phonenum']) && $data['phonenum'] != '') {
                $out['phone'] = $data['phonenum'];
            }
            if (isset($data['emailaddr']) && $data['emailaddr'] != '') {
                $out['email'] = $data['emailaddr'];
            }
            // Additional fields
            $colors = array();
            for ($i = 1; $i < 5; $i++) {
                if (isset($data['col' . $i]) && $data['col' . $i]) {
                    $colors[] = array('color' => $data['col' . $i]);
                }
            }
            if (count($colors) == 1) {
                /* Get color value */
                $out['item_color'] = $ci->mcolors->get_colorval_item($colors[0]['color']);
            } elseif (count($colors) > 1) {
                $out['item_color'] = 'Multy';
            }
            if (isset($data['customer_location']) && $data['customer_location'] != '') {
                $out['customer_location'] = $data['customer_location'];
            }
            $cc_card = '';
            if (isset($data['cctype']) && $data['cctype'] != '') {
                $cc_card.=$data['cctype'] . ' ';
            }
            if (isset($data['ccnumber']) && $data['ccnumber']) {
                $cc_card.=$data['ccnumber'] . '';
            }
            if (isset($data['ccexpmonth']) && $data['ccexpmonth']) {
                $cc_card.=$data['ccexpmonth'] . '/';
            }
            if (isset($data['ccexpyear']) && $data['ccexpyear']) {
                $cc_card.=$data['ccexpyear'];
            }
            if ($cc_card != '') {
                $out['cc_details'] = $cc_card;
            }
            if (isset($data['last_updated_item']) && $data['last_updated_item']) {
                $lastfld = '';
                foreach ($this->cardflds as $row) {
                    if ($data['last_updated_item'] == $row['idx']) {
                        $lastfld = $row['name'];
                        break;
                    }
                }
                if ($lastfld != '') {
                    $out['last_field'] = $lastfld;
                }
            }
            $artsubmit = $this->count_artlogs($dat['session_id']);
            if ($artsubmit != 0) {
                $out['artsubm'] = '<div id="artsubmitlog' . $cart_id . '" class="artsubmitlog" href="/orders/artsubmitlog?d=' . $dat['session_id'] . '"><img src="/img/art.png"/></div>';
            }
        }

        return $out;
    }

}
