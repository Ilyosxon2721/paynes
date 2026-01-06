
function funcBefore() {
  $("#information").text("Ожидание данных...");
}

$(document).ready(function () {

  $(".byudjet").on("click", function () {
    var id = $(this).attr("id");
    /* Act on the event */
    $.ajax({
      url: "../php/select_type_pays_byudjet.php",
      type: "POST",
      data: {
        id: id,
      },
      dataType: "html",
      beforeSend: funcBefore,
      success: function (data) {
        var result = $.parseJSON(data);

        console.log(result);
        if (data != "") {
          window.Storage = {}
          window.Storage.globalVar = result[0][1];
          if (result[0][1] == "МИБ") {
            document.getElementsByName("perpose-of-payment")[0].type = "hidden";
            document.getElementsByName("perpose-of-payment-select")[0].style.display = "inline";
            // mibMath();
          } else {
            // budgetMath();
            document.getElementsByName("perpose-of-payment")[0].type = "text";
            document.getElementsByName("perpose-of-payment-select")[0].style.display = "none";
          }
          document.getElementById("byudjet-pay-header-title").innerHTML =
            result[0][1];
          document.getElementById("img-label").innerHTML =
            '<img src="\\images\\' +
            result[0][9] +
            '" alt="mib" id="byudjet-pay-header-img" width="46" height="46">';
          document.getElementById("list").placeholder = result[0][8];
          document.getElementById("pays_id").value = result[0][0];
          document.getElementById("pays_moliya").value = result[0][2];
          document.getElementById("pays_bank").value = result[0][3];
          document.getElementById("pays_xr").value = result[0][4];
          document.getElementById("pays_mfo").value = result[0][5];
          document.getElementById("pays_inn").value = result[0][6];
          document.getElementById("pays_shxr").value = result[0][7];
          document.getElementById("pays_persent").value = result[0][10];
          document.getElementById("pays_sub_persent").value = result[0][11];
          document.getElementById("pays_comis").value = result[0][12];

          document.getElementById("pays_sub_comis").value = result[0][13];
          document.getElementById("pays_type").value = result[0][1];
          // document.getElementById("type").innerHTML = (result[0][1]);

          $("#welcome").hide();
          $("#payments").hide();
          $("#exchange").hide();
          $("#credits").hide();
          $("#inquiry-window").hide();
          $("#monitoring-window").hide();
          $("#report-window").hide();
          $("#collection-window").hide();
          $("#byudjet-window").show();
          $("#comunal-window").hide();
          $("#chek").hide();
        }
        if (data == "") {
          console.log(data);
        }
      },
    });
  });
});
$(document).ready(function () {
  $(".btn-print-chek").on("click", function () {
    var unique_number = $("#unique_number_chek").val();
    var type_pay = $("#pays_type").val();

    $.ajax({
      url: "\\php\\select_chek_pays.php",
      type: "POST",
      data: {
        unique_number: unique_number,
        type_pay: type_pay,
      },
      dataType: "html",
      beforeSend: funcBefore,
      success: function (data) {
        var result = $.parseJSON(data);
        console.log(result);
        console.log(result[0][0][0]);
        console.log(result[1][0][0]);
        // console.log(result[0][0][0][8]);z`
        // console.log(result[0][0][0][9]);
        // console.log(result[0][0][0][10]);
        // console.log(result[0][0][0][11]);
        // console.log(result[0][0][0][2]);
        // console.log(result[0][0][0][3]);

        if (data) {
          $(".window-chek").show();
          var sum = result[0][0][0][8];
          var comis = result[0][0][0][9];
          var total = result[0][0][0][10];
          let sum_num = Number(sum);
          let comis_num = Number(comis);
          let total_num = Number(total);
          var sum_dec = sum_num.toLocaleString("ru-RU", {
            style: "decimal",
            minimumFractionDigits: 2,
          });
          var comis_dec = comis_num.toLocaleString("ru-RU", {
            style: "decimal",
            minimumFractionDigits: 2,
          });
          var total_sum_dec = total_num.toLocaleString("ru-RU", {
            style: "decimal",
            minimumFractionDigits: 2,
          });
          console.log(sum_dec);

          document.getElementById("type_cash_pay").innerHTML =
            `Тулов тури: <span style="font-family: 'VerdanaBold';">` +
            result[0][0][0][11] +
            `</span>`;
          document.getElementById("date_pay").innerHTML = result[0][0][0][2];
          document.getElementById("time_pay").innerHTML = result[0][0][0][3];
          document.getElementById("unique_number_pay").innerHTML =
            `№` + result[0][0][0][1];
          // document.getElementById("sum_pay").innerHTML =
          //   total_sum_dec.replace(/,/, ".") + ` сум`;
          document.getElementById("sum_two_pay").innerHTML =
            `Сумма платежа <span class="chek-small-text-four">` +
            sum_dec.replace(/,/, ".") +
            `</span>`;
          document.getElementById("comis_pay").innerHTML =
            `Комиссия <span class="chek-small-text-four">` +
            comis_dec.replace(/,/, ".") +
            `</span>`;
          document.getElementById("total_pay").innerHTML =
            `Итого <span class="chek-small-text-four">` +
            total_sum_dec.replace(/,/, ".") +
            `</span>`;
          document.getElementById("cashier_pay").innerHTML =
            result[0][0][0][18];
          document.getElementById("type_pays_pay").innerHTML =
            result[1][0][0][2];
          document.getElementById("bank_pay").innerHTML =
            `Банк муассаси <span class="chek-small-text">` +
            result[1][0][0][3] +
            `</span>`;
          document.getElementById("xr_pay").innerHTML =
            `Хисоб ракам  <span class="chek-small-text">` +
            result[1][0][0][4] +
            `</span>`;
          document.getElementById("mfo_pay").innerHTML =
            `МФО  <span class="chek-small-text">` +
            result[1][0][0][5] +
            `</span>`;
          document.getElementById("inn_pay").innerHTML =
            `ИНН  <span class="chek-small-text">` +
            result[1][0][0][6] +
            `</span>`;
          document.getElementById("list_pay").innerHTML =
            result[1][0][0][8] +
            ` <span class="chek-small-text">` +
            result[0][0][0][5] +
            `</span>`;
          document.getElementById("fio_pay").innerHTML =
            result[0][0][0][6] +
            ` <p class="chek-small-text-two">(фамилияси, исми ва отасининг исми / ФИО)</p>`;
          document.getElementById("adres_pay").innerHTML =
            result[0][0][0][7] +
            ` <p class="chek-small-text-two">(Назначение платежа / Адрес)</p>`;
          document.getElementById("appoint_pay").innerHTML =
            `Тулов максади <span class="chek-small-text-three">` +
            result[0][0][0][4] +
            `</span>`;
          var img = "<img src='https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=http://chek.xn--80aumk.uz/?id=" + result[0][0][0][1] + "' alt='qr_code'>";
          document.getElementById("qr").innerHTML = img;
        }
        if (data == "") {
          console.log(data);
        }
      },
    });
  });
});

