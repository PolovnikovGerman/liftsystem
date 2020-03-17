<?php foreach ($details as $row) {?>
<div class="batchdaydata" id="batchday<?=$row['batch_date']?>">
    <?=$row['content']?>
</div>
<?php } ?>
