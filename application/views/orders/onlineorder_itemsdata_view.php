<?php foreach ($items as $item) { ?>
    <div class="tr-tableinfoitems">
        <div class="td-tableinfoitems item-img">
            <img src="<?=$item['item_image']?>">
        </div>
        <div class="td-tableinfoitems item-name">
            <p class="tii-name"><?=$item['item_name']?></p>
            <p class="tii-item">Item # <?=$item['item_number']?></p>
        </div>
        <div class="td-tableinfoitems item-color">
            <?php $numpp = 0; ?>
            <?php foreach ($item['colors'] as $color) { ?>
                <?php if ($numpp > 0) { ?>
                    <br>
                <?php } ?>
                <?=$color['color']?>
                <?php $numpp++;?>
            <?php } ?>
        </div>
        <div class="td-tableinfoitems item-qty">
            <?php $numpp = 0; ?>
            <?php foreach ($item['colors'] as $color) { ?>
                <?php if ($numpp > 0) { ?>
                    <br>
                <?php } ?>
                <?=QTYOutput($color['qty'])?>
                <?php $numpp++;?>
            <?php } ?>
        </div>
        <div class="td-tableinfoitems item-price">
            <?php $numpp = 0; ?>
            <?php foreach ($item['colors'] as $color) { ?>
                <?php if ($numpp > 0) { ?>
                    <br>
                <?php } ?>
                <?=MoneyOutput($color['price'])?>
                <?php $numpp++;?>
            <?php } ?>
        </div>
        <div class="td-tableinfoitems item-subtotal">
            <?php $numpp = 0; ?>
            <?php foreach ($item['colors'] as $color) { ?>
                <?php if ($numpp > 0) { ?>
                    <br>
                <?php } ?>
                <?=MoneyOutput($color['subtotal'])?>
                <?php $numpp++;?>
            <?php } ?>
        </div>
        <div class="td-tableinfoitems item-date1"><span><?=date('M j', strtotime($order['proof_date']))?></span></div>
        <div class="td-tableinfoitems item-date2"><span><?=date('M j', $item['shippng'])?></span></div>
        <div class="td-tableinfoitems item-date3"><span><?=date('M j', $item['arrive'])?></span></div>
    </div>
<?php } ?>