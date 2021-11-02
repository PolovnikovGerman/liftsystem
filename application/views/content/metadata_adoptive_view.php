<div class="row">
    <div class="col-12">
        <div class="metainfotitle">
            <div class="displaymeta show">
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="title">Meta Info</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="metaleftpart">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-2">
                    <div class="label metatitle">Meta Title:</div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-10">
                    <input type="text" class="metatitle" readonly="readonly" value="<?=$meta_title?>"/>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-2">
                    <div class="label metakeywords">Meta Keywords:</div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-10">
                    <textarea class="metakeywords" readonly="readonly"><?=$meta_keywords?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 col-md-6 col-lg-6 col-xl-6">
        <div class="metarightpart">
            <div class="row">
                <div class="col-12 col-sm-12 col-md-12 col-lg-3 col-xl-2">
                    <div class="label metadescription">Meta Description:</div>
                </div>
                <div class="col-12 col-sm-12 col-md-12 col-lg-9 col-xl-10">
                    <textarea class="metadescription" readonly="readonly"><?=$meta_description?></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
