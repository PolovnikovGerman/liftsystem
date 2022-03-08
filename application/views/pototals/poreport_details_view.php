<?php foreach ($datas as $data) { ?>
    <div class="row">
        <div class="col-3 poreportdatacell vendornamedat"><?=$data['vendor_name']?></div>
        <div class="col-3 poreportdatacell">
            <div class="row">
                <div class="col-12 poreportdata">
                    <?php if (empty($data['qty_year1'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=$data['qty_year1']?></span> POs
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['cost_year1'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=MoneyOutput($data['cost_year1'],0)?></span> Cost
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata <?=$data['profitclass_year1']?>">
                    <?php if (empty($data['avgprof_year1'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=round($data['avgprof_year1'],0)?>%</span> Avg Profit
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['profit_year1'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=MoneyOutput($data['profit_year1'],0)?></span> Profit
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-3 poreportdatacell">
            <div class="row">
                <div class="col-12 poreportdata">
                    <?php if (empty($data['qty_year2'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=$data['qty_year2']?></span> POs
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['cost_year2'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=MoneyOutput($data['cost_year2'],0)?></span> Cost
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata <?=$data['profitclass_year2']?>">
                    <?php if (empty($data['avgprof_year2'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=round($data['avgprof_year2'],0)?>%</span> Avg Profit
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['profit_year2'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=MoneyOutput($data['profit_year2'],0)?></span> Profit
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="col-3 poreportdatacell">
            <div class="row">
                <div class="col-12 poreportdata">
                    <?php if (empty($data['qty_year3'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=$data['qty_year3']?></span> POs
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['cost_year3'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=MoneyOutput($data['cost_year3'],0)?></span> Cost
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata <?=$data['profitclass_year3']?>">
                    <?php if (empty($data['avgprof_year3'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=round($data['avgprof_year3'],0)?>%</span> Avg Profit
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['profit_year3'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <span><?=MoneyOutput($data['profit_year3'],0)?></span> Profit
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
