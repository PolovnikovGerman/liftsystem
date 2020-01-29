<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Leadorder extends MY_Controller
{


    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    }

    public function order_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $this->load->model('leadorder_model');
            $this->load->model('engaded_model');
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
            if ($res['result']==Art::SUCCESS_RESULT) {
                $error='';
                $leadsession='leadorder'.$this->func->uniq_link(15);
                // Generate new session
                $options=array();
                $options['current_page']=(isset($postdata['page']) ? $postdata['page'] : 'art_tasks');
                $options['leadsession']=$leadsession;
                $orddata=$res['order'];
                if ($order==0) {
                    $options['order_id']=0;
                    $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                    $data=$this->func->_prepare_leadorder_view($res, $this->USR_ID, 1);
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
                        $options['order_head']=$this->load->view('leadorderdetails/head_order_view', $orddata,TRUE);
                        $options['prvorder']=$res['prvorder'];
                        $options['nxtorder']=$res['nxtorder'];
                        // Build View
                        $data=$this->func->_prepare_leadorder_view($res,$this->USR_ID, 0);
                        $order_data=$this->load->view('leadorderdetails/order_content_view', $data, TRUE);
                        // Build Content
                        $options['unlocked']=$engade_res['result'];
                        if ($engade_res['result']==Art::ERROR_RESULT) {
                            $voptions=array(
                                'user'=>$engade_res['lockusr'],
                            );
                            $options['editbtnview']=$this->load->view('leadorderdetails/orderlocked_view', $voptions, TRUE);
                        } elseif ($orddata['is_canceled']==1) {
                            $options['unlocked']=$this->error_result;
                            $options['editbtnview']=$this->load->view('leadorderdetails/ordercanceled_view', array(), TRUE);
                        }
                        $options['order_data']=$order_data;
                        $options['order_dublcnum']=$orddata['order_num'];
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
                        if ($chklock==Art::ERROR_RESULT) {
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
                $this->func->session($leadsession, $leadorder);
                $mdata['content']=$content;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}