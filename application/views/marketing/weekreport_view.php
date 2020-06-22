<div style="clear: both; float: left; width: 780px;">
    <div style="clear:both; float: left; width: 780px; text-align: center; font-size: 16px;">
        Weekly Report about Unsuccessful Searches (from <?=date('m/d/Y H:i:s',$start_date)?> to <?=date('m/d/Y H:i:s',$end_date)?>)
    </div>
    <table style="border:1px solid #000;width: 380px; font-size: 16px;">
        <tr>
            <td style="width: 300px; font-size: 16px; font-weight: bold; text-align: center;">Search Template</td>
            <td style="width: 80px; font-size: 16px; font-weight: bold; text-align: center;">Attempts</td>
        </tr>
        <?php $i=0;?>
        <?php foreach ($data as $row) { ?>
            <tr style="background-color: <?=($i%2==0 ? '#F2F2F2' : '#FFFFFF')?>;">
                <td style="width: 300px; font-size: 16px; text-align: left; padding-left: 5px; vertical-align: middle;"><?=$row['search_text']?></td>
                <td style="width: 80px; font-size: 16px; text-align: center; padding-left: 5px; vertical-align: middle;"><?=$row['cnt']?></td>
            </tr>
            <?php $i++;?>
        <?php } ?>
    </table>
</div>