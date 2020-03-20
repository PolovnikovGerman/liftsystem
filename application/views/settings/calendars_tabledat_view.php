<?php $numpp=1;?>
<?php foreach ($calendars as $row) { ?>
    <div class="calendardatarow <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>">
        <div class="calendar-actions">
            <div class="caldel" data-calendar="<?= $row['calendar_id'] ?>">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
            <div class="caledit" data-calendar="<?= $row['calendar_id'] ?>">
                <i class="fa fa-pencil" aria-hidden="true"></i>
            </div>
        </div>
        <div class="calendar_name"><?=$row['calendar_name']?></div>
        <div class="calendar_status"><?= ($row['calendar_status'] == 0 ? 'Paused' : 'Active') ?></div>
    </div>
    <?php $numpp++;?>
<?php } ?>

