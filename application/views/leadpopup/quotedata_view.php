<div class="quotecontentarea">
    <input type="hidden" id="quotesessionid" value="<?=$quote_session?>"/>
    <input type="hidden" id="quoteleadconnect" value="<?=$lead_id?>"/>
    <input type="hidden" id="quoteleadnumber" value="<?=$quote_id?>"/>
    <div class="datarow">
        <div class="quotetemplatetitle">Template:</div>
        <div class="quotetemplateinpt">
            <select data-entity="quotedat" data-item="quote_template" <?=$edit_mode==0 ? 'disabled="true"' : ''?>>
                <?php foreach ($templlists as $templlist) { ?>
                    <option value="<?=$templlist?>" <?=$templlists==$data['quote_template'] ? 'selected="selected"' : ''?>><?=$templlist?></option>
                <?php } ?>
            </select>
        </div>
        <div class="quoteactionsarea">
            <div class="quoteactionaddorder <?=$edit_mode==1 ? '' : 'active'?>" data-quote="<?=$quote_id?>">start order</div>
            <div class="quoteactionduplicate <?=$edit_mode==1 ? '' : 'active'?>" data-quote="<?=$quote_id?>">duplicate</div>
            <div class="quoteactionsend <?=$edit_mode==1 ? '' : 'active'?>" data-quote="<?=$quote_id?>">send</div>
            <div class="quoteactionpdfdoc <?=$edit_mode==1 ? '' : 'active'?>" data-quote="<?=$quote_id?>">pdf</div>
        </div>
        <?php if ($edit_mode==1) { ?>
            <div class="leadquotesavebtn">save</div>
        <?php } else { ?>
            <div class="leadquoteeditbtn">edit</div>
        <?php } ?>
    </div>
    <div class="datarow">
        <div class="quotenumbertitle">QUOTE #</div>
        <div class="quotenumbervalue"><?=$data['brand']=='SR' ? '' : 'QB-' ?><?=$data['quote_number']?><?=$data['brand']=='SR' ? '-QS' : '' ?></div>
        <div class="quotedatearea">
            <div class="quotedatetitle">Date: </div>
            <div class="quotedatevalue"><?=date('d/m/Y',$data['quote_date'])?></div>
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
            <?=$itemsview?>
            <?php if ($edit_mode==1) { ?>
                <div class="datarow"><div class="addquoteitem">+ add item</div></div>
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
            <div class="quoteitemsubtotalvalue"><?=empty($data['item_subtotal']) ? '&nbsp;' : MoneyOutput($data['item_subtotal'])?></div>
        </div>
    </div>
    <div class="datarow">
        <div class="quotesectionlabel">SHIPPING ADDRESS:</div>
    </div>
    <div class="datarow">
        <div class="quoteshipaddressarea">
            <div class="quoteshipcountryarea">
                <select class="quoteaddressinpt quotecountry" <?=$edit_mode==0 ? 'disabled="true"' : '' ?> data-item="shipping_country">
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$data['shipping_country'] ? 'seleted="selected"' : ''?>><?=$country['country_name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="quoteshipaddressother">
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="shipping_contact"
                           placeholder="Contact Name" value="<?=$data['shipping_contact']?>"/>
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="shipping_company"
                           placeholder="Company" value="<?=$data['shipping_company']?>"/>
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="shipping_address1"
                           placeholder="Address Line 1" value="<?=$data['shipping_address1']?>"/>
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="shipping_address2"
                           placeholder="Address Line 2" value="<?=$data['shipping_address1']?>"/>
                </div>
            </div>
            <div class="quoteshipaddresszip">
                <div class="datarow">
                    <div class="quoteshipaddreszipcode">
                        <input class="quoteaddressinpt quotepostal" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="shipping_zip"
                               placeholder="Zip" value="<?=$data['shipping_zip']?>"/>
                    </div>
                    <div class="quoteshipaddrescity">
                        <input class="quoteaddressinpt quotetown" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> data-item="shipping_city"
                               placeholder="City" value="<?=$data['shipping_city']?>"/>
                    </div>
                    <div class="quoteshipaddresdistrict">
                        <?php if (is_array($shipstates)) { ?>
                            <select class="quoteaddressinpt quotestate" <?=$edit_mode==0 ? 'disabled="true"' : ''?> data-item="shipping_state">
                                <option value=""></option>
                                <?php foreach ($shipstates as $state) { ?>
                                    <option value="<?=$state['state_code']?>" <?=$state['state_code']==$data['shipping_state'] ? 'selected="selected"' : ''?>><?=$state['state_code']?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="quoterushdataarea">
            <div class="datarow">
                <div class="quoteleadtimelabel">Lead Time:</div>
                <div class="quoteleadtime">
                    <select class="quoteleadtimeselect" <?=$edit_mode==0 ? 'disabled="true"' : ''?>>
                        <option value="Standard">Standard 5 days</option>
                        <option value="3 day Rush">3 day Rush</option>
                        <option value="1 day Rush">1 day Rush</option>
                    </select>
                </div>
                <div class="quoteleadrush">
                    <input class="quoteleadshipcostinpt quoteleadrushcost quotecommondatainpt" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                    data-input="rush_cost" value="<?=$data['rush_cost']?>"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quoteshippingcostarea">&nbsp;</div>
                <div class="quoteshipcost">
                    <input class="quoteleadshipcostinpt quoteshipcostvalue quotecommondatainpt" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>
                    data-item="shipping_cost" value="<?=$data['shipping_cost']?>"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotetaxarea">
                    <div class="datarow">
                        <div class="quotetaxlabelveiw">Out of State</div>
                        <div class="quotetaxemptyview">- No sales tax collected</div>
                    </div>
                </div>
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
            <div class="quotebillcountryarea">
                <select class="quoteaddressinpt quotecountry" <?=$edit_mode==0 ? 'disabled="true"' : ''?>>
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$data['billing_country'] ? 'seleted="selected"' : ''?>><?=$country['country_name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="quotebilladdressother">
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> placeholder="Contact Name">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> placeholder="Company">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> placeholder="Address Line 1">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> placeholder="Address Line 2">
                </div>
                <div class="datarow">
                    <div class="quotebilladdreszipcode">
                        <input class="quoteaddressinpt quotepostal" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> placeholder="Zip"/>
                    </div>
                    <div class="quotebilladdrescity">
                        <input class="quoteaddressinpt quotetown" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?> placeholder="City"/>
                    </div>
                    <div class="quotebilladdresdistrict">
                        <?php if (is_array($billstates)) { ?>
                            <select class="quoteaddressinpt quotestate" <?=$edit_mode==0 ? 'disabled="true"' : ''?>>
                                <option value=""></option>
                                <?php foreach ($billstates as $state) { ?>
                                    <option value="<?=$state['state_code']?>" <?=$state['state_code']==$data['billing_state'] ? 'selected="selected"' : ''?>><?=$state['state_code']?></option>
                                <?php } ?>
                            </select>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="quotenotesarea">
            <div class="datarow">
                <div class="quotesectionlabel">NOTE TO INCLUDE ON QUOTE:</div>
            </div>
            <div class="datarow">
                <textarea class="quotenote" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>></textarea>
            </div>
            <div class="datarow">
                <div class="quotesectionlabel">REP CONTACT INFO:</div>
            </div>
            <div class="datarow">
                <textarea class="quotenote" <?=$edit_mode==0 ? 'readonly="readonly"' : ''?>><?=$data['quote_repcontact']?></textarea>
            </div>
        </div>
        <div class="quotetotalarea">
            <div class="quotetotals">
                <div class="quotetotallabel">Total:</div>
                <div class="quotetotalvalue"><?=empty($data['quote_total']) ? '&nbsp;' : MoneyOutput($data['quote_total'])?></div>
            </div>
        </div>
    </div>
</div>