<?php foreach ($terms as $row) { ?>
    <div class="termsdatarow">
        <div class="content-row">
            <input class="term_header" readonly="readonly" value="<?=$row['term_header']?>"/>
        </div>
        <div class="content-row">
            <!-- Editor Area -->
            <p><?= $row['term_text'] ?></p>
        </div>
    </div>
<?php } ?>