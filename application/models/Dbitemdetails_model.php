<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Dbitemdetails_model extends MY_Model
{
    function __construct()
    {
        parent::__construct();
    }

    public function change_parameter($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter', 'oldvalue' => ''];
        $entity = ifset($data,'entity','noname');
        $out['entity']=$entity;
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $key = ifset($data,'idx',0);
        if ($entity=='item') {
            $item = ifset($session_data,'item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld,$item)) {
                $out['oldvalue'] = $item[$fld];
                if ($fld=='item_number') {
                    $chkres = $this->_check_item_number($newval, $item['item_id']);
                    $out['msg'] = $chkres['msg'];
                    if ($chkres['result'] == $this->success_result) {
                        $item[$fld] = $newval;
                        $session_data['item'] = $item;
                        usersession($session_id, $session_data);
                        $out['msg'] = '';
                        $out['result'] = $this->success_result;
                    }
                } else {
                    if ($fld=='item_sale' || $fld=='item_new' || $fld=='sellblank' || $fld=='sellcolor' || $fld=='sellcolors') {
                        $newval = 1;
                        if ($item[$fld]==1) {
                            $newval = 0;
                        }
                    }
                    $item[$fld]=$newval;
                    $session_data['item']=$item;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                }
            }
        } elseif ($entity=='similar') {
            $out['msg']='Item Simular Not Found';
            $items = ifset($session_data,'similar', []);
            $idx = 0;
            foreach ($items as $item) {
                if ($item['item_similar_id']==$key) {
                    $items[$idx][$fld]=$newval;
                    $session_data['simular']=$items;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='vendor_item') {
            $out['msg']='Item Simular Not Found';
            $vendor = ifset($session_data,'vendor_item', []);
            $out['msg']='Parameter '.$fld.' Not Found';
            if (array_key_exists($fld, $vendor)) {
                $out['oldvalue'] = $vendor[$fld];
                $vendor[$fld] = $newval;
                $session_data['vendor']=$vendor;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='shipping') {
            $out['msg']='Item Shipping Parameter Not Found';
            $items = ifset($session_data,'prices', []);
            $idx = 0;
            foreach ($items as $item) {
                if ($item['promo_price_id']==$key) {
                    $items[$idx][$fld]=$newval;
                    $session_data['prices']=$items;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='colors') {
            $out['msg']='Item Option Not Found';
            $items = ifset($session_data,'colors', []);
            $idx = 0;
            foreach ($items as $item) {
                if ($item['item_color_id']==$key) {
                    $items[$idx][$fld]=$newval;
                    $session_data['colors']=$items;
                    usersession($session_id, $session_data);
                    $out['msg']='';
                    $out['result']=$this->success_result;
                    break;
                }
                $idx++;
            }
        } elseif ($entity=='priceshow') {
            $out['msg']='Item Price Not Found';
            $items = ifset($session_data,'prices', []);
            $idx = 0;
            $found=0;
            foreach ($items as $item) {
                if ($item['promo_price_id']==$key) {
                    $items[$idx]['show_first']=1;
                    $found = 1;
                } else {
                    $items[$idx]['show_first'] = 0;
                }
                $idx++;
            }
            if ($found==1) {
                $session_data['prices']=$items;
                usersession($session_id, $session_data);
                $out['msg']='';
                $out['result']=$this->success_result;
            }
        }
        $out['fld'] = $fld;
        $out['newval'] = $newval;
        $out['entity'] = $entity;
        return $out;
    }

    public function check_vendor($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $vendor_name = ifset($postdata,'vendor_name','');
        $this->load->model('vendors_model');
        if (empty($vendor_name)) {
            $vendordat = [
                'vendor_item_id' => '',
                'vendor_item_vendor' => '',
                'vendor_item_number' => '',
                'vendor_item_name' => '',
                'vendor_item_blankcost' => '',
                'vendor_item_cost' => '',
                'vendor_item_exprint' => '',
                'vendor_item_setup' => '',
                'vendor_item_notes' => '',
                'vendor_item_zipcode' => '',
                'printshop_item_id' => '',
                'vendor_name' => '',
                'vendor_zipcode' => '',
            ];
            $session_data['vendor_item']=$vendordat;
            $session_data['vendor_price']=$this->vendors_model->newitem_vendorprices();
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
            $out['newvendor']=1;
        } else {
            $this->db->select('vendor_id, vendor_name, vendor_zipcode');
            $this->db->from('vendors');
            $this->db->where('vendor_name', $vendor_name);
            $chkres = $this->db->get()->result_array();
            if (count($chkres)>1) {
                $out['msg']='Non Unique Vendor name';
            } elseif (count($chkres)==0) {
                // New Vendor
                $vendordat = [
                    'vendor_item_id'=>'',
                    'vendor_item_vendor'=>-1,
                    'vendor_item_number'=>'',
                    'vendor_item_name'=>'',
                    'vendor_item_blankcost'=>'',
                    'vendor_item_cost'=>'',
                    'vendor_item_exprint'=>'',
                    'vendor_item_setup'=>'',
                    'vendor_item_notes'=>'',
                    'vendor_item_zipcode'=>'',
                    'printshop_item_id'=>'',
                    'vendor_name'=>$vendor_name,
                    'vendor_zipcode'=>'',
                ];
                $session_data['vendor_item']=$vendordat;
                $session_data['vendor_price']=$this->vendors_model->newitem_vendorprices();
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
                $out['newvendor']=1;
            } elseif (count($chkres)==1) {
                $vendordat = $session_data['vendor_item'];
                if ($vendordat['vendor_item_vendor']!=$chkres[0]['vendor_id']) {
                    // New Vendor
                    $vendordat=[
                        'vendor_item_id'=>'',
                        'vendor_item_vendor'=>$chkres[0]['vendor_id'],
                        'vendor_item_number'=>'',
                        'vendor_item_name'=>'',
                        'vendor_item_blankcost'=>'',
                        'vendor_item_cost'=>'',
                        'vendor_item_exprint'=>'',
                        'vendor_item_setup'=>'',
                        'vendor_item_notes'=>'',
                        'vendor_item_zipcode'=>$chkres[0]['vendor_zipcode'],
                        'printshop_item_id'=>'',
                        'vendor_name'=>$chkres[0]['vendor_name'],
                        'vendor_zipcode'=>'',
                    ];
                    $session_data['vendor_item']=$vendordat;
                    $session_data['vendor_price']=$this->vendors_model->newitem_vendorprices();
                    $out['newvendor']=1;
                    $out['result']=$this->success_result;
                    usersession($session_id, $session_data);
                } else {
                    $out['newvendor']=0;
                    $out['result']=$this->success_result;
                    usersession($session_id, $session_data);
                }
            }
        }
        return $out;
    }

    public function check_vendor_item($postdata, $session_data, $session_id) {
        $out=['result'=>$this->error_result, 'msg'=>'Unknown error'];
        $vendor_item_number = ifset($postdata,'number','');
        $this->load->model('vendors_model');
        if (empty($vendor_item_number)) {
            $data=[
                'vendor_item_id'=>'',
                'vendor_item_vendor'=>'',
                'vendor_item_number'=>'',
                'vendor_item_name'=>'',
                'vendor_item_cost'=>'',
                'vendor_item_exprint'=>'',
                'vendor_item_setup'=>'',
                'vendor_item_notes'=>'',
                'vendor_item_zipcode'=> '',
                'vendor_item_blankcost' => '',
                'vendor_name' => '',
                'vendor_zipcode' => '',
            ];
            $vendor_prices=$this->vendors_model->newitem_vendorprices();
        } else {
            $res = $this->vendors_model->chk_vendor_item($vendor_item_number);
            if (ifset($res,'vendor_item_id',0)==0) {
                $data=[
                    'vendor_item_id'=>-1,
                    'vendor_item_vendor'=>'',
                    'vendor_item_number'=>$vendor_item_number,
                    'vendor_item_name'=>$vendor_item_number,
                    'vendor_item_cost'=>'',
                    'vendor_item_exprint'=>'',
                    'vendor_item_setup'=>'',
                    'vendor_item_notes'=>'',
                    'vendor_item_zipcode'=>'',
                    'vendor_item_blankcost' => '',
                    'vendor_name' => '',
                    'vendor_zipcode' => '',
                ];
                $vendor_prices=$this->vendors_model->newitem_vendorprices();
            } else {
                $data=[
                    'vendor_item_id'=>$res['vendor_item_id'],
                    'vendor_item_vendor'=>$res['vendor_item_vendor'],
                    'vendor_item_number'=>$res['vendor_item_number'],
                    'vendor_item_name'=>$res['vendor_item_name'],
                    'vendor_item_cost'=>$res['vendor_item_cost'],
                    'vendor_item_exprint'=>$res['vendor_item_exprint'],
                    'vendor_item_setup'=>$res['vendor_item_setup'],
                    'vendor_item_notes'=>$res['vendor_item_notes'],
                    'vendor_name'=>$res['vendor_name'],
                    'vendor_item_zipcode'=>$res['vendor_item_zipcode'],
                    'vendor_item_blankcost'=>$res['vendor_item_blankcost'],
                    'vendor_name' => $res['vendor_name'],
                    'vendor_zipcode' => $res['vendor_zipcode'],
                ];
                $vendor_prices=$this->vendors_model->get_vedorprice_item($res['vendor_item_id'],0);
            }
        }
        $out['result']=$this->success_result;
        $out['data']=$data;
        $session_data['vendor_item']=$data;
        $session_data['vendor_price']=$vendor_prices;
        $out['vendor_prices']=$vendor_prices;
        usersession($session_id, $session_data);
        return $out;
    }

    public function change_price($data, $session_data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Unknown parameter'];
        $entity = ifset($data,'entity','noname');
        $out['entity']=$entity;
        $fld = ifset($data,'fld','noname');
        $newval=ifset($data,'newval');
        $key = ifset($data,'idx',0);
        $this->load->model('prices_model');
        if ($entity=='vendor_price') {
            $out['msg']='Vendor Price Not Found';
            $items = ifset($session_data,'vendor_price', []);
            $idx = 0;
            $found=0;
            foreach ($items as $item) {
                if ($item['vendorprice_id']==$key) {
                    $found=1;
                    $items[$idx][$fld] = $newval;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $session_data['vendor_price']=$items;
                // Recalc Profit %
                $prices = $session_data['prices'];
                $session_data['prices'] = $this->prices_model->recalc_promo_profit($items, $prices);
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='vendor_specprice') {
            $out['msg']='Vendor Price Not Found';
            $items=ifset($session_data,'item',[]);
            if (array_key_exists($fld, $items)) {
                $items[$fld] = $newval;
                $vendor_item = $session_data['vendor_item'];
                // Calc Special promo
                $newitems = $this->prices_model->recalc_setup_profit($items, $vendor_item);
                $session_data['item'] = $newitems;
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='item_price') {
            $out['msg']='Price Not Found';
            $prices = ifset($session_data,'prices', []);
            $idx = 0;
            $found=0;
            foreach ($prices as $price) {
                if ($price['promo_price_id']==$key) {
                    $found=1;
                    $prices[$idx][$fld] = $newval;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                // Recalc Profit %
                $vendor_prices = $session_data['vendor_price'];
                $session_data['prices'] = $this->prices_model->recalc_promo_profit($vendor_prices, $prices);
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='item_specprice') {
            $out['msg']='Price Not Found';
            $items=ifset($session_data,'item',[]);
            if (array_key_exists($fld, $items)) {
                $items[$fld] = $newval;
                $vendor_item = $session_data['vendor_item'];
                // Calc Special promo
                $newitems = $this->prices_model->recalc_setup_profit($items, $vendor_item);
                $session_data['item'] = $newitems;
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    // Check uniq number
    private function  _check_item_number($item_number, $item_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item # not unique'];
        $this->db->select('count(item_id) as cnt');
        $this->db->from('sb_items');
        $this->db->where('item_number', $item_number);
        $this->db->where('item_id != ', $item_id);
        $res = $this->db->get()->row_array();
        if ($res['cnt']==1) {
            $out['result'] = $this->success_result;
        }
        return $out;
    }
}