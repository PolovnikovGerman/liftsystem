<form id="configedit">
    <input type="hidden" name="config_id" id="config_id" value="<?=$config_id?>"/>
    <div class="config_acltions">
        <a class="saveconfig" id="saveconfig" href="javascript:void(0)"><img src="/img/icons/accept.png" alt="Save" style="width: 16px; height: 16px;"/></a>
        <a class="closeconfig" id="closeconfig" href="javascript:void(0)"><img src="/img/icons/cancel.png" alt="Cancel" style="width: 16px; height: 16px;"/></a>
    </div>
    <div class="config_name">
        <input type="text" class="config_nameedt" name="config_alias" id="config_alias" value="<?=$config_alias?>"/>
    </div>
    <div class="config_value">
        <input type="text" class="config_valueedt" name="config_value" id="config_value" value="<?=$config_value?>"/>
    </div>
</form>