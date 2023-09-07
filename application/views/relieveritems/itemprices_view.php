<div class="relievers_itemprices">
    <div class="sectionlabel">Pricing:</div>
    <div class="itemprice_legendarea">
        <div class="itemprice_legend">
            <img src="/img/itemdetails/profit_50.png"/>
            <span>50% +</span>
        </div>
        <div class="itemprice_legend">
            <img src="/img/itemdetails/profit_40.png"/>
            <span>40%'s</span>
        </div>
        <div class="itemprice_legend">
            <img src="/img/itemdetails/profit_30.png"/>
            <span>30%'s</span>
        </div>
        <div class="itemprice_legend">
            <img src="/img/itemdetails/profit_20.png"/>
            <span>20%'s</span>
        </div>
        <div class="itemprice_legend">
            <img  src="/img/itemdetails/profit_10.png"/>
            <span>10%'s</span>
        </div>
        <div class="itemprice_legend">
            <img src="/img/itemdetails/profit_0.png"/>
            <span>0%'s</span>
        </div>
        <div class="itemprice_legend">
            <img src="/img/itemdetails/profit_negat.png"/>
            <span>Loss</span>
        </div>
    </div>
    <div class="sectionbody <?=$missinfo==0 ? '' : 'missinginfo'?>">
        <div class="itempricearea">
            <div class="content-row">
                <div class="itemprice_title">Qty:</div>
                <div class="itemprice_qtyarea">
                    <?php foreach ($prices as $price) { ?>
                        <div class="itemprice_qty"><?=$price['item_qty']?></div>
                    <?php } ?>
                </div>
            </div>
            <div class="content-row">
                <div class="itemprice_title">Publish:</div>
                <?php foreach ($prices as $price) { ?>
                    <div class="itemprice_pub"><?=$price['price']?></div>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="itemprice_title">Disc:</div>
                <div class="itemprice_discount">
                    <select class="discountselect" disabled>
                        <option value=""></option>
                        <?php foreach ($discounts as $discount) { ?>
                            <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['price_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemprice_discount_separat">&nbsp;</div>
            </div>
            <div class="content-row">
                <div class="itemprice_title">Net:</div>
                <div id="netpricesarea">
                    <?=$netprices?>
                </div>
            </div>
        </div>
        <div class="itemprice_extraarea">
            <div class="itemprice_extra">
                <div class="itemprice_extratitle">Add'l Prints:</div>
                <div class="itemprice_extraprice"><?=empty($item['item_price_print']) ? '' : $item['item_price_print']?></div>
                <div class="itemprice_extradisc">
                    <select class="discountselect" disabled>
                        <option value=""></option>
                        <?php foreach ($discounts as $discount) { ?>
                            <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['print_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemprice_extrasale"><?=empty($item['item_sale_print']) ? '' : $item['item_sale_print']?></div>
            </div>
            <div class="itemprice_extra">
                <div class="itemprice_extratitle">New Setup:</div>
                <div class="itemprice_extraprice"><?=empty($item['item_price_setup']) ? '' : $item['item_price_setup']?></div>
                <div class="itemprice_extradisc">
                    <select class="discountselect" disabled>
                        <option value=""></option>
                        <?php foreach ($discounts as $discount) { ?>
                            <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['setup_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemprice_extrasale"><?=empty($item['item_sale_setup']) ? '' : $item['item_sale_setup']?></div>
            </div>
            <div class="itemprice_extra repeatsetup">
                <div class="itemprice_extratitle">Repeat Setup:</div>
                <div class="itemprice_extraprice"><?=empty($item['item_price_repeat']) ? '' : $item['item_price_repeat']?></div>
                <div class="itemprice_extradisc">
                    <select class="discountselect" disabled>
                        <option value=""></option>
                        <?php foreach ($discounts as $discount) { ?>
                            <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['repeat_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemprice_extrasale"><?=empty($item['item_sale_repeat']) ? '' : $item['item_sale_repeat']?></div>
            </div>
        </div>
        <div class="itemprice_rusharea">
            <div class="itemprice_rush">
                <div class="itemprice_rushtitle">Rush 1</div>
                <div class="itemprice_rushprice"><?=$item['item_price_rush1']?></div>
                <div class="itemprice_rushtdisc">
                    <select class="discountselect" disabled>
                        <option value=""></option>
                        <?php foreach ($discounts as $discount) { ?>
                            <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['rush1_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemprice_rushsale"><?=$item['item_sale_rush1']?></div>
            </div>
            <div class="itemprice_rush">
                <div class="itemprice_rushtitle">Rush 2</div>
                <div class="itemprice_rushprice"><?=$item['item_price_rush2']?></div>
                <div class="itemprice_rushtdisc">
                    <select class="discountselect" disabled>
                        <option value=""></option>
                        <?php foreach ($discounts as $discount) { ?>
                            <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['rush2_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="itemprice_rushsale"><?=$item['item_sale_rush2']?></div>
            </div>
        </div>
        <div class="itemprice_pantonearea">
            <div class="itemprice_pantonetitle">Pantone Match</div>
            <div class="itemprice_pantoneprice"><?=$item['item_price_pantone']?></div>
            <div class="itemprice_pantonedisc">
                <select class="discountselect" disabled>
                    <option value=""></option>
                    <?php foreach ($discounts as $discount) { ?>
                        <option value="<?=$discount['price_discount_id']?>" <?=$discount['price_discount_id']==$item['pantone_discount'] ? 'selected="selected"' : ''?>><?=$discount['discount_label']?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="itemprice_pantonesale"><?=$item['item_sale_pantone']?></div>
        </div>
        <div class="itemprice_profit_separator">&nbsp;</div>
        <div id="profitdataarea">
            <?=$profit_view?>
        </div>
    </div>
</div>
