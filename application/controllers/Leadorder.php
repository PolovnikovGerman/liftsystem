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

    public function order_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $order=(isset($postdata['order']) ? $postdata['order'] : 0);
            $ordersession=$this->input->post('ordersession');
            // Remove from session
            usersession($ordersession,NULL);
            if ($order==0) {
                $res=$this->leadorder_model->add_newlead_order($this->USR_ID);
                $edit=1;
            } else {
                $res=$this->leadorder_model->get_leadorder($order, $this->USR_ID);
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
                    $data=$this->template->_prepare_leadorder_view($res, $this->USR_ID, 1);
                    $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                    $options['order_data']=$order_data;
                    $options['leadsession']=$leadsession;
                    $content=$this->load->view('leadorderdetails/placeorder_menu_edit',$options, TRUE);
                    $mdata['content']=$content;
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
                        ];
                        $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                        $options['prvorder']=$res['prvorder'];
                        $options['nxtorder']=$res['nxtorder'];
                        // Build View
                        $data=$this->template->_prepare_leadorder_view($res,$this->USR_ID, 0);
                        $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                        // Build Content
                        // $options['unlocked']=$engade_res['result'];
                        $head_options['unlocked']=$engade_res['result'];
                        if ($engade_res['result']==$this->error_result) {
                            $voptions=array(
                                'user'=>$engade_res['lockusr'],
                            );
                            // $options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                            $head_options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                        } elseif ($orddata['is_canceled']==1) {
                            // $options['unlocked']=$this->error_result;
                            // $options['editbtnview']=$this->load->view('leadorderdetails/ordercanceled_view', array(), TRUE);
                            $head_options['unlocked']=$this->error_result;
                            $head_options['editbtnview']=$this->load->view('leadorderdetails/ordercanceled_view', array(), TRUE);
                        }
                        $options['order_data']=$order_data;
                        // $options['order_dublcnum']=$orddata['order_num'];
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
                            $this->func->ajaxResponse($mdata, $error);
                        }
                        $locking=$this->engaded_model->lockentityrec($lockoptions);
                        $res['locrecid']=$locking;
                        // Build Head
                        $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                        // Build View
                        $data=$this->func->_prepare_leadorder_view($res,$this->USR_ID, $edit);

                        $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                        // Build Content
                        $options['order_data']=$order_data;
                        $options['locrecid']=$locking;
                        $options['timeout']=(time()+$this->config->item('loctimeout'))*1000;
                        $options['current_page']=(isset($postdata['page']) ? $postdata['page'] : 'art_tasks');
                        $content=$this->load->view('leadorderdetails/top_menu_edit',$options, TRUE);
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
            $order_id=$this->input->post('order');
            $ordersession=$this->input->post('ordersession');
            // Remove from session
            usersession($ordersession,NULL);
            // Generate new session
            $leadsession='leadorder'.uniq_link(15);
            $res=$this->leadorder_model->get_leadorder($order_id, $this->USR_ID);
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
                $data=$this->template->_prepare_leadorder_view($res,$this->USR_ID, 0);
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
                    $leadorder=$this->func->session($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->func->ajaxResponse($mdata, $error);
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
            $this->func->ajaxResponse($mdata, $error);
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
                    $leadorder=$this->func->session($ordersession, NULL);
                    $error=$locres['msg'];
                    $this->func->ajaxResponse($mdata, $error);
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
            $mdata['loctime']=(time()+$this->config->item('loctimeout'))*1000;
            $this->func->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}