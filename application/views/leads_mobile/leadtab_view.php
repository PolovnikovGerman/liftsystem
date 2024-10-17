<input type="hidden" id="totallead" value="<?=$totalrec?>"/>
<input type="hidden" id="perpagelead" value="<?=$perpage?>"/>
<input type="hidden" id="curpagelead" value="<?=$curpage?>"/>
<input type="hidden" id="showfuturereport" value="0"/>
<input type="hidden" id="totalcuryearorders" value="<?=$totalorders?>"/>
<input type="hidden" id="leadsveiwbrand" value="<?=$brand?>"/>

<div class="row">
    <div class="col-12 col-sm-7 pr-2">
        <div class="btn-newlead">New Lead</div>
        <select class="leads_replica" id="leads_replica">
            <?php if ($user_role=='masteradmin') : ?>
                <option value="">All Sales Reps</option>
            <?php endif; ?>
            <?php foreach ($replicas as $replica) :?>
                <option value="<?=$replica['user_id']?>" <?=($replica['user_id']==$user_id ? 'selected="selected"' : '')?> ><?=$replica['user_name']?></option>
            <?php endforeach; ?>
        </select>
        <div class="noteslist">
            <ul>
                <li><div class="nlbox white">&nbsp;</div> Open</li>
                <li><div class="nlbox blue">&nbsp;</div> Closed</li>
                <li><div class="nlbox pink">&nbsp;</div> Dead</li>
            </ul>
        </div>
    </div>
    <div class="col-12 col-sm-5 pl-2">
        <select class="sel-sorttime" id="sorttime">
            <option value="1">Last Updated</option>
            <option value="2">When Created</option>
        </select>
        <select class="sel-sortprior" id="sortprior">
            <option value="1" selected="selected">Open, Priority & Soon</option>
            <option value="6">Ordering Soon Only</option>
            <option value="2">Priority Only</option>
            <option value="3">Closed Only</option>
            <option value="4">Dead Only</option>
            <option value="">View All Leads</option>
        </select>
    </div>
    <div class="col-12 col-sm-8 leadord-search-block">
        <div class="row">
            <div class="col-12 col-sm-7 pr-1 leadord_search">
                <div class="row">
                    <div class="col-2 pr-0">
                        <div class="ic-magnifier">
                            <img src="/img/page_mobile/magnifier.png" alt="Search">
                        </div>
                    </div>
                    <div class="col-9 px-1">
                        <input type="text" class="lead_searchinput" placeholder="Enter order #, customer, email"/>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-5 btns-leadord-search">
                <?php if ($user_role=='masteradmin') : ?>
                    <div class="btn-searchall leadsearchall">Search It</div>
                <?php endif; ?>
                <div class="btn-searchusr leadsearchusr"><?=$user_name?>&apos;s</div>
                <div class="btn-clear leadsearchclear">Clear</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4">
        <nav aria-label="" class="list-pages leadlist_pagination"></nav>
    </div>
    <div class="col-12 col-sm-12">
        <div class="table-responsive">
            <table class="table tbl-leads table-borderless">
                <thead>
                <tr class="tblleads-title">
                    <th scope="col" colspan="2"><div class="tblleads-td tblleads-lead">Lead #</div></th>
                    <th scope="col"><div class="tblleads-td tblleads-date">Date</div></th>
                    <th scope="col"><div class="tblleads-td tblleads-value">Value</div></th>
                    <th scope="col"><div class="tblleads-td tblleads-customer">Customer</div></th>
                    <th scope="col"><div class="tblleads-td tblleads-qty">QTY</div></th>
                    <th scope="col"><div class="tblleads-td tblleads-item">Item</div></th>
                    <th scope="col"><div class="tblleads-td tblleads-rep">Rep</div></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
    <?=$right_content?>
    <div id="leadcloseddataarea">&nbsp;</div>
</div>