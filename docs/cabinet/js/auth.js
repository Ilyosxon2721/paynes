function funcBefore() {
    $("#information").text("Ожидание данных...");
  }
  $(document).ready(function() {
    $("input#submit-voyti").on('click', function() {
      var login = $('input#input-login').val();
      var password = $('input#input-password').val();
      /* Act on the event */
      $.ajax({
        url: 'php/auth.php',
        type: 'POST',
        data: {
          login: login,
          password: password
        },
        dataType: "html",
        beforeSend: funcBefore,
        success: function(data) {
          console.log(data);
          if (data == true) {
            window.location.href = '/cabinet/home.php';
            $("#information").text(data);
          }
          if (data != true) {
            $('#error').show();
            $('#error').text(data);
          }
        }
      });
    });
  });