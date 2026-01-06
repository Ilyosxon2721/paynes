const sum = document.getElementById('sum'),
    percent = document.getElementById('pays_persent'),
    comis = document.getElementById('pays_comis'),
    subPercent = document.getElementById('pays_sub_persent'),
    subComis = document.getElementById('pays_sub_comis'),
    appointment = document.getElementById('appoint'),
    appointmentSelect = document.getElementById('appoint-select'),
    payType = document.getElementById("pays_type"),
    summa_num = document.getElementById('sum_num');
title = document.getElementById("mib-title");
$(document).ready(function () {
    $(".byudjet").on("click", function () {
        setTimeout(() => {
            var global = window.Storage.globalVar;
            console.log(global);
            if (global == "" || global == null) {
                setTimeout(() => {
                    var global = window.Storage.globalVar;
                    console.log(global);
                    if (global == "" || global == null) { }
                    if (global == "МИБ") {
                        mibMath();
                    } else {
                        budgetMath();
                    }
                }, 1000);
            } else {
                if (global == "МИБ") {
                    mibMath();
                } else {
                    budgetMath();
                }
            }

        }, 1000);

    });

})
function mibMath() {
    const appoints = new Map([
        ["Маъмурий жарима", //Маъмурий жарима
            [
                "Маъмурий жарима",
                0.5, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                0.5, //процент комиссии 2 - го этапа 
                0, //сумма комиссии 2 - го этапа 
                0, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Давлат божи", //Давлат божи
            [
                "Давлат божи",
                0.5, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                0.5, //процент комиссии 2 - го этапа 
                0, //сумма комиссии 2 - го этапа 
                0, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Жиноий жарима", //Жиноий жарима
            [
                "Жиноий жарима",
                0.5, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                0.5, //процент комиссии 2 - го этапа 
                0, //сумма комиссии 2 - го этапа 
                0, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Жиноят натижасида етказилган зарар", //Жиноят натижасида етказилган зарар
            [
                "Жиноят натижасида етказилган зарар",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Маъмурий хукукбузарлик натижасида етказилган зарар", // Маъмурий хукукбузарлик натижасида етказилган зарар
            [
                "Маъмурий хукукбузарлик натижасида етказилган зарар",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Карз ундириш", // Карз ундириш
            [
                "Карз ундириш",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Солик ва бошка мажбурий туловларни ундириш", //Солик ва бошка мажбурий туловларни ундириш
            [
                "Солик ва бошка мажбурий туловларни ундириш",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]],
        ["Вояга етмаган болалар учун алиментлар ундириш", //Вояга етмаган болалар учун алиментлар ундириш
            [
                "Вояга етмаган болалар учун алиментлар ундириш",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]
        ],
        ["Бола таъминоти учун даврий туловлар (3 ёшгача)", //Бола таъминоти учун даврий туловлар (3 ёшгача)
            [
                "Бола таъминоти учун даврий туловлар (3 ёшгача)",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]
        ],
        ["Почта харажатлари", //Почта харажатлари
            [
                "Почта харажатлари",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]
        ],
        ["Видеоконференцалока харажатлари", //Вояга етмаган болалар учун алиментлар ундириш
            [
                "Видеоконференцалока харажатлари",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]
        ],
        ["Ижро йигими", //Ижро йигими
            [
                "Вояга етмаган болалар учун алиментлар ундириш",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]
        ],
        ["Ота-онаси фойдасига нафака (алимент) даврий туловлар", //Вояга етмаган болалар учун алиментлар ундириш
            [
                "Ота-онаси фойдасига нафака (алимент) даврий туловлар",
                2, //процент комиссии до макс суммы 1 - го этапа 
                0, //сумма комиссии до макс суммы 1 - го этапа 
                1, //процент комиссии 2 - го этапа 
                20000, //сумма комиссии 2 - го этапа 
                2000000, //макс сумма 1 - го этапа
                0, // макс сумма платежа
            ]
        ],

    ]);
    document.getElementById("sum").addEventListener("change", math);

    document.getElementById("appoint-select").addEventListener("change", appointIsChanged);
    function appointIsChanged() {
        if (appointmentSelect.value != "null") {
            var list = appoints.get(appointmentSelect.value);
            if (summa_num.value != '' || summa_num.value != 0) {

                var Summa = summa_num.value;
                let Summa_num = Number(Summa);
                if (Summa_num >= list[5]) {
                    mathSubComis(Summa, list[3], list[4]);
                } else {
                    mathComis(Summa, list[1], list[2]);
                }
            } else {
                var SummaNum = sum.value;
                let Summa_num = Number(SummaNum);
                if (Summa_num >= list[5]) {
                    mathSubComis(SummaNum, list[3], list[4]);
                } else {
                    mathComis(SummaNum, list[1], list[2]);
                }
            }
        } else {
            console.log('Is NULL');
            document.getElementById("error").innerHTML = 'Выберите назначение платежа';
        }
    }
    function math() {
        if (appointmentSelect.value != "null") {
            var list = appoints.get(appointmentSelect.value);
            var Summa = sum.value;
            let Summa_num = Number(Summa);

            if (Summa_num >= list[5]) {
                mathSubComis(Summa, list[3], list[4]);
            } else {
                mathComis(Summa, list[1], list[2]);
            }
        } else {
            console.log('Is NULL');
            document.getElementById("error").innerHTML = 'Выберите назначение платежа';
        }


    }

    function mathComis(sum, percent, comis) {

        let Summa_num = Number(sum);

        var persent_jami = Summa_num * percent / 100;
        var comissiya = comis + persent_jami;
        var jami = comissiya + Summa_num;

        let summa_math = Math.ceil(jami);
        let summa_math2 = summa_math / 1000;
        let summa_total = Math.ceil(summa_math2);
        let summa_str = String(summa_total);
        let summa_total_str = summa_str + "000.00";
        let summa_tot = Number(summa_total_str);
        let comissiya_total = summa_tot - Summa_num;
        document.getElementById("comission_num").value = (comissiya_total);
        document.getElementById("total_num").value = (summa_tot);
        document.getElementById("sum_num").value = (Summa_num);

        var summa_dec = Summa_num.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var comissiya_dec = comissiya_total.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var jami_dec = summa_tot.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });

        document.getElementById("comission").value = (comissiya_dec);
        document.getElementById("total").value = (jami_dec);
        document.getElementById("sum").value = (summa_dec);
    }
    function mathSubComis(sum, percent, comis) {

        let Summa_num = Number(sum);

        var persent_jami = Summa_num * percent / 100;
        var comissiya = comis + persent_jami;
        var jami = comissiya + Summa_num;

        let summa_math = Math.ceil(jami);
        let summa_math2 = summa_math / 1000;
        let summa_total = Math.ceil(summa_math2);
        let summa_str = String(summa_total);
        let summa_total_str = summa_str + "000.00";
        let summa_tot = Number(summa_total_str);
        let comissiya_total = summa_tot - Summa_num;
        document.getElementById("comission_num").value = (comissiya_total);
        document.getElementById("total_num").value = (summa_tot);
        document.getElementById("sum_num").value = (Summa_num);

        var summa_dec = Summa_num.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var comissiya_dec = comissiya_total.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var jami_dec = summa_tot.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });

        document.getElementById("comission").value = (comissiya_dec);
        document.getElementById("total").value = (jami_dec);
        document.getElementById("sum").value = (summa_dec);
    }
}


function budgetMath() {
    document.getElementById("sum").addEventListener("change", displayDate);
    function displayDate() {
        var Summa = sum.value;
        let Summa_num = Number(Summa);
        if (Summa_num >= 2000000) {
            mathSubComis();
        } else {
            mathComis();
        }
    }
    function mathComis() {
        var Summa = sum.value;
        let Summa_num = Number(Summa);
        var Persent = percent.value;
        let Persent_num = Number(Persent);
        var Comis = comis.value;
        let Comis_num = Number(Comis);
        var persent_jami = Summa_num * Persent_num / 100;
        var comissiya = Comis_num + persent_jami;
        var jami = comissiya + Summa_num;

        let summa_math = Math.ceil(jami);
        let summa_math2 = summa_math / 1000;
        let summa_total = Math.ceil(summa_math2);
        let summa_str = String(summa_total);
        let summa_total_str = summa_str + "000.00";
        let summa_tot = Number(summa_total_str);
        let comissiya_total = summa_tot - Summa_num;
        document.getElementById("comission_num").value = (comissiya_total);
        document.getElementById("total_num").value = (summa_tot);
        document.getElementById("sum_num").value = (Summa_num);

        var summa_dec = Summa_num.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var comissiya_dec = comissiya_total.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var jami_dec = summa_tot.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });

        document.getElementById("comission").value = (comissiya_dec);
        document.getElementById("total").value = (jami_dec);
        document.getElementById("sum").value = (summa_dec);
    }
    function mathSubComis() {
        var Summa = sum.value;
        let Summa_num = Number(Summa);
        var Persent = subPercent.value;
        let Persent_num = Number(Persent);
        var Comis = subComis.value;
        let Comis_num = Number(Comis);
        var persent_jami = Summa_num * Persent_num / 100;
        var comissiya = Comis_num + persent_jami;
        var jami = comissiya + Summa_num;

        let summa_math = Math.ceil(jami);
        let summa_math2 = summa_math / 1000;
        let summa_total = Math.ceil(summa_math2);
        let summa_str = String(summa_total);
        let summa_total_str = summa_str + "000.00";
        let summa_tot = Number(summa_total_str);
        let comissiya_total = summa_tot - Summa_num;
        document.getElementById("comission_num").value = (comissiya_total);
        document.getElementById("total_num").value = (summa_tot);
        document.getElementById("sum_num").value = (Summa_num);

        var summa_dec = Summa_num.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var comissiya_dec = comissiya_total.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });
        var jami_dec = summa_tot.toLocaleString('ru-RU', {
            style: "decimal",
            minimumFractionDigits: 2
        });

        document.getElementById("comission").value = (comissiya_dec);
        document.getElementById("total").value = (jami_dec);
        document.getElementById("sum").value = (summa_dec);
    }
}
