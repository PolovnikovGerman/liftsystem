<?php

class Redraw extends MY_Controller
{
    private $pagelink = '/redraw';

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
        $this->load->model('artwork_model');
    }

    public function index()
    {
        $head = [];
        $head['title'] = 'Redraw';
        $brand = $this->menuitems_model->get_current_brand();
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        $content_options = [];
        $content_options['start'] = $this->input->get('start', TRUE);
        $content_options['menu'] = $menu;
        $gmaps = 0;
        foreach ($menu as $row) {
            if ($row['item_link'] == '#redrawlist') {
                $head['styles'][]=array('style'=>'/css/redraw/listredraw.css');
                $head['scripts'][]=array('src'=>'/js/redraw/listredraw.js');
                $content_options['redrawlistview'] = $this->_prepare_redrawlistview($brand); // $brand, $top_menu
            } elseif ($row['item_link']=='#redrawcoplet') {
                $head['styles'][]=array('style'=>'/css/redraw/listcompleted.css');
                $head['scripts'][]=array('src'=>'/js/redraw/listcompleted.js');
                $content_options['completlistview'] = $this->_prepare_listcompleted($brand); // $brand, $top_menu
            }
        }
        // Add main page management
        $head['scripts'][] = array('src' => '/js/redraw/page.js');
        $head['styles'][] = array('style' => '/css/redraw/page.css');

        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // Uploader
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        // File Download
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');
//        // Datepicker
//        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
//        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
//        // Searchable
//        $head['styles'][]=['style' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css"];
//        $head['scripts'][]=['src' => "https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"];
        // Scroll panel
        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'gmaps' => $gmaps,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $content_view = $this->load->view('redraw/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function get_redrawlogos()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $error = '';
            $logos=$this->artwork_model->get_logo_toredraw();
            $total = count($logos);
            if (count($logos)==0) {
                $mdata['content']=$this->load->view('redraw/toredraw_empty_view',[],TRUE);
            } else {
                $mdata['content']=$this->load->view('redraw/toredraw_tabledat_view',['logos'=>$logos, 'total' => $total],TRUE);
            }
            $this->ajaxResponse($mdata, $error);

        }
        show_404();
    }

    public function markvector()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Unknown Logo';
            $postdata = $this->input->post();
            $logo_id=ifset($postdata, 'logo_id',0);
            if ($logo_id) {
                $res=$this->artwork_model->logo_vectored($logo_id, $this->USR_ID);
                $error=$res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function src_file()
    {
        if ($this->isAjax()) {
            $mdata=[];
            $error='Logo Not Found';
            $postdata = $this->input->post();
            $redraw_id = ifset($postdata, 'art_id',0);
            $type = ifset($postdata, 'type','');
            if (!empty($redraw_id) && !empty($type)) {
                // Get Data about row
                $artdata = $this->artwork_model->get_artlocation_details($redraw_id);
                if (ifset($artdata, 'artwork_art_id',0 )==$redraw_id) {
                    $error='File not Exist';
                    if ($type=='source') {
                        $filename=$artdata['logo_src'];
                    } else {
                        $filename=$artdata['logo_vectorized'];
                    }
                    $pathfl=$this->config->item('artwork_logo');
                    $pathsh=$this->config->item('artwork_logo_relative');
                    $fullpath=str_replace($pathsh, $pathfl, $filename);
                    /* check file - exist? */
                    $content=@file_get_contents($fullpath);
                    if ($content) {
                        $filedat = extract_filename($filename);
                        $newfilename= uniq_link(10).'.'.$filedat['ext'];
                        $target=$this->config->item('upload_path_preload').$newfilename;
                        @copy($fullpath, $target);
                        if (!empty($artdata['order_num'])) {
                            $filenameout=$artdata['order_num'].'_'.str_pad($artdata['art_ordnum'], 2, '0' ,STR_PAD_LEFT).'.'.$filedat['ext'];
                        } else {
                            $filenameout=$artdata['proof_num'].'_'.str_pad($artdata['art_ordnum'], 2, '0' ,STR_PAD_LEFT).'.'.$filedat['ext'];
                        }
                        $mdata['filename']=$filenameout;
                        $mdata['url']=$this->config->item('pathpreload').$newfilename;
                        $error = '';
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function openimg()
    {
        $url = $this->input->post('url');
        $filename = $this->input->post('file');
        /* Get extension */
        openfile($url, $filename);
    }

    public function prepare_upload()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $error='Empty Logo';
            $postdata = $this->input->post();
            $logo_id = ifset($postdata, 'logo_id', 0);
            if ($logo_id) {
                $error = '';
                $logo_imageext=array(
                    'jpg', 'jpeg', 'png', 'gif',
                );
                /* Get data about upload file */
                $artdata=$this->artwork_model->get_artlocation_details($logo_id);
                $filename=$artdata['logo_src'];
                $source_view='&nbsp;';
                if ($filename) {
                    $sourcedet = extract_filename($filename);
                    if (in_array($sourcedet['ext'], $logo_imageext)) {
                        $filesource=  str_replace($this->config->item('artwork_logo_relative'), $this->config->item('artwork_logo'), $filename);
                        if (file_exists($filesource)) {
                            list($width, $height, $type, $attr) = getimagesize($filesource);
                            // Rate
                            $viewopt=array(
                                'source'=>$filename,
                            );
                            if ($width >= $height) {
                                if ($width<=200) {
                                    $viewopt['width']=$width;
                                    $viewopt['height']=$height;
                                } else {
                                    $rate=200/$width;
                                    $viewopt['width']=ceil($width*$rate);
                                    $viewopt['height']=ceil($height*$rate);
                                }
                            } else {
                                if ($height<=200) {
                                    $viewopt['width']=$width;
                                    $viewopt['height']=$height;
                                } else {
                                    $rate=200/$height;
                                    $viewopt['width']=ceil($width*$rate);
                                    $viewopt['height']=ceil($height*$rate);
                                }
                            }
                            $source_view=$this->load->view('redraw/viewsource_view',$viewopt, TRUE);
                        }
                    }
                }
                $mdata['content']=$this->load->view('redraw/upload_popup_view',['logo'=>$logo_id, 'source_view'=>$source_view],TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function vector_upload()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $error='';
            // 'filename':responseJSON.filename,'doc_name':fileName
            $filename=$this->input->post('filename');
            $docname=$this->input->post('doc_name');

            $mdata['content']=$this->load->view('redraw/vectorfile_view',array('filename'=>$filename, 'doc_name'=>$docname),TRUE);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_upload()
    {
        if ($this->isAjax()) {
            $mdata=array();
            $postdata = $this->input->post();
            $logo = ifset($postdata, 'logo', 0);
            $file = ifset($postdata, 'file','');

            $res=$this->artwork_model->save_vectorfile($logo, $file, $this->USR_ID);
            $error=$res['msg'];
            if ($res['result']==$this->success_result) {
                $error='';
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_redrawlistview($brand)
    {
        $total=$this->artwork_model->get_toredrawcont();
        $content=$this->load->view('redraw/toredraw_title_view',array('total'=>$total),TRUE);
        return $content;
    }

    private function _prepare_listcompleted($brand)
    {
        $content = '';
        return $content;
    }
}