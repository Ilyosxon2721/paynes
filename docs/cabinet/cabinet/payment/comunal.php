<div class="comunal-pay" id="comunal-pay">
    <div class="comunal-pay-header" id="comunal-pay-header">
        <h4 id="comunal-pay-header-title" class="name"></h4>
        <label for="mib" id="img-label-comunal">
        </label>
        <style>
            div#error_title {
                padding: 15px;
                background-color: #f7dada;
                color: #f92828;
                width: 80%;
                margin-left: 10%;
                border-radius: 5px;
            }

            div#error-comunal {
                display: none;
                padding: 15px;
                background-color: #f7dada;
                color: #f92828;
                width: 80%;
                margin-left: 10%;
                border-radius: 5px;
            }

           
        </style>
    </div>
    <input type="hidden" id="pays_id_comunal" value="">
    <input type="hidden" id="pays_type_comunal" value="">
    <input type="hidden" id="status" value="На проверке">
    <input type="hidden" id="user_fio" value="<?= $user_fio ?>">
    <input type="hidden" id="pays_moliya_comunal" value="">
    <input type="hidden" id="pays_bank_comunal" value="">
    <input type="hidden" id="pays_xr_comunal" value="">
    <input type="hidden" id="pays_mfo_comunal" value="">
    <input type="hidden" id="pays_inn_comunal" value="">
    <input type="hidden" id="pays_shxr_comunal" value="">
    <input type="hidden" id="pays_persent_comunal" value="">
    <input type="hidden" id="pays_comis_comunal" value="">

    <input type="hidden" name="unique_number_chek" id="unique_number_chek" value="<?php
                                                                                    echo substr(md5(time()), 0, 16);
                                                                                    echo $user_id;
                                                                                    ?>">
    <script>
        $(document).ready(function() {
            $("select[name='country']").bind("change", function() {
                $("select[name='city']").empty();
                $.get("payment/countryChek.php", {
                    country: $("select[name='country']").val()
                }, function(data) {
                    data = JSON.parse(data);
                    for (var id in data) {
                        $("select[name='city']").append($("<option value='" + id + "'>" + data[id] + "</option>"));
                    }
                });
            });
        });
    </script>
    <div class="comunal-pay-content" id="comunal-pay-content">
        <div class="error" id="error-comunal"></div>
        <div class="info" id="info-comunal" style=" background-color: #e0f0e5;
    color: #2db979;
    text-align: center;
    width: 80%;
    margin-left: 10%;
    border-radius: 5px;
    padding: 10px; 
    display:none;">Платеж успешно проведен</div>
        <label for="country" style="margin-left:5%;">Регион:</label>
        <select name="country" id="country" style="width: 33.5%; margin-left:0;">
            <option value="1">Ташкент</option>
            <option value="2">Ташкент область</option>
            <option value="3">Андижан</option>
            <option value="4">Бухара</option>
            <option value="5">Самарканд</option>
            <option value="6">Джизак</option>
            <option value="7">Навоий</option>
            <option value="8">Кашкадарья</option>
            <option value="9">Фергана</option>
            <option value="10">Наманган</option>
            <option value="11">Сурхандарья</option>
            <option value="12">Хорезм</option>
            <option value="13">Сырдарья</option>
            <option value="14">Республика Каракалпакстан</option>
        </select>
        <label for="city" style="margin-left:5%;">Город:</label>
        <select name="city" id="city" style="width: 35%; margin-left:0;">.5
            <option value="0" selected="selected"></option>
        </select>
        <input type="text" name="list" id="list_comunal" placeholder="Лицевой счёт" required>
        <input type="text" name="fio" id="fio_comunal" placeholder="ФИО" required>
        <input type="text" name="perpose-of-payment" id="perpose-of-payment" placeholder="Адрес" required>
        <label for="sum" style="margin-left:5%;">Сумма:</label>
        <input type="text" name="sum" id="sum_comunal" placeholder="0.00" style="width: 34%; margin-left:0;" required>
        <input type="hidden" name="sum" id="sum_comunal_num">
        <label for="sum" style="margin-left:5%;">Округление:</label>
        <input type="text" name="sum" id="comission_comunal" placeholder="0.00" style="width: 34%; margin-left:0;" readonly>
        <input type="hidden" name="sum" id="comission_comunal_num">
        <label for="sum" style="margin-left:5%;">Итого:</label>
        <input type="text" name="sum" id="total_comunal" placeholder="0.00" style="width: 34%; margin-left:0;" readonly>
        <input type="hidden" name="sum" id="total_comunal_num">
        <label for="comission" style="margin-left:5%;">Тариф:</label>
        <input type="text" name="comission" id="tarif_comunal" placeholder="0.00" style="width: 34%; margin-left:0;" readonly>
        <label for="total" style="margin-left:5%;">кВт:</label>
        <input type="text" name="total" id="kvt" placeholder="0.00" style="width: 34%; margin-left:3.5%;" readonly>
        <label for="type-pay" style="margin-left:5%;">Тип оплаты:</label>
        <select name="type-pay" id="type_pay_comunal" style="width: 29%; margin-left:0;">
            <option value="Наличный">Наличный</option>
            <option value="Безналичный(uzcard)">Безналичный(uzcard)</option>
            <option value="Безналичный(humo)">Безналичный(humo)</option>
        </select>
        <label for="currency-pay" style="margin-left:5%;">Валюта:</label>
        <select name="currency-pay" id="currency-pay" style="width: 34%; margin-left:0;">.5
            <option value="uzs">Сум</option>
            <option value="usd">АКШ доллари</option>
        </select>

    </div><br>
    <button class="btn-pay-back" id="btn-pay-comunal-back">Назад</button>
    <button class="btn-pay-control" id="btn-pay-control_comunal">Подтвердить</button>
    <button class="btn-pay-load" id="btn-pay-load"><img src="../images/loader.gif" alt="load" width="16" height="16" style="color: white;"> Подтвердить</button>
    <button class="btn-print-chek_comunal" id="btn-chek-print_comunal">Печатать</button>
</div>
<div class="window-chek" id="window-chek" style="display: none;">
    <div class="chek_modal"><br>
        
        <h3 style="margin-left: 15px;">Квитанция</h3>
        <hr><br>

        <?php require_once 'chek_comunal.php'; ?>
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
            <button onClick="printEl('print')" id="print">Печать</button>
        </div>
    </div>

</div>
<script src="/js/comunal_chek.js"></script>
<script src="/js/math_pay_comunal.js"></script>
<script src="/js/confirm_pay_comunal.js"></script>