<div class="block_6">    
    <div class="block_6_generalnotes">
        <div class="block_6_text">General Notes:</div>
        <textarea class="block_6_textarea1 input_border_gray inputleadorddatas" <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="message" data-field="general_notes"')?>><?=$general_notes?></textarea>
    </div>
    <?php if ($edit==1) { ?>
        <div class="block_6_update">
            <div class="block_6_text">Update:</div>
            <textarea name="name2" class="block_6_textarea2 input_border_gray inputleadorddata" data-entity="message" data-field="update"></textarea>
            <div class="button_update">
                <div class="button_update_text">update</div>
            </div>
        </div>
    <?php } ?>
    <div class="block_6_history">
        <div class="block_6_text">History:</div>
        <div class="block_6_historytext input_border_gray <?=$edit==0 ? 'previewmode' : ''?>">
        <?php foreach ($history as $row) { ?>
            <div class="history_day text_white"><?=$row['out_date']?></div>
            <div class="history_time"><?=$row['out_subdate']?></div>
            <div class="history_text"><?=$row['message']?></div>            
        <?php } ?>
        </div>
    </div>
</div>
