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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <title>Anesi Kassa</title>
</head>
<style>

</style>

<body>
    <?php $header_title = 'Главное';?>
    <div class="navbar" id="navbar">
        <?php require_once 'navbar.php';?>
    </div>
    <hr>
    <div class="menu-left" id="menu-left">
        <?php require_once 'menu-left.php'; ?>
    </div>
    <div class="menu-right" id="menu-right">
        <?php require_once 'menu-right.php'; ?>
    </div>
    <div class="window" id="window">

        <div class="welcome" id="welcome">
            <?php require_once 'window.php'; ?>
        </div>
        <div class="payments" id="payments" style="display: none;">
            <?php require_once 'payment.php'; ?>
        </div>
        <div class="exchange" id="exchange" style="display: none;">
            <?php require_once 'exchange.php'; ?>
        </div>
        <div class="credits" id="credits" style="display: none;">
            <?php require_once 'credits.php'; ?>
        </div>
        <div class="inquiry-window" id="inquiry-window" style="display: none;">
            <?php require_once 'inquiry.php'; ?>
        </div>
        <div class="monitoring-window" id="monitoring-window" style="display: none;">
            <?php require_once 'monitoring.php'; ?>
        </div>
        <div class="report-window" id="report-window" style="display: none;">
            <?php require_once 'report.php'; ?>
        </div>
        <div class="collection-window" id="collection-window" style="display: none;">
            <?php require_once 'collection.php'; ?>
        </div>
        <div class="byudjet-window" id="byudjet-window" style="display: none;">
            <?php require_once 'payment\byudjet.php'; ?>
        </div>
        <div class="comunal-window" id="comunal-window" style="display: none;">
            <?php require_once 'payment\comunal.php'; ?>
        </div>
    </div>
    <script src="\js\index.js"></script>

</body>

</html>