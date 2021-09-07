<form id="editbatchdata">
    <input type="hidden" id="batch_id" name="batch_id" value="<?=$batch_id?>"/>
    <input type="hidden" id="datedue" name="datedue" value="<?=$batch_due?>"/>
    <input type="hidden" id="batch_date" name="batch_date" value="<?=date('m/d/Y',$batch_date)?>"/>
    <div class="batchpaytable_totaltitle">
        <div class="batchpaytable_email" style="margin-top:-3px;">
            <input type="checkbox" name="batch_email" class="batch_email" value="1" <?=($batch_email==1 ? 'checked="checked"' : '')?>/>
        </div>
        <div class="batchpaytable_actions">
            <img src="/img/icons/accept.png" alt="Accept" id="acceptbatchrow" />
            <img src="/img/icons/close.png" alt="Cancel"  id="cancenbatchrow" />
        </div>
        <div class="batchpaytable_ordernum"><?=$order_num?></div>
        <div class="batchpaytable_customer" title="<?=$customer_name?>" data-content="<?=$customer_name?>">
            <?=$customer_name?>
        </div>                        
    </div>
    <div class="batchpaytable_totalvalues">
        <div class="batchpaytable_vmd">
            <input type="text" name="batch_vmd" id="batch_vmd" class="input_batch" value="<?=($batch_vmd==0 ? '' : $batch_amount )?>"/>            
        </div>
        
        <div class="batchpaytable_amex">
            <input type="text" name="batch_amex" id="batch_amex" class="input_batch" value="<?=($batch_amex==0 ? '' : $batch_amount )?>"/>                        
        </div>
        <div class="batchpaytable_other">
            <input type="text" name="batch_other" id="batch_other" class="input_batch" value="<?=($batch_other==0 ? '' : $batch_amount )?>"/>                            
        </div>
        <div class="batchpaytable_terms">
            <input type="text" name="batch_term" id="batch_term" class="input_batch" value="<?=($batch_term==0 ? '' : $batch_amount )?>"/>                            
        </div>
        <div class="batchpaytable_due">
            <?=$due_vie?>
        </div>
        <div class="batchpaytable_reach" style="margin-top:-3px;">
            <input type="checkbox" name="batch_received" class="batch_receiv" value="1" <?=($batch_received==1 ? 'checked="checked"' : '')?>/>
        </div>                                                
    </div>    
</form>