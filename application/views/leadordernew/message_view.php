<div class="orddtls_notes">
    <div class="orddtls_notestitle">General Notes:</div>
    <div class="orddtls_notesbox">
        <textarea class="ordercommondata" name="general_notes" <?=($edit==0 ? 'readonly="readonly"' : 'data-entity="message" data-field="general_notes"')?>><?=$general_notes?></textarea>
    </div>
</div>
<div class="orddtls_update">
    <div class="orddtls_updatetitle">Update:</div>
    <div class="orddtls_updatebox">
        <?php if ($edit==0) : ?>
        &nbsp;
        <?php else : ?>
            <textarea name="updatemsg" class="msgorderdata" data-entity="message" data-field="update"></textarea>
            <div class="btn_update">update</div>
        <?php endif; ?>
    </div>
</div>
<div class="orddtls_history">
    <div class="orddtls_historytitle">History:</div>
    <div class="orddtls_historybox" id="orddtls_historybox">
        <?php $this->load->view('leadordernew/update_history_view', array('history'=> $history)); ?>
    </div>
</div>