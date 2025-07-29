<div class="emailesblock datarow">
    <div class="eml-header datarow">
        <div class="emlhead-left">
            <div class="eml-bnt-back"><span class="eml-bntback-icon"><img src="/img/postbox/long-arrow-left.svg"></span>Back</div>
            <div class="eml-greyline">&nbsp;</div>
            <div class="eml-bntwhite eml-bnt-reply">Reply<span class="eml-bnticon"><i class="fa fa-reply" aria-hidden="true"></i></span></div>
            <div class="eml-bntwhite eml-bnt-replyall">Reply to All<span class="eml-bnticon"><i class="fa fa-reply-all" aria-hidden="true"></i></span></div>
            <div class="eml-bntwhite eml-bnt-forward">Forward<span class="eml-bnticon"><i class="fa fa-share" aria-hidden="true"></i></span></div>
            <div class="eml-greyline">&nbsp;</div>
            <div class="eml-bntwhite eml-bnt-assign">Assign</div>
            <div class="eml-bntwhite eml-bnt-movefrom" data-message="<?=$message['message_id']?>">Move from: <span><?=$folder_name?></span></div>
            <div class="msgdetfolders-menu" id="msgdetailsfolders"></div>
        </div>
        <div class="emlhead-right">
            <div class="emailnav">
                <div class="eml-bntwhite emailnav-print" data-message="<?=$message['message_id']?>"><span class="emlnav-bnticon"><img src="/img/postbox/printer.svg"></span> Print</div>
                <div class="eml-bntwhite emailnav-readsatus" data-message="<?=$message['message_id']?>"><span class="emlnav-bnticon"><img src="/img/postbox/icon-unread.svg"></span> <?=$seen==1 ? 'Mark Read' : 'Mark Unread'?></div>
<!--                <div class="eml-bntwhite emailnav-spam" data-message="--><?php //=$message['message_id']?><!--"><span class="emlnav-bnticon"><img src="/img/postbox/icon-spam.svg"></span> Spam</div>-->
                <div class="eml-bntwhite emailnav-delete" data-message="<?=$message['message_id']?>"><span class="emlnav-bnticon"><img src="/img/postbox/icon-delete.svg"></span> Delete</div>
            </div>
            <div class="otheremls">
                <div class="othereml-prev <?=$prvcnt > 0 ? 'active' : ''?>" data-prev="<?=$prvid?>">
                    <?php if ($prvcnt > 0) : ?>
                        <img src="/img/postbox/caret-up-active.svg"/>
                    <?php else : ?>
                        <img src="/img/postbox/caret-up.svg"/>
                    <?php endif; ?>
                </div>
                <div class="othereml-next <?=$nxtcnt > 0 ? 'active' : ''?>" data-next="<?=$nxtid?>">
                    <?php if ($nxtcnt > 0) : ?>
                        <img src="/img/postbox/caret-down-active.svg">
                    <?php else : ?>
                        <img src="/img/postbox/caret-down.svg">
                    <?php endif; ?>
                </div>
            </div>
            <div class="eml-bnt-close"><img src="/img/postbox/close.svg"></div>
        </div>
    </div>
    <div class="eml-emailbody datarow">
        <div class="emailblock">
            <div class="emailbody datarow">
                <div class="email-subject datarow">
                    <div class="eml-subjicn <?=$seen==0 ? 'readed' : 'unread'?>">
                        <i class="fa fa-circle" aria-hidden="true"></i>
                    </div>
                    <div class="eml-subject"><?=$message['message_subject']?></div>
                    <div class="emlsub-foldfavor">
                        <div class="emlsub-folder"><?=$folder_name?></div>
                        <div class="emlsub-favorite"><i class="fa fa-star-o" aria-hidden="true"></i></div>
                    </div>
                </div>
                <div class="email-infobox datarow">
                    <div class="imlinf-photo">
                        <img src="/img/postbox/image.jpg">
                    </div>
                    <div class="imlinfo">
                        <div class="imlinfo-from datarow">
                            <div class="imlinfo-title">From:</div>
                            <div class="imlinfo-fromnameuser"><?=$fromuser?></div>
                            <div class="imlinfo-emladdress">&lt;<?=$frommail?>&gt;</div>
                        </div>
                        <div class="imlinfo-to datarow">
                            <div class="imlinfo-title">To:</div>
                            <div class="imlinfo-tonameuser"><?=$touser?></div>
                            <div class="imlinfo-emladdress">&lt;<?=$tomail?>&gt;</div>
                            <?=$adrcc?>
                            <?=$adrbcc?>
                        </div>
                    </div>
                    <div class="imlinfo-right">
                        <div class="imlinfo-date"><?=date('D, M j at g:i A', $message['message_udate'])?></div>
                        <div class="imlinfo-print">
                            <img src="/img/postbox/printer-lightgrey.svg">
                        </div>
                    </div>
                </div>
                <div class="contentemail datarow">
                    <?=empty($message['message_body']) ? $message['message_text'] : $message['message_body']?>
                </div>
            </div>
            <div class="attachedfiles datarow">
                <?=$attachments?>
                <div class="emailnav-grey">
                    <div class="eml-bntgrey emailnav-print"><span class="emlnav-bnticon"><img src="/img/postbox/printer.svg"></span> Print</div>
                    <div class="eml-bntgrey emailnav-print"><span class="emlnav-bnticon"><img src="/img/postbox/icon-unread.svg"></span> Mark Unread</div>
                    <div class="eml-bntgrey emailnav-print"><span class="emlnav-bnticon"><img src="/img/postbox/icon-spam.svg"></span> Spam</div>
                    <div class="eml-bntgrey emailnav-print"><span class="emlnav-bnticon"><img src="/img/postbox/icon-delete.svg"></span> Delete</div>
                </div>
            </div>
        </div>

    </div>
</div>
