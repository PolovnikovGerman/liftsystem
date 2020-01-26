<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Itemdetails_model extends My_Model
{

    private $STRESSBALL_TEMPLATE='Stressball';
    private $Inventory_Source='Stock';

    function __construct()
    {
        parent::__construct();
    }

    public function change_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $idx = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $item[$fld]=$newval;
                $session_data['item']=$item;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function change_specialcheck_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $idx = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $item[$fld]=$newval;
                $session_data['item']=$item;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
                $out['type']='item';
            }
        } elseif ($entity=='prices') {
            $out['msg']='Price Not Found';
            $prices = $session_data['prices'];
            $found=0;
            $priceidx = 0;
            foreach ($prices as $row) {
                if ($row['item_specprice_id']==$idx) {
                    $found=1;
                    break;
                }
                $priceidx++;
            }
            if ($found==1) {
                $out['type']='price';
                $out['result']=$this->success_result;
                if ($fld=='price') {
                    $prices[$priceidx]['price']=floatval($newval);
                } else {
                    $prices[$priceidx]['price_qty']=intval($newval);
                }
                $out['amount']=$out['profit']=$out['profit_percent']=$out['profit_class']='';
                // Amount
                $amount=intval($prices[$priceidx]['price_qty'])*floatval($prices[$priceidx]['price']);
                if ($amount!=0) {
                    $vendor_prices = $session_data['vendor_prices'];
                    $prices[$priceidx]['amount']=$amount;
                    $out['amount']=MoneyOutput($amount);
                    $this->load->model('prices_model');
                    $profitdat = $this->prices_model->recalc_special_profit($vendor_prices, $prices[$priceidx]['price_qty'], $prices[$priceidx]['price']);
                    if (floatval($profitdat)!=0) {
                        $profit=floatval($profitdat);
                        $profit_perc=$profit/($amount)*100;
                        $out['profit_class']=profit_bgclass($profit_perc);
                        $out['profit']=round($profit,2);
                        $out['profit_percent']=round($profit_perc,0).'%';
                        $prices[$priceidx]['profit']=round($profit,2);
                        $prices[$priceidx]['profit_percent']=round($profit_perc,0);
                        $prices[$priceidx]['profit_class']=profit_bgclass($profit_perc);
                    }
                }
                $session_data['prices']=$prices;
                usersession($session_id, $session_data);
            }
        }
        return $out;
    }

    public function save_specialcheckout($session_data, $specsession_data, $session_id, $specsession_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $item = $session_data['item'];
        $specitem = $specsession_data['item'];
        // Update Main Item
        $item['special_checkout']=$specitem['special_checkout'];
        $item['special_shipping']=$specitem['special_shipping'];
        $item['special_setup']=$specitem['special_setup'];
        $session_data['item']=$item;
        $session_data['special_prices']=$specsession_data['prices'];
        usersession($session_id, $session_data);
        usersession($specsession_id, NULL);
        $out['result']=$this->success_result;
        return $out;
    }

    // Save item data
    public function save_itemdata($session_data, $session_id, $user_id, $user_role) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $item = $session_data['item'];
        // Check data
        $itemchk = $this->_check_itemdata($item);
        $out['msg']=$itemchk['msg'];
        if ($itemchk['result']==$this->success_result) {
            $old_seq = 0;
            if ($item['item_id']>0) {
                $this->db->select('item_sequence')->from('sb_items')->where('item_id', $item['item_id']);
                $itemres = $this->db->get()->row_array();
                $old_seq = $itemres['item_sequence'];
            }
            // Save item value
            $res = $this->_save_iteminfo($item, $user_id);
            $out['msg']=$res['msg'];
            if ($res['result']==$this->success_result) {
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    private function _check_itemdata($detal) {
        $out=['result'=>$this->success_result, 'msg' =>''];
        $out_mgs='';
        if (empty(ifset($detal,'item_number',''))) {
            $out_mgs.='Item # required.'.PHP_EOL;
        } else {
            $this->db->select('count(item_id) as cnt')->from('sb_items')->where('item_number',$detal['item_number'])->where('item_id != ',$detal['item_id']);
            $det = $this->db->get()->row_array();
            if ($det['cnt']>0) {
                $out_mgs.='Item Number is not unique'.PHP_EOL;
            }
        }
        /* Add Unique check */
        if (empty(ifset($detal,'item_name',''))) {
            $out_mgs.='Item name required'.PHP_EOL;
        }

        if (intval(ifset($detal,'item_lead_a',0))==0) {
            $out_mgs.='Lead A required'.PHP_EOL;
        }

        if (empty(ifset($detal,'item_material',''))) {
            if ($detal['item_template']==$this->STRESSBALL_TEMPLATE) {
                $out_mgs.='Item material required'.PHP_EOL;
            }
        }
        if (empty(ifset($detal,'item_weigth',0))) {
            $out_mgs.='Item weight required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_size',''))) {
            $out_mgs.='Item size required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_keywords',''))) {
            $out_mgs.='Internal Keywords required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_url',''))) {
            $out_mgs.='Page URL is required field'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_meta_title',''))) {
            $out_mgs.='Meta title required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_metadescription',''))) {
            $out_mgs.='Meta description required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_metakeywords',''))) {
            $out_mgs.='Meta keywords required'.PHP_EOL;
        }
        if (empty(ifset($detal,'item_description1',''))) {
            $out_mgs.='Attributes (row 1) required'.PHP_EOL;
        }
        if (isset($detal['item_description2']) && empty($detal['item_description2']) && $detal['item_template']=='Stressball') {
            $out_mgs.='Attributes (row 2) required'.PHP_EOL;
        }
        if (isset($detal['cartoon_qty']) && empty($detal['cartoon_qty'])) {
            $out_mgs.='Shipping Information - Carton Qty - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_qty'])) {
            $out_mgs.='Shipping Information - Carton Qty - must be a number';
        }
        if (isset($detal['cartoon_width']) && empty($detal['cartoon_width'])) {
            $out_mgs.='Shipping Information - Carton Width - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_width'])) {
            $out_mgs.='Shipping Information - Carton Width - must be a number'.PHP_EOL;
        }
        if (isset($detal['cartoon_heigh']) && empty($detal['cartoon_heigh'])) {
            $out_mgs.='Shipping Information - Carton Height - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_heigh'])) {
            $out_mgs.='Shipping Information - Carton Height - must be a number'.PHP_EOL;
        }
        if (isset($detal['cartoon_depth']) && empty($detal['cartoon_depth'])) {
            $out_mgs.='Shipping Information - Carton Depth - required'.PHP_EOL;
        } elseif (!is_numeric($detal['cartoon_depth'])) {
            $out_mgs.='Shipping Information - Carton Depth - must be a number'.PHP_EOL;
        }
        if (!is_numeric($detal['charge_pereach'])) {
            $out_mgs.='Shipping Information - Special Shipping charge per each  - must be a number'.PHP_EOL;
        }
        if (!is_numeric($detal['charge_perorder'])) {
            $out_mgs.='Shipping Information - Special Shipping charge per order  - must be a number'.PHP_EOL;
        }
        if ($detal['item_source']==$this->Inventory_Source && empty($detal['printshop_inventory_id'])) {
            $out_mgs.='Choose Inventory Item'.PHP_EOL;
        }
        if (!empty($out_mgs)) {
            $out['result']=$this->error_result;
            $out['msg']=$out_mgs;
        }
        return $out;
    }

    private function _save_iteminfo($item, $user_id) {
        $this->db->set('item_number', $item['item_number']);
        $this->db->set('item_name', $item['item_name']);
        $this->db->set('item_active', $item['item_active']);
        $this->db->set('item_new', $item['item_new']);
        $this->db->set('item_template', $item['item_template']);
        $this->db->set('item_lead_a', $item['item_lead_a']);
        $this->db->set('item_lead_b', empty($item['item_lead_b']) ? null : intval($item['item_lead_b']));
        $this->db->set('item_lead_c', empty($item['item_lead_c']) ? null : intval($item['item_lead_c']));
        $this->db->set('item_material', $item['item_material']);
        $this->db->set('item_weigth', floatval($item['item_weigth']));
        $this->db->set('item_size', $item['item_size']);
        $this->db->set('item_keywords', $item['item_keywords']);
        $this->db->set('item_url', $item['item_url']);
        $this->db->set('item_meta_title', $item['item_meta_title']);
        $this->db->set('item_metadescription', $item['item_metadescription']);
        $this->db->set('item_metakeywords', $item['item_metakeywords']);
        $this->db->set('item_description1', $item['item_description1']);
        $this->db->set('item_description2',$item['item_description2']);
        $this->db->set('item_vector_img', $item['item_vector_img']);
        $this->db->set('vendor_item_id', $item['vendor_item_id']);
        $this->db->set('common_terms', $item['common_terms']);
        $this->db->set('bottom_text', $item['bottom_text']);
        $this->db->set('options', $item['options']);
        $this->db->set('cartoon_qty', $item['cartoon_qty']);
        $this->db->set('cartoon_width', $item['cartoon_width']);
        $this->db->set('cartoon_heigh', $item['cartoon_heigh']);
        $this->db->set('cartoon_depth', $item['cartoon_depth']);
        $this->db->set('boxqty', $item['boxqty']);
        $this->db->set('charge_pereach', $item['charge_pereach']);
        $this->db->set('charge_perorder', $item['charge_perorder']);
        $this->db->set('faces', $item['faces']);
        $this->db->set('special', $item['special']);
        $this->db->set('category_id', $item['category_id']);
        $this->db->set('special_checkout', $item['special_checkout']);
        $this->db->set('special_shipping', $item['special_shipping']);
        $this->db->set('special_setup', $item['special_setup']);
        $this->db->set('item_source', $item['item_source']);
        $this->db->set('printshop_inventory_id', $item['printshop_inventory_id']);
        $this->db->set('update_template',$item['update_template']);
        $this->db->set('imprint_update', $item['imprint_update']);
        $this->db->set('item_sequence', $item['item_sequence']);
        $this->db->set('item_sale', $item['item_sale']);
        $this->db->set('printlocat_example_img', $item['printlocat_example_img']);
        $this->db->set('itemcolor_example_img', $item['itemcolor_example_img']);
        // $this->db->set('shipping_info', $item['shipping_info']);
        if ($item['item_id']==0) {
            $this->db->set('create_user', $user_id);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('update_user', $user_id);
            $this->db->insert('sb_items');
            $newrec = $this->db->insert_id();
            if ($newrec==0) {
                $out=['result'=> $this->error_result, 'msg'=>'Error during add item data'];
            } else {
                $out=['result'=>$this->success_result, 'msg'=> '', 'item_id'=>$newrec];
            }
        } else {
            $this->db->set('update_user', $user_id);
            $this->db->where('item_id', $item['item_id']);
            $this->db->update('sb_items');
            $out=['result'=>$this->success_result, 'msg'=> '', 'item_id'=>$item['item_id']];
        }
        return $out;
    }

}