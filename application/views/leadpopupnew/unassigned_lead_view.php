<div class="leadtopreps-box reps-unassigned">
    <div class="repsusers-unassigned">UNASSIGNED</div>
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
