<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbcontent">
    <input type='hidden' id='totalrecdbprice' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbydbprice' value="<?= $order_by ?>"/>
    <input type="hidden" id="directiondbprice" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagedbprice" value="<?= $cur_page ?>"/>
    <input type="hidden" id="searchdbprice" value="<?= $search ?>"/>
    <input type="hidden" id="perpagedbprice" value="<?=$perpage?>"/>
    <div class="table-price">
        <table cellspacing="0" cellpadding="0">
            <tr class="table_head">
                <td class="gradient1 bor-1">
                    <div style="width: 30px;">&nbsp;</div>
                </td>
                <td class="gradient1 text-1 head-center">
                    <div style="width:26px;font-size: 11px;">edit</div>
                </td>
                <td class="<?= ($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> text-1  head-center"
                    id="itemnum">
                    <div style="width:61px;">
                        <a href="javascript:void(0);">Item #</a>
                    </div>
                </td>
                <td class="<?= ($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> text-1  head-center"
                    id="itemname">
                    <div style="width:873px;">
                        <a href="javascript:void(0);">Name</a>
                    </div>
                </td>
            </tr>
        </table>
        <div class="tabinfo" id="dbpricetabinfo"></div>
    </div>
</div>