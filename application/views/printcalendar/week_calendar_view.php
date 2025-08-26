<input type="hidden" id="printcalendarcurweek" value="<?=$week_num.'-'.$year?>'?>"/>
<?php foreach ($weeks as $week): ?>
    <div class="pscalendar-daybox <?=$week['weekend']==0 ? '' : 'dayoff'?>" data-printdate="<?=$week['date']?>">
        <div class="daybox-date">
            <div class="dayboxdate-month"><?=$week['month']?></div>
            <div class="dayboxdate-date"><?=$week['day']?></div>
        </div>
        <div class="daybox-orders">
            <div class="dayboxorders-name">Orders:</div>
            <div class="dayboxorders-numbers"><?=$week['orders']==0 ? '-' : QTYOutput($week['orders'])?></div>
        </div>
        <div class="daybox-prints">
            <div class="dayboxprints-name">Prints:</div>
            <div class="dayboxprints-numbers"><?=$week['prints']==0 ? '-' : QTYOutput($week['prints'])?></div>
        </div>
    </div>
<?php endforeach; ?>
