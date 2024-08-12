<input type="hidden" id="printschpastopen" value="1"/>
<input type="hidden" id="printschontimeopen" value="1"/>
<input type="hidden" id="printschbrand" value="<?=$brand?>"/>
<div class="body-page">
    <div class="top-block">
        <div class="designations">
            <ul>
                <li><span><img class="img-skull" src="/img/printscheduler/icon-skull.svg"></span>Out of Stock</li>
                <li><span><img class="img-rush" src="/img/printscheduler/icon-rush.svg"></span>Rush</li>
                <li><span class="not_approved">&nbsp;</span>Not Approved</li>
                <li><span class="purpulbox">&nbsp;</span>Must Ship by Date</li>
            </ul>
        </div>
    </div>
    <div class="left-block">
        <div class="pastdue open">
            <div class="pastdue-title">
                <h4>PAST DUE ORDERS:</h4>
                <div class="pastdue-summary">
                    <span id="printschpastdueprints"><?=$old_prints?></span> prints,
                    <span id="printschpastdueitems"><?=$old_items?></span> items,
                    <span id="printschpastdueorders"><?=$old_orders?></span> orders
                </div>
                <div class="arrow-hide" id="printschpastorderview">
                    <img class="chevron-up" src="/img/printscheduler/chevron-up-white.svg">
                </div>
            </div>
            <div class="pastdue-body"></div>
        </div>
        <div class="current open">
            <div class="current-title">
                <h4>ON TIME ORDERS:</h4>
                <div class="current-summary">
                    <span id="printschcurrentprints"><?=$new_prints?></span> prints,
                    <span id="printschcurrentitems"><?=$new_items?></span> items,
                    <span id="printschcurrentorders"><?=$new_orders?></span> orders
                </div>
                <div class="arrow-hide" id="printschcurrentorderview">
                    <img class="chevron-up" src="/img/printscheduler/chevron-up-white.svg">
                </div>
            </div>
            <div id="printschcurrentbody"></div> <!-- class="day-block-open" -->
        </div>
    </div>
    <div class="right-block">
    </div>
</div>
