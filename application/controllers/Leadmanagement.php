<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for proof requests queries

class Leadmanagement extends MY_Controller
{
    protected $timeout_dead = 1209600;
    /* Statuses - DEAD & CLOSED */
    protected $LEAD_DEAD = 3;
    protected $LEAD_CLOSED = 4;
    private $restore_orderdata_error = 'Connection Lost. Please, recall form';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('leads_model');
    }

    /* Prepare content for edit */
    public function edit_lead() {
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
                    $replic=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic),TRUE);
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
                $dead_option="<option value=\"3\" ".$dead_selected.">Dead</option>";
            } else {
                $dead_option='';
            }
            /* Get Available Items */
            $items_list=$this->leads_model->items_list($lead_data['brand']);
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
            $session_id = ifset($postdata,'session_id','unkn');
            $leadattachs = usersession($session_id);
            if (!empty($leadattachs)) {
                $res=$this->leads_model->attachment_add($leadattachs, $postdata, $session_id);
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
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $this->load->model('questions_model');
            $this->load->model('quotes_model');
            $this->load->model('artproof_model');
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
                        $replic=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic),TRUE);
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

                $qdat=$this->artproof_model->get_lead_proofs($lead_id);
                if (count($qdat)==0) {
                    $onlineproofs='';
                } else {
                    $onlineproofs=$this->load->view('leadpopup/proofs_view',array('proofs'=>$qdat, 'brand' => $lead['brand']),TRUE);
                }
                $dead_option='';
                if ($dead_av==1) {
                    $dead_selected=($lead_data['lead_type'] == 3 ? 'selected="selected"' : '');
                    $dead_option="<option value=\"3\" ".$dead_selected.">Dead</option>";
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
                    'brand' => $lead['brand'],
                    'enable' => $save_av==1 ? '' : 'disabled="disabled"',
                    'read' => $save_av==1 ? '' : 'readonly="readonly"',
                    'display' => $save_av==1 ? '' :'hide',
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
                    $replic=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic,'edit'=>$save_av),TRUE);
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

    /* Create PR requst on base of Lead */
    public function lead_addproofrequst() {
        if ($this->isAjax()) {
            $mdata=array();
            $leadpost=$this->input->post();
            /* Get Tasks & user array */
            $lead_tasks=array();
            $session_id=$leadpost['session_id'];
            $lead_replic=usersession($session_id);
            $lead_usr=array();
            foreach ($lead_replic as $row) {
                array_push($lead_usr, $row['user_id']);
            }
            $usr_id=$this->USR_ID;
            $res=$this->leads_model->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
            $error=$res['msg'];
            if ($res['result']!=$this->error_result) {
                $error = '';
                $lead_id=$res['result'];
                $mdata['lead_id']=$res['result'];
                // Get new value of Lead
                $lead_data=$this->leads_model->get_lead($lead_id);
                usersession('leaddata',$lead_data);
                $resrequest=$this->leads_model->add_proof_request($lead_data, $usr_id, $this->USER_NAME);
                $error=$resrequest['msg'];
                if ($resrequest['result']==$this->success_result) {
                    $error = '';
                    $mdata['email_id']=$resrequest['email_id'];
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
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
                    $mdata['content']=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_replic),TRUE);
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
            unset($postdata['session_id']);
            $lead_usr=usersession($session_id);
            if (!empty($lead_usr)) {
                $error='';
                foreach ($postdata as $key=>$val) {
                    $usr=$this->user_model->get_user_data($val);
                    $lead=array(
                        'user_id'=>$val,
                        'user_leadname'=>$usr['user_leadname'],
                        'value'=>1,
                    );
                    $lead_usr[]=$lead;
                }
                usersession($session_id, $lead_usr);
                // Build New content
                $mdata['content']=$this->load->view('leadpopup/replicaselect_view',array('repl'=>$lead_usr),TRUE);

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
            foreach ($lead_replic as $row) {
                array_push($lead_usr, $row['user_id']);
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

    public function lead_addquote() {
        if ($this->isAjax()) {
            $mdata=array();
            $leadpost=$this->input->post();
            /* Get Tasks & user array */
            $lead_tasks=array();
            $session_id=$leadpost['session_id'];
            $lead_replic=usersession($session_id);
            $lead_usr=array();
            foreach ($lead_replic as $row) {
                array_push($lead_usr, $row['user_id']);
            }
            $usr_id=$this->USR_ID;
            $res=$this->leads_model->save_leads($lead_usr,$lead_tasks,$leadpost,$usr_id);
            $error=$res['msg'];
            if ($res['result']!=$this->error_result) {
                $error = '';
                $lead_id=$res['result'];
                $mdata['lead_id']=$res['result'];
                // Get new value of Lead
                $lead_data=$this->leads_model->get_lead($lead_id);
                usersession('leaddata',$lead_data);
                $this->load->model('leadquote_model');
                $qres=$this->leadquote_model->add_leadquote($lead_data, $usr_id, $this->USER_NAME);
                $error=$qres['msg'];
                if ($qres['result']==$this->success_result) {
                    $error = '';
                    if ($lead_data['brand']=='SR') {
                        $templlists = [
                            'Supplier',
                            'Proforma Invoice',
                        ];
                    } else {
                        $templlists = [
                            'Stressballs.com',
                            'Bluetrack Health',
                            'Proforma Invoice',
                        ];
                    }
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);
                    $billstates = $shipstates = [];
                    if (!empty($quote['shipping_country'])) {
                        $shipstates = $this->shipping_model->get_country_states($quote['shipping_country']);
                    };
                    if (!empty($quote['billing_country'])) {
                        $billstates = $this->shipping_model->get_country_states($quote['billing_country']);
                    };

                    // $states = $this->shipping_model->get_country_states($this->config->item('default_country'));
                    // Prepare content
                    $quotedata = $qres['quote'];
                    $quote_items = $qres['quote_items'];
                    $item_content='';
                    $item_subtotal = 0;
                    foreach ($quote_items as $quote_item) {
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => 1,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit'=>1,
                            'item_id'=>$quote_item['item_id'],
                        ];
                        $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        $item_subtotal+=$quote_item['item_subtotal'];
                    }
                    $quotedata['items_subtotal'] = $item_subtotal;
                    $quotedata['quote_total'] = $item_subtotal;
                    $quote_session = 'quote'.uniq_link(15);
                    $sessiondata = [
                        'quote' => $quotedata,
                        'items' => $quote_items,
                        'shipping' => [],
                        'deleted' => [],
                    ];
                    usersession($quote_session, $sessiondata);
                    $options = [
                        'quote_session' => $quote_session,
                        'quote_id' => $quotedata['quote_id'],
                        'lead_id' => $lead_id,
                        'data' => $quotedata,
                        'itemsview' => $item_content,
                        'templlists' => $templlists,
                        'countries' => $countries,
                        'edit_mode' => 1,
                        'shipstates' => $shipstates,
                        'billstates' => $billstates,
                        'shiprates' => '',
                    ];
                    $mdata['quotecontent'] = $this->load->view('leadpopup/quotedata_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function quoteedit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Unknown Quote #';
            $postdata = $this->input->post();
            $quote_id = ifset($postdata, 'quote_id', 0);
            $edit_mode = ifset($postdata, 'edit_mode', 0);
            if (!empty($quote_id)) {
                $this->load->model('leadquote_model');
                $qres=$this->leadquote_model->get_leadquote($quote_id, $edit_mode);
                $error = $qres['msg'];
                if ($qres['result']==$this->success_result) {
                    $error = '';
                    // Prepare content
                    $quotedata = $qres['quote'];
                    if ($quotedata['brand']=='SR') {
                        $templlists = [
                            'Supplier',
                            'Proforma Invoice',
                        ];
                    } else {
                        $templlists = [
                            'Stressballs.com',
                            'Bluetrack Health',
                            'Proforma Invoice',
                        ];
                    }
                    $this->load->model('shipping_model');
                    $cnt_options=array(
                        'orderby'=>'sort, country_name',
                    );
                    $countries = $this->shipping_model->get_countries_list($cnt_options);
                    $billstates = $shipstates = [];
                    if (!empty($quotedata['shipping_country'])) {
                        $shipstates = $this->shipping_model->get_country_states($quotedata['shipping_country']);
                    };
                    if (!empty($quotedata['billing_country'])) {
                        $billstates = $this->shipping_model->get_country_states($quotedata['billing_country']);
                    };
                    $quote_items = $qres['items'];
                    $shippings = $qres['shippings'];
                    $item_content='';
                    foreach ($quote_items as $quote_item) {
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => $edit_mode,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit'=> $edit_mode,
                            'item_id'=>$quote_item['item_id'],
                        ];
                        if ($edit_mode==0) {
                            $item_content.=$this->load->view('leadpopup/items_data_view', $item_options, TRUE);
                        } else {
                            $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        }
                    }
                    // Shipping view
                    $shiprates = '';
                    if (count($shippings) > 0) {
                        $shipoptions = [
                            'edit_mode' => $edit_mode,
                            'shippings' => $shippings,
                        ];
                        $shiprates = $this->load->view('leadpopup/quote_shiprates_view', $shipoptions, TRUE);
                    }
                    $quote_session = 'quote'.uniq_link(15);
                    $sessiondata = [
                        'quote' => $quotedata,
                        'items' => $quote_items,
                        'shipping' => $shippings,
                        'deleted' => [],
                    ];
                    usersession($quote_session, $sessiondata);
                    $options = [
                        'quote_session' => $quote_session,
                        'quote_id' => $quote_id,
                        'lead_id' => $quotedata['lead_id'],
                        'data' => $quotedata,
                        'itemsview' => $item_content,
                        'templlists' => $templlists,
                        'countries' => $countries,
                        'edit_mode' => $edit_mode,
                        'shipstates' => $shipstates,
                        'billstates' => $billstates,
                        'shiprates' => $shiprates,
                    ];
                    $mdata['quotecontent'] = $this->load->view('leadpopup/quotedata_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteparamchange() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $this->load->model('leadquote_model');
                $res = $this->leadquote_model->quoteparamchange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['totalcalc'] = $res['totalcalc'];
                    if ($res['totalcalc']==1) {
                        $this->leadquote_model->calc_quote_totals($session_id);
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteaddresschange() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $this->load->model('leadquote_model');
                $res = $this->leadquote_model->quoteaddresschange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $mdata['shiprebuild'] = $res['shiprebuild'];
                    if ($res['shiprebuild']==1) {
                        $mdata['shipping_zip'] = $quote['shipping_zip'];
                        $mdata['shipping_city'] = $quote['shipping_city'];
                        $mdata['shipping_state'] = $quote['shipping_state'];
                    }
                    $mdata['calcship'] = $res['calcship'];
                    if ($res['calcship']==1) {
                        $this->leadquote_model->calc_quote_shipping($session_id);
                        // Show shipping costs
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['shipping_cost'] = $quote['shipping_cost'];
                        $shipping = $quotesession['shipping'];
                        $options = [
                            'shippings' => $shipping,
                            'edit_mode' => 1,
                        ];
                        $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);

                    }
                    $mdata['totalcalc'] = $res['totalcalc'];
                    if ($res['totalcalc']==1) {
                        $this->leadquote_model->calc_quote_totals($session_id);
                        $quotesession = usersession($session_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteratechange() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $this->load->model('leadquote_model');
                $res = $this->leadquote_model->quoteratechange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $this->leadquote_model->calc_quote_totals($session_id);
                    $quotesession = usersession($session_id);
                    $quote = $quotesession['quote'];
                    $shipping = $quotesession['shipping'];
                    $options = [
                        'shippings' => $shipping,
                        'edit_mode' => 1,
                    ];
                    $mdata['shippingview'] = $this->load->view('leadpopup/quote_shiprates_view', $options, TRUE);
                    $mdata['shipping_cost'] = $quote['shipping_cost'];
                    $mdata['total'] = MoneyOutput($quote['quote_total']);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteitemchange() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $this->load->model('leadquote_model');
                $res = $this->leadquote_model->quoteitemchange($postdata, $quotesession, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['itemcolor_subtotal'] = MoneyOutput($res['item_subtotal']);
                    $mdata['totals'] = 0;
                    $mdata['refresh'] = 0;
                    $mdata['shipping'] = 0;
                    if ($res['shipcalc']==1) {
                        $this->leadquote_model->calc_quote_shipping($session_id);
                    }

                    if ($res['totalcalc']==1) {
                        $this->leadquote_model->calc_quote_totals($session_id);
                        $quotesession = usersession($session_id);
                        $mdata['items_subtotal'] = MoneyOutput($quotesession['quote']['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quotesession['quote']['quote_total']);
                        $mdata['totals'] = 1;
                    }
                    if ($res['item_refresh']==1) {
                        // Update content
                        $quotesession = usersession($session_id);
                        $quote_items = $quotesession['items'];
                        $item_content='';
                        foreach ($quote_items as $quote_item) {
                            if ($quote_item['quote_item_id']==$postdata['item']) {
                                $imprints=$quote_item['imprints'];
                                $imprint_options=[
                                    'quote_item_id'=>$quote_item['quote_item_id'],
                                    'imprints'=>$imprints,
                                    'edit_mode' => 1,
                                ];
                                $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                                $item_options=[
                                    'quote_item_id'=>$quote_item['quote_item_id'],
                                    'items'=>$quote_item['items'],
                                    'imprintview'=>$imprintview,
                                    'edit'=>1,
                                    'item_id'=>$quote_item['item_id'],
                                ];
                                $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                            }
                        }
                        $mdata['itemcontent'] = $item_content;
                        $mdata['refresh'] = 1;

                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteitemaddcolor() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'unkn');
            $quotesession = usersession($session_id);
            if (!empty($quotesession)) {
                $this->load->model('leadquote_model');
                $res = $this->leadquote_model->additemcolor($quotesession, $postdata,  $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $item_content='';
                    $quote_item = $res['item'];
                    $imprints=$quote_item['imprints'];
                    $imprint_options=[
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'imprints'=>$imprints,
                        'edit_mode' => 1,
                    ];
                    $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                    $item_options=[
                        'quote_item_id'=>$quote_item['quote_item_id'],
                        'items'=>$quote_item['items'],
                        'imprintview'=>$imprintview,
                        'edit' => 1,
                        'item_id' => $quote_item['item_id'],
                    ];
                    $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                    $mdata['itemcontent'] = $item_content;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteitemprintdetails() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_orderdata_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata,'session', 'unkn');
            $session = usersession($session_id);
            if (!empty($session)) {
                $this->load->model('leadquote_model');
                $res = $this->leadquote_model->prepare_print_details($session, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $details=$res['imprint_details'];
                    // $order_blank=$res['order_blank'];
                    $item_id=$res['item_id'];
                    /*if ($order_blank==0) {
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
                    } */
                    // Prepare View
                    $imptintid='imprintdetails'.uniq_link(15);
                    $options=array(
                        'details' => $details,
                        'item_number' => $res['item_number'],
                        'quote_blank' => 0, // $order_blank,
                        'imprints' => $res['imprints'],
                        'numlocs' => count($res['imprints']),
                        'item_name' => $res['item_name'],
                        'imprintsession'=>$imptintid,
                    );
                    $mdata['content']=  $this->load->view('leadpopup/imprint_details_edit', $options, TRUE);

                    $imprintdetails=array(
                        'imprint_details' => $details,
                        'quote_blank' => 0, // $order_blank,
                        'quote_item_id' => $postdata['item'],
                        'item_id' => $item_id,
                    );
                    usersession($imptintid, $imprintdetails);

                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quoteprintdetails_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession', 'unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession','unkn');
                $imprint_details = usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $this->load->model('leadquote_model');
                    $res = $this->leadquote_model->change_imprint_details($imprint_details, $postdata, $imprintsession_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $mdata['fldname']=$res['fldname'];
                        $mdata['details']=$res['details'];
                        $mdata['newval']=$res['newval'];
                        if ($mdata['fldname']=='imprint_type') {
                            if ($mdata['newval']=='NEW') {
                                $mdata['setup'] =  number_format($res['setup'],2,'.','');
                            } else {
                                $mdata['class']=$res['class'];
                            }
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function edit_repeatnote() {
        $postdata=$this->input->post();
        $quotesession_id = ifset($postdata, 'quotesession', 'unkn');
        $quotesession = usersession($quotesession_id);

        if (empty($quotesession)) {
            echo $this->restore_orderdata_error;
            die();
        }
        // Details
        $detail_id=ifset($postdata, 'details', 0);
        $imprintdetails=$postdata['imprintsession'];
        $imprint_details=usersession($imprintdetails);

        if (empty($imprint_details)) {
            echo $this->restore_orderdata_error;
            die();
        }
        $this->load->model('leadquote_model');
        $res=$this->leadquote_model->get_repeat_note($imprint_details, $detail_id, $imprintdetails);
        if ($res['result']==$this->error_result) {
            echo $res['msg'];
        } else {
            $note=$res['repeat_note'];
            $content=$this->load->view('leadpopup/repeat_note_edit', array('repeat_note'=>$note),TRUE);
            echo $content;
        }
        return TRUE;
    }

    public function repeatnote_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession','unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession','unkn');
                $imprint_details=usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $this->load->model('leadquote_model');
                    $res=$this->leadquote_model->save_repeat_note($imprint_details, $postdata, $imprintsession_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_imprintdetails() {
        if ($this->isAjax()) {
            $mdata = [];
            $error=$this->restore_orderdata_error;
            $postdata=$this->input->post();
            $quotesession_id = ifset($postdata, 'quotesession','unkn');
            $quotesession = usersession($quotesession_id);
            if (!empty($quotesession)) {
                usersession($quotesession_id, $quotesession);
                $imprintsession_id = ifset($postdata, 'imprintsession', 'unkn');
                $imprint_details = usersession($imprintsession_id);
                if (!empty($imprint_details)) {
                    $this->load->model('leadquote_model');
                    $res = $this->leadquote_model->save_imprint_details($imprint_details, $imprintsession_id, $quotesession, $quotesession_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        // Start
                        $this->leadquote_model->calc_quote_totals($quotesession_id);
                        $quotesession = usersession($quotesession_id);
                        $quote = $quotesession['quote'];
                        $mdata['items_subtotal'] = MoneyOutput($quote['items_subtotal']);
                        $mdata['total'] = MoneyOutput($quote['quote_total']);
                        $mdata['tax']=number_format(floatval($quote['sales_tax']),2,'.','');

                        $item_content='';
                        $quote_item = $res['item'];
                        $imprints=$quote_item['imprints'];
                        $imprint_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'imprints'=>$imprints,
                            'edit_mode' => 1,
                        ];
                        $imprintview=$this->load->view('leadpopup/imprint_data_edit', $imprint_options, TRUE);
                        $item_options=[
                            'quote_item_id'=>$quote_item['quote_item_id'],
                            'items'=>$quote_item['items'],
                            'imprintview'=>$imprintview,
                            'edit' => 1,
                            'item_id' => $quote_item['item_id'],
                        ];
                        $item_content.=$this->load->view('leadpopup/items_data_edit', $item_options, TRUE);
                        $mdata['itemcontent'] = $item_content;
                        $mdata['item_id'] = $quote_item['quote_item_id'];
                        // End
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function quotesave() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = $this->restore_orderdata_error;
            $session_id = ifset($postdata,'session','unknw');
            $quotesession = usersession($session_id);
            $mdata = [];
            if (!empty($quotesession)) {
                $lead_id = ifset($postdata,'lead',0);
                if (!empty($lead_id)) {
                    $this->load->model('leadquote_model');
                    $res = $this->leadquote_model->savequote($quotesession, $lead_id, $this->USR_ID,  $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        // Get leads list
                        $qdat = $this->leadquote_model->get_leadquotes($lead_id);
                        $lead_quotes = '';
                        if (count($qdat) > 0) {
                            $lead_quotes = $this->load->view('leadpopup/leadquotes_list_view',array('quotes'=>$qdat),TRUE);
                        }
                        $mdata['quotescontent'] = $lead_quotes;
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}