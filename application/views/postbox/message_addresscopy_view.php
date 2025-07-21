<?php foreach ($address  as $adr) : ?>
    <div class="imlinfo-tonameuser">, <?=$adr['username']?></div>
    <div class="imlinfo-emladdress">&lt;<?=$adr['usermail']?>&gt;</div>
<?php endforeach; ?>
