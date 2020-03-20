<?php $nrow=0;?>
<?php foreach ($data as $row) {?>
    <div class="countries_data_row <?=($nrow%2==0 ?  'whitedatarow' : 'greydatarow')?> <?=($row['shipallow']==0 ? 'notallowed' : '')?>" data-countryid="<?=$row['country_id']?>">
        <div class="country_name_data"><?=$row['country_name']?></div>
        <div class="country_code_dat"><?=$row['country_iso_code_2']?></div>
        <div class="country_code_dat"><?=$row['country_iso_code_3']?></div>
        <div class="country_shzone_dat">
            <select class="shzonevalue" data-countryid="<?=$row['country_id']?>">
                <?php foreach ($zones as $zrow) {?>
                    <option value="<?=$zrow['zone_id']?>" <?=($zrow['zone_id']==$row['zone_id'] ? 'selected="selected"' : '')?>><?=$zrow['zone_name']?></option>
                <?php } ?>
            </select>
        </div>
        <div class="country_shipallow_dat">
            <input type="checkbox" class="cntshipallow" name="shipallow" data-coutryid="<?=$row['country_id']?>" <?=($row['shipallow']==1 ? 'checked="checked"' : '')?> />
        </div>
        <?php $nrow++;?>
    </div>
<?php } ?>
