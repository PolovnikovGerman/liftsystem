<div class="<?=empty($imagesrc) ? 'about_affilationempty' : 'about_affilationsrc'?>" data-image="<?=$imagenum?>">
    <?php if (!empty($imagesrc)) { ?>
        <img src="<?=$imagesrc?>" alt="Affilation Logo"/>
    <?php } else { ?>
        <div class="about_affilationupload" id="newaffilate_<?=getuploadid()?>"></div>
    <?php } ?>
</div>
<?php if (!empty($imagesrc)) { ?>
    <div class="about_affilationremove" data-image="<?=$imagenum?>">
        <i class="fa fa-trash" aria-hidden="true"></i>
    </div>
<?php } ?>
