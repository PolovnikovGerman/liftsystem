<div class="quotecontentarea">
    <input type="hidden" id="quotesessionid" value="<?=$quote_session?>"/>
    <input type="hidden" id="quoteleadconnect" value="<?=$lead_id?>"/>
    <input type="hidden" id="quoteleadnumber" value="<?=$quote_id?>"/>
    <input type="hidden" id="quotemapuse" value="<?=$mapuse?>"/>
    <div class="datarow quotetemplaterow">
        <div class="quotetemplatetitle">Template:</div>
        <div class="quotetemplateinpt">
            <select data-entity="quotedat" data-item="quote_template" <?=$edit_mode==0 ? 'disabled="true"' : ''?>>
                <?php foreach ($templlists as $templlist) { ?>
                    <option value="<?=$templlist?>" <?=$templlist==$data['quote_template'] ? 'selected="selected"' : ''?>><?=$templlist?></option>
                <?php } ?>
            </select>
        </div>
        <div class="quoteactionsarea">
            <div class="quoteactionaddorder active" data-quote="<?=$quote_id?>">start order</div>
            <div class="quoteactionduplicate active" data-quote="<?=$quote_id?>">duplicate</div>
            <div class="quoteactionsend active" data-quote="<?=$quote_id?>">send</div>
            <div class="quoteactionpdfdoc active" data-quote="<?=$quote_id?>">pdf</div>
        </div>
        <?php if ($edit_mode==1) { ?>
            <div class="leadquotesavebtn">
                <div class="leadquotebtnlabel">save</div>
            </div>
        <?php } else { ?>
            <div class="leadquoteeditbtn">
                <div class="leadquotebtnlabel">edit</div>
            </div>
        <?php } ?>
    </div>
    <div class="datarow">
        <div class="quotenumbertitle">QUOTE #</div>
        <div class="quotenumbervalue"><?=$data['brand']=='SR' ? '' : 'QB-' ?><?=$data['quote_number']?><?=$data['brand']=='SR' ? '-QS' : '' ?></div>
        <div class="quotedatearea">
            <div class="quotedatetitle">Date: </div>
            <div class="quotedatevalue"><?=date('M j, Y',$data['quote_date'])?></div>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteitemtablehead <?=$data['brand']=='SR' ? 'relieverstab' : 'stresballstab'?>">
            <div class="itemnumber">Item #</div>
            <div class="itemdescription">Description</div>
            <div class="itemcolor">Color</div>
            <div class="itemqty">Qty</div>
            <div class="itemprice">Each</div>
            <div class="quoteitemsubtotal">Sub-total</div>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteitemtabledata">
            <div id="quoteitemtabledata"><?=$itemsview?></div>
            <?php if ($edit_mode==1) { ?>
                <div class="datarow"><div class="addquoteitem">+ add item</div></div>
                <div class="quoteitem_inventoryview">&nbsp;</div>
            <?php } ?>
        </div>
    </div>
    <div class="datarow">
        <div class="leadquotediscountarea">
            <div class="datarow">
                <div class="quotemisschargetitle">
                    <input class="quotediscountinpt quotecommondatainpt" data-item="mischrg_label1" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                           placeholder="Misc Charge" value="<?=$data['mischrg_label1']?>"/>
                </div>
                <div class="quotemisschargevalue">
                    <input class="quotediscountinpt quotecommondatainpt"  data-item="mischrg_value1" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                           placeholder="$0.00" value="<?=$edit_mode==0 ? MoneyOutput($data['mischrg_value1']) : $data['mischrg_value1']?>"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotemisschargetitle">
                    <input class="quotediscountinpt quotecommondatainpt" data-item="mischrg_label2" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                           placeholder="Misc Charge" value="<?=$data['mischrg_label2']?>"/>
                </div>
                <div class="quotemisschargevalue">
                    <input class="quotediscountinpt quotecommondatainpt" data-item="mischrg_value2" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                           placeholder="$0.00" value="<?=$edit_mode==0 ? MoneyOutput($data['mischrg_value2']) : $data['mischrg_value2']?>"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotediscountlabel">Discount:</div>
                <div class="quotediscounttitle">
                    <input class="quotediscountinpt quotecommondatainpt" data-item="discount_label" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                           placeholder="Courtesy Discount" value="<?=$data['discount_label']?>"/>
                </div>
                <div class="quotediscountvalue">
                    <input class="quotediscountinpt quotecommondatainpt" data-item="discount_value" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                           placeholder="$0.00" value="<?=$edit_mode==0 ? MoneyOutput($data['discount_value']) : $data['discount_value']?>"/>
                </div>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteitemsubtotal">
            <div class="quoteitemsubtotaltitle">Item Sub-total:</div>
            <div class="quoteitemsubtotalvalue"><?=empty($data['items_subtotal']) ? '&nbsp;' : MoneyOutput($data['items_subtotal'])?></div>
        </div>
    </div>
    <div class="datarow">
        <div class="quotesectionlabel">SHIPPING ADDRESS:</div>
    </div>
    <div class="datarow">
        <div class="quoteshipaddressarea">
            <div class="quoteshipcountryarea">
                <select class="quoteadrinpt quotecountry" <?=$edit_mode==0 ? 'disabled="true"' : '' ?> data-item="shipping_country">
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$data['shipping_country'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                    <?php } ?>
                </select>
                <input type="hidden" id="shipquotecntcode" value="<?=$shipcode?>"/>
            </div>
            <div class="quoteshipaddressother">
                <div class="quoteaddressarea">
                    <input class="quoteadrinpt quoteshipadrother" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_contact"
                           placeholder="Contact Name" value="<?=$data['shipping_contact']?>"/>
                    <input class="quoteadrinpt quoteshipadrother" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_company"
                           placeholder="Company" value="<?=$data['shipping_company']?>"/>
                    <div class="shiplinearea" style="width: 100%">
                        <input type="text" class="quoteadrinpt quoteshipadrother" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_address1"
                           <?=$mapuse==0 ? 'placeholder="Address Line 1"' : ''?> value="<?=$data['shipping_address1']?>" id="shiplead_line1" autocomplete="new-password" aria-autocomplete="none"/>
                    </div>
                    <input class="quoteadrinpt quoteshipadrother" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_address2"
                           placeholder="Address Line 2" value="<?=$data['shipping_address2']?>" autocomplete="new-password"/>
                        <input class="quoteadrinpt quoteshiptown" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_city"
                               placeholder="City" value="<?=$data['shipping_city']?>"/>
                        <div class="quoteshipaddresdistrict"><?=$shipstate?></div>
                        <input class="quoteadrinpt quotepostal" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_zip"
                               placeholder="Zip" value="<?=$data['shipping_zip']?>"/>
                </div>
                <div class="shipaddrescopy" <?=$edit_mode==0 ? 'style="display: none;"' : ''?>>
                    <i class="fa fa-copy"></i>
                </div>
            </div>
            <textarea id="shipingcompileaddress" style="display: none"><?=$shipaddress?></textarea>
        </div>
        <div class="quoterushdataarea">
            <div class="datarow">
                <div class="quoteleadtimelabel">Lead Time:</div>
                <div class="quoteleadtime">
                    <?=$lead_time?>
                </div>
                <div class="quoteleadrush">
                    <input class="quoteleadshipcostinpt quoteleadrushcost quotecommondatainpt" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                    data-item="rush_cost" value="<?=$data['rush_cost']?>"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quoteshippingcostarea"><?=$shiprates?></div>
                <div class="quoteshipcost">
                    <input class="quoteleadshipcostinpt quoteshipcostvalue quotecommondatainpt" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                    data-item="shipping_cost" value="<?=$data['shipping_cost']?>"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotetaxarea"><?=$taxview?></div>
                <div class="quotetaxinput">
                    <div class="quotetaxlabel">Sales Tax</div>
                    <div class="quotetaxvalue">
                        <input class="quoteleadshipcostinpt quotesalestaxvalue quotecommondatainpt" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                               data-item="sales_tax" value="<?=$data['sales_tax']?>"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="quotebillingaddressarea">
            <div class="datarow">
                <div class="quotesectionlabel">BILLING ADDRESS:</div>
            </div>
            <div class="quotebillcountryarea <?=$data['billingsame']==1 ? 'billingsame' : ''?>">
                <select class="quoteadrinpt quotecountry" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_country">
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$data['billing_country'] ? 'selected="selected"' : ''?>><?=$country['country_name']?></option>
                    <?php } ?>
                </select>
                <input type="hidden" id="billcountrycode" value="<?=$bilcode?>"/>
            </div>
            <?php if ($data['quote_id']==0) { ?>
                <div class="quotesamebilligaddress">
                    <div class="billingsameinpt">
                        <i class="fa fa-check-square-o" aria-hidden="true"></i>
                    </div>
                    <span>same</span>
                </div>
            <?php  } ?>
            <div class="quotebilladdressother <?=$data['billingsame']==1 ? 'billingsame' : ''?>">
                <div class="quotebilladdressarea">
                    <input class="quoteadrinpt quotebilladdrother" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_contact"
                           placeholder="Contact Name" value="<?=$data['billing_contact']?>">
                    <input class="quoteadrinpt quotebilladdrother" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_company"
                           placeholder="Company" value="<?=$data['billing_company']?>">
                    <div class="billadrrlinearea" style="width: 100%">
                        <input class="quoteadrinpt quotebilladdrother" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_address1"
                            <?=$mapuse==0 ? 'placeholder="Address Line 1"' : ''?> value="<?=$data['billing_address1']?>" id="bill_line1" aria-autocomplete="none" autocomplete="new-password">
                    </div>
                    <input class="quoteadrinpt quotebilladdrother" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_address2"
                           placeholder="Address Line 2" value="<?=$data['billing_address2']?>">
                    <input class="quoteadrinpt quotetown" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_city"
                           placeholder="City" value="<?=$data['billing_city']?>"/>
                    <div class="quotebilladdresdistrict"><?=$billstate?></div>
                    <input class="quoteadrinpt quotepostal" <?=($edit_mode==0 || $data['billingsame']==1) ? 'disabled="true"' : ''?> data-item="billing_zip"
                           placeholder="Zip" value="<?=$data['billing_zip']?>"/>
                </div>
                <div class="billingaddresscopy" <?=($edit_mode==0 || $data['billingsame']==1) ? 'style="display: none"' : ''?>>
                    <i class="fa fa-copy"></i>
                </div>
                <textarea id="billingcompileaddress" style="display: none"><?=$billaddress?></textarea>
            </div>
        </div>
        <div class="quotenotesarea">
            <div class="datarow">
                <div class="quotesectionlabel">NOTE TO INCLUDE ON QUOTE:</div>
            </div>
            <div class="datarow">
<!--                <textarea class="quotenote" --><?php //=$edit_mode==0 ? 'readonly="readonly"' : ''?><!-- data-item="quote_note">--><?php //=$data['quote_note']?><!--</textarea>-->
                <textarea class="quotenote" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="quote_repcontact"><?=$data['quote_repcontact']?></textarea>
            </div>
<!--            <div class="datarow">-->
<!--                <div class="quotesectionlabel">REP CONTACT INFO:</div>-->
<!--            </div>-->
<!--            <div class="datarow">-->
<!--                <textarea class="quotenote" --><?php //=$edit_mode==0 ? 'readonly="readonly"' : ''?><!-- data-item="quote_repcontact">--><?php //=$data['quote_repcontact']?><!--</textarea>-->
<!--            </div>-->
        </div>
        <div class="quotetotalarea">
            <div class="quotetotals">
                <div class="quotetotallabel">Total:</div>
                <div class="quotetotalvalue"><?=empty($data['quote_total']) ? '&nbsp;' : MoneyOutput($data['quote_total'])?></div>
            </div>
        </div>
    </div>
</div>
