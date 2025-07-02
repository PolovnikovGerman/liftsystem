<table style="width: 580px; font-size: 14px; border-collapse: collapse;">
    <tr>
        <td colspan="6" style="text-align: center; font-weight: 600">Art Proofs Uploaded to Lift from <?=date('m/d/Y', $datebgn)?> to <?=date('m/d/Y', $dateend)?></td>
    </tr>
    <tr>
        <td rowspan="2" style="text-align: center">Date</td>
        <td colspan="2" style="text-align: center">SB</td>
        <td colspan="2" style="text-align: center">SR</td>
        <td rowspan="2"  style="text-align: center">Total</td>
    </tr>
    <tr>
        <td style="text-align: center">Orders</td>
        <td style="text-align: center">Leads</td>
        <td style="text-align: center">Orders</td>
        <td style="text-align: center">Leads</td>
    </tr>
    <?php foreach ($lists as $list) : ?>
    <tr>
        <td><?=$list['report_date']?></td>
        <td style="text-align: right"><?=$list['sb_orders']?></td>
        <td style="text-align: right"><?=$list['sb_proofs']?></td>
        <td style="text-align: right"><?=$list['sr_orders']?></td>
        <td style="text-align: right"><?=$list['sr_proofs']?></td>
        <td style="text-align: right; font-weight: 600"><?=$list['total']?></td>
    </tr>
    <?php endforeach; ?>
</table>