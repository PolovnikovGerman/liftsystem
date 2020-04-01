<table cellspacing="2" border="1" cellpadding="5" width="600">
    <thead>
    <tr>
        <th>Order #</th>
        <th>Calc Revenue</th>
        <th>Revenue</th>
        <th>Diff</th>
        <th>Item Cost</th>
        <th>Imprint Cost</th>
        <th>Shipping</th>
        <th>NJ Tax</th>
        <th>Rush</th>
        <th>Misc Charge</th>
        <th>Discount</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row) { ?>
        <tr>
            <td><?=$row['order_num']?></td>
            <td><?=MoneyOutput($row['calcrevenue'])?></td>
            <td><?=MoneyOutput($row['revenue'])?></td>
            <td><?=MoneyOutput($row['diff'])?></td>
            <td><?=MoneyOutput($row['itemcost'])?></td>
            <td><?=MoneyOutput($row['imprint'])?></td>
            <td><?=MoneyOutput($row['shipping'])?></td>
            <td><?=MoneyOutput($row['tax'])?></td>
            <td><?=MoneyOutput($row['rush'])?></td>
            <td><?=MoneyOutput($row['mischarge'])?></td>
            <td><?=MoneyOutput($row['discount'])?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>