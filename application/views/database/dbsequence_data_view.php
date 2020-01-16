<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbcontent">
    <input type="hidden" id="totalrecdbseq" value="<?= $total_rec ?>"/>
    <input type="hidden" id="seqpagenum" value="<?= $cur_page ?>"/>
    <div class="tabinfohead">
        <div class="sequencinforow">
            <div class="title">First 10 Get Top Seller Label</div>
            <div class="pagination" id="dbseqPagination"></div>
            <div class="pagination-legend"></div>
        </div>
    </div>
    <div class="tabinfo" id="dbseqtabinfo"></div>
</div>