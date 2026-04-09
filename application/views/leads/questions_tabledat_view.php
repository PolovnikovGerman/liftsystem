<?php $nrow=0;?>
<?php foreach ($quests as $row) {?>
    <div class="quest_tabrow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?> <?=$row['rowclass']?>">
        <div class="quest_numrec" data-email="<?=$row['email_id']?>"><?=$row['ordnum']?></div>
        <div class="quest_websys_dat" data-questid="<?=$row['email_id']?>">
            <?=$row['inclicon']?>
        </div>
        <div class="quest_status <?=$row['assign_class']?>">
            <div class="quest_replica" data-lead="<?=empty($row['lead_id']) ? 0 : $row['lead_id']?>" data-questid="<?=$row['email_id']?>"><?=$row['lead_number']?></div>
        </div>
        <div class="questshowdetailsrow" data-email="<?=$row['email_id']?>">
            <div class="quest_date"><?=$row['email_date']?></div>
            <div class="quest_customname"><?=$row['email_sender']?></div>
            <div class="quest_custommail"><?=$row['email_sendermail']?></div>
            <div class="quest_customphone"><?=$row['email_senderphone']?></div>
            <div class="quest_type"><?=$row['email_subtype']?></div>
            <div class="quest_text" data-event="hover" data-css="question_tooltip" data-bgcolor="#FFFFFF" data-bordercolor="#000"
                 data-position="left" data-textcolor="#000" data-timer="8000" data-delay="1000" data-balloon="<?=$row['email_text']?>">
                <?=$row['email_text']?>
            </div>
            <div class="quest_webpage" data-event="hover" data-css="question_tooltip" data-bgcolor="#FFFFFF" data-bordercolor="#000"
                 data-position="left" data-textcolor="#000" data-timer="4000" data-delay="1000" data-balloon="<?=$row['email_webpage']?>">
                <?=$row['email_webpage']?>
            </div>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>