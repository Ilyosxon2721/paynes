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
    <?php $header_title = 'Запрос на кредит'; ?>
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
        <div class="inquiry-window" id="inquiry-window">
            <div class="inquiry" id="inquiry">
                <h4 id="inquiry-header">Запрос на кредит</h4>
                <div class="info-error-inquiry" id="error_inquiry"></div>
                <div class="info-success-inquiry" id="success_inquiry">Запрос успешно отправлен! Пожалуйста ждите обработку запроса</div>
                <div class="inquiry-table" id="inquiry-table">
                    <input type="text" id="inquiry_input_summa" placeholder="Введите сумму">
                    <input type="text" id="inquiry_input_user" value="<?= $user_fio ?>" readonly>
                    <button class="btn btn-success" id="inquiry_btn" style="cursor: pointer;">Отправить</button>
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
            $("#inquiry_btn").on('click', function() {
                var sum = $("#inquiry_input_summa").val();
                var user = $("#inquiry_input_user").val();
                /* Act on the event */
                $.ajax({
                    url: '\\php\\add_inquiry.php',
                    type: 'POST',
                    data: {
                        inquiry_input_summa: sum,
                        inquiry_input_user: user,
                    },
                    dataType: "html",
                    beforeSend: funcBefore,
                    success: function(data) {
                        console.log(data);
                        if (data == 1) {
                            $('#success_inquiry').show();
                            $('#error_inquiry').hide();
                            document.getElementById("inquiry_input_summa").value = ('');
                        } else {
                            $('#error_inquiry').show();
                            $('#success_inquiry').hide();
                            document.getElementById("error_inquiry").innerHTML += (data);
                        }
                        if (data == 0) {
                            document.getElementById("error_exchange_buy").innerHTML += ('Ошибка сети подключитесь к интернету');
                        }

                    }
                });
            });
        });
    </script>
</body>

</html>