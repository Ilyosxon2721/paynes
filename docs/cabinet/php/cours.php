<?php
require_once 'config.php';
?>
<?php
        $cours = mysqli_query($conn, "SELECT * FROM `rate` ORDER BY id DESC LIMIT 1");
        $cours = mysqli_fetch_all($cours);
        echo json_encode($cours);
// .$sql.$conn->error

$conn->close();


?>