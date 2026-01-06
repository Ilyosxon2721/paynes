function funcBefore() {
    $("#information").text("Ожидание данных...");
}


$(document).ready(function() {
    $("#btn-pay-control_comunal").bind('click', function() {
        var list = $("#list_comunal").val();
        var random = $("#unique_number_chek").val();
        var date = $("#data").val();
        var type = $("#pays_type_comunal").val();
        var clock = $("#clock").val();
        var name = $("#fio_comunal").val();
        var appoint = $("#perpose-of-payment").val();
        var summa = $("#sum_comunal_num").val();
        var komissiya = $("#comission_comunal_num").val();
        var jaminaz = $("#total_comunal_num").val();
        var tarif = $("#tarif_comunal").val();
        var kvt = $("#kvt").val();
        var typepay = $("#type_pay_comunal").val();
        var valyuta = $("#currency_pay").val();
        var status = $("#status").val();
        var paysid = $("#pays_id_comunal").val();
        var oper = $("#user_fio").val();
        var country = $("#country").val();
        var city = $("#city").val();
        $('#btn-pay-control_comunal').hide();
        $('#btn-pay-load').show();
        /* Act on the event */
        $.ajax({
            url: '/php/confirm_pay_comunal.php',
            type: 'post',
            data: {
                list: list,
                unique_number_chek: random,
                data: date,
                pays_type: type,
                clock: clock,
                fio_comunal: name,
                appoint: appoint,
                sum_comunal_num: summa,
                comission_comunal_num: komissiya,
                total_comunal_num: jaminaz,
                tarif_comunal: tarif,
                kvt: kvt,
                country: country,
                city: city,
                type_pay_comunal: typepay,
                currency_pay: valyuta,
                status: status,
                pays_id_comunal: paysid,
                user_fio: oper
            },
            dataType: "html",
            beforeSend: funcBefore,
            success: function(data) {
                console.log(data);
                if (data == true) {
                    $('#btn-pay-load').hide();
                    $('#btn-pay-control_comunal').hide();
                    $('#info-comunal').show();
                    $('#btn-chek-print_comunal').show();

                } else if (data == false) {
                    $('#btn-pay-load').hide();
                    $('#btn-pay-control_comunal').show()
                        // console.log(data);
                    $("#error-comunal").html("<div class='bg-danger text-white' id='error_title' style='padding:15px;'>Ошибка сервера! Проверьте интернет соединение и статус платежа если платеж не проведен повторите попытку</div>");
                    $('#error-comunal').show();
                } else {
                    $('#btn-pay-load').hide();
                    $('#btn-pay-control_comunal').show()
                    $('#error-comunal').text(data);
                    $('#error-comunal').show();

                }
            }

        });
    });

});