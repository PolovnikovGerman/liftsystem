<div style="clear: both; float: left; width: 652px;">
    <?php if ($rushmsg!='') { ?>
        <?=$doc_name?> - Change Production to <?=$rushmsg?>
    <?php } ?>
    <?php if (count($logos)>0) {?>
        <div style="clear:both; float: left; width: 650px; text-align: center; font-size: 16px;">
            New Logos Added to Redraw Queue
        </div>
        <div style="clear:both; float: left; width: 650px; text-align: left; font-size: 14px;">
            <?=date('m/d/Y H:i:s')?> added new logos to Redraw Queue - <?=$doc_name?>
        </div>
        <?php foreach ($logos as $row) {?>
            <div style="clear:both; float: left; width: 650px; text-align: left; font-size: 14px;">
                <?=$row['deed']?> - <?=$row['logo_src']?>
            </div>
        <?php } ?>
    <?php } ?>
    <div style="clear:both; float: left; width: 650px; text-align: center; font-size: 16px;">
        Regards,<br>Redraw Team
    </div>
</div>