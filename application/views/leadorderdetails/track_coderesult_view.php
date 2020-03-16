<div class="trackcoderesults">
    <div class="title">Track Code <?=$trackcode?> , Delivery System <?=$system?></div>    
    <div class="results">
        <?php foreach ($tracklog as $row) { ?>            
            <div class="title">Package # <?=$row['package_num']?></div>
            <div class="row">
            <div class="label">Last Activity</div>
            <div class="value"><?=$row['status'] ?></div>
            </div>
            <?php if (isset($row['date'])) { ?>
                <div class="row">
                    <div class="label">Date</div>
                    <div class="value"><?= date('m/d/Y H:i:s', $row['date']) ?></div>
                </div>
            <?php } ?>
            <?php if (isset($row['address'])) { ?>
                <div class="row">
                    <div class="label">Address</div>
                    <div class="value"><?=$row['address'] ?></div>
                </div>            
            <?php } ?>

        <?php } ?>
    </div>
</div>