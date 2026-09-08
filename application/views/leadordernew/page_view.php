<div class="neworder_body">
    <div class="neworder_column1">
        <div class="ord_block ordblock_details">
            <div class="ordblock_tab">Order Details</div>
            <div class="ordblock_body">
                <div class="orddetails_row">
                    <?=$messages_view?>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_contacts">
                        <div class="orddtls_subtitle">CONTACTS</div>
                        <div class="orddtls_contactsbox">
                            <div class="tbl_contacts">
                                <div class="tblconts_tr tblconts_header">
                                    <div class="tblconts_td tblconts_name">
                                        <div class="tblconts_subtitle">Name:</div>
                                    </div>
                                    <div class="tblconts_td tblconts_phone">
                                        <div class="tblconts_subtitle">Telephone:</div>
                                    </div>
                                    <div class="tblconts_td tblconts_email">
                                        <div class="tblconts_subtitle">Email:</div>
                                    </div>
                                    <div class="tblconts_td tblconts_boxes">
                                        <div class="tblconts_subtitle">Art</div>
                                    </div>
                                    <div class="tblconts_td tblconts_boxes">
                                        <div class="tblconts_subtitle">Inv</div>
                                    </div>
                                    <div class="tblconts_td tblconts_boxes">
                                        <div class="tblconts_subtitle">Trk</div>
                                    </div>
                                </div>
                                <div id="ordercontacts_table" style="width: 100%">
                                    <?=$contacts?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_items">
                        <div class="orddtls_subtitle">ITEMS</div>
                        <div class="table_items">
                            <div class="tblitems_tr tblitems_header">
                                <div class="tblitems_td tblitems_item">Item#</div>
                                <div class="tblitems_td tblitems_descript">Description</div>
                                <div class="tblitems_td tblitems_color">Color</div>
                                <div class="tblitems_td tblitems_qty">Qty</div>
                                <div class="tblitems_td tblitems_each">Each</div>
                                <div class="tblitems_td tblitems_subtotal">Sub-total</div>
                            </div>
                            <?=$itemsview?>
                        </div>
                        <div class="items_footer">
                            <div class="itemsfooter_message">
                                <label>Add’l message to appear on invoice:</label>
                                <textarea class="orderdata" name="invoice_message" data-fld="invoice_message" readonly><?=$order['invoice_message']?></textarea>
                            </div>
                            <div class="itemsfooter_inpts">
                                <div class="inpts_row">
                                    <input type="text" name="misc_charge1" class="orderdata" data-fld="mischrg_label1" readonly="readonly" placeholder="Misc Charge" value="<?=$order['mischrg_label1']?>"/>
                                </div>
                                <div class="inpts_row">
                                    <input type="text" name="misc_charge2" class="orderdata" data-fld="mischrg_label2" readonly="readonly" placeholder="Misc Charge" value="<?=$order['mischrg_label2']?>">
                                </div>
                                <div class="inpts_row">
                                    <input type="text" name="discount_label" class="orderdata" data-fld="discount_label" readonly="readonly" placeholder="Courtesy Discount" value="<?=$order['discount_label']?>">
                                </div>
                            </div>
                            <div class="itemsfooter_inptsprice">
                                <div class="inpts_row">
                                    <div class="inptsprice_box"><?=MoneyOutput($order['mischrg_val1'])?></div>
                                </div>
                                <div class="inpts_row">
                                    <div class="inptsprice_box"><?=MoneyOutput($order['mischrg_val2'])?></div>
                                </div>
                                <div class="inpts_row">
                                    <div class="inptsprice_box"><?=MoneyOutput($order['discount_val'])?></div>
                                </div>
                            </div>
                        </div>
                        <div class="item_subtotal">
                            <div class="itemsubtotal_price"><?=MoneyOutput($order['item_cost'])?></div>
                            <div class="itemsubtotal_txt">Item Sub-total:</div>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_shiptax">
                        <div class="orddtls_subtitle">SHIPPING & TAX</div>
                        <!-- Ship and Tax -->
                        <div class="shiptax_body">
                            <?=$shiptaxview?>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_dates">
                        <div class="ord_shipdate">
                            <div class="ord_datetitle">Ship Date:</div>
                            <div class="ord_datebox"><?=$shipping['out_shipdate']?></div>
                        </div>
                        <div class="ord_arrivaldate">
                            <div class="ord_datetitle">Arrival Date:</div>
                            <div class="ord_datebox"><?=$shipping['out_arrivedate']?></div>
                        </div>
                        <div class="ord_eventdate">
                            <div class="ord_datetitle">Event Date:</div>
                            <div class="ord_datebox"><?=$shipping['out_eventdate']?></div>
                        </div>
                    </div>
                </div>
                <div class="orddetails_row">
                    <div class="orddtls_ordtotal">
                        <div class="ordtotal_title">ORDER TOTAL:</div>
                        <div class="ordtotal_price"><?=MoneyOutput($order['revenue'])?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="neworder_column2">
        <div class="ord_block ordblock_artapproval">
            <div class="ordblock_tab">Art Approval</div>
            <div class="ordblock_body">
                <div class="artapprvl_row">
                    <div class="artapprvl_subtitle">Artwork:</div>
                    <div class="artapprvl_blankrush">
                        <div class="artapprvl_blank">
                            <input id="checkorderblank" type="checkbox" class="orderdata" <?=$order['order_blank']==1 ? 'checked' : ''?> <?=$edit==0 ? 'disabled="disabled"' : ''?>/>
                            <label for="checkorderblank">blank</label>
                        </div>
                        <div class="artapprvl_rush">
                            <input id="checkorderrush" type="checkbox" class="orderdata" <?=$order['order_rush']==1 ? 'checked' : ''?> <?=$edit==0 ? 'disabled="disabled"' : ''?>>
                            <label for="checkorderrush">rush</label>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_instructitle">Instuctions:</div>
                    <div class="artapprvl_templates">
                        <div class="templates_title">Templates:</div>
                        <div class="templatebox">
                            <div class="templatebox_icon" data-url="<?=$empty_url?>" data-title="<?=$empty_title?>">
                                <img src="/img/leadorder/file-alt-green.svg">
                            </div>
                            <div class="templatebox_text">Master</div>
                        </div>
                        <!-- URL for Item AI -->
                        <div class="templatebox">
                            <div class="templatebox_icon">
                                <img src="/img/leadorder/file-alt-green.svg">
                            </div>
                            <div class="templatebox_text">Item</div>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_instrucbox">
                        <textarea <?=$edit==0 ? 'readonly' : ''?>><?=$artwork['artwork_note']?></textarea>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_colors">Colors: <?=$artwork['item_color']?></div>
                    <div class="artapprvl_font">
                        <label>Font:</label>
