<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Imprints_model extends My_Model
{

    private $IMPRINT_NUMBER=12;

    function __construct()
    {
        parent::__construct();
    }

    public function get_imprint_item($item_id) {
        $this->db->select('*');
        $this->db->from('sb_item_inprints');
        $this->db->where('item_inprint_item',$item_id);
        $this->db->order_by('item_inprint_id');
        $result = $this->db->get()->result_array();
        $out=array();
        foreach ($result as $row) {
            $row['item_inprint_location']=htmlspecialchars($row['item_inprint_location']);
            $row['item_inprint_size']=htmlspecialchars($row['item_inprint_size']);
            $out[]=$row;
        }
        return $out;
    }
//
//    public function get_imprint_itemimages($item_id) {
//        $this->db->select('*');
//        $this->db->from('sb_item_inprints');
//        $this->db->where('item_inprint_item',$item_id);
//        $this->db->where('item_inprint_view is not null');
//        $this->db->order_by('item_inprint_id');
//        $result = $this->db->get()->result_array();
//        $out=array();
//        foreach ($result as $row) {
//            $row['item_inprint_location']=htmlspecialchars($row['item_inprint_location']);
//            $row['item_inprint_size']=htmlspecialchars($row['item_inprint_size']);
//            $out[]=$row;
//        }
//        return $out;
//    }
//
//
//    /* Function for edit  */
//    function get_imprint_edit_item($item_id) {
//        $this->db->select('*');
//        $this->db->from('sb_item_inprints');
//        $this->db->where('item_inprint_item',$item_id);
//        $this->db->order_by('item_inprint_id');
//        $result = $this->db->get()->result_array();
//        $out_res=array();
//        for ($i=0;$i<Imprints_model::IMPRINT_NUMBER;$i++) {
//            if (isset ($result[$i]['item_inprint_id'])) {
//                if ($result[$i]['item_inprint_view']=='') {
//                    $view='';
//                } else {
//                    $view=$result[$i]['item_inprint_view'];
//                }
//                $out_res[$i]=array(
//                    'item_inprint_id'=>$result[$i]['item_inprint_id'],
//                    'item_inprint_location'=>  htmlspecialchars($result[$i]['item_inprint_location']),
//                    'item_inprint_size'=>  htmlspecialchars($result[$i]['item_inprint_size']),
//                    'item_inprint_view'=>$view
//                );
//            } else {
//                $out_res[$i]=array(
//                    'item_inprint_id'=>0,
//                    'item_inprint_location'=>'',
//                    'item_inprint_size'=>'',
//                    'item_inprint_view'=>''
//                );
//            }
//        }
//        return $out_res;
//    }
//
//    function insert_locations($item_inprint,$item_id, $old_imprint_update) {
//        $updflag=0;
//        foreach ($item_inprint as $row) {
//            /* Check IMG */
//            if ($row['item_inprint_location']=='' && intval($row['id'])!=0) {
//                $this->db->select('item_inprint_view');
//                $this->db->from('sb_item_inprints');
//                $this->db->where('item_inprint_id',$row['id']);
//                $img=$this->db->get()->row_array();
//                /* Get Name of Image */
//                $filename=str_replace('/uploads/items_imprint/','',$img['item_inprint_view']);
//                $pathsrc=$this->config->item('item_imprint_view');
//                $pathsrc=str_replace('//','/',$pathsrc);
//                @unlink($pathsrc.$filename);
//                /* Delete Location */
//                $this->db->where('item_inprint_id',$row['id']);
//                $this->db->delete('sb_item_inprints');
//            } elseif ($row['item_inprint_location']!='') {
//                $imgname=$row['item_inprint_view'];
//                if (strpos($imgname, 'preload')) {
//                    /* New IMG */
//                    $filename=str_replace('/uploads/preload/','',$imgname);
//                    $pathsrc=$this->config->item('upload_path_preload');
//                    $pathdest=$this->config->item('item_imprint_view');
//                    copy($pathsrc.$filename, $pathdest.$filename);
//                    unlink($pathsrc.$filename);
//                    $this->db->set('item_inprint_view','/uploads/items_imprint/'.$filename);
//                    $updflag=1;
//                }
//                $this->db->set('item_inprint_location',$row['item_inprint_location']);
//                $this->db->set('item_inprint_size',$row['item_inprint_size']);
//                if ($row['id']==0) {
//                    $this->db->set('item_inprint_item',$item_id);
//                    $this->db->insert('sb_item_inprints');
//                } else {
//                    $this->db->where('item_inprint_id',$row['id']);
//                    $this->db->update('sb_item_inprints');
//                }
//
//            }
//        }
//        if ($updflag==1 && $old_imprint_update==0) {
//            $this->db->set('imprint_update', 1);
//            $this->db->where('item_id', $item_id);
//            $this->db->update('sb_items');
//        }
//        return TRUE;
//    }
//
//    function update_location($item_inprint,$item_id) {
//        foreach ($item_inprint as $row) {
//            /* Check IMG */
//            if ($row['item_inprint_view']!='') {
//                $imgname=$row['item_inprint_view'];
//                if (strpos($imgname, 'preload')) {
//                    /* New IMG */
//
//                    $filename=str_replace('/uploads/preload/','',$imgname);
//                    $pathsrc=$this->config->item('upload_path_preload');
//                    $pathdest=$this->config->item('item_imprint_view');
//
//                    copy($pathsrc.$filename, $pathdest.$filename);
//                    unlink($pathsrc.$filename);
//                    $this->db->set('item_inprint_view','/uploads/items_imprint/'.$filename);
//                }
//            }
//            switch ($row['deeds']) {
//                case 'insert':
//                    $this->db->set('item_inprint_location',$row['item_inprint_location']);
//                    $this->db->set('item_inprint_size',$row['item_inprint_size']);
//                    $this->db->set('item_inprint_item',$item_id);
//                    $this->db->insert('sb_item_inprints');
//                    break;
//                case 'update':
//                    $this->db->set('item_inprint_location',$row['item_inprint_location']);
//                    $this->db->set('item_inprint_size',$row['item_inprint_size']);
//                    $this->db->where('item_inprint_id',$row['id']);
//                    $this->db->update('sb_item_inprints');
//                    break;
//                case 'delete':
//                    /* Select image for delete */
//                    $this->db->select('item_inprint_view');
//                    $this->db->from('sb_item_inprints');
//                    $this->db->where('item_inprint_id',$row['id']);
//                    $img=$this->db->get()->row_array();
//                    /* Get Name of Image */
//                    $filename=str_replace('/uploads/items_imprint/','',$img['item_inprint_view']);
//                    $pathsrc=$this->config->item('item_imprint_view');
//                    unlink($pathsrc.$filename);
//                    /* Delete Location */
//                    $this->db->where('item_inprint_id',$row['id']);
//                    $this->db->delete('sb_item_inprints');
//                    break;
//            }
//        }
//    }
//
//    function get_imprint_byid($imprint_id) {
//        $this->db->select('*');
//        $this->db->from('sb_item_inprints');
//        $this->db->where('item_inprint_id',$imprint_id);
//        $res=$this->db->get()->row_array();
//        return $res;
//
//    }
//
//    /* Import Imprints Values */
//    function import_imprints($imprints, $item_id) {
//        $num_import=count($imprints);
//        $this->db->select('item_inprint_id, item_inprint_location');
//        $this->db->from('sb_item_inprints');
//        $this->db->where('item_inprint_item',$item_id);
//        $results=$this->db->get()->result_array();
//        foreach ($results as $row) {
//            /* Check - may be such imprint exist */
//            $find=0;
//            for($i=0; $i<$num_import;$i++) {
//                if ($row['item_inprint_location']==$imprints[$i]['item_inprint_location']) {
//                    $imprints[$i]['item_inprint_id']=$row['item_inprint_id'];
//                    $find=$row['item_inprint_id'];
//                    break;
//                }
//            }
//            if (!$find) {
//                $this->db->where('item_inprint_id',$row['item_inprint_id']);
//                $this->db->delete('sb_item_inprints');
//            }
//        }
//        foreach ($imprints as $row) {
//            $this->db->set('item_inprint_item',$row['item_inprint_item']);
//            $this->db->set('item_inprint_location',$row['item_inprint_location']);
//            $this->db->set('item_inprint_size',$row['item_inprint_size']);
//            if ($row['item_inprint_id']) {
//                $this->db->where('item_inprint_id',$row['item_inprint_id']);
//                $this->db->update('sb_item_inprints');
//            } else {
//                $this->db->insert('sb_item_inprints');
//            }
//        }
//        return TRUE;
//    }
//
//    public function count_mobileimprint_item($item_id) {
//        $this->db->select('count(item_inprint_id) as cnt');
//        $this->db->from('sb_item_inprints');
//        $this->db->where('item_inprint_item',$item_id);
//        $this->db->order_by('item_inprint_id');
//        $result = $this->db->get()->row_array();
//        return $result['cnt'];
//    }
//
//    public function get_mobileimprint_item($item_id, $limit=2, $offset=0) {
//        $this->db->select('*');
//        $this->db->from('sb_item_inprints');
//        $this->db->where('item_inprint_item',$item_id);
//        $this->db->order_by('item_inprint_id');
//        $this->db->limit($limit, $offset);
//        $result = $this->db->get()->result_array();
//        $out=array();
//        foreach ($result as $row) {
//            $row['item_inprint_location']=htmlspecialchars($row['item_inprint_location']);
//            $row['item_inprint_size']=htmlspecialchars($row['item_inprint_size']);
//            $out[]=$row;
//        }
//        return $out;
//    }

}