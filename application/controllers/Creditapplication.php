<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Creditapplication extends MY_Controller
{

    private $restore_orderdata_error='Connection Lost. Please, recall form';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('creditapp_model');
    }

    public function creditappldata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $options=array();
            $pagerows=(isset($postdata['limit']) ? $postdata['limit'] : 100);
            $pagebgn=(isset($postdata['offset']) ? $postdata['offset'] : 0);
            $options['offset']=$pagebgn*$pagerows;
            $options['limit']=$pagerows;
            if (isset($postdata['status']) && !empty($postdata['status'])) {
                $options['status']=$postdata['status'];
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=  strtoupper($postdata['search']);
            }
            // Get Data about Credit App Lines
            $res=$this->creditapp_model->get_creditapp_data($options);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $dataoptions=array(
                    'data'=>$res['data'],
                    'numrec'=>count($res['data']),
                );
                $mdata['content']=$this->load->view('crediatapp/data_table_view', $dataoptions, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function creditapplsearch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $options=array();
            if (isset($postdata['status']) && !empty($postdata['status'])) {
                $options['status']=$postdata['status'];
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=  strtoupper($postdata['search']);
            }
            $mdata['totals']=$this->creditapp_model->get_creditapp_total($options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function creditappledit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $creditapp=$this->input->post('creditapp');
            if ($creditapp==0) {
                $data=$this->creditapp_model->new_creditapp($this->USR_ID);
            } else {
                $res=$this->creditapp_model->get_creditapp($creditapp);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $data=$res['data'];
            }
            $mdata['content']=$this->load->view('crediatapp/edit_form_view', $data, TRUE);
            usersession('creditapplinedata', $data);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Data in Edit Mode
    public function creditapplchange() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $data=usersession('creditapplinedata');
            if (empty($data)) {
                $error=$this->restore_orderdata_error;
            } else {
                $postdata=$this->input->post();
                $fldname=$postdata['fldname'];
                $newval=$postdata['newval'];
                $res=$this->creditapp_model->creditapp_change($data, $fldname, $newval);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Edit form
    public function creditapplsave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $data=usersession('creditapplinedata');
            if (empty($data)) {
                $error=$this->restore_orderdata_error;
            } else {
                $res=$this->creditapp_model->creditapp_save($data, $this->USR_ID);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function creditapplcanceledit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $creditapp=$this->input->post('creditapp');
            $res=$this->creditapp_model->get_creditapp($creditapp);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $data=$res['data'];
                $mdata['content']=$this->load->view('crediatapp/data_row_view', $data, TRUE);
                usersession('creditapplinedata', NULL);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Prepare view for approve
    public function creditapplpreapprove() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $creditapp=$this->input->post('creditapp');
            $res=$this->creditapp_model->creditapp_preapprove($creditapp, $this->USR_ID);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $data=$res['data'];
                $data['reviewby']=$this->USER_NAME;
                $mdata['content']=$this->load->view('crediatapp/approve_popup_view', $data, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Aprove - reject Credit App
    public function creditapplapprove() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $res=$this->creditapp_model->creditapp_approve($postdata, $this->USR_ID);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}
