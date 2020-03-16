<div class="labeltxt">Total Missing Orders:</div>
<?php foreach ($totals as $row) { ?>
    <div class="year"><?=$row['year']?></div>
    <div class="yeardata"><?=$row['total']?></div>
<?php } ?>