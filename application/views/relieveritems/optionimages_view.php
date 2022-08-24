<?php $numpp=0;?>
<?php foreach ($imgoptions as $imgoption) { ?>
    <?php if ($numpp%2==0) { ?>
        <div class="content-row">
    <?php } ?>
    <div class="itemoptionimagesrc">&nbsp;</div>
    <div class="itemoptionimagelabel">Blue</div>
    <?php $numpp++;?>
    <?php if ($numpp%2==0) { ?>
        </div>
    <?php } ?>
<?php } ?>
<?php if ($numpp%2!=0) { ?>
    </div>
<?php } ?>
