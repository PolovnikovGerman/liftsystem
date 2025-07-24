<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// insert into menu_items (item_name, item_link, menu_order, menu_section, newver, brand)
// values('Postbox','/mailbox',26,'marketsection', 1,'SB');

// change vendor/ssilence/php-imap-client/ImapClient/TypeAttachments.php
// private static $types = array('JPEG', 'JPG', 'PNG', 'GIF', 'PDF', 'X-MPEG', 'MSWORD', 'OCTET-STREAM', 'TXT', 'TEXT', 'MWORD', 'ZIP', 'MPEG', 'DBASE',
//    'ACROBAT', 'POWERPOINT', 'BMP', 'BITMAP','PPTX','VND.MS-EXCEL','VND.OPENXMLFORMATS-OFFICEDOCUMENT.SPREADSHEETML.SHEET','ILLUSTRATOR');
class Mailbox extends MY_Controller
{

    private $pagelink = '/mailbox';
    public $current_brand;

    public function __construct()
    {
        parent::__construct();
        $this->current_brand = $this->menuitems_model->get_current_brand();
        $pagedat = $this->menuitems_model->get_menuitem($this->pagelink, 0, $this->current_brand);
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
        $head['title'] = 'Postbox';
        $brand = $this->current_brand;
        $head['scripts'][] = array('src' => '/js/postbox/page.js');
        $head['styles'][] = array('style' => '/css/postbox/page.css');
        $head['styles'][] = array('style' => '/css/postbox/message.css');
        $postboxes = $this->mailbox_model->get_user_mailboxes($this->USR_ID, $this->USR_ROLE, $brand);
        $postbox = '';
        if (count($postboxes) > 0) {
            $postbox = $postboxes[0]['postbox_id'];
        }
        $menu_view = $this->load->view('postbox/postboxes_view', ['postboxes' => $postboxes], TRUE);

        $options = [
            'title' => $head['title'],
            'user_id' => $this->USR_ID,
            'user_name' => $this->USER_NAME,
            'activelnk' => $this->pagelink,
            'styles' => $head['styles'],
            'scripts' => $head['scripts'],
            'brand' => $brand,
        ];
        $dat = $this->template->prepare_pagecontent($options);
        $brandclass = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
        $content_options = [
            'brand' => $brand,
            'brandclass' => $brandclass,
            'menu_view' => $menu_view,
            'postbox' => $postbox,
        ];
        $content_view = $this->load->view('postbox/page_view', $content_options, TRUE);
        $dat['content_view'] = $content_view;
        $dat['modal_view'] = ''; // $this->load->view('accounting/modal_view',[], TRUE);
        $this->load->view('page_modern/page_template_view', $dat);
    }

