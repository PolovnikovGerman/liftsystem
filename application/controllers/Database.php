<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends MY_Controller
{

    private $pagelink = '/database';
    private $Inventory_Source='Stock';
    private $STRESSBALL_TEMPLATE='Stressball';
    private $OTHER_TEMPLATE='Other Item';
    private $MAX_PROMOPRICES = 10;
    protected $PERPAGE=1000;
    private $empty_html_content='&nbsp;';
    private $container_with=60;
    private $maxlength=183;

    private $container_type = 'C';
    private $express_type = 'E';

    public function __construct()
    {
        parent::__construct();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink);
        if ($pagedat['result'] == $this->error_result) {
            show_404();
        }
        $page = $pagedat['menuitem'];
        $permdat = $this->menuitems_model->get_menuitem_userpermisiion($this->USR_ID, $page['menu_item_id']);
        if ($permdat['result'] == $this->success_result && $permdat['permission'] > 0) {
        } else {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url' => '/'), 'Your have no permission to this page');
            } else {
                redirect('/');
            }
        }
    }

    public function index() {
        $head = [];
        // $head['title'] = 'Database';
        $getdata = $this->input->get();
        $start = ifset($getdata,'start','');
        $brand = $this->menuitems_model->get_current_brand();
        $content_options['start'] = $start;
        $mainmenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        $mastersection = 0;
        foreach ($mainmenu as $row) {
            if ($row['item_link']=='#dbmaster') {
                $mastersection=1;
            }
        }
        if ($brand=='SR') {
            $head['title'] = 'StressRelievers';
        } else {
            $head['title'] = 'Bluetrack/Stressballs';
        }
        $pagelnk = '#dbbrand';
        $brandmenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $pagelnk, $brand);
        if (count($brandmenu) > 0) {
            $pagescontent = 0;
            foreach ($brandmenu as $row) {
                if ($row['item_link']=='#btitems') {
                    $head['styles'][]=array('style'=>'/css/database_center/btitemslist.css');
                    $head['scripts'][] = array('src'=>'/js/database_center/btitemlist.js');
                    $head['styles'][] = array('style' => '/css/database_center/btitemdetails.css');
                    $head['scripts'][]=array('src' => '/js/database_center/btitemdetails.js');
                    $head['styles'][] = array('style' => '/css/page_view/popover.css');
                    $head['scripts'][] = array('src' => '/js/adminpage/popover.js');
                    $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
                    $content_options['itemsview'] = $this->_prepare_btitemdata_view();
                } elseif ($row['item_link']=='#btcustomers') {
                    $content_options['customersview'] = $this->load->view('customers/page_view', [], TRUE);
                } elseif ($row['item_link']=='#sbpages') {
                    $pagescontent = 1;
                    $bt_options = [];
                    $bt_options['sbhomeview'] = $this->load->view('content/template_view',['link'=>'sbhomeview'], TRUE);
                    $bt_options['sbcustomshappedview'] = $this->load->view('content/template_view',['link'=>'sbcustomshappedview'], TRUE);
                    $bt_options['sbserviceview'] = $this->load->view('content/template_view',['link'=>'sbserviceview'], TRUE);
                    $bt_options['sbaboutusview'] = $this->load->view('content/template_view',['link'=>'sbaboutusview'], TRUE);
                    $bt_options['sbfaqview'] = $this->load->view('content/template_view',['link'=>'sbfaqview'], TRUE);
                    $bt_options['sbcontactusview'] = $this->load->view('content/template_view',['link'=>'sbcontactusview'], TRUE);
                    $bt_options['sbtermsview'] = $this->load->view('content/template_view',['link'=>'sbtermsview'], TRUE);
                    $submenu = [];
                    $submenu[] = ['item_link' => '#sbhomeview', 'item_name' => 'Home Page'];
                    $submenu[] = ['item_link' => '#sbcustomshappedview', 'item_name' => 'Custom Shaped'];
                    $submenu[] = ['item_link' => '#sbserviceview', 'item_name' => 'Services'];
                    $submenu[] = ['item_link' => '#sbaboutusview', 'item_name' => 'About Us'];
                    $submenu[] = ['item_link' => '#sbfaqview', 'item_name' => 'FAQ'];
                    $submenu[] = ['item_link' => '#sbcontactusview', 'item_name' => 'Contact Us'];
                    $submenu[] = ['item_link' => '#sbtermsview', 'item_name' => 'Terms'];
                    $submenu_options = [
                        'menus' => $submenu,
                        'brand' => 'SB',
                    ];
                    $bt_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
                    $content_options['sbpagesview'] = $this->load->view('content/page_content_view', $bt_options, TRUE);
                    $head['styles'][] = array('style' => '/css/content/contentpage.css');
                    $head['scripts'][] = array('src' => '/js/content/sitecontent.js');
                    // Content
                    $head['styles'][]=array('style'=>'/css/content/customshape_page.css');
                    $head['scripts'][]=array('src'=>'/js/content/custom_shaped.js');
                    $head['styles'][]=array('style'=>'/css/content/extraservices.css');
                    $head['scripts'][]=array('src'=>'/js/content/extraservices.js');
                    $head['styles'][]=array('style'=>'/css/content/aboutus.css');
                    $head['scripts'][]=array('src'=>'/js/content/aboutus.js');
                    $head['styles'][]=array('style'=>'/css/content/faqpage.css');
                    $head['scripts'][]=array('src'=>'/js/content/faqpage.js');
                    $head['styles'][]=array('style'=>'/css/content/contactus.css');
                    $head['scripts'][]=array('src'=>'/js/content/contactus.js');
                    $head['styles'][]=array('style'=>'/css/content/terms.css');
                    $head['scripts'][]=array('src'=>'/js/content/terms.js');
                    $head['scripts'][]=array('src'=>'/js/adminpage/uEditor.js');
                    $head['styles'][]=array('style'=>'/css/page_view/uEditor.css');
                } elseif ($row['item_link']=='#btpages') {
                    if ($pagescontent==0) {
                        $head['styles'][]=array('style'=>'/css/content/customshape_page.css');
                        $head['scripts'][]=array('src'=>'/js/content/custom_shaped.js');
                        $head['styles'][]=array('style'=>'/css/content/extraservices.css');
                        $head['scripts'][]=array('src'=>'/js/content/extraservices.js');
                        $head['styles'][]=array('style'=>'/css/content/aboutus.css');
                        $head['scripts'][]=array('src'=>'/js/content/aboutus.js');
                        $head['styles'][]=array('style'=>'/css/content/faqpage.css');
                        $head['scripts'][]=array('src'=>'/js/content/faqpage.js');
                        $head['styles'][]=array('style'=>'/css/content/contactus.css');
                        $head['scripts'][]=array('src'=>'/js/content/contactus.js');
                        $head['styles'][]=array('style'=>'/css/content/terms.css');
                        $head['scripts'][]=array('src'=>'/js/content/terms.js');
                        $head['scripts'][]=array('src'=>'/js/adminpage/uEditor.js');
                        $head['styles'][]=array('style'=>'/css/page_view/uEditor.css');
                    }
                    $submenu_options = [
                        'menus' => [],
                        'brand' => 'BT',
                    ];
                    $bt_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
                    $content_options['btpagesview'] = $this->load->view('content/page_content_view', $bt_options, TRUE);
                } elseif ($row['item_link']=='#btsettings') {
                    // Shipping Settings
                    $head['styles'][] = array('style' => '/css/settings/shippings.css');
                    $head['scripts'][] = array('src' => '/js/settings/shippings.js');
                    $content_options['shippingview'] = $this->_prepare_shipping_view('BT');
                } elseif ($row['item_link']=='#sritems') {
                    $head['styles'][]=array('style'=>'/css/database_center/relivitemlist.css');
                    $head['scripts'][]=array('src'=>'/js/database_center/relivitemlist.js');
                    $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
                    $head['styles'][] = array('style' => '/css/database_center/relieveitemdetails.css');
                    $head['scripts'][]=array('src' => '/js/database_center/relieveitemdetails.js');
                    $content_options['sritemsview'] = $this->_prepare_sritems_content();
                } elseif ($row['item_link']=='#srcustomers') {
                    $content_options['customersview'] = $this->load->view('relievercustomers/page_view',[],TRUE);
                } elseif ($row['item_link']=='#srpages') {
                    $sr_options = [];
                    $sr_options['srhomeview'] = $this->load->view('content/template_view',['link'=>'srhomeview'], TRUE);
                    $sr_options['sraboutusview'] = $this->load->view('content/template_view',['link'=>'sraboutusview'], TRUE);
                    $sr_options['srcontactusview'] = $this->load->view('content/template_view',['link'=>'srcontactusview'], TRUE);
                    $submenu = [];
                    $submenu[] = ['item_link' => '#srhomeview', 'item_name' => 'Home Page'];
                    $submenu[] = ['item_link' => '#sraboutusview', 'item_name' => 'About Us'];
                    $submenu[] = ['item_link' => '#srcontactusview', 'item_name' => 'Contact Us'];
                    $submenu[] = ['item_link' => '#srartinfo', 'item_name' => 'Art & Info'];
                    $submenu[] = ['item_link' => '#srhowtoorder', 'item_name' => 'How to Order'];
                    $submenu[] = ['item_link' => '#srcatalog', 'item_name' => 'Catalog'];
                    $submenu[] = ['item_link' => '#srwhyus', 'item_name' => 'Why Us'];
                    $submenu[] = ['item_link' => '#srcustomshappedview', 'item_name' => 'Custom Shaped'];
                    $submenu_options = [
                        'menus' => $submenu,
                        'brand' => 'SR',
                    ];
                    $sr_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
                    $content_options['pagesview'] = $this->load->view('content/page_content_view', $sr_options, TRUE);
                    // Common content
                    $head['styles'][] = array('style' => '/css/content/contentpage.css');
                    $head['scripts'][] = array('src' => '/js/content/sitecontent.js');
                    // Content
                    $head['styles'][] = array('style' => '/css/content/srhomepage.css');
                    $head['scripts'][] = array('src' => '/js/content/srhomepage.js');
                    // Utils
                    $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
                    $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
                    $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
                    $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
                } elseif ($row['item_link']=='#srsettings') {
                    // $content_options['settingsview'] = $this->load->view('relieversetting/page_view',[],TRUE);
                    $head['styles'][] = array('style' => '/css/settings/shippings.css');
                    $head['scripts'][] = array('src' => '/js/settings/shippings.js');
                    $content_options['settingsview'] = $this->_prepare_shipping_view('SR');
                } elseif ($row['item_link']=='#legacyview') {
                    $legacylnk = '#legacyview';
                    $head['scripts'][]=array('src'=>'/js/database/legacy.js');
                    $head['styles'][] = array('style' => '/css/database/itemdetails.css');
                    $legacymenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $legacylnk, $brand);
                    $legacy_options = [];
                    foreach ($legacymenu as $smenu) {
                        if ($smenu['item_link']=='#itempriceview') {
                            $head['styles'][] = array('style' => '/css/database/dbprice_view.css');
                            $head['scripts'][] = array('src' => '/js/database/dbprice_view.js');
                            $page_name = 'itemprice';
                            $legacy_options['itempriceview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link']=='#itemcategoryview') {
                            $head['styles'][] = array('style' => '/css/database/dbitemcategory_view.css');
                            $head['scripts'][] = array('src' => '/js/database/dbitemcategory_view.js');
                            $page_name = 'itemcategory';
                            $legacy_options['itemcategoryview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link']=='#itemsequenceview') {
                            $head['styles'][]=array('style'=>'/css/database/dbsequence_view.css');
                            $head['scripts'][]=array('src'=>'/js/database/dbsequnece_view.js');
                            $page_name = 'itemsequence';
                            $legacy_options['itemsequenceview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link']=='#itemmisinfoview') {
                            $head['styles'][]=array('style'=>'/css/database/dbmisinfo_view.css');
                            $head['scripts'][]=array('src'=>'/js/database/dbmisinfo_view.js');
                            $page_name = 'itemmisinfo';
                            $legacy_options['itemmisinfoview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link']=='#itemprofitview') {
                            $head['styles'][]=array('style'=>'/css/database/dbprofit_view.css');
                            $head['scripts'][]=array('src'=>'/js/database/dbprofit_view.js');
                            $page_name = 'itemprofit';
                            $legacy_options['itemprofitview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link']=='#itemtemplateview') {
                            $head['styles'][] = array('style' => '/css/database/dbtemplate_view.css');
                            $head['scripts'][] = array('src' => '/js/database/dbtemplate_view.js');
                            $page_name = 'itemtemplates';
                            $legacy_options['itemtemplateview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link']=='#itemexportview') {
                            $head['styles'][] = array('style' => '/css/database/dbexport_view.css');
                            $head['scripts'][] = array('src' => '/js/database/dbexport_view.js');
                            $page_name = 'itemexport';
                            $legacy_options['itemexportview'] = $this->_content_view($page_name);
                        } elseif ($smenu['item_link'] == '#categoryview') {
                            $head['styles'][] = array('style' => '/css/database/categories.css');
                            $head['scripts'][] = array('src' => '/js/database/categories.js');
                            $page_name = 'categories';
                            $legacy_options['categoryview'] = $this->_content_view($page_name);
                        }
                    }
                    $submenu_options = [
                        'menus' => $legacymenu,
                    ];
                    $legacy_options['submenu'] = $this->load->view('database/legacy_submenu_view', $submenu_options, TRUE);
                    $content_options['legacyview'] = $this->load->view('database/legacy_page_view', $legacy_options, TRUE);
                }

            }
        }

        $mastermenu = [];
        if ($mastersection==1) {
            $pagelnk = '#dbmaster';
            $mastermenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $pagelnk, $brand);
            foreach ($mastermenu as $row) {
                if ($row['item_link']=='#mastervendors') {  // vendorsview
//                $head['styles'][]=array('style'=>'/css/database/vendorsview.css');
//                $head['scripts'][]=array('src'=>'/js/database/vendorsview.js');
//                $content_options['vendorsview'] = $this->_prepare_vendors_view();
                    $head['styles'][]=array('style'=>'/css/database_center/vendorsview.css');
                    $head['styles'][]=array('style'=>'/css/database_center/vendordetails.css');
                    $head['scripts'][]=array('src'=>'/js/database_center/vendorsview.js');
                    $head['scripts'][] = array('src' => '/js/database_center/vendoraddress.js');
                    $head['gmaps']=1;
                    $content_options['vendorsview'] = $this->_prepare_vendors_view();
                } elseif ($row['item_link']=='#masterinventory') {
                    // $head['styles'][]=array('style'=>'/css/database_center/inventory_adaptive.css');
                    $head['styles'][]=array('style'=>'/css/database_center/master_inventory.css');
                    // $head['scripts'][]=array('src'=>'/js/database_center/inventory_adaptive.js');
                    $head['scripts'][]=array('src'=>'/js/database_center/master_inventory.js');
                    $content_options['inventoryview'] = $this->_prepare_inventory_view();
                } elseif ($row['item_link']=='#mastersettings') {
                    $head['scripts'][] = array('src' => '/js/database_center/master_settings.js');
                    $head['styles'][] = array('style' => '/css/settings/countriesview.css');
                    $head['scripts'][] = array('src' => '/js/settings/countriesview.js');
                    $head['styles'][] = array('style' => '/css/settings/calendars.css');
                    $head['scripts'][] = array('src' => '/js/settings/calendars.js');
                    $head['styles'][] = array('style' => '/css/database_center/master_settings.css');
                    // Datepicker
                    $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
                    $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
                    $content_options['settingsview'] = $this->_prepare_mastersettings_view();
                }
            }
        }
        // Add main page management
        $head['scripts'][] = array('src' => '/js/database/page.js');
        $head['styles'][] = array('style' => '/css/database/databasepage.css');
        // Add Item details
        $head['scripts'][] = array('src' => '/js/database/itemdetails.js');
        // Utils
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
        // Cirlce
        $head['scripts'][] = array('src' => '/js/cycle2/jquery.cycle2.min.js');
        // Scroll panel
        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');
        // Date Picker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $content_options['mastermenu'] = $mastermenu;
        $content_options['brandmenu'] = $brandmenu;
        $content_options['mastersection'] = $mastersection;
        $content_view = $this->load->view('database_center/brand_database_view', $content_options, TRUE);

        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

