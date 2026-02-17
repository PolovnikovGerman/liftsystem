<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for proof requests queries

class Leadmanagement extends MY_Controller
{
    protected $timeout_dead = 1209600;
    /* Statuses - DEAD & CLOSED */
    protected $LEAD_DEAD = 3;
    protected $LEAD_CLOSED = 4;
    protected $LEAD_OPEN = 2;
    protected $LEAD_PRIORITY = 1;
    private $restore_orderdata_error = 'Connection Lost. Please, recall form';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }

    public function edit_lead()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $lead_id = ifset($postdata, 'lead_id', 0);
            $brand = ifset($postdata, 'brand', 'SB');
            $leadfound = 0;
            $this->load->model('artproof_model');
            $this->load->model('leadquote_model');
            if ($lead_id ==0) {
                // New Lead
                $lead_data = $this->leads_model->get_empty_lead();
                $lead_data['lead_id'] = 0;
                $lead_data['lead_type'] = $this->LEAD_OPEN;
                $lead_data['lead_number'] = $this->leads_model->get_leadnum($brand);
                $lead_data['brand'] = $brand;
                $lead_history = $lead_usr = $leads_attach = [];
                $lead_contacts = [];
                for ($i=1; $i<3; $i++) {
                    $lead_contacts[] = [
                        'lead_contact_id' => $i*(-1),
                        'lead_id' => $lead_id,
                        'contact_name' => '',
                        'contact_email' => '',
                        'contact_phone' => '',
                    ];
                }
                $customer_address = [
                    'country_id' => '',
                    'country_code' => '',
                    'address_line1' => '',
                    'address_line2' => '',
                    'city' => '',
                    'zip' => '',
                    'state' => '',
                ];
                $leadfound = 1;
                $error = '';
            } else {
                $res = $this->leads_model->get_lead($lead_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $leadfound = 1;
                    $error = '';
                    $lead_data = $res['lead'];
                    if (!empty($lead_data['lead_needby'])) {
                        $lead_data['lead_needby'] = strtotime($lead_data['lead_needby']);
                    }
                    $lead_data['newhistorymsg'] = '';
                    $customer_address = $res['address'];
                    $brand = isset($lead_data['brand']) ? $lead_data['brand'] : $brand;
                    $lead_history = $this->leads_model->get_lead_history($lead_id);
                    $lead_usr = $this->leads_model->get_lead_users($lead_id);
                    $leads_attach = $this->leads_model->get_lead_attachs($lead_id);
                    $lead_contacts = $this->leads_model->get_lead_contacts($lead_id);
                }
            }
            if ($leadfound==1) {
                $replica_options = [
                    'leadusers' => $lead_usr,
                    'added' => 0,
                ];
                // Get list of users for assign
                $this->load->model('user_model');
                $active = 1;
                $usrrepl=$this->user_model->get_user_leadreplicas($active);
                $replica_options['users'] = $usrrepl;
                if ($lead_data['lead_type']==$this->LEAD_CLOSED || $lead_data['lead_type']==$this->LEAD_DEAD) {
                    if (count($lead_usr)==0) {
                        $replica_view = $this->load->view('leadpopupnew/unassigned_lead_view', $replica_options, true);
                    } else {
                        $replica_view = $this->load->view('leadpopupnew/assigned_lead_view', $replica_options, true);
                    }
                } else {
                    if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin' || $this->USR_ID==$lead_data['create_user']) {
                        $replica_options['added'] = 1;
                    }
                    if (count($lead_usr)==0) {
                        $replica_view = $this->load->view('leadpopupnew/unassigned_lead_view', $replica_options, true);
                    } else {
                        $replica_view = $this->load->view('leadpopupnew/assigned_lead_view', $replica_options, true);
                    }
                }
                $this->load->model('shipping_model');
                $countries = $this->shipping_model->get_countries_list(['orderby'=>'sort']);
                $states = [];
                if (!empty($customer_address['country_id'])) {
                    $states = $this->shipping_model->get_country_states($customer_address['country_id']);
                }
                $states_view = $this->load->view('leadpopupnew/states_view', ['states' => $states, 'statecode' => $customer_address['state']], true);
                // Customer info
                $customer_options = [
                    'customer' => $lead_data['lead_company'],
                    'contacts' => $lead_contacts,
                    'address' => $customer_address,
                    'countries' => $countries,
                    'states' => $states_view,
                ];
                $customer_view = $this->load->view('leadpopupnew/customer_view', $customer_options, true);
                // History view
                $history_view = $this->load->view('leadpopupnew/history_view', ['lead_history'=>$lead_history], true);
                // Question - Lead Relation
                $tasks = $this->leads_model->get_lead_tasks($lead_id);
                $tasks_view = $this->load->view('leadpopupnew/tasks_view', ['tasks' => $tasks], true);
                // Attachments
                $attachments_view = $this->load->view('leadpopupnew/attachments_view', ['attachments' => $leads_attach], true);
                $this->load->model('orders_model');
                $dboptions=array(
                    'exclude'=>array(-4, -5, -2),
                    'brand' => $lead_data['brand'],
                );
                $items_list = $this->orders_model->get_item_list($dboptions);
                // Quotes
                $lead_quotes = $this->leadquote_model->get_leadquotes_list($lead_id);
                $quotes_view = $this->load->view('leadpopupnew/quotes_list_view', ['quotes' => $lead_quotes], true);
                // Proof Requests
                $proofarts = $this->artproof_model->get_lead_proofs($lead_id);
                $proofarts_view = $this->load->view('leadpopupnew/proofart_list_view', ['proofs' => $proofarts], true);
                // Prepare Quote add form
                $quote_form_view = $this->_prepare_quote_form($lead_data['lead_item_id'], $lead_data['brand'], $lead_data['zip']);
                // Prepare Lead Session data
                $leaddata = [
                    'lead' => $lead_data,
                    'customer_address' => $customer_address,
                    'lead_users' => $lead_usr,
                    'leads_attachments' => $leads_attach,
                    'lead_contacts' => $lead_contacts,
                    'lead_tasks' => $tasks,
                    'lead_quotes' => $lead_quotes,
                    'lead_proofs' => $proofarts,
                    'deleted' => [],
                    'edit_flag' => $lead_id > 0 ? 0 : 1,
                ];
                $sessionid = 'lead'.uniq_link('15');
                usersession($sessionid, $leaddata);
                $content_options = [
                    'customer_view' => $customer_view,
                    'lead' => $lead_data,
                    'replica_view' => $replica_view,
                    'items' => $items_list,
                    'history_view' => $history_view,
                    'tasks_view' => $tasks_view,
                    'attachments_view' => $attachments_view,
                    'quotes_view' => $quotes_view,
                    'proofarts_list' => $proofarts_view,
                    'quote_form_view' => $quote_form_view,
                    'leadsession' => $sessionid,
                    'mapuse' => empty($this->config->item('google_map_key')) ? 0 : 1,
                ];
                $mdata['content'] = $this->load->view('leadpopupnew/page_view', $content_options, true);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    /* Prepare content for edit */
    public function edit_lead_old() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $lead_id=ifset($postdata, 'lead_id',0);
            $dead_av=1;
            $this->load->model('questions_model');
            $this->load->model('quotes_model');
            $this->load->model('artproof_model');
            $this->load->model('leadquote_model');

            if ($lead_id==0) {
                $brand = ifset($postdata,'brand');
                if (empty($brand)) {
                    $error = 'Empty Brand';
                    $this->ajaxResponse($mdata, $error);
                }

                // $replicas=$this->muser->get_user_leadreplicas(1);
                $lead_data = $this->leads_model->get_empty_lead();
                $dead_av = 0;
                $lead_data['lead_id'] = 0;
                $lead_data['lead_type'] = 2;
                $lead_data['lead_number'] = $this->leads_model->get_leadnum($brand);
                $lead_data['brand'] = $brand;
                $lead_history = array();
                $lead_usr = array();
                $leads_attach = [];
            } else {
                // $replicas=$this->muser->get_user_leadreplicas(0);
                $lead_data=$this->leads_model->get_lead($lead_id);
                if (isset($lead_data['create_date'])) {
                    $crtime=strtotime($lead_data['create_date']);
                    // Temporary COMMENTED
                    /*if (time()-$crtime<$this->timeout_dead) {
                        $dead_av=0;
                    }*/
                }
                $brand = ifset($lead_data,'brand');
                $lead_history=$this->leads_model->get_lead_history($lead_id);
                $lead_usr=$this->leads_model->get_lead_users($lead_id);
                $leads_attach = $this->leads_model->get_lead_attachs($lead_id);
            }
            $lead_tasks=$this->leads_model->get_lead_tasks($lead_id);

            $save_av=1;

            /* */
            if (count($lead_usr)==0) {
                array_push($lead_usr, $this->USR_ID);
            }
            $lead_replic=array();
            // foreach ($replicas as $row) {
            //     $row['value']=0;
            foreach ($lead_usr as $row) {
                $usr=$this->user_model->get_user_data($row);
                $lead=array(
                    'user_id'=>$row,
                    'user_leadname'=>$usr['user_leadname'],
                    'value'=>1,
                );
                $lead_replic[]=$lead;
            }
            // $leadrepl=1;
            if ($lead_data['lead_type']==$this->LEAD_CLOSED || $lead_data['lead_type']==$this->LEAD_DEAD) {
                $replic=$this->load->view('leadpopup/replicalock_view',array('repl'=>$lead_replic),TRUE);
            } else {
                if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin' || $this->USR_ID==$lead_data['create_user']) {
                    $replic=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic, 'brand' => $lead_data['brand']),TRUE);
                } else {
                    $replic=$this->load->view('leadpopup/replicareadonly_view',array('repl'=>$lead_replic),TRUE);
                }
            }
            // Save User Lead into session
            $session_id='leadusers'.uniq_link(10);
            usersession($session_id, $lead_replic);
            $attachsess = 'leadattach'.uniq_link(10);
            $leadattach = [
                'lead_attach' => $leads_attach,
                'deleted' => [],
            ];
            usersession($attachsess, $leadattach);
            $lead_data['other_item_label']='';
            if ($lead_data['lead_item']=='Other') {
                $lead_data['other_item_label']='Type Other Item Here:';
            } elseif ($lead_data['lead_item']=='Multiple') {
                $lead_data['other_item_label']='Type Multiple Items Here:';
            } elseif ($lead_data['lead_item']=='Custom Shaped Stress Balls') {
                $lead_data['other_item_label']='Type Custom Items Here:';
            }

            $history=$this->load->view('leadpopup/history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
            $lead_tasks['edit']=$save_av;

            // $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
            $tasks='';

            $qdat=$this->questions_model->get_lead_questions($lead_id);
            if (count($qdat)==0) {
                $questions='';
            } else {
                $questions=$this->load->view('leadpopup/questions_view',array('quests'=>$qdat),TRUE);
            }

            $qdat=$this->quotes_model->get_lead_quotes($lead_id);
            if (count($qdat)==0) {
                $quotes='';
            } else {
                $quotes=$this->load->view('leadpopup/quotes_view',array('quotes'=>$qdat),TRUE);
            }

            $qdat = $this->leadquote_model->get_leadquotes($lead_id);
            $lead_quotes = '';
            if (count($qdat) > 0) {
                $lead_quotes = $this->load->view('leadpopup/leadquotes_list_view',array('quotes'=>$qdat),TRUE);
            }

            $qdat=$this->artproof_model->get_lead_proofs($lead_id);
            if (count($qdat)==0) {
                $onlineproofs='';
            } else {
                $onlineproofs=$this->load->view('leadpopup/proofs_view',array('proofs'=>$qdat, 'brand' => $brand),TRUE);
            }
            $dead_option='';
            if ($dead_av==1) {
                $dead_selected=($lead_data['lead_type'] == 3 ? 'selected="selected"' : '');
                $dead_option="<option value=\"3\" ".$dead_selected.">Dormant</option>";
            } else {
                $dead_option='';
            }
            /* Get Available Items */
            // $items_list=$this->leads_model->items_list($lead_data['brand']);
            $dboptions=array(
                'exclude'=>array(-4, -5, -2),
                'brand' => $lead_data['brand'],
            );
            $this->load->model('orders_model');
            $items_list = $this->orders_model->get_item_list($dboptions);

            // Attachs
            $leadattach_view = $this->load->view('leadpopup/attach_view',array('attachs'=>$leads_attach),TRUE);
            // $itemslist=$this->m
            $options=array(
                'data'=>$lead_data,
                'history'=>$history,
                'replica'=>$replic,
                'tasks'=>$tasks,
                'quotes'=>$quotes,
                'questions'=>$questions,
                'onlineproofs'=>$onlineproofs,
                'save_available'=>$save_av,
                'dead_option'=>$dead_option,
                'items' => $items_list,
                'attachs' => $leadattach_view,
                'session_id'=>$session_id,
                'session_attach' => $attachsess,
                'brand' => $brand,
                'enable' => $save_av==1 ? '' : 'disabled="disabled"',
                'read' => $save_av==1 ? '' : 'readonly="readonly"',
                'display' => $save_av==1 ? '' :'hide',
                'lead_quotes' => $lead_quotes,
            );
            $mdata['title'] = $this->load->view('leadpopup/head_view', $options, TRUE);
            $mdata['content']=$this->load->view('leadpopup/content_view',$options,TRUE);
            $mdata['footer'] = $this->load->view('leadpopup/footer_view', $options, TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function lead_itemchange() {
        if ($this->isAjax()) {
            $mdata=array();
            $item_id=$this->input->post('item_id');
            /* Search data */
            $res=$this->leads_model->search_itemid($item_id);
            $error=$res['msg'];

            if ($res['result']==$this->success_result) {
                $mdata['item_number']=$res['item_number'];
                $mdata['item_name']=$res['item_name'];
                $mdata['other_label']='';
                if ($item_id<0) {
                    // if ($res['item_name']=='Other' || $res['item_name']=='Multiple' || $res['item_name']=='Custom Shaped Stress Balls') {
                    $mdata['other']=1;
                    switch ($res['item_name']) {
                        case 'Other':
                            $mdata['other_label']='Type Other Item Here:';
                            break;
                        case 'Multiple':
                            $mdata['other_label']='Type Multiple Items Here:';
                            break;
                        default :
                            $mdata['other_label']='Type Custom Items Here:';
                            break;
                    }
                } else {
                    $mdata['other']=0;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_attachment_delete() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Attachment session empty';
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session_id','unkn');
            $leadattachs = usersession($session_id);
            if (!empty($leadattachs)) {
                $res=$this->leads_model->attachment_remove($leadattachs, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leads_attach = $res['attachs'];
                    $mdata['content'] = $this->load->view('leadpopup/attach_view',array('attachs'=>$leads_attach),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_attachment_add() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Attachment session empty';
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'lead','unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $res=$this->leads_model->attachment_add($leaddata, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $leads_attach = $res['attachs'];
                    $mdata['content'] = $this->load->view('leadpopupnew/attachments_view', ['attachments'=>$leads_attach],TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Save Lead form data into session, show details */
    public function show_question_detail() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $data=$this->input->post();
            $leaddat=array();
            foreach ($data as $key=>$val) {
                if ($key=='quest_id') {
                    $quest_id=$val;
                } else {
                    $leaddat[$key]=$val;
                }
            }
            /* Save data to session */
            usersession('leaddata',$leaddat);
            $this->load->model('questions_model');
            $quest=$this->questions_model->get_quest_data($quest_id);
            $mdata['content']=$this->load->view('questions/details_view',$quest,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Restore Lead Edit form */
    public function restore_ledform() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mail_id = ifset($postdata,'email_id',0);
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $this->load->model('questions_model');
            $this->load->model('quotes_model');
            $this->load->model('artproof_model');
            $this->load->model('leadquote_model');
            /* Restore session data */
            $leaddat=usersession('leaddata');
            if (!empty($leaddat)) {
                /* Label */
                /* Prepare arrays for view */
                $lead_id=$leaddat['lead_id'];
                $dead_av=1;
                if ($lead_id==0) {
                    $lead_data=$this->leads_model->get_empty_lead();
                    $dead_av=0;
                    $lead_data['lead_id']=0;
                    $lead_data['lead_type']=2;
                    $lead_data['lead_number']=$this->leads_model->get_leadnum();
                    $lead_history=array();
                    $lead_usr=array();
                    $leads_attach = [];
                } else {
                    if ($mail_id > 0) {
                        // Update lead
                        $this->leads_model->update_lead($lead_id);
                    }
                    $lead_data=$this->leads_model->get_lead($lead_id);
                    if (isset($lead_data['create_date'])) {
                        $crtime=strtotime($lead_data['create_date']);
                        if (time()-$crtime<$this->timeout_dead) {
                            $dead_av=0;
                        }
                    }
                    $lead_history=$this->leads_model->get_lead_history($lead_id);
                    if (isset($leaddat['session_id'])) {
                        $session_id=$leaddat['session_id'];
                        $lead_users=usersession($session_id);
                        $lead_usr=[];
                        foreach ($lead_users as $row) {
                            array_push($lead_usr, $row['user_id']);
                        }
                    } else {
                        $lead_usr=$this->leads_model->get_lead_users($lead_id);
                    }
                    $leads_attach = $this->leads_model->get_lead_attachs($lead_id);
                }
                $lead_tasks=$this->leads_model->get_lead_tasks($lead_id);

                $save_av=1;

                /* */
                if (count($lead_usr)==0) {
                    array_push($lead_usr, $this->USR_ID);
                }
                $lead_replic=array();
                foreach ($lead_usr as $row) {
                    $usr=$this->user_model->get_user_data($row);
                    $lead=array(
                        'user_id'=>$row,
                        'user_leadname'=>$usr['user_leadname'],
                        'value'=>1,
                    );
                    $lead_replic[]=$lead;
                }
                if ($lead_data['lead_type']==$this->LEAD_CLOSED || $lead_data['lead_type']==$this->LEAD_DEAD) {
                    $replic=$this->load->view('leadpopup/replicalock_view',array('repl'=>$lead_replic),TRUE);
                } else {
                    if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin') {
                        $replic=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic,'brand' => $lead_data['brand']),TRUE);
                    } else {
                        $replic=$this->load->view('leadpopup/replicareadonly_view',array('repl'=>$lead_replic),TRUE);
                    }
                }
                // Save User Lead into session
                $session_id='leadusers'.uniq_link(10);
                usersession($session_id, $lead_replic);
                $attachsess = 'leadattach'.uniq_link(10);
                $leadattach = [
                    'lead_attach' => $leads_attach,
                    'deleted' => [],
                ];
                usersession($attachsess, $leadattach);
                $lead_data['other_item_label']='';
                if ($lead_data['lead_item']=='Other') {
                    $lead_data['other_item_label']='Type Other Item Here:';
                } elseif ($lead_data['lead_item']=='Multiple') {
                    $lead_data['other_item_label']='Type Multiple Items Here:';
                }
                $history=$this->load->view('leadpopup/history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
                $lead_tasks['edit']=$save_av;
                $tasks='';
                $qdat=$this->questions_model->get_lead_questions($lead_id);
                if (count($qdat)==0) {
                    $questions='';
                } else {
                    $questions=$this->load->view('leadpopup/questions_view',array('quests'=>$qdat),TRUE);
                }

                $qdat=$this->quotes_model->get_lead_quotes($lead_id);
                if (count($qdat)==0) {
                    $quotes='';
                } else {
                    $quotes=$this->load->view('leadpopup/quotes_view',array('quotes'=>$qdat),TRUE);
                }

                $qdat = $this->leadquote_model->get_leadquotes($lead_id);
                $lead_quotes = '';
                if (count($qdat) > 0) {
                    $lead_quotes = $this->load->view('leadpopup/leadquotes_list_view',array('quotes'=>$qdat),TRUE);
                }

                $qdat=$this->artproof_model->get_lead_proofs($lead_id);
                if (count($qdat)==0) {
                    $onlineproofs='';
                } else {
                    $onlineproofs=$this->load->view('leadpopup/proofs_view',array('proofs'=>$qdat, 'brand' => $lead_data['brand']),TRUE);
                }
                $dead_option='';
                if ($dead_av==1) {
                    $dead_selected=($lead_data['lead_type'] == 3 ? 'selected="selected"' : '');
                    $dead_option="<option value=\"3\" ".$dead_selected.">Dormant</option>";
                } else {
                    $dead_option='';
                }
                /* Get Available Items */
                $items_list=$this->leads_model->items_list($lead_data['brand']);
                $leadattach_view = $this->load->view('leadpopup/attach_view',array('attachs'=>$leads_attach),TRUE);
                $options=array(
                    'data'=>$lead_data,
                    'history'=>$history,
                    'replica'=>$replic,
                    'tasks'=>$tasks,
                    'quotes'=>$quotes,
                    'questions'=>$questions,
                    'onlineproofs'=>$onlineproofs,
                    'save_available'=>$save_av,
                    'dead_option'=>$dead_option,
                    'items' => $items_list,
                    'attachs' => $leadattach_view,
                    'session_id'=>$session_id,
                    'session_attach' => $attachsess,
                    'brand' => $lead_data['brand'],
                    'enable' => $save_av==1 ? '' : 'disabled="disabled"',
                    'read' => $save_av==1 ? '' : 'readonly="readonly"',
                    'display' => $save_av==1 ? '' :'hide',
                    'lead_quotes' => $lead_quotes,
                );
                $mdata['title'] = $this->load->view('leadpopup/head_view', $options, TRUE);
                $mdata['content']=$this->load->view('leadpopup/content_view',$options,TRUE);
                $mdata['footer'] = $this->load->view('leadpopup/footer_view', $options, TRUE);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Show proof Details from Lead Form */
    public function show_proof_details() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $data=$this->input->post();
            $leaddat=array();
            foreach ($data as $key=>$val) {
                if ($key=='proof_id') {
                    $proof_id=$val;
                } else {
                    $leaddat[$key]=$val;
                }
            }
            /* Save data to session */
            usersession('leaddata',$leaddat);
            $this->load->model('artproof_model');
            $proof=$this->artproof_model->get_proof_data($proof_id);
            $mdata['content']=$this->load->view('artproof/details_view',$proof,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function show_quote_detail() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $quote_id=$this->input->post('quote_id');
            $this->load->model('quotes_model');
            $quote=$this->quotes_model->get_quote_dat($quote_id);
            if ($quote['email_quota_link']) {
                $mdata['url']=$quote['email_quota_link'];
            } else {
                $error='Quote in Process Stage';
            }
            // $mdata['content']=$this->load->view('onlinequotes/details_view',$quote,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Duplicate Leads */
    public function dublicatelead() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('questions_model');
            $this->load->model('quotes_model');
            $this->load->model('artproof_model');
            /* Save current lead */
            $leadpost=$this->input->post();
            /* Get Tasks & user array */
            $lead_tasks=array();
            $session_id=$leadpost['session_id'];
            $lead_replic=usersession($session_id);

            $lead_usr=array();
            foreach ($lead_replic as $row) {
                array_push($lead_usr, $row['user_id']);
            }
            $lead_tasks['leadtask_id']=(isset($leadpost['leadtask_id']) ? $leadpost['leadtask_id'] : NULL);
            $lead_tasks['send_quote']=(isset($leadpost['send_quote']) ? 1 :0);
            $lead_tasks['send_artproof']=(isset($leadpost['send_artproof'])?1:0);
            $lead_tasks['send_sample']=(isset($leadpost['send_sample'])?1:0);
            $lead_tasks['answer_question']=(isset($leadpost['answer_question'])?1:0);
            $lead_tasks['other']=(isset($leadpost['other_task']) ? $leadpost['other'] : '');
            $usr_id=$this->USR_ID;
            $res=$this->leads_model->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
            $error=$res['msg'];
            if ($res['result']!=$this->error_result) {
                $lead_id=$leadpost['lead_id'];
                /* Duplicate Lead */
                $lead=$this->leads_model->duplicate_lead($lead_id,$this->USR_ID);
                if ($lead['lead_id']==0) {
                    $error='Can\'t duplicate Lead';
                } else {
                    $lead_id=$lead['lead_id'];
                    $mdata['lead_number']=$lead['lead_number'];
                    $lead_usr=$this->leads_model->get_lead_users($lead_id);
                    $lead_replic=[];
                    foreach ($lead_usr as $row) {
                        $usr=$this->user_model->get_user_data($row);
                        $lead_replic[]=[
                            'user_id'=>$row,
                            'user_leadname'=>$usr['user_leadname'],
                            'value'=>1,
                        ];
                    }
                    // Save User Lead into session
                    $session_id='leadusers'.uniq_link(10);
                    usersession($session_id, $lead_replic);
                    $save_av=1;
                    /* Get Replicas */
                    $replic=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic,'edit'=>$save_av,'brand' => $lead['brand']),TRUE);
                    /* History */
                    $lead_history=array();
                    $history=$this->load->view('leadpopup/history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
                    /* Tasks */
                    $lead_tasks=[]; // $this->mleads->get_lead_tasks($lead_id);
                    $lead_tasks['edit']=$save_av;
                    $tasks='';
                    // $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
                    /* Questions */
                    $qdat=$this->questions_model->get_lead_questions($lead_id);
                    if (count($qdat)==0) {
                        $questions='';
                    } else {
                        $questions=$this->load->view('leadpopup/questions_view',array('quests'=>$qdat),TRUE);
                    }
                    /* Quotes  */
                    $qdat=$this->quotes_model->get_lead_quotes($lead_id);
                    if (count($qdat)==0) {
                        $quotes='';
                    } else {
                        $quotes=$this->load->view('leadpopup/quotes_view',array('quotes'=>$qdat),TRUE);
                    }
                    /* Online Proofs */
                    $qdat=$this->artproof_model->get_lead_proofs($lead_id);
                    if (count($qdat)==0) {
                        $onlineproofs='';
                    } else {
                        $onlineproofs=$this->load->view('leadpopup/proofs_view',array('proofs'=>$qdat, 'brand' => $lead['brand']),TRUE);
                    }

                    $lead['other_item_label']='';
                    if ($lead['lead_item']=='Other') {
                        $lead['other_item_label']='Type Other Item Here:';
                    } elseif ($lead['lead_item']=='Multiple') {
                        $lead['other_item_label']='Type Multiple Items Here:';
                    } elseif ($lead['lead_item']=='Custom Shaped Stress Balls') {
                        $lead['other_item_label']='Type Custom Items Here:';
                    }
                    $leads_attach = $this->leads_model->get_lead_attachs($lead_id);
                    $attachsess = 'leadattach'.uniq_link(10);
                    $leadattach = [
                        'lead_attach' => $leads_attach,
                        'deleted' => [],
                    ];
                    usersession($attachsess, $leadattach);

                    $leadattach_view = $this->load->view('leadpopup/attach_view',array('attachs'=>$leads_attach),TRUE);
                    // Get Available Items
                    $items_list=$this->leads_model->items_list($lead['brand']);

                    $options=array(
                        'data'=>$lead,
                        'history'=>$history,
                        'replica'=>$replic,
                        'tasks'=>$tasks,
                        'quotes'=>$quotes,
                        'questions'=>$questions,
                        'onlineproofs'=>$onlineproofs,
                        'save_available'=>$save_av,
                        'items' => $items_list,
                        'attachs' => $leadattach_view,
                        'session_id'=>$session_id,
                        'session_attach' => $attachsess,
                    );
                    // $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
                    $mdata['title'] = $this->load->view('leadpopup/head_view', $options, TRUE);
                    $mdata['content']=$this->load->view('leadpopup/content_view',$options,TRUE);
                    $mdata['footer'] = $this->load->view('leadpopup/footer_view', $options, TRUE);
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    // Create PR requst on base of Lead
    public function lead_addproofrequst() {
        if ($this->isAjax()) {
            $error = 'Connect lost. Reload Form';
            $mdata = [];
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'lead','unkn');
            $leaddata = usersession($session_id);
            $closesession = 0;
            if (!empty($leaddata)) {
                $res = $this->leads_model->save_leadpopup($leaddata, $this->USR_ID, $session_id, $closesession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $lead_id=$res['lead_id'];
                    $mdata['lead_id']=$res['lead_id'];
                    $lead_history = $this->leads_model->get_lead_history($res['lead_id']);
                    $mdata['history_view'] = $this->load->view('leadpopupnew/history_view', ['lead_history'=>$lead_history], true);
                    $leadres=$this->leads_model->get_lead($lead_id);
                    $error = $leadres['msg'];
                    if ($leadres['result']==$this->success_result) {
                        $lead_data = $leadres['lead'];
                        $address = $leadres['address'];
                        $contacts = $this->leads_model->get_lead_contacts($lead_id);
                        $resrequest=$this->leads_model->add_proof_request($lead_data, $address, $contacts, $this->USR_ID, $this->USER_NAME);
                        $error = $resrequest['msg'];
                        if ($resrequest['result']==$this->success_result) {
                            $error = '';
                            $mdata['proof_id'] = $resrequest['email_id'];
                            // Change Session data
                            $this->load->model('artproof_model');
                            $proofarts = $this->artproof_model->get_lead_proofs($lead_id);
                            $leaddata = usersession($session_id);
                            $leaddata['lead_proofs'] = $proofarts;
                            usersession($session_id, $leaddata);
                            $mdata['proofarts_view'] = $this->load->view('leadpopupnew/proofart_list_view', ['proofs' => $proofarts], true);
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function lead_deleteproof() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $email_id=$this->input->post('email_id');
            $res=$this->leads_model->remove_proof_request($email_id, $this->USER_NAME);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Save data about Lead Form - to call edit function */
    public function lead_proofrequest() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $leadpost=$this->input->post();
            usersession('leaddata',$leadpost);
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Get Approved Proofs, related with Email */
    public function lead_approvedshow() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $email_id=$this->input->post('email_id');
            $this->load->model('artproof_model');
            $approved=$this->artproof_model->get_approved($email_id);
            if (count($approved)==0) {
                $error='Empty List of Approved Proofs';
            } else {
                $mdata['approved']=$approved;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function lead_remove_rep() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $session_id=$postdata['session_id'];
            $brand = $postdata['brand'];
            // Restore data from session
            $lead_usr=usersession($session_id);
            $error='Connect lost. Reload Form';
            if (!empty($lead_usr)) {
                $error='Lead Must have 1 Rep';
                if (count($lead_usr)>1) {
                    $error='';
                    $usrid=$postdata['user'];
                    $new_lead=array();
                    foreach ($lead_usr as $row) {
                        if ($row['user_id']!=$usrid) {
                            $new_lead[]=$row;
                        }
                    }
                    // Save to sassion
                    $lead_replic=$new_lead;
                    usersession($session_id, $lead_replic);
                    // Build New content
                    $mdata['content']=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic, 'brand' => $brand),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_addrep_view() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Connect lost. Reload Form';
            $postdata=$this->input->post();
            $session_id=$postdata['session_id'];
            $lead_usr=usersession($session_id);
            if (!empty($lead_usr)) {
                $error='';
                $used=array();
                foreach ($lead_usr as $row) {
                    array_push($used, $row['user_id']);
                }
                $active=1;
                $usrrepl=$this->user_model->get_user_leadreplicas($active);
                $newrepl=array();
                foreach ($usrrepl as $row) {
                    if (!in_array($row['user_id'], $used)) {
                        $newrepl[]=array(
                            'user_id'=>$row['user_id'],
                            'user_leadname'=>$row['user_leadname'],
                            'value'=>1,
                        );
                    }
                }
                if (count($newrepl)==0) {
                    $error='No Active users to add as Lead Rep';
                } else {
                    $mdata['content']=$this->load->view('leadpopup/replicadd_view',array('repl'=>$newrepl),TRUE);
                }
                $this->ajaxResponse($mdata, $error);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_addrep_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Connect lost. Reload Form';
            $postdata=$this->input->post();
            $session_id=$postdata['session_id'];
            $brand = $postdata['brand'];
            unset($postdata['session_id']);
            $lead_usr=usersession($session_id);
            if (!empty($lead_usr)) {
                $error='';
                foreach ($postdata as $key=>$val) {
                    $usr=$this->user_model->get_user_data($val);
                    if (ifset($usr,'user_id',0) > 0) {
                        $lead=array(
                            'user_id'=>$val,
                            'user_leadname'=>$usr['user_leadname'],
                            'value'=>1,
                        );
                        $lead_usr[]=$lead;
                    }
                }
                usersession($session_id, $lead_usr);
                // Build New content
                $mdata['content']=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_usr, 'brand' => $brand),TRUE);

            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    /* Save LEAD */
    public function save_lead() {
        if ($this->isAjax()) {
            $mdata=array();
            $leadpost=$this->input->post();
            /* Get Tasks & user array */
            $lead_tasks=array();
            $session_id=$leadpost['session_id'];
            $lead_replic=usersession($session_id);

            $lead_usr=array();
            if (is_array($lead_replic) && count($lead_replic)>0) {
                foreach ($lead_replic as $row) {
                    array_push($lead_usr, $row['user_id']);
                }
            }
            $usr_id=$this->USR_ID;
            $res=$this->leads_model->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
            $error=$res['msg'];
            if ($res['result']!=$this->error_result) {
                $error='';
            }
            /* Get # of new messages */
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function lead_data_change() {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $error='Unknown Field';
                $field = ifset($postdata, 'field_name', '');
                if (!empty($field)) {
                    $newval = ifset($postdata, 'newval', '');
                    $res = $this->leads_model->change_leadpopup_data($leaddata, $field, $newval, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        if ($field=='country_id') {
                            // Get States
                            $country = $res['newval'];
                            $this->load->model('shipping_model');
                            $states = $this->shipping_model->get_country_states($country);
                            $mdata['states_view'] = $this->load->view('leadpopupnew/states_view', ['states' => $states, 'statecode' => ''], true);
                        } elseif ($field=='lead_needby') {
                            if (empty($res['newval'])) {
                                $mdata['newdate'] = $res['newval'];
                            } else {
                                $mdata['newdate'] = date('D - M j, Y', $res['newval']);
                            }
                        } elseif ($field=='lead_item_id') {
                            if ($res['newval']==$this->config->item('custom_id')) {
                                $mdata['show_custom'] = 1;
                            } else {
                                $mdata['show_custom'] = 0;
                            }
                            // Reboot Quote form content
                            $mdata['quote_form'] = $this->_prepare_quote_form($newval, $res['brand'], $res['zip']);
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_address_change()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $error='Unknown Field';
                $field = ifset($postdata, 'field_name', '');
                if (!empty($field)) {
                    $newval = ifset($postdata, 'newval', '');
                    $res = $this->leads_model->change_leadpopup_data($leaddata, $field, $newval, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $mdata['cntchange'] = $mdata['zipchange'] = 0;
                        $this->load->model('shipping_model');
                        if ($field=='country_id') {
                            // Get States
                            $mdata['cntchange'] = 1;
                            $country = $res['newval'];
                            $cntdat = $this->shipping_model->get_country($country);
                            $mdata['country_code'] = $cntdat['country_iso_code_2'];
                            $states = $this->shipping_model->get_country_states($country);
                            $mdata['states_view'] = $this->load->view('leadpopupnew/states_view', ['states' => $states, 'statecode' => ''], true);
                        } elseif ($field=='zip') {
                            $mdata['zipchange'] = 1;
                            $leaddata = usersession($session_id);
                            $lead = $leaddata['lead'];
                            $mdata['zip'] = $lead['zip'];
                            $mdata['city'] = $lead['city'];
                            $mdata['state'] = $lead['state'];
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_contact_change()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $error='Unknown Field';
                $field = ifset($postdata, 'field_name', '');
                if (!empty($field)) {
                    $contact_id = ifset($postdata,'contact',0);
                    $newval = ifset($postdata, 'newval', '');
                    $res = $this->leads_model->change_leadpopup_contact($leaddata, $contact_id, $field, $newval, $session_id);
                    $error = $res['msg'];
                    $mdata['oldval'] = '';
                    if (isset($res['oldval'])) {
                        $mdata['oldval'] = $res['oldval'];
                    }
                    if ($res['result']==$this->success_result) {
                        $error='';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function lead_popup_save()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $closesession = ifset($postdata, 'closesession', 0);
                $res = $this->leads_model->save_leadpopup($leaddata, $this->USR_ID, $session_id, $closesession);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['lead_id'] = $res['lead_id'];
                    $mdata['lead_number'] = $res['lead_number'];
                    if ($closesession==0) {
                        $lead_history = $this->leads_model->get_lead_history($res['lead_id']);
                        $mdata['history_view'] = $this->load->view('leadpopupnew/history_view', ['lead_history'=>$lead_history], true);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function add_leadquote()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $res = $this->leads_model->save_leadpopup($leaddata, $this->USR_ID, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $lead_id = $res['lead_id'];
                    // Add Lead Quote
                    $leadsrc = $this->leads_model->get_lead($lead_id);
                    $error = $leadsrc['msg'];
                    if ($leadsrc['result']==$this->success_result) {
                        $contacts = $this->leads_model->get_lead_contacts($lead_id);
                        $lead = $leadsrc['lead'];
                        $address = $leadsrc['address'];
                        // Prices
                        $custom_item = ifset($postdata, 'custom_item', 0);
                        $promoprice_id = ifset($postdata, 'promoprice', 0);
                        if ($custom_item==0 && $promoprice_id!=='custom') {
                            $this->load->model('prices_model');
                            $prres = $this->prices_model->get_promoprice($promoprice_id);
                            $item_qty = $prres['item_qty'];
                            $item_price = $prres['sale_price'];
                        } else {
                            $item_qty = ifset($postdata, 'itemqty', 0);
                            $item_price = ifset($postdata, 'itemprice', 0);
                            // Convert price, qty
                            $item_qty = intval(str_replace(',','',$item_qty));
                            $item_price = floatval(str_replace(',','.',str_replace('$','',$item_price)));
                        }
                        $printprice = ifset($postdata, 'printprice', 0);
                        $printprice = floatval(str_replace(',','.',str_replace('$', '', $printprice)));
                        $setupprice = ifset($postdata, 'setupprice', 0);
                        $setupprice = floatval(str_replace(',','.',str_replace('$', '', $setupprice)));
                        $design = ifset($postdata, 'design', 0);
                        $design = floatval(str_replace(',','.',str_replace('$', '', $design)));
                        $discount_label = ifset($postdata, 'discount_label', '');
                        $discount = ifset($postdata, 'discount_val', 0);
                        $discount = floatval(str_replace(',','.',str_replace('$', '', $discount)));
                        $discount_exp = ifset($postdata, 'discount_exp', '');
                        $quotezip = ifset($postdata, 'quotezip', '');
                        $other_note = ifset($postdata, 'other_note', '');
                        $repcontact_note = ifset($postdata, 'repcontact_note', '');
                        $locations = [];
                        $numloc = 1;
                        for ($i=1; $i<13; $i++) {
                            if (isset($postdata['location'.$i])) {
                                if (!empty($postdata['location'.$i])) {
                                    $locations[] = [
                                        'location' => $numloc,
                                        'prints' => $postdata['location'.$i],
                                    ];
                                    $numloc++;
                                }
                            }
                        }
                        $quoteparams = [
                            'lead' => $lead,
                            'address' => $address,
                            'contacts' => $contacts,
                            'custom_item' => $custom_item,
                            'item_qty' => $item_qty,
                            'item_price' => $item_price,
                            'print_price' => $printprice,
                            'setup_price' => $setupprice,
                            'design_price' => $design,
                            'discount_label' => $discount_label,
                            'discount' => $discount,
                            'discount_exp' => $discount_exp,
                            'quotezip' => $quotezip,
                            'other_note' => $other_note,
                            'repcontact_note' => $repcontact_note,
                            'locations' => $locations,
                            'user_id' => $this->USR_ID,
                        ];
                        $this->load->model('leadquote_model');
                        $qres = $this->leadquote_model->add_leadpopup_quote($quoteparams);
                        $error = $qres['msg'];
                        if ($qres['result']==$this->success_result) {
                            $error='';
                            // Prepare content
                            $mdata['lead_id'] = $lead_id;
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function update_autoaddress()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $res = $this->leads_model->update_autoaddress($postdata, $leaddata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $leaddata = usersession($session_id);
                    $lead = $leaddata['lead'];
                    $mdata['address_1'] = $lead['address_line1'];
                    $mdata['country'] = $lead['country_id'];
                    $mdata['state'] = $lead['state'];
                    $mdata['city'] = $lead['city'];
                    $mdata['zip'] = $lead['zip'];
                    $country = ifset($res, 'country', array());
                    $mdata['country_code'] = ifset($country,'country_iso_code_2','');
                    $states = ifset($res, 'states', array());
                    $mdata['states_view'] = $this->load->view('leadpopupnew/states_view', ['states' => $states, 'statecode' => $lead['state']], true);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userreplica_popup()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $candidat = ifset($postdata,'replicas','');
                if (!empty($candidat)) {
                    $replicas = explode(',',$candidat);
                    $res = $this->leads_model->change_leadpopup_replicas($leaddata, $replicas, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $this->load->model('user_model');
                        $active = 1;
                        $usrrepl=$this->user_model->get_user_leadreplicas($active);
                        $replica_options = [
                            'leadusers' => $res['users'],
                            'added' => 1,
                            'users' => $usrrepl,
                        ];
                        $mdata['content'] = $this->load->view('leadpopupnew/assigned_lead_view', $replica_options, true);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userreplica_remove_popup()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $leadusr = ifset($postdata,'leadusr','');
                if (!empty($leadusr)) {
                    $res = $this->leads_model->remove_leadpopup_replicas($leaddata, $leadusr, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $this->load->model('user_model');
                        $active = 1;
                        $usrrepl=$this->user_model->get_user_leadreplicas($active);
                        $replica_options = [
                            'leadusers' => $res['users'],
                            'added' => 1,
                            'users' => $usrrepl,
                        ];
                        $mdata['content'] = $this->load->view('leadpopupnew/assigned_lead_view', $replica_options, true);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function revertassign()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $error = 'Relation not found';
                $leadmail = ifset($postdata,'leadmail',0);
                if (!empty($leadmail)) {
                    $res = $this->leads_model->leadpopup_revertassign($leaddata, $leadmail, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $tasks = $res['tasks'];
                        $mdata['tasksview'] = $this->load->view('leadpopupnew/tasks_view', ['tasks' => $tasks], true);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function showtask()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error='Connect lost. Reload Form';
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'lead', 'Unkn');
            $leaddata = usersession($session_id);
            if (!empty($leaddata)) {
                $error = 'Relation not found';
                $leadmail = ifset($postdata,'leadmail',0);
                $res = $this->leads_model->get_popup_task($leaddata, $leadmail, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['task'] = $res['task'];
                    $mdata['tasktype'] = $res['tasktype'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_quote_form($item_id, $brand, $zip)
    {
        // Prices
        $custom_item = 0;
        if ($item_id == $this->config->item('custom_id')) {
            $prices = $this->config->item('quote_customitem_price');
            $custom_item = 1;
            $this->load->model('leadquote_model');
            $print_price = $this->leadquote_model->custom_print_price;
            if ($brand=='SR') {
                $setup_price = $this->leadquote_model->custom_srsetup_price;
            } else {
                $setup_price = $this->leadquote_model->custom_setup_price;
            }
            $design_price = $this->config->item('custom_mischrg_value');
        } else {
            $this->load->model('prices_model');
            $pricesdat = $this->prices_model->get_itemlist_price($item_id);
            $prices = [];
            foreach ($pricesdat as $pricerow) {
                if ($pricerow['item_qty'] >= 150 && $pricerow['item_qty'] < 15000 && floatval($pricerow['sale_price']) > 0) {
                    $prices[] = $pricerow;
                }
            }
            $print_price = $this->prices_model->get_item_pricebytype($item_id,'imprint');
            $setup_price = $this->prices_model->get_item_pricebytype($item_id,'setup');
            $design_price = 0;
        }
        $this->load->model('user_model');
        $usrdat = $this->user_model->get_user_data($this->USR_ID);
        if ($custom_item) {
            $locoptions = [
                ['key' => 0, 'value' => '--', 'class' => 'emptyquoteloc'],
                ['key' => 5, 'value' => 'F', 'class' => ''],
            ];
        } else {
            $locoptions = [
                ['key' => 0, 'value' => '--', 'class' => 'emptyquoteloc'],
                ['key' => 1, 'value' => '1', 'class' => ''],
                ['key' => 2, 'value' => '2', 'class' => ''],
                ['key' => 3, 'value' => '3', 'class' => ''],
                ['key' => 4, 'value' => '4', 'class' => ''],
            ];
        }
        $options = [
            'prices' => $prices,
            'custom_item' => $custom_item,
            'print_price' => $print_price,
            'setup_price' => $setup_price,
            'design_price' => $design_price,
            'zip' => $zip,
            'quote_repcontact' => $brand=='SR' ? $usrdat['contactnote_relievers'] : $usrdat['contactnote_bluetrack'],
            'locations' => $locoptions,
        ];
        $content = $this->load->view('leadpopupnew/quote_form_view', $options, TRUE);
        return $content;
    }

}