<?php foreach ($packages as $row) { ?>
    <div class="shiptrackpackrow" data-shippack="<?= $row['order_shippack_id'] ?>" data-shipaddr="<?=$shipaddr?>">
        <div class="delivservicearea">
            <select class="deliveryservicelist" data-shippack="<?= $row['order_shippack_id'] ?>" data-shipaddr="<?=$shipaddr?>" <?=($row['delivered']==0 ? '' : 'disabled="disabled"')?>>
                <?php foreach ($deliveries as $drow) { ?>
                    <option value="<?= $drow ?>" <?= ($drow == $row['deliver_service'] ? 'selected="selected"' : '') ?>><?= $drow ?></option>
                <?php } ?>
            </select>            
        </div>
        <div class="trackcodeinptarea">
            <input type="text" class="trackcodeinpt" <?=($row['delivered']==0 ? '' : 'readonly="readonly"')?> data-shipaddr="<?=$shipaddr?>" data-shippack="<?= $row['order_shippack_id'] ?>" value="<?= $row['track_code'] ?>" />
        </div>
        <?php if (empty($row['track_code'])) { ?>
        <div class="trackcodeupdate" data-shipaddr="<?=$shipaddr?>" data-shippack="<?= $row['order_shippack_id'] ?>">&nbsp;</div>
        <?php } else { ?>
        <div class="trackcodemanage" data-shipaddr="<?=$shipaddr?>" data-shippack="<?= $row['order_shippack_id'] ?>">&nbsp;</div>
        <?php } ?>        
        <div class="trackcodesendarea">
            <?php if (!empty($row['send_date'])) { ?>
                <span class="sendedtrackcode">sent</span>
            <?php } ?>
            <?php if (!empty($row['track_code'])) { ?>
                <input type="checkbox" class="senttrackcode" data-shipaddr="<?=$shipaddr?>" data-shippack="<?= $row['order_shippack_id'] ?>"/>
            <?php } ?>            
        </div>
        <?php if (!empty($row['track_code']) && $row['delivered']==0) { ?>
            <div class="trackcoderemove" data-shipaddr="<?=$shipaddr?>" data-shippack="<?= $row['order_shippack_id'] ?>">&nbsp;</div>
        <?php } ?>
    </div>
<?php } ?>
<div class="shiptrackpackrow" data-shipaddr="<?=$shipaddr?>">
    <div class="newshippack">[+ add package]</div>
</div>
