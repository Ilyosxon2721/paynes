<?php
require_once '../php/config.php';

require_once '../php/timezone.php';

$user_id = $_SESSION[ 'user_id' ];
if ( $_SESSION[ 'user_id' ] == 0 ) {
    header( 'location: /index.php' );
}
$pos = mysqli_query( $conn, "SELECT * FROM `users` WHERE `id`='$user_id'" );
$pos = mysqli_fetch_all( $pos );
foreach ( $pos as $poss )
?>
<? $position = $poss[ 5 ];
$user_fio = $poss[ 3 ];
?>
<!DOCTYPE html>
<html lang='ru'>

<head>
    <meta charset='UTF-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <link rel='stylesheet' href='/css/home.css'>
    <link rel='stylesheet' href='/css/payment.css'>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'
        integrity='sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=' crossorigin='anonymous'></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <title>Anesi Kassa</title>
</head>
<style>
@media (max-width: 1440px) {

    /* Menu right */
    div#byudjet-pay {
        position: absolute;
        background-color: white;
        width: 50%;
        top: 10%;
        left: 25%;
        border-radius: 8px;
    }

    div#byudjet-pay-content select {
        width: 29%;
        padding: 8px;
        border-radius: 5px;
        border: 1px solid lightgray;
        margin-top: 5%;
        margin-left: 0;
        font-size: 16px;
    }

    .type_pay_byudjet {
        width: 29%;
        margin-left: 0;
    }

    div#comunal-pay {
        position: absolute;
        background-color: white;
        width: 50%;
        top: 10%;
        left: 25%;
        border-radius: 8px;
    }

}
</style>

<body>
    <?php $header_title = 'Оплата';
?>
    <div class='navbar' id='navbar'>
        <?php require_once 'navbar.php';
?>
    </div>
    <hr>
    <div class='menu-left' id='menu-left'>
        <?php require_once 'menu-left.php';
?>
    </div>
    <div class='menu-right' id='menu-right'>
        <?php require_once 'menu-right.php';
?>
    </div>
    <div class='window' id='window'>
        <style>
        p {
            text-align: center;
        }
        </style>
        <div class='payments' id='payments'>
            <br>
            <h3 id='gos-uslugi-title'>Гос услуги</h3><br>
            <div class='type-payments' id='type-payments'>
                <?php
$pays_byudjet = mysqli_query( $conn, "SELECT * FROM `pays_byudjet` WHERE `status`='Active' " );
$pays_byudjet = mysqli_fetch_all( $pays_byudjet );
foreach ( $pays_byudjet as $pay ) {
    ?>
                <button class='byudjet' id="<?= $pay[0] ?>">
                    <img src="\images\<?= $pay[9] ?>" alt='' width='36' height='30'>
                    <p id='mib-title' style='padding-top:10px;'>
                        <?=$pay[ 1 ] ?>
                    </p>
                </button>
                <?php
}
?>
            </div>
            <br>
            <h3 id='kom-uslugi-title'>Коммунальные услуги</h3><br>
            <div class='type-payments' id='type-payments'>
                <?php
$pays_comunal = mysqli_query( $conn, 'SELECT * FROM `pays_comunal`' );
$pays_comunal = mysqli_fetch_all( $pays_comunal );
foreach ( $pays_comunal as $pay ) {
    ?>
                <button class='comunal' id="<?= $pay[0] ?>">
                    <img src="\images\<?= $pay[9] ?>" alt='' width='36' height='30'>
                    <p id='mib-title' style='padding-top:10px; text-align:center;'>
                        <?=$pay[ 1 ] ?>
                    </p>
                </button>
                <?php
}
?>

            </div>
            <br>
            <h3 id='kom-uslugi-title'>Погашение кредита</h3><br>
            <div class='type-payments' id='type-payments'>

                <button class='kapital' id='kapital'>
                    <img src='\images\kapitalbank.jpg' alt='' width='36' height='30'>
                    <p id='kapital-title' style='padding-top:10px;'>Капиталбанк</p>
                </button>
                <button class='mikrokredit' id='mikrokredit'>
                    <img src='\images\mikrokreditbank.png' alt='' width='36' height='30'>
                    <p id='mikrokredit-title' style='font-size:13px; padding-top:5px;'>Микрокредитбанк</p>
                </button>
                <button class='sqb' id='sqb'>
                    <img src='\images\sqb.png' alt='' width='36' height='30'>
                    <p id='sqb-title'>Саноат Курилиш банк</p>
                </button>
                <button class='xalq' id='xalq'>
                    <img src='\images\xalqbanki.png' alt='' width='36' height='30'>
                    <p id='xalq-title' style='padding-top:10px;'>Халк банки</p>
                </button>
                <button class='infin' id='infin'>
                    <img src='\images\infin-logo.png' alt='' width='36' height='30'>
                    <p id='infin-title' style='padding-top:10px;'>Инфин банк</p>
                </button>
                <button class='elmakon' id='elmakon'>
                    <img src='\images\elmakon.png' alt='' width='36' height='30'
                        style='width: 90%; padding:0; margin-left:5%;'>
                    <p id='elmakon-title' style='padding-top:10px;'>Elmakon</p>
                </button>
                <button class='texnomart' id='texnomart'>
                    <img src='\images\texnomart.png' alt='' width='36' height='30'
                        style='width: 90%; padding:0; margin-left:5%;'>
                    <p id='texnomart-title' style='padding-top:10px;'>Texnomart</p>
                </button>
                <button class='Ishonch' id='Ishonch'>
                    <img src='\images\ishonch.png' alt='' width='36' height='30'
                        style='width: 90%; padding:0; margin-left:5%;'>
                    <p id='Ishonch-title' style='padding-top:10px;'>Ishonch</p>
                </button>
            </div>

            <script src='/js/select_type_pays.js'></script>
        </div>
    </div>
    <div class='byudjet-window' id='byudjet-window' style='display: none;'>
        <?php require_once 'payment/byudjet.php';
?>
    </div>
    <div class='comunal-window' id='comunal-window' style='display: none;'>
        <?php require_once 'payment/comunal.php';
?>

    </div>
    <script src='\js\index.js'></script>

</body>

</html>