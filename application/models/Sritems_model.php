<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Sritems_model extends My_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_relievers_itemscount($options=[]) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->select('(vm.keyinfo+vm.similar+vm.prices+vm.printing+vm.meta+vm.imagescolors+vm.supplier) as missings');
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
        $res = $this->db->get()->result_array();
        return count($res);
    }

    public function get_relievers_itemslist($options) {
        $this->db->select('i.item_id, i.item_number, i.item_name, i.item_active');
        $this->db->select('(vm.keyinfo+vm.similar+vm.prices+vm.printing+vm.meta+vm.imagescolors+vm.supplier+vm.shiping) as missings');
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
                $row['misinfo'] = 'Complete';
            } else {
                $row['misclas'] = 'missing';
                $this->db->select('*');
                $this->db->from('v_sritem_missinginfo');
                $this->db->where('item_id', $row['item_id']);
                $misdata = $this->db->get()->row_array();
                $row['misinfo'] = $this->load->view('dbitems/missinfo_details_view', $misdata, TRUE);
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
        $this->db->select('i.*,sr.category_name, ss.subcategory_name, inv.item_name as printshop_item_name');
        $this->db->from('sb_items i');
        $this->db->join('sr_categories sr', 'sr.category_id=i.category_id');
        $this->db->join('sr_subcategories ss','ss.subcategory_id=i.subcategory_id','left');
        $this->db->join('ts_inventory_items inv','inv.inventory_item_id=i.printshop_inventory_id','left');
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
            if (!empty($item['printshop_inventory_id'])) {

            }
            // Colors
            $colors = [];
            $numpp=0;
            if (empty($item['printshop_inventory_id'])) {
                $colorsrc = $this->itemcolors_model->get_colors_item($item_id, $editmode);
                foreach ($colorsrc as $itmcolor) {
                    $colors[] = [
                        'item_color_id' => $itmcolor['item_color_id'],
                        'item_color' => $itmcolor['item_color'],
                        'item_color_image' => $itmcolor['item_color_image'],
                        'item_color_order' => $numpp+1,
                    ];
                    $numpp++;
                }
            } else {
                if ($editmode==0) {
                    $colorsrc = $this->itemcolors_model->get_invent_itemcolors($item_id, $editmode);
                } else {
                    $colorsrc = $this->itemcolors_model->get_invent_itemcolors($item_id, $editmode);
                }
                foreach ($colorsrc as $itmcolor) {
                    $colors[] = [
                        'item_color_id' => $itmcolor['item_color_id'],
                        'item_color' => $itmcolor['item_color'],
                        'item_color_image' => $itmcolor['color_image'],
                        'item_color_order' => empty($itmcolor['color_order']) ? $numpp+1 : $itmcolor['color_order'],
                        'printshop_color' => $itmcolor['printshop_color_id'],
                        'item_color_source' => $itmcolor['color'],
                    ];
                    $numpp++;
                }
            }
            // Vendor Info
            $pricesmax = $this->config->item('relievers_prices_val');
            $vitem = $this->vendors_model->get_item_vendor($item['vendor_item_id'], $item['printshop_inventory_id']);
            $vprices = [];
            if (ifset($vitem,'vendor_item_id', 0 )==0) {
                // New Vendor Item
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
                    'item_shipstate' => '',
                    'item_shipcountry' => '',
                    'item_shipcountry_name' => '',
                    'item_shipcity' => '',
                    'printshop_item_id' => '',
                    'stand_days' => '',
                    'rush1_days' => '',
                    'rush2_days' => '',
                    'rush1_price' => '',
                    'rush2_price' => '',
                    'pantone_match' => '',
                    'po_note' => '',
                    'vendor_name' => '',
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
            } else {
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
            $imprints = $this->imprints_model->get_imprint_item($item_id);
            $priceres = $this->prices_model->get_itemlist_price($item_id);
            if (!empty($item['printshop_inventory_id'])) {
                $priceres = $this->_recalc_inventory_profit($priceres, $vitem['vendor_item_cost']);
            }
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
            if ($editmode==1) {
                if ($numpp < $pricesmax) {
                    $idx = 1;
                    for ($i = $numpp; $i <= $pricesmax; $i++) {
                        $prices[] = [
                            'promo_price_id' => $idx * (-1),
                            'item_id' => $item_id,
                            'item_qty' => '',
                            'price' => '',
                            'sale_price' => '',
                            'profit' => '',
                            'show_first' => '0',
                            'shipbox' => '',
                            'shipweight' => '',
                            'profit_class' => '',
                            'profit_perc' => '',
                        ];
                        $idx++;
                    }
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
            $this->db->select('*');
            $this->db->from('v_sritem_missinginfo');
            $this->db->where('item_id', $item_id);
            $misdat = $this->db->get()->row_array();
            $out['missinfo'] = $misdat;
            $data=[
                'item' => $item,
                'colors' => $colors,
                'vendor_item' => $vitem,
                'vendor_price' => $vprices,
                'images' => $images,
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
        $vendor_item = $sessiondata['vendor_item'];
        $vendor_id = ifset($postdata, 'newval', '');
        if (empty($vendor_id)) {
            $out['result'] = $this->success_result;
            $vendor_item['vendor_item_vendor'] = '';
        } else {
            $this->load->model('vendors_model');
            $venddat = $this->vendors_model->get_vendor($vendor_id);
            $out['msg'] = $venddat['msg'];
            if ($venddat['result']==$this->success_result) {
                $out['result'] = $this->success_result;
                $data = $venddat['data'];
                $vendor_item['vendor_item_vendor'] = $data['vendor_id'];
            }
        }
        if ($out['result']==$this->success_result) {
            $sessiondata['vendor_item'] = $vendor_item;
            $out['internal'] = 0;
            if ($vendor_id==$this->config->item('inventory_vendor')) {
                $out['internal'] = 1;
                $prices = $sessiondata['vendor_price'];
                $idx = 0;
                foreach ($prices as $price) {
                    $prices[$idx]['vendorprice_qty'] = 0;
                    $prices[$idx]['vendorprice_val'] = 0;
                    $prices[$idx]['vendorprice_color'] = 0;
                    $idx++;
                }
                $sessiondata['vendor_price'] = $prices;
                // Colors;
                $colors = $sessiondata['colors'];
                $deleted = $sessiondata['deleted'];
                foreach ($colors as $color) {
                    if ($color['item_color_id'] > 0) {
                        $deleted[] = [
                            'entity' => 'colors',
                            'id' => $color['item_color_id'],
                        ];
                    }
                }
                $colors = [];
                $sessiondata['colors'] = $colors;
                $sessiondata['deleted'] = $deleted;
            }
            usersession($sessionsid, $sessiondata);
            // Recalc prices and
            $out['data'] = $data;
        }
        return $out;
    }

    public function change_printshopitem($data, $sessiondata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        $vendor_item = $sessiondata['vendor_item'];
        $inventory_item_id = ifset($data, 'newval','-1');
        if (intval($inventory_item_id) >= 0) {
            if (empty($inventory_item_id)) {
                $item['printshop_inventory_id'] = NULL;
                $item['option_images'] = 0;
                $vendor_item['vendor_item_blankcost'] = 0;
                $vendor_item['vendor_item_cost'] = 0;
                $vendor_item['vendor_item_number'] = '';
                $vendor_item['vendor_item_name'] = '';
                $out['printshop_name'] = '';
                $invcolors = [];
                $out['result'] = $this->success_result;
            } else {
                $this->load->model('inventory_model');
                $invres = $this->inventory_model->get_inventory_item($inventory_item_id);
                $out['msg'] = $invres['msg'];
                if ($invres['result']==$this->success_result) {
                    $item['printshop_inventory_id'] = $inventory_item_id;
                    $item['option_images'] = 1;
                    $invitem = $invres['data'];
                    $invcolors = $invres['colors'];
                    $out['printshop_name'] = $invitem['item_name'];
                    $vendor_item['vendor_item_blankcost'] = $invitem['avg_price'];
                    $vendor_item['vendor_item_cost'] = $invitem['avg_price'];
                    $vendor_item['vendor_item_number'] = $invitem['item_num'];
                    $vendor_item['vendor_item_name'] = $invitem['item_name'];
                    $out['result'] = $this->success_result;
                }
            }
            if ($out['result']==$this->success_result) {
                $vendor_prices = $sessiondata['vendor_price'];
                // Delete all vendor prices
                $idx = 0;
                foreach ($vendor_prices as $vendor_price) {
                    $vendor_prices[$idx]['vendorprice_qty'] = '';
                    $vendor_prices[$idx]['vendorprice_val'] = '';
                    $vendor_prices[$idx]['vendorprice_color'] = '';
                    $idx++;
                }
                $colors = $sessiondata['colors'];
                $deleted = $sessiondata['deleted'];
                foreach ($colors as $color) {
                    if ($color['item_color_id'] > 0) {
                        $deleted[] = [
                            'entity' => 'colors',
                            'id' => $color['item_color_id'],
                        ];
                    }
                }
                $colors = [];
                $newid = 1;
                foreach ($invcolors as $invcolor) {
                    $colors[] = [
                        'item_color_id' => $newid*(-1),
                        'item_color' => $invcolor['color'],
                        'item_color_image' => $invcolor['color_image'],
                        'item_color_order' => $invcolor['color_order'],
                        'printshop_color' => $invcolor['inventory_color_id'],
                        'item_color_source' => $invcolor['color'],
                    ];
                }
                $out['colors'] = $colors;
                $sessiondata['colors'] = $colors;
                $sessiondata['vendor_item'] = $vendor_item;
                $sessiondata['item'] = $item;
                $sessiondata['vendor_price'] = $vendor_prices;
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
                $out['vendor_price'] = $vendor_prices;
                $out['vendor_item'] = $vendor_item;
                $out['item'] = $item;
                $commonprice = $this->_prepare_common_prices($sessiondata);
                $prices = $sessiondata['prices'];
                $item = $sessiondata['item'];
                $profits = $this->_recalc_promo_profit($prices, $vendor_prices, $commonprice);
                $this->_update_profit($profits, $item, $prices, $sessiondata, $session);
            }
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
        $images = $sessiondata['popup_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = str_replace(['replimg','addimageslider'], '',$imgidx);
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
                $sessiondata['popup_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_updimages($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['popup_images'];
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
                $sessiondata['popup_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_imagessort($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $images = $sessiondata['popup_images'];
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
                $sessiondata['popup_images'] = $newimg;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function itemdetails_addimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['popup_images'];
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
                $sessiondata['popup_images'] = $newimg;
                $sessiondata['deleted'] = $deleted;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_addimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['popup_images'];
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
                $sessiondata['popup_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_optionimages_add($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $colors = $sessiondata['popup_colors'];
        $coloridx = ifset($postdata, 'fldidx','');
        if (!empty($coloridx)) {
            $coloridx = str_replace('addoptionimageslider','', $coloridx);
            $find=0;
            $idx = 0;
            foreach ($colors as $color) {
                if ($color['item_color_id']==$coloridx) {
                    $find=1;
                    $colors[$idx]['item_color_image'] = $postdata['newval'];
                    break;
                } else {
                    $idx++;
                }
            }
            if ($find==1) {
                $sessiondata['popup_colors']=$colors;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function itemdetails_optionimages_update($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $colors = $sessiondata['popup_colors'];
        $coloridx = ifset($postdata,'fldidx','');
        if (!empty($coloridx)) {
            $coloridx = str_replace('reploptimg', '',$coloridx);
            $find = 0;
            $idx = 0;
            foreach ($colors as $color) {
                if ($color['item_color_id']==$coloridx) {
                    $find = 1;
                    $images[$idx]['item_color_image'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['popup_colors'] = $colors;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_save_optimagessort($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $colors = $sessiondata['popup_colors'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = intval($imgidx);
            $numpp = ifset($postdata, 'newval', '');
            if (!empty($numpp)) {
                $numpp = intval($numpp);
                $numord = 1;
                $newimg = [];
                $arrimg = [];
                for ($i=0; $i<count($colors); $i++) {
                    foreach ($colors as $color) {
                        if (!in_array($color['item_color_id'], $arrimg)) {
                            if ($numord==$numpp) {
                                array_push($arrimg, $imgidx);
                                $numord++;
                                break;
                            } else {
                                if ($color['item_color_id']!=$imgidx) {
                                    array_push($arrimg, $color['item_color_id']);
                                    $numord++;
                                    break;
                                }
                            }
                        }
                    }
                }
                $numord=1;
                foreach ($arrimg as $keyval) {
                    foreach ($colors as $color) {
                        if ($color['item_color_id']==$keyval) {
                            $color['item_color_order'] = $numord;
                            $newimg[] = $color;
                            $numord++;
                        }
                    }
                }
                $sessiondata['popup_colors'] = $newimg;
                usersession($session, $sessiondata);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function itemdetails_optimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $colors = $sessiondata['popup_colors'];
        $coloridx = ifset($postdata,'fldidx','');
        if (!empty($coloridx)) {
            $find = 0;
            $idx = 0;
            foreach ($colors as $color) {
                if ($color['item_color_id']==$coloridx) {
                    $find = 1;
                    $colors[$idx]['item_color_image'] = '';
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['popup_colors'] = $colors;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function itemdetails_optimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $colors = $sessiondata['popup_colors'];
        $coloridx = ifset($postdata,'fldidx','');
        if (!empty($coloridx)) {
            $find = 0;
            $idx = 0;
            foreach ($colors as $color) {
                if ($color['item_color_id']==$coloridx) {
                    $find = 1;
                    $colors[$idx]['item_color'] = $postdata['newval'];
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['popup_colors'] = $colors;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    public function item_images_rebuild($sessiondata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $item = $sessiondata['item'];
        $images = $sessiondata['popup_images'];
        $deleted = $sessiondata['deleted'];
        $newimg = [];
        $numorder = 1;
        foreach ($images as $image) {
            if (empty($image['item_img_name'])) {
                if ($image['item_img_id'] > 0) {
                    $deleted[] = [
                        'entity' => 'images',
                        'id' => $image['item_img_id'],
                    ];
                }
            } else {
                $image['item_img_order'] = $numorder;
                $newimg[] = $image;
                $numorder++;
            }
        }
        $newcolors = [];
        $colors = $sessiondata['popup_colors'];
        $numorder = 1;
        foreach ($colors as $color) {
            if (!empty($item['printshop_inventory_id'])) {
                $newcolors[] = $color;
            } else {
                if (empty($color['item_color'])) {
                    if ($color['item_color_id'] > 0) {
                        $deleted[] = [
                            'entity' => 'colors',
                            'id' => $color['item_color_id'],
                        ];
                    }
                } else {
                    $color['item_color_order'] = $numorder;
                    $newcolors[] = $color;
                    $numorder++;
                }
            }
        }
        $sessiondata['images'] = $newimg;
        $sessiondata['colors'] = $newcolors;
        unset($sessiondata['popup_images']);
        unset($sessiondata['popup_colors']);
        $out['result'] = $this->success_result;
        $this->db->select('imagescolors')->from('v_sritem_missinginfo')->where('item_id', $item['item_id']);
        $res = $this->db->get()->row_array();
        $out['missinfo'] = ifset($res,'imagescolors',1);
        usersession($session, $sessiondata);
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
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found', 'address' => 0];
        $vendoritem = $sessiondata['vendor_item'];
        $fldname = ifset($postdata, 'fld', '');
        if (!empty($fldname) && array_key_exists($fldname,$vendoritem)) {
            $vendoritem[$fldname] = ifset($postdata,'newval','');
            $out['result'] = $this->success_result;
            if ($fldname=='vendor_item_zipcode' || $fldname=='item_shipcountry') {
                $out['address'] = 1;
                $this->load->model('shipping_model');
                $chkres = $this->shipping_model->get_zip_address($vendoritem['item_shipcountry'], $vendoritem['vendor_item_zipcode']);
                if ($chkres['result']==$this->error_result) {
                    $vendoritem['item_shipstate'] = '';
                    $vendoritem['item_shipcity'] = '';
                    $out['state'] = '';
                } else {
                    $vendoritem['item_shipstate'] = $chkres['state'];
                    $vendoritem['item_shipcity'] = $chkres['city'];
                    $out['state'] = $chkres['state'];
                }
            }
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
                    $prices[$idx][$fldname] = PriceOutput($postdata['newval']);
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
                    if ($fld=='item_inprint_size') {
                        $inprints[$idx][$fld] = htmlspecialchars($postdata['newval']);
                    } else {
                        $inprints[$idx][$fld] = $postdata['newval'];
                    }
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
            $sessiondata['item'] = $item;
            usersession($session, $sessiondata);
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
            $history = [
                'keyinfo' => [],
                'similar' => [],
                'supplier' => [],
                'options' => [],
                'pricing' => [],
                'printing' => [],
                'meta' => [],
                'shipping' => [],
            ];
            // Lets go to save
            $item = $sessiondata['item'];
            $images = $sessiondata['images'];
            $similars = $sessiondata['similar'];
            $colors = $sessiondata['colors'];
            $vendor_item = $sessiondata['vendor_item'];
            $vendor_prices = $sessiondata['vendor_price'];
            $prices = $sessiondata['prices'];
            $inprints = $sessiondata['inprints'];
            $shipboxes = $sessiondata['shipboxes'];
            $oldres = $this->get_itemlist_details($item['item_id'], 0);
            $compare = 0;
            if ($oldres['result']==$this->success_result) {
                $olddata = $oldres['data'];
                $compare = 1;
            }
            if ($compare==1) {
                $history['keyinfo'] = $this->_keyinfo_diff($olddata, $item);
                $history['similar'] = $this->_similar_diff($olddata, $similars);
                $history['supplier'] = $this->_supplier_diff($olddata, $item, $vendor_item, $vendor_prices);
                $history['options'] = $this->_options_diff($olddata, $item, $images, $colors);
                $history['pricing'] = $this->_prices_diff($olddata, $item, $prices);
                $history['printing'] = $this->_custom_diff($olddata, $item, $inprints);
                $history['meta'] = $this->_meta_diff($olddata, $item);
                $history['shipping'] = $this->_shipping_diff($olddata, $item, $shipboxes);
            }
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
            $this->db->set('printshop_inventory_id', empty($item['printshop_inventory_id'])? null : $item['printshop_inventory_id']);
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
            $numpp=1;
            foreach ($images as $image) {
                if (stripos($image['item_img_name'], $preload_sh)!==FALSE) {
                    $img_src = str_replace($preload_sh, '', $image['item_img_name']);
                    $cpres = @copy($preload_fl.$img_src, $itemimg_fl.$img_src);
                    if ($cpres) {
                        $image['item_img_name'] = $itemimg_sh.$img_src;
                    } else {
                        $image['item_img_name'] = '';
                    }
                }
                if (empty($image['item_img_name'])) {
                    if ($image['item_img_id'] > 0) {
                        $this->db->where('item_img_id', $image['item_img_id']);
                        $this->db->delete('sb_item_images');
                    }
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
            // Colors
            if (empty($item['printshop_inventory_id'])) {
                foreach ($colors as $color) {
                    if (empty($color['item_color'])) {
                        if ($color['item_color_id'] > 0) {
                            $this->db->where('item_color_id', $color['item_color_id']);
                            $this->db->delete('sb_item_colors');
                        }
                    } else {
                        if (!empty($color['item_color_image']) && stripos($color['item_color_image'], $preload_sh)) {
                            $img_src = str_replace($preload_sh, '', $color['item_color_image']);
                            $cpres = @copy($preload_fl . $img_src, $itemimg_fl . $img_src);
                            if ($cpres) {
                                $color['item_color_image'] = $itemimg_sh . $img_src;
                            } else {
                                $color['item_color_image'] = '';
                            }
                        }
                        $this->db->set('item_color', $color['item_color']);
                        $this->db->set('item_color_image', $color['item_color_image']);
                        $this->db->set('item_color_order', $color['item_color_order']);
                        if ($color['item_color_id'] > 0) {
                            $this->db->where('item_color_id', $color['item_color_id']);
                            $this->db->update('sb_item_colors');
                        } else {
                            $this->db->set('item_color_itemid', $item_id);
                            $this->db->insert('sb_item_colors');
                        }
                    }
                }
            } else {
                foreach ($colors as $color) {
                    if (!empty($color['item_color_image']) && stripos($color['item_color_image'], $preload_sh)) {
                        $img_src = str_replace($preload_sh, '', $color['item_color_image']);
                        $cpres = @copy($preload_fl . $img_src, $itemimg_fl . $img_src);
                        if ($cpres) {
                            $color['item_color_image'] = $itemimg_sh . $img_src;
                        } else {
                            $color['item_color_image'] = '';
                        }
                    }
                    $this->db->set('item_color_image', $color['item_color_image']);
                    $this->db->set('item_color_order', $color['item_color_order']);
                    $this->db->set('item_color', $color['item_color']);
                    $this->db->set('printshop_color_id', $color['printshop_color']);
                    if ($color['item_color_id'] > 0) {
                        $this->db->where('item_color_id', $color['item_color_id']);
                        $this->db->update('sb_item_colors');
                    } else {
                        $this->db->set('item_color_itemid', $item_id);
                        $this->db->insert('sb_item_colors');
                    }
                }
            }
            // Vendor AI file
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
            $this->db->set('vendor_item_vendor', $vendor_item['vendor_item_vendor']);
            $this->db->set('vendor_item_zipcode', $vendor_item['vendor_item_zipcode']);
            $this->db->set('item_shipcountry', $vendor_item['item_shipcountry']);
            $this->db->set('item_shipstate', $vendor_item['item_shipstate']);
            $this->db->set('item_shipcity', $vendor_item['item_shipcity']);
            $this->db->set('po_note', $vendor_item['po_note']);

            if ($vendor_item['vendor_item_id'] > 0) {
                $this->db->where('vendor_item_id', $vendor_item['vendor_item_id']);
                $this->db->update('sb_vendor_items');
                $vendor_item_id = $vendor_item['vendor_item_id'];
            } else {
//                $this->db->set('vendor_item_vendor', $vendor_item['vendor_id']);
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
            // Prices
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
                } elseif ($deleterow['entity']=='colors') {
                    $this->db->where('item_color_id', $deleterow['id']);
                    $this->db->delete('sb_item_colors');
                }
            }
        }
        if ($out['result']==$this->success_result) {
            usersession($session, null);
            $this->load->model('items_model');
            $this->items_model->save_history($history, $item_id, $user_id);
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
                    $newprice = round($price['price']*(100-$item['price_discount_val'])/100,3);
                    $prices[$idx]['sale_price'] = PriceOutput($newprice);
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
                $newprice = round($item['item_price_print']*(100-$item['print_discount_val'])/100, 3);
                $item['item_sale_print'] = PriceOutput($newprice);
            }
        }
        // Setup
        if (empty($item['setup_discount'])) {
            $item['item_sale_setup'] = '';
        } else {
            if ($item['item_price_setup']=='') {
                $item['item_sale_setup'] = '';
            } else {
                $newprice = round(floatval($item['item_price_setup'])*(100-floatval($item['setup_discount_val']))/100, 3);
                $item['item_sale_setup'] = PriceOutput($newprice);
            }
        }
        // Repeat
        if (empty($item['repeat_discount'])) {
            $item['item_sale_repeat'] = '';
        } else {
            if ($item['item_price_repeat']=='') {
                $item['item_sale_repeat'] = '';
            } else {
                $newprice = round($item['item_price_repeat']*(100-$item['repeat_discount_val'])/100, 3);
                $item['item_sale_repeat'] = PriceOutput($newprice);
            }
        }
        // Rush
        if (empty($item['rush1_discount'])) {
            $item['item_sale_rush1'] = '';
        } else {
            if ($item['item_price_rush1']=='') {
                $item['item_sale_rush1'] = '';
            } else {
                $newprice = round($item['item_price_rush1']*(100-$item['rush1_discount_val'])/100,3);
                $item['item_sale_rush1'] = PriceOutput($newprice);
            }
        }
        if (empty($item['rush2_discount'])) {
            $item['item_sale_rush2'] = '';
        } else {
            if ($item['item_price_rush2']=='') {
                $item['item_sale_rush2'] = '';
            } else {
                $newprice = round($item['item_price_rush2']*(100-$item['rush2_discount_val'])/100,3);
                $item['item_sale_rush2'] = PriceOutput($newprice);
            }
        }
        // Pantone
        if (empty($item['pantone_discount'])) {
            $item['item_sale_pantone'] = '';
        } else {
            if ($item['item_price_pantone']=='') {
                $item['item_sale_pantone'] = '';
            } else {
                $newprice = round($item['item_price_pantone']*(100-$item['pantone_discount_val'])/100,3);
                $item['item_sale_pantone'] = PriceOutput($newprice);
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

    private function _recalc_inventory_profit($prices, $vendor_item_cost) {
        $idx = 0;
        foreach ($prices as $price) {
            if (!empty($vendor_item_cost) && !empty($price['item_qty'])) {
                $base_price = $price['price'];
                if (!empty($price['sale_price'])) {
                    $base_price = $price['sale_price'];
                }
                $profit = round(($base_price - $vendor_item_cost) * $price['item_qty'],2);
                $prices[$idx]['profit'] = $profit;
            }
            $idx++;
        }
        return $prices;
    }

    private function _keyinfo_diff($olddata, $item) {
        $olditem = $olddata['item'];
        $info = [];
        if ($olditem['item_active']!==$item['item_active']) {
            if ($item['item_active']==1) {
                $info[]='Changed Item Status from "Non-Active" to "Active"';
            } else {
                $info[]='Changed Item Status from "Active" to "Non-Active"';
            }
        }
        if ($olditem['item_name']!==$item['item_name']) {
            $info[]='Changed Item Name from "'.$olditem['item_name'].'" to "'.$item['item_name'].'"';
        }
        if ($olditem['item_template']!==$item['item_template']) {
            if (empty($olditem['item_template'])) {
                $info[]='Changed Item Template from "null" to "'.$item['item_template'].'"';
            } else {
                $info[]='Changed Item Template from "'.$olditem['item_template'].'" to "'.$item['item_template'].'"';
            }
        }
        if ($olditem['item_new']!==$item['item_new']) {
            if ($item['item_new']==1) {
                $info[]='Changed Item Tag from "Not-New" to "New"';
            } else {
                $info[]='Changed Item Tag from "New" to "Not-New"';
            }
        }
        if ($olditem['item_sale']!==$item['item_sale']) {
            if ($item['item_sale']==1) {
                $info[]='Changed Item Tag from "Not-Sale" to "Sale"';
            } else {
                $info[]='Changed Item Tag from "Sale" to "Not-Sale"';
            }
        }
        if ($olditem['item_topsale']!==$item['item_topsale']) {
            if ($item['item_topsale']==1) {
                $info[]='Changed Item Tag from "Not Top Seller" to "Top Seller"';
            } else {
                $info[]='Changed Item Tag from "Top Seller" to "Not Top Seller"';
            }
        }
        if ($olditem['item_size']!==$item['item_size']) {
            if (empty($olditem['item_size'])) {
                $info[]='Changed SIZE from "null" to "'.$item['item_size'].'"';
            } else {
                $info[]='Changed SIZE from "'.$olditem['item_size'].'" to "'.$item['item_size'].'"';
            }
        }
        if ($olditem['item_material']!==$item['item_material']) {
            if (empty($olditem['item_material'])) {
                $info[]='Changed MATERIAL from "null" to "'.$item['item_material'].'"';
            } else {
                $info[]='Changed MATERIAL from "'.$olditem['item_material'].'" to "'.$item['item_material'].'"';
            }
        }
        if ($olditem['item_description1']!==$item['item_description1']) {
            if (empty($olditem['item_description1'])) {
                $info[]='Changed ITEM DESCRIPTION from "null" to "'.$item['item_description1'].'"';
            } else {
                $info[]='Changed ITEM DESCRIPTION from "'.$olditem['item_description1'].'" to "'.$item['item_description1'].'"';
            }
        }
        if ($olditem['bullet1']!==$item['bullet1']) {
            if (empty($olditem['bullet1'])) {
                $info[]='Changed BULLET POINT 1 from "null" to "'.$item['bullet1'].'"';
            } else {
                $info[]='Changed BULLET POINT 1 from "'.$olditem['bullet1'].'" to "'.$item['bullet1'].'"';
            }
        }
        if ($olditem['bullet2']!==$item['bullet2']) {
            if (empty($olditem['bullet2'])) {
                $info[]='Changed BULLET POINT 2 from "null" to "'.$item['bullet2'].'"';
            } else {
                $info[]='Changed BULLET POINT 2 from "'.$olditem['bullet2'].'" to "'.$item['bullet2'].'"';
            }
        }
        if ($olditem['bullet3']!==$item['bullet3']) {
            if (empty($olditem['bullet3'])) {
                $info[]='Changed BULLET POINT 3 from "null" to "'.$item['bullet3'].'"';
            } else {
                $info[]='Changed BULLET POINT 3 from "'.$olditem['bullet3'].'" to "'.$item['bullet3'].'"';
            }
        }
        if ($olditem['bullet4']!==$item['bullet4']) {
            if (empty($olditem['bullet4'])) {
                $info[]='Changed BULLET POINT 4 from "null" to "'.$item['bullet4'].'"';
            } else {
                $info[]='Changed BULLET POINT 4 from "'.$olditem['bullet4'].'" to "'.$item['bullet4'].'"';
            }
        }
        return $info;
    }

    private function _similar_diff($olddata, $similars) {
        $info = [];
        $oldsimilars = $olddata['similar'];
        $idx = 0;
        $numpp = 1;
        foreach ($oldsimilars as $oldsimilar) {
            if ($oldsimilar['item_similar_similar']!==$similars[$idx]['item_similar_similar']) {
                if (empty($similars[$idx]['item_similar_similar'])) {
                    $info[]='Removed SIMILAR item # '.$numpp;
                } else {
                    $this->db->select('item_number, item_name');
                    $this->db->from('sb_items');
                    $this->db->where('item_id', $similars[$idx]['item_similar_similar']);
                    $simres = $this->db->get()->row_array();
                    if ($oldsimilar['item_similar_id']<0) {
                        $info[]='Changed SIMILAR ITEM # '.$numpp.' from "null"  to "'.$simres['item_number'].'-'.$simres['item_name'].'"';
                    } else {
                        $info[]='Changed SIMILAR ITEM # '.$numpp.' from "'.$oldsimilar['item_number'].'-'.$oldsimilar['item_name'].'" to "'.$simres['item_number'].'-'.$simres['item_name'].'"';
                    }
                }
            }
            $numpp++;
            $idx++;
        }
        return $info;
    }

    private function _supplier_diff($olddata, $item, $vendor_item, $vendor_prices) {
        $info = [];
        $oldvitem = $olddata['vendor_item'];
        $oldvprices = $olddata['vendor_price'];
        $olditem = $olddata['item'];
        $this->load->model('vendors_model');
        $this->load->model('inventory_model');
        if ($oldvitem['vendor_item_vendor']!==$vendor_item['vendor_item_vendor']) {
            $vendres  = $this->vendors_model->get_vendor($vendor_item['vendor_item_vendor']);
            if ($vendres['result']==$this->success_result) {
                if (empty($oldvitem['vendor_item_vendor'])) {
                    $info[]='Changed VENDOR from "null" to "'.$vendres['data']['vendor_name'].'"';
                } else {
                    $info[]='Changed VENDOR from "'.$oldvitem['vendor_name'].'" to "'.$vendres['data']['vendor_name'].'"';
                }
            }
        }
        if (!empty($item['printshop_inventory_id'])) {
            if ($olditem['printshop_inventory_id']!==$item['printshop_inventory_id']) {
                $this->db->select('inventory_item_id, item_num, item_name');
                $this->db->from('ts_inventory_items');
                $this->db->where('inventory_item_id', $item['printshop_inventory_id']);
                $invres = $this->db->get()->row_array();
                if (ifset($invres,'inventory_item_id',0)==$item['printshop_inventory_id']) {
                    if (empty($olditem['printshop_inventory_id'])) {
                        $info[]='Changed Inventory item from "null" to '.$invres['item_num'].' '.$invres['item_name'];
                    } else {
                        $this->db->select('inventory_item_id, item_num, item_name');
                        $this->db->from('ts_inventory_items');
                        $this->db->where('inventory_item_id', $olditem['printshop_inventory_id']);
                        $oldinvres = $this->db->get()->row_array();
                        if (ifset($oldinvres,'inventory_item_id',0)==$olditem['printshop_inventory_id']) {
                            $info[]='Changed Inventory item from "'.$oldinvres['item_num'].' '.$oldinvres['item_name'].'" to "'.$invres['item_num'].' '.$invres['item_name'].'"';
                        }
                    }
                }
            }
        } else {
            if ($oldvitem['vendor_item_number']!==$vendor_item['vendor_item_number']) {
                if (empty($oldvitem['vendor_item_number'])) {
                    $info[]='Changed Supplier Item # from "null" to "'.$vendor_item['vendor_item_number'].'"';
                } else {
                    $info[]='Changed Supplier Item # from "'.$oldvitem['vendor_item_number'].'" to "'.$vendor_item['vendor_item_number'].'"';
                }
            }
            if ($oldvitem['vendor_item_name']!==$vendor_item['vendor_item_name']) {
                if (empty($oldvitem['vendor_item_name'])) {
                    $info[]='Changed Supplier Item Name from "null" to "'.$vendor_item['vendor_item_name'].'"';
                } else {
                    $info[]='Changed Supplier Item Name from "'.$oldvitem['vendor_item_name'].'" to "'.$vendor_item['vendor_item_name'].'"';
                }
            }
        }
        if ($oldvitem['item_shipcountry']!==$vendor_item['item_shipcountry']) {
            $this->db->select('country_id, country_name, country_iso_code_3');
            $this->db->from('sb_countries');
            $this->db->where('country_id', $vendor_item['item_shipcountry']);
            $cntres = $this->db->get()->row_array();
            if (ifset($cntres,'country_id',0)==$vendor_item['item_shipcountry']) {
                if (empty($oldvitem['item_shipcountry'])) {
                    $info[]='Changed Supplier Shipping Country from "null" to "'.$cntres['country_iso_code_3'].' '.$cntres['country_name'].'"';
                } else {
                    $this->db->select('country_id, country_name, country_iso_code_3');
                    $this->db->from('sb_countries');
                    $this->db->where('country_id', $oldvitem['item_shipcountry']);
                    $oldcntres = $this->db->get()->row_array();
                    if (ifset($oldcntres,'country_id',0)==$oldvitem['item_shipcountry']) {
                        $info[]='Changed Supplier Shipping Country from "'.$oldcntres['country_iso_code_3'].' '.$oldcntres['country_name'].'" to "'.$cntres['country_iso_code_3'].' '.$cntres['country_name'].'"';
                    }
                }
            }
        }
        if ($oldvitem['vendor_item_zipcode']!==$vendor_item['vendor_item_zipcode']) {
            if (empty($oldvitem['vendor_item_zipcode'])) {
                $info[]='Changed Supplier Shipping ZIP from "null" to "'.$vendor_item['vendor_item_zipcode'].'"';
            } else {
                $info[]='Changed Supplier Shipping ZIP from "'.$oldvitem['vendor_item_zipcode'].'" to "'.$vendor_item['vendor_item_zipcode'].'"';
            }
        }
        if ($oldvitem['vendor_item_cost']!==$vendor_item['vendor_item_cost']) {
            if (intval($oldvitem['vendor_item_cost'])==0) {
                $info[]='Changed Supplier Min price from "null" to "'.MoneyOutput($vendor_item['vendor_item_cost']).'"';
            } else {
                $info[]='Changed Supplier Min price from "'.MoneyOutput($oldvitem['vendor_item_cost']).'" to "'.MoneyOutput($vendor_item['vendor_item_cost']).'"';
            }
        }
        return $info;
    }

    private function _options_diff($olddata, $item, $images, $colors) {
        $info = [];
        $olditem = $olddata['item'];
        $oldimgs = $olddata['images'];
        $oldcolors = $olddata['colors'];
        $preload_sh = $this->config->item('pathpreload');
        if ($olditem['main_image']!==$item['main_image']) {
            if (empty($item['main_image'])) {
                $info[] = 'Removed Main Image';
            } else {
                if (empty($olditem['main_image'])) {
                    $info[] = 'Changed Main Image from "null" to Main Image';
                } else {
                    $info[] = 'Changed Main Image';
                }
            }
        }
        if ($olditem['category_image']!==$item['category_image']) {
            if (empty($item['category_image'])) {
                $info[]='Removed Category Image';
            } else {
                if (empty($olditem['category_image'])) {
                    $info[] = 'Changed Category Image from "null" to Category Image';
                } else {
                    $info[] = 'Changed Category Image';
                }
            }
        }
        if ($olditem['top_banner']!==$item['top_banner']) {
            if (empty($item['top_banner'])) {
                $info[] = 'Removed Top Banner Image';
            } else {
                if (empty($olditem['top_banner'])) {
                    $info[] = 'Changed Top Banner Image from "null" to Top Banner Image';
                } else {
                    $info[] = 'Changed Top Banner Image';
                }
            }
        }
        // Images
        foreach ($oldimgs as $oldimg) {
            $find = 0;
            foreach ($images as $image) {
                if ($oldimg['item_img_id']==$image['item_img_id']) {
                    if ($oldimg['item_img_name']!==$image['item_img_name']) {
                        $info[] = 'Changed Image # '.$oldimg['item_img_order'];
                    }
                    $find=1;
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Removed Image # '.$oldimg['item_img_order'];
            }
        }
        foreach ($images as $image) {
            if ($image['item_img_id'] < 0) {
                $info[] ='Changed Image # '.$image['item_img_order'].' from "null" to Image # '.$image['item_img_order'];
            }
        }
        if ($olditem['options']!==$item['options']) {
            if (empty($olditem['options'])) {
                $info[] = 'Changed Options from "null" to "'.$item['options'].'"';
            } else {
                if (empty($item['options'])) {
                    $info[] = 'Removed Options';
                } else {
                    $info[] = 'Changed Options from "'.$olditem['options'].'" to "'.$item['options'].'"';
                }
            }
        }
        if ($olditem['option_images']!==$item['option_images']) {
            if ($item['option_images']==0) {
                $info[] = 'Changed Option from "Require Images" to "Non Require Images"';
            } else {
                $info[] = 'Changed Option from "Non Require Images" to "Require Images"';
            }
        }
        foreach ($oldcolors as $oldcolor) {
            $find=0;
            foreach ($colors as $color) {
                if ($oldcolor['item_color_id']==$color['item_color_id']) {
                    if ($oldcolor['item_color']!==$color['item_color']) {
                        $info[] = 'Changed option # '.$oldcolor['item_color_order'].' from "'.$oldcolor['item_color'].'" to "'.$color['item_color'].'"';
                    }
                    $find = 1;
                    break;
                }
            }
            if ($find==0) {
                $info[] ='Removed option # '.$oldcolor['item_color_order'].' '.$oldcolor['item_color'];
            }
        }
        foreach ($colors as $color) {
            if ($color['item_color_id'] < 0 && !empty($color['item_color'])) {
                $info[]='Changed option # '.$color['item_color_order'].' from "null" to "'.$color['item_color'].'"';
            }
        }
        return $info;
    }

    private function _prices_diff($olddata, $item, $prices) {
        $info = [];
        $olditem = $olddata['item'];
        $oldprices = $olddata['prices'];
        foreach ($oldprices as $oldprice) {
            $find = 0;
            foreach ($prices as $price) {
                if ($price['promo_price_id']==$oldprice['promo_price_id']) {
                    $find=1;
                    if ($oldprice['item_qty']!==$price['item_qty']) {
                        if (intval($price['item_qty'])==0) {
                            $info[] = 'Removed PRICE QTY '.QTYOutput($oldprice['item_qty']);
                        } else {
                            if (intval($oldprice['item_qty'])==0) {
                                $info[] = 'Changed PRICE QTY from "null" to "'.QTYOutput($price['item_qty']).'"';
                            } else {
                                $info[] = 'Changed PRICE QTY from "'.QTYOutput($oldprice['item_qty']).'" to "'.QTYOutput($price['item_qty']).'"';
                            }
                        }
                    }
                    if ($oldprice['price']!==$price['price']) {
                        if (floatval($oldprice['price'])==0) {
                            $info[] = 'Changed PRICE - QTY '.QTYOutput($price['item_qty']).' Price from "null" to "'.MoneyOutput($price['price']).'"';
                        } else {
                            $info[] = 'Changed PRICE - QTY '.QTYOutput($price['item_qty']).' Price from "'.MoneyOutput($oldprice['price']).'" to "'.MoneyOutput($price['price']).'"';
                        }
                    }
                    if ($oldprice['sale_price']!==$price['sale_price']) {
                        if (floatval($oldprice['sale_price'])==0) {
                            $info[] = 'Changed PRICE - QTY '.QTYOutput($price['item_qty']).' Sale Price from "null" to "'.MoneyOutput($price['sale_price']).'"';
                        } else {
                            $info[] = 'Changed PRICE - QTY '.QTYOutput($price['item_qty']).' Sale Price from "'.MoneyOutput($oldprice['sale_price']).'" to "'.MoneyOutput($price['sale_price']).'"';
                        }
                    }
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Removed Price for QTY '.QTYOutput($oldprice['item_qty']);
            }
        }
        foreach ($prices as $price) {
            if ($price['promo_price_id'] < 0 && !empty($price['item_qty'])) {
                $info[] = 'Changed Price - QTY from "null" to  "'.QTYOutput($price['item_qty']).'"';
                if (!empty($price['price'])) {
                    $info[] = 'Changed Price - QTY '.QTYOutput($price['item_qty']).' Price from "null" to "'.MoneyOutput($price['price']).'"';
                }
                if (!empty($price['sale_price'])) {
                    $info[] = 'Changed Price - QTY '.QTYOutput($price['item_qty']).' Sale Price from "null" to "'.MoneyOutput($price['sale_price']).'"';
                }
            }
        }
        // Special Prices
        if ($olditem['item_price_print']!==$item['item_price_print']) {
            if (empty($item['item_price_print'])) {
                $info[]='Removed Add\'l Prints price';
            } else {
                if (empty($olditem['item_price_print'])) {
                    $info[] = 'Changed Add\'l Prints price from "null" to "'.MoneyOutput($item['item_price_print']).'"';
                } else {
                    $info[] = 'Changed Add\'l Prints price from "'.MoneyOutput($olditem['item_price_print']).'" to "'.MoneyOutput($item['item_price_print']).'"';
                }
            }
        }
        if ($olditem['item_sale_print']!==$item['item_sale_print']) {
            if (empty($item['item_sale_print'])) {
                $info[] = 'Removed Add\'l Prints sale price';
            } else {
                if (empty($olditem['item_sale_print'])) {
                    $info[]= 'Changed Add\'l Prints sale price from "null" to "'.MoneyOutput($item['item_sale_print']).'"';
                } else {
                    $info[]= 'Changed Add\'l Prints sale price from "'.MoneyOutput($olditem['item_sale_print']).'" to "'.MoneyOutput($item['item_sale_print']).'"';
                }
            }
        }
        if ($olditem['item_price_setup']!==$item['item_price_setup']) {
            if (empty($item['item_price_setup'])) {
                $info[] = 'Removed New Setup price';
            } else {
                if (empty($olditem['item_price_setup'])) {
                    $info[] = 'Changed New Setup price from "null" to "'.MoneyOutput($item['item_price_setup']).'"';
                } else {
                    $info[] = 'Changed New Setup price from "'.MoneyOutput($olditem['item_price_setup']).'" to "'.MoneyOutput($item['item_price_setup']).'"';
                }
            }
        }
        if ($olditem['item_sale_setup']!==$item['item_sale_setup']) {
            if (empty($item['item_sale_setup'])) {
                $info[] = 'Removed New Setup sale price';
            } else {
                if (empty($olditem['item_sale_setup'])) {
                    $info[] = 'Changed New Setup sale price from "null" to "'.MoneyOutput($item['item_sale_setup']).'"';
                } else {
                    $info[] = 'Changed New Setup sale price from "'.MoneyOutput($olditem['item_sale_setup']).'" to "'.MoneyOutput($item['item_sale_setup']).'"';
                }
            }
        }
        if ($olditem['item_price_repeat']!==$item['item_price_repeat']) {
            if (empty($item['item_price_repeat'])) {
                $info[] = 'Removed Repeat Setup price';
            } else {
                if (empty($olditem['item_price_repeat'])) {
                    $info[] = 'Changed Repeat Setup price from "null" to "'.MoneyOutput($item['item_price_repeat']).'"';
                } else {
                    $info[] = 'Changed Repeat Setup price from "'.MoneyOutput($olditem['item_price_repeat']).'" to "'.MoneyOutput($item['item_price_repeat']).'"';
                }
            }
        }
        if ($olditem['item_sale_repeat']!==$item['item_sale_repeat']) {
            if (empty($item['item_sale_repeat'])) {
                $info[] = 'Removed Repeat Setup sale price';
            } else {
                if (empty($olditem['item_sale_repeat'])) {
                    $info[] ='Changed Repeat Setup sale price from "null" to "'.MoneyOutput($item['item_sale_repeat']).'"';
                } else {
                    $info[] ='Changed Repeat Setup sale price from "'.MoneyOutput($olditem['item_sale_repeat']).'" to "'.MoneyOutput($item['item_sale_repeat']).'"';
                }
            }
        }
        if ($olditem['item_price_rush1']!==$item['item_price_rush1']) {
            if (empty($item['item_price_rush1'])) {
                $info[] = 'Removed Rush 1 price';
            } else {
                if (empty($olditem['item_price_rush1'])) {
                    $info[] = 'Changed Rush 1 price from "null" to "'.MoneyOutput($item['item_price_rush1']).'"';
                } else {
                    $info[] = 'Changed Rush 1 price from "'.MoneyOutput($olditem['item_price_rush1']).'" to "'.MoneyOutput($item['item_price_rush1']).'"';
                }
            }
        }
        if ($olditem['item_sale_rush1']!==$item['item_sale_rush1']) {
            if (empty($item['item_sale_rush1'])) {
                $info[] ='Removed Rush 1 sale price';
            } else {
                if ($olditem['item_sale_rush1']) {
                    $info[] ='Changed Rush 1 sale price from "null" to "'.MoneyOutput($item['item_sale_rush1']).'"';
                } else {
                    $info[] ='Changed Rush 1 sale price from "'.MoneyOutput($olditem['item_sale_rush1']).'" to "'.MoneyOutput($item['item_sale_rush1']).'"';
                }
            }
        }
        if ($olditem['item_price_rush2']!==$item['item_price_rush2']) {
            if (empty($item['item_price_rush2'])) {
                $info[] ='Removed Rush 2 price';
            } else {
                if (empty($olditem['item_price_rush2'])) {
                    $info[] = 'Changed Rush 2 price from "null" to "'.MoneyOutput($item['item_price_rush2']).'"';
                } else {
                    $info[] = 'Changed Rush 2 price from "'.MoneyOutput($olditem['item_price_rush2']).'" to "'.MoneyOutput($item['item_price_rush2']).'"';
                }
            }
        }
        if ($olditem['item_sale_rush2']!==$item['item_sale_rush2']) {
            if (empty($item['item_sale_rush2'])) {
                $info[] = 'Removed Rush 2 sale price';
            } else {
                if (empty($olditem['item_sale_rush2'])) {
                    $info[] ='Changed Rush 2 sale price from "null" to "'.MoneyOutput($item['item_sale_rush2']).'"';
                } else {
                    $info[] ='Changed Rush 2 sale price from "'.MoneyOutput($olditem['item_sale_rush2']).'" to "'.MoneyOutput($item['item_sale_rush2']).'"';
                }
            }
        }
        if ($olditem['item_price_pantone']!==$item['item_price_pantone']) {
            if (empty($item['item_price_pantone'])) {
                $info[] = 'Removed Pantone Match price';
            } else {
                if (empty($olditem['item_price_pantone'])) {
                    $info[] = 'Changed Pantone Match price from "null" to "'.MoneyOutput($item['item_price_pantone']).'"';
                } else {
                    $info[] = 'Changed Pantone Match price from "'.MoneyOutput($olditem['item_price_pantone']).'" to "'.MoneyOutput($item['item_price_pantone']).'"';
                }
            }
        }
        if ($olditem['item_sale_pantone']!==$item['item_sale_pantone']) {
            if (empty($item['item_sale_pantone'])) {
                $info[] ='Removed Pantone Match sale price';
            } else {
                if (empty($olditem['item_sale_pantone'])) {
                    $info[] = 'Changed Pantone Match sale price from "null" to "'.MoneyOutput($item['item_sale_pantone']).'"';
                } else {
                    $info[] = 'Changed Pantone Match sale price from "'.MoneyOutput($olditem['item_sale_pantone']).'" to "'.MoneyOutput($item['item_sale_pantone']).'"';
                }
            }
        }
        return $info;
    }

    private function _custom_diff($olddata, $item, $inprints) {
        $info = [];
        $olditem = $olddata['item'];
        $oldinprints = $olddata['inprints'];
        if ($olditem['item_vector_img']!==$item['item_vector_img']) {
            if (empty($item['item_vector_img'])) {
                $info[] = 'Removed Vector AI file';
            } else {
                if (empty($olditem['item_vector_img'])) {
                    $info[] = 'Changed Vector AI file from "null"';
                } else {
                    $info[] = 'Changed Vector AI file';
                }
            }
        }
        if ($olditem['imprint_method']!==$item['imprint_method']) {
            if (empty($item['imprint_method'])) {
                $info[] = 'Removed Customization Method';
            } else {
                if (empty($olditem['imprint_method'])) {
                    $info[] = 'Changed Customization Method from "null" to "'.$item['imprint_method'].'"';
                } else {
                    $info[] = 'Changed Customization Method from "'.$olditem['imprint_method'].'" to "'.$item['imprint_method'].'"';
                }
            }
        }
        if ($olditem['imprint_color']!==$item['imprint_color']) {
            if (empty($item['imprint_color'])) {
                $info[] = 'Removed Print Colors';
            } else {
                if (empty($olditem['imprint_color'])) {
                    $info[] = 'Changed Print colors from "null" to "'.$item['imprint_color'].'"';
                } else {
                    $info[] = 'Changed Print Colors from "'.$olditem['imprint_color'].'" to "'.$item['imprint_color'].'"';
                }
            }
        }
        $numpp = 1;
        foreach ($oldinprints as $oldinprint) {
            foreach ($inprints as $inprint) {
                $find = 0;
                if ($inprint['item_inprint_id']==$oldinprint['item_inprint_id']) {
                    $find=1;
                    if ($oldinprint['item_inprint_location']!==$inprint['item_inprint_location']) {
                        $info[] = 'Changed Location '.$numpp.' - Name from "'.$oldinprint['item_inprint_location'].'" to "'.$inprint['item_inprint_location'].'"';
                    }
                    if ($oldinprint['item_inprint_size']!==$inprint['item_inprint_size']) {
                        $info[] = 'Changed Location '.$numpp.' - Size from "'.$oldinprint['item_inprint_size'].'" to "'.$inprint['item_inprint_size'].'"';
                    }
                    if ($oldinprint['item_inprint_view']!==$inprint['item_inprint_view']) {
                        $info[] = 'Changed Location '.$numpp.' - View ';
                    }
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Removed Location '.$numpp.' - '.$oldinprint['item_inprint_location'];
            }
            $numpp++;
        }
        foreach ($inprints as $inprint) {
            if ($inprint['item_inprint_id'] < 0) {
                $info[] = 'Changed Location - Name from "null" to "'.$inprint['item_inprint_location'].'"';
                $info[] = 'Changed Location - Size from "null" to "'.$inprint['item_inprint_size'].'"';
            }
        }
        return $info;
    }

    private function _meta_diff($olddata, $item) {
        $info = [];
        $olditem = $olddata['item'];
        if ($olditem['item_meta_title']!==$item['item_meta_title']) {
            if (empty($item['item_meta_title'])) {
                $info[] = 'Removed Meta Title "'.$olditem['item_meta_title'].'"';
            } else {
                if (empty($olditem['item_meta_title'])) {
                    $info[] = 'Changed Meta Title from "null" to "'.$item['item_meta_title'].'"';
                } else {
                    $info[] = 'Changed Meta Title from "'.$olditem['item_meta_title'].'" to "'.$item['item_meta_title'].'"';
                }
            }
        }
        if ($olditem['item_metadescription']!==$item['item_metadescription']) {
            if (empty($item['item_metadescription'])) {
                $info[] = 'Removed Meta Descriptiion "'.$olditem['item_metadescription'].'"';
            } else {
                if (empty($olditem['item_metadescription'])) {
                    $info[] = 'Changed Meta Description from "null" to "'.$item['item_metadescription'].'"';
                } else {
                    $info[] = 'Changed Meta Description from "'.$olditem['item_metadescription'].'" to "'.$item['item_metadescription'].'"';
                }
            }
        }
        if ($olditem['item_url']!==$item['item_url']) {
            if (empty($item['item_url'])) {
                $info[] = 'Removed Item URL "'.$olditem['item_url'].'"';
            } else {
                if (empty($olditem['item_url'])) {
                    $info[] = 'Changed Item URL from "null" to "'.$item['item_url'].'"';
                } else {
                    $info[] = 'Changed Item URL from "'.$olditem['item_url'].'" to "'.$item['item_url'].'"';
                }
            }
        }
        if ($olditem['item_metakeywords']!==$item['item_metakeywords']) {
            if (empty($item['item_metakeywords'])) {
                $info[] = 'Removed Meta Keywords "'.$olditem['item_metakeywords'].'"';
            } else {
                if (empty($olditem['item_metakeywords'])) {
                    $info[] = 'Changed Meta Keywords from "null" to "'.$item['item_metakeywords'].'"';
                } else {
                    $info[] = 'Changed Meta Keywords from "'.$olditem['item_metakeywords'].'" to "'.$item['item_metakeywords'].'"';
                }
            }
        }
        if ($olditem['item_keywords']!==$item['item_keywords']) {
            if (empty($item['item_keywords'])) {
                $info[] = 'Removed Internal Search Keywords "'.$olditem['item_keywords'].'"';
            } else {
                if (empty($olditem['item_keywords'])) {
                    $info[] = 'Changed Internal Search Keywords from "null" to "'.$item['item_keywords'].'"';
                } else {
                    $info[] = 'Changed Internal Search Keywords from "'.$olditem['item_keywords'].'" to "'.$item['item_keywords'].'"';
                }
            }
        }
        return $info;
    }

    private function _shipping_diff($olddata, $item, $shipboxes) {
        $info = [];
        $olditem = $olddata['item'];
        $oldshipboxes = $olddata['shipboxes'];
        if ($olditem['item_weigth']!==$item['item_weigth']) {
            if (empty($item['item_weigth'])) {
                $info[] = 'Removed Item Weight '.$olditem['item_weigth'];
            } else {
                if (empty($olditem['item_weigth'])) {
                    $info[] = 'Changed Item Weight from "null" to "'.$item['item_weigth'].'"';
                } else {
                    $info[] = 'Changed Item Weight from "'.$olditem['item_weigth'].'" to "'.$item['item_weigth'].'"';
                }
            }
        }
        if ($olditem['charge_pereach']!==$item['charge_pereach']) {
            if (empty($item['charge_pereach'])) {
                $info[] = 'Remove Extra $ Each "'.MoneyOutput($olditem['charge_pereach']).'"';
            } else {
                if (empty($olditem['charge_pereach'])) {
                    $info[] = 'Changed Extra $ Each from "null" to "'.MoneyOutput($item['charge_pereach']).'"';
                } else {
                    $info[] = 'Change Extra $ Each from "'.MoneyOutput($olditem['charge_pereach']).'" to "'.MoneyOutput($item['charge_pereach']).'"';
                }
            }
        }
        $numpp = 1;
        foreach ($oldshipboxes as $oldshipbox) {
            $find = 0;
            foreach ($shipboxes as $shipbox) {
                if ($oldshipbox['item_shipping_id']==$shipbox['item_shipping_id']) {
                    $find=1;
                    if (intval($shipbox['box_qty'])==0) {
                        $info[] = 'Removed SHIPBOX "'.chr(64+$numpp).'"';
                    } else {
                        if ($oldshipbox['box_qty']!==$shipbox['box_qty']) {
                            $info[] = 'Changed SHIPBOX "'.chr(64+$numpp).'" - QTY from "'.$oldshipbox['box_qty'].'" to "'.$shipbox['box_qty'].'"';
                        }
                        if ($oldshipbox['box_width']!==$shipbox['box_width']) {
                            $info[] = 'Changed SHIPBOX "'.chr(64+$numpp).'" - Width from "'.$oldshipbox['box_width'].'" to "'.$shipbox['box_width'].'"';
                        }
                        if ($oldshipbox['box_length']!==$shipbox['box_length']) {
                            $info[] = 'Changed SHIPBOX "'.chr(64+$numpp).'" - Length from "'.$oldshipbox['box_length'].'" to "'.$shipbox['box_length'].'"';
                        }
                        if ($oldshipbox['box_height']!==$shipbox['box_height']) {
                            $info[] = 'Changed SHIPBOX "'.chr(64+$numpp).'" - Height from "'.$oldshipbox['box_height'].'" to "'.$shipbox['box_height'].'"';
                        }
                    }
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Removed SHIPBOX "'.chr(64 + $numpp).'"';
            }
            $numpp++;
        }
        foreach ($shipboxes as $shipbox) {
            if ($shipbox['item_shipping_id'] < 0 && !empty($shipbox['box_qty'])) {
                $info[] = 'Changed SHIPBOX - QTY from "null" to "'.$shipbox['box_qty'].'"';
                $info[] = 'Changed SHIPBOX - Width from "null" to "'.$shipbox['box_width'].'"';
                $info[] = 'Changed SHIPBOX - Length from "null" to "'.$shipbox['box_length'].'"';
                $info[] = 'Changed SHIPBOX - Height from "null" to "'.$shipbox['box_height'].'"';
            }
        }
        return $info;
    }

    public function convert_sritems()
    {
        $this->db->select('*')->from('sritems_export')->where('managed',0);
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            $this->db->select('*')->from('sr_categories')->where('brand','SR')->where('category_code', substr($item['item_num'],0,1));
            $categdat = $this->db->get()->row_array();
            $this->db->select('*')->from('sr_subcategories')->where('subcategory_name', $item['sub_categ1']);
            $subcatdat = $this->db->get()->row_array();
            // Discounts
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['price_discount']);
            $qtydisc = $this->db->get()->row_array();
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['addprint_discount']);
            $printdisc = $this->db->get()->row_array();
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['newsetup_discount']);
            $newdisc = $this->db->get()->row_array();
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['repeat_discount']);
            $repeatdisc = $this->db->get()->row_array();
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['rush1_discount']);
            $rush1disc = $this->db->get()->row_array();
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['rush2_discount']);
            $rush2disc = $this->db->get()->row_array();
            $this->db->select('*')->from('sb_price_discounts')->where('discount_label', $item['pantone_discount']);
            $pantdisc = $this->db->get()->row_array();
            // Insert
            $this->db->set('item_number', $item['item_num']);
            $this->db->set('item_name', $item['item_name']);
            $this->db->set('item_active', $item['active']=='Active' ? 1 : 0);
            $this->db->set('item_new', $item['new']=='Yes' ? 1 : 0);
            $this->db->set('item_template', $item['template']);
            $this->db->set('item_lead_a', $item['su_stad_days']);
            $this->db->set('item_lead_b', $item['su_rush1_days']);
            $this->db->set('item_lead_c', $item['su_rush2_days']);
            $this->db->set('item_material', $item['material']);
            $this->db->set('item_weigth', $item['item_weight']);
            $this->db->set('item_size', $item['size']);
            $this->db->set('item_keywords', $item['internal_search']);
            $this->db->set('item_url', str_replace('http://www.stressrelievers.com/','', $item['page_url']));
            $this->db->set('item_meta_title', $item['meta_title']);
            $this->db->set('item_metadescription', $item['meta_desc']);
            $this->db->set('item_metakeywords', $item['meta_keywords']);
            $this->db->set('item_description1', $item['description']);
            $this->db->set('imprint_method', $item['print_method']);
            $this->db->set('imprint_color', $item['print_colors']);
            $this->db->set('options', $item['options']);
            $this->db->set('option_images', $item['options_images']=='Yes' ? 1 : 0);
            $this->db->set('category_id', $categdat['category_id']);
            $this->db->set('subcategory_id', $subcatdat['subcategory_id']);
            $this->db->set('item_sale', $item['sale']=='Yes' ? 1 : 0);
            $this->db->set('item_topsale', $item['top_seller']=='Yes' ? 1 : 0);
            $this->db->set('brand','SR');
            $this->db->set('price_discount', $qtydisc['price_discount_id']);
            $this->db->set('print_discount', $printdisc['price_discount_id']);
            $this->db->set('setup_discount', $newdisc['price_discount_id']);
            $this->db->set('repeat_discount', $repeatdisc['price_discount_id']);
            $this->db->set('rush1_discount', $rush1disc['price_discount_id']);
            $this->db->set('rush2_discount', $rush2disc['price_discount_id']);
            $this->db->set('pantone_discount', $pantdisc['price_discount_id']);
            $this->db->set('bullet1', $item['bullet_1']);
            $this->db->set('bullet2', $item['bullet_2']);
            $this->db->set('bullet3', $item['bullet_3']);
            $this->db->set('bullet4', $item['bullet_4']);
            $this->db->insert('sb_items');
            $newid = $this->db->insert_id();
            if ($newid > 0) {
                echo 'Item '.$item['item_num'].' added '.PHP_EOL;
                $this->db->where('id', $item['id']);
                $this->db->set('managed', $newid);
                $this->db->update('sritems_export');
                // Internal Vendor
                $this->db->select('vendor_id')->from('vendors')->where('vendor_name', 'INTERNAL');
                $interdat = $this->db->get()->row_array();
                $vendor_id = $interdat['vendor_id'];
                // Vendor Item
                $this->db->set('vendor_item_vendor', $vendor_id);
                $this->db->set('vendor_item_number', $item['vendor_item_num']);
                $this->db->set('vendor_item_name', $item['vendor_item_name']);
                $this->db->set('vendor_item_cost', floatval($item['su_price']));
                $this->db->set('vendor_item_exprint', $item['su_addl_price']);
                $this->db->set('vendor_item_setup', $item['su_new_setup']);
                $this->db->set('vendor_item_repeat', $item['su_repeat_setup']);
                $this->db->set('po_note', $item['po_notes']);
                $this->db->set('vendor_item_zipcode', $item['ship_zip']);
                $this->db->set('item_shipcountry', $this->config->item('default_country'));
                $this->db->set('item_shipstate','NJ');
                $this->db->set('item_shipcity','Clifton');
                $this->db->set('stand_days', $item['su_stad_days']);
                $this->db->set('rush1_days', $item['su_rush1_days']);
                $this->db->set('rush2_days', $item['su_rush2_days']);
                $this->db->set('rush1_price', $item['su_rush1_price']);
                $this->db->set('rush2_price', $item['su_rush2_price']);
                $this->db->set('pantone_match', $item['su_pantone']);
                $this->db->insert('sb_vendor_items');
                $vendor_item_id = $this->db->insert_id();
                if ($vendor_item_id > 0) {
                    $this->db->where('item_id', $newid);
                    $this->db->set('vendor_item_id', $vendor_item_id);
                    $this->db->update('sb_items');
                }
                for ($j=1; $j<6; $j++) {
                    if (intval($item['price_qty'.$j]) > 0) {
                        $sale = round($item['price_'.$j]*(1-$qtydisc['discount_val']/100),3);
                        $profit = null;
                        if ($item['su_price']>0) {
                            $profit = ($sale - $item['su_price']) * $item['price_qty'.$j];
                        }
                        $this->db->set('item_id', $newid);
                        $this->db->set('item_qty', $item['price_qty'.$j]);
                        $this->db->set('price', $item['price_'.$j]);
                        $this->db->set('sale_price', $sale);
                        $this->db->set('profit', $profit);
                        $this->db->insert('sb_promo_price');
                    }
                }
                // Insert Item Price
                $saleprint = round($item['add_print']*(1-$printdisc['discount_val']/100),3);
                $salenewsetup = round($item['new_setup']*(1-$newdisc['discount_val']/100),3);
                $salerepeat = round($item['repeat_setup']*(1-$repeatdisc['discount_val']/100),3);
                $salepantone = round($item['pantone_price']*(1-$pantdisc['discount_val']/100),3);
                $salerush1 = round($item['rush1_price']*(1-$rush1disc['discount_val']/100),3);
                $salerush2 = round($item['rush2_price']*(1-$rush2disc['discount_val']/100),3);
                $this->db->set('item_price_itemid', $newid);
                $this->db->set('item_price_print', $item['add_print']);
                $this->db->set('item_price_setup', $item['new_setup']);
                $this->db->set('item_price_repeat', $item['repeat_setup']);
                $this->db->set('item_price_pantone', $item['pantone_price']);
                $this->db->set('item_price_rush1', $item['rush1_price']);
                $this->db->set('item_price_rush2', $item['rush2_price']);
                $this->db->set('item_sale_print', $saleprint);
                $this->db->set('item_sale_setup', $salenewsetup);
                $this->db->set('item_sale_repeat', $salerepeat);
                $this->db->set('item_sale_pantone', $salepantone);
                $this->db->set('item_sale_rush1', $salerush1);
                $this->db->set('item_sale_rush2', $salerush2);
                $this->db->insert('sb_item_prices');
                // Item Colors
                $colors = explode(',', $item['options_val']);
                if ($colors > 0) {
                    $numpp = 1;
                    foreach ($colors as $color) {
                        $this->db->set('item_color_itemid', $newid);
                        $this->db->set('item_color', $color);
                        $this->db->set('item_color_order', $numpp);
                        $this->db->insert('sb_item_colors');
                        $numpp++;
                    }
                }
                // Item Imprints
                for ($i=1; $i<13; $i++) {
                    if (!empty($item['location_'.$i.'_name'])) {
                        echo $item['item_num'].' Locat '.$item['location_'.$i.'_name'].' Size '.$item['location_'.$i.'_size'].PHP_EOL;
                        $this->db->set('item_inprint_item', $newid);
                        $this->db->set('item_inprint_location', $item['location_'.$i.'_name']);
                        $this->db->set('item_inprint_size', $item['location_'.$i.'_size']);
                        $this->db->insert('sb_item_inprints');
                    }
                }
                // Shipping
                for ($i=1; $i<5; $i++) {
                    if (intval($item['box'.$i.'_qty']) > 0) {
                        $this->db->set('item_id', $newid);
                        $this->db->set('box_qty', $item['box'.$i.'_qty']);
                        $this->db->set('box_width', $item['box'.$i.'_width']);
                        $this->db->set('box_length', $item['box'.$i.'_length']);
                        $this->db->set('box_height', $item['box'.$i.'_height']);
                        $this->db->insert('sb_item_shipping');
                    }
                }
            }
        }
        echo 'Add similars '.PHP_EOL;
        // Build similars
        $this->db->select('*')->from('sritems_export');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            if ($item['managed'] > 0) {
                for ($i=1; $i<5; $i++) {
                    if (!empty($item['similar_'.$i])) {
                        $searchitem = substr($item['similar_'.$i],0,4);
                        $this->db->select('item_id')->from('sb_items')->where('brand','SR')->where('item_number', $searchitem);
                        $simdat = $this->db->get()->row_array();
                        if (ifset($simdat,'item_id', 0)>0) {
                            $this->db->set('item_similar_item', $item['managed']);
                            $this->db->set('item_similar_similar', $simdat['item_id']);
                            $this->db->insert('sb_item_similars');
                        }
                    }
                }
                echo 'Item '.$item['item_num'].' add similars '.PHP_EOL;
            }
            // Add Vector Image
        }
    }

    public function sritems_images() {
        $this->db->select('*')->from('sritems_export');
        $items = $this->db->get()->result_array();
        foreach ($items as $item) {
            // AI template
//            $path_fl = $this->config->item('item_aitemplate');
//            $path_sh = $this->config->item('item_aitemplate_relative');
//            $filename = '';
//            $templat = 'ai-temp_'.$item['item_num'].'_*.ai';
//            echo 'Item '.$item['item_num'].' Template '.$path_fl.$templat.PHP_EOL;
//            $chfiles = glob($path_fl.$templat);
//            if (count($chfiles)==1) {
//                $filename = str_replace($path_fl, $path_sh, $chfiles[0]);
//            } else {
//                $templat = 'ai-temp_'.$item['item_num'].'_*.pdf';
//                $chfiles = glob($templat);
//                if (count($chfiles)==1) {
//                    $filename = str_replace($path_fl, $path_sh, $chfiles[0]);
//                }
//            }
//            if (!empty($filename)) {
//                $this->db->where('item_id', $item['managed']);
//                $this->db->set('item_vector_img', $filename);
//                $this->db->update('sb_items');
//            }
            // Get imprints
//            $path_fl = $this->config->item('imprint_images_relative');
//            $path_sh = $this->config->item('imprint_images');
//            $this->db->select('*')->from('sb_item_inprints')->where('item_inprint_item', $item['managed']);
//            $imprints = $this->db->get()->result_array();
//            foreach ($imprints as $imprint) {
//                $filename = '';
//                $templat = $item['item_num'].'_*_'.str_replace([' ',', '],'_',strtolower($imprint['item_inprint_location'])).'.jpg';
//                echo 'Item '.$item['item_num'].' Template '.$path_fl.$templat.PHP_EOL;
//                $chfiles = glob($path_fl.$templat);
//                if (count($chfiles)==1) {
//                    $filename = str_replace($path_fl, $path_sh, $chfiles[0]);
//                }
//                if (!empty($filename)) {
//                    $this->db->where('item_inprint_id', $imprint['item_inprint_id']);
//                    $this->db->set('item_inprint_view', $filename);
//                    $this->db->update('sb_item_inprints');
//                }
//            }
            // Colors
            $preload_fl = $this->config->item('upload_path_preload').'items/';
            $itemname = $item['item_num'].'_'.strtolower(str_replace(' ','',str_replace(' Stress Balls','',$item['item_name'])));
            $template = $preload_fl.$itemname.'/*jpg';
            echo $template.PHP_EOL;
            $chimg = glob($preload_fl.$itemname.'/*jpg');
            var_dump($chimg);die();

        }
    }
}