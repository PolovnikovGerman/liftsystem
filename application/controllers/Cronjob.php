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

}