<?php foreach ($histories as $history) : ?>
    <div class="history_day text_white"><?=$history['out_date']?></div>
    <div class="history_time"><?=$history['out_subdate']?></div>
    <div class="history_text"><?=$history['message']?></div>
<?php endforeach; ?>
