<select class="ship_tax_select2 input_border_gray" data-shipadr="<?=$shipadr['order_shipaddr_id'] ?>">
    <option value="">&nbsp;</option>
    <?php foreach ($states as $srow) { ?>
        <option value="<?= $srow['state_id'] ?>" <?= $srow['state_id'] == $shipadr['state_id'] ? 'selected="selected"' : '' ?>><?= $srow['state_code'] ?></option>
    <?php } ?>
</select>
