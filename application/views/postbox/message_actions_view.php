<div class="emlaction-menu-close"><img src="/img/postbox/close.svg" alt="Close"/></div>
<ul>
    <li>
        <div class="esmitem movemessage" data-message="<?=$message['message_id']?>">
            <div class="esmitem-icon">
                <img src="/img/postbox/icon-move.svg">
            </div>
            <div class="esmitem-txt">Move to folder</div>
        </div>
    </li>
    <li>
        <div class="esmitem deletemessage" data-message="<?=$message['message_id']?>">
            <div class="esmitem-icon">
                <img src="/img/postbox/icon-delete.svg">
            </div>
            <div class="esmitem-txt">Delete</div>
        </div>
    </li>
<!--    <li>-->
<!--        <div class="esmitem">-->
<!--            <div class="esmitem-icon">-->
<!--                <img src="img/icon-spam.svg">-->
<!--            </div>-->
<!--            <div class="esmitem-txt">In Spam</div>-->
<!--        </div>-->
<!--    </li>-->
    <li>
        <?php if ($message['message_seen']==1) : ?>
            <div class="esmitem unreadmessage" data-message="<?=$message['message_id']?>">
                <div class="esmitem-icon icn-unread">
                    <img src="/img/postbox/icon-unread.svg">
                </div>
                <div class="esmitem-txt">Mark as unread</div>
            </div>
        <?php else: ?>
            <div class="esmitem readmessage" data-message="<?=$message['message_id']?>">
                <div class="esmitem-icon">
                    <img src="/img/postbox/icon-unread.svg">
                </div>
                <div class="esmitem-txt">Mark as read</div>
            </div>
        <?php endif; ?>
    </li>
    <li>
        <div class="esmitem archivemsg" data-message="<?=$message['message_id']?>">
            <div class="esmitem-icon">
                <img src="/img/postbox/icon-archive.svg">
            </div>
            <div class="esmitem-txt">Archive</div>
        </div>
    </li>
<!--    <li>-->
<!--        <div class="esmitem">-->
<!--            <div class="esmitem-icon">-->
<!--                <i class="fa fa-bell-slash" aria-hidden="true"></i>-->
<!--            </div>-->
<!--            <div class="esmitem-txt">Ignore</div>-->
<!--        </div>-->
<!--    </li>-->
</ul>
