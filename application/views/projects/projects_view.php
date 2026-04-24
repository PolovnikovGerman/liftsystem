<div class="buttons-testsites">
    <?php if ($this->config->item('test_server')==0) : ?>
    <div class="ts-button stressballsbluetrack">
        <a href="<?=$bluelink?>" target="_blank">
            <p>Test Site</p>
            <div class="logo-company">
                <img src="/img/projects/logo-stressballs-white.svg">
                <img src="/img/projects/blue_logo.png" class="bluetrackcompany">
            </div>
        </a>
    </div>
    <div class="ts-button stressrelievers">
        <a href="<?=$relivlink?>" target="_blank">
        <p>Test Site</p>
        <div class="logo-company">
            <img src="/img/projects/sr-newlogo.svg">
        </div>
        </a>
    </div>
    <div class="ts-button stressballs">
        <a href="<?=$designlink?>" target="_blank">
        <p>Test Site</p>
        <div class="logo-company">
            <img src="/img/projects/logo-stressballs-white.svg">
        </div>
        </a>
    </div>
    <div class="ts-buttonaccess lift" data-url="<?=$liftlink?>">
<!--        <a href="--><?php //=$liftlink?><!--" target="_blank">-->
        <p>Test <span>Site</span></p>
        <div class="logo-company">
            <div class="logo-lift">LI<span>FT</span></div>
        </div>
<!--        </a>-->
    </div>
    <?php endif; ?>
    <?php if ($this->config->item('test_server')==1) : ?>
        <div class="ts-button_project dualorders">
            <p>Dual Orders Popup</p>
        </div>
        <div class="ts-button_project leadsview">
            <p>Lead Popup (Stock)</p>
        </div>
        <div class="ts-button_project leadcustomview">
            <p>Lead Popup (Custom) + dumbed LEFT Panel</p>
        </div>
        <div class="ts-button_project orderleadsview">
            <p>Orders & Leads Popup</p>
        </div>
        <div class="ts-button_project orderleaddataview">
            <p>Dual View (Order & Lead)</p>
        </div>
    <?php else : ?>
        <div class="ts-button_project testorders">
            <a href="<?=$testorderlink?>" target="_blank">
        <div class="ts-buttonaccess testorders" data-url="<?=$testorderlink?>">
<!--            <a href="--><?php //=$testorderlink?><!--" target="_blank">-->
            <p>Test Orders</p>
            <div class="logo-company-empty">&nbsp;</div>
<!--            </a>-->
        </div>
    <?php endif; ?>
</div>
