<!-- <form id="permisseditform">  -->
<ul id="tree">
    <?php foreach ($pages as $mrow0) { ?>
        <li>
            <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow0['id'] ?>" data-menuitem="<?= $mrow0['id'] ?>" value="1" <?= ($mrow0['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow0['label'] ?></label>
            <?php if (is_array($mrow0['element'])) { ?>
                <ul>
                    <?php foreach ($mrow0['element'] as $mrow1) { ?>
                        <li>
                            <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow1['id'] ?>" data-menuitem="<?= $mrow1['id'] ?>" value="1" <?= ($mrow1['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow1['label'] ?></label>
                            <?php if (is_array($mrow1['element'])) { ?>
                                <ul>
                                    <?php foreach ($mrow1['element'] as $mrow2) { ?>
                                        <li>
                                            <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow2['id'] ?>" data-menuitem="<?=$mrow2['id']?>" value="1" <?= ($mrow2['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow2['label'] ?></label>
                                            <?php if (is_array($mrow2['element'])) { ?>
                                                <ul>
                                                    <?php foreach ($mrow2['element'] as $mrow3) { ?>
                                                        <li>
                                                            <label><input type="checkbox" class="pageuseraccess" name="perm<?=$mrow3['id']?>" data-menuitem="<?=$mrow3['id']?>" value="1" <?= ($mrow3['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow3['label'] ?></label>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            <?php } ?>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } ?>
                        </li>
                    <?php } ?>
                </ul>
            <?php } ?>
        </li>
    <?php } ?>
</ul>
<!-- </form> -->
