<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Sritems_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_relievers_itemscount($options=[]) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->select('(vm.size+vm.weigth+vm.material+vm.lead_a+vm.lead_b+vm.lead_c+vm.colors+vm.categories+vm.images+vm.prices) as missings');
        $this->db->from('sb_items i');
        $this->db->join('v_item_missinginfo vm','i.item_id=vm.item_id','left');
        $this->db->where('i.brand', 'SR');
        if (ifset($options, 'category',0)!=0) {
            $this->db->where('i.category_id', $options['category']);
        }
        if (ifset($options, 'search', '')!=='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".$options['search']."%'";
            $this->db->where($where);
        }
        if (ifset($options, 'status', 0) > 0) {
            if ($options['status']==1) {
                $this->db->where('i.item_active', 1);
            } else {
                $this->db->where('i.item_active', 0);
            }
        }
        if (ifset($options,'vendor', '')!=='') {
            $this->db->join('sb_vendor_items svi','i.vendor_item_id = svi.vendor_item_id','left');
            $this->db->join('vendors v','v.vendor_id=svi.vendor_item_vendor');
            $this->db->where('v.vendor_id', $options['vendor']);
        }
        if (ifset($options,'misinfo',0) > 0) {
            if ($options['misinfo']==1) {
                $this->db->having('missings=0');
            } else {
                $this->db->having('missings>0');
            }
        }
        $res = $this->db->get()->result_array();
        return count($res);
    }

    public function get_relievers_itemslist($options) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        // $this->db->select('v.vendor_name as vendor, v.vendor_phone, v.vendor_email, v.vendor_website, svi.vendor_item_number');
        // $this->db->select('(vm.size+vm.weigth+vm.material+vm.lead_a+vm.lead_b+vm.lead_c+vm.colors+vm.categories+vm.images+vm.prices) as missings');
        $this->db->select('(vm.size+vm.weigth+vm.material+vm.url+vm.meta_title+vm.meta_description+vm.meta_keywords+vm.attributes+vm.images+vm.itemprices+vm.mainprices) as missings');
        $this->db->from('sb_items i');
        $this->db->join('v_sritem_missinginfo vm','i.item_id=vm.item_id','left');
        $this->db->where('i.brand', 'SR');
        if (ifset($options, 'category',0)!=0) {
            $this->db->where('i.category_id', $options['category']);
        }
        if (ifset($options, 'search', '')!=='') {
            $where="lower(concat(i.item_number,i.item_name)) like '%".$options['search']."%'";
            $this->db->where($where);
        }
        if (ifset($options, 'status', 0) > 0) {
            if ($options['status']==1) {
                $this->db->where('i.item_active', 1);
            } else {
                $this->db->where('i.item_active', 0);
            }
        }
        if (ifset($options,'vendor', '')!=='') {
            $this->db->join('sb_vendor_items svi','i.vendor_item_id = svi.vendor_item_id','left');
            $this->db->join('vendors v','v.vendor_id=svi.vendor_item_vendor');
            $this->db->where('v.vendor_id', $options['vendor']);
        }
        if (ifset($options,'misinfo',0) > 0) {
            if ($options['misinfo']==1) {
                $this->db->having('missings=0');
            } else {
                $this->db->having('missings>0');
            }
        }
        $order_by = ifset($options, 'order_by','i.item_id');
        $direc = ifset($options, 'direct','asc');
        $this->db->order_by($order_by, $direc);
        $limit = ifset($options, 'limit', 0);
        $offset = ifset($options, 'offset', 0);
        if ($limit > 0) {
            if ($offset>0) {
                $this->db->limit($limit, $offset);
            } else {
                $this->db->limit($limit);
            }
        }
        $res = $this->db->get()->result_array();
        $out=[];
        $numpp = $offset + 1;
        foreach ($res as $row) {
            $rowclass='';
            if ($row['item_active']==0) {
                $rowclass='inactive';
            }
            $row['rowclass']=$rowclass;
            $row['status'] = $row['item_active']==1 ? 'Active' : 'Inactive';
            if ($row['missings']==0) {
                $row['misclas'] = '';
                $row['misinfo'] = '-';
            } else {
                $row['misclas'] = 'missing';
                if ($row['missings']==1) {
                    $row['misinfo'] = '1 field';
                } else {
                    $row['misinfo'] = $row['missings'].' fields';
                }
            }
            $row['numpp'] = $numpp;
            $out[] = $row;
            $numpp++;
        }
        return $out;
    }

    public function new_sritemlist() {
        $brand = 'SR';
        // Start
        $item = [
            'item_id' => -1,
            'item_number' => '',
            'item_name' => '',
            'item_active' => 1,
            'item_new' => 0,
            'item_sale' => 0,
            'item_topsale' => 0,
            'item_template' => '',
            'item_lead_a' => 0,
            'item_lead_b' => 0,
            'item_lead_c' => 0,
            'item_material' => '',
            'subcategory_id' => '',
            'item_weigth' => 0,
            'item_size' => '',
            'item_keywords' => '',
            'item_url' => '',
            'item_meta_title' => '',
            'item_metadescription' => '',
            'item_metakeywords' => '',
            'item_description1' => '',
            'item_description2' => '',
            'item_vector_img' => '',
            'options' => '',
            'note_material' => '',
            'brand' => $brand,
            'printlocat_example_img' => '',
            'sellblank' => 0,
            'sellcolor' => 0,
            'sellcolors' => 0,
            'item_price_id' => -1,
            'item_price_print' => 0.00,
            'item_sale_print' => 0.00,
            'profit_print' => '',
            'item_price_setup' => 0.00,
            'item_sale_setup' => 0.00,
            'profit_setup' => '',
            'profit_print_class' => '',
            'profit_print_perc' => '',
            'profit_setup_class' => '',
            'profit_setup_perc' => '',
            'bullet1' => '',
            'bullet2' => '',
            'bullet3' => '',
            'bullet4' => '',
            'item_minqty' => '',
            'main_image' => '',
            'category_image' => '',
            'top_banner' => '',
            'option_images' => 0,
            'imprint_method' => '',
            'imprint_color' => '',
            'charge_pereach' => '',
            'charge_perorder' => '',
            // Price
            'item_price_id' => -1,
            'item_price_print' => '',
            'item_sale_print' => '',
            'profit_print' => '',
            'item_price_setup' => '',
            'item_sale_setup' => '',
            'profit_setup' => '',
            'profit_print_class' => '',
            'profit_print_perc' => '',
            'profit_setup_class' => '',
            'profit_setup_perc' => '',
            'profit_repeat_class' => '',
            'profit_repeat_perc' => '',
            'item_price_rush1' => '',
            'item_sale_rush1' => '',
            'profit_rush1' => 0,
            'profit_rush1_class' => '',
            'profit_rush1_perc' => '',
            'item_price_rush2' => '',
            'item_sale_rush2' => '',
            'profit_rush2' => '',
            'profit_rush2_class' => '',
            'profit_rush2_perc' => '',
            'item_price_pantone' => '',
            'item_sale_pantone' => '',
            'profit_pantone' => '',
            'profit_pantone_class' => '',
            'profit_pantone_perc' => '',
            'price_discount' => '',
            'price_discount_val' => '',
            'print_discount' => '',
            'print_discount_val' => '',
            'setup_discount' => '',
            'setup_discount_val' => '',
            'repeat_discount' => '',
            'repeat_discount_val' => '',
            'rush1_discount' => '',
            'rush1_discount_val' => '',
            'rush2_discount' => '',
            'rush2_discount_val' => '',
            'pantone_discount' => '',
            'pantone_discount_val' => '',
            'item_price_repeat' => '',
            'item_sale_repeat' => '',
            'profit_repeat' => '',
            'pantone_profit' => '',
        ];
        $vendor = [
            'vendor_id' => '',
            'vendor_name' => '',
            'vendor_zipcode' => '',
            'shipaddr_state' => '',
            'shipaddr_country' => '',
            'po_note' => '',
        ];

        $vitem=[
            'vendor_item_id' => -1,
            'vendor_item_vendor' => '',
            'vendor_item_number' => '',
            'vendor_item_name' => '',
            'vendor_item_blankcost' => '',
            'vendor_item_cost' => '',
            'vendor_item_exprint' => '',
            'vendor_item_setup' => '',
            'vendor_item_repeat' => '',
            'vendor_item_notes' => '',
            'vendor_item_zipcode' => '',
            'printshop_item_id' => '',
            'vendor_name' => '',
            'vendor_zipcode' => '',
            'stand_days' => '',
            'rush1_days' => '',
            'rush1_price' => '',
            'rush2_days' => '',
            'rush2_price' => '',
            'pantone_match' => '',
        ];
        $vprices = [];

        $pricesmax = $this->config->item('relievers_prices_val');

        for ($i=1; $i<=$pricesmax-1; $i++) {
            $vprices[] = [
                'vendorprice_id' => $i*-1,
                'vendor_item_id' => -1,
                'vendorprice_qty' => '',
                'vendorprice_val' => '',
                'vendorprice_color' => '',
            ];
        }
        $images = [];
        $imprints = [];
        $prices = [];
        for ($i=1; $i<=$pricesmax; $i++) {
            $prices[] = [
                'promo_price_id' => $i * (-1),
                'item_id' => -1,
                'item_qty' => '',
                'price' => '',
                'sale_price' => '',
                'profit' => '',
                'show_first' => '0',
                'shipbox' => 0,
                'shipweight' => 0.000,
                'profit_class' => '',
                'profit_perc' => '',
            ];
        }
        $similar = [];
        $maxnum = $this->config->item('relievers_similar_items');
        for ($i=1; $i<=$maxnum; $i++) {
            $similar[] = [
                'item_similar_id' => $i*(-1),
                'item_similar_similar' => '',
                'item_number' => '',
                'item_name' => '',
                'item_template' => '',
            ];
        }
        $colors = [];
        for ($i=0; $i<$this->config->item('item_colors'); $i++) {
            $idx = ($i + 1) * (-1);
            $colors[] = [
                'item_color_id' => $idx,
                'item_color' => '',
            ];
        }
        $shipboxes = [];
        for ($i=0; $i<3; $i++) {
            $shipboxes[] = [
                'item_shipping_id' => (-1)*($i+1),
                'box_qty' => '',
                'box_width' => '',
                'box_length' => '',
                'box_height' => '',
            ];
        }
        $data=[
            'item' => $item,
            'colors' => $colors,
            'vendor' => $vendor,
            'vendor_item' => $vitem,
            'vendor_price' => $vprices,
            'images' => $images,
            'option_images' => [],
            'inprints' => $imprints,
            'prices' => $prices,
            'similar' => $similar,
            'shipboxes' => $shipboxes,
            'deleted' => [],
        ];
        return $data;

    }

    // Item for View / Edit
    public function get_itemlist_details($item_id, $editmode) {
        $out=['result' => $this->error_result, 'msg' => 'Item Not Found'];
        $this->db->select('i.*,sr.category_name, ss.subcategory_name');
        $this->db->from('sb_items i');
        $this->db->join('sr_categories sr', 'sr.category_id=i.category_id');
        $this->db->join('sr_subcategories ss','ss.subcategory_id=i.subcategory_id','left');
        $this->db->where('item_id', $item_id);
        $item = $this->db->get()->row_array();
        if (ifset($item, 'item_id',0)==$item_id) {
            $out['result'] = $this->success_result;
            $this->load->model('itemimages_model');
            $this->load->model('vendors_model');
            $this->load->model('imprints_model');
            $this->load->model('prices_model');
            $this->load->model('similars_model');
            $this->load->model('itemcolors_model');
            $this->load->model('shipping_model');
            // Discounts
            $def_discount = 0;
            $item['price_discount_val'] = $item['print_discount_val'] = $item['setup_discount_val'] = $def_discount;
            $item['repeat_discount_val'] = $item['rush1_discount_val'] = $item['rush2_discount_val'] = $item['pantone_discount_val'] = $def_discount;
            if (!empty($item['price_discount'])) {
                $disc = $this->prices_model->get_discount($item['price_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['price_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            if (!empty($item['print_discount'])) {
                $disc = $this->prices_model->get_discount($item['print_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['print_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            if (!empty($item['setup_discount'])) {
                $disc = $this->prices_model->get_discount($item['setup_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['setup_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            if (!empty($item['repeat_discount'])) {
                $disc = $this->prices_model->get_discount($item['repeat_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['repeat_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            if (!empty($item['rush1_discount'])) {
                $disc = $this->prices_model->get_discount($item['rush1_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['rush1_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            if (!empty($item['rush2_discount'])) {
                $disc = $this->prices_model->get_discount($item['rush2_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['rush2_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            if (!empty($item['pantone_discount'])) {
                $disc = $this->prices_model->get_discount($item['pantone_discount']);
                if ($disc['result']==$this->success_result) {
                    $item['pantone_discount_val'] = $disc['discount']['discount_val'];
                }
            }
            // Colors
            $colorsrc = $this->itemcolors_model->get_colors_item($item_id, $editmode);
            $colors = [];
            $numpp=0;
            foreach ($colorsrc as $itmcolor) {
                $colors[] = [
                    'item_color_id' => $itmcolor['item_color_id'],
                    'item_color' => $itmcolor['item_color'],
                ];
                $numpp++;
            }
            if ($numpp < 9 ) {
                for ($i=$numpp; $i < 9 ; $i++) {
                    $colors[] = [
                        'item_color_id' => $i*(-1),
                        'item_color_itemid' => $item_id,
                        'item_color' => '',
                    ];
                }
            }
            // Vendor Info
            $pricesmax = $this->config->item('relievers_prices_val');
            $vitem = $this->vendors_model->get_item_vendor($item['vendor_item_id']);
            $vprices = [];
            if (ifset($vitem,'vendor_item_id', 0 )==0) {
                $vitem=[
                    'vendor_item_id' => -1,
                    'vendor_item_vendor' => '',
                    'vendor_item_number' => '',
                    'vendor_item_name' => '',
                    'vendor_item_blankcost' => 0,
                    'vendor_item_cost' => 0,
                    'vendor_item_exprint' => 0,
                    'vendor_item_setup' => 0,
                    'vendor_item_repeat' => 0,
                    'vendor_item_notes' => '',
                    'vendor_item_zipcode' => '',
                    'printshop_item_id' => '',
                    'stand_days' => '',
                    'rush1_days' => '',
                    'rush2_days' => '',
                    'rush1_price' => '',
                    'rush2_price' => '',
                    'pantone_match' => '',
                ];
                for ($i=1; $i<=$pricesmax-1; $i++) {
                    $vprices[] = [
                        'vendorprice_id' => $i*-1,
                        'vendor_item_id' => -1,
                        'vendorprice_qty' => '',
                        'vendorprice_val' => '',
                        'vendorprice_color' => '',
                    ];
                }
                $vendor = [
                    'vendor_id' => '',
                    'vendor_name' => '',
                    'vendor_zipcode' => '',
                    'shipaddr_state' => '',
                    'shipaddr_country' => '',
                    'po_note' => '',
                ];
            } else {
                $vdat = $this->vendors_model->get_vendor($vitem['vendor_item_vendor']);
                if ($vdat['result']==$this->error_result) {
                    $vendor = [
                        'vendor_id' => '',
                        'vendor_name' => '',
                        'vendor_zipcode' => '',
                        'shipaddr_state' => '',
                        'shipaddr_country' => '',
                        'po_note' => '',
                    ];
                } else {
                    $vendor = $vdat['data'];
                }
                $results = $this->vendors_model->get_item_vendorprice($item['vendor_item_id']);
                $numpp = 1;
                foreach ($results as $result) {
                    $vprices[] = [
                        'vendorprice_id' => $result['vendorprice_id'],
                        'vendor_item_id' => $result['vendor_item_id'],
                        'vendorprice_qty' => $result['vendorprice_qty'],
                        'vendorprice_val' => $result['vendorprice_val'],
                        'vendorprice_color' => $result['vendorprice_color'],
                    ];
                    $numpp++;
                }
                for ($i=$numpp; $i<=$pricesmax-1; $i++) {
                    $vprices[] = [
                        'vendorprice_id' => $i*-1,
                        'vendor_item_id' => $vitem['vendor_item_id'],
                        'vendorprice_qty' => '',
                        'vendorprice_val' => '',
                        'vendorprice_color' => '',
                    ];
                }
            }
            $imagsrc = $this->itemimages_model->get_itemlist_images($item_id);
            $numpp = 1;
            $images = [];
            foreach ($imagsrc as $image) {
                $title = 'Pic '.$numpp;
                $images[]=[
                    'item_img_id' => $image['item_img_id'],
                    'item_img_item_id' => $image['item_img_item_id'],
                    'item_img_name' => $image['item_img_name'],
                    'item_img_thumb' => $image['item_img_thumb'],
                    'item_img_order' => $image['item_img_order'],
                    'item_img_big' => $image['item_img_big'],
                    'item_img_medium' => $image['item_img_medium'],
                    'item_img_small' => $image['item_img_small'],
                    'item_img_label' => $image['item_img_label'],
                    'title' => $title,
                ];
                $numpp++;
                if ($numpp > $this->config->item('slider_images')) {
                    break;
                }
            }
            // Options images
            if ($item['option_images']==0) {
                $option_images = [];
            } else {
                $option_images = $this->itemimages_model->get_itemoption_images($item_id);
            }
            $imprints = $this->imprints_model->get_imprint_item($item_id);
            $priceres = $this->prices_model->get_itemlist_price($item_id);
            $prices = [];
            $numpp = 1;
            foreach ($priceres as $price) {
                $profitperc = $profitclass = '';
                if (floatval($price['sale_price']) > 0 && $price['profit']!==NULL) {
                    $profitperc = round(($price['profit'] / ($price['sale_price']*$price['item_qty'])) * 100,1);
                    $profitclass = profit_bgclass($profitperc);
                }
                $prices[] = [
                    'promo_price_id' => $price['promo_price_id'],
                    'item_id' => $price['item_id'],
                    'item_qty' => $price['item_qty'],
                    'price' => $price['price'],
                    'sale_price' => $price['sale_price'],
                    'profit' => $price['profit'],
                    'show_first' => $price['show_first'],
                    'shipbox' => $price['shipbox'],
                    'shipweight' => $price['shipweight'],
                    'profit_class' => $profitclass,
                    'profit_perc' => (empty($profitperc) ? $profitperc : $profitperc.'%'),
                ];
                $numpp++;
                if ($numpp > $pricesmax) {
                    break;
                }
            }
            if ($numpp < $pricesmax) {
                $idx = 1;
                for ($i=$numpp; $i<=$pricesmax; $i++) {
                    $prices[] = [
                        'promo_price_id' => $idx * (-1),
                        'item_id' => $item_id,
                        'item_qty' => '',
                        'price' => '',
                        'sale_price' => '',
                        'profit' => '',
                        'show_first' => '0',
                        'shipbox' =>  '',
                        'shipweight' =>  '',
                        'profit_class' =>  '',
                        'profit_perc' =>  '',
                    ];
                    $idx++;
                }
            }
            // Special price - setup, print
            $specprice = $this->prices_model->get_itemlist_specprice($item_id);
            foreach ($specprice as $key => $val) {
                $item[$key] = $val;
            }
            $shipboxes = $this->shipping_model->get_itemshipbox($item_id, $editmode);
            // Simular
            $similar = $this->similars_model->get_similar_items($item_id, $item['brand']);
            // config
            $data=[
                'item' => $item,
                'colors' => $colors,
                'vendor' => $vendor,
                'vendor_item' => $vitem,
                'vendor_price' => $vprices,
                'images' => $images,
                'option_images' => $option_images,
                'inprints' => $imprints,
                'prices' => $prices,
                'similar' => $similar,
                'shipboxes' => $shipboxes,
                'deleted' => [],
            ];
            $out['data'] = $data;
        }
        return $out;
    }

    public function itemdetails_change_category($sessiondata, $postdata, $session) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        if (ifset($postdata,'newval','')=='') {
            $item['category_id'] = NULL;
            $item['item_number'] = '';
            $out['result'] = $this->success_result;
            $out['item_number'] = '';
        } else {
            $this->load->model('categories_model');
            $res = $this->categories_model->get_srcategory_data($postdata['newval']);
            $out['msg'] = $res['msg'];
            if ($res['result']==$this->success_result) {
                $category = $res['data'];
                $item['category_id'] = $category['category_id'];
                $this->db->select('count(item_id) as cnt, max(item_number) as maxnum');
                $this->db->from('sb_items');
                $this->db->where('category_id', $category['category_id']);
                $this->db->where('brand','SR');
                $dat = $this->db->get()->row_array();
                if ($dat['cnt']==0) {
                    $newnumber=$category['category_code'].'001';
                } else {
                    $lastcode = intval(substr($dat['maxnum'],1));
                    $lastcode += 1;
                    $newnumber = $category['category_code'].str_pad($lastcode,3,'0',STR_PAD_LEFT);
                }
                $item['item_number'] = $newnumber;
                $out['result'] = $this->success_result;
                $out['item_number'] = $newnumber;
            }
        }
        if ($out['result']==$this->success_result) {
            $sessiondata['item'] = $item;
            usersession($session, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_iteminfo($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($options, 'fld','unknownfd');
        $newval=ifset($options, 'newval','');
        if (array_key_exists($fldname, $item)) {
            $out['result'] = $this->success_result;
            $item[$fldname] = $newval;
            $sessiondata['item'] = $item;
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_itemcheck($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($options, 'fld','unknownfd');
        if (array_key_exists($fldname, $item)) {
            $out['result'] = $this->success_result;
            $val = $item[$fldname];
            if ($val==0) {
                $newval = 1;
            } else {
                $newval = 0;
            }
            $item[$fldname] = $newval;
            $out['newval'] = $newval;
            $sessiondata['item'] = $item;
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_similar($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $similars = $sessiondata['similar'];
        $fld = ifset($options,'fld','0');
        $newval = ifset($options, 'newval', '');
        $idx=0;
        $found = 0;
        foreach ($similars as $similar) {
            if ($similar['item_similar_id']==$fld) {
                $found = 1;
                break;
            }
            $idx++;
        }
        if ($found == 1) {
            $similars[$idx]['item_similar_similar'] = (empty($newval) ? NULL : $newval);
            $out['result'] = $this->success_result;
            $sessiondata['similar'] = $similars;
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }

    public function itemdetails_change_vendor($sessiondata, $postdata, $sessionsid) {
        $out=['result' => $this->error_result,'msg' => 'Item Not Found'];
        $vendor = $sessiondata['vendor'];
        $vendor_id = ifset($postdata, 'newval', '-1');
        if (empty($vendor_id)) {
            $out['result'] = $this->success_result;
            $vendor=[
                'vendor_id' => '',
                'vendor_name' => '',
                'vendor_zipcode' => '',
                'shipaddr_state' => '',
                'shipaddr_country' => '',
                'po_note' => '',
            ];
        } else {
            $this->load->model('vendors_model');
            $venddat = $this->vendors_model->get_vendor($vendor_id);
            $out['msg'] = $venddat['msg'];
            if ($venddat['result']==$this->success_result) {
                $out['result'] = $this->success_result;
                $data = $venddat['data'];
                $vendor = [
                    'vendor_id' => $data['vendor_id'],
                    'vendor_name' => $data['vendor_name'],
                    'vendor_zipcode' => $data['vendor_zipcode'],
                    'shipaddr_state' => $data['shipaddr_state'],
                    'shipaddr_country' => $data['shipaddr_country'],
                    'po_note' => $data['po_note'],
                ];
            }
        }
        if ($out['result']==$this->success_result) {
            $vendor_item = [
                'vendor_item_id' => -1,
                'vendor_item_vendor' => '',
                'vendor_item_number' => '',
                'vendor_item_name' => '',
                'vendor_item_blankcost' => 0,
                'vendor_item_cost' => 0,
                'vendor_item_exprint' => 0,
                'vendor_item_setup' => 0,
                'vendor_item_repeat' => 0,
                'vendor_item_notes' => '',
                'vendor_item_zipcode' => '',
                'printshop_item_id' => '',
                'stand_days' => '',
                'rush1_days' => '',
                'rush2_days' => '',
                'rush1_price' => '',
                'rush2_price' => '',
                'pantone_match' => '',
            ];
            $vprices = [];
            // if ($brand=='SR') {
            $pricesmax = $this->config->item('relievers_prices_val');
            // } else {
            //    $pricesmax = $this->config->item('prices_val');
            // }
            for ($i=1; $i<=$pricesmax-1; $i++) {
                $vprices[] = [
                    'vendorprice_id' => $i*-1,
                    'vendor_item_id' => -1,
                    'vendorprice_qty' => '',
                    'vendorprice_val' => '',
                    'vendorprice_color' => '',
                ];
            }
            $sessiondata['vendor'] = $vendor;
            $sessiondata['vendor_item'] = $vendor_item;
            $sessiondata['vendor_price'] = $vprices;
            usersession($sessionsid, $sessiondata);

            $commonprice = $this->_prepare_common_prices($sessiondata);
            $prices = $sessiondata['prices'];
            $item = $sessiondata['item'];
            $vendor_prices = $sessiondata['vendor_price'];
            $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
            $this->_update_profit($profits, $item, $prices, $sessiondata, $sessionsid);
            // Recalc prices and
            $data=[
                'vendor' => $vendor,
                'vendor_item' => $vendor_item,
                'vendor_price' => $vprices,
            ];
            $out['data'] = $data;
        }
        return $out;
    }

    public function itemdetails_check_vendoritem($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Item Not Found'];
        $this->load->model('vendors_model');
        $vendor_item = $sessiondata['vendor_item'];
        $vendor = $sessiondata['vendor'];
        $vendor_id = $vendor['vendor_id'];
        $vendor_itemnum = ifset($postdata, 'newval','');
        $newvendprice = 0;
        if (empty($vendor_itemnum)) {
            $vendor_item = [
                'vendor_item_id' => -1,
                'vendor_item_vendor' =>'',
                'vendor_item_number' => '',
                'vendor_item_name' => '',
                'vendor_item_blankcost' => 0,
                'vendor_item_cost' => '',
                'vendor_item_exprint' => 0,
                'vendor_item_setup' => 0,
                'vendor_item_repeat' => 0,
                'vendor_item_notes' => '',
                'vendor_item_zipcode' => '',
                'printshop_item_id' => '',
                'stand_days' => '',
                'rush1_days' => '',
                'rush2_days' => '',
                'rush1_price' => '',
                'rush2_price' => '',
                'pantone_match' => '',
            ];
            $newvendprice = 1;
        } else {
            // Get data about vendor item
            $vitem = $this->vendors_model->search_vendor_items($vendor_itemnum, $vendor_id);
            if (count($vitem)==0) {
                // New item
                $vendor_item = [
                    'vendor_item_id' => -1,
                    'vendor_item_vendor' =>'',
                    'vendor_item_number' => $vendor_itemnum,
                    'vendor_item_name' => '',
                    'vendor_item_blankcost' => 0,
                    'vendor_item_cost' => '',
                    'vendor_item_exprint' => 0,
                    'vendor_item_setup' => 0,
                    'vendor_item_repeat' => 0,
                    'vendor_item_notes' => '',
                    'vendor_item_zipcode' => '',
                    'printshop_item_id' => '',
                    'stand_days' => '',
                    'rush1_days' => '',
                    'rush2_days' => '',
                    'rush1_price' => '',
                    'rush2_price' => '',
                    'pantone_match' => '',
                ];
                $newvendprice = 1;
            } else {
                // First find
                $vendor_item_id = $vitem[0]['id'];
                $vendor_item = $this->vendors_model->get_item_vendor($vendor_item_id);
                unset($vendor_item['vendor_name']);
                unset($vendor_item['vendor_zipcode']);
                if ($vendor_id!==$vendor_item['vendor_item_vendor']) {
                    $vdat = $this->vendors_model->get_vendor($vendor_item['vendor_item_vendor']);
                    $out['msg'] = $vdat['msg'];
                    if ($vdat['result']==$this->success_result) {
                        $vendor = $vdat['dat'];
                    }
                }
            }
        }
        $pricesmax = $this->config->item('relievers_prices_val');
        $vendor_prices = [];
        if ($newvendprice==1) {
            for ($i=1; $i<=$pricesmax-1; $i++) {
                $vendor_prices[] = [
                    'vendorprice_id' => $i*-1,
                    'vendor_item_id' => -1,
                    'vendorprice_qty' => '',
                    'vendorprice_val' => '',
                    'vendorprice_color' => '',
                ];
            }
        } else {
            // $vendor_prices =
            $results = $this->vendors_model->get_item_vendorprice($vendor_item['vendor_item_id']);
            $numpp = 1;
            foreach ($results as $result) {
                $vendor_prices[] = [
                    'vendorprice_id' => $result['vendorprice_id'],
                    'vendor_item_id' => $result['vendor_item_id'],
                    'vendorprice_qty' => $result['vendorprice_qty'],
                    'vendorprice_val' => $result['vendorprice_val'],
                    'vendorprice_color' => $result['vendorprice_color'],
                ];
                $numpp++;
            }
            for ($i=$numpp; $i<=$pricesmax-1; $i++) {
                $vendor_prices[] = [
                    'vendorprice_id' => $i*-1,
                    'vendor_item_id' => $vendor_item['vendor_item_id'],
                    'vendorprice_qty' => '',
                    'vendorprice_val' => '',
                    'vendorprice_color' => '',
                ];
            }
        }
        // Save data to session
        $sessiondata['vendor'] = $vendor;
        $sessiondata['vendor_item'] = $vendor_item;
        $sessiondata['vendor_price'] = $vendor_prices;
        usersession($session, $sessiondata);
        $out['result'] = $this->success_result;
        // Add base price
        $commonprice = $this->_prepare_common_prices($sessiondata);
        $prices = $sessiondata['prices'];
        $item = $sessiondata['item'];
        $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
        $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
        return $out;
    }

    public function itemdetails_save_addimages($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['images'];
        $neword = 0;
        foreach ($images as $image) {
            if ($neword < $image['item_img_order']) {
                $neword = $image['item_img_order'];
            }
        }
        $neword+=1;
        $newid = (count($images)+1) * (-1);
        $images[] = [
            'item_img_id' => $newid,
            'item_img_name' => $postdata['newval'],
            'item_img_label' => '',
            'item_img_order' => $neword,
        ];
        $sessiondata['images'] = $images;
        usersession($session, $sessiondata);
        $out['result'] = $this->success_result;
        return $out;
    }

    public function itemdetails_save_updimages($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = str_replace('replimg', '',$imgidx);
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_name'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_imagessort($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = intval($imgidx);
            $numpp = ifset($postdata, 'newval', '');
            if (!empty($numpp)) {
                $numpp = intval($numpp);
                $numord = 1;
                $newimg = [];
                $arrimg = [];
                for ($i=0; $i<count($images); $i++) {
                    foreach ($images as $image) {
                        if (!in_array($image['item_img_id'], $arrimg)) {
                            if ($numord==$numpp) {
                                array_push($arrimg, $imgidx);
                                $numord++;
                                break;
                            } else {
                                if ($image['item_img_id']!=$imgidx) {
                                    array_push($arrimg, $image['item_img_id']);
                                    $numord++;
                                    break;
                                }
                            }
                        }
                    }
                }
                $numord=1;
                foreach ($arrimg as $keyval) {
                    foreach ($images as $image) {
                        if ($image['item_img_id']==$keyval) {
                            $image['item_img_order'] = $numord;
                            $newimg[] = $image;
                            $numord++;
                        }
                    }
                }
                $sessiondata['images'] = $newimg;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function itemdetails_addimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['images'];
        $deleted = $sessiondata['deleted'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            $numrow = 1;
            $newimg = [];
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    if ($imgidx > 0) {
                        $deleted[] = [
                            'entity' => 'images',
                            'id' => $imgidx,
                        ];
                    }
                } else {
                    $image['item_img_order'] = $numrow;
                    $newimg[] = $image;
                    $numrow++;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['images'] = $newimg;
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_addimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_label'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_optionimages_add($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['option_images'];
        $neword = 0;
        foreach ($images as $image) {
            if ($neword < $image['item_img_order']) {
                $neword = $image['item_img_order'];
            }
        }
        $neword+=1;
        $newid = (count($images)+1) * (-1);
        $images[] = [
            'item_img_id' => $newid,
            'item_img_name' => $postdata['newval'],
            'item_img_label' => '',
            'item_img_order' => $neword,
        ];
        $sessiondata['option_images'] = $images;
        usersession($session, $sessiondata);
        $out['result'] = $this->success_result;
        return $out;
    }

    public function itemdetails_optionimages_update($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['option_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = str_replace('reploptimg', '',$imgidx);
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_name'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['option_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_optimagessort($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['option_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = intval($imgidx);
            $numpp = ifset($postdata, 'newval', '');
            if (!empty($numpp)) {
                $numpp = intval($numpp);
                $numord = 1;
                $newimg = [];
                $arrimg = [];
                for ($i=0; $i<count($images); $i++) {
                    foreach ($images as $image) {
                        if (!in_array($image['item_img_id'], $arrimg)) {
                            if ($numord==$numpp) {
                                array_push($arrimg, $imgidx);
                                $numord++;
                                break;
                            } else {
                                if ($image['item_img_id']!=$imgidx) {
                                    array_push($arrimg, $image['item_img_id']);
                                    $numord++;
                                    break;
                                }
                            }
                        }
                    }
                }
                $numord=1;
                foreach ($arrimg as $keyval) {
                    foreach ($images as $image) {
                        if ($image['item_img_id']==$keyval) {
                            $image['item_img_order'] = $numord;
                            $newimg[] = $image;
                            $numord++;
                        }
                    }
                }
                $sessiondata['option_images'] = $newimg;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;

    }

    public function itemdetails_optimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['option_images'];
        $deleted = $sessiondata['deleted'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            $numrow = 1;
            $newimg = [];
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    if ($imgidx > 0) {
                        $deleted[] = [
                            'entity' => 'images',
                            'id' => $imgidx,
                        ];
                    }
                } else {
                    $image['item_img_order'] = $numrow;
                    $newimg[] = $image;
                    $numrow++;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['option_images'] = $newimg;
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_optimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['option_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_label'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['option_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_vendor_price($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $vendor_prices = $sessiondata['vendor_price'];
        $priceidx = ifset($postdata,'priceidx','');
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($priceidx) && !empty($fldname)) {
            $find = 0;
            $idx=0;
            foreach ($vendor_prices as $vendor_price) {
                if ($vendor_price['vendorprice_id']==$priceidx) {
                    $vendor_prices[$idx][$fldname] = ifset($postdata,'newval','');
                    $find=1;
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['vendor_price'] = $vendor_prices;
                usersession($session, $sessiondata);
                // Add base price
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $prices = $sessiondata['prices'];
                $item = $sessiondata['item'];
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
        }
        return $out;
    }

    public function itemdetails_vendoritem_price($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $vendoritem = $sessiondata['vendor_item'];
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($fldname) && array_key_exists($fldname,$vendoritem)) {
            $vendoritem[$fldname] = ifset($postdata,'newval','');
            $out['result'] = $this->success_result;
            $sessiondata['vendor_item'] = $vendoritem;
            usersession($session, $sessiondata);
            // Add base price
            $commonprice = $this->_prepare_common_prices($sessiondata);
            $prices = $sessiondata['prices'];
            $item = $sessiondata['item'];
            $vendor_prices = $sessiondata['vendor_price'];
            $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
            $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
        }
        return $out;
    }

    public function itemdetails_price_discount($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($fldname) && array_key_exists($fldname, $item)) {
            $fldval = $fldname.'_val';
            if (empty($postdata['newval'])) {
                $item[$fldname] = '';
                $item[$fldval] = '';
                $out['result'] = $this->success_result;
            } else {
                $this->load->model('prices_model');
                $res = $this->prices_model->get_discount($postdata['newval']);
                $out['msg'] = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $out['result'] = $this->success_result;
                    $discount = $res['discount'];
                    $item[$fldval] = $discount['discount_val'];
                    $item[$fldname] = $discount['price_discount_id'];
                }
            }
            if ($out['result']==$this->success_result) {
                // Recount prices
                $prices = $sessiondata['prices'];
                $recount = $this->_recalc_prices($item, $prices);
                $item = $recount['item'];
                $prices = $recount['prices'];
                $sessiondata['item'] = $item;
                $sessiondata['prices'] = $prices;
                usersession($session, $sessiondata);
                $vendor_prices = $sessiondata['vendor_price'];
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
        }
        return $out;
    }

    public function itemdetails_item_price($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $prices = $sessiondata['prices'];
        $fldname = ifset($postdata,'fld','');
        $priceidx = ifset($postdata, 'priceidx', '');
        if (!empty($fldname) && !empty($priceidx)) {
            $idx = 0;
            $find = 0;
            foreach ($prices as $price) {
                if ($price['promo_price_id']==$priceidx) {
                    $find = 1;
                    $prices[$idx][$fldname] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $item = $sessiondata['item'];
                $recount = $this->_recalc_prices($item, $prices);
                $item = $recount['item'];
                $prices = $recount['prices'];
                $sessiondata['item'] = $item;
                $sessiondata['prices'] = $prices;
                usersession($session, $sessiondata);
                $vendor_prices = $sessiondata['vendor_price'];
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
        }
        return $out;
    }

    public function itemdetails_item_priceval($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($postdata,'fld','');
        if (!empty($fldname) && array_key_exists($fldname, $item)) {
            $out['result'] = $this->success_result;
            $item[$fldname] = $postdata['newval'];
            $prices = $sessiondata['prices'];
            $recount = $this->_recalc_prices($item, $prices);
            $item = $recount['item'];
            $prices = $recount['prices'];
            $sessiondata['item'] = $item;
            $sessiondata['prices'] = $prices;
            usersession($session, $sessiondata);
            $vendor_prices = $sessiondata['vendor_price'];
            $commonprice = $this->_prepare_common_prices($sessiondata);
            $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
            $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
        }
        return $out;
    }

    public function itemdetails_shipbox($sessiondata, $postdata, $session) {
        $out=['result'=>$this->error_result, 'msg'=>'Info Not Found'];
        $shipboxes = $sessiondata['shipboxes'];
        $fldname = ifset($postdata,'fld','');
        $shipidx = ifset($postdata, 'shipidx', '');
        if (!empty($fldname) && !empty($shipidx)) {
            $idx = 0;
            $find = 0;
            foreach ($shipboxes as $shipbox) {
                if ($shipbox['item_shipping_id']==$shipidx) {
                    $find = 1;
                    $shipboxes[$idx][$fldname] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['shipboxes'] = $shipboxes;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_printloc_add($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info not found'];
        $inprints = $sessiondata['inprints'];
        $newidx = count($inprints) + 1;
        $inprints[] = [
            'item_inprint_id' => (-1)*$newidx,
            'item_inprint_location' => '',
            'item_inprint_size' => '',
            'item_inprint_view' => '',
            'item_imprint_mostpopular' => 0,
        ];
        $out['result'] = $this->success_result;
        $sessiondata['inprints'] = $inprints;
        usersession($session, $sessiondata);
        $out['inprints'] = $inprints;
        return $out;
    }

    public function itemdetails_printloc_edit($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info not found'];
        $inprints = $sessiondata['inprints'];
        $fldidx = ifset($postdata,'fldidx', '');
        $fld = ifset($postdata, 'fld','');
        if (!empty($fldidx) && !empty($fld)) {
            $idx = 0;
            $find = 0;
            foreach ($inprints as $inprint) {
                if ($inprint['item_inprint_id']==$fldidx) {
                    $inprints[$idx][$fld] = $postdata['newval'];
                    $find = 1;
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $sessiondata['inprints']=$inprints;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
                $out['inprints'] = $inprints;
            }
        }
        return $out;
    }

    public function itemdetails_printloc_view($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info not found'];
        $inprints = $sessiondata['inprints'];
        $fldidx = ifset($postdata,'fldidx', '');
        $operation = ifset($postdata,'operation','');
        if (!empty($fldidx) && !empty($operation)) {
            $idx = 0;
            $find = 0;
            foreach ($inprints as $inprint) {
                if ($inprint['item_inprint_id']==$fldidx) {
                    if ($operation=='add') {
                        $inprints[$idx]['item_inprint_view'] = $postdata['newval'];
                    } else {
                        $inprints[$idx]['item_inprint_view'] = '';
                    }
                    $find = 1;
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $sessiondata['inprints'] = $inprints;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
                $out['inprints'] = $inprints;
            }
        }
        return $out;
    }

    public function itemdetails_printloc_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info not found'];
        $inprints = $sessiondata['inprints'];
        $deleted = $sessiondata['deleted'];
        $fldidx = ifset($postdata,'fldidx', '');
        if (!empty($fldidx)) {
            $newimpr = [];
            $find = 0;
            foreach ($inprints as $inprint) {
                if ($inprint['item_inprint_id']==$fldidx) {
                    $find=1;
                } else {
                    $newimpr[] = $inprint;
                }
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['inprints'] = $newimpr;
                if ($fldidx > 0 ) {
                    $deleted[] = [
                        'entity' => 'inprints',
                        'id' => $fldidx,
                    ];
                }
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
                $out['inprints'] = $newimpr;
            }
        }
        return $out;
    }

    public function itemdetails_vectorfile($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg'=> 'Info not found'];
        $item=$sessiondata['item'];
        $operation = ifset($postdata,'operation', '');
        if (!empty($operation)) {
            $vectorlnk = '';
            if ($operation=='add') {
                $item['item_vector_img'] = $postdata['newval'];
                $vectorlnk = $postdata['newval'];
            } else {
                $item['item_vector_img'] = '';
            }
            $out['result'] = $this->success_result;
            $out['vector'] = $vectorlnk;
        }
        return $out;
    }

    public function itemdetails_save($sessiondata, $session, $user_id) {
        $out=['result' => $this->error_result, 'msg' => 'Info not found'];
        $reschk = $this->_check_itemdetails($sessiondata);
        if ($reschk['result']==$this->error_result) {
            $errmsg = $reschk['errmsg'];
            $msg = '';
            foreach ($errmsg as $row) {
                $msg.=$row.PHP_EOL;
                $out['msg'] = $msg;
            }
        } else {
            // Lets go to save
            $item = $sessiondata['item'];
            $this->db->set('item_name', $item['item_name']);
            $this->db->set('item_active', $item['item_active']);
            $this->db->set('item_new', $item['item_new']);
            $this->db->set('item_template', $item['item_template']);
            $this->db->set('item_material', $item['item_material']);
            $this->db->set('item_weigth', floatval($item['item_weigth']));
            $this->db->set('item_size', $item['item_size']);
            $this->db->set('item_lead_a', $item['item_lead_a']);
            $this->db->set('item_lead_b', $item['item_lead_b']);
            $this->db->set('item_lead_c', $item['item_lead_c']);
            $this->db->set('item_keywords', $item['item_keywords']);
            $this->db->set('item_url', $item['item_url']);
            $this->db->set('item_meta_title', $item['item_meta_title']);
            $this->db->set('item_metadescription', $item['item_metadescription']);
            $this->db->set('item_metakeywords', $item['item_metakeywords']);
            $this->db->set('item_description1', $item['item_description1']);
            $this->db->set('item_description2', $item['item_description2']);
            $this->db->set('note_material', $item['note_material']);
            $this->db->set('imprint_method', $item['imprint_method']);
            $this->db->set('imprint_color', $item['imprint_color']);
            $this->db->set('options', $item['options']);
            $this->db->set('option_images', $item['option_images']);
            $this->db->set('charge_pereach', floatval($item['charge_pereach']));
            $this->db->set('charge_perorder', floatval($item['charge_perorder']));
            $this->db->set('subcategory_id', empty($item['subcategory_id']) ? NULL : $item['subcategory_id']);
            $this->db->set('item_sale', $item['item_sale']);
            $this->db->set('item_topsale', $item['item_topsale']);
            $this->db->set('item_minqty', empty($item['item_minqty']) ? NULL : intval($item['item_minqty']));
            $this->db->set('bullet1', empty($item['bullet1']) ? NULL : $item['bullet1']);
            $this->db->set('bullet2', empty($item['bullet2']) ? NULL : $item['bullet2']);
            $this->db->set('bullet3', empty($item['bullet3']) ? NULL : $item['bullet3']);
            $this->db->set('bullet4', empty($item['bullet4']) ? NULL : $item['bullet4']);
            $this->db->set('price_discount', empty($item['price_discount'])? null: $item['price_discount']);
            $this->db->set('print_discount', empty($item['print_discount'])? null: $item['print_discount']);
            $this->db->set('setup_discount', empty($item['setup_discount'])? null: $item['setup_discount']);
            $this->db->set('repeat_discount', empty($item['repeat_discount'])? null: $item['repeat_discount']);
            $this->db->set('rush1_discount', empty($item['rush1_discount'])? null : $item['rush1_discount']);
            $this->db->set('rush2_discount', empty($item['rush2_discount'])? null : $item['rush2_discount']);
            $this->db->set('pantone_discount', empty($item['pantone_discount'])? null : $item['pantone_discount']);
            if ($item['item_id']>0) {
                $this->db->set('update_user', $user_id);
                $this->db->where('item_id', $item['item_id']);
                $this->db->update('sb_items');
                $item_id = $item['item_id'];
                $out['result'] = $this->success_result;
            } else {
                $this->db->set('create_time', date('Y-m-d H:i:s'));
                $this->db->set('create_user', $user_id);
                $this->db->set('update_user', $user_id);
                $this->db->set('brand','SR');
                $this->db->insert('sb_items');
                $item_id = $this->db->insert_id();
                if ($item_id > 0) {
                    $out['result'] = $this->success_result;
                    // Check code
                    $category_id = $item['category_id'];
                    $this->load->model('categories_model');
                    $catdat = $this->categories_model->get_srcategory_data($category_id);
                    $category = $catdat['data'];
                    // New Item #
                    $this->db->select('count(item_id) as cnt, max(item_number) as maxnum');
                    $this->db->from('sb_items');
                    $this->db->where('category_id', $category['category_id']);
                    $this->db->where('brand', 'SR');
                    $this->db->where('item_id != ', $item_id);
                    $dat = $this->db->get()->row_array();
                    if ($dat['cnt'] == 0) {
                        $newnumber = $category['category_code'] . '001';
                    } else {
                        $lastcode = intval(substr($dat['maxnum'], 1));
                        $lastcode += 1;
                        $newnumber = $category['category_code'] . str_pad($lastcode, 3, '0', STR_PAD_LEFT);
                    }
                    $this->db->set('category_id', $item['category_id']);
                    $this->db->set('item_number', $newnumber);
                    $this->db->where('item_id', $item_id);
                    $this->db->update('sb_items');
                } else {
                    $out['msg'] = 'Error During insert Item Data';
                    return $out;
                }
            }
            // Set prices
            $this->db->set('item_price_print', $item['item_price_print']);
            $this->db->set('item_sale_print', $item['item_sale_print']);
            $this->db->set('profit_print', $item['profit_print']);
            $this->db->set('item_price_setup', $item['item_price_setup']);
            $this->db->set('item_sale_setup', $item['item_sale_setup']);
            $this->db->set('profit_setup', $item['profit_setup']);
            $this->db->set('item_price_repeat', $item['item_price_repeat']);
            $this->db->set('item_sale_repeat', $item['item_sale_repeat']);
            $this->db->set('profit_repeat', $item['profit_repeat']);
            $this->db->set('item_price_rush1', $item['item_price_rush1']);
            $this->db->set('item_sale_rush1', $item['item_sale_rush1']);
            $this->db->set('rush1_profit', $item['profit_rush1']);
            $this->db->set('item_price_rush2', $item['item_price_rush2']);
            $this->db->set('item_sale_rush2', $item['item_sale_rush2']);
            $this->db->set('rush2_profit', $item['profit_rush2']);
            $this->db->set('item_price_pantone', $item['item_price_pantone']);
            $this->db->set('item_sale_pantone', $item['item_sale_pantone']);
            $this->db->set('pantone_profit', $item['profit_pantone']);
            // $this->db->set('')
            if ($item['item_price_id'] > 0) {
                $this->db->where('item_price_id', $item['item_price_id']);
                $this->db->update('sb_item_prices');
            } else {
                $this->db->set('item_price_itemid', $item_id);
                $this->db->insert('sb_item_prices');
            }
            // Pictures
            $preload_sh = $this->config->item('pathpreload');
            $preload_fl = $this->config->item('upload_path_preload');
            $itemimg_sh = $this->config->item('itemimages_relative').$item_id.'/';
            $itemimg_fl = $this->config->item('itemimages').$item_id.'/';
            // Create Path to new Images
            if (createPath($itemimg_sh)) {
                if (stripos($item['main_image'], $preload_sh)!==FALSE) {
                    $mainimg_src = str_replace($preload_sh,'', $item['main_image']);
                    $cpres = @copy($preload_fl.$mainimg_src, $itemimg_fl.$mainimg_src);
                    if ($cpres) {
                        $this->db->set('main_image', $itemimg_sh.$mainimg_src);
                        $this->db->where('item_id', $item_id);
                        $this->db->update('sb_items');
                    }
                }
                if (stripos($item['category_image'], $preload_sh)!==FALSE) {
                    $categimg_src = str_replace($preload_sh, '', $item['category_image']);
                    $cpres = @copy($preload_fl.$categimg_src, $itemimg_fl.$categimg_src);
                    if ($cpres) {
                        $this->db->set('category_image', $itemimg_sh.$categimg_src);
                        $this->db->where('item_id', $item_id);
                        $this->db->update('sb_items');
                    }
                }
                if (stripos($item['top_banner'], $preload_sh)!==FALSE) {
                    $topbanner_src = str_replace($preload_sh, '', $item['top_banner']);
                    $cpres = @copy($preload_fl.$topbanner_src, $itemimg_fl.$topbanner_src);
                    if ($cpres) {
                        $this->db->set('top_banner', $itemimg_sh.$topbanner_src);
                        $this->db->where('item_id', $item_id);
                        $this->db->update('sb_items');
                    }
                }
            }
            // Add Images
            $images = $sessiondata['images'];
            foreach ($images as $image) {
                $numpp=1;
                if (stripos($image['item_img_name'], $preload_sh)!==FALSE) {
                    $img_src = str_replace($preload_sh, '', $image['item_img_name']);
                    $cpres = @copy($preload_fl.$img_src, $itemimg_fl.$img_src);
                    if ($cpres) {
                        $image['item_img_name'] = $itemimg_sh.$img_src;
                    } else {
                        $image['item_img_name'] = '';
                    }
                }
                if (empty($image['item_img_name']) && $image['item_img_id'] > 0) {
                    $this->db->where('item_img_id', $image['item_img_id']);
                    $this->db->delete('sb_item_images');
                } else {
                    $this->db->set('item_img_name', $image['item_img_name']);
                    $this->db->set('item_img_label', $image['item_img_label']);
                    $this->db->set('item_img_order', $numpp);
                    if ($image['item_img_id'] > 0) {
                        $this->db->where('item_img_id', $image['item_img_id']);
                        $this->db->update('sb_item_images');
                    } else {
                        $this->db->set('item_img_item_id', $item_id);
                        $this->db->insert('sb_item_images');
                    }
                    $numpp++;
                }
            }
            // Option images
            if ($item['option_images']==0) {
                // Delete old images
                $this->db->where('item_id', $item_id);
                $this->db->delete('sb_itemoption_images');
            } else {
                $optionimages = $sessiondata['option_images'];
                foreach ($optionimages as $optionimage) {
                    if (stripos($optionimage['item_img_name'], $preload_sh)!==FALSE) {
                        $optimg_src = str_replace($preload_sh, '', $optionimage['item_img_name']);
                        $cpres = @copy($preload_fl.$optimg_src, $itemimg_fl.$optimg_src);
                        if ($cpres) {
                            $optionimage['item_img_name'] = $itemimg_sh.$optimg_src;
                        } else {
                            $optionimage['item_img_name'] = NULL;
                        }
                    }
                    if (!empty($optionimage['item_img_name'])) {
                        $this->db->set('image_url', $optionimage['item_img_name']);
                        $this->db->set('image_label', $optionimage['item_img_label']);
                        $this->db->set('image_order', $optionimage['item_img_order']);
                        if ($optionimage['item_img_id'] > 0) {
                            $this->db->where('itemoption_image_id', $optionimage['item_img_id']);
                            $this->db->update('sb_itemoption_images');
                        } else {
                            $this->db->set('item_id', $item_id);
                            $this->db->insert('sb_itemoption_images');
                        }
                    }
                }
            }
            if (empty($item['item_vector_img'])) {
                $this->db->where('item_id', $item_id);
                $this->db->set('item_vector_img', null);
                $this->db->update('sb_items');
            } else {
                $vector_fl = $this->config->item('item_aitemplate');
                $vector_sh = $this->config->item('item_aitemplate_relative');
                if (stripos($item['item_vector_img'], $preload_sh)!==false) {
                    $vectorimg = str_replace($preload_sh,'', $item['item_vector_img']);
                    $cpres = @copy($preload_fl.$vectorimg, $vector_fl.$vectorimg);
                    if ($cpres) {
                        $this->db->where('item_id', $item_id);
                        $this->db->set('item_vector_img', $vector_sh.$vectorimg);
                        $this->db->update('sb_items');
                    }
                }
            }

            // Similar
            $similars = $sessiondata['similar'];
            foreach ($similars as $similar) {
                if (!empty($similar['item_similar_similar'])) {
                    $this->db->set('item_similar_similar', $similar['item_similar_similar']);
                    if ($similar['item_similar_id'] > 0) {
                        $this->db->where('item_similar_id', $similar['item_similar_id']);
                        $this->db->update('sb_item_similars');
                    } else {
                        $this->db->set('item_similar_item', $item_id);
                        $this->db->insert('sb_item_similars');
                    }
                } else {
                    if ($similar['item_similar_id'] > 0) {
                        $this->db->where('item_similar_id', $similar['item_similar_id']);
                        $this->db->delete('sb_item_similars');
                    }
                }
            }
            // Vendor Item
            $vendor = $sessiondata['vendor'];
            $vendor_item = $sessiondata['vendor_item'];
            $vendor_prices = $sessiondata['vendor_price'];
            if ($vendor['vendor_id'] > 0) {
                if (!empty($vendor_item['vendor_item_number'])) {
                    $this->db->set('vendor_item_number', $vendor_item['vendor_item_number']);
                    $this->db->set('vendor_item_name', $vendor_item['vendor_item_name']);
                    $this->db->set('vendor_item_blankcost', $vendor_item['vendor_item_blankcost']);
                    $this->db->set('vendor_item_cost', $vendor_item['vendor_item_cost']);
                    $this->db->set('vendor_item_exprint', $vendor_item['vendor_item_exprint']);
                    $this->db->set('vendor_item_setup', $vendor_item['vendor_item_setup']);
                    $this->db->set('vendor_item_repeat', $vendor_item['vendor_item_repeat']);
                    // Other items
                    $this->db->set('stand_days',$vendor_item['stand_days']);
                    $this->db->set('rush1_days', $vendor_item['rush1_days']);
                    $this->db->set('rush2_days', $vendor_item['rush2_days']);
                    $this->db->set('rush1_price', $vendor_item['rush1_price']);
                    $this->db->set('rush2_price', $vendor_item['rush2_price']);
                    $this->db->set('pantone_match', $vendor_item['pantone_match']);
                    if ($vendor_item['vendor_item_id'] > 0) {
                        $this->db->where('vendor_item_id', $vendor_item['vendor_item_id']);
                        $this->db->update('sb_vendor_items');
                        $vendor_item_id = $vendor_item['vendor_item_id'];
                    } else {
                        $this->db->set('vendor_item_vendor', $vendor['vendor_id']);
                        $this->db->insert('sb_vendor_items');
                        $vendor_item_id = $this->db->insert_id();
                    }
                    $this->db->set('vendor_item_id', $vendor_item_id);
                    $this->db->where('item_id', $item_id);
                    $this->db->update('sb_items');
                    // Vendor Prices
                    foreach ($vendor_prices as $vendor_price) {
                        if (!empty($vendor_price['vendorprice_qty'])) {
                            $this->db->set('vendorprice_qty', $vendor_price['vendorprice_qty']);
                            $this->db->set('vendorprice_val', $vendor_price['vendorprice_val']);
                            $this->db->set('vendorprice_color', $vendor_price['vendorprice_color']);
                            if ($vendor_price['vendorprice_id'] > 0) {
                                $this->db->where('vendorprice_id', $vendor_price['vendorprice_id']);
                                $this->db->update('sb_vendor_prices');
                            } else {
                                $this->db->set('vendor_item_id', $vendor_item_id);
                                $this->db->insert('sb_vendor_prices');
                            }
                        } else {
                            if ($vendor_price['vendorprice_id'] > 0) {
                                $this->db->where('vendorprice_id', $vendor_price['vendorprice_id']);
                                $this->db->delete('sb_vendor_prices');
                            }
                        }
                    }
                }
            }
            // Prices
            $prices = $sessiondata['prices'];
            foreach ($prices as $price) {
                if (!empty($price['item_qty'])) {
                    $this->db->set('item_qty', $price['item_qty']);
                    $this->db->set('price', $price['price']);
                    $this->db->set('sale_price', $price['sale_price']);
                    $this->db->set('profit', $price['profit']);
                    if ($price['promo_price_id'] > 0) {
                        $this->db->where('promo_price_id', $price['promo_price_id']);
                        $this->db->update('sb_promo_price');
                    } else {
                        $this->db->set('item_id', $item_id);
                        $this->db->insert('sb_promo_price');
                    }
                }
            }
            // Print Locations
            $inprints = $sessiondata['inprints'];
            $imprint_fl = $this->config->item('imprintimages');
            $imprint_sh = $this->config->item('imprintimages_relative');
            foreach ($inprints as $inprint) {
                if (stripos($inprint['item_inprint_view'], $preload_sh)!==false) {
                    $imprimg = str_replace($preload_sh,'', $inprint['item_inprint_view']);
                    $cpres = @copy($preload_fl.$imprimg, $imprint_fl.$imprimg);
                    if ($cpres) {
                        $inprint['item_inprint_view'] = $imprint_sh.$imprimg;
                    } else {
                        $inprint['item_inprint_view'] = '';
                    }
                }
                $this->db->set('item_inprint_location', htmlspecialchars_decode($inprint['item_inprint_location']));
                $this->db->set('item_inprint_size', htmlspecialchars_decode($inprint['item_inprint_size']));
                $this->db->set('item_inprint_view', $inprint['item_inprint_view']);
                if ($inprint['item_inprint_id'] > 0) {
                    $this->db->where('item_inprint_id', $inprint['item_inprint_id']);
                    $this->db->update('sb_item_inprints');
                } else {
                    $this->db->set('item_inprint_item', $item_id);
                    $this->db->insert('sb_item_inprints');
                }
            }
            // Shipboxes
            $shipboxes = $sessiondata['shipboxes'];
            foreach ($shipboxes as $shipbox) {
                if (intval($shipbox['box_qty'])>0) {
                    $this->db->set('box_qty', $shipbox['box_qty']);
                    $this->db->set('box_width', $shipbox['box_width']);
                    $this->db->set('box_length', $shipbox['box_length']);
                    $this->db->set('box_height', $shipbox['box_height']);
                    if ($shipbox['item_shipping_id'] > 0) {
                        $this->db->where('item_shipping_id', $shipbox['item_shipping_id']);
                        $this->db->update('sb_item_shipping');
                    } else {
                        $this->db->set('item_id', $item_id);
                        $this->db->insert('sb_item_shipping');
                    }
                } else {
                    if ($shipbox['item_shipping_id'] > 0) {
                        $this->db->where('item_shipping_id', $shipbox['item_shipping_id']);
                        $this->db->delete('sb_item_shipping');
                    }
                }
            }
            $deletes = $sessiondata['deleted'];
            foreach ($deletes as $deleterow) {
                if ($deleterow['entity']=='images') {
                    $this->db->where('item_img_id', $deleterow['id']);
                    $this->db->delete('sb_item_images');
                } elseif ($deleterow['entity']=='inprints') {
                    $this->db->where('item_inprint_id', $deleterow['id']);
                    $this->db->delete('sb_item_inprints');
                }
            }
        }
        return $out;
    }

    private function _recalc_prices($item, $prices) {
        // Item Prices
        if (empty($item['price_discount'])) {
            $idx = 0;
            foreach ($prices as $price) {
                $prices[$idx]['sale_price'] = '';
                $idx++;
            }
        } else {
            $idx = 0;
            foreach ($prices as $price) {
                if ($price['price']!='') {
                    $prices[$idx]['sale_price'] = round($price['price']*(100-$item['price_discount_val'])/100,3);
                }
                $idx++;
            }
        }
        // Print
        if (empty($item['print_discount'])) {
            $item['item_sale_print'] = '';
        } else {
            if ($item['item_price_print']=='') {
                $item['item_sale_print'] = '';
            } else {
                $item['item_sale_print'] = round($item['item_price_print']*(100-$item['print_discount_val'])/100, 3);
            }
        }
        // Setup
        if (empty($item['setup_discount'])) {
            $item['item_sale_setup'] = '';
        } else {
            if ($item['item_price_setup']=='') {
                $item['item_sale_setup'] = '';
            } else {
                $item['item_sale_setup'] = round(floatval($item['item_price_setup'])*(100-floatval($item['setup_discount_val']))/100, 3);
            }
        }
        // Repeat
        if (empty($item['repeat_discount'])) {
            $item['item_sale_repeat'] = '';
        } else {
            if ($item['item_price_repeat']=='') {
                $item['item_sale_repeat'] = '';
            } else {
                $item['item_sale_repeat'] = round($item['item_price_repeat']*(100-$item['repeat_discount_val'])/100, 3);
            }
        }
        // Rush
        if (empty($item['rush1_discount'])) {
            $item['item_sale_rush1'] = '';
        } else {
            if ($item['item_price_rush1']=='') {
                $item['item_sale_rush1'] = '';
            } else {
                $item['item_sale_rush1'] = round($item['item_price_rush1']*(100-$item['rush1_discount_val'])/100,3);
            }
        }
        if (empty($item['rush2_discount'])) {
            $item['item_sale_rush2'] = '';
        } else {
            if ($item['item_price_rush2']=='') {
                $item['item_sale_rush2'] = '';
            } else {
                $item['item_sale_rush2'] = round($item['item_price_rush2']*(100-$item['rush2_discount_val'])/100,3);
            }
        }
        // Pantone
        if (empty($item['pantone_discount'])) {
            $item['item_sale_pantone'] = '';
        } else {
            if ($item['item_price_pantone']=='') {
                $item['item_sale_pantone'] = '';
            } else {
                $item['item_sale_pantone'] = round($item['item_price_pantone']*(100-$item['pantone_discount_val'])/100,3);
            }
        }
        return [
            'item' => $item,
            'prices' => $prices,
        ];
    }

    // Recalc Promo Profit
    private function _recalc_promo_profit($prices, $vendprices, $commonprices)
    {
        /* IDX of Vendor Prices */
        /* Init Profits array */
        $profits = array();
        foreach ($prices as $row) {
            $base_cost = 0;
            if (floatval($row['sale_price']) != 0) {
                $base_cost = floatval($row['sale_price']);
            } elseif (floatval($row['price']) != 0) {
                $base_cost = floatval($row['price']);
            }
            $profits[] = array(
                'price_id' => $row['promo_price_id'],
                'type' => 'qty',
                'base' => $row['item_qty'],
                'vendprice' => $commonprices['base_cost'],
                'base_cost' => $base_cost,
                'profit' => '',
                'profit_perc' => '',
                'profit_class' => 'empty',
            );
        }
        /* Add 2 special prices */
        $base_cost = 0;
        if (floatval($commonprices['item_sale_print']) != 0) {
            $base_cost = floatval($commonprices['item_sale_print']);
        } elseif (floatval($commonprices['item_price_print'])) {
            $base_cost = floatval($commonprices['item_price_print']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'print',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_exprint']) == 0 ? 0 : floatval($commonprices['vendor_item_exprint'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        $base_cost = 0;
        if (floatval($commonprices['item_sale_setup']) != 0) {
            $base_cost = floatval($commonprices['item_sale_setup']);
        } elseif (floatval($commonprices['item_price_setup'])) {
            $base_cost = floatval($commonprices['item_price_setup']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'setup',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_setup']) == 0 ? 0 : floatval($commonprices['vendor_item_setup'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Repeat
        $base_cost = 0;
        if (floatval($commonprices['item_sale_repeat']) != 0) {
            $base_cost = floatval($commonprices['item_sale_repeat']);
        } elseif (floatval($commonprices['item_price_repeat'])) {
            $base_cost = floatval($commonprices['item_price_repeat']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'repeat',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_repeat']) == 0 ? 0 : floatval($commonprices['vendor_item_repeat'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Rush 1
        $base_cost = 0;
        if (floatval($commonprices['item_sale_rush1']) != 0) {
            $base_cost = floatval($commonprices['item_sale_rush1']);
        } elseif (floatval($commonprices['item_price_rush1'])) {
            $base_cost = floatval($commonprices['item_price_rush1']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'rush1',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_rush1']) == 0 ? 0 : floatval($commonprices['vendor_item_rush1'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Rush 2
        $base_cost = 0;
        if (floatval($commonprices['item_sale_rush2']) != 0) {
            $base_cost = floatval($commonprices['item_sale_rush2']);
        } elseif (floatval($commonprices['item_price_rush2'])) {
            $base_cost = floatval($commonprices['item_price_rush2']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'rush2',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_rush2']) == 0 ? 0 : floatval($commonprices['vendor_item_rush2'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );
        // Pantone
        $base_cost = 0;
        if (floatval($commonprices['item_sale_pantone']) != 0) {
            $base_cost = floatval($commonprices['item_sale_pantone']);
        } elseif (floatval($commonprices['item_price_pantone'])) {
            $base_cost = floatval($commonprices['item_price_pantone']);
        }
        $profits[] = array(
            'price_id' => $commonprices['item_price_id'],
            'type' => 'pantone',
            'base' => 1,
            'base_cost' => $base_cost,
            'vendprice' => (floatval($commonprices['vendor_item_pantone']) == 0 ? 0 : floatval($commonprices['vendor_item_pantone'])),
            'profit' => '',
            'profit_perc' => '',
            'profit_class' => 'empty',
        );

        $new_profit = $this->recalc_profit($vendprices, $profits);
        return $new_profit;
    }

    private function recalc_profit($vendprice, $profits) {
        $out = array();
        foreach ($profits as $row) {
            if ($row['base_cost'] != 0) {
                if ($row['type'] == 'qty') {
                    /* Our Base less then 1-st entered value */
                    if ($row['base'] == 5000) {
                        $proof = 0;
                    }
                    foreach ($vendprice as $qrow) {
                        if ($qrow['vendorprice_qty'] <= $row['base'] && !empty($qrow['vendorprice_color'])) {
                            $row['vendprice'] = $qrow['vendorprice_color'];
                        }
                    }
                    if (floatval($row['vendprice']) != 0) {
                        $profit = ($row['base_cost'] - $row['vendprice']) * $row['base'];
                        $row['profit'] = round($profit, 0);
                        $row['profit_perc'] = round($profit / ($row['base_cost'] * $row['base']) * 100, 0);
                        $row['profit_class'] = profit_bgclass($row['profit_perc']);
                    }
                } else {
                    if (floatval($row['vendprice']) != 0) {
                        $row['profit'] = round($row['base_cost'] - $row['vendprice'], 2);
                        $row['profit_perc'] = round($row['profit'] / ($row['base_cost']) * 100, 0);
                        $row['profit_class'] = profit_bgclass($row['profit_perc']);
                    }
                }
            }
            $out[] = $row;
        }
        return $out;
    }


    private function _prepare_common_prices($sessiondata) {
        $vendor = $sessiondata['vendor_item'];
        $item = $sessiondata['item'];
        $commonprice['base_cost'] = $commonprice['vendor_item_exprint'] = $commonprice['vendor_item_setup'] = 0;
        $commonprice['vendor_item_repeat'] = $commonprice['vendor_item_rush1'] = $commonprice['vendor_item_rush2'] = 0;
        $commonprice['vendor_item_pantone'] = 0;
        $commonprice['item_sale_print'] = $item['item_sale_print'];
        $commonprice['item_price_print'] = $item['item_price_print'];
        $commonprice['item_sale_setup'] = $item['item_sale_setup'];
        $commonprice['item_price_setup'] = $item['item_price_setup'];
        $commonprice['item_sale_repeat'] = $item['item_sale_repeat'];
        $commonprice['item_price_repeat'] = $item['item_price_repeat'];
        $commonprice['item_sale_rush1'] = $item['item_sale_rush1'];
        $commonprice['item_price_rush1'] = $item['item_price_rush1'];
        $commonprice['item_sale_rush2'] = $item['item_sale_rush2'];
        $commonprice['item_price_rush2'] = $item['item_price_rush2'];
        $commonprice['item_sale_pantone'] = $item['item_sale_pantone'];
        $commonprice['item_price_pantone'] = $item['item_price_pantone'];

        $commonprice['item_price_id'] = $item['item_price_id'];
        if (!empty($vendor['vendor_item_cost'])) {
            $commonprice['base_cost'] = $vendor['vendor_item_cost'];
        }
        if (!empty($vendor['vendor_item_exprint'])) {
            $commonprice['vendor_item_exprint'] = $vendor['vendor_item_exprint'];
        }
        if (!empty($vendor['vendor_item_setup'])) {
            $commonprice['vendor_item_setup'] = $vendor['vendor_item_setup'];
        }
        if (!empty($vendor['vendor_item_repeat'])) {
            $commonprice['vendor_item_repeat'] = $vendor['vendor_item_repeat'];
        }
        if (!empty($vendor['rush1_price'])) {
            $commonprice['vendor_item_rush1'] = $vendor['rush1_price'];
        }
        if (!empty($vendor['rush2_price'])) {
            $commonprice['vendor_item_rush2'] = $vendor['rush2_price'];
        }
        if (!empty($vendor['pantone_match'])) {
            $commonprice['vendor_item_pantone'] = $vendor['pantone_match'];
        }

        return $commonprice;
    }

    private function _update_profit($profits, $item, $prices, $sessiondata, $session) {
        foreach ($profits as $profit) {
            if ($profit['type']=='qty') {
                $idx = 0;
                foreach ($prices as $price) {
                    if ($price['item_qty']==$profit['base']) {
                        $prices[$idx]['profit'] = $profit['profit'];
                        $prices[$idx]['profit_class'] = $profit['profit_class'];
                        $prices[$idx]['profit_perc'] = $profit['profit_perc'];
                        break;
                    }
                    $idx++;
                }
            } elseif ($profit['type']=='setup') {
                $item['profit_setup'] = $profit['profit'];
                $item['profit_setup_class'] = $profit['profit_class'];
                $item['profit_setup_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='print') {
                $item['profit_print'] = $profit['profit'];
                $item['profit_print_class'] = $profit['profit_class'];
                $item['profit_print_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='repeat') {
                $item['profit_repeat'] = $profit['profit'];
                $item['profit_repeat_class'] = $profit['profit_class'];
                $item['profit_repeat_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='rush1') {
                $item['profit_rush1'] = $profit['profit'];
                $item['profit_rush1_class'] = $profit['profit_class'];
                $item['profit_rush1_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='rush2') {
                $item['profit_rush2'] = $profit['profit'];
                $item['profit_rush2_class'] = $profit['profit_class'];
                $item['profit_rush2_perc'] = $profit['profit_perc'];
            } elseif ($profit['type']=='pantone') {
                $item['profit_pantone'] = $profit['profit'];
                $item['profit_pantone_class'] = $profit['profit_class'];
                $item['profit_pantone_perc'] = $profit['profit_perc'];
            }
        }
        $sessiondata['item'] = $item;
        $sessiondata['prices'] = $prices;
        usersession($session, $sessiondata);
        return true;
    }

    private function _check_itemdetails($sessiondata) {
        $out=['result' => $this->success_result ];
        $errmsg = [];
        $item = $sessiondata['item'];
        if (empty($item['item_name'])) {
            array_push($errmsg, 'Item Name empty');
        }
        if ($item['item_id']<=0 && (empty($item['category_id']) || empty($item['item_number']))) {
            array_push($errmsg, 'Item Number empty');
        }
        if (count($errmsg) > 0) {
            $out['result'] = $this->error_result;
            $out['errmsg'] = $errmsg;
        }
        return $out;
    }

}