<html>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Codeply simple HTML/CSS/JS preview</title>
<!-- <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"> -->
<!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> -->
<link rel="stylesheet" href="/css/page_view/bootstrap.min.css">
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.min.js"></script>
<script>
    var autocollapse = function() {

        var tabs = $('#tabs');
        var tabsHeight = tabs.innerHeight();
        console.log(tabsHeight);
        if (tabsHeight >= 50) {
            while (tabsHeight > 50) {
                //console.log("new"+tabsHeight);

                var children = tabs.children('li:not(:last-child)');
                var count = children.size();
                $(children[count - 1]).prependTo('#collapsed');

                tabsHeight = tabs.innerHeight();
            }
        }
    };

    $(document).ready(function() {

        autocollapse(); // when document first loads

        $(window).on('resize', autocollapse); // when window is resized

    });
</script>
<style></style>
</head>
<body>
<div class="container">
    <div class="row">

        <ul class="nav nav-tabs" id="tabs">
            <li><a href="#">Tab0</a></li>
            <li><a href="#">Tab1</a></li>
            <li><a href="#">Tab2</a></li>
            <li><a href="#">Tab3</a></li>
            <li><a href="#">Tab4</a></li>
            <li><a href="#">Tab5</a></li>
            <li><a href="#">Tab6</a></li>
            <li><a href="#">Tab7</a></li>
            <li><a href="#">Tab8</a></li>
            <li><a href="#">Tab9</a></li>
            <li><a href="#">Tab10</a></li>
            <li><a href="#">Tab11</a></li>
            <li><a href="#">Tab12</a></li>
            <li><a href="#">Tab13</a></li>
            <li><a href="#">Tab14</a></li>
            <li><a href="#">Tab15</a></li>
            <li><a href="#">Tab16</a></li>
            <li><a href="#">Tab17</a></li>
            <li><a href="#">Tab18</a></li>
            <li id="lastTab">
                <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                    More <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="collapsed">

                </ul>
            </li>
        </ul>
    </div>
</div>
</body>
</html>