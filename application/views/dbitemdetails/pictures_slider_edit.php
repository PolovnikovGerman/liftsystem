<div class="itemmedia">
    <div id="sliderlist">
        <ul>
            <li>
                <!-- Calculate offset -->
                <?php $i = 0;
                $num_row = 0; ?>
                <?php foreach ($images as $row) { ?>
                <div class="imgcontent">
                    <div class="imagetitle">
                        <?= $row['title'] ?>
                    </div>
                    <div class="pictures" style="background-image: url('<?= $row['item_img_name'] ?>'); background-size: contain; background-repeat: no-repeat;">
                        <div class="close-x" data-idx="<?=$row['item_img_id']?>">
                            <img src="/img/itemdetails/x.png"/>
                        </div>
                        <div class="pic" id="uplimg<?= $row['item_img_id'] ?>" data-idx="<?= $row['item_img_id'] ?>">
                            &nbsp;
                        </div>
                    </div>
                    <div class="picturename">
                        <div class="viewparam"><?= $row['item_img_label'] ?></div>
                    </div>
                </div>
                <?php $i++;
                $num_row++; ?>
                <?php if ($num_row == 5 && $i < $limit) { ?>
            </li>
            <li>
                <?php $num_row = 0; ?>
                <?php } ?>
                <?php } ?>
            </li>
        </ul>
        <!-- Buttons DIV -->
    </div>
</div>
