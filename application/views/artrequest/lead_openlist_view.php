<select name="lead_id" id="lead_id" class="leadopenlist">
    <option value="">Enter & Select Lead...</option>
    <?php foreach ($leads as $row) {?>
        <option value="<?=$row['lead_id']?>" <?=($row['lead_id']==$current ? 'selected="selected"' : '')?>><?=$row['lead_number']?>, <?=$row['lead_customer']?>, <?=$row['lead_item']?></option>
    <?php }?>
</select>