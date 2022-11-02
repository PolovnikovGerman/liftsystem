<input type='hidden' id='totalcoupon' value="<?= $total_rec ?>"/>
<input type="hidden" id='ordercoupon' value="<?= $order_by ?>"/>
<input type="hidden" id="directcoupon" value="<?= $direction ?>"/>
<input type="hidden" id="curcoupon" value="<?= $cur_page ?>"/>
<input type="hidden" id="perpagecoupon" value="<?=$perpage?>"/>
<input type="hidden" id="couponmanagebrand" value="<?=$brand?>"/>
<div class="coupons_content">
    <div class="coupons_header">
        <div class="addnewcoupon">&nbsp;</div>
        <div class="coupons_paginator" id="couponPaginator"></div>
    </div>
    <div class="coupondata_header">
        <div class="activedoc">Active</div>
        <div class="percentoff">% Off</div>
        <div class="text">or</div>
        <div class="moneyoff">$$ Off</div>
        <div class="minrevenue">Min Limit</div>
        <div class="maxrevenue">Max Limit</div>
        <div class="coupon_description">Description</div>
        <div class="coupon_code">Code</div>
        <div class="coupon_manage">Edit / Delete</div>
    </div>
    <div class="coupondata_content">&nbsp;</div>
</div>
