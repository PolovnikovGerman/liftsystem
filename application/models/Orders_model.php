<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Orders_model extends MY_Model
{
    const START_ORDNUM=22000;
    private $INIT_ERRMSG='Unknown error. Try later';
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
    private $brand_id = 1;
    private $default_profit_perc = 40;
    private $default_ccfee = 3;

    private $art_sendlater = "I'll send it later";
    private $art_sendbefore = "I already sent it";
    private $art_repeat = 'Repeat Past Order';

    private $accrec_terms = 'Terms';
    private $accrec_willupd = 'Will Update';
    private $accrec_credit = 'Credit Card';
    private $accrec_prepay = 'Prepay';
    // Terms, Will Update, Credit Card, Prapy
    /* Start date for check email 03/28/2013 */
    protected $req_email_date = 1364421600;

    protected $init_error_msg='Unknown error. Try later';
    private $multicolor='Multiple';

    function __construct() {
        parent::__construct();
    }

    public function get_count_orders($filtr=array()) {
        $this->db->select('i.order_id, group_concat(toi.item_description) as itemdescr');
        $this->db->from('ts_order_items i');
        $this->db->join('ts_order_itemcolors toi','i.order_item_id = toi.order_item_id');
        $this->db->group_by('i.order_id');
        $itemdatesql = $this->db->get_compiled_select();

        $this->db->select('count(o.order_id) as cnt',FALSE);
        $this->db->from('ts_orders o');
        if (isset($filtr['filter']) && $filtr['filter']==9) {
            $paidsql = 'select order_id, sum(batch_amount) as batch_amount from ts_order_batches where batch_term=0 group by order_id';
            $this->db->join('('.$paidsql.') p','p.order_id=o.order_id','left');
            $this->db->where('o.is_canceled',0);
            $this->db->where('coalesce(o.revenue,0) != coalesce(p.batch_amount,0) ');
            $this->db->where('o.order_date >= ', $this->config->item('netprofit_start'));
        }
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
        if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
            $this->db->where('o.brand', $filtr['brand']);
        }
        if (isset($filtr['filter']) && $filtr['filter']==7) {
            $this->db->where('o.is_canceled',1);
        } else {
            if (isset($filtr['admin_mode']) && $filtr['admin_mode']==0) {
                $this->db->where('o.is_canceled',0);
            }
        }
        if (ifset($filtr,'exclude_quickbook',0)==1) {
            $this->db->where('o.order_system','new');
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
                $this->db->join('('.$itemdatesql.') itemdata','itemdata.order_id=o.order_id','left');
                $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(itemdata.itemdescr),ucase(o.order_itemnumber), o.revenue ) ",strtoupper($filtr['search']));
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
            $this->db->where('o.is_canceled',0);
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
            if (isset($filtr['brand']) && $filtr['brand']!=='ALL') {
                $this->db->where('o.brand', $filtr['brand']);
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
            $artlastupdat="artlastmessageview";
            $row['lastmsg']='/art/order_lastmessage?d='.$row['order_id'];
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
        $this->db->select('o.*, br.brand_name, itm.item_name as item_name, itm.vendor_id as vendor_id');
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

    // Get Order by num (template)
    public function get_orderbynum($ordernum) {
        $this->db->select('count(o.order_id) as cnt');
        $this->db->from('ts_orders o');
        $this->db->like("concat(ucase(o.customer_name),' ',ucase(coalesce(o.customer_email,'')),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(o.order_items) ) ",strtoupper($ordernum));
        $res=$this->db->get()->row_array();
        if ($res['cnt']==1) {
            $this->db->select('order_id');
            $this->db->from('ts_orders o');
            $this->db->like("concat(ucase(o.customer_name),' ',ucase(coalesce(o.customer_email,'')),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(o.order_items) ) ",strtoupper($ordernum));
            $detail=$this->db->get()->row_array();
            $out=array(
                'numrec'=>1,
                'detail'=>$detail['order_id'],
            );
        } else {
            $out=array(
                'numrec'=>$res['cnt'],
                'detail'=>0,
            );
        }
        return $out;
    }

    public function get_missed_orders($brand) {
        $this->db->select("date_format(from_unixtime(order_date), '%Y') as year, count(order_id) as total",FALSE);
        $this->db->from('ts_orders');
        $this->db->where('is_canceled',0);
        $this->db->where('order_qty',0);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('year');
        $this->db->order_by('year','desc');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function update_order($options) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->init_error_msg);
        if (!isset($options['order_id']) || !isset($options['user_id'])) {
            $out['msg']='Lost Parameter for Update';
            return $out;
        }
        $this->db->where('order_id', $options['order_id']);
        $this->db->set('update_date', time());
        $this->db->set('update_usr', $options['user_id']);
        if (isset($options['order_usr_repic'])) {
            $this->db->set('order_usr_repic', $options['order_usr_repic']);
        }
        if (isset($options['weborder'])) {
            $this->db->set('weborder', $options['weborder']);
        }
        if (isset($options['unassigned'])) {
            $this->db->set('order_usr_repic', NULL);
        }
        if (isset($options['order_qty'])) {
            $this->db->set('order_qty', $options['order_qty']);
        }
        $this->db->update('ts_orders');
        $out['result'] = $this->success_result;
        return $out;
    }

    public function count_onlineorders($brand, $options=[]) {
        $this->db->select('count(order_id) as cnt');
        $this->db->from('sb_orders');
        if (isset($options['replica']) && $options['replica']) {
            $this->db->like('ucase(concat(order_rep,order_num)) ', strtoupper($options['replica']));
        }
        if (isset($options['confirm']) && $options['confirm']) {
            $this->db->like('ucase(order_confirmation) ', strtoupper($options['confirm']));
        }
        if (isset($options['customer']) && $options['customer']) {
            $this->db->like('ucase(customer_name) ', $options['customer']);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function last_order($brand) {
        $this->db->select('max(order_id) as max_order, count(order_id) as cnt');
        $this->db->from('sb_orders');
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res = $this->db->get()->row_array();
        if ($res['cnt']==0) {
            $retval = 0;
        } else {
            $retval = $res['max_order'];
        }
        return $retval;
    }

    public function last_attempt($brand) {
        $this->db->select('max(cart_id) as max_order, count(cart_id) as cnt');
        $this->db->from('sb_cartdatas');
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $res = $this->db->get()->row_array();
        if ($res['cnt']==0) {
            $retval = 0;
        } else {
            $retval = $res['max_order'];
        }
        return $retval;
    }

    public function get_online_orders($options, $order_by, $direct, $limit, $offset, $search) {
        $this->db->select("*,sb_items.item_name as item_name", FALSE);
        $this->db->from('sb_orders');
        $this->db->join('sb_items', 'sb_items.item_id=sb_orders.order_item_id', 'left');
        foreach ($options as $key => $value) {
            $this->db->where($key, $value);
        }
        if (isset($search['replica']) && $search['replica']) {
            $this->db->like('ucase(concat(order_rep,order_num)) ', strtoupper($search['replica']));
        }
        if (isset($search['confirm']) && $search['confirm']) {
            $this->db->like('ucase(order_confirmation) ', strtoupper($search['confirm']));
        }
        if (isset($search['customer']) && $search['customer']) {
            $this->db->like('ucase(customer_name) ', strtoupper($search['customer']));
        }
        if (isset($search['brand']) && $search['brand']!=='ALL') {
            $this->db->where('sb_orders.brand', $search['brand']);
        }
        $this->db->order_by($order_by, $direct);
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get()->result_array();
        $out_arr = array();
        foreach ($result as $row) {
            $row['order_out_status']='NEW!';
            $row['order_status_class']='neworder';
            if ($row['is_void'] == 1) {
                $row['order_out_status'] = 'VOID';
                $row['order_status_class']='void';
            } elseif ($row['order_status']=='Entered') {
                $row['order_out_status'] = 'Entered';
                $row['order_status_class']='entered';
            }
            if ($row['is_virtual']) {
                $row['item_name'] = $row['virtual_item'];
            }
            // $order_amount=$row['item_qty']*$row['item_price']+$row['inprinting_price']+$row['shipping_price']+$row['tax']-$row['discount'];
            $order_amount = $row['order_total'];
            $row['order_amount'] = '';
            if (floatval($order_amount) != 0) {
                $row['order_amount'] = MoneyOutput($order_amount);
            }
            $row['order_date_show'] = date('m/d/y', strtotime($row['order_date']));
            $out_arr[] = $row;
        }
        return $out_arr;
    }

    public function order_details($filter = array()) {
        $out=['result' => $this->error_result, 'msg' => 'Unknown order'];
        $this->db->select("o.*,concat(coalesce(o.shipping_street1,''),' ',coalesce(o.shipping_street2,'')) as ship_street,sc.country_name as ship_cnt, sc.country_iso_code_2 as ship_cntcode", FALSE);
        $this->db->select("concat(coalesce(o.billing_street1,''),' ',coalesce(o.billing_street2,'')) as bil_street, bc.country_name as bil_cnt,pp.payment_card_name ", FALSE);
        $this->db->select('i.item_name as item_name, i.item_number as item_number, ss.shipping_method_name, ss.ups_code as shipping_method_code, disc.coupon_description as coupon_name');
        $this->db->from('sb_orders o');
        $this->db->join('sb_countries sc', 'sc.country_id=o.shipping_country_id', 'left');
        $this->db->join('sb_countries bc', 'bc.country_id=o.billing_country_id', 'left');
        $this->db->join('sb_payment_cards pp', 'pp.payment_card_id=o.payment_card_type', 'left');
        $this->db->join('sb_items i', 'i.item_id=o.order_item_id', 'left');
        $this->db->join('sb_shipping_methods ss', 'ss.shipping_method_id=o.shipping_method', 'left');
        $this->db->join('sb_coupons disc', 'disc.coupon_id=o.coupon_id', 'left');
        if (isset($filter['order_id']) && $filter['order_id']) {
            $this->db->where('o.order_id', $filter['order_id']);
        } elseif (isset($filter['order_confirmation'])) {
            $this->db->where('o.order_confirmation', $filter['order_confirmation']);
        }
        $res = $this->db->get()->row_array();

        if (isset($res['order_id'])) {
            /* Handle results */
            $this->db->select('*');
            $this->db->from('sb_order_colors');
            $this->db->where('order_color_orderid', $res['order_id']);
            $colors = $this->db->get()->result_array();
            $res['order_colors'] = '';
            $order_itmcolors = array();
            foreach ($colors as $crow) {
                $res['order_colors'].=' ' . $crow['order_color_qty'] . ' ' . $crow['order_color_itemcolor'] . ',';
                $order_itmcolors[] = $crow;
            }
            if (strlen($res['order_colors']) > 0) {
                $res['order_colors'] = substr($res['order_colors'], 0, -1);
            }
            $res['order_itmcolors'] = $order_itmcolors;
            /* Shipping Address */
            $ship_adr = '';
            $ship_adrhtml = '';
            // $ship_adr.=' '.$res['contact_first_name'].' '.$res['contact_last_name'].PHP_EOL;
            $ship_adr.=' ' . $res['shipping_firstname'] . ' ' . $res['shipping_lastname'] . PHP_EOL;
            // $ship_adrhtml.='<p>'.$res['contact_first_name'].' '.$res['contact_last_name'].'</p>';
            $ship_adrhtml.='<p>' . $res['shipping_firstname'] . ' ' . $res['shipping_lastname'] . '</p>';
            if ($res['shipping_company'] != '' && $res['shipping_company'] != 'Company (optional)') {
                $ship_adr.=' ' . $res['shipping_company'] . PHP_EOL;
                $ship_adrhtml.='<p>' . $res['shipping_company'] . '</p>';
            }
            $ship_adr.=' ' . $res['shipping_street1'] . PHP_EOL;
            $ship_adrhtml.='<p>' . $res['shipping_street1'] . '</p>';
            if ($res['shipping_street2'] != '') {
                $ship_adr.=' ' . $res['shipping_street2'] . PHP_EOL;
                $ship_adrhtml.='<p>' . $res['shipping_street2'] . '</p>';
            }
            $ship_adr.=' ' . $res['shipping_city'] . ', ' . $res['shipping_state'] . ' ' . $res['shipping_zipcode'] . PHP_EOL;
            $ship_adrhtml.='<p>' . $res['shipping_city'] . ', ' . $res['shipping_state'] . ' ' . $res['shipping_zipcode'] . '</p>';
            if (!empty($res['ship_cnt']) && $res['ship_cnt'] != 'United States') {
                $ship_adr.=' ' . $res['ship_cnt'] . PHP_EOL;
                $ship_adrhtml.='<p>' . $res['ship_cnt'] . '</p>';
            }
            $res['shipping_address'] = $ship_adr;
            $res['shipping_address_html'] = $ship_adrhtml;
            /* Billing Address */
            $bil_adr = '';
            $bil_adrhtml = '';
            $bil_adr.=' ' . $res['contact_first_name'] . ' ' . $res['contact_last_name'] . PHP_EOL;
            $bil_adrhtml.='<p>' . $res['contact_first_name'] . ' ' . $res['contact_last_name'] . '</p>';
            if ($res['customer_company'] != '') {
                $bil_adr.=' ' . $res['customer_company'] . PHP_EOL;
                $bil_adrhtml.='<p>' . $res['customer_company'] . '</p>';
            }
            $bil_adr.=' ' . $res['billing_street1'] . PHP_EOL;
            $bil_adrhtml.='<p>' . $res['billing_street1'] . '</p>';
            if ($res['billing_street2']) {
                $bil_adr.=' ' . $res['billing_street2'] . PHP_EOL;
                $bil_adrhtml.='<p>' . $res['billing_street2'] . '</p>';
            }
            $bil_adr.=' ' . $res['billing_city'] . ', ' . $res['billing_state'] . ' ' . $res['billing_zipcode'] . PHP_EOL;
            $bil_adrhtml.='<p>' . $res['billing_city'] . ', ' . $res['billing_state'] . ' ' . $res['billing_zipcode'] . '</p>';
            if (!empty($res['bil_cnt']) && $res['bil_cnt'] != 'United States') {
                $bil_adr.=' ' . $res['bil_cnt'] . PHP_EOL;
                $bil_adrhtml.='<p>' . $res['bil_cnt'] . '</p>';
            }
            $res['billing_address'] = $bil_adr;
            $res['billing_address_html'] = $bil_adrhtml;
            if ($res['is_virtual']) {
                $res['item_name'] = $res['virtual_item'];
                $res['item_number'] = '';
            }
            $res['payment_exp'] = $res['payment_card_month'] . '/' . $res['payment_card_year'];
            $pure_price = round($res['item_qty'] * $res['item_price'], 2);
            $res['pure_price'] = number_format($pure_price, 2);
            $res['total'] = number_format($res['order_total'], 2);
            /* Get Order num */
            $out['result'] = $this->success_result;
            $out['data'] = $res;
        }
        return $out;
    }

    public function finorder($order_date) {
        /* Day BGN */
        $d_bgn = strtotime(date('m/d/Y', strtotime($order_date)));
        /* End of DAY */
        $d_end = strtotime(date('Y-m-d', $d_bgn) . ' 23:59:59');
        $this->db->select('order_id, order_num, customer_name, revenue');
        $this->db->from('ts_orders');
        $this->db->where('order_date >= ', $d_bgn);
        $this->db->where('order_date <= ', $d_end);
        $this->db->order_by('order_num', 'desc');
        $orddat = $this->db->get()->result_array();
        $out = array();
        $out[] = array(
            'order_id' => 0,
            'order_num' => $this->max_finorder_num() . ' NEW',
        );
        foreach ($orddat as $row) {
            if (!$this->grey_order_exist($row['order_num'])) {
                $out[] = array(
                    'order_id' => $row['order_id'],
                    'order_num' => $row['order_num'] . ' ' . $row['customer_name'] . ' $' . number_format($row['revenue'], 2, '.', ''),
                );
            }
        }
        return $out;
    }

    public function grey_order_exist($order_num) {
        $this->db->select('count(order_id) as cnt');
        $this->db->from('sb_orders');
        $this->db->where('order_num', $order_num);
        $res = $this->db->get()->row_array();
        if ($res['cnt'] == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function max_finorder_num() {
        $dbname = 'ts_orders';
        $this->db->select('max(order_num) as maxnum');
        $this->db->from($dbname);
        $res = $this->db->get()->row_array();
        if (!isset($res['maxnum'])) {
            $maxnum = 1;
        } else {
            $maxnum = $res['maxnum'] + 1;
        }
        return $maxnum;
    }


    public function get_online_artwork($order_id) {
        $this->db->select("*");
        $this->db->from('sb_order_artworks');
        $this->db->where('order_artwork_orderid', $order_id);
        $this->db->order_by('order_artwork_id');
        $result = $this->db->get()->result_array();
        $order_art = array();

        foreach ($result as $row) {
            if (isset($row['order_artwork_face']) && $row['order_artwork_face']) {
                $facename = str_replace('/uploads/faces/', '', $row['order_artwork_face']);
                $facename = str_replace('.png', '', $facename);
                if ($row['order_artwork_note'] == '') {
                    $row['order_artwork_note'] = 'Face ' . $facename;
                } else {
                    $row['order_artwork_note'].='<br/>Face ' . $facename;
                }
            }
            $this->db->select('order_userlogo_file, order_userlogo_filename');
            $this->db->from('sb_order_userlogos');
            $this->db->where('order_userlogo_artworkid', $row['order_artwork_id']);
            $row['users_logo'] = $this->db->get()->result_array();
            $order_art[] = $row;
        }

        return $order_art;
    }


    public function get_nonassignorders() {
        $this->db->select('order_id, order_id, order_date, order_num, customer_name, order_items, revenue');
        $this->db->from('ts_orders');
        $this->db->where('order_cog',NULL);
        $this->db->where('order_art',0);
        $this->db->where('order_arthide',0);
        $this->db->where('is_canceled',0);
        $this->db->order_by('order_num');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            $row['total']=(floatval($row['revenue'])==0 ? '---' : '$'.number_format($row['revenue'],2,'.',''));
            $row['item']=$row['order_items'];
            $out[]=$row;
        }
        return $out;
    }

    public function get_newitemdat($item_id) {
        $this->db->select('item_id, item_name, item_number');
        $this->db->from('v_itemsearch');
        $this->db->where('item_id',$item_id);
        $res=$this->db->get()->row_array();
        if ($item_id>0 && count($res)>0) {
            // Get Colors
            $this->db->select('item_color as colors');
            $this->db->from('sb_item_colors');
            $this->db->where('item_color_itemid', $item_id);
            $colors=$this->db->get()->result_array();

            $res['num_colors']=count($colors);
            if (count($colors)>0) {
                $newcolor=array();
                foreach ($colors as $row) {
                    array_push($newcolor, $row['colors']);
                }
            } else {
                $newcolor=array();
            }
            $res['colors']=$newcolor;

        } else {
            $res['colors']=array();
            $res['num_colors']=0;
        }
        return $res;
    }

    /* List of Items */
    public function get_item_list($options=array()) {
        $this->db->select('v.*');
        $this->db->from('v_itemsearch v');
        if (isset($options['exclude'])) {
            $this->db->where_not_in('v.item_id', $options['exclude']);
        }
        if (isset($options['brand'])) {
            $this->db->where('(v.brand = \''.$options['brand'].'\' or v.brand=\'\' )');
        }
        $this->db->order_by('v.item_number');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            if ($row['item_id']>1) {
                $row['item_list']=$row['item_name'].' / '.$row['item_number'];
            } else {
                $row['item_list']=$row['item_name'];
            }
            $out[]=array(
                'item_id'=>$row['item_id'],
                'item_name'=>$row['item_list'],
                'itemnumber'=>$row['item_number'],
                'itemname'=>$row['item_name'],
            );
        }
        return $out;
    }

    public function get_itemdat($item_id) {
        $this->db->select('item_id, item_name, item_number');
        $this->db->from('v_itemsearch');
        $this->db->where('item_id',$item_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    function get_last_ordernum() {
        $this->db->set('order_date', time());
        $this->db->insert('ts_ordernum');
        return $this->db->insert_id();
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
        $this->db->set('brand', $data['brand']);
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
        $this->load->model('items_model');
        $this->load->model('itemcolors_model');
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
                $res = $this->items_model->get_item($data['item_id']);
                if ($res['result']==$this->success_result) {
                    $itemdat = $res['data'];
                    $out['item'] = $itemdat['item_name'];
                }
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
                $out['item_color'] = $this->itemcolors_model->get_colorval_item($colors[0]['color']);
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
                $cardflds = $this->config->item('cardflds');
                foreach ($cardflds as $row) {
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
                $out['artsubm'] = '<div id="artsubmitlog' . $cart_id . '" class="artsubmitlog" data-content="/leads/artsubmitlog?d=' . $dat['session_id'] . '"><img src="/img/lead/art.png"/></div>';
            }
        }

        return $out;
    }

    public function count_artlogs($session_id) {
        $this->db->select('count(*) as cnt');
        $this->db->from('sb_checkout_log');
        $this->db->where('session_id', $session_id);
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_artsubmitlog($session_id) {
        $this->db->select('*');
        $this->db->from('sb_checkout_log');
        $this->db->where('session_id', $session_id);
        $this->db->order_by('checkoutlog_date', 'desc');
        $res = $this->db->get()->result_array();
        return $res;
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
        $this->db->where('m.active',1);
        $this->db->order_by('m.method_name');
        $res=$this->db->get()->result_array();
        return $res;
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
        $this->db->select('order_id, count(batch_id) batchcnt, sum(batch_amount) batchsum');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_term',0);
        $this->db->group_by('order_id');
        $balancesql = $this->db->get_compiled_select();

        $this->db->select("count(o.order_num) as numorders, sum(o.order_qty) as qty, sum(o.revenue) as revenue,
            sum(o.shipping*o.is_shipping) as shipping, sum(o.tax) as tax, sum(o.cc_fee) as cc_fee,
            sum(coalesce(o.order_cog,0)) as order_cog, sum(o.profit) as profit",FALSE);
        $this->db->from("ts_orders o");
        $type_filter = intval(ifset($filtr,'filter',0));
        if ($type_filter!==7) {
            $this->db->where('o.is_canceled',0);
        }
        // $this->db->join('('.$balancesql.') p','p.order_id=o.order_id','left');
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

            if (ifset($filtr,'exclude_quickbook',0)==1) {
                $this->db->where('o.order_system','new');
            }

            if ($type_filter == 1) {
                $this->db->where('o.order_cog is null');
            } elseif ($type_filter == 2) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=40');
            } elseif ($type_filter == 3) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=30 and round(o.profit_perc,0)<40');
            } elseif ($type_filter == 4) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=20 and round(o.profit_perc,0)<30');
            } elseif ($type_filter == 5) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=10 and round(o.profit_perc,0)<20');
            } elseif ($type_filter == 6) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)<=0');
            } elseif ($type_filter == 7) {
                $this->db->where('o.is_canceled', 1);
            } elseif ($type_filter == 8) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>0 and round(o.profit_perc,0)<10');
            } elseif ($type_filter == 9) {
                $this->db->join('('.$balancesql.') p','p.order_id=o.order_id','left');
                $this->db->where('o.order_date >= ', $this->config->item('netprofit_start'));
                $this->db->where('coalesce(o.revenue,0) != coalesce(p.batchsum,0) ');
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
        $balance = $this->count_totalbalance($filtr);
        $sum_array=array(
            'numorders'=>intval($totalres['numorders']),
            'qty'=>intval($totalres['qty']),
            'revenue'=>floatval($totalres['revenue']),
            'balance' => $balance,
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
            // Balance
            $sum_array['balance_new']=$sum_array['balance_detail_new']=floatval($totals_new['balance']);
            $sum_array['balance_repeat']=$sum_array['balance_detail_repeat']=floatval($totals_repeat['balance']);
            $sum_array['balance_blank']=$sum_array['balance_detail_blank']=floatval($totals_blank['balance']);
            // Percent Revenue
            $sum_array['balance_detail_newproc']=$sum_array['balance_detail_repeatproc']=$sum_array['balance_detail_blankproc']='';
            if ($sum_array['balance']!=0) {
                $sum_array['balance_detail_newproc']=round($sum_array['balance_new']/$sum_array['balance']*100,0);
                $sum_array['balance_detail_repeatproc']=round($sum_array['balance_repeat']/$sum_array['balance']*100,0);
                $sum_array['balance_detail_blankproc']=round($sum_array['balance_blank']/$sum_array['balance']*100,0);
            }
            // Revenue
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
        $sum_array['show_balance']=($sum_array['balance']==0 ? '-' : ($sum_array['balance']<0 ? '-$'.short_number(abs($sum_array['balance'])) : '$'.short_number($sum_array['balance'])));
        $sum_array['balance']=($sum_array['balance']==0 ? '-' : MoneyOutput($sum_array['balance'],0));
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
        if (isset($filtr['filter']) && $filtr['filter']==9) {
            $this->db->select('order_id, count(batch_id) batchcnt, sum(batch_amount) batchsum');
            $this->db->from('ts_order_batches');
            $this->db->where('batch_term',0);
            $this->db->group_by('order_id');
            $paidsql = $this->db->get_compiled_select();
            $this->db->join('('.$paidsql.') p','p.order_id=o.order_id','left');
            $this->db->where('o.order_date >= ', $this->config->item('netprofit_start'));
            $this->db->where('coalesce(o.revenue,0) != coalesce(p.batchsum,0) ');
        }
        $this->db->select("count(o.order_num) as numorders, sum(o.order_qty) as qty, sum(o.revenue) as revenue,
            sum(o.shipping*o.is_shipping) as shipping, sum(o.tax) as tax, sum(o.cc_fee) as cc_fee,
            sum(coalesce(o.order_cog,0)) as order_cog, sum(o.profit) as profit",FALSE);
        $this->db->from("ts_orders o");
        if (isset($filtr['filter']) && $filtr['filter']!=7) {
            $this->db->where('o.is_canceled',0);
        }
        if (ifset($filtr,'exclude_quickbook',0)==1) {
            $this->db->where('o.order_system','new');
        }
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
        }
        $totalres=$this->db->get()->row_array();
        $totalres['balance'] = $this->count_totalbalance($filtr, $addtype);
        return $totalres;
    }

    public function get_profit_orders($filtr,$order_by,$direct,$limit,$offset, $admin_mode, $user_id) {
        $this->load->model('user_model');
        $usrdat=$this->user_model->get_user_data($user_id);
        $item_dbtable='sb_items';
        $this->db->select('order_id, count(batch_id) batchcnt, sum(batch_amount) batchsum');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_term',0);
        $this->db->group_by('order_id');
        $balancesql = $this->db->get_compiled_select();

        $this->db->select('i.order_id, group_concat(toi.item_description) as itemdescr');
        $this->db->from('ts_order_items i');
        $this->db->join('ts_order_itemcolors toi','i.order_item_id = toi.order_item_id');
        $this->db->group_by('i.order_id');
        $itemdatesql = $this->db->get_compiled_select();

        $this->db->select('o.order_id, o.create_usr, o.order_date, o.brand, o.order_num, o.customer_name, o.customer_email, o.revenue,
            o.shipping,o.is_shipping, o.tax, o.cc_fee, o.order_cog, o.profit, o.profit_perc, o.is_canceled,
            o.reason, itm.item_name, o.item_id, o.order_items, finance_order_amountsum(o.order_id) as cnt_amnt',FALSE);
        $this->db->select('o.order_blank, o.arttype');
        $this->db->select('o.order_qty, o.shipdate, o.order_confirmation');
        $this->db->select('p.batchcnt, p.batchsum, coalesce(o.revenue,0) - coalesce(p.batchsum,0) as balance ');
        $this->db->from('ts_orders o');
        // $this->db->join('brands b','b.brand_id=o.brand_id','left');
        $this->db->join("{$item_dbtable} as  itm",'itm.item_id=o.item_id','left');
        $this->db->join('('.$balancesql.') p','p.order_id=o.order_id', 'left');
        if ($admin_mode==0) {
            $this->db->where('o.is_canceled',0);
        }
        if (ifset($filtr,'exclude_quickbook',0)==1) {
            $this->db->where('o.order_system','new');
        }
        if (count($filtr)>0) {
            if (isset($filtr['search']) && $filtr['search']) {
                $this->db->join('('.$itemdatesql.') itemdata','itemdata.order_id=o.order_id','left');
                $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(itemdata.itemdescr),ucase(o.order_itemnumber), o.revenue ) ",strtoupper($filtr['search']));
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
            if (isset($filtr['filter']) && $filtr['filter']==9) {
                $this->db->having('balance != ',0);
                $this->db->where('o.order_date >= ', $this->config->item('netprofit_start'));
                $this->db->where('o.is_canceled',0);
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
            if ($row['is_canceled']==1) {
                $balance_class='';
                $balance_view='-';
            } elseif ($row['order_date']<$this->config->item('netprofit_start')) {
                $balance_class='';
                $balance_view='-';
            } else {
                $balance = $row['balance'];
                if ($balance == 0) {
                    $balance_view = 'PAID';
                    $balance_class = 'balancepaid';
                } elseif ($balance > 0) {
                    $balance_view = MoneyOutput($balance);
                    $balance_class = 'balancepositive';
                } elseif ($balance < 0 ) {
                    $balance_view = MoneyOutput(abs($balance));
                    $balance_class = 'balancenegative';
                }
            }
            $row['balance'] = $balance_view;
            $row['balance_class'] = $balance_class;
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
                $row['points']=round(floatval($row['profit'])*$this->config->item('profitpts'),0).' pts';

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
            'field_order_items', 'field_revenue', 'field_balance', 'field_shipping', 'field_tax', 'field_shipping_state', 'field_order_cog','field_profit', 'field_profit_perc',
            'field_vendor_dates', 'field_vendor_name', 'field_vendor_cog', 'field_rush_days', 'field_order_usr_repic','field_order_new'];
        $labels=['Date', 'Order#', 'Canceled', 'Customer', 'QTY', 'Colors', 'Item #',
            'Item Name', 'Revenue', 'Balance', 'Shipping','Sales Tax','Shipping States', 'COG','Profit','Profit %',
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
                if (!in_array($row,['colors','vendor_dates', 'vendor_name', 'vendor_cog','rush_days','shipping_state','order_new','balance'])) {
                    array_push($select_flds, $row);
                }
            }
            $this->db->select('o.order_id, o.is_canceled, o.order_blank, o.arttype, o.revenue');
            foreach ($select_flds as $select_fld) {
                $this->db->select("o.{$select_fld}");
            }
            $this->db->from('ts_orders o');

            if (count($search)>0) {
                if (ifset($postdata,'exclude_quickbook',0)==1) {
                    $this->db->where('o.order_system','new');
                }
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
                if (in_array('balance', $fields)) {
                    $this->db->select('count(batch_id) batchcnt, sum(batch_amount) batchsum');
                    $this->db->from('ts_order_batches');
                    $this->db->where('order_id', $row['order_id']);
                    $this->db->where('batch_term',0);
                    $balanceres = $this->db->get()->row_array();
                    $balance = $row['revenue'];
                    if ($balanceres['batchcnt']>0) {
                        $balance = $row['revenue'] - $balanceres['batchsum'];
                    }
                    $row['balance']=$balance;
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
            } elseif ($frow=='balance') {
                array_push($labels,'Balance');
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
        $this->db->query('SET SESSION sql_mode =
                  REPLACE(REPLACE(REPLACE(
                  @@sql_mode,
                  "ONLY_FULL_GROUP_BY,", ""),
                  ",ONLY_FULL_GROUP_BY", ""),
                  "ONLY_FULL_GROUP_BY", "")');
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
        $week_results[$curweek]=array('week'=>$curweek, 'profit'=>0,'orders'=>0,'profit_percent'=>0,'revenue'=>0, 'shipping' => 0,);
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
                $week_results[$curweek]=array('week'=>$curweek, 'profit'=>0,'orders'=>0,'profit_percent'=>0,'revenue'=>0, 'shipping' => 0,);
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
        $this->db->select('sum(shipping) as shipping');
        $this->db->from('ts_orders');
        // $this->db->where_in('order_date',$datsrch);
        $this->db->where('order_date >= ',$dat_month_bgn);
        $this->db->where('order_date <= ',$dat_month_end);
        $this->db->where('is_canceled',0);
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
                $week_results[$weekkey]['shipping']+=$row['shipping'];
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
            $row['shipping_class']='';
            $row['shipping_view'] = '';
            if (abs($row['shipping']) > 0) {
                $row['shipping_class'] = 'shippingdataview';
                $row['shipping_view']='<b>Shipping</b><br>'.MoneyOutput($row['shipping']).' - ('.round($row['shipping']/$row['revenue']*100,1).'%)';
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
        $out['goal_profit_class']=orderProfitClass(round($goal_avgprofit_perc,0));
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

    /* Get List of orders in PROJ stage by Period */
    public function get_projorders_netproof($datbgn, $dateend, $brand='ALL') {
        // Totals row
        $this->db->select('count(o.order_id) as cnt, sum(o.profit) as sumprof');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_date >= ',$datbgn);
        $this->db->where('o.order_date < ',$dateend);
        $this->db->where('o.is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $totalres=$this->db->get()->row_array();
        $totals=array(
            'numorders'=>0,
            'allorders'=>intval($totalres['cnt']),
            'profit'=>0,
            'revenue'=>0,
            'allprofit'=>floatval($totalres['sumprof']),
            'customorders'=>0,
            'all_qty'=>0,
        );
        // Get data about project stage
        $this->db->select('o.order_id, o.order_date, o.order_num, o.customer_name, o.item_id, o.order_items, o.revenue, o.profit, o.order_qty');
        $this->db->select('vo.order_proj_status, vo.day_diff, vo.hour_diff, vo.art_day_diff, vo.art_hour_diff, vo.redrawn_day_diff');
        $this->db->select('vo.redrawn_hour_diff, vo.vectorized_day_diff, vo.vectorized_hour_diff, vo.proofed_day_diff, vo.proofed_hour_diff');
        $this->db->select('vo.approved_day_diff, vo.approved_hour_diff');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_statuses vo','vo.order_id=o.order_id and vo.status_type="O"','left');
        $this->db->where('o.order_date >= ',$datbgn);
        $this->db->where('o.order_date < ',$dateend);
        $this->db->where('o.order_cog is NULL');
        $this->db->where('o.is_canceled',0);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        $this->db->order_by('vo.order_proj_status desc, vo.specialdiff desc, o.order_date desc');
        $res=$this->db->get()->result_array();
        $data=array();
        foreach ($res as $row) {
            $totals['numorders']+=1;
            $totals['profit']+=$row['profit'];
            $totals['revenue']+=$row['revenue'];
            $totals['all_qty']+=$row['order_qty'];
            $row['item_class']='';
            if ($row['item_id']==$this->config->item('custom_id')) {
                $totals['customorders']+=1;
                $row['item_class']='customitem';
            }
            $row['out_date']=date('m/d/y',$row['order_date']);
            $row['out_revenue']='$'.number_format($row['revenue'],2,'.',',');
            $row['out_profit']='$'.number_format($row['profit'],2,'.',',');
            $row['art_stage']=$this->_notplaced_short;
            switch ($row['order_proj_status']) {
                case $this->JUST_APPROVED :
                    $row['art_stage']=$this->_notplaced;
                    $row['diff']=$diff=($row['approved_day_diff']==0 ? $row['approved_hour_diff'].' h' : $row['approved_day_diff'].' d '.($row['approved_hour_diff']-($row['approved_day_diff']*24)).'h');
                    break;
                case $this->NEED_APPROVAL:
                    $row['art_stage']=$this->_notapprov_short;
                    $row['diff']=$diff=($row['proofed_day_diff']==0 ? $row['proofed_hour_diff'].' h' : $row['proofed_day_diff'].' d '.($row['proofed_hour_diff']-($row['proofed_day_diff']*24)).'h');
                    break;
                case $this->TO_PROOF:
                    $row['art_stage']=$this->_notprof_short;
                    $row['diff']=$diff=($row['vectorized_day_diff']==0 ? $row['vectorized_hour_diff'].' h' : $row['vectorized_day_diff'].' d '.($row['vectorized_hour_diff']-($row['vectorized_day_diff']*24)).'h');
                    break;
                case $this->NO_VECTOR:
                    $row['art_stage']=$this->_notvector_short;
                    $row['diff']=$diff=($row['redrawn_day_diff']==0 ? $row['redrawn_hour_diff'].' h' : $row['redrawn_day_diff'].' d '.($row['redrawn_hour_diff']-($row['redrawn_day_diff']*24)).'h');
                    break;
                case $this->REDRAWN:
                    $row['art_stage']=$this->_notredr_short;
                    $row['diff']=$diff=($row['art_day_diff']==0 ? $row['art_hour_diff'].' h' : $row['art_day_diff'].' d '.($row['art_hour_diff']-($row['art_day_diff']*24)).'h');
                    break;
                case $this->NO_ART:
                    $row['art_stage']=$this->_noart_short;
                    $row['diff']=$diff=($row['day_diff']==0 ? $row['hour_diff'].' h' : $row['day_diff'].' d '.($row['hour_diff']-($row['day_diff']*24)).'h');
                    break;
            }
            $data[]=$row;
        }
        // Make totals counts
        $totals['out_profit']='$'.number_format($totals['profit'],0,'.',',');
        $totals['out_revenue']='$'.number_format($totals['revenue'],0,'.',',');
        if ($totals['allorders']==0) {
            $totals['out_customer']='&mdash;';
        } else {
            $ordcntproc=round($totals['numorders']/$totals['allorders']*100,0);
            $totals['out_customer']=$totals['numorders'].' of '.$totals['allorders'].' PROJ ('.$ordcntproc.'%)';
        }
        $totals['out_artstage']=$totals['out_item']='&nbsp;';
        if ($totals['allprofit']!=0) {
            $profitperc=round($totals['profit']/$totals['allprofit']*100,0);
            $totals['out_artstage']=$totals['out_profit'].' of $'.number_format($totals['allprofit'],0,'.',',').' Profit ('.$profitperc.'%)';
        }
        if ($totals['customorders']==0) {
            $totals['out_item']=$totals['numorders'].' regular';
        } else {
            $totals['out_item']=($totals['numorders']-$totals['customorders']).' regular, '.$totals['customorders'].' customs';
        }
        $out=array(
            'data'=>$data,
            'totals'=>$totals,
        );
        return $out;
    }

    public function notify_netdebtchanged($data) {
        $this->load->model('user_model');
        $log_options=array(
            'olddebt'=>$data['oldtotalrun'],
            'newdebt'=>$data['newtotalrun'],
            'event'=>'',
        );
        $msg_subj='For Debt changed from '.$data['oldtotalrun'].' to '.$data['newtotalrun'];
        $usrdat=$this->user_model->get_user_data($data['user_id']);
        $email_body='The For Debt value in the Net Profit report ('.$data['weeknum'].') changed value from '.$data['olddebt'].' to '.$data['newdebt'];
        $email_body.=' at '.date('h:i a').' '.date('m/d/Y').' from the following event:'.PHP_EOL;
        if (isset($data['ordercancel'])) {
            $email_body.='Order '.$data['orderdata']['order_num'].' for $'.number_format($data['orderdata']['revenue']).' ($'.number_format($data['orderdata']['profit'],2,'.',',').' profit) was cancelled by '.$usrdat['user_name'];
            $log_options['event']='cancel_order';
        }
        if (isset($data['orderchange'])) {
            if ($data['orderdata']['oldprofit']==0) {
                $email_body.='Order '.$data['orderdata']['order_num'].' for $'.number_format($data['orderdata']['revenue']).' ($'.number_format($data['orderdata']['profit'],2,'.',',').' profit) was added by '.$usrdat['user_name'];
            } else {
                // $email_body.='Order '.$data['orderdata']['order_num'].' for $'.number_format($data['orderdata']['revenue']).' ($'.number_format($data['orderdata']['profit'],2,'.',',').' profit) was changed by '.$usrdat['user_name'].'. ';
                // $email_body.='Old profit was $'.number_format($data['orderdata']['oldprofit'],2,'.',',');
                $email_body.='The revenue on order '.$data['orderdata']['order_num'].' was changed ';
                if (isset($data['orderdata']['oldrevenue'])) {
                    $email_body.=' from $'.number_format($data['orderdata']['oldrevenue']);
                }
                if (isset($data['orderdata']['oldprofit'])) {
                    $email_body.=' ($'.number_format($data['orderdata']['oldprofit'],2,'.',',').' profit) ';
                }
                $email_body.='to $'.number_format($data['orderdata']['revenue'],2,'.','.').' ($'.number_format($data['orderdata']['profit'],2,'.',',').' profit)';
                $email_body.='by '.$usrdat['user_name'].'. ';
            }
            $log_options['event']='change_order';
        }
        if (isset($data['podelete'])) {
            $email_body.='PO '.$data['order_num'].' was deleted by '.$usrdat['user_name'].' Sum $'.number_format($data['old_amount_sum'],2,'.','');
            $log_options['event']='PO deleted';
        }
        if (isset($data['pochange'])) {
            if ($data['old_amount_sum']==0) {
                $email_body.='PO '.$data['order_num'].' was added by '.$usrdat['user_name'].' Sum $'.number_format($data['amount_sum'],2,'.','');
            } else {
                $email_body.='PO '.$data['order_num'].' was changed from $'.number_format($data['old_amount_sum'],2,'.','').' to $'.number_format($data['amount_sum'],2,'.','').' by '.$usrdat['user_name'];
            }
            if (isset($data['comment']) && $data['comment']!='') {
                $email_body.=PHP_EOL.'Reason '.$data['comment'];
            }
            $log_options['event']='PO changed';
        }
        if (isset($data['netproofdebt'])) {
            if (isset($data['profit_saved'])) {
                $email_body.=PHP_EOL.' Saved was changed from $'.number_format($data['profit_saved']['old'],2,'.','').' to $'.number_format($data['profit_saved']['new'],2,'.','').' by '.$usrdat['user_name'];
                $log_options['event']='profit_changed';
            }
            if (isset($data['profit_owners'])) {
                $email_body.=PHP_EOL.' For Owners was changed from $'.number_format($data['profit_owners']['old'],2,'.','').' to $'.number_format($data['profit_owners']['new'],2,'.','').' by '.$usrdat['user_name'];
                $log_options['event']='profit_changed';
            }
            if (isset($data['od2'])) {
                $email_body.=PHP_EOL.' OD2 was changed from $'.number_format($data['od2']['old'],2,'.','').' to $'.number_format($data['od2']['new'],2,'.','').' by '.$usrdat['user_name'];
                $log_options['event']='profit_changed';
            }
        }
        if (!in_array($_SERVER['SERVER_NAME'], $this->config->item('localserver'))) {
            $this->load->library('email');
            $config = $this->config->item('email_setup');
            $config['mailtype'] = 'text';
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $mailto=$this->config->item('sean_email');
            $this->email->to($mailto);
            $this->email->cc($this->config->item('sage_email'));
            $from = $this->config->item('email_notification_sender');
            $this->email->from($from);
            $this->email->subject($msg_subj);
            $this->email->message($email_body);
            $this->email->send();
            $this->email->clear(TRUE);
        }
        /* Save to log */
        $this->db->set('old_debt',$log_options['olddebt']);
        $this->db->set('new_debt',$log_options['newdebt']);
        $this->db->set('checngelog_event', $log_options['event']);
        $this->db->set('user_id',$data['user_id']);
        $this->db->insert('netprofit_changelog');
        return TRUE;
    }

    public function export_artsync() {
        $this->db->select('o.order_num, o.order_date, o.order_id, s.artdata_sync_id, s.customer, s.item_descript, s.rush, s.blank');
        $this->db->select('s.art_stage, s.redraw_stage, s.vector_stage, s.proof_stage, s.approv_stage');
        $this->db->from('ts_artdata_sync s');
        $this->db->join('ts_orders o','o.order_id=s.order_id');
        $this->db->where('s.sended',0);
        $datares=$this->db->get()->result_array();

        foreach ($datares as $row) {
            $postdata=array(
                'sync'=>'data',
                'af_order_id'=>$row['order_num'],
            );
            // Get first contact row
            $this->db->select('*');
            $this->db->from('ts_order_contacts');
            $this->db->where('order_id', $row['order_id']);
            $this->db->order_by('order_contact_id');
            $this->db->limit(1);
            $contactres=$this->db->get()->row_array();
            $note='';
            if (!empty($contactres['contact_emal'])) {
                $note.='Email:'.$contactres['contact_emal'].' ';
            }
            if (!empty($contactres['contact_phone'])) {
                $note.='Tel: '.$contactres['contact_phone'].' ';
            }
            if (!empty($contactres['contact_name'])) {
                $note.='Contact:'.addslashes($contactres['contact_name']);
            }
            if (!empty($note)) {
                $postdata['contact_info']=$note;
            }
            // Email: xxxxxxxx@xxxxxxxxxx.com   Tel: xxx-xxx-xxxx xxxx  Contact:
            if (!empty($row['customer'])) {
                $postdata['af_cust']= addslashes($row['customer']);
            }
            if (!empty($row['item_descript'])) {
                $postdata['af_desc']= addslashes($row['item_descript']);
            }
            if ($row['rush']>=0) {
                $postdata['af_rush_ck']=$row['rush'];
            }
            if ($row['blank']>=0) {
                $postdata['order_blank']=$row['blank'];
            }
            // ART Stages
            $postdata['af_art_ck']=$row['art_stage'];
            $postdata['af_redraw_ck']=$row['redraw_stage'];
            $postdata['af_vector_ck']=$row['vector_stage'];
            $postdata['af_proof_ck']=$row['proof_stage'];
            $postdata['af_appr_ck']=$row['approv_stage'];
            $postdata['order_date']=$row['order_date'];

            $curl = curl_init(); //Init
            if ($this->config->item('netexportsecure')==1) {
                curl_setopt($curl, CURLOPT_USERPWD, 'stressballs:07031');
            }
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_URL, $this->config->item('netexportdata')); //POST URL
            curl_setopt($curl, CURLOPT_HEADER, 0); // Show Headers
            curl_setopt($curl, CURLOPT_POST, 1); // Send data via POST
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //curl return response
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata); // data for send via POST
            $res = curl_exec($curl);

            // In case of Error - print error message
            if(!$res) {
                $error = curl_error($curl).'('.curl_errno($curl).')';
                echo $error;
            } else {
                $array = json_decode($res);

                if ($array->result==1) {
                    // echo 'Export '.$row['order_num'].' Success '.PHP_EOL;
                    $this->db->set('sended',1);
                    $this->db->set('sendtime', time());
                    $this->db->where('artdata_sync_id', $row['artdata_sync_id']);
                    $this->db->update('ts_artdata_sync');
                } else {
                    echo 'Error '.$array->error.PHP_EOL;
                }
            }
            curl_close($curl);
        }

        // Get documents - added to system
        $this->db->select('s.artdoc_sync_id, s.operation, o.order_num');
        $this->db->from('ts_artdoc_sync s');
        $this->db->join('ts_orders o','o.order_id=s.order_id');
        $this->db->where('s.sended',0);
        $this->db->order_by('s.artdoc_sync_id');
        $sdoc=$this->db->get()->result_array();
        foreach ($sdoc as $docrow) {
            if ($docrow['operation']=='add') {
                $this->db->select('o.order_num, p.source_name,p.proof_name, s.artdoc_sync_id');
                $this->db->from('ts_artdoc_sync s');
                $this->db->join('ts_orders o','o.order_id=s.order_id');
                $this->db->join('ts_artworks a','a.order_id=s.order_id');
                $this->db->join('ts_artwork_proofs p','p.artwork_proof_id=s.artwork_proof_id');
                $this->db->where('s.artdoc_sync_id',$docrow['artdoc_sync_id']);
                $docres=$this->db->get()->row_array();
                $postdata=array(
                    'sync'=>'doc',
                    'operation'=>'add',
                    'af_order_id'=>$docres['order_num'],
                    'source_name'=>$docres['source_name'],
                    'source_lnk'=> 'http://'.$_SERVER['SERVER_NAME'].addslashes($docres['proof_name']),
                );
            } else {
                // Get documents - delete from system
                $this->db->select('o.order_num, s.artdoc_sync_id, s.proofdoc_link');
                $this->db->from('ts_artdoc_sync s');
                $this->db->join('ts_orders o','o.order_id=s.order_id');
                $this->db->where('s.artdoc_sync_id',$docrow['artdoc_sync_id']);
                $delres=$this->db->get()->row_array();
                $postdata=array(
                    'sync'=>'doc',
                    'operation'=>'delete',
                    'af_order_id'=>$delres['order_num'],
                    'source_name'=>$delres['proofdoc_link'],
                );
            }

            $curl = curl_init(); //Init
            curl_setopt($curl, CURLOPT_USERPWD, 'stressballs:07031');
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_URL, $this->config->item('netexportdata')); //POST URL
            curl_setopt($curl, CURLOPT_HEADER, 0); // Show Headers
            curl_setopt($curl, CURLOPT_POST, 1); // Send data via POST
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //curl return response
            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata); // data for send via POST
            $res = curl_exec($curl);

            // In case of Error - print error message
            if(!$res) {
                $error = curl_error($curl).'('.curl_errno($curl).')';
                echo $error;
            } else {
                $array = json_decode($res);
                if (!is_object($array)) {
                    var_dump($postdata);
                    var_dump($res);
                } else {
                    if ($array->result==1) {
                        echo 'Export '.  strtoupper($docrow['operation']).', DOC '.$docrow['order_num'].' Success '.PHP_EOL;
                        $this->db->set('sended',1);
                        $this->db->set('sendtime', time());
                        $this->db->where('artdoc_sync_id', $docrow['artdoc_sync_id']);
                        $this->db->update('ts_artdoc_sync');
                    } else {
                        echo 'Error '.$array->error.PHP_EOL;
                    }

                }
            }
            curl_close($curl);
        }

//
//        foreach ($docres as $drow) {
//            $postdata=array(
//                'sync'=>'doc',
//                'operation'=>'add',
//                'af_order_id'=>$drow['order_num'],
//                'source_name'=>$drow['source_name'],
//                'source_lnk'=> 'http://'.$_SERVER['SERVER_NAME'].$drow['proof_name'],
//            );
//            $curl = curl_init(); //Init
//            curl_setopt($curl, CURLOPT_USERPWD, 'stressballs:07031');
//            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//            curl_setopt($curl, CURLOPT_URL, $this->config->item('netexportdata')); //POST URL
//            curl_setopt($curl, CURLOPT_HEADER, 0); // Show Headers
//            curl_setopt($curl, CURLOPT_POST, 1); // Send data via POST
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //curl return response
//            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata); // data for send via POST
//            $res = curl_exec($curl);
//            // In case of Error - print error message
//            if(!$res) {
//                $error = curl_error($curl).'('.curl_errno($curl).')';
//                echo $error;
//            } else {
//                $array = json_decode($res);
//                if ($array->result==1) {
//                    echo 'Export ADD DOC '.$drow['order_num'].' Success '.PHP_EOL;
//                    $this->db->set('sended',1);
//                    $this->db->set('sendtime', time());
//                    $this->db->where('artdoc_sync_id', $drow['artdoc_sync_id']);
//                    $this->db->update('ts_artdoc_sync');
//                } else {
//                    echo 'Error '.$array->error.PHP_EOL;
//                }
//            }
//            curl_close($curl);
//        }
//
//
//        foreach ($delres as $drow) {
//            $postdata=array(
//                'sync'=>'doc',
//                'operation'=>'delete',
//                'af_order_id'=>$drow['order_num'],
//                'source_name'=>$drow['proofdoc_link'],
//            );
//            $curl = curl_init(); //Init
//            curl_setopt($curl, CURLOPT_USERPWD, 'stressballs:07031');
//            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
//            curl_setopt($curl, CURLOPT_URL, $this->config->item('netexportdata')); //POST URL
//            curl_setopt($curl, CURLOPT_HEADER, 0); // Show Headers
//            curl_setopt($curl, CURLOPT_POST, 1); // Send data via POST
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); //curl return response
//            curl_setopt($curl, CURLOPT_POSTFIELDS, $postdata); // data for send via POST
//            $res = curl_exec($curl);
//            // In case of Error - print error message
//            if(!$res) {
//                $error = curl_error($curl).'('.curl_errno($curl).')';
//                echo $error;
//            } else {
//                $array = json_decode($res);
//                if ($array->result==1) {
//                    echo 'Export DELETE '.$drow['order_num'].' Success '.PHP_EOL;
//                    $this->db->set('sended',1);
//                    $this->db->set('sendtime', time());
//                    $this->db->where('artdoc_sync_id', $drow['artdoc_sync_id']);
//                    $this->db->update('ts_artdoc_sync');
//                } else {
//                    echo 'Error '.$array->error.PHP_EOL;
//                }
//            }
//            curl_close($curl);
//        }

    }

    public function get_week_quotes($options) {
        $out=['result'=> $this->success_result, 'msg'=> 'Start date empty'];
        if (isset($options['weekbgn']) && !empty($options['weekbgn'])) {
            $date=$options['weekbgn'];
            $out['msg']='Finish date empty';
            if (isset($options['weekend']) && !empty($options['weekend'])) {
                $weekend=$options['weekend'];
                $data=[];
                do {
                    $i=1;
                    $newdate = strtotime(date("Y-m-d", $date) . " +".$i."days");
                    // count quotes
                    $this->db->select('count(email_id) as cnt');
                    $this->db->from('ts_emails');
                    $this->db->where('unix_timestamp(email_date) >= ', $date);
                    $this->db->where('unix_timestamp(email_date) < ', $newdate);
                    $this->db->where('email_type','Leads');
                    $this->db->where('email_subtype','Quote');
                    $this->db->where('email_status != ',4);
                    $this->db->where('brand', $options['brand']);
                    $quotes=$this->db->get()->row_array();
                    // count proof requests
                    $this->db->select('count(email_id) as cnt');
                    $this->db->from('ts_emails');
                    $this->db->where('unix_timestamp(email_date) >= ', $date);
                    $this->db->where('unix_timestamp(email_date) < ', $newdate);
                    $this->db->where('email_type','Art_Submit');
                    $this->db->where('email_status != ',4);
                    $this->db->where('brand', $options['brand']);
                    $proofreq=$this->db->get()->row_array();
                    // count orders
                    $this->db->select('count(order_id) as cnt');
                    $this->db->from('ts_orders');
                    $this->db->where('order_date >= ', $date);
                    $this->db->where('order_date < ', $newdate);
                    $this->db->where('brand', $options['brand']);
                    $this->db->where('is_canceled',0);
                    $orders=$this->db->get()->row_array();
                    if ($orders['cnt']!=0 || $quotes['cnt']!=0 || $proofreq['cnt']!=0) {
                        $data[]=[
                            'date'=>date('m/d/Y',$date),
                            'quotes'=>$quotes['cnt'],
                            'proofreq'=>$proofreq['cnt'],
                            'orders'=>$orders['cnt'],
                        ];
                    }
                    $date=$newdate;
                } while ($newdate<$weekend);
                $out['data']=$data;
                $out['result']= $this->success_result;
            }
        }
        return $out;
    }

    public function user_weekproof_reportdata($user_id, $brand) {
        $cur_year=date('Y');
        $datebonusbgn=$this->config->item('bonus_time');
        // Get start
        $this->db->select('datebgn, dateend, profit_week');
        $this->db->from('netprofit');
        $this->db->where('profit_year', $cur_year);
        $this->db->where('profit_week is not NULL');
        $this->db->order_by('datebgn desc');
        $weekres=$this->db->get()->result_array();
        $out=[];
        $total=[
            'orders_500'=>0,
            'orders_1000'=>0,
            'orders_1200'=>0,
            'total_500'=>0,
            'total_1000'=>0,
            'total_1200'=>0,
            'total_orders'=>0,
            'week_base'=>0,
            'bonuses'=>0,
            'prize'=>0,
            'week_pay'=>0,
            'cancel_orders'=>0,
            'cancel_points'=>0,
            'cancel_values'=>0,
        ];
        foreach ($weekres as $wrow) {
            $this->db->select('order_usr_repic, order_qty');
            $this->db->from('ts_orders');
            $this->db->where('order_date >= ', $wrow['datebgn']);
            $this->db->where('order_date < ', $wrow['dateend']);
            $this->db->where('is_canceled',0);
            $this->db->where('brand', $brand);
            $this->db->where('order_usr_repic', $user_id);
            $res=$this->db->get()->result_array();
            $ord_500=$ord_1000=$ord_1200=0;
            foreach ($res as $row) {
                if ($row['order_qty']<500) {
                    $ord_500+=1;
                } elseif ($row['order_qty']<1000) {
                    $ord_1000+=1;
                } else {
                    $ord_1200+=1;
                }
            }
            // Get canceled
            $this->db->select('order_usr_repic, order_qty');
            $this->db->from('ts_orders');
            $this->db->where('order_date >= ', $wrow['datebgn']);
            $this->db->where('order_date < ', $wrow['dateend']);
            $this->db->where('is_canceled',1);
            $this->db->where('brand', $brand);
            $this->db->where('order_usr_repic', $user_id);
            $cancres=$this->db->get()->result_array();
            $canc_500=$canc_1000=$canc_1200=0;
            foreach ($cancres as $row) {
                if ($row['order_qty']<500) {
                    $canc_500+=1;
                } elseif ($row['order_qty']<1000) {
                    $canc_1000+=1;
                } else {
                    $canc_1200+=1;
                }
            }

            $week_bonuses=($ord_500*$this->config->item('bonus_500')+$ord_1000*$this->config->item('bonus_1000')+$ord_1200*$this->config->item('bonus_1200'));
            $cancel_bonuses=($canc_500*$this->config->item('bonus_500')+$canc_1000*$this->config->item('bonus_1000')+$canc_1200*$this->config->item('bonus_1200'));
            $week_prize=$week_bonuses*$this->config->item('bonus_price');
            $cancel_values=$cancel_bonuses*$this->config->item('bonus_price');
            $weekbgn_month=date('m', $wrow['datebgn']);
            $weekend_month=date('m', $wrow['dateend']);
            if ($weekbgn_month!=$weekend_month) {
                $week_name=date('M',$wrow['datebgn']).'/'.date('M', $wrow['dateend']).' ';
            } else {
                $week_name=date('M', $wrow['datebgn']).' ';
            }
            $week_name.=date('d',$wrow['datebgn']).'-'.date('d',$wrow['dateend']).','.$cur_year;
            $out[]=[
                'dates'=>$week_name,
                'orders_500'=>$ord_500,
                'orders_1000'=>$ord_1000,
                'orders_1200'=>$ord_1200,
                'total_500'=>$ord_500*$this->config->item('bonus_500'),
                'total_1000'=>$ord_1000*$this->config->item('bonus_1000'),
                'total_1200'=>$ord_1200*$this->config->item('bonus_1200'),
                'total_orders'=>($ord_500+$ord_1000+$ord_1200),
                'week_base'=>$this->config->item('bonus_week_base'),
                'bonuses'=>$week_bonuses,
                'prize'=>$week_prize,
                'week_pay'=>$week_prize+$this->config->item('bonus_week_base'),
                'cancel_orders'=>($canc_500+$canc_1000+$canc_1200),
                'cancel_points'=>$cancel_bonuses,
                'cancel_values'=>$cancel_values,
                'show_user'=>($wrow['datebgn']>=$datebonusbgn ? 1 : 0),
                'admin_break'=>($wrow['datebgn']==$datebonusbgn ? 1 : 0),
            ];
            if ($wrow['datebgn']>=$datebonusbgn) {
                $total['orders_500']+=$ord_500;
                $total['orders_1000']+=$ord_1000;
                $total['orders_1200']+=$ord_1200;
                $total['total_500']+=$ord_500*$this->config->item('bonus_500');
                $total['total_1000']+=$ord_1000*$this->config->item('bonus_1000');
                $total['total_1200']+=$ord_1200*$this->config->item('bonus_1200');
                $total['total_orders']+=($ord_500+$ord_1000+$ord_1200);
                $total['week_base']+=$this->config->item('bonus_week_base');
                $total['bonuses']+=$week_bonuses;
                $total['prize']+=$week_prize;
                $total['week_pay']+=$week_prize+$this->config->item('bonus_week_base');
                $total['cancel_orders']+=($canc_500+$canc_1000+$canc_1200);
                $total['cancel_points']+=$cancel_bonuses;
                $total['cancel_values']+=$cancel_values;
            }
        }
        return ['out'=>$out, 'totals'=>$total];
    }

    public function orderdiscount_msg($start_time, $end_time, $brand) {

        $event = ['Misc Charge','Misc Charge (row 1)','Misc Charge (row 2)','Discount Value','Order Item Cost','Order Item Imprint Cost'];
        $this->db->select('o.order_num, u.first_name, u.last_name, mischrg_label1, mischrg_label2, discount_descript');
        $this->db->select('hd.parameter_name, hd.parameter_oldvalue, hd.parameter_newvalue');
        $this->db->from('ts_artwork_historydetails hd');
        $this->db->join('ts_artwork_history h','h.artwork_history_id=hd.artwork_history_id');
        $this->db->join('ts_artworks a','a.artwork_id=h.artwork_id');
        $this->db->join('ts_orders o','o.order_id=a.order_id');
        $this->db->join('users u','u.user_id=h.user_id');
        $this->db->where('h.created_time >= ', $start_time);
        $this->db->where('h.created_time < ', $end_time);
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.brand', $brand);
        $this->db->order_by('o.order_num, h.created_time');
        $res = $this->db->get()->result_array();
        $out=[];
        foreach ($res as $row) {
            if (in_array($row['parameter_name'], $event)) {
                if ($row['parameter_name']=='Order Item Cost' || $row['parameter_name']=='Order Item Imprint Cost') {
                    $out[]=[
                        'user' => $row['first_name'].' '.$row['last_name'],
                        'order' => $row['order_num'],
                        'parameter' => $row['parameter_name'],
                        'old_value' => $row['parameter_oldvalue'],
                        'new_value' => $row['parameter_newvalue'],
                        'description' => '',
                    ];
                } else {
                    $descr = $row['mischrg_label1'];
                    if ($row['parameter_name']=='Misc Charge (row 2)') {
                        $descr = $row['mischrg_label2'];
                    } elseif ($row['parameter_name']=='Discount Value') {
                        $descr = $row['discount_descript'];
                    }
                }
                $out[]=[
                    'user' => $row['first_name'].' '.$row['last_name'],
                    'order' => $row['order_num'],
                    'parameter' => $row['parameter_name'],
                    'old_value' => $row['parameter_oldvalue'],
                    'new_value' => $row['parameter_newvalue'],
                    'description' => $descr,
                ];
            }
        }
        return $out;
    }

    public function order_autoparse() {
        $datemin=new DateTime();
        $datemin->modify("-5 min");
        $this->db->select('order_id, order_confirmation,order_type');
        $this->db->from('sb_orders');
        $this->db->where('order_num is null');
        $this->db->where('is_void', 0);
        $this->db->where('unix_timestamp(order_date) <= ', $datemin->format('U'));
        $this->db->order_by('order_id');
        $this->db->limit(10);
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            if ($row['order_type']=='NEW') {
                $this->parse_new_ordertype($row['order_id']);
                echo 'NEW '.$row['order_confirmation'].PHP_EOL;
            } else {
                $this->parse_old_ordertype($row['order_id']);
                echo 'OLD '.$row['order_confirmation'].PHP_EOL;
            }
        }
    }

    public function parse_new_ordertype($order_id) {
        // Get order Data
        $defrepl = 'XX';
        $res = $this->weborder_details($order_id);
        if ($res['result']==$this->success_result) {
            $data = $res['data'];
            $orderdata = $data['order'];
            $order_items = $data['items'];
            // Get a related items
            $multyorders = 0;
            if (count($order_items)>1) {
                $multyorders = 1;
            }
            $numpp = 1;
            foreach ($order_items as $order_item) {
                $confirmation = $orderdata['order_confirmation'];
                if ($multyorders==1) {
                    $confirmation.= '-'.$numpp;
                }
                $ordnew = $this->add_liftorder($orderdata, $order_item, $confirmation);
                if ($ordnew['order_id'] == 0) {
                    // Log about Error
                } else {
                    $artsync=$ordnew['artsync'];
                    $order_num=$ordnew['order_num'];
                    // Change Current Order
                    $this->db->set('order_rep', $defrepl);
                    $this->db->set('order_num', $order_num);
                    $this->db->set('order_status', 'Entered');
                    $this->db->where('order_id', $order_id);
                    $this->db->update('sb_orders');
                    // Brown Order ID
                    $brownord = $ordnew['order_id'];
                    // Check ART Work
                    $artw = $this->check_finart($brownord);
                    // ARTS
                    if ($artw == 0) {
                        // Add Artwork & artworkor_arts
                        $rushval = (($order_item['production_term'] == 'Standard' || $order_item['production_term'] == '') ? 0 : 1 );
                        /* Get Data about Item Colors */
                        $colors = $order_item['colors'];
                        $artnote = '';
                        foreach ($colors as $crow) {
                            $artnote.=' qty ' . $crow['order_color_qty'] . ' color ' . $crow['order_color_itemcolor'] . ',';
                        }
                        if (strlen($artnote) > 0) {
                            $artnote = substr($artnote, 0, -1);
                        }

                        $art_note = 'Item ' . $artnote;
                        if ($orderdata['customer_company'] != '') {
                            $brown_customer = $orderdata['customer_company'];
                        } else {
                            $brown_customer = $orderdata['customer_name'];
                        }
                        $instruct = $orderdata['order_customer_comment'];
                        if ($order_item['imprint_type']==2) {
                            $instruct.= 'Artwork Email Later';
                        } elseif ($order_item['imprint_type']==3) {
                            $instruct.= 'Repeat Setup';
                        }
                        $artdat = array(
                            'order_id' => $brownord,
                            'customer_instruct' => $instruct, //$orderdata['order_customer_comment'],
                            /* 'customer'=>$ord_details['customer_name'], */
                            'customer' => $brown_customer,
                            'customer_phone' => $orderdata['contact_phone'],
                            'customer_email' => $orderdata['contact_email'],
                            'customer_contact' => $orderdata['contact_first_name'] . ' ' . $orderdata['contact_last_name'],
                            'item_name' => $order_item['item_name'],
                            'item_number' => $order_item['item_number'],
                            'item_id' => $order_item['item_id'],
                            'item_qty' => $order_item['item_qty'],
                            'artwork_note' => $art_note,
                            'artwork_rush' => $rushval,
                            'order_date' => $orderdata['order_date'],
                            'order_num' => $order_num,
                        );
                        // Add Artwork
                        $artw_id = $this->artwork_update($artdat);
                        if (!$artw_id) {
                            $out['message'] = 'Art Data for Order not added';
                            return $out;
                        } else {
                            $artw = $artw_id;
                        }

                    }
                    // Update Artwork
                    // Check ARTWORK Arts

                    $chkres = $this->check_finart_arts($artw);

                    if ($chkres == 0) {
                        // Add data about Order
                        // Path to ART logos
                        $path_logo_fl = $this->config->item('artwork_logo');
                        $path_logo_sh = $this->config->item('artwork_logo_relative');
                        // Insert Locations
                        $num_pp = 1;
                        $logodat = 0;
                        $textdat = 0;
                        // Get data about ART submit
                        $artloc = $order_item['artworks'];

                        foreach ($artloc as $artrow) {
                            $colors_array = explode(',', $artrow['order_artwork_colors']);
                            $color_nums = count($colors_array);
                            $color_1 = $color_2 = $color_3 = $color_4 = '';
                            switch ($color_nums) {
                                case 1:
                                    $color_1 = $colors_array[0];
                                    break;
                                case 2:
                                    $color_1 = $colors_array[0];
                                    $color_2 = $colors_array[1];
                                    break;
                                case 3:
                                    $color_1 = $colors_array[0];
                                    $color_2 = $colors_array[1];
                                    $color_3 = $colors_array[2];
                                    break;
                                case 4:
                                    $color_1 = $colors_array[0];
                                    $color_2 = $colors_array[1];
                                    $color_3 = $colors_array[2];
                                    $color_4 = $colors_array[3];
                                    break;
                            }
                            // order_artwork_printloc,order_artwork_font, order_artwork_text, order_artwork_note
                            if ($artrow['order_artwork_text'] != '') {
                                // Text Artwork
                                $artlocdata = array(
                                    'artwork_id' => $artw,
                                    'art_type' => 'Text',
                                    'art_ordnum' => $num_pp,
                                    'logo_src' => NULL,
                                    'redraw_time' => 0,
                                    'redrawvect' => 0,
                                    'rush' => $rushval,
                                    'customer_text' => $artrow['order_artwork_text'],
                                    'font' => $artrow['order_artwork_font'],
                                    'art_numcolors' => $color_nums,
                                    'art_color1' => trim($color_1),
                                    'art_color2' => trim($color_2),
                                    'art_color3' => trim($color_3),
                                    'art_color4' => trim($color_4),
                                    'art_location' => $artrow['order_artwork_printloc'],
                                );
                                $resart = $this->artworkart_update($artlocdata);
                                if ($resart) {
                                    $textdat = 1;
                                    $num_pp++;
                                }
                            }
                            if (!empty($artrow['logo_file'])) {
                                $artdat = array(
                                    'artwork_id' => $artw,
                                    'art_type' => 'Logo',
                                    'art_ordnum' => $num_pp,
                                    'logo_src' => $artrow['logo_file'],
                                    'redraw_time' => time(),
                                    'redrawvect' => 1,
                                    'rush' => $rushval,
                                    'customer_text' => '',
                                    'font' => '',
                                    'art_numcolors' => $color_nums,
                                    'art_color1' => trim($color_1),
                                    'art_color2' => trim($color_2),
                                    'art_color3' => trim($color_3),
                                    'art_color4' => trim($color_4),
                                    'art_location' => $artrow['order_artwork_printloc'],
                                );
                                $res = $this->artworkart_update($artdat);
                                if ($res) {
                                    $logodat = 1;
                                    $num_pp++;
                                }

                            }
                        }
                        // Update order in brown
                        if ($logodat==1 || $textdat==1) {
                            $this->db->set('order_art',1);
                            $this->db->set('order_art_update',time());
                            $artsync['art_stage']=1;
                            if ($textdat==1 && $logodat==0) {
                                $this->db->set('order_redrawn',1);
                                $this->db->set('order_redrawn_update',  time());
                                $artsync['redraw_stage']=1;
                                $this->db->set('order_vectorized',1);
                                $this->db->set('order_vectorized_update',  time());
                                $artsync['vector_stage']=1;
                            } else {
                                $this->db->set('order_redrawn',1);
                                $this->db->set('order_redrawn_update',  time());
                                $artsync['redraw_stage']=1;
                            }
                            $this->db->where('order_id',$brownord);
                            $this->db->update('ts_orders');
                        }
                    }
                    // Insert into ts_artdata_sync
                    $this->db->set('order_id', $artsync['order_id']);
                    $this->db->set('customer', $artsync['customer']);
                    $this->db->set('item_descript', $artsync['item_descript']);
                    $this->db->set('rush', $artsync['rush']);
                    $this->db->set('blank', $artsync['blank']);
                    $this->db->set('art_stage', $artsync['art_stage']);
                    $this->db->set('redraw_stage', $artsync['redraw_stage']);
                    $this->db->set('vector_stage', $artsync['vector_stage']);
                    $this->db->set('proof_stage', $artsync['proof_stage']);
                    $this->db->set('approv_stage', $artsync['approv_stage']);
                    $this->db->insert('ts_artdata_sync');
                }
                $numpp++;
            }
        }
        return true;
    }

    public function weborder_details($order_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Order Not Found'];

        $this->db->select('*');
        $this->db->from('sb_orders');
        $this->db->where('order_id', $order_id);
        $res = $this->db->get()->row_array();
        if (ifset($res,'order_id')) {
            // Get Items
            $this->db->select('oi.*, i.item_number, i.item_name, i.printshop_inventory_id');
            $this->db->from('sb_order_items oi');
            $this->db->join('sb_items i', 'i.item_id=oi.item_id');
            $this->db->where('oi.order_id', $order_id);
            $itemres = $this->db->get()->result_array();
            $items = [];
            foreach ($itemres as $itemrow) {
                // Select colors
                $this->db->select('*');
                $this->db->from('sb_order_colors');
                $this->db->where('order_item_id', $itemrow['order_item_id']);
                $colors = $this->db->get()->result_array();
                $itemrow['colors']=$colors;
                // Select Artworks
                $this->db->select('*');
                $this->db->from('sb_order_artworks');
                $this->db->where('order_item_id', $itemrow['order_item_id']);
                $artrows = $this->db->get()->result_array();
                $artworks = [];
                foreach ($artrows as $artrow) {
                    $this->db->select('*');
                    $this->db->from('sb_order_userlogos');
                    $this->db->where('order_userlogo_artworkid', $artrow['order_artwork_id']);
                    $logores = $this->db->get()->row_array();
                    if (ifset($logores,'order_userlogo_id',0)>0) {
                        $artrow['logo_file'] = $logores['order_userlogo_filename'];
                        $artrow['logo_source'] = $logores['order_userlogo_file'];
                    } else {
                        $artrow['logo_file'] = $artrow['logo_source'] = '';
                    }
                    $artworks[] = $artrow;
                }
                $itemrow['artworks'] = $artworks;
                $items[] = $itemrow;
            }
            $data = [
                'order' => $res,
                'items' => $items,
            ];
            $out['result'] = $this->success_result;
            $out['data'] = $data;
        }
        return $out;
    }

    public function add_liftorder($orddata, $item, $confirmation) {
        $out = array('order_id' => 0, 'order_num' => '');
        $paymethod = 'PAYPAL';
        // $orddata=$this->order_data($order_id);
        $quickord=0;
        $art = $item['artworks'];
        $blank = 0;
        // if ($orddata['imprinting'] == 0) {
        //     $blank = 1;
        // }
        if ($item['imprint_type']==0) {
            $blank = 1;
        }
        $ordnum = $this->finorder_num();
        /* Inser into Brown Orders */
        $this->db->set('create_date', time());
        $this->db->set('update_date', time());
        $this->db->set('order_date', strtotime($orddata['order_date']));
        // $this->db->set('brand_id', $this->brand_id);
        $this->db->set('order_num', $ordnum);
        $this->db->set('weborder', 1);
        $this->db->set('order_usr_repic', -1);
        $this->db->set('order_qty', $item['item_qty']);
        if ($item['shipping_date']) {
            $this->db->set('shipdate', $item['shipping_date']);
        }
        $this->db->set('order_confirmation', $confirmation);
        $this->db->set('order_system', 'new');
        $this->db->set('arttype','new');
        $this->db->set('brand', $orddata['brand']);
        $this->db->insert('ts_orders');
        $neword = $this->db->insert_id();
        if ($neword != 0) {
            $out['order_id'] = $neword;
            $out['order_num'] = $ordnum;
            echo $confirmation.PHP_EOL;
            $cc_fee = round(($item['total'] * $this->default_ccfee) / 100, 2);
            $profit = round(($item['total'] * $this->default_profit_perc) / 100, 2);
            $rushval = (($item['production_term'] == 'Standard' || $item['production_term'] == '') ? 0 : 1 );
            if (!empty(trim($orddata['customer_company']))) {
                $brown_customer = trim($orddata['customer_company']);
            } else {
                $brown_customer = $orddata['contact_first_name'] . ' ' . $orddata['contact_last_name'];
            }
            // Add record to Artdata Export
            $artsync=array(
                'order_id'=>$neword,
                'rush'=>$rushval,
                'blank'=>$blank,
                'customer'=>$brown_customer,
                'item_descript'=>$item['item_name'],
                'art_stage'=>0,
                'redraw_stage'=>0,
                'vector_stage'=>0,
                'proof_stage'=>0,
                'approv_stage'=>0,
            );
            if ($blank==1) {
                $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=1;
            }
            $out['artsync']=$artsync;
            $this->db->set('shipping', $item['shipping_cost']);
            // $order_total = ($item['item_qty']*$item['item_price'])+$orddata['inprinting_price']+$orddata['tax']+$orddata['shipping_price']+$orddata['rush_price']-$orddata['discount'];
            $order_total = $item['total'];
            $this->db->set('customer_name', $brown_customer);
            $this->db->set('customer_email', $orddata['contact_email']);
            $this->db->set('order_items', $item['item_name']);
            $this->db->set('order_itemnumber', $item['item_number']);
            $this->db->set('item_id', $item['item_id']);
            // $this->db->set('revenue', $orddata['order_total']);
            $this->db->set('revenue', $order_total);
            $this->db->set('tax', $item['tax']);
            $this->db->set('profit', $profit);
            $this->db->set('cc_fee', $cc_fee);
            $this->db->set('order_rush', $rushval);
            $this->db->set('order_blank', $blank);
            $this->db->where('order_id', $neword);
            if ($blank == 1) {
                $this->db->set('order_art', 1);
                $this->db->set('order_art_update', time());
                $this->db->set('order_redrawn', 1);
                $this->db->set('order_redrawn_update', time());
                $this->db->set('order_vectorized', 1);
                $this->db->set('order_vectorized_update', time());
                $this->db->set('order_proofed', 1);
                $this->db->set('order_proofed_update', time());
                $this->db->set('order_approved', 1);
                $this->db->set('order_approved_update', time());
            }
            $this->db->update('ts_orders');
            // Get Rush List
            $this->load->model('calendars_model');
            if ($blank==1) {
                $rushlist = $this->calendars_model->parse_rushblankcalend($item['item_id']);
            } else {
                $rushlist = $this->calendars_model->parse_rushcalend($item['item_id']);
            }
            $rushidx = strtotime($item['shipping_date']) . '-' . intval($item['rush_cost']);
            // Add contact
            $this->db->set('order_id', $neword);
            $this->db->set('contact_name', $orddata['contact_first_name'] . ' ' . $orddata['contact_last_name']);
            $this->db->set('contact_phone', $orddata['contact_phone']);
            $this->db->set('contact_emal', $orddata['contact_email']);
            $this->db->set('contact_art', 1);
            $this->db->set('contact_inv', 1);
            $this->db->set('contact_trk', 1);
            $this->db->insert('ts_order_contacts');
            for ($i = 0; $i < 2; $i++) {
                $this->db->set('order_id', $neword);
                $this->db->set('contact_art', 0);
                $this->db->set('contact_inv', 0);
                $this->db->set('contact_trk', 0);
                $this->db->insert('ts_order_contacts');
            }
            // Add data into SHIPPING
            $this->db->set('order_id', $neword);
            if (!empty($item['event_date'])) {
                $this->db->set('event_date', $item['event_date']);
            }
            // RUSH!!!!
            if ($item['shipping_date']) {
                $this->db->set('shipdate', $item['shipping_date']);
            }
            $this->db->set('rush_list', serialize($rushlist));
            $this->db->set('rush_idx', $rushidx);
            $this->db->set('rush_price', $item['rush_cost']);
            $this->db->set('arrive_date', $item['arrive_date']);
            $this->db->insert('ts_order_shippings');
            // State
            $state_id = NULL;
            if (!empty($orddata['shipping_state'])) {
                $this->db->select('state_id');
                $this->db->from('ts_states');
                $this->db->where('country_id', $orddata['shipping_country_id']);
                $this->db->where('state_code', $orddata['shipping_state']);
                $statchk = $this->db->get()->row_array();
                if (isset($statchk['state_id'])) {
                    $state_id = $statchk['state_id'];
                }
            }
            // Add data about Shipping Address
            $this->db->set('order_id', $neword);
            $this->db->set('country_id', $orddata['shipping_country_id']);
            $this->db->set('ship_contact', $orddata['shipping_firstname'] . ' ' . $orddata['shipping_lastname']);
            $this->db->set('ship_company', (empty($orddata['shipping_company']) ? NULL : $orddata['shipping_company']));
            $this->db->set('ship_address1', $orddata['shipping_street1']);
            $this->db->set('ship_address2', (empty($orddata['shipping_street2']) ? NULL : $orddata['shipping_street2']));
            $this->db->set('city', $orddata['shipping_city']);
            $this->db->set('zip', $orddata['shipping_zipcode']);
            $this->db->set('state_id', $state_id);
            $this->db->set('item_qty', $item['item_qty']);
            if ($item['shipping_date']) {
                $this->db->set('ship_date', $item['shipping_date']);
            }
            if (empty($item['shipping_cost'])) {
                $this->db->set('shipping', 0.01);
            } else {
                $this->db->set('shipping', $item['shipping_cost']);
            }
            $this->db->set('sales_tax', $item['tax']);
            $this->db->insert('ts_order_shipaddres');
            $adrid = $this->db->insert_id();
            // Shipping Cost
            if ($adrid > 0) {
                $this->db->set('order_shipaddr_id', $adrid);
                $this->db->set('shipping_method', $item['shipping_method']);
                if (empty($item['shipping_cost'])) {
                    $this->db->set('shipping_cost', 0.01);
                } else {
                    $this->db->set('shipping_cost', $item['shipping_cost']);
                }
                if (!empty($item['arrive_date'])) {
                    $this->db->set('arrive_date', $item['arrive_date']);
                }
                $this->db->set('current', 1);
                $this->db->insert('ts_order_shipcosts');
            }
            // Items
            $this->db->set('order_id', $neword);
            $this->db->set('item_id', $item['item_id']);
            $this->db->set('item_qty', $item['item_qty']);
            $this->db->set('base_price', $item['item_price']);
            $this->db->set('item_price', $item['item_price']);
            $this->db->set('setup_price', $item['setup_price']);
            $this->db->set('imprint_price', $item['imprint_price']);
            $this->db->insert('ts_order_items');
            $item_id = $this->db->insert_id();
            if ($item_id > 0) {
                // Add colors
                $colordat = $item['colors'];
                foreach ($colordat as $crow) {
                    $this->db->set('order_item_id', $item_id);
                    $this->db->set('item_description', $item['item_name']);
                    $this->db->set('item_price', $item['item_price']);
                    $this->db->set('item_qty', $crow['order_color_qty']);
                    $this->db->set('item_color', $crow['order_color_itemcolor']);
                    if (!empty($item['printshop_inventory_id'])) {
                        $this->db->set('printshop_item_id', $item['printshop_inventory_id']);
                    }
                    $this->db->insert('ts_order_itemcolors');
                }
                if ($blank == 1) {
                    $this->db->set('order_item_id', $item_id);
                    $this->db->set('imprint_description', '&nbsp;');
                    $this->db->insert('ts_order_imprints');
                    // Add empty details
                    for ($i = 0; $i < 12; $i++) {
                        $this->db->set('order_item_id', $item_id);
                        $this->db->set('imprint_active', 0);
                        if ($i==1) {
                            $this->db->set('print_1', 0.00);
                        } else {
                            $this->db->set('print_1', $item['imprint_price']);
                        }
                        $this->db->set('print_2', $item['imprint_price']);
                        $this->db->set('print_3', $item['imprint_price']);
                        $this->db->set('print_4', $item['imprint_price']);
                        $this->db->set('setup_1', $item['setup_price']);
                        $this->db->set('setup_2', $item['setup_price']);
                        $this->db->set('setup_3', $item['setup_price']);
                        $this->db->set('setup_4', $item['setup_price']);
                        $this->db->insert('ts_order_imprindetails');
                    }
                } else {
                    $numpp = 0;
                    $locnum = 1;
                    foreach ($art as $arow) {
                        // Calc a number of colors
                        $numcolors = 1;
                        $colorsarray = explode(',', $arow['order_artwork_colors']);
                        if (count($colorsarray) > 1) {
                            $numcolors = 2;
                        }
                        if ($item['imprint_type']==2) {
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('imprint_description', 'Loc 1  1st Color Imprinting');
                            $this->db->set('imprint_price', 0.00);
                            $this->db->set('imprint_item', 1);
                            $this->db->set('imprint_qty', $item['item_qty']);
                            $this->db->insert('ts_order_imprints');
                            // Setup
                        } elseif ($item['imprint_type']==3) {
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('imprint_description', 'Loc 1  1st Color Imprinting');
                            $this->db->set('imprint_price', 0.00);
                            $this->db->set('imprint_item', 1);
                            $this->db->set('imprint_qty', $item['item_qty']);
                            $this->db->insert('ts_order_imprints');
                        } else {
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('imprint_item', 1);
                            $this->db->set('imprint_qty', $item['item_qty']);
                            if ($item['imprint_type']==1) {
                                $this->db->set('imprint_description', 'Loc ' . $locnum . ' - ' . $arow['order_artwork_printloc'] . ' 1st Color Imprinting');
                                if ($numpp == 0) {
                                    $this->db->set('imprint_price', 0.00);
                                } else {
                                    $this->db->set('imprint_price', $item['imprint_price']);
                                }
                            } else {
                                $this->db->set('imprint_description', 'Loc ' . $locnum . ' -  1st Color Imprinting');
                                if ($numpp == 0) {
                                    $this->db->set('imprint_price', 0.00);
                                } else {
                                    if ($item['imprint_type']==3) {
                                        $this->db->set('imprint_price', 0.00);
                                    } else {
                                        $this->db->set('imprint_price', $item['imprint_price']);
                                    }
                                }
                            }
                            $this->db->set('order_item_id', $item_id);
                            $this->db->insert('ts_order_imprints');
                        }
                        $numpp++;
                        if ($numcolors == 2) {
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('imprint_description', 'Loc ' . $locnum . ' - ' . $arow['order_artwork_printloc'] . ' 2nd Color Imprinting');
                            $this->db->set('imprint_item', 1);
                            $this->db->set('imprint_qty', $item['item_qty']);
                            if ($numpp == 0) {
                                $this->db->set('imprint_price', 0.00);
                            } else {
                                $this->db->set('imprint_price', $item['imprint_price']);
                            }
                            $this->db->set('order_item_id', $item_id);
                            $this->db->insert('ts_order_imprints');
                            $numpp++;
                        }
                        $locnum++;
                    }
                    for ($i = 1; $i <= 12; $i++) {
                        $this->db->set('order_item_id', $item_id);
                        $this->db->set('num_colors', 1);
                        if ($i==1) {
                            $this->db->set('print_1', 0.00);
                        } else {
                            if ($item['imprint_type']==3) {
                                $this->db->set('print_1', 0);
                            } else {
                                $this->db->set('print_1', $item['imprint_price']);
                            }
                        }
                        $this->db->set('print_2', $item['imprint_price']);
                        $this->db->set('print_3', $item['imprint_price']);
                        $this->db->set('print_4', $item['imprint_price']);
                        if ($item['imprint_type']==3) {
                            $this->db->set('setup_1', 0);
                        } else {
                            $this->db->set('setup_1', $item['setup_price']);
                        }
                        $this->db->set('setup_2', $item['setup_price']);
                        $this->db->set('setup_3', $item['setup_price']);
                        $this->db->set('setup_4', $item['setup_price']);
                        if ($i < $locnum) {
                            $this->db->set('imprint_active', 1);
                        } else {
                            $this->db->set('imprint_active', 0);
                        }
                        if ($item['imprint_type']==3) {
                            $this->db->set('imprint_type','REPEAT');
                        }
                        $this->db->insert('ts_order_imprindetails');
                    }

                    // if ($item['imprint_type']!=2) {
                        $this->db->set('order_item_id', $item_id);
                        if ($item['imprint_type']==3) {
                            $this->db->set('imprint_description', 'Repeat Setup Charge');
                            $this->db->set('imprint_price', 0);
                        } else {
                            $this->db->set('imprint_description', 'One Time Art Setup Charge');
                            $this->db->set('imprint_price', $item['setup_price']);
                        }
                        $this->db->set('imprint_item', 0);
                        $this->db->set('imprint_qty', $numpp);
                        $this->db->insert('ts_order_imprints');
                    // }
                }
            }
            // Add New Billing Info
            // Get Billing State
            $bilstate_id = NULL;
            if (!empty($orddata['billing_state'])) {
                $this->db->select('state_id');
                $this->db->from('ts_states');
                $this->db->where('country_id', $orddata['billing_country_id']);
                $this->db->where('state_code', $orddata['billing_state']);
                $statchk = $this->db->get()->row_array();
                if (isset($statchk['state_id'])) {
                    $bilstate_id = $statchk['state_id'];
                }
            }
            $this->db->set('order_id', $neword);
            $this->db->set('customer_name', $orddata['customer_name']);
            $this->db->set('company', $orddata['customer_company']);
            $this->db->set('address_1', $orddata['billing_street1']);
            $this->db->set('address_2', $orddata['billing_street2']);
            $this->db->set('city', $orddata['billing_city']);
            $this->db->set('zip', $orddata['billing_zipcode']);
            $this->db->set('country_id', $orddata['billing_country_id']);
            $this->db->set('state_id', $bilstate_id);
            $this->db->set('customer_ponum', $orddata['post_office']);
            $this->db->insert('ts_order_billings');
            // Add Payments
            $batchdate=strtotime(date('Y-m-d',strtotime($orddata['order_date'])));
            $this->db->set('create_date', date('Y-m-d H:i:s'));
            $this->db->set('batch_date', $batchdate);
            $this->db->set('order_id', $neword);
            $this->db->set('batch_amount', $item['total']);
            if ($orddata['payment_card_type'] == 'American Express') {
                // batch_amex
                $ccfee = $this->config->item('paypal_amexfee');
                $pureval = round($orddata['order_total'] * ((100 - $ccfee) / 100), 2);
                $duedate = getAmexDueDate($batchdate, $paymethod);
                $this->db->set('batch_amex', $pureval);
            } else {
                $ccfee = $this->config->item('paypal_vmdfee');
                $pureval = round($orddata['order_total'] * ((100 - $ccfee) / 100), 2);
                $duedate = getVMDDueDate($batchdate, $paymethod);
                $this->db->set('batch_vmd', $pureval);
            }
            $this->db->set('batch_due', $duedate);
            $this->db->set('batch_received', 0);
            $this->db->set('batch_type', $orddata['payment_card_type']);
            $this->db->set('batch_num', substr($orddata['payment_card_number'], -4));
            $this->db->set('batch_transaction', $orddata['transaction_id']);
            $this->db->insert('ts_order_batches');
            // Charge value
            $this->db->set('order_id', $neword);
            $this->db->set('cardnum', $orddata['payment_card_number']);
            $this->db->set('exp_month', $orddata['payment_card_month']);
            $this->db->set('exp_year', $orddata['payment_card_year']);
            $this->db->set('cardcode', $orddata['payment_card_vn']);
            $this->db->set('autopay', 1);
            $this->db->insert('ts_order_payments');
        }
        return $out;
    }


    function add_brown_ord($order_id, $user_id) {
        $out = array('order_id' => 0, 'order_num' => '');
        $paymethod = 'PAYPAL';

        $data = $this->order_details(array('order_id' => $order_id));
        if ($data['result']==1) {
            $orddata=$data['data'];
            $quickord=0;
            if ($orddata['order_customer_comment']=='Quick Order' && floatval($orddata['shipping_price'])==0) {
                $quickord=1;
            }
            $art = $this->get_order_artwork($order_id);
            $blank = 0;
            if (($orddata['imprinting'] == 0 && $orddata['inprinting_price']) || count($art) == 0) { //&& count($art) == 0
                $blank = 1;
            }
            $db_table = 'ts_orders';
            $ordnum = $this->finorder_num();
            /* Inser into Brown Orders */
            if ($user_id) {
                $this->db->set('create_usr', $user_id);
                $this->db->set('update_usr', $user_id);
            }
            $this->db->set('create_date', time());
            $this->db->set('update_date', time());
            $this->db->set('order_date', strtotime($orddata['order_date']));
            $this->db->set('brand_id', $this->brand_id);
            $this->db->set('order_num', $ordnum);
            $this->db->set('weborder', 1);
            $this->db->set('order_usr_repic', -1);
            $this->db->set('order_qty', $orddata['item_qty']);
            if ($orddata['shipping_date']) {
                $this->db->set('shipdate', strtotime($orddata['shipping_date']));
            }
            $this->db->set('order_confirmation', $orddata['order_confirmation']);
            $this->db->set('order_system', 'new');
            $this->db->set('arttype','new');
            $this->db->set('brand', $orddata['brand']);
            $this->db->insert($db_table);
            $neword = $this->db->insert_id();
            if ($neword != 0) {
                $out['order_id'] = $neword;
                $out['order_num'] = $ordnum;
                $cc_fee = round(($orddata['order_total'] * $this->default_ccfee) / 100, 2);
                $profit = round(($orddata['order_total'] * $this->default_profit_perc) / 100, 2);
                $rushval = (($orddata['production_term'] == 'Standard' || $orddata['production_term'] == '') ? 0 : 1 );
                if ($orddata['customer_company'] != '') {
                    $brown_customer = $orddata['customer_company'];
                } else {
                    $brown_customer = $orddata['contact_first_name'] . ' ' . $orddata['contact_last_name'];
                }
                // Add record to Artdata Export
                $artsync=array(
                    'order_id'=>$neword,
                    'rush'=>$rushval,
                    'blank'=>$blank,
                    'customer'=>$brown_customer,
                    'item_descript'=>$orddata['item_name'],
                    'art_stage'=>0,
                    'redraw_stage'=>0,
                    'vector_stage'=>0,
                    'proof_stage'=>0,
                    'approv_stage'=>0,
                );
                if ($blank==1) {
                    $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=1;
                }
                if ($quickord==1) {
                    // Calc Shipping Cost
                    $shiprate=$this->quickordcalcshiprates($orddata);
                    $this->db->set('shipping', $shiprate);
                    $this->db->set('discount_label','Free Shipping - Quick Order');
                    if (empty($shiprate)) {
                        $this->db->set('discount_val', 0.01);
                        $orddata['shipping_price']=0.01;
                    } else {
                        $this->db->set('discount_val', $shiprate);
                        $orddata['shipping_price']=$shiprate;
                    }
                    $this->db->set('shipping', $orddata['shipping_price']);
                } else {
                    $this->db->set('shipping', $orddata['shipping_price']);
                }
                $order_total = ($orddata['item_qty']*$orddata['item_price'])+$orddata['inprinting_price']+$orddata['tax']+$orddata['shipping_price']+$orddata['rush_price']-$orddata['discount'];
                $this->db->set('customer_name', $brown_customer);
                $this->db->set('customer_email', $orddata['contact_email']);
                $this->db->set('order_items', $orddata['item_name']);
                $this->db->set('order_itemnumber', $orddata['item_number']);
                $this->db->set('item_id', $orddata['order_item_id']);
                $this->db->set('revenue', $order_total);
                $this->db->set('tax', $orddata['tax']);
                $this->db->set('profit', $profit);
                $this->db->set('cc_fee', $cc_fee);
                $this->db->set('order_rush', $rushval);
                $this->db->set('order_blank', $blank);
                $this->db->where('order_id', $neword);
                if ($blank == 1) {
                    $this->db->set('order_art', 1);
                    $this->db->set('order_art_update', time());
                    $this->db->set('order_redrawn', 1);
                    $this->db->set('order_redrawn_update', time());
                    $this->db->set('order_vectorized', 1);
                    $this->db->set('order_vectorized_update', time());
                    $this->db->set('order_proofed', 1);
                    $this->db->set('order_proofed_update', time());
                    $this->db->set('order_approved', 1);
                    $this->db->set('order_approved_update', time());
                }
                if (!empty($orddata['coupon_id'])) {
                    $this->db->set('discount_descript', $orddata['coupon_name']);
                    $this->db->set('discount_label', $orddata['coupon_name']);
                    $this->db->set('discount_val', floatval($orddata['discount']));
                }
                $this->db->update($db_table);
                // Get Rush List
                // $this->load->model('businesscalendar_model');
                $this->load->model('calendars_model');
                if ($blank==1) {
                    $rushlist = $this->calendars_model->parse_rushblankcalend($orddata['order_item_id']);
                } else {
                    $rushlist = $this->calendars_model->parse_rushcalend($orddata['order_item_id']);
                }

                // $rushlist = $rushdata['rush'];
                $rushidx = strtotime($orddata['shipping_date']) . '-' . intval($orddata['rush_price']);
                // Add contact
                $this->db->set('order_id', $neword);
                $this->db->set('contact_name', $orddata['contact_first_name'] . ' ' . $orddata['contact_last_name']);
                $this->db->set('contact_phone', $orddata['contact_phone']);
                $this->db->set('contact_emal', $orddata['contact_email']);
                $this->db->set('contact_art', 1);
                $this->db->set('contact_inv', 1);
                $this->db->set('contact_trk', 1);
                $this->db->insert('ts_order_contacts');
                for ($i = 0; $i < 2; $i++) {
                    $this->db->set('order_id', $neword);
                    $this->db->set('contact_art', 0);
                    $this->db->set('contact_inv', 0);
                    $this->db->set('contact_trk', 0);
                    $this->db->insert('ts_order_contacts');
                }
                // Add data into SHIPPING
                $this->db->set('order_id', $neword);
                if (!empty($orddata['event_date'])) {
                    $this->db->set('event_date', strtotime($orddata['event_date']));
                }
                // RUSH!!!!
                if ($orddata['shipping_date']) {
                    $this->db->set('shipdate', strtotime($orddata['shipping_date']));
                }
                $this->db->set('rush_list', serialize($rushlist));
                $this->db->set('rush_idx', $rushidx);
                $this->db->set('rush_price', $orddata['rush_price']);
                $this->db->set('arrive_date', strtotime($orddata['arrive_date']));
                $this->db->insert('ts_order_shippings');
                // State
                $state_id = NULL;
                if (!empty($orddata['shipping_state'])) {
                    $this->db->select('state_id');
                    $this->db->from('ts_states');
                    $this->db->where('country_id', $orddata['shipping_country_id']);
                    $this->db->where('state_code', $orddata['shipping_state']);
                    $statchk = $this->db->get()->row_array();
                    if (isset($statchk['state_id'])) {
                        $state_id = $statchk['state_id'];
                    }
                }
                // Add data about Shipping Address
                $this->db->set('order_id', $neword);
                $this->db->set('country_id', $orddata['shipping_country_id']);
                $this->db->set('ship_contact', $orddata['shipping_firstname'] . ' ' . $orddata['shipping_lastname']);
                $this->db->set('ship_company', (empty($orddata['shipping_company']) ? NULL : $orddata['shipping_company']));
                $this->db->set('ship_address1', $orddata['shipping_street1']);
                $this->db->set('ship_address2', (empty($orddata['shipping_street2']) ? NULL : $orddata['shipping_street2']));
                $this->db->set('city', $orddata['shipping_city']);
                $this->db->set('zip', $orddata['shipping_zipcode']);
                $this->db->set('state_id', $state_id);
                $this->db->set('item_qty', $orddata['item_qty']);
                if ($orddata['shipping_date']) {
                    $this->db->set('ship_date', strtotime($orddata['shipping_date']));
                }
                if (empty($orddata['shipping_price'])) {
                    $this->db->set('shipping', 0.01);
                } else {
                    $this->db->set('shipping', $orddata['shipping_price']);
                }
                $this->db->set('sales_tax', $orddata['tax']);
                $this->db->insert('ts_order_shipaddres');
                $adrid = $this->db->insert_id();
                // Shipping Cost
                if ($adrid > 0) {
                    $this->db->set('order_shipaddr_id', $adrid);
                    $this->db->set('shipping_method', $orddata['shipping_method_name']);
                    if (empty($orddata['shipping_price'])) {
                        $this->db->set('shipping_cost', 0.01);
                    } else {
                        $this->db->set('shipping_cost', $orddata['shipping_price']);
                    }
                    // $this->db->set('shipping_cost', $orddata['shipping_price']);
                    if (!empty($orddata['arrive_date'])) {
                        $this->db->set('arrive_date', strtotime($orddata['arrive_date']));
                    }
                    $this->db->set('current', 1);
                    $this->db->insert('ts_order_shipcosts');
                }
                // Items
                $this->db->set('order_id', $neword);
                $this->db->set('item_id', $orddata['order_item_id']);
                $this->db->set('item_qty', $orddata['item_qty']);
                $this->db->set('base_price', $orddata['item_price']);
                $this->db->set('item_price', $orddata['item_price']);
                $this->db->insert('ts_order_items');
                $item_id = $this->db->insert_id();
                if ($item_id > 0) {
                    /*  */
                    $this->load->model('prices_model');
                    $imprint_price = $this->prices_model->get_item_pricebytype($orddata['order_item_id'], 'imprint');
                    $setup_price = $this->prices_model->get_item_pricebytype($orddata['order_item_id'], 'setup');
                    $this->db->where('order_item_id', $item_id);
                    $this->db->set('setup_price', $setup_price);
                    $this->db->set('imprint_price', $imprint_price);
                    $this->db->update('ts_order_items');
                    // Add colors
                    $this->db->select('*');
                    $this->db->from('sb_order_colors');
                    $this->db->where('order_color_orderid', $order_id);
                    $colordat = $this->db->get()->result_array();
                    foreach ($colordat as $crow) {
                        $this->db->set('order_item_id', $item_id);
                        $this->db->set('item_description', $orddata['item_name']);
                        $this->db->set('item_price', $orddata['item_price']);
                        $this->db->set('item_qty', $crow['order_color_qty']);
                        $this->db->set('item_color', $crow['order_color_itemcolor']);
                        if (!empty($orddata['printshop_inventory_id'])) {
                            $this->db->set('printshop_item_id', $orddata['printshop_inventory_id']);
                        }
                        $this->db->insert('ts_order_itemcolors');
                    }
                    if ($blank == 1) {
                        $this->db->set('order_item_id', $item_id);
                        $this->db->set('imprint_description', '&nbsp;');
                        $this->db->insert('ts_order_imprints');
                        // Add empty details
                        for ($i = 0; $i < 12; $i++) {
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('imprint_active', 0);
                            if ($i==1) {
                                $this->db->set('print_1', 0.00);
                            } else {
                                $this->db->set('print_1', $imprint_price);
                            }
                            $this->db->set('print_2', $imprint_price);
                            $this->db->set('print_3', $imprint_price);
                            $this->db->set('print_4', $imprint_price);
                            $this->db->set('setup_1', $setup_price);
                            $this->db->set('setup_2', $setup_price);
                            $this->db->set('setup_3', $setup_price);
                            $this->db->set('setup_4', $setup_price);
                            $this->db->insert('ts_order_imprindetails');
                        }
                    } else {
                        $numpp = 0;
                        $locnum = 1;
                        foreach ($art as $arow) {
                            // Calc a number of colors
                            $numcolors = 1;
                            $colorsarray = explode(',', $arow['order_artwork_colors']);
                            if (count($colorsarray) > 1) {
                                $numcolors = 2;
                            }
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('imprint_description', 'Loc ' . $locnum . ' - ' . $arow['order_artwork_printloc'] . ' 1st Color Imprinting');
                            $this->db->set('imprint_item', 1);
                            $this->db->set('imprint_qty', $orddata['item_qty']);
                            if ($numpp == 0) {
                                $this->db->set('imprint_price', 0.00);
                            } else {
                                $this->db->set('imprint_price', $imprint_price);
                            }
                            $this->db->set('order_item_id', $item_id);
                            $this->db->insert('ts_order_imprints');
                            $numpp++;
                            if ($numcolors == 2) {
                                $this->db->set('order_item_id', $item_id);
                                $this->db->set('imprint_description', 'Loc ' . $locnum . ' - ' . $arow['order_artwork_printloc'] . ' 2nd Color Imprinting');
                                $this->db->set('imprint_item', 1);
                                $this->db->set('imprint_qty', $orddata['item_qty']);
                                if ($numpp == 0) {
                                    $this->db->set('imprint_price', 0.00);
                                } else {
                                    $this->db->set('imprint_price', $imprint_price);
                                }
                                $this->db->set('order_item_id', $item_id);
                                $this->db->insert('ts_order_imprints');
                                $numpp++;
                            }
                            $locnum++;
                        }
                        for ($i = 1; $i <= 12; $i++) {
                            $this->db->set('order_item_id', $item_id);
                            $this->db->set('num_colors', 1);
                            if ($i==1) {
                                $this->db->set('print_1', 0.00);
                            } else {
                                $this->db->set('print_1', $imprint_price);
                            }
                            $this->db->set('print_2', $imprint_price);
                            $this->db->set('print_3', $imprint_price);
                            $this->db->set('print_4', $imprint_price);
                            if ($quickord==1) {
                                $this->db->set('setup_1', 0);
                                $this->db->set('setup_2', $setup_price);
                                $this->db->set('setup_3', $setup_price);
                                $this->db->set('setup_4', $setup_price);
                            } else {
                                $this->db->set('setup_1', $setup_price);
                                $this->db->set('setup_2', $setup_price);
                                $this->db->set('setup_3', $setup_price);
                                $this->db->set('setup_4', $setup_price);
                            }
                            if ($i < $locnum) {
                                $this->db->set('imprint_active', 1);
                            } else {
                                $this->db->set('imprint_active', 0);
                            }
                            $this->db->insert('ts_order_imprindetails');
                        }
                        $this->db->set('order_item_id', $item_id);
                        $this->db->set('imprint_description', 'One Time Art Setup Charge');
                        $this->db->set('imprint_item', 0);
                        $this->db->set('imprint_qty', $numpp);
                        if ($quickord==1) {
                            $this->db->set('imprint_price', 0);
                        } else {
                            $this->db->set('imprint_price', $setup_price);
                        }
                        $this->db->insert('ts_order_imprints');
                    }
                }
                // Add New Billing Info
                // Get Billing State
                $bilstate_id = NULL;
                if (!empty($orddata['billing_state'])) {
                    $this->db->select('state_id');
                    $this->db->from('ts_states');
                    $this->db->where('country_id', $orddata['billing_country_id']);
                    $this->db->where('state_code', $orddata['billing_state']);
                    $statchk = $this->db->get()->row_array();
                    if (isset($statchk['state_id'])) {
                        $bilstate_id = $statchk['state_id'];
                    }
                }
                $this->db->set('order_id', $neword);
                $this->db->set('customer_name', $orddata['customer_name']);
                $this->db->set('company', $orddata['customer_company']);
                $this->db->set('address_1', $orddata['billing_street1']);
                $this->db->set('address_2', $orddata['billing_street2']);
                $this->db->set('city', $orddata['billing_city']);
                $this->db->set('zip', $orddata['billing_zipcode']);
                $this->db->set('country_id', $orddata['billing_country_id']);
                $this->db->set('state_id', $bilstate_id);
                $this->db->insert('ts_order_billings');
                // Add Payments
                $batchdate=strtotime(date('Y-m-d',strtotime($orddata['order_date'])));

                if ($user_id) {
                    $this->db->set('create_usr', $user_id);
                    $this->db->set('update_usr', $user_id);
                }
                $this->db->set('create_date', date('Y-m-d H:i:s'));
                // $this->db->set('batch_date', strtotime($orddata['order_date']));
                $this->db->set('batch_date', $batchdate);
                $this->db->set('order_id', $neword);
                $this->db->set('batch_amount', $orddata['order_total']);
                if ($orddata['payment_card_type'] == 'American Express') {
                    // batch_amex
                    $ccfee = $this->config->item('paypal_amexfee');
                    $pureval = round($orddata['order_total'] * ((100 - $ccfee) / 100), 2);
                    $duedate = getAmexDueDate($batchdate, $paymethod);
                    $this->db->set('batch_amex', $pureval);
                } else {
                    $ccfee = $this->config->item('paypal_vmdfee');
                    $pureval = round($orddata['order_total'] * ((100 - $ccfee) / 100), 2);
                    $duedate = getVMDDueDate($batchdate, $paymethod);
                    $this->db->set('batch_vmd', $pureval);
                }
                $this->db->set('batch_due', $duedate);
                $this->db->set('batch_received', 0);
                $this->db->set('batch_type', $orddata['payment_card_type']);
                $this->db->set('batch_num', substr($orddata['payment_card_number'], -4));
                $this->db->set('batch_transaction', $orddata['transaction_id']);
                $this->db->insert('ts_order_batches');
                // Charge value
                $this->db->set('order_id', $neword);
                $this->db->set('cardnum', $orddata['payment_card_number']);
                $this->db->set('exp_month', $orddata['payment_card_month']);
                $this->db->set('exp_year', $orddata['payment_card_year']);
                $this->db->set('cardcode', $orddata['payment_card_vn']);
                $this->db->set('autopay', 1);
                $this->db->insert('ts_order_payments');
                // Insert payment log
                $this->db->set('paylog_date', date('Y-m-d H:i:s'));
                $this->db->set('order_id', $neword);
                $this->db->set('paysum',$orddata['order_total']);
                $this->db->set('card_num', $orddata['payment_card_number']);
                $this->db->set('card_system',$orddata['payment_card_type']);
                $this->db->set('cvv',1);
                $this->db->set('paysucces',1);
                $this->db->set('api_response',$orddata['transaction_id']);
                $this->db->insert('ts_order_paymentlog');
            }
            $out['artsync']=$artsync;
        }
        return $out;
    }

    public function get_order_artwork($order_id) {
        $this->db->select("*");
        $this->db->from('sb_order_artworks');
        $this->db->where('order_artwork_orderid', $order_id);
        $this->db->order_by('order_artwork_id');
        $result = $this->db->get()->result_array();
        $order_art = array();

        foreach ($result as $row) {
            if (isset($row['order_artwork_face']) && $row['order_artwork_face']) {
                $facename = str_replace('/uploads/faces/', '', $row['order_artwork_face']);
                $facename = str_replace('.png', '', $facename);
                if ($row['order_artwork_note'] == '') {
                    $row['order_artwork_note'] = 'Face ' . $facename;
                } else {
                    $row['order_artwork_note'].='<br/>Face ' . $facename;
                }
            }
            $this->db->select('order_userlogo_file, order_userlogo_filename');
            $this->db->from('sb_order_userlogos');
            $this->db->where('order_userlogo_artworkid', $row['order_artwork_id']);
            $row['users_logo'] = $this->db->get()->result_array();
            $order_art[] = $row;
        }
        return $order_art;
    }

    public function finorder_num() {
        $this->db->set('order_date', time());
        $this->db->insert('ts_ordernum');
        return $this->db->insert_id();
    }

    // Calc Shipping Rate for Quick Order
    public function quickordcalcshiprates($orddata) {
        $item_id = $orddata['order_item_id'];
        $zip = $orddata['shipping_zipcode'];
        $qty = $orddata['item_qty'];
        $cntcode = $orddata['ship_cntcode'];
        $shipmeth=$orddata['shipping_method_code'];
        // Select vendor zip
        $this->db->select('v.vendor_zipcode');
        $this->db->from('sb_items i');
        $this->db->join('sb_vendor_items vi','vi.vendor_item_id=i.item_id');
        $this->db->join("vendors v",'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('i.item_id', $item_id);
        $vndres=$this->db->get()->row_array();
        $vendorzip = (isset($vndres['vendor_id']) ? $vndres['vendor_zipcode'] : $this->config->item('zip'));
        // Get Data about Item
        $this->load->model('items_model');
        $itemdata = $this->items_model->get_item($item_id);
        $item = $itemdata['data'];
        // Calc Weight, Num PACK, etc

        $weight = $item['item_weigth'];
        $numpack = (intval($item['cartoon_qty']) == 0 ? $this->min_qty_pack : $item['cartoon_qty']);
        // Shipdates
        $startdeliv = strtotime($orddata['shipping_date']);

        $options = array(
            'zip' => $zip,
            'numinpack' => $numpack,
            'itemqty' => $qty,
            'weight' => $weight,
            'startdeliv' => $startdeliv,
            'vendor_zip' => $vendorzip,
            'item_length' => $item['cartoon_depth'],
            'item_width' => $item['cartoon_width'],
            'item_height' => $item['cartoon_heigh'],
            'ship' => array(),
            'cnt_code' => $cntcode,
        );

        $out = calculate_shipcost($options);
        $outrate=0;
        if ($out['result'] == TRUE) {
            $ship = $out['ship'];
            $codes=  explode('|', $out['code']);
            if (in_array($shipmeth, $codes)) {
                /* Recalc Rates */
                $ship = recalc_rates($ship, $item, $qty, $cntcode);
                foreach ($ship as $shrow) {
                    if ($shrow['ServiceCode']==$shipmeth) {
                        $outrate=$shrow['Rate'];
                    }
                }
            }
        }
        return $outrate;
    }

    public function check_finart($order_id) {
        /* Check artwork with this Order # */
        $art_db = 'ts_artworks';
        $this->db->select('artwork_id');
        $this->db->from($art_db);
        $this->db->where('order_id', $order_id);
        $res = $this->db->get()->row_array();
        if (!isset($res['artwork_id'])) {
            return 0;
        } else {
            return $res['artwork_id'];
        }
    }

    // Insert into Brown Artworks */
    // Insert data to ARTWORK */
    private function artwork_update($data) {
        $db_table = 'ts_artworks';
        $this->db->set('order_id', $data['order_id']);
        $this->db->set('time_create', date('Y-m-d H:i:s'));
        $this->db->set('time_update', date('Y-m-d H:i:s'));
        $this->db->set('customer_instruct', $data['customer_instruct']);
        $this->db->set('customer', $data['customer']);
        $this->db->set('customer_contact', $data['customer_contact']);
        $this->db->set('customer_phone', $data['customer_phone']);
        $this->db->set('customer_email', $data['customer_email']);
        $this->db->set('item_name', $data['item_name']);
        $this->db->set('item_number', $data['item_number']);
        $this->db->set('item_id', $data['item_id']);
        $this->db->set('item_qty', $data['item_qty']);
        $this->db->set('artwork_note', $data['artwork_note']);
        $this->db->set('artwork_rush', $data['artwork_rush']);
        $this->db->insert($db_table);
        $artw_id = $this->db->insert_id();
        if ($artw_id) {
            // Insert History
            $db_history = 'ts_artwork_history';
            $history_msg = 'Order #' . $data['order_num'] . ' was created ' . date('m/d/Y', strtotime($data['order_date'])) . ' online by customer.';
            $this->db->set('artwork_id', $artw_id);
            $this->db->set('message', $history_msg);
            $this->db->insert($db_history);
        }
        return $artw_id;
    }

    public function check_finart_arts($artwork_id) {
        // $db_table = $this->config->item('system_prefix') . '.ts_artwork_arts';
        $db_table = 'ts_artwork_arts';
        $this->db->select('count(artwork_art_id) cnt');
        $this->db->from($db_table);
        $this->db->where('artwork_id', $artwork_id);
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    private function artworkart_update($data) {
        $db_table = 'ts_artwork_arts';
        $this->db->set('artwork_id', $data['artwork_id']);
        $this->db->set('art_type', $data['art_type']);
        $this->db->set('art_ordnum', $data['art_ordnum']);
        $this->db->set('logo_src', $data['logo_src']);
        $this->db->set('redraw_time', $data['redraw_time']);
        $this->db->set('redrawvect', $data['redrawvect']);
        $this->db->set('customer_text', $data['customer_text']);
        $this->db->set('font', $data['font']);
        $this->db->set('art_numcolors', $data['art_numcolors']);
        $this->db->set('art_color1', $data['art_color1']);
        $this->db->set('art_color2', $data['art_color2']);
        $this->db->set('rush', $data['rush']);
        $this->db->insert($db_table);
        $res = $this->db->insert_id();
        return $res;
    }

    public function count_totalbalance($filtr, $addtype='') {
        $this->db->select('order_id, count(batch_id) batchcnt, sum(batch_amount) batchsum');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_term',0);
        $this->db->group_by('order_id');
        $balancesql = $this->db->get_compiled_select();

        $this->db->select("sum(o.revenue) as revenue, sum(p.batchsum) as batchsum",FALSE);
        $this->db->from("ts_orders o");
        $this->db->join('('.$balancesql.') p','p.order_id=o.order_id','left');
        $type_filer = intval(ifset($filtr,'filter',0));
        $this->db->where('o.is_canceled',0);
        $this->db->where('o.order_date >= ', $this->config->item('netprofit_start'));
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
            if ($type_filer == 1) {
                $this->db->where('o.order_cog is null');
            } elseif ($type_filer == 2) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=40');
            } elseif ($type_filer == 3) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=30 and round(o.profit_perc,0)<40');
            } elseif ($type_filer == 4) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=20 and round(o.profit_perc,0)<30');
            } elseif ($type_filer == 5) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>=10 and round(o.profit_perc,0)<20');
            } elseif ($type_filer == 6) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)<=0');
            } elseif ($type_filer == 7) {
                // $this->db->where('o.is_canceled', 1);
            } elseif ($type_filer == 8) {
                $this->db->where('o.order_cog is not null and round(o.profit_perc,0)>0 and round(o.profit_perc,0)<10');
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
        if (!empty($addtype)) {
            if ($addtype=='blank') {
                $this->db->where('o.order_blank',1);
            } else {
                $this->db->where('o.order_blank',0);
                $this->db->where('o.arttype', $addtype);
            }
        }
        $totalres=$this->db->get()->row_array();
        return floatval($totalres['revenue']) - floatval($totalres['batchsum']);
    }

    public function attempts_report($filtr) {
        $data = $this->attemptreportdata($filtr);
        $res=$data['out_dat'];
        $attach=$data['out_attach'];
        $this->load->model('exportexcell_model');
        $this->exportexcell_model->expot_attemptreport($res, $attach, $filtr['starttime']);
    }

    public function attemptreportdata($filtr) {
        $this->db->select('*');
        $this->db->from('sb_baskets');
        if (ifset($filtr, 'starttime',0) > 0) {
            $this->db->where('unix_timestamp(created_time) >= ', $filtr['starttime']);
        }
        if (ifset($filtr , 'endtime', 0) > 0 ) {
            $this->db->where('unix_timestamp(created_time) <= ', $filtr['endtime']);
        }
        $this->db->order_by('updated_time');
        $rows = $this->db->get()->result_array();
        $out_dat = [];
        $out_attach = [];
        $this->load->model('shipping_model');
        foreach ($rows as $row) {
            $user = $row['contact_person'];
            $user_contact = '';
            if (!empty($row['contact_email'])) {
                $user_contact.='Email '.$row['contact_email'].PHP_EOL;
            }
            if (!empty($row['contact_phone'])) {
                $user_contact.='Phone '.$row['contact_phone'].' '.$row['cell_phone']==0 ? '' : '(mob)'.PHP_EOL;
            }

            $user_address = '';
            if (!empty($row['ship_country'])) {
                $user_address.='Country ' . $row['ship_country'] . ' '. PHP_EOL;
            }
            if (!empty($row['ship_city'])) {
                $user_address.='City ' . $row['ship_city'] . ' ';
            }
            if (!empty($cart['ship_street1'])) {
                $user_address.=$cart['ship_street1'] . ' ';
            }
            if (!empty($cart['ship_street2']) && $cart['ship_street2']) {
                $user_address.=$cart['ship_street2'] . ' ';
            }
            // Items
            $this->db->select('bi.*, i.item_number, i.item_name');
            $this->db->from('sb_basket_items bi');
            $this->db->join('sb_items i', 'bi.item_id=i.item_id');
            $this->db->where('bi.basket_id', $row['basket_id']);
            $basket_items = $this->db->get()->result_array();
            foreach ($basket_items as $basket_item) {
                $item_number = $basket_item['item_number'];
                $item_name = $basket_item['item_name'];
                $item_qty = $basket_item['qty1']+$basket_item['qty2']+$basket_item['qty3']+$basket_item['qty4'];
                $item_color = $basket_item['color1'];
                if (!empty($basket_item['color2']) || !empty($basket_item['color3']) || !empty($basket_item['color4'])) {
                    $item_color = 'assorted';
                }
                $imprint = '';
                if ($basket_item['imprint_type']==0) {
                    $imprint = 'Blank';
                } elseif ($basket_item['imprint_type']==1) {
                    $imprint = 'Attach Below';
                } elseif ($basket_item['imprint_type']==2) {
                    $imprint = 'Email Later';
                } elseif ($basket_item['imprint_type']==3) {
                    $imprint = 'Repeat Past Order';
                }
                $artdata = '';
                if ($basket_item['imprint_type']==0) {
                    $artdata = 'No Art';
                } else {
                    $this->db->select('l.*, sii.item_inprint_location');
                    $this->db->from('sb_basket_locations l');
                    $this->db->join('sb_item_inprints sii','l.item_inprint_id = sii.item_inprint_id');
                    $this->db->where('l.basket_item_id', $basket_item['basket_item_id']);
                    $arts = $this->db->get()->result_array();
                    $art_order = 1;
                    foreach ($arts as $artrow) {
                        $artadd = 'Imprint Location ' . $art_order . ' ';
                        if ($basket_item['imprint_type'] == 2) {
                            $artadd.=$this->art_sendlater;
                        } elseif ($basket_item['imprint_type'] == 3) {
                            $artadd.=$this->art_repeat;
                        } else {
                            $artadd.=$artrow['item_inprint_location']. PHP_EOL;
                            if (ifset($artrow, 'logo_url','')) {
                                $artadd.=' File URL http://' . $this->input->server('SERVER_NAME') . $artrow['logo_url']. PHP_EOL;
                                array_push($out_attach, $artrow['logo_url']);
                            }
                            if (!empty($artrow['logo_txt'])) {
                                $artadd.=' User Text ' . $artrow['logo_txt']. PHP_EOL;
                            }
                            $colors = $artrow['color1'].(!empty($artrow['color2']) ? ', '.$artrow['color2'] : '');
                            if (!empty($colors)) {
                                $artadd.=' Colors - ' . $colors . PHP_EOL;
                            }
                        }
                        $art_order++;
                        $artdata.=$artadd;
                    }
                }
                $rushprice = 0;
                $rushdays = 0;
                $rushdate = '';
                $ship_method = '';
                $ship_cost = 0;
                if (!empty($basket_item['shipping_calendar'])) {
                    $calend = json_decode($basket_item['shipping_calendar'], true);
                    if (isset($calend['shipping'])) {
                        foreach ($calend['shipping'] as $shipping) {
                            if ($shipping['current']==1) {
                                $rushdate = date('m/d/Y', $shipping['date']);
                                $rushdays = $shipping['term'];
                                $rushprice = $shipping['price'];
                                break;
                            }
                        }
                    }
                    if (isset($calend['arrive'])) {
                        foreach ($calend['arrive'] as $arrive) {
                            if ($arrive['current']==1) {
                                $ship_method = $arrive['label'];
                                $ship_cost = $arrive['price'];
                            }
                        }
                    }
                }
                $item_cost = $basket_item['sale_price']* $item_qty;
                $imprint_cost = ($basket_item['imprint_type']==0 ? 0 : $basket_item['inprint_price'] * $item_qty * ($basket_item['imprint'] - 1));
                $setup_cost = $basket_item['imprint_type']==0 ? 0 : $basket_item['setup_price'] * $basket_item['imprint'];
                $tax_cost = $basket_item['sale_tax'];
                $total_cost = $basket_item['sale_cost'];
            }
            $user_location = '';
            if (!empty($row['user_ip'])) {
                $ipdat = $this->shipping_model->ipdata_exist($row['user_ip']);
                if ($ipdat['result']) {
                    $user_location.=($ipdat['city_name'] == '-' ? '' : $ipdat['city_name'] . ', ');
                    $user_location.=($ipdat['region_name'] == '-' ? '' : $ipdat['region_name'] . ', ');
                    $user_location.=($ipdat['country_code'] == '-' ? '' : $ipdat['country_name']);
                }
            }
            $cc_card = '';
            if (!empty($row['credit_card_system'])) {
                $cc_card.= $row['credit_card_system'].' ';
            }
            if (!empty($row['credit_card_number'])) {
                $cc_card.= $row['credit_card_number'].' ';
            }
            if (!empty($row['credit_card_month'])) {
                $cc_card.=' exp '.$row['credit_card_month'];
                if (!empty($row['credit_card_year'])) {
                    $cc_card.='/'.$row['credit_card_year'];
                }
            }
            // Last upd fld
            $last_upd = '';
            $last_fld ='';
            $this->db->select('*');
            $this->db->from('sb_basketchange_log');
            $this->db->where('basket_code', $row['basket_code']);
            $this->db->order_by('basketchange_log_id','desc');
            $logdat = $this->db->get()->row_array();
            if (ifset($logdat,'basketchange_log_id', 0) > 0) {
                $last_upd = date('m/d/Y H:i:s', strtotime($logdat['change_time']));
                $last_fld = $logdat['basket_parameter'];
            }
            $out_dat[] = [
                'checkout_start' => date('m/d/Y H:i:s', strtotime($row['created_time'])),
                'last_action' => $last_upd,
                'art' => $artdata,
                'item_number' => $item_number,
                'item_name' => $item_name,
                'item_qty' => $item_qty,
                'item_colors' => $item_color,
                'imprint' => $imprint,
                'rushdate' => $rushdate,
                'rushprice' => $rushprice,
                'rushdays' => $rushdays,
                'itemcost' => $item_cost,
                'imprintval' => $imprint_cost,
                'setup' => $setup_cost,
                'tax' => $tax_cost,
                'total' => $total_cost,
                'ship_method' => $ship_method,
                'shipping' => $ship_cost,
                'user' => $user,
                'user_contact' => $user_contact,
                'user_address' => $user_address,
                'user_ip' => $row['user_ip'],
                'user_location' => $user_location,
                'cc_details' => $cc_card,
                'last_field' => $last_fld,
            ];
        }
        return [
            'out_dat' => $out_dat,
            'out_attach' => $out_attach,
        ];
    }

    public function _attemptreportdata($filtr) {
        $this->load->model('shipping_model');
        $this->db->select('*');
        $this->db->from('sb_cartdatas');
        $this->db->where('last_activity is not NULL');
        if (isset($filtr['starttime']) && $filtr['starttime']) {
            $this->db->where('created_date >= ', $filtr['starttime']);
        }

        if (isset($filtr['endtime']) && $filtr['endtime']) {
            $this->db->where('created_date <= ', $filtr['endtime']);
        }

        $this->db->order_by('last_activity');
        $res = $this->db->get()->result_array();

        $out_dat = array();
        $out_attach = array();
        foreach ($res as $row) {
            $cart = unserialize($row['cart']);
            $artdata = 'No ART';
            $arts = array();
            if ($row['arts'] != '') {
                $arts = unserialize($row['arts']);
            } elseif (isset($cart['art1'])) {
                /* Get Data about art */
                $artdet = $this->get_uploadart($cart['art1']);
                if (!empty($artdet)) {
                    $arts['art1'] = $artdet;
                }
                if (isset($cart['art2'])) {
                    $artdet = $this->get_uploadart($cart['art2']);
                    if (!empty($artdet)) {
                        $arts['art2'] = $artdet;
                    }
                }
            }
            if (!empty($arts)) {
                $artdata = '';
                $art_order = 1;

                foreach ($arts as $artrow) {
                    $artadd = 'Imprint Location ' . $art_order . ' ';
                    if ($artrow['order_artwork_note'] == $this->art_sendlater) {
                        $artadd.=$this->art_sendlater;
                    } elseif ($artrow['order_artwork_note'] == $this->art_sendbefore) {
                        $artadd.=$this->art_sendbefore;
                    } else {
                        $artadd.=$artrow['order_artwork_printloc'] . PHP_EOL;
                        if (isset($artrow['order_artwork_logo']) && $artrow['order_artwork_logo']) {
                            $artadd.=' File URL http://' . $this->input->server('SERVER_NAME') . $artrow['order_artwork_logo'] . PHP_EOL;
                            array_push($out_attach, $artrow['order_artwork_logo']);
                        } elseif (isset($artrow['order_userlogo_filename']) && !empty($artrow['order_userlogo_filename'])) {
                            $artadd.=' File URL http://' . $this->input->server('SERVER_NAME') . $artrow['order_userlogo_filename'] . PHP_EOL;
                            array_push($out_attach, $artrow['order_userlogo_filename']);
                        }
                        if (!empty($artrow['order_artwork_text'])) {
                            $artadd.=' User Text ' . $artrow['order_artwork_text'] . PHP_EOL;
                        }
                        if (!empty($artrow['order_artwork_colors'])) {
                            $artadd.=' Colors - ' . $artrow['order_artwork_colors'] . PHP_EOL;
                        }
                        if (!empty($artrow['order_artwork_font'])) {
                            $artadd.=' Font ' . $artrow['order_artwork_font'] . PHP_EOL;
                        }
                    }
                    $art_order++;
                    $artdata.=$artadd;
                }
            }

            $user = '';
            if (isset($cart['ship_firstname'])) {
                $user.=$cart['ship_firstname'];
            }
            if (isset($cart['ship_lastname'])) {
                $user.=' ' . $cart['ship_lastname'];
            }
            $user_contact = '';
            if (isset($cart['emailaddr'])) {
                $user_contact.='Email ' . $cart['emailaddr'] . PHP_EOL;
            }
            if (isset($cart['phonenum'])) {
                $user_contact.=' Phone ' . $cart['phonenum'];
            }

            $user_address = '';
            if (isset($cart['ship_country'])) {
                $user_address.='Country ' . $cart['ship_country'] . ' ' . PHP_EOL;
            }
            if (isset($cart['ship_cityname'])) {
                $user_address.='City ' . $cart['ship_cityname'] . ' ';
            }
            if (isset($cart['ship_street1']) && $cart['ship_street1']) {
                $user_address.=$cart['ship_street1'] . ' ';
            }
            if (isset($cart['ship_street2']) && $cart['ship_street2']) {
                $user_address.=$cart['ship_street2'] . ' ';
            }
            $item_number = '';
            $item_name = '';
            if (isset($cart['item_id']) && $cart['item_id']) {
                $this->db->select('i.item_number, i.item_name');
                $this->db->from('sb_items i');
                $this->db->where('item_id', $cart['item_id']);
                $itm = $this->db->get()->row_array();
                if (isset($itm['item_number'])) {
                    $item_number = $itm['item_number'];
                    $item_name = $itm['item_name'];
                }
            }
            $item_color = '';
            $item_color_id = '';
            for ($i = 1; $i < 5; $i++) {
                if (isset($cart['col' . $i]) && $cart['col' . $i]) {
                    if ($item_color_id == '') {
                        $item_color_id = $cart['col' . $i];
                    } else {
                        $item_color = 'assorted';
                        break;
                    }
                }
            }
            if ($item_color == '' && $item_color_id) {
                $this->db->select('item_color');
                $this->db->from('sb_item_colors');
                $this->db->where('item_color_id', $item_color_id);
                $itmc = $this->db->get()->row_array();
            }
            $imprint = '';
            if (isset($cart['imprint'])) {
                if ($cart['imprint'] == 0) {
                    $imprint = 'Blank - No imprint';
                } else {
                    $imprint = 'Imprint locations - ' . $cart['imprint'];
                }
            }
            /* Geo IP */
            $user_location = (isset($cart['customer_location']) ? $cart['customer_location'] : '');
            $user_ip = (isset($cart['customer_ip']) ? $cart['customer_ip'] : '');

            if ($user_ip && $user_location == '') {
                $ipdat = $this->shipping_model->ipdata_exist($user_ip);
                if ($ipdat['result']) {
                    $user_location.=($ipdat['city_name'] == '-' ? '' : $ipdat['city_name'] . ', ');
                    $user_location.=($ipdat['region_name'] == '-' ? '' : $ipdat['region_name'] . ', ');
                    $user_location.=($ipdat['country_code'] == '-' ? '' : $ipdat['country_name']);
                } else {
                    $ipdat = $this->shipping_model->get_geolocation($user_ip);
                    if (isset($ipdat['country_code']) && $ipdat['country_code'] != '-') {
                        $this->shipping_model->update_geoip($ipdat, $user_ip);
                        $user_location.=($ipdat['city_name'] == '-' ? '' : $ipdat['city_name'] . ', ');
                        $user_location.=($ipdat['region_name'] == '-' ? '' : $ipdat['region_name'] . ', ');
                        $user_location.=($ipdat['country_code'] == '-' ? '' : $ipdat['country_name']);
                    }
                }
            }
            $rushval = (isset($cart['rushval']) ? intval($cart['rushval']) : 0);
            $cc_card = '';
            if (isset($cart['cctype']) && $cart['cctype'] != '') {
                $cc_card.=$cart['cctype'] . ' ';
            }
            if (isset($cart['ccnumber']) && $cart['ccnumber']) {
                $cc_card.=$cart['ccnumber'] . '';
            }
            if (isset($cart['ccexpmonth']) && $cart['ccexpmonth']) {
                $cc_card.=$cart['ccexpmonth'] . '/';
            }
            if (isset($cart['ccexpyear']) && $cart['ccexpyear']) {
                $cc_card.=$cart['ccexpyear'];
            }
            $lastfld = '';
            if (isset($cart['last_updated_item']) && $cart['last_updated_item']) {
                foreach ($this->cardflds as $fldrow) {
                    if ($cart['last_updated_item'] == $fldrow['idx']) {
                        $lastfld = $fldrow['name'];
                        break;
                    }
                }
            }

            $out_dat[] = array(
                'checkout_start' => date('m/d/Y H:i:s', $row['created_date']),
                'last_action' => date('m/d/Y H:i:s', strtotime($row['last_activity'])),
                'art' => $artdata,
                'item_number' => $item_number,
                'item_name' => $item_name,
                'item_qty' => (isset($cart['sumval']) ? $cart['sumval'] : ''),
                'item_colors' => $item_color,
                'imprint' => $imprint,
                'rushdate' => ($rushval == 0 ? '' : date('m/d/Y', $rushval)),
                'rushprice' => (isset($cart['rushprice']) ? $cart['rushprice'] : ''),
                'rushdays' => (isset($cart['rushdays']) ? $cart['rushdays'] : ''),
                'itemcost' => (isset($cart['itemcost']) ? $cart['itemcost'] : ''),
                'imprintval' => (isset($cart['imprintval']) ? $cart['imprintval'] : ''),
                'setup' => (isset($cart['setup']) ? $cart['setup'] : ''),
                'tax' => (isset($cart['tax']) ? $cart['tax'] : ''),
                'total' => (isset($cart['total']) ? $cart['total'] : ''),
                'ship_method' => (isset($cart['ship_method']) ? $cart['ship_method'] : ''),
                'shipping' => (isset($cart['shipping']) ? $cart['shipping'] : ''),
                'user' => $user,
                'user_contact' => $user_contact,
                'user_address' => $user_address,
                'user_ip' => $user_ip,
                'user_location' => $user_location,
                'cc_details' => $cc_card,
                'last_field' => $lastfld,
            );
        }
        $retval = array('out_dat' => $out_dat, 'out_attach' => $out_attach);
        return $retval;
    }

    public function get_uploadart($art_id) {
        $this->db->select('aw.*,l.order_userlogo_filename');
        $this->db->from('sb_order_artworks aw');
        $this->db->join('sb_order_userlogos l', 'l.order_userlogo_artworkid=aw.order_artwork_id', 'left');
        $this->db->where('aw.order_artwork_id', $art_id);
        $res = $this->db->get()->row_array();
        return $res;
    }

    public function get_unpaid_orders($datebgn, $brand) {
        $this->db->select('order_id, count(batch_id) cnt, sum(batch_amount) paysum');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_term',0);
        $this->db->group_by('order_id');
        $batchsql = $this->db->get_compiled_select();

        $this->db->select('o.order_id, o.order_num, o.order_date, o.order_confirmation, o.customer_name, o.revenue, b.cnt, b.paysum');
        $this->db->from('ts_orders o');
        $this->db->join('('.$batchsql.') b','b.order_id=o.order_id','left');
        $this->db->where('order_date >= ', $datebgn);
        $this->db->where('is_canceled', 0);
        $res = $this->db->get()->result_array();

        $out = [];
        foreach ($res as $row) {
            if (round($row['revenue'],2)!==round($row['paysum'],2)) {
                $notpaid = $row['revenue'] - $row['paysum'];
                // Get contacts
                $contact = $this->db->select('contact_phone, contact_emal as contact_email')->from('ts_order_contacts')->where('order_id', $row['order_id'])->get()->row_array();
                $this->db->select('date_format(from_unixtime(h.created_time),\'%m/%d/%y\') as created_date');
                $this->db->from('ts_artwork_history h');
                $this->db->join('ts_artworks a','a.artwork_id=h.artwork_id');
                $this->db->where('a.order_id', $row['order_id']);
                $this->db->order_by('h.artwork_history_id','desc');
                $history=$this->db->get()->row_array();
                $out[] = [
                    'order_date' => date('m/d/y',$row['order_date']),
                    'order_num' => $row['order_num'],
                    'order_confirmation' => $row['order_confirmation'],
                    'customer_name' => $row['customer_name'],
                    'revenue' => MoneyOutput($row['revenue']),
                    'paysum' => MoneyOutput($row['paysum']),
                    'notpaid' =>MoneyOutput($notpaid),
                    'email' => ifset($contact,'contact_email',''),
                    'phone' => ifset($contact, 'contact_phone', ''),
                    'last_update' => ifset($history,'created_date',''),
                ];
            }
        }
        return $out;
    }

    public function get_updaid_totals($brand) {
        $start_date = strtotime('2013-01-01');
        $this->db->select('order_id, count(batch_id) cnt, sum(batch_amount) paysum');
        $this->db->from('ts_order_batches');
        $this->db->where('batch_term',0);
        $this->db->group_by('order_id');
        $batchsql = $this->db->get_compiled_select();

        $this->db->select('date_format(from_unixtime(o.order_date),\'%Y\') as year, sum(o.revenue - ifnull(b.paysum,0)) as debt');
        $this->db->from('ts_orders o');
        $this->db->join('('.$batchsql.') as b','b.order_id=o.order_id','left');
        $this->db->where('o.order_date >= ', $start_date);
        $this->db->where('o.is_canceled', 0);
        $this->db->group_by('date_format(from_unixtime(o.order_date),\'%Y\')');
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function accountreceiv_totals($period, $brand) {
        // Owned
        $daystart = strtotime(date('Y-m-d'));
        $cur_year = intval(date('Y'));
        $limit_year = 0;
        if ($period > 0) {
            $limit_year = $cur_year - intval($period) + 1;
        }
        $this->db->select('yearorder, sum(balance) as balance');
        $this->db->from('v_order_balances');
        $this->db->where('balance > 0');
        if ($limit_year!==0) {
            $this->db->where('yearorder >= ', $limit_year);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('yearorder');
        $ownsrc = $this->db->get()->result_array();
        $totalown = 0;
        foreach ($ownsrc as $ownr) {
            $totalown+=$ownr['balance'];
        }
        $this->db->select('sum(balance) as balance');
        $this->db->from('v_order_balances');
        $this->db->where('balance > 0');
        if ($limit_year!==0) {
            $this->db->where('yearorder >= ', $limit_year);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->where('batch_due < ',$daystart);
        $pastres = $this->db->get()->row_array();
        $pastown = $pastres['balance'];

        // Refund
        $this->db->select('yearorder, sum(balance) as balance');
        $this->db->from('v_order_balances');
        $this->db->where('balance < 0');
        if ($limit_year!==0) {
            $this->db->where('yearorder >= ', $limit_year);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->group_by('yearorder');
        $refsrc = $this->db->get()->result_array();
        $totalref = 0;
        foreach ($refsrc as $refr) {
            $totalref+=$refr['balance'];
        }

        $own = [];
        $refund = [];
        if ($limit_year==0) {
            $this->db->select('min(yearorder) as yearorder');
            $this->db->from('v_order_balances');
            $yearres = $this->db->get()->row_array();
            $limit_year = $yearres['yearorder'];
        }
        for ($i=0; $i<1000; $i++) {
            $yearown=0;
            $yearref=0;
            foreach ($ownsrc as $row) {
                if ($row['yearorder']==($cur_year-$i)) {
                    $yearown=$row['balance'];
                    break;
                }
            }
            foreach ($refsrc as $row) {
                if ($row['yearorder']==($cur_year-$i)) {
                    $yearref=$row['balance'];
                    break;
                }
            }

            $own[] = [
                'year' => $cur_year - $i,
                'balance' => $yearown,
            ];

            $refund[] = [
                'year' => $cur_year - $i,
                'balance' => $yearref,
            ];
            if (($cur_year - $i)<=$limit_year) {
                break;
            }
        }
        return array(
            'totalown' => $totalown,
            'pastown' => $pastown,
            'totalrefund' => $totalref,
            'own' => $own,
            'refund' => $refund,
            'balance' => $totalown+$totalref,
        );
    }

    public function accountreceiv_details($period, $brand, $ownsort, $owndirec, $refundsort, $refunddirec) {
        // $this->db->select('')
        $daystart = strtotime(date('Y-m-d'));
        $cur_year = intval(date('Y'));
        $limit_year = 0;
        if ($period > 0) {
            $limit_year = $cur_year - intval($period) + 1;
        }
        $this->db->select('*');
        $this->db->from('v_order_balances');
        $this->db->where('balance > 0');
        if ($limit_year!==0) {
            $this->db->where('yearorder >= ', $limit_year);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->order_by($ownsort, $owndirec);
        $owndats = $this->db->get()->result_array();
        // $owndats = [];
        $owns=[];
        foreach ($owndats as $owndat) {
            // $stype = 'Credit Card';
            $sclass = '';
            if ($owndat['balance_manage']==3) {
                $stype = $this->accrec_terms;
            } elseif ($owndat['balance_manage']==2) {
                $stype = $this->accrec_prepay;
            } elseif ($owndat['balance_manage']==1) {
                $stype = $this->accrec_willupd;
                if (!empty($owndat['cntcard'])) {
                    $stype = $this->accrec_credit;
                    $sclass='creditcard';
                }
            }
            $owndat['type']=$stype;
            $owndat['typeclass'] = $sclass;
            $owns[]=$owndat;
        }
        // Refund
        if ($refundsort=='balance') {
            if ($refunddirec=='asc') {
                $refunddir='desc';
            } else {
                $refunddir='asc';
            }
        } else {
            $refunddir = $refunddirec;
        }
        $this->db->select('*');
        $this->db->from('v_order_balances');
        $this->db->where('balance < 0');
        if ($limit_year!==0) {
            $this->db->where('yearorder >= ', $limit_year);
        }
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        $this->db->order_by($refundsort, $refunddir);
        $refunds = $this->db->get()->result_array();
        return array(
            'owns' => $owns,
            'refunds' => $refunds,
            'daystart' => $daystart,
            'ownsort' => $ownsort,
            'owndir' => $owndirec,
            'refundsort' => $refundsort,
            'refunddir' => $refunddirec,
        );
    }

    public function purchaseorder_totals($inner, $brand) {
        // Get Not placed
        $this->db->select('a.order_proj_status as status, count(o.order_id) as totalqty, sum(o.revenue-o.profit) as totalsum');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_statuses a','a.order_id=o.order_id');
        $this->db->where('o.profit_perc is null');
        $this->db->where('a.order_approved_view',0);
        $this->db->where_in('a.order_proj_status', array($this->JUST_APPROVED, $this->NEED_APPROVAL, $this->TO_PROOF, $this->NO_ART));
        $this->db->group_by('a.order_proj_status');
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        if ($inner==1) {
            $this->db->join('v_itemsearch vi', 'vi.item_id = o.item_id');
            $this->db->where_not_in('vi.vendor_name', array('BLUETRACK Internal', 'Stressballs.com Internal'));
        }
        $totals = $this->db->get()->result_array();
        $totaltab = [];
        $totaltab['toplace'] = [
            'qty' => 0,
            'total' => 0,
        ];
        $totaltab['toapprove'] = [
            'qty' => 0,
            'total' => 0,
        ];
        $totaltab['toproof'] = [
            'qty' => 0,
            'total' => 0,
        ];
        foreach ($totals as $total) {
            if ($total['status']==$this->JUST_APPROVED) {
                $totaltab['toplace']['qty'] += $total['totalqty'];
                $totaltab['toplace']['total'] += $total['totalsum'];
            }
            if ($total['status']==$this->NEED_APPROVAL) {
                $totaltab['toapprove']['qty'] += $total['totalqty'];
                $totaltab['toapprove']['total'] += $total['totalsum'];
            }
            if ($total['status']==$this->TO_PROOF || $total['status']==$this->NO_ART) {
                $totaltab['toproof']['qty'] += $total['totalqty'];
                $totaltab['toproof']['total'] += $total['totalsum'];
            }
        }
        return $totaltab;
    }

    public function purchase_fulltotals($brand) {
        $this->db->select('count(o.order_id) as totalqty, sum(o.revenue-o.profit) as totalsum');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_statuses a','a.order_id=o.order_id');
        $this->db->where('o.profit_perc is null');
        $this->db->where('a.order_approved_view',0);
        $this->db->where_in('a.order_proj_status', array($this->JUST_APPROVED, $this->NEED_APPROVAL, $this->TO_PROOF, $this->NO_ART));
        if ($brand!=='ALL') {
            $this->db->where('o.brand',$brand);
        }
        $resall = $this->db->get()->row_array();

        $this->db->select('count(o.order_id) as totalqty, sum(o.revenue-o.profit) as totalsum');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_statuses a','a.order_id=o.order_id');
        if ($brand!=='ALL') {
            $this->db->where('o.brand',$brand);
        }
        $this->db->join('v_itemsearch vi', 'vi.item_id = o.item_id');
        $this->db->where('a.order_approved_view',0);
        $this->db->where_not_in('vi.vendor_name', array('BLUETRACK Internal', 'Stressballs.com Internal'));
        $this->db->where('o.profit_perc is null');
        $this->db->where_in('a.order_proj_status', array($this->JUST_APPROVED, $this->NEED_APPROVAL, $this->TO_PROOF, $this->NO_ART));
        $rest = $this->db->get()->row_array();
        return [
            'total' => $resall['totalsum'],
            'totalfree' => $rest['totalsum'],
        ];
    }

    public function purchaseorder_details($stage, $inner, $brand) {
        // Get Not placed
        if ($stage=='unsign') {
            $stagesrc = [$this->JUST_APPROVED];
        } elseif ($stage == 'approved') {
            $stagesrc = [$this->NEED_APPROVAL];
        } else {
            $stagesrc = [$this->NO_ART, $this->TO_PROOF];
        }
        $this->db->select('a.order_rush, a.specialdiff, o.order_id, o.order_num, o.order_itemnumber, o.item_id, o.order_items, vi.vendor_name, (o.revenue - o.profit) as estpo');
        $this->db->select('o.customer_name as customer');
        $this->db->from('ts_orders o');
        $this->db->join('v_order_statuses a','a.order_id=o.order_id');
        $this->db->join('v_itemsearch vi','vi.item_id=o.item_id');
        $this->db->where_in('a.order_proj_status',$stagesrc);
        $this->db->where('o.profit_perc is null');
        $this->db->where('a.order_approved_view',0);
        if ($brand!=='ALL') {
            $this->db->where('o.brand', $brand);
        }
        if ($inner==1) {
            $this->db->where_not_in('vi.vendor_name', array('BLUETRACK Internal', 'Stressballs.com Internal'));
        }
        $details = $this->db->get()->result_array();
        $out = [];
        $daytime = 24*60*60;
        foreach ($details as $detail) {
            $detail['item_name'] = str_replace(['Stress Balls','Stressballs'],'SB', $detail['order_items']);
            $detail['customitem'] = ($detail['item_id'] > 0 ? '' : 'customitem');
            $detail['vendorname'] = (empty($detail['vendor_name']) ? 'OTHER' : $detail['vendor_name']);
            $detail['order_late'] = ($detail['specialdiff'] > $daytime ? 1 : 0);
            $out[] = $detail;
        }
        return $out;
    }

}
