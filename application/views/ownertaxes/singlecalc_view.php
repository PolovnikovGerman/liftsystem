<div class="calcarea">
    <div class="header">Single Filing Calculator</div>
    <?=$rateview?>
    <div class="calcdataareadata">
        <div class="incomedataarea">
            <div class="label">
                <img src="/img/ownertaxes/income_label.png"/>
            </div>
            <div class="taxdatarow">
                <div class="label">Salary:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="salary" data-calc="single" value="<?=($salary==0 ? '' : MoneyOutput($salary,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="label"><?=round($profitkf*100,0)?>% OD:</div>
                <div class="taxvalue" data-fld="owner_drawer" data-calc="single"><?=($owner_drawer==0 ? '&nbsp;' : MoneyOutput($owner_drawer,2))?></div>
            </div>
            <div class="taxdatarow ">
                <div class="label">Other Income:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="other_income" data-calc="single" value="<?=($other_income==0 ? '' : MoneyOutput($other_income,2))?>"/>
                </div>                
            </div>            
            <div class="taxdatarow totalrow">
                <div class="label">TOTAL:</div>
                <div class="taxvalue total" data-fld="total_income" data-calc="single"><?=($total_income==0 ? '&nbsp;' : MoneyOutput($total_income,2))?></div>
            </div>
        </div>
        <div class="deducatedataarea">
            <div class="label">
                <img src="/img/ownertaxes/deducations_label.png"/>
            </div>    
            <div class="taxdatarow">
                <div class="label">401(k):</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="k401" data-calc="single" value="<?=($k401==0 ? '' : MoneyOutput($k401,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="label">Property Tax:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="property_tax" data-calc="single" value="<?=($property_tax==0 ? '' : MoneyOutput($property_tax,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="label">Mortgage Int:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="mortgage_int" data-calc="single" value="<?=($mortgage_int==0 ? '' : MoneyOutput($mortgage_int,2))?>"/>
                </div>
            </div>
            <div class="taxdatarow">
                <div class="label">Other Deduct:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="other_deduct" data-calc="single" value="<?=($other_deduct==0 ? '' : MoneyOutput($other_deduct,2))?>"/>
                </div>
            </div>
        </div>
        <div class="taxableincome">
            <div class="label">Taxable Income:</div>
            <div class="taxvalue" data-fld="taxable_income" data-calc="single"><?=$taxable_income==0 ? '&nbsp;' : MoneyOutput($taxable_income,2)?></div>
        </div>
        <div class="fedtaxesarea">
            <div class="label">
                <img src="/img/ownertaxes/fedtaxes_label.png"/>
            </div>
            <div class="taxdatarow">
                <div class="label">Federal Taxes:</div>
                <div class="taxvalue" data-fld="fed_taxes" data-calc="single"><?=($fed_taxes==0 ? '&nbsp;' : MoneyOutput($fed_taxes,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="label">- Withheld:</div>
                <div class="taxvalue" data-calc="single">
                    <input class="ownertaxdatainput" data-fld="fed_withheld" data-calc="single" value="<?=($fed_withheld==0 ? '' : MoneyOutput($fed_withheld,2))?>"/>
                </div>                
            </div>            
            <div class="taxdatarow">
                <div class="label">Fed Taxes Due:</div>
                <div class="taxvalue" data-fld="fed_taxes_due" data-calc="single"><?=($fed_taxes_due==0 ? '&nbsp;' : MoneyOutput($fed_taxes_due,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="label">4 Quarterly Pay:</div>
                <div class="taxvalue" data-fld="fed_pay" data-calc="single"><?=($fed_pay==0 ? '&nbsp;' : MoneyOutput($fed_pay,2))?></div>
            </div>            
        </div>        
        <div class="statetaxesarea">
            <div class="label">
                <img src="/img/ownertaxes/njtaxes_label.png"/>
            </div>
            <div class="taxdatarow">
                <div class="label">NJ Taxes:</div>
                <div class="taxvalue" data-fld="state_taxes" data-calc="single"><?=($state_taxes==0 ? '&nbsp;' : MoneyOutput($state_taxes,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="label" data-calc="single">- Withheld:</div>
                <div class="taxvalue">
                    <input class="ownertaxdatainput" data-fld="state_withheld" data-calc="single" value="<?=($state_withheld==0 ? '' : MoneyOutput($state_withheld,2))?>"/>
                </div>
            </div>            
            <div class="taxdatarow">
                <div class="label">NJ Taxes Due:</div>
                <div class="taxvalue" data-fld="state_taxes_due" data-calc="single"><?=($state_taxes_due==0 ? '&nbsp;' : MoneyOutput($state_taxes_due,2))?></div>
            </div>            
            <div class="taxdatarow">
                <div class="label">4 Quarterly Pay:</div>
                <div class="taxvalue" data-fld="state_pay" data-calc="single"><?=($state_pay==0 ? '&nbsp;' : MoneyOutput($state_pay,2))?></div>
            </div>            
        </div>
        <div class="othertaxesrow">
            <div class="label">Other Taxes:</div>
            <div class="taxvalue">
                <input class="ownertaxdatainput" data-fld="other_taxes" data-calc="single" value="<?=($other_taxes==0 ? '' : MoneyOutput($other_taxes,2))?>"/>
            </div>
        </div>
        <div class="takehomedatarow">
            <div class="label" >Take Home:</div>
            <div class="taxvalue" data-fld="take_home" data-calc="single"><?=($take_home==0 ? '&nbsp;' : MoneyOutput($take_home,2))?></div>
        </div>
    </div>    
</div>