<!--                        <select>-->
<!--                            <option></option>-->
<!--                            <option>Arial</option>-->
<!--                        </select>-->
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_subtitle">Art:</div>
                    <div class="artapprvl_art">
                        <div class="art_header">
                            <span class="artheader_original">Original:</span>
                            <span class="artheader_vector">Vector:</span>
                        </div>
                        <div class="art_boxes" id="order_art_boxes">
                            <?=$artlocatview?>
                        </div>
                    </div>
                </div>
                <div class="artapprvl_row">
                    <div class="artapprvl_proofs">
                        <!-- Art Proofs -->
                        <?=$proofsview?>
                    </div>
                </div>
                <div class="artapprvl_row"></div>
            </div>
        </div>
        <div class="ord_block ordblock_payment">
            <div class="ordblock_tab">Payment</div>
            <div class="ordblock_body">
                <?=$paymentsview?>
            </div>
        </div>
    </div>
    <div class="neworder_column3">
        <div class="ord_block ordblock_fulfillment">
            <div class="ordblock_tab">Fulfillment</div>
            <div class="ordblock_body">
                <div class="fulflm_row"><?=$profitview?></div>
                <div class="fulflm_row">
                    <div class="fulflm_claymodels">
                        <div class="claymodels_header">Clay Models:</div>
                        <div class="claymodels_body">
                            <?php if ($edit==1) : ?>
                            <div class="claymodels_addoptn">+Add<br>Option</div>
                            <?php endif; ?>
                            <?=$claydocsview?>
                        </div>
                    </div>
                </div>
                <div class="fulflm_row">
                    <div class="fulflm_previewpict">
                        <div class="previewpict_header">Preview Pictures:</div>
                        <div class="previewpict_body">
                            <?php if ($edit==1) : ?>
                            <div class="previewpict_addoptn">+Add<br>Option</div>
                            <?php endif; ?>
                            <?=$prevdocsview?>
                        </div>
                    </div>
                </div>
                <div class="fulflm_row">
                    <div class="fulflm_shipping" id="trackcodesarea">
                    <?=$trackingview?>
                    </div>
                </div>
                <div class="fulflm_row"></div>
            </div>
        </div>
    </div>

</div>
