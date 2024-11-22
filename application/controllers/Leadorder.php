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
                // Add items list
                $this->load->model('orders_model');
                $dboptions=array(
                    'exclude'=>array(-4, -5, -2),
                    'brand' => ($brand=='SR') ? 'SR' : 'BT',
                );
                $res['itemslist']=$this->orders_model->get_item_list($dboptions);
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
                $options['mapuse'] = empty($this->config->item('google_map_key')) ? 0 : 1;
                $orddata=$res['order'];
                if ($order==0) {
                    $options['order_id']=0;
                    $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                    $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT, 1);
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
                        'brand' => $brand
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
                            'brand' => $brand,
                        ];
                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT,0);
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
                            'brand' => $res['order']['brand'],
                        ];
                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT, $edit);

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
                $mdata['cancelorder'] = $orddata['is_canceled'];
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
                $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT, 0);
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
                $mdata['cancelorder'] = $orddata['is_canceled'];
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
                    $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT, 1);

                    $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                    // Build Content
                    $options['order_data']=$order_data;
                    $options['order_confirmation']='Duplicate';
                    $options['leadsession']=$ordersession;
                    $options['mapuse'] = empty($this->config->item('google_map_key')) ? 0 : 1;
                    $options['current_page']=ifset($postdata,'current_page','orders');
                    $content=$this->load->view('leadorderdetails/placeorder_menu_edit',$options, TRUE);
                    $mdata['content']=$content;
                    $head_options = [
                        'order_head' => $this->load->view('leadorderdetails/head_placeorder_edit', $orddata, TRUE),
                        'prvorder' => 0,
                        'nxtorder' => 0,
                        'order_id' => 0,
                        'brand' => $orddata['brand'],
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
                            'claydocs' => [],
                            'previewdocs' => [],
                            'delrecords'=>array(),
                            'locrecid'=>0,
                        );
                    }
                    usersession($ordersession, $leadorder);
                }
                // $this->ajaxResponse($mdata, $error);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

