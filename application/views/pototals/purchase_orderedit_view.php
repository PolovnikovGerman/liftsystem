<div class="purchaseorder_popuparea">
    <!-- Common Data -->
    <div id="orderdataarea"><?=$order_view?></div>
    <div class="datarow">
        <div class="poeditlabel">PO Date:</div>
        <div class="poeditinput">
            <input type="text" id="podateinpt" class="poamntdateinpt" readonly="readonly" value="<?=date('m/d/Y', $amount['amount_date'])?>"/>
        </div>
        <div class="poeditleftpart">
            <div class="orderitemnameplace"><?=$order['order_qty']?>&nbsp;<?=$order['order_items']?></div>
        </div>
    </div>
    <div class="datarow">
        <div class="poeditlabel">PO Amnt:</div>
        <div class="poeditinput">
            <input type="text" class="amountvalueinpt" value="<?=$amount['amount_sum']?>" placeholder="PO Amnt">
        </div>
        <div class="poeditleftpart">
            <div class="amountprofitval <?=$order['profit_class']?>"><?=empty($order['profit']) ? '' : MoneyOutput($order['profit']) ?></div>
        </div>
    </div>
    <div class="datarow">
        <div class="poeditlabel">&nbsp;</div>
        <div class="poeditinput">
            <span>incl. shipping</span>
            <input type="checkbox" class="po_shipping" <?=$order['is_shipping']==1 ? 'checked' : ''?>/>
        </div>
        <div class="poeditleftpart">
            <div class="amountprofitprc <?=$order['profit_class']?>"><?=empty($order['profit_perc']) ? '&nbsp;' : $order['profit_perc'].'%'?></div>
        </div>
    </div>
    <div class="datarow">
        <div class="poeditlabel">Vendor:</div>
        <div class="poeditinput">
            <select class="amountvendorselect">
                <option value=""></option>
                <?php foreach ($vendors as $vendor) { ?>
                    <option value="<?=$vendor['vendor_id']?>" <?=$vendor['vendor_id']==$amount['vendor_id'] ? 'selected="selected"': ''?>>
                        <?=$vendor['vendor_name']?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="poeditleftpart">&nbsp;</div>
    </div>
    <div class="datarow">
        <div class="col-5 poeditlabel pl-0 pr-0">Method:</div>
        <div class="col-7">
            <select class="amountmethodselect">
                <option value=""></option>
                <?php foreach ($methods as $method) { ?>
                    <option value="<?=$method['method_id']?>" <?=$method['method_id']==$amount['method_id'] ? 'selected="selected"' : ''?>>
                        <?=$method['method_name']?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <div class="col-6 leftpart">&nbsp;</div>
    </div>
    <div class="datarow showreason" id="lowprofitpercreasonarea"><?=$lowprofit_view?></div>
    <?php if (!empty($editpo_view)) { ?>
        <div class="datarow showreason"><?=$editpo_view?></div>
    <?php } ?>
    <div class="datarow">
        <div class="poamount-save">
            <img src="/img/fulfillment/saveticket.png">
        </div>
    </div>
</div>
