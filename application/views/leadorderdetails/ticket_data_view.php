<div class="block_8 <?=$class=='open' ? 'text_white' : 'text_black'?>">
    <?php if ($class=='open') { ?>
        <div class="block_8_backgr1">&nbsp;</div>
        <div class="block_8_backgr2 block_8_text"><?=$label?></div>
        <div class="block_8_backgr3">&nbsp;</div>                       
    <?php } else { ?>
        <div class="block_9">
            <div class="block_9_text"><?=$label?></div>
        </div>
    <?php } ?>    
</div>
