<div class="contant-popup">
    <div class="section-customer <?=$blocked==0 ? $brandclass : 'blockedcustomer';?>">
        <?=$customer_view?>
    </div>
    <div class="section-content dualblocks <?=$blocked==0 ? $brandclass : 'blockedcustomer';?>">
        <div class="orderblock">
            <?=$order_view?>
        </div>
        <div class="orderblock">
            <?=$order_view?>
        </div>
    </div>
</div>
