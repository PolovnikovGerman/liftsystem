<div class="rushoptionscontent">
    <div class="configs_title">Settings Values for site</div>
    <div class="config_title">
        <div class="config_acltions">&nbsp;</div>
        <div class="config_name">Setting Name</div>
        <div class="config_value">Setting Value</div>
    </div>
    <div class="config_tabledat">
        <?php $nrow=0;?>
        <?php foreach ($configs as $row) {?>
            <div class="config_datarow <?=($nrow%2==0 ? 'greydatarow' : 'whitedatarow')?>" id="cfgrow<?=$row['config_id']?>">
                <div class="config_acltions" data-config="<?=$row['config_id']?>"><img src="/img/others/editnotifications.png"/></div>
                <div class="config_name"><?=$row['config_alias']?></div>
                <div class="config_value"><?=$row['config_value']?></div>
            </div>
            <?php $nrow++;?>
        <?php } ?>
    </div>
</div>
