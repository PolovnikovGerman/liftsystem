<?php if (count($orders_dat) == 0) { ?>
    <div class="online-data-row emptyvalue">
        No orders
    </div>
<?php } else { ?>
    <?php $n_row = 1 ?>
    <?php foreach ($orders_dat as $row) { ?>
        <div class="online-data-row <?= $n_row % 2 == 0 ? 'greydatarow' : 'whitedatarow' ?>">
            <div class="order_numpp <?= $row['order_status_class'] ?>"><?= $row['order_id'] ?></div>
            <div class="status <?= $row['order_status_class'] ?>"><?= $row['order_out_status'] ?></div>
            <div class="order_number <?= $row['order_status_class'] ?>"><?= $row['order_num'] ?></div>
            <div class="order_replica <?= $row['order_status_class'] ?>"><?= $row['order_rep'] ?></div>
            <div class="order_date <?= $row['order_status_class'] ?>"><?= $row['order_date_show'] ?></div>
            <div class="order_confirm <?= $row['order_status_class'] ?>" data-order="<?=$row['order_id']?>"><?= $row['order_confirmation'] ?></div>
            <div class="customer_name <?= $row['order_status_class'] ?>"><?= $row['customer_name'] ?></div>
            <div class="customer_company <?= $row['order_status_class'] ?>"><?= $row['customer_company'] ?></div>
            <div class="order_item <?= $row['order_status_class'] ?>"><?= $row['item_name'] ?></div>
            <div class="order_amount <?= $row['order_status_class'] ?>"><?= ($row['order_amount']) ?></div>
            <div class="order_export <?= $row['order_status_class'] ?>">
                <input type="checkbox" name="option1" value="a1">
            </div>
        </div>
        <?php $n_row++; ?>
    <?php } ?>
<?php } ?>
