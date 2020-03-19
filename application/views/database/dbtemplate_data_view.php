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
        <div class="template_arrow">
            <img src="/img/database/right_arrow.png" alt="right arrow"/>
        </div>
        <div class="blank_templatelink">
            <a href="/uploads/aitemp/proof_BT15000_customer_item.ai" target="_blank">Click here to open the Blank Template</a>
        </div>
        <div class="template_arrow">
            <img src="/img/database/left_arrow.png" alt="right arrow"/>
        </div>
    </div>
    <div class="clearfix"></div>
    <div class="template_head">
        <div class="gradient1 numinlist">&nbsp;</div>
        <div class="gradient1 editcoll">edit</div>
        <div class="itemnum <?=($order_by=='item_number' ? 'gradient2' : 'gradient1')?> sortcell" data-sortcell="item_number">Item #</div>
        <div class="itemname <?=($order_by=='item_name' ? 'gradient2' : 'gradient1')?> sortcell" data-sortcell="item_name">Name</div>
        <div class="gradient1 templsource">&nbsp;</div>
        <div class="gradient1 updatetemplate">Template Updated</div>
        <div class="gradient1 imprintareas">Imprint Areas &amp; Cust View Updated</div>
    </div>
    <div class="clearfix"></div>
    <div class="table-templates" id="dbtempltabinfo"></div>
</div>
