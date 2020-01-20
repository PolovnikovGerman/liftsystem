<div class="itemkeytextrow">
    <div class="itemkeytextname">Item #</div>
    <div class="itemkeytextval">
        <?php if ($item_id == 0) { ?>
            <input id="item_number" name="item_number" value="<?= $item_number ?>" style="width:66px;"/>
        <?php } else { ?>
            <?= $item_number ?>
        <?php } ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Active:</div>
    <div class="itemkeytextval">
        <select id="item_active" name="item_active" class="itemactiveselect">
            <option value="1" <?= ($item_active == 1 ? 'selected' : '') ?>>Yes</option>
            <option value="0" <?= ($item_active == 1 ? '' : 'selected') ?>>No</option>
        </select>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">New:</div>
    <div class="itemkeytextval">
        <select id="item_new" name="item_new" class="itemactiveselect">
            <option value="1" <?= ($item_new == 1 ? 'selected' : '') ?>>Yes</option>
            <option value="0" <?= ($item_new == 1 ? '' : 'selected') ?>>No</option>
        </select>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Template:</div>
    <div class="itemkeytextval">
        <?= $item_template ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Lead A:</div>
    <div class="itemkeytextval">
        <input id="item_lead_a" name="item_lead_a" value="<?= $item_lead_a ?>" style="width:15px;"/><?= ($item_lead_a == 1 ? ' day' : ' days') ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Lead B:</div>
    <div class="itemkeytextval">
        <input id="item_lead_b" name="item_lead_b" value="<?= $item_lead_b ?>" style="width:15px;"/><?= ($item_lead_b == 1 ? ' day' : ' days') ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Lead C:</div>
    <div class="itemkeytextval">
        <input id="item_lead_c" name="item_lead_c" value="<?= $item_lead_c ?>" style="width:15px;"/><?= ($item_lead_c == 1 ? ' day' : ' days') ?>
    </div>
</div>
<div class="itemkeytextrow">
    <div class="itemkeytextname">Material:</div>
    <div class="itemkeytextval">
        <input id="item_material" name="item_material" value="<?= $item_material ?>" style="width:53px;"/>
    </div>
</div>
<div class="itemkeytextrow_gray">Size</div>
<div class="itemkeytextrow_white">
    <input id="item_size" name="item_size" value="<?= $item_size ?>" style="width:103px;" />
</div>
