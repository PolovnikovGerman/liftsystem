<div class="mainheader">
    <div class="datarow">
        <div class="brands-logos">
            <div class="bluetrack_logo">
                <img src="/img/page_view/bluetrack_logo.png"/>
            </div>
            <div class="stressballs_logo">
                <img src="/img/page_view/stressballs_logo.png"/>
            </div>
            <div class="lift_logo">
                <img src="/img/page_view/lift_logo.png"/>
            </div>
        </div>
        <div class="period_analitic_info"><?=$total_view?></div>
        <div class="publicsearch">
            <select class="publicsearch_type">
                <option value="Orders">Orders</option>
                <option value="Customers">Customers</option>
                <option value="Items">Items</option>
            </select>
            <input type="text" class="publicsearch_template" id="publicsearch_template" placeholder="Find Orders"/>
            <div class="publicsearch_btn">
                <img src="/img/page_view/search_icon_blue.png"/>
            </div>
        </div>
        <div class="userinfo">
            <div class="datarow">
                <div class="signout" id="signout">[sign out]</div>
                <div class="usersigninfo"><?=$user_name?></div>
            </div>
            <div class="datarow">
                <div class="dateinfo">
                    <?=date('D, F j, Y')?>
                </div>
            </div>
        </div>
        <div class="infoalerts" id="infoalerts">
            <div class="alerticon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M224 512c35.32 0 63.97-28.65 63.97-64H160.03c0 35.35 28.65 64 63.97 64zm215.39-149.71c-19.32-20.76-55.47-51.99-55.47-154.29 0-77.7-54.48-139.9-127.94-155.16V32c0-17.67-14.32-32-31.98-32s-31.98 14.33-31.98 32v20.84C118.56 68.1 64.08 130.3 64.08 208c0 102.3-36.15 133.53-55.47 154.29-6 6.45-8.66 14.16-8.61 21.71.11 16.4 12.98 32 32.1 32h383.8c19.12 0 32-15.6 32.1-32 .05-7.55-2.61-15.27-8.61-21.71z"/></svg>
            </div>
            <div class="alerttext">Alerts</div>
        </div>
        <div class="infoalerts" id="admin">
            <div class="alerticon admin">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M487.4 315.7l-42.6-24.6c4.3-23.2 4.3-47 0-70.2l42.6-24.6c4.9-2.8 7.1-8.6 5.5-14-11.1-35.6-30-67.8-54.7-94.6-3.8-4.1-10-5.1-14.8-2.3L380.8 110c-17.9-15.4-38.5-27.3-60.8-35.1V25.8c0-5.6-3.9-10.5-9.4-11.7-36.7-8.2-74.3-7.8-109.2 0-5.5 1.2-9.4 6.1-9.4 11.7V75c-22.2 7.9-42.8 19.8-60.8 35.1L88.7 85.5c-4.9-2.8-11-1.9-14.8 2.3-24.7 26.7-43.6 58.9-54.7 94.6-1.7 5.4.6 11.2 5.5 14L67.3 221c-4.3 23.2-4.3 47 0 70.2l-42.6 24.6c-4.9 2.8-7.1 8.6-5.5 14 11.1 35.6 30 67.8 54.7 94.6 3.8 4.1 10 5.1 14.8 2.3l42.6-24.6c17.9 15.4 38.5 27.3 60.8 35.1v49.2c0 5.6 3.9 10.5 9.4 11.7 36.7 8.2 74.3 7.8 109.2 0 5.5-1.2 9.4-6.1 9.4-11.7v-49.2c22.2-7.9 42.8-19.8 60.8-35.1l42.6 24.6c4.9 2.8 11 1.9 14.8-2.3 24.7-26.7 43.6-58.9 54.7-94.6 1.5-5.5-.7-11.3-5.6-14.1zM256 336c-44.1 0-80-35.9-80-80s35.9-80 80-80 80 35.9 80 80-35.9 80-80 80z"/></svg>
            </div>
            <div class="alerttext">Admin</div>
        </div>
    </div>
    <div class="row menurow"><?=$menu_view?></div>
</div>
