<div class="shipping-info-form">
    <div class="weight">
        <div class="weight-txt">Weight Each:</div>
        <div class="weight-input">
            <input type="text" class="shipinfo-input" name="item_weigth" id="item_weigth" value="<?= $item_weigth ?>" />
        </div>
        <div class="weight-measure">lbs</div>
    </div>
    <div class="cartoon-qty">
        <div class="cartoon-qty-txt">Carton Qty:</div>
        <div class="cartoon-qty-input">
            <input type="text" class="shipinfo-input" name="cartoon_qty" id="cartoon_qty" value="<?= $cartoon_qty ?>"/>
        </div>
    </div>
    <div class="cartoon-dimension">
        <div class="cartoon-dimension-txt">Carton Dim:</div>
        <div class="cartoon-dimension-input">
            <input type="text" class="shipinfo-input" name="cartoon_width" id="cartoon_width" value="<?= $cartoon_width ?>"  />
        </div>
        <div class="cartoon-dimension-txttile">W</div>
        <div class="cartoon-dimension-input">
            <input type="text" class="shipinfo-input" name="cartoon_heigh" id="cartoon_heigh" value="<?= $cartoon_heigh ?>"  />
        </div>
        <div class="cartoon-dimension-txttile">H</div>
        <div class="cartoon-dimension-input">
            <input type="text" class="shipinfo-input" name="cartoon_depth" id="cartoon_depth" value="<?= $cartoon_depth ?>"  />
        </div>
        <div class="cartoon-dimension-txttile">D</div>
        <div class="cartoon-dimension-measure">(in inches)</div>
    </div>
    <div class="special-charge-title">Special Shipping Charges for this item:</div>
    <div class="special-charge-area">
        <div class="special-charge-row">
            <div class="special-charge-input">
                <input type="text" class="shipinfo-input" name="charge_pereach" id="charge_pereach" value="<?= $charge_pereach ?>"  />
            </div>
            <div class="special-charge-measure">Per Each Piece</div>
        </div>
        <div class="special-charge-row">
            <div class="special-charge-input">
                <input type="text" class="shipinfo-input" name="charge_perorder" id="charge_perorder" value="<?= $charge_perorder ?>"  />
            </div>
            <div class="special-charge-measure">Per Order</div>
        </div>
    </div>
    <div class="special-order-restict-area">
        <div class="special-order-restict-title">Special Ordering Restrictions:</div>
        <div class="special-order-restict-value">
            <input type="text" class="shipinfo-input" name="boxqty" id="boxqty" value="<?=$boxqty?>"  />
        </div>
        <div class="saveshipping"><img src="/img/itemdetails/save_order.png"></div>
    </div>
</div>

