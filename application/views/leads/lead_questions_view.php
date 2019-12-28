<?php foreach ($quests as $row) {?>
    <div class="lead_popup_questrow">
        <div class="lead_popup_questdate"><?=$row['email_date']?></div>
        <div class="lead_popup_questchck" id="qstview<?=$row['email_id']?>">
            <img src="/img/list.png" alt="View Quest" title="Click to view Question"/>
        </div>
    </div>
<?php } ?>