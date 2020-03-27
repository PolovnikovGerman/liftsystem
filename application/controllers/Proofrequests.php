<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for proof requests queries

class Proofrequests extends MY_Controller
{
    protected $timeout_dead=1209600;
    /* Statuses - DEAD & CLOSED */
    protected $LEAD_DEAD=3;
    protected $LEAD_CLOSED=4;
    private $restore_orderdata_error='Connection Lost. Please, recall form';

    public function __construct()
    {
        parent::__construct();
    }

    /* Count # of Proofs */
    public function proof_count() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $brand=$this->input->post('brand');
            $search_val=$this->input->post('search');
            $assign=$this->input->post('assign');
            $show_deleted=$this->input->post('show_deleted',0);
            $search=array();
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($search_val) {
                $search['search']=$search_val;
            }
            if ($brand) {
                $search['brand']=$brand;
            }
            if ($show_deleted==1) {
                $search['show_deleted']=1;
            }
            $this->load->model('artproof_model');
            $mdata['total_rec']=$this->artproof_model->get_count_proofs($search);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function proof_listdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $show_deleted=$this->input->post('show_deleted');
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','desc');
            $maxval=$this->input->post('maxval');
            $brand=$this->input->post('brand');
            $search_val=$this->input->post('search');
            $assign=$this->input->post('assign');
            $search=array();
            if ($assign) {
                $search['assign']=$assign;
            }
            if ($search_val) {
                $search['search']=$search_val;
            }
            if ($brand) {
                $search['brand']=$brand;
            }
            if ($show_deleted==1) {
                $search['show_deleted']=1;
            }

            $ordoffset=$offset*$limit;
            $offset=$offset*$limit;

            if ($ordoffset>$maxval) {
                $ordnum = $maxval;
            } else {
                $ordnum = $maxval - $ordoffset;
            }
            $this->load->model('artproof_model');
            $email_dat=$this->artproof_model->get_artproofs($search,$order_by,$direct,$limit,$offset,$maxval);

