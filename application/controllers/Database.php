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
            } elseif ($row['item_link'] == '#itempriceview') {
                $head['styles'][] = array('style' => '/css/database/dbprice_view.css');
                $head['scripts'][] = array('src' => '/js/database/dbprice_view.js');
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
                    // $page_name_full = 'Categories';
                    $special_content = $this->_prepare_dbpage_content($page_name);
                    $buttons_view = $this->load->view('database/content_viewbuttons_view', [], TRUE);
                    $options = ['buttons_view' => $buttons_view, 'special_content' => $special_content,];
                    $mdata['content'] = $this->load->view('database/category_pagecontent_view', $options, TRUE);
                } elseif ($page_name=='itemprice') {
                    $mdata['content'] = $this->_prepare_dbpage_content($page_name);
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
                $item_dat=$this->items_model->get_items(array(),$order_by,$direct,$limit,$offset,$search,$vendor_id);
                $data=array('item_dat'=>$item_dat,'offset'=>$offset);
                $mdata['content'] = $this->load->view('database/dbprice_tabledat_general_view',$data, TRUE);
            } else {
                if (count($othervend)==0) {
                    $error='Empty List of Compare price';
                } else {
                    $this->load->model('otherprices_model');
                    if (count($othervend)==4) {
                        $item_dat=$this->otherprices_model->get_compared_prices($order_by, $direct, $limit, $offset, $search, $compareprefs, $vendor_id);
                    } else {
                        $item_dat=$this->otherprices_model->get_compared_pricelimit($order_by, $direct, $limit, $offset, $search, $compareprefs, $vendor_id, $othervend);
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
            $this->load->model('items_model');
            $num_rec=$this->items_model->count_searchres($search, $vendor_id);
            $mdata['result']=$num_rec;
            $this->ajaxResponse($mdata,$error);
        }
        show_404();
    }




    // Prepare pages
    private function _prepare_dbpage_content($page_name)
    {
        if ($page_name=='categories') {
            $this->load->model('categories_model');
            $categories = $this->categories_model->get_categories_list();
            // List view
            $content = $this->load->view('database/categorylist_view',['categories'=>$categories,'current_category'=>-1], TRUE);
        } elseif ($page_name=='itemprice') {
            $current_pagename=usersession('page_name');
            $this->load->model('otherprices_model');
            $othervendor=$this->otherprices_model->get_othervendors();
            $cur_page=0;
            $order_by='item_number';
            $direction='asc';
            $search='';
            $priority='';
            $vendor_id='';
            $othervend=array();
            foreach ($othervendor as $row) {
                array_push($othervend, $row['other_vendor_id']);
            }
            if ($current_pagename == $page_name) {
                $cur_page=usersession('curpage');
                $order_by=usersession('order_by');
                $direction=usersession('direction');
                $search=usersession('search');
                $priority=usersession('priority');
                $othervend=usersession('othervend');
                $vendor_id=usersession('vendor_id');
            }
            $this->load->model('items_model');
            $total_rec=$this->items_model->count_searchres($search, $vendor_id);
            /* Prepare contetn for display */
            $content=array();
            /* View Window Legend */
            $mindiff=$this->config->item('price_diff');
            $outvend=array();
            foreach ($othervendor as $row) {
                if (in_array($row['other_vendor_id'],$othervend)) {
                    $row['chk']="checked='checked'";
                } else {
                    $row['chk']='';
                }
                $outvend[]=$row;
            }
            $this->load->model('vendors_model');
            $legend_options=array(
                'mindiff'=>$mindiff,
                'search'=>$search,
                'vendors'=>$this->vendors_model->get_vendors(),
                'priority'=>$priority,
                'othervendor'=>$outvend,
                'vendor'=>$vendor_id,
                'order_by'=>$order_by,
                'direction'=>$direction,
            );
            if ($this->USR_ROLE=='general') {
                $legend=$this->load->view('database/dbprice_general_legend_view', $legend_options, TRUE);
            } else {
                $legend=$this->load->view('database/dbprice_legend_view', $legend_options, TRUE);
            }
            $content_dat=array(
                'legend' => $legend,
                'order_by'=>$order_by,
                'direction'=>$direction,
                'total_rec'=>$total_rec,
                'cur_page'=>$cur_page,
                'search'=>$search,
                'perpage' => $this->config->item('dbview_perpage'),
            );
            if ($this->USR_ROLE=='general') {
                $table_dat=$this->load->view('database/dbprice_generaldata_view',$content_dat,TRUE);
            } else {
                $table_dat=$this->load->view('database/dbprice_data_view',$content_dat,TRUE);
            }

            return $table_dat;
        }
        return $content;
    }

}