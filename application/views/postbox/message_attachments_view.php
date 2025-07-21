<div class="attach-info">
    <div class="numberfiles"><?=count($attachments)?> attached files:</div>
    <div class="emlbtn-downloadall"><span class="downloadall-icn"><i class="fa fa-download" aria-hidden="true"></i></span> Download All</div>
</div>
<div class="fileslist">
    <ul>
        <?php foreach ($attachments as $attachment) : ?>
            <li>
                <div class="filebox" data-url="<?=$attachment['attachment_link']?>">
                    <?=$attachment['thumb']?>
                    <div class="filebox-info">
                        <p class="filename"><?=$attachment['attachment_name']?></p>
                        <p class="filesize"><?=show_filesize($attachment['attachment_size'])?></p>
                        <div class="emlbtn-download" data-url="<?=$attachment['attachment_link']?>"><i class="fa fa-download" aria-hidden="true"></i></div>
                    </div>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
