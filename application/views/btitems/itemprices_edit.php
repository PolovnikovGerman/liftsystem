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
    <div class="sectionbody">
        <div class="itempricearea">
            <div class="content-row">
                <div class="itemprice_title">Qty:</div>
                <div class="itemprice_qtyarea">
                    <?php foreach ($prices as $price) { ?>
                        <div class="itemprice_qty">
                            <input type="text" class="priceinpt" data-item="item_qty" data-price="<?=$price['promo_price_id']?>" value="<?=$price['item_qty']?>"/>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="content-row">
                <div class="itemprice_title">Price:</div>
                <?php foreach ($prices as $price) { ?>
                    <div class="itemprice_pub editmode">
                        <input type="text" class="priceinpt" data-item="price" data-price="<?=$price['promo_price_id']?>" value="<?=$price['price']?>"/>
                    </div>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="itemprice_title">Sale:</div>
                <?php foreach ($prices as $price) { ?>
                    <div class="itemprice_pub editmode">
                        <input type="text" class="priceinpt" data-item="sale_price" data-price="<?=$price['promo_price_id']?>" value="<?=$price['sale_price']?>"/>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="itemprice_extraarea">
            <div class="itemprice_extra">
                <div class="itemprice_extratitle">Add'l Prints:</div>
                <div class="itemprice_extraprice editmode">
                    <input type="text" class="itempriceinpt" data-item="item_price_print" value="<?=$item['item_price_print']?>"/>
                </div>
                <div class="itemprice_extrasale" data-item="item_sale_print">
                    <input type="text" class="itempriceinpt" data-item="item_sale_print" value="<?=$item['item_sale_print']?>"/>
                </div>
            </div>
            <div class="itemprice_extra">
                <div class="itemprice_extratitle">New Setup:</div>
                <div class="itemprice_extraprice editmode">
                    <input type="text" class="itempriceinpt" data-item="item_price_setup" value="<?=$item['item_price_setup']?>"/>
                </div>
                <div class="itemprice_extrasale" data-item="item_sale_setup">
                    <input type="text" class="itempriceinpt" data-item="item_sale_setup" value="<?=$item['item_sale_setup']?>"/>
                </div>
            </div>
            <div class="itemprice_extra repeatsetup">
                <div class="itemprice_extratitle">Repeat Setup:</div>
                <div class="itemprice_extraprice editmode">
                    <input type="text" class="itempriceinpt" data-item="item_price_repeat" value="<?=$item['item_price_repeat']?>"/>
                </div>
                <div class="itemprice_extrasale" data-item="item_sale_repeat">
                    <input type="text" class="itempriceinpt" data-item="item_sale_repeat" value="<?=$item['item_sale_repeat']?>"/>
                </div>
            </div>
        </div>
        <div class="itemprice_rusharea">
            <div class="itemprice_rush">
                <div class="itemprice_rushtitle">Rush 1</div>
                <div class="itemprice_rushprice editmode">
                    <input type="text" class="itempriceinpt" data-item="item_price_rush1" value="<?=$item['item_price_rush1']?>"/>
                </div>
                <div class="itemprice_rushsale" data-item="item_sale_rush1">
                    <input type="text" class="itempriceinpt" data-item="item_sale_rush1" value="<?=$item['item_sale_rush1']?>"/>
                </div>
            </div>
            <div class="itemprice_rush">
                <div class="itemprice_rushtitle">Rush 2</div>
                <div class="itemprice_rushprice editmode">
                    <input type="text" class="itempriceinpt" data-item="item_price_rush2" value="<?=$item['item_price_rush2']?>"/>
                </div>
                <div class="itemprice_rushsale" data-item="item_sale_rush2">
                    <input type="text" class="itempriceinpt" data-item="item_sale_rush2" value="<?=$item['item_sale_rush2']?>"/>
                </div>
            </div>
        </div>
        <div class="itemprice_pantonearea">
            <div class="itemprice_pantonetitle">Pantone Match</div>
            <div class="itemprice_pantoneprice editmode">
                <input type="text" class="itempriceinpt" data-item="item_price_pantone" value="<?=$item['item_price_pantone']?>"/>
            </div>
            <div class="itemprice_pantonesale" data-item="item_sale_pantone">
                <input type="text" class="itempriceinpt" data-item="item_sale_pantone" value="<?=$item['item_sale_pantone']?>"/>
            </div>
        </div>
        <div class="itemprice_profit_separator">&nbsp;</div>
        <div id="profitdataarea">
            <?=$profit_view?>
        </div>
    </div>
</div>
