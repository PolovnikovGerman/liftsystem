<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;

class Mailbox_model extends MY_Model
{
    var $inbox_name = 'Inbox';
    var $mainfolders = [
        'Inbox',
        'Unread',
        'Starred',
        'Draft',
        'Sent',
        'Bulk',
        'Archive',
        'Spam',
        'Trash',
    ];
    function __construct()
    {
        parent::__construct();
    }

    public function get_mailboxes()
    {
        $this->db->select('*')->from('user_postboxes');
        return $this->db->get()->result_array();
    }

    public function get_postbox_folders($postbox)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Issue with Connection'];
        $this->db->select('*')->from('postbox_folders')->where('postbox_id', $postbox['postbox_id']);
        $folders = $this->db->get()->result_array();
        // Get folders from Emails Account
        $imapdat = $this->_create_imap_client($postbox);
        $out['msg'] = $imapdat['msg'];
        if ($imapdat['result']==$this->success_result) {
            // All OK, continue
            $imap = $imapdat['imap'];
            $imapfolders = $imap->getFolders();
            // Check exist local folders
            foreach ($folders as $folder) {
                $find = 0;
                foreach ($imapfolders as $key => $val) {
                    if ($key==$folder['folder_name']) {
                        $find = 1;
                        break;
                    }
                }
                if ($find==0) {
                    // Folder deleted on server
                    $this->db->where('folder_id', $folder['folder_id']);
                    $this->db->delete('postbox_folders');
                }
            }
            // Re-get folders
            $this->db->select('*')->from('postbox_folders')->where('postbox_id', $postbox['postbox_id']);
            $folders = $this->db->get()->result_array();
            foreach($imapfolders as $key=>$val) {
                $find =0;
                $foldername = $key;
                foreach ($folders as $folder) {
                    if ($folder['folder_name']==$foldername) {
                        $find = 1;
                        break;
                    }
                }
                if ($find==0) {
                    // Add new folder
                    $this->db->set('folder_name', $foldername);
                    $this->db->set('postbox_id', $postbox['postbox_id']);
                    $this->db->insert('postbox_folders');
                }
            }
            // Get new list of folders
            $this->db->select('*')->from('postbox_folders')->where('postbox_id', $postbox['postbox_id']);
            $folders = $this->db->get()->result_array();
            $out['result'] = $this->success_result;
            $out['folders'] = $folders;
        }
        return $out;
    }

    public function read_folders_msgs($postbox, $folder)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Error with connection'];
        $imapdat = $this->_create_imap_client($postbox);
        $out['msg'] = $imapdat['msg'];
        if ($imapdat['result']==$this->success_result) {
            $out['result'] = $this->success_result;
            $imap = $imapdat['imap'];
            $imap->selectFolder($folder['folder_name']);
            $overallMessages = $imap->countMessages();
            $unreadMessages = $imap->countUnreadMessages();
            $this->db->where('folder_id', $folder['folder_id']);
            $this->db->set('folder_messages', $overallMessages);
            $this->db->update('postbox_folders');
            // $out['totalmsg'] = $overallMessages;
            // $out['unread'] = $unreadMessages;
            $briefinfos = $imap->getBriefInfoMessages();
            foreach ($briefinfos as $briefinfo) {
                $message = $imap->getMessage($briefinfo['id']);
                echo 'Manage msg '.$briefinfo['id'].' Messaage ID '.$message->header->message_id.PHP_EOL;
                // echo 'UDate '.$message->header->udate.PHP_EOL;
                $postmsgid = $message->header->message_id;
                // Check - if such msg exist
                $this->db->select('count(message_id) as cnt, max(message_id) as msgid')->from('postbox_messages')->where('postmessage_id', $postmsgid);
                $msgchk = $this->db->get()->row_array();
                if ($msgchk['cnt']==0) {
                    // New Message
                    $this->db->set('folder_id', $folder['folder_id']);
                    $this->db->set('message_subject', $message->header->subject);
                    $this->db->set('message_from', str_replace('"','',$message->header->from));
                    $this->db->set('message_to', $message->header->to);
                    $this->db->set('message_date', $message->header->date);
                    $this->db->set('postmessage_id', $message->header->message_id);
                    $this->db->set('message_uid', $message->header->uid);
                    $this->db->set('message_recent', $message->header->recent);
                    $this->db->set('message_flagged', $message->header->flagged);
                    $this->db->set('message_answered', $message->header->answered);
                    $this->db->set('message_deleted', $message->header->deleted);
                    $this->db->set('message_seen', $message->header->seen);
                    $this->db->set('message_draft', $message->header->draft);
                    $this->db->set('message_udate', $message->header->udate);
                    if (isset($message->message->info[1]->body)) {
                        $this->db->set('message_text', $message->message->info[1]->body);
                    } else {
                        $this->db->set('message_text', $message->message->info[0]->body);
                    }
                    $this->db->insert('postbox_messages');
                    $msgid = $this->db->insert_id();
                    $attachments = $message->attachments;
                    if (count($attachments) > 0) {
                        $this->_save_attachment($msgid, $attachments);
                    }
                    if (isset($message->header->details->cc)) {
                        foreach ($message->header->details->cc as $ccaddres) {
                            $this->db->set('message_id', $msgid);
                            $this->db->set('address_type','CC');
                            $this->db->set('address', $ccaddres->mailbox.'@'.$ccaddres->host);
                            $this->db->insert('postmessage_address');
                        }
                    }
                    if (isset($message->header->details->bcc)) {
                        foreach ($message->header->details->bcc as $ccaddres) {
                            $this->db->set('message_id', $msgid);
                            $this->db->set('address_type','BCC');
                            $this->db->set('address', $ccaddres->mailbox.'@'.$ccaddres->host);
                            $this->db->insert('postmessage_address');
                        }
                    }
                } else {
                    $this->db->where('message_id', $msgchk['msgid']);
                    $this->db->set('folder_id', $folder['folder_id']);
                    $this->db->set('message_flagged', $message->header->flagged);
                    $this->db->set('message_answered', $message->header->answered);
                    $this->db->set('message_deleted', $message->header->deleted);
                    $this->db->set('message_seen', $message->header->seen);
                    $this->db->set('message_draft', $message->header->draft);
                    $this->db->set('message_udate', $message->header->udate);
                    $this->db->update('postbox_messages');
                }
            }
        }
        return $out;
    }

    public function update_folders_msgs($postbox, $folder)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Error with connection'];
        $imapdat = $this->_create_imap_client($postbox);
        $out['msg'] = $imapdat['msg'];
        if ($imapdat['result']==$this->success_result) {
            $out['result'] = $this->success_result;
            $imap = $imapdat['imap'];
            $imap->selectFolder($folder['folder_name']);
            $briefinfos = $imap->getBriefInfoMessages();
            // Get last 20 messages
            $numinfos = count($briefinfos);
            $fisrtid = 0;
            if ($numinfos > 20) {
                $fisrtid = $numinfos - 20;
            }
            for ($i=$fisrtid; $i< $numinfos; $i++) {
                $message = $imap->getMessage($briefinfos[$i]['id']);
                echo 'Manage msg '.$briefinfos[$i]['id'].' Messaage ID '.$message->header->message_id.PHP_EOL;
                $postmsgid = $message->header->message_id;
                // Check - if such msg exist
                $this->db->select('count(message_id) as cnt, max(message_id) as msgid')->from('postbox_messages')->where('postmessage_id', $postmsgid);
                $msgchk = $this->db->get()->row_array();
                if ($msgchk['cnt']==0) {
                    // New Message
                    echo 'Add message '.$postmsgid.PHP_EOL;
                    $this->db->set('folder_id', $folder['folder_id']);
                    $this->db->set('message_subject', $message->header->subject);
                    $this->db->set('message_from', $message->header->from);
                    $this->db->set('message_to', $message->header->to);
                    $this->db->set('message_date', $message->header->date);
                    $this->db->set('postmessage_id', $message->header->message_id);
                    $this->db->set('message_uid', $message->header->uid);
                    $this->db->set('message_recent', $message->header->recent);
                    $this->db->set('message_flagged', $message->header->flagged);
                    $this->db->set('message_answered', $message->header->answered);
                    $this->db->set('message_deleted', $message->header->deleted);
                    $this->db->set('message_seen', $message->header->seen);
                    $this->db->set('message_draft', $message->header->draft);
                    $this->db->set('message_udate', $message->header->udate);
                    $this->db->set('message_text', $message->message->info[1]->body);
                    $this->db->insert('postbox_messages');
                    $msgid = $this->db->insert_id();
                    $attachments = $message->attachments;
                    if (count($attachments) > 0) {
                        $this->_save_attachment($msgid, $attachments);
                    }
                } else {
                    echo 'Update message '.$postmsgid.PHP_EOL;
                    $this->db->where('message_id', $msgchk['msgid']);
                    $this->db->set('folder_id', $folder['folder_id']);
                    $this->db->set('message_flagged', $message->header->flagged);
                    $this->db->set('message_answered', $message->header->answered);
                    $this->db->set('message_deleted', $message->header->deleted);
                    $this->db->set('message_seen', $message->header->seen);
                    $this->db->set('message_draft', $message->header->draft);
                    $this->db->set('message_udate', $message->header->udate);
                    $this->db->update('postbox_messages');
                }

            }
        }
    }

    // Save attachments
    private function _save_attachment($msgid, $attachments)
    {
        $this->load->config('uploader');
        $fullpath = $this->config->item('mailbox_attachments').$msgid.'/';
        $shrtpath = $this->config->item('mailbox_attachments_relative').$msgid.'/';
        // Check folder
            foreach ($attachments as $attachment) {
                if ($attachment->info->structure->encoding == 3) {
                    if (createPath($shrtpath)) {
                        $filedetails = extract_filename($attachment->name);
                        if (!empty($filedetails['ext'])) {
                            $newattachname = uniq_link(15) . '.' . $filedetails['ext'];
                            $fullattach = $fullpath . $newattachname;
                            $saveres = @file_put_contents($fullattach, base64_decode($attachment->body));
                            if ($saveres) {
                                $this->db->set('message_id', $msgid);
                                $this->db->set('attachment_name', $attachment->name);
                                $this->db->set('attachment_link', $shrtpath . $newattachname);
                                $this->db->set('attachment_type', $attachment->info->structure->subtype);
                                $this->db->insert('postbox_attachments');
                            }
                        }
                    }
                }
            }

    }

    // Create Imap Client
    private function _create_imap_client($postbox)
    {
        $out = ['result' => $this->error_result, 'msg' => ''];
        $mailbox = $postbox['mailbox'];
        $username = $postbox['postbox_user'];
        $password = $postbox['postbox_passwd'];
        $encryption = Imap::ENCRYPT_SSL;
        $this->load->library('Imapclient');

        try{
            $imap = new ImapClient($mailbox, $username, $password, $encryption);
        } catch (ImapClientException $error){
            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
            return $out;
        }
        $out['result'] = $this->success_result;
        $out['imap'] = $imap;
        return $out;
    }
    // Output functions
    public function get_user_mailboxes($usr_id, $brand='ALL')
    {
        $this->db->select('*')->from('user_postboxes')->where('user_id', $usr_id);
        // Add select by brand
        $results = $this->db->get()->result_array();
        $out = [];
        foreach ($results as $result) {
            $mailtitle = explode('@',$result['postbox_user']);
            $result['postbox_title'] = $mailtitle[0].'@';
            $out[] = $result;
        }
        return $out;
    }

    public function get_postbox_details($postbox_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Non exist postbox'];
        $this->db->select('*')->from('user_postboxes')->where('postbox_id', $postbox_id);
        $postbres = $this->db->get()->row_array();
        if (ifset($postbres,'postbox_id',0)==$postbox_id) {
            $out['result'] = $this->success_result;
            // Calc folders statistics
            $this->db->select('f.folder_id, f.folder_name, count(m.message_id) as cnt')->from('postbox_folders f')->join('postbox_messages m','f.folder_id=m.folder_id','left')->where('f.postbox_id', $postbox_id)->group_by('folder_id, f.folder_name');
            $folders = $this->db->get()->result_array();
            // Calc unread messages
            $this->db->select('count(m.message_id) as cnt')->from('postbox_messages m')->join('postbox_folders f', 'f.folder_id=m.folder_id')->where(['f.postbox_id'=>$postbox_id,'f.folder_name'=> $this->inbox_name,'m.message_seen'=>0]);
            $newmsg = $this->db->get()->row_array();
            // Calc stared
            $this->db->select('count(m.message_id) as cnt')->from('postbox_messages m')->join('postbox_folders f', 'f.folder_id=m.folder_id')->where(['f.postbox_id'=>$postbox_id,'f.folder_name'=> $this->inbox_name,'m.message_flagged'=>1]);
            $starmsg = $this->db->get()->row_array();

            $folders[] = [
                'folder_id' => 'new',
                'folder_name' => 'Unread',
                'cnt' => $newmsg['cnt'],
            ];
            $folders[] = [
                'folder_id' => 'flagged',
                'folder_name' => 'Starred',
                'cnt' => $starmsg['cnt'],
            ];
            $newfolders = [];
            // Check all folders
            $active = 1;
            $active_folder = 0;
            $active_cnt = 0;
            $folder_name = '';
            foreach ($this->mainfolders as $keyfold) {
                foreach ($folders as $folder) {
                    if ($folder['folder_name']==$keyfold) {
                        $newfolders[] = [
                            'folder_id' => $folder['folder_id'],
                            'folder_name' => $folder['folder_name'],
                            'main' => 1,
                            'active' => $active,
                            'empty' => $folder['cnt']==0 ? 1 : 0,
                            'cnt' => short_number($folder['cnt'],1),
                        ];
                        if ($active==1) {
                            $active_folder = $folder['folder_id'];
                            $active_cnt = $folder['cnt'];
                            $folder_name = $folder['folder_name'];
                            $active = 0;
                        }
                        break;
                    }
                }
            }
            foreach ($folders as $folder) {
                if (!in_array($folder['folder_name'], $this->mainfolders)) {
                    $newfolders[] = [
                        'folder_id' => $folder['folder_id'],
                        'folder_name' => $folder['folder_name'],
                        'main' => 0,
                        'active' => $active==1 ? 'active' : '',
                        'empty' => $folder['cnt']==0 ? 1 : 0,
                        'cnt' => short_number($folder['cnt'],1),
                    ];
                }
            }
            // Attachments
            $this->db->select('message_id, count(attachment_id) as cnt')->from('postbox_attachments')->group_by('message_id');
            $attachssql = $this->db->get_compiled_select();
            // Get messages
            $this->db->select('m.*, coalesce(atchs.cnt,0) as numattach')->from('postbox_messages m')->join("({$attachssql}) as atchs",'m.message_id=atchs.message_id', 'left')->
            where('m.folder_id', $active_folder)->order_by('m.message_udate', 'desc');
            $messages = $this->db->get()->result_array();

            $out['folders'] = $newfolders;
            $out['folder_name'] = $folder_name;
            $out['active_folder'] = $active_folder;
            $out['active_cnt'] = $active_cnt;
            $out['messages'] = $messages;
        }
        return $out;
    }

    public function postbox_addfolder($postbox_id, $folder_name)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Empty Postbox Parameter'];
        if (!empty($postbox_id)) {
            $out['msg'] = 'Empty Folder Name';
            if (!empty($folder_name)) {
                $out['msg'] = 'Invalid Postbox parameter';
                $this->db->select('*')->from('user_postboxes')->where('postbox_id', $postbox_id);
                $postbox = $this->db->get()->row_array();
                if (ifset($postbox, 'postbox_id',0)==$postbox_id) {
                    $imapres = $this->_create_imap_client($postbox);
                    $out['msg'] = $imapres['msg'];
                    if ($imapres['result']==$this->success_result) {
                        $imap = $imapres['imap'];
                        // Try create new folder
                        try{
                            $imap->addFolder($folder_name);
                        } catch (ImapClientException $error){
                            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
                            return $out;
                        }
                        // Check folders
                        $out['msg'] = 'Folder '.$folder_name.' Not Added';
                        $imapfolders = $imap->getFolders();
                        $find =0;
                        foreach($imapfolders as $key=>$val) {
                            $foldername = $key;
                            if ($foldername==$folder_name) {
                                $find = 1;
                                break;
                            }
                        }
                        if ($find==1) {
                            // Add new folder
                            $this->db->set('folder_name', $folder_name);
                            $this->db->set('postbox_id', $postbox_id);
                            $this->db->insert('postbox_folders');
                            $out['result'] = $this->success_result;
                            $this->db->select('f.folder_id, f.folder_name, count(m.message_id) as cnt')->from('postbox_folders f')->join('postbox_messages m','f.folder_id=m.folder_id','left')->where('f.postbox_id', $postbox_id)->group_by('folder_id, f.folder_name');
                            $folders = $this->db->get()->result_array();
                            $newfolders = [];
                            foreach ($folders as $folder) {
                                if (!in_array($folder['folder_name'], $this->mainfolders)) {
                                    $newfolders[] = [
                                        'folder_id' => $folder['folder_id'],
                                        'folder_name' => $folder['folder_name'],
                                        'main' => 0,
                                        'active' => '',
                                        'empty' => $folder['cnt']==0 ? 1 : 0,
                                        'cnt' => short_number($folder['cnt'],1),
                                    ];
                                }
                            }
                            $out['folders'] = $newfolders;
                        }

                    }
                }
            }
        }
        return $out;
    }

    public function postbox_viewfolder($postbox_id, $folder_id, $postsort)
    {
        $out=['result' => $this->error_result, 'msg' => 'Empty Folder id'];
        if ($postsort=='date_desc') {
            $sortby = 'm.message_udate';
            $sortorder = 'desc';
        } elseif ('date_asc') {
            $sortby = 'm.message_udate';
            $sortorder = 'asc';
        } elseif ('unread_asc') {
            $sortby = 'm.message_seen';
            $sortorder = 'asc';
        } elseif ('flagged_first') {
            $sortby = 'm.message_flagged';
            $sortorder = 'desc';
        }
        if (!empty($folder_id)) {
            $this->db->select('message_id, count(attachment_id) as cnt')->from('postbox_attachments')->group_by('message_id');
            $attachssql = $this->db->get_compiled_select();

            if ($folder_id=='new' || $folder_id=='flagged') {
                $out['result'] = $this->success_result;
                if ($folder_id=='new') {
                    $folder = [
                        'folder_id' => $folder_id,
                        'folder_name' => 'Unread',
                    ];
                    $this->db->select('m.*, coalesce(atchs.cnt,0) as numattach')->from('postbox_messages m')->
                    join('postbox_folders f', 'f.folder_id=m.folder_id')->join("({$attachssql}) as atchs",'m.message_id=atchs.message_id', 'left')->
                    where(['f.postbox_id'=>$postbox_id,'f.folder_name'=> $this->inbox_name,'m.message_seen'=>0])->order_by($sortby, $sortorder);
                    $messages = $this->db->get()->result_array();
                } else {
                    $folder = [
                        'folder_id' => $folder_id,
                        'folder_name' => 'Starred',
                    ];
                    $this->db->select('m.*, coalesce(atchs.cnt,0) as numattach')->from('postbox_messages m')->
                    join('postbox_folders f', 'f.folder_id=m.folder_id')->join("({$attachssql}) as atchs",'m.message_id=atchs.message_id', 'left')->
                    where(['f.postbox_id'=>$postbox_id,'f.folder_name'=> $this->inbox_name,'m.message_flagged'=>1])->order_by($sortby, $sortorder);
                    $messages = $this->db->get()->result_array();
                }
                $out['messages'] = $messages;
                $out['folder'] = $folder;
            } else {
                $out['msg'] = 'Incorrect Folder';
                $this->db->select('*')->from('postbox_folders')->where('folder_id', $folder_id);
                $folder = $this->db->get()->row_array();
                if (ifset($folder, 'folder_id',0)==$folder_id) {
                    $out['result'] = $this->success_result;
                    $out['folder'] = $folder;
                    $this->db->select('m.*, coalesce(atchs.cnt,0) as numattach')->from('postbox_messages m')->
                    join("({$attachssql}) as atchs",'m.message_id=atchs.message_id', 'left')->where('folder_id', $folder_id)->order_by($sortby, $sortorder);
                    $messages = $this->db->get()->result_array();
                    $out['messages'] = $messages;
                }
            }
        }
        return $out;
    }

    // Update read status
    public function update_message_readstatus($message_id, $postbox_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Postbox Not Found'];
        $this->db->select('*')->from('user_postboxes')->where('postbox_id', $postbox_id);
        $postbox = $this->db->get()->row_array();
        if (ifset($postbox, 'postbox_id', 0) == $postbox_id) {
            $imapres = $this->_create_imap_client($postbox);
            $out['msg'] = $imapres['msg'];
            if ($imapres['result']==$this->success_result) {
                $imap = $imapres['imap'];
                $out['msg'] = 'Message Not Exist';
                $this->db->select('*')->from('postbox_messages')->where('message_id', $message_id);
                $message = $this->db->get()->row_array();
                if (ifset($message, 'message_id',0)==$message_id) {
                    $id = $imap->getId($message['message_uid']);
                    $unread = $message['message_seen']==0 ? 1 : 0;
                    if ($unread==0) {
                        try{
                            $imap->setUnseenMessage($id);
                        } catch (ImapClientException $error){
                            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
                            return $out;
                        }
                    } else {
                        try{
                            $imap->setSeenMessage($id);
                        } catch (ImapClientException $error){
                            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
                            return $out;
                        }
                    }
                    $this->db->where('message_id', $message_id);
                    $this->db->set('message_seen', $unread);
                    $this->db->update('postbox_messages');
                    $out['result'] = $this->success_result;
                    $out['unread'] = $unread;
                }
            }
        }
        return $out;
    }

    public function update_message_flagged($message_id, $postbox_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Postbox Not Found'];
        $this->db->select('*')->from('user_postboxes')->where('postbox_id', $postbox_id);
        $postbox = $this->db->get()->row_array();
        if (ifset($postbox, 'postbox_id', 0) == $postbox_id) {
            $imapres = $this->_create_imap_client($postbox);
            $out['msg'] = $imapres['msg'];
            if ($imapres['result']==$this->success_result) {
                $imap = $imapres['imap'];
                $out['msg'] = 'Message Not Exist';
                $this->db->select('*')->from('postbox_messages')->where('message_id', $message_id);
                $message = $this->db->get()->row_array();
                if (ifset($message, 'message_id',0)==$message_id) {
                    $id = $imap->getId($message['message_uid']);
                    $unflag = $message['message_flagged']==0 ? 1 : 0;
                    if ($unflag==0) {
                        try{
                            $imap->setStarredMessage($id);
                        } catch (ImapClientException $error){
                            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
                            return $out;
                        }
                    } else {
                        try{
                            $imap->setUnstarredMessage($id);
                        } catch (ImapClientException $error){
                            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
                            return $out;
                        }
                    }
                    $this->db->where('message_id', $message_id);
                    $this->db->set('message_flagged', $unflag);
                    $this->db->update('postbox_messages');
                    $out['result'] = $this->success_result;
                    $out['unflag'] = $unflag;
                }
            }
        }
        return $out;
    }

    public function view_message($message_id, $postbox_id)
    {
        $out = ['result' => $this->error_result, 'msg' => 'Postbox Not Found'];
        $this->db->select('*')->from('user_postboxes')->where('postbox_id', $postbox_id);
        $postbox = $this->db->get()->row_array();
        if (ifset($postbox, 'postbox_id', 0) == $postbox_id) {
            $out['msg'] = 'Message Not Exist';
            $this->db->select('*')->from('postbox_messages')->where('message_id', $message_id);
            $message = $this->db->get()->row_array();
            if (ifset($message, 'message_id',0)==$message_id) {
                $out['result'] = $this->success_result;
//                $this->db->select('folder_name')->from('postbox_folders')->where('folder_id', $message['folder_id']);
//                $folderdat = $this->db->get()->row_array();
                // Get attached
                $this->db->select('*')->from('postbox_attachments')->where('message_id', $message_id);
                $attachs = $this->db->get()->result_array();
                // Get CC
                $this->db->select('*')->from('postmessage_address')->where(['message_id' => $message_id, 'address_type' => 'CC']);
                $adrcc = $this->db->get()->result_array();
                // Get BCC
                $this->db->select('*')->from('postmessage_address')->where(['message_id' => $message_id, 'address_type' => 'BCC']);
                $adrbcc = $this->db->get()->result_array();
                $out['message'] = $message;
                $out['attachments'] = $attachs;
                $out['adrcc'] = $adrcc;
                $out['adrbcc'] = $adrbcc;
//                $out['folder'] = $folderdat['folder_name'];
            }
        }
        return $out;
    }
}