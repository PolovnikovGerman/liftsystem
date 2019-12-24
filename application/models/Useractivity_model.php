<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Useractivity_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function addactivity($user_id, $action='', $activity=0) {
        $user_ip=$this->input->ip_address();
        $url = current_url();
        $this->db->set('user_id', $user_id);
        $this->db->set('user_ip', $user_ip);
        $this->db->set('user_url', $url);
        if (!empty($action)) {
            $this->db->set('action', $action);
        }
        $this->db->set('action_time', time());
        $this->db->set('activity', $activity);
        $this->db->insert('ts_user_activities');
    }

    public function get_user_filter() {
        $this->db->select('user_id, user_name');
        $this->db->from('users');
        $this->db->order_by('user_name');
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function get_count_logrecords($option=[]) {
        $this->db->select('count(user_activity_id) as cnt');
        $this->db->from('ts_user_activities');
        if (isset($option['user_id'])) {
            $this->db->where('user_id', $option['user_id']);
        }
        if (isset($option['start_date'])) {
            $this->db->where('action_time >= ', $option['start_date']);
        }
        if (isset($option['finish_date'])) {
            $this->db->where('action_time < ', $option['finish_date']);
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_actions_log($options=[]) {
        $start = $options['start']; // * ($options['display']);
        $offset = $options['display'];
        $this->db->select('u.user_name, l.user_ip, l.action, date_format(from_unixtime(l.action_time),\'%m/%d/%y %H:%i:%s\') as action_time, l.user_url', FALSE);
        $this->db->from('ts_user_activities l');
        $this->db->join('users u','u.user_id=l.user_id');
        if (isset($options['user_id'])) {
            $this->db->where('l.user_id', $options['user_id']);
        }
        if (isset($options['start_date'])) {
            $this->db->where('l.action_time >= ', $options['start_date']);
        }
        if (isset($options['finish_date'])) {
            $this->db->where('action_time < ', $options['finish_date']);
        }
        if (isset($options['order_by'])) {
            if (isset($options['direct'])) {
                $this->db->order_by($options['order_by'], $options['direct']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        } else {
            $this->db->order_by('l.user_activity_id');
        }
        $this->db->limit($offset, $start);
        $res = $this->db->get()->result_array();

        return $res;

    }
}