<div class="categories_list_title">Categories</div>
<div class="categories_list_data">
    <?php $numpp=1;?>
    <?php $numr=0;?>
    <form id="sortsequence">
        <div id="sortable">
            <?php foreach ($categories as $list) { ?>
                <div class="category_data_row <?=$numr%2==0 ? 'whitedatarow' : 'greydatarow'?> <?=$list['category_id']==$current_category ? 'active' : ''?>" data-category="<?=$list['category_id']?>">
                    <input type="hidden" id="item_<?=$list['category_id']?>" name="item_<?=$list['category_id']?>" value="<?=$list['category_id']?>" />
                    <div class="category_num"><?=($list['category_template']=='Virtual_Category' ? 'Special' : $numpp)?></div>
                    <div class="category_label"><?=$list['dropdown_title']?></div>
                    <div class="category_pointer">&nbsp;</div>
                </div>
                <?php $numr++;?>
                <?php if ($list['category_template']!='Vistual_Category') { ?>
                    <?php $numpp++; ?>
                <?php } ?>
            <?php } ?>
        </div>
    </form>
</div>
