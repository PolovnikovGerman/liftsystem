<?php $nrow = 0; ?>
<?php foreach ($tabledat as $row) { ?>
    <div class="attempts_data_week <?= ($nrow % 2 == 0 ? 'whitedatarow' : 'greydatarow') ?>">
        <div class="attempt_weekdate"><?=$row['date'] ?></div>
        <!--<div class="attempt_dayresult" id="attemptday<?=$row['mon_date']?>" href="/orders/attemtsdue/?day=<?=$row['mon_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['mon_date']?>">
            <div class="attempt_daynumber"><?=$row['mon_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['mon_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['mon_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['mon_success'] ?> </div>
            </div>
        </div>
        <!-- <div class="attempt_dayresult" id="attemptday<?=$row['tue_date']?>" href="/orders/attemtsdue/?day=<?=$row['tue_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['tue_date']?>">
            <div class="attempt_daynumber"><?=$row['tue_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['tue_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['tue_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['tue_success'] ?> </div>
            </div>
        </div>
        <!-- <div class="attempt_dayresult" id="attemptday<?=$row['wed_date']?>" href="/orders/attemtsdue/?day=<?=$row['wed_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['wed_date']?>">
            <div class="attempt_daynumber"><?=$row['wed_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['wed_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['wed_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['wed_success'] ?> </div>
            </div>
        </div>
        <!-- <div class="attempt_dayresult" id="attemptday<?=$row['thu_date']?>" href="/orders/attemtsdue/?day=<?=$row['thu_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['thu_date']?>">
            <div class="attempt_daynumber"><?=$row['thu_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['thu_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['thu_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['thu_success'] ?> </div>
            </div>
        </div>
        <!-- <div class="attempt_dayresult" id="attemptday<?=$row['fri_date']?>" href="/orders/attemtsdue/?day=<?=$row['fri_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['fri_date']?>">
            <div class="attempt_daynumber"><?=$row['fri_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['fri_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['fri_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['fri_success'] ?> </div>
            </div>
        </div>
        <!-- <div class="attempt_dayresult weekend" id="attemptday<?=$row['sat_date']?>" href="/orders/attemtsdue/?day=<?=$row['sat_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['sat_date']?>">
            <div class="attempt_daynumber"><?=$row['sat_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['sat_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['sat_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['sat_success'] ?> </div>
            </div>
        </div>
        <!-- <div class="attempt_dayresult weekend" id="attemptday<?=$row['sun_date']?>" href="/orders/attemtsdue/?day=<?=$row['sun_date']?>"> -->
        <div class="attempt_dayresult" data-attemptday="<?=$row['sun_date']?>">
            <div class="attempt_daynumber"><?=$row['sun_day'] ?></div>
            <div class="attempt_dayinfo">
                <div class="attempt_dayinfo_data"><?=$row['sun_ordercnt'] ?> orders</div>
                <div class="attempt_dayinfo_data"><?=$row['sun_attemcnt'] ?> attempts</div>
                <div class="attempt_dayinfo_data"><?=$row['sun_success'] ?></div>
            </div>
        </div>
        <div class="attempt_totalweek">
            <div class="attempt_dayinfo_data"><?=$row['total_ordercnt'] ?> orders</div>
            <div class="attempt_dayinfo_data"><?=$row['total_attemcnt'] ?> attempts</div>
            <div class="attempt_dayinfo_data"><?=$row['total_success'] ?></div>
        </div>
    </div>
    <?php $nrow++; ?>
<?php } ?>
