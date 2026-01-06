const sum_comunal = document.getElementById('sum_comunal'),
         persent_comunal = document.getElementById('pays_persent_comunal'),
         comis_comunal = document.getElementById('pays_comis_comunal'),  
         tarif_comunal = document.getElementById('tarif_comunal');  
         document.getElementById("sum_comunal").addEventListener("change", displayDate);

function displayDate() {
//  document.getElementById("demo").innerHTML = Date();
    var Summa = sum_comunal.value;
    let Summa_num = Number(Summa);
    var Persent = persent_comunal.value;
    let Persent_num = Number(Persent);
    var Comis = comis_comunal.value;
    let Comis_num = Number(Comis);
    var tarif = tarif_comunal.value;
    let tarif_num = Number(tarif);
    var kvt = Summa_num / tarif_num;
    var persent_jami = Summa_num * Persent_num / 100;
    var comissiya = Comis_num + persent_jami;

    if (comissiya < 1000) {
        var comissiya = 1000;
    }
    var jami = comissiya + Summa_num;
    document.getElementById("comission_comunal_num").value = (comissiya);
    document.getElementById("sum_comunal_num").value = (Summa_num);
    document.getElementById("total_comunal_num").value = (jami);
    var Summa_format = Summa_num.toLocaleString('ru-RU', {style: "decimal", minimumFractionDigits: 2 });
    var comissiya_format = comissiya.toLocaleString('ru-RU', {style: "decimal", minimumFractionDigits: 2 });
    var jami_format = jami.toLocaleString('ru-RU', {style: "decimal", minimumFractionDigits: 2 });
    var Summa_format = Summa_num.toLocaleString('ru-RU', {style: "decimal", minimumFractionDigits: 2 });
    console.log(Summa_format);
    // var Summa_format = new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'rub', minimumFractionDigits:2}).format(Summa_num);
    // console.log(new Intl.NumberFormat('ru-RU', { style: 'currency', currency: 'rub' }).format(Summa_num));
    // console.log(comissiya_fix);
document.getElementById("comission_comunal").value = (comissiya_format);
document.getElementById("total_comunal").value = (jami_format);
document.getElementById("kvt").value = (Math.floor(kvt * 100) / 100);
document.getElementById("sum_comunal").value = (Summa_format);
}
