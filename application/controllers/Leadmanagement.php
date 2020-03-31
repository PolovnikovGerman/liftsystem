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
                $lead_data['lead_number'] = $this->leads_model->get_leadnum();
                $lead_data['brand'] = $brand;
                $lead_history = array();
                $lead_usr = array();
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
                $lead_history=$this->leads_model->get_lead_history($lead_id);
                $lead_usr=$this->leads_model->get_lead_users($lead_id);
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
                // $leadrepl=0;
                $replic=$this->load->view('leads/lead_replicalock_view',array('repl'=>$lead_replic),TRUE);
            } else {
                if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin' || $this->USR_ID==$lead_data['create_user']) {
                    $replic=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic),TRUE);
                } else {
                    $replic=$this->load->view('leads/lead_replicareadonly_view',array('repl'=>$lead_replic),TRUE);
                }
            }
            // Save User Lead into session
            $session_id='leadusers'.uniq_link(10);
            usersession($session_id, $lead_replic);
            $lead_data['other_item_label']='';
            if ($lead_data['lead_item']=='Other') {
                $lead_data['other_item_label']='Type Other Item Here:';
            } elseif ($lead_data['lead_item']=='Multiple') {
                $lead_data['other_item_label']='Type Multiple Items Here:';
            } elseif ($lead_data['lead_item']=='Custom Shaped Stress Balls') {
                $lead_data['other_item_label']='Type Custom Items Here:';
            }

            $history=$this->load->view('leads/lead_history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
            $lead_tasks['edit']=$save_av;

            // $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
            $tasks='';

            $qdat=$this->questions_model->get_lead_questions($lead_id);
            if (count($qdat)==0) {
                $questions='';
            } else {
                $questions=$this->load->view('leads/lead_questions_view',array('quests'=>$qdat),TRUE);
            }

            $qdat=$this->quotes_model->get_lead_quotes($lead_id);
            if (count($qdat)==0) {
                $quotes='';
            } else {
                $quotes=$this->load->view('leads/lead_quotes_view',array('quotes'=>$qdat),TRUE);
            }

            $qdat=$this->artproof_model->get_lead_proofs($lead_id);
            if (count($qdat)==0) {
                $onlineproofs='';
            } else {
                $onlineproofs=$this->load->view('leads/lead_proofs_view',array('proofs'=>$qdat),TRUE);
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
                'session_id'=>$session_id,
            );
            $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
            $mdata['title'] = 'Lead L'.$lead_data['lead_number'].' Details';
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
                    $replic=$this->load->view('leads/lead_replicalock_view',array('repl'=>$lead_replic),TRUE);
                } else {
                    if ($this->USR_ROLE=='admin' || $this->USR_ROLE=='masteradmin') {
                        $replic=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic),TRUE);
                    } else {
                        $replic=$this->load->view('leads/lead_replicareadonly_view',array('repl'=>$lead_replic),TRUE);
                    }
                }
                // Save User Lead into session
                $session_id='leadusers'.uniq_link(10);
                usersession($session_id, $lead_replic);
                $lead_data['other_item_label']='';
                if ($lead_data['lead_item']=='Other') {
                    $lead_data['other_item_label']='Type Other Item Here:';
                } elseif ($lead_data['lead_item']=='Multiple') {
                    $lead_data['other_item_label']='Type Multiple Items Here:';
                }
                $history=$this->load->view('leads/lead_history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
                $lead_tasks['edit']=$save_av;
                $tasks='';
                $qdat=$this->questions_model->get_lead_questions($lead_id);
                if (count($qdat)==0) {
                    $questions='';
                } else {
                    $questions=$this->load->view('leads/lead_questions_view',array('quests'=>$qdat),TRUE);
                }

                $qdat=$this->quotes_model->get_lead_quotes($lead_id);
                if (count($qdat)==0) {
                    $quotes='';
                } else {
                    $quotes=$this->load->view('leads/lead_quotes_view',array('quotes'=>$qdat),TRUE);
                }

                $qdat=$this->artproof_model->get_lead_proofs($lead_id);
                if (count($qdat)==0) {
                    $onlineproofs='';
                } else {
                    $onlineproofs=$this->load->view('leads/lead_proofs_view',array('proofs'=>$qdat),TRUE);
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
                    'session_id'=>$session_id,
                );
                $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
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
            if ($res['result']==$this->success_result) {
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
                    $replic=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic,'edit'=>$save_av),TRUE);
                    /* History */
                    $lead_history=array();
                    $history=$this->load->view('leads/lead_history_view',array('data'=>$lead_history,'cnt'=>count($lead_history)),TRUE);
                    /* Tasks */
                    $lead_tasks=[]; // $this->mleads->get_lead_tasks($lead_id);
                    $lead_tasks['edit']=$save_av;
                    $tasks=$this->load->view('leads/lead_tasks_view',$lead_tasks,TRUE);
                    /* Questions */
                    $qdat=$this->questions_model->get_lead_questions($lead_id);
                    if (count($qdat)==0) {
                        $questions='';
                    } else {
                        $questions=$this->load->view('leads/lead_questions_view',array('quests'=>$qdat),TRUE);
                    }
                    /* Quotes  */
                    $qdat=$this->quotes_model->get_lead_quotes($lead_id);
                    if (count($qdat)==0) {
                        $quotes='';
                    } else {
                        $quotes=$this->load->view('leads/lead_quotes_view',array('quotes'=>$qdat),TRUE);
                    }
                    /* Online Proofs */
                    $qdat=$this->artproof_model->get_lead_proofs($lead_id);
                    if (count($qdat)==0) {
                        $onlineproofs='';
                    } else {
                        $onlineproofs=$this->load->view('leads/lead_proofs_view',array('proofs'=>$qdat),TRUE);
                    }

                    /* Get Available Items */
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
                        'session_id'=>$session_id,
                    );
                    $mdata['content']=$this->load->view('leads/lead_editform_view',$options,TRUE);
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
            if ($res['result']==$this->success_result) {
                $error = '';
                $lead_id=$res['result'];
                $mdata['lead_id']=$res['result'];
                // Get new value of Lead
                $lead_data=$this->leads_model->get_lead($lead_id);
                usersession('leaddata',$lead_data);
                $resrequest=$this->leads_model->add_proof_request($lead_data, $usr_id, $this->USR_NAME);
                $error=$resrequest['msg'];
                if ($resrequest['result']==$this->success_result) {
                    $error = '';
                    $mdata['email_id']=$resrequest['email_id'];
                }
            }
            $this->ajaxResponse($mdata,$error);
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
                    $mdata['content']=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_replic),TRUE);
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
                    $mdata['content']=$this->load->view('leads/lead_replicadd_view',array('repl'=>$newrepl),TRUE);
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
                $mdata['content']=$this->load->view('leads/lead_replicaselect_view',array('repl'=>$lead_usr),TRUE);

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
            if ($res['result']==$this->success_result) {
                $error='';
            }
            /* Get # of new messages */
            $this->ajaxResponse($mdata,$error);
        }
    }

}