    public function postbox_details()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Parameter';
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox',0);
            if (!empty($postbox)) {
                $res = $this->mailbox_model->get_postbox_details($postbox);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['folder'] = $res['active_folder'];
                    $mdata['folder_name'] = $res['folder_name'];
                    $mdata['folders_main'] = $this->load->view('postbox/folders_main_view',['folders' => $res['folders'], 'activefolder' => $res['active_folder']], TRUE);
                    $mdata['folders_other'] = $this->load->view('postbox/folders_other_view',['folders' => $res['folders']], TRUE);
                    $mdata['messages'] = $this->load->view('postbox/messages_list_view',['messages' => $res['messages']], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function view_folder()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $folder = ifset($postdata,'folder', '');
            $postbox = ifset($postdata,'postbox', '');
            $postsort = ifset($postdata,'postsort','date_desc');
            $res = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
            $error = $res['msg'];
            if ($res['result'] == $this->success_result) {
                $error = '';
                $folder = $res['folder'];
                $mdata['folder_name'] = $folder['folder_name'];
                $mdata['folder'] = $folder['folder_id'];
                $mdata['messages'] = $this->load->view('postbox/messages_list_view',['messages' => $res['messages']], TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function postbox_addfolder()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox');
            $folder = ifset($postdata,'folder', '');
            $res = $this->mailbox_model->postbox_addfolder($postbox, $folder);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['content'] = $this->load->view('postbox/folders_other_view',['folders' => $res['folders']], TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function messages_delete() {
        if ($this->isAjax()) {
            $error = 'Empty Postbox Details';
            $mdata = [];
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
                        $mdata['folders_main'] = $this->load->view('postbox/folders_main_view',['folders' => $res['folders'], 'activefolder' => $folder], TRUE);
                        $mdata['folders_other'] = $this->load->view('postbox/folders_other_view',['folders' => $res['folders'], 'activefolder' => $folder], TRUE);
                        $mdata['messages'] = $this->load->view('postbox/messages_list_view',['messages' => $resfld['messages']], TRUE);

//                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
//                        $folder = $resfld['folder'];
//                        $messages = $resfld['messages'];
//                        if (count($messages)==0) {
//                            $header_view = $this->load->view('mailbox/folder_header_empty',['folder'=>$folder['folder_name']], true);
//                        } else {
//                            $header_view = $this->load->view('mailbox/folder_header_view',['folder'=>$folder['folder_id']], true);
//                        }
//                        $mdata['header'] = $header_view;
//                        $mdata['messages'] = $this->_prepare_messages_view($messages, $postsort);
//                        // Count # of messages in folder
//                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function message_management()
    {
        if ($this->isAjax()) {
            $error = 'Empty Postbox Details';
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox','');
            $message = ifset($postdata,'message','');
            // $folder = ifset($postdata,'folder','');
            if (!empty($postbox) && !empty($message)) {
                $res = $this->mailbox_model->get_message_data($message);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $message = $res['message'];
                    $mdata['content'] = $this->load->view('postbox/message_actions_view',['message' => $message], TRUE);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function messages_read_status()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Parameter';
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox',0);
            $folder = ifset($postdata,'folder','');
            $msgsrc = ifset($postdata,'messages','');
            $postsort = ifset($postdata,'postsort','date_desc');
            $readstatus = ifset($postdata,'readstatus',0);
            if (!empty($postbox) && !empty($folder) && !empty($msgsrc)) {
                $messages = explode(',', $msgsrc);
                $resstat = $this->mailbox_model->update_messages_readstatus($postbox, $messages, $readstatus);
                $error = $resstat['msg'];
                if ($resstat['result']==$this->success_result) {
                    $res = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                    $error = $res['msg'];
                    if ($res['result'] == $this->success_result) {
                        $error = '';
                        $mdata['messages'] = $this->load->view('postbox/messages_list_view',['messages' => $res['messages']], TRUE);
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function messages_archive()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Parameter';
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox',0);
            $folder = ifset($postdata,'folder','');
            $msgsrc = ifset($postdata,'messages','');
            $postsort = ifset($postdata,'postsort','date_desc');
            if (!empty($postbox) && !empty($folder) && !empty($msgsrc)) {
                $messages = explode(',', $msgsrc);
                $resarch = $this->mailbox_model->messages_archive($messages, $postbox);
                $error = $resarch['msg'];
                if ($resarch['result']==$this->success_result) {
                    $res = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                    $error = $res['msg'];
                    if ($res['result'] == $this->success_result) {
                        $error = '';
                        $mdata['messages'] = $this->load->view('postbox/messages_list_view',['messages' => $res['messages']], TRUE);
                        $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    }
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function view_message()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Postbox Parameter';
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox', '');
            $folder = ifset($postdata,'folder', '');
            $message = ifset($postdata,'message_id','');
            $postsort = ifset($postdata,'postsort','date_desc');
            if (!empty($postbox) && !empty($folder) && !empty($message)) {
                $res = $this->mailbox_model->view_message($message, $postbox);
                $error = $res['msg'];
                if ($res['result']==$this->success_result) {
                    $error = '';
                    $msgnavig = $this->mailbox_model->count_messages($postbox, $folder, $message, $postsort);
                    $fromuser = $frommail = '';
                    $fromaddr = explode('<', $res['message']['message_from']);
                    if (count($fromaddr) > 0) {
                        if (count($fromaddr) > 1) {
                            $fromuser = $fromaddr[0];
                            $frommail = str_replace(['<', '>',], '', $fromaddr[1]);
                        } else {
                            $fromuser = $fromaddr[0];
                            $frommail = $fromaddr[0];
                        }
                    }
                    $touser = $tomail = '';
                    $toaddr = explode('<', $res['message']['message_to']);
                    if (count($toaddr) > 0) {
                        if (count($toaddr) > 1) {
                            $touser = $toaddr[0];
                            $tomail = str_replace(['<', '>',], '', $toaddr[1]);
                        } else {
                            $tomail = $toaddr[0];
                        }
                    }
                    $adrcc_view = '';
                    if (!empty($res['adrcc'])) {
                        $adresses = [];
                        foreach ($res['adrcc'] as $adr) {
                            $addrarray = explode('<', $adr['address']);
                            $username = $usermail = '';
                            if (count($addrarray) > 0) {
                                if (count($addrarray) > 1) {
                                    $usermail = $addrarray[0];
                                    $usermail = str_replace(['<', '>',], '', $addrarray[1]);
                                } else {
                                    $usermail = $addrarray[0];
                                }
                            }
                            $adresses[] = [
                                'username' => $username,
                                'usermail' => $usermail,
                            ];
                        }
                        // $address = implode(',', $adresses);
                        $adrcc_view = $this->load->view('postbox/message_addresscopy_view',['type'=>'Cc', 'address' => $adresses], TRUE);
                    }
                    $adrbcc_view = '';
                    if (!empty($res['adrbcc'])) {
                        $adresses = [];
                        foreach ($res['adrbcc'] as $adr) {
                            $addrarray = explode('<', $adr['address']);
                            $username = $usermail = '';
                            if (count($addrarray) > 0) {
                                if (count($addrarray) > 1) {
                                    $usermail = $addrarray[0];
                                    $usermail = str_replace(['<', '>',], '', $addrarray[1]);
                                } else {
                                    $usermail = $addrarray[0];
                                }
                            }
                            $adresses[] = [
                                'username' => $username,
                                'usermail' => $usermail,
                            ];
                        }

                        $adrbcc_view = $this->load->view('postbox/message_addresscopy_view',['type'=>'Bcc', 'address' => $adresses], TRUE);
                    }
                    $folder_name = $res['folder'];
                    if ($folder=='new') {
                        $folder_name = 'Unread';
                    } elseif ($folder=='flagged') {
                        $folder_name = 'Starred';
                    }
                    $attachment_view = '';
                    if (count($res['attachments'])>0) {

                        $attachment_view = $this->load->view('postbox/message_attachments_view',['attachments' => $res['attachments'],'imgview' => ['BMP','GIF','JPEG','JPG','PNG']], TRUE);
                    }
                    $options = [
                        'message' => $res['message'],
                        'attachments' => $attachment_view,
                        'folder' => $folder,
                        'folder_name' => $folder_name,
                        'adrcc' => $adrcc_view,
                        'adrbcc' => $adrbcc_view,
                        'prvcnt' => $msgnavig['prvcnt'],
                        'prvid' => $msgnavig['prvid'],
                        'nxtcnt' => $msgnavig['nxtcnt'],
                        'nxtid' => $msgnavig['nxtid'],
                        'fromuser' => $fromuser,
                        'frommail' => $frommail,
                        'touser' => $touser,
                        'tomail' => $tomail,
                        'seen' => $res['seen'],
                    ];
                    $mdata['content'] = $this->load->view('postbox/message_details_view',$options, TRUE);
                    $mdata['body'] = $res['message']['message_text'];
                    $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    $mdata['seen'] = $res['seen'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function flag_message()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $message_id = ifset($postdata, 'message_id','');
            $postbox = ifset($postdata, 'postbox','');
            $res = $this->mailbox_model->update_message_flagged($message_id,$postbox);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                if ($res['unflag']==1) {
                    $mdata['content'] = '<i class="fa fa-star" aria-hidden="true"></i>';
                } else {
                    $mdata['content'] = '<i class="fa fa-star-o" aria-hidden="true"></i>';
                }
                $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function message_move_folders()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox','');
            $folder = ifset($postdata, 'folder','');
            $res = $this->mailbox_model->get_postbox_folderslist($postbox, 1);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $folders = $res['folders'];
                $newfolders = [];
                foreach ($folders as $folder) {
                    if ($folder['folder_id']!=$folder) {
                        $newfolders[] = $folder;
                    }
                }
                $numfold = ceil(count($newfolders)/10);
                $options = [
                    'folders' => $newfolders,
                    'numfold' => $numfold,
                ];
                $mdata['content'] = $this->load->view('postbox/message_move_folders_view',$options,TRUE);
            }
            $this->ajaxResponse($mdata, $error);
        }
    }

    public function message_move()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata, 'postbox','');
            $folder = ifset($postdata, 'folder','');
            $message_id = ifset($postdata, 'message_id','');
            $newfolder = ifset($postdata, 'newfolder','');
            $postsort = ifset($postdata, 'postsort','date_desc');
            $msgtype = ifset($postdata, 'msgtype','once');
            if ($msgtype=='once') {
                $messages = [];
                array_push($messages, $message_id);
            } else {
                $messages = explode(',', $message_id);
            }
            $res = $this->mailbox_model->messages_move($messages,$postbox,$newfolder);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                // Get Messages of folder
                $res = $this->mailbox_model->postbox_viewfolder($postbox, $folder, $postsort);
                $error = $res['msg'];
                if ($res['result'] == $this->success_result) {
                    $error = '';
                    $mdata['messages'] = $this->load->view('postbox/messages_list_view',['messages' => $res['messages']], TRUE);
                    $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function message_read_status()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $message_id = ifset($postdata, 'message_id','');
            $postbox = ifset($postdata, 'postbox','');
            $res = $this->mailbox_model->update_message_readstatus($message_id,$postbox);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $error = '';
                $mdata['unread'] = $res['unread'];
                if ($res['unread']==1) {
                    $mdata['content'] = '<span class="emlnav-bnticon"><img src="/img/postbox/icon-unread.svg"></span> Mark Unread';
                } else {
                    $mdata['content'] = '<span class="emlnav-bnticon"><img src="/img/postbox/icon-unread.svg"></span> Mark Read';
                }
                $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function message_remove()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $postdata = $this->input->post();
            $postbox = ifset($postdata,'postbox', '');
            $folder = ifset($postdata,'folder', '');
            $message = ifset($postdata,'message_id','');
            $postsort = ifset($postdata,'postsort','date_desc');

            $messages = [];
            array_push($messages, $message);
            $res = $this->mailbox_model->messages_delete($messages, $postbox, $folder);
            $error = $res['msg'];
            if ($res['result']==$this->success_result) {
                $mdata['redirect'] = 0;
                // Get next msg
                $msgnavig = $this->mailbox_model->count_messages($postbox, $folder, $message, $postsort);
                if ($msgnavig['nxtcnt']==0) {
                    if ($msgnavig['prvcnt']==0) {
                        $mdata['redirect'] = 1;
                        $error = '';
                        $this->ajaxResponse($mdata, $error);
                        return true;
                    } else {
                        $message_id = $msgnavig['prvid'];
                    }
                } else {
                    $message_id = $msgnavig['nxtid'];
                }
                // Get new message content
                $resmsg = $this->mailbox_model->view_message($message_id, $postbox);
                $error = $resmsg['msg'];
                if ($resmsg['result']==$this->success_result) {
                    $error = '';
                    $msgnavig = $this->mailbox_model->count_messages($postbox, $folder, $message_id, $postsort);
                    $fromuser = $frommail = '';
                    $fromaddr = explode('<', $resmsg['message']['message_from']);
                    if (count($fromaddr) > 0) {
                        if (count($fromaddr) > 1) {
                            $fromuser = $fromaddr[0];
                            $frommail = str_replace(['<', '>',], '', $fromaddr[1]);
                        } else {
                            $fromuser = $fromaddr[0];
                            $frommail = $fromaddr[0];
                        }
                    }
                    $touser = $tomail = '';
                    $toaddr = explode('<', $resmsg['message']['message_to']);
                    if (count($toaddr) > 0) {
                        if (count($toaddr) > 1) {
                            $touser = $toaddr[0];
                            $tomail = str_replace(['<', '>',], '', $toaddr[1]);
                        } else {
                            $tomail = $toaddr[0];
                        }
                    }
                    $adrcc_view = '';
                    if (!empty($resmsg['adrcc'])) {
                        $adresses = [];
                        foreach ($resmsg['adrcc'] as $adr) {
                            $addrarray = explode('<', $adr['address']);
                            $username = $usermail = '';
                            if (count($addrarray) > 0) {
                                if (count($addrarray) > 1) {
                                    $usermail = $addrarray[0];
                                    $usermail = str_replace(['<', '>',], '', $addrarray[1]);
                                } else {
                                    $usermail = $addrarray[0];
                                }
                            }
                            $adresses[] = [
                                'username' => $username,
                                'usermail' => $usermail,
                            ];
                        }
                        // $address = implode(',', $adresses);
                        $adrcc_view = $this->load->view('postbox/message_addresscopy_view', ['type' => 'Cc', 'address' => $adresses], TRUE);
                    }
                    $adrbcc_view = '';
                    if (!empty($resmsg['adrbcc'])) {
                        $adresses = [];
                        foreach ($res['adrbcc'] as $adr) {
                            $addrarray = explode('<', $adr['address']);
                            $username = $usermail = '';
                            if (count($addrarray) > 0) {
                                if (count($addrarray) > 1) {
                                    $usermail = $addrarray[0];
                                    $usermail = str_replace(['<', '>',], '', $addrarray[1]);
                                } else {
                                    $usermail = $addrarray[0];
                                }
                            }
                            $adresses[] = [
                                'username' => $username,
                                'usermail' => $usermail,
                            ];
                        }
                        $adrbcc_view = $this->load->view('postbox/message_addresscopy_view', ['type' => 'Bcc', 'address' => $adresses], TRUE);
                    }
                    $folder_name = $resmsg['folder'];
                    if ($folder == 'new') {
                        $folder_name = 'Unread';
                    } elseif ($folder == 'flagged') {
                        $folder_name = 'Starred';
                    }
                    $attachment_view = '';
                    if (count($resmsg['attachments']) > 0) {
                        $attachment_view = $this->load->view('postbox/message_attachments_view', ['attachments' => $res['attachments'], 'imgview' => ['BMP', 'GIF', 'JPEG', 'JPG', 'PNG']], TRUE);
                    }
                    $options = [
                        'message' => $resmsg['message'],
                        'attachments' => $attachment_view,
                        'folder' => $folder,
                        'folder_name' => $folder_name,
                        'adrcc' => $adrcc_view,
                        'adrbcc' => $adrbcc_view,
                        'prvcnt' => $msgnavig['prvcnt'],
                        'prvid' => $msgnavig['prvid'],
                        'nxtcnt' => $msgnavig['nxtcnt'],
                        'nxtid' => $msgnavig['nxtid'],
                        'fromuser' => $fromuser,
                        'frommail' => $frommail,
                        'touser' => $touser,
                        'tomail' => $tomail,
                        'seen' => $resmsg['seen'],
                    ];
                    $mdata['content'] = $this->load->view('postbox/message_details_view', $options, TRUE);
                    $mdata['body'] = $resmsg['message']['message_text'];
                    $mdata['folders'] = $this->mailbox_model->count_folders_messages($postbox);
                    $mdata['seen'] = $resmsg['seen'];
                }
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }

    public function postbox_brand()
    {
        if ($this->isAjax()) {
            $mdata = [];
            $error = 'Empty Brand';
            $postdata = $this->input->post();
            $brand  = ifset($postdata, 'brand','');
            if (!empty($brand)) {
                $error = '';
                $postboxes = $this->mailbox_model->get_user_mailboxes($this->USR_ID, $this->USR_ROLE, $brand);
                $postbox = '';
                if (count($postboxes) > 0) {
                    $postbox = $postboxes[0]['postbox_id'];
                }
                $mdata['postbox'] = $postbox;
                $mdata['menu_view'] = $this->load->view('postbox/postboxes_view', ['postboxes' => $postboxes], TRUE);
                $mdata['brandclass'] = ($brand=='SR' ? 'relievers' : ($brand=='SG' ? '' : 'stressballs'));
            }
            $this->ajaxResponse($mdata, $error);
        }
        show_404();
    }
}
