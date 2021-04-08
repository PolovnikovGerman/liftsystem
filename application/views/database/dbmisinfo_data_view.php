<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbmisinfocontent">
    <input type='hidden' id='totalrecmisinfo' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbymisinfo' value="<?= $order_by ?>"/>
    <input type="hidden" id="directionmisinfo" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagemisinfo" value="<?= $cur_page ?>"/>
    <input type="hidden" id="perpagemisinfo" value="<?= $perpage ?>"/>
    <input type="hidden" id="itemmisinfobrand" value="<?=$brand?>"/>
    <div class="clearfix"></div>
    <div class="missing_head">
        <div class="numinlist gradient1">&nbsp;</div>
        <div class="editcoll gradient1">edit</div>
        <div class="itemnum <?= ($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="item_number">Item #</div>
        <div class="itemname <?= ($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="item_name">Name</div>
        <div class="missingdata <?= ($order_by == 'missings' ? 'gradient2' : 'gradient1') ?> sortcell" data-sortcell="missings">Missing Info</div>
    </div>
    <div class="clearfix"></div>
    <div class="table-misinfo" id="dbmisinfotabinfo"></div>
</div>