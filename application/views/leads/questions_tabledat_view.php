<?php $nrow=0;?>
<?php foreach ($quests as $row) {?>
    <div class="quest_tabrow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?> <?=$row['rowclass']?>">
        <div class="quest_numrec" data-email="<?=$row['email_id']?>"><?=$row['ordnum']?></div>
        <div class="quest_websys_dat" data-questid="<?=$row['email_id']?>">
            <!-- <?=$row['email_websys']?> -->
            <?=$row['inclicon']?>
        </div>
        <div class="quest_status <?=$row['assign_class']?>" data-questid="<?=$row['email_id']?>">
            <div class="quest_replica"><?=$row['lead_number']?></div>
        </div>
        <div class="questshowdetailsrow" data-email="<?=$row['email_id']?>">
            <div class="quest_date"><?=$row['email_date']?></div>
            <div class="quest_customname"><?=$row['email_sender']?></div>
            <div class="quest_custommail"><?=$row['email_sendermail']?></div>
            <div class="quest_customphone"><?=$row['email_senderphone']?></div>
            <div class="quest_type"><?=$row['email_subtype']?></div>
        <div class="quest_text" data-event="hover" data-css="question_tooltip" data-bgcolor="#fff" data-bordercolor="#000" data-textcolor="#000" data-balloon="<?=$row['email_text']?>"><?=$row['email_text']?></div>
            <div class="quest_webpage"><?=$row['email_webpage']?></div>
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>