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
        <div class="psctable-totalbox <?=$calendar['readyweek']==1 ? '' : ''?>"> <!-- readyweek -->
            <!-- Printed -->
            <div class="totalbox-titlerow">
                <div class="totalbox-titlename">&nbsp;</div>
                <div class="totalbox-orders">Orders</div>
                <div class="totalbox-prints">Prints</div>
            </div>
            <div class="totalbox-ready">
                <?php if ($calendar['orders_ready']+$calendar['prints_ready']==0) : ?>
                    <div class="totalbox-titlename">&nbsp;</div>
                <?php else : ?>
                    <div class="totalbox-titlename">Printed</div>
                    <div class="totalbox-orders"><?=$calendar['orders_ready']==0 ? '-' : QTYOutput($calendar['orders_ready'])?></div>
                    <div class="totalbox-prints"><?=$calendar['prints_ready']==0 ? '-' : QTYOutput($calendar['prints_ready'])?></div>
                <?php endif; ?>
            </div>
            <?php if ($calendar['readyweek']==0) : ?>
            <div class="totalbox-toprint">
                <div class="totalbox-titlename">To Print</div>
                <div class="totalbox-orders"><?=$calendar['orders_print']==0 ? '-' : QTYOutput($calendar['orders_print'])?></div>
                <div class="totalbox-prints"><?=$calendar['prints_print']==0 ? '-' : QTYOutput($calendar['prints_print'])?></div>
            </div>
            <?php endif; ?>
        </div>

    </div>
<?php endforeach; ?>