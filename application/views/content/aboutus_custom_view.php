<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="aboutuscontent-area">
    <div class="aboutus-leftpart">
        <div class="content-row">
            <div class="label about_maintitle">Main Title:</div>
            <input class="about_maintitle" readonly="readonly" value="<?=ifset($data,'about_maintitle')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_mainbodybodytext">Main Body Text:</div>
            <textarea class="about_mainbodybodytext" readonly="readonly"><?=ifset($data,'about_mainbodybodytext')?></textarea>
        </div>
        <div class="content-row">
            <div class="label about-visitaddress">Visit Us Address:</div>
            <textarea class="about-visitaddress" readonly="readonly"><?=ifset($address,'address_visit')?></textarea>
        </div>
        <div class="content-row">
            <div class="label about_addresspart">Toll Free:</div>
            <input class="about_freephone" readonly="readonly" value="<?=ifset($address,'address_phone')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_addresspart">Int'l Call:</div>
            <input class="about_intlcall" readonly="readonly" value="<?=ifset($address,'address_phonelocal')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_addresspart">Fax:</div>
            <input class="about_fax" readonly="readonly" value="<?=ifset($address,'address_fax')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_addresspart">Email:</div>
            <input class="about_email" readonly="readonly" value="<?=ifset($address,'address_email')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_addresspart">Hours:</div>
            <input class="about_hours" readonly="readonly" value="<?=ifset($address,'address_hours')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_addresspart">Days:</div>
            <input class="about_days" readonly="readonly" value="<?=ifset($address,'address_days')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_affilationlogos">Affiliation Logos:</div>
            <div class="about_imagesubtitle">click image to enlarge</div>
        </div>
        <div class="content-row">
            <div class="about_imagesubtitlesize">(80px x 65px)</div>
        </div>
        <div class="content-row">
            <div class="about_affilationsrc">
                <?php if (!empty(ifset($data,'about_affilationsrc1'))) { ?>
                    <img src="<?=$data['about_affilationsrc1']?>" alt="Affilation Logo 1"/>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
            <div class="about_affilationsrc">
                <?php if (!empty(ifset($data,'about_affilationsrc2'))) { ?>
                    <img src="<?=$data['about_affilationsrc2']?>" alt="Affilation Logo 2"/>
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </div>
        </div>
    </div>
    <div class="aboutus-rightpart">
        <div class="content-row">
            <div class="label about_mainimage">Main Image:</div>
            <div class="about_imagesubtitle">click image to enlarge</div>
        </div>
        <div class="content-row">
            <div class="about_imagesubtitlesize">(535px x 395px)</div>
        </div>
        <div class="content-row">
            <div class="about_mainimagesrc">
                <?php if (!empty(ifset($data,'about_mainimage'))) { ?>
                    <img src="<?=$data['about_mainimage']?>" alt="Main Image"/>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="label about_inboxtitle">In Box Title::</div>
            <input class="about_inboxtitle" readonly="readonly" value="<?=ifset($data,'about_inboxtitle')?>"/>
        </div>
        <div class="content-row">
            <div class="label about_inboxtext">In Box Text:</div>
            <textarea class="about_inboxtext" readonly="readonly"><?=ifset($data,'about_inboxtext')?></textarea>
        </div>
    </div>
    <div class="content-row">
        <div class="label about_testimonialstitle">Testimonial Title:</div>
        <input class="about_testimonialstitle" readonly="readonly" value="<?=ifset($data,'about_testimonialstitle')?>"/>
    </div>
</div>