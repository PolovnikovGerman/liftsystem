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
        $username = 'german.polovnikov@bluetrack.com'; // german.polovnikov@bluetrack.com
        $password = 'nxxrbadiwvrdzuwo';
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
        // Fetch all the messages in the current folder
        $emails = $imap->getMessages(1,0, 'desc');
        $message = $emails[0];
        $tt=1;
        var_dump($emails);
    }
}