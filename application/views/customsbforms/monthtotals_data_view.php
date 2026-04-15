<?php $numpp = 0; ?>
<?php foreach ($totals as $total): ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$total['month_id']==0 ? 'totalrow' : ''?>">
        <div class="total_month"><?=$total['month']?></div>
        <?php foreach ($years as $year): ?>
            <div class="total_year"><?=empty($total[$total['month_id'].'-'.$year]) ? '-' : $total[$total['month_id'].'-'.$year]?></div>
        <?php endforeach; ?>
        <div class="total_month"><?=$total['month']?></div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
