<div class="chargeattemptlogarea">
    <div class="tablehead">
        <div class="date">Date</div>
        <div class="user">User</div>
        <div class="paysum">Pay Sum</div>
        <div class="cardnum">Card #</div>
        <div class="cardsys">CC System</div>
        <div class="cvv">CVV</div>
        <div class="response">Response</div>
    </div>
    <div class="tablebody">
        <?php $numpp=0;?>
        <?php foreach ($data as $row) { ?>
        <div class="tabledatarow <?=($numpp%2==0 ? 'white' : 'grey')?>">
            <div class="date"><?=$row['paylog_date']?></div>
            <div class="user"><?=$row['user_name']?></div>
            <div class="paysum"><?=$row['paysum']?></div>
            <div class="cardnum"><?=$row['card_num']?></div>
            <div class="cardsys"><?=$row['card_system']?></div>
            <div class="cvv">
                <?=($row['cvv']==1 ? '<i class="fa fa-check-square-o" aria-hidden="true"></i>' : '<i class="fa fa-square-o" aria-hidden="true"></i>')?>
            </div>
            <div class="response <?=$row['payclass']?>"><?=$row['api_response']?></div>
        </div>
        <?php } ?>
    </div>
</div>