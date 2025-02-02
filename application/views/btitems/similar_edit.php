<div class="relievers_similar">
    <div class="sectionlabel">SIMILAR ITEMS:</div>
    <div class="sectionbody <?=$missinfo==0 ? '' : 'missinginfo'?>">
        <?php $numpp=1; ?>
        <?php foreach ($items as $item) { ?>
            <div class="content-row">
                <div class="similar_numpp"><?=$numpp?>.</div>
                <div class="itemparamvalue editmode similarname">
                    <select class="similaritems" data-item="<?=$item['item_similar_id']?>">
                        <option value=""></option>
                        <?php foreach ($similars as $similar) { ?>
                            <option value="<?=$similar['item_id']?>" <?=$similar['item_id']==$item['item_similar_similar'] ? 'selected="selected"' : ''?>>
                                <?=$similar['item_number']?> <?=$similar['item_name']?>
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <?php $numpp++;?>
        <?php } ?>
    </div>
</div>
