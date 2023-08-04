<div class="vendorqtypricesarea">
    <div class="content-row">
        <div class="vendorpricetitleqty">Qty:</div>
        <div class="vendorqtypricetitlearea">
            <div class="vendorqtypricetitle minprice">min</div>
            <?php foreach ($vendor_prices as $vendor_price) { ?>
                <div class="vendorqtypricetitle">
                    <input type="text" class="vendorpriceinpt" data-item="vendorprice_qty"
                           data-price="<?= $vendor_price['vendorprice_id'] ?>"
                           value="<?= $vendor_price['vendorprice_qty'] ?>"/>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="content-row">
        <div class="vendorpricetitle">Blank:</div>
        <div class="vendorqtypriceqty editmode">
            <input class="vendordatapriceinpt itemprice" data-item="vendor_item_blankcost"
                   value="<?= $venditem['vendor_item_blankcost'] ?>"/>
        </div>
        <?php foreach ($vendor_prices as $vendor_price) { ?>
            <div class="vendorqtypriceqty editmode">
                <input type="text" class="vendorpriceinpt" data-item="vendorprice_val"
                       data-price="<?= $vendor_price['vendorprice_id'] ?>"
                       value="<?= $vendor_price['vendorprice_val'] ?>"/>
            </div>
        <?php } ?>
    </div>
    <div class="content-row">
        <div class="vendorpricetitle">1 Print:</div>
        <div class="vendorqtypriceval editmode">
            <input class="vendordatapriceinpt itemprice" data-item="vendor_item_cost"
                   value="<?= $venditem['vendor_item_cost'] ?>"/>
        </div>
        <?php foreach ($vendor_prices as $vendor_price) { ?>
            <div class="vendorqtypriceval editmode">
                <input type="text" class="vendorpriceinpt" data-item="vendorprice_color"
                       data-price="<?= $vendor_price['vendorprice_id'] ?>"
                       value="<?= $vendor_price['vendorprice_color'] ?>"/>
            </div>
        <?php } ?>
    </div>
</div>
<div class="vendorextrapricearea">
    <div class="vendorextraprice">
        <div class="vendorextrapricetitle">Add'l Prints:</div>
        <div class="vendorextrapricevalue editmode">
            <input class="vendordatapriceinpt itemprice" data-item="vendor_item_exprint"
                   value="<?= $venditem['vendor_item_exprint'] ?>"/>
        </div>
    </div>
    <div class="vendorextraprice">
        <div class="vendorextrapricetitle">New Setup:</div>
        <div class="vendorextrapricevalue editmode">
            <input class="vendordatapriceinpt itemprice" data-item="vendor_item_setup"
                   value="<?= $venditem['vendor_item_setup'] ?>"/>
        </div>
    </div>
    <div class="vendorextraprice repeatsetup">
        <div class="vendorextrapricetitle">Repeat Setup:</div>
        <div class="vendorextrapricevalue editmode">
            <input class="vendordatapriceinpt itemprice" data-item="vendor_item_repeat"
                   value="<?= $venditem['vendor_item_repeat'] ?>"/>
        </div>
    </div>
</div>
<div class="vendorrusharea">
    <div class="vendorstandrush">
        <div class="vendorrushtitle">Stand:</div>
        <div class="vendorrushterm editmode">
            <input class="itemkeyinfoinput terms" data-item="item_lead_a" value="<?=$item['item_lead_a']?>"/>
            <!-- $venditem['stand_days'] -->
        </div>
        <div class="vendorrushtermtitle">biz days</div>
    </div>
    <div class="vendorrush1rush">
        <div class="vendorrushtitle">Rush 1:</div>
        <div class="vendorrushterm editmode">
            <input class="itemkeyinfoinput terms" data-item="item_lead_b" value="<?=$item['item_lead_b']?>"/>
            <!-- $venditem['rush1_days'] -->
        </div>
        <div class="vendorrushtermtitle">biz days</div>
        <div class="vendorrushprice editmode">
            <input class="vendordatapriceinpt itemprice" data-item="rush1_price"
                   value="<?= $venditem['rush1_price'] ?>"/>
        </div>
    </div>
    <div class="vendorrush2rush">
        <div class="vendorrushtitle">Rush 2:</div>
        <div class="vendorrushterm editmode">
            <input class="itemkeyinfoinput terms" data-item="item_lead_c" value="<?=$item['item_lead_c']?>"/>
            <!--  $venditem['rush2_days'] -->
        </div>
        <div class="vendorrushtermtitle">biz days</div>
        <div class="vendorrushprice editmode">
            <input class="vendordatapriceinpt itemprice" data-item="rush2_price"
                   value="<?= $venditem['rush2_price'] ?>"/>
        </div>
    </div>
</div>
<div class="vendorpantonearea">
    <div class="vendorpantonetitle">Pantone Match</div>
    <div class="vendorpantoneprice editmode">
        <input class="vendordatapriceinpt itemprice" data-item="pantone_match"
               value="<?= $venditem['pantone_match'] ?>"/>
    </div>
</div>
