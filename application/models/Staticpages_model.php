<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

Class Staticpages_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    function get_metadata($page_name, $brand) {
        $this->db->select('meta_title,meta_keywords,meta_description,bottom_text,rollover_help, internal_keywords');
        $this->db->select('page_name, page_id');
        $this->db->from('sb_static_pages');
        $this->db->where('page_name',$page_name);
        $this->db->where('brand', $brand);
        $res = $this->db->get()->row_array();
        if (empty($res) || !array_key_exists('meta_title',$res)) {
            $result=array(
                'meta_title'=>'', // $this->config->item('meta_title'),
                'meta_keywords'=>'', // $this->config->item('meta_keywords'),
                'meta_description'=>'', //$this->config->item('meta_description'),
                'bottom_text'=>'', $this->config->item('bottom_text'),
                'rollover_help'=>1,
                'page_name' => $page_name,
                'internal_keywords' => '',
                'page_id' => -1,
            );
        } else {
            $result=$res;
        }
        return $result;
    }

    public function get_page_inner_content($page_name, $brand) {
        $this->db->select('*');
        $this->db->from('sb_static_contents');
        $this->db->where('page_name', $page_name);
        $this->db->where('brand', $brand);
        $res  = $this->db->get()->result_array();
        $content = [];
        foreach ($res as $row) {
            $content[$row['content_parameter']]=$row['content_value'];
        }
        return $content;
    }

    public function get_custom_galleries($brand) {
        // For custom Shaped page
        $this->db->select('*');
        $this->db->from('sb_custom_galleries');
        $this->db->where('gallery_delete',0);
        $this->db->where('brand', $brand);
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

    public function get_case_study($brand) {
        $this->db->select('*');
        $this->db->from('sb_custom_casestudy');
        $this->db->where('casestudy_delete',0);
        $this->db->where('brand', $brand);
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

    public function update_customshaped_param($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['type'])) {
            if ($postdata['type']=='data') {
                $data = $session_data['data'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='meta') {
                $data=$session_data['meta'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['meta']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='gallery') {
                $out['msg']='Gallery Not Found';
                $data = $session_data['galleries'];
                $found=0;
                $idx=0;
                foreach ($data as $row) {
                    if ($row['custom_gallery_id']==$postdata['custom_gallery_id']) {
                        $found=1;
                        break;
                    }
                    $idx++;
                }
                if ($found==1) {
                    $data[$idx][$postdata['field']]=$postdata['newval'];
                    $session_data['galleries']=$data;
                    usersession($session_id, $session_data);
                    $out['result']=$this->success_result;
                }
            } elseif ($postdata['type']=='casestudy') {
                $out['msg']='Gallery Not Found';
                $data = $session_data['case_study'];
                $found=0;
                $idx=0;
                foreach ($data as $row) {
                    if ($row['custom_casestudy_id']==$postdata['custom_casestudy_id']) {
                        $found=1;
                        break;
                    }
                    $idx++;
                }
                if ($found==1) {
                    $data[$idx][$postdata['field']]=$postdata['newval'];
                    $session_data['case_study']=$data;
                    usersession($session_id, $session_data);
                    $out['result']=$this->success_result;
                }
            }
        }
        return $out;
    }

    public function save_customshape_imageupload($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['imagetype'])) {
            if ($postdata['imagetype']=='custom_mainimage') {
                $data = $session_data['data'];
                $data['custom_mainimage']=$postdata['imagesrc'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['custom_mainimage']=$data['custom_mainimage'];
            } elseif ($postdata['imagetype']=='custom_homepageimage') {
                $data = $session_data['data'];
                $data['custom_homepageimage']=$postdata['imagesrc'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['custom_homepageimage']=$data['custom_homepageimage'];
            } elseif ($postdata['imagetype']=='gallery_image') {
                $out['msg']='Gallery Not Found';
                $data = $session_data['galleries'];
                $custom_gallery_id = $postdata['imageorder'];
                $idx = 0;
                $found=0;
                foreach ($data as $row) {
                    if ($row['custom_gallery_id']==$custom_gallery_id) {
                        $found=1;
                        break;
                    }
                    $idx++;
                }
                if ($found==1) {
                    $items=$data[$idx]['items'];
                    $numitems = count($items);
                    $items[]=[
                        'custom_galleryitem_id' => ($numitems+1)*(-1),
                        'custom_gallery_id' => $custom_gallery_id,
                        'item_order'=> ($numitems+1),
                        'item_source' => $postdata['imagesrc'],
                    ];
                    $data[$idx]['items']=$items;
                    $data[$idx]['count_items']=$numitems+1;
                    $session_data['galleries']=$data;
                    usersession($session_id, $session_data);
                    $out['result']=$this->success_result;
                    $out['galleries']=$data;
                }
            } elseif ($postdata['imagetype']=='casestudy_image') {
                $out['msg']='Case Study Not Found';
                $data = $session_data['case_study'];
                $custom_casestudy_id = $postdata['imageorder'];
                $idx = 0;
                $found=0;
                foreach ($data as $row) {
                    if ($row['custom_casestudy_id']==$custom_casestudy_id) {
                        $found=1;
                        break;
                    }
                    $idx++;
                }
                if ($found==1) {
                    $data[$idx]['casestudy_image']=$postdata['imagesrc'];
                    $session_data['case_study']=$data;
                    usersession($session_id, $session_data);
                    $out['result']=$this->success_result;
                    $out['case_study']=$data;
                }
            }
        }
        return $out;
    }

    public function remove_customgalleryitem($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Gallery Not Found'];
        $data = $session_data['galleries'];
        $idx = 0;
        $found = 0;
        foreach ($data as $row) {
            if ($row['custom_gallery_id']==$postdata['custom_gallery_id']) {
                $found=1;
                break;
            }
            $idx++;
        }
        if ($found==1) {
            $items = $data[$idx]['items'];
            $newitem = [];
            $numpp=1;
            foreach ($items as $item) {
                if ($item['custom_galleryitem_id']!=$postdata['custom_galleryitem_id']) {
                    $item['item_order']=$numpp;
                    $newitem[]=$item;
                    $numpp++;
                }
            }
            $data[$idx]['items']=$newitem;
            $data[$idx]['count_items']=count($newitem);
            $session_data['galleries']=$data;
            if ($postdata['custom_galleryitem_id']>0) {
                $deleted = $session_data['deleted'];
                $deleted[]=[
                    'table' => 'sb_custom_galleryitems',
                    'id' => $postdata['custom_galleryitem_id'],
                ];
                $session_data['deleted']=$deleted;
            }
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
            $out['galleries'] = $data;
        }
        return $out;
    }

    public function add_customgallery($session_data, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Gallery Not Found'];
        $data = $session_data['galleries'];
        $minid = 0;
        foreach ($data as $row) {
            if ($row['custom_gallery_id']<$minid) {
                $minid=$row['custom_gallery_id'];
            }
        }
        $minid=$minid-1;
        $data[]=[
            'custom_gallery_id' => $minid,
            'gallery_name' => '',
            'gallery_order' => count($data)+1,
            'gallery_show' => 0,
            'numpp' => count($data)+1,
            'items' => [],
            'count_items' => 0,
        ];
        $session_data['galleries']=$data;
        usersession($session_id, $session_data);
        $out['result']=$this->success_result;
        $out['galleries'] = $data;
        return $out;
    }

    public function remove_customcasestudy($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Case Study Not Found'];
        $data = $session_data['case_study'];
        $found=0;
        $minid=0;
        $newcase = [];
        foreach ($data as $row) {
            if ($row['custom_casestudy_id']==$postdata['custom_casestudy_id']) {
                $found=1;
            } else {
                $newcase[]=$row;
                $minid=($row['custom_casestudy_id']<$minid ? $row['custom_casestudy_id'] : $minid);
            }
        }

        if ($found==1) {
            $newcase[]=[
                'custom_casestudy_id' => $minid-1,
                'casestudy_image'=>'',
                'casestudy_title' =>'',
                'casestudy_text' =>'',
                'casestudy_expand'=>'',
                'casestudy_order' => count($newcase)+1,
            ];
            $session_data['case_study']=$newcase;
            if ($postdata['custom_casestudy_id']>0) {
                $deleted=$session_data['deleted'];
                $deleted[]=[
                    'table' => 'sb_custom_casestudy',
                    'id' => $postdata['custom_casestudy_id'],
                ];
                $session_data['deleted']=$deleted;
            }
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
            $out['case_study'] = $newcase;
        }
        return $out;
    }

    public function remove_customgallery($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Case Study Not Found'];
        $data = $session_data['galleries'];
        $found=0;
        $newcase = [];
        foreach ($data as $row) {
            if ($row['custom_gallery_id']==$postdata['custom_gallery_id']) {
                $found=1;
            } else {
                $newcase[]=$row;
            }
        }

        if ($found==1) {
            $session_data['galleries']=$newcase;
            if ($postdata['custom_gallery_id']>0) {
                $deleted=$session_data['deleted'];
                $deleted[]=[
                    'table' => 'sb_custom_galleries',
                    'id' => $postdata['custom_gallery_id'],
                ];
                $session_data['deleted']=$deleted;
            }
            usersession($session_id, $session_data);
            $out['result']=$this->success_result;
            $out['galleries'] = $newcase;
        }
        return $out;
    }

    public function save_customshaped($session_data, $postdata, $session_id, $brand, $user) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        $meta=$session_data['meta'];
        $data = $session_data['data'];
        $galleries = $session_data['galleries'];
        $case_study = $session_data['case_study'];
        $deleted = $session_data['deleted'];
        // Check data
        // Save data
        // Prepare folders for galleries
        $full_path = $this->config->item('gallery_images_relative');
        if (!file_exists($full_path)) {
            mkdir($full_path, 0777, true);
        }
        $full_path = $this->config->item('casestudy_images_relative');
        if (!file_exists($full_path)) {
            mkdir($full_path, 0777, true);
        }
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        // Meta
        $this->_save_page_metadata($meta, $brand);
        // check an images - custom_mainimage custom_homepageimage
        if (!empty(ifset($data,'custom_mainimage'))) {
            if ($data['custom_mainimage'] && stripos($data['custom_mainimage'],$this->config->item('upload_preload'))!==FALSE) {
                // Save image
                $full_path = $this->config->item('contents_images_relative');
                if (!file_exists($full_path)) {
                    mkdir($full_path, 0777, true);
                }
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $data['custom_mainimage']);
                $imagedetails = $this->func->extract_filename($data['custom_mainimage']);
                $filename = 'custom_mainimage_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                $data['custom_mainimage']='';
                if ($res) {
                    $data['custom_mainimage']=$this->config->item('contents_images').$filename;
                }
            }
        }
        if (!empty(ifset($data,'custom_homepageimage'))) {
            if ($data['custom_homepageimage'] && stripos($data['custom_homepageimage'],$this->config->item('upload_preload'))!==FALSE) {
                // Save image
                $full_path = $this->config->item('contents_images_relative');
                if (!file_exists($full_path)) {
                    mkdir($full_path, 0777, true);
                }
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $data['custom_homepageimage']);
                $imagedetails = $this->func->extract_filename($data['custom_homepageimage']);
                $filename = 'custom_homepageimage_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                $data['custom_homepageimage']='';
                if ($res) {
                    $data['custom_homepageimage']=$this->config->item('contents_images').$filename;
                }
            }
        }
        // Static content
        $this->_save_page_params($data, 'custom', $brand, $user);
        // Gallery
        foreach ($galleries as $gallery) {
            $gallery_id = $gallery['custom_gallery_id'];
            $this->db->set('update_user', $user);
            $this->db->set('gallery_name', $gallery['gallery_name']);
            $this->db->set('gallery_order', $gallery['gallery_order']);
            $this->db->set('gallery_show', $gallery['gallery_show']);
            if ($gallery['custom_gallery_id']<0) {
                $this->db->set('create_user', $user);
                $this->db->set('create_date', date('Y-m-d H:i:s'));
                $this->db->set('brand', $brand);
                $this->db->insert('sb_custom_galleries');
                $gallery_id = $this->db->insert_id();
            } else {
                $this->db->set('update_user',$user);
                $this->db->where('custom_gallery_id', $gallery_id);
                $this->db->update('sb_custom_galleries');
            }
            if ($gallery_id>0 ) {
                // Insert / update images
                $name = urlencode(str_replace([" ","&","'"],'',$gallery['gallery_name']));
                $numpp=1;
                foreach ($gallery['items'] as $item) {
                    if ($item['custom_galleryitem_id']<0) {
                        // New element
                        $imagesrc = str_replace($path_preload_short, $path_preload_full, $item['item_source']);
                        $imagedetails = extract_filename($item['item_source']);
                        $filename = $name.'_'.$numpp.'_'.time().'.'.$imagedetails['ext'];
                        $res = @copy($imagesrc, $this->config->item('gallery_images_relative').$filename);
                        if ($res) {
                            $this->db->set('custom_gallery_id', $gallery_id);
                            $this->db->set('create_user', $user);
                            $this->db->set('create_time', date('Y-m-d H:i:s'));
                            $this->db->set('update_user', $user);
                            $this->db->set('item_source', $this->config->item('gallery_images').$filename);
                            $this->db->set('item_order', $item['item_order']);
                            $this->db->insert('sb_custom_galleryitems');
                        }
                    }
                    $numpp++;
                }
            }
        }
        // Case study
        foreach ($case_study as $item) {
            if ($item['custom_casestudy_id']>0 && (empty($item['casestudy_title']) || empty($item['casestudy_image']))) {
                $this->db->where('custom_casestudy_id', $item['custom_casestudy_id']);
                $this->db->set('casestudy_delete',1);
                $this->db->set('updated', $user);
                $this->db->update('sb_custom_casestudy');
            } else {
                if (!empty($item['casestudy_image']) && !empty($item['casestudy_title'])) {
                    $custom_casestudy_id = $item['custom_casestudy_id'];
                    $this->db->set('updated', $user);
                    $this->db->set('casestudy_title',$item['casestudy_title']);
                    $this->db->set('casestudy_text', $item['casestudy_text']);
                    $this->db->set('casestudy_expand', $item['casestudy_expand']);
                    if ($item['custom_casestudy_id']<0) {
                        $this->db->set('create_date', date('Y-m-d H:i:s'));
                        $this->db->set('created', $user);
                        $this->db->set('brand', $brand);
                        $this->db->insert('sb_custom_casestudy');
                        $custom_casestudy_id = $this->db->insert_id();
                    } else {
                        $this->db->where('custom_casestudy_id', $custom_casestudy_id);
                        $this->db->update('sb_custom_casestudy');
                    }
                    if ($custom_casestudy_id>0 && stripos($item['casestudy_image'],$path_preload_short)!==FALSE) {
                        $imagesrc = str_replace($path_preload_short, $path_preload_full, $item['casestudy_image']);
                        $imagedetails = extract_filename($data['custom_homepageimage']);
                        $filename = 'casestudy_'.$custom_casestudy_id.'_'.time().'.'.$imagedetails['ext'];
                        $res = @copy($imagesrc, $this->config->item('casestudy_images_relative').$filename);
                        if ($res) {
                            $this->db->where('custom_casestudy_id', $custom_casestudy_id);
                            $this->db->set('casestudy_image', $this->config->item('casestudy_images').$filename);
                            $this->db->update('sb_custom_casestudy');
                        }
                    }
                }
            }
        }
        // Delete
        if (count($deleted)>0) {
            foreach ($deleted as $row) {
                if ($row['table']=='sb_custom_galleryitems') {
                    $this->db->where('custom_galleryitem_id', $row['id']);
                    $this->db->set('item_deleted',1);
                    $this->db->update($row['table']);
                } elseif ($row['table']=='sb_custom_casestudy') {
                    $this->db->where('custom_casestudy_id', $row['id']);
                    $this->db->set('casestudy_delete',1);
                    $this->db->update($row['table']);
                } elseif ($row['table']=='sb_custom_galleries') {
                    $this->db->where('custom_gallery_id', $row['id']);
                    $this->db->set('gallery_delete',1);
                    $this->db->update($row['table']);
                    // Update items
                    $this->db->where('custom_gallery_id', $row['id']);
                    $this->db->set('item_deleted',1);
                    $this->db->update('sb_custom_galleryitems');
                }
            }
        }
        $out['result']=$this->success_result;
        return $out;
    }

    public function update_faqparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['type'])) {
            if ($postdata['type']=='data') {
                $data = $session_data['data'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='meta') {
                $data=$session_data['meta'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['meta']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='faq_section') {
                $out['msg'] = 'FAQ Section Not Found';
                $data = $session_data['faq_sections'];
                $found = 0;
                $idx = 0;
                foreach ($data as $row) {
                    if ($row['faq_section'] == $postdata['faq_section']) {
                        $found = 1;
                        break;
                    }
                    $idx++;
                }
                if ($found == 1) {
                    $questions = $data[$idx]['questions'];
                    $out['msg'] = 'Question Not Found';
                    $qidx = 0;
                    $qfound = 0;
                    foreach ($questions as $qrow) {
                        if ($qrow['faq_id'] == $postdata['faq_id']) {
                            $qfound = 1;
                            break;
                        }
                        $qidx++;
                    }
                    if ($qfound == 1) {
                        $questions[$qidx][$postdata['field']] = $postdata['newval'];
                        $data[$idx]['questions'] = $questions;
                        $session_data['faq_sections'] = $data;
                        usersession($session_id, $session_data);
                        $out['result'] = $this->success_result;
                    }
                }
            }
        }
        return $out;
    }

    public function add_faqquestion($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['faq_section'])) {
            $data = $session_data['faq_sections'];
            $idx=0;
            $found=0;
            foreach ($data as $item) {
                if ($item['faq_section']==$postdata['faq_section']) {
                    $found=1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $questions=ifset($data[$idx],'questions',[]);
                $minidx=0;
                foreach ($questions as $qrow) {
                    if ($qrow['faq_id']<$minidx) {
                        $minidx=$qrow['faq_id'];
                    }
                }
                $minidx-=1;
                $questions[]=[
                    'faq_id' => $minidx,
                    'faq_section' => $postdata['faq_section'],
                    'faq_quest' => '',
                    'faq_answ' => '',
                ];
                $data[$idx]['questions']=$questions;
                $session_data['faq_sections']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['faq_section'] = $data[$idx];
            }
        }
        return $out;
    }

    public function remove_faqquestion($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['faq_section'])) {
            $data = $session_data['faq_sections'];
            $idx=0;
            $found=0;
            foreach ($data as $item) {
                if ($item['faq_section']==$postdata['faq_section']) {
                    $found=1;
                    break;
                }
                $idx++;
            }
            if ($found==1) {
                $questions=$data[$idx]['questions'];
                $newquest = [];
                foreach ($questions as $qrow) {
                    if ($qrow['faq_id']!=$postdata['faq_id']) {
                        $newquest[]=$qrow;
                    }
                }
                $data[$idx]['questions']=$newquest;
                $session_data['faq_sections']=$data;
                if ($postdata['faq_id']>0) {
                    $deleted = $session_data['deleted'];
                    $deleted[]=[
                        'table' => 'sb_faq',
                        'id' => $postdata['faq_id'],
                    ];
                    $session_data['deleted']=$deleted;
                }
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
                $out['faq_section'] = $data[$idx];
            }
        }
        return $out;
    }

    public function save_faqpagecontent($session_data, $session_id, $brand, $user) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        $meta=$session_data['meta'];
        $data = $session_data['data'];
        $faq_sections = $session_data['faq_sections'];
        $deleted = $session_data['deleted'];
        // Meta
        $this->_save_page_metadata($meta, $brand);
        // Static content
        $this->_save_page_params($data, 'faq', $brand, $user);
        // Faq sections
        foreach ($faq_sections as $row) {
            $questions = ifset($row,'questions',[]);
            foreach ($questions as $qrow) {
                $this->db->set('faq_quest', $qrow['faq_quest']);
                $this->db->set('faq_answ', $qrow['faq_answ']);
                if ($qrow['faq_id']>0) {
                    $this->db->where('faq_id', $qrow['faq_id']);
                    $this->db->update('sb_faq');
                } else {
                    $this->db->set('faq_section', $row['faq_section']);
                    $this->db->set('brand', $brand);
                    $this->db->insert('sb_faq');
                }
            }
        }
        // Delete
        if (count($deleted)>0) {
            foreach ($deleted as $row) {
                if ($row['table'] == 'sb_faq') {
                    $this->db->where('faq_id', $row['id']);
                    $this->db->delete('sb_faq');
                }
            }
        }
        usersession($session_id,null);
        $out['result']=$this->success_result;
        return $out;
    }

    public function update_termsparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['type'])) {
            if ($postdata['type']=='data') {
                $data = $session_data['data'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='meta') {
                $data=$session_data['meta'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['meta']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='terms') {
                $out['msg'] = 'Term Not Found';
                $data = $session_data['terms'];
                $found = 0;
                $idx = 0;
                foreach ($data as $row) {
                    if ($row['term_id'] == $postdata['term_id']) {
                        $found = 1;
                        break;
                    }
                    $idx++;
                }
                if ($found == 1) {
                    $data[$idx][$postdata['field']]=$postdata['newval'];
                    $session_data['terms'] = $data;
                    usersession($session_id, $session_data);
                    $out['result'] = $this->success_result;
                    $out['terms'] = $data;
                }
            }
        }
        return $out;
    }

    public function remove_termsparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Term Not Found'];
        $data = $session_data['terms'];
        $found = 0;
        $newterms = [];
        foreach ($data as $row) {
            if ($row['term_id']== $postdata['term_id']) {
                $found = 1;
            } else {
                $newterms[]=$row;
            }
        }
        if ($found == 1) {
            $session_data['terms'] = $newterms;
            if ($postdata['term_id']>0) {
                $deleted=$session_data['deleted'];
                $deleted[]=[
                    'table' => 'sb_terms',
                    'id' => $postdata['term_id'],
                ];
                $session_data['deleted']=$deleted;
            }
            usersession($session_id, $session_data);
            $out['result'] = $this->success_result;
            $out['terms'] = $newterms;
        }
        return $out;
    }

    public function add_termsparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Term Not Found'];
        $data = ifset($session_data,'terms',[]);
        $found = 0;
        $minid = 0;
        $maxorder = 0;
        foreach ($data as $row) {
            if ($row['term_id'] < $minid) {
                $minid = $row['term_id'];
            }
            if ($row['term_order']>$maxorder) {
                $maxorder = $row['term_order'];
            }
        }
        $minid = $minid - 1;
        $maxorder = $maxorder+1;
        $data[] = [
            'term_id' => $minid,
            'term_order' => $maxorder,
            'term_header' => '',
            'term_text' => '',
        ];
        $session_data['terms'] = $data;
        usersession($session_id, $session_data);
        $out['result'] = $this->success_result;
        $out['terms'] = $data;
        return $out;
    }

    public function edit_termsparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Term Not Found'];
        $data = $session_data['terms'];
        $found = 0;
        $newterms = [];
        foreach ($data as $row) {
            if ($row['term_id']== $postdata['term_id']) {
                $found = 1;
                $newterms=$row;
                break;
            }
        }
        if ($found == 1) {
            usersession($session_id, $session_data);
            $out['result'] = $this->success_result;
            $out['terms'] = $newterms;
        }
        return $out;
    }

    public function saveedit_termsparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Term Not Found'];
        $data = $session_data['terms'];
        $found = 0;
        $idx = 0;
        $newterms = [];
        foreach ($data as $row) {
            if ($row['term_id']== $postdata['term_id']) {
                $found = 1;
                $newterms=$row;
                break;
            }
            $idx++;
        }
        if ($found == 1) {
            $newterms['term_text'] = $postdata['newcontent'];
            $data[$idx]=$newterms;
            $session_data['terms']=$data;
            usersession($session_id, $session_data);
            $out['result'] = $this->success_result;
            $out['terms'] = $newterms;
        }
        return $out;
    }

    public function save_termspagecontent($session_data, $session_id, $brand,  $user) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        $meta=$session_data['meta'];
        $data = $session_data['data'];
        $terms = ifset($session_data,'terms',[]);
        $deleted = $session_data['deleted'];
        // Meta
        $this->_save_page_metadata($meta, $brand);
        // Static content
        $this->_save_page_params($data, 'terms', $brand, $user);
        // Faq sections
        foreach ($terms as $row) {
            $this->db->set('term_header', $row['term_header']);
            $this->db->set('term_text', $row['term_text']);
            if ($row['term_id']<0) {
                $this->db->set('brand', $brand);
                $this->db->insert('sb_terms');
            } else {
                $this->db->where('term_id', $row['term_id']);
                $this->db->update('sb_terms');
            }
        }
        // Delete
        if (count($deleted)>0) {
            foreach ($deleted as $row) {
                if ($row['table'] == 'sb_terms') {
                    $this->db->where('term_id', $row['id']);
                    $this->db->delete('sb_terms');
                }
            }
        }
        usersession($session_id,null);
        $out['result']=$this->success_result;
        return $out;
    }

    public function update_aboutparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['type'])) {
            if ($postdata['type']=='meta') {
                $data = $session_data['meta'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['meta'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='data') {
                $data = $session_data['data'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['data'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='address') {
                $data = $session_data['address'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['address'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='affilate_img') {
                $data = $session_data['data'];
                $paramname = 'about_affilationsrc'.$postdata['imageorder'];
                $data[$paramname]=$postdata['imagesrc'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='main_image') {
                $data = $session_data['data'];
                $data[$postdata['field']]=$postdata['newval'];
                $session_data['data']=$data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function save_aboutus($session_data,  $session_id, $brand,  $user) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        $meta=$session_data['meta'];
        $data = $session_data['data'];
        $address = $session_data['address'];
        // Prepare folder for images
        $full_path = $this->config->item('contents_images_relative');
        if (!file_exists($full_path)) {
            mkdir($full_path, 0777, true);
        }
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        // Meta
        $this->_save_page_metadata($meta, $brand);
        if (!empty(ifset($data,'about_mainimage'))) {
            if ($data['about_mainimage'] && stripos($data['about_mainimage'],$path_preload_short)!==FALSE) {
                // Save image
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $data['about_mainimage']);
                $imagedetails = extract_filename($data['about_mainimage']);
                $filename = 'about_mainimage_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                $data['about_mainimage']='';
                if ($res) {
                    $data['about_mainimage']=$this->config->item('contents_images').$filename;
                }
            }
        }
        if (!empty(ifset($data,'about_affilationsrc1'))) {
            if ($data['about_affilationsrc1'] && stripos($data['about_affilationsrc1'],$path_preload_short)!==FALSE) {
                // Save image
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $data['about_affilationsrc1']);
                $imagedetails = extract_filename($data['about_affilationsrc1']);
                $filename = 'about_affilationsrc_1_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                $data['about_affilationsrc1']='';
                if ($res) {
                    $data['about_affilationsrc1']=$this->config->item('contents_images').$filename;
                }
            }
        }
        if (!empty(ifset($data,'about_affilationsrc2'))) {
            if ($data['about_affilationsrc2'] && stripos($data['about_affilationsrc2'],$path_preload_short)!==FALSE) {
                // Save image
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $data['about_affilationsrc2']);
                $imagedetails = $this->func->extract_filename($data['about_affilationsrc2']);
                $filename = 'about_affilationsrc_2_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                $data['about_affilationsrc2']='';
                if ($res) {
                    $data['about_affilationsrc2']=$this->config->item('contents_images').$filename;
                }
            }
        }
        // Static content
        $this->_save_page_params($data, 'about', $brand, $user);
        $this->_save_page_params($address, 'address', $brand, $user);
        usersession($session_id,null);
        $out['result']=$this->success_result;
        return $out;
    }

    public function update_contactusparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['type'])) {
            if ($postdata['type']=='meta') {
                $data = $session_data['meta'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['meta'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='data') {
                $data = $session_data['data'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['data'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='address') {
                $data = $session_data['address'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['address'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function save_contactus($session_data,  $session_id, $brand, $user) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        $meta=$session_data['meta'];
        $data = $session_data['data'];
        $address = $session_data['address'];
        // Meta
        $this->_save_page_metadata($meta, $brand);
        // Static content
        $this->_save_page_params($data, 'contactus', $brand, $user);
        $this->_save_page_params($address, 'address', $brand, $user);
        usersession($session_id,null);
        $out['result']=$this->success_result;
        return $out;
    }

    public function change_serviceparam($session_data, $postdata, $session_id) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        if (isset($postdata['type'])) {
            if ($postdata['type']=='meta') {
                $data = $session_data['meta'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['meta'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            } elseif ($postdata['type']=='data') {
                $data = $session_data['data'];
                $data[$postdata['field']] = $postdata['newval'];
                $session_data['data'] = $data;
                usersession($session_id, $session_data);
                $out['result'] = $this->success_result;
            }
        }
        return $out;
    }

    public function save_extraservice($session_data,  $session_id, $brand, $user) {
        $out=['result' => $this->error_result, 'msg' => 'Not all params send'];
        $meta=$session_data['meta'];
        $data = $session_data['data'];
        // Prepare folder for images
        $full_path = $this->config->item('contents_images_relative');
        if (!file_exists($full_path)) {
            mkdir($full_path, 0777, true);
        }
        $path_preload_short = $this->config->item('pathpreload');
        $path_preload_full = $this->config->item('upload_path_preload');
        // Meta

        // Meta
        $this->_save_page_metadata($meta, $brand);
        // Static content
        if (!empty(ifset($data,'service_mainimage'))) {
            if ($data['service_mainimage'] && stripos($data['service_mainimage'],$path_preload_short)!==FALSE) {
                // Save image
                $imagesrc = str_replace($path_preload_short, $path_preload_full, $data['service_mainimage']);
                $imagedetails = extract_filename($data['service_mainimage']);
                $filename = 'service_mainimage_'.time().'.'.$imagedetails['ext'];
                $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                $data['service_mainimage']='';
                if ($res) {
                    $data['service_mainimage']=$this->config->item('contents_images').$filename;
                }
            }
        }
        // Services images
        for ($j=1; $j<9; $j++) {
            $imagename = 'service_image'.$j;
            if (!empty(ifset($data,$imagename))) {
                if ($data[$imagename] && stripos($data[$imagename],$path_preload_short)!==FALSE) {
                    // Save image
                    $imagesrc = str_replace($path_preload_short, $path_preload_full, $data[$imagename]);
                    $imagedetails = extract_filename($data[$imagename]);
                    $filename = 'service_image_'.$j.'_'.time().'.'.$imagedetails['ext'];
                    $res = @copy($imagesrc, $this->config->item('contents_images_relative').$filename);
                    $data[$imagename]='';
                    if ($res) {
                        $data[$imagename]=$this->config->item('contents_images').$filename;
                    }
                }
            }
        }
        // Static content
        $this->_save_page_params($data, 'extraservice', $brand, $user);
        usersession($session_id,null);
        $out['result']=$this->success_result;
        return $out;
    }

    public function get_faq_sections($brand) {
        $this->db->select('faq_section, count(faq_id) as cnt',FALSE);
        $this->db->from('sb_faq');
        $this->db->where('brand', $brand);
        $this->db->group_by('faq_section');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_faq($options=array()) {
        $this->db->select('*',FALSE);
        $this->db->from('sb_faq');
        if (isset($options['faq_section'])) {
            $this->db->where('faq_section',$options['faq_section']);
        }
        if (isset($options['brand'])) {
            $this->db->where('brand', $options['brand']);
        }
        if (isset($options['order_by'])) {
            $this->db->order_by($options['order_by']);
        }
        if (isset($options['limit']) && $options['limit']) {
            $this->db->limit($options['limit']);
        }
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_terms($brand) {
        $this->db->select('*',FALSE);
        $this->db->from('sb_terms');
        $this->db->where('brand', $brand);
        $this->db->order_by('term_order, term_id');
        $result = $this->db->get()->result_array();
        return $result;
    }

    public function get_term_details($term_id) {
        $this->db->select('*');
        $this->db->from('sb_terms');
        $this->db->where('term_id',$term_id);
        $res=$this->db->get()->row_array();
        return $res;
    }

    public function save_terms($data) {
        $out=array('result'=>0, 'msg'=>'Unknown Error. Try Later');
        if (empty($data['term_title'])) {
            $out['msg']='Enter Term Title';
        } else {
            //$data['term_text']=htmlspecialchars($data['textarea']);
            $data['term_text']=$data['textarea'];
            if ($data['term_id']==0) {
                $maxord=$this->max_term_order();
            }
            $this->db->set('term_header',$data['term_title']);
            $this->db->set('term_text',$data['term_text']);
            if ($data['term_id']==0) {
                $this->db->set('term_order',$maxord);
                $this->db->insert('sb_terms');
                if ($this->db->insert_id()==0) {
                    $out['msg']='Error during insert Data. Try Later';
                } else {
                    $out['result']=1;
                    $out['msg']='';
                }
            } else {
                $this->db->where('term_id',$data['term_id']);
                $this->db->update('sb_terms');
                $out['result']=1;
                $out['msg']='';
            }
        }
        return $out;
    }

    private function max_term_order() {
        $this->db->select('max(term_order) as val');
        $this->db->from('sb_terms');
        $res=$this->db->get()->row_array();
        if (!isset($res['val'])) {
            $out_val=0;
        } else {
            $out_val=$res['val'];
        }
        $out_val++;
        return $out_val;
    }

    public function delete_term($term_id) {
        $this->db->where('term_id',$term_id);
        $this->db->delete('sb_terms');
        if ($this->db->affected_rows()==0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    private function _save_page_metadata($meta, $brand) {
        $this->db->set('meta_title', $meta['meta_title']);
        $this->db->set('meta_keywords', $meta['meta_keywords']);
        $this->db->set('meta_description', $meta['meta_description']);
        $this->db->set('internal_keywords', $meta['internal_keywords']);
        if ($meta['page_id']<0) {
            $this->db->set('page_name', $meta['page_name']);
            $this->db->set('brand',$brand);
           $this->db->insert('sb_static_pages');
        } else {
            $this->db->where('page_id', $meta['page_id']);
            $this->db->update('sb_static_pages');
        }

    }

    private function _save_page_params($data, $page_name,  $brand, $user) {
        foreach ($data as $key=>$val) {
            $chk = $this->_check_static_content($key, $page_name, $brand);
            $this->db->set('last_update', $user);
            $this->db->set('content_value', $val);
            if ($chk) {
                $this->db->where('static_content_id', $chk);
                $this->db->update('sb_static_contents');
            } else {
                $this->db->set('brand', $brand);
                $this->db->set('content_parameter', $key);
                $this->db->set('page_name', $page_name);
                $this->db->insert('sb_static_contents');
            }
        }
        return TRUE;
    }

    private function _check_static_content($key, $page_name, $brand) {
        $out_key = 0;
        $this->db->select('max(static_content_id) as content_id, count(static_content_id) cnt');
        $this->db->from('sb_static_contents');
        $this->db->where('page_name', $page_name);
        $this->db->where('brand', $brand);
        $this->db->where('content_parameter', $key);
        $res = $this->db->get()->row_array();
        if ($res['cnt']==1) {
            $out_key = $res['content_id'];
        }
        return $out_key;
    }


}