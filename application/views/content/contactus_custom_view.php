<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="contactcontent-area">
    <div class="content-row">
        <div class="label contact_maintitle">Main Title:</div>
        <input class="contact_maintitle" readonly="readonly" value="<?=ifset($data,'contact_maintitle','')?>"/>
    </div>
    <div class="content-row">
        <div class="label contact_subtitle">Sub-Title:</div>
        <input class="contact_subtitle" readonly="readonly" value="<?=ifset($data,'contact_subtitle','')?>"/>
    </div>
    <div class="content-row">
        <div class="label contact_smalltext">Small Text:</div>
        <input class="contact_smalltext" readonly="readonly" value="<?=ifset($data,'contact_smalltext','')?>"/>
    </div>
    <div class="content-row">
        <div class="label contact_bluetext">Small Text:</div>
        <input class="contact_bluetext" readonly="readonly" value="<?=ifset($data,'contact_bluetext','')?>"/>
    </div>
    <div class="content-row contact_addressarea">
        <div class="address-left">
            <div class="content-row">
                <div class="label contact_bigphone">Big Phone:</div>
                <input class="contact_bigphone" readonly="readonly" value="<?=$address['address_phone']?>">
            </div>
            <div class="content-row">
                <div class="label contact_smallphone">Small Phone:</div>
                <input class="contact_smallphone" readonly="readonly" value="<?=$address['address_phonelocal']?>"><span class="contact_smallphone"> (Local)</span>
            </div>
            <div class="content-row">
                <div class="label contact_address">Address:</div>
                <textarea class="contact_address" readonly="readonly"><?=$address['address_visit']?></textarea>
            </div>
        </div>
        <div class="address-right">
            <div class="content-row">
                <div class="label contact_email">Email:</div>
                <input class="contact_email" readonly="readonly" value="<?=$address['address_email']?>">
            </div>
            <div class="content-row">
                <div class="label contact_hours">Our Hours:</div>
                <input class="contact_hours" readonly="readonly" value="<?=$address['address_hours']?>">
            </div>
            <div class="content-row">
                <div class="label contact_hours">&nbsp;</div>
                <input class="contact_days" readonly="readonly" value="<?=$address['address_days']?>">
            </div>
        </div>
    </div>
    <div class="content-row">
        <div class="label contact_captchtitle">CAPTCHA Title:</div>
        <input class="contact_captchtitle" readonly="readonly" value="<?=ifset($data,'contact_captchtitle','')?>"/>
    </div>
</div>