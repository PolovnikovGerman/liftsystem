<input type="hidden" id="pototal_orderby" value="<?= $order ?>"/>
<input type="hidden" id="pototal_direc" value="<?=$direc?>"/>
<input type="hidden" id="pototal_total" value="<?= $total ?>"/>
<input type="hidden" id="totalnotplacedorders" value="<?=$total_nonplaced?>"/>
<input type="hidden" id="pototal_curpage" value="<?= $curpage; ?>"/>
<div class="purchase-order-content">
    <div class="purchaseorder-head">
        <div class="purchaseorder-head-left">
            <select id="showtoplaced" class="showtoplacedpo">
                <option value="show" <?=($showplace=='show' ? 'selected="selected"' : '')?>>Show TO PLACE PO</option>
                <option value="hide" <?=($showplace=='hide' ? 'selected="selected"' : '')?>>Hide TO PLACE PO</option>
            </select>
            <div class="searchpurcaheorders">
                <input type="text" placeholder="Order #" class="searchpoinput" id="searchpoinput"/>
                <div id="pofindit" class="pofindit">Find It</div>
                <div id="pofindclear" class="pofindit">Clear</div>
            </div>
        </div>
        <div class="purchaseorder_selectperpage">
            <select class="selectrecords" id="pototal_perpage">
                <?php foreach ($perpages as $row) { ?>
                    <option value="<?=$row?>" <?=($row==$perpage ? 'selected="selected"' : '')?>><?=$row?> records/per page</option>
                <?php } ?>
            </select>
        </div>
        <div class="purchorder_paginator">&nbsp;</div>
        <div class="paymethods"></div>
    </div>
    <div class="pototals-legend">
        <ul>
            <li><img src="/img/fulfillment/kvadrat-black.png"> Is 0% and under</li>
            <li><img src="/img/fulfillment/kvadrat-maroon.png"> 1% - 9%</li>
            <li><img src="/img/fulfillment/kvadrat-red.png">  10% - 19% </li>
            <li><img src="/img/fulfillment/kvadrat-orange.png"> 20% - 29% </li>
            <li><img src="/img/fulfillment/kvadrat-white.png"> 30% - 39%</li>
            <li><img src="/img/fulfillment/kvadrat-green.png"> 40% and higher</li>
        </ul>
    </div>
    <div class="sort_purchase">
        <div class="sort_purchase_val">
            <div class="sort_purchase_label" style="clear: both; float: left; width: 100px;">
                Sort field 1:
            </div>
            <select id="sortpurch1" class="sortpurch" >
                <option value=""> --------</option>
                <?php foreach($sort as $key=>$val) {?>
                    <option value="<?=$key?>" <?=($current_sort==$key ? 'selected="selected"' : '')?>><?=$val?></option>
                <?php } ?>
            </select>
        </div>
        <div class="sort_purchase_val">
            <div class="sort_purchase_label" style="clear: both; float: left; width: 100px;">
                Vendor:
            </div>
            <select id="vendorfilter" class="sortpurch" >
                <option value="">All </option>
                <?php foreach($vendors as $row) {?>
                    <option value="<?=$row['vendor_id']?>"><?=$row['vendor_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="showhideclosed">
            <select id="curstatus" name="curstatus" class="sortpurch">
                <option value="hideclose" <?=($curstatus=='hideclose' ? 'selected="selected"' : '')?>>Show closed</option>
                <option value="showclosed" <?=($curstatus=='showclosed' ? 'selected="selected"' : '')?>>Hide closed</option>
            </select>
        </div>
    </div>
    <div id="notplacedordersarea">
        <?=$nonplacedview?>
    </div>
    <div class="purchase-order-table">
        <div class="purchase-order-title">
            <div class="purchase-order-profit">Profit Exp</div>
            <div class="purchase-order-profitperc">%</div>
            <div class="purchase-order-actions">
                <!-- <i class="fa fa-plus-circle" aria-hidden="true" id="addnewamount"></i> -->
                <!-- <a id="addnewamount" href="javascript:void(0);" class="searchbtn"><em></em><span>Add</span><b></b></a> -->
                <i class="fa fa-plus" aria-hidden="true" title="Add New Purchase    "></i>
            </div>
            <div class="purchase-order-date">Date</div>
            <div class="purchase-order-ordnum">PO #</div>
            <div class="purchase-order-amount">PO Amnt</div>
            <div class="purchase-order-vendor">Vendor</div>
            <div class="purchase-order-method">Method</div>
            <div class="purchase-order-reason">Last Update Reason</div>
            <div class="purchase-order-reason">Low Profit Reason</div>
        </div>
        <div class="clearfix"></div>
        <div class="purchase-order-content" id="tableinfotab3">&nbsp;</div>
    </div>

</div>
<input type="hidden" id="purchaseordersbrand" value="<?=$brand?>"/>
<div id="purchaseordersbrandmenu">
    <?=$top_menu?>
</div>
