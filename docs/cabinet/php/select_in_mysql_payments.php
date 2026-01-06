<?php
require_once 'config.php';
$user_fio = filter_var(trim($_POST['user_fio']));
$date = date(" Y-m-d ");
$result = mysqli_query($conn, "SELECT * FROM `payment` WHERE `data` = '$date' AND `FIO`='$user_fio'");
$result = mysqli_fetch_all($result);
if ($result != 0) {
    $num = 1;
    foreach ($result as $result) {
?>
        <tr id="<?= $result[0] ?>" class="monitoring" style="cursor: pointer;">
            <td><?= $num++?></td>
            <td style="width: 100px;"><?= $result[2] ?></td>
            <td><?= $result[3] ?></td>
            <td style="width: 190px;"><?= $result[4] ?></td>
            <td><?= $result[5] ?></td>
            <td><?= $result[7] ?></td>
            <td><?= $result[10] ?></td>
            <td><?= $result[11] ?></td>
            <td><?= $result[15] ?></td>
            
        </tr>
<?php
    }
}
?>