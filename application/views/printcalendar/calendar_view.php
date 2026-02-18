<?php foreach ($calendars as $calendar) : ?>
    <?php $weeks = $calendar['week']; ?>
    <div class="week-tr" data-week="<?=$calendar['weeknum']?>">
    <?php foreach ($weeks as $week) : ?>
        <div class="psctable-td" data-printdate="<?=$week['date']?>" data-printweek="<?=$week['week']?>"
                <?=$week['active']==1 ? 'id="caledarbox_'.$week["date"].'" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)"' : ''?>>
            <div class="calnd-daybox <?=$week['active']==1 ? '' : 'pastday'?> <?=$week['weekend']==1 ? 'dayoff' : ''?>">
                <div class="daybox-date">
                    <div class="dayboxdate-month"><?=$week['month']?></div>
                    <div class="dayboxdate-date"><?=$week['day']?></div>
                </div>
                <?php if ($week['active']==1) : ?>
                    <div class="daybox-orders">
                        <div class="dayboxorders-name">Orders:</div>
                        <div class="dayboxorders-numbers"><?=$week['orders']==0 ? '-' : QTYOutput($week['orders'])?></div>
                    </div>
                    <div class="daybox-prints">
                        <div class="dayboxprints-name">Prints:</div>
                        <div class="dayboxprints-numbers"><?=$week['prints']==0 ? '-' : QTYOutput($week['prints'])?></div>
                    </div>
                <?php else : ?>
                    <div class="daybox-itemsprinted">
                        <div class="dayboxitemsprinted-name">Items Printed:</div>
                        <div class="dayboxitemsprinted-numbers"><?=$week['printed']==0 ? '-' : QTYOutput($week['printed'])?></div>
                    </div>
                    <div class="daybox-printsprinted">
                        <div class="dayboxprintsprinted-name">Prints Printed:</div>
                        <div class="dayboxprintsprinted-numbers"><?=$week['prints']==0 ? '-' : QTYOutput($week['prints'])?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
        <div class="psctable-totalbox <?=$calendar['readyweek']==1 ? 'readyweek' : ''?>">
            <!-- Printed -->
            <div class="totalbox-printed">
                <div class="totalboxtprinted-name">Prints Printed:</div>
                <div class="totalboxtprinted-numbers" data-fld="prints"><?=$calendar['total_prints']==0 ? '-' : QTYOutput($calendar['total_prints'])?></div>
            </div>
            <div class="totalbox-printed">
                <div class="totalboxtprinted-name">Items Printed:</div>
                <div class="totalboxtprinted-numbers" data-fld="items"><?=$calendar['total_items']==0 ? '-' : QTYOutput($calendar['total_items'])?></div>
            </div>
        </div>

    </div>
<?php endforeach; ?>