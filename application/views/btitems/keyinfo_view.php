<div class="relievers_keyinfo">
    <div class="sectionlabel">Key Info:</div>
    <div class="sectionbody <?=$missinfo==0 ? '' :  'missinginfo'?>">
        <div class="content-row">
            <div class="itemparamlabel itemname">Item Name:</div>
            <div class="itemparamvalue itemname"><?=$item['item_name']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemtemplate">Template:</div>
            <div class="itemparamvalue itemtemplate <?=empty($item['item_template']) ? 'missing_info' : ''?>"><?=$item['item_template']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel tags">Tags:</div>
            <div class="tags-checkbox" data-item="item_new">
                <?php if ($item['item_new']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label <?=$item['item_new']==1 ? 'active' : ''?>">New</div>
            <div class="tags-checkbox" data-item="item_sale">
                <?php if ($item['item_sale']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label <?=$item['item_sale']==1 ? 'active' : ''?>">Sale</div>
            <div class="tags-checkbox" data-item="item_topsale">
                <?php if ($item['item_topsale']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label <?=$item['item_topsale']==1 ? 'active' : ''?>">Top Seller</div>
        </div>
        <?php $numpp = 1;?>
        <?php foreach ($categories as $category) { ?>
            <div class="content-row">
                <div class="itemparamlabel subcategory">Sub-Cat <?=$numpp?>:</div>
                <div class="itemparamvalue subcategory"><?=$category['category_name']?></div>
            </div>
            <?php $numpp++;?>
        <?php } ?>
        <div class="sectionseparator">&nbsp;</div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Size:</div>
            <div class="itemparamvalue itemsize <?=empty($item['item_size']) ? 'missing_info' : ''?>"><?=$item['item_size']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemmaterial">Material:</div>
            <div class="itemparamvalue itemmaterial <?=empty($item['item_material']) ? 'missing_info' : ''?>"><?=$item['item_material']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemdescrip">Description:</div>
            <div class="itemparamvalue itemdescrip <?=empty($item['item_description1']) ? 'missing_info' : ''?>"><?=$item['item_description1']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel bullets">Bullet Points:</div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue itembullet"><?=$item['bullet1']?></div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue itembullet"><?=$item['bullet2']?></div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue itembullet"><?=$item['bullet3']?></div>
            <div class="bullet_point"><i class="fa fa-circle" aria-hidden="true"></i></div>
            <div class="itemparamvalue itembullet"><?=$item['bullet4']?></div>
        </div>
    </div>
</div>