<div class="vendorqtypricesarea">
    <div class="content-row">
        <div class="vendorpricetitleqty">Qty:</div>
        <div class="vendorqtypricetitlearea">
            <div class="vendorqtypricetitle minprice">min</div>
            <?php foreach ($vendor_prices as $vendor_price) { ?>
                <div class="vendorqtypricetitle"><?= $vendor_price['vendorprice_qty'] ?></div>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricetitle">Blank:</div>
        <div class="vendorqtypriceqty"><?= empty($venditem['vendor_item_blankcost']) ? '' : $venditem['vendor_item_blankcost'] ?></div>
        <?php foreach ($vendor_prices as $vendor_price) { ?>
            <div class="vendorqtypriceqty"><?= $vendor_price['vendorprice_val'] ?></div>
        <?php } ?>
    </div>
    <div class="content-row">
        <div class="vendorpricetitle">1 Print:</div>
        <div class="vendorqtypriceval"><?= empty($venditem['vendor_item_cost']) ? '' : $venditem['vendor_item_cost'] ?></div>
        <?php foreach ($vendor_prices as $vendor_price) { ?>
            <div class="vendorqtypriceval"><?= $vendor_price['vendorprice_color'] ?></div>
        <?php } ?>
    </div>
</div>
<div class="vendorextrapricearea">
    <div class="vendorextraprice">
        <div class="vendorextrapricetitle">Add'l Prints:</div>
        <div class="vendorextrapricevalue"><?= empty($venditem['vendor_item_exprint']) ? '' : $venditem['vendor_item_exprint'] ?></div>
    </div>
    <div class="vendorextraprice">
        <div class="vendorextrapricetitle">New Setup:</div>
        <div class="vendorextrapricevalue"><?= empty($venditem['vendor_item_setup']) ? '' : $venditem['vendor_item_setup'] ?></div>
    </div>
    <div class="vendorextraprice repeatsetup">
        <div class="vendorextrapricetitle">Repeat Setup:</div>
        <div class="vendorextrapricevalue"><?= empty($venditem['vendor_item_repeat']) ? '' : $venditem['vendor_item_repeat'] ?></div>
    </div>
</div>
<div class="vendorrusharea">
    <div class="vendorstandrush">
        <div class="vendorrushtitle">Stand:</div>
        <div class="vendorrushterm"><?=$item['item_lead_a']?></div>
            <!-- = $venditem['stand_days'] -->
        <div class="vendorrushtermtitle">biz days</div>
    </div>
    <div class="vendorrush1rush">
        <div class="vendorrushtitle">Rush 1:</div>
        <div class="vendorrushterm"><?=$item['item_lead_b']?></div>
            <!-- $venditem['rush1_days'] -->
        <div class="vendorrushtermtitle">biz days</div>
        <div class="vendorrushprice"><?= $venditem['rush1_price'] ?></div>
    </div>
    <div class="vendorrush2rush">
        <div class="vendorrushtitle">Rush 2:</div>
        <div class="vendorrushterm"><?=$item['item_lead_c']?></div>
            <!-- $venditem['rush2_days'] -->
        <div class="vendorrushtermtitle">biz days</div>
        <div class="vendorrushprice"><?= $venditem['rush2_price'] ?></div>
    </div>
</div>
<div class="vendorpantonearea">
    <div class="vendorpantonetitle">Pantone Match</div>
    <div class="vendorpantoneprice"><?= $venditem['pantone_match'] ?></div>
</div>
