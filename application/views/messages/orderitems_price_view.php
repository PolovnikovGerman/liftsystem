<table cellspacing="2" border="1" cellpadding="5" width="800">
    <thead>
    <tr>
        <th>Order Number</th>
        <th>Item</th>
        <th>Color</th>
        <th>QTY</th>
        <th>Price in Order</th>
        <th>Price by QTY</th>
        <th>Diff</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($items as $item) { ?>
        <tr>
            <td><?=$item['order_num']?></td>
            <td><?=$item['item']?></td>
            <td><?=$item['color']?></td>
            <td><?=$item['qty']?></td>
            <td align="right"><?=MoneyOutput($item['order_price'])?></td>
            <td align="right"><?=MoneyOutput($item['price'])?></td>
            <td align="right"><?=MoneyOutput($item['diff'])?></td>
        </tr>
    <?php } ?>
    </tbody>
</table>