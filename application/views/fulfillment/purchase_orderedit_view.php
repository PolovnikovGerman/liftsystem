<div class="purchaseorder_popuparea">
    <div class="purchaseorder-title"><?=$title?></div>
    <!-- Common Data -->
    <div id="orderdataarea"><?=$order_view?></div>
    <div class="purchaseorder-row">
        <div class="poamount_label">Amount Date:</div>
        <div class="poamount-dateinput">
            <input type="text" class="poamount_date" name="amount_date" id="amount_date"  value="<?=date('m/d/y',$amount['amount_date'])?>"/>
        </div>
        <div class="poamount_label">PO Amount:</div>
        <div class="poamount-amntinput">
            <input type="text" class="poamount_sum" id="amount_sum" name="amount_sum" value="<?=$amount['amount_sum']?>"/>
        </div>
        <div class="poamount_label">Incl. Shipping</div>
        <div class="poamount-shippinginput">
            <input type="checkbox" class="poamount_ship" id="po_shipping" name="is_shipping" value="1" <?=($amount['is_shipping']==1 ? 'checked="checked"' : '')?> />
        </div>
    </div>
    <div class="purchaseorder-row">
        <div class="poorder_label">Vendor:</div>
        <div class="poamount-vendorinput">
            <select class="poamount_vendor" id="vendor_id" name="vendor_id">
                <option value="">Vendor...</option>
                <?php foreach ($vendors as $vrow) { ?>
                    <option value="<?= $vrow['vendor_id'] ?>" <?= ($vrow['vendor_id'] == $amount['vendor_id'] ? 'selected="selected"' : '') ?> ><?= $vrow['vendor_name'] ?></option>
                <?php } ?>
            </select>
        </div>
        <div class="poorder_label">Method:</div>
        <div class="poamount-vendorinput">
            <select class="poamount_vendor" id="method_id" name="method_id">
                <option value="">Method...</option>
                <?php foreach ($methods as $mrow) { ?>
                    <option value="<?= $mrow['method_id'] ?>" <?= ($mrow['method_id'] == $amount['method_id'] ? 'selected="selected"' : '') ?>><?= $mrow['method_name'] ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div id="editporeasonarea"><?=$editpo_view?></div>
    <div id="lowprofitpercreasonarea"><?=$lowprofit_view?></div>
    <div class="poamount_actions">
        <div class="poamount-save">
            <img src="/img/fulfillment/saveticket.png" alt="Save"/>
        </div>
    </div>
</div>
