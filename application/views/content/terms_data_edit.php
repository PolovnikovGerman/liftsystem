<?php foreach ($terms as $row) { ?>
    <div class="termsdatarow" data-term="<?=$row['term_id']?>">
        <div class="content-row">
            <input class="term_header" data-content="terms" data-term="<?=$row['term_id']?>" data-field="term_header" value="<?=$row['term_header']?>"/>
            <div class="termcontenteditrow">
                <div class="termsedit_params" data-term="<?=$row['term_id']?>">
                    Edit <i class="fa fa-pencil" aria-hidden="true"></i>
                </div>
                <div class="termsremove" data-term="<?=$row['term_id']?>">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                </div>
            </div>
        </div>
        <div class="content-row">
            <!-- Editor Area -->
            <p><?= $row['term_text'] ?></p>
        </div>
    </div>
<?php } ?>
