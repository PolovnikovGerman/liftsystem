<?php foreach ($totals as $total) : ?>
    <div class="week-tr" <?=$total['showdata']==0 ? 'style="display: none"' : '' ?> data-week="<?=$total['weeknum']?>">
        <div class="psctable-td">
            <div class="psctable-totalbox <?=$total['readyweek']==1 ? 'readyweek' : ''?>">
                <!-- Printed -->
                <div class="totalbox-printed">
                    <div class="totalboxtprinted-name">Prints Printed:</div>
                    <div class="totalboxtprinted-numbers"><?=$total['total_prints']==0 ? '-' : QTYOutput($total['total_prints'])?></div>
                </div>
                <div class="totalbox-printed">
                    <div class="totalboxtprinted-name">Items Printed:</div>
                    <div class="totalboxtprinted-numbers"><?=$total['total_items']==0 ? '-' : QTYOutput($total['total_items'])?></div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
