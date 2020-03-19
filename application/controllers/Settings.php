<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller
{

    private $pagelink = '/settings';

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

    public function index()
    {
        $head = [];
        $head['title'] = 'Settings';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [
            'menu' => $menu,
        ];
        $brands = $this->menuitems_model->get_brand_permisions($this->USR_ID, $this->pagelink);
        if (count($brands)==0) {
            redirect('/');
        }
        $brand = $brands[0]['brand'];
        $left_options = [
            'brands' => $brands,
            'active' => $brand,
        ];
        $left_menu = $this->load->view('page/left_menu_view', $left_options, TRUE);

        foreach ($menu as $row) {
            if ($row['item_link'] == '#shippingview') {
                $head['styles'][] = array('style' => '/css/settings/shippings.css');
                $head['scripts'][] = array('src' => '/js/settings/shippings.js');
                $content_options['shippingview'] = $this->_prepare_shipping_view($brand, $left_menu);
            }
        }

        $content_view = $this->load->view('settings/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/settings/page.js');
        $head['styles'][] = array('style' => '/css/settings/settingpage.css');
        // Utils
        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function shippingdata() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            $mdata=array();
            $error='Empty Brand';
            if (!empty($brand)) {
                $error = '';
                $this->load->model('shipping_model');
                $zones = $this->shipping_model->get_shipzones(['brand'=>$brand]);
                $cont_arr=array();
                foreach ($zones as $row) {
                    $details=$this->shipping_model->get_shipzone_details($row['zone_id']);
                    $content=$this->load->view('settings/shipzone_method_view',array('zone'=>$row['zone_name'],'shipdat'=>$details),TRUE);
                    $cont_arr[]=$content;
                }
                $mdata['content']=$this->load->view('settings/shippings_data_view',array('zones'=>$cont_arr),TRUE);
                /* shipping calend */
                $month=ifset($postdata, 'month', date('m'));
                $year=ifset($postdata, 'year', date('Y'));
                $days=$this->shipping_model->shipcalc_report($month, $year, $brand);
                $data=array(
                    'days'=>$days,
                    'totaldays'=>count($days),
                );
                $mdata['report']=$this->load->view('settings/shiping_report_view',$data, TRUE);

            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function savezones() {
        if ($this->isAjax()) {
            $this->load->model('shipping_model');
            $details=$this->input->post();
            $mdata=array();
            $error='Empty Brand';
            $brand = ifset($details, 'brand');
            if (!empty($brand)) {
                $zones = $this->shipping_model->get_zones(['brand'=>$brand]);
                /* Get list of Shipping methods for all zones */
                $avail_meth=$this->shipping_model->get_shipmethodlist();
                $methods_data=array();
                foreach ($avail_meth as $row) {
                    $kf=0;
                    $dim=0;
                    if (isset($details['percent'.$row['zonemethod_id']])) {
                        $kf=floatval($details['percent'.$row['zonemethod_id']]);
                    }
                    if (isset($details['dimens'.$row['zonemethod_id']])) {
                        $dim=1;
                    }
                    $methods_data[]=array(
                        'zonemethod_id'=>$row['zonemethod_id'],
                        'method_percent'=>$kf,
                        'method_dimens'=>$dim,
                    );
                }
                $this->shipping_model->save_zones_methods_dat($methods_data);
                $error = '';
            }

            $this->ajaxResponse($mdata,$error);
        }
    }

    public function shipcalclog() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $month=$this->input->post('month');
            $year=$this->input->post('year');
            $brand = $this->input->post('brand');
            $this->load->model('shipping_model');
            $days=$this->shipping_model->shipcalc_report($month, $year, $brand);
            $data=array(
                'days'=>$days,
                'totaldays'=>count($days),
            );
            $mdata['content']=$this->load->view('settings/shiping_report_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function schipcalend_details() {
        $start=$this->input->get('d');
        $end=  strtotime(date('Y-m-d',$start).' 23:59:59');
        $brand = $this->input->get('brand');
        $this->load->model('shipping_model');
        $data=$this->shipping_model->get_shipcalcdayreport($start, $end, $brand);
        $content=$this->load->view('settings/shiping_details_view',array('data'=>$data), TRUE);
        echo $content;
    }

    private function _prepare_shipping_view($brand, $left_menu) {
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
            'left_menu' => $left_menu,
        ];
        $content=$this->load->view('settings/shippings_view',$options,TRUE);
        return $content;

    }

}