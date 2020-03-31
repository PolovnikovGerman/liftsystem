<div style="clear: both; float: left; width: 900px;">
    <?=$head?>
    <div style="clear:both; float: left; width: 850px; text-align: center; font-size: 14px;">
        Issue Details
    </div>
    <table style="width: 100%; font-size: 14px; border: 1px solid #000;border-collapse: collapse;">
        <thead>
            <tr style="border: 1px solid #000000;">
                <td style="text-align: center;border: 1px solid #000000; width: 10%;">Date</td>
                <td style="text-align: center;border: 1px solid #000000; width: 10%;">Ticket#</td>
                <td style="text-align: center;border: 1px solid #000000; width: 10%;">Order#</td>
                <td style="text-align: center;border: 1px solid #000000; width: 30%;">Customer Name</td>
                <td style="text-align: center;border: 1px solid #000000; width: 30%;">Vendor Name</td>
                <td style="text-align: center;border: 1px solid #000000; width: 10%;">Last Day Update</td>
            </tr>
        </thead>
        <tbody><?= $data ?></tbody>
    </table>    
</div>