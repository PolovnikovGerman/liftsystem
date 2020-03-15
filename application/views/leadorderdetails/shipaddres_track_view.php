<div class="shiptrackadr">
    <div class="addrlabel">Address <?= $numaddr?></div>
    <div class="addrdataarea">
        <div class="shipadrqty"><?= $item_qty ?> - </div>
        <div class="shipmethod"><?= $shipping_method ?></div>
        <div class="shipaddres">to <?= $zip ?> <?= $address ?></div>
        <div class="delivdate"><?= (empty($arrive_date) ? '&nbsp;' : date('D-M-d')) ?></div>
        <div class="shipadrcost"><?= (empty($shipping_costs) ? '&nbsp;' : MoneyOutput($shipping_costs)) ?></div>
    </div>
</div>    
<div class="shiptrackadrpacks" data-shipaddr="<?= $order_shipaddr_id ?>"><?=$packagesview?></div>    
