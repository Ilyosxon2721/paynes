const sum = document.getElementById('sum'),
    persent = document.getElementById('pays_persent'),
    comis = document.getElementById('pays_comis'),
    appointment = document.getElementById('pays_type');
    // if (appointment == 'МИБ(Алимент)') {
    //     document.getElementById("sum").addEventListener("change", displayDateAliment);
    // }else{
    //     document.getElementById("sum").addEventListener("change", displayDate);
    // }
    document.getElementById("sum").addEventListener("change", displayDate);


function displayDate() {
    var appoint_pay = appointment.value;

        var Summa = sum.value;
        let Summa_num = Number(Summa);

            
            if (Summa_num < 100000) {
                let Comis_num = 1000;
                var persent_jami = 0;
                var comissiya = Comis_num + persent_jami;

            }else{
                var Comis = comis.value;
                let Comis_num = Number(Comis);
                var Persent = persent.value;
                let Persent_num = Number(Persent);
                var persent_jami = Summa_num * Persent_num / 100;
                var comissiya = Comis_num + persent_jami;
            }

            
            var jami = comissiya + Summa_num;

            let summa_math = Math.round(jami);
            let summa_math2 = summa_math / 1000;
            let summa_total = Math.round(summa_math2);
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