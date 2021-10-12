<input type="hidden" id="totalvend" value="<?= $total ?>"/>
<input type="hidden" id="perpagevend" value="<?= $perpage ?>"/>
<input type="hidden" id="orderbyvend" value="<?= $order ?>"/>
<input type="hidden" id="directionvend" value="<?= $direc ?>"/>
<input type="hidden" id="curpagevend" value="<?= $curpage ?>"/>
<main class="container-fluid">
    <div class="vendordataview">
        <div class="pageheader">
            <div class="row">
                <div class="col-12 col-sm-3 col-md-3 col-lg-2 col-xl-2">
                    <div class="pagetitle">Vendor Database</div>
                </div>
                <div class="col-12 col-sm-9 col-md-9 col-lg-4 col-xl-4">
                    <div class="row mb-3">
                        <div class="col-6">
                            <input type="text" id="vedorsearch" class="searchinpt form-control " placeholder="Enter keyword"/>
                        </div>
                        <a class="btn btn-default datasearchbtn">Search</a>
                        <a class="btn btn-default datacleanbtn">Clear</a>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-6 col-xl-6">
                    <span class="filterlabel" for="filterdata">Display:</span>
                    <select class="filterdata form-control form-group" id="filtertype">
                        <option value="">All Vendors Types</option>
                        <option value="Supplier">Supplier</option>
                        <option value="Artwork">Artwork</option>
                        <option value="Shipping">Shipping</option>
                        <option value="Other">Other</option>
                    </select>
                    <span class="filterlabel" for="filterdata form-control">Display:</span>
                    <select class="filterdata form-control form-group" name="filerdata" id="filterdata">
                        <option value="0">All Vendors Status</option>
                        <option value="1" selected="selected">Active Vendors</option>
                        <option value="2">Non-Active Vendors</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-6 col-lg-3 col-xl-3">
                    <div class="paginator" id="vendorPagination"></div>
                    <div class="totaldata"><?=QTYOutput($total)?> Records</div>
                </div>
            </div>

        </div>
    </div>
</main>