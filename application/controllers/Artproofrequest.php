<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Artproofrequest extends MY_Controller
{

    /* Task stages */
    private $NO_ART = '06_noart';
    private $REDRAWN = '05_notredr';
    private $TO_PROOF = '03_notprof';
    private $NEED_APPROVAL = '02_notapprov';
    private $JUST_APPROVED = '01_notplaced';
    private $NO_VECTOR = '04_notvector';
    private $ART_PROOF = 'Art Proof';

    private $NO_ART_REMINDER = 'Need Art Reminder';
    private $NEED_APPROVE_REMINDER = 'Need Approval Reminder';

    protected $restore_artdata_error = 'Connection Lost. Please, recall function';

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
    }

    /* Update common data about Art Work */
    public function art_commonupdate() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $fldname=$postdata['field'];
            $value=$postdata['value'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $artdata[$fldname]=$value;
                usersession($artsession, $artdata);
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* search item  */
    public function art_search_item() {
        $item=$this->input->get('term');
        $this->load->model('artwork_model');
        $get_dat=$this->artwork_model->search_items($item);
        echo json_encode($get_dat);
    }

    /* Change Item ID */
    /* Change Item */
    public function art_itemchange() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $item_id=$postdata['item_id'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                /* Search data */
                $this->load->model('artwork_model');
                $res=$this->artwork_model->search_itemid($artdata, $item_id, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['item_number']=$res['item_number'];
                    $mdata['item_name']=$res['item_name'];
                    $mdata['other_label']='';
                    $mdata['other_show']=0;
                    if ($res['item_name']=='Other' || $res['item_name']=='Multiple' || $res['item_name']=='Custom Shaped Stress Balls') {
                        $mdata['other_show']=1;
                        if ($res['item_name']=='Other') {
                            $mdata['other_label']='Other';
                        } elseif($res['item_name']=='Multiple') {
                            $mdata['other_label']='Multiple';
                        } else {
                            $mdata['other_label']='Custom';
                        }
                    }
                    $mdata['imprints']=0;
                    $imprselect=array();
                    if (count($res['imprints'])>0) {
                        foreach ($artdata['locations'] as $locrow) {
                            $defval=$locrow['art_location'];
                            $improptions=array(
                                'artwork_art_id'=>$locrow['artwork_art_id'],
                                'locs'=>$res['imprints'],
                                'defval'=>$defval,
                            );
                            $imprint_locations=$this->load->view('artpage/imprint_location_view',$improptions,TRUE);
                            $mdata['imprints']=$mdata['imprints']+1;
                            $imprselect[$locrow['artwork_art_id']]=$imprint_locations;
                        }
                    }
                    $mdata['imprselect']=$imprselect;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Add Location */
    public function art_newlocation() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
                $this->func->ajaxResponse($mdata, $error);
            }
            $artwork_id=$postdata['artwork_id'];
            $art_type=$postdata['art_type'];
            if ($art_type=='Logo' || $art_type=='Reference') {
                $mdata['content']=$this->load->view('artpage/upload_artlogo_view',array('artwork_id'=>$artwork_id),TRUE);
            } elseif ($art_type=='Repeat') {
                /* Get Orders which was approveed */
                $mdata['content']=$this->load->view('artpage/select_archiveord_view',array('artwork_id'=>$artwork_id),TRUE);
            } else {
                $data=array(
                    'usertext'=>'',
                    'art_type'=>'Text',
                );
                $res=$this->martwork->add_location($artdata, $data, $artwork_id,$art_type, $artsession);
                if ($res['result']==Art::ERROR_RESULT) {
                    $error=$res['msg'];
                } else {
                    $newloc=$res['newlocation'];

                    $imprints=$this->martwork->get_location_imprint($artdata['item_id']);

                    $improptions=array(
                        'artwork_art_id'=>$newloc['artwork_art_id'],
                        'locs'=>$imprints,
                        'defval'=>'',
                    );
                    $newloc['imprloc_view']=$this->load->view('artpage/imprint_location_view',$improptions,TRUE);

                    /* Build View */
                    $colordat=$this->martwork->colordat_prepare($newloc, $this->imprint_colors);
                    $newloc['optioncolors']=$this->load->view('artpage/artwork_coloroptions_view',$colordat,TRUE);
                    $content=$this->load->view('artpage/artwork_arttext_view',$newloc,TRUE);
                    $mdata['content']=$content;
                }
            }
            $this->func->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Add new ARTWORK location */
    public function art_addlocation() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $data=$this->input->post();
            $artsession=(isset($data['artsession']) ? $data['artsession'] : 'failsession');
            $artwork_id=$data['artwork_id'];
            $art_type=$data['art_type'];
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
            } else {
                $res=$this->martwork->add_location($artdata, $data, $artwork_id,$art_type, $artsession);
                if ($res['result']==Art::ERROR_RESULT) {
                    $error=$res['msg'];
                } else {
                    $newloc=$res['newlocation'];
                    $imprints=$this->martwork->get_location_imprint($artdata['item_id']);
                    $improptions=array(
                        'artwork_art_id'=>$newloc['artwork_art_id'],
                        'locs'=>$imprints,
                        'defval'=>'',
                    );
                    $newloc['imprloc_view']=$this->load->view('artpage/imprint_location_view',$improptions,TRUE);

                    /* Build View */
                    $colordat=$this->martwork->colordat_prepare($newloc, $this->imprint_colors);
                    $newloc['optioncolors']=$this->load->view('artpage/artwork_coloroptions_view',$colordat,TRUE);
                    if ($newloc['art_type']=='Logo' || $newloc['art_type']=='Reference') {
                        $content=$this->load->view('artpage/artwork_artlogo_view',$newloc,TRUE);
                    } elseif ($newloc['art_type']=='Text') {
                        $content=$this->load->view('artpage/artwork_arttext_view',$newloc,TRUE);
                    } else {
                        $content=$this->load->view('artpage/artwork_repeat_view',$newloc,TRUE);
                    }
                    $mdata['content']=$content;
                }
            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* redraw add popup */
    public function art_newlogoupload() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $location_id=$this->input->post('location_id',0);
            $mdata['content']=$this->load->view('artpage/redrawlogo_upload_view',array('location_id'=>$location_id),TRUE);
            $this->func->ajaxResponse($mdata, $error);
        }
    }
    /* Return name of ART logo file */
    public function art_newartupload() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $filename=$this->input->post('filename');
            $docname=$this->input->post('doc_name');
            $data=$this->input->post('data','1');
            $mdata['content']=$this->load->view('artpage/artlogo_upload_view',array('filename'=>$filename, 'doc_name'=>$docname,'data'=>$data),TRUE);
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* show new redrawn inpopup */
    public function art_newredrawn() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $filename=$this->input->post('filename');
            $docname=$this->input->post('doc_name');
            $data=$this->input->post('data','1');
            $mdata['content']=$this->load->view('artpage/vectorfile_view',array('filename'=>$filename, 'doc_name'=>$docname,'data'=>$data),TRUE);
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* Show templates for choice */
    function art_showtemplates() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artwork_id=$postdata['artwork_id'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->get_templates($artdata, $artwork_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $templates=$res['templates'];
                    $templ_options=array();
                    if (count($templates)==0) {
                        $error='Empty list of Templates';
                    } else {
                        $templ_options['templates_list']=$this->load->view('artpage/templates_list_view',array('templates'=>$templates),TRUE);
                        $mdata['content']=$this->load->view('artpage/item_templates_view',$templ_options,TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* return URL of Vector file */
    function art_showtemplate() {
        if ($this->isAjax()) {
            $mdata=array();
            $item_id=$this->input->post('item_id');
            $this->load->model('artwork_model');
            $res=$this->artwork_model->get_template($item_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $mdata['fileurl']=$res['template'];
                $mdata['filename']=$res['item_name'];
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Show list of Available to Assign Orders */
    public function art_assignord() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty List of Orders for Art Connect';
            $this->load->model('orders_model');
            $orderdat=$this->orders_model->get_nonassignorders();
            if (count($orderdat)>0) {
                $mdata['content']=$this->load->view('artpage/order_assign_view',array('orders'=>$orderdat),TRUE);
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Save choice of Assigned Order */
    public function art_newassign() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $order_id=$postdata['order_id'];
            $order_num=$postdata['order_num'];
            $artdata=$this->func->session($artsession);
            if (!empty($artdata)) {
                $artdata['order_id']=$order_id;
                $artdata['order_num']=$order_num;
                usersession($artsession,$artdata);
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Show popup for upload new Proofs */
    public function art_newproofupload() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $artwork_id=$this->input->post('artwork_id');
            // Create Session for uploads
            $data=array(
                'artwork_id'=>$artwork_id,
                'docs'=>array(),
            );
            $uploadsess='proofupload'.uniq_link(15);
            usersession($uploadsess, $data);
            // Popup Options
            $options=array(
                'artwork_id'=>$artwork_id,
                'uplsess'=>$uploadsess,
            );
            $mdata['content']=$this->load->view('artpage/proofs_upload_view',$options,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }
    /* Upload File */
    public function proofattach() {
        $this->load->helper('upload');
        // Restore session
        $path = $this->config->item('upload_path_preload');
        // Check a folder
        createPath($this->config->item('pathpreload'));
        // Allowed Extensions
        $arrayext=array('pdf','PDF');
        if (isset($_GET['qqfile'])) {
            $file = new qqUploadedFileXhr();
            $data=$this->input->get();
        } elseif (isset($_FILES['qqfile'])) {
            $file = new qqUploadedFileForm();
        } elseif (isset($_POST['qqfile'])) {
            $file = new qqUploadedFileXhr();
            $data=$this->input->post();
        } else {
            die('{error: "server-error file not passed"}');
        }

        if ($file) {
            $filename = $file->getName();
            $filesize = $file->getSize();
            if ($filesize == 0)
                die('{error: "server-error file size is zero"}');
            $pathinfo = pathinfo($filename);
            $newfilename=uniq_link(12);
            $ext = strtolower($pathinfo['extension']);
            if (!in_array($ext, $arrayext )) {
                $these = implode(', ', $arrayext);
                echo (json_encode(array('success' => false, 'error' => 'File has an invalid extension, it should be one of '. $these . '.')));
                exit();
            } else {
                $file->save($path . $newfilename . '.' . $ext);
                // Add new file to data
                $this->load->model('artproof_model');
                $this->artproof_model->add_proofdoc_log($data['artwork_id'], $this->USR_ID, $path . $newfilename . '.' . $ext, $filename, 'Upload');
                // Build new content
                echo (json_encode(array('success' => true, 'filename'=> $newfilename. '.' . $ext, 'srcname'=>$filename)));
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }
        die('{error: "server-error query params not passed"}');
    }

    public function art_showuplproofdocs() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=$this->func->session($uploadsession);
            if (empty($data)) {
                $error='Session Expired. Please, recall form';
            } else {
                $docs=$data['docs'];
                $content='';
                foreach ($docs as $row) {
                    $voptions=array(
                        'filename'=>$row['filename'],
                        'doc_name'=>$row['filename'],
                        'data'=>$row['id'],
                    );
                    $content.=$this->load->view('artpage/vectorfile_view',$voptions,TRUE);
                }
                $mdata['content']=$content;
                $mdata['numrec']=count($docs);
            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    public function art_deluplproofdocs() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=$this->func->session($uploadsession);
            if (empty($data)) {
                $error='Session Expired. Please, recall form';
            } else {
                $docs=$data['docs'];
                $id=$this->input->post('id');
                $newdocs=array();
                $content='';
                foreach ($docs as $row) {
                    if ($row['id']!=$id) {
                        $newdocs[]=$row;
                        $voptions=array(
                            'filename'=>$row['filename'],
                            'doc_name'=>$row['filename'],
                            'data'=>$row['id'],
                        );
                        $content.=$this->load->view('artpage/vectorfile_view',$voptions,TRUE);
                    } else {
                        // Find data
                        $this->func->proofdoclog($data['artwork_id'], $this->USR_ID, $row['filesource'], $row['filename'], 'Cancel Upload');
                    }
                }
                $data['docs']=$newdocs;
                $mdata['content']=$content;
                $mdata['numrec']=  count($newdocs);
                $this->func->session($uploadsession, $data);
            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }


    /* save list of new proofs */
    public function art_saveproofload() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            // Restore from session
            $postdata=$this->input->post();
            // $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (empty(!$artdata)) {
                $this->load->model('artwork_model');
                $result=$this->artwork_model->add_prooffile($artdata, $postdata['proofdoc'], $postdata['sourcename'], $this->USR_ID, $artsession);
                $error=$result['msg'];
                if ($result['result']==$this->success_result) {
                    $error='';
                    /* All OK - show data */
                    $proofdat=$result['proofs'];
                    $proofs_view=$this->load->view('artpage/prooflist_edit_view',array('proofs'=>$proofdat,'artwork_id'=>$artdata['artwork_id']),TRUE);
                    $mdata['content']=$proofs_view;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Mark proof as assign */
    public function art_aproveproof() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');

            $artwork_id=$postdata['artwork_id'];
            $proof_id=$postdata['proof_id'];
            $artdata=$this->func->session($artsession);

            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
            } else {
                $res=$this->martwork->approve_proof($artwork_id, $proof_id, $artdata, $this->USR_ID, $artsession);
                if ($res['result']==Art::ERROR_RESULT) {
                    $error=$res['msg'];
                } else {
                    $proofdat=$res['proofs'];
                    $proofs_view=$this->load->view('artpage/prooflist_edit_view',array('proofs'=>$proofdat,'artwork_id'=>$artdata['artwork_id']),TRUE);
                    $approve_options=array(
                        'proofs'=>$proofdat,
                        'artwork_id'=>$artwork_id,
                    );
                    $approvview=$this->load->view('artpage/approved_view',$approve_options, TRUE);
                    $mdata['proofcontent']=$proofs_view;
                    $mdata['approvecontent']=$approvview;
                }
            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* Prepare Email for Approve */
    public function art_approvemail() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            // $template=$this->input->post('email_template');
            $template=Art::ART_PROOF;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
            } else {
                $userdat=$this->muser->get_user_data($this->USR_ID);
                $user_name=$userdat['user_name'];
                if ($template) {
                    $mail_template=$this->memail->get_emailtemplate_byname($template);
                    if ($artdata['order_id']) {
                        $msgdat="BT".$artdata['order_num'];
                        $doc_type='Order';
                    } else {
                        $msgdat="PR".$artdata['proof_num'];
                        $doc_type='Proof Request';
                    }
                    if ($artdata['item_id']=='-1') {
                        if ($artdata['other_item']) {
                            $itemname=$artdata['other_item'];
                        } else {
                            $itemname='';
                        }
                    } else {
                        $itemname=$artdata['item_name'];
                    }
                    $message=$mail_template['email_template_body'];
                    $message=str_replace('<<customer_name>>', $artdata['customer_name'], $message);
                    // $message=str_replace('<<item_name>>', $artdata['item_name'], $message);
                    $message=str_replace('<<item_name>>', $itemname, $message);
                    $message=str_replace('<<user_name>>', $user_name, $message);
                    $message=str_replace('<<document_type>>',$doc_type,$message);
                    $subj=str_replace('<<order_number>>', $msgdat, $mail_template['email_template_subject']);
                    $subj=str_replace('<<document_type>>',$doc_type,$subj);
                    // $subj=str_replace('<<item_name>>', $artdata['item_name'], $subj);
                    $subj=str_replace('<<item_name>>', $itemname, $subj);
                } else {
                    $subj='BLUETRACK Art proof ';
                    $message='';
                }
                $artemail=$this->config->item('art_dept_email');

                $options=array(
                    'artwork_id'=>$artdata['artwork_id'],
                    'from'=>$artemail,
                    'tomail'=>$artdata['customer_email'],
                    'subject'=>$subj,
                    'message'=>$message,
                );
                $mdata['content']=$this->load->view('artpage/approve_email_view',$options,TRUE);

            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* Send proofs to customer */
    public function art_sendproofs() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $data=$this->input->post();
            $artsession=(isset($data['artsession']) ? $data['artsession'] : 'failsession');
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
            } else {
                $res=$this->martwork->send_proof_approve($data, $artdata, $this->USR_ID, $artsession);
                if ($res['result']==Art::SUCCESS_RESULT) {
                    /* Build new content for proofs */
                    $proofdat=$res['proofs'];
                    $proofs_view=$this->load->view('artpage/prooflist_edit_view',array('proofs'=>$proofdat,'artwork_id'=>$artdata['artwork_id']),TRUE);
                    $mdata['content']=$proofs_view;
                } else {
                    $error=$res['msg'];
                }

            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* Show approved file */
    function art_approvedshow() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $proof_id=$postdata['proof_id'];
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
            } else {
                $res=$this->martwork->get_approved($artdata, $proof_id);
                if ($res['result']==Art::ERROR_RESULT) {
                    $error=$out['msg'];
                } else {
                    $mdata['url']=$res['url'];
                    $mdata['filename']=$res['filename'];
                }
            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    public function art_approvedrevert() {
        if ($this->func->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $proof_id=$postdata['proof_id'];
            $artwork_id=$postdata['artwork_id'];
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
                $error=$this->restore_artdata_error;
            } else {
                $res=$this->martwork->art_revert_approved($artdata, $artwork_id, $proof_id, $this->USR_ID, $artsession);
                if ($res['result']==Art::ERROR_RESULT) {
                    $error=$res['msg'];
                } else {
                    $proofdat=$res['proofs'];
                    $proofs_view=$this->load->view('artpage/prooflist_edit_view',array('proofs'=>$proofdat,'artwork_id'=>$artwork_id),TRUE);
                    $mdata['proof_content']=$proofs_view;
                    $mdata['content']=$this->load->view('artpage/approved_view',array('proofs'=>$res['proofs'], 'artwork_id'=>$artdata['artwork_id']),TRUE);
                }
            }

            $this->func->ajaxResponse($mdata, $error);
        }
    }

    /* Delete Approved Proofs */
    public function art_approveddelete() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $proof_id=$postdata['proof_id'];
            $artwork_id=$postdata['artwork_id'];
            $artdata=usersession($artsession);
            if (empty(!$artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->art_delproof($artdata, $artwork_id, $proof_id, $this->USR_ID, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $proofdat=$res['proofs'];
                    $proofs_view=$this->load->view('artpage/prooflist_edit_view',array('proofs'=>$proofdat,'artwork_id'=>$artwork_id),TRUE);
                    $mdata['proof_content']=$proofs_view;
                    $mdata['content']=$this->load->view('artpage/approved_view',array('proofs'=>$res['proofs'], 'artwork_id'=>$artdata['artwork_id']),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Save ART Request
    public function artwork_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $data=$this->input->post();
            /* Try to save */
            $artsession=(isset($data['artsession']) ? $data['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            $error=$this->restore_artdata_error;
            if (!empty($artdata)) {
                $callpage = $artdata['callpage'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->save_artdata($data, $artdata, $this->USR_ID, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['callpage']=$callpage;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }

    }

}
