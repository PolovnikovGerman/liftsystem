<div class="quotecontentarea">
    <input type="hidden" id="quotesessionid" value="<?=$quote_session?>"/>
    <input type="hidden" id="quoteleadconnect" value="<?=$lead_id?>"/>
    <div class="datarow">
        <div class="quotetemplatetitle">Template:</div>
        <div class="quotetemplateinpt">
            <select data-entity="quotedat" data-item="quote_template">
                <?php foreach ($templlists as $templlist) { ?>
                    <option value="<?=$templlist?>" <?=$templlists==$data['quote_template'] ? 'selected="selected"' : ''?>><?=$templlist?></option>
                <?php } ?>
            </select>
        </div>
        <div class="quoteactionsarea">
            <div class="quoteactionaddorder <?=$edit_mode==1 ? '' : 'active'?>">start order</div>
            <div class="quoteactionduplicate <?=$edit_mode==1 ? '' : 'active'?>">duplicate</div>
            <div class="quoteactionsend <?=$edit_mode==1 ? '' : 'active'?>">send</div>
            <div class="quoteactionpdfdoc <?=$edit_mode==1 ? '' : 'active'?>">pdf</div>
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
                    <input class="quotediscountinpt quotecommondatainpt" placeholder="Misc Charge"/>
                </div>
                <div class="quotemisschargevalue">
                    <input class="quotediscountinpt quotecommondatainpt" placeholder="$0.00"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotemisschargetitle">
                    <input class="quotediscountinpt quotecommondatainpt" placeholder="Misc Charge"/>
                </div>
                <div class="quotemisschargevalue">
                    <input class="quotediscountinpt quotecommondatainpt" placeholder="$0.00"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotediscountlabel">Discount:</div>
                <div class="quotediscounttitle">
                    <input class="quotediscountinpt quotecommondatainpt" placeholder="Courtesy Discount"/>
                </div>
                <div class="quotediscountvalue">
                    <input class="quotediscountinpt quotecommondatainpt" placeholder="$0.00"/>
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
                <select class="quoteaddressinpt quotecountry">
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$data['shipping_country'] ? 'seleted="selected"' : ''?>><?=$country['country_name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="quoteshipaddressother">
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" placeholder="Contact Name">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" placeholder="Company">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" placeholder="Address Line 1">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quoteshipadrother" placeholder="Address Line 2">
                </div>
            </div>
            <div class="quoteshipaddresszip">
                <div class="datarow">
                    <div class="quoteshipaddreszipcode">
                        <input class="quoteaddressinpt quotepostal" placeholder="Zip"/>
                    </div>
                    <div class="quoteshipaddrescity">
                        <input class="quoteaddressinpt quotetown" placeholder="City"/>
                    </div>
                    <div class="quoteshipaddresdistrict">
                        <?php if (is_array($states)) { ?>
                            <select class="quoteaddressinpt quotestate">
                                <option value=""></option>
                                <?php foreach ($states as $state) { ?>
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
                    <select class="quoteleadtimeselect">
                        <option value="Standard">Standard 5 days</option>
                        <option value="3 day Rush">3 day Rush</option>
                        <option value="1 day Rush">1 day Rush</option>
                    </select>
                </div>
                <div class="quoteleadrush">
                    <input class="quoteleadshipcostinpt quoteleadrushcost quotecommondatainpt"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quoteshippingcostarea">&nbsp;</div>
                <div class="quoteshipcost">
                    <input class="quoteleadshipcostinpt quoteshipcostvalue quotecommondatainpt"/>
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
                        <input class="quoteleadshipcostinpt quotesalestaxvalue quotecommondatainpt" value="0.00">
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
                <select class="quoteaddressinpt quotecountry">
                    <?php foreach ($countries as $country) { ?>
                        <option value="<?=$country['country_id']?>" <?=$country['country_id']==$data['billing_country'] ? 'seleted="selected"' : ''?>><?=$country['country_name']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="quotebilladdressother">
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" placeholder="Contact Name">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" placeholder="Company">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" placeholder="Address Line 1">
                </div>
                <div class="datarow">
                    <input class="quoteaddressinpt quotebilladdrother" placeholder="Address Line 2">
                </div>
                <div class="datarow">
                    <div class="quotebilladdreszipcode">
                        <input class="quoteaddressinpt quotepostal" placeholder="Zip"/>
                    </div>
                    <div class="quotebilladdrescity">
                        <input class="quoteaddressinpt quotetown" placeholder="City"/>
                    </div>
                    <div class="quotebilladdresdistrict">
                        <?php if (is_array($states)) { ?>
                            <select class="quoteaddressinpt quotestate">
                                <option value=""></option>
                                <?php foreach ($states as $state) { ?>
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
                <textarea class="quotenote"></textarea>
            </div>
            <div class="datarow">
                <div class="quotesectionlabel">REP CONTACT INFO:</div>
            </div>
            <div class="datarow">
                <textarea class="quotenote"><?=$data['quote_repcontact']?></textarea>
            </div>
        </div>
        <div class="quotetotalarea">
            <div class="quotetotals">
                <div class="quotetotallabel">Total:</div>
                <div class="quotetotalvalue"><?=empty($data['total']) ? '&nbsp;' : MoneyOutput($data['total'])?></div>
            </div>
        </div>
    </div>
</div>