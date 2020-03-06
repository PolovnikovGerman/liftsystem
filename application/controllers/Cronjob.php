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

    public function test_mail() {
        $this->load->library('email');
        $config['charset'] = 'utf-8';
        $config['mailtype']='html';
        $config['wordwrap'] = TRUE;

        $this->email->initialize($config);

        $email_from='grey@bluetrack.com';

        // $email_to='roy.ferrer@bluetrack.com';
        $email_to='to_german@yahoo.com';

        $email_body='At '.date('hA:i').' on '.date('m/d/y');
        $email_body.'into a Closed Order.';

        $this->email->from($email_from);
        $this->email->to($email_to);
        $subj="Roy Ferrer closed Lead #018-854";
        $this->email->subject($subj);
        $this->email->message($email_body);
        $this->email->send();
        $this->email->clear(TRUE);
        return TRUE;

    }


}