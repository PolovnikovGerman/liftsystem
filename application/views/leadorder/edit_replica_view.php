<select class="leadorder_selectreplic" name="order_usr_repic" <?=((isset($edit) && $edit==0) ? 'disabled="disabled"' : '')?>>
    <?php if ($data['order_usr_repic'] == '') { ?>
        <option value="">Unassigned</option>
    <?php } ?>
    <?php foreach ($users as $urow) { ?>
        <option value="<?=($urow['user_id']) ?>" <?= ($urow['user_id'] == $data['order_usr_repic'] ? 'selected="selected"' : '') ?>>
            <?= ($urow['user_leadname'] == '' ? $urow['user_name'] : $urow['user_leadname']) ?>
        </option>
    <?php } ?>
</select>
