<div class="artsubmitlog_area">
    <table class="artsubmitlog_data">
        <thead>
        <tr>
            <td>Date</td>
            <td>Operation</td>
            <td>Field</td>
            <td>Value</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($data as $row) { ?>
            <tr>
                <td><?=$row['checkoutlog_date']?></td>
                <td><?=$row['operation']?></td>
                <td><?=$row['param']?></td>
                <td><?=$row['opvalue']?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>