<div class="relievers_keyinfo">
    <div class="sectionlabel">Key Info</div>
    <div class="sectionbody">
        <div class="content-row">
            <div class="itemparamlabel itemactive">Active:</div>
            <div class="itemparamvalue itemactive">
                <?=($item['item_active']==1 ? 'Active' : 'Inactive')?>
            </div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemactive">Template:</div>
            <div class="itemparamvalue itemtemplate"><?=$item['item_template']?></div>
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
            <div class="tags-checkbox" data-item="item_sale">
                <?php if ($item['item_sale']==0) { ?>
                    <i class="fa fa-square-o" aria-hidden="true"></i>
                <?php } else { ?>
                    <i class="fa fa-check-square" aria-hidden="true"></i>
                <?php } ?>
            </div>
            <div class="tags-checkbox-label">Top Seller</div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel subcategory">Sub-Cat 1:</div>
            <div class="itemparamvalue subcategory">Popular</div>
        </div>
        <div class="sectionseparator">&nbsp;</div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Size:</div>
            <div class="itemparamvalue itemsize"><?=$item['item_size']?></div>
        </div>
        <div class="content-row">
            <div class="itemparamlabel itemsize">Material:</div>
            <div class="itemparamvalue itemsize"><?=$item['item_material']?></div>
        </div>
        <div class="sectionseparator">&nbsp;</div>
        <div class="content-row">
            <div class="itemparamlabel itemdescrip">Description:</div>
            <div class="itemparamvalue itemdescrip"><?=$item['item_description1']?></div>
        </div>
    </div>
</div>