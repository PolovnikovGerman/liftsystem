<div class="artpopup_historyarea">
    <div class="artpopup_custominstuctlabel">History:</div>
    <div class="artpopup_historydata">
        <?php foreach ($history as $row) {?>
            <div class="artpopup_historyhead"><?=$row['history_head']?></div>
            <div class="artpopup_historymsg"><?=$row['message']?></div>
            <?php if ($row['parsed_lnk']) { ?>
                <div class="<?=$row['parsed_class']?>" id="historyshow<?=$row['artwork_history_id']?>" href="/art/art_parsedbodyview?id=<?=$row['artwork_history_id']?>"
                     data-arthistoryid="<?=$row['artwork_history_id']?>">
                    <?=$row['parsed_lnk']?>
                </div>
            <?php } ?>
            <div class="artpopup_historymsg">----------------------------------------------------------------------------</div>
        <?php } ?>
    </div>
</div>
