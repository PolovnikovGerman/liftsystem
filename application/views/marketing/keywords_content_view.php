<?php if ($total==0) { ?>
    <div class="searcheskeywordsarea">
        <div class="searcheskeywordssubhead">
            <div class="rank">#</div>
            <div class="keyword">Keyword</div>
            <div class="result">Searches</div>
        </div>
        <div class="searcheskeywordarea">
            <div class="datarow whitedatarow" style="text-align: center;">No data about Searches for this period</div>
        </div>
    </div>
<?php } else { ?>
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
                <div class="rank"><?=$items[$idx]['rank']?></div>
                <div class="keyword"><?=$items[$idx]['keyword']?></div>
                <div class="negativeflag"><?=$items[$idx]['result']==1 ? '&nbsp;' : 'N'?></div>
                <div class="result"><?=$items[$idx]['searches']?></div>
            </div>
            <?php $idx++;?>
            <?php if ($idx>=$total) { ?>
                <?php break;?>
            <?php } ?>
        <?php } ?>
        </div>
        </div>
    <?php } ?>
<?php } ?>