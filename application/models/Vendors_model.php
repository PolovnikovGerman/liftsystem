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

    public function get_count_vendors() {
        $this->db->select('count(vendor_id) as cnt');
        $this->db->from('vendors');
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_vendors_list($options) {
        $this->db->select('v.*, c.country_name');
        $this->db->from('vendors v');
        $this->db->join('ts_countries c','c.country_id=v.country_id','left');
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
            $result['item_qty'] = $qty['cnt'];
            $this->db->select('contact_name, contact_phone, contact_cellphone, contact_email');
            $this->db->from('vendor_contacts');
            $this->db->where('vendor_id', $result['vendor_id']);
            $contact = $this->db->get()->row_array();
            $result['contact_name'] = ifset($contact, 'contact_name','');
            $result['contact_phone'] = ifset($contact, 'contact_phone','');
            $result['contact_email'] = ifset($contact, 'contact_email','');
            $res[]= $result;
        }
        return $res;
    }

    public function add_vendor() {
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
	        'country_id' => '',
	        'alt_name' => '',
	        'our_account_number' => '',
	        'address text' => '',
	        'shipping_pickup' => '',
	        'payment_accept_visa' => 0,
	        'payment_accept_amex' => 0,
	        'payment_accept_terms' => 0,
	        'payment_accept_check' => 0,
	        'payment_accept_wire' => 0,
	        'po_note' => '',
	        'internal_po_note' => '',
	        'vendor_status' => 1,
	        'vendor_slug' => '',
        );
        return [
            'vendor' => $vendor,
            'vendor_contacts' => [],
            'vendor_docs' => [],
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
            $this->db->select('*');
            $this->db->from('vendor_contacts');
            $this->db->where('vendor_id', $vendor_id);
            $out['vendor_contacts'] = $this->db->get()->result_array();
            $this->db->select('*');
            $this->db->from('vendor_docs');
            $this->db->where('vendor_id', $vendor_id);
            $out['vendor_docs'] = $this->db->get()->result_array();
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
}
