<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Creditapp_model extends My_Model
{
    private $error_message='Unknown error. Try later';
    private $pending_status='pending';
    private $approve_status='approved';
    private $rejected_status='rejected';

    function __construct()
    {
        parent::__construct();
    }

    public function get_creditapp_total($options=array()) {
        $this->db->select('count(creditapp_line_id) as cnt');
        $this->db->from('ts_creditapp_lines');
        if (isset($options['stattus']) && !empty($options['status'])) {
            $this->db->where('status', $options['status']);
        }
        if (isset($options['search']) && !empty($options['search'])) {
            $this->db->like("concat(upper(customer),upper(coalesce(phone,'')),upper(coalesce(email,''))) ", $options['search']);
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

//    public function get_creditapp_data($options) {
//        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
//        $this->db->select('a.creditapp_line_id, a.status, a.customer, a.abbrev, a.phone, a.email, a.notes');
//        $this->db->select('u.first_name as reviewby, document_link');
//        $this->db->from('ts_creditapp_lines a');
//        $this->db->join('users u', 'u.user_id=a.revieved_user','left');
//        if (isset($options['stattus']) && !empty($options['status'])) {
//            $this->db->where('a.status', $options['status']);
//        }
//        if (isset($options['search']) && !empty($options['search'])) {
//            $this->db->like("concat(upper(a.customer),upper(coalesce(a.phone,'')),upper(coalesce(a.email,''))) ", $options['search']);
//        }
//        $data=$this->db->get()->result_array();
//        $out['result']=$this->success_result;
//        $out['data']=$data;
//        return $out;
//    }
//
//    // New order
//    public function new_creditapp($user_id) {
//        $fldlist=$this->db->list_fields('ts_creditapp_lines');
//        $data=array();
//        foreach ($fldlist as $fld) {
//            switch ($fld) {
//                case 'creditapp_line_id':
//                    $data[$fld]=-1;
//                    break;
//                case 'created_user':
//                case 'update_user':
//                    $data[$fld]=$user_id;
//                    break;
//                case 'status':
//                    $data[$fld]=$this->pending_status;
//                    break;
//                default :
//                    $data[$fld]='';
//                    break;
//            }
//        }
//        return $data;
//    }
//
//    // Get data about separate APP
//    public function get_creditapp($creditapp) {
//        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
//        $this->db->select('a.*');
//        $this->db->select('u.first_name as reviewby');
//        $this->db->from('ts_creditapp_lines a');
//        $this->db->join('users u', 'u.user_id=a.revieved_user','left');
//        $this->db->where('creditapp_line_id', $creditapp);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['creditapp_line_id'])) {
//            $out['msg']='Credit App Line not found';
//        } else {
//            $out['result']=$this->success_result;
//            $out['data']=$res;
//        }
//        return $out;
//    }
//
//    // Change Value
//    public function creditapp_change($data, $fldname, $newval) {
//        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
//        if (!array_key_exists($fldname, $data)) {
//            $out['msg']='Unknown Parameter for change '.$fldname;
//            return $out;
//        }
//        $data[$fldname]=$newval;
//        $out['result']=$this->success_result;
//        $this->func->session('creditapplinedata', $data);
//        return $out;
//    }
//
//    // Save Edited Credit APP
//    public function creditapp_save($data, $user_id) {
//        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
//        if (!empty($data['email']) && !$this->func->valid_email_address($data['email'])) {
//            $out['msg']=  lang_str('errmsg_nonvalidemail',TRUE);
//            return $out;
//        }
//        if (empty($data['customer'])) {
//            $out['msg']=  lang_str('errmsg_empty_customer',TRUE);
//            return $out;
//        }
//        if (empty($data['email']) && empty($data['phone'])) {
//            $out['msg']='Contact Info Empty';
//            return $out;
//        }
//        // Save
//        $this->db->set('status', $data['status']);
//        $this->db->set('customer', $data['customer']);
//        $this->db->set('abbrev', $data['abbrev']);
//        $this->db->set('phone', $data['phone']);
//        $this->db->set('email', $data['email']);
//        $this->db->set('notes', $data['notes']);
//        if (isset($data['document_link'])) {
//            $this->db->set('document_link', $data['document_link']);
//        }
//        if ($data['creditapp_line_id']<0) {
//            $this->db->set('created_user', $user_id);
//            $this->db->set('create_time', date('Y-m-d H:i:s'));
//            $this->db->set('update_user', $user_id);
//            $this->db->insert('ts_creditapp_lines');
//        } else {
//            $this->db->where('creditapp_line_id', $data['creditapp_line_id']);
//            $this->db->set('update_user', $user_id);
//            $this->db->update('ts_creditapp_lines');
//        }
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
//    public function creditapp_preapprove($creditapp, $user_id) {
//        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
//        $this->load->model('user_model');
//        $usrdat=$this->user_model->get_user_data($user_id);
//
//        if (!isset($usrdat['user_id'])) {
//            $out['msg']='Unknown User';
//            return $out;
//        }
//        if ($usrdat['user_status']>1) {
//            $out['msg']='Unactive User';
//            return $out;
//        }
//        if ($usrdat['finuser']==0) {
//            $out['msg']='You have no premissions for manage Credit APP Lines';
//            return $out;
//        }
//        // All OK
//        $this->db->select('*');
//        $this->db->from('ts_creditapp_lines');
//        $this->db->where('creditapp_line_id', $creditapp);
//        $data=$this->db->get()->row_array();
//        if (!isset($data['creditapp_line_id'])) {
//            $out['msg']='Credit App Not Exist';
//            return $out;
//        }
//        $out['result']=$this->success_result;
//        $out['data']=$data;
//        return $out;
//    }
//
//    // Approve - reject
//    public function creditapp_approve($postdata, $user_id) {
//        $out=array('result'=>  $this->error_result, 'msg'=>$this->error_message);
//        if (!isset($postdata['creditapp'])) {
//            $out['msg']='Unknown Credit App Line';
//            return $out;
//        }
//        if (!isset($postdata['approve']) || !isset($postdata['reject'])) {
//            $out['msg']='Unknown Credit App Deed';
//            return $out;
//        }
//        $this->db->set('update_user', $user_id);
//        $this->db->set('revieved_user', $user_id);
//        if (isset($postdata['notes']) && !empty($postdata['notes'])) {
//            $this->db->set('review_notes', $postdata['notes']);
//        }
//        if ($postdata['approve']==1) {
//            $this->db->set('status', $this->approve_status);
//        } elseif ($postdata['reject']==1) {
//            $this->db->set('status', $this->rejected_status);
//        }
//        $this->db->where('creditapp_line_id', $postdata['creditapp']);
//        $this->db->update('ts_creditapp_lines');
//        $out['result']=$this->success_result;
//        return $out;
//    }
//
    public function order_creditapp($order_id) {
        $this->db->select('*');
        $this->db->from('ts_creditapp_lines');
        $this->db->where('order_id', $order_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function update_order_creditapp($datapp) {
        $this->db->set('update_user', $datapp['user']);
        $this->db->set('document_link', $datapp['document_link']);
        if ($datapp['credit_app_id']==0) {
            $this->db->set('created_user', $datapp['user']);
            $this->db->set('update_user', $datapp['user']);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('customer', $datapp['customer']);
            $this->db->set('order_id', $datapp['order_id']);
            $this->db->insert('ts_creditapp_lines');
        } else {
            $this->db->set('update_user', $datapp['user']);
            $this->db->where('creditapp_line_id', $datapp['credit_app_id']);
            $this->db->update('ts_creditapp_lines');
        }
        return TRUE;
    }

    // Remove not used APP
    public function remove_order_creditapp($order_id) {
        $this->db->where('order_id', $order_id);
        $this->db->delete('ts_creditapp_lines');
        return TRUE;
    }

}