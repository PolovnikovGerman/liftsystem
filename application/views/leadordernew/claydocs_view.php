<?php foreach ($claydocs as $claydoc) : ?>
    <div class="claymodels_optn">
        <div class="claymodels_optn_header">
            <div class="claymodels_optn_checkbox">
                <input type="checkbox" class="" <?=$edit==0 ? 'disabled="disabled"' : ''?>>
            </div>
            <div class="claymodels_optn_title">Opt <?=$claydoc['clay_option']?></div>
            <div class="claymodels_optn_star">
<!--                --><?php //if ($option['aprt']==0) : ?>
<!--                    <img src="/img/leadorder/star.svg">-->
<!--                --><?php //else: ?>
                    <img src="/img/leadorder/star.svg">
<!--                --><?php //endif; ?>
            </div>
        </div>
        <div class="claymodels_optn_box" id="claymodel_<?=$artwork?>_<?=$claydoc['clay_option']?>">
            <ul>
                <?php foreach ($claydoc['data'] as $claydat) : ?>
                    <li><?=$claydat['out_proofname']?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
<?php endforeach; ?>
