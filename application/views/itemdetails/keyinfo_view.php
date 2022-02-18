<div class="itemkeytextrow">
    <div class="itemkeytextname">Item #</div>
    <div class="itemkeytextval">
        <?php if ($item['item_id'] == 0) { ?>
            <input class="itemactiveinput" data-fld="item_number" value="<?= $item['item_number'] ?>" style="width:66px;"/>
        <?php } else { ?>
            <?= $item['item_number'] ?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Active:</div>
    <div class="itemkeytextval">
        <?php if ($mode=='edit') { ?>
        <select data-fld="item_active" class="itemactiveselect">
            <option value="1" <?= ($item['item_active'] == 1 ? 'selected' : '') ?>>Yes</option>
            <option value="0" <?= ($item['item_active'] == 1 ? '' : 'selected') ?>>No</option>
        </select>
        <?php } else { ?>
            <?=($item['item_active'] == '1' ? 'Yes' : 'No') ?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">New:</div>
    <div class="itemkeytextval">
        <?php if ($mode=='edit') { ?>
        <select data-fld="item_new" class="itemactiveselect">
            <option value="1" <?= ($item['item_new'] == 1 ? 'selected' : '') ?>>Yes</option>
            <option value="0" <?= ($item['item_new'] == 1 ? '' : 'selected') ?>>No</option>
        </select>
        <?php } else { ?>
            <?=($item['item_new'] == '1' ? 'Yes' : 'No') ?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Template:</div>
    <div class="itemkeytextval">
        <?= $item['item_template'] ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Lead A:</div>
    <div class="itemkeytextval">
        <?php if ($mode=='edit') { ?>
            <input data-fld="item_lead_a" value="<?= $item['item_lead_a'] ?>" class="itemactiveinput" style="width:15px;"/><?= ($item['item_lead_a'] == 1 ? ' day' : ' days') ?>
        <?php } else { ?>
            <?=(intval($item['item_lead_a'])==0 ? '<div class="empty_itemkeyvalue" style="width:40px">&nbsp;</div>' : ($item['item_lead_a']==1 ? $item['item_lead_a'].' day' : $item['item_lead_a'].' days'))?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Lead B:</div>
    <div class="itemkeytextval">
        <?php if ($mode=='edit') { ?>
            <input data-fld="item_lead_b" value="<?= $item['item_lead_b'] ?>" class="itemactiveinput" style="width:15px;"/><?= ($item['item_lead_b'] == 1 ? ' day' : ' days') ?>
        <?php } else { ?>
            <?=(intval($item['item_lead_b'])==0 ? '<div class="empty_itemkeyvalue" style="width:40px">&nbsp;</div>' : ($item['item_lead_b']==1 ? $item['item_lead_b'].' day' : $item['item_lead_b'].' days'))?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Lead C:</div>
    <div class="itemkeytextval">
        <?php if ($mode=='edit') { ?>
            <input data-fld="item_lead_c" value="<?= $item['item_lead_c'] ?>" class="itemactiveinput" style="width:15px;"/><?= ($item['item_lead_c'] == 1 ? ' day' : ' days') ?>
        <?php } else { ?>
            <?=(intval($item['item_lead_c'])==0 ? '<div class="empty_itemkeyvalue" style="width:40px">&nbsp;</div>' : ($item['item_lead_c']==1 ? $item['item_lead_c'].' day' : $item['item_lead_c'].' days'))?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Material:</div>
    <div class="itemkeytextval">
        <?php if ($mode=='edit') { ?>
            <input data="item_material" value="<?= $item['item_material'] ?>" class="itemactiveinput" style="width:53px;"/>
        <?php } else { ?>
            <?=($item['item_material'] == '' ? '<div class="empty_itemkeyvalue" style="width:53px;">&nbsp;</div>' : $item['item_material'])?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow_gray">Size</div>
<div class="itemkeytextrow_white">
    <?php if ($mode=='edit') { ?>
        <input data-fld="item_size" class="itemactiveinput" value="<?=$item['item_size'] ?>" style="width:103px;" />
    <?php } else { ?>
        <?=($item['item_size']=='' ? '<div class="empty_itemkeyvalue" style="width:103px">&nbsp;</div>' : htmlspecialchars_decode($item['item_size']))?>
    <?php } ?>
</div>
