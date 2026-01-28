<div class="leadtopreps-box">
    <div class="repsuserbox">
        <?php foreach ($leadusers as $leaduser) : ?>
        <div class="repsuserbox">
            <div class="repsuserbox-icn" data-usr="<?=$leaduser['user_id']?>"><i class="fa fa-trash" aria-hidden="true"></i></div>
            <div class="repsuserbox-name"><?=$leaduser['user_leadname']?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>