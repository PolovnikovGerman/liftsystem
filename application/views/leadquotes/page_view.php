<input type="hidden" id="leadquotestotal" value="<?=$total?>"/>
<input type="hidden" id="perpageleadqoutes" value="<?=$perpage?>"/>
<input type="hidden" id="curpageleadquote" value="<?=$cur_page?>"/>
<input type="hidden" id="leadquotesbrand" value="<?=$brand?>"/>
<div class="leadquotesdataview">
    <div class="datarow">
        <div class="pagetitle">Quotes</div>
        <div class="leadquotessearchclear">&nbsp;</div>
        <div class="leadquotessearchall">&nbsp;</div>
        <div class="searchbox-input">
            <input id="leadquotessearch" placeholder="Enter quote#, customer, email"/>
        </div>
        <div class="searchbox"><img src="/img/icons/magnifier.png" alt="Search"/> </div>
    </div>
    <div class="datarow">
        <div class="quotereplicas">
            <select id="quotareplica">
                <option value="" <?=$replica=='' ? 'selected="selected"' : ''?>>View All</option>
                <?php foreach ($replicas as $replica) { ?>
                    <option value="<?=$replica['id']?>"><?=$replica['value']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="leadqutespaginator"></div>
    </div>
    <div class="leadquotes_head">
        <div class="leadquote_date">Date</div>
        <div class="leadquote_number">Quote #</div>
        <div class="leadquote_customer">Customer</div>
        <div class="leadquote_qty">Qty</div>
        <div class="leadquote_item">Item</div>
        <div class="leadquote_revenue">Revenue</div>
        <div class="leadquote_replica">Sales Rep</div>
        <div class="leadquote_pdf">&nbsp;</div>
    </div>
    <div id="leadquote_tabledat"></div>
</div>