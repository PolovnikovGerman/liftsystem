<?php if ($mode=='view') { ?>
    <div class="itemsequence_area">
        <span>SEQ:</span> <?=$item_sequence?> of <?=$items_total?>
    </div>
<?php } else { ?>
    <div class="itemsequence_area">
        <span>SEQ:</span> <input type="text" id="itemsequence" value="<?=$item_sequence?>"/> of <?=$items_total?>
    </div>
<?php } ?>
