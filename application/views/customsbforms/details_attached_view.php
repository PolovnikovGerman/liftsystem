<div class="csf-inputgroup attachimg">
    <h5>Attached images:</h5>
</div>
<?php foreach ($attachs as $attach) { ?>
    <div class="csf-inputgroup">
        <div class="file-box">
            <span class="icon-clip"><img src="/img/page_modern/icon-clip.svg"></span>
            <div class="name-file" data-imgsrc="<?=$attach['attachment']?>"><?=$attach['source_name']?></div>
            <div class="btn-closefile" data-attach="<?=$attach['customquote_attachment_id']?>">
                <img src="/img/page_modern/icon-closefile.svg">
            </div>
        </div>
    </div>
<?php } ?>
