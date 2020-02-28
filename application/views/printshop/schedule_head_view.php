<input type="hidden" id="scheduletotals" value="<?= $totals ?>"/>
<input type="hidden" id="scheduleperpage" value="250"/>
<input type="hidden" id="schedulecurpage" value="0"/>
<div class="inventoryheadrow">
    <div class="search_orderreports">
        <img src="/img/magnifier.png">
        <input placeholder="Enter order #, customer, Item" value="" class="leadord_searchdata">
        <div class="leadorder_findall">&nbsp;</div>
        <div class="leadorder_clear">&nbsp;</div>
    </div>
</div>
<div class="scheduletablehead">
    <div class="bgntable">&nbsp;</div>
    <div class="num greycel">Event</div>
    <div class="ordernum greycel">Order#</div>
    <div class="qty greycel">Qty</div>
    <div class="itemname greycel">Item</div>
    <div class="itemcolor greycel">Color</div>
    <div class="firstinkcolor greycel">1st ink Color</div>
    <div class="secondinkcolor greycel">2nd ink Color</div>
    <div class="proofs greycel">Proofs</div>
    <div class="plate greycel">Plate</div>
    <div class="endtable">&nbsp;</div>
</div>
<div class="scheduletablebody">
    <div id="schedulesummaryarea" class="summaryrow"><?=$summary?></div>
    <div id="scheduledataarea">&nbsp;</div>
</div>