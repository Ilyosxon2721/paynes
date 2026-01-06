<?php 
    require_once 'config.php';
    $unique_number = filter_var(trim($_POST['unique_number']),);
    $type = filter_var(trim($_POST['type_pay']));
    $result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `random`='$unique_number   '");
    $type_pay = mysqli_query($conn, "SELECT * FROM `pays` WHERE `type`='$type'");
    $result = mysqli_fetch_all($result);
    $type_pay = mysqli_fetch_all($type_pay);
    if ($result != 0 ) {
        $result = array(array($result), array($type_pay));
        echo json_encode($result);
    }

?>