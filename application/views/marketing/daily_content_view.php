<?php if (count($items)==0) {?>
    <div class="datarow whitedatarow emptyresults">No Searchs</div>
<?php } else { ?>
    <?php $nrow=0;?>
    <?php foreach ($items as $item) {?>
        <div class="datarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>">
            <div class="weeknum"><?=$item['date']?></div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
                 data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['mon_good']?>&n=<?=$item['mon_bad']?>">
                <?=$item['mon_total']?>
            </div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
                 data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['tue_good']?>&n=<?=$item['tue_bad']?>">
                <?=$item['tue_total']?>
            </div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
            data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['wed_good']?>&n=<?=$item['wed_bad']?>">
                <?=$item['wed_total']?>
            </div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
                 data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['thu_good']?>&n=<?=$item['thu_bad']?>">
                <?=$item['thu_total']?>
            </div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
        data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['fri_good']?>&n=<?=$item['fri_bad']?>">
                <?=$item['fri_total']?>
            </div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
                 data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['sat_good']?>&n=<?=$item['sat_bad']?>">
                <?=$item['sat_total']?>
            </div>
            <div class="weekday" data-event="hover" data-css="weekbrandtotals" data-bgcolor="#ffffff" data-bordercolor="#716f6f" data-textcolor="#716f6f"
        data-position="down" data-timer="6000" data-delay="1000" data-balloon="{ajax} /marketing/daytotals?r=<?=$item['sun_good']?>&n=<?=$item['sun_bad']?>">
                <?=$item['sun_total']?>
            </div>
        </div>
        <?php $nrow++?>
    <?php } ?>
<?php } ?>