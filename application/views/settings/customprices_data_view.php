<?php $numpp=1; ?>
<?php foreach ($prices as $price): ?>
<div class="pricedatarow <?=$numpp%2 == 0 ? 'greydatarow' : 'whitedatarow'?>" data-price="<?=$price['price_id']?>">
    <div class="custompriceaction">
        <div class="custompriceedit" data-price="<?=$price['price_id']?>">
            <i class="fa fa-pencil"></i>
        </div>
        <div class="custompricedelete" data-price="<?=$price['price_id']?>">
            <i class="fa fa-trash-o"></i>
        </div>
    </div>
    <div class="custompriceqty"><?=$price['qty']?></div>
    <div class="custompriceprice"><?=$price['price']?></div>
</div>
<?php $numpp++; ?>
<?php endforeach; ?>