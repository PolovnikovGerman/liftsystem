<div class="block_10 project text_style_5 profitprojectdetailsviewarea" data-order="<?=$order_id?>"  data-editmode="<?=$edit_mode?>">
    <div class="block_10_text1">
        <?=($profit_view=='points' ? $profit : MoneyOutput($profit))?>
    </div>
    <div class="block_10_text2">
        <div class="detailsproject">
            <?=$profit_project?>% PROJ
        </div>
        <div class="detailscomplet">
            <?=$profit_completed?>% COMPLETE
        </div>
    </div>
</div>
