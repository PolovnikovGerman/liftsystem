<?php
use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;

class Emails extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {

    }

    public function test()
    {
//        phpinfo();die();
        $mailbox = 'imap.mail.yahoo.com';
        $username = getenv('EMAIL_USER');
        $password = getenv('EMAIL_PASSWD');
        $encryption = Imap::ENCRYPT_SSL;

        try{
            $imap = new Imap($mailbox, $username, $password, $encryption);
            // You can also check out example-connect.php for more connection options

        }catch (ImapClientException $error){
            echo $error->getMessage().PHP_EOL; // You know the rule, no errors in production ...
            die(); // Oh no :( we failed
        }
        // Get all folders as array of strings
        $folders = $imap->getFolders();
        foreach($folders as $key=>$val) {
            echo 'Folder - '.$key.PHP_EOL.'<br>';
        }
        // Select the folder INBOX
        $imap->selectFolder('INBOX');

        // Count the messages in current folder
        $overallMessages = $imap->countMessages();
        $unreadMessages = $imap->countUnreadMessages();
        echo 'All Messages - '.$overallMessages.' UnRead '.$unreadMessages.PHP_EOL.'<br>';
//        $tt = $imap->getBriefInfoMessages();
//        print_r($tt);
//        die();
        // Fetch all the messages in the current folder
        // $emails = $imap->getMessages(100,1, 'desc');
        $uid = '67759';
        $id = $imap->getId($uid);
        $email = $imap->getMessage($id);
        $this->load->config('uploader');
        // foreach ($emails as $email) {
            $attachments = $email->attachments;
            if (count($attachments) > 0) {
                // $imap->saveAttachments(['dir' => $this->config->item('upload_path_preload'),'incomingMessage'=>$email]);
                echo 'UID '.$email->header->uid.' Date '.$email->header->date.' Subject '.$email->header->subject.PHP_EOL.'<br/>';

                foreach ($attachments as $attachment) {
                    if ($attachment->info->structure->encoding == 3) {
                        echo 'Name '.$attachment->name.PHP_EOL.'</br>';
                        echo 'Type '.$attachment->info->structure->type.PHP_EOL.'</br>';
                        echo 'Encoding '.$attachment->info->structure->encoding.'<br/>';
                        echo 'Subtype '.$attachment->info->structure->subtype.'<br/>';
                        file_put_contents($this->config->item('upload_path_preload').$attachment->name, base64_decode($attachment->body));
                    }
                    // print_r($attachment->info).PHP_EOL.'</br>';;
                }
                die();
            }
        // }
        $tt=1;

    }

    public function testmsg()
    {
        $mailbox = 'imap.mail.yahoo.com';
        $username = getenv('EMAIL_USER');
        $password = getenv('EMAIL_PASSWD');
        $encryption = Imap::ENCRYPT_SSL;

        try{
            $imap = new Imap($mailbox, $username, $password, $encryption);
            // You can also check out example-connect.php for more connection options

        }catch (ImapClientException $error){
            echo $error->getMessage().PHP_EOL; // You know the rule, no errors in production ...
            die(); // Oh no :( we failed
        }
        $uid = '69057';
        $id = $imap->getId($uid);
        echo 'Message ID '.$id.'<br>';
        $imap->setUnseenMessage($id);

    }

}