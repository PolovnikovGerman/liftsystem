<div class="emailsmenu-body">
    <?php foreach ($postboxes as $postbox) : ?>
        <div class="emailsmenu-tab" data-postbox="<?=$postbox['postbox_id']?>"><?=$postbox['postbox_title']?></div>
    <?php endforeach; ?>
</div>
