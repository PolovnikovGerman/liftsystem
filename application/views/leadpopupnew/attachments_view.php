<?php foreach ($attachments as $attachment) : ?>
    <div class="attachfile truncateoverflowtext" data-link="<?=$attachment['attachment']?>" data-title="<?=$attachment['source_name']?>">
        <span class="attachfile-icn"><i class="fa fa-file-o" aria-hidden="true"></i></span>
        <span class="attachfile-name"><?=$attachment['source_name']?></span>
    </div>
<?php endforeach; ?>
