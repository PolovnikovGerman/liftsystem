<?php $numpp = 0; ?>
<?php foreach ($totals as $total): ?>
    <div class="datarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="total_weeknum"><?=$total['week']?></div>
        <div class="total_day"><?=$total['mon']?></div>
        <div class="total_day"><?=$total['tue']?></div>
        <div class="total_day"><?=$total['wed']?></div>
        <div class="total_day"><?=$total['thu']?></div>
        <div class="total_day"><?=$total['fri']?></div>
        <div class="total_day"><?=$total['sat']?></div>
        <div class="total_day"><?=$total['sun']?></div>
        <div class="total_totals"><?=$total['total']?></div>
    </div>
    <?php $numpp++; ?>
<?php endforeach; ?>
