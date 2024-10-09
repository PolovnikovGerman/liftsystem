<?php $curdate = '';?>
<?php foreach ($data as $datalist) : ?>
    <?php if ($datalist['order_date']!==$curdate && $datalist['order_date']!=='&mdash;') : ?>
            <tr>
                <th colspan="13">
                    <div class="tbl-td td-date"><?=$datalist['order_date']?></div>
                </th>
            </tr>
        <?php $curdate = $datalist['order_date'];?>
    <?php endif; ?>
    <tr>
        <th><div class="tbl-td td-order"><?=$datalist['order_num']?></div></th>
        <th><div class="tbl-td td-customer"><?=$datalist['customer_name']?></div></th>
        <th><div class="tbl-td td-qty"><?=QTYOutput($datalist['order_qty'])?></div></th>
        <th><div class="tbl-td td-item"><?=$datalist['out_item']?></div></th>
        <th><div class="tbl-td td-color"><?=$datalist['itemcolor']?></div></th>
        <th><div class="tbl-td td-conf"><?=$datalist['out_confirm']?></div></th>
        <th><div class="tbl-td td-revenue"><?=$datalist['revenue']?></div></th>
        <th><div class="tbl-td td-balance boldtxt <?=$datalist['balance_class']?>"><?=$datalist['balance']?></div></th>
        <th><div class="tbl-td td-salesrep"><?=$datalist['user_replic']?></div></th>
        <th><div class="tbl-td td-class"><?=$datalist['order_class']?></div></th>
        <th><div class="tbl-td td-artstatus"><?=$datalist['artstage']?></div></th>
        <th><div class="tbl-td td-points <?=$datalist['profit_class']?>"><?=$datalist['points']?></div></th>
        <th><div class="tbl-td td-fulfilled <?=$datalist['order_status_class']?>">
                <div class="fulf-proc"><?=$datalist['order_status_perc']?></div>
                <div class="fulf-status"><?=$datalist['order_status']?></div>
            </div>
        </th>
    </tr>
<?php endforeach; ?>
