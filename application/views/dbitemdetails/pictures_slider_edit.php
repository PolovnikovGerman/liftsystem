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
                    <?php if (empty($row['item_img_name'])) { ?>
                        <div class="picture-none" id="uplimg<?= $row['item_img_id'] ?>" data-idx="<?= $row['item_img_id'] ?>">
                            <div class="pic" >
                                &nbsp;
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="pictures" style="background-image: url('<?= $row['item_img_name'] ?>'); background-size: contain; background-repeat: no-repeat;">
                            <div class="remove-slideimage" data-idx="<?=$row['item_img_id']?>">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </div>
                            <div class="pic" id="uplimg<?= $row['item_img_id'] ?>" data-idx="<?= $row['item_img_id'] ?>">
                                &nbsp;
                            </div>
                        </div>
                    <?php } ?>
                    <div class="picturename">
                        <input type="text" class="imagesinpt imglabel" data-idx="<?=$row['item_img_id']?>" data-item="item_img_label" value="<?=$row['item_img_label']?>"/>
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
