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
    </div>
<?php endforeach; ?>