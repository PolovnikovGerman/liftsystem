<?php
defined('BASEPATH') OR exit('No direct script access allowed');

Class Cronjob extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
        if (!defined('CRONJOB')) {
            return FALSE;
        }
        date_default_timezone_set('America/New_York');
    }

    public function index() {

    }

    public function order_add_brand() {
        $brands = ['BT','SB'];
        $this->load->helper('array_helper');
        $this->db->select('order_id, order_num');
        $this->db->from('ts_orders');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('order_id', $row['order_id']);
            $this->db->update('ts_orders');
            echo 'Order # '.$row['order_num'].' - '.$webs.PHP_EOL;
        }
        $this->db->select('itemsold_impt_id');
        $this->db->from('ts_itemsold_impts');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('itemsold_impt_id', $row['itemsold_impt_id']);
            $this->db->update('ts_itemsold_impts');
        }
        $this->db->select('order_id, order_num');
        $this->db->from('sb_orders');
        $this->db->where('is_void',0);
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('order_id', $row['order_id']);
            $this->db->update('sb_orders');
            echo 'Order # '.$row['order_num'].' - '.$webs.PHP_EOL;
        }
    }

    public function searchresult_add_brand() {
        $brands = ['BT','SB'];
        $this->load->helper('array_helper');
        $this->db->select('search_result_id');
        $this->db->from('sb_search_results');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('search_result_id', $row['search_result_id']);
            $this->db->update('sb_search_results');
        }
    }

    public function emails_add_brand() {
        $brands = ['BT','SB'];
        $this->load->helper('array_helper');
        $this->db->select('email_id');
        $this->db->from('ts_emails');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('email_id', $row['email_id']);
            $this->db->update('ts_emails');
            echo 'Email '.$row['email_id'].' Brand '.$webs.PHP_EOL;
        }
    }

    public function leads_add_brand() {
        $brands = ['BT','SB'];
        $this->load->helper('array_helper');
        $this->db->select('lead_id, update_date, lead_number');
        $this->db->from('ts_leads');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->set('update_date', $row['update_date']);
            $this->db->where('lead_id', $row['lead_id']);
            $this->db->update('ts_leads');
            echo 'Lead '.$row['lead_number'].' Brand '.$webs.PHP_EOL;
        }
    }

    public function printshop_add_brand() {
        $brands = ['BT','SB'];
        $this->load->helper('array_helper');
        $this->db->select('printshop_instock_id');
        $this->db->from('ts_printshop_instock');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('printshop_instock_id', $row['printshop_instock_id']);
            $this->db->update('ts_printshop_instock');
            echo 'Instock '.$row['printshop_instock_id'].' Brand '.$webs.PHP_EOL;
        }
        $this->db->select('onboat_container, count(*) as cnt');
        $this->db->from('ts_printshop_onboats');
        $this->db->group_by('onboat_container');
        $this->db->order_by('onboat_container');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $webs = random_element($brands);
            $this->db->set('brand', $webs);
            $this->db->where('onboat_container', $row['onboat_container']);
            $this->db->update('ts_printshop_onboats');
            echo 'On boat '.$row['onboat_container'].' Brand '.$webs.PHP_EOL;
            $this->db->set('update_date', $row['update_date']);
            $this->db->where('lead_id', $row['lead_id']);
            $this->db->update('ts_leads');
            echo 'Lead '.$row['lead_number'].' Brand '.$webs.PHP_EOL;
        }
    }

    public function netprofit_brand() {
        $this->db->select('*');
        $this->db->from('netprofit');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            /*$sb_kf = rand(40, 60)/100;
            echo 'Stressball '.$sb_kf.PHP_EOL;
            $this->db->set('profit_id', $row['profit_id']);
            $this->db->set('brand','SB');
            $this->db->set('profit_operating', empty($row['profit_operating']) ? NULL : round($row['profit_operating']*$sb_kf,2));
            $this->db->set('interest', empty($row['interest']) ? NULL : round($row['interest']*$sb_kf,2));
            $this->db->set('profit_payroll', empty($row['profit_payroll']) ? NULL : round($row['profit_payroll']*$sb_kf,2));
            $this->db->set('profit_advertising', empty($row['profit_advertising']) ? NULL : round($row['profit_advertising']*$sb_kf,2));
            $this->db->set('profit_projects', empty($row['profit_projects']) ? NULL : round($row['profit_projects']*$sb_kf,2));
            $this->db->set('profit_w9', empty($row['profit_w9']) ? NULL : round($row['profit_w9']*$sb_kf,2));
            $this->db->set('profit_purchases', empty($row['profit_purchases']) ? NULL : round($row['profit_purchases']*$sb_kf,2));
            $this->db->set('debtinclude', $row['debtinclude']);
            $this->db->set('profit_saved', empty($row['profit_saved']) ? NULL : round($row['profit_saved']*$sb_kf,2));
            $this->db->set('profit_debt', empty($row['profit_debt']) ? NULL : round($row['profit_debt']*$sb_kf,2));
            $this->db->set('profit_owners', empty($row['profit_owners']) ? NULL : round($row['profit_owners']*$sb_kf,2));
            $this->db->set('od2', empty($row['od2']) ? NULL : round($row['od2']*$sb_kf,2));
            $this->db->set('weeknote', $row['weeknote']);
            $this->db->insert('netprofit_dat');
            $sb_kf = rand(40, 60)/100;*/
            $sb_kf = 1;
            echo 'Bluetrack '.$sb_kf.PHP_EOL;
            $this->db->set('profit_id', $row['profit_id']);
            $this->db->set('brand','BT');
            $this->db->set('profit_operating', empty($row['profit_operating']) ? NULL : round($row['profit_operating']*$sb_kf,2));
            $this->db->set('interest', empty($row['interest']) ? NULL : round($row['interest']*$sb_kf,2));
            $this->db->set('profit_payroll', empty($row['profit_payroll']) ? NULL : round($row['profit_payroll']*$sb_kf,2));
            $this->db->set('profit_advertising', empty($row['profit_advertising']) ? NULL : round($row['profit_advertising']*$sb_kf,2));
            $this->db->set('profit_projects', empty($row['profit_projects']) ? NULL : round($row['profit_projects']*$sb_kf,2));
            $this->db->set('profit_w9', empty($row['profit_w9']) ? NULL : round($row['profit_w9']*$sb_kf,2));
            $this->db->set('profit_purchases', empty($row['profit_purchases']) ? NULL : round($row['profit_purchases']*$sb_kf,2));
            $this->db->set('debtinclude', $row['debtinclude']);
            $this->db->set('profit_saved', empty($row['profit_saved']) ? NULL : round($row['profit_saved']*$sb_kf,2));
            $this->db->set('profit_debt', empty($row['profit_debt']) ? NULL : round($row['profit_debt']*$sb_kf,2));
            $this->db->set('profit_owners', empty($row['profit_owners']) ? NULL : round($row['profit_owners']*$sb_kf,2));
            $this->db->set('od2', empty($row['od2']) ? NULL : round($row['od2']*$sb_kf,2));
            $this->db->set('weeknote', $row['weeknote']);
            $this->db->insert('netprofit_dat');
        }
    }

    public function shipzones() {
        $this->db->select('*');
        $this->db->from('sb_shipzone_methods');
        $res = $this->db->get()->result_array();
        foreach ($res as $row) {
            $this->db->set('shipzone_id',$row['shipzone_id']);
            $this->db->set('method_id', $row['method_id']);
            $this->db->set('method_percent',0);
            $this->db->set('method_dimens',0);
            $this->db->set('brand','SB');
            $this->db->insert('sb_shipzone_methods');
        }
    }

    // public function
    public function artorder_daily() {
        //$min_ordernum = '30561';
        $dat_bgn=time();
        $email_to = $this->config->item('artorderdaily_to');
        $email_cc = $this->config->item('artorderdaily_cc');
        $email_from = 'fulfillment@bluetrack.com';
        /* step 1 count # of project orders */
        // $brands = ['BT', 'SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            $this->db->select('count(order_id) as cnt');
            $this->db->from('v_order_statuses');
            $this->db->where('status_type','O');
            $this->db->where('brand', $brand);
            // $this->db->where('order_num >= ',$min_ordernum);
            $res = $this->db->get()->row_array();
            if ($res['cnt'] == 0) {
                $message_body = $this->load->view('messages/artorder_empty_view', array(), TRUE);
            } else {
                /* Begin analize */
                /* Not Placed */
                $options = array(
                    'notplaced' => '',
                    'notredr' => '',
                    'notvector' => '',
                    'notprof' => '',
                    'notapprov' => '',
                    'noart' => '',
                );
                $this->load->model('artproof_model');
                $taskview='orders';
                $inclreq='0';
                $aproved_sort='time';
                $aproved_direc='desc';
                $nonart_sort='time';
                $nonart_direc='desc';
                $redraw_sort='time';
                $redraw_direc='desc';
                $proof_sort='time';
                $proof_direc='desc';
                $needapr_sort='time';
                $needapr_direc='desc';

                $data_aproved=$this->artproof_model->get_tasks_reportstage('just_approved', $taskview, $inclreq, $aproved_sort, $aproved_direc, $brand);
                if (count($data_aproved) > 0) {
                    $options['notplaced'] = $this->load->view('messages/artorder_data_view', array('title' => 'Not Placed', 'orders' => $data_aproved), TRUE);
                } else {
                    $options['notplaced'] = $this->load->view('messages/artorder_emptydata_view', array('title' => 'Not Placed'), TRUE);
                }
                $data_redraw=$this->artproof_model->get_tasks_reportstage('redrawn', $taskview, $inclreq, $redraw_sort, $redraw_direc, $brand);
                if (count($data_redraw) > 0) {
                    $options['notredr'] = $this->load->view('messages/artorder_data_view', array('title' => 'Need to Send Ravi', 'orders' => $data_redraw), TRUE);
                } else {
                    $options['notredr'] = $this->load->view('messages/artorder_emptydata_view', array('title' => 'Need to Send Ravi'), TRUE);
                }
                $data_vector=$this->artproof_model->get_tasks_reportstage('vectored', $taskview, $inclreq, $redraw_sort, $redraw_direc, $brand);
                if (count($data_vector) > 0) {
                    $options['notvector'] = $this->load->view('messages/artorder_data_view', array('title' => 'Waiting for Ravi Redraw', 'orders' => $data_vector), TRUE);
                } else {
                    $options['notvector'] = $this->load->view('messages/artorder_emptydata_view', array('title' => 'Waiting for Ravi Redraw'), TRUE);
                }
                $data_needproof=$this->artproof_model->get_tasks_reportstage('need_proof',$taskview, $inclreq, $needapr_sort, $needapr_direc, $brand);
                if (count($data_needproof)>0) {
                    $options['needprof'] = $this->load->view('messages/artorder_data_view', array('title' => 'Need Proof', 'orders' => $data_needproof), TRUE);
                } else {
                    $options['needprof'] = $this->load->view('messages/artorder_emptydata_view', array('title' => 'Need Proof'), TRUE);
                }

                $data_needapr=$this->artproof_model->get_tasks_reportstage('need_approve', $taskview, $inclreq, $needapr_sort, $needapr_direc, $brand);
                if (count($data_needapr)>0) {
                    $options['notapprov'] = $this->load->view('messages/artorder_data_view', array('title' => 'Waiting on Customer\'s Approval', 'orders' => $data_needapr), TRUE);
                } else {
                    $options['notapprov'] = $this->load->view('messages/artorder_emptydata_view', array('title' => 'Waiting on Customer\'s Approval'), TRUE);
                }
                $data_not_art=$this->artproof_model->get_tasks_reportstage('noart', $taskview, $inclreq, $nonart_sort, $nonart_direc, $brand);
                if (count($data_not_art)>0) {
                    $options['noart'] = $this->load->view('messages/artorder_data_view', array('title' => 'Waiting on Customer Art', 'orders' => $data_not_art), TRUE);
                } else {
                    $options['noart'] = $this->load->view('messages/artorder_emptydata_view', array('title' => 'Waiting on Customer Art'), TRUE);
                }

                $message_body = $this->load->view('messages/artorder_list_view', $options, TRUE);
            }
            $this->load->library('email');
            $email_conf = array(
                'protocol' => 'sendmail',
                'charset' => 'utf-8',
                'wordwrap' => TRUE,
                'mailtype' => 'html',
            );
            $this->email->initialize($email_conf);
            $this->email->to($email_to);
            $this->email->cc($email_cc);
            $this->email->bcc($this->config->item('developer_email'));
            $this->email->from($email_from);
            $mail_subj = 'Orders in PROJ stage ' . date('m/d/Y');
            if ($brand=='BT') {
                $mail_subj.=' (Bluetrack.com)';
            } elseif ($brand=='SB') {
                $mail_subj.=' (Stressballs.com)';
            }
            $this->email->subject($mail_subj);
            $this->email->message($message_body);
            $this->email->send();
            $this->email->clear(TRUE);
        }
    }

    public function pochange_notification() {
        $dateend=strtotime(date('m/d/Y'));
        $datestart = strtotime(date("Y-m-d",$dateend) . " -1 day");
        // $brands = ['BT', 'SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            // Get users list
            $this->db->select('oa.create_user, u.user_name, count(oa.amount_id) as cnt');
            $this->db->from('ts_order_amounts oa');
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            $this->db->join('users u','u.user_id=oa.create_user');
            $this->db->where('oa.create_date >=', $datestart);
            $this->db->where('oa.create_date < ', $dateend);
            $this->db->where('o.brand', $brand);
            $this->db->group_by('oa.create_user, u.user_name');
            $crres=$this->db->get()->result_array();
            $usrids=array();
            $user_data=array();
            foreach ($crres as $row) {
                array_push($usrids, $row['create_user']);
                $user_data[]=array(
                    'user_id'=>$row['create_user'],
                    'user_name'=>$row['user_name'],
                );
            }
            $this->db->select('oa.update_user, u.user_name, count(oa.amount_id) as cnt');
            $this->db->from('ts_order_amounts oa');
            $this->db->join('ts_orders o','o.order_id=oa.order_id');
            $this->db->join('users u','u.user_id=oa.update_user');
            $this->db->where('oa.update_date >=', $datestart);
            $this->db->where('oa.update_date < ', $dateend);
            $this->db->where('o.brand', $brand);
            $this->db->group_by('oa.update_user, u.user_name');
            $upres=$this->db->get()->result_array();
            foreach ($upres as $row) {
                if (!in_array($row['update_user'], $usrids)) {
                    array_push($usrids, $row['update_user']);
                    $user_data[]=array(
                        'user_id'=>$row['update_user'],
                        'user_name'=>$row['user_name'],
                    );
                }
            }
            $msgbody='';
            if (count($usrids)!=0) {
                foreach ($user_data as $row) {
                    // Get data about Added Amounts
                    // profit $  % - PO # - Amount - Vendor - Items
                    $this->db->select('o.profit as profit_sum, o.profit_perc, o.order_num , o.order_items as items, oa.amount_sum as amount, v.vendor_name as vendor, o.reason');
                    $this->db->from('ts_orders o');
                    $this->db->join('ts_order_amounts oa','oa.order_id=o.order_id');
                    $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
                    $this->db->where('oa.create_user', $row['user_id']);
                    $this->db->where("o.is_canceled",0);
                    $this->db->where('oa.create_date >=', $datestart);
                    $this->db->where('oa.create_date < ', $dateend);
                    $this->db->where('o.brand', $brand);
                    $usrcr=$this->db->get()->result_array();
                    $list=array();
                    if (count($usrcr)>0) {
                        foreach ($usrcr as $drow) {
                            $rclass='';
                            $rstyle='';
                            $drow['lowprofit']='';
                            $drow['profit_perc']=round(floatval($drow['profit_perc']));
                            if ($drow['profit_perc']<=0) {
                                $rclass='black';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #000000; color: #FFFFFF;"';
                            } elseif ($drow['profit_perc']>0 && $drow['profit_perc']<10) {
                                $rclass='maroon';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #6D0303; color: #FFFFFF;"';
                            } elseif ($drow['profit_perc']>=10 && $drow['profit_perc']<20) {
                                $rclass='red';
                                $rstyle='style="text-align: right; padding-right:3px; text-align: right; padding-right:3px; background-color: #FF0000;color: #FFFFFF;"';
                            } elseif ($drow['profit_perc']>=20 && $drow['profit_perc']<30) {
                                $rclass='orange';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #EA8A0E;color: #000000;"';
                            } elseif ($drow['profit_perc']>=30 && $drow['profit_perc']<40) {
                                $rclass='white';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #FFFFFF; color: #000000;"';
                            } elseif ($drow['profit_perc']>=40) {
                                $rclass='green';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #00E947; color: #000000;"';
                            }
                            $drow['row_class']=$rclass;
                            $drow['rstyle']=$rstyle;
                            if ($drow['profit_sum']<0) {
                                $drow['out_profit']='($'.number_format(abs($drow['profit_sum']),2,'.',',').')';
                            } else {
                                $drow['out_profit']='$'.number_format($drow['profit_sum'],2,'.',',');
                            }
                            $drow['out_amount']='$'.number_format($drow['amount'],2,'.',',');
                            if ($drow['profit_perc']<$this->config->item('minimal_profitperc') && !empty($drow['reason'])) {
                                $drow['lowprofit']=$drow['reason'];

                            }
                            $list[]=$drow;
                        }
                        $opt=array(
                            'title'=>date('D - M d, Y', $datestart).' - '.$row['user_name'],
                            'subtitle'=>'Newly Added POs:',
                            'lists'=>$list,
                            'type'=>'new',
                        );
                        $msgbody.=$this->load->view('messages/amount_notedata_view', $opt, TRUE);
                    }
                    $this->db->select('o.profit as profit_sum, o.profit_perc, o.order_num , o.order_items as items, oa.amount_sum as amount, v.vendor_name as vendor, oa.reason, o.reason as lreason');
                    $this->db->from('ts_orders o');
                    $this->db->join('ts_order_amounts oa','oa.order_id=o.order_id');
                    $this->db->join('vendors v','v.vendor_id=oa.vendor_id');
                    $this->db->where('oa.update_user', $row['user_id']);
                    $this->db->where('oa.update_date >=', $datestart);
                    $this->db->where('oa.update_date < ', $dateend);
                    $this->db->where('oa.create_date <', $datestart);
                    $this->db->where('o.brand', $brand);
                    $usrupd=$this->db->get()->result_array();
                    $list=array();
                    if (count($usrupd)) {
                        foreach ($usrupd as $drow) {
                            $rclass='';
                            $rstyle='';
                            $drow['lowprofit']='';
                            $drow['profit_perc']=round(floatval($drow['profit_perc']));
                            if ($drow['profit_perc']<$this->config->item('minimal_profitperc') && !empty($drow['lreason'])) {
                                $drow['lowprofit']=$drow['lreason'];
                            }
                            if ($drow['profit_perc']<=0) {
                                $rclass='black';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #000000; color: #FFFFFF;"';
                            } elseif ($drow['profit_perc']>0 && $drow['profit_perc']<10) {
                                $rclass='maroon';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #6D0303; color: #FFFFFF;"';
                            } elseif ($drow['profit_perc']>=10 && $drow['profit_perc']<20) {
                                $rclass='red';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #FF0000;color: #FFFFFF;"';
                            } elseif ($drow['profit_perc']>=20 && $drow['profit_perc']<30) {
                                $rclass='orange';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #EA8A0E;color: #000000;"';
                            } elseif ($drow['profit_perc']>=30 && $drow['profit_perc']<40) {
                                $rclass='white';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #FFFFFF; color: #000000;"';
                            } elseif ($drow['profit_perc']>=40) {
                                $rclass='green';
                                $rstyle='style="text-align: right; padding-right:3px; background-color: #00E947; color: #000000;"';
                            }
                            $drow['row_class']=$rclass;
                            $drow['rstyle']=$rstyle;
                            if ($drow['profit_sum']<0) {
                                $drow['out_profit']='($'.number_format(abs($drow['profit_sum']),2,'.',',').')';
                            } else {
                                $drow['out_profit']='$'.number_format(abs($drow['profit_sum']),2,'.',',');
                            }
                            $drow['out_amount']= '$'.number_format($drow['amount'],2,'.',',');
                            $list[]=$drow;
                        }
                        $opt=array(
                            'title'=>date('D - M d, Y', $datestart).' - '.$row['user_name'],
                            'subtitle'=>'Revised POs:',
                            'lists'=>$list,
                            'type'=>'edit',
                        );
                        $msgbody.=$this->load->view('messages/amount_notedata_view', $opt, TRUE);
                    }
                }

            }
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype']='html';
            $config['wordwrap'] = TRUE;
            $this->email->initialize($config);
            $email_from=$this->config->item('email_notification_sender');
            $email_to=$this->config->item('sean_email');
            $email_cc=array($this->config->item('sage_email'),$this->config->item('developer_email'));
            $this->email->from($email_from);
            $this->email->to($email_to);
            $this->email->cc($email_cc);
            // Temporary ADD for check
            $this->email->bcc([$this->config->item('developer_email')]);
            $title=date('D - M d, Y', $datestart).' - POs added to ';
            if ($brand=='BT') {
                $title.='Bluetrack.com';
            } elseif ($brand=='SB') {
                $title.='Stressballs.com';
            }
            $this->email->subject($title);
            if ($msgbody=='') {
                $body='<span style="font-weight: bold">'.$title.'</span>';
                $this->email->message($body);
            } else {
                $body=$this->load->view('messages/amount_note_view', array('content'=>$msgbody),TRUE);
                $this->email->message($body);
            }
            $this->email->send();
            $this->email->clear(TRUE);
        }
    }

    public function tickets_report() {
        $this->load->model('tickets_model');
        // Prepare Overview
        // $brands = ['BT', 'SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            $overview=$this->tickets_model->get_ticketreport_overview($brand);
            $msgdata='';
            foreach ($overview as $row) {
                $details=$this->tickets_model->get_ticketreport_details($row['tickquat'],$row['tickyear'], $brand);
                // Create date with Moth day - Number of quater
                $datconv=strtotime(date('Y-m').'-0'.$row['tickquat']);
                $msgdata.=$this->load->view('messages/ticket_report_data',array('quater'=>$row['tickquat'],'year'=>$row['tickyear'],'data'=>$details,'titledate'=>$datconv), TRUE);
            }
            $headrep=$this->load->view('messages/ticket_report_head', array('totals'=>$overview),TRUE);
            $msgbody=$this->load->view('messages/ticket_report', array('head'=>$headrep,'data'=>$msgdata),TRUE);
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype']='html';
            $config['wordwrap'] = TRUE;
            $this->email->initialize($config);
            $email_from=$this->config->item('email_notification_sender');
            $email_to=$this->config->item('sean_email');
            $email_cc=array($this->config->item('sage_email'), 'darrell.martin@bluetrack.com','Alex.Pfisterer@bluetrack.com', $this->config->item('developer_email'));

            $this->email->from($email_from);
            $this->email->to($email_to);
            $this->email->cc($email_cc);

            $title=date('D - M d, Y').' - Issues Report ';
            if ($brand=='BT') {
                $title.='(Bluetrack.com)';
            } elseif ($brand=='SB') {
                $title.='(Stressball.com)';
            }
            $this->email->subject($title);
            $this->email->message($msgbody);
            $this->email->send();
            $this->email->clear(TRUE);
        }
    }

    public function artexportdata() {
        $this->load->model('orders_model');
        $res=$this->orders_model->export_artsync();
    }

    public function check_netprofit_week() {
        $this->load->model('balances_model');
        $user_id=1;
        $this->balances_model->_check_current_week($user_id);
        $this->load->model('reports_model');
        $this->reports_model->_check_current_week($user_id);
    }

    public function check_netprofit_month() {
        $this->load->model('balances_model');
        $user_id=1;
        $this->balances_model->_check_current_month($user_id);
    }

    // Art Proof Report
    public function artproof_daily_report() {
        $dateend=strtotime(date('Y-m-d'));
        $datestart = strtotime(date("Y-m-d",$dateend) . " -1 day");
        // Select total
        // $brands = ['BT', 'SB'];
        $brands = ['BT'];
        $this->load->model('reports_model');
        foreach ($brands as $brand) {
            $data = $this->reports_model->artproof_daily_report($datestart, $dateend, $brand);
            $out = $data['out'];
            echo $brand.' data count '.count($out).PHP_EOL;
            if (!empty($out)) {
                // Prepare report
                $title=date('D - M d, Y', $datestart).' - Art Proof Report ';
                if ($brand=='BT') {
                    $title.='(Bluetrack.com)';
                } elseif ($brand=='SB') {
                    $title.='(stressballs.com)';
                }
                $total=$data['total'];
                $totaltype = $data['totaltype'];
                $outtype = $data['outtype'];
                $repoptions=[
                    'lists'=>$out,
                    'total'=>$total,
                    'title'=>$title,
                    'listtype'=>$outtype,
                    'totaltype'=>$totaltype,
                ];
                $body= $this->load->view('messages/artproof_report_view', $repoptions, TRUE);
                $this->load->library('email');
                $config['charset'] = 'utf-8';
                $config['mailtype']='html';
                $config['wordwrap'] = TRUE;
                $this->email->initialize($config);
                $email_from=$this->config->item('email_notification_sender');
                $email_to=$this->config->item('sean_email');
                $email_cc=array(
                    $this->config->item('sage_email'),
                    $this->config->item('taisenkatakura_email'),
                    $this->config->item('art_dept_email'),
                    $this->config->item('developer_email'),
                );
                $this->email->from($email_from);
                $this->email->to($email_to);
                $this->email->cc($email_cc);

                $this->email->subject($title);
                $this->email->message($body);
                $this->email->send();
                $this->email->clear(TRUE);
            }
        }
    }

    public function quotes_week_list() {
        // $date='2018-07-30';
        // $sunday=strtotime('monday this week', strtotime($date));
        $sunday=strtotime('monday this week', time());
        $start= $sunday-(24*60*60);
        $date=date('Y-m-d',$start);
        $monday=strtotime('monday this week', strtotime($date));
        $options=[
            'weekbgn'=>$monday,
            'weekend'=>$sunday,
        ];

        $this->load->model('orders_model');
        // $brands = ['BT','SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            $options['brand']=$brand;
            $res=$this->orders_model->get_week_quotes($options);
            if ($res['result']==1) {
                $title='Quotes, Proof Requests, Orders ('.date('m/d/Y', $monday).' - '.date('m/d/Y', $sunday-1).')';
                if ($brand=='BT') {
                    $title.=' Bluetrack.com';
                } elseif ($brand=='SB') {
                    $title.=' Stressballs.com';
                }
                $params['lists']=$res['data'];
                $params['title']=$title;
                // Prepare email
                $body= $this->load->view('messages/quotesweek_report_view', $params, TRUE);
                $this->load->library('email');
                $config['charset'] = 'utf-8';
                $config['mailtype']='html';
                $config['wordwrap'] = TRUE;
                $this->email->initialize($config);
                $email_from=$this->config->item('email_notification_sender');
                // $email_to=$this->config->item('sean_email');
                // $email_cc=array(
                // $this->config->item('sage_email'),
                // $this->config->item('taisenkatakura_email'),
                // );
                $email_to=$this->config->item('sean_email');
                $email_cc=$this->config->item('developer_email');
                $this->email->from($email_from);
                $this->email->to($email_to);
                $this->email->cc($email_cc);
                $this->email->subject($title);
                $this->email->message($body);

                $this->email->send();
                $this->email->clear(TRUE);
            }
        }
    }

    public function bonus_report() {
        $user_id=23; // Shanequa Hall
        $this->load->model('orders_model');
        // $brands = ['BT', 'SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            $results=$this->orders_model->user_weekproof_reportdata($user_id, $brand);
            $out=$results['out'];
            $total=$results['totals'];
            $dateend=strtotime(date('m/d/Y'));
            $datestart = strtotime(date("Y-m-d",$dateend) . " -1 day");

            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype']='html';
            $config['wordwrap'] = TRUE;
            $this->email->initialize($config);
            $email_from=$this->config->item('email_notification_sender');
            $email_to=$this->config->item('sean_email');
            $email_cc=$this->config->item('sage_email');
            $this->email->from($email_from);
            $this->email->to($email_to);
            $this->email->cc($email_cc);
            $this->email->bcc($this->config('developer_email'));
            $title=date('D - M d, Y', $datestart).' - Sales Report (Shanequa Hall) (Owners version) ';
            if ($brand=='BT') {
                $title.='(Bluetrack.com)';
            } elseif ($brand=='SB') {
                $title.='(Stressballs.com)';
            }
            $this->email->subject($title);
            $body_options=[
                'data'=>$out,
                'total'=>$total,
                'admin'=>1,
                'price_500'=>$this->config->item('bonus_500'),
                'price_1000'=>$this->config->item('bonus_1000'),
                'price_1200'=>$this->config->item('bonus_1200'),
                'bonus_price'=>$this->config->item('bonus_price'),
            ];
            $body=$this->load->view('messages/sales_report_view', $body_options,TRUE);
            $this->email->message($body);
            $this->email->send();
            $this->email->clear(TRUE);
            // Send report to user
            $this->email->from($email_from);
            $this->email->to('shanequa.hall@bluetrack.com');
            // $this->email->to('to_german@yahoo.com');
            $title=date('D - M d, Y', $datestart).' - Sales Report (Shanequa Hall) ';
            if ($brand=='BT') {
                $title.='(Bluetrack.com)';
            } elseif ($brand=='SB') {
                $title.='(Stressballs.com)';
            }
            $this->email->subject($title);
            $body_options=[
                'data'=>$out,
                'total'=>$total,
                'admin'=>0,
                'price_500'=>$this->config->item('bonus_500'),
                'price_1000'=>$this->config->item('bonus_1000'),
                'price_1200'=>$this->config->item('bonus_1200'),
                'bonus_price'=>$this->config->item('bonus_price'),
            ];
            $body=$this->load->view('messages/sales_report_view', $body_options,TRUE);
            $this->email->message($body);
            $this->email->send();
            $this->email->clear(TRUE);
            echo $brand.' Report was send succesfully '.PHP_EOL;
        }
    }

    public function check_ordermath() {

        $dateend=strtotime(date('Y-m-d'));
        $datestart = strtotime(date("Y-m-d",$dateend) . " -1 day");
        // $brands = ['BT','SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            $this->db->select('*');
            $this->db->from('ts_orders');
            $this->db->where('order_date >= ', $datestart);
            $this->db->where('order_date < ', $dateend);
            $this->db->where('is_canceled',0);
            $this->db->where('brand', $brand);
            $this->db->order_by('order_num');
            $res = $this->db->get()->result_array();
            if (count($res)>0) {
                $out=[];
                foreach ($res as $row) {
                    $this->db->select('sum(ic.item_qty*ic.item_price) as item_total');
                    $this->db->from('ts_order_items i');
                    $this->db->join('ts_order_itemcolors ic','ic.order_item_id=i.order_item_id');
                    $this->db->where('i.order_id', $row['order_id']);
                    $itm=$this->db->get()->row_array();
                    $this->db->select('sum(p.imprint_qty*p.imprint_price) as print_sum');
                    $this->db->from('ts_order_items i');
                    $this->db->join('ts_order_imprints p','p.order_item_id=i.order_item_id');
                    $this->db->where('i.order_id', $row['order_id']);
                    $print = $this->db->get()->row_array();
                    $this->db->select('rush_price');
                    $this->db->from('ts_order_shippings');
                    $this->db->where('order_id', $row['order_id']);
                    $ship = $this->db->get()->row_array();
                    $order_total = $itm['item_total']+$print['print_sum']+$row['shipping']+$row['tax']+$row['mischrg_val1']+$row['mischrg_val2']+$ship['rush_price']-$row['discount_val'];
                    if (round($row['revenue'],2)!=round($order_total,2)) {
                        $out[]=[
                            'order_num' => $row['order_num'],
                            'itemcost' => $itm['item_total'],
                            'imprint' => $print['print_sum'],
                            'shipping' => $row['shipping'],
                            'tax'=> $row['tax'],
                            'mischarge' => ($row['mischrg_val1']+$row['mischrg_val2']),
                            'rush' => $ship['rush_price'],
                            'discount' => $row['discount_val'],
                            'calcrevenue' => $order_total,
                            'revenue' => $row['revenue'],
                            'diff' => $order_total - $row['revenue'],
                        ];
                    }
                }
                if (count($out)==0) {
                    $mail_body = 'All orders '.count($res).' math is OK';
                } else {
                    $mail_body = $this->load->view('messages/order_maths_view', ['data'=>$out], TRUE);
                }
                $this->load->library('email');
                $config['charset'] = 'utf-8';
                $config['mailtype']='html';
                $config['wordwrap'] = TRUE;
                $this->email->initialize($config);
                $email_from=$this->config->item('email_notification_sender');
                $email_to='polovnikov.g@gmail.com';
                $this->email->from($email_from);
                $this->email->to($email_to);

                $title=date('D - M d, Y', $datestart).' - Check Orders Maths ';
                if ($brand=='BT') {
                    $title.='(Bluetrack.com)';
                } elseif ($brand=='SB') {
                    $title.='(Stressballs.com)';
                }
                $this->email->subject($title);
                $this->email->message($mail_body);
                $this->email->send();
                $this->email->clear(TRUE);
            } else {
                echo 'Brand '.$brand.' Orders QTY=0'.PHP_EOL;
            }
        }
    }

    public function orderdiscount_msg() {
        $end_time = strtotime(date('Y-m-d'));
        $start_time = strtotime(date('Y-m-d', strtotime(date('Y-m-d',$end_time). ' - 1 day')));
        $this->load->model('orders_model');
        // $brands = ['BT','SB'];
        $brands = ['BT'];
        foreach ($brands as $brand) {
            $out = $this->orders_model->orderdiscount_msg($start_time, $end_time, $brand);

            $msgbody = 'No orders with changes';
            if (count($out)>0) {
                $msgbody = $this->load->view('messages/order_discountalert_view',['data'=>$out], TRUE);
            }
            $this->load->library('email');
            $config['charset'] = 'utf-8';
            $config['mailtype']='html';
            $config['wordwrap'] = TRUE;
            $this->email->initialize($config);
            $email_from=$this->config->item('email_notification_sender');
            $email_to=[$this->config->item('sean_email'), $this->config->item('sage_email')];

            $this->email->from($email_from);
            $this->email->to($email_to);
            $this->email->cc($this->config->item('developer_email'));

            $title=date('D - M d, Y', $start_time).' - Discount Orders ';
            if ($brand=='BT') {
                $title.='(Bluetrack.com)';
            } elseif ($brand=='SB') {
                $title.='(Stressballs.com)';
            }
            $this->email->subject($title);
            $this->email->message($msgbody);
            $this->email->send();
            $this->email->clear(TRUE);
        }

    }

    public function order_autoparse() {
        $this->load->model('orders_model');
        $this->orders_model->order_autoparse();
    }

    public function generate_quota() {
        $this->load->model('email_model');
        $this->email_model->generate_quota();
    }

    public function hide_credit_cards() {
        $last_date=strtotime(date("Y-m-d") . " -15 days");
        $this->db->select('order_id, payment_card_number, payment_card_vn');
        $this->db->from('sb_orders');
        $this->db->where('ccnumb_hide',0);
        $this->db->where('unix_timestamp(order_date) <= ',$last_date);
        $orders=$this->db->get()->result_array();
        foreach ($orders as $row) {
            $cardn=$row['payment_card_number'];
            $cclen=strlen($cardn);
            $newcc=substr($cardn,0,1).str_repeat('X',3).'-';
            $lenhide=$cclen-8;
            $ngroup=0;
            for ($i=0;$i<$lenhide;$i++) {
                $newcc.='X';
                $ngroup++;
                if ($ngroup==4) {
                    $ngroup=0;
                    $newcc.='-';
                }
            }

            // $cclen-5).substr($cardn,-4);
            $newcc.=substr($cardn, -4);
            $this->db->set('payment_card_number',$newcc);
            $this->db->set('ccnumb_hide',1);
            $this->db->where('order_id',$row['order_id']);
            $this->db->update('sb_orders');
        }

    }

    public function attempts_report() {
        $this->load->model('orders_model');
        /* Calculate time begin - end of previous day */
        $start=strtotime(date("Y-m-d", time()) . " - 1 days");
        $end=strtotime(date('m/d/Y',$start).'23:59:59');

        $filtr=array(
            'starttime'=>$start,
            'endtime'=>$end,
        );
        $this->orders_model->attempts_report($filtr);
    }

    public function searchresults_weekreport() {
        // $brands = ['BT','SB'];
        $brands = ['BT'];
        $dat_mon = strtotime('last week Monday');
        $dat_sun = strtotime(date('Y-m-d', strtotime('last week Sunday')).' 23:59:59');
        foreach ($brands as $brand) {
            $this->db->select('search_text, count(search_result_id) as cnt');
            $this->db->from('sb_search_results');
            $this->db->where('search_result',0);
            $this->db->where('unix_timestamp(search_time) >= ', $dat_mon);
            $this->db->where('unix_timestamp(search_time) <= ', $dat_sun);
            $this->db->where('brand', $brand);
            $this->db->group_by('search_text');
            $this->db->order_by('cnt desc, search_text asc');
            $res = $this->db->get()->result_array();

            $mail_body=$this->load->view('marketing/weekreport_view',array('start_date'=>$dat_mon,'end_date'=>$dat_sun,'data'=>$res),TRUE);

            $this->load->library('email');
            $email_conf = array(
                'protocol'=>'sendmail',
                'charset'=>'utf-8',
                'wordwrap'=>TRUE,
                'mailtype'=>'html',
            );
            $this->email->initialize($email_conf);
            $mail_to=array('sean@bluetrack.com');
            $mail_cc=array('sage@bluetrack.com', $this->config->item('developer_email'));

            $this->email->to($mail_to);
            $this->email->cc($mail_cc);

            $this->email->from('no-replay@bluetrack.com');
            $title = 'Weekly Report about Unsuccessful Searches '.($brand=='SB' ? '(Stressballs.com)' : '(Bluetrack.com)');
            $this->email->subject($title);
            $this->email->message($mail_body);
            $res=$this->email->send();

            $this->email->clear(TRUE);

        }
    }

    public function test_email() {
        $email_conf = array(
            'protocol'=>'sendmail',
            'charset'=>'utf-8',
            'wordwrap'=>TRUE,
            'mailtype'=>'html',
        );
        $this->load->library('email');
        $this->email->initialize($email_conf);
        $this->email->to('german.polovnikov@bluetrack.com');

        // $this->email->from('german.polovnikov@golden-team.org');
        $this->email->from('support@bluetrack.com');
        $title = 'Weekly Report about Unsuccessful Searches '.'(Stressballs.com)';
        $this->email->subject($title);
        $this->email->message('Hello Everybody');
        $res=$this->email->send();
        echo $this->email->print_debugger();

        $this->email->clear(TRUE);

    }

}