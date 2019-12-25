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
        $total_options = $this->CI->dashboard_model->get_totals('week');
        $total_view = $this->CI->load->view('page/dashboard_total_view', $total_options, TRUE);
        $styles=[];
        if (isset($options['styles'])) {
            $styles=$options['styles'];
        }
        $scripts=[];
        if (isset($options['scripts'])) {
            $scripts=$options['scripts'];
        }

        // Build left menu
        $menu_options = [
            'activelnk'=>(isset($options['activelnk']) ? $options['activelnk'] : ''),
            // 'activeitem' => (isset($options['activeitem']) ? $options['activeitem'] : ''),
            'permissions' => $this->CI->menuitems_model->get_user_permissions($options['user_id']),
        ];
        $menu_view = $this->CI->load->view('page/menu_view', $menu_options, TRUE);
        // Admin and Alerts
        $admin_permission = $alert_permission = 0;
        $adminchk = $this->CI->menuitems_model->get_menuitem('/admin');

        if ($adminchk['result']==$this->success_result) {
            $admin_permissionchk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $adminchk['menuitem']['menu_item_id']);
            if ($admin_permissionchk['result']==$this->success_result && $admin_permissionchk['permission']>0) {
                $admin_permission = 1;
            }
        }

        $alertchk = $this->CI->menuitems_model->get_menuitem('/alerts');
        if ($alertchk['result']==$this->success_result) {
            $alert_permissionchk = $this->CI->menuitems_model->get_menuitem_userpermisiion($options['user_id'], $alertchk['menuitem']['menu_item_id']);
            if ($alert_permissionchk['result']==$this->success_result && $alert_permissionchk['permission']>0) {
                $alert_permission = 1;
            }
        }

        $pagetitle = (isset($options['title']) ? '::'.$options['title'] : '');

        $head_options=[
            'styles'=>$styles,
            'scripts'=>$scripts,
            'title' => ($this->CI->config->item('system_name').$pagetitle),
        ];

        $dat['head_view'] = $this->CI->load->view('page/head_view', $head_options, TRUE);

        $topmenu_options = [
            'user_name' => $options['user_name'],
            'activelnk' => (isset($options['activelnk']) ? $options['activelnk'] : ''),
            'total_view' => $total_view,
            'menu_view' => $menu_view,
            'adminchk' => $admin_permission,
            'alertchk' => $alert_permission,
        ];
        $dat['header_view'] = $this->CI->load->view('page/header_view', $topmenu_options, TRUE);
        // $dat['popups_view'] = $this->CI->load->view('page/popups_view', [], TRUE);
        return $dat;
    }

}