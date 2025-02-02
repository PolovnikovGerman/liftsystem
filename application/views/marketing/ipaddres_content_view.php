<?php $idx = 0;?>
<?php for ($i=1; $i<=$numcols; $i++) { ?>
<div class="searchesipaddrsarea">
    <div class="searchesipaddrsubhead <?=$i==$numcols ? 'lastcol' : ''?>">
        <div class="rank">#</div>
        <div class="keyword">IP Address</div>
        <div class="result">Qty</div>
        <div class="location">Location</div>
    </div>
    <div class="searchesipaddrarea <?=$i==$numcols ? 'lastcol' : ''?> <?=$i==1 ? 'firstcol' : ''?>">
        <?php for ($j=0; $j<$limit; $j++) { ?>
            <div class="datarow <?=$j%2==0 ? 'whitedatarow' : 'greydatarow'?>">
                <?php if ($idx>=$total) { ?>
                    <div class="rank">&nbsp;</div>
                    <div class="keyword">&nbsp;</div>
                    <div class="result">&nbsp;</div>
                    <div class="location">&nbsp;</div>
                <?php } else { ?>
                    <div class="rank"><?=$items[$idx]['rank']?></div>
                    <div class="keyword"><?=$items[$idx]['ipaddres']?></div>
                    <div class="result"><?=$items[$idx]['searches']?></div>
                    <div class="location"><?=$items[$idx]['location']?></div>
                <?php } ?>
            </div>
            <?php $idx++;?>
        <?php } ?>
    </div>
</div>
<?php } ?>
