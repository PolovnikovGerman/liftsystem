<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends MY_Controller
{

    private $pagelink = '/admin';
    protected $PERPAGE=1000;
    private $restore_session_error='Edit Connection Lost. Please, recall form';

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
        $head=[];
        $head['title']='Admin';
        $brand = $this->menuitems_model->get_current_brand();
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink,'');

        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#usersview') {
                $head['styles'][]=array('style'=>'/css/admin/usersview.css');
                $head['scripts'][]=array('src'=>'/js/admin/usersview.js');
                $content_options['usersview'] = $this->_prepare_users_view();
            } elseif ($row['item_link']=='#parseremailsview') {
                $head['styles'][]=array('style'=>'/css/admin/parseremailsview.css');
                $head['scripts'][]=array('src'=>'/js/admin/parseremailsview.js');
                $content_options['parseremailsview'] = $this->_prepare_parseremail_view();
            } elseif ($row['item_link']=='#artalertsview') {
                $head['styles'][]=array('style'=>'/css/admin/artalertsview.css');
                $head['scripts'][]=array('src'=>'/js/admin/artalertsview.js');
                $head['styles'][]=array('style'=>'/css/admin/jquery.spinnercontrol.css');
                $head['scripts'][]=array('src'=>'/js/admin/jquery.spinnercontrol.js');
                $content_options['artalertsview'] = $this->_prepare_artalert_view();
            }
        }

        $content_options['menu']=$menu;
        // Add main page management
        $head['scripts'][]=array('src'=>'/js/admin/page.js');
        $head['styles'][] = array('style'=> '/css/admin/page.css');
        // Utils
        $head['scripts'][]=array('src'=>'/js/admin/jQuery.Tree.js');
        $head['styles'][]=array('style'=>'/css/admin/jQuery.Tree.css');
        // Datepicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');

        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // Searchable
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

        $content_view = $this->load->view('admin/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;

        $this->load->view('page/page_template_view', $dat);
    }

    public function usersdata() {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $options = [];
            $pagenum = ifset($postdata,'offset',0);
            $limit = ifset($postdata,'limit',$this->PERPAGE);
            $options['limit']=$limit;
            $options['offset']=$pagenum*$limit;
            $options['order_by'] = ifset($postdata,'order_by','user_id');
            $options['direction'] = ifset($postdata,'direction','asc');
            $res = $this->user_model->get_userscontrollist($options);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $data = $res['data'];
                if (count($data)==0) {
                    $mdata['content']='';
                } else {
                    $mdata['content']=$this->load->view('admin/users_datalist_view', ['data'=>$data], TRUE);
                }

            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function user_delete() {
        if ($this->isAjax()) {
            $mdata=array();
            $user_id=$this->input->post('user_id');
            $result=$this->user_model->delete_usr($user_id, $this->USR_ID);
            $error=$result['msg'];
            if ($result['res']==$this->success_result) {
                $error = '';
                $mdata['total'] = $this->user_model->get_count_user(['status'=> [1,2]]);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function user_changestatus() {
        if ($this->isAjax()) {
            $mdata=array();
            $user_id=$this->input->post('user_id');
            $curstatus = $this->input->post('status');
            $result=$this->user_model->update_userstatus($user_id, $curstatus, $this->USR_ID);
            $error = $result['msg'];
            if ($result['result']==$this->success_result) {
                $error = '';
                $mdata['user_id']=$user_id;
                $mdata['newstatus'] = $result['user_status'];
                $mdata['status_txt'] = $result['status_txt'];
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function user_editdata() {
        if ($this->isAjax()) {
            $mdata = [];
            $user_id = $this->input->post('user_id');
            if ($user_id==0) {
                $data = $this->user_model->new_user();
                $error = '';
                $mdata['title'] = 'Add New User';
                $userip = [];
            } else {
                $res = $this->user_model->get_user_details($user_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $data = $res['data'];
                    $mdata['title'] = 'Edit User '.$data['user_name'];
                    $userip = $this->user_model->get_user_iprestrict($user_id);
                }
            }

            if ($error=='') {
                $sbpages = $this->tree(null, 0, $user_id, 'SB');
                $srpages = $this->tree(null, 0, $user_id, 'SR');
                $commonpages = $this->tree(null, 0, $user_id, 'NONE');
                $sgpages = $this->tree(null, 0, $user_id, 'SG');
                $wpages=[
                    'sbpages' => $sbpages,
                    'srpages' => $srpages,
                    'sgpages' => $sgpages,
                    'commpages' => $commonpages,
                ];
                $pagepermiss=$this->load->view('admin/webpage_tree_view', $wpages,TRUE);
                $iprestricts = $this->load->view('admin/user_iprestrict_view',['userip'=>$userip], TRUE);
                $pages_list=$this->menuitems_model->get_webpages();
                $pages_select = $this->load->view('admin/user_defaultpage_view',['data' => $pages_list, 'defpage' => $data['user_page']], TRUE);

                $session_data = [
                    'user' => $data,
                    'userip' => $userip,
                    'webpages' => $wpages,
                    'deleted' => [],
                ];
                $session_id = 'userdata'.uniq_link(10);
                usersession($session_id, $session_data);
                $options = [
                    'user' => $data,
                    'iprestricts' => $iprestricts,
                    'webpages' => $pagepermiss,
                    'session' => $session_id,
                    'pages_select' => $pages_select,
                ];

                $mdata['content'] = $this->load->view('admin/user_details_view', $options, TRUE);
                $mdata['footer'] = $this->load->view('admin/user_savedata_view',[], TRUE);
            }
        }
        $this->ajaxResponse($mdata, $error);
    }

    /* Recursion Function */
    private function tree($pid, $lvl, $user_id, $brand){
        $wpages=$this->menuitems_model->get_webpage($pid, $user_id, $brand);
        $out=array();
        foreach ($wpages as $wrow){
            $lvl++;
            $label='<span class="usrpermissionlabel">'.$wrow['item_name'].'</span>';
            // 'brand_access' => $wrow['brand_access'],
            // 'brand' => $wrow['brand'],

            // if ($wrow['brand_access']!=='' && $wrow['brand_access']!=='NONE') {
            //    $label.='&nbsp;'.$this->_sitemenu_useraccess($wrow, $user_id);
            // }
            $id=$wrow['menu_item_id'];
            $value=($wrow['permission_type']=='' ? 0 : $wrow['permission_type']);
            $elem=$this->tree($id, $lvl--,$user_id, $brand);
            if ($elem==array()) {
                $elem=$wrow['permission_type'];
            }
            $out[]=array(
                'label'=>$label,
                'id'=>$id,
                'element'=>$elem,
                'value'=>$value,
                // 'brand' => $wrow['brand'],
            );
        }
        return $out;
    }

    private function _sitemenu_useraccess($wrow, $user_id) {
        $brands = [];
        if ($wrow['brand_access']=='SITE') {
            $brands[] = ['key' => '', 'value' => 'None'];
            $brands[] = ['key' => 'ALL', 'value' => 'All'];
            $brands[] = ['key' => 'SB', 'value' => 'Stressball.com only'];
            $brands[] = ['key' => 'BT', 'value' => 'Bluetrack only'];
        } elseif ($wrow['brand_access']=='BRAND') {
            $brands = $wrow['brand'];
        } else {
            $brands[] = ['key' => '', 'value' => 'None'];
            $brands[] = ['key' => 'SB', 'value' => 'Stressball.com only'];
            $brands[] = ['key' => 'BT', 'value' => 'Bluetrack only'];
        }
        $options=array(
            'brand'=>$wrow['brand'],
            'brands'=>$brands,
            'menu_item' => $wrow['menu_item_id'],
        );
        if ($wrow['brand_access']=='BRAND') {
            return $this->load->view('admin/sitemenu_multyaccess_view', $options, TRUE);
        } else {
            return $this->load->view('admin/sitemenu_access_view', $options, TRUE);
        }


    }

    public function changepagepermission() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $menuitem = ifset($postdata, 'menuitem', 0);
                $brand = ifset($postdata,'brand','');
                $newval = ifset($postdata,'newval', 0);
                $res = $this->menuitems_model->update_userpage_permission($session_data, $menuitem, $brand, $newval, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['child'] = $res['child'];
                    $mdata['child_count'] = count($res['child']);
                    $mdata['newval'] = ($newval==0 ? '' : 'ALL');
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function changesiteaccess() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $menuitem = ifset($postdata, 'menuitem', 0);
                $newval = ifset($postdata,'newval', '');
                $res = $this->menuitems_model->update_userpage_siteaccess($session_data, $menuitem, $newval, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function changebrandaccess() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $menuitem = ifset($postdata, 'menuitem', 0);
                $brand = ifset($postdata,'brand', '');
                $res = $this->menuitems_model->update_userpage_brandaccess($session_data, $menuitem, $brand, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if ($res['newval']==0) {
                        $mdata['content']='<i class="fa fa-square-o" aria-hidden="true"></i>';
                    } else {
                        $mdata['content']='<i class="fa fa-check-square-o" aria-hidden="true"></i>';
                    }
                    $mdata['newacc'] = $res['newacc'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userip_restrict_add() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->user_model->userip_restrict_add($session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options = [
                        'userip' => $res['userip'],
                    ];
                    $mdata['content'] = $this->load->view('admin/user_iprestrict_view',$options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userip_restrict_edit() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $user_restriction_id = ifset($postdata,'id',0);
                $newval = ifset($postdata,'newval','');
                $res = $this->user_model->userip_restrict_edit($user_restriction_id, $newval, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userip_restrict_delete() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $user_restriction_id = ifset($postdata,'id',0);
                $res = $this->user_model->userip_restrict_delete($user_restriction_id, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options = [
                        'userip' => $res['userip'],
                    ];
                    $mdata['content'] = $this->load->view('admin/user_iprestrict_view',$options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userdata_change() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $item = ifset($postdata, 'item', '');
                $newval = ifset($postdata, 'newval', '');
                $res = $this->user_model->userdata_edit($item, $newval, $session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function userdata_save() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = $this->restore_session_error;
            $postdata = $this->input->post();
            $session_id = ifset($postdata, 'session','emptysession');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $res = $this->user_model->update_userdata($session_data, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    private function _prepare_users_view() {
        $total=$this->user_model->get_count_user(['status'=> [1,2]]);
        $orderby='user_id';
        $direc='asc';
        $options=array(
            'total'=>$total,
            'perpage'=>$this->PERPAGE,
            'orderby'=>$orderby,
            'direct'=>$direc,
        );
        $content=$this->load->view('admin/users_view',$options,TRUE);
        return $content;
    }

    // Parse emails
    public function whitelistdata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('email_model');
            $options = [
                'order_by' => 'sender',
                'direction' => 'asc',
            ];
            $senders=$this->email_model->get_whitelist($options);
            $mdata['content']=$this->load->view('admin/whitelist_tabledat_view',array('senders'=>$senders),TRUE);
            $this->ajaxResponse($mdata,$error);
        }

    }

    public function whitelist_delete() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $email_id=$this->input->post('email_id');
            $this->load->model('email_model');
            $res=$this->email_model->delete_whitelist($email_id);
            if (!$res) {
                $error='Delete ended unsuccessfully';
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function whitelist_edit() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $email_id=$this->input->post('email_id');
            $this->load->model('email_model');
            $mail_dat=$this->email_model->get_whitelist_data($email_id);
            if (!isset($mail_dat['email_id'])) {
                $error='White List data not found';
            } else {
                $usr_opt=array(
                    'order_by'=>'user_name',
                    'direction'=>'asc',
                    'user_status'=>1,
                );
                $mail_dat['users']=$this->user_model->get_users($usr_opt);
                $mdata['content']=$this->load->view('admin/whitelist_edit_view',$mail_dat,TRUE);
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function whitelist_new() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $usr_opt=array(
                'order_by'=>'user_name',
                'direction'=>'asc',
                'user_status'=>1,
            );
            $users=$this->user_model->get_users($usr_opt);
            $options = [
                'users' => $users,
                'sender' => '',
                'user_id' => 0,
            ];
            $mdata['content']=$this->load->view('admin/whitelist_edit_view', $options,TRUE);
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function whitelist_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $sender=$this->input->post('sender');
            $user_id=$this->input->post('user_id');
            $email_id=$this->input->post('email_id',0);
            $this->load->model('email_model');
            $res=$this->email_model->save_whitelist($sender, $user_id, $email_id);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
            }
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function parsedemailsearch() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $datelog=ifset($postdata,'date','');
            $filtr=ifset($postdata, 'filtr','');
            $search=array();
            if ($datelog!='') {
                $search['datestart']=strtotime($datelog);
                $search['dateend']=strtotime(date("Y-m-d", strtotime($datelog)) . " +1 day");
            }
            if ($filtr!='') {
                $search['filtr']=strtoupper($filtr);
            }

            $this->load->model('email_model');
            $totals=$this->email_model->get_count_parsedemails($search);
            $mdata['totals']=$totals;
            $this->ajaxResponse($mdata,$error);
        }
    }

    public function parsedemaildata() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata = $this->input->post();
            $datelog=ifset($postdata,'date','');
            $filtr=ifset($postdata, 'filtr','');
            $search=array();
            if ($datelog!='') {
                $search['datestart']=strtotime($datelog);
                $search['dateend']=strtotime(date("Y-m-d", strtotime($datelog)) . " +1 day");
            }
            if ($filtr!='') {
                $search['filtr']=strtoupper($filtr);
            }
            $order_by=ifset($postdata,'order_by', 'parsed_date');
            $direct=ifset($postdata,'','desc');
            $pagenum = ifset($postdata,'offset',0);
            $limit = ifset($postdata, 'limit', $this->PERPAGE);
            $offset = $pagenum * $limit;
            $this->load->model('email_model');
            $logdata=$this->email_model->get_parserlogdata($search, $order_by, $direct, $offset, $limit);
            if (count($logdata)==0) {
                $content=$this->load->view('admin/parselog_emptydata_view',array(),TRUE);
            } else {
                $content=$this->load->view('admin/parselog_data_view',array('data'=>$logdata),TRUE);
            }
            $mdata['content']=$content;
            $this->ajaxResponse($mdata, $error);
        }
    }

    private function _prepare_parseremail_view() {
        $this->load->model('email_model');
        $total = $this->email_model->get_count_parsedemails();
        $orderby='parsed_date';
        $direc='desc';
        $options=array(
            'total'=>$total,
            'perpage'=>$this->PERPAGE,
            'orderby'=>$orderby,
            'direct'=>$direc,
        );
        $content=$this->load->view('admin/parsedemails_head_view',$options,TRUE);
        return $content;

    }

    /* Save alert value */
    public function taskalert_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('artproof_model');
            $data=$this->input->post();
            $res=$this->artproof_model->save_taskalert($data);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    /* Save Alert time Value */
    public function taskalerttime_save() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $this->load->model('artproof_model');
            $data=$this->input->post();
            $res=$this->artproof_model->save_taskalerttime($data);
            if ($res['result']==$this->error_result) {
                $error=$res['msg'];
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    private function _prepare_artalert_view() {
        $this->load->model('artproof_model');
        $cfg=$this->artproof_model->get_taskalert_config();
        $content=$this->load->view('admin/taskalert_setup_view',$cfg,TRUE);
        return $content;
    }

}