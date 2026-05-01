$(document).ready(function () {
    //loadReporteControlBovedas();
    //loadResumenIngresos();
    //loadVisanet();
    //loadPremiosPromos();
});
//
function loadPartidaContable() {
    $.post('views/flujosBovedas/partidaContable.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Partida Contable');
        $('#fechaCierre').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaCierre .form-control").val(today);
    });
}
//
function loadReporteControlBovedas() {
    $.post('views/flujosBovedas/reporteControlBovedas.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Reporte Control de Bovedas');
    });
}
//
function generarReporteControlBovedas() {
    params = {
        idTipoReporte: $("#idTipoReporte").val(),
        tipoReporte: $("#idTipoReporte option:selected").text(),
        idSalas: $("#idSalas").val(),
        sala: $("#idSalas option:selected").text(),
        year: $("#year option:selected").text(),
        month: $("#month").val(),
        monthName: $("#month option:selected").text()
    };
    $.post('views/flujosBovedas/reporteControlBovedas.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Reporte Control de Bovedas');
    });
}
//
function loadResumenIngresos() {
    $.post('views/flujosBovedas/resumenIngresos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Resumen de Ingresos');
    });
}
//
function generarResumenIngresos() {
    params = {
        idTipoReporte: $("#idTipoReporte").val(),
        tipoReporte: $("#idTipoReporte option:selected").text(),
        idSalas: $("#idSalas").val(),
        sala: $("#idSalas option:selected").text(),
        year: $("#year option:selected").text(),
        month: $("#month").val(),
        monthName: $("#month option:selected").text()
    };
    $.post('views/flujosBovedas/resumenIngresos.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Resumen de Ingresos');
    });
}
//
function loadPremiosPromos() {
    $.post('views/flujosBovedas/premiosPromos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Premios y Promociones');
    });
}
//
function generarReportePremiosPromos() {
    params = {
        idTipoReporte: $("#idTipoReporte").val(),
        tipoReporte: $("#idTipoReporte option:selected").text(),
        idSalas: $("#idSalas").val(),
        sala: $("#idSalas option:selected").text(),
        year: $("#year option:selected").text(),
        month: $("#month").val(),
        monthName: $("#month option:selected").text()
    };
    $.post('views/flujosBovedas/premiosPromos.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Premios y Promociones');
    });
}
//
function loadDepositos() {
    $.post('views/flujosBovedas/depositos.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Control de Depositos');
    });
}
//
function loadVisanet() {
    $.post('views/flujosBovedas/visanet.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Control de Tarjetas de Credito Y/O Debito');
    });
}
//
function generarReporteVisanet() {
    params = {
        idTipoReporte: $("#idTipoReporte").val(),
        tipoReporte: $("#idTipoReporte option:selected").text(),
        idSalas: $("#idSalas").val(),
        sala: $("#idSalas option:selected").text(),
        year: $("#year option:selected").text(),
        month: $("#month").val(),
        monthName: $("#month option:selected").text()
    };
    $.post('views/flujosBovedas/visanet.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Control de Tarjetas de Credito Y/O Debito');
    });
}
//
function loadFaltantes() {
    $.post('views/flujosBovedas/faltantes.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Cuadro de Faltantes');
    });
}
//
function consultarPartidasBovedas() {
    params = {
        idTipoReporte: $("#idTipoReporte").val(),
        tipoReporte: $("#idTipoReporte option:selected").text(),
        idSalas: $("#idSalas").val(),
        sala: $("#idSalas option:selected").text(),
        fechaCierre: $("#fechaCierre .form-control").val()
    };
    $.post('views/flujosBovedas/partidaContable.php', params, function (respuesta) {
        $('#page-container').html(respuesta);
        $('#modulo').html('Contabilidad');
        $('#opcion').html('Partida Contable');
        $('#fechaCierre').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
    });
}