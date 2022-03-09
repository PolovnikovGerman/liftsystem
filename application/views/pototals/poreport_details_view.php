<?php foreach ($datas as $data) { ?>
    <div class="row">
        <div class="col-3 poreportdatacell vendornamedat"><?=$data['vendor_name']?></div>
        <div class="col-3 poreportdatacell">
            <div class="row">
                <div class="col-12 poreportdata">
                    <?php if (empty($data['qty_year1'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position="left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=qty&y=<?=$data['year1']?>&b=<?=$brand?>">
                            <span><?=$data['qty_year1']?></span>&nbsp;POs
                        </div>
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['cost_year1'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=cost&y=<?=$data['year1']?>&b=<?=$brand?>">
                            <span><?=MoneyOutput($data['cost_year1'],0)?></span>&nbsp;Cost
                        </div>
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
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=profit&y=<?=$data['year1']?>&b=<?=$brand?>">
                            <span><?=MoneyOutput($data['profit_year1'],0)?></span>&nbsp;Profit
                        </div>
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
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=qty&y=<?=$data['year2']?>&b=<?=$brand?>">
                            <span><?=$data['qty_year2']?></span>&nbsp;POs
                        </div>
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['cost_year2'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=cost&y=<?=$data['year2']?>&b=<?=$brand?>">
                            <span><?=MoneyOutput($data['cost_year2'],0)?></span>&nbsp;Cost
                        </div>
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
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=profit&y=<?=$data['year2']?>&b=<?=$brand?>">
                            <span><?=MoneyOutput($data['profit_year2'],0)?></span>&nbsp;Profit
                        </div>
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
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=qty&y=<?=$data['year3']?>&b=<?=$brand?>">
                            <span><?=$data['qty_year3']?></span>&nbsp;POs
                        </div>
                    <?php } ?>
                </div>
                <div class="col-12 poreportdata">
                    <?php if (empty($data['cost_year3'])) { ?>
                        &nbsp;
                    <?php } else { ?>
                        <div class="poreportdatahint" data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=cost&y=<?=$data['year3']?>&b=<?=$brand?>">
                            <span><?=MoneyOutput($data['cost_year3'],0)?></span>&nbsp;Cost
                        </div>
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
                        <div class="poreportdatahint"data-event="<?=$event?>" data-css="poreport_detailmessage" data-bgcolor="#FFFFFF" data-bordercolor="#000" data-textcolor="#000"
                             data-position = "left" data-balloon="{ajax} /purchaseorders/poreport_yeardetails?v=<?=$data['vendor_id']?>&t=profit&y=<?=$data['year3']?>&b=<?=$brand?>">
                            <span><?=MoneyOutput($data['profit_year3'],0)?></span>&nbsp;Profit
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
