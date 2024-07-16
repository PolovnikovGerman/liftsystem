<div class="msgmovefoldersarea">
    <?php foreach ($folders as $folder) { ?>
        <div class="movemsgfolder <?=$folder['folder_id']==$current ? '' : 'available'?>"><?=$folder['folder_name']?></div>
    <?php } ?>
</div>

