<?php foreach ($tasks as $task): ?>
    <div class="questionbox">
        <div class="quests-icon">
            <span class="questionbox-icn" data-leadmail="<?=$task['leademail_id']?>"><i class="fa fa-search" aria-hidden="true"></i></span>
        </div>
        <div class="quests-date"><?=$task['task_date']?> -</div>
        <div class="quests-type"><?=$task['task_type']?></div>
        <div class="quests-link">
            <div class="quests-unlink" data-leadmail="<?=$task['leademail_id']?>" data-leadrel="<?=$task['task_type']?>">unlink</div>
        </div>
    </div>
<?php endforeach; ?>
