<?php foreach ($attachments as $attachment) : ?>
    <div class="attachfile truncateoverflowtext">
        <span class="attachfile-icn"><i class="fa fa-file-o" aria-hidden="true"></i></span>
        <span class="attachfile-name" data-link="<?=$attachment['attachment']?>"><?=$attachment['source_name']?></span>
    </div>
<?php endforeach; ?>
