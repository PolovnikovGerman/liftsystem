<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Leadquote_model extends MY_Model
{
    private $quotetemplates = [
        'Quote',
        'Proforma',
    ];
    private $sbnumber = 11000;
    private $srnumber = 7500;
    private $box_empty_weight = 25;
    private $normal_template='Stressball';
    private $default_zip='07012';
    private $error_message='Unknown error. Try later';
    private $empty_htmlcontent='&nbsp;';
    private $tax_state = 'NJ';
    function __construct() {
        parent::__construct();
    }

    // Get list of quotes, related with lead
    public function get_leadquotes($lead_id) {
        $this->db->select('q.quote_id, q.quote_date, q.brand, q.quote_number, q.quote_total, sum(i.item_qty) as item_qty, group_concat(qc.item_description) as item_name');
        $this->db->from('ts_quotes q');
        $this->db->join('ts_quote_items i','i.quote_id=q.quote_id','left ');
        $this->db->join('ts_quote_itemcolors qc','qc.quote_item_id=i.quote_item_id','left');
        $this->db->where('q.lead_id', $lead_id);
        $this->db->group_by('q.quote_id, q.quote_date, q.brand, q.quote_number, q.quote_total');
        return $this->db->get()->result_array();

    }

    public function add_leadquote($lead_data, $usr_id, $user_name) {
        $response = ['result' => $this->error_result, 'msg' => 'USER Not Found'];
        // Get new Quote #
        $newnum = $this->get_newquote_number($lead_data['brand']);
        $this->load->model('user_model');
        $usrdat = $this->user_model->get_user_data($usr_id);
        if (ifset($usrdat, 'user_id',0) > 0) {
            $response['result'] = $this->success_result;
            $quotedat = [
                'quote_id' => 0,
                'brand' => $lead_data['brand'],
                'quote_number' => $newnum,
                'quote_date' => time(),
                'quote_template' => $this->quotetemplates[0],
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
                'taxview' => 0,
                'quote_blank' => 0,
                'lead_time' => '',
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
                'items_subtotal' => 0,
                'imprint_subtotal' => 0,
                'quote_total' => 0,
            ];
            // Items
            $quote_items = [];
            if (!empty($lead_data['lead_item_id'])) {
                $itemdat = $this->add_newleadquote_item($lead_data['lead_item_id'], $lead_data['other_item_name']);
                if ($itemdat['result']==$this->success_result) {
                    $quote_items[] = $itemdat['quote_items'];
                    // Get Delivery Terms
                    $this->load->model('calendars_model');
                    $termdat = $this->calendars_model->get_delivery_date($lead_data['lead_item_id']);
                    $quotedat['lead_time'] = json_encode($termdat);
                    foreach ($termdat as $row) {
                        if ($row['current']==1) {
                            $quotedat['rush_cost'] = $row['price'];
                            $quotedat['rush_days'] = $row['date'];
                            $quotedat['rush_terms'] = $row['name'];
                        }
                    }
                }
            }
            $response['quote'] = $quotedat;
            $response['quote_items'] = $quote_items;
        }
        return $response;
    }

    public function get_leadquote($quote_id, $edit_mode) {
        $response = ['result' => $this->error_result, 'msg' => 'Quote Mot Found'];
        $this->db->select('*');
        $this->db->from('ts_quotes');
        $this->db->where('quote_id', $quote_id);
        $quote = $this->db->get()->row_array();
        if (ifset($quote, 'quote_id', 0)==$quote_id) {
            $response['result'] = $this->success_result;
            // Quote find
            // $response['result'] = $this->success_result;
            // Get items
            $this->db->select('*');
            $this->db->from('ts_quote_items');
            $this->db->where('quote_id', $quote_id);
            $items = $this->db->get()->result_array();
            $idx = 0;
            $this->load->model('orders_model');
            $this->load->model('leadorder_model');
            foreach ($items as $item) {
                if ($item['item_id'] < 0) {
                    $itemdata=$this->orders_model->get_newitemdat($item['item_id']);
                } elseif ($item['item_id'] > 0) {
                    $itemdata=$this->leadorder_model->_get_itemdata($item['item_id']);
                } else {
                    $itemdata = [];
                }
                $items[$idx]['item_number'] = '';
                $items[$idx]['item_name'] = '';
                $items[$idx]['colors'] = [];
                $items[$idx]['num_colors'] = 0;
                $items[$idx]['imprint_locations'] = [];
                $items[$idx]['vendor_zipcode'] = '';
                $items[$idx]['charge_perorder']=0;
                $items[$idx]['charge_pereach']=0;
                if (!empty($itemdata)) {
                    $items[$idx]['item_number'] = $itemdata['item_number'];
                    $items[$idx]['item_name'] = $itemdata[ 'item_name'];
                    $items[$idx]['colors'] = $itemdata['colors'];
                    $items[$idx]['num_colors'] = count($itemdata['colors']);
                    $items[$idx]['imprint_locations']=ifset($itemdata, 'imprints',[]);
                    $items[$idx]['vendor_zipcode']=ifset($itemdata, 'vendor_zipcode', $this->config->item('zip'));
                    $items[$idx]['charge_perorder']=ifset($itemdata, 'charge_perorder',0);
                    $items[$idx]['charge_pereach']=ifset($itemdata, 'charge_pereach', 0);
                }
                // Get colors
                $this->db->select('quote_itemcolor_id as item_id, quote_item_id, item_description, item_color, item_qty, item_price');
                $this->db->select('(item_qty * item_price) as item_subtotal');
                $this->db->from('ts_quote_itemcolors');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $colors = $this->db->get()->result_array();
                $colorcnt = count($colors);
                $coloridx = 0;
                $colornum = 1;
                foreach ($colors as $color) {
                    $colors[$coloridx]['item_number'] = $items[$idx]['item_number'];
                    $colors[$coloridx]['item_row'] = $colornum;
                    $colors[$coloridx]['colors'] = $itemdata['colors'];
                    $colors[$coloridx]['num_colors'] = count($itemdata['colors']);
                    $colors[$coloridx]['out_colors'] = '';
                    if ($colors[$coloridx]['num_colors'] > 0) {
                        $options=array(
                            'quote_item_id'=>$color['quote_item_id'],
                            'item_id'=>$color['item_id'],
                            'colors'=>$itemdata['colors'],
                            'item_color'=>$color['item_color'],
                        );
                        $colors[$coloridx]['out_colors']=$this->load->view('leadpopup/quoteitem_color_choice', $options, TRUE);
                    }
                    $colors[$coloridx]['printshop_item_id'] = ifset($itemdata, 'printshop_item_id', 0);
                    $colors[$coloridx]['qtyinput_class'] = 'normal';
                    $colors[$coloridx]['qtyinput_title'] = '';
                    $colors[$coloridx]['item_color_add'] = 0;
                    if ($colors[$coloridx]['num_colors'] > 1 && $colornum==$colorcnt) {
                        $colors[$coloridx]['item_color_add'] = 1;
                    }
                    $coloridx++;
                    $colornum++;
                }
                $items[$idx]['items'] = $colors;
                // Inprints
                $this->db->select('quote_imprint_id, quote_item_id, imprint_description, imprint_item, imprint_qty, imprint_price');
                $this->db->select('\'nornal\' as imprint_price_class, \'\' as imprint_price_title, 0 as delflag');
                $this->db->select('(imprint_qty * imprint_price) as imprint_subtotal');
                $this->db->from('ts_quote_imprints');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $imprints = $this->db->get()->result_array();
                if (count($imprints)==0 && $edit_mode==1) {
                    $imprints = [];
                    $imprints[] = [
                        'quote_imprint_id' => -1,
                        'imprint_description' => $this->empty_htmlcontent,
                        'imprint_qty' => 0,
                        'imprint_price' => 0,
                        'imprint_item' => 0,
                        'imprint_subtotal' => $this->empty_htmlcontent,
                        'imprint_price_class' => 'normal',
                        'imprint_price_title' => '',
                        'delflag' => 0,
                    ];
                }
                $items[$idx]['imprints'] = $imprints;
                // Imprint details
                $this->db->select('*');
                $this->db->from('ts_quote_imprindetails');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $imprintdetdats = $this->db->get()->result_array();
                if (count($imprintdetdats)==0) {
                    $imprintdetails = [];
                    $detailfld=$this->db->list_fields('ts_quote_imprindetails');
                    for ($i=1; $i<13; $i++) {
                        $newloc=array(
                            'title'=>'Loc '.$i,
                            'active'=>0,
                        );
                        foreach ($detailfld as $row) {
                            switch ($row) {
                                case 'quote_imprindetail_id':
                                    $newloc[$row]=$i*(-1);
                                    break;
                                case 'quote_item_id':
                                    $newloc[$row] = $item['quote_item_id'];
                                    break;
                                case 'imprint_type':
                                    $newloc[$row]='NEW';
                                    break;
                                case 'num_colors':
                                    $newloc[$row]=1;
                                    break;
                                default :
                                    $newloc[$row]='';
                            }
                        }
                        if ($i==1) {
                            $newloc['print_1']=0;
                        } else {
                            $newloc['print_1']=$item['imprint_price'];
                        }
                        $newloc['print_2']=$item['imprint_price'];
                        $newloc['print_3']=$item['imprint_price'];
                        $newloc['print_4']=$item['imprint_price'];
                        $newloc['setup_1']=$item['setup_price'];
                        $newloc['setup_2']=$item['setup_price'];
                        $newloc['setup_3']=$item['setup_price'];
                        $newloc['setup_4']=$item['setup_price'];
                        $imprintdetails[]=$newloc;
                    }
                } else {
                    $imprintdetails = [];
                    $numpp = 1;
                    foreach ($imprintdetdats as $row) {
                        $imprintdetails[] = [
                            'title' => 'Loc '.$numpp,
                            'quote_imprindetail_id' => $row['quote_imprindetail_id'],
                            'quote_item_id' => $row['quote_item_id'],
                            'active' => $row['imprint_active'],
                            'imprint_active' => $row['imprint_active'],
                            'imprint_type' => $row['imprint_type'],
                            'repeat_note' => $row['repeat_note'],
                            'location_id' => $row['location_id'],
                            'num_colors' => $row['num_colors'],
                            'print_1' => $row['print_1'],
                            'print_2' => $row['print_2'],
                            'print_3' => $row['print_3'],
                            'print_4' => $row['print_4'],
                            'setup_1' => $row['setup_1'],
                            'setup_2' => $row['setup_2'],
                            'setup_3' => $row['setup_3'],
                            'setup_4' => $row['setup_4'],
                            'extra_cost' => $row['extra_cost'],
                        ];
                        $numpp++;
                    }
                }
                $items[$idx]['imprint_details'] = $imprintdetails;
                $idx++;
            }
            // Ship rates
            $this->db->select('*');
            $this->db->from('ts_quote_shippings');
            $this->db->where('quote_id', $quote_id);
            $shippings = $this->db->get()->result_array();

            $response['quote'] = $quote;
            $response['items'] = $items;
            $response['shippings'] = $shippings;
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

    public function add_newleadquote_item($item_id, $custom_name, $startid=1) {
        $out=array('result'=>$this->error_result, 'msg'=>$this->error_message);
        $this->load->model('orders_model');
        $this->load->model('leadorder_model');
        if ($item_id<0) {
            $itemdata=$this->orders_model->get_newitemdat($item_id);
        } else {
            $itemdata=$this->leadorder_model->_get_itemdata($item_id);
        }
        $colors=$itemdata['colors'];
        $itmcolor='';
        if ($itemdata['num_colors']>0) {
            $itmcolor=$colors[0];
        }
        $newid = (-1)*$startid;
        if ($item_id<0) {
            $item_description=$custom_name;
        } else {
            $item_description=$itemdata['item_name'];
        }
        $defqty=$this->config->item('defqty_common');
        if ($item_id==$this->config->item('custom_id')) {
            $defqty=$this->config->item('defqty_custom');
        }
        // Prepare Parts of Quote Items
        $quoteitem=[
            'quote_item_id'=>$newid,
            'item_id'=>$item_id,
            'item_number'=>$itemdata['item_number'],
            'item_name'=>$item_description,
            'item_qty'=>$defqty,
            'colors'=>$itemdata['colors'],
            'num_colors'=>$itemdata['num_colors'],
            'template'=> ifset($itemdata, 'item_template', $this->normal_template),
            'item_weigth'=>0,
            'cartoon_qty'=>0,
            'cartoon_width'=>0,
            'cartoon_heigh'=>0,
            'cartoon_depth'=>0,
            'boxqty'=>'',
            'item_price' => 0,
            'setup_price'=>0,
            'imprint_price'=>0,
            'base_price' => 0,
            'imprint_locations'=>[],
            'item_subtotal'=>0,
            'imprint_subtotal'=>0,
            'vendor_zipcode'=>$this->default_zip,
            'charge_perorder'=>0,
            'charge_peritem'=>0,
        ];
        $newprice=0;
        if ($item_id>0) {
            // Prices, totals
            $newprice=$this->leadorder_model->_get_item_priceqty($item_id, $quoteitem['template'] , $defqty);
            $setupprice=$this->leadorder_model->_get_item_priceimprint($item_id, 'setup');
            $printprice=$this->leadorder_model->_get_item_priceimprint($item_id, 'imprint');
            $quoteitem['item_weigth']=$itemdata['item_weigth'];
            $quoteitem['cartoon_qty']=$itemdata['cartoon_qty'];
            $quoteitem['cartoon_width']=$itemdata['cartoon_width'];
            $quoteitem['cartoon_heigh']=$itemdata['cartoon_heigh'];
            $quoteitem['cartoon_depth']=$itemdata['cartoon_depth'];
            $quoteitem['boxqty']=$itemdata['boxqty'];
            $quoteitem['setup_price']=$setupprice;
            $quoteitem['imprint_price']=$printprice;
            $quoteitem['base_price']=$newprice;
            $quoteitem['item_price']=$newprice;
            $quoteitem['imprint_locations']=$itemdata['imprints'];
            $quoteitem['vendor_zipcode']=$itemdata['vendor_zipcode'];
            $quoteitem['charge_perorder']=$itemdata['charge_perorder'];
            $quoteitem['charge_pereach']=$itemdata['charge_pereach'];
            $quoteitem['item_subtotal']=$defqty*$newprice;
        }

       //
        // Prepare firt item (as itemcolors)
        $newitem=array(
            'quote_item_id'=>$newid,
            'item_id'=>-1,
            'item_row'=>1,
            'item_number'=>$itemdata['item_number'],
            'item_color'=>$itmcolor,
            'colors'=>$colors,
            'num_colors'=>$itemdata['num_colors'],
            'item_description'=>$quoteitem['item_name']
        );
        //
        if ($itemdata['num_colors']==0) {
            $newitem['out_colors']=$this->empty_htmlcontent;
        } else {
            $options=array(
                'quote_item_id'=>$newitem['quote_item_id'],
                'item_id'=>$newitem['item_id'],
                'colors'=>$newitem['colors'],
                'item_color'=>$newitem['item_color'],
            );
            $newitem['out_colors']=$this->load->view('leadpopup/quoteitem_color_choice', $options, TRUE);
        }
        if ($newitem['num_colors']>1) {
            $newitem['item_color_add']=1;
        } else {
            $newitem['item_color_add']=0;
        }

        $newitem['item_qty']=$defqty;
        $newitem['item_price']=$newprice;
        $newitem['item_subtotal']=$defqty*$newprice;
        $newitem['printshop_item_id']=(isset($itemdata['printshop_item_id']) ? $itemdata['printshop_item_id']  : '');
        $newitem['qtyinput_class']='normal';
        $newitem['qtyinput_title']='';
        $items[]=$newitem;

        $quoteitem['items']=$items;
        // Prepare Imprint, Imprint Details
        $imprint[]=array(
            'quote_imprint_id'=>-1,
            'imprint_description'=>'&nbsp;',
            'imprint_qty'=>0,
            'imprint_price'=>0,
            'imprint_item'=>0,
            'imprint_subtotal'=>'&nbsp;',
            'imprint_price_class' => 'normal',
            'imprint_price_title' => '',
            'delflag'=>0,
        );
        $quoteitem['imprints']=$imprint;
        // Change Imprint Details
        $imprdetails=[];
        $detailfld=$this->db->list_fields('ts_quote_imprindetails');
        for ($i=1; $i<13; $i++) {
            $newloc=array(
                'title'=>'Loc '.$i,
                'active'=>0,
            );
            foreach ($detailfld as $row) {
                switch ($row) {
                    case 'quote_imprindetail_id':
                        $newloc[$row]=$i*(-1);
                        break;
                    case 'imprint_type':
                        $newloc[$row]='NEW';
                        break;
                    case 'num_colors':
                        $newloc[$row]=1;
                        break;
                    default :
                        $newloc[$row]='';
                }
            }
            if ($i==1) {
                $newloc['print_1']=0;
            } else {
                $newloc['print_1']=$quoteitem['imprint_price'];
            }
            $newloc['print_2']=$quoteitem['imprint_price'];
            $newloc['print_3']=$quoteitem['imprint_price'];
            $newloc['print_4']=$quoteitem['imprint_price'];
            $newloc['setup_1']=$quoteitem['setup_price'];
            $newloc['setup_2']=$quoteitem['setup_price'];
            $newloc['setup_3']=$quoteitem['setup_price'];
            $newloc['setup_4']=$quoteitem['setup_price'];
            $imprdetails[]=$newloc;
        }
        $quoteitem['imprint_details']=$imprdetails;
        // Add new element to Order Items
        $out['result']=$this->success_result;
        $out['quote_items']=$quoteitem;
        return $out;
    }

    public function quoteparamchange($data, $quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Empty Need Parameters', 'totalcalc' => 0];
        $fldname = ifset($data, 'fld','');
        if (!empty($fldname)) {
            $quote = $quotesession['quote'];
            $quote[$fldname] = $data['newval'];
            $quotesession['quote'] = $quote;
            $out['result'] = $this->success_result;
            usersession($session_id, $quotesession);
            if ($fldname=='mischrg_value1' || $fldname=='mischrg_value2' || $fldname=='discount_value' || $fldname=='shipping_cost' || $fldname=='rush_cost' || $fldname=='sales_tax') {
                $out['totalcalc'] = 1;
            }
        }
        return $out;
    }

    public function quoteaddresschange($data, $quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Empty Need Parameters',
            'shiprebuild' => 0, 'shipstate' => 0,
            'billstate' => 0, 'billrebuild' => 0,
            'totalcalc' => 0, 'calcship' => 0, 'taxview'=>0];
        $fldname = ifset($data, 'fld','');
        if (!empty($fldname)) {
            $quote = $quotesession['quote'];
            $quote[$fldname] = $data['newval'];
            if ($fldname=='shipping_country') {
                $out['shiprebuild'] = 1;
                $out['shipstate'] = 1;
                $quote['shipping_zip'] = '';
                $quote['shipping_city'] = '';
                $quote['shipping_state'] = '';
                $out['totalcalc'] = 1;
            } elseif ($fldname=='billing_country') {
                $out['billrebuild'] = 1;
                $out['billstate'] = 1;
                $quote['billing_zip'] = '';
                $quote['billing_city'] = '';
                $quote['billing_state'] = '';
            } elseif ($fldname=='shipping_zip') {
                $out['shiprebuild'] = 1;
                $out['totalcalc'] = 1;
                $out['calcship'] = 1;
                // Find city and zip
                if (!empty($data['newval'])) {
                    $this->load->model('shipping_model');
                    $zipdat = $this->shipping_model->get_zip_address($quote['shipping_country'], $data['newval']);
                    if ($zipdat['result']==$this->success_result) {
                        $quote['shipping_city'] = $zipdat['city'];
                        $quote['shipping_state'] = $zipdat['state'];
                        if ($zipdat['state']==$this->tax_state && $quote['shipping_country']==$this->config->item('default_country')) {
                            if ($quote['taxview']==0) {
                                $quote['taxview'] = 1;
                                $out['taxview'] = 1;
                            }
                        } else {
                            if ($quote['taxview']==1) {
                                $quote['taxview'] = 0;
                                $quote['tax_exempt'] = 0;
                                $quote['tax_reason'] = '';
                                $out['taxview'] = 1;
                            }
                        }
                    }
                } else {
                    $quote['shipping_city'] = '';
                    $quote['shipping_state'] = '';
                }
            } elseif ($fldname=='billing_zip') {
                // Find city and zip
                $out['billrebuild'] = 1;
                if (!empty($data['newval'])) {
                    $this->load->model('shipping_model');
                    $zipdat = $this->shipping_model->get_zip_address($quote['billing_country'], $data['newval']);
                    if ($zipdat['result']==$this->success_result) {
                        $quote['billing_city'] = $zipdat['city'];
                        $quote['billing_state'] = $zipdat['state'];
                    }
                } else {
                    $quote['billing_city'] = '';
                    $quote['billing_state'] = '';
                }
            } elseif ($fldname=='shipping_state') {
                if ($data['newval']==$this->tax_state && $quote['shipping_country']==$this->config->item('default_country')) {
                    if ($quote['taxview']==0) {
                        $quote['taxview'] = 1;
                        $out['taxview'] = 1;
                    }
                } else {
                    if ($quote['taxview']==1) {
                        $quote['taxview'] = 0;
                        $quote['tax_exempt'] = 0;
                        $quote['tax_reason'] = '';
                        $out['taxview'] = 1;
                    }
                }
            }
            $quotesession['quote'] = $quote;
            $out['result'] = $this->success_result;
            usersession($session_id, $quotesession);
        }
        return $out;
    }

    public function quoteratechange($postdata, $quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Shipping Method not found' ];
        $shipmeth = ifset($postdata, 'newval','unkn');
        $shippings = $quotesession['shipping'];
        $found = 0;
        $shipidx = 0;
        foreach ($shippings as $shipping) {
            if ($shipping['shipping_code']==$shipmeth) {
                $found = 1;
                break;
            } else {
                $shipidx++;
            }
        }
        if ($found==1) {
            $out['result'] = $this->success_result;
            $i=0;
            foreach ($shippings as $shipping) {
                $shippings[$i]['active'] = 0;
                $i++;
            }
            $shippings[$shipidx]['active'] = 1;
            $quote = $quotesession['quote'];
            $quote['shipping_cost'] = $shippings[$shipidx]['shipping_rate'];
            $quotesession['quote'] = $quote;
            $quotesession['shipping'] = $shippings;
            usersession($session_id, $quotesession);
        }
        return $out;
    }

    public function quotetaxextemp($quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Field not found' ];
        $quote = $quotesession['quote'];
        if ($quote['tax_exempt']==1) {
            $quote['tax_exempt'] = 0;
            $quote['tax_reason'] = '';
        } else {
            $quote['tax_exempt'] = 1;
        }
        $quotesession['quote'] = $quote;
        usersession($session_id, $quotesession);
        $out['result'] = $this->success_result;
        return $out;
    }

    public function quoteleadtimechange($data, $quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Empty Need Parameters'];
        $newval = ifset($data, 'newval', '');
        if (!empty($newval)) {
            $out['msg'] = 'Lead time not found';
            $quote = $quotesession['quote'];
            $times = json_decode($quote['lead_time'], true);
            $find = 0;
            $timeidx = 0;
            foreach ($times as $timed) {
                if ($timed['id']==$newval) {
                    $find=1;
                    break;
                } else {
                    $timeidx++;
                }
            }
            if ($find==1) {
                $i=0;
                $out['result'] = $this->success_result;
                foreach ($times as $timed) {
                    if ($timed['id']==$newval) {
                        $times[$i]['current'] = 1;
                    } else {
                        $times[$i]['current'] = 0;
                    }
                    $i++;
                }
                $quote['rush_days'] = $times[$timeidx]['date'];
                $quote['rush_cost'] = $times[$timeidx]['price'];
                $quote['rush_terms'] = $times[$timeidx]['name'];
                $quote['lead_time'] = json_encode($times);
                $quotesession['quote'] = $quote;
                usersession($session_id, $quotesession);
            }
        }
        return $out;
    }

    public function quoteitemchange($data, $quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Empty Need Parameters','shipcalc' => 0, 'totalcalc' => 0, 'item_refresh' => 0];
        $fldname = ifset($data,'fld','');
        $itemid = ifset($data, 'item','');
        $itemcolor = ifset($data,'itemcolor','');
        if (!empty($fldname) && !empty($itemid) && !empty($itemcolor)) {
            $out['msg'] = 'Item Not Found';
            $items = $quotesession['items'];
            if ($fldname=='item_qty' || $fldname=='item_price') {
                $out['totalcalc'] = 1;
            }
            if ($fldname=='item_qty') {
                $out['shipcalc'] = 1;
            }
            $find = 0;
            $itemidx = $itemcoloridx = 0;
            foreach ($items as $item) {
                if ($item['quote_item_id']==$itemid) {
                    $colors = $item['items'];
                    foreach ($colors as $color) {
                        if ($color['item_id']==$itemcolor) {
                            $find=1;
                        }
                        if ($find==1) {
                            break;
                        }
                        $itemcoloridx++;
                    }
                }
                if ($find==1) {
                    break;
                }
                $itemidx++;
            }
            if ($find==1) {
                if ($fldname=='item_qty') {
                    $items[$itemidx]['items'][$itemcoloridx][$fldname] = $data['newval'];
                    $subtotal = $items[$itemidx]['items'][$itemcoloridx]['item_qty'] * $items[$itemidx]['items'][$itemcoloridx]['item_price'];
                    $out['item_subtotal'] = $subtotal;
                    $itemsqty=0;
                    foreach ($items[$itemidx]['items'] as $irow) {
                        $itemsqty+=$irow['item_qty'];
                    }
                    $items[$itemidx]['item_qty']=$itemsqty;
                    // Get New price
                    $this->load->model('leadorder_model');
                    $newprice=$this->leadorder_model->_get_item_priceqty($items[$itemidx]['item_id'], $items[$itemidx]['template'] , $items[$itemidx]['item_qty']);
                    $items[$itemidx]['base_price']=$newprice;
                    $out['price_class']='normal';
                    $ridx=0;
                    foreach ($items[$itemidx]['items'] as $row) {
                        $items[$itemidx]['items'][$ridx]['item_price']=$newprice;
                        $items[$itemidx]['items'][$ridx]['qtyinput_class']='normal';
                        $rowtotal = $items[$itemidx]['items'][$ridx]['item_qty']*$items[$itemidx]['items'][$ridx]['item_price'];
                        $items[$itemidx]['items'][$ridx]['item_subtotal']=MoneyOutput($rowtotal);
                        $ridx++;
                    }
                    // Change imprints
                    $imprints = $items[$itemidx]['imprints'];
                    $impridx = 0;
                    foreach ($imprints as $imprint) {
                        if ($imprint['imprint_item']==1) {
                            $imprints[$impridx]['imprint_qty'] = $itemsqty;
                            $imprints[$impridx]['imprint_subtotal'] = $itemsqty * $imprint['imprint_price'];
                        }
                        $impridx++;
                    }
                    $items[$itemidx]['imprints'] = $imprints;
                    $out['item_refresh'] = 1;
                } else {
                    $items[$itemidx]['items'][$itemcoloridx][$fldname] = $data['newval'];
                    $subtotal = $items[$itemidx]['items'][$itemcoloridx]['item_qty'] * $items[$itemidx]['items'][$itemcoloridx]['item_price'];
                    $items[$itemidx]['items'][$itemcoloridx]['item_subtotal'] = MoneyOutput($subtotal);
                    $out['item_subtotal'] = $subtotal;
                }
                $quotesession['items'] = $items;
                usersession($session_id, $quotesession);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function additemcolor($session, $postdata,  $session_id) {
        $out = ['result' => $this->error_result, 'msg' => $this->error_message];
        $quote_item_id = ifset($postdata,'item',0);
        if ($quote_item_id!==0) {
            $itemidx = 0;
            $items = $session['items'];
            foreach ($items as $item) {
                if ($item['quote_item_id']==$quote_item_id) {
                    $itemcolors = $item['items'];
                    $coloridx = 0;
                    // Made Add color = 0;
                    foreach ($itemcolors as $itemcolor) {
                        $itemcolors[$coloridx]['item_color_add'] = 0;
                        $coloridx++;
                    }
                    unset($itemcolor);
                    // Add new row
                    $newid=count($itemcolors)+1;
                    $colors=$item['colors'];
                    $itemcolor = '';
                    if (count($colors) > 0) {
                        $itemcolor=$colors[0];
                    }
                    $newitemcolor=[
                        'quote_item_id' => $quote_item_id,
                        'item_id' => $newid*(-1),
                        'item_row' => $newid,
                        'item_number' => $item['item_number'],
                        'item_color' => $itemcolor,
                        'colors' => $item['colors'],
                        'num_colors' => count($colors),
                        'item_description' => $item['item_name'],
                        'item_color_add' => 1,
                        'item_qty' => 0,
                        'item_price' => $itemcolors[0]['item_price'],
                        'item_subtotal' => '',
                        'printshop_item_id' => '',
                        'qtyinput_class' => 'normal',
                        'qtyinput_title' => '',
                    ];
                    $options=array(
                        'quote_item_id'=>$newitemcolor['quote_item_id'],
                        'item_id'=>$newitemcolor['item_id'],
                        'colors'=>$newitemcolor['colors'],
                        'item_color'=>$newitemcolor['item_color'],
                    );
                    $newitemcolor['out_colors']=$this->load->view('leadpopup/quoteitem_color_choice', $options, TRUE);
                    $itemcolors[] = $newitemcolor;
                    $items[$itemidx]['items'] = $itemcolors;
                    $session['items'] = $items;
                    usersession($session_id, $session);
                    $out['result'] = $this->success_result;
                    $out['item'] = $items[$itemidx];
                    break;
                }
                $itemidx++;
            }
        }
        return $out;
    }

    public function prepare_print_details($session, $postdata, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => $this->error_message];
        $quote_item_id = ifset($postdata, 'item', 0);
        if ($quote_item_id !== 0) {
            $out['msg'] = 'Quote Item Not found';
            $quote = $session['quote'];
            $items = $session['items'];
            foreach ($items as $item) {
                if ($item['quote_item_id']==$quote_item_id) {
                    // Find
                    $out['result']=$this->success_result;
                    $out['quote'] = $quote;
                    $out['imprint_details']=$item['imprint_details'];
                    $out['item_id']=$item['item_id'];
                    $out['item_number']=$item['item_number'];
                    // $out['order_blank']=$artwork['artwork_blank'];
                    $out['imprints']=$item['imprint_locations'];
                    $out['item_name']=$item['item_name'];
                    usersession($session_id, $session);
                }
            }
        }
        return $out;
    }

    public function change_imprint_details($imprintdetails, $postdata, $imprintsession_id) {
        $out=['result' => $this->error_result, 'msg' => 'Enter all Parameters for change'];
        $fldname=ifset($postdata, 'fldname','');
        $quote_imprindetail_id=ifset($postdata, 'details',0);
        if (!empty($fldname) && !empty($quote_imprindetail_id)) {
            $newval=$postdata['newval'];
            $details=$imprintdetails['imprint_details'];
            $found=0;
            $detidx=0;
            foreach ($details as $detail) {
                if ($detail['quote_imprindetail_id']==$quote_imprindetail_id) {
                    $found = 1;
                    break;
                } else {
                    $detidx++;
                }
            }
            if ($found==1) {
                // Detail found
                $details[$detidx][$fldname]=$newval;
                if ($fldname=='active' && $newval==1) {
                    $imprintdetails['quote_blank']=0;
                }
                if ($fldname=='imprint_type') {
                    if ($newval=='REPEAT') {
                        $details[$detidx]['setup_1']=0;
                        $details[$detidx]['setup_2']=0;
                        $details[$detidx]['setup_3']=0;
                        $details[$detidx]['setup_4']=0;
                        $out['class']='';
                        if (!empty($details[$detidx]['repeat_note'])) {
                            $out['class']='full';
                        }
                    } else {
                        $this->load->model('leadorder_model');
                        $setupprice=$this->leadorder_model->_get_item_priceimprint($imprintdetails['item_id'], 'setup');
                        $out['setup']=$setupprice;
                        $details[$detidx]['setup_1']=$setupprice;
                        $details[$detidx]['setup_2']=$setupprice;
                        $details[$detidx]['setup_3']=$setupprice;
                        $details[$detidx]['setup_4']=$setupprice;
                    }
                }
                $imprintdetails['imprint_details']=$details;
                usersession($imprintsession_id, $imprintdetails);
                $out['fldname'] = $fldname;
                $out['details'] = $quote_imprindetail_id;
                $out['newval'] = $newval;
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function get_repeat_note($imprintdetails, $detail_id, $imprintsession_id) {
        $out=['result'=>$this->error_result, 'msg'=> 'Imprint Location Not Found'];
        $details=$imprintdetails['imprint_details'];
        $found=0;
        $detidx=0;
        foreach ($details as $row) {
            if ($row['quote_imprindetail_id']==$detail_id) {
                $found=1;
                break;
            } else {
                $detidx++;
            }
        }
        if ($found==1) {
            $out['repeat_note']=$details[$detidx]['repeat_note'];
            usersession($imprintsession_id, $imprintdetails);
            $out['result']=$this->success_result;
        }
        return $out;
    }

    public function save_repeat_note($imprintdetails, $postdata, $imprintsession_id) {
        $out=array('result'=>$this->error_result, 'msg'=>'Imprint Location Not Found');
        $detail_id = ifset($postdata,'detail_id',0);
        $repeat_note = ifset($postdata,'repeat_note','');
        if (!empty($detail_id) && !empty($repeat_note)) {
            $details=$imprintdetails['imprint_details'];
            $found=0;
            $detidx=0;
            foreach ($details as $row) {
                if ($row['quote_imprindetail_id']==$detail_id) {
                    $found=1;
                    break;
                } else {
                    $detidx++;
                }
            }
            if ($found==1) {
                $details[$detidx]['repeat_note']=$repeat_note;
                $imprintdetails['imprint_details']=$details;
                usersession($imprintsession_id, $imprintdetails);
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function save_imprint_details($details, $imprintsession_id, $quotedat, $quotesession_id) {
        $out=array('result' => $this->error_result, 'msg' => 'Imprint Location Not Found');
        $quote = $quotedat['quote'];
        $items = $quotedat['items'];
        $deleted = $quotedat['deleted'];
        $quote_item_id=$details['quote_item_id'];
        $imprint_details=$details['imprint_details'];
        $quote_blank=intval($details['quote_blank']);
        $found=0;
        $idx=0;
        foreach ($items as $item) {
            if ($item['quote_item_id']==$quote_item_id) {
                $found = 1;
                break;
            } else {
                $idx++;
            }
        }
        if ($found==1) {
            // Check Details
            foreach ($imprint_details as $row) {
                if ($row['active']==1 && $row['imprint_type']=='REPEAT' && empty($row['repeat_note'])) {
                    $out['msg']=$row['title'].' Empty Repeat Note';
                    return $out;
                }
            }
            // New imprint details
            $items[$idx]['imprint_details'] = $imprint_details;
            // Create imprints
            if ($quote_blank == 1) {
                // Order Blank
                $imprints[]=array(
                    'quote_imprint_id'=>-1,
                    'imprint_description'=>'blank, no imprinting',
                    'imprint_item'=>0,
                    'imprint_qty'=>0,
                    'imprint_price'=>0,
                    'outqty'=>$this->empty_htmlcontent,
                    'outprice'=>$this->empty_htmlcontent,
                    'imprint_subtotal'=>$this->empty_htmlcontent,
                    'imprint_price_class' => 'normal',
                    'imprint_price_title' => '',
                    'delflag'=>0,
                );
                $imprint_total=0;
            } else {
                $imprints = [];
                $newidx = 1;
                $setup_qty = 0;
                $setup_total = 0;
                $imprint_total = 0;
                $extra=array();
                $numpp = 1;
                foreach ($imprint_details as $row) {
                    if ($row['active']==1) {
                        // Prepare New Imprints
                        $title=$row['title'];
                        for ($i=1; $i<=$row['num_colors']; $i++) {
                            $imprint_price_class = 'normal';
                            $imprint_price_title = '';
                            $imprtitle = $title . ': ' . date('jS', strtotime('2015-01-' . $i)) . ' Color Imprinting';
                            $priceindx = 'print_' . $i;
                            $setupindx = 'setup_' . $i;
                            $subtotal = $items[$idx]['item_qty'] * floatval($row[$priceindx]);
                            if ($row['imprint_type'] !== 'REPEAT' && $numpp > 1) {
                                if ($items[$idx]['item_id'] > 0) {
                                    $newiprint_price = $items[$idx]['imprint_price'];
                                    if (round(floatval($newiprint_price), 2) != round(floatval($row[$priceindx]), 2)) {
                                        $imprint_price_class = 'warningprice';
                                        $imprint_price_title = 'Print price ' . MoneyOutput($items[$idx]['imprint_price']);
                                    }
                                }
                            }
                            $numpp++;
                            $imprint_total += $subtotal;
                            if ($row['imprint_type'] != 'REPEAT') {
                                $setup_qty += 1;
                                $setup_total += floatval($row[$setupindx]);
                                $imprint_total += floatval($row[$setupindx]);
                            }

                            $imprints[] = array(
                                'quote_imprint_id' => (-1) * $newidx,
                                'imprint_description' => $imprtitle,
                                'imprint_item' => 1,
                                'imprint_qty' => $items[$idx]['item_qty'],
                                'imprint_price' => floatval($row[$priceindx]),
                                'outqty' => ($items[$idx]['item_qty'] == 0 ? '---' : $items[$idx]['item_qty']),
                                'outprice' => MoneyOutput(floatval($row[$priceindx])),
                                // 'imprint_subtotal' => MoneyOutput($subtotal),
                                'imprint_subtotal' => $subtotal,
                                'imprint_price_class' => $imprint_price_class,
                                'imprint_price_title' => $imprint_price_title,
                                'delflag' => 0,
                            );
                            $newidx++;
                        }
                        if ($row['imprint_type']=='REPEAT') {
                            $extracost=floatval($row['extra_cost']);
                            $imprint_total+=$extracost;
                            // Add Imprint
                            $title='Repeat Setup Charge '.$row['repeat_note'];
                            $extra[]=array(
                                'quote_imprint_id'=>(-1)*$newidx,
                                'imprint_description'=>$title,
                                'imprint_item'=>0,
                                'imprint_qty'=>1,
                                'imprint_price'=>floatval($row['extra_cost']),
                                'outqty'=>1,
                                'outprice'=>MoneyOutput($extracost),
                                // 'imprint_subtotal'=>MoneyOutput($extracost),
                                'imprint_subtotal'=> $extracost,
                                'imprint_price_class' => 'normal',
                                'imprint_price_title' => '',
                                'delflag'=>0,
                            );
                            $newidx++;
                        }
                    }
                }
                if (count($extra)>0) {
                    foreach ($extra as $erow) {
                        $imprints[]=$erow;
                    }
                }
                if ($setup_total>=0 && $setup_qty>0) {
                    $setup_price=0;
                    if ($setup_qty>0) {
                        $setup_price=round($setup_total/$setup_qty,2);
                    }
                    $imprint_price_class='normal';
                    $imprint_price_title='';
                    if ($items[$idx]['item_id']>0) {
                        $newsetup_price = $items[$idx]['setup_price'];
                        if (round(floatval($newsetup_price),2)!=round(floatval($setup_price),2)) {
                            $imprint_price_class='warningprice';
                            $imprint_price_title='Setup price '.MoneyOutput($items[$idx]['setup_price']);
                        }
                    }
                    $imprints[]=array(
                        'quote_imprint_id'=>(-1)*$newidx,
                        'imprint_description'=>'One Time Art Setup Charge',
                        'imprint_item'=>0,
                        'imprint_qty'=>$setup_qty,
                        'imprint_price'=>floatval($setup_price),
                        'outqty'=>$setup_qty,
                        'outprice'=>MoneyOutput($setup_price),
                        // 'imprint_subtotal'=>  MoneyOutput($setup_total),
                        'imprint_subtotal'=>  $setup_total,
                        'imprint_price_class' => $imprint_price_class,
                        'imprint_price_title' => $imprint_price_title,
                        'delflag'=>0,
                    );
                    $newidx++;
                }
            }
            // Delete old Imprints
            foreach ($items[$idx]['imprints'] as $row) {
                if ($row['quote_imprint_id'] > 0) {
                    $deleted[] = ['id' => $row['quote_imprint_id'], 'entity' => 'imprints'];
                }
            }
            // New imprints
            $items[$idx]['imprints'] = $imprints;
            // $items[$idx]['imprint_details']=$imprint_details;
            $items[$idx]['imprint_subtotal']=$imprint_total;
            $quote['quote_blank']=$quote_blank;
            $out['quote_blank']=$quote_blank;
            $quotedat['items']=$items;
            $quotedat['quote']=$quote;
            $quotedat['deleted'] = $deleted;
            usersession($quotesession_id, $quotedat);
            usersession($imprintsession_id, NULL);
            $out['result']=$this->success_result;
            $out['item']=$items[$idx];
        }
        return $out;
    }

    public function calc_quote_shipping($session_id) {
        $quotesession = usersession($session_id);
        $quote = $quotesession['quote'];
        $items = $quotesession['items'];
        $shippings = $quotesession['shipping'];
        $deleted = $quotesession['deleted'];
        $newshipcost = 0;
        if (empty($quote['shipping_zip'])) {
            // Delete old shipping
            foreach ($shippings as $shipping) {
                if ($shipping['quote_shipping_id'] > 0) {
                    $deleted[] = ['entity' => 'shipping', 'id' => $shipping['quote_shipping_id']];
                }
            }
            $quotesession['deleted'] = $deleted;
            $quotesession['shipping'] = [];
        } else {
            $this->load->model('shipping_model');
            $res = $this->shipping_model->count_quoteshiprates($items, $quote, $quote['rush_days'], $quote['brand']);
            if ($res['result']==$this->success_result) {
                // Delete old shippings
                foreach ($shippings as $shipping) {
                    if ($shipping['quote_shipping_id'] > 0) {
                        $deleted[] = ['entity' => 'shipping', 'id' => $shipping['quote_shipping_id']];
                    }
                }
                $newrates = [];
                $numpp = 1;
                foreach ($res['ships'] as $rate) {
                    $newrates[] = [
                        'quote_shipping_id' => (-1)*$numpp,
                        'active' => $rate['current'],
                        'shipping_code' => $rate['code'],
                        'shipping_name' => $rate['ServiceName'],
                        'shipping_rate' => $rate['Rate'],
                        'shipping_date' => $rate['DeliveryDate'],
                    ];
                    if ($rate['current']==1) {
                        $newshipcost = $rate['Rate'];
                    }
                }
                $quotesession['shipping'] = $newrates;
                $quotesession['deleted'] = $deleted;
            }
        }
        $quote['shipping_cost'] = $newshipcost;
        $quotesession['quote'] = $quote;
        usersession($session_id, $quotesession);
    }

    public function calc_quote_totals($session_id) {
        $quotesession = usersession($session_id);
        $quote = $quotesession['quote'];
        $items = $quotesession['items'];

        $items_subtotal = 0;
        $total = 0;
        $itmidx = 0;
        foreach ($items as $item) {
            $item_subtotal = 0;
            $imprint_subtotal = 0;
            $colors = $item['items'];
            foreach ($colors as $color) {
                $item_subtotal+=$color['item_qty']*$color['item_price'];
            }
            $items[$itmidx]['item_subtotal'] = $item_subtotal;
            $imprints = $item['imprints'];
            foreach ($imprints as $imprint) {
                $imprint_subtotal += $imprint['imprint_qty'] * $imprint['imprint_price'];
            }
            $items[$itmidx]['imprint_subtotal'] = $imprint_subtotal;
            $items_subtotal+=($item_subtotal + $imprint_subtotal);
            $total+=($item_subtotal + $imprint_subtotal);
            $itmidx++;
        }
        $items_subtotal+=($quote['mischrg_value1']+$quote['mischrg_value2']-$quote['discount_value']);
        $total+=($quote['mischrg_value1']+$quote['mischrg_value2']-$quote['discount_value']);
        $quote['sales_tax'] = 0;
        if ($quote['taxview']==1 && $quote['tax_exempt']==0) {
            // Calc tax
            $basecost = $total + $quote['rush_cost'];
            $tax = round($basecost * ($this->config->item('salesnewtax')/100),2);
            $quote['sales_tax'] = $tax;
        }
        $total+=$quote['sales_tax'] + $quote['rush_cost'] + $quote['shipping_cost'];
        $quote['quote_total'] = $total;
        $quote['items_subtotal'] = $items_subtotal;
        $quotesession['quote'] = $quote;
        $quotesession['items'] = $items;
        usersession($session_id, $quotesession);
    }

    public function removeitem($data, $quotesession, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => 'Item Not Found'];
        $quote_item_id = ifset($data,'item', 0);
        if (!empty($quote_item_id)) {
            $quote = $quotesession['quote'];
            $items = $quotesession['items'];
            $deleted = $quotesession['deleted'];
            $found = 0;
            $idx = 0;
            $newitems = [];
            foreach ($items as $item) {
                if ($item['quote_item_id']==$quote_item_id) {
                    $found = 1;
                } else {
                    $newitems[] = $item;
                }
            }
            if ($found = 1) {
                $out['result'] = $this->success_result;
                if ($quote_item_id > 0) {
                    $deleted[] = ['entity' => 'items', 'id' => $quote_item_id];
                    if (count($newitems)==0) {
                        $quote['lead_time'] = '';
                        $quote['rush_terms'] = '';
                        $quote['rush_days'] = '';
                        $quote['rush_cost'] = 0;
                    }
                }
                $quotesession['quote'] = $quote;
                $quotesession['items'] = $newitems;
                $quotesession['deleted'] = $deleted;
                usersession($session_id, $quotesession);
            }
        }
        return $out;
    }

    public function addquoteitem($postdata, $quotesession, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all parameters send'];
        $item_id = ifset($postdata,'item_id',0);
        $custom_item = ifset($postdata, 'quote_item', '');
        if (!empty($item_id)) {
            $items = $quotesession['items'];
            $startid = count($items)+1;
            $itemdat = $this->add_newleadquote_item($item_id, $custom_item, $startid);
            $out['msg'] = $itemdat['msg'];
            if ($itemdat['result']==$this->success_result) {
                $out['result'] = $this->success_result;
                $quote = $quotesession['quote'];
                $newitem = $itemdat['quote_items'];
                $items[] = $newitem;
                if (empty($quote['lead_time'])) {
                    // Get Delivery Terms
                    $this->load->model('calendars_model');
                    $termdat = $this->calendars_model->get_delivery_date($item_id);
                    $quote['lead_time'] = json_encode($termdat);
                    foreach ($termdat as $row) {
                        if ($row['current']==1) {
                            $quote['rush_cost'] = $row['price'];
                            $quote['rush_days'] = $row['date'];
                            $quote['rush_terms'] = $row['name'];
                        }
                    }
                }
                $quotesession['quote'] = $quote;
                $quotesession['items'] = $items;
                usersession($session_id, $quotesession);
            }
        }
        return $out;
    }
    public function savequote($quotesession, $lead_id, $user_id, $session_id) {
        $out = ['result' => $this->error_result, 'msg' => $this->error_message];
        $quote = $quotesession['quote'];
        $items = $quotesession['items'];
        $shipping = $quotesession['shipping'];
        $deleted = $quotesession['deleted'];
        // Check filling data
        // All Ok, start save
        $this->db->set('quote_template', $quote['quote_template']);
        $this->db->set('mischrg_label1', $quote['mischrg_label1']);
        $this->db->set('mischrg_value1', floatval($quote['mischrg_value1']));
        $this->db->set('mischrg_label2', $quote['mischrg_label2']);
        $this->db->set('mischrg_value2', floatval($quote['mischrg_value2']));
        $this->db->set('discount_label', $quote['discount_label']);
        $this->db->set('discount_value', floatval($quote['discount_value']));
        $this->db->set('lead_time', $quote['lead_time']);
        $this->db->set('shipping_country', $quote['shipping_country']);
        $this->db->set('shipping_contact', $quote['shipping_contact']);
        $this->db->set('shipping_company', empty($quote['shipping_company']) ? null : $quote['shipping_company']);
        $this->db->set('shipping_address1', $quote['shipping_address1']);
        $this->db->set('shipping_address2', empty($quote['shipping_address2']) ? null : $quote['shipping_address2']);
        $this->db->set('shipping_zip', $quote['shipping_zip']);
        $this->db->set('shipping_city', $quote['shipping_city']);
        $this->db->set('shipping_state', $quote['shipping_state']);
        $this->db->set('sales_tax', floatval($quote['sales_tax']));
        $this->db->set('tax_exempt', intval($quote['tax_exempt']));
        $this->db->set('taxview', intval($quote['taxview']));
        $this->db->set('tax_reason', $quote['tax_reason']);
        $this->db->set('rush_terms', $quote['rush_terms']);
        $this->db->set('rush_days', $quote['rush_days']);
        $this->db->set('rush_cost', floatval($quote['rush_cost']));
        $this->db->set('shipping_cost', floatval($quote['shipping_cost']));
        $this->db->set('billing_country', $quote['billing_country']);
        $this->db->set('billing_contact', $quote['billing_contact']);
        $this->db->set('billing_company', empty($quote['billing_company']) ? null : $quote['billing_company']);
        $this->db->set('billing_address1', $quote['billing_address1']);
        $this->db->set('billing_address2', empty($quote['billing_address2']) ? null : $quote['billing_address2']);
        $this->db->set('billing_zip', $quote['billing_zip']);
        $this->db->set('billing_city', $quote['billing_city']);
        $this->db->set('billing_state', $quote['billing_state']);
        $this->db->set('quote_blank', intval($quote['quote_blank']));
        $this->db->set('quote_note', $quote['quote_note']);
        $this->db->set('quote_repcontact', $quote['quote_repcontact']);
        $this->db->set('items_subtotal', floatval($quote['items_subtotal']));
        $this->db->set('imprint_subtotal', floatval($quote['imprint_subtotal']));
        $this->db->set('quote_total', floatval($quote['quote_total']));
        if ($quote['quote_id'] > 0 ) {
            // Update
            $this->db->where('quote_id', $quote['quote_id']);
            $this->db->set('update_user', $user_id);
            $this->db->update('ts_quotes');
            $quote_id = $quote['quote_id'];
        } else {
            $newnum = $this->get_newquote_number($quote['brand']);
            $this->db->set('brand', $quote['brand']);
            $this->db->set('create_time', date('Y-m-d H:i:s'));
            $this->db->set('create_user', $user_id);
            $this->db->set('update_user', $user_id);
            $this->db->set('lead_id', $lead_id);
            $this->db->set('quote_number', $newnum);
            $this->db->set('quote_date', time());
            $this->db->insert('ts_quotes');
            $quote_id = $this->db->insert_id();
        }
        // NEW
        $out['msg'] = 'Error during add new quote';
        if ($quote_id > 0) {
            // Save items, colors, imprints, etc
            foreach ($items as $item) {
                $this->db->set('item_id', $item['item_id']);
                $this->db->set('item_qty', intval($item['item_qty']));
                $this->db->set('item_price', floatval($item['item_price']));
                $this->db->set('imprint_price', floatval($item['imprint_price']));
                $this->db->set('setup_price', floatval($item['setup_price']));
                $this->db->set('item_weigth', floatval($item['item_weigth']));
                $this->db->set('cartoon_qty', intval($item['cartoon_qty']));
                $this->db->set('cartoon_width', intval($item['cartoon_width']));
                $this->db->set('cartoon_heigh', intval($item['cartoon_heigh']));
                $this->db->set('cartoon_depth', intval($item['cartoon_depth']));
                $this->db->set('template', $item['template']);
                $this->db->set('base_price', floatval($item['base_price']));
                if ($item['quote_item_id'] > 0) {
                    $this->db->where('quote_item_id', $item['quote_item_id']);
                    $this->db->update('ts_quote_items');
                    $quote_item_id = $item['quote_item_id'];
                } else {
                    $this->db->set('quote_id', $quote_id);
                    $this->db->insert('ts_quote_items');
                    $quote_item_id = $this->db->insert_id();
                }
                $colors = $item['items'];
                foreach ($colors as $color) {
                    $this->db->set('item_description', $color['item_description']);
                    $this->db->set('item_color', $color['item_color']);
                    $this->db->set('item_qty', intval($color['item_qty']));
                    $this->db->set('item_price', floatval($color['item_price']));
                    if ($color['item_id'] > 0) {
                        $this->db->where('quote_itemcolor_id', $color['item_id']);
                        $this->db->update('ts_quote_itemcolors');
                    } else {
                        $this->db->set('quote_item_id', $quote_item_id);
                        $this->db->insert('ts_quote_itemcolors');
                    }
                }
                $imprints = $item['imprints'];
                foreach ($imprints as $imprint) {
                    if ($imprint['imprint_description']!=='&nbsp;') {
                        $this->db->set('imprint_description', $imprint['imprint_description']);
                        $this->db->set('imprint_item', $imprint['imprint_item']);
                        $this->db->set('imprint_qty', intval($imprint['imprint_qty']));
                        $this->db->set('imprint_price', floatval($imprint['imprint_price']));
                        if ($imprint['quote_imprint_id'] > 0) {
                            $this->db->where('quote_imprint_id', $imprint['quote_imprint_id']);
                            $this->db->update('ts_quote_imprints');
                        } else {
                            $this->db->set('quote_item_id', $quote_item_id);
                            $this->db->insert('ts_quote_imprints');
                        }
                    }
                }
                $imprintdetails = $item['imprint_details'];
                foreach ($imprintdetails as $imprintdetail) {
                    $this->db->set('imprint_active', $imprintdetail['active']);
                    $this->db->set('imprint_type', $imprintdetail['imprint_type']);
                    $this->db->set('repeat_note', $imprintdetail['repeat_note']);
                    $this->db->set('location_id', empty($imprintdetail['location_id']) ? NULL : $imprintdetail['location_id']);
                    $this->db->set('num_colors', $imprintdetail['num_colors']);
                    $this->db->set('print_1', $imprintdetail['print_1']);
                    $this->db->set('print_2', $imprintdetail['print_2']);
                    $this->db->set('print_3', $imprintdetail['print_3']);
                    $this->db->set('print_4', $imprintdetail['print_4']);
                    $this->db->set('setup_1', $imprintdetail['setup_1']);
                    $this->db->set('setup_2', $imprintdetail['setup_2']);
                    $this->db->set('setup_3', $imprintdetail['setup_3']);
                    $this->db->set('setup_4', $imprintdetail['setup_4']);
                    $this->db->set('extra_cost', $imprintdetail['extra_cost']);
                    if ($imprintdetail['quote_imprindetail_id'] > 0) {
                        $this->db->where('quote_imprindetail_id', $imprintdetail['quote_imprindetail_id']);
                        $this->db->update('ts_quote_imprindetails');
                    } else {
                        $this->db->set('quote_item_id', $quote_item_id);
                        $this->db->insert('ts_quote_imprindetails');
                    }
                }
            }
            // Ship Rates
            foreach ($shipping as $rate) {
                $this->db->set('active', $rate['active']);
                $this->db->set('shipping_code', $rate['shipping_code']);
                $this->db->set('shipping_name', $rate['shipping_name']);
                $this->db->set('shipping_rate', $rate['shipping_rate']);
                $this->db->set('shipping_date', $rate['shipping_date']);
                if ($rate['quote_shipping_id'] > 0) {
                    $this->db->where('quote_shipping_id', $rate['quote_shipping_id']);
                    $this->db->update('ts_quote_shippings');
                } else {
                    $this->db->set('quote_id', $quote_id);
                    $this->db->insert('ts_quote_shippings');
                }
            }

            foreach ($deleted as $row) {
                if ($row['entity']=='imprints') {
                    $this->db->where('quote_imprint_id', $row['id']);
                    $this->db->delete('ts_quote_imprints');
                } elseif ($row['entity']=='imprint_details') {
                    $this->db->where('quote_imprindetail_id', $row['id']);
                    $this->db->delete('ts_quote_imprindetails');
                } elseif ($row['entity']=='shipping') {
                    $this->db->where('quote_shipping_id', $row['id']);
                    $this->db->delete('ts_quote_shippings');
                } elseif ($row['entity']=='items') {
                    $this->db->where('quote_item_id', $row['id']);
                    $this->db->delete('ts_quote_items');
                }

            }
            $out['result'] = $this->success_result;
        }
        return $out;
    }

    public function duplicatequote($srcquote_id) {
        $out=['result' => $this->error_result, 'msg' => 'Quote Not Found'];
        $this->db->select('*');
        $this->db->from('ts_quotes');
        $this->db->where('quote_id', $srcquote_id);
        $quote = $this->db->get()->row_array();
        if (ifset($quote, 'quote_id',0)==$srcquote_id) {
            // Quote find
            $quote_setup_fld = ['create_time','create_user','update_time','update_user'];
            $out['result'] = $this->success_result;
            $newnum = $this->get_newquote_number($quote['brand']);
            $quotedat = [];
            foreach ($quote as $key=>$val) {
                if ($key=='quote_id') {
                    $quotedat[$key]=0;
                } elseif ($key=='quote_number') {
                    $quotedat[$key] = $newnum;
                } elseif ($key=='quote_date') {
                    $quotedat[$key] = time();
                } elseif (in_array($key, $quote_setup_fld)) {
                } else {
                    $quotedat[$key] = $val;
                }
            }
            $this->load->model('orders_model');
            $this->load->model('leadorder_model');
            // Items
            $this->db->select('*');
            $this->db->from('ts_quote_items');
            $this->db->where('quote_id', $srcquote_id);
            $items = $this->db->get()->result_array();
            $quoteitems = [];
            $itemid = 1;
            foreach ($items as $item) {
                // Get additional data about item
                if ($item['item_id'] < 0) {
                    $itemdata=$this->orders_model->get_newitemdat($item['item_id']);
                } else {
                    $itemdata=$this->leadorder_model->_get_itemdata($item['item_id']);
                }
                $colors = $itemdata['colors'];
                $curcolors = $curimprints = $curimprdetails = [];
                // Item Colors
                $this->db->select('*');
                $this->db->from('ts_quote_itemcolors');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $itemcolors = $this->db->get()->result_array();
                $colorid = 1;
                $colorrow = 1;
                $colorssubtotal = 0;
                foreach ($itemcolors as $itemcolor) {
                    foreach ($itemcolor as $ckey=>$cval) {
                        if ($ckey=='quote_itemcolor_id') {
                            $itemcolor['item_id'] = $colorid*(-1);
                        } elseif ($ckey=='quote_item_id') {
                            $itemcolor[$key] = $itemid * (-1);
                        }
                    }
                    $itemcolor['item_subtotal'] = $itemcolor['item_qty'] * $itemcolor['item_price'];
                    $colorssubtotal+=$itemcolor['item_qty'] * $itemcolor['item_price'];
                    $itemcolor['item_number'] = $itemdata['item_number'];
                    $itemcolor['item_row'] = $colorrow;
                    $itemcolor['colors'] = $colors;
                    $itemcolor['num_colors'] = count($colors);
                    $itemcolor['printshop_item_id'] = (isset($itemdata['printshop_item_id']) ? $itemdata['printshop_item_id']  : '');
                    $itemcolor['qtyinput_class']='normal';
                    $itemcolor['qtyinput_title']='';
                    if (count($colors)==0) {
                        $out_colors = $this->empty_htmlcontent;
                    } else {
                        $options=array(
                            'quote_item_id'=>$itemcolor['quote_item_id'],
                            'item_id'=>$itemcolor['item_id'],
                            'colors'=>$itemcolor['colors'],
                            'item_color'=>$itemcolor['item_color'],
                        );
                        $out_colors = $this->load->view('leadpopup/quoteitem_color_choice', $options, TRUE);
                    }
                    $itemcolor['out_colors'] = $out_colors;
                    $itemcolor['item_color_add'] = 0;
                    if ($colorrow==1 && count($colors) > 1) {
                        $itemcolor['item_color_add'] = 1;
                    }
                    $curcolors[] = $itemcolor;
                    $colorid++;
                    $colorrow++;
                }
                // Item Imprints
                $this->db->select('*');
                $this->db->from('ts_quote_imprints');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $itemimprints = $this->db->get()->result_array();
                if (count($itemimprints)==0) {
                    $curimprints[] = [
                        'quote_imprint_id'=>-1,
                        'imprint_description'=>'&nbsp;',
                        'imprint_qty'=>0,
                        'imprint_price'=>0,
                        'imprint_item'=>0,
                        'imprint_subtotal'=>'&nbsp;',
                        'imprint_price_class' => 'normal',
                        'imprint_price_title' => '',
                        'delflag'=>0,
                    ];
                } else {
                    $imprintid = 1;
                    foreach ($itemimprints as $itemimprint) {
                        foreach ($itemimprint as $ikey => $ival) {
                            if ($ikey=='quote_imprint_id') {
                                $itemimprint[$ikey] = $imprintid * (-1);
                            } elseif ($ikey=='quote_item_id') {
                                $itemimprint[$ikey] = $itemid * (-1);
                            }
                        }
                        $itemimprint['imprint_price_class'] = 'nornal';
                        $itemimprint['imprint_price_title'] = '';
                        $itemimprint['delflag'] = 0;
                        $itemimprint['imprint_subtotal'] = $itemimprint['imprint_qty'] * $itemimprint['imprint_price'];
                        $curimprints[] = $itemimprint;
                        $imprintid++;
                    }
                }
                // Item Imprint Details
                $this->db->select('*');
                $this->db->from('ts_quote_imprindetails');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $imprintdetails = $this->db->get()->result_array();
                if (count($imprintdetails)==0) {

                } else {
                    $detailid = 1;
                    foreach ($imprintdetails as $imprintdetail) {
                        foreach ($imprintdetail as $dkey => $dval) {
                            if ($dkey=='quote_imprindetail_id') {
                                $imprintdetail[$dkey] = $detailid * (-1);
                            } elseif ($dkey=='quote_item_id') {
                                $imprintdetail[$dkey] = $itemid * (-1);
                            } elseif ($dkey=='imprint_active') {
                                $imprintdetail['active'] = $val;
                            }
                        }
                        $imprintdetail['title'] = 'Loc '.$detailid;
                        $curimprdetails[] = $imprintdetail;
                        $detailid++;
                    }
                }
                foreach ($item as $key => $val) {
                    if ($key=='quote_item_id') {
                        $item[$key] = $itemid * (-1);
                    } elseif ($key=='quote_id') {
                        $item[$key] = $quotedat[$key];
                    }
                    $item['item_number'] = $itemdata['item_number'];
                    $item['item_name'] = $itemdata['item_name'];
                    $item['colors'] = $colors;
                    $item['num_colors'] = count($colors);
                    $item['imprint_locations']=$itemdata['imprints'];
                    $item['vendor_zipcode']=$itemdata['vendor_zipcode'];
                    $item['charge_perorder']=$itemdata['charge_perorder'];
                    $item['charge_pereach']=$itemdata['charge_pereach'];
                    $item['item_subtotal']=$colorssubtotal;
                    // Add Imprins, colors, details
                    $item['items'] = $curcolors;
                    $item['imprints'] = $curimprints;
                    $item['imprint_details'] = $curimprdetails;
                }
                $quoteitems[] = $item;
                $itemid++;
            }
            // Shipping
            $this->db->select('*');
            $this->db->from('ts_quote_shippings');
            $this->db->where('quote_id', $srcquote_id);
            $ships = $this->db->get()->result_array();
            $curship = [];
            $shipid = 1;
            foreach ($ships as $ship) {
                foreach ($ship as $skey => $sval) {
                    if ($skey=='quote_shipping_id') {
                        $ship[$skey] = $shipid * (-1);
                    } elseif ($skey=='quote_id') {
                        $ship[$skey] = $quotedat['quote_id'];
                    }
                }
                $curship[] = $ship;
                $shipid++;
            }
            $out['quote'] = $quotedat;
            $out['items'] = $quoteitems;
            $out['shippings'] = $curship;
        }
        return $out;
    }

    public function prepare_quotedoc($quote_id) {
        $out=['result' => $this->error_result, 'msg' => 'Lead Not Found'];
        $this->db->select('*');
        $this->db->from('ts_quotes');
        $this->db->where('quote_id', $quote_id);
        $quote = $this->db->get()->row_array();
        if (ifset($quote,'quote_id',0)==$quote_id) {
            $this->load->model('orders_model');
            $this->load->model('leadorder_model');
            $usrrepl = '';
            if (!empty($quote['create_user'])) {
                $this->db->select('user_initials');
                $this->db->from('users');
                $this->db->where('user_id', $quote['create_user']);
                $usrres = $this->db->get()->row_array();
                $usrrepl = ifset($usrres,'user_initials','');
            }
            $quote['usrrepl'] = $usrrepl;
            // Billing and shipping array
            $bill = $ship = [];
            if (!empty($quote['billing_company'])) {
                array_push($bill, $quote['billing_company']);
            }
            if (!empty($quote['billing_contact'])) {
                array_push($bill, $quote['billing_contact']);
            }
            if (!empty($quote['billing_address1'])) {
                array_push($bill, $quote['billing_address1']);
            }
            if (!empty($quote['billing_address2'])) {
                array_push($bill, $quote['billing_address2']);
            }
            $billcity = '';
            if (!empty($quote['billing_city'])) {
                $billcity.=$quote['billing_city'].' ';
            }
            if (!empty($quote['billing_state'])) {
                $billcity.=$quote['billing_state'].' ';
            }
            if (!empty($quote['billing_zip'])) {
                $billcity.=$quote['billing_zip'];
            }
            if (!empty($billcity)) {
                array_push($bill, $billcity);
            }
            $quote['billing'] = $bill;
            if (!empty($quote['shipping_company'])) {
                array_push($ship, $quote['shipping_company']);
            }
            if (!empty($quote['shipping_contact'])) {
                array_push($ship, $quote['shipping_contact']);
            }
            if (!empty($quote['shipping_address1'])) {
                array_push($ship, $quote['shipping_address1']);
            }
            if (!empty($quote['shipping_address2'])) {
                array_push($ship, $quote['shipping_address2']);
            }
            $shipcity = '';
            if (!empty($quote['shipping_city'])) {
                $shipcity.=$quote['shipping_city'].' ';
            }
            if (!empty($quote['shipping_state'])) {
                $shipcity.=$quote['shipping_state'].' ';
            }
            if (!empty($quote['shipping_zip'])) {
                $shipcity.=$quote['shipping_zip'];
            }
            if (!empty($shipcity)) {
                array_push($ship, $shipcity);
            }
            $quote['shipping'] = $ship;
            $this->db->select('*');
            $this->db->from('ts_quote_items');
            $this->db->where('quote_id', $quote_id);
            $items = $this->db->get()->result_array();
            $itemidx = 0;
            foreach ($items as $item) {
                if ($item['item_id'] < 0) {
                    $itemdata=$this->orders_model->get_newitemdat($item['item_id']);
                } else {
                    $itemdata=$this->leadorder_model->_get_itemdata($item['item_id']);
                }
                $items[$itemidx]['item_number'] = $itemdata['item_number'];
                // Colors
                $this->db->select('*');
                $this->db->from('ts_quote_itemcolors');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $colors = $this->db->get()->result_array();
                $items[$itemidx]['colors'] = $colors;
                // Imprints
                $this->db->select('*');
                $this->db->from('ts_quote_imprints');
                $this->db->where('quote_item_id', $item['quote_item_id']);
                $imprints = $this->db->get()->result_array();
                $items[$itemidx]['imprints'] = $imprints;
                $itemidx++;
            }
            // Shippings
            $this->db->select('*');
            $this->db->from('ts_quote_shippings');
            $this->db->where('quote_id', $quote_id);
            $this->db->where('active', 1);
            $shipping = $this->db->get()->result_array();
            if ($quote['brand']=='SR') {
                $res = $this->_prepare_quotesrdoc($quote, $items, $shipping);
            } else {
                $res = $this->_prepare_quotesbdoc($quote, $items, $shipping);
            }
            $out['msg'] = $res['msg'];
            if ($res['result']==$this->success_result) {
                $out['result'] = $this->success_result;
                $out['docurl'] = $res['docurl'];
            }
        }
        return $out;
    }

    private function _prepare_quotesrdoc($quote, $items, $shipping) {

    }

    private function _prepare_quotesbdoc($quote, $items, $shipping) {
        $out = ['result' => $this->error_result, 'msg' => 'Error during create PDF doc'];
        $filname = 'quote_QB-'.$quote['quote_number'].'.pdf';
        define('FPDF_FONTPATH', FCPATH.'font');
        $this->load->library('fpdf/fpdfeps');
        // Logo
        // $logoFile = FCPATH."/img/leadquote/logo-bluetrack-stressballs-2.eps";
        $logoFile = FCPATH."/img/invoice/logos-2.eps";
        $logoWidth = 119;
        $logoHeight = 15;
        $logoYPos = 15;
        $logoXPos = 16;
        // Table Columns X
        $headWidth = [
            25, 81, 24, 24, 28,
        ];
        $colWidth = [
            25, 81, 23, 24, 28.3,
        ];
        $startPageX = 16;
        $pdf = new FPDFEPS('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetFont('Times','',9.035143);
        $pdf->SetTextColor(0, 0, 0);
        // Logo
        $pdf->ImageEps( $logoFile, $logoXPos, $logoYPos, $logoWidth, $logoHeight );
        // Inv #
        $pdf->SetXY(138, 15.2);
        $pdf->SetFont('','B',16.564429);
        // $pdf->SetTextColor(0, 0, 255);
        if ($quote['quote_template']=='Proforma Invoice') {
            $pdf->Cell(57,16,'INVOICE',0,0,'R');
        } else {
            $pdf->Cell(57,16,'OFFICIAL QUOTE',0,0,'R');
        }
        // Our Address
        $pdf->SetFont('','',12.046857);
        $ourAddressY = 33.68;
        $pdf->Text($startPageX, $ourAddressY, '855 Bloomfield Ave');
        $ourAddressY += 5.8;
        $pdf->Text($startPageX, $ourAddressY, 'Clifton, NJ 07012');
        $ourAddressY += 5.8;
        $pdf->Text($startPageX,$ourAddressY, 'Call Us at');
        $pdf->SetTextColor(0,0,255);
        $pdf->Text(31,$ourAddressY, '1-800-790-6090');
        $ourAddressY += 5.8;
        $pdf->Text($startPageX,$ourAddressY,'www.stressballs.com'); // , 'www.bluetrack.com');
        // Quote Title
        $pdf->SetXY(153, 29);
        $pdf->SetFont('','',12.5);
        $pdf->setFillColor(17, 100, 238);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(20,6,'Quote #',0,0,'C',true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(22,6,'SB-'.$quote['quote_number'],0,0,'R');
        $pdf->SetXY(162, 36);
        $pdf->Cell(12, 6, 'Date:');
        $pdf->SetXY(174, 36);
        $pdf->Cell(20,6,date('m/d/Y', $quote['quote_date']),0,0,'R');
        $pdf->SetXY(160, 44);
        $pdf->setFillColor(128, 128, 128);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('','',12.046857);
        $pdf->Cell(15, 6, 'Rep',1,0,'C',true);
        $pdf->SetXY(178, 44);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(15,6, $quote['usrrepl'],1,0,'C');
        // Billing Address
        $pdf->SetXY($startPageX, 55);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetCellMargin(4);
        $pdf->Cell(88, 8, 'Billing Address',1,0,'',true);
        $pdf->SetXY($startPageX,63);
        $pdf->Cell(88, 36, '',1);
        $pdf->SetTextColor(0,0,0);
        $yStart = 63;
        foreach ($quote['billing'] as $billrow) {
            $pdf->SetXY($startPageX, $yStart);
            $pdf->Cell(87, 6, $billrow);
            $yStart+=7;
        }
        // Shipping Address
        $pdf->SetTextColor(255,255,255);
        $pdf->SetXY(105, 55);
        $pdf->Cell(88, 8, 'Shipping Address',1,0,'',true);
        $pdf->SetTextColor(0,0,0);
        $pdf->SetXY(105,63);
        $pdf->Cell(88, 36, '',1);
        $pdf->SetTextColor(0,0,0);
        $yStart = 63;
        foreach ($quote['shipping'] as $shiprow) {
            $pdf->SetXY(105, $yStart);
            $pdf->Cell(87, 6, $shiprow);
            $yStart+=7;
        }
        $pdf->SetCellMargin(3);
        // $yStart = $pdf->getY() + 3;
        $yStart = 102;
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY($startPageX, $yStart);
        $pdf->Cell($colWidth[0], 6, 'Item',1,0,'C', true);
        $pdf->Cell($colWidth[1], 6, 'Description',1, 0,'C', true);
        $pdf->Cell($colWidth[2], 6, 'Qty', 1, 0,'C', true);
        $pdf->Cell($colWidth[3], 6, 'Price (ea)', 1, 0, 'C', true);
        $pdf->Cell($colWidth[4], 6, 'Total:', 1, 0,'C', true);
        $yStart+=6;
        $numpp=1;
        $pdf->setFillcolor(230, 230, 230);
        $pdf->SetTextColor(0,0,0);
        foreach ($items as $item) {
            $colors = $item['colors'];
            foreach ($colors as $color) {
                $fillrow = ($numpp % 2) == 0 ? 1 : 0;
                $total = $color['item_qty'] * $color['item_price'];
                $pdf->SetXY($startPageX, $yStart);
                $pdf->Cell($colWidth[0], 5, $item['item_number'], 'LR', 0, 'L', $fillrow);
                $pdf->Cell($colWidth[1], 5, $color['item_description'] . ' ' . $color['item_color'], 'LR', 0, 'L', $fillrow);
                $pdf->Cell($colWidth[2], 5, QTYOutput($color['item_qty']), 'LR', 0, 'C', $fillrow);
                $pdf->Cell($colWidth[3], 5, number_format($color['item_price'], 2), 'LR', 0, 'C', $fillrow);
                $pdf->Cell($colWidth[4], 5, MoneyOutput($total) . 'T', 'LR', 0, 'R', $fillrow);
                $numpp++;
                $yStart += 5;
            }
            $imprints = $item['imprints'];
            foreach ($imprints as $imprint) {
                $fillrow = ($numpp % 2) == 0 ? 1 : 0;
                $total = $imprint['imprint_qty'] * $imprint['imprint_price'];
                $rowcode = 'SB-impr1';
                if ($imprint['imprint_item'] == 0) {
                    $rowcode = 'SB-setu1';
                }
                $pdf->SetXY($startPageX, $yStart);
                $pdf->Cell($colWidth[0], 5, $rowcode, 'LR', 0, 'L', $fillrow);
                $pdf->Cell($colWidth[1], 5, $imprint['imprint_description'], 'LR', 0, 'L', $fillrow);
                $pdf->Cell($colWidth[2], 5, QTYOutput($imprint['imprint_qty']), 'LR', 0, 'C', $fillrow);
                $pdf->Cell($colWidth[3], 5, number_format($imprint['imprint_price'], 2), 'LR', 0, 'C', $fillrow);
                $pdf->Cell($colWidth[4], 5, MoneyOutput($total) . 'T', 'LR', 0, 'R', $fillrow);
                $numpp++;
                $yStart += 5;
            }
        }
        if (!empty($quote['mischrg_label1']) && !empty($quote['mischrg_value1'])) {
            $pdf->SetXY($startPageX, $yStart);
            $fillrow=($numpp%2)==0 ? 1 : 0;
            $pdf->Cell($colWidth[0], 5, 'SB-misc1','LR',0,'L', $fillrow);
            $pdf->Cell($colWidth[1], 5, $quote['mischrg_label1'],'LR', 0,'L', $fillrow);
            $pdf->Cell($colWidth[2], 5, 1, 'LR', 0,'C', $fillrow);
            $pdf->Cell($colWidth[3], 5, number_format($quote['mischrg_value1'],2), 'LR', 0, 'C', $fillrow);
            $pdf->Cell($colWidth[4], 5, MoneyOutput($quote['mischrg_value1']).'T', 'LR', 0,'R', $fillrow);
            $numpp++;
            $yStart+=5;
        }
        if (!empty($quote['mischrg_label2']) && !empty($quote['mischrg_value2'])) {
            $pdf->SetXY($startPageX, $yStart);
            $fillrow=($numpp%2)==0 ? 1 : 0;
            $pdf->Cell($colWidth[0], 5, 'SB-misc2','LR',0,'L', $fillrow);
            $pdf->Cell($colWidth[1], 5, $quote['mischrg_label2'],'LR', 0,'L', $fillrow);
            $pdf->Cell($colWidth[2], 5, 1, 'LR', 0,'C', $fillrow);
            $pdf->Cell($colWidth[3], 5, number_format($quote['mischrg_value2'],2), 'LR', 0, 'C', $fillrow);
            $pdf->Cell($colWidth[4], 5, MoneyOutput($quote['mischrg_value2']).'T', 'LR', 0,'R', $fillrow);
            $numpp++;
            $yStart+=5;
        }
        if (!empty($shipping)) {
            $pdf->SetXY($startPageX, $yStart);
            $fillrow=($numpp%2)==0 ? 1 : 0;
            $pdf->Cell($colWidth[0], 5, 'SB-ship1','LR',0,'L', $fillrow);
            $pdf->Cell($colWidth[1], 5, $shipping[0]['shipping_name'].' Shippin Charge','LR', 0,'L', $fillrow);
            $pdf->Cell($colWidth[2], 5, 1, 'LR', 0,'C', $fillrow);
            $pdf->Cell($colWidth[3], 5, number_format($shipping[0]['shipping_rate'],2), 'LR', 0, 'C', $fillrow);
            $pdf->Cell($colWidth[4], 5, MoneyOutput($shipping[0]['shipping_rate']).'T', 'LR', 0,'R', $fillrow);
            $numpp++;
            $yStart+=5;
        }
        if (!empty($quote['discount_label']) && !empty($quote['discount_value'])) {
            $pdf->SetXY($startPageX, $yStart);
            $pdf->Cell($colWidth[0], 5, 'SB-misc2','LR',0,'L', $fillrow);
            $pdf->Cell($colWidth[1], 5, $quote['mischrg_label2'],'LR', 0,'L', $fillrow);
            $pdf->Cell($colWidth[2], 5, 1, 'LR', 0,'C', $fillrow);
            $pdf->Cell($colWidth[3], 5, number_format($quote['mischrg_value2'],2), 'LR', 0, 'C', $fillrow);
            $pdf->Cell($colWidth[4], 5, MoneyOutput($quote['mischrg_value2']).'T', 'LR', 0,'R', $fillrow);
            $numpp++;
            $yStart+=5;
        }
        // Empty Row
        $pdf->SetXY($startPageX, $yStart);
        $fillrow=($numpp%2)==0 ? 1 : 0;
        $pdf->Cell($colWidth[0], 7, '','LR',0,'L', $fillrow);
        $pdf->Cell($colWidth[1], 7, '','LR', 0,'L', $fillrow);
        $pdf->Cell($colWidth[2], 7, '', 'LR', 0,'C', $fillrow);
        $pdf->Cell($colWidth[3], 7, '', 'LR', 0, 'C', $fillrow);
        $pdf->Cell($colWidth[4], 7, '', 'LR', 0,'R', $fillrow);
        $numpp++;
        $yStart+=7;
        if (!empty($quote['quote_repcontact'])) {
            $fillrow=($numpp%2)==0 ? 1 : 0;
            $pdf->SetXY($startPageX + $colWidth[0], $yStart);
            $pdf->MultiCell($colWidth[1], 5, $quote['quote_repcontact'],'LR', 'L', $fillrow);
            $multY = $pdf->getY();
            $rowHeight = $multY-$yStart;
            $pdf->SetXY($startPageX, $yStart);
            $pdf->Cell($colWidth[0], $rowHeight, '','LR',0,'L', $fillrow);
            $pdf->SetXY(($startPageX+$colWidth[0]+$colWidth[1]), $yStart);
            $pdf->Cell($colWidth[2], $rowHeight, '', 'LR', 0,'C', $fillrow);
            $pdf->Cell($colWidth[3], $rowHeight, '', 'LR', 0, 'C', $fillrow);
            $pdf->Cell($colWidth[4], $rowHeight, '', 'LR', 0,'R', $fillrow);
            $numpp++;
            $yStart = $multY;
        }
        $rowHeight = 7;
        if ($yStart < 178) {
            $rowHeight = 178 - $yStart;
        }
        $pdf->SetXY($startPageX, $yStart);
        $fillrow=($numpp%2)==0 ? 1 : 0;
        $pdf->Cell($colWidth[0], $rowHeight, '','LRB',0,'L', $fillrow);
        $pdf->Cell($colWidth[1], $rowHeight, '','LRB', 0,'L', $fillrow);
        $pdf->Cell($colWidth[2], $rowHeight, '', 'LRB', 0,'C', $fillrow);
        $pdf->Cell($colWidth[3], $rowHeight, '', 'LRB', 0, 'C', $fillrow);
        $pdf->Cell($colWidth[4], $rowHeight, '', 'LRB', 0,'R', $fillrow);
        $yStart += $rowHeight;
        // Total
        $pdf->SetXY($startPageX, $yStart);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->SetFont('','B',14);
        $pdf->Cell(111.5, 14, 'Best Prices Guaranteed.  No hidden fees.', 0, 0,'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('','',12.5);
        $pdf->Cell(40,7,'NJ Sales Tax (0.0%)', 'LB',0,'C');
        $pdf->Cell(30,7, MoneyOutput($quote['sales_tax']),'BR',0,'R');
        $pdf->SetXY(127.5, $yStart+7);
        $pdf->SetFont('','B',14);
        $pdf->Cell(20,12,'Total:', 'LB',0,'C');
        $pdf->SetFont('','',14);
        $pdf->Cell(50,12, MoneyOutput($quote['quote_total']),'BR',0,'R');
        $yStart += 23;
        $pdf->SetDash(1,1);
        $pdf->Line($startPageX,$yStart,195, $yStart);
        $pdf->Line($startPageX, $yStart, $startPageX, $yStart+55);
        $pdf->Line(195,$yStart,195, $yStart+55);
        $pdf->Line($startPageX, $yStart+55, 195, $yStart+55);
        $pdf->SetDash();
        // Bottom title
        $bottomY = $yStart + 57;
        $pdf->SetFont('','',12.05);
        $pdf->SetXY($startPageX, $bottomY);
        $pdf->MultiCell(195, 5, 'Stressballs.com - 855 Bloomfield Avenue - Clifton, NJ 07012 - USA'.PHP_EOL.'(Tel) 201-210-8700  -  (Fax) 201-604-2688',0,'C');
        // Quick Order
        $quickOrdY = $yStart;
        $pdf->SetXY($startPageX, $quickOrdY);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->SetFont('', 'B', 12);
        $pdf->Cell(55, 10, 'Quick Order Form:', 0, 0, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('', '', 9.5);
        $pdf->Cell(120,6, 'Please correct any incorrect or missing billing or shipping information listed above.');
        $quickOrdY += 9;
        $pdf->SetXY($startPageX, $quickOrdY);
        $pdf->SetFont('', '', 10.5);
        $pdf->SetCellMargin(4);
        $pdf->Cell(35, 6, 'Contact:', 0, 0,'R');
        $pdf->Cell(48,6,'', 1);
        $pdf->Cell(38,6,'Payment Info:',0,0,'R');
        $quickOrdY+=7.6;
        $pdf->SetXY($startPageX, $quickOrdY);
        $pdf->Cell(35, 6, 'Telephone:', 0, 0,'R');
        $pdf->Cell(48,6,'', 1);
        $pdf->Cell(38,6,'Credit Card #:',0,0,'R');
        $pdf->Cell(48,6,'', 1);
        $quickOrdY+=7.6;
        $pdf->SetXY($startPageX, $quickOrdY);
        $pdf->Cell(35, 6, 'Email:', 0, 0,'R');
        $pdf->Cell(48,6,'', 1);
        $pdf->Cell(38,6,'Exp Date:',0,0,'R');
        $pdf->Cell(18,6,'', 1);
        $pdf->Cell(21,6,'CVV Code:',0,0,'R');
        $pdf->Cell(18,6,'', 1);
        $quickOrdY+=7.6;
        $pdf->SetXY($startPageX, $quickOrdY);
        $pdf->SetFont('','',9.5);
        $pdf->SetTextColor(0, 0, 128);
        $pdf->Cell(83,6,'All printed orders will receive an art proof to approve.',0,0,'R');
        $pdf->SetTextColor(0, 64, 0);
        $pdf->SetFont('', '', 10.5);
        $pdf->Cell(38,6,'Signature:',0,0,'R');
        $pdf->Cell(48,6,'', 'B');
        $quickOrdY+=8.2;
        $pdf->SetXY(18,$quickOrdY);
        $pdf->SetFillColor(17, 100, 238);
        $pdf->Cell(173,12,'', 1,0,'',1);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('', '', 10.5);
        $pdf->SetXY(18,$quickOrdY);
        $pdf->Cell(173,6,'To order call 1-800-790-6090 or order securely online',0,0,'C');
        $pdf->SetXY(18,$quickOrdY+6);
        $pdf->Cell(173,6,'or fill out this form and send back by email (sales@stressballs.com) or fax (201-604-2688)',0,0,'C');
        // Save file


        $file_out = $this->config->item('upload_path_preload').$filname;
        $pdf->Output('F', $file_out);
        $out['result'] = $this->success_result;
        $out['docurl'] = $this->config->item('pathpreload').$filname;
        return $out;
    }

    // List of quotes
    public function leadquotes_count($options) {
        $this->db->select('count(quote_id) as cnt');
        $this->db->from('ts_quotes');
        if (ifset($options,'brand', 'ALL')!=='ALL') {
            if ($options['brand']=='SR') {
                $this->db->where('brand', $options['brand']);
            } else {
                $this->db->where_in('brand', ['SB', 'BT']);
            }
        }
        $res = $this->db->get()->row_array();
        return $res['cnt'];
    }

    public function leadquotes_lists($options) {
        $this->db->select('q.*, u.user_name, u.user_initials');
        $this->db->from('ts_quotes q');
        $this->db->join('users u','u.user_id=q.create_user');
        $lists = $this->db->get()->result_array();
        $out = [];
        return $out;
    }
}