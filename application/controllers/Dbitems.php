<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for Vendor Center

class Dbitems extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('items_model');
        $this->load->model('sritems_model');
    }

    // Items List
    public function itemlistsearch() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $options = [];
            $options['brand'] = 'BT';

            $options['search'] = strtoupper(ifset($postdata, 'search', ''));
            $options['vendor'] = ifset($postdata,'vendor', '');
            $options['itemstatus'] = ifset($postdata, 'itemstatus', 0);
            $options['category'] = ifset($postdata,'category', 0);
            $options['missinfo'] = ifset($postdata,'missinfo',0);
            $totals = $this->items_model->count_item_searchres($options);
            $mdata['totals'] = $totals;
            $mdata['totals_view'] = QTYOutput($totals).' item(s)';
            $category_label = '';
            if ($options['category'] > 0) {
                $this->load->model('categories_model');
                $catdat = $this->categories_model->get_srcategory_data($options['category']);
                if ($catdat['result']==$this->success_result) {
                    $categ = $catdat['data'];
                    $category_label = $categ['category_name'];
                }
            }
            $mdata['category_total'] = $category_label;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemlistsdata() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();
            $pagenum = ifset($postdata, 'offset', 0);
            $options = [];
            $options['limit'] = ifset($postdata, 'limit', 100);
            $options['offset'] = ($pagenum * $options['limit']);
            $options['order_by'] = ifset($postdata, 'order_by', 'item_number');
            $options['direct'] = ifset($postdata,'direction', 'asc');
            //$options['brand'] = ifset($postdata,'brand','ALL');
            $options['search'] = strtoupper(ifset($postdata, 'search', ''));
            $options['vendor'] = ifset($postdata,'vendor', '');
            $options['itemstatus'] = ifset($postdata, 'itemstatus', 0);
            $options['category_id'] = ifset($postdata, 'category', 0);
            $options['missinfo'] = ifset($postdata,'missinfo',0);
            $options['brand'] = 'BT';
            $res = $this->items_model->get_itemlists($options);
            // $this->load->model('categories_model');
            // $pageoptions = [
            //    'datas' => $res,
            //    'categories' => $this->categories_model->get_categories_list(),
            //    'brand' => 'BT',
            // ];
            // $mdata['content'] = $this->load->view('dbitems/itemslist_data_view', $pageoptions, TRUE);
            $expand = 1;
            if (count($res) > 17) {
                $expand = 0;
            }
            $mdata['content'] = $this->load->view('btitems/itemslist_data_view', ['items' => $res, 'expand' => $expand, ], TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemlistcategory() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $this->load->model('itemcategory_model');
            $res = $this->itemcategory_model->update_itemlistcategory($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemmainimage() {
        $item_id=$this->input->get('v');
        $this->load->model('items_model');
        $res = $this->items_model->get_item_mainimage($item_id);
        $content = '';
        if ($res['result']==$this->success_result) {
            $viewopt=$res['viewoptions'];
            $content=$this->load->view('redraw/viewsource_view',$viewopt, TRUE);
        }
        echo $content;
    }

    // Add / edit item
    public function itemlistdetails() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Item Not Send';
            $postdata = $this->input->post();
            $item_id = ifset($postdata, 'item_id', -1);
            $brand = ifset($postdata,'brand', 'BT');
            $editmode = ifset($postdata,'editmode', 0);
            $this->load->model('items_model');
            if ($item_id>=0) {
                // $editmode = 0;
                if ($item_id==0) {
                    $error = '';
                    $editmode = 1;
                    $data = $this->items_model->new_itemlist($brand);
                } else {
                    $res = $this->items_model->get_itemlist_details($item_id, $editmode);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = $res['data'];
                    }
                }
            }
            if ($error=='') {
                // Build HTML
                $session_id = uniq_link('15');
                usersession($session_id, $data);
                $this->load->model('categories_model');
                $categories = $this->categories_model->get_reliver_categories(['brand'=>'BT','active'=>1]);
                $header_options = [
                    'item' => $data['item'],
                    'session_id' => $session_id,
                    'categories' => $categories,
                ];
                if ($editmode==0) {
                    $mdata['header'] = $this->load->view('btitems/header_view', $header_options, TRUE);
                } else {
                    $mdata['header'] = $this->load->view('btitems/header_edit', $header_options, TRUE);
                }

                $subcategories = $this->categories_model->get_categories(['show_dropdown'=>2]);
                $this->load->model('prices_model');
                // $discounts = $this->prices_model->get_price_discounts();
                $this->load->model('vendors_model');
                $vendors = $this->vendors_model->get_vendors_list(['status'=>1]);
                if ($editmode==0) {
                    $keyinfo = $this->load->view('btitems/keyinfo_view',['item' => $data['item'],'categories'=>$data['categories']], TRUE);
                    $similar = $this->load->view('btitems/similar_view',['items' => $data['similar']], TRUE);
                    $vendor_main = $this->load->view('btitems/vendormain_view',['vendor_item' => $data['vendor_item'], 'item' => $data['item'],/* 'vendor' => $data['vendor']*/],TRUE);
                    $vendor_prices = $this->load->view('btitems/vendorprices_view',['vendor_prices' => $data['vendor_price'], 'venditem' => $data['vendor_item'], 'item' => $data['item']],TRUE);
                    $profit_view = $this->load->view('btitems/itemprice_profit_view',['item' => $data['item'],'prices'=> $data['prices']],TRUE);
                    $price_options = [
                        'item' => $data['item'],
                        'prices'=> $data['prices'],
                        'profit_view' => $profit_view,
                    ];
                    $itemprices = $this->load->view('btitems/itemprices_view', $price_options,TRUE);
                    $otherimages = $this->load->view('btitems/otherimages_view',['images' => $data['images'], 'imgcnt' => count($data['images'])],TRUE);
                    $optionsimg = $this->load->view('btitems/optionimages_view',['colors' => $data['colors'],'item' => $data['item']],TRUE);

                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                        'item' => $data['item'],
                    ];
                    $itemimages = $this->load->view('btitems/images_view',$imagesoptions, TRUE);
                    $locations = $this->load->view('btitems/printlocations_view',['locations' => $data['inprints']], TRUE);
                    $customview = $this->load->view('btitems/itemcustom_view',['item' => $data['item'], 'locations' => $locations], TRUE);
                    $metaview = $this->load->view('btitems/itemmeta_view',['item' => $data['item']], TRUE);
                    $shippingview = $this->load->view('btitems/itemship_view',['item' => $data['item'],'boxes' => $data['shipboxes']], TRUE);
                } else {
                    $this->load->model('shipping_model');
                    $country_list = $this->shipping_model->get_countries_list(['orderby'=>'sort']);
                    $keyinfo = $this->load->view('btitems/keyinfo_edit',['item' => $data['item'],'categories'=>$data['categories'], 'subcategories' => $subcategories], TRUE);
                    $simitems = $this->items_model->get_items(['item_active' => 1],'item_number','asc');
                    $similar = $this->load->view('btitems/similar_edit',['items' => $data['similar'],'similars' => $simitems], TRUE);
                    $vendoptions = [
                        'vendor_item' => $data['vendor_item'],
                        // 'vendor' => $data['vendor'],
                        'item' => $data['item'],
                        'vendors' => $vendors,
                        'countries' => $country_list,
                    ];
                    $vendor_main = $this->load->view('btitems/vendormain_edit', $vendoptions,TRUE);
                    $vendor_prices = $this->load->view('btitems/vendorprices_edit',['vendor_prices' => $data['vendor_price'], 'venditem' => $data['vendor_item'], 'item' => $data['item']],TRUE);
                    $profit_view = $this->load->view('btitems/itemprice_profit_view',['item' => $data['item'],'prices'=> $data['prices']],TRUE);
                    $price_options = [
                        'item' => $data['item'],
                        'prices'=> $data['prices'],
                        'profit_view' => $profit_view,
                    ];
                    $itemprices = $this->load->view('btitems/itemprices_edit', $price_options,TRUE);
                    $otherimages = $this->load->view('btitems/otherimages_view',['images' => $data['images'], 'imgcnt' => count($data['images'])],TRUE);
                    $optionsimg = $this->load->view('btitems/optionimages_view',['colors' => $data['colors'],'item' => $data['item']],TRUE);
                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                        'item' => $data['item'],
                    ];
                    $itemimages = $this->load->view('btitems/images_view',$imagesoptions, TRUE);
                    $locations = $this->load->view('btitems/printlocations_edit',['locations' => $data['inprints']], TRUE);
                    $customview = $this->load->view('btitems/itemcustom_edit',['item' => $data['item'], 'locations' => $locations], TRUE);
                    $metaview = $this->load->view('btitems/itemmeta_edit',['item' => $data['item']], TRUE);
                    $shippingview = $this->load->view('btitems/itemship_edit',['item' => $data['item'],'boxes' => $data['shipboxes']], TRUE);
                }
                $body_options = [
                    'keyinfo' => $keyinfo,
                    'similar' => $similar,
                    'vendor_main' => $vendor_main,
                    'vendor_prices' => $vendor_prices,
                    'itemprices' => $itemprices,
                    'itemimages' => $itemimages,
                    'customview' => $customview,
                    'metaview' => $metaview,
                    'shipping' => $shippingview,
                ];
                $mdata['content'] = $this->load->view('relieveritems/itemdetailsbody_view', $body_options, TRUE);;
                $mdata['editmode'] = $editmode;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Edit Item
    public function olditemlistdetails() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Item Not Send';
            $postdata = $this->input->post();
            $item_id = ifset($postdata, 'item_id', -1);
            $brand = ifset($postdata,'brand', 'SB');
            $editmode = ifset($postdata,'editmode', 0);
            $this->load->model('items_model');
            if ($item_id>=0) {
                // $editmode = 0;
                if ($item_id==0) {
                    $error = '';
                    $editmode = 1;
                    $data = $this->items_model->new_itemlist($brand);
                } else {
                    $res = $this->items_model->get_itemlist_details($item_id, $editmode);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = $res['data'];
                    }
                }
            }
            if ($error=='') {
                // Build HTML
                $session_id = uniq_link('15');
                usersession($session_id, $data);
                $header_options = [
                    'editmode' => $editmode,
                    'item' => $data['item'],
                    'session_id' => $session_id,
                ];
                $header_view = $this->load->view('dbitemdetails/header_view', $header_options, TRUE);
                $mdata['header'] = $header_view;
                // Body
                $left_options = [
                    'item' => $data['item'],
                    'similar' => $data['similar'],
                    'editmode' => $editmode,
                ];
                if ($editmode==1) {
                    $left_options['items'] = $this->items_model->get_item_list($item_id, $brand);
                }
                $design_view = $this->load->view('dbitemdetails/webdesign_view', $left_options, TRUE);
                $meta_view = $this->load->view('dbitemdetails/metadata_view', $left_options, TRUE);
                $internalsearch_view = $this->load->view('dbitemdetails/intersearch_view', $left_options, TRUE);;
                // Images
                $slider_options = [
                    'images' => $data['images'],
                    'limit' => count($data['images']),
                ];
                if ($editmode==0) {
                    $image_slider = $this->load->view('dbitemdetails/pictures_slider_view', $slider_options, TRUE);
                } else {
                    $image_slider = $this->load->view('dbitemdetails/pictures_slider_edit', $slider_options, TRUE);
                }
                $images_options = [
                    'editmode' => $editmode,
                    'image_slider' => $image_slider,
                ];
                $images_view = $this->load->view('dbitemdetails/images_view', $images_options, TRUE);
                // Vendor data
                $this->load->model('vendors_model');
                $vendors = $this->vendors_model->get_vendors(['vendor_type' => 'Supplier', 'vendor_status'=>1]);
                $vendor_options = [
                    'vendor_item' => $data['vendor_item'],
                    'vendor_price' => $data['vendor_price'],
                    'item' => $data['item'],
                    'vendors' => $vendors,
                    'editmode' => $editmode,
                ];
                $vendor_view = $this->load->view('dbitemdetails/vendor_view', $vendor_options, TRUE);
                // Prices
                $profit_view = $this->load->view('dbitemdetails/price_profit_view',['prices' => $data['prices'], 'item' => $data['item']], TRUE);
                $prices_options = [
                    'editmode' => $editmode,
                    'item' => $data['item'],
                    'prices' => $data['prices'],
                    'profit_view' => $profit_view,
                ];
                $prices_view = $this->load->view('dbitemdetails/prices_view', $prices_options, TRUE);
                // Kye Info
                $key_options = [
                    'item' => $data['item'],
                    'colors' => $data['colors'],
                    'editmode' => $editmode,
                ];
                $key_view = $this->load->view('dbitemdetails/keyinfo_view', $key_options, TRUE);
                // Inprints
                $inprintdata = $this->load->view('dbitemdetails/inprintdata_view',['inprints' => $data['inprints'],'editmode' => $editmode,], TRUE);
                $inprint_options = [
                    'item' => $data['item'],
                    'inprints' => $data['inprints'],
                    'editmode' => $editmode,
                    'inpritdata' => $inprintdata,
                ];
                $inprint_view = $this->load->view('dbitemdetails/inprintinfo_view', $inprint_options, TRUE);
                // ADV info
                if ($editmode==0) {
                    $advdata = $this->load->view('dbitemdetails/advimage_view', ['item' => $data['item']], TRUE);
                } else {
                    $advdata = $this->load->view('dbitemdetails/advimage_edit', ['item' => $data['item']], TRUE);
                }
                $advoptions = [
                    'advdata' => $advdata,
                    'editmode' => $editmode,
                ];
                $adv_view = $this->load->view('dbitemdetails/advinfo_view', $advoptions, TRUE);
                $options = [
                    'design_view' => $design_view,
                    'meta_view' => $meta_view,
                    'internalsearch_view' => $internalsearch_view,
                    'images_view' => $images_view,
                    'vendor_view' => $vendor_view,
                    'prices_view' => $prices_view,
                    'key_view' => $key_view,
                    'inprint_view' => $inprint_view,
                    'adv_view' => $adv_view,
                ];
                $mdata['content'] = $this->load->view('dbitemdetails/body_view', $options, TRUE);
                $mdata['editmode'] = $editmode;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function relieve_itemsearch() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = '';
            $options = [];
            $mdata['category'] = '';
            if (ifset($postdata, 'category', 0)>0) {
                // Get data about category
                $this->load->model('categories_model');
                $catdata = $this->categories_model->get_srcategory_data($postdata['category']);
                $error = $catdata['msg'];
                if ($catdata['result']==$this->success_result) {
                    $error = '';
                    $mdata['category'] = $catdata['data']['category_code'].' - '.$catdata['data']['category_name'];
                }
            }
            if ($error=='') {
                $options['category'] = ifset($postdata,'category',0);
                $options['search'] = ifset($postdata,'search', '');
                $options['status'] = ifset($postdata, 'status', 0);
                $options['vendor'] = ifset($postdata, 'vendor', 0);
                $options['misinfo'] = ifset($postdata, 'misinfo', 0);
                $res = $this->sritems_model->get_relievers_itemscount($options);
                $mdata['totals'] = $res;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function relieve_itemslist() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata=[];
            $error = '';
            $options = [];
            $options['category'] = ifset($postdata,'category',0);
            $options['search'] = ifset($postdata,'search', '');
            $options['status'] = ifset($postdata, 'status', 0);
            $options['vendor'] = ifset($postdata, 'vendor', 0);
            $options['misinfo'] = ifset($postdata, 'misinfo', 0);
            $pagenum = ifset($postdata, 'offset', 0);
            $limit = ifset($postdata, 'limit', 50);
            $options['offset'] = $pagenum * $limit;
            $options['limit'] = $limit;
            $options['order_by'] = ifset($postdata, 'order_by', 'item_number');
            $options['direct'] = ifset($postdata,'direction', 'asc');

            $res = $this->sritems_model->get_relievers_itemslist($options);
            if (count($res)==0) {
                $mdata['content'] = $this->load->view('relieveritems/emptydata_table_view', [], TRUE);
            } else {
                $expand = 1;
                if (count($res) > 17 ) {
                    $expand = 0;
                }
                $mdata['content'] = $this->load->view('relieveritems/data_table_view', ['items' => $res, 'expand' => $expand], TRUE);
            }

            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function relieve_item_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Item Not Send';
            $postdata = $this->input->post();
            $item_id = ifset($postdata, 'item_id', -1);
            $editmode = ifset($postdata,'editmode', 0);
            if ($item_id>=0) {
                if ($item_id==0) {
                    $error = '';
                    $editmode = 1;
                    $data = $this->sritems_model->new_sritemlist();
                } else {
                    $res = $this->sritems_model->get_itemlist_details($item_id, $editmode);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = $res['data'];
                    }
                }
            }
            if ($error=='') {
                // Build HTML
                $session_id = uniq_link('15');
                usersession($session_id, $data);
                $this->load->model('categories_model');
                $categories = $this->categories_model->get_reliver_categories(['brand'=>'SR']);
                $header_options = [
                    'item' => $data['item'],
                    'session_id' => $session_id,
                    'categories' => $categories,
                ];
                if ($editmode==0) {
                    $mdata['header'] = $this->load->view('relieveritems/header_view', $header_options, TRUE);
                } else {
                    $mdata['header'] = $this->load->view('relieveritems/header_edit', $header_options, TRUE);
                }
                // Key info

                $subcategories = $this->categories_model->get_reliver_subcategories();
                $this->load->model('prices_model');
                $discounts = $this->prices_model->get_price_discounts();
                $this->load->model('vendors_model');
                $vendors = $this->vendors_model->get_vendors_list(['status'=>1]);
                if ($editmode==0) {
                    $category = '';
                    if (!empty($data['item']['category_id'])) {
                        $catdat = $this->categories_model->get_srcategory_data($data['item']['category_id']);
                        if ($catdat['result']==$this->success_result) {
                            $data['item']['category'] = $catdat['data']['category_name'];
                        }
                    }
                    $keyinfo = $this->load->view('relieveritems/keyinfo_view',['item' => $data['item']], TRUE);
                    $similar = $this->load->view('relieveritems/similar_view',['items' => $data['similar']], TRUE);
                    $vendor_main = $this->load->view('relieveritems/vendormain_view',['vendor_item' => $data['vendor_item'],'vendor' => $data['vendor']],TRUE);
                    $vendor_prices = $this->load->view('relieveritems/vendorprices_view',['vendor_prices' => $data['vendor_price'], 'venditem' => $data['vendor_item'], 'item' => $data['item']],TRUE);
                    $profit_view = $this->load->view('relieveritems/itemprice_profit_view',['item' => $data['item'],'prices'=> $data['prices']],TRUE);
                    $netprices = $this->load->view('relieveritems/itemprice_net_view',['prices' => $data['prices']], TRUE);
                    $price_options = [
                        'item' => $data['item'],
                        'prices'=> $data['prices'],
                        'profit_view' => $profit_view,
                        'discounts' => $discounts,
                        'netprices' => $netprices,
                    ];
                    $itemprices = $this->load->view('relieveritems/itemprices_view', $price_options,TRUE);
                    $otherimages = $this->load->view('relieveritems/otherimages_view',['images' => $data['images'], 'imgcnt' => count($data['images'])],TRUE);
                    $optionsimg = $this->load->view('relieveritems/optionimages_view',['imgoptions' => $data['option_images']],TRUE);
                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                        'item' => $data['item'],
                    ];
                    $itemimages = $this->load->view('relieveritems/images_view',$imagesoptions, TRUE);
                    $locations = $this->load->view('relieveritems/printlocations_view',['locations' => $data['inprints']], TRUE);
                    $customview = $this->load->view('relieveritems/itemcustom_view',['item' => $data['item'], 'locations' => $locations], TRUE);
                    $metaview = $this->load->view('relieveritems/itemmeta_view',['item' => $data['item']], TRUE);
                    $shippingview = $this->load->view('relieveritems/itemship_view',['item' => $data['item'],'boxes' => $data['shipboxes']], TRUE);
                } else {
                    $keyinfo = $this->load->view('relieveritems/keyinfo_edit',['item' => $data['item'],'subcategories' => $subcategories], TRUE);
                    $simitems = $this->sritems_model->get_relievers_itemslist(['status' => 1,'order_by' => 'item_number']);
                    $similar = $this->load->view('relieveritems/similar_edit',['items' => $data['similar'],'similars' => $simitems], TRUE);
                    $vendoptions = [
                        'vendor_item' => $data['vendor_item'],
                        'vendor' => $data['vendor'],
                        'item' => $data['item'],
                        'vendors' => $vendors,
                    ];
                    $vendor_main = $this->load->view('relieveritems/vendormain_edit', $vendoptions,TRUE);
                    $vendor_prices = $this->load->view('relieveritems/vendorprices_edit',['vendor_prices' => $data['vendor_price'], 'venditem' => $data['vendor_item'], 'item' => $data['item']],TRUE);
                    $profit_view = $this->load->view('relieveritems/itemprice_profit_view',['item' => $data['item'],'prices'=> $data['prices']],TRUE);
                    $netprices = $this->load->view('relieveritems/itemprice_net_view',['prices' => $data['prices']], TRUE);
                    $price_options = [
                        'item' => $data['item'],
                        'prices'=> $data['prices'],
                        'profit_view' => $profit_view,
                        'discounts' => $discounts,
                        'netprices' => $netprices,
                    ];
                    $itemprices = $this->load->view('relieveritems/itemprices_edit', $price_options,TRUE);
                    $otherimages = $this->load->view('relieveritems/otherimages_view',['images' => $data['images'], 'imgcnt' => count($data['images'])],TRUE);
                    $optionsimg = $this->load->view('relieveritems/optionimages_view',['imgoptions' => $data['option_images']],TRUE);
                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                        'item' => $data['item'],
                    ];
                    $itemimages = $this->load->view('relieveritems/images_view',$imagesoptions, TRUE);
                    $locations = $this->load->view('relieveritems/printlocations_edit',['locations' => $data['inprints']], TRUE);
                    $customview = $this->load->view('relieveritems/itemcustom_edit',['item' => $data['item'], 'locations' => $locations], TRUE);
                    $metaview = $this->load->view('relieveritems/itemmeta_edit',['item' => $data['item']], TRUE);
                    $shippingview = $this->load->view('relieveritems/itemship_edit',['item' => $data['item'],'boxes' => $data['shipboxes']], TRUE);
                }
                $body_options = [
                    'keyinfo' => $keyinfo,
                    'similar' => $similar,
                    'vendor_main' => $vendor_main,
                    'vendor_prices' => $vendor_prices,
                    'itemprices' => $itemprices,
                    'itemimages' => $itemimages,
                    'customview' => $customview,
                    'metaview' => $metaview,
                    'shipping' => $shippingview,
                ];
                $mdata['content'] = $this->load->view('relieveritems/itemdetailsbody_view', $body_options, TRUE);;
                $mdata['editmode'] = $editmode;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


}