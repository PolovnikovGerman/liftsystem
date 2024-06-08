<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;

class Mailbox_model extends MY_Model
{
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

            $out['totalmsg'] = $overallMessages;
            $out['unread'] = $unreadMessages;
            $briefinfos = $imap->getBriefInfoMessages();
            foreach ($briefinfos as $briefinfo) {
                $message = $imap->getMessage($briefinfo['id']);
                echo 'Manage msg '.$briefinfo['id'].' Messaage ID '.$message->header->message_id.PHP_EOL;
                $postmsgid = $message->header->message_id;
                // Check - if such msg exist
                $this->db->select('count(message_id) as cnt, max(message_id) as msgid')->from('postbox_messages')->where('postmessage_id', $postmsgid);
                $msgchk = $this->db->get()->row_array();
                if ($msgchk['cnt']==0) {
                    // New Message
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
                    $this->db->set('message_text', $message->message->info[1]->body);
                    $this->db->insert('postbox_messages');
                    $msgid = $this->db->insert_id();
                    $attachments = $message->attachments;
                    if (count($attachments) > 0) {
                        $this->_save_attachment($msgid, $attachments);
                    }
                }
            }
        }
        return $out;
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

        try{
            $imap = new Imap($mailbox, $username, $password, $encryption);
        } catch (ImapClientException $error){
            $out['msg'] = $error->getMessage(); // You know the rule, no errors in production ...
            return $out;
        }
        $out['result'] = $this->success_result;
        $out['imap'] = $imap;
        return $out;
    }
}