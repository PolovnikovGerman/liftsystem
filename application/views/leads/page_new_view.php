<?=$menu_view?>
<div class="contentdata_view">
    <?php if (isset($leadsview)) { ?>
        <div class="leadscontentarea" id="leadsview" style="display: none;"><?=$leadsview?></div>
    <?php } ?>
    <?php if (isset($itemslistview)) { ?>
        <div class="leadscontentarea" id="itemslistview" style="display: none;"><?=$itemslistview?></div>
    <?php } ?>
    <?php if (isset($onlinequotesview)) { ?>
        <div class="leadscontentarea" id="onlinequotesview" style="display: none;"><?=$onlinequotesview?></div>
    <?php } ?>
    <?php if (isset($proofrequestsview)) { ?>
        <div class="leadscontentarea" id="proofrequestsview" style="display: none;"><?=$proofrequestsview?></div>
    <?php } ?>
    <?php if (isset($questionsview)) { ?>
        <div class="leadscontentarea" id="questionsview" style="display: none;"><?=$questionsview?></div>
    <?php } ?>
    <?php if (isset($customsbformview)) { ?>
        <div class="leadscontentarea" id="customsbformview" style="display: none;"><?=$customsbformview?></div>
    <?php } ?>
    <?php if (isset($checkoutattemptsview)) { ?>
        <div class="leadscontentarea" id="checkoutattemptsview" style="display: none;"><?=$checkoutattemptsview?></div>
    <?php } ?>
    <?php if (isset($leadquotesview)) { ?>
        <div class="leadscontentarea" id="leadquotesview" style="display: none;"><?=$leadquotesview?></div>
    <?php } ?>
    <?php if (isset($leadordersview)) { ?>
        <div class="leadscontentarea" id="customorders" style="display: none;"><?=$leadordersview?></div>
    <?php } ?>
</div>
