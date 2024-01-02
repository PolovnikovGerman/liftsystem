<div style="clear: both; float: left; font-size: 14px; font-weight: bold; text-align: center; width: 100%;"><?=$title;?></div>
<div style="clear: both; float: left; font-size: 12px; font-weight: normal; text-align: center; width: 100%;"><?=$subtitle;?></div>
<div style="clear: both; float: left; font-size: 12px; font-weight: normal; text-align: center; width: 100%;">
    <table style="width: 100%; font-size: 14px; border: 1px solid #000;border-collapse: collapse;">
        <thead>
        <tr style="border: 1px solid #000000;">
            <td style="text-align: center;border: 1px solid #000000;">Order #</td>
            <td style="text-align: center;border: 1px solid #000000;">Date</td>
            <td style="text-align: center;border: 1px solid #000000;">Customer</td>
            <td style="text-align: center;border: 1px solid #000000;">Items</td>
            <td style="text-align: center;border: 1px solid #000000;">Revenue</td>
            <td style="width: 20%; text-align: center;border: 1px solid #000000;">Amount</td>
            <td style="width: 30%; text-align: center; border: 1px solid #000000">Order Balance</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lists as $row) { ?>
            <tr>
                <td style="text-align: center;border: 1px solid #000000;"><?=$row['order_num']?></td>
                <td style="text-align: center;border: 1px solid #000000;"><?=date('m/d/Y',$row['order_date']?></td>
                <td style="text-align: left;border: 1px solid #000000;"><?=$row['customer_name']?></td>
                <td style="text-align: left;border: 1px solid #000000;"><?=$row['items']?></td>
                <td style="text-align: right;border: 1px solid #000000;"><?=MoneyOutput($row['revenue'])?></td>
                <td style="text-align: right;border: 1px solid #000000;"><?=MoneyOutput($row['amount'])?></td>
                <td style="text-align: right; border: 1px solid #000000"><?=MoneyOutput($row['balance'])?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>
<br/>
<div style="clear: both; float: left; width: 100%; height: 15px;">&nbsp;</div>