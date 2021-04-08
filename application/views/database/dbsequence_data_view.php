<div class="content_header">
    <div class="legend"><?= $legend ?></div>
</div>
<div id="dbsequencecontent">
    <input type="hidden" id="totalrecdbseq" value="<?= $total_rec ?>"/>
    <input type="hidden" id="seqpagenum" value="<?= $cur_page ?>"/>
    <input type="hidden" id="itemsequencebrand" value="<?=$brand?>"/>
    <div class="tabinfohead">
        <div class="sequencinforow">
            <div class="title">First 10 Get Top Seller Label</div>
            <div class="dbseqpagination" id="dbseqPagination"></div>
            <div class="pagination-legend"></div>
        </div>
    </div>
    <div class="dbseqtabinfo" id="dbseqtabinfo"></div>
</div>