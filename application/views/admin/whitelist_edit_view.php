<div class="whitelist_action">
    <div class="editwhitelist">
        <img src="/img/icons/accept.png" alt="Add" id="addwhitelist" style="width: 18px;"/>
    </div>
    <div class="delwhitelist">
        <img src="/img/icons/cancel.png" alt="Cancel" id="cancelwhitelist" style="width: 18px;"/>
    </div>
</div>
<div class="whitelist_senderdata">
    <input type="text" name="sendermail" class="sendermail" id="sendermail" value="<?=$sender?>"/>
</div>
<div class="whitelist_userdata">
    <select id="user_id" name="user_id" class="senderuserid">
        <option value="">Select</option>
        <?php foreach ($users as $row) {?>
            <option value="<?=$row['user_id']?>" <?=($row['user_id']==$user_id ? 'selected="selected"' : '')?>><?=$row['user_name']?></option>
        <?php } ?>
    </select>
</div>