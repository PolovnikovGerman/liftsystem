<?php $nloc=1;?>
<div class="location-dats">
    <?php foreach ($art as $row) {?>
        <div class="location-dat">
            <div class="number-location-title">
                <div class="number-location"><?=$nloc?></div>
                <div class="location-title">Location <?=$nloc?>: <?=$row['order_artwork_printloc']?></div>
            </div>

            <div class="location-info">
                <div class="colors">
                    <div class="colors-input">
                        <?=$row['order_artwork_colors']?>
                    </div>
                    <div class="colors-title">Colors:</div>
                </div>
                <div class="logos">
                    <div class="logos-input">
                        <?php if (count($row['users_logo'])!=0) {?>
                            <ul style="padding:0; margin:0; list-style:none;">
                                <?php foreach ($row['users_logo'] as $logos) { ?>
                                    <li><a href="<?=$logos['order_userlogo_filename']?>" class="uplattach"><?=$logos['order_userlogo_file']?></a></li>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </div>
                    <div class="colors-title">Logos:</div>
                </div>
                <div class="font">
                    <div class="font-input">
                        <!-- <img src="/img/font-text.png"/> -->
                        <?=$row['order_artwork_font']?>
                    </div>
                    <div class="font-title">Font:</div>
                </div>
                <div class="textt">
                    <div class="textt-input">
                        <?=$row['order_artwork_text']?>
                    </div>
                    <div class="text-title">Text:</div>
                </div>
                <div class="notes">
                    <div class="notes-input">
                        <?=$row['order_artwork_note']?>
                    </div>
                    <div class="notes-title">Notes:</div>
                </div>
            </div>
        </div>
        <img src="/img/orders/art-work-separator.png"/>
        <?php $nloc++;?>
    <?php }?>
</div>
