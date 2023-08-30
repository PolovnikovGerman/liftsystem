<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Btitemdetails extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('btitemdetails_model');
    }

    // Preview Images, colors
    public function btitem_images_view() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $error = '';
                $item = $sessiondata['item'];
                $main_view = $this->load->view('btitems/popup_mainimage_view',['item' => $item], TRUE);
                $images = $sessiondata['images'];
                $cntimages = count($images);
                $addslider = $this->load->view('btitems/popup_addimageslder_view',['images' => $images,'cntimages' => $cntimages], TRUE);
                $add_view = $this->load->view('btitems/popup_addimage_edit',['slider' => $addslider], TRUE);
                $colors = $sessiondata['colors'];
                $colorslider = $this->load->view('btitems/popup_optionimageslider_view',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images']], TRUE);
                $optionview = $this->load->view('btitems/popup_options_view',['item' => $item, 'slider' => $colorslider], TRUE);
                $colorview = 1;
                $mdata['header'] = 'IMAGES & OPTIONS:';
                $options = [
                    'main_view' => $main_view,
                    'add_view' => $add_view,
                    'options_view' => $optionview,
                    'colorview' => $colorview,
                    'mode' => 'view',
                ];
                $mdata['content'] = $this->load->view('btitems/popup_image_edit',$options, TRUE);

            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change item category
    public function change_item_category() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_category($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['item_numberone'] = $res['item_numberone'];
                    $mdata['item_numbersec'] = $res['item_numbersec'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Item Status
    public function change_item_status() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_itemstatus($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['item_active'] = $res['item_active'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Item parameter
    public function change_btitem() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_iteminfo($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['item_active'] = $res['item_active'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Item category
    public function change_item_subcategory() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_subcategory($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Item checkbox value
    public function change_btitem_checkbox() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_itemcheck($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['newval'] = $res['newval'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Similar
    public function change_btsimilar() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_similar($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Vendor
    public function change_btvendor() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_vendor($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                    $sessiondata = usersession($session);
                    $item = $sessiondata['item'];
                    $vendor_item = $sessiondata['vendor_item'];
                    $vendor_price = $sessiondata['vendor_price'];
                    $colors = $sessiondata['colors'];
                    if ($res['internal']==1) {
                        $this->load->model('inventory_model');
                        $itemlist = $this->inventory_model->get_inventory_itemslist();
                        $mdata['vendoritemview'] = $this->load->view('btitems/vendoritem_inventory_edit', ['item' => $item, 'itemlists' => $itemlist], TRUE);
                        $mdata['vendor_price'] = $this->load->view('btitems/vendorprices_view',['vendor_prices' => $vendor_price, 'venditem' => $vendor_item, 'item' => $item],TRUE);
                        $mdata['colors'] = $this->load->view('btitems/printshopcolors_view',['colors' => $colors,'item' => $item],TRUE);
                    } else {
                        $mdata['vendoritemview'] = $this->load->view('btitems/vendoritem_data_edit',['vendor_item' => $vendor_item], TRUE);
                        $mdata['vendor_price'] = $this->load->view('btitems/vendorprices_edit',['vendor_prices' => $vendor_price, 'venditem' => $vendor_item, 'item' => $item],TRUE);
                        $mdata['colors'] = $this->load->view('btitems/optionimages_view',['colors' => $colors, 'item' => $item],TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Vendor item
    public function vendoritem_check() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_check_vendoritem($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                    $sessiondata=usersession($session);
                    $vendor = $sessiondata['vendor'];
                    $vendor_item = $sessiondata['vendor_item'];
                    $mdata['vendor_id'] = $vendor['vendor_id'];
                    $mdata['shipaddr_country'] = $vendor['shipaddr_country'];
                    $mdata['vendor_zipcode'] = $vendor['vendor_zipcode'];
                    $mdata['shipaddr_state'] = $vendor['shipaddr_state'];
                    $mdata['po_note'] = $vendor['po_note'];
                    $mdata['vendor_item_number'] = $vendor_item['vendor_item_number'];
                    $mdata['vendor_item_name'] = $vendor_item['vendor_item_name'];
                    $vendor_price = $sessiondata['vendor_price'];
                    $item = $sessiondata['item'];
                    $vprice_options = [
                        'vendor_prices' => $vendor_price,
                        'venditem' => $vendor_item,
                        'item' => $item,
                    ];
                    $mdata['vendor_prices'] = $this->load->view('relieveritems/vendorprices_edit',$vprice_options,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Open images for edit
    public function btitem_images_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $error = '';
                $item = $sessiondata['item'];
                $main_view = $this->load->view('btitems/popup_mainimage_edit',['item' => $item], TRUE);
                $this->load->model('items_model');
                $res = $this->items_model->prepare_options_edit($sessiondata);
                // $images = $sessiondata['images'];
                $images = $res['images'];
                $colors = $res['colors'];
                $sessiondata['popup_colors'] = $colors;
                $sessiondata['popup_images'] = $images;
                usersession($session, $sessiondata);
                $cntimages = count($images);
                $addslider = $this->load->view('btitems/popup_addimageslder_edit',['images' => $images,'cntimages' => $cntimages], TRUE);
                $add_view = $this->load->view('btitems/popup_addimage_edit',['slider' => $addslider], TRUE);
                $colorslider = $this->load->view('btitems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images'], 'inventory' => $item['printshop_inventory_id']], TRUE);
                $optionview = $this->load->view('btitems/popup_options_edit',['item' => $item, 'slider' => $colorslider], TRUE);
                $colorview = 1;
                $mdata['header'] = 'IMAGES & OPTIONS:';
                $options = [
                    'main_view' => $main_view,
                    'add_view' => $add_view,
                    'options_view' => $optionview,
                    'colorview' => $colorview,
                    'mode' => 'edit',
                ];
                $mdata['content'] = $this->load->view('btitems/popup_image_edit',$options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Save Item Images
    public function save_btimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_iteminfo($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $item = $sessiondata['item'];
                    $mdata['content'] = $this->load->view('btitems/popup_mainimage_edit',['item' => $item], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Update additional image
    public function save_btupdaddimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_save_updimages($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['popup_images'];
                    $numimgs = count($images);
                    $mdata['content'] = $this->load->view('btitems/popup_addimageslder_edit',['images' => $images, 'cntimages' => $numimgs], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Update add items sort
    public function save_btaddimagesort() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_save_imagessort($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['popup_images'];
                    $numimgs = count($images);
                    $mdata['content'] = $this->load->view('btitems/popup_addimageslder_edit',['images' => $images, 'cntimages' => $numimgs], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Remove additional image
    public function save_btaddimagedel() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_addimages_delete($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['popup_images'];
                    $numimgs = count($images);
                    $mdata['content'] = $this->load->view('btitems/popup_addimageslder_edit',['images' => $images, 'cntimages' => $numimgs], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Add item title
    public function save_btaddimagetitle() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                // $res = $this->sritems_model->itemdetails_addimages_title($sessiondata, $postdata, $session);
                $res = $this->btitemdetails_model->itemdetails_addimages_title($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Color
    // Change Options Images
    public function change_options_checkbox() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_change_itemcheck($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['newval'] = $res['newval'];
                    $sessiondata = usersession($session);
                    $colors = $sessiondata['popup_colors'];
                    $item = $sessiondata['item'];
                    $mdata['slideroptions'] = $this->load->view('btitems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images'], 'inventory' => $item['printshop_inventory_id']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    public function save_addoptionis() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_optionis_add($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $colors = $sessiondata['popup_colors'];
                    $item = $sessiondata['item'];
                    if ($item['option_images']==1) {
                        // With Images
                        $mdata['content'] = $this->load->view('btitems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors)], TRUE);
                    } else {
                        // Text only
                        $mdata['content'] = $this->load->view('btitems/popup_optiontext_edit',['colors' => $colors], TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_updoptimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_optionimages_update($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $colors = $sessiondata['popup_colors'];
                    $item = $sessiondata['item'];
                    $mdata['content'] = $this->load->view('btitems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images'], 'inventory' => $item['printshop_inventory_id']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_optionsort() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_save_optimagessort($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $colors = $sessiondata['popup_colors'];
                    $item = $sessiondata['item'];
                    $mdata['content'] = $this->load->view('btitems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images'], 'inventory' => $item['printshop_inventory_id']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_optiondel() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_optimages_delete($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $colors = $sessiondata['popup_colors'];
                    $item = $sessiondata['item'];
                    $mdata['content'] = $this->load->view('btitems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images'], 'inventory' => $item['printshop_inventory_id']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_optiontitle() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_optimages_title($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Rebuild Images view
    public function item_images_rebuild() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->item_images_rebuild($sessiondata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $item = $sessiondata['item'];
                    $images = $sessiondata['images'];
                    $colors = $sessiondata['colors'];
                    $otherimages = $this->load->view('btitems/otherimages_view',['images' => $images, 'imgcnt' => count($images)],TRUE);
                    $optionsimg = $this->load->view('btitems/optionimages_view',['colors' => $colors,'item' => $item],TRUE);
                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                        'item' => $item,
                    ];
                    $mdata['content'] = $this->load->view('btitems/images_view',$imagesoptions, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Vendor Item Price
    public function change_btvendorprice() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_vendor_price($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change vendor item - price section
    public function change_btvendoritemprice() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_vendoritem_price($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                    if ($res['address']==1) {
                        $mdata['shipstate'] = $res['state'];
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change prices
    public function change_btitemprice() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_item_price($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Item - price section
    public function change_btitempriceval() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_item_priceval($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Shipbox
    public function change_btshipbox() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_shipbox($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Add Printlocation
    public function itemprintloc_add() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_printloc_add($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('btitems/printlocations_edit',['locations' => $res['inprints']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Remove prit location
    public function remove_printlocat() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_printloc_delete($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('btitems/printlocations_edit',['locations' => $res['inprints']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Save location view
    public function save_printlocatview() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_printloc_view($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('btitems/printlocations_edit',['locations' => $res['inprints']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change imprit location
    public function itemprintloc_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_printloc_edit($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Vector file
    public function save_vectorfile() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->btitemdetails_model->itemdetails_vectorfile($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if (empty($res['vector'])) {
                        $mdata['content'] = '<div id="addvectorfile"></div>';
                    } else {
                        $mdata['content'] = '<div class="vendorfile_view" data-link="'.$res['vector'].'"><i class="fa fa-search"></i></div><div class="vendorfile_delete"><i class="fa fa-trash"></i></div>';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change printshop item
    public function change_printshopitem() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->btitemdetails_model->change_printshopitem($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // prepare views
                    $mdata = $this->_prepare_price_response($session_id);
                    $mdata['printshop_name'] = $res['printshop_name'];
                    $mdata['vendorprice'] = $this->load->view('btitems/vendorprices_view',['vendor_prices' => $res['vendor_price'], 'venditem' => $res['vendor_item'], 'item' => $res['item']],TRUE);
                    // Colors
                    $mdata['colorsview'] = $this->load->view('btitems/printshopcolors_view',['colors' => $res['colors'],'item' => $res['item']],TRUE);
                    if ($res['item']['option_images']==1) {
                        $mdata['imgoptions'] = '<i class="fa fa-check-square"></i>';
                    } else {
                        $mdata['imgoptions'] = '<i class="fa fa-square-o"></i>';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_printshopcolor() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->btitemdetails_model->change_printshopcolor($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save item
    public function save_itemdetails() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $error=$this->session_error;
            $session_id = ifset($postdata, 'session', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->btitemdetails_model->save_itemdetails($session_data, $session_id, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    private function _prepare_price_response($session) {
        $sessiondata = usersession($session);
        $prices = $sessiondata['prices'];
        $item = $sessiondata['item'];
        $mdata = [];
        // $mdata['netprices'] = $this->load->view('relieveritems/itemprice_net_view',['prices' => $prices], TRUE);
        $mdata['profit'] = $this->load->view('btitems/itemprice_profit_view',['item' => $item,'prices'=> $prices],TRUE);
        $mdata['saleprint'] = $item['item_sale_print'];
        $mdata['salesetup'] = $item['item_sale_setup'];
        $mdata['salerepeat'] = $item['item_sale_repeat'];
        $mdata['salerush1'] = $item['item_sale_rush1'];
        $mdata['salerush2'] = $item['item_sale_rush2'];
        $mdata['salepantone'] = $item['item_sale_pantone'];
        return $mdata;
    }

}