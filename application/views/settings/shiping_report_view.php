<?php $numpp=1;?>
<?php $weekday=1;?>
<div class='shiprepweekdata'>
    <?php foreach ($days as $row) {?>
    <div class='shiprepdaycell <?=$row['active']?> <?=$row['type']?>' id='<?=$row['id']?>' <?=$row['href']?>>
        <div class='shiprepdaycelltitle'><?=$row['title']?></div>
        <div class='shiprepdaycellcontent'><?=$row['count']?></div>
    </div>
    <?php $numpp++;?>
    <?php $weekday++;?>
    <?php if ($weekday>7 && $numpp<$totaldays) { ?>
</div>
<div class='shiprepweekdata'>
    <?php $weekday=1;?>
    <?php } ?>
    <?php } ?>
</div>