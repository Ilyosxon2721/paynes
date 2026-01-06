<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <title>Anesi Kassa Авторизация</title>
</head>
<body>
    <div class="wrapper">
        <section class="form login">
            <div class="error-txt" id="error" style="color: #721c24;
    background: #f8d7da; padding:10px; text-align:center; display:none;"></div><br>
            <div id="information" style="color: #721c24;
    background: #f8d7da; padding:10px; text-align:center; display:none;"></div><br>
            <header style="text-align: center;">Войти</header>
            <form>
                <div class="field input">
                    <input type="text" name="input-login" id="input-login" placeholder="Логин" required>
                </div><br>
                <div class="field input">
                    <input type="password" name="input-password" id="input-password" placeholder="Пароль" required>
                    <i class="fas fa-eye"></i>
                </div>
            </form>
            <input type="submit" id="submit-voyti" value="Войти">
        </section>
    </div>
    <script src="/js/auth.js"></script>
</body>
</html>