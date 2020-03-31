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
        $brands = ['BT', 'SB'];
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

            $this->email->from($email_from);
            $mail_subj = 'Orders in PROJ stage ' . date('m/d/Y');
            if ($brand=='BT') {
                $mail_subj.=' (Bluetrack.com)';
            } elseif ($brand=='SB') {
                $mail_subj.=' (Stressball.com)';
            }
            $this->email->subject($mail_subj);
            $this->email->message($message_body);
            $this->email->send();
            $this->email->clear(TRUE);
        }
    }

}