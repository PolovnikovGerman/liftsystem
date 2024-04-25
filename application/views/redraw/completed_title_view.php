<input type="hidden" id="completeperpage" value="<?=$perpage?>"/>
<input type="hidden" id="completeorderby" value="<?=$order?>"/>
<input type="hidden" id="completedirection" value="<?=$direc?>"/>
<input type="hidden" id="completetotal" value="<?=$total?>"/>
<input type="hidden" id="completecurpage" value="<?=$curpage;?>"/>

<div class="datarow">
    <div class="completed_totals">
        <div class="completed_jobs_label"># Jobs</div>
        <div class="completed_jobs_value" id="completejobs"><?=$total?></div>
        <div class="completed_jobs_label">Avg Time</div>
        <div class="completed_jobs_value" id="completeavgtime"><?=$avg_time?></div>
        <div class="completed_jobs_label avgtime">Avg Rush Time</div>
        <div class="completed_jobs_value" id="completeavgrushtime"><?=$avg_rush?></div>
    </div>
    <div class="completed_pagesviews">
        <div class="Pagination"></div>
    </div>
</div>
<div class="datarow">
    <div class="completed_title">
        <div class="completed_numpp_title">#</div>
        <div class="rushtitle">Rush</div>
        <div class="prooftitle">Request #</div>
        <div class="completed_ordernum_title">Order #</div>
        <div class="completed_srcfile_title">Original File</div>
        <div class="completed_srcfile_title">Vector File</div>
        <div class="completed_date_title">Completed: (Date)</div>
        <div class="completed_totaltime_title">Total Time</div>
    </div>
    <div class="datarow">
        <div class="content-art-table"></div>
    </div>
</div>
