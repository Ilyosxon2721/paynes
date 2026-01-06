<?
    $pos = mysqli_query($conn, "SELECT * FROM `users` WHERE `id`='$user_id'");
    $pos = mysqli_fetch_all($pos);
    foreach ($pos as $poss)
    ?>
    <? $position = $poss[5];
        $salary = $poss[8];
    ?>
    <?
    $month = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE MONTH(`data`) = MONTH(NOW()) AND YEAR(`data`) = YEAR(NOW()) AND `FIO`='$user_fio'");
    $month = mysqli_fetch_all($month);
    foreach ($month as $month) {
    ?>
    <? }
    ?>
    <!-- MONTH(NOW()) -->
        <?
        $admin = 'Супер Админ';  
        $person = 'Пользовательский'; 
        $deposit = 'Оплата в МИБ депозит';
        $byudjet = 'Оплата в бюджет';
        $deleted = 'Удален';
        $waiting = 'На проверке';
        $jami_salary = mysqli_query($conn, "SELECT SUM(jami) FROM `payment` WHERE MONTH(`data`) = MONTH(NOW()) AND YEAR(`data`) = YEAR(NOW())  AND `FIO`='$user_fio' AND `status`!='$deleted'");
        $komissiya_salary = mysqli_query($conn, "SELECT SUM(komissiya) FROM `payment` WHERE MONTH(`data`) = MONTH(NOW()) AND YEAR(`data`) = YEAR(NOW()) AND `FIO`='$user_fio'AND `type`!='$deposit' AND `type`!='$byudjet' AND `status`!='$deleted' ");
        $byudjet1 = mysqli_query($conn, "SELECT SUM(komissiya) FROM `payment` WHERE MONTH(`data`) = MONTH(NOW()) AND YEAR(`data`) = YEAR(NOW()) AND `FIO`='$user_fio' AND `type`='$byudjet' AND `status`!='$deleted' ");
        $deposit1 = mysqli_query($conn, "SELECT SUM(komissiya) FROM `payment` WHERE MONTH(`data`) = MONTH(NOW()) AND YEAR(`data`) = YEAR(NOW()) AND `FIO`='$user_fio' AND `type`='$deposit'AND `status`!='$deleted'  ");
        $credit = mysqli_query($conn, "SELECT SUM(credit) FROM `credit` WHERE `recipient`= '$user_fio' AND `status`!='$deleted' AND `status`!='$waiting'");
        $fine = mysqli_query($conn, "SELECT SUM(summa) FROM `fine`  WHERE MONTH(`date`) = MONTH(NOW()) AND YEAR(`date`) = YEAR(NOW()) AND `cashier`='$user_fio'");
        $debit = mysqli_query($conn, "SELECT SUM(debit) FROM `credit` WHERE `recipient`= '$user_fio' AND `status`!='$deleted' AND `status`!='$waiting'");
        $jami_salary = mysqli_fetch_all($jami_salary);
        $komissiya_salary = mysqli_fetch_all($komissiya_salary);
        $byudjet1 = mysqli_fetch_all($byudjet1);
        $deposit1 = mysqli_fetch_all($deposit1);
        $credit = mysqli_fetch_all($credit);
        $fine = mysqli_fetch_all($fine);
        $debit = mysqli_fetch_all($debit);
        foreach ($jami_salary as $jami_salary) foreach ($komissiya_salary as $komissiya_salary)
            foreach ($byudjet1 as $byudjet1) foreach ($deposit1 as $deposit1) foreach ($credit as $credit) foreach ($debit as $debit)  foreach ($fine as $fine){
        ?>
        <? $byudjet = $byudjet1[0] / 2 / 100 * $salary ?>
        <? $deposit = $deposit1[0] / 2 / 100 * $salary ?>
        <? $salar = $komissiya_salary[0] / 100 * $salary + $deposit + $byudjet - $fine[0]*100/100?>
        <? $profit = $komissiya_salary[0] * 100 / 100 - $salar ?>
        <?php $user_fio ?>
        <? $thiscredit = $credit[0] * 100 / 100 - $debit[0] * 100 / 100; ?>
        <? $fineTotal = $fine[0] * 100 / 100; ?>
<?php
            }
?>