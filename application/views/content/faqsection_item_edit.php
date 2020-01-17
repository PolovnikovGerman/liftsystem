<?php $numr = 0;?>
<?php $numpp = 1;?>
<?php foreach ($faq['questions'] as $row) { ?>
    <?php if ($numr==0) { ?>
        <div class="faqsecion_row">
    <?php } ?>
    <div class="faq_data">
        <div class="faq_question">
            <div class="label">Question <?=$numpp?>:</div>
            <input class="faq_question" data-content="faq" data-section="<?=$row['faq_section']?>" data-faq="<?=$row['faq_id']?>" data-field="faq_quest" value="<?=$row['faq_quest']?>"/>
            <div class="faq_questiondelete" data-faq="<?=$row['faq_id']?>" data-faqsection="<?=$row['faq_section']?>">
                <i class="fa fa-times-circle" aria-hidden="true" title="Remove Question"></i>
            </div>
        </div>
        <div class="faq_answer">
            <div class="label">Answer <?=$numpp?>:</div>
            <textarea class="faq_answer" data-content="faq" data-section="<?=$row['faq_section']?>" data-faq="<?=$row['faq_id']?>" data-field="faq_answ"><?=$row['faq_answ']?></textarea>
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
<div class="content-row">
    <div class="add_new_question" data-faqsection="<?=$faq_section?>">Add New Question</div>
</div>
