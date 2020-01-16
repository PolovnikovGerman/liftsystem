<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbtemplatecontent">
    <input type='hidden' id='totalrecdbtempl' value="<?=$total_rec?>"/>
    <input type="hidden" id='orderbydbtempl' value="<?=$order_by?>"/>
    <input type="hidden" id="directiondbtempl" value="<?=$direction?>"/>
    <input type="hidden" id="curpagedbtempl" value="<?=$cur_page?>"/>
    <input type="hidden" id="perpagedbtempl" value="<?=$perpage?>"/>
    <div class="clearfix"></div>
    <div class="blank_templateloader">
        <div class="arrow">
            <img src="/img/database/right_arrow.png" alt="right arrow"/>
        </div>
        <div class="blank_templatelink">
            <a href="/uploads/aitemp/proof_BT15000_customer_item.ai" target="_blank">Click here to open the Blank Template</a>
        </div>
        <div class="arrow">
            <img src="/img/database/left_arrow.png" alt="right arrow"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <table bordercolor="#bdbdbd" cellspacing="0" cellpadding="0" class="template_head">
        <tr align="center" bgcolor="#ffffff" height="24">
            <td style="width:30px" class="gradient1 bor-1 cell-center"> </td>
            <td style="width:25px;font-size:11px;" class="gradient1 text-1 cell-center">edit</td>
            <td style="width: 61px;" class="<?=($order_by=='item_number' ? 'gradient2' : 'gradient1')?> text-1 cell-center" id="itemnum">
                <a href="javascript:void(0)">Item #</a>
            </td>
            <td style="width:199px" class="<?=($order_by=='item_name' ? 'gradient2' : 'gradient1')?> text-1 cell-center" id="itemname">
                <a href="javascript:void(0)">Name</a>
            </td>
            <td style="width:166px" class="gradient1 text-1 cell-center">&nbsp;</td>
            <td style="width: 140px;"  class="gradient1 text-1 cell-center">Template Updated</td>
            <td style="width: 361px;"  class="gradient1 text-1 cell-center">Imprint Areas &amp; Cust View Updated</td>
        </tr>
    </table>
    <div class="clearfix"></div>
    <div class="table-templates" id="dbtempltabinfo"></div>
</div>
