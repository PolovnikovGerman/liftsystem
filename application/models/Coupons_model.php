<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Coupons_model extends MY_Model
{

    private $INIT_ERRMSG = 'Unknown error. Try later';
    private $init_number = 10000;
    private $init_lead_type = 2;

    function __construct()
    {
        parent::__construct();
    }
    
//    function get_coupons_number($options=array()) {
//        $this->db->select('count(coupon_id) as cnt',FALSE);
//        $this->db->from('sb_coupons');
//        foreach ($options as $key=>$value) {
//            $this->db->where($key,$value);
//        }
//        $res=$this->db->get()->row_array();
//        return $res['cnt'];
//    }
//
//    function get_coupons($options,$order_by,$direction) {
//        $this->db->select('*');
//        $this->db->from('sb_coupons');
//        foreach ($options as $key=>$value) {
//            if ($key=='coupon_code') {
//                $this->db->where("replace(coupon_code,'-','')", str_replace(array(' ','-'), '', $value));
//            } else {
//                $this->db->where($key,$value);
//            }
//        }
//        $this->db->order_by($order_by,$direction);
//        $results=$this->db->get()->result_array();
//        return $results;
//    }
//
//    function del_coupon($coupon_id) {
//        $this->db->set('coupon_deleted',1);
//        $this->db->where('coupon_id',$coupon_id);
//        $this->db->update('sb_coupons');
//
//        return $this->db->affected_rows();
//    }
//
//    function update_coupon($details) {
//        $out=array('error'=>'','result'=>0);
//        /* Check data */
//
//        $err_msg='';
//        if ($details['discount_perc']!='' && !is_numeric($details['discount_perc'])) {
//            $err_msg.='Discount percent is not number.'.PHP_EOL;
//        }
//        if (is_numeric($details['discount_perc']) && $details['discount_perc']>=100) {
//            $err_msg.='Discount percent must be less then 100%.'.PHP_EOL;
//        }
//        if ($details['discount_sum']!='' && !is_numeric($details['discount_sum'])) {
//            $err_msg.='Discount sum is not number.'.PHP_EOL;
//        }
//        if ($details['discount_perc']!='' && is_numeric($details['discount_perc']) && $details['discount_sum']!='' && is_numeric($details['discount_sum'])) {
//            if ($details['discount_perc']!=0 && $details['discount_sum']!=0) {
//                $err_msg.='You can apply only one type of discount - sum or percent.'.PHP_EOL;
//            }
//        }
//
//        if ($details['minlimit']!='' && !is_numeric($details['minlimit'])) {
//            $err_msg.='Min limit of sum is not number.'.PHP_EOL;
//        }
//        if ($details['maxlimit']!='' && !is_numeric($details['maxlimit'])) {
//            $err_msg.='Max limit of sum is not number.'.PHP_EOL;
//        }
//        if (empty($details['description'])) {
//            $err_msg.='Coupon description is mandatory field.'.PHP_EOL;
//        }
//        if (empty($details['code1']) || empty($details['code2'])) {
//            $err_msg.='Coupon code is mandatory fields and consists of 2 parts'.PHP_EOL;
//        }
//
//        if ($details['minlimit']!='' && is_numeric($details['minlimit']) && $details['maxlimit']!='' && is_numeric($details['maxlimit']) && $details['minlimit']>$details['maxlimit']) {
//            $err_msg.='Max limit of sum is less then min limit of sum.'.PHP_EOL;
//        }
//
//        if (strlen($details['code1'])<3 || strlen($details['code2'])<3) {
//            $err_msg.="Coupon's code must contain 6 symbols".PHP_EOL;
//        } else {
//            /* Check unique */
//            $new_code=strtoupper($details['code1']).'-'.strtoupper($details['code2']);
//            $this->db->select('count(coupon_id) as cnt',FALSE);
//            $this->db->from('sb_coupons');
//            $this->db->where('coupon_id !=',$details['coupon_id']);
//            $this->db->where('coupon_code',$new_code);
//            $this->db->where('coupon_deleted',0);
//            $res=$this->db->get()->row_array();
//            $nrec=$res['cnt'];
//            if ($nrec!=0) {
//                /* Code not Unique */
//                $err_msg.="Entered coupon's code is not unique";
//            }
//        }
//
//        /* RESULT of checking */
//        if ($err_msg!='') {
//            $out['error']=$err_msg;
//        } else {
//            /* 'coupon_id','pub','','','','','','code1','code2' */
//            $new_code=strtoupper($details['code1']).'-'.strtoupper($details['code2']);
//            $this->db->set('coupon_ispublic',$details['pub']);
//            $this->db->set('coupon_discount_perc',$details['discount_perc']);
//            $this->db->set('coupon_discount_sum',$details['discount_sum']);
//            $this->db->set('coupon_minlimit',$details['minlimit']);
//            $this->db->set('coupon_maxlimit',$details['maxlimit']);
//            $this->db->set('coupon_description',$details['description']);
//            $this->db->set('coupon_code',$new_code);
//            if ($details['coupon_id']==0) {
//                $this->db->insert('sb_coupons');
//                $out['res']=$this->db->insert_id();
//            } else {
//                $this->db->where('coupon_id',$details['coupon_id']);
//                $this->db->update('sb_coupons');
//                $out['res']=1;
//            }
//        }
//        return $out;
//
//    }
//
//    public function coupon_activate($options) {
//        $out=['msg'=>'', 'result'=>FALSE];
//        $this->db->where('coupon_id', $options['coupon_id']);
//        $this->db->set('coupon_ispublic', $options['coupon_ispublic']);
//        $this->db->update('sb_coupons');
//        $out['result']=TRUE;
//        return $out;
//    }

}