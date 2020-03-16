<div class="coupon_details">
    <input id="coupon_id" type="hidden" value="<?=$data['coupon_id']?>"/>
    <div class="datarow">
        <div class="coupon_active">
            <label>Active</label>
            <input type="checkbox" id="coupon_ispublic" <?=$data['coupon_ispublic']==1 ? 'checked' : ''?>>
        </div>
        <div class="coupon-system">
            <label>System</label>
            <select class="couponsystem" id="coupon_brand">
                <?php foreach ($brands as $row) { ?>
                    <option value="<?=$row['brand']?>" <?=$row['brand']==$data['brand'] ? 'selected' : ''?>><?=$row['label']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="coupon_paramsoff">
            <label>%% Off</label>
            <input type="text" id="coupon_discount_perc" class="coupon_paramsoffvalue" value="<?=$data['coupon_discount_perc']?>"/>
        </div>
        <div class="coupon_paramsofftext">
            or
        </div>
        <div class="coupon_paramsoff">
            <label>$$ Off</label>
            <input type="text" id="coupon_discount_sum" class="coupon_paramsoffvalue" value="<?=$data['coupon_discount_sum']?>"/>
        </div>
    </div>
    <div class="datarow">
        <div class="coupon_revenue">
            <label>Min Limit</label>
            <input type="text" id="coupon_minlimit" class="coupon_revenuevalue" value="<?=$data['coupon_minlimit']?>"/>
        </div>
        <div class="coupon_revenue">
            <label>Max Limit</label>
            <input type="text" id="coupon_maxlimit" class="coupon_revenuevalue" value="<?=$data['coupon_maxlimit']?>"/>
        </div>
        <div class="coupon_code">
            <label>Code</label>
            <div style="float: left; display: inline-block; width: 114px;">
                <input type="text" id="coupon_code1" class="coupon_codevalue" value="<?=$data['coupon_code1']?>"/>
                <span>&mdash;</span>
                <input type="text" id="coupon_code2" class="coupon_codevalue" value="<?=$data['coupon_code2']?>"/>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="coupon_description">
            <label>Desctiption</label>
            <input type="text" id="coupon_description" class="coupon_descriptionvalue" value="<?=$data['coupon_description']?>"/>
        </div>
    </div>
    <div class="datarow">
        <div class="coupon_save">
            <button id="coupondetailsave" class="btn btn-success">Save</button>
        </div>
    </div>
</div>