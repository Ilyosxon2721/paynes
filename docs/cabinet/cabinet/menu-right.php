<?php 
      require_once '../php/select_in_mysql_salary.php';
?>
<style>
  @media (max-width: 1440px) {
    /* Menu right */
    div#menu-right {
        position: fixed;
        background-color: #f7f7f7;
        width: 18%;
        height: 93.5%;
        top: 6.5%;
        bottom: 0;
        right: 0;
        border-left: 1px solid #9dacb3;
    }
    h5{
      font-size: 12px;
    }
}
@media (max-height: 800px) {
    /* Menu right */
    div#menu-right {
        position: fixed;
        background-color: #f7f7f7;
        width: 18%;
        height: 93.5%;
        top: 6.5%;
        bottom: 0;
        right: 0;
        border-left: 1px solid #9dacb3;
    }
    h5{
      font-size: 12px;
    }
}
</style>
<form action="" id="cashback-form">
    <h5 id="cashback-title">Кэшбек</h5><br>
    <p id="cashback-summa"><?= round($salar, 2); ?> <span>сум</span></p>
    <br>
    <p id="credit-summa">Штраф <?= round($fineTotal, 2); ?> <span>сум</span></p>
</form>
<form action="" id="credit-form">
    <h5 id="credit-title">Долг</h4><br>
    <!-- <h5 id="credit-title">Оформление кредита остановлено до 01.09.2022г</h4><br> -->
    <p id="credit-summa"><?= round($thiscredit, 2) ?> <span>сум</span></p>
</form><br>
<br>
<div class="table-balans" id="select-balans">
<label for="currency" id="total-balans-title">Общий баланс:</label>
  <select name="currency" id="currency">
    <option value="som">UZS</option>
    <option value="dollar">USD</option>
  </select>
</div><br>
<?php require_once '../php/select_in_mysql_person.php';?>
<div class="total-balans" id="total-balans">
<?php
  $totalSum = $total[0] * 100 / 100 - $buysum[0] * 100 / 100 + $sellsum[0] * 100 / 100 -$credit[0] * 100 / 100 - $incash_sum[0] * 100 / 100 - $incash_uzcard[0] * 100 / 100 - $cashing_uzcard[0] * 100 / 100 - $incash_humo[0] * 100 / 100 - $cashing_humo[0] * 100 / 100 + $outcash_sum[0] * 100/100;
  ?>
  <h2 id="total-balans-summa"> <?=round($totalSum, 2) ?></h2>
    <a href="" id="svg-total-balans-reload"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22 " fill="currentColor" class="bi bi-arrow-repeat" viewBox="0 0 16 16">
  <path d="M11.534 7h3.932a.25.25 0 0 1 .192.41l-1.966 2.36a.25.25 0 0 1-.384 0l-1.966-2.36a.25.25 0 0 1 .192-.41zm-11 2h3.932a.25.25 0 0 0 .192-.41L2.692 6.23a.25.25 0 0 0-.384 0L.342 8.59A.25.25 0 0 0 .534 9z"/>
  <path fill-rule="evenodd" d="M8 3c-1.552 0-2.94.707-3.857 1.818a.5.5 0 1 1-.771-.636A6.002 6.002 0 0 1 13.917 7H12.9A5.002 5.002 0 0 0 8 3zM3.1 9a5.002 5.002 0 0 0 8.757 2.182.5.5 0 1 1 .771.636A6.002 6.002 0 0 1 2.083 9H3.1z"/>
</svg></a>
    <a href="" id="svg-total-balans-hide"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
  <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7.029 7.029 0 0 0 2.79-.588zM5.21 3.088A7.028 7.028 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474L5.21 3.089z"/>
  <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829l-2.83-2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12-.708.708z"/>
</svg></a>
  </div><br><br> 
  <div class="balans" id="balans">
  <form action="" id="cash-form">
  <img src="\images\cash.png" alt="cash" id="cash-logo" width="42" height="42">
    <h5 id="cash-title">Наличный</h5><br>
    <?php 
     $cashSumma = $summa[0] * 100 / 100 - $incash_sum[0] * 100 / 100 - $buysum[0] * 100 / 100 + $sellsum[0] * 100 / 100 - $credit[0] * 100 / 100 + $outcash_sum[0] * 100/100;
    ?>
    <p id="cash-summa"><?= round($cashSumma, 2) ?> <span>сум</span></p>
</form><br>
<form action="" id="uzcard-form">
    <img src="/images/Uzcard-01.png" alt="uzcard" id="uzcard-logo" width="42" height="42">
    <h5 id="uzcard-title">Безналичный (UZCARD)</h5><br>
    <?php 
      $uzcardSumma = $beznal_uzcard[0] * 100 / 100 - $incash_uzcard[0] * 100 / 100 - $cashing_uzcard[0] * 100 / 100;
    ?>
    <p id="uzcard-summa"><?= round($uzcardSumma, 2)?> <span>сум</span></p>
</form><br>
<form action="" id="humo-form">
    <img src="\images\Humo-01.jpg" alt="Humo" id="humo-logo" width="42" height="42">
    <h5 id="humo-title">Безналичный (HUMO)</h5><br>
     <?php 
      $humoSumma = $beznal_humo[0] * 100 / 100 - $incash_humo[0] * 100 / 100 - $cashing_humo[0] * 100 / 100;
    ?>
    <p id="humo-summa"><?= round($humoSumma, 2) ?> <span>сум</span></p>
</form><br>
<?php 
  $rate = mysqli_query($conn, "SELECT * FROM `rate` ORDER BY `id` DESC LIMIT 1");
  $rate = mysqli_fetch_all($rate);
  foreach ($rate as $rate) {
  ?>
<?php
  }
?>
<form action="" id="usd-form">
<img src="\images\dollar.png" alt="dollar" id="humo-logo" width="42" height="42">
    <h5 id="usd-title">USD (АКШ доллари)</h5><br>
    <p id="usd-summa"><?= round($usdend, 2) ?> <span>$</span></p>
</form>
  </div>
<div class="cours" id="cours-table">
    <img src="\images\flag.png" alt="" id="flag" width="29" height="20">
    <p id="cours-title">Покупка:<span id="cours-summa"><?= $rate[1] ?></span></p>
    <p id="cours-title">Продажа:<span  id="cours-summa"><?= $rate[2] ?></span></p>
</div>