<div class="batchdetailtitle">
    <div class="batchdetailtitle_title">Batch:</div>
    <div class="batchdetailtitle_date"><?=$totals['out_date']?></div>
    <div class="batchdetailtitle_manual" data-batchdate="<?=$totals['batch_date']?>" title="Add Manual batch"><img src="/img/manual_batch_add.png" alt="Add Manual"/></div>
    <div class="batchdetailtitle_results"><?=$totals['day_results']?></div>
</div>
<div class="batchdetailtable">
    <div class="batchdetailtable_title">
        <div class="batchpaytable_email">E</div>
        <div class="batchpaytable_actions">&nbsp;</div>
        <div class="batchpaytable_ordernum">Order #</div>
        <div class="batchpaytable_customertitle">Customer</div>
        <div class="batchpaytable_vmd">V-M-D</div>
        <div class="batchpaytable_amex">Amex</div>
        <div class="batchpaytable_other">Other</div>
        <div class="batchpaytable_terms">Terms</div>
        <div class="batchpaytable_due">Due</div>
        <div class="batchpaytable_reach">R</div>
    </div>
    <div class="batchdetailtable_totalinvoiced">
        <div class="batchdetailtable_totaltitle">Total Invoiced:</div>
        <div class="batchdetailtable_totalvalues">
            <div class="batchpaytable_vmd <?=$totals['inv_vmdclass']?>"><?=$totals['inv_vmd']?></div>
            <div class="batchpaytable_amex <?=$totals['inv_amexclass']?>"><?=$totals['inv_amex']?></div>
            <div class="batchpaytable_other <?=$totals['inv_otherclass']?>"><?=$totals['inv_other']?></div>
            <div class="batchpaytable_terms <?=$totals['inv_termclass']?>"><?=$totals['inv_term']?></div>
            <div class="batchpaytable_due">&nbsp;</div>
            <div class="batchpaytable_reach">&nbsp;</div>
        </div>
    </div>
    <div class="batchdetailtable_notreceiv">
        <div class="batchdetailtable_totaltitle">Total Not Yet Received:</div>
        <div class="batchdetailtable_totalvalues">
            <div class="batchpaytable_vmd <?=$totals['deb_vmdclass']?>"><?=$totals['deb_vmd']?></div>
            <div class="batchpaytable_amex <?=$totals['deb_amexclass']?>"><?=$totals['deb_amex']?></div>
            <div class="batchpaytable_other <?=$totals['deb_otherclass']?>"><?=$totals['deb_other']?></div>
            <div class="batchpaytable_terms <?=$totals['deb_termclass']?>"><?=$totals['deb_term']?></div>
            <div class="batchpaytable_due">&nbsp;</div>
            <div class="batchpaytable_reach">&nbsp;</div>
        </div>
    </div>
    <?php $nrow=0;?>
    <?php foreach ($details as $row) {?>
        <div class="batchdetailtable_orders <?=($nrow%2==0 ? 'white' : 'grey')?>" id="batchrow<?=$row['batch_id']?>">
            <div class="batchpaytable_totaltitle">
                <div class="batchpaytable_email <?=$row['emailed_class']?>" style="margin-top:-3px;">
                    <input type="checkbox" class="batchemail" id="btchem<?=$row['batch_id']?>" <?=($row['batch_email']==1 ? 'checked="checked"' : '')?>/>
                </div>
                <div class="batchpaytable_actions">
                    <img src="/img/edit.png" alt="edit" class="editbatchrow" id="editbatchrow<?=$row['batch_id']?>" />
                    <!-- <img src="/img/delete.png" alt="delete" class="delbatchrow" id="delbatchrow<?=$row['batch_id']?>" /> -->
                </div>
                <div class="batchpaytable_ordernum"><?=$row['order_num']?></div>
                <div class="batchpaytable_note <?=$row['batchnote_class']?>" title="<?=$row['batchnote_title']?>" id="batchnote<?=$row['batch_id']?>"><?=$row['batchnote']?></div>
                <div class="batchpaytable_customer" title="<?=$row['customer_name']?>"><?=$row['customer_name']?></div>
            </div>
            <div class="batchpaytable_totalvalues">
                <div class="batchpaytable_vmd <?=$row['vmd_class']?>"><?=$row['batch_vmd']?></div>
                <div class="batchpaytable_amex <?=$row['amex_class']?>"><?=$row['batch_amex']?></div>
                <div class="batchpaytable_other <?=$row['other_class']?>"><?=$row['batch_other']?></div>
                <div class="batchpaytable_terms <?=$row['term_class']?>"><?=$row['batch_term']?></div>
                <div class="batchpaytable_due"><?=$row['batch_due']?></div>
                <div class="batchpaytable_reach <?=$row['received_class']?>" style="margin-top:-3px;">
                    <input type="checkbox" class="batchreceiv" id="btchrc<?=$row['batch_id']?>" <?=($row['batch_received']==1 ? 'checked="checked"' : '')?>/>
                </div>
            </div>
        </div>
        <?php $nrow++;?>
    <?php } ?>
</div>
