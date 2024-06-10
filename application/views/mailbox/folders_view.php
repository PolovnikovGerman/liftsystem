<div class="left-nav-title">Compose</div>
<div class="left-nav-list">
    <ul>
        <?php $numpp=1;?>
        <?php foreach ($folders as $folder) { ?>
            <?php if ($folder['main']==1) {?>
                <?php if ($folder['empty']==1) { ?>
                    <li class="<?=$folder['active']==1 ? 'active' : ''?> <?=$numpp > 5 ? 'hideallow' : '' ?>" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></li>
                <?php } else { ?>
                    <li class="<?=$folder['active']==1 ? 'active' : ''?>  <?=$numpp > 5 ? 'hideallow' : '' ?>" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?><span><?=$folder['cnt']?></span></li>
                <?php } ?>
            <?php } ?>
            <?php $numpp++;?>
        <?php } ?>
        <li><span class="arrow-less"><i class="fa fa-chevron-up" aria-hidden="true"></i></span>Less</li>
    </ul>
    <div class="views-show">
        Views <span>Show</span>
    </div>
    <div class="list-folder-title">
        Folders <span>Hide</span>
    </div>
    <div class="list-newfolder">
        <span class="plus-folder"><i class="fa fa-plus" aria-hidden="true"></i></span> New Folder
    </div>
    <ul class="list-folders">
        <?php foreach ($folders as $folder) { ?>
            <?php if ($folder['main']==0) { ?>
                <li><?=$folder['folder_name']?></li>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
