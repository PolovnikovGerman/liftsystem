<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for Vendor Center

class Vendors extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
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
                    if ($res['fld']=='vendor_status') {
                        $mdata['status_label'] = ($res['newval']==0 ? 'Make Active' : 'Make Inactive');
                    }
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
                    $mdata['class']='';
                    if ($res['newval']==0) {
                        $mdata['content'] = '<i class="fa fa-square-o" aria-hidden="true"></i>';
                    } else {
                        $mdata['class']='checked';
                        $mdata['content'] = '<i class="fa fa-check-square" aria-hidden="true"></i>';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function update_vendor_radio() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                // Update
                $res = $this->vendors_model->update_vendor_radio($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $vendor = $res['vendor'];
                    if ($vendor['payment_prepay']==0) {
                        $mdata['prepay_class'] = '';
                        $mdata['prepay_content'] = '<i class="fa fa-circle-o" aria-hidden="true"></i>';
                    } else {
                        $mdata['prepay_class'] = 'checked';
                        $mdata['prepay_content'] = '<i class="fa fa-check-circle-o" aria-hidden="true">';
                    }
                    if ($vendor['payment_terms']==0) {
                        $mdata['term_class'] = '';
                        $mdata['term_content'] = '<i class="fa fa-circle-o" aria-hidden="true"></i>';
                    } else {
                        $mdata['term_class'] = 'checked';
                        $mdata['term_content'] = '<i class="fa fa-check-circle-o" aria-hidden="true">';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function update_vendor_address() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->vendors_model->update_vendor_address($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendordoc_upload_prepare() {
        if ($this->isAjax()) {
            $error = '';
            $mdata=[];
            $postdata = $this->input->post();
            $doctype = ifset($postdata, 'doctype','pricelist');
            if ($doctype=='pricelist') {
                $years = [];
                $curyear = intval(date('Y'));
                for ($i=0; $i<=10; $i++) {
                    $years[] = $curyear - $i;
                }
                $mdata['content'] = $this->load->view('vendorcenter/upload_pricelist_view',['years' => $years], TRUE);
            } else {
                $mdata['content'] = $this->load->view('vendorcenter/upload_otherdoc_view',[], TRUE);
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
                    $mdata['doc_type'] = $res['doc_type'];
                    if ($res['doc_type']=='PRICELIST') {
                        $price_optios = [
                            'docs' => $res['vendor_docs'],
                            'count' => count($res['vendor_docs']),
                        ];
                        $mdata['content'] = $this->load->view('vendorcenter/pricedoc_short_view', $price_optios, TRUE);
                    } else {
                        $docs = $res['vendor_docs'];
                        $listcnt = (count($docs)<19 ? 19 : count($docs));
                        $options = [
                            'docs' => $docs,
                            'count' => count($docs),
                            'listcnt' => $listcnt,
                            'editmode' => 1,
                        ];
                        $mdata['content'] = $this->load->view('vendorcenter/otherdoc_full_view', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function show_pricelist_history() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $error = '';
                $docs = $session_data['vendor_pricedocs'];
                $listcnt = (count($docs)<10 ? 10 : count($docs));
                $options = [
                    'docs' => $docs,
                    'count' => count($docs),
                    'listcnt' => $listcnt,
                ];
                if (ifset($postdata,'view','short')=='short') {
                    $mdata['content'] = $this->load->view('vendorcenter/pricedoc_short_view', $options, TRUE);
                } else {
                    $mdata['content'] = $this->load->view('vendorcenter/pricedoc_full_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function show_otherdocs_history() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $error = '';
                $docs = $session_data['vendor_otherdocs'];
                $editmode = ifset($postdata, 'editmode', 0);
                $listcnt = (count($docs)<19 ? 19 : count($docs));
                $options = [
                    'docs' => $docs,
                    'count' => count($docs),
                    'listcnt' => $listcnt,
                    'editmode' => $editmode,
                ];
                if (ifset($postdata,'view','short')=='short') {
                    $mdata['content'] = $this->load->view('vendorcenter/otherdoc_short_view', $options, TRUE);
                } else {
                    $mdata['content'] = $this->load->view('vendorcenter/otherdoc_full_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function vendor_search() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = '';
            $postdata=$this->input->post();
            $options=[
                'status' => ifset($postdata,'vendor_status',0),
                'search' => ifset($postdata, 'search',''),
                'vtype' => ifset($postdata,'vtype',''),
            ];
            $this->load->model('vendors_model');
            $mdata['totals'] = $this->vendors_model->get_count_vendors($options);
            $mdata['total_txt'] = QTYOutput($mdata['totals']).' Records';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendordata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $page_num = ifset($postdata, 'offset', 0);
            $limit = ifset($postdata, 'limit', 100);
            $offset = $page_num * $limit;
            $order_by = ifset($postdata,'order_by','vendor_name');
            $direction = ifset($postdata, 'direction','asc');
            $status = ifset($postdata,'vendor_status', 0);
            $search = ifset($postdata,'search', '');
            $vtype = ifset($postdata,'vtype','');
            $options = [
                'offset' => $offset,
                'limit' => $limit,
                'order_by' => $order_by,
                'direct' => $direction,
                'status' => $status,
                'search' => $search,
                'vtype' => $vtype,
            ];
            $vendors=$this->vendors_model->get_vendors_list($options);
            if (count($vendors)==0) {
                $content=$this->load->view('vendorcenter/emptydata_view', array(), TRUE);
            } else {
                $content=$this->load->view('vendorcenter/datalist_view',array('vendors'=>$vendors),TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function vendor_newedit() {
        if ($this->isAjax()) {
            $error = 'Vendor not found';
            $mdata = [];
            $postdata = $this->input->post();
            $vendor_id = ifset($postdata, 'vendor_id');
            if (!empty($vendor_id)) {
                if ($vendor_id<0) {
                } else {
                    $editmode = ifset($postdata, 'editmode',0);
                    $res = $this->vendors_model->get_vendor($vendor_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = [
                            'vendor' => $res['data'],
                            'vendor_contacts' => $res['vendor_contacts'],
                            'vendor_docs' => $res['vendor_docs'],
                            'deleted' => [],
                        ];
                        // $mdata['title'] = 'Change Vendor '.$datap[['vendor_name'];
                        // $editmode = 0;
                    }
                }
            }

        }
    }

    public function vendor_edit() {
        if ($this->isAjax()) {
            $error = 'Vendor not found';
            $mdata = [];
            $postdata = $this->input->post();
            $vendor_id = ifset($postdata, 'vendor_id');
            if (!empty($vendor_id)) {
                $this->load->model('vendors_model');
                if ($vendor_id<0) {
                    $error = '';
                    $data = $this->vendors_model->add_vendor();
                    $data['deleted'] = [];
                    // $mdata['title'] = 'New Vendor';
                    $editmode = 1;
                } else {
                    $editmode = ifset($postdata, 'editmode',0);
                    $res = $this->vendors_model->get_vendor($vendor_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = [
                            'vendor' => $res['data'],
                            'vendor_pricedocs' => $res['vendor_pricedocs'],
                            'vendor_otherdocs' => $res['vendor_otherdocs'],
                            'deleted' => [],
                        ];
                        // $mdata['title'] = 'Change Vendor '.$datap[['vendor_name'];
                        // $editmode = 0;
                    }
                }
                if ($error =='') {
                    $session_id = uniq_link(20);
                    usersession($session_id, $data);
                    $price_optios = [
                        'docs' => $data['vendor_pricedocs'],
                        'count' => count($data['vendor_pricedocs']),
                    ];
                    $pricedoc_view = $this->load->view('vendorcenter/pricedoc_short_view', $price_optios, TRUE);

                    $this->load->model('shipping_model');
                    $profile_options = [
                        'vendor'=>$data['vendor'],
                        'editmode' => $editmode,
                        'session' => $session_id,
                        'countries' => $this->shipping_model->get_countries_list(),
                        'pricedocview' => $pricedoc_view,
                        'otherdocs' => count($data['vendor_otherdocs']),
                    ];
                    $mdata['header'] = $this->load->view('vendorcenter/header_adaptive_view', $profile_options, TRUE);
                    $mdata['mobheader'] = $this->load->view('vendorcenter/mobile_header_view', $profile_options, TRUE);
                    $general_info = $this->load->view('vendorcenter/profilepage_general_info', $profile_options, TRUE);
                    $purchase_info = $this->load->view('vendorcenter/profilepage_purchase_info', $profile_options, TRUE);
                    $pament_info =  $this->load->view('vendorcenter/profile_payment_info', $profile_options, TRUE);
                    $prices_info = $this->load->view('vendorcenter/profile_prices_info', $profile_options, TRUE);
                    $customer_info = $this->load->view('vendorcenter/profile_customers_info', $profile_options, TRUE);
                    $document_info = $this->load->view('vendorcenter/profile_otherdoc_info', $profile_options, TRUE);

                    $profview_options = [
                        'general_view' => $general_info,
                        'purchase_view' => $purchase_info,
                        'payment_view' => $pament_info,
                        'prices_view' => $prices_info,
                        'customer_view' => $customer_info,
                        'otherdoc_view' => $document_info,
                    ];
                    $profile_view = $this->load->view('vendorcenter/profile_adaptive_view', $profview_options, TRUE);
                    $options = [
                        'profile_view' => $profile_view,
                        'editmode' => $editmode,
                    ];

                    $mdata['content']=$this->load->view('vendorcenter/details_view',$options,TRUE);
                    // $mdata['content']= '';
                    $mdata['editmode'] = $editmode;
                    $mdata['status'] = ($data['vendor']['vendor_status']==1 ? 'active' : 'inactive');
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendordata_save() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Edit Time Explain. Require data again';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('vendors_model');
                $res = $this->vendors_model->save_vendordata($session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}