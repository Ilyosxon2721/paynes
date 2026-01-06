<?php 
    require_once 'config.php';
    $pays_id = filter_var(trim($_POST['id']),);
    $type = filter_var(trim($_POST['type']),);
    $result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `id`='$pays_id'");
    $type_pay = mysqli_query($conn, "SELECT * FROM `pays` WHERE `type`='$type'");
    $result = mysqli_fetch_all($result);
    $type_pay = mysqli_fetch_all($type_pay);
    if ($result != 0 ) {
        $result = array(array($result), array($type_pay));
        echo json_encode($result);
    }

?>