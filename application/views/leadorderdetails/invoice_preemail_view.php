<div class="aprovemail_content">
    <div class="approvemaildata_content">
        <div class="approvemail_row">
            <div class="approvemail_label">From:</div>
            <div class="approvemail_data">
                <input type="text" class="aprovemail_input" id="approvemail_from" value="<?=$from?>"/>
            </div>
        </div>
        <div class="approvemail_row">
            <div class="approvemail_label">To:</div>
            <div class="approvemail_data">
                <input type="text" class="aprovemail_input" id="approvemail_to" value="<?=$tomail?>"/>
            </div>
        </div>
        <div class="approvemail_row">
            <div class="addbccapprove" data-applybcc="hidden">add bcc</div>
        </div>
        <div class="approvemail_row" id="emailbccdata" style="display: none">
            <div class="approvemail_label">CC:</div>
            <div class="approvemail_data">
                <input type="text" class="aprovemail_input" id="approvemail_copy" value=""/>
            </div>
        </div>
        <div class="approvemail_row">
            <div class="approvemail_label">Subject:</div>
            <div class="approvemail_data">
                <input type="text" class="aprovemail_input" id="approvemail_subj" value="<?=$subject?>" />
            </div>
        </div>
        <div class="approvemail_row">
            <div class="approvemail_label">Message:</div>
        </div>
        <div class="approvemail_row">
            <textarea class="aprovemail_message"><?=$message?></textarea>
        </div>
        <div class="approvemail_row">
            <div class="approvemail_send" data-order="<?=$order_id?>">
                <img src="/img/artpopup_approvesebd_btn.png" alt="send"/>
            </div>
        </div>

    </div>
</div>