//    public function oldindex() {
//        $head = [];
//        $head['title'] = 'Database';
//        $getdata = $this->input->get();
//        $start = ifset($getdata,'start','');
//        $brand = $this->menuitems_model->get_current_brand();
//        $content_options['start'] = $start; // $this->input->get('start', TRUE);
//        if ($start=='') {
//            $mainmenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
//            $brandmenu=0;
//            foreach ($mainmenu as $row) {
//                if ($row['item_link']=='#dbbrand') {
//                    $brandmenu=1;
//                }
//            }
//            if ($brandmenu==0) {
//                $this->masteritems();
//            } else {
//                // if ($brand=='SB') {
//                    $this->btchannelitems();
//                //} else {
//                //    $this->srchannelitems();
//                //}
//            }
//        } else {
//            if ($start=='dbbrand') {
//                $this->btchannelitems();
//            } elseif ($start=='dbcentermaster') {
//                $this->masteritems();
//            } elseif ($start=='dbcenterrelievers') {
//                $this->srchannelitems();
//            } elseif ($start=='legacyview') {
//                $this->legacyitems();
//            }
//        }
//    }

//    public function masteritems() {
//        $head = [];
//        $head['title'] = 'Database Master';
//        $pagelnk = '#dbmaster';
//        $brand = $this->menuitems_model->get_current_brand();
//        $main_menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
//        $brand_menu = 0;
//        if (count($main_menu)>1) {
//            $brand_menu = 1;
//        }
//        // Add main page management
//        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $pagelnk, $brand);
//
//        $content_options=[
//            // 'page_menu' => $page_menu,
//            'menu' => $menu,
//            'start' => str_replace('#','',$menu[0]['item_link'])
//        ];
//        foreach ($menu as $row) {
//            if ($row['item_link']=='#mastervendors') {  // vendorsview
////                $head['styles'][]=array('style'=>'/css/database/vendorsview.css');
////                $head['scripts'][]=array('src'=>'/js/database/vendorsview.js');
////                $content_options['vendorsview'] = $this->_prepare_vendors_view();
//                $head['styles'][]=array('style'=>'/css/database_center/vendorsview.css');
//                $head['styles'][]=array('style'=>'/css/database_center/vendordetails.css');
//                $head['scripts'][]=array('src'=>'/js/database_center/vendorsview.js');
//                $head['scripts'][] = array('src' => '/js/database_center/vendoraddress.js');
//                $head['gmaps']=1;
//                $content_options['vendorsview'] = $this->_prepare_vendors_view();
//            } elseif ($row['item_link']=='#masterinventory') {
//                // $head['styles'][]=array('style'=>'/css/database_center/inventory_adaptive.css');
//                $head['styles'][]=array('style'=>'/css/database_center/master_inventory.css');
//                // $head['scripts'][]=array('src'=>'/js/database_center/inventory_adaptive.js');
//                $head['scripts'][]=array('src'=>'/js/database_center/master_inventory.js');
//                $content_options['inventoryview'] = $this->_prepare_inventory_view();
//            } elseif ($row['item_link']=='#mastersettings') {
//                $head['scripts'][] = array('src' => '/js/database_center/master_settings.js');
//                $head['styles'][] = array('style' => '/css/settings/countriesview.css');
//                $head['scripts'][] = array('src' => '/js/settings/countriesview.js');
//                $head['styles'][] = array('style' => '/css/settings/calendars.css');
//                $head['scripts'][] = array('src' => '/js/settings/calendars.js');
//                $head['styles'][] = array('style' => '/css/database_center/master_settings.css');
//                // Datepicker
//                $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
//                $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
//                $content_options['settingsview'] = $this->_prepare_mastersettings_view();
//            }
//        }
//        // Add main page management
//        $head['scripts'][] = array('src' => '/js/database/page.js');
//        $head['styles'][] = array('style' => '/css/database/databasepage.css');
//        // Add Item details
//        $head['scripts'][] = array('src' => '/js/database/itemdetails.js');
//        // Utils
//        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
//        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
//        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
//        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
//        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
//        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
//        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
//        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
//        // Item details
//        $head['styles'][]=array('style'=>'/css/database/itemdetails.css');
//        // Scroll panel
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');
//
//        $options = [
//            'title' => $head['title'],
//            'user_id' => $this->USR_ID,
//            'user_name' => $this->USER_NAME,
//            'activelnk' => $this->pagelink,
//            'styles' => $head['styles'],
//            'scripts' => $head['scripts'],
//            'gmaps' => ifset($head, 'gmaps', 0),
//        ];
//        $dat = $this->template->prepare_pagecontent($options);
//        $content_options['left_menu'] = $dat['left_menu'];
//        $content_options['brand'] = $brand;
//        $content_options['brand_menu'] = $brand_menu;
//        $content_view = $this->load->view('database_center/master_page_view', $content_options, TRUE);
//
//        $dat['content_view'] = $content_view;
//        $this->load->view('page/page_template_view', $dat);
//    }
//
//    public function btchannelitems() {
//        $head = [];
//        $brand = $this->menuitems_model->get_current_brand();
//        if ($brand=='SR') {
//            $head['title'] = 'StressRelievers';
//        } else {
//            $head['title'] = 'Bluetrack/Stressballs';
//        }
//        $pagelnk = '#dbbrand';
//
//        $main_menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
//        $master_menu = 0;
//        if (count($main_menu) > 1) {
//            $master_menu = 1;
//        }
//
//        // Add main page management
//        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $pagelnk, $brand);
//
//        $content_options=[
//            'menus' => $main_menu,
//            'menu' => $menu,
//            'start' => str_replace('#','',$menu[0]['item_link'])
//        ];
//        $pagescontent = 0;
//        foreach ($menu as $row) {
//            if ($row['item_link']=='#btitems') {
//                //$head['styles'][]=array('style'=>'/css/database_center/itemdatalist.css');
//                //$head['scripts'][]=array('src'=>'/js/database_center/itemdatalist.js');
//                //$head['styles'][] = array('style' => '/css/database_center/itemlistdetails.css');
//                //$head['scripts'][]=array('src' => '/js/database_center/itemlistdetails.js');
//                $head['styles'][]=array('style'=>'/css/database_center/btitemslist.css');
//                $head['scripts'][] = array('src'=>'/js/database_center/btitemlist.js');
//                $head['styles'][] = array('style' => '/css/database_center/btitemdetails.css');
//                $head['scripts'][]=array('src' => '/js/database_center/btitemdetails.js');
//                $head['styles'][] = array('style' => '/css/page_view/popover.css');
//                $head['scripts'][] = array('src' => '/js/adminpage/popover.js');
//                $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
//                $content_options['itemsview'] = $this->_prepare_btitemdata_view();
//            } elseif ($row['item_link']=='#btcustomers') {
//                $content_options['customersview'] = $this->load->view('customers/page_view', [], TRUE);
//            } elseif ($row['item_link']=='#sbpages') {
//                $pagescontent = 1;
//                $bt_options = [];
//                $bt_options['sbhomeview'] = $this->load->view('content/template_view',['link'=>'sbhomeview'], TRUE);
//                $bt_options['sbcustomshappedview'] = $this->load->view('content/template_view',['link'=>'sbcustomshappedview'], TRUE);
//                $bt_options['sbserviceview'] = $this->load->view('content/template_view',['link'=>'sbserviceview'], TRUE);
//                $bt_options['sbaboutusview'] = $this->load->view('content/template_view',['link'=>'sbaboutusview'], TRUE);
//                $bt_options['sbfaqview'] = $this->load->view('content/template_view',['link'=>'sbfaqview'], TRUE);
//                $bt_options['sbcontactusview'] = $this->load->view('content/template_view',['link'=>'sbcontactusview'], TRUE);
//                $bt_options['sbtermsview'] = $this->load->view('content/template_view',['link'=>'sbtermsview'], TRUE);
//                $submenu = [];
//                $submenu[] = ['item_link' => '#sbhomeview', 'item_name' => 'Home Page'];
//                $submenu[] = ['item_link' => '#sbcustomshappedview', 'item_name' => 'Custom Shaped'];
//                $submenu[] = ['item_link' => '#sbserviceview', 'item_name' => 'Services'];
//                $submenu[] = ['item_link' => '#sbaboutusview', 'item_name' => 'About Us'];
//                $submenu[] = ['item_link' => '#sbfaqview', 'item_name' => 'FAQ'];
//                $submenu[] = ['item_link' => '#sbcontactusview', 'item_name' => 'Contact Us'];
//                $submenu[] = ['item_link' => '#sbtermsview', 'item_name' => 'Terms'];
//                $submenu_options = [
//                    'menus' => $submenu,
//                    'brand' => 'SB',
//                ];
//                $bt_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
//                $content_options['sbpagesview'] = $this->load->view('content/page_content_view', $bt_options, TRUE);
//                $head['styles'][] = array('style' => '/css/content/contentpage.css');
//                $head['scripts'][] = array('src' => '/js/content/sitecontent.js');
//            } elseif ($row['item_link']=='#btpages') {
//                if ($pagescontent==0) {
//                    $head['scripts'][]=array('src'=>'/js/adminpage/uEditor.js');
//                    $head['styles'][]=array('style'=>'/css/page_view/uEditor.css');
//                }
//                $submenu_options = [
//                    'menus' => [],
//                    'brand' => 'BT',
//                ];
//                $bt_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
//                $content_options['btpagesview'] = $this->load->view('content/page_content_view', $bt_options, TRUE);
//            } elseif ($row['item_link']=='#btsettings') {
//                // Shipping Settings
//                $head['styles'][] = array('style' => '/css/settings/shippings.css');
//                $head['scripts'][] = array('src' => '/js/settings/shippings.js');
//                $content_options['shippingview'] = $this->_prepare_shipping_view('BT');
//            } elseif ($row['item_link']=='#legacyview') {
//                $legacylnk = '#legacyview';
//                $head['scripts'][]=array('src'=>'/js/database/legacy.js');
//                $head['styles'][] = array('style' => '/css/database/itemdetails.css');
//                $legacymenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $legacylnk, $brand);
//                $legacy_options = [];
//                foreach ($legacymenu as $smenu) {
//                    if ($smenu['item_link']=='#itempriceview') {
//                        $head['styles'][] = array('style' => '/css/database/dbprice_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbprice_view.js');
//                        $page_name = 'itemprice';
//                        $legacy_options['itempriceview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemcategoryview') {
//                        $head['styles'][] = array('style' => '/css/database/dbitemcategory_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbitemcategory_view.js');
//                        $page_name = 'itemcategory';
//                        $legacy_options['itemcategoryview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemsequenceview') {
//                        $head['styles'][]=array('style'=>'/css/database/dbsequence_view.css');
//                        $head['scripts'][]=array('src'=>'/js/database/dbsequnece_view.js');
//                        $page_name = 'itemsequence';
//                        $legacy_options['itemsequenceview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemmisinfoview') {
//                        $head['styles'][]=array('style'=>'/css/database/dbmisinfo_view.css');
//                        $head['scripts'][]=array('src'=>'/js/database/dbmisinfo_view.js');
//                        $page_name = 'itemmisinfo';
//                        $legacy_options['itemmisinfoview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemprofitview') {
//                        $head['styles'][]=array('style'=>'/css/database/dbprofit_view.css');
//                        $head['scripts'][]=array('src'=>'/js/database/dbprofit_view.js');
//                        $page_name = 'itemprofit';
//                        $legacy_options['itemprofitview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemtemplateview') {
//                        $head['styles'][] = array('style' => '/css/database/dbtemplate_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbtemplate_view.js');
//                        $page_name = 'itemtemplates';
//                        $legacy_options['itemtemplateview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemexportview') {
//                        $head['styles'][] = array('style' => '/css/database/dbexport_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbexport_view.js');
//                        $page_name = 'itemexport';
//                        $legacy_options['itemexportview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link'] == '#categoryview') {
//                        $head['styles'][] = array('style' => '/css/database/categories.css');
//                        $head['scripts'][] = array('src' => '/js/database/categories.js');
//                        $page_name = 'categories';
//                        $legacy_options['categoryview'] = $this->_content_view($page_name);
//                    }
//                }
//                $submenu_options = [
//                    'menus' => $legacymenu,
//                ];
//                $legacy_options['submenu'] = $this->load->view('database/legacy_submenu_view', $submenu_options, TRUE);
//                $content_options['legacyview'] = $this->load->view('database/legacy_page_view', $legacy_options, TRUE);
//            } elseif ($row['item_link']=='#sritems') {
//                $head['styles'][]=array('style'=>'/css/database_center/relivitemlist.css');
//                $head['scripts'][]=array('src'=>'/js/database_center/relivitemlist.js');
//                $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
//                $head['styles'][] = array('style' => '/css/database_center/relieveitemdetails.css');
//                $head['scripts'][]=array('src' => '/js/database_center/relieveitemdetails.js');
//                $content_options['sritemsview'] = $this->_prepare_sritems_content();
//            } elseif ($row['item_link']=='#srcustomers') {
//                $content_options['customersview'] = $this->load->view('relievercustomers/page_view',[],TRUE);
//            } elseif ($row['item_link']=='#srpages') {
//                $sr_options = [];
//                $sr_options['srhomeview'] = $this->load->view('content/template_view',['link'=>'srhomeview'], TRUE);
//                $sr_options['sraboutusview'] = $this->load->view('content/template_view',['link'=>'sraboutusview'], TRUE);
//                $sr_options['srcontactusview'] = $this->load->view('content/template_view',['link'=>'srcontactusview'], TRUE);
//                /*
//                $sr_options['sbcustomshappedview'] = $this->load->view('content/template_view',['link'=>'sbcustomshappedview'], TRUE);
//                $sr_options['sbserviceview'] = $this->load->view('content/template_view',['link'=>'sbserviceview'], TRUE);
//                $sr_options['sbfaqview'] = $this->load->view('content/template_view',['link'=>'sbfaqview'], TRUE);
//                $sr_options['sbtermsview'] = $this->load->view('content/template_view',['link'=>'sbtermsview'], TRUE);
//                */
//                $submenu = [];
//                $submenu[] = ['item_link' => '#srhomeview', 'item_name' => 'Home Page'];
//                $submenu[] = ['item_link' => '#sraboutusview', 'item_name' => 'About Us'];
//                $submenu[] = ['item_link' => '#srcontactusview', 'item_name' => 'Contact Us'];
//                $submenu[] = ['item_link' => '#srartinfo', 'item_name' => 'Art & Info'];
//                $submenu[] = ['item_link' => '#srhowtoorder', 'item_name' => 'How to Order'];
//                $submenu[] = ['item_link' => '#srcatalog', 'item_name' => 'Catalog'];
//                $submenu[] = ['item_link' => '#srwhyus', 'item_name' => 'Why Us'];
//                $submenu[] = ['item_link' => '#srcustomshappedview', 'item_name' => 'Custom Shaped'];
//                /*
//                $submenu[] = ['item_link' => '#sbserviceview', 'item_name' => 'Services'];
//                $submenu[] = ['item_link' => '#sbfaqview', 'item_name' => 'FAQ'];
//                $submenu[] = ['item_link' => '#sbtermsview', 'item_name' => 'Terms'];
//                */
//                $submenu_options = [
//                    'menus' => $submenu,
//                    'brand' => 'SR',
//                ];
//                $sr_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
//                $content_options['pagesview'] = $this->load->view('content/page_content_view', $sr_options, TRUE);
//                // Common content
//                $head['styles'][] = array('style' => '/css/content/contentpage.css');
//                $head['scripts'][] = array('src' => '/js/content/sitecontent.js');
//                // Content
//                $head['styles'][] = array('style' => '/css/content/srhomepage.css');
//                $head['scripts'][] = array('src' => '/js/content/srhomepage.js');
//                // Utils
//                $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
//                $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
//                $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
//                $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
//            } elseif ($row['item_link']=='#srsettings') {
//                $content_options['settingsview'] = $this->load->view('relieversetting/page_view',[],TRUE);
//            }
//
//        }
//        // Add main page management
//        $head['scripts'][] = array('src' => '/js/database/page.js');
//        $head['styles'][] = array('style' => '/css/database/databasepage.css');
//        // Add Item details
//        $head['scripts'][] = array('src' => '/js/database/itemdetails.js');
//        // Utils
//        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
//        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
//        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
//        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
//        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
//        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
//        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
//        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
//        // Cirlce
//        $head['scripts'][] = array('src' => '/js/cycle2/jquery.cycle2.min.js');
//        // Scroll panel
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');
//
//        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
//        $dat = $this->template->prepare_pagecontent($options);
//        $content_options['left_menu'] = $dat['left_menu'];
//        $content_options['brand'] = $brand;
//        $content_options['master_menu'] = $master_menu;
//        $content_view = $this->load->view('database_center/brand_database_view', $content_options, TRUE);
//
//        $dat['content_view'] = $content_view;
//        $this->load->view('page/page_template_view', $dat);
//    }

