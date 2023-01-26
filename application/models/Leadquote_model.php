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
    private $normal_template='Stressball';
    private $default_zip='07012';
    private $error_message='Unknown error. Try later';
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
            $quotedat = [
                'quote_id' => 0,
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
            ];
            // Items
            $quote_items = [];
            if (!empty($lead_data['lead_item_id'])) {
                $itemdat = $this->add_newleadquote_item($lead_data['lead_item_id'], $lead_data['other_item_name']);
                if ($itemdat['result']==$this->success_result) {
                    $quote_items[] = $itemdat['quote_items'];
                }
            }
//            $outdat = [
//                'quote_itemcount' => count($quote_items),
//                'quote_items' => $quote_items,
//            ];
            $response['quote'] = $quotedat;
            $response['quote_items'] = $quote_items;
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

    public function add_newleadquote_item($item_id, $custom_name) {
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
        $newid = -1;
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
            'item_template'=> ifset($itemdata, 'item_template', $this->normal_template),
            'item_weigth'=>0,
            'cartoon_qty'=>0,
            'cartoon_width'=>0,
            'cartoon_heigh'=>0,
            'cartoon_depth'=>0,
            'boxqty'=>'',
            'setup_price'=>0,
            'print_price'=>0,
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
            $newprice=$this->leadorder_model->_get_item_priceqty($item_id, $quoteitem['item_template'] , $defqty);
            $setupprice=$this->leadorder_model->_get_item_priceimprint($item_id, 'setup');
            $printprice=$this->leadorder_model->_get_item_priceimprint($item_id, 'imprint');
            $quoteitem['item_weigth']=$itemdata['item_weigth'];
            $quoteitem['cartoon_qty']=$itemdata['cartoon_qty'];
            $quoteitem['cartoon_width']=$itemdata['cartoon_width'];
            $quoteitem['cartoon_heigh']=$itemdata['cartoon_heigh'];
            $quoteitem['cartoon_depth']=$itemdata['cartoon_depth'];
            $quoteitem['boxqty']=$itemdata['boxqty'];
            $quoteitem['setup_price']=$setupprice;
            $quoteitem['print_price']=$printprice;
            $quoteitem['base_price']=$newprice;
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
        $newitem['item_subtotal']=MoneyOutput($defqty*$newprice);
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
                $newloc['print_1']=$quoteitem['print_price'];
            }
            $newloc['print_2']=$quoteitem['print_price'];
            $newloc['print_3']=$quoteitem['print_price'];
            $newloc['print_4']=$quoteitem['print_price'];
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
                    $newprice=$this->leadorder_model->_get_item_priceqty($items[$itemidx]['item_id'], $items[$itemidx]['item_template'] , $items[$itemidx]['item_qty']);
                    $items[$itemidx]['base_price']=$newprice;
                    $out['price_class']='normal';
                    $ridx=0;
                    foreach ($items[$itemidx]['items'] as $row) {
                        $items[$itemidx]['items'][$ridx]['item_price']=$newprice;
                        $items[$itemidx]['items'][$ridx]['qtyinput_class']='normal';
                        $rowtotal = $row['item_qty']*$row['item_price'];
                        $items[$itemidx]['items'][$ridx]['item_subtotal']=MoneyOutput($rowtotal);
                        $ridx++;
                    }
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

    public function calc_quote_shipping($session_id) {

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
        $total+=$quote['sales_tax'] + $quote['rush_cost'] + $quote['shipping_cost'];
        $quote['total'] = $total;
        $quote['items_subtotal'] = $items_subtotal;
        $quotesession['quote'] = $quote;
        $quotesession['items'] = $items;
        usersession($session_id, $quotesession);
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
                $this->db->set('imprint_price', floatval($item['print_price']));
                $this->db->set('setup_price', floatval($item['setup_price']));
                $this->db->set('item_weigth', floatval($item['item_weigth']));
                $this->db->set('cartoon_qty', intval($item['cartoon_qty']));
                $this->db->set('cartoon_width', intval($item['cartoon_width']));
                $this->db->set('cartoon_heigh', intval($item['cartoon_heigh']));
                $this->db->set('cartoon_depth', intval($item['cartoon_depth']));
                $this->db->set('template', $item['item_template']);
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
            }
            $out['result'] = $this->success_result;
        }
        return $out;
    }

}