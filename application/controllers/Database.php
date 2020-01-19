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
            } elseif ($row['item_link']=='#itemcategoryview') {
                $head['styles'][] = array('style' => '/css/database/dbitemcategory_view.css');
                $head['scripts'][] = array('src' => '/js/database/dbitemcategory_view.js');
            } elseif ($row['item_link'] == '#itemsequenceview') {
                $head['styles'][]=array('style'=>'/css/database/dbsequence_view.css');
                $head['scripts'][]=array('src'=>'/js/database/dbsequnece_view.js');
            } elseif ($row['item_link']=='#itemmisinfoview') {
                $head['styles'][]=array('style'=>'/css/database/dbmisinfo_view.css');
                $head['scripts'][]=array('src'=>'/js/database/dbmisinfo_view.js');
            } elseif ($row['item_link']=='#itemprofitview') {
                $head['styles'][]=array('style'=>'/css/database/dbprofit_view.css');
                $head['scripts'][]=array('src'=>'/js/database/dbprofit_view.js');
            } elseif ($row['item_link']=='#itemtemplateview') {
                $head['styles'][]=array('style'=>'/css/database/dbtemplate_view.css');
                $head['scripts'][]=array('src'=>'/js/database/dbtemplate_view.js');
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
                if ($page_name=='categories') {
                    // $page_name_full = 'Categories';
                    $special_content = $this->_prepare_dbpage_content($page_name);
                    $buttons_view = $this->load->view('database/content_viewbuttons_view', [], TRUE);
                    $options = ['buttons_view' => $buttons_view, 'special_content' => $special_content,];
                    $mdata['content'] = $this->load->view('database/category_pagecontent_view', $options, TRUE);
                } else {
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

    // DB Item - Categories
    public function categorydat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by','i.item_number');
            $direct = $this->input->post('direction','asc');
            $pagelock=$this->input->post('pagelock',1);
            $search = $this->input->post('search');
            $vendor_id=$this->input->post('vendor_id','');

            usersession('page_name','categview');
            usersession('curpage', $offset);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('vendor_id', $vendor_id);

            $offset=$offset*$limit;

            /* Get data about categories */
            $options=array();
            $this->load->model('itemcategory_model');
            $item_dat=$this->itemcategory_model->get_item_categories($options,$order_by,$direct,$limit,$offset,$search,$vendor_id);

            $options=array('show_list'=>1);
            $this->load->model('categories_model');
            $categ_list=$this->categories_model->get_categories($options);

            $data=array(
                'item_dat'=>$item_dat,
                'order_by'=>$order_by,
                'direction'=>$direct,
                'offset'=>$offset,
                'categ_list'=>$categ_list,
                'pagelock'=>$pagelock
            );
            $mdata['content'] = $this->load->view('database/dbcategory_table_data_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function updcat() {
        if ($this->isAjax()) {
            $err_str='';
            $mdata=[];
            $el_id=$this->input->post('id');
            $el_val=$this->input->post('value');
            $options=array('show_list'=>1);
            $this->load->model('itemcategory_model');
            $this->load->model('categories_model');
            $categ_list=$this->categories_model->get_categories($options);
            if (substr($el_id,0,1)=='c') {
                /* New Category */
                $item_id=substr($el_id,1,strpos($el_id,'_')-1);
                if ($el_val!=0) {
                    $chk=$this->itemcategory_model->chk_itemcateg($item_id,$el_val);
                    if ($chk!=0) {
                        $err_str='Non unique category';
                        $cat_id=$el_id;
                        $el_val=0;
                    } else {
                        $cat_id=$this->itemcategory_model->ins_categ($item_id,$el_val);
                        $cat_id='ic'.$cat_id.'_'.substr($el_id,1);
                    }
                }
            } else {
                $recid=substr($el_id,2,  strpos($el_id, '_')-1);
                $new_elid=substr($el_id,strpos($el_id,'_')+1);
                if ($el_val==0) {
                    /* Delete Item Category */
                    $cat_id=$this->itemcategory_model->del_categ($recid);
                    $cat_id='c'.$new_elid;
                } else {
                    /* Check an unique  */
                    $res=$this->itemcategory_model->check_updcateg($recid,$el_val);
                    if ($res!=0) {
                        /* Error */
                        $err_str='Non unique category';
                        $cat_id=$el_id;
                        $el_val=$res;
                    } else {
                        $cat_id=$this->itemcategory_model->upd_categ($recid,$el_val);
                        $cat_id=$el_id;
                    }
                }
            }
            /* Load view with el_val */
            $mdata['content'] = $this->load->view('database/dbcategerory_item_view',array('el_id'=>$cat_id,'catval'=>$el_val,'categ_list'=>$categ_list),TRUE);
            $this->ajaxResponse($mdata, $err_str);
        }
        show_404();
    }

    public function upditemcategory() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata = $this->input->post();
            $options=array('show_dropdown'=>array(1,2),'show_homepage'=>1);
            $this->load->model('itemcategory_model');
            $this->load->model('categories_model');
            $categ_list=$this->categories_model->get_categories($options);
            $res = $this->itemcategory_model->update_itemcategory($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $viewoptions = array(
                    'itemcategory_id' => $res['itemcategory_id'],
                    'catval'=>($postdata['item_category']==0 ? '' : $postdata['item_category']),
                    'categ_list'=>$categ_list,
                );
                $mdata['content'] = $this->load->view('database/dbcategerory_item_view',$viewoptions,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // DB Sequence
    public function itemsequence_search() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $error = '';
            $mdata=[];
            $total_options = [];
            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                $total_options['vendor_id']=$postdata['vendor_id'];
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $total_options['search']=$postdata['search'];
            }
            $this->load->model('items_model');
            $mdata['total']=$this->items_model->get_sequence_count($total_options);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_data() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $pagenum = (isset($postdata['offset']) ? $postdata['offset'] : 0);
            $limit = (isset($postdata['limit']) ? $postdata['limit'] : $this->sequence_perpage);
            $options = array(
                'offset' => $pagenum * $limit,
                'limit' => $limit,
            );
            if (isset($postdata['vendor_id']) && !empty($postdata['vendor_id'])) {
                $options['vendor_id']=$postdata['vendor_id'];
            }
            if (isset($postdata['search']) && !empty($postdata['search'])) {
                $options['search']=$postdata['search'];
            }
            // Get items
            $this->load->model('items_model');
            $data = $this->items_model->get_sequence_items($options);
            $label = 'Displaying ';
            if (count($data)==0) {
                $label.='0';
            } else {
                $label.=(($pagenum*$limit)+1).' - '.count($data);
            }
            $label.=' of '.QTYOutput($postdata['total']);
            $options=[
                'items' => $data,
                'itemperrow' => (isset($postdata['itemperrow']) ? $postdata['itemperrow'] : $this->sequence_inrow),
            ];
            $content = $this->load->view('database/dbsequence_page_view', $options, TRUE);
            $mdata=[
                'label' => $label,
                'content' => $content,
            ];
            $error = '';
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_updateitem() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            // Get items
            $this->load->model('items_model');
            $mdata=[];
            $res = $this->items_model->update_item_property($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_updateseq() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            // Get items
            $this->load->model('items_model');
            $mdata=[];
            $res = $this->items_model->update_item_sequence($postdata);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function itemsequence_sort() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $this->load->model('items_model');
            $mdata=[];
            $error='';
            $this->items_model->update_itemsequence_sort($postdata);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // DB Miss Info
    public function misinfodat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $offset=$this->input->post('offset',0);
            $limit=$this->input->post('limit',10);
            $order_by=$this->input->post('order_by','i.item_number');
            $direct = $this->input->post('direction','asc');
            $search = $this->input->post('search');
            $vendor_id=$this->input->post('vendor_id','');
            usersession('page_name','misinfo');
            usersession('curpage', $offset);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('vendor_id', $vendor_id);

            $offset=$offset*$limit;

            /* Get Data about items & missing info */
            $this->load->model('items_model');
            $item_dat=$this->items_model->get_missinginfo(array(),$order_by,$direct,$limit,$offset,$search,$vendor_id);

            $data=array('item_dat'=>$item_dat,'order_by'=>$order_by,'direction'=>$direct,'offset'=>$offset);

            $mdata['content'] = $this->load->view('database/dbmisinfo_table_data_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }
    // DB Item Profit
    function profitdat() {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            $postdata=$this->input->post();

            $pagenum=(isset($postdata['offset']) ? $postdata['offset'] : 0);
            $limit=(isset($postdata['limit']) ? $postdata['limit'] : 10);
            $order_by=(isset($postdata['order_by']) ? $postdata['order_by'] : 'i.item_number');
            $direct=(isset($postdata['direction']) ? $postdata['direction'] : 'asc');
            $search=(isset($postdata['search']) ? $postdata['search'] : '');
            $profitprefs=(isset($postdata['profitprefs']) ? $postdata['profitprefs'] : '');
            $vendor_id=(isset($postdata['vendor_id']) ? $postdata['vendor_id'] : '');

            usersession('page_name','profitview');
            usersession('curpage', $pagenum);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('priority', $profitprefs);
            usersession('vendor_id', $vendor_id);


            $offset=$pagenum*$limit;

            /* Get Data about about items & prices */
            $this->load->model('prices_model');
            $item_dat=$this->prices_model->get_item_profitprefs($order_by,$direct,$limit,$offset,$search,$profitprefs, $vendor_id);

            $data=array('item_dat'=>$item_dat,'order_by'=>$order_by,'direction'=>$direct,'offset'=>$offset);
            if ($this->USR_ROLE=='general') {
                $content = $this->load->view('database/dbprofit_tabledat_general_view',$data, TRUE);
            } else {
                $content = $this->load->view('database/dbprofit_tabledat_view',$data, TRUE);
            }


            $mdata['content']=$content;

            $this->ajaxResponse($mdata,$error);
        }
    }

    // DB Template
    public function templatedat() {
        if ($this->isAjax()) {
            $error='';
            $mdata=array();
            $numpage=$this->input->post('offset');
            $limit=$this->input->post('limit');
            $order_by=$this->input->post('order_by');
            $direct = $this->input->post('direction');
            $search=$this->input->post('search');
            $vendor_id=$this->input->post('vendor_id','');

            usersession('page_name','temlatesview');
            usersession('curpage', $numpage);
            usersession('order_by', $order_by);
            usersession('direction', $direct);
            usersession('search', $search);
            usersession('vendor_id', $vendor_id);

            $offset=$numpage*$limit;

            /* Get Data about about items & categories */
            $this->load->model('items_model');
            $item_dat=$this->items_model->get_items(array(),$order_by,$direct,$limit,$offset,$search,$vendor_id);
            $data=array('item_dat'=>$item_dat,'order_by'=>$order_by,'direction'=>$direct,'offset'=>$offset);
            $mdata['content'] = $this->load->view('database/dbtemplate_table_data_view',$data, TRUE);
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function update_imprint() {
        if ($this->isAjax()) {
            $mdata=[];
            $postdata=$this->input->post();
            $this->load->model('items_model');
            $res=$this->items_model->update_imprint_update($postdata);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
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
            return $content;
        } else {
            $cur_page = 0;
            $order_by = 'item_number';
            $direction = 'asc';
            $search = '';
            $vendor_id = '';
            $current_pagename = usersession('page_name');
            if ($current_pagename == $page_name) {
                $cur_page = usersession('curpage');
                $order_by = usersession('order_by');
                $direction = usersession('direction');
                $search = usersession('search');
                $vendor_id = usersession('vendor_id');
            }
            $this->load->model('items_model');
            $total_rec = $this->items_model->count_searchres($search, $vendor_id);
            $this->load->model('vendors_model');
            if ($page_name=='itemprice') {
                $this->load->model('otherprices_model');
                $priority = '';
                $othervendor = $this->otherprices_model->get_othervendors();
                $othervend = array();
                foreach ($othervendor as $row) {
                    array_push($othervend, $row['other_vendor_id']);
                }
                if ($current_pagename == $page_name) {
                    $priority = usersession('priority');
                    $othervend = usersession('othervend');
                }
                /* Prepare contetn for display */
                /* View Window Legend */
                $mindiff = $this->config->item('price_diff');
                $outvend = array();
                foreach ($othervendor as $row) {
                    if (in_array($row['other_vendor_id'], $othervend)) {
                        $row['chk'] = "checked='checked'";
                    } else {
                        $row['chk'] = '';
                    }
                    $outvend[] = $row;
                }
                $legend_options = array(
                    'mindiff' => $mindiff,
                    'search' => $search,
                    'vendors' => $this->vendors_model->get_vendors(),
                    'priority' => $priority,
                    'othervendor' => $outvend,
                    'vendor' => $vendor_id,
                    'order_by' => $order_by,
                    'direction' => $direction,
                );
                if ($this->USR_ROLE == 'general') {
                    $legend = $this->load->view('database/dbprice_general_legend_view', $legend_options, TRUE);
                } else {
                    $legend = $this->load->view('database/dbprice_legend_view', $legend_options, TRUE);
                }
                $content_dat = array(
                    'legend' => $legend,
                    'order_by' => $order_by,
                    'direction' => $direction,
                    'total_rec' => $total_rec,
                    'cur_page' => $cur_page,
                    'search' => $search,
                    'perpage' => $this->config->item('dbview_perpage'),
                );
                if ($this->USR_ROLE == 'general') {
                    $table_dat = $this->load->view('database/dbprice_generaldata_view', $content_dat, TRUE);
                } else {
                    $table_dat = $this->load->view('database/dbprice_data_view', $content_dat, TRUE);
                }
            } elseif ($page_name=='itemcategory') {
                $legend_options=array(
                    'search'=>$search,
                    'vendors' => $this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbcategory_legend_view', $legend_options, TRUE);
                $content_dat=[
                    'order_by'=>$order_by,
                    'total_rec'=>$total_rec,
                    'direction'=>$direction,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                $table_dat=$this->load->view('database/dbcategory_data_view',$content_dat,TRUE);
            } elseif ($page_name=='itemsequence') {
                $total_options=[];
                if ($vendor_id) {
                    $total_options['vendor_id']=$vendor_id;
                }
                if (!empty($search)) {
                    $total_options['search']=$search;
                }
                $total_rec=$this->items_model->get_sequence_count($total_options);
                /* Prepare contetn for display */
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'vendors'=>$this->vendors_model->get_itemseq_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbsequence_legend_view',$legend_options,TRUE);

                $content_dat=array(
                    'total_rec'=>$total_rec,
                    'cur_page'=>$cur_page,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                );
                $table_dat=$this->load->view('database/dbsequence_data_view',$content_dat,TRUE);
            } elseif ($page_name=='itemmisinfo') {
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'vendors'=>$this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbmisinfo_legend_view',$legend_options,TRUE);

                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$direction,
                    'cur_page'=>$cur_page,
                    'new_available' => 1,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                $table_dat=$this->load->view('database/dbmisinfo_data_view',$content_dat,TRUE);

            } elseif ($page_name=='itemprofit') {
                $priority = '';
                if ($current_pagename == $page_name) {
                    $priority = usersession('priority');
                }
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'priority'=>$priority,
                    'vendors'=>$this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                if ($this->USR_ROLE=='general') {
                    $legend=$this->load->view('database/dbprofit_legendgeneral_view', $legend_options, TRUE);
                } else {
                    $legend=$this->load->view('database/dbprofit_legend_view', $legend_options, TRUE);
                }

                $content['new_available']=1;

                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$direction,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                if ($this->USR_ROLE=='general') {
                    $table_dat=$this->load->view('database/dbprofit_datageneral_view',$content_dat,TRUE);
                } else {
                    $table_dat=$this->load->view('database/dbprofit_data_view',$content_dat,TRUE);
                }
            } elseif ($page_name=='itemtemplates') {
                /* View Window Legend */
                $legend_options=array(
                    'search'=>$search,
                    'vendors'=>$this->vendors_model->get_vendors(),
                    'vendor'=>$vendor_id,
                );
                $legend=$this->load->view('database/dbtemplate_legend_view',$legend_options,TRUE);

                $content_dat=[
                    'total_rec'=>$total_rec,
                    'order_by'=>$order_by,
                    'direction'=>$order_by,
                    'cur_page'=>$cur_page,
                    'search'=>$search,
                    'legend' => $legend,
                    'perpage' => $this->config->item('dbview_perpage'),
                ];
                $table_dat=$this->load->view('database/dbtemplate_data_view',$content_dat,TRUE);
            }
            return $table_dat;
        }
    }

}