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
    $branch = $poss[9];
?>
<?php
$summa = filter_var(trim($_POST['inquiry_input_summa']), );
$user = filter_var(trim($_POST['inquiry_input_user']), );
$date = date(" Y-m-d ");
$time = date(" H:i:s ");
$clock = date ("His");
$data = date ("dmy");
$debit = '0';
$status = "На проверке";
$first = "29801000";
$end ="001";
$xr_inquiry = $first.$user_id.$data.$clock.$end;
// echo $xr_inquiry;


if ($summa <= 0) {
	echo "Пожалуйста введите сумму";
	exit();
}
if ($summa < 1000) {
    echo "Минимальная сумма кредита 1 000.00 сум";
	exit();
}
if ($summa > 1000000) {
    echo "Максимальная сумма кредита 1 000 000.00 сум";
	exit();
}
$sql = "INSERT INTO `credit`(`date`, `clock`, `recipient`, `xr`, `branch`, `debit`, `credit`, `confirmed`, `status`) 
VALUES ('$date','$time','$user','$xr_inquiry','$branch','$debit','$summa','','$status')";
if ($conn->query($sql) === TRUE) {
	echo true;
} else {
	echo false.$sql.$conn->error;
}

// .$sql.$conn->error;
$conn->close();
  