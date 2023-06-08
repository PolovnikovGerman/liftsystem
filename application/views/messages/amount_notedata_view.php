<div style="clear: both; float: left; font-size: 14px; font-weight: bold; text-align: center; width: 100%;"><?=$title;?></div>
<div style="clear: both; float: left; font-size: 12px; font-weight: normal; text-align: center; width: 100%;"><?=$subtitle;?></div>
<div style="clear: both; float: left; font-size: 12px; font-weight: normal; text-align: center; width: 100%;">
<table style="width: 100%; font-size: 14px; border: 1px solid #000;border-collapse: collapse;">
    <thead>
        <tr style="border: 1px solid #000000;">
            <td style="text-align: center;border: 1px solid #000000;">Profit, $</td>
            <td style="text-align: center;border: 1px solid #000000;">Profit, %</td>
            <td style="text-align: center;border: 1px solid #000000;">PO #</td>
            <td style="text-align: center;border: 1px solid #000000;">PO Amnt</td>
            <td style="text-align: center;border: 1px solid #000000;">Vendor</td>
            <td style="text-align: center;border: 1px solid #000000;">Items</td>
            <?php if ($type=='edit') {?>
                <td style="width: 20%; text-align: center;border: 1px solid #000000;">Reason</td>
            <?php } ?>
            <td style="width: 30%; text-align: center; border: 1px solid #000000">Low Profit Reason</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($lists as $row) { ?>
        <tr>
            <td <?=$row['rstyle']?>><?=$row['out_profit']?></td>
            <td <?=$row['rstyle']?>><?=$row['profit_perc']?>%</td>
            <td style="text-align: center;border: 1px solid #000000;"><?=$row['order_num']?></td>
            <td style="text-align: right;border: 1px solid #000000;"><?=$row['out_amount']?></td>
            <td style="text-align: center;border: 1px solid #000000;"><?=$row['vendor']?></td>
            <td style="text-align: left;border: 1px solid #000000;"><?=$row['items']?></td>
            <?php if ($type=='edit') {?>
                <td style="text-align: left;border: 1px solid #000000;"><?=$row['reason']?></td>
            <?php } ?>
            <td style="text-align: left;border: 1px solid #000000;"><?=$row['lowprofit']?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>
</div>
<br/>
<div style="clear: both; float: left; width: 100%; height: 15px;">&nbsp;</div>