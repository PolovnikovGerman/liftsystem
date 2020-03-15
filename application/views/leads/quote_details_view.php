<div class="quote_popup_content">
    <div class="quote_popup_editform">
        <div class="quote_popup_commons">
            <strong>ONLINE QUOTE</strong>
        </div>
        <div class="quote_popup_commons">
            <div class="quotedetail_date">
                <label>Date</label>
                <input class="quote_dateval" readonly value="<?=$email_date?>"/>
            </div>
            <div class="quotedetail_customer">
                <label>Customer</label>
                <input class="quote_customerval" readonly value="<?=$email_sender?>"/>
            </div>
            <div class="quotedetail_company">
                <label>Company</label>
                <input class="quote_companyval" readonly value="<?=$email_sendercompany?>"/>
            </div>
        </div>
        <div class="quote_popup_commons">
            <div class="quote_detail_email">
                <label>Email</label>
                <input class="quote_emailval" readonly value="<?=$email_sendermail?>" />
            </div>
            <div class="quote_detail_phone">
                <label>Phone</label>
                <input class="quote_phoneval" readonly value="<?=$email_senderphone?>"/></div>
        </div>
        <div class="quote_popup_commons">
            <div class="quotedetail_itemname">
                <label>Item</label>
                <input class="quote_itemnameval" readonly value="<?=$email_item_name?>"/>
            </div>
            <div class="quotedetail_qty">
                <label>Quantity:</label>
                <input class="quote_qtyval" readonly value="<?=$email_qty?>"/>
            </div>
        </div>
        <div class="quote_popup_commons">
            <div class="quotedetail_color">
                <label>Colors:</label>
                <input class="quote_colorval" readonly value="<?=$colors?>"/>
            </div>
            <div class="quotedetail_colorinpr">
                <label>Imprint</label>
                <input class="quote_imprintval" readonly="readonly" value="<?=$colorimprint?>"/>
            </div>
        </div>
        <div class="quote_popup_commons">
            <div class="quotedetail_itemcost">
                <label>Item cost:</label>
                <input class="quote_itemcost" readonly="readonly" value="<?=$itemcost?>"/>
            </div>
            <div class="quotedetail_rushdays">
                <label>Rush days:</label>
                <input class="quote_rushdays" readonly="readonly" value="<?=$rush_days?>"/>
            </div>
            <div class="quotedetail_total">
                <label>TOTAL:</label>
                <input class="quote_totals" readonly="readonly" value="<?=$total?>"
            </div>
        </div>
    </div>
</div>
