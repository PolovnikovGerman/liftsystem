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
    
    public function get_coupons_number($options=array()) {
        $this->db->select('count(coupon_id) as cnt',FALSE);
        $this->db->from('sb_coupons');
        foreach ($options as $key=>$value) {
            if ($key!=='brand') {
                $this->db->where($key,$value);
            }
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('brand', $options['brand']);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_coupons($options) {
        $this->db->select('*');
        $this->db->from('sb_coupons');
        if (isset($options['coupon_code']) && !empty($options['coupon_code'])) {
            $this->db->where("replace(coupon_code,'-','')", str_replace(array(' ','-'), '', $value));
        }
        if (isset($options['coupon_deleted'])) {
            $this->db->where('coupon_deleted',$options['coupon_deleted']);
        }
        if (isset($options['brand']) && $options['brand']!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('brand', $options['brand']);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        if (isset($options['order_by']) && !empty($options['order_by'])) {
            if (isset($options['direction']) && !empty($options['direction'])) {
                $this->db->order_by($options['order_by'],$options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }
        if (isset($options['limit']) && !empty($options['limit'])) {
            if (isset($options['offset']) && !empty($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        $results=$this->db->get()->result_array();
        return $results;
    }

    public function new_coupon($brands) {
        $out = ['result' => $this->error_result, 'msg'=>'No permissions'];
        $data = [
            'coupon_id' => '-1',
            'coupon_ispublic' => 0,
            'coupon_discount_perc' => '',
            'coupon_discount_sum' => '',
            'coupon_minlimit' => '',
            'coupon_maxlimit' => '',
            'coupon_description' => '',
            'coupon_code1' => '',
            'coupon_code2' => '',
            'brand' => '',
        ];
        $brand_options = [];
        foreach ($brands as $row) {
            if ($row['brand']!=='ALL') {
                $brand_options[] = [
                    'brand' => $row['brand'],
                    'label' => $row['label'],
                ];
            }
        }
        if (count($brand_options)>0) {
            $data['brand']=$brand_options[0]['brand'];
            $out['result'] = $this->success_result;
            $out['data'] = $data;
            $out['brands'] = $brand_options;
            $out['percent_lock'] = 0;
            $out['money_lock'] = 0;

        }
        return $out;
    }

    public function get_coupon_details($coupon_id, $brands) {
        $out = ['result' => $this->error_result, 'msg'=>'Coupon not found'];
        $this->db->select('*');
        $this->db->from('sb_coupons');
        $this->db->where('coupon_id', $coupon_id);
        $res = $this->db->get()->row_array();
        if (isset($res['coupon_id']) && $res['coupon_id']==$coupon_id) {
            $code = explode('-',$res['coupon_code']);
            $data = [
                'coupon_id' => $res['coupon_id'],
                'coupon_ispublic' => $res['coupon_ispublic'],
                'coupon_discount_perc' => $res['coupon_discount_perc'],
                'coupon_discount_sum' => $res['coupon_discount_sum'],
                'coupon_minlimit' => $res['coupon_minlimit'],
                'coupon_maxlimit' => $res['coupon_maxlimit'],
                'coupon_description' => $res['coupon_description'],
                'coupon_code1' => $code[0],
                'coupon_code2' => $code[1],
                'brand' => $res['brand'],
            ];
            $brand_options = [];
            foreach ($brands as $row) {
                if ($row['brand']!=='ALL') {
                    $brand_options[] = [
                        'brand' => $row['brand'],
                        'label' => $row['label'],
                    ];
                }
            }
            // if (count($brand_options)>0) {
            $out['result'] = $this->success_result;
            $out['data'] = $data;
            $out['brands'] = $brand_options;
            $out['percent_lock'] = floatval($res['coupon_discount_sum'])==0 ? 0 : 1;
            $out['money_lock'] = floatval($res['coupon_discount_perc'])==0 ? 0 : 1;
            // }
        }
        return $out;
    }

    function del_coupon($coupon_id) {
        $out=array('msg'=>'Unknown Error','result'=>$this->error_result);
        $this->db->set('coupon_deleted',1);
        $this->db->where('coupon_id',$coupon_id);
        $this->db->update('sb_coupons');
        $options = [
            'coupon_deleted' => 0,
        ];
        $out['total'] = $this->get_coupons_number($options);
        $out['result'] = $this->success_result;
        return $out;
    }

    public function update_coupon($details) {
        $out=array('msg'=>'Unknown Error','result'=>$this->error_result);
        /* Check data */
        $err_msg='';
        if ($details['coupon_discount_perc']!='' && !is_numeric($details['coupon_discount_perc'])) {
            $err_msg.='Discount percent is not number.'.PHP_EOL;
        }
        if (is_numeric($details['coupon_discount_perc']) && $details['coupon_discount_perc']>=100) {
            $err_msg.='Discount percent must be less then 100%.'.PHP_EOL;
        }
        if ($details['coupon_discount_sum']!='' && !is_numeric($details['coupon_discount_sum'])) {
            $err_msg.='Discount sum is not number.'.PHP_EOL;
        }
        if ($details['coupon_discount_perc']!='' && is_numeric($details['coupon_discount_perc']) && $details['coupon_discount_sum']!='' && is_numeric($details['coupon_discount_sum'])) {
            if ($details['coupon_discount_perc']!=0 && $details['coupon_discount_sum']!=0) {
                $err_msg.='You can apply only one type of discount - sum or percent.'.PHP_EOL;
            }
        }

        if ($details['coupon_minlimit']!='' && !is_numeric($details['coupon_minlimit'])) {
            $err_msg.='Min limit of sum is not number.'.PHP_EOL;
        }
        if ($details['coupon_maxlimit']!='' && !is_numeric($details['coupon_maxlimit'])) {
            $err_msg.='Max limit of sum is not number.'.PHP_EOL;
        }
        if (empty($details['coupon_description'])) {
            $err_msg.='Coupon description is mandatory field.'.PHP_EOL;
        }
        if (empty($details['coupon_code1']) || empty($details['coupon_code2'])) {
            $err_msg.='Coupon code is mandatory fields and consists of 2 parts'.PHP_EOL;
        }

        if (floatval($details['coupon_minlimit'])>0 && floatval($details['coupon_maxlimit'])>0 && floatval($details['coupon_minlimit'])>floatval($details['coupon_maxlimit'])) {
            $err_msg.='Max limit of sum is less then min limit of sum.'.PHP_EOL;
        }

        if (strlen($details['coupon_code1'])<3 || strlen($details['coupon_code2'])<3) {
            $err_msg.="Coupon's code must contain 6 symbols".PHP_EOL;
        } else {
            /* Check unique */
            $new_code=strtoupper($details['coupon_code1']).'-'.strtoupper($details['coupon_code2']);
            $this->db->select('count(coupon_id) as cnt');
            $this->db->from('sb_coupons');
            $this->db->where('coupon_id !=',$details['coupon_id']);
            $this->db->where('brand', $details['brand']);
            $this->db->where('coupon_code',$new_code);
            $this->db->where('coupon_deleted',0);
            $res=$this->db->get()->row_array();
            $nrec=$res['cnt'];
            if ($nrec!=0) {
                /* Code not Unique */
                $err_msg.="Entered coupon's code is not unique";
            }
        }

        /* RESULT of checking */
        if ($err_msg!='') {
            $out['msg']=$err_msg;
        } else {
            $coupon_id = 0;
            $new_code=strtoupper($details['coupon_code1']).'-'.strtoupper($details['coupon_code2']);
            $this->db->set('coupon_ispublic',$details['coupon_ispublic']);
            $this->db->set('coupon_discount_perc',$details['coupon_discount_perc']);
            $this->db->set('coupon_discount_sum',$details['coupon_discount_sum']);
            $this->db->set('coupon_minlimit',$details['coupon_minlimit']);
            $this->db->set('coupon_maxlimit',$details['coupon_maxlimit']);
            $this->db->set('coupon_description',$details['coupon_description']);
            $this->db->set('coupon_code',$new_code);
            $this->db->set('brand', $details['brand']);
            if ($details['coupon_id']>0) {
                $this->db->where('coupon_id',$details['coupon_id']);
                $this->db->update('sb_coupons');
                $coupon_id = $details['coupon_id'];
            } else {
                $this->db->insert('sb_coupons');
                $coupon_id = $this->db->insert_id();
            }
            if ($coupon_id > 0) {
                $out['result'] = $this->success_result;
                $options = [
                    'coupon_deleted' => 0,
                ];
                $out['total'] = $this->get_coupons_number($options);
            }
        }
        return $out;

    }

    public function update_coupon_status($coupon_id) {
        $out=['msg'=>'Coupon Not Found', 'result'=>$this->error_result];
        $this->db->select('*');
        $this->db->from('sb_coupons');
        $this->db->where('coupon_id', $coupon_id);
        $res = $this->db->get()->row_array();
        if (isset($res['coupon_id'])) {
            $this->db->where('coupon_id', $coupon_id);
            $new_status = 0;
            if ($res['coupon_ispublic']==0) {
                $new_status = 1;
            }
            $this->db->set('coupon_ispublic', $new_status);
            $this->db->update('sb_coupons');
            $out['result']=$this->success_result;
            $out['active']=$new_status;
        }
        return $out;
    }

}