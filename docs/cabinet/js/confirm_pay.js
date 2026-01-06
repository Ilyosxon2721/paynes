function funcBefore() {
    $("#information").text("Ожидание данных...");
}

function mibConfirm() {
    var list = $("#list").val();
    var random = $("#unique_number_chek").val();
    var date = $("#data").val();
    var type = $("#pays_type").val();
    var clock = $("#clock").val();
    var name = $("#fio").val();

    var appoint = $("#appoint-select").val();

    var summa = $("#sum_num").val();
    var komissiya = $("#comission_num").val();
    var jaminaz = $("#total_num").val();
    var typepay = $("#type_pay").val();
    var valyuta = $("#currency_pay").val();
    var status = $("#status").val();
    var paysid = $("#pays_id").val();
    var oper = $("#user_fio").val();
    $('#btn-pay-control').hide();
    $('#btn-pay-load').show();


    /* Act on the event */
    $.ajax({
        url: '/php/confirm_pay.php',
        type: 'post',
        data: {
            list: list,
            unique_number_chek: random,
            data: date,
            pays_type: type,
            clock: clock,
            fio: name,
            appoint: appoint,
            sum_num: summa,
            comission_num: komissiya,
            total_num: jaminaz,
            type_pay: typepay,
            currency_pay: valyuta,
            status: status,
            pays_id: paysid,
            user_fio: oper
        },
        dataType: "html",
        beforeSend: funcBefore,
        success: function (data) {
            console.log(data);
            if (data == true) {
                $('#btn-pay-control').hide();
                $('#btn-pay-load').hide();
                $('#info').show();
                $('#btn-chek-print').show();

            } else if (data == false) {
                $('#btn-pay-load').hide();
                $('#btn-pay-control').show();
                // console.log(data);
                $("#error").html("<div class='bg-danger text-white' id='error_title' style='padding:15px;'>Ошибка сервера! Проверьте интернет соединение и статус платежа если платеж не проведен повторите попытку</div>");
                $('#error').show();
            } else {
                $('#btn-pay-load').hide();
                $('#btn-pay-control').show();
                $('#error').text(data);
                $('#error').show();

            }
        }

    });
}
function otherConfirm() {
    var list = $("#list").val();
    var random = $("#unique_number_chek").val();
    var date = $("#data").val();
    var type = $("#pays_type").val();
    var clock = $("#clock").val();
    var name = $("#fio").val();
    var appoint = $("#appoint").val();
    var summa = $("#sum_num").val();
    var komissiya = $("#comission_num").val();
    var jaminaz = $("#total_num").val();
    var typepay = $("#type_pay").val();
    var valyuta = $("#currency_pay").val();
    var status = $("#status").val();
    var paysid = $("#pays_id").val();
    var oper = $("#user_fio").val();
    $('#btn-pay-control').hide();
    $('#btn-pay-load').show();


    /* Act on the event */
    $.ajax({
        url: '/php/confirm_pay.php',
        type: 'post',
        data: {
            list: list,
            unique_number_chek: random,
            data: date,
            pays_type: type,
            clock: clock,
            fio: name,
            appoint: appoint,
            sum_num: summa,
            comission_num: komissiya,
            total_num: jaminaz,
            type_pay: typepay,
            currency_pay: valyuta,
            status: status,
            pays_id: paysid,
            user_fio: oper
        },
        dataType: "html",
        beforeSend: funcBefore,
        success: function (data) {
            console.log(data);
            if (data == true) {
                $('#btn-pay-control').hide();
                $('#btn-pay-load').hide();
                $('#info').show();
                $('#btn-chek-print').show();

            } else if (data == false) {
                $('#btn-pay-load').hide();
                $('#btn-pay-control').show();
                // console.log(data);
                $("#error").html("<div class='bg-danger text-white' id='error_title' style='padding:15px;'>Ошибка сервера! Проверьте интернет соединение и статус платежа если платеж не проведен повторите попытку</div>");
                $('#error').show();
            } else {
                $('#btn-pay-load').hide();
                $('#btn-pay-control').show();
                $('#error').text(data);
                $('#error').show();

            }
        }

    });
}

