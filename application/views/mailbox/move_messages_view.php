<div class="msgmovefoldersarea">
    <?php foreach ($folders as $folder) { ?>
        <div class="movemsgfolder <?=$folder['folder_id']==$current ? '' : 'available'?>" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></div>
    <?php } ?>
</div>