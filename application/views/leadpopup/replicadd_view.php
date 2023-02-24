<div class="leadreplicaddarea">
    <div class="leadreplicadddata">
        <?php foreach ($repl as $row) { ?>
        <div class="leadreplicadddatarow">
            <input type="checkbox" class="newuserreplchk" value="<?=$row['user_id']?>">
            <span><?=$row['user_leadname']?></span>
        </div>
        <?php } ?>
    </div>
    <div class="leadreplicadddatasave">
        <img src="/img/leads/saveticket.png" alt="Save"/>
    </div>
</div>