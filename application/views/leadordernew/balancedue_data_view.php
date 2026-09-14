<?php if (floatval($totaldue) > 0): ?> : ?>
    <div class="balanceduebox">
        <div class="balancedue_link" data-link="<?=$checkoutlink?>">
            <div class="balancedue_linkicon">
                <img src="/img/leadorder/icon-link-grey.svg">
            </div>
        </div>
        <div class="balancedue_send"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
        <div class="balancedue">
            <div class="balancedue_txt">Balance Due:</div>
            <div class="balancedue_price"><?=MoneyOutput($totaldue)?></div>
        </div>
    </div>
<?php else: ?>
    <div class="balancedue paid">
        <div class="balancedue_txt">Balance Due:</div>
        <div class="balancedue_price">PAID</div>
    </div>
<?php endif; ?>
