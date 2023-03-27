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
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand','ALL');
            $search_val = ifset($postdata,'search','');
            $assign = ifset($postdata,'assign', 0);
            $show_deleted = ifset($postdata,'show_deleted',0);
            $prooforder = ifset($postdata,'prooforder', 0);

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
            if ($prooforder==1) {
                $search['prooforder'] = 1;
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
            $postdata = $this->input->post();

            $show_deleted=ifset($postdata, 'show_deleted',0);
            $offset = ifset($postdata, 'offset',0);
            $limit = ifset($postdata, 'limit',10);
            $order_by = ifset($postdata, 'order_by','');
            $direct = ifset($postdata, 'direction','desc');
            $maxval = ifset($postdata, 'maxval',0);
            $brand = ifset($postdata, 'brand', 'ALL');
            $search_val = ifset($postdata, 'search','');
            $assign = ifset($postdata, 'assign', 1);
            $prooforder = ifset($postdata, 'prooforder', 0);
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
            if ($prooforder == 1) {
                $search['prooforder'] = 1;
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
                    'brand' => $quest['brand'],
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
//                case 'Question':
//                    $this->load->model('questions_model');
//                    $dat = $this->questions_model->get_quest_data($email_id);
//                    $maildat = $dat['data'];
//                    $res = $this->leads_model->create_leadquest($maildat, $leademail_id, $this->USR_ID);
//                    break;
//                case 'Quote':
//                    $this->load->model('quotes_model');
//                    $maildat = $this->quotes_model->get_quote_dat($email_id);
//                    $res = $this->leads_model->create_leadquote($maildat, $leademail_id, $this->USR_ID);
//                    break;
                case 'Proof';
                    $this->load->model('artproof_model');
                    $maildat = $this->artproof_model->get_proof_data($email_id);
                    $res = $this->leads_model->create_leadproof($maildat, $leademail_id, $this->USR_ID);
                    break;
                default:
                    break;
            }

            if ($res['result'] == $this->error_result) {
                $error = $res['msg'];
            } else {
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

    public function show_question_detail() {
        if ($this->isAjax()) {
            $mdata=array();
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
            //  $this->session->set_userdata('leaddata',$leaddat);
            $this->load->model('questions_model');
            $res=$this->questions_model->get_quest_data($quest_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $quest = $res['data'];
                // $mdata['content']=$this->load->view('questions/details_view',$quest,TRUE);
                $mdata['content']=$this->load->view('leads/questions_details_view',$quest,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }



}