<div class="goaleditarea">
    <div class="goaledittitle"><?=$goal_year?> Goals</div>
    <div class="goaleditrow">
        <div class="goaleditlabel"><?=$goal_year?> Orders:</div>
        <div class="goaleditvalue">
            <input type="text" id="goal_orders" class="goaleditinput" value="<?=$goal_orders?>"/>
        </div>
    </div>
    <div class="goaleditrow">
        <div class="goaleditlabel"><?=$goal_year?> Revenue:</div>
        <div class="goaleditvalue">
            <input type="text" id="goal_revenue" class="goaleditinput" value="<?=$goal_revenue?>"/>
        </div>
    </div>
    <div class="goaleditrow">
        <div class="goaleditlabel"><?=$goal_year?> Revenue:</div>
        <div class="goaleditvalue" data-fld="goalavgrevenue"><?=$goal_avgrevenue?></div>
    </div>
    <div class="goaleditrow">
        <div class="goaleditlabel"><?=$goal_year?> Profit:</div>
        <div class="goaleditvalue">
            <input type="text" id="goal_profit" class="goaleditinput" value="<?=$goal_profit?>"/>
        </div>
    </div>
    <div class="goaleditrow">
        <div class="goaleditlabel"><?=$goal_year?> Avg Profit:</div>
        <div class="goaleditvalue" data-fld="goalavgprofit"><?=$goal_avgprofit?></div>
    </div>
    <div class="goaleditrow">
        <div class="goaleditlabel"><?=$goal_year?> Average %:</div>
        <div class="goaleditvalue" data-fld="goalavgprofitperc"><?=$goal_avgprofit_perc?></div>
    </div>
    <div class="goaleditrow">
        <div class="goaleditsave">
            <img src="/img/reports/saveticket.png" alt="Save"/>
        </div>
    </div>
</div>