function printEl(print_byudjet) {
  var printContents = document.getElementById(print_byudjet).innerHTML;
  var originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  window.location.href = "\\cabinet\\payment.php";
  // $('#exampleModal').show();
}

function closeAll() {
  window.location.href = "\\cabinet\\payment.php";
}

$(document).ready(function () {
  $(".comunal").on("click", function () {
    var id = $(this).attr("id");
    /* Act on the event */
    $.ajax({
      url: "\\php\\select_type_pays_comunal.php",
      type: "POST",
      data: {
        id: id,
      },
      dataType: "html",
      beforeSend: funcBefore,
      success: function (data) {
        var result = $.parseJSON(data);
        console.log(result);
        if (data != "") {
          document.getElementById("comunal-pay-header-title").innerHTML =
            result[0][1];
          document.getElementById("img-label-comunal").innerHTML =
            '<img src="\\images\\' +
            result[0][9] +
            '" alt="mib" id="comunal-pay-header-img" width="46" height="46">';
          document.getElementById("list").placeholder = result[0][8];
          document.getElementById("pays_id_comunal").value = result[0][0];
          document.getElementById("pays_moliya_comunal").value = result[0][2];
          document.getElementById("pays_bank_comunal").value = result[0][3];
          document.getElementById("pays_xr_comunal").value = result[0][4];
          document.getElementById("pays_mfo_comunal").value = result[0][5];
          document.getElementById("pays_inn_comunal").value = result[0][6];
          document.getElementById("pays_shxr_comunal").value = result[0][7];
          document.getElementById("pays_persent_comunal").value = result[0][10];
          document.getElementById("pays_comis_comunal").value = result[0][11];
          document.getElementById("pays_type_comunal").value = result[0][1];
          document.getElementById("tarif_comunal").value = result[0][12];
          $("#welcome").hide();
          $("#payments").hide();
          $("#exchange").hide();
          $("#credits").hide();
          $("#inquiry-window").hide();
          $("#monitoring-window").hide();
          $("#report-window").hide();
          $("#collection-window").hide();
          $("#byudjet-window").hide();
          $("#comunal-window").show();
          $("#chek_comunal").hide();
        }
        if (data == "") {
          console.log(data);
        }
      },
    });
  });
});
