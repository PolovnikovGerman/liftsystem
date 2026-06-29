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
    <?php if (isset($cc_email) && $cc_email != '') : ?>
<!--        <div class="invitecheckout_row">-->
<!--            <div class="invitecheckout_data">-->
<!--                <input type="text" class="invitecheckout_input" id="invitecheckoutname_cc" value="--><?php //=$cc_name?><!--"/>-->
<!--            </div>-->
<!--        </div>-->
        <div class="invitecheckout_row">
            <div class="invitecheckout_label">CC:</div>
            <div class="invitecheckout_data">
                <input type="text" class="invitecheckout_input" id="invitecheckoutemail_cc" value="<?=$cc_email?>"/>
            </div>
        </div>
    <?php endif; ?>
    <?php if (isset($bcc_email) && $bcc_email != '') : ?>
<!--        <div class="invitecheckout_row">-->
<!--            <div class="invitecheckout_data">-->
<!--                <input type="text" class="invitecheckout_input" id="invitecheckoutname_bcc" value="--><?php //=$bcc_name?><!--"/>-->
<!--            </div>-->
<!--        </div>-->
        <div class="invitecheckout_row">
            <div class="invitecheckout_label">BCC:</div>
            <div class="invitecheckout_data">
                <input type="text" class="invitecheckout_input" id="invitecheckoutemail_bcc" value="<?=$bcc_email?>"/>
            </div>
        </div>
    <?php endif; ?>
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