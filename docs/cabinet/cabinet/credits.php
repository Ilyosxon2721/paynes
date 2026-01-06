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
<style>
    @font-face {
        font-family: "VerdanaRegular";
        src: url("../fonts/Verdana.ttf") format("truetype");
        font-style: normal;
        font-weight: normal;
    }

    @font-face {
        font-family: "VerdanaBold";
        src: url("../fonts/Verdana-Bold.ttf") format("truetype");
    }

    @font-face {
        font-family: "TimesRegular";
        src: url("../fonts/timesncyrmt.ttf") format("truetype");
        font-style: normal;
        font-weight: normal;
    }

    h3.small-text {

        font-style: normal;
        font-weight: 500;
        font-size: 13px;
        line-height: 18px;
        color: #868686;
    }


    h3.title {
        font-size: 1.1rem !important;
        font-weight: 500;
        line-height: 1.75rem;
        color: #1a1a1a;
    }

    div.chek_modal {
        background-color: white;
        width: 64rem;
        height: 28rem;
        margin-left: 12rem;
        top: 14%;
        position: fixed;
    }

    div.content-chek {
        background-color: white;
        border: 1px solid black;
        width: 60rem;
        height: 310px;
        margin-left: 2rem;
    }

    div.content-chek-left {
        float: left;
        text-align: center;
        width: 15rem;
    }

    div.content-chek-right {
        border-left: 1px solid black;
        float: right;
        width: 44rem;
    }

    h4.header-chek {
        text-align: center;
        border-bottom: 1px solid black;
        font-size: 12px;
        margin-top: 0;
        margin-bottom: 0;
    }

    h4.chek-title {
        font-family: "VerdanaBold";
        font-size: 12px;
        margin-top: 2px;
        margin-bottom: 2px;
        padding: 2px;
    }

    h4.chek-title-left {
        font-family: "VerdanaBold";
        font-size: 12px;
        margin-top: 2px;
        margin-bottom: 1px;
    }

    span.chek-small-text {
        font-size: 13px;
        font-weight: 300;
        border-bottom: 1px solid black;
        float: right;
        width: 30rem;
        margin-top: 0;
        margin-bottom: 0;
        height: 16px;
        overflow: hidden;

    }

    h4.chek-title-two {
        text-align: center;
        font-size: 16px;
        font-weight: 600;
        margin-top: 14px;
        margin-bottom: 4px;
        font-family: "VerdanaBold";
    }

    p.chek-small-text-two {
        font-size: 11px;
        border-top: 1px solid black;
        margin-top: 0;
        margin-bottom: 0;
    }

    h4.chek-title-three {
        font-size: 12px;
        padding: 2px;
        margin-top: 0;
        margin-bottom: 0;
    }

    span.chek-small-text-three {
        font-size: 12px;
        float: right;
        width: 30rem;
        text-align: center;
        margin-top: 0;
        margin-bottom: 0;
    }

    div.appoint-chek-title {
        border-top: 1px solid black;
        border-bottom: 1px solid black;
        margin-top: 0;
        margin-bottom: 0;
    }

    h4.chek-title-four {
        font-size: 12px;
        padding: 4.6px;
        margin-left: 27rem;
        border-left: 1px solid black;
        border-bottom: 1px solid black;
        margin-top: 0;
        margin-bottom: 0;
    }

    span.chek-small-text-four {
        font-size: 14px;
        float: right;
        width: 8rem;
        text-align: right;
        border-left: 1px solid black;
        margin-top: 0;
        margin-bottom: 0;
    }

    div.pay_credit_table {
        display: none;
        width: 40%;
        transform-origin: center center;
        max-width: 580px;
        margin-left: 25%;
        position: fixed;
        top: 10%;
        background-color: white;
        border-radius: 15px;
        border: 1px solid #d5d5d5;
        height: 340px;
        -webkit-overflow-scrolling: touch;
        z-index: 99;
        overflow: hidden;
        transition: transform 0.15s ease 0s, opacity 0.15s ease 0s;
        transform: scale(1);

    }

    input {
        width: 90%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid lightgray;
        margin-top: 5%;
        margin-left: 5%;
        font-size: 16px;
    }

    button#btn_pay_control {
        background-color: #198754;
        border: 1px solid #198754;
        color: #fff;
        border-radius: 8px;
        padding: 10px;
        margin-left: 10%;
        width: 35%;
        height: 50px;
        font-size: 18px;
        margin-bottom: 15px;
        cursor: pointer;
    }

    button#btn-pay-back {
        background-color: #dc3545;
        border: 1px solid #dc3545;
        color: #fff;
        border-radius: 8px;
        padding: 10px;
        margin-left: 10%;
        width: 35%;
        height: 50px;
        font-size: 18px;
        margin-bottom: 15px;
        cursor: pointer;
        margin-top: 20px;
    }

    h2 {
        padding: 10px;
    }

    div#error {
        display: none;
        padding: 15px;
        background-color: #f7dada;
        color: #f92828;
        width: 80%;
        margin-left: 10%;
        border-radius: 5px;
    }

    div#success {
        background-color: #e0f0e5;
        color: #2db979;
        text-align: center;
        width: 80%;
        margin-left: 10%;
        border-radius: 5px;
        padding: 10px;
        display: none;
    }

    @media (max-width: 1440px) {
        #xr_credit_td {
            display: none;
        }

        div.info_modal {
            display: none;
            width: 40%;
            margin-left: 16%;
        }
        div.pay_credit_table {
        display: none;
        width: 40%;
        margin-left: 16%;
       

    }
    }