//    public function srchannelitems() {
//        $head = [];
//        $head['title'] = 'StressRelievers';
//        $pagelnk = '#dbbrand';
//        $main_menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
//        $menu_options = [
//            'menus' => $main_menu,
//            'start' => $pagelnk,
//        ];
//        $page_menu = $this->load->view('database_center/main_menu_view', $menu_options, TRUE);
//        // Add main page management
//        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);
//
//        $content_options=[
//            'page_menu' => $page_menu,
//            'menu' => $menu,
//            'start' => str_replace('#','',$menu[0]['item_link'])
//        ];
//        foreach ($menu as $row) {
//            if ($row['item_link']=='#vendorsview') {
//            } elseif ($row['item_link']=='#masterinventory') {
//            } elseif ($row['item_link']=='#mastersettings') {
//            }
//        }
//        // Add main page management
//        $head['scripts'][] = array('src' => '/js/database/page.js');
//        $head['styles'][] = array('style' => '/css/database/databasepage.css');
//        // Add Item details
//        $head['scripts'][] = array('src' => '/js/database/itemdetails.js');
//        // Utils
//        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
//        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
//        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
//        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
//        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
//        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
//        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
//        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
//        // Item details
//        $head['styles'][]=array('style'=>'/css/database/itemdetails.css');
//        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
//        $dat = $this->template->prepare_pagecontent($options);
//
//        $content_view = $this->load->view('database/page_view', $content_options, TRUE);
//
//        $dat['content_view'] = $content_view;
//        $this->load->view('page/page_template_view', $dat);
//    }

