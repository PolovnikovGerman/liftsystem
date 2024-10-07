<table cellspacing="2" border="1" cellpadding="5" width="900">
    <thead>
    <tr>
        <th align="center">Order #</th>
        <th align="center">Customer</th>
        <th align="center">Last Update</th>
        <th align="center">Item</th>
        <th align="center">Color</th>
        <th align="center">QTY</th>
        <th align="center">Order Price</th>
        <th align="center">Price by QTY</th>
        <th align="center">Diff</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item) { ?>
        <tr>
            <td><?=$item['order']?></td>
            <td><?=$item['customer']?></td>
            <td><?=$item['user']?></td>
            <td><?=$item['item']?></td>
            <td><?=$item['color']?></td>
            <td><?=$item['qty']?></td>
            <td align="right"><?=MoneyOutput($item['order_price'],3)?></td>
            <td align="right"><?=MoneyOutput($item['price'],3)?></td>
            <td align="right"><?=MoneyOutput($item['diff'],2)?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>