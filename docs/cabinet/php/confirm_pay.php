<?php
 require_once 'config.php';
?>
<?php
 
	$list = filter_var(trim($_POST['list']),);
	$random = filter_var(trim($_POST['unique_number_chek']),);
	$data = date(" Y-m-d ");
	$clock = date(" H:i:s ");
	$type = filter_var(trim($_POST['pays_type']),);
	$name = filter_var(trim($_POST['fio']),);
	$appoint = filter_var(trim($_POST['appoint']),);
	$summa = filter_var(trim($_POST['sum_num']),);
	$komissiya = filter_var(trim($_POST['comission_num']),);
	$jami = filter_var(trim($_POST['total_num']),);
	$typepay = filter_var(trim($_POST['type_pay']),);
	$val = filter_var(trim($_POST['currency_pay']),);
	$status = filter_var(trim($_POST['status']),);
	$payid = filter_var(trim($_POST['pays_id']),);
	$FIO = filter_var(trim($_POST['user_fio']),);
	// $summa = str_replace(',','.',$summa);
	// $komissiya = str_replace(',','.',$komissiya);
	// $jami = str_replace(',','.',$jami);
	// echo $summa;
	// echo "  ";
	// echo $komissiya;
	// echo "  ";
	// echo $jami;
	// echo "  ";


	
	if (mb_strlen( $list ) < 0 || mb_strlen( $list ) > 30 ) {
		echo "Длина исполнительного листа недопустима";
		exit();
		} 
		else if (mb_strlen($name) < 1 || mb_strlen($name) > 70 ) {
		echo "Длина имени недопустима (мах 70)";
		exit(); 
		}
		else if (mb_strlen( $appoint ) < 3 || mb_strlen( $appoint ) > 150 ) {
		echo "Длина Назначение платежа  недопустима (мах 70)";
		exit();
		}else if (mb_strlen( $jami ) <= 0 || mb_strlen( $jami ) > 20 ) {
		echo "Сумма недопустима";
		exit();
		}
	

	

		
		$sql = "INSERT INTO payment (list,random,data,clock,type,name,appoint,summa,komissiya,jami,typepay,valyuta,status,paysid,FIO)
		VALUES
		('$list','$random','$data','$clock','$type','$name','$appoint','$summa','$komissiya','$jami','$typepay','$val','$status','$payid','$FIO')";
		if ($conn->query($sql) === TRUE) {
			echo true;	
		} else {
			echo false;
		}

        // .$sql.$conn->error
  	
		$conn->close();

 
		?>