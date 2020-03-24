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
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);

        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link']=='#usersview') {
                $head['styles'][]=array('style'=>'/css/admin/usersview.css');
                $head['scripts'][]=array('src'=>'/js/admin/usersview.js');
                $content_options['usersview'] = $this->_prepare_users_view();
            }
        }

        $content_options['menu']=$menu;
        $content_view = $this->load->view('admin/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][]=array('src'=>'/js/admin/page.js');
        $head['styles'][] = array('style'=> '/css/admin/page.css');
        // Utils
        $head['scripts'][]=array('src'=>'/js/admin/jQuery.Tree.js');
        $head['styles'][]=array('style'=>'/css/admin/jQuery.Tree.css');

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
                $wpages=$this->tree(null, 0, $user_id);
                $pagepermiss=$this->load->view('admin/webpage_tree_view',array('pages'=>$wpages),TRUE);
                $iprestricts = $this->load->view('admin/user_iprestrict_view',['userip'=>$userip], TRUE);
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
                ];

                $mdata['content'] = $this->load->view('admin/user_details_view', $options, TRUE);
                $mdata['footer'] = $this->load->view('admin/user_savedata_view',[], TRUE);
            }
        }
        $this->ajaxResponse($mdata, $error);
    }

    /* Recursion Function */
    private function tree($pid, $lvl, $user_id){
        $wpages=$this->menuitems_model->get_webpage($pid, $user_id);
        $out=array();
        foreach ($wpages as $wrow){
            $lvl++;
            $label=$wrow['item_name'];
            // 'brand_access' => $wrow['brand_access'],
            // 'brand' => $wrow['brand'],

            if ($wrow['brand_access']!=='' && $wrow['brand_access']!=='NONE') {
                $label.='&nbsp;'.$this->_sitemenu_useraccess($wrow, $user_id);
            }
            $id=$wrow['menu_item_id'];
            $value=($wrow['permission_type']=='' ? 0 : $wrow['permission_type']);
            $elem=$this->tree($id, $lvl--,$user_id);
            if ($elem==array()) {
                $elem=$wrow['permission_type'];
            }
            $out[]=array(
                'label'=>$label,
                'id'=>$id,
                'element'=>$elem,
                'value'=>$value,
                'brand' => $wrow['brand'],
            );
        }
        return $out;
    }

    private function _sitemenu_useraccess($wrow, $user_id) {
        if ($wrow['brand_access']=='SITES') {

        } else {
            $brands = [];
            $brands[] = ['key' => '', 'value' => 'None'];
            $brands[] = ['key' => 'ALL', 'value' => 'All'];
            $brands[] = ['key' => 'SB', 'value' => 'Stressball.com only'];
            $brands[] = ['key' => 'BT', 'value' => 'Bluetrack only'];
        }
        $options=array(
            'brand'=>$wrow['brand'],
            'brands'=>$brands,
            'menu_item' => $wrow['menu_item_id'],
        );
        return $this->load->view('admin/sitemenu_access_view', $options, TRUE);

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
                $newval = ifset($postdata,'newval', 0);
                $res = $this->menuitems_model->update_userpage_permission($session_data, $menuitem, $newval, $session_id);
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


}