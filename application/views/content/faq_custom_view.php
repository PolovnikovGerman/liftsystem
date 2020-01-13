<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="faqcontent-area">
    <div class="content-row">
        <div class="label faq_maintitle">Main Title:</div>
        <input class="faq_maintitle" name="faq_maintitle" readonly="readonly" value="<?=$data['faq_maintitle']?>"/>
    </div>
    <div class="content-row">
        <div class="label faq_mainbody">Main Body Text:</div>
        <textarea class="faq_mainbody" name="faq_mainbody" readonly="readonly"><?=$data['faq_mainbody']?></textarea>
    </div>
    <div class="content-row">
        <div class="label faq_helptext">Instruction Text:</div>
        <input class="faq_helptext" name="faq_helptext" readonly="readonly" value="<?=$data['faq_helptext']?>"/>
    </div>
</div>
<?=$faq_sections?>