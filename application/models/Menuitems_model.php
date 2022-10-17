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

    public function get_user_permissions($user_id, $brand='') {
        $this->db->select('m.menu_item_id, m.item_name, m.menu_section, m.item_link, m.newver');
        $this->db->from('menu_items m');
        $this->db->join('user_permissions u','m.menu_item_id = u.menu_item_id');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('u.permission_type > ', 0);
        $this->db->where('m.parent_id is null');
        $this->db->where_not_in('m.menu_section',['adminsection','alertsection']);
        if (!empty($brand)) {
            $this->db->where('m.brand', $brand);
        }
        $this->db->order_by('m.menu_order, m.menu_section');
        $menu = $this->db->get()->result_array();
        // Get submenus
        $out=[];
        foreach ($menu as $mrow) {
            $this->db->select('m.menu_item_id, max(m.item_name) as item_name, max(m.item_link) as item_link');
            $this->db->from('menu_items m');
            $this->db->join('user_permissions u','m.menu_item_id = u.menu_item_id');
            $this->db->where('u.user_id', $user_id);
            $this->db->where('u.permission_type > ', 0);
            $this->db->where('m.parent_id', $mrow['menu_item_id']);
            $this->db->group_by('m.menu_item_id');
            $this->db->order_by('m.menu_order, m.menu_section');
            $submenu = $this->db->get()->result_array();
            $out[] = [
                'menu_item_id' => $mrow['menu_item_id'],
                'item_name' => $mrow['item_name'],
                'menu_section' => $mrow['menu_section'],
                'item_link' => $mrow['item_link'],
                'submenus' => count($submenu),
                'submenu' => $submenu,
                'newver' => $mrow['newver'],
            ];
        }
        return $out;
    }

    public function get_menuitem($lnk='', $menu_id=0) {
        $out=['result'=>$this->error_result,'msg'=>'Menu Item Not Found'];
        $this->db->select('*');
        $this->db->from('menu_items');
        if (!empty($lnk)) {
            $this->db->where('item_link', $lnk);
        }
        if (!empty($menu_id)) {
            $this->db->where('menu_item_id', $menu_id);
        }
        $res = $this->db->get()->row_array();
        if (!empty($res) && array_key_exists('menu_item_id', $res)) {
            $out['result'] = $this->success_result;
            $out['menuitem'] = $res;
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

    public function get_itemsubmenu($user_id, $root_lnk, $brand) {
        $this->db->select('m.menu_item_id, m.item_name, m.menu_section, m.item_link, m.brand_access, m.newver');
        $this->db->from('menu_items mm');
        $this->db->join('menu_items m','m.parent_id=mm.menu_item_id');
        $this->db->where('mm.item_link', $root_lnk);
        if (!empty($brand)) {
            $this->db->where('mm.brand', $brand);
        }
        $this->db->order_by('m.menu_order, m.menu_section');
        $res=$this->db->get()->result_array();
        $menuitems = [];
        foreach ($res as $row) {
            $this->db->select('p.brand, p.user_permission_id, p.permission_type');
            $this->db->select("(select count(*) from menu_items where parent_id={$row['menu_item_id']}) as subitem");
            $this->db->from('user_permissions p');
            $this->db->where('p.menu_item_id', $row['menu_item_id']);
            $this->db->where('p.user_id', $user_id);
            if ($row['brand_access']=='BRAND') {
                $this->db->where('p.permission_type > 0');
                $userperm = $this->db->get()->result_array();
                if (count($userperm)>0) {
                    $newbrand = [];
                    foreach ($userperm as $permrow) {
                        array_push($newbrand, $permrow);
                    }
                    $menuitems[] = [
                        'menu_item_id' => $row['menu_item_id'],
                        'item_name' => $row['item_name'],
                        'menu_section' => $row['menu_section'],
                        'item_link' => $row['item_link'],
                        'brand_access' => $row['brand_access'],
                        'brand' => $newbrand,
                        'newver' => $row['newver'],
                    ];
                }
            } else {
                $this->db->where('p.permission_type >= 0');
                $userperm = $this->db->get()->row_array();
                if ($userperm['permission_type'] > 0 ) {
                    if ($row['brand_access']=='NONE') {
                        $menuitems[] = [
                            'menu_item_id' => $row['menu_item_id'],
                            'item_name' => $row['item_name'],
                            'menu_section' => $row['menu_section'],
                            'item_link' => $row['item_link'],
                            'brand_access' => $row['brand_access'],
                            'brand' => null,
                            'newver' => $row['newver'],
                        ];
                    } else {
                        if (ifset($userperm,'brand','')!=='') {
                            $menuitems[] = [
                                'menu_item_id' => $row['menu_item_id'],
                                'item_name' => $row['item_name'],
                                'menu_section' => $row['menu_section'],
                                'item_link' => $row['item_link'],
                                'brand_access' => $row['brand_access'],
                                'brand' => $userperm['brand'],
                                'newver' => $row['newver'],
                            ];
                        }
                    }
                } elseif ($userperm['subitem'] > 0) {
                    // Count sub-permissions
                    $this->db->select('count(p.user_permission_id) as cnt');
                    $this->db->from('user_permissions p');
                    $this->db->join('menu_items m', 'm.menu_item_id=p.menu_item_id');
                    $this->db->where('m.parent_id', $row['menu_item_id']);
                    $this->db->where('p.permission_type > 0');
                    $this->db->where('p.user_id', $user_id);
                    $sumdat = $this->db->get()->row_array();
                    if ($sumdat['cnt'] > 0) {
                        $menuitems[] = [
                            'menu_item_id' => $row['menu_item_id'],
                            'item_name' => $row['item_name'],
                            'menu_section' => $row['menu_section'],
                            'item_link' => $row['item_link'],
                            'brand_access' => $row['brand_access'],
                            'brand' => null,
                            'newver' => $row['newver'],
                        ];
                    }
                }
            }
        }
        return $menuitems;
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

    public function get_menubrands_permisions($brand_list) {
        $brands = [];
        // Universal
        foreach ($brand_list as $row) {
            if ($row['brand']=='All') {
                $brands[] = ['brand' => 'ALL', 'logo' => $this->all_logo, 'label' => 'All brands'];
            }
        }
        // SB
        foreach ($brand_list as $row) {
            if ($row['brand']=='SB') {
                $brands[] = ['brand' => 'SB', 'logo' => $this->sb_logo, 'label' => 'stressball.com only'];
            }
        }
        // BT
        foreach ($brand_list as $row) {
            if ($row['brand']=='BT') {
                $brands[] = ['brand' => 'BT', 'logo' => $this->bt_logo, 'label' => 'bluetrack only'];
            }
        }
        return $brands;
    }

    public function get_brand_pagepermisions($brand_access, $brand) {
        if ($brand_access=='SITE') {
            if ($brand=='ALL') {
                $brands =[
                    ['brand' => 'ALL', 'logo' => $this->all_logo, 'label' => 'All brands'],
                    ['brand' => 'SB', 'logo' => $this->sb_logo, 'label' => 'stressball.com only'],
                    ['brand' => 'BT', 'logo' => $this->bt_logo, 'label' => 'bluetrack only'],
                ];
            } elseif ($brand=='SB') {
                $brands =[
                    ['brand' => 'SB', 'logo' => $this->sb_logo, 'label' => 'stressball.com only'],
                ];
            } elseif ($brand=='BT') {
                $brands =[
                    ['brand' => 'BT', 'logo' => $this->bt_logo, 'label' => 'bluetrack only'],
                ];
            } else {
                $brands=[];
            }
        }
        return $brands;

    }

    public function get_webpage($pid, $user_id) {
        $system_brands = [];
        $system_brands[] = ['key'=> 'All','label'=>'Univ'];
        $system_brands[] = ['key' => 'SB', 'label'=> 'Stressball'];
        $system_brands[] = ['key' => 'BT', 'label' => 'Bluetrack'];
        $this->db->select('wp.menu_item_id, wp.item_name, wp.brand_access');
        $this->db->from('menu_items wp');
        $this->db->where('wp.parent_id', $pid);
        $this->db->where('wp.item_link is not null');
        $this->db->order_by('wp.menu_order');
        $result=$this->db->get()->result_array();
        // , rp.permission_type, rp.brand
        // $this->db->join('(select menu_item_id, permission_type, brand from user_permissions where user_id='.$user_id.') rp','rp.menu_item_id=wp.menu_item_id','left');
        $out = [];
        foreach ($result as $row) {
            $this->db->select('user_permission_id, menu_item_id, permission_type, brand');
            $this->db->from('user_permissions');
            $this->db->where('user_id', $user_id);
            $this->db->where('menu_item_id', $row['menu_item_id']);
            if ($row['brand_access']=='BRAND') {
                $permres = $this->db->get()->result_array();
            } else {
                $permres = $this->db->get()->row_array();
            }

            if ($row['brand_access']=='NONE') {
                $out[] = [
                    'menu_item_id' => $row['menu_item_id'],
                    'item_name' => $row['item_name'],
                    'brand_access' => $row['brand_access'],
                    'permission_type' => isset($permres['permission_type']) ? $permres['permission_type'] : '',
                    'brand' => NULL,
                ];
            } elseif ($row['brand_access']=='SITE') {
                $out[] = [
                    'menu_item_id' => $row['menu_item_id'],
                    'item_name' => $row['item_name'],
                    'brand_access' => $row['brand_access'],
                    'permission_type' => isset($permres['permission_type']) ? $permres['permission_type'] : '',
                    'brand' => isset($permres['brand']) ? $permres['brand'] : NULL,
                ];
            } else {
                $newbrand = [];
                $idx = 1;
                foreach ($system_brands as $brow) {
                    $found = 0;
                    $pidx = 0;
                    foreach ($permres as $prow) {
                        if ($prow['brand']==$brow['key']) {
                            $found=1;
                            break;
                        }
                        $pidx++;
                    }
                    if ($found==1) {
                        $newbrand[] = [
                            'user_permission_id' => $permres[$pidx]['user_permission_id'],
                            'permission_type' => 1,
                            'brand' => $brow['key'],
                            'label' => $brow['label'],
                            'checkval' => 1,
                        ];
                    } else {
                        $newbrand[] = [
                            'user_permission_id' => $idx * (-1),
                            'permission_type' => 0,
                            'brand' => $brow['key'],
                            'label' => $brow['label'],
                            'checkval' => 0,
                        ];
                    }
                }
                $out[] = [
                    'menu_item_id' => $row['menu_item_id'],
                    'item_name' => $row['item_name'],
                    'brand_access' => $row['brand_access'],
                    'permission_type' => count($newbrand)==0 ? '' : 1,
                    'brand' => $newbrand,
                ];
            }
        }
        return $out;
    }

    public function update_userpage_permission($session_data, $menuitem, $newval, $session_id) {
        $out=['result' => $this->error_result,'msg'=>'Page Not found'];
        if ($menuitem>0) {
            $webpages = $session_data['webpages'];
            $found = 0;
            $child = [];
            $idx = 0;
            foreach ($webpages as $row) {
                if ($row['id']==$menuitem) {
                    array_push($child, $row['id']);
                    $found = 1;
                    $webpages[$idx]['value']=$newval;
                };
                if (is_array($row['element'])) {
                    $eidx = 0;
                    foreach ($row['element'] as $erow) {
                        if ($erow['id']==$menuitem || $row['id']==$menuitem) {
                            $found=1;
                            array_push($child, $erow['id']);
                            $webpages[$idx]['element'][$eidx]['value']=$newval;
                        }
                        if (is_array($erow['element'])) {
                            $sidx = 0;
                            foreach ($erow['element'] as $srow) {
                                if ($srow['id']==$menuitem || $row['id']==$menuitem || $erow['id']==$menuitem) {
                                    $found = 1;
                                    array_push($child, $srow['id']);
                                    $webpages[$idx]['element'][$eidx]['element'][$sidx]['value']=$newval;
                                }
                                $sidx++;
                            }
                        }
                        $eidx++;
                    }
                }
                $idx++;
            }
            if ($found==1) {
                $out['result'] = $this->success_result;
                $out['child'] = $child;
                $session_data['webpages']=$webpages;
                usersession($session_id, $session_data);
            }
        }
        return $out;
    }

    public function update_userpage_siteaccess($session_data, $menuitem, $newval, $session_id) {
        $out=['result' => $this->error_result,'msg'=>'Page Not found'];
        if ($menuitem>0) {
            $webpages = $session_data['webpages'];
            $found = 0;
            $idx = 0;
            foreach ($webpages as $row) {
                if ($row['id']==$menuitem) {
                    $found = 1;
                    $webpages[$idx]['brand']=$newval;
                    if ($newval=='') {
                        $webpages[$idx]['value']=0;
                    } else {
                        $webpages[$idx]['value']=1;
                    }
                };
                if (is_array($row['element'])) {
                    $eidx = 0;
                    foreach ($row['element'] as $erow) {
                        if ($erow['id']==$menuitem) {
                            $found=1;
                            $webpages[$idx]['element'][$eidx]['brand']=$newval;
                            if ($newval=='') {
                                $webpages[$idx]['element'][$eidx]['value']=0;
                            } else {
                                $webpages[$idx]['element'][$eidx]['value']=1;
                            }
                        }
                        $eidx++;
                    }
                }
                $idx++;
            }
            if ($found==1) {
                $out['result'] = $this->success_result;
                $session_data['webpages']=$webpages;
                usersession($session_id, $session_data);
            }
        }
        return $out;
    }

    public function update_userpage_brandaccess($session_data, $menuitem, $brand, $session_id) {
        $out=['result' => $this->error_result,'msg'=>'Page Not found'];
        if ($menuitem>0 && !empty($brand)) {
            $webpages = $session_data['webpages'];
            $found = 0;
            $idx = 0;
            foreach ($webpages as $row) {
                if ($row['id']==$menuitem) {
                    $found = 1;
                    // $webpages[$idx]['brand']=$newval;
                    // if ($newval=='') {
                    //     $webpages[$idx]['value']=0;
                    // } else {
                    //     $webpages[$idx]['value']=1;
                    // }
                };
                if (is_array($row['element'])) {
                    $eidx = 0;
                    foreach ($row['element'] as $erow) {
                        if ($erow['id']==$menuitem) {
                            $found=1;
                            $bidx = 0;
                            $newval = 0;
                            $brandacc = 0;
                            foreach ($erow['brand'] as $brrow) {
                                if ($brrow['brand']==$brand) {
                                    $newval=($brrow['checkval']==1 ? 0 : 1);
                                    $webpages[$idx]['element'][$eidx]['brand'][$bidx]['checkval']=$newval;
                                    $brandacc+=$newval;
                                } else {
                                    $brandacc+=$brrow['checkval'];
                                }
                                $bidx++;
                            }
                            if ($brandacc==0) {
                                $webpages[$idx]['element'][$eidx]['value']=0;
                            } else {
                                $webpages[$idx]['element'][$eidx]['value']=1;
                            }
                            $out['newval']=$newval;
                            $out['newacc'] = $brandacc;
                        }
                        $eidx++;
                    }
                }
                $idx++;
            }
            if ($found==1) {
                $out['result'] = $this->success_result;
                $session_data['webpages']=$webpages;
                usersession($session_id, $session_data);
            }
        }
        return $out;

    }

    public function save_userpermissions($webpages, $user_id) {
        foreach ($webpages as $row) {
            $menuchk = $this->get_menuitem('', $row['id']);
            if ($menuchk['result']==$this->success_result) {
                $menuitem = $menuchk['menuitem'];
                if ($menuitem['brand_access']=='BRAND') {
                    //
                } else {
                    $res = $this->_chkuserpermission($row['id'], $user_id);
                    $this->db->set('permission_type', $row['value']);
                    if ($row['value']==0) {
                        $this->db->set('brand', NULL);
                    } else {
                        if (empty($row['brand'])) {
                            $this->db->set('brand', NULL);
                        } else {
                            $this->db->set('brand', $row['brand']);
                        }
                    }
                    $this->db->where('user_permission_id', $res);
                    $this->db->update('user_permissions');
                }
                if (is_array($row['element'])) {
                    $elements = $row['element'];
                    foreach ($elements as $erow) {
                        $elmenuchk = $this->get_menuitem('', $erow['id']);
                        if ($elmenuchk['result']==$this->success_result) {
                            $elmenuitem = $elmenuchk['menuitem'];
                            if ($elmenuitem['brand_access']=='BRAND') {
                                $elbrands = $erow['brand'];
                                foreach ($elbrands as $rbrand) {
                                    // New Item Access
                                    if ($rbrand['user_permission_id']<0 && $rbrand['checkval']==1) {
                                        $this->db->set('user_id', $user_id);
                                        $this->db->set('menu_item_id', $erow['id']);
                                        $this->db->set('permission_type', 1);
                                        $this->db->set('brand', $rbrand['brand']);
                                        $this->db->insert('user_permissions');
                                    }
                                    // Delete Unchecked Access
                                    if ($rbrand['user_permission_id']>0 && $rbrand['checkval']==0) {
                                        $this->db->where('user_permission_id', $rbrand['user_permission_id']);
                                        $this->db->delete('user_permissions');
                                    }
                                }
                            } else {
                                $eres = $this->_chkuserpermission($erow['id'], $user_id);
                                $this->db->set('permission_type', $erow['value']);
                                if ($erow['value']==0) {
                                    $this->db->set('brand', NULL);
                                } else {
                                    if (empty($erow['brand'])) {
                                        $this->db->set('brand', NULL);
                                    } else {
                                        $this->db->set('brand', $erow['brand']);
                                    }
                                }
                                $this->db->where('user_permission_id', $eres);
                                $this->db->update('user_permissions');
                                if ($erow['value']==1) {
                                    $this->db->set('permission_type', 1);
                                    $this->db->where('user_permission_id', $res);
                                    $this->db->update('user_permissions');
                                }
                            }
                        }
                        // 3-rd level
                        if (isset($erow['element']) && is_array($erow['element'])) {
                            $selements = $erow['element'];
                            foreach ($selements as $srow) {
                                $selmenuchk = $this->get_menuitem('', $srow['id']);
                                if ($selmenuchk['result']==$this->success_result) {
                                    $selmenuitem = $selmenuchk['menuitem'];
                                    $sres = $this->_chkuserpermission($srow['id'], $user_id);
                                    $this->db->set('permission_type', $srow['value']);
                                    if ($srow['value']==0) {
                                        $this->db->set('brand', NULL);
                                    } else {
                                        if (empty($srow['brand'])) {
                                            $this->db->set('brand', NULL);
                                        } else {
                                            $this->db->set('brand', $srow['brand']);
                                        }
                                    }
                                    $this->db->where('user_permission_id', $sres);
                                    $this->db->update('user_permissions');
                                    if ($srow['value']==1) {
                                        $this->db->set('permission_type', 1);
                                        $this->db->where('user_permission_id', $eres);
                                        $this->db->update('user_permissions');
                                        $this->db->set('permission_type', 1);
                                        $this->db->where('user_permission_id', $res);
                                        $this->db->update('user_permissions');
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return true;
    }

    private function _chkuserpermission($menu_item_id, $user_id) {
        $this->db->select('max(user_permission_id) as user_permission_id, count(user_permission_id) as cnt');
        $this->db->from('user_permissions');
        $this->db->where('user_id', $user_id);
        $this->db->where('menu_item_id', $menu_item_id);
        $dat = $this->db->get()->row_array();
        if ($dat['cnt']==0) {
            $this->db->set('user_id', $user_id);
            $this->db->set('menu_item_id', $menu_item_id);
            $this->db->insert('user_permissions');
            $result = $this->db->insert_id();
        } else {
            $result = $dat['user_permission_id'];
        }
        return $result;
    }

    public function get_webpages() {
        // Get list of main branches
        $this->db->select('*');
        $this->db->from('menu_items');
        $this->db->where('parent_id is null');
        $this->db->where('item_link is not null');
        $this->db->order_by('menu_order');
        $main=$this->db->get()->result_array();
        $out=array();
        foreach ($main as $mrow) {
            $out[]=array(
                'key'=>$mrow['menu_item_id'],
                'label'=>$mrow['item_name'],
            );
            // Get subpages
            $this->db->select('*');
            $this->db->from('menu_items');
            $this->db->where('parent_id', $mrow['menu_item_id']);
            $this->db->where('item_link is not null');
            $this->db->order_by('menu_order');
            $pages=$this->db->get()->result_array();
            foreach ($pages as $row) {
                $out[]=array(
                    'key'=>$row['menu_item_id'],
                    'label'=>' &ndash; '.$row['item_name'],
                );
            }
        }
        return $out;

    }

    public function get_submenu($user_id, $root_lnk) {
        $this->db->select('m.menu_item_id, m.item_name, m.menu_section, m.item_link, m.brand_access, m.newver');
        $this->db->from('menu_items mm');
        $this->db->join('menu_items m','m.parent_id=mm.menu_item_id');
        $this->db->where('mm.item_link', $root_lnk);
        $this->db->order_by('m.menu_order, m.menu_section');
        $res=$this->db->get()->result_array();
        $menuitems = [];
        foreach ($res as $row) {
            $this->db->select('p.brand, p.user_permission_id, p.permission_type');
            $this->db->select("(select count(*) from menu_items where parent_id={$row['menu_item_id']}) as subitem");
            $this->db->from('user_permissions p');
            $this->db->where('p.menu_item_id', $row['menu_item_id']);
            $this->db->where('p.user_id', $user_id);
            if ($row['brand_access']=='BRAND') {
                $this->db->where('p.permission_type > 0');
                $userperm = $this->db->get()->result_array();
                if (count($userperm)>0) {
                    $newbrand = [];
                    foreach ($userperm as $permrow) {
                        array_push($newbrand, $permrow);
                    }
                    $menuitems[] = [
                        'menu_item_id' => $row['menu_item_id'],
                        'item_name' => $row['item_name'],
                        'menu_section' => $row['menu_section'],
                        'item_link' => $row['item_link'],
                        'brand_access' => $row['brand_access'],
                        'brand' => $newbrand,
                        'newver' => $row['newver'],
                    ];
                }
            } else {
                $this->db->where('p.permission_type >= 0');
                $userperm = $this->db->get()->row_array();
                if ($userperm['subitem'] > 0 ) {
                    $menusub = $this->get_submenu($user_id, $row['item_link']);
                    if (!empty($menusub)) {
                        $menuitems[] = [
                            'menu_item_id' => $row['menu_item_id'],
                            'item_name' => $row['item_name'],
                            'menu_section' => $row['menu_section'],
                            'item_link' => $row['item_link'],
                            'brand_access' => $row['brand_access'],
                            'brand' => null,
                            'newver' => $row['newver'],
                            'submenu' => $menusub,
                        ];
                    }
                } elseif ($userperm['permission_type'] > 0 ) {
                    if ($row['brand_access']=='NONE') {
                        $menuitems[] = [
                            'menu_item_id' => $row['menu_item_id'],
                            'item_name' => $row['item_name'],
                            'menu_section' => $row['menu_section'],
                            'item_link' => $row['item_link'],
                            'brand_access' => $row['brand_access'],
                            'brand' => null,
                            'newver' => $row['newver'],
                        ];
                    } else {
                        if (ifset($userperm,'brand','')!=='') {
                            $menuitems[] = [
                                'menu_item_id' => $row['menu_item_id'],
                                'item_name' => $row['item_name'],
                                'menu_section' => $row['menu_section'],
                                'item_link' => $row['item_link'],
                                'brand_access' => $row['brand_access'],
                                'brand' => $userperm['brand'],
                                'newver' => $row['newver'],
                            ];
                        }
                    }
                }
            }
        }
        return $menuitems;
    }

    public function get_user_mobpermissions($user_id) {
        $this->db->select('m.menu_item_id, m.item_name, m.menu_section, m.item_link, m.newver');
        $this->db->from('menu_items m');
        $this->db->join('user_permissions u','m.menu_item_id = u.menu_item_id');
        $this->db->where('u.user_id', $user_id);
        $this->db->where('u.permission_type > ', 0);
        $this->db->where('m.parent_id is null');
        $this->db->order_by('m.menu_order, m.menu_section');
        $menu = $this->db->get()->result_array();
        return $menu;
    }

    public function get_current_brand() {
        $brand =  usersession('currentbrand');
        if (!empty($brand)) {
            usersession('currentbrand', $brand);
        }
        return $brand;
    }

}