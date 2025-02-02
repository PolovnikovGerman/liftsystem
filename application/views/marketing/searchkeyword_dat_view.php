<?php if ($numrecs!=0) {?>
<?php $idx=0; $nrow=1;?>
<div class="search_keywords_header">
    <?php for ($j=1; $j<=$numcols;$j++) { ?>
        <div class="search_keywords_headarea">
            <div class="search_keywords_head_rank">Rank</div>
            <div class="search_keywords_head_keyword">Keyword</div>
            <div class="search_keywords_head_result">Searches</div>
        </div>
    <?php } ?>
    <div class="search_keywords_dataarea">
        <?php for ($j=0; $j<$numrows; $j++) { ?>
            <?php for ($i=1; $i<=$numcols; $i++) { ?>
                <div class="search_keywords_datarow <?=$j%2==0 ? 'greydatarow' : 'whitedatarow'?> <?=$i==$numcols ? 'lastrow' : ''?>">
                    <?php if ($idx>=$numrecs) { ?>
                        <div class="search_keywords_data_rank">&nbsp;</div>
                        <div class="search_keywords_data_keyword">&nbsp;</div>
                        <div class="search_keywords_data_negativeresult">&nbsp;</div>
                        <div class="search_keywords_data_result">&nbsp;</div>
                    <?php } else { ?>
                        <div class="search_keywords_data_rank"><?=$nrow?></div>
                        <div class="search_keywords_data_keyword"><?=$dat[$idx]['search_text']?></div>
                        <div class="search_keywords_data_negativeresult"><?=($dat[$idx]['search_result']=='1'? '&nbsp;' : 'N')?></div>
                        <div class="search_keywords_data_result"><?=$dat[$idx]['cnt']?></div>
                    <?php } ?>
                </div>
                <?php $nrow++;?>
                <?php $idx++;?>
            <?php } ?>
        <?php } ?>
    </div>
</div>
<?php } else { ?>
    <div style="float: left;width:969px;font-size: 14px; font-weight: bold">
        No data about Searches for this period
    </div>
<?php } ?>