//    public function legacyitems() {
//        $head = [];
//        $head['title'] = 'Legacy';
//        $pagelnk = '#legacyview';
//        $main_menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
//        $menu_options = [
//            'menus' => $main_menu,
//            'start' => $pagelnk,
//        ];
//        $page_menu = $this->load->view('database_center/main_menu_view', $menu_options, TRUE);
//        // Add main page management
//        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);
//
//        $content_options=[
//            'page_menu' => $page_menu,
//            'menu' => $menu,
//            'start' => str_replace('#','',$menu[0]['item_link'])
//        ];
//        $head['scripts'][]=array('src'=>'/js/database/legacy.js');
//        // Get Submenu
//        // $legacy_options = [];
//        // $submenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $pagelnk);
//        foreach ($menu as $smenu) {
//            if ($smenu['item_link']=='#itempriceview') {
//                $head['styles'][] = array('style' => '/css/database/dbprice_view.css');
//                $head['scripts'][] = array('src' => '/js/database/dbprice_view.js');
//                $page_name = 'itemprice';
//                $content_options['itempriceview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link']=='#itemcategoryview') {
//                $head['styles'][] = array('style' => '/css/database/dbitemcategory_view.css');
//                $head['scripts'][] = array('src' => '/js/database/dbitemcategory_view.js');
//                $page_name = 'itemcategory';
//                $content_options['itemcategoryview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link']=='#itemsequenceview') {
//                $head['styles'][]=array('style'=>'/css/database/dbsequence_view.css');
//                $head['scripts'][]=array('src'=>'/js/database/dbsequnece_view.js');
//                $page_name = 'itemsequence';
//                $content_options['itemsequenceview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link']=='#itemmisinfoview') {
//                $head['styles'][]=array('style'=>'/css/database/dbmisinfo_view.css');
//                $head['scripts'][]=array('src'=>'/js/database/dbmisinfo_view.js');
//                $page_name = 'itemmisinfo';
//                $content_options['itemmisinfoview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link']=='#itemprofitview') {
//                $head['styles'][]=array('style'=>'/css/database/dbprofit_view.css');
//                $head['scripts'][]=array('src'=>'/js/database/dbprofit_view.js');
//                $page_name = 'itemprofit';
//                $content_options['itemprofitview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link']=='#itemtemplateview') {
//                $head['styles'][] = array('style' => '/css/database/dbtemplate_view.css');
//                $head['scripts'][] = array('src' => '/js/database/dbtemplate_view.js');
//                $page_name = 'itemtemplates';
//                $content_options['itemtemplateview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link']=='#itemexportview') {
//                $head['styles'][] = array('style' => '/css/database/dbexport_view.css');
//                $head['scripts'][] = array('src' => '/js/database/dbexport_view.js');
//                $page_name = 'itemexport';
//                $content_options['itemexportview'] = $this->_content_view($page_name);
//            } elseif ($smenu['item_link'] == '#categoryview') {
//                $head['styles'][] = array('style' => '/css/database/categories.css');
//                $head['scripts'][] = array('src' => '/js/database/categories.js');
//                $page_name = 'categories';
//                $content_options['categoryview'] = $this->_content_view($page_name);
//            }
//        }
//        // $page_name = strtolower($submenu[0]['item_name']);
//        // $submenu_options = [
//        //    'menus' => $submenu,
//        // ];
//        // $legacy_options['submenu'] = $this->load->view('database/legacy_submenu_view', $submenu_options, TRUE);
//        // $content_options['legacyview'] = $this->load->view('database/legacy_page_view', $legacy_options, TRUE);
//        // Add main page management
//        $head['scripts'][] = array('src' => '/js/database/page.js');
//        $head['styles'][] = array('style' => '/css/database/databasepage.css');
//        // Add Item details
//        $head['scripts'][] = array('src' => '/js/database/itemdetails.js');
//        // Utils
//        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
//        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
//        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
//        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
//        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
//        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
//        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
//        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
//        // Item details
//        $head['styles'][]=array('style'=>'/css/database/itemdetails.css');
//        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
//        $dat = $this->template->prepare_pagecontent($options);
//
//        $content_view = $this->load->view('database/page_view', $content_options, TRUE);
//
//        $dat['content_view'] = $content_view;
//        $this->load->view('page/page_template_view', $dat);
//    }

