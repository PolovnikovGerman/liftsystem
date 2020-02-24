<input type="hidden" id="totalvend" value="<?= $total ?>"/>
<input type="hidden" id="perpagevend" value="<?= $perpage ?>"/>
<input type="hidden" id="orderbyvend" value="<?= $order ?>"/>
<input type="hidden" id="directionvend" value="<?= $direc ?>"/>
<input type="hidden" id="curpagevend" value="<?= $curpage ?>"/>
<div class="vendorsview">
    <div class="vendortitlerow">
        <div class="newvendor">add vendor</div>
        <div class="fulfillment_pagination" id="vendorPagination"></div>
    </div>
    <div class="vendortitlerow">
        <div class="vendordata-title">
            <div class="manage">&nbsp;</div>
            <div class="vendor-name">Name</div>
            <div class="inclreport">&nbsp;</div>
            <div class="vendor-zip">Zip Code</div>
            <div class="vendor-calend">Calendar</div>
        </div>
    </div>

    <div class="vendortab" id="vendorinfo"></div>
</div>
