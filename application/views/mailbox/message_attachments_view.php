<div class="box-email-bottom-attachments">
    <ul class="attachmentlist">
        <?php foreach ($attachments as $attachment) { ?>
            <li class="attachmentbox">
                <div class="attach-file-area" data-link="<?=$attachment['attachment_link']?>" data-name="<?=$attachment['attachment_name']?>" title="<?=$attachment['attachment_name']?>">
                    <?php if ($attachment['attachment_type']=='JPEG' || $attachment['attachment_type']=='JPG' || $attachment['attachment_type']=='PNG' || $attachment['attachment_type']=='GIF') { ?>
                        <img src="<?=$attachment['attachment_link']?>" alt="Preview"/>
                    <?php } elseif ($attachment['attachment_type']=='VND.OPENXMLFORMATS-OFFICEDOCUMENT.SPREADSHEETML.SHEET') {?>
                        <i class="fa fa-file-excel-o"></i>
                    <?php } elseif ($attachment['attachment_type']=='POSTSCRIPT') {?>
                        <i class="fa fa-file-photo-o"></i>
                    <?php } elseif ($attachment['attachment_type']=='X-PHOTOSHOP') {?>
                        <i class="fa fa-file-photo-o"></i>
                    <?php } elseif ($attachment['attachment_type']=='PDF') {?>
                        <i class="fa fa-file-pdf-o"></i>
                    <?php } else { ?>
                        <i class="fa fa-file-text-o"></i>
                    <?php } ?>
                </div>
                <div class="attachmenttitle"><?=$attachment['attachment_name']?></div>
            </li>
        <?php } ?>
    </ul>
</div>
