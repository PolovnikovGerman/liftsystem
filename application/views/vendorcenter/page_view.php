<input type="hidden" id="totalvend" value="<?= $total ?>"/>
<input type="hidden" id="perpagevend" value="<?= $perpage ?>"/>
<input type="hidden" id="orderbyvend" value="<?= $order ?>"/>
<input type="hidden" id="directionvend" value="<?= $direc ?>"/>
<input type="hidden" id="curpagevend" value="<?= $curpage ?>"/>
<div class="vendordataview">
    <div class="pageheader">
        <div class="pagetitle">Vendor Database</div>
        <div class="pageheader-righ">
            <div class="filterdata">
                <select class="filterdata" name="filerdata" id="filterdata">
                    <option value="0">All Vendors Status</option>
                    <option value="1" selected="selected">Active Vendors</option>
                    <option value="2">Non-Active Vendors</option>
                </select>
                <select class="filterdata" id="filtertype">
                    <option value="">All Vendors Types</option>
                    <option value="Supplier">Supplier</option>
                    <option value="Artwork">Artwork</option>
                    <option value="Shipping">Shipping</option>
                    <option value="Other">Other</option>
                </select>
            </div>
            <div class="datasearch">
                <div class="datatemplate">
                    <input type="text" id="vedorsearch" class="searchinpt" placeholder="Enter keyword"/>
                </div>
                <div class="datasearchbtn">Search</div>
                <div class="datacleanbtn">Clear</div>
            </div>
            <div class="datafilternavig">
                <div class="datanavigation">
                    <div class="paginator" id="vendorPagination"></div>
                    <div class="totaldata"><?=QTYOutput($total)?> Records</div>
                </div>
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
    <div id="vendorinfo"></div>
</div>