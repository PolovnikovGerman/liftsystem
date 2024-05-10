<?php $idx = 0;?>
<?php for ($i=1; $i<=$numcols; $i++) { ?>
    <div class="searcheskeywordsarea">
        <div class="searcheskeywordssubhead <?=$i==$numcols ? 'lastcol' : ''?>">
            <div class="rank">#</div>
            <div class="keyword">Keyword</div>
            <div class="result">Searches</div>
        </div>
        <div class="searcheskeywordarea <?=$i==$numcols ? 'lastcol' : ''?> <?=$i==1 ? 'firstcol' : ''?>">
        <?php for ($j=0; $j<$limit; $j++) { ?>
            <div class="datarow <?=$j%2==0 ? 'whitedatarow' : 'greydatarow'?>">
            <?php if ($idx>=$total) { ?>
                <div class="rank">&nbsp;</div>
                <div class="keyword">&nbsp;</div>
                <div class="negativeflag">&nbsp;</div>
                <div class="result">&nbsp;</div>
            <?php } else { ?>
                <div class="rank"><?=$items[$idx]['rank']?></div>
                <div class="keyword"><?=$items[$idx]['keyword']?></div>
                <div class="negativeflag"><?=$items[$idx]['result']==1 ? '&nbsp;' : 'N'?></div>
                <div class="result"><?=$items[$idx]['searches']?></div>
            <?php } ?>
            </div>
            <?php $idx++;?>
        <?php } ?>
        </div>
    </div>
<?php } ?>
