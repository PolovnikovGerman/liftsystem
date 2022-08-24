<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Itemimages_model extends My_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_images_item($item_id,$limit=10,$offset=0) {
        $this->db->select('*');
        $this->db->from('sb_item_images');
        $this->db->where('item_img_item_id',$item_id);
        $this->db->order_by('item_img_order');
        $this->db->limit($limit,$offset);
        $result = $this->db->get()->result_array();
        $ret_img = array();
        if ($offset==0) {
            $offset=1;
        }
        $num_rec=$limit+$offset;
        for ($i=$offset;$i<$num_rec;$i++) {
            $ind=0;
            if ($i==1) {
                $name_img='Main Pic';
            } else {
                $name_img='Pic '.$i;
            }
            foreach ($result as $row) {
                if ($row['item_img_order']==$i) {
                    $ind=$row['item_img_id'];
                    $ret_img[]=array('item_img_id'=>$row['item_img_id']
                    ,'item_img_item_id'=>$row['item_img_item_id']
                    ,'src'=>$row['item_img_name']
                    ,'name'=>$name_img
                    ,'item_img_order'=>$row['item_img_order']);
                    break;
                }
            }
            if ($ind==0) {
                $ret_img[]=array('item_img_id'=>(-1)*$i
                ,'item_img_item_id'=>''
                , 'src'=>''
                ,'name'=>$name_img
                ,'item_img_order'=>$i);
            }
        }
        return $ret_img;
    }

