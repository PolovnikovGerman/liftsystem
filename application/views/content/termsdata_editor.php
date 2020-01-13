<div class="content-row">
    <input class="term_header" data-content="terms" data-term="<?=$term_id?>" data-field="term_header" value="<?=$term_header?>"/>
    <div class="termcontenteditrow">
        <div class="termssave_params" data-term="<?=$term_id?>">
            Save <i class="fa fa-pencil" aria-hidden="true"></i>
        </div>
        <div class="termscancel_edit" data-term="<?=$term_id?>">
            Cancel <i class="fa fa-times" aria-hidden="true"></i>
        </div>
    </div>
</div>
<div class="content-row" data-term="<?=$term_id?>">
    <!-- Editor Area -->
    <textarea class="uEditorCustom termtext" name="textarea"><?=$term_text?></textarea>
</div>
