<div class="contant-page">
    <div class="container_popup">
        <div class="content-box">
            <div class="content-box-body">
                <div class="cp-row">
                    <div class="info-confirmation">
                        <p class="number-confirmation">Confirmation #<?=$confirm?></p>
                        <p class="date">Date: <span><?=date('m/d/y', $order_date)?></span></p>
                    </div>
                    <div class="info-contact">
                        <h4>Contact Information:</h4>
                        <p>Name: <?=$contact_name?><br>Phone: <?=$contact_phone?><br>Email: <?=$contact_email?></p>
                    </div>
                    <div class="info-billing">
                        <h4>Billing Information:</h4>
                        <?=$billing?>
                    </div>
                    <div class="info-shipping">
                        <h4>Shipping Information:</h4>
                        <?=$shipping?>
                    </div>
                </div>
                <div class="cp-row">
                    <h4 class="cpii-title">Items information:</h4>
                    <div class="cpii-tableinfoitems">
                        <div class="tableinfoitems-title">
                            <div class="tr-tableinfoitems">
                                <div class="td-tableinfoitems item-img">&nbsp;</div>
                                <div class="td-tableinfoitems item-name">Item</div>
                                <div class="td-tableinfoitems item-color">Color/s</div>
                                <div class="td-tableinfoitems item-qty">Quantily</div>
                                <div class="td-tableinfoitems item-price">Price</div>
                                <div class="td-tableinfoitems item-subtotal">Subtotal</div>
                                <div class="td-tableinfoitems item-date1">A proof will<br>be emailed</div>
                                <div class="td-tableinfoitems item-date2">Your order<br>ships!</div>
                                <div class="td-tableinfoitems item-date3">Expected<br>delivery date!</div>
                            </div>
                        </div>
                        <div class="tableinfoitems-body"><?=$items?></div>
                    </div>
                </div>
                <div class="cp-row">
                    <div class="order-summary-left">
                        <p class="osl-name">Items:<br>Imprinting:<br>Rush Options:<br>Shipping:<br>NJ Sales Tax:</p>
                        <p class="osl-prices">
                            <?=MoneyOutput($totals['items'])?><br>
                            <?=MoneyOutput($totals['imprinting'])?><br>
                            <?=MoneyOutput($totals['rush'])?><br>
                            <?=MoneyOutput($totals['shipping'])?><br>
                            <?=MoneyOutput($totals['tax'])?>
                        </p>
                    </div>
                    <div class="order-summary-right">
                        <p class="osr-regularprice">Regular Price:<span><?=MoneyOutput($regular_total)?></span></p>
                        <p class="osr-saling">SALINGS:<span><?=MoneyOutput($salings)?></span></p>
                        <p class="osr-ordertotal">Order Total:<span><?=MoneyOutput($order_total)?></span></p>
                    </div>
                </div>
                <div class="cp-row">
                    <div class="blocks-info">
                        <div class="blockinfo">
                            <h4>Art Information:</h4>
                            <p>We will match up your order with art you previously submitted to us. If we have any questions we
                                will reach out to you.</p>
                        </div>
                        <div class="blockinfo">
                            <h4>Payment Information:</h4>
                            <p>Order was changed to you <?=$payment_card_type?> ending in - <?=substr($payment_card_number,-4);?>.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
