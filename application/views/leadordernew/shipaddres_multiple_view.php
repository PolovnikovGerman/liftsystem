<!--<div class="shipdocs multyships">-->
<!--    <div class="shipdocs_txt">Ship Docs:</div>-->
<!--    --><?php //foreach ($shipdocs as $shipdoc) : ?>
<!--        <div class="shipdocs_icons">-->
<!--            <div class="shipdocs_file"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>-->
<!--        </div>-->
<!--    --><?php //endforeach; ?>
<!--</div>-->
<div class="shiptax_bodyleftmultiple" id="shiptax_bodyleftmultiple">
    <?php $numpp = 1; ?>
    <?php foreach ($addresses as $address) : ?>
        <div class="shipaddressarea <?=$numpp%2==0 ? 'whitedatarow' : 'greydatarow'?>" data-shipadr="<?=$address['order_shipaddr_id']?>">
            <div class="numpp"><?=$numpp?></div>
            <div class="shipqty"><?=$address['item_qty']?></div>
            <div class="devider">-</div>
            <div class="shipaddres">
                <span class="text_bold"><?=$address['out_shipping_method']?></span> to <?=$address['out_zip']?>, <?=$address['out_country']?>
            </div>
            <div class="shipcost"><?=MoneyOutput($address['shipping'])?></div>
            <div class="arrivedate"><?=empty($address['arrive_date']) ? '&nbsp;' : '- '.date('D - M d',$address['arrive_date'])?> </div>
        </div>
        <?php $numpp++;?>
    <?php endforeach; ?>
</div>
<div class="shiptax_bodyrightmultiple">
    <div class="shipdocs">
        <div class="shipdocs_txt">Ship Docs:</div>
        <?php foreach ($shipdocs as $shipdoc) : ?>
            <div class="shipdocs_icons">
                <div class="shipdocs_file"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="shiptax_info">
        <div class="infoshiptax_row">
            <div class="infoshiptax_pricebox">
                <?php if ($edit==0) : ?>
                    <?=MoneyOutput($shipping['rush_price'])?>
                <?php else : ?>
                    <input type="text" class="inptpaymentdata" data-fld="rush_price" value="<?=$shipping['rush_price']?>"/>
                <?php endif; ?>
            </div>
            <div class="infoshiptax_title">Production:</div>
        </div>
        <div class="infoshiptax_row">
            <div class="infoshiptax_pricebox">
                <?=MoneyOutput($order['shipping'])?></div>
            <div class="infoshiptax_title">Shipping:</div>
        </div>
        <div class="infoshiptax_row">
            <div class="infoshiptax_pricebox">
                <?=MoneyOutput($address['sales_tax'])?>
            </div>
            <div class="infoshiptax_title">Sale Tax:</div>
        </div>
    </div>
</div>