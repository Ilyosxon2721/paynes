<?php 
    require_once 'config.php';
    $pay_id = filter_var(trim($_POST['unique_number_chek']),);
    $pays_id = filter_var(trim($_POST['pays_id']),);
    echo $pay_id;    
    echo $pays_id;    
    // $result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `random`='$pay_id'");
    // $pays = mysqli_query($conn, "SELECT * FROM `pays_byudjet` WHERE `id`='$pays_id'");
    // $pay = mysqli_fetch_all($result);
    // $pays = mysqli_fetch_all($pays);
    // var_dump($pay);
    // var_dump($pays);
    // echo $pay_id;
    // if ($pay_id != 0 ) {
    //     $result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `random`='$pay_id'");
    // }
?>