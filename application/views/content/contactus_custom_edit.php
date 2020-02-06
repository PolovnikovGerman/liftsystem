<input type="hidden" id="contact_session" value="<?=$session?>"/>
<input type="hidden" id="contactus_previewurl" value="/content/prepare_contactus_preview?version=<?=$session?>"/>
<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="contactcontent-area">
    <div class="content-row">
        <div class="label contact_maintitle">Main Title:</div>
        <input class="contact_maintitle" data-content="content" data-field="contact_maintitle" value="<?=ifset($data,'contact_maintitle')?>"/>
    </div>
    <div class="content-row">
        <div class="label contact_subtitle">Sub-Title:</div>
        <input class="contact_subtitle" data-content="content" data-field="contact_subtitle" value="<?=ifset($data,'contact_subtitle')?>"/>
    </div>
    <div class="content-row">
        <div class="label contact_smalltext">Small Text:</div>
        <input class="contact_smalltext" data-content="content" data-field="contact_smalltext" value="<?=ifset($data,'contact_smalltext')?>"/>
    </div>
    <div class="content-row">
        <div class="label contact_bluetext">Small Text:</div>
        <input class="contact_bluetext" data-content="content" data-field="contact_bluetext" value="<?=ifset($data,'contact_bluetext')?>"/>
    </div>
    <div class="content-row contact_addressarea">
        <div class="address-left">
            <div class="content-row">
                <div class="label contact_bigphone">Big Phone:</div>
                <input class="contact_bigphone" data-content="address" data-field="address_phone" value="<?=ifset($address,'address_phone')?>">
            </div>
            <div class="content-row">
                <div class="label contact_smallphone">Small Phone:</div>
                <input class="contact_smallphone" data-content="address" data-field="address_phonelocal" value="<?=ifset($address,'address_phonelocal')?>"><span class="contact_smallphone"> (Local)</span>
            </div>
            <div class="content-row">
                <div class="label contact_address">Address:</div>
                <textarea class="contact_address" data-content="address" data-field="address_visit"><?=ifset($address,'address_visit')?></textarea>
            </div>
        </div>
        <div class="address-right">
            <div class="content-row">
                <div class="label contact_email">Email:</div>
                <input class="contact_email" data-content="address" data-field="address_email" value="<?=ifset($address,'address_email')?>">
            </div>
            <div class="content-row">
                <div class="label contact_hours">Our Hours:</div>
                <input class="contact_hours" data-content="address" data-field="address_hours" value="<?=ifset($address,'address_hours')?>">
            </div>
            <div class="content-row">
                <div class="label contact_hours">&nbsp;</div>
                <input class="contact_days" data-content="address" data-field="address_days" value="<?=ifset($address,'address_days')?>">
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label contact_captchtitle">CAPTCHA Title:</div>
        <input class="contact_captchtitle" data-content="content" data-field="contact_captchtitle" value="<?=ifset($data,'contact_captchtitle')?>"/>
    </div>
</div>