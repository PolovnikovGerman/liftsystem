<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Artwork_model extends MY_Model
{

    private $INIT_MSG = 'Unknown error. Try later';
    private $order_status = 3;
    private $nonredrawn = array('ai', 'pdf', 'eps');
    private $logo_imageext = array('jpg', 'jpeg', 'png', 'gif');


    const NO_ART = '06_noart';
    const REDRAWN = '05_notredr';
    const TO_PROOF = '03_notprof';
    const NEED_APPROVAL = '02_notapprov';
    const JUST_APPROVED = '01_notplaced';
    const NO_VECTOR = '04_notvector';
    const NO_ART_REMINDER = 'Need Art Reminder';
    const ART_PROOF = 'Art Proof';
    const NEED_APPROVE_REMINDER = 'Need Approval Reminder';

    function __construct()
    {
        parent::__construct();
    }

    public function get_artwork_proof($proof_id, $user_id) {
        $artw_id=$this->artworkExist(array('proof_id'=>$proof_id));
        if (!$artw_id) {
            $this->db->select('*');
            $this->db->from('ts_emails');
            $this->db->where('email_id',$proof_id);
            $maildat=$this->db->get()->row_array();
            // Get Item Id
            $item_id='';
            if (!empty($maildat['email_item_number'])) {
                $itemoption=array(
                    'item_num'=>$maildat['email_item_number'],
                );
                $itemdat=$this->get_item_details($itemoption);
                if (count($itemdat)==1) {
                    $item_id=$itemdat[0]['item_id'];
                }
            }
            $artnote='';
            if (isset($maildat['email_special_requests'])) {
                $artnote.='Item color '.$maildat['email_special_requests'].PHP_EOL;
            }
            if (isset($maildat['email_qty'])) {
                $artnote.='Item qty - '.$maildat['email_qty'].PHP_EOL;
            }


            $artw=array(
                'artwork_id'=>0,
                'order_id'=>NULL,
                'mail_id'=>$proof_id,
                'customer_instruct'=>$maildat['email_text'],
                'user_id'=>$user_id,
                'customer'=>$maildat['email_sender'],
                'customer_contact'=>$maildat['email_printing'],
                'customer_phone'=>$maildat['email_senderphone'],
                'customer_email'=>$maildat['email_sendermail'],
                'item_name'=>$maildat['email_item_name'],
                'item_number'=>$maildat['email_item_number'],
                'item_color'=>$maildat['email_special_requests'],
                'item_qty'=>$maildat['email_qty'],
                'item_id'=>$item_id,
                'artwork_note'=>$artnote,
            );
            $art_id=$this->artwork_update($artw);
            /* Create locations & logos */
            if ($art_id) {
                /* Create Record in history */
                $history_msg='Proof Request was created '.date('m/d/Y H:i:s',  strtotime($maildat['email_date'])).' online by customer.';
                $this->db->set('artwork_id',$art_id);
                // $this->db->set('user_id',$user_id);
                // $this->db->set('created_time',time());
                $this->db->set('message',$history_msg);
                $this->db->insert('ts_artwork_history');
                /* Create LOCATIONS & LOGOS */
                $this->db->select('email_other_info, proof_num');
                $this->db->from('ts_emails');
                $this->db->where('email_id',$proof_id);
                $proof=$this->db->get()->row_array();
                $proof_art=array(
                    'logo'=>get_json_param($proof['email_other_info'],'usrlogo',''),
                    'text'=>get_json_param($proof['email_other_info'],'usrtext',''),
                    'numcolors'=>get_json_param($proof['email_other_info'],'numcolors','Full'),
                    'color_1'=>get_json_param($proof['email_other_info'],'user_color1',''),
                    'color_2'=>get_json_param($proof['email_other_info'],'user_color2',''),
                    'font'=>get_json_param($proof['email_other_info'],'user_font',''),
                    'item_color'=>get_json_param($proof['email_other_info'],'itemcolors',''),
                );
                if ($proof_art['color_1']=='' && $proof_art['color_2']=='') {
                    $proof_art['numcolors']='';
                }

                $arts=array();
                $numpp=1;
                if ($proof_art['logo']) {
                    $path_full=$this->config->item('artwork_logo');
                    $path_sh=$this->config->item('artwork_logo_relative');
                    $srclogo=str_replace($path_sh, $path_full, $proof_art['logo']);
                    if (file($srclogo)) {
                        /* File Exist */
                        $nameold=str_replace($path_sh,'',$proof_art['logo']);
                        $filedet=extract_filename($nameold);
                        $namenew='pr'.$proof['proof_num'].'_01.'.$filedet['ext'];
                        $cmp=@copy($srclogo, $path_full.$namenew);
                        if ($cmp) {
                            /* Insert LOGO */
                            $arts[]=array(
                                'artwork_id'=>$art_id,
                                'artwork_art_id'=>0,
                                'art_type'=>'Logo',
                                'art_ordnum'=>$numpp,
                                'logo_src'=>$path_sh.$namenew,
                                'redrawvect'=>1,
                                'art_numcolors'=>$proof_art['numcolors'],
                                'art_color1'=>$proof_art['color_1'],
                                'art_color2'=>$proof_art['color_2'],
                            );
                            $numpp++;
                        }
                    }
                }

                if ($proof_art['text']) {
                    $arts[]=array(
                        'artwork_id'=>$art_id,
                        'artwork_art_id'=>0,
                        'art_type'=>'Text',
                        'art_ordnum'=>$numpp,
                        'customer_text'=>$proof_art['text'],
                        'font'=>$proof_art['font'],
                        'redrawvect'=>0,
                        'art_numcolors'=>$proof_art['numcolors'],
                        'art_color1'=>$proof_art['color_1'],
                        'art_color2'=>$proof_art['color_2'],
                    );
                }
                foreach ($arts as $row) {
                    $this->artlocation_update($row);
                }
            }
            $this->db->select('a.*, m.proof_num, o.order_num');
            $this->db->from('ts_artworks a');
            $this->db->join('ts_emails m','m.email_id=a.mail_id','left');
            $this->db->join('ts_orders o','o.order_id=a.order_id','left');
            $this->db->where('a.artwork_id',$art_id);
            $return_array=$this->db->get()->row_array();
            $return_array['artwork_blank']=0;
            /* Make this after first save */
            $return_array['art_history']=$this->get_artmsg_history($art_id);
        } else {
            $this->db->select('a.*, m.proof_num, o.order_num,o.order_blank as artwork_blank');
            $this->db->from('ts_artworks a');
            $this->db->join('ts_emails m','m.email_id=a.mail_id','left');
            $this->db->join('ts_orders o','o.order_id=a.order_id','left');
            $this->db->where('a.artwork_id',$artw_id);
            $return_array=$this->db->get()->row_array();
            $return_array['art_history']=$this->get_artmsg_history($artw_id);
        }
        return $return_array;
    }

    public function get_artwork_order($order_id, $user_id) {
        $artfull_path=$this->config->item('artwork_logo');
        $artshort_path=$this->config->item('artwork_logo_relative');
        $artw_id=$this->artworkExist(array('order_id'=>$order_id));
        if (!$artw_id) {
            /* New ARTWORK */
            $online=0;
            $this->db->select('o.*, u.user_name as userwhocreate');
            $this->db->from('ts_orders o');
            $this->db->join('users u','u.user_id=o.create_usr','left');
            $this->db->where('o.order_id',$order_id);
            $orderdat=$this->db->get()->row_array();
            if (!isset($orderdat['order_id'])) {
                return array();
            }
            $this->db->where('order_id',$order_id);
            $blank_order=0;
            if ($orderdat['order_blank']==1) {
                $blank_order=1;
                $this->db->set('order_art',1);
                $orderdat['order_art']=1;
                $this->db->set('order_art_update', time());
                $orderdat['order_art_update']=time();
                $this->db->set('order_redrawn',1);
                $orderdat['order_redrawn']=1;
                $this->db->set('order_redrawn_update',time());
                $orderdat['order_redrawn_update']=time();
                $this->db->set('order_vectorized',1);
                $orderdat['order_vectorized']=1;
                $this->db->set('order_vectorized_update',  time());
                $orderdat['order_vectorized_update']=time();
                $this->db->set('order_proofed',1);
                $orderdat['order_proofed']=1;
                $this->db->set('order_proofed_update',time());
                $orderdat['order_proofed_update']=time();
            }
            $this->db->set('update_usr',$user_id);
            $this->db->set('update_date',  time());
            $this->db->update('ts_orders');

            $item_id='';
            $item_num='';
            $itemdat=array();
            if (!empty($orderdat['item_id'])) {
                $itemoption=array(
                    'item_id'=>$orderdat['item_id'],
                );
                $itemdat=$this->get_item_details($itemoption);
            } elseif (!empty($orderdat['order_items'])) {
                $itemoption=array(
                    'item_name'=>$orderdat['order_items'],
                );
                $itemdat=$this->get_item_details($itemoption);
            }
            if (count($itemdat)==1) {
                $item_id=$itemdat[0]['item_id'];
                $item_num=$itemdat[0]['item_number'];
            }
            /* Try to read order data from grey */
            $ordgrey=$this->get_orderdetails($orderdat['order_num']);
            if (isset($ordgrey['order_id'])) {
                $online=1;
                /* Read Data about locations */
                $arts=$this->get_orderarts($ordgrey['order_id']);
            } else {
                $ordgrey=array(
                    'contact_first_name'=>'',
                    'contact_last_name'=>'',
                    'contact_phone'=>'',
                    'contact_email'=>'',
                    'item_id'=>'',
                    'item_qty'=>(isset($orderdat['order_qty']) ? $orderdat['order_qty'] : 0),
                    'order_customer_comment'=>'',
                    'item_name'=>'',
                    'item_number'=>'',
                );
                $arts=array();
            }
            $add_instr='';
            foreach ($arts as $arow) {
                if ($arow['user_note']!='') {
                    $add_instr.=$arow['user_note'].PHP_EOL;
                }
            }
            $item_name=$orderdat['order_items'];
            $item_number=$item_num;
            if (!empty($ordgrey['item_name'])) {
                $item_name=$ordgrey['item_name'];
                $item_number=$ordgrey['item_number'];
                $item_id=$ordgrey['item_id'];
            }
            $artw=array(
                'artwork_id'=>0,
                'order_id'=>$order_id,
                'mail_id'=>NULL,
                'customer_instruct'=>$ordgrey['order_customer_comment'].$add_instr,
                'user_id'=>$user_id,
                'customer'=>$orderdat['customer_name'],
                'customer_contact'=>$ordgrey['contact_first_name'].' '.$ordgrey['contact_last_name'],
                'customer_phone'=>$ordgrey['contact_phone'],
                'customer_email'=>($orderdat['customer_email']=='' ? $ordgrey['contact_email'] : $orderdat['customer_email']),
                'item_name'=>$item_name,
                'item_number'=>$item_number,
                'item_id'=>$item_id,
                'item_color'=>'',
                'item_qty'=>$ordgrey['item_qty'],
            );
            if ($orderdat['item_id']==$this->config->item('other_id') || $orderdat['item_id']==$this->config->item('multy_id') || $orderdat['item_id']==$this->config->item('custom_id')) {
                $artw['other_item']=$orderdat['order_items'];
            }
            $art_id=$this->artwork_update($artw);

            if ($art_id) {
                $num_arts=0;
                foreach ($arts as $row) {
                    $fl_upd=0;
                    if ($row['art_type']=='Logo') {
                        $logopath=  str_replace($artshort_path, $artfull_path, $row['logo_src']);
                        $content=@file_get_contents($logopath);
                        if ($content) {
                            $srcname=str_replace($artshort_path, '', $row['logo_src']);
                            $file_det=extract_filename($srcname);
                            $newname=$orderdat['order_num'].'_'.$row['art_ordnum'].'.'.$file_det['ext'];
                            $destname=$artfull_path.$newname;
                            copy($logopath, $destname);
                            $row['logo_src']=$artshort_path.$newname;
                            $row['redrawvect']=1;
                            $row['redraw_time']=time();
                            $fl_upd=1;
                        }
                    } else {
                        $fl_upd=1;
                        $row['redrawvect']=0;
                    }
                    if ($fl_upd==1) {
                        $row['artwork_id']=$art_id;
                        $this->artlocation_update($row);
                        $num_arts++;
                    }
                }
                /* If we add ARTS && order is not blank */
                if ($num_arts>0 && $blank_order==0) {
                    $this->db->set('order_art',1);
                    $this->db->set('order_art_update',  time());
                    $this->db->where('order_id',$order_id);
                    $this->db->update('ts_orders');
                }

            }
            $this->db->select('a.*, m.proof_num, o.order_num');
            $this->db->from('ts_artworks a');
            $this->db->join('ts_emails m','m.email_id=a.mail_id','left');
            $this->db->join('ts_orders o','o.order_id=a.order_id','left');
            $this->db->where('a.artwork_id',$art_id);
            $return_array=$this->db->get()->row_array();
            $return_array['artwork_blank']=$orderdat['order_blank'];
            /* Insert into history data about Create Order */
            if ($online==1) {
                $msg='Order #'.$orderdat['order_num'].' was created '.date('m/d/Y',$orderdat['order_date']).' online by customer.';
            } else {
                $usrnote=' manually '.($orderdat['userwhocreate']=='' ? '' : 'by '.$orderdat['userwhocreate']);
                $msg='Order #'.$orderdat['order_num'].' was created '.date('m/d/Y',$orderdat['order_date']).$usrnote;
            }

            $this->db->set('artwork_id',$art_id);
            // $this->db->set('created_time',$orderdat['order_date']);
            $this->db->set('message',$msg);
            $this->db->insert('ts_artwork_history');
            /* Make this after first save */
            $return_array['art_history']=$this->get_artmsg_history($art_id);
        } else {
            $this->db->select('a.*, m.proof_num, o.order_num, o.order_blank as artwork_blank');
            $this->db->from('ts_artworks a');
            $this->db->join('ts_emails m','m.email_id=a.mail_id','left');
            $this->db->join('ts_orders o','o.order_id=a.order_id','left');
            $this->db->where('a.artwork_id',$artw_id);
            $return_array=$this->db->get()->row_array();
            /* Get History */
            $return_array['art_history']=$this->get_artmsg_history($artw_id);
        }
        return $return_array;
    }

    /* Artwork Update */
    public function artwork_update($artw) {
        $this->db->set('order_id',$artw['order_id']);
        $this->db->set('mail_id',$artw['mail_id']);
        if (isset($artw['customer_instruct'])) {
            $this->db->set('customer_instruct',$artw['customer_instruct']);
        }
        if (isset($artw['customer'])) {
            $this->db->set('customer',$artw['customer']);
        }
        if (isset($artw['customer_contact'])) {
            $this->db->set('customer_contact',$artw['customer_contact']);
        }
        if (isset($artw['customer_phone'])) {
            $this->db->set('customer_phone', $artw['customer_phone']);
        }
        if (isset($artw['customer_email'])) {
            $this->db->set('customer_email',$artw['customer_email']);
        }
        if (isset($artw['item_name'])) {
            $this->db->set('item_name',$artw['item_name']);
        }
        if (isset($artw['other_item'])) {
            $this->db->set('other_item',$artw['other_item']);
        }
        if (isset($artw['item_id'])) {
            $this->db->set('item_id',$artw['item_id']);
        }
        if (isset($artw['item_number'])) {
            $this->db->set('item_number',$artw['item_number']);
        }
        if (isset($artw['item_color'])) {
            $this->db->set('item_color',$artw['item_color']);
        }
        if (isset($artw['item_qty']) ) {
            if (!empty($artw['item_qty'])) {
                $this->db->set('item_qty',$artw['item_qty']);
            }
        }
        if (isset($artw['artwork_rush'])) {
            $this->db->set('artwork_rush',$artw['artwork_rush']);
        }
        if (isset($artw['artwork_note'])) {
            $this->db->set('artwork_note',$artw['artwork_note']);
        }
        if (isset($artw['other_item'])) {
            $this->db->set('other_item', $artw['other_item']);
        }
        $this->db->set('user_updated',$artw['user_id']);
        if ($artw['artwork_id']==0) {
            $this->db->set('user_created',$artw['user_id']);
            $this->db->set('time_create',date('Y-m-d H:i:s'));
            $this->db->insert('ts_artworks');
            $res=$this->db->insert_id();
            if ($res==0) {
                return FALSE;
            } else {
                /* If exist update MSG */
                $artw['artwork_id']=$res;
            }
        } else {
            $this->db->where('artwork_id',$artw['artwork_id']);
            $this->db->update('ts_artworks');
        }
        if (isset($artw['update_msg']) && $artw['update_msg']) {
            /* Create record in History */
            $this->artwork_history_update($artw);
        }
        return $artw['artwork_id'];
    }

    /* get history */
    public function get_artmsg_history($artwork_id) {
        $this->db->select('ah.artwork_history_id, ah.created_time, ah.message, u.user_name, u.user_leadname, ah.parsed_mailbody, ah.message_details');
        $this->db->select('a.order_id, o.create_date as order_date, a.mail_id, e.email_date');
        $this->db->from('ts_artwork_history ah');
        $this->db->join('users u','u.user_id=ah.user_id','left');
        $this->db->join('ts_artworks a','a.artwork_id=ah.artwork_id');
        $this->db->join('ts_orders o','o.order_id=a.order_id','left');
        $this->db->join('ts_emails e','e.email_id=a.mail_id','left');
        $this->db->where('ah.artwork_id',$artwork_id);
        $this->db->order_by('ah.created_time','desc');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            // Get Details
            $this->db->select('count(*) as cnt');
            $this->db->from('ts_artwork_historydetails');
            $this->db->where('artwork_history_id', $row['artwork_history_id']);
            $detres=$this->db->get()->row_array();
            $row['history_head']='';
            $row['out_subdate']=$row['out_date']='&nbsp;';
            if ($row['created_time']!=0) {
                $create_time=$row['created_time'];
            } else {
                if (empty($row['mail_id'])) {
                    $create_time=$row['order_date'];
                } else {
                    $create_time=strtotime($row['email_date']);
                    if (!empty($row['order_id']) && $row['order_date']<$create_time) {
                        $create_time=$row['order_date'];
                    }
                }
            }
            $row['history_head']=($row['user_name']=='' ? 'System' : $row['user_name']).','.date('m/d/y g:i:s a',$create_time);
            $row['out_date']=date('D - M d, Y',$create_time);
            if (!empty($row['user_leadname'])) {
                $row['out_subdate']=date('g:i a',$create_time).' - '.$row['user_leadname'];
            } elseif (!empty($row['user_name'])) {
                $row['out_subdate']=date('g:i a',$create_time).' - '.$row['user_name'];
            } else {
                $row['out_subdate']=date('g:i a',$create_time).' - System';
            }
            $row['parsed_lnk']='';
            $row['parsed_class']='';
            $row['title']='';
            if ($row['parsed_mailbody']!='') {
                $row['parsed_lnk']='<img alt="Parser" src="/img/art/parsed.png"/>';
                $row['parsed_class']='parsedproofrequest';
                $row['title']=$row['parsed_mailbody'];
            }
            if ($row['message_details']) {
                $row['title']=$row['message_details'];
            }
            if ($detres['cnt']>0) {
                $msg=$row['message'].PHP_EOL.'<br/>';
                $msg.='<a class="historydetailsview" href="javascript: void(0);" data-history="'.$row['artwork_history_id'].'">View all details</a>';
                $row['message']=$msg;
            }
            $out[]=$row;
        }
        return $out;
    }

    /* Art Messages History */
    public function artwork_history_update($artw) {
        $this->db->set('artwork_id',$artw['artwork_id']);
        $this->db->set('user_id',$artw['user_id']);
        $this->db->set('created_time', time());
        $this->db->set('message',$artw['update_msg']);
        $this->db->insert('ts_artwork_history');
        return TRUE;
    }

    public function get_needaprovelnk($artwork_id) {
        $this->db->select('pf.proof_name');
        $this->db->from('ts_artwork_proofs pf');
        $this->db->where('pf.artwork_id', $artwork_id);
        $this->db->where('sended',1);
        $lnkarray=$this->db->get()->result_array();
        if (count($lnkarray)==0) {
            return '';
        } else {
            $lnks=array();
            $path=$this->config->item('artwork_proofs_relative');
            $proofurl=$this->config->item('prooflnk');
            foreach ($lnkarray as $row) {
                $newlnk=$proofurl.str_replace($path, '', $row['proof_name']);
                $lnks[]=$newlnk;
            }
            if (count($lnks)==1) {
                $message='Below you will find a link to your art proof.  Please click on the link to view it:'.PHP_EOL;
                $message.=''.PHP_EOL;
                $message.=$lnks[0];
            } else {
                $message='Below you will find links to your art proofs.  Please click on each link to view the different pages:'.PHP_EOL;;
                $message.=''.PHP_EOL;
                foreach ($lnks as $row) {
                    $message.=$row.PHP_EOL;
                }
            }
            return $message;
        }
    }

    public function get_artproofs($artwork_id) {
        $this->db->select('*');
        $this->db->from('ts_artwork_proofs');
        $this->db->where('artwork_id',$artwork_id);
        $this->db->order_by('artwork_proof_id');
        $res=$this->db->get()->result_array();
        $out=array();
        $path_sh=$this->config->item('artwork_proofs_relative');
        $proofnum=1;
        $approvenum=1;
        foreach ($res as $row) {
            $row['out_approved']='';
            $row['approve_class']='';
            $row['dellink']='';
            // if ($row['sended']==0) {
            $row['dellink']='<div data-proofid="'.$row['artwork_proof_id'].'" data-artworkid="'.$row['artwork_id'].'" class="artpopup_artredcirkle removeproof">&nbsp;</div>';
            // }
            $row['src']=$row['proof_name'];
            $newname=str_replace($path_sh, '', $row['proof_name']);
            $row['proof_name']=$newname;
            $row['out_approved']='<img src="/img/art/artpopup_whitestar.png" alt="proof"/>';
            $row['approve_class']='proofnotapproved';
            $row['out_proofname']='proof_'.str_pad($proofnum, 2, '0', STR_PAD_LEFT);
            $proofnum++;
            $row['out_apprname']='';
            $row['senddoc']=0;
            if ($row['approved']==1) {
                $row['out_apprname']='approved_'.str_pad($approvenum, 2, '0', STR_PAD_LEFT);
                $approvenum++;
                $row['approve_class']='proofapproved';
                $row['out_approved']='<img src="/img/art/artpopup_greenstar.png" alt="proof"/>';
            }
            $row['deleted']='';
            $out[]=$row;
        }
        return $out;
    }

    public function send_reminder($data, $attach, $user_id) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_MSG);
        if (empty($data['from'])) {
            $out['msg']='Enter Sender Email';
            return $out;
        } elseif (empty($data['customer_email'])) {
            $out['msg']='Enter Customer Email';
            return $out;
        } elseif (empty($data['subject'])) {
            $out['msg']='Enter Message Subject';
            return $out;
        } elseif (empty($data['message'])) {
            $out['msg']='Enter Message Body';
            return $out;
        } else {
            $from_array=explode(',', $data['customer_email']);
            foreach ($from_array as $row) {
                if (!valid_email_address(trim($row))) {
                    $out['msg']='Customer Email Address '.$row.' is Not Valid';
                    return $out;
                }
            }
            if ($data['cc']!='') {
                $ccarray=  explode(',', $data['cc']);
                foreach ($ccarray as $row) {
                    if (!valid_email_address(trim($row))) {
                        $out['msg']='Email CC '.$row.' is Not Valid';
                        return $out;
                    }
                }
            }
            // Send message
            $path_fl=$this->config->item('artwork_proofs');
            $this->load->library('email');
            $config['protocol'] = 'sendmail';
            $config['charset'] = 'utf8';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'text';

            $this->email->initialize($config);

            $this->email->from($data['from']);
            if ($data['cc']!='') {
                $this->email->cc($data['cc']);
            }
            $this->email->to($data['customer_email']);

            $this->email->subject($data['subject']);
            $this->email->message($data['message']);
            $data['msg_details']=NULL;
            if (count($attach)>0) {
                $details='';
                $data['history_msg'].=' '.count($attach).' attachments';
                foreach ($attach as $row) {
                    // if (file_exists($row)) {
                    //            $this->email->attach($row);
                    $details.=str_replace($path_fl, '', $row).'<br/>'.PHP_EOL;
                    // }
                }
                $data['msg_details']=$details;
            }
            $this->email->send();
            // $msgresult=$this->email->print_debugger();
            $this->email->clear(TRUE);
            $logoptions=array(
                'from'=>$data['from'],
                'to'=>$data['customer_email'],
                'subject'=>$data['subject'],
                'message'=>$data['message'],
                // 'result'=>$msgresult,
                'user_id'=>$user_id,
            );
            if (!empty($data['cc'])) {
                $logoptions['cc']=$data['cc'];
            }
            if (count($attach)>0) {
                $logoptions['attachments']=$attach;
            }
            $this->load->model('email_model');
            $this->email_model->logsendmail($logoptions);
            // Insert into history message about send Reminder
            $this->db->set('artwork_id',$data['artwork_id']);
            $this->db->set('user_id',$user_id);
            $this->db->set('created_time',time());
            $this->db->set('message',$data['history_msg']);
            $this->db->set('message_details', $data['msg_details']);
            $this->db->insert('ts_artwork_history');

            $out['result']= $this->success_result;
            $out['msg']='';
        }
        return $out;
    }

    public function get_item_details($options) {
        $this->db->select("item_name, item_number, item_id",FALSE);
        $this->db->from('v_itemsearch');
        if (isset($options['item_num'])) {
            $this->db->where('item_number', $options['item_num']);
        }
        if (isset($options['item_name'])) {
            $this->db->where('item_name',$options['item_name']);
        }
        if (isset($options['item_id'])) {
            $this->db->where('item_id',$options['item_id']);
        }
        $result=$this->db->get()->result_array();
        return $result;
    }

    /* List of Items */
    public function get_items_list() {
        $this->db->select('*');
        $this->db->from('v_itemsearch');
        $this->db->order_by('item_number');
        $res=$this->db->get()->result_array();
        $out=array();
        foreach ($res as $row) {
            if ($row['item_id']>1) {
                $row['item_list']=$row['item_name'].' / '.$row['item_number'];
            } else {
                $row['item_list']=$row['item_name'];
            }
            $out[]=array(
                'item_id'=>$row['item_id'],
                'item_name'=>$row['item_list'],
            );
        }
        return $out;
    }

    /* Locations */
    function get_art_locations($artwork_id, $artsession='') {
        $this->db->select('a.*,art.order_id, art.mail_id, ord.order_num, proof.proof_num');
        $this->db->from('ts_artwork_arts a');
        $this->db->join('ts_artworks art','art.artwork_id=a.artwork_id');
        $this->db->join('ts_orders ord','ord.order_id=art.order_id','left');
        $this->db->join('ts_emails proof','proof.email_id=art.mail_id','left');
        $this->db->where('a.artwork_id',$artwork_id);
        $results=$this->db->get()->result_array();

        $return_array=array();

        $empty_icon='<img src="/img/artpage/white_square.png"/>';

        foreach ($results as $row) {
            $row['artlabel']=$row['art_ordnum'].'.'.($row['art_type']=='Reference' ? 'Refer' : $row['art_type']);
            $row['redrawchk']=$row['rushchk']=$row['redochk']='&nbsp;';
            if ($row['logo_vectorized']) {
                if ($row['art_type']!='Repeat') {
                    $row['redochk']='<input type="checkbox" class="artundo" data-artworkartid="'.$row['artwork_art_id'].'" value="1" />';
                }
            } else {
                if ($row['redrawvect']) {
                    $row['redrawchk']='<input type="checkbox" class="artredraw" data-artworkartid="'.$row['artwork_art_id'].'" checked="checked" value="1" />';
                } else {
                    if (($row['art_type']=='Logo' || $row['art_type']=='Reference') && !$row['logo_vectorized']) {
                        $row['redrawchk']='<input type="checkbox" class="artredraw" data-artworkartid="'.$row['artwork_art_id'].'" checked="checked" value="1" />';
                    } elseif ($row['art_type']=='Text') {
                        $row['redrawchk']='<input type="checkbox" class="artredraw" data-artworkartid="'.$row['artwork_art_id'].'" value="1" />';
                    }
                }
            }
            if ($row['rush']==1) {
                $chk='checked="checked"';
            } else {
                $chk='';
            }
            $row['rushchk']='<input type="checkbox" class="artrush" data-artworkartid="'.$row['artwork_art_id'].'" value="1" '.$chk.'/>';
            $src='&nbsp;';
            $vec='&nbsp;';
            $row['texticon']='';
            $row['redrawicon']=$empty_icon;
            $row['logo_srcpath']=$row['logo_vectorizedpath']='';
            $row['location_state']='source';
            $row['imagesourceclass']=$row['imagesourceview']='';
            $path_sh=$this->config->item('artwork_logo_relative');
            if (!empty($row['logo_src'])) {
                $sourcedet=extract_filename($row['logo_src']);
                if (in_array($sourcedet['ext'], $this->logo_imageext)) {
                    $viewurl='/artproofrequest/viewartsource?id='.$row['artwork_art_id'];
                    if (!empty($artsession)) {
                        $viewurl.='&artsession='.$artsession;
                    }
                    $row['imagesourceclass']='viewsource';
                    $row['imagesourceview']=$viewurl;
                }
            }
            if (!empty($row['logo_vectorized'])) {
                $vectordet=extract_filename($row['logo_vectorized']);
            }
            if ($row['art_type']=='Logo' || $row['art_type']=='Reference') {
                if ($row['logo_vectorized']) {
                    $row['location_state']='redrawn';
                } else {
                    if (!empty($row['logo_src'])) {
                        $row['redrawvect']=1;
                        $logodet=extract_filename($row['logo_src']);
                        if (in_array($logodet['ext'], $this->nonredrawn)) {
                            $row['location_state']='source_alert';
                        }
                    }
                }
                $vec=$src='';
                if (!empty($row['logo_src'])) {
                    $row['logo_srcpath']=$row['logo_src'];
                    // $src=str_replace($path_sh,'',$row['logo_src']);
                    $src=($row['order_num']=='' ? 'pr_'.$row['proof_num'] : $row['order_num']);
                    $src.='_'.str_pad($row['art_ordnum'], 2, '0', STR_PAD_LEFT).'.'.$sourcedet['ext'];
                }
                if (!empty($row['logo_vectorized'])) {
                    $row['logo_vectorizedpath']=$row['logo_vectorized'];
                    // $vec=str_replace($path_sh, '', $row['logo_vectorized']);
                    $vec=($row['order_num']=='' ? 'pr_'.$row['proof_num'] : $row['order_num']);
                    $vec.='_'.str_pad($row['art_ordnum'], 2, '0', STR_PAD_LEFT).'.'.$vectordet['ext'];
                }
                $row['logo_src']=$src;
                $row['logo_vectorized']=$vec;
            } else {
                if ($row['redrawvect']==1 && !$row['logo_vectorized']) {
                    $row['location_state']='source';
                } else {
                    $row['location_state']='redrawn';
                }

                if (!empty($row['logo_vectorized'])) {
                    $row['logo_vectorizedpath']=$row['logo_vectorized'];
                    $vec=str_replace($path_sh, '', $row['logo_vectorized']);
                }
                $row['logo_vectorized']=$vec;
                if ($row['customer_text']) {
                    $row['texticon']='<img src="/img/artpage/artstatus_icon.png" alt="User Text" data-content="'.$row['customer_text'].'"/>';
                } else {
                    $row['texticon']=$empty_icon;
                }
            }
            if ($row['redraw_message']) {
                $row['redrawicon']='<img src="/img/artpage/artstatus_icon.png" alt="User Texe" data-content="'.$row['redraw_message'].'"/>';;
            }
            $row['deleted']='';
            $return_array[]=$row;
        }
        return $return_array;
    }

    function get_location_imprint($item_id) {
        $out=array();
        $out[]=array(
            'key'=>'',
            'value'=>'',
        );
        if ($item_id) {
            $dbtablename='sb_item_inprints';
            $this->db->select('item_inprint_location');
            $this->db->from($dbtablename);
            $this->db->where('item_inprint_item',$item_id);
            $result=$this->db->get()->result_array();
            foreach ($result as $row) {
                $out[]=array(
                    'key'=>$row['item_inprint_location'],
                    'value'=>$row['item_inprint_location'],
                );
            }
        }
        return $out;
    }

    function colordat_prepare($loc, $imprint_colors) {
        $colordat=array();
        $colordat['artwork_art_id']=$loc['artwork_art_id'];
        $colordat['color1_title']='';
        $colordat['color1_style']='emptycolor';
        $colordat['color2_title']='';
        $colordat['color2_style']='emptycolor';
        $colordat['color3_title']='';
        $colordat['color3_style']='emptycolor';
        $colordat['color4_title']='';
        $colordat['color4_style']='emptycolor';

        if ($loc['art_color1']!='') {
            foreach ($imprint_colors as $colrow) {
                if ($colrow['name']==$loc['art_color1']) {
                    $colordat['color1_title']='title="'.$colrow['name'].'"';
                    $colordat['color1_style']=$colrow['class'];
                    break;
                }
            }
        }
        if ($loc['art_color2']!='') {
            foreach ($imprint_colors as $colrow) {
                if ($colrow['name']==$loc['art_color2']) {
                    $colordat['color2_title']='title="'.$colrow['name'].'"';
                    $colordat['color2_style']=$colrow['class'];
                    break;
                }
            }
        }
        if ($loc['art_color3']!='') {
            foreach ($imprint_colors as $colrow) {
                if ($colrow['name']==$loc['art_color3']) {
                    $colordat['color3_title']='title="'.$colrow['name'].'"';
                    $colordat['color3_style']=$colrow['class'];
                    break;
                }
            }
        }
        if ($loc['art_color4']!='') {
            foreach ($imprint_colors as $colrow) {
                if ($colrow['name']==$loc['art_color4']) {
                    $colordat['color4_title']='title="'.$colrow['name'].'"';
                    $colordat['color4_style']=$colrow['class'];
                    break;
                }
            }
        }
        return $colordat;
    }



    private function artworkExist($options) {
        if (empty($options)) {
            return FALSE;
        }
        $this->db->select('artwork_id');
        $this->db->from('ts_artworks');
        if (isset($options['proof_id']) && $options['proof_id']) {
            $this->db->where('mail_id',$options['proof_id']);
        }
        if (isset($options['order_id']) && $options['order_id']) {
            $this->db->where('order_id',$options['order_id']);
        }
        $res=$this->db->get()->row_array();
        if (!isset($res['artwork_id'])) {
            $retval=0;
        } else {
            $retval=$res['artwork_id'];
        }
        return $retval;
    }

    public function save_artdata($data, $artdata, $user_id, $artsession) {
        $out=array('result'=>$this->error_result, 'msg'=>  $this->INIT_MSG);
        $this->load->model('artproof_model');
        $redraw_logos=array();
        if (empty($artdata['customer_name'])) {
            $out['msg']='Enter Customer Name';
        } elseif (empty($artdata['customer_email'])) {
            $out['msg']='Enter Customer Email';
        } elseif (empty($artdata['item_name'])) {
            $out['msg']='Please select an item first. Your changes cannot be saved until you do this.';
        } else {
            $out['msg']='Test Save';
            /* Check - Order was assigned or not */
            $assign_order=0;
            if ($artdata['artwork_id']!=0) {
                /* Select previous data about order */
                $this->db->select('order_id, mail_id');
                $this->db->from('ts_artworks');
                $this->db->where('artwork_id',$artdata['artwork_id']);
                $oldart=$this->db->get()->row_array();
                if ($oldart['order_id']==0 && $oldart['mail_id']!=0 && $artdata['order_id']!=0) {
                    $assign_order=1;
                }
            }
            $oldartwork_id=0;
            if ($assign_order) {
                $this->db->select('artwork_id');
                $this->db->from('ts_artworks');
                $this->db->where('order_id',$artdata['order_id']);
                $artwdat=$this->db->get()->row_array();
                if (isset($artwdat['artwork_id'])) {
                    $oldartwork_id=$artwdat['artwork_id'];
                }
            }

            $artwork=array(
                'artwork_id'=>$artdata['artwork_id'],
                'order_id'=>($artdata['order_id']==0 ? NULL : $artdata['order_id']),
                'mail_id'=>($artdata['proofs_id']==0 ? NULL : $artdata['proofs_id']),
                'update_msg'=>NULL,
                'artwork_rush'=>$artdata['rush'],
                'customer'=>$artdata['customer_name'],
                'customer_contact'=>$artdata['contact'],
                'customer_phone'=>$artdata['customer_phone'],
                'customer_email'=>$artdata['customer_email'],
                'item_name'=>$artdata['item_name'],
                'item_number'=>$artdata['item_num'],
                'artwork_note'=>$artdata['notes'],
                'item_color'=>$artdata['item_color'],
                'item_qty'=>$artdata['item_qty'],
                'item_id'=>$artdata['item_id'],
                'other_item'=>$artdata['other_item'],
                'customer_instruct'=>$artdata['customer_instruct'],
            );

            if ($artdata['update_msg']) {
                $artwork['update_msg']=$artdata['update_msg'];
            }
            $artwork['user_id']=$user_id;

            $artwork_id=$this->artwork_update($artwork);
            $oldproofdocs=$this->get_artproofs($artwork_id);
            /* update Proof & Order data */
            if (intval($artdata['order_id'])==0) {
                /* Update Proofs */
                $proof_dat=array(
                    'email_id'=>$artdata['proofs_id'],
                    'proof_rush'=>$artdata['rush'],
                    'email_sender'=>$artdata['customer_name'],
                    'email_printing'=>$artdata['contact'],
                    'email_senderphone'=>$artdata['customer_phone'],
                    'email_sendermail'=>$artdata['customer_email'],
                    'email_item_number'=>$artdata['item_num'],
                    'email_questions'=>$artdata['notes'],
                    'email_special_requests'=>$artdata['item_color'],
                    'email_qty'=>$artdata['item_qty'],
                    'email_item_id'=>$artdata['item_id'],
                );
                if ($artdata['item_name']=='Other' || $artdata['item_name']=='Multiple' || $artdata['item_name']=='Custom Shaped Stress Balls') {
                    if ($artdata['other_item']) {
                        $proof_dat['email_item_name']=$artdata['other_item'];
                    } else {
                        $proof_dat['email_item_name']=$artdata['item_name'];
                    }
                } else {
                    $proof_dat['email_item_name']=$artdata['item_name'];
                }
                $this->proof_update($proof_dat);
            } else {
                /* Update Orders */
                if ($assign_order) {
                    /* Proof was assigned with Orders */
                    $this->assign_order($artdata['proofs_id'],$artdata['order_id'],$oldartwork_id, $artdata['artwork_id']);

                    $proof_dat=array(
                        'email_status'=>$this->order_status,
                        'email_id'=>$artdata['proofs_id'],
                    );
                    $this->proof_update($proof_dat);
                    // Order #, Mail NUM
                    $ordpref=$artdata['order_num'];
                    $mailpref='pr'.$artdata['proof_num'];
                    $orddocpref=$artdata['order_num'];
                    $maildocpref='proof_'.$artdata['proof_num'];
                    /* Proofs */
                    $idxproof=0;
                    foreach ($artdata['proofs'] as $prrow) {
                        $namedoc=$prrow['proof_name'];
                        $newname=str_replace($maildocpref, $orddocpref, $namedoc);
                        $artdata['proofs'][$idxproof]['proof_name']=$newname;
                        $idxproof++;
                    }
                }
                $orddata=array(
                    'order_rush'=>$artdata['rush'],
                    'order_id'=>$artdata['order_id'],
                    'order_blank'=>$artdata['blank'],
                );
                if ($artdata['item_name']=='Other' || $artdata['item_name']=='Multiple' || $artdata['item_name']=='Custom Shaped Stress Balls') {
                    if ($artdata['other_item']) {
                        $orddata['order_items']=$artdata['other_item'];
                    } else {
                        $orddata['order_items']=$artdata['item_name'];
                    }
                } else {
                    $orddata['order_items']=$artdata['item_name'];
                }
            }
            $i=1;
            /* Update Locations */
            $locations=$artdata['locations'];
            $this->save_artdatalocations($locations, $artwork_id);
            /* Save Proofs */
            // $idxproof=0;
            $path_prooffull=$this->config->item('artwork_proofs');
            $path_proofsh=$this->config->item('artwork_proofs_relative');
            $path_full=$this->config->item('upload_path_preload');
            $path_sh=$this->config->item('pathpreload');
            createPath($path_proofsh);
            foreach ($artdata['proofs'] as $prow) {
                /* Analyse row*/
                $proof=array();
                if ($prow['artwork_proof_id']<0) {
                    $chkfilescr=str_replace($path_sh,$path_full,$prow['src']);
                    if (!file_exists($chkfilescr)) {
                        if ($prow['deleted']==0) {
                            $this->artproof_model->add_proofdoc_log($artwork_id, $user_id, $prow['src'], $prow['source_name'], 'Lost Upload (Save)');
                        }
                        $prow['deleted']=1;
                    }
                }
                if ($prow['deleted']=='') {
                    if ($prow['artwork_proof_id']<0) {
                        // $srclocation=str_replace($path_sh,$path_full,$prow['src']);
                        // New rec
                        $proof['artwork_proof_id']=0;
                        /* rebuild Doc src */
                        $proofsrc=$prow['src'];
                        $proofname=$prow['proof_name'];
                        $srclocation=str_replace($path_sh,$path_full,$proofsrc);
                        $newlocation=$path_prooffull.$proofname;
                        @copy($srclocation, $newlocation);
                        @unlink($srclocation);
                        $newsrc=$path_proofsh.$proofname;
                        $proof['proof_name']=$newsrc;
                    } else {
                        $proof['artwork_proof_id']=$prow['artwork_proof_id'];
                        $proofsrc=$prow['src'];
                        $proofname=$prow['proof_name'];
                        if (str_replace($path_proofsh,'',$proofsrc)!=$proofname) {
                            // Need to rename
                            $srclocation=str_replace($path_proofsh,$path_prooffull,$proofsrc);
                            $newlocation=$path_prooffull.$proofname;
                            @copy($srclocation, $newlocation);
                            @unlink($srclocation);
                            $newsrc=$path_proofsh.$proofname;
                            $proof['proof_name']=$newsrc;
                        }
                    }
                    $proof['proof_ordnum']=$prow['proof_ordnum'];
                    $proof['source_name']=$prow['source_name'];
                    $proof['artwork_id']=$artwork_id;
                    if ($prow['approved']==1) {
                        $proof['approved']=1;
                        $proof['approved_time']=time();
                    } else {
                        $proof['approved']=0;
                        $proof['approved_time']=0;
                    }
                    $res=$this->save_proofdat($proof, $user_id);
                    // Save log
                    $this->artproof_model->add_proofdoc_log($artwork_id, $user_id, $prow['src'], $prow['source_name'], 'Save ProofDoc (Save)');
                } else {
                    if ($prow['artwork_proof_id']>0) {
                        /* Delete */
                        $this->db->where('artwork_proof_id',$prow['artwork_proof_id']);
                        $this->db->delete('ts_artwork_proofs');
                        $proofsrc=$prow['src'];
                        $srclocation=str_replace($path_proofsh,$path_prooffull,$proofsrc);
                        @unlink($srclocation);
                        $this->artproof_model->add_proofdoc_log($artwork_id, $user_id, $prow['src'], $prow['source_name'], 'Delete ProofDoc (Save)');
                    }
                }
            }
            // Clean session
            usersession($artsession,NULL);
            $out['result']=  $this->success_result;
            $out['msg']='';
            // Art in redraw stage
            $cntnonvect=$this->artwork_chktext($artwork_id, 'VECTORED')+$this->artwork_chklogo($artwork_id, 'VECTORED');
            $rushnote=0;
            $rush_msgtxt='';
            if ($cntnonvect!=0 && $artdata['oldrush']!=$artdata['rush']) {
                $rushnote=1;
                $rush_msgtxt=($artdata['rush']==1 ? 'Rush' : 'Standard');
            }
            if (count($redraw_logos)>0 || $rushnote==1) {
                $this->artlogo_notification($redraw_logos, $artwork_id, $rush_msgtxt);
            }
            $blank=0;

            $this->db->select('o.order_blank');
            $this->db->from('ts_artworks aw');
            $this->db->join('ts_orders o','o.order_id=aw.order_id','left');
            $this->db->where('aw.artwork_id',$artwork_id);
            $res1=$this->db->get()->row_array();
            if ($res1['order_blank']=='1') {
                $blank=1;
            }
            if ($blank==1) {
                $this->art_blank_changestage($data, $artdata, $artwork_id, $user_id);
            } else {
                $this->art_common_changestage($data, $artdata, $artwork_id,$user_id);
            }
            if ($assign_order) {
                $this->_prepare_sync($artdata, $oldproofdocs, $user_id);
            }

        }
        return $out;
    }

    private function assign_order($proof_id, $order_id, $oldartwork_id, $newartid) {
        /* change name of logos */
        $this->db->select('e.email_sender,e.email_item_name,
            e.email_art, e.email_art_update, e.email_redrawn, e.email_redrawn_update, e.email_vectorized, e.email_vectorized_update, e.email_proofed,
            e.email_proofed_update, e.email_approved, e.email_approved_update, v.order_proj_status');
        $this->db->from('ts_emails e');
        $this->db->join('v_order_statuses v','v.order_id=e.email_id and v.status_type="R"','left');
        $this->db->where('e.email_id',$proof_id);
        $res=$this->db->get()->row_array();

        switch ($res['order_proj_status']) {
            case Artwork_model::JUST_APPROVED:
                // $res['email_approved_update']=($res['email_approved_update']<$ordupd ? $ordupd : $res['email_approved_update']);
                $res['email_approved_update']=time();
                break;
            case Artwork_model::NEED_APPROVAL:
                // $res['email_proofed_update']=($res['email_proofed_update']<$ordupd ? $ordupd : $res['email_proofed_update']);
                $res['email_proofed_update']=time();
                break;
            case Artwork_model::TO_PROOF:
                // $res['email_vectorized_update']=($res['email_vectorized_update']<$ordupd ? $ordupd : $res['email_vectorized_update']);
                $res['email_vectorized_update']=time();
                break;
            case Artwork_model::NO_VECTOR:
                // $res['email_redrawn_update']=($res['email_redrawn_update']<$ordupd ? $ordupd : $res['email_redrawn_update']);
                $res['email_redrawn_update']=time();
                break;
            case Artwork_model::REDRAWN:
                // $res['email_art_update']=($res['email_art_update']<$ordupd ? $ordupd : $res['email_art_update']);
                $res['email_art_update']=time();
                break;
            case "":
                // $res['email_approved_update']=($res['email_approved_update']<$ordupd ? $ordupd : $res['email_approved_update']);
                $res['email_approved_update']=time();
                break;
            default :
                break;
        }
        /* Update order */
        $this->db->where('order_id',$order_id);
        $this->db->set('order_art',$res['email_art']);
        $this->db->set('order_art_update',$res['email_art_update']);
        $this->db->set('order_redrawn',$res['email_redrawn']);
        $this->db->set('order_redrawn_update',$res['email_redrawn_update']);
        $this->db->set('order_vectorized',$res['email_vectorized']);
        $this->db->set('order_vectorized_update',$res['email_vectorized_update']);
        $this->db->set('order_proofed',$res['email_proofed']);
        $this->db->set('order_proofed_update',$res['email_proofed_update']);
        $this->db->set('order_approved',$res['email_approved']);
        $this->db->set('order_approved_update',$res['email_approved_update']);
        $this->db->update('ts_orders');
        // Update status
        $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
        $this->db->from('ts_orders');
        $this->db->where('order_id',$order_id);
        $statres=$this->db->get()->row_array();
        $this->db->where('order_id',$order_id);
        $this->db->set('order_artview', $statres['aprrovview']);
        $this->db->set('order_placed', $statres['placeord']);
        $this->db->update('ts_orders');

        if ($oldartwork_id) {
            $this->db->select('a.customer_instruct, a.general_notes');
            $this->db->from('ts_artworks a');
            $this->db->where('a.artwork_id', $oldartwork_id);
            $oldart=$this->db->get()->row_array();
            $this->db->where('artwork_id', $oldartwork_id);
            $this->db->set('artwork_id', $newartid);
            $this->db->update('ts_artwork_history');
            $updart=0;
            if (!empty($oldart['customer_instruct'])) {
                $this->db->set('customer_instruct', $oldart['customer_instruct']);
                $updart=1;
            }
            if (!empty($oldart['general_notes'])) {
                $this->db->set('general_notes', $oldart['general_notes']);
                $updart=1;
            }
            if ($updart==1) {
                $this->db->where('artwork_id', $newartid);
                $this->db->update('ts_artworks');
            }
            $this->db->where('artwork_id',$oldartwork_id);
            $this->db->delete('ts_artworks');
        }
    }

    public function proof_update($proofdat) {
        if (isset($proofdat['email_status'])) {
            $this->db->set('email_status',$proofdat['email_status']);
        }
        if (isset($proofdat['proof_rush'])) {
            $this->db->set('proof_rush',$proofdat['proof_rush']);
        }
        if (isset($proofdat['email_sender'])) {
            $this->db->set('email_sender',$proofdat['email_sender']);
        }
        if (isset($proofdat['email_printing'])) {
            $this->db->set('email_printing',$proofdat['email_printing']);
        }
        if (isset($proofdat['email_senderphone'])) {
            $this->db->set('email_senderphone',$proofdat['email_senderphone']);
        }
        if (isset($proofdat['email_sendermail'])) {
            $this->db->set('email_sendermail',$proofdat['email_sendermail']);
        }
        if (isset($proofdat['email_item_name'])) {
            $this->db->set('email_item_name',$proofdat['email_item_name']);
        }
        if (isset($proofdat['email_item_number'])) {
            $this->db->set('email_item_number',$proofdat['email_item_number']);
        }
        if (isset($proofdat['email_questions'])) {
            $this->db->set('email_questions',$proofdat['email_questions']);
        }
        if (isset($proofdat['email_special_requests'])) {
            $this->db->set('email_special_requests',$proofdat['email_special_requests']);
        }
        if (isset($proofdat['email_qty'])) {
            if (!empty($proofdat['email_qty'])) {
                $this->db->set('email_qty',$proofdat['email_qty']);
            }
        }

        $this->db->where('email_id',$proofdat['email_id']);
        $this->db->update('ts_emails');
        return TRUE;
    }

    public function save_artdatalocations($locations, $artwork_id) {
        $path_fl=$this->config->item('artwork_logo');
        $path_sh=$this->config->item('artwork_logo_relative');
        $preload_path_fl=$this->config->item('upload_path_preload');
        $preload_path_sh=$this->config->item('pathpreload');
        createPath($path_sh);
        foreach ($locations as $loc) {
            $location=array();
            if ($loc['deleted']!='') {
                // Mark logos as deleted
                if ($loc['artwork_art_id']>0) {
                    // We delete previously saved location
                    $this->delete_artlocation($loc['artwork_art_id']);
                }
            } else {
                $location['artwork_id']=$artwork_id;
                if ($loc['artwork_art_id']<=0) {
                    $location['artwork_art_id']=0;
                } else {
                    $location['artwork_art_id']=$loc['artwork_art_id'];
                }
                $location['art_type']=$loc['art_type'];
                $location['art_ordnum']=$loc['art_ordnum'];
                $location['art_numcolors']=$loc['art_numcolors'];
                $location['art_color1']=($loc['art_color1']=='' ? NULL : $loc['art_color1']);
                $location['art_color2']=($loc['art_color2']=='' ? NULL : $loc['art_color2']);
                $location['art_color3']=($loc['art_color3']=='' ? NULL : $loc['art_color3']);
                $location['art_color4']=($loc['art_color4']=='' ? NULL : $loc['art_color4']);
                $location['customer_text']=($loc['customer_text']=='' ? NULL : $loc['customer_text']);
                $location['font']=($loc['font']=='' ? NULL : $loc['font']);
                $location['redraw_message']=$loc['redraw_message'];
                $location['art_location']=($loc['art_location']=='' ? NULL : $loc['art_location']);
                $location['rush']=intval($loc['rush']);
                $location['redrawvect']=intval($loc['redrawvect']);
                $location['redo']=intval($loc['redo']);
                $location['repeat_text']=($loc['repeat_text']=='' ? NULL : $loc['repeat_text']);
                if ($loc['art_type']=='Logo' || $loc['art_type']=='Reference') {
                    /* Prepare art logos */
                    if ($loc['artwork_art_id']<=0) {
                        // New location - a) move file to new location
                        if ($loc['logo_src']!='' && $loc['logo_src']!='&nbsp;') {
                            /* copy */
                            $srcname=str_replace($preload_path_sh, $preload_path_fl,$loc['logo_srcpath']);
                            $destname=$path_fl.$loc['logo_src'];
                            @copy($srcname,$destname);
                            $location['logo_src']=$path_sh.$loc['logo_src'];
                            $location['redraw_time']=time();
                            if ($loc['redrawvect']==0) {
                                // Make source vectorized
                                $location['logo_vectorized']=$path_sh.$loc['logo_src'];
                                $location['vectorized_time']=time();
                            } else {
                                $redraw_logos[]=array(
                                    'logo_src'=>$loc['logo_src'],
                                    'deed'=>'Add',
                                );
                            }
                        }
                    } else {
                        if ($location['redo']==1) {
                            $location['logo_vectorized']='';
                            $location['vectorized_time']=0;
                            $redraw_logos[]=array(
                                'logo_src'=>$loc['logo_src'],
                                'deed'=>'Redo',
                            );
                        }
                    }
                } else {
                    if ($location['redo']==1) {
                        $location['logo_vectorized']='';
                        $location['vectorized_time']=0;
                    }
                    if ($location['redrawvect']==1 && empty($loc['redraw_time'])) {
                        $location['redraw_time']=time();
                    }
                }
                $res=$this->artlocation_update($location);
            }
        } // End locations list
        return TRUE;
    }

    /* Del saved location */
    public function delete_artlocation($artwork_art_id) {
        $this->db->where('artwork_art_id',$artwork_art_id);
        $this->db->delete('ts_artwork_arts');
        return TRUE;
    }

    public function save_proofdat($proofdat, $user_id) {
        $this->db->set('artwork_id',$proofdat['artwork_id']);
        $this->db->set('updated_user',$user_id);
        if (isset($proofdat['proof_name'])) {
            $this->db->set('proof_name',$proofdat['proof_name']);
        }
        if (isset($proofdat['sended'])) {
            $this->db->set('sended',$proofdat['sended']);
        }
        if (isset($proofdat['sended_time'])) {
            $this->db->set('sended_time',$proofdat['sended_time']);
        }
        if (isset($proofdat['approved'])) {
            $this->db->set('approved',$proofdat['approved']);
        }
        if (isset($proofdat['proof_ordnum'])) {
            $this->db->set('proof_ordnum',$proofdat['proof_ordnum']);
        }
        if (isset($proofdat['source_name'])) {
            $this->db->set('source_name',$proofdat['source_name']);
        }
        if (isset($proofdat['approved_time'])) {
            $this->db->set('approved_time',$proofdat['approved_time']);
        }
        if (isset($proofdat['proofdoc_link'])) {
            $this->db->set('proofdoc_link', $proofdat['proofdoc_link']);
        }
        if ($proofdat['artwork_proof_id']==0) {
            $this->db->set('created_user',$user_id);
            $this->db->set('created_time',date('Y-m-d H:i:s'));
            $this->db->insert('ts_artwork_proofs');
            $retval=$this->db->insert_id();
        } else {
            $this->db->where('artwork_proof_id',$proofdat['artwork_proof_id']);
            $this->db->update('ts_artwork_proofs');
            $retval=$proofdat['artwork_proof_id'];
        }
        return $retval;
    }



    /* Location Data Update */
    public function artlocation_update($loc) {
        if (!isset($loc['artwork_id'])) {
            return FALSE;
        } else {
            $fl_upd=0;
            if (isset($loc['artwork_id'])) {
                $this->db->set('artwork_id',$loc['artwork_id']);
                $fl_upd=1;
            }
            if (isset($loc['art_type'])) {
                $this->db->set('art_type',$loc['art_type']);
                $fl_upd=1;
            }
            if (isset($loc['art_ordnum'])) {
                $fl_upd=1;
                $this->db->set('art_ordnum',$loc['art_ordnum']);
            }
            if (isset($loc['logo_src'])) {
                $fl_upd=1;
                $this->db->set('logo_src',$loc['logo_src']);
            }
            if (isset($loc['redraw_time'])) {
                $fl_upd=1;
                $this->db->set('redraw_time',$loc['redraw_time']);
            }
            if (isset($loc['logo_vectorized'])) {
                $fl_upd=1;
                $this->db->set('logo_vectorized',($loc['logo_vectorized']=='' ? NULL : $loc['logo_vectorized']));
            }
            if (isset($loc['vectorized_time'])) {
                $fl_upd=1;
                $this->db->set('vectorized_time',($loc['vectorized_time']==0 ? NULL : $loc['vectorized_time']));
            }
            if (isset($loc['redrawvect'])) {
                $fl_upd=1;
                $this->db->set('redrawvect',$loc['redrawvect']);
            }
            if (isset($loc['rush'])) {
                $fl_upd=1;
                $this->db->set('rush',$loc['rush']);
            }
            if (isset($loc['customer_text'])) {
                $fl_upd=1;
                $this->db->set('customer_text',$loc['customer_text']);
            }
            if (isset($loc['font'])) {
                $this->db->set('font',$loc['font']);
            }
            if (isset($loc['redraw_message'])) {
                $fl_upd=1;
                $this->db->set('redraw_message',$loc['redraw_message']);
            }
            if (isset($loc['redo'])) {
                $fl_upd=1;
                $this->db->set('redo',$loc['redo']);
            }
            if (isset($loc['art_numcolors'])) {
                $fl_upd=1;
                $this->db->set('art_numcolors',intval($loc['art_numcolors']));
            }
            if (isset($loc['art_color1'])) {
                $fl_upd=1;
                $this->db->set('art_color1',$loc['art_color1']);
            }
            if (isset($loc['art_color2'])) {
                $fl_upd=1;
                $this->db->set('art_color2',$loc['art_color2']);
            }
            if (isset($loc['art_color3'])) {
                $fl_upd=1;
                $this->db->set('art_color3',$loc['art_color3']);
            }
            if (isset($loc['art_color4'])) {
                $fl_upd=1;
                $this->db->set('art_color4',$loc['art_color4']);
            }
            if (isset($loc['art_location'])) {
                $fl_upd=1;
                $this->db->set('art_location',$loc['art_location']);
            }
            if (isset($loc['repeat_text'])) {
                $fl_upd=1;
                $this->db->set('repeat_text',$loc['repeat_text']);
            }
            if ($fl_upd==1) {
                if ($loc['artwork_art_id']==0) {
                    $this->db->insert('ts_artwork_arts');
                    $retval=$this->db->insert_id();
                } else {
                    $this->db->where('artwork_art_id',$loc['artwork_art_id']);
                    $this->db->update('ts_artwork_arts');
                    $retval=$loc['artwork_art_id'];
                }
                return $retval;
            } else {
                return FALSE;
            }
        }
    }

    public function artwork_chktext($artwork_id, $type) {
        $this->db->select('count(a.artwork_art_id) as cnt');
        $this->db->from('ts_artwork_arts a');
        $this->db->where('a.artwork_id',$artwork_id);
        $this->db->where('a.art_type','Text');
        if ($type=='REDRAW') {
            $this->db->where('a.redrawvect',1);
            $this->db->where('a.redraw_time',0);
        }
        if ($type=='VECTORED') {
            $this->db->where('a.redrawvect',1);
            $this->db->where('a.vectorized_time',0);
        }
        if ($type=='TOPROOF') {
            $this->db->where('((a.redrawvect=1 and a.vectorized_time > 0 ) or a.redrawvect = 0 )');
            //$this->db->or_where('a.redrawvect',0);
        }
        $res=$this->db->get()->row_array();

        return $res['cnt'];
    }

    public function artwork_chklogo($artwork_id, $type) {
        if ($type=='REDRAW' || $type=='VECTORED' || $type=='ALL' || $type=='TOPROOF') {
            $this->db->select('count(a.artwork_art_id) as cnt');
            $this->db->from('ts_artwork_arts a');
            $this->db->where('a.artwork_id',$artwork_id);
            $this->db->where('a.art_type','Logo');
            if ($type=='REDRAW') {
                $this->db->where('a.redraw_time',0);
            }
            if ($type=='VECTORED') {
                $this->db->where('a.redrawvect',1);
                $this->db->where('a.vectorized_time',0);
            }
            if ($type=='TOPROOF') {
                $this->db->where('a.vectorized_time > ',0);
            }
            $res=$this->db->get()->row_array();
        } elseif ($type=='PROOF_ALL' || $type=='PROOF_SEND' || $type=='PROOF_APPROVED') {
            $this->db->select('count(artwork_proof_id) as cnt');
            $this->db->from('ts_artwork_proofs');
            $this->db->where('artwork_id',$artwork_id);
            if ($type=='PROOF_SEND') {
                $this->db->where('sended',1);
            }
            if ($type=='PROOF_APPROVED') {
                $this->db->where('approved',1);
            }
            $res=$this->db->get()->row_array();
        }
        return $res['cnt'];
    }

    public function artwork_check_repeat($artwork_id, $type) {
        if ($type=='REDRAW' || $type=='VECTORED' || $type=='ALL' || $type=='TOPROOF') {
            $this->db->select('count(a.artwork_art_id) as cnt');
            $this->db->from('ts_artwork_arts a');
            $this->db->where('a.artwork_id',$artwork_id);
            $this->db->where('a.art_type','Repeat');
            if ($type=='REDRAW') {
                $this->db->where('a.redraw_time',0);
            }
            if ($type=='VECTORED') {
                $this->db->where('a.redrawvect',1);
                $this->db->where('a.vectorized_time',0);
            }
            if ($type=='TOPROOF') {
                $this->db->where('a.vectorized_time > ',0);
            }
            $res=$this->db->get()->row_array();
        } elseif ($type=='PROOF_ALL' || $type=='PROOF_SEND' || $type=='PROOF_APPROVED') {
            $this->db->select('count(artwork_proof_id) as cnt');
            $this->db->from('ts_artwork_proofs');
            $this->db->where('artwork_id',$artwork_id);
            if ($type=='PROOF_SEND') {
                $this->db->where('sended',1);
            }
            if ($type=='PROOF_APPROVED') {
                $this->db->where('approved',1);
            }
            $res=$this->db->get()->row_array();
        }
        return $res['cnt'];
    }

    private function artlogo_notification($logos, $artwork_id, $rush_msgtxt) {
        $emails=$this->get_emails_fornotification('New Art Redraw');
        $num_emails=count($emails);
        $cc_array=array();
        if ($num_emails>0) {
            if ($num_emails==1) {
                $from=$emails[0]['email_address'];
            } else {
                $from=$emails[0]['email_address'];
                $idx=0;
                foreach ($emails as $row) {
                    if ($idx>0) {
                        array_push($cc_array, $row['email_address']);
                    }
                    $idx++;
                }
            }
            /* ART order or Proof */
            $this->db->select('e.proof_num, o.order_num, a.artwork_rush');
            $this->db->from('ts_artworks a');
            $this->db->join('ts_orders o','o.order_id=a.order_id','left');
            $this->db->join('ts_emails e','e.email_id=a.mail_id','left');
            $this->db->where('a.artwork_id',$artwork_id);
            $art=$this->db->get()->row_array();
            if ($art['order_num']) {
                $doc_name='Order #'.$art['order_num'];
            } else {
                $doc_name='Proof Request #'.$art['proof_num'];
            }
            $email_body=$this->load->view('messages/newredraw_message_view',array('doc_name'=>$doc_name,'logos'=>$logos, 'rushmsg'=>$rush_msgtxt),TRUE);
            // Send message
            $this->load->library('email');
            $config['protocol'] = 'sendmail';
            $config['charset'] = 'utf8';
            $config['wordwrap'] = TRUE;
            $config['mailtype'] = 'html';

            $this->email->initialize($config);

            $this->email->to($from);
            if (count($cc_array)!=0) {
                $this->email->cc($cc_array);
            }
            $from=$this->config->item('redraw_email');
            $this->email->from($from);
            $msg='New Logo to Redraw. Production: '.($art['artwork_rush']==1 ? ' Rush' : ' Standard');
            $this->email->subject($msg);
            $this->email->message($email_body);
            $this->email->send();
            $this->email->clear(TRUE);
        }
        return TRUE;
    }

    public function get_emails_fornotification($system) {
        $this->db->select('email_address');
        $this->db->from('ts_email_notifications');
        $this->db->where('notification_type',$system);
        $this->db->where('notification_status','Active');
        $res=$this->db->get()->result_array();
        return $res;
    }

    /* Change Stage - BLANK type */
    public function art_blank_changestage($data, $artdata, $artwork_id, $user_id) {
        $cntproofall=$this->artwork_chklogo($artwork_id, 'PROOF_ALL');
        $cntproofappr=$this->artwork_chklogo($artwork_id, 'PROOF_APPROVED');
        $newstage='';
        /* Lets GO */
        $newstage=Artwork_model::JUST_APPROVED;
        if ($artdata['artstage']!=$newstage) {
            switch ($artdata['artstage']) {
                case Artwork_model::NO_ART :
                    $newstage=Artwork_model::REDRAWN;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::NO_VECTOR;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::TO_PROOF;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::NEED_APPROVAL;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::JUST_APPROVED;
                    $this->change_artstage($data, $newstage,$user_id);
                    break;
                case Artwork_model::REDRAWN :
                    $newstage=  Artwork_model::NO_VECTOR;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::TO_PROOF;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::NEED_APPROVAL;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::JUST_APPROVED;
                    $this->change_artstage($data, $newstage,$user_id);
                    break;
                case Artwork_model::NO_VECTOR :
                    $newstage=Artwork_model::TO_PROOF;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::NEED_APPROVAL;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::JUST_APPROVED;
                    $this->change_artstage($data, $newstage,$user_id);
                    break;
                case Artwork_model::TO_PROOF:
                    $newstage=Artwork_model::NEED_APPROVAL;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::JUST_APPROVED;
                    $this->change_artstage($data, $newstage,$user_id);
                    break;
                case Artwork_model::NEED_APPROVAL:
                    $newstage=Artwork_model::JUST_APPROVED;
                    $this->change_artstage($data, $newstage,$user_id);
                    $newstage=Artwork_model::TO_PROOF;
                    $this->change_artstage($data, $newstage,$user_id);
                case Artwork_model::JUST_APPROVED:
                    break;
            }
        }
        return TRUE;
    }


    /* Change Stage - COMMON type (with logos) */
    public function art_common_changestage($data,$artdata,$artwork_id,$user_id) {
        /* count Logos, Proofs , etc */
        $current_stage=$artdata['artstage'];
        $cntlogoall=$this->artwork_chklogo($artwork_id, 'ALL');
        $cnttextall=$this->artwork_chktext($artwork_id, 'ALL');
        $cntrepeat=$this->artwork_check_repeat($artwork_id, 'ALL');

        $cntall=(intval($cntlogoall)+intval($cnttextall)+intval($cntrepeat));

        $cntlogovector=$this->artwork_chklogo($artwork_id, 'TOPROOF');
        $cnttextvector=$this->artwork_chktext($artwork_id, 'TOPROOF');

        $cntvector=(intval($cntlogovector)+intval($cnttextvector)+intval($cntrepeat));

        $cntproofall=$this->artwork_chklogo($artwork_id, 'PROOF_ALL');
        $cntproofappr=$this->artwork_chklogo($artwork_id, 'PROOF_APPROVED');
        $artchk=array(
            'stage'=>$current_stage,
            'cntlogoall'=>$cntlogoall,
            'cnttextall'=>$cnttextall,
            'cntall'=>$cntall,
            'cntlogovector'=>$cntlogovector,
            'cnttextvector'=>$cnttextvector,
            'cntvector'=>$cntvector,
            'cntproofall'=>$cntproofall,
            'cntproofappr'=>$cntproofappr,
        );
        $newstage='';
        /* Lets GO */
        if ($cntproofappr>0 /*&& $cntproofappr==$cntproofall*/) {
            $newstage= Artwork_model::JUST_APPROVED;
        } elseif ($cntproofall>0) {
            $newstage=  Artwork_model::NEED_APPROVAL;
        } elseif ($cntall>0) {
            if ($cntvector==$cntall) {
                $newstage=Artwork_model::TO_PROOF;
            } else /*($cntvector!=$cntall)*/ {
                $newstage=Artwork_model::NO_VECTOR;
            }
        } else {
            $newstage=Artwork_model::NO_ART;
        }
        /* Make correct change of stage */
        if ($newstage!=$current_stage) {
            // Need to change
            switch ($newstage) {
                case Artwork_model::JUST_APPROVED:
                    if ($current_stage==Artwork_model::NO_ART) {
                        $this->change_artstage($data, Artwork_model::REDRAWN, $user_id);
                        $this->change_artstage($data, Artwork_model::NO_VECTOR, $user_id);
                        $this->change_artstage($data, Artwork_model::TO_PROOF, $user_id);
                        $this->change_artstage($data, Artwork_model::NEED_APPROVAL, $user_id);
                    } elseif ($current_stage==Artwork_model::REDRAWN) {
                        $this->change_artstage($data, Artwork_model::NO_VECTOR, $user_id);
                        $this->change_artstage($data, Artwork_model::TO_PROOF, $user_id);
                        $this->change_artstage($data, Artwork_model::NEED_APPROVAL, $user_id);
                    } elseif ($current_stage==Artwork_model::NO_VECTOR) {
                        $this->change_artstage($data, Artwork_model::TO_PROOF, $user_id);
                        $this->change_artstage($data, Artwork_model::NEED_APPROVAL, $user_id);
                    } elseif ($current_stage==Artwork_model::TO_PROOF) {
                        $this->change_artstage($data, Artwork_model::NEED_APPROVAL, $user_id);
                    }
                    $this->change_artstage($data, $newstage, $user_id);
                    break;
                case Artwork_model::NEED_APPROVAL:
                    if ($current_stage==Artwork_model::NO_ART) {
                        $this->change_artstage($data, Artwork_model::REDRAWN, $user_id);
                        $this->change_artstage($data, Artwork_model::NO_VECTOR, $user_id);
                        $this->change_artstage($data, Artwork_model::TO_PROOF, $user_id);
                    } elseif ($current_stage==Artwork_model::REDRAWN) {
                        $this->change_artstage($data, Artwork_model::NO_VECTOR, $user_id);
                        $this->change_artstage($data, Artwork_model::TO_PROOF, $user_id);
                    } elseif ($current_stage==Artwork_model::NO_VECTOR) {
                        $this->change_artstage($data, Artwork_model::TO_PROOF, $user_id);
                    } elseif ($current_stage==Artwork_model::TO_PROOF) {
                        $this->change_artstage($data, Artwork_model::NEED_APPROVAL, $user_id);
                    }
                    $this->change_artstage($data, $newstage, $user_id);
                    break;
                case Artwork_model::TO_PROOF:
                    if ($current_stage==Artwork_model::NO_ART) {
                        $this->change_artstage($data, Artwork_model::REDRAWN, $user_id);
                        $this->change_artstage($data, Artwork_model::NO_VECTOR, $user_id);
                    } elseif ($current_stage==Artwork_model::REDRAWN) {
                        $this->change_artstage($data, Artwork_model::NO_VECTOR, $user_id);
                    }
                    $this->change_artstage($data, $newstage, $user_id);
                    break;
                case Artwork_model::NO_VECTOR:
                    if ($current_stage==Artwork_model::NO_ART) {
                        $this->change_artstage($data, Artwork_model::REDRAWN, $user_id);
                    }
                    $this->change_artstage($data, $newstage, $user_id);
                    break;
                case Artwork_model::REDRAWN:
                    $this->change_artstage($data, $newstage, $user_id);
                    break;
                case Artwork_model::NO_ART:
                    $this->change_artstage($data, $newstage, $user_id);
                    break;
                default:
                    break;
            }
        }

    }

    public function change_artstage($data, $newstage, $user_id) {
        if ($data['order_id']==0) {
            $this->db->set('proof_updated',time());
            $this->db->where('email_id',$data['proof_id']);
            /* Analyse STAGE */
            switch ($newstage) {
                case Artwork_model::NO_ART:
                    $this->db->set('email_art',0);
                    $this->db->set('email_art_update', time());
                    $this->db->set('email_redrawn', 0);
                    $this->db->set('email_redrawn_update',0);
                    $this->db->set('email_vectorized',0);
                    $this->db->set('email_vectorized_update',0);
                    $this->db->set('email_proofed', 0);
                    $this->db->set('email_proofed_update', 0);
                    $this->db->set('email_approved', 0);
                    $this->db->set('email_approved_update',0);
                    break;
                case Artwork_model::REDRAWN:
                    $this->db->set('email_art',1);
                    $this->db->set('email_art_update', time());
                    $this->db->set('email_redrawn', 0);
                    $this->db->set('email_redrawn_update', 0);
                    $this->db->set('email_vectorized',0);
                    $this->db->set('email_vectorized_update',0);
                    $this->db->set('email_proofed', 0);
                    $this->db->set('email_proofed_update', 0);
                    $this->db->set('email_approved', 0);
                    $this->db->set('email_approved_update',0);
                    break;
                case Artwork_model::NO_VECTOR:
                    $this->db->set('email_redrawn', 1);
                    $this->db->set('email_redrawn_update', time());
                    $this->db->set('email_vectorized',0);
                    $this->db->set('email_vectorized_update',0);
                    $this->db->set('email_proofed', 0);
                    $this->db->set('email_proofed_update', 0);
                    $this->db->set('email_approved', 0);
                    $this->db->set('email_approved_update',0);
                    break;
                case Artwork_model::TO_PROOF:
                    $this->db->set('email_vectorized',1);
                    $this->db->set('email_vectorized_update',time());
                    $this->db->set('email_proofed', 0);
                    $this->db->set('email_proofed_update', 0);
                    $this->db->set('email_approved', 0);
                    $this->db->set('email_approved_update',0);
                    break;
                case Artwork_model::NEED_APPROVAL:
                    $this->db->set('email_proofed', 1);
                    $this->db->set('email_proofed_update', time());
                    $this->db->set('email_approved', 0);
                    $this->db->set('email_approved_update',0);
                    break;
                case Artwork_model::JUST_APPROVED:
                    $this->db->set('email_approved', 1);
                    $this->db->set('email_approved_update',  time());
            }
            $this->db->update('ts_emails');
        } else {
            $this->db->set('update_date',  time());
            $this->db->where('order_id',$data['order_id']);
            /* Analyse STAGE */
            switch ($newstage) {
                case Artwork_model::NO_ART:
                    $this->db->set('order_art',0);
                    $this->db->set('order_art_update', time());
                    $this->db->set('order_redrawn', 0);
                    $this->db->set('order_redrawn_update',0);
                    $this->db->set('order_vectorized',0);
                    $this->db->set('order_vectorized_update',0);
                    $this->db->set('order_proofed', 0);
                    $this->db->set('order_proofed_update', 0);
                    $this->db->set('order_approved', 0);
                    $this->db->set('order_approved_update',0);
                    break;
                case Artwork_model::REDRAWN:
                    $this->db->set('order_art',1);
                    $this->db->set('order_art_update', time());
                    $this->db->set('order_redrawn', 0);
                    $this->db->set('order_redrawn_update', 0);
                    $this->db->set('order_vectorized',0);
                    $this->db->set('order_vectorized_update',0);
                    $this->db->set('order_proofed', 0);
                    $this->db->set('order_proofed_update', 0);
                    $this->db->set('order_approved', 0);
                    $this->db->set('order_approved_update',0);
                    break;
                case Artwork_model::NO_VECTOR:
                    $this->db->set('order_redrawn', 1);
                    $this->db->set('order_redrawn_update', time());
                    $this->db->set('order_vectorized',0);
                    $this->db->set('order_vectorized_update',  0);
                    $this->db->set('order_proofed', 0);
                    $this->db->set('order_proofed_update', 0);
                    $this->db->set('order_approved', 0);
                    $this->db->set('order_approved_update',0);
                    break;
                case Artwork_model::TO_PROOF:
                    $this->db->set('order_vectorized',1);
                    $this->db->set('order_vectorized_update', time());
                    $this->db->set('order_proofed', 0);
                    $this->db->set('order_proofed_update', 0);
                    $this->db->set('order_approved', 0);
                    $this->db->set('order_approved_update',0);
                    break;
                case Artwork_model::NEED_APPROVAL:
                    $this->db->set('order_proofed', 1);
                    $this->db->set('order_proofed_update', time());
                    $this->db->set('order_approved', 0);
                    $this->db->set('order_approved_update',0);
                    break;
                case Artwork_model::JUST_APPROVED:
                    $this->db->set('order_approved', 1);
                    $this->db->set('order_approved_update',time());
                    break;
            }
            $this->db->set('update_usr',$user_id);
            $this->db->update('ts_orders');
            // Update status
            $this->db->select('order_approved_view(order_id) as aprrovview, order_placed(order_id) as placeord');
            $this->db->from('ts_orders');
            $this->db->where('order_id',$data['order_id']);
            $statres=$this->db->get()->row_array();
            $this->db->where('order_id',$data['order_id']);
            $this->db->set('order_artview', $statres['aprrovview']);
            $this->db->set('order_placed', $statres['placeord']);
            $this->db->update('ts_orders');
        }
    }

    /* Search Items from GREY */
    public function search_items($item) {
        $this->db->select("item_id  as value, concat(item_number,' / ',item_name) as label",FALSE);
        $this->db->from('v_itemsearch');
        $this->db->like('upper(concat(item_name,item_number)) ',  strtoupper($item));
        $this->db->order_by('item_number');
        $result=$this->db->get()->result_array();
        return $result;
    }

    /* Search item by ID */
    public function search_itemid($artdata, $item_id, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        // Search
        $this->db->select('item_number, item_name');
        $this->db->from('v_itemsearch');
        $this->db->where('item_id',$item_id);
        $res=$this->db->get()->row_array();
        if (!isset($res['item_number'])) {
            $out['msg']='Item Not Found';
        } else {
            $artdata['item_name']=$res['item_name'];
            $artdata['item_num']=$res['item_number'];
            $artdata['item_id']=$item_id;

            usersession($artsession, $artdata);
            $out['result']= $this->success_result;
            $out['msg']='';
            $out['item_name']=$res['item_name'];
            $out['item_number']=$res['item_number'];
            $out['imprints']=$this->get_location_imprint($item_id);
        }
        return $out;
    }

    public function get_template($item_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        $dbtablename='sb_items';
        $this->db->select("item_vector_img, item_name");
        $this->db->from($dbtablename);
        $this->db->where('item_id',$item_id);
        $result=$this->db->get()->row_array();
        if (!isset($result['item_vector_img'])) {
            $out['msg']='Undefine Item';
        } else {
            if (!empty($result['item_vector_img'])) {
                $out['template']=$result['item_vector_img'];
                $out['item_name']=$result['item_name'];
                $out['result']=  $this->success_result;
            } else {
                $out['msg']='Empty Template';
            }
        }
        return $out;
    }

    public function get_templates($artdata, $artwork_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        if ($artdata['artwork_id']!=$artwork_id) {
            $out['msg']='Artwork data was lost. Please reload data';
        } else {
            $dbtablename='sb_items';
            $this->db->select("item_id, item_number, item_name, item_vector_img");
            $this->db->from($dbtablename);
            $this->db->where('item_vector_img is not null');
            $result=$this->db->get()->result_array();

            $out['templates']=$result;
            $out['result']=  $this->success_result;
        }
        return $out;
    }

    public function add_prooffile($artdata, $file, $filename, $usr_id, $artsession)
    {
        $out = array('result' => $this->error_result, 'msg' => $this->INIT_MSG);
        $idxproof = 0;
        $numpp = 0;
        foreach ($artdata['proofs'] as $row) {
            $numpp = $row['proof_ordnum'];
            $idxproof++;
        }
        $path_full = $this->config->item('upload_path_preload');
        $path_sh = $this->config->item('pathpreload');
        $prefix = ($artdata['order_num'] == '' ? $artdata['proof_num'] : $artdata['order_num']);
        if (file_exists($path_full.$file)) {
            $newsrc = str_replace($path_full, $path_sh, $file);
            $numpp++;
            $idxproof++;
            $proof_id = ($idxproof) * (-1);
            $newname = 'proof_' . $prefix . '_' . str_pad($numpp, 2, '0', STR_PAD_LEFT) . '.pdf';
            $dellink = '<div data-proofid="' . $proof_id . '" data-artworkid="' . $artdata['artwork_id'] . '" class="artpopup_artredcirkle removeproof">&nbsp;</div>';
            $newproof = ['artwork_proof_id' => $proof_id, 'artwork_id' => $artdata['artwork_id'], 'proof_name' => $newname, 'src' => $newsrc, 'approved' => 0, 'approved_time' => 0, 'sended' => 0, 'sended_time' => 0, 'deleted' => '', 'dellink' => $dellink, 'proof_ordnum' => $numpp, 'source_name' => $filename,];
            $artdata['proofs'][] = $newproof;
            // Save to log
            $this->load->model('artproof_model');
            $this->artproof_model->add_proofdoc_log($artdata['artwork_id'], $usr_id, $path_sh.$file, $filename, 'Save Upload');
        }

        usersession($artsession, $artdata);
        $out['result'] = $this->success_result;
        /* Get all proofs */
        $proofs = array();
        $proofnum = 1;
        $approvenum = 1;
        foreach ($artdata['proofs'] as $row) {
            if ($row['deleted'] == '') {
                $row['out_approved'] = '';
                $row['approve_class'] = '';
                $row['approve_class'] = 'proofnotapproved';
                $row['out_approved'] = '<img src="/img/artpage/artpopup_whitestar.png" alt="proof"/>';
                $row['out_proofname'] = 'proof_' . str_pad($proofnum, 2, '0', STR_PAD_LEFT);
                $proofnum++;
                $row['out_apprname'] = '';
                if ($row['approved'] == 1) {
                    $row['out_approved'] = '<img src="/img/artpage/artpopup_greenstar.png" alt="proof"/>';
                    $row['approve_class'] = 'proofapproved';
                    $row['out_apprname'] = 'approved_' . str_pad($approvenum, 2, '0', STR_PAD_LEFT);
                    $approvenum++;
                }
                $proofs[] = $row;
            }
        }
        $out['proofs'] = $proofs;
        return $out;
    }

    /* Delete Proof */
    function art_delproof($artdata, $artwork_id, $proof_id, $user_id, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        if ($artdata['artwork_id']!=$artwork_id) {
            $out['msg']='Artwork data was lost. Please reload data';
        } else {
            $found=0;
            $idxproof=0;
            foreach ($artdata['proofs'] as $prow) {
                if ($prow['artwork_proof_id']==$proof_id) {
                    $artdata['proofs'][$idxproof]['deleted']='del';
                    $found=1;
                    $this->load->model('artproof_model');
                    $this->artproof_model->add_proofdoc_log($artwork_id, $user_id, $prow['src'], $prow['source_name'], 'Remove Proof');
                    break;
                }
                if ($found==1) {
                    break;
                }
                $idxproof++;
            }
            if ($found) {
                /* Save ARTDATA */
                $newproof=array();
                $idxproof=0;
                $proofnum=1;
                $approvenum=1;
                // $numpp=0;
                foreach ($artdata['proofs'] as $row) {
                    if ($row['deleted']=='') {
                        // $numpp++;
                        $newprofname='proof_';
                        if (intval($artdata['order_id'])==0) {
                            $newprofname.=str_replace('-', '_', $artdata['proof_num']);
                        } else {
                            $newprofname.=str_replace('-', '_', $artdata['order_num']);
                        }
                        // $newprofname.='_'.str_pad($numpp, 2, '0', STR_PAD_LEFT).'.pdf';
                        $newprofname.='_'.str_pad($row['proof_ordnum'], 2, '0', STR_PAD_LEFT).'.pdf';
                        $artdata['proofs'][$idxproof]['proof_name']=$newprofname;
                        $row['proof_name']=$newprofname;
                        $row['out_approved']='';
                        $row['approve_class']='';
                        $row['approve_class']='proofnotapproved';
                        /* artpopup_whitestar.png */
                        $row['out_approved']='<img src="/img/artpage/artpopup_whitestar.png" alt="proof"/>';
                        $row['out_proofname']='proof_'.str_pad($proofnum, 2, '0', STR_PAD_LEFT);
                        $proofnum++;
                        $row['out_apprname']='';
                        if ($row['approved']==1) {
                            $row['out_approved']='<img src="/img/artpage/artpopup_greenstar.png" alt="proof"/>';
                            $row['approve_class']='proofapproved';
                            $row['out_apprname']='approved_'.str_pad($approvenum,2,'0',STR_PAD_LEFT);
                            $approvenum++;
                        }
                        $newproof[]=$row;
                    }
                    $idxproof++;
                }
                usersession($artsession,$artdata);
                $out['proofs']=$newproof;
                $out['result']=  $this->success_result;
                $out['msg']='';
            } else {
                $out['msg']='Proof Doc not found';
            }
        }
        return $out;
    }

    /* Approve Proof */
    public function approve_proof($artwork_id, $proof_id, $artdata, $user_id, $artsession) {
        $out=array('result'=>  $this->error_result,'msg'=>  $this->INIT_MSG);
        if ($artdata['artwork_id']!=$artwork_id) {
            $out['msg']='Your connection is lost. Please, reload form';
        } else {
            $found=0;
            $idxproof=0;
            foreach ($artdata['proofs'] as $prow) {
                if ($prow['artwork_proof_id']==$proof_id) {
                    $found=1;
                    $artdata['proofs'][$idxproof]['approved']=1;
                    $this->load->model('artproof_model');
                    $this->artproof_model->add_proofdoc_log($artwork_id, $user_id, $prow['src'], $prow['source_name'], 'Approve Upload');
                    break;
                }
                $idxproof++;
            }
            if ($found==1) {
                $newproofs=array();
                $proofnum=1;
                $approvenum=1;
                foreach ($artdata['proofs'] as $row) {
                    if ($row['deleted']=='') {
                        $row['out_approved']='';
                        $row['approve_class']='';
                        $row['approve_class']='proofnotapproved';
                        $row['out_approved']='<img src="/img/artpage/artpopup_whitestar.png" alt="proof"/>';
                        $row['out_proofname']='proof_'.str_pad($proofnum, 2, '0', STR_PAD_LEFT);
                        $proofnum++;
                        $row['out_apprname']='';
                        if ($row['approved']==1) {
                            $row['out_approved']='<img src="/img/artpage/artpopup_greenstar.png" alt="proof"/>';
                            $row['approve_class']='proofapproved';
                            $row['out_apprname']='approved_'.str_pad($approvenum,2,'0',STR_PAD_LEFT);
                            $approvenum++;
                        }
                        $newproofs[]=$row;
                    }
                }
                $out['proofs']=$newproofs;
                usersession($artsession, $artdata);
                $out['result']= $this->success_result;
                $out['msg']='';
            } else {
                $out['msg']='Proof not found';
            }
        }
        return $out;
    }

    public function send_proof_approve($data, $artdata, $user_id, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        $seanmail=0;
        /* Check Data */
        if ($data['artwork_id']!=$artdata['artwork_id']) {
            $out['msg']='You Lost connection to Form. Please, reload form';
            return $out;
        } elseif (empty($data['from'])) {
            $out['msg']='Enter Sender Email';
            return $out;
        } elseif (empty($data['customer'])) {
            $out['msg']='Enter Customer Email';
            return $out;
        } elseif (empty($data['subject'])) {
            $out['msg']='Enter Message Subject';
            return $out;
        } elseif (empty($data['message'])) {
            $out['msg']='Enter Message Body';
            return $out;
        } elseif (intval($data['numproofs'])==0) {
            $out['msg']='Mark Proofs for Send';
            return $out;
        } else {
            $this->load->model('artproof_model');
            $this->load->model('email_model');
            $toarray=  explode(',', $data['customer']);
            foreach ($toarray as $row) {
                if (!valid_email_address(trim($row))) {
                    $out['msg']='Customer Email Address '.$row.' Is not Valid';
                    return $out;
                }
                if ($row==$this->config->item('sean_email')) {
                    $seanmail=1;
                }
            }
            if (!empty($data['cc'])) {
                $ccarray=explode(',',$data['cc']);
                foreach ($ccarray as $row) {
                    if (!valid_email_address(trim($row))) {
                        $out['msg']='BCC Email Address '.$row.' Is not Valid';
                        return $out;
                    }
                    if ($row==$this->config->item('sean_email')) {
                        $seanmail=1;
                    }
                }
            }
            /* Devide proof string to array */
            if (substr($data['proofs'],-1)=='|') {
                $proofsdat=substr($data['proofs'],0,-1);
            } else {
                $proofsdat=$data['proofs'];
            }
            $proof_array=  explode('|', $proofsdat);
            $idxproofs=0;
            $attachments=array();
            $attachlink=array();
            $path_prooffull=$this->config->item('artwork_proofs');
            $path_proofsh=$this->config->item('artwork_proofs_relative');
            createPath($path_proofsh);
            $path_full=$this->config->item('upload_path_preload');
            $path_sh=$this->config->item('pathpreload');
            // $proofurl=$this->config->item('prooflnk');
            $proofurl=$this->config->item('newprooflnk');
            foreach ($artdata['proofs'] as $row) {
                // Check that file exist
                $chkfile=$srclocation=str_replace($path_sh,$path_full,$row['src']);
                if ($row['artwork_proof_id']< 0 && !file_exists($chkfile)) {
                    $artdata['proofs'][$idxproofs]['deleted']=1;
                    $row['deleted']=1;
                    $this->artproof_model->add_proofdoc_log($data['artwork_id'], $user_id, $row['src'], $row['source_name'], 'Lost Upload');
                }
                if (in_array($row['artwork_proof_id'],$proof_array) && $row['deleted']==0) {
                    // This proof doc was maked as send
                    // Collect data to insert / update
                    $proof=array();
                    $proof['artwork_proof_id']=($row['artwork_proof_id']<0 ? 0 : $row['artwork_proof_id']);
                    $upload=0;
                    if ($row['artwork_proof_id']<0) {
                        /* Conver doc to real path */
                        $proofsrc=$row['src'];
                        $proofname=$row['proof_name'];
                        $srclocation=str_replace($path_sh,$path_full,$proofsrc);
                        $newlocation=$path_prooffull.$proofname;
                        @copy($srclocation, $newlocation);
                        if (file_exists($newlocation)) {
                            // File Saved successfully
                            $upload=1;
                        }
                        @unlink($srclocation);
                        $newsrc=$path_proofsh.$proofname;
                        $newlink=$this->func->uniq_link(20);
                        $proof['proof_name']=$newsrc;
                        $proof['proofdoc_link']=$newlink;
                    } else {
                        if (empty($row['proofdoc_link'])) {
                            $newlink=$this->func->uniq_link(20);
                            $proof['proofdoc_link']=$newlink;
                            $artdata['proofs'][$idxproofs]['proofdoc_link']=$newlink;
                        }
                        $upload=1;
                    }
                    $proof['sended']=1;
                    $proof['sended_time']=time();
                    $proof['artwork_id']=$artdata['artwork_id'];
                    // Save data
                    if ($upload==1) {
                        $res=$this->save_proofdat($proof, $user_id);
                    } else {
                        $res=0;
                    }

                    if ($res) {
                        $this->artproof_model->add_proofdoc_log($data['artwork_id'], $user_id, $row['src'], $row['source_name'], 'Send Proof');
                        if ($row['artwork_proof_id']<0) {
                            $artdata['proofs'][$idxproofs]['artwork_proof_id']=$res;
                            $artdata['proofs'][$idxproofs]['src']=$newsrc;
                            $artdata['proofs'][$idxproofs]['proofdoc_link']=$newlink;
                        }
                        $artdata['proofs'][$idxproofs]['sended']=1;
                        $artdata['proofs'][$idxproofs]['sended_time']=$proof['sended_time'];
                        $artdata['proofs'][$idxproofs]['approve_class']='proofnotapproved';
                        $artdata['proofs'][$idxproofs]['dellink']='';
                        $attachsrc=$artdata['proofs'][$idxproofs]['proofdoc_link'];
                        $attachments[]=$proofurl.$attachsrc;
                    }
                }
                $idxproofs++;
            }

            if (count($attachments)>0) {
                // Check Lead
                $lead_cc=array();
                $other_cc=array();
                // Add Notification for SEAN
                if ($seanmail==0) {
                    array_push($other_cc, $this->config->item('sean_email'));
                }
                if ($artdata['proofs_id']) {
                    $this->load->model('user_model');
                    $replicas=$this->user_model->get_user_leadreplicas(1);
                    // Get Lead and Main REP
                    $this->db->select('u.user_email, l.lead_number');
                    $this->db->from('users u');
                    $this->db->join('ts_lead_users lu','lu.user_id=u.user_id');
                    $this->db->join('ts_lead_emails le','le.lead_id=lu.lead_id');
                    $this->db->join('ts_leads l','l.lead_id=le.lead_id');
                    $this->db->where('le.email_id', $artdata['proofs_id']);
                    $this->db->where('u.user_status',1);
                    $notemails=$this->db->get()->result_array();
                    if (count($notemails)>0) {
                        // Send message
                        foreach ($notemails as $row) {
                            if (!in_array($row['user_email'], $lead_cc)) {
                                array_push($lead_cc, $row['user_email']);
                            }
                        }
                    }
                }

                // Send message
                $this->load->library('email');
                $config['protocol'] = 'sendmail';
                $config['charset'] = 'utf8';
                $config['wordwrap'] = TRUE;
                $config['mailtype'] = 'text';

                $this->email->initialize($config);

                $this->email->from($data['from']);
                $this->email->to($data['customer']);
                if ($data['cc']!='') {
                    $cc=$data['cc'];
                    foreach ($lead_cc as $row) {
                        $cc.=','.$row;
                    }
                    $this->email->cc($cc);
                } else {
                    if (count($lead_cc)>0) {
                        $this->email->cc($lead_cc);
                    }
                }
                if (!empty($other_cc)) {
                    $this->email->bcc($other_cc);
                }
                $this->email->subject($data['subject']);

                if (count($attachments)==1) {
                    $message='Below you will find a link to your art proof.  Please click on the link to view it:'.PHP_EOL;
                    $message.=''.PHP_EOL;
                    $message.=$attachments[0];
                } else {
                    $message='Below you will find links to your art proofs.  Please click on each link to view the different pages:'.PHP_EOL;;
                    $message.=''.PHP_EOL;
                    foreach ($attachments as $row) {
                        $message.=$row.PHP_EOL;
                    }
                }

                $smessage=  str_replace('<<links>>', $message, $data['message']);

                $this->email->message($smessage);
                $histmsg='Art proof sent - ';
                $histmsg.=''.count($attachments).' attachments';
                $details='';
                foreach ($attachments as $row) {
                    $details.=$row.'<br/>'.PHP_EOL;
                }
                $this->email->send();
                $this->email->clear(TRUE);
                $logoptions=array(
                    'from'=>$data['from'],
                    'to'=>$data['customer'],
                    'subject'=>$data['subject'],
                    'message'=>$data['message'],
                    'user_id'=>$user_id,
                );
                if (!empty($data['cc'])) {
                    $logoptions['cc']=$data['cc'];
                }
                if (count($attachments)>0) {
                    $logoptions['attachments']=$attachments;
                }
                $this->email_model->logsendmail($logoptions);
                // Get Lead related with order / proof requests
                if ($artdata['proofs_id']) {
                    $this->db->select('u.user_email, l.lead_number');
                    $this->db->from('users u');
                    $this->db->join('ts_lead_users lu','lu.user_id=u.user_id');
                    $this->db->join('ts_lead_emails le','le.lead_id=lu.lead_id');
                    $this->db->join('ts_leads l','l.lead_id=le.lead_id');
                    $this->db->where('le.email_id', $artdata['proofs_id']);
                    $this->db->where('u.user_status',1);
                    $notemails=$this->db->get()->result_array();
                    if (count($notemails)>0) {
                        // Send message
                        $list=array();
                        $leadnum='';
                        foreach ($notemails as $row) {
                            array_push($list, $row['user_email']);
                            $leadnum=$row['lead_number'];
                        }
                        $this->email->to($list);
                        $this->email->from($data['from']);
                        $notesubj='Proof sent to '.$artdata['customer_name'];
                        $this->email->subject($notesubj);
                        $msgnote='The Art Dept sent '.$artdata['customer_name'].' '.count($attachments).' proofs today ('.date('m/d/y g:i a').') for Lead # '.$leadnum.' '.$artdata['item_name'].':'.PHP_EOL;
                        foreach ($attachments as $row) {
                            $msgnote.=' - '.str_replace($path_prooffull,'', $row).PHP_EOL;
                        }
                        $this->email->message($msgnote);
                        $this->email->clear(TRUE);
                    }
                    // Update lead history and lead update status
                    $this->db->select('l.lead_number, l.lead_id');
                    $this->db->from('ts_leads l');
                    $this->db->join('ts_lead_emails le','le.lead_id=l.lead_id');
                    $this->db->where('le.email_id', $artdata['proofs_id']);
                    $leadlist=$this->db->get()->result_array();
                    $msg='Art proof emailed'; // .$usrdat['user_name'];
                    foreach ($leadlist as $row) {
                        $this->db->set('lead_id',$row['lead_id']);
                        $this->db->set('user_id', $user_id);
                        $this->db->set('created_date', time());
                        $this->db->set('history_message', $msg);
                        $this->db->insert('ts_lead_logs');
                        $this->db->set('update_usr', $user_id);
                        $this->db->set('update_date', date('Y-m-d H:i:s'));
                        $this->db->where('lead_id',$row['lead_id']);
                        $this->db->update('ts_leads');
                    }
                }
                /* Add to History record that we send ART proof message */
                if ($artdata['artwork_id']) {
                    $this->db->set('artwork_id',$artdata['artwork_id']);
                    $this->db->set('user_id',$user_id);
                    $this->db->set('created_time',time());
                    $this->db->set('message',$histmsg);
                    $this->db->set('message_details', $details);
                    $this->db->insert('ts_artwork_history');
                }

            }
            usersession($artsession, $artdata);

            $proofdat=array();
            $proofnum=1;
            $approvenum=1;
            foreach ($artdata['proofs'] as $row) {
                $row['out_approved']='';
                $row['approve_class']='';
                $row['approve_class']='proofnotapproved';
                $row['out_approved']='<img src="/img/artpage/artpopup_whitestar.png" alt="proof"/>';
                $row['out_proofname']='proof_'.str_pad($proofnum, 2, '0', STR_PAD_LEFT);
                $proofnum++;
                $row['out_apprname']='';
                if ($row['approved']==1) {
                    $row['out_approved']='<img src="/img/artpage/artpopup_greenstar.png" alt="proof"/>';
                    $row['approve_class']='proofapproved';
                    $row['out_apprname']='approved_'.str_pad($approvenum,2,'0',STR_PAD_LEFT);
                    $approvenum++;
                }
                $proofdat[]=$row;
            }
            $out['proofs']=$proofdat;
            $out['result']=$this->success_result;
            $out['msg']='';
        }
        return $out;
    }

    /* Revert Approved */
    public function art_revert_approved($artdata, $artwork_id, $proof_id, $user_id, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        if ($artdata['artwork_id']!=$artwork_id) {
            $out['msg']='Artwork data was lost. Please reload data';
        } else {
            $found=0;
            $idxproof=0;
            foreach ($artdata['proofs'] as $prow) {
                if ($prow['artwork_proof_id']==$proof_id) {
                    $artdata['proofs'][$idxproof]['approved']=0;
                    $artdata['proofs'][$idxproof]['approved_time']=0;
                    $found=1;
                    $this->load->model('artproof_model');
                    $this->artproof_model->add_proofdoc_log($artwork_id, $user_id, $prow['src'], $prow['source_name'], 'Revert Approved Upload');
                    break;
                }
                if ($found==1) {
                    break;
                }
                $idxproof++;
            }
            if ($found) {
                /* Save ARTDATA */
                $newproof=array();
                $idxproof=0;
                // $numpp=0;
                $proofnum=1;
                $approvenum=1;
                foreach ($artdata['proofs'] as $row) {
                    if ($row['deleted']=='') {
                        // $numpp++;
                        $newprofname='proof_';
                        if (intval($artdata['order_id'])==0) {
                            $newprofname.=str_replace('-', '_', $artdata['proof_num']);
                        } else {
                            $newprofname.=str_replace('-', '_', $artdata['order_num']);
                        }
                        $newprofname.='_'.str_pad($row['proof_ordnum'], 2, '0', STR_PAD_LEFT).'.pdf';
                        $artdata['proofs'][$idxproof]['proof_name']=$newprofname;
                        $row['proof_name']=$newprofname;
                        $row['out_approved']='';
                        $row['approve_class']='';
                        $row['approve_class']='proofnotapproved';
                        /* artpopup_whitestar.png */
                        $row['out_approved']='<img src="/img/artpopup_whitestar.png" alt="proof"/>';
                        $row['out_proofname']='proof_'.str_pad($proofnum, 2, '0', STR_PAD_LEFT);
                        $proofnum++;
                        $row['out_apprname']='';
                        if ($row['approved']==1) {
                            $row['out_approved']='<img src="/img/artpopup_greenstar.png" alt="proof"/>';
                            $row['approve_class']='proofapproved';
                            $row['out_apprname']='approved_'.str_pad($approvenum,2,'0',STR_PAD_LEFT);
                            $approvenum++;
                        }
                        $newproof[]=$row;
                    }
                    $idxproof++;
                }
                usersession($artsession, $artdata);
                $out['proofs']=$newproof;
                $out['result']=$this->success_result;
                $out['msg']='';
            } else {
                $out['msg']='Proof Doc not found';
            }
        }
        return $out;
    }

    public function get_approved($artdata, $proof_id) {
        $out=array('result'=> $this->error_result, 'msg'=>  $this->INIT_MSG,'filename'=>'', 'url'=>'');
        $found=0;
        foreach ($artdata['proofs'] as $row) {
            if ($row['artwork_proof_id']==$proof_id) {
                $found=1;
                $url=$row['src'];
                $file=$row['proof_name'];
                break;
            }
            if ($found==1) {
                break;
            }
        }
        if ($found==1) {
            $out['result']=$this->success_result;
            $out['msg']='';
            $out['filename']=$file;
            $out['url']=$url;
        } else {
            $out['msg']='File not found';
        }
        return $out;
    }

    function add_location($artdata, $data, $artwork_id, $art_type, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        if ($artdata['artwork_id']!=$artwork_id) {
            $out['msg']='Unknown Artwork. Please, reload page';
        } else {
            $idxloc=0;
            $numpp=0;
            if (isset($artdata['locations'])) {
                foreach ($artdata['locations'] as $lrow) {
                    $idxloc++;
                    $numpp=$lrow['art_ordnum'];
                }
            }
            $idxloc++;
            $numpp++;
            /* New Location */
            $logo_src='&nbsp;';
            $logo_vect='&nbsp;';
            $usrtxt='';
            $logosrc_path='';
            $logovec_path='';
            $preload_path_fl=$this->config->item('upload_path_preload');
            $preload_path_sh=$this->config->item('pathpreload');
            $logopath='';
            $repeat_text='';
            $imagesourceclass=$imagesourceview='';
            $newart_id=($idxloc*(-1));
            if ($art_type=='Logo' || $art_type=='Reference') {
                if ($art_type=='Logo') {
                    $redraw=1;
                } else {
                    $redraw=0;
                }
                $logopath=$data['logo'];
                /* Make Filename */
                $file_name=str_replace($preload_path_fl, '', $logopath);
                $file_det=extract_filename($file_name);
                if (in_array($file_det['ext'], $this->logo_imageext)) {
                    $imagesourceclass='viewsource';
                    $imagesourceview='/artproofrequest/viewartsource?id='.$newart_id.'&artsession='.$artsession;
                }
                $logosrc_path=$preload_path_sh.$file_name;
                if ($artdata['order_id']) {
                    $logo_src=$artdata['order_num'].'_'.$numpp.'.'.$file_det['ext'];
                } else {
                    $logo_src=$artdata['proof_num'].'_'.$numpp.'.'.$file_det['ext'];
                }
            } elseif($art_type=='Text') {
                $redraw=0;
                $usrtxt=$data['usertext'];
            } else {
                $redraw=0;
                $repeat_text=$data['repeat_text'];
            }
            $rush=$artdata['rush'];
            $location=array(
                'artwork_art_id'=>$newart_id,
                'artwork_id'=>$artwork_id,
                'art_type'=>$art_type,
                'art_ordnum'=>$numpp,
                'logo_src'=>$logo_src,
                'logo_srcpath'=>$logosrc_path,
                'redraw_time'=>'',
                'logo_vectorized'=>$logo_vect,
                'logo_vectorizedpath'=>$logovec_path,
                'vectorized_time'=>'',
                'redrawvect'=>$redraw,
                'rush'=>$rush,
                'customer_text'=>$usrtxt,
                'font'=>'',
                'redraw_message'=>'',
                'redo'=>'',
                'art_numcolors'=>'',
                'art_color1'=>'',
                'art_color2'=>'',
                'art_color3'=>'',
                'art_color4'=>'',
                'art_location'=>'',
                'repeat_text'=>$repeat_text,
                'deleted' =>'',
                'imagesourceclass'=>$imagesourceclass,
                'imagesourceview'=>$imagesourceview,
            );
            $artdata['locations'][]=$location;
            /* Save  */
            usersession($artsession,$artdata);
            $location['numpp']=$numpp;
            $newlocation=$location;
            $empty_icon='<img src="/img/artpage/white_square.png"/>';
            $newlocation['artlabel']=$location['art_ordnum'].'.'.($location['art_type']=='Reference' ? 'Refer' : $location['art_type']);
            $newlocation['redrawchk']=$newlocation['rushchk']=$newlocation['redochk']='&nbsp;';
            if ($art_type=='Logo') {
                $chk='checked="checked"';
                $texticon='';
                $srcdat=extract_filename($newlocation['logo_src']);
                if (in_array($srcdat['ext'],$this->nonredrawn)) {
                    $newlocation['location_state']='source_alert';
                } else {
                    $newlocation['location_state']='source';
                }
                $newlocation['redochk']='<input type="checkbox" class="artredo" data-artworkartid="'.$location['artwork_art_id'].'" value="1"/>';
            } else {
                $chk='';
                $texticon=($newlocation['customer_text']=='' ? $empty_icon : '<img src="/img/artpage/artstatus_icon.png" title="'.$newlocation['customer_text'].'"/>');
                $newlocation['redochk']='&nbsp;';
                $newlocation['location_state']='redrawn';
            }
            if ($rush==1) {
                $chkrush='checked="checked"';
            } else {
                $chkrush='';
            }
            $newlocation['repeat_text']=$repeat_text;
            if ($art_type!='Repeat') {
                $newlocation['redrawchk']='<input type="checkbox" class="artredraw" data-artworkartid="'.$location['artwork_art_id'].'" value="1" '.$chk.'/>';
            }
            $newlocation['rushchk']='<input type="checkbox" class="artrush" data-artworkartid="'.$location['artwork_art_id'].'" value="1" '.$chkrush.'/>';
            $newlocation['redrawicon']=$empty_icon;
            $newlocation['texticon']=$texticon;
            $newlocation['imagesourceclass']=$imagesourceclass;
            $newlocation['imagesourceview']=$imagesourceview;

            $out['newlocation']=$newlocation;
            $out['result']= $this->success_result;
            $out['msg']='';
        }
        return $out;
    }

    public function get_artdata_locusrtxt($artdata, $art_id) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_MSG,'usrtxt'=>'');
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                $out['result']=$this->success_result;
                $out['msg']='';
                $out['usrtxt']=$lrow['customer_text'];
            }
            if ($found==1) {
                break;
            }
        }
        return $out;
    }

    function save_artdata_locusrtxt($artdata, $art_id, $customer_text, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_MSG,'content'=>'');
        $found=0;
        $idx=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id && $lrow['art_type']=='Text') {
                $found=1;
                $artdata['locations'][$idx]['customer_text']=$customer_text;
                usersession($artsession, $artdata);
                $out['result']=$this->success_result;
                $out['msg']='';
                $out['content']='<img src="/img/artpage/artstatus_icon.png" alt="User Text" title="'.$customer_text.'"/>';
            }
            if ($found==1) {
                break;
            }
            $idx++;
        }
        return $out;
    }

    /* List fo fonst */
    function get_fonts($options=array()) {
        $dbtablename='sb_fonts';
        $this->db->select('*');
        $this->db->from($dbtablename);
        if (isset($options['is_popular'])) {
            $this->db->where('is_popular',$options['is_popular']);
        }
        $res=$this->db->get()->result_array();
        return $res;
    }

    /* Update Location field value */
    public function artlocationdata_update($artdata, $locitem, $locvalue, $art_id, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>'Location Not Found');
        $idxloc=0;
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                if (array_key_exists($locitem, $lrow)) {
                    $artdata['locations'][$idxloc][$locitem]=$locvalue;
                    usersession($artsession,$artdata);
                    $out['result']=$this->success_result;
                    break;
                } else {
                    $out['msg']='Location Item '.$locitem.' not Exist';
                }
            }
            if ($found==1) {
                break;
            }
            $idxloc++;
        }
        return $out;
    }

    /* Search Source of LOGO img */
    public function logofilesrc($artworkdata, $art_id, $type) {
        $out=array('result'=>$this->error_result,'filename'=>'','url'=>'','msg'=>'File not found');
        $find=0;
        foreach ($artworkdata['locations'] as $row) {
            if ($row['artwork_art_id']==$art_id) {
                $out['result']=$this->success_result;
                $out['msg']='';
                if ($type=='redraw') {
                    $out['filename']=$row['logo_src'];
                    $out['url']=$row['logo_srcpath'];
                } else {
                    $out['filename']=$row['logo_vectorized'];
                    $out['url']=$row['logo_vectorizedpath'];
                }
                $find=1;
            }
            if ($find==1) {
                break;
            }
        }
        return $out;
    }

    public function delete_location($artdata, $art_id, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        $idxloc=0;
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $artdata['locations'][$idxloc]['deleted']='del';
                $found=1;
                break;
            }
            if ($found==1) {
                break;
            } else {
                $idxloc++;
            }
        }
        if ($found==1) {
            usersession($artsession, $artdata);
            $out['result']= $this->success_result;
            $out['msg']='';
        }
        return $out;
    }


    public function get_artdata_locrdnote($artdata, $art_id) {
        $out=array('result'=>  $this->error_result, 'msg'=> $this->INIT_MSG, 'usrtxt'=>'');
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                $out['result']= $this->success_result;
                $out['msg']='';
                $out['usrtxt']=$lrow['redraw_message'];
            }
            if ($found==1) {
                break;
            }
        }
        return $out;
    }

    public function save_artdata_locrdnote($artdata, $art_id, $redraw_message, $artsession) {
        $out=array('result'=> $this->error_result, 'msg'=> $this->INIT_MSG,'content'=>'');
        $found=0;
        $idx=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                $artdata['locations'][$idx]['redraw_message']=$redraw_message;
                usersession($artsession, $artdata);
                $out['result']=$this->success_result;
                $out['msg']='';
                if ($redraw_message=='') {
                    $out['content']='<img src="/img/artpage/white_square.png">';
                } else {
                    $out['content']='<img src="/img/artpage/artstatus_icon.png" title="'.$redraw_message.'"/>';
                }
            }
            if ($found==1) {
                break;
            } else {
                $idx++;
            }
        }
        return $out;
    }

    public function get_artcolors($artdata,$art_id) {
        $out=array(
            'art_color1'=>'',
            'art_color2'=>'',
            'art_color3'=>'',
            'art_color4'=>'',
        );
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                $out['art_color1']=$lrow['art_color1'];
                $out['art_color2']=$lrow['art_color2'];
                $out['art_color3']=$lrow['art_color3'];
                $out['art_color4']=$lrow['art_color4'];
            }
            if ($found==1) {
                break;
            }
        }
        return $out;
    }

    public function get_artloc_numcolors($artdata, $art_id) {
        $out=array('result'=>$this->success_result,'msg'=>'Artwork was not found');
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                $out['result']= $this->success_result;
                $out['msg']='';
                $out['art_numcolors']=intval($lrow['art_numcolors']);
            }
            if ($found==1) {
                break;
            }
        }
        return $out;
    }

    /* change parameter REDRAWVECT */
    public function art_redraw_update($artdata, $art_id, $redraw, $artsession) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        $idxloc=0;
        $found=0;
        foreach ($artdata['locations'] as $lrow) {
            if ($lrow['artwork_art_id']==$art_id) {
                $found=1;
                $artdata['locations'][$idxloc]['redrawvect']=$redraw;
                usersession($artsession,$artdata);
                $out['result']= $this->success_result;
                $out['msg']='';
                if ($lrow['art_type']=='Logo') {
                    if ($redraw==0) {
                        $out['artwork_class']='redrawn';
                    } else {
                        $out['artwork_class']='source';
                        $logodat=$lrow['logo_src'];
                        if ($logodat) {
                            $logodetails=$this->func->extract_filename($logodat);
                            if (in_array($logodetails['ext'], $this->nonredrawn)) {
                                $out['artwork_class']='source_alert';
                            }
                        }
                    }
                } else {
                    if ($redraw==1) {
                        $out['artwork_class']='source';
                    } else {
                        $out['artwork_class']='redrawn';
                    }
                }
            }
            if ($found==1) {
                break;
            }
            $idxloc++;
        }
        return $out;
    }

    public function get_orderdetails($order_num) {
        $dbtablename='sb_orders';
        $this->db->select('order_id, contact_first_name, contact_last_name, contact_phone, contact_email, order_customer_comment, order_item_id as item_id, item_qty');
        $this->db->from($dbtablename);
        $this->db->where('order_num', $order_num);
        $result=$this->db->get()->result_array();
        if (count($result)==1) {
            $return_arr=$result[0];
            if (isset($return_arr['item_id'])) {
                $this->db->select('item_number, item_name');
                $this->db->from('v_itemsearch');
                $this->db->where('item_id',$return_arr['item_id']);
                $resitm=$this->db->get()->row_array();
                if (isset($resitm['item_number'])) {
                    $return_arr['item_number']=$resitm['item_number'];
                    $return_arr['item_name']=$resitm['item_name'];
                } else {
                    $return_arr['item_number']='';
                    $return_arr['item_name']='';
                }
            } else {
                $return_arr['item_number']='';
                $return_arr['item_name']='';
            }
        } else {
            $return_arr=array();
        }
        return $return_arr;
    }

    /* Read DATA about order ART from GREY  */
    public function get_orderarts($order_id)
    {
        $dbtablename = 'sb_order_artworks';
        $this->db->select('order_artwork_id, order_artwork_printloc, order_artwork_colors, order_artwork_font, order_artwork_text, order_artwork_note');
        $this->db->from($dbtablename);
        $this->db->where('order_artwork_orderid', $order_id);
        $results = $this->db->get()->result_array();
        $out = array();
        $numpp = 1;
        foreach ($results as $row) {
            /* Get data about Order Logos */
            $dbtablename = 'sb_order_userlogos';
            $this->db->select('order_userlogo_file, order_userlogo_filename');
            $this->db->from($dbtablename);
            $this->db->where('order_userlogo_artworkid', $row['order_artwork_id']);
            $logos = $this->db->get()->result_array();
            $numlogos = 0;
            foreach ($logos as $lrow) {
                $out[] = array('artwork_art_id' => 0, 'art_type' => 'Logo', 'art_ordnum' => $numpp, 'logo_src' => $lrow['order_userlogo_filename'], 'customer_text' => '', 'font' => '', 'art_location' => $row['order_artwork_printloc'], 'user_note' => $row['order_artwork_note'],);
                $numpp++;
                $numlogos++;
            }
            if ($row['order_artwork_text']) {
                $out[] = array('artwork_art_id' => 0, 'art_type' => 'Text', 'art_ordnum' => $numpp, 'logo_src' => '', 'customer_text' => $row['order_artwork_text'], 'font' => $row['order_artwork_font'], 'art_location' => $row['order_artwork_printloc'], 'user_note' => ($numlogos == 0 ? $row['order_artwork_note'] : ''),);
                $numpp++;
            }
            // $out[]=$row;
        }
        return $out;
    }

    // Get Update details
    public function get_updatehistory_details($artwork_history_id) {
        $out=array('result'=>  $this->error_result, 'msg'=>'History Update Not Found');
        $this->db->select('ah.artwork_history_id, ah.created_time, ah.message, u.user_name, u.user_leadname, ah.parsed_mailbody, ah.message_details');
        $this->db->select('a.order_id, o.create_date as order_date, a.mail_id, e.email_date');
        $this->db->from('ts_artwork_history ah');
        $this->db->join('users u','u.user_id=ah.user_id','left');
        $this->db->join('ts_artworks a','a.artwork_id=ah.artwork_id');
        $this->db->join('ts_orders o','o.order_id=a.order_id','left');
        $this->db->join('ts_emails e','e.email_id=a.mail_id','left');
        $this->db->where('ah.artwork_history_id',$artwork_history_id);
        $res=$this->db->get()->row_array();
        if (isset($res['artwork_history_id'])) {
            $out['result']= $this->success_result;
            $out['head']=$res;
            // Get Details
            $this->db->select('*');
            $this->db->from('ts_artwork_historydetails');
            $this->db->where('artwork_history_id', $artwork_history_id);
            $out['details']=$this->db->get()->result_array();
        }
        return $out;
    }

    public function _artlocation_log($artwork_id, $location_id, $event) {
        $this->db->set('artwork_id', $artwork_id);
        $this->db->set('art_id', $location_id);
        $this->db->set('event_text', $event);
        $this->db->insert('ts_artwork_logs');
        return true;
    }

    // Save changes in order
    public function leadorder_changeslog($compare_array, $neworddata, $user_id) {
        $this->load->model('shipping_model');
        $msg='';
        $changes=array();
        $historylist=array();
        // Compare Order parameters
        $orderold=$compare_array['order'];
        $ordernew=$neworddata['order'];
        if ($orderold['customer_name']!=$ordernew['customer_name']) {
            array_push($changes, 'customer name to '.$ordernew['customer_name']);
            $historylist[]=array(
                'parameter_name'=>'Customer name',
                'parameter_oldvalue'=>substr($orderold['customer_name']),
                'parameter_newvalue'=>$ordernew['customer_name'],
            );
        }
        if ($orderold['customer_email']!=$ordernew['customer_email']) {
            array_push($changes, 'customer email to '.$ordernew['customer_email']);
            $historylist[]=array(
                'parameter_name'=>'Customer email',
                'parameter_oldvalue'=>$orderold['customer_email'],
                'parameter_newvalue'=>$ordernew['customer_email'],
            );
        }
        if ($orderold['item_id']!=$ordernew['item_id']) {
            array_push($changes, 'order item to '.$ordernew['order_items']);
            $historylist[]=array(
                'parameter_name'=>'Order Item',
                'parameter_oldvalue'=>$orderold['order_items'].' ('.$orderold['order_itemnumber'].')',
                'parameter_newvalue'=>$ordernew['order_items'].' ('.$ordernew['order_itemnumber'].')',
            );
        }
        if (intval($orderold['order_qty'])!=intval($ordernew['order_qty'])) {
            array_push($changes, 'item qty to '.$ordernew['order_qty']);
            $historylist[]=array(
                'parameter_name'=>'Order Item QTY',
                'parameter_oldvalue'=>intval($orderold['order_qty']),
                'parameter_newvalue'=>intval($ordernew['order_qty']),
            );
        }
        if (array_key_exists('item_cost', $orderold) && array_key_exists('item_cost', $ordernew) && round(floatval($orderold['item_cost']),2)!=round(floatval($ordernew['item_cost']),2)) {
            array_push($changes, 'item cost to '.MoneyOutput($ordernew['item_cost']));
            $historylist[]=array(
                'parameter_name'=>'Order Item Cost',
                'parameter_oldvalue'=>MoneyOutput($orderold['item_cost']),
                'parameter_newvalue'=>MoneyOutput($ordernew['item_cost']),
            );
        }
        if (array_key_exists('item_imprint', $orderold) && array_key_exists('item_imprint', $ordernew) && round(floatval($orderold['item_imprint']),2)!=round(floatval($ordernew['item_imprint']),2)) {
            array_push($changes, 'item imprint cost to '.MoneyOutput($ordernew['item_cost']));
            $historylist[]=array(
                'parameter_name'=>'Order Item Imprint Cost',
                'parameter_oldvalue'=>MoneyOutput($orderold['item_imprint']),
                'parameter_newvalue'=>MoneyOutput($ordernew['item_imprint']),
            );
        }
        if (round(floatval($orderold['revenue']),2)!=round(floatval($ordernew['revenue']),2)) {
            array_push($changes, 'revenue to '.MoneyOutput($ordernew['revenue'],2));
            $historylist[]=array(
                'parameter_name'=>'Order Revenue',
                'parameter_oldvalue'=>MoneyOutput($orderold['revenue'],2),
                'parameter_newvalue'=>MoneyOutput($ordernew['revenue'],2),
            );
        }
        if (round(floatval($orderold['shipping']),2)!=round(floatval($ordernew['shipping']),2)) {
            $historylist[]=array(
                'parameter_name'=>'Shipping Cost',
                'parameter_oldvalue'=>MoneyOutput($orderold['shipping'],2),
                'parameter_newvalue'=>MoneyOutput($ordernew['shipping'],2),
            );
        }
        if (round(floatval($orderold['tax']),2)!=round(floatval($ordernew['tax']),2)) {
            $historylist[]=array(
                'parameter_name'=>'Sales Tax',
                'parameter_oldvalue'=>MoneyOutput($orderold['tax'],2),
                'parameter_newvalue'=>MoneyOutput($ordernew['tax'],2),
            );
        }
        if ($orderold['mischrg_label1']!=$ordernew['mischrg_label1']) {
            $historylist[]=array(
                'parameter_name'=>'Misc Charge Label (row 1)',
                'parameter_oldvalue'=>$orderold['mischrg_label1'],
                'parameter_newvalue'=>$ordernew['mischrg_label1'],
            );
        }
        if (round(floatval($orderold['mischrg_val1']),2)!=round(floatval($ordernew['mischrg_val1']),2)) {
            $historylist[]=array(
                'parameter_name'=>'Misc Charge (row 1)',
                'parameter_oldvalue'=>MoneyOutput($orderold['mischrg_val1'],2),
                'parameter_newvalue'=>MoneyOutput($ordernew['mischrg_val1'],2),
            );
        }
        if ($orderold['mischrg_label2']!=$ordernew['mischrg_label2']) {
            $historylist[]=array(
                'parameter_name'=>'Misc Charge Label (row 2)',
                'parameter_oldvalue'=>$orderold['mischrg_label2'],
                'parameter_newvalue'=>$ordernew['mischrg_label2'],
            );
        }
        if (round(floatval($orderold['mischrg_val2']),2)!=round(floatval($ordernew['mischrg_val2']),2)) {
            $historylist[]=array(
                'parameter_name'=>'Misc Charge (row 2)',
                'parameter_oldvalue'=>MoneyOutput($orderold['mischrg_val2'],2),
                'parameter_newvalue'=>MoneyOutput($ordernew['mischrg_val2'],2),
            );
        }
        if ($orderold['discount_label']!=$ordernew['discount_label']) {
            $historylist[]=array(
                'parameter_name'=>'Discount Label',
                'parameter_oldvalue'=>$orderold['discount_label'],
                'parameter_newvalue'=>$ordernew['discount_label'],
            );
        }
        if (round(floatval($orderold['discount_val']),2)!=round(floatval($ordernew['discount_val']),2)) {
            $historylist[]=array(
                'parameter_name'=>'Discount Value',
                'parameter_oldvalue'=>MoneyOutput($orderold['discount_val'],2),
                'parameter_newvalue'=>MoneyOutput($ordernew['discount_val'],2),
            );
        }
        // Shipping
        $shippingold=$compare_array['shipping'];
        $shippingnew=$neworddata['shipping'];
        // Lets Go
        if ($shippingold['shipdate']!=$shippingnew['shipdate']) {
            array_push($changes, 'Shipping Date to '.date('m/d/Y', $shippingnew['shipdate']));
            $historylist[]=array(
                'parameter_name'=>'Shipping Date',
                'parameter_oldvalue'=>(intval($shippingold['shipdate'])==0 ? '' : date('m/d/Y', $shippingold['shipdate'])),
                'parameter_newvalue'=>(intval($shippingnew['shipdate'])==0 ? '' : date('m/d/Y', $shippingnew['shipdate'])),
            );
        }
        if ($shippingold['arrive_date']!=$shippingnew['arrive_date']) {
            if (intval($shippingnew['arrive_date'])==0) {
                array_push($changes, 'Arrive Date  removed');
            } else {
                array_push($changes, 'Arrive Date to '.date('m/d/Y', $shippingnew['arrive_date']));
            }
            $historylist[]=array(
                'parameter_name'=>'Arrive Date',
                'parameter_oldvalue'=>(intval($shippingold['arrive_date'])==0 ? '' : date('m/d/Y', $shippingold['arrive_date'])),
                'parameter_newvalue'=>(intval($shippingnew['arrive_date'])==0 ? '' : date('m/d/Y', $shippingnew['arrive_date'])),
            );
        }
        if ($shippingold['event_date']!=$shippingnew['event_date']) {
            if (!empty($shippingnew['event_date'])) {
                array_push($changes, 'Event Date to '.date('m/d/Y', $shippingnew['shipdate']));
            } else {
                array_push($changes,'Event Date removed');
            }
            $historylist[]=array(
                'parameter_name'=>'Event Date',
                'parameter_oldvalue'=>(intval($shippingold['event_date'])==0 ? '' : date('m/d/Y', $shippingold['event_date'])),
                'parameter_newvalue'=>(intval($shippingnew['event_date'])==0 ? '' : date('m/d/Y', $shippingnew['event_date'])),
            );
        }
        if (floatval($shippingold['rush_price'])!=  floatval($shippingnew['rush_price'])) {
            array_push($changes, 'Rush Price to '.MoneyOutput($ordernew['rush_price'],2));
            $historylist[]=array(
                'parameter_name'=>'Rush Price',
                'parameter_oldvalue'=>  MoneyOutput($shippingold['rush_price'],2),
                'parameter_newvalue'=>  MoneyOutput($shippingnew['rush_price'],2),
            );
        }
        // Shipping Address
        $shipoldaddress=$compare_array['shipping_address'];
        $shipnewaddress=$neworddata['shipping_address'];
        $oldzip='';
        foreach ($shipoldaddress as $adrrow) {
            if (!empty($adrrow['zip'])) {
                $oldzip.=$adrrow['zip'].' ';
            }
        }
        $newzip='';
        foreach ($shipnewaddress as $adrrow) {
            if (!empty($adrrow['zip'])) {
                $newzip.=$adrrow['zip'].' ';
            }
        }
        $oldzip=trim($oldzip);
        $newzip=trim($newzip);
        if ($oldzip!=$newzip) {
            array_push($changes, 'Zip to '.$newzip);
            $historylist[]=array(
                'parameter_name'=>'Zip',
                'parameter_oldvalue'=>$oldzip,
                'parameter_newvalue'=>$newzip,
            );
        }
        // Contacts
        $contactsold=$compare_array['contacts'];
        $contactsnew=$neworddata['contacts'];
        for ($i=0; $i<count($contactsold); $i++) {
            if ($contactsold[$i]['contact_name']!=$contactsnew[$i]['contact_name']) {
                $historylist[]=array(
                    'parameter_name'=>'Contact Name (line '.($i+1).')',
                    'parameter_oldvalue'=>$contactsold[$i]['contact_name'],
                    'parameter_newvalue'=>$contactsnew[$i]['contact_name'],
                );
            }
            if ($contactsold[$i]['contact_phone']!=$contactsnew[$i]['contact_phone']) {
                $historylist[]=array(
                    'parameter_name'=>'Contact Phone (line '.($i+1).')',
                    'parameter_oldvalue'=>$contactsold[$i]['contact_phone'],
                    'parameter_newvalue'=>$contactsnew[$i]['contact_phone'],
                );
            }
            if ($contactsold[$i]['contact_emal']!=$contactsnew[$i]['contact_emal']) {
                $historylist[]=array(
                    'parameter_name'=>'Contact Email (line '.($i+1).')',
                    'parameter_oldvalue'=>$contactsold[$i]['contact_emal'],
                    'parameter_newvalue'=>$contactsnew[$i]['contact_emal'],
                );
            }
            if ($contactsold[$i]['contact_art']!=$contactsnew[$i]['contact_art']) {
                $historylist[]=array(
                    'parameter_name'=>'Contact Track ART (line '.($i+1).')',
                    'parameter_oldvalue'=>$contactsold[$i]['contact_art'],
                    'parameter_newvalue'=>$contactsnew[$i]['contact_art'],
                );
            }
            if ($contactsold[$i]['contact_inv']!=$contactsnew[$i]['contact_inv']) {
                $historylist[]=array(
                    'parameter_name'=>'Contact Track INV (line '.($i+1).')',
                    'parameter_oldvalue'=>$contactsold[$i]['contact_inv'],
                    'parameter_newvalue'=>$contactsnew[$i]['contact_inv'],
                );
            }
            if ($contactsold[$i]['contact_trk']!=$contactsnew[$i]['contact_trk']) {
                $historylist[]=array(
                    'parameter_name'=>'Contact Track TRK (line '.($i+1).')',
                    'parameter_oldvalue'=>$contactsold[$i]['contact_trk'],
                    'parameter_newvalue'=>$contactsnew[$i]['contact_trk'],
                );
            }
        }
        // Billing
        $billingold=$compare_array['order_billing'];
        $billingnew=$neworddata['order_billing'];
        if ($billingold['customer_name']!=$billingnew['customer_name']) {
            $historylist[]=array(
                'parameter_name'=>'Billing Name',
                'parameter_oldvalue'=>$billingold['customer_name'],
                'parameter_newvalue'=>$billingnew['customer_name'],
            );
        }
        if ($billingold['company']!=$billingnew['company']) {
            $historylist[]=array(
                'parameter_name'=>'Billing Company',
                'parameter_oldvalue'=>$billingold['company'],
                'parameter_newvalue'=>$billingnew['company'],
            );
        }
        if ($billingold['customer_ponum']!=$billingnew['customer_ponum']) {
            $historylist[]=array(
                'parameter_name'=>'Customer PO#',
                'parameter_oldvalue'=>$billingold['customer_ponum'],
                'parameter_newvalue'=>$billingnew['customer_ponum'],
            );
        }
        if ($billingold['address_1']!=$billingnew['address_1']) {
            $historylist[]=array(
                'parameter_name'=>'Billing Address (Line 1)',
                'parameter_oldvalue'=>$billingold['address_1'],
                'parameter_newvalue'=>$billingnew['address_1'],
            );
        }
        if ($billingold['address_2']!=$billingnew['address_2']) {
            $historylist[]=array(
                'parameter_name'=>'Billing Address (Line 2)',
                'parameter_oldvalue'=>$billingold['address_2'],
                'parameter_newvalue'=>$billingnew['address_2'],
            );
        }
        if ($billingold['city']!=$billingnew['city']) {
            $historylist[]=array(
                'parameter_name'=>'Billing City',
                'parameter_oldvalue'=>$billingold['city'],
                'parameter_newvalue'=>$billingnew['city'],
            );
        }
        if ($billingold['state_id']!=$billingnew['state_id']) {
            $oldstate=$newstate='';
            if (!empty($billingold['state_id'])) {
                $statedat=$this->shipping_model->get_state($billingold['state_id']);
                $oldstate=$statedat['state_name'].' ('.$statedat['state_code'].')';
            }
            if (!empty($billingnew['state_id'])) {
                $statedat=$this->shipping_model->get_state($billingnew['state_id']);
                $newstate=$statedat['state_name'].' ('.$statedat['state_code'].')';
            }
            $historylist[]=array(
                'parameter_name'=>'Billing State',
                'parameter_oldvalue'=>$oldstate,
                'parameter_newvalue'=>$newstate,
            );
        }
        if ($billingold['zip']!=$billingnew['zip']) {
            $historylist[]=array(
                'parameter_name'=>'Billing ZIP/Postal Code',
                'parameter_oldvalue'=>$billingold['zip'],
                'parameter_newvalue'=>$billingnew['zip'],
            );
        }
        if ($billingold['country_id']!=$billingnew['country_id']) {
            $oldcntr=$newcntr='';
            if (!empty($billingold['country_id'])) {
                $cntrdat=$this->shipping_model->get_country($billingold['country_id']);
                $oldcntr=$cntrdat['country_name'];
            }
            if (!empty($billingnew['country_id'])) {
                $cntrdat=$this->shipping_model->get_country($billingnew['country_id']);
                $newcntr=$cntrdat['country_name'];
            }
            $historylist[]=array(
                'parameter_name'=>'Billing Country',
                'parameter_oldvalue'=>$oldcntr,
                'parameter_newvalue'=>$newcntr,
            );
        }
        // Artwork
        $artworkold=$compare_array['artwork'];
        $artworknew=$neworddata['artwork'];
        if ($artworkold['artwork_rush']!=$artworknew['artwork_rush']) {
            array_push($changes, 'order '.($artworknew['artwork_rush']==1 ? ' RUSH' : 'NOT RUSH'));
            $historylist[]=array(
                'parameter_name'=>'Order Rush',
                'parameter_oldvalue'=>$artworkold['artwork_rush'],
                'parameter_newvalue'=>$artworknew['artwork_rush'],
            );
        }
        if ($artworkold['artwork_blank']!=$artworknew['artwork_blank']) {
            array_push($changes, 'order '.($artworknew['artwork_blank']==1 ? ' BLANK' : 'NOT BLANK'));
            $historylist[]=array(
                'parameter_name'=>'Order Blank',
                'parameter_oldvalue'=>$artworkold['artwork_blank'],
                'parameter_newvalue'=>$artworknew['artwork_blank'],
            );
        }
        if ($artworkold['artstage']!=$artworknew['artstage']) {
            array_push($changes, 'ART Stage '.$artworknew['artstage_txt']);
            $historylist[]=array(
                'parameter_name'=>'ART Stage',
                'parameter_oldvalue'=>$artworkold['artstage_txt'],
                'parameter_newvalue'=>$artworknew['artstage_txt'],
            );
        }
        // Build Main message
        if (count($historylist)>0) {
            $artwork_id=$artworknew['artwork_id'];
            if (count($changes)>0) {
                $msg='Changed';
                foreach ($changes as $crow) {
                    $msg.=' '.$crow.',';
                }
                $msg=substr($msg,0,-1);
            }
            $this->db->set('artwork_id',$artwork_id);
            $this->db->set('user_id',$user_id);
            $this->db->set('created_time', time());
            $this->db->set('message',$msg);
            $this->db->insert('ts_artwork_history');
            $newid=$this->db->insert_id();
            // Add Details
            foreach ($historylist as $row) {
                $this->db->set('artwork_history_id', $newid);
                $this->db->set('parameter_name', $row['parameter_name']);
                $this->db->set('parameter_oldvalue', $row['parameter_oldvalue']);
                $this->db->set('parameter_newvalue', $row['parameter_newvalue']);
                $this->db->insert('ts_artwork_historydetails');
            }
            // Insert
        }
        return TRUE;
    }


}