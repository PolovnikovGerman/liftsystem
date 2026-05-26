<div class="statisticsbox">
    <div class="statisticsboxtitle">Printed in <?=$year?>:</div>
    <div class="statistics-box">
        <div class="statisticsbox-orders">
            <div class="statisticsbox-name">Orders:</div>
            <div class="statisticsbox-numbers"><?=$total_orders==0 ? '-' : QTYOutput($total_orders)?></div>
        </div>
        <div class="statisticsbox-items">
            <div class="statisticsbox-name">Items:</div>
            <div class="statisticsbox-numbers"><?=$total_items==0 ? '-' : QTYOutput($total_items)?></div>
        </div>
        <div class="statisticsbox-prints">
            <div class="statisticsbox-name">Total Prints:</div>
            <div class="statisticsbox-numbers"><?=$total_prints==0 ? '-' : QTYOutput($total_prints)?></div>
        </div>
    </div>
</div>
<div class="statisticsbox">
    <div class="statisticsboxtitle">Still to Print:</div>
    <div class="statistics-box">
        <div class="statisticsbox-orders">
            <div class="statisticsbox-name">Orders:</div>
            <div class="statisticsbox-numbers"><?=$leave_orders==0 ? '-' : QTYOutput($leave_orders)?></div>
        </div>
        <div class="statisticsbox-items">
            <div class="statisticsbox-name">Items:</div>
            <div class="statisticsbox-numbers"><?=$leave_items==0 ? '-' : QTYOutput($leave_items)?></div>
        </div>
        <div class="statisticsbox-prints">
            <div class="statisticsbox-name">Total Prints:</div>
            <div class="statisticsbox-numbers"><?=$leave_prints==0 ? '-' : QTYOutput($leave_prints)?></div>
        </div>
    </div>
</div>
