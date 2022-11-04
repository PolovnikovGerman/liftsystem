<!-- <form id="permisseditform">  -->
<div class="datarow">
    <div class="webbrandtitle" data-brand="sbweb">StressBalls</div>
    <div class="webbrandtitle" data-brand="srweb">StressRelievers</div>
    <div class="webbrandtitle" data-brand="commonweb">Common Pages</div>
</div>
<div class="menuitemsview" data-brand="sbweb">
    <ul id="sbtree">
        <?php foreach ($sbpages as $mrow0) { ?>
            <li>
                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow0['id'] ?>" data-menuitem="<?= $mrow0['id'] ?>" data-brand="sb" value="1" <?= ($mrow0['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow0['label'] ?></label>
                <?php if (is_array($mrow0['element'])) { ?>
                    <ul>
                        <?php foreach ($mrow0['element'] as $mrow1) { ?>
                            <li>
                                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow1['id'] ?>" data-menuitem="<?= $mrow1['id'] ?>" data-brand="sb" value="1" <?= ($mrow1['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow1['label'] ?></label>
                                <?php if (is_array($mrow1['element'])) { ?>
                                    <ul>
                                        <?php foreach ($mrow1['element'] as $mrow2) { ?>
                                            <li>
                                                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow2['id'] ?>" data-menuitem="<?=$mrow2['id']?>" data-brand="sb" value="1" <?= ($mrow2['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow2['label'] ?></label>
                                                <?php if (is_array($mrow2['element'])) { ?>
                                                    <ul>
                                                        <?php foreach ($mrow2['element'] as $mrow3) { ?>
                                                            <li>
                                                                <label><input type="checkbox" class="pageuseraccess" name="perm<?=$mrow3['id']?>" data-menuitem="<?=$mrow3['id']?>" data-brand="sb" value="1" <?= ($mrow3['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow3['label'] ?></label>
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
</div>
<div class="menuitemsview" data-brand="srweb">
    <ul id="srtree">
        <?php foreach ($srpages as $mrow0) { ?>
            <li>
                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow0['id'] ?>" data-menuitem="<?= $mrow0['id'] ?>" data-brand="sr" value="1" <?= ($mrow0['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow0['label'] ?></label>
                <?php if (is_array($mrow0['element'])) { ?>
                    <ul>
                        <?php foreach ($mrow0['element'] as $mrow1) { ?>
                            <li>
                                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow1['id'] ?>" data-menuitem="<?= $mrow1['id'] ?>" data-brand="sr" value="1" <?= ($mrow1['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow1['label'] ?></label>
                                <?php if (is_array($mrow1['element'])) { ?>
                                    <ul>
                                        <?php foreach ($mrow1['element'] as $mrow2) { ?>
                                            <li>
                                                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow2['id'] ?>" data-menuitem="<?=$mrow2['id']?>" data-brand="sr" value="1" <?= ($mrow2['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow2['label'] ?></label>
                                                <?php if (is_array($mrow2['element'])) { ?>
                                                    <ul>
                                                        <?php foreach ($mrow2['element'] as $mrow3) { ?>
                                                            <li>
                                                                <label><input type="checkbox" class="pageuseraccess" name="perm<?=$mrow3['id']?>" data-menuitem="<?=$mrow3['id']?>" data-brand="sr" value="1" <?= ($mrow3['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow3['label'] ?></label>
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
</div>
<div class="menuitemsview" data-brand="commonweb">
    <ul id="commontree">
        <?php foreach ($commpages as $mrow0) { ?>
            <li>
                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow0['id'] ?>" data-menuitem="<?= $mrow0['id'] ?>" data-brand="common" value="1" <?= ($mrow0['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow0['label'] ?></label>
                <?php if (is_array($mrow0['element'])) { ?>
                    <ul>
                        <?php foreach ($mrow0['element'] as $mrow1) { ?>
                            <li>
                                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow1['id'] ?>" data-menuitem="<?= $mrow1['id'] ?>" data-brand="common" value="1" <?= ($mrow1['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow1['label'] ?></label>
                                <?php if (is_array($mrow1['element'])) { ?>
                                    <ul>
                                        <?php foreach ($mrow1['element'] as $mrow2) { ?>
                                            <li>
                                                <label><input type="checkbox" class="pageuseraccess" name="perm<?= $mrow2['id'] ?>" data-menuitem="<?=$mrow2['id']?>" data-brand="common" value="1" <?= ($mrow2['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow2['label'] ?></label>
                                                <?php if (is_array($mrow2['element'])) { ?>
                                                    <ul>
                                                        <?php foreach ($mrow2['element'] as $mrow3) { ?>
                                                            <li>
                                                                <label><input type="checkbox" class="pageuseraccess" name="perm<?=$mrow3['id']?>" data-menuitem="<?=$mrow3['id']?>" data-brand="common" value="1" <?= ($mrow3['value'] == 1 ? 'checked="checked"' : '') ?>/><?= $mrow3['label'] ?></label>
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
</div>
<!-- </form> -->
