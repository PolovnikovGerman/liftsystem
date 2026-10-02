<select class="select_addressarea" data-address="<?= $address['order_shipaddr_id'] ?>" data-fld="state_id" <?=$edit==0 ? 'disabled' : ''?>>
    <option value="">State</option>
    <?php foreach ($states as $state) : ?>
        <option value="<?= $state['state_id'] ?>" <?= $state['state_id'] == $address['state_id'] ? 'selected="selected"' : '' ?>><?= $state['state_code'] ?></option>
    <?php endforeach; ?>
</select>
