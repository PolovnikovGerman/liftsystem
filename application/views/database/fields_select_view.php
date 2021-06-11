<div class="fields_list">
    <?php foreach ($fields as $row) {?>
        <?php $addclass=$def_class;?>
        <?php
        if ($allowed==1 && $row['expfield_selected']==0) {
            $addclass='allowed_hide';
        } elseif ($allowed==0 && $row['expfield_selected']==0) {
            $addclass='select_allowed';
        }
        ?>
        <div class="fieldname <?=($addclass)?>" id="<?=($allowed==1 ? 'all' : 'sel')?><?=$row['expfield_id']?>"><?=$row['expfield_description']?></div>
    <?php } ?>
</div>