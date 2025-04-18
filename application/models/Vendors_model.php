<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Vendors_model extends My_Model
{

    protected $vend_maxprice=7;

    function __construct()
    {
        parent::__construct();
    }

    public function get_vendors($options=array()) {
        $this->db->select('*',FALSE);
        $this->db->from('vendors');
        foreach ($options as $key=>$value) {
            $this->db->where($key,$value);
        }
        $this->db->order_by('vendor_name');
        $result=$this->db->get()->result_array();
        return $result;
    }

    public function get_itemseq_vendors() {
        $vend=[
            'Stressballs.com','Ariel','Alpi','Mailine','Pinnacle','Jetline','Hit',
        ];
        $out=[];
        foreach ($vend as $vrow) {
            $this->db->select('*');
            $this->db->from('vendors');
            $this->db->where('vendor_name',$vrow);
            $vres=$this->db->get()->row_array();
            $out[]=$vres;
        }
        $out[]=[
            'vendor_id' => -1,
            'vendor_name' => '----------------------',
        ];
        $this->db->select('*');
        $this->db->from('vendors');
        $this->db->where_not_in('vendor_name', $vend);
        $this->db->order_by('vendor_name');
        $otherres = $this->db->get()->result_array();
        foreach ($otherres as $row) {
            $out[]=$row;
        }
        return $out;
    }

    public function get_inventory_list() {
        $this->db->select('*');
        $this->db->from('ts_printshop_items');
        $this->db->order_by('item_num');
        $res=$this->db->get()->result_array();
        // Get data
        $out=array();
        foreach ($res as $row) {
            $out[]=array(
                'printshop_item_id'=>$row['printshop_item_id'],
                'item_name'=>$row['item_num'].' '.trim(str_replace('Stress Balls', '', $row['item_name'])),
            );
        }
        return $out;
    }

    public function get_vendor_item($vendor_id) {
        $this->db->select('vi.*, v.vendor_name as vendor_name, v.vendor_zipcode');
        $this->db->from('sb_vendor_items vi');
        $this->db->join("vendors v",'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('vendor_item_id',$vendor_id);
        $result=$this->db->get()->row_array();
        if (!isset($result['vendor_item_id'])) {
            $result=array(
                'vendor_item_id'=>'',
                'vendor_item_vendor'=>'',
                'vendor_item_number'=>'',
                'vendor_item_name'=>'',
                'vendor_item_blankcost'=>0.000,
                'vendor_item_cost'=>0.000,
                'vendor_item_exprint'=>0.000,
                'vendor_item_setup'=>0.000,
                'vendor_item_notes'=>'',
                'vendor_item_zipcode'=>'',
                'vendor_name'=>'',
                'vendor_zipcode'=>'',
            );
        }
        return $result;
    }

    public function get_inventory_item($printshop_item_id) {
        // Get Vendor Data
        $this->db->select('*');
        $this->db->from('vendors');
        $this->db->where('vendor_id', $this->config->item('inventory_vendor'));
        $vendordata=$this->db->get()->row_array();
        // Get Printshop Item Data
        $this->db->select('*');
        $this->db->from('ts_printshop_items');
        $this->db->where('printshop_item_id', $printshop_item_id);
        $inventdata=$this->db->get()->row_array();

        // Add Vendor Item
        $this->db->select('vi.*, v.vendor_name as vendor_name',FALSE);
        $this->db->from('sb_vendor_items vi');
        $this->db->join("$this->vendor_db v",'v.vendor_id=vi.vendor_item_vendor');
        $this->db->where('printshop_item_id',$printshop_item_id);
        $result=$this->db->get()->row_array();
        if (!isset($result['vendor_item_id'])) {
            // Insert Data
            $this->db->set('vendor_item_vendor', $this->config->item('inventory_vendor'));
            $this->db->set('vendor_item_blankcost',0);
            $this->db->set('vendor_item_cost','0');
            $this->db->set('vendor_item_exprint',0);
            $this->db->set('vendor_item_setup',0);
            $this->db->set('printshop_item_id', $printshop_item_id);
            $this->db->insert('sb_vendor_items');
            $this->db->select('vi.*, v.vendor_name as vendor_name',FALSE);
            $this->db->from('sb_vendor_items vi');
            $this->db->join("$this->vendor_db v",'v.vendor_id=vi.vendor_item_vendor');
            $this->db->where('printshop_item_id',$printshop_item_id);
            $result=$this->db->get()->row_array();
        }
        $out=array(
            'vendor_item_id'=>$result['vendor_item_id'],
            'vendor_item_vendor'=>$result['vendor_item_vendor'],
            'vendor_item_number'=>$inventdata['item_num'],
            'vendor_item_name'=>$inventdata['item_name'],
            'vendor_item_blankcost'=>$result['vendor_item_blankcost'],
            'vendor_item_cost' =>$result['vendor_item_cost'],
            'vendor_item_exprint' =>$result['vendor_item_exprint'],
            'vendor_item_setup' =>$result['vendor_item_setup'],
            'vendor_item_notes' =>$inventdata['item_note'],
            'vendor_item_zipcode' =>$vendordata['vendor_zipcode'],
            'vendor_name' =>$vendordata['vendor_name'],
        );
        return $out;
    }

    public function get_vedorprice_item($vendor_id,$viewonly=0) {
        $this->db->select('*');
        $this->db->from('sb_vendor_prices');
        $this->db->where('vendor_item_id',$vendor_id);
        $this->db->order_by('vendorprice_qty');
        $result=$this->db->get()->result_array();
        $vendprice=array();
        $i=0;
        foreach ($result as $row) {
            if ($viewonly==1 && $row['vendorprice_qty']>=10000) {
                $row['vendorprice_qty']=($row['vendorprice_qty']%1000==0 ? intval($row['vendorprice_qty']/1000).'K' : $row['vendorprice_qty']);
            }
            $vendprice[]=$row;
            $i++;
        }
        for ($j=$i; $j< $this->vend_maxprice ; $j++) {
            $vendprice[]=array(
                'vendorprice_id'=>(-1)*$j,
                'vendorprice_qty'=>'',
                'vendorprice_val'=>'',
                'vendorprice_color'=>'',
            );
        }
        return $vendprice;
    }

    public function search_vendor_items($vend_it_num, $vendor_id) {
        $this->db->select('vendor_item_number as label,vendor_item_id as id');
        $this->db->from('sb_vendor_items');
        $this->db->like('upper(vendor_item_number)',  strtoupper($vend_it_num));
        if (intval($vendor_id)>0) {
            $this->db->where('vendor_item_vendor', $vendor_id);
        }
        $this->db->order_by('vendor_item_number');
        $result=$this->db->get()->result_array();
        return $result;
    }

    public function chk_vendor_item($vendor_it_num) {
        $this->db->select('vi.*,v.vendor_name');
        $this->db->from('sb_vendor_items vi');
        $this->db->join("vendors v",'v.vendor_id=vi.vendor_item_vendor','left');
        $this->db->where('upper(vendor_item_number)',  strtoupper($vendor_it_num));
        $result = $this->db->get()->row_array();
        return $result;
    }

    public function newitem_vendorprices() {
        $vendprice=array();
        for ($j=0; $j< $this->vend_maxprice ; $j++) {
            $vendprice[]=array(
                'vendorprice_id'=>(-1)*$j,
                'vendorprice_qty'=>'',
                'vendorprice_val'=>'',
                'vendorprice_color'=>'',
            );
        }
        return $vendprice;

    }

    public function get_count_vendors($options = []) {
        $this->db->select('count(vendor_id) as cnt');
        $this->db->from('vendors');
        if (ifset($options, 'status',0) > 0) {
            $this->db->where('vendor_status', ($options['status']==2 ? 0 : $options['status']));
        }
        if (ifset($options,'search','')!=='') {
            $this->db->like('concat(coalesce(vendor_name,\'\'), coalesce(alt_name,\'\'), coalesce(vendor_slug,\'\'))', $options['search']);
        }
        if (ifset($options,'vtype','')!=='') {
            $this->db->where('vendor_type', $options['vtype']);
        }
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_vendors_list($options) {
        $this->db->select('v.*, c.country_name');
        $this->db->from('vendors v');
        $this->db->join('ts_countries c','c.country_id=v.country_id','left');
        if (ifset($options, 'status',0) > 0) {
            $this->db->where('vendor_status', ($options['status']==2 ? 0 : $options['status']));
        }
        if (ifset($options,'search','')!=='') {
            $this->db->like('concat(coalesce(vendor_name,\'\'), coalesce(alt_name,\'\'), coalesce(vendor_slug,\'\'))', $options['search']);
        }
        if (ifset($options,'vtype','')!=='') {
            $this->db->where('vendor_type', $options['vtype']);
        }
        if (isset($options['exclude'])) {
            if (is_array($options['exclude'])) {
                $this->db->where_not_in('vendor_id', $options['exclude']);
            } else {
                $this->db->where('vendor_id != ', $options['exclude']);
            }
        }
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['order_by'])) {
            if (isset($options['direct'])) {
                $this->db->order_by($options['order_by'], $options['direct']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }
        $results=$this->db->get()->result_array();
        $res = [];
        $start = ifset($options, 'offset', 0);
        foreach ($results as $result) {
            $start++;
            $result['numpp'] = $start;
            $result['country_name'] = ($result['country_name']=='United States' ? 'USA' : $result['country_name']);
            $this->db->select('count(vendor_item_id) as cnt');
            $this->db->from('sb_vendor_items');
            $this->db->where('vendor_item_vendor', $result['vendor_id']);
            $qty = $this->db->get()->row_array();
            // $result['item_qty'] = $qty['cnt'];
            $result['item_qty'] = 0;
            // $this->db->select('contact_name, contact_phone, contact_cellphone, contact_email');
            // $this->db->from('vendor_contacts');
            // $this->db->where('vendor_id', $result['vendor_id']);
            // $contact = $this->db->get()->row_array();
            // $result['contact_name'] = ifset($contact, 'contact_name','');
            // $result['contact_phone'] = ifset($contact, 'contact_phone','');
            // $result['contact_email'] = ifset($contact, 'contact_email','');
            $res[]= $result;
        }
        return $res;
    }

    public function add_vendor() {
        $this->db->select('count(vendor_id) as cnt, max(vendor_id) as maxval, max(vendor_code) as vcode');
        $this->db->from('vendors');
        $res = $this->db->get()->row_array();
        if ($res['cnt']==0) {
            $newid = 50000;
        } else {
            $newid = $res['vcode'] + 1;
        }
        $vendor=array(
            'vendor_id' => 0,
            'calendar_id' => $this->config->item('bank_calendar'),
            'vendor_name' => '',
            'vendor_zipcode' => '',
            'vendor_asinumber' => '',
            'payinclude' => 0,
            'payinclorder' => 0,
            'vendor_phone' => '',
            'vendor_email' => '',
            'vendor_website' => '',
            'vendor_type' => 'Supplier',
            'country_id' => $this->config->item('vendor_default_country'),
            'alt_name' => '',
            'our_account_number' => '',
            'address' => '',
            'address_line1' => '',
            'address_line2' => '',
            'address_city' => '',
            'address_zip' => '',
            'address_state' => '',
            'address_country' => '',
            'shipping_pickup' => '',
            'shipaddr_line1' => '',
            'shipaddr_line2' => '',
            'shipaddr_city' => '',
            'shipaddr_state' => '',
            'shipaddr_country' => '',
            'payment_accept_visa' => 0,
            'payment_accept_amex' => 0,
            'payment_accept_ach' => 0,
            'payment_accept_check' => 0,
            'payment_accept_wire' => 0,
            'payment_accept_paypal' => 0,
            'checkout_details' => '',
            'po_note' => '',
            'internal_po_note' => '',
            'vendor_status' => 1,
            'vendor_slug' => 'V-'.$newid,
            'general_note' => '',
            'po_contact' => '',
            'po_phone' => '',
            'po_email' => '',
            'po_ccemail' => '',
            'po_bcemail' => '',
            'payment_contact' => '',
            'payment_phone' => '',
            'payment_email' => '',
            'payment_prepay' => 1,
            'payment_terms' => 0,
            'payment_note' => '',
            'pricing_contact' => '',
            'pricing_phone' => '',
            'pricing_email' => '',
            'customer_contact' => '',
            'customer_phone' => '',
            'customer_email' => '',
        );
        return [
            'vendor' => $vendor,
            'vendor_pricedocs' => [],
            'vendor_otherdocs' => [],
        ];
    }

    public function get_vendor($vendor_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Vendor not found'];
        $this->db->select('v.*, c.country_name');
        $this->db->from('vendors v');
        $this->db->join('ts_countries c','c.country_id=v.country_id','left');
        $this->db->where('vendor_id', $vendor_id);
        $vendor = $this->db->get()->row_array();
        if (ifset($vendor, 'vendor_id',0)>0) {
            $out['result'] = $this->success_result;
            $out['data'] = $vendor;
//            $this->db->select('*');
//            $this->db->from('vendor_contacts');
//            $this->db->where('vendor_id', $vendor_id);
//            $out['vendor_contacts'] = $this->db->get()->result_array();
            $this->db->select('*');
            $this->db->from('vendor_docs');
            $this->db->where('vendor_id', $vendor_id);
            $this->db->where('doc_type','PRICELIST');
            $this->db->order_by('doc_year desc, vendor_doc_id desc');
            $out['vendor_pricedocs'] = $this->db->get()->result_array();
            $this->db->select('*');
            $this->db->from('vendor_docs');
            $this->db->where('vendor_id', $vendor_id);
            $this->db->where('doc_type','OTHERS');
            $out['vendor_otherdocs'] = $this->db->get()->result_array();

        }
        return $out;
    }

    function save_vendor($vendor_id, $vendor_name, $vendor_zipcode, $calendar_id) {
        $out=array('result'=>$this->error_result,'msg'=>'Error during save Vendor');
        if (trim($vendor_name)=='') {
            $out['msg']='Vendor name required parameter';
        } else {
            $this->db->select('count(*) as cnt');
            $this->db->from('vendors');
            $this->db->where('vendor_name',$vendor_name);
            $this->db->where('vendor_id !=',$vendor_id);
            $res=$this->db->get()->row_array();
            if ($res['cnt']>0) {
                $out['msg']='Vendor name non unique';
            } else {
                $this->db->set('vendor_name',$vendor_name);
                $this->db->set('vendor_zipcode',$vendor_zipcode);
                $this->db->set('calendar_id',($calendar_id=='' ? NULL : $calendar_id));
                if ($vendor_id==0) {
                    $this->db->insert('vendors');
                    $vendor_id=$this->db->insert_id();
                } else {
                    $this->db->where('vendor_id',$vendor_id);
                    $this->db->update('vendors');
                }
                if ($vendor_id==0) {
                    $out['msg']='Vendors data wasn\'t saved. Please, try later';
                } else {
                    $out['result'] = $this->success_result;
                }
            }
        }
        return $out;
    }

    public function delete_vendor($vendor_id) {
        $out=['result'=>$this->error_result, 'msg'=> 'Vendor Not Found'];
        $this->db->select('count(vendor_id) cnt');
        $this->db->from('vendors');
        $this->db->where('vendor_id',$vendor_id);
        $chkres = $this->db->get()->row_array();
        if ($chkres['cnt']==1) {
            $this->db->where('vendor_id',$vendor_id);
            $this->db->delete('vendors');
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function vendor_includerep($vendor_id, $payinclude) {
        $this->db->set('payinclude', $payinclude);
        $this->db->where('vendor_id', $vendor_id);
        $this->db->update('vendors');
        return TRUE;
    }

    public function vendors_included() {
        $this->db->select('vendor_id, vendor_name');
        $this->db->from('vendors');
        $this->db->where('payinclude', 1);
        $this->db->order_by('payinclorder, vendor_name');
        $list=$this->db->get()->result_array();
        return $list;
    }

    public function search_vendors($vend_name) {
        $this->db->select('vendor_name as label, vendor_id as id');
        $this->db->from("vendors");
        $this->db->like('upper(vendor_name)',  strtoupper($vend_name));
        $this->db->order_by('vendor_name');
        $result=$this->db->get()->result_array();
        return $result;
    }

    public function update_vendor_details($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Parameter Not Found'];
        $fld = ifset($data,'fld', '');
        $newval = ifset($data,'newval', '');
        $entity = ifset($data,'entity','');
        $key = ifset($data,'idx', 0);
        if ($entity=='vendor') {
            $vendor = ifset($session_data, 'vendor', []);
            if (array_key_exists($fld, $vendor)) {
                if ($fld=='vendor_status') {
                    $newval = ($vendor['vendor_status']==0 ? 1 : 0);
                }
                $vendor[$fld] = $newval;
                $session_data['vendor'] = $vendor;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['fld'] = $fld;
                $out['vendor'] = $vendor;
                $out['newval'] = $newval;
            }
        } elseif ($entity=='vendor_contacts') {
            $contacts = $session_data['vendor_contacts'];
            $found = 0;
            $idx = 0;
            foreach ($contacts as $contact) {
                if ($contact['vendor_contact_id']==$key) {
                    $found = 1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $contacts[$idx][$fld]=$newval;
                $session_data['vendor_contacts'] = $contacts;
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        } elseif ($entity=='vendor_docs') {
            $docs = $session_data['vendor_docs'];
            $found = 0;
            $idx = 0;
            foreach ($docs as $doc) {
                if ($doc['vendor_doc_id']==$key) {
                    $found = 1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $docs[$idx][$fld]=$newval;
                $session_data['vendor_docs'] = $docs;
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function update_vendor_check($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Parameter Not Found'];
        $fld = ifset($data,'fld', '');
        $entity = ifset($data,'entity','');
        $key = ifset($data,'idx',0);
        if ($entity=='vendor') {
            $vendor = ifset($session_data, 'vendor', []);
            if (array_key_exists($fld, $vendor)) {
                $oldval = $vendor[$fld];
                $newval = ($oldval==0 ? 1 : 0);
                $vendor[$fld] = $newval;
                $session_data['vendor'] = $vendor;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['fld'] = $fld;
                $out['newval'] = $newval;
            }
        } elseif ($entity=='vendor_contacts') {
            $contacts = ifset($session_data, 'vendor_contacts', []);
            $idx = 0;
            $found = 0;
            foreach ($contacts as $contact) {
                if ($contact['vendor_contact_id']==$key) {
                    $found=1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $oldval = $contacts[$idx][$fld];
                $newval = ($oldval==0 ? 1 : 0);
                $contacts[$idx][$fld] = $newval;
                $session_data['vendor_contacts'] = $contacts;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['fld'] = $fld;
                $out['newval'] = $newval;
            }
        }
        return $out;
    }

    public function update_vendor_radio($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Parameter Not Found'];
        $fld = ifset($data,'fld', '');
        $entity = ifset($data,'entity','');
        if ($entity=='vendor') {
            $vendor = ifset($session_data, 'vendor', []);
            if (array_key_exists($fld, $vendor)) {
                $oldval = $vendor[$fld];
                $newval = ($oldval==0 ? 1 : 0);
                $vendor[$fld] = $newval;
                if ($fld=='payment_prepay') {
                    $vendor['payment_terms'] = ($newval==1 ? 0 : 1);
                } else {
                    $vendor['payment_prepay'] = ($newval==1 ? 0 : 1);
                }
                $session_data['vendor'] = $vendor;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['fld'] = $fld;
                $out['vendor'] = $vendor;
            }
        }
        return $out;
    }

    public function update_vendor_address($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Parameter Not Found'];
        $adrtype = ifset($data,'address_type', '');
        $vendor = $session_data['vendor'];
        $update = 0;
        if ($adrtype=='address') {
            $update = 1;
            $vendor['address_line1'] = ifset($data,'line_1','');
            $vendor['address_city'] = ifset($data, 'city', '');
            $vendor['address_state'] = ifset($data, 'state','');
            $vendor['address_zip'] = ifset($data, 'zip', '');
            $vendor['address_country'] = ifset($data, 'country', '');
        } elseif ($adrtype=='shipping') {
            $update = 1;
            $vendor['shipaddr_line1'] = ifset($data,'line_1','');
            $vendor['shipaddr_city'] = ifset($data, 'city', '');
            $vendor['shipaddr_state'] = ifset($data, 'state','');
            $vendor['vendor_zipcode'] = ifset($data, 'zip', '');
            $vendor['shipaddr_country'] = ifset($data, 'country', '');
        }
        if ($update==1) {
            $out['result'] = $this->success_result;
            $session_data['vendor']=$vendor;
            usersession($session_id, $session_data);
        }
        return $out;
    }

    public function vendor_contact_manage($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Parameter Not Found'];
        $manage = ifset($data,'manage', '');
        $key = ifset($data, 'idx', 0);
        $contacts = $session_data['vendor_contacts'];
        if ($manage=='add') {
            $minidx = 0;
            foreach ($contacts as $contact) {
                if ($contact['vendor_contact_id']<$minidx) {
                    $minidx = $contact['vendor_contact_id'];
                }
            }
            $minidx = $minidx -1;
            $newcontact = [
                'vendor_contact_id' => $minidx,
                'contact_name' => '',
                'contact_phone' => '',
                'contact_cellphone' => '',
                'contact_email' => '',
                'contact_po' => 0,
                'contact_art' => 0,
                'contact_pay' => 0,
                'contact_note' => '',
            ];
            $contacts[] = $newcontact;
            $session_data['vendor_contacts']=$contacts;
            usersession($session_id, $session_data);
            $out['result'] = $this->success_result;
            $out['vendor_contacts'] = $contacts;
        } elseif ($manage=='del') {
            $deleted = $session_data['deleted'];
            $newcontact = [];
            $found = 0;
            foreach ($contacts as $contact) {
                if ($contact['vendor_contact_id']==$key) {
                    $found=1;
                    if ($key > 0) {
                        $deleted[] = [
                            'entity' => 'contacts',
                            'id' => $key,
                        ];
                    }
                } else {
                    $newcontact[] = $contact;
                }
            }
            if ($found==1) {
                $session_data['vendor_contacts']=$newcontact;
                $session_data['deleted'] = $deleted;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['vendor_contacts'] = $newcontact;
            }
        }
        return $out;
    }

    public function vendor_docs_manage($data, $session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Parameter Not Found'];
        $manage = ifset($data,'manage', '');
        $doc_type = ifset($data,'doc_type','');
        if (!empty($doc_type)) {
            if ($doc_type=='PRICELIST') {
                $docs = $session_data['vendor_pricedocs'];
            } else {
                $docs = $session_data['vendor_otherdocs'];
            }
            $key = ifset($data, 'idx', 0);
            $doc_url = ifset($data,'doc_url','');
            $doc_name = ifset($data,'doc_name','');
            $doc_year = ifset($data, 'doc_year', '');
            if ($manage=='add') {
                // New doc
                $minidx = 0;
                foreach ($docs as $doc) {
                    if ($doc['vendor_doc_id']<$minidx) {
                        $minidx = $doc['vendor_doc_id'];
                    }
                }
                $minidx=$minidx-1;
                $docs[] = [
                    'vendor_doc_id' => $minidx,
                    'doc_url' => $doc_url,
                    'doc_name' => $doc_name,
                    'doc_description' => isset($data['doc_description']) ? $data['doc_description'] : '',
                    'doc_year' => $doc_year,
                ];
                $out['result'] = $this->success_result;
                if ($doc_type=='PRICELIST') {
                    $keys = array_column($docs, 'doc_year');
                    array_multisort($keys, SORT_DESC, $docs);
                    $session_data['vendor_pricedocs'] = $docs;
                    $out['vendor_docs'] = $docs;
                } else {
                    $session_data['vendor_otherdocs'] = $docs;
                    $out['vendor_docs'] = $docs;
                }
                usersession($session_id, $session_data);
                $out['doc_type'] = $doc_type;
                $out['result'] = $this->success_result;
            } elseif ($manage=='del') {
                $found = 0;
                $deleted = $session_data['deleted'];
                $newdoc = [];
                foreach ($docs as $doc) {
                    if ($doc['vendor_doc_id']==$key) {
                        $found=1;
                        if ($key > 0) {
                            $deleted[] = [
                                'entity' => 'docs',
                                'id' => $key,
                            ];
                        }
                        // break;
                    } else {
                        $newdoc[] = $doc;
                    }
                }
                if ($found==1) {
                    $out['result'] = $this->success_result;
                    if ($doc_type=='PRICELIST') {
                        $session_data['vendor_pricedocs'] = $newdoc;
                    } else {
                        $session_data['vendor_otherdocs'] = $newdoc;
                    }
                    $out['vendor_docs'] = $newdoc;
                    $out['doc_type'] = $doc_type;
                    $session_data['deleted'] = $deleted;
                    usersession($session_id, $session_data);
                }
            }
        }
        return $out;
    }

    public function save_vendordata($session_data, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Path for save not correct','errfld' => []];
        // Check doc path
        $pathdoc_sh = $this->config->item('vendor_docs');
        $chkpath = createPath($pathdoc_sh);
        if ($chkpath) {
            // Check data
            $chkres = $this->_checkvendordata($session_data);
            $out['msg'] = $chkres['msg'];
            $out['errfld'] = $chkres['fieds'];
            if ($chkres['result']==$this->success_result) {
                $newrec = 0;
                $vendor = $session_data['vendor'];
                // $vendor_contacts = $session_data['vendor_contacts'];
                $vendor_pricedocs = $session_data['vendor_pricedocs'];
                $vendor_otherdocs = $session_data['vendor_otherdocs'];
                $deleted = $session_data['deleted'];
                $vendor_id = $vendor['vendor_id'];
                // Save main data
                $this->db->set('vendor_name', $vendor['vendor_name']);
                $this->db->set('alt_name', $vendor['alt_name']);
                $this->db->set('vendor_type', $vendor['vendor_type']);
                $this->db->set('country_id', $vendor['country_id']);
                $this->db->set('vendor_asinumber', $vendor['vendor_asinumber']);
                $this->db->set('our_account_number', $vendor['our_account_number']);
                $this->db->set('vendor_website', $vendor['vendor_website']);
                $this->db->set('vendor_phone', $vendor['vendor_phone']);
                // $this->db->set('address', $vendor['address']);
                $this->db->set('address_line1', $vendor['address_line1']);
                $this->db->set('address_line2', $vendor['address_line2']);
                $this->db->set('address_city', $vendor['address_city']);
                $this->db->set('address_state', $vendor['address_state']);
                $this->db->set('address_zip', $vendor['address_zip']);
                $this->db->set('address_country', $vendor['address_country']);

                $this->db->set('general_note', $vendor['general_note']);
                $this->db->set('po_contact', $vendor['po_contact']);
                $this->db->set('po_phone', $vendor['po_phone']);
                $this->db->set('po_email', $vendor['po_email']);
                $this->db->set('po_ccemail', $vendor['po_ccemail']);
                $this->db->set('po_bcemail', $vendor['po_bcemail']);
                // $this->db->set('shipping_pickup', $vendor['shipping_pickup']);
                $this->db->set('shipaddr_line1', $vendor['shipaddr_line1']);
                $this->db->set('shipaddr_line2', $vendor['shipaddr_line2']);
                $this->db->set('shipaddr_city', $vendor['shipaddr_city']);
                $this->db->set('shipaddr_state', $vendor['shipaddr_state']);
                $this->db->set('vendor_zipcode', $vendor['vendor_zipcode']);
                $this->db->set('shipaddr_country', $vendor['shipaddr_country']);
                $this->db->set('po_note', $vendor['po_note']);
                $this->db->set('payment_contact', $vendor['payment_contact']);
                $this->db->set('payment_phone', $vendor['payment_phone']);
                $this->db->set('payment_email', $vendor['payment_email']);
                $this->db->set('payment_prepay', $vendor['payment_prepay']);
                $this->db->set('payment_terms', $vendor['payment_terms']);
                $this->db->set('payment_accept_visa', $vendor['payment_accept_visa']);
                $this->db->set('payment_accept_amex', $vendor['payment_accept_amex']);
                $this->db->set('payment_accept_check', $vendor['payment_accept_check']);
                $this->db->set('payment_accept_ach', $vendor['payment_accept_ach']);
                $this->db->set('payment_accept_paypal', $vendor['payment_accept_paypal']);
                $this->db->set('payment_accept_wire', $vendor['payment_accept_wire']);
                $this->db->set('checkout_details', $vendor['checkout_details']);
                $this->db->set('payment_note', $vendor['payment_note']);
                $this->db->set('pricing_contact', $vendor['pricing_contact']);
                $this->db->set('pricing_phone', $vendor['pricing_phone']);
                $this->db->set('pricing_email', $vendor['pricing_email']);
                $this->db->set('customer_contact', $vendor['customer_contact']);
                $this->db->set('customer_phone', $vendor['customer_phone']);
                $this->db->set('customer_email', $vendor['customer_email']);


//                $this->db->set('calendar_id', $vendor['calendar_id']);
//                $this->db->set('payinclude', $vendor['payinclude']);
//                $this->db->set('payinclorder', $vendor['payinclorder']);
//                $this->db->set('vendor_status', $vendor['vendor_status']);
//
//                $this->db->set('internal_po_note', $vendor['internal_po_note']);

                if ($vendor_id > 0) {
                    $this->db->where('vendor_id', $vendor_id);
                    $this->db->update('vendors');
                } else {
                    $newrec = 1;
                    $this->db->insert('vendors');
                    $vendor_id=$this->db->insert_id();
                }
                $out['msg'] = 'Error during insert / update record';
                if ($vendor_id > 0) {
                    $out['result'] = $this->success_result;
                    if ($newrec==1) {
                        $this->db->select('max(vendor_code) last_code, count(vendor_id) as cnt');
                        $this->db->from('vendors');
                        $vdres = $this->db->get()->row_array();
                        if ($vdres['cnt']==0) {
                            $vcode = 'V-50000';
                            $newcode = 50000;
                        } else {
                            $newcode = $vdres['last_code']+1;
                            $vcode = 'V-'.$newcode;
                        }
                        // $newslug = 'V-5'.str_pad($vendor_id,4,'0', STR_PAD_LEFT);
                        $this->db->where('vendor_id', $vendor_id);
                        $this->db->set('vendor_slug', $vcode);
                        $this->db->set('vendor_code', $newcode);
                        $this->db->update('vendors');
                    }
                    // Contacts
//                    $idx = 0;
//                    foreach ($vendor_contacts as $vendor_contact) {
//                        $this->db->set('vendor_id', $vendor_id);
//                        $this->db->set('contact_name', $vendor_contact['contact_name']);
//                        $this->db->set('contact_phone', $vendor_contact['contact_phone']);
//                        $this->db->set('contact_cellphone', $vendor_contact['contact_cellphone']);
//                        $this->db->set('contact_email', $vendor_contact['contact_email']);
//                        $this->db->set('contact_po', $vendor_contact['contact_po']);
//                        $this->db->set('contact_art', $vendor_contact['contact_art']);
//                        $this->db->set('contact_pay', $vendor_contact['contact_pay']);
//                        $this->db->set('contact_note', $vendor_contact['contact_note']);
//                        if ($vendor_contact['vendor_contact_id']>0) {
//                            $this->db->where('vendor_contact_id', $vendor_contact['vendor_contact_id']);
//                            $this->db->update('vendor_contacts');
//                        } else {
//                            $this->db->insert('vendor_contacts');
//                        }
//                        if ($idx==0) {
//                            $this->db->set('vendor_phone', $vendor_contact['contact_phone']);
//                            $this->db->set('vendor_email', $vendor_contact['contact_email']);
//                            $this->db->where('vendor_id', $vendor_id);
//                            $this->db->update('vendors');
//                        }
//                        $idx++;
//                    }
                    // Docs
                    $idx=1;
                    foreach ($vendor_pricedocs as $vendor_doc) {
                        if ($vendor_doc['vendor_doc_id']<0) {
                            $filename = 'vendoc_'.time().'_'.str_pad($idx,3,'0',STR_PAD_LEFT);
                            $srcdat = extract_filename($vendor_doc['doc_url']);
                            $filename.='.'.$srcdat['ext'];
                            $source = str_replace($this->config->item('pathpreload'),$this->config->item('upload_path_preload'), $vendor_doc['doc_url']);
                            $target = $this->config->item('vendor_docs_relative').$filename;
                            $res = @copy($source, $target);
                            $newfile = '';
                            if ($res) {
                                $newfile = $this->config->item('vendor_docs').$filename;
                            }
                            $vendor_doc['doc_url'] = $newfile;
                            $idx++;
                        }
                        if (!empty($vendor_doc['doc_url'])) {
                            $this->db->set('vendor_id', $vendor_id);
                            $this->db->set('doc_type','PRICELIST');
                            $this->db->set('doc_url', $vendor_doc['doc_url']);
                            $this->db->set('doc_name', $vendor_doc['doc_name']);
                            $this->db->set('doc_description', $vendor_doc['doc_description']);
                            $this->db->set('doc_year', $vendor_doc['doc_year']);
                            if ($vendor_doc['vendor_doc_id']>0) {
                                $this->db->where('vendor_doc_id', $vendor_doc['vendor_doc_id']);
                                $this->db->update('vendor_docs');
                            } else {
                                $this->db->insert('vendor_docs');
                            }
                        }
                    }
                    $idx=1;
                    foreach ($vendor_otherdocs as $vendor_doc) {
                        if ($vendor_doc['vendor_doc_id']<0) {
                            $filename = 'vendoc_'.time().'_'.str_pad($idx,3,'0',STR_PAD_LEFT);
                            $srcdat = extract_filename($vendor_doc['doc_url']);
                            $filename.='.'.$srcdat['ext'];
                            $source = str_replace($this->config->item('pathpreload'),$this->config->item('upload_path_preload'), $vendor_doc['doc_url']);
                            $target = $this->config->item('vendor_docs_relative').$filename;
                            $res = @copy($source, $target);
                            $newfile = '';
                            if ($res) {
                                $newfile = $this->config->item('vendor_docs').$filename;
                            }
                            $vendor_doc['doc_url'] = $newfile;
                            $idx++;
                        }
                        if (!empty($vendor_doc['doc_url'])) {
                            $this->db->set('vendor_id', $vendor_id);
                            $this->db->set('doc_type', 'OTHERS');
                            $this->db->set('doc_url', $vendor_doc['doc_url']);
                            $this->db->set('doc_name', $vendor_doc['doc_name']);
                            $this->db->set('doc_description', $vendor_doc['doc_description']);
                            if ($vendor_doc['vendor_doc_id']>0) {
                                $this->db->where('vendor_doc_id', $vendor_doc['vendor_doc_id']);
                                $this->db->update('vendor_docs');
                            } else {
                                $this->db->insert('vendor_docs');
                            }
                        }
                    }
                    // Deleted
                    foreach ($deleted as $item) {
                        if ($item['entity']=='contacts') {
                            $this->db->where('vendor_contact_id', $item['id']);
                            $this->db->delete('vendor_contacts');
                        } elseif ($item['entity']=='docs') {
                            $this->db->where('vendor_doc_id', $item['id']);
                            $this->db->delete('vendor_docs');
                        }
                    }
                    $out['result'] = $this->success_result;
                    // Clean session
                    usersession($session_id, null);
                }
            }
        }
        return $out;
    }

    private function _checkvendordata($session_data) {
        $out = ['result' => $this->success_result, 'msg' => ''];
        $vendor = $session_data['vendor'];
        // Type, Vendor Name, Country
        // $vendor_contacts = $session_data['vendor_contacts'];
        $errmsg = '';
        $errorfld = [];
        // Empty name
        if (empty($vendor['vendor_name'])) {
            $errmsg.='Empty Vendor Name'.PHP_EOL;
            array_push($errorfld,'vendor_name');
            $out['result'] = $this->error_result;
        }
        // if (empty($vendor['alt_name'])) {
        //    $errmsg.='Empty Vendor Alt Name'.PHP_EOL;
        // }
        if (empty($vendor['vendor_slug'])) {
            $errmsg.='Empty Vendor Number'.PHP_EOL;
            array_push($errorfld,'vendor_slug');
            $out['result'] = $this->error_result;
        } else {
            $this->db->select('count(vendor_id) as cnt');
            $this->db->from('vendors');
            $this->db->where('vendor_slug', strtoupper($vendor['vendor_slug']));
            $this->db->where('vendor_id != ', $vendor['vendor_id']);
            $slugchk = $this->db->get()->row_array();
            if ($slugchk['cnt'] > 0) {
                $errmsg.='Vendor # Not Unique'.PHP_EOL;
                array_push($errorfld,'vendor_slug');
                $out['result'] = $this->error_result;
            }
        }
        if (empty($vendor['vendor_type'])) {
            $errmsg.='Empty Vendor Type'.PHP_EOL;
            array_push($errorfld,'vendor_type');
            $out['result'] = $this->error_result;
        }
        if (empty($vendor['country_id'])) {
            $errmsg.='Empty Vendor Country'.PHP_EOL;
            array_push($errorfld,'country_id');
            $out['result'] = $this->error_result;
        }
        // vendor_type, country_id
        // Contacts
//        $idx =1;
//        foreach ($vendor_contacts as $vendor_contact) {
//            if (empty($vendor_contact['contact_name'])) {
//                $errmsg.='Contact # '.$idx.' Contact Name Empty'.PHP_EOL;
//            }
//            if (!empty($vendor_contact['contact_email']) && !valid_email_address($vendor_contact['contact_email'])) {
//                $errmsg.='Contact '.$idx.' Contact email '.$vendor_contact['contact_email'].' Not Valid'.PHP_EOL;
//            }
//            $idx++;
//        }

        $out['msg'] = $errmsg;
        $out['fieds'] = $errorfld;
        return $out;
    }

    // Get Vendor for new edit
    public function get_item_vendor($vendor_item_id, $inventory_item_id=0) {
        $this->db->select('vi.*');
        $this->db->select('v.vendor_name, cnt.country_iso_code_3 as item_shipcountry_name, cnt.country_iso_code_2 as item_shipcountry_code');
        $this->db->from('sb_vendor_items vi');
        $this->db->join('vendors v','v.vendor_id=vi.vendor_item_vendor');
        $this->db->join('ts_countries cnt','cnt.country_id=vi.item_shipcountry','left');
        $this->db->where('vendor_item_id', $vendor_item_id);
        $vitem = $this->db->get()->row_array();
        if (!empty($inventory_item_id)) {
            $this->load->model('inventory_model');
            $res = $this->inventory_model->get_inventory_item($inventory_item_id);
            if ($res['result']==$this->success_result) {
                $invdata = $res['data'];
                $vitem['vendor_item_cost'] = $invdata['avg_price'];
                $vitem['vendor_item_blankcost'] = $invdata['avg_price'];
            }
        }
        return $vitem;
    }

    public function get_item_vendorprice($vendor_item_id) {
        $this->db->select('*');
        $this->db->from('sb_vendor_prices');
        $this->db->where('vendor_item_id', $vendor_item_id);
        $vprices = $this->db->get()->result_array();
        return $vprices;
    }

    public function get_vendors_oldlist($options) {
        $this->db->select('v.*, c.calendar_name');
        $this->db->from('vendors v');
        $this->db->join('calendars c','c.calendar_id=v.calendar_id','left');
        if (isset($options['limit'])) {
            if (isset($options['offset'])) {
                $this->db->limit($options['limit'], $options['offset']);
            } else {
                $this->db->limit($options['limit']);
            }
        }
        if (isset($options['order_by'])) {
            if (isset($options['direct'])) {
                $this->db->order_by($options['order_by'], $options['direct']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }
        $results=$this->db->get()->result_array();
        return $results;

    }

    public function get_vendor_partners() {
        $partners = $this->db->select('vendor_id, vendor_name')->from('vendors')->where('partner', 1)->order_by('vendor_name')->get()->result_array();
        $vendors = [];
        foreach ($partners as $partner) {
            $vendors[] = ['vendor_id' => $partner['vendor_id'],'vendor_name' => $partner['vendor_name']];
        }
        if (count($partners) > 0) {
            $vendors[] = ['vendor_id' => -1, 'vendor_name' => '-------'];
        }
        $others = $this->db->select('vendor_id, vendor_name')->from('vendors')->where('partner', 0)->order_by('vendor_name')->get()->result_array();
        foreach ($others as $partner) {
            $vendors[] = ['vendor_id' => $partner['vendor_id'],'vendor_name' => $partner['vendor_name']];
        }
        return $vendors;
    }

}