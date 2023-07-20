<?php

class Export extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function claypreview_export() {
        // if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $postdata = $this->input->post();
            foreach ($postdata as $key => $val) {
                log_message('ERROR','POST '.$key.' - '.$val);
            }
            $this->db->set('order_number', $postdata['order_number']);
            $this->db->set('doc_type', $postdata['doc_type']);
            $this->db->set('doc_link', $postdata['doc_link']);
            $this->db->set('doc_name', $postdata['doc_name']);
            $this->db->insert('lift_exports');
            $aResponse = array(
                'data' => [$this->db->insert_id()],
                'errors' => ''
            );
            http_response_code('200');
            echo(json_encode($aResponse));
            exit;
//        } else {
//            $aResponse = array(
//                'data' => [],
//                'errors' => 'Incorrect request'
//            );
//            http_response_code('400');
//            echo(json_encode($aResponse));
//            exit;
//        }
    }
}