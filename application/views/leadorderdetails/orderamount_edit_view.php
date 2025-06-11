<input type="hidden" id="amntsession" value="<?=$session?>"/>
<div class="qtyamnt">
    <input type="text" class="centeralign orderamntqtyinpt" value="<?=$amount['shipped']?>" placeholder="Qty"/>
</div>
<div class="priceamnt">
    <input type="text" class="centeralign orderamntpriceinpt" value="<?=$amount['shipped_price']?>" placeholder="Price"/>
</div>
<div class="dateamnt">
    <input type="text" class="orderamntdateinpt" id="podateinpt" value="<?=date('m/d/Y', $amount['amount_date'])?>" placeholder="Date"/>
</div>
<div class="typeamnt"><?=$amount['type']?></div>
<div class="vendoramnt">
    <select class="orderamntvendorinpt">
        <option value=""></option>
        <?php foreach ($vendors as $vendor) : ?>
            <?php if ($vendor['vendor_id'] < 0) : ?>
                <option value="<?=$vendor['vendor_id']?>" disabled="disabled"><?=$vendor['vendor_name']?></option>
            <?php else: ?>
                <option value="<?=$vendor['vendor_id']?>" <?=$amount['vendor_id']==$vendor['vendor_id'] ? 'selected="selected"' : ''?>><?=$vendor['vendor_name']?></option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</div>
<div class="paymetodamnt">
    <select class="orderamntmethodinpt">
        <option value=""></option>
        <?php foreach ($methods as $method): ?>
            <option value="<?=$method['method_id']?>" <?=$amount['method_id']==$method['method_id'] ? 'selected="selected"' : ''?>><?=$method['method_name']?></option>
        <?php endforeach; ?>
    </select>
</div>
<div class="amountsum">
    <input type="text" class="orderamnttotalinpt" value="<?=$amount['amount_sum']?>"/>
</div>
<div class="includeship">
    <input type="checkbox" class="incudeshipcheck" value=1 <?=$amount['is_shipping']==1 ? 'checked="checked"' : ''?>>
</div>
<div class="saveamount">save</div>
<div class="cancelamount">cancel</div>

