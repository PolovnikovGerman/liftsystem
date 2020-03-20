<div class="shipdetailarea">
    <table>
        <thead>
        <tr>
            <td>Time</td>
            <td>Item</td>
            <td>QTY</td>
            <td>Country</td>
            <td>Zip</td>
            <td>IP Address</td>
            <td>User Location</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row) {?>
            <tr>
                <td><?=$row['time']?></td>
                <td><?=$row['item']?></td>
                <td><?=$row['qty']?></td>
                <td><?=$row['country']?></td>
                <td><?=$row['zip']?></td>
                <td><?=$row['user_ip']?></td>
                <td><?=$row['location']?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>