<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leadquote_model extends MY_Model
{
    private $stresstemplate = 'Stressballs.com';
    private $bluehealthtemplate = 'Bluetrack Health';
    private $proformatemplate = 'Proforma Invoice';
    private $suppliertemplate = 'Supplier';
    function __construct() {
        parent::__construct();
    }

    public function add_leadquote($lead_data, $usr_id, $user_name) {
        $response = ['result' => $this->error_result, 'msg' => 'Item Not Found'];
        // Get new Quote #
        $newnum = $this->get_newquote_number($lead_data['brand']);
        $this->load->model('leads_model');
        $itmres = $this->leads_model->search_itemid($lead_data['lead_item_id']);
        if ($itmres['result']==$this->success_result) {
            $response['result'] = $this->success_result;
            $outdat = [
                'brand' => $lead_data['brand'],
                'quote_number' => $newnum,
                'quote_date' => time(),
                'quote_template' => $this->stresstemplate,
                'item_id' => $lead_data['lead_item_id'],
                'item_number' => $itmres['item_number'],
                'item_description' => $itmres['item_name'],
                'item_color' => '',
                'item_qty' => '',
                'item_price' => '',
                'mischrg_label1' => '',
                'mischrg_value1' => 0,
                'mischrg_label2' => '',
                'mischrg_value2' => 0,
                'discount_label' => '',
                'discount_value' => 0,
                ''
            ];
        }
        return $response;
    }

    public function get_newquote_number($brand) {
        $this->db->select('count(quote_id) as cnt, max(quote_number) as numb');
        $this->db->from('ts_quotes');
        $this->db->where('brand', $brand);
        $res = $this->db->get()->row_array();
        if ($res['cnt']==0) {
            $newnumber = 1;
        } else {
            $newnumber = $res['numb']+1;
        }
        return $newnumber;
    }

}