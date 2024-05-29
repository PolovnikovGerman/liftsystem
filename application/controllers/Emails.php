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
        $username = ''; // german.polovnikov@bluetrack.com
        $password = '';
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
            echo 'Folder - '.$key.PHP_EOL;
        }
        // Select the folder INBOX
        $imap->selectFolder('INBOX');

        // Count the messages in current folder
        $overallMessages = $imap->countMessages();
        $unreadMessages = $imap->countUnreadMessages();
        echo 'All Messages - '.$overallMessages.' UnRead '.$unreadMessages.PHP_EOL;
        // Fetch all the messages in the current folder
        $emails = $imap->getMessages(5,10, 'asc');
        var_dump($emails);
    }
}