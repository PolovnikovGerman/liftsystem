<input type="hidden" id="totalvend" value="<?= $total ?>"/>
<input type="hidden" id="perpagevend" value="<?= $perpage ?>"/>
<input type="hidden" id="orderbyvend" value="<?= $order ?>"/>
<input type="hidden" id="directionvend" value="<?= $direc ?>"/>
<input type="hidden" id="curpagevend" value="<?= $curpage ?>"/>
<main class="container-fluid">
    <div class="vendordataview">
        <div class="pageheader">
            <div class="row pt-2">
                <div class="col-12 col-sm-3 col-md-3 col-lg-3 col-xl-2">
                    <div class="pagetitle">Vendor Database</div>
                </div>
                <div class="col-12 col-sm-9 col-md-9 col-lg-9 col-xl-6">
                    <div class="row mb-3">
                        <div class="col-6">
                            <input type="text" id="vedorsearch" class="searchinpt " placeholder="Enter keyword"/>
                        </div>
                        <a class="btn btn-default datasearchbtn">Search</a>
                        <a class="btn btn-default datacleanbtn">Clear</a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-7 col-xl-6">
<!--                    <span class="filterlabel" for="filterdata">Display:</span>-->
                    <div class="row">
                        <div class="col-6">
                            <select class="filterdata" id="filtertype">
                                <option value="">All Vendors Types</option>
                                <option value="Supplier">Supplier</option>
                                <option value="Artwork">Artwork</option>
                                <option value="Shipping">Shipping</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <!--                    <span class="filterlabel" for="filterdata form-control">Display:</span>-->
                        <div class="col-6">
                            <select class="filterdata" name="filerdata" id="filterdata">
                                <option value="0">All Vendors Status</option>
                                <option value="1" selected="selected">Active Vendors</option>
                                <option value="2">Non-Active Vendors</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-5 col-xl-2 mt-2">
                    <div class="paginator" id="vendorPagination"></div>
                    <div class="totaldata"><?=QTYOutput($total)?> Records</div>
                </div>
            </div>
        </div>

        <div class="datatitle">
            <div class="status">
                <div class="addnewvendor">+ add</div>
            </div>
            <div class="type">Type</div>
            <div class="slug sortable" data-sortcell="vendor_slug">Vend #</div>
            <div class="name sortable active" data-sortcell="vendor_name">Vendor Name  <div class="ascsort">&nbsp;</div></div>
            <div class="altname sortable" data-sortcell="alt_name">Alternate Name</div>
            <div class="asinumber sortable" data-sortcell="vendor_asinumber">ASI #</div>
            <div class="website">Website</div>
            <div class="phone">Phone</div>
            <div class="itemqty">Our Items</div>
        </div>

        <div class="dataarea" id="vendorinfo"></div>
    </div>
</main>