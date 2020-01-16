<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbprofitcontent">
    <input type='hidden' id='totalrecdbprofit' value="<?= $total_rec ?>"/>
    <input type="hidden" id='orderbydbprofit' value="<?= $order_by ?>"/>
    <input type="hidden" id="directiondbprofit" value="<?= $direction ?>"/>
    <input type="hidden" id="curpagedbprofit" value="<?= $cur_page ?>"/>
    <input type="hidden" id="perpagedbprofit" value="<?=$perpage?>"/>
    <div class="clearfix"></div>
    <table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0" class="profit_head">
        <tr style="text-align: center; bgcolor:#ffffff; height:24px">
            <td class="gradient1 bor-1">
                <div style="width:30px;">&nbsp;</div>
            </td>
            <td style="width:28px;font-size: 11px;" class="gradient1 text-1 head-center">
                edit
            </td>
            <td style="width:62px;"
                class="<?= ($order_by == 'item_number' ? 'gradient2' : 'gradient1') ?> text-1 head-center" id="itemnum">
                <a href="javascript:void(0)">Item #</a>
            </td>
            <td style="width:193px"
                class="<?= ($order_by == 'item_name' ? 'gradient2' : 'gradient1') ?> text-1 head-center" id="itemname">
                <a href="javascript:void(0)">Name</a>
            </td>
            <td style="width:48px;"
                class="<?= ($order_by == 'vendor_item_cost' ? 'gradient2' : 'gradient1') ?> text-1 head-center"
                id="vendorcost">
                <a href="javascript:void(0)">Cost</a>
            </td>
            <td style="width:53px;"
                class="<?= ($order_by == 'vendor_name' ? 'gradient2' : 'gradient1') ?> text-1 head-center"
                id="vendorname">
                <a href="javascript:void(0)">Vendor</a>
            </td>
            <td style="width:4px;" class="gradient1">&nbsp;</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">25</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">75</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">150</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">250</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">500</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">1000</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">2500</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">5000</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">10K</td>
            <td style="width:47px;" class="gradient1 text-1 head-center">20K</td>
            <td style="width:4px;" class="gradient1">&nbsp;</td>
            <td style="width:43px;" class="gradient1 text-1 head-center">Prints</td>
            <td style="width:43px;" class="gradient1 text-1 head-center">Setup</td>
            <td style="width:20px" class="gradient1 text-1">&nbsp;</td>
        </tr>
    </table>
    <div class="clearfix"></div>
    <div class="table-profit" id="dbprofittabinfo"></div>
</div>