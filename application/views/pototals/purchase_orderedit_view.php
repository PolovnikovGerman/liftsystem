<div class="purchaseorder_popuparea">
    <!-- Common Data -->
    <div id="orderdataarea"><?=$order_view?></div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-5 poeditlabel pl-0 pr-0">PO Date:</div>
                <div class="col-7 poeditinput">
                    <input type="text" id="podateinpt" class="poamntdateinpt" readonly="readonly" value="<?=date('m/d/Y', $amount['amount_date'])?>"/>
                </div>
            </div>
        </div>
        <div class="col-6 leftpart">
            <div class="orderitemnameplace"><?=$order['item_name']?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-5 poeditlabel pl-0 pr-0">PO Amnt:</div>
                <div class="col-7 poeditinput">
                    <input type="text" class="amountvalueinpt" value="<?=$amount['amount_sum']?>" placeholder="PO Amnt">
                </div>
            </div>
        </div>
        <div class="col-6 leftpart">
            <div class="amountprofitval <?=$order['profit_class']?>"><?=empty($order['profit']) ? '' : MoneyOutput($order['profit']) ?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-2 col-md-5 col-sm-5 col-lg-5 pl-0 pr-0">&nbsp;</div>
                <div class="col-10 col-md-7 col-sm-7 col-lg-7">
                    <span>incl. shipping</span>
                    <input type="checkbox" class="po_shipping" <?=$order['is_shipping']==1 ? 'checked' : ''?>/>
                </div>
            </div>
        </div>
        <div class="col-6 leftpart">
            <div class="amountprofitprc <?=$order['profit_class']?>"><?=empty($order['profit_perc']) ? '&nbsp;' : $order['profit_perc'].'%'?></div>
        </div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="row">
                <div class="col-5 poeditlabel pl-0 pr-0">Vendor:</div>
                <div class="col-7">
                    <select class="amountvendorselect">
                        <option value=""></option>
                        <?php foreach ($vendors as $vendor) { ?>
                            <option value="<?=$vendor['vendor_id']?>" <?=$vendor['vendor_id']==$amount['vendor_id'] ? 'selected="selected"': ''?>>
                                <?=$vendor['vendor_name']?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-6 leftpart">&nbsp;</div>
    </div>
    <div class="row">
        <div class="col-6">
            <div class="row">
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
            </div>
        </div>
        <div class="col-6 leftpart">&nbsp;</div>
    </div>
    <div class="row mt-1 showreason">
        <div class="col-8 pl-0 pr-0 poeditlabel">Why is the profit low on this order?</div>
        <div class="col-12">
            <textarea id="po_comment" class="poreasondata" style="width: 100%; resize: none;"><?=$amount['reason']?></textarea>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-12 poamount-save">
            <div class="btn btn-success">Save</div>
        </div>
    </div>
</div>
