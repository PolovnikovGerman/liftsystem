<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Controller for Vendor Center

class Sritemdetails extends MY_Controller
{
    private $session_error = 'Edit session lost. Please, reload page';

    public function __construct()
    {
        parent::__construct();
        $this->load->model('sritems_model');
    }

    public function sritem_images_view() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $error = '';
                $item = $sessiondata['item'];
                $main_view = $this->load->view('relieveritems/popup_mainimage_view', ['item' => $item], TRUE);
                $images = $sessiondata['images'];
                $cntimages = count($images);
                $addslider = $this->load->view('relieveritems/popup_addimageslder_view', ['images' => $images, 'cntimages' => $cntimages], TRUE);
                $add_view = $this->load->view('relieveritems/popup_addimage_edit', ['slider' => $addslider], TRUE);
                $colors = $sessiondata['colors'];
                $colorslider = $this->load->view('relieveritems/popup_optionimageslider_view', ['colors' => $colors, 'cntimages' => count($colors), 'image' => $item['option_images']], TRUE);
                $optionview = $this->load->view('relieveritems/popup_options_view', ['item' => $item, 'slider' => $colorslider], TRUE);
                $colorview = 1;
                $mdata['header'] = 'IMAGES & OPTIONS:';
                $options = [
                    'main_view' => $main_view,
                    'add_view' => $add_view,
                    'options_view' => $optionview,
                    'colorview' => $colorview,
                    'mode' => 'view',
                ];
                $mdata['content'] = $this->load->view('relieveritems/popup_image_edit', $options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_relive_itemcategory() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_change_category($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['item_number'] = $res['item_number'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_relive_item() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_change_iteminfo($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_relive_checkbox() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_change_itemcheck($sessiondata, $postdata, $session);
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

    function change_relive_similar() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_change_similar($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_relive_vendor() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_change_vendor($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $data = $res['data'];
                    $mdata = $this->_prepare_price_response($session);
                    $sessiondata = usersession($session);
                    $item = $sessiondata['item'];
                    $vendor_item = $sessiondata['vendor_item'];
                    $vendor_price = $sessiondata['vendor_price'];
                    $colors = $sessiondata['colors'];
                    if ($res['internal']==1) {
                        $this->load->model('inventory_model');
                        $itemlist = $this->inventory_model->get_inventory_itemslist();
                        $mdata['vendoritemview'] = $this->load->view('relieveritems/vendoritem_inventory_edit', ['item' => $item, 'itemlists' => $itemlist], TRUE);
                        $mdata['vendor_price'] = $this->load->view('relieveritems/vendorprices_view',['vendor_prices' => $vendor_price, 'venditem' => $vendor_item, 'item' => $item],TRUE);
                        $mdata['colors'] = $this->load->view('relieveritems/optionimages_view',['colors' => $colors,'item' => $item],TRUE);
                        // printshopcolors_view
                    } else {
                        $mdata['vendoritemview'] = $this->load->view('relieveritems/vendoritem_data_edit',['vendor_item' => $vendor_item], TRUE);
                        $mdata['vendor_price'] = $this->load->view('relieveritems/vendorprices_edit',['vendor_prices' => $vendor_price, 'venditem' => $vendor_item, 'item' => $item],TRUE);
                        $mdata['colors'] = $this->load->view('relieveritems/optionimages_view',['colors' => $colors, 'item' => $item],TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_printshopitem() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session', 'defsess');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->sritems_model->change_printshopitem($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // prepare views
                    $mdata = $this->_prepare_price_response($session_id);
                    $mdata['printshop_name'] = $res['printshop_name'];
                    $mdata['vendorprice'] = $this->load->view('relieveritems/vendorprices_inventory_edit',['vendor_prices' => $res['vendor_price'], 'venditem' => $res['vendor_item'], 'item' => $res['item']],TRUE);
                    // Colors
                    $mdata['colorsview'] = $this->load->view('relieveritems/optionimages_view',['colors' => $res['colors'], 'item' => $res['item']],TRUE);
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

    public function relive_vendoritem_check() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata,'session','unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_check_vendoritem($sessiondata, $postdata, $session);
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

    public function relive_images_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $error = '';
                $this->load->model('items_model');
                $res = $this->items_model->prepare_options_edit($sessiondata);
                $images = $res['images'];
                $colors = $res['colors'];
                $sessiondata['popup_colors'] = $colors;
                $sessiondata['popup_images'] = $images;
                usersession($session, $sessiondata);
                $item = $sessiondata['item'];
                $main_view = $this->load->view('relieveritems/popup_mainimage_edit',['item' => $item], TRUE);
                $cntimages = count($images);
                $addslider = $this->load->view('relieveritems/popup_addimageslder_edit',['images' => $images,'cntimages' => $cntimages], TRUE);
                $add_view = $this->load->view('relieveritems/popup_addimage_edit',['slider' => $addslider], TRUE);
                $colorslider = $this->load->view('relieveritems/popup_optionimageslider_edit',['colors' => $colors,'cntimages' => count($colors), 'image' => $item['option_images'], 'inventory' => $item['printshop_inventory_id']], TRUE);
                $optionview = $this->load->view('relieveritems/popup_options_edit',['item' => $item, 'slider' => $colorslider], TRUE);
                $colorview = 1;
                $mdata['header'] = 'IMAGES & OPTIONS:';
                $options = [
                    'main_view' => $main_view,
                    'add_view' => $add_view,
                    'options_view' => $optionview,
                    'colorview' => $colorview,
                    'mode' => 'edit',
                ];
                $mdata['content'] = $this->load->view('relieveritems/popup_image_edit',$options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_image() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_change_iteminfo($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $item = $sessiondata['item'];
                    $mdata['content'] = $this->load->view('relieveritems/popup_mainimage_edit',['item' => $item], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_addimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_save_addimages($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['images'];
                    $cntimages = count($images);
                    $mdata['content'] = $this->load->view('relieveritems/popup_addimageslder_edit',['images' => $images,'cntimages'=>$cntimages], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_updaddimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_save_updimages($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['images'];
                    $numimgs = count($images);
                    $mdata['content'] = $this->load->view('relieveritems/popup_addimageslder_edit',['images' => $images, 'cntimages' => $numimgs], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_addimagesort() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_save_imagessort($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['images'];
                    $numimgs = count($images);
                    $mdata['content'] = $this->load->view('relieveritems/popup_addimageslder_edit',['images' => $images, 'cntimages' => $numimgs], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_addimagedel() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_addimages_delete($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['images'];
                    $numimgs = count($images);
                    $mdata['content'] = $this->load->view('relieveritems/popup_addimageslder_edit',['images' => $images, 'cntimages' => $numimgs], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_addimagetitle() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_addimages_title($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_addoptionimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_optionimages_add($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['option_images'];
                    $mdata['content'] = $this->load->view('relieveritems/popup_optionimageslider_edit',['images' => $images,'cntimages' => count($images)], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_updoptimage() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_optionimages_update($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['option_images'];
                    $mdata['content'] = $this->load->view('relieveritems/popup_optionimageslider_edit',['images' => $images,'cntimages' => count($images)], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_optimagesort() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_save_optimagessort($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['option_images'];
                    $mdata['content'] = $this->load->view('relieveritems/popup_optionimageslider_edit',['images' => $images,'cntimages' => count($images)], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_optimagedel() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_optimages_delete($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $images = $sessiondata['option_images'];
                    $mdata['content'] = $this->load->view('relieveritems/popup_optionimageslider_edit',['images' => $images,'cntimages' => count($images)], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_optimagetitle() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_optimages_title($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function item_images_rebuild() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->item_images_rebuild($sessiondata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $sessiondata = usersession($session);
                    $item = $sessiondata['item'];
                    $images = $sessiondata['images'];
                    $colors = $sessiondata['colors'];
                    $otherimages = $this->load->view('relieveritems/otherimages_view',['images' => $images, 'imgcnt' => count($images)],TRUE);
                    $optionsimg = $this->load->view('relieveritems/optionimages_view',['colors' => $colors, 'item' => $item],TRUE);
                    $imagesoptions = [
                        'otherimages' => $otherimages,
                        'optionsimg' => $optionsimg,
                        'item' => $item,
                        // 'missinfo' => $missinfo['imagescolors'],
                    ];
                    $mdata['content'] = $this->load->view('relieveritems/images_edit',$imagesoptions, TRUE);
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_relive_vendorprice() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_vendor_price($sessiondata, $postdata, $session);
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

    public function change_relive_vendoritemprice() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_vendoritem_price($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata = $this->_prepare_price_response($session);
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

    public function change_relive_itempricediscount() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_price_discount($sessiondata, $postdata, $session);
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

    public function change_relive_itemprice() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_item_price($sessiondata, $postdata, $session);
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

    public function change_relive_itempriceval() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_item_priceval($sessiondata, $postdata, $session);
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

    public function change_relive_shipbox() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_shipbox($sessiondata, $postdata, $session);
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

    public function relive_itemprintloc_add() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_printloc_add($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('relieveritems/printlocations_edit',['locations' => $res['inprints']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function relive_itemprintloc_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_printloc_edit($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_printlocatview() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_printloc_view($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('relieveritems/printlocations_edit',['locations' => $res['inprints']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function remove_relive_printlocat() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_printloc_delete($sessiondata, $postdata, $session);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('relieveritems/printlocations_edit',['locations' => $res['inprints']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_relive_vectorfile() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_vectorfile($sessiondata, $postdata, $session);
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

    public function item_relive_savedata() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Session data empty';
            $postdata = $this->input->post();
            $session = ifset($postdata, 'session', 'unkn');
            $sessiondata = usersession($session);
            if (!empty($sessiondata)) {
                $res = $this->sritems_model->itemdetails_save($sessiondata, $session, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // $mdata = $this->_prepare_price_response($session);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_price_response($session) {
        $sessiondata = usersession($session);
        $prices = $sessiondata['prices'];
        $item = $sessiondata['item'];
        $mdata = [];
        $mdata['netprices'] = $this->load->view('relieveritems/itemprice_net_view',['prices' => $prices], TRUE);
        $mdata['profit'] = $this->load->view('relieveritems/itemprice_profit_view',['item' => $item,'prices'=> $prices],TRUE);
        $mdata['saleprint'] = $item['item_sale_print'];
        $mdata['salesetup'] = $item['item_sale_setup'];
        $mdata['salerepeat'] = $item['item_sale_repeat'];
        $mdata['salerush1'] = $item['item_sale_rush1'];
        $mdata['salerush2'] = $item['item_sale_rush2'];
        $mdata['salepantone'] = $item['item_sale_pantone'];
        return $mdata;
    }

}