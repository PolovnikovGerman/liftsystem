<div class="left-nav-title">Compose</div>
<div class="left-nav-list">
    <ul>
        <?php $numpp=1;?>
        <?php foreach ($folders as $folder) { ?>
            <?php if ($folder['main']==1) {?>
                <?php if ($folder['empty']==1) { ?>
                    <li class="viewfoldermsg <?=$folder['active']==1 ? 'active' : ''?> <?=$numpp > 5 ? 'hideallow' : '' ?>" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></li>
                <?php } else { ?>
                    <li class="viewfoldermsg <?=$folder['active']==1 ? 'active' : ''?>  <?=$numpp > 5 ? 'hideallow' : '' ?>" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?><span><?=$folder['cnt']?></span></li>
                <?php } ?>
            <?php } ?>
            <?php $numpp++;?>
        <?php } ?>
        <li class="hideshowfolders"><span class="arrow-less"><i class="fa fa-chevron-up" aria-hidden="true"></i></span>Less</li>
    </ul>
    <div class="views-show">
        Views <span>Show</span>
    </div>
    <div class="list-folder-title">
        Folders <span>Hide</span>
    </div>
    <div class="list-newfolder">
        <div class="newfolderadd">
            <span class="plus-folder"><i class="fa fa-plus" aria-hidden="true"></i></span> New Folder
        </div>
    </div>
    <ul class="list-folders">
        <?php foreach ($folders as $folder) { ?>
            <?php if ($folder['main']==0) { ?>
                <?php if ($folder['empty']==1) { ?>
                    <li class="customfoldermsg" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></li>
                <?php } else { ?>
                    <li class="customfoldermsg" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?><span><?=$folder['cnt']?></span></li>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </ul>
</div>
