<?php foreach ($shippings as $shipping) { ?>
    <div class="datarow" data-shiprate="<?=$shipping['shipping_code']?>">
        <div class="quoteratecheck <?=$edit_mode==1 ? 'choice' : ''?>" data-shiprate="<?=$shipping['shipping_code']?>">
            <?php if ($shipping['active']==1) { ?>
                <i class="fa fa-check-square-o" aria-hidden="true"></i>
            <?php } else { ?>
                <i class="fa fa-square-o" aria-hidden="true"></i>
            <?php } ?>
        </div>
        <div class="quoteratemethod <?=$shipping['active']==1 ? 'active' : ''?>"><?=$shipping['shipping_name']?> - </div>
        <div class="quoteratecost <?=$shipping['active']==1 ? 'active' : ''?>"><?=MoneyOutput($shipping['shipping_rate'])?> - </div>
        <div class="quoteratearrive <?=$shipping['active']==1 ? 'active' : ''?>"><?=date('D - M j', $shipping['shipping_date'])?></div>
    </div>
<?php } ?>