<?php foreach ($brands as $row) { ?>
    <div class="left_tab <?=$row['brand']==$active ? 'active' : ''?>" data-brand="<?=$row['brand']?>">
        <img src="<?=$row['logo']?>" alt="Logo<?=$row['brand']?>"/>
    </div>
<?php } ?>
