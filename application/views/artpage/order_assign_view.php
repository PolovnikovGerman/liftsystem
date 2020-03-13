<div class="orderassign_content">
    <div class="orderassigndata_content">
        <div class="orderassign_row orderdatatitle">
            <div class="orderassign_num">Order #</div>
            <div class="orderassign_date">Date</div>
            <div class="orderassign_customer">Customer</div>
            <div class="orderassign_item">Item</div>
            <div class="orderassign_total">Total</div>
        </div>
        <div class="orderassigndata_info">
            <?php $nrow=0;?>
            <?php foreach ($orders as $row) {?>
                <div class="orderassign_row orderdata <?=($nrow%2==0 ? 'grey' : '')?>" data-orderid="<?=$row['order_id']?>">
                    <div class="orderassign_num" style="float: left;"><?=$row['order_num']?></div>
                    <div class="orderassign_date" style="float: left;"><?=date('m/d/y',$row['order_date'])?></div>
                    <div class="orderassign_customer" style="float: left;"><?=$row['customer_name']?></div>
                    <div class="orderassign_item" style="float: left;"><?=$row['item']?></div>
                    <div class="orderassign_total" style="float: left;"><?=$row['total']?></div>
                </div>
                <?php $nrow++;?>
            <?php } ?>
        </div>
    </div>
</div>