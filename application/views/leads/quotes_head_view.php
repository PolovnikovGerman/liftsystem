<input type='hidden' id='totalquotes' value="<?=$total_rec?>"/>
<input type="hidden" id='orderquotes' value="<?=$order_by?>"/>
<input type="hidden" id="direcquotes" value="<?=$direction?>"/>
<input type="hidden" id="curpagequotes" value="<?=$cur_page?>"/>
<input type="hidden" id="perpagequotes" value="<?=$perpage?>"/>
<input type="hidden" id="onlinequotesbrand" value="<?=$brand?>"/>
<div class="quote_content">
    <div class="quote_header">
        <div class="quotes_selecttype_label">Display:</div>
        <select id="quote_status" class="quote_status_select">
            <option value="1" selected>Not assigned</option>
            <option value="">All Quotes</option>
        </select>
        <input type="text" name="quotesearch" id="quotesearch" class="quotesearch search_input"
               placeholder="Customer,company, email.."/>
        <div class="leadsearch_actions">
            <a class="find_quotebnt" id="find_quote" href="javascript:void(0);">Search It</a>
            <a class="find_quotebnt" id="clear_quote" href="javascript:void(0);">Clear</a>
        </div>

        <div class="quotes_pagination" id="quotepagination"></div>
    </div>
    <div class="quotes_title">
        <div class="quote_ordnum">#</div>
        <div class="quote_brand">
            <select class="quotehideincl" id="quoteincl">
                <option value="1" selected="selected">Not Hidden</option>
                <option value="">All</option>
            </select>
        </div>
        <div class="quote_status">Status</div>
        <div class="quote_date">Date</div>
        <div class="quote_customer">Customer</div>
        <div class="quote_email">Email</div>
        <div class="quote_phone">Phone</div>
        <div class="quote_type">Type</div>
        <div class="quote_qty">Qty</div>
        <div class="quote_item">Item</div>
        <div class="quote_total">Total</div>
    </div>
    <div class="quotes_tabledat"></div>
</div>
