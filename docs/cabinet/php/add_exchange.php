<?php

use Ratchet\Server\EchoServer;

require_once 'config.php';
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
<?php
$type = filter_var(trim($_POST['type_exchange']), FILTER_SANITIZE_STRING);
$usd = filter_var(trim($_POST['exchange_input_one_two']), FILTER_SANITIZE_STRING);
$uzs = filter_var(trim($_POST['exchange_input_two_two']), FILTER_SANITIZE_STRING);
$rate_buy = filter_var(trim($_POST['usd_buy_cours']), FILTER_SANITIZE_STRING);
$date = date(" Y-m-d ");
$time = date(" H:i:s ");

$deleted = 'Удален';
$USD = 'АКШ доллари';
$som = 'Сум';
$nal = 'Наличный';
$buy = 'Покупка';
$sell = 'Продажа';
if ($usd <= 0 && $uzs <= 0) {
	echo "Пожалуйста заполните все поля";
	exit();
}elseif ($rate_buy <= 0) {
    echo "Курс обмен валют не указан";
	exit();
}
$buysum= mysqli_query($conn, "SELECT SUM(UZS) FROM `exchange` WHERE `date`='$date' AND `type`='$buy' AND `cashier`='$user_fio'");
$sellsum = mysqli_query($conn, "SELECT SUM(UZS) FROM `exchange` WHERE `date`='$date'  AND `type`='$sell'AND `cashier`='$user_fio'");
$summa = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$date' AND `valyuta`='$som'AND `typepay`='$nal' AND `FIO`='$user_fio' AND `status`!='$deleted' ");
$summa = mysqli_fetch_all($summa);
$buysum = mysqli_fetch_all($buysum);
$sellsum = mysqli_fetch_all($sellsum);
$sum = $summa[0][0];
$buy = $buysum[0][0];
$sell = $sellsum[0][0];
$result = ($sum + $sell) - $buy; 
$result_sum = $result - $uzs;
if ($result_sum < 0 ) {
    echo "Надостаточно средство на счете ". $result_sum . " UZS";
    exit();
}

$sql = "INSERT INTO exchange (date,time,USD,UZS,type,rate,cashier)
		VALUES
		('$date','$time','$usd','$uzs','$type','$rate_buy','$user_fio')";
if ($conn->query($sql) === TRUE) {
	echo true;
} else {
	echo false;
}

// .$sql.$conn->error;
$conn->close();
  