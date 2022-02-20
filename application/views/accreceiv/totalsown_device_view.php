<div class="row">
    <div class="col-7 accreceiv-totalown">
        <div class="row">
            <div class="col-6 accreceiv-totalown-title">Owed to Us:</div>
            <div class="col-6 accreceiv-totalown-value"><?= TotalOutput($totalown) ?></div>
        </div>
    </div>
    <div class="col-5 accreceiv-totalpast">
        <div class="accreceiv-totalpast-title">Past Due:</div>
        <div class="accreceiv-totalpast-value"><?= TotalOutput($pastown) ?></div>
    </div>
</div>
<div class="row">
    <div class="col-12 accreceiv-totalown-table">
        <?php foreach ($own as $row) { ?>
            <div class="accreceiv-totalown-cell">
                <div class="accreceiv-totalown-cell-year"><?= $row['year'] ?></div>
                <div class="accreceiv-totalown-cell-value"><?= $row['balance'] == 0 ? '---' : TotalOutput($row['balance']) ?></div>
            </div>
        <?php } ?>
    </div>
</div>
