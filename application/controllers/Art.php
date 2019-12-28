<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Art extends MY_Controller {

    private $pagelink = '/art';
    protected $artorderperpage=250;

    private $NO_ART_REMINDER='Need Art Reminder';
    private $NEED_APPROVE_REMINDER='Need Approval Reminder';

    public function __construct()
    {
        parent::__construct();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink);
        if ($pagedat['result']==$this->error_result) {
            show_404();
        }
        $page = $pagedat['menuitem'];
        $permdat = $this->menuitems_model->get_menuitem_userpermisiion($this->USR_ID, $page['menu_item_id']);
        if ($permdat['result']==$this->success_result && $permdat['permission']>0) {
        } else {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url' => '/'), 'Your have no permission to this page');
            } else {
                redirect('/');
            }
        }
    }

    public function index() {
        $head=[];
        $head['title']='ART';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#taskview') {
                // Taks View
                $head['styles'][]=array('style'=>'/css/art/taskview.css');
                $head['scripts'][]=array('src'=>'/js/art/taskview.js');
                $content_options['taskview'] = $this->_prepare_task_view();
            } elseif ($row['item_link']=='#orderlist') {
                $head['styles'][]=array('style'=>'/css/art/orderslist.css');
                $head['scripts'][]=array('src'=>'/js/art/orderslist.js');
                $content_options['orderlist'] = $this->_prepare_orderlist_view();
            } elseif ($row['item_link']=='#requestlist') {
                $head['styles'][]=array('style'=>'/css/art/requestlist.css');
                $head['scripts'][]=array('src'=>'/js/art/requestlist.js');
                $content_options['requestlist'] = $this->_prepare_requestlist_view();
            }
        }
        $content_options['menu']=$menu;
        $content_view = $this->load->view('artpage/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][]=array('src'=>'/js/art/page.js');
        $head['styles'][] = array('style'=> '/css/art/artpage.css');
        // Utils
        // $head['scripts'][]=array('src'=>'/js/jquery.bt.js');
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // Searchable
        // $head['scripts'][]=array('src'=>'/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
        // Artwork popup
        $head['scripts'][]=array('src'=>'/js/artwork/artpopup.js?r='.$this->config->item('js_version'));
        $head['styles'][]=array('style'=>'/css/artwork/artpopup.css?r='.$this->config->item('css_version'));

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function tasks_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';

            $taskview=$this->input->post('taskview','orders');
            $inclreq=$this->input->post('showreq',0);
            $nonart_sort=$this->input->post('nonart_sort','time');
            $nonart_direc=$this->input->post('nonart_direc','desc');
            $redraw_sort=$this->input->post('redraw_sort','time');
            $redraw_direc=$this->input->post('redraw_direc','desc');
            $proof_sort=$this->input->post("proof_sort",'time');
            $proof_direc=$this->input->post('proof_direc','desc');
            $needapr_sort=$this->input->post('needapr_sort','time');
            $needapr_direc=$this->input->post('needapr_direc','desc');
            $aproved_sort=$this->input->post('approved_sort','time');
            $aproved_direc=$this->input->post('aproved_direc','desc');
            $aproved_viewall=$this->input->post('aproved_viewall');
            /* Get data */
            $this->load->model('artproof_model');

            $data_not_art=$this->artproof_model->get_tasks_stage('noart', $taskview, $inclreq, $nonart_sort, $nonart_direc);
            if (count($data_not_art)==0) {
                $mdata['nonart']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['nonart']=$this->load->view('tasklist/task_data_view',array('data'=>$data_not_art,'note'=>1),TRUE);
            }
            $data_redraw=$this->artproof_model->get_tasks_stage('redrawn', $taskview, $inclreq, $redraw_sort, $redraw_direc);
            if (count($data_redraw)==0) {
                $mdata['redrawn']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['redrawn']=$this->load->view('tasklist/task_data_view',array('data'=>$data_redraw,'note'=>0),TRUE);
            }
            $data_proof=$this->artproof_model->get_tasks_stage('need_proof', $taskview, $inclreq, $proof_sort, $proof_direc);
            if (count($data_proof)==0) {
                $mdata['toproof']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['toproof']=$this->load->view('tasklist/task_data_view',array('data'=>$data_proof,'note'=>0),TRUE);
            }
            $data_needapr=$this->artproof_model->get_tasks_stage('need_approve', $taskview, $inclreq, $needapr_sort, $needapr_direc);
            if (count($data_needapr)==0) {
                $mdata['needapr']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['needapr']=$this->load->view('tasklist/task_data_view',array('data'=>$data_needapr,'note'=>1),TRUE);
            }
            $data_aproved=$this->artproof_model->get_tasks_stage('just_approved', $taskview, $inclreq, $aproved_sort, $aproved_direc, $aproved_viewall);
            if (count($data_aproved)==0) {
                $mdata['aproved']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['aproved']=$this->load->view('tasklist/task_data_view',array('data'=>$data_aproved,'note'=>0),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Change SEPARATE column */
    public function tasks_stage() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $taskview=$this->input->post('taskview');
            $inclreq=$this->input->post('showreq',0);
            $stage=$this->input->post('stage');
            $task_sort=$this->input->post('task_sort','time');
            $task_direc=$this->input->post('task_direc','desc');
            $noteval=0;
            $aproved_viewall=0;
            if ($stage=='noart' || $stage=='need_approve') {
                $noteval=1;
            }
            $aproved_viewall=0;
            if ($stage=='just_approved') {
                $aproved_viewall=$this->input->post('aproved_viewall');
            }
            $this->load->model('artproof_model');
            $data_task=$this->artproof_model->get_tasks_stage($stage, $taskview, $inclreq, $task_sort, $task_direc, $aproved_viewall);
            if (count($data_task)==0) {
                $mdata['content']=$this->load->view('tasklist/task_dataempty_view',array(),TRUE);
            } else {
                $mdata['content']=$this->load->view('tasklist/task_data_view',array('data'=>$data_task,'note'=>$noteval),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }


    public function task_remindmail() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $task_id=$this->input->post('task_id');
            $template='';
            if (!$task_id) {
                $error='Unknown Task';
            } else {
                $this->load->model('artwork_model');
                $this->load->model('email_model');
                if (substr($task_id,0,2)=='pr') {
                    $this->load->model('artproof_model');
                    $email_id=substr($task_id, 2);
                    $artdata=$this->artwork_model->get_artwork_proof($email_id, $this->USR_ID);

                    $data=$this->artproof_model->get_proof_data($email_id);
                    if (!isset($artdata['artwork_id'])) {
                        $error='Undefined Proof Request';
                        $this->ajaxResponse($mdata, $error);
                    } else {
                        if ($data['email_proofed']==1) {
                            /* Approve art reminder */
                            $templdat=$this->email_model->get_emailtemplate_byname($this->NEED_APPROVE_REMINDER);
                        } else {
                            $templdat=$this->email_model->get_emailtemplate_byname($this->NO_ART_REMINDER);
                        }

                        if (isset($templdat['email_template_id'])) {
                            $template=$templdat['email_template_id'];
                        }
                        $artdata['order_date']=strtotime($data['email_date']);
                        $artdata['order_number']="PR".$artdata['proof_num'];
                        $artdata['document_type']='Proof Request';
                    }
                } else {
                    $this->load->model('orders_model');
                    $order_id=substr($task_id, 3);
                    $artdata=$this->artwork_model->get_artwork_order($order_id, $this->USR_ID);
                    $data=$this->orders_model->get_order_detail($order_id);
                    if ($data['order_system']=='new') {
                        // Get Order Contact with ART
                        $this->load->model('leadorder_model');
                        $contacts=$this->leadorder_model->get_order_contacts($order_id);
                        $artmail='';
                        foreach ($contacts as $crow) {
                            if ($crow['contact_art']==1 && !empty($crow['contact_emal'])) {
                                $artmail.=$crow['contact_emal'].',';
                            }
                        }
                        $artdata['customer_email']=substr($artmail,0,-1);
                    }
                    $artdata['order_date']=$data['order_date'];
                    $artdata['order_number']="BT".$data['order_num'];
                    $artdata['document_type']='Proof Request';
                    if ($data['order_proofed']==1) {
                        /* Approve art reminder */
                        $templdat=$this->email_model->get_emailtemplate_byname($this->NEED_APPROVE_REMINDER);
                    } else {
                        $templdat=$this->email_model->get_emailtemplate_byname($this->NO_ART_REMINDER);
                    }

                    if (isset($templdat['email_template_id'])) {
                        $template=$templdat['email_template_id'];
                    }
                }
                // Get Artdata LINKs message
                $linkmsg=$this->artwork_model->get_needaprovelnk($artdata['artwork_id']);

                if (!$template) {
                    $msgdat='';
                    $message='';
                } else {
                    $userdat=$this->user_model->get_user_data($this->USR_ID);
                    $user_name=$userdat['user_name'];
                    $mail_template=$this->email_model->get_email_template($template);
                    $message=$mail_template['email_template_body'];
                    $message=str_replace('<<customer_name>>', $artdata['customer'], $message);
                    $message=str_replace('<<user_name>>', $user_name, $message);
                    $message=str_replace('<<order_date>>', date('F j Y',$artdata['order_date']), $message);
                    $message=str_replace('<<order_number>>', $artdata['order_number'], $message);
                    $message=str_replace('<<item_name>>', $artdata['item_name'], $message);
                    $message=str_replace('<<document_type>>',$artdata['document_type'],$message);
                    $message=str_replace('<<links>>', $linkmsg, $message);
                    $msgdat=str_replace('<<order_number>>', $artdata['order_number'], $mail_template['email_template_subject']);
                    $msgdat=str_replace('<<item_name>>', $artdata['item_name'], $msgdat);
                    $msgdat=str_replace('<<document_type>>',$artdata['document_type'],$msgdat);
                }
                $artemail=$this->config->item('art_dept_email');
                $options=array(
                    'artwork_id'=>$artdata['artwork_id'],
                    'from'=>$artemail,
                    'tomail'=>$artdata['customer_email'],
                    'subject'=>$msgdat,
                    'message'=>$message,
                );
                $mdata['content']=$this->load->view('artpage/approve_email_view',$options,TRUE);

            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Send reminder mail */
    public function task_sendreminder() {
        if ($this->isAjax()) {
            $mdata=array();
            $path_sh=$this->config->item('artwork_proofs_relative');
            $path_fl=$this->config->item('artwork_proofs');
            $postdata=$this->input->post();
            $this->load->model('artwork_model');
            if (substr($postdata['task_id'],0,2)=='pr') {
                $this->load->model('artproof_model');
                $email_id=substr($postdata['task_id'], 2);
                $data=$this->artproof_model->get_proof_data($email_id);
                if ($data['email_proofed']==1) {
                    $proofs=$this->artwork_model->get_artproofs($postdata['artwork_id']);
                    $attach=array();
                    foreach ($proofs as $row) {
                        $file=  str_replace($path_sh, $path_fl, $row['src']);
                        array_push($attach,$file);
                    }
                    $postdata['history_msg']='Art Approval - reminder email sent.<br/>';
                } else {
                    $attach=array();
                    $postdata['history_msg']='Need Art - reminder email sent.<br/>';
                }
            } else {
                $this->load->model('orders_model');
                $order_id=substr($postdata['task_id'], 3);
                $data=$this->orders_model->get_order_detail($order_id);
                if ($data['order_proofed']==1) {
                    $proofs=$this->artwork_model->get_artproofs($postdata['artwork_id']);
                    $attach=array();
                    foreach ($proofs as $row) {
                        $file=  str_replace($path_sh, $path_fl, $row['src']);
                        array_push($attach,$file);
                    }
                    $postdata['history_msg']='Art proof sent ';
                } else {
                    $attach=array();
                    $postdata['history_msg']='Need Art - reminder email sent.<br/>';
                }
            }

            $res=$this->artwork_model->send_reminder($postdata, $attach, $this->USR_ID);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function tasksearch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            if ($postdata['tasktype']=='O') {
                // Order search
                $limit=250;
                $order_by='order_num';
                $direct = 'desc';
                $search=$postdata['tasksearch'];
                $offset=0;
                /* Get data about orders */
                $filtr=array(
                    'search'=>$search,
                );
                $filtr['hideart']=1;
                // $orders=$this->morder->get_orders($filtr,$order_by,$direct,$limit,$offset);
                $this->load->model('orders_model');
                $orders=$this->orders_model->get_general_orders($filtr,$order_by,$direct,$limit,$offset, $this->USR_ID);

                if (count($orders)==0) {
                    $content=$this->load->view('artorder/order_emptydat_view',array(),TRUE);
                } else {
                    $content=$this->load->view('artorder/order_tabledat_view',array('orders'=>$orders),TRUE);
                }
                $mdata['content']=$this->load->view('tasklist/order_head_view',array('content'=>$content),TRUE);
                $mdata['type']='order';
            } else {
                // Requests
                $offset=0;
                $limit=250;
                $order_by='email_date';
                $direct = 'desc';
                // $maxval=$this->input->post('maxval');
                $search=array();
                $search['search']=$postdata['tasksearch'];
                $email_dat=$this->mproofs->get_artproofs($search,$order_by,$direct,$limit,$offset,$limit);
                if (count($email_dat)==0) {
                    $content = $this->load->view('artpage/proofs_emptytabledat_view',array(), TRUE);
                } else {
                    $data=array('email_dat'=>$email_dat);
                    $content = $this->load->view('artpage/proofs_tabledat_view',$data, TRUE);
                }
                $mdata['content']=$this->load->view('tasklist/proofrequest_head_view',array('content'=>$content),TRUE);
                $mdata['type']='proofrequest';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // restore_task
    public function restore_task() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $mdata['content']=$this->load->view('tasklist/restore_task_view',array(),TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* LIST view of Orders List */
    public function order_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            $searchval=$this->input->post('search','');
            $add_filtr=$this->input->post('add_filtr');
            $filter=$this->input->post('filter');
            $search=array();
            if ($searchval) {
                $search['search']=$searchval;
            }
            if ($filter!='') {
                $search['artfiltr']=$filter;
            }
            if ($add_filtr!='') {
                $search['artadd_filtr']=$add_filtr;
            }
            $offset=$offset*$limit;
            /* Fetch data about prices */
            $this->load->model('orders_model');
            $ordersdat=$this->orders_model->get_general_orders($search,$order_by,$direct,$limit,$offset, $this->USR_ID);
            if (count($ordersdat)==0) {
                $content=$this->load->view('artorder/order_emptydat_view',array(),TRUE);
            } else {
                $orders = [];
                foreach ($ordersdat as $row) {
                    $row['lastmsg']='/art/order_lastmessage?d='.$row['order_id'];
                    $orders[]=$row;
                }
                $data=array(
                    'orders'=>$orders,
                );
                $content = $this->load->view('artorder/order_tabledat_view',$data, TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Calculate qty of Orders */
    function search_orders() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $search=$this->input->post('search');
            $filter=$this->input->post('filter');
            $add_filtr=$this->input->post('add_filtr');

            $options=array();

            $options['search']=$search;
            $options['artfiltr']=$filter;
            $options['artadd_filtr']=$add_filtr;
            /* count number of orders */
            $this->load->model('orders_model');
            $mdata['totals']=$this->orders_model->get_count_orders($options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function order_lastmessage() {
        $order_id=$this->input->get('d');
        $this->load->model('orders_model');
        $out_msg=$this->orders_model->get_lastupdate($order_id,'order');
        echo $out_msg;
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
            $error='';
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
            $quest=$this->questions_model->get_quest_data($quest_id);

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
            $this->ajaxResponse($mdata, $error);
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
                    $row['lastmsg']='/art/proof_lastmessage?d='.$row['email_id'];
                    $emails[]=$row;
                }
                $data=array('email_dat'=>$emails);
                $content = $this->load->view('artrequest/proofs_tabledat_view',$data, TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata,$error);
        }
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
                    $maildat = $this->questions_model->get_quest_data($email_id);
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
//                $mdata['total_proof'] = $this->mproofs->get_count_proofs(array('assign' => 1));
//                $mdata['total_quote'] = $this->mquotes->get_count_quotes(array('assign' => 1));
//                $mdata['total_quest'] = $this->mquests->get_count_questions(array('assign' => 1));
//                $mdata['sumquote'] = $this->mquotes->get_todays();
//                $mdata['sumproofs'] = $this->mproofs->get_todays();
//                $mdata['sumquest'] = $this->mquests->get_todays();
                $mdata['leadid'] = $res['result'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
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
                $data=$this->questions_model->get_quest_data($quest['mail_id']);
                /* Recalculate Totals New  */
                $mdata['type']=$data['email_type'];
//                $mdata['total_proof']=$this->mproofs->get_count_proofs(array('assign'=>1));
//                $mdata['total_quote']=$this->mquotes->get_count_quotes(array('assign'=>1));
//                $mdata['total_quest']=$this->mquests->get_count_questions(array('assign'=>1));
//                $mdata['sumquote']=$this->mquotes->get_todays();
//                $mdata['sumproofs']=$this->mproofs->get_todays();
//                $mdata['sumquest']=$this->mquests->get_todays();
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
    // Last Art message
    public function proof_lastmessage() {
        $order_id=$this->input->get('d');
        $this->load->model('artproof_model');
        $out_msg=$this->artproof_model->get_lastupdate($order_id,'artproofs');
        echo $out_msg;
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




    private function _prepare_task_view() {
        $datf=array();
        $datf['sort_need_art']='time';
        $datf['direc_needart']='desc';
        $datf['sort_redraw']='time';
        $datf['direc_redraw']='desc';
        $datf['sort_proof']='time';
        $datf['direc_proof']='desc';
        $datf['sort_needapr']='time';
        $datf['direc_needapr']='desc';
        $datf['sort_aproved']='time';
        $datf['direc_aproved']='asc';
        $content=$this->load->view('tasklist/page_view',$datf,TRUE);
        return $content;
    }

    private function _prepare_orderlist_view() {
        $datqs=array();
        $datqs['perpage']=$this->artorderperpage;
        $search=array('hideart'=>0);
        $this->load->model('orders_model');
        $datqs['total_rec']=$this->orders_model->get_count_orders($search);
        $datqs['order_by']='order_num';
        $datqs['direction']='desc';
        $datqs['cur_page']=0;
        $datqs['assign']='';
        $datqs['hideart']=0;

/*        $options=array(
            'hideart'=>1,
        );
        $this->load->model('orders_model');
        $totals=$this->orders_model->get_count_orders($options);

        $options_view=array(
            'perpage'=> $this->artorderperpage,
            'order'=>'order_num',
            'direc'=>'desc',
            'total'=>$totals,
            'curpage'=>0,
        );*/
        $content=$this->load->view('artorder/page_view',$datqs,TRUE);
        return $content;
    }

    private function _prepare_requestlist_view() {
        $datqs=array();
        $datqs['perpage']=$this->artorderperpage;
        $search=array('assign'=>'','hideart'=>0);
        $this->load->model('artproof_model');
        $datqs['total_rec']=$this->artproof_model->get_count_proofs($search);
        $datqs['order_by']='email_date';
        $datqs['direction']='desc';
        $datqs['cur_page']=0;
        $datqs['assign']='';
        $datqs['hideart']=0;
        $content=$this->load->view('artrequest/page_view',$datqs,TRUE);
        return $content;
    }
}