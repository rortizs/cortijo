/**
 * @version 1.0 20180322
 */
$(document).ready(function () {
    //loadPlanillasGSP();
});
//
function loadPlanillasGSP() {
    $.post('views/planilla/planillasGSP.php', function (respuesta) {
        $('#page-container').html(respuesta);
        $("#modulo").html('Planilla');
        $("#opcion").html('Nominas');
        $('#fechaInicio,#fechaFin,#fechaInicioHE,#fechaFinHE').datetimepicker({
            pickTime: false,
            format: 'DD-MM-YYYY'
        });
        $("#fechaInicio .form-control").val(today);
        $("#fechaFin .form-control").val(today);
        $("#fechaInicioHE .form-control").val(today);
        $("#fechaFinHE .form-control").val(today);
    });
}
//
function generarPlanillaGSP() {
    $("#loader").show();
    var fechaInicio = $("#fechaInicio .form-control").val();
    var fechaFin = $("#fechaFin .form-control").val();
    var fechaInicioHE = $("#fechaInicioHE .form-control").val();
    var fechaFinHE = $("#fechaFinHE .form-control").val();
    var fuenteHorasExtras = $("#fuenteHorasExtras").val();
    params = {
        service: 'consultaCierre',
        fechaInicio: fechaInicio,
        fechaFin: fechaFin,
        fuenteHorasExtras: fuenteHorasExtras,
        fechaInicioHE: fechaInicioHE,
        fechaFinHE: fechaFinHE,
        //dias: restaFechas(fechaInicio, fechaFin)
        dias: 15
    };
    //Consultar si planilla ya fue cerrada
    $.post('controllers/planillaController.php', params, function (data) {
        if (data === null) {
            $.post('views/planilla/planillasGSP.php', params, function (respuesta) {
                $('#page-container').html(respuesta);
                $("#modulo").html('Planilla');
                $("#opcion").html('Generación de Planilla');
                $("#fechaInicio,#fechaFin,#fechaInicioHE,#fechaFinHE").datetimepicker({
                    format: 'DD-MM-YYYY',
                    pickTime: false
                });
                $("#fechaInicio .form-control").val(fechaInicio);
                $("#fechaFin .form-control").val(fechaFin);
                $("#fechaInicioHE .form-control").val(fechaInicioHE);
                $("#fechaFinHE .form-control").val(fechaFinHE);
                $("#btnImprimir1,#btnImprimir2,#btnBoletaPago,#btnCerrarPlanilla").removeClass('hidden');
                $("#fuenteHorasExtras option").each(function () {
                    if ($(this).val() === params.fuenteHorasExtras) {
                        $(this).prop('selected', true);
                    }
                });
            });
        } else {
            alert('Planilla ya fue cerrada');
            $("#btnImprimir1,#btnImprimir2,#btnBoletaPago,#btnCerrarPlanilla").removeClass('hidden').addClass('hidden');
            $("#detalle").html('');
        }
    }, 'json').done(function () {
        $("#loader").hide();
    });
}