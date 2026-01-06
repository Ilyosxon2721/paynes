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
    @media (min-width: 1400px) and (max-width: 1600px) {
        div#collection-window {
            position: absolute;
            background-color: white;
            width: 35%;
            top: 10%;
            left: 32%;
            border-radius: 8px;
        }
    }
</style>

<body>
    <?php $header_title = 'Выдача денег'; ?>
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
        <div class="collection-window" id="collection-window">
            <div class="collection-form">
                <h4 id="collection-header">Выдача</h4><br>
                <div class="info-error-collection" id="info_error">Ошибка сети</div>
                <div class="info-success-collection" id="info_success">Успешно</div><br>
                <div class="collection-table" id="collection-table">
                    <form id="collection-form">
                        <label for="type_operation">Тип: </label>
                        <select name="type_operation" id="type_operation" style="margin-left:108px;">
                            <option value="Расход">Выдача</option>
                            <option value="Приход">Подкрепление</option>
                        </select><br>
                        <label for="type_cash">Тип выдачи денег: </label>
                        <select name="type_cash" id="type_cash" style="margin-left:2px;">
                            <option value="Сум (р/с 001)">UZS</option>
                            <option value="Доллар (р/с 840)">USD</option>
                            <option value="Uzcard (р/с 002)">UZCARD</option>
                            <option value="Humo (р/с 003)">HUMO</option>
                        </select>
                        <label for="cashier">Кассир: </label>
                        <select name="cashier" id="cashier" style="margin-left:82px;">
                            <option value="<?= $user_fio ?>" selected><?= $user_fio ?></option>
                        </select>
                        <input type="text" name="summa" id="summa" placeholder="Введите сумму...">
                    </form><br>
                    <button class="success" id="collection-btn" style="cursor: pointer;">Подтвердить</button><br>
                </div>
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
            $(".success").click(function() {
                var type_cash = $("#type_cash").val();
                var cashier = $("#cashier").val();
                var summa = $("#summa").val();
                var type_operation = $("#type_operation").val();
                /* Act on the event */
                $.ajax({
                    url: '\\php\\confirm_incash.php',
                    type: 'POST',
                    data: {
                        type_cash: type_cash,
                        cashier: cashier,
                        summa: summa,
                        type_operation: type_operation
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        if (data == true) {
                            $('#info_success').show();
                        }
                        if (data != true) {
                            $('#info_error').show();
                            document.getElementById("info_error").innerHTML = `<p>` + data + `</p>`;
                        }
                        if (data == false) {
                            $('#info_error').show();
                        }
                    }
                });
            });
        });
    </script>
</body>

</html>