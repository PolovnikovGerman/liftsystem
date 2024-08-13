<?php foreach ($stocks as $stock): ?>
    <div class="stock-table-tr">
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
        <div class="stock-table-td-qty"><?=$stock['order_qty']?></div>
        <div class="stock-table-td-itemcolor"><?=$stock['item_color']?></div>
        <div class="stock-table-td-descriptions"><?=$stock['item_name']?></div>
    </div>
<?php endforeach; // } ?>