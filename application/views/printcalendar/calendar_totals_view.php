<?php foreach ($totals as $total) : ?>
    <div class="week-tr summaryweek" <?=$total['showdata']==0 ? 'style="display: none"' : '' ?> data-week="<?=$total['weeknum']?>" data-weeknum="<?=$total['weeknumber']?>">
        <div class="psctable-td">
            <div class="psctable-totalbox <?=$total['readyweek']==1 ? 'readyweek' : ''?>">
                <!-- Printed -->
                <div class="totalbox-printed">
                    <div class="totalboxtprinted-name">Prints Printed:</div>
                    <div class="totalboxtprinted-numbers" data-fld="prints"><?=$total['total_prints']==0 ? '-' : QTYOutput($total['total_prints'])?></div>
                </div>
                <div class="totalbox-printed">
                    <div class="totalboxtprinted-name">Items Printed:</div>
                    <div class="totalboxtprinted-numbers" data-fld="items"><?=$total['total_items']==0 ? '-' : QTYOutput($total['total_items'])?></div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
