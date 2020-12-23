<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends MY_Controller
{

    private $pagelink = '/settings';
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

    public function index()
    {
        $head = [];
        $head['title'] = 'Settings';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [
            'menu' => $menu,
            'start' => $this->input->get('start', TRUE),
        ];
//        $brands = $this->menuitems_model->get_brand_permisions($this->USR_ID, $this->pagelink);
//        if (count($brands)==0) {
//            redirect('/');
//        }
//        $brand = $brands[0]['brand'];
//        $left_options = [
//            'brands' => $brands,
//            'active' => $brand,
//        ];
//        $left_menu = $this->load->view('page/left_menu_view', $left_options, TRUE);

        foreach ($menu as $row) {
            if ($row['item_link']=='#countriesview') {
                $head['styles'][] = array('style' => '/css/settings/countriesview.css');
                $head['scripts'][] = array('src' => '/js/settings/countriesview.js');
                $content_options['countriesview'] = $this->_prepare_countries_view();
            } elseif ($row['item_link'] == '#calendarsview') {
                $head['styles'][] = array('style' => '/css/settings/calendars.css');
                $head['scripts'][] = array('src' => '/js/settings/calendars.js');
                $content_options['calendarsview'] = $this->_prepare_calendars_view();
            } elseif ($row['item_link'] =='#btsettingsview' ) {
                $bt_options = [];
                $submenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $row['item_link']);
                foreach ($submenu as $menu) {
                    if ($menu['item_link']=='#btshippingview') {
                        $bt_options['btshippingview'] = $this->_prepare_shipping_view('BT');
                    } elseif ($menu['item_link'] == '#btnotificationsview') {
                        $bt_options['btnotificationsview'] = $this->_prepare_notifications_view('BT');
                    } elseif ($menu['item_link'] == '#btrushoptionsview') {
                        $bt_options['btrushoptionsview'] = $this->_prepare_rushoptions_view('BT');
                    }
                    $submenu_options = [
                        'menus' => $submenu,
                        'brand' => 'BT',
                    ];
                }
                $bt_options['submenu'] = $this->load->view('settings/submenu_view', $submenu_options, TRUE);
                $content_options['btsettingsview'] = $this->load->view('settings/page_content_view', $bt_options, TRUE);
            } elseif ($row['item_link']=='#sbsettingsview') {
                $sb_options = [];
                $submenu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $row['item_link']);
                foreach ($submenu as $menu) {
                    if ($menu['item_link']=='#sbshippingview') {
                        $sb_options['sbshippingview'] = $this->_prepare_shipping_view('SB');
                    } elseif ($menu['item_link'] == '#sbnotificationsview') {
                        $sb_options['sbnotificationsview'] = $this->_prepare_notifications_view('SB');
                    } elseif ($menu['item_link'] == '#sbrushoptionsview') {
                        $sb_options['sbrushoptionsview'] = $this->_prepare_rushoptions_view('SB');
                    }
                    $submenu_options = [
                        'menus' => $submenu,
                        'brand' => 'SB',
                    ];
                }
                $sb_options['submenu'] = $this->load->view('settings/submenu_view', $submenu_options, TRUE);
                $content_options['sbsettingsview'] = $this->load->view('settings/page_content_view', $sb_options, TRUE);
            }
        }
        $head['styles'][] = array('style' => '/css/settings/shippings.css');
        $head['scripts'][] = array('src' => '/js/settings/shippings.js');
        $head['styles'][] = array('style' => '/css/settings/notificationsview.css');
        $head['scripts'][] = array('src' => '/js/settings/notificationsview.js');
        $head['styles'][] = array('style' => '/css/settings/rushoptionsview.css');
        $head['scripts'][] = array('src' => '/js/settings/rushoptionsview.js');
        $head['scripts'][] = array('src' => '/js/settings/sitesettings.js');
        $content_view = $this->load->view('settings/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/settings/page.js');
        $head['styles'][] = array('style' => '/css/settings/settingpage.css');
        // Utils
        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

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
                $zones = $this->shipping_model->get_shipzones();
                $cont_arr=array();
                foreach ($zones as $row) {
                    $details=$this->shipping_model->get_shipzone_details($row['zone_id'], $brand);
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

    // Calendars
    public function calenddata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            // $searchval=$this->input->post('search','');
            $brand = $this->input->post('brand');
            $this->load->model('calendars_model');
            $calends=$this->calendars_model->get_calendars_list($brand);
            $mdata['content']=$this->load->view('settings/calendars_tabledat_view',array('calendars'=>$calends,),TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function calendar_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $calend_id=$this->input->post('calend_id');
            $this->load->model('calendars_model');
            $calenddat=$this->calendars_model->get_calendar_edit($calend_id);
            $error=$calenddat['msg'];
            if ($calenddat['result']==$this->success_result) {
                $error = '';
                $calend=$calenddat['data'];
                $mdata['title']='Edit Business Calendar '.$calend['calendar_name'];
                $calend_lines=$this->calendars_model->get_calendar_lines($calend_id);
                $session_id = 'calend'.uniq_link('14');
                $session_data = [
                    'calend' => $calend,
                    'calend_lines' => $calend_lines,
                    'deleted' => [],
                ];
                usersession($session_id, $session_data);
                $holidays=$this->load->view('settings/holidaylist_view',array('calendar_lines'=>$calend_lines),TRUE);

                $elaps_proc=round($calenddat['elaps_days']/$calenddat['total_days']*100,1);
                $remin_proc=100-$elaps_proc;
                $date=array(
                    'session_id' => $session_id,
                    'calendar'=>$calend,
                    'holidaylist'=>$holidays,
                    'total_days'=>$calenddat['total_days'],
                    'elaps_days'=>$calenddat['elaps_days'],
                    'elaps_proc'=>$elaps_proc.'%',
                    'remin_days'=>$calenddat['remin_days'],
                    'remin_proc'=>$remin_proc.'%',
                    'year'=>date('Y'),
                );
                $mdata['content']=$this->load->view('settings/calendarform_view',$date,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function calendar_addholliday() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata =[];
            $error = $this->session_error;
            $session_id = ifset($postdata, 'session','defsession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $error = 'Empty New Date';
                $newdate = ifset($postdata,'newdate','');
                if (!empty($newdate)) {
                    // $holiday = $newdate/1000;
                    // $holiday = date('m/d/Y',strtotime($newdate));
                    $holiday = strtotime($newdate);
                    $this->load->model('calendars_model');
                    $res = $this->calendars_model->add_holiday($session_data, $holiday, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $calend_lines = $res['calend_lines'];
                        $mdata['content']=$this->load->view('settings/holidaylist_view',array('calendar_lines'=>$calend_lines),TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function calendar_delline() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata =[];
            $error = $this->session_error;
            $session_id = ifset($postdata, 'session','defsession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $error = 'Empty Date';
                $line = ifset($postdata,'line','');
                if (!empty($line)) {
                    $this->load->model('calendars_model');
                    $res = $this->calendars_model->delete_calendline($session_data, $line, $session_id);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                        $calend_lines = $res['calend_lines'];
                        $mdata['content']=$this->load->view('settings/holidaylist_view',array('calendar_lines'=>$calend_lines),TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);

        }
        show_404();
    }

    public function calendar_save() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata =[];
            $error = $this->session_error;
            $session_id = ifset($postdata, 'session','defsession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('calendars_model');
                $res = $this->calendars_model->save_calendar($session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_calendars_view() {
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

        $content=$this->load->view('settings/calendars_view',$options,TRUE);
        return $content;

    }
    // Notifications
    public function emailnotificationdat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            /* 'offset':page_index,'limit':perpage,'order_by':order_by,'direction':direction,'maxval':maxval */
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','desc');
            $maxval=$this->input->post('maxval');
            $brand = $this->input->post('brand');
            $ordoffset=$offset*$limit;
            $offset=$offset*$limit;

            /* Fetch data about prices */
            $this->load->model('email_model');
            $email_dat=$this->email_model->get_notification_emails($order_by,$direct,$limit,$offset, $brand);

            /* Get data about Competitor prices */
            if ($ordoffset>$maxval) {
                $ordnum = $maxval;
            } else {
                $ordnum = $maxval - $ordoffset;
            }
            $data=array('emails'=>$email_dat,'offset'=>$offset,'ordnum'=>$ordnum);
            $mdata['content'] = $this->load->view('settings/notification_tabledat_view',$data, TRUE);

            $this->ajaxResponse($mdata,$error);
        }
    }

    public function notification() {
        if ($this->isAjax()) {
            $this->load->model('email_model');
            $mdata=array();
            $error='';
            $notification_id=$this->input->post('notification_id',0);
            if ($notification_id==0) {
                $data=array(
                    'notification_id'=>0,
                    'notification_system'=>'',
                    'notification_email'=>'',
                );
                $mdata['title']='Add Notification';
            } else {
                $data=$this->email_model->get_notification($notification_id);
            }
            if (!isset($data['notification_id'])) {
                $error='Unknown notification record';
            } else {
                $data['notification_systems']=$this->config->item('notification_systems');
                $mdata['content']=$this->load->view('settings/notificationform_view',$data,TRUE);
                $mdata['title']='Edit Notification';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function save_notification() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('email_model');
            $notification_id=$this->input->post('notification_id',0);
            $notification_system=$this->input->post('notification_system','');
            $notification_email=$this->input->post('notification_email','');
            $brand = $this->input->post('brand');
            $res=$this->email_model->savenotification($notification_id, $notification_system, $notification_email, $brand);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function deletenotification() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('email_model');
            $notification_id=$this->input->post('notification_id');
            $brand = $this->input->post('brand');
            $res=$this->email_model->delete_notification($notification_id);
            if ($res==0) {
                $error='Can\'t delete notification. Please try again';
            } else {
                $options = ['brand'=>$brand];
                $mdata['totals']=$this->email_model->get_count_notifications($options);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_notifications_view($brand) {
        $this->load->model('email_model');
        $totals=$this->email_model->get_count_notifications();
        // $survay_data=$this->memails->get_surveydata();
        $options=array(
            'total'=>$totals,
            'order_by'=>'notification_system',
            'direction'=>'asc',
            'cur_page'=>0,
            'brand' => $brand,
        );
        return $this->load->view('settings/notifications_view',$options,TRUE);
    }
    // Rush options
    public function settingsdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('staticpages_model');
            $configs=$this->staticpages_model->get_configs();
            $mdata['content']=$this->load->view('settings/configs_view',array('configs'=>$configs),TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function edit_config() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $config_id=$this->input->post('config_id');
            $this->load->model('staticpages_model');
            $conf=$this->staticpages_model->get_config_data($config_id);
            if (!isset($conf['config_id'])) {
                $error='Unknown Config parameter';
            } else {
                $mdata['content']=$this->load->view('settings/configedit_view',$conf,TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    public function saveconfig() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('staticpages_model');
            $config_alias=$this->input->post('config_alias');
            $config_value=$this->input->post('config_value');
            $config_id=$this->input->post('config_id');
            if (empty($config_alias) || empty($config_value)) {
                $error='Enter config data';
            } else {
                $res=$this->staticpages_model->save_config($config_id,$config_alias,$config_value);
                if (!$res['result']) {
                    $error=$res['msg'];
                } else {
                    $configs=$this->staticpages_model->get_configs();
                    $mdata['content']=$this->load->view('settings/configs_view',array('configs'=>$configs),TRUE);
                }
            }
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }

    private function _prepare_rushoptions_view($brand) {
        $options = [
            'brand' => $brand,
        ];
        return $this->load->view('settings/rushoptions_view', $options, TRUE);
    }
    // Countries
    public function countries_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('shipping_model');
            $template=$this->input->post('search_template');
            $sort=$this->input->post('sort');
            $direc=$this->input->post('direc');
            $filtr=array();
            if ($template!='') {
                $filtr['search_template']=$template;
            }
            $zones=$this->shipping_model->get_zones();
            $data=$this->shipping_model->get_countries_data($filtr, $sort, $direc);
            $mdata['content']=$this->load->view('settings/countries_data_view',array('data'=>$data, 'zones'=>$zones),TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function countries_shipallow() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('shipping_model');
            $country_id=$this->input->post('country_id');
            $options=array();
            $options['shipallow']=$this->input->post('shipallow');
            $res=$this->shipping_model->update_countries($country_id,$options);
            $this->ajaxResponse($mdata,$error);
        }
    }

    private function _prepare_countries_view() {
        $this->load->model('shipping_model');
        $search_templates=$this->shipping_model->get_country_search_templates();
        $options = [
            // 'brand' => $brand,
            // 'left_menu' => $left_menu,
            'search_templ' => $search_templates,
        ];
        $content=$this->load->view('settings/countries_view', $options,TRUE);
        return $content;
    }
}