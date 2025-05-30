<?php $ordernum = ''; $displaymain = 0; $itemname = ''; $order_item=0; $displayplat = 0; ?>
<?php foreach ($stocks as $stock): ?>
    <?php if ($stock['order_num'] != $ordernum): ?>
        <?php $displaymain = 1; $ordernum = $stock['order_num'];?>
    <?php endif; ?>
    <?php if ($stock['order_item_id'] != $order_item): ?>
        <?php $order_item = $stock['order_item_id']; $displayplat = 1; ?>
    <?php endif; ?>
    <div class="stock-table-tr <?=$displaymain==1 ? 'mainorderrow' : ''?>">
        <?php if ($displaymain == 1): ?>
            <div class="stock-table-td-move" data-order="<?=$stock['order_id']?>">
                <?php if ($stock['brand']=='SR') { ?>
                    <img class="icon-move" src="/img/printscheduler/move-yellow.svg">
                <?php } else { ?>
                    <img class="icon-move" src="/img/printscheduler/move-blue.svg">
                <?php } ?>
            </div>
            <div class="itm-table-td-icons">
                <div class="ic-skull <?=$stock['balance_class']?>"><img class="img-skull" src="/img/printscheduler/icon-skull.svg"></div>
                <div class="ic-rush">
                    <?php if ($stock['order_rush']==1 ): ?>
                        <img class="img-rush" src="/img/printscheduler/icon-rush.svg"/>
                    <?php else : ?>
                        &nbsp;
                    <?php endif;?>
                </div>
            </div>
            <div class="stock-table-td-approval <?=$stock['order_blank']==1 ? '' : ($stock['approved']==0 ? 'notapproved' : '')?>">
                <?=$stock['order_blank']==1 ? 'Blank' : ($stock['approved']==0 ? 'Not Approved' : 'Approved')?></div>
            <div class="stock-table-td-order" data-order="<?=$stock['order_id']?>"><?=$stock['order_num']?></div>
            <div class="stock-table-td-item">
                <div class="stock-table-td-iteminfo"><?=$stock['item_name']?></div>
            </div>
            <div class="stock-table-td-itemend"><img src="/img/icons/stock-table-td-item-end.svg" alt="Item Name End"/></div>
            <?php $displaymain = 0; ?>
            <?php $itemname = $stock['item_name']; ?>
        <?php else: ?>
            <div class="stock-table-td-orderempty">&nbsp;</div>
            <?php if ($itemname != $stock['item_name']) :?>
                 <div class="stock-table-td-item" style="border-left: 1px solid #888888">
                     <div class="stock-table-td-iteminfo"><?=$stock['item_name']?></div>
                 </div>
                 <div class="stock-table-td-itemend"><img src="/img/icons/stock-table-td-item-end.svg" alt="Item Name End"/></div>
                 <?php $itemname = $stock['item_name']; ?>
            <?php else : ?>
                <div class="stock-table-td-itemempty">&nbsp;</div>
                <div class="stock-empty_spaceitem">&nbsp;</div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="stock-table-td-itemcolor <?=$stock['stock_class']?>"><?=$stock['color']?></div>
        <div class="stock-table-td-itemqty <?=$stock['stock_class']?>"><?=$stock['item_qty']?></div>
        <div class="stock-table-td-orderchk <?=$stock['stock_class']?>" data-order="<?=$stock['order_id']?>">
            <?php if ($stock['print_ready']==0) : ?>
                <i class="fa fa-square-o" data-order="<?=$stock['order_itemcolor_id']?>"></i>
            <?php else : ?>
                <i class="fa fa-check-square-o" data-order="<?=$stock['order_itemcolor_id']?>"></i>
            <?php endif; ?>
        </div>
        <?php if ($displayplat == 1): ?>
            <div class="stock-empty_space">&plus;</div>
            <div class="stock-table-td-imprints <?=$stock['plate_class']?>"><?=$stock['imprints']?></div>
            <div class="stock-table-td-plates  <?=$stock['plate_class']?>"><?=$stock['plates']?></div>
            <div class="stock-table-td-platescheck  <?=$stock['plate_class']?>" data-order="<?=$stock['order_id']?>">
                <?php if ($stock['order_blank']==1) : ?>
                    &nbsp;
                <?php else : ?>
                    <?php if ($stock['plates_ready']==0) : ?>
                        <i class="fa fa-square-o" data-order="<?=$stock['order_item_id']?>"></i>
                    <?php else : ?>
                        <i class="fa fa-check-square-o" data-order="<?=$stock['order_item_id']?>"></i>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="stock-empty_space">&nbsp;</div>
        <?php endif; ?>
        <?php $displayplat = 0; ?>
    </div>
<?php endforeach; ?>

