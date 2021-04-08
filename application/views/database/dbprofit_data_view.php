<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbprofitcontent">
    <input type='hidden' id='totalrecdbprofit' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbydbprofit' value="<?= $order_by ?>"/>
    <input type="hidden" id="directiondbprofit" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagedbprofit" value="<?= $cur_page ?>"/>
    <input type="hidden" id="perpagedbprofit" value="<?=$perpage?>"/>
    <input type="hidden" id="itemprofitbrand" value="<?=$brand?>"/>
    <div class="clearfix"></div>
    <div class="profit_head">
        <div class="gradient1 numinlist">&nbsp;</div>
        <div class="gradient1 editcoll">edit</div>
        <div class="itemnum <?= ($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="item_number">Item #</div>
        <div class="itemname <?= ($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="item_name">Name</div>
        <div class="vendorcost <?= ($order_by == 'vendor_item_cost' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="vendor_item_cost">Cost</div>
        <div class="vendorname <?= ($order_by == 'vendor_name' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="vendor_name">Vendor</div>
        <div class="gradient1 priceseparator">&nbsp;</div>
        <div class="gradient1 pricedatacell">25</div>
        <div class="gradient1 pricedatacell">75</div>
        <div class="gradient1 pricedatacell">150</div>
        <div class="gradient1 pricedatacell">250</div>
        <div class="gradient1 pricedatacell">500</div>
        <div class="gradient1 pricedatacell">1000</div>
        <div class="gradient1 pricedatacell">2500</div>
        <div class="gradient1 pricedatacell">5000</div>
        <div class="gradient1 pricedatacell">10K</div>
        <div class="gradient1 pricedatacell">20K</div>
        <div class="gradient1 emptyspace">&nbsp;</div>
        <div class="gradient1 pricespecdatacell">Prints</div>
        <div class="gradient1 pricespecdatacell">Setup</div>
        <div class="gradient1 emptyspace1">&nbsp;</div>
    </div>
    <div class="clearfix"></div>
    <div class="table-profit" id="dbprofittabinfo"></div>
</div>