</style>

<body>
    <?php $header_title = 'Кредиты'; ?>
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
    <input type="hidden" name="user_fio" id="user_fio" value="<?= $user_fio ?>">
    <div class="window" id="window">
        <div class="credit-window" id="credit-window">
            <table id="credit_table">
                <?php
                require_once '../php/config.php';
                $date = date(" Y-m-d ");
                $result = mysqli_query($conn, "SELECT * FROM `credit` WHERE `recipient`='$user_fio'");
                $result = mysqli_fetch_all($result);
                if ($result != 0) {
                    $num = 1;
                    foreach ($result as $result) {
                ?>
                        <tr id="<?= $result[0] ?>" class="credit" style="cursor: pointer;">

                            <td><?= $num++ ?></td>
                            <td><?= $result[0] ?></td>
                            <td style="width: 100px;"><?= $result[1] ?></td>
                            <td style="width: 100px;"><?= $result[2] ?></td>
                            <td><?= $result[3] ?></td>
                            <td style="width: 190px;" id="xr_credit_td"><?= $result[4] ?></td>
                            <td><?= $result[5] ?></td>
                            <td style="color: green;"><?= $result[6] ?></td>
                            <td style="color: red;"><?= $result[7] ?></td>
                            <td><?= $result[8] ?></td>
                            <td><?= $result[9] ?></td>

                        </tr>
                <?php
                    }
                }
                ?>
            </table>

        </div>
        <div class="info_window">
            <div class="info_modal">
                <div class="content">
                    <div class="content-left">
                        <div class="header_info_pay" id="header_info_pay">
                            <br>
                            <h3 class="small-text">Успешно</h3>
                            <hr>
                        </div>
                        <div class="content_info" id="content_info">

                        </div>
                    </div>
                    <div class="content-right">
                        <div class="pay_credit">
                            <img class="pay_credit" src="\images\pay_credit.svg" alt="" width="76" height="76">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="pay_credit_table">
            <div class="pay_credit_top">
                <h2>Погашение кредита</h2>
                <hr>
            </div>
            <div class="error" id="error">

            </div>
            <div class="success" id="success">
                <p style="text-align: center;">Успешно</p>
            </div>
            <div class="pay_credit_content" id="pay_credit_content">
            </div>
            <div class="pay_credit_footer">
                <button class="btn-pay-back" id="btn-pay-back">Назад</button>
                <button class="btn_pay_control" id="btn-pay-control">Оплатить</button>

            </div>
        </div>
    </div>
    <script src="\js\index.js"></script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $(".credit").click(function() {
                var id = $(this).attr("id");
                // console.log(id);
                // $('.info_modal').show();

                /* Act on the event */
                $.ajax({
                    url: '\\php\\select_info_credit.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        var result = $.parseJSON(data);
                        if (data != '') {
                            var debit_sum = result[0][6];
                            var credit_sum = result[0][7];
                            var remainder_sum = credit_sum - debit_sum;
                            let debit_sum_num = Number(debit_sum);
                            let credit_sum_num = Number(credit_sum);
                            let remainder_sum_num = Number(remainder_sum);
                            var debit_dec = debit_sum_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            var credit_dec = credit_sum_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            var remainder_dec = remainder_sum_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });


                            document.getElementById("header_info_pay").innerHTML = `
                            <h3 class="title">` + remainder_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">` + result[0][9] + `</h3><hr><br>
                            `;
                            document.getElementById("content_info").innerHTML = `
                            <h3 class="small-text">ФИО Получателя кредита</h3>
                            <h3 class="title">` + result[0][3] + `</h3>
                            <h3 class="small-text">р/с кредита</h3>
                            <h3 class="title" >` + result[0][4] + `</h3>
                            <h3 class="small-text">Место работы кассира</h3>
                            <h3 class="title">` + result[0][5] + `</h3>
                            <h3 class="small-text">Сумма кредита</h3>
                            <h3 class="title">` + credit_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">Оплачено</h3>
                            <h3 class="title">` + debit_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">Остаток</h3>
                            <h3 class="title">` + remainder_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">№ транзакции</h3>
                            <h3 class="title">` + result[0][0] + `</h3>
                            <h3 class="small-text">Дата транзакции</h3>
                            <h3 class="title">` + result[0][1] + ` ` + result[0][2] + `</h3> <input type="hidden" id="credit_id" value="` + result[0][0] + `">
                            <h3 class="small-text">Кредитор</h3>
                            <h3 class="title">` + result[0][8] + `</h3>`;
                            document.getElementById("pay_credit_content").innerHTML = `<pre style="float:right; padding:5px;">Текущий остаток кредита ` + remainder_dec.replace(/,/, '.') + ` сўм </pre>
                            <input type="hidden" name="id_credit" id="id_credit" value="` + result[0][0] + `">
                            <input type="hidden" name="remainder" id="remainder" value="` + remainder_sum + `">
                            <input type="hidden" name="name_pay" id="name_pay" value="Погашение кредита">
                            <input type="hidden" name="type_pay" id="type_pay" value="Наличный">
                            <input type="hidden" name="last_debit" id="last_debit" value="` + result[0][6] + `">
                            <input type="hidden" name="unique" id="unique" value="<?php
                                                                                    echo substr(md5(time()), 0, 16);
                                                                                    echo $user_id;
                                                                                    ?>">

                            <input type="hidden" name="xr_credit" id="xr_credit" value="` + result[0][4] + `">
                            <input type="text" name="fio_cashier" id="credit_fio_cashier" value="` + result[0][3] + `" readonly>
                            <input type="text" name="summa_credit" id="summa_credit" placeholder="0.00">
                            
                            `;

                            $('.info_modal').show();
                            $('#welcome').hide();
                            $('#payments').hide();
                            $('#exchange').hide();
                            $('#credits').hide();
                            $('#inquiry-window').hide();
                            $('#report-window').hide();
                            $('#collection-window').hide();
                            $('#byudjet-window').hide();
                            $('#comunal-window').hide();
                            $('#chek').hide();
                        }
                        if (data == '') {
                            console.log(data);
                        }

                    }
                });
            });
        });
        $(document).mouseup(function(e) {
            var info_modal = $(".info_modal");
            var credit_modal = $(".pay_credit_table");
            if (info_modal.has(e.target).length === 0) {
                info_modal.hide();
            }
            if (credit_modal.has(e.target).length === 0) {
                credit_modal.hide();
            }

        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $(".pay_credit").click(function() {
                var id = $("#credit_id").val();
                $('.info_modal').hide();
                $('.pay_credit_table').show();
            });
        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $("#btn-pay-control").bind('click', function() {
                var id = $("#id_credit").val();
                var fio = $("#credit_fio_cashier").val();
                var summa = $("#summa_credit").val();
                var remainder = $("#remainder").val();
                var name_pay = $("#name_pay").val();
                var type_pay = $("#type_pay").val();
                var xr_credit = $("#xr_credit").val();
                var unique = $("#unique").val();
                var last_debit = $("#last_debit").val();
                console.log(id);
                $.ajax({
                    url: '\\php\\pay_credit.php',
                    type: 'POST',
                    data: {
                        id_credit: id,
                        credit_fio_cashier: fio,
                        summa_credit: summa,
                        remainder: remainder,
                        name_pay: name_pay,
                        type_pay: type_pay,
                        xr_credit: xr_credit,
                        unique: unique,
                        last_debit: last_debit
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        if (data == true) {
                            $('#success').show();
                        }
                        if (data != true) {
                            $('#error').show();
                            document.getElementById("error").innerHTML = `<p style="text-align:center; "> ` + data + ` </p>`;
                        }
                    }
                });
            });
        });
        $(document).ready(function() {
            $("#btn-pay-back").bind('click', function() {
                window.location.href = '/cabinet/credits.php';
            });
        });
    </script>
</body>

</html>