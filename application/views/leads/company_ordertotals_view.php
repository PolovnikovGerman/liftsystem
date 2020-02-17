<div class="cmpusrorderviewarea">
    <div class="cmpusrorderviewtitle">
        <div class="orders"># Orders</div>
        <div class="revenue">Revenue</div>
        <div class="points">Points</div>
    </div>
    <div class="cmpusrorderviewdatatotals">
        <div class="totallabel">Total:</div>
        <div class="orderstotals"><?=$totals['cnt']?></div>
        <div class="revenuetotals"><?=$totals['revenue']?></div>
        <div class="pointstotals"><?=$totals['points']?></div>
    </div>
    <div class="cmpusrorderviewdata">
        <div class="total">Regular:</div>
        <div class="orders"><?=$regular['cnt']?></div>
        <div class="revenue"><?=$regular['revenue']?></div>
        <div class="points"><?=$regular['points']?></div>
    </div>
    <div class="cmpusrorderviewdata">
        <div class="total">Custom:</div>
        <div class="orders"><?=$customs['cnt']?></div>
        <div class="revenue"><?=$customs['revenue']?></div>
        <div class="points"><?=$customs['points']?></div>
    </div>
</div>