<div class="pr-instructions">
    <div class="pr-blocktitle">Instructions:</div>
    <div class="pr-blockbox">
        <textarea class="proofreqcommon" data-fld="customer_instruct"><?=$customer_instruct?></textarea>
    </div>
    <?php // echo ?>
</div>
<div class="pr-update">
    <div class="pr-blocktitle">Update: <div class="viewhistory <?=count($art_history) > 0 ? 'active' : ''?>">view history</div></div>
    <div class="pr-blockbox">
        <textarea class="proofreqcommon" data-fld="update_msg"></textarea>
    </div>
</div>
