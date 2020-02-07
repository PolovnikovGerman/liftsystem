<div class="brandschoosearea">
    <?php foreach ($brands as $row) { ?>
        <span class="brandchoseval">
            <?= $row['brand']==$active ? '<i class="fa fa-check-square-o" aria-hidden="true"></i>' : '<i class="fa fa-square-o" aria-hidden="true"></i>'?>
        </span>
        <span class="brandlabel"><?=$row['label']?></span>
    <?php } ?>
</div>