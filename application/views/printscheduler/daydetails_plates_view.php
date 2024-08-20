<?php $ordernum = ''; $displaymain = 0; ?>
<?php foreach ($plates as $plate): ?>
    <?php if ($ordernum != $plate['order_num']): ?>
        <?php $ordernum = $plate['order_num']; $displaymain = 1; ?>
    <?php endif; ?>
    <div class="plates-table-tr <?=$displaymain==1 ? '' : 'addition'?>">
        <?php if ($displaymain==1) : ?>
            <div class="plates-table-td-move"  data-order="<?=$plate['order_id']?>">
                <?php if ($brand=='SR') { ?>
                    <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                <?php } else { ?>
                    <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                <?php } ?>
            </div>
            <div class="plates-table-td-done">
                <input type="checkbox" class="plates-done-checkbox" data-order="<?=$plate['order_id']?>" <?=$plate['print_ready']==0 ? '' : 'checked="checked"'?>/>
            </div>
            <div class="plates-table-td-order"><?=$plate['order_num']?></div>
        <?php endif; ?>
        <div class="plates-table-td-plates">
            <div class="number-plates"><?=$plate['plates_qty']?></div>
            <div class="btn-plates">
                <div class="ic-teal-art"><img class="img-magnifier-white" src="/img/printscheduler/magnifier-white.svg"></div>
            </div>
        </div>
        <div class="plates-table-td-descriptions"><?=$plate['item_name']?></div>
    </div>
    <?php $displaymain = 0;?>
<?php endforeach; ?>
