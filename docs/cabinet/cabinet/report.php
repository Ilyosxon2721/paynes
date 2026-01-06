<?php
require_once '../php/config.php';
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

<body>
<?php $header_title = 'Отчеты';?>
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
        <div class="report-window" id="report-window">
            <div class="report-form">
                <h4 id="report-header">Отчет</h4><br>
                <div class="info-error-report">Ошибка сети</div>
                <div class="info-success-report" style="display: none;">Успешно</div><br>
                <div class="report-table" id="report-table">
                    <form action="" id="report-form">
                        <label for="date">Выберите дату с:</label>
                        <input type="date" name="date" id="date">
                        <br>
                        <label for="date">Выберите дату до: </label>
                        <input type="date" name="dateto" id="dateto">
                        <br>
                        <label for="type-pay">Тип оплаты: </label>
                        <select name="type-pay" id="type_pay" style="margin-left: 12.5%;">
                            <option value="Все" selected>Все</option>
                            <option value="cash">Наличный</option>
                            <option value="uzcard">Безналичный(uzcard)</option>
                            <option value="humo">Безналичный(humo)</option>
                        </select>
                        <br>
                        <label for="type-cash">Валюты: </label>
                        <select name="type-cash" id="type_cash" style="margin-left: 19%;">
                            <option value="Все" selected>Все</option>
                            <option value="Сум">UZS</option>
                            <option value="usd">USD</option>
                        </select>
                        <br>
                        <label for="cashier">Кассир: </label>
                        <select name="cashier" id="cashier" style="margin-left: 20.8%;">
                            <option value="<?=$user_fio?>" selected><?=$user_fio?></option>
                        </select>
                    </form><br>



                    <button class="btn btn-success" id="report-btn">Сформировать</button><br>

                </div>
            </div>
        </div>

    </div>
    <script src="\js\index.js"></script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $("#report-btn").click(function() {
                var date = $("#date").val();
                var dateTo = $("#dateto").val();
                var type_pay = $("#type_pay").val();
                var type_cash = $("#type_cash").val();
                var cashier = $("#cashier").val();
                // console.log(date);
                // console.log(dateTo);
                // console.log(type_pay);
                // console.log(type_cash);
                // console.log(cashier);

                /* Act on the event */
                $.ajax({
                    url: '\\php\\select_report_pays.php',
                    type: 'POST',
                    data: {
                        date: date,
                        dateto: dateTo,
                        type_pay: type_pay,
                        type_cash: type_cash,
                        cashier: cashier,
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        var result = $.parseJSON(data);
                        console.log(result);
                        // if (data != '') {
                        //     var sum = result[0][8];
                        //     var comis = result[0][9];
                        //     var total = result[0][10];
                        //     let sum_num = Number(sum);
                        //     let comis_num = Number(comis);
                        //     let total_num = Number(total);
                        //     var sum_dec = sum_num.toLocaleString('ru-RU', {
                        //         style: "decimal",
                        //         minimumFractionDigits: 2
                        //     });
                        //     var comis_dec = comis_num.toLocaleString('ru-RU', {
                        //         style: "decimal",
                        //         minimumFractionDigits: 2
                        //     });
                        //     var total_sum_dec = total_num.toLocaleString('ru-RU', {
                        //         style: "decimal",
                        //         minimumFractionDigits: 2
                        //     });

                        //     document.getElementById("header_info_pay").innerHTML = `
                        //     <h3 class="title">` + total_sum_dec.replace(/,/, '.') + ` UZS</h3>
                        //     <h3 class="small-text">` + result[0][15] + `</h3><hr><br>
                        //     `;
                        //     document.getElementById("content_info").innerHTML = `
                        //     <h3 class="small-text">ФИО Плательшика</h3>
                        //     <h3 class="title">` + result[0][6] + `</h3>
                        //     <h3 class="small-text">Тип оплаты</h3>
                        //     <h3 class="title" >` + result[0][4] + `</h3> <input type="hidden" id="type_pays_dubl" value="` + result[0][4] + `">
                        //     <h3 class="small-text">Назначение</h3>
                        //     <h3 class="title">` + result[0][7] + `</h3>
                        //     <h3 class="small-text">р/с</h3>
                        //     <h3 class="title">` + result[0][5] + `</h3>
                        //     <h3 class="small-text">Сумма оплаты</h3>
                        //     <h3 class="title">` + sum_dec.replace(/,/, '.') + ` UZS</h3>
                        //     <h3 class="small-text">Комиссия</h3>
                        //     <h3 class="title">` + comis_dec.replace(/,/, '.') + ` UZS</h3>
                        //     <h3 class="small-text">Итого</h3>
                        //     <h3 class="title">` + total_sum_dec.replace(/,/, '.') + ` UZS</h3>
                        //     <h3 class="small-text">№ транзакции</h3>
                        //     <h3 class="title">` + result[0][1] + `</h3>
                        //     <h3 class="small-text">Дата транзакции</h3>
                        //     <h3 class="title">` + result[0][3] + ` ` + result[0][2] + `</h3> <input type="hidden" id="pay_id" value="` + result[0][0] + `">`;
                        //     $('.info_modal').show();

                        //     $('#welcome').hide();
                        //     $('#payments').hide();
                        //     $('#exchange').hide();
                        //     $('#credits').hide();
                        //     $('#inquiry-window').hide();
                        //     $('#report-window').hide();
                        //     $('#collection-window').hide();
                        //     $('#byudjet-window').hide();
                        //     $('#comunal-window').hide();
                        //     $('#chek').hide();
                        // }
                        // if (data == '') {
                        //     console.log(data);
                        // }

                    }
                });
            });
        });
    </script>
</body>

</html>