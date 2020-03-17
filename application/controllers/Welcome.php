<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Welcome extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $head=[];
        $head['title']='Welcome';
        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $dat['content_view'] = '';
        $this->load->view('page/page_template_view', $dat);
    }

    /* Open File content */
    public function art_openimg() {
        $url = $this->input->post('url');
        $filename = $this->input->post('file');
        /* Get extension */
        openfile($url, $filename);
    }

}
