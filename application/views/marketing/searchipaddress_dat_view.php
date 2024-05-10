<?php if ($numrecs!=0) {?>
    <?php $idx=0;?>
    <div class="search_ipaddress_header">
        <?php for ($j=1; $j<=$numcols;$j++) { ?>
            <div class="search_ipaddress_headarea">
                <div class="search_ipaddress_head_rank">Rank</div>
                <div class="search_ipaddress_head_ipadrs">IP Address</div>
                <div class="search_ipaddress_head_result">Searches</div>
                <div class="search_ipaddress_head_location">Location</div>
            </div>
        <?php } ?>
    </div>
    <div class="search_ipaddress_dataarea">
        <?php for ($j=0; $j<$numrows; $j++) { ?>
            <?php for ($i=1; $i<=$numcols; $i++) { ?>
                <div class="search_ipaddress_datarow <?=$j%2==0 ? 'greydatarow' : 'whitedatarow'?> <?=$i==$numcols ? 'lastrow' : ''?>">
                    <?php if ($idx>=$numrecs) { ?>
                        <div class="search_ipaddress_data_rank">&nbsp;</div>
                        <div class="search_ipaddress_data_ipadrs">&nbsp;</div>
                        <div class="search_ipaddress_data_result">&nbsp;</div>
                        <div class="search_ipaddress_data_location">&nbsp;</div>
                    <?php } else { ?>
                        <div class="search_ipaddress_data_rank"><?=$dat[$idx]['rank']?></div>
                        <div class="search_ipaddress_data_ipadrs"><?=$dat[$idx]['search_ip']?></div>
                        <div class="search_ipaddress_data_result"><?=$dat[$idx]['cnt']?></div>
                        <div class="search_ipaddress_data_location"><?=$dat[$idx]['search_user']?></div>
                    <?php } ?>
                </div>
                <?php $idx++;?>
            <?php } ?>
        <?php } ?>
    </div>
<?php } else { ?>
    <div style="font-size: 14px; font-weight: bold">
        No data about Searches for this period
    </div>
<?php } ?>
