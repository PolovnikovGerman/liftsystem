<?php $nrow=0;?>
<?php foreach ($data as $row) {?>
    <div class="whitelist_parselog_row <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>">
        <div class="wlparse_date"><?=$row['out_parsed_date']?></div>
        <div class="wlparse_email" data-content="<?=$row['message_from']?>"><?=$row['message_from']?></div>
        <div class="wlparse_subject" data-content="<?=$row['message_subject']?>"><?=$row['message_subject']?></div>
        <div class="wlparse_result"><?=$row['out_parsed_result']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
