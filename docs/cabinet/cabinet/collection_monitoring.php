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
            <h4 id="monitoring-header">Инкассовые операции</h4>
            <table id="monitoring_table">
                <?php
                require_once '../php/config.php';
                $date = date(" Y-m-d ");
                $deleted = 'Удален';
                $result = mysqli_query($conn, "SELECT * FROM `incash` WHERE `fio`='$user_fio' AND MONTH(`date`) = MONTH(NOW()) AND YEAR(`date`) = YEAR(NOW()) AND `status`!='$deleted'");
                $result = mysqli_fetch_all($result);
                if ($result != 0) {
                    $num = 1;
                    foreach ($result as $result) {
                ?>
                        <tr id="<?= $result[0] ?>" class="monitoring" style="cursor: pointer;">
                            <td><?= $num++ ?></td>
                            <td><?= $result[0] ?></td>
                            <td><?= $result[1] ?></td>
                            <td><?= $result[2] ?></td>
                            <td><?= $result[5] ?></td>
                            <td><?= $result[4] ?></td>
                            <td><?= $result[3] ?></td>
                            <td><?= $result[7] ?></td>

                        </tr>
                <?php
                    }
                }
                ?>
            </table>

        </div>
        
</body>

</html>