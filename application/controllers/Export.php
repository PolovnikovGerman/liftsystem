<?php

class Export extends CI_Controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function claypreview_export() {
        $postdata = $this->input->post();
        if (isset($postdata['order_number']) && isset($postdata['doc_type']) && isset($postdata['doc_link']) && isset($postdata['doc_name'])) {
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
        }
        exit;
    }
}