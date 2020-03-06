<div class="batchpoptable_title">
    <div class="batchpoptable_totaltitle" style="border-right: 1px solid #6D6AFF">
        <div class="batchpoptable_email">E</div>    
        <div class="batchpoptable_ordernum">Order #</div>
        <div class="batchpoptable_customer">Customer</div>        
    </div>
    <div class="batchpoptable_totalvalues">
        <div class="batchpoptable_vmd">V-M-D</div>
        <div class="batchpoptable_amex">Amex</div>
        <div class="batchpoptable_other">Other</div>
        <div class="batchpoptable_terms">Terms</div>
        <div class="batchpoptable_due">Due</div>
        <div class="batchpoptable_reach">R</div>        
    </div>
</div>
<div class="batchpoptable_totalinvoiced">
    <div class="batchpoptable_totaltitle">Total Invoiced:</div>
    <div class="batchpoptable_totalvalues">
        <div class="batchpoptable_vmd <?=$totals['inv_vmdclass']?>"><?=$totals['inv_vmd']?></div>
        <div class="batchpoptable_amex <?=$totals['inv_amexclass']?>"><?=$totals['inv_amex']?></div>
        <div class="batchpoptable_other <?=$totals['inv_otherclass']?>"><?=$totals['inv_other']?></div>
        <div class="batchpoptable_terms <?=$totals['inv_termclass']?>"><?=$totals['inv_term']?></div>
        <div class="batchpoptable_due">&nbsp;</div>
        <div class="batchpoptable_reach">&nbsp;</div>                        
    </div>
</div>
<div class="batchpoptable_notreceiv">
    <div class="batchpoptable_totaltitle">Total Not Yet Received:</div>
    <div class="batchpoptable_totalvalues">
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
    <div class="batchpopdetailtable_orders <?=($nrow%2==0 ? 'white' : 'grey')?>">
        <div class="batchpoptable_totaltitle">
            <div class="batchpoptable_email">
                <input type="checkbox" disabled="disabled" class="batchemail" style="margin-top:-2px;" id="btchem<?=$row['batch_id']?>" <?=($row['batch_email']==1 ? 'checked="checked"' : '')?>/>
            </div>
            <div class="batchpoptable_ordernum"><?=$row['order_num']?></div>
            <div class="batchpoptable_customer" title="<?=$row['customer_name']?>"><?=$row['customer_name']?></div>                        
        </div>
        <div class="batchpoptable_totalvalues">
            <div class="batchpoptable_vmd <?=$row['vmd_class']?>"><?=$row['batch_vmd']?></div>
            <div class="batchpoptable_amex <?=$row['amex_class']?>"><?=$row['batch_amex']?></div>
            <div class="batchpoptable_other <?=$row['other_class']?>"><?=$row['batch_other']?></div>
            <div class="batchpoptable_terms <?=$row['term_class']?>"><?=$row['batch_term']?></div>
            <div class="batchpoptable_due"><?=$row['batch_due']?></div>
            <div class="batchpoptable_reach">
                <input type="checkbox" disabled="disabled" class="batchreceiv" style="margin-top:-2px;" id="btchrc<?=$row['batch_id']?>" <?=($row['batch_received']==1 ? 'checked="checked"' : '')?>/>
            </div>                                                
        </div>
    </div>
    <?php $nrow++;?>
<?php } ?>
