<html>
<head>
    <style type="text/css">
        body {
            font-family: Verdana, sans-serif;
            font-size: 0.8em;
            color:#484848;
        }
    </style>
</head>
<body>
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
    <?php if (count($data)==0) : ?>
        <tr><td colspan="11">All PO orders '.count($orderlists).' math is OK</td>
    <?php else : ?>
        <?php foreach ($data as $row) : ?>
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
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
