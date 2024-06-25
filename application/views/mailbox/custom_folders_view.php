<?php foreach ($folders as $folder) { ?>
    <?php if ($folder['main']==0) { ?>
        <?php if ($folder['empty']==1) { ?>
            <li class="customfoldermsg" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></li>
        <?php } else { ?>
            <li class="customfoldermsg" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?><span><?=$folder['cnt']?></span></li>
        <?php } ?>
    <?php } ?>
<?php } ?>
