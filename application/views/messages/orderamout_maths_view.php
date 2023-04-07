<table cellspacing="2" border="1" cellpadding="5" width="600">
    <thead>
    <tr>
        <th>Order ID</th>
        <th>Order Number</th>
        <th>Order COG</th>
        <th>PO Amount</th>
        <th>Diff</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row) { ?>
        <tr>
            <td><?=$row['order_id']?></td>
            <td><?=$row['order_num']?></td>
            <td><?=MoneyOutput($row['order_cog'])?></td>
            <td><?=MoneyOutput($row['amount_cog'])?></td>
            <td><?=MoneyOutput($row['diff'])?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>