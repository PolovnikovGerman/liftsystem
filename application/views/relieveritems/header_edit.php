<input type="hidden" id="dbdetailid" value="<?=$item['item_id']?>"/>
<input type="hidden" id="dbdetailsession" value="<?=$session_id?>"/>
<input type="hidden" id="dbdetailbrand" value="<?=$item['brand']?>"/>

<div class="content-row">
    <div class="itemdetails_logo">
        <img src="/img/dbitems/stressrelievers_logo.png">
    </div>
    <div class="itemdetailsstatus">
        <div class="itemdetailsstatus-value"><?=$item['item_active']==1 ? 'ACTIVE' : 'INACTIVE'?></div>
        <div class="itemdetailsstatus-label">ITEM</div>
    </div>
    <div class="itemdetails-keydataarea">
        <div class="itemdetails-namearea">
            <div class="namearea-label">Item Name</div>
            <div class="itemdetails-keydatvalue">
                <input type="text" class="itemkeyinfoinput itemname"  data-item="item_name" value="<?=$item['item_name']?>"/>
            </div>
        </div>
        <div class="itemdetails-itemnumarea">
            <div class="namearea-label">Item #</div>
            <div class="itemdetails-keydatvalue" data-item="item_number"><?=$item['item_number']?></div>
        </div>
        <div class="itemdetails-itemcategory">
            <div class="namearea-label">Category:</div>
            <div class="itemdetails-keydatvalue">
                <?php if ($item['item_id'] > 0) { ?>
                    <?=$item['category_name']?>
                <?php } else { ?>
                    <select class="categoryitemselect">
                        <option value=""></option>
                        <?php foreach ($categories as $category) { ?>
                            <option value="<?=$category['category_id']?>"><?=$category['category_name']?></option>
                        <?php } ?>
                    </select>
                <?php } ?>

            </div>
        </div>
    </div>
    <div class="itemdetails-mode">
        <div class="itemdetails-modevalue">EDIT</div>
        <div class="itemdetails-modelabel">MODE</div>
    </div>
    <div class="itemdetails-action">
        <div class="save_itemdetails">
            <img src="/img/dbitems/save_details_btn.png"/>
        </div>
    </div>
</div>