<?php $ncel=0;?>
<?php $nweek=intval($data_results[0]['week']);?>
<div class="line-week">
<?php foreach ($data_results as $row) { ?>
    <?php if (intval($row['week'])!=$nweek) {?>
    <div class="cell-weekinfo <?=$weeks_results[$nweek]['profit_class']?> <?=$weeks_results[$nweek]['shipping_class']?>"
    <?=$weeks_results[$nweek]['shipping_class']=='' ? '' : 'data-bgcolor="#fff" data-position="right" data-event="hover" data-bordercolor="#000" data-textcolor="#000" data-balloon="'.$weeks_results[$nweek]['shipping_view'].'"' ?>>
            <div class="cell-weekinfo-profitperc">                                
                <div class="cell-weekinfo-profitperc-value">
                    <?=$weeks_results[$nweek]['profit_perc']?>
                </div>
            </div>
            <div class="cell-weekinfo-data">
                <div class="cell-weekinfo-data-profit <?=$weeks_results[$nweek]['profitdata_class']?>"><?=$weeks_results[$nweek]['profit']?></div>
                <div class="cell-weekinfo-data-orders"><?=$weeks_results[$nweek]['orders']?></div>
                <div class="cell-weekinfo-data-profit"><?=$weeks_results[$nweek]['revenue']?></div>
            </div>
        </div>    
        <?php $nweek=intval($row['week']);?>
        </div>
        <div class="line-week">
    <?php }  ?>
    <div class="cell-dayinfo <?=$row['day_class']?>" data-bgcolor="#f0f0f0" data-addclose="false" data-event="click" data-bordercolor="#999" data-textcolor="#333" data-balloon= "{ajax} /accounting/dayresults/?day=<?=$row['curdate']?>&brand=<?=$brand?>" style="">
        <div class="cell-dayinfo-profitperc">
            <div class="cell-dayinfo-day"><?=$row['day']?></div>
            <div class="cell-dayinfo-profitval <?=$row['profit_class']?>">
                <?=$row['profit_perc']?>
            </div>
        </div>
        <div class="cell-dayinfo-profdat">
            <div class="cell-dayinfo-profval <?=$row['profitval_class']?>"><?=$row['profit']?></div>
            <div class="cell-dayinfo-orders"><?=$row['orders']?></div>
            <div class="cell-dayinfo-revenue"><?=$row['revenue']?></div>
        </div>
    </div>
<?php } ?>
    <div class="cell-weekinfo <?=$weeks_results[$nweek]['profit_class']?> <?=$weeks_results[$nweek]['shipping_class']?>"
        <?=$weeks_results[$nweek]['shipping_class']=='' ? '' : 'data-bgcolor="#fff" data-position="right" data-event="hover" data-bordercolor="#000" data-textcolor="#000" data-balloon="'.$weeks_results[$nweek]['shipping_view'].'"' ?>>
        <div class="cell-weekinfo-profitperc">                                
            <div class="cell-weekinfo-profitperc-value">
                <?=$weeks_results[$nweek]['profit_perc']?>
            </div>
        </div>
        <div class="cell-weekinfo-data">
            <div class="cell-weekinfo-data-profit <?=$weeks_results[$nweek]['profitdata_class']?>"><?=$weeks_results[$nweek]['profit']?></div>
            <div class="cell-weekinfo-data-orders"><?=$weeks_results[$nweek]['orders']?></div>
            <div class="cell-weekinfo-data-profit"><?=$weeks_results[$nweek]['revenue']?></div>
        </div>
    </div>    
</div>
