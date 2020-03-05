<table>
    <thead>
    <tr>
        <th>Color</th>
        <th>QTY</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($data as $row) { ?>
    <tr>
        <td><?=$row['item_color']?></td>
        <td><?=$row['item_qty']?></td>
    </tr>
    <?php } ?>
    </tbody>
</table>