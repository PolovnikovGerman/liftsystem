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

    function get_artproofs($artwork_id) {
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

}