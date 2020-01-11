<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends MY_Controller
{

    private $pagelink = '/content';


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
        $head['title'] = 'Content';
        $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink);
        $content_options = [];
        foreach ($menu as $row) {
            if ($row['item_link'] == '#customshappedview') {
                // Custom shaped
                $head['styles'][]=array('style'=>'/css/content/customshape_page.css');
                $head['scripts'][]=array('src'=>'/js/content/custom_shaped.js');
            }
        }
        $content_options['menu'] = $menu;
        $content_view = $this->load->view('content/page_view', $content_options, TRUE);
        // Add main page management
        $head['scripts'][] = array('src' => '/js/content/page.js');
        $head['styles'][] = array('style' => '/css/content/contentpage.css');
        // Utils
        // $head['scripts'][]=array('src'=>'/js/jquery.bt.js');
        // $head['styles'][] = array('style' => '/css/page_view/pagination_shop.css');
        // $head['scripts'][] = array('src' => '/js/adminpage/jquery.mypagination.js');
        $head['scripts'][]=array('src'=>'/js/adminpage/fileuploader.js');
        $head['styles'][]=array('style'=>'/css/page_view/fileuploader.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/uEditor.js');
        $head['styles'][]=array('style'=>'/css/page_view/uEditor.css');
        $head['scripts'][]=array('src'=>'/js/fancybox/jquery.fancybox.js');
        $head['styles'][]=array('style'=>'/css/fancybox/jquery.fancybox.css');

        // Searchable
        // $head['scripts'][]=array('src'=>'/js/adminpage/jquery.searchabledropdown-1.0.8.min.js');
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
            $mdata = array();
            $error = 'Empty Page Name';
            if (!empty($page_name)) {
                $error = '';
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($postdata['page_name']);
                $meta_view = $this->load->view('content/metadata_view', $meta, TRUE);
                $special_content = '';
                if ($page_name == 'home') {
                    $page_name_full = 'Homepage';
                } elseif ($page_name == 'custom') {
                    $page_name_full = 'Custom Shaped Stress Balls';
                    $special_content = $this->_prepare_custom_content($page_name);
                } elseif ($page_name == 'faq') {
                    $page_name_full = 'Frequently Asked Questions';
                    $special_content = $this->_prepare_custom_content($page_name);
                } elseif ($page_name == 'terms') {
                    $page_name_full = 'Terms & Polices';
                    $special_content = $this->_prepare_custom_content($page_name);
                } elseif ($page_name == 'about') {
                    $page_name_full = 'About Us';
                    $special_content = $this->_prepare_custom_content($page_name);
                } elseif ($page_name == 'contactus') {
                    $page_name_full = 'Contact Us';
                    $special_content = $this->_prepare_custom_content($page_name);
                } elseif ($page_name == 'categories') {
                    $page_name_full = 'Categories';
                    $special_content = $this->_prepare_custom_content($page_name);
                } elseif ($page_name == 'extraservice') {
                    $page_name_full = 'Services';
                    $special_content = $this->_prepare_custom_content($page_name);
                }
                $button_options = ['page' => $page_name, 'content_name' => $page_name_full];
                $buttons_view = $this->load->view('content/content_viewbuttons_view', $button_options, TRUE);
                $options = ['meta_view' => $meta_view, 'buttons_view' => $buttons_view, 'special_content' => $special_content,];
                $mdata['content'] = $this->load->view('content/staticpage_view', $options, TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function edit_customcontent() {
        if ($this->isAjax()) {
            $page_name = 'custom';
            $page_name_full = 'Custom Shaped Stress Balls';
            $session_id = uniq_link(15);
            $this->load->model('staticpages_model');
            $meta = $this->staticpages_model->get_metadata($page_name);
            $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
            $special_content = $this->_prepare_custom_content($page_name, 1, $session_id);
            $session_data = usersession($session_id);
            $session_data['meta'] = $meta;
            $session_data['deleted'] = []; // type , id
            usersession($session_id, $session_data);
            $button_options = ['page'=>'custom', 'content_name' => $page_name_full, 'session'=> $session_id];
            $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
            $options = [
                'meta_view' => $meta_view,
                'buttons_view' => $buttons_view,
                'special_content' => $special_content,
            ];
            $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
            $this->ajaxResponse($mdata, '');
        }
        show_404();
    }

    private function _prepare_custom_content($page_name, $edit_mode=0, $session ='') {
        $this->load->model('staticpages_model');
        $data = $this->staticpages_model->get_page_inner_content($page_name);
        $content = '';
        if ($page_name == 'custom') {
            $galleries = $this->staticpages_model->get_custom_galleries();
            $case_study = $this->staticpages_model->get_case_study();
            // Get data about categories, examples
            $gallery_options = [
                'galleries' => $galleries,
                'maxitems' => $this->config->item('max_slider_galleryitems'),
            ];
            if ($edit_mode == 0) {
                $gallery_view = $this->load->view('content/custom_galleries_view', $gallery_options, TRUE);
            } else {
                $editgallery = $this->load->view('content/custom_galleryitems_edit', $gallery_options, TRUE);
                $gallery_view = $this->load->view('content/custom_galleries_edit', ['gallery_view'=>$editgallery], TRUE);
            }
            // Get data about Case Study
            $casestudy_options = [
                'casestudy' => $case_study,
                'maxitems' => $this->config->item('max_slider_casestudy'),
            ];
            if ($edit_mode == 0) {
                $casestudy_view = $this->load->view('content/custom_casestudy_view', $casestudy_options, TRUE);
            } else {
                $editcasestudy = $this->load->view('content/custom_casestudyitems_edit', $casestudy_options, TRUE);
                $casestudy_view = $this->load->view('content/custom_casestudy_edit', ['casestudy_view'=>$editcasestudy], TRUE);
            }
            $page_options = [
                'data' => $data,
                'gallery_view' => $gallery_view,
                'casestudy_view' => $casestudy_view,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode == 0) {
                $content = $this->load->view('content/customshaped_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/customshaped_custom_edit', $page_options, TRUE);
                // Save data to session
                $session_data = ['data' => $data, 'galleries' => $galleries, 'case_study' => $case_study];
                usersession($session, $session_data);
            }
        } elseif ($page_name=='faq') {
            $faq_sections = $this->staticpages_model->get_faq_sections();
            $idx = 0;
            $faq_content = '';
            foreach ($faq_sections as $row) {
                $quest = $this->staticpages_model->get_faq(['faq_section'=>$row['faq_section']]);
                $faq_sections[$idx]['questions'] = $quest;
                $faq_sections[$idx]['title'] = ucfirst($row['faq_section']).' Questions';
                if ($edit_mode==0) {
                    $faq_content.=$this->load->view('contents/faqsection_view', ['faq'=>$faq_sections[$idx]], TRUE);
                } else {
                    $faq_items = $this->load->view('contents/faqsection_item_edit',['faq'=>$faq_sections[$idx],'faq_section'=>$faq_sections[$idx]['faq_section']],TRUE);
                    $faq_content.=$this->load->view('contents/faqsection_edit', ['faq'=>$faq_sections[$idx],'details'=>$faq_items], TRUE);
                }
                $idx++;
            }
            $page_options = [
                'faq_sections' => $faq_content,
                'data' => $data,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('contents/faq_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('contents/faq_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data, 'faq_sections' => $faq_sections];
                $this->func->session($session, $session_data);
            }
        } elseif ($page_name=='terms') {
            $terms=$this->staticpages_model->get_terms();
            if ($edit_mode==0) {
                $terms_view = $this->load->view('contents/terms_data_view', ['terms'=>$terms], TRUE);
            } else {
                $terms_view = $this->load->view('contents/terms_data_edit', ['terms'=>$terms], TRUE);
            }
            $page_options = [
                'data' => $data,
                'terms' => $terms_view,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('contents/terms_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('contents/terms_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data, 'terms' => $terms];
                $this->func->session($session, $session_data);
            }
        } elseif ($page_name=='about') {
            $address = $this->staticpages_model->get_page_inner_content('address');
            $page_options = [
                'data' => $data,
                'address' => $address,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('contents/aboutus_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('contents/aboutus_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data,'address'=>$address];
                $this->func->session($session, $session_data);
            }
        } elseif ($page_name=='contactus') {
            $address = $this->staticpages_model->get_page_inner_content('address');
            $page_options = [
                'data' => $data,
                'address' => $address,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('contents/contactus_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('contents/contactus_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data,'address'=>$address];
                $this->func->session($session, $session_data);
            }
        } elseif ($page_name=='extraservice') {
            $page_options = [
                'data' => $data,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('contents/extraservices_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('contents/extraservices_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data,];
                $this->func->session($session, $session_data);
            }

        }
        return $content;
    }


}

