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
                    <div class="totalinventvalue"><?=$total?></div>
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
                            <div class="inventsectionvalue_data"><?=$invtype['value']?></div>
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
                        <div class="valuedata" id="maximuminvent"><?=$maxtotal?></div> <!-- <?// echo MoneyOutput($maxsum)?> -->
                    </div>
                    <div class="datarow">
                        <div class="addlabeltxt">Add’l Amnt:</div>
                        <div class="">
                            <input class="inventadd" value="<?=$addcost?>"/>
                        </div>
                        <div class="addlabeltotal">ea  (<?=$addval?>)</div>
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
                </div>
                <!-- End Left menu -->
                <!-- Express -->
                <div class="mastinvent_header_express">
                    <div class="datarow">
                        <div class="mastinvent_express_manage">Express <span>[+]</span></div>
                    </div>
                    <div class="datarow">
                        <div class="mastinvent_express_slide">
                            <div class="mastinvent_express_slideleft">
                                <img src="/img/masterinvent/container_nonactive_left.png"/>
                            </div>
                        </div>
                        <div class="mastinvent_express_contentarea">&nbsp;</div>
                        <div class="mastinvent_express_slide">
                            <div class="mastinvent_express_slideright">
                                <img src="/img/masterinvent/container_nonactive_right.png"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Express -->
                <!-- Container -->
                <div class="mastinvent_header_container">
                    <div class="datarow">
                        <div class="mastinvent_container_manage">Container <span>[+]</span></div>
                    </div>
                    <div class="datarow">
                        <div class="mastinvent_container_slide">
                            <div class="mastinvent_container_slideleft">
                                <img src="/img/masterinvent/container_nonactive_left.png"/>
                            </div>
                        </div>
                        <div class="mastinvent_container_contentarea">&nbsp;</div>
                        <div class="mastinvent_container_slide">
                            <div class="mastinvent_container_slideright">
                                <img src="/img/masterinvent/container_nonactive_right.png"/>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Container -->
                <!-- Right part -->
                <div class="mastinvent_header_right">
                    <div class="masterinventexport">
                        <i class="fa fa-share-square-o" aria-hidden="true"></i>
                        Export <?=$export_type?> Inventory
                    </div>
                </div>
                <!-- Right part -->
            </div>
            <div class="datarow masterinventtablehead">
                <div class="mastinvent_header_left">
                    <div class="addnewmasterinvent">
                        <img src="/img/masterinvent/addinvitem_bg.png" alt="Add"/>
                    </div>
                    <div class="masterinventdescrip">Item</div>
                    <div class="masterinventpercent"><?=$itempercent?></div>
                    <div class="masterinventorymaximum"><?=$maxval?></div>
                    <div class="masterinventinstock"><?=$instock?></div>
                    <div class="masterinventreserv"><?=$reserved?></div>
                    <div class="masterinventavailab"><?=$available?></div>
                    <div class="masterinventhistory">&nbsp;</div>
                    <div class="masterinventunit">Unit</div>
                </div>
                <div class="expresstotals">&nbsp;</div>
                <div class="containertotals">&nbsp;</div>
                <div class="mastinvent_header_right">
                    <div class="masterinventtablehead">
                        <div class="masterinvemptyspace">&nbsp;</div>
                        <div class="masterinventavgprice">Contract</div>
                        <div class="masterinventavgprice">Avg Price</div>
                        <div class="masterinventhistory">&nbsp;</div>
                        <div class="masterinventtotalval">Total Value</div>
                    </div>
                </div>
            </div>
            <div class="datarow">
                <div id="masterinventtablebody"></div>
            </div>
            <div class="datarow">
                <div class="mastinvent_footlink_left">&nbsp;</div>
                <div class="mastinvent_footlink_express">&nbsp;</div>
                <div class="mastinvent_footlink_container">&nbsp;</div>
            </div>
        </div>
    </div>
</div>
