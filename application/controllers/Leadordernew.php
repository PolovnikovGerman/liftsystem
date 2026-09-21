<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadordernew extends MY_Controller
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

    public function index() {}

    // Change common data - not need recalc, etc
    public function change_order_commondata()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession = ifset($postdata, 'ordersession', 0);

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
                        $mdata['freshship'] = $mdata['freshbill'] = 0;
                        if (isset($res['shipcompany'])) {
                            $mdata['freshship'] = 1;
                            $mdata['shipcompany'] = $res['shipcompany'];
                        }
                        if (isset($res['billcompany'])) {
                            $mdata['freshbill'] = 1;
                            $mdata['billcompany'] = $res['billcompany'];
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

    // Imprints for new item
    public function neworderitemimprints()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $ordersession= ifset($postdata, 'ordersession','unkn');
            $leadorder = usersession($ordersession);
            if (empty($leadorder)) {
                $error=$this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres = $this->_lockorder($leadorder);
                if ($locres['result'] == $this->error_result) {
                    $leadorder = usersession($ordersession, NULL);
                    $error = $locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $orderitem_id = ifset($postdata, 'orderitem_id', 0);
                $imprdata = $this->_prepare_imprint_details($leadorder, $orderitem_id, $ordersession, 'new');
                $error = $imprdata['msg'];
                if ($imprdata['result']==$this->success_result) {
                    $error = '';
                    $mdata['imprintview'] = $imprdata['content'];
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);

        }
        show_404();
    }

    // Save Imprint details
    public function save_imprintdetails()
    {
        if ($this->isAjax()) {
            $mdata=array();
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
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leadorder = usersession($ordersession);
                    $items = $leadorder['order_items'];
                    $order = $leadorder['order'];
                    // Add items list
                    $this->load->model('orders_model');
                    $dboptions=array(
                        'exclude'=>array(-4, -5, -2),
                        'brand' => ($leadorder['order']['brand']=='SR') ? 'SR' : 'BT',
                    );
                    $itemslist = $this->orders_model->get_item_list($dboptions);
                    $mdata['itemsview'] = $this->load->view('leadordernew/items_data_edit', ['items' => $items, 'itemslist' => $itemslist], TRUE);
                    // New revenue
                    $mdata['order_revenue']=MoneyOutput($order['revenue']);
                    // $mdata['shipdate']=$shipping['shipdate'];
                    // $mdata['rush_price']=$shipping['rush_price'];
                    // $mdata['is_shipping']=$order['is_shipping'];
                    // ???
                    $mdata['shipping']=$order['shipping'];
                    // $mdata['cntshipadrr']=count($shipping_address);
                    // New Item subtotal
                    $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                    $mdata['item_subtotal']=MoneyOutput($subtotal);
                    // New Balance Due link
                    $total_due=$order['revenue']-$order['payment_total'];
                    $dueoptions=array(
                        'totaldue'=>$total_due,
                    );
                    if ($order['brand']=='SR') {
                        $dueoptions['checkoutlink']=$this->config->item('srcheckoutlink').$order['checkout_link'];
                    } else {
                        $dueoptions['checkoutlink']=$this->config->item('btcheckoutlink').$order['checkout_link'];;
                    }
                    if ($total_due==0 && $order['payment_total']>0) {
                        $dueoptions['class']='closed';
                    } else {
                        $dueoptions['class']='open';
                        if ($total_due<0) {
                            $dueoptions['class']='overflow';
                        }
                    }
                    $mdata['total_due']=$this->load->view('leadordernew/balancedue_data_view', $dueoptions, TRUE);
                    // Tax
                    $mdata['tax']=MoneyOutput($order['tax']);

                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Prepare Item Imprints
    private function _prepare_imprint_details($leadorder, $newitem, $ordersession, $itemstatus='old') {
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
            $out['content'] = $this->load->view('leadordernew/imprint_details_edit', $options, TRUE);

            $imprintdetails = array(
                'imprint_details' => $details,
                'order_blank' => $order_blank,
                'order_item_id' => $newitem,
                'item_id' => $item_id,
                'brand' => $res['brand'],
                'itemstatus' => $itemstatus,
            );
            usersession($imptintid, $imprintdetails);
        }
        return $out;
    }

    // Function update lock order
    private function _lockorder($leadorder) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->locktimeout);
        if (!isset($leadorder['locrecid'])) {
            $out['result']=$this->success_result;
        } elseif (intval($leadorder['locrecid'])==0) {
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

    // New Lock Time
    private function _leadorder_locktime() {
        if ($this->input->ip_address()=='127.0.0.1') {
            $timeout=(time()+$this->config->item('loctimeout_local'))*1000;
        } else {
            $timeout=(time()+$this->config->item('loctimeout'))*1000;
        }
        return $timeout;
    }

    public function show_update_details()
    {
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
                    $mdata['content']=$this->load->view('leadordernew/history_update_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showproofdoc()
    {
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
                $res = $this->artlead_model->show_atproofdocnew($leadorder, $artwork_proof_id, $ordersession);
                if ($res['result'] == $this->error_result) {
                    $error = $res['msg'];
                } else {
                    $proofdoc=$res['outproof'];
                    if ($proofdoc['artwork_proof_id']>0) {
                        $mdata['proofdocurl']=$proofdoc['proof_name'];
                    } else {
                        $fullpreload=$this->config->item('upload_path_preload');
                        $shpreload=$this->config->item('pathpreload');
                        $mdata['proofdocurl']=  str_replace($fullpreload,$shpreload, $proofdoc['proof_name']);
                    }
                    $mdata['proofdocname']=$proofdoc['source_name'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function send_checkout_invite()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $invite_name = ifset($postdata, 'invite_name','');
                $invite_email = ifset($postdata, 'invite_email','');
                $subject = ifset($postdata, 'subject', '');
                $msgoptions = [
                    'invite_name' => $invite_name,
                    'invite_email' => $invite_email,
                    'subject' => $subject,
                ];
                if (isset($postdata['cc_email']) && !empty($postdata['cc_email'])) {
                    $msgoptions['cc_email'] = $postdata['cc_email'];
//                    $msgoptions['cc_name'] = ifset($postdata, 'cc_name', '');
                }
                if (isset($postdata['bcc_email']) && !empty($postdata['bcc_email'])) {
                    $msgoptions['bcc_email'] = $postdata['bcc_email'];
//                    $msgoptions['bcc_name'] = ifset($postdata, 'bcc_name','');
                }
                $res=$this->leadorder_model->send_checkout_invite($leadorder, $msgoptions, $this->USR_ID, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $history = $res['history'];
                    $mdata['histoyview'] = $this->load->view('leadordernew/update_history_view', ['history' => $history], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}