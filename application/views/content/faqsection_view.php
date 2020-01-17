<div class="faqsectiontitle">
    <div class="displayfaqsection show" data-faqsection="<?=$faq['faq_section']?>">
        <i class="fa fa-chevron-down" aria-hidden="true"></i>
    </div>
    <div class="title"><?=$faq['title']?></div>
</div>
<div class="faqsection_area" data-faqsection="<?=$faq['faq_section']?>">
    <?php $numr = 0;?>
    <?php $numpp = 1;?>
    <?php foreach ($faq['questions'] as $row) { ?>
        <?php if ($numr==0) { ?>
            <div class="faqsecion_row">
        <?php } ?>
        <div class="faq_data">
            <div class="faq_question">
                <div class="label">Question <?=$numpp?>:</div>
                <input class="faq_question" readonly="readonly" data-content="faq" data-faq="<?=$row['faq_id']?>" value="<?=$row['faq_quest']?>"/>
            </div>
            <div class="faq_answer">
                <div class="label">Answer <?=$numpp?>:</div>
                <textarea class="faq_answer" readonly="readonly" data-content="faq" data-faq="<?=$row['faq_id']?>"><?=$row['faq_answ']?></textarea>
            </div>
        </div>
        <?php $numr++?>
        <?php $numpp++;?>
        <?php if ($numr==2) { ?>
            </div>
            <?php $numr=0?>
        <?php } ?>
    <?php } ?>
    <?php if ($numr > 0) {?>
</div>
<?php } ?>
</div>
