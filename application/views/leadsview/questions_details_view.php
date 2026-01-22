<input type="hidden" id="question_id" value="<?=$data['email_id']?>"/>
<input type="hidden" id="leademail_id" value="<?=$data['leademail_id']?>"/>
<div class="contant-popup">
    <div class="webquestsform">
        <div class="webquestsform-row">
            <div class="webquestsform-group onecolume">
                <input class="webquests-date" type="text" name="webquests-date" readonly="readonly" value="<?=$data['email_date']?>"/>
                <label>Date:</label>
            </div>
            <div class="webquestsform-group onecolume">
                <input class="webquests-type" type="text" name="webquests-type" readonly="readonly" value="<?=$data['email_subtype']?>"/>
                <label>Type:</label>
            </div>
            <div class="webquestsform-group twocolume">
                <input class="webquests-webpage" type="text" name="webquests-webpage" readonly="readonly" value="<?=$data['email_webpage']?>"/>
                <label class="labelbig">Webpage:</label>
            </div>
        </div>
        <div class="webquestsform-row">
            <div class="webquestsform-group onecolume">
                <input class="webquests-date" type="text" name="webquests-date" readonly="readonly" value="<?=$data['email_sender']?>"/>
                <label>Name:</label>
            </div>
            <div class="webquestsform-group onecolume">
                <input class="webquests-type" type="text" name="webquests-type" readonly="readonly" value="<?=$data['email_sendermail']?>"/>
                <label>Email:</label>
            </div>
            <div class="webquestsform-group twocolume">
                <input class="webquests-webpage" type="text" name="webquests-webpage" readonly="readonly" value="<?=$data['email_senderphone']?>"/>
                <label class="labelbig">Phone:</label>
            </div>
        </div>
        <div class="webquestsform-row">
            <div class="webquestsform-group webquestsform-message">
                <label>Message:</label>
                <textarea readonly="readonly"><?=$data['email_text']?></textarea>
            </div>
        </div>
    </div>
</div>
