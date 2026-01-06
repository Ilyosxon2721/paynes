<div class="byudjet-pay" id="byudjet-pay">
    <div class="byudjet-pay-header" id="byudjet-pay-header">
        <h4 id="byudjet-pay-header-title"></h4>
        <label for="mib" id="img-label">
        </label>
    </div>
    <style>
        div#error_title {
            padding: 15px;
            background-color: #f7dada;
            color: #f92828;
            width: 80%;
            margin-left: 10%;
            border-radius: 5px;
        }

        div#error {
            display: none;
            padding: 15px;
            background-color: #f7dada;
            color: #f92828;
            width: 80%;
            margin-left: 10%;
            border-radius: 5px;
        }
    </style>
    <div class="byudjet-pay-content" id="byudjet-pay-content">
        <div class="error" id="error"></div>
        <div class="info" id="info" style=" background-color: #e0f0e5;
    color: #2db979;
    text-align: center;
    width: 80%;
    margin-left: 10%;
    border-radius: 5px;
    padding: 10px; display:none;">Платеж успешно проведен</div>

        <input type="hidden" id="pays_id" value="">
        <input type="hidden" id="pays_type" value="">
        <input type="hidden" id="status" value="На проверке">
        <input type="hidden" id="user_fio" value="<?= $user_fio ?>">
        <input type="hidden" id="pays_moliya" value="">
        <input type="hidden" id="pays_bank" value="">
        <input type="hidden" id="pays_xr" value="">
        <input type="hidden" id="pays_mfo" value="">
        <input type="hidden" id="pays_inn" value="">
        <input type="hidden" id="pays_shxr" value="">
        <input type="hidden" id="pays_persent" value="">
        <input type="hidden" id="pays_comis" value="">
        <input type="hidden" id="pays_sub_persent" value="">
        <input type="hidden" id="pays_sub_comis" value="">

        <input type="hidden" name="unique_number_chek" id="unique_number_chek" value="<?php
                                                                                        echo substr(md5(time()), 0, 16);
                                                                                        echo $user_id;
                                                                                        ?>">
        <input type="text" name="list" id="list" placeholder="Номер документа" required>
        <input type="text" name="fio" id="fio" placeholder="ФИО" required>

        <select name="perpose-of-payment-select" id="appoint-select" style="width: 42%; margin-left:5%;" required>
            <option value="null">Назначение платежа</option>
            <option value="Маъмурий жарима">Маъмурий жарима</option>
            <option value="Давлат божи">Давлат божи</option>
            <option value="Жиноий жарима">Жиноий жарима</option>
            <option value="Жиноят натижасида етказилган зарар">Жиноят натижасида етказилган зарар</option>
            <option value="Маъмурий хукукбузарлик натижасида етказилган зарар">Маъмурий хукукбузарлик натижасида етказилган зарар</option>
            <option value="Карз ундириш">Карз ундириш</option>
            <option value="Солик ва бошка мажбурий туловларни ундириш">Солик ва бошка мажбурий туловларни ундириш</option>
            <option value="Вояга етмаган болалар учун алиментлар ундириш">Вояга етмаган болалар учун алиментлар ундириш</option>
            <option value="Бола таъминоти учун даврий туловлар (3 ёшгача)">Бола таъминоти учун даврий туловлар (3 ёшгача)</option>
            <option value="Почта харажатлари">Почта харажатлари</option>
            <option value="Видеоконференцалока харажатлари">Видеоконференцалока харажатлари</option>
            <option value="Ижро йигими">Ижро йигими</option>
            <option value="Ота-онаси фойдасига нафака (алимент) даврий туловлар">Ота-онаси фойдасига нафака (алимент) даврий туловлар</option>
        </select>
        <input type="text" name="perpose-of-payment" id="appoint" placeholder="Назначение платежа" required>
        <label for="sum" style="margin-left:5%;">Сумма:</label>
        <input class="sum" type="text" name="sum" id="sum" placeholder="0.00" style="width: 34%; margin-left:0;" required>
        <input class="sum" type="hidden" name="sum" id="sum_num">
        <label for="comission" style="margin-left:5%;">Комиссия:</label>
        <input type="text" name="comission" id="comission" placeholder="0.00" style="width: 31%; margin-left:0;" readonly>
        <input type="hidden" name="comission" id="comission_num">
        <label for="total" style="margin-left:5%;">Итого:</label>
        <input type="text" name="total" id="total" placeholder="0.00" style="width: 34%; margin-left:1.5%;" readonly>
        <input type="hidden" name="total" id="total_num">
        <label for="type-pay" style="margin-left:5%;">Тип оплаты:</label>
        <select name="type-pay" id="type_pay" style="width: 29%; margin-left:0;">
            <option value="Наличный">Наличный</option>
            <option value="Безналичный(uzcard)">Безналичный(uzcard)</option>
            <option value="Безналичный(humo)">Безналичный(humo)</option>
        </select>
        <label for="currency-pay" style="margin-left:5%;">Валюта:</label>
        <select name="currency-pay" id="currency_pay" style="width: 34%; margin-left:0;">.5
            <option value="Сум">Сум</option>
            <option value="АКШ доллари">АКШ доллари</option>
        </select>

    </div><br>
    <button class="btn-pay-back" id="btn-pay-back">Назад</button>
    <button class="btn-pay-control" id="btn-pay-control">Подтвердить</button>
    <button class="btn-pay-load" id="btn-pay-load"><img src="../images/loader.gif" alt="load" width="16" height="16" style="color: white;"> Подтвердить</button>
    <button class="btn-print-chek" id="btn-chek-print">Печатать</button>

</div>
<div class="window-chek" id="window-chek" style="display: none;">
    <div class="chek_modal"><br>
        <h3 style="margin-left: 15px;">Квитанция</h3>
        <hr><br>

        <?php require_once 'chek.php'; ?>
        <br>
        <hr>
        <br>
        <style>
            @media (max-width: 1440px) {
                div.chek_modal {
                    background-color: white;
                    width: 72%;
                    height: 30rem;
                    margin-left: 14%;
                    top: 15%;
                    position: fixed;
                }
            }
        </style>
        <div class="chek-footer" style="float: right; margin-right:2.5rem">
            <button onclick="closeAll()" id="cancel-chek">Отмена</button>
            <button onClick="printEl('print_byudjet')" id="print_byudjet">Печать</button>
        </div>
    </div>

</div>
<!-- <script src="/js/math_pay.js"></script> -->
<script src="/js/confirm_pay.js"></script>
<script src="/js/math_pay.js"></script>