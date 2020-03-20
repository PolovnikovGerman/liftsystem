<?php if (count($emails)==0) {?>
    <div class="emailnotification_line">
        <div class="emptynotifications">No records</div>
    </div>
<?php } else { ?>
    <?php $nrow=0;?>
    <?php foreach ($emails as $row) {?>
        <div class="emailnotification_line <?=(($nrow%2)==0 ? 'greydatarow' : 'whitedatarow')?>" id="notline<?=$row['notification_id']?>">
            <div class="notification_acltions">
                <div class="editnotification">
                    <a class="edtnotificationlnk" href="javascript:void(0)" data-notification="<?=$row['notification_id']?>"><img src="/img/others/editnotifications.png"/></a>
                </div>
                <div class="deletenotification">
                    <a class="delnotificationlnk" href="javascript:void(0)" data-notification="<?=$row['notification_id']?>"><img src="/img/others/delnotifications.png"/></a>
                </div>
            </div>
            <div class="typenotification"><?=$row['notification_system']?></div>
            <div class="notification_emaildata"><?=$row['notification_email']?></div>
        </div>
        <?php $nrow++;?>
    <?php } ?>
<?php } ?>

