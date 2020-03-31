<tr style="border: 1px solid #000000;">
    <td style="text-align: center; border: 1px solid #000000;font-weight: bold" colspan="6">
        <?=$year?>, <?=date('j-S', $titledate)?> Quarter
    </td>
</tr>
<?php foreach ($data as $row) { ?>
    <tr style="border: 1px solid #000000;">
        <td style="text-align: center; border: 1px solid #000000;"><?= date('m/d/Y', $row['ticket_date']) ?></td>
        <td style="text-align: center; border: 1px solid #000000;"><?= $row['ticket_num'] ?></td>
        <td style="text-align: center; border: 1px solid #000000;"><?= $row['order_num'] ?></td>
        <td style="text-align: left; border: 1px solid #000000;"><?= $row['customer'] ?></td>
        <td style="text-align: left; border: 1px solid #000000;"><?= $row['vendor_name'] ?></td>
        <td style="text-align: center; border: 1px solid #000000;"><?= date('m/d/Y', $row['lastupdate']) ?></td>
    </tr>
<?php } ?>
