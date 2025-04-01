<?php $numpp = 0; ?>
<?php foreach ($totals as $total): ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="total_weeknum"><?=$total['week']?></div>
        <div class="total_day"><?=empty($total['mon']) ? '&nbsp;' : $total['mon']?></div>
        <div class="total_day"><?=empty($total['tue']) ? '&nbsp;' : $total['tue']?></div>
        <div class="total_day"><?=empty($total['wed']) ? '&nbsp;' : $total['wed']?></div>
        <div class="total_day"><?=empty($total['thu']) ? '&nbsp;' : $total['thu']?></div>
        <div class="total_day"><?=empty($total['fri']) ? '&nbsp;' : $total['fri']?></div>
        <div class="total_day"><?=empty($total['sat']) ? '&nbsp;' : $total['sat']?></div>
        <div class="total_day"><?=empty($total['sun']) ? '&nbsp;' : $total['sun']?></div>
        <div class="total_totals"><?=empty($total['total']) ? '&nbsp;' : $total['total']?></div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
