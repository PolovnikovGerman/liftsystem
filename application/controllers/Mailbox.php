<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// change vendor/ssilence/php-imap-client/ImapClient/TypeAttachments.php
// private static $types = array('JPEG', 'JPG', 'PNG', 'GIF', 'PDF', 'X-MPEG', 'MSWORD', 'OCTET-STREAM', 'TXT', 'TEXT', 'MWORD', 'ZIP', 'MPEG', 'DBASE',
//    'ACROBAT', 'POWERPOINT', 'BMP', 'BITMAP','PPTX','VND.MS-EXCEL','VND.OPENXMLFORMATS-OFFICEDOCUMENT.SPREADSHEETML.SHEET','ILLUSTRATOR');
class Mailbox extends MY_Controller
{

    private $pagelink = '/mailbox';

    public function __construct()
    {
        parent::__construct();
        $brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink,0, $brand);
        if ($pagedat['result'] == $this->error_result) {
            show_404();
        }
        $page = $pagedat['menuitem'];
        $permdat = $this->menuitems_model->get_menuitem_userpermisiion($this->USR_ID, $page['menu_item_id']);
        if ($permdat['result'] == $this->success_result && $permdat['permission'] > 0) {
        } else {
            if ($this->isAjax()) {
                $this->ajaxResponse(array('url' => '/'), 'Your have no permission to this page');
            } else {
                redirect('/');
            }
        }
        $this->load->model('mailbox_model');
    }

    public function index() {
        $head = [];
        $head['title'] = 'Mailbox';
        $brand = $this->menuitems_model->get_current_brand();
        // $menu = $this->menuitems_model->get_itemsubmenu($this->USR_ID, $this->pagelink, $brand);
        // Get User Mailboxes
        $postboxes = $this->mailbox_model->get_user_mailboxes($this->USR_ID);
        $menu_view = $this->load->view('mailbox/postboxes_view', ['postboxes' => $postboxes], TRUE);
        $content_options = [];
        $gmaps = 0;

        $content_options['menu'] = $menu_view;
        // Add main page management
        $head['scripts'][] = array('src' => '/js/mailbox/page.js');
        $head['styles'][] = array('style' => '/css/mailbox/page.css');
        // Utils
        $head['styles'][]=array('style'=>'/css/page_view/pagination_shop.css');
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.mypagination.js');
        // DatePicker
        $head['scripts'][]=array('src'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js');
        $head['styles'][]=array('style'=>'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css');
        // Scroll panel
        $head['scripts'][] = array('src' => '/js/adminpage/jquery-scrollpanel.js');
        // File Download
        $head['scripts'][]=array('src'=>'/js/adminpage/jquery.fileDownload.js');

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
        ];
        if ($gmaps==1) {
            $options['gmaps'] = $gmaps;
        }
        $options['googlefont'] = 1;
        $dat = $this->template->prepare_pagecontent($options);
        $content_options['left_menu'] = $dat['left_menu'];
        $content_options['brand'] = $brand;
        $content_view = $this->load->view('mailbox/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $this->load->view('page/page_template_view', $dat);
    }

    public function postbox_details()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox',0);
            $error = 'Empty Postbox Parameter';
            if (!empty($postbox)) {
                $res = $this->mailbox_model->get_postbox_details($postbox);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    if ($res['active_cnt']==0) {
                        $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$res['folder_name']], true);
                    } else {
                        $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$res['active_folder']], true);
                    }
                    $folders_view = $this->load->view('mailbox/folders_view',['folders'=>$res['folders']], TRUE);
                    $messages_view = '';
                    if (count($res['messages'])>0) {
                        $messages_view = $this->_prepare_messages_view($res['messages']);
                    }
                    $folder_view = $this->load->view('mailbox/folder_detail_view',['headers_view'=>$header_view, 'messages' => $messages_view],true);
                    $options = [
                        'postbox' => $postbox,
                        'folders' => $folders_view,
                        'folder_view' => $folder_view,
                        'folder_id' => $res['active_folder'],
                    ];
                    $mdata['content'] = $this->load->view('mailbox/postbox_details_view', $options, TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function postbox_addfolder()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $postbox = ifset($postdata, 'postbox');
            $folder = ifset($postdata,'folder', '');
            $res = $this->mailbox_model->postbox_addfolder($postbox, $folder);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['content'] = $this->load->view('mailbox/custom_folders_view',['folders'=>$res['folders']], TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // View folder
    public function view_folder()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $folder = ifset($postdata,'folder', '');
            $postbox = ifset($postdata,'postbox', '');
            $postsort = ifset($postdata,'postsort','date_desc');
            $res = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $folder = $res['folder'];
                $messages = $res['messages'];
                if (count($messages)==0) {
                    $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$folder['folder_name']], true);
                } else {
                    $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$folder['folder_id']], true);
                }
                $mdata['header'] = $header_view;
                $mdata['messages'] = $this->_prepare_messages_view($messages, $postsort);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Update unread / read status
    public function update_message_readstatus()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $postbox = ifset($postdata, 'postbox', '');
            $message = ifset($postdata, 'message_id', '');
            $res = $this->mailbox_model->update_message_readstatus($message, $postbox);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                if ($res['unread']==1) {
                    $mdata['content'] = '<span class="ic-normal" data-message="'.$message.'" title="Mark As Unread"><i class="fa fa-circle-thin"></i></span>';
                    $mdata['read_state'] = 0;
                } else {
                    $mdata['content'] = '<span class="ic-blue" data-message="'.$message.'" title="Mark As Read"><i class="fa fa-circle" aria-hidden="true"></i></span>';
                    $mdata['read_state'] = 1;
                }
                // Count # of messages in folder
                $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
    // Update flagged
    public function update_message_star()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $postbox = ifset($postdata, 'postbox', '');
            $message = ifset($postdata, 'message_id', '');
            $res = $this->mailbox_model->update_message_flagged($message, $postbox);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                if ($res['unflag']==1) {
                    $mdata['content'] = '<span class="ic-orange" data-message="'.$message.'"><i class="fa fa-star" aria-hidden="true"></i></span>';
                } else {
                    $mdata['content'] = '<span class="ic-grey" data-message="'.$message.'"><i class="fa fa-star-o" aria-hidden="true"></i></span>';
                }
                $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function view_message()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $postbox = ifset($postdata, 'postbox', '');
            $message = ifset($postdata, 'message_id', '');
            $folder = ifset($postdata,'folder', 'Inbox');
            $res = $this->mailbox_model->view_message($message, $postbox);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $adrcc_view = '';
                if (!empty($res['adrcc'])) {
                    $adresses = [];
                    foreach ($res['adrcc'] as $adr) {
                        $adresses[] = $adr['address'];
                    }
                    $address = implode(',', $adresses);
                    $adrcc_view = $this->load->view('mailbox/message_copy_view',['type'=>'Cc', 'address' => $address], TRUE);
                }
                $adrbcc_view = '';
                if (!empty($res['adrbcc'])) {
                    $adresses = [];
                    foreach ($res['adrbcc'] as $adr) {
                        $adresses[] = $adr['address'];
                    }
                    $address = implode(',', $adresses);
                    $adrbcc_view = $this->load->view('mailbox/message_copy_view',['type'=>'Bcc', 'address' => $address], TRUE);
                }
                $folder_name = $res['folder'];
                if ($folder=='new') {
                    $folder_name = 'Unread';
                } elseif ($folder=='flagged') {
                    $folder_name = 'Starred';
                }
                $attachment_view = '';
                if (count($res['attachments'])>0) {
                    $attachment_view = $this->load->view('mailbox/message_attachments_view',['attachments' => $res['attachments']], TRUE);
                }
                $options = [
                    'message' => $res['message'],
                    'attachments' => $attachment_view,
                    'folder' => $folder,
                    'folder_name' => $folder_name,
                    'adrcc' => $adrcc_view,
                    'adrbcc' => $adrbcc_view,
                ];
                $mdata['content'] = $this->load->view('mailbox/message_details_view',$options, TRUE);
                $mdata['body'] = $res['message']['message_text'];
                $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function message_flag()
    {
        if ($this->isAjax()) {
            $postdata = $this->input->post();
            $mdata = [];
            $postbox = ifset($postdata, 'postbox', '');
            $message = ifset($postdata, 'message', '');
            $res = $this->mailbox_model->update_message_flagged($message, $postbox);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['flag'] = $res['unflag'];
                if ($res['unflag']==1) {
                    $mdata['content_head'] = '<span class="ic-orange"><i class="fa fa-star" aria-hidden="true"></i></span>';
                    $mdata['content'] = '<i class="fa fa-star" aria-hidden="true"></i>';
                } else {
                    $mdata['content_head'] = '<span class="ic-nonflagged"><i class="fa fa-star-o" aria-hidden="true"></i></span>';
                    $mdata['content'] = '<i class="fa fa-star-o" aria-hidden="true"></i>';
                }
                $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function messages_archive()
    {
        if ($this->isAjax()) {
            $mdata =[];
            $error = 'Empty Postbox Details';
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox', '');
            $folder = ifset($postdata,'folder', '');
            $msgsrc = ifset($postdata, 'messages','');
            $postsort = ifset($postdata,'postsort','date_desc');
            if (!empty($postbox) && !empty($folder) && !empty($msgsrc)) {
                $messages = explode(',', $msgsrc);
                $res = $this->mailbox_model->messages_archive($messages, $postbox);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $resfld = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                    $error = $resfld['msg'];
                    if ($resfld['result']==$this->success_result) {
                        $error = '';
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                        $folder = $resfld['folder'];
                        $messages = $resfld['messages'];
                        if (count($messages)==0) {
                            $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$folder['folder_name']], true);
                        } else {
                            $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$folder['folder_id']], true);
                        }
                        $mdata['header'] = $header_view;
                        $mdata['messages'] = $this->_prepare_messages_view($messages, $postsort);
                        // Count # of messages in folder
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function prepare_movemsgs() {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Details';
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox', '');
            $folder = ifset($postdata,'folder', '');
            if (!empty($postbox) && !empty($folder)) {
                $res = $this->mailbox_model->get_postbox_folderslist($postbox);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $mdata['content'] = $this->load->view('mailbox/move_messages_view',['folders'=>$res['folders'], 'current' => $folder], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Move messages to target
    public function messages_move()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Details';
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox', '');
            $folder = ifset($postdata,'folder', '');
            $msgsrc = ifset($postdata, 'messages','');
            $postsort = ifset($postdata,'postsort','date_desc');
            $target = ifset($postdata, 'target', '');
            if (!empty($postbox) && !empty($folder) && !empty($msgsrc) && !empty($target)) {
                $messages = explode(',', $msgsrc);
                $res = $this->mailbox_model->messages_move($messages, $postbox, $target);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $resfld = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                    $error = $resfld['msg'];
                    if ($resfld['result']==$this->success_result) {
                        $error = '';
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                        $folder = $resfld['folder'];
                        $messages = $resfld['messages'];
                        if (count($messages)==0) {
                            $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$folder['folder_name']], true);
                        } else {
                            $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$folder['folder_id']], true);
                        }
                        $mdata['header'] = $header_view;
                        $mdata['messages'] = $this->_prepare_messages_view($messages, $postsort);
                        // Count # of messages in folder
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function messages_delete()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Details';
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox', '');
            $folder = ifset($postdata,'folder', '');
            $msgsrc = ifset($postdata, 'messages','');
            $postsort = ifset($postdata,'postsort','date_desc');
            if (!empty($postbox) && !empty($folder) && !empty($msgsrc)) {
                $messages = explode(',', $msgsrc);
                $res = $this->mailbox_model->messages_delete($messages, $postbox, $folder);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $resfld = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                    $error = $resfld['msg'];
                    if ($resfld['result']==$this->success_result) {
                        $error = '';
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                        $folder = $resfld['folder'];
                        $messages = $resfld['messages'];
                        if (count($messages)==0) {
                            $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$folder['folder_name']], true);
                        } else {
                            $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$folder['folder_id']], true);
                        }
                        $mdata['header'] = $header_view;
                        $mdata['messages'] = $this->_prepare_messages_view($messages, $postsort);
                        // Count # of messages in folder
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    // Prepare more actions
    public function prepare_moreactions()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = '';
            $mdata['content'] = $this->load->view('mailbox/moreactions_view',[],true);
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function messages_moreactions()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Details';
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox', '');
            $folder = ifset($postdata,'folder', '');
            $msgsrc = ifset($postdata, 'messages','');
            $postsort = ifset($postdata,'postsort','date_desc');
            $flagread = ifset($postdata, 'flagread', '-1');
            $flagstar = ifset($postdata, 'flagstar', '-1');
            if (!empty($postbox) && !empty($folder) && !empty($msgsrc)) {
                $messages = explode(',', $msgsrc);
                if ($flagread>=0) {
                    $res = $this->mailbox_model->update_messages_readstatus($postbox, $messages, $flagread);
                    $error = $res['msg'];
                    if ($res['result']==$this->success_result) {
                        $error = '';
                    }
                } elseif ($flagstar>=0) {

                } else {
                    $error = 'Flag Not Send';
                }
                if (empty($error)) {
                    $resfld = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                    $error = $resfld['msg'];
                    if ($resfld['result']==$this->success_result) {
                        $error = '';
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                        $folder = $resfld['folder'];
                        $messages = $resfld['messages'];
                        if (count($messages)==0) {
                            $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$folder['folder_name']], true);
                        } else {
                            $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$folder['folder_id']], true);
                        }
                        $mdata['header'] = $header_view;
                        $mdata['messages'] = $this->_prepare_messages_view($messages, $postsort);
                        // Count # of messages in folder
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    private function _prepare_messages_view($messages, $sort='date_desc')
    {
        $curdate = date('Y-m-d');
        $today_bgn = strtotime($curdate);
        $today_end = strtotime($curdate.' 23:59:59');
        $curyear = date('Y');
        $curyear_bgn = strtotime($curyear.'-01-01');
        if ($sort=='date_desc') {
            $contant = '';
            $yesterday = date('Y-m-d', strtotime($curdate. ' - 1 day'));
            $yesterday_bgn=strtotime($yesterday);
            $yesterday_end=strtotime($yesterday.' 23:59:59');
            // Week bgn
            $weekbgn = strtotime($curdate. ' - 1 week');
            $options = [
                'messages' => $messages,
                'today_bgn' => $today_bgn,
                'today_end' => $today_end,
                'yesterday_bgn' => $yesterday_bgn,
                'yesterday_end' => $yesterday_end,
                'week_bgn' => $weekbgn,
                'year_bgn' => $curyear_bgn,
            ];
            $content = $this->load->view('mailbox/messages_dates_view', $options, TRUE);
        } else {
            $options = [
                'messages' => $messages,
                'today_bgn' => $today_bgn,
                'year_bgn' => $curyear_bgn,
            ];
            $content = $this->load->view('mailbox/messages_common_view', $options, TRUE);
        }
        return $content;
    }

}
