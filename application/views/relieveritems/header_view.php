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
            <div class="itemdetails-keydatvalue" data-event="hover" data-css="itemdetailsballonbox" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-position="down" data-textcolor="#000"
                 data-balloon="<?=$item['item_name']?>" data-timer="4000" data-delay="1000"><?=$item['item_name']?></div>
        </div>
        <div class="itemdetails-itemnumarea">
            <div class="namearea-label">Item #</div>
            <div class="itemdetails-keydatvalue"><?=$item['item_number']?></div>
        </div>
        <div class="itemdetails-itemcategory">
            <div class="namearea-label">Category:</div>
            <div class="itemdetails-keydatvalue"><?=$item['category_name']?></div>
        </div>
    </div>
    <div class="itemdetails-mode">
        <div class="itemdetails-modevalue">VIEW</div>
        <div class="itemdetails-modelabel">MODE</div>
    </div>
    <div class="itemdetails-action">
        <div class="edit_itemdetails">
            <img src="/img/dbitems/edit_details_btn.png"/>
        </div>
    </div>
</div>