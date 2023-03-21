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
        if (ifset($params,'search','')!=='') {
            $this->db->like('concat(customer_name, customer_company,customer_email)', $params['search']);
        }
        if (ifset($params,'hideincl',0)==1) {
            $this->db->where('active', $params['hideincl']);
        }
        if (ifset($params,'brand','')!=='') {
            if ($params['brand']=='SR') {
                $this->db->where('brand', $params['brand']);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_customform_data($options) {
        $pagenum = ifset($options,'offset',0);
        $limit = ifset($options,'limit',0);
        $offset = $pagenum * $limit;
        $this->db->select('q.*,le.lead_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        if (ifset($options,'search','')!=='') {
            // Search by customer, company, email
            $this->db->like('concat(q.customer_name, q.customer_company,q.customer_email)', $options['search']);
        }
        if (ifset($options,'assign','')==1) {
            // Assign
            $this->db->where('le.leademail_id is null');
        }
        if (ifset($options,'brand','')!=='') {
            if ($options['brand']=='SR') {
                $this->db->where('q.brand', $options['brand']);
            } else {
                $this->db->where_in('q.brand', ['BT','SB']);
            }
        }
        if (ifset($options,'hideincl',0)==1) {
            $this->db->where('q.active', $options['hideincl']);
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
        $this->load->model('leads_model');
        foreach ($dats as $dat) {
            $numpp++;
            // $dat['numpp'] = $numpp;
            $dat['numpp'] = $dat['quote_number'];
            if (!empty($dat['lead_id'])) {
                $ldat = $this->leads_model->get_lead($dat['lead_id']);
                $dat['lead_number']=ifset($ldat,'lead_number','');
            }
            if (empty($dat['ship_date'])) {
                $dat['event_date'] = '';
            } else {
                $dat['event_date'] = date('m/d/y', $dat['ship_date']);
            }
            $out[] = $dat;
        }
        return $out;
    }

    public function get_customform_details($custom_quote_id) {
        $out=['result' => $this->error_result, 'msg' => 'Info doesn\'t found'];
        $this->db->select('q.*, c.country_name, c.country_iso_code_2, le.lead_id, le.leademail_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_countries c','c.country_id=q.ship_country','left');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        $this->db->where('q.custom_quote_id', $custom_quote_id);
        $data = $this->db->get()->row_array();
        if (ifset($data,'custom_quote_id',0)==$custom_quote_id) {
            $out['result'] = $this->success_result;
            if (empty($data['lead_id'])) {
                $data['lead_date'] = $data['lead_customer'] = $data['lead_mail'] = '';
            } else {
                $this->db->select('lead_date, lead_customer, lead_mail');
                $this->db->from('ts_leads');
                $this->db->where('lead_id', $data['lead_id']);
                $leaddat = $this->db->get()->row_array();
                $data['lead_date'] = $leaddat['lead_date'];
                $data['lead_customer'] = $leaddat['lead_customer'];
                $data['lead_mail'] = $leaddat['lead_mail'];
            }
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

    public function update_customforn($options) {
        $this->db->where('custom_quote_id', $options['form_id']);
        $this->db->set('active', ifset($options,'activity', 0));
        $this->db->update('ts_custom_quotes');
        return true;
    }

}