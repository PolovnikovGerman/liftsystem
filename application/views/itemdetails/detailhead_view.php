<?php if ($mode=='edit') { ?>
    <input type="hidden" id="session_id" value="<?=$session?>"/>
<?php } ?>
<div class="itemname"><?=htmlspecialchars_decode($item_name)?></div>
<div class="itemsourcearea">
    <?=$itemseq_view?>
</div