<div class="itemdetails_prices">
    <div class="chapterlabel centerpart">Prices & Profit:</div>
    <div class="priceinfoarea">
        <div class="content-row">
            <div class="pricedatlabel qty">Qty Breaks:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue qty">
                    <?php if ($editmode==0) { ?>
                        <div class="viewparam"><?=empty($price['item_qty']) ? '&nbsp;' : $price['item_qty']?></div>
                    <?php } else { ?>
                        <input type="pricevalinpt" data-item="price_qty" value="<?=$price['item_qty']?>"/>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div class="content-row">
            <div class="price_subtitle">min</div>
        </div>
        <div class="content-row">
            <div class="pricedatlabel price">Price:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue qty">
                    <?php if ($editmode==0) { ?>
                        <div class="viewparam"><?=empty($price['price']) ? '&nbsp;' : $price['price']?></div>
                    <?php } else { ?>
                        <input type="pricevalinpt" data-item="price" value="<?=$price['price']?>"/>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="pricedatlabel specprice">Prints</div>
            <div class="pricedatlabel specprice">Setup</div>
        </div>
        <div class="content-row">
            <div class="pricedatlabel saleprice">Sale Price:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue qty">
                    <?php if ($editmode==0) { ?>
                        <div class="viewparam"><?=empty($price['sale_price']) ? '&nbsp;' : $price['sale_price']?></div>
                    <?php } else { ?>
                        <input type="pricevalinpt" data-item="sale_price" value="<?=$price['sale_price']?>"/>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="pricedatvalue specprice">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=empty($item['item_sale_print']) ? '&nbsp;' : $item['item_sale_print']?></div>
                <?php } else { ?>
                    <input type="pricevalinpt" data-item="item_sale_print" value="<?=$item['item_sale_print']?>"/>
                <?php } ?>
            </div>
            <div class="pricedatvalue specprice">
                <?php if ($editmode==0) { ?>
                    <div class="viewparam"><?=empty($item['item_sale_setup']) ? '&nbsp;' : $item['item_sale_setup']?></div>
                <?php } else { ?>
                    <input type="pricevalinpt" data-item="item_sale_print" value="<?=$item['item_sale_setup']?>"/>
                <?php } ?>
            </div>
        </div>
        <div class="content-row">
            <div class="pricedatlabel startdisplay">Start Display:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue displayprice">
                    <?php if ($price['show_first']==0) { ?>
                        <i class="fa fa-circle-o" aria-hidden="true"></i>
                    <?php } else { ?>
                        <i class="fa fa-check-circle-o" aria-hidden="true"></i>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <!-- <div class="pricehightline"><hr></div> -->
        <div class="content-row">
            <div class="pricedatlabel shipbox"># Ship Boxes:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue shipbox">
                    <?php if ($editmode==0) { ?>
                        <div class="viewparam"><?=empty($price['shipbox']) ? '&nbsp;' : $price['shipbox']?></div>
                    <?php } else { ?>
                        <input type="pricevalinpt" data-item="shipbox" value="<?=$price['shipbox']?>"/>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
        <div class="content-row">
            <div class="pricedatlabel shipweight">Ship Weight:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue shipweight">
                    <?php if ($editmode==0) { ?>
                        <div class="viewparam"><?=empty($price['shipweight']) ? '&nbsp;' : $price['shipweight']?></div>
                    <?php } else { ?>
                        <input type="pricevalinpt" data-item="shipweight" value="<?=$price['shipweight']?>"/>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="priceshiplabel">Higher of reg or dimm wt</div>
        </div>
        <div class="pricehightline"><hr></div>
        <div class="content-row">
            <div class="pricedatlabel profitval">Profit $$:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue qty">
                    <div class="viewparam"><?=empty($price['profit']) ? '&nbsp;' : $price['profit']?></div>
                </div>
            <?php } ?>
            <div class="pricedatlabel specprice">Prints</div>
            <div class="pricedatlabel specprice">Setup</div>
        </div>
        <div class="content-row">
            <div class="pricedatlabel profitval">Profit %:</div>
            <?php foreach ($prices as $price) { ?>
                <div class="pricedatvalue qty">
                    <div class="viewparam <?=$price['profit_class']?>"><?=empty($price['profit_perc']) ? '&nbsp;' : $price['profit_perc']?></div>
                </div>
            <?php } ?>
            <div class="pricedatvalue specprice">
                <div class="viewparam <?=$item['profit_print_class']?>"><?=empty($item['profit_print_perc']) ? '&nbsp;' : $item['profit_print_perc']?></div>
            </div>
            <div class="pricedatvalue specprice">
                <div class="viewparam <?=$item['profit_setup_class']?>"><?=empty($item['profit_setup_perc']) ? '&nbsp;' : $item['profit_setup_perc']?></div>
            </div>
        </div>

    </div>
</div>