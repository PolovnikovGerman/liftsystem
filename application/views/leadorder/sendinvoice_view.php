<div class="leadorder_sendarea">
    <div class="addressrow">
        <div class="label">From:</div>
        <div class="datainput">
            <input type="text" class="addresfrom" id="mailfrom" data-fld="email_from" value="<?=$email_from?>"/>
        </div>        
    </div>
    <div class="addressrow textdata">
        <div class="label">To:</div>
        <div class="datainput">
            <textarea class="addresto" id="mailto" data-fld="email_to"><?=$email_to?></textarea>
        </div>
    </div>
    <div class="addressrow">
        <div class="bccadd">add bcc</div>        
    </div>
    <div class="addressrow bccdata">
        <div class="label">Bcc:</div>
        <div class="datainput">
            <input type="text" class="addresfrom" id="mailbcc" data-fld="email_bcc" value="<?=$email_bcc?>"/>
        </div>                
    </div>
    <div class="addressrow">
        <div class="subjectlabel">Subject:</div>
        <div class="subjectdata">
            <input type="text" class="addresfrom" id="subject" data-fld="subject" value="<?=$subject?>"/>
        </div>
    </div>
    <div class="attachmentdataarea">
        <div class="attachmrow">
            <div class="label">Attachments:</div>
            <div class="attachmarkdata">
                <input type="checkbox" class="attachcheck" id="send_invoice" data-fld="send_invoice" <?=($send_invoice==1 ? 'checked="checked"' : '')?> />
            </div>
            <div class="attachmarklabel">Invoice</div>
            <div class="attachmarkdata">
                <input type="checkbox" class="attachcheck" id="send_creditapp" data-fld="send_creditapp" <?=($send_creditapp==1 ? 'checked="checked"' : '')?> />
            </div>
            <div class="attachmarklabel" style="width: 63px;">Credit App</div>
            <div class="attachmarkdata">
                <input type="checkbox" class="attachcheck" id="send_w9form" data-fld="send_w9form" <?=($send_w9form==1 ? 'checked="checked"' : '')?> />
            </div>
            <div class="attachmarklabel">W9 Form</div>
        </div>
        <div id="attachlistarea" class="attachlistarea"></div>
        <!--
        <div class="attacform">add attachment</div>
        -->
    </div>    
    <div class="addressrow textdata">
        <div class="messagelabel">Message:</div>
        <textarea class="messagetext" id="message" data-fld="message"><?=$message?></textarea>
    </div>
    <div class="sendinvoiceaction">
        <img src="/img/saleorder/invoice_sendmail_btn.png" alt="send"/>
    </div>
</div>