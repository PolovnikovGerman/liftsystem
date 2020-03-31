<div style="clear:both; float: left; width: 850px; text-align: center; font-size: 16px;">
    Issues Daily Report - <?= date('m/d/Y H:i:s') ?>
</div>
<div style="clear:both; float: left; width: 850px; text-align: center; font-size: 14px;">
    Overview
</div>
<table style="width: 60%; font-size: 14px; border: 1px solid #000;border-collapse: collapse;">
    <thead>
        <tr style="border: 1px solid #000000;">
            <td style="text-align: center;border: 1px solid #000000; width: 30%;">Year</td>
            <td style="text-align: center;border: 1px solid #000000; width: 30%;">Quarter</td>
            <td style="text-align: center;border: 1px solid #000000; width: 40%;">Issues #</td>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($totals as $row) { ?>
            <tr style="border: 1px solid #000000;">
                <td style="text-align: center;border: 1px solid #000000; "><?= $row['out_tickyear'] ?></td>
                <td style="text-align: center;border: 1px solid #000000; "><?= $row['tickquat'] ?></td>
                <td style="text-align: center;border: 1px solid #000000; "><?= $row['cnt'] ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>    
