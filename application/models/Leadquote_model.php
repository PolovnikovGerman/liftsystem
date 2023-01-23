<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leadquote_model extends MY_Model
{
    private $stresstemplate = 'Stressballs.com';
    private $bluehealthtemplate = 'Bluetrack Health';
    private $proformatemplate = 'Proforma Invoice';
    private $suppliertemplate = 'Supplier';
    private $sbnumber = 11000;
    private $srnumber = 7500;
    private $box_empty_weight = 25;
    function __construct() {
        parent::__construct();
    }

    public function add_leadquote($lead_data, $usr_id, $user_name) {
        $response = ['result' => $this->error_result, 'msg' => 'USER Not Found'];
        // Get new Quote #
        $newnum = $this->get_newquote_number($lead_data['brand']);
        $this->load->model('user_model');
        $usrdat = $this->user_model->get_user_data($usr_id);
        if (ifset($usrdat, 'user_id',0) > 0) {
            $response['result'] = $this->success_result;
            // Items
            $quote_items = [];
            if (!empty($lead_data['lead_item_id'])) {
                $quote_items = $this->add_leadquote_items($lead_data['lead_item_id']);
                $quote_items['quote_item_id'] = -1;
            }
            $outdat = [
                'brand' => $lead_data['brand'],
                'quote_number' => $newnum,
                'quote_date' => time(),
                'quote_template' => $this->stresstemplate,
                'mischrg_label1' => '',
                'mischrg_value1' => 0,
                'mischrg_label2' => '',
                'mischrg_value2' => 0,
                'discount_label' => '',
                'discount_value' => 0,
                'shipping_country' => $this->config->item('default_country'),
                'shipping_contact' => '',
                'shipping_company' => '',
                'shipping_address1' => '',
                'shipping_address2' => '',
                'shipping_zip' => '',
                'shipping_city' => '',
                'shipping_state' => '',
                'sales_tax' => 0,
                'tax_exempt' => 0,
                'tax_reason' => '',
                'rush_terms' => '',
                'rush_days' => 0,
                'rush_cost' => 0,
                'shipping_cost' => 0,
                'billing_country' => $this->config->item('default_country'),
                'billing_contact' => '',
                'billing_company' => '',
                'billing_address1' => '',
                'billing_address2' => '',
                'billing_zip' => '',
                'billing_city' => '',
                'billing_state' => '',
                'quote_note' => '',
                'quote_repcontact' => $usrdat['email_signature'],
                'quote_itemcount' => count($quote_items),
                'quote_items' => $quote_items,
            ];
            $response['data'] = $outdat;
        }
        return $response;
    }

    public function get_newquote_number($brand) {
        $this->db->select('count(quote_id) as cnt, max(quote_number) as numb');
        $this->db->from('ts_quotes');
        $this->db->where('brand', $brand);
        $res = $this->db->get()->row_array();
        if ($res['cnt']==0) {
            if ($brand=='SR') {
                $newnumber = $this->srnumber;
            } else {
                $newnumber = $this->sbnumber;
            }
        } else {
            $newnumber = $res['numb']+1;
        }
        return $newnumber;
    }

    public function add_leadquote_items($item_id) {
        if ($item_id < 0) {
            $this->load->config('shipping');
            $item = [
                'item_id' => $item_id,
                'item_qty' => 0,
                'item_price' => 0.000,
                'imprint_price' => 0.00,
                'setup_price' => 0.00,
                'item_weigth' => $this->box_empty_weight / $this->config->item('default_inpack'),
                'cartoon_qty' => $this->config->item('default_inpack'),
                'cartoon_width' => $this->config->item('default_pack_width'),
                'cartoon_heigh' => $this->config->item('default_pack_heigth'),
                'cartoon_depth' => $this->config->item('default_pack_depth'),
                'template' => 'Stressball',
                'base_price' => 0.000,
            ];
        } else {
            $this->load->model('leadorder_model');
            $itemdat = $this->leadorder_model->_get_itemdata($item_id);
            $item = [
                'item_id' => $item_id,
                'item_qty' => 0,
                'item_price' => 0.000,
                'imprint_price' => 0.00,
                'setup_price' => 0.00,
                'item_weigth' => $this->box_empty_weight / $this->config->item('default_inpack'),
                'cartoon_qty' => $this->config->item('default_inpack'),
                'cartoon_width' => $this->config->item('default_pack_width'),
                'cartoon_heigh' => $this->config->item('default_pack_heigth'),
                'cartoon_depth' => $this->config->item('default_pack_depth'),
                'template' => 'Stressball',
                'base_price' => 0.000,
            ];


        }
        return $item;
    }

}