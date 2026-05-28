<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Customform_model extends MY_Model
{
    private $tax_state = 'NJ';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_count_forms($params) {
        $this->db->select('count(q.custom_quote_id) as cnt');
        $this->db->from('ts_custom_quotes q');
        if (ifset($params,'assign','')=='1') {
            $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
            $this->db->where('le.leademail_id is null');
        }
        if (ifset($params,'search','')!=='') {
            $this->db->like('concat(customer_name, customer_company,customer_email)', $params['search']);
        }
        if (ifset($params,'hideincl',0)==1) {
            $this->db->where('active', $params['hideincl']);
        }
        if (ifset($params,'brand','')!=='') {
            if ($params['brand']=='SR') {
                $this->db->where('brand', $params['brand']);
            } else {
                $this->db->where_in('brand', ['BT','SB']);
            }
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function get_customform_data($options) {
        $pagenum = ifset($options,'offset',0);
        $limit = ifset($options,'limit',0);
        $offset = $pagenum * $limit;
        $this->db->select('q.*,le.lead_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        if (ifset($options,'search','')!=='') {
            // Search by customer, company, email
            $this->db->like('concat(q.customer_name, q.customer_company,q.customer_email)', $options['search']);
        }
        if (ifset($options,'assign','')==1) {
            // Assign
            $this->db->where('le.leademail_id is null');
        }
        if (ifset($options,'brand','')!=='') {
            if ($options['brand']=='SR') {
                $this->db->where('q.brand', $options['brand']);
            } else {
                $this->db->where_in('q.brand', ['BT','SB']);
            }
        }
        if (ifset($options,'hideincl',0)==1) {
            $this->db->where('q.active', $options['hideincl']);
        }
        if ($limit !==0 ) {
            if ($offset !==0) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        if (ifset($options,'order_by','')!=='') {
            if (ifset($options,'direction','')!=='') {
                $this->db->order_by($options['order_by'], $options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }

        $dats = $this->db->get()->result_array();
        $out = [];
        // $numpp = $offset;
        $numpp = 0;
        $this->load->model('leads_model');
        foreach ($dats as $dat) {
            $numpp++;
            $dat['numorder'] = $numpp;
            $dat['numpp'] = $dat['quote_number'];
            if (!empty($dat['lead_id'])) {
                $ldat = $this->leads_model->get_lead($dat['lead_id']);
                if ($ldat['result']==$this->success_result) {
                    $lead = $ldat['lead'];
                    $dat['lead_number']=ifset($lead,'lead_number','');
                }

            }
            if (empty($dat['ship_date'])) {
                $dat['event_date'] = '';
            } else {
                $dat['event_date'] = date('m/d/y', $dat['ship_date']);
            }
            $dat['weeknum'] = date('Y-W', strtotime($dat['date_add']));
            $out[] = $dat;
        }
        return $out;
    }

    public function get_customform_details($custom_quote_id) {
        $out=['result' => $this->error_result, 'msg' => 'Info doesn\'t found'];
        $this->db->select('q.*, c.country_name, c.country_iso_code_2, le.lead_id, le.leademail_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_countries c','c.country_id=q.ship_country','left');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        $this->db->where('q.custom_quote_id', $custom_quote_id);
        $data = $this->db->get()->row_array();
        if (ifset($data,'custom_quote_id',0)==$custom_quote_id) {
            $out['result'] = $this->success_result;
            if (empty($data['lead_id'])) {
                // $data['lead_date'] = $data['lead_customer'] = $data['lead_mail'] = '';
                $data['lead_number'] = $lead_data = '';
            } else {
                $this->db->select('lead_date, lead_customer, lead_mail, lead_number');
                $this->db->from('ts_leads');
                $this->db->where('lead_id', $data['lead_id']);
                $leaddat = $this->db->get()->row_array();
                $data['lead_date'] = $leaddat['lead_date'];
//                $data['lead_customer'] = $leaddat['lead_customer'];
//                $data['lead_mail'] = $leaddat['lead_mail'];
                $data['lead_number'] = $leaddat['lead_number'];
            }
            $out['data'] = $data;
            // Attachments
            $this->db->select('*');
            $this->db->from('ts_customquote_attachment');
            $this->db->where('custom_quote_id', $custom_quote_id);
            $attach = $this->db->get()->result_array();
            $out['attach'] = $attach;
        }
        return $out;
    }

    public function update_customforn($options) {
        $this->db->where('custom_quote_id', $options['form_id']);
        $this->db->set('active', ifset($options,'activity', 0));
        $this->db->update('ts_custom_quotes');
        return true;
    }

    public function get_customform_totals($brand)
    {
        $date_string = date('Y-m-d');
        $weekdat = explode('-',date("W-Y", strtotime($date_string)));
        $dats = getDatesByWeek($weekdat[0], $weekdat[1]);
        $monday = $dats['start_week'];
        $sunday = $dats['end_week'];
        $weeks = [];
        $numweeks = 52 * 3;
        for ($i=0; $i < $numweeks; $i++) {
            $startd = strtotime("-".$i." week", $monday);
            $finishd = strtotime("-".$i." week", $sunday);
            $curweek = [
                'week' => date('M j, y', $startd),
                'mon' => 0,
                'tue' => 0,
                'wed' => 0,
                'thu' => 0,
                'fri' => 0,
                'sat' => 0,
                'sun' => 0,
                'total' => 0
            ];
            // Get data
            $this->db->select('date_format(date_add, "%w") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
            if ($brand!=='ALL') {
                if ($brand=='SR') {
                    $this->db->where('brand', $brand);
                } else {
                    $this->db->where_in('brand', ['SB','BT']);
                }
            }
            $this->db->where('unix_timestamp(date_add) >=', $startd);
            $this->db->where('unix_timestamp(date_add) <=', $finishd);
            $this->db->group_by('dayw');
            $quotes = $this->db->get()->result_array();
            foreach ($quotes as $quote) {
                if ($quote['dayw']==0) {
                    $curweek['sun']+=$quote['cnt'];
                } elseif ($quote['dayw']==1) {
                    $curweek['mon']+=$quote['cnt'];
                } elseif ($quote['dayw']==2) {
                    $curweek['tue']+=$quote['cnt'];
                } elseif ($quote['dayw']==3) {
                    $curweek['wed']+=$quote['cnt'];
                } elseif ($quote['dayw']==4) {
                    $curweek['thu']+=$quote['cnt'];
                } elseif ($quote['dayw']==5) {
                    $curweek['fri']+=$quote['cnt'];
                } elseif ($quote['dayw']==6) {
                    $curweek['sat']+=$quote['cnt'];
                }
                $curweek['total']+=$quote['cnt'];
            }
            $weeks[] = $curweek;
        }
        return $weeks;
    }

    public function get_customform_totalchart($brand)
    {
        $date_string = date('Y-m-d');
        $weekdat = explode('-',date("W-Y", strtotime($date_string)));
        $dats = getDatesByWeek($weekdat[0], $weekdat[1]);
        $monday = $dats['start_week'];
        $sunday = $dats['end_week'];
        $maxdat = $sunday;
//         $mindat = strtotime('-52 weeks', $monday);
        $mindat = strtotime('-156 weeks', $monday);
        $this->db->select('date_format(date_add, "%X-%V") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
        }
        $this->db->where('unix_timestamp(date_add) >=', $mindat);
        $this->db->where('unix_timestamp(date_add) <=', $maxdat);
        $this->db->group_by('dayw');
        $results = $this->db->get()->result_array();
        $data = [];
        $labels = [];
        foreach ($results as $result) {
            $days = explode('-', $result['dayw']);
            $dates = getDatesByWeek($days[1], $days[0]);
            $labels[] = date('M`y', $dates['start_week']);
            // $labels[] = $days[1].'/'.$days[0];
            // $labels[] = $result['dayw'];
            $data[] = $result['cnt'];
        }
        return ['labels'=>$labels,'data'=>$data];
    }

    public function get_customform_monthtotals($brand)
    {
        $months = [];
        $years = [];
        $curyear = intval(date('Y'));
        for ($i=3; $i >= 0; $i--) {
            $years[] = $curyear-$i;
        }
        for($j=1; $j<=12; $j++) {
            $monthname = date('F', strtotime('2012-'.str_pad($j,2,'0',STR_PAD_LEFT).'-01'));
            $monthrow = [
                'month_id' => str_pad($j,2,'0',STR_PAD_LEFT),
                'month' => $monthname,
            ];
            foreach ($years as $year) {
                $monthrow[str_pad($j,2,'0',STR_PAD_LEFT).'-'.$year] = 0;
            }
            $months[] = $monthrow;
        }
        $monthrow = [
            'month_id' => 0,
            'month' => 'Total',
        ];
        foreach ($years as $year) {
            $monthrow['0-'.$year] = 0;
        }
        $months[] = $monthrow;
        $startdate = strtotime($years[0].'-01-01');
        $this->db->select('date_format(date_add, "%m-%Y") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
        }
        $this->db->where('unix_timestamp(date_add) >= ', $startdate);
        $this->db->group_by('dayw');
        $results = $this->db->get()->result_array();
        foreach ($results as $result) {
            $dd = 1;
            $datkey = explode('-', $result['dayw']);
            $mdat = $datkey[0];
            $ydat = $datkey[1];
            $idx = 0;
            foreach ($months as $month) {
                if ($month['month_id']==$mdat) {
                    $months[$idx][$result['dayw']]+=$result['cnt'];
                    break;
                }
                $idx++;
            }
            $idx = 0;
            foreach ($months as $month) {
                if ($month['month_id']==0) {
                    $months[$idx]['0-'.$ydat]+=$result['cnt'];
                    break;
                }
                $idx++;
            }
        }
        return ['totals' => $months, 'years' => $years];
    }

    public function get_customform_monthchart($brand)
    {
        $curyear = intval(date('Y'));
        $yearstart = $curyear-3;
        $datestart = strtotime($yearstart.'-01-01');

        $this->db->select('date_format(date_add, "%Y-%m") as dayw, count(custom_quote_id) as cnt')->from('ts_custom_quotes');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('brand', $brand);
            } else {
                $this->db->where_in('brand', ['SB','BT']);
            }
        }
        $this->db->where('unix_timestamp(date_add) >=', $datestart);
        $this->db->group_by('dayw');
        $results = $this->db->get()->result_array();
        $data = [];
        $labels = [];
        foreach ($results as $result) {
            $days = explode('-', $result['dayw']);
            $date = strtotime($days[0].'-'.$days[1].'-01');
            $labels[] = date('M`y', $date);
            $data[] = $result['cnt'];
        }
        return ['labels'=>$labels,'data'=>$data];
    }

    public function get_customform_interest($brand, $showall=1)
    {
        $this->db->select('q.*,le.lead_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        $this->db->where('le.leademail_id is null');
        if ($brand!=='ALL') {
            if ($brand=='SR') {
                $this->db->where('q.brand', $brand);
            } else {
                $this->db->where_in('q.brand', ['SB','BT']);
            }
        }
        $this->db->where('q.active', 1);
        if ($showall==0) {
            $limitdate = strtotime('now - 90 days');
            $this->db->where('unix_timestamp(q.date_add) >= ', $limitdate);
        }
        $this->db->order_by('q.custom_quote_id', 'desc');
        $dats = $this->db->get()->result_array();
        return $dats;
    }

    public function update_customformdetails($data, $custom_quote_id)
    {
        $fld = $data['fld'];
        $newval = $data['newval'];
        $this->db->where('custom_quote_id', $custom_quote_id);
        $this->db->set($fld, $newval);
        $this->db->update('ts_custom_quotes');
        return true;
    }

    public function get_customquote_prices($options=array())
    {
        $this->db->select('*')->from('ts_customquote_prices');
        if (isset($options['order_by']) && !empty($options['order_by'])) {
            if (isset($options['direction'])) {
                $this->db->order_by($options['order_by'], $options['direction']);
            } else {
                $this->db->order_by($options['order_by']);
            }
        }
        return $this->db->get()->result_array();
    }

    public function get_customquote_price($price_id)
    {
        $out = ['result'=>$this->error_result, 'msg'=>'Custom price not found'];
        if ($price_id==0) {
            $out['result']=$this->success_result;
            $data = [
                'price_id' => $price_id,
                'qty' => 0,
                'price' => 0,
            ];
            $out['data']=$data;
        } else {
            $res = $this->db->select('*')->from('ts_customquote_prices')->where('price_id', $price_id)->get()->row_array();
            if (isset($res['price_id'])) {
                $out['result'] = $this->success_result;
                $out['data'] = $res;
            }
        }
        return $out;
    }

    public function save_customquote_price($price_id, $qty, $price)
    {
        $out=['result'=>$this->error_result, 'msg'=>'Custom price duplicated'];
        // Check - may be exist such qty
        $this->db->select('count(price_id) as cnt')->from('ts_customquote_prices')->where('qty', $qty)->where('price_id != ', $price_id);
        $chkres = $this->db->get()->row_array();
        if ($chkres['cnt']==0) {
            $out['result']=$this->success_result;
            $this->db->set('qty', $qty);
            $this->db->set('price', $price);
            if ($price_id==0) {
                $this->db->insert('ts_customquote_prices');
            } else {
                $this->db->where('price_id', $price_id);
                $this->db->update('ts_customquote_prices');
            }
            $out['data'] = $this->db->select('*')->from('ts_customquote_prices')->order_by('qty')->get()->result_array();
        }
        return $out;
    }

    public function remove_customquote_price($price_id)
    {
        $out=['result'=>$this->error_result, 'msg'=>'Custom price not found'];
        $this->db->select('count(price_id) as cnt')->from('ts_customquote_prices')->where('price_id', $price_id);
        $chkres = $this->db->get()->row_array();
        if ($chkres['cnt'] > 0) {
            $out['result']=$this->success_result;
            $this->db->where('price_id', $price_id);
            $this->db->delete('ts_customquote_prices');
            $out['data'] = $this->db->select('*')->from('ts_customquote_prices')->order_by('qty')->get()->result_array();
        }
        return $out;
    }

    public function get_customquote_pricevalue($qty)
    {
        $outprice = 0;
        $pricelist = $this->db->select('*')->from('ts_customquote_prices')->order_by('qty')->get()->result_array();
        foreach ($pricelist as $list) {
            if ($list['qty']>$qty) {
                break;
            }
            $outprice = $list['price'];
        }
        return $outprice;
    }

    public function customquote_transform()
    {
        $start = @getenv('CUSTOMQUOTE_START');
        if (empty($start)) {
            $start = 3411;
        }
        // Get nearest quote
        $this->db->select('q.*,le.lead_id');
        $this->db->from('ts_custom_quotes q');
        $this->db->join('ts_lead_emails le','le.custom_quote_id=q.custom_quote_id','left');
        $this->db->where('le.leademail_id is null');
        $this->db->where('q.active', 1);
        $this->db->where('q.custom_quote_id >= ', $start);
        $this->db->order_by('q.custom_quote_id');
        $data = $this->db->get()->row_array();
        if (ifset($data, 'custom_quote_id',0) > 0) {
            $customquote = $data['custom_quote_id'];
            // Get Custom Quote details
            $res = $this->customform_model->get_customform_details($customquote);
            if ($res['result']==$this->success_result) {
                $formdata = $res['data'];
                if (isset($res['attach']) && count($res['attach']) > 0) {
                    $formdata['attach'] = $res['attach'];
                }
                $this->load->model('leads_model');
                $this->load->model('leadquote_model');
                $leadpost=[
                    'lead_id'=>0,
                    'lead_company'=> $formdata['customer_company'],
                    'lead_phone'=> $formdata['customer_phone'],
                    'lead_customer'=> $formdata['customer_name'],
                    'lead_mail'=> $formdata['customer_email'],
                    'lead_itemqty'=> $formdata['quota_qty'],
                    'lead_item'=> 'Custom Item',
                    'lead_item_id' => $this->config->item('custom_id'),
                    'lead_needby'=> (empty($formdata['ship_date']) ? NULL : date('Y-m-d', $formdata['ship_date'])),
                    'lead_status'=>'',
                    'lead_value' => '',
                    'lead_note' => $formdata['shape_desription'],
                    'lead_type'=> $this->leads_model->init_lead_type,
                    'country_id' => $formdata['ship_country'],
                    'state' => strtoupper($formdata['ship_state']),
                    'city' => $formdata['ship_city'],
                    'zip' => $formdata['ship_zipcode'],
                    'address_1' => $formdata['ship_address1'],
                    'address_2' => $formdata['ship_address2'],
                    'brand' => $formdata['brand'],
                ];
                if (!empty($formdata['shape_type'])) {
                    $leadpost['lead_customtype'] = $formdata['shape_type'];
                }
                // Create Lead
                $dat = $this->leads_model->onlinequote_addlead($leadpost);
                if ($dat['result'] > 0) {
                    $lead_id = $dat['result'];
                    if (isset($formdata['attach'])) {
                        $attachments = $formdata['attach'];
                        foreach ($attachments as $attachment) {
                            $this->db->set('lead_id', $lead_id);
                            $this->db->set('source_name', $attachment['source_name']);
                            $this->db->set('attachment', $attachment['attachment']);
                            $this->db->set('quoteattach', 1);
                            $this->db->insert('ts_lead_attachs');
                        }
                    }
                    // Add relation
                    $this->db->set('lead_id', $lead_id);
                    $this->db->set('custom_quote_id', $formdata['custom_quote_id']);
                    $this->db->insert('ts_lead_emails');
                    // Add quotes
                    $leadsrc = $this->leads_model->get_lead($lead_id);
                    $contacts = $this->leads_model->get_lead_contacts($lead_id);
                    $lead = $leadsrc['lead'];
                    $address = $leadsrc['address'];
                    $custom_item = 1;
                    $quotesqty = [
                        $formdata['quota_qty']-500, $formdata['quota_qty'], $formdata['quota_qty']+500,
                    ];
                    foreach ($quotesqty as $sqty) {
                        $item_price = $this->get_customquote_pricevalue($sqty);
                        if ($sqty >= 6000) {
                            echo 'Lead '.$lead_id.' QTY '.$sqty.' Price '.$item_price.PHP_EOL;
                        }
                        $print_price = $this->leadquote_model->custom_print_price;
                        if ($lead['brand']=='SR') {
                            $setup_price = $this->leadquote_model->custom_srsetup_price;
                        } else {
                            $setup_price = $this->leadquote_model->custom_setup_price;
                        }
                        $design_price = $this->config->item('custom_mischrg_value');
                        $locations = [];
                        $locations[] = [
                            'location' => 1,
                            'prints' => 5,
                        ];
                        $quoteparams = [
                            'lead' => $lead,
                            'address' => $address,
                            'contacts' => $contacts,
                            'custom_item' => $custom_item,
                            'item_qty' => $sqty,
                            'item_price' => $item_price,
                            'print_price' => $print_price,
                            'setup_price' => $setup_price,
                            'design_price' => $design_price,
                            'locations' => $locations,
                            'setuptype' => 'NEW',
                            'setupnote' => '',
                            'designtype' => 'NEW',
                            'designnote' => '',
                            'repcontact_note' => $this->config->item('custom_quote_note'),
                        ];
                        $this->_convert_customform_quote($quoteparams);
                    }
                }
            }
        }
    }

    private function _convert_customform_quote($quoteparams)
    {
        $this->load->model('leadquote_model');
        $lead_data = $quoteparams['lead'];
        $lead_address = $quoteparams['address'];
        if (ifset($lead_address,'country_id','')=='') {
            $lead_address['country_id'] = $this->config->item('default_country');
        }
        $lead_contacts = $quoteparams['contacts'];
        $zipcode = $lead_address['zip'];
        $locations = $quoteparams['locations'];
        // Get new Quote #
        $newnum = $this->leadquote_model->get_newquote_number($lead_data['brand']);
        $taxcalc = 0;
        $item_subtotal = 0;
        // Add a Quote
        $this->db->set('lead_id', $lead_data['lead_id']);
        $this->db->set('brand', $lead_data['brand']);
        $this->db->set('quote_number', $newnum);
        $this->db->set('quote_date', time());
        $this->db->set('quote_template', 'Quote');
        $this->db->set('mischrg_label1', $this->config->item('custom_mischrg_label'));
        $this->db->set('mischrg_value1', $quoteparams['design_price']);
        $item_subtotal += $quoteparams['design_price'];
        $this->db->set('shipping_country', $lead_address['country_id']);
        $this->db->set('shipping_company', $lead_data['lead_company']);
        $this->db->set('shipping_address1', $lead_address['address_line1']);
        $this->db->set('shipping_address2', $lead_address['address_line2']);
        $this->db->set('shipping_zip', $zipcode);
        $this->db->set('shipping_city', $lead_address['city']);
        $this->db->set('shipping_state', $lead_address['state']);
        if ($lead_address['state']==$this->tax_state) {
            $this->db->set('taxview', 1);
            $taxcalc = 1;
        }
        $this->db->set('billing_country', $lead_address['country_id']);
        $this->db->set('billing_company', $lead_data['lead_company']);
        $this->db->set('billing_address1', $lead_address['address_line1']);
        $this->db->set('billing_address2', $lead_address['address_line2']);
        $this->db->set('billing_zip', $zipcode);
        $this->db->set('billing_city', $lead_address['city']);
        $this->db->set('billing_state', $lead_address['state']);
        $this->db->set('quote_repcontact', $quoteparams['repcontact_note']);
        $this->db->set('quote_blank', 0);
        $this->db->set('create_time', date('Y-m-d H:i:s'));
        $this->db->insert('ts_quotes');
        if ($this->db->insert_id()>0) {
            // $response['result'] = $this->success_result;
            $quote_id = $this->db->insert_id();
            // $response['quote_id'] = $quote_id;
            // Contacts
            foreach ($lead_contacts as $contact) {
                if (!empty($contact['contact_name'])) {
                    $this->db->where('quote_id', $quote_id);
                    $this->db->set('shipping_contact', $contact['contact_name']);
                    $this->db->set('billing_contact', $contact['contact_name']);
                    $this->db->update('ts_quotes');
                }
            }
            $this->load->model('orders_model');
            $itemdata=$this->orders_model->get_newitemdat($lead_data['lead_item_id']);
            $item_description=(empty($lead_data['other_item_name']) ? 'Custom Shaped Stress Balls' : $lead_data['other_item_name']);
            $colors=$itemdata['colors'];
            $itmcolor='';
            if ($itemdata['num_colors']>0) {
                $itmcolor=$colors[0];
            }
            $this->db->set('quote_id', $quote_id);
            $this->db->set('item_id', $lead_data['lead_item_id']);
            $this->db->set('item_qty', $quoteparams['item_qty']);
            $this->db->set('item_price', $quoteparams['item_price']);
            $this->db->set('imprint_price', $quoteparams['print_price']);
            $this->db->set('setup_price', $quoteparams['setup_price']);
            $this->db->insert('ts_quote_items');
            if ($this->db->insert_id()>0) {
                $quote_item_id = $this->db->insert_id();
                $invcolor = '';
                $item_subtotal+=$quoteparams['item_qty']*$quoteparams['item_price'];
                $this->db->set('quote_item_id', $quote_item_id);
                $this->db->set('item_description', $item_description);
                $this->db->set('item_color', $itmcolor);
                $this->db->set('item_qty', $quoteparams['item_qty']);
                $this->db->set('item_price', $quoteparams['item_price']);
                $this->db->insert('ts_quote_itemcolors');
                if ($this->db->insert_id()) {
                    $quote_itemcolor_id = $this->db->insert_id();
                    $numloc = 1;
                    foreach ($locations as $location) {
                        $this->db->set('quote_item_id', $quote_item_id);
                        $this->db->set('imprint_active', 1);
                        $this->db->set('num_colors', $location['prints']);
                        if ($location['prints']==1 || $location['prints']>4) {
                            if ($location['location']==1) {
                                $this->db->set('print_1',0);
                            } else {
                                $this->db->set('print_1', $quoteparams['print_price']);
                            }
                        }
                        $this->db->set('setup_1', $quoteparams['setup_price']);
                        $this->db->set('print_2', $quoteparams['print_price']);
                        $this->db->set('print_3', $quoteparams['print_price']);
                        $this->db->set('print_4', $quoteparams['print_price']);
                        $this->db->set('setup_2', $quoteparams['setup_price']);
                        $this->db->set('setup_3', $quoteparams['setup_price']);
                        $this->db->set('setup_4', $quoteparams['setup_price']);
                        $this->db->set('imprint_type', $quoteparams['setuptype']);
                        $this->db->insert('ts_quote_imprindetails');
                        $numloc++;
                    }
                    $start = count($locations)+1;
                    for ($i=$start; $i<13; $i++) {
                        $this->db->set('quote_item_id', $quote_item_id);
                        $this->db->set('imprint_active', 0);
                        // $this->db->set('num_colors', 1);
                        if ($i==1) {
                            $this->db->set('print_1',0);
                        } else {
                            $this->db->set('print_1', $quoteparams['print_price']);
                        }
                        $this->db->set('print_2', $quoteparams['print_price']);
                        $this->db->set('print_3', $quoteparams['print_price']);
                        $this->db->set('print_4', $quoteparams['print_price']);
                        $this->db->set('setup_1', $quoteparams['setup_price']);
                        $this->db->set('setup_2', $quoteparams['setup_price']);
                        $this->db->set('setup_3', $quoteparams['setup_price']);
                        $this->db->set('setup_4', $quoteparams['setup_price']);
                        $this->db->insert('ts_quote_imprindetails');
                    }
                    $imprint_subtotal = 0;
                    if (count($locations)==0) {
                        $this->db->set('quote_item_id', $quote_item_id);
                        $this->db->set('imprint_description','Blank');
                        $this->db->set('imprint_qty', $quoteparams['item_qty']);
                        $this->db->insert('ts_quote_imprints');
                    } else {
                        $imprrecs = $this->db->select('*')->from('ts_quote_imprindetails')->where(['quote_item_id'=>$quote_item_id, 'imprint_active' => 1])->get()->result_array();
                        $numloc = 1;
                        $setupcnt = 0;
                        foreach ($imprrecs as $imprint) {
                            $locatname = 'Loc '.$numloc;
                            if ($imprint['num_colors']>4) {
                                $descipt = $locatname.': Full Full Color Imprinting';
                                $this->db->set('quote_item_id', $quote_item_id);
                                $this->db->set('imprint_description', $descipt);
                                $this->db->set('imprint_item', 1);
                                $this->db->set('imprint_qty', $quoteparams['item_qty']);
                                $this->db->set('imprint_price', $imprint['print_1']);
                                $this->db->insert('ts_quote_imprints');
                                $imprint_subtotal+=$quoteparams['item_qty']*$imprint['print_1'];
                                $setupcnt++;
                            } else {
                                for ($j=1; $j<5; $j++) {
                                    if ($imprint['num_colors']>=$j) {
                                        $locid = date('jS', strtotime('2019-01-0'.$j));
                                        $descipt = $locatname.': '.$locid.' Color Imprinting';
                                        $this->db->set('quote_item_id', $quote_item_id);
                                        $this->db->set('imprint_description', $descipt);
                                        $this->db->set('imprint_item', 1);
                                        $this->db->set('imprint_qty', $quoteparams['item_qty']);
                                        $this->db->set('imprint_price', $imprint['print_'.$j]);
                                        $this->db->insert('ts_quote_imprints');
                                        $imprint_subtotal+=$quoteparams['item_qty']*$imprint['print_'.$j];
                                        $setupcnt++;
                                    }
                                }
                            }
                            $numloc++;
                        }
                        // Add setup
                        if ($quoteparams['setuptype']=='NEW') {
                            $descipt = 'One Time Art Setup Charge';
                        } else {
                            $descipt = 'Repeat Setup Charge '.$quoteparams['setupnote'];
                        }
                        $this->db->set('quote_item_id', $quote_item_id);
                        $this->db->set('imprint_description', $descipt);
                        $this->db->set('imprint_item', 0);
                        $this->db->set('imprint_qty', $setupcnt);
                        if ($quoteparams['setuptype']=='NEW') {
                            $this->db->set('imprint_price', $quoteparams['setup_price']);
                            $imprint_subtotal+=$setupcnt*$quoteparams['setup_price'];
                        } else {
                            $this->db->set('imprint_price', 0);
                        }
                        $this->db->insert('ts_quote_imprints');
                    }
                    $item_subtotal+=$imprint_subtotal;
                    // Update Quote body
                    $this->db->where('quote_id', $quote_id);
                    $this->db->set('items_subtotal', $item_subtotal);
                    $this->db->set('imprint_subtotal', $imprint_subtotal);
                    $this->db->update('ts_quotes');
                    // Ship Calendar
                    $this->load->model('calendars_model');
                    $Date = date('Y-m-d');
                    $start = strtotime($Date. ' + 1 days');
                    $deliv_date = $this->calendars_model->get_business_date($start, 1, $this->config->item('custom_id'));
                    $qitems = $this->db->select('*')->from('ts_quote_items')->where('quote_id', $quote_id)->get()->result_array();
                    $quote = $this->db->select('*')->from('ts_quotes')->where('quote_id', $quote_id)->get()->row_array();
                    $this->load->model('shipping_model');
                    $shipcost = 0;
                    $shipres = $this->shipping_model->count_quoteshiprates($qitems, $quote, $deliv_date, $quote['brand']);
                    if ($shipres['result']==$this->success_result) {
                        $ships = $shipres['ships'];
                        foreach ($ships as $ship) {
                            $this->db->set('quote_id', $quote_id);
                            $this->db->set('active', $ship['current']);
                            $this->db->set('shipping_code', $ship['code']);
                            $this->db->set('shipping_name', $ship['ServiceName']);
                            $this->db->set('shipping_rate', $ship['Rate']);
                            $this->db->set('shipping_date', $ship['DeliveryDate']);
                            $this->db->insert('ts_quote_shippings');
                            if ($ship['current']==1) {
                                $shipcost = $ship['Rate'];
                            }
                        }
                    }
                    $tax = 0;
                    if ($taxcalc==1) {
//                        $tax = round(($quote['mischrg_value1']-$quote['discount_value']+$quote['rush_cost']+$quote['items_subtotal']+$quote['imprint_subtotal'])*($this->config->item('salesnewtax')/100),2);
                        $tax = round(($quote['rush_cost']+$quote['items_subtotal'])*($this->config->item('salesnewtax')/100),2);
                    }
//                    $total = $quote['mischrg_value1']-$quote['discount_value']+$quote['rush_cost']+$quote['items_subtotal']+$quote['imprint_subtotal'] + $tax + $shipcost;
                    $total = $quote['rush_cost']+$quote['items_subtotal'] + $tax + $shipcost;
                    $this->db->where('quote_id', $quote_id);
                    $this->db->set('sales_tax', $tax);
                    $this->db->set('shipping_cost', $shipcost);
                    $this->db->set('quote_total', $total);
                    $this->db->update('ts_quotes');
                }
            }
        }
    }
}