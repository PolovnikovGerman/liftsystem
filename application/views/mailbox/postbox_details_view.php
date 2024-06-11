<input type="hidden" id="hidefolders" value="1"/>
<input type="hidden" id="hidecustom" value="0">
<input type="hidden" id="postbox" value="<?=$postbox?>"/>
<div class="body-page">
    <div class="left-nav">
        <?=$folders?>
    </div>
    <div class="emails-block">
        <div class="emails-block-header">
            <?=$headers_view?>
        </div>
        <div class="emails-block-body"><?=$messages?></div>
    </div>
</div>