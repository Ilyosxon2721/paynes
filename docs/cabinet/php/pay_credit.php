<?php 
    require_once 'config.php';
    $credit_id = filter_var(trim($_POST['id_credit']),);
    $credit_fio = filter_var(trim($_POST['credit_fio_cashier']),);
    $credit_summa = filter_var(trim($_POST['summa_credit']),);
    $remainder = filter_var(trim($_POST['remainder']),);
    $name_pay = filter_var(trim($_POST['name_pay']),);
    $type_pay = filter_var(trim($_POST['type_pay']),);
    $xr_credit = filter_var(trim($_POST['xr_credit']),);
    $unique = filter_var(trim($_POST['unique']),);
    $last_debit = filter_var(trim($_POST['last_debit']),);
    $date = date(" Y-m-d ");
    $time = date(" H:i:s ");
    $status = 'Проведен';
    $val = 'Сум';
    $thisdebit = $last_debit + $credit_summa;
    $zero = 0;
    if (!$credit_summa) {
        echo "Введите сумму";
        exit();
    }
    if ($remainder <= 0) {
        echo "Ошибка! кредит погашен";
        exit();
    }
     $result_remainder = $remainder-$credit_summa;
    if ($result_remainder < 0) {
       echo "Ошибка! остаток сумма кредита некорректно".$result_remainder;
       exit();
    }
    $credit = mysqli_query($conn, "UPDATE `credit` SET `debit` = '$thisdebit' WHERE `credit`.`id` = '$credit_id'");

    $sql = "INSERT INTO payment (list,random,data,clock,type,name,appoint,summa,komissiya,jami,typepay,valyuta,status,paysid,FIO)
		VALUES
		('$xr_credit','$unique','$date','$time','$name_pay','$credit_fio','$name_pay','$credit_summa','$zero','$credit_summa','$type_pay','$val','$status','$zero','$credit_fio')";
		if ($conn->query($sql) === TRUE) {
			echo true;
		} else {
			echo false;
		}
        // .$sql.$conn->error
		$conn->close();
?>