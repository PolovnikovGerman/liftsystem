<input type="hidden" id="category_session" value="<?=$session_id?>"/>
<input type="hidden" id="current_category" value="<?=$category_id?>"/>
<div class="content-row">
    <div class="labelinpt category_url">URL:</div>
    <input class="category_url" data-content="category" data-field="category_url" value="<?=$meta['category_url']?>"/>
</div>
<div class="content-row">
    <div class="labelinpt category_meta_title">Meta Title:</div>
    <input class="category_meta_title" data-content="category" data-field="category_meta_title" value="<?=$meta['category_meta_title']?>"/>
</div>
<div class="content-row">
    <div class="labelinpt category_meta_keywords">Meta Keywords:</div>
    <textarea class="category_meta_keywords" data-content="category" data-field="category_meta_keywords"><?=$meta['category_meta_keywords']?></textarea>
</div>
<div class="content-row">
    <div class="labelinpt category_meta_description">Meta Desc:</div>
    <textarea class="category_meta_description" data-content="category" data-field="category_meta_description"><?=$meta['category_meta_description']?></textarea>
</div>