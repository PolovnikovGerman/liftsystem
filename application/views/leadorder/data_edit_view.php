<form id="orderedit">
    <input type="hidden" name="order_items" id="order_items" value="<?=$order_items?>"/>
    <div class="leadorderform_edit">
        <div class="leadorderform_save"><img src="/img/accept.png"/></div>        
    </div>
    <div class="leadorderform_date">
        <input type="text" class="leadorder_dateinpt" value="<?=date('m/d/y',$order_date)?>" id="order_date" name="order_date"/>
    </div>
    <div class="leadorderform_ordernum"><?=$order_num?></div>
    <div class="leadorderform_customeremail">
        <input type="text" class="leadorder_customnameinpt" value="<?=$customer_name?>" name="customer_name"/>
        <input type="text" class="leadorder_customemailinpt" value="<?=$customer_email?>" name="customer_email"/>
    </div>
    <div class="leadorderform_orderqty">
        <input type="text" class="leadorder_ordeqtyinpt" value="<?=$order_qty?>" name="order_qty"/>
    </div>
    <div class="leadorderform_item">
        <select class="leadorder_itemnameinpt" id="item_id" name="item_id">
            <option value="">Choose Item</option>
            <?php foreach ($items_list as $row) { ?>
            <option value="<?=$row['item_id']?>" <?=($row['item_id']==$item_id ? 'selected="selected"' : '')?>><?=$row['item_name']?></option>
            <?php } ?>
        </select>
    </div>
    <div class="leadorderform_shipdate">        
        <input type="text" class="leadorder_dateinpt" value="<?=($shipdate==0 ? '' : date('m/d/y',$order_date))?>" id="shipdate" name="shipdate"/>
    </div>
    <div class="leadorderform_revenue">
        <input type="text" class="leadorder_revenueinpt" value="<?=$revenue?>" name="revenue"/>
    </div>
    <div class="leadorderform_shipping">
        <input type="checkbox" id="is_shipping" name="is_shipping" value="1" style="margin-top: 3px;" <?=($is_shipping==0 ? '' : 'checked="checked"')?>/>
        <input type="text" class="leadorder_shipinpt" value="<?=$shipping?>" name="shipping"/>
    </div>
    <div class="leadorderform_salestax">
        <input type="text" class="leadorder_taxinpt" value="<?=$tax?>" name="tax"/>
    </div>
    <div class="leadorderform_ccfee">
        <input type="checkbox" id="cc_fee" name="cc_fee" value="1" style="margin-top: 3px;margin-left: 10px;" <?=($cc_fee==0 ? '' : 'checked="checked"')?> title="<?='$'.number_format($cc_fee,2,'.',',')?>" />
    </div>
    <div class="leadorderform_replica"><?=$replica?></div>
    <div class="leadorderform_cancel"><img src="/img/cancel.png"/></div>
    
</form>