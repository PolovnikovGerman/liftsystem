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
    public function art_newlocation()
    {
        if ($this->isAjax()) {
            $mdata = array();
            $error = $this->restore_artdata_error;
            $postdata = $this->input->post();
            $artsession = (isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata = usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $artwork_id = $postdata['artwork_id'];
                $art_type = $postdata['art_type'];
                if ($art_type == 'Logo' || $art_type == 'Reference') {
                    $mdata['content'] = $this->load->view('artpage/upload_artlogo_view', array('artwork_id' => $artwork_id), TRUE);
                    $error='';
                } elseif ($art_type == 'Repeat') {
                    /* Get Orders which was approveed */
                    $mdata['content'] = $this->load->view('artpage/select_archiveord_view', array('artwork_id' => $artwork_id), TRUE);
                    $error='';
                } else {
                    $data = array('usertext' => '', 'art_type' => 'Text',);
                    $res = $this->artwork_model->add_location($artdata, $data, $artwork_id, $art_type, $artsession);
                    $error = $res['msg'];
                    if ($res['result'] == $this->success_result) {
                        $error='';
                        $newloc = $res['newlocation'];
                        $imprints = $this->artwork_model->get_location_imprint($artdata['item_id']);

                        $improptions = array('artwork_art_id' => $newloc['artwork_art_id'], 'locs' => $imprints, 'defval' => '',);
                        $newloc['imprloc_view'] = $this->load->view('artpage/imprint_location_view', $improptions, TRUE);

                        /* Build View */
                        $imprint_colors = $this->config->item('imprint_colors');
                        $colordat = $this->artwork_model->colordat_prepare($newloc, $imprint_colors);
                        $newloc['optioncolors'] = $this->load->view('artpage/artwork_coloroptions_view', $colordat, TRUE);
                        $content = $this->load->view('artpage/artwork_arttext_view', $newloc, TRUE);
                        $mdata['content'] = $content;
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function art_redrawattach() {
        $this->load->helper('upload');
        $filename = '';
        $filesize = 0;
        $file = null;
        $path = $this->config->item('upload_path_preload');

        $arrayext=array('jpg','gif', 'jpeg', 'pdf', 'ai', 'eps','doc', 'docx', 'png');
        if (isset($_GET['qqfile'])) {
            $file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $file = new qqUploadedFileForm();
        } elseif (isset($_POST['qqfile'])) {
            $file = new qqUploadedFileXhr();
        } else {
            die('{error: "server-error file not passed"}');
        }

        if ($file) {
            $filename = $file->getName();
            $filesize = $file->getSize();

            if ($filesize == 0)
                die('{error: "server-error file size is zero"}');

            $pathinfo = pathinfo($file->getName());

            $filename = uniq_link(12);
            $ext = strtolower($pathinfo['extension']);
            if (!in_array($ext, $arrayext )) {
                $these = implode(', ', $arrayext);
                echo (json_encode(array('success' => false, 'error' => 'File has an invalid extension, it should be one of '. $these . '.')));
                exit();
            } else {
                $file->save($path . $filename . '.' . $ext);
                echo (json_encode(array('success' => true, 'filename' => $path.$filename . '.' . $ext, 'filesize' => $filesize,'source'=>$file->getName())));
                exit();
            }
        } else {
            echo (json_encode(array('success' => false,'path'=>$path)));
            exit();
        }

        die('{error: "server-error query params not passed"}');
    }

    /* Add new ARTWORK location */
    public function art_addlocation() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $data=$this->input->post();
            $artsession=(isset($data['artsession']) ? $data['artsession'] : 'failsession');
            $artwork_id=$data['artwork_id'];
            $art_type=$data['art_type'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->add_location($artdata, $data, $artwork_id,$art_type, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $newloc=$res['newlocation'];
                    $imprints=$this->artwork_model->get_location_imprint($artdata['item_id']);
                    $improptions=array(
                        'artwork_art_id'=>$newloc['artwork_art_id'],
                        'locs'=>$imprints,
                        'defval'=>'',
                    );
                    $newloc['imprloc_view']=$this->load->view('artpage/imprint_location_view',$improptions,TRUE);

                    /* Build View */
                    $imprint_colors = $this->config->item('imprint_colors');
                    $colordat=$this->artwork_model->colordat_prepare($newloc, $imprint_colors);
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
            $this->ajaxResponse($mdata, $error);
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
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $filename=$this->input->post('filename');
            $docname=$this->input->post('doc_name');
            $data=$this->input->post('data','1');
            $mdata['content']=$this->load->view('artpage/artlogo_upload_view',array('filename'=>$filename, 'doc_name'=>$docname,'data'=>$data),TRUE);
            $this->ajaxResponse($mdata, $error);
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
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');

            $artwork_id=$postdata['artwork_id'];
            $proof_id=$postdata['proof_id'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->approve_proof($artwork_id, $proof_id, $artdata, $this->USR_ID, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
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
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Prepare Email for Approve */
    public function art_approvemail() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $template=$this->ART_PROOF;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('user_model');
                $this->load->model('email_model');
                $userdat=$this->user_model->get_user_data($this->USR_ID);
                $user_name=$userdat['user_name'];
                $error='';
                if ($template) {
                    $mail_template=$this->email_model->get_emailtemplate_byname($template);
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
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Send proofs to customer */
    public function art_sendproofs() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $data=$this->input->post();
            $artsession=(isset($data['artsession']) ? $data['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->send_proof_approve($data, $artdata, $this->USR_ID, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    /* Build new content for proofs */
                    $error='';
                    $proofdat=$res['proofs'];
                    $proofs_view=$this->load->view('artpage/prooflist_edit_view',array('proofs'=>$proofdat,'artwork_id'=>$artdata['artwork_id']),TRUE);
                    $mdata['content']=$proofs_view;
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Show approved file */
    public function art_approvedshow() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $proof_id=$postdata['proof_id'];
            $artdata=$this->func->session($artsession);
            if (empty($artdata)) {
            } else {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->get_approved($artdata, $proof_id);
                $error=$res['msg'];
                if ($res['result']==Art::ERROR_RESULT) {
                } else {
                    $error='';
                    $mdata['url']=$res['url'];
                    $mdata['filename']=$res['filename'];
                }
            }
            $this->func->ajaxResponse($mdata, $error);
        }
    }

    public function art_approvedrevert() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $proof_id=$postdata['proof_id'];
            $artwork_id=$postdata['artwork_id'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->art_revert_approved($artdata, $artwork_id, $proof_id, $this->USR_ID, $artsession);
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

    public function art_changeusrtxt() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $art_id=$postdata['art_id'];
                $artwork_id=$artdata['artwork_id'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->get_artdata_locusrtxt($artdata, $art_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content']=$this->load->view('artpage/newarttext_view',array('artwork_id'=>$artwork_id,'usrtxt'=>$res['usrtxt'],'title'=>'Enter Customer Text'),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function art_saveusertext() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $art_id=$postdata['art_id'];
                $customer_text=$postdata['customer_text'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->save_artdata_locusrtxt($artdata, $art_id, $customer_text, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content']=$res['content'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Open File content */
    public function art_openimg() {
        $url = $this->input->post('url');
        $filename = $this->input->post('file');
        /* Get extension */
        $this->func->openfile($url, $filename);
    }

    /* Delete location */
    public function art_dellocation() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $art_id=$postdata['art_id'];
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->delete_location($artdata, $art_id, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Popup with FONTS for select */
    public function art_fontselect() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('artwork_model');
            $fonts_popular=$this->artwork_model->get_fonts(array('is_popular'=>1));
            $fonts_other=$this->artwork_model->get_fonts(array('is_popular'=>0));
            if (count($fonts_popular)+count($fonts_other)==0) {
                $error='Empty list of fonts';
            } else {
                $mdata['content']=$this->load->view('artpage/font_select_view',array('fonts_popular'=>$fonts_popular,'fonts_other'=>$fonts_other),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function art_locationupdate() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $locitem=$postdata['field'];
                $locvalue=$postdata['value'];
                $art_id=$postdata['art_id'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->artlocationdata_update($artdata, $locitem, $locvalue, $art_id, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function art_showfile() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $art_id=$postdata['art_id'];
            $type=$postdata['type'];
            $artworkdata=usersession($artsession);
            if (!empty($artworkdata)) {
                $this->load->model('artwork_model');
                $res=$this->artwork_model->logofilesrc($artworkdata, $art_id, $type);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $mdata['url']=$res['url'];
                    $mdata['filename']=$res['filename'];
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function viewartsource() {
        $art_id=$this->input->get('id');
        $artsession=$this->input->get('artsession');
        if (empty($artsession)) {
            $artdata=usersession('artwork');
        } else {
            $artdata=usersession($artsession);
        }

        if (empty($artdata)) {
            echo $this->restore_artdata_error;
            die();
        }
        $locations=$artdata['locations'];
        $found=0;
        foreach ($locations as $row) {
            if ($row['artwork_art_id']==$art_id) {
                $found=1;
                $filename=$row['logo_srcpath'];
                break;
            }
        }
        if ($found==0) {
            echo 'Not Found';
            die();
        }
        if (empty($filename)) {
            echo 'File Not Found';
            die();
        }
        if ($art_id<0) {
            // New Location
            $filesource=  str_replace($this->config->item('pathpreload'), $this->config->item('upload_path_preload') , $filename);
        } else {
            $filesource=  str_replace($this->config->item('artwork_logo_relative'), $this->config->item('artwork_logo') , $filename);
        }
        if (!file_exists($filesource)) {
            echo 'Source File '.$filesource.' Not Found ';
            die();
        }
        $viewopt=array(
            'source'=>$filename,
        );
        list($width, $height, $type, $attr) = getimagesize($filesource);
        // Rate
        if ($width >= $height) {
            if ($width<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$width;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        } else {
            if ($height<=200) {
                $viewopt['width']=$width;
                $viewopt['height']=$height;
            } else {
                $rate=200/$height;
                $viewopt['width']=ceil($width*$rate);
                $viewopt['height']=ceil($height*$rate);
            }
        }
        $content=$this->load->view('redraw/viewsource_view',$viewopt, TRUE);
        echo $content;
    }

    public function art_rdnoteview() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $artwork_id=$artdata['artwork_id'];
                $art_id=$postdata['art_id'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->get_artdata_locrdnote($artdata, $art_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $mdata['content']=$this->load->view('artpage/newarttext_view',array('artwork_id'=>$artwork_id,'usrtxt'=>$res['usrtxt'],'title'=>'Type notes to Redraw Team'),TRUE);
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function  art_rdnotesave() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $art_id=$postdata['art_id'];
                $redraw_message=$postdata['redraw_message'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->save_artdata_locrdnote($artdata, $art_id, $redraw_message, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $mdata['content']=$res['content'];
                    $error='';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Change number of colors */
    public function art_savenumcolors() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Connection Lost. Please, recall function';
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $art_id=$postdata['art_id'];
                $numcolors=$postdata['numcolors'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->artlocationdata_update($artdata, 'art_numcolors', $numcolors, $art_id, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    switch ($numcolors) {
                        case '':
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color1', '', $art_id, $artsession);
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color2', '', $art_id, $artsession);
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color3', '', $art_id, $artsession);
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color4', '', $art_id, $artsession);
                            break;
                        case 1:
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color2', '', $art_id, $artsession);
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color3', '', $art_id, $artsession);
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color4', '', $art_id, $artsession);
                            break;
                        case 2:
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color3', '', $art_id, $artsession);
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color4', '', $art_id, $artsession);
                            break;
                        case 3:
                            $artdata=usersession($artsession);
                            $this->artwork_model->artlocationdata_update($artdata, 'art_color4', '', $art_id, $artsession);
                            break;
                        default:
                            break;
                    }
                    $artdata=usersession($artsession);
                    $artcolors=$this->artwork_model->get_artcolors($artdata, $art_id);
                    $artcolors['artwork_art_id']=$art_id;
                    $imprint_colors = $this->config->item('imprint_colors');
                    $colordat=$this->artwork_model->colordat_prepare($artcolors, $imprint_colors);
                    $mdata['content']=$this->load->view('artpage/artwork_coloroptions_view',$colordat,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    /* show form for colorcolorchoice */
    public function art_colorchoice() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $art_id=$postdata['art_id'];
                $color_num=$postdata['color_num'];
                $this->load->model('artwork_model');
                $res=$this->artwork_model->get_artloc_numcolors($artdata, $art_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    if ($res['art_numcolors']<$color_num) {
                        $error='Change #Colors for Edit Color';
                    } else {
                        $mdata['content']=$this->load->view('artpage/art_colorchoice_view',array('colors'=>$this->config->item('imprint_colors')),TRUE);
                        $error='';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    /* Save color choice */
    public function art_savecolor() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $this->load->model('artwork_model');
                $art_id=$postdata['art_id'];
                $color_num=$postdata['color_num'];
                $color_code=$postdata['color_code'];
                /* find color name */
                $color_title='';
                $imprint_colors = $this->config->item('imprint_colors');
                foreach ($imprint_colors as $colrow) {
                    if ($colrow['code']==$color_code) {
                        $color_title=$colrow['name'];
                        break;
                    }
                }
                if ($color_title=='') {
                    $error='Unknown Color Code';
                } else {
                    $fld_name='art_color'.$color_num;
                    $res=$this->artwork_model->artlocationdata_update($artdata, $fld_name, $color_title, $art_id, $artsession);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $artdata=usersession($artsession);
                        $artcolors=$this->artwork_model->get_artcolors($artdata, $art_id);
                        $artcolors['artwork_art_id']=$art_id;
                        $colordat=$this->artwork_model->colordat_prepare($artcolors, $imprint_colors);
                        $mdata['content']=$this->load->view('artpage/artwork_coloroptions_view',$colordat,TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function art_redrawupd() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = $this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $art_id=$this->input->post('art_id');
                $redraw=$this->input->post('redraw');
                $this->load->model('artwork_model');
                $res=$this->artwork_model->art_redraw_update($artdata, $art_id, $redraw, $artsession);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['newclass']=$res['artwork_class'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function artwork_history() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_artdata_error;
            $postdata=$this->input->post();
            $artsession=(isset($postdata['artsession']) ? $postdata['artsession'] : 'failsession');
            $artdata=usersession($artsession);
            if (!empty($artdata)) {
                $error='';
                $history=$artdata['art_history'];
                $mdata['content']=$this->load->view('artpage/history_view', array('history'=>$history), TRUE);
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
