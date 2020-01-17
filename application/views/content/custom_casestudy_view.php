<div class="casestudyinfotitle">
    <div class="displaycasestudy show">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title">CASE STUDIES:</div>
</div>
<div class="custom_casestudies_area">
    <?php $i=0;$numpp=1;?>
    <?php foreach ($casestudy as $item) { ?>
        <div class="custom_casestudy_area">
            <div class="content-row">
                <div class="label custom_casestudyhead">Case Study <?=$numpp?>:</div>
                <div class="custom_imagesubtitle">click image to enlarge</div>
            </div>
            <div class="content-row">
                <div class="custom_imagesubtitlesize">(543px x 306px)</div>
            </div>
            <div class="content-row">
                <?php if (!empty($item['casestudy_image'])) { ?>
                    <div class="custom_casestudyimage">
                        <img src="<?=$item['casestudy_image']?>"/>
                    </div>
                <?php } else { ?>
                    <div class="custom_casestudyimage_empty">&nbsp;</div>
                <?php } ?>
            </div>
            <div class="content-row">
                <div class="label custom_casestudytitle">Title</div>
                <input class="custom_casestudytitle" readonly="readonly" name="custom_casestudytitle" value="<?=$item['casestudy_title']?>"/>
            </div>
            <div class="content-row">
                <div class="label custom_casestudytext">Text</div>
            </div>
            <div class="content-row">
                <textarea class="custom_casestudytext" readonly="readonly" name="custom_casestudytext"><?=$item['casestudy_text']?></textarea>
                <textarea class="custom_casestudyexpand" readonly="readonly" name="custom_casestudyexpand"><?=$item['casestudy_expand']?></textarea>
            </div>
        </div>
        <?php $i++; $numpp++;?>
    <?php } ?>
</div>