<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Customform_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_count_forms($params) {
        $this->db->select('count(custom_quote_id) as cnt');
        $this->db->from('ts_custom_quotes');
        if (ifset($params,'assign')) {

        }
        if (ifset($params,'brand','')!=='') {
            $this->db->where('brand', $params['brand']);
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_customform_data($options) {
        $pagenum = ifset($options,'offset',0);
        $limit = ifset($options,'limit',0);
        $offset = $pagenum * $limit;
        $this->db->select('*');
        $this->db->from('ts_custom_quotes');
        if (ifset($options,'search','')!=='') {
            // Search by customer, company, email
        }
        if (ifset($options,'assign','')!=='') {
            // Assign
        }
        if (ifset($options,'brand','')!=='') {
            $this->db->where('brand', $options['brand']);
        }
        if (ifset($options,'hideincl',0)==1) {
            $this->db->where('active', $options['hideincl']);
        }
        if ($limit !==0 ) {
            if ($offset !==0) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        if (ifset($options,'order_by','')!=='') {
            if (ifset($options,'direction','')!=='') {
                $this->db->order_by($options['order_by'], $options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }

        $dats = $this->db->get()->result_array();
        $out = [];
        $numpp = $offset;
        foreach ($dats as $dat) {
            $numpp++;
            $dat['numpp'] = $numpp;
            $out[] = $dat;
        }
        return $out;
    }

    public function get_customform_details($custom_quote_id) {
        $out=['result' => $this->error_result, 'msg' => 'Info doesn\'t found'];
        $this->db->select('q.*, c.country_name, c.country_iso_code_2');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_countries c','c.country_id=q.ship_country','left');
        $this->db->where('q.custom_quote_id', $custom_quote_id);
        $data = $this->db->get()->row_array();
        if (ifset($data,'custom_quote_id',0)==$custom_quote_id) {
            $out['result'] = $this->success_result;
            $out['data'] = $data;
            // Attachments
            $this->db->select('*');
            $this->db->from('ts_customquote_attachment');
            $this->db->where('custom_quote_id', $custom_quote_id);
            $attach = $this->db->get()->result_array();
            $out['attach'] = $attach;
        }
        return $out;
    }

}