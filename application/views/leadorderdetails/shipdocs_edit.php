<?php $numpp = 1;?>
<?php foreach ($shipdocs as $shipdoc) : ?>
    <div class="datarow">
        <div class="shipdocnumpp"><?=$numpp?></div>
        <div class="shipdocviewdoc editmode truncateoverflowtext" data-link="<?=$shipdoc['shipdoc_link']?>"
             data-source="<?=$shipdoc['shipdoc_src']?>"><?=$shipdoc['shipdoc_src']?></div>
        <div class="removeshipdoc" data-shipdoc="<?=$shipdoc['order_shipdoc_id']?>">delete</div>
    </div>
    <?php $numpp++;?>
<?php endforeach; ?>
