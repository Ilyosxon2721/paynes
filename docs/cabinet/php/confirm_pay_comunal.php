<?php
require_once 'config.php';
?>
<?php

$list = filter_var(trim($_POST['list']), );
$random = filter_var(trim($_POST['unique_number_chek']), );
$data = date(" Y-m-d ");
$clock = date(" H:i:s ");
$type = filter_var(trim($_POST['pays_type']), );
$name = filter_var(trim($_POST['fio_comunal']), );
$appoint = filter_var(trim($_POST['appoint']), );
$summa = filter_var(trim($_POST['sum_comunal_num']), );
$komissiya = filter_var(trim($_POST['comission_comunal_num']), );
$jami = filter_var(trim($_POST['total_comunal_num']), );
$tarif = filter_var(trim($_POST['tarif_comunal']), );
$kvt = filter_var(trim($_POST['kvt']), );
$typepay = filter_var(trim($_POST['type_pay_comunal']), );
$val = filter_var(trim($_POST['currency_pay']), );
$status = filter_var(trim($_POST['status']), );
$payid = filter_var(trim($_POST['pays_id_comunal']), );
$FIO = filter_var(trim($_POST['user_fio']), );
$country = filter_var(trim($_POST['country']), );
$city = filter_var(trim($_POST['city']), );

// $summa = str_replace(',','.',$summa);
// $komissiya = str_replace(',','.',$komissiya); 
// $jami = str_replace(',','.',$jami);
 
// echo $summa;
// echo "  ";
// echo $komissiya;
// echo "  ";
// echo $jami;
// echo "  ";


if (mb_strlen($list) < 0 || mb_strlen($list) > 30) {
	echo "Длина исполнительного листа недопустима";
	exit();
} else if (mb_strlen($name) < 1 || mb_strlen($name) > 90) {
	echo "Длина имени недопустима";
	exit();
} else if (mb_strlen($appoint) < 3 || mb_strlen($appoint) > 50) {
	echo "Длина Назначение платежа  недопустима";
	exit();
} else if ($jami <= 0  ) {
	echo "Сумма недопустима";
	exit();
} else if (!$country) {
	echo "Выберите Регион";
	exit();
} else if (!$city) {
	echo "Выберите город";
	exit();
}



$sql = "INSERT INTO payment (list,random,data,clock,type,name,appoint,summa,komissiya,jami,typepay,city,region,valyuta,status,paysid,FIO)
		VALUES
		('$list','$random','$data','$clock','$type','$name','$appoint','$summa','$komissiya','$jami','$typepay','$city','$country','$val','$status','$payid','$FIO')";
if ($conn->query($sql) === TRUE) {
	echo true;
} else {
	echo false .$sql.$conn->error;
}

// .$sql.$conn->error

$conn->close();


?>