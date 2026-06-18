<div class="invitecheckout_content">
    <div class="invitecheckout_header">
        <h2 class="lined-heading">
            <span class="securepaymenttitle">Secure Payment Invite</span>
        </h2>
    </div>
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
            send
        </div>
    </div>
</div>