<?php $numpp=1; ?>
<?php foreach ($quests as $quest): ?>
    <div class="datarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>" data-task="<?=$quest['email_id']?>">
        <div class="newunassign_date_dat"><?=date('m/d/y', strtotime($quest['email_date']));?></div>
        <div class="webquestion_email_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$quest['email_sendermail']?>">
            <?=$quest['email_sendermail']?>
        </div>
        <?php if (empty($quest['email_senderphone'])): ?>
            <div class="webquestion_phone_dat">&nbsp;</div>
        <?php else: ?>
            <div class="webquestion_phone_dat" data-event="hover" data-css="itemdetailsballonbox"
                 data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
                 data-balloon="<?=$quest['email_senderphone']?>">
                <i class="fa fa-phone"></i>
            </div>
        <?php endif; ?>
        <div class="webquestion_webpage_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="4000" data-delay="1000"
             data-balloon="<?=$quest['email_webpage']?>">
            <?=$quest['email_webpage']?>
        </div>
        <div class="webquestion_message_dat truncateoverflowtext" data-event="hover" data-css="itemdetailsballonbox"
             data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="auto" data-textcolor="#000" data-timer="10000" data-delay="1000"
             data-balloon="<?=$quest['email_text']?>">
            <?=$quest['email_text']?>
        </div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
