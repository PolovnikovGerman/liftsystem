<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finances extends MY_Controller
{

    private $pagelink = '/finances';
    private $order_profit_perpage = 100;
    private $order_totals_perpage = 100;
    private $perpage_options = array(100, 250, 500, 1000);
    private $restore_data_error = 'Edit Connection Lost. Please, recall form';
    private $weekshow_limit = 104;

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
        $this->load->model('orders_model');
        $this->load->model('balances_model');
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Finance';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);

        $start = $this->input->get('start', TRUE);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#purchaseordersview') {
                $head['styles'][]=array('style'=>'/css/accounting/pototals_adative.css');
                $head['scripts'][]=array('src'=>'/js/accounting/pototals_adative.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['purchaseordersview'] = $this->_prepare_purchaseorders_view($brand, $top_menu);
            } elseif ($row['item_link']=='#accreceiv') {
                $head['styles'][]=array('style'=>'/css/accounting/accreceiv_adapt.css');
                $head['scripts'][]=array('src'=>'/js/accounting/accreceiv.js');
                $brands = $this->menuitems_model->get_brand_pagepermisions($row['brand_access'], $row['brand']);
                if (count($brands)==0) {
                    redirect('/');
                }
                $brand = $brands[0]['brand'];
                $top_options = [
                    'brands' => $brands,
                    'active' => $brand,
                ];
                $top_menu = $this->load->view('page/top_menu_view', $top_options, TRUE);
                $content_options['accreceivview'] = $this->_prepare_accreceiv_view($brand, $top_menu);
            }
        }
        $content_options['menu'] = $menu;
        $content_options['start'] = $start;
        $content_view = $this->load->view('accounting/page_device_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/accounting/page_adaptive.js');
        $head['styles'][] = array('style' => '/css/accounting/accountpage_adaptive.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // DatePicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        /*Google Chart */
        $head['scripts'][]=array('src'=>"https://www.gstatic.com/charts/loader.js");
        // Order popup
        $head['styles'][]=array('style'=>'/css/leadorder/popup.css');
        $head['scripts'][]=array('src'=>'/js/leads/leadorderpopup.js');
        // Uploader
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        // File Download
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
        // Datepicker
        // $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        // $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Select 2
        $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
        $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];
        $head['styles'][]=['style' => '/css/mb_ballons/mb.balloon.css'];
        $head['scripts'][] = ['src' => '/js/mb_balloons/jquery.mb.balloon.js'];
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
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    private function _prepare_purchaseorders_view($brand, $top_menu) {
        $inner = 0;
        $this->load->model('orders_model');
        $this->load->model('payments_model');

        $totaltab = $this->orders_model->purchaseorder_totals($inner, $brand);
        $totals = $this->orders_model->purchase_fulltotals($brand);
        // Years
        $years = $this->payments_model->get_pototals_years($brand);
        $year1 = $year2 = $year3 = $years[0];
        if (count($years) > 1) {
            $year2 = $years[1];
        }
        if (count($years) > 2) {
            $year3 = $years[2];
        }
        // Temporary
        $year1=2018; $year2=2017; $year3=2016;
        $poreptotals = $this->payments_model->get_poreport_totals($year1, $year2, $year3, $brand);
        $options=[
            'totaltab' => $totaltab,
            'totals' => $totals,
            'inner' => $inner,
            'brand' => $brand,
            'top_menu' => $top_menu,
            'years' => $years,
            'year1' => $year1,
            'year2' => $year2,
            'year3' => $year3,
            'poreporttotals' => $poreptotals,
            'poreportperpage' => 8,
        ];
        return $this->load->view('pototals/page_adaptive_view',$options,TRUE);
    }

    private function _prepare_accreceiv_view($brand, $top_menu) {
        $options=[
            'brand' => $brand,
            'top_menu' => $top_menu,
        ];
        return $this->load->view('accreceiv/page_device_view',$options, TRUE);
    }

}