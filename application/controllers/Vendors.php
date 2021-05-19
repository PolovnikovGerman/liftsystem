<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for Vendor Center

class Vendors extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        log_message('ERROR','Open Vendors');
        $this->load->model('vendors_model');
    }

    // Update vendors parameters
    public function update_vendor_param() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                // Update
                $res = $this->vendors_model->update_vendor_details($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function update_vendor_check() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                // Update
                $res = $this->vendors_model->update_vendor_check($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if ($res['newval']==0) {
                        $mdata['content'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                    } else {
                        $mdata['content'] = '<i class="fa fa-check-square" aria-hidden="true"></i>';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendor_contact_manage() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                // Update
                $res = $this->vendors_model->vendor_contact_manage($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options = [
                        'vendor_contacts' => $res['vendor_contacts'],
                        'editmode' => 1,
                    ];
                    $mdata['content'] = $this->load->view('vendorcenter/contacts_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendor_doc_manage() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                // Update
                $res = $this->vendors_model->vendor_docs_manage($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options = [
                        'vendor_docs' => $res['vendor_docs'],
                        'editmode' => 1,
                    ];
                    $mdata['content'] = $this->load->view('vendorcenter/vedordocs_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}