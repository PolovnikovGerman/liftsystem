<?php if (count($items)==0) {?>
    <div class="datarow whitedatarow emptyresults">No Searchs</div>
<?php } else { ?>
    <?php $nrow=0;?>
    <?php foreach ($items as $item) {?>
        <div class="datarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>">
            <div class="weeknum"><?=$item['date']?></div>
            <div class="weekday">
                <?=$item['mon_total']?>
            </div>
            <div class="weekday">
                <?=$item['tue_total']?>
            </div>
            <div class="weekday">
                <?=$item['wed_total']?>
            </div>
            <div class="weekday">
                <?=$item['thu_total']?>
            </div>
            <div class="weekday">
                <?=$item['fri_total']?>
            </div>
            <div class="weekday">
                <?=$item['sat_total']?>
            </div>
            <div class="weekday">
                <?=$item['sun_total']?>
            </div>
        </div>
        <?php $nrow++?>
    <?php } ?>
<?php } ?>