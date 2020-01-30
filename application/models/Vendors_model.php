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
        if (intval($vendor_id)!=0) {
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


}
