<div class="questiondetails">
    <div class="questdetailsrow">
        <div class="quest_detaildate">
            <div class="questdetail_label"><img src="/img/leads/tooltip-date.png" alt='Date Label'/></div>
            <div class="questdetail_input">
                <input id="detaildate" type="text" readonly="readonly" class="inptdetdate" value="<?=$email_date?>"/>
            </div>
        </div>
        <div class="quest_detailtype">
            <div class="questdetail_label"><img src="/img/leads/tooltip-type.png"/></div>
            <div class="questdetail_input">
                <input type="text" readonly="readonly"  id="detailsubtype" class="inptdetdate" value="<?=$email_subtype?>"/>
            </div>
        </div>
        <div class="quest_detailwebpage">
            <div class="questdetail_label"><img src="/img/leads/tooltip-webpage.png"/></div>
            <div class="questdetail_input">
                <input type="text" readonly="readonly" id="detailwebpage" class="inptdetdate" value="<?=$email_webpage?>"/>
            </div>
        </div>
    </div>
    <div class="questdetailsrow">
        <div class="quest_detaildate">
            <div class="questdetail_label"><img src="/img/leads/tooltip-name.png"/></div>
            <div class="questdetail_input">
                <input type="text" readonly="readonly" id="detailname" class="inptdetdate" value="<?=$email_sender?>"/>
            </div>
        </div>
        <div class="quest_detailtype">
            <div class="questdetail_label"><img src="/img/leads/tooltip-email.png"/></div>
            <div class="questdetail_input">
                <input type="text" readonly="readonly"  id="detailemail" class="inptdetdate" value="<?=$email_sendermail?>"/>
            </div>
        </div>
        <div class="quest_detailwebpage">
            <div class="questdetail_label"><img src="/img/leads/tooltip-phone.png"/></div>
            <div class="questdetail_input">
                <input type="text" readonly="readonly"  id="detailphone" class="inptdetdate" value="<?=$email_senderphone?>"/>
            </div>
        </div>
    </div>
    <div class="questdetailsrow">
        <div class="quest_detailwebpage">
            <img src="/img/leads/tooltip-message.png"/>
        </div>
        <div class="questdetail_text">
            <textarea class="msgtext" readonly="readonly" id="detailmessage"><?=$email_text?></textarea>
        </div>
    </div>
</div>
