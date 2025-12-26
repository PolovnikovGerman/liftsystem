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
    <div class="ts-button lift">
        <a href="<?=$liftlink?>" target="_blank">
        <p>Test <span>Site</span></p>
        <div class="logo-company">
            <div class="logo-lift">LI<span>FT</span></div>
        </div>
        </a>
    </div>
    <?php endif; ?>
    <?php if ($this->config->item('test_server')==1) : ?>
    <div class="ts-button doupleorders">
        <p>Test Orders</p>
    </div>
    <div class="ts-button lockedorders">
        <p>Test Orders BLOCKED</p>
    </div>
    <?php else : ?>
        <div class="ts-button testorders">
            <a href="<?=$testorderlink?>" target="_blank">
            <p>Test Orders</p>
            <div class="logo-company-empty">&nbsp;</div>
            </a>
        </div>
    <?php endif; ?>
</div>
