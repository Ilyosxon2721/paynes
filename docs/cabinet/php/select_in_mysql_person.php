<?php
$pos = mysqli_query($conn, "SELECT * FROM `users` WHERE `id`='$user_id'");
$pos = mysqli_fetch_all($pos);
//print_r($pos);
foreach ($pos as $poss)
?>
    <? $position = $poss[5];

    ?>
<?php
$today = date(" Y-m-d ");
// $today = date(" 2023-04-03 ");
$sumxr = 'Сум (р/с 001)'; //расход из кассы (выдача наличных в инкассу)
$uzcard = 'Uzcard (р/с 002)'; //расход по оплате через терминал
$humo = 'Humo (р/ч 003)';
$rasxod = 'Расход'; // тип операций
$prixod = 'Приход'; // тип операций
$rasnal = 'Расход по чек'; //расход при обналичке

$USDN = 'Доллар (р/с 840)'; // расход доллар в оплате
$USD = 'АКШ доллари';
$som = 'Сум';

$nal = 'Наличный';

$buy = 'Покупка';
$sell = 'Продажа';
$bez_nal_uzcard = 'Безналичный(uzcard)';
$bez_nal_humo = 'Безналичный(humo)';
$deleted = 'Удален';
$waiting = 'На проверке';
$success = 'Подтвержден';
$deposit = 'Оплата в МИБ депозит';
$byudjet = 'Оплата в бюджет';
$count = mysqli_query($conn, "SELECT COUNT(jami) FROM `payment` WHERE `data`='$today' AND `status`!='$deleted'AND `FIO`='$user_fio'");//количество проводок
$summa = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$som' AND `typepay`='$nal' AND `FIO`='$user_fio' AND `status`!='$deleted' "); //общая сумма наличных проводок кассира
$total = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$som' AND `FIO`='$user_fio' AND `status`!='$deleted' "); //общая сумма проводок кассира
$beznal_uzcard = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$som'AND `typepay`='$bez_nal_uzcard' AND `FIO`='$user_fio' AND `status`!='$deleted'");//общая сумма uzcard проводок кассира
$beznal_humo = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$som'AND `typepay`='$bez_nal_humo' AND `FIO`='$user_fio' AND `status`!='$deleted'");//общая сумма humo проводок кассира
$USD = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE `data`='$today' AND `valyuta`='$USD' AND `FIO`='$user_fio' AND `status`!='$deleted'");//общая сумма проводок в валюте кассира
$incash_sum = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$sumxr' AND `type`='$rasxod' AND `FIO`='$user_fio'"); //общая сумма инкасса  кассира
$incash_uzcard = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$uzcard' AND `type`='$rasxod' AND `FIO`='$user_fio'");//общая сумма инкасса терминал uzcard кассира
$incash_humo = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$humo' AND `type`='$rasxod' AND `FIO`='$user_fio'");//общая сумма инкасса терминал humo кассира
$cashing_uzcard = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$uzcard' AND `type`='$rasnal' AND `FIO`='$user_fio'");//общая сумма обналички денег через терминал uzcard кассира
$cashing_humo = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$humo' AND `type`='$rasnal' AND `FIO`='$user_fio'");//общая сумма обналички через терминал humo кассира
$incash_usd = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$USDN' AND `type`='$rasxod' AND `FIO`='$user_fio'");//общая сумма инкассы в валюте кассира

$outcash_usd = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$USDN' AND `type`='$prixod' AND `FIO`='$user_fio'");//общая сумма подкрепление в валюте кассира
$outcash_sum = mysqli_query($conn, "SELECT SUM(sum) FROM `incash` WHERE `date`='$today' AND `xisobraqam`='$sumxr' AND `type`='$prixod' AND `FIO`='$user_fio'");//общая сумма подкрепление кассира


$buydollar = mysqli_query($conn, "SELECT SUM(USD) FROM `exchange` WHERE `date`='$today' AND `type`='$buy' AND `cashier`='$user_fio'"); //общая сумма покупка доллара в долларе
$selldollar = mysqli_query($conn, "SELECT SUM(USD) FROM `exchange` WHERE `date`='$today'  AND `type`='$sell'AND `cashier`='$user_fio'");//общая сумма продажа доллара в долларе

$buysum = mysqli_query($conn, "SELECT SUM(UZS) FROM `exchange` WHERE `date`='$today'  AND `type`='$buy'AND `cashier`='$user_fio'"); //общая сумма покупка доллара в сум
$sellsum = mysqli_query($conn, "SELECT SUM(UZS) FROM `exchange` WHERE `date`='$today'  AND `type`='$sell'AND `cashier`='$user_fio'");//общая сумма продажа доллара в сум

$branch = mysqli_query($conn, "SELECT * FROM `users` WHERE `name`='$user_fio'");//филиал кассира

$kurs = mysqli_query($conn, "SELECT * FROM `rate` ORDER BY `id` DESC LIMIT 1");//курс обмен валют
$credit = mysqli_query($conn, "SELECT SUM(credit) FROM `credit` WHERE `date`='$today' AND `recipient`='$user_fio' AND `status`='$success' ");//общая сумма выданного кредита на кассир сегоднешный

//print_r($summa)

$summa = mysqli_fetch_all($summa);
$beznal_uzcard = mysqli_fetch_all($beznal_uzcard);
$beznal_humo = mysqli_fetch_all($beznal_humo);
$USD = mysqli_fetch_all($USD);
$incash_sum = mysqli_fetch_all($incash_sum);
$incash_uzcard = mysqli_fetch_all($incash_uzcard);
$incash_humo = mysqli_fetch_all($incash_humo);
$cashing_uzcard = mysqli_fetch_all($cashing_uzcard);
$cashing_humo = mysqli_fetch_all($cashing_humo);
$incash_usd = mysqli_fetch_all($incash_usd);
$outcash_usd = mysqli_fetch_all($outcash_usd);
$outcash_sum = mysqli_fetch_all($outcash_sum);
$buydollar = mysqli_fetch_all($buydollar);
$selldollar = mysqli_fetch_all($selldollar);
$buysum = mysqli_fetch_all($buysum);
$sellsum = mysqli_fetch_all($sellsum);
$branch = mysqli_fetch_all($branch);
$kurs = mysqli_fetch_all($kurs);
$count = mysqli_fetch_all($count);
$credit = mysqli_fetch_all($credit);
$total = mysqli_fetch_all($total);





foreach ($summa as $summa) foreach ($USD as $USD) foreach ($incash_sum as $incash_sum) foreach ($incash_uzcard as $incash_uzcard) foreach ($incash_humo as $incash_humo)
    foreach ($cashing_uzcard as $cashing_uzcard) foreach ($cashing_humo as $cashing_humo) foreach ($incash_usd as $incash_usd) foreach ($beznal_uzcard as $beznal_uzcard) 
    foreach ($beznal_humo as $beznal_humo) foreach ($outcash_usd as $outcash_usd) foreach ($outcash_sum as $outcash_sum)
        foreach ($selldollar as $selldollar) foreach ($buydollar as $buydollar) foreach ($sellsum as $sellsum) foreach ($buysum as $buysum)
            foreach ($kurs as $kurs) foreach ($branch as $branch) foreach ($count as $count) foreach ($credit as $credit) foreach ($total as $total) {


$ratebuy = $kurs[1];
$ratesell = $kurs[2];
$usd = $USD[0] / $ratebuy;

                $usdb = $usd + $buydollar[0];
                $usds = $usdb - $selldollar[0];
                $usdend = $usds - $incash_usd[0] + $outcash_usd[0];
        
} 