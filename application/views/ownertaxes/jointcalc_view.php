<div class="calcarea jointcalc">
    <div class="header">Joint Filing Calculator</div>
    <?=$rateview?>
    <div class="calcdataareadata">
        <div class="incomedataarea jointcalc">
            <div class="labeltxt">
                <img src="/img/ownertaxes/income_label.png"/>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Salary:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="salary" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($salary==0 ? '' : MoneyOutput($salary,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt"><?=round($profitkf*100,0)?>% OD:</div>
                <div class="taxvalue" data-fld="owner_drawer" data-calc="joint"><?=($owner_drawer==0 ? '&nbsp;' : MoneyOutput($owner_drawer,2))?></div>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Partner Income:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="partner_income" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($partner_income==0 ? '' : MoneyOutput($partner_income,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Other Income:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="other_income" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($other_income==0 ? '' : MoneyOutput($other_income,2))?>"/>
                </div>                
            </div>            
            <div class="taxdatarow totalrow">
                <div class="labeltxt">TOTAL:</div>
                <div class="taxvalue total" data-fld="total_income" data-calc="joint"><?=($total_income==0 ? '&nbsp;' : MoneyOutput($total_income,2))?></div>
            </div>
        </div>
        <div class="deducatedataarea">
            <div class="labeltxt">
                <img src="/img/ownertaxes/deducations_label.png"/>
            </div>    
            <div class="taxdatarow">
                <div class="labeltxt">401(k):</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="k401" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($k401==0 ? '' : MoneyOutput($k401,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Property Tax:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="property_tax" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($property_tax==0 ? '' : MoneyOutput($property_tax,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Mortgage Int:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="mortgage_int" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($mortgage_int==0 ? '' : MoneyOutput($mortgage_int,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Other Deduct:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="other_deduct" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($other_deduct==0 ? '' : MoneyOutput($other_deduct,2))?>"/>
                </div>
            </div>
        </div>
        <div class="taxableincome">
            <div class="labeltxt">Taxable Income:</div>
            <div class="taxvalue" data-fld="taxable_income" data-calc="joint"><?=$taxable_income==0 ? '&nbsp;' : MoneyOutput($taxable_income,2)?></div>
        </div>
        <div class="fedtaxesarea">
            <div class="labeltxt">
                <img src="/img/ownertaxes/fedtaxes_label.png"/>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">Federal Taxes:</div>
                <div class="taxvalue" data-fld="fed_taxes" data-calc="joint"><?=($fed_taxes==0 ? '&nbsp;' : MoneyOutput($fed_taxes,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="labeltxt">- Withheld:</div>
                <div class="taxvalue" data-calc="joint">
                    <input class="ownertaxdatainput" data-fld="fed_withheld" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($fed_withheld==0 ? '' : MoneyOutput($fed_withheld,2))?>"/>
                </div>                
            </div>            
            <div class="taxdatarow">
                <div class="labeltxt">Fed Taxes Due:</div>
                <div class="taxvalue" data-fld="fed_taxes_due" data-calc="joint"><?=($fed_taxes_due==0 ? '&nbsp;' : MoneyOutput($fed_taxes_due,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="labeltxt">4 Quarterly Pay:</div>
                <div class="taxvalue" data-fld="fed_pay" data-calc="joint"><?=($fed_pay==0 ? '&nbsp;' : MoneyOutput($fed_pay,2))?></div>
            </div>            
        </div>        
        <div class="statetaxesarea">
            <div class="labeltxt">
                <img src="/img/ownertaxes/njtaxes_label.png"/>
            </div>
            <div class="taxdatarow">
                <div class="labeltxt">NJ Taxes:</div>
                <div class="taxvalue" data-fld="state_taxes" data-calc="joint"><?=($state_taxes==0 ? '&nbsp;' : MoneyOutput($state_taxes,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="labeltxt" data-calc="joint">- Withheld:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="state_withheld" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($state_withheld==0 ? '' : MoneyOutput($state_withheld,2))?>"/>
                </div>
            </div>            
            <div class="taxdatarow">
                <div class="labeltxt">NJ Taxes Due:</div>
                <div class="taxvalue" data-fld="state_taxes_due" data-calc="joint"><?=($state_taxes_due==0 ? '&nbsp;' : MoneyOutput($state_taxes_due,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="labeltxt">4 Quarterly Pay:</div>
                <div class="taxvalue" data-fld="state_pay" data-calc="joint"><?=($state_pay==0 ? '&nbsp;' : MoneyOutput($state_pay,2))?></div>
            </div>            
        </div>
        <div class="othertaxesrow">
            <div class="labeltxt">Other Taxes:</div>
            <div class="taxvalue">
                <input class="ownertaxdatainput" data-fld="other_taxes" data-calc="joint" <?=$brand=='ALL' ? 'readonly="readonly"' : ''?> value="<?=($other_taxes==0 ? '' : MoneyOutput($other_taxes,2))?>"/>
            </div>
        </div>
        <div class="takehomedatarow">
            <div class="labeltxt" >Take Home:</div>
            <div class="taxvalue" data-fld="take_home" data-calc="joint"><?=($take_home==0 ? '&nbsp;' : MoneyOutput($take_home,2))?></div>
        </div>
    </div>    
</div>