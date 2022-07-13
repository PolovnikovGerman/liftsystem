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
                <div class="itemprice_discount">R40</div>
                <div class="itemprice_discount_separat">&nbsp;</div>
            </div>
            <div class="content-row">
                <div class="itemprice_title">Net:</div>
                <?php foreach ($prices as $price) { ?>
                    <div class="itemprice_new"><?=$price['sale_price']?></div>
                <?php } ?>
            </div>
        </div>
        <div class="itemprice_extraarea">
            <div class="itemprice_extra">
                <div class="itemprice_extratitle">Add'l Prints:</div>
                <div class="itemprice_extraprice"><?=empty($item['item_price_print']) ? '' : $item['item_price_print']?></div>
                <div class="itemprice_extradisc">V20</div>
                <div class="itemprice_extrasale"><?=empty($item['item_sale_print']) ? '' : $item['item_sale_print']?></div>
            </div>
            <div class="itemprice_extra">
                <div class="itemprice_extratitle">New Setup:</div>
                <div class="itemprice_extraprice"><?=empty($item['item_price_setup']) ? '' : $item['item_price_setup']?></div>
                <div class="itemprice_extradisc">V20</div>
                <div class="itemprice_extrasale"><?=empty($item['item_sale_setup']) ? '' : $item['item_sale_setup']?></div>
            </div>
            <div class="itemprice_extra repeatsetup">
                <div class="itemprice_extratitle">Repeat Setup:</div>
                <div class="itemprice_extraprice"><?=empty($item['item_price_setup']) ? '' : $item['item_price_setup']?></div>
                <div class="itemprice_extradisc">V20</div>
                <div class="itemprice_extrasale"><?=empty($item['item_sale_setup']) ? '' : $item['item_sale_setup']?></div>
            </div>
        </div>
        <div class="itemprice_rusharea">
            <div class="itemprice_rush">
                <div class="itemprice_rushtitle">Rush 1</div>
                <div class="itemprice_rushprice">60.00</div>
                <div class="itemprice_rushtdisc">V20</div>
                <div class="itemprice_rushsale">48.00</div>
            </div>
            <div class="itemprice_rush">
                <div class="itemprice_rushtitle">Rush 2</div>
                <div class="itemprice_rushprice">100.00</div>
                <div class="itemprice_rushtdisc">V20</div>
                <div class="itemprice_rushsale">96.00</div>
            </div>
        </div>
        <div class="itemprice_pantonearea">
            <div class="itemprice_pantonetitle">Pantone Match</div>
            <div class="itemprice_pantoneprice">35.00</div>
            <div class="itemprice_pantonedisc">V20</div>
            <div class="itemprice_pantonesale">28.00</div>
        </div>
        <div class="itemprice_profit_separator">&nbsp;</div>
        <div class="itemprice_profitqtyarea">
            <div class="content-row">
                <div class="itemprice_profittitle">Profit:</div>
                <?php foreach ($prices as $price) { ?>
                    <div class="itemprice_profitval"><?=empty($price['profit']) ? '' : MoneyOutput($price['profit'])?></div>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="itemprice_profittitle">%:</div>
                <?php foreach ($prices as $price) { ?>
                    <div class="itemprice_profitperc <?=$price['profit_class']?>"><?=empty($price['profit_perc']) ? '' : $price['profit_perc'].'%'?></div>
                <?php } ?>
            </div>
        </div>
        <div class="itemprice_profitextraarea">
            <div class="content-row">
                <div class="itemprice_profitval"><?=empty($item['profit_print']) ? '' : MoneyOutput($item['profit_print'])?></div>
                <div class="itemprice_profitval"><?=empty($item['profit_setup']) ? '' : MoneyOutput($item['profit_setup'])?></div>
                <div class="itemprice_profitval">$3</div>
            </div>
            <div class="content-row">
                <div class="itemprice_profitperc <?=$item['profit_print_class']?>"><?=empty($item['profit_print_perc']) ? '' : $item['profit_print_perc'].'%'?></div>
                <div class="itemprice_profitperc <?=$item['profit_setup_class']?>"><?=empty($item['profit_setup_perc']) ? '' : $item['profit_setup_perc'].'%'?></div>
                <div class="itemprice_profitperc orangeprofit">25%</div>
            </div>
        </div>
        <div class="itemprice_profitrusharea">
            <div class="content-row">
                <div class="itemprice_profitval">$23</div>
                <div class="itemprice_profitval">($4)</div>
            </div>
            <div class="content-row">
                <div class="itemprice_profitperc greenprofit">48%</div>
                <div class="itemprice_profitperc blackprofit">-4%</div>
            </div>
        </div>
        <div class="itemprice_profitpantonearea">
            <div class="content-row">
                <div class="itemprice_profitval">$3</div>
            </div>
            <div class="content-row">
                <div class="itemprice_profitperc redprofit">11%</div>
            </div>
        </div>
    </div>
</div>
