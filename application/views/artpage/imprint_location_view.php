<select class="artworkoption_location" data-artworkartid="<?=$artwork_art_id?>">
    <?php foreach ($locs as $row) {?>
        <option value="<?=$row['key']?>" <?=($row['key']==$defval ? 'selected="selected"' : '')?> ><?=$row['value']?></option>
    <?php } ?>
</select>