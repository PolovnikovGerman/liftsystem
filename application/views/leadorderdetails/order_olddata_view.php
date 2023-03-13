<div class="block_5">
    <div class="orderssystemswitch"><?=$template_switch?></div>    
    <div class="info_text">Details for this order can be found in Quickbooks</div>
    <div class="bl_contact">
        <div class="bl_tx2">
            <div class="bl_contact_tx2 text_blue text_bold">CONTACT</div>
        </div>
        <div class="bl_contact_content2">
            <div class="contact_content_line contact_content_title">
                <div class="contact_content_name">Name:</div>
                <div class="contact_content_phone">Telephone:</div>
                <div class="contact_content_email">Email:</div>
                <div class="contact_content_art">Art</div>
                <div class="contact_content_inv">Inv</div>
                <div class="contact_content_trk">Trk</div>
            </div>
            <div class="contact_content_line">
                <div class="contact_content_name">
                    <input type="text" class="contact_name_input contact_input input_border_gray inputleadorddata" value="<?=$customer_contact?>" 
                    <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="artwork" data-field="customer_contact"')?>/>
                </div>
                <div class="contact_content_phone">
                    <input type="text" class="contact_phone_input contact_input input_border_gray inputleadorddata" value="<?=$customer_phone?>"
                      <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="artwork" data-field="customer_phone"')?>/>
                </div>
                <div class="contact_content_email">
                    <input type="text" class="contact_email_input contact_input input_border_gray inputleadorddata" value="<?=$customer_email?>"
                       <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="order" data-field="customer_email"')?> />
                </div>
                <div class="contact_content_art contact_art_input">
                    <input type="checkbox" name="name4" value="c1" class="input_checkbox" disabled="disabled" <?=$customer_art==1 ? 'checked="checked"' : ''?> />
                </div>
                <div class="contact_content_inv contact_inv_input">
                    <input type="checkbox" name="name4" value="c1" class="input_checkbox" disabled="disabled" <?=$customer_inv==1 ? 'checked="checked"' : ''?> />
                </div>
                <div class="contact_content_trk contact_trk_input">
                    <input type="checkbox" name="name4" value="c1" class="input_checkbox" disabled="disabled" <?=$customer_track==1 ? 'checked="checked"' : ''?> />
                </div>
            </div>
        </div>
    </div>
    <div class="bl_inputs">
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <div class="bl_inputs1_tx">Ship Date:</div>
                <input type="text" readonly="readonly" class="bl_inputs1_input input_border_black inputleadorddata" value="<?=($shipdate=='' ? '' : date('m/d/Y', $shipdate))?>" 
                <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="order" data-field="shipdate" id="shipdatecalendinput"')?> />
            </div>
        </div>
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <div class="bl_inputs1_tx">Sales Rep:</div>
                <?=$replica_view?>
            </div>
        </div>
    </div>
    <div class="bl_items">
        <div class="bl_tx2">
            <div class="bl_contact_tx2 text_blue text_bold">ITEMS</div>
        </div>
        <?=$item_view?>
    </div>
    <div class="bl_inputs">
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <div class="bl_inputs1_tx">Sub-total:</div>
                <div class="bl_subtotal_txt"><?=MoneyOutput($subtotal)?></div>
            </div>
        </div>
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <input type="checkbox" <?=($edit==0 ? 'disabled="disabled"' : '')?> class="input_checkbox" id="leadordercredcard"
                 <?=$cc_fee==0 ? '' : 'checked="checked"'?>/> Credit Card Order?
            </div>
        </div>
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <div class="bl_inputs1_tx">Shipping:</div>
                <input type="text" class="bl_inputs1_input input_text_right input_border_black" id="shippingcostdata" value="<?=$out_shipping?>"
                <?=($edit==0 ? 'readonly="readonly"' : '')?> />                
            </div>
        </div>
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <div class="bl_inputs1_tx">Sales Tax:</div>
                <input type="text" class="bl_inputs1_input input_text_right input_border_black" <?=$taxalign?> id="taxsalecostdata" value="<?=$out_tax?>"
                <?=($edit==0 ? 'readonly="readonly"' : '')?> />
            </div>
        </div>
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
                <div class="billing_content2_line">
                    <div class="billing_content2_bl1 <?=$invoice_class?>">
                        <div class="b_content2_bl1_txt <?=($invoice_class=='active' ? 'text_white' : 'text_gray')?>">INV</div>
                    </div>
                    <div class="block_8 text_white">
                        <div class="block_8_backgr1">&nbsp;</div>
                        <div class="block_8_backgr2 block_8_text">
                            <div class="block_8_text1">TOTAL:</div>
                            <div class="block_8_text3 text_bold">
                                <?php if ($edit==0) { ?>
                                    <?=$out_revenue?>
                                <?php } else { ?>
                                    <input type="text" class="bl_inputs1_input input_border_black" id="revenuevaluedata" value="<?=$out_revenue?>"/>                                    
                                <?php } ?>

                            </div>
                        </div>
                        <div class="block_8_backgr3">&nbsp;</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bl_inputs1">
            <div class="bl_inputs1_line">
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
                </div>
            </div>
        </div>
    </div>			
</div>
<?php if (isset($order_bottom)) { ?>
    <?=$order_bottom?>
<?php } ?>
