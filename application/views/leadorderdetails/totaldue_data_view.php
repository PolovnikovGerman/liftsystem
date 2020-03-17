<div class="block_8 <?=$class=='closed' ? 'text_black' : 'text_white'?>">
    <?php if ($class=='closed') { ?>
        <div class="block_9">
            <div class="block_9_text">
                <div class="block_8bottom_text1">Total Due:</div>
                <div class="block_8_text2 text_bold">PAID</div>
            </div>
        </div>
    <?php } else { ?>
        <div class="block_8_backgr1">&nbsp;</div>
        <div class="block_8_backgr2 block_8_text">
            <div class="block_8bottom_text1">Total Due:</div>
            <div class="block_8_text2 text_bold <?=($class=='open' ? 'text_white' : 'text_lightred blinked')?>"><?=($totaldue<0 ? '(' : '').MoneyOutput(abs($totaldue)).($totaldue<0 ? ')' : '')?></div>
        </div>
        <div class="block_8_backgr3">&nbsp;</div>                       
    <?php } ?>    
</div>