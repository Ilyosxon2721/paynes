$('#pay-svg').click(function() {
    $('#welcome').hide();
    $('#payments').show();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').hide();
    $('#monitoring-window').hide();
    $('#report-window').hide();
    $('#collection-window').hide();
    $('#byudjet-window').hide();
    $('#comunal-window').hide();
});
// $('#exchange-svg').click(function() {
//     $('#welcome').hide();
//     $('#payments').hide();
//     $('#exchange').show();
//     $('#credits').hide();
//     $('#inquiry-window').hide();
//     $('#monitoring-window').hide();
//     $('#report-window').hide();
//     $('#collection-window').hide();
//     $('#byudjet-window').hide();
//     $('#comunal-window').hide();
// });
$('#credit-svg').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').show();
    $('#inquiry-window').hide();
    $('#monitoring-window').hide();
    $('#report-window').hide();
    $('#collection-window').hide();
    $('#byudjet-window').hide();
    $('#comunal-window').hide();
});
$('#inquiry-credit-svg').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').show();
    $('#monitoring-window').hide();
    $('#report-window').hide();
    $('#collection-window').hide();
    $('#byudjet-window').hide();
    $('#comunal-window').hide();
});
$('#monitoring-svg').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').hide();
    $('#monitoring-window').show();
    $('#report-window').hide();
    $('#collection-window').hide();
    $('#byudjet-window').hide();
    $('#comunal-window').hide();
});
$('#report-svg').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').hide();
    $('#monitoring-window').hide();
    $('#report-window').show();
    $('#collection-window').hide();
    $('#byudjet-window').hide();
    $('#comunal-window').hide();
});
$('#collection-money-svg').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').hide();
    $('#monitoring-window').hide();
    $('#report-window').hide();
    $('#collection-window').show();
    $('#byudjet-window').hide();
    $('#comunal-window').hide();
});
$('#mib').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').hide();
    $('#monitoring-window').hide();
    $('#report-window').hide();
    $('#collection-window').hide();
    $('#byudjet-window').show();
    $('#comunal-window').hide();
});
$('#elektr').click(function() {
    $('#welcome').hide();
    $('#payments').hide();
    $('#exchange').hide();
    $('#credits').hide();
    $('#inquiry-window').hide();
    $('#monitoring-window').hide();
    $('#report-window').hide();
    $('#collection-window').hide();
    $('#byudjet-window').hide();
    $('#comunal-window').show();
});
$('#btn-pay-back').click(function() {
    window.location.href = '\\cabinet\\payment.php';
});
$('#btn-pay-comunal-back').click(function() {
    window.location.href = '\\cabinet\\payment.php';
});