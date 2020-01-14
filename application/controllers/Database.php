<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Database extends MY_Controller
{

    private $pagelink = '/database';


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
        $head['title'] = 'Database';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link'] == '#categoryview') {
                // Custom shaped
                $head['styles'][] = array('style' => '/css/database/categories.css');
                $head['scripts'][] = array('src' => '/js/database/categories.js');
            }
        }
        $content_options['menu'] = $menu;
        $content_view = $this->load->view('database/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/database/page.js');
        $head['styles'][] = array('style' => '/css/database/databasepage.css');
        // Utils

        $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['scripts'][] = array('src' => '/js/adminpage/fileuploader.js');
        $head['styles'][] = array('style' => '/css/page_view/fileuploader.css');
        $head['scripts'][] = array('src' => '/js/fancybox/jquery.fancybox.js');
        $head['styles'][] = array('style' => '/css/fancybox/jquery.fancybox.css');
        $head['scripts'][] = array('src' => '/js/adminpage/jquery.sortable.js');
        $options = ['title' => $head['title'], 'user_id' => $this->USR_ID, 'user_name' => $this->USER_NAME, 'activelnk' => $this->pagelink, 'styles' => $head['styles'], 'scripts' => $head['scripts'],];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function get_content_view()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $page_name = (isset($postdata['page_name']) ? $postdata['page_name'] : '');
            $mdata=array();
            $error='Empty Page Name';
            if (!empty($page_name)) {
                $error = '';
                // $this->load->model('staticpages_model');
                // $meta = $this->staticpages_model->get_metadata($postdata['page_name']);
                // $meta_view = $this->load->view('contents/metadata_view', $meta, TRUE);
                $special_content = '';
                if ($page_name=='categories') {
                    $page_name_full = 'Categories';
                    $special_content = $this->_prepare_dbpage_content($page_name);
                    $buttons_view = $this->load->view('database/content_viewbuttons_view', [], TRUE);
                    $options = ['buttons_view' => $buttons_view, 'special_content' => $special_content,];
                    $mdata['content'] = $this->load->view('database/category_pagecontent_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
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



    // Prepare pages
    private function _prepare_dbpage_content($page_name)
    {
        if ($page_name=='categories') {
            $this->load->model('categories_model');
            $categories = $this->categories_model->get_categories_list();
            // List view
            $content = $this->load->view('database/categorylist_view',['categories'=>$categories,'current_category'=>-1], TRUE);
        }
        return $content;
    }

}