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
        $head = [];
        $head['title'] = 'Database Center';
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $this->pagelink);
        $master=[];
        $channelsb=[];
        $channelnsb=[];
        $channelbt=[];
        $channellbt=[];
        $channelamz=[];
        $channelcnt = 0;
        foreach ($menu as $mitems) {
            if ($mitems['item_link']=='#dbcentermaster') {
                foreach ($mitems['submenu'] as $mitem) {
                    $master[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            } elseif ($mitems['item_link']=='#dbcentersbchannel') {
                $channelcnt = 1;
                foreach ($mitems['submenu'] as $mitem) {
                    $channelsb[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            } elseif ($mitems['item_link']=='#dbcenternsbchannel') {
                $channelcnt = 1;
                foreach ($mitems['submenu'] as $mitem) {
                    $channelnsb[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            } elseif ($mitems['item_link']=='#dbcenterbtchannel') {
                $channelcnt = 1;
                foreach ($mitems['submenu'] as $mitem) {
                    $channelbt[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            } elseif ($mitems['item_link']=='#dbcenterlbtchannel') {
                $channelcnt = 1;
                foreach ($mitems['submenu'] as $mitem) {
                    $channellbt[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            } elseif ($mitems['item_link']=='#dbcenteramazonchannel') {
                $channelcnt = 1;
                foreach ($mitems['submenu'] as $mitem) {
                    $channelamz[] = [
                        'item_link' => $mitem['item_link'],
                    ];
                }
            }
        }

        $content_options = [
            'master' => $master,
            'channelcnt' => $channelcnt,
            'channelsb' => $channelsb,
            'channelnsb' => $channelnsb,
            'channelbt' => $channelbt,
            'channellbt' => $channellbt,
            'channelamz' => $channelamz,
        ];
        $search = usersession('liftsearch');
        usersession('liftsearch', NULL);
        // Add main page management
        $head['styles'][] = array('style' => '/css/database_center/main_addoptpage.css');
        $head['scripts'][] = array('src' => '/js/database_center/main_page.js');
        // Item details
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'adaptive' => 1,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_view = $this->load->view('database_center/page_device_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function masteritems() {
        $head = [];
        $start = $this->input->get('start');
        $head['title'] = 'Database Center Master';
        $pagelnk = '#dbcentermaster';
        $menu = $this->menuitems_model->get_submenu($this->USR_ID, $pagelnk);
        $menu_options = [
            'menu' => $menu,
            'start' => $start,
        ];
        $page_menu = $this->load->view('database_center/master_adptive_headmenu', $menu_options, TRUE);
        // Add main page management
        $content_options=[
            'page_menu' => $page_menu,
        ];
        foreach ($menu as $row) {
            if ($row['item_link']=='#mastercustomer') {

            } elseif ($row['item_link']=='#mastervendors') {
                $head['styles'][]=array('style'=>'/css/database_center/vendorsadapview.css');
                $head['styles'][]=array('style'=>'/css/database_center/vendordetails_adaptive.css');
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
        $head['styles'][] = array('style' => '/css/database_center/master_adaptivepage.css');
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
        // Item details
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'gmaps' => ifset($head, 'gmaps', 0),
            'adaptive' => 1,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_view = $this->load->view('database_center/master_page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function channelitems() {
        $head = [];
        $start = $this->input->get('start');
        $brand = $this->input->get('brand');
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
        $content = $this->load->view('vendorcenter/page_adaptive_view', $options, TRUE);
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

    private function _prepare_itemdata_view($brand) {
        $this->load->model('items_model');
        $totals = $this->items_model->count_searchres('', $brand);
        $this->load->model('vendors_model');
        $options = [
            'perpage' => 250,
            'order' => 'item_number',
            'direct' => 'asc',
            'totals' =>  $totals,
            'brand' => $brand,
            'vendors' => $this->vendors_model->get_vendors(),
        ];
        $content = $this->load->view('dbitems/itemslist_view', $options, TRUE);
        return $content;
    }

    private function _prepare_inventory_view() {
        $this->load->model('inventory_model');
        $invtypes = $this->inventory_model->get_inventory_types();
        $idx=0;
        foreach ($invtypes as $invtype) {
            $invtypes[$idx]['value'] = 692500;
            $idx++;
        }
        $options = [
            'invtypes' => $invtypes,
            'active_type' => $invtypes[0]['inventory_type_id'],
            'export_type' => $invtypes[0]['type_short'],
        ];
        $content = $this->load->view('masterinvent/page_view', $options, TRUE);
        return $content;
    }

}
