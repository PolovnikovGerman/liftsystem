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

    /* Status functions */
    function get_count_orderslimits($options=array()) {
        $this->db->select('count(order_id) as cnt');
        $this->db->from('v_order_statuses');
        if (isset($options['min_time'])) {
            $this->db->where('order_date >= ',$options['min_time']);
        }
        if (isset($options['search'])) {
            $this->db->like("concat(ucase(customer_name),' ',order_num,' ',revenue) ",strtoupper($options['search']));
        }
        if (isset($options['order_status'])) {
            $this->db->where('substr(order_proj_status,4)',$options['order_status']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    function get_orderslimits($options,$order_by,$addsort,$direct,$limit,$offset) {
        $this->db->select('*');
        $this->db->from('v_order_statuses');
        $this->db->where('order_proj_status != ','99_unknown');
        if (isset($options['min_time'])) {
            $this->db->where('order_date >= ',$options['min_time']);
        }
        if (isset($options['search'])) {
            $this->db->like("concat(ucase(customer_name),' ',order_num,' ',revenue) ",strtoupper($options['search']));
        }
        if (isset($options['order_status'])) {
            $this->db->where('substr(order_proj_status,4) ', $options['order_status']);
        }
        if (isset($options['status_type'])) {
            $this->db->where('status_type',$options['status_type']);
        }
        $this->db->where('order_cog',NULL);
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            $this->db->where('brand', $options['brand']);
        }
        $this->db->order_by($order_by, $direct);
        if ($addsort==1) {
            $this->db->order_by('specialdiff , commondiff','asc');
        } elseif ($addsort==2) {
            $this->db->order_by('order_num','asc');
        } elseif ($addsort==3) {
            $this->db->order_by('order_date','asc');
        } elseif ($addsort==4) {
            $this->db->order_by('revenue','asc');
        }
        $this->db->limit($limit,$offset);
        $res=$this->db->get()->result_array();

        $out=array();
        $curr_status='test';
        $i=0;
        foreach ($res as $row) {
            $order_out_status='';
            switch (substr($row['order_proj_status'],3)) {
                case 'notplaced':
                    $order_out_status=$this->_notplaced;
                    if ($row['update_date']==0) {
                        $diff='';
                    } else {
                        $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                    }
                    break;
                case 'notredr':
                    $order_out_status=$this->_notredr;
                    if ($row['order_art_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                        }
                    } else {
                        $diff=($row['art_day_diff']==0 ? $row['art_hour_diff'].' h' : $row['art_day_diff'].' d '.($row['art_hour_diff']-($row['art_day_diff']*24)).'h');
                    }
                    break;
                case 'notapprov':
                    $order_out_status=$this->_notapprov;
                    if ($row['order_proofed_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                        }
                    } else {
                        $diff=($row['proofed_day_diff']==0 ? $row['proofed_hour_diff'].' h' : $row['proofed_day_diff'].' d '.($row['proofed_hour_diff']-($row['proofed_day_diff']*24)).'h');
                    }
                    break;
                case 'notprof':
                    $order_out_status=$this->_notprof;
                    if ($row['order_vectorized_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                        }
                    } else {
                        $diff=($row['vectorized_day_diff']==0 ? $row['vectorized_hour_diff'].' h' : $row['vectorized_day_diff'].' d '.($row['vectorized_hour_diff']-($row['vectorized_day_diff']*24)).'h');
                    }
                    break;
                case 'notvector':
                    $order_out_status=$this->_notvector;
                    if ($row['order_redrawn_update']==0) {
                        if ($row['update_date']==0) {
                            $diff='';
                        } else {
                            $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                        }
                    } else {
                        $diff=($row['redrawn_day_diff']==0 ? $row['redrawn_hour_diff'].' h' : $row['redrawn_day_diff'].' d '.($row['redrawn_hour_diff']-($row['redrawn_day_diff']*24)).'h');
                    }
                    break;
                case 'noart':
                    $order_out_status=$this->_noart;
                    if ($row['update_date']==0) {
                        $diff='';
                    } else {
                        $diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                    }
                    break;
            }

            if ($order_out_status!='') {
                if ($curr_status!=substr($row['order_proj_status'],3)) {
                    $content=$this->load->view('fulfillment/statut_head_view',array(),TRUE);
                    $out[]=$content;
                    $curr_status=substr($row['order_proj_status'],3);
                }
                $row['order_out_status']=$order_out_status;
                $row['order_out_date']=($row['order_date']==0 ? '&nbsp;' : date('m/d/Y',$row['order_date']));
                $row['order_out_upd']=($diff=='' ? '&nbsp;' : $diff);
                $row['out_revenue']=(floatval($row['revenue'])==0 ? '&nbsp;' : '$'.number_format($row['revenue'],2,'.',','));
                $row['order_class']=($row['order_rush']==0 ? '' :'orderstatus_rush');
                $content=$this->load->view('fulfillment/statut_row_view',array('row'=>$row,'i'=>$i),TRUE);
                $out[]=$content;
                $i++;
            }
        }
        return $out;
    }

    public function count_notplaced_orders($search=array()) {
        $this->db->select('count(order_id) as cnt');
        $this->db->from('ts_orders o');
        $this->db->where('order_placedflag(o.order_id)',0);
        if (isset($search['searchpo'])) {
            $this->db->where('o.order_num', $search['searchpo']);
        }
        if (isset($search['brand']) && $search['brand']!=='ALL') {
            $this->db->where('o.brand', $search['brand']);
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_notplaced_orders($user_id, $search=array()) {
        $this->load->model('user_model');
        $usrdata=$this->user_model->get_user_data($user_id);
        $item_table='sb_items';

        // 'Stock','Domestic','Chinese'
        $this->db->select('o.order_id, o.order_num, o.order_itemnumber, o.order_items, o.profit, i.item_source, i.item_id, o.order_system');
        $this->db->from('ts_orders o');
        $this->db->join("{$item_table} i",'i.item_id=o.item_id','left');
        $this->db->where('order_placedflag(o.order_id)',0);
        if (isset($search['searchpo'])) {
            $this->db->where('o.order_num', $search['searchpo']);
        }
        if (isset($search['brand']) && $search['brand']!=='ALL') {
            $this->db->where('o.brand', $search['brand']);
        }
        $res=$this->db->get()->result_array();
        $stoks=$domastic=$chinese=array();
        foreach ($res as $row) {
            if (floatval($row['profit'])==0) {
                $row['profit']='-';
            } else {
                if ($usrdata['profit_view']=='Points') {
                    $row['profit']=round($row['profit']*$this->config->item('profitpts'),0).' pts';
                } else {
                    $row['profit']=MoneyOutput($row['profit'],2);
                }
            }
            $row['profit_perc']='TO PLACE';
            $row['potitle']=$row['order_itemnumber'].' - '.htmlspecialchars($row['order_items']);
            $row['addord']='<a class="searchbtn" href="javascript:void(0);" data-orderid="'.$row['order_id'].'"><em></em><span>Add</span><b></b></a>';
            if ($row['item_source']=='') {
                $domastic[]=$row;
            } else {
                if ($row['item_source']=='Stock') {
                    if ($row['order_system']=='new') {
                        $this->db->select('itmc.printshop_item_id');
                        $this->db->from('ts_order_itemcolors itmc');
                        $this->db->join('ts_order_items i','i.order_item_id=itmc.order_item_id');
                        $this->db->where('order_id', $row['order_id']);
                        $this->db->where('i.item_id', $row['item_id']);
                        $chkres=$this->db->get()->row_array();
                        if (!empty($chkres['printshop_item_id'])) {
                            $stoks[]=$row;
                        } else {
                            $domastic[]=$row;
                        }
                    } else {
                        $domastic[]=$row;
                    }
                } elseif($row['item_source']=='Domestic') {
                    $domastic[]=$row;
                } else {
                    $chinese[]=$row;
                }
            }
        }
        $out_array=array(
            'stock'=>$stoks,
            'domestic'=>$domastic,
            'chinese'=>$chinese,
        );
        return $out_array;
    }

    public function get_methods_edit() {
        $this->db->select('m.method_id, m.method_name, cntord.cnt');
        $this->db->from('purchase_methods m');
        $this->db->join('(select method_id, count(order_id) as cnt from ts_orders group by method_id) cntord','cntord.method_id=m.method_id','left');
        $this->db->order_by('m.method_name');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function notify_netdebtchanged($data)
    {
        $this->load->model('user_model');
        $log_options = array('olddebt' => $data['oldtotalrun'], 'newdebt' => $data['newtotalrun'], 'event' => '',);
        $msg_subj = 'For Debt changed from ' . $data['oldtotalrun'] . ' to ' . $data['newtotalrun'];
        $usrdat = $this->user_model->get_user_data($data['user_id']);
        $email_body = 'The For Debt value in the Net Profit report (' . $data['weeknum'] . ') changed value from ' . $data['olddebt'] . ' to ' . $data['newdebt'];
        $email_body .= ' at ' . date('h:i a') . ' ' . date('m/d/Y') . ' from the following event:' . PHP_EOL;
        if (isset($data['ordercancel'])) {
            $email_body .= 'Order ' . $data['orderdata']['order_num'] . ' for $' . number_format($data['orderdata']['revenue']) . ' ($' . number_format($data['orderdata']['profit'], 2, '.', ',') . ' profit) was cancelled by ' . $usrdat['user_name'];
            $log_options['event'] = 'cancel_order';
        }
        if (isset($data['orderchange'])) {
            if ($data['orderdata']['oldprofit'] == 0) {
                $email_body .= 'Order ' . $data['orderdata']['order_num'] . ' for $' . number_format($data['orderdata']['revenue']) . ' ($' . number_format($data['orderdata']['profit'], 2, '.', ',') . ' profit) was added by ' . $usrdat['user_name'];
            } else {
                // $email_body.='Order '.$data['orderdata']['order_num'].' for $'.number_format($data['orderdata']['revenue']).' ($'.number_format($data['orderdata']['profit'],2,'.',',').' profit) was changed by '.$usrdat['user_name'].'. ';
                // $email_body.='Old profit was $'.number_format($data['orderdata']['oldprofit'],2,'.',',');
                $email_body .= 'The revenue on order ' . $data['orderdata']['order_num'] . ' was changed ';
                if (isset($data['orderdata']['oldrevenue'])) {
                    $email_body .= ' from $' . number_format($data['orderdata']['oldrevenue']);
                }
                if (isset($data['orderdata']['oldprofit'])) {
                    $email_body .= ' ($' . number_format($data['orderdata']['oldprofit'], 2, '.', ',') . ' profit) ';
                }
                $email_body .= 'to $' . number_format($data['orderdata']['revenue'], 2, '.', '.') . ' ($' . number_format($data['orderdata']['profit'], 2, '.', ',') . ' profit)';
                $email_body .= 'by ' . $usrdat['user_name'] . '. ';
            }
            $log_options['event'] = 'change_order';
        }
        if (isset($data['podelete'])) {
            $email_body .= 'PO ' . $data['order_num'] . ' was deleted by ' . $usrdat['user_name'] . ' Sum $' . number_format($data['old_amount_sum'], 2, '.', '');
            $log_options['event'] = 'PO deleted';
        }
        if (isset($data['pochange'])) {
            if ($data['old_amount_sum'] == 0) {
                $email_body .= 'PO ' . $data['order_num'] . ' was added by ' . $usrdat['user_name'] . ' Sum $' . number_format($data['amount_sum'], 2, '.', '');
            } else {
                $email_body .= 'PO ' . $data['order_num'] . ' was changed from $' . number_format($data['old_amount_sum'], 2, '.', '') . ' to $' . number_format($data['amount_sum'], 2, '.', '') . ' by ' . $usrdat['user_name'];
            }
            if (isset($data['comment']) && $data['comment'] != '') {
                $email_body .= PHP_EOL . 'Reason ' . $data['comment'];
            }
            $log_options['event'] = 'PO changed';
        }
        if (isset($data['netproofdebt'])) {
            if (isset($data['profit_saved'])) {
                $email_body .= PHP_EOL . ' Saved was changed from $' . number_format($data['profit_saved']['old'], 2, '.', '') . ' to $' . number_format($data['profit_saved']['new'], 2, '.', '') . ' by ' . $usrdat['user_name'];
                $log_options['event'] = 'profit_changed';
            }
            if (isset($data['profit_owners'])) {
                $email_body .= PHP_EOL . ' For Owners was changed from $' . number_format($data['profit_owners']['old'], 2, '.', '') . ' to $' . number_format($data['profit_owners']['new'], 2, '.', '') . ' by ' . $usrdat['user_name'];
                $log_options['event'] = 'profit_changed';
            }
            if (isset($data['od2'])) {
                $email_body .= PHP_EOL . ' OD2 was changed from $' . number_format($data['od2']['old'], 2, '.', '') . ' to $' . number_format($data['od2']['new'], 2, '.', '') . ' by ' . $usrdat['user_name'];
                $log_options['event'] = 'profit_changed';
            }
        }
        $this->load->library('email');
        $config = $this->config->item('email_setup');
        $config['mailtype'] = 'text';
        $this->email->initialize($config);
        $this->email->set_newline("\r\n");
        $mailto = $this->config->item('sean_email');
        $this->email->to($mailto);
        $this->email->cc($this->config->item('sage_email'));
        $from = $this->config->item('email_notification_sender');
        $this->email->from($from);
        $this->email->subject($msg_subj);
        $this->email->message($email_body);
        $this->email->send();
        $this->email->clear(TRUE);
        /* Save to log */
        $this->db->set('old_debt', $log_options['olddebt']);
        $this->db->set('new_debt', $log_options['newdebt']);
        $this->db->set('checngelog_event', $log_options['event']);
        $this->db->set('user_id', $data['user_id']);
        $this->db->insert('netprofit_changelog');
        return TRUE;
    }

}
