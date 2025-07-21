<?php $numf = 0;?>
<?php foreach ($folders as $folder): ?>
    <?php if ($folder['main']==0): ?>
        <?php if ($numf == 0): ?>
            <div class="folders-column">
        <?php endif; ?>
        <div class="btn-folder" data-folder="<?=$folder['folder_id']?>"><?=$folder['folder_name']?></div>
        <?php $numf++;?>
        <?php if ($numf == 2): ?>
            <?php $numf = 0;?>
            </div>
        <?php endif;?>
    <?php endif; ?>
<?php endforeach;?>
<?php if ($numf > 0): ?>
    </div>
<?php endif; ?>