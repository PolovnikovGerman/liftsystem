<?php foreach ($postboxes as $postbox) { ?>
    <div class="maincontentmenu_item" data-postbox="<?=$postbox['postbox_id']?>">
        <?=$postbox['postbox_title']?>
    </div>
<?php } ?>
