<?php $numpp = 0;?>
<div class="emlfolders-menu-close"><img src="/img/postbox/close.svg" alt="Close"/></div>
<?php foreach($folders as $folder):?>
    <?php if ($numpp%10==0) : ?>
        <div class="efm-column">
    <?php endif; ?>
    <div class="efm-item" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></div>
    <?php $numpp++;?>
    <?php if ($numpp==10) : ?>
        <?php $numpp = 0;?>
        </div>
    <?php endif; ?>
<?php endforeach;?>
<?php if ($numpp<10) : ?>
    </div>
<?php endif; ?>

