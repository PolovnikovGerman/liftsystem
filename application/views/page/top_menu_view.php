<div class="brandschooselabel">Display: </div>
<div class="brandschoosearea">
    <?php foreach ($brands as $row) { ?>
        <div class="brandchoseval <?=$row['brand']==$active ? 'active' :'' ?>" data-brand="<?=$row['brand']?>">
            <?= $row['brand']==$active ? '<i class="fa fa-check-square-o" aria-hidden="true"></i>' : '<i class="fa fa-square-o" aria-hidden="true"></i>'?>
        </div>
        <div class="brandlabel <?=$row['brand']==$active ? 'active' :'' ?>" data-brand="<?=$row['brand']?>"><?=$row['label']?></div>
    <?php } ?>
</div>