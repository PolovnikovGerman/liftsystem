<div class="shipping-info-form">
    <div class="weight">
        <div class="weight-txt">Weight Each:</div>
        <div class="weight-input view"><?= $item_weigth ?></div>
        <div class="weight-measure">lbs</div>
    </div>
    <div class="cartoon-qty">
        <div class="cartoon-qty-txt">Carton Qty:</div>
        <div class="cartoon-qty-input view"><?= $cartoon_qty ?></div>
    </div>
    <div class="cartoon-dimension">
        <div class="cartoon-dimension-txt">Carton Dim:</div>
        <div class="cartoon-dimension-input view"><?= $cartoon_width ?></div>
        <div class="cartoon-dimension-txttile">W</div>
        <div class="cartoon-dimension-input view"><?= $cartoon_heigh ?></div>
        <div class="cartoon-dimension-txttile">H</div>
        <div class="cartoon-dimension-input view"><?= $cartoon_depth ?></div>
        <div class="cartoon-dimension-txttile">D</div>
        <div class="cartoon-dimension-measure">(in inches)</div>
    </div>
    <div class="special-charge-title">Special Shipping Charges for this item:</div>
    <div class="special-charge-area">
        <div class="special-charge-row">
            <div class="special-charge-input view"><?=$charge_pereach ?></div><div class="special-charge-measure">Per Each Piece</div>
        </div>
        <div class="special-charge-row">
            <div class="special-charge-input view"><?= $charge_perorder ?></div><div class="special-charge-measure">Per Order</div>
        </div>
    </div>
    <div class="special-order-restict-area">
        <div class="special-order-restict-title">Special Ordering Restrictions:</div>
        <div class="special-order-restict-value view"><?=$boxqty?></div>
    </div>
</div>

