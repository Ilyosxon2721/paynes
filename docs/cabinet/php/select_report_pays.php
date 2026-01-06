<?php
require_once 'config.php';
$date = filter_var(trim($_POST['date']), );
$dateTo = filter_var(trim($_POST['dateto']), );
$type_pay = filter_var(trim($_POST['type_pay']), );
$type_cash = filter_var(trim($_POST['type_cash']), );
$cashier = filter_var(trim($_POST['cashier']), );
// echo $date;
// echo $dateTo;
// echo $type_pay;
// echo $type_cash;
// echo $cashier;
$all = 'Все';
// if (!$date && !$dateTo) {
//     echo 'Дата указано неверно';
// }
if ($type_pay == $all && $type_cash != $all) {
    $reports = mysqli_query($conn, "SELECT * FROM `payment` WHERE `data`BETWEEN '$date' AND '$dateTo' AND `valyuta`='$type_cash' AND `FIO`='$cashier'");
    $reports = mysqli_fetch_all($reports);
    echo json_encode($reports);
    exit();
}
if ($type_cash != $all && $type_cash == $all) {
    $reports = mysqli_query($conn, "SELECT * FROM `payment` WHERE `data`BETWEEN '$date' AND '$dateTo' AND `typepay`= '$type_pay' AND `FIO`='$cashier'");
    $reports = mysqli_fetch_all($reports);
    echo json_encode($reports);
    exit();
}
if ($type_cash == $all && $type_pay == $all ) {
    $reports = mysqli_query($conn, "SELECT * FROM `payment` WHERE `data`BETWEEN '$date' AND '$dateTo' AND `FIO`='$cashier'");
    $reports = mysqli_fetch_all($reports);
    echo json_encode($reports);
    exit();
} else
    $reports = mysqli_query($conn, "SELECT * FROM `payment` WHERE `data`BETWEEN '$date' AND '$dateTo' AND `valyuta`='$type_cash' AND `typepay`= '$type_pay' AND `FIO`='$cashier'");
    $reports = mysqli_fetch_all($reports);
    echo json_encode($reports);
    exit();
    // $result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `id`='$pays_id'");
    // $type_pay = mysqli_query($conn, "SELECT * FROM `pays` WHERE `type`='$type'");
    // $result = mysqli_fetch_all($result);
    // $type_pay = mysqli_fetch_all($type_pay);
    // if ($result != 0 ) {
    //     $result = array(array($result), array($type_pay));
    //     echo json_encode($result);
    // }
