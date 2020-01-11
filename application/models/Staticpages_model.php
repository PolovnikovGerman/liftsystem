<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Staticpages_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function get_metadata($page_name) {
        $this->db->select('meta_title,meta_keywords,meta_description,bottom_text,rollover_help, internal_keywords');
        $this->db->select('page_name, page_id');
        $this->db->from('sb_static_pages');
        $this->db->where('page_name',$page_name);
        $res = $this->db->get()->row_array();
        if (!array_key_exists('meta_title',$res)) {
            $result=array(
                'meta_title'=>$this->config->item('meta_title'),
                'meta_keywords'=>$this->config->item('meta_keywords'),
                'meta_description'=>$this->config->item('meta_description'),
                'bottom_text'=>$this->config->item('bottom_text'),
                'rollover_help'=>1,
            );
        } else {
            $result=$res;
        }
        return $result;
    }

    public function get_page_inner_content($page_name) {
        $this->db->select('*');
        $this->db->from('sb_static_contents');
        $this->db->where('page_name', $page_name);
        $res  = $this->db->get()->result_array();
        $content = [];
        foreach ($res as $row) {
            $content[$row['content_parameter']]=$row['content_value'];
        }
        return $content;
    }

    public function get_custom_galleries() {
        // For custom Shaped page
        $this->db->select('*');
        $this->db->from('sb_custom_galleries');
        $this->db->where('gallery_delete',0);
        $this->db->order_by('gallery_order');
        $categ = $this->db->get()->result_array();
        $out = [];
        $numpp = 1;
        foreach ($categ as $item) {
            $this->db->select('*');
            $this->db->from('sb_custom_galleryitems');
            $this->db->where('custom_gallery_id', $item['custom_gallery_id']);
            $this->db->where('item_deleted',0);
            $this->db->order_by('item_order');
            $dataitems = $this->db->get()->result_array();
            $item['count_items'] = count($dataitems);
            $item['items']=$dataitems;
            $item['numpp'] = $numpp;
            $out[]=$item;
            $numpp++;
        }
        return $out;
    }

    public function get_case_study() {
        $this->db->select('*');
        $this->db->from('sb_custom_casestudy');
        $this->db->where('casestudy_delete',0);
        $this->db->order_by('casestudy_order');
        $res = $this->db->get()->result_array();
        $out=$res;
        if (count($res)<$this->config->item('max_slider_casestudy')) {
            for ($i=count($res)+1; $i<=$this->config->item('max_slider_casestudy'); $i++) {
                $out[]=[
                    'custom_casestudy_id' => ($i)*(-1),
                    'casestudy_image' => '',
                    'casestudy_title' => '',
                    'casestudy_text' => '',
                    'casestudy_expand' => '',
                    'casestudy_order' => $i,
                ];
            }
        }
        return $out;
    }

}