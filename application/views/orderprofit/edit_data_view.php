<form id="orderedit">
    <input type="hidden" id="order_id" name="order_id" value="<?=$order_id?>"/>
    <input type="hidden" id="oldrevenue" value="<?=$revenue?>"/>
    <input type="hidden" id="oldshipping" value="<?=$shipping?>"/>
    <input type="hidden" id="oldtax" value="<?=$tax?>"/>
    <input type="hidden" id="oldcc_fee" value="<?=$cc_fee?>"/>
    <input type="hidden" id="oldorder_cog" value="<?=$order_cog?>"/>    
    <input type="hidden" id="order_items" value="<?=$order_items?>"/>
    <input type="hidden" id="order_itemnum" value="<?=$order_itemnumber?>"/>
    <div class="profitorder_action_data">
        <div class="edt">
            <a class="aprblnk" href="javascript:void(0);"><img src="/img/accept.png"/></a>
        </div>
        <div class="del">
            <a class="cancblnk" href="javascript:void(0);"><img src="/img/cancel.png"/></a>
        </div>
    </div>
    <div class="profitorder_date_data">
        <input type="text" class="orderdate" id="order_date" name="order_date" value="<?=date('m/d/Y',$order_date)?>"/>
    </div>
    <div class="profitorder_numorder_data <?=($order_id==0 ? 'nonactive' :'')?>">
        <?php if (isset($order_num) && $order_num) { ?>
        <?=$order_num?>
        <?php } else { ?>
        &nbsp;
        <?php } ?>
    </div>
    <div class="profitorder_emailcustomer_data">
        <input type="text" id="customer_email" name="customer_email" value="<?=$customer_email?>" class="ordercustomermail"/>
        <input type="text" id="customer_name" name="customer_name" value="<?=$customer_name?>" class="ordercustomer"/>
    </div>
    <div class="profitorder_qty_data">
        <input type="text" id="revenue" name="order_qty" value="<?=$order_qty?>" class="orderdataqty"/>
    </div>
    <div class="profitorder_item_editdata">
        <select id="orderitemval" name="item_id" class="orderitemval">
            <option value="" <?=($item_id=='' ? 'selected="selected"' : '')?>>Enter Item Name</option>
            <?php foreach ($items_list as $row) { ?>
                <option value="<?=$row['item_id']?>" <?=($row['item_id']==$item_id ? 'selected="selected"' : '')?>><?=$row['item_name']?></option>
            <?php } ?>
        </select>        
    </div>
    <div class="profitorder_revenue_dataedit">
        <input type="text" id="revenue" name="revenue" value="<?=$revenue?>" class="orderrevenue"/>
    </div>
    <div class="profitorder_shipping_data">
        <input type="text" id="shipping" name="shipping" value="<?=$shipping?>" class="profitshipping"/>
    </div>
    <div class="profitorder_shipping_calc">
        <input type="checkbox" id="is_shipping" name="is_shipping" value="1" style="margin-top: 6px;" <?=($is_shipping ? 'checked="checked"' : '')?> />
    </div>
    <div class="profitorder_shipdate_data">
        <input type="text" id="shipping_date" name="shipdate" value="<?=($shipdate==0 ? '' : date('m/d/y', $shipdate))?>" class="profitshipdate" readonly="readonly"/>
    </div>
    <div class="profitorder_tax_data">
        <input type="text" id="tax" name="tax" value="<?=$tax?>" class="taxval"/>
    </div>
    <div class="profitorder_othercost_data">
        <input type="checkbox" id="cc_fee" name="cc_fee" value="1" style="margin-top: 3px;" <?=($cc_fee==0 ? '' : 'checked="checked"')?> title="<?='$'.number_format($cc_fee,2,'.',',')?>" />
    </div>
    <div class="profitorder_cog_data" style="width: 69px;">
        <input type="text" id="order_cog" name="order_cog" readonly="readonly" value="<?=$order_cog?>" class="cogval moneydat"/>
    </div>
    <div class="profitorder_profit_data" style="width: 63px;">
        <input type="text" id="profit" name="profit" value="<?=$profit?>" readonly="readonly" class="profitval moneydat"/>
    </div>
    <div class="profitorder_profitperc_data">
        <input type="text" id="profit_perc" name="profit_perc" value="<?=$profit_perc?>" readonly="readonly" class="profitperc moneydat"/>
    </div>
</form>