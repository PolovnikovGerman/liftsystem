<?php $numpp = 1; ?>
<?php foreach ($claydocs as $claydoc) : ?>
    <div class="claymodelview">clay_<?=str_pad($numpp, 2, '0', STR_PAD_LEFT)?></div>
    <?php $numpp++; ?>
<?php endforeach; ?>
