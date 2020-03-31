<div style="clear:both; float: left; width: 850px; font-weight: bold; color: #0000FF; text-align: center; font-size: 16px; margin-bottom: 5px;">
    <?=$title?>
</div>
<div style="clear:both; float: left; width: 850px; margin-bottom: 5px;">
    <table style="border:1px solid #000; font-size: 14px;">
        <tr>
            <td style="width: 80px; font-size: 16px; font-weight: bold; text-align: center;">Order Date</td>
            <td style="width: 80px; font-size: 16px; font-weight: bold; text-align: center;">Last Update</td>
            <td style="width: 80px; font-size: 16px; font-weight: bold; text-align: center;">Order #</td>
            <td style="width: 330px; font-size: 16px; font-weight: bold; text-align: center;">Customer</td>
            <td style="width: 80px; font-size: 16px; font-weight: bold; text-align: center;">QTY</td>
            <td style="width: 300px; font-size: 16px; font-weight: bold; text-align: center">Item</td>
            <td style="width: 100px; font-size: 16px; font-weight: bold; text-align: center;">Revenue</td>
        </tr>
        <?php $i = 0; ?>
        <?php foreach ($orders as $row) { ?>
            <tr style="background-color: <?= ($row['order_rush_val'] == '1' ? '#FF2A00' : ($i % 2 == 0 ? '#F2F2F2' : '#FFFFFF')) ?>;">
                <td style="width: 80px; font-size: 14px; text-align: left; padding-left: 5px; vertical-align: middle;"><?= date('m/d/Y', $row['order_date']) ?></td>
                <td style="width: 80px; font-size: 14px; text-align: left; padding-left: 5px; vertical-align: middle;"><?=$row['diff']?></td>
                <td style="width: 80px; font-size: 14px; text-align: left; padding-left: 5px; vertical-align: middle;"><?= $row['order_num'] ?></td>
                <td style="width: 330px; font-size: 14px; text-align: left; padding-left: 5px; vertical-align: middle;"><?=$row['customer_name']?></td>
                <td style="width: 80px; font-size: 14px; text-align: right; padding-right: 5px; vertical-align: middle;"><?=($row['order_qty']==0 ? '&mdash;' : $row['order_qty'])?></td>
                <td style="width: 300px; font-size: 14px; text-align: left; padding-left: 5px; vertical-align: middle;<?=$row['itemname_class']?>"><?=$row['item_name']?></td>
                <td style="width: 100px; font-size: 14px; text-align: right; padding-right: 5px; vertical-align: middle;"><?= '$' . number_format($row['revenue'], 2, '.', ',') ?></td>
            </tr>
            <?php $i++; ?>
        <?php } ?>
    </table>
</div>