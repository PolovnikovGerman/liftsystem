<div class="zone-form">
    <div class="shipping-place"><?=$zone?></div>
    <div class="shipping-service-topform">
        <div class="topform-service">Service:</div>
        <div class="topform-percent">%</div>
        <div class="dimension-on">dimension on</div>
    </div>
    <?php foreach ($shipdat as $row) {?>
        <div class="shipping-form-row">
            <div class="shipping-form-service"><?=$row['name']?>:</div>
            <div class="shipping-form-percent">
                <input type="text" name="percent<?=($row['id']) ?>" id="percent<?= $row['id'] ?>" value="<?=$row['method_percent']?>" readonly />
            </div>
            <div class="shipping-form-dimension">
                <input type="checkbox" name="dimens<?=$row['id'] ?>" id="dimens<?= $row['id'] ?>" value="1" <?=($row['method_dimens']==1 ? 'checked="checked"' : '')?> disabled/>
            </div>
        </div>
    <?php } ?>
</div>
