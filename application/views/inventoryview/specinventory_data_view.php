<?php $numpp = 0 ?>
<?php foreach ($data as $row) { ?>
    <div class="inventorydatarow <?= $numpp % 2 == 0 ? 'whitedatarow' : 'greydatarow' ?>" data-color="<?=$row['printshop_color_id']?>">
        <div class="aftercontainers <?=$row['needclass']?>"><?=$row['aftercontproc']?>%</div>
        <div class="needtomake <?=$row['needclass']?>"><?=QTYOutput($row['needtomake'])?></div>        
        <div class="costea"><?= $row['price'] ?></div>
        <div class="devider">&nbsp;</div>
        <div class="specs">
            <div class="specsdata <?=$row['specsclass']?>" <?=$row['specsurl']?> data-color="<?=$row['printshop_color_id']?>">
                <i class="fa fa-file-text-o <?=$row['specsclass']?>" aria-hidden="true"></i>
            </div>
        </div>
        <div class="draw">            
            <div class="itemlabel <?=(empty($row['item_label']) ? '' : 'full')?>" data-color="<?=$row['printshop_item_id']?>">
                <i class="fa fa-file-text-o <?=$row['itemlabel']?>" aria-hidden="true"></i>
            </div>
        </div>        
        <div class="pics">
            <div class="picsdata" data-color="<?=$row['printshop_color_id']?>">
                <i class="fa fa-file-text-o <?= $row['picsclass'] ?>" aria-hidden="true"></i>
            </div>
        </div>        
    </div>
    <?php $numpp++;?>
<?php } ?>