$(document).ready(function () {
    $("#btn-pay-control").bind('click', function () {
        var global = window.Storage.globalVar;
        console.log(global);
        if (global == "МИБ") {
            mibConfirm();
        } else {
            otherConfirm();
        }
        // if (global == "МИБ") {
        //     var list = $("#list").val();
        //     var random = $("#unique_number_chek").val();
        //     var date = $("#data").val();
        //     var type = $("#pays_type").val();
        //     var clock = $("#clock").val();
        //     var name = $("#fio").val();

        //     var appoint = $("#appoint-select").val();

        //     var summa = $("#sum_num").val();
        //     var komissiya = $("#comission_num").val();
        //     var jaminaz = $("#total_num").val();
        //     var typepay = $("#type_pay").val();
        //     var valyuta = $("#currency_pay").val();
        //     var status = $("#status").val();
        //     var paysid = $("#pays_id").val();
        //     var oper = $("#user_fio").val();
        //     $('#btn-pay-control').hide();
        //     $('#btn-pay-load').show();


        //     /* Act on the event */
        //     $.ajax({
        //         url: '/php/confirm_pay.php',
        //         type: 'post',
        //         data: {
        //             list: list,
        //             unique_number_chek: random,
        //             data: date,
        //             pays_type: type,
        //             clock: clock,
        //             fio: name,
        //             appoint: appoint,
        //             sum_num: summa,
        //             comission_num: komissiya,
        //             total_num: jaminaz,
        //             type_pay: typepay,
        //             currency_pay: valyuta,
        //             status: status,
        //             pays_id: paysid,
        //             user_fio: oper
        //         },
        //         dataType: "html",
        //         beforeSend: funcBefore,
        //         success: function (data) {
        //             console.log(data);
        //             if (data == true) {
        //                 $('#btn-pay-control').hide();
        //                 $('#btn-pay-load').hide();
        //                 $('#info').show();
        //                 $('#btn-chek-print').show();

        //             } else if (data == false) {
        //                 $('#btn-pay-load').hide();
        //                 $('#btn-pay-control').show();
        //                 // console.log(data);
        //                 $("#error").html("<div class='bg-danger text-white' id='error_title' style='padding:15px;'>Ошибка сервера! Проверьте интернет соединение и статус платежа если платеж не проведен повторите попытку</div>");
        //                 $('#error').show();
        //             } else {
        //                 $('#btn-pay-load').hide();
        //                 $('#btn-pay-control').show();
        //                 $('#error').text(data);
        //                 $('#error').show();

        //             }
        //         }

        //     });
        // } else {
        //     var list = $("#list").val();
        //     var random = $("#unique_number_chek").val();
        //     var date = $("#data").val();
        //     var type = $("#pays_type").val();
        //     var clock = $("#clock").val();
        //     var name = $("#fio").val();
        //     var appoint = $("#appoint").val();
        //     var summa = $("#sum_num").val();
        //     var komissiya = $("#comission_num").val();
        //     var jaminaz = $("#total_num").val();
        //     var typepay = $("#type_pay").val();
        //     var valyuta = $("#currency_pay").val();
        //     var status = $("#status").val();
        //     var paysid = $("#pays_id").val();
        //     var oper = $("#user_fio").val();
        //     $('#btn-pay-control').hide();
        //     $('#btn-pay-load').show();


        //     /* Act on the event */
        //     $.ajax({
        //         url: '/php/confirm_pay.php',
        //         type: 'post',
        //         data: {
        //             list: list,
        //             unique_number_chek: random,
        //             data: date,
        //             pays_type: type,
        //             clock: clock,
        //             fio: name,
        //             appoint: appoint,
        //             sum_num: summa,
        //             comission_num: komissiya,
        //             total_num: jaminaz,
        //             type_pay: typepay,
        //             currency_pay: valyuta,
        //             status: status,
        //             pays_id: paysid,
        //             user_fio: oper
        //         },
        //         dataType: "html",
        //         beforeSend: funcBefore,
        //         success: function (data) {
        //             console.log(data);
        //             if (data == true) {
        //                 $('#btn-pay-control').hide();
        //                 $('#btn-pay-load').hide();
        //                 $('#info').show();
        //                 $('#btn-chek-print').show();

        //             } else if (data == false) {
        //                 $('#btn-pay-load').hide();
        //                 $('#btn-pay-control').show();
        //                 // console.log(data);
        //                 $("#error").html("<div class='bg-danger text-white' id='error_title' style='padding:15px;'>Ошибка сервера! Проверьте интернет соединение и статус платежа если платеж не проведен повторите попытку</div>");
        //                 $('#error').show();
        //             } else {
        //                 $('#btn-pay-load').hide();
        //                 $('#btn-pay-control').show();
        //                 $('#error').text(data);
        //                 $('#error').show();

        //             }
        //         }

        //     });
        // }

    });

});