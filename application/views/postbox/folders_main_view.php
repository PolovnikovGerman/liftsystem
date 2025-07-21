<?php foreach ($folders as $folder) : ?>
    <?php if ($folder['main']==1) : ?>
        <div class="mainbtn <?=$folder['class']?> <?=$folder['folder_id']==$activefolder ? 'active' : ''?>" data-folder="<?=$folder['folder_id']?>">
            <?=$folder['folder_name']?><?=$folder['empty']==1 ? '' : '<span class="mainbtn-number">'.$folder['cnt'].'</span>'?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

