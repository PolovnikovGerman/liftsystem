<?php foreach ($images as $row) { ?>
    <input type="hidden" id="picid<?=$row['item_img_order']?>" name="picid<?=$row['item_img_order']?>" value="<?=$row['item_img_id']?>"/>
    <input type="hidden" id="picsrc<?=$row['item_img_order']?>" name="picsrc<?=$row['item_img_order']?>" value="<?=$row['src']?>"/>
<?php } ?>