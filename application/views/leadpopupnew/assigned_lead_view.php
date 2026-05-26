<div class="leadtopreps-box">
    <div class="repsusers">
        <?php $numpp = 0; ?>
        <?php foreach ($leadusers as $leaduser) : ?>
        <div class="repsuserbox">
            <div class="repsuserbox-icn" data-usr="<?=$leaduser['leaduser_id']?>" data-usrname="<?=$leaduser['user_leadname']?>">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </div>
            <div class="repsuserbox-name"><?=$leaduser['user_leadname']?></div>
        </div>
        <?php $numpp++; ?>
        <?php if ($numpp >= 2) : ?>
            <?php break;?>
        <?php endif; ?>
        <?php endforeach; ?>
        <?php if ($replicqty > 2) : ?>
            <div class="repsuserbox-other">+<?=$replicqty-2?></div>
            <div class="leadotherreplica-popup">
                <div class="leadusrreplicacancel"><i class="fa fa-times-circle-o"></i></div>
                <?php $othidx = 0; ?>
                <?php foreach ($leadusers as $leaduser) : ?>
                    <?php if ($othidx >= 2) : ?>
                    <div class="datarow">
                        <div class="repsuserbox-icn" data-usr="<?=$leaduser['leaduser_id']?>" data-usrname="<?=$leaduser['user_leadname']?>">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </div>
                        <div class="repsuserbox-name"><?=$leaduser['user_leadname']?></div>
                    </div>
                    <?php endif; ?>
                    <?php $othidx++; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($added==1) : ?>
    <div class="leadtopreps-addbtn">+</div>
    <div class="leadtopassign-popup">
        <div class="leadusrreplicacancel"><i class="fa fa-times-circle-o"></i></div>
        <?php foreach ($users as $user) : ?>
        <div class="datarow">
            <div class="leadusrreplicachk">
                <input type="checkbox" name="leadusercandidat" data-usr="<?=$user['user_id']?>"/>
            </div>
            <div class="leadusrreplicausrname truncateoverflowtext"><?=$user['user_leadname']?></div>
        </div>
        <?php endforeach; ?>
        <div class="datarow">
            <div class="leadusrreplicasave">Save</div>
        </div>
    </div>
    <?php endif; ?>
</div>