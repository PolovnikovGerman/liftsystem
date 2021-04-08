<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbcategorcontent">
    <input type='hidden' id='totalrecdbcateg' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbydbcateg' value="<?= $order_by ?>"/>
    <input type="hidden" id="directiondbcateg" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagedbcateg" value="<?= $cur_page ?>"/>
    <input type="hidden" id="perpagedbcateg" value="<?= $perpage ?>"/>
    <input type="hidden" id="itemcategorybrand" value="<?=$brand?>"/>
    <div class="dbcategory_table_head">
        <div class="gradient1 numinlist">&nbsp;</div>
        <div class="gradient1 editcoll">edit</div>
        <div class="<?= ($order_by == 'item_number' ? "gradient2" : "gradient1") ?> sortcell itemnum" data-sortfld="item_number">Item #</div>
        <div class="<?= ($order_by == 'item_name' ? "gradient2" : "gradient1") ?> sortcell itemname" data-sortfld="item_name">Name</div>
        <div class="<?=$order_by == 'count_up' ? 'gradient2' : 'gradient1' ?> sortcell cntcategories" data-sortfld="count_up">
            <img src="/img/database/upwards.png"/>
        </div>
        <div class="<?=$order_by == 'count_dwn' ? 'gradient2' : 'gradient1' ?> sortcell cntcategories" data-sortfld="count_down">
            <img src="/img/database/downward.png"/>
        </div>
        <?php for ($i = 1; $i < 7; $i++) { ?>
            <div class="gradient1 categoryname">Category <?= $i ?></div>
        <?php } ?>
        <div class="gradient1 emptyspace">
            <div style="width:10px;">&nbsp;</div>
        </div>
    </div>
    <div class="table-categories" id="dbcategtabinfo"></div>
</div>