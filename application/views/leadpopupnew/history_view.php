<?php foreach ($lead_history as $history) : ?>
    <div class="leadhistory-box">
        <div class="leadhistory-titlebox"><span class="leadhistory-name"><?=($history['user_name']=='' ? 'Former Employee:' : $history['user_name'])?></span> - <?=date('m/d/Y H:i:s',$history['created_date'])?></div>
        <div class="leadhistory-txtbox"><?=$history['history_message']?></div>
    </div>
<?php endforeach; ?>
