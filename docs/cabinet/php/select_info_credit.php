<?php 
    require_once 'config.php';
    $credit_id = filter_var(trim($_POST['id']),);

    $result = mysqli_query($conn, "SELECT * FROM `credit` WHERE `id`='$credit_id'");
    $result = mysqli_fetch_all($result);
    if ($result != 0 ) {
        echo json_encode($result);
    }
?>