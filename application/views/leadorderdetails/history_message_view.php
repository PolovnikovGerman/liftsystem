<?php foreach ($history as $row) { ?>
    <div class="history_day text_white"><?= $row['out_date'] ?></div>
    <div class="history_time"><?= $row['out_subdate'] ?></div>
    <div class="history_text"><?= $row['message'] ?></div>            
<?php } ?>
