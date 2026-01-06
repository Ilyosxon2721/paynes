<?php 
    require_once 'config.php';
    $pays_id = filter_var(trim($_POST['id']),);
    $result = mysqli_query($conn, "SELECT * FROM `pays_comunal` WHERE `id`='$pays_id'");
    $result = mysqli_fetch_all($result);
    if ($result != 0 ) {
        echo json_encode($result);
    }
?>