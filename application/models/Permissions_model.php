<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Permissions_model extends My_Model
{

//    const INIT_ERRMSG='Unknown error. Try later';
    private $WEBSYS='System';

    function __construct()
    {
        parent::__construct();
    }

//    function get_mainitems($user_id) {
//        $this->db->select('rp.*, wp.websys_page_link');
//        $this->db->from('user_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->where('wp.websys_page_parent',NULL);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $this->db->where('rp.user_id',$user_id);
//        $res=$this->db->get()->result_array();
//        $menu_array=array();
//        foreach ($res as $row) {
//            array_push($menu_array, $row['websys_page_link']);
//        }
//        $menu_array['grey_login']=$this->config->item('grey_login');
//        return $menu_array;
//    }
//
//    function get_mainitems_role($user_role) {
//        $this->db->select('rp.*, wp.websys_page_link');
//        $this->db->from('role_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->join('roles r','r.role_id=rp.role_id');
//        $this->db->where('r.role_short',$user_role);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $this->db->where('wp.websys_page_parent',NULL);
//        $res=$this->db->get()->result_array();
//        $menu_array=array();
//        foreach ($res as $row) {
//            array_push($menu_array, $row['websys_page_link']);
//        }
//        $menu_array['grey_login']=$this->config->item('grey_login');
//        return $menu_array;
//    }
//
//    function check_itempermis($user_id, $item_lnk) {
//        $this->db->select('rp.*');
//        $this->db->from('user_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->where('rp.user_id',$user_id);
//        $this->db->where('wp.websys_page_link',$item_lnk);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['user_permission_id'])) {
//            return FALSE;
//        } else {
//            return $res['permission_type'];
//        }
//    }
//
//
//    function check_itempermis_role($user_role, $item_lnk) {
//        $this->db->select('rp.*');
//        $this->db->from('role_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->join('roles r','r.role_id=rp.role_id');
//        $this->db->where('r.role_short',$user_role);
//        $this->db->where('wp.websys_page_link',$item_lnk);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $res=$this->db->get()->row_array();
//        if (!isset($res['role_permission_id'])) {
//            return FALSE;
//        } else {
//            return TRUE;
//        }
//    }

    function get_subitems($user_id, $item_lnk) {
        $res =[
            ['user_permission_id'=>4794,'user_id'=>1,'websys_page_id'=>89,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalescustoms',],
            ['user_permission_id'=>4795,'user_id'=>1,'websys_page_id'=>90,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesstock',],
            ['user_permission_id'=>4796,'user_id'=>1,'websys_page_id'=>91,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesariel',],
            ['user_permission_id'=>4797,'user_id'=>1,'websys_page_id'=>92,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesalpi',],
            ['user_permission_id'=>4798,'user_id'=>1,'websys_page_id'=>93,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesmailine',],
            ['user_permission_id'=>4799,'user_id'=>1,'websys_page_id'=>94,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesother',],
            ['user_permission_id'=>4800,'user_id'=>1,'websys_page_id'=>95,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsaleshit',],
            ['user_permission_id'=>4800,'user_id'=>1,'websys_page_id'=>96,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesesp',],
            // ['user_permission_id'=>4795,'user_id'=>1,'websys_page_id'=>90,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'',],
        ];
//        $this->db->select('rp.*, wp.websys_page_link');
//        $this->db->from('user_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->join('websys_pages wps','wps.websys_page_id=wp.websys_page_parent');
//        $this->db->where('rp.user_id',$user_id);
//        $this->db->where('wps.websys_page_link',$item_lnk);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $res=$this->db->get()->result_array();
        $menu_array=array();
        foreach ($res as $row) {
            array_push($menu_array, $row['websys_page_link']);
        }
        return $menu_array;
    }

//    function get_subitemstopmenu($user_id, $item_lnk) {
//        $this->db->select('rp.*, wp.websys_page_link, wp.websys_pagesref, wp.websys_page_name');
//        $this->db->from('user_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->join('websys_pages wps','wps.websys_page_id=wp.websys_page_parent');
//        $this->db->where('rp.user_id',$user_id);
//        $this->db->where('wps.websys_page_link',$item_lnk);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $this->db->order_by('wp.websys_pageorder');
//        $res=$this->db->get()->result_array();
//
//
//        $menu_array=array();
//        foreach ($res as $row) {
//            $menu_array[]=array(
//                'pagelnk'=>$row['websys_page_link'],
//                'pageref'=>$row['websys_pagesref'],
//                'label'=>$row['websys_page_name'],
//            );
//        }
//        return $menu_array;
//    }
//
//    function get_subitems_role($user_role, $item_lnk) {
//        $this->db->select('rp.*, wp.websys_page_link');
//        $this->db->from('role_permissions rp');
//        $this->db->join('websys_pages wp','wp.websys_page_id=rp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->join('websys_pages wps','wps.websys_page_id=wp.websys_page_parent');
//        $this->db->join('roles r','r.role_id=rp.role_id');
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $this->db->where('r.role_short',$user_role);
//        $this->db->where('wps.websys_page_link',$item_lnk);
//        $res=$this->db->get()->result_array();
//        $menu_array=array();
//        foreach ($res as $row) {
//            array_push($menu_array, $row['websys_page_link']);
//        }
//        return $menu_array;
//    }
//
//
//    function count_user_roles() {
//        $this->db->select('count(*) as cnt');
//        $this->db->from('roles');
//        $res=$this->db->get()->row_array();
//        return $res['cnt'];
//    }
//
//    function get_alluser_roles() {
//        $this->db->select('*');
//        $this->db->from('roles');
//        $this->db->order_by('role_order');
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
//    function get_user_roles($order_by,$direct,$limit,$offset) {
//        $this->db->select('*');
//        $this->db->from('roles');
//        if ($order_by) {
//            if ($direct) {
//                $this->db->order_by($order_by,$direct);
//            } else {
//                $this->db->order_by($order_by);
//            }
//        }
//        $this->db->limit($limit, $offset);
//        $res=$this->db->get()->result_array();
//        return $res;
//    }
//
//    function get_role_dat($role_id) {
//        $this->db->select('*');
//        $this->db->from('roles');
//        $this->db->where('role_id',$role_id);
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    function get_webpage($pid,$websystem_id, $user_id) {
//        $this->db->select('wp.websys_page_id, wp.websys_page_name, rp.permission_type, wp.profit_view');
//        $this->db->from('websys_pages wp');
//        $this->db->join('(select websys_page_id,permission_type from user_permissions where user_id='.$user_id.') rp','rp.websys_page_id=wp.websys_page_id','left');
//        $this->db->where('wp.websystem_id',$websystem_id);
//        $this->db->where('wp.websys_page_parent',$pid);
//        $this->db->where('wp.websys_page_link is not null');
//        $this->db->order_by('wp.websys_pageorder');
//        $result=$this->db->get()->result_array();
//        return $result;
//    }
//
//
//    function get_webpage_role($pid,$websystem_id, $role_id) {
//        $this->db->select('wp.websys_page_id, wp.websys_page_name, rp.permission_type');
//        $this->db->from('websys_pages wp');
//        $this->db->join('(select websys_page_id,permission_type from role_permissions where role_id='.$role_id.') rp','rp.websys_page_id=wp.websys_page_id','left');
//        $this->db->where('wp.websystem_id',$websystem_id);
//        $this->db->where('wp.websys_page_parent',$pid);
//        $result=$this->db->get()->result_array();
//        return $result;
//    }
//
//    function save_permissions($user_id,$websystem_id,$permissions, $profits) {
//        $out=array('result'=>Permissions_model::ERR_FLAG, 'msg'=>Permissions_model::INIT_ERRMSG);
//        /* Select MAIN */
//        $this->db->select('websys_page_parent');
//        $this->db->from('websys_pages');
//        $this->db->where("websys_page_parent is not null");
//        $this->db->where_in('websys_page_id',$permissions);
//        $main=$this->db->get()->result_array();
//        foreach ($main as $row) {
//            if (!in_array($row['websys_page_parent'], $permissions)) {
//                array_push($permissions, $row['websys_page_parent']);
//                $this->db->select('websys_page_parent');
//                $this->db->from('websys_pages');
//                $this->db->where("websys_page_parent is not null");
//                $this->db->where_in('websys_page_id',$row['websys_page_parent']);
//                $super=$this->db->get()->result_array();
//                foreach ($super as $srow) {
//                    if (!in_array($srow['websys_page_parent'], $permissions)) {
//                        array_push($permissions, $srow['websys_page_parent']);
//                    }
//                }
//            }
//        }
//
//        $this->db->select('websys_page_id');
//        $this->db->from('websys_pages');
//        $this->db->where('websystem_id',$websystem_id);
//        $webp=$this->db->get()->result_array();
//
//        $webpages=array();
//        foreach ($webp as $row) {
//            array_push($webpages,$row['websys_page_id']);
//        }
//        /* Delete old permissions */
//        $this->db->where('user_id',$user_id);
//        $this->db->where_in('websys_page_id',$webpages);
//        $this->db->delete('user_permissions');
//        /* Insert new values */
//        foreach ($permissions as $row) {
//            $this->db->set('websys_page_id',$row);
//            $this->db->set('user_id',$user_id);
//            $this->db->set('permission_type',1);
//            $this->db->insert('user_permissions');
//        }
//        /* Update profit */
//        foreach ($profits as $prow) {
//            $this->db->where('websys_page_id',$prow['websys_page_id']);
//            $this->db->where('user_id',$user_id);
//            $this->db->set('profit_view',$prow['profit_view']);
//            $this->db->update('user_permissions');
//        }
//        $out['result']=Permissions_model::SUCCESS_RESULT;
//        return $out;
//
//    }
//
//    function save_permissions_role($role_id,$websystem_id,$permissions) {
//        $out=array('result'=>Permissions_model::ERR_FLAG, 'msg'=>Permissions_model::INIT_ERRMSG);
//        /* Select MAIN */
//        $this->db->select('websys_page_parent');
//        $this->db->from('websys_pages');
//        $this->db->where("websys_page_parent is not null");
//        $this->db->where_in('websys_page_id',$permissions);
//        $main=$this->db->get()->result_array();
//        foreach ($main as $row) {
//            if (!in_array($row['websys_page_parent'], $permissions)) {
//                array_push($permissions, $row['websys_page_parent']);
//            }
//        }
//        $this->db->select('websys_page_id');
//        $this->db->from('websys_pages');
//        $this->db->where('websystem_id',$websystem_id);
//        $webp=$this->db->get()->result_array();
//
//        $webpages=array();
//        foreach ($webp as $row) {
//            array_push($webpages,$row['websys_page_id']);
//        }
//        /* Delete old permissions */
//        $this->db->where('role_id',$role_id);
//        $this->db->where_in('websys_page_id',$webpages);
//        $this->db->delete('role_permissions');
//        /* Insert new values */
//        foreach ($permissions as $row) {
//            $this->db->set('websys_page_id',$row);
//            $this->db->set('role_id',$role_id);
//            $this->db->set('permission_type','WRITE');
//            $this->db->insert('role_permissions');
//        }
//        $out['result']=Permissions_model::SUCCESS_RESULT;
//        return $out;
//    }
//
//    /* Save Role */
//    function save_role($roledat) {
//        $out=array('result'=>Permissions_model::ERR_FLAG,'msg'=>Permissions_model::INIT_ERRMSG);
//        if (empty($roledat['role_name'])) {
//            $out['msg']='Enter Role Name';
//        } elseif (empty($roledat['role_short'])) {
//            $out['msg']='Enter Short Role Name';
//        } elseif(empty($roledat['role_description'])) {
//            $out['msg']='Enter Role Description';
//        } else {
//            $this->db->set('role_name',$roledat['role_name']);
//            $this->db->set('role_short',$roledat['role_short']);
//            $this->db->set('role_description',$roledat['role_description']);
//            $this->db->set('role_keytext',$roledat['role_keytext']);
//            if ($roledat['role_id']) {
//                $this->db->where('role_id',$roledat['role_id']);
//                $this->db->update('roles');
//                $out['result']=Permissions_model::SUCCESS_RESULT;
//                $out['msg']='';
//            } else {
//                $this->db->insert('roles');
//                if ($this->db->insert_id()==0) {
//                    $out['msg']='Error during insert data into Roles';
//                } else {
//                    $roledat['role_id']=$this->db->insert_id();
//                    $out['result']=Permissions_model::SUCCESS_RESULT;
//                    $out['msg']='';
//                    if ($roledat['roleselect']) {
//                        /*  */
//                        $this->copy_role_permissions($roledat['roleselect'], $roledat['role_id']);
//                    }
//                }
//            }
//        }
//        return $out;
//    }
//
//    function copy_role_permissions($source_roleid, $target_roleid) {
//        $this->db->select('*');
//        $this->db->from('role_permissions');
//        $this->db->where('role_id',$source_roleid);
//        $res=$this->db->get()->result_array();
//        foreach ($res as $row) {
//            $this->db->set('websys_page_id',$row['websys_page_id']);
//            $this->db->set('role_id',$target_roleid);
//            $this->db->set('permission_type',$row['permission_type']);
//            $this->db->insert('role_permissions');
//        }
//        return TRUE;
//    }
//
//    function delete_role($role_id) {
//        $this->db->where('role_id',$role_id);
//        $this->db->delete('roles');
//        if ($this->db->affected_rows()==0) {
//            $retval=Permissions_model::ERR_FLAG;
//        } else {
//            $retval=Permissions_model::SUCCESS_RESULT;
//        }
//        return $retval;
//    }
//
//    // Prepare list of pages and Main site
//    public function get_websyspages() {
//        // Get list of main branches
//        $this->db->select('*');
//        $this->db->from('websys_pages');
//        $this->db->where('websys_page_parent is null');
//        $this->db->where('websys_page_link is not null');
//        $this->db->order_by('websys_pageorder');
//        $main=$this->db->get()->result_array();
//        $out=array();
//        foreach ($main as $mrow) {
//            $out[]=array(
//                'key'=>$mrow['websys_page_id'],
//                'label'=>$mrow['websys_page_name'],
//            );
//            // Get subpages
//            $this->db->select('*');
//            $this->db->from('websys_pages');
//            $this->db->where('websys_page_parent', $mrow['websys_page_id']);
//            $this->db->where('websys_page_link is not null');
//            $this->db->order_by('websys_pageorder');
//            $pages=$this->db->get()->result_array();
//            foreach ($pages as $row) {
//                $out[]=array(
//                    'key'=>$row['websys_page_id'],
//                    'label'=>' &ndash; '.$row['websys_page_name'],
//                );
//            }
//        }
//        return $out;
//    }
//
//    public function get_profit_userpage($websys_page_id, $user_id) {
//        $this->load->model('user_model');
//        $out='Points';
//        $usrdata=$this->user_model->get_user_data($user_id);
//        if (isset($usrdata['user_id'])) {
//            $this->db->select('user_permission_id, profit_view');
//            $this->db->from('user_permissions');
//            $this->db->where('websys_page_id', $websys_page_id);
//            $this->db->where('user_id', $user_id);
//            $res=$this->db->get()->row_array();
//            $out=$usrdata['profit_view'];
//            if (isset($res['user_permission_id'])){
//                if (!empty($res['profit_view'])) {
//                    $out=$res['profit_view'];
//                }
//            }
//        }
//        return $out;
//    }

    public function get_pageprofit_view($user_id, $item_lnk) {

//        $this->db->select('rp.websys_page_id, rp.profit_view, wps.websys_page_link');
//        $this->db->from('websys_pages wp');
//        $this->db->join('websys_pages wps','wps.websys_page_parent=wp.websys_page_id');
//        $this->db->join('websystems w','w.websystem_id=wp.websystem_id');
//        $this->db->join('user_permissions rp','wps.websys_page_id=rp.websys_page_id');
//        $this->db->where('wp.websys_page_link',$item_lnk);
//        $this->db->where('w.websystem_shortname',  $this->WEBSYS);
//        $this->db->where('rp.user_id',$user_id);
//        $res=$this->db->get()->result_array();
        $res =[
            ['user_permission_id'=>4794,'user_id'=>1,'websys_page_id'=>89,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalescustoms',],
            ['user_permission_id'=>4795,'user_id'=>1,'websys_page_id'=>90,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesstock',],
            ['user_permission_id'=>4796,'user_id'=>1,'websys_page_id'=>91,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesariel',],
            ['user_permission_id'=>4797,'user_id'=>1,'websys_page_id'=>92,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesalpi',],
            ['user_permission_id'=>4798,'user_id'=>1,'websys_page_id'=>93,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesmailine',],
            ['user_permission_id'=>4799,'user_id'=>1,'websys_page_id'=>94,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesother',],
            ['user_permission_id'=>4800,'user_id'=>1,'websys_page_id'=>95,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsaleshit',],
            ['user_permission_id'=>4800,'user_id'=>1,'websys_page_id'=>96,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'itemsalesesp',],
            // ['user_permission_id'=>4795,'user_id'=>1,'websys_page_id'=>90,'permission_type'=>1,'profit_view'=>'Profit','websys_page_link'=>'',],
        ];

        return $res;

    }

}