//    public function index()
//    {
//        $head = [];
//        $head['title'] = 'Database';
//        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
//        $content_options = [
//            'menu' => $menu,
//            'start' => $this->input->get('start', TRUE),
//        ];
//        $search = usersession('liftsearch');
//        usersession('liftsearch', NULL);
//        foreach ($menu as $row) {
//            if ($row['item_link'] == '#customersview') {
//
//            } elseif ($row['item_link'] == '#vendorsview') {
//                $head['styles'][]=array('style'=>'/css/database/vendorsview.css');
//                $head['scripts'][]=array('src'=>'/js/database/vendorsview.js');
//                $content_options['vendorsview'] = $this->_prepare_vendors_view();
//            } elseif ($row['item_link'] == '#btitemsview') {
//
//            } elseif ($row['item_link'] == '#sbitemsview') {
//
//            } elseif ($row['item_link'] == '#amazonitemsview') {
//
//            } elseif ($row['item_link'] == '#legacyview') {
//                $head['scripts'][]=array('src'=>'/js/database/legacy.js');
//                // Get Submenu
//                $legacy_options = [];
//                $submenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $row['item_link']);
//                foreach ($submenu as $smenu) {
//                    if ($smenu['item_link']=='#itempriceview') {
//                        $head['styles'][] = array('style' => '/css/database/dbprice_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbprice_view.js');
//                        $page_name = 'itemprice';
//                        $legacy_options['itempriceview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemcategoryview') {
//                        $head['styles'][] = array('style' => '/css/database/dbitemcategory_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbitemcategory_view.js');
//                        $page_name = 'itemcategory';
//                        $legacy_options['itemcategoryview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemsequenceview') {
//                        $head['styles'][]=array('style'=>'/css/database/dbsequence_view.css');
//                        $head['scripts'][]=array('src'=>'/js/database/dbsequnece_view.js');
//                        $page_name = 'itemsequence';
//                        $legacy_options['itemsequenceview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemmisinfoview') {
//                        $head['styles'][]=array('style'=>'/css/database/dbmisinfo_view.css');
//                        $head['scripts'][]=array('src'=>'/js/database/dbmisinfo_view.js');
//                        $page_name = 'itemmisinfo';
//                        $legacy_options['itemmisinfoview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemprofitview') {
//                        $head['styles'][]=array('style'=>'/css/database/dbprofit_view.css');
//                        $head['scripts'][]=array('src'=>'/js/database/dbprofit_view.js');
//                        $page_name = 'itemprofit';
//                        $legacy_options['itemprofitview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemtemplateview') {
//                        $head['styles'][] = array('style' => '/css/database/dbtemplate_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbtemplate_view.js');
//                        $page_name = 'itemtemplates';
//                        $legacy_options['itemtemplateview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link']=='#itemexportview') {
//                        $head['styles'][] = array('style' => '/css/database/dbexport_view.css');
//                        $head['scripts'][] = array('src' => '/js/database/dbexport_view.js');
//                        $page_name = 'itemexport';
//                        $legacy_options['itemexportview'] = $this->_content_view($page_name);
//                    } elseif ($smenu['item_link'] == '#categoryview') {
//                        $head['styles'][] = array('style' => '/css/database/categories.css');
//                        $head['scripts'][] = array('src' => '/js/database/categories.js');
//                        $page_name = 'categories';
//                        $legacy_options['categoryview'] = $this->_content_view($page_name);
//                    }
//                }
//                // $page_name = strtolower($submenu[0]['item_name']);
//                $submenu_options = [
//                    'menus' => $submenu,
//                ];
//                $legacy_options['submenu'] = $this->load->view('database/legacy_submenu_view', $submenu_options, TRUE);
//                $content_options['legacyview'] = $this->load->view('database/legacy_page_view', $legacy_options, TRUE);
//            }
//
//        }
//        // Add main page management
//        $head['scripts'][] = array('src' => '/js/database/page.js');
//        $head['styles'][] = array('style' => '/css/database/databasepage.css');
//        // Add Item details
//        $head['scripts'][] = array('src' => '/js/database/itemdetails.js');
//        // Utils
//        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
//        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
//        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
//        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
//        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
//        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
//        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
//        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
//        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
//        // Item details
//        $head['styles'][]=array('style'=>'/css/database/itemdetails.css');
//        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
//        $dat = $this->template->prepare_pagecontent($options);
//
//        $content_view = $this->load->view('database/page_view', $content_options, TRUE);
//
//        $dat['content_view'] = $content_view;
//        $this->load->view('page/page_template_view', $dat);
//    }

    // private function _content_view($page_name, $brand, $left_menu, $search='') {
    private function _content_view($page_name, $search='') {
        // $data = ['brand' => $brand, 'left_menu' => $left_menu, 'brandid' => $page_name.'brand', 'brandmenuid' => $page_name.'brandmenu'];
        $data=[];
        if ($page_name=='categories') {
            // $page_name_full = 'Categories';
            $special_content = $this->_prepare_dbpage_content($page_name);
            $buttons_view = $this->load->view('database/content_viewbuttons_view', [], TRUE);
            $options = ['buttons_view' => $buttons_view, 'special_content' => $special_content,];
            $data['content'] = $this->load->view('database/category_pagecontent_view', $options, TRUE);
        } else {
            $data['content'] = $this->_prepare_dbpage_content($page_name, $search);
        }
        return $this->load->view('database/page_content_view', $data, TRUE);
    }

    // Categories
    public function get_category_details() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Category Empty';
            $postdata = $this->input->post();
            $category_id = (isset($postdata['category_id'])? $postdata['category_id'] : 0);
            if ($category_id) {
                $this->load->model('categories_model');
                $res = $this->categories_model->get_category_data($category_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $category = $res['data'];
                    $mdata['meta'] = $this->load->view('database/category_meta_view', $category, TRUE);
                    $mdata['content'] = $this->load->view('database/category_content_view',$category,TRUE);
                    $mdata['category_name'] = $category['category_name'];
                    $mdata['view_button'] = $this->load->view('database/editbutton_view', $category, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function category_sort() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $this->load->model('categories_model');
            $res = $this->categories_model->update_category_sort($postdata, $this->USR_ID);
            $options = [
                'categories' => $res['categories'],
                'current_category' => $res['category_id'],
            ];
            $mdata['content'] = $this->load->view('database/categorylist_view', $options, TRUE);
            $this->ajaxResponse($mdata,'');
        }
        show_404();
    }


    public function get_category_edit() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Category Empty';
            $postdata = $this->input->post();
            $category_id = (isset($postdata['category_id'])? $postdata['category_id'] : 0);
            if ($category_id) {
                $this->load->model('categories_model');
                $res = $this->categories_model->get_category_data($category_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $category = $res['data'];
                    $session_id = uniq_link(15);
                    usersession($session_id, $category);
                    $options=[
                        'session_id' => $session_id,
                        'meta' => $category,
                        'category_id'=>$category_id,
                    ];
                    $mdata['meta'] = $this->load->view('database/category_meta_edit', $options , TRUE);
                    $mdata['content'] = $this->load->view('database/category_content_edit',$category,TRUE);
                    $mdata['view_button'] = $this->load->view('database/actionbutton_view', $category, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_category_content() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session_id']) ? $postdata['session_id'] : 'category');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('categories_model');
                $res = $this->categories_model->change_category_content($postdata, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if ($postdata['field']=='icon_homepage' && !empty($postdata['newval'])) {
                        //  Build content
                        $options = [
                            'icon_homepage' => $postdata['newval'],
                        ];
                        $mdata['content']=$this->load->view('database/category_homeimage_edit', $options, TRUE);
                    }
                    if ($postdata['field']=='icon_dropdown' && !empty($postdata['newval'])) {
                        //  Build content
                        $options = [
                            'icon_dropdown' => $postdata['newval'],
                        ];
                        $mdata['content']=$this->load->view('database/category_dropdownimage_edit', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }


    public function update_category_content() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session_id']) ? $postdata['session_id'] : 'category');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('categories_model');
                $res = $this->categories_model->update_category_content($session_data, $session_id, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['category']=$res['category_id'];
                    $mdata['category_name']=$res['category_name'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // DB Items - Price
    public function pricedat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            /* All POST Params */
            $datpost=$this->input->post();
            $pagenum=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by','i.item_number');
            $direct = $this->input->post('direction','asc');
            $search=$this->input->post('search','');
            $compareprefs=$this->input->post('compareprefs','');
            $vendor_id=$this->input->post('vendor_id','');
            $othervend=array();
            foreach ($datpost as $key=>$val) {
                if (substr($key, 0,8)=='otherved' && $val==1) {
                    array_push($othervend, substr($key,8));
                }
            }
            $brand = ifset($datpost,'brand', '')=='' ? 'ALL' : $datpost['brand'];
            usersession('page_name','priceview');
            usersession('curpage', $pagenum);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('priority', $compareprefs);
            usersession('vendor_id', $vendor_id);
            usersession('othervend', $othervend);

            $offset=$pagenum*$limit;

            if ($this->USR_ROLE=='general') {
                $this->load->model('items_model');
                $item_dat=$this->items_model->get_items(array('brand'=>$brand,),$order_by,$direct,$limit,$offset,$search,$vendor_id);
                $data=array('item_dat'=>$item_dat,'offset'=>$offset);
                $mdata['content'] = $this->load->view('database/dbprice_tabledat_general_view',$data, TRUE);
            } else {
                if (count($othervend)==0) {
                    $error='Empty List of Compare price';
                } else {
                    $this->load->model('otherprices_model');
                    if (count($othervend)==4) {
                        $item_dat=$this->otherprices_model->get_compared_prices($order_by, $direct, $limit, $offset, $search, $compareprefs, $vendor_id, $brand);
                    } else {
                        $item_dat=$this->otherprices_model->get_compared_pricelimit($order_by, $direct, $limit, $offset, $search, $compareprefs, $vendor_id, $brand, $othervend);
                    }
                    $data=array('item_dat'=>$item_dat,'offset'=>$offset);
                    $mdata['content'] = $this->load->view('database/dbprice_table_dat_view',$data, TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function searchcount() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $search = $this->input->post('search');
            $vendor_id=$this->input->post('vendor_id','');
            $brand = $this->input->post('brand');
            $this->load->model('items_model');
            $num_rec=$this->items_model->count_searchres($search, $brand, $vendor_id );
            $mdata['result']=$num_rec;
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    // DB Item - Categories
    public function categorydat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by','i.item_number');
            $direct = $this->input->post('direction','asc');
            $pagelock=$this->input->post('pagelock',1);
            $search = $this->input->post('search');
            $vendor_id=$this->input->post('vendor_id','');

            usersession('page_name','categview');
            usersession('curpage', $offset);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('vendor_id', $vendor_id);

            $offset=$offset*$limit;

            /* Get data about categories */
            $options=array();
            $this->load->model('itemcategory_model');
            $item_dat=$this->itemcategory_model->get_item_categories($options,$order_by,$direct,$limit,$offset,$search,$vendor_id);

            $options=array('show_list'=>1);
            $this->load->model('categories_model');
            $categ_list=$this->categories_model->get_categories($options);

            $data=array(
                'item_dat'=>$item_dat,
                'order_by'=>$order_by,
                'direction'=>$direct,
                'offset'=>$offset,
                'categ_list'=>$categ_list,
                'pagelock'=>$pagelock
            );
            $mdata['content'] = $this->load->view('database/dbcategory_table_data_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function updcat() {
        if ($this->isAjax()) {
            $err_str='';
            $mdata=[];
            $el_id=$this->input->post('id');
            $el_val=$this->input->post('value');
            $options=array('show_list'=>1);
            $this->load->model('itemcategory_model');
            $this->load->model('categories_model');
            $categ_list=$this->categories_model->get_categories($options);
            if (substr($el_id,0,1)=='c') {
                /* New Category */
                $item_id=substr($el_id,1,strpos($el_id,'_')-1);
                if ($el_val!=0) {
                    $chk=$this->itemcategory_model->chk_itemcateg($item_id,$el_val);
                    if ($chk!=0) {
                        $err_str='Non unique category';
                        $cat_id=$el_id;
                        $el_val=0;
                    } else {
                        $cat_id=$this->itemcategory_model->ins_categ($item_id,$el_val);
                        $cat_id='ic'.$cat_id.'_'.substr($el_id,1);
                    }
                }
            } else {
                $recid=substr($el_id,2,  strpos($el_id, '_')-1);
                $new_elid=substr($el_id,strpos($el_id,'_')+1);
                if ($el_val==0) {
                    /* Delete Item Category */
                    $cat_id=$this->itemcategory_model->del_categ($recid);
                    $cat_id='c'.$new_elid;
                } else {
                    /* Check an unique  */
                    $res=$this->itemcategory_model->check_updcateg($recid,$el_val);
                    if ($res!=0) {
                        /* Error */
                        $err_str='Non unique category';
                        $cat_id=$el_id;
                        $el_val=$res;
                    } else {
                        $cat_id=$this->itemcategory_model->upd_categ($recid,$el_val);
                        $cat_id=$el_id;
                    }
                }
            }
            /* Load view with el_val */
            $mdata['content'] = $this->load->view('database/dbcategerory_item_view',array('el_id'=>$cat_id,'catval'=>$el_val,'categ_list'=>$categ_list),TRUE);
            $this->ajaxResponse($mdata, $err_str);
        }
        show_404();
    }

    public function upditemcategory() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $options=array('show_dropdown'=>array(1,2),'show_homepage'=>1);
            $this->load->model('itemcategory_model');
            $this->load->model('categories_model');
            $categ_list=$this->categories_model->get_categories($options);
            $res = $this->itemcategory_model->update_itemcategory($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $viewoptions = array(
                    'itemcategory_id' => $res['itemcategory_id'],
                    'catval'=>($postdata['item_category']==0 ? '' : $postdata['item_category']),
                    'categ_list'=>$categ_list,
                );
                $mdata['content'] = $this->load->view('database/dbcategerory_item_view',$viewoptions,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // DB Sequence
    public function itemsequence_search() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = '';
            $mdata=[];
            $total_options = [];
            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                $total_options['vendor_id']=$postdata['vendor_id'];
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $total_options['search']=$postdata['search'];
            }
            $total_options['brand']=$postdata['brand'];
            $this->load->model('items_model');
            $mdata['total']=$this->items_model->get_sequence_count($total_options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_data() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $pagenum = (isset($postdata['offset']) ? $postdata['offset'] : 0);
            $limit = (isset($postdata['limit']) ? $postdata['limit'] : $this->sequence_perpage);
            $options = array(
                'offset' => $pagenum * $limit,
                'limit' => $limit,
            );
            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                $options['vendor_id']=$postdata['vendor_id'];
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=$postdata['search'];
            }
            $options['brand'] = (ifset($postdata,'brand','')=='' ? 'ALL' : $postdata['brand']);
            // Get items
            $this->load->model('items_model');
            $data = $this->items_model->get_sequence_items($options);
            $label = 'Displaying ';
            if (count($data)==0) {
                $label.='0';
            } else {
                $label.=(($pagenum*$limit)+1).' - '.count($data);
            }
            $label.=' of '.QTYOutput($postdata['total']);
            $options=[
                'items' => $data,
                'itemperrow' => (isset($postdata['itemperrow']) ? $postdata['itemperrow'] : $this->sequence_inrow),
            ];
            $content = $this->load->view('database/dbsequence_page_view', $options, TRUE);
            $mdata=[
                'label' => $label,
                'content' => $content,
            ];
            $error = '';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_updateitem() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            // Get items
            $this->load->model('items_model');
            $mdata=[];
            $res = $this->items_model->update_item_property($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_updateseq() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            // Get items
            $this->load->model('items_model');
            $mdata=[];
            $res = $this->items_model->update_item_sequence($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_sort() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $this->load->model('items_model');
            $mdata=[];
            $error='';
            $this->items_model->update_itemsequence_sort($postdata);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // DB Miss Info
    public function misinfodat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $postdata = $this->input->post();

            $pagenum=ifset($postdata, 'offset',0);
            $limit=ifset($postdata, 'limit',10);
            $order_by= ifset($postdata, 'order_by','i.item_number');
            $direct = ifset($postdata,'direction','asc');
            $search = ifset($postdata,'search','');
            $vendor_id=ifset($postdata,'vendor_id','');
            $brand = ifset($postdata, 'brand', '')=='' ? 'ALL' : $postdata['brand'];
            usersession('page_name','misinfo');
            usersession('curpage', $pagenum);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('vendor_id', $vendor_id);

            $offset=intval($pagenum)*$limit;

            /* Get Data about items & missing info */
            $this->load->model('items_model');
            $item_dat=$this->items_model->get_missinginfo(array('brand'=>$brand),$order_by,$direct,$limit,$offset,$search,$vendor_id);

            $data=array('item_dat'=>$item_dat,'order_by'=>$order_by,'direction'=>$direct,'offset'=>$offset);

            $mdata['content'] = $this->load->view('database/dbmisinfo_table_data_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }
    // DB Item Profit
    public function profitdat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();

            $pagenum=(isset($postdata['offset']) ? $postdata['offset'] : 0);
            $limit=(isset($postdata['limit']) ? $postdata['limit'] : 10);
            $order_by=(isset($postdata['order_by']) ? $postdata['order_by'] : 'i.item_number');
            $direct=(isset($postdata['direction']) ? $postdata['direction'] : 'asc');
            $search=(isset($postdata['search']) ? $postdata['search'] : '');
            $profitprefs=(isset($postdata['profitprefs']) ? $postdata['profitprefs'] : '');
            $vendor_id=(isset($postdata['vendor_id']) ? $postdata['vendor_id'] : '');
            $brand = ifset($postdata, 'brand','')=='' ? 'ALL' : $postdata['brand'];
            usersession('page_name','profitview');
            usersession('curpage', $pagenum);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('priority', $profitprefs);
            usersession('vendor_id', $vendor_id);


            $offset=$pagenum*$limit;

            /* Get Data about about items & prices */
            $this->load->model('prices_model');
            $item_dat=$this->prices_model->get_item_profitprefs($order_by,$direct,$limit,$offset,$search,$profitprefs, $vendor_id, $brand);

            $data=array('item_dat'=>$item_dat,'order_by'=>$order_by,'direction'=>$direct,'offset'=>$offset);
            if ($this->USR_ROLE=='general') {
                $content = $this->load->view('database/dbprofit_tabledat_general_view',$data, TRUE);
            } else {
                $content = $this->load->view('database/dbprofit_tabledat_view',$data, TRUE);
            }


            $mdata['content']=$content;

            $this->ajaxResponse($mdata,$error);
        }
    }

    // DB Template
    public function templatedat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $postdata = $this->input->post();
            $numpage=ifset($postdata, 'offset',0);
            $limit=ifset($postdata,'limit',10);
            $order_by=ifset($postdata, 'order_by','item_id');
            $direct = ifset($postdata,'direction','asc');
            $search=ifset($postdata, 'search','');
            $vendor_id=ifset($postdata, 'vendor_id','');
            $brand = ifset($postdata, 'brand','')=='' ? 'ALL' : $postdata['brand'];
            usersession('page_name','temlatesview');
            usersession('curpage', $numpage);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('vendor_id', $vendor_id);

            $offset=$numpage*$limit;

            /* Get Data about about items & categories */
            $this->load->model('items_model');
            $item_dat=$this->items_model->get_items(array('brand'=>$brand),$order_by,$direct,$limit,$offset,$search,$vendor_id);
            $data=array('item_dat'=>$item_dat,'order_by'=>$order_by,'direction'=>$direct,'offset'=>$offset);
            $mdata['content'] = $this->load->view('database/dbtemplate_table_data_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function update_imprint() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata=$this->input->post();
            $this->load->model('items_model');
            $res=$this->items_model->update_imprint_update($postdata);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function view_item() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty Item';
            $postdata = $this->input->post();
            $item_id = ifset($postdata,'item_id',0);
            // $brand = ifset($postdata,'brand','ALL');
            if (!empty($item_id)) {
//                $brands = $this->menuitems_model->get_brand_permisions($this->USR_ID, $this->pagelink);
//                $left_options = [
//                    'brands' => $brands,
//                    'active' => $brand,
//                ];
//                $mdata['menu']=$this->load->view('page/left_menu_view', $left_options, TRUE);
                $res = $this->_prepare_itemdetails($item_id, 'view');
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content']=$res['content'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function edit_item() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Empty Item';
            $postdata = $this->input->post();
            $item_id = ifset($postdata,'item_id',0);
            if (!empty($item_id)) {
                $res = $this->_prepare_itemdetails($item_id, 'edit');
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['content']=$res['content'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function restore_databaseview() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = '';
            $page_name = usersession('page_name');
            $mdata['pagename'] = (empty($page_name) ? '' : $page_name);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function vendordata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $page_num = ifset($postdata, 'offset', 0);
            $limit = ifset($postdata, 'limit', 100);
            $offset = $page_num * $limit;
            $order_by = ifset($postdata,'order_by','vendor_name');
            $direction = ifset($postdata, 'direction','asc');
            $options = [
                'offset' => $offset,
                'limit' => $limit,
                'order_by' => $order_by,
                'direct' => $direction,
            ];
            $this->load->model('vendors_model');
            $vendors=$this->vendors_model->get_vendors_oldlist($options);
            if (count($vendors)==0) {
                $content=$this->load->view('fulfillment/vendors_emptydata_view', array(), TRUE);
            } else {
                $content=$this->load->view('fulfillment/vendor_tabledat_view',array('vendors'=>$vendors),TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function vendor_remove() {
        if ($this->isAjax()) {
            $error = 'No permissions';
            $mdata = [];
            if ($this->USR_ROLE!=='general') {
                $vendor_id=$this->input->post('vendor_id');
                $this->load->model('vendors_model');
                $res=$this->vendors_model->delete_vendor($vendor_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['totals'] = $this->vendors_model->get_count_vendors();
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendor_edit() {
        if ($this->isAjax()) {
            $error = 'Vendor not found';
            $mdata = [];
            $postdata = $this->input->post();
            $vendor_id = ifset($postdata, 'vendor_id');
            if (!empty($vendor_id)) {
                $this->load->model('vendors_model');
                $this->load->model('calendars_model');
                $calendars=$this->calendars_model->get_calendars();
                if ($vendor_id<0) {
                    $error = '';
                    $data = $this->vendors_model->add_vendor();
                    $mdata['title'] = 'New Vendor';
                } else {
                    $res = $this->vendors_model->get_vendor($vendor_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $data = $res['data'];
                        $mdata['title'] = 'Change Vendor '.$data['vendor_name'];
                    }
                }
                if ($error =='') {
                    $mdata['content']=$this->load->view('fulfillment/vendor_formdata_view',array('vendor'=>$data,'calendars'=>$calendars),TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendordata_save() {
        if ($this->isAjax()) {
            $mdata = [];
            $vendor_id=$this->input->post('vendor_id');
            $vendor_name=$this->input->post('vendor_name');
            $vendor_zipcode=$this->input->post('vendor_zipcode');
            $calendar_id=$this->input->post('calendar_id');
            $this->load->model('vendors_model');
            $res=$this->vendors_model->save_vendor($vendor_id,$vendor_name, $vendor_zipcode, $calendar_id);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['totals'] = $this->vendors_model->get_count_vendors();
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vendor_includereport() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $vendor_id=$this->input->post('vendor_id');
            $payinclude=$this->input->post('payinclude');
            $this->load->model('vendors_model');
            $res=$this->vendors_model->vendor_includerep($vendor_id, $payinclude);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function exportdata() {
        if ($this->isAjax()) {
            $search= $this->input->post();
            $mdata=array();
            $error='';
            $this->load->model('items_model');
            $results=$this->items_model->get_export_data($search);
            $mdata['totals']=count($results);
            $mdata['content']=$this->load->view('database/exportdb_tabledat_view',array('items'=>$results),TRUE);
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function export_select_fields() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('items_model');
            $flds=$this->items_model->get_export_description();
            /* Allowed select */
            $allow_select=$this->load->view('database/fields_select_view',array('fields'=>$flds,'allowed'=>1,'def_class'=>'allowed_enable'),TRUE);
            $selected=$this->load->view('database/fields_select_view',array('fields'=>$flds,'allowed'=>0,'def_class'=>'select_hide'),TRUE);
            $mdata['content']=$this->load->view('database/export_selectfelds_view',array('allowed_select'=>$allow_select,'selected'=>$selected),TRUE);
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function save_export() {
        if ($this->isAjax()) {
            $mdata=array();

            $search = $this->input->post();
            $search['print_ver']=1;
            $this->load->model('items_model');
            $results=$this->items_model->get_export_data($search);
            $fldstr=$this->input->post('fldlst');
            $fldstr=substr($fldstr,0,-1);
            $fldarr=  explode('|',$fldstr);
            $options=array();
            foreach ($fldarr as $key=>$value) {
                array_push($options, $value);
            }
            $flds=$this->items_model->get_export_description($options);
            $report_name=uniq_link(15).'.xls';

            $this->load->model('exportexcell_model');
            $res = $this->exportexcell_model->export_itemdata($results, $options, $flds, $report_name);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['export_url']=$res['url'];
            }
            // $res=$this->prepare_export_file($results,$options,$flds,$filename);
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }



    // Prepare pages
    private function _prepare_dbpage_content($page_name, $search='')
    {
        if ($page_name=='categories') {
            $this->load->model('categories_model');
            $categories = $this->categories_model->get_categories_list();
            // List view
            $content = $this->load->view('database/categorylist_view',['categories'=>$categories,'current_category'=>-1], TRUE);
            return $content;
        } else {
            $cur_page = 0;
            $order_by = 'item_number';
            $direction = 'asc';
            // $search = '';
            $vendor_id = '';
            $current_pagename = usersession('page_name');
            if ($current_pagename == $page_name) {
                $cur_page = usersession('curpage');
                $order_by = usersession('order_by');
                $direction = usersession('direction');
                $search = usersession('search');
                $vendor_id = usersession('vendor_id');
            }
            $this->load->model('items_model');
            $total_rec = $this->items_model->count_searchres($search, 'ALL', $vendor_id);
            $this->load->model('vendors_model');
            if ($page_name=='itemprice') {
                $this->load->model('otherprices_model');
                $priority = '';
                $othervendor = $this->otherprices_model->get_othervendors();
                $othervend = array();
                foreach ($othervendor as $row) {
                    array_push($othervend, $row['other_vendor_id']);
                }
                if ($current_pagename == $page_name) {
                    $priority = usersession('priority');
                    $othervend = usersession('othervend');
                }
                /* Prepare contetn for display */
                /* View Window Legend */
                $mindiff = $this->config->item('price_diff');
                $outvend = array();
                foreach ($othervendor as $row) {
                    if (in_array($row['other_vendor_id'], $othervend)) {
                        $row['chk'] = "checked='checked'";
                    } else {
                        $row['chk'] = '';
                    }
                    $outvend[] = $row;
                }
                $legend_options = array(
                    'mindiff' => $mindiff,
                    'search' => $search,
                    'vendors' => $this->vendors_model->get_vendors(),
                    'priority' => $priority,
                    'othervendor' => $outvend,
                    'vendor' => $vendor_id,
                    'order_by' => $order_by,
                    'direction' => $direction,
                );
                if ($this->USR_ROLE == 'general') {
                    $legend = $this->load->view('database/dbprice_general_legend_view', $legend_options, TRUE);
                } else {
                    $legend = $this->load->view('database/dbprice_legend_view', $legend_options, TRUE);
                }
                $content_dat = array(
                    'legend' => $legend,
                    'order_by' => $order_by,
                    'direction' => $direction,
                    'total_rec' => $total_rec,
                    'cur_page' => $cur_page,
                    'search' => $search,
                    'perpage' => $this->config->item('dbview_perpage'),
                );
                if ($this->USR_ROLE == 'general') {
                    $table_dat = $this->load->view('database/dbprice_generaldata_view', $content_dat, TRUE);
                } else {
                    $table_dat = $this->load->view('database/dbprice_data_view', $content_dat, TRUE);
                }
            } elseif ($page_name=='itemcategory') {
                $legend_options=array(
                    'search'=>$search,
                    'vendors' => $this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbcategory_legend_view', $legend_options, TRUE);
                $content_dat=[
                    'order_by'=>$order_by,
                    'total_rec'=>$total_rec,
                    'direction'=>$direction,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                $table_dat=$this->load->view('database/dbcategory_data_view',$content_dat,TRUE);
            } elseif ($page_name=='itemsequence') {
                $total_options=[];
                if ($vendor_id) {
                    $total_options['vendor_id']=$vendor_id;
                }
                if (!empty($search)) {
                    $total_options['search']=$search;
                }
                $total_rec=$this->items_model->get_sequence_count($total_options);
                /* Prepare contetn for display */
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'vendors'=>$this->vendors_model->get_itemseq_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbsequence_legend_view',$legend_options,TRUE);

                $content_dat=array(
                    'total_rec'=>$total_rec,
                    'cur_page'=>$cur_page,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                );
                $table_dat=$this->load->view('database/dbsequence_data_view',$content_dat,TRUE);
            } elseif ($page_name=='itemmisinfo') {
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'vendors'=>$this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbmisinfo_legend_view',$legend_options,TRUE);

                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$direction,
                    'cur_page'=>$cur_page,
                    'new_available' => 1,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                $table_dat=$this->load->view('database/dbmisinfo_data_view',$content_dat,TRUE);

            } elseif ($page_name=='itemprofit') {
                $priority = '';
                if ($current_pagename == $page_name) {
                    $priority = usersession('priority');
                }
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'priority'=>$priority,
                    'vendors'=>$this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                if ($this->USR_ROLE=='general') {
                    $legend=$this->load->view('database/dbprofit_legendgeneral_view', $legend_options, TRUE);
                } else {
                    $legend=$this->load->view('database/dbprofit_legend_view', $legend_options, TRUE);
                }

                $content['new_available']=1;

                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$direction,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                if ($this->USR_ROLE=='general') {
                    $table_dat=$this->load->view('database/dbprofit_datageneral_view',$content_dat,TRUE);
                } else {
                    $table_dat=$this->load->view('database/dbprofit_data_view',$content_dat,TRUE);
                }
            } elseif ($page_name=='itemtemplates') {
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'vendors'=>$this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbtemplate_legend_view',$legend_options,TRUE);

                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$order_by,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                $table_dat=$this->load->view('database/dbtemplate_data_view',$content_dat,TRUE);
            } elseif ($page_name=='itemexport') {
                //
                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$order_by,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    // 'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                    'vendors'=>$this->vendors_model->get_vendors(),
                ];
                $table_dat=$this->load->view('database/dbexport_data_view',$content_dat,TRUE);
            }
            return $table_dat;
        }
    }

    private function _prepare_itemdetails($item_id, $mode='view') {
        $out = ['result' => $this->error_result,'msg'=>'Item Not Found'];
        $this->load->model('items_model');
        $this->load->model('vendors_model');
        $this->load->model('imprints_model');
        $this->load->model('prices_model');
        $this->load->model('otherprices_model');
        $this->load->model('itemcolors_model');
        $this->load->model('similars_model');
        $this->load->model('itemimages_model');
        $res=$this->items_model->get_item($item_id);
        $out['msg']=$res['msg'];
        if ($res['result']==$this->success_result) {
            $out['result']=$this->success_result;
            // Begin
            $item=$res['data'];
            if ($mode=='edit') {
                $session_id=uniq_link(15);
                $item['item_vector_id']=1;
                $session_data=[
                    'item' => $item,
                ];
            }

            $data=[];
            $itemsequence_view='&nbsp;';
            if ($item['item_active']==1) {
                $seqoptions = [
                    'item_sequence' => $item['item_sequence'],
                    'items_total' => $this->items_model->get_items_count(['item_active'=>1]),
                    'mode' => $mode,
                ];
                $itemsequence_view = $this->load->view('itemdetails/itemsequence_view',$seqoptions, TRUE);
            }

            $headoptions=array(
                'item_name'=>$item['item_name'],
                'itemseq_view' => $itemsequence_view,
                'mode' => $mode,
            );
            if ($mode=='edit') {
                $headoptions['session']=$session_id;
            }
            /* Header */
            $data['header']=$this->load->view('itemdetails/detailhead_view',$headoptions,TRUE);
            if ($mode=='edit') {
                $session_data['commons']=$this->items_model->get_commonterms_item($item_id,10);
            }
            // Key Info
            $info_options = [
                'item' => $item,
                'mode' => $mode,
            ];
            $data['keyinfo']=$this->load->view('itemdetails/keyinfo_view',$info_options,TRUE);
            // Get Data about Item Colors
            $offset=0;
            $limit=$this->config->item('slider_images');
            $img_display = $this->itemimages_model->get_images_item($item_id,$limit,$offset);
            if ($mode=='edit') {
                $session_data['item_images']=$img_display;
            }
            /* Video View */
            $item_dat['videooption']='';
            if ($mode=='edit') {
                $data['outofstock'] = $this->load->view('itemdetails/outofstock_edit', $item, TRUE);
            } else {
                $data['outofstock'] = $this->load->view('itemdetails/outofstock_view', $item, TRUE);
            }


            $img_options=array(
                'images'=>$img_display,
                'pos'=>0,
                'edit'=>0,
                'limit'=>$limit,
                'video'=> '', // $video,
                'audio'=> '', // $audio,
                'faces'=> '', // $faces,
            );

            $pictures_dat=$this->load->view('itemdetails/pictures_dat_view',$img_options,TRUE);
            $pictures_slider=$this->load->view('itemdetails/pictures_slider_view',$img_options,TRUE);
            $media_options=array(
                'imagesdata'=>$pictures_dat,
                'slider'=>$pictures_slider,
                'images_count'=>count($img_display),
            );
            $data['images']=$this->load->view('itemdetails/images_view',$media_options,TRUE);
            // Vector
            $data['vectorfiledata']=$this->load->view('itemdetails/vectorfile_view',$item,TRUE);
            /* Vendor Item Dat */
            if (empty($item['printshop_inventory_id'])) {
                $vendor_dat=$this->vendors_model->get_vendor_item($item['vendor_item_id']);
            } else {
                $vendor_dat=$this->vendors_model->get_inventory_item($item['printshop_inventory_id']);
            }

            $vendor_prices=$this->vendors_model->get_vedorprice_item($item['vendor_item_id'],1);
            $vend_options=array(
                'vendor'=>$vendor_dat,
                'vendprice'=>$vendor_prices,
                'mode' => $mode,
            );
            if (empty($item['printshop_inventory_id'])) {
                $data['vendordata']=$this->load->view('itemdetails/vendordata_view',$vend_options,TRUE);
            } else {
                $data['vendordata']=$this->load->view('itemdetails/inventoryitemdata_view',$vend_options,TRUE);
            }
            if ($mode=='view') {
                $data['vendorprices']=$this->load->view('itemdetails/vendorprice_view',$vend_options,TRUE);
            } else {
                $data['vendorprices']=$this->load->view('itemdetails/vendorpriceedit_view',$vend_options,TRUE);
                $session_data['vendor']=$vendor_dat;
                $session_data['vendor_prices']=$vendor_prices;
            }

            $data['shiplink_view']=$this->load->view('itemdetails/shiplink_view',array('item_id'=>$item_id),TRUE);
            // Special Checkout
            if ($mode=='edit') {
                $session_data['special_prices']=$this->items_model->get_special_prices($item_id,1);
            }
            // Get Data about item Imprint Locations

            if ($mode=='view') {
                $imprint = $this->imprints_model->get_imprint_item($item_id);
                $imprintdata=$this->load->view('itemdetails/imprintsdata_view',array('imprint'=>$imprint),TRUE);
            } else {
                $imprint = $this->imprints_model->get_imprint_edit_item($item_id);
                $imprintdata=$this->load->view('itemdetails/imprintsedit_view',array('imprint'=>$imprint),TRUE);
                $session_data['imprints']=$imprint;
            }
            $imprint_options=array(
                'imprint_data'=>$imprintdata,
                'mode' => $mode,
            );
            $data['imprints']=$this->load->view('itemdetails/imprints_view',$imprint_options,TRUE);
            /* Get Data about item Colors */
            if (empty($item['printshop_inventory_id'])) {
                $colors = $this->itemcolors_model->get_colors_item($item_id, ($mode=='view' ? 0 : 1));
                $color_options=array(
                    'option'=>$item['options'],
                    'colors'=>$colors,
                );
                if ($mode=='view') {
                    $data['options']=$this->load->view('itemdetails/colorsview_view',$color_options, TRUE);
                } else {
                    $data['options']=$this->load->view('itemdetails/colorsedit_view',$color_options, TRUE);
                    $session_data['item_colors']=$colors;
                }
            } else {
                $colors = $this->itemcolors_model->get_inventcolors_item($item['printshop_inventory_id']);
                $color_options=array(
                    'colors'=>$colors,
                );
                $data['options']=$this->load->view('itemdetails/stockcolorsview_view',$color_options, TRUE);
            }
            if ($mode=='view') {
                $data['metadata']=$this->load->view('itemdetails/metaview_view',$item,TRUE);
            } else {
                $data['metadata']=$this->load->view('itemdetails/metaedit_view',$item,TRUE);
            }

            // Get Data About item_price
            $research_price=array();

            if ($this->USR_ROLE=='general') {
                $data['pricesdat']='';
                $data['pricearea']='';
            } else {
                if ($item['item_template']==$this->OTHER_TEMPLATE) {
                    /* */
                    $price_dats = $this->prices_model->get_promoprices_edit($item_id);
                    $prices=$price_dats['qty_prices'];
                    $common_prices=$price_dats['common_prices'];
                    if ($mode=='view') {
                        $price_options=array(
                            'prices'=>$prices,
                            'common_prices'=>$common_prices,
                            'numprices'=> $this->MAX_PROMOPRICES-1,
                        );
                        $profitdat=$this->load->view('itemdetails/promo_profit_view',$price_options,TRUE);
                        $prices_view=$this->load->view('itemdetails/promo_itempriceview_view',$price_options,TRUE);
                        $priceview_options=array(
                            'profitdat'=>$profitdat,
                            'pricesdata'=>$prices_view,
                        );
                        $data['pricesdat']=$this->load->view('itemdetails/promoitem_pricesview_view',$priceview_options,TRUE);
                    } else {
                        $price_options=array(
                            'prices'=>$prices,
                            'common_prices'=>$common_prices,
                            'numprices'=> $this->MAX_PROMOPRICES-1,
                        );
                        // $data['profitdat']=$this->load->view('itemdetails/promo_profit_view', $price_options,TRUE);
                        // $data['prices_view']=$this->load->view('itemdetails/promo_itempriceedit_view',$price_options,TRUE);
                        $profitdat=$this->load->view('itemdetails/promo_profit_view', $price_options,TRUE);
                        $prices_view=$this->load->view('itemdetails/promo_itempriceedit_view',$price_options,TRUE);
                        $session_data['item_prices']=$prices;
                        $session_data['common_prices']=$common_prices;
                        $priceview_options = [
                            'profitdat'=>$profitdat,
                            'pricesdata'=>$prices_view,
                        ];
                        $data['pricesdat']=$this->load->view('itemdetails/promoitem_pricesview_view',$priceview_options,TRUE);
                    }
                } else {
                    $prices=$this->prices_model->get_price_itemedit($item_id);
                    /* Get Data about Research of price */
                    $research_price=$this->otherprices_model->get_prices_item($item_id);
                    $outresearch=$this->otherprices_model->compare_prices_item($prices,$research_price);
                    if ($mode=='view') {
                        $research_options = [
                            'research_price'=>$outresearch,
                            'price_types'=>$this->config->item('price_types'),
                        ];
                        $research_data=$this->load->view('itemdetails/research_data_view',$research_options,TRUE);
                        $profit_options = [
                            'prices'=>$prices,
                            'price_types'=>$this->config->item('price_types'),
                        ];
                        $profitdat=$this->load->view('itemdetails/stressball_profit_view',$profit_options,TRUE);
                        $numprice=count($this->config->item('price_types'))-1;
                        $priceview_options = [
                            'prices'=>$prices,
                            'price_types'=>$this->config->item('price_types'),
                            'numprice'=>$numprice,
                        ];
                        $prices_view=$this->load->view('itemdetails/stressball_itempriceview_view',$priceview_options,TRUE);

                        $price_options=array(
                            'researchdata'=>$research_data,
                            'price_types'=>$this->config->item('price_types'),
                            'numprice'=>$numprice,
                            'profit_dat'=>$profitdat,
                            'prices'=>$prices_view,
                        );
                        $data['pricesdat']=$this->load->view('itemdetails/stressball_pricesview_view',$price_options,TRUE);
                    } else {
                        $research_options = [
                            'research_price'=>$outresearch,
                            'price_types'=>$this->config->item('price_types'),
                        ];
                        $research_data=$this->load->view('itemdetails/researchedit_data_view',$research_options,TRUE);
                        $profit_options = [
                            'prices'=>$prices,
                            'price_types'=>$this->config->item('price_types'),
                        ];
                        $profitdat=$this->load->view('itemdetails/stressball_profit_view',$profit_options,TRUE);
                        $numprice=count($this->config->item('price_types'))-1;
                        $priceview_options= [
                            'prices'=>$prices,
                            'price_types'=>$this->config->item('price_types'),
                            'numprice'=>$numprice,
                        ];
                        $prices_view=$this->load->view('itemdetails/stressball_itempriceedit_view',$priceview_options,TRUE);
                        // Collect all together
                        $price_options=array(
                            'researchdata'=>$research_data,
                            'price_types'=>$this->config->item('price_types'),
                            'numprice'=>$numprice,
                            'profit_dat'=>$profitdat,
                            'prices'=>$prices_view,
                        );
                        $data['pricesdat']=$this->load->view('itemdetails/stressball_pricesview_view',$price_options,TRUE);
                        $session_data['item_prices']=$prices;
                        $session_data['research_prices']=$research_price;
                    }
                }
                $data['pricearea']='active';
            }
            if ($mode=='view') {
                $data['attributes']=$this->load->view('itemdetails/attribview_view',$item, TRUE);
            } else {
                $data['attributes']=$this->load->view('itemdetails/attribedit_view',$item, TRUE);
            }
            // Get Data about Similar Items
            $similar = $this->similars_model->get_similar_items($item_id);
            if ($mode=='view') {
                $data['simulardata']=$this->load->view('itemdetails/simulitems_view',array('similar'=>$similar),TRUE);
            } else {
                $item_list=$this->items_model->get_item_list($item_id);
                $simular_options =[
                    'similar'=>$similar,
                    'item_list' => $item_list,
                ];
                $data['simulardata']=$this->load->view('itemdetails/simuledit_view', $simular_options,TRUE);
                $session_data['simular']=$similar;
            }
            $footer_options=[
                'edit'=>($mode=='view' ? 0 : 1),
                'item' => $item,
                'commons'=>'',
            ];
            if ($item['item_template']==$this->STRESSBALL_TEMPLATE) {
                $footer_options['commons']='Common Terms';
            }
            $data['footer']=$this->load->view('itemdetails/itemdetfooter_view',$footer_options,TRUE);
            $content=$this->load->view('itemdetails/details_view',$data,TRUE);
            if ($mode=='edit') {
                $session_data['deleted']=[];
                usersession($session_id, $session_data);
            }
            $out['content'] = $content;
        }
        return $out;
    }

    private function _prepare_vendors_view() {
//        $this->load->model('vendors_model');
//        $totals=$this->vendors_model->get_count_vendors();
//        $options=array(
//            'perpage'=> 250,
//            'order'=>'vendor_name',
//            'direc'=>'asc',
//            'total'=>$totals,
//            'curpage'=>0,
//        );
//        $content=$this->load->view('fulfillment/vendors_view', $options, TRUE);
        $this->load->model('vendors_model');
        $totals=$this->vendors_model->get_count_vendors(['status' => 1]);
        $options=array(
            'perpage'=> 100,
            'order'=>'vendor_name',
            'direc'=>'asc',
            'total'=>$totals,
            'curpage'=>0,
        );
        $content = $this->load->view('vendorcenter/page_view', $options, TRUE);
        return $content;
    }

    private function _prepare_shipping_view($brand) {
        $months=array();
        for ($i=1; $i<13; $i++) {
            $key=str_pad($i,2,'0',STR_PAD_LEFT);
            $name=date('F',strtotime('1980-'.$key.'-01'));
            $months[]=array('id'=>$key, 'name'=>$name);
        }
        $this->load->model('shipping_model');
        $mindate=$this->shipping_model->shipcalk_stardate($brand);
        $startyear=date('Y',$mindate);
        $curyear=date('Y');
        $years=array();
        for ($i=$startyear; $i<=$curyear; $i++) {
            $years[]=array('id'=>$i, 'name'=>$i);
        }
        $options=[
            'months'=>$months,
            'years'=>$years,
            'curmonth'=>date('m'),
            'curyear'=>date('Y'),
            'brand' => $brand,
        ];
        $content=$this->load->view('settings/shippings_view',$options,TRUE);
        return $content;
    }

    private function _prepare_btitemdata_view() {
        $brand = 'BT';
        $this->load->model('items_model');
        $this->load->model('vendors_model');
        $this->load->model('categories_model');
        $categories = $this->categories_model->get_reliver_categories(['brand'=>'BT']);
        // Check items
        $idx=0;
        foreach ($categories as $category) {
            if ($category['category_active']==0) {
                $cntitems = $this->items_model->get_items_count(['brand' => 'BT', 'category_id' => $category['category_id']]);
                if ($cntitems > 0) {
                    $categories[$idx]['category_active'] = 1;
                    // Update categories
                    $this->categories_model->activate_reliver_categories($category['category_id']);
                }
            }
        }
        $activcategory = 0;
        foreach ($categories as $category) {
            if ($category['category_active']==1) {
                $activcategory = $category['category_id'];
                $activcategory_label = $category['category_code'].' - '.$category['category_name'];
                break;
            }
        }
        if ($activcategory == 0) {
            $activcategory = $categories[0]['category_id'];
            $activcategory_label = $categories[0]['category_code'].' - '.$categories[0]['category_name'];
        }
        $brandtotal = $this->items_model->get_items_count(['brand' => 'BT']);
        $cntitems = $this->items_model->get_items_count(['brand' => 'BT', 'category_id' => $activcategory]);

        $options = [
            'perpage' => 250,
            'order' => 'item_number',
            'direct' => 'asc',
            'totals' =>  $cntitems,
            'brand' => $brand,
            'vendors' => $this->vendors_model->get_vendors(),
            'categories' => $categories,
            'category_id' => $activcategory,
            'category_label' => $activcategory_label,
            'brandtotal' => $brandtotal,
        ];
        // $content = $this->load->view('dbitems/itemslist_view', $options, TRUE);
        $content = $this->load->view('btitems/itemslist_view', $options, TRUE);
        return $content;
    }

    private function _prepare_sritems_content() {
        $this->load->model('categories_model');
        $this->load->model('items_model');
        $this->load->model('vendors_model');
        $categories = $this->categories_model->get_reliver_categories(['brand'=>'SR']);
        $activcategory = $categories[0]['category_id'];
        $activcategory_label = $categories[0]['category_code'].' - '.$categories[0]['category_name'];
        $cntitems = $this->items_model->get_items_count(['brand' => 'SR', 'category_id' => $activcategory]);
        $vendors = $this->vendors_model->get_vendors();
        $options = [
            'categories' => $categories,
            'totals' => $cntitems,
            'category_id' => $activcategory,
            'category_label' => $activcategory_label,
            'vendors' => $vendors,
        ];
        $content = $this->load->view('relieveritems/page_view', $options,TRUE);
        return $content;
    }

    private function _prepare_inventory_view() {
        $this->load->model('inventory_model');
        // $this->load->model('printshop_model');
        // $addcost=$this->printshop_model->invaddcost();
        $invtypes = $this->inventory_model->get_inventory_types();
        $idx=0;
        $totalval = 0;
        foreach ($invtypes as $invtype) {
            $stock = $this->inventory_model->get_inventtype_stock($invtype['inventory_type_id']);
            $totalval+=$stock;
            $invtypes[$idx]['value'] = empty($stock) ? $this->empty_html_content : MoneyOutput($stock);
            $idx++;
        }
        // Get totals
        $type_id = $invtypes[0]['inventory_type_id'];
        $totals = $this->inventory_model->get_inventory_totals($type_id);
        $addcost = $invtypes[0]['type_addcost'];
        $addval = $totals['available'] * $addcost;
        // Get OnBoats
        $onboats = $this->inventory_model->get_data_onboat($type_id, $this->container_type);
        $boathead_view='';
        $boatlinks_view = '';
        foreach ($onboats as $onboat) {
            $boathead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $onboat, TRUE);
            $boatlinks_view.=$this->load->view('masterinvent/onboat_containerlinks_view', $onboat, TRUE);
        }
        // Build head containers  content
        $slider_width=60*count($onboats);
        $margin = $this->maxlength-$slider_width;
        $margin=($margin>0 ? 0 : $margin);
        // $width_edit = 58;
        $boatoptions=array(
            'data'=>$onboats,
            'container_view' => $boathead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $onboat_content = $this->load->view('masterinvent/onboathead_view', $boatoptions, TRUE);
        $linkoptions = [
            'data'=>$onboats,
            'container_view' => $boatlinks_view,
            'width' => $slider_width,
            'margin' => $margin,
        ];
        $onboat_links = $this->load->view('masterinvent/onboatlinks_view', $linkoptions, TRUE);
        $container_leftview = ($margin < 0 ? 1 : 0);
        // Prepare Expres
        $expresses = $this->inventory_model->get_data_onboat($type_id, $this->express_type);
        $expresshead_view = '';
        $expresslinks_view = '';
        foreach ($expresses as $express) {
            $expresshead_view.=$this->load->view('masterinvent/onboat_containerhead_view', $express, TRUE);
            $expresslinks_view.=$this->load->view('masterinvent/onboat_containerlinks_view', $express, TRUE);
        }
        // Build head containers  content
        $slider_width=60*count($expresses);
        $margin = $this->maxlength-$slider_width;
        $margin=($margin>0 ? 0 : $margin);
        // $width_edit = 58;
        $expressoptions=array(
            'data'=>$expresses,
            'container_view' => $expresshead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $express_content = $this->load->view('masterinvent/onboathead_view', $expressoptions, TRUE);
        $linkoptions = [
            'data'=>$onboats,
            'container_view' => $expresslinks_view,
            'width' => $slider_width,
            'margin' => $margin,
        ];
        $express_links = $this->load->view('masterinvent/onboatlinks_view', $linkoptions, TRUE);
        $express_leftview = ($margin < 0 ? '1' : 0);
        $options = [
            'invtypes' => $invtypes,
            'active_type' => $invtypes[0]['inventory_type_id'],
            'export_type' => $invtypes[0]['type_short'],
            'total' => empty($totalval) ? $this->empty_html_content : MoneyOutput($totalval),
            // 'eventtype' => 'purchasing',
            'eventtype' => 'manufacturing',
            'addcost' => $addcost,
            'addval' => empty($addval) ? '-' : MoneyOutput($addval,0),
            'maxval' => empty($totals['max']) ? $this->empty_html_content : QTYOutput($totals['max']),
            'maxtotal' => empty($totals['maxsum']) ? $this->empty_html_content : MoneyOutput($totals['maxsum']),
            'itempercent' => $totals['itempercent'],
            'instock' => empty($totals['instock']) ? $this->empty_html_content : QTYOutput($totals['instock']),
            'reserved' => empty($totals['reserved']) ? $this->empty_html_content : QTYOutput($totals['reserved']),
            'available' => empty($totals['available']) ? $this->empty_html_content : QTYOutput($totals['available']),
            'container_head' => $onboat_content,
            'container_leftview' => $container_leftview,
            'container_links' => $onboat_links,
            'express_head' => $express_content,
            'express_links' => $express_links,
            'express_leftview' => $express_leftview,
        ];
        $content = $this->load->view('masterinvent/page_view', $options, TRUE);
        return $content;
    }

    private function _prepare_mastersettings_view() {
        $this->load->model('calendars_model');
        $totals = $this->calendars_model->count_calendars('ALL');
        $orderby='calendar_id';
        $direc='asc';

        $options=array(
            'total'=>$totals,
            'perpage'=>$this->PERPAGE,
            'orderby'=>$orderby,
            'direct'=>$direc,
        );
        $calendar_view=$this->load->view('settings/calendars_view',$options,TRUE);
        $this->load->model('shipping_model');
        $search_templates=$this->shipping_model->get_country_search_templates();
        $options = [
            'search_templ' => $search_templates,
        ];
        $countries_view=$this->load->view('settings/countries_view', $options,TRUE);
        $setting_options = [
            'calendar_view' => $calendar_view,
            'countries_view' => $countries_view,
        ];
        $content = $this->load->view('database_center/master_settings_view', $setting_options, TRUE);
        return $content;
    }

}