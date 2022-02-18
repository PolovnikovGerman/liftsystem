<div class="metatxtdat">
    <div class="metatitle">Internal Keywords</div>
    <div class="metavalue">
        <?php if ($item_keywords == '') { ?>
            <div class='empty_value' style='height:58px;width:230px;'>&nbsp;</div>
        <?php } else { ?>
            <?=$item_keywords ?>
        <?php } ?>
    </div>
    <div class="metatitle">Page URL:</div>
    <div class="page_url">
        <?php if ($item_url == '') { ?>
            <div class="empty_value" style="width:172px">&nbsp;</div>
        <?php } else { ?>
            <?= $item_url ?>
        <?php } ?>
    </div>
    <div class="metatitle">Meta Title:</div>
    <div class="metavaluetitle">
        <?php if ($item_meta_title == '') { ?>
            <div class="empty_value" style="width: 230px;">&nbsp;</div>
        <?php } else { ?>
            <?=htmlspecialchars_decode($item_meta_title) ?>
        <?php } ?>
    </div>
    <div class="metatitle">Meta Description:</div>
    <div class="metavalue">
        <?php if ($item_metadescription == '') { ?>
            <div class="empty_value" style="height:58px;width: 230px">&nbsp;</div>
        <?php } else { ?>
            <?= htmlspecialchars_decode($item_metadescription) ?>
        <?php } ?>
    </div>
    <div class="metatitle">Meta Keywords:</div>
    <div class="metavalue">
        <?php if ($item_metakeywords == '') { ?>
            <div class="empty_value" style="height:58px;width: 230px"></div>
        <?php } else { ?>
            <?= htmlspecialchars_decode($item_metakeywords) ?>
        <?php } ?>
    </div>
</div>
