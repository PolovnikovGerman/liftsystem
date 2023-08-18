<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Btitemdetails_model extends MY_Model
{
    protected $max_colors = 56;
    protected $max_images = 40;
    function __construct()
    {
        parent::__construct();
    }

    // Change item category
    public function itemdetails_change_category($sessiondata, $postdata, $session) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        if (ifset($postdata,'newval','')=='') {
            $item['category_id'] = NULL;
            $item['item_number'] = '';
            $item['item_numberone'] = '';
            $item['item_numbersec'] = '';
            $out['result'] = $this->success_result;
        } else {
            $this->load->model('categories_model');
            $res = $this->categories_model->get_srcategory_data($postdata['newval']);
            $out['msg'] = $res['msg'];
            if ($res['result']==$this->success_result) {
                $out['result'] = $this->success_result;
                $item['item_numberone'] = $res['data']['category_code'].'-';
                $item['item_numbersec'] = '';
            }
        }
        if ($out['result']==$this->success_result) {
            $out['item_numberone'] = $item['item_numberone'];
            $out['item_numbersec'] = $item['item_numbersec'];
            $sessiondata['item'] = $item;
            usersession($session, $sessiondata);
        }
        return $out;
    }
    // Change item status
    public function itemdetails_change_itemstatus($sessiondata, $postdata, $session) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        if ($item['item_active']==0) {
            $item['item_active'] = 1;
        } else {
            $item['item_active'] = 0;
        }
        $out['result'] = $this->success_result;
        $out['item_active'] = $item['item_active'];
        $sessiondata['item'] = $item;
        usersession($session, $sessiondata);
        return $out;
    }
    // Prepare colors and images for edit
    public function prepare_options_edit($sessiondata) {
        $colors = $sessiondata['colors'];
        $item = $sessiondata['item'];
        $images = $sessiondata['images'];
        $numidx = 1;
        $outcolors = [];
        foreach ($colors as $color) {
            $outcolors[] = $color;
            $numidx++;
        }
        if (empty($item['printshop_inventory_id'])) {
            if ($numidx < $this->max_colors) {
                for ($i=$numidx; $i < $this->max_colors; $i++) {
                    $outcolors[] = [
                        'item_color_id' => ($i)*(-1),
                        'item_color' => '',
                        'item_color_image' => '',
                        'item_color_order' => $i,
                    ];
                }
            }
        }
        $outimages = [];
        $numidx=1;
        foreach ($images as $image) {
            $outimages[] = $image;
            $numidx++;
        }
        if ($numidx < $this->max_images) {
            for ($i=$numidx; $i <= $this->max_images; $i++) {
                $outimages[] = [
                    'item_img_id' => $i*(-1),
                    'item_img_name' => '',
                    'item_img_order' => $i,
                    'item_img_label' => '',
                    'title' => '',
                ];
            }
        }
        return [
            'colors' => $outcolors,
            'images' => $outimages,
        ];
    }
    // Change item parameter
    public function itemdetails_change_iteminfo($sessiondata, $options, $sessionsid) {
        $out=['result'=>$this->error_result, 'msg' => 'Item Not Found'];
        $item = $sessiondata['item'];
        $fldname = ifset($options, 'fld','unknownfd');
        $newval=ifset($options, 'newval','');
        if (array_key_exists($fldname, $item)) {
            $out['result'] = $this->success_result;
            $item[$fldname] = $newval;
            if ($fldname=='item_numbersec') {
                $item['item_number'] = $item['item_numberone'].$item['item_numbersec'];
            }
            $sessiondata['item'] = $item;
            $out['item_active'] = $item['item_active'];
            usersession($sessionsid, $sessiondata);
        }
        return $out;
    }
    // Change item subcategory
    public function itemdetails_change_subcategory($sessiondata, $postdata, $session) {
        $out=['result'=>$this->error_result, 'msg' => 'Category Not Found'];
        $categories = $sessiondata['categories'];
        $catid = ifset($postdata,'fld','');
        if (!empty($catid)) {
            $find = 0;
            $idx = 0;
            foreach ($categories as $category) {
                if ($category['item_categories_id']==$catid) {
                    $find = 1;
                    $categories[$idx]['category_id'] = $postdata['newval'];
                    $sessiondata['categories'] = $categories;
                    usersession($session, $sessiondata);
                    $out['result'] = $this->success_result;
                    break;
                }
                $idx++;
            }
        }
        return $out;
    }
    // Change checkbox
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
    // Change similar
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
    // Change vendor
    public function itemdetails_change_vendor($sessiondata, $postdata, $sessionsid) {
        $out=['result' => $this->error_result,'msg' => 'Item Not Found'];
        $vendor_item = $sessiondata['vendor_item'];
        $vendor_id = ifset($postdata, 'newval', '-1');
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
    // Change Vendor item
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
    // Save additional images
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
    // Update add image
    public function itemdetails_save_updimages($sessiondata, $postdata, $session) {
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
    // Change Add image sort
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
    // Remove additional image
    public function itemdetails_addimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $images = $sessiondata['popup_images'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            foreach ($images as $image) {
                if ($image['item_img_id']==$imgidx) {
                    $find = 1;
                    $images[$idx]['item_img_name']='';
                    $images[$idx]['item_img_label'] = '';
                    break;
                } else {
                    $idx++;
                }
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['popup_images'] = $images;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }
    // Update Add image title
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
    // Add Option
    public function itemdetails_optionis_add($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $colors = $sessiondata['popup_colors'];
        $item = $sessiondata['item'];
        $newid = (count($colors)+1) * (-1);
        $neword = count($colors)+1;
        if ($item['option_images']==0) {
            $colors[] = [
                'item_color_id' => $newid,
                'item_color' => '',
                'item_color_image' => '',
                'item_color_order' => $neword,
            ];
        } else {
            $colors[] = [
                'item_color_id' => $newid,
                'item_color' => '',
                'item_color_image' => $postdata['newval'],
                'item_color_order' => $neword,
            ];
        }
        $sessiondata['popup_colors'] = $colors;
        usersession($session, $sessiondata);
        $out['result'] = $this->success_result;
        return $out;
    }
    // Update option image
    public function itemdetails_optionimages_update($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result,'msg' => 'Info not found'];
        $colors = $sessiondata['popup_colors'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $imgidx = str_replace(['reploptimg','addoptionimageslider'], '',$imgidx);
            $find = 0;
            $idx = 0;
            foreach ($colors as $color) {
                if ($color['item_color_id']==$imgidx) {
                    $find = 1;
                    $colors[$idx]['item_color_image'] = $postdata['newval'];
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
    // Update image sort
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
    // Delete option
    public function itemdetails_optimages_delete($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $colors = $sessiondata['popup_colors'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            // $numrow = 1;
            // $newimg = [];
            foreach ($colors as $color) {
                if ($color['item_color_id']==$imgidx) {
                    $colors[$idx]['item_color'] = '';
                    $colors[$idx]['item_color_image'] = '';
                    $find = 1;
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
    // Change option caption
    public function itemdetails_optimages_title($sessiondata, $postdata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Image Not Found'];
        $colors = $sessiondata['popup_colors'];
        $imgidx = ifset($postdata,'fldidx','');
        if (!empty($imgidx)) {
            $find = 0;
            $idx = 0;
            foreach ($colors as $color) {
                if ($color['item_color_id']==$imgidx) {
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
    // Rebuild images & colors
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
            if (!empty($item[''])) {
                $newcolors = $color;
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
        usersession($session, $sessiondata);
        return $out;
    }
    // Update vendor item price
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
    // update vendor item - price section
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
    // Update printshop
    public function change_printshopitem($data, $sessiondata, $session) {
        $out = ['result' => $this->error_result, 'msg' => 'Error during update vendor Item'];
        $item = $sessiondata['item'];
        $vendor_item = $sessiondata['vendor_item'];
        $inventory_item_id = ifset($data, 'newval','-1');
        if (intval($inventory_item_id) >= 0) {
            if (empty($inventory_item_id)) {
                $item['printshop_inventory_id'] = NULL;
                $item['option_images'] = 0;
                $vendor_item['vendor_item_blankcost'] = 0;
                $vendor_item['vendor_item_cost'] = 0;
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
                    $vendor_item['vendor_item_blankcost'] = 0;
                    $vendor_item['vendor_item_cost'] = $invitem['avg_price'];
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

    // Update Inventory color
    public function change_printshopcolor($postdata, $sessiondata, $session) {
        $out=['result' => $this->error_result, 'msg' => 'Info Not Found'];
        $color = ifset($postdata, 'color',0);
        if (!empty($color)) {
            $newval = ifset($postdata,'newval','');
            $colors = $sessiondata['colors'];
            $find = 0;
            $idx = 0;
            foreach ($colors as $item) {
                if ($item['item_color_id']==$color) {
                    $find=1;
                    $colors[$idx]['item_color'] = $newval;
                    break;
                }
                $idx++;
            }
            if ($find==1) {
                $out['result'] = $this->success_result;
                $sessiondata['colors'] = $colors;
                usersession($session, $sessiondata);
            }
        }
        return $out;
    }

    // Update item prices
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

    // Save item data
    public function save_itemdetails($sessiondata, $session, $user_id) {
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
            $item = $sessiondata['item'];
            $categories = $sessiondata['categories'];
            $similars = $sessiondata['similar'];
            $vendor_item = $sessiondata['vendor_item'];
            $vendor_prices = $sessiondata['vendor_price'];
            $images = $sessiondata['images'];
            $colors = $sessiondata['colors'];
            $prices = $sessiondata['prices'];
            $inprints = $sessiondata['inprints'];
            $shipboxes = $sessiondata['shipboxes'];
            // old dat
            $this->load->model('items_model');
            $oldres = $this->items_model->get_itemlist_details($item['item_id'], 0);
            $compare = 0;
            if ($oldres['result']==$this->success_result) {
                $olddata = $oldres['data'];
                $compare = 1;
            }
            if ($compare==1) {
                $history['keyinfo'] = $this->_keyinfo_diff($olddata, $item, $categories);
                $history['similar'] = $this->_similar_diff($olddata, $similars);
                $history['supplier'] = $this->_supplier_diff($olddata, $item, $vendor_item, $vendor_prices);
                $history['options'] = $this->_options_diff($olddata, $item, $images, $colors);
                $history['pricing'] = $this->_prices_diff($olddata, $item, $prices);
                $history['printing'] = $this->_custom_diff($olddata, $item, $inprints);
                $history['meta'] = $this->_meta_diff($olddata, $item);
                $history['shipping'] = $this->_shipping_diff($olddata, $item, $shipboxes);
            }
            // Lets go to save
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
            $this->db->set('item_sale', $item['item_sale']);
            $this->db->set('item_topsale', $item['item_topsale']);
            $this->db->set('item_minqty', empty($item['item_minqty']) ? NULL : intval($item['item_minqty']));
            $this->db->set('bullet1', empty($item['bullet1']) ? NULL : $item['bullet1']);
            $this->db->set('bullet2', empty($item['bullet2']) ? NULL : $item['bullet2']);
            $this->db->set('bullet3', empty($item['bullet3']) ? NULL : $item['bullet3']);
            $this->db->set('bullet4', empty($item['bullet4']) ? NULL : $item['bullet4']);
            $this->db->set('printshop_inventory_id', empty($item['printshop_inventory_id'])? NULL : $item['printshop_inventory_id']);
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
                $this->db->set('item_number', $item['item_number']);
                $this->db->set('brand','BT');
                $this->db->insert('sb_items');
                $item_id = $this->db->insert_id();
                if ($item_id > 0) {
                    $out['result'] = $this->success_result;
                    // Check code
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
            // Add categories
            foreach ($categories as $category) {
                if (empty($category['category_id'])) {
                    if ($category['item_categories_id'] > 0) {
                        $this->db->where('item_categories_id', $category['item_categories_id']);
                        $this->db->delete('sb_item_categories');
                    }
                } else {
                    $this->db->set('item_categories_categoryid', $category['category_id']);
                    if ($category['item_categories_id'] > 0) {
                        $this->db->where('item_categories_id', $category['item_categories_id']);
                        $this->db->update('sb_item_categories');
                    } else {
                        $this->db->set('item_categories_itemid', $item_id);
                        $this->db->insert('sb_item_categories');
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
            // Vector template
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
            // Remove objects
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
                    // if (floatval($row['vendprice']) != 0) {
                        $profit = ($row['base_cost'] - $row['vendprice']) * $row['base'];
                        // $profit_perc = $profit / ($row['base_cost'] * $row['base']) * 100;
                        $row['profit'] = round($profit, 0);
                        $row['profit_perc'] = round($profit / ($row['base_cost'] * $row['base']) * 100, 1);
                        $row['profit_class'] = profit_bgclass($row['profit_perc']);
                    //}
                } else {
                    // if (floatval($row['vendprice']) != 0) {
                        $row['profit'] = round($row['base_cost'] - $row['vendprice'], 2);
                        $row['profit_perc'] = round($row['profit'] / ($row['base_cost']) * 100, 1);
                        $row['profit_class'] = profit_bgclass($row['profit_perc']);
                    // }
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
        if ($item['option_images']==1) {
            if (empty($item['printshop_inventory_id'])) {
                $colors = $sessiondata['colors'];
                foreach ($colors as $color) {
                    if (!empty($color['item_color']) && empty($color['item_color_image'])) {
                        array_push($errmsg, 'Option '.$color['item_color'].' - empty Image');
                    }
                }
            }
        }
        if (count($errmsg) > 0) {
            $out['result'] = $this->error_result;
            $out['errmsg'] = $errmsg;
        }
        return $out;
    }

    private function _keyinfo_diff($olddata, $item, $categories) {
        $olditem = $olddata['item'];
        $oldcategs = $olddata['categories'];
        $info = [];
        if ($olditem['item_active']!==$item['item_active']) {
            if ($item['item_active']==1) {
                $info[]='Change Item Status on Active,';
            } else {
                $info[]='Change Item Status on Non-Active';
            }
        }
        if ($olditem['item_name']!==$item['item_name']) {
            $info[]='Change Item Name from "'.$olditem['item_name'].'" to "'.$item['item_name'].'"';
        }
        if ($olditem['item_template']!==$item['item_template']) {
            $info[]='Change Item Template from '.$olditem['item_template'].' to '.$item['item_template'];
        }
        if ($olditem['item_new']!==$item['item_new']) {
            if ($item['item_new']==1) {
                $info[]='Checked Tag New';
            } else {
                $info[]='Unchecked Tag New';
            }
        }
        if ($olditem['item_sale']!==$item['item_sale']) {
            if ($item['item_sale']==1) {
                $info[]='Checked Tag Sale';
            } else {
                $info[]='Unchecked Tag Sale';
            }
        }
        if ($olditem['item_topsale']!==$item['item_topsale']) {
            if ($item['item_topsale']==1) {
                $info[]='Checked Tag Top Seller';
            } else {
                $info[]='Unchecked Tag Top Seller';
            }
        }
        // Categories
        $this->load->model('categories_model');
        $idx = 0;
        foreach ($oldcategs as $oldcateg) {
            if ($oldcateg['category_id']!==$categories[$idx]['category_id']) {
                if (empty($categories[$idx]['cateegory_id'])) {
                    $info[]='Remove subcategory '.$oldcateg['category_name'];
                } else {
                    $datcat = $this->categories_model->get_category_data($categories[$idx]['category_id']);
                    if ($datcat['result']==$this->success_result) {
                        $newcat = $datcat['data'];
                        if ($oldcateg['item_categories_id']<0) {
                            $info[]='Add subcategory '.$newcat['category_name'];
                        } else {
                            $info[]='Change subcategory from '.$oldcateg['category_name'].' to '.$newcat['category_name'];
                        }
                    }
                }
            }
            $idx++;
        }
        if ($olditem['item_size']!==$item['item_size']) {
            $info[]='Change SIZE from '.$olditem['item_size'].' to '.$item['item_size'];
        }
        if ($olditem['item_material']!==$item['item_material']) {
            $info[]='Change MATERIAL from '.$olditem['item_material'].' on '.$item['item_material'];
        }
        if ($olditem['item_description1']!==$item['item_description1']) {
            $info[]='Change ITEM DESCRIPTION from "'.$olditem['item_description1'].'" to "'.$item['item_description1'].'"';
        }
        if ($olditem['bullet1']!==$item['bullet1']) {
            $info[]='Change BULLET POINT 1 "'.$olditem['bullet1'].'" to "'.$item['bullet1'].'"';
        }
        if ($olditem['bullet2']!==$item['bullet2']) {
            $info[]='Change BULLET POINT 2 "'.$olditem['bullet2'].'" to "'.$item['bullet2'].'"';
        }
        if ($olditem['bullet3']!==$item['bullet3']) {
            $info[]='Change BULLET POINT 3 "'.$olditem['bullet3'].'" to "'.$item['bullet4'].'"';
        }
        if ($olditem['bullet4']!==$item['bullet4']) {
            $info[]='Change BULLET POINT 4 "'.$olditem['bullet4'].'" to "'.$item['bullet4'].'"';
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
                    $info[]='Delete SIMILAR item # '.$numpp;
                } else {
                    $this->db->select('item_number, item_name');
                    $this->db->from('sb_items');
                    $this->db->where('item_id', $similars[$idx]['item_similar_similar']);
                    $simres = $this->db->get()->row_array();
                    if ($oldsimilar['item_similar_id']<0) {
                        $info[]='Add SIMILAR ITEM # '.$numpp.' '.$simres['item_number'].'-'.$simres['item_name'];
                    } else {
                        $info[]='Change SIMILAR ITEM # '.$numpp.' from '.$oldsimilar['item_number'].'-'.$oldsimilar['item_name'].' to '.$simres['item_number'].'-'.$simres['item_name'];
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
                    $info[]='Add VENDOR '.$vendres['data']['vendor_name'];
                } else {
                    $info[]='Change VENDOR from '.$oldvitem['vendor_name'].' to '.$vendres['data']['vendor_name'];
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
                    $info[]='Change Inventory item on '.$invres['item_num'].' '.$invres['item_name'];
                }
            }
        } else {
            if ($oldvitem['vendor_item_number']!==$vendor_item['vendor_item_number'] || $oldvitem['vendor_item_name']!==$vendor_item['vendor_item_name']) {
                $info[]='Change Supplier item from '.$oldvitem['vendor_item_number'].' to '.$vendor_item['vendor_item_number'].' '.$vendor_item['vendor_item_name'];
            }
        }
        if ($oldvitem['item_shipcountry']!==$vendor_item['item_shipcountry'] || $oldvitem['vendor_item_zipcode']!==$vendor_item['vendor_item_zipcode']) {
            $info[]='Change Supplier Shipping Address';
        }
        if ($oldvitem['vendor_item_cost']!==$vendor_item['vendor_item_cost']) {
            $info[]='Change Supplier Min price from '.MoneyOutput($oldvitem['vendor_item_cost']).' on '.MoneyOutput($vendor_item['vendor_item_cost']);
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
                $info[] = 'Remove Main Image';
            } else {
                if (empty($olditem['main_image'])) {
                    $info[] ='Add Main Image';
                } else {
                    $info[] = 'Replace Main Image';
                }
            }
        }
        if ($olditem['category_image']!==$item['category_image']) {
            if (empty($item['category_image'])) {
                $info[]='Remove Category Image';
            } else {
                if (empty($olditem['category_image'])) {
                    $info[] = 'Add Category Image';
                } else {
                    $info[] = 'Replace Category Image';
                }
            }
        }
        if ($olditem['top_banner']!==$item['top_banner']) {
            if (empty($item['top_banner'])) {
                $info[] = 'Remove Top Banner Image';
            } else {
                if (empty($olditem['top_banner'])) {
                    $info[] = 'Add Top Banner Image';
                } else {
                    $info[] = 'Replace Top Banner Image';
                }
            }
        }
        // Images
        foreach ($oldimgs as $oldimg) {
            $find = 0;
            foreach ($images as $image) {
                if ($oldimg['item_img_id']==$image['item_img_id']) {
                    if ($oldimg['item_img_name']!==$image['item_img_name']) {
                        $info[] = 'Replace Image # '.$oldimg['item_img_order'];
                    }
                    $find=1;
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Remove Image # '.$oldimg['item_img_order'];
            }
        }
        foreach ($images as $image) {
            if ($image['item_img_id'] < 0) {
                $info[] ='Add Image # '.$image['item_img_order'];
            }
        }
        if ($olditem['options']!==$item['options']) {
            $info[] = 'Change options on '.$item['options'];
        }
        if ($olditem['option_images']!==$item['option_images']) {
            if ($item['option_images']==0) {
                $info[] = 'Uncheck Require Images';
            } else {
                $info[] = 'Check Require Images';
            }
        }
        foreach ($oldcolors as $oldcolor) {
            $find=0;
            foreach ($colors as $color) {
                if ($oldcolor['item_color_id']==$color['item_color_id']) {
                    if ($oldcolor['item_color']!==$color['item_color']) {
                        $info[] = 'Change option # '.$oldcolor['item_color_order'].' from '.$oldcolor['item_color'].' to '.$color['item_color'];
                    }
                    $find = 1;
                    break;
                }
            }
            if ($find==0) {
                $info[] ='Delete option # '.$oldcolor['item_color_order'].' '.$oldcolor['item_color'];
            }
        }
        foreach ($colors as $color) {
            if ($color['item_color_id'] < 0 && !empty($color['item_color'])) {
                $info[]='Add option # '.$oldcolor['item_color_order'].' '.$color['item_color'];
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
                    if ($oldprice['item_qty']!==$price['item_qty'] || $oldprice['price']!==$price['price'] || $oldprice['sale_price']!==$price['sale_price']) {
                        $infstr = 'Changed Price for QTY '.$oldprice['item_qty'];
                        if ($oldprice['item_qty']!==$price['item_qty']) {
                            $infstr.=' on qty '.$price['item_qty'];
                        }
                        if ($oldprice['price']!==$price['price']) {
                            $infstr.=' price on '.MoneyOutput($price['price']);
                        }
                        if ($oldprice['sale_price']!==$price['sale_price']) {
                            $infstr.=' sale price on '.MoneyOutput($price['sale_price']);
                        }
                        $info[] = $infstr;
                    }
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Delete Price for QTY '.$oldprice['item_qty'];
            }
        }
        foreach ($prices as $price) {
            if ($price['promo_price_id'] < 0 && !empty($price['item_qty'])) {
                $infstr = 'Add Price on QTY '.$price['item_qty'];
                if (!empty($price['price'])) {
                    $infstr.=' price '.$price['price'];
                }
                if (!empty($price['sale_price'])) {
                    $infstr.=' sale price '.MoneyOutput($price['sale_price']);
                }
                $info[] = $infstr;
            }
        }
        if ($olditem['item_price_print']!==$item['item_price_print']) {
            if (empty($item['item_price_print'])) {
                $info[]='Delete Add\'l Prints price';
            } else {
                $info[] = 'Change Add\'l Prints price from '.MoneyOutput($olditem['item_price_print']).' to '.MoneyOutput($item['item_price_print']);
            }
        }
        if ($olditem['item_sale_print']!==$item['item_sale_print']) {
            if (empty($item['item_sale_print'])) {
                $info[] = 'Delete Add\'l Prints sale price';
            } else {
                $info[]= 'Change Add\'l Prints sale price from '.MoneyOutput($olditem['item_sale_print']).' on '.MoneyOutput($item['item_sale_print']);
            }
        }
        if ($olditem['item_price_setup']!==$item['item_price_setup']) {
            if (empty($item['item_price_setup'])) {
                $info[] = 'Delete New Setup price';
            } else {
                $info[] = 'Change New Setup price from '.MoneyOutput($olditem['item_price_setup']).' to '.MoneyOutput($item['item_price_setup']);
            }
        }
        if ($olditem['item_sale_setup']!==$item['item_sale_setup']) {
            if (empty($item['item_sale_setup'])) {
                $info[] = 'Delete New Setup sale price';
            } else {
                $info[] = 'Change New Setup sale price from '.MoneyOutput($olditem['item_sale_setup']).' to '.MoneyOutput($item['item_sale_setup']);
            }
        }
        if ($olditem['item_price_repeat']!==$item['item_price_repeat']) {
            if (empty($item['item_price_repeat'])) {
                $info[] = 'Delete Repeat Setup price';
            } else {
                $info[] = 'Change Repeat Setup price from '.MoneyOutput($olditem['item_price_repeat']).' to '.MoneyOutput($item['item_price_repeat']);
            }
        }
        if ($olditem['item_sale_repeat']!==$item['item_sale_repeat']) {
            if (empty($item['item_sale_repeat'])) {
                $info[] = 'Delete Repeat Setup sale price';
            } else {
                $info[] ='Change Repeat Setup sale price from '.MoneyOutput($olditem['item_sale_repeat']).' to '.MoneyOutput($item['item_sale_repeat']);
            }
        }
        if ($olditem['item_price_rush1']!==$item['item_price_rush1']) {
            if (empty($item['item_price_rush1'])) {
                $info[] = 'Delete Rush 1 price';
            } else {
                $info[] = 'Change Rush 1 price from '.MoneyOutput($olditem['item_price_rush1']).' to '.MoneyOutput($item['item_price_rush1']);
            }
        }
        if ($olditem['item_sale_rush1']!==$item['item_sale_rush1']) {
            if (empty($item['item_sale_rush1'])) {
                $info[] ='Delete Rush 1 sale price';
            } else {
                $info[] ='Change Rush 1 sale price from '.MoneyOutput($olditem['item_sale_rush1']).' to '.MoneyOutput($item['item_sale_rush1']);
            }
        }
        if ($olditem['item_price_rush2']!==$item['item_price_rush2']) {
            if (empty($item['item_price_rush2'])) {
                $info[] ='Delete Rush 2 price';
            } else {
                $info[] = 'Change Rush 2 price from '.MoneyOutput($olditem['item_price_rush2']).' to '.MoneyOutput($item['item_price_rush2']);
            }
        }
        if ($olditem['item_sale_rush2']!==$item['item_sale_rush2']) {
            if (empty($item['item_sale_rush2'])) {
                $info[] = 'Delete Rush 2 sale price';
            } else {
                $info[] ='Change Rush 2 sale price from '.MoneyOutput($olditem['item_sale_rush2']).' to '.MoneyOutput($item['item_sale_rush2']);
            }
        }
        if ($olditem['item_price_pantone']!==$item['item_price_pantone']) {
            if (empty($item['item_price_pantone'])) {
                $info[] = 'Delete Pantone Match price';
            } else {
                $info[] = 'Change Pantone Match price from '.MoneyOutput($olditem['item_price_pantone']).' to '.MoneyOutput($item['item_price_pantone']);
            }
        }
        if ($olditem['item_sale_pantone']!==$item['item_sale_pantone']) {
            if (empty($item['item_sale_pantone'])) {
                $info[] ='Delete Pantone Match sale price';
            } else {
                $info[] = 'Change Pantone Match sale price from '.MoneyOutput($olditem['item_sale_pantone']).' to '.MoneyOutput($item['item_sale_pantone']);
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
                $info[] = 'Delete Vector AI file';
            } else {
                if (empty($olditem['item_vector_img'])) {
                    $info[] = 'Add Vector AI file';
                } else {
                    $info[] = 'Change Vector AI file';
                }
            }
        }
        if ($olditem['imprint_method']!==$item['imprint_method']) {
            if (empty($item['imprint_method'])) {
                $info[] = 'Remove Customization Method';
            } else {
                if (empty($olditem['imprint_method'])) {
                    $info[] = 'Add Customization Method '.$item['imprint_method'];
                } else {
                    $info[] = 'Change Customization Method from '.$olditem['imprint_method'].' to '.$item['imprint_method'];
                }
            }
        }
        if ($olditem['imprint_color']!==$item['imprint_color']) {
            if (empty($item['imprint_color'])) {
                $info[] = 'Remove Print Colors';
            } else {
                if (empty($olditem['imprint_color'])) {
                    $info[] = 'Add Print colors '.$item['imprint_color'];
                } else {
                    $info[] = 'Change Print Colors from '.$olditem['imprint_color'].' to '.$item['imprint_color'];
                }
            }
        }
        foreach ($oldinprints as $oldinprint) {
            foreach ($inprints as $inprint) {
                $find = 0;
                if ($inprint['item_inprint_id']==$oldinprint['item_inprint_id']) {
                    $find=1;
                    if ($oldinprint['item_inprint_location']!==$inprint['item_inprint_location'] || $oldinprint['item_inprint_size']!==$inprint['item_inprint_size'] || $oldinprint['item_inprint_view']!==$inprint['item_inprint_view']) {
                        $infstr = 'Change Location ';
                        if ($oldinprint['item_inprint_location']!==$inprint['item_inprint_location']) {
                            $infstr.='name from '.$oldinprint['item_inprint_location'].' to '.$inprint['item_inprint_location'].' ';
                        }
                        if ($oldinprint['item_inprint_size']!==$inprint['item_inprint_size']) {
                            $infstr.=' size from '.$oldinprint['item_inprint_size'].' to '.$inprint['item_inprint_size'];
                        }
                        if ($oldinprint['item_inprint_view']!==$inprint['item_inprint_view']) {
                            $infstr.=' new locatio view';
                        }
                        $info[] = $infstr;
                    }
                    break;
                }
                if ($find==0) {
                    $info[] = 'Remove Location '.$oldinprint['item_inprint_location'];
                }
            }
        }
        foreach ($inprints as $inprint) {
            if ($inprint['item_inprint_id'] < 0) {
                $info[] = 'Add Location '.$inprint['item_inprint_location'].' size '.$inprint['item_inprint_size'];
            }
        }
        return $info;
    }

    private function _meta_diff($olddata, $item) {
        $info = [];
        $olditem = $olddata['item'];
        if ($olditem['item_meta_title']!==$item['item_meta_title']) {
            if (empty($item['item_meta_title'])) {
                $info[] = 'Remove Meta Title "'.$olditem['item_meta_title'].'"';
            } else {
                if (empty($olditem['item_meta_title'])) {
                    $info[] = 'Add Meta Title "'.$item['item_meta_title'].'"';
                } else {
                    $info[] = 'Change Meta Title from "'.$olditem['item_meta_title'].' to "'.$item['item_meta_title'].'"';
                }
            }
        }
        if ($olditem['item_metadescription']!==$item['item_metadescription']) {
            if (empty($item['item_metadescription'])) {
                $info[] = 'Remove Meta Descriptiion "'.$olditem['item_metadescription'].'"';
            } else {
                if (empty($olditem['item_metadescription'])) {
                    $info[] = 'Add Meta Description "'.$item['item_metadescription'].'"';
                } else {
                    $info[] = 'Change Meta Description from "'.$olditem['item_metadescription'].' to "'.$item['item_metadescription'].'"';
                }
            }
        }
        if ($olditem['item_url']!==$item['item_url']) {
            if (empty($item['item_url'])) {
                $info[] = 'Remove Item URL '.$olditem['item_url'];
            } else {
                if (empty($olditem['item_url'])) {
                    $info[] = 'Add Item URL '.$item['item_url'];
                } else {
                    $info[] = 'Change Item URL from '.$olditem['item_url'].' to '.$item['item_url'];
                }
            }
        }
        if ($olditem['item_metakeywords']!==$item['item_metakeywords']) {
            if (empty($item['item_metakeywords'])) {
                $info[] = 'Remove Meta Keywords "'.$olditem['item_metakeywords'].'"';
            } else {
                if (empty($olditem['item_metakeywords'])) {
                    $info[] = 'Add Meta Keywords "'.$item['item_metakeywords'].'"';
                } else {
                    $info[] = 'Change Meta Keywords from "'.$olditem['item_metakeywords'].' to "'.$item['item_metakeywords'].'"';
                }
            }
        }
        if ($olditem['item_keywords']!==$item['item_keywords']) {
            if (empty($item['item_keywords'])) {
                $info[] = 'Remove Internal Search Keywords "'.$olditem['item_keywords'].'"';
            } else {
                if (empty($olditem['item_keywords'])) {
                    $info[] = 'Add Internal Search Keywords "'.$item['item_keywords'].'"';
                } else {
                    $info[] = 'Change Internal Search Keywords from "'.$olditem['item_keywords'].' to "'.$item['item_keywords'].'"';
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
                $info[] = 'Remove Item Weight '.$olditem['item_weigth'];
            } else {
                if (empty($olditem['item_weigth'])) {
                    $info[] = "Add Item Weight ".$item['item_weigth'];
                } else {
                    $info[] = 'Change Item Weight from '.$olditem['item_weigth'].' to '.$item['item_weigth'];
                }
            }
        }
        if ($olditem['charge_pereach']!==$item['charge_pereach']) {
            if (empty($item['charge_pereach'])) {
                $info[] = 'Remove Extra $ Each '.$olditem['charge_pereach'];
            } else {
                if (empty($olditem['charge_pereach'])) {
                    $info[] = 'Add Extra $ Each '.$item['charge_pereach'];
                } else {
                    $info[] = 'Change Extra $ Each from '.$olditem['charge_pereach'].' to '.$item['charge_pereach'];
                }
            }
        }
        $numpp = 1;
        foreach ($oldshipboxes as $oldshipbox) {
            $find = 0;
            foreach ($shipboxes as $shipbox) {
                if ($oldshipbox['item_shipping_id']==$shipbox['item_shipping_id']) {
                    $find=1;
                    if ($oldshipbox['box_qty']!==$shipbox['box_qty'] || $oldshipbox['box_width']!==$shipbox['box_width'] || $oldshipbox['box_length']!==$shipbox['box_length'] || $oldshipbox['box_height']!==$shipbox['box_height']) {
                        $infstr = 'Change Ship Box '.chr(64 + $numpp);
                        if ($oldshipbox['box_qty']!==$shipbox['box_qty']) {
                            $infstr.=' QTY from '.$oldshipbox['box_qty'].' to '.$shipbox['box_qty'];
                        }
                        if ($oldshipbox['box_width']!==$shipbox['box_width']) {
                            $infstr.=' Width from '.$oldshipbox['box_width'].' to '.$shipbox['box_width'];
                        }
                        if ($oldshipbox['box_length']!==$shipbox['box_length']) {
                            $infstr.=' Length from '.$oldshipbox['box_length'].' to '.$shipbox['box_length'];
                        }
                        if ($oldshipbox['box_height']!==$shipbox['box_height']) {
                            $infstr.=' Height from '.$oldshipbox['box_height'].' to '.$shipbox['box_height'];
                        }
                        $info[] = $infstr;
                    }
                    break;
                }
            }
            if ($find==0) {
                $info[] = 'Remove Ship Box '.chr(64 + $numpp);
            }
            $numpp++;
        }
        foreach ($shipboxes as $shipbox) {
            if ($shipbox['item_shipping_id'] < 0 && !empty($shipbox['box_qty'])) {
                $info[] = 'Add Ship Box '.$shipbox['box_qty'].' Width '.$shipbox['box_width'].' Length '.$shipbox['box_length'].' Height '.$shipbox['box_height'];
            }
        }
        return $info;
    }

}