<div class="netprofit_weekdetails_area">
    <?php foreach ($details as $detail) { ?>
        <div class="datarow">
            <div class="weekdetails_brandname"><?=$detail['brand']?></div>
            <div class="weekdetails_sales"><?=$detail['sales']?></div>
            <div class="weekdetails_revenue"><?=$detail['revenue']?></div>
            <div class="weekdetails_grossprofit"><?=$detail['profit']?></div>
            <div class="weekdetails_profitperc"><?=$detail['profitperc']?></div>
        </div>
    <?php } ?>
</div>