<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Artlead_model extends MY_Model
{
    private $empty_out_content='&nbsp;';

    function __construct()
    {
        parent::__construct();
    }

    public function get_art_locations($artwork_id) {
        $this->db->select('a.*,art.order_id, art.mail_id, ord.order_num, proof.proof_num');
        $this->db->from('ts_artwork_arts a');
        $this->db->join('ts_artworks art','art.artwork_id=a.artwork_id');
        $this->db->join('ts_orders ord','ord.order_id=art.order_id','left');
        $this->db->join('ts_emails proof','proof.email_id=art.mail_id','left');
        $this->db->where('a.artwork_id',$artwork_id);
        $results=$this->db->get()->result_array();

        $return_array=array();
        // Add elements for output
        foreach ($results as $row) {
            if ($row['art_type']=='Logo' || $row['art_type']=='Reference') {
                $row['locat_ready']=($row['vectorized_time']>0 ? 1 : 0);
            } else {
                if ($row['redrawvect']==1 && empty($row['logo_vectorized'])) {
                    $row['locat_ready']=0;
                } else {
                    $row['locat_ready']=1;
                }
            }
            $row['artlabel']=$this->empty_out_content;
            switch ($row['art_type']) {
                case 'Logo':
                    $row['artlabel']='Open Jpg';
                    break;
                case 'Text':
                    $row['artlabel']='Text';
                    break;
                case 'Reference':
                    $row['artlabel']='Open Doc';
                    break;
                default :
                    $row['artlabel']='Repeat';
                    break;
            }
            $row['deleted']='';
            $row['redrawchk']=$row['rushchk']=$row['redochk']='&nbsp;';
            if ($row['logo_vectorized']) {
                if ($row['art_type']!='Repeat') {
                    $inptopt=array(
                        'artwork_art_id'=>$row['artwork_art_id'],
                        'chk'=>'',
                        'type'=>'artundo',
                        'title'=>'Redo',
                    );
                    $row['redochk']=$this->load->view('leadorderdetails/artlocs/artlocation_check_view', $inptopt, TRUE);
                }
            } else {
                if ($row['redrawvect']) {
                    $inptopt=array(
                        'artwork_art_id'=>$row['artwork_art_id'],
                        'chk'=>'checked="checked"',
                        'type'=>'artredraw',
                        'title'=>'Redraw',
                    );
                    $row['redrawchk']=$this->load->view('leadorderdetails/artlocs/artlocation_check_view', $inptopt, TRUE);
                } else {
                    $inptopt=array(
                        'artwork_art_id'=>$row['artwork_art_id'],
                        'type'=>'artredraw',
                        'title'=>'Redraw',
                        'chk'=>'',
                    );
                    if (($row['art_type']=='Logo' || $row['art_type']=='Reference') && !$row['logo_vectorized']) {
                        $inptopt['chk']='checked="checked"';
                    } elseif ($row['art_type']=='Text') {
                        $inptopt['chk']='';
                    }
                    $row['redrawchk']=$this->load->view('leadorderdetails/artlocs/artlocation_check_view', $inptopt, TRUE);
                }
            }
            if ($row['rush']==1) {
                $chk='checked="checked"';
            } else {
                $chk='';
            }
            $inptopt=array(
                'artwork_art_id'=>$row['artwork_art_id'],
                'chk'=>$chk,
                'type'=>'artrush',
                'title'=>'Rush',
            );
            $row['rushchk']=$this->load->view('leadorderdetails/artlocs/artlocation_check_view', $inptopt, TRUE);
            $return_array[]=$row;
        }
        return $return_array;
    }
//
//    public function add_location($leadorder, $data, $loctype, $ordersession) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $order=$leadorder['order'];
//        if ($loctype=='Repeat' && empty($data['order_num'])) {
//            $out['msg']='Empty Repeat Order';
//            return $out;
//        }
//        $locations=$leadorder['artlocations'];
//        $numrec=count($locations)+1;
//        $artwork=$leadorder['artwork'];
//        $fields = $this->db->list_fields('ts_artwork_arts');
//        $newlocation=array();
//        foreach ($fields as $field) {
//            $newlocation[$field]='';
//        }
//        $newlocation['artwork_id']=$artwork['artwork_id'];
//        $newlocation['artwork_art_id']=$numrec*(-1);
//        $newlocation['art_type']=$loctype;
//        $newlocation['locat_ready']=0;
//        $newlocation['art_ordnum']=$numrec;
//        $newlocation['artlabel']=$this->empty_out_content;
//        $newlocation['redrawchk']=$newlocation['rushchk']=$newlocation['redochk']='&nbsp;';
//        if ($loctype=='Logo' || $loctype=='Reference') {
//            $newlocation['artlabel']='Open Jpg';
//            // Change Logo path
//            $preload_path_fl=$this->config->item('upload_path_preload');
//            $preload_path_sh=$this->config->item('pathpreload');
//            $logopath=$data['logo'];
//            /* Make Filename */
//            $file_name=str_replace($preload_path_fl, '', $logopath);
//            $logosrc_path=$preload_path_sh.$file_name;
//            $newlocation['logo_src']=$logosrc_path;
//            // Checks
//            $inptopt=array(
//                'artwork_art_id'=>$newlocation['artwork_art_id'],
//                'chk'=>'checked="checked"',
//                'type'=>'artredraw',
//                'title'=>'Redraw',
//            );
//            $newlocation['redrawvect']=1;
//            $newlocation['redrawchk']=$this->load->view('leadorderdetails/artlocs/artlocation_check_view', $inptopt, TRUE);
//            $chkrush='';
//            if ($order['order_rush']) {
//                $chkrush='checked="checked"';
//            }
//            $rushopt=array(
//                'artwork_art_id'=>$newlocation['artwork_art_id'],
//                'chk'=>$chkrush,
//                'type'=>'artrush',
//                'title'=>'Rush',
//            );
//            $newlocation['rushchk']=$this->load->view('leadorderdetails/artlocs/artlocation_check_view', $rushopt, TRUE);
//        } elseif ($loctype=='Text') {
//            $newlocation['artlabel']='Text';
//        } else {
//            $newlocation['artlabel']='Repeat';
//            $newlocation['order_num']=$data['order_num'];
//            $newlocation['repeat_text']=$data['order_num'];
//            $newlocation['locat_ready']=1;
//        }
//        $newlocation['deleted']='';
//        $locations[]=$newlocation;
//        $leadorder['artlocations']=$locations;
//        $leadorder['order']=$order;
//        $this->func->session($ordersession, $leadorder);
//        $out['result']=$this->success_result;
//        $this->load->model('artwork_model');
//        $this->artwork_model->_artlocation_log($newlocation['artwork_id'], $newlocation['artwork_art_id'], 'Add New Location');
//        $outloc=array();
//        foreach ($locations as $row) {
//            if ($row['deleted']=='') {
//                $outloc[]=$row;
//            }
//        }
//        $out['artlocations']=$outloc;
//        return $out;
//    }
//
//    public function remove_location($leadorder, $artwork_art_id, $ordersession) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $locations=$leadorder['artlocations'];
//        // Find locat
//        $found=0;
//        $locid=0;
//        foreach ($locations as $row) {
//            if ($row['artwork_art_id']==$artwork_art_id) {
//                $found=1;
//                $locations[$locid]['deleted']='del';
//                $this->load->model('artwork_model');
//                $this->artwork_model->_artlocation_log($row['artwork_id'], $row['artwork_art_id'], 'Delete Location');
//                break;
//            }
//            $locid++;
//        }
//        if ($found==0) {
//            $out['msg']='Art Location Not Found';
//            return $out;
//        }
//        $leadorder['artlocations']=$locations;
//        $this->func->session($ordersession, $leadorder);
//        $out['result']=$this->success_result;
//        $outloc=array();
//        foreach ($locations as $row) {
//            if ($row['deleted']=='') {
//                $outloc[]=$row;
//            }
//        }
//        $out['artlocations']=$outloc;
//        return $out;
//    }
//
//    // Change Art Location Parameter
//    public function change_location($leadorder, $artwork_art_id, $field, $newval, $ordersession) {
//        $out=array('result'=>$this->error_result,'msg'=>$this->init_msg);
//        $locations=$leadorder['artlocations'];
//        $found=0;
//        $locidx=0;
//        foreach ($locations as $row) {
//            if ($row['artwork_art_id']==$artwork_art_id) {
//                $found=1;
//                break;
//            } else {
//                $locidx++;
//            }
//        }
//        if ($found==0) {
//            $out['msg']='Art Location Not Found';
//            return $out;
//        }
//        if (!array_key_exists($field, $locations[$locidx])) {
//            $out['msg']='Location Parameter '.strtoupper($field).' Undefined';
//        }
//        // Change Parameter
//        $locations[$locidx][$field]=$newval;
//        $leadorder['artlocations']=$locations;
//        $this->func->session($ordersession, $leadorder);
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//    // Show Art Location Docs
//    public function show_artlocation($leadorder, $artwork_art_id, $ordersession) {
//        $out=array('result'=>$this->error_result,'msg'=>$this->init_msg);
//        $locations=$leadorder['artlocations'];
//        $found=0;
//        $locidx=0;
//        foreach ($locations as $row) {
//            if ($row['artwork_art_id']==$artwork_art_id) {
//                $found=1;
//                break;
//            } else {
//                $locidx++;
//            }
//        }
//        if ($found==0) {
//            $out['msg']='Art Location Not Found';
//            return $out;
//        }
//        // Change Parameter
//        $out['location']=$locations[$locidx];
//        $leadorder['artlocations']=$locations;
//        $this->func->session($ordersession, $leadorder);
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//    public function show_artdata($location,$doctype) {
//        $out=array('result'=>$this->error_result,'msg'=>$this->init_msg);
//        $order_num=$location['repeat_text'];
//        if (empty($order_num)) {
//            $out['msg']='Empty Repeat Data';
//            return $out;
//        }
//        // Select logo
//        if ($doctype=='source') {
//            $this->db->select('a.logo_src as logourl');
//        } else {
//            $this->db->select('a.logo_vectorized as logourl');
//        }
//        $this->db->from('ts_artwork_arts a');
//        $this->db->join('ts_artworks art','art.artwork_id=a.artwork_id');
//        $this->db->join('ts_orders o','o.order_id=art.order_id');
//        $this->db->where('o.order_num', $order_num);
//        $srcres=$this->db->get()->result_array();
//        $urls=array();
//        foreach ($srcres as $row) {
//            if (!empty($row['logourl'])) {
//                array_push($urls, $row['logourl']);
//            }
//        }
//        if (count($urls)==0) {
//            $out['msg']='Empty Arts Content for Order # '.$order_num;
//        } else {
//            $out['result']=$this->success_result;
//            $out['urls']=$urls;
//        }
//        return $out;
//    }
//
//    // Save uploaded
//    public function save_artproofdocs($leadorder, $proofupload, $ordersession, $uplsession) {
//        $out=array('result'=>$this->error_result,'msg'=>$this->init_msg);
//        $proofdocs=$leadorder['artproofs'];
//        $newidx=count($proofdocs)+1;
//        foreach ($proofupload as $row) {
//            $newdoc=array(
//                'artwork_proof_id'=>$newidx*(-1),
//                'created_time'=>date('Y-m-d H:i:s'),
//                'proof_ordnum'=>$newidx,
//                'sended'=>0,
//                'sended_time'=>0,
//                'approved'=>0,
//                'approved_time'=>0,
//                'source_name'=>$row['filename'],
//                'proofdoc_link'=>'',
//                'src'=>$row['filesource'],
//                'out_proofname'=>'proof_'.str_pad($newidx,2,'0',STR_PAD_LEFT),
//                'senddoc'=>0,
//                'deleted'=>'',
//            );
//            $proofdocs[]=$newdoc;
//            $newidx++;
//        }
//        $leadorder['artproofs']=$proofdocs;
//        $this->func->session($ordersession, $leadorder);
//        $this->func->session($uplsession, NULL);
//        $out['result']=$this->success_result;
//        $out_proof=array();
//        foreach ($proofdocs as $row) {
//            if ($row['deleted']=='') {
//                $out_proof[]=$row;
//            }
//        }
//        $out['outproof']=$out_proof;
//        return $out;
//    }
//
//    // Change Art Proof docs
//    public function change_artproofdocs($leadorder, $artwork_proof_id, $fldname, $newval, $ordersession) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $proofdocs=$leadorder['artproofs'];
//        $found=0;
//        $pidx=0;
//        foreach ($proofdocs as $row) {
//            if ($row['artwork_proof_id']==$artwork_proof_id) {
//                $found=1;
//                break;
//            } else {
//                $pidx++;
//            }
//        }
//        if ($found==0) {
//            $out['msg']='Proof Document Not Found';
//            return $out;
//        }
//        $proofdocs[$pidx][$fldname]=$newval;
//        if ($fldname=='approved') {
//            if ($newval==1) {
//                $proofdocs[$pidx]['approved_time']=time();
//            } else {
//                $proofdocs[$pidx]['approved_time']=0;
//            }
//        }
//        $leadorder['artproofs']=$proofdocs;
//        $this->func->session($ordersession, $leadorder);
//        // $this->func->session('proofupload', NULL);
//        $out['result']=$this->success_result;
//        $out_proof=array();
//        foreach ($proofdocs as $row) {
//            if ($row['deleted']=='') {
//                $out_proof[]=$row;
//            }
//        }
//        $out['outproof']=$out_proof;
//        return $out;
//    }
//
//    // Prepare template for send ART Proof Email
//    public function prepare_proofdocapproveemail($leadorder, $template, $user_id, $ordersession) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $proofdocs=$leadorder['artproofs'];
//        $order_system=$leadorder['order_system'];
//        if ($order_system=='new') {
//            $contacts=$leadorder['contacts'];
//        } else {
//            $order=$leadorder['order'];
//            $contacts=array(
//                array(
//                    'contact_name'=>$order['customer_name'],
//                    'contact_emal'=>$order['customer_email'],
//                    'contact_art'=>1,
//                )
//            );
//        }
//
//        $order=$leadorder['order'];
//        $artwork=$leadorder['artwork'];
//        $found=0;
//        foreach ($proofdocs as $row) {
//            if ($row['deleted']=='' && $row['senddoc']==1) {
//                $found++;
//            }
//        }
//        if ($found==0) {
//            $out['msg']='Check Proofs for Sending';
//            return $out;
//        }
//        // Analise contacts
//        $emails=array();
//        foreach ($contacts as $row) {
//            if ($row['contact_art']==1 && $this->func->valid_email_address($row['contact_emal'])) {
//                $emails[]=array(
//                    'contact_name'=>$row['contact_name'],
//                    'contact_emal'=>$row['contact_emal'],
//                );
//            }
//        }
//        if (count($emails)==0) {
//            $out['msg']='Mark Contact email for Proof Docs Notification';
//            return $out;
//        }
//        $tomail='';
//        foreach ($emails as $row) {
//            $tomail.=$row['contact_emal'].',';
//        }
//        $out['customer_email']=substr($tomail,0,-1);
//        $this->load->model('user_model');
//        $this->load->model('email_model');
//        $userdat = $this->user_model->get_user_data($user_id);
//        $user_name = $userdat['user_name'];
//        $this->load->model('email_model');
//        $mail_template = $this->email_model->get_emailtemplate_byname($template);
//        $msgdat = "BT" . $order['order_num'];
//        $doc_type = 'Order';
//        $itemname = $order['order_items'];
//        $message = $mail_template['email_template_body'];
//        $message = str_replace('<<customer_name>>', $order['customer_name'], $message);
//        $message = str_replace('<<item_name>>', $itemname, $message);
//        $message = str_replace('<<user_name>>', $user_name, $message);
//        $message = str_replace('<<document_type>>', $doc_type, $message);
//        $subj = str_replace('<<order_number>>', $msgdat, $mail_template['email_template_subject']);
//        $subj = str_replace('<<document_type>>', $doc_type, $subj);
//        $subj = str_replace('<<item_name>>', $itemname, $subj);
//        $out['result']=$this->success_result;
//        $out['artwork_id']=$artwork['artwork_id'];
//        $out['subject']=$subj;
//        $out['message']=$message;
//        $this->func->session($ordersession, $leadorder);
//        return $out;
//    }
//
//    public function send_artproofmail($data, $leadorder, $user_id, $ordersession) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $proofdocs=$leadorder['artproofs'];
//        // $contacts=$leadorder['contacts'];
//        // $order=$leadorder['order'];
//        $artwork=$leadorder['artwork'];
//        $artwork_id=$artwork['artwork_id'];
//        $this->load->model('user_model');
//        $this->load->model('artwork_model');
//        /* Check Data */
//        if (empty($data['from'])) {
//            $out['msg']='Enter Sender Email';
//            return $out;
//        }
//        if (empty($data['customer'])) {
//            $out['msg']='Enter Customer Email';
//            return $out;
//        }
//        if (empty($data['subject'])) {
//            $out['msg']='Enter Message Subject';
//            return $out;
//        }
//        if (empty($data['message'])) {
//            $out['msg']='Enter Message Body';
//            return $out;
//        }
//        $toarray = explode(',', $data['customer']);
//        foreach ($toarray as $row) {
//            if (!$this->func->valid_email_address(trim($row))) {
//                $out['msg'] = $row . ' Is not Valid';
//                return $out;
//            }
//        }
//        if (!empty($data['cc'])) {
//            $ccarray = explode(',', $data['cc']);
//            foreach ($ccarray as $row) {
//                if (!$this->func->valid_email_address(trim($row))) {
//                    $out['msg'] = 'BCC Email Address ' . $row . ' Is not Valid';
//                    return $out;
//                }
//            }
//        }
//        // Get list of proofs, which marked FOR SEND
//        $numpp=1;
//        $pridx=0;
//        foreach ($proofdocs as $row) {
//            if ($row['deleted']=='') {
//                $proofdocs[$pridx]['proof_ordnum']=$numpp;
//                $numpp++;
//            }
//            $pridx++;
//        }
//        $attachments = array();
//        $attachlink = array();
//        $proofurl = $this->config->item('newprooflnk');
//        foreach ($proofdocs as $row) {
//            $row['artwork_id']=$artwork_id;
//            if ($row['deleted']=='' && $row['senddoc']==1) {
//                $row['sended']=1;
//                $row['sended_time']=time();
//                $row['proofdoc_link']=(empty($row['proofdoc_link']) ? $this->func->uniq_link(20) : $row['proofdoc_link']);
//            }
//            $res=$this->_save_proofdoc($row, $artwork_id, $user_id);
//            if ($res['result']==$this->error_result) {
//                $this->func->proofdoclog($artwork_id, $user_id, $row['src'], $row['source_name'], 'Lost Upload');
//            } else {
//                if ($row['deleted']=='' && $row['senddoc']==1) {
//                    $this->func->proofdoclog($artwork_id, $user_id, $row['src'], $row['source_name'], 'Send Proof');
//                    array_push($attachlink, $row['proofdoc_link']);
//                    $attachsrc = $row['proofdoc_link'];
//                    $attachments[] = $proofurl . $attachsrc;
//                } else {
//                    $this->func->proofdoclog($artwork_id, $user_id, $row['src'], $row['source_name'], 'Save Proof');
//                }
//            }
//        }
//        // Send message
//        $this->load->library('email');
//        $config = $this->config->item('email_setup');
//        $config['mailtype'] = 'text';
//        $this->email->initialize($config);
//        if ($config['protocol']=='smtp') {
//            $this->email->from($config['smtp_user']);
//        } else {
//            $this->email->from($data['from']);
//        }
//
//        $this->email->to($data['customer']);
//        if ($data['cc'] != '') {
//            $cc = $data['cc'];
//            $this->email->cc($cc);
//        }
//        $this->email->subject($data['subject']);
//
//        if (count($attachments) == 1) {
//            $message = 'Below you will find a link to your art proof.  Please click on the link to view it:' . PHP_EOL;
//            $message.='' . PHP_EOL;
//            $message.=$attachments[0];
//        } else {
//            $message = 'Below you will find links to your art proofs.  Please click on each link to view the different pages:' . PHP_EOL;
//            $message.='' . PHP_EOL;
//            foreach ($attachments as $row) {
//                $message.=$row . PHP_EOL;
//            }
//        }
//
//        $smessage = str_replace('<<links>>', $message, $data['message']);
//
//        $this->email->message($smessage);
//
//        $histmsg = 'Art proof sent - ';
//        $histmsg.='' . count($attachments) . ' attachments';
//        $details = '';
//        foreach ($attachments as $row) {
//            $details.=$row . '<br/>' . PHP_EOL;
//        }
//        $mailres=$this->email->send();
//
//        $this->email->clear(TRUE);
//        $logoptions = array(
//            'from' => $data['from'],
//            'to' => $data['customer'],
//            'subject' => $data['subject'],
//            'message' => $data['message'],
//            'user_id' => $user_id,
//        );
//        if (!empty($data['cc'])) {
//            $logoptions['cc'] = $data['cc'];
//        }
//        if (count($attachments) > 0) {
//            $logoptions['attachments'] = $attachments;
//        }
//        $this->func->logsendmail($logoptions);
//
//        $proofdat=$this->artwork_model->get_artproofs($artwork_id);
//        $leadorder['artproofs']=$proofdat;
//        $this->func->session($ordersession, $leadorder);
//        $out['outproof'] = $proofdat;
//        $out['result'] = $this->success_result;
//        return $out;
//    }
//
//    // Show uploaded Proof document
//    public function show_atproofdoc($leadorder, $artwork_proof_id, $ordersession) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $proofdat=$leadorder['artproofs'];
//        $found=0;
//        $pidx=0;
//        foreach ($proofdat as $row) {
//            if ($row['artwork_proof_id']==$artwork_proof_id) {
//                $found=1;
//                break;
//            } else {
//                $pidx++;
//            }
//        }
//        if ($found==0) {
//            $out['msg']='Proof Doc Not Found';
//            return $out;
//        }
//        $out['outproof'] = $proofdat[$pidx];
//        $leadorder['artproofs']=$proofdat;
//        $this->func->session($ordersession, $leadorder);
//        $out['result'] = $this->success_result;
//        return $out;
//    }
//
//    public function save_artwork($artw, $user_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//        $this->db->set('order_id',$artw['order_id']);
//        $this->db->set('mail_id',$artw['mail_id']);
//        if (isset($artw['customer_instruct'])) {
//            $this->db->set('customer_instruct',$artw['customer_instruct']);
//        }
//        if (isset($artw['customer'])) {
//            $this->db->set('customer',$artw['customer']);
//        }
//        if (isset($artw['customer_contact'])) {
//            $this->db->set('customer_contact',$artw['customer_contact']);
//        }
//        if (isset($artw['customer_phone'])) {
//            $this->db->set('customer_phone', $artw['customer_phone']);
//        }
//        if (isset($artw['customer_email'])) {
//            $this->db->set('customer_email',$artw['customer_email']);
//        }
//        if (isset($artw['item_name']) && !empty($artw['item_name'])) {
//            $this->db->set('item_name',$artw['item_name']);
//        }
//        if (isset($artw['other_item']) && !empty($artw['other_item'])) {
//            $this->db->set('other_item',$artw['other_item']);
//        }
//        if (isset($artw['item_id']) && !empty($artw['item_id'])) {
//            $this->db->set('item_id',$artw['item_id']);
//        }
//        if (isset($artw['item_number']) && !empty($artw['item_number'])) {
//            $this->db->set('item_number',$artw['item_number']);
//        }
//        if (isset($artw['item_color']) && !empty($artw['item_color'])) {
//            $this->db->set('item_color',$artw['item_color']);
//        }
//        if (isset($artw['item_qty']) && !empty($artw['item_qty'])) {
//            $this->db->set('item_qty',$artw['item_qty']);
//        }
//        if (isset($artw['artwork_rush'])) {
//            $this->db->set('artwork_rush',$artw['artwork_rush']);
//        }
//        if (isset($artw['artwork_note'])) {
//            $this->db->set('artwork_note',$artw['artwork_note']);
//        }
//        if (isset($artw['other_item']) && !empty($artw['other_item'])) {
//            $this->db->set('other_item', $artw['other_item']);
//        }
//        if (isset($artw['customer_art'])) {
//            $this->db->set('customer_art', $artw['customer_art']);
//        }
//        if (isset($artw['customer_inv'])) {
//            $this->db->set('customer_inv', $artw['customer_inv']);
//        }
//        if (isset($artw['customer_track'])) {
//            $this->db->set('customer_track', $artw['customer_track']);
//        }
//        $this->db->set('user_updated',$user_id);
//        if ($artw['artwork_id']>0) {
//            $this->db->where('artwork_id',$artw['artwork_id']);
//            $this->db->update('ts_artworks');
//        } else {
//            $this->db->set('user_created',$user_id);
//            $this->db->set('time_create',date('Y-m-d H:i:s'));
//            $this->db->insert('ts_artworks');
//            $res=$this->db->insert_id();
//            if ($res==0) {
//                return FALSE;
//            } else {
//                /* If exist update MSG */
//                $artw['artwork_id']=$res;
//            }
//        }
//        return $artw['artwork_id'];
//    }
//
//    public function save_artproof($proofs, $artwork_id, $user_id) {
//        $out=array('result'=>$this->error_result);
//        // Get OLD proofs
//        $this->db->select('artwork_proof_id, approved, source_name');
//        $this->db->from('ts_artwork_proofs');
//        $this->db->where('artwork_id', $artwork_id);
//        $oldproofs=$this->db->get()->result_array();
//        $artsyncdoc=array();
//        // Order
//        $this->db->select('order_id');
//        $this->db->from('ts_artworks');
//        $this->db->where('artwork_id', $artwork_id);
//        $orddat=$this->db->get()->row_array();
//        $order_id=$orddat['order_id'];
//        // Full and Short proofs
//        $fullpath=$this->config->item('artwork_proofs');
//        $shrtpath=$this->config->item('artwork_proofs_relative');
//        $fullpreload=$this->config->item('upload_path_preload');
//        $numpp=1;
//        foreach ($proofs as $row) {
//            if ($row['deleted']!='') {
//                if ($row['artwork_proof_id']>0) {
//                    // add data for delete
//                    $this->db->select('p.source_name, o.order_id');
//                    $this->db->from('ts_artwork_proofs p');
//                    $this->db->join('ts_artworks a','a.artwork_id=p.artwork_id');
//                    $this->db->join('ts_orders o','o.order_id=a.order_id');
//                    $this->db->where('p.artwork_proof_id', $row['artwork_proof_id']);
//                    $delres=$this->db->get()->row_array();
//                    if (isset($delres['order_id'])) {
//                        $artsyncdoc[]=array(
//                            'user_id'=>$user_id,
//                            'order_id'=>$delres['order_id'],
//                            'operation'=>'delete',
//                            'proofdoc_link'=>$delres['source_name'],
//                        );
//                    }
//                    $this->db->where('artwork_proof_id', $row['artwork_proof_id']);
//                    $this->db->delete('ts_artwork_proofs');
//                }
//            } else {
//                $saverow=0;
//                if ($row['artwork_proof_id']<0) {
//                    // Artwork Folder
//                    if (file_exists($row['src'])) {
//                        $this->_artworkfolder($fullpath, $artwork_id);
//                        // New Proof Doc
//                        $purename=  str_replace($fullpreload, '', $row['src']);
//                        $target_file=$fullpath.$artwork_id.'/'.$purename;
//                        $cpres=@copy($row['src'],$target_file);
//                        if ($cpres) {
//                            $saverow=1;
//                            $row['src']=$shrtpath.$artwork_id.'/'.$purename;
//                        }
//                    }
//                } else {
//                    $saverow=1;
//                }
//                if ($saverow==1) {
//                    $this->db->set('updated_user',$user_id);
//                    $this->db->set('proof_ordnum',$numpp);
//                    if (isset($row['src'])) {
//                        $this->db->set('proof_name',$row['src']);
//                    }
//                    if (isset($row['sended'])) {
//                        $this->db->set('sended',$row['sended']);
//                    }
//                    if (isset($row['sended_time'])) {
//                        $this->db->set('sended_time',$row['sended_time']);
//                    }
//                    if (isset($row['approved'])) {
//                        $this->db->set('approved',$row['approved']);
//                    }
//                    if (isset($row['approved_time'])) {
//                        $this->db->set('approved_time',$row['approved_time']);
//                    }
//                    if (isset($row['source_name'])) {
//                        $this->db->set('source_name', $row['source_name']);
//                    }
//                    if (isset($row['proofdoc_link']) && !empty($row['proofdoc_link'])) {
//                        $this->db->set('proofdoc_link', $row['proofdoc_link']);
//                    }
//                    if ($row['artwork_proof_id']<=0) {
//                        $this->db->set('artwork_id',$artwork_id);
//                        $this->db->set('created_user',$user_id);
//                        $this->db->set('created_time',date('Y-m-d H:i:s'));
//                        $this->db->insert('ts_artwork_proofs');
//                        $retval=$this->db->insert_id();
//                        $this->db->select('order_id');
//                        $this->db->from('ts_artworks');
//                        $this->db->where('artwork_id', $artwork_id);
//                        $insres=$this->db->get()->row_array();
////                        if (!empty($insres['order_id'])) {
////                            // Add export doc
////                            $artsyncdoc[]=array(
////                                'user_id'=>$user_id,
////                                'order_id'=>$insres['order_id'],
////                                'artwork_proof_id'=>$retval,
////                                'operation'=>'add',
////                            );
////                        }
//                    } else {
//                        $this->db->where('artwork_proof_id',$row['artwork_proof_id']);
//                        $this->db->update('ts_artwork_proofs');
//                        $retval=$row['artwork_proof_id'];
//                    }
//                }
//            }
//        }
//        $out['result']=$this->success_result;
//        // Build ART Sync doc
//        if (!empty($order_id)) {
//            // Get New Proofs
//            $this->db->select('artwork_proof_id, approved, source_name');
//            $this->db->from('ts_artwork_proofs');
//            $this->db->where('artwork_id', $artwork_id);
//            $newproofs=$this->db->get()->result_array();
//            // $profkeys=array();
//            foreach ($oldproofs as $row) {
//                if ($row['approved']==1) {
//                    foreach ($newproofs as $prow) {
//                        if ($prow['artwork_proof_id']==$row['artwork_proof_id'] && $prow['approved']==0) {
//                            // Remove Approve Link
//                            $artsyncdoc[]=array(
//                                'user_id'=>$user_id,
//                                'order_id'=>$order_id,
//                                'operation'=>'delete',
//                                'proofdoc_link'=>$row['source_name'],
//                            );
//                            break;
//                        }
//                    }
//                }
//            }
//            // Check New Res
//            foreach ($newproofs as $row) {
//                if ($row['approved']==1) {
//                    $found=0;
//                    foreach ($oldproofs as $prow) {
//                        if ($prow['artwork_proof_id']==$row['artwork_proof_id']) {
//                            $found=1;
//                            if ($prow['approved']==0) {
//                                // Add Approve Link
//                                $artsyncdoc[]=array(
//                                    'user_id'=>$user_id,
//                                    'order_id'=>$order_id,
//                                    'operation'=>'add',
//                                    'artwork_proof_id'=>$row['artwork_proof_id'],
//                                );
//                            }
//                            break;
//                        }
//                    }
//                    if ($found==0) {
//                        $artsyncdoc[]=array(
//                            'user_id'=>$user_id,
//                            'order_id'=>$order_id,
//                            'operation'=>'add',
//                            'artwork_proof_id'=>$row['artwork_proof_id'],
//                        );
//                    }
//                }
//            }
//        }
//        $out['artsyncdoc']=$artsyncdoc;
//        return $out;
//    }
//
//    private function _save_proofdoc($proofdoc, $artwork_id, $user_id) {
//        $out=array('result'=>$this->error_result, 'msg'=>$this->init_msg);
//
//        $fullpath=$this->config->item('artwork_proofs');
//        $shrtpath=$this->config->item('artwork_proofs_relative');
//        $fullpreload=$this->config->item('upload_path_preload');
//
//        if ($proofdoc['deleted'] != '') {
//            if ($proofdoc['artwork_proof_id'] > 0) {
//                $this->db->where('artwork_proof_id', $proofdoc['artwork_proof_id']);
//                $this->db->delete('ts_artwork_proofs');
//            }
//            $out['result']=$this->success_result;
//            return $out;
//        }
//        $saverow = 0;
//        if ($proofdoc['artwork_proof_id'] < 0) {
//            // Artwork Folder
//            if (file_exists($proofdoc['src'])) {
//                $this->_artworkfolder($fullpath, $artwork_id);
//                // New Proof Doc
//                $purename = str_replace($fullpreload, '', $proofdoc['src']);
//                $target_file = $fullpath . $artwork_id . '/' . $purename;
//                $cpres = @copy($proofdoc['src'], $target_file);
//                if ($cpres) {
//                    $saverow = 1;
//                    $proofdoc['src'] = $shrtpath . $artwork_id . '/' . $purename;
//                }
//            }
//        } else {
//            $saverow = 1;
//        }
//        if ($saverow == 1) {
//            $this->db->set('updated_user', $user_id);
//            if (isset($proofdoc['src'])) {
//                $this->db->set('proof_name', $proofdoc['src']);
//            }
//            if (isset($proofdoc['sended'])) {
//                $this->db->set('sended', $proofdoc['sended']);
//            }
//            if (isset($proofdoc['sended_time'])) {
//                $this->db->set('sended_time', $proofdoc['sended_time']);
//            }
//            if (isset($proofdoc['approved'])) {
//                $this->db->set('approved', $proofdoc['approved']);
//            }
//            if (isset($proofdoc['approved_time'])) {
//                $this->db->set('approved_time', $proofdoc['approved_time']);
//            }
//            if (isset($proofdoc['source_name'])) {
//                $this->db->set('source_name', $proofdoc['source_name']);
//            }
//            if (isset($proofdoc['proofdoc_link']) && !empty($proofdoc['proofdoc_link'])) {
//                $this->db->set('proofdoc_link', $proofdoc['proofdoc_link']);
//            }
//            if ($proofdoc['artwork_proof_id'] <= 0) {
//                $this->db->set('artwork_id', $artwork_id);
//                $this->db->set('created_user', $user_id);
//                $this->db->set('created_time', date('Y-m-d H:i:s'));
//                $this->db->insert('ts_artwork_proofs');
//                $retval = $this->db->insert_id();
//            } else {
//                $this->db->where('artwork_proof_id', $proofdoc['artwork_proof_id']);
//                $this->db->update('ts_artwork_proofs');
//                $retval = $proofdoc['artwork_proof_id'];
//            }
//            $out['result']=$this->success_result;
//            $out['artwork_proof_id']=$retval;
//        }
//        return $out;
//    }
//
//    private function _artworkfolder($path, $artwork_id) {
//        $pathdir=$path.$artwork_id;
//        @mkdir($pathdir, 0777, true);
//    }
//
//    public function save_artlocations($locations, $artwork_id, $order_items) {
//        $path_fl=$this->config->item('artwork_logo');
//        $path_sh=$this->config->item('artwork_logo_relative');
//        $preload_path_fl=$this->config->item('upload_path_preload');
//        $preload_path_sh=$this->config->item('pathpreload');
//        $this->load->model('artwork_model');
//        foreach ($locations as $loc) {
//            $location=array();
//            if ($loc['deleted']!='') {
//                // Mark logos as deleted
//                if ($loc['artwork_art_id']>0) {
//                    // We delete previously saved location
//                    $this->artwork_model->delete_artlocation($loc['artwork_art_id'], $loc['artwork_id']);
//                }
//            } else {
//                $location_id = $loc['artwork_art_id'];
//                $location['artwork_id']=$artwork_id;
//                if ($loc['artwork_art_id']<=0) {
//                    $location['artwork_art_id']=0;
//                } else {
//                    $location['artwork_art_id']=$loc['artwork_art_id'];
//                }
//                $location['art_type']=$loc['art_type'];
//                $location['art_ordnum']=$loc['art_ordnum'];
//                $location['art_numcolors']=intval($loc['art_numcolors']);
//                $location['art_color1']=($loc['art_color1']=='' ? NULL : $loc['art_color1']);
//                $location['art_color2']=($loc['art_color2']=='' ? NULL : $loc['art_color2']);
//                $location['art_color3']=($loc['art_color3']=='' ? NULL : $loc['art_color3']);
//                $location['art_color4']=($loc['art_color4']=='' ? NULL : $loc['art_color4']);
//                $location['customer_text']=($loc['customer_text']=='' ? NULL : $loc['customer_text']);
//                $location['font']=($loc['font']=='' ? NULL : $loc['font']);
//                $location['redraw_message']=$loc['redraw_message'];
//                $location['art_location']=($loc['art_location']=='' ? NULL : $loc['art_location']);
//                $location['rush']=intval($loc['rush']);
//                $location['redrawvect']=intval($loc['redrawvect']);
//                $location['redo']=intval($loc['redo']);
//                $location['repeat_text']=($loc['repeat_text']=='' ? NULL : $loc['repeat_text']);
//                if ($loc['art_type']=='Logo' || $loc['art_type']=='Reference') {
//                    /* Prepare art logos */
//                    if ($loc['artwork_art_id']<=0) {
//                        // New location - a) move file to new location
//                        if ($loc['logo_src']!='' && $loc['logo_src']!='&nbsp;') {
//                            $purefile=str_replace($preload_path_sh, '',$loc['logo_src']);
//                            /* copy */
//                            $srcname=str_replace($preload_path_sh, $preload_path_fl,$loc['logo_src']);
//                            // $srcname=str_replace($preload_path_fl, $preload_path_fl,$loc['logo_src']);
//                            // Make Folder
//                            $this->_artworkfolder($path_fl, $artwork_id);
//
//                            $destname=$path_fl.$artwork_id.'/'.$purefile;
//                            @copy($srcname,$destname);
//                            $location['logo_src']=$path_sh.$artwork_id.'/'.$purefile;
//                            $location['redraw_time']=time();
//                            if ($loc['redrawvect']==0) {
//                                // Make source vectorized
//                                $location['logo_vectorized']=$path_sh.$artwork_id.'/'.$purefile;
//                                $location['vectorized_time']=time();
//                            } else {
//                                $redraw_logos[]=array(
//                                    'logo_src'=>$loc['logo_src'],
//                                    'deed'=>'Add',
//                                );
//                            }
//                        }
//                    } else {
//                        if ($location['redo']==1) {
//                            $location['logo_vectorized']='';
//                            $location['vectorized_time']=0;
//                            $redraw_logos[]=array(
//                                'logo_src'=>$loc['logo_src'],
//                                'deed'=>'Redo',
//                            );
//                        } else {
//                            if ($loc['redrawvect']==0) {
//                                // Make source vectorized
//                                $location['logo_vectorized']=$loc['logo_src'];
//                                $location['vectorized_time']=time();
//                            }
//                        }
//                    }
//                } else {
//                    if ($location['redo']==1) {
//                        $location['logo_vectorized']='';
//                        $location['vectorized_time']=0;
//                    }
//                    if ($location['redrawvect']==1 && empty($loc['redraw_time'])) {
//                        $location['redraw_time']=time();
//                    }
//                }
//                $res=$this->artwork_model->artlocation_update($location, $location_id);
//
//                // New Locations
//                if ($res!==FALSE && $loc['artwork_art_id']<0) {
//                    foreach ($order_items as $itemrow) {
//                        foreach ($itemrow['imprint_details'] as $irow) {
//                            if ($irow['artwork_art_id']==$loc['artwork_art_id']) {
//                                $this->db->set('artwork_art_id', $res);
//                                $this->db->where('order_imprindetail_id', $irow['order_imprindetail_id']);
//                                $this->db->update('ts_order_imprindetails');
//                            }
//                        }
//                    }
//                }
//            }
//        } // End locations list
//        return TRUE;
//    }
//
//    function artwork_history_update($artw) {
//        $this->db->set('artwork_id',$artw['artwork_id']);
//        $this->db->set('user_id',$artw['user_id']);
//        $this->db->set('created_time', $artw['created_time']);
//        $this->db->set('message',$artw['update_msg']);
//        $this->db->insert('ts_artwork_history');
//        return TRUE;
//    }
//
//    public function art_blank_changestage($data, $artdata, $artwork_id, $artsync, $user_id) {
//        $this->load->model('artwork_model');
//        /* Change Stage - BLANK type */
//        $cntproofall=$this->artwork_model->artwork_chklogo($artwork_id, 'PROOF_ALL');
//        // $cntproofsend=$this->artwork_chklogo($artwork_id, 'PROOF_SEND');
//        $cntproofappr=$this->artwork_model->artwork_chklogo($artwork_id, 'PROOF_APPROVED');
//
//        $newstage='';
//        /* Lets GO */
//
//        $newstage=Artlead_model::JUST_APPROVED;
//        $artsync['art_stage']=1;
//        $artsync['redraw_stage']=1;
//        $artsync['vector_stage']=1;
//        $artsync['proof_stage']=1;
//        $artsync['approv_stage']=1;
//        if ($artdata['artstage']!=$newstage) {
//            switch ($artdata['artstage']) {
//                case Artlead_model::NO_ART :
//                    $newstage=Artlead_model::REDRAWN;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::NO_VECTOR;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::TO_PROOF;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::NEED_APPROVAL;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::JUST_APPROVED;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    break;
//                case Artlead_model::REDRAWN :
//                    $newstage=  Artlead_model::NO_VECTOR;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::TO_PROOF;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::NEED_APPROVAL;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::JUST_APPROVED;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    break;
//                case Artlead_model::NO_VECTOR :
//                    $newstage=Artlead_model::TO_PROOF;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::NEED_APPROVAL;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::JUST_APPROVED;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    break;
//                case Artlead_model::TO_PROOF:
//                    $newstage=Artlead_model::NEED_APPROVAL;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::JUST_APPROVED;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    break;
//                case Artlead_model::NEED_APPROVAL:
//                    $newstage=Artlead_model::JUST_APPROVED;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                    $newstage=Artlead_model::TO_PROOF;
//                    $this->artwork_model->change_artstage($data, $newstage,$user_id);
//                case Artlead_model::JUST_APPROVED:
//                    break;
//            }
//        }
//        return $artsync;
//    }
//
//    /* Change Stage - COMMON type (with logos) */
//    public function art_common_changestage($data,$artdata,$artwork_id, $artsync, $user_id) {
//        $this->load->model('artwork_model');
//        /* count Logos, Proofs , etc */
//        $current_stage=$artdata['artstage'];
//        if ($current_stage==Artlead_model::JUST_APPROVED) {
//            $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=1;
//        } elseif ($current_stage==Artlead_model::NEED_APPROVAL) {
//            $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=1;
//        } elseif ($current_stage==Artlead_model::TO_PROOF) {
//            $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=1;
//        } elseif($current_stage==Artlead_model::NO_VECTOR) {
//            $artsync['art_stage']=$artsync['redraw_stage']=1;
//        } elseif ($current_stage==Artlead_model::REDRAWN) {
//            $artsync['art_stage']=1;
//        }
//        $cntlogoall=$this->artwork_model->artwork_chklogo($artwork_id, 'ALL');
//        $cnttextall=$this->artwork_model->artwork_chktext($artwork_id, 'ALL');
//        $cntrepeat=$this->artwork_model->artwork_check_repeat($artwork_id, 'ALL');
//
//        $cntall=(intval($cntlogoall)+intval($cnttextall)+intval($cntrepeat));
//
//        $cntlogovector=$this->artwork_model->artwork_chklogo($artwork_id, 'TOPROOF');
//        $cnttextvector=$this->artwork_model->artwork_chktext($artwork_id, 'TOPROOF');
//
//        $cntvector=(intval($cntlogovector)+intval($cnttextvector)+intval($cntrepeat));
//
//        $cntproofall=$this->artwork_model->artwork_chklogo($artwork_id, 'PROOF_ALL');
//        $cntproofappr=$this->artwork_model->artwork_chklogo($artwork_id, 'PROOF_APPROVED');
//        $artchk=array(
//            'stage'=>$current_stage,
//            'cntlogoall'=>$cntlogoall,
//            'cnttextall'=>$cnttextall,
//            'cntall'=>$cntall,
//            'cntlogovector'=>$cntlogovector,
//            'cnttextvector'=>$cnttextvector,
//            'cntvector'=>$cntvector,
//            'cntproofall'=>$cntproofall,
//            'cntproofappr'=>$cntproofappr,
//        );
//        $newstage='';
//        $notification=0;
//        /* Lets GO */
//        if ($cntproofappr>0 /*&& $cntproofappr==$cntproofall*/) {
//            $newstage= Artlead_model::JUST_APPROVED;
//        } elseif ($cntproofall>0) {
//            $newstage=  Artlead_model::NEED_APPROVAL;
//        } elseif ($cntall>0) {
//            if ($cntvector==$cntall) {
//                $newstage=Artlead_model::TO_PROOF;
//            } else /*($cntvector!=$cntall)*/ {
//                $newstage=Artlead_model::NO_VECTOR;
//            }
//        } else {
//            $newstage=Artlead_model::NO_ART;
//        }
//        /* Make correct change of stage */
//        if ($newstage!=$current_stage) {
//            // Need to change
//            switch ($newstage) {
//                case Artlead_model::JUST_APPROVED:
//                    $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=1;
//                    if ($current_stage==Artlead_model::NO_ART) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::REDRAWN, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::NO_VECTOR, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::TO_PROOF, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::NEED_APPROVAL, $user_id);
//                    } elseif ($current_stage==Artlead_model::REDRAWN) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::NO_VECTOR, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::TO_PROOF, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::NEED_APPROVAL, $user_id);
//                    } elseif ($current_stage==Artlead_model::NO_VECTOR) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::TO_PROOF, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::NEED_APPROVAL, $user_id);
//                    } elseif ($current_stage==Artlead_model::TO_PROOF) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::NEED_APPROVAL, $user_id);
//                    }
//                    $this->artwork_model->change_artstage($data, $newstage, $user_id);
//                    break;
//                case Artlead_model::NEED_APPROVAL:
//                    $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=1;
//                    $artsync['approv_stage']=0;
//                    if ($current_stage==Artlead_model::NO_ART) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::REDRAWN, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::NO_VECTOR, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::TO_PROOF, $user_id);
//                    } elseif ($current_stage==Artlead_model::REDRAWN) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::NO_VECTOR, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::TO_PROOF, $user_id);
//                    } elseif ($current_stage==Artlead_model::NO_VECTOR) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::TO_PROOF, $user_id);
//                    } elseif ($current_stage==Artlead_model::TO_PROOF) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::NEED_APPROVAL, $user_id);
//                    }
//                    $this->artwork_model->change_artstage($data, $newstage, $user_id);
//                    break;
//                case Artlead_model::TO_PROOF:
//                    $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=1;
//                    $artsync['proof_stage']=$artsync['approv_stage']=0;
//                    if ($current_stage==Artlead_model::NO_ART) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::REDRAWN, $user_id);
//                        $this->artwork_model->change_artstage($data, Artlead_model::NO_VECTOR, $user_id);
//                    } elseif ($current_stage==Artlead_model::REDRAWN) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::NO_VECTOR, $user_id);
//                    }
//                    $this->artwork_model->change_artstage($data, $newstage, $user_id);
//                    break;
//                case Artlead_model::NO_VECTOR:
//                    $artsync['art_stage']=$artsync['redraw_stage']=1;
//                    $artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=0;
//                    if ($current_stage==Artlead_model::NO_ART) {
//                        $this->artwork_model->change_artstage($data, Artlead_model::REDRAWN, $user_id);
//                    }
//                    $this->artwork_model->change_artstage($data, $newstage, $user_id);
//                    break;
//                case Artlead_model::REDRAWN:
//                    $artsync['art_stage']=1;
//                    $artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=0;
//                    $this->artwork_model->change_artstage($data, $newstage, $user_id);
//                    break;
//                case Artlead_model::NO_ART:
//                    $artsync['art_stage']=$artsync['redraw_stage']=$artsync['vector_stage']=$artsync['proof_stage']=$artsync['approv_stage']=0;
//                    $this->artwork_model->change_artstage($data, $newstage, $user_id);
//                    break;
//                default:
//                    break;
//            }
//            if ($current_stage== Artlead_model::JUST_APPROVED) {
//                $notification=1;
//            }
//        }
//        if ($notification==1) {
//            $this->artstage_change_notification($artwork_id, $newstage, $user_id);
//        }
//        return $artsync;
//    }
//
//    public function artstage_change_notification($artwork_id, $newstage, $user_id) {
//        $this->db->select('order_id, mail_id');
//        $this->db->from('ts_artworks');
//        $this->db->where('artwork_id', $artwork_id);
//        $artres=$this->db->get()->row_array();
//        if (isset($artres['order_id']) && !empty($artres['order_id'])) {
//            // Get Order Date
//            $order_id=$artres['order_id'];
//            $this->db->select('order_num, order_date, update_date');
//            $this->db->from('ts_orders');
//            $this->db->where('order_id', $order_id);
//            $ordres=$this->db->get()->row_array();
//            // Get User Data
//            $this->db->select('user_name');
//            $this->db->from('users');
//            $this->db->where('user_id', $user_id);
//            $usrres=$this->db->get()->row_array();
//            // Send Email
//            $this->load->library('email');
//            $config['charset'] = 'utf-8';
//            $config['mailtype']='html';
//            $config['wordwrap'] = TRUE;
//            $this->email->initialize($config);
//
//            $email_from=$this->config->item('email_notification_sender');
//
//            $email_to=array(
//                $this->config->item('sean_email'),
//                $this->config->item('sage_email'),
//            );
//
//            $email_body='At '.date('hA:i', $ordres['update_date']).' on '.date('m/d/y', $ordres['update_date']).' '.$usrres['user_name'];
//            $email_body.=' changed order #'.$ordres['order_num'].' ('.date('m/d/y', $ordres['order_date']).'). <br/>';
//            $newstage_name='Need Art';
//            switch ($newstage) {
//                case Artlead_model::NEED_APPROVAL:
//                    $newstage_name='Need Approval';
//                    break;
//                case Artlead_model::TO_PROOF:
//                    $newstage_name='To Proof';
//                    break;
//                case Artlead_model::NO_VECTOR:
//                case Artlead_model::REDRAWN:
//                    $newstage_name='Redrawing';
//                    break;
//                case Artlead_model::NO_ART:
//                    $newstage_name='Need Art';
//                    break;
//            }
//            $email_body.=' New Stage '.$newstage_name;
//
//            $this->email->from($email_from);
//            $this->email->to($email_to);
//            $subj=$usrres['user_name']." update Order #".$ordres['order_num'];
//            $this->email->subject($subj);
//            $this->email->message($email_body);
//            $this->email->send();
//            $this->email->clear(TRUE);
//        }
//        return TRUE;
//    }
//
//    function order_arttype($order_id) {
//        $arttype = 'new';
//        $this->db->select('count(id.order_imprindetail_id) as cnt');
//        $this->db->from('ts_order_imprints im');
//        $this->db->join('ts_order_items oi','oi.order_item_id=im.order_item_id');
//        $this->db->join('ts_order_imprindetails id','id.order_item_id=oi.order_item_id');
//        $this->db->where('oi.order_id', $order_id);
//        $this->db->where('id.imprint_active',1);
//        $this->db->where('id.imprint_type','REPEAT');
//        $res = $this->db->get()->row_array();
//        if ($res['cnt']>0) {
//            $arttype='repeat';
//        }
//        return $arttype;
//    }

}