<div class="attributes_txtdat">
    <div class="attributes_row overflowtext">
        <?php if ($item_description1 == '') { ?>
            <div style="height:16px;width:230px;" class="empty_value">&nbsp;</div>
        <?php } else { ?>
            <div class="tooltip-descript" data-content="<?= $item_description1 ?>"><?= $item_description1 ?></div>
        <?php } ?>
    </div>
    <div class="attributes_row">
        <?php if ($item_description2 == '') { ?>
            <div style="height:16px;width:230px;" class="empty_value">&nbsp;</div>
        <?php } else { ?>
            <div class="tooltip-descript" data-content="<?= $item_description2 ?>"><?= $item_description2 ?></div>
        <?php } ?>
    </div>
</div>
