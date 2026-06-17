<div class="invitecheckout_content">
    <div class="invitecheckout_content">
        <div class="invitecheckout_row">
            <div class="invitecheckout_label">From:</div>
            <div class="invitecheckout_data">
                <input type="text" readonly="readonly" class="invitecheckout_input" id="invitecheckoutemail_from" value="<?=$from?>"/>
            </div>
        </div>
        <div class="invitecheckout_row">
            <div class="invitecheckout_label">To:</div>
            <div class="invitecheckout_data">
                <input type="text" class="invitecheckout_input" id="invitecheckoutname_to" value="<?=$invite_name?>"/>
            </div>
        </div>
        <div class="invitecheckout_row">
            <div class="invitecheckout_label">&nbsp;</div>
            <div class="invitecheckout_data">
                <input type="text" class="invitecheckout_input" id="invitecheckoutemail_to" value="<?=$invite_email?>"/>
            </div>
        </div>
        <div class="invitecheckout_row">
            <div class="invitecheckout_label">Subject:</div>
            <div class="invitecheckout_data">
                <input type="text" class="invitecheckout_input" id="invitecheckoutsubject" value="<?=$subject?>"/>
            </div>
        </div>
        <div class="invitecheckout_row">
            <div class="invitecheckout_send" data-order="<?=$order_id?>">
                <img src="/img/art/artpopup_approvesebd_btn.png" alt="send"/>
            </div>
        </div>
    </div>
</div>