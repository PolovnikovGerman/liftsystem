<?php if ($order['payment_total']<$order['revenue']): ?>
    <div class="balanceduebox">
        <div class="balancedue_link">
            <input type="text" style="display: none;" id="checkoutlink" value="<?=$checkoutlink?>"/>
            <div class="balancedue_linkicon">
                <img src="/img/leadorder/icon-link-grey.svg">
            </div>
        </div>
        <div class="balancedue_send"><i class="fa fa-envelope-o" aria-hidden="true"></i></div>
        <div class="balancedue">
            <div class="balancedue_txt">Balance Due:</div>
            <div class="balancedue_price"><?=MoneyOutput($order['revenue']-$order['payment_total'])?></div>
        </div>
    </div>
<?php else: ?>
    <div class="balancedue paid">
        <div class="balancedue_txt">Balance Due:</div>
        <div class="balancedue_price">PAID</div>
    </div>
<?php endif; ?>

