<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends MY_Controller
{

    private $pagelink = '/content';
    private $faq_sections = [
        "artwork",
        "general",
        "ordering",
        "product",
    ];


    public function __construct()
    {
        parent::__construct();
    }


    public function get_content_view()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $page_name = ifset($postdata,'page_name', '');
            $brand = ifset($postdata,'brand','');
            $mdata = array();
            $error = 'Empty Page Name';
            if (!empty($page_name) && !empty($brand)) {
                $error = '';
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name, $brand);
                $meta_view = $this->load->view('content/metadata_view', $meta, TRUE);
                $special_content = '';
                if ($page_name == 'home') {
                    $page_name_full = 'Homepage';
                    $pagelink = 'homeview';
                } elseif ($page_name == 'custom') {
                    $page_name_full = 'Custom Shaped Stress Balls';
                    $pagelink = 'customshappedview';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                } elseif ($page_name == 'faq') {
                    $page_name_full = 'Frequently Asked Questions';
                    $pagelink = 'faqview';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                } elseif ($page_name == 'terms') {
                    $page_name_full = 'Terms & Polices';
                    $pagelink = 'termsview';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                } elseif ($page_name == 'about') {
                    $page_name_full = 'About Us';
                    $pagelink = 'aboutusview';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                } elseif ($page_name == 'contactus') {
                    $page_name_full = 'Contact Us';
                    $pagelink = 'contactusview';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                } elseif ($page_name == 'categories') {
                    $page_name_full = 'Categories';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                } elseif ($page_name == 'extraservice') {
                    $page_name_full = 'Services';
                    $pagelink = 'serviceview';
                    $special_content = $this->_prepare_custom_content($page_name, $brand);
                }
                $button_options = ['page' => $pagelink, 'content_name' => $page_name_full, 'brand' => $brand];
                $buttons_view = $this->load->view('content/content_viewbuttons_view', $button_options, TRUE);
                $buttons_mobile = $this->load->view('content/mobile_viewbuttons_view', $button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view, // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,];
                $mdata['content'] = $this->load->view('content/staticpage_view', $options, TRUE);
                $mdata['buttons'] = $buttons_view;
                $mdata['mobilebuttons'] = $buttons_mobile;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function edit_customcontent() {
        if ($this->isAjax()) {
            $postdata=$this->input->post();
            $brand=ifset($postdata,'brand');
            $mdata = [];
            $error = 'Empty Brand';
            if (!empty($brand)) {
                $error = '';
                $page_name = 'custom';
                $page_name_full = 'Custom Shaped Stress Balls';
                $session_id = uniq_link(15);
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name, $brand);
                $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
                $special_content = $this->_prepare_custom_content($page_name, $brand, 1, $session_id);
                $session_data = usersession($session_id);
                $session_data['meta'] = $meta;
                $session_data['deleted'] = []; // type , id
                usersession($session_id, $session_data);
                $button_options = ['page'=>'custom', 'content_name' => $page_name_full, 'session'=> $session_id];
                $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view,
                    // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,
                ];
                $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
                $mdata['buttons'] = $buttons_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_customparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->update_customshaped_param($session_data, $postdata, $session_id, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if ($postdata['type']=='data' && $postdata['field']=='custom_mainimage' && !empty($postdata['newval'])) {
                        $mdata['content']=$this->load->view('content/custom_mainimage_view',['src'=>$postdata['newval']], TRUE);
                    }
                    if ($postdata['type']=='data' && $postdata['field']=='custom_homepageimage'&& !empty($postdata['newval'])) {
                        $mdata['content']=$this->load->view('content/custom_homepageimage_view',['src'=>$postdata['newval']], TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_imageupload_custom() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_customshape_imageupload($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error='';
                    if ($postdata['imagetype']=='custom_mainimage') {
                        $mdata['content']=$this->load->view('content/custom_mainimage_view', ['src' => $res['custom_mainimage']], TRUE);
                        $mdata['custom_mainimage'] = 1;
                    } elseif ($postdata['imagetype']=='custom_homepageimage') {
                        $mdata['content']=$this->load->view('content/custom_homepageimage_view',['src' => $res['custom_homepageimage']], TRUE);
                        $mdata['custom_homepageimage'] = 1;
                    } elseif ($postdata['imagetype']=='gallery_image') {
                        $mdata['gallery'] = 1;
                        $gallery_options = [
                            'galleries' => $res['galleries'],
                            'maxitems' => $this->config->item('max_slider_galleryitems'),
                        ];
                        $mdata['content'] = $this->load->view('content/custom_galleries_edit', $gallery_options, TRUE);
                    } elseif ($postdata['imagetype']=='casestudy_image') {
                        $mdata['casestudy'] = 1;
                        $casestudy_options = [
                            'casestudy' => $res['case_study'],
                            'maxitems' => $this->config->item('max_slider_casestudy'),
                        ];
                        $mdata['content'] = $this->load->view('content/custom_casestudyitems_edit', $casestudy_options, TRUE);
                    } elseif ($postdata['imagetype']=='galleryitem_image') {
                        $mdata['galleryitem'] = 1;
                        $item_options = [
                            'galleryitems' => $res['items'],
                        ];
                        $mdata['content'] = $this->load->view('content/custom_galleryitems_edit', $item_options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function remove_customgalleryimage() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->remove_customgalleryimage($session_data, $postdata, $session_id);
                if ($res['result']==$this->success_result) {
                    $error='';
                    $gallery_options = [
                        'galleries' => $res['galleries'],
                        'maxitems' => $this->config->item('max_slider_galleryitems'),
                    ];
                    $mdata['content'] = $this->load->view('content/custom_galleries_edit', $gallery_options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function add_customgallery() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->add_customgallery($session_data, $session_id);
                if ($res['result']==$this->success_result) {
                    $error='';
                    $gallery_options = [
                        'galleries' => $res['galleries'],
                        'maxitems' => $this->config->item('max_slider_galleryitems'),
                    ];
                    $mdata['content'] = $this->load->view('content/custom_galleries_edit', $gallery_options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function remove_customgallery() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata=$this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->remove_customgallery($session_data, $postdata, $session_id);
                if ($res['result']==$this->success_result) {
                    $error='';
                    $gallery_options = [
                        'galleries' => $res['galleries'],
                        'maxitems' => $this->config->item('max_slider_galleryitems'),
                    ];
                    $mdata['content'] = $this->load->view('content/custom_galleries_edit', $gallery_options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function remove_customgalleryitem() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Edit session lost. Please, reload page';
            $postdata=$this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->remove_customgalleryitems($session_data, $postdata, $session_id);
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $galleryitems = $res['galleryitems'];
                    $mdata['content'] = $this->load->view('content/custom_galleryitems_edit', ['galleryitems' => $galleryitems], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function remove_customcasestudy() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata=$this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->remove_customcasestudy($session_data, $postdata, $session_id);
                if ($res['result']==$this->success_result) {
                    $error='';
                    $casestudy_options = [
                        'casestudy' => $res['case_study'],
                        'maxitems' => $this->config->item('max_slider_casestudy'),
                    ];
                    $mdata['content'] = $this->load->view('content/custom_casestudyitems_edit', $casestudy_options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function save_customcontent() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'custom');
            $session_data = usersession($session_id);
            $brand = ifset($postdata,'brand');
            if (!empty($session_data) && !empty($brand)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_customshaped($session_data, $postdata, $session_id, $brand, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function prepare_customshaped_preview() {
        $postdata = $this->input->get();
        $session_id = (isset($postdata['version']) ? $postdata['version'] : 'custom');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $options = [
                'meta' => $session_data['meta'],
                'contents' => $session_data['data'],
                'galleries' => $session_data['galleries'],
                'case_study' => $session_data['case_study'],
                'address' => $this->config->item('address'),
                'header' => $this->load->view('contents_preview/preview_header',['address'=>$this->config->item('address')],TRUE),
                'footer' => $this->load->view('contents_preview/preview_footer',['address'=>$this->config->item('address')], TRUE),
            ];
            echo $this->load->view('contents_preview/custom_shaped_view', $options, TRUE);
        } else {
            echo '';
        }
        die();
    }



    public function edit_servicecontent() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            $error = 'Empty Brand';
            $mdata=[];
            if (!empty($brand)) {
                $error = '';
                $page_name = 'extraservice';
                $page_name_full = 'Services';
                $session_id = uniq_link(15);
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name, $brand);
                $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
                $special_content = $this->_prepare_custom_content($page_name, $brand, 1, $session_id);
                $session_data = usersession($session_id);
                $session_data['meta'] = $meta;
                $session_data['deleted'] = []; // type , id
                usersession($session_id, $session_data);
                $button_options = ['page'=>'extraservice', 'content_name' => $page_name_full, 'session'=> $session_id];
                $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view,
                    // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,
                ];
                $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
                $mdata['buttons'] = $buttons_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function change_serviceparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'service');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->change_serviceparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if ($postdata['field']=='service_mainimage' && !empty($postdata['newval'])) {
                        $options=[
                            'image' => $postdata['newval'],
                        ];
                        $mdata['content']=$this->load->view('content/service_mainimage_edit', $options,TRUE);
                    }
                    if (($postdata['field']=='service_image1' || $postdata['field']=='service_image2' || $postdata['field']=='service_image3'
                            || $postdata['field']=='service_image4' || $postdata['field']=='service_image5' || $postdata['field']=='service_image6'
                            || $postdata['field']=='service_image7' || $postdata['field']=='service_image8') && !empty($postdata['newval'])) {
                        $options=[
                            'image' => $postdata['newval'],
                            'service' => $postdata['service'],
                        ];
                        $mdata['content']=$this->load->view('content/service_image_edit', $options,TRUE);

                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_servicepagecontent() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'service');
            $session_data = usersession($session_id);
            $brand = ifset($postdata,'brand');
            if (!empty($session_data) && !empty($brand)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_extraservice($session_data,  $session_id, $brand, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function prepare_service_preview() {
        $postdata = $this->input->get();
        $session_id = (isset($postdata['version']) ? $postdata['version'] : 'service');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $options = [
                'meta' => $session_data['meta'],
                'contents' => $session_data['data'],
                'header' => $this->load->view('contents_preview/preview_header',['address'=>$this->config->item('address')],TRUE),
                'footer' => $this->load->view('contents_preview/preview_footer',['address'=>$this->config->item('address')], TRUE),
            ];
            echo $this->load->view('contents_preview/service_page_view', $options, TRUE);
        } else {
            echo '';
        }
        die();
    }


    public function edit_aboutcontent() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            $error = 'Empty Brand';
            if (!empty($brand)) {
                $error = '';
                $page_name = 'about';
                $page_name_full = 'About Us';
                $session_id = uniq_link(15);
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name,$brand);
                $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
                $special_content = $this->_prepare_custom_content($page_name, $brand, 1, $session_id);
                $session_data = usersession($session_id);
                $session_data['meta'] = $meta;
                $session_data['deleted'] = []; // type , id
                usersession($session_id, $session_data);
                $button_options = ['page'=>'about', 'content_name' => $page_name_full, 'session'=> $session_id];
                $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view,
                    // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,
                ];
                $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
                $mdata['buttons'] = $buttons_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_aboutparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'aboutpage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->update_aboutparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function save_aboutimage() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'aboutpage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->update_aboutparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    // Build content
                    if ($postdata['type']=='main_image') {
                        $options = [
                            'imagesrc' => $postdata['newval'],
                        ];
                        $mdata['content'] = $this->load->view('content/aboutus_mainimage_edit', $options, TRUE);
                    } else {
                        $options = [
                            'imagenum' => $postdata['imageorder'],
                            'imagesrc' => $postdata['imagesrc'],
                        ];
                        $mdata['content'] = $this->load->view('content/aboutus_affilateimage_edit', $options, TRUE);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function save_aboutpagecontent() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'aboutus');
            $session_data = usersession($session_id);
            $brand = ifset($postdata, 'brand');
            if (!empty($session_data) && !empty($brand)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_aboutus($session_data,  $session_id, $brand, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function prepare_aboutus_preview() {
        $postdata = $this->input->get();
        $session_id = (isset($postdata['version']) ? $postdata['version'] : 'about');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $options = [
                'meta' => $session_data['meta'],
                'contents' => $session_data['data'],
                'address' => $session_data['address'],
                'header' => $this->load->view('contents_preview/preview_header',['address'=>$this->config->item('address')],TRUE),
                'footer' => $this->load->view('contents_preview/preview_footer',['address'=>$this->config->item('address')], TRUE),
            ];
            echo $this->load->view('contents_preview/aboutus_page_view', $options, TRUE);
        } else {
            echo '';
        }
        die();
    }

    public function edit_faqcontent() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $brand = ifset($postdata,'brand');
            $error = 'Empty Brand';
            if (!empty($brand)) {
                $error = '';
                $page_name = 'faq';
                $page_name_full = 'Frequently Asked Questions';
                $session_id = uniq_link(15);
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name, $brand);
                $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
                $special_content = $this->_prepare_custom_content($page_name, $brand, 1, $session_id);
                $session_data = usersession($session_id);
                $session_data['meta'] = $meta;
                $session_data['deleted'] = []; // type , id
                usersession($session_id, $session_data);
                $button_options = ['page'=>'faq', 'content_name' => $page_name_full, 'session'=> $session_id];
                $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view,
                    // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,
                ];
                $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
                $mdata['buttons'] = $buttons_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_faqparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'faqpage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->update_faqparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function add_faqquestion() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'faqpage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->add_faqquestion($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options=[
                        'faq'=>$res['faq_section'],
                        'faq_section'=>$postdata['faq_section'],
                    ];
                    $mdata['content'] = $this->load->view('content/faqsection_item_edit',$options,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function remove_faqquestion() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'faqpage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->remove_faqquestion($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $options=[
                        'faq'=>$res['faq_section'],
                        'faq_section'=>$postdata['faq_section'],
                    ];
                    $mdata['content'] = $this->load->view('content/faqsection_item_edit',$options,TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function save_faqpagecontent() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'faqpage');
            $session_data = usersession($session_id);
            $brand = ifset($postdata, 'brand');
            if (!empty($session_data) && !empty($brand)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_faqpagecontent($session_data, $session_id, $brand, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function prepare_faqpage_preview() {
        $postdata = $this->input->get();
        $session_id = (isset($postdata['version']) ? $postdata['version'] : 'custom');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $faq=[];
            $faq_sections = $session_data['faq_sections'];
            foreach ($faq_sections as $section) {
                $questions=$section['questions'];
                foreach ($questions as $row) {
                    $faq[$section['faq_section']][]=[
                        'faq_id'=>$row['faq_id'],
                        'faq_quest' => $row['faq_quest'],
                        'faq_answ' => $row['faq_answ'],
                    ];
                }
            }
            $options = [
                'meta' => $session_data['meta'],
                'contents' => $session_data['data'],
                'faq' => $faq,
                'version' => $session_id,
                'address'=>$this->config->item('address'),
                'header' => $this->load->view('contents_preview/preview_header',['address'=>$this->config->item('address')],TRUE),
                'footer' => $this->load->view('contents_preview/preview_footer',['address'=>$this->config->item('address')], TRUE),
            ];

            echo $this->load->view('contents_preview/faqpage_view', $options, TRUE);
        } else {
            echo '';
        }
        die();
    }

    public function faq_preview_details() {
        $faq_id=$this->input->get('id');
        $session_id = $this->input->get('ver');
        $section = $this->input->get('part');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $data=['faq_quest'=> 'Not Found', 'faq_answ'=> 'Not Found'];
            $faq_sections = $session_data['faq_sections'];
            $found=0;
            foreach ($faq_sections as $sections) {
                if ($sections['faq_section']==$section) {
                    $questions = $sections['questions'];
                    foreach ($questions as $row) {
                        if ($row['faq_id']==$faq_id) {
                            $data['faq_quest']=$row['faq_quest'];
                            $data['faq_answ']=$row['faq_answ'];
                        }
                    }
                }
            }
            $content=$this->load->view('contents_preview/faq_details_view',$data, TRUE);
            echo $content;
            die();
        }
        show_404();
    }


    public function edit_contactus() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            $error = 'Empty Brand';
            $mdata=[];
            if (!empty($brand)) {
                $error = '';
                $page_name = 'contactus';
                $page_name_full = 'Contact Us';
                $session_id = uniq_link(15);
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name, $brand);
                $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
                $special_content = $this->_prepare_custom_content($page_name, $brand, 1, $session_id);
                $session_data = usersession($session_id);
                $session_data['meta'] = $meta;
                $session_data['deleted'] = []; // type , id
                usersession($session_id, $session_data);
                $button_options = ['page'=> $page_name, 'content_name' => $page_name_full, 'session'=> $session_id];
                $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view,
                    // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,
                ];
                $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
                $mdata['buttons'] = $buttons_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_contactparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'contactus');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->update_contactusparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_contactcontent() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'contactus');
            $session_data = usersession($session_id);
            $brand = ifset($postdata,'brand');
            if (!empty($session_data) && !empty($brand)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_contactus($session_data,  $session_id, $brand, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function prepare_contactus_preview() {
        $postdata = $this->input->get();
        $session_id = (isset($postdata['version']) ? $postdata['version'] : 'contactus');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $options = [
                'meta' => $session_data['meta'],
                'contents' => $session_data['data'],
                'address' => $session_data['address'],
                'math_captcha_question' => $session_data['data']['contact_captchtitle'].' 7 + 3 = ',
                'header' => $this->load->view('contents_preview/preview_header',['address'=>$this->config->item('address')],TRUE),
                'footer' => $this->load->view('contents_preview/preview_footer',['address'=>$this->config->item('address')], TRUE),
            ];
            echo $this->load->view('contents_preview/contactus_page_view', $options, TRUE);
        } else {
            echo '';
        }
        die();
    }


    public function edit_termscontent() {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $brand = ifset($postdata, 'brand');
            $error = 'Empty Brand';
            if (!empty($brand)) {
                $error = '';
                $page_name = 'terms';
                $page_name_full = 'Terms & Polices';
                $session_id = uniq_link(15);
                $this->load->model('staticpages_model');
                $meta = $this->staticpages_model->get_metadata($page_name, $brand);
                $meta_view = $this->load->view('content/metadata_edit', $meta, TRUE);
                $special_content = $this->_prepare_custom_content($page_name, $brand, 1, $session_id);
                $session_data = usersession($session_id);
                $session_data['meta'] = $meta;
                $session_data['deleted'] = []; // type , id
                usersession($session_id, $session_data);
                $button_options = ['page'=>'terms', 'content_name' => $page_name_full, 'session'=> $session_id];
                $buttons_view = $this->load->view('content/content_editbuttons_view',$button_options, TRUE);
                $options = [
                    'meta_view' => $meta_view,
                    // 'buttons_view' => $buttons_view,
                    'special_content' => $special_content,
                ];
                $mdata['content'] = $this->load->view('content/staticpage_view',$options, TRUE);
                $mdata['buttons'] = $buttons_view;
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function change_termsparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'termspage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->update_termsparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function remove_termsparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'termspage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->remove_termsparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('content/terms_data_edit', ['terms'=>$res['terms']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function add_termsparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'termspage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->add_termsparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('content/terms_data_edit', ['terms'=>$res['terms']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function edit_termsparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'termspage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->edit_termsparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('content/termsdata_editor', $res['terms'], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function canceledit_termsparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'termspage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->edit_termsparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('content/termsdata_view', $res['terms'], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function saveedit_termsparam() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'termspage');
            $session_data = usersession($session_id);
            if (!empty($session_data)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->saveedit_termsparam($session_data, $postdata, $session_id);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('content/termsdata_view', $res['terms'], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function save_termspagecontent() {
        if ($this->isAjax()) {
            $mdata=[];
            $error = 'Edit session lost. Please, reload page';
            $postdata = $this->input->post();
            $session_id = (isset($postdata['session']) ? $postdata['session'] : 'faqpage');
            $session_data = usersession($session_id);
            $brand = ifset($postdata,'brand');
            if (!empty($session_data) && !empty($brand)) {
                $this->load->model('staticpages_model');
                $res = $this->staticpages_model->save_termspagecontent($session_data, $session_id, $brand, $this->USR_ID);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function prepare_termspage_preview() {
        $postdata = $this->input->get();
        $session_id = (isset($postdata['version']) ? $postdata['version'] : 'custom');
        $session_data = usersession($session_id);
        if (!empty($session_data)) {
            $maxnum = ceil(count($session_data['terms'])/3);
            $options = [
                'meta' => $session_data['meta'],
                'contents' => $session_data['data'],
                'terms' => $session_data['terms'],
                'version' => $session_id,
                'maxnum' => $maxnum,
                'address'=>$this->config->item('address'),
                'header' => $this->load->view('contents_preview/preview_header',['address'=>$this->config->item('address')],TRUE),
                'footer' => $this->load->view('contents_preview/preview_footer',['address'=>$this->config->item('address')], TRUE),
            ];
            echo $this->load->view('contents_preview/terms_page_view', $options, TRUE);
        } else {
            echo '';
        }
        die();
    }

    private function _prepare_custom_content($page_name, $brand, $edit_mode=0, $session ='') {
        $this->load->model('staticpages_model');
        $data = $this->staticpages_model->get_page_inner_content($page_name, $brand);
        $content = '';
        if ($page_name == 'custom') {
            $galleries = $this->staticpages_model->get_custom_galleries($brand);
            $case_study = $this->staticpages_model->get_case_study($brand);
            $galleryitems = $this->staticpages_model->get_galleryitems($brand);
            // Get data about categories, examples
            $gallery_options = [
                'galleries' => $galleries,
                'maxitems' => $this->config->item('max_slider_galleryitems'),
                'data' => $data,
            ];
            if ($edit_mode == 0) {
                $gallery_view = $this->load->view('content/custom_galleries_view', $gallery_options, TRUE);
            } else {
                // $editgallery = $this->load->view('content/custom_galleryitems_edit', $gallery_options, TRUE);
                $gallery_view = $this->load->view('content/custom_galleries_edit', $gallery_options, TRUE); // ['gallery_view'=>$editgallery]
            }
            // Examples gallery
            if ($edit_mode == 0) {
                $galleryitems_view = $this->load->view('content/custom_galleryitems_view', ['galleryitems'=> $galleryitems], TRUE);
            } else {
                $galleryitems_view = $this->load->view('content/custom_galleryitems_edit', ['galleryitems'=> $galleryitems], TRUE);
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
                'galleryitems_view' => $galleryitems_view,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode == 0) {
                $content = $this->load->view('content/customshaped_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/customshaped_custom_edit', $page_options, TRUE);
                // Save data to session
                $session_data = ['data' => $data, 'galleries' => $galleries, 'case_study' => $case_study, 'galleryitems' => $galleryitems];
                usersession($session, $session_data);
            }
        } elseif ($page_name=='faq') {
            $faq_sections = $this->staticpages_model->get_faq_sections($brand);
            $faq_content = '';
            if (!empty($faq_sections)) {
                $idx = 0;
                foreach ($faq_sections as $row) {
                    $quest = $this->staticpages_model->get_faq(['faq_section'=>$row['faq_section'],'brand'=>$brand]);
                    $faq_sections[$idx]['questions'] = $quest;
                    $faq_sections[$idx]['title'] = ucfirst($row['faq_section']).' Questions';
                    if ($edit_mode==0) {
                        $faq_content.=$this->load->view('content/faqsection_view', ['faq'=>$faq_sections[$idx]], TRUE);
                    } else {
                        $faq_items = $this->load->view('content/faqsection_item_edit',['faq'=>$faq_sections[$idx],'faq_section'=>$faq_sections[$idx]['faq_section']],TRUE);
                        $faq_content.=$this->load->view('content/faqsection_edit', ['faq'=>$faq_sections[$idx],'details'=>$faq_items], TRUE);
                    }
                    $idx++;
                }
            } else {
                if ($edit_mode==1) {
                    $sections = $this->faq_sections;
                    foreach ($sections as $frow) {
                        $faqdat=['title' => ucfirst($frow).' Questions', 'faq_section'=>$frow];
                        $faq_content.=$this->load->view('content/faqsection_edit', ['faq'=>$faqdat,'details'=>''], TRUE);
                        $faq_sections[]=['faq_section'=>$frow];
                    }
                }
            }
            $page_options = [
                'faq_sections' => $faq_content,
                'data' => $data,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('content/faq_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/faq_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data, 'faq_sections' => $faq_sections];
                usersession($session, $session_data);
            }
        } elseif ($page_name=='terms') {
            $terms=$this->staticpages_model->get_terms($brand);
            if ($edit_mode==0) {
                $terms_view = $this->load->view('content/terms_data_view', ['terms'=>$terms], TRUE);
            } else {
                $terms_view = $this->load->view('content/terms_data_edit', ['terms'=>$terms], TRUE);
            }
            $page_options = [
                'data' => $data,
                'terms' => $terms_view,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('content/terms_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/terms_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data, 'terms' => $terms];
                usersession($session, $session_data);
            }
        } elseif ($page_name=='about') {
            $address = $this->staticpages_model->get_page_inner_content('address', $brand);
            $page_options = [
                'data' => $data,
                'address' => $address,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('content/aboutus_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/aboutus_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data,'address'=>$address];
                usersession($session, $session_data);
            }
        } elseif ($page_name=='contactus') {
            $address = $this->staticpages_model->get_page_inner_content('address', $brand);
            $page_options = [
                'data' => $data,
                'address' => $address,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('content/contactus_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/contactus_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data,'address'=>$address];
                usersession($session, $session_data);
            }
        } elseif ($page_name=='extraservice') {
            $page_options = [
                'data' => $data,
            ];
            if ($edit_mode==1) {
                $page_options['session'] = $session;
            }
            if ($edit_mode==0) {
                $content = $this->load->view('content/extraservices_custom_view', $page_options, TRUE);
            } else {
                $content = $this->load->view('content/extraservices_custom_edit', $page_options, TRUE);
                $session_data = ['data' => $data,];
                usersession($session, $session_data);
            }

        }
        return $content;
    }

}

