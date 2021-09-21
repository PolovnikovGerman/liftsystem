<?php $nrow=0;?>
<?php foreach ($data as $row) {?>
    <div class="whitelist_parselog_row <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>">
        <div class="wlparse_date"><?=$row['out_parsed_date']?></div>
        <div class="wlparse_email" data-event="hover" data-css="parserlog_tooltip" data-bgcolor="#f0f0f0" data-bordercolor="#999" data-textcolor="#333" data-balloon="<?=$row['message_from']?>"><?=$row['message_from']?></div>
        <div class="wlparse_subject" data-event="hover" data-css="parserlog_tooltip" data-bgcolor="#f0f0f0" data-bordercolor="#999" data-textcolor="#333" data-balloon="<?=$row['message_subject']?>"><?=$row['message_subject']?></div>
        <div class="wlparse_result"><?=$row['out_parsed_result']?></div>
    </div>
    <?php $nrow++;?>
<?php } ?>
