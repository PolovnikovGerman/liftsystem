<div class="trackemail_title">Tracking Email</div>
<div class="trackemail_row">
    <div class="label">From:</div>
    <div class="value">
        <input type="text" class="trackemailinpt" data-field="sender" value="<?=$sender?>"/>        
    </div>
</div>
<div class="trackemail_row">
    <div class="label">To:</div>
    <div class="value">
        <textarea class="trackemailto"><?=$customer?></textarea>
    </div>
</div>
<div class="trackemail_row" id="trackshowbccarea">
    <?php if (empty($bcc)) { ?>
    <div class="showtrackmailbcc">add bcc</div>
    <?php } else { ?>
        <div class="label">From:</div>
        <div class="value">
            <input type="text" class="trackemailinpt" data-field="bcc" value="<?=$bcc?>"/>        
        </div>    
    <?php } ?>
</div>
<div class="trackemail_row">
    <div class="labelsubj">Subject:</div>
    <div class="value">
        <input type="text" class="trackemailsubj" value="<?=$subject?>"/>        
    </div>    
</div>
<div class="trackemail_row">
    <div class="labelsubj">Message:</div>
</div>
<div class="trackemail_message">
    <textarea class="trackemailmessage"><?=$message?></textarea>
</div>
<div class="sendtraccodemessage">&nbsp;</div>