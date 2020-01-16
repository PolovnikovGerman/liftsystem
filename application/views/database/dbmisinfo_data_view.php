<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbmisinfocontent">
    <input type='hidden' id='totalrecmisinfo' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbymisinfo' value="<?= $order_by ?>"/>
    <input type="hidden" id="directionmisinfo" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagemisinfo" value="<?= $cur_page ?>"/>
    <input type="hidden" id="perpagemisinfo" value="<?= $perpage ?>"
    <div class="clearfix"></div>
    <table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0" class="missing_head">
        <tr align="center" bgcolor="#ffffff" height="24">
            <td class="gradient1 bor-1">
                <div style="width:30px;">&nbsp;</div>
            </td>
            <td class="gradient1 text-1 head-center">
                <div style="width:28px;font-size: 11px;">
                    edit
                </div>
            </td>
            <td class="<?= ($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> text-1 head-center" id="itemnum">
                <div style="width:61px;">
                    <a href="javascript:void(0)">Item #</a>
                </div>
            </td>
            <td class="<?= ($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> text-1 head-center" id="itemname">
                <div style="width:199px;">
                    <a href="javascript:void(0)">Name</a>
                </div>
            </td>
            <td class="<?= ($order_by == 'missings' ? 'gradient2' : 'gradient1') ?> text-1 head-center" id="missinfo">
                <div style="width:674px;">
                    <a href="javascript:void(0)">Missing Info</a>
                </div>
            </td>
        </tr>
    </table>
    <div class="clearfix"></div>
    <div class="table-misinfo" id="dbmisinfotabinfo"></div>
</div>