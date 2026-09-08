<?php foreach ($previews as $preview) : ?>
    <div class="previewpict_optn">
        <div class="previewpict_optn_header">
            <div class="previewpict_optn_checkbox">
                <input type="checkbox" class="" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
            </div>
            <div class="previewpict_optn_title">Opt <?=$preview['preview_option']?></div>
            <div class="previewpict_optn_star">
                <img src="/img/leadorder/star.svg">
            </div>
        </div>
        <div class="previewpict_optn_box" id="preview_<?=$artwork?>_<?=$preview['preview_option']?>">
            <ul>
                <?php foreach ($preview['data'] as $previewdata) : ?>
                    <li><?=$previewdata['out_proofname']?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endforeach; ?>