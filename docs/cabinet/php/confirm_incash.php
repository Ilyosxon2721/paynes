<?php
 require_once 'config.php';
?>
<?php
 
	$type_cash = filter_var(trim($_POST['type_cash']),);
	$cashier = filter_var(trim($_POST['cashier']),);
	$summa = filter_var(trim($_POST['summa']),);
	$type_operation = filter_var(trim($_POST['type_operation']),);
    $date = date(" Y-m-d ");
    $time = date(" H:i:s ");
	$status = 'Проведен';
	if ( $summa  <= 0 ) {
		echo "Введите сумму";
		exit();
		} 
		$sql = "INSERT INTO incash (date,time,xisobraqam,sum,fio,type,status)
		VALUES('$date','$time','$type_cash','$summa','$cashier','$type_operation','$status')";
		if ($conn->query($sql) ===TRUE) {
			echo true;
		} else {
			echo false.$sql.$conn->error;
		}

        // .$sql.$conn->error
  	
		$conn->close();

 
		?>