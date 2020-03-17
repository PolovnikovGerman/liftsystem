<div class="ticket_editform">
    <form id="tickededitform">
        <div class="ticket_title">
            <div class="ticket_title_number">
                Ticket # <?=$ticket_num?>
            </div>
            <div class="ticket_date">
                <input type="text" class="ticket_date" id="ticket_date" name="ticket_date" value="<?=$ticket_date?>"/>
            </div>
            <div class="ticket_title_attachment">Attachment</div>
        </div>
        <input type="hidden" id="ticket_id" name="ticket_id" value="<?=$ticket_id?>"/>
        <div class="ticket_custom_part <?=$custom_class?>">
            <div class="ticket_custom_leftpart">
                <div class="ticket_typeval">
                    Type:
                    <select class="select_ticktype" id="type" name="type">
                        <option value="">-Select-</option>
                        <option value="Issue" <?=($type=='Issue' ? 'selected="selected"' : '')?>>Issue</option>
                        <option value="Ticket" <?=($type=='Ticket' ? 'selected="selected"' : '')?>>Ticket</option>
                    </select>
                </div>
                <div class="ticket_ordernum">
                    Order #
                    <input type="text" class="ordernum" id="order_num" name="order_num" value="<?=$order_num?>"/>
                </div>
                <div class="ticket_customer_title">Customer:</div>
                <div class="ticket_customer_value">
                    <input type="text" class="customer_value" id="customer" name="customer" value="<?=$customer?>"/>
                </div>
                <div class="ticket_issue_title">Issue:</div>
                <div class="ticket_issue_value">
                    <select class="issue_select" id="custom_issue_id" name="custom_issue_id">
                        <option value="">Select..</option>
                        <?php foreach ($custom_issues as $row) {?>
                            <option value="<?=$row['ticket_issue_id']?>" <?=($row['ticket_issue_id']==$custom_issue_id ? 'selected="selected"' : '')?>><?=$row['issue_name']?></option>
                        <?php } ?>                    
                    </select>                        
                </div>
                <div class="ticket_description_title">Description:</div>
                <div class="ticket_description_value">
                    <textarea class="description" id="custom_description" name="custom_description"><?=$custom_description?></textarea>
                </div>
            </div>
            <div class="ticket_custom_rightpart">
                <div class="ticket_customer_closed">
                    Closed
                    <div class="closed_value">
                        <input type="checkbox" id="custom_closed" name="custom_closed" value="1" <?=($custom_close==1 ? 'checked="checked"' : '')?>/>
                    </div>                        
                </div>
                <div class="ticket_ticket_adjast">
                    Adjustment
                    <div class="closed_value">
                        <input type="checkbox" id="ticket_adjast" name="ticket_adjast" value="1" <?=($ticket_adjast==1 ? 'checked="checked"' : '')?>/>
                    </div>                        
                </div>
                <div class="ticket_history_title">History:</div>
                <div class="ticket_history_value">
                    <textarea class="historydat" id="custom_history" name="custom_history"><?=$custom_history?></textarea>
                </div>
            </div>
        </div>
        <div class="ticket_attachments">
            <div class="ticket_attachments_dat"><?=$attachment?></div>
            <div class="saveticket">
                <a class="saveticketdat" href="javascript:void(0);">
                    <img src="/img/saveticket.png" alt="Save"/>
                </a>
            </div>
        </div>
        <div class="ticket_vendr_part <?=$vendor_class?>">
            <div class="ticket_vendr_leftpart">
                <div class="ticket_cost">
                    Cost:
                    <input type="text" class="ticketcost" id="cost" name="cost" value="<?=$cost?>"/>
                </div>
                <div class="ticket_vendor_dat">
                    Vendor:
                    <select class="vendorselect" id="vendor_id" name="vendor_id">
                        <option value="">Select</option>
                        <?php foreach ($vendors as $row) {?>
                            <option value="<?=$row['vendor_id']?>" <?=($vendor_id==$row['vendor_id'] ? 'selected="selected"' : '')?>><?=$row['vendor_name']?></option>
                        <?php } ?>                    
                        <option value="-">-------------------</option>
                        <option value="-1" <?=($vendor_id=='-1' ? 'selected="selected"' : '')?>>Other</option>
                    </select>
                </div>
                <div class="ticket_vendor_dat">
                    Other
                    <input type="text" class="ticketcost" id="other_vendor" <?=($vendor_id==-1 ? '' : 'readonly="readonly"')?>  name="other_vendor" value="<?=$other_vendor?>"/>
                </div>
                <div class="ticket_issue_title">Issue:</div>
                <div class="ticket_issue_value">
                    <select class="vendorissue" id="vendor_issue_id" name="vendor_issue_id">
                        <option value="">Select</option>
                        <?php foreach ($vendor_issues as $row) { ?>
                        <option value="<?=$row['ticket_issue_id']?>" <?=($vendor_issue_id==$row['ticket_issue_id'] ? 'selected="selected"' : '')?>><?=$row['issue_name']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="ticket_description_title">Description:</div>
                <div class="ticket_description_value">
                    <textarea class="vendordescription" id="vendor_description" name="vendor_description"><?=$vendor_description?></textarea>
                </div>
            </div>
            <div class="ticket_vendr_rightpart">
                <div class="ticket_vendor_closed">
                    Closed
                    <div class="closed_value">
                        <input type="checkbox" id="vendor_closed" name="vendor_closed" value="1" <?=($vendor_close==1 ? 'checked="checked"' : '')?>/>
                    </div>                        
                </div>
                <div class="ticket_vendhistory_title">History:</div>
                <div class="ticket_vendhistory_value">
                    <textarea class="vendorhistory" id="vendor_history" name="vendor_history"><?=$vendor_history?></textarea>
                </div>
            </div>
        </div>
    </form>
</div>