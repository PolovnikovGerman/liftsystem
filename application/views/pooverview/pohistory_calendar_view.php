<?php foreach ($datas as $data) : ?>
    <?php $week = $data['week']; ?>
    <div class="pohcald-tr">
        <div class="pohcald-td week">
            <div class="pohcald-date datarow"><?=$week['total']['title']?></div>
            <div class="pohcald-totalprice datarow"><?=TotalOutput($week['total']['amount'])?></div>
            <div class="pohcald-pototal datarow"><?=$week['total']['orders']?> POs</div>
            <div class="pohcald-pototalinf datarow">(<?=$week['total']['regular']?> R, <?=$week['total']['custom']?> C)</div>
        </div>
        <?php $days = $week['days']; ?>
        <?php foreach ($days as $day) : ?>
            <div class="pohcald-td historycalendday <?=$day['class']?>" data-dayweek="<?=$day['date']?>">
                <div class="pohcald-date datarow"><?=$day['title']?></div>
                <div class="pohcald-totalprice datarow"><?=TotalOutput($day['amount'])?></div>
                <div class="pohcald-pototal datarow"><?=$day['orders']?> POs</div>
                <div class="pohcald-pototalinf datarow">(<?=$day['regular']?> R, <?=$day['custom']?> C)</div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
