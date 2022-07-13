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
    }

    // Items List
    public function itemlistsearch() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $postdata = $this->input->post();

            $brand = ifset($postdata,'brand','ALL');
            $search = strtoupper(ifset($postdata, 'search', ''));
            $vendor = ifset($postdata,'vendor', '');
            $itemstatus = ifset($postdata, 'itemstatus', 0);

            $totals = $this->items_model->count_searchres($search, $brand, $vendor, $itemstatus);
            $mdata['totals'] = $totals;
            $mdata['totals_view'] = QTYOutput($totals).' Records';
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
            $options['brand'] = ifset($postdata,'brand','ALL');
            $options['search'] = strtoupper(ifset($postdata, 'search', ''));
            $options['vendor'] = ifset($postdata,'vendor', '');
            $options['itemstatus'] = ifset($postdata, 'itemstatus', 0);

            $res = $this->items_model->get_itemlists($options);
            $this->load->model('categories_model');
            $pageoptions = [
                'datas' => $res,
                'categories' => $this->categories_model->get_categories_list(),
                'brand' => $options['brand'],
            ];
            $mdata['content'] = $this->load->view('dbitems/itemslist_data_view', $pageoptions, TRUE);
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

    public function itemmainimage($item_id) {
        // $item_id=$this->input->get('id');
        $this->load->model('leadorder_model');
        $res = $this->leadorder_model->get_leadorder_itemimage($item_id);
        $content = '';
        if ($res['result']==$this->success_result) {
            $viewopt=$res['viewoptions'];
            $content=$this->load->view('redraw/viewsource_view',$viewopt, TRUE);
        }
        echo $content;
    }

    // Edit Item
    public function itemlistdetails() {
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
                $res = $this->items_model->get_relievers_itemscount($options);
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
            $res = $this->items_model->get_relievers_itemslist($options);
            if (count($res)==0) {
                $mdata['content'] = $this->load->view('relieveritems/emptydata_table_view', [], TRUE);
            } else {
                $mdata['content'] = $this->load->view('relieveritems/data_table_view', ['items' => $res], TRUE);
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
            $brand = 'SR';
            if ($item_id>=0) {
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
                    'item' => $data['item'],
                    'session_id' => $session_id,
                ];
                if ($editmode==0) {
                    $mdata['header'] = $this->load->view('relieveritems/header_view', $header_options, TRUE);
                } else {
                    $mdata['header'] = $this->load->view('relieveritems/header_edit', $header_options, TRUE);
                }
                // Key info
                if ($editmode==0) {
                    $keyinfo = $this->load->view('relieveritems/keyinfo_view',['item' => $data['item']], TRUE);
                    $similar = $this->load->view('relieveritems/similar_view',['items' => $data['similar']], TRUE);
                    $vendor_main = $this->load->view('relieveritems/vendormain_view',[],TRUE);
                    $vendor_prices = $this->load->view('relieveritems/vendorprices_view',['vendor_prices' => $data['vendor_price'], 'venditem' => $data['vendor_item']],TRUE);
                    $itemprices = $this->load->view('relieveritems/itemprices_view',['item' => $data['item'],'prices'=> $data['prices']],TRUE);
                    $otherimages = $this->load->view('relieveritems/otherimages_view',[],TRUE);
                    $optionsimg = $this->load->view('relieveritems/optionimages_view',[],TRUE);
                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                    ];
                    $itemimages = $this->load->view('relieveritems/images_view',$imagesoptions, TRUE);
                }
                $body_options = [
                    'keyinfo' => $keyinfo,
                    'similar' => $similar,
                    'vendor_main' => $vendor_main,
                    'vendor_prices' => $vendor_prices,
                    'itemprices' => $itemprices,
                    'itemimages' => $itemimages,
                ];
                $mdata['content'] = $this->load->view('relieveritems/itemdetailsbody_view', $body_options, TRUE);;

            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

}