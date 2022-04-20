<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
Class Leadorder_model extends My_Model {

    private $error_message='Unknown error. Try later';
    private $start_ordnum=22000;
    private $old_orders=19785;
    private $empty_htmlcontent='&nbsp;';
    private $normal_template='Stressball';
    private $default_zip='07012';
    private $tax_state=38;
    protected $project_name='PROJ';
    protected $project_class='projprof';
    protected $custom_class='02 - Custom SB';
    protected $other_class='04 - Other';
    protected $vendor_class='03 - Vendor SB';
    protected $common_class='01 - Inventory';
    protected $art_stages=array(
        array('key'=>'06_noart', 'value'=>'Art - Need Art', 'class'=>'art'),
        array('key'=>'05_notredr', 'value'=>'Art - Redrawing', 'class'=>'art'),
        array('key'=>'04_notvector', 'value'=>'Art - Redrawing', 'class'=>'art'),
        array('key'=>'03_notprof', 'value'=>'Art - To Proof', 'class'=>'art'),
        array('key'=>'02_notapprov', 'value'=>'Art -Need Apprvl', 'class'=>'art'),
        /* array('key'=>'01_notplaced', 'value'=>'FF - To Place', 'class'=>'art'), */
    );

    private $NO_ART = '06_noart';
    private $NO_ART_TXT='Need Art';
    private $REDRAWN = '05_notredr';
    private $REDRAWN_TXT = 'Redrawing';
    private $TO_PROOF = '03_notprof';
    private $TO_PROOF_TXT='To Proof';
    private $NEED_APPROVAL = '02_notapprov';
    private $NEED_APPROVAL_TXT = 'To Approve';
    private $JUST_APPROVED = '01_notplaced';
    private $JUST_APPROVED_TXT = 'Approved';
    private $NO_VECTOR = '04_notvector';
    private $NO_ART_REMINDER='Need Art Reminder';
    private $ART_PROOF='Art Proof';
    private $NEED_APPROVE_REMINDER='Need Approval Reminder';
    // Credit Card Type
    private $creditcardTypes = array(
        array('Name'=>'American Express','cardLength'=>array(15),'cardPrefix'=>array('34', '37'))
    ,array('Name'=>'Maestro','cardLength'=>array(12, 13, 14, 15, 16, 17, 18, 19),'cardPrefix'=>array('5018', '5020', '5038', '6304', '6759', '6761', '6763'))
    ,array('Name'=>'Mastercard','cardLength'=>array(16),'cardPrefix'=>array('51', '52', '53', '54', '55'))
    ,array('Name'=>'Visa','cardLength'=>array(13,16),'cardPrefix'=>array('4'))
    ,array('Name'=>'JCB','cardLength'=>array(16),'cardPrefix'=>array('3528', '3529', '353', '354', '355', '356', '357', '358'))
    ,array('Name'=>'Discover','cardLength'=>array(16),'cardPrefix'=>array('6011', '622126', '622127', '622128', '622129', '62213',
            '62214', '62215', '62216', '62217', '62218', '62219',
            '6222', '6223', '6224', '6225', '6226', '6227', '6228',
            '62290', '62291', '622920', '622921', '622922', '622923',
            '622924', '622925', '644', '645', '646', '647', '648',
            '649', '65'))
    ,array('Name'=>'Solo','cardLength'=>array(16, 18, 19),'cardPrefix'=>array('6334', '6767'))
    ,array('Name'=>'Unionpay','cardLength'=>array(16, 17, 18, 19),'cardPrefix'=>array('622126', '622127', '622128', '622129', '62213', '62214',
            '62215', '62216', '62217', '62218', '62219', '6222', '6223',
            '6224', '6225', '6226', '6227', '6228', '62290', '62291',
            '622920', '622921', '622922', '622923', '622924', '622925'))
    ,array('Name'=>'Diners Club','cardLength'=>array(14),'cardPrefix'=>array('300', '301', '302', '303', '304', '305', '36'))
    ,array('Name'=>'Diners Club US','cardLength'=>array(16),'cardPrefix'=>array('54', '55'))
    ,array('Name'=>'Diners Club Carte Blanche','cardLength'=>array(14),'cardPrefix'=>array('300','305'))
    ,array('Name'=>'Laser','cardLength'=>array(16, 17, 18, 19),'cardPrefix'=>array('6304', '6706', '6771', '6709'))
    );


    function __construct() {
        parent::__construct();
    }

    // Credit Card Type
//    private $creditcardTypes = array(
//        array('Name'=>'American Express','cardLength'=>array(15),'cardPrefix'=>array('34', '37'))
//    ,array('Name'=>'Maestro','cardLength'=>array(12, 13, 14, 15, 16, 17, 18, 19),'cardPrefix'=>array('5018', '5020', '5038', '6304', '6759', '6761', '6763'))
//    ,array('Name'=>'Mastercard','cardLength'=>array(16),'cardPrefix'=>array('51', '52', '53', '54', '55'))
//    ,array('Name'=>'Visa','cardLength'=>array(13,16),'cardPrefix'=>array('4'))
//    ,array('Name'=>'JCB','cardLength'=>array(16),'cardPrefix'=>array('3528', '3529', '353', '354', '355', '356', '357', '358'))
//    ,array('Name'=>'Discover','cardLength'=>array(16),'cardPrefix'=>array('6011', '622126', '622127', '622128', '622129', '62213',
//            '62214', '62215', '62216', '62217', '62218', '62219',
//            '6222', '6223', '6224', '6225', '6226', '6227', '6228',
//            '62290', '62291', '622920', '622921', '622922', '622923',
//            '622924', '622925', '644', '645', '646', '647', '648',
//            '649', '65'))
//    ,array('Name'=>'Solo','cardLength'=>array(16, 18, 19),'cardPrefix'=>array('6334', '6767'))
//    ,array('Name'=>'Unionpay','cardLength'=>array(16, 17, 18, 19),'cardPrefix'=>array('622126', '622127', '622128', '622129', '62213', '62214',
//            '62215', '62216', '62217', '62218', '62219', '6222', '6223',
//            '6224', '6225', '6226', '6227', '6228', '62290', '62291',
//            '622920', '622921', '622922', '622923', '622924', '622925'))
//    ,array('Name'=>'Diners Club','cardLength'=>array(14),'cardPrefix'=>array('300', '301', '302', '303', '304', '305', '36'))
//    ,array('Name'=>'Diners Club US','cardLength'=>array(16),'cardPrefix'=>array('54', '55'))
//    ,array('Name'=>'Diners Club Carte Blanche','cardLength'=>array(14),'cardPrefix'=>array('300','305'))
//    ,array('Name'=>'Laser','cardLength'=>array(16, 17, 18, 19),'cardPrefix'=>array('6304', '6706', '6771', '6709'))
//    );
//
//    function __construct() {
//        parent::__construct();
//    }

    public function get_leadorders($options) {
        $item_dbtable='sb_items';
        $amountcnt="select order_id, sum(amount_sum) as cnt_amnt from ts_order_amounts where amount_sum>0 group by order_id";
        $this->db->select('o.order_id, o.create_usr, o.order_date, o.brand_id, o.order_num, o.customer_name');
        $this->db->select('o.customer_email, o.revenue, o.shipping, o.is_shipping, o.tax, o.cc_fee, o.order_cog');
        $this->db->select('o.profit, o.profit_perc, o.is_canceled, o.reason,  o.item_id, o.invoice_doc, o.invoice_send');
        $this->db->select('o.order_confirmation, o.order_items, o.order_usr_repic, o.weborder, o.order_qty, o.shipdate');
        $this->db->select('o.is_invoiced, o.order_system, o.order_arthide, o.order_itemnumber, o.deliverydate');
        // ART Stages
        $this->db->select('o.order_art, o.order_redrawn, o.order_proofed, o.order_vectorized, o.order_approved, o.is_canceled');
        $this->db->select('itm.item_name');
        // $this->db->select("coalesce(oa.cnt_amnt,0)  as cnt_amnt",FALSE);
        $this->db->select('u.user_leadname, u.user_name');
        $this->db->select('itm.item_number, coalesce(st.item_id, 0) as stok_item', FALSE);
        $this->db->from('ts_orders o');
        $this->db->join("{$item_dbtable} as itm",'itm.item_id=o.item_id ','left');
        $this->db->join('users u','u.user_id=o.order_usr_repic','left');
        $this->db->join('ts_stock_items st','st.item_id=o.item_id','left');
        // $this->db->join("({$amountcnt}) oa",'oa.order_id=o.order_id','left');
        // $this->db->where('o.is_canceled',0);
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
            // $this->db->like("concat(ucase(o.customer_name),' ',ucase(o.customer_email),' ',o.order_num,' ',coalesce(o.order_confirmation,'')) ",strtoupper($options['search']));
            $this->db->like("concat(ucase(o.customer_name),' ',ucase(coalesce(o.customer_email,'')),' ',o.order_num,' ', coalesce(o.order_confirmation,''), ' ', ucase(o.order_items), o.revenue ) ",strtoupper($options['search']));
        }
        if (isset($options['begin'])) {
            $this->db->where('o.order_date >= ',$options['begin']);
        }
        if (isset($options['end'])) {
            $this->db->where('o.order_date <= ',$options['end']);
        }
        if (isset($options['order_qty'])) {
            $this->db->where('o.order_qty',$options['order_qty']);
            $this->db->where('o.is_canceled',0);
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
        $curdat='';
        $ordidx=1;
        foreach ($res as $row) {
            $this->db->select('distinct(c.item_color) as item_color');
            $this->db->from('ts_order_itemcolors c');
            $this->db->join('ts_order_items i','i.order_item_id=c.order_item_id');
            $this->db->where('i.order_id', $row['order_id']);
            $colordat=$this->db->get()->result_array();
            $itemcolor='';
            foreach ($colordat as $crow) {
                $itemcolor.=$crow['item_color'].', ';
            }
            $itemcolor=substr($itemcolor,0,-2);
            $row['itemcolor']=$itemcolor;
            $row['itemcolorclass']='';
            if (strlen($itemcolor)>14) {
                $row['itemcolorclass']='wide';
            }
            $this->db->select('count(amount_id) as cnt, sum(amount_sum) as cnt_amnt');
            $this->db->from('ts_order_amounts');
            $this->db->where('order_id', $row['order_id']);
            $this->db->where('amount_sum > 0');
            $amntres=$this->db->get()->row_array();
            if ($amntres['cnt']==0) {
                $row['cnt_amnt']=0;
            } else {
                $row['cnt_amnt']=$amntres['cnt_amnt'];
            }
            // group by order_id
            $row['rowclass']='';
            $row['scrdate']=$row['order_date'];
            $orddate=date('m/d/y',$row['order_date']);
            if ($orddate!=$curdat) {
                $curdat=$orddate;
                if ($ordidx>1) {
                    $row['rowclass']='underline';
                }
                $row['order_date']=($row['order_date']=='' ? '' : date('m/d/y',$row['order_date']));
            } else {
                $row['order_date']='&mdash;';
            }
            $ordidx++;
            if ($row['order_num']!=$ordnxt) {
                $row['rowclass']='underline';
            }

            $row['ordernum_class']='';

            if ($row['order_system']=='new') {
            } else {
                $row['ordernum_class']='quckbook';
            }
            $row['order_confirmclass']='';
            if (empty($row['order_confirmation'])) {
                $row['out_confirm']='historical';
                $row['order_confirmclass']='historical';
            } else {
                $row['out_confirm']=$row['order_confirmation'];
            }
            $row['order_confirmation']=(empty($row['order_confirmation']) ? 'historical' : $row['order_confirmation']);
            $ordnxt=intval($row['order_num'])-1;
            $row['numpp']=$numpp;
            $row['profit_class']='';
            $row['proftitleclass']='';
            $row['proftitle']='';
            $row['out_item']='&nbsp;';
            if ($row['order_items']) {
                $row['out_item']=$row['order_items'];
            } elseif ($row['item_name']) {
                $row['out_item']=$row['item_name'];
            }
            $row['custom_order']=($row['item_id']==$this->config->item('custom_id') ? 1 : 0);
            $row['points']=round($row['profit']*$this->config->item('profitpts'),0).' pts';
            $row['points_val']=round($row['profit']*$this->config->item('profitpts'),0);
            $row['profit']='$'.number_format($row['profit'],2,'.',',');
            if ($row['order_cog']=='') {
                $row['order_cog']='project';
                $row['cog_class']='projectcog';
                $row['profit_class']=$this->project_class;
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
            $row['order_class']=$this->vendor_class;
            if ($row['item_id']==$this->config->item('custom_id')) {
                $row['order_class']=$this->custom_class;
            } elseif (substr($row['item_number'],0,2)=='23') {
                $row['order_class']=$this->other_class;
            } elseif ($row['stok_item']>0) {
                $row['order_class']=$this->common_class;
            }
            $row['order_status']='&nbsp;';
            $row['order_status_class']='';
            $order_proj_status='';
            if ($row['is_canceled']==1) {
                $row['order_status']='canceled';
                $row['order_status_class']='canceled';
            } else {
                if ($row['order_arthide']==0) {
                    if ($row['cnt_amnt']>0 && $row['order_approved']=1) {
                        // Order Placed
                    } else {
                        if ($row['order_art'] = 1 && $row['order_redrawn'] = 0) {
                            $order_proj_status='05_notredr';
                        } elseif ($row['order_approved'] = 0 && $row['order_proofed'] = 1) {
                            $order_proj_status='02_notapprov';
                        } elseif ($row['order_proofed']= 0 && $row['order_vectorized'] = 1) {
                            $order_proj_status='03_notprof';
                        } elseif ($row['order_vectorized'] = 0 && $row['order_redrawn'] = 1) {
                            $order_proj_status='04_notvector';
                        } elseif ($row['order_art'] = 0) {
                            $order_proj_status='06_noart';
                        }
                    }
                }
                if (!empty($order_proj_status)) {
                    foreach ($this->art_stages as $stagerow) {
                        if ($stagerow['key']==$order_proj_status) {
                            $row['order_status']=$stagerow['value'];
                            $row['order_status_class']=$stagerow['class'];
                            break;
                        }
                    }
                } else {
                    /*if (intval($row['tickcnt'])>0) {
                        $row['order_status']='FF - Fulfillment';
                        $row['order_status_class']='fulfillment';
                    } else { */
                    $statusship=$this->_leadorder_shipping_status($row['order_id']);
                    $row['order_status']=$statusship['order_status'];
                    $row['order_status_class']=$statusship['order_status_class'];
                    /* } */
                }

            }
            $out_array[]=$row;
            $numpp++;
        }
        return $out_array;
    }


    // Get Data about Lead Order
    public function get_leadorder($order_id, $user_id, $brand='ALL') {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->load->model('orders_model');
        $this->load->model('creditapp_model');
        $this->db->select('o.*, br.brand_name, itm.item_name as item_name, u.user_leadname, u.user_name, finance_order_amountsum(o.order_id) as amnt');
        $this->db->from('ts_orders o');
        $this->db->join('brands br','br.brand_id=o.brand_id','left');
        $this->db->join('v_itemsearch itm','itm.item_id=o.item_id','left');
        $this->db->join('users u','u.user_id=o.order_usr_repic','left');
        $this->db->where('o.order_id',$order_id);
        $res=$this->db->get()->row_array();

        if (!isset($res['order_id'])) {
            $out['msg']='Order Not Found';
            return $out;
        }

        if ($res['order_cog']=='') {
            $res['profit_class']=$this->project_class;
        } else {
            $res['profit_class']=orderProfitClass($res['profit_perc']);
        }
        $res['showbilladdress']=0;
        $res['user_replic']='&nbsp;';
        $res['usrreplclass']='user';
        if ($res['order_usr_repic']>0) {
            $res['user_replic']=($res['user_leadname']=='' ? $res['user_name'] : $res['user_leadname']);
        } else {
            if ($res['weborder']==1) {
                $res['user_replic']='Website';
                $res['usrreplclass']='website';
            }
        }
        $res['invoice_class']='';
        if ($res['is_invoiced']!=0) {
            $res['invoice_class']='active';
        }
        // Check approved Credit App
        $crdapp=$this->creditapp_model->order_creditapp($order_id);
        if (!isset($crdapp['creditapp_line_id'])) {
            $res['appaproved']=0;
            $res['credit_app_id']=0;
        } else {
            $res['credit_app_id']=$crdapp['creditapp_line_id'];
            if ($crdapp['status']=='approved') {
                $res['appaproved']=1;
            } else {
                $res['appaproved']=0;
            }
        }
        $res['newappcreditlink']=0;
        $out['result']=$this->success_result;
        $out['prvorder']=$this->_get_previous_order($order_id, $brand);
        $out['nxtorder']=$this->_get_next_order($order_id, $brand);
        $out['order_system_type']=($res['order_system']=='new' ? 'new' : 'old');

        $payments=$this->get_leadorder_payments($order_id);
        $paytotal=0;
        foreach ($payments as $row) {
            $paytotal+=$row['batch_amount'];
        }
        $res['payment_total']=$paytotal;
        $out['total_due']=round($res['revenue']-$paytotal,2);
        $out['payments']=$payments;
        // Get Ticket
        $ticket=array(
            'class'=>'closed',
            'label'=>'No Ticket',
        );
        $this->db->select('count(ticket_id) as cnt');
        $this->db->from('ts_tickets');
        $this->db->where('order_num', $res['order_num']);
        $this->db->where('ticket_closed',0);
        $tickres=$this->db->get()->row_array();
        $out['numtickets']=$tickres['cnt'];
        if ($tickres['cnt']>0) {
            $this->db->select('TIMESTAMPDIFF(HOUR, created, now()) as hdiff');
            $this->db->select('ticket_closed');
            $this->db->from('ts_tickets');
            $this->db->where('order_num', $res['order_num']);
            $this->db->where('ticket_closed',0);
            $this->db->order_by('ticket_id', 'desc');
            $tickdet=$this->db->get()->row_array();
            $label='Open Ticket - ';
            $days=floor($tickdet['hdiff']/24);
            if ($days>0) {
                $hdiff=$tickdet['hdiff']-$days*24;
                $label.=$days.'d '.$hdiff.'h';
            } else {
                $label.=$tickdet['hdiff'].'h';
            }
            $ticket['class']='open';
            $ticket['label']=$label;
        } else {
            $this->db->select('count(ticket_id) as cnt');
            $this->db->from('ts_tickets');
            $this->db->where('order_num', $res['order_num']);
            $this->db->where('ticket_closed',1);
            $tickres=$this->db->get()->row_array();
            if ($tickres['cnt']>0) {
                $ticket['label']='Closed Ticket';
            }
        }
        $out['ticket']=$ticket;
        // Get Artwork
        $this->load->model('artwork_model');
        $this->load->model('artlead_model');
        $artwork=$this->artwork_model->get_artwork_order($order_id, $user_id);
        /* Fetch data into Common Part */
        $artstage=$curstage='';
        if ($res['order_art']==0) {
            $curstage=$this->NO_ART;
            $artstage=$this->NO_ART_TXT;
            $diff=time()-$res['create_date'];
        } elseif ($res['order_redrawn']==0) {
            $curstage=$this->REDRAWN;
            $artstage=$this->REDRAWN_TXT;
            $diff=time()-$res['order_art_update'];
        } elseif ($res['order_vectorized']==0) {
            $curstage=$this->NO_VECTOR;
            $artstage=$this->REDRAWN_TXT;
            $diff=time()-$res['order_redrawn_update'];
        } elseif ($res['order_proofed']==0) {
            $curstage=$this->TO_PROOF;
            $artstage=$this->TO_PROOF_TXT;
            $diff=time()-$res['order_vectorized_update'];
        } elseif ($res['order_approved']==0) {
            $curstage=$this->NEED_APPROVAL;
            $artstage=$this->NEED_APPROVAL_TXT;
            $diff=time()-$res['order_proofed_update'];
        } else {
            $curstage=$this->JUST_APPROVED;
            $artstage=$this->JUST_APPROVED_TXT;
            $diff=time()-$res['order_proofed_update'];
        }
        $artwork['artstage']=$curstage;
        $artwork['artstage_txt']=$artstage;
        $artlabel='';
        $days=floor($diff/(24*60*60));
        if ($days>0) {
            $diff=$diff-$days*(24*60*60);
            $artlabel.=$days.'d ';
        } else {
            $hours=floor($diff/(60*60));
            if ($hours>0) {
                $artlabel.=$hours.'h ';
                $diff=$diff-$hours*(60*60);
            }
            $min=floor($diff/60);
            $artlabel.=$min.'m';
        }
        $artwork['artstage_time']=$artlabel;
        $out['artwork']=$artwork;

        $artwork_id=$artwork['artwork_id'];
        $out['message']=array(
            'general_notes'=>$artwork['general_notes'],
            'history'=>$artwork['art_history'],
            'update'=>'',
        );
        // Get Artwork Locations
        $locations=$this->artlead_model->get_art_locations($artwork_id);
        $proofdocs=$this->artwork_model->get_artproofs($artwork_id);
        $out['artlocations']=$locations;
        $out['proofdocs']=$proofdocs;
        if ($out['order_system_type']=='new') {
            // Get Contacts
            $out['contacts']=$this->get_order_contacts($order_id);
            // Get Order Items
            $out['order_items']=$this->get_order_items($order_id);
            $item_cost=$item_imprint=0;
            foreach ($out['order_items'] as $item) {
                $item_cost+=$item['item_subtotal'];
                $item_imprint+=$item['imprint_subtotal'];
            }
            $res['item_cost']=$item_cost;
            $res['item_imprint']=$item_imprint;
            // Get Shippings
            $out['shipping']=$this->get_order_shipping($order_id);

            // Get Shipping Address
            $out['shipping_address']=$this->get_order_shippaddress($order_id);
            // Get Billing Infor
            $out['order_billing']=$this->get_order_billing($order_id);
            // Get Charge Info
            $out['charges']=$this->get_order_charges($order_id);
        } else {
            $out['contacts']=$out['order_items']=$out['shipping']=$out['order_billing']=$out['charges']=array();
            $out['shipping_address']=$this->get_orderold_shippaddress($res);
        }
        // Get a Order Shipping status
        $res['shipstatus']=$this->_get_order_shipstatus($out['shipping_address']);
        $out['order']=$res;
        $this->load->model('shipping_model');
        $cnt_options=array(
            'orderby'=>'sort, country_name',
        );
        $out['countries']=$this->shipping_model->get_countries_list($cnt_options);
        return $out;
    }

//    public function get_leadorder_system($order_id, $user_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
//        $datares=$this->get_leadorder($order_id, $user_id);
//        if ($datares['result']==$this->error_result) {
//            $out['msg']=$datares['msg'];
//            return $out;
//        }
//        $out['message']=$datares['message'];
//        $out['artlocations']=$datares['artlocations'];
//        $out['proofdocs']=$datares['proofdocs'];
//        $out['charges']=$datares['charges'];
//        $shipaddr=$datares['shipping_address'];
//
//        $shipaddr[0]['shipping_costs']=array();
//        $out['shipping_address']=$shipaddr;
//        $out['order_billing']=$this->_create_empty_billddress();
//        $out['countries']=$datares['countries'];
//        $out['payments']=$datares['payments'];
//        $out['prvorder']=$datares['prvorder'];
//        $out['nxtorder']=$datares['nxtorder'];
//        $out['total_due']=$datares['total_due'];
//        $out['numtickets']=$datares['numtickets'];
//        $out['ticket']=$datares['ticket'];
//
//        $artwork=$datares['artwork'];
//        $out['artwork']=$artwork;
//        $contacts=$datares['contacts'];
//        if (count($contacts)<3) {
//            $cntcontact=  count($contacts)+1;
//            for ($i=$cntcontact; $i<4 ; $i++) {
//                $contacts[]=array(
//                    'order_contact_id'=>($i)*(-1),
//                    'order_id'=>$order_id,
//                    'contact_name'=>'',
//                    'contact_phone'=>'',
//                    'contact_emal'=>'',
//                    'contact_art'=>0,
//                    'contact_inv'=>0,
//                    'contact_trk'=>0,
//                );
//            }
//        }
//        $contacts[0]['contact_name']=$artwork['customer_contact'];
//        $contacts[0]['contact_phone']=$artwork['customer_phone'];
//        $contacts[0]['contact_emal']=$artwork['customer_email'];
//        if (!empty($artwork['customer_email']) && $this->func->valid_email_address($artwork['customer_email'])) {
//            $contacts[0]['contact_art']=1;
//            $contacts[0]['contact_inv']=1;
//            $contacts[0]['contact_trk']=1;
//        }
//        $out['contacts']=$contacts;
//        $out['order_system_type']='new';
//        $order=$datares['order'];
//        $order['showbilladdress']=1;
//        $out['order']=$order;
//        // Get Data about Item
//        $item_id=$order['item_id'];
//        $items[]=$this->_get_oldorder_item($item_id, $order['order_qty']);
//        $out['order_items']=$items;
//        $this->load->model('shipping_model');
//        $shipping=$datares['shipping'];
//        $shipping['order_shipping_id']=-1;
//        if ($order['order_blank']==1) {
//            $rush=$this->shipping_model->get_rushlist_blank($item_id, $order['order_date']);
//        } else {
//            $rush=$this->shipping_model->get_rushlist($item_id, $order['order_date']);
//        }
//
//        $shipping['rush_list']=serialize($rush);
//        $shipping['out_rushlist']=$rush;
//        foreach ($rush['rush'] as $row) {
//            if ($row['current']==1) {
//                $shipping['shipdate']=$row['date'];
//                $shipping['rush_price']=$row['price'];
//                $shipping['rush_idx']=$row['id'];
//            }
//        }
//        $shipping['out_eventdate']=$this->empty_htmlcontent;
//        $shipping['event_date']='';
//        $out['shipping']=$shipping;
//        $out['charges']=$this->get_order_charges($order_id);
//
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//
    // New Order
    public function add_newlead_order($user_id, $brand) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $out['result']=$this->success_result;
        $this->db->select('order_system');
        $this->db->from('ts_configs');
        $this->db->limit(1);
        $confres=$this->db->get()->row_array();
        $defsystem=$confres['order_system'];

        $this->load->model('orders_model');
        $ordfld = $this->db->list_fields('ts_orders');
        $data=array();
        foreach ($ordfld as $row) {
            $data[$row]='';
        }
        // Change Fields
        $data['order_id']=0;
        $data['order_num']=$data['order_confirmation']='';
        $data['order_date']=time();
        $data['profit_class']=$this->project_class;
        $data['brand_id']=$this->config->item('default_brand');
        $data['order_usr_repic']=$user_id;
        $data['item_cost']=$data['item_imprint']=0;
        $data['amnt']=0;
        $data['invoice_class']='';
        $data['payment_total']=0;
        $data['balance_manage']=1;
        $data['appaproved']=0;
        $data['credit_app_id']=0;
        $data['credit_appdue']=strtotime(date("Y-m-d", time()) . " +30 days");
        $data['newappcreditlink']=0;
        $data['credit_applink']='';
        $data['is_shipping']=1;
        $data['mischrg_val1']=$data['mischrg_val2']=$data['discount_val']=0;
        $data['showbilladdress']=1;
        $data['brand'] = $brand;
        $out['order_system_type']=$defsystem;
        $out['order']=$data;
        $out['numtickets']=0;
        $out['total_due']=0;
        $out['payment_total']=0;
        // Contacts
        $contacts=array();
        for ($i=1; $i<=3; $i++) {
            $contacts[]=array(
                'order_contact_id'=>($i)*(-1),
                'order_id'=>0,
                'contact_name'=>'',
                'contact_phone'=>'',
                'contact_emal'=>'',
                'contact_art'=>0,
                'contact_inv'=>0,
                'contact_trk'=>0,
            );
        }
        $out['contacts']=$contacts;
        /* List of Items + QTY */
        $out['order_items']=array();
        $artfld=$this->db->list_fields('ts_artworks');
        $art=array();
        foreach ($artfld as $fld) {
            $art[$fld]='';
        }
        $art['artwork_blank']=0;
        $art['artwork_rush'] =0;
        $art['customer_art'] =0;
        $art['customer_inv'] =0;
        $art['customer_track'] =0;
        $art['artstage']=$this->NO_ART;
        $art['artstage_txt']=$this->NO_ART_TXT;
        $art['artstage_time']=$this->empty_htmlcontent;
        $out['artwork']=$art;
        $out['payments']=array();
        $out['artlocations']=$out['proofdocs']=array();
        $this->load->model('user_model');
        $usrdata=$this->user_model->get_user_data($user_id);
        if (!empty($usrdata['user_leadname'])) {
            $addusr=$usrdata['user_leadname'];
        } else {
            $addusr=$usrdata['user_name'];
        }
        $msg='Order was created '.date('m/d/y h:i:s a', time()).' by '.$usrdata['user_name'];
        // Add Record about duplicate
        $newart_history[]=array(
            'artwork_history_id'=>(-1),
            'created_time' =>time(),
            'message' =>$msg,
            'user_name' =>$addusr,
            'user_leadname' =>$addusr,
            'parsed_mailbody' =>'',
            'message_details' =>$msg,
            'history_head'=>$addusr.','.date('m/d/y h:i:s a', time()),
            'out_date'=>date('D - M j, Y', time()),
            'out_subdate'=>date('h:i:s a').' - '.$addusr,
            'parsed_lnk'=>'',
            'parsed_class'=>'',
            'title'=>'',
        );

        $out['message']=array(
            'general_notes'=>'',
            'history'=>$newart_history,
            'update'=>'',
        );
        /* Shipping Data */
        $this->load->model('shipping_model');
        $cnt_options=array(
            'orderby'=>'sort, country_name',
        );
        $countries=$this->shipping_model->get_countries_list($cnt_options);
        // $defcountry=$countries[0]['country_id'];
        $defcountry='';
        // Get List of states
        $states=$this->shipping_model->get_country_states($defcountry);
        $out['countries']=$countries;

        $defstate=NULL;

        $newaddr=$this->_create_empty_shipaddress();
        $newaddr['order_shipaddr_id']=-1;
        $shipaddres[]=$newaddr;

        $shpfld=$this->db->list_fields('ts_order_shippings');
        $shipping=array();
        foreach ($shpfld as $fld) {
            $shipping[$fld]='';
        }
        $shipping['order_shipping_id']=-1;
        $shipping['out_eventdate']=$shipping['out_arrivedate']=$shipping['out_shipdate']='';
        $shipping['arriveclass']='';
        $shipping['rush_list']='';
        $shipping['out_rushlist']=array();
        $shipping['rush_price']=0;
        $out['shipping_address']=$shipaddres;
        $out['shipping']=$shipping;
        $billfld=$this->db->list_fields('ts_order_billings');
        $billing=array();
        foreach ($billfld as $fld) {
            $billing[$fld]='';
        }
        $billing['order_billing_id']=-1;
        $billing['state_id']=$defstate;
        $defcountry=$countries[0]['country_id'];
        $billing['country_id']=$defcountry;

        $out['order_billing']=$billing;
        // Payments
        $payfld=$this->db->list_fields('ts_order_payments');
        $charges=array();
        $out['charges']=$charges;
        return $out;
    }

    // Get List of payments
    public function get_leadorder_payments($order_id) {
        $this->db->select('*');
        $this->db->from('ts_order_batches');
        $this->db->where('order_id', $order_id);
        // $this->db->where('batch_received', 1);
        $this->db->where('batch_term',  0);
        $this->db->order_by('batch_date');
        $res = $this->db->get()->result_array();
        $out = array();
        foreach ($res as $row) {
            $row['out_date'] = date('m/d/y', $row['batch_date']);
            if ($row['batch_type']=='American Express') {
                $row['batch_type']='AmEx';
            }
            if (abs($row['batch_vmd']) > 0) {
                if (!empty($row['batch_type'])) {
                    $row['out_name'] = $row['batch_type'] . ' ' . substr($row['batch_num'], -4);
                } else {
                    $row['out_name'] = 'VMD';
                }
            } elseif (abs($row['batch_amex']) > 0) {
                if (!empty($row['batch_type'])) {
                    $row['out_name'] = $row['batch_type'] . ' ' . substr($row['batch_num'], -4);
                } else {
                    $row['out_name'] = 'AmEx';
                }
            } elseif (abs($row['batch_term']) > 0) {
                $row['out_name'] = 'Term';
            } elseif (abs($row['batch_other'])) {
                if (!empty($row['batch_type'])) {
                    $row['out_name']=$row['batch_type'].' '.$row['batch_num'];
                } else {
                    $row['out_name'] = 'Other';
                }
            } elseif (abs($row['batch_writeoff']) >0 ) {
                $row['out_name'] = 'WriteOFF';
            }
            if ($row['batch_amount'] < 0) {
                $row['payclass'] = 'text_red';
                $row['paysum'] = '(' . MoneyOutput(abs($row['batch_amount'])) . ')';
            } else {
                $row['payclass'] = 'text_grey';
                $row['paysum'] = MoneyOutput(abs($row['batch_amount']));
            }
            $out[] = $row;
        }
        return $out;
    }

    // Change Order data
    public function change_order_input($leadorder, $entity, $fldname, $newval, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $data=$leadorder[$entity];
        $this->load->model('shipping_model');
        if (array_key_exists($fldname,$data)) {
            if ($fldname=='item_id' && $entity=='order') {
                // Search DB Item
                $srchres=$this->_get_oldorder_itemdata($newval);
                if ($srchres['result']==$this->error_result) {
                    $out['msg']='Item Not Found';
                    return $out;
                }
                $data['order_itemnumber']=$srchres['item_number'];
                $data['order_items']=$srchres['item_name'];
            }
            $data[$fldname]=$newval;
            if ($fldname=='balance_manage' && $entity=='order') {
                if ($newval==3) {
                    if ($data['balance_term']=='') {
                        $data['balance_term']='School';
                    }
                }
            }
            if ($entity=='shipping' && $fldname=='event_date') {
                $data['out_eventdate']=date('m/d/y',$newval);
            }
            $newshipcalc = 0;
            if ($entity=='order' && $fldname=='order_date') {
                $shipping = $leadorder['shipping'];
                if (!empty($shipping['rush_idx'])) {
                    $newshipcalc = 1;
                }
            }
            $leadorder[$entity]=$data;
            if ($fldname=='rush_idx') {
                $params=explode("-", $newval);
                $shipping=$leadorder['shipping'];
                $rushallow=1;
                if (isset($params[0])) {
                    $shipping['shipdate']=$params[0];
                    $shipping['out_shipdate']=date('m/d/y', $params[0]);
                    // Analyze
                    $chklist=$shipping['out_rushlist']['rush'];
                    foreach ($chklist as $rrow) {
                        if ($rrow['date']==$params[0] && $rrow['rushterm']!='Standard') {
                            $rushallow=0;
                            $leadorder['artwork']['artwork_rush']=1;
                            $leadorder['order']['order_rush']=1;
                            break;
                        }
                    }
                }
                $out['rushallow']=$rushallow;
                if (isset($params[1])) {
                    $shipping['rush_price']=$params[1];
                }
                $leadorder['shipping']=$shipping;
                // Start
                $order=$leadorder['order'];
                $order['shipdate']=$shipping['shipdate'];
                // Calculate shipping
                $this->load->model('shipping_model');
                $shiprate=0;
                $items=$leadorder['order_items'];
                $shipaddr=$leadorder['shipping_address'];
                $shipidx=0;
                $cnt=0;
                foreach ($shipaddr as $shprow) {
                    if (!empty($shipaddr[$shipidx]['zip'])) {
                        // Get Old Shipping Method
                        $default_ship_method='';
                        if (isset($shprow['shipping_cost'])) {
                            $oldcosts=$shprow['shipping_costs'];
                            foreach ($oldcosts as $costrow) {
                                if ($costrow['delflag']==0 && $costrow['current']==1) {
                                    $default_ship_method=$costrow['shipping_method'];
                                }
                            }
                        }
                        $cntres=$this->shipping_model->count_shiprates($items, $shipaddr[$shipidx], $shipping['shipdate'], $order['brand'], $default_ship_method);
                        if ($cntres['result']==$this->error_result) {
                            $out['msg']=$cntres['msg'];
                            return $out;
                        } else {
                            $rates=$cntres['ships'];
                            $shipcost=$shipaddr[$shipidx]['shipping_costs'];
                            $cidx=0;
                            foreach ($shipcost as $row) {
                                $shipcost[$cidx]['delflag']=1;
                                $cidx++;
                            }
                            $newidx=count($shipcost)+1;
                            foreach ($rates as $row) {
                                $shipcost[]=array(
                                    'order_shipcost_id'=>$newidx*(-1),
                                    'shipping_method'=>$row['ServiceName'],
                                    'shipping_cost'=>$row['Rate'],
                                    'arrive_date'=>$row['DeliveryDate'],
                                    'current'=>$row['current'],
                                    'delflag'=>0,
                                );
                                if ($row['current']==1) {
                                    $shipaddr[$shipidx]['shipping']=$row['Rate'];
                                    $shipaddr[$shipidx]['arrive_date']=$row['DeliveryDate'];
                                    $shiprate+=$row['Rate'];
                                }
                                $newidx++;
                            }
                            $shipaddr[$shipidx]['shipping_costs']=$shipcost;
                        }
                        $shipidx++;
                        $cnt++;
                    }
                }
                $order['shipping']=$shiprate;
                if ($cnt==1) {
                    $out['shipaddr']=$shipaddr[0];
                } else {
                    $out['shipaddress']=$shipaddr;
                }
                // Save data into Session
                $leadorder['order']=$order;
                $leadorder['shipping']=$shipping;
                $leadorder['shipping_address']=$shipaddr;
            } elseif ($fldname=='country_id' && $entity=='billing') {
                $states=$this->shipping_model->get_country_states($newval);
                // remove default State
                $out['defstate']=NULL;
                $data['state_id']=NULL;

                $out['out_states']=$states;
                $leadorder['billing']=$data;
            } elseif ($fldname=='shipping' && $entity=='order') {
                $shipaddr=$leadorder['shipping_address'];
                if (count($shipaddr)==1) {
                    $shipaddr[0]['shipping']=$newval;
                    $leadorder['shipping_address']=$shipaddr;
                }
            } elseif ($fldname=='showbilladdress' && $entity=='order') {
                $cnt_options=array(
                    'orderby'=>'sort, country_name',
                );
                $out['countries']=$this->shipping_model->get_countries_list($cnt_options);
                $billing=$leadorder['billing'];
                $states=array();
                if (!empty($billing['country_id'])) {
                    $cntid=$billing['country_id'];
                    $states=$this->shipping_model->get_country_states($cntid);
                }
                $out['states']=$states;
            }
            if ($newshipcalc==1) {
                // Calc new shipping time
                $this->load->model('shipping_model');
                $order = $leadorder['order'];
                $item_id = $order['item_id'];
                if ($order['order_blank']==0) {
                    $rush=$this->shipping_model->get_rushlist($item_id, $order['order_date']);
                } else {
                    $rush=$this->shipping_model->get_rushlist_blank($item_id, $order['order_date']);
                }
                $out['rushlist']=$rush;
                $shipping=$leadorder['shipping'];
                $shipping['rush_list']=serialize($rush);
                $shipping['out_rushlist']=$rush;
                foreach ($rush['rush'] as $row) {
                    if ($row['current']==1) {
                        $shipping['shipdate']=$row['date'];
                        $shipping['rush_price']=$row['price'];
                        $shipping['rush_idx']=$row['id'];
                        $order['shipdate']=$row['date'];
                        $out['current']=$row['id'];
                    }
                }
                $leadorder['shipping']=$shipping;
                $leadorder['order']=$order;
                $out['shipdate']=$shipping['shipdate'];
                $out['rush_price']=$shipping['rush_price'];
                // Calculate shipping
                $this->load->model('shipping_model');
                $shiprate=0;
                $items=$leadorder['order_items'];
                $shipaddr=$leadorder['shipping_address'];
                if (count($shipaddr)==1) {
                    $shipaddr[0]['item_qty']=$order['order_qty'];
                }
                $shipping=$leadorder['shipping'];
                $shipidx=0;
                $cnt=0;
                foreach ($shipaddr as $shprow) {
                    if (!empty($shprow['zip'])) {
                        // Get Old Shipping Method
                        $default_ship_method='';
                        if (isset($shprow['shipping_cost'])) {
                            $oldcosts=$shprow['shipping_costs'];
                            foreach ($oldcosts as $costrow) {
                                if ($costrow['delflag']==0 && $costrow['current']==1) {
                                    $default_ship_method=$costrow['shipping_method'];
                                }
                            }
                        }
                        $cntres=$this->shipping_model->count_shiprates($items, $shipaddr[$shipidx], $shipping['shipdate'], $order['brand'], $default_ship_method);
                        if ($cntres['result']==$this->error_result) {
                            $out['msg']=$cntres['msg'];
                            usersession($ordersession, $leadorder);
                            return $out;
                        } else {
                            $rates=$cntres['ships'];
                            $shipcost=$shipaddr[$shipidx]['shipping_costs'];
                            $cidx=0;
                            foreach ($shipcost as $row) {
                                $shipcost[$cidx]['delflag']=1;
                                $cidx++;
                            }
                            $newidx=count($shipcost)+1;
                            foreach ($rates as $row) {
                                $shipcost[]=array(
                                    'order_shipcost_id'=>$newidx*(-1),
                                    'shipping_method'=>$row['ServiceName'],
                                    'shipping_cost'=>$row['Rate'],
                                    'arrive_date'=>$row['DeliveryDate'],
                                    'current'=>$row['current'],
                                    'delflag'=>0,
                                );
                                if ($row['current']==1) {
                                    $shipaddr[$shipidx]['shipping']=$row['Rate'];
                                    $shipaddr[$shipidx]['arrive_date']=$row['DeliveryDate'];
                                    $shiprate+=$row['Rate'];
                                }
                                $newidx++;
                            }
                            $shipaddr[$shipidx]['shipping_costs']=$shipcost;
                        }
                    }
                    $shipidx++;
                    $cnt++;
                }
                $out['shipping']=$shiprate;
                $order['shipping']=$shiprate;
                $out['cntshipadrr']=$cnt;
                if ($cnt==1) {
                    $out['shipaddr']=$shipaddr[0];
                } else {
                    $out['shipaddress']=$shipaddr;
                }
                // Save data into Session
                $leadorder['order']=$order;
                $leadorder['shipping']=$shipping;
                $leadorder['shipping_address']=$shipaddr;
            }
            $out['result']=$this->success_result;
            $out['shipcalc'] = $newshipcalc;
            usersession($ordersession, $leadorder);
            // Rebuild Totals of order
            if ($leadorder['order_system']=='new') {
                $this->_leadorder_totals($leadorder, $ordersession);
            }
        } else {
            $out['msg']='Field not found';
        }
        return $out;
    }

    // Save Order item
    public function save_item($leadorder, $item_id, $custom_item, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        $this->load->model('orders_model');
        $itemdata=$this->orders_model->get_itemdat($item_id);
        $order['item_id']=$itemdata['item_id'];
        $order['order_itemnumber']=$itemdata['item_number'];
        if ($order['item_id']<0) {
            $order['order_items']=$custom_item;
            $out['item_name']=$custom_item;
        } else {
            $order['order_items']=$itemdata['item_name'];
            $out['item_name']=$itemdata['item_name'];
        }
        $out['result']=$this->success_result;
        $out['item_number']=$itemdata['item_number'];
        $leadorder['order']=$order;
        usersession($ordersession, $leadorder);
        return $out;
    }

    // Change Profit
    public function change_profit($leadorder, $fldname, $newval,$ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        if (!isset($order[$fldname])) {
            $out['msg']='Parameter '.$fldname.' Not Declared';
        }
        if ($fldname=='cc_fee') {
            if ($newval==0) {
                $order['cc_fee']=0;
            } else {
                $ccdef=$this->config->item('default_ccfee');
                $new_ccfee=round(($order['revenue']*$ccdef/100),2);
                $order['cc_fee']=$new_ccfee;
            }
        } else {
            $order[$fldname]=floatval($newval);
        }
        // Count New Profit
        $out['result']=$this->success_result;
        $profit_class=orderProfitClass($order['profit_perc']);
        if (floatval($order['order_cog'])!=0) {
            /* Update Profit */
            $profit=$this->_leadorder_profit($order);
            if (floatval($order['revenue'])!=0) {
                $profit_perc=round($profit/$order['revenue']*100,1);
                // $this->load->model('order_model');
                $profit_class=orderProfitClass($profit_perc);
            } else {
                $profit_perc=NULL;
                $profit_class='PROJ';
            }
            $order['profit']=$profit;
            $order['profit_perc']=$profit_perc;
        }
        $leadorder['order']=$order;
        usersession($ordersession, $leadorder);
        // Prepare order profit response
        $out['profit']=$order['profit'];
        $out['profit_perc']=$order['profit_perc'];
        $out['profit_class']=$profit_class;
        $subtotal=floatval($order['revenue'])-floatval($order['shipping'])-floatval($order['tax']);
        $out['subtotal']=MoneyOutput($subtotal,2);
        return $out;
    }

    public function edit_contact($leadorder, $contact_id, $fldname, $newval, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if ($fldname=='contact_emal' && !empty($newval)) {
            if (!valid_email_address($newval)) {
                $out['msg']='Contact Email Incorrect';
                return $out;
            }
        }
        $contacts=$leadorder['contacts'];
        $found=0;
        $idx=0;
        foreach ($contacts as $row) {
            if ($row['order_contact_id']==$contact_id) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Contact Data Not Exist';
            return $out;
        }
        $contacts[$idx][$fldname]=$newval;
        if ($fldname=='contact_emal') {
            if (!valid_email_address($newval)) {
                $contacts[$idx]['contact_art']=0;
                $contacts[$idx]['contact_inv']=0;
                $contacts[$idx]['contact_trk']=0;
            }
            $out['currec']=$contacts;
        }
        if ($fldname=='contact_phone') {
            if (!empty($newval)) {
                $phone=str_replace('-', '', $newval);
                $phonenum=formatPhoneNumber($phone,1);
                $contacts[$idx]['contact_phone']=$phonenum;
            }
        }
        $leadorder['contacts']=$contacts;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        return $out;
    }

    // Save item for new type of Order
    public function save_order_items($leadorder, $item_id, $custom_item, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order_items=$leadorder['order_items'];
        $order=$leadorder['order'];
        $this->load->model('orders_model');
        if ($item_id<0) {
            $itemdata=$this->orders_model->get_newitemdat($item_id);
        } else {
            $itemdata=$this->_get_itemdata($item_id);
        }
        $colors=$itemdata['colors'];
        $itmcolor='';
        if ($itemdata['num_colors']>0) {
            $itmcolor=$colors[0];
        }
        $newid=count($order_items)+1;
        if ($item_id<0) {
            $item_description=$custom_item;
        } else {
            $item_description=$itemdata['item_name'];
        }
        $defqty=$this->config->item('defqty_common');
        if ($item_id==$this->config->item('custom_id')) {
            $defqty=$this->config->item('defqty_custom');
        }

        // Prepare Parts of Order Items
        $orditem=array(
            'order_item_id'=>$newid*(-1),
            'item_id'=>$item_id,
            'item_number'=>$itemdata['item_number'],
            'item_name'=>$item_description,
            'item_qty'=>$defqty,
            'colors'=>$itemdata['colors'],
            'num_colors'=>$itemdata['num_colors'],
            'item_qty'=>$defqty,
            'item_template'=>$this->normal_template,
            'item_weigth'=>0,
            'cartoon_qty'=>0,
            'cartoon_width'=>0,
            'cartoon_heigh'=>0,
            'cartoon_depth'=>0,
            'boxqty'=>'',
            'setup_price'=>0,
            'print_price'=>0,
            'base_price' => 0,
            'imprint_locations'=>array(),
            'item_subtotal'=>0,
            'imprint_subtotal'=>0,
            'vendor_zipcode'=>$this->default_zip,
            'charge_perorder'=>0,
            'charge_peritem'=>0,
        );
        $newprice=0;
        if ($item_id>0) {
            // Prices, totals
            $newprice=$this->_get_item_priceqty($item_id, $orditem['item_template'] , $defqty);
            $setupprice=$this->_get_item_priceimprint($item_id, 'setup');
            $printprice=$this->_get_item_priceimprint($item_id, 'imprint');
            $orditem['item_template']=$itemdata['item_template'];
            $orditem['item_weigth']=$itemdata['item_weigth'];
            $orditem['cartoon_qty']=$itemdata['cartoon_qty'];
            $orditem['cartoon_width']=$itemdata['cartoon_width'];
            $orditem['cartoon_heigh']=$itemdata['cartoon_heigh'];
            $orditem['cartoon_depth']=$itemdata['cartoon_depth'];
            $orditem['boxqty']=$itemdata['boxqty'];
            $orditem['setup_price']=$setupprice;
            $orditem['print_price']=$printprice;
            $orditem['base_price']=$newprice;
            $orditem['imprint_locations']=$itemdata['imprints'];
            $orditem['vendor_zipcode']=$itemdata['vendor_zipcode'];
            $orditem['charge_perorder']=$itemdata['charge_perorder'];
            $orditem['charge_pereach']=$itemdata['charge_pereach'];
            $orditem['item_subtotal']=$defqty*$newprice;
        }

        if (count($order_items)==0) {
            $order['order_items']=$orditem['item_name'];
            $order['order_itemnumber']=$orditem['item_number'];
            $order['item_id']=$orditem['item_id'];
        } else {
            $orditem_id=$this->config->item('multy_id');
            $this->load->model('orders_model');
            $orditm=$this->orders_model->get_itemdat($orditem_id);
            $order['item_id']=$orditem_id;
            $order['order_items']=$orditm['item_name'];
            $order['order_itemnumber']=$orditm['item_number'];
        }
        $order['order_qty']=intval($order['order_qty'])+intval($defqty);
        //
        $this->load->model('shipping_model');
        if ($order['order_blank']==0) {
            $rush=$this->shipping_model->get_rushlist($item_id, $order['order_date']);
        } else {
            $rush=$this->shipping_model->get_rushlist_blank($item_id, $order['order_date']);
        }
        $out['rushlist']=$rush;
        $shipping=$leadorder['shipping'];
        $shipping['rush_list']=serialize($rush);
        $shipping['out_rushlist']=$rush;
        foreach ($rush['rush'] as $row) {
            if ($row['current']==1) {
                $shipping['shipdate']=$row['date'];
                $shipping['rush_price']=$row['price'];
                $shipping['rush_idx']=$row['id'];
                $order['shipdate']=$row['date'];
                $out['current']=$row['id'];
            }
        }
        $leadorder['shipping']=$shipping;
        $leadorder['order']=$order;
        $out['shipdate']=$shipping['shipdate'];
        $out['rush_price']=$shipping['rush_price'];
        // Prepare firt item (as itemcolors)
        $newitem=array(
            'order_item_id'=>$newid*(-1),
            'item_id'=>-1,
            'item_row'=>1,
            'item_number'=>$itemdata['item_number'],
            'item_color'=>$itmcolor,
            'colors'=>$colors,
            'num_colors'=>$itemdata['num_colors'],
            'item_description'=>$orditem['item_name']
        );
        //
        if ($itemdata['num_colors']==0) {
            $newitem['out_colors']=$this->empty_htmlcontent;
        } else {
            $options=array(
                'order_item_id'=>$newitem['order_item_id'],
                'item_id'=>$newitem['item_id'],
                'colors'=>$newitem['colors'],
                'item_color'=>$newitem['item_color'],
            );
            $newitem['out_colors']=$this->load->view('leadorderdetails/item_color_choice', $options, TRUE);
        }
        if ($newitem['num_colors']>1) {
            $newitem['item_color_add']=1;
        } else {
            $newitem['item_color_add']=0;
        }

        $newitem['item_qty']=$defqty;
        $newitem['item_price']=$newprice;
        $newitem['item_subtotal']=MoneyOutput($defqty*$newprice);
        $newitem['printshop_item_id']=(isset($itemdata['printshop_item_id']) ? $itemdata['printshop_item_id']  : '');
        $newitem['qtyinput_class']='normal';
        $newitem['qtyinput_title']='';
        $items[]=$newitem;

        $orditem['items']=$items;
        // Prepare Imprint, Imprint Details
        $imprint[]=array(
            'order_imprint_id'=>-1,
            'imprint_description'=>'&nbsp;',
            'imprint_qty'=>0,
            'imprint_price'=>0,
            'imprint_item'=>0,
            'imprint_subtotal'=>'&nbsp;',
            'imprint_price_class' => 'normal',
            'imprint_price_title' => '',
            'delflag'=>0,
        );
        $orditem['imprints']=$imprint;
        // Change Imprint Details
        $imprdetails=array();
        $detailfld=$this->db->list_fields('ts_order_imprindetails');
        for ($i=1; $i<13; $i++) {
            $newloc=array(
                'title'=>'Loc '.$i,
                'active'=>0,
            );
            foreach ($detailfld as $row) {
                switch ($row) {
                    case 'order_imprindetail_id':
                        $newloc[$row]=$i*(-1);
                        break;
                    case 'imprint_type':
                        $newloc[$row]='NEW';
                        break;
                    case 'num_colors':
                        $newloc[$row]=1;
                        break;
                    default :
                        $newloc[$row]='';
                }
            }
            if ($i==1) {
                $newloc['print_1']=0;
            } else {
                $newloc['print_1']=$orditem['print_price'];
            }
            $newloc['print_2']=$orditem['print_price'];
            $newloc['print_3']=$orditem['print_price'];
            $newloc['print_4']=$orditem['print_price'];
            $newloc['setup_1']=$orditem['setup_price'];
            $newloc['setup_2']=$orditem['setup_price'];
            $newloc['setup_3']=$orditem['setup_price'];
            $newloc['setup_4']=$orditem['setup_price'];
            $imprdetails[]=$newloc;
        }
        $orditem['imprint_details']=$imprdetails;
        // Add new element to Order Items
        $order_items[]=$orditem;
        $leadorder['order_items']=$order_items;
        // Calculate shipping
        $this->load->model('shipping_model');
        $shiprate=0;
        $items=$leadorder['order_items'];
        $shipaddr=$leadorder['shipping_address'];
        if (count($shipaddr)==1) {
            $shipaddr[0]['item_qty']=$order['order_qty'];
        }
        $shipping=$leadorder['shipping'];
        $shipidx=0;
        $cnt=0;
        foreach ($shipaddr as $shprow) {
            if (!empty($shprow['zip'])) {
                // Get Old Shipping Method
                $default_ship_method='';
                if (isset($shprow['shipping_cost'])) {
                    $oldcosts=$shprow['shipping_costs'];
                    foreach ($oldcosts as $costrow) {
                        if ($costrow['delflag']==0 && $costrow['current']==1) {
                            $default_ship_method=$costrow['shipping_method'];
                        }
                    }
                }
                $cntres=$this->shipping_model->count_shiprates($items, $shipaddr[$shipidx], $shipping['shipdate'], $order['brand'], $default_ship_method);
                if ($cntres['result']==$this->error_result) {
                    $out['msg']=$cntres['msg'];
                    usersession($ordersession, $leadorder);
                    return $out;
                } else {
                    $rates=$cntres['ships'];
                    $shipcost=$shipaddr[$shipidx]['shipping_costs'];
                    $cidx=0;
                    foreach ($shipcost as $row) {
                        $shipcost[$cidx]['delflag']=1;
                        $cidx++;
                    }
                    $newidx=count($shipcost)+1;
                    foreach ($rates as $row) {
                        $shipcost[]=array(
                            'order_shipcost_id'=>$newidx*(-1),
                            'shipping_method'=>$row['ServiceName'],
                            'shipping_cost'=>$row['Rate'],
                            'arrive_date'=>$row['DeliveryDate'],
                            'current'=>$row['current'],
                            'delflag'=>0,
                        );
                        if ($row['current']==1) {
                            $shipaddr[$shipidx]['shipping']=$row['Rate'];
                            $shiprate+=$row['Rate'];
                        }
                        $newidx++;
                    }
                    $shipaddr[$shipidx]['shipping_costs']=$shipcost;
                }
            }
            $shipidx++;
            $cnt++;
        }
        $out['shipping']=$shiprate;
        $order['shipping']=$shiprate;
        $out['cntshipadrr']=$cnt;
        if ($cnt==1) {
            $out['shipaddr']=$shipaddr[0];
        } else {
            $out['shipaddress']=$shipaddr;
        }
        // Save data into Session
        $leadorder['order']=$order;
        $leadorder['shipping']=$shipping;
        $leadorder['shipping_address']=$shipaddr;
        usersession($ordersession, $leadorder);

        $out['result']=$this->success_result;
        $out['order_items']=$order_items;
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Remove Order Item
    public function remove_order_item($leadorder, $order_item_id, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $delrecords=$leadorder['delrecords'];
        $order_items=$leadorder['order_items'];
        $order=$leadorder['order'];
        $shipping=$leadorder['shipping'];
        $shipping_address=$leadorder['shipping_address'];
        $artlocations=$leadorder['artlocations'];
        $found=0;
        $neworder=array();
        $item_qty=0;
        foreach ($order_items as $row) {
            if ($row['order_item_id']==$order_item_id) {
                $found=1;
                if ($order_item_id>0) {
                    $delrecords[]=array(
                        'entity'=>'order_items',
                        'id'=>$order_item_id,
                    );
                }
                foreach ($row['imprint_details'] as $irow) {
                    if (!empty($irow['artwork_art_id'])) {
                        $artlocidx=0;
                        foreach ($artlocations as $artlrow) {
                            if ($artlrow['artwork_art_id']==$irow['artwork_art_id']) {
                                $artlocations[$artlocidx]['deleted']='del';
                            }
                            $artlocidx++;
                        }
                    }
                }
            } else {
                $neworder[]=$row;
                $item_qty+=$row['item_qty'];
            }
        }
        if ($found==0) {
            $out['msg']='Record Not Found';
            return $out;
        }
        // Rebuild Shipping
        if (count($neworder)==0) {
            // We remove last item
            $out['rushlist']=array(
                'rush'=>array(),
            );
            $out['current']='';
            // Rebuild Shipping
            $shipping['rush_idx']='';
            $shipping['rush_list']='';
            $shipping['rush_price']=0.00;
            $shipping['shipdate']='';
            $shipping['out_rushlist'] =array(
                'rush'=>array(),
                'current_rush' =>0,
            );
            $order['order_qty']=0;
        } else {
            $order['order_qty']=$item_qty;
            // Get First Item Id
            // Rebuild Rush View
            $item_id=$neworder[0]['item_id'];
            $this->load->model('shipping_model');
            if ($order['order_blank']==1) {
                $rush=$this->shipping_model->get_rushlist_blank($item_id, $order['order_date']);
            } else {
                $rush=$this->shipping_model->get_rushlist($item_id, $order['order_date']);
            }
            $out['rushlist']=$rush;
            $shipping['rush_list']=serialize($rush);
            $shipping['out_rushlist']=$rush;
            foreach ($rush['rush'] as $row) {
                if ($row['current']==1) {
                    $shipping['shipdate']=$row['date'];
                    $shipping['rush_price']=$row['price'];
                    $shipping['rush_idx']=$row['id'];
                    $order['shipdate']=$row['date'];
                    $out['current']=$row['id'];
                }
            }
            $out['shipdate']=$shipping['shipdate'];
            $out['rush_price']=$shipping['rush_price'];
        }
        /* Params - order_items shipping, shipping_address */
        $shpres=$this->_recalc_shipping($neworder, $shipping, $shipping_address, $order['brand']);

        if ($shpres['result']==$this->success_result) {
            $shipping=$shpres['shipping'];
            $shipping_address=$shpres['shipping_address'];
        }

        $leadorder['shipping']=$shipping;
        $leadorder['artlocations']=$artlocations;
        $leadorder['order']=$order;
        $leadorder['delrecords']=$delrecords;
        $leadorder['shipping_address']=$shipping_address;
        $leadorder['order_items']=$neworder;

        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        $out['order_items']=$neworder;
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }


    // Add Item color
    public function add_itemcolor($leadorder, $order_item_id, $item_id, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order_items=$leadorder['order_items'];
        $idx=0;
        $found=0;
        foreach ($order_items as $row) {
            if ($row['order_item_id']==$order_item_id) {
                $found=1;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Order Item Record Not Found';
            return $out;
        }
        $items=$order_items[$idx]['items'];
        // Remove Add color flag
        $itmidx=0;
        foreach ($items as $row) {
            $items[$itmidx]['item_color_add']=0;
            $itmidx++;
        }
        $newid=count($items)+1;
        $colors=$order_items[$idx]['colors'];
        $itemcolor=$colors[0];
        $newitem=array(
            'order_item_id'=>$order_items[$idx]['order_item_id'],
            'item_id'=>$newid*(-1),
            'item_row'=>$newid,
            'item_number'=>$order_items[$idx]['item_number'],
            'item_color'=>$itemcolor,
            'colors'=>$colors,
            'num_colors'=>$order_items[$idx]['num_colors'],
            'item_description'=>$order_items[$idx]['item_name'],
            'qtyinput_class' => 'normal',
            'qtyinput_title' => '',
        );
        if ($order_items[$idx]['num_colors']==0) {
            $newitem['out_colors']=$this->empty_htmlcontent;
        } else {
            $options=array(
                'order_item_id'=>$newitem['order_item_id'],
                'item_id'=>$newitem['item_id'],
                'colors'=>$newitem['colors'],
                'item_color'=>$newitem['item_color'],
            );
            $newitem['out_colors']=$this->load->view('leadorderdetails/item_color_choice', $options, TRUE);
        }
        if ($newitem['num_colors']>1) {
            $newitem['item_color_add']=1;
        } else {
            $newitem['item_color_add']=0;
        }
        $newitem['item_qty']=0;
        if ($order_items[$idx]['item_id']>0) {
            $itemdata=$this->_get_itemdata($order_items[$idx]['item_id']);
            $newitem['printshop_item_id']=$itemdata['printshop_item_id'];
        } else {
            $newitem['printshop_item_id']='';
        }
        $newprice=$this->_get_item_priceqty($order_items[$idx]['item_id'], $order_items[$idx]['item_template'] , $order_items[$idx]['item_qty']);
        $newitem['item_price']=$newprice;
        $newitem['item_subtotal']=MoneyOutput(0);
        $items[]=$newitem;
        // Save
        $order_items[$idx]['items']=$items;
        $leadorder['order_items']=$order_items;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        $out['items']=$order_items[$idx];
        return $out;
    }


    // Change Items List parameter
    public function change_items($leadorder, $order_item_id, $item_id, $fldname, $newval, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        $order_items=$leadorder['order_items'];
        $idx=0;
        $found=0;

        foreach ($order_items as $row) {
            if ($row['order_item_id']==$order_item_id) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Order Item Record Not Found';
            return $out;
        }
        $items=$order_items[$idx]['items'];

        $founditm=0;
        $itmidx=0;

        foreach ($items as $row) {
            if ($row['item_id']==$item_id) {
                $founditm=1;
                break;
            } else {
                $itmidx++;
            }
        }
        if ($founditm==0) {
            $out['msg']='Item Record Not Found';
            return $out;
        }

        if (!array_key_exists($fldname, $items[$itmidx])) {
            $out['msg']='Unknown parameter '.$fldname;
            return $out;
        }
        $imprints=$order_items[$idx]['imprints'];

        $out['result']=$this->success_result;
        $out['price_class']=$items[$itmidx]['qtyinput_class'];
        $out['price_title']=$order_items[$idx]['base_price'];
        $items[$itmidx][$fldname]=$newval;
        $out['items']=$order_items[$idx];
        $out['shipcalc']=0;
        // Get new price for QTY
        if ($fldname=='item_qty') {
            $itemsqty=0;
            foreach ($items as $irow) {
                $itemsqty+=$irow['item_qty'];
            }
            $order_items[$idx]['item_qty']=$itemsqty;
            // Get New price
            $newprice=$this->_get_item_priceqty($order_items[$idx]['item_id'], $order_items[$idx]['item_template'] , $order_items[$idx]['item_qty']);
            $order_items[$idx]['base_price']=$newprice;
            $out['price_class']='normal';
            $ridx=0;
            foreach ($items as $row) {
                $items[$ridx]['item_price']=$newprice;
                $items[$ridx]['qtyinput_class']='normal';
                $ridx++;
            }
            // Recalc Shipping Rates
            $out['shipcalc']=1;
        } elseif ($fldname=='item_color') {
            $options=array(
                'order_item_id'=>$items[$itmidx]['order_item_id'],
                'item_id'=>$items[$itmidx]['item_id'],
                'colors'=>$items[$itmidx]['colors'],
                'item_color'=>$items[$itmidx]['item_color'],
            );
            $items[$itmidx]['out_colors']=$this->load->view('leadorderdetails/item_color_choice', $options, TRUE);
        } elseif ($fldname=='item_price') {
            // Get  Item price
            if($order_items[$idx]['item_id']>0) {
                $newprice = $order_items[$idx]['base_price'];
                // $newprice=$this->_get_item_priceqty($order_items[$idx]['item_id'], $order_items[$idx]['item_template'] , $order_items[$idx]['item_qty']);
                if (floatval(round($items[$itmidx]['item_price'],2))!==floatval(round($newprice,2))) {
                    $items[$itmidx]['qtyinput_class']='warningprice';
                    $out['price_class']=$items[$itmidx]['qtyinput_class'];
                    $out['price_title']=$order_items[$idx]['base_price'];
                } else {
                    $items[$itmidx]['qtyinput_class']='normal';
                    $out['price_class']=$items[$itmidx]['qtyinput_class'];
                    $out['price_title']='';
                }
            }
        }
        $ridx=0;
        $subtotal=0;
        $prices=array();
        $item_subtotals=array();
        $itemsqty=0;
        foreach ($items as $row) {
            $subtotal+=$items[$ridx]['item_price']*$items[$ridx]['item_qty'];
            array_push($prices, $items[$ridx]['item_id'].'|'.PriceOutput($items[$ridx]['item_price']));
            $items[$ridx]['item_subtotal']=  MoneyOutput($items[$ridx]['item_price']*$items[$ridx]['item_qty']);
            array_push($item_subtotals,$items[$ridx]['item_id'].'|'.$items[$ridx]['item_subtotal']);
            $itemsqty+=$items[$ridx]['item_qty'];
            $ridx++;
        }
        $order_items[$idx]['item_subtotal']=$subtotal;
        $out['subtotals']=$item_subtotals;
        $iidx=0;
        $imprsubtotal=0;
        foreach ($imprints as $row) {
            if ($imprints[$iidx]['delflag']==0) {
                if ($row['imprint_item']==1) {
                    $imprints[$iidx]['imprint_qty']=$itemsqty;
                    $imprints[$iidx]['outqty']=$itemsqty;
                    $imprints[$iidx]['imprint_subtotal']=MoneyOutput($itemsqty*$imprints[$iidx]['imprint_price']);
                    $imprsubtotal+=$itemsqty*$imprints[$iidx]['imprint_price'];
                } else {
                    $imprsubtotal+=$imprints[$iidx]['imprint_price']*$imprints[$iidx]['imprint_qty'];
                }
            }
            $iidx++;
        }
        $order_items[$idx]['imprint_subtotal']=$imprsubtotal;
        $out['prices']=$prices;
        $out['item_subtotals']=$item_subtotals;
        // Save to session
        $order_items[$idx]['items']=$items;
        $order_items[$idx]['imprints']=$imprints;
        // Calc # of items
        $order_itemqty=0;
        foreach ($order_items as $row) {
            $order_itemqty+=$row['item_qty'];
        }
        $order['order_qty']=$order_itemqty;
        // Add new val to leadorder array
        $leadorder['order']=$order;
        $leadorder['order_items']=$order_items;
        // If shipping
        if ($out['shipcalc']==1) {
            // Calculate shipping
            $this->load->model('shipping_model');
            $shiprate=0;
            $items=$leadorder['order_items'];
            $shipaddr=$leadorder['shipping_address'];
            if (count($shipaddr)==1) {
                $shipaddr[0]['item_qty']=$order['order_qty'];
            }
            $shipping=$leadorder['shipping'];
            $shipidx=0;
            $cnt=0;
            foreach ($shipaddr as $shprow) {
                if (!empty($shprow['zip'])) {
                    // Get Old Shipping Method
                    $default_ship_method='';
                    if (isset($shprow['shipping_cost'])) {
                        $oldcosts=$shprow['shipping_costs'];
                        foreach ($oldcosts as $costrow) {
                            if ($costrow['delflag']==0 && $costrow['current']==1) {
                                $default_ship_method=$costrow['shipping_method'];
                            }
                        }
                    }
                    $cntres=$this->shipping_model->count_shiprates($items, $shipaddr[$shipidx], $shipping['shipdate'], $order['brand'], $default_ship_method);
                    if ($cntres['result']==$this->error_result) {
                        $out['msg']=$cntres['msg'];
                        usersession($ordersession, $leadorder);
                        return $out;
                    } else {
                        // Old Costs
                        $rates=$cntres['ships'];
                        $shipcost=$shipaddr[$shipidx]['shipping_costs'];
                        $cidx=0;
                        foreach ($shipcost as $row) {
                            $shipcost[$cidx]['delflag']=1;
                            $cidx++;
                        }
                        $newidx=count($shipcost)+1;
                        foreach ($rates as $row) {
                            $shipcost[]=array(
                                'order_shipcost_id'=>$newidx*(-1),
                                'shipping_method'=>$row['ServiceName'],
                                'shipping_cost'=>$row['Rate'],
                                'arrive_date'=>$row['DeliveryDate'],
                                'current'=>$row['current'],
                                'delflag'=>0,
                            );
                            if ($row['current']==1) {
                                $shipaddr[$shipidx]['shipping']=$row['Rate'];
                                $shiprate+=$row['Rate'];
                            }
                            $newidx++;
                        }
                        $shipaddr[$shipidx]['shipping_costs']=$shipcost;
                    }
                }
                $shipidx++;
                $cnt++;
            }
            $out['shipping']=$shiprate;
            $order['shipping']=$shiprate;
            $out['cntshipadrr']=$cnt;
            if ($cnt==1) {
                $out['shipaddr']=$shipaddr[0];
            } else {
                $out['shipaddress']=$shipaddr;
            }
            // Save data into Session
            $leadorder['order']=$order;
            $leadorder['shipping']=$shipping;
            $leadorder['shipping_address']=$shipaddr;
            usersession($ordersession, $leadorder);
        }
        $leadorder['order']=$order;
        usersession($ordersession, $leadorder);
        $out['items']=$order_items[$idx];
        $out['shipping']=$leadorder['shipping'];
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Prepare Items Imprint Popup
    public function prepare_imprint_details($leadorder, $order_item_id, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order_items=$leadorder['order_items'];
        $artwork=$leadorder['artwork'];
        $found=0;
        $idx=0;
        foreach ($order_items as $row) {
            if ($row['order_item_id']==$order_item_id) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found=0) {
            $out['msg']='Order Item not Found';
            return $out;
        }
        $out['result']=$this->success_result;
        $out['imprint_details']=$order_items[$idx]['imprint_details'];
        $out['item_id']=$order_items[$idx]['item_id'];
        $out['item_number']=$order_items[$idx]['item_number'];
        $out['order_blank']=$artwork['artwork_blank'];
        $out['imprints']=$order_items[$idx]['imprint_locations'];
        $out['item_name']=$order_items[$idx]['item_name'];
        usersession($ordersession, $leadorder);
        return $out;
    }


    // Change Item Imprint Details Popup
    public function change_imprint_details($imprintdetails, $order_imprindetail_id, $fldname, $newval, $imprintsession) {
        $out=array('result'=>$this->error_result, 'msg'=>'Imprint Location Not Found');
        $details=$imprintdetails['imprint_details'];
        $found=0;
        $detidx=0;
        foreach ($details as $row) {
            if ($row['order_imprindetail_id']==$order_imprindetail_id) {
                $found=1;
                break;
            } else {
                $detidx++;
            }
        }
        if ($found==1) {
            $details[$detidx][$fldname]=$newval;
            if ($fldname=='active' && $newval==1) {
                $imprintdetails['order_blank']=0;
            }
            if ($fldname=='imprint_type') {
                if ($newval=='REPEAT') {
                    $details[$detidx]['setup_1']=0;
                    $details[$detidx]['setup_2']=0;
                    $details[$detidx]['setup_3']=0;
                    $details[$detidx]['setup_4']=0;
                    $out['class']='';
                    if (!empty($details[$detidx]['repeat_note'])) {
                        $out['class']='full';
                    }
                } else {
                    $setupprice=$this->_get_item_priceimprint($imprintdetails['item_id'], 'setup');
                    $out['setup']=$setupprice;
                    $details[$detidx]['setup_1']=$setupprice;
                    $details[$detidx]['setup_2']=$setupprice;
                    $details[$detidx]['setup_3']=$setupprice;
                    $details[$detidx]['setup_4']=$setupprice;
                }
            }
            $imprintdetails['imprint_details']=$details;
            usersession($imprintsession, $imprintdetails);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    // Get Repeat Note
    public function get_repeat_note($imprintdetails, $detail_id, $imprintsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $details=$imprintdetails['imprint_details'];
        $found=0;
        $detidx=0;
        foreach ($details as $row) {
            if ($row['order_imprindetail_id']==$detail_id) {
                $found=1;
                break;
            } else {
                $detidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Imprint Location Not Found';
            return $out;
        }
        $out['repeat_note']=$details[$detidx]['repeat_note'];
        usersession($imprintsession, $imprintdetails);
        $out['result']=$this->success_result;
        return $out;
    }

    // Save Repeat Note
    public function save_repeat_note($imprintdetails, $repeat_note, $detail_id, $imprintsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $details=$imprintdetails['imprint_details'];
        $found=0;
        $detidx=0;
        foreach ($details as $row) {
            if ($row['order_imprindetail_id']==$detail_id) {
                $found=1;
                break;
            } else {
                $detidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Imprint Location Not Found';
            return $out;
        }
        $details[$detidx]['repeat_note']=$repeat_note;
        $imprintdetails['imprint_details']=$details;
        usersession($imprintsession, $imprintdetails);
        $out['result']=$this->success_result;
        return $out;
    }

    // Make Blank order
    public function imprintdetails_blankorder($imprintdetails, $newval, $imprintsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $details=$imprintdetails['imprint_details'];
        $imprintdetails['order_blank']=$newval;
        if ($newval==1) {
            $detidx=0;
            foreach ($details as $row) {
                $details[$detidx]['active']=0;
                $detidx++;
            }
            $imprintdetails['imprint_details']=$details;
        }
        usersession($imprintsession, $imprintdetails);
        $out['result']=$this->success_result;
        return $out;

    }

    // Save imprints
    public function save_imprintdetails($leadorder, $details, $ordersession, $imprintsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        $order_items=$leadorder['order_items'];
        $order_item_id=$details['order_item_id'];
        $imprint_details=$details['imprint_details'];
        $order_blank=intval($details['order_blank']);
        $artwork=$leadorder['artwork'];
        $locations=$leadorder['artlocations'];
        // Lets go - Find Order Items
        $found=0;
        $idx=0;
        foreach ($order_items as $row) {
            if ($row['order_item_id']==$order_item_id) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Order Item Not Found';
            return $out;
        }
        // Check Details
        foreach ($imprint_details as $row) {
            if ($row['active']==1 && $row['imprint_type']=='REPEAT' && empty($row['repeat_note'])) {
                $out['msg']=$row['title'].' Empty Repeat Note';
                return $out;
            }
        }
        // Delete Old Imprints
        $impridx=0;
        $imprints=$order_items[$idx]['imprints'];

        foreach ($imprints as $irow) {
            $imprints[$impridx]['delflag']=1;
            $impridx++;
        }
        // Create Imprint
        $newidx=count($imprints)+1;
        $out['shiprebuild']=0;
        if ($order_blank!=$order['order_blank']) {
            $out['shiprebuild']=1;
            // Rebuild shipping
            $this->load->model('shipping_model');
            $shipping=$leadorder['shipping'];
            if ($order_blank==1) {
                $rush=$this->shipping_model->get_rushlist_blank($order['item_id'], $order['order_date']);
            } else {
                $rush=$this->shipping_model->get_rushlist($order['item_id'], $order['order_date']);
            }

            $shipping['rush_list']=serialize($rush);
            $shipping['out_rushlist']=$rush;
            foreach ($rush['rush'] as $row) {
                if ($row['current']==1) {
                    $shipping['shipdate']=$row['date'];
                    $shipping['rush_price']=$row['price'];
                    $shipping['rush_idx']=$row['id'];
                }
            }
            $leadorder['shipping']=$shipping;
        }

        if ($order_blank==1) {
            // Order Blank
            $imprints[]=array(
                'order_imprint_id'=>$newidx*(-1),
                'imprint_description'=>'blank, no imprinting',
                'imprint_item'=>0,
                'imprint_qty'=>0,
                'imprint_price'=>0,
                'outqty'=>$this->empty_htmlcontent,
                'outprice'=>$this->empty_htmlcontent,
                'imprint_subtotal'=>$this->empty_htmlcontent,
                'imprint_price_class' => 'normal',
                'imprint_price_title' => '',
                'delflag'=>0,
            );
            $imprint_total=0;
        } else {
            $setup_qty=0;
            $setup_total=0;
            $imprint_total=0;
            $extra=array();
            $numpp = 1;
            foreach ($imprint_details as $row) {
                if ($row['active']==1) {
                    // Prepare New Imprints
                    $title=$row['title'];
                    for ($i=1; $i<=$row['num_colors']; $i++) {
                        $imprint_price_class = 'normal';
                        $imprint_price_title = '';
                        $imprtitle=$title.': '.date('jS',strtotime('2015-01-'.$i)).' Color Imprinting';
                        $priceindx='print_'.$i;
                        $setupindx='setup_'.$i;
                        $subtotal=$order_items[$idx]['item_qty']*floatval($row[$priceindx]);
                        if ($row['imprint_type']!=='REPEAT' && $numpp>1) {
                            if ($order_items[$idx]['item_id']>0) {
                                // $newiprint_price = $this->_get_item_priceimprint($order_items[$idx]['item_id'],'imprint');
                                $newiprint_price = $order_items[$idx]['print_price'];
                                if (round(floatval($newiprint_price),2)!=round(floatval($row[$priceindx]),2)) {
                                    $imprint_price_class='warningprice';
                                    $imprint_price_title='Print price '.MoneyOutput($order_items[$idx]['print_price']);
                                }
                            }
                        }
                        $numpp++;
                        $imprint_total+=$subtotal;
                        if ($row['imprint_type']!='REPEAT') {
                            //if (floatval($row[$setupindx])>0) {
                            $setup_qty+=1;
                            $setup_total+=floatval($row[$setupindx]);
                            $imprint_total+=floatval($row[$setupindx]);
                            //}
                        }
                        $imprints[]=array(
                            'order_imprint_id'=>(-1)*$newidx,
                            'imprint_description'=>$imprtitle,
                            'imprint_item'=>1,
                            'imprint_qty'=>$order_items[$idx]['item_qty'],
                            'imprint_price'=>floatval($row[$priceindx]),
                            'outqty'=>($order_items[$idx]['item_qty']==0 ? '---' : $order_items[$idx]['item_qty']),
                            'outprice'=>  MoneyOutput(floatval($row[$priceindx])),
                            'imprint_subtotal'=>  MoneyOutput($subtotal),
                            'imprint_price_class' => $imprint_price_class,
                            'imprint_price_title' => $imprint_price_title,
                            'delflag'=>0,
                        );
                        $newidx++;
                    }
                    if ($row['imprint_type']=='REPEAT') {
                        $extracost=floatval($row['extra_cost']);
                        $imprint_total+=$extracost;
                        // Add Imprint
                        $title='Repeat Setup Charge '.$row['repeat_note'];
                        $extra[]=array(
                            'order_imprint_id'=>(-1)*$newidx,
                            'imprint_description'=>$title,
                            'imprint_item'=>0,
                            'imprint_qty'=>1,
                            'imprint_price'=>floatval($row['extra_cost']),
                            'outqty'=>1,
                            'outprice'=>MoneyOutput($extracost),
                            'imprint_subtotal'=>MoneyOutput($extracost),
                            'imprint_price_class' => 'normal',
                            'imprint_price_title' => '',
                            'delflag'=>0,
                        );
                        $newidx++;
                    }
                }
            }
            if (count($extra)>0) {
                foreach ($extra as $erow) {
                    $imprints[]=$erow;
                }
            }
            if ($setup_total>=0 && $setup_qty>0) {
                $setup_price=0;
                if ($setup_qty>0) {
                    $setup_price=round($setup_total/$setup_qty,2);
                }
                $imprint_price_class='normal';
                $imprint_price_title='';
                if ($order_items[$idx]['item_id']>0) {
                    // $newsetup_price = $this->_get_item_priceimprint($order_items[$idx]['item_id'],'setup');
                    $newsetup_price = $order_items[$idx]['setup_price'];
                    if (round(floatval($newsetup_price),2)!=round(floatval($setup_price),2)) {
                        $imprint_price_class='warningprice';
                        $imprint_price_title='Setup price '.MoneyOutput($order_items[$idx]['setup_price']);
                    }
                }
                $imprints[]=array(
                    'order_imprint_id'=>(-1)*$impridx,
                    'imprint_description'=>'One Time Art Setup Charge',
                    'imprint_item'=>0,
                    'imprint_qty'=>$setup_qty,
                    'imprint_price'=>floatval($setup_price),
                    'outqty'=>$setup_qty,
                    'outprice'=>MoneyOutput($setup_price),
                    'imprint_subtotal'=>  MoneyOutput($setup_total),
                    'imprint_price_class' => $imprint_price_class,
                    'imprint_price_title' => $imprint_price_title,
                    'delflag'=>0,
                );
            }
        }
        // Add / Remove Repeat Locations
        $artlocchange=0;
        $sdx=0;
        foreach ($imprint_details as $row) {
            if ($row['imprint_type']=='REPEAT') {
                if ($row['active']==1) {
                    if (empty($row['artwork_art_id'])) {
                        // Add Location
                        $numrec=count($locations)+1;
                        $fields = $this->db->list_fields('ts_artwork_arts');
                        $newlocation=array();
                        foreach ($fields as $field) {
                            $newlocation[$field]='';
                        }
                        $imprint_details[$sdx]['artwork_art_id']=$numrec*(-1);
                        $newlocation['artwork_id']=$artwork['artwork_id'];
                        $newlocation['artwork_art_id']=$numrec*(-1);
                        $newlocation['art_type']='Repeat';
                        $newlocation['locat_ready']=0;
                        $newlocation['art_ordnum']=$numrec;
                        $newlocation['artlabel']=$this->empty_htmlcontent;
                        $newlocation['redrawchk']=$newlocation['rushchk']=$newlocation['redochk']='&nbsp;';
                        $newlocation['artlabel']='Repeat';
                        $newlocation['order_num']=$row['repeat_note'];
                        $newlocation['repeat_text']=$row['repeat_note'];
                        $newlocation['locat_ready']=1;
                        $newlocation['deleted']='';
                        $locations[]=$newlocation;
                        $artlocchange=1;
                    }
                } else {
                    if (!empty($row['artwork_art_id'])) {
                        // Remove Location
                        $locid=0;
                        foreach ($locations as $row) {
                            if ($row['artwork_art_id']==$row['artwork_art_id']) {
                                $found=1;
                                $locations[$locid]['deleted']='del';
                                $imprint_details[$sdx]['artwork_art_id']=NULL;
                                $artlocchange=1;
                                break;
                            }
                            $locid++;
                        }
                    }
                }
            } else {
                if (!empty($row['artwork_art_id'])) {
                    // Remove Location
                    $locid=0;
                    foreach ($locations as $row) {
                        if ($row['artwork_art_id']==$row['artwork_art_id']) {
                            $found=1;
                            $locations[$locid]['deleted']='del';
                            $imprint_details[$sdx]['artwork_art_id']=NULL;
                            $artlocchange=1;
                            break;
                        }
                        $locid++;
                    }
                }
            }
            $sdx++;
        }
        // Save Imprint Details
        $order_items[$idx]['imprint_details']=$imprint_details;
        $order_items[$idx]['imprint_subtotal']=$imprint_total;
        $artwork['artwork_blank']=$order_blank;
        $leadorder['artwork']=$artwork;
        $leadorder['artlocations']=$locations;
        $order['order_blank']=$order_blank;
        $out['order_blank']=$order_blank;
        $order_items[$idx]['imprints']=$imprints;
        $leadorder['order_items']=$order_items;
        $leadorder['order']=$order;
        usersession($ordersession, $leadorder);
        usersession($imprintsession, NULL);
        $out['result']=$this->success_result;
        $out['item']=$order_items[$idx];
        $out['artlocchange']=$artlocchange;
        if ($artlocchange==1) {
            $out['artlocat']=$locations;
        }
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Save changes in Ship Address
    public function change_shipaddres($leadorder, $shipaddr_id, $fldname, $newval, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message, 'multicity' => 0);
        $this->load->model('shipping_model');
        $shipaddr=$leadorder['shipping_address'];
        $shipping=$leadorder['shipping'];
        $shipidx=0;
        $found=0;
        foreach ($shipaddr as $row) {
            if ($row['order_shipaddr_id']==$shipaddr_id) {
                $found=1;
                break;
            } else {
                $shipidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Addres Not Found';
            return $out;
        }

        $shipaddr[$shipidx][$fldname]=$newval;
        if ($fldname=='country_id') {
            // Get a list of states
            $states=$this->shipping_model->get_country_states($newval);
            $cntdat=$this->shipping_model->get_country($newval);
            $shipaddr[$shipidx]['out_country']=$cntdat['country_iso_code_2'];
            $out['states']=$states;
            // Change country - state empty
            $shipaddr[$shipidx]['state_id']=NULL;
            $shipaddr[$shipidx]['taxcalc']=0;
            $shipaddr[$shipidx]['taxview']=0;

        } elseif ($fldname=='state_id') {
            if ($newval==$this->tax_state) {
                $shipaddr[$shipidx]['taxcalc']=0;
                $shipaddr[$shipidx]['taxview']=1;
                if ($shipaddr[$shipidx]['tax_exempt']==0) {
                    $shipaddr[$shipidx]['taxcalc']=1;
                }
            } else {
                $shipaddr[$shipidx]['taxcalc']=0;
                $shipaddr[$shipidx]['taxview']=0;
            }
            $statedat=$this->shipping_model->get_state($newval);
            $shipaddr[$shipidx]['out_zip']=$statedat['state_code'].' '.$shipaddr[$shipidx]['zip'];
        } elseif ($fldname=='zip') {
            // Try to validate Address
            // $this->load->library('United_parcel_service');
            // $upsserv=new United_parcel_service();
            $items=$leadorder['order_items'];
            $qty=0;
            foreach ($items as $row) {
                $qty+=$row['item_qty'];
            }
            if ($qty>0) {
                if (count($shipaddr)==1) {
                    $shipaddr[$shipidx]['item_qty']=$qty;
                }
                // Old Shipping Method
                $default_ship_method='';
                if (isset($shipaddr[$shipidx]['shipping_costs'])) {
                    $oldcosts=$shipaddr[$shipidx]['shipping_costs'];
                    foreach ($oldcosts as $costrow) {
                        if ($costrow['delflag']==0 && $costrow['current']==1) {
                            $default_ship_method=$costrow['shipping_method'];
                        }
                    }
                }
                $order=$leadorder['order'];
                $cntres=$this->shipping_model->count_shiprates($items, $shipaddr[$shipidx], $shipping['shipdate'], $order['brand'], $default_ship_method);
                if ($cntres['result']==$this->error_result) {
                    $out['msg']=$cntres['msg'];
                    return $out;
                } else {
                    $leadorder['order']=$order;
                    $rates=$cntres['ships'];
                    $shipcost=$shipaddr[$shipidx]['shipping_costs'];
                    $cidx=0;
                    foreach ($shipcost as $row) {
                        $shipcost[$cidx]['delflag']=1;
                        $cidx++;
                    }
                    $newidx=count($shipcost)+1;
                    foreach ($rates as $key=>$row) {
                        $shipcost[]=array(
                            'order_shipcost_id'=>$newidx*(-1),
                            'shipping_method'=>$row['ServiceName'],
                            'shipping_cost'=>$row['Rate'],
                            'arrive_date'=>$row['DeliveryDate'],
                            'current'=>$row['current'],
                            'delflag'=>0,
                        );
                        if ($row['current']==1) {
                            $shipaddr[$shipidx]['shipping']=$row['Rate'];
                        }
                        $newidx++;
                    }
                    $shipaddr[$shipidx]['shipping_costs']=$shipcost;
                    $shiptotal=$this->_leadorder_shipcost($shipaddr);
                    $out['shipping']=$shiptotal;
                    $order['shipping']=$shiptotal;
                }

            }
            // Validate Address
//            if ($shipaddr[$shipidx]['out_country']=='US') {
//                $tracking=$upsserv->validaddress($newval, $shipaddr[$shipidx]['out_country']);
//                if ($tracking['result']==$this->success_result) {
//                    if (!empty($tracking['city'])) {
//                        $shipaddr[$shipidx]['city']=$tracking['city'];
//                    }
//                    if (!empty($tracking['state'])) {
//                        $shipaddr[$shipidx]['state_id']=$tracking['state_id'];
//                        $shipaddr[$shipidx]['out_zip']=$tracking['state'].' '.$newval;
//                        if ($shipaddr[$shipidx]['state_id']==$this->tax_state) {
//                            $shipaddr[$shipidx]['taxcalc']=0;
//                            $shipaddr[$shipidx]['taxview']=1;
//                            if ($shipaddr[$shipidx]['tax_exempt']==0) {
//                                $shipaddr[$shipidx]['taxcalc']=1;
//                            }
//                        } else {
//                            $shipaddr[$shipidx]['taxcalc']=0;
//                            $shipaddr[$shipidx]['taxview']=0;
//                        }
//                    }
//                }
//            }
            // Build select
            if ($shipaddr[$shipidx]['out_country']=='CA') {
                $seachzip = substr($newval,0, 3);
            } else {
                $seachzip = $newval;
            }
            $this->db->select('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code as state, t.state_id, count(c.geoip_city_id) as cntcity');
            $this->db->from('ts_geoipdata gdata');
            $this->db->join('ts_geoip_city c','c.geoname_id=gdata.geoname_id');
            $this->db->join('ts_countries cntr','cntr.country_iso_code_2=c.country_iso_code');
            $this->db->join('ts_states t','t.state_code=c.subdivision_1_iso_code','left');
            $this->db->where('gdata.postal_code',$seachzip);
            $this->db->where('cntr.country_id',$shipaddr[$shipidx]['country_id']);
            $this->db->group_by('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code, t.state_id');
            $this->db->order_by('cntcity','desc');
            $validdata = $this->db->get()->result_array();
            if (count($validdata)>0) {
                $validres = $validdata[0];
                if (count($validdata)>1) {
                    $out['multicity']=1;
                    $shpcity = [];
                    foreach ($validdata as $vrow) {
                        array_push($shpcity,$vrow['city_name']);
                    }
                    $out['validcity']=$shpcity;
                }
                $shipaddr[$shipidx]['city']=$validres['city_name'];
                if ($shipaddr[$shipidx]['out_country']=='US' || $shipaddr[$shipidx]['out_country']=='CA') {
                    if (!empty($validres['state'])) {
                        $shipaddr[$shipidx]['state_id']=$validres['state_id'];
                        $shipaddr[$shipidx]['out_zip']=$validres['state'].' '.$newval;
                        if ($shipaddr[$shipidx]['state_id']==$this->tax_state) {
                            $shipaddr[$shipidx]['taxcalc']=0;
                            $shipaddr[$shipidx]['taxview']=1;
                            if ($shipaddr[$shipidx]['tax_exempt']==0) {
                                $shipaddr[$shipidx]['taxcalc']=1;
                            }
                        } else {
                            $shipaddr[$shipidx]['taxcalc']=0;
                            $shipaddr[$shipidx]['taxview']=0;
                        }
                    }
                } else {
                    $shipaddr[$shipidx]['state_id']='';
                    $shipaddr[$shipidx]['out_zip']=$newval;
                    $shipaddr[$shipidx]['taxcalc']=0;
                    $shipaddr[$shipidx]['taxview']=0;
                }
            }
        } elseif ($fldname=='tax_exempt') {
            if ($newval==0) {
                $shipaddr[$shipidx]['taxcalc']=1;
            } else {
                $shipaddr[$shipidx]['taxcalc']=0;
            }
        }

        $leadorder['shipping_address']=$shipaddr;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        $out['shipadr']=$shipaddr[$shipidx];
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Change Ship Cost Default Method
    public function change_shipaddrescost($leadorder, $shipaddr_id, $fldname, $newval, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipaddr=$leadorder['shipping_address'];
        $found=0;
        $shipidx=0;
        foreach ($shipaddr as $row) {
            if ($row['order_shipaddr_id']==$shipaddr_id) {
                $found=1;
                break;
            } else {
                $shipidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Ship Address Not Found';
            return $out;
        }
        $shipcost=$shipaddr[$shipidx]['shipping_costs'];
        $found=0;
        $costidx=0;
        foreach ($shipcost as $crow) {
            if ($crow['delflag']==0 && $crow['shipping_method']==$newval) {
                $found=1;
                break;
            } else {
                $costidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Method Not Found';
            return $out;
        }
        $out['order_shipcost_id']=$shipcost[$costidx]['order_shipcost_id'];
        $idx=0;
        foreach ($shipcost as $crow) {
            if ($idx==$costidx) {
                $shipcost[$idx]['current']=1;
                $shipaddr[$shipidx]['shipping']=$crow['shipping_cost'];
                $shipaddr[$shipidx]['arrive_date']=$crow['arrive_date'];
            } else {
                $shipcost[$idx]['current']=0;
            }
            $idx++;
        }
        $shipaddr[$shipidx]['shipping_costs']=$shipcost;
        // Change Order params
        $order=$leadorder['order'];
        $shiptotal=$this->_leadorder_shipcost($shipaddr);
        $out['shipping']=$shiptotal;
        $order['shipping']=$shiptotal;
        // $profit=$this->_leadorder_profit($order);
        $leadorder['order']=$order;
        $leadorder['shipping_address']=$shipaddr;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Get Tax data from Shipping Address
    public function get_taxdetails($leadorder, $shipadr, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipadress=$leadorder['shipping_address'];
        $found=0;
        $shpidx=0;
        foreach ($shipadress as $row) {
            if ($row['order_shipaddr_id']==$shipadr) {
                $found=1;
                break;
            } else {
                $shpidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Addres Not Found';
            return $out;
        }
        $taxdata=array(
            'tax_exemptdoc'=>$shipadress[$shpidx]['tax_exemptdoc'],
            'tax_exemptdocid'=>$shipadress[$shpidx]['tax_exemptdocid'],
        );
        $out['taxdata']=$taxdata;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        return $out;
    }

    // Save Tax Extem Document
    public function save_newtaxdoc($leadorder, $shipadr, $newdoc, $srcname, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipadress=$leadorder['shipping_address'];
        $found=0;
        $shpidx=0;
        foreach ($shipadress as $row) {
            if ($row['order_shipaddr_id']==$shipadr) {
                $found=1;
                break;
            } else {
                $shpidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Addres Not Found';
            return $out;
        }
        // Save Data - 1 - Flag New File
        $shipadress[$shpidx]['tax_exemptdocid']=-1;
        // Save Tax doc details
        $shipadress[$shpidx]['tax_exemptdoc']=$newdoc;
        $shipadress[$shpidx]['tax_exemptdocsrc']=$srcname;
        $leadorder['shipping_address']=$shipadress;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        return $out;
    }

    // Save new Credit App document
    public function save_newcreditappdoc($leadorder, $newdoc, $srcname, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        // Save Tax doc details
        $order['newappcreditlink']=-1;
        $order['credit_applink']=$newdoc;
        $leadorder['order']=$order;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        return $out;
    }

    public function change_chargedata($leadorder, $order_payment_id, $fldname, $newval, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $charges=$leadorder['charges'];
        $found=0;
        $chrgidx=0;
        foreach ($charges as $row) {
            if ($row['order_payment_id']==$order_payment_id) {
                $found=1;
                break;
            } else {
                $chrgidx++;
            }
        }
        if ($found==0) {
            $out['msg']='Credit Card Data Not Found';
            return $out;
        }
        if ($fldname=='exp_month' && intval($newval)>12) {
            $out['oldval']=$charges[$chrgidx]['exp_month'];
            $out['msg']='Incorrect Expire Date';
            return $out;
        }
        if (!array_key_exists($fldname, $charges[$chrgidx])) {
            $out['msg']='Unknown Parameter '.$fldname;
            return $out;
        }
        if ($fldname=='cardnum' && !empty($newval)) {
            $newval=creditcard_format($newval);
        }
        $charges[$chrgidx][$fldname]=$newval;
        $out['charge']=$charges[$chrgidx];
        $leadorder['charges']=$charges;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Pay
    public function leadorder_paycharge($leadorder, $order_payment_id, $usr_id, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        $order_id=$order['order_id'];
        $charges=$leadorder['charges'];
        $found=0;
        $chridx=0;
        foreach ($charges as $row) {
            if ($row['order_payment_id']==$order_payment_id) {
                $found=1;
                break;
            } else {
                $chridx++;
            }
        }
        if ($found==0) {
            $out['msg']='Charge Data Not Found';
            return $out;
        }
        $charge=$charges[$chridx];
        $billing=$leadorder['billing'];
        $order_data = $leadorder['order'];
        $cardnum=  str_replace('-', '', $charge['cardnum']);
        $bilchk=0;
        if (empty($billing['customer_name'])) {
            $out['msg']='Enter Customer Name';
        } elseif (empty($billing['address_1'])) {
            $out['msg']='Enter Billing Address';
        } elseif (empty($billing['city'])) {
            $out['msg']='Enter Billing City';
        } elseif (empty($billing['zip'])) {
            $out['msg']='Enter Billing Zip / Postal Code';
        } elseif (floatval($charge['amount'])==0) {
            $out['msg']='Empty Amount Value';
        } elseif(empty($cardnum)) {
            $out['msg']='Enter Card #';
        } elseif(empty ($charge['cardcode'])) {
            $out['msg']='Enter Card CVV2 code';
        } elseif (intval($charge['exp_month'])==0) {
            $out['msg']='Expire Month Incorrect';
        } elseif (intval($charge['exp_year'])==0) {
            $out['msg'] = 'Expire Year Incorrect';
        } elseif (round(floatval($charge['amount']),2)>round(floatval($order_data['revenue']),2)) {
            $out['msg'] = 'Charge Value Great than Order Total';
            log_message('ERROR', 'Charge '.round(floatval($charge['amount']),2).' REVENUE '.round(floatval($order_data['revenue']),2));
        } else {
            $cardtype=$this->getCCardType($cardnum);
            if (empty($cardtype)) {
                $out['msg']='Unknown Credit Card Type';
            } else {
                $bilchk=1;
            }
        }
        if ($bilchk==0) {
            return $out;
        }

        $custdat=explode(' ', $billing['customer_name']);
        $customer_first_name=(isset($custdat[0]) ? $custdat[0] : 'Unknown');
        $customer_last_name=(isset($custdat[1]) ? $custdat[1] : 'Unknown');
        // Get contact name
        $contacts=$leadorder['contacts'];
        $payemail=$payphone='';
        foreach ($contacts as $crow) {
            if (!empty($crow['contact_phone'])) {
                $payphone=$crow['contact_phone'];
            }
            if (!empty($crow['contact_emal'])) {
                $payemail=$crow['contact_emal'];
            }
        }
        $state='UNK';
        if (!empty($billing['state_id'])) {
            $this->db->select('state_code');
            $this->db->from('ts_states');
            $this->db->where('state_id', $billing['state_id']);
            $stat=$this->db->get()->row_array();
            $state=$stat['state_code'];
        }
        $country='UNK';
        if (!empty($billing['country_id'])) {
            $this->db->select('country_iso_code_2');
            $this->db->from('ts_countries');
            $this->db->where('country_id', $billing['country_id']);
            $cntres=$this->db->get()->row_array();
            if (isset($cntres['country_iso_code_2'])) {
                $country=$cntres['country_iso_code_2'];
            }
        }
        // Lets go
        $this->load->model('batches_model');
        $this->load->model('orders_model');
        // Try to pay
        $pay_options=array(
            'email'=>$payemail,
            'company'=>$billing['company'],
            'firstname'=>$customer_first_name,
            'lastname'=>$customer_last_name,
            'address1'=>$billing['address_1'],
            'address2'=>$billing['address_2'],
            'city'=>$billing['city'],
            'state'=>$state,
            'country'=>$country,
            'zip'=>$billing['zip'],
            'phone'=>$payphone,
            'amount'=>$charge['amount'],
            'cardnum'=>$cardnum,
            'cardcode'=>$charge['cardcode'],
            'cardtype'=>($cardtype=='American Express' ? 'Amex' : $cardtype),
            'exp_month'=>str_pad($charge['exp_month'],2,'0', STR_PAD_LEFT),
            'exp_year'=>str_pad($charge['exp_year'],2,'0', STR_PAD_LEFT),
        );
        $transres=$this->order_payment($pay_options);
        if ($transres['result']==$this->error_result) {
            $out['msg']=$transres['error_msg'];
            $cc_options = [
                'amount'=>$charge['amount'],
                'cardnum'=>$cardnum,
                'cardtype'=>($cardtype=='American Express' ? 'Amex' : $cardtype),
                'cardcode'=>$charge['cardcode'],
            ];
            $this->_save_order_paymentlog($order_id, $usr_id, $transres['error_msg'], $cc_options);
        } else {
            $cc_options = [
                'amount'=>$charge['amount'],
                'cardnum'=>$cardnum,
                'cardtype'=>($cardtype=='American Express' ? 'Amex' : $cardtype),
                'cardcode'=>$charge['cardcode'],
            ];
            $this->_save_order_paymentlog($order_id, $usr_id, $transres['transaction_id'], $cc_options, 1);
            // Make Current row Amount=0, Add Charge
            $this->db->set('amount',0);
            $this->db->where('order_payment_id', $row['order_payment_id']);
            $this->db->update('ts_order_payments');
            // Batch data
            $paymethod='';
            if ($pay_options['cardtype']=='amex') {
                $paymethod='a';
            } else {
                $paymethod='v';
            }
            $batch_data=array(
                'batch_id'=>0,
                'batch_date'=>time(),
                'paymethod'=>$paymethod,
                'amount'=>$row['amount'],
                'batch_note'=>NULL,
                'order_id'=>$order_id,
                'batch_received'=>1,
                'batch_type'=>$pay_options['cardtype'],
                'batch_num'=>$pay_options['cardnum'],
                'batch_transaction'=>$transres['transaction_id'],
            );
            $batch_id=$this->batches_model->save_batch($batch_data, $order, $usr_id);
            // Get New list of Payments
            $payments=$this->get_leadorder_payments($order_id);
            $total=0;
            $fee=0;
            foreach ($payments as $row) {
                $total+=$row['batch_amount'];
                $fee+=(($row['batch_amex']+$row['batch_vmd']==0) ? 0 : $row['batch_amount']-($row['batch_amex']+$row['batch_vmd']));
            }
            $order['payment_total']=$total;
            $order['cc_fee']=$fee;
            $leadorder['order']=$order;
            $leadorder['payments']=$payments;
            usersession($ordersession, $leadorder);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function add_chargedata($leadorder, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        $total_due=$order['revenue']-$order['payment_total'];

        $charges=$leadorder['charges'];
        $newidx=count($charges)+1;
        $payfld=$this->db->list_fields('ts_order_payments');
        $newpay=array();
        foreach ($payfld as $fld) {
            switch ($fld) {
                case 'order_payment_id':
                    $newpay[$fld]=(-1)*$newidx;
                    break;
                case 'autopay':
                    $newpay[$fld]=($newidx==1 ? 1 : 0);
                    break;
                case 'amount':
                    $newpay[$fld]=($total_due>0 ? $total_due : 0);
                    break;
                default :
                    $newpay[$fld]='';
                    break;
            }
        }
        $newpay['delflag']=0;
        $charges[]=$newpay;
        $out['charges']=$charges;
        $leadorder['charges']=$charges;
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        return $out;
    }

    private function _leadorder_totals($leadorder, $ordersession) {
        // Restore Order and parts
        if (isset($leadorder['shipping'])) {
            $shipping=$leadorder['shipping'];
        } else {
            $shipping=array();
        }
        $order=$leadorder['order'];

        // Shipping
        if ($leadorder['order_system']=='old') {
            $base_cost=intval($order['is_shipping'])*floatval($order['shipping']);
            // $base_cost+=floatval($order['item_cost'])+floatval($order['item_imprint']);
            $base_cost+=floatval($order['cc_fee']);
            if (isset($shipping['rush_price'])) {
                $base_cost+=floatval($shipping['rush_price']);
            }
            $base_cost+=floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
            // Revenue
            // $revenue=$base_cost+floatval($order['tax']);
        } else {
            $order_items=$leadorder['order_items'];
            $shipping_address=$leadorder['shipping_address'];
            $order['shipping']=$this->_leadorder_shipcost($shipping_address);
            // Rebuild Shipping Data
            $newshipping=$this->_leadorder_shipping($shipping_address, $shipping);
            $total_item=0;
            $total_qty=0;
            $total_imprint=0;
            foreach ($order_items as $row) {
                $total_item+=$row['item_subtotal'];
                $total_imprint+=$row['imprint_subtotal'];
                $total_qty+=$row['item_qty'];
            }
            $order['item_imprint']=$total_imprint;
            $order['item_cost']=$total_item;
            $order['order_qty']=$total_qty;
            $shpidx=0;
            $base_cost=floatval($order['item_cost'])+floatval($order['item_imprint'])+floatval($order['shipping']);
            $base_cost+=floatval($shipping['rush_price']);
            $base_cost+=floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);

            $tax=0;
            $shipadrcnt=count($shipping_address);

            foreach ($shipping_address as $row) {
                if ($row['taxcalc']==0) {
                    $shipping_address[$shpidx]['sales_tax']=0;
                } else {
                    $taxpercent=$this->config->item('salestax');
                    if ($order['order_date']>=$this->config->item('datenewtax')) {
                        $taxpercent=$this->config->item('salesnewtax');
                    }
                    if ($shipadrcnt==1) {
                        $adrtax=round($base_cost*$taxpercent/100,2); // $taxpercent
                        $shipping_address[$shpidx]['sales_tax']=$adrtax;
                        $tax+=$adrtax;
                    } else {
                        $adrtax=round($base_cost/$total_qty*$row['item_qty']*$taxpercent/100,2);
                        $shipping_address[$shpidx]['sales_tax']=$adrtax;
                        $tax+=$adrtax;
                    }
                }
                $shpidx++;
            }
            $order['tax']=$tax;
        }
        $revenue=$base_cost+floatval($order['tax']);
        $order['revenue']=$revenue;

        if ($order['order_cog']=='') {
            $profit=round($revenue*$this->config->item('default_profit')/100,2);
        } else {
            $profit=$this->_leadorder_profit($order);
            if ($revenue!=0) {
                $profit_perc=round($profit/$revenue*100,1);
                $order['profit_perc']=$profit_perc;
            } else {
                $order['profit_perc']=0;
            }
        }
        // Change shipping address
        $shipping['arriveclass']='';
        if (!empty($shipping['event_date'])) {
            if (!empty($shipping['arrive_date'])){
                $eventdate=$shipping['event_date']+$this->config->item('event_time');
                if ($shipping['arrive_date']>$eventdate) {
                    $shipping['arriveclass']='arrivelate';
                }
            }
        }
        $leadorder['shipping']=$shipping;
        $order['profit']=$profit;
        $leadorder['order']=$order;
        $leadorder['shipping']=$newshipping;
        usersession($ordersession, $leadorder);
    }

    public function oldorder_item_subtotal($order) {
        $base_cost=intval($order['is_shipping'])*floatval($order['shipping']);
        $base_cost+=floatval($order['cc_fee']);
        $base_cost+=floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
        $base_cost+=floatval($order['tax']);
        // Revenue
        $subtotal=floatval($order['revenue'])-$base_cost;
        return $subtotal;
    }

    // Check open ticket for order
    public function get_opentickets($leadorder, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        $this->load->model('tickets_model');
        $this->load->model('vendors_model');
        $custom_class=$vendor_class="";
        $custom_issues=$this->tickets_model->get_issues('C');
        $vendor_issues=$this->tickets_model->get_issues('V');
        $v_options=[
            'order_by' => 'v.vendor_name',
            'direct' => 'asc',
        ];
        $vendors=$this->vendors_model->get_vendors_list($v_options);
        $options=array(
            'order_num'=>$order['order_num'],
        );
        $this->db->select('count(ticket_id) as cnt');
        $this->db->from('ts_tickets');
        $this->db->where('order_num', $order['order_num']);
        $this->db->where('ticket_closed',0);
        $chkres=$this->db->get()->row_array();

        if ($chkres['cnt']==0) {
            $ticket=$this->tickets_model->add_newticket($options);
            $ticket['custom_class']=$custom_class;
            $ticket['vendor_class']=$vendor_class;
            $attachment_list=array();
        } else {
            // Get Last Opened ticket
            $this->db->select('ticket_id');
            $this->db->from('ts_tickets');
            $this->db->where('order_num', $order['order_num']);
            $this->db->where('ticket_closed',0);
            $res=$this->db->get()->row_array();
            if (!isset($res['ticket_id'])) {
                $ticket=$this->tickets_model->add_newticket($options);
                $ticket['custom_class']=$custom_class;
                $ticket['vendor_class']=$vendor_class;
                $attachment_list=array();
            } else {
                $ticket_id=$res['ticket_id'];
                $ticket=$this->tickets_model->get_ticket_data($ticket_id);
                /* Get attachment */
                // $sess_id='';
                /* Change is_delete */
                $this->tickets_model->attach_init($ticket_id);
                if ($ticket['other_vendor']) {
                    $ticket['vendor_id']='-1';
                }
                $ticket['custom_class']=$custom_class;
                $ticket['vendor_class']=$vendor_class;
                $attachment_list=$this->tickets_model->get_attachments($ticket_id, 'unkn');
                if (!empty($ticket['custom_issue_id'])) {
                    $ticket['custom_class']='colored';
                }
                if (!empty($ticket['vendor_issue_id'])) {
                    $ticket['vendor_class']='colored';
                }
                if ($ticket['ticket_date']=='') {
                    $ticket['ticket_date']=date('m/d/Y');
                } else {
                    $ticket['ticket_date']=date('m/d/Y',$ticket['ticket_date']);
                }
            }
        }
        $ticket['custom_issues']=$custom_issues;
        $ticket['vendor_issues']=$vendor_issues;
        $ticket['vendors']=$vendors;

        $leadorder['order']=$order;
        usersession($ordersession, $leadorder);
        $out['ticket']=$ticket;
        $out['ticket_attach']=$attachment_list;
        $out['result']=$this->success_result;
        return $out;
    }

    //Tickets, related with order
    public function get_opentickets_data($leadorder, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $order=$leadorder['order'];
        // Get Ticket
        $ticket=array(
            'class'=>'closed',
            'label'=>'No Ticket',
        );
        $this->db->select('count(ticket_id) as cnt');
        $this->db->from('ts_tickets');
        $this->db->where('order_num', $order['order_num']);
        $this->db->where('ticket_closed',0);
        $tickres=$this->db->get()->row_array();
        $out['numtickets']=$tickres['cnt'];
        if ($tickres['cnt']>0) {
            $this->db->select('TIMESTAMPDIFF(HOUR, created, now()) as hdiff');
            $this->db->select('ticket_closed');
            $this->db->from('ts_tickets');
            $this->db->where('order_num', $order['order_num']);
            $this->db->where('ticket_closed',0);
            $this->db->order_by('ticket_id', 'desc');
            $tickdet=$this->db->get()->row_array();
            $label='Open Ticket - ';
            $days=floor($tickdet['hdiff']/24);
            if ($days>0) {
                $hdiff=$tickdet['hdiff']-$days*24;
                $label.=$days.'d '.$hdiff.'h';
            } else {
                $label.=$tickdet['hdiff'].'h';
            }
            $ticket['class']='open';
            $ticket['label']=$label;
        }
        $out['ticket']=$ticket;
        $out['result']=$this->success_result;
        usersession($ordersession, $leadorder);
        return $out;
    }

    // Add Track Packages
    public function shiptrack_addpackage($shiptracks, $shipaddr, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipaddres=$shiptracks['shipping_address'];
        $found=0;
        $idx=0;
        foreach ($shipaddres as $row) {
            if ($row['order_shipaddr_id']==$shipaddr) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Address Not Found';
            return $out;
        }
        $packages=$shipaddres[$idx]['packages'];
        $newpackidx=count($packages)+1;
        $packages[]=array(
            'order_shippack_id'=>$newpackidx*(-1),
            'deliver_service'=>'UPS',
            'track_code'=>'',
            'track_date'=>0,
            'send_date'=>0,
            'senddata'=>0,
            'delivered'=>0,
            'delivery_address' => '',
            'delflag'=>0,
        );
        $shipaddres[$idx]['packages']=$packages;
        $shiptracks['shipping_address']=$shipaddres;
        usersession($shiptraccodes, $shiptracks);
        $out['result']=$this->success_result;
        $outpack=array();
        foreach ($packages as $row) {
            if ($row['delflag']==0) {
                $outpack[]=$row;
            }
        }
        $out['packages']=$outpack;
        return $out;
    }

    // Remove package
    public function shiptrack_package_remove($shiptracks, $data, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipaddr=$data['shipaddres'];
        $package_id=$data['package_id'];

        $shipaddres=$shiptracks['shipping_address'];
        $found=0;
        $idx=0;
        foreach ($shipaddres as $row) {
            if ($row['order_shipaddr_id']==$shipaddr) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Address Not Found';
            return $out;
        }

        $packages=$shipaddres[$idx]['packages'];
        $pidx=0;
        $pfound=0;
        foreach ($packages as $prow) {
            if ($prow['order_shippack_id']==$package_id) {
                $pfound=1;
                break;
            } else {
                $pidx++;
            }
        }
        if ($pfound==0) {
            $out['msg']='Package Not Found';
            return $out;
        }
        // Mark as deleted
        $packages[$pidx]['delflag']=1;
        $shipaddres[$idx]['packages']=$packages;
        $shiptracks['shipping_address']=$shipaddres;
        usersession($shiptraccodes, $shiptracks);
        $out['result']=$this->success_result;
        $out['shipping_address']=$shipaddres;
        $outpack=array();
        foreach ($packages as $row) {
            if ($row['delflag']==0) {
                $outpack[]=$row;
            }
        }
        $out['packages']=$outpack;
        return $out;
    }

    // Track code
    public function shiptrack_trackcode($shiptracks, $data, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shownewrow=0;
        $shipaddres=$shiptracks['shipping_address'];
        $shipadr_id=$data['shipaddres'];
        $package_id=$data['package_id'];
        $idx=0;
        $found=0;
        foreach ($shipaddres as $srow) {
            if ($srow['order_shipaddr_id']==$shipadr_id) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Addres Not Found';
            return $out;
        }
        $packages=$shipaddres[$idx]['packages'];
        $pidx=0;
        $pfound=0;
        foreach ($packages as $prow) {
            if ($prow['order_shippack_id']==$package_id) {
                $pfound=1;
                break;
            } else {
                $pidx++;
            }
        }
        if ($pfound==0) {
            $out['msg']='Shipping Package Not Found';
            return $out;
        }
        $package=$packages[$pidx];
        if ($package['delivered']==0) {
            $trackcode=$package['track_code'];
            $check_system=$package['deliver_service'];
            if ($check_system=='UPS') {
                $this->load->library('United_parcel_service');
                $upsserv=new United_parcel_service();
                $tracking=$upsserv->trackpackage($trackcode);
                if ($tracking['result']==FALSE) {
                    $out['msg']=$tracking['msg'];
                    return $out;
                } else {
                    // Check that packages delivered
                    $tracklog=$tracking['tracklog'];
                    $deliver=1;
                    $delivtime=0;
                    foreach ($tracklog as $trrow) {
                        if ($trrow['status']!='DELIVERED') {
                            $deliver=0;
                            break;
                        } else {
                            if ($trrow['date']>$delivtime) {
                                $delivtime=$trrow['date'];
                            }
                        }
                    }
                    if ($deliver==1) {
                        // All packages delivered
                        $package['delivered']=$delivtime;
                        $package['delivery_address']=$tracklog[0]['address'];
                        // Create logs
                        $package['logs']=$tracklog;
                    }
                    // Package
                    $packages[$pidx]=$package;
                    $shipaddres[$idx]['packages']=$packages;
                    $shiptracks['shipping_address']=$shipaddres;
                }
            }
        } else {
            $tracking=array(
                'result' =>  $this->success_result,
                'tracklog' =>$package['logs'],
                'trackcode' =>$package['track_code'],
                'system' =>$package['deliver_service'],
            );
        }
        // Save changed parameter to Session
        usersession($shiptraccodes, $shiptracks);
        $out['result']=$this->success_result;
        $out['tracking']=$tracking;
        $out['package']=$package;
        return $out;
    }


    // Change package parameters
    public function shiptrack_changepackage($shiptracks, $data, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shownewrow=0;
        $shipaddres=$shiptracks['shipping_address'];
        $shipadr_id=$data['shipaddres'];
        $package_id=$data['package_id'];
        $fldname=$data['field'];
        $newval=$data['newval'];
        $idx=0;
        $found=0;
        foreach ($shipaddres as $srow) {
            if ($srow['order_shipaddr_id']==$shipadr_id) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Addres Not Found';
            return $out;
        }
        $packages=$shipaddres[$idx]['packages'];
        $pidx=0;
        $pfound=0;
        foreach ($packages as $prow) {
            if ($prow['order_shippack_id']==$package_id) {
                $pfound=1;
                break;
            } else {
                $pidx++;
            }
        }
        if ($pfound==0) {
            $out['msg']='Shipping Package Not Found';
            return $out;
        }
        $package=$packages[$pidx];
        if (!array_key_exists($fldname, $package)) {
            $out['msg']='Unknown Parameter '.$fldname;
            return $out;
        }
        // Change Value
        if ($fldname=='track_code') {
            $oldval=$package['track_code'];
            if (empty($oldval) && !empty($newval)) {
                $shownewrow=1;
            } elseif (!empty($oldval) && empty($newval)) {
                $shownewrow=1;
            }
        }
        $package[$fldname]=$newval;
        $viewtrack=0;
        if (in_array($package['deliver_service'], $this->config->item('tracking_service'))) {
            $viewtrack=1;
        }
        if ($shownewrow==1) {
            $out['package']=$package;
        }
        // Save changed parameter to Session
        $packages[$pidx]=$package;
        $shipaddres[$idx]['packages']=$packages;
        $shiptracks['shipping_address']=$shipaddres;
        usersession($shiptraccodes, $shiptracks);
        $out['result']=$this->success_result;
        $out['shipping_address']=$shipaddres;
        $out['viewtrack']=$viewtrack;
        $out['shownewrow']=$shownewrow;
        return $out;
    }

    // Change Track Message Parameters
    public function shiptrack_changemessage($shiptracks, $data, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $email=$shiptracks['email'];
        $fldname=$data['field'];
        $newval=$data['newval'];
        if (!array_key_exists($fldname, $email)) {
            $out['msg']='Unknown Parameter '.$fldname;
            return $out;
        }
        if (!empty($newval)) {
            if ($fldname=='sender' || $fldname=='customer' || $fldname=='bcc') {
                // Devide value by coma
                $valdata=  explode(',', $newval);
                foreach ($newval as $row) {
                    if (!empty($row) && !valid_email_address($row)) {
                        $out['msg']='Email Address '.$row.' is not Valid';
                        return $out;
                    }
                }
            }
        }
        $email[$fldname]=$newval;
        $shiptracks['email']=$email;
        usersession($shiptraccodes, $shiptracks);
        $out['result']=$this->success_result;
        return $out;
    }

    public function shiptrack_sendcodes($shiptracks, $leadorder, $edit_mode, $ordersession, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipaddres=$shiptracks['shipping_address'];
        $codes=array();
        $shipidx=0;
        foreach ($shipaddres as $srow) {
            $packages=$srow['packages'];
            $pidx=0;
            foreach ($packages as $prow) {
                if ($prow['delflag']==0 && !empty($prow['track_code']) && $prow['senddata']==1) {
                    array_push($codes, $prow['track_code']);
                    $shipaddres[$shipidx]['packages'][$pidx]['send_date']=time();
                }
                $pidx++;
            }
            $shipidx++;
        }
        if (!empty($codes)) {
            $codesrow='';
            foreach ($codes as $row) {
                $codesrow.=$row.' ';
            }

            $emailconf=$shiptracks['email'];

            $email_body=  str_replace('<<codes>>', $codesrow, $emailconf['message']);
            $this->load->library('email');
            $config = $this->config->item('email_setup');
            $config['mailtype'] = 'text';
            $this->email->initialize($config);
            $this->email->set_newline("\r\n");
            $this->email->to($emailconf['customer']);
            if (!empty($emailconf['bcc'])) {
                $this->email->bcc($emailconf['bcc']);
            }
            if ($config['protocol']=='smtp') {
                $this->email->from($config['smtp_user']);
            } else {
                $this->email->from($emailconf['sender']);
            }
            $this->email->subject($emailconf['subject']);
            $this->email->message($email_body);
            $this->email->send();
            $this->email->clear(TRUE);
        } else {
            $out['msg']='No Codes was checked';
            return $out;
        }
        $shiptracks['shipping_address']=$shipaddres;
        $saveres=$this->shiptrack_savetrackcodes($shiptracks, $leadorder, $edit_mode, $ordersession, $shiptraccodes);
        if ($saveres['result']==$this->error_result) {
            $out['msg']=$saveres['msg'];
        } else {
            $out['result']=$this->success_result;
        }
        return $out;
    }

    // Save Shipping Tracks
    public function shiptrack_savetrackcodes($shiptracks, $leadorder, $edit_mode, $ordersession, $shiptraccodes) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipaddres=$shiptracks['shipping_address'];
        $numaddr=1;
        foreach ($shipaddres as $srow) {
            $packages=$srow['packages'];
            foreach ($packages as $row) {
                if ($row['delflag']==0 && empty($row['track_code'])) {
                    $out['msg']='Address '.$numaddr.' content empty Track Code';
                    return $out;
                }
                $numaddr++;
            }
        }
        $order_id=$leadorder['order']['order_id'];
        $order=$leadorder['order'];
        $shipping=$leadorder['shipping'];
        // Save address
        if ($edit_mode==0) {
            if (empty($shipping)) {
                $this->db->set('order_id', $order_id);
                $this->db->set('rush_price',0);
                $this->db->set('shipdate', $order['shipdate']);
                $this->db->insert('ts_order_shippings');
                $leadorder['shipping']=array(
                    'order_shipping_id'=>$this->db->insert_id(),
                    'order_id'=>$order_id,
                    'shipdate'=>$order['shipdate'],
                );
            }
            foreach ($shipaddres as $srow) {
                $this->db->set('country_id',(empty($srow['country_id']) ? NULL : $srow['country_id']));
                $this->db->set('address', $srow['address']);
                $this->db->set('city', $srow['city']);
                $this->db->set('state_id', (empty($srow['state_id']) ? NULL : $srow['state_id']));
                $this->db->set('zip', $srow['zip']);
                $this->db->set('item_qty', $srow['item_qty']);
                $this->db->set('ship_date', $srow['ship_date']);
                $this->db->set('arrive_date', $srow['arrive_date']);
                $this->db->set('shipping', $srow['shipping']);
                $this->db->set('sales_tax', $srow['sales_tax']);
                $this->db->set('resident', $srow['resident']);
                $this->db->set('ship_blind', $srow['ship_blind']);
                $this->db->set('tax', $srow['tax']);
                $this->db->set('tax_exempt', $srow['tax_exempt']);
                $this->db->set('tax_reason', $srow['tax_reason']);
                $this->db->set('tax_exemptdoc', $srow['tax_exemptdoc']);
                $this->db->set('tax_exemptdocsrc', $srow['tax_exemptdocsrc']);
                if ($srow['order_shipaddr_id']<0) {
                    $this->db->set('order_id', $order_id);
                    $this->db->insert('ts_order_shipaddres');
                    $shipaddr_id=$this->db->insert_id();
                } else {
                    $this->db->where('order_shipaddr_id', $srow['order_shipaddr_id']);
                    $this->db->update('ts_order_shipaddres');
                    $shipaddr_id=$srow['order_shipaddr_id'];
                }
                // Save packages
                $packages=$srow['packages'];
                foreach ($packages as $prow) {
                    if ($prow['delflag']==1) {
                        if ($prow['order_shippack_id']>0) {
                            $this->db->where('order_shippack_id', $prow['order_shippack_id']);
                            $this->db->delete('ts_order_shippacks');
                        }
                    } else {
                        $this->db->set('order_shipaddr_id', $shipaddr_id);
                        $this->db->set('deliver_service', $prow['deliver_service']);
                        $this->db->set('track_code', $prow['track_code']);
                        $this->db->set('track_date', $prow['track_date']);
                        $this->db->set('send_date', $prow['send_date']);
                        $this->db->set('delivered', $prow['delivered']);
                        $this->db->set('delivery_address', $prow['delivery_address']);
                        if ($prow['order_shippack_id']<0) {
                            $this->db->insert('ts_order_shippacks');
                            $packid_id=$this->db->insert_id();
                        } else {
                            $this->db->where('order_shippack_id',$prow['order_shippack_id']);
                            $this->db->update('ts_order_shippacks');
                            $packid_id=$prow['order_shippack_id'];
                        }
                        if (isset($prow['logs'])) {
                            // Insert data into log
                            foreach ($prow['logs'] as $lrow) {
                                $this->db->set('package_num', $lrow['package_num']);
                                $this->db->set('status', $lrow['status']);
                                $this->db->set('date', $lrow['date']);
                                $this->db->set('address', $lrow['address']);
                                if ($lrow['log_id']<0) {
                                    $this->db->set('order_shippack_id', $packid_id);
                                    $this->db->insert('ts_shippack_tracklogs');
                                } else {
                                    $this->db->where('shippack_tracklog_id', $lrow['log_id']);
                                    $this->db->update('ts_shippack_tracklogs');
                                }
                            }
                        }
                    }
                }
            }
            $leadorder['shipping_address']=$shipaddres;
            usersession($ordersession, $leadorder);
        } else {
            $leadorder['shipping_address']=$shipaddres;
            usersession($ordersession, $leadorder);
        }
        $out['result']=$this->success_result;
        // Remove track codes
        usersession($shiptraccodes, NULL);
        return $out;
    }

    // Change Order or shipping value from Multi Ship
    public function change_multishiporder_input($shipdata, $entity, $fldname, $newval, $shipsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $data=$shipdata[$entity];
        if (array_key_exists($fldname,$data)) {
            $data[$fldname]=$newval;
            $shipdata[$entity]=$data;
            if ($fldname=='rush_idx') {
                $params=explode("-", $newval);
                $shipping=$shipdata['shipping'];
                if (isset($params[0])) {
                    $shipping['shipdate']=$params[0];
                }
                if (isset($params[1])) {
                    $shipping['rush_price']=$params[1];
                }
                $shipdata['shipping']=$shipping;
                $order=$shipdata['order'];
                $order['shipdate']=$shipping['shipdate'];
                $out['shipdate']=$shipping['shipdate'];

                // Calculate shipping
                $this->load->model('shipping_model');
                $shiprate=0;
                $items=$shipdata['order_items'];
                $shipaddr=$shipdata['shipping_address'];
                $shipidx=0;
                $cnt=0;
                foreach ($shipaddr as $shprow) {
                    // Get Old Shipping Method
                    $default_ship_method='';
                    if (isset($shprow['shipping_cost'])) {
                        $oldcosts=$shprow['shipping_costs'];
                        foreach ($oldcosts as $costrow) {
                            if ($costrow['delflag']==0 && $costrow['current']==1) {
                                $default_ship_method=$costrow['shipping_method'];
                            }
                        }
                    }
                    $cntres=$this->shipping_model->count_shiprates($items, $shipaddr[$shipidx], $shipping['shipdate'], $order['brand'], $default_ship_method);
                    if ($cntres['result']==$this->error_result) {
                        $out['msg']=$cntres['msg'];
                        return $out;
                    } else {
                        $rates=$cntres['ships'];
                        $shipcost=$shipaddr[$shipidx]['shipping_costs'];
                        $cidx=0;
                        foreach ($shipcost as $row) {
                            $shipcost[$cidx]['delflag']=1;
                            $cidx++;
                        }
                        $newidx=count($shipcost)+1;
                        foreach ($rates as $row) {
                            $shipcost[]=array(
                                'order_shipcost_id'=>$newidx*(-1),
                                'shipping_method'=>$row['ServiceName'],
                                'shipping_cost'=>$row['Rate'],
                                'arrive_date'=>$row['DeliveryDate'],
                                'current'=>$row['current'],
                                'delflag'=>0,
                            );
                            if ($row['current']==1) {
                                $shipaddr[$shipidx]['shipping']=$row['Rate'];
                                $shipaddr[$shipidx]['arrive_date']=$row['DeliveryDate'];
                                $shiprate+=$row['Rate'];
                            }
                            $newidx++;
                        }
                        $shipaddr[$shipidx]['shipping_costs']=$shipcost;
                    }
                    $shipidx++;
                    $cnt++;
                }
                $order['shipping']=$shiprate;
                // Save data into Session
                $shipdata['order']=$order;
            }
            $out['result']=$this->success_result;
            usersession($shipsession, $shipdata);
            // Rebuild Totals of shipping
            $this->_recalc_shippingaddress($shipdata, $shipsession);
        } else {
            $out['msg']='Field not found';
        }
        return $out;
    }

    public function multiship_addadres($shipping, $shipsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipping_address=$shipping['shipping_address'];
        // Get Last element
        $newidx=(count($shipping_address)+1)*(-1);

        $newshadr=$this->_create_empty_shipaddress();
        $newshadr['order_shipaddr_id']=($newidx);
        $shipping_address[]=$newshadr;
        // Add empty shipping Address
        $shipping['shipping_address']=$shipping_address;
        $out['result']=$this->success_result;
        usersession($shipsession, $shipping);
        $out['shipping_address']=$shipping_address;
        return $out;
    }

    public function multiship_removeadres($multishipping, $shipadr_id, $shipsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipping_address=$multishipping['shipping_address'];
        $delrecords=$multishipping['delrecords'];

        $found=0;
        $newaddr=array();
        foreach ($shipping_address as $srow) {
            if ($srow['order_shipaddr_id']==$shipadr_id) {
                $found=1;
                if ($shipadr_id>0) {
                    $delrecords[]=array(
                        'entity'=>'shipping_address',
                        'id'=>$shipadr_id,
                    );
                }
            } else {
                $newaddr[]=$srow;
            }
        }
        if ($found==0) {
            $out['msg']='Record Not Found';
            return $out;
        }
        if (count($newaddr)==0) {
            $newshadr=$this->_create_empty_shipaddress();
            $newshadr['order_shipaddr_id']=-1;
            $newaddr[]=$newshadr;
        } else {
            // Rebuild index
            $shidx=0;
            $idx=1;
            foreach ($newaddr as $nrow) {
                if ($nrow['order_shipaddr_id']<0) {
                    $newaddr[$shidx]['order_shipaddr_id']=(-1)*$idx;
                }
                $shidx++;
                $idx++;
            }
        }
        $multishipping['shipping_address']=$newaddr;
        $multishipping['delrecords']=$delrecords;
        $out['result']=$this->success_result;
        usersession($shipsession, $multishipping);
        $out['shipping_address']=$newaddr;
        return $out;
    }

    // Change shipping Address
    public function change_multishiporder_address($shipdata, $shipadr, $fldname, $newval, $brand, $shipsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipping_address=$shipdata['shipping_address'];

        $found=0;
        $idx=0;
        foreach ($shipping_address as $srow) {
            if ($srow['order_shipaddr_id']==$shipadr) {
                $found=1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==0) {
            $out['msg']='Shipping Address Not Found';
            return $out;
        }
        $this->load->model('shipping_model');
        //
        if ($fldname=='shipping_method') {
            $shipcost=$shipping_address[$idx]['shipping_costs'];
            $costfound=0;
            $costidx=0;
            foreach ($shipcost as $scrow) {
                if ($scrow['shipping_method']==$newval && $scrow['delflag']==0) {
                    $costfound=1;
                    break;
                } else {
                    $costidx++;
                }
            }
            if ($costfound==0) {
                $out['msg']='Shipping Method Not Found';
                return $out;
            }
            // Update prev costs with 0 in Current
            $tidx=0;
            foreach ($shipcost as $scrow) {
                $shipcost[$tidx]['current']=0;
                $tidx++;
            }
            $shipcost[$costidx]['current']=1;
            $shipping_address[$idx]['arrive_date']=$shipcost[$costidx]['arrive_date'];
            $shipping_address[$idx]['shipping']=$shipcost[$costidx]['shipping_cost'];
            // $shipping_address[$idx][''] - Tax
            $out['shipcost_id']=$shipcost[$costidx]['order_shipcost_id'];
            $out['shiprate']=$shipcost[$costidx]['shipping_cost'];
            $shipping_address[$idx]['out_shipping_method']=$newval;
            $shipping_address[$idx]['shipping_costs']=$shipcost;
        }else {
            $shipping_address[$idx][$fldname]=$newval;
            switch ($fldname) {
                case 'zip':
                case 'state_id':
                    // Get new value of state code
                    if (!empty($shipping_address[$idx]['state_id'])) {
                        $statedat=$this->shipping_model->get_state($shipping_address[$idx]['state_id']);
                        if (isset($statedat['state_id'])) {
                            $shipping_address[$idx]['out_zip']=$shipping_address[$idx]['zip'].' '.$statedat['state_code'];
                        } else {
                            $shipping_address[$idx]['out_zip']=$shipping_address[$idx]['zip'];
                        }
                    } else {
                        $shipping_address[$idx]['out_zip']=$shipping_address[$idx]['zip'];
                    }
                    break;
                case 'country_id':
                    $cntdate=$this->shipping_model->get_country($newval);
                    $shipping_address[$idx]['out_country']=$cntdate['country_iso_code_2'];
                    // Get list of states
                    $states_list=$this->shipping_model->get_country_states($newval);
                    $shipping_address[$idx]['state_id']=NULL;
                    $shipping_address[$idx]['out_zip']=NULL;

                    if (count($states_list)==0) {
                        // $shipping_address[$idx]['state_id']=NULL;
                        // $shipping_address[$idx]['out_zip']=$shipping_address[$idx]['zip'];
                        $out['state_list']=array();
                    } else {
                        // $shipping_address[$idx]['state_id']=$states_list[0]['state_id'];
                        // $shipping_address[$idx]['out_zip']=$shipping_address[$idx]['zip'].' '.$states_list[0]['state_code'];
                        $out['state_list']=$states_list;
                    }
                    break;
            }
        }

        if ($fldname=='state_id') {
            if ($newval==$this->tax_state) {
                $shipping_address[$idx]['taxcalc']=0;
                $shipping_address[$idx]['taxview']=1;
                if ($shipping_address[$idx]['tax_exempt']==0) {
                    $shipping_address[$idx]['taxcalc']=1;
                }
            } else {
                $shipping_address[$idx]['taxcalc']=0;
                $shipping_address[$idx]['taxview']=0;
            }
        }

        if ($fldname=='tax_exempt') {
            if ($newval==0) {
                $shipping_address[$idx]['taxcalc']=1;
            } else {
                $shipping_address[$idx]['taxcalc']=0;
            }
        }

        if ($fldname=='item_qty' || $fldname=='zip') {
            if ($fldname=='zip') {
                // Validate ZIP
//                $this->load->library('United_parcel_service');
//                $upsserv=new United_parcel_service();
//                $tracking=$upsserv->validaddress($newval, $shipping_address[$idx]['out_country']);
                if ($shipping_address[$idx]['out_country']=='CA') {
                    $seachzip = substr($newval,0, 3);
                } else {
                    $seachzip = $newval;
                }
                $this->db->select('c.geoip_city_id, c.city_name, c.subdivision_1_iso_code as state, t.state_id');
                $this->db->from('ts_geoipdata gdata');
                $this->db->join('ts_geoip_city c','c.geoname_id=gdata.geoname_id');
                $this->db->join('ts_countries cntr','cntr.country_iso_code_2=c.country_iso_code');
                $this->db->join('ts_states t','t.state_code=c.subdivision_1_iso_code','left');
                $this->db->where('gdata.postal_code',$seachzip);
                $this->db->where('cntr.country_id',$shipping_address[$idx]['country_id']);
                $validres = $this->db->get()->row_array();

                if (ifset($validres,'geoip_city_id',0)>0) {
                    if (!empty($validres['city_name'])) {
                        $shipping_address[$idx]['city']=$validres['city_name'];
                    }
                    if ($shipping_address[$idx]['out_country']=='US' || $shipping_address[$idx]['out_country']=='CA') {
                        $shipping_address[$idx]['state_id']=$validres['state_id'];
                        $shipping_address[$idx]['out_zip']=$validres['state'].' '.$newval;
                    } else {
                        $shipping_address[$idx]['state_id']='';
                        $shipping_address[$idx]['out_zip']=$newval;
                    }
                }
            }
            $shiprate=0;
            // Calc calculation
            $shipping=$shipdata['shipping'];
            $items=$shipdata['order_items'];
            if (!empty($shipping_address[$idx]['zip']) && !empty($shipping_address[$idx]['item_qty'])) {
                // Get Old Shipping Method
                $default_ship_method='';
                if (isset($shipping_address[$idx]['shipping_cost'])) {
                    $oldcosts=$shipping_address[$idx]['shipping_costs'];
                    foreach ($oldcosts as $costrow) {
                        if ($costrow['delflag']==0 && $costrow['current']==1) {
                            $default_ship_method=$costrow['shipping_method'];
                        }
                    }
                }
                $cntres=$this->shipping_model->count_shiprates($items, $shipping_address[$idx], $shipping['shipdate'], $brand, $default_ship_method);
                if ($cntres['result']==$this->error_result) {
                    $out['msg']=$cntres['msg'];
                    return $out;
                }
                $rates=$cntres['ships'];
                $shipcost=$shipping_address[$idx]['shipping_costs'];
                $cidx=0;
                foreach ($shipcost as $row) {
                    $shipcost[$cidx]['delflag']=1;
                    $cidx++;
                }
                $outshipcost=array();
                $newidx=count($shipcost)+1;
                foreach ($rates as $row) {
                    $shipcost[]=array(
                        'order_shipcost_id'=>$newidx*(-1),
                        'shipping_method'=>$row['ServiceName'],
                        'shipping_cost'=>$row['Rate'],
                        'arrive_date'=>$row['DeliveryDate'],
                        'current'=>$row['current'],
                        'delflag'=>0,
                    );
                    if ($row['current']==1) {
                        $shipping_address[$idx]['shipping']=$row['Rate'];
                        $shiprate+=$row['Rate'];
                    }
                    $outshipcost[]=array(
                        'order_shipcost_id'=>$newidx*(-1),
                        'shipping_method'=>$row['ServiceName'],
                        'shipping_cost'=>$row['Rate'],
                        'arrive_date'=>$row['DeliveryDate'],
                        'current'=>$row['current'],
                        'delflag'=>0,
                    );
                    $newidx++;
                }
                $out['shipcost']=$outshipcost;
                $out['shiprate']=$shiprate;
                $out['shipcalc']=1;
                $shipping_address[$idx]['shipping_costs']=$shipcost;
            } else {
                $out['shipcalc']=0;
            }
        }
        if ($shipping_address[$idx]['state_id']==$this->tax_state) {
            $shipping_address[$idx]['taxcalc']=0;
            $shipping_address[$idx]['taxview']=1;
            if ($shipping_address[$idx]['tax_exempt']==0) {
                $shipping_address[$idx]['taxcalc']=1;
            }
        } else {
            $shipping_address[$idx]['taxcalc']=0;
            $shipping_address[$idx]['taxview']=0;
        }


        $out['result']=$this->success_result;
        $shipdata['shipping_address']=$shipping_address;

        $out['shipadr']=$shipping_address[$idx];
        usersession($shipsession, $shipdata);
        $this->_recalc_shippingaddress($shipdata, $shipsession);
        return $out;
    }

    // Save Multi Shipping
    public function multiship_save($shipdata, $leadorder, $ordersession, $shipsession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $shipping_address=$shipdata['shipping_address'];
        $delrecords=$shipdata['delrecords'];
        $this->load->model('shipping_model');
        // Check that all qty equal order qty
        $order=$leadorder['order'];
        $order_qty=$order['order_qty'];
        $shipcost=$tax=$shipqty=0;
        foreach ($shipping_address as $row) {
            $shipqty+=$row['item_qty'];
            $shipcost+=$row['shipping'];
            $tax+=$row['tax'];
            if ($row['item_qty']>0 && count($row['shipping_costs'])==0) {
                $out['msg']='Shipping Address was not calculated';
                return $out;
            }
        }
        if ($shipqty!=$order_qty) {
            $out['msg']='Shipping QTY not equal to Order QTY';
            return $out;
        }
        // Lets go
        // Update Shipping Address
        $sidx=0;
        foreach ($shipping_address as $srow) {
            $costs=$srow['shipping_costs'];
            $method_name='';
            foreach ($costs as $crow) {
                if ($crow['current']==1 && $crow['delflag']==0) {
                    $method_name=$crow['shipping_method'];
                    $shipping_address[$sidx]['arrive_date']=$crow['arrive_date'];
                }
            }
            $shipping_address[$sidx]['out_shipping_method']=$method_name;
            $cntdat=$this->shipping_model->get_country($srow['country_id']);
            $shipping_address[$sidx]['out_country']=$cntdat['country_iso_code_2'];
            // Ship && Zip
            $outzip=$srow['zip'];
            if (!empty($srow['state_id'])) {
                $statdat=$this->shipping_model->get_state($srow['state_id']);
                if (isset($statdat['state_code'])) {
                    $outzip.=' '.$statdat['state_code'];
                }
            }
            $shipping_address[$sidx]['out_zip']=$outzip;
            $sidx++;
        }
        $order['shipping']=$shipcost;
        $order['tax']=$tax;
        $leadorder['order']=$order;
        $leadorder['shipping']=$shipdata['shipping'];
        $leadorder['shipping_address']=$shipping_address;
        $leadorder['delrecords']=$delrecords;
        usersession($ordersession, $leadorder);
        usersession($shipsession, NULL);
        $out['result']=$this->success_result;
        $this->_leadorder_totals($leadorder, $ordersession);
        return $out;
    }

    // Save Order
    public function save_order($leadorder, $user_id, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->load->model('artlead_model');
        $this->load->model('artwork_model');
        $order=$leadorder['order'];
        $savedorder_id=$order['order_id'];
        $artwork=$leadorder['artwork'];
        $order['order_blank']=$artwork['artwork_blank'];
        $order['order_rush']=$artwork['artwork_rush'];
        // if ($order['order_id']==0 || $order['order_id']>=$this->old_orders) {
        $artsync=array(
            'user_id'=>$user_id,
            'blank'=>$order['order_blank'],
            'rush'=>$order['order_rush'],
            'customer'=>$order['customer_name'],
            'item_descript'=>$order['order_items'],
            'art_stage'=>0,
            'redraw_stage'=>0,
            'vector_stage'=>0,
            'proof_stage'=>0,
            'approv_stage'=>0,
        );
        if ($order['order_system']=='old') {
            $order_type='old';
            $chkres=$this->_check_old_order($order);
        } else {
            $order_type='new';
            $chkres=$this->_check_new_order($leadorder);
        }
        if ($chkres['result']==$this->error_result) {
            $out['msg']=$chkres['msg'];
            return $out;
        }
        $this->load->model('orders_model');
        $this->load->model('artlead_model');
        if ($order['order_id']==0) {
            $oldorderddata=array(
                'profit'=>0,
                'revenue'=>0,
                'shipdate'=>0,
            );
        } else {
            $oldorderddata=$this->orders_model->get_order_detail($order['order_id']);
            $compare_array=$this->get_leadorder($order['order_id'], $user_id);
        }
        $brand = $order['brand'];
        $netdata=$this->_get_netpofitweek($order['order_date'], $brand);
        $savepayment=0;
        $newplaceorder = 0;
        $finerror = 0;
        if ($order_type=='new') {
            // Get Item ID, Order QTY
            $order_items=$leadorder['order_items'];
            $artwork['item_qty']=$order['order_qty'];
            $artwork['item_id']=$order['item_id'];
            $res=$this->_save_neworder($order, $user_id);

            if ($res['result']==$this->error_result) {
                $out['msg']=$res['msg'];
                return $out;
            }
            if ($order['order_id']==0) {
                $newplaceorder = 1;
                $savepayment=1;
                $order['order_num']=$res['neworder'];
                $leadorder['order']=$order;
                // Confirmation # WY-3028 Placed - Internal Order # 39457
                $order['order_id']=$res['result'];
                $artwork['order_id']=$order['order_id'];
                $artwork['mail_id']=NULL;
            }
            $order_id=$order['order_id'];
            $artsync['order_id']=$order_id;
            // Notify changes in Shipping date
            $this->_save_shipdtelog($order_id, $user_id, $order['order_num'], $oldorderddata['shipdate'], $order['shipdate']);
            // Save Order Contacts
            $contacts=$leadorder['contacts'];
            $cntcres=$this->_save_order_contacts($contacts, $order_id, $user_id);
            if ($cntcres['result']==$this->error_result) {
                $out['msg']=$cntcres['msg'];
                return $out;
            }
            // Save Order Items, Imprint, Imprint Details, Item color
            $itemres=$this->_save_order_items($order_items, $order_id, $user_id);
            if ($itemres['result']==$this->error_result) {
                $out['msg']=$itemres['msg'];
                return $out;
            }

            $order_items=$itemres['order_items'];

            $shipping=$leadorder['shipping'];
            $shipres=$this->_save_order_shipping($shipping, $order_id, $user_id);
            if ($shipres['result']==$this->error_result) {
                $out['msg']=$shipres['msg'];
                return $out;
            }
            $shipping_address=$leadorder['shipping_address'];
            $adrres=$this->_save_order_shipaddress($shipping_address, $order_id, $user_id);
            if ($adrres['result']==$this->error_result) {
                $out['msg']=$adrres['msg'];
                return $out;
            }

            if ($order['showbilladdress']==1) {
                $biladr=$leadorder['billing'];
                $billing=$this->_billingaddres_copy($shipping_address, $biladr);
            } else {
                $billing=$leadorder['billing'];
            }
            $billres=$this->_save_order_billings($billing, $order_id, $user_id);
            if ($billres['result']==$this->error_result) {
                $out['msg']=$billres['msg'];
                return $out;
            }
            $charges=$leadorder['charges'];
            $chrgres=$this->_save_order_chargedata($charges, $order_id, $user_id);
            if ($chrgres['result']==$this->error_result) {
                $out['msg']=$chrgres['msg'];
                return $out;
            }
            // Payments
            $payments=$leadorder['payments'];
            $paymres=$this->_save_order_payments($payments, $order_id, $user_id);
            if ($paymres['result']==$this->error_result) {
                $out['msg']=$paymres['msg'];
                return $out;
            }
            $delrecords=$leadorder['delrecords'];
            $this->_delete_leadorder_components($delrecords);
            $out['result']=$this->success_result;
            // Tty to Pay
            if ($newplaceorder==1) {
                $popopt=array(
                    'order'=>$res['neworder'],
                    'confirm'=>$order['order_confirmation'],
                );
            }
            if ($savepayment==1) {
                $finres=$this->_prepare_order_payment($order_id, $user_id);
                if ($finres['result']==$this->error_result) {
                    $finerror = 1;
                    $out['finres']=$this->error_result;
                    if ($newplaceorder==1) {
                        $popopt['finres'] = 'The order has not been paid. Cause - '.$finres['msg'];
                    }
                } else {
                    $out['finres']=$this->success_result;
                }
            }
            if ($newplaceorder==1) {
                $out['finerror'] = $finerror;
                $out['popupmsg']=$this->load->view('leadorderdetails/order_placed_view',$popopt, TRUE);
            }
            $out['newplaceorder'] = $newplaceorder;
            $out['order_id']=$order_id;
            $newdata=$this->orders_model->get_order_detail($order_id);
            $newprofit=$newdata['profit'];
        } else {
            $res=$this->_save_oldorder($order, $user_id);
            if ($res['result']==$this->error_result) {
                $out['msg']=$res['msg'];
                return $out;
            }
            if ($order['order_id']==0) {
                $order['order_id']=$res['result'];
            }
            $newprofit=$res['profit'];
            $order_id=$order['order_id'];
            $out['order_id']=$order_id;
            $out['result']=$this->success_result;
            $out['finres']=$this->success_result;
            $artwork['order_id']=$order_id;
            $artwork['mail_id']=NULL;
        }
        // Note about Net Profit Changes
        if ($netdata['flag_note']==1 && round(floatval($oldorderddata['profit']),2)!=round(floatval($newprofit),2)) {
            // Send notification
            $this->_changeprofit_notification($netdata, $order, $oldorderddata, $user_id);
        }
        // Save Artwork
        $artres=$this->artlead_model->save_artwork($artwork, $user_id);
        $artwork_id=$artres;
        // Save order Locations
        $locations=$leadorder['artlocations'];
        if ($order_type=='new') {
            $this->artlead_model->save_artlocations($locations, $artwork_id, $order_items);
        } else {
            $this->artlead_model->save_artlocations($locations, $artwork_id, array());
        }

        $proofs=$leadorder['artproofs'];
        $res=$this->artlead_model->save_artproof($proofs, $artwork_id, $user_id);
        $artsyncdoc=$res['artsyncdoc'];
        // Save history and message
        $history=$leadorder['message']['history'];

        foreach ($history as $row) {
            if ($row['artwork_history_id']<=0) {
                // New Record
                $artw=array(
                    'artwork_id'=>$artwork_id,
                    'user_id'=>$user_id,
                    'created_time'=>$row['created_time'],
                    'update_msg'=>$row['message'],
                );
                $this->artlead_model->artwork_history_update($artw);
            }
        }
        if (!empty($leadorder['message']['update'])) {
            $artw=array(
                'artwork_id'=>$artwork_id,
                'user_id'=>$user_id,
                'created_time'=>time(),
                'update_msg'=>$leadorder['message']['update'],
            );
            $this->artlead_model->artwork_history_update($artw);
        }
        if (!empty($leadorder['message']['general_notes'])) {
            $this->db->set('general_notes', $leadorder['message']['general_notes']);
        } else {
            $this->db->set('general_notes', '');
        }
        $this->db->where('artwork_id', $artwork_id);
        $this->db->update('ts_artworks');
        $this->db->select('o.order_blank');
        $this->db->from('ts_orders o');
        $this->db->where('o.order_id',$order_id);
        $blankres=$this->db->get()->row_array();
        $blank=0;
        if ($blankres['order_blank']=='1') {
            $blank=1;
        }
        if ($blank==1) {
            $artsync=$this->artlead_model->art_blank_changestage($order, $artwork, $artwork_id, $artsync, $user_id);
        } else {
            $artsync=$this->artlead_model->art_common_changestage($order, $artwork, $artwork_id, $artsync, $user_id);
        }
        // Update Order -> ART TYPE
        $arttype = $this->artlead_model->order_arttype($order_id);
        $this->db->where('order_id', $order_id);
        $this->db->set('arttype', $arttype);
        $this->db->update('ts_orders');

        // artwork_model
        if ($savedorder_id>0) {
            // Save changes
            $neworddata=$this->get_leadorder($savedorder_id, $user_id);
            // $this->firephp->log($compare_array['order'],'Order');
            // Lets go
            $this->artwork_model->leadorder_changeslog($compare_array, $neworddata, $user_id);
        } else {
            $newzip='';
            foreach ($shipping_address as $adrrow) {
                if (!empty($adrrow['zip'])) {
                    $newzip.=$adrrow['zip'].' ';
                }
            }
            $newzip=trim($newzip);
            if (abs($order['shipping'])>1.00 && empty($newzip)) {
                $this->_emptyzip_notification($leadorder, $user_id);
            }
        }
        // Clean Session
        usersession($ordersession, NULL);
        if ($order_type=='new') {
            $this->_prepare_netexport($artsync, $artsyncdoc);
        }
        return $out;
    }

    // Check Old Order
    private function _check_old_order($data) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if (intval($data['order_date'])==0) {
            $res['msg']='Enter Order Date';
            return $res;
        }
        if ($data['customer_name']=='') {
            $res['msg']='Enter Customer Name';
            return $res;
        }
        if ($data['customer_email']=='') {
            $res['msg']='Enter Customer Email';
            return $res;
        }
        if (!valid_email_address($data['customer_email'])) {
            $res['msg']='Enter Valid Email';
            return $res;
        }
        if (floatval($data['revenue'])==0) {
            $res['msg']='Enter Valid Order Revenue';
            return $res;
        }
        if (floatval($data['shipping'])==0) {
            $res['msg']='Enter Valid Shipping Cost';
            return $res;
        }
        if ($data['order_cog']!='' && !is_numeric($data['order_cog'])) {
            $res['msg']='COG value is not numeric';
            return $res;
        }

        if (intval($data['order_qty'])<=0) {
            $res['msg']='Order QTY required';
            return $res;
        }

        if (empty($data['shipdate']) || intval($data['shipdate'])<=0) {
            $res['msg']='Shipping Date is required';
            return $res;
        }
        $res['result']=$this->success_result;
        return $res;
    }

    private function _save_oldorder($data, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        // Calc New Profit
        $order_id=$data['order_id'];
        if ($data['order_cog']=='') {
            $profit=floatval($data['revenue'])*$this->config->item('default_profit')/100;
            $profit_perc=NULL;
        } else {
            $profit=$this->_leadorder_profit($data);
            if (floatval($data['revenue'])==0) {
                $profit_perc=NULL;
            } else {
                $profit_perc=round(($profit/floatval($data['revenue']))*100,1);
            }
        }
        $res['profit']=$profit;
        $order_cog=($data['order_cog']=='' ? NULL : floatval($data['order_cog']));
        // Update Order
        $this->db->set('order_date',$data['order_date']);
        $this->db->set('brand_id',$data['brand_id']);
        $this->db->set('customer_name',$data['customer_name']);
        $this->db->set('customer_email',$data['customer_email']);
        $this->db->set('revenue', floatval($data['revenue']));
        $this->db->set('shipping', floatval($data['shipping']));
        $this->db->set('is_shipping',$data['is_shipping']);
        $this->db->set('tax',floatval($data['tax']));
        // $this->db->set('cc_fee',$cc_fee*$ccfee_sum);
        $this->db->set('cc_fee', $data['cc_fee']);
        $this->db->set('order_cog',$order_cog);
        $this->db->set('update_date',time());
        $this->db->set('update_usr',$user_id);
        $this->db->set('item_id',$data['item_id']);
        $this->db->set('order_itemnumber',$data['order_itemnumber']);
        $this->db->set('order_items',$data['order_items']);
        $this->db->set('order_qty', $data['order_qty']);
        $this->db->set('shipdate', $data['shipdate']);
        $this->db->set('order_blank', $data['order_blank']);
        $this->db->set('order_rush', $data['order_rush']);
        /* Profit, Profit Perc */
        $this->db->set('profit',$profit);
        $this->db->set('profit_perc',$profit_perc);
        $this->db->set('order_usr_repic',$data['order_usr_repic']);
        $this->db->set('order_system','old');
        if ($order_id==0) {
            $this->db->set('create_usr',$user_id);
            $this->db->set('create_date',time());
            $this->db->insert('ts_orders');
            if ($this->db->insert_id()==0) {
                $res['msg']='Error during save order data';
                return $res;
            } else {
                $res['result']=$order_id=$this->db->insert_id();
                $this->load->model('orders_model');
                $neworder_num=$this->orders_model->get_last_ordernum();
                $this->db->set('order_num',$neworder_num);
                $this->db->where('order_id', $order_id);
                $this->db->update('ts_orders');
                $res['neworder']=$neworder_num;
                /* Get New total */
            }
        } else {
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_orders');
            $res['result']=$this->success_result;
            /* Try to Update ARTWORK */
            $this->db->set('customer',$data['customer_name']);
            $this->db->set('customer_email',$data['customer_email']);
            $this->db->set('item_id',$data['item_id']);
            $this->db->set('item_number',$data['order_itemnumber']);
            $this->db->set('other_item',$data['order_items']);
            $this->db->set('item_name',$data['order_items']);
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_artworks');
        }
        return $res;
    }

    // Check New Order
    private function _check_new_order($leadorder) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $data=$leadorder['order'];
        $order_id=$data['order_id'];

        if (intval($data['order_date'])==0) {
            $res['msg']='Enter Order Date';
            return $res;
        }
        if ($data['customer_name']=='') {
            $res['msg']='Enter Customer Name';
            return $res;
        }
        if (floatval($data['revenue'])==0) {
            $res['msg']='Enter Valid Order Revenue';
            return $res;
        }
        if (floatval($data['shipping'])==0) {
            $res['msg']='Enter Valid Shipping Cost';
            return $res;
        }
        if ($data['order_cog']!='' && !is_numeric($data['order_cog'])) {
            $res['msg']='COG value is not numeric';
            return $res;
        }
        if (abs(floatval($data['mischrg_val1']))>0 && empty($data['mischrg_label1'])) {
            $res['msg']='Empty Misc Charge Notification (row 1)';
            return $res;
        }
        if (abs(floatval($data['mischrg_val2']))>0 && empty($data['mischrg_label2'])) {
            $res['msg']='Empty Misc Charge Notification (row 2)';
            return $res;
        }
        if (abs(floatval($data['discount_val']))>0 && empty($data['discount_descript'])) {
            $res['msg']='All Discounts Must Have Valid Reason Explaining Why';
            return $res;
        }
        if (intval($data['order_qty'])<=0) {
            $res['msg']='Order QTY required';
            return $res;
        }

        if (empty($data['shipdate']) || intval($data['shipdate'])<=0) {
            $res['msg']='Shipping Date is required';
            return $res;
        }
        $contchk=$this->_check_contact_info($leadorder['contacts']);
        if ($contchk==$this->error_result) {
            $res['msg']='Enter Customer Email';
            return $res;
        }
        if ($order_id==0 && $data['showbilladdress']==0) {
            $billing=$leadorder['billing'];
            if (empty($billing['customer_name'])) {
                $res['msg']='Enter Billing Customer Name';
                return $res;
            }
            if (empty($billing['address_1'])) {
                $res['msg']='Enter Billing Address';
                return $res;
            }
            if (empty($billing['city'])) {
                $res['msg']='Enter Billing City';
                return $res;
            }
            if (empty($billing['zip'])) {
                $res['msg']='Enter Billing Zip/Postal Code';
                return $res;
            }
            if (empty($billing['country_id'])) {
                $res['msg']='Enter Billing Country';
                return $res;
            }
        }
        $shipadr=$leadorder['shipping_address'];
        if (count($shipadr)>1) {
            // Check # of Shipping Items
            $numship=0;
            foreach ($shipadr as $srow) {
                $numship+=intval($srow['item_qty']);
            }
            if ($numship!=$data['order_qty']) {
                $res['msg']='Shipping QTY not equal Total QTY';
                return $res;
            }
        }
        $res['result']=$this->success_result;
        return $res;
    }

    // Save New Order
    private function _save_neworder($data, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        // Calc New Profit
        $order_id=$data['order_id'];
        if ($data['order_cog']=='') {
            $profit=floatval($data['revenue'])*$this->config->item('default_profit')/100;
            $profit_perc=NULL;
        } else {
            $profit=$this->_leadorder_profit($data);
            if (floatval($data['revenue'])==0) {
                $profit_perc=NULL;
            } else {
                $profit_perc=round(($profit/floatval($data['revenue']))*100,1);
            }
        }
        $res['profit']=$profit;
        $order_cog=($data['order_cog']=='' ? NULL : floatval($data['order_cog']));
        // Move upload file
        if ($data['balance_manage']==3) {
            if ($data['newappcreditlink']<0) {
                // New Upload Link
                $docsrc=$data['credit_applink'];
                $docparams=extract_filename($docsrc);
                $newdocname=uniq_link(15).'.'.$docparams['ext'];
                $doctarget=$this->config->item('creditappdoc').$newdocname;
                if (!is_dir($this->config->item('creditappdoc'))) {
                    mkdir($this->config->item('creditappdoc'),0777);
                }
                $rescp=@copy($docsrc, $doctarget);
                if ($rescp) {
                    $data['credit_applink']=$this->config->item('creditappdoc_relative').$newdocname;
                }
            }
        } else {
            $data['credit_applink']=NULL;
        }
        // Update Order
        $this->db->set('order_date',$data['order_date']);
        $this->db->set('brand_id',(empty($data['brand_id']) ? $this->config->item('default_brand') : $data['brand_id']));
        $this->db->set('customer_name',$data['customer_name']);

        $this->db->set('revenue', floatval($data['revenue']));
        $this->db->set('shipping', floatval($data['shipping']));
        $this->db->set('is_shipping',intval($data['is_shipping']));

        $this->db->set('tax',floatval($data['tax']));
        $this->db->set('cc_fee', intval($data['cc_fee']));
        $this->db->set('order_cog',$order_cog);
        $this->db->set('update_date',time());
        $this->db->set('update_usr',$user_id);
        $this->db->set('item_id',(empty($data['item_id']) ? NULL : $data['item_id']));
        $this->db->set('order_itemnumber',$data['order_itemnumber']);
        $this->db->set('order_items',$data['order_items']);
        $this->db->set('order_qty', intval($data['order_qty']));
        $this->db->set('shipdate', $data['shipdate']);
        $this->db->set('order_blank', intval($data['order_blank']));
        $this->db->set('order_rush', intval($data['order_rush']));
        // Profit, Profit Perc
        $this->db->set('profit',$profit);
        $this->db->set('profit_perc',$profit_perc);
        $this->db->set('order_usr_repic',$data['order_usr_repic']);
        $this->db->set('balance_manage', $data['balance_manage']);
        $this->db->set('balance_term', $data['balance_term']);
        $this->db->set('credit_appdue', $data['credit_appdue']);
        $this->db->set('credit_applink',$data['credit_applink']);
        $this->db->set('order_system', 'new');
        // Miscs, Discount
        $this->db->set('invoice_message', $data['invoice_message']);
        $this->db->set('mischrg_label1', $data['mischrg_label1']);
        $this->db->set('mischrg_val1', floatval($data['mischrg_val1']));
        $this->db->set('mischrg_label2', $data['mischrg_label2']);
        $this->db->set('mischrg_val2', floatval($data['mischrg_val2']));
        $this->db->set('discount_label', $data['discount_label']);
        $this->db->set('discount_val', floatval($data['discount_val']));
        $this->db->set('discount_descript', $data['discount_descript']);
        if ($order_id==0) {
            $confirm=strtoupper(uniq_link(2,'chars')).'-'.uniq_link(5,'digits');
            $this->db->set('order_confirmation', $confirm);
            $this->db->set('create_usr',$user_id);
            $this->db->set('create_date',time());
            $this->db->set('brand', $data['brand']);
            $this->db->insert('ts_orders');
            if ($this->db->insert_id()==0) {
                $res['msg']='Error during save order data';
                return $res;
            } else {
                $res['result']=$order_id=$this->db->insert_id();
                $this->load->model('orders_model');
                $neworder_num=$this->orders_model->get_last_ordernum();
                // $this->db->set('order_num',$neworder_num+1);
                $this->db->set('order_num',$neworder_num);
                $this->db->where('order_id', $order_id);
                $this->db->update('ts_orders');
                // $res['neworder']=$neworder_num+1;
                $res['neworder']=$neworder_num;
                /* Get New total */
            }
        } else {
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_orders');
            $res['result']=$this->success_result;
            // Update ART View parameters
            $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
            $this->db->from('ts_orders');
            $this->db->where('order_id',$order_id);
            $statres=$this->db->get()->row_array();
            $this->db->where('order_id',$order_id);
            $this->db->set('order_artview', $statres['aprrovview']);
            $this->db->set('order_placed', $statres['placeord']);
            $this->db->update('ts_orders');
            // Try to Update ARTWORK
            $this->db->set('customer',$data['customer_name']);
            $this->db->set('customer_email',$data['customer_email']);
            $this->db->set('item_id',$data['item_id']);
            $this->db->set('item_number',$data['order_itemnumber']);
            $this->db->set('other_item',$data['order_items']);
            $this->db->set('item_name',$data['order_items']);
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_artworks');
        }
        // Add / edit Credit App Doc
        $this->load->model('creditapp_model');
        if ($data['balance_manage']==3) {
            $datapp=array(
                'credit_app_id'=>$data['credit_app_id'],
                'user'=>$user_id,
                'customer'=>$data['customer_name'],
                'document_link'=>$data['credit_applink'],
                'order_id'=>$order_id,
            );
            $this->creditapp_model->update_order_creditapp($datapp);
        } else {
            $this->creditapp_model->remove_order_creditapp($order_id);
        }
        return $res;
    }

    // Save Order Contacts
    private function _save_order_contacts($contacts, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $first_email=$art_contact=$art_email='';
        $default_email=$default_contact='';
        foreach ($contacts as $row) {
            $phone=  str_replace('-', '', $row['contact_phone']);
            if (!empty($phone)) {
                $phone=formatPhoneNumber($phone,1);
            }
            if (!empty($row['contact_emal']) && empty($default_email)) {
                $default_email=$row['contact_emal'];
                $default_contact=$row['contact_name'];
            }
            $this->db->set('contact_name', $row['contact_name']);
            $this->db->set('contact_phone', $phone);
            $this->db->set('contact_emal', $row['contact_emal']);
            $this->db->set('contact_art', $row['contact_art']);
            $this->db->set('contact_inv', $row['contact_inv']);
            $this->db->set('contact_trk', $row['contact_trk']);
            if ($row['order_contact_id']<0) {
                $this->db->set('order_id', $order_id);
                $this->db->insert('ts_order_contacts');
                if (!$this->db->insert_id()) {
                    $res['msg']='Insert in Contact Fired with error';
                    return $res;
                }
            } else {
                $this->db->where('order_contact_id', $row['order_contact_id']);
                $this->db->update('ts_order_contacts');
            }
            if ($first_email=='' && $row['contact_inv']==1 && !empty($row['contact_emal'])) {
                $first_email=$row['contact_emal'];
            }
            if ($art_email=='' && $row['contact_art']==1 && !empty($row['contact_emal'])) {
                $art_email=$row['contact_emal'];
                $art_contact=$row['contact_name'];
            }
        }
        if (empty($first_email)) {
            $first_email=$default_email;
        }
        if (empty($art_email)) {
            $art_email=$default_email;
            $art_contact=$default_contact;
        }
        if (!empty($first_email)) {
            $this->db->set('customer_email', $first_email);
            $this->db->where('order_id', $order_id);
            $this->db->update('ts_orders');
        }
        if (!empty($art_email)) {
            $this->db->set('customer',$art_contact);
            $this->db->set('customer_email',$art_email);
            $this->db->where('order_id',$order_id);
            $this->db->update('ts_artworks');
        }
        $res['result']=$this->success_result;
        return $res;
    }

    // Save Order Items Data
    private function _save_order_items($order_items, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->load->model('orders_model');
        if (count($order_items)==1) {
            if ($order_items[0]['item_id']<0) {
                $itemcolors=$order_items[0]['items'];
                $this->db->set('order_items', $itemcolors[0]['item_description']);
            } else {
                $this->db->set('order_items', $order_items[0]['item_name']);
            }
            $this->db->set('order_itemnumber', $order_items[0]['item_number']);
            $this->db->set('item_id', $order_items[0]['item_id']);
        } else {
            // Check Multi Item
            $item_id=$order_items[0]['item_id'];
            $itemcolors=$order_items[0]['items'];
            $itemdescr=$itemcolors[0]['item_description'];
            foreach ($order_items as $irow) {
                if ($item_id!=$irow['item_id']) {
                    $item_id=$this->config->item('multy_id');
                    break;
                }
                foreach ($irow['items'] as $colorrow) {
                    if ($colorrow['item_description']!=$itemdescr) {
                        $itemdescr='';
                    }
                }
            }
            $orditm=$this->orders_model->get_itemdat($item_id);
            if ($item_id<0 && $item_id!=$this->config->item('multy_id') && !empty($itemdescr)) {
                $this->db->set('order_items', $itemdescr);
            } else {
                $this->db->set('order_items', $orditm['item_name']);
            }
            $this->db->set('order_itemnumber', $orditm['item_number']);
            $this->db->set('item_id', $item_id);
        }
        $this->db->where('order_id', $order_id);
        $this->db->update('ts_orders');
        $totalqty=0;
        $itmidx=0;
        foreach ($order_items as $row) {
            $rowprice=($row['item_qty']==0 ? 0 : $row['item_subtotal']/$row['item_qty']);
            $this->db->set('item_id', $row['item_id']);
            $this->db->set('item_qty', $row['item_qty']);
            $this->db->set('item_price', $rowprice);
            $this->db->set('imprint_price', $row['print_price']);
            $this->db->set('setup_price', $row['setup_price']);
            $this->db->set('base_price', $row['base_price']);
            if ($row['order_item_id']<0) {
                $this->db->set('order_id', $order_id);
                $this->db->insert('ts_order_items');
                if (!$this->db->insert_id()) {
                    $res['msg']='Error During Insert data into Order Items';
                    return $res;
                } else {
                    $row['order_item_id']=$this->db->insert_id();
                }
            } else {
                $this->db->where('order_item_id', $row['order_item_id']);
                $this->db->update('ts_order_items');
            }
            $order_item_id=$row['order_item_id'];
            // Insert data about Item Colors
            $itemcolors=$row['items'];
            foreach ($itemcolors as $irow) {
                $this->db->set('item_color', $irow['item_color']);
                $this->db->set('item_description', $irow['item_description']);
                $this->db->set('item_qty', $irow['item_qty']);
                $this->db->set('item_price', $irow['item_price']);
                if (isset($irow['printshop_item_id']) && !empty($irow['printshop_item_id'])) {
                    $this->db->set('printshop_item_id', $irow['printshop_item_id']);
                } else {
                    $this->db->set('printshop_item_id', NULL);
                }
                if ($irow['item_id']<0) {
                    $this->db->set('order_item_id', $order_item_id);
                    $this->db->insert('ts_order_itemcolors');
                } else {
                    $this->db->where('order_itemcolor_id', $irow['item_id']);
                    $this->db->update('ts_order_itemcolors');
                }
                $totalqty+=$irow['item_qty'];
            }

            $imprints=$row['imprints'];
            foreach ($imprints as $prow) {
                if ($prow['delflag']==1) {
                    if ($prow['order_imprint_id']>0) {
                        $this->db->where('order_imprint_id', $prow['order_imprint_id']);
                        $this->db->delete('ts_order_imprints');
                    }
                } else {
                    $this->db->set('imprint_description', $prow['imprint_description']);
                    $this->db->set('imprint_item', $prow['imprint_item']);
                    $this->db->set('imprint_qty', $prow['imprint_qty']);
                    $this->db->set('imprint_price', $prow['imprint_price']);
                    if ($prow['order_imprint_id']<0) {
                        $this->db->set('order_item_id', $order_item_id);
                        $this->db->insert('ts_order_imprints');
                    } else {
                        $this->db->where('order_imprint_id', $prow['order_imprint_id']);
                        $this->db->update('ts_order_imprints');
                    }
                }
            }
            $imprint_details=$row['imprint_details'];
            $detidx=0;
            $numpp=1;
            foreach ($imprint_details as $drow) {
                $this->db->set('imprint_active',intval($drow['active']));
                if (intval($drow['active'])==0) {
                    $this->db->set('imprint_type','NEW');
                    $this->db->set('repeat_note', NULL);
                    $this->db->set('location_id',NULL);
                    $this->db->set('num_colors',1);
                    $this->db->set('print_1',$numpp==1 ? 0 : $row['print_price']);
                    $this->db->set('print_2',$row['print_price']);
                    $this->db->set('print_3',$row['print_price']);
                    $this->db->set('print_4',$row['print_price']);
                    $this->db->set('setup_1',$row['setup_price']);
                    $this->db->set('setup_2',$row['setup_price']);
                    $this->db->set('setup_3',$row['setup_price']);
                    $this->db->set('setup_4',$row['setup_price']);
                    $this->db->set('extra_cost', 0);
                    $this->db->set('artwork_art_id', NULL);
                } else {
                    $note=($drow['imprint_type']=='NEW' ? NULL : $drow['repeat_note']);
                    if (isset($drow['artwork_art_id'])) {
                        $artid=(intval($drow['artwork_art_id'])>0 ? $drow['artwork_art_id'] : NULL);
                    } else {
                        $artid=NULL;
                    }
                    $this->db->set('imprint_type',$drow['imprint_type']);
                    $this->db->set('repeat_note', $note);
                    $this->db->set('location_id', (empty($drow['location_id']) ? NULL : $drow['location_id']));
                    $this->db->set('num_colors',$drow['num_colors']);
                    $this->db->set('print_1',$drow['print_1']);
                    $this->db->set('print_2',$drow['print_2']);
                    $this->db->set('print_3',$drow['print_3']);
                    $this->db->set('print_4',$drow['print_4']);
                    $this->db->set('setup_1',$drow['setup_1']);
                    $this->db->set('setup_2',$drow['setup_2']);
                    $this->db->set('setup_3',$drow['setup_3']);
                    $this->db->set('setup_4',$drow['setup_4']);
                    $this->db->set('extra_cost', floatval($drow['extra_cost']));
                    $this->db->set('artwork_art_id', $artid);
                }
                if ($drow['order_imprindetail_id']<0) {
                    $this->db->set('order_item_id', $order_item_id);
                    $this->db->insert('ts_order_imprindetails');
                    $order_items[$itmidx]['imprint_details'][$detidx]['order_imprindetail_id']=$this->db->insert_id();
                } else {
                    $this->db->where('order_imprindetail_id', $drow['order_imprindetail_id']);
                    $this->db->update('ts_order_imprindetails');
                }
                $detidx++;
                $numpp++;
            }
            $itmidx++;
        }
        $this->db->set('order_qty', $totalqty);
        $this->db->where('order_id', $order_id);
        $this->db->update('ts_orders');
        $res['result']=$this->success_result;
        $res['order_items']=$order_items;
        return $res;
    }

    // Save Shipping Info for Order
    private function _save_order_shipping($shipping, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->db->set('event_date', (empty($shipping['event_date']) ? NULL : $shipping['event_date']));
        $this->db->set('rush_idx', $shipping['rush_idx']);
        $this->db->set('rush_price', floatval($shipping['rush_price']));
        $this->db->set('shipdate', intval($shipping['shipdate']));
        $this->db->set('arrive_date', intval($shipping['arrive_date']));
        $this->db->set('rush_list', $shipping['rush_list']);
        if ($shipping['order_shipping_id']<0) {
            $this->db->set('order_id', $order_id);
            $this->db->insert('ts_order_shippings');
        } else {
            $this->db->where('order_shipping_id', $shipping['order_shipping_id']);
            $this->db->update('ts_order_shippings');
        }
        $res['result']=$this->success_result;
        return $res;
    }

    // Save Shipping Address
    private function _save_order_shipaddress($shipping_address, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        foreach ($shipping_address as $row) {
            // Check Tax
            $taxexempt=intval($row['tax_exempt']);
            if ($taxexempt==1) {
                $this->db->set('tax_reason', ($row['tax_reason']=='' ? NULL : $row['tax_reason']));
                if ($row['tax_exemptdocid']<0) {
                    // New Document
                    $filesrc=$row['tax_exemptdoc'];
                    $purefile=str_replace($this->config->item('upload_path_preload'), '', $filesrc);
                    $sourcefile=$this->config->item('upload_path_preload').$purefile;
                    $targetfile=$this->config->item('documattach').$purefile;
                    $copyres=@copy($sourcefile, $targetfile);
                    if ($copyres) {
                        // Copy finished successfully
                        $docfile=$this->config->item('documattach_path').$purefile;
                        $this->db->set('tax_exemptdoc', $docfile);
                        $this->db->set('tax_exemptdocsrc', $row['tax_exemptdocsrc']);
                    } else {
                        $this->db->set('tax_exemptdoc', NULL);
                        $this->db->set('tax_exemptdocsrc', NULL);
                    }
                }
            } else {
                $this->db->set('tax_reason', NULL);
                $this->db->set('tax_exemptdoc', NULL);
                $this->db->set('tax_exemptdocsrc', NULL);
            }
            $this->db->set('country_id', $row['country_id']);
            $this->db->set('address', $row['address']);
            $this->db->set('ship_contact', $row['ship_contact']);
            $this->db->set('ship_company', $row['ship_company']);
            $this->db->set('ship_address1', $row['ship_address1']);
            $this->db->set('ship_address2', $row['ship_address2']);
            $this->db->set('city', $row['city']);
            $this->db->set('state_id', ($row['state_id']=='' ? NULL : $row['state_id']));
            $this->db->set('zip', $row['zip']);
            $this->db->set('item_qty',intval($row['item_qty']));
            $this->db->set('ship_date', empty($row['ship_date']) ? 0 : $row['ship_date']);
            $this->db->set('arrive_date', empty($row['arrive_date']) ? 0 : $row['arrive_date']);
            $this->db->set('shipping', floatval($row['shipping']));
            $this->db->set('sales_tax', floatval($row['sales_tax']));
            $this->db->set('resident', intval($row['resident']));
            $this->db->set('ship_blind', intval($row['ship_blind']));
            $this->db->set('tax', floatval($row['tax']));
            $this->db->set('tax_exempt', $taxexempt);

            if ($row['order_shipaddr_id']<0) {
                $this->db->set('order_id', $order_id);
                $this->db->insert('ts_order_shipaddres');
                $row['order_shipaddr_id']=$this->db->insert_id();
            } else {
                $this->db->where('order_shipaddr_id', $row['order_shipaddr_id']);
                $this->db->update('ts_order_shipaddres');
            }
            $costs=$row['shipping_costs'];

            foreach ($costs as $crow) {
                if ($crow['delflag']==1) {
                    if ($crow['order_shipcost_id']>0) {
                        $this->db->where('order_shipcost_id', $crow['order_shipcost_id']);
                        $this->db->delete('ts_order_shipcosts');
                    }
                } else {
                    $this->db->set('shipping_method', $crow['shipping_method']);
                    $this->db->set('shipping_cost', $crow['shipping_cost']);
                    $this->db->set('arrive_date', $crow['arrive_date']);
                    $this->db->set('current', $crow['current']);
                    if ($crow['order_shipcost_id']<0) {
                        $this->db->set('order_shipaddr_id', $row['order_shipaddr_id']);
                        $this->db->insert('ts_order_shipcosts');
                    } else {
                        $this->db->where('order_shipcost_id', $crow['order_shipcost_id']);
                        $this->db->update('ts_order_shipcosts');
                    }
                }
            }
            $delivered=1;
            $cntpack=$cntdeliv=0;
            $packages=$row['packages'];
            foreach ($packages as $prow) {
                if ($prow['delflag']==1) {
                    if ($prow['order_shippack_id']>0) {
                        $this->db->where('order_shippack_id', $prow['order_shippack_id']);
                        $this->db->delete('ts_order_shippacks');
                    }
                } else {
                    $this->db->set('deliver_service', $prow['deliver_service']);
                    $this->db->set('track_code', $prow['track_code']);
                    $this->db->set('track_date', $prow['track_date']);
                    $this->db->set('send_date', $prow['send_date']);
                    $this->db->set('delivered', $prow['delivered']);
                    $this->db->set('delivery_address', $prow['delivery_address']);
                    if ($prow['order_shippack_id']<0) {
                        $this->db->set('order_shipaddr_id', $row['order_shipaddr_id']);
                        $this->db->insert('ts_order_shippacks');
                        $packid_id=$this->db->insert_id();
                    } else {
                        $this->db->where('order_shippack_id',$prow['order_shippack_id']);
                        $this->db->update('ts_order_shippacks');
                        $packid_id=$prow['order_shippack_id'];
                    }
                    $cntpack++;
                    if ($prow['delivered']>0) {
                        $cntdeliv++;
                        $delivered=($delivered<$prow['delivered'] ? $prow['delivered'] : $delivered);
                    }
                    if (isset($prow['logs'])) {
                        // Insert data into log
                        foreach ($prow['logs'] as $lrow) {
                            $this->db->set('package_num', $lrow['package_num']);
                            $this->db->set('status', $lrow['status']);
                            $this->db->set('date', $lrow['date']);
                            $this->db->set('address', $lrow['address']);
                            if ($lrow['log_id']<0) {
                                $this->db->set('order_shippack_id', $packid_id);
                                $this->db->insert('ts_shippack_tracklogs');
                            } else {
                                $this->db->where('shippack_tracklog_id', $lrow['log_id']);
                                $this->db->update('ts_shippack_tracklogs');
                            }
                        }
                    }
                }
            }
        }
        if ($cntpack>0 && $cntdeliv==$cntpack) {
            $this->db->set('deliverydate', $delivered);
            $this->db->where('order_id', $order_id);
            $this->db->update('ts_orders');
        }
        $res['result']=$this->success_result;
        return $res;
    }

    // Copy first shipping address to billing
    private function _billingaddres_copy($shipping_address, $biladr) {
        $shipadr=$shipping_address[0];
        if (empty($biladr['customer_name']) && empty($biladr['address_1']) && empty($biladr['city'])) {
            $biladr['customer_name']=$shipadr['ship_contact'];
            $biladr['company']=$shipadr['ship_company'];
            $biladr['address_1']=$shipadr['ship_address1'];
            $biladr['address_2']=$shipadr['ship_address2'];
            $biladr['country_id']=$shipadr['country_id'];
            $biladr['state_id']=$shipadr['state_id'];
            $biladr['city']=$shipadr['city'];
            $biladr['zip']=$shipadr['zip'];
        }
        return $biladr;
    }

    // Save billing Info
    private function _save_order_billings($billing, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->db->set('customer_name', (empty($billing['customer_name']) ? NULL : $billing['customer_name']));
        $this->db->set('company', (empty($billing['company']) ? NULL : $billing['company']));
        $this->db->set('customer_ponum', (empty($billing['customer_ponum']) ? NULL : $billing['customer_ponum']));
        $this->db->set('address_1', (empty($billing['address_1']) ? NULL : $billing['address_1']));
        $this->db->set('address_2', (empty($billing['address_2']) ? NULL : $billing['address_2'] ));
        $this->db->set('city', (empty($billing['city']) ? NULL : $billing['city']));
        $this->db->set('state_id', (empty($billing['state_id']) ? NULL : $billing['state_id']));
        $this->db->set('zip', (empty($billing['zip']) ? NULL : $billing['zip']));
        $this->db->set('country_id', $billing['country_id']);
        if ($billing['order_billing_id']<0) {
            $this->db->set('order_id', $order_id);
            $this->db->insert('ts_order_billings');
        } else {
            $this->db->where('order_billing_id', $billing['order_billing_id']);
            $this->db->update('ts_order_billings');
        }
        $res['result']=$this->success_result;
        return $res;
    }

    // Save Charge Data
    private function _save_order_chargedata($charges, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        foreach ($charges as $row) {
            $this->db->set('amount', floatval($row['amount']));
            $this->db->set('cardnum', $row['cardnum']);
            $this->db->set('exp_month', intval($row['exp_month']));
            $this->db->set('exp_year', intval($row['exp_year']));
            $this->db->set('cardcode', $row['cardcode']);
            $this->db->set('autopay', intval($row['autopay']));
            if ($row['order_payment_id']<0) {
                $this->db->set('order_id', $order_id);
                $this->db->insert('ts_order_payments');
            } else {
                $this->db->where('order_payment_id', $row['order_payment_id']);
                $this->db->update('ts_order_payments');
            }
        }
        $res['result']=$this->success_result;
        return $res;
    }

    // Save new manual payments
    private function _save_order_payments($payments, $order_id, $user_id) {
        $res=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        foreach ($payments as $row) {
            if ($row['batch_id']<0) {
                $this->db->set('create_usr', $user_id);
                $this->db->set('create_date', date('Y-m-d H:i:s'));
                $this->db->set('update_usr', $user_id);
                $this->db->set('batch_date', $row['batch_date']);
                $this->db->set('order_id', $order_id);
                $this->db->set('batch_amount', $row['batch_amount']);
                $this->db->set('batch_other', $row['batch_other']);
                $this->db->set('batch_writeoff', $row['batch_writeoff']);
                $this->db->set('batch_type', $row['batch_type']);
                $this->db->set('batch_num', $row['batch_num']);
                $this->db->set('batch_received', 1);
                $this->db->insert('ts_order_batches');
            }
        }
        $res['result']=$this->success_result;
        return $res;
    }


    // Delete old order components
    private function _delete_leadorder_components($delrecords) {
        foreach ($delrecords as $row) {
            switch ($row['entity']) {
                case 'order_items':
                    $this->db->where('order_item_id', $row['id']);
                    $this->db->delete('ts_order_items');
                    break;
                case 'shipping_address':
                    $this->db->where('order_shipaddr_id', $row['id']);
                    $this->db->delete('ts_order_shipaddres');
                    break;
            }
        }
        return TRUE;
    }


    // Net Profit per week
    private function _get_netpofitweek($order_date, $brand) {
        $this->db->select('np.*, netprofit_profit(datebgn, dateend,\''.$brand.'\') as gross_profit',FALSE);
        $this->db->from('netprofit np');
        $this->db->where('np.profit_month',NULL);
        $this->db->where('np.datebgn <= ',$order_date);
        $this->db->where('np.dateend > ',$order_date);
        $netdat=$this->db->get()->row_array();

        $retval=array(
            'netdat_id'=>0,
            'flag_note'=>0,
            'rundat'=>0,
            'oldtotalrun'=>0,
            'totalcost'=>0,
            'netprofit'=>0,
            'olddebt'=>0,
        );
        if (isset($netdat['profit_id']) && $netdat['debtinclude']==1) {
            $retval['flag_note']=1;
            $retval['netdat_id']=$netdat['profit_id'];
            $this->load->model('balances_model');
            $total_options=array(
                'type'=>'week',
                'start'=>$this->config->item('netprofit_start'),
                'brand' => $brand,
            );
            $this->load->model('balances_model');
            $rundat=$this->balances_model->get_netprofit_runs($total_options);
            $oldtotalrun=$rundat['out_debtval'];
            $totalcost=floatval($netdat['profit_operating'])+floatval($netdat['profit_payroll'])+floatval($netdat['profit_advertising'])+floatval($netdat['profit_projects'])+floatval($netdat['profit_purchases']);
            $netprofit=floatval($netdat['gross_profit'])-$totalcost;
            $olddebt=floatval($netprofit)-floatval($netdat['profit_owners'])-floatval($netdat['profit_saved'])-floatval($netdat['od2']);
            $retval['rundat']=$rundat;
            $retval['oldtotalrun']=$oldtotalrun;
            $retval['totalcost']=$totalcost;
            $retval['netprofit']=$netprofit;
            $retval['olddebt']=$olddebt;
            $retval['datebgn']=$netdat['datebgn'];
            $retval['dateend']=$netdat['dateend'];
        }
        return $retval;
    }

    private function _leadorder_profit($order) {
        $profit=floatval($order['revenue'])-(floatval($order['shipping'])*$order['is_shipping'])-floatval($order['tax'])-floatval($order['cc_fee'])-floatval($order['order_cog']);
        return $profit;
    }

    private function _leadorder_shipping($shipping_address, $shipping) {
        // Rebuild Arrive Date
        $arrivedate=0;
        foreach ($shipping_address as $srow) {
            foreach ($srow['shipping_costs'] as $rcost) {
                if ($rcost['delflag']==0 && $rcost['current']==1) {
                    $arrivedate=($arrivedate<$rcost['arrive_date'] ? $rcost['arrive_date'] : $arrivedate);
                }
            }
        }
        $shipping['arrive_date']=$arrivedate;
        $shipping['arriveclass']='';
        if ($arrivedate!=0 && intval($shipping['event_date'])>0) {
            $eventdate=$shipping['event_date']+$this->config->item('event_time');
            if ($eventdate<$arrivedate) {
                $shipping['arriveclass']='arrivelate';
            }
        }
        $shipping['out_eventdate']=(intval($shipping['event_date'])==0 ? $this->empty_htmlcontent : date('m/d/y', $shipping['event_date']));
        $shipping['out_arrivedate']=(intval($shipping['arrive_date'])==0 ? $this->empty_htmlcontent : date('m/d/y', $shipping['arrive_date']));
        $shipping['out_shipdate']=(intval($shipping['shipdate'])==0 ? $this->empty_htmlcontent : date('m/d/y', $shipping['shipdate']));
        return $shipping;
    }


    private function _leadorder_shipcost($shipaddr) {
        $shipcost=0;
        foreach ($shipaddr as $row) {
            $shipcost+=$row['shipping'];
        }
        return $shipcost;
    }

    private function _changeprofit_notification($netdat, $orderdata, $oldorder, $user_id) {
        $this->load->model('balances_model');
        $this->load->model('orders_model');
        $total_options=array(
            'type'=>'week',
            'start'=>$this->config->item('netprofit_start'),
        );
        $rundat=$this->balances_model->get_netprofit_runs($total_options);
        $newtotalrun=$rundat['out_debtval'];
        // Prepare to notification Email
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
        if ($netdat['oldtotalrun']<0) {
            $outoldrundebt='($'.number_format(abs($netdat['oldtotalrun']),0,'.',',').')';
        } else {
            $outoldrundebt='$'.number_format($netdat['oldtotalrun'],0,'.',',');
        }
        if ($newtotalrun<0) {
            $outnewrundebt='($'.number_format(abs($newtotalrun),0,'.',',').')';
        } else {
            $outnewrundebt='$'.number_format($newtotalrun,0,'.',',');
        }
        $this->db->select('np.*, netprofit_profit(datebgn, dateend) as gross_profit',FALSE);
        $this->db->from('netprofit np');
        $this->db->where('np.profit_id',$netdat['netdat_id']);
        $netdata=$this->db->get()->row_array();
        $totalcost=floatval($netdata['profit_operating'])+floatval($netdata['profit_payroll'])+floatval($netdata['profit_advertising'])+floatval($netdata['profit_projects'])+floatval($netdata['profit_purchases']);
        $netprofit=floatval($netdata['gross_profit'])-$totalcost;
        $newdebt=floatval($netprofit)-floatval($netdata['profit_owners'])-floatval($netdata['profit_saved'])-floatval($netdata['od2']);
        if ($newdebt<0) {
            $outnewdebt='($'.number_format(abs($newdebt),0,'.',',').')';
        } else {
            $outnewdebt='$'.number_format($newdebt,0,'.',',');
        }
        if ($netdat['olddebt']<0) {
            $outolddebt='($'.number_format(abs($netdat['olddebt']),0,'.',',').')';
        } else {
            $outolddebt='$'.number_format(abs($netdat['olddebt']),0,'.',',');
        }

        $orderdata['oldprofit']=$oldorder['profit'];
        $orderdata['oldrevenue']=$oldorder['revenue'];
        $notifoptions=array(
            'orderchange'=>1,
            'orderdata'=>$orderdata,
            'weeknum'=>$weekname,
            'user_id'=>$user_id,
            'oldtotalrun'=>$outoldrundebt,
            'newtotalrun'=>$outnewrundebt,
            'olddebt'=>$outolddebt,
            'newdebt'=>$outnewdebt,
        );
        $this->orders_model->notify_netdebtchanged($notifoptions);
        return TRUE;
    }

    // Get Previous Order
    private function _get_previous_order($order_id, $brand) {
        $order=0;
        $this->db->select('order_id, order_num');
        $this->db->from('ts_orders');
        $this->db->where('order_id < ', $order_id);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        // $this->db->where('is_canceled',0);
        $this->db->order_by('order_id', 'desc');
        $prvres=$this->db->get()->row_array();
        if (isset($prvres['order_id'])) {
            $order=$prvres['order_id'];
        }
        return $order;
    }

    // Get Next Order
    private function _get_next_order($order_id, $brand) {
        $order=0;
        $this->db->select('order_id, order_num');
        $this->db->from('ts_orders');
        $this->db->where('order_id > ', $order_id);
        if ($brand!=='ALL') {
            $this->db->where('brand', $brand);
        }
        // $this->db->where('is_canceled',0);
        $this->db->order_by('order_id', 'asc');
        $nxtres=$this->db->get()->row_array();
        if (isset($nxtres['order_id'])) {
            $order=$nxtres['order_id'];
        }
        return $order;
    }
    // Get GREY Item Data
    public function _get_itemdata($item_id) {
        $item_table='sb_items';
        $venditem_table='sb_vendor_items';

        $this->db->select('i.item_id, i.item_name, i.item_number, i.item_template, i.item_weigth, i.cartoon_qty, i.cartoon_width');
        $this->db->select('i.cartoon_heigh, i.cartoon_depth, i.boxqty, i.charge_pereach, i.charge_perorder, i.printshop_inventory_id as printshop_item_id');
        $this->db->select('v.vendor_zipcode, vi.vendor_item_zipcode');
        $this->db->from("{$item_table} i");
        $this->db->join("{$venditem_table} vi",'vi.vendor_item_id=i.vendor_item_id');
        $this->db->join("vendors v","v.vendor_id=vi.vendor_item_vendor");
        $this->db->where('i.item_id', $item_id);
        $itmres=$this->db->get()->row_array();
        if ($item_id>0 && count($itmres)>0) {
            if (empty($itmres['printshop_item_id'])) {
                // Get Item Price, Item Colors
                // Get Colors
                $this->db->select('item_color as colors');
                $this->db->from('sb_item_colors');
                $this->db->where('item_color_itemid', $item_id);
                $colors=$this->db->get()->result_array();
            } else {
                $this->db->select('color as colors');
                $this->db->from('ts_printshop_colors');
                $this->db->where('printshop_item_id',$itmres['printshop_item_id']);
                $colors=$this->db->get()->result_array();
            }
            $itmres['num_colors']=count($colors);
            if (count($colors)>0) {
                $newcolor=array();
                foreach ($colors as $row) {
                    array_push($newcolor, $row['colors']);
                }
            } else {
                $newcolor=array();
            }
            $itmres['colors']=$newcolor;
            if (!empty($itmres['vendor_item_zipcode'])) {
                $itmres['vendor_zipcode']=$itmres['vendor_item_zipcode'];
            }
            // Get Imprints
            $this->db->select('item_inprint_id, item_inprint_location, item_inprint_size, item_inprint_view');
            $this->db->from('sb_item_inprints');
            $this->db->where('item_inprint_item', $item_id);
            $itmres['imprints']=$this->db->get()->result_array();
        } else {
            $itmres['colors']=array();
            $itmres['num_colors']=0;
            $itmres['imprints']=array();
            $itmres['item_template']=$this->normal_template;
        }
        return $itmres;
    }

    // New price for changed QTY
    public function _get_item_priceqty($item_id, $itemtype, $qty) {
        $item_table='sb_items';
        $this->db->select('item_template');
        $this->db->from("{$item_table}");
        $this->db->where('item_id', $item_id);
        $itmres=$this->db->get()->row_array();
        if (isset($itmres['item_template'])) {
            $itemtype = $itmres['item_template'];
        }
        $price = 0;
        if ($itemtype==$this->normal_template) {
            $price_bases=$this->config->item('normal_price_base');
            $base=$price_bases[0];
            foreach ($price_bases as $row) {
                if ($row>$qty) {
                    $pricefld='item_price_'.$base;
                    $salefld='item_sale_'.$base;
                    $this->db->select("{$pricefld} as price, {$salefld} as sale, item_price_itemid");
                    $this->db->from('sb_item_prices');
                    $this->db->where('item_price_itemid', $item_id);
                    $priceres=$this->db->get()->row_array();
                    if (!isset($priceres['item_price_itemid'])) {
                        $price=0;
                    } else {
                        if ($priceres['sale']=='') {
                            $price=$priceres['price'];
                        } else {
                            $price=$priceres['sale'];
                        }
                    }
                    if (!empty($price)) {
                        break;
                    } else {
                        $base=$row;
                    }
                } else {
                    $base=$row;
                }
            }
            if ($price==0 && $base>0) {
                $pricefld='item_price_'.$base;
                $salefld='item_sale_'.$base;
                $this->db->select("{$pricefld} as price, {$salefld} as sale, item_price_itemid");
                $this->db->from('sb_item_prices');
                $this->db->where('item_price_itemid', $item_id);
                $priceres=$this->db->get()->row_array();
                if (!isset($priceres['item_price_itemid'])) {
                    $price=0;
                } else {
                    if ($priceres['sale']=='') {
                        $price=$priceres['price'];
                    } else {
                        $price=$priceres['sale'];
                    }
                }
            }
        } else {
            $this->db->select('item_qty, price, sale_price');
            $this->db->from('sb_promo_price');
            $this->db->where('item_id', $item_id);
            $this->db->order_by('item_qty');
            $priceres=  $this->db->get()->result_array();

            $price=(intval($priceres[0]['sale_price'])==0 ? $priceres[0]['price'] : $priceres[0]['sale_price']);
            foreach ($priceres as $row) {
                if ($qty<$row['item_qty']) {
                    break;
                } else {
                    $price=(floatval($row['sale_price'])==0 ? $row['price'] : $row['sale_price']);
                }
            }
        }
        return $price;
    }

    public function _get_item_priceimprint($item_id, $pricetype) {
        $this->db->select('item_price_itemid');
        if ($pricetype=='setup') {
            $this->db->select('item_price_setup as price, item_sale_setup as sale');
        } else {
            $this->db->select('item_price_print as price, item_sale_print as sale');
        }
        $this->db->from('sb_item_prices');
        $this->db->where('item_price_itemid', $item_id);
        $priceres=$this->db->get()->row_array();
        if (!isset($priceres['item_price_itemid'])) {
            return 0;
        } else {
            if (!empty($priceres['sale'])) {
                $price=floatval($priceres['sale']);
            } else {
                $price=floatval($priceres['price']);
            }
            return $price;
        }
    }

    public function get_order_contacts($order_id) {
        $this->db->select('*');
        $this->db->from('ts_order_contacts');
        $this->db->where('order_id', $order_id);
        $this->db->order_by('order_contact_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_order_items($order_id, $full=1) {
        $this->load->model('orders_model');
        $this->db->select('*');
        $this->db->from('ts_order_items');
        $this->db->where('order_id', $order_id);
        $this->db->order_by('order_item_id');
        $res=$this->db->get()->result_array();
        if ($full==0) {
            return $res;
        }
        // Begin Build full object
        $out=array();
        foreach ($res as $row) {
            $item_id=$row['item_id'];
            $newitem=array(
                'order_item_id'=>$row['order_item_id'],
                'item_id'=>$item_id,
                'item_number'=>'',
                'item_name'=>'',
                'item_qty'=>$row['item_qty'],
                'colors'=>'',
                'num_colors'=>0,
                'item_template'=>$this->normal_template,
                'item_weigth'=>0,
                'cartoon_qty'=>0,
                'cartoon_width'=>0,
                'cartoon_heigh'=>0,
                'cartoon_depth'=>0,
                'boxqty'=>0,
                'setup_price'=>$row['setup_price'],
                'print_price'=>$row['imprint_price'],
                'item_subtotal'=>0,
                'imprint_subtotal'=>0,
                'vendor_zipcode'=>$this->default_zip,
                'charge_perorder'=>0,
                'charge_peritem'=>0,
                'charge_pereach'=>0,
                'imprint_locations'=>array(),
                // 'qtyinput_class' => 'normal',
                'base_price' => $row['base_price'],
            );
            $qty_class='normal';
            if ($item_id<0) {
                $itemdata=$this->orders_model->get_newitemdat($item_id);
                $newitem['item_number']=$itemdata['item_number'];
                $newitem['item_name']=$itemdata['item_name'];
                $item_price = $newitem['base_price'];
            } else {
                $itemdata=$this->_get_itemdata($item_id);
                if (empty($newitem['setup_price'])) {
                    $setupprice=$this->_get_item_priceimprint($item_id, 'setup');
                    $newitem['setup_price']=$setupprice;
                } else {
                    $setupprice=$newitem['setup_price'];
                }
                if (empty($newitem['print_price'])) {
                    $printprice=$this->_get_item_priceimprint($item_id, 'imprint');
                    $newitem['print_price']=$printprice;
                } else {
                    $printprice=$newitem['print_price'];
                }
                if (empty($newitem['base_price'])) {
                    $item_price = $this->_get_item_priceqty($item_id, $itemdata['item_template'], $row['item_qty']);
                    $newitem['base_price'] = $item_price;
                } else {
                    $item_price = $newitem['base_price'];
                }
                $newitem['item_number']=$itemdata['item_number'];
                $newitem['item_name']=$itemdata['item_name'];
                $newitem['item_template']=$itemdata['item_template'];
                $newitem['item_weigth']=$itemdata['item_weigth'];
                $newitem['cartoon_qty']=$itemdata['cartoon_qty'];
                $newitem['cartoon_width']=$itemdata['cartoon_width'];
                $newitem['cartoon_heigh']=$itemdata['cartoon_heigh'];
                $newitem['cartoon_depth']=$itemdata['cartoon_depth'];
                $newitem['boxqty']=$itemdata['boxqty'];
                $newitem['imprint_locations']=$itemdata['imprints'];
                $newitem['vendor_zipcode']=$itemdata['vendor_zipcode'];
                $newitem['charge_perorder']=$itemdata['charge_perorder'];
                $newitem['charge_pereach']=$itemdata['charge_pereach'];
            }
            $colors=$itemdata['colors'];
            // Colors
            $newitem['colors']=$colors;
            $newitem['num_colors']=count($colors);
            // Get a list of Color Items
            $itemcolors=$this->_get_item_colorrows($row['order_item_id']);

            $items=array();
            $numpp=1;
            $countitems=count($itemcolors);
            foreach ($itemcolors as $irow) {
                $subtotal=$irow['item_qty']*$irow['item_price'];
                $newitem['item_subtotal']+=$subtotal;
                $coloradd=0;
                if ($newitem['num_colors']>1 && $numpp==$countitems) {
                    $coloradd=1;
                }
                $options=array(
                    'order_item_id'=>$irow['order_item_id'],
                    'item_id'=>$irow['item_id'],
                    'colors'=>$newitem['colors'],
                    'item_color'=>$irow['item_color'],
                );
                if ($newitem['num_colors']==0) {
                    // $out_colors=$this->empty_htmlcontent;
                    $out_colors=$this->load->view('leadorderdetails/item_color_input', $options, TRUE);
                } else {
                    // check that current color exist
                    $colors = $newitem['colors'];
                    $found = 0;
                    foreach ($colors as $crow) {
                        if ($crow==$irow['item_color']) {
                            $found=1;
                        }
                    }
                    if ($found==0) {
                        $colors[]=$irow['item_color'];
                        $options['colors']=$colors;
                    }
                    $out_colors=$this->load->view('leadorderdetails/item_color_choice', $options, TRUE);
                }
                $qty_class = 'normal';
                $qty_title = '';
                if ($row['item_id']>0 && floatval(round($item_price,2))!==floatval(round($irow['item_price'],2))) {
                    $qty_class=$newitem['qtyinput_class']='warningprice';
                    $qty_title = 'Base Price '.MoneyOutput($item_price);
                }
                $items[]=array(
                    'order_item_id' =>$irow['order_item_id'],
                    'item_id' =>$irow['item_id'],
                    'item_row' =>$numpp,
                    'item_number' =>$newitem['item_number'],
                    'item_color' =>$irow['item_color'],
                    'colors'=>$colors,
                    'out_colors'=>$out_colors,
                    'num_colors' =>$newitem['num_colors'],
                    'item_description' =>$irow['item_description'],
                    'item_color_add' =>$coloradd,
                    'item_qty' =>$irow['item_qty'],
                    'item_price'=>$irow['item_price'],
                    'item_subtotal'=>MoneyOutput($subtotal),
                    'printshop_item_id'=>(isset($irow['printshop_item_id']) ? $irow['printshop_item_id'] : ''),
                    'qtyinput_class' => $qty_class,
                    'qtyinput_title' => $qty_title,
                );
                $numpp++;
            }
            $newitem['items']=$items;
            // Get Imprints
            $item_imprints=$this->_get_itemorder_imprints($row['order_item_id']);
            $imprints=array();
            $numpp=1;
            foreach ($item_imprints as $irow) {
                $subtotal=$irow['imprint_price']*$irow['imprint_qty'];
                $newitem['imprint_subtotal']+=$subtotal;
                $iprint_price_title = '';
                $iprint_price_class = 'normal';
                if ($item_id>0) {
                    if ($irow['imprint_item']==1) {
                        if ($numpp>1 && floatval(round($irow['imprint_price'],2))!==floatval(round($printprice,2))) {
                            $iprint_price_class = 'warningprice';
                            $iprint_price_title = 'Print price '.MoneyOutput($printprice);
                        }
                        $numpp++;
                    } else {
                        if (substr($irow['imprint_description'],0,12)!=='Repeat Setup' &&  floatval(round($irow['imprint_price'],2))!==floatval(round($setupprice,2))) {
                            $iprint_price_class = 'warningprice';
                            $iprint_price_title = 'Setup price '.MoneyOutput($setupprice);
                        }
                    }
                }
                $imprints[]=array(
                    'order_imprint_id'=>$irow['order_imprint_id'],
                    'imprint_description' =>$irow['imprint_description'],
                    'imprint_qty'=>$irow['imprint_qty'],
                    'imprint_price' =>$irow['imprint_price'],
                    'imprint_item' =>$irow['imprint_item'],
                    'imprint_subtotal' =>MoneyOutput($subtotal),
                    'imprint_price_class' => $iprint_price_class,
                    'imprint_price_title' => $iprint_price_title,
                    'delflag'=>0,
                );
            }
            $newitem['imprints']=$imprints;
            // Get Imprint Details
            $details=$this->_get_itemorder_impintdetails($row['order_item_id']);
            $impr_details=array();
            $numdet=1;
            foreach ($details as $drow) {
                $impr_details[]=array(
                    'title' =>'Loc '.$numdet,
                    'active' =>$drow['imprint_active'],
                    'order_imprindetail_id' =>$drow['order_imprindetail_id'],
                    'order_item_id' =>$drow['order_item_id'],
                    'imprint_type' =>$drow['imprint_type'],
                    'repeat_note' =>$drow['repeat_note'],
                    'location_id' =>$drow['location_id'],
                    'num_colors' =>$drow['num_colors'],
                    'print_1' =>$drow['print_1'],
                    'print_2' =>$drow['print_2'],
                    'print_3' =>$drow['print_3'],
                    'print_4' =>$drow['print_4'],
                    'setup_1' =>$drow['setup_1'],
                    'setup_2' =>$drow['setup_2'],
                    'setup_3' =>$drow['setup_3'],
                    'setup_4' =>$drow['setup_4'],
                    'extra_cost' =>$drow['extra_cost'],
                    'artwork_art_id'=>$drow['artwork_art_id'],
                );
                $numdet++;
            }
            $newitem['imprint_details']=$impr_details;
            $out[]=$newitem;
        }
        return $out;
    }

    public function _get_item_colorrows($order_item_id) {
        $this->db->select('order_itemcolor_id as item_id, order_item_id, item_description, item_color, item_qty, item_price, printshop_item_id');
        $this->db->from('ts_order_itemcolors');
        $this->db->where('order_item_id', $order_item_id);
        $this->db->order_by('order_itemcolor_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function _get_itemorder_imprints($order_item_id) {
        $this->db->select('*');
        $this->db->from('ts_order_imprints');
        $this->db->where('order_item_id', $order_item_id);
        $this->db->order_by('order_imprint_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function _get_itemorder_impintdetails($order_item_id) {
        $this->db->select('*');
        $this->db->from('ts_order_imprindetails');
        $this->db->where('order_item_id', $order_item_id);
        $this->db->order_by('order_imprindetail_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_order_shipping($order_id) {
        $this->db->select('*');
        $this->db->from('ts_order_shippings');
        $this->db->where('order_id', $order_id);
        $res=$this->db->get()->row_array();
        // Check Data
        if (empty($res)) {
            $shpfld=$this->db->list_fields('ts_order_shippings');
            $shipping=array();
            foreach ($shpfld as $fld) {
                $shipping[$fld]='';
            }
            $shipping['order_shipping_id']=-1;
            $shipping['rush_list']='';
            $shipping['out_rushlist']=array();
            $res=$shipping;
        }
        $res['out_eventdate']=(intval($res['event_date'])==0 ? $this->empty_htmlcontent : date('m/d/y', $res['event_date']));
        $res['out_arrivedate']=(intval($res['arrive_date']==0) ? $this->empty_htmlcontent : date('m/d/y', $res['arrive_date']));
        $res['out_shipdate']=(intval($res['shipdate'])==0 ? $this->empty_htmlcontent : date('m/d/y', $res['shipdate']));
        $res['arriveclass']='';
        if (!empty($res['event_date'])) {
            if (!empty($res['arrive_date'])) {
                $eventdate=$res['event_date']+$this->config->item('event_time');
                if ($res['arrive_date']>$eventdate) {
                    $res['arriveclass']='arrivelate';
                }
            }
        }
        $rush_list=$res['rush_list'];
        $outrush=array();
        if (!empty($rush_list)) {
            $outrush=unserialize($rush_list);
        }
        $res['out_rushlist']=$outrush;
        return $res;
    }
//
    /* Shipping address for old style orders */
    public function get_orderold_shippaddress($order) {
        $order_id=$order['order_id'];
        $this->db->select('*');
        $this->db->from('ts_order_shipaddres');
        $this->db->where('order_id', $order_id);
        $this->db->order_by('order_shipaddr_id');
        $res=$this->db->get()->row_array();
        $address=array();
        if (count($res)==0) {
            $packs=array();
            $packs[]=array(
                'order_shippack_id'=>-1,
                'deliver_service'=>'UPS',
                'track_code'=>'',
                'track_date'=>0,
                'send_date'=>0,
                'senddata'=>0,
                'delflag'=>0,
                'delivered'=>0,
                'delivery_address'=>'',
            );
            $newadr=$this->_create_empty_shipaddress();
            $newadr['order_shipaddr_id']=-1;
            $newadr['item_qty']=$order['order_qty'];
            $newadr['ship_date']=$order['shipdate'];
            $newadr['shipping']=$order['is_shipping'];
            $newadr['sales_tax']=$order['tax'];
            $newadr['shipping_costs']=$order['shipping'];
            $newadr['packages']=$packs;
            $address[]=$newadr;
        } else {
            $this->db->select('*, 0 as delflag, 0 as senddata',FALSE);
            $this->db->from('ts_order_shippacks');
            $this->db->where('order_shipaddr_id', $res['order_shipaddr_id']);
            $packs=$this->db->get()->result_array();
            if (count($packs)==0) {
                $packages=array();
                $packages[]=array(
                    'order_shippack_id'=>-1,
                    'deliver_service'=>'UPS',
                    'track_code'=>'',
                    'track_date'=>0,
                    'send_date'=>0,
                    'senddata'=>0,
                    'delflag'=>0,
                    'delivered' =>0,
                    'delivery_address'=>'',
                );
            } else {
                $pidx=0;
                foreach ($packs as $prow) {
                    // Get Logs
                    $this->db->select('shippack_tracklog_id as log_id, package_num, status, `date`, address');
                    $this->db->from('ts_shippack_tracklogs');
                    $this->db->where('order_shippack_id', $prow['order_shippack_id']);
                    $tracklogs=$this->db->get()->result_array();
                    if (count($tracklogs)>0) {
                        $packs[$pidx]['logs']=$tracklogs;
                    }
                    $pidx++;
                }
                $packages=$packs;
            }
            $address[]=array(
                'order_shipaddr_id' =>$res['order_shipaddr_id'],
                'country_id' =>$res['country_id'],
                'address' =>$res['address'],
                'ship_contact'=>$res['ship_contact'],
                'ship_company'=>$res['ship_company'],
                'ship_address1'=>$res['ship_address1'],
                'ship_address2'=>$res['ship_address2'],
                'city' =>$res['city'],
                'state_id' =>$res['state_id'],
                'zip' =>$res['zip'],
                'item_qty' =>$order['order_qty'],
                'ship_date' =>$order['shipdate'],
                'arrive_date' =>$res['arrive_date'],
                'shipping' =>$res['shipping'],
                'sales_tax' =>$res['sales_tax'],
                'resident' =>$res['resident'],
                'ship_blind' =>$res['ship_blind'],
                'taxview'=>($res['state_id']==$this->tax_state ? 1 : 0),
                'taxcalc'=>($res['state_id']==$this->tax_state ? 1 : 0),
                'tax'=>$res['tax'],
                'tax_exempt'=>$res['tax_exempt'],
                'tax_reason'=>$res['tax_reason'],
                'tax_exemptdoc'=>$res['tax_exemptdoc'],
                'tax_exemptdocsrc'=>$res['tax_exemptdocsrc'],
                'tax_exemptdocid'=>1,
                'shipping_costs'=>$order['shipping'],
                'packages'=>$packages,
            );
        }
        return $address;
    }


    // Shipping address for new style orders
    public function get_order_shippaddress($order_id) {
        $this->db->select('a.*, st.state_code, c.country_iso_code_2');
        $this->db->from('ts_order_shipaddres a');
        $this->db->join('ts_states st','st.state_id=a.state_id','left');
        $this->db->join('ts_countries c','c.country_id=a.country_id','left');
        $this->db->where('order_id', $order_id);
        $this->db->order_by('order_shipaddr_id');
        $res=$this->db->get()->result_array();
        if (empty($res)) {
            $this->load->model('shipping_model');
            $shpadr=$this->_create_empty_shipaddress();
            $cntdat=$this->shipping_model->get_country($shpadr['country_id']);
            $cntlist=$this->shipping_model->get_country_states($shpadr['country_id']);
            $shpadr['order_shipaddr_id']=-1;
            $shpadr['state_id']=$cntlist[0]['state_id'];
            $shpadr['state_code']=$cntlist[0]['state_code'];
            $shpadr['country_iso_code_2']=$cntdat['country_iso_code_2'];
            // $shpadr['']
            $res[]=$shpadr;
        }

        $address=array();
        foreach ($res as $row) {
            $costs=$this->_get_shipaddr_costs($row['order_shipaddr_id']);
            $shipcosts=array();
            $method='';
            foreach ($costs as $crow) {
                $shipcosts[]=array(
                    'order_shipcost_id' =>$crow['order_shipcost_id'],
                    'shipping_method' =>$crow['shipping_method'],
                    'shipping_cost' =>$crow['shipping_cost'],
                    'arrive_date' =>$crow['arrive_date'],
                    'current' =>$crow['current'],
                    'delflag' =>0,
                );
                if ($crow['current']==1) {
                    $method=$crow['shipping_method'];
                }
            }
            // Get Shipping Packs
            $this->db->select('*, 0 as delflag, 0 as senddata',FALSE);
            $this->db->from('ts_order_shippacks');
            $this->db->where('order_shipaddr_id', $row['order_shipaddr_id']);
            $packs=$this->db->get()->result_array();
            if (count($packs)==0) {
                $packages=array();
                $packages[]=array(
                    'order_shippack_id'=>-1,
                    'deliver_service'=>'UPS',
                    'track_code'=>'',
                    'track_date'=>0,
                    'send_date'=>0,
                    'senddata'=>0,
                    'delflag'=>0,
                    'delivered' =>0,
                    'delivery_address'=>'',
                );
            } else {
                $pidx=0;
                foreach ($packs as $prow) {
                    // Get Logs
                    $this->db->select('shippack_tracklog_id as log_id, package_num, status, `date`, address');
                    $this->db->from('ts_shippack_tracklogs');
                    $this->db->where('order_shippack_id', $prow['order_shippack_id']);
                    $tracklogs=$this->db->get()->result_array();
                    if (count($tracklogs)>0) {
                        $packs[$pidx]['logs']=$tracklogs;
                    }
                    $pidx++;
                }
                $packages=$packs;
            }
            $taxcalc=0;
            if ($row['state_id']==$this->tax_state && $row['tax_exempt']==0) {
                $taxcalc=1;
            }
            $address[]=array(
                'order_shipaddr_id' =>$row['order_shipaddr_id'],
                'country_id' =>$row['country_id'],
                'address' =>$row['address'],
                'ship_contact'=>$row['ship_contact'],
                'ship_company'=>$row['ship_company'],
                'ship_address1'=>$row['ship_address1'],
                'ship_address2'=>$row['ship_address2'],
                'city' =>$row['city'],
                'state_id' =>$row['state_id'],
                'zip' =>$row['zip'],
                'item_qty' =>$row['item_qty'],
                'ship_date' =>$row['ship_date'],
                'arrive_date' =>$row['arrive_date'],
                'shipping' =>$row['shipping'],
                'sales_tax' =>$row['sales_tax'],
                'resident' =>$row['resident'],
                'ship_blind' =>$row['ship_blind'],
                'taxview'=>($row['state_id']==$this->tax_state ? 1 : 0),
                'taxcalc'=>$taxcalc,
                'tax'=>$row['tax'],
                'tax_exempt'=>$row['tax_exempt'],
                'tax_reason'=>$row['tax_reason'],
                'tax_exemptdoc'=>$row['tax_exemptdoc'],
                'tax_exemptdocsrc'=>$row['tax_exemptdocsrc'],
                'tax_exemptdocid'=>1,
                'shipping_costs'=>$shipcosts,
                'out_shipping_method'=>$method,
                'out_zip'=>$row['zip'].' '.$row['state_code'],
                'out_country'=>$row['country_iso_code_2'],
                'packages'=>$packages,
            );
        }

        return $address;
    }

    public function _get_shipaddr_costs($order_shipaddr_id) {
        $this->db->select('*');
        $this->db->from('ts_order_shipcosts');
        $this->db->where('order_shipaddr_id', $order_shipaddr_id);
        $this->db->order_by('order_shipcost_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    // Get status of Shipping
    public function _get_order_shipstatus($shipping_address) {
        if (count($shipping_address)==0) {
            return 'Not Shipped Yet';
        } else {
            $status='Shipped';
            $emptypack=0;
            $sendtrack=0;
            foreach ($shipping_address as $srow) {
                if (empty($srow['packages'])) {
                    $emptypack=1;
                    break;
                } else {
                    $packages=$srow['packages'];
                    foreach ($packages as $prow) {
                        if ($prow['delflag']==0 && $prow['send_date']!=0) {
                            $sendtrack=1;
                        }
                    }
                }
            }
            if ($emptypack==1) {
                $status='Partially Shipped';
            } elseif ($sendtrack==1) {
                $status='Tracking Sent';
            }
        }
        return $status;
    }


    // Get Bill Infor
    public function get_order_billing($order_id) {
        $this->db->select('*');
        $this->db->from('ts_order_billings');
        $this->db->where('order_id', $order_id);
        $res=$this->db->get()->row_array();
        if (empty($res)) {
            $bil=$this->_create_empty_billddress();
            $res=$bil;
        }
        return $res;
    }

    // Get Charges Info
    public function get_order_charges($order_id) {
        $this->db->select('*');
        $this->db->from('ts_order_payments');
        $this->db->where('order_id', $order_id);
        $res=$this->db->get()->result_array();
        $out=array();
        if (count($res)==0) {
            $out[]=array(
                'order_payment_id' =>'-1',
                'order_id'=>$order_id,
                'amount'=>0,
                'cardnum' =>'',
                'exp_month' =>'',
                'exp_year' =>'',
                'cardcode'=>'',
                'exp_date'=>'',
                'out_amount'=>'',
                'autopay' =>1,
                'delflag'=>0,
            );
        } else {
            foreach ($res as $row) {
                $row['exp_date']=str_pad($row['exp_month'], 2, '0', STR_PAD_LEFT).'/'.str_pad($row['exp_year'], 2,'0', STR_PAD_LEFT);
                $row['out_amount']=($row['amount']==0 ? '' : MoneyOutput($row['amount']));
                $row['delflag']=0;
                $out[]=$row;
            }
        }
        return $out;
    }

    // Prepare Payment Info
    private function _prepare_order_payment($order_id, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->load->model('batches_model');
        $this->load->model('orders_model');
        $order_data=$this->orders_model->get_order_detail($order_id);
        if (!isset($order_data['order_id'])) {
            $out['msg']='Order Not Found';
            return $out;
        }

        // Get Info about Payments
        $this->db->select('*');
        $this->db->from('ts_order_payments');
        $this->db->where('order_id', $order_id);
        $this->db->where('amount > 0');
        $res=$this->db->get()->result_array();
        if (count($res)==0) {
            $out['result']=$this->success_result;
            return $out;
        }
        $this->db->select('b.customer_name, b.company, b.address_1, b.address_2, b.city, b.state_id');
        $this->db->select('b.zip, b.country_id, c.country_name, c.country_iso_code_2');
        $this->db->from('ts_order_billings b');
        $this->db->join('ts_countries c','c.country_id=b.country_id');
        $this->db->where('b.order_id', $order_id);
        $bilres=$this->db->get()->row_array();
        $bilchk=0;
        if (empty($bilres['customer_name'])) {
            $out['msg']='Enter Customer Name';
        } elseif (empty($bilres['address_1'])) {
            $out['msg']='Enter Billing Address';
        } elseif (empty($bilres['city'])) {
            $out['msg']='Enter Billing City';
        } elseif (empty($bilres['zip'])) {
            $out['msg']='Enter Billing Zip / Postal Code';
        } else {
            $bilchk=1;
        }
        if ($bilchk==0) {
            $this->_save_order_paymentlog($order_id, $user_id, $out['msg']);
            return $out;
        }
        $custdat=explode(' ', $bilres['customer_name']);
        $customer_first_name=(isset($custdat[0]) ? $custdat[0] : 'Unknown');
        $customer_last_name=(isset($custdat[1]) ? $custdat[1] : 'Unknown');
        // Get contact data
        $this->db->select('contact_phone, contact_emal');
        $this->db->from('ts_order_contacts');
        $this->db->where('order_id', $order_id);
        $this->db->where('contact_inv',1);
        $contres=$this->db->get()->result_array();
        $payemail=$payphone='';
        foreach ($contres as $crow) {
            if (!empty($crow['contact_phone'])) {
                $payphone=$crow['contact_phone'];
            }
            if (!empty($crow['contact_emal'])) {
                $payemail=$crow['contact_emal'];
            }
        }
        $state='UNK';
        if (!empty($bilres['state_id'])) {
            $this->db->select('state_code');
            $this->db->from('ts_states');
            $this->db->where('state_id', $bilres['state_id']);
            $stat=$this->db->get()->row_array();
            $state=$stat['state_code'];
        }

        // Try to pay
        $pay_options=array(
            'email'=>$payemail,
            'company'=>$bilres['company'],
            'firstname'=>$customer_first_name,
            'lastname'=>$customer_last_name,
            'address1'=>$bilres['address_1'],
            'address2'=>$bilres['address_2'],
            'city'=>$bilres['city'],
            'state'=>$state,
            'country'=>$bilres['country_iso_code_2'],
            'zip'=>$bilres['zip'],
            'phone'=>$payphone,
            'amount'=>0,
            'cardnum'=>'',
            'cardcode'=>'',
            'exp_month'=>'',
            'exp_year'=>'',
        );

        foreach ($res as $row) {
            $chkpay=0;
            if (empty($row['cardnum'])) {
                $out['msg']='Enter Card #';
            } elseif (empty($row['cardcode'])) {
                $out['msg']='Enter Card CVV2 code';
            } elseif (intval($row['exp_month'])==0) {
                $out['msg']='Expire Month Incorrect';
            } elseif (intval($row['exp_year'])==0) {
                $out['msg']='Expire Year Incorrect';
            } else {
                $cardtype=$this->getCCardType(str_replace('-','',$row['cardnum']));
                if (empty($cardtype)) {
                    $out['msg']='Unknown Credit Card Type';
                } else {
                    $chkpay=1;
                }
            }
            if ($chkpay==1) {
                switch ($cardtype) {
                    case 'American Express':
                        $pay_options['cardtype']='Amex';
                        break;
                    default :
                        $pay_options['cardtype']=$cardtype;
                        break;
                }

                $pay_options['amount']=$row['amount'];
                $pay_options['cardnum']=  str_replace('-', '',$row['cardnum']);
                $pay_options['cardcode']=$row['cardcode'];
                $pay_options['exp_month']=$row['exp_month'];
                $pay_options['exp_year']=$row['exp_year'];
                $transres=$this->order_payment($pay_options);
                if ($transres['result']==$this->error_result) {
                    $out['msg']=$transres['error_msg'];
                    $this->_save_order_paymentlog($order_id, $user_id, $out['msg'], $pay_options);
                    return $out;
                } else {
                    // Make Current row Amount=0, Add Charge
                    $this->db->set('amount',0);
                    $this->db->where('order_payment_id', $row['order_payment_id']);
                    $this->db->update('ts_order_payments');
                    // Batch data
                    $paymethod='';
                    if ($pay_options['cardtype']=='amex') {
                        $paymethod='a';
                    } else {
                        $paymethod='v';
                    }
                    $batch_data=array(
                        'batch_id'=>0,
                        'batch_date'=>time(),
                        'paymethod'=>$paymethod,
                        'amount'=>$row['amount'],
                        'batch_note'=>NULL,
                        'order_id'=>$order_id,
                        'batch_received'=>0,
                        'batch_type'=>$pay_options['cardtype'],
                        'batch_num'=>$pay_options['cardnum'],
                        'batch_transaction'=>$transres['transaction_id'],
                    );
                    $batch_id=$this->batches_model->save_batch($batch_data, $order_data, $user_id);
                    $this->_save_order_paymentlog($order_id, $user_id, $transres['transaction_id'], $pay_options, 1);
                }
            } else {
                $this->_save_order_paymentlog($order_id, $user_id, $out['msg']);
                return $out;
            }
        }
        $out['result']=$this->success_result;
        return $out;
    }

    // Update History Msg
    public function histore_msgupdate($leadorder, $newmsg, $usr_id, $usr_name, $dbupdate, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if ($dbupdate==0) {
            $history=$leadorder['message']['history'];
            $idx=count($history);
            $newhrec=array();
            $newhrec[]=array(
                'artwork_history_id'=>(-1)*$idx,
                'created_time'=>time(),
                'message'=>$newmsg,
                'user_name'=>$usr_name,
                'parsed_mailbody'=>'',
                'message_details'=>'',
                'history_head'=>$usr_name.','.date('m/d/y h:i:s a', time()),
                'out_date'=>date('D - M j, Y'),
                'out_subdate'=>date('h:i:s a').' - '.$usr_name,
                'parsed_lnk'=>'',
                'parsed_class'=>'',
                'title'=>'',
            );
            $newhistory=array_merge($newhrec, $history);
        } else {
            // Update DB
            $this->load->model('artwork_model');
            $artwork=$leadorder['artwork'];
            // Update History
            $artwh=array(
                'artwork_id'=>$artwork['artwork_id'],
                'user_id'=>$usr_id,
                'update_msg'=>$newmsg,
            );
            $res=$this->artwork_model->artwork_history_update($artwh);
            // Get New History
            $newhistory=$this->artwork_model->get_artmsg_history($artwork['artwork_id']);
        }
        $leadorder['message']['history']=$newhistory;
        $leadorder['message']['update']='';
        usersession($ordersession, $leadorder);
        $out['result']=$this->success_result;
        return $out;
    }

    // Try to pay
    public function order_payment($options) {
        $cntcode = $options['country'];
        if (isset($options['cardnum'])) {
            $options['cardnum']=str_replace('-', '',$options['cardnum']);
        }
        if ($this->config->item('default_paysystem')=='paypal') {
            $realconfig=1;
            $servername=str_replace('www.','',$_SERVER['SERVER_NAME']);
            if (empty($servername) || in_array($servername, $this->config->item('localserver'))) {
                $realconfig=0;
            }
            // Load PayPal library
            if ($realconfig==0) {
                $this->config->load('paypal_test');
                $config = array(
                    'Sandbox' => TRUE, 			// Sandbox / testing mode option.
                    'APIUsername' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
                    'APIPassword' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
                    'APISignature' => $this->config->item('APISignature'), 	// PayPal API signature of the API caller
                    'APISubject' => '', 						// PayPal API subject (email address of 3rd party user that has granted API permission for your app)
                    'APIVersion' => $this->config->item('APIVersion')		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
                );
            } else {
                $this->config->load('paypal_live');
                $config = array(
                    'APIUsername' => $this->config->item('APIUsername'), 	// PayPal API username of the API caller
                    'APIPassword' => $this->config->item('APIPassword'), 	// PayPal API password of the API caller
                    'APISignature' => $this->config->item('APISignature'), 	// PayPal API signature of the API caller
                    'APISubject' => '', 									// PayPal API subject (email address of 3rd party user that has granted API permission for your app)
                    'APIVersion' => $this->config->item('APIVersion'),		// API version you'd like to use for your call.  You can set a default version in the class and leave this blank if you want.
                    'Sandbox' => $this->config->item('Sandbox'), 			// Sandbox / testing mode option.
                );
            }
            // Show Errors
            $this->load->library('paypal/Paypal_pro', $config);
            // Prepare Objects
            $DPFields = array(
                'paymentaction' => 'Sale', // How you want to obtain payment.  Authorization indidicates the payment is a basic auth subject to settlement with Auth & Capture.  Sale indicates that this is a final sale for which you are requesting payment.  Default is Sale.
                'ipaddress' => $this->input->server('REMOTE_ADDR'), // $_SERVER['REMOTE_ADDR'], // Required.  IP address of the payer's browser.
                'returnfmfdetails' => '1'      // Flag to determine whether you want the results returned by FMF.  1 or 0.  Default is 0.
            );
            $CCDetails = array(
                'creditcardtype' => $options['cardtype'],  // ($data['cctype']=='American Express' ? 'Amex' : $data['cctype']), // Required. Type of credit card.  Visa, MasterCard, Discover, Amex, Maestro, Solo.  If Maestro or Solo, the currency code must be GBP.  In addition, either start date or issue number must be specified.
                'acct' => $options['cardnum'], // Required.  Credit card number.  No spaces or punctuation.
                'expdate' => $options['exp_month'].'20'.$options['exp_year'], // Required.  Credit card expiration date.  Format is MMYYYY
                'cvv2' => $options['cardcode'], // Requirements determined by your PayPal account settings.  Security digits for credit card.
                'startdate' => '', // Month and year that Maestro or Solo card was issued.  MMYYYY
                'issuenumber' => ''       // Issue number of Maestro or Solo card.  Two numeric digits max.
            );

            $PayerInfo = array(
                'email' => $options['email'], // Email address of payer.
                'payerid' => '', // Unique PayPal customer ID for payer.
                'payerstatus' => '', // Status of payer.  Values are verified or unverified
                'business' => ($options['company']=='' ? '' : $options['company'])        // Payer's business name.
            );

            $PayerName = array(
                'salutation' => '', // Payer's salutation.  20 char max.
                'firstname' => $options['firstname'], // Payer's first name.  25 char max.
                'middlename' => '', // Payer's middle name.  25 char max.
                'lastname' => $options['lastname'], // Payer's last name.  25 char max.
                'suffix' => ''        // Payer's suffix.  12 char max.
            );

            $BillingAddress = array(
                'street' => $options['address1'], // Required.  First street address.
                'street2' => ($options['address2']=='' ? '' : $options['address2']), // Second street address.
                'city' => $options['city'], // Required.  Name of City.
                'state' => $options['state'], // Required. Name of State or Province.
                'countrycode' => $cntcode,  // Required.  Country code.
                'zip' => $options['zip'], // Required.  Postal code of payer.
                'phonenum' => $options['phone']       // Phone Number of payer.  20 char max.
            );

            $ShippingAddress = array(
                'shiptoname' => substr($options['firstname'].' '.$options['lastname'],0,32), // Required if shipping is included.  Person's name associated with this address.  32 char max.
                'shiptostreet' => $options['address1'], // Required if shipping is included.  First street address.  100 char max.
                'shiptostreet2' => $options['address2'], // Second street address.  100 char max.
                'shiptocity' => $options['city'], // Required if shipping is included.  Name of city.  40 char max.
                'shiptostate' => $options['state'], // Required if shipping is included.  Name of state or province.  40 char max.
                'shiptozip' => $options['zip'], // Required if shipping is included.  Postal code of shipping address.  20 char max.
                'shiptocountry' => $cntcode, // Required if shipping is included.  Country code of shipping address.  2 char max.
                'shiptophonenum' => $options['phone']     // Phone number for shipping address.  20 char max.
            );

            $PaymentDetails = array(
                'amt' => $options['amount'], // Required.  Total amount of order, including shipping, handling, and tax.
                'currencycode' => 'USD', // Required.  Three-letter currency code.  Default is USD.
                'itemamt' => $options['amount'], // Required if you include itemized cart details. (L_AMTn, etc.)  Subtotal of items not including S&H, or tax.
                'shippingamt' => '', // Total shipping costs for the order.  If you specify shippingamt, you must also specify itemamt.
                'shipdiscamt' => '', // Shipping discount for the order, specified as a negative number.
                'handlingamt' => '', // Total handling costs for the order.  If you specify handlingamt, you must also specify itemamt.
                'taxamt' => '', // Required if you specify itemized cart tax details. Sum of tax for all items on the order.  Total sales tax.
                'desc' => 'Web Order', // Description of the order the customer is purchasing.  127 char max.
                'custom' => '', // Free-form field for your own use.  256 char max.
                'invnum' => '', // Your own invoice or tracking number
                'notifyurl' => ''      // URL for receiving Instant Payment Notifications.  This overrides what your profile is set to use.
            );

            $OrderItems = array();
            $Item = array(
                'l_name' => 'Stressball', // Item Name.  127 char max.
                'l_desc' => 'The best stressball on the planet!', // Item description.  127 char max.
                'l_amt' => $options['amount'], // Cost of individual item.
                'l_number' => '1', // Item Number.  127 char max.
                'l_qty' => '1', // Item quantity.  Must be any positive integer.
                'l_taxamt' => '', // Item's sales tax amount.
                'l_ebayitemnumber' => '', // eBay auction number of item.
                'l_ebayitemauctiontxnid' => '', // eBay transaction ID of purchased item.
                'l_ebayitemorderid' => ''     // eBay order ID for the item.
            );
            array_push($OrderItems, $Item);

            $Secure3D = array(
                'authstatus3d' => '',
                'mpivendor3ds' => '',
                'cavv' => '',
                'eci3ds' => '',
                'xid' => ''
            );

            $PayPalRequestData = array(
                'DPFields' => $DPFields,
                'CCDetails' => $CCDetails,
                'PayerInfo' => $PayerInfo,
                'PayerName' => $PayerName,
                'BillingAddress' => $BillingAddress,
                'ShippingAddress' => $ShippingAddress,
                'PaymentDetails' => $PaymentDetails,
                'OrderItems' => $OrderItems,
                'Secure3D' => $Secure3D
            );
            foreach ($Item as $key => $val) {
                log_message('ERROR','Item Param '.$key.' Val '.$val.' !');
            }
            foreach ($PaymentDetails as $key=>$val) {
                log_message('ERROR','Payment Param '.$key.' Val '.$val.' !');
            }
            $PayPalResult = $this->paypal_pro->DoDirectPayment($PayPalRequestData);

            if (!$this->paypal_pro->APICallSuccessful($PayPalResult['ACK'])) {
                $errors = $PayPalResult['ERRORS'];
                return array('result' => $this->error_result, 'error_msg' => $errors[0]['L_LONGMESSAGE']);
            } else {
                // Successful call.  Load view or whatever you need to do here.
                return array('result' => $this->success_result , 'transaction_id' => $PayPalResult['TRANSACTIONID']);
            }
        } else {
//            $this->load->library('authorizenet');
//            if ($data['firstname']=='test' && $data['lastname']=='test' && $data['emailaddr']=='test@bluetrack.com') {
//                $login_id = $this->config->item('authorizenet_api_login_id_test');
//                $trans_key = $this->config->item('authorizenet_transaction_key_test');
//                define("AUTHORIZENET_SANDBOX", $this->config->item('authorizenet_sandbox_test'));
//            } else {
//                $login_id = $this->config->item('authorizenet_api_login_id');
//                $trans_key = $this->config->item('authorizenet_transaction_key');
//                define("AUTHORIZENET_SANDBOX", $this->config->item('authorizenet_sandbox'));
//            }
//            /* Special Field STATE */
//            if (!isset($data['state_id'])) {
//                $state = '';
//            } else {
//                $state = $data['state_id'];
//            }
//
//            $res = $this->mship->get_country($data['country_id']);
//            $country = $res['country'];
//
//            $cntcode = $country['country_iso_code_3'];
//            $res = $this->mship->get_country($data['ship_countryid']);
//            $country = $res['country'];
//
//            $ship_cntcode = $country['country_iso_code_3'];
//
//            $sale = new Authorizenet($login_id, $trans_key);
//            $sale->setFields(
//                array(
//                    'amount' => number_format($cart['total'], 2, '.', ''),
//                    'card_num' => $data['ccnumber'],
//                    'exp_date' => $data['ccexpmonth'] . '/' . $data['ccexpyear'],
//                    'first_name' => $data['firstname'],
//                    'last_name' => $data['lastname'],
//                    'address' => $data['address1'],
//                    'city' => $data['cityname'],
//                    'state' => $state,
//                    'country' => $cntcode,
//                    'zip' => $data['zipcode'],
//                    /*'email' => $data['emailaddr'],*/
//                    'card_code' => $data['ccverification'],
//                    /* Ship info */
//                    'ship_to_first_name'   => $data['ship_firstname'],
//                    'ship_to_last_name'    => $data['ship_lastname'],
//                    'ship_to_address'      => $data['ship_street1'],
//                    'ship_to_city'         => $data['ship_cityname'],
//                    'ship_to_zip'     => $data['ship_zipcode'],
//                    'ship_to_country'      => $ship_cntcode,
//                    'tax'                  => $cart['tax'],
//                )
//            );
//            $response = $sale->authorizeAndCapture();
//            if ($response->approved) {
//                return array('error' => FALSE, 'transaction_id' => $response->transaction_id);
//            } else {
//                return array('error' => TRUE, 'error_msg' => $response->response_reason_text);
//            }
        }
    }


    public function getCCardType($CCNumber) {
        $type = '';
        foreach ($this->creditcardTypes as $card) {
            if (!in_array(strlen($CCNumber), $card['cardLength'])) {
                continue;
            }
            $prefixes = '/^(' . implode('|', $card['cardPrefix']) . ')/';
            if (preg_match($prefixes, $CCNumber) == 1) {
                $type = $card['Name'];
                break;
            }
        }
        return $type;
    }

    public function dublicate_order($leadorder, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);

        $order_type=$leadorder['order_system'];
        $new_data=$this->_dublicate_new_order($leadorder, $order_type, $user_id);
        // Recalt totals for new duplicated order
        $out['result']=$this->success_result;
        $out['order']=$new_data['order'];
        $out['contacts']=$new_data['contacts'];
        $out['order_system_type']='new';
        $out['numtickets']=0;
        $out['total_due']=$new_data['order']['revenue'];
        $out['payment_total']=0;
        $out['artwork']=$new_data['artwork'];

        $out['payments']=array();
        $out['order_items']=$new_data['order_items'];
        $out['shipping']=$new_data['shipping'];
        $out['shipping_address']=$new_data['shipping_address'];
        $out['order_billing']=$new_data['order_billing'];
        $out['countries']=$new_data['countries'];
        $out['message']=$new_data['message'];
        $out['charges']=$new_data['charges'];
        $out['artlocations']=$new_data['artlocations'];
        $out['proofdocs']=array();
        return $out;
    }

    private function _dublicate_new_order($leadorder, $order_type, $user_id) {
        $this->load->model('shipping_model');
        $this->load->model('user_model');
        $this->load->model('artlead_model');
        $cnt_options=array(
            'orderby'=>'sort, country_name',
        );
        $countries=$this->shipping_model->get_countries_list($cnt_options);
        $defcountry=$countries[0]['country_id'];
        // Get List of states
        $states=$this->shipping_model->get_country_states($defcountry);
        $defstate=NULL;
        if (count($states)>0) {
            $defstate=$states[0]['state_id'];
        }
        $order=$leadorder['order'];
        $neworder=array();
        foreach ($order as $key=>$val) {
            if ($key=='order_id') {
                $neworder[$key]=0;
            } elseif ($key=='order_num') {
                $neworder[$key]='';
            } elseif ($key=='order_confirmation') {
                $neworder[$key]='';
            } elseif ($key=='profit_class') {
                $neworder[$key]=$this->project_class;
            } elseif ($key=='order_usr_repic') {
                $neworder[$key]=$user_id;
            } elseif ($key=='order_date') {
                $neworder[$key]=time();
            } elseif ($key=='appaproved') {
                $neworder[$key]=0;
            } elseif ($key=='credit_app_id') {
                $neworder[$key]=0;
            } elseif ($key=='credit_appdue') {
                $neworder['credit_appdue']=strtotime(date("Y-m-d", time()) . " +30 days");
            } elseif ($key=='newappcreditlink') {
                $neworder['newappcreditlink']=0;
            } elseif ($key=='credit_applink') {
                $neworder[$key]='';
            } elseif ($key=='order_cog') {
                $neworder[$key]=NULL;
            } elseif ($key=='profit_perc') {
                $neworder[$key]=NULL;
            } elseif ($key=='profit') {
                $neworder[$key]=round($val*($this->config->item('default_profit')/100),2);
            } elseif ($key=='payment_total') {
                $neworder[$key]=0;
            } else {
                $neworder[$key]=$val;
            }
        }
        if ($order_type=='old') {
            $neworder['item_cost']=$neworder['item_imprint']=0;
        }

        // Contacts
        $contacts=array();
        $artwork=$leadorder['artwork'];
        if ($order_type=='old') {
            for ($i=1; $i<4; $i++) {
                $contacts[]=array(
                    'order_contact_id'=>(-1)*($i),
                    'order_id'=>0,
                    'contact_name' =>($i==1 ? $artwork['customer_contact'] : '' ),
                    'contact_phone' =>($i==1 ? $artwork['customer_phone'] : ''),
                    'contact_emal' =>($i==1 ? $artwork['customer_email'] : ''),
                    'contact_art' =>($i==1 ? 1 : 0),
                    'contact_inv' =>($i==1 ? 1 : 0),
                    'contact_trk' =>($i==1 ? 1 : 0),
                );
            }
        } else {
            $i=1;
            foreach ($leadorder['contacts'] as $crow) {
                $contacts[]=array(
                    'order_contact_id'=>(-1)*($i),
                    'order_id'=>0,
                    'contact_name' =>$crow['contact_name'],
                    'contact_phone' =>$crow['contact_phone'],
                    'contact_emal' =>$crow['contact_emal'],
                    'contact_art' =>$crow['contact_art'],
                    'contact_inv' =>$crow['contact_inv'],
                    'contact_trk' =>$crow['contact_trk'],
                );
                $i++;
            }
        }
        // Artwork
        $newart_history=array();
        $usrdata=$this->user_model->get_user_data($user_id);
        if (!empty($usrdata['user_leadname'])) {
            $addusr=$usrdata['user_leadname'];
        } else {
            $addusr=$usrdata['user_name'];
        }
        $msg='Order was created '.date('m/d/y h:i:s a', time()).' by duplicate order # '.$order['order_num'].' by '.$usrdata['user_name'];
        // Add Record about duplicate
        $newart_history[]=array(
            'artwork_history_id'=>(-1),
            'created_time' =>time(),
            'message' =>$msg,
            'user_name' =>$addusr,
            'user_leadname' =>$addusr,
            'parsed_mailbody' =>'',
            'message_details' =>$msg,
            'history_head'=>$addusr.','.date('m/d/y h:i:s a', time()),
            'out_date'=>date('D - M j, Y', time()),
            'out_subdate'=>date('h:i:s a').' - '.$addusr,
            'parsed_lnk'=>'',
            'parsed_class'=>'',
            'title'=>'',
        );
        $newartw=array(
            'artwork_id'=>-1,
            'order_id' =>0,
            'last_message' =>'',
            'customer_instruct' =>$artwork['customer_instruct'],
            'customer' => $artwork['customer'],
            'customer_contact' =>$artwork['customer_contact'],
            'customer_phone' => $artwork['customer_phone'],
            'customer_email' => $artwork['customer_phone'],
            'item_name' =>$artwork['item_name'],
            'other_item' =>$artwork['other_item'],
            'item_number' =>$artwork['item_number'],
            'item_id' =>$artwork['item_id'],
            'item_color' =>$artwork['item_color'],
            'item_qty' =>$artwork['item_qty'],
            'artwork_rush' =>$artwork['artwork_rush'],
            'artwork_note' =>$artwork['artwork_note'],
            'customer_art' =>$artwork['customer_art'],
            'customer_inv' =>$artwork['customer_inv'],
            'customer_track' =>$artwork['customer_track'],
            'proof_num' =>'',
            'order_num' =>$neworder['order_num'],
            'artwork_blank' =>$artwork['artwork_blank'],
            'art_history' =>$newart_history,
            'artstage' =>$this->NO_ART,
            'artstage_txt'=>$this->NO_ART_TXT,
            'artstage_time'=>'0h',
        );
        $locations=$this->artlead_model->get_art_locations($artwork['artwork_id']);

        $artlocations=array();
        $locidx=1;
        foreach ($locations as $lrow) {
            $artlocations[]=array(
                'artwork_art_id'=>(-1)*$locidx,
                'artwork_id'=>'',
                'art_type'=>'Repeat',
                'art_ordnum'=>$locidx,
                'logo_src'=>'',
                'redraw_time'=>0,
                'logo_vectorized'=>'',
                'vectorized_time'=>0,
                'redrawvect'=>0,
                'rush'=>$lrow['rush'],
                'customer_text'=>$lrow['customer_text'],
                'font'=>$lrow['font'],
                'redraw_message'=>'',
                'redo'=>0,
                'art_numcolors'=>$lrow['art_numcolors'],
                'art_color1'=>$lrow['art_color1'],
                'art_color2'=>$lrow['art_color2'],
                'art_color3'=>$lrow['art_color3'],
                'art_color4'=>$lrow['art_color4'],
                'art_location'=>$lrow['art_location'],
                'repeat_text'=>$order['order_num'],
                'sys_redrawn'=>0,
                'order_id'=>-1,
                'mail_id'=>NULL,
                'order_num' =>'',
                'proof_num' =>'',
                'locat_ready'=>1,
                'artlabel' =>'Repeat',
                'deleted' =>'',
                'redochk' =>'&nbsp;',
                'rushchk' =>'<input type="checkbox" title="Rush" class="artlocationinpt artrush" data-artloc="'.(-1)*$locidx.'" />',
                'redrawchk'=>'<input type="checkbox" title="Redraw" class="artlocationinpt artredraw" data-artloc="'.(-1)*$locidx.'" />',
            );
            $locidx++;
        }
        // Order Items
        $neworder_items=array();
        if ($order_type=='new') {
            $order_items=$leadorder['order_items'];
            $idx=1;
            foreach ($order_items as $irow) {
                $items=$irow['items'];
                $itmid=1;
                $newitems=array();
                foreach ($items as $pitem) {
                    $coloroptions=array(
                        'order_item_id'=>(-1)*$idx,
                        'item_id'=>(-1)*($itmid),
                        'colors'=>$pitem['colors'],
                        'item_color'=>$pitem['item_color'],
                    );
                    $out_colors=$this->load->view('leadorderdetails/item_color_choice', $coloroptions, TRUE);
                    $newitems[]=array(
                        'order_item_id'=>(-1)*$idx,
                        'item_id' =>(-1)*($itmid),
                        'item_row' =>$pitem['item_row'],
                        'item_number' =>$pitem['item_number'],
                        'item_color' =>$pitem['item_color'],
                        'colors'=>$pitem['colors'],
                        'num_colors' =>$pitem['num_colors'],
                        'item_description' =>$pitem['item_description'],
                        'out_colors'=>$out_colors,
                        'item_color_add' =>$pitem['item_color_add'],
                        'item_qty' =>$pitem['item_qty'],
                        'item_price' =>$pitem['item_price'],
                        'item_subtotal' =>$pitem['item_subtotal'],
                        'qtyinput_class' => $pitem['qtyinput_class'],
                        'qtyinput_title' => $pitem['qtyinput_title'],
                        'printshop_item_id'=>$pitem['printshop_item_id'],
                    );
                    $itmid++;
                }
                $imprints=$irow['imprints'];
                $newimpr=array();
                $itmid=1;
                foreach ($imprints as $imprrow) {
                    $newimpr[]=array(
                        'order_imprint_id' =>(-1)*$itmid,
                        'imprint_description' =>$imprrow['imprint_description'],
                        'imprint_qty' =>$imprrow['imprint_qty'],
                        'imprint_price' =>($imprrow['imprint_item']==0 ? 0 : $imprrow['imprint_price']),
                        'imprint_item' =>$imprrow['imprint_item'],
                        'imprint_subtotal' =>($imprrow['imprint_item']==0 ? 0 : $imprrow['imprint_subtotal']),
                        'imprint_price_class' => $imprrow['imprint_price_class'],
                        'imprint_price_title' => $imprrow['imprint_price_title'],
                        'delflag' =>0,
                    );
                    $itmid++;
                }
                $details=$irow['imprint_details'];
                $newdetais=array();
                $repnote=$leadorder['order']['order_num'];
                $didx=1;
                foreach ($details as $drow) {
                    $newdetais[]=array(
                        'order_imprindetail_id' =>(-1)*$didx,
                        'order_item_id'=>(-1)*$idx,
                        'active'=>$drow['active'],
                        'title'=>'Loc #'.$didx,
                        'imprint_type' =>($drow['active']==1 ? 'REPEAT' : $drow['imprint_type']),
                        'repeat_note' =>($drow['active']==1 ? $repnote : $drow['repeat_note']),
                        'location_id' =>$drow['location_id'],
                        'num_colors' =>$drow['num_colors'],
                        'print_1' =>$drow['print_1'],
                        'print_2' =>$drow['print_2'],
                        'print_3' =>$drow['print_3'],
                        'print_4' =>$drow['print_4'],
                        'setup_1' =>($drow['active']==1 ? 0 : $drow['setup_1']),
                        'setup_2' =>$drow['setup_2'],
                        'setup_3' =>$drow['setup_3'],
                        'setup_4' =>$drow['setup_4'],
                        'extra_cost' => $drow['extra_cost'],
                        'artwork_art_id'=>NULL,
                    );
                    $didx++;
                }
                $neworder_items[]=array(
                    'order_item_id'=>(-1)*$idx,
                    'item_id' =>$irow['item_id'],
                    'item_number' =>$irow['item_number'],
                    'item_name' =>$irow['item_name'],
                    'item_qty' =>$irow['item_qty'],
                    'colors' =>$irow['colors'],
                    'num_colors' =>$irow['num_colors'],
                    'item_template' =>$irow['item_template'],
                    'item_weigth' =>$irow['item_weigth'],
                    'cartoon_qty'=>$irow['cartoon_qty'],
                    'cartoon_width' =>$irow['cartoon_width'],
                    'cartoon_heigh' =>$irow['cartoon_heigh'],
                    'cartoon_depth' =>$irow['cartoon_depth'],
                    'boxqty' =>$irow['boxqty'],
                    'setup_price' => $irow['setup_price'],
                    'print_price' =>$irow['print_price'],
                    'base_price' => $irow['base_price'],
                    'item_subtotal' =>$irow['item_subtotal'],
                    'imprint_subtotal' =>$irow['imprint_subtotal'],
                    'vendor_zipcode' =>$irow['vendor_zipcode'],
                    'charge_perorder' =>$irow['charge_perorder'],
                    'charge_peritem' =>$irow['charge_peritem'],
                    'charge_pereach' =>$irow['charge_pereach'],
                    'items'=>$newitems,
                    'imprints'=>$newimpr,
                    'imprint_details'=>$newdetais,
                    'imprint_locations'=>$irow['imprint_locations'],
                    // 'qtyinput_class' => $irow['qtyinput_class'],
                );
            }
        } else {
            $itemsubtotal=0;
            $item_id=$order['item_id'];
            if ($item_id<0) {
                $this->load->model('orders_model');
                $itemdata=$this->orders_model->get_newitemdat($item_id);
            } else {
                $itemdata=$this->_get_itemdata($item_id);
            }
            $colors=$itemdata['colors'];
            $itmcolor='';
            if ($itemdata['num_colors']>0) {
                $itmcolor=$colors[0];
            }
            $newid=1;
            if ($item_id<0) {
                $item_description=$order['order_items'];
            } else {
                $item_description=$itemdata['item_name'];
            }

            // Prepare Parts of Order Items
            $orditem=array(
                'order_item_id'=>$newid*(-1),
                'item_id'=>$item_id,
                'item_number'=>$itemdata['item_number'],
                'item_name'=>$item_description,
                'item_qty'=>$order['order_qty'],
                'colors'=>$itemdata['colors'],
                'num_colors'=>$itemdata['num_colors'],
                'item_template'=>$this->normal_template,
                'item_weigth'=>0,
                'cartoon_qty'=>0,
                'cartoon_width'=>0,
                'cartoon_heigh'=>0,
                'cartoon_depth'=>0,
                'boxqty'=>'',
                'setup_price'=>0,
                'print_price'=>0,
                'imprint_locations'=>array(),
                'item_subtotal'=>0,
                'imprint_subtotal'=>0,
                'vendor_zipcode'=>$this->default_zip,
                'charge_perorder'=>0,
                'charge_peritem'=>0,
            );
            if ($item_id>0) {
                $setupprice=$this->_get_item_priceimprint($item_id, 'setup');
                $printprice=$this->_get_item_priceimprint($item_id, 'imprint');
                $orditem['item_template']=$itemdata['item_template'];
                $orditem['item_weigth']=$itemdata['item_weigth'];
                $orditem['cartoon_qty']=$itemdata['cartoon_qty'];
                $orditem['cartoon_width']=$itemdata['cartoon_width'];
                $orditem['cartoon_heigh']=$itemdata['cartoon_heigh'];
                $orditem['cartoon_depth']=$itemdata['cartoon_depth'];
                $orditem['boxqty']=$itemdata['boxqty'];
                $orditem['setup_price']=$setupprice;
                $orditem['print_price']=$printprice;
                $orditem['imprint_locations']=$itemdata['imprints'];
                $orditem['vendor_zipcode']=$itemdata['vendor_zipcode'];
                $orditem['charge_perorder']=$itemdata['charge_perorder'];
                $orditem['charge_pereach']=$itemdata['charge_pereach'];
            }
            $newprice=$this->_get_item_priceqty($orditem['item_id'], $orditem['item_template'] , $orditem['item_qty']);

            $neworder['item_cost']=$orditem['item_subtotal']=$orditem['item_qty']*$newprice;
            $neworder['revenue']=$neworder['item_cost']+$neworder['shipping']+$neworder['tax'];
            $newitem=array(
                'order_item_id'=>$newid*(-1),
                'item_id'=>-1,
                'item_row'=>1,
                'item_number'=>$itemdata['item_number'],
                'item_color'=>$itmcolor,
                'colors'=>$colors,
                'num_colors'=>$itemdata['num_colors'],
                'item_description'=>$orditem['item_name']
            );
            //
            if ($itemdata['num_colors']==0) {
                $newitem['out_colors']=$this->empty_htmlcontent;
            } else {
                $options=array(
                    'order_item_id'=>$newitem['order_item_id'],
                    'item_id'=>$newitem['item_id'],
                    'colors'=>$newitem['colors'],
                    'item_color'=>$newitem['item_color'],
                );
                $newitem['out_colors']=$this->load->view('leadorderdetails/item_color_choice', $options, TRUE);
            }
            if ($newitem['num_colors']>1) {
                $newitem['item_color_add']=1;
            } else {
                $newitem['item_color_add']=0;
            }

            $newitem['item_qty']=$order['order_qty'];
            $newitem['item_price']=$newprice;
            $newitem['item_subtotal']=MoneyOutput($newitem['item_qty']*$newprice);
            $newitem['printshop_item_id']=(isset($itemdata['printshop_item_id']) ? $itemdata['printshop_item_id'] : '');
            $items[]=$newitem;
            $orditem['items']=$items;
            // Prepare Imprint, Imprint Details
            $imprint[]=array(
                'order_imprint_id'=>-1,
                'imprint_description'=>'&nbsp;',
                'imprint_qty'=>0,
                'imprint_price'=>0,
                'imprint_item'=>0,
                'imprint_subtotal'=>'&nbsp;',
                'delflag'=>0,
            );
            $orditem['imprints']=$imprint;
            // Change Imprint Details
            $imprdetails=array();
            $detailfld=$this->db->list_fields('ts_order_imprindetails');
            for ($i=1; $i<13; $i++) {
                $newloc=array(
                    'title'=>'Loc '.$i,
                    'active'=>0,
                );
                foreach ($detailfld as $row) {
                    switch ($row) {
                        case 'order_imprindetail_id':
                            $newloc[$row]=$i*(-1);
                            break;
                        case 'imprint_type':
                            $newloc[$row]='NEW';
                            break;
                        case 'num_colors':
                            $newloc[$row]=1;
                            break;
                        default :
                            $newloc[$row]='';
                    }
                }
                $newloc['print_1']=($i==1 ? 0 : $orditem['print_price']);
                $newloc['print_2']=$orditem['print_price'];
                $newloc['print_3']=$orditem['print_price'];
                $newloc['print_4']=$orditem['print_price'];
                $newloc['setup_1']=$orditem['setup_price'];
                $newloc['setup_2']=$orditem['setup_price'];
                $newloc['setup_3']=$orditem['setup_price'];
                $newloc['setup_4']=$orditem['setup_price'];
                $imprdetails[]=$newloc;
            }
            $orditem['imprint_details']=$imprdetails;
            // Add new element to Order Items
            $neworder_items[]=$orditem;
        }

        $billing=$leadorder['billing'];
        if ($order_type=='old') {
            $newbilling=array(
                'order_billing_id' =>-1,
                'customer_name' =>'',
                'company' =>'',
                'customer_ponum' =>'',
                'address_1' =>'',
                'address_2' =>'',
                'city' =>'',
                'state_id' =>$defstate,
                'zip' =>'',
                'country_id' =>$defcountry,
            );
        } else {
            $newbilling=array(
                'order_billing_id' =>-1,
                'customer_name' =>$billing['customer_name'],
                'company' =>$billing['company'],
                'customer_ponum' =>$billing['customer_ponum'],
                'address_1' =>$billing['address_1'],
                'address_2' =>$billing['address_2'],
                'city' =>$billing['city'],
                'state_id' =>$billing['state_id'],
                'zip' =>$billing['zip'],
                'country_id' =>$billing['country_id'],
            );

        }
        // Shipping
        if ($order['order_blank']==0) {
            $rush=$this->shipping_model->get_rushlist($order['item_id'], $neworder['order_date']);
        } else {
            $rush=$this->shipping_model->get_rushlist_blank($order['item_id'], $neworder['order_date']);
        }

        foreach ($rush['rush'] as $row) {
            if ($row['current']==1) {
                $shipdate=$row['date'];
                $rush_price=$row['price'];
                $rush_idx=$row['id'];
            }
        }

        $shipping=array(
            'order_shipping_id'=>-1,
            'event_date'=>'',
            'rush_idx'=>$rush_idx,
            'rush_list'=>  serialize($rush),
            'rush_price'=>$rush_price,
            'shipdate'=>$shipdate,
            'out_rushlist'=>$rush,
            'out_eventdate' =>'&nbsp;',
            'out_shipdate'=>date('m/d/y', $shipdate),
            'arrive_date'=>'',
            'out_arrivedate'=>'',
            'arriveclass'=>'',
        );

        $shipaddress=$leadorder['shipping_address'];

        $newshipaddr=array();
        if ($order_type=='old') {
            if (count($shipaddress)==0) {
                $newaddr=$this->_create_empty_shipaddress();
                $newaddr['order_shipaddr_id']=-1;
                $newshipaddr[]=$newaddr;
            } else {
                foreach ($shipaddress as $srow) {
                    $srow['shipping']=$srow['shipping_costs'];
                    $srow['shipping_costs']=array();
                    $newshipaddr[]=$srow;
                }
                //$newshipaddr=$shipaddress;
            }
        } else {
            if (count($shipaddress)==0) {
                // Add empty shipaddress

            } else {
                $adridx=1;
                $shiprate=0;
                foreach ($shipaddress as $adrrow) {
                    // Get Old Shipping Method
                    $default_ship_method='';
                    if (isset($adrrow['shipping_cost'])) {
                        $oldcosts=$adrrow['shipping_costs'];
                        foreach ($oldcosts as $costrow) {
                            if ($costrow['delflag']==0 && $costrow['current']==1) {
                                $default_ship_method=$costrow['shipping_method'];
                            }
                        }
                    }
                    $cntres=$this->shipping_model->count_shiprates($neworder_items, $adrrow, $shipping['shipdate'], $order['brand'], $default_ship_method);
                    if ($cntres['result']==$this->success_result) {
                        $rates=$cntres['ships'];
                    } else {
                        $rates=array();
                    }
                    $newidx=1;
                    $shipcost=array();
                    foreach ($rates as $rrow) {
                        $shipcost[]=array(
                            'order_shipcost_id'=>$newidx*(-1),
                            'shipping_method'=>$rrow['ServiceName'],
                            'shipping_cost'=>$rrow['Rate'],
                            'arrive_date'=>$rrow['DeliveryDate'],
                            'current'=>$rrow['current'],
                            'delflag'=>0,
                        );
                        if ($rrow['current']==1) {
                            $shipdat=$rrow['Rate'];
                            $shiprate+=$rrow['Rate'];
                            $arivdate=$rrow['DeliveryDate'];
                        }
                        $newidx++;
                    }
                    $packages[]=array(
                        'order_shippack_id'=>$adridx*(-1),
                        'deliver_service'=>'UPS',
                        'track_code'=>'',
                        'track_date'=>0,
                        'send_date'=>0,
                        'senddata'=>0,
                        'delflag'=>0,
                        'delivered' =>0,
                        'delivery_address'=>'',
                    );
                    $newshipaddr[]=array(
                        'order_shipaddr_id'=>$adridx*(-1),
                        'country_id' =>$adrrow['country_id'],
                        'address' =>$adrrow['address'],
                        'ship_contact'=>$adrrow['ship_contact'],
                        'ship_company'=>$adrrow['ship_company'],
                        'ship_address1'=>$adrrow['ship_address1'],
                        'ship_address2'=>$adrrow['ship_address2'],
                        'city' =>$adrrow['city'],
                        'state_id' =>$adrrow['state_id'],
                        'zip' =>$adrrow['zip'],
                        'item_qty' =>$adrrow['item_qty'],
                        'ship_date' =>$shipping['shipdate'],
                        'arrive_date' =>$arivdate,
                        'shipping' =>$adrrow['shipping'], // $shipdat,
                        'sales_tax' =>$adrrow['sales_tax'],
                        'resident' =>$adrrow['resident'],
                        'ship_blind' =>$adrrow['ship_blind'],
                        'taxview' =>$adrrow['taxview'],
                        'taxcalc' =>$adrrow['taxcalc'],
                        'tax' =>$adrrow['tax'],
                        'tax_exempt' =>$adrrow['tax_exempt'],
                        'tax_reason' =>$adrrow['tax_reason'],
                        'tax_exemptdoc' =>$adrrow['tax_exemptdoc'],
                        'tax_exemptdocsrc' =>$adrrow['tax_exemptdocsrc'],
                        'tax_exemptdocid' =>$adrrow['tax_exemptdocid'],
                        'shipping_costs'=>$shipcost,
                        'out_shipping_method' =>$adrrow['out_shipping_method'],
                        'out_zip' => $adrrow['out_zip'],
                        'out_country'=>$adrrow['out_country'],
                        'packages'=>$packages,
                    );
                    $adridx++;
                }

            }
        }
        $newshipping=$this->_leadorder_shipping($newshipaddr, $shipping);

        $message=array(
            'general_notes'=>'',
            'history'=>$newart_history,
        );
        $oldcharges=$this->get_order_charges($order['order_id']);
        $newcharge=array();
        $chridx=1;
        foreach ($oldcharges as $row) {
            $row['order_payment_id']=$chridx*(-1);
            unset($row['order_id']);
            if ($chridx==1) {
                $row['amount']=$neworder['revenue'];
            }
            $newcharge[]=$row;
            $chridx++;
        }
        // Recalc totals for new order
        $out=$this->_dublicate_order_totals($neworder,$contacts,$neworder_items, $newartw,$newshipping,$newshipaddr,$newbilling,$message,$countries,$newcharge,$artlocations);
        return $out;
    }
//
    // Prepare invoice details
    public function prepare_invoice_details($leadorder, $adrcnt) {
        $order_items=$leadorder['order_items'];
        $order=$leadorder['order'];

        $item_details=array();
        foreach ($order_items as $row) {
            $items=$row['items'];
            foreach ($items as $irow) {
                if ($irow['item_number']==$this->config->item('custom_itemnum')) {
                    $item_description='Custom Shaped Stress Balls - '.$irow['item_description'];
                } else {
                    $item_description=$irow['item_description'].' - '.$irow['item_color'];
                }
                $item_details[]=array(
                    'item_num'=>$irow['item_number'],
                    'item_description'=>$item_description,
                    'item_qty'=>$irow['item_qty'],
                    'item_price'=>PriceOutput($irow['item_price']),
                    'item_subtotal'=>$irow['item_subtotal'],
                    'item_color'=>'#000000',
                );
            }
            $imprints=$row['imprints'];
            if ($order['order_blank']==0) {
                foreach ($imprints as $irow) {
                    if ($irow['imprint_description']!='&nbsp;') {
                        $item_details[]=array(
                            'item_num'=>'',
                            'item_description'=>$irow['imprint_description'],
                            'item_qty'=>$irow['imprint_qty'],
                            'item_price'=>$irow['imprint_price'],
                            'item_subtotal'=>$irow['imprint_subtotal'],
                            'item_color'=>'#000000',
                        );
                    }
                }
            }
        }

        if (floatval($order['mischrg_val1'])!=0) {
            $item_details[]=array(
                'item_num'=>'',
                'item_description'=>$order['mischrg_label1'],
                'item_qty'=>'',
                'item_price'=>$order['mischrg_val1'],
                'item_subtotal'=>$order['mischrg_val1'],
                'item_color'=>'#000000',
            );
        }

        if (floatval($order['mischrg_val2'])!=0) {
            $item_details[]=array(
                'item_num'=>'',
                'item_description'=>$order['mischrg_label2'],
                'item_qty'=>'',
                'item_price'=>$order['mischrg_val2'],
                'item_subtotal'=>$order['mischrg_val2'],
                'item_color'=>'#000000',
            );
        }

        if (floatval($order['discount_val'])!=0) {
            $item_details[]=array(
                'item_num'=>'',
                'item_description'=>(empty($order['discount_label']) ? 'Discount' : $order['discount_label']),
                'item_qty'=>'',
                'item_price'=>($order['discount_val'] > 0 ? '('.$order['discount_val'].')' : abs($order['discount_val'])),
                'item_subtotal'=>($order['discount_val'] > 0 ? '('.MoneyOutput($order['discount_val'],2).')' : MoneyOutput(abs($order['discount_val']),2)),
                'item_color'=>($order['discount_val'] > 0 ? '#ff0000' : '#000000'),
            );
        }


        if (!empty($leadorder['order']['shipping'])) {
            $shipping_address=$leadorder['shipping_address'];
            $shipmethod='';
            if (count($shipping_address)>1) {
                $shipmethod='Shipping Charge - Multiple Addresses';
            } else {
                $costs=$shipping_address[0]['shipping_costs'];
                foreach ($costs as $srow) {
                    if ($srow['delflag']==0 && $srow['current']==1) {
                        $shipmethod=$srow['shipping_method'].' Shipping Charge';
                    }
                }
            }
            $item_details[]=array(
                'item_num'=>'',
                // 'item_description'=>'Shipping',
                'item_description'=>$shipmethod,
                'item_qty'=>1,
                'item_price'=>$leadorder['order']['shipping'],
                'item_subtotal'=>  MoneyOutput($leadorder['order']['shipping'],2),
                'item_color'=>'#000000',
            );
            // Check Rush
            $shipdata=$leadorder['shipping'];
            $rushlist=$shipdata['out_rushlist']['rush'];
            $term='';
            foreach ($rushlist as $rrow) {
                if ($rrow['date']==$shipdata['shipdate']) {
                    $term=$rrow['rushterm'];
                    break;
                }
            }
            if (!empty($term) && $term!='Standard' && $shipdata['rush_price']>0) {
                $item_details[]=array(
                    'item_num'=>'',
                    'item_description'=>$term,
                    'item_qty'=>'',
                    'item_price'=>$shipdata['rush_price'],
                    'item_subtotal'=>MoneyOutput($shipdata['rush_price'],2),
                    'item_color'=>'#000000',
                );
            }
        }
        $detcnt=count($item_details)+$adrcnt;
        if ($detcnt<15) {
            // $numv=count($item_details);
            for ($i=$detcnt; $i<15; $i++) {
                $item_details[]=array(
                    'item_num'=>'',
                    'item_description'=>'',
                    'item_qty'=>'',
                    'item_price'=>'',
                    'item_subtotal'=>'',
                    'item_color'=>'#000000',
                );
            }
        }

        return $item_details;
    }

    // Show Imprint Location
    public function get_leadorder_imprintloc($imprint_loc_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
        $this->db->select('i.item_inprint_id, i.item_inprint_view');
        $this->db->from('sb_item_inprints i');
        $this->db->where('i.item_inprint_id', $imprint_loc_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_inprint_id'])) {
            $out['msg']='Image Not Found';
            return $out;
        }

        $path_sh=$this->config->item('imprintimages_relative');
        $path_fl=$this->config->item('imprintimages');
        $source=$res['item_inprint_view'];
        $filesource=  str_replace($path_sh, $path_fl, $source);
        if (!file_exists($filesource)) {
            $out['msg']='Source File '.$filesource.' Not Found ';
            return $out;
        }
        $viewopt=array(
            'source'=>$source,
        );
        list($width, $height, $type, $attr) = getimagesize($filesource);
        // Rate
        if ($width >= $height) {
            if ($width<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$width;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        } else {
            if ($height<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$height;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        }
        $out['result']=$this->success_result;
        $out['viewoptions']=$viewopt;
        return $out;
    }

    // Show Item Picture
    public function get_leadorder_itemimage($item_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
        $this->db->select('i.item_img_thumb, i.item_img_id');
        $this->db->from('sb_item_images i');
        $this->db->where('i.item_img_item_id', $item_id);
        $this->db->order_by('i.item_img_order');
        $res=$this->db->get()->row_array();
        if (!isset($res['item_img_id'])) {
            $out['msg']='Image Not Found';
            return $out;
        }
        $path_sh=$this->config->item('itemimages_relative');
        $path_fl=$this->config->item('itemimages');
        $source=$res['item_img_thumb'];
        $filesource=  str_replace($path_sh, $path_fl, $source);
        if (!file_exists($filesource)) {
            $out['msg']='Source File '.$filesource.' Not Found ';
            return $out;
        }
        $viewopt=array(
            'source'=>$source,
        );
        list($width, $height, $type, $attr) = getimagesize($filesource);
        // Rate
        if ($width >= $height) {
            if ($width<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$width;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        } else {
            if ($height<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$height;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        }
        $out['result']=$this->success_result;
        $out['viewoptions']=$viewopt;
        return $out;
    }

    public function payment_edit($payment, $postdata) {
        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
        if (!array_key_exists('fldname',$postdata) || !array_key_exists('newval',$postdata)) {
            $out['msg']='Parametr Name or Value not Send';
            return $out;
        }
        $fldname=$postdata['fldname'];
        $newval=$postdata['newval'];
        if ($fldname=='date' && !empty($newval)) {
            $newval=strtotime($newval);
        }
        $payment[$fldname]=$newval;
        usersession('newpayment', $payment);
        $out['result']=$this->success_result;
        return $out;
    }

    // Save new manual payment
    public function payment_save($leadorder, $payment, $ordersession) {
        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
        if (empty($payment['date'])) {
            $out['msg']='Empty Payment Date';
            return $out;
        }
        if (empty($payment['paytype'])) {
            $out['msg']='Empty Payment Type';
            return $out;
        }
        if (empty($payment['paynum'])) {
            $out['msg']='Empty Payment#';
            return $out;
        }
        if (floatval($payment['amount'])<=0) {
            $out['msg']='Empty Payment Value';
            return $out;
        }
        // Save to payments
        $payments=$leadorder['payments'];
        $order=$leadorder['order'];
        // Make changes
        $paymentamnt=$payment['amount'];
        if ($payment['type']=='refund') {
            $paymentamnt=(-1)*$paymentamnt;
        }
        $payclass='text_grey';
        $paysum=MoneyOutput($paymentamnt,2);
        if ($paymentamnt<0) {
            $payclass='text_red';
            $paysum='('.MoneyOutput(abs($paymentamnt),2).')';
        }
        $idx=count($payments)+1;

        $payments[]=array(
            'batch_id'=>$idx*(-1),
            'batch_date'=>$payment['date'],
            'batch_amount'=>$paymentamnt,
            'batch_vmd'=>0,
            'batch_amex'=>0,
            'batch_other'=>($payment['paytype']=='WriteOFF' ? 0 : $paymentamnt),
            'batch_writeoff'=>($payment['paytype']=='WriteOFF' ? $paymentamnt : 0),
            'batch_term'=>0,
            'batch_due'=>0,
            'batch_type'=>$payment['paytype'],
            'batch_num'=>$payment['paynum'],
            'out_date'=>date('m/d', $payment['date']),
            'out_name'=>$payment['paytype'].' '.$payment['paynum'],
            'payclass'=>$payclass,
            'paysum'=>$paysum,
        );
        $totalpay=0;
        foreach ($payments as $row) {
            $totalpay+=$row['batch_amount'];
        }
        $order['payment_total']=$totalpay;
        $leadorder['payments']=$payments;
        $leadorder['order']=$order;
        usersession('newpayment', NULL);
        usersession($ordersession, $leadorder);
        $this->_leadorder_totals($leadorder,$ordersession);
        $out['result']=$this->success_result;
        return $out;
    }

    private function _create_empty_shipaddress($prvadr=array(), $newidx='') {
        $this->load->model('shipping_model');
        $packages[]=array(
            'order_shippack_id'=>-1,
            'deliver_service'=>'UPS',
            'track_code'=>'',
            'track_date'=>0,
            'send_date'=>0,
            'senddata'=>0,
            'delflag'=>0,
            'delivered' =>0,
            'delivery_address'=>'',
        );
        if (empty($prvadr)) {
            $countries=$this->shipping_model->get_countries_list(array('orderby'=>'sort'));
            $defcnt=$countries[0]['country_id'];
            $defstate=NULL;
            $defoutstate='';
            $states=$this->shipping_model->get_country_states($defcnt);

            $newaddress=array(
                'order_shipaddr_id' =>($newidx),
                'country_id'=>$countries[0]['country_id'],
                'address' =>'',
                'ship_contact'=>'',
                'ship_company'=>'',
                'ship_address1'=>'',
                'ship_address2'=>'',
                'city' =>'',
                'state_id' =>$defstate,
                'zip' =>'',
                'item_qty' =>0,
                'ship_date' =>0,
                'arrive_date' =>0,
                'shipping' =>0,
                'sales_tax' =>0,
                'resident' =>0,
                'ship_blind' =>0,
                'taxview' =>0,
                'taxcalc' =>0,
                'tax' =>0.00,
                'tax_exempt' =>0,
                'tax_reason' =>'',
                'tax_exemptdoc' =>'',
                'tax_exemptdocid' =>0,
                'tax_exemptdocsrc'=>'',
                'shipping_costs' => array(),
                'out_shipping_method'=>'',
                'out_zip'=>$defoutstate.' ',
                'out_country'=>$countries[0]['country_iso_code_2'],
                'packages'=>$packages,
            );
        } else {
            $oldshipping_costs=$prvadr['shipping_costs'];
            $newcost=array();
            $costidx=1;
            foreach ($oldshipping_costs as $crow) {
                $newcost[]=array(
                    'order_shipcost_id'=>(-1)*$costidx,
                    'shipping_method' =>$crow['shipping_method'],
                    'shipping_cost' =>$crow['shipping_cost'],
                    'arrive_date' =>$crow['arrive_date'],
                    'current' =>$crow['current'],
                    'delflag' =>$crow['delflag'],
                );
                $costidx++;
            }
            $newaddress=array(
                'order_shipaddr_id' =>$newidx,
                'country_id'=>$prvadr['country_id'],
                'address' =>$prvadr['address'],
                'ship_contact'=>$prvadr['ship_contact'],
                'ship_company'=>$prvadr['ship_company'],
                'ship_address1'=>$prvadr['ship_address1'],
                'ship_address2'=>$prvadr['ship_address2'],
                'city' =>$prvadr['city'],
                'state_id' =>$prvadr['state_id'],
                'zip' =>$prvadr['zip'],
                'item_qty' =>$prvadr['item_qty'],
                'ship_date' =>$prvadr['ship_date'],
                'arrive_date' =>$prvadr['arrive_date'],
                'shipping' =>$prvadr['shipping'],
                'sales_tax' =>$prvadr['sales_tax'],
                'resident' =>$prvadr['resident'],
                'ship_blind' =>$prvadr['ship_blind'],
                'taxview' =>$prvadr['taxview'],
                'taxcalc' =>$prvadr['taxcalc'],
                'tax' =>$prvadr['tax'],
                'tax_exempt' =>$prvadr['tax_exempt'],
                'tax_reason' =>$prvadr['tax_reason'],
                'tax_exemptdoc' =>$prvadr['tax_exemptdoc'],
                'tax_exemptdocid' =>$prvadr['tax_exemptdocid'],
                'tax_exemptdocsrc'=>$prvadr['tax_exemptdocsrc'],
                'shipping_costs' => $newcost,
                'out_shipping_method'=>$prvadr['out_shipping_method'],
                'out_zip'=>$prvadr['out_zip'],
                'out_country'=>$prvadr['out_country'],
                'packages'=>$packages,
            );
        }
        return $newaddress;
    }

    private function _create_empty_billddress() {
        $this->load->model('shipping_model');
        $countries=$this->shipping_model->get_countries_list(array('orderby'=>'sort'));
        $defcountry=$countries[0]['country_id'];
        $states=$this->shipping_model->get_country_states($defcountry);
        $defstate=NULL;
        if (count($states)>0) {
            $defstate=$states[0]['state_id'];
        }
        $billfld=$this->db->list_fields('ts_order_billings');
        $billing=array();
        foreach ($billfld as $fld) {
            $billing[$fld]='';
        }
        $billing['order_billing_id']=-1;
        $billing['country_id']=$defcountry;
        $billing['state_id']=$defstate;
        return $billing;
    }

    private function _recalc_shipping($order_items, $shipping, $shipping_address, $brand) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        if (count($order_items)==0) {
            // change shipping and address
            $sidx=0;
            foreach ($shipping_address as $srow) {
                $costs=$shipping_address[$sidx]['shipping_costs'];
                $cidx=0;
                foreach ($costs as $crow) {
                    $costs[$cidx]['delflag']=1;
                    $cidx++;
                }
                $shipping_address[$sidx]['item_qty']=0;
                $shipping_address[$sidx]['ship_date']=0;
                $shipping_address[$sidx]['arrive_date']=0;
                $shipping_address[$sidx]['sales_tax']=0;
                $shipping_address[$sidx]['shipping']=0;
                $shipping_address[$sidx]['tax']=0;
                $shipping_address[$sidx]['shipping_costs']=$costs;
                $sidx++;
            }
            $out['result']=$this->success_result;
        } else {
            $shiprate=0;
            $items=$order_items;
            $shipidx=0;
            $cnt=0;
            foreach ($shipping_address as $shprow) {
                if (!empty($shprow['zip'])) {
                    // Get Old Shipping Method
                    $default_ship_method='';
                    if (isset($shprow['shipping_cost'])) {
                        $oldcosts=$shprow['shipping_costs'];
                        foreach ($oldcosts as $costrow) {
                            if ($costrow['delflag']==0 && $costrow['current']==1) {
                                $default_ship_method=$costrow['shipping_method'];
                            }
                        }
                    }
                    $cntres=$this->shipping_model->count_shiprates($items, $shipping_address[$shipidx], $shipping['shipdate'], $brand, $default_ship_method);
                    if ($cntres['result']==$this->success_result) {
                        $rates=$cntres['ships'];
                        $shipcost=$shipping_address[$shipidx]['shipping_costs'];
                        $cidx=0;
                        foreach ($shipcost as $row) {
                            $shipcost[$cidx]['delflag']=1;
                            $cidx++;
                        }
                        $newidx=count($shipcost)+1;
                        foreach ($rates as $row) {
                            $shipcost[]=array(
                                'order_shipcost_id'=>$newidx*(-1),
                                'shipping_method'=>$row['ServiceName'],
                                'shipping_cost'=>$row['Rate'],
                                'arrive_date'=>$row['DeliveryDate'],
                                'current'=>$row['current'],
                                'delflag'=>0,
                            );
                            if ($row['current']==1) {
                                $shipping_address[$shipidx]['shipping']=$row['Rate'];
                                $shiprate+=$row['Rate'];
                            }
                            $newidx++;
                        }
                        $shipping_address[$shipidx]['shipping_costs']=$shipcost;
                    } else {
                        $shipping_address[$shipidx]['shipping_costs']=array();
                        $shipping_address[$shipidx]['out_shipping_method']='';
                        $shipping_address[$shipidx]['shipping']=0;
                    }
                }
                $shipidx++;
                $cnt++;
            }
            $shipping['shipping']=$shiprate;
            $out['result']=$this->success_result;
        }
        $out['shipping']=$shipping;
        $out['shipping_address']=$shipping_address;
        return $out;
    }

    private function _recalc_shippingaddress($shipdata, $shipsession) {
        $order=$shipdata['order'];
        $shipping=$shipdata['shipping'];
        $shipping_address=$shipdata['shipping_address'];
        $order_items=$shipdata['order_items'];
        $order['shipping']=$this->_leadorder_shipcost($shipping_address);
        $total_item=0;
        $total_qty=0;
        $total_imprint=0;
        foreach ($order_items as $row) {
            $total_item+=$row['item_subtotal'];
            $total_imprint+=$row['imprint_subtotal'];
            $total_qty+=$row['item_qty'];
        }
        $order['item_imprint']=$total_imprint;
        $order['item_cost']=$total_item;
        $order['order_qty']=$total_qty;
        $shpidx=0;
        // $base_cost=floatval($order['item_cost'])+floatval($order['item_imprint'])+intval($order['is_shipping'])*floatval($order['shipping']);
        $base_cost=floatval($order['item_cost'])+floatval($order['item_imprint'])+floatval($order['shipping']);
        // $base_cost+=floatval($order['cc_fee'])+floatval($shipping['rush_price']);
        $base_cost+=floatval($shipping['rush_price']);
        $base_cost+=floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);

        $tax=0;
        $shipadrcnt=count($shipping_address);

        foreach ($shipping_address as $row) {
            if ($row['taxcalc']==0) {
                $shipping_address[$shpidx]['tax']=0;
                $shipping_address[$shpidx]['sales_tax']=0;
            } else {
                $taxpercent=$this->config->item('salestax');
                if ($order['order_date']>=$this->config->item('datenewtax')) {
                    $taxpercent=$this->config->item('salesnewtax');
                }
                if ($shipadrcnt==1) {
                    $adrtax=round($base_cost*$taxpercent/100,2);
                    $shipping_address[$shpidx]['sales_tax']=$adrtax;
                    $shipping_address[$shpidx]['tax']=$adrtax;
                    $tax+=$adrtax;
                } else {
                    $adrtax=round($base_cost/$total_qty*$row['item_qty']*$taxpercent/100,2);
                    $shipping_address[$shpidx]['sales_tax']=$adrtax;
                    $shipping_address[$shpidx]['tax']=$adrtax;
                    $tax+=$adrtax;
                }
            }
            $shpidx++;
        }
        $order['tax']=$tax;
        $revenue=$base_cost+floatval($order['tax']);
        $order['revenue']=$revenue;
        if ($order['order_cog']=='') {
            $profit=round($revenue*$this->config->item('default_profit')/100,2);
        } else {
            $profit=$this->_leadorder_profit($order);
            if ($revenue!=0) {
                $profit_perc=round($profit/$revenue*100,1);
                $order['profit_perc']=$profit_perc;
            } else {
                $order['profit_perc']=0;
            }
        }
        $order['profit']=$profit;
        $shipdata['order']=$order;
        $shipdata['shipping']=$shipping;
        $shipdata['shipping_address']=$shipping_address;
        usersession($shipsession,$shipdata);
    }

//    private function _get_oldorder_item($item_id, $item_qty) {
//        $itemdata=$this->_get_itemdata($item_id);
//
//        $setupprice=$this->_get_item_priceimprint($item_id, 'setup');
//        $printprice=$this->_get_item_priceimprint($item_id, 'imprint');
//        $priceqty=$this->_get_item_priceqty($item_id, $itemdata['item_template'], $item_qty);
//        $colors=$itemdata['colors'];
//        $order_item_id=-1;
//        $newitem=array(
//            'order_item_id'=>$order_item_id,
//            'item_id'=>$item_id,
//            'item_number'=>$itemdata['item_number'],
//            'item_name'=>$itemdata['item_name'],
//            'item_qty'=>$item_qty,
//            'colors'=>$colors,
//            'num_colors'=>count($colors),
//            'item_template'=>$itemdata['item_template'],
//            'item_weigth'=>$itemdata['item_weigth'],
//            'cartoon_qty'=>$itemdata['cartoon_qty'],
//            'cartoon_width'=>$itemdata['cartoon_width'],
//            'cartoon_heigh'=>$itemdata['cartoon_heigh'],
//            'cartoon_depth'=>$itemdata['cartoon_depth'],
//            'boxqty'=>$itemdata['boxqty'],
//            'setup_price'=>$setupprice,
//            'print_price'=>$printprice,
//            'item_subtotal'=>0,
//            'imprint_subtotal'=>0,
//            'vendor_zipcode'=>$itemdata['vendor_zipcode'],
//            'charge_perorder'=>0,
//            'charge_peritem'=>0,
//            'charge_pereach'=>0,
//            'imprint_locations'=>$itemdata['imprints'],
//        );
//        $defcolor='';
//        if (count($colors)>0) {
//            $defcolor=$colors[0];
//        }
//        $items=array();
//        $numpp=1;
//        $countitems=1;
//
//        $subtotal=$item_qty*$priceqty;
//        $newitem['item_subtotal']+=$subtotal;
//        $coloradd=0;
//        if ($newitem['num_colors']>1 && $numpp==$countitems) {
//            $coloradd=1;
//        }
//        if ($newitem['num_colors']==0) {
//            $out_colors=$this->empty_htmlcontent;
//        } else {
//            $options=array(
//                'order_item_id'=>$order_item_id,
//                'item_id'=>(-1),
//                'colors'=>$newitem['colors'],
//                'item_color'=>$defcolor,
//            );
//            $out_colors=$this->load->view('leadorderdetails/item_color_choice', $options, TRUE);
//        }
//        $items[]=array(
//            'order_item_id' =>$order_item_id,
//            'item_id' =>(-1),
//            'item_row' =>$numpp,
//            'item_number' =>$newitem['item_number'],
//            'item_color' =>$defcolor,
//            'colors'=>$colors,
//            'out_colors'=>$out_colors,
//            'num_colors' =>$newitem['num_colors'],
//            'item_description' =>$itemdata['item_name'],
//            'item_color_add' =>$coloradd,
//            'item_qty' =>$item_qty,
//            'item_price'=>$priceqty,
//            'item_subtotal'=>MoneyOutput($subtotal),
//            'printshop_item_id'=>(isset($itemdata['printshop_item_id']) ? $itemdata['printshop_item_id'] : ''),
//        );
//        $numpp++;
//        $newitem['items']=$items;
//        $imprints=array();
//        $imprints[]=array(
//            'order_imprint_id'=>(-1),
//            'imprint_description'=>$this->empty_htmlcontent,
//            'imprint_item'=>0,
//            'imprint_qty'=>0,
//            'imprint_price'=>0,
//            'outqty'=>$this->empty_htmlcontent,
//            'outprice'=>$this->empty_htmlcontent,
//            'imprint_subtotal'=>$this->empty_htmlcontent,
//            'delflag'=>0,
//        );
//
//        $newitem['imprints']=$imprints;
//        // Add Imprints Details
//        $impr_details=array();
//        for ($i=1; $i<=16; $i++) {
//            $impr_details[]=array(
//                'title' =>'Loc '.$i,
//                'active' =>0,
//                'order_imprindetail_id' =>(-1)*$i,
//                'order_item_id' =>$order_item_id,
//                'imprint_type' =>'NEW',
//                'repeat_note' =>'',
//                'location_id' =>'',
//                'num_colors' =>$newitem['num_colors'],
//                'print_1' =>0,
//                'print_2' =>$printprice,
//                'print_3' =>$printprice,
//                'print_4' =>$printprice,
//                'setup_1' =>$setupprice,
//                'setup_2' =>$setupprice,
//                'setup_3' =>$setupprice,
//                'setup_4' =>$setupprice,
//                'extra_cost' =>'',
//            );
//        }
//        $newitem['imprint_details']=$impr_details;
//        return $newitem;
//    }

    public function search_items($item_num) {
        $this->db->select('i.item_number as label, i.item_id as id');
        $this->db->from('v_itemsearch i');
        $this->db->like('i.item_number', $item_num);
        $this->db->order_by('i.item_number');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function _get_oldorder_itemdata($item_id) {
        $this->db->select('i.item_id, i.item_number, i.item_name');
        $this->db->from('v_itemsearch i');
        $this->db->where('i.item_id', $item_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_id'])) {
            $out['result']=$this->error_result;
        } else {
            $out['result']=$this->success_result;
            $out['item_id']=$res['item_id'];
            $out['item_number']=$res['item_number'];
            $out['item_name']=$res['item_name'];
        }
        return $out;
    }

    // Prepare Invoce File
    public function prepare_invoicedoc($leadorder, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);

        $this->load->model('shipping_model');
        $this->load->helper(array('dompdf', 'file'));
        $order=$leadorder['order'];
        $payments_details = [];
        foreach ($leadorder['payments'] as $prow) {
            $label = '';
            $type = 'payment';
            if ($prow['batch_amount']<0) {
                $label.='Refund ';
                $type = 'refund';
            } else {
                // if ($)
                $label.='Payment ';
            }
            $label.='- '.date('m/d/y', $prow['batch_date']);
            if ($type=='payment') {
                if ($prow['batch_type']=='ACH') {
                    $label.=' (ACH)';
                } elseif ($prow['batch_type']=='American Express') {
                    $label .= ' (AmEx ' . substr($prow['batch_num'],-4,4) . ')';
                } elseif ($prow['batch_type']=='Amex') {
                    $label .= ' (AmEx ' . substr($prow['batch_num'], -4, 4) . ')';
                } elseif ($prow['batch_type']=='Cash') {
                    $label .= ' (Cash)';
                } elseif ($prow['batch_type']=='Check') {
                    $label .= ' (Check)';
                } elseif ($prow['batch_type']=='Discover') {
                    $label .= ' (Discover ' . substr($prow['batch_num'],-4,4) . ')';
                } elseif ($prow['batch_type']=='Manual CC') {
                    $label .= ' (Manual CC)';
                } elseif ($prow['batch_type']=='Mastercard') {
                    $label .= ' (MC ' . substr($prow['batch_num'], -4,4) . ')';
                } elseif ($prow['batch_type']=='Paypal') {
                    $label .= ' (Paypal)';
                } elseif ($prow['batch_type']=='Visa') {
                    $label .= ' (Visa ' . substr($prow['batch_num'],-4,4) . ')';
                } elseif ($prow['batch_type']=='Wire') {
                    $label.=' (Wire)';
                }
            }
            $payments_details[]=[
                'label' => $label,
                'value' => MoneyOutput(abs($prow['batch_amount'])),
                'type' => $type,
            ];
        }

        $customer_po='';
        $biladr=array();
        if (isset($leadorder['billing'])) {
            $billing=$leadorder['billing'];
            if (!empty($billing['customer_name'])) {
                if (!empty($billing['company'])) {
                    array_push($biladr, $billing['company']);
                }
                if (!empty($billing['customer_name'])) {
                    array_push($biladr, $billing['customer_name']);
                }
                if (!empty($billing['address_1'])) {
                    array_push($biladr, $billing['address_1']);
                }
                if (!empty($billing['address_2'])) {
                    array_push($biladr, $billing['address_2']);
                }
                $adrrow='';
                if (!empty($billing['city'])) {
                    $adrrow.=$billing['city'];
                    if (!empty($billing['state_id'])) {
                        // Get State
                        $statedat=$this->shipping_model->get_state($billing['state_id']);
                        if (isset($statedat['state_id'])) {
                            $adrrow.=' '.$statedat['state_code'];
                        }
                    }
                    if (!empty($billing['zip'])) {
                        $adrrow.=' '.$billing['zip'];
                    }
                }
                if (!empty($adrrow)) {
                    array_push($biladr, $adrrow);
                }
                if (!empty($billing['customer_ponum'])) {
                    $customer_po=$billing['customer_ponum'];
                }
            }
        }

        $shipping_address=$leadorder['shipping_address'];
        $shipadr=array();
        $idx=0;
        $shipdate=$arivedate=0;
        if (count($shipping_address)>1) {
            array_push($shipadr, 'Shipping/shipped to '.count($shipping_address).' addresses');
        } else {
            foreach ($shipping_address as $row) {
                if ($idx==0) {
                    // array_push($shipadr, $order['customer_name']);
                }
                if (!empty($row['ship_contact'])) {
                    array_push($shipadr, $row['ship_contact']);
                }
                if (!empty($row['ship_company'])) {
                    array_push($shipadr, $row['ship_company']);
                }
                if (!empty($row['ship_address1'])) {
                    array_push($shipadr, $row['ship_address1']);
                }
                if (!empty($row['ship_address2'])) {
                    array_push($shipadr, $row['ship_address2']);
                }
                $adrrow='';
                if (!empty($row['city'])) {
                    $adrrow.=$row['city'];
                }
                if (!empty($row['state_id'])) {
                    $statedat=$this->shipping_model->get_state($row['state_id']);
                    if (isset($statedat['state_id'])) {
                        $adrrow.=' '.$statedat['state_code'];
                    }
                }
                if (!empty($row['zip'])) {
                    $adrrow.=' '.$row['zip'];
                }
                array_push($shipadr, $adrrow);
                if ($row['ship_date']>$shipdate) {
                    $shipdate=$row['ship_date'];
                }
                if ($row['arrive_date']>$arivedate) {
                    $arivedate=$row['arrive_date'];
                }
                $idx++;
            }

        }

        $shipdata=$leadorder['shipping'];

        if (count($biladr)==0 && count($shipping_address)==1) {
            $biladr=$shipadr;
        }

        $shpadrcnt=count($shipadr);
        if (count($biladr)>$shpadrcnt) {
            $shpadrcnt=count($biladr);
        }
        // Prepare details
        $items=$this->prepare_invoice_details($leadorder, $shpadrcnt);
        $balance=$order['revenue']-$order['payment_total'];
        $invnum=$order['order_num'];
        if (!empty($order['order_confirmation'])) {
            $invnum=$order['order_confirmation'];
        }
        $payment_due = '';
        if (!empty($order['balance_term'])) {
            if (!empty($order['credit_appdue'])) {
                $payment_due = date('m/d/y', $order['credit_appdue']);
            }
        } elseif ($balance!=0) {
            $payment_due = date('m/d/y', $order['order_date']);
        }
        $options=array(
            'order_num'=>$invnum,
            'invoice_message'=>$order['invoice_message'],
            'order_date'=>date('m/d/Y',$order['order_date']),
            'customer_code'=>$customer_po,
            'terms'=>(empty($order['balance_term']) ? '' : 'Net 30'), // $order['balance_term']
            // 'payment_due'=>((empty($order['balance_term']) && empty($order['credit_appdue'])) ? '' : date('m/d/y', $order['credit_appdue'])),
            'payment_due'=> $payment_due,
            'shipdate'=>(empty($shipdata['out_shipdate']) ? '' : $shipdata['out_shipdate']),
            'arrive'=>(empty($shipdata['out_arrivedate']) ? '' : $shipdata['out_arrivedate']),
            'billing'=>$biladr,
            'shipping'=>$shipadr,
            'details'=>$items,
            'tax'=>MoneyOutput($order['tax']),
            'total'=>  MoneyOutput($order['revenue']),
            'payments'=> MoneyOutput($order['payment_total']),
            'payments_count' => count($payments_details),
            'payments_detail' => $payments_details,
            'balance'=>  MoneyOutput($balance),
            'tax_term'=>($order['order_date']<=$this->config->item('datenewtax') ? $this->config->item('salestax') : $this->config->item('salesnewtax')),
        );

        // $html=$this->load->view('leadorderdetails/docs/invoice_view', $options, TRUE);

        $file_name='invoice_'.$order['order_confirmation'].'_'.str_replace(array(' ', '/',',','\n','%','#'),'_',$order['order_items']).'.pdf';
        $file_out = $this->config->item('upload_path_preload') . $file_name;

        $this->_invoice_pdfdoc_create($options, $file_out);
        // pdf_create($html, $file_out, true);
        if (file_exists($file_out)) {
            $out['result']=$this->success_result;
            $out['html_path']=$this->config->item('pathpreload').$file_name.'?t='.time();
            $out['doc_path']=$file_out;
        }
        return $out;
    }


    public function prepare_orderinvemail($leadorder, $user_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $contacts=$leadorder['contacts'];
        $inv=array();
        foreach ($contacts as $row) {
            if (intval($row['contact_inv'])==1 && valid_email_address($row['contact_emal'])) {
                $inv[]=array(
                    'name'=>$row['contact_name'],
                    'email'=>$row['contact_emal'],
                );
            }
        }
        if (empty($inv)) {
            $out['msg']='No contacts, associated with invoice';
        }
        $contact_mail='';
        $contact_name='';
        foreach ($inv as $row) {
            $contact_mail.=$row['email'].',';
            if (empty($contact_name)) {
                $contact_name=$row['name'];
            }

        }
        $mail=substr($contact_mail,0,-1);

        $order=$leadorder['order'];

        $this->load->model('user_model');
        $this->load->model('email_model');
        $template='invoice';

        $userdat = $this->user_model->get_user_data($user_id);
        $user_name = $userdat['user_name'];
        $email_sign=$userdat['email_signature'];
        if (empty($userdat['email_signature'])) {
            $email_sign='Sincerely, '.PHP_EOL;
            $email_sign.=''.PHP_EOL;
            $email_sign.=$userdat['user_name'].PHP_EOL;
            $email_sign.='BLUETRACK, Inc.'.PHP_EOL;
            $email_sign.=$userdat['user_email'];
        }
        $mail_template = $this->email_model->get_emailtemplate_byname($template);

        $srcreplace=array(
            '<<order_confirmation>>',
            '<<contact_name>>',
            '<<user_email_signature>>',
            '<<item_name>>',
        );
        $orddata=array(
            $order['order_confirmation'],
            $contact_name,
            $email_sign,
            $order['order_items'],
        );

        $message=  str_replace($srcreplace, $orddata, $mail_template['email_template_body']);
        $subj=  str_replace($srcreplace, $orddata, $mail_template['email_template_subject']);

        $data=array(
            'order_id'=>$order['order_id'],
            'contacts'=>$inv,
            'order_num'=>$order['order_num'],
            'order_confirmation'=>$order['order_confirmation'],
            'contact_mail'=>$mail,
            'subject'=>$subj,
            'message'=>$message,
            'sender'=>(empty($userdat['personal_email']) ? $userdat['user_email'] : $userdat['personal_email']),
        );
        $out['result']=$this->success_result;
        $out['data']=$data;
        return $out;
    }

    public function send_invoicemail($data, $leadorder, $user_id, $ordersession) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->load->model('user_model');
        $this->load->model('artwork_model');
        /* Check Data */
        if (empty($data['from'])) {
            $out['msg']='Enter Sender Email';
            return $out;
        }
        if (empty($data['customer'])) {
            $out['msg']='Enter Customer Email';
            return $out;
        }
        if (empty($data['subject'])) {
            $out['msg']='Enter Message Subject';
            return $out;
        }
        if (empty($data['message'])) {
            $out['msg']='Enter Message Body';
            return $out;
        }
        $toarray = explode(',', $data['customer']);
        foreach ($toarray as $row) {
            if (!valid_email_address(trim($row))) {
                $out['msg'] = $row . ' Is not Valid';
                return $out;
            }
        }
        if (!empty($data['cc'])) {
            $ccarray = explode(',', $data['cc']);
            foreach ($ccarray as $row) {
                if (!valid_email_address(trim($row))) {
                    $out['msg'] = 'BCC Email Address ' . $row . ' Is not Valid';
                    return $out;
                }
            }
        }
        $docdata=$this->prepare_invoicedoc($leadorder, $user_id);
        if ($docdata['result']==$this->error_result) {
            $out['msg']=$docdata['msg'];
            return $out;
        }
        $invdoc=$docdata['doc_path'];

        // Send message
        $this->load->library('email');
        $config = $this->config->item('email_setup');
        $config['mailtype'] = 'text';
        $this->email->initialize($config);
        if ($config['protocol']=='smtp') {
            $this->email->from($config['smtp_user']);
        } else {
            $this->email->from($data['from']);
        }

        $this->email->to($data['customer']);
        if ($data['cc'] != '') {
            $cc = $data['cc'];
            $this->email->cc($cc);
        }
        $this->email->subject($data['subject']);


        $this->email->message($data['message']);
        $this->email->attach($invdoc);

        $mailres=$this->email->send();

        $this->email->clear(TRUE);

        usersession($ordersession, $leadorder);

        $out['result'] = $this->success_result;

        return $out;
    }

    public function _leadorder_shipping_status($order_id) {
        $order_status='FF - To Ship ';
        $order_status_class='fulfillment';
        $this->db->select('o.shipdate as ord_ship, s.order_shipping_id, s.shipdate');
        $this->db->from('ts_orders o');
        $this->db->join('ts_order_shippings s','s.order_id=o.order_id','left');
        $this->db->where('o.order_id', $order_id);
        $res=$this->db->get()->row_array();
        if (intval($res['shipdate'])==0) {
            $shipdate=$res['ord_ship'];
        } else {
            $shipdate=$res['shipdate'];
        }
        if (intval($res['order_shipping_id'])==0) {
            // Return shipdate from order;
            $order_status='FF - To Ship '.date('m/d/y',$shipdate);
        } else {
            $order_status='To Ship '.date('m/d/y',$shipdate);
            if ($shipdate<time()) {
                $order_status_class='late';
            }
            // Select total # of packages
            $this->db->select('count(p.order_shippack_id) as cnt');
            $this->db->from('ts_order_shippacks p');
            $this->db->join('ts_order_shipaddres s','s.order_shipaddr_id=p.order_shipaddr_id');
            $this->db->where('s.order_id', $order_id);
            $cntres=$this->db->get()->row_array();
            $totalpacks=$cntres['cnt'];
            // Get Data about track codes
            $this->db->select('count(p.order_shippack_id) as cnt, max(s.arrive_date) as delivdate, max(p.send_date), max(p.track_date)');
            $this->db->from('ts_order_shippacks p');
            $this->db->join('ts_order_shipaddres s','s.order_shipaddr_id=p.order_shipaddr_id');
            $this->db->where('s.order_id', $order_id);
            $this->db->where("coalesce(p.track_code,'') != ","''",FALSE);
            $chkres=$this->db->get()->row_array();
            // Select data
            if ($chkres['cnt']>0) {
                if ($chkres['cnt']==$totalpacks) {
                    $order_status='Shipped '.date('m/d/y', $shipdate);
                    $order_status_class='done';
                    // Select Delivered
                    $this->db->select('count(p.order_shippack_id) as cnt');
                    $this->db->from('ts_order_shippacks p');
                    $this->db->join('ts_order_shipaddres s','s.order_shipaddr_id=p.order_shipaddr_id');
                    $this->db->where('s.order_id', $order_id);
                    $this->db->where('p.delivered',1);
                    $delivres=$this->db->get()->row_array();
                    if ($delivres['cnt']>0 && $delivres['cnt']==$totalpacks) {
                        $order_status='Delivered';
                        $order_status_class='delivered';
                    }
                } else {
                    $order_status='Partially Shipped';
                }
            }
        }
        return array(
            'order_status'=>$order_status,
            'order_status_class'=>$order_status_class,
        );
    }

    // Status for shipping area
    public function _leadorderview_shipping_status($leadorder) {
        $order_status='FF - To Ship ';
        $order_status_class='open';
        $order=$leadorder['order'];
        $shipping=$leadorder['shipping'];
        if (!empty($shipping) && !empty($shipping['shipdate'])) {
            $shipdate=$shipping['shipdate'];
        } else {
            $shipdate=$order['shipdate'];
        }
        if (intval($shipdate)==0) {
            $order_status='Not Shipped Yet';
        } else {
            $order_status='To Ship '.date('m/d/y', $shipdate);
            if (isset($leadorder['shipping_address'])) {
                $shipping_address=$leadorder['shipping_address'];
                $delivflag=0;
                $delivdate=0;
                $numpack=0;
                $allpack=0;
                foreach ($shipping_address as $arow) {
                    $packs=$arow['packages'];
                    foreach ($packs as $prow) {
                        if ($prow['delflag']==0) {
                            $allpack++;
                            if (!empty($prow['track_code'])) {
                                $numpack++;
                                if ($prow['delivered']==1) {
                                    $delivflag++;
                                } else {
                                    $delivdate=($arow['arrive_date']>$delivdate ? $arow['arrive_date'] : $delivdate );
                                }
                            }
                        }
                    }
                }
                if ($allpack>0) {
                    if ($numpack>0) {
                        if ($allpack==$numpack) {
                            $order_status='Shipped '.date('m/d/y', $shipdate);
                            $order_status_class='shipped';
                        } else {
                            $order_status='Partially Shipped';
                            if ($delivflag>0 && $delivflag==$numpack) {
                                $order_status='Delivered';
                                $order_status_class='ready';
                            }
                        }
                    }
                }
            }
        }
        return array(
            'order_status'=>$order_status,
            'order_status_class'=>$order_status_class,
        );
    }


    private function _save_shipdtelog($order_id,  $user_id, $order_num, $oldshipdate, $newshipdate) {
        $this->db->set('order_id', $order_id);
        $this->db->set('user_id', $user_id);
        $this->db->set('order_num', $order_num);
        $this->db->set('old_shipdate', $oldshipdate);
        $this->db->set('new_shipdate', $newshipdate);
        $this->db->insert('ts_order_shipdatelog');
        return TRUE;
    }

    private function _save_order_paymentlog($order_id, $user_id, $msg, $ccdetails=array(), $succes=0) {
        if (!empty($ccdetails)) {
            $this->db->set('paysum', floatval($ccdetails['amount']));
            $this->db->set('card_num', $ccdetails['cardnum']);
            $this->db->set('card_system', $ccdetails['cardtype']);
            $this->db->set('cvv', (empty($ccdetails['cardcode']) ? 0 : 1));
        }
        $this->db->set('order_id', $order_id);
        $this->db->set('user_id', $user_id);
        $this->db->set('paysucces', $succes);
        $this->db->set('api_response', $msg);
        $this->db->insert('ts_order_paymentlog');
        return TRUE;
    }

    public function get_templates($leadorder) {
        $out=array('result'=> $this->error_result, 'msg'=> 'Item Not Select');
        $items=$leadorder['order_items'];
        $outfiles=array();
        $custom=0;
        $dbtablename='sb_items';
        foreach ($items as $row) {
            if ($row['item_id']==$this->config->item('custom_id')) {
                $custom=1;
            } else {
                $this->db->select("item_id, item_number, item_name, item_vector_img");
                $this->db->from($dbtablename);
                $this->db->where('item_id', $row['item_id']);
                $this->db->where('item_vector_img is not null');
                $res=$this->db->get()->row_array();
                if (!empty($res['item_vector_img'])) {
                    $outfiles[]=array(
                        'filename'=>$res['item_name'],
                        'fileurl'=>$res['item_vector_img'],
                    );
                }

            }
        }
        if (count($outfiles)==0 && $custom==1) {
            $this->db->select("item_id, item_number, item_name, item_vector_img");
            $this->db->from($dbtablename);
            $this->db->where('item_vector_img is not null');
            $res=$this->db->get()->result_array();
            $out['templates']=$res;
        } else {
            $out['templates']=$outfiles;
        }
        $out['custom']=$custom;
        $out['result']=$this->success_result;
        return $out;
    }

    private function _prepare_netexport($artsync, $artsyncdoc) {
        // Main Data
        $this->db->set('user_id', $artsync['user_id']);
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

        // Add documents
        foreach ($artsyncdoc as $row) {
            $this->db->set('user_id', $row['user_id']);
            $this->db->set('order_id', $row['order_id']);
            if (!empty($row['artwork_proof_id'])) {
                $this->db->set('artwork_proof_id', $row['artwork_proof_id']);
            }
            $this->db->set('operation', $row['operation']);
            if (!empty($row['proofdoc_link'])) {
                $this->db->set('proofdoc_link', $row['proofdoc_link']);
            }
            $this->db->insert('ts_artdoc_sync');
        }
        return TRUE;
    }

    // Count # of Order charges attempts
    public function count_charges_attempts($order_id) {
        $this->db->select('count(*) as cnt');
        $this->db->from('ts_order_paymentlog');
        $this->db->where('order_id', $order_id);
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }
    // Get Charges Attempts
    public function get_charges_attempts($order_id) {
        $out=array('result'=> $this->error_result, 'msg'=> 'Attempts Not Found');
        $this->db->select('l.paylog_date, u.user_name, l.paysum, l.card_num, l.card_system, l.cvv, l.paysucces, l.api_response');
        $this->db->from('ts_order_paymentlog l');
        $this->db->join('users u','u.user_id=l.user_id','left');
        $this->db->where('l.order_id', $order_id);
        $this->db->order_by('l.paylog_date');
        $res=$this->db->get()->result_array();
        if (count($res)>0) {
            $out['result']=  $this->success_result;
            $data=array();
            foreach ($res as $row) {
                $paylog_date=date('m/d/y g:i a', strtotime($row['paylog_date']));
                $paysum=  MoneyOutput($row['paysum'],2);
                $card_num='N/A';
                if (!empty($row['card_num'])) {
                    $card_num=creditcard_format($row['card_num']);
                }
                $payclass=($row['paysucces']==1 ? 'success' : 'error');
                $data[]=array(
                    'paylog_date'=>$paylog_date,
                    'user_name'=>$row['user_name'],
                    'paysum'=>$paysum,
                    'card_num'=>$card_num,
                    'card_system'=>$row['card_system'],
                    'cvv'=>$row['cvv'],
                    'payclass'=>$payclass,
                    'api_response'=>$row['api_response'],
                );
            }
            $out['data']=$data;
        }
        return $out;
    }

    public function get_leadorder_amounts($order_id) {
        $this->db->select('oa.amount_date, oa.printshop, v.vendor_name, oa.amount_sum');
        $this->db->from('ts_order_amounts oa');
        $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
        $this->db->where('oa.order_id', $order_id);
        $this->db->order_by('oa.amount_date');
        $res=$this->db->get()->result_array();
        return $res;
    }

    private function _emptyzip_notification($leadorder, $user_id) {
        $this->load->library('email');
        $this->db->select('user_name');
        $this->db->from('users');
        $this->db->where('user_id', $user_id);
        $usrres=$this->db->get()->row_array();
        $user_name=$usrres['user_name'];
        $config['protocol'] = 'sendmail';
        $config['charset'] = 'utf8';
        $config['wordwrap'] = TRUE;
        $config['mailtype'] = 'text';
        $this->email->initialize($config);
        $this->email->from($this->config->item('email_notification_sender'));
        $this->email->to($this->config->item('sean_email'));
        $subject='Empty Zip code - Order #'.$leadorder['order']['order_num'];
        $this->email->subject($subject);
        $message='Hi Sean,'.PHP_EOL;
        $message.=PHP_EOL;
        $message.='Order #'.$leadorder['order']['order_num'].', shipping cost '.  MoneyOutput($leadorder['order']['shipping']).' was placed with empty Zip code by '.$user_name.PHP_EOL;
        $message.='Shipping Address:'.PHP_EOL;
        $shipaddress=$leadorder['shipping_address'];
        foreach ($shipaddress as $adrrow) {
            if (!empty($adrrow['ship_contact'])) {
                $message.='Contact Name : '.$adrrow['ship_contact'].PHP_EOL;
            }
            if (!empty($adrrow['ship_company'])) {
                $message.='Shipping Company : '.$adrrow['ship_company'].PHP_EOL;
            }
            $message.='Address : '.PHP_EOL;
            if (!empty($adrrow['ship_address1'])) {
                $message.=$adrrow['ship_address1'].' ';
            }
            if (!empty($adrrow['ship_address2'])) {
                $message.=$adrrow['ship_address2'];
            }
            $message.=PHP_EOL;
            if (!empty($adrrow['city'])) {
                $message.='City :'.$adrrow['city'].PHP_EOL;
            }
            if (!empty($adrrow['out_country'])) {
                $message.='Country : '.$adrrow['out_country'].PHP_EOL;
            }
            if (!empty($adrrow['out_zip'])) {
                $message.='State : '.$adrrow['out_zip'].PHP_EOL;
            }
        }
        $this->email->message($message);
        $this->email->send();
        $this->email->clear();
    }

    public function check_neworder_payment($leadorder) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message, 'fin'=>0);
        $charges=$leadorder['charges'];
        $order_data=$leadorder['order'];
        $order_revenue=$order_data['revenue'];
        // Calc amount of charges
        $chargesum=0;
        $chargenum=0;
        foreach ($charges as $chrow) {
            if ($chrow['delflag']==0 && $chrow['autopay']==1) {
                $chargesum+=$chrow['amount'];
                $chargenum+=1;
            }
        }
        if ($chargesum==0) {
            $out['result']=$this->success_result;
        } else {
            if ($chargesum==$order_revenue) {
                $out['result']=$this->success_result;
            } else {
                if ($chargenum==1) {
                    $out['msg']='Payment amount does not match the order revenue. Change to <b>'.MoneyOutput($order_revenue).'</b>?';
                    $out['fin']=1;
                } else {
                    $out['result']=$this->success_result;
                }
            }
        }
        return $out;
    }

    public function change_neworder_payment($leadorder, $session_id) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $charges=$leadorder['charges'];
        $order_data=$leadorder['order'];
        $order_revenue=$order_data['revenue'];
        // Calc amount of charges
        $chargidx=0;
        $found=0;
        foreach ($charges as $chrow) {
            if ($chrow['delflag']==0 && $chrow['autopay']==1) {
                $charges[$chargidx]['amount']=$order_revenue;
                $found=1;
            }
            $chargidx++;
            if ($found==1) {
                break;
            }
        }
        if ($found==1) {
            $leadorder['charges']=$charges;
            usersession($session_id, $leadorder);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    private function _dublicate_order_totals($neworder,$contacts,$neworder_items, $newartw,$newshipping,$newshipaddr,$newbilling,$message,$countries,$newcharge,$artlocations) {
        $total_item=0;
        // $total_qty=0;
        $total_qty=$neworder['order_qty'];
        $total_imprint=0;
        $itemidx=0;
        foreach ($neworder_items as $row) {
            $imprints=$row['imprints'];
            $itmimprint=0;
            foreach ($imprints as $irow) {
                $itmimprint+=round($irow['imprint_qty']*$irow['imprint_price'],2);
            }
            $neworder_items[$itemidx]['imprint_subtotal']=$itmimprint;
            $total_item+=$row['item_subtotal'];
            $total_imprint+=$neworder_items[$itemidx]['imprint_subtotal'];
            $itemidx++;
        }
        $neworder['item_imprint']=$total_imprint;
        $neworder['item_cost']=$total_item;
        $shpidx=0;
        $base_cost=floatval($neworder['item_cost'])+floatval($neworder['item_imprint'])+floatval($neworder['shipping']);
        $base_cost+=floatval($newshipping['rush_price']);
        $base_cost+=floatval($neworder['mischrg_val1'])+floatval($neworder['mischrg_val2'])-floatval($neworder['discount_val']);
        $tax=0;
        $shipadrcnt=count($newshipaddr);

        foreach ($newshipaddr as $row) {
            if ($row['taxcalc']==0) {
                $newshipaddr[$shpidx]['sales_tax']=0;
            } else {
                $taxpercent=$this->config->item('salestax');
                if ($neworder['order_date']>=$this->config->item('datenewtax')) {
                    $taxpercent=$this->config->item('salesnewtax');
                }
                if ($shipadrcnt==1) {
                    $adrtax=round($base_cost*$taxpercent/100,2); // $taxpercent
                    $newshipaddr[$shpidx]['sales_tax']=$adrtax;
                    $tax+=$adrtax;
                } else {
                    // $adrtax=round($base_cost/$total_qty*$row['item_qty']*$this->config->item('salestax')/100,2);
                    $adrtax=round($base_cost/$total_qty*$row['item_qty']*$taxpercent/100,2);
                    $newshipaddr[$shpidx]['sales_tax']=$adrtax;
                    $tax+=$adrtax;
                }
            }
            $shpidx++;
        }
        $neworder['tax']=$tax;
        $revenue=$base_cost+floatval($neworder['tax']);
        $neworder['revenue']=$revenue;
        $profit=round($revenue*$this->config->item('default_profit')/100,2);
        $neworder['profit']=$profit;
        if (count($newcharge)>0) {
            $newcharge[0]['amount']=$revenue;
        }
        return array(
            'order'=>$neworder,
            'contacts'=>$contacts,
            'order_items'=>$neworder_items,
            'artwork'=>$newartw,
            'shipping'=>$newshipping,
            'shipping_address'=>$newshipaddr,
            'order_billing'=>$newbilling,
            'message'=>$message,
            'countries'=>$countries,
            'charges'=>$newcharge,
            'artlocations'=>$artlocations,
        );
    }

    private function _check_contact_info($contacts) {
        $out= $this->error_result;
        $chkname=0;
        $chkemail=0;
        $chkphone=0;
        foreach ($contacts as $row) {
            if (!empty($row['contact_name'])) {
                $chkname=1;
                if (!empty($row['contact_emal']) && valid_email_address($row['contact_emal'])) {
                    $chkemail=1;
                }
                if (!empty($row['contact_phone'])) {
                    $chkphone=1;
                }
            }
        }
        if ($chkname==1 && $chkemail==1) { // && $chkphone==1) {
            $out=$this->success_result;
        }
        return $out;
    }

    public function save_trackcode($order_num, $trackcode) {
        $out=array('result'=>$this->error_result, 'msg'=>'Order Not Found');
        $this->db->select('order_id, order_num, is_canceled, order_system');
        $this->db->from('ts_orders');
        $this->db->where('order_num', $order_num);
        $res=$this->db->get()->row_array();
        if (isset($res['order_id'])) {
            $out['msg']='Order was Canceled';
            if ($res['is_canceled']==0) {
                $order_id=$res['order_id'];
                $out['msg']='Empty Shipping Address';
                // Get Shipping Address and Track Packages
                $this->db->select('p.*');
                $this->db->from('ts_order_shipaddres s');
                $this->db->join('ts_order_shippacks p','p.order_shipaddr_id=s.order_shipaddr_id');
                $this->db->where('s.order_id',$order_id);
                $packres=$this->db->get()->result_array();
                if (count($packres)>0) {
                    $out['msg']='Track Code Entered';
                    $found=0;
                    foreach ($packres as $prow) {
                        if ($prow['track_code']==$trackcode) {
                            $found=1;
                            break;
                        }
                    }
                    if ($found==0) {
                        $shpadr=0;
                        foreach ($packres as $prow) {
                            if (empty($prow['track_code'])) {
                                // We find Empty Track Code
                                $this->db->set('track_code', $trackcode);
                                $this->db->where('order_shippack_id', $prow['order_shippack_id']);
                                $this->db->update('ts_order_shippacks');
                                $out['msg']='';
                                $out['result']=$this->success_result;
                                $found=1;
                                break;
                            } else {
                                $shpadr=$prow['order_shipaddr_id'];
                            }
                        }
                        if ($found==0) {
                            $this->db->set('order_shipaddr_id', $shpadr);
                            $this->db->set('track_code', $trackcode);
                            $this->db->insert('ts_order_shippacks');
                            $out['msg']='';
                            $out['result']=$this->success_result;
                        }
                    }
                }
            }
        }
        return $out;
    }

    private function _invoice_pdfdoc_create($options, $file_out) {
        define('FPDF_FONTPATH', FCPATH.'font');
        $this->load->library('fpdf/fpdfeps');
        // Prepare
        $logoFile = FCPATH."/img/invoice/logos-2.eps";
        $logoXPos = 5;
        $logoYPos = 10;
        $logoWidth = 105.655;
        $logoHeight = 12.855;
        $logoType = 'JPG';

        $invnumImg = FCPATH.'/img/invoice/invoice_num.eps';
        $invnumXPos = 120;
        $invnumYPos = 10;
        $invnumWidth = 0;
        $invnumHeigth = 16.5;

        $dateImage = FCPATH.'/img/invoice/date_bg-3.eps';
        $dateXPos = 158;
        $dateYPos = 28.7;
        $dateWidth = 0;
        $dateHeight = 9;

        $ponumImage = FCPATH.'/img/invoice/customer_code_bg.eps';
        $ponumXPos = 90;
        $ponumYPos = 28.7;
        $ponumWidth = 0;
        $ponumHeight = 9;


        $invoiceimgHeadType = 'PNG';
        $invoiceimgHeadHeight = 8.5;

        $termsImage = FCPATH.'/img/invoice/terms_head_bg.eps';
        $termsXPos = 5;
        $termsYPos = 52;
        $termsWidth = 0;

        $paydueImage = FCPATH.'/img/invoice/paymentdue_head_bg.eps';
        $paydueXPos = 53.5;
        $paydueYPos = 52;
        $paydueWidth = 0;

        $shipdateImage = FCPATH.'/img/invoice/shipdate_head_bg.eps';
        $shipdateXPos = 112;
        $shipdateYPos = 52;
        $shipdateWidth = 0;

        $arivdateImage = FCPATH.'/img/invoice/deliverydate_head_bg.eps';
        $arivdateXPos = 160;
        $arivdateYPos = 52;
        $arivdateWidth = 0;

        $billadrImage = FCPATH.'/img/invoice/billto_head_bg.eps';
        $billadrXPos = 5;
        $billadrYPos = 71;
        $billadrWidth = 0;

        $shipadrImage = FCPATH.'/img/invoice/shipto_head_bg.eps';
        $shipadrXPos = 112;
        $shipadrYPos = 71;
        $shipadrWidth = 0;

        $pdf = new FPDFEPS('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Times','',9.035143);
        $pdf->SetTextColor(65, 65, 65);
        // $pdf->SetMargins(14,14,14);
        // Logo
        $pdf->ImageEps( $logoFile, $logoXPos, $logoYPos, $logoWidth, $logoHeight );
        // Inv #
        $pdf->ImageEps($invnumImg, $invnumXPos, $invnumYPos, $invnumWidth, $invnumHeigth);
        $pdf->SetXY(167, 10.8);
        $pdf->SetFont('','B',16.564429);
        $pdf->SetTextColor(0, 0, 255);
        $pdf->Cell(35.8,16,$options['order_num'],0,0,'C');

        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('','',12.046857);
        $pdf->Text(5, 27.88, '855 Bloomfield Ave');
        $pdf->Text(5, 33.88, 'Clifton, NJ 07012');
        $pdf->Text(5,39.88, 'Call Us at');
        $pdf->SetTextColor(0,0,255);
        $pdf->Text(23,39.88, '1-800-790-6090');
        $pdf->Text(5,45.88,'www.bluetrack.com'); // , 'http://www.bluetrack.com');
        $pdf->SetTextColor(65, 65, 65);
        $pdf->ImageEps($dateImage, $dateXPos, $dateYPos, $dateWidth, $dateHeight);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('', '', 13.552714);
        $pdf->SetXY(177, 29);
        $pdf->Cell(27, 8, $options['order_date'],0,0, 'C');
        // $pdf->Text(179.8, 35.88, $options['order_date']);
        if (!empty($options['customer_code'])) {
            $pdf->ImageEps($ponumImage, $ponumXPos, $ponumYPos, $ponumWidth, $ponumHeight);
            $pdf->SetXY(127,29);
            $pdf->SetFont('','B');
            $pdf->Cell(27,8,$options['customer_code'],0,0,'C');
        }
        $pdf->SetFont('','', 12.046857);
        $pdf->SetTextColor(65, 65, 65);
        // Terms
        $pdf->ImageEps($termsImage, $termsXPos, $termsYPos, $termsWidth, $invoiceimgHeadHeight);
        $pdf->SetXY(5, 61);
        if (!empty($options['terms'])) {
            // $pdf->Ceil(26,73, $options['terms']);
            $pdf->Cell(44,8,$options['terms'],0,0,'C');
        }
        // Payment Due
        $pdf->ImageEps($paydueImage, $paydueXPos, $paydueYPos, $paydueWidth, $invoiceimgHeadHeight);
        $pdf->SetX(53.5);
        $pdf->Cell(44, 8, $options['payment_due'],0,0,'C');
        // Ship Date
        $pdf->ImageEps($shipdateImage, $shipdateXPos, $shipdateYPos, $shipdateWidth, $invoiceimgHeadHeight);
        $pdf->SetX(112);
        $pdf->Cell(44,8, $options['shipdate'],0,0,'C');
        // Delivery Date
        $pdf->ImageEps($arivdateImage, $arivdateXPos, $arivdateYPos, $arivdateWidth, $invoiceimgHeadHeight, $invoiceimgHeadType);
        $pdf->SetX(159);
        $pdf->Cell(44,8, $options['arrive'],0,0,'C');
        // Billing Address
        $pdf->SetFont('','', 12.046857);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->ImageEps($billadrImage, $billadrXPos, $billadrYPos, $billadrWidth, $invoiceimgHeadHeight);
        $pdf->SetXY(7, 80.8);
        // $startY = 88;
        foreach ($options['billing'] as $biladrrow) {
            // $pdf->Text(17, $startY, $biladrrow);
            // $startY+=5;
            $pdf->SetX(7);
            $pdf->Cell(91,5.5, $biladrrow,0,1,'L');
        }
        // Shipping Address
        $pdf->ImageEps($shipadrImage, $shipadrXPos, $shipadrYPos, $shipadrWidth, $invoiceimgHeadHeight);
        $pdf->SetXY(112, 80.8);

        foreach ($options['shipping'] as $shipadrrow) {
            $pdf->SetX(112);
            $pdf->Cell(91,5.5, $shipadrrow,0,1,'L');
            // $pdf->Text(125, $startY, $shipadrrow);
            // $startY+=5;
        }
        // Table
        $tableHeadYPos = 110;
        $itemnumImage = FCPATH.'/img/invoice/itemnum_head_bg.eps';
        $itemnumXPos = 5;
        $itemnumWidth = 0;

        $descripImage = FCPATH.'/img/invoice/itemdescript_head_bg.eps';
        $descripXPos = 38;
        $descripWidth = 0;

        $itemqtyImage = FCPATH.'/img/invoice/itemqty_head_bg2.eps';
        $itemqtyXPos = 126;
        $itemqtyWidth = 0;

        $priceImage = FCPATH.'/img/invoice/priceeach_head_bg-2.eps';
        $priceXPos = 146;
        $priceWidth = 0;

        $totalImage = FCPATH.'/img/invoice/subtotal_head_bg.eps';
        $totalXPos = 175;
        $totalWidth = 0;

        $pdf->ImageEps($itemnumImage, $itemnumXPos, $tableHeadYPos, $itemnumWidth, $invoiceimgHeadHeight);
        $pdf->ImageEps($descripImage, $descripXPos, $tableHeadYPos, $descripWidth, $invoiceimgHeadHeight);
        $pdf->ImageEps($itemqtyImage, $itemqtyXPos, $tableHeadYPos, $itemqtyWidth, $invoiceimgHeadHeight);
        $pdf->ImageEps($priceImage, $priceXPos, $tableHeadYPos, $priceWidth, $invoiceimgHeadHeight);
        $pdf->ImageEps($totalImage, $totalXPos, $tableHeadYPos, $totalWidth, $invoiceimgHeadHeight);
        // Table Data
        $tableWidths = [
            33,
            88,
            18,
            29,
            32.5,
        ];
        $numpp = 1;
        $pdf->SetFillColor(225, 225, 225);
        $pdf->SetXY(0, 118.7);
        foreach ($options['details'] as $detail) {
            $fillcell = ($numpp%2==1 ? true:  false);
            if ($detail['item_color']=='#ff0000') {
                $pdf->SetTextColor(255,0,0);
            } else {
                $pdf->SetTextColor(0,0,0);
            }
            $pdf->SetX(5);
            $pdf->Cell($tableWidths[0], 9, $detail['item_num'], 0, 0,'C', $fillcell);
            $pdf->Cell($tableWidths[1], 9, $detail['item_description'],0,0,'L',$fillcell);
            $pdf->Cell($tableWidths[2], 9, $detail['item_qty']==0 ? '' : QTYOutput($detail['item_qty']),0, 0, 'C', $fillcell);
            $pdf->Cell($tableWidths[3], 9, $detail['item_price'],0,0,'C', $fillcell);
            $pdf->Cell($tableWidths[4], 9, $detail['item_subtotal'],0, 1,'C', $fillcell);
            $numpp++;
        }
        if (!empty($options['invoice_message'])) {
            $pdf->SetXY(5,228);
            $pdf->SetFont('','',13);
            // $pdf->Cell(105, 0, $options['invoice_message'],1);
            $pdf->MultiCell(100, 6, $options['invoice_message'], 1, 'L', FALSE);

        }
        // Totals
        $totalbgn = $pdf->GetY();
        $invtotalXPos = 90;
        $invtotalYPos = $totalbgn+5;
        $invtotalWidth = 115;
        $invtotalHeght = 26 + 8*$options['payments_count'];
        $pdf->Rect($invtotalXPos, $invtotalYPos, $invtotalWidth, $invtotalHeght);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(91,$totalbgn+5.5);
        $pdf->SetFont('','',13);
        // $pdf->Cell(75, 8, 'NJ '.$options['tax_term'].'% Sales Tax '.$options['tax'],0,1);
        $pdf->Cell(77, 8, 'NJ '.$options['tax_term'].'% Sales Tax ',0, 0);
        $pdf->Cell(35.9, 8, $options['tax'],0,1);

        $pdf->SetX(91);
        $pdf->SetFont('','B');
        $pdf->Cell(77, 8, 'Total',0, 0);
        $pdf->SetTextColor(8,0,255);
        $pdf->Cell(35.9, 8, $options['total'],0,1);

        if ($options['payments_count'] > 0) {
            foreach ($options['payments_detail'] as $payments_detail) {
                $pdf->SetX(90.5);
                $pdf->SetTextColor(0,0,0);
                $pdf->SetFont('','B');
                $pdf->Cell(77.4, 8, $payments_detail['label'],0,0,'L',true);
                if ($payments_detail['type']=='refund') {
                    $pdf->Cell(35.9, 8,'+'.$payments_detail['value'],0,1,'L',true);
                } else {
                    $pdf->SetTextColor(255,0,0);
                    $pdf->Cell(35.9, 8,'-'.$payments_detail['value'],0,1,'L',true);
                    $pdf->SetTextColor(0,0,0);
                }
            }
        }
        $pdf->SetX(90.5);
        $pdf->SetFont('','B');
        $pdf->Cell(77,8,'Balance Due',0,0);
        $pdf->SetTextColor(0,0,255);
        $pdf->Cell(35.9,8,$options['balance'],0,1);
        // Save file
        $pdf->Output('F', $file_out);
        return TRUE;

    }

}
/* End of file leadorder_model.php */
/* Location: ./application/models/leadorder_model.php */

