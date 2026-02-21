<input type="hidden" id="btitemsperpage" value="<?=$perpage?>"/>
<input type="hidden" id="btitemsorder" value="<?=$order?>"/>
<input type="hidden" id="btitemsorderdirect" value="<?=$direct?>"/>
<input type="hidden" id="btipriceorder" value="<?=$order?>"/>
<input type="hidden" id="btipriceorderdirect" value="<?=$direct?>"/>
<input type="hidden" id="btitemstotals" value="<?=$totals?>"/>
<input type="hidden" id="btitemspagenum" value="0"/>
<input type="hidden" id="btitemsvendor" value=""/>
<input type="hidden" id="btitemtab" value="complete"/>
<div class="itemlistview" data-brand="<?=$brand?>">
    <div class="pageheader">
        <div class="pagetitle">Item Center</div>
        <div class="pageitemstatistic">
            <div class="totalactiveitems">
                <div class="totalactiveitemslabel">TOTAL ACTIVE:</div>
                <div class="totalactiveitemsvalue"><?=$activeitms?></div>
            </div>
            <div class="totalcompleteditems">
                <div class="datarow">
                    <div class="totalcompleteditemslabel">Complete:</div>
                    <div class="totalcompleteditemsvalue"><?=$completed_items?> - <?=$completed_perc?>%</div>
                </div>
                <div class="datarow">
                    <div class="totalcompleteditemslabel">Incomplete:</div>
                    <div class="totalcompleteditemsvalue"><?=$uncompleted_items?> - <?=$uncompleted_perc?>%</div>
                </div>
            </div>
        </div>
        <div class="pageheadersearcharea">
            <div class="pageheadfilter">
                <select class="itemcategoryfilter">
                    <?php foreach ($categories as $category) { ?>
                        <option data-categ="<?=$category['category_id']?>" <?=$category['category_active']==1 ? '' : 'disabled="true"'?> value="<?=$category['category_id']?>" <?=$category['category_id']==$category_id ? 'selected="selected"' : ''?>>
                            <?=$category['category_code'].' - '.$category['category_name']?>
                        </option>
                    <?php } ?>
                </select>
            </div>
            <div class="pageheadfilter">
                <input class="itemnamesearch" placeholder="Search"/>
                <div class="itemsearchbtn">
                    <i class="fa fa-search" aria-hidden="true"></i>
                </div>
            </div>
            <div class="itemclearsearch">Clear</div>
        </div>
    </div>
    <div class="pageheadcategories">
        <?php $numpp=0;?>
        <?php $numsep=0;?>
        <?php foreach ($categories as $category) { ?>
            <?php if ($numpp%10==0) { ?>
                <div class="content-row">
            <?php } ?>
            <div class="btcategorybtn <?=$category['category_id']==$category_id ? 'active' : ''?> <?=$category['category_active']==0 ? 'locked' : ''?> <?=$category['category_separate']==1 ? ($numsep==0 ? 'separatefirst' : 'separate') : ''?>"
                 data-category="<?=$category['category_id']?>">
                <?=$category['category_code']?> - <?=$category['category_name']?>
                <?php if ($category['category_items'] > 0) : ?>
                <span>(<?=$category['category_items']?>)</span>
                <?php endif; ?>
            </div>
            <?php $numpp++;?>
            <?php if ($category['category_separate']==1) $numsep++; ?>
            <?php if ($numpp%10==0) { ?>
                </div>
            <?php } ?>
        <?php } ?>
        <?php if ($numpp%10!=0) { ?>
            </div>
        <?php } ?>
    </div>
    <div class="datatablearea">
        <div class="tabledatatitle"><?=$category_label?></div>
        <div class="tabledatatotals">
            <div class="datarow">
                <div class="categoryactivestatistics">&nbsp;</div>
            </div>
            <div class="datarow">
                <div class="tabledatafilter">
                    <select class="itemstatusfilter">
                        <option value="0">Active & Inactive</option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                </div>
                <div class="tabledatafilter">
                    <select class="itemmisinfofilter">
                        <option value="0">Complete & Not</option>
                        <option value="1">Complete</option>
                        <option value="2">Not Complete</option>
                    </select>
                </div>
            </div>
            <div class="datarow">
                <div class="tabledatataotalvalue"><?=$totals==0 ? '' : 'Displaying '.QTYOutput($totals).' item(s)'?></div>
            </div>
        </div>
        <div class="tabledatavendorsview">
            &nbsp;
        </div>
        <div class="tabledataexecute">
            <div class="datarow">
                <div class="tabledataexport">
                    <i class="fa fa-share-square-o" aria-hidden="true"></i>
                    <span>Export Item List</span>
                </div>
            </div>
            <div class="datarow">
                <div class="tabledatapaginator" id="btitemsPaginator"></div>
            </div>
        </div>
        <div class="tabledataheader-tabs">
            <div class="tabledataheader-tab active" data-tab="complete">Completeness</div>
            <div class="tabledataheader-tab" data-tab="pricing">Pricing/Profit</div>
        </div>
        <div class="tabledataheader_profitmap">
            <div class="profitmapview greenhight">&nbsp;</div>
            <div class="profitmaplegend">50%+</div>
            <div class="profitmapview green">&nbsp;</div>
            <div class="profitmaplegend">40%'s</div>
            <div class="profitmapview white">&nbsp;</div>
            <div class="profitmaplegend">30%'s</div>
            <div class="profitmapview orange">&nbsp;</div>
            <div class="profitmaplegend">20%'s</div>
            <div class="profitmapview red">&nbsp;</div>
            <div class="profitmaplegend">10%'s</div>
            <div class="profitmapview maroon">&nbsp;</div>
            <div class="profitmaplegend">0%'s</div>
            <div class="profitmapview black">&nbsp;</div>
            <div class="profitmaplegend">Loss</div>
            <div class="profitmapview purple">&nbsp;</div>
            <div class="profitmaplegend">Needs Action</div>
        </div>
        <?=$headercomplet?>
        <?=$headerpricing?>
        <div class="btitemnewaddarea"></div>
        <div class="btitemnewsucategarea"></div>
        <div class="tabledataarea" id="btitemdata"></div>
    </div>
</div>