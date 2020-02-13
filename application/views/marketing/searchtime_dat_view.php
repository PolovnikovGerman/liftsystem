<?php if (count($dat)==0) {?>
    <div class="datarow whitedatarow emptyresults">No Searchs</div>
<?php } else { ?>
    <?php $nrow=0;?>
    <?php foreach ($dat as $row) {?>
        <div class="datarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>">
            <div class="weekdates"><?=$row['date']?></div>
            <div class="weekday">
                <em><?=$row['mon_good']?> R /</em>
                <em class="negativeresult"><?=$row['mon_bad']?>  N</em>
            </div>
            <div class="weekday">
                <em><?=$row['tue_good']?> R / </em>
                <em class="negativeresult"><?=$row['tue_bad']?> N</em>
            </div>
            <div class="weekday">
                <em><?=$row['wed_good']?> R / </em>
                <em class="negativeresult"><?=$row['wed_bad']?> N</em>
            </div>
            <div class="weekday">
                <em><?=$row['thu_good']?> R / </em>
                <em class="negativeresult"><?=$row['thu_bad']?> N</em>
            </div>
            <div class="weekday">
                <em><?=$row['fri_good']?> R / </em>
                <em class="negativeresult"><?=$row['fri_bad']?> N</em>
            </div>
            <div class="weekday">
                <em><?=$row['sat_good']?> R / </em>
                <em class="negativeresult"><?=$row['sat_bad']?> N</em>
            </div>
            <div class="weekdayend">
                <em><?=$row['sun_good']?> R / </em>
                <em class="negativeresult"><?=$row['sun_bad']?> N</em>
            </div>
        </div>
        <?php $nrow++?>
    <?php } ?>
<?php } ?>

