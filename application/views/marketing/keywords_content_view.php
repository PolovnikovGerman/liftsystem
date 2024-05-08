<?php if ($total==0) { ?>
    <div class="searcheskeywordssubhead">
        <div class="rank">#</div>
        <div class="keyword">Keyword</div>
        <div class="result">Searches</div>
    </div>
    <div class="searcheskeywordarea">
        <div class="datarow whitedatarow" style="text-align: center;">No data about Searches for this period</div>
    </div>
<?php } else { ?>
    <?php $idx = 0;?>
    <?php for ($i=1; $i<=$numcols; $i++) { ?>
        <div class="searcheskeywordssubhead <?=$i==$numcols ? 'lastcol' : ''?>">
            <div class="rank">#</div>
            <div class="keyword">Keyword</div>
            <div class="result">Searches</div>
        </div>
        <div class="searcheskeywordarea <?=$i==$numcols ? 'lastcol' : ''?>">
        <?php for ($i=0; $i<$limit; $i++) { ?>
            <div class="datarow <?=$i%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                <div class="rank"><?=$items[$idx]['rank']?></div>
                <div class="keyword"><?=$items[$idx]['keyword']?></div>
                <div class="result"><?=$item[$idx]['result']?></div>
            </div>
            <?php $idx++;?>
        <?php } ?>
        </div>
    <?php } ?>
<?php } ?>