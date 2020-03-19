<div class="footerslinks">
    <div class="bottomtxtlnk" data-item="<?=$item['item_id']?>">Bottom Text</div>
    <div class="commontermslnk"  data-item="<?=$item['item_id']?>"><?=$commons?></div>
</div>
<?php if ($edit==0) {?>
    <div class="activate_btn"  data-item="<?=$item['item_id']?>">Activate Editing</div>
<?php } else { ?>
    <div class="saveedit_btn"  data-item="<?=$item['item_id']?>">Save Editing</div>
<?php } ?>