<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Lift System</title>
    <link rel="stylesheet" href="/css/page_view/main.css">
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
</head>
<body>
<header>
    <div class="mainheader">
        <div class="row">
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
            <div class="period_analitic_info">
                <div class="period_name">This Week</div>
                <div class="param_value_label">ord</div>
                <div class="param_value">256</div>
                <div class="param_value_label">ord</div>
                <div class="param_value money">$256,614</div>
            </div>
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
                <div class="row">
                    <div class="signout" id="signout">[sign out]</div>
                    <div class="usersigninfo">
                        Sean Glasser
                    </div>
                </div>
                <div class="row">
                    <div class="dateinfo">
                        <?=date('D, F j, Y')?>
                    </div>
                </div>
            </div>
            <div class=""
        </div>
    </div>
</header>
<container></container>
<footer></footer>
</body>
</html>