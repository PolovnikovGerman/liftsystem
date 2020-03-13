<div class="shipdatarowarea" data-shipadr="<?=$order_shipaddr_id?>">
    <div class="numpp"><?=$numpp?></div>
    <div class="addressarea <?=$numpp%2==0 ? 'lightgrey' : 'darkgrey'?>">
        <div class="shipqty"><?=$item_qty?></div>
        <div class="devider">-</div>
        <div class="shipaddres">
            <span class="text_bold"><?=$out_shipping_method?></span> to <?=$out_zip?>, <?=$out_country?>
        </div>
        <div class="shipcost"><?=MoneyOutput($shipping)?></div>
        <div class="arrivedate text_blue"><?=empty($arrive_date) ? '&nbsp;' : '- '.date('D - M d',$arrive_date)?> </div>
    </div>
</div>