<input type="hidden" id="notification_id" value="<?=$notification_id;?>"/>
<fieldset>
    <legend>Email Notification</legend>
    <div class="edit_notification_row">
        <div class="notification_label">
            Notification System
        </div>
        <div class="notification_input">
            <select name="notification_system" id="notification_system">
                <option value="">Select Notification System</option>
                <?php foreach ($notification_systems as $row) { ?>
                    <option value="<?=$row?>" <?=($row==$notification_system ? 'selected="selected"' : '')?> ><?=$row?></option>
                <?php } ?>
            </select>
        </div>
    </div>
    <div class="edit_notification_row">
        <div class="notification_label">
            Email for Notification
        </div>
        <div class="notification_input">
            <input type="text" name="notification_email" id="notification_email" value="<?=$notification_email;?>" />
        </div>
    </div>
    <div class="edit_notification_row">
        <div class="saveediting">
            Save changes
        </div>
    </div>
</fieldset>