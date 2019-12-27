<form id="msgstatus">
    <input type="hidden" id="mail_id" name="mail_id" value="<?=$email_id?>"/>
    <input type="hidden" id="leademail_id" name="leademail_id" value="<?=$leademail_id?>"/>
    <div class="updatequest_status">
        <!-- Question details -->
        <div class="question_details label"><?=$title?></div>
        <div class="question_details">
            <div class="question_qdate">Date: <?=$email_date?></div>
            <div class="question_customer">Name: <?=$email_sender?></div>
            <div class="question_custommail">Email: <?=$email_sendermail?></div>
        </div>
        <div class="question_details">
            <div class="leadselect_label">Select Lead</div>
            <div class="leadselect_input"><?=$leadselect?></div>
        </div>
        <div class="question_details label">Lead Details</div>
        <div class="question_details">
            <div class="leaddate"><?=$lead_date?></div>
            <div class="leadcustomer"><?=$lead_customer?></div>
            <div class="leadcustommail"><?=$lead_mail?></div>
        </div>
        <div class="question_details">
            <div class="leadnew_label">Or create new Lead</div>
            <div class="leads_addnew"><img alt="New Leads" src="/img/art/new_lead.png"></div>
        </div>
        <div class="savequeststatus">
            <a class="savequest" href="javascript:void(0);"><img src="/img/art/saveticket.png" alt="Save"></a>
        </div>
    </div>
</form>