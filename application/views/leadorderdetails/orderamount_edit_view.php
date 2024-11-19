
    <input type="hidden" id="amntsession" value="<?=$session?>"/>
    <div class="saveamount"><i class="fa fa-check-circle"></i></div>
    <div class="cancelamount"><i class="fa fa-times-circle"></i></div>
    <div class="qtyamnt">
        <input type="text" class="orderamntqtyinpt" value="<?=$amount['shipped']?>" placeholder="Qty"/>
    </div>
    <div class="priceamnt">
        <input type="text" class="orderamntpriceinpt" value="<?=$amount['shipped_price']?>" placeholder="Price"/>
    </div>
    <div class="dateamnt">
        <input type="text" class="orderamntdateinpt" id="podateinpt" value="<?=date('m/d/Y', $amount['amount_date'])?>" placeholder="Date"/>
    </div>
    <div class="typeamnt"><?=$amount['type']?></div>
    <div class="vendoramnt">
        <select class="orderamntvendorinpt">
            <option value=""></option>
            <?php foreach ($vendors as $vendor) : ?>
                <option value="<?=$vendor['vendor_id']?>" <?=$amount['vendor_id']==$vendor['vendor_id'] ? 'selected="selected"' : ''?>><?=$vendor['vendor_name']?></option>
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
</div>