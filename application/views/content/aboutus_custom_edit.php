<input type="hidden" id="about_session" value="<?=$session?>"/>
<!--<input type="hidden" id="previewurl" value="/content/prepare_aboutus_preview?version=--><?php //=$session?><!--"/>-->
<!--<div class="contentinfotitle">-->
<!--    <div class="displaycontent show">-->
<!--        <i class="fa fa-chevron-down" aria-hidden="true"></i>-->
<!--    </div>-->
<!--    <div class="title">Content</div>-->
<!--</div>-->
<!--<div class="aboutuscontent-area">-->
<!--    <div class="aboutus-leftpart">-->
<!--        <div class="content-row">-->
<!--            <div class="label about_maintitle">Main Title:</div>-->
<!--            <input class="about_maintitle" data-content="content" data-field="about_maintitle" value="--><?php //=ifset($data,'about_maintitle')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_mainbodybodytext">Main Body Text:</div>-->
<!--            <textarea class="about_mainbodybodytext" data-content="content" data-field="about_mainbodybodytext">--><?php //=ifset($data,'about_mainbodybodytext')?><!--</textarea>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about-visitaddress">Visit Us Address:</div>-->
<!--            <textarea class="about-visitaddress" data-content="address" data-field="address_visit">--><?php //=ifset($address,'address_visit')?><!--</textarea>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_addresspart">Toll Free:</div>-->
<!--            <input class="about_freephone" data-content="address" data-field="address_phone" value="--><?php //=ifset($address,'address_phone')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_addresspart">Int'l Call:</div>-->
<!--            <input class="about_intlcall" data-content="address" data-field="address_phonelocal" value="--><?php //=ifset($address,'address_phonelocal')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_addresspart">Fax:</div>-->
<!--            <input class="about_fax" data-content="address" data-field="address_fax" value="--><?php //=ifset($address,'address_fax')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_addresspart">Email:</div>-->
<!--            <input class="about_email" data-content="address" data-field="address_email" value="--><?php //=ifset($address,'address_email')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_addresspart">Hours:</div>-->
<!--            <input class="about_hours" data-content="address" data-field="address_hours" value="--><?php //=ifset($address,'address_hours')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_addresspart">Days:</div>-->
<!--            <input class="about_days" data-content="address" data-field="address_days" value="--><?php //=ifset($address,'address_days')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_affilationlogos">Affiliation Logos:</div>-->
<!--            <div class="about_imagesubtitle">click image to enlarge</div>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="about_imagesubtitlesize">(80px x 65px)</div>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="affilateimagearea" data-image="1">-->
<!--                <div class="--><?php //=empty(ifset($data,'about_affilationsrc1')) ? 'about_affilationempty' : 'about_affilationsrc'?><!--" data-image="1">-->
<!--                    --><?php //if (!empty(ifset($data,'about_affilationsrc1'))) { ?>
<!--                        <img src="--><?php //=$data['about_affilationsrc1']?><!--" alt="Affilation Logo 1"/>-->
<!--                    --><?php //} else { ?>
<!--                        <div class="about_affilationupload" id="newaffilate_--><?php //=getuploadid()?><!--"></div>-->
<!--                    --><?php //} ?>
<!--                </div>-->
<!--                --><?php //if (!empty(ifset($data,'about_affilationsrc1'))) { ?>
<!--                    <div class="about_affilationremove" data-image="1">-->
<!--                        <i class="fa fa-trash" aria-hidden="true"></i>-->
<!--                    </div>-->
<!--                --><?php //} ?>
<!--            </div>-->
<!--            <div class="affilateimagearea" data-image="2">-->
<!--                <div class="--><?php //=empty(ifset($data,'about_affilationsrc2')) ? 'about_affilationempty' : 'about_affilationsrc'?><!--" data-image="2">-->
<!--                    --><?php //if (!empty(ifset($data,'about_affilationsrc2'))) { ?>
<!--                        <img src="--><?php //=$data['about_affilationsrc2']?><!--" alt="Affilation Logo 2"/>-->
<!--                    --><?php //} else { ?>
<!--                        <div class="about_affilationupload" id="newaffilate_--><?php //=getuploadid()?><!--"></div>-->
<!--                    --><?php //} ?>
<!--                </div>-->
<!--                --><?php //if (!empty(ifset($data,'about_affilationsrc2'))) { ?>
<!--                    <div class="about_affilationremove" data-image="2">-->
<!--                        <i class="fa fa-trash" aria-hidden="true"></i>-->
<!--                    </div>-->
<!--                --><?php //} ?>
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="aboutus-rightpart">-->
<!--        <div class="content-row">-->
<!--            <div class="label about_mainimage">Main Image:</div>-->
<!--            <div class="about_imagesubtitle">click image to enlarge</div>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="about_imagesubtitlesize">(535px x 395px)</div>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div id="about_mainimagearea">-->
<!--                --><?php //if (!empty(ifset($data,'about_mainimage'))) { ?>
<!--                    <div class="about_mainimagesrc">-->
<!--                        <img src="--><?php //=$data['about_mainimage']?><!--" alt="Main Image"/>-->
<!--                    </div>-->
<!--                    <div class="about_mainimageremove">-->
<!--                        <i class="fa fa-trash" aria-hidden="true"></i>-->
<!--                    </div>-->
<!--                --><?php //} else { ?>
<!--                    <div class="about_mainimageempty">-->
<!--                        <div class="about_mainimageupload" id="mainimageupload"></div>-->
<!--                    </div>-->
<!--                --><?php //} ?>
<!--            </div>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_inboxtitle">In Box Title::</div>-->
<!--            <input class="about_inboxtitle" data-content="content" data-field="about_inboxtitle" value="--><?php //=ifset($data,'about_inboxtitle')?><!--"/>-->
<!--        </div>-->
<!--        <div class="content-row">-->
<!--            <div class="label about_inboxtext">In Box Text:</div>-->
<!--            <textarea class="about_inboxtext" data-content="content" data-field="about_inboxtext">--><?php //=ifset($data,'about_inboxtext')?><!--</textarea>-->
<!--        </div>-->
<!--    </div>-->
<!--    <div class="content-row">-->
<!--        <div class="label about_testimonialstitle">Testimonial Title:</div>-->
<!--        <input class="about_testimonialstitle" data-content="content" data-field="about_testimonialstitle" value="--><?php //=ifset($data,'about_testimonialstitle')?><!--"/>-->
<!--    </div>-->
<!--</div>-->