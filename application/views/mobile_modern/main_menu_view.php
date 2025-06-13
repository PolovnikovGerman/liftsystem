<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-deptitems">
    <?php foreach ($permissions as $permission) :?>
        <div class="dropdown-item <?=$permission['item_link']==$activelnk ? 'active-item' : ''?>"
             data-link="<?=$permission['item_link']?>"><?=$permission['item_name']?></div>
    <?php endforeach; ?>
</div>
