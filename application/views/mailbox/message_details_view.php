<input type="hidden" id="message" value="<?=$message['message_id']?>"/>
<input type="hidden" id="folder_id" value="<?=$folder?>"/>
<div class="emails-block-header">
    <div class="ebh-left">
        <div class="ebh-left-button backpostfolder">
            <span class="ebh-left-back"><img src="/img/mailbox/arrow-left.svg"></span> Back
        </div>
        <div class="ebh-left-button">
            <span class="ebh-left-reply"><img src="/img/mailbox/reply.svg"></span>
        </div>
        <div class="ebh-left-button">
            <span class="ebh-left-reply-all"><img src="/img/mailbox/reply-all.svg"></span>
        </div>
        <div class="ebh-left-button">
            <span class="ebh-left-forward"><img src="/img/mailbox/arrow-alt-right.svg"></span>
        </div>
    </div>
    <div class="menu-icons messagedetails">
        <ul>
            <li><span><img src="/img/mailbox/icon-archive.svg"></span> Archive</li>
            <li><span><img src="/img/mailbox/icon-move.svg"></span> Move</li>
            <li><span><img src="/img/mailbox/icon-delete.svg"></span> Delete</li>
            <li><span><img src="/img/mailbox/icon-spam.svg"></span> Spam</li>
            <li><span><img src="/img/mailbox/icon-more.svg"></span></li>
        </ul>
    </div>
    <div class="ebh-right">
        <div class="ebh-right-button">
            <span class="ebh-right-close"><img src="/img/mailbox/times.svg"></span>
        </div>
        <div class="ebh-right-button">
            <span class="ebh-right-down active"><img src="/img/mailbox/caret-down.svg"></span>
        </div>
        <div class="ebh-right-button">
            <span class="ebh-right-up"><img src="/img/mailbox/caret-up.svg"></span>
        </div>
    </div>
</div>

<div class="email-block">
    <div class="box-email-header">
        <div class="box-email-dateinfo">
            <span class="dateinfo-circle"><i class="fa fa-circle" aria-hidden="true"></i></span>
            <?=date('D - M d, Y', $message['message_udate'])?> - <span class="email-subject"><?=$message['message_subject']?></span>
        </div>
        <div class="box-email-inbox">
            <?=$folder_name?>
            <div id="message_flagarea" style="float: right!important; margin-left: 3px;">
                <?php if ($message['message_flagged']==1) { ?>
                    <span class="ic-orange"><i class="fa fa-star" aria-hidden="true"></i></span>
                <?php } else { ?>
                    <span class="ic-nonflagged"><i class="fa fa-star-o" aria-hidden="true"></i></span>
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="box-email">
        <div class="box-email-info">
            <div class="box-email-info-img">
                <img src="/img/mailbox/image.jpg">
            </div>
            <div class="box-email-info-from">
                <span class="status"><i class="fa fa-circle" aria-hidden="true"></i></span>
                <p class="nameuser"><?=$message['message_from']?></p>
                <p><span>From:</span> <?=$message['message_from']?></p>
                <p><span>To:</span> <?=$message['message_to']?></p>
                <?=$adrcc?>
                <?=$adrbcc?>
            </div>
            <div class="box-email-info-right">
                <div class="btn-print"><img src="/img/mailbox/print.svg"></div>
                <div class="date"><?=date('D, M d', $message['message_udate'])?> at <?=date('h:i A')?></div>
                <div class="favorite <?=$message['message_flagged']==1 ? 'flagged' : ''?>">
                    <i class="fa <?=$message['message_flagged']==1 ? 'fa-star' : 'fa-star-o'?>" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <iframe class="box-email-body" id="iframe"></iframe>
        <div class="box-email-bottom">
            <?=$attachments?>
            <div class="box-email-bottom-nav">
                <div class="bottom-nav-button">
                    <span class="bnb-reply"><img src="/img/mailbox/reply.svg"></span>
                </div>
                <div class="bottom-nav-button">
                    <span class="bnb-reply-all"><img src="/img/mailbox/reply-all.svg"></span>
                </div>
                <div class="bottom-nav-button">
                    <span class="bnb-forward"><img src="/img/mailbox/arrow-alt-right.svg"></span>
                </div>
                <div class="bottom-nav-button">
                    <span class="bnb-more"><img src="/img/mailbox/icon-more.svg"></span>
                </div>
            </div>
        </div>
    </div>
</div>