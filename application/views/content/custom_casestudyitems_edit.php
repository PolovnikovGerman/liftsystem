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
                <div class="custom_casestudyimage" data-casestudy="<?=$item['custom_casestudy_id']?>">
                    <img src="<?=$item['casestudy_image']?>"/>
                </div>
                <div class="custom_casestudyimagedelete" data-casestudy="<?=$item['custom_casestudy_id']?>">
                    <i class="fa fa-trash" aria-hidden="true"></i>
                </div>
            <?php } else { ?>
                <div class="custom_casestudyimage_empty" data-casestudy="<?=$item['custom_casestudy_id']?>">
                    <div class="custom_casestudyitemupload" id="casestudy_<?=getuploadid()?>"></div>
                </div>
            <?php } ?>
        </div>
        <div class="content-row">
            <div class="label custom_casestudytitle">Title</div>
            <input class="custom_casestudytitle" data-content="casestudy" data-field="casestudy_title" data-casestudy="<?=$item['custom_casestudy_id']?>" value="<?=$item['casestudy_title']?>"/>
        </div>
        <div class="content-row">
            <div class="label custom_casestudytext">Text</div>
        </div>
        <div class="content-row">
            <textarea class="custom_casestudytext" data-content="casestudy" data-field="casestudy_text" data-casestudy="<?=$item['custom_casestudy_id']?>"><?=$item['casestudy_text']?></textarea>
            <textarea class="custom_casestudyexpand" data-content="casestudy" data-field="casestudy_expand" data-casestudy="<?=$item['custom_casestudy_id']?>"><?=$item['casestudy_expand']?></textarea>
        </div>
    </div>
    <?php $i++; $numpp++;?>
<?php } ?>
