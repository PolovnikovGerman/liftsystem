<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tickets extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('tickets_model');
    }

    function saveattach() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Edit Session Expired. Please, re-connect';
            $filename=$this->input->post('filename');
            $doc_name=$this->input->post('doc_name');
            $ticket_id=$this->input->post('ticket_id');
            $sess_id=usersession('ticketattach');
            if (!empty($sess_id)) {
                /* Save file */
                $res=$this->tickets_model->save_uploadattach($filename,$doc_name,$sess_id);
                if ($res==0) {
                    $error='Attachment wasn\'t saved';
                } else {
                    $error = '';
                    $attachment_list=$this->tickets_model->get_attachments($ticket_id,$sess_id);
                    $cnt=count($attachment_list);
                    $options=array(
                        'ticket_id'=>$ticket_id,
                        'list'=>$attachment_list,
                        'cnt'=>$cnt,
                    );
                    $mdata['content']=$this->load->view('tickets/ticket_attachlist_view',$options,TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function save_ticket() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $tickdata=array();
            $tickdata['ticket_id']=$this->input->post('ticket_id');
            $tickdata['type']=$this->input->post('type');
            $tickdata['order_num']=$this->input->post('order_num');
            $tickdata['customer']=$this->input->post('customer');
            $tickdata['custom_issue_id']=$this->input->post('custom_issue_id');
            $tickdata['custom_description']=$this->input->post('custom_description');
            $tickdata['custom_close']=$this->input->post('custom_closed');
            $tickdata['ticket_adjast']=$this->input->post('ticket_adjast');
            $tickdata['custom_history']=$this->input->post('custom_history');
            $tickdata['cost']=$this->input->post('cost');
            $tickdata['vendor_id']=$this->input->post('vendor_id');
            $tickdata['vendor_issue_id']=$this->input->post('vendor_issue_id');
            $tickdata['vendor_description']=$this->input->post('vendor_description');
            $tickdata['vendor_close']=$this->input->post('vendor_closed');
            $tickdata['vendor_history']=$this->input->post('vendor_history');
            $tickdata['other_vendor']=$this->input->post('other_vendor');
            $tickdate=$this->input->post('ticket_date');
            $tickdata['ticket_date']=null;
            if ($tickdate!='') {
                $tickdata['ticket_date']=  strtotime($tickdate);
            }
            $user_id=$this->USR_ID;
            $res=$this->tickets_model->save_ticket($tickdata,$user_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $ticket_id=$res['ticket'];
                $sess_id=usersession('ticketattach');
                $this->tickets_model->save_attach($ticket_id,$sess_id);
            }
            $this->ajaxResponse($mdata,$error);
        }

    }



}