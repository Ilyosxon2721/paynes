<?php
	require_once "config.php";
	$login = filter_var(trim($_POST['login']),);
	$password = filter_var(trim($_POST['password']),);
    if ($login == '' && $password == '') {
        echo 'Заполните все поля';
    }else{
        $pass = md5($password."bankir");
        $close= 'Закрыт';
        $zero = 0;
        $result = mysqli_query($conn, "SELECT * FROM `users` WHERE `login`='$login'  AND `pass` ='$pass'");
        $result = mysqli_fetch_all($result);
        foreach ($result as $result) {
            $user_id = $result['0'];
            $user_login = $result['1'];
            $user_name = $result['3'];
            $user_status = $result['10'];
        }
        if ($user_id == 0) {
            echo "Логин или пароль неверно";
        }
        elseif ($user_status == $close) {
            echo "Ваш аккаунт заблокирован пожалуйста обратитесь к Администратору";
        }
        elseif ($user_id != 0 && $user_status != $close) {
            $_SESSION['user_id'] = $user_id;
            // setcookie('user_id',$user_id, time() + 600000, "/");
            echo true;
        }
    }
	
?>