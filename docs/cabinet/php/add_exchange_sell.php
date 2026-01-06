<?php
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
$type = filter_var(trim($_POST['type_exchange_sell']), );
$usd = filter_var(trim($_POST['usd_sell_summa']), );
$uzs = filter_var(trim($_POST['uzs_sell_summa']), );
$rate_sell = filter_var(trim($_POST['usd_sell_cours']), );
$date = date(" Y-m-d ");
$time = date(" H:i:s ");

// echo $type;
// echo $usd;
// echo $uzs;
// echo $rate_sell;
// echo $date;
// echo $time;

$buy = 'Покупка';
$sell = 'Продажа';
$deleted = 'Удален';
$USD = 'АКШ доллари';
$som = 'Сум';
$nal = 'Наличный';
if ($usd <= 0 && $uzs <= 0) {
	echo "Пожалуйста заполните все поля";
	exit();
}elseif ($rate_sell <= 0) {
    echo "Курс обмен валют не указан";
	exit();
}
$buydollar = mysqli_query($conn, "SELECT SUM(USD) FROM `exchange` WHERE `date`='$date' AND `type`='$buy' AND `cashier`='$user_fio'");
$selldollar = mysqli_query($conn, "SELECT SUM(USD) FROM `exchange` WHERE `date`='$date'  AND `type`='$sell'AND `cashier`='$user_fio'");
$buydollar = mysqli_fetch_all($buydollar);
$selldollar = mysqli_fetch_all($selldollar);
$buy = $buydollar[0][0];
$sell = $selldollar[0][0];

if (!$sell) {
	$sell = 0;
}
if (!$buy) {
	$buy = 0;
}
$result = $buy - $sell;
$result_usd = $result - $usd;
if ($result_usd < 0 ) {
    echo "Надостаточно средство на счете ". $result_usd . " USD";
    exit();
}

$sql = "INSERT INTO exchange (date,time,USD,UZS,type,rate,cashier)
		VALUES
		('$date','$time','$usd','$uzs','$type','$rate_sell','$user_fio')";
if ($conn->query($sql) === TRUE) {
	echo true;
} else {
	echo false;
}

// .$sql.$conn->error;
$conn->close();
  