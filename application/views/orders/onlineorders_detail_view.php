<input type='hidden' id="order_id" name="order_id" value="<?= $order['order_id'] ?>"/>
<input type='hidden' id="order_status" name="order_status" value="<?= $order['order_status'] ?>"/>
<div class="grey-fon">
    <div class="upper-block">
        <div class="number">
            <?= $order['order_id'] ?>
        </div>
        <div class="conf">
            <div class="conf-title">Conf # :</div>
            <div class="blok-input-confirm">
                <input type="text" name="order_confirmation" readonly id="order_confirmation"
                       value="<?= $order['order_confirmation'] ?>"/>
            </div>
            <div class="void">
                <div class="void-title">Mark VOID</div>
                <input type="checkbox" id="is_void" name="is_void"
                       value="1" <?= ($order['is_void'] == 0 ? '' : 'checked="checked" readonly') ?> />
            </div>

        </div>
        <div class="or-close">
            <a href="javascript:void(0);" class="closeorderdetails">
                <img src="/img/orderdetail-close.png" alt='close'/>
            </a>
        </div>
        <div class="entered">
            <div class="entered-title" <?= ($order['order_status'] == 'NEW' ? "style='display:none;'" : '') ?>>
                <img src="/img/entered.png"/>
            </div>
            <div class="entered-input" id="entered-input">
                <input type="text"
                       class="replicadat" <?= (strtoupper($order['order_status']) == 'NEW' ? '' : 'readonly="readonly"') ?>
                       name="order_rep" id="order_rep" value="<?= $order['order_rep'] ?>" title="Rep"/>
                <?= $order['order_num_view'] ?>
            </div>
        </div>
    </div> <!-- upper-block -->

    <div class="left-blok">
        <div class="date">
            <div class="date-title"><b>Date:</b></div>
            <div class="blok-input">
                <input type="text" name="order_date" id="order_date" readonly="readonly" style="width:63px;"
                       value="<?= date('m/d/y', strtotime($order['order_date'])) ?>"/>
            </div>
        </div>
        <div class="contact">
            <img src="/img/ordertitle-contact.png"/>
            <div class="contact-info">
                <div class="contact-name">
                    <div class="contact-name-title">Name:</div>
                    <div class="details-input">
                        <input type="text" style="width:66px; margin-right:6px;" name="contact_first_name"
                               id="contact_first_name" value="<?= $order['contact_first_name'] ?>"/>
                        <input type="text" style="width:114px;" name="contact_last_name" id="contact_last_name"
                               value="<?= $order['contact_last_name'] ?>"/>
                    </div>
                </div>
                <div class="contact-tel">
                    <div class="contact-tel-title">Tel:</div>
                    <div class="details-input">
                        <input type="text" style="width:191px;" name="contact_phone" id="contact_phone"
                               value="<?= $order['contact_phone'] ?>"/>
                    </div>
                </div>
                <div class="contact-email">
                    <div class="contact-email-title">Email:</div>
                    <div class="details-input">
                        <input type="text" style="width:191px;" name="contact_email" id="contact_email"
                               value="<?= $order['contact_email'] ?>">
                    </div>
                </div>
            </div>
        </div> <!-- contact -->

        <div class="shipping-info">
            <img src="/img/orderdetail-shipping-info.png"/>
            <div class="shipping-info-orders-blok">
                <textarea name="shipping-info" style="height: 82px;width: 249px;resize: none;"
                          readonly><?= $order['shipping_address'] ?></textarea>
            </div>

            <div class="order-ships-blind">
                <?php if ($order['shipping_blink'] == 1) { ?>
                    <img src="/img/order-ships-blind.png"/>
                <?php } ?>
            </div>

        </div> <!-- shipping-info -->

        <div class="billing-info">
            <img src="/img/orderdetail-billing-info.png"/>
            <div class="shipping-info-orders-blok">
                <textarea name="shipping-info" style="height: 79px;width: 251px;resize: none;"
                          readonly><?= $order['billing_address'] ?></textarea>
            </div>
        </div> <!-- billing-info -->
        <div class="payment-info">
            <img src="/img/order-payment-info.png"/>
            <div class="paymentinfo-ccname">
                <div class="paymentinfo-ccname-title">Trans. Id:</div>
                <div class="paymentinfo-ccname-input">
                    <input type="text" style="width:175px;" readonly="readonly" name="payment_card_name"
                           id="payment_card_name" value="<?= $order['transaction_id'] ?>">
                </div>
            </div>
            <div class="paymentinfo-cc">
                <div class="paymentinfo-cc-title">CC:</div>
                <div class="paymentinfo-cc-input">
                    <input type="text" style="width:175px;" readonly="readonly" name="payment_card_number"
                           id="payment_card_number" value="<?= $order['payment_card_number'] ?>"/>
                </div>
            </div>
            <div class="paymentinfo-exp-cvc">
                <div class="paymentinfo-exp-title">EXP:</div>
                <div class="paymentinfo-exp-input">
                    <input type="text" style="width:61px;" readonly="readonly" name="payment_exp" id="payment_exp"
                           value="<?= $order['payment_exp'] ?>"/>
                </div>
                <div class="paymentinfo-cvc-title">CVC:</div>
                <div class="paymentinfo-cvc-input">
                    <input type="text" style="width:67px;" readonly="readonly" name="payment_card_vn"
                           id="payment_card_vn" value="<?= $order['payment_card_vn'] ?>"/>
                </div>
            </div>
        </div>
    </div> <!-- left-blok -->
    <div class="center-blok">
        <div class="details-blok">
            <div class="po-event">
                <div class="po">
                    <div class="details-title">P.O. #:</div>
                    <div class="details-input" style="margin-left:8px;">
                        <input type="text" style="width:94px;" id="post_office" name="post_office"
                               value="<?= $order['post_office'] ?>"/>
                    </div>
                </div>
                <div class="event">
                    <div class="details-title">Event:</div>
                    <div class="details-input" style="margin-left:9px;">
                        <input type="text" readonly="readonly" style="width:94px;" id="event_date" name="event_date"
                               value="<?= ($order['event_date'] == '' ? '' : date('m/d/y', strtotime($order['event_date']))) ?>"/>
                    </div>
                </div>
            </div>
            <div class="proof-ships-arrive">
                <div class="proof">
                    <div class="details-title" style="padding-left:3px;">Proof:</div>
                    <div class="details-input" style="margin-left:6px;">
                        <input type="text" readonly="readonly" style="width:114px;" name="proof_date" id="proof_date"
                               value="<?= date('m/d/y', strtotime($order['proof_date'])) ?>"/>
                    </div>
                </div>
                <div class="ships">
                    <div class="details-title" style="padding-left:3px;">Ships:</div>
                    <div class="details-input" style="margin-left:6px;">
                        <input type="text" readonly="readonly" style="width:114px;" name="shipping_date"
                               id="shipping_date" value="<?= date('m/d/y', strtotime($order['shipping_date'])) ?>"/>
                    </div>
                </div>
                <div class="arrive">
                    <div class="details-title" style="margin-left:-3px;">Arrive:</div>
                    <div class="details-input" style="margin-left:6px;">
                        <input type="text" style="width:114px;" readonly="readonly" name="arrive_date" id="arrive_date"
                               value="<?= date('m/d/y', strtotime($order['arrive_date'])) ?>"/>
                    </div>
                </div>
            </div>
        </div> <!-- details-blok -->
        <div class="order-info">
            <img src="/img/order-title-info.png"/>
            <div class="order-info1">
                <div class="qty">
                    <div class="order-info-title1" style="margin-left:11px;">Qty:</div>
                    <div class="blok-input">
                        <input type="text" style="width:43px;" readonly="readonly" name="item_qty" id="item_qty"
                               value="<?= $order['item_qty'] ?>"/>
                    </div>
                </div>
                <div class="item">
                    <div class="order-info-title1" style="margin-left:81px;">Item:</div>
                    <div class="blok-input">
                        <input type="text" style="width:191px;" readonly="readonly" name="item_name" id="item_name"
                               value="<?= $order['item_name'] ?>"/>
                    </div>
                </div>
                <div class="priceea">
                    <div class="order-info-title1" style="margin-left:10px;">Price Ea:</div>
                    <div class="blok-input">
                        <input type="text" style="text-align: right;width:67px;" readonly="readonly" id="item_price"
                               name="item_price" value="<?= $order['item_price'] ?>"/>
                    </div>
                </div>
            </div>
            <div class="order-info2">
                <div class="prod-ship-coupon" style="width: 167px;padding-top: 4px;">
                    <div class="order-info-title1" style="margin-left:11px;float: left;margin-right: 5px;">Item #</div>
                    <div class="order-itemnum-val" style="float:left; width:92px;">
                        <input id="item_number" type="text" style="width: 68px;" name="item_number"
                               value="<?= $order['item_number'] ?>" readonly="readonly"/>
                    </div>
                </div>
                <div class="items-imprinting-shipping">
                    <div class="items">
                        <div class="order-info-title2" style="margin-left:30px;">Items:</div>
                        <div class="details-input" style="margin-left:4px;">
                            <input type="text" style="text-align: right;width:67px;" readonly="readonly"
                                   name="pure_price" id="pure_price" value="<?= $order['pure_price'] ?>"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="order-info2">
                <div class="color-breakdown">
                    <div class="order-info-title1" style="margin-left:11px;">Color Breakdown:</div>
                    <div class="blok-input">
                        <textarea id="color-breakdown" readonly="readonly" rows="2"
                                  style="width:161px; height: 39px;resize: none;"
                                  name="color-breakdown"><?= $order['order_colors'] ?></textarea>
                    </div>
                </div>
                <div class="items-imprinting-shipping">
                    <div class="imprinting">
                        <div class="order-info-title2">Imprinting:</div>
                        <div class="details-input" style="margin-left:4px;">
                            <input type="text" style="text-align: right;width:67px;" readonly="readonly"
                                   id="inprinting_price" name="inprinting_price"
                                   value="<?= $order['inprinting_price'] ?>"/>
                        </div>
                    </div>
                    <div class="imprinting">
                        <div class="order-info-title2" style="margin-left:32px;">Rush:</div>
                        <div class="details-input" style="margin-left:4px;">
                            <input type="text" style="text-align: right;width:67px;" readonly="readonly" id="rush_price"
                                   name="rush_price" value="<?= $order['rush_price'] ?>"/>
                        </div>
                    </div>
                    <div class="imprinting">
                        <div class="order-info-title2" style="margin-left: 9px;">Shipping:</div>
                        <div class="details-input" style="margin-left:4px;">
                            <input type="text" style="text-align: right;width:67px;" readonly="readonly"
                                   id="shipping_price" name="shipping_price" value="<?= $order['shipping_price'] ?>"/>
                        </div>
                    </div>

                </div>
            </div>
            <div class="order-info3">
                <div class="prod-ship-coupon">
                    <div class="prod">
                        <div class="order-info-title2" style="margin-left:21px;">Prod:</div>
                        <div class="details-input" style="margin-left:3px;">
                            <input type="text" style="width:87px;" readonly="readonly" id="production_term"
                                   name="production_term" value="<?= $order['production_term'] ?>"/>
                        </div>
                    </div>
                    <div class="ship">
                        <div class="order-info-title2" style="margin-left:20px;">Ship:</div>
                        <div class="details-input" style="margin-left:3px;">
                            <input type="text" style="width:87px;" readonly="readonly" id="shipping_method_name"
                                   name="shipping_method_name" value="<?= $order['shipping_method_name'] ?>">
                        </div>
                    </div>
                    <div class="coupon">
                        <div class="order-info-title2" style="margin-left:0px;">Coupon:</div>
                        <div class="details-input" style="margin-left:3px;">
                            <input type="text" style="width:87px;" name="coupon_name" readonly="readonly"
                                   id="coupon_name" value="<?= $order['coupon_name'] ?>"/>
                        </div>
                    </div>
                </div>
                <div class="nj-discount-total">
                    <div class="nj">
                        <div class="order-info-title2" style="margin-left:34px;">NJ Tax:</div>
                        <div class="details-input" style="margin-left:15px;float: left">
                            <input type="text" style="text-align: right;width:67px;" readonly="readonly" id="tax"
                                   name="tax" value="<?= $order['tax'] ?>"/>
                        </div>
                    </div>
                    <div class="discount">
                        <div class="order-info-title2" style="margin-left:11px;">Coupon $$:</div>
                        <div class="details-input" style="margin-left:3px;">
                            <input type="text" style="text-align: right;width:67px;color: #82060e" readonly="readonly"
                                   id="discount" name="discount" value="<?= $order['discount'] ?>"/>
                        </div>
                    </div>
                    <div class="total">
                        <div class="order-info-title2" style="margin-left:14px;">Total:</div>
                        <div class="details-input" style="margin-left:3px;">
                            <input type="text" style="text-align: right;width:97px; font-size: 12px;" readonly
                                   name="total" id="total" value="<?= $order['total'] ?>"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="comments-orders">
            <img src="/img/order-comments-info.png"/>
            <div class="customer-comments">
                <div class="customer-comments-name"><b>Customer’s Comments:</b></div>
                <textarea name="order_customer_comment" cols="37" rows="3" readonly="readonly"
                          style="resize: none;"><?= $order['order_customer_comment'] ?></textarea>
            </div>
            <div class="our-comments">
                <div class="our-comments-name"><b>Our Comments:</b></div>
                <textarea name="order_our_comment" cols="37" rows="3"
                          style="resize: none;"><?= $order['order_our_comment'] ?></textarea>
            </div>
        </div>
    </div>
    <div class="right-blok">
        <div class="order-imprint"><?= $imprint ?></div>
        <img src="/img/order-artwork-info.png"/>
        <?= $artwork ?>
    </div>

</div>
