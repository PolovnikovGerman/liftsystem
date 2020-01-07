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
                    $viewurl='/art/viewartsource?id='.$row['artwork_art_id'];
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

}