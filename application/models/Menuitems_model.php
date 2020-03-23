<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Menuitems_model extends MY_Model
{

    private $sb_logo = '/img/page_view/sb_lefttab_logo.png';
    private $bt_logo = '/img/page_view/bt_lefttab_logo.png';
    private $all_logo = '/img/page_view/universal_lefttab_logo.png';

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
        $this->db->select('m.menu_item_id, m.item_name, m.menu_section, m.item_link');
        $this->db->from('menu_items m');
        $this->db->join('user_permissions u','m.menu_item_id = u.menu_item_id');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('u.permission_type > ', 0);
        $this->db->where('m.parent_id is null');
        $this->db->where_not_in('m.menu_section',['adminsection','alertsection']);
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

    public function get_itemsubmenu($user_id, $root_lnk) {
        $this->db->select('m.menu_item_id, m.item_name, m.menu_section, m.item_link');
        $this->db->from('menu_items mm');
        $this->db->join('menu_items m','m.parent_id=mm.menu_item_id');
        $this->db->join('user_permissions u','on m.menu_item_id = u.menu_item_id');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('u.permission_type > 0');
        $this->db->where('mm.item_link', $root_lnk);
        $this->db->order_by('m.menu_order, m.menu_section');
        return $res=$this->db->get()->result_array();
    }

    public function get_brand_permisions($user_id, $pagelink) {
        // Temporary
        $pages = ['/content'];
        $brands = [];
        if (in_array($pagelink, $pages)) {
            $brands =[
                ['brand' => 'SB', 'logo' => $this->sb_logo],
                ['brand' => 'BT', 'logo' => $this->bt_logo],
            ];
        } else {
            // Top Menu
            $brands =[
                ['brand' => 'ALL', 'logo' => $this->all_logo, 'label' => 'All brands'],
                ['brand' => 'SB', 'logo' => $this->sb_logo, 'label' => 'stressball.com only'],
                ['brand' => 'BT', 'logo' => $this->bt_logo, 'label' => 'bluetrack only'],
            ];
        }
        return $brands;
    }

    public function get_webpage($pid, $user_id) {
        $this->db->select('wp.menu_item_id, wp.item_name, wp.brand_access, rp.permission_type, rp.brand');
        $this->db->from('menu_items wp');
        $this->db->join('(select menu_item_id, permission_type, brand from user_permissions where user_id='.$user_id.') rp','rp.menu_item_id=wp.menu_item_id','left');
        $this->db->where('wp.parent_id', $pid);
        $this->db->where('wp.item_link is not null');
        $this->db->order_by('wp.menu_order');
        $result=$this->db->get()->result_array();
        return $result;
    }

}