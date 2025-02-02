<?php foreach ($prices as $price) { ?>
    <div class="itemprice_new"><?=empty($price['sale_price']) ? '' : PriceOutput($price['sale_price'])?></div>
<?php } ?>
