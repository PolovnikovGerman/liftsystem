<div class="contant-popup">
    <div class="prpopup-contant">
        <div class="prpopup-historyarea">
            <div class="list-history">
                <?php foreach ($histories as $history) : ?>
                    <div class="historybox">
                        <div class="historybox-info"><?=$history['history_head']?></div>
                        <div class="historybox-text"><?=$history['message']?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
