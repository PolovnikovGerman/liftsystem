<?php $ordernum = ''; $displaymain = 0; ?>
<?php foreach ($stocks as $stock): ?>
    <?php if ($stock['order_num'] != $ordernum): ?>
        <?php $displaymain = 1; $ordernum = $stock['order_num'];?>
    <?php endif; ?>
    <div class="stock-table-tr <?=$displaymain==1 ? '' : 'addition'?>">
        <?php if ($displaymain == 1): ?>
            <div class="stock-table-td-move" data-order="<?=$stock['order_id']?>">
                <?php if ($brand=='SR') { ?>
                    <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                <?php } else { ?>
                    <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                <?php } ?>
            </div>
            <div class="stock-table-td-done">
                <input class="stock-done-checkbox" type="checkbox" data-order="<?=$stock['order_id']?>" <?=$stock['print_ready']==0 ? '' : 'checked="checked"'?>/>
            </div>
            <div class="stock-table-td-order"><?=$stock['order_num']?></div>
        <?php endif; ?>
        <div class="stock-table-td-qty"><?=$stock['order_qty']?></div>
        <div class="stock-table-td-itemcolor"><?=$stock['item_color']?></div>
        <div class="stock-table-td-descriptions"><?=$stock['item_name']?></div>
        <?php $displaymain = 0; ?>
    </div>
<?php endforeach; ?>