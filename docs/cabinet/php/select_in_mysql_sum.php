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
$today = date(" Y-m-d ");
// $today = date(" 2023-04-03 ");
$sumxr = 'Сум (р/с 001)'; //расход из кассы (выдача наличных в инкассу)
$uzcard = 'Uzcard (р/с 002)'; //расход по оплате через терминал
$humo = 'Humo (р/ч 003)';
$rasxod = 'Расход'; // тип операций
$rasnal = 'Расход по чек'; //расход при обналичке

$USDN = 'Доллар (р/с 840)'; // расход доллар в оплате
$USD = 'АКШ доллари';
$som = 'Сум';

$nal = 'Наличный';

$buy = 'Покупка';
$sell = 'Продажа';

$deleted = 'Удален';
$waiting = 'На проверке';
$success = 'Подтвержден';
$deposit = 'Оплата в МИБ депозит';
$byudjet = 'Оплата в бюджет';
$count = mysqli_query($conn, "SELECT COUNT(jami) FROM `payment` WHERE `data`='$today' AND `status`!='$deleted'AND `FIO`='$user_fio'");
$summa = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$som'AND `typepay`='$nal' AND `FIO`='$user_fio' AND `status`!='$deleted' ");
$beznal = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$som'AND `typepay`!='$nal' AND `FIO`='$user_fio' AND `status`!='$deleted'");
$USD = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$USD' AND `FIO`='$user_fio' AND `status`!='$deleted'");
$sum = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$sumxr' AND `type`='$rasxod' AND `FIO`='$user_fio'");
$sumup = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$uzcard' AND `type`='$rasxod' AND `FIO`='$user_fio'");
$sumhup = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$humo' AND `type`='$rasxod' AND `FIO`='$user_fio'");
$sumun = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$uzcard' AND `type`='$rasnal' AND `FIO`='$user_fio'");
$sumhun = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$humo' AND `type`='$rasnal' AND `FIO`='$user_fio'");
$USDN = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$USDN' AND `type`='$rasxod' AND `FIO`='$user_fio'");

$buydollar = mysqli_query($conn, "SELECT SUM(USD) FROM `exchange` WHERE `date`='$today' AND `type`='$buy' AND `cashier`='$user_fio'");
$selldollar = mysqli_query($conn, "SELECT SUM(USD) FROM `exchange` WHERE `date`='$today'  AND `type`='$sell'AND `cashier`='$user_fio'");

$buysum = mysqli_query($conn, "SELECT SUM(UZS) FROM `exchange` WHERE `date`='$today'  AND `type`='$buy'AND `cashier`='$user_fio'");
$sellsum = mysqli_query($conn, "SELECT SUM(UZS) FROM `exchange` WHERE `date`='$today'  AND `type`='$sell'AND `cashier`='$user_fio'");

$branch = mysqli_query($conn, "SELECT * FROM `users` WHERE `name`='$user_fio'");

$kurs = mysqli_query($conn, "SELECT * FROM `rate` ORDER BY `id` DESC LIMIT 1");
$credit = mysqli_query($conn, "SELECT SUM(credit) FROM `credit` WHERE `date`='$today' AND `recipient`='$user_fio' AND `status`='$success' ");

//print_r($summa)

$summa = mysqli_fetch_all($summa);
$beznal = mysqli_fetch_all($beznal);
$USD = mysqli_fetch_all($USD);
$sum = mysqli_fetch_all($sum);
$sumup = mysqli_fetch_all($sumup);
$sumhup = mysqli_fetch_all($sumhup);
$sumun = mysqli_fetch_all($sumun);
$sumhun = mysqli_fetch_all($sumhun);
$USDN = mysqli_fetch_all($USDN);
$buydollar = mysqli_fetch_all($buydollar);
$selldollar = mysqli_fetch_all($selldollar);
$buysum = mysqli_fetch_all($buysum);
$sellsum = mysqli_fetch_all($sellsum);
$branch = mysqli_fetch_all($branch);
$kurs = mysqli_fetch_all($kurs);
$count = mysqli_fetch_all($count);
$credit = mysqli_fetch_all($credit);





foreach ($summa as $summ) foreach ($USD as $USD) foreach ($sum as $sum) foreach ($sumup as $sumup) foreach ($sumhup as $sumhup)
    foreach ($sumun as $sumun) foreach ($sumhun as $sumhun) foreach ($USDN as $USDN) foreach ($beznal as $beznal)
        foreach ($selldollar as $selldollar) foreach ($buydollar as $buydollar) foreach ($sellsum as $sellsum) foreach ($buysum as $buysum)
            foreach ($kurs as $kurs) foreach ($branch as $branch) foreach ($count as $count) foreach ($credit as $credit) {
?>
<? $ratebuy = $kurs[1];
                $ratesell = $kurs[2]; ?>
      <? $usd = $USD[0] / $ratebuy;

                $usdb = $usd + $buydollar[0];
                $usds = $usdb - $selldollar[0];
                $usdend = $usds - $USDN[0];
        ?>
<? } ?>
<?= $summ[0] * 100 / 100 - $sum[0] * 100 / 100 - $sumun[0] * 100 / 100 - $sumhun[0] * 100 / 100 - $buysum[0] * 100 / 100 + $sellsum[0] * 100 / 100 - $credit[0] * 100 / 100?>