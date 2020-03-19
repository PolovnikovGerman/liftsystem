<select class="<?= ($catval != '' ? 'category_exist' : 'category_empty')?>" data-itemcategid="<?=empty($itemcategory_id) ? -1 : $itemcategory_id?>">
    <option value="0">--select--</option>
    <?php foreach ($categ_list as $list) { ?>
        <option value="<?= $list['category_id'] ?>" <?= ($list['category_id'] == $catval ? 'selected="selected"' : '') ?>>
            <?= $list['category_name'] ?>
        </option>
    <?php } ?>
</select>
