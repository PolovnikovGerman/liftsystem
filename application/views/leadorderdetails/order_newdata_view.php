<div class="block_5">
    <div class="bl_contact">
        <div class="bl_tx">
            <div class="bl_contact_tx"><img src="/img/leadorder/tx_contact.png" alt="contact"/></div>
        </div>
        <div class="bl_contact_content">
            <div class="contact_content_line contact_content_title">
                <div class="contact_content_name">Name:</div>
                <div class="contact_content_phone">Telephone:</div>
                <div class="contact_content_email">Email:</div>
                <div class="contact_content_art">Art</div>
                <div class="contact_content_inv">Inv</div>
                <div class="contact_content_trk">Trk</div>
            </div>
            <?=$contacts?>
        </div>
    </div>
    <div class="bl_items">
        <div class="bl_tx">
            <div class="bl_items_tx">
                <img src="/img/leadorder/tx_items.png" alt="items"/>
            </div>
        </div>
        <div class="bl_items_content">
            <div class="items_line items_line_title items_content_title">
                <div class="itemnumber_head">Item#</div>
                <div class="itemdescription_head">Description</div>
                <div class="itemcolor_head">Color</div>
                <div class="items_content_qty">Qty</div>
                <div class="items_content_each">Each</div>
                <div class="items_content_sub_total">Sub-total</div>
                <div class="items_content_trash">&nbsp;</div>
            </div>
            <div class="bl_items_content_table items_table_text">
                <div id="orderitemdataarea">
                    <?=$items?>
                </div>
                <?php if ($edit==1) { ?>
                    <div class="items_table_line">
                        <div class="items_content_item2 text_green addleadorderitem">+ add item</div>
                        <div class="orderitem_inventoryview">&nbsp;</div>
                    </div>
                <?php } else { ?>
                <div class="items_table_line">
                    <div class="orderitem_inventoryview">&nbsp;</div>
                </div>
                <?php } ?>
            </div>
        </div>
        <div class="bl_items_content2 items_content2_txt">
            <?=$discounts_view?>
        </div>
        <div class="bl_items_sub-total">
            <div class="bl_items_sub-total1 text_gray text_bold">Item Sub-total:</div>
            <div class="bl_items_sub-total2 text_style_3 text_bold"><?=$subtotal?></div>
        </div>
    </div>
    <div class="bl_ship_tax">
        <div class="bl_tx">
            <div class="bl_ship_tax_tx">
                <img src="/img/leadorder/tx_ship_tax.png"  alt="ship_tax"/>
            </div>
        </div>
        <!-- Ship View -->
        <?=$shippingview?>
        <div class="shippingdatesarea"><?=$shipdatesview?></div>        
    </div>
    <div class="bl_billing">
        <div class="bl_tx">
            <div class="bl_billing_tx">
                <img src="/img/leadorder/tx_billing.png" alt="billing"/>
            </div>
        </div>
        <div class="bl_billing_content">
            <!-- Billing View -->
            <?=$billingview?>
        </div>
    </div>
    <div class="bl_pay_method">
        <div class="bl_tx">
            <div class="bl_pay_method_tx">
                <img src="/img/leadorder/tx_pay_method.png" alt="pay_method"/>
            </div>
        </div>
        <div class="bl_pay_method_content"><?=$chargeview?></div>
    </div>
    <div class="bl_payments">
        <div class="bl_tx">
            <div class="bl_payments_tx">
                <img src="/img/leadorder/tx_payments.png" width="10" height="64" alt="payments">
            </div>
        </div>
        <div class="bl_payments_content">
            <div class="payments_line payments_title">
                <div class="payments_date">Date</div>
                <div class="payments_payment">Payment</div>
                <div class="payments_amnt">Amnt</div>
            </div>
            <?=$payment_history?>
        </div>
        <?=$chargeattemptview?>
    </div>
</div>    
<?php // echo $order_bottom?>