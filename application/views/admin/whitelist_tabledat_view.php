<div class="whitelist_datarow" data-whitelistid="0" style="display: none"></div>
<?php $nrow=0;?>
<?php foreach ($senders as $row) {?>
    <div class="whitelist_datarow <?=($nrow%2==0 ? 'whitedatarow' : 'greydatarow')?>" data-whitelistid="<?=$row['email_id']?>">
        <div class="whitelist_action">
            <div class="editwhitelist" data-whitelistid="<?=$row['email_id']?>">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </div>
            <div class="delwhitelist" data-whitelistid="<?=$row['email_id']?>">
                <i class="fa fa-trash-o" aria-hidden="true"></i>
            </div>
        </div>
        <div class="whitelist_senderdata truncateoverflowtext"><?=$row['sender']?></div>
        <div class="whitelist_userdata truncateoverflowtext"><?=$row['user_name']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>

