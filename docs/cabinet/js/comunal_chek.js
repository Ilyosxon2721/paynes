$(document).ready(function() {
    $(".btn-print-chek_comunal").on('click', function() {
        // $('#chek_comunal').show();
        var unique_number = $("#unique_number_chek").val();
        var type_pay = $("#pays_type_comunal").val();
        var kvt = $("#kvt").val();

        $.ajax({
            url: '\\php\\select_chek_pays.php',
            type: 'POST',
            data: {
                unique_number: unique_number,
                type_pay: type_pay
            },
            dataType: "html",
            beforeSend: funcBefore,
            success: function(data) {
                var result = $.parseJSON(data);
                if (data) {
                    $('.window-chek').show();
                    var sum = result[0][0][0][8];
                    var comis = result[0][0][0][9];
                    var total = result[0][0][0][10];
                    let sum_num = Number(sum);
                    let comis_num = Number(comis);
                    let total_num = Number(total);
                    var sum_dec = sum_num.toLocaleString('ru-RU', {
                        style: "decimal",
                        minimumFractionDigits: 2
                    });
                    var comis_dec = comis_num.toLocaleString('ru-RU', {
                        style: "decimal",
                        minimumFractionDigits: 2
                    });
                    var total_sum_dec = total_num.toLocaleString('ru-RU', {
                        style: "decimal",
                        minimumFractionDigits: 2
                    });
                    if (result[0][0][0][4] == 'Электроэнергия' || result[0][0][0][4] == 'Электроэнергия(пеня)') {
                        var kvt_name = 'кВт';
                    }
                    if (result[0][0][0][4] == 'Газ' || result[0][0][0][4] == 'Газ(пеня)') {
                        var kvt_name = 'm<sup>3</sup>';
                    }
                    document.getElementById("type_cash_pay_comunal").innerHTML = `Тулов тури: <span style="font-family: 'VerdanaBold';">` + result[0][0][0][11] + `</span>`;
                    document.getElementById("date_pay_comunal").innerHTML = result[0][0][0][2];
                    document.getElementById("time_pay_comunal").innerHTML = result[0][0][0][3];
                    document.getElementById("unique_number_pay_comunal").innerHTML = `№` + result[0][0][0][1];
                    document.getElementById("sum_pay_comunal").innerHTML = total_sum_dec.replace(/,/, '.') + ` сум`;
                    document.getElementById("sum_two_pay_comunal").innerHTML = `Сумма платежа <span class="chek-small-text-four">` + sum_dec.replace(/,/, '.') + `</span>`;
                    document.getElementById("comis_pay_comunal").innerHTML = `Комиссия <span class="chek-small-text-four">` + comis_dec.replace(/,/, '.') + `</span>`;
                    document.getElementById("total_pay_comunal").innerHTML = `Итого <span class="chek-small-text-four">` + total_sum_dec.replace(/,/, '.') + `</span>`;
                    document.getElementById("cashier_pay_comunal").innerHTML = result[0][0][0][17];
                    document.getElementById("type_pays_pay_comunal").innerHTML = result[1][0][0][2];
                    document.getElementById("bank_pay_comunal").innerHTML = `Банк муассаси <span class="chek-small-text">` + result[1][0][0][3] + `</span>`;
                    document.getElementById("xr_pay_comunal").innerHTML = `Хисоб ракам  <span class="chek-small-text">` + result[1][0][0][4] + `</span>`;
                    document.getElementById("mfo_pay_comunal").innerHTML = `МФО  <span class="chek-small-text">` + result[1][0][0][5] + `</span>`;
                    document.getElementById("inn_pay_comunal").innerHTML = `ИНН  <span class="chek-small-text">` + result[1][0][0][6] + `</span>`;
                    document.getElementById("list_pay_comunal").innerHTML = result[1][0][0][8] + ` <span class="chek-small-text">` + result[0][0][0][5] + `</span>`;
                    document.getElementById("fio_pay_comunal").innerHTML = result[0][0][0][6] + ` <p class="chek-small-text-two">(фамилияси, исми ва отасининг исми / ФИО)</p>`;
                    document.getElementById("adres_pay_comunal").innerHTML = result[0][0][0][7] + ` <p class="chek-small-text-two">(Назначение платежа / Адрес)</p>`;
                    document.getElementById("appoint_pay_comunal").innerHTML = `Тулов максади <span class="chek-small-text-three">` + result[0][0][0][4] + `</span>`;
                    document.getElementById("tarif_pay_comunal").innerHTML = `Тариф: <span style="margin-right:55%;">1 ` + kvt_name + ` = ` + result[1][0][0][12] + ` сум </span> Оплачено за:<span >` + kvt + kvt_name + `</span>`;
                }
                if (data == '') {
                    console.log(data);
                }

            }
        });
    })

});

function printEl(print) {
    var printContents = document.getElementById(print).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    window.location.href = '\\cabinet\\payment.php';
    // $('#exampleModal').show();
}

function closeAll() {
    window.location.href = '\\cabinet\\payment.php';
}