<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Template
{

    var $CI;
    var $config;
    var $template;
    var $master;
    var $regions = array(
        '_scripts' => array(),
        '_styles' => array(),
        '_title' => ''
    );
    var $output;
    var $js = array();
    var $css = array();
    var $parser = 'parser';
    var $parser_method = 'parse';
    var $parse_template = FALSE;
    private $error_result = 0;
    private $success_result = 1;

    function __construct() {
        $this->CI=&get_instance();
    }

    public function prepare_public_page($options) {
        $dat = array();
        $styles=[];
        if (isset($options['styles'])) {
            $styles=$options['styles'];
        }
        $scripts=[];
        if (isset($options['scripts'])) {
            $scripts=$options['scripts'];
        }

        $pagetitle = (isset($options['title']) ? $options['title'] : 'System');

        $head_options=[
            'styles'=>$styles,
            'scripts'=>$scripts,
            'title' => $pagetitle,
        ];


        $dat['head'] = $this->CI->load->view('public_pages/head_view', $head_options, TRUE);

        return $dat;

    }

    public function prepare_pagecontent($options=[]) {
        $dat = array();
        $this->CI->load->model('dashboard_model');
        $this->CI->load->model('menuitems_model');
        $total_options = $this->CI->dashboard_model->get_totals('day');
        $total_view = $this->CI->load->view('dashboard_total_view', $total_options, TRUE);
        $styles=[];
        if (isset($options['styles'])) {
            $styles=$options['styles'];
        }
        $scripts=[];
        if (isset($options['scripts'])) {
            $scripts=$options['scripts'];
        }

        $pagetitle = (isset($options['title']) ? '::'.$options['title'] : '');

        $head_options=[
            'styles'=>$styles,
            'scripts'=>$scripts,
            'title' => ($this->CI->config->item('system_name').$pagetitle),
            'activelnk' => (isset($options['activelnk']) ? $options['activelnk'] : ''),
        ];


        $dat['head'] = $this->CI->load->view('pages/head_view', $head_options, TRUE);

        // Build left menu
        $leftmenu_options = [
            'activelnk'=>$options['activelnk'],
            'activeitem' => (isset($options['activeitem']) ? $options['activeitem'] : ''),
            'total' => $total_view,
            'permissions' => $this->CI->menuitems_model->get_user_permissions($options['userid']),
        ];
        $dat['left_menu'] = $this->CI->load->view('pages/leftside_menu_view', $leftmenu_options, TRUE);
        $topmenu_options = [
            'username' => $options['username'],
            'usermail' => $options['usermail'],
            'userlogo' => $options['userlogo'],
            'admin' => (in_array($options['userrole'],['admin','masteradmin']) ? 1 : 0),
            'loadcontact' => 0,
            'loadmessages' =>0,
        ];
        $dat['head_menu'] = $this->CI->load->view('pages/headmenu_view', $topmenu_options, TRUE);
        $right_options = [
            'username' => $options['username'],
            'usermail' => $options['usermail'],
            'userlogo' => $options['userlogo'],
            'admin' => (in_array($options['userrole'],['admin','masteradmin']) ? 1 : 0),
            'loadcontact' => 0,
            'loadmessages' =>0,
        ];
        $dat['right_menu'] = $this->CI->load->view('pages/rightside_menu_view', $right_options, TRUE);
        return $dat;
    }

}