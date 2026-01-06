<?php
require_once '../php/config.php';
require_once '../php/timezone.php';
$user_id = $_SESSION['user_id'];
if ($_SESSION['user_id'] == 0) {
    header("location: /index.php");
}
$pos = mysqli_query($conn, "SELECT * FROM `users` WHERE `id`='$user_id'");
$pos = mysqli_fetch_all($pos);
foreach ($pos as $poss)
?>
<? $position = $poss[5];
$user_fio = $poss[3];
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/home.css">
    <link rel="stylesheet" href="/css/payment.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Anesi Kassa</title>
</head>
<style>
    @media (max-width: 1440px) {
        div#exchange-uzs-usd {
            position: absolute;
            background-color: white;
            width: 40%;
            top: 10%;
            left: 30%;
            border-radius: 8px;
        }
        div#exchange-usd-uzs {
            position: absolute;
            background-color: white;
            width: 40%;
            top: 10%;
            left: 30%;
            border-radius: 8px;
        }
    }
</style>

<body>
    <?php $header_title = 'Обмен валют'; ?>
    <div class="navbar" id="navbar">
        <?php require_once 'navbar.php'; ?>
    </div>
    <hr>
    <div class="menu-left" id="menu-left">
        <?php require_once 'menu-left.php'; ?>
    </div>
    <div class="menu-right" id="menu-right">
        <?php require_once 'menu-right.php'; ?>
    </div>
    <div class="window" id="window">
        <div class="exchange" id="exchange">
            <div class="exchange" id="exchange-usd-uzs">
                <h4 id="exchange-header">Обмен валют</h4>
                <hr><br>
                <h5 style="text-align: center; font-size:16px;">Продажа</h5>
                <div class="info-error-exchange" id="error_exchange_sell"></div>
                <div class="info-success-exchange" id="success_exchange_sell" style="display: none;">Успешно</div><br>
                <div class="exchange-table" id="exchange-table">
                    <input class="date" name="date" type="hidden" value="<?php echo date(" Y-m-d "); ?>">
                    <input class="time" name="time" type="hidden" value="<?php echo date(" H:i:s "); ?>">
                    <input type="hidden" name="user_fio" id="user_fio" value="<?= $user_fio ?>">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
                    <form action="" id="exchange-usd-form">
                        <img src="\images\flag.png" alt="" width="42" height="42" style="border-radius: 5px;">
                        <h5 id="exchange-usd-xr">10101000840123456001</h5><br>
                        <p id="usd_table_sell"></p>
                    </form><br>
                    <p id="svg-exchange-usd-uzs" style="cursor: pointer; float:right; margin-top:-5%; margin-left:-5%;"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z" />
                        </svg></p>

                    <form action="" id="exchange-uzs-form">
                        <img src="\images\flag-uzb.png" alt="" width="42" height="42" style="border-radius: 5px;">
                        <h5 id="exchange-uzs-xr">10101000110254638001</h5><br>
                        <p id="summa_table_sell"></p>
                    </form><br>
                    <input type="text" id="exchange_input_one" placeholder="Введите сумму"><input type="text" id="label" placeholder="USD" readonly><br><br>
                    <input type="text" id="exchange_input_two" placeholder="0.00" readonly><input type="text" id="label" placeholder="UZS" readonly>
                    <input type="hidden" name="type_exchange" id="type_exchange_sell" value="Продажа">
                    <input type="hidden" name="usd_sell" id="usd_sell_summa">
                    <input type="hidden" name="uzs_sell" id="uzs_sell_summa">

                    <input type="hidden" id="usd_sell_cours">
                    <br>
                </div><br>
                <button class="btn btn-success" id="exchange_btn_sell" style="cursor: pointer;">Обменять</button><br>
            </div>
            <div class="exchange" id="exchange-uzs-usd">
                <h4 id="exchange-header">Обмен валют</h4>
                <hr><br>
                <h5 style="text-align: center; font-size:16px;">Покупка</h5>
                <div class="info-error-exchange" id="error_exchange_buy"></div>
                <div class="info-success-exchange" id="success_exchange_buy" style="display: none;">Успешно</div><br>
                <div class="exchange-table" id="exchange-table">
                    <input class="date" name="date" type="hidden" value="<?php echo date(" Y-m-d "); ?>">
                    <input class="time" name="time" type="hidden" value="<?php echo date(" H:i:s "); ?>">
                    <input type="hidden" name="user_fio" id="user_fio" value="<?= $user_fio ?>">
                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
                    <form action="" id="exchange-uzs-form">
                        <img src="\images\flag-uzb.png" alt="" width="42" height="42" style="border-radius: 5px;">
                        <h5 id="exchange-uzs-xr">10101000110254638001</h5><br>
                        <p id="summa_table"></p>
                    </form><br>
                    <p id="svg-exchange-uzs-usd" style="cursor: pointer; float:right; margin-top:-5%; margin-left:-5%;"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-arrow-down-up" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M11.5 15a.5.5 0 0 0 .5-.5V2.707l3.146 3.147a.5.5 0 0 0 .708-.708l-4-4a.5.5 0 0 0-.708 0l-4 4a.5.5 0 1 0 .708.708L11 2.707V14.5a.5.5 0 0 0 .5.5zm-7-14a.5.5 0 0 1 .5.5v11.793l3.146-3.147a.5.5 0 0 1 .708.708l-4 4a.5.5 0 0 1-.708 0l-4-4a.5.5 0 0 1 .708-.708L4 13.293V1.5a.5.5 0 0 1 .5-.5z" />
                        </svg></p>

                    <form action="" id="exchange-usd-form">
                        <img src="\images\flag.png" alt="" width="42" height="42" style="border-radius: 5px;">
                        <h5 id="exchange-usd-xr">10101000840123456001</h5><br>
                        <p id="usd_table"></p>
                    </form><br>
                    <input type="text" id="exchange_input_one_two" placeholder="Введите сумму"><input type="text" id="label" placeholder="USD" readonly><br><br>
                    <input type="text" id="exchange_input_two_two" placeholder="0.00" readonly><input type="text" id="label" placeholder="UZS" readonly>
                    <input type="hidden" id="usd_buy_cours">
                    <input type="hidden" name="type_exchange" id="type_exchange" value="Покупка">
                    <input type="hidden" name="usd_buy" id="usd_buy_summa">
                    <input type="hidden" name="uzs_buy" id="uzs_buy_summa">
                    <br>
                </div><br>
                <button class="btn btn-success" id="exchange_btn_buy" style="cursor: pointer;">Обменять</button><br>
            </div>
        </div>

    </div>
    <script src="\js\index.js"></script>
    <script>
        $('#svg-exchange-uzs-usd').click(function() {
            $('#exchange-uzs-usd').hide();
            $('#exchange-usd-uzs').show();
            var pust = '';
            document.getElementById("exchange_input_one_two").value = (pust);
            document.getElementById("exchange_input_two_two").value = (pust);
            document.getElementById("usd_buy_summa").value = (pust);
            document.getElementById("uzs_buy_summa").value = (pust);
            $('#success_exchange_buy').hide();
            $('#error_exchange_buy').hide();
        });
        $('#svg-exchange-usd-uzs').click(function() {
            $('#exchange-usd-uzs').hide();
            $('#exchange-uzs-usd').show();
            var pust = '';
            document.getElementById("exchange_input_one").value = (pust);
            document.getElementById("exchange_input_two").value = (pust);
            document.getElementById("usd_sell_summa").value = (pust);
            document.getElementById("uzs_sell_summa").value = (pust);
            $('#success_exchange_sell').hide();
            $('#error_exchange_sell').hide();
        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $.ajax({
                url: '/php/cours.php',
                type: 'POST',
                dataType: "html",
                beforeSend: funcBefore,
                success: function(data) {
                    var cours = $.parseJSON(data);
                    if (data) {
                        document.getElementById("usd_sell_cours").value = (cours[0][2]);
                        document.getElementById("usd_buy_cours").value = (cours[0][1]);
                    }
                    if (!data) {
                        $('#error').show();
                        $('#error').text(data);
                    }
                }
            });
        });
    </script>
    <script>
        const Summa_one = document.getElementById('exchange_input_one'),
            cours_sell = document.getElementById('usd_sell_sell');
        document.getElementById("exchange_input_one").addEventListener("change", displayDateOne);

        function displayDateOne() {
            var Summa_one = exchange_input_one.value;
            let Summa_num = Number(Summa_one);
            var cours_sell = usd_sell_cours.value;
            let cours_sell_num = Number(cours_sell);
            var exchange = cours_sell_num * Summa_num;
            document.getElementById("usd_sell_summa").value = (Summa_num);
            document.getElementById("uzs_sell_summa").value = (exchange);
            var Summa_format = Summa_num.toLocaleString('ru-RU', {
                style: "decimal",
                minimumFractionDigits: 2
            });
            var total = exchange.toLocaleString('ru-RU', {
                style: "decimal",
                minimumFractionDigits: 2
            });
            document.getElementById("exchange_input_one").value = (Summa_format);
            document.getElementById("exchange_input_two").value = (total);
        }
    </script>
    <script>
        const Summa_one_sell = document.getElementById('exchange_input_one_two'),
            cours_buy_sell = document.getElementById('usd_buy_cours');
        document.getElementById("exchange_input_one_two").addEventListener("change", displayDateTwo);

        function displayDateTwo() {
            var Summa_one_sell = exchange_input_one_two.value;
            let Summa_num_sell = Number(Summa_one_sell);
            var cours_buy_sell = usd_buy_cours.value;
            let cours_buy_num_sell = Number(cours_buy_sell);
            var exchange_sell = cours_buy_num_sell * Summa_num_sell;
            document.getElementById("uzs_buy_summa").value = (exchange_sell);
            document.getElementById("usd_buy_summa").value = (Summa_num_sell);
            var Summa_format_sell = Summa_num_sell.toLocaleString('ru-RU', {
                style: "decimal",
                minimumFractionDigits: 2
            });
            var total_sell = exchange_sell.toLocaleString('ru-RU', {
                style: "decimal",
                minimumFractionDigits: 2
            });
            document.getElementById("exchange_input_one_two").value = (Summa_format_sell);
            document.getElementById("exchange_input_two_two").value = (total_sell);
        }
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $("#exchange_btn_buy").on('click', function() {
                var type = $("#type_exchange").val();
                var usd_buy = $("#usd_buy_summa").val();
                var uzs_buy = $("#uzs_buy_summa").val();
                var rate_buy = $("#usd_buy_cours").val();
                /* Act on the event */
                $.ajax({
                    url: '\\php\\add_exchange.php',
                    type: 'POST',
                    data: {
                        type_exchange: type,
                        exchange_input_one_two: usd_buy,
                        exchange_input_two_two: uzs_buy,
                        usd_buy_cours: rate_buy,
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        if (data == 1) {
                            $('#success_exchange_buy').show();
                            $('#error_exchange_buy').hide();
                            $(document).ready(function() {
                                $.ajax({
                                    url: '/php/select_in_mysql_sum.php',
                                    type: 'POST',
                                    dataType: "html",
                                    beforeSend: funcBefore,
                                    success: function(data) {
                                        var sum = data;
                                        if (data >= 0) {
                                            let Summa_num = Number(sum);
                                            var Summa = Summa_num.toLocaleString('ru-RU', {
                                                style: "decimal",
                                                minimumFractionDigits: 2
                                            });
                                            document.getElementById("summa_table").innerHTML = (Summa + " <span>UZS</span>").replace(/,/, '.');
                                            document.getElementById("summa_table_sell").innerHTML = (Summa + " <span>UZS</span>").replace(/,/, '.');
                                        }
                                        if (!data) {
                                            $('#error').show();
                                            $('#error').text(data);
                                        }
                                    }
                                });
                            });
                            $(document).ready(function() {
                                $.ajax({
                                    url: '/php/select_in_mysql_usd.php',
                                    type: 'POST',
                                    dataType: "html",
                                    beforeSend: funcBefore,
                                    success: function(data) {
                                        var usd = data;
                                        if (data >= 0) {
                                            let usd_num = Number(usd);
                                            var usd = usd_num.toLocaleString('ru-RU', {
                                                style: "decimal",
                                                minimumFractionDigits: 2
                                            });
                                            document.getElementById("usd_table").innerHTML = (usd + " <span>USD</span>").replace(/,/, '.');
                                            document.getElementById("usd_table_sell").innerHTML = (usd + " <span>USD</span>").replace(/,/, '.');
                                        }
                                        if (!data) {
                                            $('#error').show();
                                            $('#error').text(data);
                                        }
                                    }
                                });
                            });
                        } else {
                            $('#error_exchange_buy').show();
                            $('#success_exchange_buy').hide();
                            document.getElementById("error_exchange_buy").innerHTML += (data);
                        }
                        if (data == 0) {
                            document.getElementById("error_exchange_buy").innerHTML += ('Ошибка сети подключитесь к интернету');
                        }

                    }
                });
            });
        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $("#exchange_btn_sell").on('click', function() {
                var type = $("#type_exchange_sell").val();
                var usd_sell = $("#usd_sell_summa").val();
                var uzs_sell = $("#uzs_sell_summa").val();
                var rate_sell = $("#usd_sell_cours").val();
                /* Act on the event */
                $.ajax({
                    url: '\\php\\add_exchange_sell.php',
                    type: 'POST',
                    data: {
                        type_exchange_sell: type,
                        usd_sell_summa: usd_sell,
                        uzs_sell_summa: uzs_sell,
                        usd_sell_cours: rate_sell,
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        if (data == 1) {
                            $('#success_exchange_sell').show();
                            $('#error_exchange_sell').hide();
                            $(document).ready(function() {
                                $.ajax({
                                    url: '/php/select_in_mysql_sum.php',
                                    type: 'POST',
                                    dataType: "html",
                                    beforeSend: funcBefore,
                                    success: function(data) {
                                        var sum = data;
                                        if (data >= 0) {
                                            let Summa_num = Number(sum);
                                            var Summa = Summa_num.toLocaleString('ru-RU', {
                                                style: "decimal",
                                                minimumFractionDigits: 2
                                            });
                                            document.getElementById("summa_table").innerHTML = (Summa + " <span>UZS</span>").replace(/,/, '.');
                                            document.getElementById("summa_table_sell").innerHTML = (Summa + " <span>UZS</span>").replace(/,/, '.');
                                        }
                                        if (!data) {
                                            $('#error').show();
                                            $('#error').text(data);
                                        }
                                    }
                                });
                            });
                            $(document).ready(function() {
                                $.ajax({
                                    url: '/php/select_in_mysql_usd.php',
                                    type: 'POST',
                                    dataType: "html",
                                    beforeSend: funcBefore,
                                    success: function(data) {
                                        var usd = data;
                                        if (data >= 0) {
                                            let usd_num = Number(usd);
                                            var usd = usd_num.toLocaleString('ru-RU', {
                                                style: "decimal",
                                                minimumFractionDigits: 2
                                            });
                                            document.getElementById("usd_table").innerHTML = (usd + " <span>USD</span>").replace(/,/, '.');
                                            document.getElementById("usd_table_sell").innerHTML = (usd + " <span>USD</span>").replace(/,/, '.');
                                        }
                                        if (!data) {
                                            $('#error').show();
                                            $('#error').text(data);
                                        }
                                    }
                                });
                            });
                        } else {
                            $('#error_exchange_sell').show();
                            $('#success_exchange_sell').hide();
                            document.getElementById("error_exchange_sell").innerHTML += (data);
                        }
                        if (data == 0) {
                            document.getElementById("error_exchange_sell").innerHTML += ('Ошибка сети подключитесь к интернету');
                        }
                    }
                });
            });
        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $.ajax({
                url: '/php/select_in_mysql_sum.php',
                type: 'POST',
                dataType: "html",
                beforeSend: funcBefore,
                success: function(data) {
                    var sum = data;
                    if (data >= 0) {
                        let Summa_num = Number(sum);
                        var Summa = Summa_num.toLocaleString('ru-RU', {
                            style: "decimal",
                            minimumFractionDigits: 2
                        });
                        document.getElementById("summa_table").innerHTML = (Summa + " <span>UZS</span>").replace(/,/, '.');
                        document.getElementById("summa_table_sell").innerHTML = (Summa + " <span>UZS</span>").replace(/,/, '.');
                    }
                    if (!data) {
                        $('#error').show();
                        $('#error').text(data);
                    }
                }
            });
        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $.ajax({
                url: '/php/select_in_mysql_usd.php',
                type: 'POST',
                dataType: "html",
                beforeSend: funcBefore,
                success: function(data) {
                    var usd = data;
                    if (data >= 0) {
                        let usd_num = Number(usd);
                        var usd = usd_num.toLocaleString('ru-RU', {
                            style: "decimal",
                            minimumFractionDigits: 2
                        });
                        document.getElementById("usd_table").innerHTML = (usd + " <span>USD</span>").replace(/,/, '.');
                        document.getElementById("usd_table_sell").innerHTML = (usd + " <span>USD</span>").replace(/,/, '.');
                    }
                    if (!data) {
                        $('#error').show();
                        $('#error').text(data);
                    }
                }
            });
        });
    </script>
    <script>

    </script>
</body>

</html>