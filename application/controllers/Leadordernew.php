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

    private $emptycontent = '&nbsp;';


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

    public function change_order_findata()
    {
        $mdata=array();
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
                $entity = $postdata['entity'];
                $fldname = $postdata['fldname'];
                $newval = floatval(str_replace('$', '', $postdata['newval']));

                $res=$this->leadorder_model->change_order_input($leadorder, $entity, $fldname, $newval, $ordersession);
                $error=$res['msg'];
                if (isset($res['old_value'])) {
                    $mdata['old_value']=$res['old_value'];
                }
                if ($res['result']==$this->success_result) {
                    $error='';
                    $leadorder = usersession($ordersession);
                    $order = $leadorder['order'];
                    $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                    $mdata['item_cost']=MoneyOutput($subtotal);
                    $mdata['revenue'] = MoneyOutput($order['revenue']);
                    $mdata['profitview'] = $this->_profit_data_view($order);
                }
            }
        }
        // Calc new period for lock
        $mdata['loctime']=$this->_leadorder_locktime();
        $this->ajaxResponse($mdata, $error);
    }

    // Imprints for new item
    public function neworderitemimprints()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $ordersession= ifset($postdata, 'ordersession','unkn');
            $leadorder = usersession($ordersession);
            $edit = ifset($postdata, 'edit',1);
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
                $imprdata = $this->_prepare_imprint_details($leadorder, $orderitem_id, $ordersession, $edit, 'new');
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
                    // ???
                    $mdata['shipping']=$order['shipping'];
                    // New Item subtotal
                    $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                    $mdata['item_subtotal']=MoneyOutput($subtotal);
                    // New Balance Due link
                    if ($order['brand']=='SR') {
                        $checkoutlink = $this->config->item('srcheckoutlink').$order['checkout_link'];
                    } else {
                        $checkoutlink = $this->config->item('btcheckoutlink').$order['checkout_link'];;
                    }
                    $mdata['total_due']=$this->load->view('leadordernew/balancedue_view', ['order' => $order, 'checkoutlink' => $checkoutlink], TRUE);
                    // Tax
                    $mdata['tax']=MoneyOutput($order['tax']);
                    $mdata['profitview'] = $this->_profit_data_view($order);
                    // Tracking
                    $shipstatus = $this->leadorder_model->_leadorderview_shipping_status($leadorder);
                    $mdata['tracking'] = $this->_prepare_tracking_content($leadorder['order_items'], $shipstatus, 1);
                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Prepare Item Imprints
    private function _prepare_imprint_details($leadorder, $newitem, $ordersession, $edit, $itemstatus='old') {
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
                'edit' => $edit,
            );
            $out['content'] = $this->load->view('leadordernew/imprint_details_edit', $options, TRUE);
            if ($edit == 1) {
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
        }
        return $out;
    }

    public function change_itemparams()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
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
                $entity = ifset($postdata, 'entity', '');
                if (empty($entity)) {
                    $error = 'Empty Needed Parameter';
                    $this->ajaxResponse($mdata, $error);
                }
                $oldshipcost = $leadorder['order']['shipping'];
                $mdata['fldtype'] = $entity;
                if ($entity == 'item') {
                    if (!isset($postdata['fldname']) || !isset($postdata['item']) || !isset($postdata['newval']) || !isset($postdata['order_item'])) {
                        $error = 'Empty Needed Parameter';
                        $this->ajaxResponse($mdata, $error);
                    }
                    $fldname = $postdata['fldname'];
                    $item_id = $postdata['item'];
                    $newval = $postdata['newval'];
                    $order_item_id = $postdata['order_item'];
                    $mdata['item'] = $item_id;
                    $mdata['order_item'] = $order_item_id;
                    $res = $this->leadorder_model->change_items($leadorder, $order_item_id, $item_id, $fldname, $newval, $ordersession);
                    $error = $res['msg'];
                    if ($res['result'] == $this->success_result) {
                        $error = '';
                        // Price class and title
//                        $mdata['price_class']=$res['price_class'];
//                        $mdata['price_title']='';
//                        if ($res['price_class']=='warningprice') {
//                            $mdata['price_title']=(empty($res['price_title']) ? '' : 'Base price '.MoneyOutput($res['price_title']));
//                        }
                        $mdata['item_price']=$res['prices'];
                        $mdata['subtotals']=$res['subtotals'];

                        $leadorder=usersession($ordersession);
                        $order=$leadorder['order'];
                        $mdata['order_revenue']=empty($order['revenue']) ? $this->emptycontent : MoneyOutput($order['revenue']);

                        $subtotal=$order['item_cost']+$order['item_imprint']+floatval($order['mischrg_val1'])+floatval($order['mischrg_val2'])-floatval($order['discount_val']);
                        $mdata['item_subtotal'] = empty($subtotal) ? $this->emptycontent : MoneyOutput($subtotal);

                        $shipping=$leadorder['shipping'];
                        $shipping_address=$leadorder['shipping_address'];
                        $mdata['shipcalc'] = 0;
                        $mdata['shipdate'] = $shipping['shipdate'];
                        $mdata['rush_price'] = $shipping['rush_price'];
                        $mdata['is_shipping'] = $order['is_shipping'];
                        $mdata['shipping'] = $order['shipping'];
                        $mdata['cntshipadrr'] = count($shipping_address);
                        // Total Due
                        if ($order['brand']=='SR') {
                            $checkoutlink = $this->config->item('srcheckoutlink').$order['checkout_link'];
                        } else {
                            $checkoutlink = $this->config->item('btcheckoutlink').$order['checkout_link'];;
                        }
                        $mdata['total_due']=$this->load->view('leadordernew/balancedue_view', ['order' => $order, 'checkoutlink' => $checkoutlink], TRUE);
                        // Tax
                        $mdata['tax']=MoneyOutput($order['tax']);
                        // Profit
                        $mdata['profit_content']=$this->_profit_data_view($order);
                        // Imprint details
                        $order_items=$res['items'];
                        $imprint_options = [
                            'order_item_id'=>$order_items['order_item_id'],
                            'imprints'=>$order_items['imprints'],
                        ];
                        $mdata['imprint_content']=$this->load->view('leadordernew/imprint_data_view', $imprint_options, TRUE);
                        // Trackings
                        $mdata['trackcode'] = 0;
                        if ($postdata['fldname']=='item_qty' || $postdata['fldname']=='item_description' || $postdata['fldname']=='item_color') {
                            $mdata['trackcode'] = 1;
                            $shipstatus = $this->leadorder_model->_leadorderview_shipping_status($leadorder);
                            $mdata['tracking'] = $this->_prepare_tracking_content($leadorder['order_items'], $shipstatus, 1);
                        }
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

                    }
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
                if (!isset($postdata['order_item']) && !empty($postdata['order_item'])) {
                    $error='Empty Needed Parameter';
                    $this->ajaxResponse($mdata, $error);
                }
                $order_item_id=$postdata['order_item'];
                // $item_id=$postdata['item'];
                $item_id = -1;
                $res=$this->leadorder_model->add_itemcolor($leadorder, $order_item_id, $item_id, $ordersession);
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

                }
            }
            // Calc new period for lock
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function preparenewitem()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (empty($leadorder)) {
                $error = $this->restore_orderdata_error;
            } else {
                // Lock Edit Record
                $locres = $this->_lockorder($leadorder);
                if ($locres['result'] == $this->error_result) {
                    $leadorder = usersession($ordersession, NULL);
                    $error = $locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                } else {
                    $res = $this->leadorder_model->preparenewitem($leadorder, $ordersession);
                    $error = $res['msg'];
                    $leadorder = usersession($ordersession);
                    $order = $leadorder['order'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $orderitem = $res['newitem'];
                        $dboptions=array(
                            'exclude'=>array(-4, -5, -2),
                            'brand' => ($order['brand']=='SR') ? 'SR' : 'BT',
                        );
                        $this->load->model('orders_model');
                        $itemslist = $this->orders_model->get_item_list($dboptions);
                        $mdata['content'] = $this->load->view('leadordernew/itemadd_data_view', array('items' => $orderitem['items'], 'itemslist' => $itemslist), TRUE);
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
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

    private function _shipwarning_confirm_view($options)
    {
        $content=$this->load->view('leadordernew/shipcost_warning_view', $options, TRUE);
        return $content;
    }

    private function _profit_data_view($order, $edit_mode=1) {
        if (empty($order['order_cog'])) {
            $content = $this->load->view('leadordernew/profit_project_view', ['order' => $order], TRUE);
        } else {
            $content = $this->load->view('leadordernew/profit_view', ['order' =>  $order], TRUE);
        }
        return $content;
    }

    public function _prepare_tracking_content($order_items, $shipstatus, $edit)
    {
        // $trackcontent = '<div class="fulflm_shipping empty">&nbsp</div>';
        $trackcontent = '&nbsp;';
        $numcolors = 0;
        foreach ($order_items as $order_item) {
            $numcolors+=count($order_item['items']);
        }
        $services = [];
        $services[] = ['key' => 'UPS', 'value' => 'UPS'];
        $services[] = ['key' => 'FedEx', 'value' => 'FedEx'];
        $services[] = ['key' => 'DHL', 'value' => 'DHL'];
        $services[] = ['key' => 'USPS', 'value' => 'USPS'];
        $services[] = ['key' => 'Van', 'value' => 'Van'];
        $services[] = ['key' => 'Pickup', 'value' => 'Pickup'];
        $services[] = ['key' => 'Courier', 'value' => 'Courier'];
        $services[] = ['key' => 'Other', 'value' => 'Other'];
        if ($numcolors==1) {
            $orderitem = $order_items[0];
            $itemdata = $orderitem['items'][0];
            if (!empty($itemdata['item_qty'])) {
                if ($orderitem['item_id'] > 0) {
                    $itemname = $orderitem['item_name'] . (empty($itemdata['item_color']) ? '' : ' - ' . $itemdata['item_color']);
                } else {
                    $itemname = $itemdata['item_description'];
                }
                $shipoptions = [
                    'shipdate' => $shipstatus['order_status'],
                    'item' => $itemname, // $orderitem['item_name'].(empty($itemdata['item_color']) ? '' : ' - '.$itemdata['item_color']),
                    'qty' => $itemdata['item_qty'],
                    'order_item' => $orderitem['order_item_id'],
                    'item_color' => $itemdata['item_id'],
                ];
                $tracktotal = 0;
                if (!empty($itemdata['trackings'])) {
                    foreach ($itemdata['trackings'] as $tracking) {
                        $tracktotal += $tracking['qty'];
                    }
                }
                $resttrack = intval($itemdata['item_qty']) - intval($tracktotal);
                $shipoptions['remind'] = $resttrack;
                $shipoptions['completed'] = ($resttrack > 0 ? 0 : 1);
                $shipoptions['shipped'] = intval($tracktotal);
                $shipoptions['edit'] = $edit;
                $trackbody = '';
                if (!empty($itemdata['trackings'])) {
                    $tbodyoptions = [
                        'trackings' => $itemdata['trackings'],
                        'completed' => ($resttrack > 0 ? 0 : 1),
                        'order_item' => $orderitem['order_item_id'],
                        'item_color' => $itemdata['item_id'],
                        'shipped' => $tracktotal,
                        'services' => $services,
                        'edit' => $edit,
                    ];
                    $trackbody = $this->load->view('leadordernew/tracking_data_view', $tbodyoptions, TRUE);
                }
                $shipoptions['trackbody'] = $trackbody;
                $trackcontent = $this->load->view('leadordernew/tracking_view', $shipoptions, TRUE);
            }
        } elseif ($numcolors > 1) {
            // Multi Items Track
            $totalitems = 0;
            $tracktotal = 0;
            foreach ($order_items as $order_item) {
                $totalitems+=intval($order_item['item_qty']);
                $itemcolors = $order_item['items'];
                foreach ($itemcolors as $itemcolor) {
                    foreach ($itemcolor['trackings'] as $tracking) {
                        $tracktotal+=$tracking['qty'];
                    }
                }
            }
            $remains = $totalitems - $tracktotal;
            $allcompleted = 1;
            if ($remains > 0) {
                $allcompleted = 0;
            }
            $footeroptions = [
                'completed' => $allcompleted,
                'remind' => $remains,
                'shipdate' => $shipstatus['order_status'],
            ];
            $trackcontent = '';
            $numpp = 0;
            foreach ($order_items as $order_item) {
                $items = $order_item['items'];
                foreach ($items as $item) {
                    if (!empty($item['item_qty'])) {
                        if ($order_item['item_id'] > 0) {
                            $itemname = $order_item['item_name'] . (empty($item['item_color']) ? '' : ' - ' . $item['item_color']);
                        } else {
                            $itemname = $item['item_description'];
                        }
                        $shipoptions = [
                            'shipdate' => $shipstatus['order_status'],
                            'item' => $itemname, // $orderitem['item_name'].(empty($itemdata['item_color']) ? '' : ' - '.$itemdata['item_color']),
                            'qty' => $item['item_qty'],
                            'order_item' => $order_item['order_item_id'],
                            'item_color' => $item['item_id'],
                        ];
                        $tracktotal = 0;
                        if (!empty($item['trackings'])) {
                            foreach ($item['trackings'] as $tracking) {
                                $tracktotal += $tracking['qty'];
                            }
                        }
                        $resttrack = intval($item['item_qty']) - intval($tracktotal);
                        $shipoptions['remind'] = $resttrack;
                        $shipoptions['completed'] = ($resttrack > 0 ? 0 : 1);
                        $shipoptions['shipped'] = intval($tracktotal);
                        $shipoptions['edit'] = $edit;
                        $trackbody = '';
                        if (!empty($item['trackings'])) {
                            $tbodyoptions = [
                                'trackings' => $item['trackings'],
                                'completed' => ($resttrack > 0 ? 0 : 1),
                                'order_item' => $order_item['order_item_id'],
                                'item_color' => $item['item_id'],
                                'shipped' => $tracktotal,
                                'services' => $services,
                                'edit' => $edit,
                            ];
                            $trackbody = $this->load->view('leadordernew/tracking_data_view', $tbodyoptions, TRUE);
                        }
                        $shipoptions['trackbody'] = $trackbody;
                        if ($numpp > 0) {
                            $trackcontent.='<div class="fulflmshipping_track_separator">&nbsp;</div>';
                        }
                        $trackcontent.=$this->load->view('leadordernew/multitracking_view', $shipoptions, TRUE);
                        $numpp++;
                    }
                }
            }
            // Add total footer
            $trackcontent.=$this->load->view('leadordernew/multitracking_footer_view', $footeroptions, TRUE);
        }
        return $trackcontent;
    }
}