<div class="orddtls_notes">
    <div class="orddtls_notestitle">General Notes:</div>
    <div class="orddtls_notesbox">
        <textarea class="inputleadorddatas" name="general_notes" <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="message" data-field="general_notes"')?>><?=$general_notes?></textarea>
    </div>
</div>
<div class="orddtls_update">
    <div class="orddtls_updatetitle">Update:</div>
    <div class="orddtls_updatebox">
        <?php if ($edit==0) : ?>
        &nbsp;
        <?php else : ?>
            <textarea name="name2" class="inputleadorddata" data-entity="message" data-field="update"></textarea>
            <div class="btn_update">update</div>
        <?php endif; ?>
    </div>
</div>
<div class="orddtls_history">
    <div class="orddtls_historytitle">History:</div>
    <div class="orddtls_historybox" id="orddtls_historybox">
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
    </div>
</div>

