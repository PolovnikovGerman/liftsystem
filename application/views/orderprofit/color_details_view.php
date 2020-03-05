<table style="width: 100%">
    <thead>
    <tr>
        <th style="width: 60%; height: 22px; line-height: 22px;">Color</th>
        <th style="height: 22px; line-height: 22px;">QTY</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row) { ?>
    <tr>
        <td style="width: 60%; height: 22px; line-height: 22px;"><?=$row['item_color']?></td>
        <td style="height: 22px; line-height: 22px;"><?=$row['item_qty']?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>