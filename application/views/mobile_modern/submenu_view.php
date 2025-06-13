<a class="btn btn-secondary dropdown-toggle" href="#" role="button" id="dd-subitems" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></a>
<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-subitems">
    <?php foreach ($menu as $item) : ?>
        <div class="dropdown-item <?=str_replace('#','',$item['item_link'])==$start ? 'active-item' : ''?>"
             data-url="<?=str_replace('#','', $item['item_link'])?>"><?=$item['item_name']?></div>
    <?php endforeach ?>
</div>
