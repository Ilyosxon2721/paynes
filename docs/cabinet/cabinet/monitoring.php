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
    <?php $header_title = 'Мониторинг'; ?>
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
        <div class="monitoring-window" id="monitoring-window">
            <h4 id="monitoring-header">Мониторинг платежей</h4>
            <table id="monitoring_table">
                <?php
                require_once '../php/config.php';
                $date = date(" Y-m-d ");
                $result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `data` = '$date' AND `FIO`='$user_fio' AND `status` != 'Удален'");
                $result = mysqli_fetch_all($result);
                if ($result != 0) {
                    $num = 1;
                    foreach ($result as $result) {
                ?>
                        <tr id="<?= $result[0] ?>" class="monitoring" style="cursor: pointer;">
                            <td><?= $num++ ?></td>
                            <td style="width: 100px;"><?= $result[2] ?></td>
                            <td><?= $result[3] ?></td>
                            <td style="width: 190px;"><?= $result[4] ?></td>
                            <td><?= $result[5] ?></td>
                            <td><?= $result[7] ?></td>
                            <td><?= $result[10] ?></td>
                            <td><?= $result[11] ?></td>
                            <td><?= $result[15] ?></td>

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
                            <!-- <h3 class="title">78 050.20 UZS</h3> -->
                            <h3 class="small-text">Успешно</h3>
                            <hr>
                        </div>
                        <div class="content_info" id="content_info">

                        </div>
                    </div>
                    <div class="content-right">
                        <div class="print_dubl_chek">
                            <img class="print_chek" src="\images\print.svg" alt="" width="64" height="64">
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <div class="window-dubl-chek" style="display: none;">
            <div class="chek_modal"><br>
                <h3 style="margin-left: 15px;">Квитанция</h3>
                <hr><br>

                <?php require_once 'payment/dubl_chek.php'; ?>
                <br>
                <hr>
                <br>
                <div class="chek-footer" style="float: right; margin-right:2.5rem">
                    <button onclick="closeAll()" id="cancel-chek">Отмена</button>
                    <button onClick="printEl('print')" id="print">Печать</button>
                </div>
            </div>

        </div>
    </div>
    <script src="\js\index.js"></script>
    <script>
        function printEl(print) {
            var printContents = document.getElementById(print).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            window.location.href = '\\cabinet\\monitoring.php';
        }

        function closeAll() {
            window.location.href = '\\cabinet\\monitoring.php';
        }
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $(".monitoring").click(function() {
                var id = $(this).attr("id");
                // console.log(id);
                // $('.info_modal').show();

                /* Act on the event */
                $.ajax({
                    url: '\\php\\select_info_pays.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        var result = $.parseJSON(data);
                        console.log(result);
                        if (data != '') {
                            var sum = result[0][8];
                            var comis = result[0][9];
                            var total = result[0][10];
                            let sum_num = Number(sum);
                            let comis_num = Number(comis);
                            let total_num = Number(total);
                            var sum_dec = sum_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            var comis_dec = comis_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            var total_sum_dec = total_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });

                            document.getElementById("header_info_pay").innerHTML = `
                            <h3 class="title">` + total_sum_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">` + result[0][15] + `</h3><hr><br>
                            `;
                            document.getElementById("content_info").innerHTML = `
                            <h3 class="small-text">ФИО Плательшика</h3>
                            <h3 class="title">` + result[0][6] + `</h3>
                            <h3 class="small-text">Тип оплаты</h3>
                            <h3 class="title" >` + result[0][4] + `</h3> <input type="hidden" id="type_pays_dubl" value="` + result[0][4] + `">
                            <h3 class="small-text">Назначение</h3>
                            <h3 class="title">` + result[0][7] + `</h3>
                            <h3 class="small-text">р/с</h3>
                            <h3 class="title">` + result[0][5] + `</h3>
                            <h3 class="small-text">Сумма оплаты</h3>
                            <h3 class="title">` + sum_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">Комиссия</h3>
                            <h3 class="title">` + comis_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">Итого</h3>
                            <h3 class="title">` + total_sum_dec.replace(/,/, '.') + ` UZS</h3>
                            <h3 class="small-text">№ транзакции</h3>
                            <h3 class="title">` + result[0][1] + `</h3>
                            <h3 class="small-text">Дата транзакции</h3>
                            <h3 class="title">` + result[0][3] + ` ` + result[0][2] + `</h3> <input type="hidden" id="pay_id" value="` + result[0][0] + `">`;
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
            if (info_modal.has(e.target).length === 0) {
                info_modal.hide();
            }
        });
    </script>
    <script>
        function funcBefore() {
            $("#information").text("Ожидание данных...");
        }
        $(document).ready(function() {
            $(".print_dubl_chek").click(function() {
                var id = $("#pay_id").val();
                var type = $("#type_pays_dubl").val();
                // console.log(type);
                // console.log(id);
                // $('.info_modal').show();

                /* Act on the event */
                $.ajax({
                    url: '\\php\\select_type_pays.php',
                    type: 'POST',
                    data: {
                        id: id,
                        type: type
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        var result = $.parseJSON(data);

                        console.log(result);
                        console.log(result[0][0][0]);
                        console.log(result[1][0][0]);
                        console.log(result[0][0][0][8]);
                        console.log(result[0][0][0][9]);
                        console.log(result[0][0][0][10]);
                        console.log(result[0][0][0][11]);
                        console.log(result[0][0][0][2]);
                        console.log(result[0][0][0][3]);

                        if (data) {
                            var sum = result[0][0][0][8];
                            var comis = result[0][0][0][9];
                            var total = result[0][0][0][10];
                            let sum_num = Number(sum);
                            let comis_num = Number(comis);
                            let total_num = Number(total);
                            var sum_dec = sum_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            var comis_dec = comis_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            var total_sum_dec = total_num.toLocaleString('ru-RU', {
                                style: "decimal",
                                minimumFractionDigits: 2
                            });
                            console.log(sum_dec);

                            document.getElementById("type_cash_dubl").innerHTML =
                                `Тулов тури: <span style="font-family: 'VerdanaBold';">` +
                                result[0][0][0][11] + `</span>`;
                            document.getElementById("date_dubl").innerHTML = result[0][0][0][2];
                            document.getElementById("time_dubl").innerHTML = result[0][0][0][3];
                            document.getElementById("unique_number_dubl").innerHTML = `№` +
                                result[0][0][0][1];
                            // document.getElementById("sum_dubl").innerHTML = total_sum_dec
                            //     .replace(/,/, '.') + ` сум`;
                            document.getElementById("sum_dubl_two").innerHTML =
                                `Сумма платежа <span class="chek-small-text-four">` + sum_dec
                                .replace(/,/, '.') + `</span>`;
                            document.getElementById("comis_dubl").innerHTML =
                                `Комиссия <span class="chek-small-text-four">` + comis_dec
                                .replace(/,/, '.') + `</span>`;
                            document.getElementById("total_dubl").innerHTML =
                                `Итого <span class="chek-small-text-four">` + total_sum_dec
                                .replace(/,/, '.') + `</span>`;
                            document.getElementById("cashier_dubl").innerHTML = result[0][0][0][
                                18
                            ];
                            document.getElementById("type_pay_dubl").innerHTML = result[1][0][0]
                                [2];
                            document.getElementById("bank_dubl").innerHTML =
                                `Банк муассаси <span class="chek-small-text">` + result[1][0][0]
                                [3] + `</span>`;
                            document.getElementById("xr_dubl").innerHTML =
                                `Хисоб ракам  <span class="chek-small-text">` + result[1][0][0][
                                    4
                                ] + `</span>`;
                            document.getElementById("mfo_dubl").innerHTML =
                                `МФО  <span class="chek-small-text">` + result[1][0][0][5] +
                                `</span>`;
                            document.getElementById("inn_dubl").innerHTML =
                                `ИНН  <span class="chek-small-text">` + result[1][0][0][6] +
                                `</span>`;
                            document.getElementById("list_dubl").innerHTML = result[1][0][0][
                                    8
                                ] + ` <span class="chek-small-text">` + result[0][0][0][5] +
                                `</span>`;
                            document.getElementById("fio_dubl").innerHTML = result[0][0][0][6] +
                                ` <p class="chek-small-text-two">(фамилияси, исми ва отасининг исми / ФИО)</p>`;
                            document.getElementById("adres_dubl").innerHTML = result[0][0][0][
                                    7
                                ] +
                                ` <p class="chek-small-text-two">(Назначение платежа / Адрес)</p>`;
                            document.getElementById("appoint_dubl").innerHTML =
                                `Тулов максади <span class="chek-small-text-three">` + result[0]
                                [0][0][4] + `</span>`;
                            var img =
                                "<img src='https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=http://chek.xn--80aumk.uz/?id=" +
                                result[0][0][0][1] + "' alt='qr_code'>";
                            document.getElementById("qr_dubl").innerHTML = img;

                            $('.info_modal').hide();
                            $('.window-dubl-chek').show();

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
            var chek_modal = $(".window-dubl-chek");
            if (info_modal.has(e.target).length === 0) {
                info_modal.hide();
            }
            if (chek_modal.has(e.target).length === 0) {
                chek_modal.hide();
            }
        });
    </script>
</body>

</html>