<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Itemcategory_model extends My_Model
{

    function __construct()
    {
        parent::__construct();
    }

    public function get_item_categories($options=array(),$order='i.item_id',$direc='asc',$limit=0,$offset=0,$search_template='',$vendor_id='') {
        $this->db->select('i.item_id, i.item_number, i.item_name, unix_timestamp(i.update_time) as updtime, count(ic.item_categories_id) as cnt',FALSE);
        $this->db->from('sb_items i');
        $this->db->join('sb_item_categories ic','ic.item_categories_itemid=i.item_id','left');

        if ($vendor_id) {
            $this->db->join('sb_vendor_items v','v.vendor_item_id=i.vendor_item_id');
            $this->db->where('v.vendor_item_vendor',$vendor_id);
        }

        foreach ($options as $key=>$value) {
            $this->db->where($key,$value);
        }
        if ($search_template!='') {
            $where = "lower(concat(i.item_number,i.item_name)) like '%".strtolower($search_template)."%'";
            $this->db->where($where);
        }
        $this->db->group_by('i.item_id, i.item_number, i.item_name, updtime');
        if ($order=='count_up' || $order=='count_dwn') {
            $this->db->order_by('count(ic.item_categories_id) ',$direc);
        } else {
            $this->db->order_by($order,$direc);
        }

        if ($limit) {
            $this->db->limit($limit,$offset);
        }
        $result=$this->db->get()->result_array();
        $curtime=time();
        $diff=86400;
        $out=array();
        foreach ($result as $row) {
            $row['itemnameclass']='';
            if ($curtime-$row['updtime']<$diff) {
                $row['itemnameclass']='nearlyupdate';
            }
            $categories=array();
            for ($i=0;$i<6;$i++) {
                $categories[]=array('category'=>'','recid'=>'');
            }
            if ($row['cnt']) {
                $i=0;
                $this->db->select('ic.item_categories_id, ic.item_categories_categoryid');
                $this->db->from('sb_item_categories ic');
                $this->db->where('ic.item_categories_itemid',$row['item_id']);
                $categ=$this->db->get()->result_array();
                foreach ($categ as $cat) {
                    $categories[$i]['category']=$cat['item_categories_categoryid'];
                    $categories[$i]['recid']=$cat['item_categories_id'];
                    $i++;
                }
            }
            $row['categories']=$categories;
            $out[]=$row;
        }
        return $out;
    }

    /* Update category */
    public function upd_categ($recid,$new_val) {
        $this->db->where('item_categories_id',$recid);
        $this->db->set('item_categories_categoryid',$new_val);
        $this->db->update('sb_item_categories');
        return $this->db->affected_rows();
    }

    /* Delete category */
    function del_categ($recid) {
        $this->db->where('item_categories_id',$recid);
        $this->db->delete('sb_item_categories');
        return $this->db->affected_rows();
    }

    /* Insert new category */
    public function ins_categ($item_id,$el_val) {
        $this->db->set('item_categories_itemid',$item_id);
        $this->db->set('item_categories_categoryid',$el_val);
        $this->db->insert('sb_item_categories');
        return $this->db->insert_id();
    }

    public function chk_itemcateg($item_id,$el_val) {
        $this->db->select('count(*) as cnt',FALSE);
        $this->db->from('sb_item_categories');
        $this->db->where('item_categories_itemid',$item_id);
        $this->db->where('item_categories_categoryid',$el_val);
        $res=$this->db->get()->row_array();
        return $res['cnt'];
    }

    public function check_updcateg($recid,$el_val) {
        /* select data about item category */
        $this->db->select('item_categories_itemid, item_categories_categoryid');
        $this->db->from('sb_item_categories');
        $this->db->where('item_categories_id',$recid);
        $cat_res=$this->db->get()->row_array();
        /* Check unique */
        $this->db->select('count(item_categories_id) as cnt',FALSE);
        $this->db->from('sb_item_categories');
        $this->db->where('item_categories_itemid',$cat_res['item_categories_itemid']);
        $this->db->where('item_categories_categoryid',$el_val);
        $res=$this->db->get()->row_array();
        if ($res['cnt']==0) {
            return $res['cnt'];
        } else {
            return $cat_res['item_categories_categoryid'];
        }


    }

//    /* Get Items for category */
//    function get_category_items($category_id) {
//        $this->db->select("ic.item_categories_order,ic.item_categories_id,  i.item_id, i.item_name");
//        $this->db->from("sb_item_categories ic");
//        $this->db->join("sb_items i","ic.item_categories_itemid=i.item_id");
//        $this->db->where("ic.item_categories_categoryid",$category_id);
//        $this->db->order_by("ic.item_categories_order");
//        $result=$this->db->get()->result_array();
//        $out_array=array();
//        foreach ($result as $row) {
//            /* Get First Image */
//            $this->db->select('item_img_name');
//            $this->db->from('sb_item_images');
//            $this->db->where('item_img_item_id',$row['item_id']);
//            $this->db->where('item_img_order',1);
//            $img=$this->db->get()->row_array();
//            if (isset($img['item_img_name'])) {
//                if ($img['item_img_name']=='') {
//                    $img_name='/img/no-camera.png';
//                } else {
//                    $img_name=$img['item_img_name'];
//                }
//            } else {
//                $img_name='/img/no-camera.png';
//            }
//            $row['img_name']=$img_name;
//            $out_array[]=$row;
//        }
//        return $out_array;
//    }
//
//    function get_item_category($item_id) {
//        $this->db->select("ic.item_categories_order,ic.item_categories_id,  cat.category_name, cat.category_url");
//        $this->db->from("sb_item_categories ic");
//        $this->db->join("sb_categories cat","ic.item_categories_categoryid=cat.category_id");
//        $this->db->where("ic.item_categories_itemid",$item_id);
//        $this->db->where('cat.parent_id',NULL);
//        $this->db->order_by("ic.item_categories_order");
//        $result=$this->db->get()->result_array();
//        return $result;
//    }


}