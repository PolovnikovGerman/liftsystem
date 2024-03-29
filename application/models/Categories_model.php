<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Categories_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_categories($filtr=array(),$order_by='category_order',$sort='asc') {
        $this->db->select('*');
        $this->db->from('sb_categories');
        $this->db->where('parent_id',NULL);
        foreach ($filtr as $key=>$val) {
            if (is_array($val)) {
                $this->db->where_in($key,$val);
            } else {
                $this->db->where($key,$val);
            }

        }
        if ($order_by!='') {
            $this->db->order_by($order_by,$sort);
        }
        $result=$this->db->get()->result_array();
        return $result;
    }

    // List for edit
    public function get_categories_list() {
        $this->db->select('*');
        $this->db->from('sb_categories');
        $this->db->where('show_dropdown',1);
        $this->db->order_by('category_order');
        $cat1 = $this->db->get()->result_array();
        $this->db->select('*');
        $this->db->from('sb_categories');
        $this->db->where('show_dropdown',2);
        $this->db->order_by('category_order');
        $cat2 = $this->db->get()->result_array();
        $categories = array_merge($cat1, $cat2);
        return $categories;
    }

    public function get_category_data($category_id) {
        $out=['result'=>0, 'msg'=>'Category Not Found'];
        $this->db->select('*');
        $this->db->from('sb_categories');
        $this->db->where('category_id', $category_id);
        $res = $this->db->get()->row_array();
        if (isset($res['category_id']) && $res['category_id']==$category_id) {
            $out['result']=1;
            $out['data']=$res;
        }
        return $out;
    }

    public function update_category_sort($data, $user_id) {
        $out=['result'=>1, 'msg'=>'', 'category_id'=>0];
        $numpp=1;
        foreach ($data as $key=>$val) {
            if ($key=='active') {
                $out['category_id']=$val;
            } else {
                $this->db->where('category_id', $val);
                $this->db->set('updated_by', $user_id);
                $this->db->set('category_order',$numpp);
                $this->db->update('sb_categories');
                $numpp++;
            }
        }
        $out['categories']=$this->get_categories_list();
        return $out;
    }

    public function change_category_content($postdata, $session_data, $session_id) {
        $out=['result'=>0,'msg'=>'Parameter Not Found'];
        if (isset($postdata['field']) && array_key_exists($postdata['field'],$session_data)) {
            $session_data[$postdata['field']]=$postdata['newval'];
            usersession($session_id, $session_data);
            $out['result']=1;
        }
        return $out;
    }


    public function update_category_content($session_data, $session_id, $user_id) {
        $out=['result'=>0, 'msg'=>'Category Not Found'];
        $chkres = $this->_check_category_content($session_data);
        if (!empty($chkres)) {
            $err_msg='';
            foreach ($chkres as $row) {
                $err_msg.=$row.PHP_EOL;
            }
            $out['msg']=$err_msg;
        } else {
            // save data
            // Images
            $full_path = $this->config->item('category_images_relative');
            if (!file_exists($full_path)) {
                mkdir($full_path, 0777, true);
            }
            $path_preload_short = $this->config->item('pathpreload');
            $path_preload_full = $this->config->item('upload_path_preload');

            if ($session_data['icon_dropdown'] && stripos($session_data['icon_dropdown'],$path_preload_short)!==FALSE) {
                // Save dropdown image
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $session_data['icon_dropdown']);
                $imagedetails = extract_filename($session_data['icon_dropdown']);

                $filename=  str_replace('.html', '', $session_data['category_url']).'_dropdown_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('category_images_relative').$filename);
                $session_data['icon_dropdown']='';
                if ($res) {
                    $session_data['icon_dropdown']=$this->config->item('category_images').$filename;
                }
            }
            if ($session_data['icon_homepage'] && stripos($session_data['icon_homepage'],$path_preload_short)!==FALSE) {
                // Save dropdown image
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $session_data['icon_homepage']);
                $imagedetails = extract_filename($session_data['icon_homepage']);

                $filename=  str_replace('.html', '', $session_data['category_url']).'_homepage_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('category_images_relative').$filename);
                $session_data['icon_homepage']='';
                if ($res) {
                    $session_data['icon_homepage']=$this->config->item('category_images').$filename;
                }
            }
            // Update params
            $this->db->set('category_url', $session_data['category_url']);
            $this->db->set('category_meta_title', $session_data['category_meta_title']);
            $this->db->set('category_meta_keywords', $session_data['category_meta_keywords']);
            $this->db->set('category_meta_description', $session_data['category_meta_description']);
            $this->db->set('category_name', $session_data['category_name']);
            $this->db->set('category_keywords', $session_data['category_keywords']);
            $this->db->set('dropdown_title', $session_data['dropdown_title']);
            $this->db->set('icon_dropdown', $session_data['icon_dropdown']);
            $this->db->set('homepage_title', $session_data['homepage_title']);
            $this->db->set('icon_homepage', $session_data['icon_homepage']);
            $this->db->set('updated_by', $user_id);
            $this->db->where('category_id', $session_data['category_id']);
            $this->db->update('sb_categories');
            $out['result']=1;
            $out['category_id']=$session_data['category_id'];
            $out['category_name']=$session_data['dropdown_title'];
            // Clean session
            usersession($session_id, null);
        }
        return $out;
    }

    private function _check_category_content($session_data) {
        $out=[];
        if (empty($session_data['category_url'])) {
            $out[]='Empty Category URL';
        } elseif (!valid_url(base_url().$session_data['category_url'])) {
            $out[]='Not Valid Category URL';
        } else {
            // Check unique
            $this->db->select('count(category_id) as cnt');
            $this->db->from('sb_categories');
            $this->db->where('category_url', $session_data['category_url']);
            $this->db->where('category_id != ', $session_data['category_id']);
            $chkres = $this->db->get()->row_array();
            if ($chkres['cnt']>0) {
                $out[]='Not Unique Category URL';
            }
        }
        if (empty($session_data['category_name'])) {
            $out[]='Empty Category Name';
        }
        if (empty($session_data['dropdown_title'])) {
            $out[]='Empty Drop Down Name';
        }
        if (empty($session_data['homepage_title'])) {
            $out[]='Empty Homepage Collage Name';
        }
        return $out;
    }

    public function get_reliver_categories($options=[]) {
        $this->db->select('*');
        $this->db->from('sr_categories');
        if (isset($options['brand'])) {
            $this->db->where('brand', $options['brand']);
        }
        if (isset($options['active'])) {
            $this->db->where('category_active', $options['active']);
        }
        $this->db->order_by('category_order');
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function activate_reliver_categories($category_id) {
        $this->db->where('category_id', $category_id);
        $this->db->set('category_active', 1);
        $this->db->update('sr_categories');
        return true;
    }

    public function get_srcategory_data($category_id) {
        $out=['result' => $this->error_result, 'msg' => 'Category Not Found'];
        $this->db->select('*');
        $this->db->from('sr_categories');
        $this->db->where('category_id', $category_id);
        $data = $this->db->get()->row_array();
        if (ifset($data,'category_id',0)==$category_id) {
            $out['result'] = $this->success_result;
            $out['data'] = $data;
        }
        return $out;
    }

    public function get_reliver_subcategories() {
        $this->db->select('*');
        $this->db->from('sr_subcategories');
        $res = $this->db->get()->result_array();
        return $res;
    }

    public function get_sbitem_categories($category_id) {
        $this->db->select('*');
        $this->db->from('sb_subcategories');
        $this->db->where('category_id', $category_id);
        return $this->db->get()->result_array();
    }

    public function addsubcategory($data, $user_id) {
        $out=['result' => $this->error_result, 'msg' => 'Category Not Found'];
        $category_id = ifset($data, 'category_id',0);
        $subcategory_code = ifset($data, 'subcateg_code', '');
        $subcategory_name = ifset($data, 'subcateg_name', '');
        if (!empty($category_id)) {
            $out['msg'] = 'Empty Subcategory Code';
            if (!empty($subcategory_code)) {
                $this->db->select('count(subcategory_id) as cnt, max(subcategory_id) as subcategory_id');
                $this->db->from('sb_subcategories');
                $this->db->where('category_id', $category_id);
                $this->db->where('code', strtoupper($subcategory_code));
                $res = $this->db->get()->row_array();
                if ($res['cnt']==0) {
                    $out['msg'] = 'Empty Subcategory Name';
                    if (!empty($subcategory_name)) {
                        $out['msg'] = 'Error during add new Subcategory';
                        $this->db->set('added_at', date('Y-m-d H:i:s'));
                        $this->db->set('added_by', $user_id);
                        $this->db->set('updated_by', $user_id);
                        $this->db->set('category_id', $category_id);
                        $this->db->set('name', $subcategory_name);
                        $this->db->set('code', strtoupper($subcategory_code));
                        $this->db->insert('sb_subcategories');
                        $newid = $this->db->insert_id();
                        if ($newid > 0) {
                            $out['result'] = $this->success_result;
                            $out['subcategory_id'] = $newid;
                        }
                    }
                } else {
                    $out['subcategory_id'] = $res['subcategory_id'];
                    $out['result'] = $this->success_result;
                }
            }
            if ($out['result']==$this->success_result) {
                $out['subcategories'] = $this->get_sbitem_categories($category_id);
            }
        }
        return $out;
    }

}