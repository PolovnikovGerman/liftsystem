<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Description of login_model
 *
 * @author german polovnikov
 */

class Email_model extends My_Model
{

    private $INIT_MSG = 'Unknown error. Try later';

    function __construct()
    {
        parent::__construct();
    }

    public function get_email_templates() {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->order_by('email_template_id');
        $res=$this->db->get()->result_array();
        return $res;
    }

    public function get_main_template() {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->order_by('email_template_id');
        $this->db->limit(1);
        $res=$this->db->get()->row_array();
        return $res;

    }

    public function get_email_template($template) {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->where('email_template_id',$template);
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function get_emailtemplate_byname($template_name) {
        $this->db->select('*');
        $this->db->from('ts_email_templates');
        $this->db->where('email_template_name',$template_name);
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function save_template($email_template_id, $email_template_body, $email_template_subject,$email_template_address) {
        $out=array('result'=>  $this->error_result, 'msg'=>  $this->INIT_MSG);
        if (!$email_template_id) {
            $out['msg']='Unknown Email Template';
        } elseif (empty($email_template_subject)) {
            $out['msg']='Enter Email Subject';
        } elseif (empty($email_template_body)) {
            $out['msg']='Enter Email Body';
            // }elseif (empty($email_template_address)) {
            //    $out['msg']='Enter Sender Email';
        } elseif (!empty($email_template_address) && !$this->func->valid_email_address($email_template_address)) {
            $out['msg']='Enter Correct Sender Email';
        } else {
            $this->db->set('email_template_body',$email_template_body);
            $this->db->set('email_template_subject',$email_template_subject);
            $this->db->set('email_template_address',$email_template_address);
            $this->db->where('email_template_id',$email_template_id);
            $this->db->update('ts_email_templates');
            $out['result']=  $this->success_result;
            $out['msg']='';
        }
        return $out;
    }

    public function logsendmail($options) {
        $fl_ins=0;
        if (isset($options['from'])) {
            $fl_ins=1;
            $mailfrom='';
            if (is_array($options['from'])) {
                foreach ($options['from'] as $row) {
                    $mailfrom.=$row.', ';
                }
                $mailfrom=substr($mailfrom ,0,-2);
            } else {
                $mailfrom=$options['from'];
            }
            $this->db->set('from',$mailfrom);
        }
        if (isset($options['to'])) {
            $fl_ins=1;
            $mailto='';
            if (is_array($options['to'])) {
                foreach ($options['to'] as $row) {
                    $mailto.=$row.', ';
                }
                $mailto=substr($mailto,0,-2);
            } else {
                $mailto=$options['to'];
            }
            $this->db->set('to',$mailto);
        }
        if (isset($options['cc'])) {
            $fl_ins=1;
            $mailcc='';
            if (is_array($options['cc'])) {
                foreach ($options['cc'] as $row) {
                    $mailcc.=$row.', ';
                }
                $mailcc=substr($mailcc,0,-2);
            } else {
                $mailcc=$options['cc'];
            }
            $this->db->set('cc',$mailcc);
        }
        if (isset($options['message'])) {
            $fl_ins=1;
            $this->db->set('body',$options['message']);
        }
        if (isset($options['attachments'])) {
            $fl_ins=1;
            $mailattach='';
            if (is_array($options['attachments'])) {
                foreach ($options['attachments'] as $row) {
                    $mailattach.=$row.PHP_EOL;
                }
            } else {
                $mailattach=$options['attachments'];
            }
            $this->db->set('attachments',$mailattach);
        }
        if (isset($options['result'])) {
            $fl_ins=1;
            $this->db->set('result',$options['result']);
        }
        if (isset($options['user_id'])) {
            $fl_ins=1;
            $this->db->set('user_id',$options['user_id']);
        }
        if (isset($options['subject'])) {
            $fl_ins=1;
            $this->db->set('subject',$options['subject']);
        }
        if ($fl_ins==1) {
            $this->db->set('message_time',time());
            $this->db->insert('ts_sendmaillogs');
        }
        return TRUE;
    }

}

