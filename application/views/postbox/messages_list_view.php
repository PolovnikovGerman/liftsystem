<?php $msgdate = ''; ?>
<?php foreach ($messages as $message): ?>
    <?php $chkdate = date('l - M j, Y', $message['message_udate']); ?>
    <?php if ($msgdate != $chkdate): ?>
        <?php $msgdate = $chkdate; ?>
        <div class="emltbl-tr tr-date">
            <div class="date-title"><?=$msgdate?></div>
            <hr noshade size="3">
        </div>
    <?php endif; ?>
    <div class="emltbl-tr <?=$message['message_seen']==1 ? '' : 'eml-unread'?>">
        <div class="emltbl-td td-select">
            <input type="checkbox" name="selectemail" data-message="<?=$message['message_id']?>"/>
        </div>
        <div class="emlselected-menu" data-message="<?=$message['message_id']?>"></div>
        <div class="emltbl-td td-folder" data-message="<?=$message['message_id']?>">
            <i class="fa fa-folder-o" aria-hidden="true"></i>
        </div>
        <div class="emlfolders-menu" data-message="<?=$message['message_id']?>"></div>
        <div class="emltbl-td td-reply" data-message="<?=$message['message_id']?>">
            <?php if ($message['message_answered']) : ?>
                <i class="fa fa-reply" aria-hidden="true"></i>
            <?php elseif ($message['message_seen'] == 0) : ?>
                <i class="fa fa-circle" aria-hidden="true"></i>
            <?php else: ?>
                &nbsp;
            <?php endif; ?>
        </div>
        <div class="emltbl-td td-namesender" data-message="<?=$message['message_id']?>"><?=$message['message_from']?></div>
        <div class="emltbl-td td-favorites" data-message="<?=$message['message_id']?>">
            <?php if ($message['message_flagged']) : ?>
                <i class="fa fa-star" aria-hidden="true"></i>
            <?php else: ?>
                <i class="fa fa-star-o" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
        <div class="emltbl-td td-email" data-message="<?=$message['message_id']?>">
            <span class="eml-subjectlist"><?=$message['message_subject']?></span>
            <span class="eml-contant"><?=$message['message_text']?></span>
        </div>
        <div class="emltbl-td td-files" data-message="<?=$message['message_id']?>">
            <?php if ($message['numattach'] > 0) : ?>
                <i class="fa fa-paperclip paperclip" aria-hidden="true"></i>
            <?php else : ?>
                &nbsp;
            <?php endif; ?>
        </div>
        <div class="emltbl-td td-time" data-message="<?=$message['message_id']?>"><?=date('g:i A', $message['message_udate'])?></div>
    </div>
<?php endforeach; ?>
