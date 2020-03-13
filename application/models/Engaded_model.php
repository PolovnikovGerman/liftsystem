<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Engaded_model extends My_Model
{

    private $error_message='Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

    // Check lock of entity
    public function check_engade($options) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->db->select('e.engaged_record_id, e.engaded_user, e.engaded_update, u.user_name');
        $this->db->from('ts_engaged_records e');
        $this->db->join('users u','u.user_id=e.engaded_user','left');
        $this->db->where('entity', $options['entity']);
        $this->db->where('entity_id', $options['entity_id']);
        $this->db->order_by('engaded_update');
        $res=$this->db->get()->row_array();
        if (!isset($res['engaged_record_id'])) {
            $out['result']=$this->success_result;
        } else {
            $upddiff=time()-$res['engaded_update'];
            if ($upddiff>=$this->config->item('max_lock_time')) {
                $this->db->where('engaged_record_id', $res['engaged_record_id']);
                $this->db->delete('ts_engaged_records');
                $out['result']=$this->success_result;
            } else {
                $out['lockrec']=$res['engaged_record_id'];
                $out['lockusr']=$res['user_name'];
            }
        }
        return $out;
    }

    // Lock record for edit
    public function lockentityrec($options) {
        // Options - entity, entity_id, user_id
        $this->db->set('entity', $options['entity']);
        $this->db->set('entity_id', $options['entity_id']);
        $this->db->set('engaded_user', $options['user_id']);
        $this->db->set('engaded_update', time());
        $this->db->insert('ts_engaged_records');
        return $this->db->insert_id();
    }

    // Update lock record for edit
    public function update_lockedid($locrecid) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->db->select('engaged_record_id');
        $this->db->from('ts_engaged_records');
        $this->db->where('engaged_record_id', $locrecid);
        $chkres=$this->db->get()->row_array();
        if (!isset($chkres['engaged_record_id'])) {
            $out['msg']='Record Not Found';
            return $out;
        }
        $this->db->set('engaded_update', time());
        $this->db->where('engaged_record_id', $locrecid);
        $this->db->update('ts_engaged_records');
        $out['result']=$this->success_result;
        return $out;
    }

    // Clean locked rec
    public function clean_engade($locrecid) {
        $this->db->where('engaged_record_id', $locrecid);
        $this->db->delete('ts_engaged_records');
        return TRUE;

    }

}