//    public function update_images($item_images,$item_url,$item_id)  {
//        $pathsrc=$this->config->item('upload_path_preload');
//        $pathsrc_sh=$this->config->item('upload_preload');
//        $pathdest=$this->config->item('item_images');
//
//        $thumb_width=250;
//        $thumb_heigh=260;
//        $media_width=800;
//        $media_height=800;
//        $template_name=str_replace('.html', '', $item_url);
//
//        foreach ($item_images as $row) {
//            if (intval($row['id'])==0) {
//                /* Not emty content */
//                if (isset($row['src']) and $row['src']!='') {
//                    $imgname=$row['src'];
//                    /* Get Name of Upload file */
//                    $filename=str_replace($pathsrc_sh,'',$imgname);
//                    $filecontent=$pathsrc.$filename;
//                    if (file_get_contents($filecontent)) {
//                        $filedet=$this->func->extract_filename($filename);
//                        $this->db->set('item_img_item_id',$item_id);
//                        $this->db->set('item_img_order',$row['order_num']);
//                        $this->db->insert('sb_item_images');
//                        $new_id=$this->db->insert_id();
//                        if ($new_id>0) {
//                            /* Get File Extension */
//                            $dest_path=$pathsrc.$filename;
//                            $dest_path=str_replace('//','/',$dest_path);
//                            /* Full Image */
//                            $target_path=$pathdest.$template_name.'_'.$new_id.'.'.$filedet['ext'];
//                            $target_path=str_replace('//','/',$target_path);
//                            // Save Main image
//                            $this->func->resizecmd($dest_path, $target_path, $media_width, $media_height);
//                            $img_name=str_replace($pathdest,'/uploads/items_images/',$target_path);
//                            /* Thumbnail */
//                            $target_path=$pathdest.$template_name.'_'.$new_id.'_thumb.'.$filedet['ext'];
//                            $target_path=str_replace('//','/',$target_path);
//                            $this->func->resizecmd($dest_path, $target_path, $thumb_width, $thumb_heigh);
//                            $img_thumb=str_replace($pathdest,'/uploads/items_images/',$target_path);
//                            /* Delete Preload Image */
//                            unlink($dest_path);
//                            /* Update DB */
//                            $this->db->where('item_img_id', $new_id);
//                            $this->db->set('item_img_name',$img_name);
//                            $this->db->set('item_img_thumb',$img_thumb);
//                            $this->db->update('sb_item_images');
//                        }
//                    }
//                }
//            } else {
//                // Image was changed
//                $this->db->select('item_img_name, item_img_thumb, item_img_order');
//                $this->db->from('sb_item_images');
//                $this->db->where('item_img_id',$row['id']);
//                $res=$this->db->get()->row_array();
//                if ($row['src']=='') {
//                    /* Image was deleted */
//                    if (isset($res['item_img_name']) && !empty($row['item_img_name'])) {
//                        $imgname=$res['item_img_name'];
//                        $filename=str_replace('/uploads/items_images/','',$imgname);
//                        /* Get Full Path to image */
//                        $filename=$pathdest.$filename;
//                        $filename=str_replace('//','/',$filename);
//                        @unlink($filename);
//                    }
//                    if (isset($res['item_img_thumb']) && !empty($row['item_img_thumb'])) {
//                        $imgname=$res['item_img_thumb'];
//                        $filename=str_replace('/uploads/items_images/','',$imgname);
//                        /* Get Full Path to image */
//                        $filename=$pathdest.$filename;
//                        $filename=str_replace('//','/',$filename);
//                        @unlink($filename);
//                    }
//                    $this->db->where('item_img_id',$row['id']);
//                    $this->db->delete('sb_item_images');
//                } else {
//                    /* Update Image */
//                    $filename=$row['src'];
//                    if ($filename!=$res['item_img_name']) {
//                        // Add As new
//                        if (isset($res['item_img_name']) && !empty($row['item_img_name'])) {
//                            $imgname=$res['item_img_name'];
//                            $filename=str_replace('/uploads/items_images/','',$imgname);
//                            /* Get Full Path to image */
//                            $filename=$pathdest.$filename;
//                            $filename=str_replace('//','/',$filename);
//                            @unlink($filename);
//                        }
//                        if (isset($res['item_img_thumb']) && !empty($row['item_img_thumb'])) {
//                            $imgname=$res['item_img_thumb'];
//                            $filename=str_replace('/uploads/items_images/','',$imgname);
//                            /* Get Full Path to image */
//                            $filename=$pathdest.$filename;
//                            $filename=str_replace('//','/',$filename);
//                            @unlink($filename);
//                        }
//                        $this->db->where('item_img_id',$row['id']);
//                        $this->db->delete('sb_item_images');
//                        // Add New
//                        $imgname=$row['src'];
//                        /* Get Name of Upload file */
//                        $filename=str_replace($pathsrc_sh,'',$imgname);
//                        $filecontent=$pathsrc.$filename;
//                        if (file_get_contents($filecontent)) {
//                            $filedet=$this->func->extract_filename($filename);
//                            $this->db->set('item_img_item_id',$item_id);
//                            $this->db->set('item_img_order',$row['order_num']);
//                            $this->db->insert('sb_item_images');
//                            $new_id=$this->db->insert_id();
//                            if ($new_id>0) {
//                                /* Get File Extension */
//                                $dest_path=$pathsrc.$filename;
//                                $dest_path=str_replace('//','/',$dest_path);
//                                /* Full Image */
//                                $target_path=$pathdest.$template_name.'_'.$new_id.'.'.$filedet['ext'];
//                                $target_path=str_replace('//','/',$target_path);
//                                // Save Main image
//                                $this->func->resizecmd($dest_path, $target_path, $media_width, $media_height);
//                                $img_name=str_replace($pathdest,'/uploads/items_images/',$target_path);
//                                /* Thumbnail */
//                                $target_path=$pathdest.$template_name.'_'.$new_id.'_thumb.'.$filedet['ext'];
//                                $target_path=str_replace('//','/',$target_path);
//                                $this->func->resizecmd($dest_path, $target_path, $thumb_width, $thumb_heigh);
//                                $img_thumb=str_replace($pathdest,'/uploads/items_images/',$target_path);
//                                /* Delete Preload Image */
//                                unlink($dest_path);
//                                /* Update DB */
//                                $this->db->where('item_img_id', $new_id);
//                                $this->db->set('item_img_name',$img_name);
//                                $this->db->set('item_img_thumb',$img_thumb);
//                                $this->db->update('sb_item_images');
//                            }
//                        }
//                    } else {
//                        // Just update Order Num
//                        $this->db->where('item_img_id',$row['id']);
//                        $this->db->set('item_img_order',$row['order_num']);
//                        $this->db->update('sb_item_images');
//                    }
//                }
//            }
//        }
//
//    }
//
//    function get_count_images($item_id) {
//        $this->db->select('count(*) as cnt');
//        $this->db->from('sb_item_images');
//        $this->db->where('item_img_item_id',$item_id);
//        $res=$this->db->get()->row_array();
//
//        return $res['cnt'];
//    }

    public function get_item_images($item_id,$limit=10,$offset=0) {
        $this->db->select('*');
        $this->db->from('sb_item_images');
        $this->db->where('item_img_item_id',$item_id);
        $this->db->order_by('item_img_order');
        $this->db->limit($limit,$offset);
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_itemlist_images($item_id) {
        $this->db->select('*');
        $this->db->from('sb_item_images');
        $this->db->where('item_img_item_id', $item_id);
        $this->db->order_by('item_img_order');
        $images = $this->db->get()->result_array();
        return $images;
    }


//    function get_item_media($item_id,$mediatype) {
//        $this->db->select('*');
//        $this->db->from('sb_item_media');
//        $this->db->where('item_id',$item_id);
//        $this->db->where('itemmedia_type',$mediatype);
//        $res=$this->db->get()->row_array();
//        return $res;
//    }
//
//    function get_faces() {
//        $this->db->select('fc.face_category_id, fc.face_category_name, f.face_view, f.face_name');
//        $this->db->from('sb_face_categories fc');
//        $this->db->join('sb_faces f','f.face_category_id=fc.face_category_id');
//        $this->db->order_by('fc.face_category_id');
//        $res=$this->db->get()->result_array();
//        $faces=array();
//        $curcat=0;
//        $nrow=0;
//        $face=array();
//        foreach ($res as $row) {
//            if ($nrow==15) {
//                $nrow=0;
//                $faces[]=$face;
//                $face=array();
//            }
//            $face[]=array(
//                'category_name'=>$row['face_category_name'],
//                'face_url'=>($row['face_view']=='' ? '/img/no-camera.png' : $row['face_view']),
//                'face_name'=>$row['face_name'],
//            );
//            $nrow++;
//        }
//        if (count($face)>0) {
//            $faces[]=$face;
//        }
//        return $faces;
//    }
//
//    // For mobile
//    public function get_mobile_enlargeimage($item_img_id) {
//        $result=array('result'=>0, 'msg'=>'Item Image not Found');
//
//        $this->db->select('ii.*, i.item_name, i.item_number');
//        $this->db->from('sb_item_images ii');
//        $this->db->join('sb_items i','i.item_id=ii.item_img_item_id');
//        $this->db->where('ii.item_img_id', $item_img_id);
//        $mainimg=$this->db->get()->row_array();
//        if (isset($mainimg['item_img_id'])) {
//            // Item Image found
//            $result['result']=1;
//            $result['main']=$mainimg;
//            // List other
//            $item_id=$mainimg['item_img_item_id'];
//            $limit=$this->get_count_images($item_id);
//            $result['other_images']=$this->get_item_images($item_id, $limit);
//        }
//        return $result;
//    }

    public function get_itemoption_images($item_id) {
        $this->db->select('*');
        $this->db->from('sb_itemoption_images');
        $this->db->where('item_id', $item_id);
        $res = $this->db->get()->result_array();
        return $res;
    }
}