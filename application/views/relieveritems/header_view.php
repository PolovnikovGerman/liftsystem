<input type="hidden" id="dbdetailid" value="<?=$item['item_id']?>"/>
<input type="hidden" id="dbdetailsession" value="<?=$session_id?>"/>
<input type="hidden" id="dbdetailbrand" value="<?=$item['brand']?>"/>
<div class="content-row">
    <div class="relieverdetails-left-header">
        <div class="itemnameview">
            <div><?=$item['item_name']?></div>
        </div>
        <div class="itemnumberlabel">Item #</div>
        <div class="itemnumberview"><?=$item['item_number']?></div>
    </div>
    <div class="relieverdetails-right-header">
        <div class="relieverdetails-action">
            <div class="relieverdetailseditbtn">Edit</div>
        </div>
    </div>
</div>