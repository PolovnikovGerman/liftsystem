<input type="hidden" id="batchcalmaxdate" value=""/>
<input type="hidden" id="batchcalmindate" value=""/>
<div class="batchcalendar_title">
    <div class="batchcalendar_daytitle">Mon</div>
    <div class="batchcalendar_daytitle">Tue</div>
    <div class="batchcalendar_daytitle">Wed</div>
    <div class="batchcalendar_daytitle">Thu</div>
    <div class="batchcalendar_daytitle">Fri</div>
    <div class="batchcalendar_daytitle">Sat</div>
    <div class="batchcalendar_daytitle">Sun</div>
</div>
<div id="calendar_date" class="batchcalendar_view">
    <?php $weekday=0; $numpp=0;?>
    <div class="batchcalendar_data">
    <?php foreach ($data as $row) {?>
        <div class="batchcalend_dateinfo <?=$row['out_daytype']?> <?=($curdate==$row['batch_date'] ? 'curday' : '')?>" id="batch<?=$row['batch_date']?>" href="/finance/batchdue/?day=<?=$row['batch_date']?>">
            <div class="batchcalendar_daytitle"><?=$row['out_date']?></div>
            <div class="batchcalendar_sums <?=$row['out_vmdclass']?>"><?=$row['out_vmd']?></div>
            <div class="batchcalendar_sums <?=$row['out_amexclass']?>"><?=$row['out_amex']?></div>
            <div class="batchcalendar_sums <?=$row['out_otherclass']?>"><?=$row['out_other']?></div>
            <div class="batchcalendar_sums <?=$row['out_termclass']?>"><?=$row['out_term']?></div>
            <div class="batchcalendar_sums <?=$row['out_writeofflass']?>"><?=$row['out_writeoff']?></div>
        </div>
        <?php $numpp++;?>
        <?php $weekday++?>
        <?php if ($weekday==7 && $numpp<$cnt) {?>
        </div>
    <div class="batchcalendar_data">
        <?php $weekday=0;?>
        <?php } ?>
    <?php }?>    
    </div>
</div>
<div class="batchcalend_totals">
    <span class="batchcalendar_totals_title">Pending CC</span>
    <span class="batchcalendar_totals_sums pendcc <?=$totals['pendcc_class']?>"><?=$totals['out_pendcc']?></span>
    <span class="batchcalendar_totals_title">Open Terms</span>
    <span class="batchcalendar_totals_sums openterm <?=$totals['term_class']?>"><?=$totals['out_term']?></span>
    <span class="batchcalendar_totals_title">Past Due</span>
    <span class="batchcalendar_totals_sums pastdue <?=$totals['pastdue_class']?>"><?=$totals['out_pastdue']?></span>
</div>

