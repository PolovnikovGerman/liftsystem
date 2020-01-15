<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbcontent">
    <input type='hidden' id='totalrecdbcateg' value="<?=$total_rec?>"/>
    <input type="hidden" id='orderbydbcateg' value="<?=$order_by?>"/>
    <input type="hidden" id="directiondbcateg" value="<?=$direction?>"/>
    <input type="hidden" id="curpagedbcateg" value="<?=$cur_page?>"/>
    <input type="hidden" id="perpagedbcateg" value="<?=$perpage?>"/>
    <table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0" border="0" class="categories_head">
        <tr class="table_head">
            <td class="gradient1 bor-1 head-center">
                <div  style="width: 32px;">&nbsp;</div>
            </td>
            <td class="gradient1 text-1 head-center">
                <div style="width:25px;font-size: 11px;">edit</div>
            </td>
            <td class="<?=($order_by=='i.item_number' ? "gradient2" : "gradient1")?> text-1 head-center" id="itemnum">
                <div style="width:61px;"><a href="javascript:void(0)">Item #</a></div>
            </td>
            <td class="<?=($order_by=='i.item_name' ? "gradient2" : "gradient1")?> text-1 head-center" id="itemname">
                <div style="width:202px;">
                    <a href="javascript:void(0)">Name</a>
                </div>
            </td>
            <?php if ($order_by=='count_up') {$addclass='gradient2';} else {$addclass='gradient1';}?>
            <td class="<?=$addclass?> head-center"  id="categup">
                <div style="width: 26px;"><a href="javascript:void(0)"><img src="/img/database/upwards.png"/></a></div>
            </td>
            <?php if ($order_by=='count_dwn') {$addclass='gradient2';} else {$addclass='gradient1';}?>
            <td class="<?=$addclass?> head-center" id="categdwn">
                <div style="width: 27px;"><a href="javascript:void(0)"><img src="/img/database/downward.png"/></a></div>
            </td>
            <?php for($i=1;$i<7;$i++) {?>
                <td class="gradient1 text-1 <?=($i==1 ? 'bor-1' : '')?>  head-center">
                    <div style="width: 102px;">
                        Category <?=$i?>
                    </div>
                </td>
            <?php } ?>
            <td class="gradient1 text-1">
                <div style="width:10px;">&nbsp;</div>
            </td>
        </tr>
    </table>
    <div class="table-categories" id="dbcategtabinfo"></div>
</div>