<?php foreach ($history as $row) : ?>
    <div class="historybox_header">
        <span class="historybox_icon"><i class="fa fa-search" aria-hidden="true"></i></span>
        <span><?=$row['out_date']?></span> -
        <span><?=$row['out_subdate']?></span>
    </div>
    <div class="historybox">
        <div class="historybox_text"><?=$row['message']?></div>
    </div>
<?php endforeach; ?>
