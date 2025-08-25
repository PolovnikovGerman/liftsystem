<script>
    function dragstartHandler(ev) {
        ev.dataTransfer.setData("text", ev.target.id);
        console.log('Star ID '+ev.target.id);
    }

    function dragoverHandler(ev) {
        ev.preventDefault();
    }

    function dropHandler(ev) {
        ev.preventDefault();
        const data = ev.dataTransfer.getData("text");
        ev.target.appendChild(document.getElementById(data));
        console.log(ev.target.id);
    }
</script>
<div class="calendarcontent" style="width: 650; padding: 20px 15px">
    <div class="datarow">
        <div class="calendardataarea">
            <div class="titlelabel">08/15/2025</div>
            <div class="calendardata" data-day="<?=strtotime('2025-08-15')?>" id="printday_<?=strtotime('2025-08-15')?>" ondrop="dropHandler(event)"   ondragover="dragoverHandler(event)">
                <?php for($i=0; $i<10; $i++) : ?>
                    <div class="orderdata" data-order="<?=$i + 1?>" id="printord_<?=$i + 1?>" draggable="true" ondragstart="dragstartHandler(event)">
                        Order # <?=$i+1?> Date 08/15/2025
                    </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="calendardataarea">
            <div class="titlelabel">08/16/2025</div>
            <div class="calendardata" data-day="<?=strtotime('2025-08-16')?>" id="printday_<?=strtotime('2025-08-16')?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)">
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="calendardataarea">
            <div class="titlelabel">08/17/2025</div>
            <div class="calendardata" data-day="<?=strtotime('2025-08-17')?>" id="printday_<?=strtotime('2025-08-17')?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)">
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="calendardataarea">
            <div class="titlelabel">08/18/2025</div>
            <div class="calendardata" data-day="<?=strtotime('2025-08-18')?>" id="printday_<?=strtotime('2025-08-18')?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)">
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="calendardataarea">
            <div class="titlelabel">08/19/2025</div>
            <div class="calendardata" data-day="<?=strtotime('2025-08-19')?>" id="printday_<?=strtotime('2025-08-19')?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)">
            </div>
        </div>
    </div>
    <div class="datarow">
        <div class="calendardataarea">
            <div class="titlelabel">08/20/2025</div>
            <div class="calendardata" data-day="<?=strtotime('2025-08-20')?>" id="printday_<?=strtotime('2025-08-20')?>" ondrop="dropHandler(event)" ondragover="dragoverHandler(event)">
            </div>
        </div>
    </div>
</div>