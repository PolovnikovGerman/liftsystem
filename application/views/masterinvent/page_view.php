<input type="hidden" id="active_invtype" value="<?=$active_type?>"/>
<input type="hidden" id="invshowmax" value="0"/>
<div class="inventorydataview">
    <div class="datarow">
        <div class="pagetitle">
            <div class="datarow">
                <div class="pagetitlelabel">Master Inventory</div>
            </div>
            <div class="datarow">
                <div class="masterinventlegend">
                    <div class="legendmapicon severeval">
                        <i class="fa fa-square"></i>
                    </div>
                    <div class="legendmaplabel">Severe (25% & Under)</div>
                    <div class="legendmapicon lowval">
                        <i class="fa fa-square"></i>
                    </div>
                    <div class="legendmaplabel">Low (26% - 50%)</div>
                </div>
            </div>
        </div>
<!--        <div class="invtypebutton --><?php //=$eventtype=='purchasing' ? 'active' : ''?><!-- oldver" data-itemtype="purchasing">-->
<!--            <span>Purchasing</span>-->
<!--            <div class="oldvesionlabel">&nbsp;</div>-->
<!--        </div>-->
        <div class="invtypebutton <?=$eventtype=='manufacturing' ? 'active' : ''?> oldver" data-itemtype="manufacturing">
            <span>Manufacturing</span>
            <div class="oldvesionlabel">&nbsp;</div>
        </div>
        <div class="invtypebutton <?=$eventtype=='printing' ? 'active' : ''?> oldver" data-itemtype="printing">
            <span>Printing</span>
            <div class="oldvesionlabel">&nbsp;</div>
        </div>
        <div class="invtypebutton <?=$eventtype=='assembly' ? 'active' : ''?> oldver" data-itemtype="assembly">
            <span>Assembly</span>
            <div class="oldvesionlabel">&nbsp;</div>
        </div>
        <div class="inventfilterarea">
            <div class="datarow">
                <div class="inventfilterlabel">
                    Display:
                </div>
                <div class="inventfilterdata">
                    <select class="inventfilterselect" name="inventfilerdata">
                        <option value="0" selected="selected">Active & Inactive</option>
                        <option value="1">Active</option>
                        <option value="2">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="datarow">
                <div class="totalinvent">
                    <div class="totalinventlabel">Total Value:</div>
                    <div class="totalinventvalue"><?=!empty($total) ? MoneyOutput($total) : '&nbsp;'?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="mastinvent_left_menu">
            <?php foreach ($invtypes as $invtype) { ?>
                <div class="mastinvent_left_section <?=$invtype['inventory_type_id']==$active_type ? 'active' : ''?>" data-invrtype="<?=$invtype['inventory_type_id']?>" data-invlabel="<?=$invtype['type_short']?>">
                    <div class="mastinvent_left_sectiondata <?=$invtype['inventory_type_id']==$active_type ? 'active' : ''?> <?=$invtype['type_special']==1 ? 'rawsbtype' : ''?>">
                        <div class="inventsectionhead"><?=$invtype['type_short']?> - <?=$invtype['type_name']?></div>
                        <div class="inventsectionvalue">
                            <div class="inventsectionvalue_label">Value:</div>
                            <div class="inventsectionvalue_data"><?=empty($invtype['value']) ? '&nbsp;' : MoneyOutput($invtype['value'])?></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="datarow">
        <div class="mastinvent_databody">
            <!-- Table header -->
            <div class="datarow">
                <!-- Left menu -->
                <div class="mastinvent_header_left">
                    <div class="datarow">
                        <div class="labeltxt">Max Value:</div>
                        <div class="valuedata" id="maximuminvent">$346,004.50</div> <!-- <?// echo MoneyOutput($maxsum)?> -->
                    </div>
                    <div class="datarow">
                        <div class="addlabeltxt">Add’l Amnt:</div>
                        <div class="">
                            <input class="inventadd" value="">
                        </div>
                        <div class="addlabeltxt">ea  ($53,339)</div>
                    </div>
                    <div class="datarow">
                        <div class="masterinventtotals">
                            <div class="inventtotalmaxshow">[Show Max]</div>
                            <div class="masterinventpercent">%</div>
                            <div class="masterinventorymaximum">Maximum</div>
                            <div class="masterinventinstock">In Stock</div>
                            <div class="masterinventreserv">Reserved</div>
                            <div class="masterinventavailab">Available</div>
                        </div>
                    </div>
                    <div class="datarow">
                        <div class="masterinventtablehead">
                            <div class="addnewmasterinvent">
                                <img src="/img/masterinvent/addinvitem_bg.png" alt="Add"/>
                            </div>
                            <div class="masterinventdescrip">Item</div>
                            <div class="masterinventpercent"></div>
                            <div class="masterinventorymaximum">Maximum</div>
                            <div class="masterinventinstock"></div>
                            <div class="masterinventreserv"></div>
                            <div class="masterinventavailab"></div>
                            <div class="masterinventhistory">&nbsp;</div>
                            <div class="masterinventunit">Unit</div>
                        </div>
                    </div>
                </div>
                <!-- End Left menu -->
                <!-- Express -->
                <div class="mastinvent_header_express"></div>
                <!-- Express -->
                <!-- Container -->
                <div class="mastinvent_header_container"></div>
                <!-- Container -->
                <!-- Right part -->
                <div class="mastinvent_header_right">
                    <div class="masterinventexport">
                        <i class="fa fa-share-square-o" aria-hidden="true"></i>
                        Export <?=$export_type?> Inventory
                    </div>
                    <div class="datarow">
                        <div class="masterinventtablehead">
                            <div class="masterinventavgprice">Contract</div>
                            <div class="masterinventavgprice">Avg Price</div>
                            <div class="masterinventhistory">&nbsp;</div>
                            <div class="masterinventtotalval">Total Value</div>
                        </div>
                    </div>
                </div>
                <!-- Right part -->
            </div>
            <div class="datarow">
                <div id="masterinventtablebody"></div>
            </div>
        </div>
    </div>
</div>