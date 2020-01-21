<div class="itemmedia">
    <div id="slider">
        <ul>
            <li>
                <!-- Calculate offset -->
                <?php $i=0;$num_row=0;?>
                <?php foreach ($images as $row) {?>
                <div class="imgcontent" id="imgcontent<?=$row['item_img_order']?>">
                    <div class="imagename">
                        <?=$row['name']?>
                    </div>
                    <?php if ($row['src']=='') {?>
                        <div class="picture-none">
                            <a href="javascript:void(0)">
                                <img src="/img/itemdetails/picture-none.png" <?=($edit==1 ? 'onclick="uploadimg(this);"' : '')?> id="uplimg<?=$row['item_img_order']?>" alt="Empty"/>
                            </a>
                        </div>
                    <?php } else { ?>
                        <div class="pictures">
                            <div class="close-x">
                                <a href="javascript:void(0)">
                                    <img src="/img/itemdetails/x.png" id="delimg<?=$row['item_img_order']?>" style="z-index: 10; position: relative;" <?=($edit==1 ? 'onclick="delete_item_image(this);"' : '')?>/>
                                </a>
                            </div>
                            <div class="pic">
                                <a href="javascript:void(0)">
                                    <img src="<?=$row['src']?>?r=<?=rand(1,200)?>" style="width: 77px; height: 77px; margin-left: -9px; margin-top: -14px;" alt="Img" id="uplimg<?=$row['item_img_order']?>" <?=($edit==1 ? 'onclick="uploadimg(this);"' : '')?>/>
                                </a>
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
