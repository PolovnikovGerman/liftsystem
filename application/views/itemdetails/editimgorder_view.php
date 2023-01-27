<div class="imgorderdata">
    <div class="imgsort-scroll">
        <form id="sortimg">
            <ul id="sortable" class="imgsortable">
                <?php foreach ($images as $row) {?>
                    <li>
                        <input type="hidden" id="it<?=$row['imgid']?>" name="it<?=$row['imgid']?>" value="<?=$row['item_id']?>|<?=$row['img_name']?>" />
                        <img src="<?=$row['img_name']?>" style="height: 91px; width: 97px;" title="<?=$row['img_name']?>" />
                    </li>
                <?php } ?>
            </ul>
        </form>
    </div>
    <div class="saveoder">
        <div class="activate_btn saveediting" style="color:#FFF; text-align: center; font-size: 16px; font-weight: bold; padding-top: 11px; ">
            Save Order
        </div>
    </div>
</div>
