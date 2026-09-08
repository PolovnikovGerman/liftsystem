<div class="fulflmshipping_header"><?=QTYOutput($qty)?> <?=$item?></div>
<div class="fulflmshipping_body"><?=$trackbody?></div>
<div class="fulflmshipping_footer">
    <?php if ($completed==0) : ?>
        <div class="fulflmshipping_remaining"><?=$remind?> Remaining</div>
        <div class="fulflmshipping_ship"><?=$shipdate?></div>
    <?php else : ?>
        <div class="fulflmshipping_remaining">100% FULFILLED <?=$remind==0 ? '' : '(+'.abs($remind).' extra pieces)'?></div>
    <?php endif; ?>
</div>
