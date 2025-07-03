<?php
use SSilence\ImapClient\ImapClientException;
use SSilence\ImapClient\ImapConnect;
use SSilence\ImapClient\ImapClient as Imap;

class Imapclient extends  Imap
{
    const ENCRYPT_SSL = 'ssl';

    /**
     * Force use of start-TLS to encrypt the session, and reject connection to servers that do not support it
     */
    const ENCRYPT_TLS = 'tls';
    const CONNECT_ADVANCED = 'connectAdvanced';
    const CONNECT_DEFAULT = 'connectDefault';

    /**
     * Connect status or advanced or default
     *
     * @var string
     */
    public static $connect;

    /**
     * Config for advanced connect
     *
     * @var array
     */
    public static $connectConfig;

    /**
     * Imap connection
     *
     * @var resource ImapConnect
     */
    protected $imap;

    /**
     * Mailbox url
     *
     * @var string
     */
    protected $mailbox = "";


    public function __construct($mailbox=null , $username=null, $password=null , $encryption=null)
    {
        if (!empty($mailbox)) {
            parent::__construct($mailbox , $username, $password , $encryption);
        }


    }

    public function setStarredMessage($ids)
    {
        return imap_setflag_full($this->imap, $ids, "\\Flagged");
    }

    public function setUnstarredMessage($ids)
    {
        return imap_clearflag_full($this->imap, $ids, "\\Flagged");
    }
}