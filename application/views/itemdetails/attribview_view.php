<div class="attributes_txtdat">
    <div class="attributes_row overflowtext">
        <?php if ($item_description1 == '') { ?>
            <div style="height:16px;width:230px;" class="empty_value">&nbsp;</div>
        <?php } else { ?>
            <a href=javascript:void(0);" style="color:#FFFFFF" class="tooltip-descript" title="<?= $item_description1 ?>"><?= $item_description1 ?></a>
        <?php } ?>
    </div>
    <div class="attributes_row">
        <?php if ($item_description2 == '') { ?>
            <div style="height:16px;width:230px;" class="empty_value">&nbsp;</div>
        <?php } else { ?>
            <a href=javascript:void(0);" style="color:#FFFFFF" class="tooltip-descript" title="<?= $item_description2 ?>"><?= $item_description2 ?></a>
        <?php } ?>
    </div>
</div>
