<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Menuitems_model extends MY_Model
{


    public function __construct()
    {
        parent::__construct();
    }

    public function get_menuitems_permisiions($user_id=0) {
        // Get root
        $this->db->select('*');
        $this->db->from('menu_items');
        $this->db->where('parent_id', NULL);
        $this->db->order_by('menu_order');
        $root = $this->db->get()->result_array();
        $menu = [];
        foreach ($root as $row) {
            // Get submenu
            $submenu=[];
            $this->db->select('*');
            $this->db->from('menu_items');
            $this->db->where('parent_id', $row['menu_item_id']);
            $this->db->order_by('menu_order');
            $subitems = $this->db->get()->result_array();
            foreach ($subitems as $srow) {
                if ($user_id==0) {
                    $permis = [];
                } else {
                    $this->db->select('user_permission_id, permission_type');
                    $this->db->from('user_permissions');
                    $this->db->where('menu_item_id', $srow['menu_item_id']);
                    $this->db->where('user_id', $user_id);
                    $permis = $this->db->get()->row_array();
                }
                $submenu[]=[
                    'menu_item_id' => $srow['menu_item_id'],
                    'item_name' => $srow['item_name'],
                    'user_permission_id' => (isset($permis['user_permission_id']) ? $permis['user_permission_id'] : -1),
                    'permission_type' => (isset($permis['user_permission_id']) ? $permis['permission_type'] : 0),
                ];
            }
            // Get permission for main menu
            if ($user_id==0) {
                $permis = [];
            } else {
                $this->db->select('user_permission_id, permission_type');
                $this->db->from('user_permissions');
                $this->db->where('menu_item_id', $row['menu_item_id']);
                $this->db->where('user_id', $user_id);
                $permis = $this->db->get()->row_array();
            }
            $menu[]=[
                'menu_item_id' => $row['menu_item_id'],
                'item_name' => $row['item_name'],
                'user_permission_id' => (isset($permis['user_permission_id']) ? $permis['user_permission_id'] : -1),
                'permission_type' => (isset($permis['user_permission_id']) ? $permis['permission_type'] : 0),
                'submenu' => $submenu,
                'expand' => count($submenu),
            ];
        }
        return $menu;
    }

    public function change_root_menuaccess($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Menu Item not found'];
        $menu=(isset($session_data['menu']) ? $session_data['menu'] : []);
        $menu_item_id = (isset($data['menu']) ? $data['menu'] : '');
        $newval = (isset($data['newval']) ? $data['newval'] : 0);

        if (!empty($menu_item_id)) {
            $found=0; $menuidx = 0;
            foreach ($menu as $row) {
                if ($row['menu_item_id']==$menu_item_id) {
                    $found=1;
                    break;
                }
                $menuidx++;
            }
            if ($found==1) {
                $menu[$menuidx]['permission_type']=$newval;
                // update submenus
                $newsubm=[];
                if ($menu[$menuidx]['expand']>0) {
                    $submenu = $menu[$menuidx]['submenu'];
                    $submenuidx=0;
                    foreach ($submenu as $srow) {
                        $submenu[$submenuidx]['permission_type']=$newval;
                        $newsubm[]=[
                            'menu_item_id' => $srow['menu_item_id'],
                            'permission_type' => $newval,
                        ];
                    }
                    $menu[$menuidx]['submenu']=$submenu;

                }
                $session_data['menu'] = $menu;
                // Save session
                usersession($session_id, $session_data);
                $out['submenu']=$newsubm;
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function change_submenu_menuaccess($session_data, $data, $session_id) {
        $out=['result'=>$this->error_result,'msg'=>'Menu Item not found'];
        $menu=(isset($session_data['menu']) ? $session_data['menu'] : []);
        $menu_item_id = (isset($data['menu']) ? $data['menu'] : '');
        $newval = (isset($data['newval']) ? $data['newval'] : 0);

        if (!empty($menu_item_id)) {
            $found=0; $menuidx = 0;
            foreach ($menu as $row) {
                $submenuidx = 0;
                foreach ($row['submenu'] as $srow) {
                    if ($srow['menu_item_id']==$menu_item_id) {
                        $found = 1;
                        break;
                    }
                    $submenuidx++;
                }
                if ($found == 1) {
                    break;
                }
                $menuidx++;
            }

            if ($found==1) {
                // Submenus
                $submenu = $menu[$menuidx]['submenu'];
                $submenu[$submenuidx]['permission_type']=$newval;
                // Calc new permission for root
                $permis = 0;
                $numsubmenu = count($submenu);
                foreach ($submenu as $srow) {
                    $permis += $srow['permission_type'];
                }
                if ($permis == 0) {
                    $menu[$menuidx]['permission_type'] = $permis;
                    $out['permission_type'] = 0;
                } else {
                    if ($permis/$numsubmenu==2) {
                        $menu[$menuidx]['permission_type'] = 2;
                        $out['permission_type'] = 2;
                    } else {
                        $menu[$menuidx]['permission_type'] = 1;
                        $out['permission_type'] = 1;
                    }
                }
                $out['root'] = $menu[$menuidx]['menu_item_id'];
                $menu[$menuidx]['submenu']=$submenu;
                $session_data['menu'] = $menu;
                // Save session
                usersession($session_id, $session_data);
                $out['result']=$this->success_result;
            }
        }
        return $out;
    }

    public function get_user_permissions($user_id) {
        $this->db->select('m.menu_item_id, m.item_name, m.menu_icon, m.menu_section, m.item_link');
        $this->db->from('menu_items m');
        $this->db->join('user_permissions u','m.menu_item_id = u.menu_item_id');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('u.permission_type > ', 0);
        $this->db->where('m.parent_id is null');
        $this->db->order_by('m.menu_order, m.menu_section');
        $menu = $this->db->get()->result_array();
        // Get submenus
        $out=[];
        foreach ($menu as $mrow) {
            $this->db->select('m.menu_item_id, m.item_name, m.item_link');
            $this->db->from('menu_items m');
            $this->db->join('user_permissions u','m.menu_item_id = u.menu_item_id');
            $this->db->where('u.user_id', $user_id);
            $this->db->where('u.permission_type > ', 0);
            $this->db->where('m.parent_id', $mrow['menu_item_id']);
            $this->db->order_by('m.menu_order, m.menu_section');
            $submenu = $this->db->get()->result_array();
            $out[] = [
                'menu_item_id' => $mrow['menu_item_id'],
                'item_name' => $mrow['item_name'],
                'menu_icon' => $mrow['menu_icon'],
                'menu_section' => $mrow['menu_section'],
                'item_link' => $mrow['item_link'],
                'submenus' => count($submenu),
                'submenu' => $submenu,
            ];
        }
        return $out;
    }

    public function get_menuitem($lnk='', $menu_id=0) {
        $out=['result'=>$this->error_result,'msg'=>'Menu Item Not Found'];
        if ($lnk) {
            $this->db->select('*');
            $this->db->from('menu_items');
            $this->db->where('item_link', $lnk);
            $res = $this->db->get()->row_array();
            if (!empty($res) && array_key_exists('menu_item_id', $res)) {
                $out['result']=$this->success_result;
                $out['menuitem'] = $res;
            }
        }
        return $out;
    }

    public function get_menuitem_userpermisiion($user_id, $menu_item_id) {
        $out=['result'=>$this->error_result,'msg'=>'Menu Item Not Found'];
        $this->db->select('*');
        $this->db->from('user_permissions');
        $this->db->where('user_id', $user_id);
        $this->db->where('menu_item_id', $menu_item_id);
        $permis = $this->db->get()->row_array();
        if (!empty($permis) && array_key_exists('user_permission_id', $permis)) {
            $out['result']=$this->success_result;
            $out['permission']=$permis['permission_type'];
        }
        return $out;
    }
}