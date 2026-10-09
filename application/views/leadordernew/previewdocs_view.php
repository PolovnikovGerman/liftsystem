<?php foreach ($previews as $preview) : ?>
    <div class="previewpict_optn <?=$edit==1 ? 'edit' : ''?>">
        <div class="previewpict_optn_header">
            <div class="previewpict_optn_checkbox">
                <input type="checkbox" class="" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
            </div>
            <div class="previewpict_optn_title">Opt <?=$preview['preview_option']?></div>
            <div class="previewpict_optn_star <?=$preview['approved']==1 ? 'approved' : 'unapproved'?>" data-section="<?=$preview['preview_option']?>">
                <img src="/img/leadorder/star.svg">
            </div>
        </div>
        <div class="previewpict_optn_box <?=$edit==1 ? 'edit' : ''?>" id="preview_<?=$artwork?>_<?=$preview['preview_option']?>">
            <ul>
                <?php foreach ($preview['data'] as $previewdata) : ?>
                    <li>
                        <span class="previewpicname" data-previewdoc="<?=$previewdata['artwork_preview_id']?>" data-event="hover" data-css="proofdetailsballonbox"
                        data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="left" data-textcolor="#000"
                        data-balloon="<?=$previewdata['preview_source']?>" data-timer="4000" data-delay="1000">preview_<?=str_pad($previewdata['numpp'],2,0,STR_PAD_LEFT)?></span>
                    <?php if ($edit==1) : ?>
                        <span class="previewpic_remove" data-previewdoc="<?=$previewdata['artwork_preview_id']?>" data-section="<?=$previewdata['preview_option']?>" data-previewname="<?=$previewdata['preview_source']?>">
                            [&mdash;]
                        </span>
                    <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endforeach; ?>