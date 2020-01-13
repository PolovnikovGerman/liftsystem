<input type="hidden" id="terms_session" value="<?=$session?>"/>
<input type="hidden" id="terms_previewurl" value="/contents/prepare_termspage_preview?version=<?=$session?>"/>
<div class="contentinfotitle">
    <div class="displaycontent show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">Content</div>
</div>
<div class="termscontent-area">
    <div class="content-row">
        <div class="label term_maintitle">Main Title:</div>
        <input class="term_maintitle" name="term_maintitle" data-content="content" data-field="term_maintitle" value="<?=$data['term_maintitle']?>"/>
    </div>
    <div class="content-row">
        <div class="label term_smalltext">Main Body Text:</div>
        <textarea class="term_smalltext" name="term_smalltext" data-content="content" data-field="term_smalltext"><?=$data['term_smalltext']?></textarea>
    </div>
</div>
<div class="termsinfotitle">
    <div class="displaytermsdata show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="termstitle">Terms & Polices</div>
</div>
<div class="termsdata-area">
    <?=$terms?>
</div>
