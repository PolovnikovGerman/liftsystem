<div class="itemmedia">
    <div id="slider">
        <ul>
            <li>
                <!-- Calculate offset -->
                <?php $i=0;$num_row=0;?>
                <?php foreach ($images as $row) {?>
                <div class="imgcontent">
                    <div class="imagename">
                        <?=$row['name']?>
                    </div>
                    <?php if ($row['src']=='') {?>
                        <div class="picture-none" id="uplimg<?=$row['item_img_id']?>" data-idx="<?=$row['item_img_id']?>">
                            &nbsp;
                        </div>
                    <?php } else { ?>
                        <div class="pictures" style="background-image: url('<?=$row['src']?>'); background-size: contain; background-repeat: no-repeat;">
                            <div class="close-x" data-idx="<?=$row['item_img_id']?>">
                                <img src="/img/itemdetails/x.png"/>
                            </div>
                            <div class="pic" id="uplimg<?=$row['item_img_id']?>" data-idx="<?=$row['item_img_id']?>">
                                &nbsp;
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php $i++;$num_row++;?>
                <?php if ($num_row==8 && $i<$limit) { ?>
            </li>
            <li>
                <?php $num_row=0;?>
                <?php } ?>
                <?php } ?>
            </li>
        </ul>
        <!-- Buttons DIV -->
    </div>
</div>
<div class="mediadata">
    <div class="video">
        <?=$video?>
    </div>
    <div class="audio">
        <?=$audio;?>
    </div>
    <div class="faces">
        <?=$faces;?>
    </div>
</div>
