<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-deptitems">
    <?php foreach ($permissions as $permission) :?>
        <div class="dropdown-item" data-link="<?=$permission['item_link']?>"
            <?=str_replace('/','',$permission['item_link'])==$activelnk ? '' : 'active-item'?>><?=$permission['item_name']?></div>
    <?php endforeach; ?>
</div>
