<form id="editpaymenform">
    <input type="hidden" name="order_id" id="order_id" value="<?=$order_id?>"/>    
    <div class="editcogform_edit" style="clear: both; float: left;width: 195px; padding: 10px;">
        <fieldset>
            <legend>Paid Sum</legend>
            <div class="cogvaledt" style="float: left; width: 68px;">Paid sum</div>
            <div class="cogvaledtval" style="float:left; width: 88px; margin-left: 10px">
                <input type="text" id="paid_sum" name="paid_sum" value="<?=$paid_sum?>" style="width: 70px;"/>
            </div>
            <div class="admin-area" id="savecustpaid">
                <div class="admin-area1">&nbsp;</div>
                <div class="admin-area2">Save Paid Sum</div>
                <div class="admin-area3">&nbsp;</div>
            </div>    
            
        </fieldset>
    </div>
</form>