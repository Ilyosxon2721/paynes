<div class="print" id="print">
    <div class="content-chek">
        <style>
            @font-face {
                font-family: "VerdanaRegular";
                src: url("../fonts/Verdana.ttf") format("truetype");
                font-style: normal;
                font-weight: normal;
            }

            @font-face {
                font-family: "VerdanaBold";
                src: url("../fonts/Verdana-Bold.ttf") format("truetype");
            }

            @font-face {
                font-family: "TimesRegular";
                src: url("../fonts/timesncyrmt.ttf") format("truetype");
                font-style: normal;
                font-weight: normal;
            }

            h3.small-text {

                font-style: normal;
                font-weight: 500;
                font-size: 13px;
                line-height: 25px;
                color: #868686;
            }

            div#qr {
                margin-top: -1rem;
            }

            h3.title {
                font-size: 1.1rem !important;
                font-weight: 500;
                line-height: 1.75rem;
                color: #1a1a1a;
            }

            div.chek_modal {
                background-color: white;
                width: 64rem;
                height: 32rem;
                margin-left: 26rem;
                top: 15%;
                position: fixed;
            }

            div.content-chek {
                background-color: white;
                border: 1px solid black;
                width: 60rem;
                height: 360px;
                margin-left: 2rem;
            }

            div.content-chek-left {
                float: left;
                text-align: center;
                width: 15rem;
            }

            div.content-chek-right {
                border-left: 1px solid black;
                float: right;
                width: 44rem;
            }

            h4.header-chek {
                text-align: center;
                border-bottom: 1px solid black;
                font-size: 12px;
                margin-top: 0;
                margin-bottom: 0;
            }

            h4.chek-title {
                font-family: "VerdanaBold";
                font-size: 12px;
                margin-top: 2px;
                margin-bottom: 2px;
                padding: 2px;
            }

            div.chek-title-left {
                font-family: "VerdanaBold";
                font-size: 12px;
                margin-top: 2px;
                margin-bottom: 1px;
            }

            span.chek-small-text {
                font-size: 13px;
                font-weight: 300;
                border-bottom: 1px solid black;
                float: right;
                width: 30rem;
                margin-top: 0;
                margin-bottom: 0;
                height: 16px;
                overflow: hidden;

            }

            h4.chek-title-two {
                text-align: center;
                font-size: 16px;
                font-weight: 600;
                margin-top: 20px;
                margin-bottom: 9px;
                font-family: "VerdanaBold";
            }

            p.chek-small-text-two {
                font-size: 11px;
                border-top: 1px solid black;
                margin-top: 0;
                margin-bottom: 0;
            }

            h4.chek-title-three {
                font-size: 12px;
                padding: 2px;
                margin-top: 0;
                margin-bottom: 0;
            }

            span.chek-small-text-three {
                font-size: 12px;
                float: right;
                width: 30rem;
                text-align: center;
                margin-top: 0;
                margin-bottom: 0;
            }

            div.appoint-chek-title {
                border-top: 1px solid black;
                border-bottom: 1px solid black;
                margin-top: 42px;
                margin-bottom: 0;
            }

            h4.chek-title-four {
                font-size: 12px;
                padding: 4.6px;
                margin-left: 27rem;
                border-left: 1px solid black;
                border-bottom: 1px solid black;
                margin-top: 0;
                margin-bottom: 0;
            }

            span.chek-small-text-four {
                font-size: 14px;
                float: right;
                width: 8rem;
                text-align: right;
                border-left: 1px solid black;
                margin-top: 0;
                margin-bottom: 0;
            }
        </style>
        <div class="content-chek-left">
            <h4 class="chek-title-left">Хабарнома (Копия)</h4><br>
            <h4 class="chek-title-left">Anesi Kassa</h4><br>
            <h4 class="chek-title-left" id="type_cash_dubl"></h4><br>
            <h4 class="chek-title-left" id="date_dubl"></h4>
            <h4 class="chek-title-left" id="time_dubl"></h4><br>
            <div class="chek-title-left" id="qr_dubl"></div>
            <!-- <h4 class="chek-title-left" id="sum_dubl" style="font-size: 14px;"></h4><br> -->
            <h4 class="chek-title-left" id="unique_number_dubl"></h4><br>
            <h4 class="chek-title-left" id="cashier_dubl"></h4><br>
            <!-- <h4 class="chek-title-left" style="text-align: left;margin-left:5px;">Оператор имзоси ____________</h4> -->
        </div>
        <div class="content-chek-right">
            <h4 class="header-chek" id="type_pay_dubl"></h4>
            <h4 class="chek-title" id="bank_dubl"></h4>
            <h4 class="chek-title" id="xr_dubl"></h4>
            <h4 class="chek-title" id="mfo_dubl"> <span class="chek-small-text"></span> </h4>
            <h4 class="chek-title" id="inn_dubl"> <span class="chek-small-text"></span> </h4>
            <h4 class="chek-title" id="list_dubl"><span class="chek-small-text"></span> </h4>
            <h4 class="chek-title-two" id="fio_dubl"></h4>
            <h4 class="chek-title-two" id="adres_dubl">
                <p class="chek-small-text-two">(Назначение платежа / Адрес)</p>
            </h4>
            <div class="appoint-chek-title">
                <h4 class="chek-title-three" id="appoint_dubl"> </h4>
            </div>
            <h4 class="chek-title-four" id="sum_dubl_two"> <span class="chek-small-text-four"></span></h4>
            <h4 class="chek-title-four" id="comis_dubl"><span class="chek-small-text-four"></span></h4>
            <h4 class="chek-title-four" id="total_dubl" style="border-bottom:none;"><span class="chek-small-text-four"></span></h4>
        </div>
    </div>
</div>