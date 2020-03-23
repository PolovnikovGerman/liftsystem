<?php $numpp=1;?>
<?php foreach ($data as $row) { ?>
<div class="userdatarow <?=$numpp%2==0 ? 'greydatarow' : 'whitedatarow'?>">
    <div class="actions">
        <div class="edituser" data-user="<?=$row['user_id']?>" title="Edit">
            <i class="fa fa-pencil" aria-hidden="true"></i>
        </div>
        <div class="deleteuser" data-user="<?=$row['user_id']?>" title="Remove">
            <i class="fa fa-trash-o" aria-hidden="true"></i>
        </div>
        <div class="userstatus" data-user="<?=$row['user_id']?>">
            <input type="hidden" class="userstatusval" value="<?=$row['user_status']?>"/>
            <?php if ($row['user_status']==1) { ?>
                <i class="fa fa-pause-circle-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-play-circle-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
    </div>
    <div class="username truncateoverflowtext"><?=$row['userlogin']?></div>
    <div class="status" data-user="<?=$row['user_id']?>"><?=$row['status_txt']?></div>
    <div class="userrealname truncateoverflowtext"><?=$row['user_name']?></div>
    <div class="useremail truncateoverflowtext"><?=$row['user_email']?></div>
    <div class="userlevel truncateoverflowtext"><?=$row['role_name']?></div>
    <div class="userlastactivity truncateoverflowtext"><?=$row['last_activity']?></div>
</div>
    <?php $numpp++;?>
<?php } ?>
