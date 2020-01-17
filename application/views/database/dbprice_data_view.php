<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbcontent">
    <input type='hidden' id='totalrecdbprice' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbydbprice' value="<?= $order_by ?>"/>
    <input type="hidden" id="directiondbprice" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagedbprice" value="<?= $cur_page ?>"/>
    <input type="hidden" id="perpagedbprice" value="<?=$perpage?>"/>
    <div class="table-price">
        <div class="table-price-header">
            <div class="gradient1 bor-1 numinlist"> &nbsp; </div>
            <div class="gradient1 text-1 head-center editcoll">edit</div>
            <div class="<?=($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemnumber" data-sortfld="item_number">Item #</div>
            <div class="<?=($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemname" data-sortfld="item_name">Item #</div>
            <div class="gradient1 text-1 head-center competitor">Competitor</div>
            <div class="<?= ($order_by == 'price_25' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_25">25</div>
            <div class="<?= ($order_by == 'price_75' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_75">75</div>
            <div class="<?= ($order_by == 'price_150' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_150">150</div>
            <div class="<?= ($order_by == 'price_250' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_250">250</div>
            <div class="<?= ($order_by == 'price_500' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_500">500</div>
            <div class="<?= ($order_by == 'price_1000' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_1000">1000</div>
            <div class="<?= ($order_by == 'price_2500' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_2500">2500</div>
            <div class="<?= ($order_by == 'price_5000' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_5000">5000</div>
            <div class="<?= ($order_by == 'price_10000' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_10000">10K</div>
            <div class="<?= ($order_by == 'price_20000' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_20000">20K</div>
            <div class="<?= ($order_by == 'price_setup' ? 'gradient2' : 'gradient1') ?> text-1  head-center cellsort itemprice" data-sortfld="price_setup">Setup</div>
            <div class="gradient1 priceseparator">&nbsp;</div>
            <div class="gradient1 text-1  head-center lastupdate">Updated</div>
        </div>
        <div class="tabinfo" id="dbpricetabinfo"></div>
    </div>
</div>