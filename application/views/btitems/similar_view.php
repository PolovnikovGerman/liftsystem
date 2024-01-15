<div class="relievers_similar">
    <div class="sectionlabel">SIMILAR ITEMS:</div>
    <div class="sectionbody <?=$missinfo==0 ? '' :  'missinginfo'?>">
        <?php $numpp=1; ?>
        <?php foreach ($items as $item) { ?>
            <div class="content-row">
                <div class="similar_numpp"><?=$numpp?>.</div>
                <div class="itemparamvalue similarname">
                    <?=empty($item['item_similar_similar']) ? '' : $item['item_number'].' - '.$item['item_name'] ?>
                </div>
            </div>
            <?php $numpp++;?>
        <?php } ?>
    </div>
</div>
