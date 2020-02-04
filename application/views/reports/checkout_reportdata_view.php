<table class="table-Orders-text3" cellspacing="0" cellpadding="0">
    <?php $nrow=0;?>
    <?php foreach ($tabledat as $row) {?>
        <tr class="<?=($nrow%2==0 ? 'line-reports' : 'grey-line-reports')?>">
            <td width="101" class="Table-bor-5"><?=$row['date']?></td>
            <td width="111" class="Table-bor-6">
                <div class="daynumber">
                    <b><?=$row['mon_day']?></b>
                </div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['mon_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['mon_sum']?></div>
                </div>
            </td>
            <td width="111" class="Table-bor-6">
                <div class="daynumber">
                    <b><?=$row['tue_day']?></b>
                </div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['tue_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['tue_sum']?></div>
                </div>
            </td>
            <td width="111" class="Table-bor-6">
                <div class="daynumber">
                    <b><?=$row['wed_day']?></b>
                </div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['wed_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['wed_sum']?></div>
                </div>
            </td>
            <td width="111" class="Table-bor-6">
                <div class="daynumber">
                    <b><?=$row['thu_day']?></b>
                </div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['thu_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['thu_sum']?></div>
                </div>
            </td>
            <td width="111" class="Table-bor-6 ">
                <div class="daynumber"><b><?=$row['fri_day']?></b></div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['fri_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['fri_sum']?></div>
                </div>
            </td>
            <td width="111" class="Table-bor-6 weekend">
                <div class="daynumber"><b><?=$row['sat_day']?></b></div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['sat_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['sat_sum']?></div>
                </div>
            </td>
            <td width="111" class="Table-bor-6 weekend">
                <div class="daynumber"><b><?=$row['sun_day']?></b></div>
                <div class="info">
                    <div class="table-Orders-text12"><?=$row['sun_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['sun_sum']?></div>
                </div>
            </td>
            <td width="107" class="Table-bor-7 blue-cell-reports">
                <div class="info" style="padding-top:0px;">
                    <div class="table-Orders-text12"><?=$row['total_cnt']?> orders</div>
                    <div class="table-Orders-text5"><?=$row['total_sum']?></div>
                </div>
            </td>
        </tr>
        <?php $nrow++;?>
    <?php } ?>
</table>