            if (count($email_dat)==0) {
                $content = $this->load->view('artrequest/proofs_emptytabledat_view',array(), TRUE);
            } else {
                $emails = [];
                foreach ($email_dat as $row) {
                    $row['lastmsg']='/proofrequests/proof_lastmessage?d='.$row['email_id'];
                    $emails[]=$row;
                }
                $data=array('email_dat'=>$emails);
                $content = $this->load->view('artrequest/proofs_tabledat_view',$data, TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata,$error);
        }
    }

    // Last Art message
    public function proof_lastmessage() {
        $order_id=$this->input->get('d');
        $this->load->model('artproof_model');
        $out_msg=$this->artproof_model->get_lastupdate($order_id,'artproofs');
        echo $out_msg;
    }

    /* Mark proof as Void */
    public function proof_delete() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $proof_id=$this->input->post('proof_id');
            $type=$this->input->post('type');
            /* Mark need proof as deleted / VOID */
            $this->load->model('artproof_model');
            if ($type=='delete') {
                $res=$this->artproof_model->delete_proof($proof_id);
            } else {
                $res=$this->artproof_model->revert_proof($proof_id);
            }

            if ($res['result']==$this->error_result) {
                /* Get data about proofs */
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    function change_status() {
        if ($this->isAjax()) {
            $mdata=array();
            $quest_id=$this->input->post('quest_id');
            $type=$this->input->post('type');
            $this->load->model('leads_model');
            $this->load->model('questions_model');
            $chkrel=$this->leads_model->check_leadrelation($quest_id);
            if ($chkrel) {
                $error='This Request Related with Lead. Please, reload page';
                $this->ajaxResponse($mdata, $error);
            }
            /* Get data about question */
            $res = $this->questions_model->get_quest_data($quest_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $quest= $res['data'];
                /* Get open leads  */
                $options=array(
                    'orderby'=>'lead_number',
                    'direction'=>'desc',
                );
                $leaddat=$this->leads_model->get_lead_list($options);
                $options=array('leads'=>$leaddat,'current'=>$quest['lead_id']);
                switch ($type) {
                    case 'quote':
                        $options['title']='Quote Details';
                        break;
                    case 'question':
                        $options['title']='Question Details';
                        break;
                    case 'proof':
                        $options['title']='Proof Details';
                        break;
                    default:
                        $options['title']='Message Details';
                        break;
                }
                $quest['leadselect']=$this->load->view('artrequest/lead_openlist_view',$options,TRUE);
                $mdata['content']=$this->load->view('artrequest/update_status_view',$quest,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Data about new lead */
    public function change_leadrelation() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $lead_id=$this->input->post('lead_id');
            if (!$lead_id) {
                $error='Unknown Lead';
            } else {
                $this->load->model('leads_model');
                $leaddata=$this->leads_model->get_lead($lead_id);
                if (!isset($leaddata['lead_id'])) {
                    $error='Lead not found';
                } else {
                    $mdata['lead_date']=($leaddata['lead_date']==0 ? '' : 'Date: '.date('m/d/y',$leaddata['lead_date']));
                    $mdata['lead_customer']='Name: '.$leaddata['lead_customer'];
                    $mdata['lead_mail']='Email: '.$leaddata['lead_mail'];
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function savequeststatus() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $quest=$this->input->post();
            /* Get data about question */
            $this->load->model('leads_model');
            $res=$this->leads_model->save_leadrelation($quest);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $this->load->model('questions_model');
                $res =$this->questions_model->get_quest_data($quest['mail_id']);
                $data = $res['data'];
                /* Recalculate Totals New  */
                $mdata['type']=$data['email_type'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function create_leadmessage() {
        if ($this->isAjax()) {
            $mdata = array();
            $email_id = $this->input->post('mail_id');
            $leademail_id = $this->input->post('leademail_id');
            $type = $this->input->post('type');
            $this->load->model('leads_model');
            $chkrel = $this->leads_model->check_leadrelation($email_id);
            if ($chkrel) {
                $error = 'This Request Related with Lead. Please, reload page';
                $this->ajaxResponse($mdata, $error);
            }

            switch ($type) {
                case 'Question':
                    $this->load->model('questions_model');
                    $dat = $this->questions_model->get_quest_data($email_id);
                    $maildat = $dat['data'];
                    $res = $this->leads_model->create_leadquest($maildat, $leademail_id, $this->USR_ID);
                    break;
                case 'Quote':
                    $this->load->model('quotes_model');
                    $maildat = $this->quotes_model->get_quote_dat($email_id);
                    $res = $this->leads_model->create_leadquote($maildat, $leademail_id, $this->USR_ID);
                    break;
                case 'Proof';
                    $this->load->view('artproof_model');
                    $maildat = $this->artproof_model->get_proof_data($email_id);
                    $res = $this->leads_model->create_leadproof($maildat, $leademail_id, $this->USR_ID);
                    break;
                default:
                    break;
            }

            $error = $res['msg'];

            if ($res['result'] == $this->success_result) {
                $error = '';
                $mdata['leadid'] = $res['result'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Open Order note */
    public function proof_openartnote() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';

            $mail_id=$this->input->post('mail_id');
            $this->load->model('artproof_model');
            $order_dat=$this->artproof_model->get_proof_data($mail_id);
            $options=array(
                'title'=>' Proof Request # '.$order_dat['proof_num'],
                'order_id'=>$order_dat['email_id'],
                'art_note'=>$order_dat['email_questions'],
            );
            $mdata['content']=$this->load->view('artrequest/order_noteedit_view',$options,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    /* Save order note */
    public function proof_saveartnote() {
        if ($this->isAjax()) {
            $mdata=array();
            $mail_id=$this->input->post('mail_id');
            $email_questions=$this->input->post('art_note');
            $this->load->model('artproof_model');
            $res=$this->artproof_model->save_artnote($mail_id,$email_questions);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    /* Include Proofs */
    public function proof_include() {
        if ($this->isAjax()) {
            $mdata=array();

            $email_id=$this->input->post('email_id');
            $this->load->model('artproof_model');
            $data=$this->artproof_model->get_proof_data($email_id);
            if ($data['email_include_lead']==1) {
                $newval=0;
            } else {
                $newval=1;
            }
            $res=$this->artproof_model->update_proof_include($email_id, $newval);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $mdata['content']=$res['newicon'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Prepare content for edit */
    public function edit_lead() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $lead_id=$this->input->post('lead_id');
            $dead_av=1;
            $this->load->model('leads_model');
            $this->load->model('questions_model');
            $this->load->model('quotes_model');
            $this->load->model('artproof_model');
            if ($lead_id==0) {
                // $replicas=$this->muser->get_user_leadreplicas(1);
                $lead_data=$this->leads_model->get_empty_lead();
                $dead_av=0;
                $lead_data['lead_id']=0;
                $lead_data['lead_type']=2;
                $lead_data['lead_number']=$this->leads_model->get_leadnum();
                $lead_history=array();
                $lead_usr=array();
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
            $mdata['title'] = 'Lead '.$lead_data['lead_number'].' Details';
            $this->ajaxResponse($mdata,$error);
        }
    }



}