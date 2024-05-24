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
        phpinfo();die();
        $mailbox = 'mail.yahoosmallbusiness.com';
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
        echo 'Imap Open successfully'.PHP_EOL;
        die();
    }
}