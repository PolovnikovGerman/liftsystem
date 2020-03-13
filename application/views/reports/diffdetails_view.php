<div class="differencesshowdetails">
    <div class="title">Quarter <?=$qt?></div>
    <div class="totalarea">
        <div class="yeardata">
            <div class="labeltxt"><?=$start?></div>
            <div class="value">
                <?php if ($profit=='Profit') { ?>
                    <?=MoneyOutput($startval,0)?>
                <?php } else { ?>
                    <?=($startval)?>qt
                <?php } ?>
            </div>
        </div>
        <div class="yeardata">
            <div class="labeltxt"><?=$finish?></div>
            <div class="value">
                <?php if ($profit=='Profit') { ?>
                    <?=MoneyOutput($finishval,0)?>
                <?php } else { ?>
                    <?=($finishval)?>qt
                <?php } ?>
            </div>
        </div>
    </div>
</div>
