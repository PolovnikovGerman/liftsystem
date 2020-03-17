<div class="delivservicearea">
    <select class="deliveryservicelist" data-shippack="<?= $package['order_shippack_id'] ?>" data-shipaddr="<?= $shipaddr ?>" <?=($package['delivered']==0 ? '' : 'disabled="disabled"')?>>
        <?php foreach ($deliveries as $drow) { ?>
            <option value="<?= $drow ?>" <?= ($drow == $package['deliver_service'] ? 'selected="selected"' : '') ?>><?= $drow ?></option>
        <?php } ?>
    </select>            
</div>
<div class="trackcodeinptarea">
    <input type="text" class="trackcodeinpt" <?=($package['delivered']==0 ? '' : 'readonly="readonly"')?> data-shipaddr="<?= $shipaddr ?>" data-shippack="<?= $package['order_shippack_id'] ?>" value="<?= $package['track_code'] ?>" />
</div>
<?php if (empty($package['track_code'])) { ?>
    <div class="trackcodeupdate" data-shipaddr="<?= $shipaddr ?>" data-shippack="<?= $package['order_shippack_id'] ?>">&nbsp;</div>
<?php } else { ?>
    <div class="trackcodemanage <?= (empty($package['track_code']) ? 'update' : '') ?>" data-shipaddr="<?= $shipaddr ?>" data-shippack="<?= $package['order_shippack_id'] ?>">&nbsp;</div>
<?php } ?>

<div class="trackcodesendarea">
    <?php if (!empty($package['send_date'])) { ?>
        <span class="sendedtrackcode">sent</span>
    <?php } ?>
    <?php if (!empty($package['track_code'])) { ?>
        <input type="checkbox" class="senttrackcode" data-shipaddr="<?= $shipaddr ?>" data-shippack="<?= $package['order_shippack_id'] ?>"/>
    <?php } ?>            
</div>
<?php if (!empty($package['track_code']) && $package['delivered']==0) { ?>
    <div class="trackcoderemove" data-shipaddr="<?= $shipaddr ?>" data-shippack="<?= $package['order_shippack_id'] ?>">&nbsp;</div>
<?php } ?>
