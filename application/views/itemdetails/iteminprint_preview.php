<div class="loactionview_preview <?=empty($item_inprint_view) ? 'emptyview' : ''?>">
    <?php if (!empty($item_inprint_view)) { ?>
        <img src ="<?=$item_inprint_view?>" alt="Preview">
        <div class="delimprintview">
            <i class="fa fa-trash-o" aria-hidden="true"></i>
        </div>
    <?php } else { ?>
        <div id="newimprintlocationview"></div>
    <?php } ?>
</div>
