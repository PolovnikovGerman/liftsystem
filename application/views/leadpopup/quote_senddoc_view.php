<div class="quoteemail_content">
    <div class="datarow">
        <div class="quoteemail_label">From:</div>
        <div class="quoteemail_data">
            <input type="text" class="quoteemail_input" id="quoteemail_from" value="<?=$from?>"/>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteemail_label">To:</div>
        <div class="quoteemail_data">
            <input type="text" class="quoteemail_input" id="quoteemail_to" value="<?=$to?>"/>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteemail_label">Subject:</div>
        <div class="quoteemail_data">
            <input type="text" class="quoteemail_input" id="quoteemail_subject" value="<?=$subject?>"/>
        </div>
    </div>
    <div class="datarow">
        <div class="quoteemail_label">Message:</div>
    </div>
    <div class="datarow">
        <textarea class="quoteemail_message"><?=$message?></textarea>
    </div>
    <div class="datarow">
        <div class="quoteemail_send" data-quote="<?=$quote_id?>">
            <img src="/img/art/artpopup_approvesebd_btn.png" alt="send"/>
        </div>
    </div>
</div>