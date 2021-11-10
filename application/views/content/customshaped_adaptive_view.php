<div class="row">
    <div class="col-12">
        <div class="contentinfotitle">
            <div class="displaycontent show">
                <i class="fa fa-chevron-down" aria-hidden="true"></i>
            </div>
            <div class="title">Content</div>
        </div>
    </div>
</div>
<div class="customcontent-area">
    <div class="row">
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <div class="label custom_maintitle">Main Title:</div>
            <input class="custom_maintitle" name="custom_maintitle" readonly="readonly" value="<?=ifset($data,'custom_maintitle','')?>"/>
        </div>
        <div class="col-12 col-sm-12 col-md-12 col-lg-6 col-xl-6">
            <div class="label custom_secondarytext">Sub Title:</div>
            <input class="custom_secondarytext" name="custom_secondarytext" readonly="readonly" value="<?=ifset($data,'custom_secondarytext','')?>"/>
        </div>
    </div>
</div>