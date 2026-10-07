<div class="datarow">
    <div class="artdocupload_title"><?=$title?></div>
</div>
<div class="datarow">
    <div class="artdocsections">
        <?php $optionchr = 65; ?>
        <select name="docoption" class="uploaddocsection">
            <?php for ($i=0; $i<16; $i++) : ?>
            <option value="<?=chr($optionchr+$i)?>">Option <?=chr($optionchr+$i)?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>
<div class="datarow">
    <div id="orderattachlists" class="artlogouploads">&nbsp;</div>
    <div id="artdoc-uploader" data-artwork="<?=$artwork?>"></div>
</div>