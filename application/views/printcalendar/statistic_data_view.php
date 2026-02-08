<div class="statistics-tbl">
    <div class="statistics-tr statistics-tbltitle">
        <div class="statistics-years">&nbsp;</div>
        <div class="statistics-td orders_prntd">Orders<br>Printed</div>
        <div class="statistics-td items_prntd">Items<br>Printed</div>
        <div class="statistics-td total_prntd">Total<br>Prints</div>
        <div class="statistics-td avg_prntday">Avg<br>Prints/Day</div>
        <div class="statistics-td avg_prntweek">Avg<br>Prints/Week </div>
        <div class="statistics-td best_day">Best<br>Day</div>
        <div class="statistics-td best_week">Best<br>Week</div>
    </div>
    <?php foreach ($totals as $total) : ?>
        <div class="statistics-tr">
            <div class="statistics-years"><?=$total['year']?></div>
            <div class="statistics-prntd">
                <div class="statistics-td orders_prntd"><?=QTYOutput($total['orders'])?></div>
                <div class="statistics-td items_prntd"><?=QTYOutput($total['items'])?></div>
                <div class="statistics-td total_prntd"><?=QTYOutput($total['prints'])?></div>
            </div>
            <div class="statistics-avgbest">
                <div class="statistics-td avg_prntday"><?=QTYOutput($total['avgday'])?></div>
                <div class="statistics-td avg_prntweek"><?=QTYOutput($total['avgweek'])?></div>
                <div class="statistics-td best_day">
                    <div class="bestday_date"><?=date('M, j', $total['maxday_label'])?></div>
                    <div class="bestday_total"><?=QTYOutput($total['maxday'])?></div>
                </div>
                <div class="statistics-td best_week">
                    <div class="bestweek_date"><?=date('M, j', $total['maxweek_bgn'])?> - <?=date('M, j', $total['maxweek_end'])?></div>
                    <div class="bestweek_total"><?=QTYOutput($total['maxweek'])?></div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>