<?php foreach ($plates as $plate): ?>
    <div class="plates-table-tr">
        <div class="plates-table-td-move"  data-order="<?=$plate['order_id']?>">
            <?php if ($brand=='SR') { ?>
                <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
            <?php } else { ?>
                <img class="icon-move" src="/img/printscheduler/move-blue.svg">
            <?php } ?>
        </div>
        <div class="plates-table-td-done">
            <input type="checkbox" data-order="<?=$plate['order_id']?>" <?=$plate['print_ready']==0 ? '' : 'checked="checked"'?>/>
        </div>
        <div class="plates-table-td-order"><?=$plate['order_num']?></div>
        <div class="plates-table-td-plates">
            <div class="number-plates"><?=$plate['plates_qty']?></div>
            <div class="btn-plates">
                <div class="ic-teal-art"><img class="img-magnifier-white" src="/img/printscheduler/magnifier-white.svg"></div>
            </div>
        </div>
        <div class="plates-table-td-descriptions"><?=$plate['item_name']?></div>
    </div>
<?php endforeach; ?>
