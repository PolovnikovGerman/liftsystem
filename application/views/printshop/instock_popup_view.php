<div class="stockdataarea">
    <div class="stockheaddate">
        <?php if ($brand!=='ALL') { ?>
            <div class="addstock" data-brand="<?=$brand?>" data-color="<?=$color?>"><i class="fa fa-plus-circle"></i></div>
        <?php } else { ?>
            <div class="emptyaddstock">&nbsp;</div>
        <?php } ?>
        <div class="stockdate">Date</div>
        <div class="stockdescr">Desc</div>
        <div class="stockamnt">Amnt</div>
        <div class="stockbalance">Balance</div>    
    </div>
    <div class="stockcontentdata">
        <?= $content ?>
    </div>    
</div>