//    public function leadorder_dublicate($ordersession) {
//        $leadorder = usersession($ordersession);
//        usersession($ordersession,NULL);
//        if (!empty($leadorder)) {
//            $res = $this->leadorder_model->dublicate_order($leadorder, $this->USR_ID);
//            $error=$res['msg'];
//            if ($res['result']==$this->success_result) {
//                $content_options = [];
//                $options = array();
//                $orddata = $res['order'];
//                // Build Head
//                $options['order_head'] = $this->load->view('leadorderdetails/head_order_view', $orddata, TRUE);
//                // Build View
//                $data = $this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, 1);
//
//                $order_data = $this->load->view('leadorderdetails/order_content_view', $data, TRUE);
//                // Build Content
//                $options['order_data'] = $order_data;
//                $options['order_confirmation'] = 'Duplicate';
//                $options['leadsession'] = $ordersession;
//                $options['mapuse'] = empty($this->config->item('google_map_key')) ? 0 : 1;
//                $options['current_page'] = 'orders';
//                // $ions['user_id'] = $this->USR_ID;
//                // $options['user_name'] = $this->USER_NAME;
//                $gmaps = 0;
//                if (!empty($this->config->item('google_map_key'))) {
//                    $gmaps = 1;
//                }
//                $head['gmaps'] = $gmaps;
//                // Uploader
//                $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
//                $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
//                if ($gmaps==1) {
//                    $head['scripts'][]=array('src'=>'/js/leads/order_address.js');
//                }
//                // File Download
//                $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
//                // Datepicker
//                $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
//                $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
//                // Select 2
//                $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
//                $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];
//
//                // Order popup
//                $head['styles'][]=array('style'=>'/css/leadorder/popup.css');
//                $head['scripts'][]=array('src'=>'/js/leads/leadorderpopup.js');
//                $head['styles'][] = array('style'=>'/css/leadorder/duplicate_order.css');
//                $head['scripts'][]=array('src'=>'/js/leads/duplicate_order.js');
//
//                $content = $this->load->view('leadorderdetails/placeorder_menu_edit', $options, TRUE);
//                $content_options['content'] = $content;
//                $head_options = [
//                    'order_head' => $this->load->view('leadorderdetails/head_placeorder_edit', $orddata, TRUE),
//                    'prvorder' => 0,
//                    'nxtorder' => 0,
//                    'order_id' => 0,
//                    'brand' => $orddata['brand'],
//                ];
//                $content_options['header'] = $this->load->view('leadorderdetails/head_edit', $head_options, TRUE);
//                /* Save to session */
//                if ($res['order_system_type'] == 'old') {
//                    $leadorder = array(
//                        'order' => $orddata,
//                        'payments' => $res['payments'],
//                        'artwork' => $res['artwork'],
//                        'artlocations' => $res['artlocations'],
//                        'artproofs' => $res['proofdocs'],
//                        'message' => $res['message'],
//                        'order_system' => $res['order_system_type'],
//                        'locrecid' => 0,
//                    );
//                } else {
//                    $leadorder = array(
//                        'order' => $orddata,
//                        'payments' => $res['payments'],
//                        'artwork' => $res['artwork'],
//                        'artlocations' => $res['artlocations'],
//                        'artproofs' => $res['proofdocs'],
//                        'message' => $res['message'],
//                        'contacts' => $res['contacts'],
//                        'order_items' => $res['order_items'],
//                        'order_system' => $res['order_system_type'],
//                        'shipping' => $res['shipping'],
//                        'shipping_address' => $res['shipping_address'],
//                        'billing' => $res['order_billing'],
//                        'charges' => $res['charges'],
//                        'claydocs' => [],
//                        'previewdocs' => [],
//                        'delrecords' => array(),
//                        'locrecid' => 0,
//                    );
//                }
//                usersession($ordersession, $leadorder);
//                $dat = $this->template->prepare_duplicateorder($head);
//                // $content_options['left_menu'] = $dat['left_menu'];
//                $content_options['brand'] = $orddata['brand'];
//                $content_view = $this->load->view('duplcate_orders/page_view', $content_options, TRUE);
//                $dat['content'] = $content_view;
//                $this->load->view('public_pages/public_template_view', $dat);
//            } else {
//                show_404();
//            }
//        } else {
//            show_404();
//        }
//    }

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
                        $this->load->model('shipping_model');
                        $error='';
                        $leadorder=usersession($ordersession);
                        $order = $leadorder['order'];
                        $billing = $leadorder['billing'];
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
                            $mdata['country_code'] = $res['cntcode'];
                            $mdata['addresline'] = $this->load->view('leadorderdetails/bill_addressline_edit', ['billing' => $billing], true);
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
                                    $cntcode = '';
                                    if (ifset($billing,'country_id','')!=='') {
                                        $cntres = $this->shipping_model->get_country($billing['country_id']);
                                        $cntcode = $cntres['country_iso_code_2'];
                                    }
                                    $billoptions['billcntcode'] = $cntcode;
                                    $billoptions['billaddress'] = $this->shipping_model->prepare_billaddress($billing);
                                    $billoptions['country_code'] = $cntcode;
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
                        $mdata['freshship'] = $mdata['freshbill'] = 0;
                        if (isset($res['shipcompany'])) {
                            $mdata['freshship'] = 1;
                            $mdata['shipcompany'] = $res['shipcompany'];
                        }
                        if (isset($res['billcompany'])) {
                            $mdata['freshbill'] = 1;
                            $mdata['billcompany'] = $res['billcompany'];
                        }
                        if ($entity=='billing') {
                            $mdata['billaddress'] = $this->shipping_model->prepare_billaddress($billing);
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
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mdata['orderitem'] = $res['order_item_id'];

                        $order_items=$res['order_items'];
                        $order = $res['order'];
                        $this->load->model('orders_model');
                        $dboptions=array(
                            'exclude'=>array(-4, -5, -2),
                            'brand' => ($order['brand']=='SR') ? 'SR' : 'BT',
                        );
                        $itemslist = $this->orders_model->get_item_list($dboptions);

                        $content='';
                        foreach ($order_items as $irow) {
                            $imprints=$irow['imprints'];
                            $imprint_options=array(
                                'order_item_id'=>$irow['order_item_id'],
                                'imprints'=>$imprints,
                            );
                            $imprintview=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                            $showinvent = 0;
                            if ($order['brand']=='SR' && $irow['item_id']>0) {
                                $showinvent = 1;
                            }
                            $item_options=array(
                                'order_item_id'=>$irow['order_item_id'],
                                'items'=>$irow['items'],
                                'imprintview'=>$imprintview,
                                'edit'=>1,
                                'item_id'=>$irow['item_id'],
                                'brand' => $order['brand'],
                                'itemslist' => $itemslist,
                                'showinvent' => $showinvent,
                            );
                            if ($irow['order_item_id']==$res['order_item_id']) {
                                $content.=$this->load->view('leadorderdetails/items_data_add', $item_options, TRUE);
                            } else {
                                $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                            }
                        }
                        $mdata['items_content']=$content;
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // New item
    public function saveneworderitem()
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
                $locres=$this->_lockorder($leadorder);
                if ($locres['result']==$this->error_result) {
                    $leadorder=usersession($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->ajaxResponse($mdata, $error);
                } else {
                    $orderitem_id = ifset($postdata, 'orderitem_id',0);
                    $item_id = ifset($postdata, 'item_id', 0);
                    if (empty($item_id)) {
                        $error = 'Select Item';
                    } elseif (empty($orderitem_id)) {
                        $error = 'Select Order Item';
                    } else {
                        $res = $this->leadorder_model->saveneworderitem($leadorder, $item_id, $orderitem_id, $ordersession);
                        $error = $res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            $item = $res['item'];
                            // Prepare out
                            $special = 0;
                            if ($item['item_id']<0) {
                                $special = 1;
                            }
                            $mdata['special'] = $special;
                            $options = [
                                'order_item_id' => $orderitem_id,
                                'item_id' => $item_id,
                                'item_color' => $item['items'][0]['item_color'],
                                'colors' => $item['colors'],
                                'qty' => $item['item_qty'],
                                'price' => $item['base_price'],
                            ];
                            if ($special==0) {
                                if ($res['brand']=='SR') {
                                    $mdata['outcolors'] = $this->load->view('leadorderdetails/sradditem_color_view', $options, true);
                                } else {
                                    $mdata['outcolors'] = $this->load->view('leadorderdetails/item_color_choice', $options, true);
                                }
                            } else {
                                $mdata['outcolors'] = '&nbsp;';
                            }
                            $mdata['qty'] = $this->load->view('leadorderdetails/additem_qty_view', $options, TRUE); // $item['item_qty']
                            $mdata['price'] = $this->load->view('leadorderdetails/additem_price_view', $options, TRUE); // $item['base_price']
                            $mdata['subtotal'] = MoneyOutput($item['item_subtotal']);
                            $mdata['brand'] = $res['brand'];
                        }
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventoryitem()
    {
        if ($this->isAjax()) {
            $mdata=[];
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
                } else {
                    $orderitem_id = ifset($postdata, 'orderitem_id',0);
                    $itemstatus = ifset($postdata, 'itemstatus',0);
                    if (empty($orderitem_id)) {
                        $error = 'Select Order Item';
                    } else {
                        $res = $this->leadorder_model->orderiteminventory($leadorder, $orderitem_id, $ordersession);
                        $error = $res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            $options = [
                                'onboats' => $res['onboats'],
                                'invents' => $res['invents'],
                                'itemstatus' => $itemstatus,
                            ];
                            $mdata['content'] = $this->load->view('leadorderdetails/itemcolor_inventory_view', $options, TRUE);
                        }
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function saveneworderitemparam()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata=$this->input->post();
            $ordersession= ifset($postdata, 'ordersession','unkn');
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
                $orderitem_id = ifset($postdata, 'orderitem_id',0);
                $paramname = ifset($postdata,'paramname','');
                $newval = ifset($postdata, 'newval', '');
                if (empty($orderitem_id)) {
                    $error = 'Select Order Item';
                } elseif (empty($paramname)) {
                    $error = 'Empty Parameter';
                } else {
                    $res = $this->leadorder_model->saveneworderitemparam($leadorder, $orderitem_id, $paramname, $newval, $ordersession);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $options = [
                            'order_item_id' => $orderitem_id,
                            'item_id' => $res['item_id'],
                            'item_color' => $res['color'],
                            'colors' => $res['colors'],
                            'qty' => $res['item_qty'],
                            'price' => $res['base_price'],
                        ];
                        if ($res['brand']=='SR') {
                            $mdata['outcolors'] = $this->load->view('leadorderdetails/sradditem_color_view', $options, true);
                        } else {
                            $mdata['outcolors'] = $this->load->view('leadorderdetails/item_color_choice', $options, true);
                        }
                        $mdata['qty'] = $this->load->view('leadorderdetails/additem_qty_view', $options, TRUE); // $item['item_qty']
                        $mdata['price'] = $this->load->view('leadorderdetails/additem_price_view', $options, TRUE); // $item['base_price']
                        $mdata['subtotal'] = MoneyOutput($res['item_subtotal']);
                        $mdata['brand'] = $res['brand'];
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function neworderitemimprints()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata=$this->input->post();
            $ordersession= ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
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

    // Cancel new Item
    public function cancelneworderitem()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata=$this->input->post();
            $ordersession= ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
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
                $res = $this->leadorder_model->cancelneworderitem($leadorder, $orderitem_id, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $order_items=$res['order_items'];
                    $order = $res['order'];
                    $content='';
                    $mdata['newitem'] = 0;
                    foreach ($order_items as $irow) {
                        $imprints=$irow['imprints'];
                        $imprint_options=array(
                            'order_item_id'=>$irow['order_item_id'],
                            'imprints'=>$imprints,
                        );
                        $imprintview=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                        $showinvent = 0;
                        if ($order['brand']=='SR' && $irow['item_id']>0) {
                            $showinvent = 1;
                        }
                        $item_options=array(
                            'order_item_id'=>$irow['order_item_id'],
                            'items'=>$irow['items'],
                            'imprintview'=>$imprintview,
                            'edit'=>1,
                            'item_id'=>$irow['item_id'],
                            'brand' => $order['brand'],
                            'showinvent' => $showinvent,
                        );
                        if ($irow['item_id']=='') {
                            $this->load->model('orders_model');
                            $dboptions=array(
                                'exclude'=>array(-4, -5, -2),
                                'brand' => ($order['brand']=='SR') ? 'SR' : 'BT',
                            );
                            $itemslist=$this->orders_model->get_item_list($dboptions);
                            $item_options['itemslist'] = $itemslist;
                            $content.=$this->load->view('leadorderdetails/items_data_add', $item_options, TRUE);
                            $mdata['newitem'] = 1;
                        } else {
                            $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                        }
                    }
                    $mdata['items_content']=$content;
                }
            }
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
                // $custom_item=(isset($postdata['order_items']) ? $postdata['order_items'] : '');
                $orderitem_id = ifset($postdata,'orderitem_id',0);
                if (empty($item_id)) {
                    $error = 'Select Item';
                } elseif (empty($orderitem_id)) {
                    $error = 'Select Order Item';
                } else {
                    $mdata['order_system']=$leadorder['order_system'];
                    if ($leadorder['order_system']=='old') {
                        $res=$this->leadorder_model->save_item($leadorder, $item_id, $orderitem_id, $ordersession);
                        $error=$res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            $mdata['item_num']=$res['item_number'];
                            $mdata['item_description']=$res['item_name'];
                        }
                    } else {
                        // New Order
                        $res=$this->leadorder_model->save_order_items($leadorder, $item_id, $orderitem_id, $ordersession);
                        $error=$res['msg'];
                        if ($res['result']==$this->success_result) {
                            $error = '';
                            $leadorder=usersession($ordersession);
                            $newitem = $res['newitem'];
                            $order=$leadorder['order'];
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
                                $showinvent = 0;
                                if ($order['brand']=='SR' && $irow['item_id']>0) {
                                    $showinvent = 1;
                                }
                                $item_options=array(
                                    'order_item_id'=>$irow['order_item_id'],
                                    'items'=>$irow['items'],
                                    'imprintview'=>$imprintview,
                                    'edit'=>1,
                                    'item_id'=>$irow['item_id'],
                                    'brand' => $order['brand'],
                                    'showinvent' => $showinvent,
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
                            // Shipping count results
                            $mdata['shipcount'] = $res['shipcount'];
                            if ($res['shipcount']==$this->success_result) {
                                if ($mdata['cntshipadrr']==1) {
                                    $mdata['adressship'] = $shipping_address[0]['order_shipaddr_id'];
                                    // Ship Rates
                                    $shipcost=$shipping_address[0]['shipping_costs'];
                                    $costoptions=array(
                                        'shipadr'=>$shipping_address[0]['order_shipaddr_id'],
                                        'shipcost'=>$shipcost,
                                    );
                                    $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                                    // Tax View
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
                            $mdata['extendview'] = $res['extendview'];
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
                    $mdata['newitem'] = 0;
                    foreach ($order_items as $irow) {
                        $imprints = $irow['imprints'];
                        $imprint_options = array(
                            'order_item_id' => $irow['order_item_id'],
                            'imprints' => $imprints,
                        );
                        $imprintview = $this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                        $showinvent = 0;
                        if ($order['brand']=='SR' && $irow['item_id']>0) {
                            $showinvent = 1;
                        }
                        $item_options = array(
                            'order_item_id' => $irow['order_item_id'],
                            'items' => $irow['items'],
                            'imprintview' => $imprintview,
                            'edit' => 1,
                            'item_id' => $irow['item_id'],
                            'showinvent' => $showinvent,
                            'brand' => $order['brand'],
                        );
                        if ($irow['item_id']=='') {
                            $this->load->model('orders_model');
                            $dboptions=array(
                                'exclude'=>array(-4, -5, -2),
                                'brand' => ($order['brand']=='SR') ? 'SR' : 'BT',
                            );
                            $itemslist=$this->orders_model->get_item_list($dboptions);
                            $item_options['itemslist'] = $itemslist;
                            $content.=$this->load->view('leadorderdetails/items_data_add', $item_options, TRUE);
                            $mdata['newitem'] = 1;
                        } else {
                            $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                        }
                        // $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
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
                    // Tracking
                    $mdata['trackbody'] = '';
                    $orderitems = $leadorder['order_items'];
                    if (count($orderitems)==1) {
                        $itemdat = $orderitems[0]['items'];
                        if (count($itemdat)==1) {
                            $mdata['trackbody'] = $this->_prepare_tracking_view($leadorder, 1);
                        } else {
                            $mdata['trackbody'] = $this->_prepare_multitrack_view($leadorder,1);
                        }
                    } elseif (count($orderitems)>1) {
                        $mdata['trackbody'] = $this->_prepare_multitrack_view($leadorder,1);
                    }
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
                    $mdata['freshship'] = $mdata['freshbill'] = 0;
                    if (isset($res['shipcontact'])) {
                        $mdata['freshship'] = 1;
                        $mdata['shipcontact'] = $res['shipcontact'];
                    }
                    if (isset($res['billcontact'])) {
                        $mdata['freshbill'] = 1;
                        $mdata['billcontact'] = $res['billcontact'];
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
                if (!isset($postdata['order_item']) || !isset($postdata['item'])) {
                    $error='Empty Needed Parameter';
                    $this->ajaxResponse($mdata, $error);
                }
                $order_item_id=$postdata['order_item'];
                $item_id=$postdata['item'];
                $res=$this->leadorder_model->add_itemcolor($leadorder, $order_item_id, $item_id, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $order_items=$res['items'];
                    $imprints=$order_items['imprints'];
                    $imprintview=$this->load->view('leadorderdetails/imprint_data_edit', array('imprints'=>$imprints), TRUE);
                    $showinvent = 0;
                    if ($res['order']['brand']=='SR' && $order_items['item_id']>0) {
                        $showinvent = 1;
                    }
                    $item_options=array(
                        'order_item_id'=>$order_items['order_item_id'],
                        'items'=>$order_items['items'],
                        'imprintview'=>$imprintview,
                        'edit'=>1,
                        'showinvent' => $showinvent,
                        'brand' => $res['order']['brand'],
                        'item_id' => $order_items['item_id'],
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
                        $mdata['trackcode'] = 0;
                        if ($postdata['fldname']=='item_qty' || $postdata['fldname']=='item_description' || $postdata['fldname']=='item_color') {
                            $mdata['trackcode'] = 1;
                            $orderitems = $leadorder['order_items'];
                            if (count($orderitems)==1) {
                                $itemdat = $orderitems[0]['items'];
                                if (count($itemdat)==1) {
                                    $mdata['trackbody'] = $this->_prepare_tracking_view($leadorder, 1);
                                } else {
                                    $mdata['trackbody'] = $this->_prepare_multitrack_view($leadorder,1);
                                }
                            } else {
                                $mdata['trackbody'] = $this->_prepare_multitrack_view($leadorder,1);
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
                                $mdata['setup']=  number_format($res['setup'],2,'.','');
                                if ($newval=='NEW') {
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
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leadorder=usersession($ordersession);
                    $order=$leadorder['order'];
                    $shipping=$leadorder['shipping'];
                    $shipping_address=$leadorder['shipping_address'];
                    // $order_items
                    $mdata['ordersystem']=$leadorder['order_system'];
                    $mdata['newitem'] = $res['itemstatus']=='new' ? 1 : 0;
                    if ($res['itemstatus']=='new') {
                        $order_items = $leadorder['order_items'];
                        $content='';
                        foreach ($order_items as $irow) {
                            $imprints=$irow['imprints'];
                            $imprint_options=array(
                                'order_item_id'=>$irow['order_item_id'],
                                'imprints'=>$imprints,
                            );
                            $imprintview=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                            $showinvent = 0;
                            if ($order['brand']=='SR' && $irow['item_id']>0) {
                                $showinvent = 1;
                            }
                            $item_options=array(
                                'order_item_id'=>$irow['order_item_id'],
                                'items'=>$irow['items'],
                                'imprintview'=>$imprintview,
                                'edit'=>1,
                                'item_id'=>$irow['item_id'],
                                'brand' => $order['brand'],
                                'showinvent' => $showinvent,
                            );
                            $content.=$this->load->view('leadorderdetails/items_data_edit', $item_options, TRUE);
                        }
                        $mdata['items_content']=$content;
                    }
                    $mdata['order_revenue']=MoneyOutput($order['revenue']);
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
                    if ($res['itemstatus']=='old') {
                        $order_items=$res['item'];
                        $imprint_options=array(
                            'order_item_id'=>$order_items['order_item_id'],
                            'imprints'=>$order_items['imprints'],
                        );
                        $mdata['imprint_content']=$this->load->view('leadorderdetails/imprint_data_edit', $imprint_options, TRUE);
                        $mdata['order_item_id']=$order_items['order_item_id'];
                    }
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
                    $mdata['shipcount'] = $res['shipcount'];
                    if ($res['shipcount']==$this->success_result) {
                        if ($mdata['cntshipadrr']==1) {
                            $mdata['shipaddress'] = $shipping_address[0]['order_shipaddr_id'];
                            // Ship Rates
                            $shipcost=$shipping_address[0]['shipping_costs'];
                            $costoptions=array(
                                'shipadr'=>$shipping_address[0]['order_shipaddr_id'],
                                'shipcost'=>$shipcost,
                            );
                            $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                            // Tax View
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
                    // Tracking info
                    $orderitems = $leadorder['order_items'];
                    if (count($orderitems)==1) {
                        $itemdat = $orderitems[0]['items'];
                        if (count($itemdat)==1) {
                            $mdata['trackbody'] = $this->_prepare_tracking_view($leadorder, 1);
                        } else {
                            $mdata['trackbody'] = $this->_prepare_multitrack_view($leadorder,1);
                        }
                    } else {
                        $mdata['trackbody'] = $this->_prepare_multitrack_view($leadorder,1);
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
                        $order_items = $leadorder['order_items'];
                        $mdata['trackcount'] = 1;
                        if (count($order_items)==1) {
                            $itemdat = $order_items[0]['items'];
                            if (count($itemdat)==1) {
                                $mdata['trackbodby'] = $this->_prepare_tracking_view($leadorder, 1);
                            } else {
                                $mdata['trackbodby'] = $this->_prepare_multitrack_view($leadorder, 1);
                            }
                        } else {
                            $mdata['trackbodby'] = $this->_prepare_multitrack_view($leadorder, 1);
                        }
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
                            $mdata['addresscopy'] = $this->shipping_model->prepare_shipaddress($shipaddr);
                            $mdata['cntcode'] = $shipaddr['out_country'];
                            if ($fldname=='country_id') {
                                $mdata['addressline'] = $this->load->view('leadorderdetails/shipaddresline_view', ['shipadr' => $shipaddr], true);
                            }
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
                        $mdata['hidelock'] = 0;
                        if ($fldname=='cardnum' || $fldname=='cardcode') {
                            $mdata['hidelock'] = 1;
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
                                'brand' => $brand,
                            ];
                            // Build View
                            $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT, 1);
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
                            $options['mapuse'] = empty($this->config->item('google_map_key')) ? 0 : 1;
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
                    $user = usersession('usr_data');
                    $options=array(
                        'charges'=>$charges,
                        'order_id'=>$order_id,
                        'payment_user' => $this->USER_PAYMENT,
                        'financeview' => $user['finuser'],
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
                    $mdata['viewadd'] = $this->_checknewshipaddres($shipping_address, $order_qty);
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
                        $mdata['viewadd'] = $this->_checknewshipaddres($shipping_address, $order_qty);
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
                        $mdata['viewadd'] = $this->_checknewshipaddres($shipping_address, $order_qty);
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
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
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
                        $mdata['arrivedate'] = (intval($srow['arrive_date'])==0 ? '' : date('m/d/Y', $srow['arrive_date']));
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
                        $mdata['viewadd'] = $this->_checknewshipaddres($shipping_address, $order_qty);
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
                                'shipaddress' => $this->shipping_model->prepare_shipaddress($shipping_address[0]),
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
            $cntdat = $this->shipping_model->get_country($country_id);
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
                'shipcntcode' => $cntdat['country_iso_code_2'],
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

    private function _checknewshipaddres($shipping_address, $order_qty)
    {
        $showview=0;
        $totalqty=0;
        foreach ($shipping_address as $row) {
            $totalqty+=$row['item_qty'];
        }
        if ($totalqty!=$order_qty) {
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
                    $brand = ifset($postdata,'brand', 'ALL');
                    $res=$this->leadorder_model->get_leadorder($order_id, $this->USR_ID, $brand);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        // Build Head
                        $engade_res=$this->engaded_model->check_engade(array('entity'=>'ts_orders','entity_id'=>$order_id));
                        $res['unlocked']=$engade_res['result'];
                        $orddata=$res['order'];

                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res, $ordersession, $this->USR_ID, $this->USR_ROLE, $this->USER_PAYMENT, 0);

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
                            'brand' => $brand,
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
            $res=$this->leadorder_model->get_leadorder_projamounts($order_id);// get_leadorder_amounts($order_id);
            $options=array(
                'data'=>$res,
                'profit_class'=>$postdata['clas'],
                'edit_mode' => (ifset($postdata,'edit',1)),
            );
            $cogcontent=$this->load->view('leadorderdetails/ordercog_newdetails_view', $options, TRUE);
        }
        echo $cogcontent;
    }

    public function orderprojdetails()
    {
        $postdata=$this->input->get();
        $order_id=(isset($postdata['ord']) ? intval($postdata['ord']) : 0);
        $session = (isset($postdata['sess']) ? $postdata['sess'] : '');
        $editmode = (isset($postdata['edit']) ? intval($postdata['edit']) : 0);
        $cogcontent='<div class="error">Order Not Found</div>';
        if (!empty($order_id)) {
            $res=$this->leadorder_model->get_leadorder_projamounts($order_id);
            $lists = $res['list'];
            $listidx = 0;
            foreach ($lists as $list) {
                $details = $list['details'];
                $view = '';
                if (count($details) > 0) {
                    $view = $this->load->view('leadorderdetails/orderamount_details_view', ['details' => $details, 'edit_mode' => $editmode], TRUE);
                }
                $lists[$listidx]['detailsview'] = $view;
                $listidx++;
            }
            $res['list'] = $lists;
            // Get data for project amount
            $options = [
                'edit_mode' => $editmode,
                'data' => $res,
            ];
            $cogcontent=$this->load->view('leadorderdetails/orderprojcog_details_view', $options, TRUE);
        }
        echo $cogcontent;
    }

    public function podetailsedit()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Order #';
            $postdata=$this->input->post();
            $order_id=ifset($postdata, 'order',0);
            $session = ifset($postdata, 'ordersession', 'UNKN');
            $editmode = ifset($postdata, 'edit', 0);
            if (!empty($order_id)) {
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->get_leadorder_projamounts($order_id);
                $lists = $res['list'];
                $listidx = 0;
                foreach ($lists as $list) {
                    $details = $list['details'];
                    $view = '';
                    if (count($details) > 0) {
                        $view = $this->load->view('leadorderdetails/orderamount_details_view', ['details' => $details, 'edit_mode' => $editmode], TRUE);
                    }
                    $lists[$listidx]['detailsview'] = $view;
                    $listidx++;
                }
                $res['list'] = $lists;
                // Get data for project amount
                $options = [
                    'edit_mode' => $editmode,
                    'data' => $res,
                ];
                $mdata['content']=$this->load->view('leadorderdetails/orderprojcog_details_view', $options, TRUE);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
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
                    $order_id = $leadorder['order']['order_id'];
                    // $mdata['content']=$this->_profit_data_view($order,0);
                    $res=$this->leadorder_model->get_leadorder_projamounts($order_id);
                    $lists = $res['list'];
                    $listidx = 0;
                    foreach ($lists as $list) {
                        $details = $list['details'];
                        $view = '';
                        if (count($details) > 0) {
                            $view = $this->load->view('leadorderdetails/orderamount_details_view', ['details' => $details, 'edit_mode' => $editmode], TRUE);
                        }
                        $lists[$listidx]['detailsview'] = $view;
                        $listidx++;
                    }
                    $res['list'] = $lists;
                    // Get data for project amount
                    $options = [
                        'edit_mode' => $editmode,
                        'data' => $res,
                    ];
                    $mdata['content']=$this->load->view('leadorderdetails/orderprojcog_details_view', $options, TRUE);
                    // Profit Button
                    $mdata['profit'] = $this->_prepare_profitbtn_view($res['profit']);
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
                    $session_id = 'orderamnt'.uniq_link(15);
                    usersession($session_id, $res['amount']);
                    $this->load->model('vendors_model');
                    $this->load->model('orders_model');
                    $v_options = [
                        'order_by' => 'v.vendor_name',
                        // 'exclude' => $this->config->item('inventory_vendor'),
                    ];
                    $vendors=$this->vendors_model->get_vendors_list($v_options);
                    $methods=$this->orders_model->get_methods_edit();
//                    $order_view=$this->load->view('pototals/purchase_orderdata_view', $res['order'],TRUE);
//                    $poeditview = $this->load->view('pototals/purchase_reason_view', $res['amount'],TRUE);
//                    $lowprofit_view = '';
//                    if (!empty($res['order']['reason'])) {
//                        $lowprofit_view = $this->load->view('pototals/lowprofit_reason_view', $res['order'],TRUE);
//                    }
//                    $options=array(
//                        'order'=>$res['order'],
//                        'amount'=>$res['amount'],
//                        'itemcolor' => $res['itemcolor'],
//                        'attach'=>'',
//                        'vendors'=>$vendors,
//                        'methods'=>$methods,
//                        'order_view'=>$order_view,
//                        'lowprofit_view'=>$lowprofit_view,
//                        'editpo_view'=>$poeditview,
//                    );
//                    $content=$this->load->view('pototals/purchase_ordercoloredit_view',$options,TRUE);
//                    $mdata['content']=$content;
//                    $data=array(
//                        'amount'=>$res['amount'],
//                        'itemcolor' => $res['itemcolor'],
//                        'order'=>$res['order'],
//                        'attach'=>array(),
//                    );
//                    // Save Data to Session
//                    usersession('editpurchase', $data);
                    $options = [
                        'amount'=>$res['amount'],
                        'vendors'=>$vendors,
                        'methods'=>$methods,
                        'session' => $session_id,
                    ];
                    $mdata['content'] = $this->load->view('leadorderdetails/orderamount_edit_view', $options, TRUE);
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
            $out['content'] = $this->load->view('leadorderdetails/imprint_details_edit', $options, TRUE);

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
                    $mdata['numdocs'] = count($claydocs);
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
                    $mdata['numdocs'] = count($claydocs);
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
                    $mdata['numdocs'] = count($previewdocs);
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
                    $mdata['numdocs'] = count($previewdocs);
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

    public function update_autoaddress() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;

            $postdata = $this->input->post();
            $ordersession = ifset($postdata, 'ordersession','unkn');
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $res = $this->leadorder_model->update_autoaddress($postdata, $leadorder, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $this->load->model('shipping_model');
                    $error = '';
                    $address = $res['address'];
                    $address_full = $res['address_full'];
                    $mdata['address_1'] = $address['address_1'];
                    $mdata['country'] = $address['country'];
                    $mdata['city'] = $address['city'];
                    $mdata['zip'] = $address['zip'];
                    if (!empty($address['state'])) {
                        $states = $res['states'];
                        if ($postdata['address_type']=='billing') {
                            $mdata['bilstate'] = 1;
                            $options = [
                                'states' => $states,
                                'curstate' => $address['state'],
                            ];
                            $mdata['stateview'] = $this->load->view('leadorderdetails/billing_state_select', $options, TRUE);
                        } else {
                            $mdata['shipstate'] = 1;
                            $options = [
                                'states' => $states,
                                'shipadr' => $res['shipping_address'],
                            ];
                            $mdata['stateview'] = $this->load->view('leadorderdetails/shipping_state_select', $options, TRUE);
                        }
                    } else {
                        if ($postdata['address_type']=='billing') {
                            $mdata['bilstate'] = 0;
                        } else {
                            $mdata['shipstate'] = 0;
                        }
                    }
                    if ($postdata['address_type']=='billing') {
                        $mdata['addresscopy'] = $this->shipping_model->prepare_billaddress($address_full);
                    } else {
                        $mdata['addresscopy'] = $this->shipping_model->prepare_shipaddress($address_full);
                    }
                    $mdata['shipcount'] = $res['shipcount'];
                    if ($res['shipcount']==$this->success_result) {
                        // Change Shipping cost, total, tax
                        $leadorder = usersession($ordersession);
                        $order=$leadorder['order'];
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
                        // shipdates_content
                        if ($mdata['cntshipadrr']==1) {
                            $shipcost=$shipping_address[0]['shipping_costs'];
                            $costoptions=array(
                                'shipadr'=>$postdata['shipadr'],
                                'shipcost'=>$shipcost,
                            );
                            $mdata['shipcost']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions, TRUE);
                            // Tax View
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
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function update_autoaddressmulti()
    {
        if ($this->isAjax()) {
            $mdata = [];
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
                usersession($ordersession, $leadorder);
                $shipsession=$postdata['shipsession'];
                $multishipping=usersession($shipsession);
                if (!empty($multishipping)) {
                    $res = $this->leadorder_model->update_autoaddress_multi($postdata, $multishipping, $shipsession);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        // Prepare output
                        $mdata['is_calc']=0;
                        $mdata['taxdata']=0;
                        $multishipping=usersession($shipsession);
                        $shipping_address=$multishipping['shipping_address'];
                        $order = $multishipping['order'];
                        $order_qty=$order['order_qty'];
                        $mdata['total_view']=$this->_build_shiptotals_view($shipping_address, $order_qty);
                        $shipadr = $res['shipadr'];
                        $adridx=0;
                        foreach ($shipping_address as $adrrow) {
                            if ($adrrow['order_shipaddr_id']==$shipadr) {
                                break;
                            } else {
                                $adridx++;
                            }
                        }
                        $srow=$shipping_address[$adridx];
                        $mdata['address_1'] = $srow['ship_address1'];
                        $mdata['country'] = $srow['country_id'];
                        $mdata['city']=$srow['city'];
                        $mdata['zip'] = $srow['zip'];
                        $mdata['state_id']=$srow['state_id'];
                        // Build Content
                        $mdata['shipping']=number_format($srow['shipping'],2);
                        $mdata['tax']=number_format($srow['sales_tax'],2);
                        $mdata['arrivedate'] = (intval($srow['arrive_date'])==0 ? '' : date('m/d/Y', $srow['arrive_date']));
                        if ($res['shipcount']==1) {
                            $mdata['is_calc']=1;
                            $mdata['shipcount'] = 1;
                            $shipcost=$srow['shipping_costs'];
                            $costoptions=array(
                                'shipadr'=>$srow['order_shipaddr_id'],
                                'shipcost'=>$shipcost,
                                'costname'=>'shippingrate'.$srow['order_shipaddr_id'],
                            );
                            $mdata['cost_view']=$this->load->view('leadorderdetails/ship_cost_edit', $costoptions,TRUE);
                        }
                        $states=$res['states'];
                        if (count($states)==0) {
                            $mdata['stateview']='&nbsp;';
                            $mdata['shipstate'] = 0;
                        } else {
                            $mdata['shipstate'] = 1;
                            $stateoptions=array(
                                // 'shipadr'=>$res['shipadr'],
                                'shipadr'=>$srow,
                                'states'=>$res['states'],
                            );
                            $mdata['stateview']=$this->load->view('leadorderdetails/shipping_state_select', $stateoptions, TRUE);
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
                        $mdata['viewadd'] = $this->_checknewshipaddres($shipping_address, $order_qty);
                    }
                }
            }
            $mdata['loctime']=$this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showinventory()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $order_item_id = ifset($postdata,'item_id',0);
                if (empty($order_item_id)) {
                    $error = 'Empty Item';
                } else {
                    $res = $this->leadorder_model->show_iteminvent($leadorder, $order_item_id, $ordersession);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $options = [
                            'onboats' => $res['onboats'],
                            'invents' => $res['invents'],
                            'itemstatus' => 1,
                        ];
                        $mdata['content'] = $this->load->view('leadorderdetails/itemcolor_inventory_view', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function customersearch() {
        $search = $this->input->get('query');
        $limit = $this->input->get('limit');
        $list = $this->leadorder_model->search_customer($search, $limit);
        echo json_encode($list);
    }

    public function unlockpayparams()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (!empty($leadorder)) {
                $res = $this->leadorder_model->unlock_payment_content($leadorder, $postdata, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['cardnum'] = $res['cardnum'];
                    $mdata['cardcode'] = $res['cardcode'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function newtrackcode()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (!empty($leadorder)) {
                $res = $this->leadorder_model->newtrackcode($leadorder, $postdata, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['tracking'] = $res['tracking'];
                    $leadorder = usersession($ordersession);
                    $order_items = $leadorder['order_items'];
                    if (count($order_items)==1) {
                        $itemdat = $order_items[0]['items'];
                        if (count($itemdat)==1) {
                            $mdata['content'] = $this->_prepare_tracking_view($leadorder, 1);
                        } else {
                            $mdata['content'] = $this->_prepare_multitrack_view($leadorder, 1);
                        }
                    } else {
                        $mdata['content'] = $this->_prepare_multitrack_view($leadorder, 1);
                    }
                }
            }
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function updatetrackqtyinfo()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (!empty($leadorder)) {
                $res = $this->leadorder_model->updatetrackinfo($leadorder, $postdata, $ordersession);
                $error = $res['msg'];
                if (isset($res['oldval'])) {
                    $mdata['oldval'] = $res['oldval'];
                }
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leadorder = usersession($ordersession);
                    $order_items = $leadorder['order_items'];
                    if (count($order_items)==1) {
                        $itemdat = $order_items[0]['items'];
                        if (count($itemdat)==1) {
                            $mdata['content'] = $this->_prepare_tracking_view($leadorder, 1);
                        } else {
                            $mdata['content'] = $this->_prepare_multitrack_view($leadorder, 1);
                        }
                    } else {
                        $mdata['content'] = $this->_prepare_multitrack_view($leadorder, 1);
                    }
                }
            }
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function updatetrackinfo()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (!empty($leadorder)) {
                $res = $this->leadorder_model->updatetrackinfo($leadorder, $postdata, $ordersession);
                $error = $res['msg'];
                if (isset($res['oldval'])) {
                    $mdata['oldval'] = $res['oldval'];
                }
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if (isset($res['rest'])) {
                        $mdata['rest'] = $res['rest'];
                    }
                    if (empty($res['trackcode'])) {
                        $mdata['hidecopy'] = 1;
                    } else {
                        $mdata['hidecopy'] = 0;
                    }
                }
            }
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function deletetrackinfo()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $ordersession = (isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder = usersession($ordersession);
            if (!empty($leadorder)) {
                $res = $this->leadorder_model->deletetrackinfo($leadorder, $postdata, $ordersession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leadorder = usersession($ordersession);
                    $order_items = $leadorder['order_items'];
                    if (count($order_items)==1) {
                        $itemdat = $order_items[0]['items'];
                        if (count($itemdat)==1) {
                            $mdata['content'] = $this->_prepare_tracking_view($leadorder, 1);
                        } else {
                            $mdata['content'] = $this->_prepare_multitrack_view($leadorder, 1);
                        }
                    } else {
                        $mdata['content'] = $this->_prepare_multitrack_view($leadorder, 1);
                    }
                }
            }
            $mdata['loctime'] = $this->_leadorder_locktime();
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_tracking_view($leadorder, $edit =1) {
        $order_items = $leadorder['order_items'];
        $shipping = $leadorder['shipping'];
        $shipdate = ifset($shipping,'shipdate', time());
        if (empty($shipdate)) {
            $shipdate = time();
        }
        $contant = '';
        foreach ($order_items as $order_item) {
            if (!empty($order_item['item_id'])) {
                $itemcolors = $order_item['items'];
                foreach ($itemcolors as $itemcolor) {
                    if ($order_item['item_id'] > 0 ) {
                        $itemname = $order_item['item_name'].(empty($itemcolor['item_color']) ? '' : ' - '.$itemcolor['item_color']);
                    } else {
                        $itemname = $itemcolor['item_description'].(empty($itemcolor['item_color']) ? '' : ' - '.$itemcolor['item_color']);
                    }
                    $shipoptions = [
                        'shipdate' => 'To Ship '.date('m/d/y', $shipdate), // $shipping['shipdate']
                        'item' => $itemname, // $order_item['item_name'].(empty($itemcolor['item_color']) ? '' : ' - '.$itemcolor['item_color']),
                        'qty' => $itemcolor['item_qty'],
                        'order_item' => $order_item['order_item_id'],
                        'item_color' => $itemcolor['item_id'],
                    ];
                    $totaltrack = 0;
                    $totaloldtrack = 0;
                    foreach ($itemcolor['trackings'] as $tracking) {
                        $totaltrack+=$tracking['qty'];
                        if ($tracking['tracking_id']>0) {
                            $totaloldtrack+=$tracking['qty'];
                        }
                    }
                    $resttrack = intval($itemcolor['item_qty'])-$totaltrack;
                    $chkrest = intval($itemcolor['item_qty']) - $totaloldtrack;
                    $shipoptions['remind'] = $resttrack;
                    $shipoptions['completed'] = ($chkrest > 0 ? 0 : 1);
                    $trackbody = '';
                    if (!empty($itemcolor['trackings'])) {
                        $tbodyoptions = [
                            'trackings' => $itemcolor['trackings'],
                            'completed' => ($resttrack > 0 ? 0 : 1),
                            'order_item' => $order_item['order_item_id'],
                            'item_color' => $itemcolor['item_id'],
                        ];
                        $trackbody = $this->load->view('leadorderdetails/tracking_data_edit', $tbodyoptions, TRUE);
                    }
                    $shipoptions['trackbody'] = $trackbody;
                    $shipoptions['shipped'] = $totaltrack;
                    if ($edit==0) {
                        $trackcontent = $this->load->view('leadorderdetails/tracking_view', $shipoptions, TRUE);
                    } else {
                        $trackcontent = $this->load->view('leadorderdetails/tracking_edit', $shipoptions, TRUE);
                    }
                    $contant.=$trackcontent;
                }
            }
        }
        return $contant;
    }

    private function _prepare_multitrack_view($leadorder, $edit=1)
    {
        $order_items = $leadorder['order_items'];
        $shipping = $leadorder['shipping'];
        $totalitems = 0;
        $tracktotal = 0;
        foreach ($order_items as $order_item) {
            $totalitems+=$order_item['item_qty'];
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

        $trackcontent = '<div class="trackingdataarea">';
        $numhead = 1;
        $trackcontent.='<div class="multitrackbodyarea">';
        foreach ($order_items as $order_item) {
            $itemcolors = $order_item['items'];
            foreach ($itemcolors as $itemcolor) {
                $trackings = $itemcolor['trackings'];
                $shipped = 0;
                foreach ($trackings as $tracking) {
                    $shipped+=$tracking['qty'];
                }
                $completed = ($itemcolor['item_qty'] > $shipped ? 0 : 1);
                if ($order_item['item_id'] > 0) {
                    $itemname = $order_item['item_name'].(empty($itemcolor['item_color']) ? '' : ' - '.$itemcolor['item_color']);
                } else {
                    $itemname = $itemcolor['item_description'].(empty($itemcolor['item_color']) ? '' : ' - '.$itemcolor['item_color']);
                }
                $headoptions = [
                    'item' => $itemname, // $order_item['item_name'].' - '.$itemcolor['item_color'],
                    'qty' => $itemcolor['item_qty'],
                    'order_item' => $order_item['order_item_id'],
                    'item_color' => $itemcolor['item_id'],
                    'headclass' => ($numhead==1 ? '' : 'middlehead'),
                    'completed' => $completed,
                ];
                if ($edit==1) {
//                    if ($completed==1) {
//                        $trackcontent.= $this->load->view('leadorderdetails/multitrack_head_view', $headoptions, TRUE);
//                    } else {
                        $trackcontent.= $this->load->view('leadorderdetails/multitrack_head_edit', $headoptions, TRUE);
//                    }
                } else {
                    $trackcontent.= $this->load->view('leadorderdetails/multitrack_head_view', $headoptions, TRUE);
                }
                $tbodyoptions = [
                    'trackings' => $itemcolor['trackings'],
                    'completed' => $completed,
                    'order_item' => $order_item['order_item_id'],
                    'item_color' => $itemcolor['item_id'],
                    'shipped' => $shipped,
                ];
                if ($edit==1) {
                    $trackcontent.=$this->load->view('leadorderdetails/multitrack_data_edit', $tbodyoptions, TRUE);
                } else {
                    $trackcontent.=$this->load->view('leadorderdetails/multitrack_data_view', $tbodyoptions, TRUE);
                }
                $numhead++;
            }
        }
        $trackcontent.='</div>';
        $tfooteroptions = [
            'completed' => $allcompleted,
            'remind' => $remains,
            'shipdate' => 'To Ship '.date('m/d/y', $shipping['shipdate']),
        ];
        $trackcontent.=$this->load->view('leadorderdetails/multitrack_footer_view', $tfooteroptions, TRUE);
        $trackcontent.='</div>';
        return $trackcontent;
    }

    public function pototal_add()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $ordersession=(isset($postdata['ordersession']) ? $postdata['ordersession'] : 0);
            $leadorder=usersession($ordersession);
            if (!empty($leadorder)) {
                $ordercolor = ifset($postdata,'ordercolor',0);
                $editmode = ifset($postdata,'editmode',0);
                $this->load->model('leadorder_model');
                $res=$this->leadorder_model->add_amount($ordercolor, $this->USR_ID, $editmode, $leadorder, $ordersession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $session_id = 'orderamnt'.uniq_link(15);
                    usersession($session_id, $res['amount']);
                    $this->load->model('vendors_model');
                    $this->load->model('orders_model');
                    $v_options = [
                        'order_by' => 'v.vendor_name',
                        /* 'exclude' => $this->config->item('inventory_vendor'), */
                    ];
                    // $vendors=$this->vendors_model->get_vendors_list($v_options);
                    $vendors = $this->vendors_model->get_vendor_partners();
                    $methods=$this->orders_model->get_methods_edit();
                    /* Row for edit */
                    $options = [
                        'amount'=>$res['amount'],
                        'vendors'=>$vendors,
                        'methods'=>$methods,
                        'session' => $session_id,
                    ];
                    $content = $this->load->view('leadorderdetails/orderamount_edit_view', $options, TRUE);
                    $mdata['content'] = '<div class="tabledatasection whitedatarow">'.$content.'</div>';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function poamountchange()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session=ifset($postdata, 'session', 'unkn');
            $action = ifset($postdata, 'action', 'cancel');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $fldname = ifset($postdata, 'fldname','');
                $fldval = ifset($postdata, 'fldval', '');
                if (!empty($fldname)) {
                    $res = $this->leadorder_model->poamountchange($sessiondata, $session, $fldname, $fldval);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mdata['finchange'] = $res['finchange'];
                        if ($res['finchange']==1) {
                            $mdata['price'] = $res['price'];
                            $mdata['total'] = $res['total'];
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function poamountaction()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error=$this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session=ifset($postdata, 'session', 'unkn');
            $action = ifset($postdata, 'action', 'cancel');
            $edit_mode = ifset($postdata, 'edit_mode', 0);
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                // Session restored successfully
                if ($action=='save') {
                    $amntres = $this->leadorder_model->save_poamount($sessiondata, $session, $this->USR_ID);
                    $error = $amntres['msg'];
                    if ($amntres['result']==$this->success_result) {
                        $order_id = $amntres['order_id'];
                        $error = '';
                    }
                } else {
                    $order_id = $sessiondata['order_id'];
                    $error = '';
                }
                if ($error=='') {
                    $res=$this->leadorder_model->get_leadorder_projamounts($order_id);
                    $lists = $res['list'];
                    $listidx = 0;
                    foreach ($lists as $list) {
                        $details = $list['details'];
                        $view = '';
                        if (count($details) > 0) {
                            $view = $this->load->view('leadorderdetails/orderamount_details_view', ['details' => $details, 'edit_mode' => $edit_mode], TRUE);
                        }
                        $lists[$listidx]['detailsview'] = $view;
                        $listidx++;
                    }
                    $res['list'] = $lists;
                    // Get data for project amount
                    $options = [
                        'edit_mode' => $edit_mode,
                        'data' => $res,
                    ];
                    $mdata['content']=$this->load->view('leadorderdetails/orderprojcog_details_view', $options, TRUE);
                    // Profit Button
                    $mdata['profit'] = $this->_prepare_profitbtn_view($res['profit']);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_profitbtn_view($profitoptions)
    {
        if ($profitoptions['profit_class']=='project') {
            $profit_view = $this->load->view('leadorderdetails/profitproject_view', $profitoptions, TRUE);
        } else {
            $profit_view = $this->load->view('leadorderdetails/profit_view', $profitoptions, TRUE);
        }
        return $profit_view;
    }
}