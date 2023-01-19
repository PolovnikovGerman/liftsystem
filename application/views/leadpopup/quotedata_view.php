<div class="quotecontentarea">
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
            <div class="quoteactionaddorder">start order</div>
            <div class="quoteactionduplicate">duplicate</div>
            <div class="quoteactionsend">send</div>
            <div class="quoteactionpdfdoc">pdf</div>
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
        <div class="quoteitemtabledata">&nbsp;</div>
    </div>
    <div class="datarow">
        <div class="leadquotediscountarea">
            <div class="datarow">
                <div class="quotemisschargetitle">
                    <input class="quotediscountinpt" placeholder="Misc Charge"/>
                </div>
                <div class="quotemisschargevalue">
                    <input class="quotediscountinpt" placeholder="$0.00"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotemisschargetitle">
                    <input class="quotediscountinpt" placeholder="Misc Charge"/>
                </div>
                <div class="quotemisschargevalue">
                    <input class="quotediscountinpt" placeholder="$0.00"/>
                </div>
            </div>
            <div class="datarow">
                <div class="quotediscountlabel">Discount:</div>
                <div class="quotediscounttitle">
                    <input class="quotediscountinpt" placeholder="Courtesy Discount"/>
                </div>
                <div class="quotediscountvalue">
                    <input class="quotediscountinpt" placeholder="$0.00"/>
                </div>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteitemsubtotal">
            <div class="quoteitemsubtotaltitle">Item Sub-total:</div>
            <div class="quoteitemsubtotalvalue">$111.50</div>
        </div>
    </div>
    <div class="datarow">
        <div class="quotesectionlabel">SHIPPING ADDRESS:</div>
    </div>
    <div class="datarow">
        <div class="quoteshipaddressarea">
            <div class="quoteshipcountryarea">
                <select class="quoteshipcountry">
                    <option value="US">United States</option>
                </select>
            </div>
            <div class="quoteshipaddressother">
                <div class="datarow">
                    <input class="quoteshipadrother">
                </div>
            </div>
        </div>
        <div class="quoterushdataarea"></div>
    </div>

</div>