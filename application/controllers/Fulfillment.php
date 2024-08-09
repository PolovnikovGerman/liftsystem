<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fulfillment extends MY_Controller
{

    private $pagelink = '/fulfillment';

    private $container_with=60;
    private $maxlength=240;
    private $needlistlength=180;
    private $salesreplength=300;
    protected $restore_invdata_error='Connection Lost. Please, recall function';

    private $empty_html_content='&nbsp;';
    private $container_type = 'C';
    private $express_type = 'E';
    private $mimetypes = [
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'gif' => 'image/gif',
        'eps' => 'image/x-eps', //  'application/postscript',
        'ai' => 'application/pdf', // 'application/postscript',
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
    ];

    public function __construct()
    {
        parent::__construct();
        $brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink,0, $brand);
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
        $head['title'] = 'Fulfillment';
        $brand = $this->menuitems_model->get_current_brand();
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);

        $content_options = [];
        $content_options['start'] = $this->input->get('start', TRUE);
        foreach ($menu as $row) {
            if ($row['item_link']=='#vendorsview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/vendorsview.css');
                $head['scripts'][]=array('src'=>'/js/fulfillment/vendorsview.js');
                $content_options['vendorsview'] = $this->_prepare_vendors_view();
            } elseif ($row['item_link']=='#fullfilstatusview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/postatus.css');
                $head['scripts'][]=array('src'=>'/js/fulfillment/postatus.js');
                $content_options['fullfilstatusview'] = $this->_prepare_status_view($brand);
            } elseif ($row['item_link']=='#pototalsview') {
                $head['styles'][]=array('style'=>'/css/accounting/pototals.css');
                $head['scripts'][]=array('src'=>'/js/accounting/pototals.js');
                $content_options['pototalsview'] = $this->_prepare_purchaseorders_view($brand);
            } elseif ($row['item_link']=='#printshopinventview') {
                // $head['styles'][]=array('style'=>'/css/fulfillment/inventory.css');
                // $head['scripts'][]=array('src'=>'/js/fulfillment/inventory.js');
                // $content_options['printshopinventview'] = $this->_prepare_printshop_inventory($brand);
                $head['styles'][]=array('style'=>'/css/database_center/master_inventory.css');
                $head['scripts'][]=array('src'=>'/js/database_center/master_inventory.js');
                $content_options['printshopinventview'] = $this->_prepare_inventory_view();
            } elseif ($row['item_link']=='#invneedlistview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/invneedlistview.css');
                $head['scripts'][] = array('src'=>'/js/fulfillment/invneedlistview.js');
                $content_options['invneedlistview'] = $this->_prepare_needlist_view($brand);
            } elseif ($row['item_link']=='#salesrepinventview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/salesrepinventview.css');
                $head['scripts'][] = array('src'=>'/js/fulfillment/salesrepinventview.js');
                $content_options['salesrepinventview'] = $this->_prepare_inventsalesrep_view($brand);
            } elseif ($row['item_link']=='#printshopreportview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/printshopreportview.css');
                $head['scripts'][] = array('src'=>'/js/fulfillment/printshopreportview.js');
                $content_options['printshopreportview'] = $this->_prepare_printshop_report($brand);
            } elseif ($row['item_link']=='#printscheduleview') {
                $head['styles'][]=array('style'=>'/css/fulfillment/printscheduler.css');
                $head['scripts'][] = array('src'=>'/js/fulfillment/printscheduler.js');
                $content_options['printschedulerview'] = $this->_prepare_printscheduler_view($brand);
            }
        }
        $content_options['menu'] = $menu;
        // Add main page management
        $head['scripts'][] = array('src' => '/js/fulfillment/page.js');
        $head['styles'][] = array('style' => '/css/fulfillment/fulfilmpage.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        // DatePicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Scroll panel
        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'brand' => $brand,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $content_view = $this->load->view('fulfillment/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
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
                    $res = $this->vendors_model->add_vendor();
                    $mdata['title'] = 'New Vendor';
                    $data = $res['vendor'];
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

    // STATUS page
    // Status data
    public function statusdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $pagenum=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction','asc');
            $search=$this->input->post('search');
            $date_filter=$this->input->post('date_filter');
            $options_filter=$this->input->post('options_filter');
            $addsort=$this->input->post('addsort');
            $brand = $this->input->post('brand');
            $offset=$pagenum*$limit;

            /* Get data about orders */
            $options=array(
                'profit_perc'=>NULL,
                'is_canceled'=>0,
                'status_type'=>'O',
                'brand' => $brand,
            );
            if ($date_filter==1) {
                $dat=strtotime(date('m/d/Y',time())." -6 months");
                $options['min_time']=$dat;
            }
            if ($options_filter!='') {
                $options['order_status']=$options_filter;
            }
            if ($search!='') {
                $options['search']=$search;
            }
            $this->load->model('orders_model');
            $orders=$this->orders_model->get_orderslimits($options,$order_by,$addsort,$direct,$limit,$offset);
            if (count($orders)>0) {
                $mdata['content']=$this->load->view('fulfillment/status_data_view',array('orders'=>$orders),TRUE);
            } else {
                $mdata['content']=$this->load->view('fulfillment/status_emptydata_view',array('orders'=>$orders),TRUE);
            }

            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Count # of records for new status of filter */
    function statussearchdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            /* Post params */
            $search=$this->input->post('search');
            $date_filter=$this->input->post('date_filter');
            $options_filter=$this->input->post('options_filter');
            $options=array(
                'profit_perc'=>NULL,
                'is_canceled'=>0,
            );
            if ($date_filter==1) {
                $dat=strtotime(date('m/d/Y',time())." -6 months");
                $options['min_time']=$dat;
            }
            if ($options_filter!='') {
                $options['order_status']=$options_filter;
            }
            if ($search!='') {
                $options['search']=$search;
            }
            /* Get New Total recors */
            $this->load->model('orders_model');
            $mdata['totalrec']=$this->orders_model->get_count_orderslimits($options);

            $this->ajaxResponse($mdata, $error);
        }
    }

    // Inventory
    // Change Brand
    public function inventory_brand() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $salesreport = ifset($postdata,'salesreport',0);
                $this->load->model('printshop_model');
                if ($salesreport==0) {
                    $addcost=$this->printshop_model->invaddcost();
                    $totalinv=$this->printshop_model->get_inventory_totals($brand);
                    $mdata['maxsum'] = MoneyOutput($totalinv['maxsum']);
                    $mdata['invetorytotal'] = $this->load->view('printshopinventory/total_inventory_view',$totalinv,TRUE);
                    $data = $this->printshop_model->get_data_onboat($brand);
                    $boathead_view='';
                    foreach ($data as $drow) {
                        $boathead_view.=$this->load->view('printshopinventory/onboat_containerhead_view', $drow, TRUE);
                    }
                    // Build head content
                    $slider_width=60*count($data);
                    $margin = $this->maxlength-$slider_width;
                    $margin=($margin>0 ? 0 : $margin);
                    $boatoptions=array(
                        'data'=>$data,
                        'container_view'=>$boathead_view,
                        'width' => $slider_width,
                        'margin' => $margin,
                    );
                    $mdata['onboathead'] = $this->load->view('printshopinventory/onboathead_view', $boatoptions, TRUE);
                    $mdata['width']=$slider_width.'px';
                    $mdata['margin'] = $margin.'px';
                    $mdata['download_view'] =$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
                } else {
                    $totalinv=$this->printshop_model->get_inventory_totals($brand);
                    $mdata['totalinvview']=$this->load->view('invsalesrep/total_inventory_view',$totalinv,TRUE);
                    $data = $this->printshop_model->get_data_onboat($brand);
                    $boathead_view='';
                    foreach ($data as $drow) {
                        $boathead_view.=$this->load->view('invsalesrep/onboat_containerhead_view', $drow, TRUE);
                    }
                    // Build head content
                    $slider_width=60*count($data);
                    $margin = $this->salesreplength-$slider_width;
                    $margin=($margin>0 ? 0 : $margin);

                    $boatoptions=array(
                        'data'=>$data,
                        'container_view'=>$boathead_view,
                        'width' => $slider_width,
                        'margin' => $margin,
                    );
                    $mdata['onboat_content']=$this->load->view('invsalesrep/onboathead_view', $boatoptions, TRUE);

                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Inventory Data
    public function inventory_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $postdata=$this->input->post();
            // $brand = ifset($postdata,'brand');
            $brand = 'ALL';

            $options=array(
                'orderby'=>'item_num',
                'direct'=>'asc',
                'brand' => $brand,
            );

            $data=$this->printshop_model->get_printshopitems($options);

            $permission=$this->user_model->get_user_data($this->USR_ID);
            // Make Total Inv content
            $totaloptions=array(
                'data'=>$data['inventory'],
            );
            if (isset($postdata['salesreport']) && $postdata['salesreport']==1) {
                $mdata['totalinvcontent']=$this->load->view('invsalesrep/totalinventory_data_view', $totaloptions, TRUE);
                $specopt=array(
                    'data'=>$data['inventory'],
                    'permission'=>$permission['profit_view'],
                );

                $mdata['speccontent']=$this->load->view('invsalesrep/specinventory_data_view', $specopt, TRUE);
            } else {
                $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
                $specopt=array(
                    'data'=>$data['inventory'],
                    'permission'=>$permission['profit_view'],
                );

                $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
            }
            $mdata['total_inventory']=MoneyOutput($data['inventtotal']);
            // Get OnBoat Data
            $colors=$data['colors'];
            $boatdata = $this->printshop_model->get_data_onboat($brand);
            $containers_view='';
            foreach ($boatdata as $drow) {
                $boatcontndata=$this->printshop_model->get_container_view($drow['onboat_container'], $colors);
                $boptions=array(
                    'data'=>$boatcontndata,
                    'onboat_container'=>$drow['onboat_container'],
                    'onboat_status'=>$drow['onboat_status'],
                );
                $containers_view.=$this->load->view('printshopinventory/container_data_view', $boptions, TRUE);
            }
            // foreach ($data)
            $slider_width=60*(count($boatdata));
            if (isset($postdata['salesreport']) && $postdata['salesreport']==1) {
                $margin=$this->salesreplength-$slider_width;
            } else {
                $margin=$this->maxlength-$slider_width;
            }
            $boatoptions=array(
                'width'=>$slider_width,
                'margin'=>($margin>0 ? 0 : $margin),
                'boatcontent'=>$containers_view,
            );
            $mdata['onboatcontent']=$this->load->view('printshopinventory/onboatdata_view', $boatoptions, TRUE);
            // $mdata['width'] = $slider_width-240;
            $mdata['margin']=$margin;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change cost
    public function inventory_addcost() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $this->load->model('printshop_model');
            $postdata = $this->input->post();
            $addcost=ifset($postdata,'cost',0);
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $error = '';
                $this->load->model('printshop_model');
                $res=$this->printshop_model->inventory_addcost_upd($addcost);
                $options=array(
                    'orderby'=>'item_num',
                    'direct'=>'asc',
                    'brand' => $brand,
                );
                $data=$this->printshop_model->get_printshopitems($options);
                $permission=$this->user_model->get_user_data($this->USR_ID);
                // Make Total Inv content
                $mdata['total_inventory']=MoneyOutput($data['inventtotal']);
                $specopt=array(
                    'data'=>$data['inventory'],
                    'permission'=>$permission['profit_view'],
                );
                $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Item Data change
    public function inventory_item() {
        if ($this->isAjax()) {
            $this->load->model('printshop_model');
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $printshop_item_id=$postdata['printshop_item_id'];
            if ($printshop_item_id==0) {
                $item=$this->printshop_model->new_invent_item();
            } else {
                $res=$this->printshop_model->get_invent_item($printshop_item_id);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                }
                $item=$res['item'];
            }
            if (empty($error)) {
                $permission=$this->user_model->get_user_data($this->USR_ID);
                // Make Total Inv content
                $item['permission']=$permission['profit_view'];
                usersession('invitemdata', $item);
                $mdata['content']=$this->load->view('printshopinventory/invitem_itemdata_view', $item, TRUE);
                $mdata['addcontent']=$this->load->view('printshopinventory/invitem_itemaddons_view', $item, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change Item Detail Value
    public function inventory_item_change() {
        if ($this->isAjax()) {
            $mdata = array();
            $error='Edit time expired. Recconect';
            $item=usersession('invitemdata');
            if (!empty($item)) {
                $this->load->model('printshop_model');
                $postdata = $this->input->post();
                $res=$this->printshop_model->invitem_item_change($item, $postdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Load data
    public function inventory_loaddata() {
        if ($this->isAjax()) {
            $error=$this->restore_invdata_error;
            $mdata = [];
            $newss = usersession('invitemdata');
            if (!empty($newss)) {
                $plate_temp=$this->input->post('plate_temp');
                $proof_temp=$this->input->post('proof_temp');
                $item_label=$this->input->post('item_label');
                $error='Unknown Upload Parameter';
                if (!empty($proof_temp)) {
                    // $out=$this->printshop_model->get_invent_item($proof_temp);
                    $error='';
                    $id = $proof_temp;
                    $filesource = $newss['proof_temp'];
                    $filename=$newss['proof_temp_source'];
                    $uploadsess='prooftempupload';
                    $title='Upload Proof temp';
                    $uploadtype='proof_temp';
                } elseif (!empty($plate_temp)) {
                    $error='';
                    $id=$plate_temp;
                    $filesource = $newss['plate_temp'];
                    $filename=$newss['plate_temp_source'];
                    $uploadsess='platetempupload';
                    $title='Upload Plate temp';
                    $uploadtype='plate_temp';
                } elseif (!empty($item_label)) {
                    $error='';
                    $id=$item_label;
                    $filesource = $newss['item_label'];
                    $filename=$newss['item_label_source'];
                    $uploadsess='itemlabelupload';
                    $title='Upload Item Label';
                    $uploadtype='item_label';
                }
            }
            if ($error=='') {
                $viewParams=array(
                    'filename'=> $filename,
                    'filesource'=>$filesource,
                    'uplsess' => $uploadsess,
                    'uploadtype'=>$uploadtype,
                );
                usersession($uploadsess, $newss);
                $mdata['title'] = $title;
                $mdata['content'] = $this->load->view('printshopinventory/plate_temp_view',$viewParams,TRUE);
                $mdata['filename'] = $filename;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function inventory_platetempattach() {
        $this->load->helper('upload');
        $postdata=$this->input->get();
        $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');

        $response = array('success' => false, 'msg'=>'Empty Upload Data');
        $data=usersession($uploadsession);

        if (!empty($data)) {
            $response['error']= 'server-error file not passed';
            $path = $this->config->item('upload_path_preload');
            // Allowed Extensions
            $uploadtype=$postdata['uploadtype'];
            if ($uploadtype=='item_label') {
                $arrayext=array('jpg','JPG','jpeg','JPEG');
            } else {
                $arrayext=array('ai','AI', 'pdf', 'PDF');
            }

            if (isset($_GET['qqfile'])) {
                $file = new qqUploadedFileXhr();
            } elseif (isset($_FILES['qqfile'])) {
                $file = new qqUploadedFileForm();
            } elseif (isset($_POST['qqfile'])) {
                $file = new qqUploadedFileXhr();
            }

            if ($file) {
                $response['error']= 'server-error file size is zero';
                $filename = $file->getName();
                $filesize = $file->getSize();

                if ($filesize > 0) {
                    $these = implode(', ', $arrayext);
                    $response['error']= 'File has an invalid extension, it should be one of '. $these . '.';
                    $pathinfo = pathinfo($filename);
                    $newfilename=uniq_link(12);
                    $ext = strtolower($pathinfo['extension']);

                    if (in_array($ext, $arrayext )) {
                        $filesource = $path . $newfilename . '.' . $ext;
                        $ressave = $file->save($filesource);
                        if ($ressave) {
                            $mimeext = $this->mimetypes[$ext];
                            $mimetype = mime_content_type($filesource);
                            if ($mimetype==$mimeext) {
                                $data['filesource'] = $filesource;
                                $data['filename'] = $filename;
                                usersession($uploadsession, $data);
                                $response['success'] = true;
                                $response['error'] = '';
                            } else {
                                $response['error'] = 'Error During save File';
                                @unlink($filesource);
                                // Insert data into log
                                $this->db->set('file_name', $file->getName());
                                $this->db->set('file_ext', $mimetype);
                                if (isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
                                    $this->db->set('page_call',$_SERVER['HTTP_REFERER']);
                                }
                                if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                                    $this->db->set('site', $_SERVER['HTTP_HOST']);
                                }
                                $this->db->set('user_ip', $this->input->ip_address());
                                $this->db->set('user_id', $this->USR_ID);
                                $this->db->insert('ts_uploadfile_logs');
                            }
                        }
                    }
                }
            }
        }
        echo (json_encode($response));
        exit();
    }

    public function inventory_deluplplatetempdocs() {
        if ($this->isAjax()) {
            $error='Session Expired. Please, recall form';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);

            if (!empty($data)) {
                $data['filesource'] = NULL;
                $data['filename'] = NULL;
                usersession($uploadsession, $data);
                $error ="";
            }
            $this->ajaxResponse(array('filename'=>$data['filesource']), $error);
        }
    }

    public function save_platetempload() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_invdata_error;

            // Restore from session
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);
            $ss = usersession('invitemdata');

            if (!empty($data) || !empty($ss)) {
                $this->load->model('printshop_model');
                $data['uploadtype']=$postdata['uploadtype'];
                $cut = $this->printshop_model->cut_link($data);
                if ($postdata['uploadtype']=='item_label') {
                    $ss['item_label'] = $cut['filesource'];
                    $ss['item_label_source'] = $cut['filename'];
                } elseif ($postdata['uploadtype']=='proof_temp') {
                    $ss['proof_temp'] = $cut['filesource'];
                    $ss['proof_temp_source'] = $cut['filename'];
                } else {
                    $ss['plate_temp'] = $cut['filesource'];
                    $ss['plate_temp_source'] = $cut['filename'];
                }
                usersession($uploadsession,NULL);
                usersession('invitemdata', $ss);
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Save Inventory item
    public function inventory_item_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $item=usersession('invitemdata');
                $error='Edit time expired. Reload';
                if (!empty($item)) {
                    $item_id=$item['printshop_item_id'];
                    $this->load->model('printshop_model');
                    $res=$this->printshop_model->invitem_item_save($item);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        if ($item_id>0) {
                            $options=array(
                                'orderby'=>'item_num',
                                'direct'=>'asc',
                                // 'brand' => $brand,
                                'brand' => 'ALL',
                            );

                            $data=$this->printshop_model->get_printshopitems($options);
                            $permission=$this->user_model->get_user_data($this->USR_ID);
                            // Make Total Inv content
                            $totaloptions=array(
                                'data'=>$data['inventory'],
                            );
                            $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
                            $specopt=array(
                                'data'=>$data['inventory'],
                                'permission'=>$permission['profit_view'],
                            );
                            $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
                            $mdata['newitem']=0;
                        } else {
                            $mdata['newitem']=1;
                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Add Color for Inventory Item
    public function inventory_color() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $postdata=$this->input->post();
            $printshop_item_id=$postdata['printshop_item_id'];
            $printshop_color_id=$postdata['printshop_color_id'];
            $showmax=$postdata['showmax'];
            $brand = ifset($postdata, 'brand', 'ALL');
            if ($printshop_color_id==0) {
                $color=$this->printshop_model->invitem_newcolor($printshop_item_id);
                $colors=$this->printshop_model->get_item_colors($printshop_item_id);
                $color['numcolors']=count($colors)+1;
                $color["printshop_pics"] = array();
            } else {
                $res=$this->printshop_model->invitem_colordata($printshop_color_id, $brand);
                // Get a number of colors
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                    $this->ajaxResponse($mdata, $error);
                } else {
                    $color=$res['data'];
                    $color["printshop_pics"] = $this->printshop_model->get_picsattachments($printshop_color_id);
                }
            }
            $uploadsess='picsupload'.uniq_link(15);

            $color["uplsess"] = $uploadsess;
            $color['showmax']=$showmax;
            usersession($uploadsess, $color);
            // Devide form on 2 parts
            $mdata['commoncontent']=$this->load->view('printshopinventory/invitem_colordata_view', $color, TRUE);
            // Permissions
            $permission=$this->user_model->get_user_data($this->USR_ID);
            $color['permission']=$permission['profit_view'];
            $mdata['addcontent']=$this->load->view('printshopinventory/invitem_coloradddata_view', $color, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change Item Color Value
    public function inventory_color_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Edut time expired. Reconnect';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $color=usersession($uploadsession);
            if (!empty($color)) {
                $this->load->model('printshop_model');
                $postdata=$this->input->post();
                $res=$this->printshop_model->invitem_color_change($color, $postdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $avail=$res['availabled'];
                    $mdata['availabled']='';
                    if ($avail!=0) {
                        $mdata['availabled']=  number_format($avail,0,'.',',');
                    }
                    $mdata['notreorder']=$res['notreorder'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Pics download - prepare
    public function inventory_pics() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $uploadsess=$this->input->post('uplsess');
            $data = usersession($uploadsess);
            $data["state_".$uploadsess] = $data;
            usersession($uploadsess, $data);
            $data["html"] = "";
            $mdata["numrec"] = 0;
            if (isset($data['printshop_pics']) && count($data['printshop_pics']) > 0) {
                $this->load->model('printshop_model');
                foreach ($data['printshop_pics'] as $row) {
                    if ($row["status"] != Printshop_model::ROW_DELETE) {
                        $data["html"] .= $this->load->view('printshopinventory/picsfile_view', $row, TRUE);
                        $mdata["numrec"]++;
                    }
                }
            }
            // Popup Options
            $mdata['content']=$this->load->view('printshopinventory/pics_upload_view', $data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }
    // Save upload pictures
    public function inventory_picsattach() {
        $this->load->helper('upload');
        $postdata=$this->input->get();
        $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');

        $response = array('success' => false, 'msg'=>'Empty Upload Data');
        $data=usersession($uploadsession);

        if (!empty($data)) {
            $response['error']= 'server-error file not passed';

            $path = $this->config->item('upload_path_preload');
            // Allowed Extensions
            $arrayext=array('jpg', 'jpeg', 'JPG', 'JPEG');
            if (isset($_GET['qqfile'])) {
                $file = new qqUploadedFileXhr();
            } elseif (isset($_FILES['qqfile'])) {
                $file = new qqUploadedFileForm();
            } elseif (isset($_POST['qqfile'])) {
                $file = new qqUploadedFileXhr();
            }
            if ($file) {
                $response['error']= 'server-error file size is zero';
                $pics_source = $file->getName();
                $filesize = $file->getSize();

                if ($filesize > 0) {
                    $these = implode(', ', $arrayext);
                    $response['error']= 'File has an invalid extension, it should be one of '. $these . '.';
                    $pathinfo = pathinfo($pics_source);
                    $newfilename=uniq_link(12);
                    $ext = strtolower($pathinfo['extension']);

                    if (in_array($ext, $arrayext )) {
                        $pics = $path . $newfilename . '.' . $ext;
                        $file->save($pics);

                        $this->load->model('printshop_model');
                        $data['printshop_pics'][] = array(
                            'printshop_pics_id' => -1 * (count($data['printshop_pics']) + 1),
                            'pics_source' => $pics_source,
                            'pics' => $newfilename . '.' . $ext,
                            'status' => Printshop_model::ROW_INSERT,
                            'printshop_color_id' => $data["printshop_color_id"]
                        );
                        $content = "";
                        $response['numrec'] = 0;
                        foreach ($data['printshop_pics'] as &$row) {
                            if ($row["status"] != Printshop_model::ROW_DELETE) {
                                $response['numrec']++;
                                $content .= $this->load->view('printshopinventory/picsfile_view',$row,TRUE);
                            }
                        }
                        $response['content']=$content;
                        $response['success'] = true;
                        $response['error'] = '';
                        usersession($uploadsession, $data);
                    }
                }
            }
        }
        echo (json_encode($response));
        exit();
    }

    public function inventory_deluplpics() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);
            if (empty($data)) {
                $error='Session Expired. Please, recall form';
            } else {
                $id = $this->input->post('id');
                $content='';
                $mdata['numrec'] = 0;
                $this->load->model('printshop_model');
                foreach ($data['printshop_pics'] as &$row) {
                    if ($row["status"] != Printshop_model::ROW_DELETE && $row['printshop_pics_id'] != $id) {
                        $mdata['numrec']++;
                        $content .= $this->load->view('printshopinventory/picsfile_view',$row,TRUE);
                    } else {
                        $row["status"] = Printshop_model::ROW_DELETE;
                    }
                }
                $mdata['content']=$content;
                usersession($uploadsession, $data);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Change Spec for Inventory
    public function inventory_specedit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $specfile = $this->input->get('specfile');
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $data=usersession($uploadsession);
            $mdata['content']=$this->load->view('printshopinventory/specedit_view', $data,TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Save changes
    public function inventory_color_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Edit time expired. Reconnect';
            $postdata=$this->input->post();
            $uploadsession=(isset($postdata['uploadsession']) ? $postdata['uploadsession'] : 'failsession');
            $color=usersession($uploadsession);
            $brand = ifset($postdata,'brand');
            $printshop_color_id=$this->input->post('printshop_color_id');
            $specfile=$this->input->post('specfile');
            if (!empty($color)) {
                $color_id=$color['printshop_color_id'];
                $this->load->model('printshop_model');
                $res=$this->printshop_model->invitem_color_save($color);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $printshop_pics = $color['printshop_pics'];
                    $this->printshop_model->save_printshop_pics($printshop_pics, $res['printshop_color_id']);
                    usersession($uploadsession, NULL);
                    $mdata['new']=0;
                    if ($color_id<=0) {
                        $mdata['new']=1;
                    }
                    if ($mdata['new']==0) {
                        $options=array(
                            'orderby'=>'item_num',
                            'direct'=>'asc',
                            // 'brand' => $brand,
                            'brand' => 'ALL',
                        );

                        $data=$this->printshop_model->get_printshopitems($options);
                        $permission=$this->user_model->get_user_data($this->USR_ID);
                        // Make Total Inv content
                        $totaloptions=array(
                            'data'=>$data['inventory'],
                        );
                        $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
                        $specopt=array(
                            'data'=>$data['inventory'],
                            'permission'=>$permission['profit_view'],
                        );
                        $mdata['speccontent']=$this->load->view('printshopinventory/specinventory_data_view', $specopt, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Download OnBoat
    public function inventory_boat_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Date';
            $postdate=$this->input->post();
            if (isset($postdate['onboat_container'])) {
                $onboat_container=$postdate['onboat_container'];
                $this->load->model('printshop_model');
                $res=$this->printshop_model->inventory_download($onboat_container);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['url']=$res['url'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Add / change onboat container
    public function inventory_changecontainer() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata = $this->input->post();
            $onboat_container=ifset($postdata, 'container',0);
            $showmax=ifset($postdata, 'showmax',0);
            $brand = ifset($postdata,'brand','ALL');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_container_edit($onboat_container, $brand);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
                $session_id=uniq_link(14);
                $mdata['managecontent']=$this->load->view('printshopinventory/onboat_editmanage',array(), TRUE);
                if ($onboat_container==0) {
                    // Calc exist number of
                    $containers=$this->printshop_model->get_data_onboat($brand);
                    $viewwidth=(count($containers)+1)*$this->container_with;
                    $mdata['width']=$viewwidth;
                    $marginleft=($viewwidth>$this->maxlength ? ($this->maxlength-$viewwidth) : 0);
                    $mdata['marginleft']=($showmax==0 ? $marginleft : $marginleft-$this->container_with);
                    $details=$res['details'];
                    $headview='<div data-container="'.$details['onboat_container'].'" class="onboacontainer ">'.$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE)."</div>";
                    $mdata['containerhead']=$headview;
                    $boptions=array(
                        'data'=>$res['data'],
                        'onboat_container'=>$onboat_container,
                        'session'=>$session_id,
                        'brand' => $brand,
                    );
                    $content='<div data-container="'.$details['onboat_container'].'" class="onboacontainerarea">'.$this->load->view('printshopinventory/container_data_edit', $boptions, TRUE).'</div>';
                    $mdata['containercontent']=$content;
                    $mdata['onboat_container']=$details['onboat_container'];
                } else {
                    $boptions=array(
                        'data'=>$res['data'],
                        'onboat_container'=>$onboat_container,
                        'session'=>$session_id,
                        'brand' => $res['details']['brand'],
                    );
                    $mdata['containercontent']=$this->load->view('printshopinventory/container_data_edit', $boptions, TRUE);
                }

                $sessdata=array(
                    'total'=>$res['total'],
                    'data'=>$res['data'],
                    'details'=>$res['details'],
                );
                usersession($session_id, $sessdata);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change container data
    public function inventory_editcontainer() {
        if ($this->isAjax()) {
            $mdata=array();
            $error=$this->restore_invdata_error;
            $postdata=$this->input->post();
            $session_id=ifset($postdata, 'session', 'emptysession');
            $sessdata=usersession($session_id);
            if (!empty($sessdata)) {
                // Lets go
                $this->load->model('printshop_model');
                $res=$this->printshop_model->inventory_editcontainer($sessdata, $postdata);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($postdata['entity']=='color') {
                        $mdata['total']=  QTYOutput($res['total']);
                        $mdata['item']=$res['item'];
                        $mdata['totalitem']=QTYOutput($res['totalitem']);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_savecontainer() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $this->load->model('printshop_model');
            $session_id=ifset($postdata, 'session', 'emptysession');
            if ($postdata['action']=='cancel') {
                $error='';
                usersession($session_id, NULL);
                $onboat_container=$postdata['container'];
                // $data=$res['data'];
                $details=$this->printshop_model->get_container_details($onboat_container);
                $mdata['containerhead']=$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE);
                $colors=$this->printshop_model->get_printshop_itemcolors();
                $data=$this->printshop_model->get_container_view($onboat_container, $colors);
                $boptions=array(
                    'data'=>$data,
                    'onboat_container'=>$details['onboat_container'],
                    'onboat_status'=>$details['onboat_status'],
                );
                $mdata['containercontent']=$this->load->view('printshopinventory/purecontainer_data_view', $boptions, TRUE);
            } else {
                $error=$this->restore_invdata_error;
                $sessdata=usersession($session_id);
                if (!empty($sessdata)) {
                    $edit_container=$sessdata['details']['onboat_container'];
                    $res=$this->printshop_model->inventory_savecontainer($sessdata, $session_id);
                    $error=$res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error='';
                        $brand = ifset($postdata,'brand','ALL');
                        $onboat_container=$res['onboat_container'];
                        $mdata['onboat_container']=$res['onboat_container'];
                        $data=$res['data'];
                        $details=$this->printshop_model->get_container_details($onboat_container);
                        $mdata['delete']=1;
                        if (empty($details)) {
                            $options=array(
                                'orderby'=>'item_num',
                                'direct'=>'asc',
                                'brand' => $brand,
                            );
                            $data=$this->printshop_model->get_printshopitems($options);
                            // Get OnBoat Data
                            $colors=$data['colors'];

                            $boatdata = $this->printshop_model->get_data_onboat();
                            $slider_width=60*(count($boatdata));
                            $margin=$this->maxlength-$slider_width;
                            $margin=($margin>0 ? 0 : $margin);
                            // Head View
                            $boathead_view='';
                            foreach ($boatdata as $drow) {
                                $boathead_view.=$this->load->view('printshopinventory/onboat_containerhead_view', $drow, TRUE);
                            }
                            $mdata['headview']=$boathead_view;
                            // Download view
                            $mdata['download_view']=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$boatdata), TRUE);

                            $containers_view='';
                            foreach ($boatdata as $drow) {
                                $boatcontndata=$this->printshop_model->get_container_view($drow['onboat_container'], $colors);
                                $boptions=array(
                                    'data'=>$boatcontndata,
                                    'onboat_container'=>$drow['onboat_container'],
                                    'onboat_status'=>$drow['onboat_status'],
                                );
                                $containers_view.=$this->load->view('printshopinventory/container_data_view', $boptions, TRUE);
                            }
                            $boatoptions=array(
                                'width'=>$slider_width,
                                'margin'=>$margin,
                                'boatcontent'=>$containers_view,
                            );
                            $mdata['onboatcontent']=$this->load->view('printshopinventory/onboatdata_view', $boatoptions, TRUE);
                            $mdata['margin']=$margin;
                            $mdata['width']=$slider_width;
                        } else {
                            $mdata['delete']=0;
                            $boptions=array(
                                'data'=>$data,
                                'onboat_container'=>$details['onboat_container'],
                                'onboat_status'=>$details['onboat_status'],
                            );
                            if ($edit_container<0) {
                                $headview='<div data-container="'.$details['onboat_container'].'" class="onboacontainer ">'.$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE)."</div>";
                                $mdata['containerhead']=$headview;
                                $content='<div data-container="'.$details['onboat_container'].'" class="onboacontainerarea">'.$this->load->view('printshopinventory/purecontainer_data_view', $boptions, TRUE).'</div>';
                                $mdata['containercontent']=$content;
                                $containers=$this->printshop_model->get_data_onboat($brand);
                                $viewwidth=(count($containers))*$this->container_with;
                                $mdata['width']=$viewwidth;
                                $marginleft=($viewwidth>$this->maxlength ? ($this->maxlength-$viewwidth) : 0);
                                $mdata['marginleft']=($postdata['showmax']==0 ? $marginleft : $marginleft-$this->container_with);
                                $mdata['downloadview']=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$containers,), TRUE);
                            } else {
                                $mdata['containercontent']=$this->load->view('printshopinventory/purecontainer_data_view', $boptions, TRUE);
                                $mdata['containerhead']=$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE);
                            }

                        }
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Export inventore to excell
    public function inventory_export() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $error = 'Empty Brand';
            $brand = ifset($postdata, 'brand');
            if (!empty($brand)) {
                $this->load->model('printshop_model');
                $res=$this->printshop_model->export_inventory($brand);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    $mdata['url']=$res['url'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Stock Data
    public function inventory_colorstock() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $printshop_color_id=$this->input->post('printshop_color_id');
            $brand = $this->input->post('brand');
            // $brand = 'ALL';
            $this->load->model('printshop_model');
            $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id, 'ALL');
            $content=$this->load->view('printshop/instock_data_view', array('data'=>$res,'brand'=>$brand),TRUE);
            $mdata['content']=$this->load->view('printshop/instock_popup_view', array('content'=>$content, 'brand'=>$brand, 'color' => $printshop_color_id,), TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Edit Stock value
    public function invcolor_stock_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $printshop_instock_id=$this->input->post('printshop_instock_id');
            $printshop_color_id=$this->input->post('printshop_color_id');

            $this->load->model('printshop_model');
            if ($printshop_instock_id==0) {
                $stock=$this->printshop_model->new_colorinstock($printshop_color_id);
            } else {
                $res=$this->printshop_model->invitem_color_stockdata($printshop_instock_id);
                if ($res['result']==$this->error_result) {
                    $error=$res['msg'];
                    $this->ajaxResponse($mdata, $error);
                }
                $stock=$res['data'];
            }
            $mdata['content']=$this->load->view('printshop/instock_data_edit', $stock,TRUE);
            usersession('stockdata', $stock);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Change value
    public function invcolor_stock_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $stockdata=usersession('stockdata');
            $postdata=$this->input->post();
            $this->load->model('printshop_model');
            $res=$this->printshop_model->invcolor_stock_change($stockdata, $postdata);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Cancel data
    public function inventory_colorstock_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $brand = $this->input->post('brand');
            $this->load->model('printshop_model');
            $stockdata=usersession('stockdata');
            $res=$this->printshop_model->invitem_color_stocksave($stockdata, $brand);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            } else {
                $printshop_color_id=$res['printshop_color_id'];
                // $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id, $brand);
                $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id, 'ALL');
                $mdata['content']=$this->load->view('printshop/instock_data_view', array('data'=>$res, 'brand' => $brand),TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function inventory_colorstock_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $printshop_color_id=$this->input->post('printshop_color_id');
            $brand = $this->input->post('brand');
            // $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id, $brand);
            $res=$this->printshop_model->invitem_color_stocklog($printshop_color_id, 'ALL');
            $mdata['content']=$this->load->view('printshop/instock_data_view', array('data'=>$res, 'brand' => $brand),TRUE);
            usersession('stockdata', NULL);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    // Arive container
    public function inventory_arrivecontainer() {
        if($this->isAjax()) {
            $mdata = array();
//            $onboat_container=$this->input->post('onboat_container');
//            $brand = $this->input->post('brand');
            $postdata = $this->input->post();
            $onboat_container = ifset($postdata,'onboat_container',0);
            $error = 'Container Not Found';
            if (!empty($onboat_container)) {
                $this->load->model('printshop_model');
                $res = $this->printshop_model->onboat_arrived($onboat_container);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
//                $options=array(
//                    'orderby'=>'item_num',
//                    'direct'=>'asc',
//                    'brand' => 'brand',
//                );
//                $data=$this->printshop_model->get_printshopitems($options);
//                // New Inv totals
//                $mdata['total_inventory']=MoneyOutput($data['inventtotal']);
//                // Make Total Inv content
//                $totaloptions=array(
//                    'data'=>$data['inventory'],
//                );
//                $mdata['totalinvcontent']=$this->load->view('printshopinventory/totalinventory_data_view', $totaloptions, TRUE);
//
//                $details=$this->printshop_model->get_container_details($onboat_container);
//                $mdata['containerhead']=$this->load->view('printshopinventory/purecontainer_head_view', $details, TRUE);
//                $totalinv=$this->printshop_model->get_inventory_totals($brand);
//                $mdata['totalinvview']=$this->load->view('printshopinventory/total_inventory_view',$totalinv,TRUE);

            // $edit = $this->printshop_model->add_to_instock($onboat_date);

            $this->ajaxResponse($mdata, $error);
        }
    }

    function inventory_plate_download() {
        if ($this->isAjax()) {
            $mdata=array();

            $item_id=$this->input->post('printshop_item_id');
            $type = $this->input->post('type');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_invent_item($item_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $item=$res['item'];
                $mdata['fileurl']=$item['plate_temp'];
                $mdata['filename']=$item['plate_temp_source'];
                if($type=='proof') {
                    $mdata['fileurl']=$item['proof_temp'];
                    $mdata['filename']=$item['proof_temp_source'];
                } elseif ($type=='itemlabel') {
                    $mdata['fileurl']=$item['item_label'];
                    $mdata['filename']=$item['item_label_source'];
                }
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    function inventory_pics_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $color_id=$this->input->post('printshop_color_id');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_picsattachments($color_id);

            foreach ($res as $fileurl => $row) {
                $fileu[$fileurl]=$row['pics'];
            }

            foreach ($res as $filename => $row) {
                $filen[$filename]=$row['pics_source'];
            }

            $mdata['fileurl'] = $fileu;
            $mdata['filename'] =$filen;
            $error='';

            $this->ajaxResponse($mdata, $error);
        }
    }

    // INVENTORY NEED LIST
    public function invneedlist_brand() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                $this->load->model('printshop_model');
                $data = $this->printshop_model->get_data_onboat($brand);
                $boathead_view='';
                foreach ($data as $drow) {
                    $boathead_view.=$this->load->view('inventoryview/onboat_containerhead_view', $drow, TRUE);
                }
                // Build head content
                $slider_width=60*count($data);
                $margincount = $this->needlistlength-$slider_width;
                $margin=($margincount>0 ? 0 : $margincount);

                $boatoptions=array(
                    'data'=>$data,
                    'container_view'=>$boathead_view,
                    'width' => $slider_width,
                    'margin' => $margin,
                );
                $mdata['onboat_content']=$this->load->view('inventoryview/onboathead_view', $boatoptions, TRUE);
                $mdata['download_view']=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
                $mdata['width']=$slider_width;
                $mdata['margin'] = $margin;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Inventory List data
    public function datalist() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $this->load->model('printshop_model');
            $brand = ifset($postdata,'brand');
            $options=array(
                'orderby'=>'aftercontproc',
                'direct'=>'asc',
                'place'=>'fulfillment',
                'brand' => $brand,
            );
            $data=$this->printshop_model->get_needinvlistdata($options);


            // Make Total Inv content
            $totaloptions=array(
                'data'=>$data['inventory'],
            );
            $mdata['totalinvcontent']=$this->load->view('inventoryview/inventory_data_view', $totaloptions, TRUE);
            $specopt=array(
                'data'=>$data['inventory'],
            );
            $mdata['speccontent']=$this->load->view('inventoryview/specinventory_data_view', $specopt, TRUE);
            // Get OnBoat Data
            $colors=$data['inventory'];
            $boatdata = $this->printshop_model->get_data_onboat($brand);
            $containers_view='';
            foreach ($boatdata as $drow) {
                $boatcontndata=$this->printshop_model->get_needinvlistboat_details($drow['onboat_container'], $colors);
                $boptions=array(
                    'data'=>$boatcontndata,
                    'onboat_container'=>$drow['onboat_container'],
                    'onboat_status'=>$drow['onboat_status'],
                );
                $containers_view.=$this->load->view('inventoryview/container_data_view', $boptions, TRUE);
            }
            $slider_width=60*(count($boatdata));
            $margin=$this->needlistlength-$slider_width;

            $boatoptions=array(
                'width'=>$slider_width,
                'margin'=>($margin>0 ? 0 : $margin),
                'boatcontent'=>$containers_view,
            );
            $mdata['onboatcontent']=$this->load->view('inventoryview/onboatdata_view', $boatoptions, TRUE);
            $mdata['margin']=$margin;
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    function plate_download() {
        if ($this->isAjax()) {
            $mdata=array();
            $item_id=$this->input->post('printshop_item_id');
            $type = $this->input->post('type');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->get_invent_item($item_id);
            $error=$res['msg'];
            if ($res['result']== $this->success_result) {
                $item=$res['item'];
                $mdata['fileurl']=$item['plate_temp'];
                $mdata['filename']=$item['plate_temp_source'];

                if($type=='proof') {
                    $mdata['fileurl']=$item['proof_temp'];
                    $mdata['filename']=$item['proof_temp_source'];
                } elseif ($type=='itemlabel') {
                    $mdata['fileurl']=$item['item_label'];
                    $mdata['filename']=$item['item_label_source'];
                }
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Printshop Report
    // Order Data View and manage
    /*public function orderreport_head() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $totalrecs=$this->printshop_model->get_orderreport_counts();
            $summary=$this->printshop_model->get_orderreport_totals();
            $summary_view=$this->load->view('printshop/orderreport_summary_view', $summary, TRUE);
            $addcosts=$this->printshop_model->_get_plates_costs();
            $options=array(
                'totals'=>$totalrecs,
                'summary'=>$summary_view,
                'repaid_cost'=>$addcosts['repaid_cost'],
                'orangeplate_price'=>$addcosts['orangeplate_price'],
                'blueplate_price'=>$addcosts['blueplate_price'],
            );
            // Get Summary
            $mdata['content']=$this->load->view('printshop/orderreport_head_view', $options, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    */
    // show Profit $ and %
    public function ordereport_profit() {
        $order_id=$this->input->get('d');
        $inven_id=$this->input->get('amnt');
        $this->load->model('orders_model');
        $this->load->model('printshop_model');
        $order=$this->orders_model->get_order_detail($order_id);
        $inventlevel=$this->printshop_model->get_invenory_level($inven_id);
        $options=array(
            'order'=>$order,
            'invent'=>$inventlevel,
        );
        $out_msg=$this->load->view('printshop/ordrereport_orderprofit_view', $options, TRUE);
        echo $out_msg;
    }

    public function inventoryoutdetails($amount_id) {
        $this->load->model('inventory_model');
        $res = $this->inventory_model->get_amount_details($amount_id);
        $content = $this->load->view('printshop/ordrereport_outcomedetails_view',['details' => $res], TRUE);
        echo $content;
    }

    // Edit Data of report
    public function orderreport_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $this->load->model('inventory_model');
            $postdata=$this->input->post();
            $printshop_income_id=(isset($postdata['printshop_income_id']) ? $postdata['printshop_income_id'] : 0);
            $showorange = (isset($postdata['showorange']) ? $postdata['showorange'] : 0);
            $res=$this->inventory_model->get_printshop_order($printshop_income_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $data=$res['data'];
                // Get Items for dropdown
                $items=$this->inventory_model->get_printshopitem_list();
                // Get Colors of Item
                $colors=$this->inventory_model->get_item_colors($data['inventory_item_id']);
                $sessionid='order'.uniq_link(15);
                $data['items']=$items;
                $data['colors']=$colors;
                $data['session']=$sessionid;
                $data['showorange'] = $showorange;
                $data['title'] = $res['title'];
                $mdata['content']=$this->load->view('printshop/orderreport_edit_view', $data, TRUE);
                usersession($sessionid, $data);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Change session values
    public function orderreport_change() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Edit Connection Lost. Please, recall form';
            $postdata=$this->input->post();
            $sessionid=$postdata['sessionid'];
            $orderdata=usersession($sessionid);
            if (!empty($orderdata)) {
                $fldname=$postdata['fldname'];
                $newval=$postdata['newval'];
                $this->load->model('inventory_model');
                $res=$this->inventory_model->change_printshop_order($orderdata, $fldname, $newval,$sessionid);
                $error=$res['msg'];
                if (isset($res['oldval'])) {
                    $mdata['oldval']=$res['oldval'];
                }
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $orderdata=usersession($sessionid);
                    $mdata['misprint_proc']=$orderdata['misprint_proc'];
                    $mdata['total_qty']=QTYOutput($orderdata['total_qty']);
                    $mdata['costitem']=MoneyOutput($orderdata['costitem']);
                    $mdata['totalplates']=QTYOutput($orderdata['totalplates']);
                    $mdata['platescost']=MoneyOutput($orderdata['platescost']);
                    $mdata['itemstotalcost']=MoneyOutput($orderdata['itemstotalcost']);
                    $mdata['misprintcost']=  MoneyOutput($orderdata['misprintcost']);
                    $mdata['extraitem']= MoneyOutput($orderdata['extraitem']);
                    $mdata['price']=number_format($orderdata['price'],3);
                    $mdata['extracost']=number_format($orderdata['extracost'],3);
                    $mdata['totalea']=number_format($orderdata['totalea'],3);
                    $mdata['customer']=$orderdata['customer'];
                    // totalea,3
                    if ($fldname=='inventory_item_id') {
                        $options=array(
                            'printshop_color_id'=>$orderdata['inventory_color_id'],
                            'colors'=>$orderdata['colors'],
                        );
                        $mdata['colorlist']=$this->load->view('printshop/orderreport_colorselect_view', $options, TRUE);
                    }
                    if ($fldname=='inventory_color_id') {
                        $balance = $this->inventory_model->inventory_balance($newval);
                        $mdata['title'] = 'Available '.QTYOutput($balance);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Save Sesision data
    public function orderreport_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata=$this->input->post();
            $error='Edit Connection Lost. Please, recall form';
            $sessionid=ifset($postdata, 'sessionid','empptysession');
            $brand = ifset($postdata,'brand');
            $search = ifset($postdata,'search');
            $report_year = ifset($postdata,'report_year');
            $orderdata=usersession($sessionid);
            if (!empty($orderdata)) {
                $this->load->model('inventory_model');
                $res=$this->inventory_model->save_printshop_order($orderdata, $sessionid, $this->USR_ID);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $orderdata['printshop_income_id'] = $res['printshop_income_id'];
                    $invres = $this->inventory_model->save_inventory_outcome($orderdata, $sessionid, $this->USR_ID);
                    $error = $invres['msg'];
                    if ($invres['result']==$this->success_result) {
                        $error = '';
                        $total_options = [];
                        if (!empty($search)) {
                            $total_options['search']=strtoupper($search);
                        }
                        if (!empty($report_year)) {
                            $total_options['report_year'] = $report_year;
                        }
                        if (!empty($brand)) {
                            $total_options['brand'] = $brand;
                        }
                        $mdata['totals']=$this->inventory_model->get_orderreport_counts($total_options);
                        $summary=$this->inventory_model->get_orderreport_totals($total_options);
                        $mdata['summary_view']=$this->load->view('printshop/orderreport_summary_view', $summary, TRUE);
                        // $this->load->model('orders_model');
                        // $order=$this->orders_model->get_order_detail($res['order_id']);
                        // $inventlevel=$this->inventory_model->get_invenory_level($res['printshop_income_id']);
                        // $options=array(
                        //    'order'=>$order,
                        //    'invent'=>$inventlevel,
                        // );
                        // $mdata['newprofit_view']=$this->load->view('printshop/ordrereport_orderprofit_view', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }


    public function orderreport_data() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Brand';
            $this->load->model('inventory_model');
            $postdata=$this->input->post();
            $limit=(intval($postdata['limit'])==0 ? 30 : $postdata['limit']);
            $page=(intval($postdata['offset'])==0 ? 0 : $postdata['offset']);
            $offset=$page*$limit;
            $totals=$postdata['totals'];
            $options=array(
                'limit'=>$limit,
                'offset'=>$offset,
                'totals'=>$totals,
            );
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }
            if (isset($postdata['report_year']) && !empty($postdata['report_year'])) {
                $options['report_year']=$postdata['report_year'];
            }
            $brand = ifset($postdata,'brand');
            if (!empty($brand)) {
                $error = '';
                if ($brand=='SG') {
                    $brand = 'ALL';
                }
                $options['brand']=$brand;
                $res=$this->inventory_model->get_orderreport_data($options);
                $options=array(
                    'orders'=>$res,
                );
                $mdata['content']=$this->load->view('printshop/orderreport_data_view', $options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Export data to excel
    public function orderreport_dataexport() {
        if ($this->isAjax()) {
            $mdata=array();

            $this->load->model('inventory_model');
            $postdata=$this->input->post();
            $options=array(
                'export' => 1,
            );
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }
            if (isset($postdata['report_year']) && !empty($postdata['report_year'])) {
                $options['report_year']=$postdata['report_year'];
            }
            if (isset($postdata['brand']) && !empty($postdata['brand'])) {
                if ($postdata['brand']!=='SG') {
                    $options['brand'] = $postdata['brand'];
                }
            }
            $res=$this->inventory_model->get_orderreport_data($options);
            $error='Empty content for exxport';
            if (count($res)>0) {
                $error = '';
                $this->load->model('exportexcell_model');
                $mdata['url'] = $this->exportexcell_model->export_orderreport($res);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function orderreport_remove() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();
            // After
            $amount_id=$postdata['printshop_income_id'];
            $this->load->model('inventory_model');
            $res=$this->inventory_model->orderreport_remove($amount_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $options=array();
                if (isset($postdata['search']) && !empty($postdata['search'])) {
                    $options['search']=strtoupper($postdata['search']);
                }
                if (isset($postdata['report_year']) && !empty($postdata['report_year'])) {
                    $options['report_year'] = $postdata['report_year'];
                }
                if (isset($postdata['brand']) && !empty($postdata['brand'])) {
                    if ($postdata['brand']!=='SG') {
                        $options['brand']=$postdata['brand'];
                    }
                }
                $mdata['totals']=$this->inventory_model->get_orderreport_counts($options);
                $summary=$this->inventory_model->get_orderreport_totals($options);
                $mdata['summary_view']=$this->load->view('printshop/orderreport_summary_view', $summary, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    // Change additional cost
    public function orderreport_addcost() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $fldname=$this->input->post('fldname');
            $newval=$this->input->post('newval');
            $this->load->model('printshop_model');
            $res=$this->printshop_model->change_additional_cost($fldname, $newval);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Search by template
    public function orderreport_search() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('printshop_model');
            $postdata=$this->input->post();
            $options=array();
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=strtoupper($postdata['search']);
            }
            if (isset($postdata['report_year']) && !empty($postdata['report_year'])) {
                $options['report_year']=$postdata['report_year'];
            }
            if (isset($postdata['brand']) && !empty($postdata['brand'])) {
                if ($postdata['brand']!=='SG') {
                    $options['brand'] = $postdata['brand'];
                }
            }
            $mdata['totals']=$this->printshop_model->get_orderreport_counts($options);
            $summary=$this->printshop_model->get_orderreport_totals($options);
            $mdata['summary_view']=$this->load->view('printshop/orderreport_summary_view', $summary, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }



    private function _prepare_vendors_view() {
        $this->load->model('vendors_model');
        $totals=$this->vendors_model->get_count_vendors();
        $options=array(
            'perpage'=> 250,
            'order'=>'vendor_name',
            'direc'=>'asc',
            'total'=>$totals,
            'curpage'=>0,
        );
        $content=$this->load->view('fulfillment/vendors_view', $options, TRUE);
        return $content;
    }

    private function _prepare_status_view($brand) {
        $dat=strtotime(date('m/d/Y',time())." -6 months");
        $def_options=array(
            'profit_perc'=>NULL,
            'is_canceled'=>0,
            'min_time'=>$dat,
            'brand' => $brand,
        );
        $this->load->model('orders_model');
        $totals=$this->orders_model->get_count_orderslimits($def_options);
        $options=array(
            'perpage'=> 250,
            'order'=>'order_proj_status',
            'direc'=>'asc',
            'total'=>$totals,
            'curpage'=>0,
            'brand' => $brand,
        );
        $content=$this->load->view('fulfillment/status_view',$options,TRUE);
        return $content;
    }

    private function _prepare_purchaseorders_view($brand) {
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

        $poreptotals = $this->payments_model->get_poreport_totals($year1, $year2, $year3, $brand);
        $options=[
            'totaltab' => $totaltab,
            'totals' => $totals,
            'inner' => $inner,
            'brand' => $brand,
            'years' => $years,
            'year1' => $year1,
            'year2' => $year2,
            'year3' => $year3,
            'poreporttotals' => $poreptotals,
            'poreportperpage' => 8,
        ];
        return $this->load->view('pototals/page_view',$options,TRUE);
    }


//    private function _prepare_printshop_inventory($brand) {
//        $this->load->model('printshop_model');
//        $addcost=$this->printshop_model->invaddcost();
//        $totals=$this->printshop_model->count_prinshop_items();
//        // $totalinv=$this->printshop_model->get_inventory_totals($brand);
//        $totalinv=$this->printshop_model->get_inventory_totals('ALL');
//        $totalinvview=$this->load->view('printshopinventory/total_inventory_view',$totalinv,TRUE);
//        // $data = $this->printshop_model->get_data_onboat($brand);
//        $data = $this->printshop_model->get_data_onboat('ALL');
//        $boathead_view='';
//        foreach ($data as $drow) {
//            $boathead_view.=$this->load->view('printshopinventory/onboat_containerhead_view', $drow, TRUE);
//        }
//        // Build head content
//        // $slider_width=60*count($data);
//        $slider_width=60*count($data);
//        $margin = $this->maxlength-$slider_width;
//        $margin=($margin>0 ? 0 : $margin);
//        $width_edit = 58;
//        $boatoptions=array(
//            'data'=>$data,
//            'container_view'=>$boathead_view,
//            'width' => $slider_width,
//            'margin' => $margin,
//        );
//        $onboat_content=$this->load->view('printshopinventory/onboathead_view', $boatoptions, TRUE);
//
//        $permission=$this->user_model->get_user_data($this->USR_ID);
//        $download_view=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
//        $headoptions=array(
//            'permission' => $permission['profit_view'],
//            'addcost'=>$addcost,
//            'data' => $data,
//            'width' => $slider_width,
//            'margin' => $margin,
//            'onboathead'=>$onboat_content,
//            'invetorytotal'=>$totalinvview,
//            'download_view'=>$download_view,
//        );
//        $headview=$this->load->view('printshopinventory/fullview_head_view', $headoptions,TRUE);
//
//        /*$specs_disc = $this->printshop_model->get_color_disc();*/
//
//        $invoption=array(
//            'totals'=>$totals,
//            'fullview'=>$headview,
//            'maxsum'=>$totalinv['maxsum'],
//            'brand' => $brand,
//        );
//        $content=$this->load->view('printshopinventory/page_view', $invoption, TRUE);
//        return $content;
//    }

    private function _prepare_needlist_view($brand) {
        $this->load->model('printshop_model');
        $addcost=$this->printshop_model->invaddcost();
        $totals=$this->printshop_model->count_prinshop_items();
        $data = $this->printshop_model->get_data_onboat($brand);
        $boathead_view='';
        foreach ($data as $drow) {
            $boathead_view.=$this->load->view('inventoryview/onboat_containerhead_view', $drow, TRUE);
        }
        // Build head content
        $slider_width=60*count($data);
        $margincount = $this->needlistlength-$slider_width;
        $margin=($margincount>0 ? 0 : $margincount);

        $boatoptions=array(
            'data'=>$data,
            'container_view'=>$boathead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $onboat_content=$this->load->view('inventoryview/onboathead_view', $boatoptions, TRUE);

        $download_view=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
        $headoptions=array(
            'addcost'=>$addcost,
            'data' => $data,
            'width' => $slider_width,
            'margin' => $margin,
            'onboathead'=>$onboat_content,
            'download_view'=>$download_view,
        );
        $headview=$this->load->view('inventoryview/needlist_head_view', $headoptions,TRUE);

        /*$specs_disc = $this->printshop_model->get_color_disc();*/

        $invoption=array(
            'totals'=>$totals,
            'headview'=>$headview,
            'brand' => $brand,
        );

        $content=$this->load->view('inventoryview/page_data_view', $invoption, TRUE);
        return $content;
    }

    private function _prepare_inventsalesrep_view($brand) {
        $this->load->model('printshop_model');
        $addcost=$this->printshop_model->invaddcost();
        $totals=$this->printshop_model->count_prinshop_items();
        $totalinv=$this->printshop_model->get_inventory_totals($brand);
        $totalinvview=$this->load->view('invsalesrep/total_inventory_view',$totalinv,TRUE);
        $data = $this->printshop_model->get_data_onboat($brand);
        $boathead_view='';
        foreach ($data as $drow) {
            $boathead_view.=$this->load->view('invsalesrep/onboat_containerhead_view', $drow, TRUE);
        }
        // Build head content
        $slider_width=60*count($data);
        $margin = $this->salesreplength-$slider_width;
        $margin=($margin>0 ? 0 : $margin);

        $width_edit = 58;
        $boatoptions=array(
            'data'=>$data,
            'container_view'=>$boathead_view,
            'width' => $slider_width,
            'margin' => $margin,
        );
        $onboat_content=$this->load->view('invsalesrep/onboathead_view', $boatoptions, TRUE);

        $permission=$this->user_model->get_user_data($this->USR_ID);
        // $download_view=$this->load->view('printshopinventory/onboat_download_view', array('data'=>$data,), TRUE);
        $headoptions=array(
            'permission' => $permission['profit_view'],
            'addcost'=>$addcost,
            'data' => $data,
            'width' => $slider_width,
            'margin' => $margin,
            'onboathead'=>$onboat_content,
            'invetorytotal'=>$totalinvview,
        );
        $headview=$this->load->view('invsalesrep/salesrep_head_view', $headoptions,TRUE);

        $invoption=array(
            'totals'=>$totals,
            'fullview'=>$headview,
            'maxsum'=>$totalinv['maxsum'],
            'brand' => $brand,
        );

        $content=$this->load->view('invsalesrep/page_view', $invoption, TRUE);
        return $content;

    }

    private function _prepare_printshop_report($brand) {
        // $this->load->model('printshop_model');
        $this->load->model('inventory_model');
        if ($brand=='SG') {
            $brand = 'ALL';
        }
        $total_options = ['brand'=> $brand];
        $totalrecs=$this->inventory_model->get_orderreport_counts($total_options);
        $summary=$this->inventory_model->get_orderreport_totals($total_options);
        $summary_view=$this->load->view('printshop/orderreport_summary_view', $summary, TRUE);
        $addcosts=$this->inventory_model->_get_plates_costs();
        $report_years=$this->inventory_model->get_report_years($total_options);
        $options=array(
            'totals'=>$totalrecs,
            'summary'=>$summary_view,
            'repaid_cost'=>$addcosts['repaid_cost'],
            'orangeplate_price'=>$addcosts['orangeplate_price'],
            'blueplate_price'=>$addcosts['blueplate_price'],
            'beigeplate_price' => $addcosts['beigeplate_price'],
            'report_years'=>$report_years,
            'brand' => $brand,
        );
        // Get Summary
        $content=$this->load->view('printshop/pagereport_view', $options, TRUE);
        return $content;
    }

    private function _prepare_inventory_view() {
        $this->load->model('inventory_model');
        $this->load->model('printshop_model');
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

    private function _prepare_printscheduler_view($brand)
    {
        // Prepare t
        $this->load->model('printscheduler_model');

        $res = $this->printscheduler_model->get_printsheduler_totals($brand);

        $content = $this->load->view('printscheduler/page_view', $res, true);
        return $content;
    }

}