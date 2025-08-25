<?php foreach ($calendars as $calendar) : ?>
    <?php $weeks = $calendar['week']; ?>
    <div class="psctable-tr">
    <?php foreach ($weeks as $week) : ?>
        <div class="psctable-td" data-printdate="<?=$week['date']?>">
            <div class="psctable-daybox <?=$week['active']==1 ? '' : 'pastday'?> <?=$week['weekend']==1 ? 'dayoff' : ''?>">
                <div class="daybox-date">
                    <div class="dayboxdate-month"><?=$week['month']?></div>
                    <div class="dayboxdate-date"><?=$week['day']?></div>
                </div>
                <?php if ($week['active']==1) : ?>
                    <div class="daybox-orders">
                        <div class="dayboxorders-name">Orders:</div>
                        <div class="dayboxorders-numbers"><?=$week['orders']==0 ? '-' : $week['orders']?></div>
                    </div>
                    <div class="daybox-prints">
                        <div class="dayboxprints-name">Prints:</div>
                        <div class="dayboxprints-numbers"><?=$week['prints']==0 ? '-' : $week['prints']?></div>
                    </div>
                <?php else : ?>
                    <div class="daybox-itemsprinted">
                        <div class="dayboxitemsprinted-name">Items Printed:</div>
                        <div class="dayboxitemsprinted-numbers"><?=$week['prints']==0 ? '-' : $week['prints']?></div>
                    </div>
                    <div class="daybox-printsprinted">
                        <div class="dayboxprintsprinted-name">Prints Printed:</div>
                        <div class="dayboxprintsprinted-numbers"><?=$week['printed']==0 ? '-' : $week['printed']?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
        <!-- Week results -->
        <div class="psctable-td">
            <div class="psctable-totalbox">
                <div class="totalbox-printed">
                    <div class="totalboxtprinted-name">Printed:</div>
                    <div class="totalboxtprinted-numbers"><?=$calendar['total_printed']==0 ? '-' : QTYOutput($calendar['total_printed'])?></div>
                </div>
                <div class="totalbox-orders">
                    <div class="totalboxorders-name">Orders:</div>
                    <div class="totalboxorders-numbers"><?=$calendar['total_orders']==0 ? '-' : QTYOutput($calendar['total_orders'])?></div>
                </div>
                <div class="totalbox-toprint">
                    <div class="totalboxtoprint-name">To Print:</div>
                    <div class="totalboxtoprint-numbers"><?=$calendar['total_toprint']==0 ? '-' : QTYOutput($calendar['total_toprint'])?></div>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>