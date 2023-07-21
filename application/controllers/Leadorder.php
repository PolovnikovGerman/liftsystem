<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadorder extends MY_Controller
{

    private $restore_orderdata_error='Edit Connection Lost. Please, recall form';
    private $locktimeout='You have been timed out from this page due to inactivity';
    private $profitproject='proj';
    protected $TRACK_TEMPLATE='track_message';

    protected $NO_ART_REMINDER='Need Art Reminder';
    protected $ART_PROOF='Art Proof';
    protected $NEED_APPROVE_REMINDER='Need Approval Reminder';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('leadorder_model');
        $this->load->model('engaded_model');
    }

    public function index()
    {
    }

    public function leadorder_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $order=(isset($postdata['order']) ? $postdata['order'] : 0);
            $brand = ifset($postdata,'brand','ALL');
            $ordersession=$this->input->post('ordersession');
            // Remove from session
            usersession($ordersession,NULL);
            if (ifset($postdata,'locrecid',0)>0) {
                $this->engaded_model->clean_engade($postdata['locrecid']);
            }
            if ($order==0) {
                $res=$this->leadorder_model->add_newlead_order($this->USR_ID, $brand);
                $edit=1;
            } else {
                $res=$this->leadorder_model->get_leadorder($order, $this->USR_ID, $brand);
                $edit=(isset($postdata['edit']) ? $postdata['edit'] : 0);
            }
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $leadsession='leadorder'.uniq_link(15);
                // Generate new session
                $options=array();
                $options['current_page']=ifset($postdata,'page', 'art_tasks');
                $options['leadsession']=$leadsession;
                $orddata=$res['order'];
                if ($order==0) {
                    $options['order_id']=0;
                    $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                    $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, 1);
                    $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                    $options['order_data']=$order_data;
                    $options['leadsession']=$leadsession;
                    $content=$this->load->view('leadorderdetails/placeorder_menu_edit',$options, TRUE);
                    // $mdata['content']=$content;
                    $head_options = [
                        'order_head' => $this->load->view('leadorderdetails/head_placeorder_edit', $orddata, TRUE),
                        'prvorder' => 0,
                        'nxtorder' => 0,
                        'order_id' => 0,
                    ];
                    $header = $this->load->view('leadorderdetails/head_edit', $head_options, TRUE);
                    $locking='';
                } else {
                    if ($edit==0) {
                        // Check Lock data
                        if (isset($postdata['locrecid'])) {
                            $this->engaded_model->clean_engade($postdata['locrecid']);
                        }
                        // Get Data about Engaded records
                        $engade_res=$this->engaded_model->check_engade(array('entity'=>'ts_orders','entity_id'=>$order));
                        $res['unlocked']=$engade_res['result'];
                        // Build Head
                        $head_options = [
                            'order_head' => $this->load->view('leadorderdetails/head_order_view', $orddata,TRUE),
                            'prvorder' => $res['prvorder'],
                            'nxtorder' => $res['nxtorder'],
                            'order_id' => $orddata['order_id'],
                        ];
                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res,$this->USR_ID, $this->USR_ROLE, 0);
                        $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                        // Build Content
                        $head_options['unlocked']=$engade_res['result'];
                        if ($engade_res['result']==$this->error_result) {
                            $voptions=array(
                                'user'=>$engade_res['lockusr'],
                            );
                            // $options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                            $head_options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                        } elseif ($orddata['is_canceled']==1) {
                            $head_options['unlocked']=$this->error_result;
                            $head_options['editbtnview']=$this->load->view('leadorderdetails/ordercanceled_view', array(), TRUE);
                        }
                        $options['order_data']=$order_data;
                        $head_options['order_dublcnum']=$orddata['order_num'];
                        $header = $this->load->view('leadorderdetails/head_view', $head_options, TRUE);
                        $options['order_system']=$res['order_system_type'];
                        $content=$this->load->view('leadorderdetails/top_menu_view',$options, TRUE);
                        $locking='';
                    } else {
                        // Lock record
                        $lockoptions=array(
                            'entity'=>'ts_orders',
                            'entity_id'=>$order,
                            'user_id'=>$this->USR_ID,
                        );
                        // Check lock of record
                        $chklock=$this->engaded_model->check_engade($lockoptions);
                        if ($chklock==$this->error_result) {
                            $error='Order was locked for edit by other user. Try later';
                            $this->ajaxResponse($mdata, $error);
                        }
                        $locking=$this->engaded_model->lockentityrec($lockoptions);
                        $res['locrecid']=$locking;
                        // Build Head
                        $head_options = [
                            'order_head' => $this->load->view('leadorderdetails/head_order_edit', $orddata,TRUE),
                            'prvorder' => $res['prvorder'],
                            'nxtorder' => $res['nxtorder'],
                        ];
                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res,$this->USR_ID, $this->USR_ROLE, $edit);

                        $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                        // Build Content
                        $options['order_data']=$order_data;
                        $options['locrecid']=$locking;
                        if ($this->input->ip_address()=='127.0.0.1') {
                            $options['timeout']=(time()+$this->config->item('loctimeout_local'))*1000;
                        } else {
                            $options['timeout']=(time()+$this->config->item('loctimeout'))*1000;
                        }
                        $options['current_page']=(isset($postdata['page']) ? $postdata['page'] : 'art_tasks');
                        $content=$this->load->view('leadorderdetails/top_menu_edit',$options, TRUE);
                        // $head_options['order_dublcnum']=$orddata['order_num'];
                        $header = $this->load->view('leadorderdetails/head_edit', $head_options, TRUE);
                    }
                }
                /* Save to session */
                if ($res['order_system_type']=='old') {
                    $leadorder=array(
                        'order'=>$orddata,
                        'payments'=>$res['payments'],
                        'artwork'=>$res['artwork'],
                        'artlocations'=>$res['artlocations'],
                        'artproofs'=>$res['proofdocs'],
                        'message'=>$res['message'],
                        'order_system'=>$res['order_system_type'],
                        'locrecid'=>$locking,
                    );
                } else {
                    $leadorder=array(
                        'order'=>$orddata,
                        'payments'=>$res['payments'],
                        'artwork'=>$res['artwork'],
                        'artlocations'=>$res['artlocations'],
                        'artproofs'=>$res['proofdocs'],
                        'message'=>$res['message'],
                        'contacts'=>$res['contacts'],
                        'order_items'=>$res['order_items'],
                        'order_system'=>$res['order_system_type'],
                        'shipping'=>$res['shipping'],
                        'shipping_address'=>$res['shipping_address'],
                        'billing'=>$res['order_billing'],
                        'charges'=>$res['charges'],
                        'claydocs' => $res['claydocs'],
                        'previewdocs' => $res['previewdocs'],
                        'delrecords'=>array(),
                        'locrecid'=>$locking,
                    );
                }
                usersession($leadsession, $leadorder);
                $mdata['content']=$content;
                $mdata['header']=$header;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function leadordernavigate() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $order_id = ifset($postdata, 'order');
            $ordersession = ifset($postdata, 'ordersession','updsession');
            $brand = ifset($postdata,'brand','ALL');
            // Remove from session
            usersession($ordersession,NULL);
            // Generate new session
            $leadsession='leadorder'.uniq_link(15);
            $res=$this->leadorder_model->get_leadorder($order_id, $this->USR_ID, $brand);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $mdata['ordersession']=$leadsession;
                $orddata=$res['order'];
                // Get Data about Engaded records
                $this->load->model('engaded_model');
                $engade_res=$this->engaded_model->check_engade(array('entity'=>'ts_orders','entity_id'=>$order_id));
                $res['unlocked']=$engade_res['result'];
                // Build Head
                $mdata['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                $mdata['prvorder']=$res['prvorder'];
                $mdata['nxtorder']=$res['nxtorder'];
                $mdata['order_system']=$res['order_system_type'];
                $data=$this->template->_prepare_leadorder_view($res,$this->USR_ID, $this->USR_ROLE,0);
                /* Save to session */
                $leadorder=array(
                    'order'=>$orddata,
                    'payments'=>$res['payments'],
                    'artwork'=>$res['artwork'],
                    'artlocations'=>$res['artlocations'],
                    'artproofs'=>$res['proofdocs'],
                    'message'=>$res['message'],
                    'contacts'=>$res['contacts'],
                    'order_items'=>$res['order_items'],
                    'order_system'=>$res['order_system_type'],
                    'shipping'=>$res['shipping'],
                    'shipping_address'=>$res['shipping_address'],
                    'billing'=>$res['order_billing'],
                    'charges'=>$res['charges'],
                );
                usersession($leadsession, $leadorder);

                $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                // Build Content
                $mdata['content']=$order_data;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Check lock status of order
    public function checklockedorder() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                $curlock=$this->input->post('curlock');
                $order=$leadorder['order'];
                $order_id=$order['order_id'];
                $editbtn=$switchtempl='&nbsp;';
                if ($order_id<=0) {
                    $mdata['lockstatus']=$curlock;
                } else {
                    if ($order['is_canceled']) {
                        $editbtn=$this->load->view('leadorderdetails/ordercanceled_view', array(), TRUE);
                    } else {
                        $options=array(
                            'entity'=>'ts_orders',
                            'entity_id'=>$order_id,
                        );
                        $engadres=$this->engaded_model->check_engade($options);
                        $mdata['lockstatus']=$engadres['result'];
                        if ($engadres['result']!=$curlock) {
                            if ($engadres['result']==$this->success_result) {
                                $editbtn=$this->load->view('leadorderdetails/orderedit_btn_view', array(), TRUE);
                                $switchtempl=$this->load->view('leadorderdetails/order_systemselect_view', array(), TRUE);
                            } else {
                                // Lock view
                                $voptions=array(
                                    'user'=>$engadres['lockusr'],
                                );
                                $editbtn=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                            }
                        } else {
                            if ($engadres['result']==$this->error_result) {
                                // Lock view
                                $voptions=array(
                                    'user'=>$engadres['lockusr'],
                                );
                                $editbtn=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                            }
                        }
                    }
                }
                $mdata['editbutton']=$editbtn;
                $mdata['switchtemplate']=$switchtempl;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // View Item Image
    public function viewitemimage() {
        $item_id=$this->input->get('id');
        $res=$this->leadorder_model->get_leadorder_itemimage($item_id);
        if ($res['result']==$this->error_result) {
            die($res['msg']);
        }
        $viewopt=$res['viewoptions'];
        $content=$this->load->view('redraw/viewsource_view',$viewopt, TRUE);
        echo $content;
    }

    public function art_showtemplates() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);

            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                $res=$this->leadorder_model->get_templates($leadorder);

                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $templates=$res['templates'];
                    $mdata['custom']=$res['custom'];
                    if ($res['custom']==1) {
                        $templates=$res['templates'];
                        $templ_options=array();
                        if (count($templates)==0) {
                            $error='Empty list of Templates';
                        } else {
                            $templ_options['templates_list']=$this->load->view('artpage/templates_list_view',array('templates'=>$templates),TRUE);
                            $mdata['content']=$this->load->view('artpage/item_templates_view',$templ_options,TRUE);
                        }
                    } else {
                        if (count($templates)==0) {
                            $error='Empty list of Templates';
                        } else {
                            $mdata['templates']=$templates;
                        }

                    }
                }
                $this->ajaxResponse($mdata, $error);

            }
        }
    }

    // Duplicate
    public function leadorder_dublicate() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->dublicate_order($leadorder, $this->USR_ID);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options=array();
                    $orddata=$res['order'];
                    // Build Head                    
                    $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                    // Build View
                    $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE,1);

                    $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                    // Build Content
                    $options['order_data']=$order_data;
                    $options['order_confirmation']='Duplicate';
                    $options['leadsession']=$ordersession;
                    $options['current_page']=ifset($postdata,'current_page','orders');
                    $content=$this->load->view('leadorderdetails/placeorder_menu_edit',$options, TRUE);
                    $mdata['content']=$content;
                    $head_options = [
                        'order_head' => $this->load->view('leadorderdetails/head_placeorder_edit', $orddata, TRUE),
                        'prvorder' => 0,
                        'nxtorder' => 0,
                        'order_id' => 0,
                    ];
                    $mdata['header'] = $this->load->view('leadorderdetails/head_edit', $head_options, TRUE);
                    /* Save to session */
                    if ($res['order_system_type']=='old') {
                        $leadorder=array(
                            'order'=>$orddata,
                            'payments'=>$res['payments'],
                            'artwork'=>$res['artwork'],
                            'artlocations'=>$res['artlocations'],
                            'artproofs'=>$res['proofdocs'],
                            'message'=>$res['message'],
                            'order_system'=>$res['order_system_type'],
                            'locrecid'=>0,
                        );
                    } else {
                        $leadorder=array(
                            'order'=>$orddata,
                            'payments'=>$res['payments'],
                            'artwork'=>$res['artwork'],
                            'artlocations'=>$res['artlocations'],
                            'artproofs'=>$res['proofdocs'],
                            'message'=>$res['message'],
                            'contacts'=>$res['contacts'],
                            'order_items'=>$res['order_items'],
                            'order_system'=>$res['order_system_type'],
                            'shipping'=>$res['shipping'],
                            'shipping_address'=>$res['shipping_address'],
                            'billing'=>$res['order_billing'],
                            'charges'=>$res['charges'],
                            'delrecords'=>array(),
                            'locrecid'=>0,
                        );
                    }
                    usersession($ordersession, $leadorder);
                }
                $this->ajaxResponse($mdata, $error);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    public function change_leadorder_item() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);

            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                if (!isset($postdata['entity']) || !isset($postdata['fldname']) || !isset($postdata['newval'])) {
                    $error='Changes parameter is not full';
                } else {
                    $entity=$postdata['entity'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    $oldshipcost=$leadorder['order']['shipping'];

                    if ($entity=='order' && $fldname=='shipdate' && $newval!='') {
                        $newval=strtotime($newval);
                    } elseif ($entity=='shipping' && $fldname=='event_date' && $newval!='') {
                        $newval=strtotime($newval);
                    } elseif ($entity=='order' && $fldname=='credit_appdue' && $newval!='') {
                        $newval=strtotime($newval);
                    } elseif ($entity=='order' && $fldname=='order_date') {
                        if ($newval!='') {
                            $newval=strtotime($newval);
                        } else {
                            $error='Select Order Date';
                            $this->ajaxResponse($mdata, $error);
                        }
                    }
                    $res=$this->leadorder_model->change_order_input($leadorder, $entity, $fldname, $newval, $ordersession);
                    $error=$res['msg'];
                    if (isset($res['old_value'])) {
                        $mdata['old_value']=$res['old_value'];
                    }
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        $mdata['warning']=0;
                        if ($order['shipping']!=$oldshipcost && $oldshipcost!=0) {
                            $mdata['warning']=1;
                            // Prepare new view
                            $options = [
                                'newship' => $order['shipping'],
                                'citychange' => 0,
                                'costchange' => 1,
                                'oldship' => $oldshipcost,
                            ];
                            $mdata['shipwarn']=$this->_shipwarning_confirm_view($options); // $oldshipcost, $order['shipping']
                        }
                        if ($entity=='order' && $fldname=='order_date') {
                            $mdata['order_dateview']=$this->load->view('leadorderdetails/orderdate_edit_view', $order, TRUE);
                        }
                        if ($leadorder['order_system']=='new') {
                            $mdata['order_revenue']=MoneyOutput($order['revenue']);
                            $shipping=$leadorder['shipping'];
                            $shipping_address=$leadorder['shipping_address'];
                            $mdata['shipdate']=$shipping['shipdate'];
                            $mdata['rush_price']=number_format($shipping['rush_price'],2);
                            $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                            $mdata['cntshipadrr']=count($shipping_address);
                            $dateoptions=array(
                                'edit'=>1,
                                'shipping'=>$shipping,
                                'user_role' => $this->USR_ROLE,
                            );
                            $mdata['shipdates_content']=$this->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
                        } else {
                            $mdata['shipdate']=$order['shipdate'];
                            //$mdata['rush_price']=$order['rush_price'];
                            $mdata['cntshipadrr']=1;
                            $subtotal=$this->leadorder_model->oldorder_item_subtotal($order);
                            $mdata['subtotal_view']=  MoneyOutput($subtotal,2);
                        }
                        $mdata['is_shipping']=$order['is_shipping'];
                        $mdata['shipping']=number_format(floatval($order['shipping']),2);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $total_due=$order['revenue']-$order['payment_total'];
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['tax']=MoneyOutput($order['tax']);
                        $mdata['showbilladdress']=0;
                        $mdata['profit_content']=$this->_profit_data_view($order);
                        if ($fldname=='rush_idx' && $entity=='shipping') {
                            if ($leadorder['order_system']=='old') {
                                $mdata['shipdate']=date('m/d/Y', $res['shipdate']);
                            }
                            $mdata['rushallow']=$res['rushallow'];
                            if ($mdata['cntshipadrr']==1) {
                                // Buld rate view                                
                                $shipcost=$shipping_address[0]['shipping_costs'];
                                $costoptions=array(
                                    'shipadr'=>$shipping_address[0]['order_shipaddr_id'],
                                    'shipcost'=>$shipcost,
                                );
                                $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                                $mdata['shipaddress']=$shipping_address[0]['order_shipaddr_id'];
                            } else {
                                $cost_view = '';
                                $numpp = 1;
                                foreach ($shipping_address as $srow) {
                                    $srow['numpp'] = $numpp;
                                    $cost_view.=$this->load->view('leadorderdetails/shipping_datarow_view', $srow, TRUE);
                                    $numpp++;
                                }
                                $mdata['shipcost']=$cost_view;
                            }
                        } elseif ($fldname=='country_id' && $entity=='billing') {
                            $states=$res['out_states'];
                            $curstate=$res['defstate'];
                            if (empty($states)) {
                                $mdata['stateview']='&nbsp;';
                            } else {
                                $stateopt=array(
                                    'curstate'=>$curstate,
                                    'states'=>$states,
                                );
                                $mdata['stateview']=$this->load->view('leadorderdetails/billing_state_select', $stateopt, TRUE);
                            }
                        } elseif ($fldname=='balance_manage' && $entity=='order') {
                            if ($newval==3) {
                                $editalov=1;
                                if ($total_due==0 && $order['payment_total']>0) {
                                    $editalov=0;
                                }
                                $appoptions=array(
                                    'balance_term'=>$order['balance_term'],
                                    'credit_appdue'=>$order['credit_appdue'],
                                    'appaproved'=>$order['appaproved'],
                                    'editalov'=>$editalov,
                                );
                                $mdata['creditview']=$this->load->view('leadorderdetails/creditapp_edit', $appoptions, TRUE);
                            } else {
                                $mdata['creditview']='&nbsp;';
                            }
                        } elseif ($fldname=='item_id' && $entity=='order') {
                            $mdata['order_items']=$order['order_items'];
                        } elseif ($fldname=='showbilladdress' && $entity=='order') {
                            if ($order['order_id']==0) {
                                $mdata['showbilladdress']=1;
                                $billoptions=array(
                                    'billing'=>$leadorder['billing'],
                                    'countries'=>$res['countries'],
                                    'states'=>$res['states'],
                                    'order'=>$order,
                                );
                                if ($newval==0) {
                                    $leftcont=$this->load->view('leadorderdetails/billadress_edit', $billoptions, TRUE);
                                } else {
                                    $leftcont=$this->load->view('leadorderdetails/billsameadress_edit', $billoptions, TRUE);
                                }
                                $mdata['leftbilling']=$leftcont;
                            }
                        }
                        $mdata['shipcal'] = $res['shipcalc'];
                        if ($res['shipcalc']==1) {
                            $order=$leadorder['order'];
                            // $mdata['order_revenue']=MoneyOutput($order['revenue']);
                            $shipping=$leadorder['shipping'];
                            $shipping_address=$leadorder['shipping_address'];
                            $mdata['cntshipadrr']=count($shipping_address);
                            /* Rush */
                            $rushlist=$res['rushlist'];
                            $rushopt=array(
                                'edit'=>1,
                                'rush'=>$rushlist['rush'],
                                'current'=>$res['current'],
                                'shipdate'=>$shipping['shipdate'],
                            );
                            $mdata['rushview']=$this->load->view('leadorderdetails/rushlist_view', $rushopt, TRUE);
                            if ($mdata['cntshipadrr']==1) {
                                // Buld rate view
                                $shipcost=$shipping_address[0]['shipping_costs'];
                                $costoptions=array(
                                    'shipadr'=>$shipping_address[0]['order_shipaddr_id'],
                                    'shipcost'=>$shipcost,
                                );
                                $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                                $mdata['shipaddress']=$shipping_address[0]['order_shipaddr_id'];
                            } else {
                                $cost_view = '';
                                $numpp = 1;
                                foreach ($shipping_address as $srow) {
                                    $srow['numpp'] = $numpp;
                                    $cost_view.=$this->load->view('leadorderdetails/shipping_datarow_view', $srow, TRUE);
                                    $numpp++;
                                }
                                $mdata['shipcost']=$cost_view;
                            }
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    public function change_leadorder_discount() {
        if ($this->isAjax()) {
            $mdata = array();
            $error=$this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $error='Changes parameter is not full';
                if (isset($postdata['entity']) && isset($postdata['fldname']) && isset($postdata['newval'])) {
                    $entity=$postdata['entity'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    $res=$this->leadorder_model->change_order_input($leadorder, $entity, $fldname, $newval, $ordersession);
                    $error=$res['msg'];
                    if (isset($res['old_value'])) {
                        $mdata['old_value']=$res['old_value'];
                    }
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mischrg1_class=$mischrg2_class='input_border_gray';
                        $discnt_class='empty_icon_file';
                        $discnt_title = '';
                        $leadorder=usersession($ordersession);
                        $ord_data=$leadorder['order'];
                        if (abs($ord_data['mischrg_val1'])>0 && empty($ord_data['mischrg_label1'])) {
                            $mischrg1_class='input_border_red';
                        }
                        if (abs($ord_data['mischrg_val2'])>0 && empty($ord_data['mischrg_label2'])) {
                            $mischrg2_class='input_border_red';
                        }
                        if (abs($ord_data['discount_val'])>0 && empty($ord_data['discount_descript'])) {
                            $discnt_class = 'discountdescription_red';
                            $discnt_title = 'All Discounts Must Have Valid Reason Explaining Why';
                        } elseif (!empty($ord_data['discount_descript'])) {
                            $discnt_class = 'icon_file';
                            $discnt_title = $ord_data['discount_descript'];
                        }
                        $mdata['mischrg1_class']=$mischrg1_class;
                        $mdata['mischrg2_class']=$mischrg2_class;
                        $mdata['discnt_class']=$discnt_class;
                        $mdata['discnt_title'] = $discnt_title;
                        $mdata['order_revenue']=MoneyOutput($ord_data['revenue']);
                        $subtotal=$ord_data['item_cost']+$ord_data['item_imprint']+floatval($ord_data['mischrg_val1'])+floatval($ord_data['mischrg_val2'])-floatval($ord_data['discount_val']);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $total_due=$ord_data['revenue']-$ord_data['payment_total'];
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        if ($total_due==0 && $ord_data['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['profit_content']=$this->_profit_data_view($ord_data);
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function orderdiscount_preview() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $options=array(
                    'artwork_id'=>-1,
                    'usrtxt'=>$leadorder['order']['discount_descript'],
                    /* 'title'=>'Type Discount Description', */
                );
                $mdata['content']=$this->load->view('artpage/newarttext_view', $options, TRUE);
                $mdata['title']='Type Discount Description';
                $error='';
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function orderdiscount_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $entity='order';
                $fldname='discount_descript';
                $newval=$postdata['message'];

                $res=$this->leadorder_model->change_order_input($leadorder, $entity, $fldname, $newval, $ordersession);
                $error=$res['msg'];
                if (isset($res['old_value'])) {
                    $mdata['old_value']=$res['old_value'];
                }
                if ($res['result']==$this->success_result) {
                    $error='';
                    $leadorder=usersession($ordersession);
                    $discnt_class ='empty_icon_file';
                    $discnt_title = '';
                    // if (!empty($newval)) {
                    //     $mdata['newclass']='icon_file';
                    // }
                    if (abs($leadorder['order']['discount_val'])>0 && empty($leadorder['order']['discount_descript'])) {
                        $discnt_class = 'discountdescription_red';
                        $discnt_title = 'All Discounts Must Have Valid Reason Explaining Why';
                    } elseif (!empty($leadorder['order']['discount_descript'])) {
                        $discnt_class = 'icon_file';
                        $discnt_title = $leadorder['order']['discount_descript'];
                    }
                    $mdata['newclass'] = $discnt_class;
                    $mdata['newtitle'] = $discnt_title;                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // -----------------------------------------------

    public function orderdiscount_view() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $options=array(
                    'artwork_id'=>-1,
                    'usrtxt'=>$leadorder['order']['discount_descript'],
                    'title'=>'Type Discount Description',
                );
                $mdata['content']=$this->load->view('artpage/newarttext_view', $options, TRUE);
                $error='';
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function show_itemsearch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $order=$leadorder['order'];
                $this->load->model('orders_model');
                $dboptions=array(
                    'exclude'=>array(-4, -5, -2),
                    'brand' => ($order['brand']=='SB' || $order['brand']=='BT') ? 'BT' : 'SR',
                );
                $items=$this->orders_model->get_item_list($dboptions);
                $options=array(
                    'item_id'=>$order['item_id'],
                    'items_list'=>$items,
                    'order_item_label'=>$order['order_itemnumber'],
                    'order_id'=>$order['order_id'],
                    'order_items'=>$order['order_items'],
                );
                $mdata['content']=$this->load->view('leadorder/order_itemedit_view', $options, TRUE);
                $mdata['showother']=($order['item_id']<0 ? 1 : 0);
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Order item
    public function save_orderitem() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $item_id=(isset($postdata['item_id']) ? intval($postdata['item_id']) : 0);
                $custom_item=(isset($postdata['order_items']) ? $postdata['order_items'] : '');
                if (empty($item_id)) {
                    $error='Select Item';
                } else {
                    $mdata['order_system']=$leadorder['order_system'];
                    if ($leadorder['order_system']=='old') {
                        $res=$this->leadorder_model->save_item($leadorder, $item_id, $custom_item, $ordersession);
                        if ($res['result']==$this->error_result) {
                            $error=$res['msg'];
                        } else {
                            $mdata['item_num']=$res['item_number'];
                            $mdata['item_description']=$res['item_name'];
                        }
                    } else {
                        // New Order
                        $res=$this->leadorder_model->save_order_items($leadorder, $item_id, $custom_item, $ordersession);
                        $error=$res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            $leadorder=usersession($ordersession);
                            $newitem = $res['newitem'];
                            $order=$leadorder['order'];
                            $mdata['order_revenue']=MoneyOutput($order['revenue']);
                            $shipping=$leadorder['shipping'];
                            $shipping_address=$leadorder['shipping_address'];
                            $mdata['shipdate']=$shipping['shipdate'];
                            $mdata['rush_price']=$shipping['rush_price'];
                            $mdata['is_shipping']=$order['is_shipping'];
                            $mdata['shipping']=$order['shipping'];
                            $mdata['cntshipadrr']=count($shipping_address);
                            $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                            $mdata['item_subtotal']=MoneyOutput($subtotal);
                            $total_due=$order['revenue']-$order['payment_total'];
                            $dueoptions=array(
                                'totaldue'=>$total_due,
                            );
                            $mdata['ordersystem']=$leadorder['order_system'];
                            $mdata['balanceopen']=1;
                            if ($total_due==0 && $order['payment_total']>0) {
                                $dueoptions['class']='closed';
                                $mdata['balanceopen']=0;
                            } else {
                                $dueoptions['class']='open';
                                if ($total_due<0) {
                                    $dueoptions['class']='overflow';
                                }
                            }
                            $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);

                            $mdata['tax']=$order['tax'];
                            $mdata['profit_content']=$this->_profit_data_view($order);
                            $order_items=$res['order_items'];

                            $content='';
                            foreach ($order_items as $irow) {
                                $imprints=$irow['imprints'];
                                $imprint_options=array(
                                    'order_item_id'=>$irow['order_item_id'],
                                    'imprints'=>$imprints,
                                );
                                $imprintview=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                                $item_options=array(
                                    'order_item_id'=>$irow['order_item_id'],
                                    'items'=>$irow['items'],
                                    'imprintview'=>$imprintview,
                                    'edit'=>1,
                                    'item_id'=>$irow['item_id'],
                                );
                                $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                            }
                            $mdata['content']=$content;
                            /* Rush */
                            $rushlist=$res['rushlist'];
                            $rushopt=array(
                                'edit'=>1,
                                'rush'=>$rushlist['rush'],
                                'current'=>$res['current'],
                                'shipdate'=>$shipping['shipdate'],
                            );
                            $mdata['rushview']=$this->load->view('leadorderdetails/rushlist_view', $rushopt, TRUE);
                            // Prepare view of imprint details
                            $imprdata = $this->_prepare_imprint_details($leadorder, $newitem, $ordersession);
                            if ($imprdata['result']==$this->error_result) {
                                $error = $imprdata['msg'];
                            } else {
                                $mdata['imprintview'] = $imprdata['content'];
                            }
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Remove Item
    public function orderitem_remove() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $order_item_id=$this->input->post('order_item');
                $res=$this->leadorder_model->remove_order_item($leadorder, $order_item_id, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $leadorder = usersession($ordersession);
                    $order = $leadorder['order'];
                    $mdata['order_revenue']=MoneyOutput($order['revenue']);
                    $shipping = $leadorder['shipping'];
                    $shipping_address = $leadorder['shipping_address'];
                    $mdata['shipdate'] = $shipping['shipdate'];
                    $mdata['rush_price'] = $shipping['rush_price'];
                    $mdata['is_shipping'] = $order['is_shipping'];
                    $mdata['shipping'] = $order['shipping'];
                    $mdata['cntshipadrr'] = count($shipping_address);
                    $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                    $mdata['item_subtotal']=MoneyOutput($subtotal);
                    $total_due = $order['revenue'] - $order['payment_total'];
                    $dueoptions=array(
                        'totaldue'=>$total_due,
                    );
                    $mdata['ordersystem']=$leadorder['order_system'];
                    $mdata['balanceopen']=1;
                    if ($total_due==0 && $order['payment_total']>0) {
                        $dueoptions['class']='closed';
                        $mdata['balanceopen']=0;
                    } else {
                        $dueoptions['class']='open';
                        if ($total_due<0) {
                            $dueoptions['class']='overflow';
                        }
                    }
                    $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                    $mdata['tax']=MoneyOutput($order['tax']);
                    $mdata['profit_content']=$this->_profit_data_view($order);

                    $order_items = $res['order_items'];

                    $content = '';
                    foreach ($order_items as $irow) {
                        $imprints = $irow['imprints'];
                        $imprint_options = array(
                            'order_item_id' => $irow['order_item_id'],
                            'imprints' => $imprints,
                        );
                        $imprintview = $this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                        $item_options = array(
                            'order_item_id' => $irow['order_item_id'],
                            'items' => $irow['items'],
                            'imprintview' => $imprintview,
                            'edit' => 1,
                            'item_id' => $irow['item_id'],
                        );
                        $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                    }
                    $mdata['content'] = $content;
                    /* Rush */
                    $rushlist = $res['rushlist'];
                    $rushopt = array(
                        'edit' => 1,
                        'rush' => $rushlist['rush'],
                        'current' => $res['current'],
                        'shipdate'=>$shipping['shipdate'],
                    );
                    $rushview=$this->load->view('leadorderdetails/rushlist_view', $rushopt, TRUE);
                    $mdata['rushview'] = $rushview;
                    // Build Shipping Addres View                    
                    if (count($shipping_address)==1) {
                        $shipaddr=$shipping_address[0];
                        $mdata['shipaddress']=$shipaddr['order_shipaddr_id'];
                        $shipcost=$shipaddr['shipping_costs'];
                        if (empty($shipcost)) {
                            $mdata['shipcost']='&nbsp;';
                        } else {
                            $costoptions=array(
                                'shipadr'=>$shipaddr['order_shipaddr_id'],
                                'shipcost'=>$shipcost,
                            );
                            $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                        }
                    } else {
                        // Show New shipp Adress
                        $mdata['shipcost']=$this->_build_multiship_view($shipping_address, $rushview, $order, $shipping);
                    }
                    $locat=$leadorder['artlocations'];
                    $locat_view='';
                    $blankopt=array(
                        'view'=>($order['order_blank']==1 ? 'block' : 'none'),
                    );
                    $locat_view.=$this->load->view('leadorderdetails/artlocs/blank_view', $blankopt, TRUE);
                    foreach ($locat as $row) {
                        if ($row['deleted']=='') {
                            switch ($row['art_type']) {
                                case 'Logo':
                                    $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_logo_edit', $row, TRUE);
                                    break;
                                case 'Text':
                                    $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_text_edit', $row, TRUE);
                                    break;
                                case 'Reference':
                                    $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_reference_edit', $row, TRUE);
                                    break;
                                case 'Repeat':
                                    $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_repeat_edit', $row, TRUE);
                                    break;
                            }
                        }
                    }
                    $mdata['locat_view']=$locat_view;

                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // ----------------------------------------------------

    // Change fields, which has response with Order Profit
    public function change_profit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $fldname=(isset($postdata['fldname']) ? $postdata['fldname'] : '');
                $newval=(isset($postdata['newval']) ? $postdata['newval'] : '');
                if (empty($fldname)) {
                    $error='Fill all Parameters';
                } else {
                    $res=$this->leadorder_model->change_profit($leadorder, $fldname, $newval,$ordersession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                        if (isset($res['oldval'])) {
                            $mdata['oldval']=$res['oldval'];
                        }
                    } else {
                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        $mdata['profit_content']=$this->_profit_data_view($order);

                        // $mdata['subtotal_view']=$res['subtotal'];
                        if ($leadorder['order_system']=='old') {
                            $subtotal=$this->leadorder_model->oldorder_item_subtotal($order);
                            $mdata['subtotal_view']=  MoneyOutput($subtotal,2);
                        } else {
                            $mdata['order_revenue']=MoneyOutput($order['revenue']);
                        }
                        $total_due=$order['revenue']-$order['payment_total'];

                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function artlocation_add() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $art_type=$this->input->post('loctype');
                $artwork=$leadorder['artwork'];
                if ($art_type=='Logo' || $art_type=='Reference') {
                    $test = usersession($ordersession);
                    $mdata['content']=$this->load->view('artpage/upload_artlogo_view',array('artwork_id'=>$artwork['artwork_id']),TRUE);
                } elseif ($art_type=='Repeat') {
                    /* Get Orders which was approveed */
                    $mdata['content']=$this->load->view('artpage/select_archiveord_view',array('artwork_id'=>$artwork['artwork_id']),TRUE);
                } else {
                    $data=array(
                        'usertext'=>'',
                        'art_type'=>'Text',
                    );
                    $this->load->model('artlead_model');
                    $res=$this->artlead_model->add_location($leadorder, $data, $art_type, $ordersession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $locat=$res['artlocations'];
                        $locat_view='';
                        foreach ($locat as $row) {
                            $row['edit']=1;
                            if ($row['locat_ready']==0) {
                                $locat_view.=$this->load->view('leadorderdetails/artwork_sourcelocat_view', $row, TRUE);
                            } else {
                                $locat_view.=$this->load->view('leadorderdetails/artwork_readylocat_view', $row, TRUE);
                            }
                        }
                        $mdata['content']=$locat_view;
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    public function artnewlocation_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $data=$this->input->post();
                $loctype=$data['loctype'];
                $this->load->model('artlead_model');

                $res=$this->artlead_model->add_location($leadorder, $data, $loctype, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $locat=$res['artlocations'];

                    $locat_view='';
                    $blankopt=array(
                        'view'=>'none',
                    );
                    $locat_view.=$this->load->view('leadorderdetails/artlocs/blank_view', $blankopt, TRUE);
                    foreach ($locat as $row) {
                        switch ($row['art_type']) {
                            case 'Logo':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_logo_edit', $row, TRUE);
                                break;
                            case 'Text':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_text_edit', $row, TRUE);
                                break;
                            case 'Reference':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_reference_edit', $row, TRUE);
                                break;
                            case 'Repeat':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_repeat_edit', $row, TRUE);
                                break;
                        }
                    }
                    $mdata['content']=$locat_view;
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Art location parameter
    public function artlocation_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $this->load->model('artlead_model');
                $artwork_art_id=$this->input->post('artloc');
                $field=$this->input->post('field');
                $newval=$this->input->post('newval');
                $res=$this->artlead_model->change_location($leadorder, $artwork_art_id, $field, $newval, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function artlocation_view() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $artwork_art_id=$this->input->post('artloc');
                $doctype=$this->input->post('doctype');
                $this->load->model('artlead_model');
                $res = $this->artlead_model->show_artlocation($leadorder, $artwork_art_id, $ordersession);
                if ($res['result'] == $this->error_result) {
                    $error = $res['msg'];
                } else {
                    $location=$res['location'];
                    if ($location['art_type']=='Repeat') {
                        $mdata['arttype']='repeat';
                        $locs=$this->artlead_model->show_artdata($location,$doctype);
                        if ($locs['result']==$this->error_result) {
                            $error=$locs['msg'];
                        } else {
                            $mdata['viewurls']=$locs['urls'];
                        }
                    } else {
                        $mdata['arttype']='logo';
                        if ($doctype=='source') {
                            $mdata['artlocurl']=$location['logo_src'];
                        } else {
                            $mdata['artlocurl']=$location['logo_vectorized'];
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Show Customer Text
    public function artlocation_customtextview() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $artwork_art_id=$this->input->post('artloc');
                $this->load->model('artlead_model');
                $res = $this->artlead_model->show_artlocation($leadorder, $artwork_art_id, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $location=$res['location'];
                    $mdata['content']=$this->load->view('artpage/newarttext_view',array('artwork_id'=>$artwork_art_id,'usrtxt'=>$location['customer_text'],'title'=>'Enter Customer Text'),TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Redraw Note Show
    public function artlocation_rdnoteview() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $artwork_art_id=$this->input->post('artloc');
                $this->load->model('artlead_model');
                $res = $this->artlead_model->show_artlocation($leadorder, $artwork_art_id, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $location=$res['location'];
                    $mdata['content']=$this->load->view('artpage/newarttext_view',array('artwork_id'=>$artwork_art_id,'usrtxt'=>$location['redraw_message'],'title'=>'Type notes to Redraw Team'),TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Save Redo Note
    public function artlocation_rdnotesave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $postdata=$this->input->post();
                $artwork_art_id=$postdata['artloc'];
                if (isset($postdata['fldname'])) {
                    $field=$postdata['fldname'];
                    $newval=$postdata['message'];
                } else {
                    $field='redraw_message';
                    $newval=$this->input->post('redraw_message');
                }
                $this->load->model('artlead_model');
                $res=$this->artlead_model->change_location($leadorder, $artwork_art_id, $field, $newval, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $mdata['newclass']='';
                    if (!empty($newval)) {
                        $mdata['newclass']='active';
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Artlocation Font select show
    public function artlocation_fontselect() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                // $artwork_art_id=$this->input->post('artloc');
                $this->load->model('artwork_model');
                $fonts_popular=$this->artwork_model->get_fonts(array('is_popular'=>1));
                $fonts_other=$this->artwork_model->get_fonts(array('is_popular'=>0));
                if (count($fonts_popular)+count($fonts_other)==0) {
                    $error='Empty list of fonts';
                } else {
                    $mdata['content']=$this->load->view('artpage/font_select_view',array('fonts_popular'=>$fonts_popular,'fonts_other'=>$fonts_other),TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Remove artlocation
    public function artlocation_remove() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $this->load->model('artlead_model');
                $artwork_art_id=$this->input->post('artloc');
                $res=$this->artlead_model->remove_location($leadorder, $artwork_art_id, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $locat=$res['artlocations'];
                    $locat_view='';
                    $blankopt=array(
                        'view'=>'none',
                    );
                    $locat_view.=$this->load->view('leadorderdetails/artlocs/blank_view', $blankopt, TRUE);

                    foreach ($locat as $row) {
                        switch ($row['art_type']) {
                            case 'Logo':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_logo_edit', $row, TRUE);
                                break;
                            case 'Text':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_text_edit', $row, TRUE);
                                break;
                            case 'Reference':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_reference_edit', $row, TRUE);
                                break;
                            case 'Repeat':
                                $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_repeat_edit', $row, TRUE);
                                break;
                        }
                    }
                    $mdata['content']=$locat_view;
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // New msg to History
    public function newmsgupdate() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $newmsg=$this->input->post('newmsg');
                $error='Enter New Update Message';
                if (!empty($newmsg)) {
                    $dbupdate=(isset($postdata['updarebd']) ? $postdata['updarebd'] : 0);
                    $res=$this->leadorder_model->histore_msgupdate($leadorder, $newmsg, $this->USR_ID, $this->USER_REPLICA, $dbupdate, $ordersession);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $leadorder=usersession($ordersession);
                        $data=array(
                            'history'=>$leadorder['message']['history'],
                        );
                        $mdata['content']=$this->load->view('leadorderdetails/history_message_view', $data, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // New msg to History
    public function leadorder_historyupdate() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $newmsg=$this->input->post('newmsg');
                $error='Enter New Update Message';
                if (!empty($newmsg)) {
                    $res=$this->leadorder_model->histore_msgupdate($leadorder, $newmsg, $this->USR_ID, $this->USER_REPLICA, $ordersession);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $leadorder=usersession($ordersession);
                        $data=array(
                            'history'=>$leadorder['message']['history'],
                        );
                        $mdata['content']=$this->load->view('leadorderdetails/history_message_view', $data, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Contact Parameters
    public function change_contact() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                if (!isset($postdata['fldname']) || !isset($postdata['contact']) || !isset($postdata['newval'])) {
                    $error='Post Data Not Full';
                    $this->ajaxResponse($mdata, $error);
                }
                $contact_id=$postdata['contact'];
                $fldname=$postdata['fldname'];
                $newval=$postdata['newval'];
                $res=$this->leadorder_model->edit_contact($leadorder, $contact_id, $fldname, $newval, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    if ($fldname=='contact_emal') {
                        if (empty($newval) || !valid_email_address($newval)) {
                            $mdata['locstatus']=1;
                        } else {
                            $mdata['locstatus']=0;
                        }
                    }
                    $leadorder=usersession($ordersession);
                    $contacts=$leadorder['contacts'];
                    $phone='';
                    foreach ($contacts as $row) {
                        if ($row['order_contact_id']==$contact_id) {
                            $phone=$row['contact_phone'];
                        }
                    }
                    $mdata['contact_phone']=$phone;
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function add_itemcolor() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                if (!isset($postdata['order_item']) || !isset($postdata['item'])) {
                    $error='Empty Needed Parameter';
                    $this->ajaxResponse($mdata, $error);
                }
                $order_item_id=$postdata['order_item'];
                $item_id=$postdata['item'];
                $res=$this->leadorder_model->add_itemcolor($leadorder, $order_item_id, $item_id, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $order_items=$res['items'];
                    $imprints=$order_items['imprints'];
                    $imprintview=$this->load->view('leadorderdetails/imprint_data_edit', array('imprints'=>$imprints), TRUE);
                    $item_options=array(
                        'order_item_id'=>$order_items['order_item_id'],
                        'items'=>$order_items['items'],
                        'imprintview'=>$imprintview,
                        'edit'=>1,
                    );
                    $mdata['content']=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Item Params
    public function change_itemparams() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                if (!isset($postdata['entity'])) {
                    $error='Empty Needed Parameter';
                    $this->ajaxResponse($mdata, $error);
                }

                $oldshipcost=$leadorder['order']['shipping'];
                $entity=$postdata['entity'];
                $mdata['fldtype']=$entity;
                if ($entity=='item') {
                    if (!isset($postdata['fldname']) || !isset($postdata['item']) || !isset($postdata['newval']) || !isset($postdata['order_item'])) {
                        $error='Empty Needed Parameter';
                        $this->ajaxResponse($mdata, $error);
                    }
                    $fldname=$postdata['fldname'];
                    $item_id=$postdata['item'];
                    $newval=$postdata['newval'];
                    $order_item_id=$postdata['order_item'];
                    $mdata['item']=$item_id;
                    $mdata['order_item']=$order_item_id;
                    $res=$this->leadorder_model->change_items($leadorder, $order_item_id, $item_id, $fldname, $newval, $ordersession);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $mdata['price_class']=$res['price_class'];
                        $mdata['price_title']='';
                        if ($res['price_class']=='warningprice') {
                            $mdata['price_title']=(empty($res['price_title']) ? '' : 'Base price '.MoneyOutput($res['price_title']));
                        }
                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        $mdata['order_revenue']=MoneyOutput($order['revenue']);
                        $mdata['warning']=0;
                        if ($order['shipping']!=$oldshipcost && $oldshipcost!=0) {
                            $mdata['warning']=1;
                            $options = [
                                'newship' => $order['shipping'],
                                'citychange' => 0,
                                'costchange' => 1,
                                'oldship' => $oldshipcost,
                            ];
                            // Prepare new view
                            $mdata['shipwarn']=$this->_shipwarning_confirm_view($options); // $oldshipcost, $order['shipping']
                        }
                        $shipping=$leadorder['shipping'];
                        $shipping_address=$leadorder['shipping_address'];
                        $mdata['shipcalc']=0;
                        $mdata['shipdate']=$shipping['shipdate'];
                        $mdata['rush_price']=$shipping['rush_price'];
                        $mdata['is_shipping']=$order['is_shipping'];
                        $mdata['shipping']=$order['shipping'];
                        $mdata['cntshipadrr']=count($shipping_address);
                        $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $mdata['subtotals']=$res['subtotals'];
                        $total_due=$order['revenue']-$order['payment_total'];
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['tax']=MoneyOutput($order['tax']);
                        $mdata['profit_content']=$this->_profit_data_view($order);
                        // Output parameters                        
                        $mdata['item_price']=$res['prices'];
                        $order_items=$res['items'];

                        $imprint_options=array(
                            'order_item_id'=>$order_items['order_item_id'],
                            'imprints'=>$order_items['imprints'],
                        );
                        $mdata['imprint_content']=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                        if ($mdata['is_shipping']==1) {
                            $mdata['shipcalc']=1;
                            // We change Shipping Cost
                            $shipping=$leadorder['shipping'];
                            if ($mdata['cntshipadrr']==1) {
                                $shipaddr=$leadorder['shipping_address'][0];
                                $mdata['shipaddress']=$shipaddr['order_shipaddr_id'];
                                $shipcost=$shipaddr['shipping_costs'];
                                $costoptions=array(
                                    'shipadr'=>$shipaddr['order_shipaddr_id'],
                                    'shipcost'=>$shipcost,
                                );
                                $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                            } else {
                                $rushlist=$shipping['out_rushlist'];
                                $rushopt = array(
                                    'edit' => 1,
                                    'rush' => $rushlist['rush'],
                                    'current' => $shipping['rush_idx'],
                                    'shipdate'=>$shipping['shipdate'],
                                );
                                $rushview=$this->load->view('leadorderdetails/rushlist_view', $rushopt, TRUE);

                                $mdata['shipcost']=$this->_build_multiship_view($shipping_address, $rushview, $order, $shipping);
                            }
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function show_itemimprint() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $order_item_id=$this->input->post('order_item_id');
                $res=$this->leadorder_model->prepare_imprint_details($leadorder, $order_item_id, $ordersession);

                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $details=$res['imprint_details'];
                    $order_blank=$res['order_blank'];
                    $item_id=$res['item_id'];
                    if ($order_blank==0) {
                        $chkactiv=0;
                        foreach ($details as $row) {
                            if ($row['active']==1) {
                                $chkactiv=1;
                                break;
                            }
                        }
                        if ($chkactiv==0) {
                            $details[0]['active']=1;
                        }
                    }
                    // Prepare View 
                    $imptintid='imprintdetails'.uniq_link(15);
                    $options=array(
                        'details'=>$details,
                        'item_number'=>$res['item_number'],
                        'order_blank'=>$order_blank,
                        'imprints'=>$res['imprints'],
                        'numlocs'=>count($res['imprints']),
                        'item_name'=>$res['item_name'],
                        'imprintsession'=>$imptintid,
                        'custom' => ($res['item_id']==$this->config->item('custom_id') || $res['item_id']==$this->config->item('other_id')) ? 1 : 0,
                        'brand' => $res['brand'],
                    );
                    $mdata['content']=  $this->load->view('leadorderdetails/imprint_details_edit', $options, TRUE);

                    $imprintdetails=array(
                        'imprint_details'=>$details,
                        'order_blank'=>$order_blank,
                        'order_item_id'=>$order_item_id,
                        'item_id'=>$item_id,
                        'brand' => $res['brand'],
                    );
                    usersession($imptintid, $imprintdetails);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save changes in Imprint Details
    public function imprintdetails_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);

            if (!empty($leadorder)) {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $imprintdetails=$postdata['imprintsession'];
                $imprint_details=usersession($imprintdetails);
                $error=$this->restore_orderdata_error;
                if (!empty($imprint_details)) {
                    $error='Enter all Parameters for change';
                    if (isset($postdata['details']) && !empty($postdata['details']) && isset($postdata['fldname']) && !empty($postdata['fldname'])) {
                        $fldname=$postdata['fldname'];
                        $order_imprindetail_id=$postdata['details'];
                        $newval=$postdata['newval'];
                        $res=$this->leadorder_model->change_imprint_details($imprint_details, $order_imprindetail_id, $fldname, $newval, $imprintdetails);
                        $error=$res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error='';
                            $mdata['fldname']=$fldname;
                            $mdata['details']=$order_imprindetail_id;
                            $mdata['newval']=$newval;
                            if ($fldname=='imprint_type') {
                                if ($newval=='NEW') {
                                    $mdata['setup']=  number_format($res['setup'],2,'.','');
                                } else {
                                    $mdata['class']=$res['class'];
                                }
                            }
                        }
                        // Calc new period for lock
                        $mdata['loctime'] = $this->_leadorder_locktime();
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Edit repeat Note
    public function edit_repeatnote() {

        $postdata=$this->input->post();
        $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
        $leadorder=usersession($ordersession);

        if (empty($leadorder)) {
            echo $this->restore_orderdata_error;
            die();
        }
        // Lock Edit Record                
        $locres=$this->_lockorder($leadorder);
        if ($locres['result']==$this->error_result) {
            echo $locres['msg'];
            die();
        }
        // Details
        $detail_id=$postdata['details'];

        $imprintdetails=$postdata['imprintsession'];
        $imprint_details=usersession($imprintdetails);

        if (empty($imprint_details)) {
            echo $this->restore_orderdata_error;
            die();
        }
        $res=$this->leadorder_model->get_repeat_note($imprint_details, $detail_id, $imprintdetails);
        if ($res['result']==$this->error_result) {
            echo $res['msg'];
        } else {
            $note=$res['repeat_note'];
            $content=$this->load->view('leadorderdetails/repeat_note_edit', array('repeat_note'=>$note),TRUE);
            echo $content;
        }
        return TRUE;
    }

    // Save Repeat Note
    public function repeatnote_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $imprintdetails=$postdata['imprintsession'];
                $imprint_details=usersession($imprintdetails);
                if (empty($imprint_details)) {
                    $error=$this->restore_orderdata_error;
                } else {
                    $detail_id=$postdata['detail_id'];
                    $repeat_note=$postdata['repeat_note'];
                    $res=$this->leadorder_model->save_repeat_note($imprint_details, $repeat_note, $detail_id, $imprintdetails);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function imprintdetails_blankorder() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $imprintdetails=$postdata['imprintsession'];
                $imprint_details=usersession($imprintdetails);
                if (empty($imprint_details)) {
                    $error=$this->restore_orderdata_error;
                } else {
                    $newval=$this->input->post('newval');
                    $res=$this->leadorder_model->imprintdetails_blankorder($imprint_details, $newval, $imprintdetails);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    }
                }

            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Save Data from Popup
    public function save_imprintdetails() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);

            $imprintdetails=$postdata['imprintsession'];
            $imprint_details=usersession($imprintdetails);

            if (empty($imprint_details)) {
                $error=$this->restore_orderdata_error;
            } elseif (empty ($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $res=$this->leadorder_model->save_imprintdetails($leadorder, $imprint_details, $ordersession, $imprintdetails);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $leadorder=usersession($ordersession);
                    $order=$leadorder['order'];
                    $mdata['order_revenue']=MoneyOutput($order['revenue']);
                    $shipping=$leadorder['shipping'];
                    $shipping_address=$leadorder['shipping_address'];
                    $mdata['shipdate']=$shipping['shipdate'];
                    $mdata['rush_price']=$shipping['rush_price'];
                    $mdata['is_shipping']=$order['is_shipping'];
                    $mdata['shipping']=$order['shipping'];
                    $mdata['cntshipadrr']=count($shipping_address);
                    $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                    $mdata['item_subtotal']=MoneyOutput($subtotal);
                    $total_due=$order['revenue']-$order['payment_total'];
                    $dueoptions=array(
                        'totaldue'=>$total_due,
                    );
                    if ($total_due==0 && $order['payment_total']>0) {
                        $dueoptions['class']='closed';
                    } else {
                        $dueoptions['class']='open';
                        if ($total_due<0) {
                            $dueoptions['class']='overflow';
                        }
                    }
                    $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                    $mdata['tax']=MoneyOutput($order['tax']);
                    $mdata['profit_content']=$this->_profit_data_view($order);

                    $order_items=$res['item'];
                    $imprint_options=array(
                        'order_item_id'=>$order_items['order_item_id'],
                        'imprints'=>$order_items['imprints'],
                    );
                    $mdata['imprint_content']=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                    $mdata['order_item_id']=$order_items['order_item_id'];
                    $mdata['order_blank']=$res['order_blank'];
                    $mdata['shiprebuild']=$res['shiprebuild'];
                    if ($res['shiprebuild']==1) {
                        // Rebuild Ship Date
                        $mdata['rush_price']=number_format($shipping['rush_price'],2);
                        $rush=unserialize($shipping['rush_list']);
                        $outrush=$rush['rush'];
                        $rushopt=array(
                            'edit'=>1,
                            'rush'=>$outrush,
                            'current'=>$shipping['rush_idx'],
                            'shipdate'=>$shipping['shipdate'],
                        );
                        $mdata['rushview']=$this->load->view('leadorderdetails/rushlist_view', $rushopt, TRUE);
                    }
                    // Art Locations
                    $mdata['artlocchange']=$res['artlocchange'];
                    if ($res['artlocchange']==1) {
                        $locat=$leadorder['artlocations'];
                        $locat_view='';
                        $blankopt=array(
                            'view'=>'none',
                        );
                        $locat_view.=$this->load->view('leadorderdetails/artlocs/blank_view', $blankopt, TRUE);
                        foreach ($locat as $row) {
                            if ($row['deleted']=='') {
                                switch ($row['art_type']) {
                                    case 'Logo':
                                        $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_logo_edit', $row, TRUE);
                                        break;
                                    case 'Text':
                                        $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_text_edit', $row, TRUE);
                                        break;
                                    case 'Reference':
                                        $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_reference_edit', $row, TRUE);
                                        break;
                                    case 'Repeat':
                                        $locat_view.=$this->load->view('leadorderdetails/artlocs/artlocation_repeat_edit', $row, TRUE);
                                        break;
                                }
                            }
                        }
                        $mdata['locat_view']=$locat_view;
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Ship Address
    public function change_shipadrress() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                if (!isset($postdata['fldname']) || !isset($postdata['newval']) || !isset($postdata['shipadr'])) {
                    $error='Changes parameter is not full';
                } else {
                    $oldshipcost=$leadorder['order']['shipping'];
                    $shipaddr_id=$postdata['shipadr'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    $mdata['fldname']=$fldname;
                    $mdata['shipaddress']=$shipaddr_id;
                    $res=$this->leadorder_model->change_shipaddres($leadorder, $shipaddr_id, $fldname, $newval, $ordersession);
                    $error=$res['msg'];
                    if (isset($res['old_value'])) {
                        $mdata['old_value']=$res['old_value'];
                    }
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        $mdata['warning']=0;
                        if ($res['multicity']!==0 || ($order['shipping']!=$oldshipcost && $oldshipcost!=0)) {
                            $mdata['warning']=1;
                            $options = [
                                'newship' => $order['shipping'],
                                'citychange' => 0,
                                'costchange' => 0,
                                'oldship' => 0,
                            ];
                            if ($res['multicity']!==0) {
                                $options['citylist'] = $res['validcity'];
                                $options['citychange'] = $shipaddr_id;
                            }
                            if ($order['shipping']!=$oldshipcost && $oldshipcost!=0) {
                                $options['oldship'] = $oldshipcost;
                                $options['costchange'] = 1;
                            }
                            // $oldshipcost, $order['shipping']
                            // Prepare new view
                            $mdata['shipwarn']=$this->_shipwarning_confirm_view($options);
                        }
                        $shipping=$leadorder['shipping'];
                        $shipping_address=$leadorder['shipping_address'];
                        $mdata['order_revenue']=MoneyOutput($order['revenue']);
                        $mdata['shipdate']=$shipping['shipdate'];
                        $mdata['rush_price']=$shipping['rush_price'];
                        $mdata['is_shipping']=$order['is_shipping'];
                        $mdata['shipping']=$order['shipping'];
                        $mdata['cntshipadrr']=count($shipping_address);
                        $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $total_due=$order['revenue']-$order['payment_total'];
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['tax']=MoneyOutput($order['tax']);
                        $mdata['profit_content']=$this->_profit_data_view($order);
                        if ($fldname=='country_id') {
                            if (count($res['states'])==0) {
                                $mdata['stateview']='&nbsp;';
                            } else {
                                $stateoptions=array(
                                    'shipadr'=>$res['shipadr'],
                                    'states'=>$res['states'],
                                );
                                $mdata['stateview']=$this->load->view('leadorderdetails/shipping_state_select', $stateoptions, TRUE);
                            }
                        } elseif ($fldname=='zip') {
                            if ($mdata['cntshipadrr']==1) {
                                $shipcost=$res['shipadr']['shipping_costs'];
                                $costoptions=array(
                                    'shipadr'=>$res['shipadr']['order_shipaddr_id'],
                                    'shipcost'=>$shipcost,
                                );
                                $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                                $mdata['city']=$res['shipadr']['city'];
                                $mdata['state_id']=$res['shipadr']['state_id'];
                            } else {
                                //
                            }
                        }
                        if ($mdata['cntshipadrr']==1) {
                            $shipaddr=$shipping_address[0];
                            if ($shipaddr['taxview']==0) {
                                $taxview=$this->load->view('leadorderdetails/tax_empty_view', array(), TRUE);
                            } else {
                                $taxview=$this->load->view('leadorderdetails/tax_data_edit', $shipaddr, TRUE);
                            }
                            $mdata['taxview']=$taxview;
                        }
                        $dateoptions=array(
                            'edit'=>1,
                            'shipping'=>$shipping,
                            'user_role' => $this->USR_ROLE,
                        );
                        $mdata['shipdates_content']=$this->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_shipcost() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                if (!isset($postdata['fldname']) || !isset($postdata['newval']) || !isset($postdata['shipadr'])) {
                    $error='Changes parameter is not full';
                } else {
                    $shipaddr_id=$postdata['shipadr'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    $mdata['fldname']=$fldname;
                    $mdata['shipaddress']=$shipaddr_id;
                    $res=$this->leadorder_model->change_shipaddrescost($leadorder, $shipaddr_id, $fldname, $newval, $ordersession);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        $mdata['order_revenue']=MoneyOutput($order['revenue']);
                        $shipping=$leadorder['shipping'];
                        $shipping_address=$leadorder['shipping_address'];
                        $mdata['shipdate']=$shipping['shipdate'];
                        $mdata['rush_price']=$shipping['rush_price'];
                        $mdata['is_shipping']=$order['is_shipping'];
                        $mdata['shipping']=$order['shipping'];
                        $mdata['cntshipadrr']=count($shipping_address);
                        $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $total_due=$order['revenue']-$order['payment_total'];
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['tax']=MoneyOutput($order['tax']);
                        $mdata['profit_content']=$this->_profit_data_view($order);
                        $mdata['order_shipcost_id']=$res['order_shipcost_id'];
                        if ($leadorder['order_system']=='new') {
                            // Build Shipping Dates content
                            $dateoptions=array(
                                'edit'=>1,
                                'shipping'=>$shipping,
                                'user_role' => $this->USR_ROLE,
                            );
                            $mdata['shipdates_content']=$this->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_leadorder_charges() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                if (!isset($postdata['fldname']) || !isset($postdata['newval']) || !isset($postdata['charge'])) {
                    $error='Changes parameter is not full';
                } else {
                    $order_payment_id=$postdata['charge'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    $mdata['fldname']=$fldname;
                    $mdata['charge']=$order_payment_id;
                    $res=$this->leadorder_model->change_chargedata($leadorder, $order_payment_id, $fldname, $newval,$ordersession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                        if (isset($res['oldval'])) {
                            $mdata['oldval']=$res['oldval'];
                        }
                    } else {
                        $leadorder=usersession($ordersession);
                        if ($fldname=='cardnum') {
                            $mdata['cardnum']=$res['charge']['cardnum'];
                        }
                        $order=$leadorder['order'];
                        $shipping=$leadorder['shipping'];
                        $shipping_address=$leadorder['shipping_address'];
                        $mdata['shipdate']=$shipping['shipdate'];
                        $mdata['rush_price']=$shipping['rush_price'];
                        $mdata['is_shipping']=$order['is_shipping'];
                        $mdata['shipping']=$order['shipping'];
                        $mdata['cntshipadrr']=count($shipping_address);
                        $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $total_due=$order['revenue']-$order['payment_total'];
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['tax']=MoneyOutput($order['tax']);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Payment
    public function leadorder_paycharge() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                // Place a new charge
                $order_payment_id=$postdata['order_payment_id'];
                $payres=$this->leadorder_model->leadorder_paycharge($leadorder, $order_payment_id, $this->USR_ID, $ordersession);
                $error=$payres['msg'];
                if ($payres['result']==$this->success_result) {
                    // Save Order
                    $leadorder=usersession($ordersession);
                    $saveres = $this->leadorder_model->save_order($leadorder, $this->USR_ID, $ordersession);
                    $error = $saveres['msg'];
                    if ($saveres['result']==$this->success_result) {
                        if (isset($leadorder['locrecid'])) {
                            $this->engaded_model->clean_engade($leadorder['locrecid']);
                        }
                        $order = $saveres['order_id'];
                        $brand = $postdata['brand'];
                        // Get Order data
                        $res = $this->leadorder_model->get_leadorder($order, $this->USR_ID, $brand);
                        $error = $res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            // Lock record
                            $lockoptions=array(
                                'entity'=>'ts_orders',
                                'entity_id'=>$order,
                                'user_id'=>$this->USR_ID,
                            );
                            // Check lock of record
                            $chklock=$this->engaded_model->check_engade($lockoptions);
                            if ($chklock==$this->error_result) {
                                $error='Order was locked for edit by other user. Try later';
                                $this->ajaxResponse($mdata, $error);
                            }
                            $locking=$this->engaded_model->lockentityrec($lockoptions);
                            $orderres['locrecid']=$locking;
                            $orddata=$res['order'];
                            // Build Head
                            $head_options = [
                                'order_head' => $this->load->view('leadorderdetails/head_order_edit', $orddata,TRUE),
                                'prvorder' => $res['prvorder'],
                                'nxtorder' => $res['nxtorder'],
                            ];
                            // Build View
                            $data=$this->template->_prepare_leadorder_view($res,$this->USR_ID, 1);
                            $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                            // Build Content
                            $options['order_data']=$order_data;
                            $options['locrecid']=$locking;
                            $options['leadsession']=$ordersession;
                            if ($this->input->ip_address()=='127.0.0.1') {
                                $options['timeout']=(time()+$this->config->item('loctimeout_local'))*1000;
                            } else {
                                $options['timeout']=(time()+$this->config->item('loctimeout'))*1000;
                            }
                            $options['current_page']=ifset($postdata,'callpage','art_tasks');
                            $content=$this->load->view('leadorderdetails/top_menu_edit',$options, TRUE);
                            $header = $this->load->view('leadorderdetails/head_edit', $head_options, TRUE);
                            /* Save to session */
                            if ($res['order_system_type']=='old') {
                                $leadorder=array(
                                    'order'=>$orddata,
                                    'payments'=>$res['payments'],
                                    'artwork'=>$res['artwork'],
                                    'artlocations'=>$res['artlocations'],
                                    'artproofs'=>$res['proofdocs'],
                                    'message'=>$res['message'],
                                    'order_system'=>$res['order_system_type'],
                                    'locrecid'=>$locking,
                                );
                            } else {
                                $leadorder=array(
                                    'order'=>$orddata,
                                    'payments'=>$res['payments'],
                                    'artwork'=>$res['artwork'],
                                    'artlocations'=>$res['artlocations'],
                                    'artproofs'=>$res['proofdocs'],
                                    'message'=>$res['message'],
                                    'contacts'=>$res['contacts'],
                                    'order_items'=>$res['order_items'],
                                    'order_system'=>$res['order_system_type'],
                                    'shipping'=>$res['shipping'],
                                    'shipping_address'=>$res['shipping_address'],
                                    'billing'=>$res['order_billing'],
                                    'charges'=>$res['charges'],
                                    'delrecords'=>array(),
                                    'locrecid'=>$locking,
                                );
                            }
                            usersession($ordersession, $leadorder);
                            $mdata['content']=$content;
                            $mdata['header']=$header;
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Add Payment method
    public function add_leadorder_charge() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $res=$this->leadorder_model->add_chargedata($leadorder, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $leadorder = usersession($ordersession);
                    $order = $leadorder['order'];
                    $order_id=$order['order_id'];
                    $charges=$leadorder['charges'];
                    $options=array(
                        'charges'=>$charges,
                        'order_id'=>$order_id,
                    );
                    $mdata['content']=$this->load->view('leadorderdetails/charge_details_view',$options,TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Prepare Credit App 
    public function creditappdoc() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $options=array(
                    'title'=>'Upload Credit App Doc',
                );
                $mdata['content']=$this->load->view('leadorderdetails/taxdoc_upload_view',$options,TRUE);
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Appl Doc
    public function creditappdocsave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $newdoc=$postdata['newdoc'];
                $srcname=$postdata['srcname'];
                $res=$this->leadorder_model->save_newcreditappdoc($leadorder, $newdoc, $srcname,$ordersession);
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Tax Execpt doc upload prepare
    public function taxexcptdoc() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $shipadr=$this->input->post('shipadr');
                $res=$this->leadorder_model->get_taxdetails($leadorder, $shipadr, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $tax=$res['taxdata'];
                    $tax['title']='Upload Exempt Tax Document';
                    $mdata['content']=$this->load->view('leadorderdetails/taxdoc_upload_view',$tax,TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function taxexcptdocsave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $shipadr=$postdata['shipadr'];
                $newdoc=$postdata['newdoc'];
                $srcname=$postdata['srcname'];
                $res=$this->leadorder_model->save_newtaxdoc($leadorder, $shipadr, $newdoc, $srcname, $ordersession);
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Add Proof Doc
    public function proofdoc_add() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // Create Session for uploads
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $data=array(
                    'artwork_id'=>-1,
                    'docs'=>array(),
                );
                $artwork_id=-1;
                $sessid='proofupload'.uniq_link(15);
                usersession($sessid, $data);
                $mdata['content']=$this->load->view('artpage/proofs_upload_view',array('artwork_id'=>$artwork_id,'uplsess'=>$sessid),TRUE);
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function saveproofdocload() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                    $this->load->model('artlead_model');
                    $res=$this->artlead_model->save_artproofdocs($leadorder, $postdata['proofdoc'], $postdata['sourcename'] , $ordersession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $proofs=$res['outproof'];
                        $mdata['content']=leadProfdocOut($proofs, 1);
                        $numoutprofdoc=ceil(count($proofs)/5);
                        // $mdata['profdocwidth']=$numoutprofdoc*160;
                        $mdata['profdocwidth']=$numoutprofdoc*145;
                    }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function changeprofdoc() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $artwork_proof_id=$postdata['artproof'];
                $fldname=$postdata['fldname'];
                $newval=$postdata['newval'];

                $this->load->model('artlead_model');
                $res=$this->artlead_model->change_artproofdocs($leadorder, $artwork_proof_id, $fldname, $newval, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $proofs=$res['outproof'];
                    $mdata['content']=leadProfdocOut($proofs, 1);
                    $numoutprofdoc=ceil(count($proofs)/5);
                    // $mdata['profdocwidth']=$numoutprofdoc*160;
                    $mdata['profdocwidth']=$numoutprofdoc*145;
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Prepare Email for send Proof doc
    public function prepare_profdocemail() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = '';
            $template = $this->ART_PROOF;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $this->load->model('artlead_model');
                $res=$this->artlead_model->prepare_proofdocapproveemail($leadorder, $template, $this->USR_ID, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $order = $leadorder['order'];
                    if ($order['brand']=='SR') {
                        $artemail = $this->config->item('art_srdept_email');
                    } else {
                        $artemail = $this->config->item('art_dept_email');
                    }
                    $options = array(
                        'artwork_id' => $res['artwork_id'],
                        'from' => $artemail,
                        'tomail' => $res['customer_email'],
                        'subject' => $res['subject'],
                        'message' => $res['message'],
                    );
                    $mdata['content'] = $this->load->view('artpage/approve_email_view', $options, TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);

        }
    }

    // Send Proof doc for Approve
    public function sendproofs() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = '';
            $data = $this->input->post();
            $mdata['post'] = $data;
            $ordersession=(isset($data['ordersession']) ? $data['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $this->load->model('artlead_model');
                $res = $this->artlead_model->send_artproofmail($data, $leadorder, $this->USR_ID, $ordersession);
                if ($res['result'] == $this->error_result) {
                    $error = $res['msg'];
                } else {
                    /* Build new content for proofs */
                    $proofs=$res['outproof'];
                    $mdata['content']=leadProfdocOut($proofs, 1);
                    $numoutprofdoc=ceil(count($proofs)/5);
                    // $mdata['profdocwidth']=$numoutprofdoc*160;
                    $mdata['profdocwidth']=$numoutprofdoc*145;
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showproofdoc() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $artwork_proof_id=$this->input->post('proofdoc');
                $this->load->model('artlead_model');
                $res = $this->artlead_model->show_atproofdoc($leadorder, $artwork_proof_id, $ordersession);
                if ($res['result'] == $this->error_result) {
                    $error = $res['msg'];
                } else {
                    $proofdoc=$res['outproof'];
                    if ($proofdoc['artwork_proof_id']>0) {
                        $mdata['proofdocurl']=$proofdoc['src'];
                    } else {
                        $fullpreload=$this->config->item('upload_path_preload');
                        $shpreload=$this->config->item('pathpreload');
                        $mdata['proofdocurl']=  str_replace($fullpreload,$shpreload, $proofdoc['src']);
                    }
                    $mdata['proofdocname']=$proofdoc['source_name'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Ticket related with order
    public function order_ticket() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                // Get data about ticket
                $res=$this->leadorder_model->get_opentickets($leadorder, $ordersession);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $ticket=$res['ticket'];
                    $attachment_list=$res['ticket_attach'];
                    $session_id = 'attach'.uniq_link(10);
                    usersession('ticketattach',$session_id);
                    $options=array(
                        'ticket_id'=>$ticket['ticket_id'],
                        'list'=>$attachment_list,
                        'cnt'=>count($attachment_list),
                    );
                    $attachlist=$this->load->view('tickets/ticket_attachlist_view',$options,TRUE);
                    $ticket['attachment']=$this->load->view('tickets/ticket_attach_view',array('attach_list'=>$attachlist),TRUE);
                    $mdata['content']=$this->load->view('tickets/ticket_edit_view',$ticket,TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save ticket
    public function save_orderticket() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $tickdata=$this->input->post();
            $ordersession=(isset($tickdata['ordersession']) ? $tickdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $tickdate=$this->input->post('ticket_date');
                $tickdata['ticket_date']=null;

                if ($tickdate!='') {
                    $tickdata['ticket_date']=  strtotime($tickdate);
                }
                $user_id=$this->USR_ID;
                $this->load->model('tickets_model');

                $res=$this->tickets_model->save_ticket($tickdata,$user_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $ticket_id=$res['ticket'];
                    $sess_id=usersession('ticketattach');
                    $this->tickets_model->save_attach($ticket_id,$sess_id);
                    // Get data about open tickets   
                    $res=$this->leadorder_model->get_opentickets_data($leadorder, $ordersession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $ticketcnt=$res['numtickets'];
                        if ($ticketcnt==0) {
                            // $ticketview='&nbsp;';
                            $mdata['ticket_content']=$this->load->view('leadorderdetails/ticket_dataempty_view', array(),TRUE);
                        } else {
                            $tickdata=$res['ticket'];
                            $mdata['ticket_content']=$this->load->view('leadorderdetails/ticket_data_view', $tickdata, TRUE);
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function shiptracks_show() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('email_model');
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                // Prepare to View
                $shipaddrs=$leadorder['shipping_address'];
                $shipview='';
                $numaddr=1;
                $showalltrack=0;
                $session_id='shiptraccodes'.uniq_link(15);

                if ($leadorder['order_system']=='new') {
                    foreach ($shipaddrs as $srow) {
                        $srow['numaddr']=$numaddr;
                        $srow['shipping_method']=$srow['out_shipping_method'];
                        $srow['deliveries']=$this->config->item('delivery_service');
                        $srow['shipping_costs']=$srow['shipping'];
                        $packoptions=array(
                            'packages'=>$srow['packages'],
                            'shipaddr'=>$srow['order_shipaddr_id'],
                            'deliveries'=>$this->config->item('delivery_service'),
                        );
                        $srow['packagesview']=$this->load->view('leadorderdetails/shiptrack_packages_view', $packoptions, TRUE);
                        $shipview.=$this->load->view('leadorderdetails/shipaddres_track_view', $srow, TRUE);
                        $numaddr++;
                        foreach ($srow['packages'] as $prow) {
                            if (!empty($prow['track_code']) && in_array($prow['deliver_service'], $this->config->item('tracking_service'))) {
                                $showalltrack=1;
                            }
                        }
                    }
                    $shipstatus=$this->leadorder_model->_leadorderview_shipping_status($leadorder);
                    $options=array(
                        'order'=>$leadorder['order'],
                        'shipping'=>$leadorder['shipping'],
                        'addres'=>$shipview,
                        'showalltrack'=>$showalltrack,
                        'tracksession'=>$session_id,
                        'status'=>$shipstatus['order_status'],
                    );
                    $mdata['content']=$this->load->view('leadorderdetails/shiptrack_popup_view', $options, TRUE);
                    // $this->ajaxResponse($mdata,'Test');
                } else {
                    foreach ($shipaddrs as $srow) {
                        $srow['numaddr'] = $numaddr;
                        $srow['shipping_method']='Ground';
                        $srow['deliveries']=$this->config->item('delivery_service');
                        $packoptions=array(
                            'packages'=>$srow['packages'],
                            'shipaddr'=>$srow['order_shipaddr_id'],
                            'deliveries'=>$this->config->item('delivery_service'),
                        );
                        $srow['packagesview']=$this->load->view('leadorderdetails/shiptrack_packages_view', $packoptions, TRUE);
                        $shipview.=$this->load->view('leadorderdetails/shipaddres_track_view', $srow, TRUE);
                        $numaddr++;
                        foreach ($srow['packages'] as $prow) {
                            if (!empty($prow['track_code']) && in_array($prow['deliver_service'], $this->config->item('tracking_service'))) {
                                $showalltrack=1;
                            }
                        }
                    }
                    $shipstatus=$this->leadorder_model->_leadorderview_shipping_status($leadorder);
                    $options=array(
                        'order'=>$leadorder['order'],
                        'shipping'=>$leadorder['shipping'],
                        'addres'=>$shipview,
                        'showalltrack'=>$showalltrack,
                        'tracksession'=>$session_id,
                        'status'=>$shipstatus['order_status'],
                    );
                    $mdata['content']=$this->load->view('leadorderdetails/shiptrack_popup_view', $options, TRUE);
                }
                // Get Template
                $template=$this->email_model->get_emailtemplate_byname($this->TRACK_TEMPLATE);
                if (!isset($template['email_template_id'])) {
                    $template['email_template_body']=$template['email_template_address']='';
                    $template['email_template_subject']='Tracking Number - Bluetrack Order  <<order_number>';
                }
                if ($leadorder['order_system']=='old') {
                    $order=$leadorder['order'];

                    $email=array(
                        'customer'=>$order['customer_email'],
                        'sender'=>$template['email_template_address'],
                        'bcc'=>'',
                        'subject'=>  str_replace('<<order_number>>', $order['order_num'],$template['email_template_subject']),
                        'message'=>$template['email_template_body'],
                    );
                } else {
                    $order=$leadorder['order'];
                    $contact=$leadorder['contacts'];
                    $email=array(
                        'customer'=>$order['customer_email'],
                        'sender'=>$template['email_template_address'],
                        'bcc'=>'',
                        'subject'=>  str_replace('<<order_number>>', $order['order_num'],$template['email_template_subject']),
                        'message'=>$template['email_template_body'],
                    );
                }

                // Save to session data about track models
                $shiptracks=array(
                    'shipping'=>$leadorder['shipping'],
                    'shipping_address'=>$leadorder['shipping_address'],
                    'email'=>$email,
                );
                usersession($session_id, $shiptracks);
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Add Shipaddres Package
    public function shippackage_add() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                usersession($ordersession, $leadorder);
                // Session for trackcodes
                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);
                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $shipaddr=$this->input->post('shipaddr');
                    $res=$this->leadorder_model->shiptrack_addpackage($shiptracks, $shipaddr, $shiptraccodes);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $data=array(
                            'packages'=>$res['packages'],
                            'shipaddr'=>$shipaddr,
                            'deliveries'=>$this->config->item('delivery_service'),
                        );
                        $mdata['shipaddr_content']=$this->load->view('leadorderdetails/shiptrack_packages_view', $data, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function shiptrackpackage_remove() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = '';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                usersession($ordersession, $leadorder);
                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);
                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $res = $this->leadorder_model->shiptrack_package_remove($shiptracks, $postdata, $shiptraccodes);
                    if ($res['result'] == $this->error_result) {
                        $error = $res['msg'];
                    } else {
                        $shipaddr=$postdata['shipaddres'];
                        $data=array(
                            'packages'=>$res['packages'],
                            'shipaddr'=>$shipaddr,
                            'deliveries'=>$this->config->item('delivery_service'),
                        );
                        $mdata['shipaddr_content']=$this->load->view('leadorderdetails/shiptrack_packages_view', $data, TRUE);
                        $shipping_address=$res['shipping_address'];
                        $trackall=0;
                        foreach ($shipping_address as $srow) {
                            foreach ($srow['packages'] as $prow) {
                                if (!empty($prow['track_code']) && $prow['delflag']==0 && in_array($prow['deliver_service'], $this->config->item('tracking_service'))) {
                                    $trackall=1;
                                }
                            }
                        }
                        $mdata['showalltrack']=$trackall;
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Package params
    public function shiptrack_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                usersession($ordersession, $leadorder);
                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);
                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $res=$this->leadorder_model->shiptrack_changepackage($shiptracks, $postdata, $shiptraccodes);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $shipping_address=$res['shipping_address'];
                        $showalltrack=0;
                        foreach ($shipping_address as $row) {
                            $packages=$row['packages'];
                            foreach ($packages as $prow) {
                                if (!empty($prow['track_code']) && $prow['delflag']==0 && in_array($prow['deliver_service'], $this->config->item('tracking_service'))) {
                                    $showalltrack=1;
                                }
                            }
                        }
                        $mdata['showalltrack']=$showalltrack;
                        if ($postdata['field']=='senddata' && $postdata['newval']==1) {
                            $mdata['email_view']=$this->load->view('leadorderdetails/track_email_view', $shiptracks['email'],TRUE);
                        }
                        $mdata['shownewrow']=$res['shownewrow'];
                        $mdata['viewtrack']=$res['viewtrack'];
                        if ($res['shownewrow']==1) {
                            $poptions=array(
                                'shipaddr'=>$postdata['shipaddres'],
                                'package'=>$res['package'],
                                'deliveries'=>$this->config->item('delivery_service'),
                            );
                            $mdata['packageview']=$this->load->view('leadorderdetails/track_package_view', $poptions, TRUE);
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Track Code
    public function shiptrackpackage_tracking() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                usersession($ordersession, $leadorder);
                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);
                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $res=$this->leadorder_model->shiptrack_trackcode($shiptracks, $postdata, $shiptraccodes);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $tracking=$res['tracking'];
                        $mdata['content']=$this->load->view('leadorderdetails/track_coderesult_view', $tracking,TRUE);
                        // Change Package View
                        $poptions=array(
                            'package'=>$res['package'],
                            'shipaddr'=>$postdata['shipaddres'],
                            'deliveries'=>$this->config->item('delivery_service'),
                        );
                        $mdata['packageview']=$this->load->view('leadorderdetails/track_package_view', $poptions, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Message Change
    public function shiptrackmessage_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                usersession($ordersession, $leadorder);
                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);
                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $res=$this->leadorder_model->shiptrack_changemessage($shiptracks, $postdata, $shiptraccodes);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        if ($postdata['field']=='senddata' && $postdata['newval']==1) {
                            $mdata['email_view']=$this->load->view('leadorderdetails/track_email_view', $shiptracks['email'],TRUE);
                        }
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Send message
    public function shiptrackmessage_send() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            //
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);

            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                usersession($ordersession, $leadorder);

                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);

                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $edit_mode=$this->input->post('edit_mode');
                    $res=$this->leadorder_model->shiptrack_sendcodes($shiptracks, $leadorder, $edit_mode, $ordersession, $shiptraccodes);

                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        // Make a new shipcontent
                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        // Shipping Date        
                        if ($order['shipdate']==0) {
                            $shipoption=array(
                                'label'=>'Not Yet Shipped',
                                'class'=>'open',
                            );
                        } else {
                            $shipoption=array(
                                'label'=>'To Ship '.date('m/d/y', $order['shipdate']),
                                'shipdate'=>date('m/d/y', $order['shipdate']),
                                'class'=>'open',
                            );
                        }
                        $mdata['shipstatus']=$this->load->view('leadorderdetails/shipdate_data_view', $shipoption, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Save Ship Track part
    public function shiptrack_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            //
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                usersession($ordersession, $leadorder);

                $shiptraccodes=$postdata['shiptraccodes'];
                $shiptracks=usersession($shiptraccodes);

                if (empty($shiptracks)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $edit_mode=$this->input->post('edit_mode');

                    $res=$this->leadorder_model->shiptrack_savetrackcodes($shiptracks, $leadorder, $edit_mode, $ordersession, $shiptraccodes);

                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $leadorder=usersession($ordersession);
                        // $order=$leadorder['order'];
                        // Shipping Date        
                        $shipstatus=$this->leadorder_model->_leadorderview_shipping_status($leadorder);
                        $shipoption=array(
                            'label'=>$shipstatus['order_status'],
                            'class'=>$shipstatus['order_status_class'],
                        );
                        $mdata['shipstatus']=$this->load->view('leadorderdetails/shipdate_data_view', $shipoption, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Multi Address Popup View / Edit
    public function multishipview() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $postdata=$this->input->post();
                $edit=(isset($postdata['edit']) ? $postdata['edit'] : 0);
                $manage=(isset($postdata['manage']) ? $postdata['manage'] : 1);
                $order=$leadorder['order'];
                $order_qty=$order['order_qty'];
                $shipping=$leadorder['shipping'];

                $shipping_address=$leadorder['shipping_address'];
                if (count($shipping_address)==1) {
                    $shipping_address[0]['item_qty']=$order_qty;
                }
                $rushlist=$shipping['out_rushlist'];
                $rushoptions=array(
                    'edit'=>$edit,
                );
                if (isset($rushlist['rush'])) {
                    $rushoptions['rush']=$rushlist['rush'];
                    $rushoptions['current']=$shipping['rush_idx'];
                    $rushoptions['shipdate']=$shipping['shipdate'];
                } else {
                    $rushoptions['rush']=array();
                    $rushoptions['current']='';
                    $rushoptions['shipdate']='';
                }

                $rushview=$this->load->view('leadorderdetails/rushlist_view', $rushoptions, TRUE);

                // Start collect shipaddress

                $shipaddrview=$this->_build_shippadress_view($shipping_address, $shipping, $order_qty, $edit);
                $shipstotal=$this->_build_shiptotals_view($shipping_address, $order_qty);
                $session_id='multiship'.uniq_link(15);
                $options=array(
                    'shipping'=>$shipping,
                    'edit'=>$edit,
                    'rushview'=>$rushview,
                    'shipaddrview'=>$shipaddrview,
                    'order_qty'=>$order_qty,
                    'numadderss'=>count($shipping_address),
                    'totals_view'=>$shipstotal,
                    'manage'=>$manage,
                    'shipsession'=>$session_id,
                );

                $mdata['content']=$this->load->view('leadorderdetails/multiship_popup_view', $options, TRUE);

                if ($edit==1) {
                    // Add session for manipulate
                    $multishipping=array(
                        'shipping'=>$shipping,
                        'shipping_address'=>$shipping_address,
                        'order'=>$order,
                        'order_items'=>$leadorder['order_items'],
                        'delrecords'=>$leadorder['delrecords'],
                    );
                    usersession($session_id,$multishipping);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_multishiporder_item() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                usersession($ordersession, $leadorder);
                $shipsession=$postdata['shipsession'];
                $multishipping=usersession($shipsession);
                if (empty($multishipping)) {
                    $error=$this->restore_orderdata_error;
                } else {
                    $entity=$postdata['entity'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    if ($entity=='shipping' && $fldname=='event_date' && $newval!='') {
                        $newval=strtotime($newval);
                    }
                    $res=$this->leadorder_model->change_multishiporder_input($multishipping, $entity, $fldname, $newval, $shipsession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $multishipping=usersession($shipsession);
                        $shipping_address=$multishipping['shipping_address'];
                        $order=$multishipping['order'];
                        $order_qty=$order['order_qty'];
                        if ($fldname=='rush_idx' && $entity=='shipping') {
                            $shipping=$multishipping['shipping'];
                            $mdata['shipdate']=date('m/d/Y',$order['shipdate']);
                            $mdata['shipcontent']=$this->_build_shippadress_view($shipping_address, $shipping, $order_qty, 1);
                            $mdata['rush_price']=$shipping['rush_price'];
                        }
                    }
                    $mdata['total_view']=$this->_build_shiptotals_view($shipping_address, $order_qty);
                    $mdata['save_view']=$this->_checksaveview($shipping_address, $order_qty);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Add new shipping address
    public function multiship_addaddress() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                usersession($ordersession, $leadorder);
                $shipsession=$postdata['shipsession'];
                $multishipping=usersession($shipsession);
                if (empty($multishipping)) {
                    $error=$this->restore_orderdata_error;
                } else {
                    $res=$this->leadorder_model->multiship_addadres($multishipping, $shipsession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $shipping_address=$res['shipping_address'];
                        $mdata['numaddress']=count($shipping_address).' Addresses';
                        $shipping=$multishipping['shipping'];
                        $order=$multishipping['order'];
                        $order_qty=$order['order_qty'];
                        $mdata['shipcontent']=$this->_build_shippadress_view($shipping_address, $shipping, $order_qty, 1);
                        $mdata['total_view']=$this->_build_shiptotals_view($shipping_address, $order_qty);
                        $mdata['save_view']=$this->_checksaveview($shipping_address, $order_qty);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Remove address 
    public function remove_multiship_address() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                usersession($ordersession, $leadorder);
                $shipsession=$postdata['shipsession'];
                $multishipping=usersession($shipsession);
                if (empty($multishipping)) {
                    $error=$this->restore_orderdata_error;
                } else {
                    $shipadr_id=$this->input->post('shipadr');
                    $res=$this->leadorder_model->multiship_removeadres($multishipping, $shipadr_id, $shipsession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $shipping_address=$res['shipping_address'];
                        $shipping=$multishipping['shipping'];
                        $order=$multishipping['order'];
                        $order_qty=$order['order_qty'];
                        $mdata['shipcontent']=$this->_build_shippadress_view($shipping_address, $shipping, $order_qty, 1);
                        $mdata['total_view']=$this->_build_shiptotals_view($shipping_address, $order_qty);
                        $mdata['numaddress']=count($shipping_address).' Addresses';
                        $mdata['save_view']=$this->_checksaveview($shipping_address, $order_qty);
                    }

                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_multiship_adrress() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                usersession($ordersession, $leadorder);

                $shipsession=$postdata['shipsession'];
                $multishipping=usersession($shipsession);
                if (empty($multishipping)) {
                    $error=$this->restore_orderdata_error;
                } else {
                    $postdata=$this->input->post();
                    $shipadr=$postdata['shipadr'];
                    $fldname=$postdata['fldname'];
                    $newval=$postdata['newval'];
                    $order=$multishipping['order'];
                    $res=$this->leadorder_model->change_multishiporder_address($multishipping, $shipadr, $fldname, $newval, $order['brand'], $shipsession);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    } else {
                        $mdata['is_calc']=0;
                        $mdata['taxdata']=0;
                        $multishipping=usersession($shipsession);
                        $shipping_address=$multishipping['shipping_address'];
                        $order_qty=$order['order_qty'];
                        $mdata['total_view']=$this->_build_shiptotals_view($shipping_address, $order_qty);
                        $adridx=0;
                        foreach ($shipping_address as $adrrow) {
                            if ($adrrow['order_shipaddr_id']==$shipadr) {
                                break;
                            } else {
                                $adridx++;
                            }
                        }
                        $srow=$shipping_address[$adridx];
                        $mdata['city']=$srow['city'];
                        $mdata['state_id']=$srow['state_id'];
                        // Build Content
                        $mdata['shiprate']=number_format($srow['shipping'],2);
                        $mdata['sales_tax']=number_format($srow['sales_tax'],2);
                        if (($fldname=='item_qty' || $fldname=='zip') && $res['shipcalc']==1) {
                            $mdata['is_calc']=1;
                            $shipcost=$srow['shipping_costs'];
                            $costoptions=array(
                                'shipadr'=>$srow['order_shipaddr_id'],
                                'shipcost'=>$shipcost,
                                'costname'=>'shippingrate'.$srow['order_shipaddr_id'],
                            );
                            $mdata['cost_view']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions,TRUE);
                        }
                        if ($fldname=='shipping_method') {
                            $mdata['shipping_cost_id']=$res['shipcost_id'];
                            // $mdata['shiprate']=$res['shiprate'];                        
                        }
                        if ($fldname=='country_id') {
                            $states=$res['state_list'];
                            if (count($states)==0) {
                                $mdata['stateview']='&nbsp;';
                            } else {
                                $stateoptions=array(
                                    // 'shipadr'=>$res['shipadr'],
                                    'shipadr'=>$srow,
                                    'states'=>$res['state_list'],
                                );
                                $mdata['stateview']=$this->load->view('leadorderdetails/shipping_state_select', $stateoptions, TRUE);
                            }
                        }
                        $shipaddr=$srow;
                        if ($shipaddr['taxview']==0) {
                            $taxview=$this->load->view('leadorderdetails/tax_empty_view', array(), TRUE);
                        } else {
                            $taxview=$this->load->view('leadorderdetails/tax_data_edit', $shipaddr, TRUE);
                        }
                        $mdata['taxview']=$taxview;
                        $mdata['taxdata']=1;

                        $mdata['save_view']=$this->_checksaveview($shipping_address, $order_qty);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Multishipping
    public function multiship_save() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = '';
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres = $this->_lockorder($leadorder);
                if ($locres['result'] == $this->error_result) {
                    $leadorder = usersession($ordersession, NULL);
                    $error = $locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                usersession($ordersession, $leadorder);
                $shipsession = $postdata['shipsession'];
                $multishipping = usersession($shipsession);
                if (empty($multishipping)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $res = $this->leadorder_model->multiship_save($multishipping, $leadorder, $ordersession, $shipsession);
                    $error = $res['msg'];
                    if ($res['result'] == $this->success_result) {
                        $error='';
                        $this->load->model('shipping_model');
                        $countries = $this->shipping_model->get_countries_list(array('orderby' => 'sort'));
                        $leadorder = usersession($ordersession);
                        // Other
                        $shipping = $leadorder['shipping'];
                        $shipping_address = $leadorder['shipping_address'];
                        $order = $leadorder['order'];
                        $edit = 1;
                        // Prepare content
                        $rushlist = $shipping['out_rushlist'];
                        $rushoptions = array(
                            'edit' => $edit
                        );
                        if (isset($rushlist['rush'])) {
                            $rushoptions['rush'] = $rushlist['rush'];
                            $rushoptions['current'] = $shipping['rush_idx'];
                            $rushoptions['shipdate']=$shipping['shipdate'];
                        } else {
                            $rushoptions['rush'] = array();
                            $rushoptions['current'] = '';
                        }

                        $rushview = $this->load->view('leadorderdetails/rushlist_view', $rushoptions, TRUE);
                        if (count($shipping_address) == 1) {
                            $shipcost = $shipping_address[0]['shipping_costs'];
                            if ($edit == 0) {
                                $cost_view = $this->load->view('leadorderdetails/ship_cost_view', array('shipcost' => $shipcost), TRUE);
                            } else {
                                $costoptions = array(
                                    'shipadr' => $shipping_address[0]['order_shipaddr_id'],
                                    'shipcost' => $shipcost,
                                );
                                $cost_view = $this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                            }
                            $country_id = $shipping_address[0]['country_id'];
                            if ($shipping_address[0]['taxview'] == 1) {
                                $taxview = $this->load->view('leadorderdetails/tax_data_edit', $shipping_address[0], TRUE);
                            } else {
                                $taxview = $this->load->view('leadorderdetails/tax_empty_view', array(), TRUE);
                            }
                            $states = $this->shipping_model->get_country_states($country_id);
                            $shipoptions = array(
                                'shipping' => $shipping,
                                'countries' => $countries,
                                'states' => $states,
                                'shipadr' => $shipping_address[0],
                                'shipcostview' => $cost_view,
                                'order' => $order,
                                'rushview' => $rushview,
                                'taxview' => $taxview,
                            );
                            $shippingview = $this->load->view('leadorderdetails/single_ship_edit', $shipoptions, TRUE);
                        } else {
                            $shippingview = $this->_build_multiship_view($shipping_address, $rushview, $order, $shipping);
                        }
                        $mdata['content'] = $shippingview;
                        // New 
                        $mdata['order_revenue']=MoneyOutput($order['revenue']);
                        $mdata['shipdate']=$shipping['shipdate'];
                        $mdata['rush_price']=$shipping['rush_price'];
                        $mdata['is_shipping']=$order['is_shipping'];
                        $mdata['shipping']=$order['shipping'];
                        $mdata['cntshipadrr']=count($shipping_address);
                        $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                        $mdata['item_subtotal']=MoneyOutput($subtotal);
                        $total_due=$order['revenue']-$order['payment_total'];
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        $dueoptions=array(
                            'totaldue'=>$total_due,
                        );
                        if ($total_due==0 && $order['payment_total']>0) {
                            $dueoptions['class']='closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class']='open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due']=$this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                        $mdata['tax']=MoneyOutput($order['tax']);
                        $mdata['profit_content']=$this->_profit_data_view($order);
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $dateoptions=array(
                            'edit'=>1,
                            'shipping'=>$shipping,
                            'user_role' => $this->USR_ROLE,
                        );
                        $mdata['shipdates_content']=$this->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function creditapp_lines() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('creditapp_model');
            $totals=$this->creditapp_model->get_creditapp_total();
            $options=array(
                'totals'=>$totals,
                'perpage'=>100,
            );
            $mdata['content']=$this->load->view('crediatapp/page_view', $options, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _build_shiptotals_view($shipping_address, $order_qty) {
        $totalqty=$totalship=$totaltax=0;
        foreach ($shipping_address as $srow) {
            $totalqty+=$srow['item_qty'];
            $totalship+=$srow['shipping'];
            $totaltax+=$srow['sales_tax'];
        }
        $totalclass='';
        if ($totalqty!=$order_qty) {
            $totalclass='error';
        }
        $options=array(
            'order_qty'=>$order_qty,
            'totalclass'=>$totalclass,
            'totalqty'=>$totalqty,
            'totalship'=>(empty($totalship) ? '&nbsp;' : MoneyOutput($totalship)),
            'totaltax'=>(empty($totaltax) ? '&nbsp;' : MoneyOutput($totaltax)),
        );
        $total_view=$this->load->view('leadorderdetails/multishipping_total_view',$options, TRUE);
        return $total_view;
    }

    private function _build_shippadress_view($shipping_address, $shipping, $order_qty, $edit) {
        $this->load->model('shipping_model');
        $countries=$this->shipping_model->get_countries_list();
        $numpp=1;
        $shipaddrview='';

        foreach ($shipping_address as $srow) {
            // if ($srow['delflag]==0) {
            // }
            $shipcost=$srow['shipping_costs'];
            if ($edit==0) {
                $cost_view=$this->load->view('leadorderdetails/ship_cost_view', array('shipcost'=>$shipcost),TRUE);
            } else {
                $costoptions=array(
                    'shipadr'=>$srow['order_shipaddr_id'],
                    'shipcost'=>$shipcost,
                    'costname'=>'shippingrate'.$srow['order_shipaddr_id'],
                );
                $cost_view=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions,TRUE);
            }
            $country_id=$srow['country_id'];
            if ($srow['taxview']==1) {
                if ($edit==1) {
                    $taxview=$this->load->view('leadorderdetails/tax_data_edit', $srow, TRUE);
                } else {
                    $taxview=$this->load->view('leadorderdetails/tax_data_view', $srow, TRUE);
                }
            } else {
                $taxview=$this->load->view('leadorderdetails/tax_empty_view', array(), TRUE);
            }
            $states=$this->shipping_model->get_country_states($country_id);
            $shipoptions=array(
                'shipping'=>$shipping,
                'countries'=>$countries,
                'states'=>$states,
                'shipadr'=>$srow,
                'shipcostview'=>$cost_view,
                'taxview'=>$taxview,
                'numpp'=>$numpp,
                'total_itemqty'=>$order_qty,
            );
            if ($edit==1) {
                $shipaddrview.=$this->load->view('leadorderdetails/multiship_addres_edit', $shipoptions, TRUE);
            } else {
                $shipaddrview.=$this->load->view('leadorderdetails/multiship_addres_view', $shipoptions, TRUE);
            }
            $numpp++;
        }
        if ($edit==1) {
            $shipaddrview.=$this->load->view('leadorderdetails/multiship_address_add', array(), TRUE);
        }
        return $shipaddrview;
    }

    private function _checksaveview($shipping_address, $order_qty) {
        $showview=0;
        $totalqty=0;
        foreach ($shipping_address as $row) {
            if (intval($row['item_qty'])>0 && floatval($row['shipping'])==0) {
                return $showview;
            }
            $totalqty+=$row['item_qty'];
        }
        if ($totalqty==$order_qty) {
            $showview=1;
        }
        return $showview;
    }

    public function prepare_invoice() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                $res=$this->leadorder_model->prepare_invoicedoc($leadorder, $this->USR_ID);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    $mdata['docurl']=$res['html_path'];
                    $mdata['balance_term'] = $leadorder['order']['balance_term'];
                    $mdata['balance_due'] = $leadorder['order']['credit_appdue'];
                }
                $mdata['content'] = $res;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _build_multiship_view($shipping_address, $rushview, $order, $shipping) {
        $cost_view = '';
        $numpp = 1;
        foreach ($shipping_address as $srow) {
            $srow['numpp'] = $numpp;
            $cost_view.=$this->load->view('leadorderdetails/shipping_datarow_view', $srow, TRUE);
            $numpp++;
        }
        $shipoptions = array(
            'shipping' => $shipping,
            'shipcostview' => $cost_view,
            'order' => $order,
            'rushview' => $rushview,
        );
        return $this->load->view('leadorderdetails/multi_ship_edit', $shipoptions, TRUE);
    }

    // Show popup for new payment
    public function paymentadd() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $newpay=array(
                    'type'=>'payment',
                    'replica'=>$this->USER_REPLICA,
                    'date'=>'',
                    'paytype'=>'',
                    'paynum'=>'',
                    'amount'=>0,
                );
                usersession('newpayment', $newpay);
                $mdata['content']=$this->load->view('leadorderdetails/newpayment_view', $newpay, TRUE);
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Edit payment details
    public function payment_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $payment=usersession('newpayment');
                if (empty($payment)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $postdata=$this->input->post();
                    $res=$this->leadorder_model->payment_edit($payment, $postdata);
                    if ($res['result']==$this->error_result) {
                        $error=$res['msg'];
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Save payment
    public function payment_save() {
        if ($this->isAjax()) {
            $mdata = array();
            $error = '';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);

            $leadorder = usersession($ordersession);

            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }

                $payment = usersession('newpayment');
                if (empty($payment)) {
                    $error = $this->restore_orderdata_error;
                } else {
                    $postdata = $this->input->post();

                    $payment['type'] = $postdata['paytype'];


                    $res = $this->leadorder_model->payment_save($leadorder, $payment, $ordersession);
                    if ($res['result'] == $this->error_result) {
                        $error = $res['msg'];
                    } else {
                        $leadorder = usersession($ordersession);
                        $payments = $leadorder['payments'];
                        $order = $leadorder['order'];
                        // Build view
                        $mdata['content'] = $this->load->view('leadorderdetails/payment_history_view', array('payments' => $payments), TRUE);
                        // Total
                        $total_due = $order['revenue'] - $order['payment_total'];
                        $mdata['ordersystem']=$leadorder['order_system'];
                        $mdata['balanceopen']=1;
                        $dueoptions = array(
                            'totaldue' => $total_due,
                        );
                        if ($total_due == 0 && $order['payment_total'] > 0) {
                            $dueoptions['class'] = 'closed';
                            $mdata['balanceopen']=0;
                        } else {
                            $dueoptions['class'] = 'open';
                            if ($total_due<0) {
                                $dueoptions['class']='overflow';
                            }
                        }
                        $mdata['total_due'] = $this->load->view('leadorderdetails/totaldue_data_view', $dueoptions, TRUE);
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function search_item() {
        $item_num=$this->input->get('term');
        $item_number=strtoupper($item_num);
        $get_dat=$this->leadorder_model->search_items($item_number);
        echo json_encode($get_dat);
    }

    // Update data about locked for Edit
    public function updatelockedorder() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $locrecid=$this->input->post('locrecid');
            $this->load->model('engaded_model');
            $res=$this->engaded_model->update_lockedid($locrecid);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Clean locked record
    public function cleanlockedorder() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $locrecid=$this->input->post('locrecid');
            $this->load->model('engaded_model');
            $this->engaded_model->clean_engade($locrecid);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function viewimprintloc() {
        $imprint_loc_id=$this->input->get('id');
        $res=$this->leadorder_model->get_leadorder_imprintloc($imprint_loc_id);
        if ($res['result']==$this->error_result) {
            die($res['msg']);
        }
        $viewopt=$res['viewoptions'];
        $content=$this->load->view('redraw/viewsource_view',$viewopt, TRUE);
        echo $content;
    }

    public function leadorderinv_prepare() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                $res=$this->leadorder_model->prepare_orderinvemail($leadorder, $this->USR_ID);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                } else {
                    // $sender = $this->config->item('customer_notification_sender');                    
                    $data=$res['data'];
                    $sender = $data['sender'];
                    $options = array(
                        'order_id' => $data['order_id'],
                        'from' => $sender,
                        'tomail' => $data['contact_mail'],
                        'subject' => $data['subject'],
                        'message' => $data['message'],
                    );
                    $mdata['content'] = $this->load->view('leadorderdetails/invoice_preemail_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Send Invoice 
    public function sendinvoice() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                $data = $this->input->post();

                $res = $this->leadorder_model->send_invoicemail($data, $leadorder, $this->USR_ID, $ordersession);

                if ($res['result'] == $this->error_result) {
                    $error = $res['msg'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _lockorder($leadorder) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->locktimeout);
        if (!isset($leadorder['locrecid'])) {
            $out['result']=$this->success_result;
        } elseif ($leadorder['locrecid']==0) {
            $out['result']=$this->success_result;
        } else {
            $locrecid=$leadorder['locrecid'];
            $res=$this->engaded_model->update_lockedid($locrecid);
            if ($res['result']==$this->success_result) {
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function extend_edittime() {
        if ($this->isAjax()) {
            // Extend Edit Time
            $postdata = $this->input->post();
            $ordersession=ifset($postdata, 'ordersession','defsession');
            $leadorder=usersession($ordersession);
            $mdata=[];
            $error = $this->restore_orderdata_error;
            if (!empty($leadorder)) {
                $mdata['content']=$this->load->view('leadorderdetails/extent_edittime_view', array(),TRUE);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function extendtime_order() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record                
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Order    
    public function leadorder_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $this->load->model('leadorder_model');

                $res=$this->leadorder_model->save_order($leadorder, $this->USR_ID, $ordersession);
                $error=$res['msg'];

                if ($res['result']==$this->success_result) {
                    $error='';
                    if (isset($postdata['locrecid'])) {
                        $this->engaded_model->clean_engade($postdata['locrecid']);
                    }
                    $callpage='leadorder';
                    if (isset($postdata['callpage'])) {
                        $callpage=$postdata['callpage'];
                    }
                    $mdata['color']=0;
                    if ($callpage=='inventory') {
                        // Try to restore Printshop color
                        $printshop=usersession('inventleadorder');
                        if (!empty($printshop)) {
                            $mdata['color']=$printshop['inventcolor'];
                        }
                    }
                    // Get Data about Engaded records
                    $order_id=$res['order_id'];
                    $mdata['newplaceorder'] = $res['newplaceorder'];
                    if ($res['newplaceorder']==1) {
                        $mdata['popupmsg']=$res['popupmsg'];
                        $mdata['finerror'] = $res['finerror'];
                    }
                    $brand = ifset($postdata, 'brand', 'ALL');
                    $res=$this->leadorder_model->get_leadorder($order_id, $this->USR_ID, $brand);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        // Build Head                    
                        $engade_res=$this->engaded_model->check_engade(array('entity'=>'ts_orders','entity_id'=>$order_id));
                        $res['unlocked']=$engade_res['result'];
                        $orddata=$res['order'];
                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE,0);

                        $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                        // Build Content
                        $options['order_data']=$order_data;
                        $options['order_system']=$res['order_system_type'];
                        $options['leadsession']=$ordersession;
                        // $options['current_page']='orders';
                        $options['current_page'] = $callpage;
                        $options['unlocked'] = $engade_res['result'];
                        $mdata['content']=$this->load->view('leadorderdetails/top_menu_view',$options, TRUE);
                        $head_options = [
                            'order_head' => $this->load->view('leadorderdetails/head_order_view', $orddata,TRUE),
                            'prvorder' => $res['prvorder'],
                            'nxtorder' => $res['nxtorder'],
                            'order_id' => $orddata['order_id'],
                            'unlocked' => $engade_res['result'],
                        ];
                        if ($engade_res['result']==$this->error_result) {
                            $voptions=array(
                                'user'=>$engade_res['lockusr'],
                            );
                            // $options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                            $head_options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                        } elseif ($orddata['is_canceled']==1) {
                            $head_options['unlocked']=$this->error_result;
                            $head_options['editbtnview']=$this->load->view('leadorderdetails/ordercanceled_view', array(), TRUE);
                        }
                        $head_options['order_dublcnum']=$orddata['order_num'];
                        $mdata['header'] = $this->load->view('leadorderdetails/head_view', $head_options, TRUE);

                        /* Save to session */
                        $leadorder=array(
                            'order'=>$orddata,
                            'payments'=>$res['payments'],
                            'artwork'=>$res['artwork'],
                            'artlocations'=>$res['artlocations'],
                            'artproofs'=>$res['proofdocs'],
                            'message'=>$res['message'],
                            'contacts'=>$res['contacts'],
                            'order_items'=>$res['order_items'],
                            'order_system'=>$res['order_system_type'],
                            'shipping'=>$res['shipping'],
                            'shipping_address'=>$res['shipping_address'],
                            'billing'=>$res['order_billing'],
                            'charges'=>$res['charges'],
                        );
                        usersession($ordersession, $leadorder);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function show_update_details() {
        if ($this->isAjax()) {
            $artwork_history_id=$this->input->post('artwork_history_id');
            $mdata=array();
            $error='Empty History Content';
            if ($artwork_history_id) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->get_updatehistory_details($artwork_history_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $options=array(
                        'head'=>$res['head'],
                        'details'=>$res['details'],
                    );
                    $mdata['content']=$this->load->view('artpage/update_details_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

// Show Charge Attempts
    public function show_charge_attempts() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Order #';
            $order_id=$this->input->post('order_id');
            if (!empty($order_id)) {
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->get_charges_attempts($order_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $options=array(
                        'data'=>$res['data'],
                    );
                    $mdata['content']=$this->load->view('leadorderdetails/charge_attempts_log', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function ordercogdetails() {
        $postdata=$this->input->get();
        $order_id=(isset($postdata['ord']) ? intval($postdata['ord']) : 0);
        $cogcontent='<div class="error">Order Not Found</div>';
        if (!empty($order_id)) {

            $this->load->model('leadorder_model');
            $res=$this->leadorder_model->get_leadorder_amounts($order_id);
            $options=array(
                'data'=>$res,
                'profit_class'=>$postdata['clas'],
                'edit_mode' => (ifset($postdata,'edit',1)),
            );
            $cogcontent=$this->load->view('leadorderdetails/ordercog_details_view', $options, TRUE);
        }
        echo $cogcontent;
    }

    private function _profit_data_view($order, $edit_mode=1) {
        $usrdat=$this->user_model->get_user_data($this->USR_ID);
        $options=array(
            'profit_perc'=>$order['profit_perc'],
            'profit'=>$order['profit'],
            'profit_view'=>'',
            'profit_class'=>orderProfitClass($order['profit_perc']),
            'order_id'=>$order['order_id'],
            'bgcolor' => '#FFFFFF',
            'hitcolor' => '#000000',
            'edit_mode' => $edit_mode,
        );
        if (!empty($order['profit_perc'])) {
            $classprof = orderProfitClass($order['profit_perc']);
            if ($classprof=='green') {
                $options['bgcolor']='#00e947';
            } elseif ($classprof=='red') {
                $options['bgcolor']='#ff0000';
                $options['hitcolor']='#ffffff';
            } elseif ($classprof=='black') {
                $options['bgcolor']='#000000';
                $options['hitcolor']='#ffffff';
            } elseif ($classprof=='orange') {
                $options['bgcolor']='#ea8a0e';
            } elseif ($classprof=='moroon') {
                $options['bgcolor']='#6d0303';
                $options['hitcolor']='#ffffff';
            }
        }
        if ($usrdat['profit_view']=='Points') {
            $options['profit']=round($order['profit']*$this->config->item('profitpts'),0).' pts';
            $options['profit_view']='points';
        }
        if (empty($order['profit_perc'])) {
            $content=$this->load->view('leadorderdetails/profitproject_view', $options, TRUE);;
        } else {
            if ($options['profit_view']=='points') {
                $content=$this->load->view('leadorderdetails/profit_points_view', $options, TRUE);
            } else {
                $content=$this->load->view('leadorderdetails/profit_view', $options, TRUE);
            }
        }
        return $content;
    }

    private function _shipwarning_confirm_view($options) {
//        $oldshipcost, $newshipcost
//        $options=array(
//            'oldship'=>$oldshipcost,
//            'newship'=>$newshipcost,
//        );
        $content=$this->load->view('leadorderdetails/shipcost_warning_view', $options, TRUE);
        return $content;
    }
    // Check Order total and payments total
    public function leadorder_place() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->check_neworder_payment($leadorder);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                } else {
                    $mdata['fin']=$res['fin'];
                    if ($res['fin']==1) {
                        // Build popup content
                        $options=[
                            'msg'=>$error,
                        ];
                        $mdata['content']=$this->load->view('leadorderdetails/payment_warning_view', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change payment to new order total
    public function leadorder_change_paymenttotal() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->change_neworder_payment($leadorder, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _leadorder_locktime() {
        if ($this->input->ip_address()=='127.0.0.1') {
            $timeout=(time()+$this->config->item('loctimeout_local'))*1000;
        } else {
            $timeout=(time()+$this->config->item('loctimeout'))*1000;
        }
        return $timeout;
    }

    public function change_leadorder_rushpast() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $newval=strtotime($postdata['newval']);
                $res = $this->leadorder_model->change_order_rushpast($leadorder, $newval, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leadorder=usersession($ordersession);
                    $shipping=$leadorder['shipping'];
                    $shipping_address=$leadorder['shipping_address'];
                    $mdata['shipping_cost'] = $leadorder['order']['shipping'];
                    $mdata['cntshipadrr']=count($shipping_address);
                    /* Rush */
                    $rushlist=$res['rushlist'];
                    $rushopt=array(
                        'edit'=>1,
                        'rush'=>$rushlist['rush'],
                        'current'=>$res['current'],
                        'shipdate'=>$shipping['shipdate'],
                    );
                    $mdata['rushview']=$this->load->view('leadorderdetails/rushlist_view', $rushopt, TRUE);
                    $dateoptions=array(
                        'edit'=>1,
                        'shipping'=>$shipping,
                        'user_role' => $this->USR_ROLE,
                    );
                    $mdata['shipdates_content']=$this->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
                    if ($mdata['cntshipadrr']==1) {
                        // Buld rate view
                        $shipcost=$shipping_address[0]['shipping_costs'];
                        $costoptions=array(
                            'shipadr'=>$shipping_address[0]['order_shipaddr_id'],
                            'shipcost'=>$shipcost,
                        );
                        $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                        $mdata['shipaddress']=$shipping_address[0]['order_shipaddr_id'];
                    } else {
                        $cost_view = '';
                        $numpp = 1;
                        foreach ($shipping_address as $srow) {
                            $srow['numpp'] = $numpp;
                            $cost_view.=$this->load->view('leadorderdetails/shipping_datarow_view', $srow, TRUE);
                            $numpp++;
                        }
                        $mdata['shipcost']=$cost_view;
                    }
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_leadorder_arrivepast() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $newval=strtotime($postdata['newval']);
                $res = $this->leadorder_model->change_order_arrivepast($leadorder, $newval, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leadorder=usersession($ordersession);
                    $shipping=$leadorder['shipping'];
                    $dateoptions=array(
                        'edit'=>1,
                        'shipping'=>$shipping,
                        'user_role' => $this->USR_ROLE,
                    );
                    $mdata['shipdates_content']=$this->load->view('leadorderdetails/shipping_dates_edit', $dateoptions, TRUE);
                }
            }
            // Calc new period for lock
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    public function pototal_remove() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $amount = ifset($postdata,'amount',0);
                $editmode = ifset($postdata,'editmode',0);
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->remove_amount($amount, $this->USR_ID, $editmode, $leadorder, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $leadorder = usersession($ordersession);
                    $order = $leadorder['order'];
                    $mdata['content']=$this->_profit_data_view($order,0);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function pototal_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $amount = ifset($postdata,'amount',0);
                $editmode = ifset($postdata,'editmode',0);
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->edit_amount($amount, $this->USR_ID, $editmode, $leadorder, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $this->load->model('vendors_model');
                    $this->load->model('orders_model');
                    $v_options = [
                        'order_by' => 'v.vendor_name',
                    ];
                    $vendors=$this->vendors_model->get_vendors_list($v_options);
                    $methods=$this->orders_model->get_methods_edit();
                    $order_view=$this->load->view('pototals/purchase_orderdata_view', $res['order'],TRUE);
                    $poeditview = $this->load->view('pototals/purchase_reason_view', $res['amount'],TRUE);
                    $lowprofit_view = '';
                    if (!empty($res['order']['reason'])) {
                        $lowprofit_view = $this->load->view('pototals/lowprofit_reason_view', $res['order'],TRUE);
                    }
                    $options=array(
                        'order'=>$res['order'],
                        'amount'=>$res['amount'],
                        'attach'=>'',
                        'vendors'=>$vendors,
                        'methods'=>$methods,
                        'order_view'=>$order_view,
                        'lowprofit_view'=>$lowprofit_view,
                        'editpo_view'=>$poeditview,
                    );
                    $content=$this->load->view('pototals/purchase_orderedit_view',$options,TRUE);
                    $mdata['content']=$content;
                    $data=array(
                        'amount'=>$res['amount'],
                        'order'=>$res['order'],
                        'attach'=>array(),
                    );
                    // Save Data to Session
                    usersession('editpurchase', $data);
                    // $mdata['content']=$this->_profit_data_view($order);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function amount_save() {
        if ($this->isAjax()) {
            $mdata = [];
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $amntdata=usersession('editpurchase');
                if (!empty($amntdata)) {
                    $editmode = ifset($postdata,'editmode',0);
                    $this->load->model('leadorder_model');
                    $res=$this->leadorder_model->amount_save($amntdata, $this->USR_ID, $editmode, $leadorder, $ordersession);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $leadorder = usersession($ordersession);
                        $order = $leadorder['order'];
                        $mdata['content']=$this->_profit_data_view($order, 0);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_imprint_details($leadorder, $newitem, $ordersession) {
        $out = ['result' => $this->error_result, 'msg' => 'Unknown Error'];
        $res=$this->leadorder_model->prepare_imprint_details($leadorder, $newitem, $ordersession);
        if ($res['result']==$this->error_result) {
            $out['msg']=$res['msg'];
        } else {
            $out['result'] = $this->success_result;
            $details = $res['imprint_details'];
            $order_blank = $res['order_blank'];
            $item_id = $res['item_id'];
            if ($order_blank == 0) {
                $chkactiv = 0;
                foreach ($details as $row) {
                    if ($row['active'] == 1) {
                        $chkactiv = 1;
                        break;
                    }
                }
                if ($chkactiv == 0) {
                    $details[0]['active'] = 1;
                }
            }
            // Prepare View
            $imptintid = 'imprintdetails' . uniq_link(15);
            $options = array(
                'details' => $details,
                'item_number' => $res['item_number'],
                'order_blank' => $order_blank,
                'imprints' => $res['imprints'],
                'numlocs' => count($res['imprints']),
                'item_name' => $res['item_name'],
                'imprintsession' => $imptintid,
                'custom' => ($res['item_id'] == $this->config->item('custom_id') || $res['item_id'] == $this->config->item('other_id')) ? 1 : 0,
                'brand' => $res['brand'],
            );
            $out['content'] = $this->load->view('leadorderdetails/imprint_details_edit', $options, TRUE);

            $imprintdetails = array(
                'imprint_details' => $details,
                'order_blank' => $order_blank,
                'order_item_id' => $newitem,
                'item_id' => $item_id,
                'brand' => $res['brand'],
            );
            usersession($imptintid, $imprintdetails);
        }
        return $out;
    }

    public function saveclaydocupload() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata=$this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $this->load->model('artlead_model');
                $res=$this->artlead_model->save_artclaydocs($leadorder, $postdata['claydoc'], $postdata['sourcename'] , $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $claydocs=$res['outdocs'];
                    $mdata['content']=leadClaydocOut($claydocs, 1);
                    $numdoc=ceil(count($claydocs)/4);
                    $mdata['claywidth']=$numdoc*115;
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function artclay_remove() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                $this->load->model('artlead_model');
                $res=$this->artlead_model->remove_artclaydocs($leadorder, $postdata['clayid'], $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $claydocs=$res['outdocs'];
                    $mdata['content']=leadClaydocOut($claydocs, 1);
                    $mdata['claywidth'] = ceil(count($claydocs)/4)*115;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showclaymodels() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                $claydocs = $leadorder['claydocs'];
                $error = 'Any Clay Models Found';
                if (count($claydocs) > 0) {
                    $error = '';
                    $mdata['clays'] = $claydocs;
                }
                usersession($ordersession, $leadorder);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function savepreviewdocupload() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata=$this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $this->load->model('artlead_model');
                $res=$this->artlead_model->save_artpreviewdocs($leadorder, $postdata['previewdoc'], $postdata['sourcename'] , $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $previewdocs=$res['outdocs'];
                    $mdata['content']=leadPreviewdocOut($previewdocs, 1);
                    $mdata['previewwidth'] = ceil(count($previewdocs)/4)*115;
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function artpreview_remove() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                $this->load->model('artlead_model');
                $res=$this->artlead_model->remove_artpreviewdocs($leadorder, $postdata['previewid'], $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $previewdocs=$res['outdocs'];
                    $mdata['content']=leadPreviewdocOut($previewdocs, 1);
                    $mdata['previewwidth'] = ceil(count($previewdocs)/4)*115;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showpreviewpics() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                $previewdocs = $leadorder['previewdocs'];
                $error = 'Any Preview Pictures Found';
                if (count($previewdocs) > 0) {
                    $error = '';
                    $mdata['previews'] = $previewdocs;
                }
                usersession($ordersession, $leadorder);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}