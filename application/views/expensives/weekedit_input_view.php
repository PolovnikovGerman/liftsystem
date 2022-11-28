<div class="expensive-edit-radio">
    <?php if ($checked==1) { ?>
        <i class="fa fa-circle checked" data-amount="<?=$datetype?>"></i>
    <?php } else { ?>
        <i class="fa fa-circle-o" data-amount="<?=$datetype?>"></i>
    <?php } ?>
</div>
<div class="expensive-edit-inputval">
    <input id="<?=$datetype?>sum_inpt" <?=$checked==1 ? '' : 'readonly'?> type="text" value=""/>
</div>
