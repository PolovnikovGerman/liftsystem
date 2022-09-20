<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Databasecenter extends MY_Controller
{
    private $pagelink = '/databasecenter';
    private $Inventory_Source='Stock';
    private $STRESSBALL_TEMPLATE='Stressball';
    private $OTHER_TEMPLATE='Other Item';
    private $MAX_PROMOPRICES = 10;
    protected $PERPAGE=1000;
    private $session_error = 'Edit session lost. Please, reload page';

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
        $getdata = $this->input->get();
        $start = ifset($getdata,'start','');
        if ($start=='') {
            $menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
            if ($menu[0]['item_link']=='#dbcentermaster') {
                $this->masteritems();
            } elseif ($menu[0]['item_link']=='#dbcenterbtchannel') {
                $this->btchannelitems();
            } elseif ($menu[0]['item_link']=='#dbcenterrelievers') {

            }
        } else {
            //
            if ($start=='dbcenterbtchannel') {
                $this->btchannelitems();
            } elseif ($start=='dbcentermaster') {
                $this->masteritems();
            } elseif ($start=='dbcenterrelievers') {
                $this->srchannelitems();
            }
        }
    }

    public function masteritems() {
        $head = [];
//        $start = $this->input->get('start');
//        if (empty($start)) {
//            $start='#dbcentermaster';
//        }
        $head['title'] = 'Database Center Master';
        $pagelnk = '#dbcentermaster';
        $main_menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
        $menu_options = [
            'menus' => $main_menu,
            'start' => $pagelnk,
        ];
        $page_menu = $this->load->view('database_center/main_menu_view', $menu_options, TRUE);
        // Add main page management
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);

        $content_options=[
            'page_menu' => $page_menu,
            'menu' => $menu,
            'start' => str_replace('#','',$menu[0]['item_link'])
        ];
        foreach ($menu as $row) {
            if ($row['item_link']=='#mastervendors') {
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
        $head['styles'][] = array('style' => '/css/database_center/main_page.css');
        // $head['styles'][] = array('style' => '/css/database_center/master_page.css');
        $head['scripts'][] = array('src' => '/js/database_center/master_page.js');

        // Utils
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
        $head['scripts'][] = array('src'=>'/js/adminpage/easySlider1.5.js');
        $head['scripts'][] = array('src'=> '/js/adminpage/jquery.autocompleter.js');
        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.fileDownload.js');
        // Order popup
        $head['styles'][]=array('style'=>'/css/leadorder/popup.css');
        $head['scripts'][]=array('src'=>'/js/leads/leadorderpopup.js');
        // Item details
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'gmaps' => ifset($head, 'gmaps', 0),
            'adaptive' => 0,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_view = $this->load->view('database_center/master_page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function btchannelitems() {
        $head = [];
        $head['title'] = 'Database Center Bluetrack/Stressalls';
        $pagelnk = '#dbcenterbtchannel';
        $main_menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
        $menu_options = [
            'menus' => $main_menu,
            'start' => $pagelnk,
        ];

        $page_menu = $this->load->view('database_center/main_menu_view', $menu_options, TRUE);
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);
        if (empty($menu))  {
            redirect('/');
        }

        $content_options=[
            'page_menu' => $page_menu,
            'menu' => $menu,
            'start' => str_replace('#','',$menu[0]['item_link'])
        ];
        $pagescontent = 1;
        foreach ($menu as $item) {
            if ($item['item_link']=='#btitems') {
                //$head['styles'][]=array('style'=>'/css/database_center/itemdatalist.css');
                //$head['scripts'][]=array('src'=>'/js/database_center/itemdatalist.js');
                //$head['styles'][] = array('style' => '/css/database_center/itemlistdetails.css');
                //$head['scripts'][]=array('src' => '/js/database_center/itemlistdetails.js');
                $head['styles'][]=array('style'=>'/css/database_center/btitemslist.css');
                $head['scripts'][] = array('src'=>'/js/database_center/btitemlist.js');
                $head['styles'][] = array('style' => '/css/database_center/btitemdetails.css');
                $head['scripts'][]=array('src' => '/js/database_center/btitemdetails.js');
                $head['styles'][] = array('style' => '/css/page_view/popover.css');
                $head['scripts'][] = array('src' => '/js/adminpage/popover.js');
                $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
                $content_options['itemsview'] = $this->_prepare_btitemdata_view();
            } elseif ($item['item_link']=='#btcustomers') {
                $content_options['customersview'] = $this->load->view('customers/page_view',[],TRUE);
            } elseif ($item['item_link']=='#sbpages') {
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
            } elseif ($item['item_link']=='#btpages') {
                if ($pagescontent==0) {
                    $head['scripts'][]=array('src'=>'/js/adminpage/uEditor.js');
                    $head['styles'][]=array('style'=>'/css/page_view/uEditor.css');
                }
                $submenu_options = [
                    'menus' => [],
                    'brand' => 'BT',
                ];
                $bt_options['submenu'] = $this->load->view('content/submenu_view', $submenu_options, TRUE);
                $content_options['btpagesview'] = $this->load->view('content/page_content_view', $bt_options, TRUE);
            } elseif ($item['item_link']=='#btsettings') {
                $content_options['settingsview'] = $this->load->view('site_settings/page_view',[],TRUE);
            }

        }

        // Item details
        $head['styles'][] = array('style' => '/css/database_center/main_page.css');
        $head['scripts'][] = array('src' => '/js/database_center/btchannel_page.js');
        //  Utils
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/adminpage/easySlider1.5.js');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
        // Autocompleter
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.autocompleter.js');
        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
        // Cycle
        $head['scripts'][] = array('src' => '/js/cycle2/jquery.cycle2.min.js');

        $options = ['title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'gmaps' => ifset($head, 'gmaps', 0)
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $this->load->view('database_center/btchannel_page_view', $content_options, TRUE);
        $this->load->view('page/page_template_view', $dat);
    }

    public function srchannelitems() {
        $head = [];
        $head['title'] = 'Database Center Bluetrack/Stressalls';
        $pagelnk = '#dbcenterrelievers';
        $main_menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
        $menu_options = [
            'menus' => $main_menu,
            'start' => $pagelnk,
        ];

        $page_menu = $this->load->view('database_center/main_menu_view', $menu_options, TRUE);
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);
        if (empty($menu))  {
            redirect('/');
        }

        $content_options=[
            'page_menu' => $page_menu,
            'menu' => $menu,
            'start' => str_replace('#','',$menu[0]['item_link'])
        ];

        foreach ($menu as $item) {
            if ($item['item_link']=='#sritems') {
                $head['styles'][]=array('style'=>'/css/database_center/relivitemlist.css');
                $head['scripts'][]=array('src'=>'/js/database_center/relivitemlist.js');
                $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
                $head['styles'][] = array('style' => '/css/database_center/relieveitemdetails.css');
                $head['scripts'][]=array('src' => '/js/database_center/relieveitemdetails.js');
                $content_options['itemsview'] = $this->_prepare_sritems_content();
            } elseif ($item['item_link']=='#srcustomers') {
                $content_options['customersview'] = $this->load->view('relievercustomers/page_view',[],TRUE);
            } elseif ($item['item_link']=='#srpages') {
                $sr_options = [];
                $sr_options['srhomeview'] = $this->load->view('content/template_view',['link'=>'srhomeview'], TRUE);
                $sr_options['sraboutusview'] = $this->load->view('content/template_view',['link'=>'sraboutusview'], TRUE);
                $sr_options['srcontactusview'] = $this->load->view('content/template_view',['link'=>'srcontactusview'], TRUE);
                /*
                $sr_options['sbcustomshappedview'] = $this->load->view('content/template_view',['link'=>'sbcustomshappedview'], TRUE);
                $sr_options['sbserviceview'] = $this->load->view('content/template_view',['link'=>'sbserviceview'], TRUE);
                $sr_options['sbfaqview'] = $this->load->view('content/template_view',['link'=>'sbfaqview'], TRUE);
                $sr_options['sbtermsview'] = $this->load->view('content/template_view',['link'=>'sbtermsview'], TRUE);
                */
                $submenu = [];
                $submenu[] = ['item_link' => '#srhomeview', 'item_name' => 'Home Page'];
                $submenu[] = ['item_link' => '#sraboutusview', 'item_name' => 'About Us'];
                $submenu[] = ['item_link' => '#srcontactusview', 'item_name' => 'Contact Us'];
                $submenu[] = ['item_link' => '#srartinfo', 'item_name' => 'Art & Info'];
                $submenu[] = ['item_link' => '#srhowtoorder', 'item_name' => 'How to Order'];
                $submenu[] = ['item_link' => '#srcatalog', 'item_name' => 'Catalog'];
                $submenu[] = ['item_link' => '#srwhyus', 'item_name' => 'Why Us'];
                $submenu[] = ['item_link' => '#srcustomshappedview', 'item_name' => 'Custom Shaped'];
                /*
                $submenu[] = ['item_link' => '#sbserviceview', 'item_name' => 'Services'];
                $submenu[] = ['item_link' => '#sbfaqview', 'item_name' => 'FAQ'];
                $submenu[] = ['item_link' => '#sbtermsview', 'item_name' => 'Terms'];
                */
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


            } elseif ($item['item_link']=='#srsettings') {
                $content_options['settingsview'] = $this->load->view('relieversetting/page_view',[],TRUE);
            }
        }
        $head['styles'][] = array('style' => '/css/database_center/main_page.css');
        $head['scripts'][] = array('src' => '/js/database_center/srchannel_page.js');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        // Autocompleter
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.autocompleter.js');
        $head['styles'][] = array('style' => '/css/page_view/jquery.autocompleter.css');
        // Cycle
        $head['scripts'][] = array('src' => '/js/cycle2/jquery.cycle2.min.js');

        $options = ['title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'gmaps' => ifset($head, 'gmaps', 0)
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $this->load->view('database_center/srchannel_page_view', $content_options, TRUE);
        $this->load->view('page/page_template_view', $dat);

    }

    public function channelitems() {
        $head = [];
        // $start = $this->input->get('start');
        // $brand = $this->input->get('brand');
        $brand = 'bluetrack';
        $start = '';
        if (empty($brand)) {
            redirect('/');
        }
        if ($brand=='stressballs') {
            $head['title'] = 'Database Center Stressalls';
            $pagelnk = '#dbcentersbchannel';
        } elseif ($brand=='nationalsb') {
            $head['title'] = 'Database Center National Stressalls';
            $pagelnk = '#dbcenternsbchannel';
        } elseif ($brand=='bluetrack') {
            $head['title'] = 'Database Center Bluetrack';
            $pagelnk = '#dbcenterbtchannel';
        } elseif ($brand=='btlegacy') {
            $head['title'] = 'Database Center Bluetrack Legacy Site';
            $pagelnk = '#dbcenterlbtchannel';
        } elseif ($brand=='amazon') {
            $head['title'] = 'Database Center Amazon';
            $pagelnk = '#dbcenteramazonchannel';
        }
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);
        if (empty($menu))  {
            redirect('/');
        }
        $menu_options = [
            'menu' => $menu,
            'start' => $start,
            'brand' => $brand,
        ];
        $content_options=[];
        $content_options['page_menu'] = $this->load->view('database_center/channel_head_menu', $menu_options, TRUE);
        // Add
        $itemslist = 0;
        foreach ($menu as $row) {
            if ($row['item_link']=='#sbitems') {
                if ($itemslist==0) {
                    $itemslist = 1;
                    $head['styles'][]=array('style'=>'/css/database_center/itemdatalist.css');
                    $head['scripts'][]=array('src'=>'/js/database_center/itemdatalist.js');
                    $head['styles'][] = array('style' => '/css/database_center/itemlistdetails.css');
                    $head['scripts'][]=array('src' => '/js/database_center/itemlistdetails.js');
                    $head['styles'][] = array('style' => '/css/page_view/popover.css');
                    $head['scripts'][] = array('src' => '/js/adminpage/popover.js');
                    $head['scripts'][] = array('src' => '/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
                }
                $content_options['sbitemsview'] = $this->_prepare_itemdata_view('SB');
            } elseif ($row['item_link']=='#sbpages') {
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

            }
        }

        // Item details
        $head['styles'][] = array('style' => '/css/database_center/channel_page.css');
        $head['scripts'][] = array('src' => '/js/database_center/channel_page.js');
        //  Utils
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/adminpage/easySlider1.5.js');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');

        $options = ['title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'gmaps' => ifset($head, 'gmaps', 0)
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $this->load->view('database_center/channel_page_view', $content_options, TRUE);
        $this->load->view('page/page_template_view', $dat);
    }

    private function _prepare_vendors_view() {
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

    private function _prepare_btitemdata_view() {
        $brand = 'BT';
        $this->load->model('items_model');
        $this->load->model('vendors_model');
        $this->load->model('categories_model');
        $categories = $this->categories_model->get_reliver_categories(['brand'=>'BT']);
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

    private function _prepare_inventory_view() {
        $this->load->model('inventory_model');
        $invtypes = $this->inventory_model->get_inventory_types();
        $idx=0;
        $totalval = 0;
        foreach ($invtypes as $invtype) {
            $stock = $this->inventory_model->get_inventtype_stock($invtype['inventory_type_id']);
            $totalval+=$stock;
            $invtypes[$idx]['value'] = $stock;
            $idx++;
        }
        $options = [
            'invtypes' => $invtypes,
            'active_type' => $invtypes[0]['inventory_type_id'],
            'export_type' => $invtypes[0]['type_short'],
            'total' => $totalval,
            'eventtype' => 'purchasing',
        ];
        $content = $this->load->view('masterinvent/page_view', $options, TRUE);
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

}
