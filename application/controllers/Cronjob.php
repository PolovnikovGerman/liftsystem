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
        $brands = ['SB', 'SR'];
        foreach ($brands as $brand) {
            $this->db->select('count(order_id) as cnt');
            $this->db->from('v_order_statuses');
            $this->db->where('status_type','O');
            if ($brand=='SB') {
                $this->db->where_in('brand', ['SB','BT']);
            } else {
                $this->db->where('brand', $brand);
            }
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
            if ($brand=='SB') {
                $mail_subj.=' (Bluetrack/Stressballs)';
            } elseif ($brand=='SR') {
                $mail_subj.=' (StressRelievers.com)';
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
        $this->load->model('orders_model');
        $this->orders_model->ckeckpototals($datestart, $dateend);
        $this->orders_model->pochange_notification($datestart, $dateend);
    }

    public function tickets_report() {
        $this->load->model('tickets_model');
        // Prepare Overview
        $brands = ['SB','SR'];

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
            $email_cc=array($this->config->item('sage_email'), $this->config->item('developer_email'));

            $this->email->from($email_from);
            $this->email->to($email_to);
            $this->email->cc($email_cc);

            $title=date('D - M d, Y').' - Issues Report ';
            if ($brand=='SB') {
                $title.='(Bluetrack/Stressballs)';
            } elseif ($brand=='SR') {
                $title.='(StressRelievers.com)';
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
        // $brands = ['SB','SR'];
        $brands = ['SB'];
        $this->load->model('reports_model');
        foreach ($brands as $brand) {
            $data = $this->reports_model->artproof_daily_report($datestart, $dateend, $brand);
            $out = $data['out'];
            echo $brand.' data count '.count($out).PHP_EOL;
            if (!empty($out)) {
                // Prepare report
                $title=date('D - M d, Y', $datestart).' - Art Proof Report ';
                if ($brand=='SB') {
                    $title.='(Bluetrack/Stressballs)';
                } elseif ($brand=='SR') {
                    $title.='(StressRelievers)';
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
                $sendsmtp = intval($brand=='SR' ? $this->config->item('sr_artproof_smtp') : $this->config->item('sb_artproof_smtp'));
                if ($sendsmtp==1) {
                    if ($brand=='SR') {
                        $email_conf = array(
                            'protocol'=>'smtp',
                            'smtp_host' => $this->config->item('sr_smtp_host'),
                            'smtp_port' => $this->config->item('sr_smtp_port'),
                            'smtp_crypto' => $this->config->item('sr_smtp_crypto'),
                            'smtp_user' => $this->config->item('sr_artproof_user'),
                            'smtp_pass' => $this->config->item('sr_artproof_pass'),
                            'charset'=>'utf-8',
                            'mailtype'=>'html',
                            'wordwrap'=>TRUE,
                            'newline' => "\r\n",
                        );
                        $email_from = $this->config->item('sr_artproof_user');
                    } else {
                        $email_conf = array(
                            'protocol'=>'smtp',
                            'smtp_host' => $this->config->item('sb_smtp_host'),
                            'smtp_port' => $this->config->item('sb_smtp_port'),
                            'smtp_crypto' => $this->config->item('sb_smtp_crypto'),
                            'smtp_user' => $this->config->item('sb_artproof_user'),
                            'smtp_pass' => $this->config->item('sb_artproof_pass'),
                            'charset'=>'utf-8',
                            'mailtype'=>'html',
                            'wordwrap'=>TRUE,
                            'newline' => "\r\n",
                        );
                        $email_from = $this->config->item('sr_artproof_user');
                    }
                } else {
                    $email_conf = [
                        'charset' => 'utf-8',
                        'mailtype' => 'html',
                        'wordwrap'=>TRUE,
                    ];
                    $email_from = $this->config->item('email_notification_sender');
                }
                $this->email->initialize($email_conf);
                $email_to=$this->config->item('sean_email');
                $email_cc=array(
                    $this->config->item('sage_email'),
                    $this->config->item('taisenkatakura_email'),
                    $brand=='SR' ? $this->config->item('art_srdept_email') : $this->config->item('art_dept_email'),
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
        $sendsmtp = intval($this->config->item('quoteweek_smtp'));
        if ($sendsmtp==1) {
            $config = [
                'protocol'=>'smtp',
                'smtp_host' => $this->config->item('sb_smtp_host'),
                'smtp_port' => $this->config->item('sb_smtp_port'),
                'smtp_crypto' => $this->config->item('sb_smtp_crypto'),
                'smtp_user' => $this->config->item('quoteweek_user'),
                'smtp_pass' => $this->config->item('quoteweek_pass'),
                'charset'=>'utf-8',
                'mailtype'=>'html',
                'wordwrap'=>TRUE,
                'newline' => "\r\n",
            ];
            $email_from = $this->config->item('quoteweek_user');
        } else {
            $config = [
                'charset' => 'utf-8',
                'mailtype' => 'html',
                'wordwrap' => TRUE,
            ];
            $email_from=$this->config->item('email_notification_sender');
        }
        $this->load->model('orders_model');
        $brands = ['SB','SR'];
        foreach ($brands as $brand) {
            $options['brand']=$brand;
            $res=$this->orders_model->get_week_quotes($options);
            if ($res['result']==1) {
                $title='Quotes, Proof Requests, Orders ('.date('m/d/Y', $monday).' - '.date('m/d/Y', $sunday-1).')';
                if ($brand=='SB') {
                    $title.=' Bluetrack/Stressballs';
                } elseif ($brand=='SR') {
                    $title.=' StressRelievers';
                }
                $params['lists']=$res['data'];
                $params['title']=$title;
                // Prepare email
                $body= $this->load->view('messages/quotesweek_report_view', $params, TRUE);
                $this->load->library('email');
                $this->email->initialize($config);
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
        $brands = ['SB','SR'];
        foreach ($brands as $brand) {
            $results=$this->orders_model->user_weekproof_reportdata($user_id, $brand);
            $out=$results['out'];
            $total=$results['totals'];
            $dateend=strtotime(date('m/d/Y'));
            $datestart = strtotime(date("Y-m-d",$dateend) . " -1 day");
            $sendsmtp = intval($brand=='SR' ? $this->config->item('sr_bonusreport_smtp') : $this->config->item('sb_bonusreport_smtp'));
            if ($sendsmtp==1) {
                $config = [
                    'protocol'=>'smtp',
                    'smtp_host' => $this->config->item('sb_smtp_host'),
                    'smtp_port' => $this->config->item('sb_smtp_port'),
                    'smtp_crypto' => $this->config->item('sb_smtp_crypto'),
                    'charset'=>'utf-8',
                    'mailtype'=>'html',
                    'wordwrap'=>TRUE,
                    'newline' => "\r\n",
                ];
                if ($brand=='SR') {
                    $config['smtp_user'] = $this->config->item('sr_bonusreport_user');
                    $config['smtp_pass'] = $this->config->item('sr_bonusreport_pass');
                    $email_from = $this->config->item('sr_bonusreport_user');
                } else {
                    $config['smtp_user'] = $this->config->item('sb_bonusreport_user');
                    $config['smtp_pass'] = $this->config->item('sb_bonusreport_pass');
                    $email_from = $this->config->item('sb_bonusreport_user');
                }
            } else {
                $config = [
                    'mailtype' => 'html',
                    'charset' => 'utf-8',
                    'wordwrap' => TRUE,
                ];
                $email_from = $this->config->item('email_notification_sender');
            }
            $this->load->library('email');
            $this->email->initialize($config);
            $email_to=$this->config->item('sean_email');
            $email_cc=$this->config->item('sage_email');
            $this->email->from($email_from);
            $this->email->to($email_to);
            $this->email->cc($email_cc);
            $this->email->bcc($this->config->item('developer_email'));
            $title=date('D - M d, Y', $datestart).' - Sales Report (Shanequa Hall) (Owners version) ';
            if ($brand=='SB') {
                $title.='(Bluetrack/Stressballs)';
            } elseif ($brand=='SR') {
                $title.='(StressRelievers)';
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
            $title=date('D - M d, Y', $datestart).' - Sales Report (Shanequa Hall) ';
            if ($brand=='BT') {
                $title.='(Bluetrack/Stressballs)';
            } elseif ($brand=='SB') {
                $title.='(StressRelievers)';
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
        $brands = ['SB','SR'];
        $email_conf = array(
            'protocol'=>'smtp',
            'smtp_host' => $this->config->item('sb_smtp_host'),
            'smtp_port' => $this->config->item('sb_smtp_port'),
            'smtp_crypto' => $this->config->item('sb_smtp_crypto'),
            'smtp_user' => $this->config->item('sb_quote_user'),
            'smtp_pass' => $this->config->item('sb_quote_pass'),
            'charset'=>'utf-8',
            'mailtype'=>'html',
            'wordwrap'=>TRUE,
            'newline' => "\r\n",
        );
        $email_from = $this->config->item('sb_quote_user');
        $email_to='to_german@yahoo.com';
        $this->load->model('orders_model');
        $this->load->library('email');
        foreach ($brands as $brand) {
            $data = $this->orders_model->check_ordermath($brand,$datestart, $dateend);
            $out = $data['data'];
            $orders = $data['orders'];
            if ($orders > 0) {
                if (count($out)==0) {
                    $mail_body = 'All orders '.$orders.' math is OK';
                } else {
                    $mail_body = $this->load->view('messages/order_maths_view', ['data'=>$out], TRUE);
                }
                $this->email->initialize($email_conf);
                $this->email->from($email_from);
                $this->email->to($email_to);

                $title=date('D - M d, Y', $datestart).' - Check Orders Maths ';
                if ($brand=='SB') {
                    $title.='Bluetrack/Stressballs';
                } elseif ($brand=='SR') {
                    $title.='StressRelievers';
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
        $brands = ['SB','SR'];
        // $brands = ['SB'];
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
            if ($brand=='SB') {
                $title.='(Bluetrack/Stressballs)';
            } elseif ($brand=='SR') {
                $title.='(StressRelievers)';
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
        // $this->email_model->generate_quota();
        $this->email_model->newquote_generate();
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
        $brands = ['SB','SR'];
        // $brands = ['SB'];
        $dat_mon = strtotime('last week Monday');
        $dat_sun = strtotime(date('Y-m-d', strtotime('last week Sunday')).' 23:59:59');
        $sendsmtp = intval($this->config->item('searchreport_smtp'));
        if ($sendsmtp==1) {
            $email_conf = [
                'protocol'=>'smtp',
                'smtp_host' => $this->config->item('sb_smtp_host'),
                'smtp_port' => $this->config->item('sb_smtp_port'),
                'smtp_crypto' => $this->config->item('sb_smtp_crypto'),
                'smtp_user' => $this->config->item('searchreport_user'),
                'smtp_pass' => $this->config->item('searchreport_pass'),
                'charset'=>'utf-8',
                'mailtype'=>'html',
                'wordwrap'=>TRUE,
                'newline' => "\r\n",
            ];
            $mail_from = $this->config->item('searchreport_user');
        } else {
            $email_conf = array(
                'protocol'=>'sendmail',
                'charset'=>'utf-8',
                'wordwrap'=>TRUE,
                'mailtype'=>'html',
            );
            $mail_from = 'no-replay@bluetrack.com';
        }
        $this->load->model('searchresults_model');
        $this->load->library('email');
        $mail_to=array('sean@bluetrack.com');
        $mail_cc=array('sage@bluetrack.com', $this->config->item('developer_email'));
        foreach ($brands as $brand) {
            $res = $this->searchresults_model->get_searchweekreport($dat_mon, $dat_sun, $brand);
            $mail_body=$this->load->view('marketing/weekreport_view',array('start_date'=>$dat_mon,'end_date'=>$dat_sun,'data'=>$res),TRUE);
            $this->email->initialize($email_conf);
            $this->email->to($mail_to);
            $this->email->cc($mail_cc);
            $this->email->from($mail_from);
            $title = 'Weekly Report about Unsuccessful Searches '.($brand=='SB' ? '(Bluetrack/Stressballs)' : '(StressRelievers)');
            $this->email->subject($title);
            $this->email->message($mail_body);
            $res=$this->email->send();
            $this->email->clear(TRUE);
        }
    }

    public function unpaid_orders() {
        $brands = ['SB', 'SR'];
        $yearbgn = intval(date('Y'))-1;
        $datebgn = strtotime($yearbgn.'-01-01');
        $this->load->model('orders_model');
        foreach ($brands as $brand) {
            $totals = $this->orders_model->get_updaid_totals($brand);
            $dat = $this->orders_model->get_unpaid_orders($datebgn, $brand);
            if (count($dat)==0) {
                $mail_body=$this->load->view('messages/notpaidorders_listempty_view',array(),TRUE);
            } else {
                $mail_body=$this->load->view('messages/notpaidorders_list_view',array('data'=>$dat,'totals'=>$totals),TRUE);
            }
            $sendsmtp = intval($brand=='SR' ? $this->config->item('sr_unpaid_smtp') : $this->config->item('sb_unpaid_smtp'));
            if ($sendsmtp==1) {
                $email_conf = [
                    'protocol'=>'smtp',
                    'smtp_host' => $this->config->item('sb_smtp_host'),
                    'smtp_port' => $this->config->item('sb_smtp_port'),
                    'smtp_crypto' => $this->config->item('sb_smtp_crypto'),
                    'charset'=>'utf-8',
                    'mailtype'=>'html',
                    'wordwrap'=>TRUE,
                    'newline' => "\r\n",
                ];
                if ($brand=='SR') {
                    $email_conf['smtp_user'] = $this->config->item('sr_unpaid_user');
                    $email_conf['smtp_pass'] = $this->config->item('sr_unpaid_pass');
                    $email_from = $this->config->item('sr_unpaid_user');
                } else {
                    $email_conf['smtp_user'] = $this->config->item('sb_unpaid_user');
                    $email_conf['smtp_pass'] = $this->config->item('sb_unpaid_pass');
                    $email_from = $this->config->item('sb_unpaid_user');
                }
            } else {
                $email_conf = array(
                    'protocol'=>'sendmail',
                    'charset'=>'utf-8',
                    'wordwrap'=>TRUE,
                    'mailtype'=>'html',
                );
                $email_from = $this->config->item('email_notification_sender');
            }
            $this->load->library('email');
            $this->email->initialize($email_conf);
            $mail_to=array($this->config->item('sage_email'));
            $mail_cc=array($this->config->item('developer_email'));

            $this->email->to($mail_to);
            $this->email->cc($mail_cc);
            $this->email->from($email_from);

            $title = 'Report about Unpaid Orders '.($brand=='SB' ? '(Bluetrack/Stressballs)' : '(StressRelievers)');
            $this->email->subject($title);
            $this->email->message($mail_body);
            $res=$this->email->send();
            $this->email->clear(TRUE);
        }
    }


    public function export_parse() {
        $this->load->model('artlead_model');
        $this->artlead_model->export_parse();
    }

    public function artclay_export() {
        $this->load->model('artlead_model');
        $this->artlead_model->artclay_export();
    }

    public function batchdailyreport()
    {
        $dateend=strtotime(date('m/d/Y'));
        $datestart = strtotime(date("Y-m-d",$dateend) . " -1 day");

        $this->load->model('batches_model');
        $brands = ['SB', 'SR'];
        $msgbody='';
        foreach ($brands as $brand) {
            $usrlist = $this->batches_model->batchreport_users($datestart, $dateend, $brand);
            if (count($usrlist) > 0) {
                $title = 'Amounts added to ';
                if ($brand=='SB') {
                    $title.='Bluetrack/Stressballs';
                } elseif ($brand=='SR') {
                    $title.='StressRelievers.com';
                }
                $msgbody.='<span style="font-weight: bold">'.$title.'</span><br/>';
                foreach ($usrlist as $row) {
                    $list = $this->batches_model->batchreport_data($datestart, $dateend, $row['user_id'], $brand);
                    if (empty($row['user_id'])) {
                        $row['user_name'] = 'WEB Order';
                    }
                    $opt=[
                        'title'=>date('D - M d, Y', $datestart).' - '.$row['user_name'],
                        'subtitle'=>'', // Newly Added Amounts:
                        'lists'=>$list,
                    ];
                    $msgbody.=$this->load->view('messages/batches_data_view', $opt, TRUE);
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
        $email_cc=array($this->config->item('sage_email'));
        $this->email->from($email_from);
        $this->email->to($email_to);
        $this->email->cc($email_cc);
        // Temporary ADD for check
        $this->email->bcc([$this->config->item('developer_email')]);
        $title=date('D - M d, Y', $datestart).' - Amouts added';
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

    public function clean_preload() {
        $path = $this->config->item('upload_path_preload');
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                if (is_file($path.$file)) {
                    if ((time()-filectime($path.$file)) > $this->config->item('maxstoretime')) {  // 30 * 24 * 60 * 60 - 30 days
                        echo $path.$file.PHP_EOL;
                        unlink($path.$file);
                    }
                }
            }
        }
    }

    public function cleanverification()
    {
        $this->load->model('user_model');
        $this->user_model->clean_verification();
    }

    public function orderitems_price_report()
    {
        $this->load->model('orders_model');
        $this->orders_model->orderitems_price_report();
    }

    public function change_incomeprices()
    {
        /* Array */
        $changes = [];
        $changes[] = [
            'item_num' => 'i001',
            'color' => 'Navy Blue',
            'income' => 'AJ02318',
            'new_price' => 0.6197,
        ];
//        $changes[] = [
//            'item_num' => 'i021',
//            'color' => 'Grass Green',
//            'income' => 'AJ01931',
//            'new_price' => 0.460,
//        ];
//        $changes[] = [
//            'item_num' => 'i021',
//            'color' => 'Red',
//            'income' => 'AJ01933',
//            'new_price' => 0.460,
//        ];
//        $changes[] = [
//            'item_num' => 'i021',
//            'color' => 'Yellow',
//            'income' => 'AJ01932',
//            'new_price' => 0.460,
//        ];
        foreach ($changes as $change) {
            $this->db->select('i.income_price, i.income_qty, i.income_expense, c.inventory_color_id, i.inventory_income_id, im.inventory_item_id');
            $this->db->from('ts_inventory_incomes i');
            $this->db->join('ts_inventory_colors c','c.inventory_color_id=i.inventory_color_id');
            $this->db->join('ts_inventory_items im','im.inventory_item_id=c.inventory_item_id');
            $this->db->where('im.item_num', $change['item_num']);
            $this->db->where('c.color', $change['color']);
            $this->db->where('i.income_record', $change['income']);
            $candidat = $this->db->get()->row_array();
            if (ifset($candidat,'inventory_color_id',0)==0) {
                echo 'Color '.$change['color'].' Income '.$change['income'].' not found'.PHP_EOL;
                echo 'QRY '.$this->db->last_query().PHP_EOL;
                die();
            } else {
                echo 'Color '.$change['color'].' QTY '.$candidat['income_qty'].' Rest '.$candidat['income_expense'].' Price '.$candidat['income_price'].' New Price '.$change['new_price'].' Check '.$candidat['inventory_income_id'].PHP_EOL;
                $this->db->select('oi.order_id, oi.amount_id, oi.qty, o.order_cog, o.profit, o.revenue, o.profit_perc');
                $this->db->from('ts_order_inventory oi');
                $this->db->join('ts_orders o','oi.order_id=o.order_id');
                $this->db->where('oi.inventory_income_id', $candidat['inventory_income_id']);
                $amnts = $this->db->get()->result_array();
                foreach ($amnts as $amnt) {
                    $this->db->select('oa.amount_id, oa.price, oa.shipped, oa.misprint, oa.kepted, i.income_price, oi.qty, i.inventory_income_id');
                    $this->db->select('oa.orangeplate, oa.blueplate, oa.orangeplate_price, oa.blueplate_price, oa.beigeplate, oa.beigeplate_price, oa.extracost');
                    $this->db->from('ts_order_amounts oa');
                    $this->db->join('ts_order_inventory oi','oi.amount_id=oa.amount_id');
                    $this->db->join('ts_inventory_incomes i','i.inventory_income_id=oi.inventory_income_id');
                    $this->db->where('oa.amount_id',$amnt['amount_id']);
                    $amtdatas = $this->db->get()->result_array();
                    $sumtotal = 0;
                    $sumqty = 0;
                    foreach ($amtdatas as $amtdata) {
                        if ($amtdata['inventory_income_id']==$candidat['inventory_income_id']) {
                            $price = $change['new_price'];
                        } else {
                            $price = $amtdata['price'];
                        }
                        $sumqty+=$amtdata['shipped']+$amtdata['misprint']+$amtdata['kepted'];
                        $sumtotal+=$price * ($amtdata['shipped']+$amtdata['misprint']+$amtdata['kepted']);
                    }
                    $amtprice = round($sumtotal/$sumqty,3); // +$amtdata['extracost'];
                    echo 'Amount '.$amtdata['amount_id'].' Old Price '.$amtdata['price'].' New Price '.$amtprice.PHP_EOL;
                    $amounttotal = $sumtotal+($amtdata['extracost']*$sumqty)+($amtdata['orangeplate']*$amtdata['orangeplate_price'])+($amtdata['blueplate']*$amtdata['blueplate_price'])+($amtdata['beigeplate']*$amtdata['beigeplate_price']);
                    // Update Amount
                    $this->db->where('amount_id', $amnt['amount_id']);
                    $this->db->set('price', $amtprice);
                    $this->db->set('printshop_total', $amounttotal);
                    $this->db->set('amount_sum', $amounttotal);
                    $this->db->update('ts_order_amounts');
                    // Update Order
                    // New cog, profit, profit percent
                    $diffcog = $amounttotal - $amnt['order_cog'];
                    $newprofit = $amnt['profit'] - $diffcog;
                    $newprofit_perc = round($newprofit/$amnt['revenue']*100,1);
                    $this->db->where('order_id', $amnt['order_id']);
                    $this->db->set('order_cog', $amounttotal);
                    $this->db->set('profit', $newprofit);
                    $this->db->set('profit_perc', $newprofit_perc);
                    $this->db->update('ts_orders');
                }
                // Update income
                $this->db->where('inventory_income_id', $candidat['inventory_income_id']);
                $this->db->set('income_price', $change['new_price']);
                $this->db->update('ts_inventory_incomes');
            }
        }
    }

    public function update_calendars() {
        $this->load->model('calendars_model');
        $this->calendars_model->update_calendars();
    }

    public function merchantcenter_items()
    {
        $this->load->model('items_model');
        $this->items_model->merchantcenter_items('BT');
    }

    public function artuploadreport()
    {
        $start = strtotime(date('Y-m-d') . ' - 1 day');
        $datebgn = strtotime(date('Y-m-d',$start));
        $dateend = strtotime(date('Y-m-d'));
        $this->load->model('artwork_model');
        $this->artwork_model->artproof_report($datebgn, $dateend);
    }

    public function trackreport()
    {
        $start = strtotime(date('Y-m-d') . ' - 1 day');
        $datebgn = strtotime(date('Y-m-d',$start));
        $dateend = strtotime(date('Y-m-d'));

    }
}