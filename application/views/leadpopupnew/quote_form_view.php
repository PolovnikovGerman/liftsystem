<input type="hidden" id="quoteformcustomitem" value="<?=$custom_item?>"/>
<input type="hidden" id="quoteformexpandview" value="0"/>
<div class="leadquotesform-top">
    <div class="qtypricetitle">
        <div class="qtyprice-rowqty">Qty:</div>
        <div class="qtyprice-rowprice">Price Ea:</div>
    </div>
    <div class="qtypricelist">
        <div class="qtypricelist-box" data-price="custom">
            <div class="qtyprice-rowqty">
                <div class="qtyprice-qtybox">
                    <input type="radio" name="pricecheck" value="custom"/>
                    <input class="qtybox-qty" type="text" readonly="readonly" name="qtybox" data-price="custom"/>
                </div>
            </div>
            <div class="qtyprice-rowprice">
                <div class="qtyprice-pricebox">
                    <input class="qtybox-price" type="text" readonly="readonly" name="qtybox" data-price="custom"/>
                </div>
            </div>
        </div>
        <?php foreach ($prices as $price) : ?>
            <div class="qtypricelist-box" data-price="<?=$price['promo_price_id']?>" data-promoqty="<?=$price['item_qty']?>">
                <div class="qtyprice-rowqty">
                    <div class="qtyprice-qtybox">
                        <input type="radio" name="pricecheck" value="<?=$price['promo_price_id']?>">
                        <span><?=short_number($price['item_qty'])?></span>
                    </div>
                </div>
                <div class="qtyprice-rowprice">
                    <div class="qtyprice-pricebox" data-promoprice="<?=$price['sale_price']?>">
                    <?php if ($custom_item) : ?>
                        <input class="qtybox-price" type="text" readonly="readonly" name="qtybox" data-price="<?=$price['promo_price_id']?>" value="<?=MoneyOutput($price['sale_price'])?>"/>
                    <?php else : ?>
                        <?=MoneyOutput($price['sale_price'])?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<div class="leadquotesform-row">
    <div class="quotesform-locs">
        <label>Locs:</label>
        <?php for($i=1; $i<6; $i++) : ?>
            <select class="quoteform-locations inactive" data-location="<?=$i?>" name="imprlocation">
                <?php foreach ($locations as $location ) : ?>
                <option value="<?=$location['key']?>" class="<?=$location['class']?>"><?=$location['value']?></option>
                <?php endforeach; ?>
            </select>
        <?php endfor; ?>
    </div>
    <div class="quotesform-prints">
        <label>Prints:</label>
        <input type="text" name="quotesform-prints" value="<?=$print_price?>"/>
    </div>
    <div class="quotesform-setup">
        <label>Setup:</label>
        <input type="text" name="quotesform-setup" value="<?=$setup_price?>"/>
    </div>
    <?php if ($custom_item) : ?>
        <div class="quotesform-design">
            <label>Design:</label>
            <input type="text" name="quotesform-design" value="<?=$design_price?>">
        </div>
    <?php endif; ?>
</div>
<div class="leadquotesform-row">
    <div class="quotesform-zipcode">
        <label>Zip Code:</label>
        <input type="text" name="quotesform-zipcode" value="<?=$zip?>"/>
    </div>
    <div class="checkbox-discount">
        <input type="checkbox" name="discountcheckbox"/>
    </div>
    <div class="quotesform-discount inactive">
        <input type="text" name="quotesform-discount" readonly="readonly" placeholder="Courtesy Discount" class="discount-code"/>
        <input type="text" name="quotesform-price" readonly="readonly" class="discount-price">
        <label>Exp:</label>
        <input type="text" name="quotesform-exp" class="discount-exp" readonly="readonly"/>
    </div>
    <div class="btn-createquote fixedview">Create Quote</div>
</div>
<div class="messagequote-block" style="display:none;">
    <div class="messagequote-box">
        <textarea class="quoteform_othernotes"></textarea>
    </div>
    <div class="messagequote-box">
        <textarea class="quoteform_repcontact"><?=$quote_repcontact?></textarea>
    </div>
    <div class="btn-createquote">Create Quote</div>
</div>
