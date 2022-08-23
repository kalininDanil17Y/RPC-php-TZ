<?php
    $url = sprintf("%s://%s/api/", Request::getHttpProtocol(), Request::getHost());
    //$url = getUrlByName('api');
?><!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8" />
    <title>Решение тестового задания</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/css/bootstrap.min.css" integrity="sha512-T584yQ/tdRR5QwOpfvDfVQUidzfgc2339Lc8uBDtcp/wYu80d7jwBgAxbyMh0a9YM9F8N3tdErpFI8iaGx6x5g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.min.js" integrity="sha512-UR25UO94eTnCVwjbXozyeVd6ZqpaAE9naiEUBK/A+QDbfSTQFhPGj5lOR6d8tsgbBk84Ggb5A3EkjsOgPRPcKA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        $(function(){
            function output(id, inp) {
                document.getElementById(id).appendChild(document.createElement('pre')).innerHTML = inp;
                $("i").on("click", function(){
                    var area = $(this).parent(".scob").children(".area");
                    var icon = $(this).parent(".scob").children("i");
                    area.toggle();
                    if (area.is(":hidden")) {
                        icon.removeClass("fa-angle-down");
                        icon.addClass("fa-angle-left");
                        //<i style="margin-left:4px;color:#000e59;" class="fa-solid fa-angle-left"></i>
                    } else {
                        icon.removeClass("fa-angle-left");
                        icon.addClass("fa-angle-down");
                        //<i style="margin-left:4px;color:#000e59;" class="fa-solid fa-angle-down"></i>
                    }
                });
            }
            function syntaxHighlight(json) {
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)|(\{)|(\})/g, function (match) {

                    var style = 'color: #000e59;';

                    if(match == "{"){
                        return '<span class="scob"><span style="color: #000;">{</span><i style="margin-left:3px; margin-right:3px; color:#025900; cursor: pointer;" class="fa-solid fa-angle-down"></i><span class="area" style="">';
                    }

                    if(match == "}"){
                        return '</span><span style="color: #000;">}</span></span>';
                    }

                    // if (/^"/.test(match)) {
                    //     if (/:$/.test(match)) {
                    //         style = 'color: #000;';
                    //     } else {
                    //         style = 'color: #025900; font-weight: 600;';
                    //     }
                    // } else if (/true|false/.test(match)) {
                    //     style = 'color: #600100; font-weight: 600;';
                    // } else if (/null/.test(match)) {
                    //     style = 'color: red; font-weight: 600;';
                    // }


                    style = 'color: #ff5370;';

                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            style = 'color: #c792ea;';
                        } else {
                            style = 'color: #c3e88d; font-weight: 600;';
                        }
                    } else if (/true|false/.test(match)) {
                        style = 'color: #f78c6c; font-weight: 600;';
                    } else if (/null/.test(match)) {
                        style = 'color: #f78c6c; font-weight: 600;';
                    }


                    return '<span style="' + style + '">' + match + '</span>';
                });
            }

            $.ajax({
                url: '<?= $url ?>',
                method: 'POST',
                data: {
                    message: '{"jsonrpc": "2.0", "method": "getEthereumDate", "id": 1}'
                },
                success: function (json){
                    let str = JSON.stringify(
                        JSON.parse(json),
                        null,
                        2
                    );
                    output('data', syntaxHighlight(str));
                }
            });

            $.ajax({
                url: '<?= $url ?>',
                method: 'POST',
                data: {
                    message: '[{"jsonrpc": "2.0", "method": "getEthereumDate", "id": 1},{"jsonrpc": "2.0", "method": "getParams", "params": {"Var1": 1000, "Var2": 2000}, "id": 2}]'
                },
                success: function (json){
                    let str = JSON.stringify(
                        JSON.parse(json),
                        null,
                        2
                    );
                    output('data2', syntaxHighlight(str));
                }
            });
        });
    </script>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
</head>
<body>
<nav class="navbar sticky-top navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand" href="/">Решение тестового задания</a>
</nav>
<div class="container" style="margin-top: 20px;">
    <div class="row">
        <h3>Запрос #1</h3><br>
        <div class="col-md-12">
            <h5>JSON строка</h5>
            <p>{"jsonrpc": "2.0", "method": "getEthereumDate", "id": 1}</p>
            <h5>CURL запрос</h5>
            <p>
                <span style="color:#30a;">curl</span>  <span style="color:#a11;">'<?=$url?>'</span> \<br>
                <span style="color:#30a; margin-left: 15px;">-H</span> <span style="color:#a11;">'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'</span> \<br>
                <span style="color:#30a; margin-left: 15px;">--data-raw</span> <span style="color:#a11;">'message=%7B%22jsonrpc%22%3A+%222.0%22%2C+%22method%22%3A+%22getEthereumDate%22%2C+%22id%22%3A+1%7D'</span> \<br>
                <span style="color:#30a; margin-left: 15px;">--compressed</span>
            </p>
        </div>
        <div class="col-md-12">
            <h5>JSON ответ</h5>
            <p id="data"></p>
        </div>

        <h3>Запрос #2</h3><br>
        <div class="col-md-12">
            <h5>JSON строка</h5>
            <p>[{"jsonrpc": "2.0", "method": "getEthereumDate", "id": 1},{"jsonrpc": "2.0", "method": "getParams", "params": {"Var1": 1000, "Var2": 2000}, "id": 2}]</p>
            <h5>CURL запрос</h5>
            <p style="overflow: auto;">
                <span style="color:#30a;">curl</span>  <span style="color:#a11;">'<?=$url?>'</span> \<br>
                <span style="color:#30a; margin-left: 15px;">-H</span> <span style="color:#a11;">'Content-Type: application/x-www-form-urlencoded; charset=UTF-8'</span> \<br>
                <span style="color:#30a; margin-left: 15px;">--data-raw</span> <span style="color:#a11;">'message=%5B%7B%22jsonrpc%22%3A+%222.0%22%2C+%22method%22%3A+%22getEthereumDate%22%2C+%22id%22%3A+1%7D%2C%7B%22jsonrpc%22%3A+%222.0%22%2C+%22method%22%3A+%22getParams%22%2C+%22params%22%3A+%7B%22Var1%22%3A+1000%2C+%22Var2%22%3A+2000%7D%2C+%22id%22%3A+2%7D%5D'</span> \<br>
                <span style="color:#30a; margin-left: 15px;">--compressed</span>
            </p>
        </div>
        <div class="col-md-12">
            <h5>JSON ответ</h5>
            <p id="data2"></p>
        </div>

        <div class="col-md-12">
            <a href="./images/s1.png">
                <img class="col-5" src="./images/s1.png" alt="screenshot 1">
            </a>
            <a href="./images/s2.png">
                <img class="col-5" src="./images/s2.png" alt="screenshot 2">
            </a>
        </div>
    </div>
</div>
</body>
</html>
