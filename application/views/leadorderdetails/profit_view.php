<div class="block_10 <?=$profit_class?> text_style_5 profitdetailsviewarea"
     data-viewsrc="/leadorder/ordercogdetails/?ord=<?=$order_id?>&clas=<?=$profit_class?>">
    <div class="block_10_text1">
        <?=MoneyOutput($profit)?>
    </div>
    <div class="block_10_text2"><?=round($profit_perc,0)?>%</div>
</div>
